<?php
/**
 * @package Newscoop\InstagramPluginBundle
 * @author Mark Lewis <mark.lewis@sourcefabric.org>
 * @copyright 2014 Sourcefabric o.p.s.
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

namespace Newscoop\InstagramPluginBundle\EventListener;

use Symfony\Component\EventDispatcher\EventSubscriberInterface;
use Newscoop\EventDispatcher\Events\GenericEvent;

/**
 * Event lifecycle management
 */
class LifecycleSubscriber implements EventSubscriberInterface
{
    private $em;
    
    protected $scheduler;

    protected $cronjobs;

    public function __construct($em, SchedulerService $scheduler)
    {
        $appDirectory = realpath(__DIR__.'/../../../../application/console');
        $this->em = $em;
        $this->scheduler = $scheduler;
        $this->cronjobs = array(
            "Instagram plugin ingest photos cron job" => array(
                'command' => $appDirectory . ' instagram_photos:ingest lennonwal 40',
                'schedule' => '*/15 * * * *',
            )
        );
    }

    public function install(GenericEvent $event)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($this->getClasses(), true);

        // Generate proxies for entities
        $this->em->getProxyFactory()->generateProxyClasses($this->getClasses(), __DIR__ . '/../../../../library/Proxy');
        $this->addJobs();
    }

    public function update(GenericEvent $event)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->updateSchema($this->getClasses(), true);

        // Generate proxies for entities
        $this->em->getProxyFactory()->generateProxyClasses($this->getClasses(), __DIR__ . '/../../../../library/Proxy');
    }

    public function remove(GenericEvent $event)
    {
        $tool = new \Doctrine\ORM\Tools\SchemaTool($this->em);
        $tool->dropSchema($this->getClasses(), true);
    }

    public static function getSubscribedEvents()
    {
        return array(
            'plugin.install.newscoop_instagram_plugin_bundle' => array('install', 1),
            'plugin.update.newscoop_instagram_plugin_bundle' => array('update', 1),
            'plugin.remove.newscoop_instagram_plugin_bundle' => array('remove', 1),
        );
    }

    /**
     * Add plugin cron jobs
     */
    private function addJobs()
    {
        foreach ($this->cronjobs as $jobName => $jobConfig) {
            $this->scheduler->registerJob($jobName, $jobConfig);
        }
    }

    private function getClasses()
    {
        return array(
            $this->em->getClassMetadata('Newscoop\InstagramPluginBundle\Entity\InstagramPhoto'),
        );
    }
}
