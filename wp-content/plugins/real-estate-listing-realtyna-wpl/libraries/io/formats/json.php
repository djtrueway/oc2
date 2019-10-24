<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

class wpl_io_format_json extends wpl_io_format_base
{
    protected $header = 'content-type: text/json; charset=utf-8';

    public function __construct($cmd, $params)
	{
        $this->init($cmd, $params);
	}
    
    /**
     * @param wpl_io_cmd_base $response
     * @return string
     */
	public function render($response)
	{
		if(version_compare(PHP_VERSION, '5.4.0') >= 0) $rendered = json_encode($response, JSON_UNESCAPED_UNICODE);
        else $rendered = json_encode($this->utf8ize($response));
        return $rendered;
	}

    /**
     * Get header string
     * @author Steve A. <steve@realtyna.com>
     * @return string
     */
    public function get_header()
    {
        return $this->header;
    }

    protected function utf8ize($d)
    {
        if(is_array($d))
        {
            foreach($d as $k=>$v)
            {
                $d[$k] = $this->utf8ize($v);
            }
        }
        elseif(is_string($d))
        {
            return utf8_encode($d);
        }
        
        return $d;
    }
}