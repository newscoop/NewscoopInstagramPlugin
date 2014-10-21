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
use Newscoop\InstagramPluginBundle\TemplateList\InstagramPhotoCriteria;

/**
 * Route("/instagram")
 */
class InstagramController extends Controller
{
    /**
     * @Route("/instagram/photosearch")
     */
    public function photoSearchAction(Request $request)
    {
        $em = $this->get('em');
        $cacheService = $this->get('newscoop.cache');
        $templatesService = $this->container->get('newscoop.templates.service');
        $instagramService = $this->container->get('newscoop_instagram_plugin.instagram_service');
        $criteria = new InstagramPhotoCriteria();
        if ($request->query->has('search')) {
            $criteria->query = $request->query->get('search');
        }        
        $criteria->maxResults = $request->query->get('perPage', 10);
        if ($request->query->has('offset')) {
            $criteria->firstResult = $request->query->get('offset');
        }
        $cacheKey = array('instagram_photos__'.md5(serialize($criteria)));

        if ($cacheService->contains($cacheKey)) {
            $responseArray = $cacheService->fetch($cacheKey);                                                                                               
        } else { 
            $photos = $em->getRepository('Newscoop\InstagramPluginBundle\Entity\InstagramPhoto')->getListByCriteria($criteria);
            $cacheService->save($cacheKey, $photos);                                                                                                 
        }

        $smarty = $templatesService->getSmarty();
        $templateDir = array_shift($smarty->getTemplateDir());
        $templateFile = "_views/instagram_search_results.tpl";
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');

        // render _views/instagram_photo.tpl if it exists, if not render the plugin template instead
        if (!file_exists($templateDir . $templateFile)) {
            $templateFile = __DIR__ . "/../Resources/views/Instagram/instagram_search_results.tpl";
        }

        $next = ($request->query->get('offset')+$criteria->maxResults);
        $prev = ($request->query->get('offset')-$criteria->maxResults);
        if ($next < count($photos)) { 
            $nextPageUrl = $this->generateUrl('newscoop_instagramplugin_instagram_photosearch', array(
                'search' => $request->query->get('search'),
                'offset' => $next,
                'perPage' => $request->query->get('perPage')
            ));
        } else {
            $nextPageUrl = "#";
        }

        if ($criteria->firstResult > 0) { 
            $prevPageUrl = $this->generateUrl('newscoop_instagramplugin_instagram_photosearch', array(
                'search' => $request->query->get('search'),
                'offset' => $prev,
                'perPage' => $request->query->get('perPage')
            ));
        } else {
            $prevPageUrl = "#";
        }

        $response->setContent($templatesService->fetchTemplate(
            $templateFile, 
            array(
                'instagramPhotos' => $photos, 
                'instagramPhotoCount' => count($photos),
                'nextPageUrl' => $nextPageUrl,
                'prevPageUrl' => $prevPageUrl
            )
        ));
        return $response;

    }

    /**
     * @Route("/instagram/photolist")
     */
    public function photoListAction(Request $request)
    {
        // this is just an example page to show how to use the smarty {{ list_instagram_photo }} block
        $templatesService = $this->container->get('newscoop.templates.service');
        $smarty = $templatesService->getSmarty();
        $templateDir = array_shift($smarty->getTemplateDir());
        $templateFile = __DIR__ . "/../Resources/views/Instagram/instagram_photo_list.tpl";
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');
        $response->setContent($templatesService->fetchTemplate($templateFile));
        return $response;

    }

    /**
     * @Route("/instagram/photos/{id}")
     */
    public function photosAction($id, Request $request)
    {
        if (!$id) {
            throw new NotFoundHttpException('You must provice an id!');
        }

        $instagramService = $this->container->getService('newscoop_instagram_plugin.instagram_service');
        $templatesService = $this->container->get('newscoop.templates.service');

        $photo = $instagramService->getInstagramPhotoById($id);
        $smarty = $templatesService->getSmarty();
        $templateDir = array_shift($smarty->getTemplateDir());
        $templateFile = "_views/instagram_photo.tpl";
        $response = new Response();
        $response->headers->set('Content-Type', 'text/html');

        // render _views/instagram_photo.tpl if it exists, if not render the plugin template instead
        if (!file_exists($templateDir . $templateFile)) {
            $templateFile = __DIR__ . "/../Resources/views/Instagram/instagram_photo.tpl";
        }

        $response->setContent($templatesService->fetchTemplate(
            $templateFile, 
            array('instagramPhoto' => $photo)
        ));
        return $response;
    }
}

