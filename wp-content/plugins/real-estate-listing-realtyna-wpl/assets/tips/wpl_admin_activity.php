<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();

$content = '<h3>'.__('WPL Activities', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Activity is an internal WPL widget used to show some parts of the WPL interface. For example, Google Maps, Agent info, Property gallery etc. are shown by using an activity in WPL. Activities are contained inside of WPL views (not WordPress sidebars).', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>1, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Filter Activities', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('You can filter activities here. Lets search for "Google"!', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>2, 'selector'=>'#activity_manager_filter', 'content'=>$content, 'position'=>array('edge'=>'left', 'align'=>'center'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'), 'code'=>'wplj("#activity_manager_filter").val("Google");wplj("#activity_manager_filter").trigger("keyup");'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Manage Activities', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('You can toggle activities simply by clicking on an action icon.', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>3, 'selector'=>'#wpl_actions_td_thead', 'content'=>$content, 'position'=>array('edge'=>'right', 'align'=>'center'), 'buttons'=>array(2=>array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page=wpl_admin_notifications&wpltour=1";'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'), 'code'=>'wplj("#activity_manager_filter").val("");wplj("#activity_manager_filter").trigger("keyup");')));

return $tips;