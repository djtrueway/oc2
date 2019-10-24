<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** Define Tabs **/
$tabs = array();
$tabs['tabs'] = array();

$content = '<h3>'.__('WPL Activity Manager', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('WPL is a modular system that runs certain activities to generate page outputs. You can change options on this page for the gallery, Google Maps, and other activities. ', 'real-estate-listing-realtyna-wpl').'</p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_int', 'content'=>$content, 'title'=>__('Introduction', 'real-estate-listing-realtyna-wpl'));

$articles  = '';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/651/" target="_blank">'.__("How do you modify and change settings for items in the WPL Activity Manager?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/611/" target="_blank">'.__("How do I link images using the mailto option?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/584/" target="_blank">'.__("How do I disable Google Maps in the listing pages?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/594/" target="_blank">'.__("How to enable the Mortgage Calculator feature?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/598/" target="_blank">'.__("How do I enable WPL contact forms/activities?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/688/" target="_blank">'.__("How do I make the Walk Score responsive?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/547/" target="_blank">'.__("Enabling/Disabling/Sorting WPL Activities.", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/567/" target="_blank">'.__("How do I manage social media icons on the WPL front-end?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/571/" target="_blank">'.__("How do I enable the Google Places feature?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/762/" target="_blank">'.__("Widget areas and activity positions in the WPL.", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/758/" target="_blank">'.__("How to show/hide property tags?", 'real-estate-listing-realtyna-wpl').'</a></li>';

$content = '<h3>'.__('Related KB Articles', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Here you will find KB articles with information related to this page. You can come back to this section to find an answer to any questions that may come up.', 'real-estate-listing-realtyna-wpl').'</p><p><ul>'.$articles.'</ul></p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_kb', 'content'=>$content, 'title'=>__('KB Articles', 'real-estate-listing-realtyna-wpl'));

return $tabs;