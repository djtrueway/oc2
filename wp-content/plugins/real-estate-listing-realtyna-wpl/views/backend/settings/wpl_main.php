<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.settings');

class wpl_settings_controller extends wpl_controller
{
	public $tpl_path = 'views.backend.settings.tmpl';
	public $tpl;
	
	public function home()
	{
		/** check permission **/
		wpl_global::min_access('administrator');

		/** colorpicker **/
		wp_enqueue_style('wp-color-picker');
		wp_enqueue_script('wp-color-picker');
		
        // Create Nonce
        $this->nonce = wpl_security::create_nonce('wpl_settings');
        
		$this->setting_categories = wpl_settings::get_categories();
		
		/** import tpl **/
		parent::render($this->tpl_path, $this->tpl);

	}
	
	protected function generate_slide($category)
	{
		$tpl = 'internal_slide';
		
		$this->settings = wpl_settings::get_settings($category->id, 1, true);
		$this->setting_category = $category;
		
		/** import tpl **/
		parent::render($this->tpl_path, $tpl);
	}
	
	protected function generate_internal($layout_name)
	{
		$tpl = 'internal_'.$layout_name;
		
		/** import tpl **/
		parent::render($this->tpl_path, $tpl);
	}
}