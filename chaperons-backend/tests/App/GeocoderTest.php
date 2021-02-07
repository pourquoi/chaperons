<?php

namespace Tests\App;

use App\CsvParser;
use App\Geocoder;

class GeocoderTest extends \PHPUnit_Framework_TestCase
{
    use CsvParser;

    public function testGeocodeFile()
    {
        $geocoder = new Geocoder();

        $filename = tempnam('/tmp', 'geocode_test');

        copy(__DIR__ . '/../test-carto.csv', $filename);

        $geocoder->geocodeFile($filename);

        $delimiter = $this->detectDelimiter($filename, 3);

        $f = new \SplFileObject($filename);
        $cols = $f->fgetcsv($delimiter);

        $this->assertContains('latitude', $cols);
        $this->assertContains('longitude', $cols);

        $header = array_flip($cols);

        $cols = $f->fgetcsv($delimiter);

        $this->assertTrue($cols[$header['latitude']] !== '');
    }
}