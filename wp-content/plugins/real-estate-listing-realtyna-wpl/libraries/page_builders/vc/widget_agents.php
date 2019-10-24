<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('widgets.agents.main');

/**
 * Agents Widget Shortcode for VC
 * @author Howard <howard@realtyna.com>
 * @package WPL Core
 */
class wpl_page_builders_vc_widget_agents
{
    public $settings;

    public function __construct()
    {
        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
        
        // VC Widget Shortcode
        add_shortcode('wpl_vc_agents_widget', array($this, 'shortcode_callback'));
        
        vc_map(array
        (
            'name' => __('WPL Agents Widget', 'real-estate-listing-realtyna-wpl'),
            //'custom_markup' => '<strong>'.__('WPL Agents Widget', 'real-estate-listing-realtyna-wpl').'</strong>',
            'description' => __('WPL Agents Widget', 'real-estate-listing-realtyna-wpl'),
            'base' => 'wpl_vc_agents_widget',
            'class' => '',
            'controls' => 'full',
            'icon' => 'wpb-wpl-icon',
            'category' => __('WPL', 'real-estate-listing-realtyna-wpl'),
            'params' => $this->get_fields()
        ));
	}
    
    public function get_fields()
    {
        $agents = new wpl_agents_widget();
        
        // Module Fields
        $fields = array();
        
        $fields[] = array(
            'heading'         => esc_html__('Title', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'title',
            'value'           => '',
            'admin_label'     => true,
            'description'     => esc_html__('The widget title', 'real-estate-listing-realtyna-wpl'),
        );
        
        $widget_layouts = $agents->get_layouts('agents');
        
        $widget_layouts_options = array();
        foreach($widget_layouts as $widget_layout) $widget_layouts_options[esc_html__(ucfirst(str_replace('.php', '', $widget_layout)), 'real-estate-listing-realtyna-wpl')] = str_replace('.php', '', $widget_layout);
        
        $fields[] = array(
            'heading'         => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'tpl',
            'value'           => $widget_layouts_options,
            'std'             => '',
            'description'     => esc_html__('Layout of the widget', 'real-estate-listing-realtyna-wpl'),
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Style', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'style',
            'value'           => array(
                esc_html__('Horizontal', 'real-estate-listing-realtyna-wpl') => '1',
                esc_html__('Vertical', 'real-estate-listing-realtyna-wpl') => '2',
            ),
            'std'             => '',
        );
        
        $pages = wpl_global::get_wp_pages();
        
        $pages_options = array();
        $pages_options['-----'] = '';
        
        foreach($pages as $page) $pages_options[esc_html__($page->post_title, 'real-estate-listing-realtyna-wpl')] = $page->ID;
        
        $fields[] = array(
            'heading'         => esc_html__('Target Page', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'value'           => $pages_options,
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'wpltarget',
            'std'             => '',
        );
        
        $fields[] = array(
            'heading'         => esc_html__('CSS Class', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'css_class',
            'value'           => '',
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Image Width', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'image_width',
            'value'           => '230',
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Image Height', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'image_height',
            'value'           => '230',
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Mailto Status', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'mailto_status',
            'value'           => array(
                esc_html__('No', 'real-estate-listing-realtyna-wpl') => '0',
                esc_html__('Yes', 'real-estate-listing-realtyna-wpl') => '1',
            ),
            'std'             => '',
        );
        
        if(wpl_global::check_addon('pro'))
        {
            $membership_types = wpl_users::get_user_types();
        
            $membership_types_options = array();
            $membership_types_options['-----'] = '';

            foreach($membership_types as $membership_type) $membership_types_options[esc_html__($membership_type->name, 'real-estate-listing-realtyna-wpl')] = $membership_type->id;

            $fields[] = array(
                'heading'         => esc_html__('User Type', 'real-estate-listing-realtyna-wpl'),
                'type'            => 'dropdown',
                'value'           => $membership_types_options,
                'holder'          => 'div',
                'class'           => '',
                'param_name'      => 'user_type',
                'std'             => '',
                'admin_label'     => true,
            );
            
            $memberships = wpl_users::get_wpl_memberships();
        
            $memberships_options = array();
            $memberships_options['-----'] = '';

            foreach($memberships as $membership) $memberships_options[esc_html__($membership->membership_name, 'real-estate-listing-realtyna-wpl')] = $membership->id;

            $fields[] = array(
                'heading'         => esc_html__('Membership', 'real-estate-listing-realtyna-wpl'),
                'type'            => 'dropdown',
                'value'           => $memberships_options,
                'holder'          => 'div',
                'class'           => '',
                'param_name'      => 'membership',
                'std'             => '',
            );
        }
        
        $fields[] = array(
            'heading'         => esc_html__('User IDs', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'user_ids',
            'value'           => '',
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Random', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'random',
            'value'           => array(
                esc_html__('No', 'real-estate-listing-realtyna-wpl') => '0',
                esc_html__('Yes', 'real-estate-listing-realtyna-wpl') => '1',
            ),
            'std'             => '',
        );
        
        $sorts = wpl_sort_options::render(wpl_sort_options::get_sort_options(2));
        
        $sorts_options = array();
        foreach($sorts as $sort) $sorts_options[esc_html__($sort['name'], 'real-estate-listing-realtyna-wpl')] = $sort['field_name'];
        
        $fields[] = array(
            'heading'         => esc_html__('Order By', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'orderby',
            'value'           => $sorts_options,
            'std'             => ''
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Order', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'dropdown',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'order',
            'value'           => array(
                esc_html__('Ascending', 'real-estate-listing-realtyna-wpl') => 'ASC',
                esc_html__('Descending', 'real-estate-listing-realtyna-wpl') => 'DESC',
            ),
            'std'             => '',
        );
        
        $fields[] = array(
            'heading'         => esc_html__('Limit', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'textfield',
            'holder'          => 'div',
            'class'           => '',
            'param_name'      => 'limit',
            'value'           => '6',
        );
        
		return $fields;
	}
    
    public function shortcode_callback($atts)
    {
        ob_start();
        
        $agents = new wpl_agents_widget();
        $agents->widget(array(
            'before_widget'=>'',
            'after_widget'=>'',
            'before_title'=>'',
            'after_title'=>'',
        ),
        array
        (
            'title'=>isset($atts['title']) ? $atts['title'] : '',
            'layout'=>isset($atts['tpl']) ? $atts['tpl'] : '',
            'wpltarget'=>isset($atts['wpltarget']) ? $atts['wpltarget'] : '',
            'data'=>array(
                'style'=>isset($atts['style']) ? $atts['style'] : 1,
                'css_class'=>isset($atts['css_class']) ? $atts['css_class'] : '',
                'image_width'=>isset($atts['image_width']) ? $atts['image_width'] : 230,
                'image_height'=>isset($atts['image_height']) ? $atts['image_height'] : 230,
                'mailto_status'=>isset($atts['mailto_status']) ? $atts['mailto_status'] : '',
                'user_type'=>isset($atts['user_type']) ? $atts['user_type'] : NULL,
                'membership'=>isset($atts['membership']) ? $atts['membership'] : NULL,
                'user_ids'=>isset($atts['user_ids']) ? $atts['user_ids'] : '',
                'random'=>isset($atts['random']) ? $atts['random'] : '',
                'orderby'=>isset($atts['orderby']) ? $atts['orderby'] : '',
                'order'=>isset($atts['order']) ? $atts['order'] : '',
                'limit'=>isset($atts['limit']) ? $atts['limit'] : 6,
            )
        ));
        
        return ob_get_clean();
    }
}