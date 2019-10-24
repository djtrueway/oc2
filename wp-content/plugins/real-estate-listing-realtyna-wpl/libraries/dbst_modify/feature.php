<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

if($type == 'feature' and !$done_this)
{
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
	wpl_flex_feature_sortable();
});

function wpl_flex_feature_sortable()
{
	wplj(".wpl-flex-feature-sortable").sortable(
			{
				handle: 'span.icon-move',
				cursor: "move" ,
				update : function(e, ui)
				{
				}
			});
}

function wpl_flex_add_feature_param()
{
	var html = '<div class="fanc-row" id="wpl_flex_select_params_row' + wpl_flex_param_counter + '">' +
            '<input type="hidden" id="<?php echo $__prefix; ?>opt_values[' + wpl_flex_param_counter + '][key]" name="<?php echo $__prefix; ?>opt_values[' + wpl_flex_param_counter + '][key]" value="' + wpl_flex_param_counter + '" />' +
            '<label for="<?php echo $__prefix; ?>opt_values[' + wpl_flex_param_counter + '][value]"><?php echo __('Option', 'real-estate-listing-realtyna-wpl'); ?> '+wpl_flex_param_counter+'</label>' +
			'<input type="text" id="<?php echo $__prefix; ?>opt_values[' + wpl_flex_param_counter + '][value]" name="<?php echo $__prefix; ?>opt_values[' + wpl_flex_param_counter + '][value]" value="" />';
	wplj("#wpl_flex_feature_before").before(html);

	wpl_flex_param_counter++;
	wpl_flex_select_sortable();
}

function wpl_flex_disable_param(param_id)
{
	if (wplj("#wpl_felx_change_param_status" + param_id).hasClass("wpl_actions_icon_enable"))
	{
		wplj("#wpl_flex_select_params_row" + param_id + " input[type='text']").attr("disabled", "disabled");
		wplj("#wpl_felx_change_param_status" + param_id).removeClass("wpl_actions_icon_enable");
		wplj("#wpl_felx_change_param_status" + param_id).removeClass("icon-enabled");
		wplj("#wpl_felx_change_param_status" + param_id).addClass("wpl_actions_icon_disable");
		wplj("#wpl_felx_change_param_status" + param_id).addClass("icon-disabled");
		wplj("#wpl_flex_select_params_row" + param_id + " input[name='<?php echo $__prefix; ?>opt_values[" + param_id +
				"][enabled]']").val(0);
	}
	else
	{
		wplj("#wpl_flex_select_params_row" + param_id + " input[type='text']").removeAttr("disabled");
		wplj("#wpl_felx_change_param_status" + param_id).removeClass("wpl_actions_icon_disable");
		wplj("#wpl_felx_change_param_status" + param_id).removeClass("icon-disabled");
		wplj("#wpl_felx_change_param_status" + param_id).addClass("icon-enabled");
		wplj("#wpl_felx_change_param_status" + param_id).addClass("wpl_actions_icon_enable");
		wplj("#wpl_flex_select_params_row" + param_id + " input[name='<?php echo $__prefix; ?>opt_values[" + param_id + "][enabled]']").val(1);
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
				/** include main file **/
				include _wpl_import('libraries.dbst_modify.main.main', true, true);
			?>
		</div>
		<div class="col-fanc-right" id="wpl_flex_specific_options">
            <div class="fanc-row fanc-inline-title">
				<?php echo __('Specific Options', 'real-estate-listing-realtyna-wpl'); ?>
			</div>
			<?php
				/** include specific file **/
				include _wpl_import('libraries.dbst_modify.main.'.($kind == 2 ? 'user' : '').'specific', true, true);
			?>
            <div class="fanc-row fanc-inline-title">
				<span><?php echo __('Params', 'real-estate-listing-realtyna-wpl'); ?></span>
                <span class="action-btn icon-plus margin-left-1p" onclick="wpl_flex_add_feature_param();"></span>
			</div>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_type"><?php echo __('Type', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select name="<?php echo $__prefix; ?>opt_type" id="<?php echo $__prefix; ?>opt_type">
                    <option value="none" <?php echo ((isset($options['type']) and $options['type'] == 'none') ? 'selected="selected"' : ''); ?>><?php echo __('None', 'real-estate-listing-realtyna-wpl'); ?></option>
                    <option value="single" <?php echo ((isset($options['type']) and $options['type'] == 'single') ? 'selected="selected"' : ''); ?>><?php echo __('Single', 'real-estate-listing-realtyna-wpl'); ?></option>
                    <option value="multiple" <?php echo ((isset($options['type']) and $options['type'] == 'multiple') ? 'selected="selected"' : ''); ?>><?php echo __('Multiple', 'real-estate-listing-realtyna-wpl'); ?></option>
                </select>
			</div>
			<div class="wpl-flex-feature-sortable">
            <?php
			$i = 1;
			$option_params = (isset($options['values']) and is_array($options['values'])) ? $options['values'] : array();
			foreach($option_params as $k => $v):
			?>
            <div class="fanc-row" id="wpl_flex_select_params_row<?php echo $i; ?>">
                <input type="hidden" id="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][key]" name="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][key]" value="<?php echo ((isset($v['key']) and trim($v['key']) != '') ? $v['key'] : $i); ?>" />
                <label for="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][value]"><?php echo __('Option', 'real-estate-listing-realtyna-wpl').' '.$i; ?></label>
                <input type="text" id="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][value]" name="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][value]" value="<?php echo stripslashes($v['value']); ?>" />

	            <span class="action-btn icon-move ui-sortable-handle"></span>
	            <span class="margin-left-1p action-btn icon-<?php echo ($v['enabled'] ? 'enabled wpl_actions_icon_enable' : 'disabled'); ?>" id="wpl_felx_change_param_status<?php echo $i; ?>" onclick="wpl_flex_disable_param(<?php echo $i; ?>);"></span>
	            <input type="hidden" id="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][sort]" name="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][sort]" value="<?php echo $v['sort']; ?>" />
	            <input type="hidden" id="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][enabled]" name="<?php echo $__prefix; ?>opt_values[<?php echo $i; ?>][enabled]" value="<?php echo $v['enabled']; ?>" />
            </div>
			<?php $i++; endforeach; ?>
			<div id="wpl_flex_feature_before"></div>
			<script type="text/javascript">
			var wpl_flex_param_counter = <?php echo $i; ?>;
			</script>
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