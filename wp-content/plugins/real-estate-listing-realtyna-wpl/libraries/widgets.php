<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * WPL widget library
 * @author Howard <howard@realtyna.com>
 * @since WPL1.0.0
 * @date 21/07/2013
 * @package WPL
 */
class wpl_widget extends WP_Widget
{
    /**
     * 
     * @var array
     */
	public $data;
    public $instance;
    public $css_class;
    public $settings;
    public $widget_id;
    public $widget_uq_name; # widget unique name
	
    /**
     * Constructor method
     * @author Howard <howard@realtyna.com>
     * @param int $widget_id
     * @param string $widget_name
     * @param array $options
     */
	public function __construct($widget_id = null, $widget_name = '', $options = array())
	{
		parent::__construct($widget_id, $widget_name, $options);
	}
    
    /**
     * Get The List of Layouts in the Widget
     * @author Howard <howard@realtyna.com>
     * @static
     * @param string $widget_name
     * @return array
     */
	public static function get_layouts($widget_name)
	{
		$path = WPL_ABSPATH. 'widgets' .DS. $widget_name .DS. 'tmpl';
		return wpl_folder::files($path, '.php', false, false);
	}
    
    /**
     * List the layouts in <option> fields
     * @author Howard <howard@realtyna.com>
     * @param string $widget_name
     * @param array $instance
     * @return string
     */
	public function generate_layouts_selectbox($widget_name, $instance)
	{
		// Base Layouts
		$layouts = self::get_layouts($widget_name);
		$i = 0;
		$output = '';
        
		while($i < count($layouts))
		{
			$output .= '<option ';
			if(str_replace('.php', '', $layouts[$i]) == $instance['layout']) $output .= 'selected="selected" ';
			$output .= 'value="'.str_replace('.php', '', $layouts[$i]).'"';
			$output .= '>';
			$output .= str_replace('.php', '', $layouts[$i]);
			$output .= '</option>';
		    $i++;
		}
		
		return $output;
	}
    
    /**
     * List the layouts in <option> fields
     * @author Howard <howard@realtyna.com>
     * @deprecated since version 2.4.0
     * @param array $instance
     * @param string $field_name
     * @return string
     */
	public function generate_pages_selectbox($instance, $field_name = 'wpltarget')
	{
        return wpl_global::generate_pages_selectbox((isset($instance[$field_name]) ? $instance[$field_name] :  NULL));
	}
    
    /**
     * Load Registered Widget with Shortcode
     * @author Howard <howard@realtyna.com>
     * @param array $atts
     * @return string
     */
	public function load_widget_instance($atts)
	{
        $id = 0;
		extract(shortcode_atts(array('id'=>''), $atts));
        
	    ob_start();
		wpl_widget::widget_instance($id);
		$output = ob_get_contents();
	    ob_end_clean();
        
	    return $output;
    }
    
    /**
     * loads widget instance
     * @author Howard <howard@realtyna.com>
     * @param int $widget_id
     * @return void
     */
	public function widget_instance($widget_id)
	{
        $wp_registered_widgets = self::get_registered_widgets();
        
	    // validation
	    if(!array_key_exists($widget_id, $wp_registered_widgets))
		{
			echo 'No widget found with id = '.$widget_id; 
			return;
	    }
		
		$params = array_merge(array(array_merge(array('widget_id'=>$widget_id, 'widget_name'=>$wp_registered_widgets[$widget_id]['name']))), (array) $wp_registered_widgets[$widget_id]['params']);
  
	    $callback = $wp_registered_widgets[$widget_id]['callback'];
		if(is_callable($callback)) call_user_func_array($callback, $params);
	}
	
    /**
     * Get Widgets Instance For Listing in Shortcode Wizard
     * @author Howard <howard@realtyna.com>
     * @static
     * @return array
     */
	public static function get_existing_widgets()
	{
		$sidebar_widgets = wp_get_sidebars_widgets();
	    $widgets_with_title = array();
        
	    foreach($sidebar_widgets as $sidebar=>$widgets)
		{
            if(!is_array($widgets)) continue;
            
		    $widgets_with_title[$sidebar] = array();
		    foreach($widgets as $widget_id) array_push($widgets_with_title[$sidebar], array('id'=>$widget_id));
        }
		
		return $widgets_with_title;
	}
    
    /**
     * Get registered widgets
     * @author Howard <howard@realtyna.com>
     * @static
     * @global array $wp_registered_widgets
     * @return array
     */
	public static function get_registered_widgets()
	{
		global $wp_registered_widgets;
        return $wp_registered_widgets;
	}
	
	/**
     * for importing internal files in object mode
     * @author Howard <howard@realtyna.com>
     * @param string $include
     * @param boolean $override
     * @param boolean $set_footer
     * @param boolean $once
     * @return void
     */
    protected function _wpl_import($include, $override = true, $set_footer = false, $once = false)
    {
        $path = _wpl_import($include, $override, true);

        /** check exists **/
        if(!wpl_file::exists($path)) return;
        
        if(!$set_footer)
        {
            if(!$once) include $path;
            else include_once $path;
        }
        else
        {
            ob_start();
            
            if(!$once) include $path;
            else include_once $path;
            
            wpl_html::set_footer(ob_get_clean());
        }
    }
}