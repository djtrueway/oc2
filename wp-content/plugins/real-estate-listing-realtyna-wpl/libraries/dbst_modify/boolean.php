<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

if($type == 'boolean' and !$done_this)
{
?>
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
				/** include specific file **/
				include _wpl_import('libraries.dbst_modify.main.'.($kind == 2 ? 'user' : '').'specific', true, true);
			?>
            <div class="fanc-row fanc-inline-title">
				<span>
					<?php echo __('Params', 'real-estate-listing-realtyna-wpl'); ?>
				</span>
			</div>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_true_label"><?php echo __('True Label', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input type="text" name="<?php echo $__prefix; ?>opt_true_label" id="<?php echo $__prefix; ?>opt_true_label" value="<?php echo (isset($options['true_label']) ? $options['true_label'] : 'Yes'); ?>" />
			</div>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_false_label"><?php echo __('False Label', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input type="text" name="<?php echo $__prefix; ?>opt_false_label" id="<?php echo $__prefix; ?>opt_false_label" value="<?php echo (isset($options['false_label']) ? $options['false_label'] : 'No'); ?>" />
			</div>
            <?php if(!$dbst_id): ?>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_default_value"><?php echo __('Default Value', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select name="<?php echo $__prefix; ?>opt_default_value" id="<?php echo $__prefix; ?>opt_default_value">
                    <option value="1" <?php echo ((isset($options['default_value']) and $options['default_value'] == 1) ? 'selected="selected"' : ''); ?>><?php echo __('True', 'real-estate-listing-realtyna-wpl'); ?></option>
                    <option value="0" <?php echo ((isset($options['default_value']) and $options['default_value'] == 0) ? 'selected="selected"' : ''); ?>><?php echo __('False', 'real-estate-listing-realtyna-wpl'); ?></option>
                </select>
			</div>
            <?php endif; ?>
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