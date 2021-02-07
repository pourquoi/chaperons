<?php

namespace App;

use AppBundle\Entity\Address;
use AppBundle\Entity\Family;

class FamilyParser {

    use CsvParser;

    /**
     * Parse the csv into a list of families.
     *
     * @param string $path
     * @return Family[]
     */
    public function parseCsv($path) {
        $delimiter = $this->detectDelimiter($path, 3);
        $csv = new \SplFileObject($path);

        $header = $csv->fgetcsv($delimiter);

        $has_geocode = in_array('latitude', $header);

        $required_cols = ['city', 'street', 'zip'];

        if($has_geocode) $required_cols = array_merge($required_cols, ['latitude', 'longitude']);

        foreach($required_cols as $c) {
            if(!in_array($c, $header)) {
                throw new \Exception(sprintf('missing %s column',$c));
            }
        }

        $header = array_flip($header);

        $families = [];

        while(!$csv->eof()) {
            $row = $csv->fgetcsv($delimiter);
            if(sizeof($row)<3) continue;

            $city = trim($row[$header['city']]);
            $street = trim($row[$header['street']]);
            $zip = trim($row[$header['zip']]);

            $family = new Family();
            $address = new Address();
            $family->setAddress($address);

            $address->setCity($city);
            $address->setStreet($street);
            $address->setZip($zip);

            // csv geocoded
            // @see Geocoder
            if($has_geocode) {
                if( $row[$header['latitude']] !== '' ) {
                    $address->setLatitude($row[$header['latitude']]);
                    $address->setLongitude($row[$header['longitude']]);
                    $address->setGeocodeStatus(Address::GEOCODE_SUCCESS);

                    if (isset($header['result_city']) && $row[$header['result_city']])
                        $address->setCity($row[$header['result_city']]);
                    if (isset($header['result_postcode']) && $row[$header['result_postcode']])
                        $address->setZip($row[$header['result_postcode']]);
                    if (isset($header['result_street']) && $row[$header['result_street']])
                        $address->setStreet($row[$header['result_street']]);
                } else {
                    $address->setGeocodeStatus(Address::GEOCODE_ERROR);
                }
            }

            $families[] = $family;
        }

        return $families;
    }

}