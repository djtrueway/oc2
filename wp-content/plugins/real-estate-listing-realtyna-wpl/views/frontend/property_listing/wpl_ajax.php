<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.locations');

class wpl_property_listing_controller extends wpl_controller
{
    public function display()
    {
        $function = wpl_request::getVar('wpl_function');

        if($function == 'get_locations')
        {
            $location_level = wpl_request::getVar('location_level');
            $parent = wpl_request::getVar('parent');
            $current_location_id = wpl_request::getVar('current_location_id');
            $widget_id = wpl_request::getVar('widget_id');

            $this->get_locations($location_level, $parent, $current_location_id, $widget_id);
        }
        elseif($function == 'locationtextsearch_autocomplete')
        {
            $term = wpl_request::getVar('term');
            $this->locationtextsearch_autocomplete($term);
        }
        elseif($function == 'advanced_locationtextsearch_autocomplete')
        {
            $term = wpl_request::getVar('term');
            $this->advanced_locationtextsearch_autocomplete($term);
        }
        elseif($function == 'contact_listing_user' or $function == 'contact_agent')
        {
            $this->contact_listing_user();
        }
        elseif($function == 'set_pcc')
        {
            $this->set_pcc();
        }
        elseif($function == 'refresh_searchwidget_counter')
        {
            $this->refresh_searchwidget_counter();
        }
        elseif($function == 'get_total_results')
        {
            $this->get_total_results();
        }
        elseif($function == 'estimate_price')
        {
            $this->estimate_price();
        }
    }

    private function get_locations($location_level = '', $parent = '', $current_location_id = '', $widget_id)
    {
        $location_settings = wpl_global::get_settings('3'); # location settings

        if($location_settings['zipcode_parent_level'] == $location_level - 1)
        {
            $location_level = 'zips';
        }

        $location_data = wpl_locations::get_locations($location_level, $parent, ($location_level == '1' ? 1 : ''), '', '`name` ASC', '');

        $res = count($location_data) ? 1 : 0;
        $message = $res ? __('Fetched.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
        $name_id = $location_level != 'zips' ? 'sf' . $widget_id . '_select_location' . $location_level . '_id' : 'sf' . $widget_id . '_select_zip_id';

        $html = '<select name="' . $name_id . '" id="' . $name_id . '"';

        if($location_level != 'zips')
            $html .='onchange="wpl' . $widget_id . '_search_widget_load_location(\'' . $location_level . '\', this.value, \'' . $current_location_id . '\');"';

        $html .= '>';
        $html .= '<option value="-1">' . __((trim($location_settings['location'.$location_level.'_keyword']) != '' ? $location_settings['location'.$location_level.'_keyword'] : 'Select'), 'real-estate-listing-realtyna-wpl') . '</option>';

        foreach($location_data as $location)
        {
            $html .= '<option value="' . $location->id . '" ' . ($current_location_id == $location->id ? 'selected="selected"' : '') . '>' . __($location->name, 'real-estate-listing-realtyna-wpl') . '</option>';
        }

        $html .= '</select>';

        $response = array('success' => $res, 'message' => $message, 'data' => $location_data, 'html' => $html, 'keyword' => __($location_settings['location' . $location_level . '_keyword'], 'real-estate-listing-realtyna-wpl'));
        $this->response($response);
    }

    private function locationtextsearch_autocomplete($term)
    {
        $limit = 10;

        if(wpl_global::check_multilingual_status())
        {
            $location_text = wpl_addon_pro::get_column_lang_name('location_text', wpl_global::get_current_language(), false);
            $query = "SELECT `{$location_text}` AS name, COUNT(1) AS `count` FROM `#__wpl_properties` WHERE `{$location_text}` LIKE '" . $term . "%' GROUP BY `{$location_text}` ORDER BY `count` DESC LIMIT " . $limit;
            $results = wpl_db::select($query, 'loadAssocList');
        }
        else
        {
            $query = "SELECT `count`, `location_text` AS name FROM `#__wpl_locationtextsearch` WHERE `location_text` LIKE '" . $term . "%' ORDER BY `count` DESC LIMIT " . $limit;
            $results = wpl_db::select($query, 'loadAssocList');
        }

        $output = array();
        foreach($results as $result)
        {
            $name = preg_replace("/\s,/", '', $result['name']);
            $output[] = array('label' => $name, 'value' => $name);
        }

        $this->response($output);
    }

    private function advanced_locationtextsearch_autocomplete($term)
    {
        $settings = wpl_settings::get_settings(3);
        $street = 'field_42';
        $location2 = 'location2_name';
        $location3 = 'location3_name';
        $location4 = 'location4_name';
        $location5 = 'location5_name';

        if(wpl_global::check_multilingual_status() and wpl_addon_pro::get_multiligual_status_by_column($street, 0)) $street = wpl_addon_pro::get_column_lang_name($street, wpl_global::get_current_language(), false);

        $limit = 5;
        $output = array();
        $condition = "`finalized` = 1 AND `confirmed` = 1 AND `deleted` = 0 AND `expired` = 0";
        $queries = array(
            $street => __('Street', 'real-estate-listing-realtyna-wpl'),
            $location2 => __($settings['location2_keyword'], 'real-estate-listing-realtyna-wpl'),
            $location3 => __($settings['location3_keyword'], 'real-estate-listing-realtyna-wpl'),
            $location4 => __($settings['location4_keyword'], 'real-estate-listing-realtyna-wpl'),
            $location5 => __($settings['location5_keyword'], 'real-estate-listing-realtyna-wpl'),
            'location_text' => __('Address', 'real-estate-listing-realtyna-wpl'),
            'zip_name' => __($settings['locationzips_keyword'], 'real-estate-listing-realtyna-wpl'),
            'mls_id' => __('Listing ID', 'real-estate-listing-realtyna-wpl')
        );

        foreach($queries as $column => $title)
        {
            $query = "SELECT `{$column}` AS `name`, COUNT(`{$column}`) AS `count` FROM `#__wpl_properties` WHERE $condition AND (`{$column}` LIKE '" . $term . "%' OR `{$column}` LIKE '% " . $term . "%') GROUP BY `{$column}` ORDER BY `{$column}` LIMIT " . $limit;
            $results = wpl_db::select($query, 'loadAssocList');

            foreach($results as $result)
            {
                $output[] = array('label' => $result['name'].' ('.$result['count'].')', 'title' => $title, 'column' => $column, 'value' => $result['name']);
            }
        }

        $output[] = array('label' => $term, 'title' => __('Keyword', 'real-estate-listing-realtyna-wpl'), 'column' => '', 'value' => $term);

        $this->response($output);
    }

    private function contact_listing_user()
    {
        $fullname = wpl_request::getVar('fullname', '');
        $phone = wpl_request::getVar('phone', '');
        $email = wpl_request::getVar('email', '');
        $message = wpl_request::getVar('message', '');
        $property_id = wpl_request::getVar('pid', '');
        $gre = wpl_request::getVar('g-recaptcha-response', '');

        // check recaptcha 
        $gre_response = wpl_global::verify_google_recaptcha($gre, 'gre_listing_contact_activity');

        // For integrating third party plugins such as captcha plugins
        apply_filters('preprocess_comment', array());

        $returnData = array();
        if(!filter_var($email, FILTER_VALIDATE_EMAIL))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('Your email is not a valid email!', 'real-estate-listing-realtyna-wpl');
        }
        elseif(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_listing_contact_form'))
        {
            $returnData['success'] = 0;
            $returnData['message'] = __('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl');
        }
        elseif($gre_response['success'] === 0)
        {
            $returnData['success'] = 0;
            $returnData['message'] = $gre_response['message'];
        }
        else
        {
            $parameters = array(
                'fullname' => $fullname,
                'phone' => $phone,
                'email' => $email,
                'message' => $message,
                'property_id' => $property_id,
                'user_id' => wpl_property::get_property_user($property_id)
            );

            wpl_events::trigger('contact_agent', $parameters);

            $returnData['success'] = 1;
            $returnData['message'] = __('Information sent to agent.', 'real-estate-listing-realtyna-wpl');

            // Adding in items with type contact stat
            wpl_property::add_property_stats_item($property_id, 'contact_numb');
        }

        $this->response($returnData);
    }

    private function set_pcc()
    {
        $pcc = wpl_request::getVar('pcc', '');

        setcookie('wplpcc', $pcc, time()+(86400*30), '/');
        wpl_request::setVar('wplpcc', $pcc, 'COOKIE');

        $this->response(array('success'=>1));
    }

    private function refresh_searchwidget_counter()
    {
        $current_user_id = wpl_users::get_cur_user_id();
        $saved_searches_count = 0;
        $favorites_count = 0;
        
        if(wpl_global::check_addon('pro'))
        {
            _wpl_import('libraries.addon_pro');

            if($current_user_id)
                $favorites = wpl_addon_pro::favorite_get_pids(false, $current_user_id);
            else
                $favorites = wpl_addon_pro::favorite_get_pids(true);

            $favorites_count = count($favorites);
        }

        if(wpl_global::check_addon('save_searches') and $current_user_id)
        {
            _wpl_import('libraries.addon_save_searches');

            $save_searches = new wpl_addon_save_searches();
            $save_searches = $save_searches->get('', $current_user_id);
            $saved_searches_count = count($save_searches);
        }

        $this->response(array('saved_searches' => $saved_searches_count, 'favorites' => $favorites_count));
    }
    
    private function get_total_results()
    {
        // Kind
		$kind = wpl_request::getVar('kind', 0);
        $table = ($kind == 2) ? '#__wpl_users' : '#__wpl_properties';
        $default = ($kind == 2) ? array('sf_tmin_id'=>1, 'sf_select_access_public_profile'=>1, 'sf_select_expired'=>0) : array('sf_select_confirmed'=>1, 'sf_select_finalized'=>1, 'sf_select_deleted'=>0, 'sf_select_expired'=>0, 'sf_select_kind'=>$kind);
        
        // WHERE statement
        $vars = array_merge(wpl_request::get('POST'), wpl_request::get('GET'));
        $where = wpl_db::create_query(array_merge($vars, $default));
       
        $query = "SELECT COUNT(`id`) FROM `{$table}` WHERE 1 ".$where;
        $total = wpl_db::select($query, 'loadResult');

        $this->response(array('success'=>1, 'total'=>$total));
    }

    private function estimate_price()
    {
        $data = array();
        $listings = wpl_global::get_listings();
        foreach($listings as $listing) $data[$listing['id']] = array('count' => 'N/A', 'price' => 'N/A');

        $where = array();
        $where['sf_locationtextsearch'] = wpl_request::getVar('sf_locationtextsearch', '');
        $where['sf_select_property_type'] = wpl_request::getVar('sf_select_property_type', '');
        $where['sf_select_bedrooms'] = wpl_request::getVar('sf_select_bedrooms', '');
        $where['sf_select_bathrooms'] = wpl_request::getVar('sf_select_bathrooms', '');
        $where['sf_select_living_area'] = wpl_request::getVar('sf_select_living_area', '');
        $where['sf_select_lot_area'] = wpl_request::getVar('sf_select_lot_area', '');
        $where['sf_select_build_year'] = wpl_request::getVar('sf_select_build_year', '');
        $where = wpl_db::create_query($where);

        $results = wpl_db::select("SELECT `listing`, COUNT(1) AS `count`, AVG(`price`) AS `price` FROM `#__wpl_properties` WHERE 1 $where GROUP BY `listing`", 'loadObjectList');
        foreach($results as $result)
        {
            $data[$result->listing]['count'] = $result->count;
            $data[$result->listing]['price'] = wpl_render::render_price($result->price);
        }

        $this->response(array('success'=>1, 'data'=>$data));
    }
}