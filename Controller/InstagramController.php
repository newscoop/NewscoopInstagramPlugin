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
class InstagramController extends Controller
{
    /**
     * @Route("/photos/{id}")
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

