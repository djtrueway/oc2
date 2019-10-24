<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.images');

$image_width = isset($image_width) ? $image_width : 180;
$image_height = isset($image_height) ? $image_height : 125;

/*Agent and office name for mls compliance*/
$show_agent_name = wpl_global::get_setting('show_agent_name');
$show_office_name = wpl_global::get_setting('show_listing_brokerage');

foreach($this->wpl_properties as $key=>$property)
{
	$property_id = $property['data']['id'];

    $kind = $property['data']['kind'];
    $locations	 = $property['location_text'];
    
    // Get blog ID of property
    $blog_id = wpl_property::get_blog_id($property_id);

	$room       = isset($property['materials']['bedrooms']) ? '<div class="bedroom">'.$property['materials']['bedrooms']['value'].'<span class="name">'.__("Bedroom(s)", 'real-estate-listing-realtyna-wpl').'</span></div>' : '';
    if((!isset($property['materials']['bedrooms']) or (isset($property['materials']['bedrooms']) and $property['materials']['bedrooms']['value'] == 0)) and (isset($property['materials']['rooms']) and $property['materials']['rooms']['value'] != 0)) $room = '<div class="room">'.$property['materials']['rooms']['value'].'<span class="name">'.__("Room(s)", 'real-estate-listing-realtyna-wpl').'</span></div>';
    
    $bathroom   = isset($property['materials']['bathrooms']) ? '<div class="bathroom">'.$property['materials']['bathrooms']['value'].'<span class="name">'.__("Bathroom(s)", 'real-estate-listing-realtyna-wpl').'</span></div>' : '';

	$parking_number = isset($property['materials']['f_150']) ? $property['materials']['f_150']['values'][0] : NULL;
    $parking    = (isset($property['raw']['f_150']) and trim($property['raw']['f_150_options'])) ? '<div class="parking">'.$parking_number.'</div>' : '';

    $pic_count  = '<div class="pic_count">'.$property['raw']['pic_numb'].'</div>';
    $price 		= '<div class="price">'.$property['materials']['price']['value'].'</div>';

    $office_name = '';
    $agent_name = '';
    if(wpl_global::check_addon('MLS') and ($show_agent_name or $show_office_name))
    {
        $office_name = isset($property['raw']['field_2111']) ? '<div class="wpl-prp-office-name">'.$property['raw']['field_2111'].'</div>' : '';
        $agent_name = isset($property['raw']['field_2112']) ? '<div class="wpl-prp-agent-name">'.$property['raw']['field_2112'].'</div>' : '';
    }
?>
	<div id="main_infowindow">
		<div class="main_infowindow_l">
		<?php
            if(isset($property['items']['gallery']))
			{
				$i = 0;
                $images_total = count($property['items']['gallery']);
                $property_path = wpl_items::get_path($property_id, $kind, $blog_id);

                $image = $property['items']['gallery'][0];
                $params = array();
                $params['image_name'] = $image->item_name;
                $params['image_parentid'] = $image->parent_id;
                $params['image_parentkind'] = $image->parent_kind;
                $params['image_source'] = $property_path.$image->item_name;

                if(isset($image->item_cat) and $image->item_cat != 'external') $image_url = wpl_images::create_gallery_image($image_width, $image_height, $params);
                else $image_url = $image->item_extra3;

                echo '<a href="'.$property['property_link'].'"><img itemprop="image" id="wpl_gallery_image'.$property_id .'_'.$i.'" src="'.$image_url.'" class="wpl_gallery_image" width="'.$image_width.'" height="'.$image_height.'" style="width: '.$image_width.'px; height: '.$image_height.'px;" /></a>';
                $i++;
			}
			else
			{
				echo '<a href="'.$property['property_link'].'"><div class="no_image_box"></div></a>';
			}

			?>
		</div>
		<div class="main_infowindow_r">
			<div class="main_infowindow_r_t">
				<?php echo '<a itemprop="url" class="main_infowindow_title" href="'.$property['property_link'].'">'.$property['property_title'].'</a>'; ?>
				<div class="main_infowindow_location" itemprop="address" ><?php echo $locations; ?></div>
			    <?php
                    if($show_agent_name) echo $agent_name;
                    if($show_office_name) echo $office_name;
			    ?>
            </div>
			<div class="main_infowindow_r_b">
				<?php echo $room.$bathroom.$parking.$pic_count.$price; ?>
			</div>
		</div>
	</div>
<?php } ?>