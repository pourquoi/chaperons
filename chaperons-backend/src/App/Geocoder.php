<?php

namespace App;

use AppBundle\Entity\Address;

class Geocoder
{
    const endpoint = 'http://api-adresse.data.gouv.fr/search';

    /**
     * @deprecated
     * @param Address $address
     */
    public function geocodeAddress(Address $address) {
        $url = self::endpoint . '/?q=' .
            urlencode($address->getStreet() . ' ' . $address->getCity());

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $r = curl_exec($ch);

        $r = json_decode($r);

        if( sizeof($r->features) ) {
            $lng = $r->features[0]->geometry->coordinates[0];
            $lat = $r->features[0]->geometry->coordinates[1];

            $address->setLatitude($lng);
            $address->setLongitude($lat);
            $address->setGeocodeStatus(Address::GEOCODE_SUCCESS);
        } else {
            $address->setGeocodeStatus(Address::GEOCODE_ERROR);
        }
    }

    /**
     * Geocode the csv file. Appends result columns.
     * @see http://adresse.data.gouv.fr/api/
     *
     * @param string $path
     */
    public function geocodeFile($path) {
        $url = self::endpoint . '/csv/';

        // the api only accept utf-8
        $content = file_get_contents($path);
        $encoding = mb_detect_encoding($content);
        if( $encoding != 'utf-8' ) {
            if($encoding)
                $content = mb_convert_encoding($content, 'utf-8', $encoding);
            else
                $content = mb_convert_encoding($content, 'utf-8', 'pass');

            file_put_contents($path, $content);
        }

        $cfile = curl_file_create($path, 'text/csv', 'data');

        $payload = [
            'data' => $cfile,
            'columns' => ['city', 'zip', 'street'],
            'postcode' => 'zip'
        ];

        $payload = $this->_http_build_query_fix($payload);

        $ch = curl_init($url);
        curl_setopt($ch, CURLOPT_POST, true);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $r = curl_exec($ch);

        $infos = curl_getinfo($ch);

        if ($infos['http_code'] != 200) {
            throw new \Exception(sprintf('csv geocoding failed (http %d)', $infos['http_code']));
        }

        file_put_contents($path, $r);
    }

    // fix for curl postfields with array parameter AND file
    private function _http_build_query_fix($data) {
        if(!is_array($data)) {
            return $data;
        }
        foreach($data as $key => $val) {
            if(is_array($val)) {
                foreach($val as $k => $v) {
                    if(is_array($v)) {
                        $data = array_merge($data, $this->_http_build_query_fix(array( "{$key}[{$k}]" => $v)));
                    } else {
                        $data["{$key}[{$k}]"] = $v;
                    }
                }
                unset($data[$key]);
            }
        }
        return $data;
    }
}