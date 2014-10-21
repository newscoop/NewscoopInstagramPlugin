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
use Newscoop\InstagramPluginBundle\Entity\InstagramPhoto;

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
        ->addArgument('pull', InputArgument::OPTIONAL, 'number of photos to load from instagram')
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
        $pull = $input->getArgument('pull');
        $photos = array();
        $photosAdded = 0;
        $cacheService = $this->getContainer()->get('newscoop.cache');
        $cacheKey = $cacheService->getCacheKey(array("tag", $tag), 'instagram_photos');
        $url = $baseurl . "tags/" . $tag . "/media/recent?client_id=" . $clientId;
        if ($pull) {
            $maxCount = $pull;
        }

        try {
            $em = $this->getContainer()->getService('em');
            $instagramService = $this->getContainer()->getService('newscoop_instagram_plugin.instagram_service');

            $output->writeln("Looking for " . $cacheKey . " in cache");
            if ($cacheService->contains($cacheKey)) {
                // TODO: figure out why this is never happening for some reason
                $photos = json_decode($cacheService->fetch($cacheKey), true);
                $output->writeln("Loaded " . count($photos) . " photos from cache");
            } else {
                $client = new \Buzz\Client\Curl();
                $client->setTimeout(3600);
                $browser = new \Buzz\Browser($client);
                $response =  $browser->get($url);
                $results = json_decode($response->getContent(), true);

                // we must paginate through all the results
                while (count($photos) < $maxCount) {
                    $photos = array_merge($photos, $results['data']); 
                    $output->writeln("...recieved " . count($photos) . " photos");
                    if ($results['pagination']['next_url']) {
                        $nextUrl = $results['pagination']['next_url'];
                        $response =  $browser->get($nextUrl);
                        $results = json_decode($response->getContent(), true);
                    } else {
                        // we aren't going to get $maxCount
                        break;
                    }
                }

                $output->writeln("Saving " . $cacheKey . " in cache");
                $cacheService->save($cacheKey, json_encode($photos));
            }

            $output->writeln("Processing " . count($photos) . " photos...");
            foreach ($photos as $photo) {
                // check if we already have this photo
                if (!$instagramService->exists($photo['id'])) {
                    $output->writeln("Adding " . $photo['id']);
                    $instagramPhoto = $instagramService->saveInstagramPhoto($photo);
                    $photosAdded++;
                }
            }

            $output->writeln('<info>Finished...' . $photosAdded . ' records ingested.</info>');

        } catch (\Exception $e) {
            $output->writeln('<error>Error occured: '.$e->getMessage().'</error>');

            return false;
        }
    }

}
