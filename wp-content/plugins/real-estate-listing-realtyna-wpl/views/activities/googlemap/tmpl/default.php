<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

// Google Map is set to don't disply by default. A click by user on Googlemap widget is needed to show the map
if(isset($this->settings['googlemap_display_status']) and !$this->settings['googlemap_display_status'] and !wpl_request::getVar('wplfmap', 0)) return;

/** set params **/
$wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();

/** Parameters **/
$this->params = $params;

/** get params **/
$this->googlemap_view = isset($params['googlemap_view']) ? $params['googlemap_view'] : 'ROADMAP';
$this->map_width = isset($params['map_width']) ? $params['map_width'] : 980;
$this->map_height = isset($params['map_height']) ? $params['map_height'] : 480;
$this->default_lt = isset($params['default_lt']) ? $params['default_lt'] : '38.685516';
$this->default_ln = isset($params['default_ln']) ? $params['default_ln'] : '-101.073324';
$this->default_zoom = isset($params['default_zoom']) ? $params['default_zoom'] : '4';
$this->infowindow_event = isset($params['infowindow_event']) ? $params['infowindow_event'] : 'click';
$this->get_direction = isset($params['get_direction']) ? $params['get_direction'] : 0;
$this->scroll_wheel = isset($params['scroll_wheel']) ? $params['scroll_wheel'] : 'false';
$this->spatial = isset($params['spatial']) ? $params['spatial'] : 0;

// Clustering
$this->clustering = isset($params['clustering']) ? $params['clustering'] : 0;
$this->clusterer_iconset = isset($this->settings['aps_cluster_iconset']) ? $this->settings['aps_cluster_iconset'] : 'c';

/** Preview Property feature **/
$this->map_property_preview = isset($params['map_property_preview']) ? $params['map_property_preview'] : 0;
$this->map_property_preview_show_marker_icon = isset($params['map_property_preview_show_marker_icon']) ? $params['map_property_preview_show_marker_icon'] : 'price';

$this->show_marker = 1;

/** unset current key **/
unset($wpl_properties['current']);

$this->markers = wpl_property::render_markers($wpl_properties);

/** Map Search **/
$this->map_search_status = isset($params['map_search']) ? $params['map_search'] : 0;

/** Disabling the map property preview feature if the Map Search field is disabled **/
if(!$this->map_search_status) $this->map_property_preview = 0;

/** load js codes **/
$this->_wpl_import($this->tpl_path.'.scripts.js', true, true);
$this->_wpl_import($this->tpl_path.'.scripts.default', true, true);

/** Demographic **/
$this->demographic_status = isset($params['demographic']) ? $params['demographic'] : 0;
if($this->demographic_status and wpl_global::check_addon('demographic')) $this->_wpl_import($this->tpl_path.'.scripts.addon_demographic', true, true);

/** load addon APS js codes **/
if($this->map_search_status and wpl_global::check_addon('aps')) $this->_wpl_import($this->tpl_path.'.scripts.addon_aps', true, true);
?>
<div class="wpl_googlemap_container wpl_googlemap_plisting" id="wpl_googlemap_container<?php echo $this->activity_id; ?>" data-wpl-height="<?php echo $this->map_height; ?>">
    <div class="wpl-map-add-ons"></div>
    <div class="wpl_map_canvas" id="wpl_map_canvas<?php echo $this->activity_id; ?>" style="height: <?php echo $this->map_height ?>px;"></div>
</div>