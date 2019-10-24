<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Flex Library
 * @author Howard R <howard@realtyna.com>
 * @since WPL1.0.0
 * @date 05/01/2013
 * @package WPL
 */
class wpl_flex
{
	public static $category_listing_specific_array = array();
	public static $category_property_type_specific_array = array();
	public static $wizard_js_validation = array();
    public static $category_user_specific_array = array();
    
    /**
     * Used for caching in get_field_by_column function
     * @static
     * @var array
     */
    public static $fields_by_column = array();
	
    /**
     * Returns dbst fields
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $category
     * @param int $enabled
     * @param int $kind
     * @param string $key
     * @param mixed $value
     * @param string $condition
     * @return array of objects
     */
	public static function get_fields($category = NULL, $enabled = 0, $kind = 0, $key = '', $value = '', $condition = '')
	{
		if(!$condition)
		{
			$condition = '';
			
			if(trim($category) != '') $condition .= " AND `category`='$category'";
			if(trim($enabled) != '') $condition .= " AND `enabled`>='$enabled'";
			if(trim($kind) != '') $condition .= " AND `kind`='$kind'";
			
			if(trim($key) != '') $condition .= " AND `".$key."`>='$value'";
		}
		
		$query = "SELECT * FROM `#__wpl_dbst` WHERE 1 ".$condition." ORDER BY `index` ASC";
		return wpl_db::select($query, 'loadObjectList');
	}
	
    /**
     * Get dbst field data
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $field_id
     * @return object
     */
	public static function get_field($field_id)
	{
        $query = "SELECT * FROM `#__wpl_dbst` WHERE `id`='$field_id'";
		return wpl_db::select($query, 'loadObject');
	}

    /**
     * Get DB structure id based on kind and table column
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $table_column
     * @param int $kind
     * @return int
     */
    public static function get_dbst_id($table_column, $kind = 0)
    {
        $cache_key = 'wpl_dbst_id_'.$table_column.'_'.$kind;

        // Return From Cache
        $cached = wp_cache_get($cache_key);
        if($cached) return $cached;

        $query = "SELECT `id` FROM `#__wpl_dbst` WHERE `kind`='$kind' and `table_column`='$table_column'";
		$id = wpl_db::select($query, 'loadResult');

        // Set to Cache
        wp_cache_set($cache_key, $id, '', 100);

        return $id;
    }

    /**
     * @param string $column
     * @param integer $kind
     * @return object|null
     */
    public static function get_field_by_column($column, $kind = 0)
	{
        if(isset(self::$fields_by_column[$kind]) and isset(self::$fields_by_column[$kind][$column])) return self::$fields_by_column[$kind][$column];

        $query = "SELECT * FROM `#__wpl_dbst` WHERE `kind`='$kind' AND `table_column`='$column' LIMIT 1";
        $result = wpl_db::select($query, 'loadObject');

        if(!isset(self::$fields_by_column[$kind])) self::$fields_by_column[$kind] = array();

        self::$fields_by_column[$kind][$column] = $result;
        return $result;
	}
    
    /**
     * Create default dbst field and returns new dbst id
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $dbst_id
     * @param int $searchmod
     * @return int
     */
	public static function create_default_dbst($dbst_id = 0, $searchmod = 1)
	{
		if(!$dbst_id) $dbst_id = self::get_new_dbst_id();
		
		$query = "INSERT INTO `#__wpl_dbst` (`id`,`enabled`,`pshow`,`plisting`,`searchmod`,`pwizard`,`index`) VALUES ('$dbst_id','1','1','0','$searchmod','1','$dbst_id');";
		wpl_db::q($query, 'insert');

        return $dbst_id;
	}
	
    /**
     * Returns dbcats data
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $enabled
     * @param int $kind
     * @param string $condition
     * @return array of objects
     */
	public static function get_categories($enabled = 1, $kind = 0, $condition = '')
	{
		if(trim($condition) == '') $condition = " AND `enabled`>='$enabled' AND `kind`='$kind'";
		
		$query = "SELECT * FROM `#__wpl_dbcat` WHERE 1 ".$condition." ORDER BY `index` ASC";
		return wpl_db::select($query, 'loadObjectList');
	}
	
    /**
     * Returns dbcat data
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $category_id
     * @param string $condition
     * @return object
     */
	public static function get_category($category_id, $condition = '')
	{
        if(trim($condition) == '') $condition = " AND `id`='$category_id'";
        
		$query = "SELECT * FROM `#__wpl_dbcat` WHERE 1 ".$condition;
		return wpl_db::select($query, 'loadObject');
	}
	
    /**
     * Returns Kind Label
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $kind
     * @return string
     */
	public static function get_kind_label($kind = 0)
	{
		return wpl_db::get('name', 'wpl_kinds', 'id', $kind);
	}
	
    /**
     * Returns Kind Data
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $kind
     * @return array
     */
    public static function get_kind($kind = 0)
	{
        $query = "SELECT * FROM `#__wpl_kinds` WHERE `id`='$kind'";
        return wpl_db::select($query, 'loadAssoc');
	}
    
    /**
     * Returns Kind ID
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $kind_name
     * @return int
     */
	public static function get_kind_id($kind_name = 'property')
	{
        $query = "SELECT `id` FROM `#__wpl_kinds` WHERE LOWER(name)='".strtolower($kind_name)."'";
        return wpl_db::select($query, 'loadResult');
	}
    
    /**
     * Returns Kind Table
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $dbcat
     * @param int $kind
     * @return string
     */
	public static function get_kind_table($kind = 0, $dbcat = 0)
	{
        $query = "SELECT `table`, `params` FROM `#__wpl_kinds` WHERE `id`='$kind'";
        $result = wpl_db::select($query, 'loadObject');
        
        if($dbcat and $result->params)
        {
            $params = json_decode($result->params, true);
            if(isset($params['dbcat_tables']) and isset($params['dbcat_tables'][$dbcat]))
            {
                return $params['dbcat_tables'][$dbcat];
            }
        } 

        return $result->table;
	}
	
    /**
     * Returns Valid Kinds
     * @author Howard R <howard@realtyna.com>
     * @static
     * @return array
     */
	public static function get_valid_kinds()
	{
        $query = "SELECT `id` FROM `#__wpl_kinds` ORDER BY `index` ASC";
        $kinds = wpl_db::select($query, 'loadAssocList');
        
        $retrun = array();
        foreach($kinds as $kind) $retrun[] = $kind['id'];
        
		return $retrun;
	}
    
    /**
     * Returns Kinds
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param String $table
     * @return array
     */
	public static function get_kinds($table = 'wpl_properties')
	{
        $query = "SELECT * FROM `#__wpl_kinds` WHERE `enabled`>='1'".(trim($table) ? " AND `table`='$table'" : "")." ORDER BY `index` ASC";
        return wpl_db::select($query, 'loadAssocList');
	}
    
    /**
     * Returns Kind Template
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $wplpath
     * @param string $tpl
     * @param int $kind
     * @return string
     */
    public static function get_kind_tpl($wplpath, $tpl = NULL, $kind = 0)
	{
        if(!trim($tpl)) $tpl = 'default';
        
        /** Create Kind tpl such as default1.php etc. **/
        $kind_tpl = $tpl.'_k'.$kind;
        
        $wplpath = rtrim($wplpath, '.').'.'.$kind_tpl;
        $path = _wpl_import($wplpath, true, true);
        
        if(wpl_file::exists($path)) return $kind_tpl;
        else return $tpl;
	}
    
    /**
     * Returns dbst types
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $enabled
     * @param int $kind
     * @param string $condition
     * @return array of objects
     */
	public static function get_dbst_types($enabled = 1, $kind = 0, $condition = '')
	{
		if(trim($condition) == '')
		{
			$condition = " AND `enabled`>='$enabled' AND `kind` LIKE '%[$kind]%'";
		}
		
		$query = "SELECT * FROM `#__wpl_dbst_types` WHERE 1 ".$condition." ORDER BY `id` ASC";
		return wpl_db::select($query, 'loadObjectList');
	}
    
    /**
     * Returns dbst type data
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $enabled
     * @param int $kind
     * @param string $type
     * @return object
     */
	public static function get_dbst_type($enabled = 1, $kind = 0, $type = '')
	{
		$condition = " AND `enabled`>='$enabled' AND `kind` LIKE '%[$kind]%' AND `type`='$type'";
		
		$query = "SELECT * FROM `#__wpl_dbst_types` WHERE 1 ".$condition;
		return wpl_db::select($query, 'loadObject');
	}
	
    /**
     * Returns value of one specific column of dbst record on dbst table
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $key
     * @param int $dbst_id
     * @param int $kind
     * @return boolean
     */
	public static function get_dbst_key($key, $dbst_id, $kind = 0)
	{
		/** first validation **/
		if(trim($key) == '' or trim($dbst_id) == '') return false;
		
		$dbst_data = self::get_field($dbst_id);
		return (isset($dbst_data->$key) ? $dbst_data->$key : NULL);
	}
	
    /**
     * Returns new dbst id
     * @author Howard R <howard@realtyna.com>
     * @static
     * @return int
     */
	public static function get_new_dbst_id()
	{
		$max_dbst_id = wpl_db::get("MAX(`id`)", "wpl_dbst", '', '', '', "`id`<'10000'");
		return max(($max_dbst_id+1), 3000);
	}
	
    /**
     * Removes a dbst field from dbst table
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $dbst_id
     * @return boolean
     */
	public static function remove_dbst($dbst_id)
	{
		/** first validation **/
		if(!$dbst_id) return false;
		
        /** Multilingual **/
        if(wpl_global::check_addon('pro')) wpl_addon_pro::remove_multilingual($dbst_id);

        /** trigger event **/
		wpl_global::event_handler('dbst_removed', $dbst_id);
        
        $table_column = wpl_flex::get_dbst_key('table_column', $dbst_id);
        
		wpl_db::delete("wpl_dbst", $dbst_id);
        
        // Remove field from all blogs
        if(wpl_global::is_multisite() and trim($table_column) != '')
        {
            $current_blog_id = wpl_global::get_current_blog_id();
            
            $blogs = wpl_db::select("SELECT `blog_id` FROM `#__blogs`", 'loadColumn');
            foreach($blogs as $blog_id)
            {
                if($blog_id == $current_blog_id) continue;
                switch_to_blog($blog_id);
                
                wpl_db::q("DELETE FROM `#__wpl_dbst` WHERE `table_column`='$table_column'", "UPDATE");
            }

            switch_to_blog($current_blog_id);
        }
        
        return true;
	}
	
    /**
     * Generates modify form of a dbst field
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $dbst_type
     * @param int $dbst_id
     * @param int $kind
     * @param int $cat_id
     * @return void
     */
	public static function generate_modify_form($dbst_type = 'text', $dbst_id = 0, $kind = 0, $cat_id = 0)
	{
		/** first validation **/
		if(!$dbst_type) return;
		
        if($dbst_id != 0)
        {
            $dbst_data = self::get_field($dbst_id);
        }
        else
        {
            $dbst_data = new stdClass();
            $dbst_data->category = $cat_id;
        }

        if(wpl_global::check_addon('pro'))
        {
            $dbst_data = self::render_comparable($dbst_data);
        }
		
		$done_this = false;
		$type = $dbst_type;
		$values = $dbst_data;
		$options = isset($values->options) ? json_decode($values->options, true) : array();
		
		$__prefix = 'wpl_flex_modify';
		
		$dbcats = self::get_categories(0, $kind);
		$listings = wpl_listing_types::get_listing_types();
		$property_types = wpl_property_types::get_property_types();
        $user_types = wpl_users::get_user_types(1, 'loadAssocList');
        $memberships = wpl_users::get_wpl_memberships();
		
		/** get files **/
		$dbst_modifypath = WPL_ABSPATH . DS . 'libraries' . DS . 'dbst_modify';
		$files = array();
		
		if(wpl_folder::exists($dbst_modifypath))
		{
			$files = wpl_folder::files($dbst_modifypath, '.php$');
			
			foreach($files as $file)
			{
				include($dbst_modifypath .DS. $file);
			}
			
			if(!$done_this)
			{
				/** include default file **/
				include _wpl_import('libraries.dbst_modify.main.default', true, true);
			}
		}
	}
	
    /**
     * Runs dbst type queries for a dbst field
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $dbst_id
     * @param string $dbst_type
     * @param int $dbst_kind
     * @param string $query_type
     */
	public static function run_dbst_type_queries($dbst_id, $dbst_type, $dbst_kind, $query_type = 'add')
	{
		$dbst_type_data = self::get_dbst_type(0, $dbst_kind, $dbst_type);
        $dbst = self::get_field($dbst_id);
		$kind_table = self::get_kind_table($dbst_kind, $dbst->category);

        $options = array();
		if($query_type == 'add') $options = self::get_field_options($dbst_id);
        
        /** Configure dbst columns if add mode **/
        if($query_type == 'add' and $dbst_type_data->options)
        {
            $dbst_type_options = json_decode($dbst_type_data->options, true);
            $q = '';
            
            foreach($dbst_type_options as $key=>$value) $q .= "`$key`='$value',";
            if(trim($q)) wpl_db::q("UPDATE `#__wpl_dbst` SET ".trim($q, ', ')." WHERE `id`='$dbst_id'");
        }
        
		/** running all necessary queries **/
		if($query_type == 'add') $queries = $dbst_type_data->queries_add;
		else $queries = $dbst_type_data->queries_delete;
		
		$queries = explode(';', $queries);
		foreach($queries as $query)
		{
			if(trim($query) == '') continue;

            $wpdb = wpl_db::get_DBO();
            $query = str_replace('[TB_PREFIX]', $wpdb->prefix, $query);

			$query = str_replace('[TABLE_NAME]', $kind_table, $query);
			$query = str_replace('[FIELD_ID]', $dbst_id, $query);
            
            /** Set default value if exists **/
			if(isset($options['default_value'])) $query = str_replace('[DEFAULT_VALUE]', $options['default_value'], $query);
            
			wpl_db::q($query);
		}
	}
	
    /**
     * Returns encoded options
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param array $values
     * @param string $prefix
     * @param array $options
     * @return string
     */
	public static function get_encoded_options($values, $prefix, $options = array())
	{
		$length = strlen($prefix);
		
		foreach($values as $key=>$value)
		{
			if(substr($key, 0, $length) != $prefix) continue;
			
			$field = substr($key, $length);
			$options[$field] = $value;
		}
        
		return json_encode($options);
	}
	
    /**
     * Updates a table record
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $table
     * @param int $dbst_id
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
	public static function update($table = 'wpl_dbst', $dbst_id, $key, $value = '')
	{
		/** first validation **/
		if(trim($table) == '' or trim($dbst_id) == '' or trim($key) == '') return false;
		
		return wpl_db::set($table, $dbst_id, $key, $value);
	}
    
    /**
     * Generates wizard form using dbst fields
     * @author Howard R <howard@realtyna.com>
     * @param array $fields
     * @param array $values
     * @param int $item_id
     * @param array $finds
     * @param string $nonce
     * @return void
     */
	public function generate_wizard_form($fields, $values, $item_id = 0, &$finds = array(), $nonce = NULL)
	{
		/** first validation **/
		if(!$fields) return;
        
        // Create Nonce
        if(is_null($nonce)) $nonce = wpl_security::create_nonce('wpl_listing');
        
		/** get files **/
		$path = WPL_ABSPATH .DS. 'libraries' .DS. 'dbst_wizard';
		$files = array();
		
		if(wpl_folder::exists($path)) $files = wpl_folder::files($path, '.php$', false, false);
		
        $wpllangs = wpl_global::check_multilingual_status() ? wpl_addon_pro::get_wpl_languages() : array();
        $has_more_details = false;

        /** store hidden fields used for field-specific */
        $hidden_fields = array();
        
		foreach($fields as $key=>$field)
		{
			if(!$field) return;
			
			$done_this = false;
			$type = $field->type;
            $label = $field->name;
            $mandatory = $field->mandatory;
			$options = json_decode($field->options, true);
            $value = isset($values[$field->table_column]) ? stripslashes($values[$field->table_column]) : NULL;
            $kind = isset($values['kind']) ? $values['kind'] : NULL;
            $specified_children = self::get_field_specific_children($field->id);
			$display = '';

            /** Specific **/
			if(trim($field->listing_specific) != '')
			{
				$specified_listings = explode(',', trim($field->listing_specific, ', '));
				self::$category_listing_specific_array[$field->id] = $specified_listings;
				if(!in_array($values['listing'], $specified_listings))
                {
                    $display = 'display: none;';
                    $hidden_fields[] = $field->id;  
                }
			}
			elseif(trim($field->property_type_specific) != '')
			{
				$specified_property_types = explode(',', trim($field->property_type_specific, ', '));
				self::$category_property_type_specific_array[$field->id] = $specified_property_types;
				if(!in_array($values['property_type'], $specified_property_types))
                {
                    $display = 'display: none;';
                    $hidden_fields[] = $field->id;  
                }
			}
            elseif(trim($field->user_specific) != '')
			{
				$specified_user_types = explode(',', trim($field->user_specific, ', '));
				self::$category_user_specific_array[$field->id] = $specified_user_types;
				if(!in_array($values['membership_type'], $specified_user_types))
                {
                    $display = 'display: none;';
                    $hidden_fields[] = $field->id;  
                }
			}
            elseif(trim($field->field_specific) != '')
            {
                $specified_field = explode(':', trim($field->field_specific));
                $parent_field = self::get_field($specified_field[0]);
                if(isset($parent_field) and (in_array($parent_field->id, $hidden_fields) or $values[$parent_field->table_column] != $specified_field[1]))
                {
                    $display = 'display: none;';
                    $hidden_fields[] = $field->id;
                } 
            }
			elseif(isset($options['access']))
			{
				foreach($options['access'] as $access)
				{
					if(!wpl_global::check_access($access))
					{
						$display = 'display: none;';
                        $hidden_fields[] = $field->id;  
						break;
					}
				}
			}
			
            /** More Details **/
            if($type == 'more_details' and !$has_more_details)
            {
                echo '<div class="wpl_listing_field_container wpl-pwizard-prow-'.$type.'" id="wpl_listing_field_container'.$field->id.'">';
                echo '<label for="wpl_c_'.$field->id.'"><span>'.__($label, 'real-estate-listing-realtyna-wpl').'</span></label>';
                echo '<div id="wpl_more_details'.$field->id.'" style="display: none;" class="wpl-fields-more-details-block">';
                
                $has_more_details = true;
            }
            elseif($type == 'more_details' and $has_more_details)
            {
                /** Only one details field is acceptable in each category **/
                continue;
            }
            
            /** Accesses **/
			if(isset($field->accesses) and trim($field->accesses) != '' and wpl_global::check_addon('membership'))
			{
				$accesses = explode(',', trim($field->accesses, ', '));
                $cur_membership_id = wpl_users::get_user_membership();
                
				if(!in_array($cur_membership_id, $accesses) and trim($field->accesses_message) == '') continue;
                elseif(!in_array($cur_membership_id, $accesses) and trim($field->accesses_message) != '')
                {
                    echo '<div class="prow wpl_listing_field_container prow-'.$type.'" id="wpl_listing_field_container'.$field->id.'" style="'.$display.'">';
                    echo '<label for="wpl_c_'.$field->id.'">'.__($label, 'real-estate-listing-realtyna-wpl').'</label>';
                    echo '<span class="wpl-access-blocked-message">'.__($field->accesses_message, 'real-estate-listing-realtyna-wpl').'</span>';
                    echo '</div>';

                    continue;
                }
			}
            
			/** js validation **/
			self::$wizard_js_validation[$field->id] = self::generate_js_validation($field);
            
			if(isset($finds[$type]))
			{
				echo '<div class="prow wpl_listing_field_container prow-'.$type.'" id="wpl_listing_field_container'.$field->id.'" style="'.$display.'">';
				include($path .DS. $finds[$type]);
				echo '</div>';
				
				continue;
			}
			
			echo '<div class="prow wpl_listing_field_container prow-'.$type.'" id="wpl_listing_field_container'.$field->id.'" style="'.$display.'">';
			foreach($files as $file)
			{
				include($path .DS. $file);
				
				if($done_this)
				{
					$finds[$type] = $file;
					break;
				}
			}
            
			echo '</div>';
		}
        
        if($has_more_details)
        {
            echo '</div></div>';
        }
	}
	
    /**
     * Returns js validation code
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param object $field
     * @return string
     */
	public static function generate_js_validation($field)
	{
		$field = (object) $field;
        $label = $field->name;
        $mandatory = $field->mandatory;
        
		$js_string = '';
		
		$path = WPL_ABSPATH .DS. 'libraries' .DS. 'dbst_wizard' .DS. 'js_validation' .DS. $field->type .'.php';
		$override_path = WPL_ABSPATH .DS. 'libraries' .DS. 'dbst_wizard' .DS. 'js_validation' .DS. 'overrides' .DS. $field->type .'.php';
		
		if(wpl_file::exists($override_path)) $path = $override_path;
		
		/** include file **/
		if(wpl_file::exists($path)) include $path;
		
		return $js_string;
	}
	
    /**
     * Returns field options
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $field_id
     * @param boolean $return_array
     * @return mixed
     */
	public static function get_field_options($field_id, $return_array = true)
	{
		$field = self::get_field($field_id);
		return ($return_array ? json_decode($field->options, true) : $field->options);
	}
    
    /**
     * Sorts flex fields
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $sort_ids
     */
	public static function sort_flex($sort_ids)
	{
		$query = "SELECT DISTINCT `category`  FROM `#__wpl_dbst` WHERE `id` IN ($sort_ids) ORDER BY `index` ASC";
		$flex_category = wpl_db::select($query, 'loadAssoc');

		$counter = 0;
		$ex_sort_ids = explode(',', $sort_ids);
		
		foreach($ex_sort_ids as $ex_sort_id)
		{
			if($counter < 10) $index = $flex_category["category"].'.00'.$counter;
            elseif($counter < 100) $index = $flex_category["category"].'.0'.$counter;
			else $index = $flex_category["category"].'.'.$counter;

			self::update('wpl_dbst', $ex_sort_id, 'index', $index);
			$counter++;
		}
	}
	/**
	 * Sorts flex categories
	 * @author Alfred M <Alfred@realtyna.com>
	 * @static
	 * @param string $sort_ids
	 */
	public static function sort_flex_categories($sort_ids=null)
	{
		$query = "SELECT DISTINCT `index` FROM `#__wpl_dbcat` WHERE `id` IN ($sort_ids) ORDER BY `index` ASC";
		$flex_category = wpl_db::select($query, 'loadAssoc');
		$counter = 0;
		$ex_sort_ids = explode(',', $sort_ids);

		foreach($ex_sort_ids as $ex_sort_id)
		{
			if ($counter < 10) $index = $flex_category["index"] . '.00' . $counter;
			elseif ($counter < 100) $index = $flex_category["index"] . '.0' . $counter;
			else $index = $flex_category["index"] . '.' . $counter;

			self::update('wpl_dbcat', $ex_sort_id, 'index', $index);
			$counter++;
		}
	}
	/**
	 * Generate search fields based on DBST fields
	 * @author Steve A. <steve@realtyna.com>
	 * @param  array  $fields
	 * @param  array  $finds
	 * @return array
	 */
	public function generate_search_fields($fields, $finds = array())
	{
		$fields = json_decode(json_encode($fields), true);

		$path = WPL_ABSPATH .DS. 'libraries' .DS. 'widget_search' .DS. 'frontend';
		$files = array();
		$widget_id = '';
		
		if(wpl_folder::exists($path)) $files = wpl_folder::files($path, '.php$');
		
		$rendered = array();
		foreach($fields as $key=>$field)
		{
			$type = $field['type'];
			$field_id = $field['id'];
			$field_data = $field;
			$options = json_decode($field['options'], true);
			$specified_children = self::get_field_specific_children($field_id);

			$done_this = false;
			$html = '';
			
			if(isset($finds[$type]))
			{
				$html .= '<span class="wpl_search_field_container '.(isset($field['type']) ? $field['type'].'_type' : '').' '.((isset($field['type']) and $field['type'] == 'predefined') ? 'wpl_hidden' : '').'" id="wpl'.$widget_id.'_search_field_container_'.$field['id'].'">';
				include($path .DS. $finds[$type]);
				$html .= '</span> ';
				
				$rendered[$field_id]['id'] = $field_id;
				$rendered[$field_id]['field_options'] = json_decode($field['options'], true);
				$rendered[$field_id]['html'] = $html;
                $rendered[$field_id]['current_value'] = isset($current_value) ? $current_value : NULL;
				continue;
			}
			
			$html .= '<span class="wpl_search_field_container '.(isset($field['type']) ? $field['type'].'_type' : '').' '.((isset($field['type']) and $field['type'] == 'predefined') ? 'wpl_hidden' : '').'" id="wpl'.$widget_id.'_search_field_container_'.$field['id'].'">';
			foreach($files as $file)
			{
				include($path .DS. $file);
				
				/** proceed to next field **/
				if($done_this)
				{
					$finds[$type] = $file;
					break;
				}
			}
			$html .= '</span> ';
			
			$rendered[$field_id]['id'] = $field_id;
			$rendered[$field_id]['field_options'] = json_decode($field['options'], true);
			$rendered[$field_id]['html'] = $html;
            $rendered[$field_id]['current_value'] = isset($current_value) ? $current_value : NULL;
		}
        
		return $rendered;
	}
    
    /**
     * Returns WPL tag fields
     * @author Howard. <howard@realtyna.com>
     * @static
     * @param int $kind
     * @return array of objects
     */
    public static function get_tag_fields($kind = 0)
    {
        if(is_array($kind)) return wpl_flex::get_fields(NULL, NULL, NULL, NULL, NULL, "AND `type`='tag' AND `enabled`>='1' AND `kind` IN (".implode(',', $kind).")");
        else return wpl_flex::get_fields(NULL, NULL, NULL, NULL, NULL, "AND `type`='tag' AND `enabled`>='1' AND `kind`='$kind'");
    }
    
    /**
     * Returns field, category and kind names of a certain field
     * @author Howard. <howard@realtyna.com>
     * @static
     * @param int $dbst_id
     * @return array
     */
    public static function get_names($dbst_id)
    {
        if(!trim($dbst_id)) return array();
        
        $dbst = wpl_flex::get_field($dbst_id);
        $dbcat = wpl_flex::get_category($dbst->category);
        $kind = wpl_flex::get_kind($dbst->kind);
        
        return array('dbst_name'=>__($dbst->name, 'real-estate-listing-realtyna-wpl'), 'dbcat_name'=>__($dbcat->name, 'real-estate-listing-realtyna-wpl'), 'kind_name'=>__($kind['name'], 'real-estate-listing-realtyna-wpl'));
    }

    /**
     * Maintain sortable status from a input fields
     * @author Edward <edward@realtyna.com>
     * @static
     * @param array|object $fields
     * @return mixed
     */
    public static function render_sortable($fields)
    {
        if(!is_array($fields) and !is_object($fields)) return false;

        $is_single = is_array($fields) ? false : true;
        if($is_single) $fields = array($fields);

        foreach($fields as $field)
        {
            if(!trim($field->table_name) or !trim($field->table_column)) $field->sortable = 0;

            $not_sortables = self::get_not_sortables();
            foreach($not_sortables as $pattern)
            {
                if(preg_match($pattern, $field->type)) $field->sortable = 0;
            }
        }

        return $is_single ? reset($fields) : $fields;
    }

    /**
     * Gets all of not sortables types (type is a field at dbst)
     * @author Edward <edward@realtyna.com>
     * @static
     * @return array
     */
    public static function get_not_sortables()
    {
        /** RegExp **/
        return array('/^addon_*/', '/^sys_*/', '/^fs_*/', '/separator/', '/attachments/', '/gallery/', '/googlemap/', '/locations/', '/neighborhood/', '/parent/', '/rooms/', '/tag/', '/textarea/', '/textsearch/', '/upload/');
    }

    /**
     * Maintain comparable status from a input fields
     * @author Edward <edward@realtyna.com>
     * @static
     * @param array|object $fields
     * @return mixed
     */
    public static function render_comparable($fields)
    {
        if(!is_array($fields) and !is_object($fields)) return false;

        $is_single = is_array($fields) ? false : true;
        if($is_single) $fields = array($fields);

        foreach($fields as $field)
        {
            /** When user inserts a new row **/
            if(!isset($field->table_name) or !isset($field->table_column)) continue;

            if(!trim($field->table_name) or !trim($field->table_column)) $field->comparable = 0;
            $field->comparable_row = (intval($field->comparable) and in_array($field->type, self::get_comparable_row_types())) ? 1 : 0;
        }

        return $is_single ? reset($fields) : $fields;
    }

    /**
     * Gets all of comparable_row types (type is a field at dbst)
     * @author Edward <edward@realtyna.com>
     * @static
     * @return array
     */
    public static function get_comparable_row_types()
    {
        return array('area', 'number', 'price', 'rooms', 'text');
    }

    /**
     * Get list of fields that are dependent to selected field
     * @author Steve A. <steve@realtyna.com>
     * @static
     * @param  string $id Field ID
     * @return string     List of fields
     */
    public static function get_field_specific_children($id)
    {
        $result = array();
        $fields = wpl_db::select("SELECT `id`, `field_specific` FROM `#__wpl_dbst` WHERE `field_specific` LIKE '{$id}:%'");

        foreach ($fields as $field) 
        {
            $value = explode(':', $field->field_specific);
            $result[] = $field->id.':'.$value[1];
        }

        return implode(',', $result);
    }
    
    public static function get_field_values($dbst_id)
    {
        $field = wpl_flex::get_field($dbst_id);
        $options = json_decode($field->options, true);
        
        $values = array();
        if($field->type == 'select')
        {
            $params = isset($options['params']) ? $options['params'] : array();
            foreach($params as $param) $values[$param['key']] = array('value'=>$param['key'], 'label'=>$param['value']);
        }
        elseif($field->type == 'feature')
        {
            $params = isset($options['values']) ? $options['values'] : array();
            foreach($params as $param) $values[$param['key']] = array('value'=>$param['key'], 'label'=>$param['value']);
        }
        elseif($field->type == 'listings')
        {
            $listings = wpl_global::get_listings();
            foreach($listings as $listing) $values[$listing['id']] = array('value'=>$listing['id'], 'label'=>$listing['name']);
        }
        elseif($field->type == 'property_types')
        {
            $property_types = wpl_global::get_property_types();
            foreach($property_types as $property_type) $values[$property_type['id']] = array('value'=>$property_type['id'], 'label'=>$property_type['name']);
        }
        
        return $values;
    }
}