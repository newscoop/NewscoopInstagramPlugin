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
function smarty_block_list_youtube_videos(array $params, $content, &$smarty, &$repeat)
{
    if (empty($params['tag'])) {
        return;
    }

    $container = \Zend_Registry::get('container');
    $cacheService = $container->get('newscoop.cache');
    $config = $container->getParameter('instagram_plugin');
    $cacheKey = $cacheService->getCacheKey("photos_found_" . $tag , 'instagram_photos');

    if (!isset($content)) {
        // load the list from entites InstagramPhoto
        $cacheService->save($cacheKey, ($results['json']));
    }

    $photos = json_decode($cacheService->fetch($cacheKey), true);

    if (!empty($photos)) {
        // load the current record
        $photo = array_shift($photos);
        $smarty->assign('photo', $photo['json']); 
        $cacheService->save($cacheKey, json_encode($photos));
        $repeat = true;
    } else {
        $repeat = false;
    } 

    return $content;
}

