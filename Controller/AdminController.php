<?php
/**
 * @package Newscoop\TagesWocheExtraBundle
 * @author Mark Lewis <mark.lewis@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\InstagramPluginBundle\Controller;

use Symfony\Bundle\FrameworkBundle\Controller\Controller;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Route;
use Sensio\Bundle\FrameworkExtraBundle\Configuration\Template;
use Symfony\Component\HttpFoundation\Request;
use Symfony\Component\HttpFoundation\Response;

/**
 * Route("/instagram")
 */
class AdminController extends Controller
{
    /**
     * @Route("/admin")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->container->get('em');
        //$instagramService = $this->container->getService('newscoop_instagram_plugin.instagram_service');
        $cacheService = $this->container->get('newscoop.cache');
        $cacheKey = "instagram_all_photos_list";

        if ($cacheService->contains($cacheKey)) {
            $photos = $cacheService->fetch($cacheKey);
        } else {
            $photos = $em->getRepository('Newscoop\InstagramPluginBundle\Entity\InstagramPhoto')->findAll();
            $cacheService->save($cacheKey, $photos);
        }

        return array('photos' => $photos);
    }
}
