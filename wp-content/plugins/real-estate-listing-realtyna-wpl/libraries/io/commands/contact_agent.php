<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Contact agent command
 * @author Chris <chris@realtyna.com>
 * @since WPL2.7.0
 * @package WPL
 * @date 2015/08/31
 */
class wpl_io_cmd_contact_agent extends wpl_io_cmd_base
{
    protected $built = array();

    /**
     * This method is the main method of each commands
     * @author Chris <chris@realtyna.com>
     * @return mixed
     */
    public function build()
    {
        $params = array(
            'fullname' => $this->params['fullname'],
            'phone' => $this->params['phone'],
            'email' => $this->params['email'],
            'message' => $this->params['message'],
            'id' => $this->params['id'],
            'user_id' => $this->params['user_id']
        );
        
        wpl_events::trigger('contact_agent', $params);
        return $this->built['contact_agent'] = array('status'=>'success');
    }

    /**
     * Data validation
     * @author Chris <chris@realtyna.com>
     * @return boolean
     */
    public function validate()
    {
        if(isset($this->params['fullname']) == false ||  $this->params['fullname'] == '') return false;
        if(isset($this->params['phone']) == false ||  $this->params['phone'] == '') return false;
        if(isset($this->params['email']) == false ||  $this->params['email'] == '') return false;
        if(isset($this->params['message']) == false ||  $this->params['message'] == '') return false;
        if(isset($this->params['id']) == false ||  $this->params['id'] == '') return false;
        if(isset($this->params['user_id']) == false ||  $this->params['user_id'] == '') return false;
        
        return true;
    }
}