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
                <h3><?php echo __("Organic IDX Full Version"); ?></h3>
                <div class="panel-body">
                    <div class="wpl-idx-wizard">
                        <div class="wpl-wizard-tabs">
                            <ul class="wpl-row">
                                <li id="wpl-idx-wizard-step1" class="wpl-small-3 wpl-medium-3 wpl-large-3 wpl-column current" >
                                    <span class="number">1</span>
                                    <span><?php echo __('Sign Up', 'real-estate-listing-realtyna-wpl'); ?></span>
                                </li>
                                <li id="wpl-idx-wizard-step2" class="wpl-small-3 wpl-medium-3 wpl-large-3 wpl-column" >
                                    <span class="number">2</span>
                                    <span><?php echo __('Choose MLS', 'real-estate-listing-realtyna-wpl'); ?></span>
                                </li>
                                <li id="wpl-idx-wizard-step3" class="wpl-small-3 wpl-medium-3 wpl-large-3 wpl-column" >
                                    <span class="number">3</span>
                                    <span><?php echo __('Payment', 'real-estate-listing-realtyna-wpl'); ?></span>
                                </li>
                                <li id="wpl-idx-wizard-step4" class="wpl-small-3 wpl-medium-3 wpl-large-3 wpl-column" >
                                    <span class="number">4</span>
                                    <span><?php echo __('Configuration', 'real-estate-listing-realtyna-wpl'); ?></span>
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
                                    <a class="btn next" onclick="wpl_idx_form_validation('.wpl-idx-sign-up','registration');"><?php echo __('Next', 'real-estate-listing-realtyna-wpl');?></a>
                                </div>
                            </div>
                            <div id="wpl-wizard-section2" class="wpl-wizard-section wpl-idx-choose-mls wpl-idx-table clearfix">
                                <div class="wpl-row wpl-expanded wpl-idx-table-tools">
                                    <div class="wpl-small-12 wpl-medium-6 wpl-large-4 wpl-column wpl-idx-form">
                                        <div class="wpl-idx-form-element">
                                            <input id="wpl-idx-search-mls-provider" value="" type="text" placeholder="<?php echo __('Search Your MLS','real-estate-listing-realtyna-wpl'); ?>">
                                            <span class="wpl-idx-icon search-icon"></span>
                                        </div>
                                    </div>
                                    <div class="wpl-small-12 wpl-medium-6 wpl-large-6 wpl-large-offset-2 wpl-column wpl-idx-add-mls-request">
                                        <a class="btn" href="#wpl_request_mls_fancybox_cnt" data-realtyna-lightbox data-realtyna-lightbox-opts="reloadPage:false" data-realtyna-href="#wpl_request_mls_fancybox_cnt"><?php echo __('My MLS is not in the list! Request MLS','real-estate-listing-realtyna-wpl'); ?></a>
                                        <div id="wpl_request_mls_fancybox_cnt" class="wpl_hidden_element">
                                            <div class="fanc-content size-width-1">
                                                <h2><?php echo __('Request MLS', 'real-estate-listing-realtyna-wpl'); ?></h2>
                                                <div class="fanc-body">
                                                    <div class="fanc-row">
                                                        <label for="wpl_location_name"><?php echo __('MLS Provider', 'real-estate-listing-realtyna-wpl'); ?></label>
                                                        <input class="text_box" type="text" id="wpl_request_mls_provider" value="" autocomplete="off" />
                                                    </div>
                                                    <div class="fanc-row">
                                                        <label for="wpl_location_abbr"><?php echo __('State', 'real-estate-listing-realtyna-wpl'); ?></label>
                                                        <input class="text_box" type="text" id="wpl_request_mls_state" value="" autocomplete="off" />
                                                    </div>
                                                    <div class="fanc-row fanc-button-row">
                                                        <input class="wpl-button button-1" type="submit" id="wpl_submit" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>" onclick="wpl_idx_request_mls();" />
                                                        <span class="ajax-inline-save" id="wpl_ajax_loader"></span>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>
                                <table id="wpl-idx-all-mls-providers" class="wpl-idx-addon-table page">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="message"><?php echo __('No MLS Provider is Found!', 'real-estate-listing-realtyna-wpl');?></div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div id="wpl-idx-total-price-choose-mls" class="wpl-idx-total-price">
                                    <strong><?php echo __('Total Amount', 'real-estate-listing-realtyna-wpl');?>:</strong>
                                    <span class="price">$0</span>
                                    <span class="recurring">Per Month</span>
                                </div>
                                <div class="clearfix"></div>
                                <div class="wpl-idx-wizard-navigation clearfix">
                                    <a class="btn next" onclick="wpl_idx_save();"><?php echo __('Next', 'real-estate-listing-realtyna-wpl');?></a>
                                    <a class="btn back" onclick="wpl_idx_back_step('register');"><?php echo __('Back', 'real-estate-listing-realtyna-wpl');?></a>
                                </div>
                            </div>
                            <div id="wpl-wizard-section3" class="wpl-wizard-section wpl-idx-payment">

                                <table id="wpl-idx-selected-mls-providers" class="wpl-idx-addon-table page">
                                    <tbody>
                                    <tr>
                                        <td>
                                            <div class="message"><?php echo __('No MLS Provider is Found!', 'real-estate-listing-realtyna-wpl');?></div>
                                        </td>
                                    </tr>
                                    </tbody>
                                </table>
                                <div id="wpl-idx-total-price-payment" class="wpl-idx-total-price">
                                    <strong><?php echo __('Total Amount', 'real-estate-listing-realtyna-wpl');?>:</strong>
                                    <span class="price">$0</span>
                                    <span class="recurring">Per Month</span>

                                </div>
                                <div class="wpl-idx-help wpl-idx-terms-and-conditions">
                                    <?php 
                                    echo __('By submitting your registration, you agree that you have read and accepted the', 'real-estate-listing-realtyna-wpl');
                                    echo ' <a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/840/28/organic-idx-terms-and-conditions">'.__('Terms and Conditions', 'real-estate-listing-realtyna-wpl').'</a>';
                                    ?>
                                        
                                </div>
                                <div class="clearfix"></div>
                                <div class="wpl-idx-wizard-navigation clearfix">
                                    <a class="btn next" onclick="wpl_idx_payment();"><?php echo __('Pay', 'real-estate-listing-realtyna-wpl');?></a>
                                    <a class="btn back" onclick="wpl_idx_back_step('provider');"><?php echo __('Back', 'real-estate-listing-realtyna-wpl');?></a>
                                </div>
                            </div>
                            <div id="wpl-wizard-section4" class="wpl-wizard-section wpl-idx-configuration wpl-idx-form">
                                <div id="wpl-idx-selected-mls-providers-configuration" class="wpl-idx-addon-table">
                                    <div class="message"><?php echo __('No Configuration is Found!', 'real-estate-listing-realtyna-wpl');?></div>
                                </div>
                                <div class="wpl-idx-help">
                                    <?php echo __("Need help about this step?",'real-estate-listing-realtyna-wpl'); ?>
                                    <a href="https://realtyna.com/contact" target="_blank"><?php echo __("Please create a ticket here",'real-estate-listing-realtyna-wpl'); ?></a>
                                </div>
                                <div class="wpl-idx-wizard-navigation clearfix">
                                    <a class="btn next" onclick="wpl_idx_form_validation('.wpl-idx-config-form-part1 .wpl-idx-form','configuration');"><?php echo __('Next', 'real-estate-listing-realtyna-wpl');?></a>
                                    <!--a<a class="btn back" ></a>-->
                                </div>
                            </div>
                        </div>
                        <div class="wpl_show_message_idx"></div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>