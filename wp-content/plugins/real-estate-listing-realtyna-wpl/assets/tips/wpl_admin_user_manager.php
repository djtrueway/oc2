<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();
$index = 1;

$content = '<h3>'.__('Manage WPL Agents', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Here you see all WPL agents. You can remove the agents or add new agents to WPL using this menu. Also you're able to manage the agent accesse by editing each agent.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('User Data Structure', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("You can manage user profile fields here. You can manage existing fields or add new fields simply using WPL Flex system.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.setting-toolbar-btn', 'content'=>$content, 'position'=>array('edge'=>'right', 'align'=>'right'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Search Users', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Use this form to perform a search on WPL agents.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'#wpl_users_search_form', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'middle'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Manage User Accesses', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Click edit icon of each user to change the accesses of the user.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'table.widefat.page tr:nth-child(1) td.wpl_manager_td .icon-edit', 'content'=>$content, 'position'=>array('edge'=>'right', 'align'=>'middle'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Remove Agent', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("For removing agents from WPL, you can use this icon. This will remove the users only from WPL.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'table.widefat.page tr:nth-child(1) td.wpl_manager_td .icon-recycle', 'content'=>$content, 'position'=>array('edge'=>'right', 'align'=>'middle'), 'buttons'=>array(2=>array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page=wpl_admin_profile&wpltour=1";'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

return $tips;