<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();
$this->property_id = isset($wpl_properties['current']['data']['id']) ? $wpl_properties['current']['data']['id'] : NULL;

$this->top_comment = isset($params['top_comment']) ? $params['top_comment'] : '';

include _wpl_import($this->tpl_path.'.scripts.js', true, true);
?>
<div class="wpl_contact_container wpl-contact-listing-wp" id="wpl_contact_container<?php echo $this->activity_id; ?><?php echo $this->property_id; ?>">
    <?php if(trim($this->top_comment) != ''): ?>
    <p class="wpl_contact_comment"><?php echo $this->top_comment; ?></p>
    <?php endif; ?>
	<form method="post" action="#" id="wpl_contact_form<?php echo $this->activity_id; ?><?php echo $this->property_id; ?>" onsubmit="return wpl_send_contact<?php echo $this->activity_id; ?>(<?php echo $this->property_id; ?>);">
        <div class="form-field">
            <input class="text-box" type="text" id="wpl_contact_fullname<?php echo $this->activity_id; ?><?php echo $this->property_id; ?>" name="fullname" placeholder="<?php echo __('Full Name', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>

        <div class="form-field">
            <input class="text-box" type="text" id="wpl_contact_phone<?php echo $this->activity_id; ?><?php echo $this->property_id; ?>" name="phone" placeholder="<?php echo __('Phone', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>

        <div class="form-field">
            <input class="text-box" type="text" id="wpl_contact_email<?php echo $this->activity_id; ?><?php echo $this->property_id; ?>" name="email" placeholder="<?php echo __('Email', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>

        <div class="form-field wpl-contact-listing-msg">
            <textarea class="text-box" id="wpl_contact_message<?php echo $this->activity_id; ?><?php echo $this->property_id; ?>" name="message" placeholder="<?php echo __('Message', 'real-estate-listing-realtyna-wpl'); ?>"></textarea>
        </div>
        
        <div class="form-field">
        <?php
            /**
            * Fires for integrating contact forms with third party plugins such as captcha plugins
            */
            do_action('comment_form_after_fields');
        ?>
        </div>
        <div class="contact-recaptcha">
            <?php echo wpl_global::include_google_recaptcha('gre_listing_contact_activity', $this->property_id); ?>
            <?php wpl_security::nonce_field('wpl_listing_contact_form'); ?>
        </div>
        <div class="form-field wpl-contact-listing-btn">
            <input class="btn btn-primary" type="submit" value="<?php echo __('Send', 'real-estate-listing-realtyna-wpl'); ?>" />
        </div>
    </form>
    <div id="wpl_contact_ajax_loader<?php echo $this->activity_id; ?>_<?php echo $this->property_id; ?>"></div>
    <div id="wpl_contact_message<?php echo $this->activity_id; ?>_<?php echo $this->property_id; ?>"></div>
</div>