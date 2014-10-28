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
use Symfony\Component\HttpFoundation\JsonResponse;
use Symfony\Component\Security\Core\Exception\AccessDeniedException;
use Newscoop\InstagramPluginBundle\TemplateList\InstagramPhotoCriteria;

class AdminController extends Controller
{
    /**
     * @Route("/admin/instagram")
     * @Template()
     */
    public function indexAction(Request $request)
    {
        $em = $this->container->get('em');
        $currentIngestJob = $this->getCurrentJob();
        $scheduleParts = array();
        preg_match('/^\*\/([0-9]+) .+/', $currentIngestJob->getSchedule(), $scheduleParts);
        $mins = $scheduleParts[1];
        list($console, $command, $tag, $limit) = split(" ", $currentIngestJob->getCommand());

        if ($request->isMethod('POST')) {
            $newTag = $request->request->get('tag');
            $newLimit = $request->request->get('limit');
            $newMins = $request->request->get('mins');
            try {
                $newCommand = "$console $command $newTag $newLimit";
                $newSchedule = "*/$newMins * * * *"; 
                $currentIngestJob->setCommand($newCommand);
                $currentIngestJob->setSchedule($newSchedule);
                $em->flush();
                $status = true;
                $message = "";
            } catch (\Exception $e) {
                $status = false;
                $message = $e->getMessage();
            }
            // return JSON response with status, message
            return new JsonResponse(array("status" => $status, "message" => $message));
        }

        return array(
            'tag' => $tag,
            'limit' => $limit,
            'mins' => $mins
        );
    }

    /**
     * @Route("admin/instagram/ingest", options={"expose"=true})
     */
    public function ingestAction(Request $request)
    {
        $instagramService = $this->container->getService('newscoop_instagram_plugin.instagram_service');
        $config = $this->container->getParameter('instagram_bundle');
        $baseurl = $config['baseurl'];
        $clientId = $config['client_id'];
        $tag = $request->request->get('tag');
        $limit = $request->request->get('limit');
        $photos = array();
        $photosAdded = 0;
        $url = $baseurl . "tags/" . $tag . "/media/recent?client_id=" . $clientId;

        try {
            $client = new \Buzz\Client\Curl();
            $client->setTimeout(3600);
            $browser = new \Buzz\Browser($client);
            $response =  $browser->get($url);
            $results = json_decode($response->getContent(), true);

            // we must paginate through all the results
            while (count($photos) < $limit) {
                $photos = array_merge($photos, $results['data']); 
                if ($results['pagination']['next_url']) {
                    $nextUrl = $results['pagination']['next_url'];
                    $response =  $browser->get($nextUrl);
                    $results = json_decode($response->getContent(), true);
                } else {
                    // we aren't going to get $maxCount
                    break;
                }
            }

            foreach ($photos as $photo) {
                // check if we already have this photo
                if (!$instagramService->exists($photo['id'])) {
                    $instagramPhoto = $instagramService->saveInstagramPhoto($photo);
                    $photosAdded++;
                }
            }
            $status = true;
            $message = "Added " . $photosAdded . " photos";
        } catch(\Exception $e) {
            $status = false;
            $message = 'Error: ' . $e->getMessage();
        }

        return new JsonResponse(array("status" => $status, "message" => $message));
    }


    /**
     * @Route("admin/instagram/load/", options={"expose"=true})
     */
    public function loadPhotosAction(Request $request)
    {   
        $em = $this->get('em');
        $cacheService = $this->get('newscoop.cache');
        $instagramService = $this->container->get('newscoop_instagram_plugin.instagram_service');
        $criteria = $this->processRequest($request);
        $photosCount = $instagramService->countBy(array('isActive' => true)); 
        $photosInactiveCount = $instagramService->countBy(array('isActive' => false));
     
        $cacheKey = array('instagram_photos__'.md5(serialize($criteria)), $photosCount, $photosInactiveCount);
        if ($cacheService->contains($cacheKey)) {
            $responseArray = $cacheService->fetch($cacheKey);                                                                                               
        } else { 
            $photos = $em->getRepository('Newscoop\InstagramPluginBundle\Entity\InstagramPhoto')->getListByCriteria($criteria);                                         
            
            $processed = array();
            foreach ($photos as $photo) {
                $processed[] = array(
                    'id' => $photo->getId(),
                    'instagramUserName' => $photo->getInstagramUserName(),
                    'tags' => $photo->getTags(),
                    'caption' => $photo->getCaption(),
                    'thumbnailUrl' => $photo->getThumbnailUrl(),
                    'locationName' => $photo->getLocationName(),
                    'createdAt' => $photo->getCreatedAt(),
                    'isActive' => $photo->getIsActive()
                );                                                                                           
            }                                                                                                                                               
            
            $responseArray = array(
                'records' => $processed,
                'queryRecordCount' => $photos->count,
                'totalRecordCount'=> count($photos->items)
            );                                                                                                                                              
            
            $cacheService->save($cacheKey, $responseArray);                                                                                                 
        }                                                                                                                                                   
        
        return new JsonResponse($responseArray);                                                                                                            
    }

    /**
     * Load current scheduled instagram ingest job
     *
     * @return Newscoop\Entity\CronJob
     */
    private function getCurrentJob()
    {
        $em = $this->get('em');
        $schedulerService = $this->get('newscoop.scheduler');
        $job = $em->getRepository('Newscoop\Entity\CronJob')->findOneByName("Instagram plugin ingest photos cron job");
        return $job;
    }

    /**
     * Process request parameters
     *
     * @param Request $request Request object
     *
     * @return InstagramPhotoCriteria
     */
    private function processRequest(Request $request)
    {   
        $criteria = new InstagramPhotoCriteria();                                                                                                             
        
        if ($request->query->has('sorts')) {
            foreach ($request->get('sorts') as $key => $value) {
                $criteria->orderBy[$key] = $value == '-1' ? 'desc' : 'asc';                                                                                 
            }                                                                                                                                               
        }                                                                                                                                                   
        
        if ($request->query->has('queries')) {
            $queries = $request->query->get('queries');                                                                                                     
            
            if (array_key_exists('search', $queries)) {                                                                                                     
                $criteria->query = $queries['search'];                                                                                                      
            }                                                                                                                                               
            
        }                                                                                                                                                   
        
        $criteria->maxResults = $request->query->get('perPage', 10);
        if ($request->query->has('offset')) {
            $criteria->firstResult = $request->query->get('offset');
        }
        
        return $criteria;                                                                                                                                   
    }


    /**
     * @Route("/admin/instagram/activate/{id}", options={"expose"=true})
     */
    public function activateAction(Request $request, $id)
    {
        try {
            $em = $this->container->get('em');
            $instagramService = $this->container->get('newscoop_instagram_plugin.instagram_service');
            $status = true;

            $photo = $em->getRepository('Newscoop\InstagramPluginBundle\Entity\InstagramPhoto')
                ->findOneById($id);
            $instagramService->activateInstagramPhoto($photo);

        } catch (\Exception $e) {
            $status = false;
        }

        return new JsonResponse(array(
            'status' => $status
        ));
    }

    /**
     * @Route("admin/instagram/deactivate/{id}", options={"expose"=true})
     */
    public function deactivateAction(Request $request, $id)
    {
        try {
            $em = $this->container->get('em');
            $instagramService = $this->container->get('newscoop_instagram_plugin.instagram_service');
            $status = true;

            $photo = $em->getRepository('Newscoop\InstagramPluginBundle\Entity\InstagramPhoto')
                ->findOneById($id);
            $instagramService->deactivateInstagramPhoto($photo);
        } catch (\Exception $e) {
            $status = false;
        }

        return new JsonResponse(array(
            'status' => $status
        ));
    }
}
