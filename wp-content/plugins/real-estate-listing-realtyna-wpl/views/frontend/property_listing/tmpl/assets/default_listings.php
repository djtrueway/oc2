<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$description_column = 'field_308';
if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($description_column, $this->kind)) $description_column = wpl_addon_pro::get_column_lang_name($description_column, wpl_global::get_current_language(), false);

// Membership ID of current user
$current_user_membership_id = wpl_users::get_user_membership();

// Favorites
if(wpl_global::check_addon('PRO') and $this->favorite_btn) $favorites = wpl_addon_pro::favorite_get_pids();

foreach($this->wpl_properties as $key=>$property)
{
    if($key == 'current') continue;

    /** unset previous property **/
    unset($this->wpl_properties['current']);

    /** set current property **/
    $this->wpl_properties['current'] = $property;

    if(isset($property['materials']['bedrooms']['value']) and trim($property['materials']['bedrooms']['value'])) $room = sprintf('<div class="bedroom"><span class="value">%s</span><span class="name">%s</span></div>', $property['materials']['bedrooms']['value'], __("Bedroom(s)", 'real-estate-listing-realtyna-wpl'));
	elseif(isset($property['materials']['rooms']['value']) and trim($property['materials']['rooms']['value'])) $room = sprintf('<div class="room"><span class="value">%s</span><span class="name">%s</span></div>', $property['materials']['rooms']['value'], __("Room(s)", 'real-estate-listing-realtyna-wpl'));
	else $room = '';

	$bathroom = (isset($property['materials']['bathrooms']['value']) and trim($property['materials']['bathrooms']['value'])) ? sprintf('<div class="bathroom"><span class="value">%s</span><span class="name">%s</span></div>', $property['materials']['bathrooms']['value'], __("Bathroom(s)", 'real-estate-listing-realtyna-wpl')) : '';
	$parking = (isset($property['materials']['f_150']['values'][0]) and trim($property['materials']['f_150']['values'][0])) ? sprintf('<div class="parking"><span class="value">%s</span><span class="name">%s</span></div>', $property['materials']['f_150']['values'][0], __("Parking(s)", 'real-estate-listing-realtyna-wpl')) : '';
	$pic_count = (isset($property['raw']['pic_numb']) and trim($property['raw']['pic_numb'])) ? sprintf('<div class="pic_count"><span class="value">%s</span><span class="name">%s</span></div>', $property['raw']['pic_numb'], __("Picture(s)", 'real-estate-listing-realtyna-wpl')) : '';

	$living_area = isset($property['materials']['living_area']['value']) ? explode(' ', $property['materials']['living_area']['value']) : (isset($property['materials']['lot_area']['value']) ? explode(' ', $property['materials']['lot_area']['value']): array());
	$living_area_count = count($living_area);

	$build_up_area = $living_area_count ? '<div class="built_up_area">'.(isset($living_area[0]) ? implode(' ', array_slice($living_area, 0, $living_area_count-1)) : '').'<span>'.$living_area[$living_area_count-1].'</span></div>' : '';
	$property_price = isset($property['materials']['price']['value']) ? $property['materials']['price']['value'] : '&nbsp;';
    
    $description = stripslashes(strip_tags($property['raw'][$description_column]));
    $cut_position = strrpos(substr($description, 0, 400), '.', -1);
    if(!$cut_position) $cut_position = 399;

	$property_id = $property['data']['id'];

    $office_name = $agent_name = '';
    if(wpl_global::check_addon('MLS') && $this->show_agent_name || $this->show_office_name )
    {
        $office_name = isset($property['raw']['field_2111']) ? '<div class="wpl-prp-office-name"><label>'.$this->label_office_name.'</label> <span>'.$property['raw']['field_2111'].'</span></div>' : '';
        $agent_name = isset($property['raw']['field_2112']) ? '<div class="wpl-prp-agent-name"> <label>'.$this->label_agent_name.'</label> <span>'.$property['raw']['field_2112'].'</span></div>' : '';
    }
	?>
	<div class="wpl-column">
		<div class="wpl_prp_cont wpl_prp_cont_old
			<?php echo ((isset($this->property_css_class) and in_array($this->property_css_class, array('row_box', 'grid_box'))) ? $this->property_css_class : ''); ?>"
				  id="wpl_prp_cont<?php echo $property['data']['id']; ?>"
				  <?php	echo $this->itemscope.' '.$this->itemtype_SingleFamilyResidence; ?> >
			<div class="wpl_prp_top">
				<div class="wpl_prp_top_boxes front">
					<?php wpl_activity::load_position('wpl_property_listing_image', array('wpl_properties'=>$this->wpl_properties)); ?>
				</div>
				<div class="wpl_prp_top_boxes back">
					<a <?php echo $this->itemprop_url;?> id="prp_link_id_<?php echo $property['data']['id']; ?>" href="<?php echo $property['property_link']; ?>" class="view_detail"><?php echo __('More Details', 'real-estate-listing-realtyna-wpl'); ?></a>
				</div>
			</div>
			<div class="wpl_prp_bot">

				<a <?php echo 'id="prp_link_id_'.$property['data']['id'].'_view_detail" href="'.$property['property_link'].'" class="view_detail" title="'.$property['property_title'].'"'; ?>>
				  <h3 class="wpl_prp_title"	<?php echo $this->itemprop_name; ?> > <?php echo $property['property_title'] ?></h3>
				</a>

                <?php $location_visibility = wpl_property::location_visibility($property['data']['id'], $property['data']['kind'], $current_user_membership_id); ?>
				<h4 class="wpl_prp_listing_location"><span <?php echo $this->itemprop_address.''.$this->itemscope.' '.$this->itemtype_PostalAddress;?> ><span <?php echo $this->itemprop_addressLocality; ?>><?php echo ($location_visibility === true ? $property['location_text'] : $location_visibility);?></span></span></h4>
                <?php if(wpl_global::check_addon('MLS') && $this->show_agent_name || $this->show_office_name): ?>
                    <div class="wpl-mls-brokerage-info">
                        <?php if($this->show_agent_name) echo $agent_name; ?>
                        <?php if($this->show_office_name) echo $office_name; ?>
                    </div>
                <?php endif; ?>
				<div class="wpl_prp_listing_icon_box">
                    <?php echo $room . $bathroom . $parking . $pic_count . $build_up_area; ?>
                    <?php if(wpl_global::get_setting('show_plisting_visits')): ?>
					<div class="visits_box">
						<span class="name"><?php echo __('Visits', 'real-estate-listing-realtyna-wpl'); ?>:</span><span class="value"><?php echo $property['data']['visit_time']; ?></span>
					</div>
                    <?php endif; ?>
				</div>
				<div class="wpl_prp_desc" <?php echo $this->itemprop_description; ?>><?php echo substr($description, 0, $cut_position + 1); ?></div>
			</div>
			<div class="price_box" <?php echo $this->itemscope.' '.$this->itemtype_offer; ?>>
				<span <?php echo $this->itemprop_price; ?>><?php echo $property_price; ?></span>
			</div>

			<?php if(wpl_global::check_addon('PRO') and $this->favorite_btn): ?>
				<div class="wpl_prp_listing_like">
					<div class="wpl_listing_links_container">
						<ul>
							<?php $find_favorite_item = in_array($property_id, $favorites); ?>
							<li class="favorite_link<?php echo ($find_favorite_item ? ' added' : '') ?>">
								<a href="#" style="<?php echo ($find_favorite_item ? 'display: none;' : '') ?>" id="wpl_favorite_add_<?php echo $property_id; ?>" onclick="return wpl_favorite_control('<?php echo $property_id; ?>', 1);" title="<?php echo __('Add to favorites', 'real-estate-listing-realtyna-wpl'); ?>"></a>
								<a href="#" style="<?php echo (!$find_favorite_item ? 'display: none;' : '') ?>" id="wpl_favorite_remove_<?php echo $property_id; ?>" onclick="return wpl_favorite_control('<?php echo $property_id; ?>', 0);" title="<?php echo __('Remove from favorites', 'real-estate-listing-realtyna-wpl'); ?>"></a>
							</li>
						</ul>
					</div>
				</div>
			<?php endif; ?>
		</div>
	</div>
    <?php
}