<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.addon_idx.base');
_wpl_import('libraries.addon_idx.service');

class wpl_addon_idx {

    public static function registration($url,$params)
    {

        $data = wpl_addon_idx_base::make_post_request('register', array(
            'url'    => $url,
            'params' => $params
        ));

        return $data;
    }

    public static function providers($url,$trigerData)
    {

        $data = wpl_addon_idx_base::make_get_request('providers', array(
            'url'    => $url,
            'secret' => $trigerData['secret'],
            'token'  => $trigerData['token']
        ));

        return $data;
    }

    public static function save($url,$params,$auth)
    {

        $data = wpl_addon_idx_base::make_post_auth_request('save', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function price($url,$trigerData)
    {

        $data = wpl_addon_idx_base::make_get_request('price', array(
            'url'    => $url,
            'secret' => $trigerData['secret'],
            'token'  => $trigerData['token']
        ));

        return $data;
    }

    public static function calculatePrice($url,$params,$auth)
    {

        $data = wpl_addon_idx_base::make_post_auth_request('payable', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function payment($url,$params,$auth)
    {

        $data = wpl_addon_idx_base::make_post_auth_request('payment', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function configuration($url,$params,$auth)
    {
        $data = wpl_addon_idx_base::make_post_auth_request('config', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function delete_client($url,$params,$auth)
    {
        $data = wpl_addon_idx_base::make_post_auth_request('delete', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function reset_client($url,$params,$auth)
    {
        $data = wpl_addon_idx_base::make_delete_auth_request('reset', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function status($url,$params,$auth)
    {

        $data = wpl_addon_idx_base::make_post_auth_request('status', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function load_trial_data($url,$auth,$user_id)
    {

        $data = wpl_addon_idx_base::make_post_auth_request_without_params('load/trial', array(
            'url'    => $url,
            'params' => false,
            'auth'   => $auth,
            'user_id'=> $user_id
        ));

        return $data;
    }

    public static function back($url,$params,$auth)
    {
        $data = wpl_addon_idx_base::make_post_auth_request('back', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function save_client_request($url,$params,$auth)
    {
        $data = wpl_addon_idx_base::make_post_auth_request('request', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }

    public static function service(array $auth)
    {
        $data = wpl_addon_idx_service::init($auth);
        return $data;
    }

    public static function check_payment($url,$params,$auth)
    {
        $data = wpl_addon_idx_base::make_post_auth_request('check_payment', array(
            'url'    => $url,
            'params' => $params,
            'auth'   => $auth
        ));

        return $data;
    }
}