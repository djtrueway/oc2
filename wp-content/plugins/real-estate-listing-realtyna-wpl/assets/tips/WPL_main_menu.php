<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();

$content = '<h3>'.__('WPL Dashboard', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Welcome to WPL dashboard. Here, you will see information about WPL and its add-ons.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>1, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Optional add-ons', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('WPL has some optional add-ons for extending its functionality. You can download and install them, if needed.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>2, 'selector'=>'#wpl_dashboard_ni_addons h3', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('WPL Change-log', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Browse WPL change-log.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>3, 'selector'=>'#wpl_dashboard_changelog h3', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Update WPL PRO and its add-ons', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Here you can update WPL Pro and its add-ons.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>4, 'selector'=>'#wpl_dashboard_side_addons h3', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('WPL manual and Support', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Here you can download WPL manual and check its KB articles. You can find answer of your questions here. Please feel free to open a support ticket if you couldn\'t find an answer to your question in WPL manual and KB articles.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>5, 'selector'=>'#wpl_dashboard_side_support h3', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Browse KB articles', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('If you have a question, take a look through the following KB articles (they are searchable). In most cases, you will find your answer in minutes.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>6, 'selector'=>'#wpl_dashboard_side_announce h3', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page=wpl_admin_data_structure&wpltour=1";'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

return $tips;