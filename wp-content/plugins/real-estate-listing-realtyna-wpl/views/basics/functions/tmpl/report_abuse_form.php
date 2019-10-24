<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl-links-report-wp" id="wpl_form_report_abuse_container">
    <form class="wpl-gen-form-wp" id="wpl_report_abuse_form" onsubmit="wpl_report_abuse_submit(); return false;" novalidate="novalidate">
        <div class="wpl-gen-form-row">
            <label for="wpl-links-report-name"><?php echo __('Name', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="text" name="wplfdata[name]" id="wpl-links-report-name" placeholder="<?php echo __('Name', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl-gen-form-row">
            <label for="wpl-links-report-email"><?php echo __('Email', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="email" name="wplfdata[email]" placeholder="<?php echo __('Email', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl-gen-form-row">
            <label for="wpl-links-report-tel"><?php echo __('Phone', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="tel" name="wplfdata[tel]" placeholder="<?php echo __('Phone', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl-gen-form-row">
            <label for="wpl-links-report-message"><?php echo __('Message', 'real-estate-listing-realtyna-wpl'); ?></label>
            <textarea name="wplfdata[message]" placeholder="<?php echo __('Message', 'real-estate-listing-realtyna-wpl'); ?>"></textarea>
        </div>
        <div class="wpl-gen-form-row wpl-recaptcha">
            <label for="wpl-links-report-message"></label>
            <?php echo wpl_global::include_google_recaptcha('gre_report_listing', $this->property_id); ?>
            <?php wpl_security::nonce_field('wpl_report_abuse_form'); ?>
        </div>
        
        <div class="wpl-gen-form-row wpl-util-right">
            <input class="wpl-gen-btn-1" type="submit" value="<?php echo __('Send', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl_show_message"></div>

        <input type="hidden" name="wplfdata[property_id]" value="<?php echo $this->property_id; ?>" />
    </form>
</div>