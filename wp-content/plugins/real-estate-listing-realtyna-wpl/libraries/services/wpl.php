<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * WPL Core service
 * @author Howard <howard@realtyna.com>
 * @date 9/28/2015
 * @package WPL
 */
class wpl_service_wpl
{
    /**
     * Service runner
     * @author Howard <howard@realtyna.com>
     * @return void
     */
	public function run()
	{
        // Run WPL delete user function when a user removed from WordPress
        add_action('delete_user', array('wpl_users', 'delete_user'), 10, 1);
        
        // Start Session
        if(!session_id()) session_start();

        // Setting the default timezone of WordPress
        if(get_option('timezone_string')) date_default_timezone_set(get_option('timezone_string'));
        
        // Shutdown WPL objects
        add_action('wp_footer', array('wpl_global', 'wpl_shutdown'), 99);
        add_action('admin_footer', array('wpl_global', 'wpl_shutdown'), 99);
        
        // Load Google Maps async
        add_filter('script_loader_tag', array($this, 'async_googlemaps'), 99, 2);
        
        /** Do Cronjobs **/
        if(wpl_request::getVar('wpl_do_cronjobs') == 1)
        {
            $this->cronjobs();
        }
        
        if(wpl_global::get_client()) $this->backend();
        else $this->frontend();
	}
    
    public function backend()
    {
        // If we're in an AJAX request don't do the rest
        if(defined('DOING_AJAX') and DOING_AJAX) return;

        // Add classes to the body tag on backend
        add_filter('admin_body_class', array($this, 'backend_body_class'));
        
        // Show update notification in WPL backend
        $available_updates = wpl_global::get_updates_count();
        if($available_updates >= 1 and wpl_users::is_administrator()) wpl_flash::set(sprintf(__('%s update(s) are available for WPL and its addons. Please proceed with update after creating a backup.', 'real-estate-listing-realtyna-wpl'), '<strong>'.$available_updates.'</strong>'), 'wpl_gold_msg', 1);
    }
    
    public function frontend()
    {
        // If we're in an AJAX request don't do the rest
        if(defined('DOING_AJAX') and DOING_AJAX) return;
        
        // Run Theme compatibility option
        if(wpl_global::get_setting('wpl_theme_compatibility')) add_action('wp_enqueue_scripts', array($this, 'theme_compatibility'), 8);

        // Set the geolocation Session
        if(wpl_request::getVar('sf_geolocationstatus', 0)) wpl_session::set('geolocation', 1);
    }
    
    public function cronjobs()
    {
        /** do cronjobs **/
		wpl_events::do_cronjobs();
        
        // Save the latest cronjob run
        $now = date('Y-m-d H:i:s');
        wpl_settings::save_setting('wpl_last_cpanel_cronjobs', $now);
        
        // Exit the execution because it's a cPanel cronjob
        exit;
    }
    
    public function async_googlemaps($tag, $handle)
    {
        if('google-maps-wpl' !== $handle) return $tag;
        
        return str_replace(' src', ' async="async" defer="defer" src', $tag);
    }
    
    public function theme_compatibility()
    {
        $style = NULL;
        $js = NULL;
        $current_theme = get_option('template');

        if($current_theme == 'bridge')
        {
            $style = 'styles/bridge/main.css';
            $js = 'styles/bridge/main.min.js';
		}
        elseif($current_theme == 'Avada')
        {
            $style = 'styles/avada/main.css';
            $js = 'styles/avada/main.min.js';
        }
        elseif($current_theme == 'enfold')
        {
            $style = 'styles/enfold/main.css';
            $js = 'styles/enfold/main.min.js';
        }
        elseif($current_theme == 'betheme')
        {
            $style = 'styles/be/main.css';
            $js = 'styles/be/main.min.js';
        }
        elseif($current_theme == 'x')
        {
            $style = 'styles/x/main.css';
            $js = 'styles/x/main.min.js';

            // Fix for imagesLoaded conflict in themex
            wp_enqueue_script('imageloaded', 'https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js', array('x-site-body','jquery'), null, true);
        }
        elseif($current_theme == 'pro')
        {
            $style = 'styles/pro/main.css';
            $js = 'styles/pro/main.min.js';

            // Fix for imagesLoaded conflict in themex
            wp_enqueue_script('imageloaded', 'https://unpkg.com/imagesloaded@4/imagesloaded.pkgd.min.js', array('x-site-body','jquery'), null, true);
        }
        elseif($current_theme == 'genesis')
        {
            $style = 'styles/genesis/main.css';
            $js = 'styles/genesis/main.min.js';

            // Enable shortcodes for a text widget
            add_filter('widget_text', 'do_shortcode');
        }
        elseif($current_theme == 'Divi')
        {
            $style = 'styles/divi/main.css';
            $js = 'styles/divi/main.min.js';
        }
        elseif($current_theme == 'houzez')
        {
            $style = 'styles/houzez/main.css';
            $js = 'styles/houzez/main.min.js';

            wp_enqueue_script('chart', 'https://cdnjs.cloudflare.com/ajax/libs/Chart.js/2.7.2/Chart.min.js',array(), null, true);
        }
        
        if(!is_null($style)) wpl_extensions::import_style((object) array('param1'=>'wpl_theme_compatibility_style', 'param2'=>$style));
        if(!is_null($js)) wpl_extensions::import_javascript((object) array('param1'=>'wpl_theme_compatibility_js', 'param2'=>$js));
    }

    public function backend_body_class($classes)
    {
        if(is_rtl()) $classes .= ' wpl_rtl';
        return $classes;
    }
}