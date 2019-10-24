<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** set params **/
$wpl_properties = isset($params['wpl_properties']) ? $params['wpl_properties'] : array();
$this->property_id = isset($wpl_properties['current']['data']['id']) ? $wpl_properties['current']['data']['id'] : NULL;
$this->current_property = $wpl_properties['current'];

/** get image params **/
$this->image_width = isset($params['image_width']) ? $params['image_width'] : 285;
$this->image_height = isset($params['image_height']) ? $params['image_height'] : 200;
$this->image_class = isset($params['image_class']) ? $params['image_class'] : '';
$this->category = (isset($params['category']) and trim($params['category']) != '') ? $params['category'] : '';
$this->resize = (isset($params['resize']) and trim($params['resize']) != '') ? $params['resize'] : 1;
$this->rewrite = (isset($params['rewrite']) and trim($params['rewrite']) != '') ? $params['rewrite'] : 0;
$this->watermark = (isset($params['watermark']) and trim($params['watermark']) != '') ? $params['watermark'] : 0;

/** show tags **/
$show_tags = (isset($params['show_tags']) and trim($params['show_tags']) != '') ? $params['show_tags'] : 1;

/** render gallery **/
$raw_gallery = isset($wpl_properties['current']['items']['gallery']) ? $wpl_properties['current']['items']['gallery'] : array();

// Filter images by category
if(trim($this->category) != '') $raw_gallery = $this->categorize($raw_gallery, $this->category);

$gallery = wpl_items::render_gallery($raw_gallery, wpl_property::get_blog_id($this->property_id));

/** import js codes **/
$this->_wpl_import($this->tpl_path.'.scripts.default', true, true, true);
?>
<div class="wpl_gallery_container" id="wpl_gallery_container<?php echo $this->property_id; ?>" >
    <?php 
    if(!count($gallery))
    {
        echo '<div class="no_image_box" style="width: '.$this->image_width.'px; height: '.$this->image_height.'px;"></div>';
    }
    else
    {
        $i = 0;
        $images_total = count($gallery);
        foreach($gallery as $image)
        {
            $image_url = $image['url'];
            
            if(isset($image['raw']['item_extra2'])) $image_alt = $image['raw']['item_extra2'];
            else $image_alt = $wpl_properties['current']['raw']['meta_keywords'];

            if($this->resize and $this->image_width and $this->image_height and $image['category'] != 'external')
            {
                /** set resize method parameters **/
                $params = array();
                $params['image_name'] = $image['raw']['item_name'];
                $params['image_parentid'] = $image['raw']['parent_id'];
                $params['image_parentkind'] = $image['raw']['parent_kind'];
                $params['image_source'] = $image['path'];
                
                /** resize image if does not exist **/
                $image_url = wpl_images::create_gallery_image($this->image_width, $this->image_height, $params, $this->watermark, $this->rewrite);
            }
            
            echo '<img '.$this->itemprop_image.' id="wpl_gallery_image'.$this->property_id .'_'.$i.'" src="'.$image_url.'" class="wpl_gallery_image '.$this->image_class.'" onclick="wpl_plisting_slider('.$i.', '.$images_total.', '.$this->property_id.');" alt="'.$image_alt.'" width="'.$this->image_width.'" height="'.$this->image_height.'" style="width: '.$this->image_width.'px; height: '.$this->image_height.'px;" />';
            $i++;
        }
    }
	?>
    
	<?php if($show_tags): ?>
    <div class="wpl-listing-tags-wp">
        <div class="wpl-listing-tags-cnt">
            <?php /* Property tags */ echo $this->tags(); ?>
        </div>
    </div>
	<?php endif; ?>
    
</div>