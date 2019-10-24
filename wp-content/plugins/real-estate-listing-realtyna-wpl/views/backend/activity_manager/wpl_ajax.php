<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.activities');

class wpl_activity_manager_controller extends wpl_controller
{
    public $tpl_path = 'views.backend.activity_manager.tmpl';

    public function display()
    {
        /** check permission * */
        wpl_global::min_access('administrator');
        $function = wpl_request::getVar('wpl_function');
        
        // Check Nonce
        if(!wpl_security::verify_nonce(wpl_request::getVar('_wpnonce', ''), 'wpl_activity_manager')) $this->response(array('success'=>0, 'message'=>__('The security nonce is not valid!', 'real-estate-listing-realtyna-wpl')));
        
        // Create Nonce
        $this->nonce = wpl_security::create_nonce('wpl_activity_manager');
        
        if($function == 'generate_modify_page') $this->generate_modify_page();
        elseif($function == 'sort_activities') $this->sort_activities(wpl_request::getVar('sort_ids'));
        elseif($function == 'set_enabled_activity') $this->set_enabled_activity(wpl_request::getVar('activity_id'), wpl_request::getVar('enabled_status'));
        elseif($function == 'remove_activity') $this->remove_activity(wpl_request::getVar('activity_id'), wpl_request::getVar('wpl_confirmed', 0));
        elseif($function == 'save_activity') $this->save_activity();
        elseif($function == 'load_options') $this->load_options(wpl_request::getVar('activity_name'), wpl_request::getVar('activity_layout'));
    }

    private function sort_activities($sort_ids)
    {
        echo wpl_activity::sort($sort_ids);
        exit;
    }

    private function set_enabled_activity($activity_id, $enabled_status)
    {
        $res = wpl_activity::update_one($activity_id, 'enabled', $enabled_status);
        $message = $res ? __('Operation was successful.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
        
        $response = array('success'=>$res, 'message'=>$message);
        $this->response($response);
    }

    private function remove_activity($activity_id, $confirmed = 0)
    {
        if($confirmed) $res = wpl_activity::remove_activity($activity_id);
        else $res = false;
        
        $message = $res ? __('Activity removed successfully.', 'real-estate-listing-realtyna-wpl') : __('Error Occured.', 'real-estate-listing-realtyna-wpl');
        $data = NULL;

        $response = array('success'=>$res, 'message'=>$message, 'data'=>$data);
        $this->response($response);
    }

    private function generate_modify_page()
    {
        $this->activity_id = wpl_request::getVar('activity_id');
		$activity_name = wpl_request::getVar('activity_name');
		
        if(!$this->activity_id)
        {
            $this->activity_data = new stdClass;
			$this->activity_raw_name = wpl_activity::get_activity_name_layout($activity_name);
        }
        else
        {
            $this->activity_data = wpl_activity::get_activity("AND `id`='".wpl_db::escape($this->activity_id)."'");
            $this->activity_raw_name = wpl_activity::get_activity_name_layout($this->activity_data->activity);
        }
		
		$this->activity_layouts = wpl_activity::get_activity_layout($this->activity_raw_name[0]);
		if(!isset($this->activity_raw_name[1])) $this->activity_raw_name[1] = '';
		
        $this->memberships = wpl_users::get_wpl_memberships();
        $this->options = isset($this->activity_data->params) ? json_decode($this->activity_data->params) : new stdClass;
        parent::render($this->tpl_path, 'internal_modify');
        exit;
    }

    private function save_activity()
    {
        $information = wpl_request::getVar('info');
        $options = wpl_request::getVar('option');
        
        $associations = wpl_request::getVar('associations', '') ? wpl_request::getVar('associations', '') : array();
        $associations_str = '';
        foreach($associations as $page_id=>$value) if($value) $associations_str .= '['.$page_id.']';
        $information['associations'] = $associations_str;
        
        /** validation for association type **/
        if(!isset($information['association_type']) or (isset($information['association_type']) and is_null($information['association_type']))) $information['association_type'] = 1;
        
        $accesses = wpl_request::getVar('accesses', '') ? wpl_request::getVar('accesses', '') : array();
        $accesses_str = '';
        foreach($accesses as $membership_id=>$value) if($value) $accesses_str .= ','.$membership_id;
        $information['accesses'] = ','.trim($accesses_str, ', ').',';
        
        /** validation for access type **/
        if(!isset($information['access_type']) or (isset($information['access_type']) and is_null($information['access_type']))) $information['access_type'] = 2;
        
        if(is_null($options)) $information['params'] = '';
        else $information['params'] = json_encode($options);
        
        if(trim($information['layout']) != '') $information['activity'] = $information['activity'] . ':' . $information['layout'];
        
        if(!isset($information['activity_id'])) wpl_activity::add_activity($information);
        else wpl_activity::update_activity($information);
		
        exit;
	}

    /**
     * load activity options such a layout and options
     * @param string $activity_name Activity name
     * @param string $current_layout current selected layout for this activity
     */
    private function load_options($activity_name, $current_layout)
    {
        $current_activity = wpl_activity::get_activity("AND `activity`='".wpl_db::escape($activity_name)."'");
        $current_activity = wpl_activity::get_activity_name_layout($current_activity->activity);

        $returnData = array();

        $optionPath = wpl_activity::get_activity_option_form($activity_name);
        $returnData['layouts'] = wpl_activity::load_layouts_html($activity_name, $current_layout);
		
        if($optionPath)
        {
            ob_start();
            include $optionPath;
            $returnData['options'] = ob_get_contents();
            ob_end_clean();
        }
        else $returnData['options'] = __("This activity doesn't have options!", 'real-estate-listing-realtyna-wpl');

        $this->response($returnData);
    }
}
