<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="fanc-row">
    <label for="<?php echo $__prefix; ?>category"><?php echo __('Data category', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select id="<?php echo $__prefix; ?>category" name="<?php echo $__prefix; ?>data_category">
        <?php foreach ($dbcats as $dbcat): ?>
        <option value="<?php echo $dbcat->id; ?>" <?php if (isset($values->category) and $dbcat->id == $values->category) echo 'selected="selected"'; ?>><?php echo $dbcat->name; ?></option>
        <?php endforeach; ?>
    </select>
    <!-- hidden fields -->
    <input type="hidden" name="<?php echo $__prefix; ?>type" id="<?php echo $__prefix; ?>type" value="<?php echo $type; ?>" />
    <input type="hidden" name="<?php echo $__prefix; ?>kind" id="<?php echo $__prefix; ?>kind" value="<?php echo $kind; ?>" />
    <input type="hidden" name="<?php echo $__prefix; ?>table_name" id="<?php echo $__prefix; ?>table_name" value="<?php echo wpl_flex::get_kind_table($kind); ?>" />
</div>
<div class="fanc-row">
    <label for="<?php echo $__prefix; ?>name"><?php echo __('Name', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input type="text" name="<?php echo $__prefix; ?>name" id="<?php echo $__prefix; ?>name" value="<?php echo (isset($values->name) ? $values->name : ''); ?>" />
</div>
<div class="fanc-row">
    <label for="<?php echo $__prefix; ?>text_search"><?php echo __('Text Search', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select name="<?php echo $__prefix; ?>text_search" id="<?php echo $__prefix; ?>text_search">
        <option value="1" <?php if (isset($values->text_search) and $values->text_search == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if (isset($values->text_search) and $values->text_search == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<?php if(wpl_global::check_multilingual_status() and (in_array($type, array('text', 'textarea', 'meta_key', 'meta_desc')) or (isset($values->type) and in_array($values->type, array('text', 'textarea', 'meta_key', 'meta_desc'))))): ?>
<div class="fanc-row">
    <label for="<?php echo $__prefix; ?>multilingual"><?php echo __('Multilingual', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select name="<?php echo $__prefix; ?>multilingual" id="<?php echo $__prefix; ?>multilingual">
        <option value="0" <?php if (isset($values->multilingual) and $values->multilingual == '0') echo 'selected="selected"'; ?>><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1" <?php if (isset($values->multilingual) and $values->multilingual == '1') echo 'selected="selected"'; ?>><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<?php endif; ?>
<div class="fanc-row">
    <label for="<?php echo $__prefix; ?>pshow"><?php echo __('Detail Page', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select name="<?php echo $__prefix; ?>pshow" id="<?php echo $__prefix; ?>pshow">
        <option value="1" <?php if (isset($values->pshow) and $values->pshow == '1') echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if (isset($values->pshow) and $values->pshow == '0') echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<div class="fanc-row">
    <label for="<?php echo $__prefix; ?>searchmod"><?php echo __('Search Widget', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select name="<?php echo $__prefix; ?>searchmod" id="<?php echo $__prefix; ?>searchmod">
        <option value="1" <?php if (isset($values->searchmod) and $values->searchmod == '1') echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if (isset($values->searchmod) and $values->searchmod == '0') echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<?php if(wpl_global::check_addon('pro')): ?>
<div class="fanc-row">
    <label for="<?php echo $__prefix; ?>pdf"><?php echo __('PDF Flyer', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select name="<?php echo $__prefix; ?>pdf" id="<?php echo $__prefix; ?>pdf">
        <option value="1" <?php if (isset($values->pdf) and $values->pdf == '1') echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="0" <?php if (isset($values->pdf) and $values->pdf == '0') echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<?php endif; ?>

<?php if(wpl_global::is_multisite() and wpl_users::is_super_admin()): ?>
<div class="fanc-row" id="multisite_modify_status_container">
    <label for="multisite_modify_status"><?php echo __('Network Apply', 'real-estate-listing-realtyna-wpl'); ?></label>
    <select name="multisite_modify_status" id="multisite_modify_status">
        <option value="0"><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></option>
        <option value="1"><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></option>
    </select>
</div>
<?php endif; ?>

<?php if(wpl_global::check_addon('pro') and isset($values->comparable) and intval($values->comparable)): ?>
    <div class="fanc-row">
        <label for="<?php echo $__prefix; ?>compare_visible"><?php echo __('Compare', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select onchange="wpl_flex_compare_change(this)" name="<?php echo $__prefix; ?>compare_visible" id="<?php echo $__prefix; ?>compare_visible">
            <option value="1" <?php if(isset($values->comparable) and intval($values->compare_visible)) echo 'selected="selected"'; ?>><?php echo __('Show', 'real-estate-listing-realtyna-wpl'); ?></option>
            <option value="0" <?php if(isset($values->comparable) and !intval($values->compare_visible)) echo 'selected="selected"'; ?>><?php echo __('Hide', 'real-estate-listing-realtyna-wpl'); ?></option>
        </select>
    </div>
    <?php if(isset($values->comparable_row) && intval($values->comparable_row)): ?>
        <div class="fanc-row wpl-compare-row-container <?php if (isset($values->comparable) and !intval($values->compare_visible)) echo 'hide'; ?>">
            <label for="<?php echo $__prefix; ?>compare_row"><?php echo __('Compare row', 'real-estate-listing-realtyna-wpl'); ?></label>
            <select name="<?php echo $__prefix; ?>compare_row" id="<?php echo $__prefix; ?>compare_row">
                <option value="0" <?php if (isset($values->compare_row) and intval($values->compare_row) == 0) echo 'selected="selected"'; ?>><?php echo __('Disabled', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="1" <?php if (isset($values->compare_row) and intval($values->compare_row) == 1) echo 'selected="selected"'; ?>><?php echo __('Higher is better', 'real-estate-listing-realtyna-wpl'); ?></option>
                <option value="2" <?php if (isset($values->compare_row) and intval($values->compare_row) == 2) echo 'selected="selected"'; ?>><?php echo __('Lower is better', 'real-estate-listing-realtyna-wpl'); ?></option>
            </select>
        </div>
    <?php endif; ?>
<?php endif; ?>
<?php if(isset($values->id)): ?>
<div class="fanc-row">
    <label><?php echo __('Field ID', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input type="text" disabled="disabled" value="<?php echo (isset($values->id) ? $values->id : ''); ?>" placeholder="<?php echo __('Field ID', 'real-estate-listing-realtyna-wpl'); ?>" />
</div>
<div class="fanc-row">
    <label><?php echo __('Column Name', 'real-estate-listing-realtyna-wpl'); ?></label>
    <input type="text" disabled="disabled" value="<?php echo (isset($values->table_column) ? $values->table_column : ''); ?>" placeholder="<?php echo __('Column Name', 'real-estate-listing-realtyna-wpl'); ?>" />
</div>
<?php endif; ?>
