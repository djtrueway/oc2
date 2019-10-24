<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/**
 * Schedule Tour command
 * @author Steve A. <steve@realtyna.com>
 * @since WPL3.3.1
 * @package WPL
 * @date 2017/05/18
 */
class wpl_io_cmd_schedule_tour extends wpl_io_cmd_base
{
    protected $built = array();

    /**
     * This method is the main method of each commands
     * @author Steve A. <steve@realtyna.com>
     * @return mixed
     */
    public function build()
    {
        $params = array(
            'fullname' => $this->params['fullname'],
            'email' => $this->params['email'],
            'tel' => $this->params['tel'],
            'date' => $this->params['date'],
            'time' => $this->params['time'],
            'property_id' => $this->params['id'],
            'user_id' => $this->params['user_id']
        );
        
        wpl_events::trigger('schedule_tour', $params);
        return $this->built['schedule_tour'] = array('status'=>'success');
    }

    /**
     * Data validation
     * @author Steve A. <steve@realtyna.com>
     * @return boolean
     */
    public function validate()
    {
        if(isset($this->params['fullname']) == false ||  $this->params['fullname'] == '') return false;
        if(isset($this->params['email']) == false ||  $this->params['email'] == '') return false;
        if(isset($this->params['tel']) == false ||  $this->params['tel'] == '') return false;
        if(isset($this->params['date']) == false ||  $this->params['date'] == '') return false;
        if(isset($this->params['time']) == false ||  $this->params['time'] == '') return false;
        if(isset($this->params['id']) == false ||  $this->params['id'] == '') return false;
        if(isset($this->params['user_id']) == false ||  $this->params['user_id'] == '') return false;
        
        return true;
    }
}