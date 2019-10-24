<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Profile Listing Shortcode for Divi Builder
 * @author Howard <howard@realtyna.com>
 * @package WPL PRO
 */
class wpl_page_builders_divi_profile_listing extends ET_Builder_Module
{
    public $slug       = 'et_pb_wpl_profile_listing';
    public $vb_support = 'on';

    public function init()
    {
        $this->name = __('Profile/Agent Listing', 'real-estate-listing-realtyna-wpl');
        $this->slug = 'et_pb_wpl_profile_listing';

		$this->fields_defaults = array();

        // Global WPL Settings
		$this->settings = wpl_global::get_settings();
	}

    public function get_fields()
    {
        // Module Fields
        $fields = array();

        $profile_listing_layouts = wpl_global::get_layouts('profile_listing', array('message.php'), 'frontend');

        $profile_listing_layouts_options = array();
        foreach($profile_listing_layouts as $profile_listing_layout) $profile_listing_layouts_options[$profile_listing_layout] = esc_html__($profile_listing_layout, 'real-estate-listing-realtyna-wpl');

        $fields['tpl'] = array(
            'label'           => esc_html__('Layout', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $profile_listing_layouts_options,
            'description'     => esc_html__('Layout of the page', 'real-estate-listing-realtyna-wpl'),
        );

        $user_types = wpl_users::get_user_types();

        $user_types_options = array();
        $user_types_options[''] = '-----';

        foreach($user_types as $user_type) $user_types_options[$user_type->id] = esc_html__($user_type->name, 'real-estate-listing-realtyna-wpl');

        $fields['sf_select_membership_type'] = array(
            'label'           => esc_html__('User Type', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $user_types_options,
            'description'     => esc_html__('You can select different user type for filtering the users', 'real-estate-listing-realtyna-wpl'),
        );

        $memberships = wpl_users::get_wpl_memberships();

        $memberships_options = array();
        $memberships_options[''] = '-----';

        foreach($memberships as $membership) $memberships_options[$membership->id] = esc_html__($membership->membership_name, 'real-estate-listing-realtyna-wpl');

        $fields['sf_select_membership_id'] = array(
            'label'           => esc_html__('Membership', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $memberships_options,
            'description'     => esc_html__('You can filter the users by their membership package', 'real-estate-listing-realtyna-wpl'),
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

        $page_sizes = explode(',', trim($this->settings['page_sizes'], ', '));

        $page_sizes_options = array();
        foreach($page_sizes as $page_size) $page_sizes_options[$page_size] = esc_html__($page_size, 'real-estate-listing-realtyna-wpl');

        $fields['limit'] = array(
            'label'           => esc_html__('Page Size', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $page_sizes_options,
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

        $sorts = wpl_sort_options::render(wpl_sort_options::get_sort_options(2, 1));

        $sorts_options = array();
        foreach($sorts as $sort) $sorts_options[$sort['field_name']] = esc_html__($sort['name'], 'real-estate-listing-realtyna-wpl');

        $fields['orderby'] = array(
            'label'           => esc_html__('Order By', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => $sorts_options,
        );

        $fields['order'] = array(
            'label'           => esc_html__('Order', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => array(
                'ASC' => esc_html__('Ascending', 'real-estate-listing-realtyna-wpl'),
                'DESC' => esc_html__('Descending', 'real-estate-listing-realtyna-wpl'),
            ),
        );

        $fields['wplcolumns'] = array(
            'label'           => esc_html__('Columns Count', 'real-estate-listing-realtyna-wpl'),
            'type'            => 'select',
            'option_category' => 'basic_option',
            'options'         => array(
                '3' => 3,
                '1' => 1,
                '2' => 2,
                '4' => 4,
                '6' => 6,
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

        return do_shortcode('[wpl_profile_listing'.(trim($shortcode_atts) ? ' '.trim($shortcode_atts) : '').']');
    }
}