<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="fanc-content size-width-2">
    <h2><?php echo __('Edit User', 'real-estate-listing-realtyna-wpl'); ?></h2>
    <div class="wpl_show_message"></div>

    <div class="fanc-body">
        <div class="fanc-row fanc-button-row-2">
            <input type="button" class="wpl-button button-1" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>" onclick="wpl_save_user();" />
        </div>
        <div class="col-wp">
            <div class="col-fanc-left fanc-tabs-wp">
                <ul>
                    <li class="active">
                        <a href="#basic" class="tab-basic" id="wpl_slide_label_id_basic" onclick="rta.internal.slides.open('_basic','.fanc-tabs-wp','.fanc-content-body');"><?php echo __('Basic', 'real-estate-listing-realtyna-wpl'); ?></a>
                    </li>
                    <li>
                        <a href="#advanced" class="tab-advanced" id="wpl_slide_label_id_advanced" onclick="rta.internal.slides.open('_advanced','.fanc-tabs-wp','.fanc-content-body');"><?php echo __('Advanced', 'real-estate-listing-realtyna-wpl'); ?></a>
                    </li>
                    <li>
                        <a href="#pricing" class="tab-pricing" id="wpl_slide_label_id_pricing" onclick="rta.internal.slides.open('_pricing','.fanc-tabs-wp','.fanc-content-body');"><?php echo __('Pricing', 'real-estate-listing-realtyna-wpl'); ?></a>
                    </li>
                    <?php if(wpl_global::check_addon('brokerage') and $this->user_data->membership_type == 7): ?>
                    <li>
                        <a href="#brokerage" class="tab-brokerage" id="wpl_slide_label_id_brokerage" onclick="rta.internal.slides.open('_brokerage','.fanc-tabs-wp','.fanc-content-body');"><?php echo __('Brokerage', 'real-estate-listing-realtyna-wpl'); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if(wpl_global::check_addon('crm')): ?>
                    <li>
                        <a href="#crm" class="tab-crm" id="wpl_slide_label_id_crm" onclick="rta.internal.slides.open('_crm','.fanc-tabs-wp','.fanc-content-body');"><?php echo __('CRM', 'real-estate-listing-realtyna-wpl'); ?></a>
                    </li>
                    <?php endif; ?>
                    <?php if(wpl_global::check_addon('rets')): ?>
                    <li class="<?php echo (wpl_users::check_access('rets', NULL, $this->user_data->id) ? '' : 'wpl-util-hidden'); ?>" id="wpl_edit_user_rets_tab">
                        <a href="#rets" class="tab-rets" id="wpl_slide_label_id_rets" onclick="rta.internal.slides.open('_rets','.fanc-tabs-wp','.fanc-content-body');"><?php echo __('RETS Server', 'real-estate-listing-realtyna-wpl'); ?></a>
                    </li>
                    <?php endif; ?>
                </ul>
            </div>
            <div class="col-fanc-right fanc-content-wp" id="wpl_edit_user">
                <div class="fanc-content-body" id="wpl_slide_container_id_basic">
                    <div class="fanc-row">
                        <div class="mark-controls">
                            <input type="button" class="wpl-button button-2" value="<?php echo __('Toggle', 'real-estate-listing-realtyna-wpl'); ?>" onclick="rta.util.checkboxes.toggle(wpl_slide_container_id_basic);" />
                            <input type="button" class="wpl-button button-2" value="<?php echo __('All', 'real-estate-listing-realtyna-wpl'); ?>" onclick="rta.util.checkboxes.selectAll(wpl_slide_container_id_basic);" />
                            <input type="button" class="wpl-button button-2" value="<?php echo __('None', 'real-estate-listing-realtyna-wpl'); ?>" onclick="rta.util.checkboxes.deSelectAll(wpl_slide_container_id_basic);" />
                        </div>
                        <div class="access-checkbox-wp">
                            <input type="hidden" id="id" name="id" value="<?php echo $this->user_data->id; ?>" />
                            <?php
                            foreach($this->fields as $field=>$value)
                            {
                                if(substr($value, 0, 7) != 'access_') continue;
                                
                                echo '<div class="checkbox-wp"><input type="checkbox" id="' . $value . '" value="' . $this->user_data->{$value} . '"' . ($this->user_data->{$value} == 1 ? 'checked="checked"' : '');
                                echo ' /> <label for="' . $value . '">' . str_replace('_', ' ', substr($value, 7)) . '</label></div>';
                            }
                            ?>
                        </div>
                    </div>
                </div>
                <div class="fanc-content-body" id="wpl_slide_container_id_advanced" style="display: none">
                    <div id="tab_setting_advance">
                        <?php $this->generate_tab('internal_setting_advanced'); ?>
                    </div>
                </div>
                <div class="fanc-content-body" id="wpl_slide_container_id_pricing" style="display: none">
                    <div id="tab_setting_pricing">
                        <?php $this->generate_tab('internal_setting_pricing'); ?>
                    </div>
                </div>
                <?php if(wpl_global::check_addon('brokerage') and $this->user_data->membership_type == 7): ?>
                <div class="fanc-content-body" id="wpl_slide_container_id_brokerage" style="display: none">
                    <div id="tab_setting_brokerage">
                        <?php $this->generate_tab('internal_setting_brokerage'); ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(wpl_global::check_addon('crm')): ?>
                <div class="fanc-content-body" id="wpl_slide_container_id_crm" style="display: none">
                    <div id="tab_setting_crm">
                        <?php $this->generate_tab('internal_setting_crm'); ?>
                    </div>
                </div>
                <?php endif; ?>
                <?php if(wpl_global::check_addon('rets')): ?>
                <div class="fanc-content-body" id="wpl_slide_container_id_rets" style="display: none">
                    <div id="tab_setting_rets">
                        <?php $this->generate_tab('internal_setting_rets'); ?>
                    </div>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
</div>