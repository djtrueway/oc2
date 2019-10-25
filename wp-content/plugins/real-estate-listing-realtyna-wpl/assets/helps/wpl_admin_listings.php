<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** Define Tabs **/
$tabs = array();
$tabs['tabs'] = array();

$content = '<h3>'.__('Listing Manager', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Here you can see and manage your listings. You can add a new listing, and modify and disable existing listings.", 'real-estate-listing-realtyna-wpl').'</p>';
$tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_int', 'content'=>$content, 'title'=>__('Introduction', 'real-estate-listing-realtyna-wpl'));

if(wpl_users::is_administrator())
{
    $articles  = '';
    $articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/546/" target="_blank">'.__("How do I change the listing user/agent?", 'real-estate-listing-realtyna-wpl').'</a></li>';
    $articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/753/" target="_blank">'.__("How to purge all Unfinalized listings?", 'real-estate-listing-realtyna-wpl').'</a></li>';
    $articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/727/" target="_blank">'.__("How to clone a listing?", 'real-estate-listing-realtyna-wpl').'</a></li>';
    $articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/701/" target="_blank">'.__("After installing WPL, there is an error message: \"You don't have access to this part!\" in the WPL menu.", 'real-estate-listing-realtyna-wpl').'</a></li>';
    $articles .= '<li><a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/615/" target="_blank">'.__("My added listings are not showing in listing pages in frontend.", 'real-estate-listing-realtyna-wpl').'</a></li>';

    $content = '<h3>'.__('Related KB Articles', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('Here you will find KB articles with information related to this page. You can come back to this section to find an answer to any questions that may come up.', 'real-estate-listing-realtyna-wpl').'</p><p><ul>'.$articles.'</ul></p>';
    $tabs['tabs'][] = array('id'=>'wpl_contextual_help_tab_kb', 'content'=>$content, 'title'=>__('KB Articles', 'real-estate-listing-realtyna-wpl'));
}

return $tabs;