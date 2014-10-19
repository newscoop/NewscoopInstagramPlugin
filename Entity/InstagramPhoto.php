<?php
/**
 * @package Newscoop\InstagramPluginBundle
 * @author Mark Lewis <mark.lewis@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\InstagramPluginBundle\Entity;

use Doctrine\ORM\Mapping AS ORM;

/**
 * InstagramPhoto entity
 *
 * @ORM\Entity()
 * @ORM\Table(name="plugin_insagram_photo")
 */
class InstagramPhoto
{
    /**
     * @ORM\Id()
     * @ORM\Column(type="integer", name="id")
     * @var int
     */
    protected $id;

    /**
     * @ORM\Column(type="text",  name="tags")
     * @var string
     */
    protected $tags;

    /**
     * @ORM\Column(type="text",  name="location_name")
     * @var string
     */
    protected $locationName;

    /**
     * @ORM\Column(type="float",  name="location_longitude")
     * @var float
     */
    protected $locationLongitude;

    /**
     * @ORM\Column(type="float",  name="location_latitude")
     * @var float
     */
    protected $locationLatitude;

    /**
     * @ORM\Column(type="integer", name="instagram_userid")
     * @var int
     */
    protected $instagramUserId;

    /**
     * @ORM\Column(type="string", length="255", name="instagram_username")
     * @var string 
     */
    protected $instagramUserName;

    /**
     * @ORM\Column(type="string", length="255", name="low_resolution_url")
     * @var string 
     */
    protected $lowResolutionUrl;

    /**
     * @ORM\Column(type="int", name="low_resolution_width")
     * @var int 
     */
    protected $lowResolutionWidth;

    /**
     * @ORM\Column(type="int", name="low_resolution_height")
     * @var int 
     */
    protected $lowResolutionHeight;

    /**
     * @ORM\Column(type="string", length="255", name="thumbnail_url")
     * @var string 
     */
    protected $thumbnailUrl;

    /**
     * @ORM\Column(type="int", name="thumbnail_width")
     * @var int 
     */
    protected $thumbnailWidth;

    /**
     * @ORM\Column(type="int", name="thumbnail_height")
     * @var int 
     */
    protected $thumbnailHeight;

    /**
     * @ORM\Column(type="string", length="255", name="standard_resolution_url")
     * @var string 
     */
    protected $standardResolutionUrl;

    /**
     * @ORM\Column(type="int", name="standard_resolution_width")
     * @var int 
     */
    protected $standardResolutionWidth;

    /**
     * @ORM\Column(type="int", name="standard_resolution_height")
     * @var int 
     */
    protected $standardResolutionHeight;

    /**
     * @ORM\Column(type="string", length="255", name="caption")
     * @var string 
     */
    protected $caption;

    /**
     * @ORM\Column(type="datetime", name="created_at")
     * @var datetime
     */
    protected $createdAt;

    /**
     * @ORM\Column(type="text", name="json")
     * @var string
     */
    protected $json;

    /**
     * @ORM\Column(type="datetime", name="imported_at")
     * @var datetime
     */
    protected $importedAt;

    /**
     * @ORM\Column(type="boolean", name="is_active")
     * @var boolean
     */
    protected $is_active;

    public function __construct()
    {
        $this->setImportedAt(new \DateTime());
        $this->setIsActive(true);
    }

    /**
     * Gets the value of id.
     *
     * @return int
     */
    public function getId()
    {
        return $this->id;
    }

    /**
     * Sets the value of id.
     *
     * @param int $id the id
     *
     * @return self
     */
    public function setId($id)
    {
        $this->id = $id;

        return $this;
    }

    /**
     * Gets the value of tags.
     *
     * @return string
     */
    public function getTags()
    {
        return $this->tags;
    }

    /**
     * Sets the value of tags.
     *
     * @param int $tags the tags
     *
     * @return self
     */
    public function setTags($tags)
    {
        $this->tags = $tags;

        return $this;
    }

    /**
     * Gets the value of locationName.
     *
     * @return string 
     */
    public function getLocationName()
    {
        return $this->locationName;
    }

    /**
     * Sets the value of locationName.
     *
     * @param string $locationName the locationName 
     *
     * @return self
     */
    public function setLocationName($locationName)
    {
        $this->locationName = $locationName;

        return $this;
    }

    /**
     * Gets the value of locationLongitude.
     *
     * @return float 
     */
    public function getLocationLongitude()
    {
        return $this->locationLongitude;
    }

    /**
     * Sets the value of locationLongitude.
     *
     * @param float $locationLongitude the locationLongitude
     *
     * @return self
     */
    public function setLocationLongitude($locationLongitude)
    {
        $this->locationLongitude = $locationLongitude;

        return $this;
    }

    /**
     * Gets the value of locationLatitude.
     *
     * @return float 
     */
    public function getLocationLatitude()
    {
        return $this->locationLatitude;
    }

    /**
     * Sets the value of locationLatitude.
     *
     * @param float $locationLatitude the locationLatitude
     *
     * @return self
     */
    public function setLocationLatitude($locationLatitude)
    {
        $this->locationLatitude = $locationLatitude;

        return $this;
    }

    /**
     * Gets the value of instagramUserId.
     *
     * @return int
     */
    public function getInstagramUserId()
    {
        return $this->instagramUserId;
    }

    /**
     * Sets the value of instagramUserId.
     *
     * @param int $instagramUserId the instagramUserId
     *
     * @return self
     */
    public function setInstagramUserId($instagramUserId)
    {
        $this->instagramUserId = $instagramUserId;

        return $this;
    }

    /**
     * Gets the value of caption.
     *
     * @return string 
     */
    public function getCaption()
    {
        return $this->caption;
    }

    /**
     * Sets the value of caption.
     *
     * @param string $caption the caption 
     *
     * @return self
     */
    public function setCaption($caption)
    {
        $this->caption = $caption;

        return $this;
    }

    /**
     * Gets the value of json.
     *
     * @return string 
     */
    public function getJson()
    {
        return $this->json;
    }

    /**
     * Sets the value of json.
     *
     * @param string $json the json 
     *
     * @return self
     */
    public function setJson($json)
    {
        $this->json = $json;

        return $this;
    }

    /**
     * Gets the value of created_at.
     *
     * @return datetime
     */
    public function getCreatedAt()
    {
        return $this->created_at;
    }

    /**
     * Sets the value of created_at.
     *
     * @param datetime $created_at the created  at
     *
     * @return self
     */
    public function setCreatedAt(\DateTime $created_at)
    {
        $this->created_at = $created_at;

        return $this;
    }

    /**
     * Gets the value of is_active.
     *
     * @return boolean
     */
    public function getIsActive()
    {
        return $this->is_active;
    }

    /**
     * Sets the value of is_active.
     *
     * @param boolean $is_active the is  active
     *
     * @return self
     */
    public function setIsActive($is_active)
    {
        $this->is_active = $is_active;

        return $this;
    }

}
