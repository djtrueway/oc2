<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

include _wpl_import('widgets.carousel.scripts.js', true, true);

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
$lazy_load = isset($this->instance['data']['lazy_load']) ? $this->instance['data']['lazy_load'] : false;

/** add Layout js **/
$js[] = (object) array('param1'=>'responsive-slider.js', 'param2'=>'packages/responsive_slider/js/responsive-slider.min.js');
foreach($js as $javascript) wpl_extensions::import_javascript($javascript);

$css[] = (object) array('param1'=>'responsive-slider.css', 'param2'=>'packages/responsive_slider/css/responsive-slider.css');
foreach($css as $style) wpl_extensions::import_style($style);

$large_images = $thumbnail = NULL;
$pager = 0;
$pager_numbers = count($wpl_properties);
$pager_width = (100 / $pager_numbers).'%';

foreach($wpl_properties as $key=>$gallery)
{
    if(!isset($gallery['items']['gallery'][0])) continue;

    $params = array();
    $params['image_name'] 		= $gallery["items"]["gallery"][0]->item_name;
    $params['image_parentid'] 	= $gallery["items"]["gallery"][0]->parent_id;
    $params['image_parentkind'] = $gallery["items"]["gallery"][0]->parent_kind;
    $params['image_source'] 	= wpl_global::get_upload_base_path(wpl_property::get_blog_id($params['image_parentid'])).$params['image_parentid'].DS.$params['image_name'];
    $pager = $pager + 1;
    $image_title = wpl_property::update_property_title($gallery['raw']);

    if(isset($gallery['items']['gallery'][0]->item_extra2) and trim($gallery['items']['gallery'][0]->item_extra2) != '') $image_alt = $gallery['items']['gallery'][0]->item_extra2;
    else $image_alt = $gallery['raw']['meta_keywords'];

    if($gallery["items"]["gallery"][0]->item_cat != 'external')
    {
        $image_url 			= wpl_images::create_gallery_image($image_width, $image_height, $params);
        $thumbnail_url 		= wpl_images::create_gallery_image($thumbnail_width, $thumbnail_height, $params);
    }
    else
    {
        $image_url 			= $gallery["items"]["gallery"][0]->item_extra3;
        $thumbnail_url 		= $gallery["items"]["gallery"][0]->item_extra3;
    }

    $large_images .= '<li '.$this->itemscope.' '.$this->itemtype_SingleFamilyResidence.'><div class="slide-body" data-group="slide">
            <img itemprop="image" src="'.$image_url.'" alt="'.$image_alt.'" style="width: '.$image_width.'px; height:'.$image_height.'px" />';

    if(!$hide_caption)
    {
        $large_images .= '<div class="caption header" data-animate="slideAppearRightToLeft" data-delay="500" data-length="300">
                <h2 '.$this->itemprop_name.'>'.$image_title.'</h2>
                <h3 '.$this->itemprop_address.' class="caption sub sub1" data-animate="slideAppearLeftToRight" data-delay="800" data-length="500">'.(trim($gallery["rendered"][10]["value"]) != '' ? $gallery["rendered"][10]["value"].' - ' : '').$gallery["location_text"].'</h3>
                <a '.$this->itemprop_url.' href="'.$gallery["property_link"].'" class="btn-primary caption sub sub2 more_info" data-animate="slideAppearRightToLeft" data-delay="1000" data-length="500">'. __('More info', 'real-estate-listing-realtyna-wpl').'</a>
             </div>';
    }

    $large_images .= '</div></li>';
    $pagination	.='<a class="page" data-jump-to="'.$pager.'" style="width:'.$pager_width.'">'.$pager.'</a>';
}
?>
<div class="wpl_carousel_container <?php echo $this->css_class; ?>">
    <div id="wpl-modern-<?php echo $this->widget_id; ?>"  class="responsive-slider <?php if($lazy_load): ?>loading<?php endif; ?>" style="height: <?php echo $image_height.'px'; ?>">
        <div class="slides" data-group="slides" style="display: none">
            <ul>
                <?php echo $large_images; ?>
            </ul>

            <?php if($show_nav): ?>
            <a class="slider-control left" href="#" data-jump="prev"></a>
            <a class="slider-control right" href="#" data-jump="next"></a>
            <?php endif; ?>

            <?php if(!$hide_pagination): ?>
            <div class="pages-cnt">
                <div class="pages">
                    <?php echo $pagination; ?>
                </div>
            </div>
            <?php endif; ?>
        </div>
    </div>
</div>

<script type="text/javascript">
wplj(function()
{
    <?php if($pager > 1): ?>
        wplj('#wpl-modern-<?php echo $this->widget_id; ?>').responsiveSlider(
        {
            autoplay: <?php echo $auto_play ? 'true' : 'false'; ?>,
            interval: <?php echo $slide_interval ? $slide_interval : '3000'; ?>,
            transitiontime: "1000",
            onInit: function()
            {
                <?php if(!$lazy_load): ?>
                    wplj('#wpl-modern-<?php echo $this->widget_id; ?>').removeClass('loading');
                <?php endif; ?>
                wplj('#wpl-modern-<?php echo $this->widget_id; ?>').css({'height':'auto'});
                wplj('#wpl-modern-<?php echo $this->widget_id; ?> .slides').fadeIn(1000);
            }
        });
    <?php else: ?>
        <?php if(!$lazy_load): ?>
            wplj('#wpl-modern-<?php echo $this->widget_id; ?>').removeClass('loading');
        <?php endif; ?>
        wplj('#wpl-modern-<?php echo $this->widget_id; ?>').css({'height':'auto'});
        wplj('#wpl-modern-<?php echo $this->widget_id; ?> .slides').fadeIn(1000);
    <?php endif; ?>
});
</script>