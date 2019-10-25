<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$settings = wpl_global::get_settings();
?>
<div class="wpl-cronjobs">

    <div class="wpl_cronjobs_panel"><div class="wpl_show_message"></div></div>
    <p><?php _e("WPL should do some jobs regularly in order to keep your website functional. By default, these regular jobs run when a single property details page is viewed by a visitor. However, this method can create some speed issues on your website frontend. We highly recommend that you to setup a cronjob on your website Control Panel to open the following URL <strong>once per 5 minutes</strong>.", 'real-estate-listing-realtyna-wpl'); ?></p>
    <div><code><?php echo wpl_global::get_wp_url('frontend').'?wpl_do_cronjobs=1'; ?></code> <span><?php echo sprintf(__('Latest Run: %s', 'real-estate-listing-realtyna-wpl'), ((isset($settings['wpl_last_cpanel_cronjobs']) and trim($settings['wpl_last_cpanel_cronjobs'])) ? '<strong>'.$settings['wpl_last_cpanel_cronjobs'].'</strong>' : '<strong>'.__('Never', 'real-estate-listing-realtyna-wpl').'</strong>')); ?></span></div>
    <br><hr>
    <p><?php _e("After setting the cronjob, please disable the default cronjobs using the below form. ATTENTION: Please don't disable it if your cPanel cronjob is not set. If your cPanel cronjob is set correctly, then you will see the latest run time above.", 'real-estate-listing-realtyna-wpl'); ?></p>
    <form id="wpl_cronjobs_toggle_form">
        <span><?php echo sprintf(__('Current Status: %s', 'real-estate-listing-realtyna-wpl'), ((isset($settings['wpl_cronjobs']) and $settings['wpl_cronjobs']) ? '<span id="wpl_cronjobs_label"><strong style="color: red;">'.__('Enabled', 'real-estate-listing-realtyna-wpl').'</strong></span>' : '<span id="wpl_cronjobs_label"><strong style="color: green;">'.__('Disabled', 'real-estate-listing-realtyna-wpl').'</strong></span>')); ?></span>
        <input type="hidden" name="status" id="wpl_cronjobs_status" value="<?php echo (isset($settings['wpl_cronjobs']) and $settings['wpl_cronjobs']) ? '1' : '0'; ?>" />
        <button type="submit" class="wpl-button button-1" id="wpl_cronjobs_toggle_submit"><?php echo (isset($settings['wpl_cronjobs']) and $settings['wpl_cronjobs']) ? __('Disable it', 'real-estate-listing-realtyna-wpl') : __('Enable it', 'real-estate-listing-realtyna-wpl'); ?></button>
    </form>
</div>