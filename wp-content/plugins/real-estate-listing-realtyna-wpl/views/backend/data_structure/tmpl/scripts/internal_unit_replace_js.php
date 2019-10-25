<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function wpl_save_replaced_unit()
{
    var ajax_loader_element = "#wpl_replaced_unit_ajax_loader";
    wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
    wplj("#wpl_replaced_unit_submit_button").prop("disabled", "disabled");

    var element = wplj("#wpl_replaced_unit"), new_unit = element.val(), old_unit = element.attr('data-old-unit'), type = element.attr('data-type');
    var ajax_flag = '#wpl_ajax_flag_'+old_unit+'_'+type;

    var request_str = 'wpl_format=b:data_structure:ajax_unit_manager&wpl_function=replaceunit_with_activeunit&new_unit='+new_unit+'&old_unit='+old_unit+'&type='+type+'&_wpnonce=<?php echo $this->nonce; ?>';
    var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element, 'HTML', 'POST');

    ajax.success(function(data)
    {
        wplj(ajax_loader_element).html('');
        wplj("#wpl_replaced_unit_submit_button").removeAttr("disabled");
        wplj._realtyna.lightbox.close();
        wplj(ajax_flag).removeClass('icon-enabled').addClass('icon-disabled');
    });
}
</script>