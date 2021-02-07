<?php

namespace AppBundle\Controller;

use AppBundle\Entity\Map;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class NurseryController extends BaseController
{
    /**
     * @ApiDoc(
     *   section="Nursery",
     *   description="get some global stats on each type of nursery"
     * )
     */
    public function getNurseriesStatsAction()
    {
        $em = $this->getEm();

        $stats = $em->createQuery('SELECT count(n) as c, n.nature, n.type FROM AppBundle:Nursery n GROUP BY n.nature, n.type')->getScalarResult();

        return $this->handleView($this->view($stats));
    }

    /**
     * @ApiDoc(
     *   section="Nursery"
     * )
     */
    public function getNurseriesAction(Request $request)
    {
        $em = $this->getEm();

        $qb = $em->createQueryBuilder()
            ->select('n')
            ->from('AppBundle:Nursery', 'n');


        if($map_id = $request->query->get('map_id')) {
            /** @var Map $map */
            $map = $em->getRepository('AppBundle:Map')->find($map_id);
            if(!$map) throw new NotFoundHttpException();

            if(!$map->getShowDSP()) $qb->andWhere("n.nature != 'DSP'");
            if(!$map->getShowPartners()) $qb->andWhere("n.nature != 'PARTNER'");
            if(!$map->getShowMac()) $qb->andWhere("n.nature != 'CEP' or n.type != 'MAC'");
            if(!$map->getShowMicro()) $qb->andWhere("n.nature != 'CEP' or n.type != 'MICRO'");
        } else {

        }

        // @todo cache
        $nurseries = $qb->getQuery()->getResult();

        $view = $this->view($nurseries);
        return $this->handleView($view);
    }
}