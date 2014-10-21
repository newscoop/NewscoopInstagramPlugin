<?php
/**
 * @license http://www.gnu.org/licenses/gpl-3.0.txt
 */

/**
 * List Yourtube Videos block
 *
 * @param array $params
 * @param string $content
 * @param Smarty_Internal_Template $smarty
 * @param bool $repeat
 * @return string
 */

use Newscoop\InstagramPluginBundle\Entity\InstagramPhoto;

function smarty_block_list_instagram_photos(array $params, $content, &$smarty, &$repeat)
{
    if (empty($params['tag'])) {
        return;
    }

    $container = \Zend_Registry::get('container');
    $config = $container->getParameter('instagram_bundle');
    $em = $container->get('em');
    $cacheService = $container->get('newscoop.cache');
    $cacheKey = "instagram_photos_list_" . $params['tag'];

    if ($params['length']) {
        $length = $params['length'];
    } else {
        $length = $config['max_count'];
    }

    if (!isset($content)) {
        // load the list from entites InstagramPhoto
        try {
            $photos = $em->getRepository('Newscoop\InstagramPluginBundle\Entity\InstagramPhoto')
                ->createQueryBuilder('p')
                ->where('p.tags LIKE :tag')
                ->andWhere('p.is_active = 1')
                ->setParameter('tag', '%'.$params['tag'].'%')
                ->addOrderBy('p.createdAt', 'DESC')
                ->setMaxResults($length)
                ->getQuery()
                ->getResult();
            $cacheService->save($cacheKey, $photos);
        } catch(\Exception $e) {
            error_log($e->getMessage());
        }
    }

    $photos = $cacheService->fetch($cacheKey);

    if (!empty($photos)) {
        // load the current record
        $photo = array_shift($photos);
        $smarty->assign('photo', $photo); 
        $smarty->assign('photoIndex', abs(count($photos) - $length)); 
        $cacheService->save($cacheKey, $photos);
        $repeat = true;
    } else {
        $repeat = false;
    } 

    return $content;
}

