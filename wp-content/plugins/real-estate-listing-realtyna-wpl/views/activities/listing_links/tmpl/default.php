<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$wpl_properties         = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();
$property_id            = isset($wpl_properties['current']['data']['id']) ? $wpl_properties['current']['data']['id'] : NULL;
$property_link          = urlencode($wpl_properties['current']['property_link']);

$show_facebook          = (isset($params['facebook']) and $params['facebook']) ? 1 : 0;
$show_twitter           = (isset($params['twitter']) and $params['twitter']) ? 1 : 0;
$show_pinterest         = (isset($params['pinterest']) and $params['pinterest']) ? 1 : 0;
$show_linkedin          = (isset($params['linkedin']) and $params['linkedin']) ? 1 : 0;
$show_favorite          = (isset($params['favorite']) and $params['favorite']) ? 1 : 0;
$show_pdf               = (isset($params['pdf']) and $params['pdf']) ? 1 : 0;
$show_abuse             = (isset($params['report_abuse']) and $params['report_abuse']) ? 1 : 0;
$show_crm               = (isset($params['crm']) and $params['crm']) ? 1 : 0;
$show_request_a_visit   = (isset($params['request_a_visit']) and $params['request_a_visit']) ? 1 : 0;
$show_send_to_friend    = (isset($params['send_to_friend']) and $params['send_to_friend']) ? 1 : 0;
$watch_changes          = (isset($params['watch_changes']) and $params['watch_changes']) ? 1 : 0;

$this->lightbox_container = '#wpl_plisting_lightbox_content_container';
$this->_wpl_import($this->tpl_path.'.scripts.js', true, true, true);
?>
<div class="wpl_listing_links_container" id="wpl_listing_links_container<?php echo $property_id; ?>">
	<ul>
        <?php if($show_facebook): ?>
		<li class="facebook_link">
			<a class="wpl-tooltip-top" href="https://www.facebook.com/sharer/sharer.php?u=<?php echo $property_link; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600'); return false;" title="<?php echo __('Share on Facebook', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Share on Facebook', 'real-estate-listing-realtyna-wpl'); ?></div>
		</li>
        <?php endif; ?>

        <?php if($show_twitter): ?>
		<li class="twitter_link">
			<a class="wpl-tooltip-top" href="https://twitter.com/share?url=<?php echo $property_link; ?>" target="_blank" title="<?php echo __('Tweet', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Share on Twitter', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
        <?php endif; ?>

        <?php if($show_pinterest): ?>
		<li class="pinterest_link">
			<a class="wpl-tooltip-top" href="http://pinterest.com/pin/create/link/?url=<?php echo $property_link; ?>&media=<?php echo wpl_property::get_property_image($property_id, '300*300'); ?>&description=<?php echo (isset($wpl_properties['current']['property_title']) ? $wpl_properties['current']['property_title'] : ''); ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600'); return false;" title="<?php echo __('Pin it', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Share on Pinterest', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
        <?php endif; ?>

        <?php if($show_linkedin): ?>
		<li class="linkedin_link">
			<a class="wpl-tooltip-top" href="https://www.linkedin.com/shareArticle?mini=true&url=<?php echo $property_link; ?>" onclick="javascript:window.open(this.href, '', 'menubar=no,toolbar=no,resizable=yes,scrollbars=yes,height=500,width=600'); return false;" title="<?php echo __('Share on Linkedin', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Share on Linkedin', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
        <?php endif; ?>

        <?php if($show_pdf): ?>
		<li class="pdf_link">
			<a class="wpl-tooltip-top"href="<?php echo wpl_property::get_property_pdf_link($property_id); ?>" target="_blank" title="<?php echo __('PDF', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('PDF', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
        <?php endif; ?>

        <?php if($show_favorite): $find_favorite_item = in_array($property_id, wpl_addon_pro::favorite_get_pids()); ?>
        <li class="favorite_link<?php echo ($find_favorite_item ? ' added' : '') ?>">
            <a class="wpl-tooltip-top" href="#" style="<?php echo ($find_favorite_item ? 'display: none;' : '') ?>" id="wpl_favorite_add_<?php echo $this->activity_id; ?>_<?php echo $property_id; ?>" onclick="return wpl_favorite_control<?php echo $this->activity_id; ?>(<?php echo $property_id; ?>, 1);" title="<?php echo __('Add to favorites', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Add to favorites', 'real-estate-listing-realtyna-wpl'); ?></div>
            <a class="wpl-tooltip-top" href="#" style="<?php echo (!$find_favorite_item ? 'display: none;' : '') ?>" id="wpl_favorite_remove_<?php echo $this->activity_id; ?>_<?php echo $property_id; ?>" onclick="return wpl_favorite_control<?php echo $this->activity_id; ?>(<?php echo $property_id; ?>, 0);" title="<?php echo __('Remove from favorites', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Remove from favorites', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
	    <?php endif; ?>

        <?php if($show_abuse): ?>
        <li class="report_abuse_link">
            <a class="wpl-tooltip-top" data-realtyna-lightbox data-realtyna-lightbox-opts="title:<?php echo __('Report Listing', 'real-estate-listing-realtyna-wpl'); ?>" href="<?php echo $this->lightbox_container; ?>" onclick="return wpl_report_abuse_get_form(<?php echo $property_id; ?>);" title="<?php echo __('Report Listing', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Report Listing', 'real-estate-listing-realtyna-wpl'); ?></div>
		</li>
        <?php endif; ?>

        <?php if($show_send_to_friend): ?>
        <li class="send_to_friend_link">
            <a class="wpl-tooltip-top" data-realtyna-lightbox data-realtyna-lightbox-opts="title:<?php echo __('Send to Friend', 'real-estate-listing-realtyna-wpl'); ?>" href="<?php echo $this->lightbox_container; ?>" onclick="return wpl_send_to_friend_get_form(<?php echo $property_id; ?>);" title="<?php echo __('Send to Friend', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Send to Friend', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
        <?php endif; ?>

        <?php if($show_request_a_visit): ?>
        <li class="request_a_visit_link">
            <a class="wpl-tooltip-top" data-realtyna-lightbox data-realtyna-lightbox-opts="title:<?php echo __('Request a Visit', 'real-estate-listing-realtyna-wpl'); ?>" href="<?php echo $this->lightbox_container; ?>" onclick="return wpl_request_a_visit_get_form(<?php echo $property_id; ?>);" title="<?php echo __('Request a Visit', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Request a Visit', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
        <?php endif; ?>

        <?php if(wpl_global::check_addon('save_searches') and $watch_changes): ?>
        <li class="watch_changes_link">
            <a class="wpl-tooltip-top" data-realtyna-lightbox data-realtyna-lightbox-opts="title:<?php echo __('Watch changes on this property', 'real-estate-listing-realtyna-wpl'); ?>" href="<?php echo $this->lightbox_container; ?>" onclick="return wpl_watch_changes_get_form(<?php echo $property_id; ?>);" title="<?php echo __('Watch changes on this property', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Watch changes on this property', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
        <?php endif; ?>

        <?php if($show_crm): _wpl_import('libraries.addon_crm'); $crm = new wpl_addon_crm(); ?>
        <li class="crm_link">
            <a class="wpl-tooltip-top" href="<?php echo $crm->URL('form'); ?>&pid=<?php echo $property_id; ?>" target="_blank" title="<?php echo __('Send Request for a new Property', 'real-estate-listing-realtyna-wpl'); ?>"></a>
            <div class="wpl-util-hidden"><?php echo __('Send Request for a new Property', 'real-estate-listing-realtyna-wpl'); ?></div>
        </li>
        <?php endif; ?>
	</ul>
</div>