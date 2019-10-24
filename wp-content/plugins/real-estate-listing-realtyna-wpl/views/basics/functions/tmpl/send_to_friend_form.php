<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl-links-send-to-friend-wp" id="wpl_form_send_to_friend_container">
    <form class="wpl-gen-form-wp" id="wpl_send_to_friend_form" onsubmit="wpl_send_to_friend_submit(); return false;" novalidate="novalidate">
        <div class="wpl-gen-form-row">
            <label for="wpl-links-send-to-friend-your_name"><?php echo __('Your Name', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="text" name="wplfdata[your_name]" id="wpl-links-send-to-friend-your_name" placeholder="<?php echo __('Your Name', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl-gen-form-row">
            <label for="wpl-links-send-to-friend-your_email"><?php echo __('Your Email', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="email" name="wplfdata[your_email]" id="wpl-links-send-to-friend-your_email" placeholder="<?php echo __('Your Email', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl-gen-form-row">
            <label for="wpl-links-send-to-friend-friends_email"><?php echo __('Friend Email', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="email" name="wplfdata[friends_email]" id="wpl-links-send-to-friend-friends_email" placeholder="<?php echo __('Friend Email', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl-gen-form-row wpl-recaptcha">
            <label for="wpl-links-send-to-friend-email_subject"><?php echo __('Email Subject', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="text" name="wplfdata[email_subject]" id="wpl-links-send-to-friend-email_subject" placeholder="<?php echo __('Email Subject', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl-gen-form-row">
            <label for="wpl-links-send-to-friend-message"><?php echo __('Message', 'real-estate-listing-realtyna-wpl'); ?></label>
            <textarea name="wplfdata[message]" id="wpl-links-send-to-friend-message" placeholder="<?php echo __('Message', 'real-estate-listing-realtyna-wpl'); ?>"></textarea>
        </div>
        <div class="wpl-gen-form-row wpl-recaptcha">
            <label for="wpl-links-report-message"></label>
            <?php echo wpl_global::include_google_recaptcha('gre_send_to_friend', $this->property_id); ?>
            <?php wpl_security::nonce_field('wpl_send_to_friend_form'); ?>
        </div>
        
        <div class="wpl-gen-form-row wpl-util-right">
            <input class="wpl-gen-btn-1" type="submit" value="<?php echo __('Send', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
        <div class="wpl_show_message"></div>
        <input type="hidden" name="wplfdata[property_id]" value="<?php echo $this->property_id; ?>" />
    </form>
</div>