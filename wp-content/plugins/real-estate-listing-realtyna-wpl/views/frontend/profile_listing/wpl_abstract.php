<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.pagination');
_wpl_import('libraries.render');
_wpl_import('libraries.items');
_wpl_import('libraries.activities');
_wpl_import('libraries.settings');
_wpl_import('libraries.sort_options');

abstract class wpl_profile_listing_controller_abstract extends wpl_controller
{
	public $tpl_path = 'views.frontend.profile_listing.tmpl';
	public $tpl;
	public $wpl_profiles;
	public $model;
    public $settings;
    public $page_number;
    public $limit;
    public $start;
    public $orderby;
    public $order;
    public $property_css_class;
    public $property_css_class_switcher;
    public $property_listview;
    public $switcher_type;
    public $profile_columns;
    public $listing_picture_mouseover;
    public $wpl_listing_sort_type;
    public $user_type;
    public $kind;
    public $message;
    public $wplpagination;
    public $pagination;
    public $total_pages;
    public $wplraw;

    public $microdata;
    public $itemscope;
    public $itemprop_name;
    public $itemprop_value;
    public $itemprop_url;
    public $itemprop_telephone;
    public $itemprop_faxNumber;
    public $itemprop_image;
    public $itemprop_address;
    public $itemprop_description;
    public $itemprop_additionalProperty;
    public $itemprop_addressLocality;
    public $itemtype_PropertyValue;
    public $itemtype_RealEstateAgent;

	public function display($instance = array())
	{
		/** profile listing model **/
		$this->model = new wpl_users;
		
		/** global settings **/
		$this->settings = wpl_settings::get_settings();
		
		/** listing settings **/
		$this->page_number = wpl_request::getVar('wplpage', 1, '', true);
		$this->limit = wpl_request::getVar('limit', $this->settings['default_profile_page_size'], '', true);
		$this->start = wpl_request::getVar('start', (($this->page_number-1)*$this->limit), '', true);
		$this->orderby = wpl_request::getVar('wplorderby', $this->settings['default_profile_orderby'], '', true);
		$this->order = wpl_request::getVar('wplorder', $this->settings['default_profile_order'], '', true);
		
        /** Set Property CSS class **/
        $this->property_css_class = wpl_request::getVar('wplpcc', NULL);
        if(!$this->property_css_class) $this->property_css_class = wpl_request::getVar('wplpcc', 'grid_box', 'COOKIE');
        
        $this->property_css_class_switcher = wpl_request::getVar('wplpcc_switcher', '1');
        $this->property_listview = wpl_request::getVar('wplplv', '1'); #Show listview or not

		// only icon or icon+text
		$this->switcher_type = isset($this->settings['wpl_listing_switcher_type']) ? $this->settings['wpl_listing_switcher_type'] : 'icon';

		// Disable or Enable Mouseover effect
		$this->listing_picture_mouseover = isset($this->settings['wpl_listing_picture_mouseover']) ? $this->settings['wpl_listing_picture_mouseover'] : 1;

		/**Sort Option Type**/
		$this->wpl_listing_sort_type = isset($this->settings['wpl_listing_sort_type']) ? $this->settings['wpl_listing_sort_type'] : 'list';
        
		/** set page if start var passed **/
		$this->page_number = ($this->start/$this->limit)+1;
		wpl_request::setVar('wplpage', $this->page_number);
		
        /** User Type **/
        $this->user_type = wpl_request::getVar('sf_select_membership_type', NULL);
        
		/** detect kind **/
		$this->kind = wpl_request::getVar('kind', 2);
		if(!in_array($this->kind, wpl_flex::get_valid_kinds()))
		{
			/** import message tpl **/
			$this->message = __('Invalid Request!', 'real-estate-listing-realtyna-wpl');
			return parent::render($this->tpl_path, 'message', false, true);
		}
		
        /** pagination types **/
        $this->wplpagination = wpl_request::getVar('wplpagination', 'normal', '', true);
        wpl_request::setVar('wplpagination', $this->wplpagination);
        
		$where = array('sf_tmin_id'=>1, 'sf_select_access_public_profile'=>1, 'sf_select_expired'=>0);
		
		/** Add search conditions to the where **/
        $vars = array_merge(wpl_request::get('POST'), wpl_request::get('GET'));
		$where = array_merge($vars, $where);
		
		/** start search **/
		$this->model->start($this->start, $this->limit, $this->orderby, $this->order, $where);
		
		/** run the search **/
		$this->model->query();
		$profiles = $this->model->search();
        
        /** finish search **/
		$this->model->finish();
        
        /** validation for page_number **/
		$this->total_pages = ceil($this->model->total / $this->limit);
		if($this->page_number <= 0 or ($this->page_number > $this->total_pages)) $this->model->start = 0;

		/** Microdata **/
		$this->microdata = isset($this->settings['microdata']) ? $this->settings['microdata'] : 0;
		$this->itemscope = ($this->microdata) ? 'itemscope' : '';

		$this->itemprop_name = ($this->microdata) ? 'itemprop="name"' : '';
		$this->itemprop_value = ($this->microdata) ? 'itemprop="value"' : '';
		$this->itemprop_url = ($this->microdata) ? 'itemprop="url"' : '';
		$this->itemprop_telephone = ($this->microdata) ? 'itemprop="telephone"' : '';
		$this->itemprop_faxNumber = ($this->microdata) ? 'itemprop="faxNumber"' : '';
		$this->itemprop_image = ($this->microdata) ? 'itemprop="image"' : '';
		$this->itemprop_address = ($this->microdata) ? 'itemprop="address"' : '';
		$this->itemprop_description = ($this->microdata) ? 'itemprop="description"' : '';
		$this->itemprop_additionalProperty = ($this->microdata) ? 'itemprop="additionalProperty"' : '';
		$this->itemprop_addressLocality = ($this->microdata) ? 'itemprop="addressLocality"' : '';

		$this->itemtype_PropertyValue = ($this->microdata) ? 'itemtype="http://schema.org/PropertyValue"' : '';
		$this->itemtype_RealEstateAgent = ($this->microdata) ? 'itemtype="http://schema.org/RealEstateAgent"' : '';
		
		/** Profile Listing Columns Count **/
		$profile_columns = wpl_global::get_setting('wpl_ui_customizer_profile_listing_columns');
		$profile_columns_default = trim($profile_columns) ? $profile_columns : '3';
		$this->profile_columns = wpl_request::getVar('wplcolumns', $profile_columns_default); 
        
		$plisting_fields = $this->model->get_plisting_fields();
		
		$wpl_profiles = array();
		foreach($profiles as $profile)
		{
            // User is not exists in WordPress
            if(!wpl_users::is_wp_user($profile->id)) continue;
            
			$wpl_profiles[$profile->id] = $this->model->full_render($profile->id, $plisting_fields);
		}
		
		/** define current index **/
		$wpl_profiles['current'] = array();

        // Apply Filters
		@extract(wpl_filters::apply('profile_listing_after_render', array('wpl_profiles'=>$wpl_profiles)));
		
		$this->pagination = wpl_pagination::get_pagination($this->model->total, $this->limit, true, $this->wplraw);
		$this->wpl_profiles = $wpl_profiles;
		
        /** import tpl **/
        $this->tpl = wpl_users::get_user_type_tpl($this->tpl_path, $this->tpl, $this->user_type);
        
		/** import tpl **/
		return parent::render($this->tpl_path, $this->tpl, false, true);
	}
}