<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** import js codes **/
$this->_wpl_import('widgets.carousel.scripts.js', true, true);

$image_width = isset($this->instance['data']['image_width']) ? $this->instance['data']['image_width'] : 1920;
$image_height = isset($this->instance['data']['image_height']) ? $this->instance['data']['image_height'] : 558;
$tablet_image_height = isset($this->instance['data']['tablet_image_height']) ? $this->instance['data']['tablet_image_height'] : 400;
$phone_image_height = isset($this->instance['data']['phone_image_height']) ? $this->instance['data']['phone_image_height'] : 310;

$thumbnail_width = isset($this->instance['data']['thumbnail_width']) ? $this->instance['data']['thumbnail_width'] : 150;
$thumbnail_height = isset($this->instance['data']['thumbnail_height']) ? $this->instance['data']['thumbnail_height'] : 60;
$auto_play = isset($this->instance['data']['auto_play']) ? $this->instance['data']['auto_play'] : false;
$smart_resize = isset($this->instance['data']['smart_resize']) ? $this->instance['data']['smart_resize'] : false;
$slide_interval = isset($this->instance['data']['slide_interval']) ? $this->instance['data']['slide_interval'] : 3000;
$show_nav = isset($this->instance['data']['show_nav']) ? $this->instance['data']['show_nav'] : false;
$hide_pagination = isset($this->instance['data']['hide_pagination']) ? $this->instance['data']['hide_pagination'] : false;
$hide_caption = isset($this->instance['data']['hide_caption']) ? $this->instance['data']['hide_caption'] : false;
$show_tags = isset($this->instance['data']['show_tags']) ? $this->instance['data']['show_tags'] : false;
$lazy_load = isset($this->instance['data']['lazy_load']) ? $this->instance['data']['lazy_load'] : false;

/** add Layout js **/
$js[] = (object) array('param1'=>'modern.slider', 'param2'=>'js/libraries/wpl.modern.slider.min.js');
foreach($js as $javascript) wpl_extensions::import_javascript($javascript);

$large_images = $thumbnail = NULL;
$tags = wpl_flex::get_tag_fields((isset($this->instance['data']['kind']) ? $this->instance['data']['kind'] : 0));

foreach($wpl_properties as $key=>$gallery)
{
    if(!isset($gallery['items']['gallery'][0])) continue;
    
    $params = array();
    $params['image_name'] 		= $gallery['items']['gallery'][0]->item_name;
    $params['image_parentid'] 	= $gallery['items']['gallery'][0]->parent_id;
    $params['image_parentkind'] = $gallery['items']['gallery'][0]->parent_kind;
    $params['image_source'] 	= wpl_global::get_upload_base_path(wpl_property::get_blog_id($params['image_parentid'])).$params['image_parentid'].DS.$params['image_name'];

    $image_title = wpl_property::update_property_title($gallery['raw']);

    if(isset($gallery['items']['gallery'][0]->item_extra2) and trim($gallery['items']['gallery'][0]->item_extra2) != '') $image_alt = $gallery['items']['gallery'][0]->item_extra2;
    else $image_alt = $gallery['raw']['meta_keywords'];

    if($gallery["items"]["gallery"][0]->item_cat != 'external')
    {
        $image_url 			= wpl_images::create_gallery_image($image_width, $image_height, $params, 1);
        $thumbnail_url 		= wpl_images::create_gallery_image($thumbnail_width, $thumbnail_height, $params);
    }
    else
    {
        $image_url 			= $gallery["items"]["gallery"][0]->item_extra3;
        $thumbnail_url 		= $gallery["items"]["gallery"][0]->item_extra3;
    }

    // Location visibility
    $location_visibility = wpl_property::location_visibility($gallery['items']['gallery'][0]->parent_id, $gallery['items']['gallery'][0]->parent_kind, wpl_users::get_user_membership());
	
    $large_images .= '<li '.$this->itemscope.' '.$this->itemtype_SingleFamilyResidence.'><img '.$this->itemprop_image.' src="'.$image_url.'" alt="'.$image_alt.'" style="width: '.$image_width.'px; height: '.$image_height.'px;" />';
	
    if($show_tags)
    {
		$large_images .= '<div class="wpl-listing-tags-wp">
            <div class="wpl-listing-tags-cnt">
                '.$this->tags($tags, $gallery['raw']).'
            </div>
        </div>';
	}
    
    if(!$hide_caption)
    {
        $large_images .= '
        <div class="ei-title">
            <h2 '.$this->itemprop_name.'>' . $image_title . '</h2>
            <h3 '.$this->itemprop_address.'>' . (trim($gallery['materials']['living_area']['value']) != '' ? $gallery['materials']['living_area']['value'] . ' - ' : '') . ($location_visibility === true ? $gallery['location_text'] : $location_visibility) . '</h3>
            <a '.$this->itemprop_url.' class="more_info" href="' . $gallery["property_link"] . '">' . __('More info', 'real-estate-listing-realtyna-wpl') . '</a>
        </div>';
    }
    
    $large_images .= '</li>';
    $thumbnail .= '<li><a href="#">'.$image_title.'</a><img src="'.$thumbnail_url.'" alt="'.$image_alt.'" width="'.$thumbnail_width.'" height="'.$thumbnail_height.'" style="width: '.$thumbnail_width.'px; height: '.$thumbnail_height.'px;" /></li>';
}
?>
<div class="wpl_carousel_container <?php echo $this->css_class; ?>">
    <div id="wpl-modern-<?php echo $this->widget_id; ?>" class="ei-slider">
        <ul class="ei-slider-large">
            <?php echo $large_images; ?>
        </ul>
        <div class="ei-slider-navigation">
            <div class="ei-slider-next"></div>
            <div class="ei-slider-prev"></div>
        </div>
        <ul class="ei-slider-thumbs <?php if($hide_pagination) echo 'wpl-util-hidden';?>">
            <li class="ei-slider-element"><?php echo __('Current', 'real-estate-listing-realtyna-wpl'); ?></li>
            <?php echo $thumbnail; ?>
        </ul>
    </div>
</div>

<script type="text/javascript">
wplj(function()
{
    wplj('#wpl-modern-<?php echo $this->widget_id; ?>').modernSlider(
    {
        animation: 'center',
        autoplay: <?php echo $auto_play ? 'true' : 'false'; ?>,
        slideshow_interval: <?php echo $slide_interval ? $slide_interval : '3000'; ?>,
        titlesFactor: 0,
        thumbMaxWidth: <?php echo $thumbnail_width; ?>,
        smartResize: <?php echo $smart_resize ? 'true' : 'false'; ?>
    });

});
</script>
<style type="text/css">
#wpl-modern-<?php echo $this->widget_id; ?>{
    height: <?php echo $image_height; ?>px;
}

@media (min-width: 481px) and (max-width: 1024px){
    #wpl-modern-<?php echo $this->widget_id; ?>{
        max-height: <?php echo $tablet_image_height; ?>px;
    }
}

@media(max-width: 480px){
    #wpl-modern-<?php echo $this->widget_id; ?>{
        max-height: <?php echo $phone_image_height; ?>px;
    }
}
<?php if(!$lazy_load): ?>
    .ei-slider-loading {display: none !important;}
<?php endif; ?>
</style>