<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.property');
_wpl_import('libraries.pagination');
_wpl_import('libraries.settings');
_wpl_import('libraries.items');
_wpl_import('libraries.images');
_wpl_import('libraries.activities');

class wpl_listings_controller extends wpl_controller
{
    public $tpl_path = 'views.backend.listings.tmpl';
    public $tpl;

    public function manager($instance = array())
    {
		/** check access **/
		if(!wpl_users::check_access('propertymanager'))
		{
			/** import message tpl **/
			$this->message = __("You don't have access to this menu! Maybe your user is not added to WPL as an agent. You can contact to website admin for this.", 'real-estate-listing-realtyna-wpl');
			return parent::render($this->tpl_path, 'message');
		}
		
        // Create Nonce
        $this->nonce = wpl_security::create_nonce('wpl_listings');
        
        $init = $this->init_page();
        if(!$init) return false;
        
		$this->tpl = wpl_flex::get_kind_tpl($this->tpl_path, wpl_request::getVar('tpl', 'manager'), $this->kind);
        parent::render($this->tpl_path, $this->tpl);
    }
    
    /**
     * written by Francis
     * description: initialize pagination and properties for property manager page
     */
    private function init_page()
    {
        /** global settings **/
        $settings = wpl_settings::get_settings();

        /** listing settings **/
		$this->page_number = wpl_request::getVar('wplpage', 1);
        $limit = wpl_request::getVar('limit', $settings['default_page_size']);
        $start = wpl_request::getVar('start', (($this->page_number-1)*$limit));
        $orderby = wpl_request::getVar('orderby', $settings['default_orderby']);
        $order = wpl_request::getVar('order', $settings['default_order']);
		$current_user_id = wpl_users::get_cur_user_id();
        $where = array();
		
		/** set page if start var passed **/
		$this->page_number = ($start/$limit)+1;
		wpl_request::setVar('wplpage', $this->page_number);
		
        $this->model = new wpl_property;
		
        /** detect kind **/
        $this->kind = wpl_request::getVar('kind', 0);
        if(!in_array($this->kind, wpl_flex::get_valid_kinds()))
		{
			/** import message tpl **/
			$this->message = __('Invalid Request!', 'real-estate-listing-realtyna-wpl');
			parent::render($this->tpl_path, 'message');
            
            return false;
		}
        
        // Check Complex Access
        if($this->kind == 1 and !wpl_users::check_access('complex_addon'))
        {
            /** import message tpl **/
			$this->message = __("You don't have access to complexes/condos. You can request access from website admin.", 'real-estate-listing-realtyna-wpl');
			parent::render($this->tpl_path, 'message');
            
            return false;
        }
        
        // Check Neighborhood Access
        if($this->kind == 4 and !wpl_users::check_access('neighborhoods'))
        {
            /** import message tpl **/
			$this->message = __("You don't have access to neighborhoods. You can request access from website admin.", 'real-estate-listing-realtyna-wpl');
			parent::render($this->tpl_path, 'message');
            
            return false;
        }
        
        /** Access **/
        $access = true;

        // Apply Filters
		@extract(wpl_filters::apply('listing_manager_access', array('access'=>$access, 'kind'=>$this->kind, 'user_id'=>$current_user_id)));
        
        if(!$access)
		{
			/** import message tpl **/
			$this->message = __("Sorry, you currently don't have access to this page!", 'real-estate-listing-realtyna-wpl');
			parent::render($this->tpl_path, 'message');
            
            return false;
		}
        
        // Load User Properties
		if(!wpl_users::is_administrator($current_user_id) and !wpl_users::is_broker($current_user_id))
		{
			$where['sf_select_user_id'] = $current_user_id;
		}
		elseif(wpl_users::is_broker($current_user_id))
        {
            _wpl_import('libraries.addon_brokerage');

            $brokerage = new wpl_addon_brokerage();
            $where['sf_multiple_user_id'] = implode(',', $brokerage->get_agent_ids($current_user_id, true));
        }
        
        /** Multisite **/
		if(wpl_global::is_multisite())
		{
            $fs = wpl_sql_parser::getInstance();
            $fs->disable();
            
            $current_blog_id = wpl_global::get_current_blog_id();
			$where['sf_fschild'] = $current_blog_id;
		}
        
        $this->kind_label = wpl_flex::get_kind_label($this->kind);
        $where['sf_select_kind'] = $this->kind;
        
		/** Add search conditions to the where **/
        $vars = array_merge(wpl_request::get('POST'), wpl_request::get('GET'));
		$where = array_merge($vars, $where);
		
        $this->model->start($start, $limit, $orderby, $order, $where, $this->kind);
        
        $query = $this->model->query();
        $properties = $this->model->search($query);
        
        $this->model->finish();
        
        /** set pagination according to the number of items and limit **/
        $this->pagination = wpl_pagination::get_pagination($this->model->total, $limit);
		$plisting_fields = $this->model->get_plisting_fields();
        
        $wpl_properties = array();
		foreach($properties as $property)
		{
			$wpl_properties[$property->id] = $this->model->full_render($property->id, $plisting_fields, $property);
		}
		
        $this->wpl_properties = $wpl_properties;
        $this->client = wpl_global::get_client();
        
        if($this->client)
        {
            $this->backend = true;
            $this->frontend = false;
            
            $this->add_link = wpl_global::add_qs_var('kind', $this->kind, wpl_global::get_wpl_admin_menu('wpl_admin_add_listing'));
        }
        else
        {
            $this->backend = false;
            $this->frontend = true;
            
            $this->add_link = wpl_global::add_qs_var('kind', $this->kind, wpl_global::add_qs_var('wplmethod', 'wizard'));
        }
        
        /** Multisite **/
		if(wpl_global::is_multisite())
		{
            $fs->enable();
		}
        
        return true;
    }
    
    public function generate_search_form()
    {
        $this->property_types = wpl_global::get_property_types();
        $this->listings = wpl_global::get_listings();
        
        $this->users = array();
		if(wpl_db::num("SELECT COUNT(id) FROM `#__wpl_users` WHERE `id`>0") < 500) $this->users = $this->get_users();
        
        parent::render($this->tpl_path, 'internal_search_form');
    }
    
    protected function include_tabs()
    {
        $this->kinds = wpl_flex::get_kinds();
        
        /** include the layout **/
		parent::render($this->tpl_path, 'internal_tabs');
    }

    public function get_users()
    {
        $current_user_id = wpl_users::get_cur_user_id();

        if(wpl_users::is_part_of_brokerage($current_user_id))
        {
            $broker = wpl_users::get_broker($current_user_id);
            return wpl_users::get_wpl_users("AND (wpl.`parent`='".$broker->id."' OR wpl.`id`='".$broker->id."')");
        }
        else return wpl_users::get_wpl_users();
    }
}