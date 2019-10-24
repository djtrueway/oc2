<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * SEF service
 * @author Howard <howard@realtyna.com>
 * @date 08/19/2013
 * @package WPL
 */
class wpl_service_sef
{
    public $view;
    public $property_page_title;
    public $property_keywords;
    public $property_description;
    public $user_title;
    public $user_keywords;
    public $user_description;

    /**
     * Service runner
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public function run()
	{
		// Global Settings
		$settings = wpl_global::get_settings();
		$wpl_qs = urldecode(wpl_global::get_wp_qvar('wpl_qs'));
		
        // Get view
		$this->view = wpl_sef::get_view($wpl_qs, $settings['sef_main_separator']);
        
        // Add classes to body
        if(trim($this->view)) add_filter('body_class', array($this, 'body_class'));
        
		// Set vars
		wpl_sef::setVars($this->view, $wpl_qs);
        
        // Trigger event
		wpl_global::event_handler('wplview_detected', array('wplview'=>$this->view));
        
		if($this->view == 'property_show')
		{
            if(trim($wpl_qs) != '')
            {
                $ex = explode('-', $wpl_qs);
                $exp = explode('-', $ex[0]);
                $proeprty_id = $exp[0];
            }
			else
            {
                $proeprty_id = wpl_request::getVar('pid', NULL);
                if(!$proeprty_id) $proeprty_id = wpl_request::getVar('property_id', NULL);
            }
            
			if(trim($wpl_qs) != '') $this->check_property_link($proeprty_id);
			$this->set_property_page_params($proeprty_id);
		}
		elseif($this->view == 'profile_show')
		{
            $ex = explode($settings['sef_main_separator'], $wpl_qs);
			$username = isset($ex[0]) ? $ex[0] : NULL;
            $user_id = 0;

            if(trim($username) != '') $user_id = wpl_users::get_id_by_username($username);
            elseif(wpl_request::getVar('sf_select_user_id', 0)) $user_id = wpl_request::getVar('sf_select_user_id', 0);
            elseif(wpl_request::getVar('uid', 0)) $user_id = wpl_request::getVar('uid', 0);
                
			$this->set_profile_page_params($user_id);
		}
		elseif($this->view == 'property_listing')
        {
            $this->set_property_listing_page_params();
        }
		elseif($this->view == 'profile_listing')
        {
            $this->set_profile_listing_page_params();
        }
		elseif($this->view == 'features')
		{
			$function = str_replace('features/', '', $wpl_qs);
			if(!trim($function)) $function = wpl_request::getVar('wpltype');
            
			_wpl_import('views.basics.features.wpl_'.$function);
            
            if(!class_exists('wpl_features_controller'))
            {
                http_response_code(404);
                echo 'Not Found!';
                exit;
            }
            
			$obj = new wpl_features_controller();
			$obj->display();
		}
        elseif($this->view == 'addon_crm')
		{
			_wpl_import('views.frontend.addon_crm.wpl_main');
            
			$obj = new wpl_addon_crm_controller();
			$obj->display();
		}
        elseif($this->view == 'payments')
        {
            $this->set_payments_page_params();
        }
        elseif($this->view == 'addon_membership')
        {
            $this->set_addon_membership_page_params();
        }
        elseif($this->view == 'compare')
        {
            if(wpl_global::check_addon('pro'))
            {
                wpl_addon_pro::compare_display();
            }
        }
        
        // Print Geo Meta Tags
        $this->geotags();
        
        // Print Geo Meta Tags
        $this->dublincore();
	}
	
    /**
     * Checke proeprty alias and 301 redirect the page to the correct link
     * @author Howard <howard@realtyna.com>
     * @param int $proeprty_id
     */
	public function check_property_link($proeprty_id)
	{
        // Global Settings
        $settings = wpl_global::get_settings();
		$wpl_qs = urldecode(wpl_global::get_wp_qvar('wpl_qs'));
		
		// Check property alias for avoiding duplicate content
		$called_alias = $wpl_qs;
        
        $column = 'alias';
        $field_id = wpl_flex::get_dbst_id($column, wpl_property::get_property_kind($proeprty_id));
        $field = wpl_flex::get_field($field_id);
        
        if(isset($field->multilingual) and $field->multilingual and wpl_global::check_multilingual_status()) $column = wpl_addon_pro::get_column_lang_name($column, wpl_global::get_current_language(), false);
        
        $alias = wpl_db::get($column, 'wpl_properties', 'id', $proeprty_id);
        if(trim($alias) == '') $alias = wpl_property::update_alias(NULL, $proeprty_id);
        
		$property_alias = $proeprty_id.'-'.urldecode($alias);
		
		if(trim($alias) and $called_alias != $property_alias)
		{
			$url = wpl_sef::get_wpl_permalink(true).'/'.urlencode($property_alias);
			
			header('HTTP/1.1 301 Moved Permanently');
			header('Location: '.$url);
			exit;
		}

		// 404 Redirect
		if(isset($settings['listing_404_redirect']) and $settings['listing_404_redirect'] and !wpl_db::exists($proeprty_id, 'wpl_properties'))
		{
            $property_listing = wpl_property::get_property_listing_link();

            header('HTTP/1.1 301 Moved Permanently');
            header('Location: '.$property_listing);
            exit;
        }
	}
	
    /**
     * Sets property single page parameters
     * @author Howard <howard@realtyna.com>
     * @param int $proeprty_id
     */
	public function set_property_page_params($proeprty_id)
	{
		$property_data = wpl_property::get_property_raw_data($proeprty_id);
        
        $locale = wpl_global::get_current_language();
		$this->property_page_title = wpl_property::update_property_page_title($property_data);
        
        $meta_keywords_column = 'meta_keywords';
        if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($meta_keywords_column, $property_data['kind'])) $meta_keywords_column = wpl_addon_pro::get_column_lang_name($meta_keywords_column, $locale, false);
        
        $this->property_keywords = $property_data[$meta_keywords_column];
        if(trim($this->property_keywords) == '') $this->property_keywords = wpl_property::get_meta_keywords($property_data);
        
        $meta_description_column = 'meta_description';
        if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($meta_description_column, $property_data['kind'])) $meta_description_column = wpl_addon_pro::get_column_lang_name($meta_description_column, $locale, false);
        
        $this->property_description = $property_data[$meta_description_column];
        if(trim($this->property_description) == '') $this->property_description = wpl_property::get_meta_description($property_data);
        
		$html = wpl_html::getInstance();
		
		// Set Title
		$html->set_title($this->property_page_title);
		
		// Set Meta Keywords
		$html->set_meta_keywords($this->property_keywords);
		
		// Set Meta Description
		$html->set_meta_description(strip_tags($this->property_description));
        
        // SET Canonical URL
        $property_link = wpl_property::get_property_link($property_data);
        wpl_html::$canonical = $property_link;
        
        // Remove canonical tags
        $this->remove_canonical();
        
        // Remove Page Title Filters
		$this->remove_page_title_filters();
        
        // Remove Open Graph Filters
        $this->remove_open_graph_filters();
        
        $html->set_custom_tag('<meta property="og:type" content="article" />');
        $html->set_custom_tag('<meta property="og:locale" content="'.$locale.'" />');
        
        $content_column = 'field_308';
        if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($content_column, $property_data['kind'])) $content_column = wpl_addon_pro::get_column_lang_name($content_column, $locale, false);
        
        $html->set_custom_tag('<meta property="og:url" content="'.str_replace('&', '&amp;', $property_link).'" />');
        $html->set_custom_tag('<meta property="og:title" data-page-subject="true" content="'.$this->property_page_title.'" />');
        $html->set_custom_tag('<meta property="og:description" content="'.strip_tags(stripslashes($property_data[$content_column])).'" />');
        
        $html->set_custom_tag('<meta property="twitter:card" content="summary" />');
        $html->set_custom_tag('<meta property="twitter:title" content="'.$this->property_page_title.'" />');
        $html->set_custom_tag('<meta property="twitter:description" content="'.strip_tags(stripslashes($property_data[$content_column])).'" />');
        $html->set_custom_tag('<meta property="twitter:url" content="'.str_replace('&', '&amp;', $property_link).'" />');
        
        $gallery = wpl_items::get_gallery($proeprty_id, $property_data['kind']);
        if(is_array($gallery) and count($gallery))
        {
            foreach($gallery as $image)
            {
                $html->set_custom_tag('<meta property="og:image" content="'.$image['url'].'" />');
                $html->set_custom_tag('<meta property="twitter:image" content="'.$image['url'].'" />');
                
                // Only print one og and twitter image (First Image)
                break;
            }
        }
	}
	
    /**
     * Sets profile single page parameters
     * @author Howard <howard@realtyna.com>
     * @param int $user_id
     */
	public function set_profile_page_params($user_id)
	{
		$user_data = (array) wpl_users::get_wpl_user($user_id);
		
		$this->user_title = '';
		$this->user_keywords = '';
		$this->user_description = __('Listings of', 'real-estate-listing-realtyna-wpl');
		
		if(trim($user_data['first_name']) != '')
		{
			$this->user_title .= $user_data['first_name'];
			$this->user_keywords .= $user_data['first_name'].',';
			$this->user_description .= ' '.$user_data['first_name'];
		}
		
		if(trim($user_data['last_name']) != '')
		{
			$this->user_title .= ' '.$user_data['last_name'];
			$this->user_keywords .= $user_data['last_name'].',';
			$this->user_description .= ' '.$user_data['last_name'];
		}
		
		if(trim($user_data['company_name']) != '')
		{
			$this->user_title .= ' - '.$user_data['company_name'];
			$this->user_keywords .= $user_data['company_name'].',';
			$this->user_description .= ' - '.$user_data['company_name'];
		}
		
		$this->user_title .= ' '.__('Listings', 'real-estate-listing-realtyna-wpl');
		$this->user_keywords = trim($this->user_keywords, ', ');
		$this->user_description .= ' '.__('which is located in', 'real-estate-listing-realtyna-wpl').' '.$user_data['location_text'];
		
		$html = wpl_html::getInstance();
		wpl_html::$canonical = wpl_users::get_profile_link($user_id);
        
        // Remove canonical tags
        $this->remove_canonical();
        
        // Remove Page Title Filters
		$this->remove_page_title_filters();
        
        // Remove Open Graph Filters
        $this->remove_open_graph_filters();
        
		// Set Title
		$html->set_title($this->user_title);
		
		// Set Meta Keywords
		$html->set_meta_keywords($this->user_keywords);
		
		// Set Meta Description
		$html->set_meta_description($this->user_description);
	}
    
    /**
     * Sets property listing page parameters
     * @author Howard <howard@realtyna.com>
     */
    public function set_property_listing_page_params()
    {
    }
    
    /**
     * Sets profile listing page parameters
     * @author Howard <howard@realtyna.com>
     */
    public function set_profile_listing_page_params()
    {
    }
    
    /**
     * Sets payments page parameters
     * @author Howard <howard@realtyna.com>
     */
    public function set_payments_page_params()
    {
        $html = wpl_html::getInstance();
        
		// Set Title
		$html->set_title(__('Payments', 'real-estate-listing-realtyna-wpl'));
    }
    
    /**
     * Sets addon membership page parameters
     * @author Howard <howard@realtyna.com>
     */
    public function set_addon_membership_page_params()
    {
        $html = wpl_html::getInstance();
        
		// Set Title
		$html->set_title(__('Members', 'real-estate-listing-realtyna-wpl'));
    }
    
    /**
     * Sets Geo Meta Tags
     * @author Howard <howard@realtyna.com>
     * @return boolean
     */
    public function geotags()
    {
        $settings = wpl_global::get_settings();
        
        // Check status of geo tags
        if(!isset($settings['geotag_status']) or (isset($settings['geotag_status']) and !$settings['geotag_status'])) return false;
        
        $html = wpl_html::getInstance();
        
        if(trim($settings['geotag_region'])) $html->set_custom_tag('<meta name="geo.region" content="'.$settings['geotag_region'].'" />');
        if(trim($settings['geotag_placename'])) $html->set_custom_tag('<meta name="geo.placename" content="'.$settings['geotag_placename'].'" />');
        if(trim($settings['geotag_latitude']) and trim($settings['geotag_longitude'])) $html->set_custom_tag('<meta name="geo.position" content="'.$settings['geotag_latitude'].';'.$settings['geotag_longitude'].'" />');
        if(trim($settings['geotag_latitude']) and trim($settings['geotag_longitude'])) $html->set_custom_tag('<meta name="ICBM" content="'.$settings['geotag_latitude'].', '.$settings['geotag_longitude'].'" />');

        return true;
    }
    
    /**
     * Sets Dublin Core Meta Tags
     * @author Howard <howard@realtyna.com>
     * @return boolean
     */
    public function dublincore()
    {
        $settings = wpl_global::get_settings();
        $dc_status = isset($settings['dc_status']) ? $settings['dc_status'] : false;
        
        // Check status of geo tags
        if(!$dc_status) return false;
        
        $current_link_url = wpl_global::get_full_url();
        $html = wpl_html::getInstance();
        
        // WPL views and WordPress views (Page/Post)
        if((trim($this->view) != '' and $dc_status == 2) or $dc_status == 1)
        {
            if(trim($settings['dc_coverage']) != '') $html->set_custom_tag('<meta name="DC.coverage" content="'.$settings['dc_coverage'].'" />');
            if(trim($settings['dc_contributor']) != '') $html->set_custom_tag('<meta name="DC.contributor" content="'.$settings['dc_contributor'].'" />');
            if(trim($settings['dc_publisher']) != '') $html->set_custom_tag('<meta name="DC.publisher" content="'.$settings['dc_publisher'].'" />');
            if(trim($settings['dc_copyright']) != '') $html->set_custom_tag('<meta name="DC.rights" content="'.$settings['dc_copyright'].'" />');
            if(trim($settings['dc_source']) != '') $html->set_custom_tag('<meta name="DC.source" content="'.$settings['dc_source'].'" />');
            if(trim($settings['dc_relation']) != '') $html->set_custom_tag('<meta name="DC.relation" content="'.$settings['dc_relation'].'" />');

            $html->set_custom_tag('<meta name="DC.type" content="Text" />');
            $html->set_custom_tag('<meta name="DC.format" content="text/html" />');
            $html->set_custom_tag('<meta name="DC.identifier" content="'.$current_link_url.'" />');
            
            $locale = apply_filters('plugin_locale', get_locale(), 'real-estate-listing-realtyna-wpl');
            $html->set_custom_tag('<meta name="DC.language" scheme="RFC1766" content="'.$locale.'" />');
        }
        
        if($this->view == 'property_show')
        {
            $proeprty_id = wpl_request::getVar('pid');
            $property_data = wpl_property::get_property_raw_data($proeprty_id);
            $user_data = (array) wpl_users::get_user($property_data['user_id']);
            
            $html->set_custom_tag('<meta name="DC.title" content="'.$this->property_page_title.'" />');
            $html->set_custom_tag('<meta name="DC.subject" content="'.$this->property_page_title.'" />');
            $html->set_custom_tag('<meta name="DC.description" content="'.$this->property_description.'" />');
            $html->set_custom_tag('<meta name="DC.date" content="'.$property_data['add_date'].'" />');
            $html->set_custom_tag('<meta name="DC.creator" content="'.$user_data['data']->user_login.'" />');
        }
        elseif($this->view == 'profile_show')
        {
            $user_id = wpl_request::getVar('uid');
            $user_data = (array) wpl_users::get_user($user_id);
            
            $html->set_custom_tag('<meta name="DC.title" content="'.$this->user_title.'" />');
            $html->set_custom_tag('<meta name="DC.subject" content="'.$this->user_title.'" />');
            $html->set_custom_tag('<meta name="DC.description" content="'.$this->user_description.'" />');
            $html->set_custom_tag('<meta name="DC.date" content="'.$user_data['data']->user_registered.'" />');
            $html->set_custom_tag('<meta name="DC.creator" content="'.$user_data['data']->user_login.'" />');
        }
        elseif(is_single())
        {
            $post_author_id = wpl_global::get_post_field('post_author');
            $author_username = wpl_global::get_the_author_meta('user_login', $post_author_id);
            
            $html->set_custom_tag('<meta name="DC.title" content="'.wpl_global::get_the_title().'" />');
            $html->set_custom_tag('<meta name="DC.subject" content="'.wpl_global::get_the_title().'" />');
            $html->set_custom_tag('<meta name="DC.date" content="'.wpl_global::get_the_date().'" />');
            $html->set_custom_tag('<meta name="DC.creator" content="'.$author_username.'" />');
        }

        return true;
    }
    
    /**
     * For removing canonical URLs from WPL pages
     * @author Howard <howard@realtyna.com>
     */
    public function remove_canonical()
    {
        // Remove Yoast Canonical URL
        add_filter('wpseo_canonical', '__return_false');

        // Remove All in One SEO Pack Canonical URL
        add_filter('aioseop_canonical_url', '__return_false');
    }
    
    /**
     * For removing page title filters of WPL pages that applied by some third party plugins
     * @author Howard <howard@realtyna.com>
     */
    public function remove_page_title_filters()
	{
		// Remove Yoast page title filter
		if(class_exists('WPSEO_Frontend'))
		{
			$yoast = WPSEO_Frontend::get_instance();
            
			remove_filter('pre_get_document_title', array($yoast, 'title'), 15);
			remove_filter('wp_title', array($yoast, 'title'), 15);
		}
	}
    
    /**
     * For removing open graph filters of WPL pages that applied by some third party plugins
     * @author Howard <howard@realtyna.com>
     */
    public function remove_open_graph_filters()
	{
		// Disable JetPack filters
		add_filter('jetpack_enable_open_graph', '__return_false');

        // Disable Yoast Open Graph Tags
        add_filter('wpseo_locale' , '__return_false');
        add_filter('wpseo_opengraph_url' , '__return_false');
        add_filter('wpseo_opengraph_desc', '__return_false');
        add_filter('wpseo_opengraph_title', '__return_false');
        add_filter('wpseo_opengraph_type', '__return_false');
        add_filter('wpseo_opengraph_site_name', '__return_false');
        add_filter('wpseo_opengraph_image' , '__return_false');
        add_filter('wpseo_opengraph_author_facebook' , '__return_false');
        add_filter('wpseo_opengraph_admin' , '__return_false');
        add_filter('wpseo_opengraph_show_publish_date' , '__return_false');

        // Disable Yoast Twitter Card
        add_filter('wpseo_twitter_title' , '__return_false');
        add_filter('wpseo_twitter_description' , '__return_false');
        add_filter('wpseo_twitter_card' , '__return_false');
        add_filter('wpseo_twitter_card_type' , '__return_false');
        add_filter('wpseo_twitter_site' , '__return_false');
        add_filter('wpseo_twitter_image' , '__return_false');
        add_filter('wpseo_twitter_creator_account' , '__return_false');
	}
    
    /**
     * For adding HTML classes to HTML body tag
     * @author Howard <howard@realtyna.com>
     * @param array $classes
     * @return array
     */
    public function body_class($classes)
    {
        $classes[] = 'wpl-page';
        $classes[] = 'wpl_'.$this->view;

        if($this->view == 'property_show')
        {
            $pid = wpl_request::getVar('pid', 0);
            $property = wpl_property::get_property_raw_data($pid);

            if($property['kind'] == 1) $tpl = wpl_global::get_setting('wpl_complex_propertyshow_layout');
            elseif($property['kind'] == 4) $tpl = wpl_global::get_setting('wpl_neighborhood_propertyshow_layout');
            else $tpl = wpl_global::get_setting('wpl_propertyshow_layout');
            
            if(trim($tpl) == '') $tpl = 'default';
            $classes[] = 'wpl_'.$this->view.'_'.$tpl;
        }

        // Add theme compability classes
        if(wpl_global::get_setting('wpl_theme_compatibility'))
        {
            $current_theme = get_option('template');
            
            if($current_theme == 'bridge') $classes[] = 'wpl-bridge-layout';
            elseif($current_theme == 'Avada') $classes[] = 'wpl-avada-layout';
            elseif($current_theme == 'enfold') $classes[] = 'wpl-enfold-layout';
            elseif($current_theme == 'betheme') $classes[] = 'wpl-be-layout';
            elseif($current_theme == 'x') $classes[] = 'wpl-x-layout';
            elseif($current_theme == 'genesis') $classes[] = 'wpl-genesis-layout';
            elseif($current_theme == 'houzez') $classes[] = 'wpl-houzez-layout';
        }

        if(is_rtl())
        {
            $classes[] = 'wpl_rtl';
        }

        return $classes;
    }
}