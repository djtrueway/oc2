<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.notifications.notifications');

class wpl_notifications_controller extends wpl_controller
{
    public $tpl_path = 'views.backend.notifications.tmpl';

    public function display()
    {
        /** check permission **/
        wpl_global::min_access('administrator');
        $function = wpl_request::getVar('wpl_function');
        
        // Check Nonce
        if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_notifications')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
        if($function == 'set_enabled_notification') $this->set_enabled_notification();
        elseif($function == 'save_notification') $this->save_notification();
    }

    private function set_enabled_notification()
    {
        $notification_id = wpl_request::getVar('notification_id');
        $enabled_field = wpl_request::getVar('enabled_field', 'enabled');
        $enabled_status = wpl_request::getVar('enabled_status');

        $res = wpl_notifications::set($notification_id, $enabled_field, $enabled_status);
        $message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
        
        $response = array('success'=>$res, 'message'=>$message);
        echo json_encode($response);
        exit;
    }

    private function save_notification()
    {
        $info = wpl_request::getVar('info');
        wpl_notifications::save_notification($info);
        
        $message = __('Operation was successful.', 'real-estate-listing-realtyna-wpl');
        
        $response = array('success'=>1, 'message'=>$message);
        echo json_encode($response);
        exit;
    }
}