<?php

/**
 * @package Newscoop\InstagramPluginBundle
 * @author Mark Lewis <mark.kewis@sourcefabric.org>
 */

namespace Newscoop\InstagramPluginBundle\TemplateList;

use Newscoop\Criteria;

/**
 * Available criteria for InstagramPhoto
 */
class InstagramPhotoCriteria extends Criteria
{
    /**
     * @var string
     */
    public $id;

    /**
     * @var string
     */
    public $tags;

    /**
     * @var string
     */
    public $caption;

    /**
     * @var string
     */
    public $locationName;

    /**
     * @var string
     */
    public $instagramUserName;

    /**
     * @var string
     */
    public $query;

    /**
     * @var array
     */
    public $orderBy = array('createdAt' => 'desc');

}
