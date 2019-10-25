<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.flex');
_wpl_import('libraries.items');
_wpl_import('libraries.images');
_wpl_import('libraries.units');
_wpl_import('libraries.render');
_wpl_import('libraries.locations');
_wpl_import('libraries.property_types');
_wpl_import('libraries.listing_types');

/**
 * Property Library
 * @author Howard R <howard@realtyna.com>
 * @since WPL1.0.0
 * @package WPL
 * @date 05/26/2013
 */
class wpl_property
{
    public $query;
    public $main_table;
    public $start_time;
    public $join_query;
    public $groupby_query;
    public $start;
    public $limit;
    public $orderby;
    public $order;
    public $order_val;
    public $where;
    public $select;
    public $finish_time;
    public $time_taken;
    public $total;
    public $kind;
    public $listing_fields;

    /**
     * Returns property wizard fields
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int|string $category
     * @param int $kind
     * @param int $enabled
     * @return array of objects
     */
	public static function get_pwizard_fields($category = '', $kind = 0, $enabled = 1)
	{
		return wpl_flex::get_fields($category, $enabled, $kind, 'pwizard', '1');
	}
    
    /**
     * Returns property listing fields
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int|string $category
     * @param int $kind
     * @param int $enabled
     * @return array of objects
     */
	public static function get_plisting_fields($category = '', $kind = 0, $enabled = 1)
	{
		return wpl_flex::get_fields($category, $enabled, $kind, 'plisting', '1');
	}
	
    /**
     * Returns property show fields
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int|string $category
     * @param int $kind
     * @param int $enabled
     * @return array of objects
     */
	public static function get_pshow_fields($category = '', $kind = 0, $enabled = 1)
	{
		return wpl_flex::get_fields($category, $enabled, $kind, 'pshow', '1');
	}
    
    /**
     * Returns property PDF fields
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int|string $category
     * @param int $kind
     * @param int $enabled
     * @return array of objects
     */
    public static function get_pdf_fields($category = '', $kind = 0, $enabled = 1)
	{
		return wpl_flex::get_fields($category, $enabled, $kind, 'pdf', '1');
	}
    
    /**
     * Creates default property for listing wizard etc pages
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param int $kind
     * @return int Created Property ID
     */
	public static function create_property_default($user_id = NULL, $kind = 0)
	{
		if(!$user_id) $user_id = wpl_users::get_cur_user_id();

        $fields = wpl_flex::get_fields('', 1, $kind);
	    list($query, $values) = self::generate_default_query($fields, $user_id);

        // Apply Filters
		@extract(wpl_filters::apply('create_default_property', array('query'=>$query, 'values'=>$values, 'user_id'=>$user_id, 'kind'=>$kind)));
		
       	$query = "INSERT INTO  `#__wpl_properties` (".(trim($query) != '' ? $query.", " : '')."`kind`, `user_id`, `finalized`, `add_date`, `mls_id`) VALUES (".(trim($values) != '' ? $values.", " : '')."'$kind', '$user_id', '0', '".date("Y-m-d H:i:s")."', '".self::get_new_mls_id()."')";
        return wpl_db::q($query, 'insert');
    }
	
    /**
     * Generates default query for creating default property
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param array $fields
     * @param int $user_id
     * @return array
     */
	public static function generate_default_query($fields, $user_id)
	{
		$query = '';
		$values = '';
        
        $units1 = wpl_units::get_units(1);
        $units2 = wpl_units::get_units(2);
        $units3 = wpl_units::get_units(3);
        $units4 = wpl_units::get_units(4);
        
        // Default Values
        $unit1 = isset($units1[0]) ? $units1[0]['id'] : NULL;
        $unit2 = isset($units2[0]) ? $units2[0]['id'] : NULL;
        $unit3 = isset($units3[0]) ? $units3[0]['id'] : NULL;
        $unit4 = isset($units4[0]) ? $units4[0]['id'] : NULL;
        
        $country_id = NULL;
        if(wpl_db::num("SELECT COUNT(id) FROM `#__wpl_location1` WHERE `enabled`='1'") == 1) $country_id = wpl_db::select("SELECT `id` FROM `#__wpl_location1` WHERE `enabled`='1' LIMIT 1", 'loadResult');
        
        // Default Values Per Agent
        if(wpl_global::check_addon('aps') and count($user_default_values = wpl_users::get_default_values($user_id)))
        {
            if(isset($user_default_values['country']) and trim($user_default_values['country'])) $country_id = $user_default_values['country'];
            if(isset($user_default_values['unit1']) and trim($user_default_values['unit1'])) $unit1 = $user_default_values['unit1'];
            if(isset($user_default_values['unit2']) and trim($user_default_values['unit2'])) $unit2 = $user_default_values['unit2'];
            if(isset($user_default_values['unit3']) and trim($user_default_values['unit3'])) $unit3 = $user_default_values['unit3'];
            if(isset($user_default_values['unit4']) and trim($user_default_values['unit4'])) $unit4 = $user_default_values['unit4'];
        }

		/** To insert default values for measuring units **/
        foreach($fields as $field)
		{
            if($field->type == 'length' or $field->type == 'mmlength')
			{
                $query .= '`'.$field->table_column.'_unit`, ';
                $values .= "'".$unit1."', ";
            }
            elseif($field->type == 'area' or $field->type == 'mmarea')
			{
                $query .= '`'.$field->table_column.'_unit`, ';
                $values .= "'".$unit2."', ";
            }
            elseif($field->type == 'volume' or $field->type == 'mmvolume')
			{
                $query .= '`'.$field->table_column.'_unit`, ';
                $values .= "'".$unit3."', ";
            }
            elseif($field->type == 'price' or $field->type == 'mmprice')
			{
                $query .= '`'.$field->table_column.'_unit`, ';
                $values .= "'".$unit4."', ";
            }
        }
		
        /** Add default value for geopoints column **/
        if(wpl_global::check_addon('aps'))
        {
            $query .= '`geopoints`, ';
            $values .= "Point(0,0), ";
        }
        
        // Default Country
        if($country_id)
        {
            $country = wpl_db::select("SELECT `id`, `name` FROM `#__wpl_location1` WHERE `id`='".$country_id."'", 'loadAssoc');
            
            $query .= '`location1_id`, `location1_name`, ';
            $values .= "'".$country['id']."', '".$country['name']."', ";
        }
        
		return array(trim($query, ', '), trim($values, ', '));
	}
    
    /**
     * Starts search command
     * @author Howard R <howard@realtyna.com>
     * @param int $start
     * @param int $limit
     * @param string|array $orderby
     * @param string|array $order
     * @param array $where
     * @param int $kind
     */
	public function start($start, $limit, $orderby, $order, $where, $kind = 0)
    {
		// Start time of model
		$this->start_time = microtime(true);
        
        // Fetch Order By and Order Value
        $order_ex = explode(':', $orderby);
        
		$orderby = $order_ex[0];
        $order_val = isset($order_ex[1]) ? $order_ex[1] : '';
        
        if(in_array($orderby, array('p.mls_id+0', 'p.mls_id'))) $orderby = 'p.mls_id_num';
        
        // Advanced Property Type
        if($orderby == 'ptype_adv') $orderby = "(p.`property_type` ".($order == 'ASC' ? '!' : '')."= '".$order_val."'), p.`property_type`";
        
        // Advanced Listing Type
        if($orderby == 'ltype_adv') $orderby = "(p.`listing` ".($order == 'ASC' ? '!' : '')."= '".$order_val."'), p.`listing`";

        // Apply Filters
		@extract(wpl_filters::apply('property_search_orderby', array('orderby'=>$orderby, 'order'=>$order)));
        @extract(wpl_filters::apply('property_search_order', array('order'=>$order, 'orderby'=>$orderby)));
        
		// Pagination and Order Options
		$this->start = $start;
		$this->limit = $limit;
        $this->order = $order;
		$this->orderby = $orderby;
        $this->order_val = $order_val;
        $this->kind = $kind;

        // Apply Maximum Search Result
        if($max = wpl_property::get_maximum_search_results() and ($this->start+$this->limit) > $max)
        {
            $this->limit = max(($max - $this->start), 0);
        }
		
		// Listing Fields
		$this->listing_fields = $this->get_plisting_fields('', $this->kind);
		
		// Main Table
		$this->main_table = "`#__wpl_properties` AS p";
		
		// Queries
		$this->join_query = $this->create_join();
		$this->groupby_query = $this->create_groupby();
		
		// Generate Where Condition
		$where = (array) $where;
		$this->where = wpl_db::create_query($where);
		
		// Generate Select
		$this->select = isset($this->select) ? $this->select : $this->generate_select($this->listing_fields, 'p');
    }
	
    /**
     * Creates complete query for searching
     * @author Howard R <howard@realtyna.com>
     * @param boolean $calc_rows
     * @return string
     */
	public function query($calc_rows = true)
    {
        $this->query  = " SELECT ".$this->select;
        $this->query .= " FROM ".$this->main_table;
        $this->query .= $this->join_query;
		$this->query .= " WHERE 1 ".$this->where;
		$this->query .= $this->groupby_query;
        $this->query .= " ORDER BY ".$this->orderby." ".$this->order;
        $this->query .= " LIMIT ".$this->start.", ".$this->limit;
		$this->query  = trim($this->query, ', ');
		
		return $this->query;
    }
    
    /**
     * @author Howard R <howard@realtyna.com>
     * @todo
     * @return string
     */
	public function create_join()
	{
		return '';
	}
    
    /**
     * @author Howard R <howard@realtyna.com>
     * @todo
     * @return string
     */
	public function create_groupby()
	{
		return '';
	}
    
    /**
     * Searches on properties
     * @author Howard R <howard@realtyna.com>
     * @param string $query
     * @return array of objects
     */
	public function search($query = '')
    {
        if(!trim($query)) $query = $this->query;
        
        return wpl_db::select($query);
    }
    
    /**
     * Calculates token time and results count
     * @author Howard R <howard@realtyna.com>
     * @static
     * @return int
     */
	public function finish($calccount = 1)
	{
		$this->finish_time = microtime(true);
        $this->time_taken = $this->finish_time - $this->start_time;

        if($calccount) $this->total = $this->get_properties_count();
		
        /**
         * We're using old method for finding total amount of listings
         * $this->total = wpl_db::select('SELECT FOUND_ROWS()', 'loadResult');
         */
        
        // Apply Maximum Search Result
        if($max = wpl_property::get_maximum_search_results() and $this->total > $max)
        {
            $this->total = $max;
        }
        
        if($this->orderby == 'p.mls_id_num') $this->orderby = 'p.mls_id';
        
		return $this->time_taken;
	}
	
    /**
     * Generates select cluase of search query
     * @author Howard R <howard@realtyna.com>
     * @param array $fields
     * @param string $table_name
     * @return string|boolean
     */
	public function generate_select($fields, $table_name = 'p')
	{
		/** first validation **/
		if(!$fields) return false;
		
		/** get files **/
		$path = WPL_ABSPATH .DS. 'libraries' .DS. 'query_select';
        $files = wpl_folder::files($path, '.php$');
		$query = '';
		
		$defaults = array('id', 'kind', 'pic_numb', 'att_numb', 'confirmed', 'finalized', 'deleted', 'user_id', 'add_date', 'visit_time', 'visit_date', 'sent_numb', 'contact_numb', 'zip_name', 'zip_id', 'location_text');
		foreach($defaults as $default)
		{
			$query .= $table_name.".`".$default."`, ";
		}
		
		foreach($fields as $key=>$field)
		{
			if(!$field) continue;
			if(trim($field->table_name) == '' or trim($field->table_column) == '') continue;
			
			$done_this = false;
			$type = $field->type;
			
			foreach($files as $file)
			{
				include($path .DS. $file);
				
				/** break and go to next field **/
				if($done_this) break;
			}
			
			if(!$done_this) $query .= $table_name.".`{$field->table_column}`, ";
		}
		
		return trim($query, ', ');
	}
	
    /**
     * Returns new unique id for mls_id column of properties table
     * @author Howard R <howard@realtyna.com>
     * @static
     * @return int
     */
	public static function get_new_mls_id()
	{
        $query = "SELECT MAX(cast(mls_id AS unsigned)) as max_id FROM #__wpl_properties WHERE mls_id REGEXP '^[0-9]+$' LIMIT 1";
		$result = wpl_db::select($query, 'loadResult');
		
        if(!$result) $mls_id = 1000;
		else $mls_id = $result+1;
		
        return $mls_id;
    }
	
    /**
     * Get raw data of a listing
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param string $output_type
     * @return mixed
     */
	public static function get_property_raw_data($property_id, $output_type = 'loadAssoc')
	{
        // First Validation
        if(!$property_id) return NULL;

        $query = "SELECT * FROM `#__wpl_properties` WHERE `id`='$property_id'";
        return wpl_db::select($query, $output_type);
    }

    /**
     * Get cached raw data of a listing
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @return mixed
     */
    public static function get_property_cached_data($property_id)
    {
        // Return From Cache
        return wp_cache_get('wpl_p_raw_data_'.$property_id);
    }
    
    /**
     * Renders Property data (And User data and other entities)
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param array $property
     * @param array $fields
     * @param array $finds
     * @param boolean $material
     * @return array
     */
	public static function render_property($property, $fields, &$finds = array(), $material = false)
	{
		$rendered = array();
		$materials = array();
        
		$path = WPL_ABSPATH .DS. 'libraries' .DS. 'dbst_show';
		$files = wpl_folder::files($path, '.php$', false, false);
		$values = (array) $property;
		
        $prp_listing = isset($values['listing']) ? $values['listing'] : NULL;
        $prp_property_type = isset($values['property_type']) ? $values['property_type'] : NULL;
        
		foreach($fields as $key=>$field)
		{
			if(trim($field->type) == '') continue;
			
            /** Take care for property type specific **/
            if(trim($field->property_type_specific) and $prp_property_type)
            {
                $ex = explode(',', $field->property_type_specific);
                if(!in_array($prp_property_type, $ex))
                {
                    $values[$field->table_column] = NULL;
                    continue;
                }
            }
            
            /** Take care for listing type specific **/
            if(trim($field->listing_specific) and $prp_listing)
            {
                $ex = explode(',', $field->listing_specific);
                if(!in_array($prp_listing, $ex))
                {
                    $values[$field->table_column] = NULL;
                    continue;
                }
            }
            
            /** Take care for field specific **/
            if(isset($field->field_specific) and trim($field->field_specific))
            {
                $ex = explode(':', $field->field_specific);
                $parent_field = wpl_flex::get_field($ex[0]);

                if(isset($parent_field) and $values[$parent_field->table_column] != $ex[1])
                {
                    $values[$field->table_column] = NULL;
                    continue;
                }
            }
            
            /** Accesses **/
			if(isset($field->accesses) and trim($field->accesses) != '' and wpl_global::check_addon('membership'))
			{
				$accesses = explode(',', trim($field->accesses, ', '));
                $cur_membership_id = wpl_users::get_user_membership();
                
				if(!in_array($cur_membership_id, $accesses) and trim($field->accesses_message) == '') continue;
                elseif(!in_array($cur_membership_id, $accesses) and trim($field->accesses_message) != '')
                {
                    $rendered[$field->id] = array('field_id'=>$field->id, 'type'=>$field->type, 'name'=>__($field->name, 'real-estate-listing-realtyna-wpl'), 'value'=>__($field->accesses_message, 'real-estate-listing-realtyna-wpl'));
                    if($material and trim($field->table_column) != '') $materials[$field->table_column] = array('field_id'=>$field->id, 'type'=>$field->type, 'name'=>__($field->name, 'real-estate-listing-realtyna-wpl'), 'value'=>__($field->accesses_message, 'real-estate-listing-realtyna-wpl'));

                    continue;
                }
			}
            
			$value = isset($values[$field->table_column]) ? stripslashes($values[$field->table_column]) : NULL;
            
			$done_this = false;
			$type = $field->type;
			$options = json_decode($field->options, true);
			$return = array();
			
			/** use detected files **/
			if(isset($finds[$type]))
			{
                include($path .DS. $finds[$type]);

                if(is_array($return) and count($return))
                {
                    $rendered[$field->id] = $return;
                    if($material and trim($field->table_column) != '') $materials[$field->table_column] = $return;
                }
                
                continue;
			}
            
			foreach($files as $file)
			{
				require $path.DS.$file;
				
				if($done_this == true)
				{
					/** set in detected files and proceed to next field **/
					$finds[$type] = $file;
					break;
				}
			}
			
			if(is_array($return) and count($return))
            {
                $rendered[$field->id] = $return;
                if($material and trim($field->table_column) != '') $materials[$field->table_column] = $return;
            }
			
			if(!$done_this)
			{
				$rendered[$field->id] = array('field_id'=>$field->id, 'type'=>$field->type, 'name'=>__($field->name, 'real-estate-listing-realtyna-wpl'), 'value'=>$value);
                if($material and trim($field->table_column) != '') $materials[$field->table_column] = array('field_id'=>$field->id, 'type'=>$field->type, 'name'=>__($field->name, 'real-estate-listing-realtyna-wpl'), 'value'=>$value);
			}
		}
		
        /** returns rendered data by field ids and table columns **/
        if($material) return array('ids'=>$rendered, 'columns'=>$materials);
        
		return $rendered;
	}
    
    /**
     * Renders one dbst field
     * @author Howard R <howard@realtyna.com>
     * @param string $value
     * @param int $dbst_id
     * @param int $property_id
     * @return array rendered field
     */
    public static function render_field($value, $dbst_id, $property_id = NULL)
    {
        /** first validation **/
        if(!$dbst_id) return array();
        
		$done_this = false;
        $return = array();
        
		$path = WPL_ABSPATH .DS. 'libraries' .DS. 'dbst_show';
		$files = wpl_folder::files($path, '.php$', false, false);
        $field = wpl_flex::get_field($dbst_id);
        $value = stripslashes($value);

        $values = array();
        if($property_id) $values = wpl_property::get_property_raw_data($property_id);
        
        $type = $field->type;
        $options = json_decode($field->options, true);

		foreach($files as $file)
        {
            require $path.DS.$file;
            if($done_this == true) break;
        }
        
        /** Accesses **/
        if(trim($field->accesses) != '')
        {
            $accesses = explode(',', trim($field->accesses, ', '));
            $cur_membership_id = wpl_users::get_user_membership();

            if(!in_array($cur_membership_id, $accesses) and trim($field->accesses_message) == '') return array();
            elseif(!in_array($cur_membership_id, $accesses) and trim($field->accesses_message) != '')
            {
                $return = array('field_id'=>$field->id, 'type'=>$field->type, 'name'=>__($field->name, 'real-estate-listing-realtyna-wpl'), 'value'=>__($field->accesses_message, 'real-estate-listing-realtyna-wpl'));
            }
        }
		
		return $return;
    }
    
    /**
     * Render Google markers
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $wpl_properties
     * @return array
     */
    public static function render_markers($wpl_properties)
    {
        $listings = wpl_global::return_in_id_array(wpl_global::get_listings());

        $markers = array();
        $geo_points = array();
        $rendered = array();
        $cookie_unit = wpl_request::getVar('wpl_unit4', 0, 'COOKIE');
        $cur_user_membership = wpl_users::get_user_membership();
        
        $multiple_marker_icon = wpl_global::get_setting('multiple_marker_icon');
        if(trim($multiple_marker_icon) == '') $multiple_marker_icon = 'multiple.png';
        
        // Advanced Markers
        $advanced_markers_json = wpl_global::get_setting('advanced_markers');
        
        $advanced_markers = json_decode($advanced_markers_json, true);
        if(!is_array($advanced_markers)) $advanced_markers = array();
        
        // Advanced Markers Status
        $advanced_markers_status = false;
        if(isset($advanced_markers['status']) and $advanced_markers['status']) $advanced_markers_status = true;
        
        $i = 0;
        foreach($wpl_properties as $key=>$property)
        {
            if($key == 'current' and !count($property)) continue;
            
            // Location visibility
            $location_visibility = wpl_property::location_visibility($property['raw']['id'], $property['raw']['kind'], $cur_user_membership);
            
            // Skip to next if address is hidden
            if($location_visibility !== true) continue;
            
            // if property already rendered
            if(in_array($property['raw']['id'], $rendered)) continue;
            array_push($rendered, $property['raw']['id']);
            
            // Fix Latitude and longitude
            $property['raw']['googlemap_lt'] = (double) $property['raw']['googlemap_lt'];
            $property['raw']['googlemap_ln'] = (double) $property['raw']['googlemap_ln'];
            
            // Fetch latitude and longitude if it's not set
            if(!$property['raw']['googlemap_lt'] or !$property['raw']['googlemap_ln'])
            {
                $LatLng = wpl_locations::update_LatLng($property['raw']);

                $property['raw']['googlemap_lt'] = (double) $LatLng[0];
                $property['raw']['googlemap_ln'] = (double) $LatLng[1];
            }
            
            // Still geo-point is not available so we will hide the marker
            if((!$property['raw']['googlemap_lt'] or !$property['raw']['googlemap_ln']) and wpl_global::get_setting('hide_invalid_markers')) continue;
            
            // Create multiple marker
            if(isset($geo_points[$property['raw']['googlemap_lt'].','.$property['raw']['googlemap_ln']]))
            {
                $j = $geo_points[$property['raw']['googlemap_lt'].','.$property['raw']['googlemap_ln']];
                
                $markers[$j]['pids'] .= ','.$property['raw']['id'];
                $markers[$j]['gmap_icon'] = $multiple_marker_icon;
                
                // Advanced Marker
                if($advanced_markers_status) $markers[$j]['advanced_marker'] = '<img src="'.wpl_global::get_wpl_asset_url('img/listing_types/gicon/'.$multiple_marker_icon).'">';
            
                continue;
            }
            
            $markers[$i]['id'] = $property['raw']['id'];
            $markers[$i]['googlemap_lt'] = $property['raw']['googlemap_lt'];
            $markers[$i]['googlemap_ln'] = $property['raw']['googlemap_ln'];

            // Generate Title
            if($cookie_unit and $cookie_unit != $property['raw']['price_unit'])
            {
                $price = wpl_units::convert($property['raw']['price'], $property['raw']['price_unit'], $cookie_unit);
                $markers[$i]['title'] = wpl_render::render_price($price, $cookie_unit, '', wpl_global::wpl_minimize_price($price));
            }
            else $markers[$i]['title'] = wpl_render::render_price($property['raw']['price'], $property['raw']['price_unit'], '', wpl_global::wpl_minimize_price($property['raw']['price']));
            
            $markers[$i]['pids'] = $property['raw']['id'];
	        $markers[$i]['gmap_icon'] = (isset($listings[$property['raw']['listing']]['gicon']) and $listings[$property['raw']['listing']]['gicon']) ? $listings[$property['raw']['listing']]['gicon'] : 'default.png';

            // Advanced Marker
            if($advanced_markers_status)
            {
                $color = (isset($advanced_markers['listing_types'][$property['raw']['listing']])) ? $advanced_markers['listing_types'][$property['raw']['listing']] : '#333333';
                $icon = (isset($advanced_markers['property_types'][$property['raw']['property_type']])) ? $advanced_markers['property_types'][$property['raw']['property_type']] : 'residential.svg';
                
                $richmarker = '<div class="wpl-richmarker-wp" style="color: '.$color.';">';
                $richmarker .= '<div class="wpl-richmarker-icon"><img src="'.wpl_global::get_wpl_asset_url('img/property_types/'.$icon).'"></div>';
                $richmarker .= '</div>';
                
                $markers[$i]['advanced_marker'] = $richmarker;
            }
            
	        $geo_points[$property['raw']['googlemap_lt'].','.$property['raw']['googlemap_ln']] = $i;
            
            $i++;
        }

        // Apply Filters
		@extract(wpl_filters::apply('render_property_markers', array('markers'=>$markers, 'wpl_properties'=>$wpl_properties)));
        
        return $markers;
    }
    
    /**
     * Returns number of properties according to query condition
     * @author Francis <francis@realtyna.com>
     * @param string $condition
     * @return mixed
     */
    public function get_properties_count($condition = '')
    {
		$condition = trim($condition) != '' ? $condition : $this->where;
		
        $query = "SELECT COUNT(*) AS count FROM `#__wpl_properties` WHERE 1 ".$condition;
        return wpl_db::select($query, 'loadResult');
    }
	
    /**
     * Finalize a property and render needed data
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param string $mode
     * @param int $user_id
     * @return boolean
     */
	public static function finalize($property_id, $mode = 'edit', $user_id = NULL)
	{
        // Import Library
        _wpl_import('libraries.property.finalize');

        // Finalize
        $finalize = new wpl_property_finalize($property_id, $mode, $user_id);
        return $finalize->start();
    }
	
    /**
     * Unfinalize a property
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @return boolean
     */
	public static function unfinalize($property_id)
	{
        $query = "UPDATE `#__wpl_properties` SET `finalized`='0' WHERE `id`='$property_id'";
		wpl_db::q($query, 'update');
		
        /** throwing events **/
        wpl_events::trigger('property_unfinalized', $property_id);
		
		return true;
    }
	
    /**
     * Generates finalize query of property converts units to SI units etc.
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $data
     * @param int $id
     * @return string
     */
	public static function generate_finalize_query($data, $id = NULL)
	{
        $units = wpl_global::return_in_id_array(wpl_units::get_units('', 1));
        $query = '';
		
        foreach($data as $field=>$value)
		{
            if(!strstr($field, '_unit')) continue;
            if(!isset($units[$value])) continue;
			
			$core_field = str_replace('_unit', '', $field);
            if(!array_key_exists($core_field.'_si', $data)) continue;
            if(!isset($data[$core_field])) continue;
			
			$si_value = $units[$value]['tosi'] * $data[$core_field];
			$query .= "`".$core_field."_si`='".$si_value."',";

			if(isset($data[$core_field.'_max']))
			{
				$si_value = $units[$value]['tosi'] * $data[$core_field.'_max'];
				$query .= "`".$core_field."_max_si`='".$si_value."',";
			}
        }
		
		return $query;
	}
	
    /**
     * Generate rendered data of a property
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $pid
     * @param array $plisting_fields
     * @return string
     */
	public static function generate_rendered_data($pid, $plisting_fields = NULL)
	{
		// Get Property Data
		$property_data = wpl_property::get_property_raw_data($pid);

        if(!$plisting_fields) $plisting_fields = self::get_plisting_fields('', $property_data['kind']);
		
		// Location Text
		$location_text = self::generate_location_text($property_data, NULL, ',', true);
        
        // Rendered Data
        $find_files = array();
		$rendered_fields = self::render_property($property_data, $plisting_fields, $find_files, true);
        
		$result = json_encode(array('rendered'=>$rendered_fields['ids'], 'materials'=>$rendered_fields['columns'], 'location_text'=>$location_text));
        
        $column = 'rendered';
        if(wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        
		$query = "UPDATE `#__wpl_properties` SET `$column`='".wpl_db::escape($result)."' WHERE `id`='$pid'";
		
		/** update **/
		wpl_db::q($query, 'update');
        return $result;
	}
	
    /**
     * Updates picture count, attachment count etc.
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param array $property_data
     */
	public static function update_numbs($property_id, $property_data = NULL)
	{
		/** get property data if not provided **/
		if(!$property_data) $property_data = wpl_property::get_property_raw_data($property_id);
		
        $items = wpl_items::get_items($property_id, '', $property_data['kind'], '', 1);
		
		$pic_numb = isset($items['gallery']) ? count($items['gallery']) : 0;
		$att_numb = isset($items['attachment']) ? count($items['attachment']) : 0;
		
		$query = "UPDATE `#__wpl_properties` SET `pic_numb`='$pic_numb', `att_numb`='$att_numb' WHERE `id`='$property_id'";
		wpl_db::q($query);
    }
	
    /**
     * Updates text search field
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     */
	public static function update_text_search_field($property_id)
	{
        $property_data = wpl_property::get_property_raw_data($property_id);
		$kind = wpl_property::get_property_kind($property_id);
        
		/** get text_search fields **/
		$fields = wpl_flex::get_fields('', 1, $property_data['kind'], 'text_search', '1');
		$rendered = self::render_property($property_data, $fields);
        
		$text_search_data = array();
		foreach($rendered as $data)
		{
			if(!isset($data['type'])) continue;
			if((isset($data['type']) and !trim($data['type'])) or (isset($data['value']) and !trim($data['value']))) continue;
			
			/** default value **/
			$value = isset($data['value']) ? $data['value'] : '';
			$value2 = '';
			$type = $data['type'];
			
			if($type == 'text' or $type == 'textarea')
			{
				$value = $data['name'] .' '. $data['value'];
			}
			elseif($type == 'neighborhood')
			{
				$value = $data['name'] .(isset($data['distance']) ? ' ('. $data['distance'] .' '. __('MINUTES', 'real-estate-listing-realtyna-wpl') .' '. __('BY', 'real-estate-listing-realtyna-wpl') .' '. $data['by'] .')' : '');
			}
			elseif($type == 'feature')
			{
				$feature_value = $data['name'];
				
				if(isset($data['values'][0]))
				{
					$feature_value .= ' ';
					
					foreach($data['values'] as $val) $feature_value .= $val .', ';
					$feature_value = rtrim($feature_value, ', ');
				}
				
				$value = $feature_value;
			}
			elseif($type == 'locations' and isset($data['locations']) and is_array($data['locations']))
			{
                $location_values = array();
				foreach($data['locations'] as $location_level=>$value)
				{
                    array_push($location_values, $data['keywords'][$location_level]);
                    
                    $location_name = stripslashes_deep($data['raw'][$location_level]);
                    $abbr = wpl_locations::get_location_abbr_by_name(wpl_db::escape($location_name), $location_level);
                    $name = wpl_locations::get_location_name_by_abbr(wpl_db::escape($abbr), $location_level);
                    
                    $ex_space = explode(' ', stripslashes_deep($name));
                    foreach($ex_space as $value_raw) array_push($location_values, stripslashes_deep($value_raw));

                    if($name !== $abbr)
                    {
                        array_push($location_values, stripslashes_deep($abbr));

                        if($abbr == 'US') array_push($location_values, 'USA');
                        elseif($abbr == 'USA') array_push($location_values, 'US');
                    }
				}
                
                /** Add all location fields to the location text search **/
                $location_category = wpl_flex::get_category(NULL, " AND `kind`='$kind' AND `prefix`='ad'");
                $location_fields = wpl_flex::get_fields($location_category->id, 1, $kind);
                
                foreach($location_fields as $location_field)
                {
                    if(!isset($rendered[$location_field->id])) continue;
                    if(!trim($location_field->table_column)) continue;
                    if(!isset($rendered[$location_field->id]['value']) or (isset($rendered[$location_field->id]['value']) and !trim($rendered[$location_field->id]['value']))) continue;
                    
                    $ex_space = explode(' ', strip_tags($rendered[$location_field->id]['value']));
                    foreach($ex_space as $value_raw) array_push($location_values, stripslashes_deep($value_raw));
                }
                
                $location_suffix_prefix = wpl_locations::get_location_suffix_prefix();
                foreach($location_suffix_prefix as $suffix_prefix) array_push($location_values, $suffix_prefix);
                
                $location_string = '';
                $location_values = array_unique($location_values);
                foreach($location_values as $location_value) $location_string .= 'LOC-'.__($location_value, 'real-estate-listing-realtyna-wpl').' ';
                
				$value = trim($location_string);
			}
			elseif(isset($data['value']))
			{
				$value = $data['name'] .' '. $data['value'];
				if(is_numeric($data['value']))
				{
					$value2 = $data['name'] .' '. wpl_global::number_to_word($data['value']);
				}
			}
			
			/** set value in text search data **/
			if(trim($value) != '') $text_search_data[] = strip_tags($value);
			if(trim($value2) != '') $text_search_data[] = strip_tags($value2);
		}
        
        $column = 'textsearch';
        if(wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        
		wpl_db::set('wpl_properties', $property_id, $column, wpl_db::escape(implode(' ', $text_search_data)));
    }
    
    /**
     * Returns property page link
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $property_data
     * @param int $property_id
     * @param int $target_id
     * @return string
     */
	public static function get_property_link($property_data, $property_id = 0, $target_id = 0)
	{
		/** fetch property data if property id is setted **/
		if($property_id) $property_data = self::get_property_raw_data($property_id);
		if(!$property_id) $property_id = $property_data['id'];
        
        $url = wpl_sef::get_wpl_permalink(true);
        
        $alias_column = 'alias';
        $alias_field = wpl_flex::get_field_by_column($alias_column, $property_data['kind']);
        
        if(isset($alias_field->multilingual) and $alias_field->multilingual and wpl_global::check_multilingual_status()) $alias_column = wpl_addon_pro::get_column_lang_name($alias_column, wpl_global::get_current_language(), false);
        
		if(trim($property_data[$alias_column]) != '') $alias = urlencode($property_data[$alias_column]);
		else $alias = urlencode(self::update_alias($property_data, NULL));
		
        $home_type = wpl_global::get_wp_option('show_on_front', 'posts');
        $home_id = wpl_global::get_wp_option('page_on_front', 0);
        
		if(!$target_id) $target_id = wpl_request::getVar('wpltarget', 0);
		if($target_id)
        {
            $url = wpl_global::add_qs_var('pid', $property_id, wpl_sef::get_page_link($target_id));
            $url = wpl_global::add_qs_var('alias', $alias, $url);
            
            $url = wpl_global::add_qs_var('wplview', 'property_show', $url);
        }
		else
        {
            $nosef = wpl_sef::is_permalink_default();
            $wpl_main_page_id = wpl_sef::get_wpl_main_page_id();
            
            if($nosef or ($home_type == 'page' and $home_id == $wpl_main_page_id))
            {
                $url = wpl_global::add_qs_var('wplview', 'property_show', $url);
                $url = wpl_global::add_qs_var('pid', $property_id, $url);
                $url = wpl_global::add_qs_var('alias', $alias, $url);
            }
            else
            {
                $url .= $property_id.'-'.$alias.'/';
            }
        }
		
        return $url;
    }
    
    /**
     * Returns PDF link of property
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param int $target_id
     * @return string|boolean
     */
	public static function get_property_pdf_link($property_id, $target_id = 0)
	{
        /** first validation **/
        if(!trim($property_id)) return false;
        
        $nosef = wpl_sef::is_permalink_default();
        
        $home_type = wpl_global::get_wp_option('show_on_front', 'posts');
        $home_id = wpl_global::get_wp_option('page_on_front', 0);
        $wpl_main_page_id = wpl_sef::get_wpl_main_page_id();
        
        if($nosef or ($home_type == 'page' and $home_id == $wpl_main_page_id))
        {
            $url = wpl_sef::get_wpl_permalink(true);
            $url = wpl_global::add_qs_var('wplview', 'features', $url);
            $url = wpl_global::add_qs_var('wpltype', 'pdf', $url);
            $url = wpl_global::add_qs_var('pid', $property_id, $url);
        }
        else $url = wpl_sef::get_wpl_permalink(true).'features/pdf?pid='.$property_id;
        
        return $url;
    }
    
    /**
     * Returns listing page link
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $target_id
     * @return string
     */
	public static function get_property_listing_link($target_id = 0)
	{
        if($target_id) $url = wpl_sef::get_page_link($target_id);
        else $url = wpl_sef::get_wpl_permalink(true);
        
        return $url;
    }
	
    /**
     * Generates proeprty location text
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $property_data
     * @param int $property_id
     * @param string $glue
     * @param boolean $force
     * @param boolean $only_return
     * @return string
     */
	public static function generate_location_text($property_data, $property_id = 0, $glue = ',', $force = false, $only_return = false)
	{
		/** fetch property data if property id is setted **/
		if($property_id) $property_data = self::get_property_raw_data($property_id);
        if(!$property_id) $property_id = $property_data['id'];
        
        /** Return hidex_keyword if address of property is hidden **/
        if(isset($property_data['show_address']) and !$property_data['show_address'])
        {
            $location_hidden_keyword = wpl_global::get_setting('location_hidden_keyword', 3);
            return __($location_hidden_keyword, 'real-estate-listing-realtyna-wpl');
        }
        
        $column = 'location_text';
        $field = wpl_flex::get_field_by_column($column, $property_data['kind']);
        
        $base_column = NULL;
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status())
        {
            $base_column = wpl_global::get_current_language() == wpl_addon_pro::get_default_language() ? $column : NULL;
            $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        }
        
        /** return current location text if exists **/
        if(isset($property_data[$column]) and trim($property_data[$column]) != '' and !$force) return $property_data[$column];
        
		$locations = array();
        
        $street_no_column = 'street_no';
        if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($street_no_column, $property_data['kind'])) $street_no_column = wpl_addon_pro::get_column_lang_name($street_no_column, wpl_global::get_current_language(), false);
		if(isset($property_data[$street_no_column]) and trim($property_data[$street_no_column]) != '') $locations['street_no'] = __($property_data[$street_no_column], 'real-estate-listing-realtyna-wpl');
        
        $street_column = 'field_42';
        if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($street_column, $property_data['kind'])) $street_column = wpl_addon_pro::get_column_lang_name($street_column, wpl_global::get_current_language(), false);
        if(isset($property_data[$street_column]) and trim($property_data[$street_column]) != '') $locations['street'] = __($property_data[$street_column], 'real-estate-listing-realtyna-wpl');
        
        $street_suffix_column = 'street_suffix';
        if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($street_suffix_column, $property_data['kind'])) $street_suffix_column = wpl_addon_pro::get_column_lang_name($street_suffix_column, wpl_global::get_current_language(), false);
        if(isset($property_data[$street_suffix_column]) and trim($property_data[$street_suffix_column]) != '') $locations['street_suffix'] = __($property_data[$street_suffix_column], 'real-estate-listing-realtyna-wpl');
        
        if(isset($property_data['location7_name']) and trim($property_data['location7_name']) != '') $locations['location7_name'] = __($property_data['location7_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['location6_name']) and trim($property_data['location6_name']) != '') $locations['location6_name'] = __($property_data['location6_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['location5_name']) and trim($property_data['location5_name']) != '') $locations['location5_name'] = __($property_data['location5_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['location4_name']) and trim($property_data['location4_name']) != '') $locations['location4_name'] = __($property_data['location4_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['location3_name']) and trim($property_data['location3_name']) != '') $locations['location3_name'] = __($property_data['location3_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['location2_name']) and trim($property_data['location2_name']) != '') $locations['location2_name'] = __($property_data['location2_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['zip_name']) and trim($property_data['zip_name']) != '') $locations['zip_name'] = __($property_data['zip_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['location1_name']) and trim($property_data['location1_name']) != '') $locations['location1_name'] = __($property_data['location1_name'], 'real-estate-listing-realtyna-wpl');

        // Location Abbr Names
        if(isset($property_data['location1_name']) and trim($property_data['location1_name'])) $locations['location1_abbr'] = __(wpl_locations::get_location_abbr_by_name($property_data['location1_name'], 1), 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['location2_name']) and trim($property_data['location2_name'])) $locations['location2_abbr'] = __(wpl_locations::get_location_abbr_by_name($property_data['location2_name'], 2), 'real-estate-listing-realtyna-wpl');
        
        // Get the pattern
        $default_pattern = '[street_no] [street] [street_suffix][glue] [location4_name][glue] [location2_abbr] [zip_name]';
        $location_pattern = wpl_global::get_pattern('property_location_pattern', $default_pattern, $property_data['kind'], $property_data['property_type']);
        
        $location_text = wpl_global::render_pattern($location_pattern, $property_id, $property_data, $glue, $locations);

        // Apply Filters
		@extract(wpl_filters::apply('generate_property_location_text', array('location_text'=>$location_text, 'glue'=>$glue, 'property_data'=>$property_data)));
        
        $final = '';
        $ex = explode($glue, $location_text);
        
        foreach($ex as $value)
        {
            if(trim($value) == '') continue;
            $final .= trim($value).$glue.' ';
        }
        
        $location_text = trim($final, $glue.' ');

        // Return and don't save it
        if($only_return) return $location_text;
        
        /** update **/
		$query = "UPDATE `#__wpl_properties` SET `$column`='".wpl_db::escape($location_text)."' WHERE `id`='".$property_id."'";
		wpl_db::q($query, 'update');
        
        if($base_column)
        {
            $query = "UPDATE `#__wpl_properties` SET `$base_column`='".wpl_db::escape($location_text)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
		return $location_text;
    }
	
    /**
     * Generates alias of property
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $property_data
     * @param int $property_id
     * @param string $glue
     * @param boolean $force
     * @param boolean $only_return
     * @return string
     */
	public static function update_alias($property_data, $property_id = 0, $glue = '-', $force = false, $only_return = false)
	{
		// Fetch property data if property id is setted
		if($property_id) $property_data = self::get_property_raw_data($property_id);
        if(!$property_id) $property_id = $property_data['id'];
        
        $column = 'alias';
        $field = wpl_flex::get_field_by_column($column, $property_data['kind']);
        $base_column = NULL;
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status())
        {
            $base_column = wpl_global::get_current_language() == wpl_addon_pro::get_default_language() ? $column : NULL;
            $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        }
        
        // Return current alias if exists
        if(isset($property_data[$column]) and trim($property_data[$column]) != '' and !$force) return $property_data[$column];
        
		$alias = array();
		$alias['id'] = $property_id;
        
		if(trim($property_data['property_type'])) $alias['property_type'] = __(wpl_global::get_property_types($property_data['property_type'])->name, 'real-estate-listing-realtyna-wpl');
		if(trim($property_data['listing'])) $alias['listing'] = __(wpl_global::get_listings($property_data['listing'])->name, 'real-estate-listing-realtyna-wpl');
        
        if(trim($property_data['location1_name'])) $alias['location1'] = __($property_data['location1_name'], 'real-estate-listing-realtyna-wpl');
        if(trim($property_data['location2_name'])) $alias['location2'] = __($property_data['location2_name'], 'real-estate-listing-realtyna-wpl');
        if(trim($property_data['location3_name'])) $alias['location3'] = __($property_data['location3_name'], 'real-estate-listing-realtyna-wpl');
        if(trim($property_data['location4_name'])) $alias['location4'] = __($property_data['location4_name'], 'real-estate-listing-realtyna-wpl');
        if(trim($property_data['location5_name'])) $alias['location5'] = __($property_data['location5_name'], 'real-estate-listing-realtyna-wpl');
        if(trim($property_data['zip_name'])) $alias['zipcode'] = __($property_data['zip_name'], 'real-estate-listing-realtyna-wpl');

        // Location Abbr Names
        if(isset($property_data['location1_name']) and trim($property_data['location1_name'])) $alias['location1_abbr'] = __(wpl_locations::get_location_abbr_by_name($property_data['location1_name'], 1), 'real-estate-listing-realtyna-wpl');
        if(isset($property_data['location2_name']) and trim($property_data['location2_name'])) $alias['location2_abbr'] = __(wpl_locations::get_location_abbr_by_name($property_data['location2_name'], 2), 'real-estate-listing-realtyna-wpl');
        
		$alias['location'] = self::generate_location_text($property_data, NULL, '-', false, $only_return);
		
		if(trim($property_data['rooms'])) $alias['rooms'] = $property_data['rooms'].' '.($property_data['rooms'] > 1 ? __('Rooms', 'real-estate-listing-realtyna-wpl') : __('Room', 'real-estate-listing-realtyna-wpl'));
		if(trim($property_data['bedrooms'])) $alias['bedrooms'] = $property_data['bedrooms'].' '.($property_data['bedrooms'] > 1 ? __('Bedrooms', 'real-estate-listing-realtyna-wpl') : __('Bedroom', 'real-estate-listing-realtyna-wpl'));
		if(trim($property_data['bathrooms'])) $alias['bathrooms'] = $property_data['bathrooms'].' '.($property_data['bathrooms'] > 1 ? __('Bathrooms', 'real-estate-listing-realtyna-wpl') : __('Bathroom', 'real-estate-listing-realtyna-wpl'));
		if(trim($property_data['mls_id'])) $alias['listing_id'] = $property_data['mls_id'];
        
		$unit_data = wpl_units::get_unit($property_data['price_unit']);
		if(trim($property_data['price'])) $alias['price'] = str_replace('.', '', wpl_render::render_price($property_data['price'], $unit_data['id'], $unit_data['extra']));
        
        // Get the pattern
        $default_pattern = '[property_type][glue][listing_type][glue][location][glue][rooms][glue][bedrooms][glue][bathrooms][glue][price]';
        $alias_pattern = wpl_global::get_pattern('property_alias_pattern', $default_pattern, $property_data['kind'], $property_data['property_type']);
        
        $alias_str = wpl_global::render_pattern($alias_pattern, $property_id, $property_data, $glue, $alias);

        // Apply Filters
		@extract(wpl_filters::apply('generate_property_alias', array('alias'=>$alias, 'alias_str'=>$alias_str)));
        
		// Escape
		$alias_str = wpl_db::escape(wpl_global::url_encode($alias_str));
        
        // Don't save and return the value
        if($only_return) return $alias_str;
        
		// Update
		$query = "UPDATE `#__wpl_properties` SET `$column`='".wpl_db::escape($alias_str)."' WHERE `id`='".$property_id."'";
		wpl_db::q($query, 'update');
        
        if($base_column)
        {
            $query = "UPDATE `#__wpl_properties` SET `$base_column`='".wpl_db::escape($alias_str)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
		return $alias_str;
    }
	
	/**
     * Updates property page title
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $property_data
     * @param int $property_id
     * @param boolean $force
     * @return string
     */
	public static function update_property_page_title($property_data, $property_id = 0, $force = false)
	{
        /** fetch property data if property id is setted **/
		if($property_id) $property_data = self::get_property_raw_data($property_id);
        if(!$property_id) $property_id = $property_data['id'];
        
        $column = 'field_312';
        $field = wpl_flex::get_field_by_column($column, $property_data['kind']);
        
        $base_column = NULL;
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status())
        {
            $base_column = wpl_global::get_current_language() == wpl_addon_pro::get_default_language() ? $column : NULL;
            $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        }
        
        /** return current page title if exists **/
        if(isset($property_data[$column]) and trim($property_data[$column]) != '' and !$force) return stripslashes($property_data[$column]);
        
        /** first validation **/
		if(!$property_data) return '';
        
        // Get the pattern
        $default_pattern = '[property_type] [listing][glue] [rooms][glue] [bedrooms][glue] [bathrooms][glue] [price][glue] [mls_id]';
        $page_title_pattern = wpl_global::get_pattern('property_page_title_pattern', $default_pattern, $property_data['kind'], $property_data['property_type']);
        
        $title_str = wpl_global::render_pattern($page_title_pattern, $property_id, $property_data, ' - ');
		$title_str = trim($title_str, '- ');

        // Apply Filters
		@extract(wpl_filters::apply('generate_property_page_title', array('title_str'=>$title_str, 'patern'=>$page_title_pattern, 'property_data'=>$property_data)));
        
        /** update **/
        if(wpl_db::columns('wpl_properties', $column))
        {
            $query = "UPDATE `#__wpl_properties` SET `".$column."`='".wpl_db::escape($title_str)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
        /** update **/
        if($base_column and wpl_db::columns('wpl_properties', $base_column))
        {
            $query = "UPDATE `#__wpl_properties` SET `".$base_column."`='".wpl_db::escape($title_str)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
		return stripslashes($title_str);
    }
    
    /**
     * Updates property title
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $property_data
     * @param int $property_id
     * @param boolean $force
     * @return string
     */
    public static function update_property_title($property_data, $property_id = 0, $force = false)
	{
        /** fetch property data if property id is set **/
		if($property_id) $property_data = self::get_property_raw_data($property_id);
        if(!$property_id) $property_id = $property_data['id'];
        
        $column = 'field_313';
        $field = wpl_flex::get_field_by_column($column, $property_data['kind']);
        
        $base_column = NULL;
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status())
        {
            $base_column = wpl_global::get_current_language() == wpl_addon_pro::get_default_language() ? $column : NULL;
            $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        }
        
        /** return current title if exists **/
        if(isset($property_data[$column]) and trim($property_data[$column]) != '' and !$force) return stripslashes($property_data[$column]);
        
        /** first validation **/
		if(!$property_data) return '';
		
        // Get the pattern
        $title_pattern = wpl_global::get_pattern('property_title_pattern', '[property_type] [listing]', $property_data['kind'], $property_data['property_type']);
		$title_str = wpl_global::render_pattern($title_pattern, $property_id, $property_data, ' ');

        // Apply Filters
		@extract(wpl_filters::apply('generate_property_title', array('title_str'=>$title_str, 'patern'=>$title_pattern, 'property_data'=>$property_data)));
        
        /** update **/
        if(wpl_db::columns('wpl_properties', $column))
        {
            $query = "UPDATE `#__wpl_properties` SET `".$column."`='".wpl_db::escape($title_str)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
        /** update **/
        if($base_column and wpl_db::columns('wpl_properties', $base_column))
        {
            $query = "UPDATE `#__wpl_properties` SET `".$base_column."`='".wpl_db::escape($title_str)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
        return stripslashes($title_str);
    }
	
    /**
     * Fix Aliases for all languages
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $property_data
     * @param int $property_id
     */
    public static function fix_aliases($property_data, $property_id = 0)
	{
		/** fetch property data if property id is setted **/
		if($property_id) $property_data = self::get_property_raw_data($property_id);
        if(!$property_id) $property_id = $property_data['id'];
        
        $columns = wpl_global::get_multilingual_columns(array('alias'), 'wpl_properties');
        foreach($columns as $column)
        {
            $alias = wpl_db::escape(wpl_global::url_encode($property_data[$column]));
            
            $query = "UPDATE `#__wpl_properties` SET `$column`='".wpl_db::escape($alias)."' WHERE `id`='$property_id'";
            wpl_db::q($query, 'UPDATE');
        }
    }
    
    /**
     * Calls proeprty visit actions
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @return boolean
     */
	public static function property_visited($property_id)
	{
		// First Validation
		if(!trim($property_id)) return false;
		
		$current_user_id = wpl_users::get_cur_user_id();

        $query = "UPDATE `#__wpl_properties` SET `visit_time`=visit_time+1, `visit_date`='".date("Y-m-d H:i:s")."' WHERE `id`='$property_id' AND `user_id`!='$current_user_id'";
        wpl_db::q($query, 'update');

        // Adding page visits for multisite blogs
        if(wpl_global::is_multisite() and (!$current_user_id or ($current_user_id and $current_user_id != wpl_property::get_property_user($property_id))))
        {
            $blog_id = wpl_global::get_current_blog_id();

            $query = "SELECT `id` FROM `#__wpl_addon_franchise_property_visits` WHERE `pid`='$property_id' AND `blog_id`='$blog_id' LIMIT 1";
            $result = wpl_db::select($query, 'loadResult');

            if($result)
            {
                $query = "UPDATE `#__wpl_addon_franchise_property_visits` SET `visits`=visits+1 WHERE `pid`='$property_id' AND `blog_id`='$blog_id'";
                wpl_db::q($query, 'update');
            }
            else
            {
                $query = "INSERT INTO `#__wpl_addon_franchise_property_visits` (`pid`, `visits`, `blog_id`) VALUES ('$property_id', 1, '$blog_id')";
                wpl_db::q($query, 'insert');
            }
        }

        // Market Reports Addon
        if(wpl_global::check_addon('market_reports'))
        {
            // Include Library
            _wpl_import('libraries.addon_market_reports');

            // Log the Visit
            $mr = new wpl_addon_market_reports();
            $mr->visit($property_id);
        }

        return true;
	}
	
    /**
     * Returns a certain field of property data
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $field_name
     * @param int $property_id
     * @return mixed
     */
	public static function get_property_field($field_name, $property_id)
	{
        $cached = (array) wpl_property::get_property_cached_data($property_id);
        if($cached and isset($cached[$field_name])) return $cached[$field_name];

		return wpl_db::get($field_name, 'wpl_properties', 'id', $property_id);
	}
    
    /**
     * Returns property user (Agent) ID
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @return int
     */
	public static function get_property_user($property_id)
	{
		return self::get_property_field('user_id', $property_id);
	}
	
    /**
     * Returns property kind
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @return int
     */
	public static function get_property_kind($property_id)
	{
		return self::get_property_field('kind', $property_id);
	}
	
    /**
     * Temporary delete a proeprty
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param int $status
     * @param boolean $trigger_event
     * @return boolean
     */
	public static function delete($property_id, $status = 1, $trigger_event = true)
	{
		/** first validation **/
		if(!trim($property_id) or !in_array($status, array(0, 1))) return false;
		
        /** Multisite **/
		if(wpl_global::is_multisite() and wpl_users::is_administrator())
		{
            $fs = wpl_sql_parser::getInstance();
            $fs->disable();
		}
        
        wpl_db::update('wpl_properties', array('deleted'=>$status), 'id', $property_id);
		
        /** Multisite **/
		if(wpl_global::is_multisite() and wpl_users::is_administrator())
		{
            $fs->enable();
		}
        
		/** trigger event **/
		if($trigger_event and $status == 1) wpl_global::event_handler('property_deleted', array('property_id'=>$property_id, 'status'=>$status));
		elseif($trigger_event and $status == 0) wpl_global::event_handler('property_restored', array('property_id'=>$property_id, 'status'=>$status));
		
		return true;
	}
	
    /**
     * Confirm a property
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param int $status
     * @param boolean $trigger_event
     * @return boolean
     */
	public static function confirm($property_id, $status = 1, $trigger_event = true)
	{
		/** first validation **/
		if(!trim($property_id) or !in_array($status, array(0, 1))) return false;
		
        /** Multisite **/
		if(wpl_global::is_multisite() and wpl_users::is_administrator())
		{
            $fs = wpl_sql_parser::getInstance();
            $fs->disable();
		}
        
        wpl_db::update('wpl_properties', array('confirmed'=>$status), 'id', $property_id);
		
        /** Multisite **/
		if(wpl_global::is_multisite() and wpl_users::is_administrator())
		{
            $fs->enable();
		}
        
		/** trigger event **/
		if($trigger_event and $status == 1) wpl_global::event_handler('property_confirmed', array('property_id'=>$property_id, 'status'=>$status));
		elseif($trigger_event and $status == 0) wpl_global::event_handler('property_unconfirmed', array('property_id'=>$property_id, 'status'=>$status));
		
		return true;
	}
    
    /**
     * Purge a property completely
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param boolean $trigger_event
     * @return boolean
     */
	public static function purge($property_id, $trigger_event = true)
	{
		// First Validation
		if(!trim($property_id)) return false;
        
		$property_data = self::get_property_raw_data($property_id);

		// Trigger Events
        if($trigger_event) wpl_global::event_handler('property_purged', array('property_id'=>$property_id, 'property_data'=>$property_data));

        // Multisite
		if(wpl_global::is_multisite() and wpl_users::is_administrator())
		{
            $fs = wpl_sql_parser::getInstance();
            $fs->disable();
		}
        
		// Purging Property Related Data
		wpl_items::delete_all_items($property_id, $property_data['kind']);
        
        // Purging Property Folder
        $blog_id = wpl_property::get_blog_id($property_id);
        wpl_folder::delete(wpl_items::get_path($property_id, $property_data['kind'], $blog_id));
        
		// Purging Property Record
        wpl_db::delete('wpl_properties', $property_id);

        // Market Reports Addon
        if(wpl_global::check_addon('market_reports'))
        {
            // Include Library
            _wpl_import('libraries.addon_market_reports');

            // Remove the Logs
            $mr = new wpl_addon_market_reports();
            $mr->remove($property_id);
        }
		
        // Multisite
		if(wpl_global::is_multisite() and wpl_users::is_administrator() and isset($fs))
		{
            $fs->enable();
		}
        
		return true;
	}
    
    /**
     * Changes user (Agent) of a property
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param int $user_id
     * @return boolean
     */
	public static function change_user($property_id, $user_id)
	{
		/** first validation **/
		if(!trim($property_id) or !trim($user_id)) return false;
		
		/** running the query **/
        wpl_db::q("UPDATE `#__wpl_properties` SET `user_id`='$user_id' WHERE `id`='$property_id'");
        
		/** trigger event **/
		wpl_global::event_handler('property_user_changed', array('property_id'=>$property_id, 'user_id'=>$user_id));
		
		return true;
	}
	
    /**
     * Returns active properties according to criteria
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $extra_condition
     * @param string $select
     * @param string $output
     * @param int $limit
     * @param string $order
     * @return array
     */
	public static function select_active_properties($extra_condition = '', $select = '*', $output = 'loadAssocList', $limit = 0, $order = '`id` ASC')
	{
		$condition = " AND `deleted`='0' AND `finalized`='1' AND `confirmed`='1' AND `expired`='0'";
		if(trim($extra_condition) != '') $condition .= $extra_condition;
		
		$query = "SELECT ".$select." FROM `#__wpl_properties` WHERE 1 ".$condition." ORDER BY $order ".($limit ? "LIMIT $limit" : '');
		return wpl_db::select($query, $output);
	}
	
    /**
     * Generates sort output
     * @author Howard <howard@realtyna.com>
     * @param array $params
     * @return string
     */
	public function generate_sorts($params = array())
	{
        $result = NULL;

		include _wpl_import('views.basics.sorts.property_listing', true, true);
		return $result;
	}
	
    /**
     * This is a very useful function for rendering whole data of property. you need to just pass property_id and get everything!
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param array $plisting_fields
     * @param array $property
     * @param array $params
     * @param boolean $force
     * @return array
     */
	public static function full_render($property_id, $plisting_fields = NULL, $property = NULL, $params = array(), $force = false)
	{
		/** get plisting fields **/
		if(!$plisting_fields) $plisting_fields = self::get_plisting_fields();
		
		$raw_data = self::get_property_raw_data($property_id);
        
        if(!$raw_data) return array();
		if(!$property) $property = (object) $raw_data;
		
        $column = 'rendered';
        if(wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        
        /** generate rendered data if rendered data is empty **/
        if(!wpl_settings::get('cache') or $force) $rendered = array();
        elseif(!trim($raw_data[$column]) and wpl_settings::get('cache')) $rendered = json_decode(wpl_property::generate_rendered_data($property_id, $plisting_fields), true);
        else $rendered = json_decode($raw_data[$column], true);
        
		$result = array();
		$result['data'] = (array) $property;

        $rendered_fields = array();
        if(!isset($rendered['rendered']) or !isset($rendered['materials']))
        {
            /** render data on the fly **/
            $find_files = array();
            $rendered_fields = self::render_property($property, $plisting_fields, $find_files, true);
        }
        
		if(isset($rendered['rendered']) and $rendered['rendered']) $result['rendered'] = $rendered['rendered'];
		else $result['rendered'] = $rendered_fields['ids'];
        
        if(isset($rendered['materials']) and $rendered['materials']) $result['materials'] = $rendered['materials'];
		else $result['materials'] = $rendered_fields['columns'];
		
		$result['items'] = wpl_items::get_items($property_id, '', $property->kind, '', 1);
		$result['raw'] = $raw_data;
		
		/** location text **/
		$result['location_text'] = self::generate_location_text($raw_data);
		
		/** property full link **/
        $target_page = isset($params['wpltarget']) ? $params['wpltarget'] : 0;
		$result['property_link'] = self::get_property_link($raw_data, NULL, $target_page);
        $result['property_title'] = self::update_property_title($raw_data);
		
		return $result;
	}
    
    /**
     * Converts Listing ID to pid
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $value
     * @param string $key
     * @return string
     */
	public static function pid($value, $key = 'mls_id')
	{
		return wpl_db::get('id', 'wpl_properties', $key, $value);
	}
    
    /**
     * Returns mls_id column of properties table
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param mixed $value
     * @param string $key
     * @return string $listing_id
     */
    public static function listing_id($value, $key = 'id')
	{
		return wpl_db::get('mls_id', 'wpl_properties', $key, $value);
	}
	
    /**
     * This function is for importing/updating properties into the WPL. It uses WPL standard format for importing
     * This function must call in everywhere that we need to import properties like MLS and IMPORTER Add-ons.
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $properties_to_import
     * @param string $wpl_unique_field
     * @param int $user_id
     * @param string $source
     * @param boolean $finalize
     * @param array $log_params
     * @return array property IDs
     */
	public static function import($properties_to_import, $wpl_unique_field = 'mls_id', $user_id = NULL, $source = 'mls', $finalize = true, $log_params = array())
	{
	    // Import Library
        _wpl_import('libraries.property.import');

        // Import
        $import = new wpl_property_import($properties_to_import, $wpl_unique_field, $user_id, $source, $finalize, $log_params);
        return $import->start();
	}
    
    /**
     * Returns Property Edit Link
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @return boolean|string
     */
	public static function get_property_edit_link($property_id = 0)
	{
		/** first validation **/
		if(!$property_id) return false;
		
		$target_id = wpl_request::getVar('wpltarget', 0);
		
		if($target_id) $url = wpl_global::add_qs_var('pid', $property_id, wpl_sef::get_page_link($target_id));
		else
        {
            /** Backend **/
            if(wpl_global::get_client()) $url = wpl_global::add_qs_var('pid', $property_id, wpl_global::get_wpl_admin_menu('wpl_admin_add_listing'));
            /** Frontend **/
            else $url = wpl_global::add_qs_var('pid', $property_id, wpl_global::add_qs_var('wplmethod', 'wizard'));
        }
		
        return $url;
    }
    
    /**
     * Get property ids for a criteria
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $column
     * @param mixed $value
     * @return array
     */
	public static function get_properties_list($column, $value)
	{
		$query = "SELECT `id` FROM `#__wpl_properties` WHERE `$column`='$value'";
		return wpl_db::select($query, 'loadAssocList');
    }
    
    /**
     * Updates properties and regenerate some of cached property data
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $column
     * @param mixed $previous_value
     * @param mixed $new_value
     * @return boolean
     */
	public static function update_properties($column, $previous_value, $new_value)
	{
		$listings = wpl_property::get_properties_list($column, $previous_value);
		$query = "UPDATE `#__wpl_properties` SET `$column`='".wpl_db::escape($new_value)."' WHERE `$column`='".wpl_db::escape($previous_value)."'";
		$result = wpl_db::q($query);
        
		foreach($listings as $listing)
		{
			$pid = $listing['id'];
			$property = self::get_property_raw_data($pid);
            
			wpl_property::update_text_search_field($pid);
			wpl_property::update_alias($property, NULL);
			wpl_property::update_numbs($pid, $property);
            
			/** generate rendered data **/
			if(wpl_settings::get('cache')) wpl_property::generate_rendered_data($pid);
		}
        
		return $result;
    }
    
    /**
     * Removes property thumbnails
     * @author Howard R <Howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param int $kind
     * @return boolean
     */
    public static function remove_thumbnails($property_id, $kind = 0)
    {
        /** first validation **/
        if(!trim($property_id)) return false;
        
        $ext_array = array('jpg', 'jpeg', 'gif', 'png');
        
        $path = wpl_items::get_path($property_id, $kind, wpl_property::get_blog_id($property_id));
        $thumbnails = wpl_folder::files($path, '^th.*\.('.implode('|', $ext_array).')$', 3, true);

        foreach($thumbnails as $thumbnail) wpl_file::delete($thumbnail);
        
        return true;
    }
    
    /**
     * Removes property cache
     * @author Howard R <Howard@realtyna.com>
     * @static
     * @param int $property_id
     * @return boolean
     */
    public static function clear_property_cache($property_id)
    {
        /** First Validation **/
        if(!trim($property_id)) return false;

        $multilingual = wpl_global::check_multilingual_status();
        
        $q = " `location_text`='', `rendered`='', `alias`=''";
        if($multilingual) $q = wpl_settings::get_multilingual_query(array('location_text', 'rendered', 'alias'));

        $query = "UPDATE `#__wpl_properties` SET ".$q." WHERE `id`='$property_id'";
        wpl_db::q($query, 'UPDATE');
        
        // Remove meta keywords if it's not set to manual
        $q = " `meta_keywords`=''";
        if($multilingual) $q = wpl_settings::get_multilingual_query(array('meta_keywords'));
            
        $query = "UPDATE `#__wpl_properties` SET ".$q." WHERE `id`='$property_id' AND `meta_keywords_manual`='0'";
        wpl_db::q($query, 'UPDATE');
        
        // Remove meta description if it's not set to manual
        $q = " `meta_description`=''";
        if($multilingual) $q = wpl_settings::get_multilingual_query(array('meta_description'));
            
        $query = "UPDATE `#__wpl_properties` SET ".$q." WHERE `id`='$property_id' AND `meta_description_manual`='0'";
        wpl_db::q($query, 'UPDATE');
        
        // Clear Property Thumbnails
        wpl_property::clear_property_thumbnails($property_id);
        
        return true;
    }
    
    /**
     * Checks if property has parent or not
     * @author Howard R <Howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param string $parent_column
     * @return boolean
     */
    public static function has_parent($property_id, $parent_column = 'parent')
    {
        if(wpl_property::get_parent($property_id, $parent_column)) return true;
        else return false;
    }
    
    /**
     * Returns Parent of a property
     * @author Howard R <Howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param string $parent_column
     * @return int
     */
    public static function get_parent($property_id, $parent_column = 'parent')
    {
        return wpl_property::get_property_field($parent_column, $property_id);
    }

    /**
     * Returns RSS link of property listing
     * @author Steve A. <steve@realtyna.com>
     * @static
     * @return string|boolean
     */
    public static function get_property_rss_link()
    {
        $nosef = wpl_sef::is_permalink_default();
        
        $home_type = wpl_global::get_wp_option('show_on_front', 'posts');
        $home_id = wpl_global::get_wp_option('page_on_front', 0);
        $wpl_main_page_id = wpl_sef::get_wpl_main_page_id();
        
        if($nosef  or ($home_type == 'page' and $home_id == $wpl_main_page_id))
        {
            $url = wpl_sef::get_wpl_permalink(true);
            $url = wpl_global::add_qs_var('wplview', 'features', $url);
            $url = wpl_global::add_qs_var('wpltype', 'rss', $url);
        }
        else $url = wpl_sef::get_wpl_permalink(true).'features/rss';
        
        return $url;
    }
    
    /**
     * Returns Print link of property listing
     * @author Howard R. <howard@realtyna.com>
     * @static
     * @return string
     */
    public static function get_property_print_link()
    {
        $nosef = wpl_sef::is_permalink_default();
        
        $home_type = wpl_global::get_wp_option('show_on_front', 'posts');
        $home_id = wpl_global::get_wp_option('page_on_front', 0);
        $wpl_main_page_id = wpl_sef::get_wpl_main_page_id();
        
        if($nosef  or ($home_type == 'page' and $home_id == $wpl_main_page_id))
        {
            $url = wpl_sef::get_wpl_permalink(true);
            $url = wpl_global::add_qs_var('wplview', 'features', $url);
            $url = wpl_global::add_qs_var('wpltype', 'print', $url);
        }
        else $url = wpl_sef::get_wpl_permalink(true).'features/print';
        
        return $url;
    }
    
    /**
     * Returns property meta keywords, This function calls on sef service when meta description of property is empty
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $property_data
     * @param int $property_id
     * @param boolean $force
     * @return string
     */
    public static function get_meta_keywords($property_data, $property_id = 0, $force = false)
	{
        /** fetch property data if property id is setted **/
		if($property_id) $property_data = self::get_property_raw_data($property_id);
        if(!$property_id) $property_id = $property_data['id'];
        
        $column = 'meta_keywords';
        $field = wpl_flex::get_field_by_column($column, $property_data['kind']);
        
        $base_column = NULL;
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status())
        {
            $base_column = wpl_global::get_current_language() == wpl_addon_pro::get_default_language() ? $column : NULL;
            $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        }
        
        /** return current meta keywords if exists or manual keywords is enabled **/
        if(isset($property_data['meta_keywords_manual']) and $property_data['meta_keywords_manual'] or (isset($property_data[$column]) and trim($property_data[$column]) != '' and !$force)) return stripslashes($property_data[$column]);
        
        /** first validation **/
		if(!$property_data) return '';
        
        // Get the pattern
        $default_pattern = '[location][glue] [bedrooms] [str:Bedrooms:bedrooms][glue] [rooms] [str:Rooms:rooms][glue][bathrooms][str:Bathrooms:bathrooms][glue][property_type][glue][listing_type][glue][field_54][glue][field_42][glue][field_55][glue][listing_id]';
        $meta_keywords_pattern = wpl_global::get_pattern('meta_keywords_pattern', $default_pattern, $property_data['kind'], $property_data['property_type']);
        
        $keywords_str = wpl_global::render_pattern($meta_keywords_pattern, $property_id, $property_data, ',');

        // Apply Filters
		@extract(wpl_filters::apply('generate_meta_keywords', array('keywords_str'=>$keywords_str, 'patern'=>$meta_keywords_pattern, 'property_data'=>$property_data)));
        
        /** update **/
        if(wpl_db::columns('wpl_properties', $column))
        {
            $query = "UPDATE `#__wpl_properties` SET `".$column."`='".wpl_db::escape($keywords_str)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
        /** update **/
        if($base_column and wpl_db::columns('wpl_properties', $base_column))
        {
            $query = "UPDATE `#__wpl_properties` SET `".$base_column."`='".wpl_db::escape($keywords_str)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
        return stripslashes($keywords_str);
    }
    
    /**
     * Returns property meta description, This function calls on sef service when meta description of listing is empty
     * @author Howard <howard@realtyna.com>
     * @static
     * @param array $property_data
     * @param int $property_id
     * @param boolean $force
     * @return string
     */
    public static function get_meta_description($property_data, $property_id = 0, $force = false)
	{
        /** fetch property data if property id is setted **/
		if($property_id) $property_data = self::get_property_raw_data($property_id);
        if(!$property_id) $property_id = $property_data['id'];
        
        $column = 'meta_description';
        $field = wpl_flex::get_field_by_column($column, $property_data['kind']);
        
        $base_column = NULL;
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status())
        {
            $base_column = wpl_global::get_current_language() == wpl_addon_pro::get_default_language() ? $column : NULL;
            $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        }
        
        /** return current meta description if exists or manual description is enabled **/
        if(isset($property_data['meta_description_manual']) and $property_data['meta_description_manual'] or (isset($property_data[$column]) and trim($property_data[$column]) != '' and !$force)) return stripslashes($property_data[$column]);
        
        /** first validation **/
		if(!$property_data) return '';
        
        // Get the pattern
        $default_pattern = '[bedrooms] [str:Bedrooms:bedrooms] [rooms] [str:Rooms:rooms] [str:With:bathrooms] [bathrooms] [str:Bathrooms:bathrooms] [property_type] [listing_type] [field_54] [field_42] [str:On the:field_55] [field_55] [str:Floor:field_55] [str:In] [location]';
        $meta_description_pattern = wpl_global::get_pattern('meta_description_pattern', $default_pattern, $property_data['kind'], $property_data['property_type']);
        
        $description = wpl_global::render_pattern($meta_description_pattern, $property_id, $property_data, ' ');

        // Apply Filters
		@extract(wpl_filters::apply('generate_meta_description', array('description'=>$description, 'property_data'=>$property_data)));
        
        /** update **/
        if(wpl_db::columns('wpl_properties', $column))
        {
            $query = "UPDATE `#__wpl_properties` SET `".$column."`='".wpl_db::escape($description)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
        /** update **/
        if($base_column and wpl_db::columns('wpl_properties', $base_column))
        {
            $query = "UPDATE `#__wpl_properties` SET `".$base_column."`='".wpl_db::escape($description)."' WHERE `id`='".$property_id."'";
            wpl_db::q($query, 'update');
        }
        
        return stripslashes($description);
    }
    
    /**
     * Returns property featured image if exists otherwise it returns empty string
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @param string $sizes
     * @return string
     */
    public static function get_property_image($property_id, $sizes = '150*150')
    {
        if(!trim($property_id)) return false;
        if(!trim($sizes)) $sizes = '150*150';
        
        $images = wpl_items::render_gallery_custom_sizes($property_id, NULL, array($sizes));
        $size_alias = str_replace('*', '_', $sizes);
        
        return (count($images) ? $images[$size_alias][0]['url'] : '');
    }
    
    /**
     * Get located blog id of property 
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $property_id
     * @return int
     */
    public static function get_blog_id($property_id)
    {
        if(!wpl_global::is_multisite() or !$property_id) return wpl_global::get_current_blog_id();
        
        return wpl_property::get_property_field('blog_id', $property_id);
    }
    
     /**
     * Clear the property thumbnails
     * @author Matthew <matthew@realtyna.com>
     * @param int $property_id
     * @return void
     */
    public static function clear_property_thumbnails($property_id)
    {
        if(empty($property_id)) return;
        
        $kind = wpl_property::get_property_kind($property_id);
        $ext_array = array('jpg', 'jpeg', 'gif', 'png');

        $path = wpl_items::get_path($property_id, $kind, wpl_property::get_blog_id($property_id));
        $thumbnails = wpl_folder::files($path, '^th.*\.('.implode('|', $ext_array).')$', 3, true);

        foreach($thumbnails as $thumbnail) wpl_file::delete($thumbnail);
    }
    
    /**
     * For clonning a listing
     * @author Howard R. <howard@realtyna.com>
     * @param int $property_id
     * @return int
     */
    public static function clone_listing($property_id)
    {
        $listing_data = wpl_property::get_property_raw_data($property_id);
        
        $kind = isset($listing_data['kind']) ? $listing_data['kind'] : 0;
        $user_id = isset($listing_data['user_id']) ? $listing_data['user_id'] : wpl_users::get_cur_user_id();
        
        // Generate new property
		$clone_id = wpl_property::create_property_default($user_id, $kind);
        
        // Clone listing data
        $forbidden_fields = array('id', 'kind', 'mls_id', 'pic_numb', 'att_numb', 'sent_numb', 'contact_numb', 'user_id', 'visit_time',
            'visit_date', 'add_date', 'last_modified_time_stamp', 'sp_featured', 'sp_hot','vids_numb', 'geopoints', 'blog_id');

        $q = '';
        foreach($listing_data as $key=>$value)
        {
            if(in_array($key, $forbidden_fields)) continue;

            $value = wpl_db::escape($value);
            $q .= "`$key`='$value', ";
        }

        $q .= trim($q, ', ');
        wpl_db::q("UPDATE `#__wpl_properties` SET $q WHERE `id`='$clone_id'");
        
        // Clone listing items such as images etc
        $all_items = wpl_items::get_items($property_id, '', $kind);
        
        foreach($all_items as $type=>$items)
        {
            foreach($items as $item)
            {
                $item->parent_id = $clone_id;
                
                unset($item->id);
                if(isset($item->blog_id)) unset($item->blog_id);
                
                wpl_items::save((array) $item);
            }
        }
        
        // Clone listing folder
        $listing_path = wpl_items::get_path($property_id, $kind, wpl_property::get_blog_id($property_id));
        $clone_path = wpl_items::get_path($clone_id, $kind, wpl_property::get_blog_id($clone_id));
        
        wpl_folder::copy($listing_path, $clone_path, '', true);
        
        // Finalize the listing
        wpl_property::finalize($clone_id);
        
        return $clone_id;
    }
    
    /**
     * For checking if location of a listing is visible for current user or not
     * @author Howard R. <howard@realtyna.com>
     * @param int $property_id
     * @param int $kind
     * @param int $membership_id
     * @return boolean
     */
    public static function location_visibility($property_id, $kind = NULL, $membership_id = NULL)
    {
        $show_address = wpl_property::get_property_field('show_address', $property_id);
        if(!$show_address) return false;
        
        if(is_null($kind)) $kind = wpl_property::get_property_kind($property_id);
        if(is_null($membership_id))
        {
            $user_id = wpl_users::get_cur_user_id();
            $membership_id = wpl_users::get_user_membership($user_id);
        }
        
        $dbst = (array) wpl_flex::get_field_by_column('locations', $kind);
        
        // access is not set
        if(trim($dbst['accesses']) == '') return true;
        
        $accesses = explode(',', trim($dbst['accesses'], ', '));
        if(!in_array($membership_id, $accesses)) return __($dbst['accesses_message'], 'real-estate-listing-realtyna-wpl');
        
        return true;
    }

    /**
     * Adding property stats in item with different type
     * @author Matthew N. (matthew@realtyna.com)
     * @param $property_id
     * @param string $stats_type
     * @return boolean
     */
    public static function add_property_stats_item($property_id, $stats_type = '')
    {
        /** first validation **/
        if(!trim($property_id) or empty($stats_type)) return false;

        $current_user_id = wpl_users::get_cur_user_id();

        $query = "UPDATE `#__wpl_properties` SET `$stats_type`=".$stats_type."+1 WHERE `id`='$property_id' AND `user_id`!='$current_user_id'";
        return wpl_db::q($query, 'update');
    }

    /**
     * Get property stats in item with different type
     * 
     * @author Steve A. (steve@realtyna.com)
     * @param $property_id
     * @param string $stats_type
     * @return integer
     */
    public static function get_property_stats_item($property_id, $stats_type = '')
    {
        /** first validation **/
        if(!trim($property_id) or empty($stats_type)) return 0;

        return wpl_property::get_property_field($stats_type, $property_id);
    }    

    /**
     * Get table names of units
     * 
     * @author Damon A. (damon@realtyna.com)
     * @param int $type
     * @return array|boolean
     */
    public static function get_units_table_cols($type)
    {
        /** validation **/
        if(!$type) return false;

        switch($type)
        {
            case 1:
                $type = 'length';
                break;

            case 2:
                $type = 'area';
                break;

            case 3:
                $type = 'volume';
                break;

            case 4:
                $type = 'price';
                break;

            default:
                $type = NULL;
                break;
        }

        $columns = array();

        $fields = wpl_flex::get_fields(NULL, NULL, NULL, NULL, NULL, "AND `type`='$type'");
        foreach($fields as $key => $field) $columns[] = $field->table_column . '_unit';
        
        return $columns;
    }

    /**
     * Update unit price in wpl_properties table
     * 
     * @author Damon A. (damon@realtyna.com)
     * @param array $columns
     * @param int $unit_id
     * @return integer
     */
    public static function check_unit_columns($columns, $unit_id)
    {
        /** validation **/
        if(!$columns) return false;

        foreach($columns as $key => $value)
        {
            $query = "SELECT COUNT(`$value`) FROM `#__wpl_properties` WHERE `$value`='$unit_id'";
            $response = wpl_db::select($query, 'loadResult');

            if($response > 0) return $response;
        }

        return false;
    }

    /**
     * Update unit price in wpl_properties table
     * 
     * @author Damon A. (damon@realtyna.com)
     * @param int $old_unit
     * @param int $new_unit
     * @param int $type
     * @return array|boolean
     */
    public static function update_listing_units($old_unit = NULL, $new_unit = NULL, $type = NULL)
    {
        /** validation **/
        if($old_unit === NULL || $new_unit === NULL || $type === NULL) return false;

        $columns = self::get_units_table_cols($type);
        if(!$columns) return false;

        $response = array();

        $unit = wpl_units::get_unit($new_unit);
        $tosi = $unit['tosi'];

        foreach($columns as $key => $col)
        {
            $main_column = str_replace('_unit', '', $col);
            $SI_column = $main_column.'_si';

            $query = "UPDATE `#__wpl_properties` SET `$col`='$new_unit', `$SI_column`=`$main_column`*".($tosi)." WHERE `$col`='$old_unit'";
            $response[] = wpl_db::q($query, 'update');
        }

        // Clear the Cache
        wpl_settings::clear_cache('properties_cached_data');

        return $response;
    }

    /**
     * Returns finalized status of property
     * @author Howard R <howard@realtyna.com>
     * @param $property_id
     * @return mixed
     */
    public static function is_finalized($property_id)
    {
        return wpl_property::get_property_field('finalized', $property_id);
    }

    /**
     * Returns number of Days On Market
     * @author Howard R <howard@realtyna.com>
     * @param integer $property_id
     * @param string $column
     * @return integer
     */
    public static function DOM($property_id, $column = 'add_date')
    {
        $date_added = wpl_property::get_property_field($column, $property_id);
        $now = time();

        $diff = $now - strtotime($date_added);
        return ceil($diff / 86400);
    }

    /**
     * Returns Days On Market of a property
     * @author Howard R <howard@realtyna.com>
     * @param integer $property_id
     * @param string $column
     * @return string
     */
    public static function DOMString($property_id, $column = 'add_date')
    {
        $DOM = wpl_property::DOM($property_id, $column);
        return sprintf(__('%s Days', 'real-estate-listing-realtyna-wpl'), $DOM);
    }

    /**
     * It returns maximum search results
     * It's compliance of some MLS providers
     */
    public static function get_maximum_search_results()
    {
        // Only Applicable in Property Listing Page
        if(wpl_request::getVar('wplview', NULL) != 'property_listing') return false;

        $max = wpl_global::get_setting('maximum_search_results');
        if(!trim($max)) return false;

        return $max;
    }
}