<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
$this->_wpl_import($this->tpl_path . '.scripts.internal_unit_replace_js');
?>
<div class="fanc-content size-width-2">
	<h2><?php _e('Replace Unit', 'real-estate-listing-realtyna-wpl'); ?></h2>
	<div class="fanc-body">
		<div class="fanc-row fanc-button-row-2">
			<span class="ajax-inline-save" id="wpl_replaced_unit_ajax_loader"></span>
			<input class="wpl-button button-1" type="button" value="Save" id="wpl_replaced_unit_submit_button" onclick="wpl_save_replaced_unit();">
		</div>
		<div class="col-wp">
			<div class="col-fanc-bottom wpl-fanc-full-row">
				<div class="fanc-row fanc-inline-title">
					<?php _e('Replace current unit with another active unit in listings', 'real-estate-listing-realtyna-wpl'); ?>
				</div>
				<div class="fanc-row">
					<label for="wpl_replaced_unit"><?php _e('Set new unit type', 'real-estate-listing-realtyna-wpl'); ?>:</label>
					<select data-old-unit="<?php esc_attr_e($this->unit_id); ?>" data-type="<?php esc_attr_e($this->unit_type); ?>" class="wpl_unit_selectbox" id="wpl_replaced_unit">
						<?php foreach($this->units as $unit): if($this->unit_id == $unit['id']) continue; ?>
                            <?php echo '<option value="'.$unit['id'].'">'.$unit['name'].'</option>'; ?>
						<?php endforeach; ?>
					</select>
				</div>
			</div>
		</div>
    </div>
</div>