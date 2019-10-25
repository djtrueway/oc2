<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import($this->tpl_path.'.scripts.js');
?>
<script src="https://js.stripe.com/v3/"></script>
<div class="wpl-idx-addon wrap wpl-wp settings-wp">
    <div class="wpl-idx-choose-plan">
        <header>
            <div id="icon-settings" class="icon48"></div>
            <h2><?php echo __('Organic IDX / Plan', 'real-estate-listing-realtyna-wpl'); ?></h2>
        </header>
        <section class="sidebar-wp">
            <div class="panel-wp">
                <h3><?php echo __("Choose your plan"); ?></h3>
                <div class="panel-body">
                    <div class="wpl-row" style="width: 50%;margin: auto">
                        <div class="wpl-small-12 wpl-medium-6 wpl-large-6 wpl-column">
                            <a class="wpl-idx-plan wpl-idx-plan-trial" href="<?php echo wpl_global::add_qs_var('tpl', 'trial'); ?>"  >
                                <span class="title"><?php echo __('Trial Version', 'real-estate-listing-realtyna-wpl');?></span>
                                <span class="description"><?php echo __('In this plan, sample properties will be imported to your website in order to test our software', 'real-estate-listing-realtyna-wpl');?></span>

                            </a>
                        </div>
                        <div class="wpl-small-12 wpl-medium-6 wpl-large-6 wpl-column">
                            <a class="wpl-idx-plan wpl-idx-plan-valid" href="<?php echo wpl_global::add_qs_var('tpl', 'valid'); ?>">
                                <span class="title"><?php echo __('Full Version', 'real-estate-listing-realtyna-wpl');?></span>
                                <span class="description"><?php echo __('In this plan you will receive actual properties from your requested mls provider', 'real-estate-listing-realtyna-wpl');?></span>
                            </a>
                        </div>
                    </div>
                </div>
            </div>
        </section>
    </div>
</div>