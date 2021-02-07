<?php

namespace AppBundle\Fixtures\ORM;

use AppBundle\Entity\Address;
use AppBundle\Entity\Nursery;
use Doctrine\Common\DataFixtures\FixtureInterface;
use Doctrine\Common\DataFixtures\OrderedFixtureInterface;
use Doctrine\Common\Persistence\ObjectManager;

class LoadNurseryData implements FixtureInterface, OrderedFixtureInterface
{
    public function getOrder()
    {
        return 1;
    }

    public function load(ObjectManager $manager)
    {
        $address = new Address();
        $address->setCity('montreuil');
        $address->setStreet('30 rue robespierre');
        $address->setLatitude(48.865858);
        $address->setLongitude(2.450423);
        $address->setZip(93100);
        $address->setGeocodeStatus(1);

        $nursery = new Nursery();
        $nursery->setAddress($address);
        $nursery->setName('montreuil');
        $nursery->setNature('DSP');
        $nursery->setType('MICRO');

        $manager->persist($nursery);
        $manager->flush();
    }
}