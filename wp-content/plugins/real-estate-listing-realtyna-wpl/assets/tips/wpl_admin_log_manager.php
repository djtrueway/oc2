<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();
$index = 1;

$content = '<h3>'.__('WPL Logs', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("WPL uses an advanced log system that logs almost everything. If needed you can enable the logs from WPL Settings menu to see the logs here. It's not recommended to enable the logs if you don't need it.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Search Logs', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("You can search on logs using this complete search form.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.log_tools', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'middle'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$next_page = 'wpl_admin_user_manager';
if(wpl_global::check_addon('pro')) $next_page = 'wpl_admin_payments';

$content = '<h3>'.__('Delete Logs', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("If you want to remove all logs, you can do it using this button simply.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.delete_button .button', 'content'=>$content, 'position'=>array('edge'=>'right', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page='.$next_page.'&wpltour=1";'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

return $tips;