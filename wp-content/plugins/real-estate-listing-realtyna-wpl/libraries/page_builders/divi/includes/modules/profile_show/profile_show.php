<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Profile Show Shortcode for Divi Builder
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_divi_profile_show extends ET_Builder_Module
{
    public $slug       = 'et_pb_wpl_profile_show';
    public $vb_support = 'on';

    public function init()
    {
        $this->name = __('Profile/Agent Show', 'real-estate-listing-realtyna-wpl');
        $this->slug = 'et_pb_wpl_profile_show';
		$this->fields_defaults = array();

        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
	}

    public function get_fields()
    {
        // Module Fields
        $fields = array();

        $profile_show_layouts = wpl_global::get_layouts('profile_show', array('message.php'), 'frontend');

        $profile_show_layouts_options = array();
        foreach($profile_show_layouts as $profile_show_layout) $profile_show_layouts_options[$profile_show_layout] = esc_html__($profile_show_layout, 'real-estate-listing-realtyna-wpl');

        $fields['tpl'] = array(
            'label'           => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $profile_show_layouts_options,
            'description'     => esc_html__('Layout of the page', 'real-estate-listing-realtyna-wpl'),
        );

        $wpl_users = wpl_users::get_wpl_users();

        $wpl_users_options = array();
        foreach($wpl_users as $wpl_user) $wpl_users_options[$wpl_user->ID] = esc_html__($wpl_user->user_login.((trim($wpl_user->first_name) != '' or trim($wpl_user->last_name) != '') ? ' ('.$wpl_user->first_name.' '.$wpl_user->last_name.')' : ''), 'real-estate-listing-realtyna-wpl');

        $fields['sf_select_user_id'] = array(
            'label'           => esc_html__('User', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $wpl_users_options,
            'description'     => esc_html__('The agent to show', 'real-estate-listing-realtyna-wpl'),
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

        $fields['wplpagination'] = array(
            'label'           => esc_html__('Pagination', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => array(
                '' => '-----',
                'scroll' => esc_html__('Scroll Pagination', 'real-estate-listing-realtyna-wpl'),
            ),
        );

		return $fields;
	}

    public function render($atts, $content = NULL, $function_name = NULL)
    {
        $shortcode_atts = '';
        foreach($atts as $key=>$value)
        {
            if(trim($value) == '' or $value == '-1') continue;
            if($key == 'tpl' and $value == 'default') continue;

            $shortcode_atts .= $key.'="'.$value.'" ';
        }

        return do_shortcode('[wpl_profile_show'.(trim($shortcode_atts) ? ' '.trim($shortcode_atts) : '').']');
    }
}