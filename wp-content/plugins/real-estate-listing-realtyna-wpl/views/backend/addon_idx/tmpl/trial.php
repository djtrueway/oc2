<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import($this->tpl_path.'.scripts.js');

?>

<script src="https://js.stripe.com/v3/"></script>

<div class="wpl-idx-addon wrap wpl-wp settings-wp">
    <div class="wpl-idx-wizard-main wpl-idx-valid">
        <header>
            <div id="icon-settings" class="icon48"></div>
            <h2><?php echo __('Organic IDX / Registration Wizard', 'real-estate-listing-realtyna-wpl'); ?></h2>
        </header>
        <section class="sidebar-wp">
            <div class="panel-wp">
                <h3><?php echo __("Organic IDX Trial Version"); ?></h3>
                <div class="panel-body">
                    <div class="wpl-idx-wizard">
                        <div class="wpl-wizard-tabs">
                            <ul class="wpl-row">
                                <li id="wpl-idx-wizard-step1" class="wpl-small-4 wpl-medium-4 wpl-large-4 wpl-column current" >
                                    <span class="number">1</span>
                                    <span><?php echo __('Sign Up', 'real-estate-listing-realtyna-wpl'); ?></span>
                                </li>
                                <li id="wpl-idx-wizard-step2" class="wpl-small-4 wpl-medium-4 wpl-large-4 wpl-column" >
                                    <span class="number">2</span>
                                    <span><?php echo __('Import Listings', 'real-estate-listing-realtyna-wpl'); ?></span>
                                </li>
                                <li id="wpl-idx-wizard-step3" class="wpl-small-4 wpl-medium-4 wpl-large-4 wpl-column" >
                                    <span class="number">3</span>
                                    <span><?php echo __('Success', 'real-estate-listing-realtyna-wpl'); ?></span>
                                </li>
                            </ul>
                        </div>
                        <div class="wpl-wizard-sections">
                            <div id="wpl-wizard-section1" class="wpl-wizard-section wpl-idx-sign-up wpl-idx-form current">
                                <div class="wpl-idx-form-element">
                                    <input id="name" name="name" type="text" placeholder="Name"  />
                                    <span class="wpl-idx-icon user-icon"></span>
                                </div>
                                <div class="wpl-idx-form-element">
                                    <input id="email" name="email" type="email" placeholder="Email" />
                                    <span class="wpl-idx-icon email-icon"></span>
                                </div>
                                <div class="wpl-idx-form-element">
                                    <input id="phone" name="phone" type="tel" placeholder="Phone" />
                                    <span class="wpl-idx-icon phone-icon"></span>
                                </div>
                                <div class="wpl-idx-wizard-navigation clearfix">
                                    <span class="loading"></span>
                                    <a class="btn next" onclick="wpl_idx_form_validation('.wpl-idx-sign-up','registration');"><?php echo __('Register & import listings', 'real-estate-listing-realtyna-wpl');?></a>
                                </div>
                            </div>
                            <div id="wpl-wizard-section2" class="wpl-wizard-section wpl-idx-import-listings clearfix">
                                <div class="wpl-idx-progress-bar">
                                    <div id="progress_img">
                                        <div id="progressbar" class="progress progress-success progress-striped">
                                            <div class="bar"></div>
                                        </div>
                                    </div>
                                    <div class="percentage">
                                        <div class="title">
                                            <?php echo __('Importing Sample Listings ...','real-estate-listing-realtyna-wpl'); ?>
                                        </div>
                                        <div class="subtitle">
                                            <?php echo __('Importing sample listings takes a while. Please do not close this window in order to get all sample listings','real-estate-listing-realtyna-wpl'); ?>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div id="wpl-wizard-section3" class="wpl-wizard-section wpl-idx-thank-you">

                            </div>
                        </div>
                    </div>
                    <div class="wpl_show_message_idx"></div>
                </div>
            </div>
        </section>
    </div>
</div>



