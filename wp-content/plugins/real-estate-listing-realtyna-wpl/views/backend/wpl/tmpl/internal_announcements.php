<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

// WPL Cache Instance
$wplcache = wpl_global::get_wpl_cache();

// Cache File
$cache_file = $wplcache->path('wpl_announcements.json');

// Check if cache file is valid
if($wplcache->valid($cache_file, 86400))
{
    $JSON = $wplcache->read($cache_file);
    $response = json_decode($JSON, true);
}
else
{
    $response = wpl_global::get_realtyna_announcements();
    
    // Save data to cache
    if(is_array($response))
    {
        $JSON = json_encode($response);
        $wplcache->write($cache_file, $JSON);
    }
}

// There is no announcements!
if(!isset($response['status']) or (isset($response['status']) and !$response['status'])) return;

$announcements = isset($response['announcements']) ? $response['announcements'] : array();
?>
<div class="sidebar-wp banner-side">
    <div class="side-15">
        <?php foreach($announcements as $announcement): ?>
        <div class="wpl-announcements">
            <?php echo strip_tags($announcement, '<img><p><a><div><span><ol><ul><li><strong><i><em>'); ?>
        </div>
        <?php endforeach; ?>
    </div>
</div>