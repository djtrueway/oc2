<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.login', true, ($this->wplraw ? false : true));

$fb_app_id = wpl_settings::get('fb_login_appid');
$fb_app_secret = wpl_settings::get('fb_login_secret');
$fb_login_enabled = ($fb_app_id and $fb_app_secret);
$fb_login_url = '';

if($fb_login_enabled)
{
    _wpl_import('libraries.vendors.facebook.autoload');

    $fb = new Facebook\Facebook(array(
        'app_id' => $fb_app_id,
        'app_secret' => $fb_app_secret,
        'default_graph_version' => 'v2.2',
    ));

    $fb_callback = wpl_addon_membership::URL('fblogin');
    $fb_helper = $fb->getRedirectLoginHelper();
    $fb_login_url = htmlspecialchars($fb_helper->getLoginUrl($fb_callback, ['email']));
}

$twitter_api_key = wpl_settings::get('twitter_login_key');
$twitter_api_secret = wpl_settings::get('twitter_login_secret');
$twitter_login_enabled = ($twitter_api_key and $twitter_api_secret);
$twitter_login_url = '';

if($twitter_login_enabled)
{
    _wpl_import('libraries.vendors.twitter.autoload');

    $twitter_callback = wpl_addon_membership::URL('twitterlogin');
    $connection = new Abraham\TwitterOAuth\TwitterOAuth($twitter_api_key, $twitter_api_secret);
    $request_token = $connection->oauth('oauth/request_token', array('oauth_callback' => $twitter_callback));
    $twitter_login_url = $connection->url('oauth/authorize', array('oauth_token' => $request_token['oauth_token']));

    session_start();
    wpl_session::set('oauth_token', $request_token['oauth_token']);
    wpl_session::set('oauth_token_secret', $request_token['oauth_token_secret']);
}

$instagram_app_key = wpl_settings::get('instagram_login_key');
$instagram_app_secret = wpl_settings::get('instagram_login_secret');
$instagram_login_enabled = ($instagram_app_key and $instagram_app_secret);
$instagram_login_url = '';

if($instagram_login_enabled)
{
    $instagram_callback = wpl_addon_membership::URL('instagramlogin');

    _wpl_import('libraries.vendors.instagram.vendor.autoload');

    $instagram = new Andreyco\Instagram\Client(array(
        'apiKey'      => $instagram_app_key,
        'apiSecret'   => $instagram_app_secret,
        'apiCallback' => $instagram_callback,
    ));

    $instagram_login_url = $instagram->getLoginUrl();
}
?>
<div class="wpl-user-login-register" id="wpl_user_login_register_container">
    <div id="wpl_user_login_register_form_container">

        <form id="wpl_user_login_register_form" class="wpl-gen-form-wp wpl-login-register-form-wp" method="POST" onsubmit="wpl_user_logreg(); return false;">

            <div class="wpl-util-hidden" id="wpl_user_login_register_form_register">

                <div class="wpl-gen-form-row">
                    <label for="wpl_lr_email"><?php echo __('Email', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                    <input type="email" name="email" id="wpl_lr_email" autocomplete="off" />
                </div>
                
                <div class="wpl-gen-form-row">
                    <label for="wpl_lr_first_name"><?php echo __('First Name', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                    <input type="text" name="first_name" id="wpl_lr_first_name" autocomplete="off" />
                </div>
                
                <div class="wpl-gen-form-row">
                    <label for="wpl_lr_last_name"><?php echo __('Last Name', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                    <input type="text" name="last_name" id="wpl_lr_last_name" autocomplete="off" />
                </div>
                
                <div class="wpl-gen-form-row">
                    <label for="wpl_lr_tel"><?php echo __('Phone', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                    <input type="text" name="tel" id="wpl_lr_tel" autocomplete="off" />
                </div>

            </div>

            <div id="wpl_user_login_register_form_login" class="wpl-gen-form-wp">

                <div class="wpl-gen-form-row">
                    <label for="wpl_lr_username"><?php echo __('Username', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                    <input type="text" name="username" id="wpl_lr_username" autocomplete="off" />
                </div>

                <div class="wpl-gen-form-row">
                    <label for="wpl_lr_password"><?php echo __('Password', 'real-estate-listing-realtyna-wpl'); ?>: </label>
                    <input type="password" name="password" id="wpl_lr_password" autocomplete="off" />
                </div>

            </div>
            <div class="wpl-gen-form-row last wpl-row wpl-expanded clearfix">
                <div id="wpl_user_login_register_toggle" class="wpl-toggle-btns wpl-small-6 wpl-medium-6 wpl-large-6 wpl-column">
                    <div class="wpl-util-hidden" id="wpl_user_login_register_toggle_register">
                        <?php echo sprintf(__('Already a member? %s', 'real-estate-listing-realtyna-wpl'), '<a href="#" class="wpl-gen-link" onclick="wpl_user_logreg_toggle(\'login\');return false;">'.__('Login', 'real-estate-listing-realtyna-wpl').'</a>'); ?>
                    </div>
                    <div id="wpl_user_login_register_toggle_login">
                        <?php echo sprintf(__('Not a member? %s', 'real-estate-listing-realtyna-wpl'), '<a href="#" class="wpl-gen-link" onclick="wpl_user_logreg_toggle(\'register\');return false;">'.__('Register', 'real-estate-listing-realtyna-wpl').'</a>'); ?>
                    </div>
                </div>
                <div class="wpl-util-right wpl-small-6 wpl-medium-6 wpl-large-6 wpl-column">
                    <button type="submit" class="wpl-gen-btn-1 wpl-util-hidden" id="wpl_user_login_register_register_submit"><?php echo __('Register & Continue', 'real-estate-listing-realtyna-wpl'); ?></button>
                    <button type="submit" class="wpl-gen-btn-1" id="wpl_user_login_register_login_submit"><?php echo __('Login & Continue', 'real-estate-listing-realtyna-wpl'); ?></button>
                </div>
            </div>

            <input type="hidden" name="wpl_function" value="login" id="wpl_user_logreg_guest_method" />
            <input type="hidden" name="token" id="wpl_user_login_register_token" value="<?php echo $this->wpl_security->token(); ?>" />

        </form>

        <div class="wpl-social-login-container">
            <?php if($fb_login_enabled): ?>
                <div class="wpl-login-form-row">
                    <div class="wpl_facebook_sign_in">
                        <a href="<?php echo $fb_login_url; ?>" class="wpl_facebook_sign_in_btn">
                            <span class="wpl_fb_sign_in_inner"><?php _e('Login with Facebook', 'real-estate-listing-realtyna-wpl') ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($twitter_login_enabled): ?>
                <div class="wpl-login-form-row">
                    <div class="wpl_twitter_sign_in">
                        <a href="<?php echo $twitter_login_url; ?>" class="wpl_twitter_sign_in_btn">
                            <span class="wpl_twitter_sign_in_inner"><?php _e('Login with Twitter', 'real-estate-listing-realtyna-wpl') ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>

            <?php if($instagram_login_enabled): ?>
                <div class="wpl-login-form-row">
                    <div class="wpl_instagram_sign_in">
                        <a href="<?php echo $instagram_login_url; ?>" class="wpl_instagram_sign_in_btn">
                            <span class="wpl_instagram_sign_in_inner"><?php _e('Login with Instagram', 'real-estate-listing-realtyna-wpl') ?></span>
                        </a>
                    </div>
                </div>
            <?php endif; ?>
        </div>

        <div id="wpl_user_login_register_form_show_messages"></div>

    </div>
</div>