<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

if($type == 'tag' and !$done_this)
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
            <?php if(wpl_global::check_addon('tags')): $tag_categories = json_decode(wpl_global::get_setting('tags_categories'), true); ?>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_category"><?php echo __('Category', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select name="<?php echo $__prefix; ?>opt_category" id="<?php echo $__prefix; ?>opt_category">
                    <option value="">----</option>
                    <?php foreach($tag_categories as $tag_category): ?>
                    <option value="<?php echo $tag_category; ?>" <?php echo ((isset($options['category']) and $options['category'] == $tag_category) ? 'selected="selected"' : ''); ?>><?php echo __($tag_category, 'real-estate-listing-realtyna-wpl'); ?></option>
                    <?php endforeach; ?>
                </select>
			</div>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_ribbon"><?php echo __('Ribbon', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select name="<?php echo $__prefix; ?>opt_ribbon" id="<?php echo $__prefix; ?>opt_ribbon">
                    <option value="1" <?php echo ((isset($options['ribbon']) and $options['ribbon'] == 1) ? 'selected="selected"' : ''); ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
                    <option value="0" <?php echo ((isset($options['ribbon']) and $options['ribbon'] == 0) ? 'selected="selected"' : ''); ?>><?php echo __('hide', 'real-estate-listing-realtyna-wpl'); ?></option>
                </select>
			</div>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_widget"><?php echo __('Tags Widget', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select name="<?php echo $__prefix; ?>opt_widget" id="<?php echo $__prefix; ?>opt_widget">
                    <option value="1" <?php echo ((isset($options['widget']) and $options['widget'] == 1) ? 'selected="selected"' : ''); ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
                    <option value="0" <?php echo ((isset($options['widget']) and $options['widget'] == 0) ? 'selected="selected"' : ''); ?>><?php echo __('hide', 'real-estate-listing-realtyna-wpl'); ?></option>
                </select>
			</div>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_color"><?php echo __('Background Color', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input class="wpl-flex-tag-field-color" type="text" name="<?php echo $__prefix; ?>opt_color" id="<?php echo $__prefix; ?>opt_color" value="<?php echo (isset($options['color']) ? $options['color'] : '29a9df'); ?>" />
			</div>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_text_color"><?php echo __('Text Color', 'real-estate-listing-realtyna-wpl'); ?></label>
                <input class="wpl-flex-tag-field-color" type="text" name="<?php echo $__prefix; ?>opt_text_color" id="<?php echo $__prefix; ?>opt_text_color" value="<?php echo (isset($options['text_color']) ? $options['text_color'] : 'ffffff'); ?>" />
			</div>
            <?php if(!$dbst_id): ?>
            <div class="fanc-row">
				<label for="<?php echo $__prefix; ?>opt_default_value"><?php echo __('Labeled by default', 'real-estate-listing-realtyna-wpl'); ?></label>
                <select name="<?php echo $__prefix; ?>opt_default_value" id="<?php echo $__prefix; ?>opt_default_value">
                    <option value="0" <?php echo ((isset($options['default_value']) and $options['default_value'] == 0) ? 'selected="selected"' : ''); ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
                    <option value="1" <?php echo ((isset($options['default_value']) and $options['default_value'] == 1) ? 'selected="selected"' : ''); ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
                </select>
			</div>
            <?php endif; ?>
            <?php else: ?>
            <div class="fanc-row">
                <span><?php echo __('The Tags Add-on must be installed for this feature!', 'real-estate-listing-realtyna-wpl'); ?></span>
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