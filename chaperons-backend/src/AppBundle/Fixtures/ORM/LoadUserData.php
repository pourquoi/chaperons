<?php

namespace AppBundle\Fixtures\ORM;

use AppBundle\Entity\User;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadUserData implements FixtureInterface, OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $user = new User();
        $user->setApiKey('key123');
        $user->setUsername('bob');
        $user->setPassword('$2y$13$xX29peiGiwXKlgdQrgIF8OZo7ldhdGbP7BgtJXmAYnUQkGlpAweUq');

        $manager->persist($user);
        $manager->flush();
    }
}