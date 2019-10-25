<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

class wpl_property_show_controller extends wpl_controller
{
	public function display()
	{
		$function = wpl_request::getVar('wpl_function');

        if($function == 'dpr_closed') $this->dpr_closed();
	}
    
    public function dpr_closed()
    {
        setcookie('wpl_dpr_closed', 1, time()+(86400*1), '/');
        wpl_request::setVar('wpl_dpr_closed', 1, 'COOKIE');
        
        // Set the session
        wpl_session::set('wpl_dpr_popup', 0);
        
        $this->response(array('success'=>1));
    }
}