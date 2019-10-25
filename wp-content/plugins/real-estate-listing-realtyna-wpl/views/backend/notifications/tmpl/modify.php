<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path . '.scripts.modify_js');
$this->_wpl_import($this->tpl_path . '.scripts.css');

$subject_column = 'subject';
if(wpl_global::check_multilingual_status()) $subject_column = wpl_addon_pro::get_column_lang_name($subject_column, wpl_global::get_admin_language(), false);
?>
<form action="#" id="wpl_notification_form">
    <div class="wrap wpl-wp pwizard-wp">
        <header>
            <div id="icon-pwizard" class="icon48"></div>
            <h2><?php echo __('Edit Notification', 'real-estate-listing-realtyna-wpl'); ?> (<?php echo $this->notification->{$subject_column}; ?>) <span class="ajax-inline-save" id="wpl_modify_ajax_loader"></span></h2>
        </header>
        <div class="wpl_notification_modify"><div class="wpl_show_message"></div></div>
        <div class="sidebar-wp">
            <div class="side-2 side-tabs-wp">
                <ul>
                    <li class="finilized">
                        <a id="wpl_notification_submit_button" class="tab-finalize wpl_slide_label" href="#" onclick="wplj('#wpl_notification_form').submit();">
                            <span><?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?></span>
                            <i class="icon-finalize"></i>
                        </a>
                    </li>
                    <li>
                        <a href="#basic" class="wpl-no-icon wpl_slide_label" id="wpl_slide_label_idbasic" onclick="rta.internal.slides.open('basic', '.side-tabs-wp', '.wpl_slide_container', 'currentTab');">
                            <?php echo __('Basic', 'real-estate-listing-realtyna-wpl'); ?>
                        </a>
                    </li>
                    <li>
                        <a href="#advanced" class="wpl-no-icon wpl_slide_label" id="wpl_slide_label_idadvanced" onclick="rta.internal.slides.open('advanced', '.side-tabs-wp', '.wpl_slide_container', 'currentTab');">
                            <?php echo __('Email Options', 'real-estate-listing-realtyna-wpl'); ?>
                        </a>
                    </li>

                    <?php if(wpl_global::check_addon('sms')): ?>
                    <li>
                        <a href="#sms_advanced" class="wpl-no-icon wpl_slide_label" id="wpl_slide_label_idsms_advanced" onclick="rta.internal.slides.open('sms_advanced', '.side-tabs-wp', '.wpl_slide_container', 'currentTab');">
                            <?php echo __('SMS Options', 'real-estate-listing-realtyna-wpl'); ?>
                        </a>
                    </li>
                    <?php endif; ?>

                </ul>
            </div>
            <div class="side-12 side-content-wp">
                <div class="pwizard-panel wpl_slide_container wpl_slide_containerbasic" id="wpl_slide_container_idbasic">
                    <?php $this->generate_basic_options(); ?>
                </div>
                <div class="pwizard-panel wpl_slide_container wpl_slide_containeradvanced" id="wpl_slide_container_idadvanced">
                    <?php $this->generate_advanced_options(); ?>
                </div>

                <?php if(wpl_global::check_addon('sms')): ?>
                <div class="pwizard-panel wpl_slide_container wpl_slide_containeradvanced" id="wpl_slide_container_idsms_advanced">
                    <?php $this->generate_sms_advanced_options(); ?>
                </div>
                <?php endif; ?>

            </div>
        </div>
        <input type="hidden" name="info[id]" value="<?php echo $this->notification->id; ?>" />
    </div>
</form>