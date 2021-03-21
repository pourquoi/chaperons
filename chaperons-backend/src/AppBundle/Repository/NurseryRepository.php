<?php

namespace AppBundle\Repository;

/**
 * NurseryRepository
 *
 * This class was generated by the Doctrine ORM. Add your own custom
 * repository methods below.
 */
class NurseryRepository extends \Doctrine\ORM\EntityRepository
{
    public function findNotInIds($ids) {
        return $this->createQueryBuilder('n')
            ->where('n.source_id not in (:ids)')
            ->setParameter('ids', $ids)
            ->getQuery()->getResult();
    }
}
