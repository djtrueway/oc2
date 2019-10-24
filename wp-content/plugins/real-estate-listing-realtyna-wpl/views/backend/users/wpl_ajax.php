<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

class wpl_users_controller extends wpl_controller
{
	public $tpl_path = 'views.backend.users.tmpl';
	public $tpl;
	
	public function display()
	{
		$function = wpl_request::getVar('wpl_function');
		
        // Check Nonce
        if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_users')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
		if($function == 'add_user_to_wpl')
		{
			/** check permission **/
			wpl_global::min_access('administrator');
		
			$user_id = wpl_request::getVar('user_id');
			$this->add_user_to_wpl($user_id);
		}
		elseif($function == 'del_user_from_wpl')
		{
			/** check permission **/
			wpl_global::min_access('administrator');
			
			$user_id = wpl_request::getVar('user_id');
			$confirmed = wpl_request::getVar('wpl_confirmed', 0);
			
			$this->del_user_from_wpl($user_id, $confirmed);
		}
		elseif($function == 'generate_edit_page')
		{
			/** check permission **/
			wpl_global::min_access('administrator');
			
			$user_id = wpl_request::getVar('user_id');
			$this->generate_edit_page($user_id);
		}
		elseif($function == 'save_user')
		{
			/** check permission **/
			wpl_global::min_access('administrator');
			
			$inputs = wpl_request::get('POST');
			$this->save_user($inputs);
		}
		elseif($function == 'save')
		{
            /** check permission **/
            wpl_global::min_access('agent');
        
			$table_name = wpl_request::getVar('table_name', 'wpl_users');
			$table_column = wpl_request::getVar('table_column');
			$value = wpl_request::getVar('value');
			$item_id = wpl_request::getVar('item_id');
			
			$this->save($table_name, $table_column, $value, $item_id);
		}
		elseif($function == 'change_membership')
		{
            /** check permission **/
            wpl_global::min_access('agent');
        
			$user_id = wpl_request::getVar('id');
			$membership_id = wpl_request::getVar('membership_id');
			
			$this->change_membership($user_id, $membership_id);
		}
		elseif($function == 'location_save')
		{
            /** check permission **/
            wpl_global::min_access('agent');
        
			$table_name = wpl_request::getVar('table_name');
			$table_column = wpl_request::getVar('table_column');
			$value = wpl_request::getVar('value');
			$item_id = wpl_request::getVar('item_id');
			
			$this->location_save($table_name, $table_column, $value, $item_id);
		}
		elseif($function == 'finalize')
		{
            /** check permission **/
            wpl_global::min_access('agent');
            
			$item_id = wpl_request::getVar('item_id');
			$this->finalize($item_id);
		}
		elseif($function == 'upload_file')
		{
            /** check permission **/
            wpl_global::min_access('agent');
            
			$file_name = wpl_request::getVar('file_name');
			$user_id = wpl_request::getVar('item_id');
			
			$this->upload_file($file_name, $user_id);
		}
		elseif($function == 'delete_file')
		{
            /** check permission **/
            wpl_global::min_access('agent');
            
			$field_id = wpl_request::getVar('field_id');
			$user_id = wpl_request::getVar('item_id');
			
			$this->delete_file($field_id, $user_id);
		}
        elseif($function == 'save_multilingual')
        {
            /** check permission **/
            wpl_global::min_access('agent');
            
            $this->save_multilingual();
        }
        elseif($function == 'renew_membership')
        {
            /** check permission **/
            wpl_global::min_access('agent');
            
            $this->renew_membership();
        }
        elseif($function == 'expire_membership')
        {
            /** check permission **/
            wpl_global::min_access('administrator');
            
            $this->expire_membership();
        }
	}
	
	private function add_user_to_wpl($user_id)
	{
		$res = wpl_users::add_user_to_wpl($user_id);
		
		$res = (int) $res;
		$message = $res ? __('User added to WPL successfully.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
	
	private function del_user_from_wpl($user_id, $confirmed = 0)
	{
		if($confirmed) $res = wpl_users::delete_user_from_wpl($user_id);
		else $res = false;
		
		$res = (int) $res;
		$message = $res ? __('User removed from WPL successfully.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
	
	private function generate_edit_page($user_id = '')
	{
		$this->user_info = wpl_users::get_user($user_id);
		$this->fields = wpl_db::columns('wpl_users');
        
        $this->user_data = wpl_users::get_wpl_user($user_id);
        $this->data = $this->user_data;
        
        $this->units = wpl_units::get_units(4);
		$this->listings = wpl_listing_types::get_listing_types();
		$this->property_types = wpl_property_types::get_property_types();
		$this->memberships = wpl_users::get_wpl_memberships();
		$this->membership_types = wpl_users::get_user_types();
		$this->users =wpl_users::get_wpl_users();
		parent::render($this->tpl_path, 'edit');
		exit;
	}
    
    public function generate_tab($tpl = 'internal_setting_advanced')
	{
		if($tpl == 'internal_setting_crm')
		{
			if(!wpl_global::check_addon('crm'))
			{
				echo __('The CRM Add-on must be installed for this feature!', 'real-estate-listing-realtyna-wpl');
				return;	
			}
		}
        elseif(!wpl_global::check_addon('membership')) /** checking PRO addon **/
		{
			echo __('The Membership Add-on must be installed for this feature!', 'real-estate-listing-realtyna-wpl');
			return;
		}
        
		/** include the layout **/
		parent::render($this->tpl_path, $tpl);
	}
	
	private function save_user($inputs)
	{
		$res = $this->save_user_do($inputs);
		
		$res = (int) $res;
		$message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
	
	public function save_user_do($inputs)
	{
		$restricted_fields = array('page', 'wpl_format', 'wpl_function', 'function', 'id');
        
		/** edit user **/
		$query = "";
		$id = $inputs['id'];
		$columns = wpl_db::columns('wpl_users');
        $crm_access = array();

		/** set restriction to none **/
		if(!isset($inputs['maccess_lrestrict'])) $inputs['maccess_listings'] = '';
		if(!isset($inputs['maccess_ptrestrict'])) $inputs['maccess_property_types'] = '';
		
		foreach($inputs as $field=>$value)
		{
			if(substr($field, 0, 11) == 'maccess_crm')
			{
				if($value == 1)	$crm_access[] = substr($field, 11);
				continue;
			}
			
			if(in_array($field, $restricted_fields) or !in_array($field, $columns)) continue;
			
			$query .= "`".$field."`='" .$value. "', ";
		}

		/** update CRM access list if available **/
		if(count($crm_access) > 0)
		{
			$query .= "`maccess_crm` = '" .implode(',', $crm_access). "', ";
		}
        
        // RETS Addon
        if(isset($inputs['rets_prefilter']) and is_array($inputs['rets_prefilter']) and wpl_global::check_addon('rets'))
        {
            $valid_filters = array();
            foreach($inputs['rets_prefilter'] as $column=>$prefilter)
            {
                // Filter is Removed
                if(!isset($prefilter['removed']) or (isset($prefilter['removed']) and $prefilter['removed'])) continue;
                
                $valid_filters[$column] = $prefilter;
            }
            
            $query .= "`maccess_rets_prefilters` = '" .json_encode($valid_filters). "', ";
        }
		
		$query = rtrim($query, ', ');
		$query = "UPDATE `#__wpl_users` SET ".$query." WHERE `id`='".$id."'";
		
		/** update user **/
		wpl_db::q($query);
        
        // Renew the user if period is set to unlimited
        if(isset($inputs['maccess_period']) and $inputs['maccess_period'] == '-1' and wpl_global::check_addon('membership'))
        {
            _wpl_import('libraries.addon_membership');
            
            $membership = new wpl_addon_membership();
            $membership->renew($id);
        }
        
		return true;
	}
	
	private function save($table_name, $table_column, $value, $item_id)
	{
		$field_type = wpl_global::get_db_field_type($table_name, $table_column);
		if($field_type == 'datetime' or $field_type == 'date') $value = wpl_render::derender_date($value);
        else $value = wpl_db::escape($value);
		
		$res = wpl_db::set($table_name, $item_id, $table_column, $value, 'id');
		
		$res = (int) $res;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
    
    private function save_multilingual()
	{
		$dbst_id = wpl_request::getVar('dbst_id');
        $value = wpl_db::escape(wpl_request::getVar('value'));
        $item_id = wpl_request::getVar('item_id');
        $lang = wpl_request::getVar('lang');
        
        $field = wpl_flex::get_field($dbst_id);
        
        $table_name = $field->table_name;
        $table_column1 = wpl_addon_pro::get_column_lang_name($field->table_column, $lang, false);
        $default_language = wpl_addon_pro::get_default_language();
        
        $table_column2 = NULL;
        if(strtolower($default_language) == strtolower($lang)) $table_column2 = wpl_addon_pro::get_column_lang_name($field->table_column, $lang, true);
        
		wpl_db::set($table_name, $item_id, $table_column1, $value, 'id');
        if($table_column2) wpl_db::set($table_name, $item_id, $table_column2, $value, 'id');
        
		$res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
	
	private function change_membership($user_id, $membership_id)
	{
		/** changing membership of the user **/
		wpl_users::change_membership($user_id, $membership_id);
		
		$res = 1;
		$message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
	
	private function location_save($table_name, $table_column, $value, $item_id)
	{
		$location_settings = wpl_global::get_settings('3'); # location settings
		
		$location_level = str_replace('_id', '', $table_column);
		$location_level = substr($location_level, -1);
		
		if($table_column == 'zip_id') $location_level = 'zips';
		
		$location_data = wpl_locations::get_location($value, $location_level);
		$location_name_column = $location_level != 'zips' ? 'location'.$location_level.'_name' : 'zip_name';
		
		/** update property location data **/
		if($location_settings['location_method'] == 2 or ($location_settings['location_method'] == 1 and in_array($location_level, array(1, 2)))) $res = wpl_db::update($table_name, array($table_column=>$value, $location_name_column=>$location_data->name), 'id', $item_id);
		else $res = wpl_db::update($table_name, array($location_name_column=>$value), 'id', $item_id);
		
		$res = (int) $res;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
	
	private function finalize($user_id)
	{
		wpl_users::finalize($user_id);
		
		$res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
	
	private function upload_file($file_name, $user_id)
	{
		$file = wpl_request::getVar($file_name, '', 'FILES');
		$filename = $file['name'];
		$ext_array = array('jpg','png','gif','jpeg');
		$error = "";
		$message = "";

		if(!empty($file['error']) or (empty($file['tmp_name']) or ($file['tmp_name'] == 'none')))
		{
			$error = __('An error ocurred uploading your file.', 'real-estate-listing-realtyna-wpl');
		}
		else 
		{
			// check the extention
			$extention = strtolower(wpl_file::getExt($file['name']));
			
			if(!in_array($extention, $ext_array))
			{
				$error = __('File extension should be .jpg, .png or .gif.', 'real-estate-listing-realtyna-wpl');
			}

			if($error == '')
			{
				if($file_name == 'wpl_c_912') # profile picture
				{
					/** delete previous file **/
					$this->delete_file(912, $user_id, false);
					
					$new_file_name = 'profile.'.$extention;
                    
					/** save into db and add to items **/
					wpl_db::set('wpl_users', $user_id, 'profile_picture', $new_file_name);
				}
				elseif($file_name == 'wpl_c_913') # company logo
				{
					/** delete previous file **/
					$this->delete_file(913, $user_id, false);
					
					$new_file_name = 'logo.'.$extention;
					
					/** save into db and add to items **/
					wpl_db::set('wpl_users', $user_id, 'company_logo', $new_file_name);
				}				
				elseif($file_name == 'wpl_c_4104') # cover image
				{
					/** delete previous file **/
					$this->delete_file(4104, $user_id, false);
					
					$new_file_name = 'cover.'.$extention;
					
					/** save into db and add to items **/
					wpl_db::set('wpl_users', $user_id, 'agent_cover', $new_file_name);
				}
				else $new_file_name = $filename;
				
				$dest = wpl_items::get_path($user_id, 2). $new_file_name;
				wpl_file::upload($file['tmp_name'], $dest);
			}
		}
		
		$res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('error'=>$error, 'message'=>$message);
		echo json_encode($response);
		exit;
	}
	
	private function delete_file($field_id, $user_id, $output = true)
	{
		$field_data = (array) wpl_db::get('*', 'wpl_dbst', 'id', $field_id);
		$user_data = (array) wpl_users::get_wpl_user($user_id);
		$path = wpl_items::get_path($user_id, $field_data['kind']). $user_data[$field_data['table_column']];
		
		/** delete file and reset db **/
		wpl_file::delete($path);
		wpl_db::set('wpl_users', $user_id, $field_data['table_column'], '');
        
        /** delete thumbnails **/
        wpl_users::remove_thumbnails($user_id);
		
		/** called from other functions (upload function) **/
		if(!$output) return;
		
		$res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
    
    private function renew_membership()
	{
        $user_id = wpl_request::getVar('id', 0);
        
        _wpl_import('libraries.addon_membership');
        $membership = new wpl_addon_membership();
        $membership->renew($user_id);
        
        $user_data = wpl_users::get_wpl_data($user_id);
		
		$res = 1;
		$message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = array('expiry_date'=>date('Y-m-d', strtotime($user_data->expiry_date)));
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
    
    private function expire_membership()
	{
        $user_id = wpl_request::getVar('id', 0);

        if($user_id)
        {
			_wpl_import('libraries.addon_membership');
            
	        $membership = new wpl_addon_membership();
	        $res = $membership->expired($user_id);
            
			$message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
			$response = array('success'=>$res, 'message'=>$message, 'data'=>NULL);
        }
        else
        {
        	$response = array('success'=>0, 'message'=>__('Error Occured.', 'real-estate-listing-realtyna-wpl'), 'data'=>NULL);
        }
		
		echo json_encode($response);
		exit;
	}
}