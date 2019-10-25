<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
(function($)
{
    $(function()
    {
        <?php if($this->lazyload): ?>
        Realtyna.options.ajaxloader.coverStyle.backgroundColor = '#eeeeee';
        var loader = Realtyna.ajaxLoader.show('.wpl-gallery-pshow-wp', 'normal', 'center', true);
        <?php endif; ?>

        var slider = $('#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> #wpl_gallery_container<?php echo $this->property_id; ?>').lightSlider(
        {
            pause : 4000,
            auto: <?php echo (($this->autoplay) ? 'true' : 'false'); ?>,
            mode: 'fade',
            item: 1,
            thumbItem:<?php echo (($this->thumbnail_numbers) ? $this->thumbnail_numbers : 20); ?>,
            loop: true,
            autoWidth: true,
            adaptiveHeight: true,
            gallery: <?php echo (($this->thumbnail and count($this->gallery)) ? 'true' : 'false'); ?>,
            preload: 1,
            onSliderLoad: function(el)
            {
                $('#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> #wpl_gallery_container<?php echo $this->property_id; ?> li').show();
                if($('#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> #wpl_gallery_container<?php echo $this->property_id; ?>').find('.gallery_no_image').length == 0)
                {
                    el.lightGallery(
                    {
                        selector: '#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> #wpl_gallery_container<?php echo $this->property_id; ?> .lslide',
                        thumbWidth: <?php echo $this->thumbnail_width ?>
                    });
                }

                <?php if($this->thumbnail and count($this->gallery)): ?>
                $('#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> .lSSlideOuter').append('<div class="wpl-lSSlider-thumbnails"><div class="lSAction"><a class="lSPrev"></a><a class="lSNext"></a></div><div class="wpl-lSSlider-thumbnails-inner"></div></div>');
                $('#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> .lSSlideOuter .wpl-lSSlider-thumbnails-inner').append($('#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> .lSPager'));

                $('#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> .wpl-lSSlider-thumbnails .lSNext').on('click', function()
                {
                    var id = slider.getCurrentSlideCount();
                    id = id + 5;
                    slider.goToSlide(id);
                });

                $('#wpl_gallery_wrapper-<?php echo $this->activity_id; ?> .wpl-lSSlider-thumbnails .lSPrev').on('click', function()
                {
                    var id = slider.getCurrentSlideCount();
                    id = id - 1;
                    slider.goToSlide(id);
                });
                <?php endif; ?>

                <?php if($this->lazyload): ?>
                Realtyna.ajaxLoader.hide(loader);
                <?php endif; ?>
            }
        });
    });
})(jQuery);
</script>