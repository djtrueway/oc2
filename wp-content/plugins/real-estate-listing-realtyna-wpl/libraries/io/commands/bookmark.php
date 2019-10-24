<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.addon_save_searches');

/**
 * The bookmark command
 * @author Chris <chris@realtyna.com>
 * @since WPL2.7.0
 * @package WPL
 * @date 2015/08/31
 */
class wpl_io_cmd_bookmark extends wpl_io_cmd_base
{
    protected $built = array();

    /**
     * This method is the main method of each commands
     * @author Chris <chris@realtyna.com>
     * @return mixed
     */
    public function build()
    {
        if(wpl_global::check_addon('pro') == false)
        {
            return $this->built['favorite'] = array('status'=>false);
        }

        if($this->params['function'] == 'toggle_favorite')
        {
            $res = $this->toggle_favorite();
            return $this->built['favorite'] = array('status'=>$res);
        }
        elseif($this->params['function'] == 'remove_favorite')
        {
            $this->remove_favorite();
            return $this->built['favorite'] = array('status'=>true);
        }
        elseif($this->params['function'] == 'add_favorite')
        {
            $this->add_favorite();
            return $this->built['favorite'] = array('status'=>true);
        }
        elseif($this->params['function'] == 'add_savesearch')
        {
            $id = $this->add_savesearch();
            return $this->built['savesearch'] = array('status'=>true, 'id'=>$id);
        }
        elseif($this->params['function'] == 'remove_savesearch')
        {
            $this->remove_savesearch();
            return $this->built['savesearch'] = array('status'=>true);
        }
        
        return $this->built['favorite'] = array('status'=>false);
    }

    /**
     * Data validation
     * @author Chris <chris@realtyna.com>
     * @return boolean
     */
    public function validate()
    {
    	if($this->params['function'] == 'toggle_favorite' or $this->params['function'] == 'add_favorite' or $this->params['function'] == 'remove_favorite')
    	{
    		if(trim($this->params['uid']) == '') return false;
        	if(trim($this->params['pid']) == '') return false;
    	}
    	elseif ($this->params['function'] == 'add_savesearch') 
    	{
    		if(trim($this->params['uid']) == '') return false;
        	if(trim($this->params['searchname']) == '') return false;
        	if(trim($this->params['values']) == '') return false;
    	}
    	elseif ($this->params['function'] == 'remove_savesearch') 
    	{
    		if(trim($this->params['sid']) == '') return false;
    	}
        
        if(trim($this->params['function']) == '') return false;
        
        return true;
    }

    /**
     * Toggle property favorite status
     * @author Steve A. <steve@realtyna.com>
     * @return string     Result
     */
    public function toggle_favorite()
    {
        $res = wpl_addon_pro::favorite_get_pids(false, $this->params['uid']);
        
        if(in_array($this->params['pid'], $res))
        {
            $this->remove_favorite();
            return 'removed';
        }
        else
        {
            $this->add_favorite();
            return 'added';
        }
    }
    
    /**
     * Remove property from favorites
     * @author Chris <chris@realtyna.com>
     * @return void
     */
    public function remove_favorite()
    {
        wpl_addon_pro::favorite_add_remove($this->params['pid'], 'remove', $this->params['uid']);
    }

    /**
     * Add property to favorites
     * @author Chris <chris@realtyna.com>
     * @return void
     */
    public function add_favorite()
    {
        wpl_addon_pro::favorite_add_remove($this->params['pid'], 'add', $this->params['uid']);
    }

    /**
     * Add Saved Search
     * @author Steve A. <steve@realtyna.com>
     * @return integer Saved Search ID
     */
    public function add_savesearch()
    {
    	$values = array();
    	$values['user_id'] = $this->params['uid'];
        $values['name'] = str_replace('%20', ' ', $this->params['searchname']);
        $values['criteria'] = json_decode(base64_decode($this->params['values']), true);

        $savesearch = new wpl_addon_save_searches();
    	return $savesearch->create($values);
    }

    /**
     * Remove Saved Search
     * @author Steve A. <steve@realtyna.com>
     * @return void
     */
    public function remove_savesearch()
    {
    	$savesearch = new wpl_addon_save_searches();
		$savesearch->delete($this->params['sid']);
    }
}
