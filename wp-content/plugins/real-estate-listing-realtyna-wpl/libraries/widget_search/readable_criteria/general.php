<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($format == 'select' and !$done_this)
{
    if(stristr($table_column, 'location') and $value and $value != '-1')
    {
        $exp_location = explode('_', $table_column);
        $location_level = str_replace('location', '', $exp_location[0]);

        $location_method = $exp_location[1];
        $location_settings = wpl_global::get_settings('3'); # location settings

        if($location_method == 'name') $get_location = wpl_locations::get_location(wpl_locations::get_location_id($value, '', $location_level), $location_level);
        else $get_location = wpl_locations::get_location($value, $location_level);

        $field_name = isset($location_settings[$exp_location[0] . '_keyword']) ? $location_settings[$exp_location[0] . '_keyword'] : $table_column;
        $readable_search_field[$field_name] = $get_location->name;
    }
    elseif($table_column == 'kind' and trim($value) != '' and $value != '-1')
    {
        $readable_search_field[$field_name] = wpl_flex::get_kind_label($value);
    }
    elseif($value and $value != '-1')
    {
        $generated_values = wpl_global::generate_readable_criteria_values($table_column, $value);
        $readable_search_field[$field_name] = $generated_values;
    }

	$done_this = true;
}
elseif($format == 'tmin' and !$done_this)
{
    if($value != '-1' and trim($value)) $readable_search_field[$field_name] = sprintf(__('Equal or more than %s', 'real-estate-listing-realtyna-wpl'), $value);
	$done_this = true;
}
elseif($format == 'tmax' and !$done_this)
{
    if($value != '-1' and trim($value)) $readable_search_field[$field_name] = sprintf(__('Equal or less than %s', 'real-estate-listing-realtyna-wpl'), $value);
	$done_this = true;
}
elseif($format == 'multiple' and !$done_this)
{
    if(!($value == '' or $value == '-1' or $value == ','))
	{
		$value = rtrim($value, ',');
		if($value != '')
        {
            if(strpos($value, ',') === false)
            {
                $values = wpl_global::generate_readable_criteria_values($table_column, $value);
                $generated_values = $values;
            }
            else
            {
                $values_ex = explode(',', $value);
                $generated_values = '';

                foreach ($values_ex as $val)
                {
                    $values = wpl_global::generate_readable_criteria_values($table_column, $val);
                    $generated_values .= $values.', ';
                }

                $generated_values = rtrim($generated_values, ', ');
            }
            $readable_search_field[$field_name] = sprintf(__('In these values: %s', 'real-estate-listing-realtyna-wpl'), $generated_values);
        }
	}

	$done_this = true;
}
elseif($format == 'text' and !$done_this)
{
    if(stristr($table_column, 'location'))
    {
        $exp_location = explode('_', $table_column);
        $location_level = str_replace('location', '', $exp_location[0]);

        $location_method = $exp_location[1];
        $location_settings = wpl_global::get_settings('3'); # location settings

        if($location_method == 'name') $get_location = wpl_locations::get_location(wpl_locations::get_location_id($value, '', $location_level), $location_level);
        else $get_location = wpl_locations::get_location($value, $location_level);

        $field_name = isset($location_settings[$exp_location[0] . '_keyword']) ? $location_settings[$exp_location[0] . '_keyword'] : $table_column;
        $readable_search_field[$field_name] = sprintf(__('Contains %s', 'real-estate-listing-realtyna-wpl'), $get_location->name);
    }
    else
    {
        if(trim($value) != '') $readable_search_field[$field_name] = sprintf(__('Contains %s', 'real-estate-listing-realtyna-wpl'), $value);
    }

	$done_this = true;
}
elseif($format == 'between' and !$done_this)
{
    if($value != '-1' and trim($value) != '')
    {
        $ex = explode(':', $value);
        $min = isset($ex[0])? $ex[0] : 0;
        $max = isset($ex[1])? $ex[1] : NULL;

        $readable_search_field[$field_name] = sprintf(__('Between %s and %s', 'real-estate-listing-realtyna-wpl'), $min, $max);
    }

	$done_this = true;
}
elseif($format == 'betweenunit' and !$done_this)
{
    if($value != '-1' and trim($value) != '')
	{
		$unit_id = isset($vars['sf_unit_'.$table_column]) ? $vars['sf_unit_'.$table_column] : 0;
        $unit_data = wpl_units::get_unit($unit_id);

        $ex = explode(':', $value);
        $min = isset($ex[0])? $ex[0] : 0;
        $max = isset($ex[1])? $ex[1] : 0;

		$si_value_min = $unit_data['tosi'] * $min;
		$si_value_max = $unit_data['tosi'] * $max;

        $string = __('Between ', 'real-estate-listing-realtyna-wpl');
        if($si_value_min != 0) $string .= $si_value_min;
		if($si_value_max != 0) $string .= sprintf(__(' and %s', 'real-estate-listing-realtyna-wpl'), $si_value_max);

        $readable_search_field[$field_name] = $string;
	}

	$done_this = true;
}
elseif($format == 'feature' and !$done_this)
{
    if(!($value == '' or $value == '-1' or $value == ','))
	{
        $value = trim($value, ',');

		if($value != '')
        {
            if(strpos($value, ',') === false)
            {
                $values = wpl_global::generate_readable_criteria_values($table_column, $value);
                $generated_values = $values;
            }
            else
            {
                $values_ex = explode(',', $value);
                $generated_values = '';

                foreach ($values_ex as $val)
                {
                    $values = wpl_global::generate_readable_criteria_values($table_column, $val);
                    $generated_values .= $values.', ';
                }

                $generated_values = rtrim($generated_values, ', ');
            }
            $readable_search_field[$field_name] = sprintf(__('In these values: %s', 'real-estate-listing-realtyna-wpl'), $generated_values);
        }
	}

	$done_this = true;
}
elseif($format == 'ptcategory' and !$done_this)
{
	if($value != '-1' and trim($value) != '')
	{
        $category_id = wpl_db::select("SELECT `id` FROM `#__wpl_property_types` WHERE LOWER(name)='".strtolower($value)."' AND `parent`='0'", 'loadResult');
        $property_types = wpl_db::select("SELECT `id` FROM `#__wpl_property_types` WHERE `parent`='$category_id'", 'loadAssocList');

        $property_types_str = '';
        if(count($property_types) and $category_id)
        {
            foreach($property_types as $property_type) $property_types_str .= $property_type['id'].',';
            $property_types_str = trim($property_types_str, ', ');
        }

        if(strpos($property_types_str, ',') === false)
        {
            $property_types_str = wpl_global::generate_readable_criteria_values($table_column, $value);
            $generated_values = $values;
        }
        else
        {
            $values_ex = explode(',', $property_types_str);
            $generated_values = '';

            foreach ($values_ex as $val)
            {
                $values = wpl_global::generate_readable_criteria_values($table_column, $val);
                $generated_values .= $values.', ';
            }

            $generated_values = rtrim($generated_values, ', ');
        }

        $readable_search_field[$field_name] = sprintf(__('In these values: %s', 'real-estate-listing-realtyna-wpl'), $generated_values);
	}

	$done_this = true;
}
elseif($format == 'ltcategory' and !$done_this)
{
	if($value != '-1' and trim($value) != '')
	{
        $category_id = wpl_db::select("SELECT `id` FROM `#__wpl_listing_types` WHERE LOWER(name)='".strtolower($value)."' AND `parent`='0'", 'loadResult');
        $listing_types = wpl_db::select("SELECT `id` FROM `#__wpl_listing_types` WHERE `parent`='$category_id'", 'loadAssocList');

        $listing_types_str = '';
        if(count($listing_types) and $category_id)
        {
            foreach($listing_types as $listing_type) $listing_types_str .= $listing_type['id'].',';
            $listing_types_str = trim($listing_types_str, ', ');
        }

        if(strpos($listing_types_str, ',') === false)
        {
            $property_types_str = wpl_global::generate_readable_criteria_values($table_column, $value);
            $generated_values = $values;
        }
        else
        {
            $values_ex = explode(',', $listing_types_str);
            $generated_values = '';

            foreach ($values_ex as $val)
            {
                $values = wpl_global::generate_readable_criteria_values($table_column, $val);
                $generated_values .= $values.', ';
            }

            $generated_values = rtrim($generated_values, ', ');
        }

        $readable_search_field[$field_name] = sprintf(__('In these values: %s', 'real-estate-listing-realtyna-wpl'), $generated_values);
	}

	$done_this = true;
}
elseif($format == 'datemin' and !$done_this)
{
	if(trim($value) != '')
	{
		$value = wpl_render::derender_date($value);
		$readable_search_field[$field_name] = sprintf(__('Equal or more than %s', 'real-estate-listing-realtyna-wpl'), $value);
	}

	$done_this = true;
}
elseif($format == 'datemax' and !$done_this)
{
	if(trim($value) != '')
	{
		$value = wpl_render::derender_date($value);
		$readable_search_field[$field_name] = sprintf(__('Equal or less than %s', 'real-estate-listing-realtyna-wpl'), $value);
	}

	$done_this = true;
}
elseif($format == 'rawdatemin' and !$done_this)
{
	if(trim($value) != '') $readable_search_field[$field_name] = sprintf(__('Equal or more than %s', 'real-estate-listing-realtyna-wpl'), $value);
	$done_this = true;
}
elseif($format == 'rawdatemax' and !$done_this)
{
	if(trim($value) != '') $readable_search_field[$field_name] = sprintf(__('Equal or less than %s', 'real-estate-listing-realtyna-wpl'), $value);
	$done_this = true;
}
elseif($format == 'notselect' and !$done_this)
{
    $readable_search_field[$field_name] = sprintf(__('Not select in %s', 'real-estate-listing-realtyna-wpl'), $value);
	$done_this = true;
}
elseif($format == 'parent' and !$done_this)
{
	if($value != '-1' and trim($value) != '')
	{
        /** converts listing id to property id **/
        if($value) $value = wpl_property::pid($value);

		$readable_search_field[$field_name] = wpl_property::update_property_title(NULL, $value);
	}

	$done_this = true;
}
elseif($format == 'textsearch' and !$done_this)
{
    if($value != '-1' and trim($value) != '')
    {
        $readable_search_field[$field_name] = sprintf(__('Contains %s', 'real-estate-listing-realtyna-wpl'), $value);
    }

	$done_this = true;
}
elseif($format == 'unit' and !$done_this)
{
	if($value != '-1' and trim($value) != '')
	{
		$unit_data = wpl_units::get_unit($value);

        $min = (isset($search_fields['sf_min_'.$table_column]) and $search_fields['sf_min_'.$table_column] != '-1') ? $search_fields['sf_min_'.$table_column] : 0;
		$max = (isset($search_fields['sf_max_'.$table_column]) and $search_fields['sf_max_'.$table_column] != '-1') ? $search_fields['sf_max_'.$table_column] : 0;

		if($min or $max)
        {
            $si_value_min = $unit_data['tosi'] * $min;
            $si_value_max = $unit_data['tosi'] * $max;

            if(!$si_value_min and $si_value_max)
            {
                $value = ($unit_data['type'] == 4 ? wpl_render::render_price($max, $unit_data['id']) : $max.' '.$unit_data['name']);
                $string = sprintf(__('Up to %s', 'real-estate-listing-realtyna-wpl'), $value);
            }
            elseif($si_value_min and !$si_value_max)
            {
                $value = ($unit_data['type'] == 4 ? wpl_render::render_price($min, $unit_data['id']) : $min.' '.$unit_data['name']);
                $string = sprintf(__('More than %s', 'real-estate-listing-realtyna-wpl'), $value);
            }
            else
            {
                $min_value = ($unit_data['type'] == 4 ? wpl_render::render_price($min, $unit_data['id']) : $min.' '.$unit_data['name']);
                $max_value = ($unit_data['type'] == 4 ? wpl_render::render_price($max, $unit_data['id']) : $max.' '.$unit_data['name']);

                $string = sprintf(__('Between %s and %s', 'real-estate-listing-realtyna-wpl'), $min_value, $max_value);
            }

            $readable_search_field[$field_name] = $string;
        }
	}

	$done_this = true;
}
elseif($format == 'locationtextsearch' and !$done_this)
{
    if(trim($value)) $readable_search_field[$field_name] = $value;
	$done_this = true;
}
elseif($format == 'advancedlocationtextsearch' and !$done_this)
{
    if(trim($value)) $readable_search_field[$field_name] = $value;
    $done_this = true;
}
elseif($format == 'multiplelocationtextsearch' and !$done_this)
{
    if(trim($value)) $readable_search_field[$field_name] = $value;
    $done_this = true;
}