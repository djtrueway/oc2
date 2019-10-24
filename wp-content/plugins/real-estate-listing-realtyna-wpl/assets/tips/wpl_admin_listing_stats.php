<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();
$index = 1;

$content = '<h3>'.__('Listing Stats', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("You can find some stats about listings here.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array());

return $tips;