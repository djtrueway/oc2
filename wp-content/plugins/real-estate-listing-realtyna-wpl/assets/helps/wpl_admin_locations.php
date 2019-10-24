<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** Define Tabs **/
$tabs = array();
$tabs['tabs'] = array();

$content = '<h3>'.__('WPL Locations', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Here you can manage countries and customize regions (i.e. states and provinces) in WPL.", 'real-estate-listing-realtyna-wpl').'</p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_int', 'content'=>$content, 'title'=>__('Introduction', 'real-estate-listing-realtyna-wpl'));

$articles  = '';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/527/" target="_blank">'.__("How do I use the Location Manager in WPL?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/586/" target="_blank">'.__("How do I enable new countries in WPL?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/562/" target="_blank">'.__("How do I add/remove a location level in WPL?", 'real-estate-listing-realtyna-wpl').'</a></li>';

$content = '<h3>'.__('Related KB Articles', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Here you will find KB articles with information related to this page. You can come back to this section to find an answer to any questions that may come up.', 'real-estate-listing-realtyna-wpl').'</p><p><ul>'.$articles.'</ul></p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_kb', 'content'=>$content, 'title'=>__('KB Articles', 'real-estate-listing-realtyna-wpl'));

return $tabs;