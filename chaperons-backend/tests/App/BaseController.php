<?php

namespace Tests\App;


use AppBundle\Entity\User;
use Liip\FunctionalTestBundle\Test\WebTestCase;

class BaseController extends WebTestCase
{
    protected function getHost() {
        return 'localhost:8000';
    }

    protected function login($username, $password='pass1234')
    {
        $payload = array(
            'username' => $username,
            'password' => $password,
        );

        $infos = [];

        $r = $this->postResource('/api/users/logins', $payload, null, $infos);

        return $r;
    }

    protected function postResource($url, $data, $key = null, &$infos = null, $method = 'POST')
    {
        $payload = $data ? json_encode($data) : null;
        $headers = array(
            'Content-Type: application/json',
            'Content-Length: '.strlen($payload),
        );
        if ($key) {
            $headers[] = "X-AUTH-TOKEN: $key";
        }

        $ch = curl_init($this->getHost().'/app_test.php'.$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        if ($payload) {
            curl_setopt($ch, CURLOPT_POSTFIELDS, $payload);
        }
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $r = curl_exec($ch);


        if (null === $infos) {
            $infos = array();
        }
        $infos = curl_getinfo($ch);

        if ($infos['http_code'] == 500) {
            echo PHP_EOL.$r.PHP_EOL;
        }

        curl_close($ch);

        $json = json_decode($r);

        return null === $json ? $r : $json;
    }

    protected function putResource($url, $data, $key = null, &$infos = null, $method = 'PUT')
    {
        return $this->postResource($url, $data, $key, $infos, $method);
    }

    protected function getResource($url, $key = null, &$infos = null, $method = 'GET')
    {
        $headers = array();
        if ($key) {
            $headers[] = "X-AUTH-TOKEN: $key";
        }

        $ch = curl_init($this->getHost().'/app_test.php'.$url);
        curl_setopt($ch, CURLOPT_CUSTOMREQUEST, $method);
        curl_setopt($ch, CURLOPT_RETURNTRANSFER, true);
        curl_setopt($ch, CURLOPT_HTTPHEADER, $headers);
        curl_setopt($ch, CURLOPT_FOLLOWLOCATION, false);

        $r = curl_exec($ch);

        if (null === $infos) {
            $infos = array();
        }
        $infos = curl_getinfo($ch);

        if ($infos['http_code'] == 500) {
            echo PHP_EOL.$r.PHP_EOL;
        }

        curl_close($ch);

        $json = json_decode($r);

        return null === $json ? $r : $json;
    }
}