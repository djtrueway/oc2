<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
$this->_wpl_import($this->tpl_path . '.scripts.internal_sort_options_js');
?>
<div>
    <table class="widefat page">
        <thead>
            <tr>
                <th scope="col" class="manage-column"><?php echo __('Name', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th scope="col" class="manage-column"><?php echo __('Ascending Label', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th scope="col" class="manage-column"><?php echo __('Descending Label', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th scope="col" class="manage-column"><?php echo __('Default Order', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th scope="col" class="manage-column"><?php echo __('Kinds', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th scope="col" class="manage-column"><?php echo __('Enabled', 'real-estate-listing-realtyna-wpl'); ?></th>
                <th scope="col" class="manage-column"><?php echo __('Move', 'real-estate-listing-realtyna-wpl'); ?></th>
            </tr>
        </thead>
        <tbody class="sortable_sort_options">
            <?php foreach($this->sort_options as $option): ?>
                <tr id="items_row_<?php echo $option['id']; ?>">
                    <td>
                        <input type="text" value="<?php echo __($option['name'], 'real-estate-listing-realtyna-wpl'); ?>" id="wpl_sort_option_name<?php echo $option['id']; ?>" onchange="wpl_save_sort_option(<?php echo $option['id']; ?>, 'name', this.value);" />
                        <span id="wpl_sort_option_ajax_loader<?php echo $option['id']; ?>"></span>
                    </td>
                    <td>
                        <input <?php if($option['asc_label'] === '0'): ?>disabled="disabled"<?php endif; ?> type="text" value="<?php echo $option['asc_label'] !== '0' ? __($option['asc_label'], 'real-estate-listing-realtyna-wpl') : __('Cannot Change!', 'real-estate-listing-realtyna-wpl'); ?>" id="wpl_sort_option_asc_label<?php echo $option['id']; ?>" onchange="wpl_save_sort_option(<?php echo $option['id']; ?>, 'asc_label', this.value);" />
                        <span class="action-btn <?php echo $option['asc_enabled'] == 1 ? "icon-enabled" : "icon-disabled"; ?>" onclick="wpl_sort_options_enabled_change(<?php echo $option['id']; ?>, 'asc_enabled');" id="wpl_ajax_asc_enabled_<?php echo $option['id']; ?>"></span>
                    </td>
                    <td>
                        <input <?php if($option['desc_label'] === '0'): ?>disabled="disabled"<?php endif; ?> type="text" value="<?php echo $option['desc_label'] !== '0' ? __($option['desc_label'], 'real-estate-listing-realtyna-wpl') : __('Cannot Change!', 'real-estate-listing-realtyna-wpl'); ?>" id="wpl_sort_option_desc_label<?php echo $option['id']; ?>" onchange="wpl_save_sort_option(<?php echo $option['id']; ?>, 'desc_label', this.value);" />
                        <span class="action-btn <?php echo $option['desc_enabled'] == 1 ? "icon-enabled" : "icon-disabled"; ?>" onclick="wpl_sort_options_enabled_change(<?php echo $option['id']; ?>, 'desc_enabled');" id="wpl_ajax_desc_enabled_<?php echo $option['id']; ?>"></span>
                    </td>
                    <td>
                        <select id="wpl_sort_option_default_order<?php echo $option['id']; ?>" onchange="wpl_save_sort_option(<?php echo $option['id']; ?>, 'default_order', this.value);" title="<?php echo __('Default Order', 'real-estate-listing-realtyna-wpl'); ?>">
                            <option value="DESC" <?php echo ($option['default_order'] == 'DESC' ? 'selected="selected"' : ''); ?>><?php echo __('High to Low', 'real-estate-listing-realtyna-wpl'); ?></option>
                            <option value="ASC" <?php echo ($option['default_order'] == 'ASC' ? 'selected="selected"' : ''); ?>><?php echo __('Low to High', 'real-estate-listing-realtyna-wpl'); ?></option>
                        </select>
                    </td>
                    <td class="manager-wp"><?php echo implode('/', $option['kind']); ?></td>
                    <td class="manager-wp wpl_sort_options_manager">
                        <span class="action-btn <?php echo $option['enabled'] == 1 ? "icon-enabled" : "icon-disabled"; ?>" onclick="wpl_sort_options_enabled_change(<?php echo $option['id']; ?>, 'enabled');" id="wpl_ajax_enabled_<?php echo $option['id']; ?>"></span>
                        <span class="wpl_ajax_loader" id="wpl_ajax_loader_options_<?php echo $option['id']; ?>"></span>
                    </td>
                    <td class="manager-wp">
                        <span class="action-btn icon-move" id="sort_move_<?php echo $option['id']; ?>"></span>
                    </td>
                </tr>
            <?php endforeach; ?>
        </tbody>
    </table>
    <p><?php _e('Disabling/Enabling feature for ascending and descending options is for dropdown sort option only. The dropdown sort normally shows in map view of WPL.', 'real-estate-listing-realtyna-wpl'); ?></p>
    <p><?php _e('Default order used for normal sort bar that appears in list or grid styles. "Low to High" means ascending order and "High to Low" means descending order.', 'real-estate-listing-realtyna-wpl'); ?></p>
</div>