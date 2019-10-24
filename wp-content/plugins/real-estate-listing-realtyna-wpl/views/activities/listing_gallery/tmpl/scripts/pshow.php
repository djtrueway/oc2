<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<?php if(count($this->gallery) > 1): ?>
<script type="text/javascript">
(function($)
{
    $(function()
    {
        <?php if($this->lazyload): ?>
        Realtyna.options.ajaxloader.coverStyle.backgroundColor = '#eeeeee';
        var loader = Realtyna.ajaxLoader.show('.wpl_gallery_container', 'normal', 'center', true);
        <?php endif; ?>

        $('#bxslider_<?php echo $this->property_id.'_'.$this->activity_id; ?>').bxSlider({
            mode: 'fade',
            pause : 6000,
            auto: <?php echo (($this->autoplay) ? 'true' : 'false'); ?>,
            captions: false,
            controls: true,
            adaptiveHeight: true,
            pagerCustom: '#bx-pager-<?php echo $this->activity_id; ?>',
            onSliderLoad: function()
            {
                <?php if($this->lazyload): ?>
                Realtyna.ajaxLoader.hide(loader);
                <?php endif; ?>
            }
        });
    });
})(jQuery);
</script>
<?php endif;