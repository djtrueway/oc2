<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.flex');
_wpl_import('libraries.users');
_wpl_import('libraries.images');
_wpl_import('libraries.sort_options');

/**
 * WPL Agents Widget
 * This class handles everything that needs to be handled with the widget:
 * the settings, form, display, and update
 */
class wpl_agents_widget extends wpl_widget
{
	public $wpl_tpl_path = 'widgets.agents.tmpl';
	public $wpl_backend_form = 'widgets.agents.form';
    public $start;
    public $limit;
    public $orderby;
    public $order;

    public $microdata;
    public $itemscope;
    public $itemprop_name;
    public $itemprop_value;
    public $itemprop_url;
    public $itemprop_telephone;
    public $itemprop_faxNumber;
    public $itemprop_email;
    public $itemprop_image;
    public $itemprop_address;
    public $itemprop_description;
    public $itemprop_additionalProperty;
    public $itemprop_addressLocality;
    public $itemtype_PropertyValue;
    public $itemtype_RealEstateAgent;
	
	public function __construct()
	{
		parent::__construct('wpl_agents_widget', __('(WPL) Agents', 'real-estate-listing-realtyna-wpl'), array('description'=>__('Showing specific agents.', 'real-estate-listing-realtyna-wpl')));
	}

    /**
     * @param array $args
     * @param array $instance
     */
	public function widget($args, $instance)
	{
        $this->widget_id = $this->number;
        if($this->widget_id < 0) $this->widget_id = abs($this->widget_id)+1000;
        
		$this->widget_uq_name = 'wpla'.$this->widget_id;
        
		$this->instance = $instance;
		$widget_id = $this->widget_id;
        
        $this->css_class = isset($instance['data']['css_class']) ? $instance['data']['css_class'] : '';
        
		/** render properties **/
		$query = self::query($instance);
        $model = new wpl_users();
		$profiles = $model->search($query);

		/** global settings **/
		$this->settings = wpl_settings::get_settings();
		$this->microdata = isset($this->settings['microdata']) ? $this->settings['microdata'] : 0;
		$this->itemscope = ($this->microdata) ? 'itemscope' : '';
		$this->itemprop_name = ($this->microdata) ? 'itemprop="name"' : '';
		$this->itemprop_value = ($this->microdata) ? 'itemprop="value"' : '';
		$this->itemprop_url = ($this->microdata) ? 'itemprop="url"' : '';
		$this->itemprop_telephone = ($this->microdata) ? 'itemprop="telephone"' : '';
		$this->itemprop_faxNumber = ($this->microdata) ? 'itemprop="faxNumber"' : '';
		$this->itemprop_email = ($this->microdata) ? 'itemprop="email"' : '';
		$this->itemprop_image = ($this->microdata) ? 'itemprop="image"' : '';
		$this->itemprop_address = ($this->microdata) ? 'itemprop="address"' : '';
		$this->itemprop_description = ($this->microdata) ? 'itemprop="description"' : '';
		$this->itemprop_additionalProperty = ($this->microdata) ? 'itemprop="additionalProperty"' : '';
		$this->itemprop_addressLocality = ($this->microdata) ? 'itemprop="addressLocality"' : '';
		$this->itemtype_PropertyValue = ($this->microdata) ? 'itemtype="http://schema.org/PropertyValue"' : '';
		$this->itemtype_RealEstateAgent = ($this->microdata) ? 'itemtype="http://schema.org/RealEstateAgent"' : '';

        /** return if no property found **/
        if(!count($profiles)) return;
        
		$plisting_fields = $model->get_plisting_fields();
		$wpl_profiles = array();
        $render_params['wpltarget'] = isset($instance['wpltarget']) ? $instance['wpltarget'] : 0;
        $params = array();
        
		foreach($profiles as $profile)
		{
            // User is not exists in WordPress
            if(!wpl_users::is_wp_user($profile->id)) continue;
            
			$wpl_profiles[$profile->id] = $model->full_render($profile->id, $plisting_fields, $profile, $render_params);
            
            $params['image_parentid'] = $profile->id;
            
            /** profile picture **/
            if(isset($wpl_profiles[$profile->id]['profile_picture']['url']))
            {
                $params['image_name'] = isset($wpl_profiles[$profile->id]['profile_picture']['name']) ? $wpl_profiles[$profile->id]['profile_picture']['name'] : '';
                $profile_picture_path = isset($wpl_profiles[$profile->id]['profile_picture']['path']) ? $wpl_profiles[$profile->id]['profile_picture']['path'] : '';

                $wpl_profiles[$profile->id]['profile_picture']['url'] = wpl_images::create_profile_images($profile_picture_path, $instance['data']['image_width'], $instance['data']['image_height'], $params);
                $wpl_profiles[$profile->id]['profile_picture']['image_width'] = isset($instance['data']['image_width']) ? $instance['data']['image_width'] : '';
                $wpl_profiles[$profile->id]['profile_picture']['image_height'] = isset($instance['data']['image_height']) ? $instance['data']['image_height'] : '';
            }
            
            /** company logo **/
            if(isset($wpl_profiles[$profile->id]['company_logo']['url']))
            {
                $params['image_name'] = isset($wpl_profiles[$profile->id]['company_logo']['name']) ? $wpl_profiles[$profile->id]['company_logo']['name'] : '';
                $company_logo_path = isset($wpl_profiles[$profile->id]['company_logo']['path']) ? $wpl_profiles[$profile->id]['company_logo']['path'] : '';
                $wpl_profiles[$profile->id]['company_logo']['url'] = wpl_images::create_profile_images($company_logo_path, $instance['data']['image_width'], $instance['data']['image_height'], $params);
            }
		}
        
		echo $args['before_widget'];
        
		$title = apply_filters('widget_title', $instance['title']);
		if(trim($title) != '') echo $args['before_title'] .$title. $args['after_title'];
		
		$layout = 'widgets.agents.tmpl.'.$instance['layout'];
		$layout = _wpl_import($layout, true, true);
        
		if(!wpl_file::exists($layout)) $layout = _wpl_import('widgets.agents.tmpl.default', true, true);
        
		if(wpl_file::exists($layout)) require $layout;
		else echo __('Widget Layout Not Found!', 'real-estate-listing-realtyna-wpl');
		
		echo $args['after_widget'];
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
		$instance['layout'] = $new_instance['layout'];
        $instance['wpltarget'] = $new_instance['wpltarget'];
		$instance['data'] = (array) $new_instance['data'];
		
        /** random option **/
        if(isset($instance['data']['random']) and $instance['data']['random']) $instance['data']['user_ids'] = '';
        
		return $instance;
	}

    /**
     * @param array $instance
     * @return string|void
     */
	public function form($instance)
	{
        $this->widget_id = $this->number;
        
		/* Set up some default widget settings. */
		if(!isset($instance['layout']))
		{
			$instance = array('title'=>__('Featured Agents', 'real-estate-listing-realtyna-wpl'), 'layout'=>'default.php', 'data'=>array('limit'=>'4', 'orderby'=>'p.first_name', 'order'=>'DESC', 'image_width'=>'230', 'image_height'=>'230'));
			$instance = wp_parse_args((array) $instance, NULL);
		}
		
		$path = _wpl_import($this->wpl_backend_form, true, true);
		
		ob_start();
		include $path;
		echo $output = ob_get_clean();
	}

    /**
     * @param $instance
     * @return string
     */
	private function query($instance)
	{
        $model = new wpl_users();
		$data = $instance['data'];
        
        $this->start = 0;
        $this->limit = $data['limit'];
		$this->orderby = trim($data['orderby']) ? urldecode($data['orderby']) : 'p.first_name';
        $this->order = $data['order'];
		
        $where = array('sf_tmin_id'=>1, 'sf_select_access_public_profile'=>1, 'sf_select_expired'=>0);
		
		if(isset($data['user_type']) and $data['user_type'] != '-1') $where['sf_select_membership_type'] = $data['user_type'];
		if(isset($data['membership']) and $data['membership'] != '') $where['sf_select_membership_id'] = $data['membership'];
		if(trim($data['user_ids'])) $where['sf_multiple_id'] = trim($data['user_ids'], ', ');
		
        if(isset($data['random']) and trim($data['random']) and trim($data['user_ids']) == '')
		{
			$query_rand = "SELECT p.`id` FROM `#__wpl_users` AS p WHERE 1 ".wpl_db::create_query($where)." ORDER BY RAND() LIMIT ".$this->limit;
			$results = wpl_db::select($query_rand);
			
			$rand_ids = array();
			foreach($results as $result) $rand_ids[] = $result->id;
			
            $where['sf_multiple_id'] = implode(',', $rand_ids);
		}
        
        $model->start($this->start, $this->limit, $this->orderby, $this->order, $where);
		return $model->query(false);
	}
}