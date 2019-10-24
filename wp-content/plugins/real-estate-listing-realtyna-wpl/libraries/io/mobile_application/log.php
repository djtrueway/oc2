<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

class wpl_io_cmd_log extends wpl_io_cmd_base
{
    protected $built;
    public $logs_folder = 'logs';
	
    public function build()
    {
        $log_file = $this->get_logs_path().date('Y-m-d_H-i-s').'.txt';
        $vars = wpl_request::get('POST');
        
        $log_str = '';
        foreach($vars as $key=>$value) $log_str .= $key.' = '.$value.'\n';
        
        wpl_file::write($log_file, $log_str);
        
        $this->built = array('success'=>1, 'message'=>__('Logs Saved.', 'real-estate-listing-realtyna-wpl'));
        return $this->built;
    }
    
    public function get_logs_path()
	{
        return WPL_ABSPATH . 'libraries' . DS . 'io' . DS . $this->logs_folder . DS;
	}
    
    /**
     * Data validation
     * @return boolean
     */
    public function validate()
    {
        if(wpl_global::check_addon('mobile_application') == false)
        {
            $this->error = "Add-on mobile application is not installed";
            return false;
        }
        
        return true;
    }
}