<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();
$index = 1;

$content = '<h3>'.__('Manage Existing Listings', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Here you can find all existing listings to manage. You can edit them, remove them or unpublish them.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Different Kinds', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Switch to different kinds of listings.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'#wpl_listings_top_tabs_container', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Search Listings', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Do you have many listings? You can filter them using this form simply.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wpl_listing_manager_search_form_element_cnt .wpl-button', 'content'=>$content, 'position'=>array('edge'=>'left', 'align'=>'middle'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Confirm / Unconfirm', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("You can unconfirm the listings using this button if needed. This way the listing don't appear on search results untill it confirmed again.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.properties-wp .propery-wp:first .p-actions-wp .p-action-btn:first', 'content'=>$content, 'position'=>array('edge'=>'bottom', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Purge Listing', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("If you need to remove a listing completely. You can use purge button.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.properties-wp .propery-wp:first .p-actions-wp .p-action-btn:nth-child(4)', 'content'=>$content, 'position'=>array('edge'=>'right', 'align'=>'middle'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Edit Listing', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("For modifying the listing click this button.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.properties-wp .propery-wp:first .p-actions-wp .p-action-btn:nth-child('.(wpl_global::check_addon('pro') ? '6' : '5').')', 'content'=>$content, 'position'=>array('edge'=>'right', 'align'=>'middle'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$next_page = NULL;
if(wpl_global::check_addon('pro')) $next_page = 'wpl_admin_listing_stats';

$content = '<h3>'.__('Property Details Page', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Open the property details page in the frontend of website.", 'real-estate-listing-realtyna-wpl').'</p>';

$buttons = array();
if($next_page) $buttons[2] = array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page='.$next_page.'&wpltour=1";');
$buttons[3] = array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'));

$tips[] = array('id'=>$index++, 'selector'=>'.properties-wp .propery-wp:first .property-image .p-links', 'content'=>$content, 'position'=>array('edge'=>'left', 'align'=>'middle'), 'buttons'=>$buttons);

return $tips;