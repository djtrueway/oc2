<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import($this->tpl_path . '.scripts.css');

$property_data = isset($params['property_data']['data']) ? $params['property_data']['data'] : NULL;
$pid = isset($property_data['id']) ? $property_data['id'] : NULL;

$wpl_users = isset($params['wpl_users']) ? $params['wpl_users'] : wpl_users::get_wpl_users();

$source_blog_id = 1;

 if(wpl_global::check_addon("facebook") && get_option('wpl_addon_facebook_init_info') !== false && get_option( 'wpl_addon_facebook_catalog_id' ) !== false) {
   $fb_listings = get_option( 'wpl_facebook_addon_property_list' );

   $fb_listings = ( $fb_listings === false ) ? array() : $fb_listings;


   $is_stored = false;

   foreach ($fb_listings as $fb_data) {
        if ( $pid == $fb_data['home_listing_id'] ) {
           $is_stored = true;
           break;
        }
   }

   $property_data['confirmed_fb'] = ( $is_stored === false ) ? 1 : 0;

}



if(wpl_global::is_multisite()) $source_blog_id = wpl_property::get_property_field('source_blog_id', $pid);
?>

<div id="pmanager_action_div<?php echo $pid; ?>" class="p-actions-wp pmanager_actions">
    <?php if(wpl_users::check_access('change_user') and $source_blog_id == wpl_global::get_current_blog_id()): ?>
    <div id="pmanager_change_user<?php echo $pid; ?>" class="change-user-cnt-wp">
        <div class="change-user-wp">
            <label id="pmanager_change_user_label<?php echo $pid; ?>" for="pmanager_change_user_select<?php echo $pid; ?>"><?php echo __('User', 'real-estate-listing-realtyna-wpl'); ?>: </label>
            <select id="pmanager_change_user_select<?php echo $pid; ?>" data-has-chosen onchange="change_user(<?php echo $pid; ?>, this.value);">
                <?php foreach($wpl_users as $wpl_user): ?>
                <option value="<?php echo $wpl_user->ID; ?>" <?php if($wpl_user->ID == $property_data['user_id']) echo 'selected="selected"'; ?>>
                    <?php echo $wpl_user->user_login; ?>
                </option>
                <?php endforeach; ?>
            </select>
        </div>
    </div>
    <?php endif; ?>
    <?php if(wpl_users::check_access('confirm', $property_data['user_id'])): ?>
    <div id="pmanager_confirm<?php echo $pid; ?>" class="p-action-btn" onclick="confirm_property(<?php echo $pid; ?>);">
        <span><?php echo($property_data['confirmed'] == 1 ? __('Publish', 'real-estate-listing-realtyna-wpl') : __('Unpublish', 'real-estate-listing-realtyna-wpl')); ?></span>
        <i class="<?php echo($property_data['confirmed'] == 1 ? 'icon-confirm' : 'icon-unconfirm'); ?>"></i>
    </div>
    <?php endif; ?>
    <?php if(wpl_users::check_access('delete', $property_data['user_id'])): ?>
    <div id="pmanager_trash<?php echo $pid; ?>" class="p-action-btn" onclick="trash_property(<?php echo $pid; ?>);">
        <span><?php echo($property_data['deleted'] == 1 ? __('Restore', 'real-estate-listing-realtyna-wpl') : __('Trash', 'real-estate-listing-realtyna-wpl')); ?></span>
        <i class="<?php echo($property_data['deleted'] == 1 ? 'icon-restore' : 'icon-trash'); ?>"></i>
    </div>
    <div id="pmanager_delete<?php echo $pid; ?>" class="p-action-btn" onclick="purge_property(<?php echo $pid; ?>);">
        <span><?php echo __('Purge', 'real-estate-listing-realtyna-wpl'); ?></span>
        <i class="icon-delete"></i>
    </div>
    <?php endif; ?>
    <?php if(wpl_users::check_access('clone') and wpl_global::check_addon('pro')): ?>
    <div id="pmanager_clone<?php echo $pid; ?>" class="p-action-btn" onclick="clone_property(<?php echo $pid; ?>);">
        <span><?php echo __('Clone', 'real-estate-listing-realtyna-wpl'); ?></span>
        <i class="icon-clone"></i>
    </div>
    <?php endif; ?>
    <a id="pmanager_edit<?php echo $pid; ?>" class="p-action-btn" href="<?php echo wpl_property::get_property_edit_link($pid); ?>">
        <span><?php echo __('Edit', 'real-estate-listing-realtyna-wpl'); ?></span>
        <i class="icon-edit"></i>
    </a>
    <?php if(wpl_global::check_addon("facebook") && get_option('wpl_addon_facebook_init_info') !== false && get_option( 'wpl_addon_facebook_catalog_id' ) !== false): ?>
    <div id="pmanager_facebook_publish<?php echo $pid; ?>"  class="p-action-btn p-action-facebook-btn" onclick="facebook_publish(<?php echo $pid; ?>);">
        <label><?php echo($property_data['confirmed_fb'] == 1 ? __('Publish on FB', 'real-estate-listing-realtyna-wpl') : __('Unpublish From FB', 'real-estate-listing-realtyna-wpl')); ?></label>
        <i class="<?php echo($property_data['confirmed_fb'] == 1 ? 'icon-confirm' : 'icon-unconfirm'); ?>"></i>
    </div>
    <?php endif; ?>
    <?php if(wpl_users::check_access('multi_agents') and wpl_global::check_addon('multi_agents') and in_array($property_data['kind'], array(0,1)) and $source_blog_id == wpl_global::get_current_blog_id()): ?>
    <?php
        _wpl_import('libraries.addon_multi_agents');
        
        $multi = new wpl_addon_multi_agents($pid);
        $additional_agents = $multi->get_agents();
    ?>
    <div class="pmanager-multi-agent">
        <label id="pmanager_additional_agents_label<?php echo $pid; ?>" for="pmanager_additional_agents_select<?php echo $pid; ?>"><?php echo __('Additional Agents', 'real-estate-listing-realtyna-wpl'); ?>: </label>
        <select id="pmanager_additional_agents_select<?php echo $pid; ?>" data-has-chosen multiple="multiple" data-chosen-opt="width:100%" onchange="additional_agents(<?php echo $pid; ?>);">
            <?php foreach($wpl_users as $wpl_user): ?>
            <option value="<?php echo $wpl_user->ID; ?>" <?php if(in_array($wpl_user->ID, $additional_agents)) echo 'selected="selected"'; ?>><?php echo $wpl_user->user_login; ?></option>
            <?php endforeach; ?>
        </select>
    </div>
    <?php endif; ?>
</div>