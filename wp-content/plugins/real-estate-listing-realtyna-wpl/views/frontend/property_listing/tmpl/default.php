<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->properties_str = $this->_wpl_render($this->tpl_path.'.assets.default_listings', true);
if($this->wplraw == 1)
{
    echo $this->properties_str;
    exit;
}

$this->listview_str = $this->_wpl_render($this->tpl_path.'.assets.default_listings_listview', true);
if($this->wplraw == 2)
{
    echo json_encode(array('total_pages'=>$this->total_pages, 'current_page'=>$this->page_number, 'html'=>$this->listview_str));
    exit;
}

$plisting_googlemap_activities = wpl_activity::get_activities('plisting_position1', 1, '', 'loadObject', 'googlemap');
$this->plisting_googlemap_activity_id = isset($plisting_googlemap_activities->id) ? $plisting_googlemap_activities->id : NULL;

$this->_wpl_import($this->tpl_path.'.scripts.js', true, true);
if($this->wplpagination == 'scroll' and $this->property_listview and wpl_global::check_addon('pro')) $this->_wpl_import($this->tpl_path.'.scripts.js_scroll', true, true);

/** Save Search Add-on **/
if(wpl_global::check_addon('save_searches')) $this->_wpl_import($this->tpl_path.'.scripts.addon_save_searches', true, true);

/** import print library if is enabled **/
if(wpl_global::check_addon('PRO') and wpl_global::get_setting('pdf_results_page_method') == 'print')
{
	$js[] = (object) array('param1'=>'jquery.print.js', 'param2'=>'js/libraries/wpl.jquery.print.min.js');
	foreach($js as $javascript) wpl_extensions::import_javascript($javascript);
}
?>
<div class="wpl_property_listing_container <?php if(isset($this->property_css_class) and $this->property_css_class == 'map_box') echo 'wpl-property-listing-mapview'; if(!$this->listing_picture_mouseover) echo " wpl-prp-disable-image-hover"; ?>" id="wpl_property_listing_container">
	<?php /** load position1 **/ wpl_activity::load_position('plisting_position1', array('wpl_properties'=>$this->wpl_properties)); ?>
    
    <?php if(is_active_sidebar('wpl-plisting-top') and $this->kind == 0): ?>
    <div class="wpl_plisting_top_sidebar_container">
        <?php dynamic_sidebar('wpl-plisting-top'); ?>
    </div>
    <?php elseif(is_active_sidebar('wpl-complex-plisting-top') and $this->kind == 1): ?>
    <div class="wpl_plisting_top_sidebar_container">
        <?php dynamic_sidebar('wpl-complex-plisting-top'); ?>
    </div>
    <?php endif; ?>
    
    <?php if($this->property_listview): ?>
    <div class="wpl_property_listing_list_view_container">
        <?php echo $this->listview_str; ?>
    </div>
    <?php endif; ?>

    <?php /** Don't remove this element **/ ?>
    <div id="wpl_plisting_lightbox_content_container" class="wpl-util-hidden"></div>

</div>

<?php if($this->show_signature): ?>
<div class="wpl-powered-by-realtyna">
    <a href="https://realtyna.com/wpl-platform/ref/<?php echo $this->affiliate_id; ?>/">
        <img src="<?php echo wpl_global::get_wpl_url().'assets/img/idx/powered-by-realtyna.png'; ?>" alt="Powered By Realtyna" width="120" />
    </a>
</div>
<?php endif; ?>

<?php if(is_active_sidebar('wpl-plisting-bottom') and $this->kind == 0): ?>
<div class="wpl_plisting_bottom_sidebar_container">
    <?php dynamic_sidebar('wpl-plisting-bottom'); ?>
</div>
<?php elseif(is_active_sidebar('wpl-complex-plisting-bottom') and $this->kind == 1): ?>
<div class="wpl_plisting_bottom_sidebar_container">
    <?php dynamic_sidebar('wpl-complex-plisting-bottom'); ?>
</div>
<?php endif; ?>