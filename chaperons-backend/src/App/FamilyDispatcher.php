<?php

namespace App;

use AppBundle\Entity\Family;
use AppBundle\Entity\Map;
use Doctrine\ORM\EntityManager;
use Doctrine\ORM\Query\ResultSetMapping;

class FamilyDispatcher
{
    /** @var EntityManager */
    private $em;

    public function __construct($em) {
        $this->em = $em;
    }

    /**
     * Update the closest nurseries for each family.
     *
     * @param Map $map
     * @param bool $force
     */
    public function updateMapNurseries(Map $map, $force=false) {
        if( !$force ) {
            // find families with no nurseries association
            $ids = $this->em->createQuery('SELECT f.id FROM AppBundle:Family f LEFT JOIN f.nurseries n WHERE f.map=:map AND n.id IS NULL')
                ->setParameter('map', $map)
                ->getScalarResult();
        } else {
            $ids = $this->em->createQuery('SELECT f.id FROM AppBundle:Family f WHERE f.map=:map')
                ->setParameter('map', $map)
                ->getScalarResult();
        }

        foreach($ids as $family_id) {
            $this->updateFamilyNurseries($family_id['id']);
        }
    }

    /**
     * Update the family closest nurseries.
     *
     * @param int $family_id
     */
    public function updateFamilyNurseries($family_id) {
        $this->em->createNativeQuery('CALL update_close_nurseries(:family_id)', new ResultSetMapping())
            ->setParameter('family_id', $family_id)
            ->execute();
    }
}