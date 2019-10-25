<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.settings');
_wpl_import('libraries.flex');

class wpl_settings_controller extends wpl_controller
{
	public $tpl_path = 'views.backend.settings.tmpl';
	public $tpl;
	
	public function display()
	{
		/** check permission **/
		wpl_global::min_access('administrator');
		
		$function = wpl_request::getVar('wpl_function');
		
        // Check Nonce
        if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_settings')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
        // Create Nonce
        $this->nonce = wpl_security::create_nonce('wpl_settings');
        
		if($function == 'save')
		{
			$setting_name = wpl_request::getVar('setting_name');
			$setting_value = wpl_request::getVar('setting_value');
			$setting_category = wpl_request::getVar('setting_category');
			
			$this->save($setting_name, $setting_value, $setting_category);
		}
		elseif($function == 'save_watermark_image') $this->save_watermark_image();
        elseif($function == 'save_languages') $this->save_languages();
        elseif($function == 'save_advanced_markers') $this->save_advanced_markers();
        elseif($function == 'generate_language_keywords') $this->generate_language_keywords();
        elseif($function == 'save_customizer') $this->save_customizer();
		elseif($function == 'clear_cache') $this->clear_cache();
        elseif($function == 'remove_upload') $this->remove_upload();
        elseif($function == 'clear_calendar_data') $this->clear_calendar_data();
        elseif($function == 'import_settings') $this->import_settings();
        elseif($function == 'export_settings') $this->export_settings();
        elseif($function == 'uploader') $this->uploader();
        elseif($function == 'save_seo_patterns') $this->save_seo_patterns();
        elseif($function == 'add_sample_properties') $this->add_sample_properties();
        elseif($function == 'update_ranks') $this->update_ranks();
        elseif($function == 'get_mem_dpr_field_options') $this->get_mem_dpr_field_options();
        elseif($function == 'save_mem_dpr_criterias') $this->save_mem_dpr_criterias();
        elseif($function == 'toggle_cronjobs') $this->toggle_cronjobs();
        elseif($function == 'get_field_options') $this->get_field_options();
	}
	
	private function save($setting_name, $setting_value, $setting_category)
	{
		$res = wpl_settings::save_setting($setting_name, $setting_value, $setting_category);
		
		$res = (int) $res;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
    
	/**
	 * added by Francis
	 * @param array $file: the array which come from $_FILE
	 * description       : save watermark image to the specific path and
	 *                     save filename as a setting value to database
	 */
	private function save_watermark_image()
	{
        $file = wpl_request::getVar('wpl_watermark_uploader', NULL, 'FILES');
		$filename = wpl_global::normalize_string($file['name']);
		$ext_array = array('jpg','png','gif','jpeg');
        
		$error = '';
		$message = '';
        
		if(!empty($file['error']) or (empty($file['tmp_name']) or ($file['tmp_name'] == 'none')))
		{
			$error = __('An error ocurred uploading your file.', 'real-estate-listing-realtyna-wpl');
		}
		else 
		{
			// check the extention
			$extention = strtolower(wpl_file::getExt($file['name']));

			if(!in_array($extention, $ext_array)) $error = __('File extension should be .jpg, .png or .gif.', 'real-estate-listing-realtyna-wpl');
			if($error == '')
			{
				$dest = WPL_ABSPATH. 'assets' .DS. 'img' .DS. 'system' .DS. $filename;
                
				wpl_file::upload($file['tmp_name'], $dest);
				wpl_settings::save_setting('watermark_url', $filename, 2);
			}
		}

		$response = array('error'=>$error, 'message'=>$message);

		echo json_encode($response);
		exit;
	}
	
	private function clear_cache()
	{
		$caches = wpl_request::getVar('cache', NULL);
		foreach($caches as $cache_type=>$value) wpl_settings::clear_cache($cache_type);
		
		$res = 1;
		$message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
	}
    
    private function remove_upload()
    {
        $setting_name = wpl_request::getVar('setting_name', '');
        $settings_value = wpl_settings::get($setting_name);
        $upload_src = wpl_global::get_wpl_asset_url('img/system/'.$settings_value);
        
        wpl_settings::save_setting($setting_name, NULL);
        wpl_file::delete($upload_src);
        
        /** Remove Thumbnails **/
        wpl_settings::clear_cache('listings_thumbnails');
        wpl_settings::clear_cache('users_thumbnails');
        
        $response = array('success'=>1, 'message'=>__('Uploaded file removed successfully!', 'real-estate-listing-realtyna-wpl'));
		
		echo json_encode($response);
		exit;
    }
    
    private function save_languages()
    {
        $raws = wpl_request::getVar('wpllangs', array());
        
        $langs = array();
        $lang_options = array();
        
        foreach($raws as $key=>$raw)
        {
            if(!trim($raw['full_code'])) continue;
            
            $langs[$key] = $raw['full_code'];
            $lang_options[$key] = $raw;
        }
        
        wpl_settings::save_setting('lang_options', json_encode($lang_options));
        wpl_addon_pro::save_languages($langs);
		
		$res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
    }
    
    private function save_customizer()
    {
        $wplcustomizer = wpl_request::getVar('wplcustomizer', array());
        
        $_variables = wpl_file::read(WPL_ABSPATH.'assets'.DS.'scss'.DS.'ui_customizer'.DS.'_variables_source.scss');
        foreach($wplcustomizer as $key=>$value) $_variables = str_replace('['.$key.']', $value, $_variables);
        
        /** Write on _variables.scss file **/
        wpl_file::write(WPL_ABSPATH.'assets'.DS.'scss'.DS.'ui_customizer'.DS.'_variables.scss', $_variables);
        
        /** Initialize SCSS Compiler **/
        _wpl_import('libraries.scss');
        
        $wplscss = new wpl_scss();
        $wplscss->set_import_path(WPL_ABSPATH.'assets'.DS.'scss'.DS.'ui_customizer'.DS);
        
        /** Compile **/
        $css_path = WPL_ABSPATH.'assets'.DS.'css'.DS.'ui_customizer'.DS.'wpl.css';
        
        // Make WPL UI Customizer multisite support
        $current_blog_id = wpl_global::get_current_blog_id();
        if($current_blog_id and $current_blog_id != 1) $css_path = WPL_ABSPATH.'assets'.DS.'css'.DS.'ui_customizer'.DS.'wpl'.$current_blog_id.'.css';
        
        $wplscss->compile_file(WPL_ABSPATH.'assets'.DS.'scss'.DS.'ui_customizer'.DS.'wpl.scss', $css_path);
        
        /** Save UI Customizer Options in Database **/
        wpl_settings::save_setting('wpl_ui_customizer', json_encode($wplcustomizer));
        
		$res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
    }
    
    private function generate_language_keywords()
    {
        wpl_addon_pro::generate_dynamic_keywords();
		
		$res = 1;
		$message = $res ? __('Language strings are generated.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
    }
    
    private function clear_calendar_data()
    {
        _wpl_import('libraries.addon_calendar');
        
        $res = wpl_addon_calendar::clear_calendar_data();
        $message = $res ? __('Calendar Data removed.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
        $data = NULL;
        
        $response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		echo json_encode($response);
		exit;
    }

    private function import_settings()
    {
		$file = wpl_request::getVar('wpl_import_file', '', 'FILES');
		$tmp_directory = wpl_global::init_tmp_folder();
		$ext = strtolower(wpl_file::getExt($file['name']));
		$settings_file = $tmp_directory.'settings.'.$ext;
		
		$response = wpl_global::upload($file, $settings_file, array('json', 'xml'), 20971520); #20MB
		if(trim($response['error']) != '')
		{
			echo json_encode($response);
			exit;
		}
		
		if(wpl_settings::import_settings($settings_file))
		{
			$error = '';
        	$message = __('Settings have been imported successfuly!', 'real-estate-listing-realtyna-wpl');
		}
        else
        {
        	$error = '1';
        	$message = __('Cannot import settings!', 'real-estate-listing-realtyna-wpl');
        }

		echo json_encode(array('error'=>$error, 'message'=>$message));
		exit;
    }

    private function export_settings()
    {
    	$format = wpl_request::getVar('wpl_export_format', 'json');
    	$output = wpl_settings::export_settings($format);

    	if($format == 'json')
    	{
    		header('Content-disposition: attachment; filename=settings.json');
			header('Content-type: application/json');	
    	}
    	elseif($format == 'xml')
    	{
    		header('Content-disposition: attachment; filename=settings.xml');
			header('Content-type: application/xml');
    	}

    	echo $output;
		exit;
    }
    
    private function uploader()
	{
        $settings_key = wpl_request::getVar('settings_key', '');
        $file = wpl_request::getVar($settings_key, NULL, 'FILES');
        
		$filename = wpl_global::normalize_string($file['name']);
		$ext_array = array('jpg','png','gif','jpeg');
        
		$error = '';
		$message = '';
        
		if(!empty($file['error']) or (empty($file['tmp_name']) or ($file['tmp_name'] == 'none')))
		{
			$error = __('An error ocurred uploading your file.', 'real-estate-listing-realtyna-wpl');
		}
		else 
		{
			// check the extention
			$extention = strtolower(wpl_file::getExt($file['name']));

			if(!in_array($extention, $ext_array)) $error = __('File extension should be .jpg, .png or .gif.', 'real-estate-listing-realtyna-wpl');
			if($error == '')
			{
				$dest = WPL_ABSPATH. 'assets' .DS. 'img' .DS. 'system' .DS. $filename;
                
				wpl_file::upload($file['tmp_name'], $dest);
				wpl_settings::save_setting($settings_key, $filename);
			}
		}
        
		$response = array('error'=>$error, 'message'=>$message);
        
		echo json_encode($response);
		exit;
	}
    
    private function save_seo_patterns()
    {
        $seo_patterns = wpl_request::getVar('seo_patterns', array());
        
        wpl_settings::save_setting('seo_patterns', json_encode($seo_patterns));
		
		$res = 1;
		$message = $res ? __('Saved.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		
		echo json_encode($response);
		exit;
    }
    
    private function add_sample_properties()
    {
    	$fields = wpl_flex::get_fields('', 0, 0, '', '', "AND `enabled` >= 1 AND `kind` = 0 
										AND ((`id` IN (2,3,6,7,8,9,10,11,12,13,14,17)) 
										OR (`category` = '4' AND `type` = 'feature') 
										OR (`category` = '5' AND `type` = 'feature') 
										OR (`category` = '6' AND `type` = 'neighborhood') 
										OR (`category` = '11' AND `type` = 'tag'))");

    	$post = array('command' => 'wpl_sample', 'format' => 'json');
		$data = json_decode(wpl_global::get_web_page('http://billing.realtyna.com/io/io.php', $post));
		$states = wpl_locations::get_locations(2, 254, '');

    	for ($i = 0; $i < 6; $i++) 
    	{ 
    		$query = '';
	        $pid = wpl_property::create_property_default();

			foreach ($fields as $field) 
			{
				if($field->type == 'listings')
				{
					$types = wpl_global::get_listings();
					$pos = array_rand($types);
					$value = $types[$pos]['id'];
				}
				elseif($field->type == 'property_types')
				{
					$types = wpl_global::get_property_types();
					$pos = array_rand($types);
					$value = $types[$pos]['id'];
				}
				elseif($field->type == 'price')
				{
					$value = rand(100, 999) * 1000;
				}
				elseif($field->type == 'select')
				{
					$params = wpl_flex::get_field_options($field->id);
					$params = array_keys($params['params']);
					$value = array_rand($params);
				}
				elseif($field->type == 'number')
				{
					$value = ($field->id == 12) ? rand(1950, 2015) : rand(1, 9);
				}
				elseif($field->type == 'area')
				{
					$value = rand(200, 999);
				}
				elseif($field->type == 'feature' or $field->type == 'tag')
				{
					$value = rand(0, 1);
				}
				elseif($field->type == 'neighborhood')
				{
					$value = rand(0, 1);

					if($value == 1)
					{
						$dist = rand(5, 90);
						$dist_by = rand(1, 3);
						$query .= "`{$field->table_column}_distance` = '{$dist}', `{$field->table_column}_distance_by` = '{$dist_by}', ";
					}
				}

				$query .= "`{$field->table_column}` = '{$value}', ";
			}

			$state = array_rand($states);
			$state_id = $states[$state]->id;
			$state_name = $states[$state]->name;
			$county = $data->counties[array_rand($data->counties)];
			$city = $data->cities[array_rand($data->cities)];
			$street = $data->streets[array_rand($data->streets)];
			$street_no = rand(500, 3000);
			$zipcode = rand(10000, 90000);
			$query .= "`street` = '{$street}', `street_no` = '{$street_no}', `post_code` = '{$zipcode}', ";
			$query .= "`location2_id` = '{$state_id}', `location2_name` = '{$state_name}', `location3_name` = '{$county}', `location4_name` = '{$city}'";

			wpl_db::q("UPDATE `#__wpl_properties` SET $query WHERE `id` = '$pid'");
			wpl_property::finalize($pid);

			$image = $data->images[array_rand($data->images)];
			$image_data = wpl_global::get_web_page($image);
			$image_file = wpl_global::get_upload_base_path().$pid.DS.basename($image);
			wpl_file::write($image_file, $image_data);

			$item = array('parent_id'=>$pid, 'parent_kind'=>0, 'item_type'=>'gallery', 'item_cat'=>'image', 'item_name'=>basename($image), 'creation_date'=>date("Y-m-d H:i:s"), 'index'=>0);
			wpl_items::save($item);
			wpl_property::update_numbs($pid);
    	}

		$this->response(array('success'=>1, 'message'=>__('Sample properties added.', 'real-estate-listing-realtyna-wpl'), 'data'=>NULL));
    }
    
    private function update_ranks()
    {
        _wpl_import('libraries.addon_rank');
        
        $limit = wpl_request::getVar('limit', '100');
        $offset = wpl_request::getVar('offset', 0);
        
        $query = "SELECT `id`, `kind` FROM `#__wpl_properties` WHERE `finalized`='1' ORDER BY `id` ASC LIMIT ".$offset.", ".$limit;
		$listings = wpl_db::select($query, 'loadAssocList');
        
        $rank = new wpl_addon_rank();
        foreach($listings as $listing) $rank->update_rank($listing['id'], $listing['kind']);
        
        $remained = (count($listings) < $limit ? 0 : 1);
        $new_offset = $remained ? ($limit+$offset) : $offset+count($listings);
        
        $this->response(array('success'=>1, 'offset'=>$new_offset, 'remained'=>$remained));
    }
    
    private function get_mem_dpr_field_options()
    {
        $id = wpl_request::getVar('id', 0);
        $field = (array) wpl_flex::get_field($id);
        
        $data = array();
        $data['field'] = $field;
        $data['options'] = array();
        
        $options = json_decode($field['options'], true);
                
        $params = array();
        foreach($options['params'] as $param) $params[] = array('key'=>$param['key'], 'name'=>__($param['value'], 'real-estate-listing-realtyna-wpl'));

        $data['options'] = $params;
        
        echo json_encode(array('success'=>1, 'data'=>$data));
        exit;
    }
    
    private function save_mem_dpr_criterias()
    {
        $criterias = wpl_request::getVar('criteria', array());
        
        $this->save('mem_dpr_criteria', json_encode($criterias), 6);
    }
    
    private function toggle_cronjobs()
	{
		$status = wpl_request::getVar('status', 0);
        $new_status = $status ? 0 : 1;
        
        // Save the new Status
        wpl_settings::save_setting('wpl_cronjobs', $new_status);
        
		$res = 1;
		$message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = array(
            'label'=>($new_status ? '<strong style="color: red;">'.__('Enabled', 'real-estate-listing-realtyna-wpl').'</strong>' : '<strong style="color: green;">'.__('Disabled', 'real-estate-listing-realtyna-wpl').'</strong>'),
            'submit_label'=>($new_status ? __('Disable it', 'real-estate-listing-realtyna-wpl') : __('Enable it', 'real-estate-listing-realtyna-wpl')),
            'new_status'=>$new_status
        );
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		$this->response($response);
	}
    
    private function save_advanced_markers()
    {
        $advanced_markers = wpl_request::getVar('wpl_advanced_markers', array());
        
        // Save the Advanced Markers
        wpl_settings::save_setting('advanced_markers', json_encode($advanced_markers));
        
		$res = 1;
		$message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = array();
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
		$this->response($response);
    }

    private function get_field_options()
    {
        $id = wpl_request::getVar('id', 0);
        $field = (array) wpl_flex::get_field($id);

        $data = array();
        $data['field'] = $field;
        $data['operators'] = array();
        $data['options'] = array();

        if(in_array($field['type'], array('select', 'feature')))
        {
            $data['operators'] = array(array('name'=>__('Include', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_multiple_'.$field['table_column']), array('name'=>__('Exclude', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_notmultiple_'.$field['table_column']));

            $options = json_decode($field['options'], true);
            $params = array();

            if($field['type'] == 'select')
            {
                foreach($options['params'] as $param) $params[] = array('key'=>$param['key'], 'name'=>__($param['value'], 'real-estate-listing-realtyna-wpl'));
            }
            elseif($field['type'] == 'feature')
            {
                foreach($options['values'] as $param) $params[] = array('key'=>$param['key'], 'name'=>__($param['value'], 'real-estate-listing-realtyna-wpl'));

                if(count($params)) $data['operators'] = array(array('name'=>__('Include', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_feature_'.$field['table_column']));
                else $data['operators'] = array(array('name'=>__('Select', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_select_'.$field['table_column']));
            }

            $data['options'] = $params;
        }
        elseif(in_array($field['type'], array('number', 'area', 'price', 'length', 'volume'))) $data['operators'] = array(array('name'=>__('Greater', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_tmin_'.$field['table_column']), array('name'=>__('Smaller', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_tmax_'.$field['table_column']), array('name'=>__('Include', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_multiple_'.$field['table_column']), array('name'=>__('Exclude', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_notmultiple_'.$field['table_column']));
        else $data['operators'] = array(array('name'=>__('Contains', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_text_'.$field['table_column']), array('name'=>__('Include', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_multiple_'.$field['table_column']), array('name'=>__('Exclude', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_notmultiple_'.$field['table_column']), array('name'=>__('Exactly', 'real-estate-listing-realtyna-wpl'), 'key'=>'sf_select_'.$field['table_column']));

        echo json_encode(array('success'=>1, 'data'=>$data));
        exit;
    }
}