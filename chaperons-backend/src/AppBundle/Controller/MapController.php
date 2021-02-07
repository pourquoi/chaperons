<?php

namespace AppBundle\Controller;

use App\FamilyDispatcher;
use App\FamilyParser;
use AppBundle\Entity\Map;
use AppBundle\Form\MapStyleType;
use AppBundle\Form\MapType;
use Doctrine\ORM\EntityManager;
use FOS\RestBundle\Context\Context;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class MapController extends BaseController
{
    /**
     * @ApiDoc(
     *   section="Map",
     *   description="get user maps"
     * )
     */
    public function getUserMapsAction(Request $request, $user_id)
    {
        $user = $this->getAuthorizedUser($user_id);

        /** @var EntityManager $em */
        $qb = $this->getEm()->createQueryBuilder()
            ->select('m, u')
            ->from('AppBundle:Map', 'm')
            ->innerJoin('m.user', 'u')
            ->where('u.id = :user_id')
                ->setParameter('user_id', $user_id);

        if($request->query->get('order') == 'old')
            $qb->orderBy('m.createdAt', 'ASC');
        else
            $qb->orderBy('m.createdAt', 'DESC');

        if( $start = $request->query->get('start') )
            $qb->setFirstResult($start);

        if( $limit = $request->query->get('limit') )
            $qb->setMaxResults($limit);

        $maps = $qb->getQuery()->getResult();

        $view = $this->view($maps);
        $context = new Context();
        $context->setGroups(['Default', 'list']);
        $view->setContext($context);

        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Map",
     *   description="create a map",
     *   input="\AppBundle\Form\MapType"
     * )
     */
    public function postUserMapAction(Request $request, $user_id)
    {
        $user = $this->getAuthorizedUser($user_id);

        $map = new Map();

        $form = $this->createForm(MapType::class, $map);

        $form->handleRequest($request);

        if( $form->isValid() ) {
            $map->setUser($user);
            $map->setCreatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->persist($map);
            $em->flush();

            $view = $this->view($map, 201);
            return $this->handleView($view);
        }

        $view = $this->view($form, 400);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Map",
     *   description="update map parameters",
     *   input="\AppBundle\Form\MapType"
     * )
     */
    public function putMapAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($id);

        if( !$map ) {
            throw new NotFoundHttpException();
        }

        $this->getAuthorizedUser($map->getUser()->getId());

        $form = $this->createForm(MapType::class, $map, [
            'method' => 'PUT'
        ]);

        $form->handleRequest($request);

        if( $form->isValid() ) {
            $map->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $dispatcher = new FamilyDispatcher($em);
            $dispatcher->updateMapNurseries($map, true);

            $view = $this->view($map, 200);
            return $this->handleView($view);
        }

        $view = $this->view($form, 400);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Map",
     *   description="update map styles",
     *   input="\AppBundle\Form\MapStyleType"
     * )
     */
    public function styleMapAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($id);

        if( !$map ) {
            throw new NotFoundHttpException();
        }

        $this->getAuthorizedUser($map->getUser()->getId());

        $form = $this->createForm(MapStyleType::class, $map, [
            'method' => 'PATCH'
        ]);

        $form->handleRequest($request);

        if( $form->isValid() ) {
            $map->setUpdatedAt(new \DateTime());

            $em = $this->getDoctrine()->getManager();
            $em->flush();

            $view = $this->view($map, 200);
            return $this->handleView($view);
        }

        $view = $this->view($form, 400);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Map",
     *   output="\AppBundle\Entity\Map"
     * )
     */
    public function getMapAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($id);

        if( !$map ) {
            throw new NotFoundHttpException();
        }

        $this->getAuthorizedUser($map->getUser()->getId());

        $view = $this->view($map);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Map"
     * )
     */
    public function deleteMapAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($id);

        if( !$map ) {
            throw new NotFoundHttpException();
        }

        $this->getAuthorizedUser($map->getUser()->getId());

        $em->remove($map);
        $em->flush();

        return $this->handleView($this->view());
    }

    /**
     * @ApiDoc(
     *   section="Map",
     *   description="render the map"
     * )
     */
    public function renderMapAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($id);

        if( !$map ) {
            throw new NotFoundHttpException();
        }

        $this->getAuthorizedUser($map->getUser()->getId());

        $this->get('app.renderer')->render($map);

        $em->flush();

        $view = $this->view($map);
        return $this->handleView($view);
    }
}