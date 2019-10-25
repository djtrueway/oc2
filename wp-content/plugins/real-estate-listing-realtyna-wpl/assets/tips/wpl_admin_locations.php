<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();

$content = '<h3>'.__('WPL Locations', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('WPL is designed for world wide usage so every user around the world can use and localize it per their needs. You can select your desired country using this menu and disable default country simply.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>1, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('See All countries', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('You can see all WPL countries here.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>2, 'selector'=>'.location_tools .button:first', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Search on countries', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Also you can search on listed locations here.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>3, 'selector'=>'#wpl_search_location', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Add new locations', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Don't you see your desired location? Add it to here.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>4, 'selector'=>'#wpl_add_location_item', 'content'=>$content, 'position'=>array('edge'=>'left', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Add new locations', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("WPL uses a hierarchy system for locations. You can go to the next level here.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>5, 'selector'=>'table.widefat.page tr:nth-child(1) td:nth-child(3) a', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page=wpl_admin_settings&wpltour=1";'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

return $tips;