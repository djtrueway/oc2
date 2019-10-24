<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

if($type == 'locations' and !$done_this)
{
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
    wplj(".wpl-flex-locations-sortable").sortable(
    {
        handle: 'span.icon-move',
        cursor: "move" ,
        update : function(e, ui)
        {
        }
    });
});

function wpl_flex_disable_location(param_id)
{
    if(wplj("#wpl_flex_change_param_status" + param_id).hasClass("wpl_actions_icon_enable"))
    {
        wplj("#wpl_flex_change_param_status" + param_id).removeClass("wpl_actions_icon_enable").removeClass("icon-enabled");
        wplj("#wpl_flex_change_param_status" + param_id).addClass("wpl_actions_icon_disable").addClass("icon-disabled");
        wplj("#wpl_flex_locations_params_row" + param_id + " input[name='<?php echo $__prefix; ?>opt_params[" + param_id + "][enabled]']").val(0);
    }
    else
    {
        wplj("#wpl_flex_change_param_status" + param_id).removeClass("wpl_actions_icon_disable").removeClass("icon-disabled");
        wplj("#wpl_flex_change_param_status" + param_id).addClass("icon-enabled").addClass("wpl_actions_icon_enable");
        wplj("#wpl_flex_locations_params_row" + param_id + " input[name='<?php echo $__prefix; ?>opt_params[" + param_id + "][enabled]']").val(1);
    }
}
</script>
<div class="fanc-body">
	<div class="fanc-row fanc-button-row-2">
        <span class="ajax-inline-save" id="wpl_dbst_modify_ajax_loader"></span>
		<input class="wpl-button button-1" type="button" onclick="save_dbst('<?php echo $__prefix; ?>', <?php echo $dbst_id; ?>);" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>" id="wpl_dbst_submit_button" />
	</div>
	<div class="col-wp">
		<div class="col-fanc-left" id="wpl_flex_general_options">
			<div class="fanc-row fanc-inline-title">
				<?php echo __('General Options', 'real-estate-listing-realtyna-wpl'); ?>
			</div>
			<?php
				/** include main file * */
				include _wpl_import('libraries.dbst_modify.main.main', true, true);
			?>
		</div>
		<div class="col-fanc-right" id="wpl_flex_specific_options">
			<div class="fanc-row fanc-inline-title">
				<?php echo __('Specific Options', 'real-estate-listing-realtyna-wpl'); ?>
			</div>
			<?php
				/** include specific file * */
				include _wpl_import('libraries.dbst_modify.main.'.($kind == 2 ? 'user' : '').'specific', true, true);
			?>
            <div class="fanc-row fanc-inline-title">
				<span>
					<?php echo __('Params', 'real-estate-listing-realtyna-wpl'); ?>
				</span>
			</div>
			<div class="fanc-row">
                <p><?php _e('You can disable / enable locations in listing details page.', 'real-estate-listing-realtyna-wpl'); ?></p>
                <div class="wpl-flex-locations-sortable">
                    <?php
                    $location_settings = wpl_global::get_settings('3'); # location settings
                    $option_params = (isset($options['params']) and is_array($options['params'])) ? $options['params'] : array();

                    foreach($option_params as $k => $v)
                    {
                        $keyword = $location_settings['location' . $k . '_keyword'];
                        if(trim($keyword) == '') continue;
                        ?>
                        <div class="fanc-row" id="wpl_flex_locations_params_row<?php echo $k; ?>">
                            <span style="width: 200px; display: inline-block;"><?php echo __($keyword, 'real-estate-listing-realtyna-wpl'); ?></span>
                            <span class="margin-left-1p action-btn icon-<?php echo((isset($v['enabled']) and $v['enabled']) ? 'enabled wpl_actions_icon_enable' : 'disabled'); ?>"
                                  id="wpl_flex_change_param_status<?php echo $k; ?>"
                                  onclick="wpl_flex_disable_location(<?php echo $k; ?>);"></span>
                            <span class="action-btn icon-move ui-sortable-handle"></span>
                            <input type="hidden" id="<?php echo $__prefix; ?>opt_params[<?php echo $k; ?>][enabled]"
                                   name="<?php echo $__prefix; ?>opt_params[<?php echo $k; ?>][enabled]"
                                   value="<?php echo ((isset($v['enabled']) and $v['enabled'])) ? $v['enabled'] : 0; ?>"/>
                        </div>
                        <?php
                    }
                    ?>
                </div>
			</div>
		</div>
	</div>
    <div class="col-wp">
        <div class="col-fanc-left">
        	<div class="fanc-row fanc-inline-title">
                <?php echo __('Accesses', 'real-estate-listing-realtyna-wpl'); ?>
            </div>
            <?php
				/** include accesses file **/
				include _wpl_import('libraries.dbst_modify.main.accesses', true, true);
            ?>
        </div>
    </div>
</div>
<?php
    $done_this = true;
}