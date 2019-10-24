<script type="text/javascript">
jQuery(document).ready(function()
{
    wplj('.wpl_profile_container').hover(function(){
        wplj(this).children('.wpl_profile_picture').addClass('flip');
    },function(){
        wplj(this).children('.wpl_profile_picture').removeClass('flip');
    });
});
</script>