<?php

namespace Tests\App;

class UserControllerTest extends BaseController
{
    public function testLogin()
    {
        $this->loadFixtures([
            'AppBundle\Fixtures\ORM\LoadUserData'
        ]);

        $user = $this->login('bob', 'pass1234');

        $this->assertEquals('key123', $user->api_key);
    }

    public function testGetUser()
    {
        $this->loadFixtures([
            'AppBundle\Fixtures\ORM\LoadUserData'
        ]);

        $user = $this->login('bob', 'pass1234');

        $user = $this->getResource('/api/users/' . $user->id, $user->api_key);

        $this->assertEquals('bob', $user->username);

        $infos = [];
        $user = $this->getResource('/api/users/' . $user->id, 'wrong_key', $infos);

        $this->assertEquals(403, $infos['http_code']);
    }

    public function testPostUser()
    {
        $user = ['user'=>[
            'username' => 'wendy',
            'password' => 'pass1234'
        ]];

        $r = $this->postResource('/api/users', $user);

        $this->assertEquals('wendy', $r->username);
    }
}