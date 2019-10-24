<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.pagination');
_wpl_import('libraries.sort_options');

class wpl_data_structure_controller extends wpl_controller
{
	public $tpl_path = 'views.backend.data_structure.tmpl';
	public $tpl;
	
	public function display()
	{
		/** check permission **/
		wpl_global::min_access('administrator');
		
		$function = wpl_request::getVar('wpl_function');
		
        // Check Nonce
        if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_data_structure')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
        // Create Nonce
        $this->nonce = wpl_security::create_nonce('wpl_data_structure');
        
		if($function == 'sort_options')
		{
			$sort_ids = wpl_request::getVar('sort_ids');
			$this->sort_options($sort_ids);
		}
		elseif($function == 'sort_options_enabled_state_change')
		{
			$id = wpl_request::getVar('id');
			$status = wpl_request::getVar('enabled_status');
            $key = wpl_request::getVar('key', 'enabled');
            
			$this->update('wpl_sort_options', $id, $key, $status);
		}
        elseif($function == 'save_sort_option')
        {
            $id = wpl_request::getVar('id');
			$key = wpl_request::getVar('key', '');
			$value = wpl_request::getVar('value', '');
            
            $this->update('wpl_sort_options', $id, $key, $value);
        }
	}
	
	/**
	*{tablename,id,key,value of key}
	* this function call update function in units library and change value of a field
	**/
	private function update($table = 'wpl_sort_options', $id, $key, $value = '')
	{
		$res = wpl_sort_options::update($table, $id, $key, sanitize_text_field($value));
		
		$res = (int) $res;
		$message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
		$data = NULL;
		
		$response = array('success'=>$res, 'message'=>$message, 'data'=>$data);		
		echo json_encode($response);
		exit;
	}
	
	private function sort_options($sort_ids)
	{
		if(trim($sort_ids) == '') $sort_ids = wpl_request::getVar('sort_ids');
		wpl_sort_options::sort_options($sort_ids);		
		exit;
	}
}