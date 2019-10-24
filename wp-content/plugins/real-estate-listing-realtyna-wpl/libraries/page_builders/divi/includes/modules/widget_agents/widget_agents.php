<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('widgets.agents.main');

/**
 * Agents Widget Shortcode for Divi Builder
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_divi_widget_agents extends ET_Builder_Module
{
    public $fields_defaults;
    public $settings;
    public $vb_support = 'on';

    public function init()
    {
        $this->name = __('WPL Agents Widget', 'real-estate-listing-realtyna-wpl');
        $this->slug = 'et_pb_wpl_widget_agents';
		$this->fields_defaults = array('image_width'=>230, 'image_height'=>230);

        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
	}

    public function get_fields()
    {
        $agents = new wpl_agents_widget();

        // Module Fields
        $fields = array();

        $fields['title'] = array(
            'label'           => esc_html__('Title', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'text',
            'option_category' => 'basic_option',
            'description'     => esc_html__('The widget title', 'real-estate-listing-realtyna-wpl'),
        );

        $widget_layouts = $agents->get_layouts('agents');

        $widget_layouts_options = array();
        foreach($widget_layouts as $widget_layout) $widget_layouts_options[str_replace('.php', '', $widget_layout)] = esc_html__(ucfirst(str_replace('.php', '', $widget_layout)), 'real-estate-listing-realtyna-wpl');

        $fields['tpl'] = array(
            'label'           => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $widget_layouts_options,
        );

        $fields['style'] = array(
            'label'           => esc_html__('Style', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => array(
                '1' => esc_html__('Horizontal', 'real-estate-listing-realtyna-wpl'),
                '2' => esc_html__('Vertical', 'real-estate-listing-realtyna-wpl'),
            ),
        );

        $pages = wpl_global::get_wp_pages();

        $pages_options = array();
        $pages_options[''] = '-----';

        foreach($pages as $page) $pages_options[$page->ID] = esc_html__($page->post_title, 'real-estate-listing-realtyna-wpl');

        $fields['wpltarget'] = array(
            'label'           => esc_html__('Target Page', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $pages_options,
        );

        $fields['css_class'] = array(
            'label'           => esc_html__('CSS Class', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'text',
            'option_category' => 'basic_option',
        );

        $fields['image_width'] = array(
            'label'           => esc_html__('Image Width', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'text',
            'option_category' => 'basic_option',
        );

        $fields['image_height'] = array(
            'label'           => esc_html__('Image Height', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'text',
            'option_category' => 'basic_option',
        );

        $fields['mailto_status'] = array(
            'label'           => esc_html__('Mailto Status', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => array(
                '0' => esc_html__('No', 'real-estate-listing-realtyna-wpl'),
                '1' => esc_html__('Yes', 'real-estate-listing-realtyna-wpl'),
            ),
        );

        if(wpl_global::check_addon('pro'))
        {
            $membership_types = wpl_users::get_user_types();

            $membership_types_options = array();
            $membership_types_options[''] = esc_html__('All', 'real-estate-listing-realtyna-wpl');

            foreach($membership_types as $membership_type) $membership_types_options[$membership_type->id] = esc_html__($membership_type->name, 'real-estate-listing-realtyna-wpl');

            $fields['user_type'] = array(
                'label'           => esc_html__('User Type', 'real-estate-listing-realtyna-wpl'),
                'type'            => 'select',
                'option_category' => 'basic_option',
                'options'         => $membership_types_options,
            );

            $memberships = wpl_users::get_wpl_memberships();

            $memberships_options = array();
            $memberships_options[''] = esc_html__('All', 'real-estate-listing-realtyna-wpl');

            foreach($memberships as $membership) $memberships_options[$membership->id] = esc_html__($membership->membership_name, 'real-estate-listing-realtyna-wpl');

            $fields['membership'] = array(
                'label'           => esc_html__('Membership', 'real-estate-listing-realtyna-wpl'),
                'type'            => 'select',
                'option_category' => 'basic_option',
                'options'         => $memberships_options,
            );
        }

        $fields['user_ids'] = array(
            'label'           => esc_html__('User IDs', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'text',
            'option_category' => 'basic_option',
        );

        $fields['random'] = array(
            'label'           => esc_html__('Random', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => array(
                '0' => esc_html__('No', 'real-estate-listing-realtyna-wpl'),
                '1' => esc_html__('Yes', 'real-estate-listing-realtyna-wpl'),
            ),
        );

        $sort_options = wpl_sort_options::render(wpl_sort_options::get_sort_options(2));

        $sort_options_options = array();
        foreach($sort_options as $sort_option) $sort_options_options[urlencode($sort_option['field_name'])] = esc_html__($sort_option['name'], 'real-estate-listing-realtyna-wpl');

        $fields['orderby'] = array(
            'label'           => esc_html__('Order By', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $sort_options_options,
        );

        $fields['order'] = array(
            'label'           => esc_html__('Order', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => array(
                'ASC' => esc_html__('ASC', 'real-estate-listing-realtyna-wpl'),
                'DESC' => esc_html__('DESC', 'real-estate-listing-realtyna-wpl'),
            ),
        );

        $fields['limit'] = array(
            'label'           => esc_html__('Limit', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'text',
            'option_category' => 'basic_option',
        );

		return $fields;
	}

    public function render($atts, $content = NULL, $function_name = NULL)
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