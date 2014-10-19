<?php
/**
 * @package   Newscoop\InstagramPluginBundle
 * @author    Mark Lewis <mark.lewis@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license   http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\InstagramPluginBundle\Command;

use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Console\Input\InputOption;
use Symfony\Component\Console;
use Symfony\Bundle\FrameworkBundle\Command\ContainerAwareCommand;

/**
 * Gets instagram photos via instagram api by hashtag and insert into plugin_instagram_photo 
 */
class IngestInstagramPhotosCommand extends ContainerAwareCommand
{
    /**
     */
    protected function configure()
    {
        $this
        ->setName('instagram_photos:ingest')
        ->addArgument('tag', InputArgument::REQUIRED, 'instagram hashtag')
        ->setDescription('Gets instagram photos by tag and insert into plugin_instagram_photo');
    }

    /**
     */
    protected function execute(Console\Input\InputInterface $input, Console\Output\OutputInterface $output)
    {
        $config = $this->getContainer()->getParameter('instagram_bundle');
        $baseurl = $config['baseurl'];
        $clientId = $config['client_id'];
        $maxCount = $config['max_count'];
        $tag = $input->getArgument('tag');
        $photoCount = 0;
        $cacheService = $container->get('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey("photos_found_" . $tag , 'instagram_photos');
        $url = $baseurl . $tag . "//media/recent?count=" . $maxCount . "&client_id=" . $clientId;

        try {
            $em = $this->getContainer()->getService('em');
            $instagramService = $this->getContainer()->getService('newscoop_instagram_plugin.instgram_service');

            $client = new \Zend_Http_Client($url);
            $client->setMethod(Zend_Http_Client::GET);
            $results = json_decode($client->request()->getBody(), true);
            $cacheService->save($cacheKey, json_encode($results['items']));
            $photos = json_decode($cacheService->fetch($cacheKey), true);

            $output->writeln('<info>Finished... '.$photoCount.' records ingested...</info>');
        } catch (\Exception $e) {
            $output->writeln('<error>Error occured: '.$e->getMessage().'</error>');

            return false;
        }
    }

}
