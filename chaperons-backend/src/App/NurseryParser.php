<?php

namespace App;

use AppBundle\Entity\Address;
use AppBundle\Entity\Nursery;
use Doctrine\ORM\EntityManager;

class NurseryParser
{
    use CsvParser;

    /** @var EntityManager */
    private $em;

    public function __construct(EntityManager $em) {
        $this->em = $em;
    }

    /**
     * Parse and save the nurseries.
     *
     * @param $path
     */
    public function importCsv($path)
    {
        $delimiter = $this->detectDelimiter($path, 10);
        $csv = new \SplFileObject($path);

        $header = $csv->fgetcsv($delimiter);

        /*
        creche_id_nature;
        nom;
        nature;
        agr;
        type;
        ag_cible;
        date_ouverture;
        nb_sections;
        horaire_ouverture;
        horaire_fermeture;
        adresse;
        code_postal;
        ville;
        telephone;
        email;
        societe;
        departement;
        region;
        siret;
        enseigne;
        enseigne_slug;
        geo_lat;
        geo_lng;
        espace_exterieur;
        repas;
        lait;
        couches;
        marque_blanche;
        affichage_logo_enseigne
        */

        $required_cols = ['creche_id_nature', 'nom', 'nature', 'type', 'adresse', 'code_postal', 'ville', 'geo_lat', 'geo_lng'];
        foreach($required_cols as $c) {
            if(!in_array($c, $header)) {
                throw new \Exception(sprintf('missing %s column',$c));
            }
        }

        $header = array_flip($header);

        $n = 0;

        while(!$csv->eof()) {
            $row = $csv->fgetcsv($delimiter);
            if(sizeof($row)<10) continue;

            $name = trim($row[$header['nom']]);
            $city = trim($row[$header['ville']]);
            $street = trim($row[$header['adresse']]);
            $zip = trim($row[$header['code_postal']]);
            $lat = (float)trim($row[$header['geo_lat']]);
            $lng = (float)trim($row[$header['geo_lng']]);

            $nature = strtoupper(trim($row[$header['nature']]));
            $type = strtoupper(trim($row[$header['type']]));

            $id = trim($row[$header['creche_id_nature']]);
            $nursery = $this->em->getRepository('AppBundle:Nursery')->findOneBy(['source_id'=>$id]);

            if(!$nursery) {
                $nursery = new Nursery();
                $nursery->setSourceId($id);
                $address = new Address();
                $nursery->setAddress($address);
            }

            $nursery->setName($name);
            $nursery->setNature($this->normalizeNature($nature));
            $nursery->setType($this->normalizeType($type));

            $address = $nursery->getAddress();

            $address->setCity($city);
            $address->setStreet($street);
            $address->setZip($zip);
            $address->setLatitude($lat);
            $address->setLongitude($lng);
            $address->setGeocodeStatus(1);

            $this->em->persist($nursery);
            $n++;
        }

        return $n;
    }


    private function normalizeType($type) {
        if($type == 'MIC') return 'MICRO';
        if(preg_match('/MICRO.*/i', $type)) return 'MICRO';
        return $type;
    }

    private function normalizeNature($nature) {
        if(preg_match('/DSP.*/i', $nature)) return 'DSP';
        if(preg_match('/CEP.*/i', $nature)) return 'CEP';
        if(preg_match('/PART.*/i', $nature)) return 'PARTNER';
        return $nature;
    }

}