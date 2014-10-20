<?php

/**
 * @package Newscoop\InstagramPluginBundle
 * @author Mark Lewis <mark.lewis@sourcefabric.org>
 */

namespace Newscoop\InstagramPluginBundle\Services;

use Doctrine\ORM\EntityManager;
use Newscoop\InstagramPluginBundle\Entity\InstagramPhoto;
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
     * @param int|string $id InstagramPhoto id
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
     * Tests if a photo already exists by given id
     *
     * @param string $id
     *
     * @return bool
     */
    public function exists($id)
    {
        $em = $this->container->get('em');
        $photo = $this->getRepository()
            ->findOneById($id);

        if ($photo) {
          return true;
        }

        return false;
    }

    /**
     * Takes JSON response from the instagram api and saves an Entity\InstagramPhoto
     *
     * @param text $photo
     *
     * @return Entity\InstagramPhoto
     */
    public function saveInstagramPhoto($photo)
    {
        $em = $this->container->get('em');

        try {
            //TODO: extract relational data from json
            $instagramPhoto = new InstagramPhoto();
            $instagramPhoto->setId($photo['id'])
                ->setTags(implode(",", $photo['tags']))
                ->setLink($photo['link'])
                ->setLocationName($photo['location']['name'])
                ->setLocationLongitude($photo['location']['longitude'])
                ->setLocationLatitude($photo['location']['latitude'])
                ->setInstagramUserId($photo['user']['id'])
                ->setInstagramUserName($photo['user']['username'])
                ->setLowResolutionUrl($photo['images']['low_resolution']['url'])
                ->setLowResolutionWidth($photo['images']['low_resolution']['width'])
                ->setLowResolutionHeight($photo['images']['low_resolution']['height'])
                ->setThumbnailUrl($photo['images']['thumbnail']['url'])
                ->setThumbnailWidth($photo['images']['thumbnail']['width'])
                ->setThumbnailHeight($photo['images']['thumbnail']['height'])
                ->setStandardResolutionUrl($photo['images']['standard_resolution']['url'])
                ->setStandardResolutionWidth($photo['images']['standard_resolution']['width'])
                ->setStandardResolutionHeight($photo['images']['standard_resolution']['height'])
                ->setCaption($photo['caption']['text'])
                ->setCreatedAt(new \DateTime(date('Y-m-d H:i:s',$photo['created_time'])))
                ->setJson(json_encode($photo));
            $em->persist($instagramPhoto);
            $em->flush();
        } catch (\Exception $e) {
            print('Error: ' . $e->getMessage() . "\n");
        }

        return $instagramPhoto;
    }

    /**
     * Get photo by given id
     *
     * @param int|string $id IstagramPhoto id
     *
     * @return InstagramPhoto
     */
    public function getInstagramPhotoById($id)
    {
        $em = $this->container->get('em');
        $photo = $this->getRepository()
            ->findOneById($id);

        if ($photo) {
            return $photo;
        }
        
        return false;
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

