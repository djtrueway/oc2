<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.locations');
_wpl_import('libraries.users');

/**
 * WPL Search Widget
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update
 */
class wpl_search_widget extends wpl_widget
{
	public $wpl_tpl_path = 'widgets.search.tmpl';
	public $wpl_backend_form = 'widgets.search.form';
	public $listing_specific_array = array();
	public $property_type_specific_array = array();
	public $field_specific_array = array();
	public $target_id;
	public $kind;
	public $ajax;
	public $show_total_results;
	public $more_options_type;
	public $show_reset_button;
	public $style;
	public $show_saved_searches;
	public $show_favorites;
	public $rendered;
	public $saved_searches_count;
	public $favorites_count;

	public function __construct()
	{
		parent::__construct('wpl_search_widget', __('(WPL) Search', 'real-estate-listing-realtyna-wpl'), array('description'=>__('Search properties/profiles.', 'real-estate-listing-realtyna-wpl')));
	}

    /**
     * @param array $args
     * @param array $instance
     */
	public function widget($args, $instance)
	{
        $this->widget_id = $this->number;
        if($this->widget_id < 0) $this->widget_id = abs($this->widget_id)+1000;

        $this->widget_uq_name = 'wpls'.$this->widget_id;
		$widget_id = $this->widget_id;
		$this->target_id = isset($instance['wpltarget']) ? $instance['wpltarget'] : 0;

        $this->kind = isset($instance['kind']) ? $instance['kind'] : 0;
        $this->ajax = isset($instance['ajax']) ? $instance['ajax'] : 0;
        $this->show_total_results = isset($instance['total_results']) ? $instance['total_results'] : 0;
        $this->css_class = isset($instance['css_class']) ? $instance['css_class'] : '';
        $this->more_options_type = isset($instance['more_options_type']) ? $instance['more_options_type'] : '0';
        $this->show_reset_button = isset($instance['show_reset_button']) ? $instance['show_reset_button'] : '0';
		$this->style = isset($instance['style']) ? $instance['style'] : '0';
		$this->show_saved_searches = isset($instance['show_saved_searches']) ? $instance['show_saved_searches'] : '0';
		$this->show_favorites = isset($instance['show_favorites']) ? $instance['show_favorites'] : '0';

		/** add main scripts **/
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-ui-datepicker');

        /** add Layout js **/
        $js[] = (object) array('param1'=>'jquery.checkbox', 'param2'=>'packages/jquery.ui/checkbox/jquery.checkbox.min.js');
        foreach($js as $javascript) wpl_extensions::import_javascript($javascript);

		echo isset($args['before_widget']) ? $args['before_widget'] : '';

		$title = apply_filters('widget_title', (isset($instance['title']) ? $instance['title'] : ''));
		if(trim($title) != '') echo (isset($args['before_title']) ? $args['before_title'] : '') .$title. (isset($args['after_title']) ? $args['after_title'] : '');

		$layout = 'widgets.search.tmpl.'.(isset($instance['layout']) ? $instance['layout'] : 'default');
		$layout = _wpl_import($layout, true, true);
		$find_files = array();

		/** render search fields **/
		$this->rendered = $this->render_search_fields($instance, $widget_id, $find_files);

		/** generate stats **/
		$current_user_id = wpl_users::get_cur_user_id();
        
		$this->saved_searches_count = 0;
		$this->favorites_count = 0;

		if(wpl_global::check_addon('pro'))
		{
            _wpl_import('libraries.addon_pro');

            if($current_user_id) $favorites = wpl_addon_pro::favorite_get_pids(false, $current_user_id);
            else $favorites = wpl_addon_pro::favorite_get_pids(true);

            $this->favorites_count = count($favorites);
		}

		if(wpl_global::check_addon('save_searches') and $current_user_id)
		{
            _wpl_import('libraries.addon_save_searches');

            $save_searches = new wpl_addon_save_searches();
            $this->saved_searches_count = count($save_searches->get('', $current_user_id));
		}

		if(!wpl_file::exists($layout)) $layout = _wpl_import('widgets.search.tmpl.default', true, true);

		if(wpl_file::exists($layout)) require $layout;
		else echo __('Widget Layout Not Found!', 'real-estate-listing-realtyna-wpl');

		echo isset($args['after_widget']) ? $args['after_widget'] : '';
	}

    /**
     * @param array $new_instance
     * @param array $old_instance
     * @return array
     */
	public function update($new_instance, $old_instance)
	{
		$instance = array();
		$instance['title'] = strip_tags($new_instance['title']);
        $instance['kind'] = isset($new_instance['kind']) ? $new_instance['kind'] : 0;
		$instance['layout'] = $new_instance['layout'];
        $instance['wpltarget'] = $new_instance['wpltarget'];
        $instance['ajax'] = isset($new_instance['ajax']) ? $new_instance['ajax'] : 0;
        $instance['total_results'] = isset($new_instance['total_results']) ? $new_instance['total_results'] : 0;
        $instance['css_class'] = isset($new_instance['css_class']) ? $new_instance['css_class'] : '';
		$instance['data'] = (array) $new_instance['data'];
		$instance['more_options_type'] = isset($new_instance['more_options_type']) ? $new_instance['more_options_type'] : 0;
		$instance['show_reset_button'] = isset($new_instance['show_reset_button']) ? $new_instance['show_reset_button'] : 0;
		$instance['style'] = isset($new_instance['style']) ? $new_instance['style'] : 0;
		$instance['show_saved_searches'] = isset($new_instance['show_saved_searches']) ? $new_instance['show_saved_searches'] : 0;
		$instance['show_favorites'] = isset($new_instance['show_favorites']) ? $new_instance['show_favorites'] : 0;

		return $instance;
	}

    /**
     * @param array $instance
     * @return string|void
     */
	public function form($instance)
	{
        $this->widget_id = $this->number;

        $this->kind = isset($instance['kind']) ? $instance['kind'] : 0;
        $this->ajax = isset($instance['ajax']) ? $instance['ajax'] : 0;

		_wpl_import('libraries.flex');

		/** add main scripts **/
		wp_enqueue_script('jquery-ui-core');
		wp_enqueue_script('jquery-ui-slider');
		wp_enqueue_script('jquery-ui-datepicker');
		wp_enqueue_script('jquery-ui-draggable');
		wp_enqueue_script('jquery-ui-droppable');
		wp_enqueue_script('jquery-effects-core');

		/* Set up some default widget settings. */
		if(!isset($instance['layout']))
		{
			$instance = array('title'=>__('Search', 'real-estate-listing-realtyna-wpl'), 'layout'=>'default.php', 'data'=>self::make_array_defaults(wpl_flex::get_fields('', 1, $this->kind, 'searchmod', 1)));
			$defaults = array();
			$instance = wp_parse_args((array) $instance, $defaults);
		}

		$path = _wpl_import($this->wpl_backend_form, true, true);

		ob_start();
		include $path;
		echo $output = ob_get_clean();
	}

    /**
     * @param $values
     */
	public function generate_backend_categories($values)
	{
        $categories = wpl_flex::get_categories(1, $this->kind, " AND `searchmod`=1 AND `kind`='{$this->kind}' AND `enabled`>=1");

        // Tab Content
		foreach($categories as $category) include _wpl_import('widgets.search.scripts.fields_category', true, true);
	}

    /**
     * @param $values
     */
    public function generate_backend_categories_tabs($values)
	{
		$categories = wpl_flex::get_categories(1, $this->kind, " AND `searchmod`=1 AND `kind`='{$this->kind}' AND `enabled`>=1");

        // Tabs
		foreach($categories as $category) include _wpl_import('widgets.search.scripts.fields_category_tabs', true, true);
	}

    /**
     * @param $fields
     * @param $values
     * @param array $finds
     */
	public function generate_backend_fields($fields, $values, &$finds = array())
	{
		# File Listing
		$path = WPL_ABSPATH .DS. 'libraries' .DS. 'widget_search' .DS. 'backend';
		$files = array();

		if(wpl_folder::exists($path)) $files = wpl_folder::files($path, '.php$');

		foreach($fields as $key=>$field)
		{
			if(!$field) return;

			$done_this = false;
			$type = $field->type;
			$options = json_decode($field->options, true);
			$value = isset($values[$field->id]) ? $values[$field->id] : NULL;

			if(isset($finds[$type]))
			{
				include($path .DS. $finds[$type]);
				continue;
			}

			foreach($files as $file)
			{
				include($path .DS. $file);

				/** break and go to next field **/
				if($done_this)
				{
					$finds[$type] = $file;
					break;
				}
			}
		}
	}

    /**
     * @param $instance
     * @param $widget_id
     * @param array $finds
     * @return array
     */
	public function render_search_fields($instance, $widget_id, $finds = array())
	{
		// First Validation
		if(!$instance) return array();

		$path = WPL_ABSPATH .DS. 'libraries' .DS. 'widget_search' .DS. 'frontend';
		$files = array();

		if(wpl_folder::exists($path)) $files = wpl_folder::files($path, '.php$');

		$fields = $instance['data'];
		uasort($fields, array('wpl_global', 'wpl_array_sort'));

		// Current User Membership ID
		$cur_membership_id = wpl_users::get_user_membership();

		$rendered = array();
		foreach($fields as $key=>$field)
		{
			// Proceed to next field if field is not enabled
			if(!isset($field['enable']) or (isset($field['enable']) and $field['enable'] != 'enable')) continue;

            // Fix empty id issue
            if((!isset($field['id']) or (isset($field['id']) and !$field['id'])) and $key) $field['id'] = $key;

			$field_data = (array) wpl_flex::get_field($field['id']);
            if(!$field_data) continue;

			$field['name'] = $field_data['name'];

			$type = $field_data['type'];
			$field_id = $field['id'];
			$options = json_decode($field_data['options'], true);
			$specified_children = wpl_flex::get_field_specific_children($field_id);

			$display = '';
			$done_this = false;
			$html = '';
            $current_value = '';

			// Listing and Property Type Specific Fields
			if(trim($field_data['listing_specific']) != '')
			{
				$specified_listings = explode(',', trim($field_data['listing_specific'], ', '));
				$this->listing_specific_array[$field_data['id']] = $specified_listings;
			}
			elseif(trim($field_data['property_type_specific']) != '')
			{
				$specified_property_types = explode(',', trim($field_data['property_type_specific'], ', '));
				$this->property_type_specific_array[$field_data['id']] = $specified_property_types;
			}
			elseif(trim($field_data['field_specific']) != '')
			{
				$this->field_specific_array[$field_data['id']] = $field_data['field_specific'];
			}

            // Accesses
            if(trim($field_data['accesses']) != '')
            {
                $accesses = explode(',', trim($field_data['accesses'], ', '));
                if(!in_array($cur_membership_id, $accesses)) continue;
            }

			if(isset($finds[$type]))
			{
				$html .= '<div class="wpl_search_field_container wpl_search_field_'.$type.' wpl_search_field_container_'.$field['id'].' '.(isset($field['type']) ? $field['type'].'_type' : '').' '.((isset($field['type']) and $field['type'] == 'predefined') ? 'wpl_hidden' : '').'" id="wpl'.$widget_id.'_search_field_container_'.$field['id'].'">';
				include($path .DS. $finds[$type]);
				$html .= '</div>';

				$rendered[$field_id]['id'] = $field_id;
				$rendered[$field_id]['field_data'] = $field_data;
				$rendered[$field_id]['field_options'] = json_decode($field_data['options'], true);
				$rendered[$field_id]['search_options'] = isset($field['extoption']) ? $field['extoption'] : NULL;
				$rendered[$field_id]['html'] = $html;
                $rendered[$field_id]['current_value'] = isset($current_value) ? $current_value : NULL;
				$rendered[$field_id]['display'] = $display;
				continue;
			}

			$html .= '<div class="wpl_search_field_container wpl_search_field_'.$type.' wpl_search_field_container_'.$field['id'].' '.(isset($field['type']) ? $field['type'].'_type' : '').' '.((isset($field['type']) and $field['type'] == 'predefined') ? 'wpl_hidden' : '').'" id="wpl'.$widget_id.'_search_field_container_'.$field['id'].'" style="'.$display.'">';
			foreach($files as $file)
			{
				include($path .DS. $file);

				// Proceed to next field
				if($done_this)
				{
					$finds[$type] = $file;
					break;
				}
			}

			$html .= '</div>';

			$rendered[$field_id]['id'] = $field_id;
			$rendered[$field_id]['field_data'] = $field_data;
			$rendered[$field_id]['field_options'] = json_decode($field_data['options'], true);
			$rendered[$field_id]['search_options'] = isset($field['extoption']) ? $field['extoption'] : NULL;
			$rendered[$field_id]['html'] = $html;
            $rendered[$field_id]['current_value'] = isset($current_value) ? $current_value : NULL;
			$rendered[$field_id]['display'] = $display;
		}

		return $rendered;
	}

    /**
     * @param array $fields
     * @return array
     */
	public function make_array_defaults($fields)
	{
		$array_defaults = array();

		foreach($fields as $key=>$field)
		{
			if(!$field) return $array_defaults;

			$type = $field->type;
			$options = json_decode($field->options, true);
			$array_defaults[$field->id] = array('id'=>$field->id, 'enable'=>'disable', 'name'=>$field->name);
		}

		return $array_defaults;
	}

    /**
     * @param integer $target_id
     * @return string
     */
    public function get_target_page($target_id = NULL)
    {
        if(trim($target_id) and $target_id == '-1') $target_page = wpl_global::get_full_url();
        else $target_page = wpl_property::get_property_listing_link($target_id);

        return $target_page;
    }

	public function create_listing_specific_js()
	{
        $lt_dbst_id = wpl_flex::get_dbst_id('listing', $this->kind);

        $sale_listings = wpl_global::get_listing_types_by_parent('1');
        $rental_listings = wpl_global::get_listing_types_by_parent('2,3');

		echo '
		function wpl_listing_changed'.$this->widget_id.'(value)
		{
            if(typeof value == "undefined") value = wplj("#wpl_searchwidget_'.$this->widget_id.' #wpl'.$this->widget_id.'_search_field_container_'.$lt_dbst_id.' .wpl_search_widget_field_'.$lt_dbst_id.'").val();
            if(typeof value == "undefined") return false;
        ';

        $cond_sale = '';
        foreach($sale_listings as $sale_listing) $cond_sale .= 'value=='.$sale_listing['id'].'||';
        $cond_sale = rtrim($cond_sale, '||');

        $cond_rental = '';
        foreach($rental_listings as $rental_listing) $cond_rental .= 'value=='.$rental_listing['id'].'||';
        $cond_rental = rtrim($cond_rental, '||');

        if(trim($cond_sale) == '') $cond_sale = 'false';
        if(trim($cond_rental) == '') $cond_rental = 'false';

        echo '
        // Sale and Rental prices
        try
        {
            if('.$cond_sale.')
            {
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_sale").removeClass("wpl-util-hidden");
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_sale .wpl_search_widget_price_field").removeClass("wpl-exclude-search-widget");

                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_rental").addClass("wpl-util-hidden");
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_rental .wpl_search_widget_price_field").addClass("wpl-exclude-search-widget");
            }
            else if('.$cond_rental.')
            {
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_rental").removeClass("wpl-util-hidden");
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_rental .wpl_search_widget_price_field").removeClass("wpl-exclude-search-widget");

                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_sale").addClass("wpl-util-hidden");
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_sale .wpl_search_widget_price_field").addClass("wpl-exclude-search-widget");
            }
            else
            {
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_sale").removeClass("wpl-util-hidden");
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_sale .wpl_search_widget_price_field").removeClass("wpl-exclude-search-widget");

                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_rental").addClass("wpl-util-hidden");
                wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl_listing_price_rental .wpl_search_widget_price_field").addClass("wpl-exclude-search-widget");
            }
        }catch(err){}
        ';

		foreach($this->listing_specific_array as $id=>$listing_specific)
		{
			if($listing_specific == '') continue;

			if(is_array($listing_specific)) $listings = $listing_specific;
			else $listings = explode(',', $listing_specific);

			$cond = 'value==-1||';
			foreach($listings as $listing) $cond .= 'value=='.$listing.'||';
			$cond = rtrim($cond, '||');

			if(trim($cond) != 'value==')
			{
				echo '
				try
				{
					if('.$cond.')
						wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl'.$this->widget_id.'_search_field_container_'.$id.', #wpl_searchwidget_'.$this->widget_id.' #wpl'.$this->widget_id.'_search_field_container_'.$id.'").css("display", "");
					else
						wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl'.$this->widget_id.'_search_field_container_'.$id.', #wpl_searchwidget_'.$this->widget_id.' #wpl'.$this->widget_id.'_search_field_container_'.$id.'").css("display", "none");
				}catch(err){}
				';
			}
		}

		echo '
		}
        (function($){$(function(){wpl_listing_changed'.$this->widget_id.'()});})(jQuery);';
	}

	public function create_property_type_specific_js()
	{
        $pt_dbst_id = wpl_flex::get_dbst_id('property_type', $this->kind);

		echo '
		function wpl_property_type_changed'.$this->widget_id.'(value)
		{
            if(typeof value == "undefined") value = wplj("#wpl_searchwidget_'.$this->widget_id.' #wpl'.$this->widget_id.'_search_field_container_'.$pt_dbst_id.' .wpl_search_widget_field_'.$pt_dbst_id.'").val();
            if(typeof value == "undefined") return false;
        ';

		foreach($this->property_type_specific_array as $id=>$property_type_specific)
		{
			if($property_type_specific == '') continue;

			if(is_array($property_type_specific)) $property_types = $property_type_specific;
			else $property_types = explode(',', $property_type_specific);

			$cond = 'value==-1||';
			foreach($property_types as $property_type) $cond .= 'value=='.$property_type.'||';
			$cond = rtrim($cond, '||');

			if (trim($cond) != 'value==')
			{
				echo '
				try
				{
					if('.$cond.')
						wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl'.$this->widget_id.'_search_field_container_'.$id.', #wpl_searchwidget_'.$this->widget_id.' #wpl'.$this->widget_id.'_search_field_container_'.$id.'").css("display", "");
					else
						wplj("#wpl_searchwidget_'.$this->widget_id.' .wpl'.$this->widget_id.'_search_field_container_'.$id.', #wpl_searchwidget_'.$this->widget_id.' #wpl'.$this->widget_id.'_search_field_container_'.$id.'").css("display", "none");
				}catch(err){}
				';
			}
		}

		echo '
		}
        (function($){$(function(){wpl_property_type_changed'.$this->widget_id.'()});})(jQuery);';
	}

	public function create_field_specific_js()
	{
        $init_script = '';
        foreach($this->field_specific_array as $id=>$field_specific)
		{
			if($field_specific == '') continue;
			$ex = explode(':', $field_specific);
			$init_script .= "wpl_field_specific_changed{$this->widget_id}('{$ex[0]}');";
		}

		echo '
		function wpl_field_specific_changed'.$this->widget_id.'(field, visible)
		{
            if(typeof field == "undefined") return false;
			try
			{
				var field_id = field;
				field = "#wpl_searchwidget_'.$this->widget_id.' .wpl'.$this->widget_id.'_search_field_container_"+field+", #wpl_searchwidget_'.$this->widget_id.' #wpl'.$this->widget_id.'_search_field_container_"+field+" [class^=\'wpl_search_widget_field_"+field+"\']";

			    if(!wplj(field).attr("data-specific")) return false;

			    var visible = (typeof visible == "undefined") ? true : visible;
			    var children = wplj(field).data("specific").split(",");
			    var value = (wplj(field).is(":checkbox") || wplj(field).is(":radio")) ? wplj(field).is(":checked") : wplj(field).val();

			    for (var i = 0; i < children.length; i++)
			    {
			        var split = children[i].split(":");
			        var child = "#wpl_searchwidget_'.$this->widget_id.' .wpl'.$this->widget_id.'_search_field_container_"+split[0]+", #wpl_searchwidget_'.$this->widget_id.' #wpl'.$this->widget_id.'_search_field_container_"+split[0];
			        var child_visible = false;

			        if(!visible || split[1] != value)
			        {
			            child_visible = false;
			            wplj(child).css("display", "none");
			        }
			        else
			        {
			            child_visible = true;
			            wplj(child).css("display", "");
			        }

			        wpl_field_specific_changed'.$this->widget_id.'(split[0], child_visible);
			    }

			}catch(err){}
		}(function($){$(function(){'.$init_script.'});})(jQuery);';
	}
}