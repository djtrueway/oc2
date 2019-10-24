<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** define tips **/
$tips = array();
$index = 1;

$content = '<h3>'.__('WPL Payments', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Here you can find all payment configurations and transactions.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'.wrap.wpl-wp h2:first', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Payment gateways', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Enable your desired payment gateways and set the credentials here.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'#wpl_payments_options_tab', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next', 'real-estate-listing-realtyna-wpl')), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

$content = '<h3>'.__('Payment Transactions', 'real-estate-listing-realtyna-wpl').'</h3><p>'.__("Also you can see all transactions here.", 'real-estate-listing-realtyna-wpl').'</p>';
$tips[] = array('id'=>$index++, 'selector'=>'#wpl_payments_transactions_tab', 'content'=>$content, 'position'=>array('edge'=>'top', 'align'=>'left'), 'buttons'=>array(2=>array('label'=>__('Next Menu', 'real-estate-listing-realtyna-wpl'), 'code'=>'window.location.href = "admin.php?page=wpl_admin_user_manager&wpltour=1";'), 3=>array('label'=>__('Previous', 'real-estate-listing-realtyna-wpl'))));

return $tips;