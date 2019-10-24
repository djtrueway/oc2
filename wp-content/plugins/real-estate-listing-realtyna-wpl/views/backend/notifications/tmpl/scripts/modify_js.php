<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
	wplj(function()
	{
		wplj('#wpl_notification_form').submit(function(e)
		{
			e.preventDefault();
			var include_user = '';
			var include_email = '';
			var include_membership = '';
            
			wplj("#additional_users > option").each(function(i)
			{
				if(include_user !== '') include_user += ',';
				include_user += this.value;
			});
			
			wplj("#additional_memberships > option").each(function(i)
			{
				if(include_membership !== '') include_membership += ',';
				include_membership += this.value;
			});
            
			wplj("#additional_emails > option").each(function(i)
			{
				if(include_email !== '') include_email += ',';
				include_email += encodeURIComponent(this.value);
			});

            var sms_request_str = '';
			<?php if(wpl_global::check_addon('sms')): ?>
            var include_sms_user = '';
            var include_sms_membership = '';
            var include_sms_mobile = '';

            wplj("#sms_additional_users > option").each(function(i)
            {
                if(include_sms_user !== '') include_sms_user += ',';
                include_sms_user += this.value;
            });

            wplj("#sms_additional_memberships > option").each(function(i)
            {
                if(include_sms_membership !== '') include_sms_membership += ',';
                include_sms_membership += this.value;
            });

            wplj("#sms_additional_mobile > option").each(function(i)
            {
                if(include_sms_mobile !== '') include_sms_mobile += ',';
                include_sms_mobile += encodeURIComponent(this.value);
            });

            sms_request_str = '&info[include_sms_mobile]=' + include_sms_mobile + '&info[include_sms_membership]=' + include_sms_membership + '&info[include_sms_user]=' + include_sms_user;
			<?php endif; ?>
			
            wplj("#wpl_template-tmce").trigger('click');
			tinyMCE.triggerSave();

            var data = tinyMCE.activeEditor.getContent();
			start = data.indexOf("<p>");
            data = data.substring(start);
            
			while(data.indexOf("<img") != -1)
			{
                start = data.indexOf("<img");
                end = data.indexOf("/>", start);
                
				var replace = data.substring(start, end+2);
                
                // It's not a WPL image
                if(replace.indexOf("data-wpl-var") === -1)
                {
                    // Destroy the image tag for avoiding infinitive loop
                    var new_image = replace.replace('<img', '< img');
                    data = data.replace(replace, new_image);
                    
                    continue;
                }
                
				var data_wpl_var = replace.substring(replace.indexOf("data-wpl-var")+14);
				data_wpl_var = data_wpl_var.substring(0, data_wpl_var.indexOf("\""));
                
				data = data.replace(replace, "##"+data_wpl_var+"##");
			}
            
            // Fix destroyed image tags again
            data = data.replace(new RegExp('< img', 'g'), '<img');
                    
			wplj("#wpl_template").val(data);
			ajax_loader_element = "#wpl_modify_ajax_loader";
			wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');

			var request_str = 'wpl_format=b:notifications:ajax&wpl_function=save_notification&info[include_email]=' + include_email + '&info[include_membership]=' + include_membership + '&info[include_user]=' + include_user + '&' + wplj(this).serialize() + '&_wpnonce=<?php echo $this->nonce; ?>&'+sms_request_str;
			var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element, 'HTML', 'POST');
			
			ajax.success(function(data)
			{
				wplj(ajax_loader_element).html('');
				wpl_show_messages('<?php echo __('Notification modified succesfully.', 'real-estate-listing-realtyna-wpl'); ?>', '.wpl_notification_modify .wpl_show_message', 'wpl_green_msg');
			});
		});
	});
});

function add_recipients(sel,inc,field)
{
	var names = new Array();
	if(field == 'email_recipients')
	{
		var ids = Array();
		var email_address = wplj("#email_address").val();
		
        if(email_address == '' || email_address.indexOf("@") == -1 || email_address.indexOf(".") == -1)
		{
			wpl_alert("<?php echo __("Please enter a valid email address", 'real-estate-listing-realtyna-wpl'); ?>");
			return;
		}
        
		ids[0] = email_address;
		names[0] = email_address;
		wplj("#email_address").val('');
	}
	else if(field == 'sms_recipients')
	{
		var ids=Array();
		var email_address = wplj("#sms_number").val();
        
		ids[0] = email_address;
		names[0] = email_address;
		wplj("#sms_number").val('');
	}
	else
	{
		var ids = wplj("#"+sel).val();
		if(ids == null || ids.length == 0)
		{
			wpl_alert("<?php echo __("Please select at least one option", 'real-estate-listing-realtyna-wpl'); ?>");
			return;
		}
        
		for(i=0; i<ids.length; i++)
		{
			names[i] = wplj("#"+sel+" option[value='"+ids[i]+"']").html();
			wplj("#"+sel+" option[value='"+ids[i]+"']").remove();
		}
	}
    
	for(i=0; i<ids.length; i++) wplj("#"+inc).append('<option value="'+ids[i]+'">'+names[i]+'</option>');
}

function remove_recipients(sel,inc,field)
{
	var ids = wplj("#"+inc).val();
	if(ids == null || ids.length == 0)
	{
		wpl_alert("<?php echo __("Please select at least one option", 'real-estate-listing-realtyna-wpl'); ?>");
		return;
	}
    
	var names = new Array();
	var i;
    
	for(i=0; i<ids.length; i++)
	{
		names[i] = wplj("#"+inc+" option[value='"+ids[i]+"']").html();
		wplj("#"+inc+" option[value='"+ids[i]+"']").remove();
	}
    
	if(field == 'email_recipients')
	{
		wplj("#email_address").val(ids[0]);
	}
	else if(field == 'sms_recipients')
	{
		wplj("#sms_number").val(ids[0]);
	}
	else
	{
		for(i=0; i<ids.length; i++) wplj("#"+sel).append('<option value="'+ids[i]+'">'+names[i]+'</option>');
	}
}
</script>