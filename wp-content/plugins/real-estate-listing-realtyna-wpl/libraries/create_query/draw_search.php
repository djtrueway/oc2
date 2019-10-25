<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($format == 'radiussearchunit' and !$done_this)
{
	$unit_id = $value;
	$address = isset($vars['sf_radiussearch']) ? $vars['sf_radiussearch'] : '';
	$radius = isset($vars['sf_radiussearchradius']) ? $vars['sf_radiussearchradius'] : 0;
	
    if(trim($address))
    {
        $location_point = wpl_locations::get_LatLng($address);
        $latitude = $location_point[0];
        $longitude = $location_point[1];
        
        // For drawing radius on the map if APS addon exists
        wpl_request::setVar('sf_radiussearch_lat', $latitude);
        wpl_request::setVar('sf_radiussearch_lng', $longitude);
    }
    else
    {
        $latitude = isset($vars['sf_radiussearch_lat']) ? $vars['sf_radiussearch_lat'] : 0;
        $longitude = isset($vars['sf_radiussearch_lng']) ? $vars['sf_radiussearch_lng'] : 0;
    }
	
	if($latitude and $longitude and $radius and $unit_id)
	{
		$unit = wpl_units::get_unit($unit_id);
		
		if($unit)
		{
			$tosi = (6371*1000)/$unit['tosi'];
			$radius_si = $radius*$unit['tosi'];
			
			$query .= " AND (( ".$tosi." * acos( cos( radians(".$latitude.") ) * cos( radians( googlemap_lt ) ) * cos( radians( googlemap_ln ) - radians(".$longitude.") ) + sin( radians(".$latitude.") ) * sin( radians( googlemap_lt ) ) ) ) < ".($radius) .") AND `show_address`='1'";
		}
	}

	$done_this = true;
}
elseif(($format == 'polygonsearch' or $format == 'dmgfc') and !$done_this)
{
	if(version_compare(wpl_db::version(), '5.6.1', '>=')) $sql_function = 'ST_Contains';
    else $sql_function = 'Contains';

	if($format == 'dmgfc')
    {
        // Import Demographic Library
        _wpl_import('libraries.addon_demographic');

        $demographic = new wpl_addon_demographic();
        $raw_points = $demographic->get_raw_points($value);

        wpl_request::setVar('sf_polygonsearch', 1);
        wpl_request::setVar('sf_polygonsearchpoints', $raw_points);
    }
    else $raw_points = isset($vars['sf_polygonsearchpoints']) ? $vars['sf_polygonsearchpoints'] : '[]';

    // Convert Raw Points to Polygon
    $polygons = wpl_global::toPolygons($raw_points);
    
    $qq = array();
    foreach($polygons as $polygon)
    {
        $polygon_str = '';
        foreach($polygon as $polygon_point) $polygon_str .= $polygon_point[1].' '.$polygon_point[0].', ';
        $polygon_str = trim($polygon_str, ', ');
        
        $qq[] = $sql_function."(GeomFromText('Polygon((".$polygon_str."))'), geopoints) = 1";
    }
    
    if(count($qq)) $query .= " AND (".implode(' OR ', $qq).") AND `show_address`='1'";
    
	$done_this = true;
}
elseif($format == 'multipleradiussearch' and wpl_global::check_addon('aps') and !$done_this)
{
    $unit_id = wpl_global::get_setting('multiple_radius_location_unit');
    $location_level = wpl_global::get_setting('multiple_radius_location_level');

    $sf_multipleradiussearch = isset($vars['sf_multipleradiussearch']) ? $vars['sf_multipleradiussearch'] : '';

    $sf_multipleradiussearch = trim(urldecode($sf_multipleradiussearch), '|');

    if(stristr($sf_multipleradiussearch, '|')) $array_locations = explode('|', $sf_multipleradiussearch);
    else $array_locations = array($sf_multipleradiussearch);
    
    $exp_location_data = array();

    foreach($array_locations as $location)
    {
        $exp_loc = explode(':', $location);
        $city = str_replace('city-', '', $exp_loc[0]);
        $radius = str_replace('radius-', '', $exp_loc[1]);
        $complete_address = str_replace('address-', '', $exp_loc[2]);

        $exp_location_data[$city] = array('location'=>str_replace('_', ' ', $city), 'radius'=>$radius, 'complete_address'=>$complete_address);
    }

    $queries = array();

    if(count($exp_location_data) <= 0) return;

    foreach($exp_location_data as $key=>$value)
    {
        $radius = $value['radius'];
        $complete_address = $value['complete_address'];
        $location_id = 0;
        $location_data = '';

        if(!empty($location_level))
        {
            $location_id = wpl_locations::get_location_id($value['location'], '', $location_level);
            $location_data = wpl_locations::get_location($location_id, $location_level);
        }
        else return;

        if(trim($complete_address))
        {
            if(!empty($location_data))
            {
                $location_lat = $location_data->latitude;
                $location_lng = $location_data->longitude;

                if(empty($location_lat) or $location_lat == 0 and empty($location_lng) or $location_lng == 0)
                {
                    $location_point = wpl_locations::get_LatLng($complete_address);

                    $latitude = $location_point[0];
                    $longitude = $location_point[1];

                    wpl_locations::update_location($location_data->id, 'latitude', $latitude, $location_level);
                    wpl_locations::update_location($location_data->id, 'longitude', $longitude, $location_level);
                }
                else
                {
                    $latitude = $location_lat;
                    $longitude = $location_lng;
                }
            }
            else
            {
                $location_point = wpl_locations::get_LatLng($complete_address);
                $latitude = $location_point[0];
                $longitude = $location_point[1];
            }
        }
        else
        {
            $latitude = isset($vars['sf_radiussearch_lat']) ? $vars['sf_radiussearch_lat'] : 0;
            $longitude = isset($vars['sf_radiussearch_lng']) ? $vars['sf_radiussearch_lng'] : 0;
        }

        if($latitude and $longitude and $radius and $unit_id)
        {
            $unit = wpl_units::get_unit($unit_id);

            if($unit)
            {
                $tosi = (6371*1000)/$unit['tosi'];
                $radius_si = $radius*$unit['tosi'];

                $queries[] = " ((".$tosi." * acos( cos( radians(".$latitude.") ) * cos( radians( googlemap_lt ) ) * cos( radians( googlemap_ln ) - radians(".$longitude.") ) + sin( radians(".$latitude.") ) * sin( radians( googlemap_lt ) ) ) ) < ".($radius) .") ";
            }
        }
    }

    if(!empty($queries) || !isset($queries)) $query .= " AND (".implode(' OR ', $queries).") AND `show_address`='1' ";

    $done_this = true;
}