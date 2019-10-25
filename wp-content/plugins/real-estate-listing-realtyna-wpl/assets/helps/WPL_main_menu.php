<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** Define Tabs **/
$tabs = array();
$tabs['tabs'] = array();

$content = '<h3>'.__('WPL Dashboard', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Welcome to WPL dashboard. Here, you will see information about WPL and its add-ons, WPL manuals, KB articles and some statistics about your website. You can update WPL PRO and its add-ons from this menu too.', 'real-estate-listing-realtyna-wpl').'</p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_int', 'content'=>$content, 'title'=>__('Introduction', 'real-estate-listing-realtyna-wpl'));

$content = '<h3>'.__('Documentation', 'real-estate-listing-realtyna-wpl').'</h3><p><ul><li><a href="http://wpl.realtyna.com/wassets/wpl-manual.pdf" target="_blank">'.__('WPL Manual', 'real-estate-listing-realtyna-wpl').'</a></li><li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/List/Index/28/wpl---wordpress-property-listing" target="_blank">'.__('WPL KB articles', 'real-estate-listing-realtyna-wpl').'</a></li></ul></p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_doc', 'content'=>$content, 'title'=>__('Documentation', 'real-estate-listing-realtyna-wpl'));

$articles  = '';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Knowledgebase/Article/View/557/28/how-to-update-wpl-pro" target="_blank">'.__('How do I update WPL PRO?', 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/561/" target="_blank">'.__('How do you upgrade WPL basic to WPL PRO?', 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/703/" target="_blank">'.__('How do I download my purchased products?', 'real-estate-listing-realtyna-wpl').'</a></li>';

$content = '<h3>'.__('Related KB Articles', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Here you will find KB articles with information related to this page. You can come back to this section to find an answer to any questions that may come up.', 'real-estate-listing-realtyna-wpl').'</p><p><ul>'.$articles.'</ul></p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_kb', 'content'=>$content, 'title'=>__('KB Articles', 'real-estate-listing-realtyna-wpl'));

return $tabs;