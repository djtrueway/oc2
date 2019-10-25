<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

class wpl_io_cmd_get_locations extends wpl_io_cmd_base
{
    protected $built;

    /**
     * Building the command
     * @author Steve A. <steve@realtyna.com>
     * @return mixed
     */
    public function build()
    {
        $level = isset($this->params['level']) ? $this->params['level'] : 1;
        $parent = $this->params['parent'];

        $this->built['locations'] = wpl_locations::get_locations($level, $parent, '');
        
        return $this->built;
    }

    /**
     * Validation params
     * @author Steve A. <steve@realtyna.com>
     * @return bool
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