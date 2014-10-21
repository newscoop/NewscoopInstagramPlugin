<?php

/**
 * @package Newscoop\InstagramPluginBundle
 * @author Mark Lewis <mark.lewis@sourcefabric.org>
 */

namespace Newscoop\InstagramPluginBundle\Repository;

use Doctrine\ORM\EntityRepository;
use Newscoop\InstagramPluginBundle\TemplateList\InstagramPhotoCriteria;
use Newscoop\ListResult;

/**
 * InstagramPhotoRepository
 */
class InstagramPhotoRepository extends EntityRepository
{
    /**
     * Get list for given criteria
     *
     * @param Newscoop\InstagramPluginBundle\TemplateList\InstagramPhotoCriteria $criteria
     *
     * @return Newscoop\ListResult
     */
    public function getListByCriteria(InstagramPhotoCriteria $criteria, $showResults = true)
    {
        $qb = $this->createQueryBuilder('a');
        $list = new ListResult();

        $qb->select('a');

        if (!empty($criteria->status)) {
            if (count($criteria->status) > 1) {
                $qb->andWhere($qb->expr()->orX('a.isActive = true', 'a.isActive = false'));
            } else {
                $qb->andWhere('a.isActive = :status');
                $qb->setParameter('status', $criteria->status[0] == 'true' ? true : false);
            }
        }

        if ($criteria->instagramUserName !== null) {
            $qb->andWhere('a.instagramUserName = :user')
                ->setParameter('user', $criteria->user);
        }

        if ($criteria->query) {
            $qb->andWhere($qb->expr()->orX(
                "(a.tags LIKE :query)", 
                "(a.instagramUserName LIKE :query)", 
                "(a.caption LIKE :query)", 
                "(a.locationName LIKE :query)"
            ));
            $qb->setParameter('query', '%' . trim($criteria->query, '%') . '%');
        }

        foreach ($criteria->perametersOperators as $key => $operator) {
            if ($key !== 'user') {
                $qb->andWhere('a.'.$key.' '.$operator.' :'.$key)
                    ->setParameter($key, $criteria->$key);
            }
        }

        $countQb = clone $qb;
        $list->count = (int) $countQb->select('COUNT(DISTINCT a)')->getQuery()->getSingleScalarResult();

        if ($criteria->firstResult != 0) {
            $qb->setFirstResult($criteria->firstResult);
        }

        if ($criteria->maxResults != 0) {
            $qb->setMaxResults($criteria->maxResults);
        }

        $metadata = $this->getClassMetadata();
        foreach ($criteria->orderBy as $key => $order) {
            if (array_key_exists($key, $metadata->columnNames)) {
                $key = 'a.' . $key;
            }

            $qb->orderBy($key, $order);
        }

        if (!$showResults) {
            return $qb->getQuery();
        }

        $list->items = $qb->getQuery()->getResult();

        return $list;
    }

    /**
     * Get InstagramPhoto count for given criteria
     *
     * @param  array $criteria
     * @return int
     */
    public function countBy(array $criteria = array())
    {
        $queryBuilder = $this->getEntityManager()->createQueryBuilder()
            ->select('COUNT(a)')
            ->from($this->getEntityName(), 'a');

        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $queryBuilder->andWhere("a.$property = :$property");
            }
        }

        $query = $queryBuilder->getQuery();
        foreach ($criteria as $property => $value) {
            if (!is_array($value)) {
                $query->setParameter($property, $value);
            }
        }

        return (int) $query->getSingleScalarResult();
    }
}
