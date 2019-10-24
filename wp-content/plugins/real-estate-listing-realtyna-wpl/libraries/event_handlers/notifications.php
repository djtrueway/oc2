<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.notifications.notifications');

/**
 * WPL notifications
 * @author Howard <howard@realtyna.com>
 */
class wpl_events_notifications
{
    /**
     * Listing Contact activity. It's for contacting to a listing agent.
     * @author Howard <howard@realtyna.com>
     * @updated by Alfred <Alfred@realtyna.com>
     * @static
     * @param array $params
     * @return boolean
     */
    public static function contact_agent($params)
    {
        $replacements = $params[0];
        $notification_data = wpl_notifications::get_notification(2);

        /** Email notification is enabled **/
        if($notification_data['enabled'])
        {
            // Make the message multiline
            $replacements['message'] = nl2br($replacements['message']);
            
            $notification = new wpl_notifications('email');
            $notification->prepare(2, $replacements);
        
            $property_id = $params[0]['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);
            
            $user = wpl_users::get_user($property['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $recipients = array();
            $recipients[] = $user->data->user_email;

            // Add additional agents to the recipients of email
            if(wpl_global::check_addon('multi_agents'))
            {
                _wpl_import('libraries.addon_multi_agents');
        
                $multi = new wpl_addon_multi_agents($property_id);
                $additional_agents = $multi->get_agents();
                
                foreach($additional_agents as $additional_agent) $recipients[] = $additional_agent;
            }

            // Apply Filters
            @extract(wpl_filters::apply('wpl_email_notification_recipients_contact_agent', array('recipients'=>$recipients, 'property_id'=>$property_id)));

            $property_title = wpl_property::update_property_title($property);
            $replacements['listing_id'] = '<a href="'.wpl_property::get_property_link(NULL, $property_id).'">'.$property['mls_id'].' ('.$property_title.')</a>';
            $replacements['location'] = $property['location_text'];
            
            $notification->set_replyto($replacements['fullname'], $replacements['email']);
            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients($recipients);
            $notification->send();
        }
        
        /** SMS notification is enabled **/
        if(wpl_global::check_addon('sms') and $notification_data['sms_enabled'])
        {
            $notification = new wpl_notifications('sms');
            $notification->prepare(2, $replacements);
            
            $property_id = $params[0]['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);

            $user = wpl_users::get_user($params[0]['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $user = $notification->sms->wpl_get_user_data('wpl.`mobile`', "AND wpl.`id`='".$user->data->ID."'", 'loadObject');

            $recipients = array();
            $recipients[] = $user->mobile;
            
            // Add additional agents to the recipients of email
            if(wpl_global::check_addon('multi_agents'))
            {
                _wpl_import('libraries.addon_multi_agents');
        
                $multi = new wpl_addon_multi_agents($property_id);
                $additional_agents = $multi->get_agents();

                foreach($additional_agents as $additional_agent) $recipients[] = $additional_agent;
            }

            // Apply Filters
            @extract(wpl_filters::apply('wpl_sms_notification_recipients_contact_agent', array('recipients'=>$recipients, 'property_id'=>$property_id)));
            
            $property_title = wpl_property::update_property_title($property);
            $replacements['listing_id'] = '<a href="'.wpl_property::get_property_link(NULL, $property_id).'">'.$property['mls_id'].' ('.$property_title.')</a>';

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients($recipients);
            $notification->send();
        }

        return true;
    }
    
    /**
     * User Contact activity. It's for contacting to user directly from profile show page
     * @author Howard <howard@realtyna.com>
     * @updated by Alfred Alfred@realtyna.com
     * @static
     * @param type $params
     * @return boolean
     */
    public static function contact_profile($params)
    {
        $replacements = $params[0];
        $notification_data = wpl_notifications::get_notification(3);

        /** Email notification is enabled **/
        if($notification_data['enabled'])
        {
            // Make the message multiline
            $replacements['message'] = nl2br($replacements['message']);
            
            $notification = new wpl_notifications('email');
            $notification->prepare(3, $replacements);

            $user = wpl_users::get_user($params[0]['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $notification->set_replyto($replacements['fullname'], $replacements['email']);
            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->data->user_email));
            $notification->send();
        }
        
        /**
         * SMS notification is enabled
         * Updated by Alfred  Alfred@realtyna.com
         **/
        if(wpl_global::check_addon('sms') and $notification_data['sms_enabled'])
        {
            $notification = new wpl_notifications('sms');
            $notification->prepare(3, $replacements);

            $user = wpl_users::get_user($params[0]['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $user = $notification->sms->wpl_get_user_data('wpl.`mobile`', "AND wpl.`id`='".$user->data->ID."'", 'loadObject');

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->mobile));
            $notification->send();
        }
        
        return true;
    }
    
    /**
     * Sends welcome email to user after registeration
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $params
     * @return boolean
     */
    public static function user_registered($params)
    {
        $replacements = $params[0];
        $notification_data = wpl_notifications::get_notification(5);

        /** Email notification is enabled **/
        if($notification_data['enabled'])
        {
            $notification = new wpl_notifications('email');
            $notification->prepare(5, $replacements);

            $user = wpl_users::get_user($params[0]['user_id']);
            $replacements['name'] = isset($user->data->wpl_data) ? $user->data->wpl_data->first_name : $user->data->display_name;
            $replacements['password'] = $params[0]['password'];
            $replacements['username'] = $user->data->user_login;

            $link = wpl_global::get_wp_site_url();
            $replacements['site_address'] = '<a target="_blank" href="'.$link.'">'.$link.'</a>';

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->data->user_email));
            $notification->send();
        }
        
        /** SMS notification is enabled **/
        if(wpl_global::check_addon('sms') and $notification_data['sms_enabled'])
        {
            $notification = new wpl_notifications('sms');
            $notification->prepare(5, $replacements);
           
            $user = $notification->sms->wpl_get_user_data('*', "AND wpl.`id`='".$params[0]['user_id']."'", 'loadObject');

            $replacements['name'] = isset($user->wpl_data) ? $user->wpl_data->first_name : $user->display_name;
            $replacements['password'] = $params[0]['password'];
            $replacements['username'] = $user->user_login;

            $link = wpl_global::get_wp_site_url();
            $replacements['site_address'] = '<a target="_blank" href="'.$link.'">'.$link.'</a>';

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->mobile));
            $notification->send();
        }
        
        return true;
    }
    
    public static function send_to_friend($params)
    {
        $replacements = $params[0];
        $notification_data = wpl_notifications::get_notification(6);

        /** Email notification is enabled **/
        if($notification_data['enabled'])
        {
            $notification = new wpl_notifications('email');
            $notification->prepare(6, $replacements);

            $property_id = $replacements['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);

            $property_title = wpl_property::update_property_title($property);
            $replacements['listing_id'] = '<a href="'.wpl_property::get_property_link(NULL, $property_id).'">'.$property['mls_id'] .' ('.$property_title.')</a>';
            
            $labels = array('your_name'=>__("Your friend's name", 'real-estate-listing-realtyna-wpl'), 'your_email'=>__("Your friend's email", 'real-estate-listing-realtyna-wpl'), 'email_subject'=>__("Subject", 'real-estate-listing-realtyna-wpl'), 'message'=>__("Message", 'real-estate-listing-realtyna-wpl'));
            
            $details = '';
            foreach($replacements as $key=>$value)
            {
                if(in_array($key, array('property_id', 'listing_id', 'friends_email')) or trim($value) == '' or !isset($labels[$key])) continue;
                $details .= '<strong>'.$labels[$key].': </strong><span>'.$value.'</span><br />';
            }

            $replacements['details'] = $details;

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($replacements['friends_email'], wpl_global::get_admin_id()));
            $notification->send();
        }
        
        /** SMS notification is enabled **/
        if(wpl_global::check_addon('sms') and $notification_data['sms_enabled'])
        {
            $notification = new wpl_notifications('sms');
            $notification->prepare(6, $replacements);
           
            $property_id = $replacements['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);

            $property_title = wpl_property::update_property_title($property);
            $replacements['listing_id'] = '<a href="'.wpl_property::get_property_link(NULL, $property_id).'">'.$property['mls_id'] .' ('.$property_title.')</a>';
            
            $labels = array('your_name'=>__("Your friend's name", 'real-estate-listing-realtyna-wpl'), 'your_email'=>__("Your friend's email", 'real-estate-listing-realtyna-wpl'), 'email_subject'=>__("Subject", 'real-estate-listing-realtyna-wpl'), 'message'=>__("Message", 'real-estate-listing-realtyna-wpl'));
            
            $details = '';
            foreach($replacements as $key=>$value)
            {
                if(in_array($key, array('property_id', 'listing_id', 'friends_email')) or trim($value) == '' or !isset($labels[$key])) continue;
                $details .= '<strong>'.$labels[$key].': </strong><span>'.$value.'</span><br />';
            }

            $replacements['details'] = $details;

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($replacements['friends_mobile'], wpl_global::get_admin_id()));
            $notification->send();
        }

        return true;
    }

    public static function request_a_visit($params)
    {
        $replacements = $params[0];
        $notification_data = wpl_notifications::get_notification(7);

        /** Email notification is enabled **/
        if($notification_data['enabled'])
        {
            $notification = new wpl_notifications('email');
            $notification->prepare(7, $replacements);

            $property_id = $replacements['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);
            $user = wpl_users::get_user($property['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $property_title = wpl_property::update_property_title($property);
            $replacements['listing_id'] = '<a href="'.wpl_property::get_property_link(NULL, $property_id).'">'.$property['mls_id'] .' ('.$property_title.')</a>';

            $details = '';
            foreach($replacements as $key=>$value)
            {
                if(in_array($key, array('property_id', 'listing_id')) or trim($value) == '') continue;
                $details .= '<strong>'.__($key, 'real-estate-listing-realtyna-wpl').': </strong><span>'.$value.'</span><br />';
            }

            $replacements['details'] = $details;

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->data->user_email, wpl_global::get_admin_id()));

            $notification->send();
        }
        
        /** SMS notification is enabled **/
        if(wpl_global::check_addon('sms') and $notification_data['sms_enabled'])
        {
            $notification = new wpl_notifications('sms');
            $notification->prepare(7, $replacements);
            
            $property_id = $replacements['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);
            
            $user = wpl_users::get_user($property['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $user = $notification->sms->wpl_get_user_data('*', "AND wpl.`id`='".$user->data->ID."'", 'loadObject');

            $property_title = wpl_property::update_property_title($property);
            $replacements['listing_id'] = '<a href="'.wpl_property::get_property_link(NULL, $property_id).'">'.$property['mls_id'] .' ('.$property_title.')</a>';

            $details = '';
            foreach($replacements as $key=>$value)
            {
                if(in_array($key, array('property_id', 'listing_id')) or trim($value) == '') continue;
                $details .= '<strong>'.__($key, 'real-estate-listing-realtyna-wpl').': </strong><span>'.$value.'</span><br />';
            }

            $replacements['details'] = $details;

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->mobile));
            $notification->send();
        }

        return true;
    }

    /**
     * Sends email when new listing added
     * @author Edward <edward@realtyna.com>
     * @static
     * @param $params
     * @return boolean
     */
    public static function listing_create($params)
    {
        $property_id = $params[0];
        $notification_data = wpl_notifications::get_notification(8);

        /** Email notification is enabled **/
        if($notification_data['enabled'])
        {
            $notification = new wpl_notifications('email');
            $notification->prepare(8);

            $property = wpl_property::get_property_raw_data($property_id);
            $user = wpl_users::get_user($property['user_id']);

            $replacements['username'] = $user->data->user_login;
            $replacements['listing_edit_url'] = wpl_global::get_wpl_admin_menu('wpl_admin_add_listing') . '&pid=' . $property_id;
            $replacements['listing_view_url'] = wpl_property::get_property_link('', $property_id);

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array(wpl_global::get_admin_id()));

            $notification->send();
        }
        
        /** SMS notification is enabled **/
        if(wpl_global::check_addon('sms') and $notification_data['sms_enabled'])
        {
            $notification = new wpl_notifications('sms');
            $notification->prepare(8, $replacements);
   
            $property = wpl_property::get_property_raw_data($property_id);
            $user = $notification->sms->wpl_get_user_data('*', "AND wpl.`id`='".$property['user_id']."'", 'loadObject');

            $replacements['username'] = $user->user_login;
            $replacements['listing_edit_url'] = wpl_global::get_wpl_admin_menu('wpl_admin_add_listing') . '&pid=' . $property_id;
            $replacements['listing_view_url'] = wpl_property::get_property_link('', $property_id);

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array(wpl_global::get_admin_id()));
            $notification->send();
        }

        return true;
    }

    /**
     * Schedule tour notification
     * @author Steve A. <steve@realtyna.com>
     * @param  array    $params
     * @return boolean
     */
    public static function schedule_tour($params)
    {
        $replacements = $params[0];
        $notification_data = wpl_notifications::get_notification(9);

        /** Email notification is enabled **/
        if($notification_data['enabled'])
        {
            $notification = new wpl_notifications('email');
            $notification->prepare(9, $replacements);

            $property_id = $replacements['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);
            $user = wpl_users::get_user($property['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $replacements['listing_id'] = '<a href="'.wpl_property::get_property_link(NULL, $property_id).'">'.$property['mls_id'].' ('.$property['location_text'].')</a>';

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->data->user_email, wpl_global::get_admin_id()));

            $notification->send();
        }
        
        /** SMS notification is enabled **/
        if(wpl_global::check_addon('sms') and $notification_data['sms_enabled'])
        {
            $notification = new wpl_notifications('sms');
            $notification->prepare(9, $replacements);
            
            $property_id = $replacements['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);
            
            $user = wpl_users::get_user($property['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $user = $notification->sms->wpl_get_user_data('*', "AND wpl.`id`='".$user->data->ID."'", 'loadObject');
            $replacements['listing_id'] = $property['mls_id'].' ('.$property['location_text'].')';

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->mobile));
            $notification->send();
        }

        return true;
    }

    /**
     * Sending an email to Agent for adding price request
     *
     * @return bool
     */
    public static function adding_price_request($params)
    {
        $replacements = $params[0];
        $notification_data = wpl_notifications::get_notification(10);

        /** Email notification is enabled **/
        if($notification_data['enabled'])
        {
            $notification = new wpl_notifications('email');
            $notification->prepare(10, $replacements);

            $property_id = $replacements['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);
            $user = wpl_users::get_user($property['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $property_title = wpl_property::update_property_title($property);
            $replacements['listing_id'] = '<a href="'.wpl_property::get_property_link(NULL, $property_id).'">'.$property['mls_id'] .' ('.$property_title.')</a>';

            $details = '';
            foreach($replacements as $key=>$value)
            {
                if(in_array($key, array('property_id', 'listing_id')) or trim($value) == '') continue;
                $details .= '<strong>'.__($key, 'real-estate-listing-realtyna-wpl').': </strong><span>'.$value.'</span><br />';
            }

            $replacements['details'] = $details;

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->data->user_email, wpl_global::get_admin_id()));

            $notification->send();
        }

        /** SMS notification is enabled **/
        if(wpl_global::check_addon('sms') and $notification_data['sms_enabled'])
        {
            $notification = new wpl_notifications('sms');
            $notification->prepare(10, $replacements);

            $property_id = $replacements['property_id'];
            $property = wpl_property::get_property_raw_data($property_id);

            $user = wpl_users::get_user($property['user_id']);
            if(wpl_global::check_addon('membership') && $user->wpl_data->maccess_direct_contact == 0)
                $user = wpl_users::get_user($user->wpl_data->maccess_direct_contact_user_id);

            $user = $notification->sms->wpl_get_user_data('*', "AND wpl.`id`='".$user->data->ID."'", 'loadObject');

            $property_title = wpl_property::update_property_title($property);
            $replacements['listing_id'] = $property['mls_id'] .' ('.$property_title.')';

            $details = '';
            foreach($replacements as $key=>$value)
            {
                if(in_array($key, array('property_id', 'listing_id')) or trim($value) == '') continue;
                $details .= '<strong>'.__($key, 'real-estate-listing-realtyna-wpl').': </strong><span>'.$value.'</span><br />';
            }

            $replacements['details'] = $details;

            $notification->replacements = $notification->set_replacements($replacements);
            $notification->rendered_content = $notification->render_notification_content();
            $notification->recipients = $notification->set_recipients(array($user->mobile));
            $notification->send();
        }

        return true;
    }
}