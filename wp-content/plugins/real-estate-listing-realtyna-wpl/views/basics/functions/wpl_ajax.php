<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.flex');
_wpl_import('libraries.property');
_wpl_import('libraries.images');
		
class wpl_functions_controller extends wpl_controller
{
	public $tpl_path = 'views.basics.functions.tmpl';
	public $tpl;
	public $property_id;
	public $wpl_properties;
	public $settings;
	public $form_id;
	
	public function display()
	{
		$function = wpl_request::getVar('wpl_function');
		
		if($function == 'infowindow') $this->infowindow();
		elseif($function == 'shortcode_wizard') $this->shortcode_wizard();
        elseif($function == 'report_abuse_form') $this->report_abuse_form();
        elseif($function == 'report_abuse_submit') $this->report_abuse_submit();
        elseif($function == 'send_to_friend_form') $this->send_to_friend_form();
        elseif($function == 'send_to_friend_submit') $this->send_to_friend_submit();
        elseif($function == 'request_a_visit_form') $this->request_a_visit_form();
        elseif($function == 'request_a_visit_submit') $this->request_a_visit_submit();
        elseif($function == 'adding_price_request') $this->adding_price_request();
        elseif($function == 'adding_price_request_submit') $this->adding_price_request_submit();
        elseif($function == 'watch_changes_form') $this->watch_changes_form();
        elseif($function == 'watch_changes_submit') $this->watch_changes_submit();
	}
	
	private function infowindow()
	{
        $wpl_property = new wpl_property();

        $property_ids = wpl_request::getVar('property_ids', '');
        $ex_pids = explode(',', $property_ids);
        $kind = wpl_property::get_property_kind($ex_pids[0]);

        $plisting_fields = $wpl_property->get_plisting_fields('', $kind);
        $select = $wpl_property->generate_select($plisting_fields, 'p');

		$query = "SELECT ".$select." FROM `#__wpl_properties` AS p WHERE 1 AND p.`deleted`='0' AND p.`finalized`='1' AND p.`confirmed`='1' AND p.`expired`='0' AND p.`id` IN (".$property_ids.")";
		$properties = $wpl_property->search($query);
		
        // We have to disable the cache if some of units changed by unit switcher feature or something else
        $force = false;
        $cookies = wpl_request::get('COOKIE');
        if(isset($cookies['wpl_unit1']) or isset($cookies['wpl_unit2']) or isset($cookies['wpl_unit3']) or isset($cookies['wpl_unit4'])) $force = true;
		
		$wpl_properties = array();
		foreach($properties as $property)
		{
			$wpl_properties[$property->id] = $wpl_property->full_render($property->id, $plisting_fields, $property, array(), $force);
		}

        // Apply Filters
		@extract(wpl_filters::apply('property_listing_after_render', array('wpl_properties'=>$wpl_properties)));
        
        $this->wpl_properties = $wpl_properties;

        $tpl = wpl_request::getVar('tpl', 'infowindow');
		parent::render($this->tpl_path, $tpl);
		exit;
	}
	
	private function shortcode_wizard()
	{
		_wpl_import('libraries.sort_options');
		
		/** global settings **/
		$this->settings = wpl_global::get_settings();
		
		parent::render($this->tpl_path, 'shortcode_wizard');
	}
    
    private function report_abuse_form()
	{
		$this->property_id = wpl_request::getVar('pid', 0);
        $this->form_id = wpl_request::getVar('form_id', 0);

        $HTML = '';
		if(!$this->form_id) $HTML = parent::render($this->tpl_path, 'report_abuse_form', false, true);
        
        echo $HTML;
        exit;
	}
    
    private function report_abuse_submit()
	{
        // Check Nonce
		if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_report_abuse_form')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
        $parameters = wpl_request::getVar('wplfdata', array());
        $property_id = isset($parameters['property_id']) ? $parameters['property_id'] : 0;
        $gre = wpl_request::getVar('g-recaptcha-response');

        // check recaptcha 
        $gre_response = wpl_global::verify_google_recaptcha($gre, 'gre_report_listing');

        $returnData = array();
        if(!$property_id)
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Invalid Property!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['email']) and !filter_var($parameters['email'], FILTER_VALIDATE_EMAIL))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your email is not valid!', 'real-estate-listing-realtyna-wpl');
        }        
        elseif($gre_response['success'] === 0)
        {
            $returnData['success'] = 0;
            $returnData['message'] = $gre_response['message'];
        }
        elseif(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_report_abuse_form'))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        else
        {
            $PRO = new wpl_addon_pro();
            if($PRO->report_abuse_send($parameters))
            {
                $returnData['success'] = 1;
                $returnData['message'] = __('Report sent successfully.', 'real-estate-listing-realtyna-wpl');
            }
            else
            {
                $returnData['success'] = 0;
                $returnData['message'] = __('Error sending!', 'real-estate-listing-realtyna-wpl');
            }
        }
        
        echo json_encode($returnData);
        exit;
	}

    private function send_to_friend_form()
    {
        $this->property_id = wpl_request::getVar('pid', 0);
        $this->form_id = wpl_request::getVar('form_id', 0);

        $HTML = '';
        if(!$this->form_id) $HTML = parent::render($this->tpl_path, 'send_to_friend_form', false, true);

        echo $HTML;
        exit;
    }

    private function send_to_friend_submit()
    {
        // Check Nonce
		if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_send_to_friend_form')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
        $parameters = wpl_request::getVar('wplfdata', array());
        $property_id = isset($parameters['property_id']) ? $parameters['property_id'] : 0;
        $gre = wpl_request::getVar('g-recaptcha-response');

        // check recaptcha 
        $gre_response = wpl_global::verify_google_recaptcha($gre, 'gre_send_to_friend');

        $returnData = array();
        if(!$property_id)
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Invalid Property!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['your_email']) == false || !filter_var($parameters['your_email'], FILTER_VALIDATE_EMAIL))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your email is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['friends_email']) == false || !filter_var($parameters['friends_email'], FILTER_VALIDATE_EMAIL))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Friends email is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['email_subject']) == false || $parameters['email_subject'] == '')
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Email subject  is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['your_name']) == false || $parameters['your_name'] == '')
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your name is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif($gre_response['success'] === 0)
        {
            $returnData['success'] = 0;
            $returnData['message'] = $gre_response['message'];
        }
        elseif(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_send_to_friend_form'))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        else
        {
            if(wpl_global::send_to_friend($parameters))
            {
                $returnData['success'] = 1;
                $returnData['message'] = __('Send to friend message sent successfully.', 'real-estate-listing-realtyna-wpl');
            }
            else
            {
                $returnData['success'] = 0;
                $returnData['message'] = __('Error sending!', 'real-estate-listing-realtyna-wpl');
            }
        }

        echo json_encode($returnData);
        exit;
    }

    private function request_a_visit_form()
    {
        $this->property_id = wpl_request::getVar('pid', 0);
        $this->form_id = wpl_request::getVar('form_id', 0);

        $HTML = '';
        if(!$this->form_id) $HTML = parent::render($this->tpl_path, 'request_a_visit_form', false, true);

        echo $HTML;
        exit;
    }

    private function request_a_visit_submit()
    {
        // Check Nonce
		if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_request_a_visit_form')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
        $parameters = wpl_request::getVar('wplfdata', array());
        $property_id = isset($parameters['property_id']) ? $parameters['property_id'] : 0;
        $gre = wpl_request::getVar('g-recaptcha-response');

        // check recaptcha 
        $gre_response = wpl_global::verify_google_recaptcha($gre, 'gre_request_visit');
        $returnData = array();
        if(!$property_id)
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Invalid Property!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['email']) == false || !filter_var($parameters['email'], FILTER_VALIDATE_EMAIL))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your email is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['name']) == false || $parameters['name'] == '')
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your name is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['tel']) == false || $parameters['tel'] == '')
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Contact phone number is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif($gre_response['success'] === 0)
        {
            $returnData['success'] = 0;
            $returnData['message'] = $gre_response['message'];
        }
        elseif(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_request_a_visit_form'))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        else
        {
            if(wpl_global::request_a_visit_send($parameters))
            {
                $returnData['success'] = 1;
                $returnData['message'] = __('Request a visit sent successfully.', 'real-estate-listing-realtyna-wpl');
            }
            else
            {
                $returnData['success'] = 0;
                $returnData['message'] = __('Error sending!', 'real-estate-listing-realtyna-wpl');
            }
        }

        echo json_encode($returnData);
        exit;
    }

    private function adding_price_request()
    {
        $this->property_id = wpl_request::getVar('pid', 0);
        $this->form_id = wpl_request::getVar('form_id', 0);

        $HTML = '';
        if(!$this->form_id) $HTML = parent::render($this->tpl_path, 'adding_price_request_form', false, true);

        echo $HTML;
        exit;
    }

    private function adding_price_request_submit()
    {
        // Check Nonce
        if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_adding_price_request_form')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));

        $parameters = wpl_request::getVar('wplfdata', array());
        $property_id = isset($parameters['property_id']) ? $parameters['property_id'] : 0;

        $returnData = array();
        if(!$property_id)
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Invalid Property!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['email']) == false || !filter_var($parameters['email'], FILTER_VALIDATE_EMAIL))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your email is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['name']) == false || $parameters['name'] == '')
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your name is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(isset($parameters['tel']) == false || $parameters['tel'] == '')
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Contact phone number is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_adding_price_request_form'))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        else
        {
            if(wpl_events::trigger('wpl_adding_price_request', $parameters))
            {
                $returnData['success'] = 1;
                $returnData['message'] = __('Adding price request sent successfully.', 'real-estate-listing-realtyna-wpl');
            }
            else
            {
                $returnData['success'] = 0;
                $returnData['message'] = __('Error sending!', 'real-estate-listing-realtyna-wpl');
            }
        }

        echo json_encode($returnData);
        exit;
    }

    private function watch_changes_form()
    {
        $this->property_id = wpl_request::getVar('pid', 0);
        echo parent::render($this->tpl_path, 'watch_changes_form', false, true);
        exit;
    }

    private function watch_changes_submit()
    {
        // Check Nonce
        if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_watch_changes_form')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
        $returnData = array();
        $parameters = wpl_request::getVar('wplfdata', array());
        $property_id = isset($parameters['property_id']) ? $parameters['property_id'] : 0;

        if(!$property_id)
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Invalid Property!', 'real-estate-listing-realtyna-wpl');
        }
        else
        {
            $user_id = wpl_users::get_cur_user_id();
            
            if(!$user_id)
            {
                if($parameters['guest_method'] == 'register')
                {
                    $username = $parameters['email'];
                    $email = $parameters['email'];
                    $phone = $parameters['phone'];

                    if(!$email or !$phone)
                    {
                        $returnData['success'] = 0;
                        $returnData['message'] = __('You must fill all the fields!', 'real-estate-listing-realtyna-wpl');
                        echo json_encode($returnData);
                        exit;
                    }
                    
                    /** Checking validation of email **/
                    if(!filter_var($email, FILTER_VALIDATE_EMAIL))
                    {
                        $returnData['success'] = 0;
                        $returnData['message'] = __('Invalid email!', 'real-estate-listing-realtyna-wpl');
                        echo json_encode($returnData);
                        exit;
                    }
        
                    /** Checking existance of email **/
                    if(wpl_users::email_exists($email))
                    {
                        $returnData['success'] = 0;
                        $returnData['message'] = __('Email exists!', 'real-estate-listing-realtyna-wpl');
                        echo json_encode($returnData);
                        exit;
                    }
                    
                    $password = wpl_global::generate_password(8);
                    $user_id = wpl_users::insert_user(array('user_login'=>$username, 'user_email'=>$email, 'user_pass'=>$password));

                    if(is_wp_error($user_id))
                    {
                        $returnData['success'] = 0;
                        $returnData['message'] = $user_id->get_error_message();
                        echo json_encode($returnData);
                        exit; 
                    }

                    wpl_users::add_user_to_wpl($user_id);
                    wpl_users::update('wpl_users', $user_id, 'tel', $phone);
                }
                else
                {
                    $username = $parameters['username'];
                    $password = $parameters['password'];

                    if(!$username or !$password)
                    {
                        $returnData['success'] = 0;
                        $returnData['message'] = __('You must fill all the fields!', 'real-estate-listing-realtyna-wpl');
                        echo json_encode($returnData);
                        exit;
                    }
                }

                $credentials = array();
                $credentials['user_login'] = $username;
                $credentials['user_password'] = $password;
                $credentials['remember'] = 0;
                $result = wpl_users::login_user($credentials);

                if(is_wp_error($result))
                {
                    $returnData['success'] = 0;
                    $returnData['message'] = $result->get_error_message();
                    echo json_encode($returnData);
                    exit; 
                }

                $user_id = wpl_users::get_cur_user_id();
            }

            if(isset($parameters['enabled']) and $parameters['enabled'] == 'on')
            {
                if(!wpl_db::exists($property_id, 'wpl_addon_watch_changes', 'pid'))
                {
                    $query = "INSERT INTO `#__wpl_addon_watch_changes` (`pid`, `user_id`) VALUES ('$property_id', '$user_id')";
                    wpl_db::q($query, 'insert');    
                }
            }
            else
            {
                $query = "DELETE FROM `#__wpl_addon_watch_changes` WHERE `pid` = '$property_id' AND `user_id` = '$user_id'";
                wpl_db::q($query, 'delete');
            }

            $returnData['success'] = 1;
            $returnData['message'] = __('Saved successfully.', 'real-estate-listing-realtyna-wpl');
        }

        echo json_encode($returnData);
        exit;
    }
}