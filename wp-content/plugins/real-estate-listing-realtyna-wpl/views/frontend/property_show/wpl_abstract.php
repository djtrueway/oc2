<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.property');
_wpl_import('libraries.render');
_wpl_import('libraries.items');
_wpl_import('libraries.activities');

abstract class wpl_property_show_controller_abstract extends wpl_controller
{
	public $tpl_path = 'views.frontend.property_show.tmpl';
	public $tpl;
	public $wpl_properties;
	public $pid;
	public $kind;
	public $property;
	public $model;
	public $pshow_fields;
	public $settings;
	public $message;
	public $pshow_categories;
	public $location_visibility;
	public $fields_columns;
	public $wplraw;
    public $error_code;

    public $microdata;
    public $itemscope;
    public $itemprop_name;
    public $itemprop_value;
    public $itemprop_floorSize;
    public $itemprop_price;
    public $itemprop_url;
    public $itemprop_address;
    public $itemprop_description;
    public $itemprop_additionalProperty;
    public $itemprop_addressLocality;
    public $itemprop_numberOfRooms;
    public $itemtype_PropertyValue;
    public $itemtype_Apartment;
    public $itemtype_ApartmentComplex;
    public $itemtype_SingleFamilyResidence;
    public $itemtype_Place;
    public $itemtype_PostalAddress;
    public $itemtype_offer;
    public $itemtype_QuantitativeValue;
    public $show_agent_name;
    public $show_office_name;
    public $label_agent_name;
    public $label_office_name;
    public $show_signature;
    public $affiliate_id; 
	
	public function display($instance = array())
	{
        // Global Settings
		$this->settings = wpl_settings::get_settings();
        
		// Do Cronjobs
        if(isset($this->settings['wpl_cronjobs']) and $this->settings['wpl_cronjobs'])
        {
            wpl_events::do_cronjobs();
        }
		
		// Check Access
		if(!wpl_users::check_access('propertyshow'))
		{
            // Import Message tpl
            if(wpl_users::is_administrator()) $this->message = sprintf(__("You don't have access to this menu! %s KB article might be helpful.", 'real-estate-listing-realtyna-wpl'), '<a href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/618/">'.__('this', 'real-estate-listing-realtyna-wpl').'</a>');
            else
            {
                $this->message = __("You don't have access to see properties! Please login or register first.", 'real-estate-listing-realtyna-wpl');
                $this->error_code = 401;
            }

			return parent::render($this->tpl_path, 'message', false, true);
		}

        // MLS Maximum Daily Visits Reached
        if(wpl_global::check_addon('mls') and wpl_request::getVar('mls_mdv_reached', 0))
        {
            $this->message = __("You reached the maximum daily visit Today. You can browse listings Tomorrow again!", 'real-estate-listing-realtyna-wpl');
            return parent::render($this->tpl_path, 'message', false, true);
        }

        // property listing model
        $this->model = new wpl_property();
        $this->pid = wpl_request::getVar('pid', 0);

        $listing_id = wpl_request::getVar('mls_id', 0);
        if(trim($listing_id))
        {
            $this->pid = wpl_property::pid($listing_id);
            wpl_request::setVar('pid', $this->pid);
        }

        $property = $this->model->get_property_raw_data($this->pid);

        // Property show layout
        if($property['kind'] == 1) $tpl = wpl_global::get_setting('wpl_complex_propertyshow_layout');
        elseif ($property['kind'] == 4) $tpl = wpl_global::get_setting('wpl_neighborhood_propertyshow_layout');
        else $tpl = wpl_global::get_setting('wpl_propertyshow_layout');

        if(trim($tpl) == '') $tpl = 'default';
        
        $this->tpl = wpl_request::getVar('tpl', $tpl);

		/** no property found **/
		if(!$property or $property['finalized'] == 0 or $property['confirmed'] == 0 or $property['deleted'] == 1 or $property['expired'] >= 1)
		{
			/** import message tpl **/
			if(isset($property['confirmed']) and !$property['confirmed']) $this->message = __("Sorry! The property is not visible until it is confirmed by someone.", 'real-estate-listing-realtyna-wpl');
            else $this->message = __("Sorry! Either the url is incorrect or the listing is no longer available.", 'real-estate-listing-realtyna-wpl');
            
			return parent::render($this->tpl_path, 'message', false, true);
		}
		
		$current_user = wpl_users::get_wpl_user();
		$lrestrict = $current_user->maccess_lrestrict_pshow;
		$rlistings = explode(',', $current_user->maccess_listings_pshow);
		$ptrestrict = $current_user->maccess_ptrestrict_pshow;
		$rproperty_types = explode(',', $current_user->maccess_property_types_pshow);

		if(($lrestrict and !in_array($property['listing'], $rlistings)) or ($ptrestrict and !in_array($property['property_type'], $rproperty_types)))
		{
			$this->message = __("Sorry! You don't have access to view this property.", 'real-estate-listing-realtyna-wpl');
            
			return parent::render($this->tpl_path, 'message', false, true);
		}
        
		$this->pshow_fields = $this->model->get_pshow_fields('', $property['kind']);
		$this->pshow_categories = wpl_flex::get_categories('', '', " AND `enabled`>='1' AND `kind`='".$property['kind']."' AND `pshow`='1'");
		$wpl_properties = array();

		/** Microdata **/
		$this->microdata = isset($this->settings['microdata']) ? $this->settings['microdata'] : 0;
		$this->itemscope = ($this->microdata) ? 'itemscope' : '';

		$this->itemprop_name = ($this->microdata) ? 'itemprop="name"' : '';
		$this->itemprop_value = ($this->microdata) ? 'itemprop="value"' : '';
		$this->itemprop_floorSize = ($this->microdata) ? 'itemprop="floorSize"' : '';
		$this->itemprop_price = ($this->microdata) ? 'itemprop="price"' : '';
		$this->itemprop_url = ($this->microdata) ? 'itemprop="url"' : '';
		$this->itemprop_address = ($this->microdata) ? 'itemprop="address"' : '';
		$this->itemprop_description = ($this->microdata) ? 'itemprop="description"' : '';
		$this->itemprop_additionalProperty = ($this->microdata) ? 'itemprop="additionalProperty"' : '';
		$this->itemprop_addressLocality = ($this->microdata) ? 'itemprop="addressLocality"' : '';
		$this->itemprop_numberOfRooms = ($this->microdata) ? 'itemprop="numberOfRooms"' : '';

		$this->itemtype_PropertyValue = ($this->microdata) ? 'itemtype="http://schema.org/PropertyValue"' : '';
		$this->itemtype_Apartment = ($this->microdata) ? 'itemtype="http://schema.org/Apartment"' : '';
		$this->itemtype_ApartmentComplex = ($this->microdata) ? 'itemtype="http://schema.org/ApartmentComplex"' : '';
		$this->itemtype_SingleFamilyResidence = ($this->microdata) ? 'itemtype="http://schema.org/SingleFamilyResidence"' : '';
		$this->itemtype_Place = ($this->microdata) ? 'itemtype="http://schema.org/Place"' : '';
		$this->itemtype_PostalAddress = ($this->microdata) ? 'itemtype="http://schema.org/PostalAddress"' : '';
		$this->itemtype_offer = ($this->microdata) ? 'itemtype="http://schema.org/offer"' : '';
		$this->itemtype_QuantitativeValue = ($this->microdata) ? 'itemtype="http://schema.org/QuantitativeValue"' : '';

        /*Agent and office name for mls compliance*/
        $this->show_agent_name = isset($this->settings['show_agent_name']) ? $this->settings['show_agent_name'] : 0;
        $this->show_office_name = isset($this->settings['show_listing_brokerage']) ? $this->settings['show_listing_brokerage'] : 0;

        $this->label_agent_name = isset($this->settings['label_agent_name']) ? $this->settings['label_agent_name'] : 0;
        $this->label_office_name = isset($this->settings['label_listing_brokerage']) ? $this->settings['label_listing_brokerage'] : 0;

        // Realtyna Signature and Affiliate
        $this->show_signature = isset($this->settings['realtyna_signature']) ? $this->settings['realtyna_signature'] : 1;
        $this->affiliate_id = (isset($this->settings['realtyna_affiliate_id']) and trim($this->settings['realtyna_affiliate_id'])) ? $this->settings['realtyna_affiliate_id'] : 4;

		/** define current index **/
		$wpl_properties['current']['data'] = (array) $property;
		$wpl_properties['current']['raw'] = (array) $property;
        
        $find_files = array();
		$rendered_fields = $this->model->render_property($property, $this->pshow_fields, $find_files, true);
        
		$wpl_properties['current']['rendered_raw'] = $rendered_fields['ids'];
        $wpl_properties['current']['materials'] = $rendered_fields['columns'];
		
		foreach($this->pshow_categories as $pshow_category)
		{
			if(trim($pshow_category->listing_specific) != '')
            {
                if(substr($pshow_category->listing_specific, 0, 5) == 'type=')
                {
                    $specified_listings = wpl_global::get_listing_types_by_parent(substr($pshow_category->listing_specific, 5));

                    $array_specified_listing = array();
                    foreach($specified_listings as $specified_listing) $array_specified_listing[] = $specified_listing['id'];

                    if(!in_array($wpl_properties['current']['data']['listing'], $array_specified_listing)) continue;
                }
            }
            elseif(trim($pshow_category->property_type_specific) != '')
            {
            	if(substr($pshow_category->property_type_specific, 0, 5) == 'type=')
                {
                    $specified_property_types = wpl_global::get_property_types_by_parent(substr($pshow_category->property_type_specific, 5));

                    $array_specified_property_types = array();
                    foreach($specified_property_types as $specified_property_type) $array_specified_property_types[] = $specified_property_type['id'];

                    if(!in_array($wpl_properties['current']['data']['property_type'], $array_specified_property_types)) continue;
                }
            }

			$pshow_cat_fields = $this->model->get_pshow_fields($pshow_category->id, $property['kind']);
			$wpl_properties['current']['rendered'][$pshow_category->id]['self'] = (array) $pshow_category;
			$wpl_properties['current']['rendered'][$pshow_category->id]['data'] = $this->model->render_property($property, $pshow_cat_fields);
		}
		
		$wpl_properties['current']['items'] = wpl_items::get_items($this->pid, '', $property['kind'], '', 1);
		/** property location text **/ $wpl_properties['current']['location_text'] = $this->model->generate_location_text((array) $property);
		/** property full link **/ $wpl_properties['current']['property_link'] = $this->model->get_property_link((array) $property);
        /** property page title **/ $wpl_properties['current']['property_page_title'] = $this->model->update_property_page_title($property);
        /** property title **/ $wpl_properties['current']['property_title'] = $this->model->update_property_title($property);

        // Apply Filters
		@extract(wpl_filters::apply('property_listing_after_render', array('wpl_properties'=>$wpl_properties)));
		
		$this->wpl_properties = $wpl_properties;
		$this->kind = $property['kind'];
		$this->property = $wpl_properties['current'];
		
		/** updating the visited times and etc **/
		wpl_property::property_visited($this->pid);
		
        // Location visibility
        $this->location_visibility = wpl_property::location_visibility($this->pid, $this->kind, wpl_users::get_user_membership());
        
        /** trigger event **/
		wpl_global::event_handler('property_show', array('id'=>$this->pid));
        
		/** Property Show fields Columns Count **/
		$fields_columns = wpl_global::get_setting('wpl_ui_customizer_property_show_fields_columns');
		$this->fields_columns = trim($fields_columns) ? $fields_columns : '3';
        
		/** import tpl **/
        $this->tpl = wpl_flex::get_kind_tpl($this->tpl_path, $this->tpl, $this->kind);
		$output = parent::render($this->tpl_path, $this->tpl, false, true);
        
        if($this->wplraw)
        {
            echo $output;
            exit;
        }
        else return $output;
	}
}