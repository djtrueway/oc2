<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.addon_school_info');

class wpl_io_cmd_get_schools extends wpl_io_cmd_base
{
    private $pid;
    private $radius = 5;
    private $limit = 5;
    private $built;

    /**
     * This method is the main method of each commands
     * @return mixed
     */
    public function build()
    {
        $schools = array();
        
        if(wpl_global::check_addon('school_info'))
        {
            $this->pid = $this->params['sf_selectid'] ? $this->params['sf_selectid'] : $this->params['sf_select_id'];
            $property = wpl_property::get_property_raw_data($this->pid);
            $state = wpl_locations::get_location_abbr_by_name($property['location2_name'], 2);
            $school_info = new wpl_addon_school_info($this->radius, $this->limit, $state);
            $schools =  $school_info->nearby_schools($property['googlemap_lt'], $property['googlemap_ln']);
        }
       
        $this->built = array('schools' => $schools);
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
}