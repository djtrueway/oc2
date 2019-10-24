<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();
$index = 1;

$content = '<h3>'.__('WPL Notifications', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Here you can find WPL notifications. You can disable or enable the notification and edit the notifications' content and recipients.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$next_page = 'wpl_admin_user_manager';
if(wpl_global::check_addon('pro')) $next_page = 'wpl_admin_log_manager';

$content = '<h3>'.__('Edit Notifications', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Click notification title to redirect to edit page of each notification.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'table.widefat.page tr:nth-child(1) td:nth-child(2) a', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page='.$next_page.'&wpltour=1";'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

return $tips;