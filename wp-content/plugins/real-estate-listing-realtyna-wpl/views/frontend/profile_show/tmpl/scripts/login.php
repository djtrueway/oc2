<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function wpl_user_logreg()
{
    var request = wplj('#wpl_user_login_register_form').serialize();
    var message_path = '#wpl_user_login_register_form_show_messages';
    var wplmethod = wplj("#wpl_user_logreg_guest_method").val();
    
    /** Make button disabled **/
    wplj("#wpl_user_login_register_"+wplmethod+"_submit").attr('disabled', 'disabled');
    
    wplj.ajax(
    {
        url: '<?php echo ($this->wplraw ? wpl_global::get_wp_url() : wpl_global::get_full_url()); ?>',
        data: 'wpl_format=f:profile_show:ajax&'+request,
        type: 'POST',
        dataType: 'json',
        cache: false,
        success: function(response)
        {
            /** Make button enabled **/
            wplj("#wpl_user_login_register_"+wplmethod+"_submit").removeAttr('disabled');
            
            if(response.success)
            {
                if(wplmethod == 'login')
                {
                    wplj("#wpl_user_login_register_form").hide();
                    wplj("#wpl_user_login_register_toggle").hide();

                    setTimeout(function()
                    {
                        wplj._realtyna.lightbox.close();
                    }, 2000);
                }
                else
                {
                    wplj("#wpl_lr_username").val(wplj("#wpl_lr_email").val());
                    wplj("#wpl_lr_password").val('');
                    wpl_user_logreg_toggle('login');
                }
                
                wpl_show_messages(response.message, message_path, 'wpl_green_msg');
                if(response.data.token) wplj("#wpl_user_login_register_token").val(response.data.token);
            }
            else
            {
                wpl_show_messages(response.message, message_path, 'wpl_red_msg');
                if(response.data.token) wplj("#wpl_user_login_register_token").val(response.data.token);
            }
        },
        error: function(jqXHR, textStatus, errorThrown)
        {
            wpl_show_messages("<?php echo addslashes(__('Error Occurred!', 'real-estate-listing-realtyna-wpl')); ?>", message_path, 'wpl_red_msg');
            
            /** Make button enabled **/
            wplj("#wpl_user_login_register_"+wplmethod+"_submit").removeAttr('disabled');
        }
    });
}

function wpl_user_logreg_toggle(type)
{
    if(typeof type === undefined) type = 'register';
    
    if(type === 'login')
    {
        wplj("#wpl_user_login_register_toggle_register").hide();
        wplj("#wpl_user_login_register_toggle_login").show();
        
        wplj("#wpl_user_login_register_form_register").hide();
        wplj("#wpl_user_login_register_form_login").show();
        
        wplj("#wpl_user_login_register_register_submit").hide();
        wplj("#wpl_user_login_register_login_submit").show();
    }
    else
    {
        wplj("#wpl_user_login_register_toggle_register").show();
        wplj("#wpl_user_login_register_toggle_login").hide();
        
        wplj("#wpl_user_login_register_form_register").show();
        wplj("#wpl_user_login_register_form_login").hide();
        
        wplj("#wpl_user_login_register_register_submit").show();
        wplj("#wpl_user_login_register_login_submit").hide();
    }
    
    /** Set type to form values **/
    wplj("#wpl_user_logreg_guest_method").val(type);
}
</script>