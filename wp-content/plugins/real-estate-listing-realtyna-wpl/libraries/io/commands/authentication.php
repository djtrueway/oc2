<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.notifications.notifications');

/**
 * The authentication command
 * @author Chris <chris@realtyna.com>
 * @since WPL2.7.0
 * @package WPL
 * @date 2015/08/31
 */
class wpl_io_cmd_authentication extends wpl_io_cmd_base
{
    private $built = array();

    /**
     * Disable auto login
     */
    public function __construct()
    {
        $this->set_authentication(false);
    }

    /**
     * check which function should be call
     * @author Chris <chris@realtyna.com>
     * @return array|bool
     */
    public function build()
    {
        if(trim($this->params['setting_type']) == 'login')
        {
            return $this->login();
        }
        elseif(trim($this->params['setting_type']) == 'register')
        {
            return $this->register();
        }
        elseif(trim($this->params['setting_type']) == 'forget_password')
        {
            return $this->forget_password();
        }
        else
        {
            $this->error = "Invalid method type!" ;
            return false;
        }
    }

    /**
     * Login with user and password
     * @author Chris <chris@realtyna.com>
     * @return array
     */
    private function login()
    {
        $user = base64_decode($this->params['user']);
        $pass = base64_decode($this->params['pass']);

        $remember = (array_key_exists('remember', $this->params)) ? $this->params['remember'] : false;
        $login_data = array(
            'user_login'=>$user,
            'user_password'=>$pass,
            'remember'=>$remember
        );

        $user_verify = wpl_users::login_user($login_data);
        if(is_wp_error($user_verify))
        {
            $this->built['authentication'] = array(
                'type'  =>'login',
                'status'=>false,
                'uid'   =>0
            );
        }
        else
        {
            $user_id = $user_verify->ID;
            wp_set_current_user($user_id, $login_data);
            wp_set_auth_cookie($user_id, true, false);
            $this->built['authentication'] = array(
                'type'  =>'login',
                'status'=>true,
                'uid'   =>wpl_users::get_cur_user_id()
            );
        }
        
        return $this->built;
    }

    /**
     * Register a user in system
     * @author Chris <chris@realtyna.com>
     * @return array
     */
    private function register()
    {
        $name = base64_decode($this->params['fullname']);
        $phone = base64_decode($this->params['phone']);
        $pass = base64_decode($this->params['pass']);
        $email = base64_decode($this->params['user_email']);

        $user_data = array(
            'display_name'=>$name,
            'user_login'=>$email,
            'user_email'=>$email,
            'user_pass'=>$pass,
            'description'=>'Registered With Mobile'
        );

        $user_id = wpl_users::insert_user($user_data);
        if($user_id == false || is_wp_error($user_id))
        {
            $this->built['authentication'] = array(
                'type'=>'register',
                'status'=>false
            );
        }
        else
        {
            wpl_users::add_user_to_wpl($user_id);
            wpl_users::update('wpl_users', $user_id, 'tel', $phone);

            $this->built['authentication'] = array(
                'type'=>'register',
                'status'=>true
            );
        }
        
        return $this->built;
    }

    /**
     * Forget password handling
     * @author Chris <chris@realtyna.com>
     * @return array
     */
    private function forget_password()
    {
        $user = base64_decode($this->params['user']);
        $error_array = array('authentication' => array('type' => 'forget_password', 'status'=> false ));

        $db = wpl_db::get_DBO();
        $user_login = trim(wpl_db::sanitize($user));

        if($user_login == '')
        {
            return $error_array;
        }
        elseif(strpos($user_login, '@'))
        {
            $user_data = wpl_users::get_user_by('email', $user_login);
        }
        else
        {
            $user_data = wpl_users::get_user_by('login', $user_login);
        }

        if(!$user_data) return $error_array;

        $user_login = $user_data->user_login;
        $user_email = $user_data->user_email;

        do_action('lostpassword_post');
        do_action('retreive_password', $user_login);
        do_action('retrieve_password', $user_login);

        $allow = apply_filters('allow_password_reset', true, $user_data->ID);

        if(is_wp_error($allow))
        {
            return $error_array;
        }

        $key = wpl_global::generate_password(20, false);
        do_action('retrieve_password_key', $user_login, $key);
        $hashed = wpl_global::wpl_hasher(8, $key);

        wpl_db::update('users', array('user_activation_key'=>$hashed), 'user_login', $user_login);

        $is_membership_url = wpl_settings::get('membership_user_action_urls');
        if($is_membership_url)
        {
            $parameters = array('user_activation_key'=>$hashed, 'user_id'=>$user_data->ID);
            wpl_events::trigger('user_reset_password_request', $parameters);    
        }
        else
        {
            $message = __('We received a password be reset request for the following account:', 'real-estate-listing-realtyna-wpl')."\r\n";
            $message .= network_home_url('/')."\r\n";
            $message .= sprintf(__('Username: %s', 'real-estate-listing-realtyna-wpl'), $user_login)."\r\n";
            $message .= __('If you did not request this change, please ignore this email.', 'real-estate-listing-realtyna-wpl')."\r\n";
            $message .= __('To reset your password, please follow the link below:', 'real-estate-listing-realtyna-wpl')."\r\n";
            $message .= network_site_url("wp-login.php?action=rp&key=$key&login=".rawurlencode($user_login), 'login')."\r\n";

            if(is_multisite())
            {
                $blogname = $GLOBALS['current_site']->site_name;
            }
            else
            {
                $blogname = wp_specialchars_decode(get_option('blogname'),ENT_QUOTES);
            }

            $sender = wpl_notifications::get_sender();
            $from = is_array($sender) ? $sender[1] : $sender;
            $title = sprintf(__('[%s] Password Reset', 'real-estate-listing-realtyna-wpl'), $blogname);
            $title = apply_filters('retrieve_password_title', $title);
            $message = apply_filters('retrieve_password_message', $message, $key);
            $headers = "From: {$from}\nContent-Type: text/html; charset=UTF-8\n";

            if(($message) && (!wp_mail($user_email, $title, $message, $headers)))
            {
                return $error_array;
            } 
        }

        $this->built['authentication'] = array('type' => 'forget_password', 'status' => true);
        return $this->built;
    }

    /**
     * Data validation
     * @author Chris <chris@realtyna.com>
     * @return boolean
     */
    public function validate()
    {
        if(isset($this->params['setting_type']) == false || trim($this->params['setting_type']) == "")
        {
            return false;
        }
        else
        {
            if($this->params['setting_type'] == 'register')
            {
                if(isset($this->params['user_email']) == false || isset($this->params['fullname']) == false || isset($this->params['pass']) == false)
                {
                    return false;
                }
            }
            else if($this->params['setting_type'] == 'login')
            {
                if(isset($this->params['user']) == false || isset($this->params['pass']) == false)
                {
                    return false;
                }
            }
            else if($this->params['setting_type'] == 'forget_password')
            {
                if(isset($this->params['user']) == false)
                {
                    return false;
                }
            }
        }

        return true;
    }
}
