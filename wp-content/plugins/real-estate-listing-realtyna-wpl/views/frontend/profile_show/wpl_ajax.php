<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

class wpl_profile_show_controller extends wpl_controller
{
    /**
     * @var wpl_security
     */
    public $wpl_security;
    public $token;

	public function display()
	{
		$function = wpl_request::getVar('wpl_function');
        
        $this->wpl_security = new wpl_security();
        $this->token = wpl_request::getVar('token', NULL);
        
        if($function == 'login') $this->login();
        elseif($function == 'register') $this->register();
	}
    
    private function login()
    {
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array()));
        
        $vars = wpl_request::get('POST');
        
        $credentials = array();
        $credentials['user_login'] = isset($vars['username']) ? $vars['username'] : NULL;
        $credentials['user_password'] = isset($vars['password']) ? $vars['password'] : NULL;
        $credentials['remember'] = 0;
        
        $result = wpl_users::login_user($credentials);
        
        if(is_wp_error($result))
        {
            $success = 0;
            $code = $result->get_error_code();
            
            if($code == 'incorrect_password') $message = __('<strong>ERROR</strong>: The password you entered for the username is incorrect.', 'real-estate-listing-realtyna-wpl');
            elseif($code == 'invalid_username') $message = __('<strong>ERROR</strong>: Invalid username.', 'real-estate-listing-realtyna-wpl');
            else $message = $result->get_error_message();
            
            $data = array('token'=>$this->wpl_security->token());
        }
        else
        {
            $success = 1;
            $message = __('You logged in successfully!', 'real-estate-listing-realtyna-wpl');
            $code = NULL;
            $data = array('user_id'=>$result->data->ID);
        }
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }
    
    private function register()
    {
        $vars = wpl_request::get('POST');
        
        if(!wpl_global::get_wp_option('users_can_register')) $this->response(array('success'=>0, 'message'=>__('Registration disabled!', 'real-estate-listing-realtyna-wpl'), 'code'=>'registration_disabled', 'field_name'=>NULL, 'data'=>array()));
        
        $username = $vars['email'];
        $email = $vars['email'];
        $password = wpl_global::generate_password(8);
        
        if(!$this->wpl_security->validate_token($this->token, true)) $this->response(array('success'=>0, 'message'=>__('Invalid Token!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_token', 'field_name'=>'token', 'data'=>array('token'=>$this->wpl_security->token())));
        if(!filter_var($email, FILTER_VALIDATE_EMAIL)) $this->response(array('success'=>0, 'message'=>__('Invalid Email!', 'real-estate-listing-realtyna-wpl'), 'code'=>'invalid_email', 'field_name'=>'email', 'data'=>array('token'=>$this->wpl_security->token())));
        
        /** Checking existance of email **/
        if(wpl_users::email_exists($email)) $this->response(array('success'=>0, 'message'=>__('Email exists.', 'real-estate-listing-realtyna-wpl'), 'code'=>'email_exists', 'field_name'=>'email', 'data'=>array('token'=>$this->wpl_security->token())));
        
        $first_name = isset($vars['first_name']) ? $vars['first_name'] : '';
        $last_name = isset($vars['last_name']) ? $vars['last_name'] : '';
        
        $result = wpl_users::insert_user(array('user_login'=>$username, 'user_email'=>$email, 'user_pass'=>$password, 'first_name'=>$first_name, 'last_name'=>$last_name));
        
        if(is_wp_error($result))
        {
            $success = 0;
            $code = $result->get_error_code();
            
            $message = $result->get_error_message();
            $data = array('token'=>$this->wpl_security->token());
        }
        else
        {
            $user_id = $result;
            
            /** Trigger event for sending notification **/
            wpl_events::trigger('user_registered', array('password'=>$password, 'user_id'=>$user_id));
            
            /** Change membership of user to default membership **/
            wpl_users::change_membership($user_id);
            
            $tel = isset($vars['tel']) ? $vars['tel'] : '';
            wpl_users::update('wpl_users', $user_id, 'tel', $tel);
            
            $success = 1;
            $message = __('User registered. Please check your email for password and then login.', 'real-estate-listing-realtyna-wpl');
            $code = NULL;
            $data = array('user_id'=>$user_id, 'token'=>$this->wpl_security->token());
        }
        
        $this->response(array('success'=>$success, 'message'=>$message, 'code'=>$code, 'field_name'=>NULL, 'data'=>$data));
    }
}