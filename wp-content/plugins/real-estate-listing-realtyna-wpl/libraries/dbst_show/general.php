<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($type == 'number' and !$done_this) //////////////////////////// number ////////////////////////////
{
	if(trim($value) != '')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		$return['value'] = $value;
        
        if(isset($options['if_zero']) and $options['if_zero'] == 2 and !trim($value)) $return['value'] = __($options['call_text'], 'real-estate-listing-realtyna-wpl');
        if(isset($options['if_zero']) and !$options['if_zero'] and !trim($value)) $return = array();
	}
	
	$done_this = true;
}
elseif($type == 'text' and !$done_this) //////////////////////////// text ////////////////////////////
{
    if(trim($value) != '')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		
		if($field->table_column == 'googlemap_ln') #Longitude
			$return['value'] = wpl_render::render_longitude($value);
		elseif($field->table_column == 'googlemap_lt') #Latitude
			$return['value'] = wpl_render::render_latitude($value);
		else
        {
            if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status())
            {
                $current_language = wpl_global::get_current_language();
                $lang_column = wpl_addon_pro::get_column_lang_name($field->table_column, $current_language, false);
                
                if(isset($values[$lang_column])) $value = $values[$lang_column];
            }

			// UTF8 encoded
			if(!preg_match('!!u', $value)) $value = utf8_decode($value);

            $return['value'] = $value;
        }
        
        if(isset($options['if_zero']) and $options['if_zero'] == 2 and $value == '0') $return['value'] = __($options['call_text'], 'real-estate-listing-realtyna-wpl');
        if(isset($options['if_zero']) and !$options['if_zero'] and $value == '0') $return = array();
	}
	
	$done_this = true;
}
elseif($type == 'select' and !$done_this) //////////////////////////// select ////////////////////////////
{
	if($value != '' and $value != '-1')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');

		$options['params'] = is_array($options['params']) ? $options['params'] : array();

		foreach($options['params'] as $field_option)
		{
			if($value == $field_option['key']) $return['value'] = stripslashes(__($field_option['value'], 'real-estate-listing-realtyna-wpl'));
		}

		if(!isset($return['value']) or (isset($return['value']) and empty($return['value']))) $return = array();
	}
	
	$done_this = true;
}
elseif($type == 'textarea' and !$done_this) //////////////////////////// textarea ////////////////////////////
{
	if(trim($value) != '')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status())
        {
            $current_language = wpl_global::get_current_language();
            $lang_column = wpl_addon_pro::get_column_lang_name($field->table_column, $current_language, false);

            if(isset($values[$lang_column])) $value = $values[$lang_column];
        }
        
        $value = stripslashes($value);
        if($field->table_column == 'field_308') $value = apply_filters('the_content', $value);
        $value = wpl_global::do_shortcode($value);

		// UTF8 encoded
		if(!preg_match('!!u', $value)) $value = utf8_decode($value);

        $return['value'] = $value;
	}
	
	$done_this = true;
}
elseif($type == 'feature' and !$done_this) //////////////////////////// Features ////////////////////////////
{
	if($values[$field->table_column] != 0) 
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		
		/** options of property column **/
		$column_options = isset($values[$field->table_column.'_options']) ? $values[$field->table_column.'_options'] : '';
		$column_values = explode(',', trim($column_options, ', '));
        if(count($column_values) == 1 and trim($column_values[0]) == '') $column_values = array();
        
		$i = 0;
		if(isset($options['values']))
		{
			foreach($options['values'] as $field_option)
			{
				if(in_array($field_option['key'], $column_values))
				{
					$return['values'][$i] = stripslashes(__($field_option['value'], 'real-estate-listing-realtyna-wpl'));
					$i++;
				}
			}
		}
		else
		{
			$return['value'] = 1;
		}
	}
	
	$done_this = true;       
}
elseif($type == 'neighborhood' and !$done_this) //////////////////////////// Neighborhood ////////////////////////////
{
	if($values[$field->table_column] == '1')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		
		if($values[$field->table_column.'_distance'] != 0 and $values[$field->table_column.'_distance_by'] != 0)
		{
			$return['distance'] = $values[$field->table_column.'_distance'];
			
			if($values[$field->table_column.'_distance_by'] == '1')
            {
                $return['vehicle_type'] = 'Walk';
                $return['by'] = __('Walk', 'real-estate-listing-realtyna-wpl');
            }
			elseif($values[$field->table_column.'_distance_by'] == '2')
            {
                $return['vehicle_type'] = 'Car';
                $return['by'] = __('Car', 'real-estate-listing-realtyna-wpl');
            }
			elseif($values[$field->table_column.'_distance_by'] == '3')
            {
                $return['vehicle_type'] = 'Train';
                $return['by'] = __('Train', 'real-estate-listing-realtyna-wpl');
            }
		}
	}
	
	$done_this = true;
}
elseif($type == 'separator' and !$done_this) //////////////////////////// separator ////////////////////////////
{
	$return['field_id'] = $field->id;
	$return['type'] = $field->type;
	$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
	
	$done_this = true;
}
elseif($type == 'property_types' and !$done_this) //////////////////////////// property types ////////////////////////////
{
	if(trim($value) != '0' or trim($value) != '-1')
	{
        // Get Property Type
		$property_type = wpl_global::get_property_types($value);
        
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		$return['value'] = is_object($property_type) ? __($property_type->name, 'real-estate-listing-realtyna-wpl') : NULL;
	}
	
	$done_this = true;
}
elseif($type == 'listings' and !$done_this) //////////////////////////// listings ////////////////////////////
{
	if(trim($value) != '0' or trim($value) != '-1')
	{
		// Get Listing Type
		$listing_type = wpl_global::get_listings($value);
		
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		$return['value'] = is_object($listing_type) ? __($listing_type->name, 'real-estate-listing-realtyna-wpl') : NULL;
	}
	
	$done_this = true;
}
elseif($type == 'email' and !$done_this) //////////////////////////// email ////////////////////////////
{
	if(trim($value) != '') 
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		$return['value'] = $value;
	}
	
	$done_this = true;
}
elseif($type == 'mmnumber' and !$done_this) //////////////////////////// Min/Max numbers ////////////////////////////
{
    if(trim($value) != '')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		$return['value'] = $value;
        
        if(trim($values[$field->table_column.'_max'])) $return['value'] .= ' - '. $values[$field->table_column.'_max'];
        
        if(isset($options['if_zero']) and $options['if_zero'] == 2 and !trim($value) and !trim($values[$field->table_column.'_max'])) $return['value'] = __($options['call_text'], 'real-estate-listing-realtyna-wpl');
        if(isset($options['if_zero']) and !$options['if_zero'] and !trim($value) and !trim($values[$field->table_column.'_max'])) $return = array();
	}
	
	$done_this = true;
}
elseif($type == 'locations' and !$done_this) //////////////////////////// Locations ////////////////////////////
{
	$location_settings = wpl_global::get_settings('3'); # location settings
	
	$return['field_id'] = $field->id;
	$return['type'] = $field->type;
	$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
	
	for($i=1; $i<=7; $i++)
	{
		$location_id = isset($values['location'.$i.'_id']) ? $values['location'.$i.'_id'] : NULL;
		$location_name = 'location'.$i.'_name';

		if(!isset($values[$location_name])) continue;
		if(!trim($values[$location_name])) continue;

		$location_value = trim(stripslashes($values[$location_name]));

		// Location Abbr
		$abbr = wpl_locations::get_location_abbr_by_name($location_value, $i);

		$return['location_ids'][$i] = $location_id;
		$return['locations'][$i] = trim($abbr) ? __($abbr, 'real-estate-listing-realtyna-wpl') : __($location_value, 'real-estate-listing-realtyna-wpl');
        $return['raw'][$i] = $location_value;
		$return['keywords'][$i] = __($location_settings['location'.$i.'_keyword'], 'real-estate-listing-realtyna-wpl');
	}
	
	if(isset($values['zip_name']) and trim($values['zip_name']))
	{
		$return['location_ids']['zips'] = $values['zip_id'];
		$return['locations']['zips'] = __($values['zip_name'], 'real-estate-listing-realtyna-wpl');
        $return['raw']['zips'] = $values['zip_name'];
		$return['keywords']['zips'] = __($location_settings['locationzips_keyword'], 'real-estate-listing-realtyna-wpl');
	}

	$return['value'] = (isset($return['raw']) and is_array($return['raw'])) ? implode(', ', array_filter($return['raw'])) : '';
    $return['settings'] = (is_array($options) and isset($options['params'])) ? $options['params'] : array();

    $done_this = true;
}
elseif(($type == 'checkbox' or $type == 'tag') and !$done_this) //////////////////////////// Checkbox, Tag ////////////////////////////
{
	if($value != '0')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		$return['value'] = __('Yes', 'real-estate-listing-realtyna-wpl');
	}
	
	$done_this = true;
}
elseif(($type == 'volume' or $type == 'area' or $type == 'length') and !$done_this) //////////////////////////// Volume, Area, Length ////////////////////////////
{
	if(trim($value) != '')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		
        /** adding unit **/
        $unit_id = $values[$field->table_column.'_unit'];
        $unit = wpl_units::get_unit($unit_id);

		$cookie_unit = wpl_request::getVar('wpl_unit2', 0, 'COOKIE');
        if($cookie_unit and $cookie_unit != $unit_id)
        {
            $unit_id = $cookie_unit;

            $value = wpl_units::convert($value, $unit_id, $cookie_unit);
        	$unit = wpl_units::get_unit($unit_id);
        }
        
        $return['value'] = ($value == round($value) ? number_format($value, 0) : number_format($value, 2));
        if($unit) $return['value'] .= ' '.$unit['name'];
        
        // Automatically convert the unit to Acre and Hectare
        if($type == 'area' and isset($options['conversion']) and $options['conversion'] == 1 and in_array($values[$field->table_column.'_unit'], array(1, 2)))
        {
            // SQFT => Acre
            if($unit_id == 1 and $value >= 21780)
            {
                $return['value'] = number_format(round(($value/43560), 2), 2);
        
                /** adding unit **/
                $unit = wpl_units::get_unit(3);
                if($unit) $return['value'] .= ' '.$unit['name'];
            }
            // SQM => Hectare
            elseif($unit_id == 2 and $value >= 5000)
            {
                $return['value'] = number_format(round(($value/10000), 2), 2);
        
                /** adding unit **/
                $unit = wpl_units::get_unit(7);
                if($unit) $return['value'] .= ' '.$unit['name'];
            }
        }
        
        $return['raw'] = $value;
        $return['unit_id'] = $values[$field->table_column.'_unit'];
        
        if(isset($options['if_zero']) and $options['if_zero'] == 2 and !trim($value)) $return['value'] = __($options['call_text'], 'real-estate-listing-realtyna-wpl');
        if(isset($options['if_zero']) and !$options['if_zero'] and !trim($value)) $return = array();
	}
	
	$done_this = true;
}
elseif(($type == 'mmvolume' or $type == 'mmarea' or $type == 'mmlength') and !$done_this) //////////////////////////// Min/Max Volume, Area, Length ////////////////////////////
{
	if(trim($value) != '' or trim($values[$field->table_column.'_max']) != '')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		$return['value'] = ($value == round($value) ? number_format($value, 0) : number_format($value, 2));
		
        if(trim($values[$field->table_column.'_max'])) $return['value'] .= ' - '.($values[$field->table_column.'_max'] == round($values[$field->table_column.'_max']) ? number_format($values[$field->table_column.'_max'], 0) : number_format($values[$field->table_column.'_max'], 2));
        
		/** adding unit **/
		$unit = wpl_units::get_unit($values[$field->table_column.'_unit']);
		if($unit) $return['value'] .= ' '.$unit['name'];
        
        // Automatically convert the unit to Acre and Hectare
        if($type == 'mmarea' and isset($options['conversion']) and $options['conversion'] == 1 and in_array($values[$field->table_column.'_unit'], array(1, 2)))
        {
            $unit_id = $values[$field->table_column.'_unit'];
            
            // SQFT => Acre
            if($unit_id == 1 and $value >= 21780)
            {
                $return['value'] = number_format(round(($value/43560), 2), 2);
                if(trim($values[$field->table_column.'_max'])) $return['value'] .= ' - '.number_format(round(($values[$field->table_column.'_max']/43560), 2), 2);
                
                /** adding unit **/
                $unit = wpl_units::get_unit(3);
                if($unit) $return['value'] .= ' '.$unit['name'];
            }
            // SQM => Hectare
            elseif($unit_id == 2 and $value >= 5000)
            {
                $return['value'] = number_format(round(($value/10000), 2), 2);
                if(trim($values[$field->table_column.'_max'])) $return['value'] .= ' - '.number_format(round(($values[$field->table_column.'_max']/10000), 2), 2);
        
                /** adding unit **/
                $unit = wpl_units::get_unit(7);
                if($unit) $return['value'] .= ' '.$unit['name'];
            }
        }
        
        $return['raw'] = $value;
        $return['unit_id'] = $values[$field->table_column.'_unit'];
        
        if(isset($options['if_zero']) and $options['if_zero'] == 2 and !trim($value) and !trim($values[$field->table_column.'_max'])) $return['value'] = __($options['call_text'], 'real-estate-listing-realtyna-wpl');
        if(isset($options['if_zero']) and !$options['if_zero'] and !trim($value) and !trim($values[$field->table_column.'_max'])) $return = array();
	}
	
	$done_this = true;
}
elseif($type == 'price' and !$done_this) //////////////////////////// Price ////////////////////////////
{
	$return['field_id'] = $field->id;
	$return['type'] = $field->type;
	$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
    
    $unit_id = $values[$field->table_column.'_unit'];
    $cookie_unit = wpl_request::getVar('wpl_unit4', 0, 'COOKIE');
    if($cookie_unit and $cookie_unit != $unit_id)
    {
        $value = wpl_units::convert($value, $unit_id, $cookie_unit);
        $unit_id = $cookie_unit;
    }
    
	$rendered_price = wpl_render::render_price($value, $unit_id);
    
    $return['raw'] = $value;
    $return['unit_id'] = $unit_id;
    $return['value'] = $rendered_price;
    $return['price_only'] = $rendered_price;
	
    $price_period = array();
    if(isset($values[$field->table_column.'_period'])) $price_period = wpl_property::render_field($values[$field->table_column.'_period'], wpl_flex::get_dbst_id($field->table_column.'_period', $field->kind));
    if(isset($price_period['value']))
    {
        $return['value'] .= ' '.$price_period['value'];
        $return['price_period'] = $price_period['value'];
    }
    
    /** Add "From" to Vacation Rental Properties **/
    if($field->table_column == 'price' and wpl_global::check_addon('calendar'))
    {
        $listing_types = wpl_global::get_listing_types_by_parent(3);
        foreach($listing_types as $listing) $vacational_listing_types[] = $listing['id'];

        if(is_array($vacational_listing_types) and array_key_exists('listing', $values) and in_array($values['listing'], $vacational_listing_types))
        {
            $return['value'] = __('From', 'real-estate-listing-realtyna-wpl').' '.$return['value'];
        }
    }
    
    if(isset($options['if_zero']) and $options['if_zero'] == 2 and !trim($value)) $return['value'] = __($options['call_text'], 'real-estate-listing-realtyna-wpl');
    if(isset($options['if_zero']) and !$options['if_zero'] and !trim($value)) $return = array();

    $rebate = wpl_settings::get('properties_price_rebate');
    if(trim($value) and $rebate)
    {
    	$rebate = wpl_render::render_price(($value * $rebate / 100), $unit_id);
    	$return['value'] .= " ({$rebate} ".__('Rebate', 'real-estate-listing-realtyna-wpl').')';
    }

	$done_this = true;
}
elseif($type == 'mmprice' and !$done_this) //////////////////////////// Min/Max Price ////////////////////////////
{
	$return['field_id'] = $field->id;
	$return['type'] = $field->type;
	$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
    
    $unit_id = $values[$field->table_column.'_unit'];
    $cookie_unit = wpl_request::getVar('wpl_unit4', 0, 'COOKIE');
    if($cookie_unit and $cookie_unit != $unit_id)
    {
        $value = wpl_units::convert($value, $unit_id, $cookie_unit);
        $unit_id = $cookie_unit;
    }
    
	$rendered_price = wpl_render::render_price($value, $unit_id);
	
    $value_max = $values[$field->table_column.'_max'];
    if(trim($value_max))
    {
        $unit_id = $values[$field->table_column.'_unit'];
        
        if($cookie_unit and $cookie_unit != $unit_id)
        {
            $value_max = wpl_units::convert($value_max, $unit_id, $cookie_unit);
            $unit_id = $cookie_unit;
        }
    
        $rendered_price .= ' - '.wpl_render::render_price($value_max, $unit_id);
    }
    
    $return['raw'] = $value;
    $return['raw_max'] = $value_max;
    $return['unit_id'] = $unit_id;
    $return['value'] = $rendered_price;
    $return['price_only'] = $rendered_price;
    
    $price_period = wpl_property::render_field($values['price_period'], wpl_flex::get_dbst_id('price_period', $field->kind));
    if(isset($price_period['value']))
    {
        $return['value'] .= ' '.$price_period['value'];
        $return['price_period'] = $price_period['value'];
    }
    
    if(isset($options['if_zero']) and $options['if_zero'] == 2 and !trim($value) and !trim($value_max)) $return['value'] = __($options['call_text'], 'real-estate-listing-realtyna-wpl');
    if(isset($options['if_zero']) and !$options['if_zero'] and !trim($value) and !trim($value_max)) $return = array();
            
	$done_this = true;
}
elseif($type == 'url' and !$done_this) //////////////////////////// URL ////////////////////////////
{
    if(trim($value) != '')
	{
        $return['field_id'] = $field->id;
        $return['type'] = $field->type;
        $return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');

        $title = (isset($options['link_title']) and trim($options['link_title']) != '') ? $options['link_title'] : $value;
        $target = (isset($options['link_target']) and trim($options['link_target']) != '') ? $options['link_target'] : '_blank';

        if(stripos($value, 'http://') === false and stripos($value, 'https://') === false)
        	$value = 'http://'.$value;

        $return['value'] = '<a href="'.$value.'" target="'.$target.'">'.$title.'</a>';
    }
    
	$done_this = true;
}
elseif($type == 'parent' and !$done_this) //////////////////////////// Parent ////////////////////////////
{
    if(trim($value))
	{
        $return['field_id'] = $field->id;
        $return['type'] = $field->type;
        $return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
        
        $parents_ids = wpl_render::render_parent($value, (isset($options['key']) ? $options['key'] : 'parent'), true);
        
        $value_str = '';
        $parents_html_str = '';

        $parents = array_reverse(explode(',', $parents_ids));
        foreach($parents as $parent)
        {
        	$link = wpl_property::get_property_link(NULL, $parent);
        	$title = trim(wpl_property::update_property_title(NULL, $parent), ', ');
        	$title = $title ? $title : $parent;

        	$value_str .= '<a href="'.$link.'">'.$title.'</a> / ';
        	$parents_html_str .= '<a href="'.$link.'"><b>'.$title.'</b></a> / ';
        }

        $return['value'] = trim($value_str, '/ ');
        $return['html'] = trim($parents_html_str, '/ ');
    }
    
	$done_this = true;
}
elseif($type == 'date' and !$done_this) //////////////////////////// Date ////////////////////////////
{
    if(trim($value))
	{
        $return['field_id'] = $field->id;
        $return['type'] = $field->type;
        $return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
        $return['value'] = wpl_render::render_date($value);
    }
	
	$done_this = true;
}
elseif($type == 'datetime' and !$done_this) //////////////////////////// Date Time ////////////////////////////
{
    if(trim($value))
	{
        $return['field_id'] = $field->id;
        $return['type'] = $field->type;
        $return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
        $return['value'] = wpl_render::render_datetime($value);
    }
	
	$done_this = true;
}
elseif($type == 'boolean' and !$done_this) //////////////////////////// boolean - true&false////////////////////////////
{
	$field_options = json_decode($field->options, true);
	$array_boolean_options = array(1=>'true_label', 0=>'false_label');
	
    if(trim($value) != '')
	{	
        $return['field_id'] = $field->id;
        $return['type'] = $field->type;
        $return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
        $return['value'] = $field_options[$array_boolean_options[$value]];
    }
	
	$done_this = true;
}
elseif($type == 'array' and !$done_this) //////////////////////////// array ////////////////////////////
{
	if(trim($value, '|') != '')
	{
		$render_value = '';

		if(stristr($value, '|'))
		{
			$array_values = explode('|', $value);

			foreach($array_values as $val) $render_value .= trim($val).', ';

			$render_value = trim($render_value, ', ');
		}
		else $render_value = $value;

		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');
		$return['value'] = $render_value;
	}

	$done_this = true;
}
elseif($type == 'multiselect' and !$done_this) //////////////////////////// multiselect ////////////////////////////
{
	if($value != '' and $value != '-1')
	{
		$return['field_id'] = $field->id;
		$return['type'] = $field->type;
		$return['name'] = __($field->name, 'real-estate-listing-realtyna-wpl');

		$options['params'] = is_array($options['params']) ? $options['params'] : array();
		$keys = explode(',', $value);
		$multiselect_values = array();

		foreach($options['params'] as $field_option)
		{
			if(in_array($field_option['key'], $keys)) $multiselect_values[] = stripslashes(__($field_option['value'], 'real-estate-listing-realtyna-wpl'));
		}

		$return['value'] = implode(', ', $multiselect_values);

		if(!isset($return['value']) or (isset($return['value']) and empty($return['value']))) $return = array();
	}
	
	$done_this = true;
}