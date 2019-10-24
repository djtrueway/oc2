<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

class wpl_io_cmd_get_property extends wpl_io_cmd_base
{
    private $pid = 0;
    private $built;

    /**
     * This method is the main method of each commands
     * @return mixed
     */
    public function build()
    {
        $this->pid = $this->params['sf_selectid'] ? $this->params['sf_selectid'] : $this->params['sf_select_id'];

        wpl_property::property_visited($this->pid);
        $result = wpl_property::get_property_raw_data($this->pid);
        $user = wpl_users::get_user($result['user_id']);
        $image = $this->get_profile_image($result['user_id']);
        $area_unit = str_replace('&sup2;', '2', wpl_units::get_unit($result['lot_area_unit'])['name']);

        $this->built = array('property_show_sections'=>array(
            array(
                'section_type'=>'long_text',
                'title'=>'DESCRIPTION',
                'content'=>strip_tags(stripslashes($result['field_308'])),
                'read_more_is_enabled'=>true,
                'number_of_shown_characters'=>500
            ),
            array(
                'section_type'=>'share_content',
                'content'=>$result['field_313'].'\n'.wpl_property::get_property_link($result),
            ),
            array(
                'section_type'=>'string_list',
                'title'=>'FACTS',
                'content'=>array(
                    __('Lot Area', 'real-estate-listing-realtyna-wpl').': '.$result['lot_area'].' '.$area_unit,
                    __('Price', 'real-estate-listing-realtyna-wpl').': '.wpl_render::render_price($result['price'], $result['price_unit']),
                    __('Listing ID', 'real-estate-listing-realtyna-wpl').': '.$result['mls_id'],
                )
            ),
            array(
                'section_type'=>'map_view',
                'content'=>array(
                    'lat'=>$result['googlemap_lt'],
                    'lng'=>$result['googlemap_ln'],
                    'zoom'=>15
                )
            ),
            array(
                'section_type'=>'agent',
                'title'=>'GET_MORE_INFO',
                'content'=>array(
                    array(
                        'agent_name'=>$user->data->display_name,
                        'description_1'=>$user->data->wpl_data->company_name,
                        'description_2'=>$user->data->wpl_data->main_email,
                        'image'=>$image,
                        'is_call_button_enabled'=>true,
                        'call_button_text'=>'CALL',
                        'call_number'=>$user->data->wpl_data->tel ? $user->data->wpl_data->tel : $user->data->wpl_data->mobile,
                        'sms_text'=>'Hello, I need more information about the property ID #'.$result['mls_id'].' at '.$result['location_text']
                    ),
                )
            ),
            array(
                'section_type'=>'form',
                'url'=>array($this->generate_command_url('contact_agent', wpl_request::getVar('public_key'), wpl_request::getVar('private_key'), array('id'=>$result['id'], 'user_id'=>$result['user_id']))),
                'content'=>array(
                    array(
                        'field_type'=>'text',
                        'placeholder'=>'FULL_NAME',
                        'column_name'=>'fullname',
                    ),
                    array(
                        'field_type'=>'number',
                        'placeholder'=>'PHONE_NUMBER',
                        'column_name'=>'phone',
                    ),
                    array(
                        'field_type'=>'email',
                        'placeholder'=>'EMAIL',
                        'column_name'=>'email',
                    ),
                    array(
                        'field_type'=>'textarea',
                        'placeholder'=>'MESSAGE',
                        'column_name'=>'message',
                    ),
                    array(
                        'field_type'=>'button',
                        'placeholder'=>'SUBMIT',
                    ),
                )
            ),
            array(
                'section_type'=>'schedule_tour',
                'title'=>'SCHEDULE_TOUR',
                'url'=>array($this->generate_command_url('schedule_tour', wpl_request::getVar('public_key'), wpl_request::getVar('private_key'), array('id'=>$result['id'], 'user_id'=>$result['user_id'])),
                'content'=>array()
            ),
            array(
                'section_type'=>'schools',
                'title'=>'NEARBY_SCHOOLS',
                'content'=>array()
            ),
            array(
                'section_type'=>'mortgage_calculator',
                'title'=>'MORTGAGE_CALCULATOR',
                'content'=>array(
                        'price'=>$result['price'],
                        'currency'=>'$',
                        'down_payment'=>10,
                        'loan_term'=>30,
                        'interest_rate'=>4.5,
                        'tax'=>0,
                        'insurance'=>0
                )
            )
        )));

        return $this->built;
    }

    /**
     * Data validation
     * @return boolean
     */
    public function validate()
    {
        return true;
    }

    /**
     * Getting agent profile image
     * @param $user_id
     * @return null|string
     */
    private function get_profile_image($user_id)
    {
        $wpl_user = wpl_users::full_render($user_id, wpl_users::get_pshow_fields(), NULL, array(), true);
        $sex = $wpl_user['data']['sex'] == 0 ? 'male' : 'female';

        $params                   = array();
        $params['image_parentid'] = $user_id;
        $params['image_name']     = isset($wpl_user['profile_picture']['name']) ? $wpl_user['profile_picture']['name'] : '_' . $sex . '.png';
        $picture_path             = isset($wpl_user['profile_picture']['path']) ? $wpl_user['profile_picture']['path'] : '';
        
        if(trim($picture_path) == '')
        {
            $picture_path = WPL_ABSPATH. 'assets' .DS. 'img' .DS. 'membership' .DS. $sex .'.jpg';
        }
        
        return wpl_images::create_profile_images($picture_path, 160, 160, $params);
    }
}