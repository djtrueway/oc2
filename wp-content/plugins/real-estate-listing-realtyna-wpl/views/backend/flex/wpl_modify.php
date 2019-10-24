<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.flex');

class wpl_flex_controller extends wpl_controller
{
	public $tpl_path = 'views.backend.flex.tmpl';
	public $tpl;
	
	public function display()
	{
		/** check permission **/
		wpl_global::min_access('administrator');
		
		$function = wpl_request::getVar('wpl_function');
		
        // Check Nonce
        if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_flex')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
		if($function == 'generate_modify_page')
		{
			$field_id = wpl_request::getVar('field_id', 0);
			$field_type = wpl_request::getVar('field_type', 'text');
			$kind = wpl_request::getVar('kind', 0);
			$cat_id = wpl_request::getVar('cat_id', 0);
			
			self::generate_modify_page($field_type, $field_id, $kind, $cat_id);
		}
	}
	
	private function generate_modify_page($field_type, $field_id, $kind = 0, $cat_id = 0)
	{
		if(trim($field_type) == '') $field_type = wpl_request::getVar('field_type', 0);
		if(trim($field_id) == '') $field_id = wpl_request::getVar('field_id', 0);
		
		$this->field_type = $field_type;
		$this->field_id = $field_id;
		$this->kind = $kind;
		$this->cat_id = $cat_id;
		
		parent::render($this->tpl_path, 'internal_modify');
		exit;
	}
}