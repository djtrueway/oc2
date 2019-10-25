<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Sort options library
 * @author Howard <howard@realtyna.com>
 * @since WPL1.0.0
 * @date 08/11/2013
 * @package WPL
 */
class wpl_sort_options
{
	/**
	 * Minimum ID of a custom sort option
	 * @var integer
	 */
	static $sort_options_min_id = 500;
    
    /**
     * Used for caching in get_sort_options function
     * @static
     * @var array
     */
    public static $sort_options = array();

    /**
     * Gets sort options
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $kind
     * @param int $enabled
     * @param string $condition
     * @param string $output_type
     * @param bool $format_kinds
     * @return mixed
     */
	public static function get_sort_options($kind = '', $enabled = 1, $condition = '', $output_type = 'loadAssocList', $format_kinds = false)
	{
        // Generate the Cache Key
        $cache_key = $kind.'_'.$enabled.'_'.$condition.'_'.$output_type.'_'.((int) $format_kinds);
        
        // Return from cache if exists
        if(isset(self::$sort_options[$cache_key])) return self::$sort_options[$cache_key];
        
		if(trim($condition) == '')
		{
			$condition = "";
			
			if(trim($enabled) != '') $condition .= " AND `enabled`>='$enabled'";
			if(trim($kind) != '') $condition .= " AND `kind` LIKE '%[$kind]%'";
			$condition .= " ORDER BY `index` ASC";
		}
		
		$query = "SELECT * FROM `#__wpl_sort_options` WHERE 1 ".$condition;
		$result = wpl_db::select($query, $output_type);

		if($format_kinds)
		{
			if($output_type == 'loadAssocList') foreach($result as $index=>$row) $result[$index]['kind'] = self::format_kinds($row['kind']);
			elseif($output_type == 'loadObjectList') foreach($result as $index=>$row) $result[$index]->kind = self::format_kinds($row->kind);
		}

        /** add to cache **/
		self::$sort_options[$cache_key] = $result;
        
		return $result;
	}
	
    /**
     * Sorts sort options
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $sort_ids
     */
	public static function sort_options($sort_ids)
	{
        $conter = 1;
        $ex_sort_ids = explode(',', $sort_ids);

        foreach($ex_sort_ids as $ex_sort_id)
        {
            self::update('wpl_sort_options', $ex_sort_id, 'index', $conter);
            $conter++;
        }
	}
	
    /**
     * Updates wpl_sort_options table
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $table
     * @param int $id
     * @param string $key
     * @param string $value
     * @return boolean
     */
	public static function update($table = 'wpl_sort_options', $id, $key, $value = '')
	{
		/** first validation **/
		if(trim($table) == '' or trim($id) == '' or trim($key) == '') return false;

		/** trigger event **/
		wpl_global::event_handler('sort_options_updated', array('id'=>$id,'key'=>$value));

		return wpl_db::set($table, $id, $key, $value);
	}

	/**
	* Formats kinds to labeled arrays
	* @author Edward <edward@realtyna.com>
	* @static
	* @param string $kinds
	* @return array
	*/
	public static function format_kinds($kinds)
	{
		$result = array();
		$wpl_kinds = array();
		if(trim($kinds) == '') return $result;

		preg_match_all('/\[(\d+)\]/', $kinds, $matches);
		$kinds_array = $matches[1];
		if(!sizeof($kinds_array)) return $result;

		$wpl_kinds_query = wpl_db::select("SELECT `id`,`name` FROM `#__wpl_kinds`");
		foreach($wpl_kinds_query as $wpl_kinds_row) $wpl_kinds[(int) $wpl_kinds_row->id] = $wpl_kinds_row->name;

		foreach($kinds_array as $kind)
		{
			$kind = (int) $kind;
			if(in_array($kind, array_keys($wpl_kinds))) $result[] = $wpl_kinds[$kind];
		}

		return $result;
	}

	/**
	* Get available sort options
	* @author Edward <edward@realtyna.com>
	* @static
	* @param $kind
	* @param $column
	* @param $formatted
	* @return mixed
	*/
	public static function get_available($kind = '', $column = 'field_name', $formatted = true)
	{
		if(trim($column) == '' && !wpl_db::columns('wpl_sort_options', $column)) return null;

		$result = self::get_sort_options($kind, 0, '', 'loadAssocList');
		if(!$formatted) return $result;

		$records = array();
		
		foreach($result as $row)
		{
			$current_value = $row[$column];

			if($column == 'field_name')
			{
				/** format field_name **/
				$replace = array('/^(?:p\.)(.*)/'=>'${1}', '/(.*)(?:_si)$/'=>'${1}');
				$current_value = preg_replace(array_keys($replace), array_values($replace), $current_value);
			}

			$records[$row['id']] = $current_value;
		}

		return $records;
	}

	/**
	* Adds a sort option 
	* @author Edward <edward@realtyna.com>
	* @static
	* @param int $dbst_id
	* @param int $kind
	* @param boolean
    * @return mixed
	*/
	public static function add_sort_option($dbst_id, $kind)
	{
		$dbst_id = (int) $dbst_id;
		$kind = (int) $kind;
		$kind_formatted = "[{$kind}]";

		if($dbst_id <= 0 or !wpl_db::exists($kind, 'wpl_kinds')) return false;

		$dbst_field = wpl_flex::render_sortable(wpl_flex::get_field($dbst_id));
		if(!$dbst_field or !intval($dbst_field->sortable)) return false;

		if(in_array(strtolower($dbst_field->type), wpl_units::get_si_unit_types())) $dbst_field->table_column .= '_si';
		if(!wpl_db::columns($dbst_field->table_name, $dbst_field->table_column)) return false;

		$sort_option = self::get_sort_options('', 0, " AND `field_name` = 'p.{$dbst_field->table_column}'", 'loadObject');

		if($sort_option)
		{
			if(strpos($sort_option->kind, $kind_formatted) !== false) return true;
			return self::update('wpl_sort_options', $sort_option->id, 'kind', $sort_option->kind . $kind_formatted) !== false;
		}

		$insert_id = self::get_insert_id();
		$result = wpl_db::q("INSERT INTO `#__wpl_sort_options` (`id`,`kind`,`name`,`field_name`,`enabled`,`index`) VALUES ('{$insert_id}', '{$kind_formatted}','{$dbst_field->name}','p.{$dbst_field->table_column}','1','99.00')") !== false;
        
        // Add the index
        wpl_db::index_add($dbst_field->table_column, wpl_flex::get_kind_table($kind));
        
        return $result;
	}

	/**
	* Remove a sort option
	* @author Edward <edward@realtyna.com>
	* @static
	* @param int $dbst_id
	* @param int $kind
	* @return boolean
	*/
	public static function remove_sort_option($dbst_id, $kind)
	{
		$dbst_id = (int) $dbst_id;
		$kind = (int) $kind;
		$kind_formatted = "[{$kind}]";

		if($dbst_id <= 0 or !wpl_db::exists($kind, 'wpl_kinds')) return false;

		$dbst_field = wpl_flex::get_field($dbst_id);
		if(!$dbst_field) return false;

		if(in_array(strtolower($dbst_field->type), wpl_units::get_si_unit_types())) $dbst_field->table_column .= '_si';

		$sort_option = self::get_sort_options('', 0, " AND `field_name` = 'p.{$dbst_field->table_column}'", 'loadObject');
		if(!$sort_option or strpos($sort_option->kind, $kind_formatted) === false) return true;

		$new_kind = str_replace($kind_formatted, '', trim($sort_option->kind));

		if($new_kind == '') $result = wpl_db::delete('wpl_sort_options', $sort_option->id) !== false;
		else $result = self::update('wpl_sort_options', $sort_option->id, 'kind', $new_kind) !== false;
        
        // Remove the index
        wpl_db::index_remove($dbst_field->table_column, wpl_flex::get_kind_table($kind));
        
        return $result;
	}

	/**
	 * Get a safe ID for insert a new sort option
	 * @author Edward <edward@realtyna.com>
	 * @static
	 * @return int
	 */
	protected static function get_insert_id()
	{
		$max_id = wpl_db::get("MAX(`id`)", "wpl_sort_options", '', '', '', "`id`<'10000'");
		return max($max_id + 1, self::$sort_options_min_id + 1);
	}
    
    public static function render($sort_options)
    {
        $rendered = array();
        
        foreach($sort_options as $sort_option)
        {
            if($sort_option['field_name'] == 'ptype_adv')
            {
                $types = wpl_global::get_property_types();
                
                foreach($types as $type)
                {
                    $sort_option['field_name'] = 'ptype_adv:'.$type['id'];
                    $sort_option['name'] = __(wpl_global::pluralize(2, $type['name']), 'real-estate-listing-realtyna-wpl');
                    
                    $rendered[] = $sort_option;
                }
            }
            elseif($sort_option['field_name'] == 'ltype_adv')
            {
                $types = wpl_global::get_listings();
                
                foreach($types as $type)
                {
                    $sort_option['field_name'] = 'ltype_adv:'.$type['id'];
                    $sort_option['name'] = __(wpl_global::pluralize(2, $type['name']), 'real-estate-listing-realtyna-wpl');
                    
                    $rendered[] = $sort_option;
                }
            }
            else
            {
                $rendered[] = $sort_option;
            }
        }
        
        return $rendered;
    }
    
    public static function sort_options_add_indexes()
    {
        $enabled_sort_options = wpl_sort_options::get_sort_options('', 1);
        
        foreach($enabled_sort_options as $enabled_sort_option)
        {
            // Skip current sort option if it's a system option
            if(strpos($enabled_sort_option['field_name'], 'p.') === false) continue;
            
            $kind = strpos($enabled_sort_option['kind'], '[2]') !== false ? 2 : 0;
            
            $ex = explode('.', $enabled_sort_option['field_name']);
            if(!isset($ex[1])) continue;
            
            $column = $ex[1];
            wpl_db::index_add($column, wpl_flex::get_kind_table($kind));
        }
        
        return true;
    }
}