<?php

/**
 * @package Newscoop\InstagramPluginBundle
 * @author Mark Lewis <mark.lewis@sourcefabric.org>
 */

namespace Newscoop\InstagramPluginBundle\Service;

use Symfony\Component\HttpFoundation\Request;
use Newscoop\InstagramPluginBundle\Entity\InstagramPhoto;
use Symfony\Component\HttpKernel\Exception\NotFoundHttpException;
use Symfony\Component\DependencyInjection\Container;

/**
 * Instagram Service
 */
class InstagramService
{
    /** @var Container */
    protected $container;

    /**
     * @param Container $container
     */
    public function __construct(Container $container)
    {
        $this->container = $container;
    }

    /**
     * Delete photo by given id
     *
     * @param int|string $id Classified id
     *
     * @return boolean
     */
    public function deleteInstagramPhoto($id)
    {
        $em = $this->container->get('em');
        $photo = $this->getRepository()
            ->findOneById($id);

        if ($photo) {
            $em->remove($photo);
            $em->flush();

            return true;
        }

        return false;
    }

    /**
     * Activate photo by given id
     *
     * @param InstagramPhoto $photo InstagramPhoto
     *
     * @return boolean
     */
    public function activateInstagramPhoto(InstagramPhoto $photo)
    {
        $em = $this->container->get('em');
        $photo->setIsActive(true);
        $em->flush();

        return true;
    }

    /**
     * Deactivate photo by given id
     *
     * @param InstagramPhoto $photo InstagramPhoto
     *
     * @return boolean
     */
    public function deactivateInstagramPhoto(InstagramPhoto $photo)
    {
        $em = $this->container->get('em');
        $photo->setIsActive(false);
        $em->flush();

        return true;
    }

    /**
     * Count photos by given criteria
     *
     * @param array $criteria
     *
     * @return int
     */
    public function countBy(array $criteria = array())
    {
        return $this->getRepository()->countBy($criteria);
    }

    /**
     * Get repository for announcments entity
     *
     * @return Newscoop\InstagramPluginBundle\Repository
     */
    private function getRepository()
    {
        $em = $this->container->get('em');

        return $em->getRepository('Newscoop\InstagramPluginBundle\Entity\InstagramPhoto');

    }
}

