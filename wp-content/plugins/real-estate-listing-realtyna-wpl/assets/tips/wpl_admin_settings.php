<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();
$index = 1;

$content = '<h3>'.__('WPL Settings', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Almost all of WPL options included in this menu so you can configure all WPL and its addons in one menu.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Categories', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Use the categories to navigate between different sections.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'#wpl_slide_label_id1', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'), 'code'=>'jQuery("#wpl_slide_label_id5").trigger("click");'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Notifications Options', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__('For example you can change sender email and sender name of WPL emails here!', 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'#wpl_st_67', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

if(wpl_global::check_addon('pro'))
{
    $content = '<h3>'.__('UI Customizer', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Also you can customize frontend user interface of WPL here.", 'real-estate-listing-realtyna-wpl').'</p>';
    $tips[] = array('id'=>$index++, 'selector'=>'#wpl_slide_label_id11', 'content'=>$content, 'position'=>array('edge'=>'left', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'), 'code'=>'jQuery("#wpl_slide_label_id5").trigger("click");')));
}

$content = '<h3>'.__('Maintenance', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("You can clear the WPL cache data here! You may need to do it sometimes after changing some settings.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wpl-maintenance-container', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Server Requirements', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Please make sure that your server meets the requirements first.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wpl-requirements-container', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page=wpl_admin_activity&wpltour=1";'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

return $tips;