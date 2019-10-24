<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** Define Tabs **/
$tabs = array();
$tabs['tabs'] = array();

$content = '<h3>'.__('WPL Settings', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("With this menu, you can change most your website settings. If there is a setting you're unsure about, please check the KB Articles for more information.", 'real-estate-listing-realtyna-wpl').'</p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_int', 'content'=>$content, 'title'=>__('Introduction', 'real-estate-listing-realtyna-wpl'));

$articles  = '';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/558/" target="_blank">'.__("What is the difference between the image resize methods?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/582/" target="_blank">'.__("How do I use the WPL Geo Meta Tag feature?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/643/" target="_blank">'.__("How do I enable the WPL RSS feed feature?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/574/" target="_blank">'.__("How do I adjust the WPL address pattern?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/593/" target="_blank">'.__("How do I enable the WPL Watermark feature?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/708/" target="_blank">'.__("How to change main color of the WPL frontend?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/573/" target="_blank">'.__("What is the user auto add feature in WPL Membership settings?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/699/" target="_blank">'.__("Why aren't my thumbnail photos changing in my listings?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/705/" target="_blank">'.__("How do I add a print option on a listing page?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/568/" target="_blank">'.__("How do I change the WPL date format?", 'real-estate-listing-realtyna-wpl').'</a></li>';
$articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/776/" target="_blank">'.__("How to enable new WPL Cronjob system?", 'real-estate-listing-realtyna-wpl').'</a></li>';

$content = '<h3>'.__('Related KB Articles', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Here you will find KB articles with information related to this page. You can come back to this section to find an answer to any questions that may come up.', 'real-estate-listing-realtyna-wpl').'</p><p><ul>'.$articles.'</ul></p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_kb', 'content'=>$content, 'title'=>__('KB Articles', 'real-estate-listing-realtyna-wpl'));

return $tabs;