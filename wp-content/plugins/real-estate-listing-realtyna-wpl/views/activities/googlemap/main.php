<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** activity class **/
class wpl_activity_main_googlemap extends wpl_activity
{
	public $tpl_path = 'views.activities.googlemap.tmpl';
	public $settings;
	
	public function start($layout, $params)
	{
		// Don't show the map if it is a bot!
		if(wpl_global::is_bot()) return;

        // WPL Settings
        $this->settings = wpl_global::get_settings();

        // Maximum Hits
        $maximum_hits = isset($params['googlemap_hits']) ? $params['googlemap_hits'] : 1000000;

        // Current Hits
        $today_hits = get_option('wpl_gmap_hits_'.$this->activity_id, 0);
        $today = get_option('wpl_gmap_hits_date_'.$this->activity_id);

        if(!$today)
        {
            $today = current_time('Y-m-d');
            update_option('wpl_gmap_hits_date_'.$this->activity_id, $today);
        }

        // We're in a new date
        if($today != current_time('Y-m-d'))
        {
            update_option('wpl_gmap_hits_date_'.$this->activity_id, current_time('Y-m-d'));
            update_option('wpl_gmap_hits_'.$this->activity_id, 0);

            $today_hits = 0;
        }

        // Maximum hits reached!
        if($today_hits >= $maximum_hits) return;
        // Update the Daily Hits
        else update_option('wpl_gmap_hits_'.$this->activity_id, ++$today_hits);

        // Include Layout
        include _wpl_import($layout, true, true);
    }
}