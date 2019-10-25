<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

?>
<div class="fanc-row">
    <label for="wpl_contacts"><?php echo __('Contact', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input <?php if(isset($this->options->contacts) and $this->options->contacts == '1') echo 'checked="checked"'; ?> class="text_box" name="option[contacts]" type="checkbox" id="wpl_contacts" value="<?php echo isset($this->options->contacts) ? $this->options->contacts : '1'; ?>" />
</div>

<div class="fanc-row">
    <label for="wpl_including_in_listing"><?php echo __('Including in listing page', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input <?php if(isset($this->options->including_in_listing) and $this->options->including_in_listing == '1') echo 'checked="checked"'; ?> class="text_box" name="option[including_in_listing]" type="checkbox" id="wpl_including_in_listing" value="<?php echo isset($this->options->including_in_listing) ? $this->options->including_in_listing : '1'; ?>" />
</div>

<div class="fanc-row">
    <label for="wpl_view_parent"><?php echo __('View parent', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input <?php if(isset($this->options->view_parent) and $this->options->view_parent == '1') echo 'checked="checked"'; ?> class="text_box" name="option[view_parent]" type="checkbox" id="wpl_view_parent" value="<?php echo isset($this->options->view_parent) ? $this->options->view_parent : '1'; ?>" />
</div>

<div class="fanc-row">
    <label for="wpl_visit"><?php echo __('Visit', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input <?php if(isset($this->options->visit) and $this->options->visit == '1') echo 'checked="checked"'; ?> class="text_box" name="option[visit]" type="checkbox" id="wpl_visit" value="<?php echo isset($this->options->visit) ? $this->options->visit : '1'; ?>" />
</div>

