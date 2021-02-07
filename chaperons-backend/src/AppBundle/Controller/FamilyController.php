<?php

namespace AppBundle\Controller;

use App\FamilyDispatcher;
use App\FamilyParser;
use AppBundle\Entity\Family;
use AppBundle\Entity\Map;
use AppBundle\Entity\NurserySelection;
use AppBundle\Form\AddressType;
use Doctrine\ORM\EntityManager;
use Nelmio\ApiDocBundle\Annotation\ApiDoc;
use Symfony\Component\HttpFoundation\File\UploadedFile;
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;

class FamilyController extends BaseController
{

    /**
     * @ApiDoc(
     *   section="Family",
     *   description="upload the families"
     * )
     */
    public function postMapFamiliesAction(Request $request, $id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($id);

        if( !$map ) {
            throw new NotFoundHttpException();
        }

        $this->getAuthorizedUser($map->getUser()->getId());

        if( $request->files->has('file') ) {
            // remove existing families
            foreach($map->getFamilies() as $f) {
                $map->removeFamily($f);
            }

            /** @var UploadedFile $file */
            $file = $request->files->get('file');

            // geocode the file
            $this->get('app.geocoder')->geocodeFile($file->getPathname());

            $parser = new FamilyParser();
            $families = $parser->parseCsv($file->getPathname());

            foreach($families as $f) {
                $f->setMap($map);
                $map->addFamily($f);
            }

            $em->flush();

            $view = $this->view($map, 200);
            return $this->handleView($view);
        }

        $view = $this->view(null, 400);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Family",
     *   description="update the family address",
     *   input="\AppBundle\Form\AddressType"
     * )
     */
    public function putFamilyAddressAction(Request $request, $id)
    {
        $em = $this->getEm();
        /** @var Family $family */
        $family = $em->getRepository('AppBundle:Family')->find($id);

        $this->getAuthorizedUser($family->getMap()->getUser()->getId());

        $address = $family->getAddress();

        $form = $this->createForm(AddressType::class, $address, ['method' => 'PUT']);

        $form->handleRequest($request);

        if( $form->isValid() ) {
            $em->persist($address);
            $em->flush();

            $dispatcher = new FamilyDispatcher($em);
            $dispatcher->updateFamilyNurseries($family);

            $view = $this->view($address);
            return $this->handleView($view);
        }

        $view = $this->view($form, 400);
        return $this->handleView($view);
    }

    /**
     * @ApiDoc(
     *   section="Family"
     * )
     */
    public function deleteFamilyAction($id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Family $family */
        $family = $em->getRepository('AppBundle:Family')->find($id);

        $this->getAuthorizedUser($family->getMap()->getUser()->getId());

        $em->remove($family);
        $em->flush();

        return new JsonResponse();
    }

    /**
     * @ApiDoc(
     *   section="Family",
     *   description="return the csv export with nursery distance histogram"
     * )
     */
    public function getFamilyHistogramAction(Request $request, $map_id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($map_id);

        $this->getAuthorizedUser($map->getUser()->getId());

        $filename = tempnam('/tmp', 'ch_export');
        $f = fopen($filename, "w");

        $distances = [0.5, 1, 3, 5, 10, 20, 30];

        $header = ['Addresses'];
        foreach ($distances as $dist) {
            $label = 'Nombre total de crèches dans un rayon < ';
            if ($dist < 1.) {
                $label .= ($dist * 1000) . 'm';
            } else {
                $label .= $dist . 'km';
            }
            $header[] = $label;
        }

        fputcsv($f, $header);

        foreach($map->getFamilies() as $family) {
            /** @var Family $family $row */
            $row = [];
            $row[] = implode(' ', [$family->getAddress()->getStreet(),
                $family->getAddress()->getCity(),
                $family->getAddress()->getZip(), '(' . $family->getId() . ')']);

            foreach ($distances as $dist) {
                $sel = $family->getClosestNurserySelection(30);
                $nurseries = [];
                foreach($sel as $s) {
                    /** @var NurserySelection $s */
                    if ($s->getDistance() <= $dist*1000) {
                        $nurseries[] = $s->getFormattedDistance() . ': ' . $s->getNursery()->getName() . ' (' . $s->getNursery()->getId() . ')';
                    }
                }

                $total = $family->getClosestTotal($dist);

                $row[] = $total .  ' crèche' . ($total ? 's' : '') . "\n" . implode("\n", $nurseries);
            }

            fputcsv($f, $row);
        }

        fclose($f);

        $response = new Response(file_get_contents($filename), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export-familles-' . $map->getId() . '.csv"'
        ]);

        return $response;
    }


    /**
     * @ApiDoc(
     *   section="Family",
     *   description="return the csv export with family/nursery associations"
     * )
     */
    public function getFamilyExportAction(Request $request, $map_id)
    {
        $em = $this->getDoctrine()->getManager();
        /** @var Map $map */
        $map = $em->getRepository('AppBundle:Map')->find($map_id);

        $this->getAuthorizedUser($map->getUser()->getId());

        $filename = tempnam('/tmp', 'ch_export');
        $f = fopen($filename, "w");

        $n_sel = 3;

        fputcsv($f, ['id', 'adresse', 'ville', 'code postal', 'creche_1', 'creche_1_id', 'distance_1', 'creche_2', 'creche_2_id', 'distance_2', 'creche_3', 'creche_3_id', 'distance_3']);

        /** @var Family $family */
        foreach($map->getFamilies() as $family) {
            $distance = [];
            $creche = [];
            $creche_id = [];

            $sel = $family->getClosestNurserySelection($n_sel);
            if( count($sel) ) {

                foreach($sel as $s) {
                    $distance[] = $s->getFormattedDistance();
                    $creche[] = $s->getNursery()->getName();
                    $creche_id[] = $s->getNursery()->getSourceId();
                }

            }

            fputcsv($f, [
                $family->getId(),
                $family->getAddress()->getStreet(),
                $family->getAddress()->getCity(),
                $family->getAddress()->getZip(),
                isset($creche[0]) ? $creche[0] : '-',
                isset($creche_id[0]) ? $creche_id[0] : '-',
                isset($distance[0]) ? $distance[0] : '-',
                isset($creche[1]) ? $creche[1] : '-',
                isset($creche_id[1]) ? $creche_id[1] : '-',
                isset($distance[1]) ? $distance[1] : '-',
                isset($creche[2]) ? $creche[2] : '-',
                isset($creche_id[2]) ? $creche_id[2] : '-',
                isset($distance[2]) ? $distance[2] : '-',
            ]);
        }

        fclose($f);

        $response = new Response(file_get_contents($filename), 200, [
            'Content-Type' => 'text/csv',
            'Content-Disposition' => 'attachment; filename="export-familles-' . $map->getId() . '.csv"'
        ]);

        return $response;
    }


    /**
     * @ApiDoc(
     *   section="Family",
     *   description="update the families closest nurseries"
     * )
     */
    public function dispatch_familiesMapAction($id)
    {
        /** @var EntityManager $em */
        $em = $this->getDoctrine()->getManager();

        $map = $em->getRepository('AppBundle:Map')->find($id);

        if( !$map ) throw new NotFoundHttpException();

        $this->getAuthorizedUser($map->getUser()->getId());

        $familyDispatcher = new FamilyDispatcher($em);
        $familyDispatcher->updateMapNurseries($map, true);

        $view = $this->view($map);

        return $this->handleView($view);
    }
}