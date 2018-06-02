<?php

namespace BiteCodes\TaxonomyBundle\Repository;

use Doctrine\ORM\EntityRepository;

class TaxonomyRepository extends EntityRepository
{
    /**
     * @param $title
     * @return mixed
     */
    public function findRoot($title)
    {
        return $this
            ->createQueryBuilder('t')
            ->where('t.title = :title')
            ->setParameter('title', $title)
            ->andWhere('t.parent IS NULL')
            ->getQuery()
            ->getOneOrNullResult();
    }
}