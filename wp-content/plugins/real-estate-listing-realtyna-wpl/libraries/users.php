<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Users Library
 * @author Howard R <howard@realtyna.com>
 * @since WPL1.0.0
 * @package WPL
 * @date 03/01/2013
 */
class wpl_users
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
    public $where;
    public $select;
    public $finish_time;
    public $time_taken;
    public $total;

    /**
     * Used for caching in get_user_membership function
     * @static
     * @var array
     */
    public static $user_memberships = array();
    
    /**
     * Returns plisting fields of profiles
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $category
     * @param int $kind
     * @param int $enabled
     * @return array
     */
	public static function get_plisting_fields($category = '', $kind = 2, $enabled = 1)
	{
		return wpl_flex::get_fields($category, $enabled, $kind, 'plisting', '1');
	}
	
    /**
     * Returns pshow fields of profiles
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $category
     * @param int $kind
     * @param int $enabled
     * @return array
     */
    public static function get_pshow_fields($category = '', $kind = 2, $enabled = 1)
	{
		return wpl_flex::get_fields($category, $enabled, $kind, 'pshow', '1');
	}
    
    /**
     * Returns PDF fields of profiles
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int|string $category
     * @param int $kind
     * @param int $enabled
     * @return array of objects
     */
    public static function get_pdf_fields($category = '', $kind = 2, $enabled = 1)
	{
		return wpl_flex::get_fields($category, $enabled, $kind, 'pdf', '1');
	}
    
    /**
     * Removes a user from WPL
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return int affected rows
     */
	public static function delete_user_from_wpl($user_id)
	{
        /** trigger event **/
		wpl_global::event_handler('user_deleted_from_wpl', array('id'=>$user_id));

		$query = "DELETE FROM `#__wpl_users` WHERE `id`='$user_id'";
		return wpl_db::q($query);
	}
    
    /**
     * Adds WordPress user to WPL
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param int $group_id
     * @return boolean
     */
	public static function add_user_to_wpl($user_id, $group_id = -1)
	{
		/** User already added **/
		if(wpl_users::get_wpl_user($user_id)) return true;

        // Check Maximum Agents feature of Franchise Addon
        if(wpl_global::is_multisite())
        {
            $fs = new wpl_addon_franchise();
            if($fs->is_agents_limit_reached(wpl_global::get_current_blog_id())) return false;
        }

		$user_data = wpl_users::get_user($user_id);
		$default_data = wpl_users::get_wpl_data($group_id);
		
		$forbidden_fields = array('id', 'first_name', 'last_name', 'main_email', 'blog_id');
		$auto_query1 = '';
		$auto_query2 = '';
		
		foreach($default_data as $key=>$value)
		{
			if(in_array($key, $forbidden_fields)) continue;
			
			$value = wpl_db::escape($value);
			$auto_query1 .= "`$key`,";
			$auto_query2 .= "'$value',";
		}
		
		if($user_data)
		{
			$auto_query1 .= "`first_name`,`last_name`,`main_email`,";
			$auto_query2 .= "'".wpl_db::escape($user_data->data->meta['first_name'])."','".wpl_db::escape($user_data->data->meta['last_name'])."','".wpl_db::escape($user_data->data->user_email)."',";
		}
		
		$auto_query1 = trim($auto_query1, ', ');
		$auto_query2 = trim($auto_query2, ', ');
		
		$query = "INSERT INTO `#__wpl_users` (`id`, ".$auto_query1.") VALUES ('".$user_id."', ".$auto_query2.")";
		$result = wpl_db::q($query);
		
        /** trigger event **/
		wpl_global::event_handler('user_added_to_wpl', array('id'=>$user_id));
        
        /** finalize user **/
        wpl_users::finalize($user_id);
        
		return $result;
	}
	
    /**
     * Returns All WordPress users. Wrapper for WordPress get_users function
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param array $args
     * @param boolean $add_data
     * @return array
     */
	public static function get_all_wp_users($args, $add_data = false)
	{
		$users = get_users($args);
		if(!$add_data) return $users;
		
		foreach($users as $key=>$user)
		{
			$users[$key]->meta = self::get_user_meta($user->ID);
			$users[$key]->wpl_data = self::get_wpl_data($user->ID);
		}
		
		return $users;
	}
	
    /**
     * Returns WordPress users
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $condition
     * @return array
     */
	public static function get_wp_users($condition = '')
	{
		$query = "SELECT * FROM `#__users` AS u LEFT JOIN `#__wpl_users` AS wpl ON u.ID = wpl.id WHERE 1 $condition";
		return wpl_db::select($query);
	}
	
    /**
     * Returns WPL users
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $condition
     * @return array
     */
	public static function get_wpl_users($condition = '')
	{
		$query = "SELECT * FROM `#__users` AS u INNER JOIN `#__wpl_users` AS wpl ON u.ID = wpl.id WHERE 1 $condition";
		return wpl_db::select($query);
	}
	
    /**
     * Returns full data of a user
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return object
     */
	public static function get_user($user_id = NULL)
	{
		/** fetch currenr user data if user id is empty **/
		if(trim($user_id) == '') $user_id = self::get_cur_user_id();
		
		/** fetch user data **/
		$user_data = get_userdata($user_id);
        
        /** Invalid or Guest User **/
		if(!is_object($user_data)) $user_data = new stdClass();

		$user_data->meta = self::get_user_meta($user_id);
		$user_data->wpl_data = self::get_wpl_data($user_id);
		
		return $user_data;
	}
	
    /**
     * Returns WPL data of a user
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return object
     */
	public static function get_wpl_user($user_id = NULL)
	{
		// Load Current User
		if(trim($user_id) == '') $user_id = self::get_cur_user_id();

		$cache_key = 'wpl_get_user_'.$user_id;

        // Return From Cache
        $cached = wp_cache_get($cache_key);
        if($cached) return $cached;
		
		$query = "SELECT * FROM `#__wpl_users` WHERE `id`='$user_id'";
		$user = wpl_db::select($query, 'loadObject');

        // Set to Cache
        wp_cache_set($cache_key, $user, '', 100);

		return $user;
	}
	
    /**
     * Returns user meta
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param string $key
     * @param string $single
     * @return boolean|array
     */
	public static function get_user_meta($user_id = NULL, $key = '', $single = '')
	{
		$rendered_meta = array();
		
		if(trim($user_id) == '') $user_id = self::get_cur_user_id();
		if(!$user_id) return false;
		
		$user_meta = get_user_meta($user_id, $key, $single);
		
		if(!$user_meta) return null;
		
		foreach($user_meta as $key=>$meta)
		{
			if(count($meta) == 1) $rendered_meta[$key] = $meta[0];
			else $rendered_meta[$key] = $meta;
		}
		
		return $rendered_meta;
	}
	
    /**
     * Returns WPL data of a user
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return object
     */
	public static function get_wpl_data($user_id = NULL)
	{
		if(trim($user_id) == '') $user_id = self::get_cur_user_id();
		return wpl_db::get('*', 'wpl_users', 'id', $user_id);
	}
	
    /**
     * Returns current user ID
     * @author Howard R <howard@realtyna.com>
     * @static
     * @return int
     */
	public static function get_cur_user_id()
	{
		return get_current_user_id();
	}
    
    /**
     * Get membership ID of a user
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return int
     */
    public static function get_user_membership($user_id = NULL)
	{
        /** Current User **/
        if(is_null($user_id)) $user_id = self::get_cur_user_id();
        
        // Return From Cache
        if(isset(self::$user_memberships[$user_id])) return self::$user_memberships[$user_id];
        
		$membership_id = wpl_db::get('membership_id', 'wpl_users', 'id', $user_id);
        
        /** add to cache **/
		self::$user_memberships[$user_id] = $membership_id;

        /** return from cache if exists **/
		if(isset(self::$user_memberships[$user_id])) return self::$user_memberships[$user_id];
		else return NULL;
	}
    
    /**
     * Get user type of a user
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return int
     */
    public static function get_user_user_type($user_id = NULL)
	{
        /** Current User **/
        if(is_null($user_id)) $user_id = self::get_cur_user_id();
        
		return wpl_db::get('membership_type', 'wpl_users', 'id', self::get_user_membership($user_id));
	}
	
    /**
     * Returns User ID by username
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $username
     * @return int|boolean
     */
	public static function get_id_by_username($username)
	{
        /** first validation **/
        if(!trim($username)) return false;
        
		$query = "SELECT * FROM `#__users` WHERE `user_login`='$username'";
		$user = wpl_db::select($query, 'loadObject');
		
		if($user) return $user->ID;
        else return 0;
	}
	
    /**
     * Returns User ID by email
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $email
     * @return int|boolean
     */
	public static function get_id_by_email($email)
	{
        /** first validation **/
        if(!trim($email)) return false;
        
		$query = "SELECT * FROM `#__users` WHERE `user_email`='$email'";
		$user = wpl_db::select($query, 'loadObject');
		
		if($user) return $user->ID;
        else return 0;
	}
	
    /**
     * Return IP of website visitor
     * @author Howard R <howard@realtyna.com>
     * @static
     * @return string
     */
	public static function get_current_ip()
	{
		$ip = wpl_request::getVar('REMOTE_ADDR', '', 'SERVER');
		if(empty($ip) || $ip == '127.0.0.1') $ip = wpl_request::getVar('HTTP_X_REAL_IP', $ip, 'SERVER');

		return $ip;
	}
	
    /**
     * Returns role of user
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param boolean $superadmin_role
     * @return string
     */
	public static function get_role($user_id = NULL, $superadmin_role = true)
	{
		$user_data = self::get_user($user_id);
        $role = (isset($user_data->roles) and is_array($user_data->roles)) ? reset($user_data->roles) : 'guest';
        
        /** check network admin **/
        if($superadmin_role and wpl_users::is_super_admin($user_id)) $role = 'superadmin';
        
        return $role;
	}
	
    /**
     * Returns WPL roles
     * @author Howard R <howard@realtyna.com>
     * @static
     * @return array
     */
	public static function get_wpl_roles()
	{
		$roles = array();
        $roles['superadmin'] = 'superadmin';
		$roles['admin'] = 'administrator';
		$roles['editor'] = 'editor';
		$roles['agent'] = 'author';
		$roles['Contributor'] = 'Contributor';
		$roles['subscriber'] = 'subscriber';
		$roles['guest'] = 'guest';
		
		return $roles;
	}
	
    /**
     * Get role point
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $role
     * @return int
     */
	public static function get_role_point($role)
	{
		/** get all roles **/
		$roles = self::get_wpl_roles();
		
		/** role validation **/
		if(!in_array($role, $roles)) $role = 'guest';
		
		$roles_point = array();
        $roles_point['superadmin'] = 6;
		$roles_point['administrator'] = 5;
		$roles_point['editor'] = 4;
		$roles_point['author'] = 3;
		$roles_point['Contributor'] = 2;
		$roles_point['subscriber'] = 1;
		$roles_point['guest'] = 0;
		
		return $roles_point[$role];
	}
	
    /**
     * Checks if user have the capability or not
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param array|string $caps
     * @param int $user_id
     * @return boolean
     */
	public static function is($caps, $user_id)
	{
		$result = false;
		
		if(is_array($caps))
		{
			foreach($caps as $cap)
			{
				if(self::is($cap, $user_id)) return true;
			}
			
			return false;
		}
		
		$user_data = self::get_user($user_id);
		if(!$user_data) return false;
		
		if($user_data->caps[$caps]) $result = true;
		return $result;
	}
	
    /**
     * Returns WPL memberships
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $condition
     * @return array of objects
     */
	public static function get_wpl_memberships($condition = NULL)
	{
		$query = "SELECT * FROM `#__wpl_users` WHERE 1 AND `id` < 0 ".(trim($condition) ? $condition : '')." ORDER BY `index` ASC";
		return wpl_db::select($query);
	}
    
    /**
     * Use wpl_users::get_user_types instead
     * @deprecated since version 1.8.3
     * @return array of objects
     */
	public static function get_membership_types()
	{
		return self::get_user_types();
	}
	
    /**
     * Returns WPL user types
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $enabled
     * @param string $return_type
     * @return array of objects
     */
    public static function get_user_types($enabled = 1, $return_type = 'loadObjectList')
    {
        $query = "SELECT * FROM `#__wpl_user_group_types` WHERE `enabled`>='$enabled' ORDER BY `index` ASC";
		return wpl_db::select($query, $return_type);
    }
    
    /**
     * Returns one user type record
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $id
     * @param int $enabled
     * @return object
     */
    public static function get_user_type($id = 1, $enabled = 0)
    {
        $query = "SELECT * FROM `#__wpl_user_group_types` WHERE `id`='$id' AND `enabled`>='$enabled'";
		return wpl_db::select($query, 'loadObject');
    }
    
    /**
     * Creates a new user type and returns its id
     * @author Howard <howard@realtyna.com>
     * @static
     * @return int $id
     */
    public static function create_default_user_type()
    {
        $id = self::get_new_user_type_id();
        wpl_db::q("INSERT INTO `#__wpl_user_group_types` (`id`,`editable`,`deletable`,`index`,`enabled`) VALUES ('$id','2','1','$id','1')", 'INSERT');
        
        return $id;
    }
    
    /**
     * Returns id for new user type
     * @author Howard <howard@realtyna.com>
     * @static
     * @return int
     */
    public static function get_new_user_type_id()
    {
        $query = "SELECT MAX(id) as max_id FROM `#__wpl_user_group_types`";
		$result = wpl_db::select($query, 'loadResult');
		$id = max($result, 100);
        
		/** generate new user type id **/
		return ($id+1);
    }
    
    /**
     * Removes user types
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $id
     * @return boolean
     */
    public static function remove_user_type($id)
	{
        $user_type = self::get_user_type($id);
        
		/** don't remove undeletable user types **/
		if(!$user_type or ($user_type and !$user_type->deletable)) return false;
        
        /** trigger event **/
		wpl_global::event_handler('user_type_removed', array('id'=>$id));
        
		wpl_db::q("DELETE FROM `#__wpl_user_group_types` WHERE `id`='$id'", 'DELETE');
        wpl_db::q("UPDATE `#__wpl_users` SET `membership_type`='' WHERE `membership_type`='$id'", 'UPDATE');
        
		return true;
	}
    
    /**
     * Returns Unique new membership id
     * @author Howard R <howard@realtyna.com>
     * @static
     * @return int
     */
	public static function get_membership_id()
	{
		$query = "SELECT MIN(id) as min_id FROM `#__wpl_users`";
		$result = wpl_db::select($query, 'loadResult');
		
		/** generate new membership id **/
		return ($result-1);
	}
	
    /**
     * Removes a membership
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $membership_id
     * @return boolean
     */
	public static function remove_membership($membership_id)
	{
		/** don't remove default and guest membership **/
		if(in_array($membership_id, array(-1, -2))) return false;
		
		$query = "UPDATE `#__wpl_users` SET `membership_id`='-1' WHERE `membership_id`='$membership_id'";
		wpl_db::q($query);
		
        /** trigger event **/
		wpl_global::event_handler('membership_removed', array('id'=>$membership_id));
		
		$query = "DELETE FROM `#__wpl_users` WHERE `id`='$membership_id'";
		wpl_db::q($query);
		
        
		return true;
	}
	
    /**
     * Returns Data of a specific membership
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $membership_id
     * @return object|boolean
     */
	public static function get_membership($membership_id)
	{
        // Return false if it's not a membership ID
		if($membership_id >= 0) return false;
        
		$query = "SELECT * FROM `#__wpl_users` WHERE `id`='$membership_id'";
		return wpl_db::select($query, 'loadObject');
	}
	
    /**
     * Updates a user record
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $table
     * @param int $id
     * @param string $key
     * @param mixed $value
     * @return boolean
     */
	public static function update($table = 'wpl_users', $id, $key, $value = '')
	{
		/** first validation **/
		if(trim($table) == '' or trim($id) == '' or trim($key) == '') return false;
		return wpl_db::set($table, $id, $key, $value);
	}
    
    /**
     * Check is user is administrator or not
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return boolean
     */
	public static function is_administrator($user_id = NULL)
	{
		/** get current user id **/
		if(!trim($user_id)) $user_id = wpl_users::get_cur_user_id();
		if($user_id == 0 or $user_id == '') return false;
		
		$administrator = wpl_global::has_permission('administrator', $user_id);
		$super_admin = wpl_users::is_super_admin($user_id);
		
		if($super_admin) return true;
		return $administrator;
	}
	
    /**
     * for checking if a user is wordpress network admin or not.
     * USE is_administrator FUNCTION IF YOU WANT TO CHECK ADMIN. this function is checking super admin (Network admin)
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return boolean
     */
	public static function is_super_admin($user_id = NULL)
	{
		/** get current user id **/
		if(!trim($user_id)) $user_id = wpl_users::get_cur_user_id();
		if($user_id == 0 or $user_id == '') return false;
		
		return is_super_admin($user_id);
	}
	
    /**
     * Check if user is added to WPL or not
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return boolean
     */
	public static function is_wpl_user($user_id = NULL)
	{
		/** get current user id **/
		if(!trim($user_id)) $user_id = wpl_users::get_cur_user_id();
		if($user_id == 0 or $user_id == '') return false;
		
		$result = wpl_users::get_wpl_user($user_id);
		
		if(!$result) return false;
		else return true;
	}
    
    /**
     * Check to see if user exists in WordPress or not
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return boolean
     */
    public static function is_wp_user($user_id)
    {
        $user = get_userdata($user_id);
        
        if($user === false) return false;
        else return true;
    }
	
	/**
     * Changes or Renew Membership of a user
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param int $membership_id
     */
	public static function change_or_renew_membership($user_id, $membership_id = -1)
	{
		$user_data = wpl_users::get_wpl_data($user_id);
        
        /** Add user to WPL if not added **/
        if(!$user_data)
        {
            wpl_users::add_user_to_wpl($user_id);
            $user_data = wpl_users::get_wpl_data($user_id);
        }
        
		// Renew
        if($user_data->membership_id == $membership_id and wpl_global::check_addon('membership'))
		{
			_wpl_import('libraries.addon_membership');
			
			$membership = new wpl_addon_membership();
			$membership->renew($user_id);
		}
		// Membership changed
		else
		{
			wpl_users::change_membership($user_id, $membership_id, true, 'membership_changed');
		}
	}
    
    /**
     * Changes Membership of a user
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param int $membership_id
     * @param boolean $trigger_event
     * @param string $method
     */
	public static function change_membership($user_id, $membership_id = -1, $trigger_event = true, $method = NULL)
	{
		$user_data = wpl_users::get_wpl_data($user_id);
        
        /** Add user to WPL if not added **/
        if(!$user_data)
        {
            wpl_users::add_user_to_wpl($user_id);
            $user_data = wpl_users::get_wpl_data($user_id);
            
            if(!trim($method)) $method = 'membership_changed';
        }
        
        if(!trim($method)) $method = (isset($user_data->membership_id) and $user_data->membership_id != $membership_id) ? 'membership_changed' : 'access_updated';
        
		$membership_data = wpl_users::get_wpl_data($membership_id);
		$query1 = '';
		
		foreach($membership_data as $key=>$value)
		{
			if(substr($key, 0, 7) != 'access_' and substr($key, 0, 8) != 'maccess_') continue;
			$query1 .= "`$key`='".wpl_db::escape($value)."', ";
		}
		
		$query1 .= "`membership_id`='$membership_id', `membership_name`='".$membership_data->membership_name."', `membership_type`='".$membership_data->membership_type."', ";
		$query1 = trim($query1, ', ');
        
		$query = "UPDATE `#__wpl_users` SET ".$query1." WHERE `id`='".$user_id."'";
		wpl_db::q($query);
		
        /** user assigned to a new group **/
        if($method == 'membership_changed' and wpl_global::check_addon('membership'))
        {
            /** Import library **/
            _wpl_import('libraries.addon_membership');
            
            $membership = new wpl_addon_membership();
            $membership->update_expiry_date($user_id, NULL, true);
        }
        
		/** trigger event **/
        $params = array('user_id'=>$user_id, 'previous_membership'=>$user_data->membership_id, 'new_membership'=>$membership_id);
        
		if($trigger_event and $method == 'access_updated') wpl_global::event_handler('user_access_updated', $params);
		elseif($trigger_event and $method == 'membership_changed') wpl_global::event_handler('user_membership_changed', $params);
	}
	
    /**
     * Check Access for a specific user
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $access
     * @param int $owner_id
     * @param int $user_id
     * @return boolean
     */
	public static function check_access($access, $owner_id = 0, $user_id = NULL)
	{
		/** get current user id **/
		if(trim($user_id) == '') $user_id = wpl_users::get_cur_user_id();
        
        // Admin user has access to anything
        if(wpl_users::is_administrator($user_id)) return true;
        
		$user_data = wpl_users::get_wpl_data($user_id);
        
        /** user is registered in WordPress but not in WPL so we choose guest user for accesses **/
        if(!$user_data) $user_data = wpl_users::get_wpl_data(0);
		
		if($access == 'edit')
		{
		    if(wpl_users::is_broker($user_id))
            {
                _wpl_import('libraries.addon_brokerage');

                $brokerage = new wpl_addon_brokerage();
                if($brokerage->is_in_brokerage($owner_id, $user_id)) return true;
            }

			if($owner_id == $user_id) return true;
		}
		elseif($access == 'add')
		{
			$num_prop_limit = $user_data->maccess_num_prop;
			$num_prop = wpl_users::get_users_properties_count($user_id);

			if(wpl_users::is_part_of_brokerage($user_id))
            {
                $broker = wpl_users::get_broker($user_id);

                $num_prop_limit = min($user_data->maccess_num_prop, $broker->maccess_num_prop);
                if($num_prop_limit == '-1') $num_prop_limit = $broker->maccess_num_prop;

                $num_prop = wpl_users::get_users_properties_count($broker->id);
            }
			
			if($num_prop_limit == '-1') return true; # unlimited
			if($num_prop_limit <= $num_prop and !wpl_users::is_administrator($user_id)) return false;
			else return true;
		}
		elseif($access == 'delete')
		{
            if(wpl_users::is_broker($user_id))
            {
                _wpl_import('libraries.addon_brokerage');

                $brokerage = new wpl_addon_brokerage();
                if($brokerage->is_in_brokerage($owner_id, $user_id)) return true;
            }

			if($user_data->access_delete and $owner_id == $user_id) return true;
		}
		elseif($access == 'confirm')
		{
            if(wpl_users::is_broker($user_id))
            {
                _wpl_import('libraries.addon_brokerage');

                $brokerage = new wpl_addon_brokerage();
                if($brokerage->is_in_brokerage($owner_id, $user_id)) return true;
            }

			if($user_data->access_confirm and $owner_id == $user_id) return true;
		}
		else
		{
			return isset($user_data->{'access_'.$access}) ? $user_data->{'access_'.$access} : 0;
		}
		
		return false;
	}
	
    /**
     * Return User Property Count
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param string $condition
     * @return int
     */
	public static function get_users_properties_count($user_id = NULL, $condition = '')
	{
		// Get current user id
		if(trim($user_id) == '') $user_id = wpl_users::get_cur_user_id();

		// Brokerage Addon
        if(wpl_users::is_broker($user_id))
        {
            _wpl_import('libraries.addon_brokerage');

            $brokerage = new wpl_addon_brokerage();
            $user_ids = $brokerage->get_agent_ids($user_id, true);

            $query = "SELECT COUNT(id) FROM `#__wpl_properties` WHERE `user_id` IN (".implode(',', $user_ids).") ".$condition;
            return wpl_db::select($query, 'loadResult');
        }
        else
        {
            $query = "SELECT COUNT(id) FROM `#__wpl_properties` WHERE `user_id`='$user_id' ".$condition;
            return wpl_db::select($query, 'loadResult');
        }
	}
    
    /**
     * Starts the search command
     * @author Howard R <howard@realtyna.com>
     * @param int $start
     * @param int $limit
     * @param string $orderby
     * @param string $order
     * @param array $where
     */
	public function start($start, $limit, $orderby, $order, $where)
    {
		// Start time of model
		$this->start_time = microtime(true);
		
		// Pagination and order options
		$this->start = $start;
		$this->limit = $limit;
		$this->orderby = $orderby;
		$this->order = $order;
		
		// Main table
		$this->main_table = "`#__wpl_users` AS p";
		
		// Queries
		$this->join_query = $this->create_join();
		$this->groupby_query = $this->create_groupby();
		
		// Generate where condition
		$where = (array) $where;
		$this->where = wpl_db::create_query($where);
		
		// Generate select
		$this->select = '*';
    }
	
    /**
     * @author Howard R <howard@realtyna.com>
     * @param boolean $calc_rows
     * @return string
     */
	public function query($calc_rows = true)
    {
		$this->query  = "SELECT ".($calc_rows ? 'SQL_CALC_FOUND_ROWS ' : '').$this->select;
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
     * Run search command of profiles
     * @author Howard R <howard@realtyna.com>
     * @param string $query
     * @return array
     */
	public function search($query = '')
    {
        if(!trim($query)) $query = $this->query;
		
        return wpl_db::select($query);
    }
    
    /**
     * Calculates token time and total result
     * @author Howard R <howard@realtyna.com>
     * @return int
     */
	public function finish()
	{
		$this->finish_time = microtime(true);
        $this->time_taken = $this->finish_time - $this->start_time;
        
		/**
         * We're using a new method for finding total amount of listings
         * $this->total = $this->get_users_count();
         */
        $this->total = wpl_db::select('SELECT FOUND_ROWS()', 'loadResult');
		
		return $this->time_taken;
	}
	
	/**
     * @return number of users according to query condition
     * @author Howard
     */
    public function get_users_count()
    {
        $query = "SELECT COUNT(*) AS count FROM `#__wpl_users` WHERE 1 " . $this->where;
        return wpl_db::select($query, 'loadResult');
    }
	
    /**
     * Returns User Profile Link
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param int $target_id
     * @return string
     */
	public static function get_profile_link($user_id = NULL, $target_id = 0)
	{
		/** fetch currenr user data if user id is empty **/
		if(trim($user_id) == '') $user_id = self::get_cur_user_id();
        
        $user_data = get_userdata($user_id);
        $home_type = wpl_global::get_wp_option('show_on_front', 'posts');
        $home_id = wpl_global::get_wp_option('page_on_front', 0);
            
		if(!$target_id) $target_id = wpl_request::getVar('wpltarget', 0);
		if($target_id)
        {
            $url = wpl_global::add_qs_var('uid', $user_id, wpl_sef::get_page_link($target_id));
            
            if($home_type == 'page' and $home_id == $target_id) $url = wpl_global::add_qs_var('wplview', 'profile_show', $url);
        }
		else
        {
            $url = wpl_sef::get_wpl_permalink(true);
            $nosef = wpl_sef::is_permalink_default();
            $wpl_main_page_id = wpl_sef::get_wpl_main_page_id();
            
            if($nosef or ($home_type == 'page' and $home_id == $wpl_main_page_id))
            {
                $url = wpl_global::add_qs_var('wplview', 'profile_show', $url);
                $url = wpl_global::add_qs_var('uid', $user_id, $url);
            }
            else $url .= urlencode($user_data->data->user_login).'/';
        }
		
        return $url;
    }
    
    /**
     * Generates Sort Options
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param array $params
     * @return array
     */
	public function generate_sorts($params = array())
	{
        $result = NULL;

		include _wpl_import('views.basics.sorts.profile_listing', true, true);
		return $result;
	}
	
    /**
     * Renders profile data
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param array $profile
     * @param array $fields
     * @param array $finds
     * @param boolean $material
     * @return array
     */
	public static function render_profile($profile, $fields, &$finds = array(), $material = false)
	{
		return wpl_property::render_property($profile, $fields, $finds, $material);
	}
	
    /**
     * Register a new user into the WordPress
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $username
     * @param string $email
     * @param string $password
     * @return mixed user id or error object
     */
    public static function register_user($username, $email, $password = NULL)
    {
        // First Validation
        if(!trim($username) or !trim($email)) return false;
        
        // Register User
        $user_id = wp_create_user($username, $password, $email);

        // User already exists but not on current child website so add it to the current child website
        if(is_wp_error($user_id) and in_array($user_id->get_error_code(), array('existing_user_login', 'existing_user_email')) and wpl_global::is_multisite())
        {
            $fs = wpl_sql_parser::getInstance();
            $fs->disable();

            $user = ($user_id->get_error_code() == 'existing_user_login') ? get_user_by('login', $username) : get_user_by('email', $email);
            add_user_to_blog(wpl_global::get_current_blog_id(), $user->ID, 'subscriber');

            $user_id = $user->ID;

            $fs->enable();
        }

        // Return User ID
        return $user_id;
    }
    
    /**
     * Finalize User Profile
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return boolean
     */
	public static function finalize($user_id)
	{
        // Provided user ID is not valid
        if(!trim($user_id)) return false;
        
		// Create Folder
		$folder_path = wpl_items::get_path($user_id, 2);
		if(!wpl_folder::exists($folder_path)) wpl_folder::create($folder_path);

		// Turn Off Force Profile Completion
		if(wpl_global::check_addon('membership')) wpl_db::q("UPDATE `#__wpl_users` SET `maccess_fpc`='0' WHERE `id`='$user_id'", 'UPDATE');
		
        // Multilingual
        if(wpl_global::check_multilingual_status())
        {
            $languages = wpl_addon_pro::get_wpl_languages();
            $current_language = wpl_global::get_current_language();
            
            foreach($languages as $language)
            {
                wpl_global::switch_language($language);
            
                // Generate Rendered Data
                wpl_users::generate_rendered_data($user_id);
                wpl_users::update_text_search_field($user_id);
            }
            
            // Switch to current language again
            wpl_global::switch_language($current_language);
        }
        else
        {
            // Generate Rendered Data
            wpl_users::generate_rendered_data($user_id);
            wpl_users::update_text_search_field($user_id);
        }
        
        // Generate Email Files
		wpl_users::generate_email_files($user_id);
		
        // Triggering Event
        wpl_events::trigger('user_finalized', $user_id);
		
		return true;
    }
	
    /**
     * Generate Text search field
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     */
	public static function update_text_search_field($user_id)
	{
        $user_data = (array) wpl_users::get_wpl_user($user_id);
		
		/** get text_search fields **/
		$fields = wpl_flex::get_fields('', 1, 2, 'text_search', '1');
		$rendered = self::render_profile($user_data, $fields);
		
		$text_search_data = array();
		foreach($rendered as $data)
		{
			if((isset($data['type']) and !trim($data['type'])) or (isset($data['value']) and !trim($data['value']))) continue;
			
			/** default value **/
			$value = isset($data['value']) ? $data['value'] : '';
			$value2 = '';
			$type = isset($data['type']) ? $data['type'] : '';
			
			if($type == 'text' or $type == 'textarea')
			{
				$value = $data['name'] .' '. $data['value'];
			}
			elseif($type == 'locations' and isset($data['locations']) and is_array($data['locations']))
			{
				$location_values = array();
				foreach($data['locations'] as $location_level=>$value)
				{
                    array_push($location_values, $data['keywords'][$location_level]);
                    
                    $abbr = wpl_locations::get_location_abbr_by_name(wpl_db::escape($data['raw'][$location_level]), $location_level);
                    $name = wpl_locations::get_location_name_by_abbr(wpl_db::escape($abbr), $location_level);
                    
                    $ex_space = explode(' ', $name);
                    foreach($ex_space as $value_raw) array_push($location_values, $value_raw);
                    
                    if($name !== $abbr) array_push($location_values, $abbr);
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
			$text_search_data[] = $value;
			if(trim($value2) != '') $text_search_data[] = $value2;
		}
		
        $column = 'textsearch';
        if(wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        
		wpl_db::set('wpl_users', $user_id, $column, implode(' ', wpl_db::escape($text_search_data)));
    }
	
    /**
     * Generate email files of user
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     */
	public static function generate_email_files($user_id)
	{
        $user_data = (array) wpl_users::get_user($user_id);
		$path = wpl_items::get_path($user_id, 2);
        
        /** delete images **/
        if(wpl_file::exists($path.'main_email.png')) wpl_file::delete($path.'main_email.png');
        if(wpl_file::exists($path.'second_email.png')) wpl_file::delete($path.'second_email.png');
        
        /** Get text color **/
        $text_color = wpl_global::get_setting('txtimg_color1');
        if(!trim($text_color) or strlen($text_color) != 6) $text_color = '000000';
        
		if(is_object($user_data['data']) and trim($user_data['data']->wpl_data->main_email) != '') wpl_images::text_to_image($user_data['data']->wpl_data->main_email, $text_color, $path.'main_email.png');
		if(is_object($user_data['data']) and trim($user_data['data']->wpl_data->secondary_email) != '') wpl_images::text_to_image($user_data['data']->wpl_data->secondary_email, $text_color, $path.'second_email.png');
    }
	
    /**
     * This function will generate rendered data of user and save them into db
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return string
     */
	public static function generate_rendered_data($user_id)
	{
		/** get user data **/
		$user_data = (array) wpl_users::get_wpl_user($user_id);
		
		/** location text **/
		$location_text = wpl_users::generate_location_text($user_data);
		
        /** render data **/
        $find_files = array();
        $rendered_fields = self::render_profile($user_data, wpl_users::get_plisting_fields(), $find_files, true);
        
		$result = json_encode(array('rendered'=>$rendered_fields['ids'], 'materials'=>$rendered_fields['columns'], 'location_text'=>$location_text));
        
        $column = 'rendered';
        if(wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        
		$query = "UPDATE `#__wpl_users` SET `$column`='".wpl_db::escape($result)."' WHERE `id`='$user_id'";
		
		/** update **/
		wpl_db::q($query, 'update');
        
        return $result;
	}
	
    /**
     * Generate location text of User
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param array $user_data
     * @param int $user_id
     * @param string $glue
     * @return string
     */
	public static function generate_location_text($user_data, $user_id = 0, $glue = ',')
	{
		/** fetch user data if user id is setted **/
		if($user_id) $user_data = (array) wpl_users::get_wpl_user($user_id);
		if(!$user_id) $user_id = $user_data['id'];
        
		$locations = array();
        
        if(isset($user_data['location7_name']) and trim($user_data['location7_name']) != '') $locations['location7_name'] = __($user_data['location7_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($user_data['location6_name']) and trim($user_data['location6_name']) != '') $locations['location6_name'] = __($user_data['location6_name'], 'real-estate-listing-realtyna-wpl');
		if(isset($user_data['location5_name']) and trim($user_data['location5_name']) != '') $locations['location5_name'] = __($user_data['location5_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($user_data['location4_name']) and trim($user_data['location4_name']) != '') $locations['location4_name'] = __($user_data['location4_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($user_data['location3_name']) and trim($user_data['location3_name']) != '') $locations['location3_name'] = __($user_data['location3_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($user_data['location2_name']) and trim($user_data['location2_name']) != '') $locations['location2_name'] = __($user_data['location2_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($user_data['location1_name']) and trim($user_data['location1_name']) != '') $locations['location1_name'] = __($user_data['location1_name'], 'real-estate-listing-realtyna-wpl');
        if(isset($user_data['zip_name']) and trim($user_data['zip_name']) != '') $locations['zip_name'] = __($user_data['zip_name'], 'real-estate-listing-realtyna-wpl');

        // Location Abbr Names
        if(isset($user_data['location1_name']) and trim($user_data['location1_name'])) $locations['location1_abbr'] = __(wpl_locations::get_location_abbr_by_name($user_data['location1_name'], 1), 'real-estate-listing-realtyna-wpl');
        if(isset($user_data['location2_name']) and trim($user_data['location2_name'])) $locations['location2_abbr'] = __(wpl_locations::get_location_abbr_by_name($user_data['location2_name'], 2), 'real-estate-listing-realtyna-wpl');
        
        // Get the pattern
        $default_pattern = '[location5_name][glue] [location4_name][glue] [location2_name] [zip_name]';
        $location_pattern = wpl_global::get_pattern('user_location_pattern', $default_pattern, 2, NULL);
        
        $location_text = wpl_global::render_pattern($location_pattern, $user_id, $user_data, $glue, $locations);

        // Apply Filters
		@extract(wpl_filters::apply('generate_user_location_text', array('location_text'=>$location_text, 'glue'=>$glue, 'user_data'=>$user_data)));
        
        $final = '';
        $ex = explode($glue, $location_text);
        
        foreach($ex as $value)
        {
            if(trim($value) == '') continue;
            
            $final .= trim($value).$glue.' ';
        }
        
        $location_text = trim($final, $glue.' ');
        
        $column = 'location_text';
        $field = wpl_flex::get_field_by_column($column, 2);
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        
        /** update **/
		$query = "UPDATE `#__wpl_users` SET `$column`='".$location_text."' WHERE `id`='$user_id'";
		wpl_db::q($query, 'update');
        
        return $location_text;
    }
	
    /**
     * This is a very useful function for rendering whole data of user. you need to just pass user_id and get everything!
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param array $plisting_fields
     * @param array $profile
     * @param array $params
     * @param boolean $force
     * @return array
     */
	public static function full_render($user_id, $plisting_fields = NULL, $profile = NULL, $params = array(), $force = false)
	{
		/** get plisting fields **/
		if(!$plisting_fields) $plisting_fields = self::get_plisting_fields();
		
		$raw_data = (array) self::get_wpl_user($user_id);
		if(!$profile) $profile = (object) $raw_data;
		
        $column = 'rendered';
        if(wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        
        /** generate rendered data if rendered data is empty **/
        if(!wpl_settings::get('cache') or $force) $rendered = array();
        elseif(!trim($raw_data[$column]) and wpl_settings::get('cache')) $rendered = json_decode(wpl_users::generate_rendered_data($user_id), true);
        else $rendered = json_decode($raw_data[$column], true);
        
		$result = array();
		$result['data'] = (array) $profile;
		$result['items'] = wpl_items::get_items($profile->id, '', 2, '', 1);
		$result['raw'] = $raw_data;

        $rendered_fields = array();
        if(!isset($rendered['rendered']) or !isset($rendered['materials']))
        {
            /** render data on the fly **/
            $find_files = array();
            $rendered_fields = self::render_profile($profile, $plisting_fields, $find_files, true);
        }
        
		if(isset($rendered['rendered'])) $result['rendered'] = $rendered['rendered'];
		else $result['rendered'] = $rendered_fields['ids'];
        
        if(isset($rendered['materials']) and $rendered['materials']) $result['materials'] = $rendered['materials'];
		else $result['materials'] = $rendered_fields['columns'];
        
		/** location text **/
		if(isset($rendered['location_text'])) $result['location_text'] = $rendered['location_text'];
		else $result['location_text'] = self::generate_location_text($raw_data);
		
		/** profile full link **/
        $target_id = isset($params['wpltarget']) ? $params['wpltarget'] : 0;
		$result['profile_link'] = self::get_profile_link($profile->id, $target_id);
		
        $path = wpl_items::get_path($profile->id, 2);
        $folder = wpl_items::get_folder($profile->id, 2);
        
		/** profile picture **/
		if(isset($raw_data['profile_picture']) and trim($raw_data['profile_picture']) != '')
		{
			$result['profile_picture'] = array(
				'url'=>$folder.$raw_data['profile_picture'],
				'path'=>$path.$raw_data['profile_picture'],
				'name'=>$raw_data['profile_picture']
			);
		}
		
		/** company logo **/
		if(isset($raw_data['company_logo']) and trim($raw_data['company_logo']) != '')
		{
			$result['company_logo'] = array(
				'url'=>$folder.$raw_data['company_logo'],
				'path'=>$path.$raw_data['company_logo'],
				'name'=>$raw_data['company_logo']
			);
		}
		
        /** Generate Email Files **/
        if((isset($raw_data['main_email']) and trim($raw_data['main_email']) and !wpl_file::exists($path.'main_email.png')) or (isset($raw_data['secondary_email']) and trim($raw_data['secondary_email']) and !wpl_file::exists($path.'second_email.png'))) wpl_users::generate_email_files($user_id);
        
		/** Emails url **/
		if(wpl_file::exists($path.'main_email.png')) $result['main_email_url'] = $folder.'main_email.png';
		if(wpl_file::exists($path.'second_email.png')) $result['second_email_url'] = $folder.'second_email.png';
		
		return $result;
	}
	
    /**
     * Authenticate a username and password
     * @author Howard R <howard@realtyna.com>
     * @static
     * @param string $username
     * @param string $password plain password
     * @return array
     */
	public static function authenticate($username, $password)
	{
		$wp_auth = wp_authenticate($username, $password);
		$result = array();
		
		if(get_class($wp_auth) == 'WP_User')
		{
			$result['status'] = 1;
			$result['uid'] = $wp_auth->ID;
		}
		else
		{
			$result['status'] = 0;
			$result['uid'] = 0;
		}
		
		return $result;
	}
	
    /**
     * This Conditional Tag checks if the current visitor is logged in. This is a boolean function, meaning it returns either TRUE or FALSE
     * @author Chris <chris@realtyna.com>
     * @static
     * @return boolean
     */
	public static function check_user_login()
	{
		if(is_user_logged_in()) return true;
		else return false;
	}
    
    /**
     * Insert a user into the database. if successful, returns the newly-created user's user_id, otherwise returns a WP_Error object
     * @author Chris <chris@realtyna.com>
     * @static
     * @param array $user_data
     * @return int|WP_Error
     */
	public static function insert_user($user_data)
	{
		$acceptable_fileds = array('ID', 'user_pass', 'user_login', 'user_nicename', 'user_url', 'user_email', 'display_name', 'nickname', 'first_name', 'last_name', 'description', 'rich_editing', 'user_registered', 'role', 'jabber', 'aim', 'yim');
		$insert_data = array();
        
		if((array_key_exists('user_login', $user_data)) and (array_key_exists('user_email', $user_data)) and (array_key_exists('user_pass', $user_data)))
		{
			foreach($user_data as $key=>$value)
			{
				if(!in_array($key, $acceptable_fileds)) continue;
				$insert_data[$key] = $value;
			}
            
			return wp_insert_user($insert_data);
		}
		else
		{
			return new WP_Error('broke', __("ERROR: Required fileds are invalid!", 'real-estate-listing-realtyna-wpl'));
		}
	}
	
    /**
     * Authenticates a user with option to remember credentials. if successful, returns the newly-created user's user_id, otherwise returns a WP_Error object
     * @author Chris <chris@realtyna.com>
     * @static
     * @param array $user_data
     * @return WP_User|WP_Error
     */
	public static function login_user($user_data)
	{
		$acceptable_fileds = array('user_login', 'user_password', 'remember');
		$login_data = array();
        
		if((is_array($user_data)) && (array_key_exists('user_login', $user_data)) && (array_key_exists('user_password', $user_data)) && (array_key_exists('remember', $user_data)))
		{
			foreach($user_data as $key=>$value)
			{
				if(!in_array($key, $acceptable_fileds)) continue;
                $login_data[$key] = $value;
			}
            
			return wp_signon($login_data, '');
		}
		else
		{
			return new WP_Error('broke', __('ERROR: Required fields are invalid!', 'real-estate-listing-realtyna-wpl'));
		}
	}
    
    /**
     * Get user data by field and data. The possible fields are shown below with the corresponding columns in the wp_users database table. 
     * @author Chris <chris@realtyna.com>
     * @param string $field
     * @param string $data
     * @return mixed WP_User object or false if no user is found. Will also return false if $field does not exist.
     */
	public static function get_user_by($field, $data)
	{
		$acceptable_fileds = array('id', 'slug', 'email', 'login');
		if(in_array($field, $acceptable_fileds)) return get_user_by($field, trim($data));
		else return false;
	}
    
    /**
     * Removes user thumbnails
     * @author Howard R <Howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return boolean
     */
    public static function remove_thumbnails($user_id)
	{
        /** first validation **/
        if(!trim($user_id)) return false;
        
		$ext_array = array('jpg', 'jpeg', 'gif', 'png');
        $path = wpl_items::get_path($user_id, 2);
        $thumbnails = wpl_folder::files($path, 'th.*\.('.implode('|', $ext_array).')$', 3, true);

        foreach($thumbnails as $thumbnail)
        {
            wpl_file::delete($thumbnail);
        }
        
        return true;
	}
    
    /**
     * Wrapper for WordPress wp_logout function
     * @author Howard <howard@realtyna.com>
     * @static
     */
    public static function wp_logout()
    {
        wp_logout();
    }
    
    /**
     * Validate activation key
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $key
     * @return int User ID
     */
    public static function validate_activation_key($key)
    {
        /** first validation **/
        if(!trim($key)) return 0;
        
        $query = "SELECT `id` FROM `#__users` WHERE `user_activation_key`='$key'";
        $id = wpl_db::select($query, 'loadResult');
        
        if($id) return $id;
        return 0;
    }
    
    /**
     * Wrapper for WordPress wp_set_password function
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param string $password
     * @return void
     */
    public static function set_password($user_id, $password)
    {
        wp_set_password($password, $user_id);
    }
    
    /**
     * Wrapper for WordPress update_user_option function
     * @author Howard <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param string $option_name
     * @param mixed $newvalue
     * @param boolean $global
     * @return boolean
     */
    public static function update_user_option($user_id, $option_name, $newvalue, $global = false)
    {
        return update_user_option($user_id, $option_name, $newvalue, $global);
    }
    
    /**
     * Check if username exists or not
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $username
     * @return boolean
     */
    public static function username_exists($username)
    {
        /** first validation **/
        if(!trim($username)) return true;
        
        return username_exists($username);
    }
    
    /**
     * Check if email exists or not
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $email
     * @return boolean
     */
    public static function email_exists($email)
    {
        /** first validation **/
        if(!trim($email)) return true;
        
        return email_exists($email);
    }
    
    /**
     * Wrapper function for WordPress wp_insert_user
     * @author Howard R <Howard@realtyna.com>
     * @static
     * @param array $userdata
     * @return mixed
     */
    public static function wp_insert_user($userdata = array())
    {
        return wp_insert_user($userdata);
    }
    
    /**
     * Wrapper function for WordPress wp_set_password function
     * @author Howard R <Howard@realtyna.com>
     * @static
     * @param string $password
     * @param int $user_id
     */
    public static function wp_set_password($password, $user_id)
    {
        wp_set_password($password, $user_id);
    }
    
    /**
     * Check user/blog access to WPL menus
     * @author Howard R <Howard@realtyna.com>
     * @static
     * @param string $menu_slug
     * @param int $user_id
     * @return boolean
     */
    public static function has_menu_access($menu_slug, $user_id = NULL)
    {
        if(wpl_global::is_multisite() and !wpl_users::is_super_admin($user_id))
        {
            $current_blog_id = wpl_global::get_current_blog_id();
            
            // Franchise Object
            $fs = new wpl_addon_franchise();
            $fs_settings = $fs->fs_settings($current_blog_id);
            
            if(isset($fs_settings['menus']) and isset($fs_settings['menus'][$menu_slug])) return (boolean) $fs_settings['menus'][$menu_slug];
        }
        
        return true;
    }

    /**
     * Return WP admins
     * @author Steve A. <steve@realtyna.com>
     * @static
     * @param string   $fields  Fields to return	
     * @return array 			WP Admins
     */
    public static function get_wp_admins($fields = 'id')
    {
    	return get_users(array('role'=>'Administrator', 'fields'=>$fields));
    }

    /**
     * Wrapper for WordPress wp_logout_url function
     * @author Steve A. <steve@realtyna.com>
     * @static
     */
    public static function wp_logout_url()
    {
        return wp_logout_url();
    }
    
    /**
     * Runs after user login
     * @author Howard R. <howard@realtyna.com>
     * @param string $username
     * @param object $user
     */
    public function user_loggedin($username, $user = NULL)
    {
        /** trigger event **/
        $id = self::get_id_by_username($username);
		wpl_global::event_handler('user_loggedin', array('username'=>$username, 'user'=>$user, 'user_id'=>$id));
    }
    
    /**
     * Returns user type tpl for users view such as profile listing and profile show
     * @author Howard R. <howard@realtyna.com>
     * @param string $wplpath
     * @param string $tpl
     * @param int $user_type
     * @return string
     */
    public static function get_user_type_tpl($wplpath, $tpl = NULL, $user_type = NULL)
	{
        if(!trim($tpl)) $tpl = 'default';
        if(is_null($user_type)) return $tpl;
        
        /** Create User Type tpl such as default_ut1.php etc. **/
        $user_type_tpl = $tpl.'_ut'.$user_type;
        
        $wplpath = rtrim($wplpath, '.').'.'.$user_type_tpl;
        $path = _wpl_import($wplpath, true, true);
        
        if(wpl_file::exists($path)) return $user_type_tpl;
        else return $tpl;
	}
    
    /**
     * Returns current blog main admin (Owner) id by caring about multisite feature
     * @author Howard R. <howard@realtyna.com>
     * @static
     * @return int
     */
    public static function get_blog_admin_id()
    {
        if(wpl_global::is_multisite()) $email = wpl_global::get_blog_option(wpl_global::get_current_blog_id(), 'admin_email');
        else $email = wpl_global::get_wp_option('admin_email');
        
        return wpl_users::get_id_by_email($email);
    }
    
    /**
     * Wrapper for wp_login_url function
     * @author Howard R. <howard@realtyna.com>
     * @static
     * @param string $redirect
     * @return string
     */
    public static function wp_login_url($redirect = '')
    {
        return wp_login_url($redirect);
    }
    
    /**
     * Wrapper for wp_registration_url function
     * @author Howard R. <howard@realtyna.com>
     * @static
     * @return string
     */
    public static function wp_registration_url()
    {
        return wp_registration_url();
    }
    
    /**
     * Triggers when a user removed from WordPress completely
     * @author Howard R. <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @return boolean
     */
    public static function delete_user($user_id)
    {
        wpl_users::delete_user_from_wpl($user_id);
        return true;
    }
    
    /**
     * Add user to WordPress blog in Multisite installation
     * @author Howard R. <howard@realtyna.com>
     * @static
     * @param int $user_id
     * @param int $blog_id
     * @param string $role
     * @return boolean
     */
    public static function add_user_to_blog($user_id, $blog_id = NULL, $role = 'subscriber')
    {
        // It's not a Multisite Installation
        if(!wpl_global::is_multisite()) return false;
        
        // Get current blog
        if(is_null($blog_id)) $blog_id = wpl_global::get_current_blog_id();
        
        if(!is_user_member_of_blog($user_id, $blog_id))
        {
            add_user_to_blog($blog_id, $user_id, $role);
        }

        return true;
    }
    
    public static function save_default_values($user_id, $values = array())
    {
        if(!is_array($values)) return false;
        
        return wpl_db::q("UPDATE `#__wpl_users` SET `default_values`='".json_encode($values)."' WHERE `id`='$user_id'");
    }
    
    /**
     * Return default values of user (Country, Units etc) It used in APS addon
     * @param int $user_id
     * @return boolean|array
     */
    public static function get_default_values($user_id)
    {
        if(!wpl_global::check_addon('aps')) return false;
        
        $JSON = wpl_db::get('default_values', 'wpl_users', 'id', $user_id);
        return trim($JSON) ? json_decode($JSON, true) : array();
    }

    /**
     * Check if a user is broker or not
     * @param integer $user_id
     * @return bool
     */
    public static function is_broker($user_id = NULL)
    {
        // Get Current User
        if(is_null($user_id)) $user_id = wpl_users::get_cur_user_id();

        // Guest User
        if(!$user_id) return false;

        // Brokerage Addon is not installed
        if(!wpl_global::check_addon('brokerage')) return false;

        // It's not a Broker
        if(wpl_users::get_user_key('membership_type', $user_id) != '7') return false;

        // It doesn't have access
        if(!wpl_users::check_access('addon_brokerage', 0, $user_id)) return false;

        return true;
    }

    /**
     * Check if a user is broker agent
     * @param integer $user_id
     * @return bool
     */
    public static function is_broker_agent($user_id = NULL)
    {
        // Get Current User
        if(is_null($user_id)) $user_id = wpl_users::get_cur_user_id();

        // Guest User
        if(!$user_id) return false;

        // Brokerage Addon is not installed
        if(!wpl_global::check_addon('brokerage')) return false;

        // It's a Broker Agent
        if(wpl_users::get_user_key('parent', $user_id)) return true;

        return false;
    }

    /**
     * Check if user is part of a brokerage
     * @param null $user_id
     * @return bool
     */
    public static function is_part_of_brokerage($user_id = NULL)
    {
        return (wpl_users::is_broker($user_id) or wpl_users::is_broker_agent($user_id));
    }

    /**
     * Get Broker User
     * @param null $user_id
     * @return bool|object
     */
    public static function get_broker($user_id = NULL)
    {
        // Get Current User
        if(is_null($user_id)) $user_id = wpl_users::get_cur_user_id();

        // Get Broker ID
        if(wpl_users::is_broker($user_id)) $broker_id = $user_id;
        else $broker_id = wpl_users::get_user_key('parent', $user_id);

        // No Broker ID
        if(!$broker_id) return false;

        return wpl_users::get_wpl_user($broker_id);
    }

    /**
     * Get a certain key from user data
     * @param $key
     * @param integer $user_id
     * @return mixed
     */
    public static function get_user_key($key, $user_id = NULL)
    {
        // Get Current User
        if(is_null($user_id)) $user_id = wpl_users::get_cur_user_id();

        return wpl_db::get($key, 'wpl_users', 'id', $user_id);
    }
}