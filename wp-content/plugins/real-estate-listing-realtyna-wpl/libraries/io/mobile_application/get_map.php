<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.addon_save_searches');

/**
 * get map command
 * @author Chris <chris@realtyna.com>
 * @since WPL2.7.0
 * @package WPL
 * @date 2015/08/31
 */
class wpl_io_cmd_get_map extends wpl_io_cmd_base
{
    private $where = array();
    private $order_by = "id";
    private $order = "DESC";
    private $built;
    private $polygon;
    private $units;
    private $start = 0;
    private $limit = 1000;
    private $methods = array("get_bubbles", "get_properties", "get_favorites", "get_savedsearches");

    /**
     * This method is the main method of each commands
     * @return mixed
     */
    public function build()
    {
        if(isset($this->params['start']))
        {
            $this->start = $this->params['start'];
        }

        if(isset($this->params['limit']))
        {
            $this->limit = $this->params['limit'];
        }

        if(isset($this->params['sort_item']) && $this->params['sort_item'] != '')
        {
            $this->order_by = $this->params['sort_item'];
        }

        if(isset($this->params['sort_method']) && $this->params['sort_method'] != '')
        {
            $this->order = $this->params['sort_method'];
        }

        $this->where = wpl_addon_mobile_application::create_query_where($this->params);

        if(isset($this->params['polygon']))
        {
            $polygon = $this->params['polygon'];
            foreach($polygon as $key=>$value)
            {
                $explode = explode(",", $value);
                $array = array();

                for($i = 0; $i < count($explode); $i = 2 + $i++)
                {
                    array_push($array, $explode[$i] . "," . $explode[$i + 1]);
                }

                $this->polygon[$key] = $array;
            }
        }

        if($this->params['method'] == 'get_favorites')
        {
            $uid = $this->params['uid'];
            $pids = implode(',', wpl_addon_pro::favorite_get_pids(false, $uid));

            if(!$pids)
            {
                $this->built['properties']['count'] = 0;
                return $this->built;
            }

            $this->where['sf_multiple_id'] = $pids;
            $this->params['method'] = 'get_properties';
        }

        if($this->params['method'] == 'get_properties')
        {
            $this->permalink = wpl_sef::get_wpl_permalink(true);

            $model = new wpl_property();
            $this->where['sf_select_confirmed'] = '1';
            $this->where['sf_select_finalized'] = '1';
            $this->where['sf_select_deleted'] = '0';

            $model->start($this->start, $this->limit, $this->order_by, $this->order, $this->where);
            $model->select = 'id,pic_numb,sp_featured,sp_hot,sp_openhouse,sp_forclosure,field_313,field_308,bedrooms,bathrooms,living_area,living_area_unit,price,price_unit,kind,location_text,googlemap_lt,googlemap_ln,listing';
            $model->query();

            $result_holder = json_decode(json_encode($model->search()), true);
            $model->finish();

            $result = array();
            if(empty($this->polygon))
            {
                $result = $result_holder;
            }
            else
            {
                foreach($result_holder as $key=>$value)
                {
                    if($this->is_property_in_polygon($value['googlemap_lt'], $value['googlemap_ln']))
                    {
                        $result[] = $value;
                    }
                }
            }

            $this->built['properties']['count'] = count($result);
            if(!empty($result))
            {
                $i = 0;
                foreach($result as $key=>$value)
                {
                    $this->built['properties']['result'][$i]['sections']['images'] = $this->create_images_section($value);
                    $this->built['properties']['result'][$i]['sections']['property_show_sections'] = $this->create_property_show_section($value);
                    $this->built['properties']['result'][$i]['sections']['map_sections'] = $this->create_map_marker_section($value, $value['price'], true, $value['price_unit']);
                    $this->built['properties']['result'][$i]['sections']['property_preview_sections'] = $this->create_map_preview_section($value);
                    $this->built['properties']['result'][$i]['sections']['listing_sections'] = $this->create_listing_section($value);
                    $this->built['properties']['result'][$i]['id'] = $value['id'];
                    $this->built['properties']['result'][$i]['googlemap_ln'] = $value['googlemap_ln'];
                    $this->built['properties']['result'][$i]['googlemap_lt'] = $value['googlemap_lt'];
                    $this->built['properties']['result'][$i]['listing_type_id'] = $value['listing'];
                    $this->built['properties']['result'][$i]['type'] = $value['kind'];
                    $i++;
                }
            }
        }
        elseif($this->params['method'] == 'get_savedsearches')
        {
            $savedsearches = array();

            if(wpl_global::check_addon('save_searches'))
            {
                $uid = $this->params['uid'];
                $model = new wpl_addon_save_searches($uid);
                $searches = $model->get(0, $uid);


                foreach ($searches as $id => $search)
                {
                    $savedsearches[$id] = json_decode($search['criteria'], true);
                    $savedsearches[$id]['id'] = $search['id'];
                    $savedsearches[$id]['searchname'] = $search['name'];
                }
            }

            $this->built['savedsearches'] = $savedsearches;
        }
        else
        {
            $results = $this->get_bubbles();
            $i = 0;
            $count = 0;

            $this->built['bubbles']['bubbles_count'] = count($results);
            $this->built['bubbles']['count'] = $count;

            foreach($results as $key=>$result)
            {
                unset($result['g_lt']);
                unset($result['g_ln']);

                $this->built['bubbles']['result'][$key] = $result;
                $this->built['bubbles']['result'][$key]['sections']['map_sections'] = $this->create_map_marker_section($result, $result['count'], false);

                $count = $count + $result['count'];
                $i++;
            }

            $this->built['bubbles']['count'] = $count;
        }

        return $this->built;
    }

    /**
     * Data validation
     * @return boolean
     */
    public function validate()
    {
        if(trim($this->params['method']) == '' || !in_array($this->params['method'], $this->methods))
            return false;
        elseif(($this->params['method'] == 'get_favorites' or $this->params['method'] == 'get_savedsearches') and !$this->params['uid'])
            return false;

        return true;
    }

    /**
     * Check if property is inside of a polygon
     * @param $latitude
     * @param $longitude
     * @return bool
     */
    protected function is_property_in_polygon($latitude, $longitude)
    {
        foreach($this->polygon as $key=>$value)
        {
            if($this->is_within_boundary($latitude.",".$longitude, $value))
            {
                return true;
            }
        }

        return false;
    }

    /**
     * Pointing a string to coordinates
     * @param $pointString
     * @return array
     */
    protected function point_string_to_coordinates($pointString)
    {
        $coordinates = explode(",", $pointString);
        $a = array("x"=>trim($coordinates[0]), "y"=>trim($coordinates[1]));
        return $a;
    }

    /**
     * Check if point in boundary
     * @param $point
     * @param $polygon
     * @return bool
     */
    protected function is_within_boundary($point, $polygon)
    {
        $result = false;
        $point = $this->point_string_to_coordinates($point);
        $vertices = array();

        foreach($polygon as $vertex)
        {
            $vertices[] = $this->point_string_to_coordinates($vertex);
        }

        $intersections = 0;
        $vertices_count = count($vertices);

        for($i=1; $i < $vertices_count; $i++)
        {
            $vertex1 = $vertices[$i-1];
            $vertex2 = $vertices[$i];

            if($vertex1['y'] == $vertex2['y'] and $vertex1['y'] == $point['y'] and $point['x'] > min($vertex1['x'], $vertex2['x']) and $point['x'] < max($vertex1['x'], $vertex2['x']))
            {
                $result = true;
                $i = $vertices_count;
            }

            if($point['y'] > min($vertex1['y'], $vertex2['y']) and $point['y'] <= max($vertex1['y'], $vertex2['y']) and $point['x'] <= max($vertex1['x'], $vertex2['x']) and $vertex1['y'] != $vertex2['y'])
            {
                $xinters = ($point['y'] - $vertex1['y']) * ($vertex2['x'] - $vertex1['x']) / ($vertex2['y'] - $vertex1['y']) + $vertex1['x'];
                if($xinters == $point['x'])
                {
                    $result = true;
                    $i = $vertices_count;
                }

                if($vertex1['x'] == $vertex2['x'] || $point['x'] <= $xinters)
                {
                    $intersections++;
                }
            }
        }

        if($intersections % 2 != 0 && $result == false)
        {
            $result = true;
        }

        return $result;
    }

    /**
     * Creating images section
     * @param $property
     * @return array
     */
    private function create_images_section($property)
    {
        $blog_id = wpl_property::get_blog_id($property['id']);
        $path = wpl_items::get_folder($property['id'], $property['kind'], $blog_id);
        $gallery = wpl_items::get_items($property['id'], 'gallery', $property['kind'], '', 1);
        $return = array();

        foreach($gallery as $image)
        {
            if($image->item_cat == 'external')
            {
                $return[] = $image->item_extra3;
            }
            else
            {
                $return[] = $path.$image->item_name;
            }
        }

        return $return;
    }

    /**
     * Creating property show section
     * @param $property
     * @return array
     */
    private function create_property_show_section($property)
    {
        $tags = array();
        if(!empty($property['sp_featured']) && $property['sp_featured'] != 0)
        {
            array_push($tags, __("Featured", 'real-estate-listing-realtyna-wpl'));
        }

        if(!empty($property['sp_hot']) && $property['sp_hot'] != 0)
        {
            array_push($tags, __("Hot", 'real-estate-listing-realtyna-wpl'));
        }

        if(!empty($property['sp_openhouse']) && $property['sp_openhouse'] != 0)
        {
            array_push($tags, __("OpenHouse", 'real-estate-listing-realtyna-wpl'));
        }

        if(!empty($property['sp_forclosure']) && $property['sp_forclosure'] != 0)
        {
            array_push($tags, __("Forclosure", 'real-estate-listing-realtyna-wpl'));
        }

        return array(
            array(
                'section_type'=>'header',
                'labels'=>$tags,
            ),
            array(
                'section_type'=>'inline_section',
                'content'=>array(
                    array(
                        'item_type'=>'title',
                        'text'=>!empty($property['field_313']) ? $property['field_313'] : 'No Title',
                        'position'=>'left'
                    ),
                    array(
                        'item_type'=>'image',
                        'url'=>'ic_image',
                        'position'=>'right'
                    ),
                    array(
                        'item_type'=>'text',
                        'text'=>$property['pic_numb'], // number of property images
                        'position'=>'right'
                    ),
                ),
            ),
            array(
                'section_type'=>'info_box',
                'content'=>array(
                    array(
                        'weight'=>1,
                        'orientation'=>'horizontal',
                        'icon'=>'ic_locator',
                        'text'=>empty($property['location_text']) ? '-' : $property['location_text'], //address address of property
                        'description'=>'' // description of property
                    ),
                )
            ),
            array(
                'section_type'=>'share_content',
                'content'=>$result['field_313']."\n".$this->permalink.$property['id'],
            ),
            array(
                'section_type'=>'info_box',
                'content'=>array(
                    array(
                        'weight'=>1,
                        'orientation'=>'vertical',
                        'icon'=>'ic_bedroom_dark',
                        'description'=>$this->get_field($property, $property['bedrooms'])
                    ),
                    array(
                        'weight'=>1,
                        'orientation'=>'vertical',
                        'icon'=>'ic_bathroom_2',
                        'description'=>$this->get_field($property, $property['bathrooms'])
                    ),
                    array(
                        'weight'=>2,
                        'orientation'=>'vertical',
                        'icon'=>'ic_built_up_area',
                        'description'=>$this->get_field($property, $property['living_area']).' '.$this->get_unit_name($property['living_area_unit'])
                    ),
                    array(
                        'weight'=>2,
                        'orientation'=>'vertical',
                        'description'=>$this->get_field($property, $property['price'], true, $property['price_unit'])
                    ),
                )
            ),
        );
    }

    /**
     * Creating map section
     * @param $value
     * @param $marker_text
     * @param bool $price
     * @param string $unit
     * @return array
     */
    private function create_map_marker_section($value, $marker_text, $price = true, $unit = '')
    {
        return array(
            array(
                'section_type'=>'marker_text',
                'text'=>!empty($marker_text) ? $this->get_field($value, $marker_text, $price, $unit) : 'No Price' //price
            )
        );
    }

    /**
     * Convert number to money format
     * @param $array
     * @param $value
     * @param $unit
     * @return string
     */
    public function money_format($value, $unit)
    {
        $key = '';
        $len = strlen($value);

        if($len > 9)
        {
            $value = ($value / 1000000000);
            $key = 'B';
        }
        elseif($len > 6)
        {
            $value = ($value / 1000000);
            $key = 'M';
        }
        elseif($len > 3)
        {
            $value = ($value / 1000);
            $key = 'K';
        }

        return $this->get_unit_name($unit) . (number_format($value, 2) + 0) . $key;
    }

    /**
     * Get Unit Name
     * @author Steve A. <steve@realtyna.com>
     * @param  int      $unit   Unit ID
     * @return string           Unit Name
     */
    private function get_unit_name($unit)
    {
        if($this->units[$unit])
        {
            return $this->units[$unit];
        }

        $unit_name = str_replace('&sup2;', '2', wpl_units::get_unit($unit)['name']);
        $this->units[$unit] = $unit_name;
        return $unit_name;
    }

    /**
     * Getting property field
     * @param $array
     * @param $field
     * @param bool $price
     * @return string
     */
    public function get_field($array, $field, $price = false, $unit = '')
    {
        $return = "";
        if($price)
        {
            if(is_numeric($field))
            {
                $return = $this->money_format($field, $unit);
            }
            else
            {
                $return = $field;
            }
        }
        else
        {
            $return = $field;
        }

        return $return;
    }

    /**
     * Creating map preview section
     * @param $property
     * @return array
     */
    private function create_map_preview_section($property)
    {
        $location = $property['location_text'];
        if(strlen($location) > 25)
        {
            $location = substr($location, 0, 25);
            $location .= "...";
        }

        return array(
            array(
                'section_type'=>'header',
                'text'=>$this->get_field($property, $property['price'], true, $property['price_unit']) //price
            ),
            array(
                'section_type'=>'inline_section',
                'items'=>array(
                    array(
                        'item_type'=>'text',
                        'position'=>'left',
                        'text'=>empty($location) ? '-' : $location //address
                    ),
                    array(
                        'item_type'=>'image',
                        'position'=>'left',
                        'url'=>'ic_bedroom'
                    ),
                    array(
                        'item_type'=>'text',
                        'position'=>'left',
                        'text'=>$this->get_field($property, $property['bedrooms'] )//number of bedroom
                    ),
                    array(
                        'item_type'=>'image',
                        'position'=>'left',
                        'url'=>'ic_bathroom'
                    ),
                    array(
                        'item_type'=>'text',
                        'position'=>'left',
                        'text'=>$this->get_field($property, $property['bathrooms']) //bathroom
                    ),
                )
            )
        );
    }

    /**
     * Create listing section
     * @param $property
     * @return array
     */
    private function create_listing_section($property)
    {
        $location = $property['location_text'];

        if(strlen($location) > 25)
        {
            $location = substr($location, 0, 25);
            $location .= "...";
        }

        return array(
            array(
                'id'=>'1',
                'section_type'=>'header',
                'text'=>$this->get_field($property, $property['price'], true, $property['price_unit']) //price
            ),
            array(
                'section_type'=>'inline_section',
                'items'=>array(
                    array(
                        'id'=>'2',
                        'item_type'=>'text',
                        'position'=>'left',
                        'text'=>empty($location) ? '-' : $location // address
                    ),
                    array(
                        'id'=>'3',
                        'item_type'=>'image',
                        'position'=>'left',
                        'url'=>'ic_bedroom'
                    ),
                    array(
                        'id'=>'4',
                        'item_type'=>'text',
                        'position'=>'left',
                        'text'=>$this->get_field($property, $property['bedrooms']) //bedroom
                    ),
                    array(
                        'id'=>'5',
                        'item_type'=>'image',
                        'position'=>'left',
                        'url'=>'ic_bathroom'
                    ),
                    array(
                        'id'=>'6',
                        'item_type'=>'text',
                        'position'=>'left',
                        'text'=>$this->get_field($property, $property['bathrooms']) //bathroom
                    ),
                )
            )
        );
    }

    /**
     * Merge bubbles with together
     * @param $zoomlevel
     * @param $properties
     * @param float $a
     * @return mixed
     */
    function merge_bubbles($zoomlevel, $properties, $a = 1.1)
    {
        $properties2 = $properties;
        $min_radius = $a/(pow(2, $zoomlevel));

        $merge = array();

        foreach($properties as $key=>$value)
        {
            $merged = false;

            foreach($properties2 as $key2=>$value2)
            {
                /** ignore duplicate bubble **/
                if($key == $key2)
                {
                    unset($properties2[$key2]);
                    continue;
                }

                /** $result = sqrt(pow(($value['lat']-$value2['lat']),2)+pow(($value['lng']-$value2['lng']),2)); **/
                $result = acos( cos( deg2rad($value['lat']) ) * cos( deg2rad( $value2['lat'] ) ) * cos( deg2rad( $value2['lng'] ) - deg2rad($value['lng']) ) + sin( deg2rad($value['lat']) ) * sin( deg2rad( $value2['lat'] ) ) );

                if($result < $min_radius)
                {
                    $merged = true;
                    $merge[] = array($key, $key2);

                    unset($properties2[$key2]);
                }
            }
        }

        foreach($merge as $key=>$value)
        {
            if(!$properties[$value[0]]) continue;

            $count = $properties[$value[0]]['count']+$properties[$value[1]]['count'];
            $av_lat = ($properties[$value[0]]['lat']+$properties[$value[1]]['lat'])/2;
            $av_lng = ($properties[$value[0]]['lng']+$properties[$value[1]]['lng'])/2;

            $nvalue = array();
            $nvalue['count'] = $count;
            $nvalue['lat'] = $av_lat;
            $nvalue['lng'] = $av_lng;

            $properties[$value[0]] = $nvalue;

            unset($properties[$value[1]]);
        }

        rsort($properties);
        return $properties;
    }

    /**
     * Search for bubbles
     * @param $where
     * @param $number
     * @param $zoom_level
     * @return mixed
     */
    public function bubbles_search($where, $number, $zoom_level)
    {
        //$where = str_replace("'", '', $where);
        $multiply_number = (1 / 500) * pow(2.4, $zoom_level);
        $query = "SELECT COUNT(p.id) AS `count`, AVG(p.googlemap_lt) AS `lat`, AVG(p.googlemap_ln) AS `lng`, FORMAT(p.googlemap_lt*" . $multiply_number . "," . $number . ") AS `g_lt`, FORMAT(p.googlemap_ln*" . $multiply_number . "," . $number . ") AS `g_ln`";
        $query .= " FROM #__wpl_properties AS p";
        $query .= ' WHERE 1 ' . $where;
        $query .= " GROUP BY `g_lt`,`g_ln`";
        $query .= " ORDER BY `g_lt`,`g_ln` DESC";

        return wpl_db::select($query, 'loadAssocList');
    }

    /**
     * Getting bubbles
     * @return array
     */
    private function get_bubbles()
    {
        $model = new wpl_property();
        $this->where['sf_select_confirmed'] = '1';
        $this->where['sf_select_finalized'] = '1';
        $this->where['sf_select_deleted'] = '0';

        $model->start($this->start, $this->limit, $this->order_by, $this->order, $this->where);
        $where = $model->where;
        $properties = $this->bubbles_search($where, 0, $this->params['zoomlevel']);

        $results = $this->merge_bubbles($this->params['zoomlevel'], $properties);
        $results_holder = array();

        if(empty($this->polygon))
        {
            return $results;
        }
        else
        {
            foreach($results as $key=>$value)
            {
                if($this->is_property_in_polygon($value['lat'], $value['lng']))
                {
                    $results_holder[] = $value;
                }
            }
        }

        return $results_holder;
    }
}
