<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="fanc-content size-width-1">
    <h2><?php echo ($this->location_id ? __('Edit location', 'real-estate-listing-realtyna-wpl') : __('Add location', 'real-estate-listing-realtyna-wpl')); ?></h2>
    <div class="fanc-body">
        <div class="fanc-row">
            <label for="wpl_location_name"><?php echo __('Name', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input class="text_box" type="text" id="wpl_location_name" value="<?php echo wpl_global::isset_object('name', $this->location_data); ?>" autocomplete="off" />
        </div>
        <?php if($this->level != 'zips'): ?>
        <div class="fanc-row">
            <label for="wpl_location_abbr"><?php echo __('Abbreviation', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input class="text_box" type="text" id="wpl_location_abbr" value="<?php echo wpl_global::isset_object('abbr', $this->location_data); ?>" autocomplete="off" />
        </div>
        <?php endif; ?>
        <div class="fanc-row fanc-button-row">
            <?php if ($this->location_id): ?>
                <input type="hidden" id="wpl_location_id" value="<?php $this->location_id; ?>" />
                <input class="wpl-button button-1" type="submit" id="wpl_submit" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>" onclick="wpl_ajax_modify_location('<?php echo $this->level; ?>', '', '<?php echo $this->location_id; ?>');" />
            <?php else: ?>
                <input class="wpl-button button-1" type="submit" id="wpl_submit" value="<?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?>" onclick="wpl_ajax_modify_location('<?php echo $this->level; ?>', '<?php echo $this->parent; ?>');" />
            <?php endif; ?>
            <span class="ajax-inline-save" id="wpl_ajax_loader"></span>
        </div>
        <div class="wpl_show_message_location"></div>
    </div>
</div>