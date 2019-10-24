<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
function add_to_wpl(user_id)
{
	if(!user_id)
	{
		wpl_show_messages("<?php echo addslashes(__('Invalid user', 'real-estate-listing-realtyna-wpl')); ?>", '.wpl_user_list .wpl_show_message');
		return false;
	}
	
	ajax_loader_element = '#wpl_ajax_loader_'+user_id;
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	
	request_str = 'wpl_format=b:users:ajax&wpl_function=add_user_to_wpl&user_id='+user_id+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element);
	
	ajax.success(function(data)
	{
		if(data.success == 1)
		{
			wpl_show_messages(data.message, '.wpl_user_list .wpl_show_message', 'wpl_green_msg');
			wplj(ajax_loader_element).html('');			
			
			 location.reload();
		}
		else if(data.success != 1)
		{
			wpl_show_messages(data.message, '.wpl_user_list .wpl_show_message', 'wpl_red_msg');
			wplj(ajax_loader_element).html('');
		}
	});
}

function wpl_ajax_save_users(key, element, id)
{
	ajax_loader_element = '#'+element.id+'_ajax_loader';
	url = '<?php echo wpl_global::get_full_url(); ?>';
	
	wpl_remove_message('.wpl_show_message'+id);
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	
    /** run ajax query **/
    request_str = 'wpl_format=b:users:ajax&wpl_function=save&item_id=' + id + '&table_column=' + key + '&value=' + element.value + '&_wpnonce=<?php echo $this->nonce; ?>';
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
    
	ajax.success(function(data)
	{
		if(data.success == 1)
		{
			wpl_show_messages(data.message, '.wpl_show_message'+id, 'wpl_green_msg');
			wplj(ajax_loader_element).html('');
		}
		else if(data.success != 1)
		{
			wpl_show_messages(data.message, '.wpl_show_message'+id, 'wpl_red_msg');
			wplj(ajax_loader_element).html('');
		}
	});
}

function wpl_remove_user(user_id, confirmed)
{
	if(!user_id)
	{
		wpl_show_messages("<?php echo addslashes(__('Invalid user', 'real-estate-listing-realtyna-wpl')); ?>", '.wpl_user_list .wpl_show_message');
		return false;
	}
	
	if(!confirmed)
	{
		message = "<?php echo addslashes(__('Are you sure you want to remove this item?', 'real-estate-listing-realtyna-wpl')); ?>&nbsp;(<?php echo addslashes(__('ID', 'real-estate-listing-realtyna-wpl')); ?>:"+user_id+")&nbsp;<?php echo addslashes(__('All related items will be removed.', 'real-estate-listing-realtyna-wpl')); ?>";
		message += '&nbsp;<span class="wpl_actions" onclick="wpl_remove_user(\''+user_id+'\', 1);"><?php echo addslashes(__('Yes', 'real-estate-listing-realtyna-wpl')); ?></span>&nbsp;<span class="wpl_actions" onclick="wpl_remove_message();"><?php echo addslashes(__('No', 'real-estate-listing-realtyna-wpl')); ?></span>';
		
		wpl_show_messages(message, '.wpl_user_list .wpl_show_message');
		return false;
	}
	else if(confirmed) wpl_remove_message();
	
	ajax_loader_element = '#wpl_ajax_loader_'+user_id;
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	
	request_str = 'wpl_format=b:users:ajax&wpl_function=del_user_from_wpl&user_id='+user_id+'&wpl_confirmed='+confirmed+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element);
	
	ajax.success(function(data)
	{
		if(data.success == 1)
		{
			wpl_show_messages(data.message, '.wpl_user_list .wpl_show_message', 'wpl_green_msg');
			wplj(ajax_loader_element).html('');
			
			location.reload();
		}
		else if(data.success != 1)
		{
			wpl_show_messages(data.message, '.wpl_user_list .wpl_show_message', 'wpl_red_msg');
			wplj(ajax_loader_element).html('');
		}
	});
}

function wpl_edit_user(id)
{
	if(!id) return false;
	wpl_remove_message('.wpl_data_structure_list .wpl_show_message');
	request_str = 'wpl_format=b:users:ajax&wpl_function=generate_edit_page&user_id='+id+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	wplj.ajax(
	{
		type: "POST",
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
		success: function(data)
		{
			wplj("#wpl_user_edit_div").html(data);
			wplj('.wpl_help').wpl_help();
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			wpl_show_messages('<?php echo addslashes(__('Error Occured.', 'real-estate-listing-realtyna-wpl')); ?>', '.wpl_data_structure_list .wpl_show_message', 'wpl_red_msg');
			wplj._realtyna.lightbox.close();
		}
	});
}

function ajax_save(table_name, table_column, value, item_id, field_id, form_element_id, wpl_function)
{
	if(!wpl_function) wpl_function = 'save';
	if(!form_element_id) form_element_id = "#wpl_c_"+field_id;
	
	wplj(form_element_id).attr("disabled", "disabled");
	
	var element_type = wplj("#wpl_c_"+field_id).attr('type');
	
	if(element_type == 'checkbox')
	{
		if(wplj("#wpl_c_"+field_id).is(':checked')) value = 1;
		else value = 0;
	}
	
	value = encodeURIComponent(value);
	var ajax_loader_element = '#wpl_listing_saved_span_'+field_id;
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	
	var request_str = 'wpl_format=b:users:ajax&wpl_function='+wpl_function+'&table_name='+table_name+'&table_column='+table_column+'&value='+value+'&item_id='+item_id+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element);
	
	ajax.success(function(data)
	{
		wplj("#wpl_c_"+field_id).removeAttr("disabled");
		
		if(data.success == 1)
		{
			wplj(ajax_loader_element).html('');
		}
		else if(data.success != 1)
		{
			wplj(ajax_loader_element).html('');
		}
	});
}

function ajax_multilingual_save(field_id, lang, value, item_id)
{
    var wpl_function = 'save_multilingual';
	var form_element_id = "#wpl_c_"+field_id+"_"+lang;
	
	var current_element_status = wplj(form_element_id).attr("disabled");
	wplj(form_element_id).attr("disabled", "disabled");
	
	var ajax_loader_element = '#wpl_listing_saved_span_'+field_id+"_"+lang;
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	
	request_str = 'wpl_format=b:users:ajax&wpl_function='+wpl_function+'&dbst_id='+field_id+'&value='+encodeURIComponent(value)+'&item_id='+item_id+'&lang='+lang+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element);
	ajax.success(function(data)
	{
		if(current_element_status != 'disabled') wplj(form_element_id).removeAttr("disabled");
		
		if(data.success == 1)
		{
			wplj(ajax_loader_element).html('');
		}
		else if(data.success != 1)
		{
			try{eval(data.js)} catch(err){}
			wplj(ajax_loader_element).html('');
		}
	});
}

function wpl_save_user()
{
	request_str = 'wpl_format=b:users:ajax&wpl_function=save_user&_wpnonce=<?php echo $this->nonce; ?>';
	
	wplj("#wpl_edit_user input:checkbox").each(function(ind, elm)
	{
		request_str += "&"+elm.id+"=";
		if(elm.checked) request_str += '1'; else request_str += '0';
	});
	
	wplj("#wpl_edit_user input:text, #wpl_edit_user input[type='hidden'], #wpl_edit_user select").each(function(ind, elm)
	{
		request_str += "&"+elm.id+"=";
		request_str += wplj(elm).val();
	});
	
	wpl_remove_message('.wpl_data_structure_list .wpl_show_message');
	
	/** run ajax query **/
	wplj.ajax(
	{
		type: "POST",
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
		success: function(data)
		{
			wpl_show_messages('<?php echo addslashes(__('Membership add.', 'real-estate-listing-realtyna-wpl')); ?>', '.wpl_data_structure_list .wpl_show_message', 'wpl_green_msg');
			wplj._realtyna.lightbox.close();
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			wpl_show_messages('<?php echo addslashes(__('Error Occured.', 'real-estate-listing-realtyna-wpl')); ?>', '.wpl_data_structure_list .wpl_show_message', 'wpl_red_msg');
			wplj._realtyna.lightbox.close();
		}
	});
}

function wpl_check_all()
{
	wplj("#wpl_edit_user input:checkbox").each( function(ind,elm)
	{
		elm.checked = true;
	});
}

function wpl_check_toggle()
{
	wplj("#wpl_edit_user input:checkbox").each( function(ind,elm)
	{
		if(elm.checked) elm.checked = false;
        else elm.checked = true;
	});
}

function wpl_check_none()
{
	wplj("#wpl_edit_user input:checkbox").each( function(ind,elm)
	{
		elm.checked = false;
	});
}
	
function wpl_other_option_show(state, target)
{
	if(state == 1)
	{
		wplj("#"+target).slideDown(300);
	}
	else
	{
		wplj("#"+target).slideUp(300);
	}
}

function wpl_change_membership(id)
{
	wpl_remove_message('.wpl_user_list .wpl_show_message');
	ajax_loader_element = '#wpl_ajax_loader_membership_'+id;
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	
	var membership_id = wplj("#membership_id_"+ id).val();
	request_str = 'wpl_format=b:users:ajax&wpl_function=change_membership&id='+id+'&membership_id='+membership_id+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element);
	ajax.success(function(data)
	{
		if(data.success == 1)
		{
			wpl_show_messages(data.message, '.wpl_show_message', 'wpl_green_msg');
			wplj(ajax_loader_element).html('');
		}
		else if(data.success != 1)
		{
			wpl_show_messages(data.message, ' .wpl_show_message', 'wpl_red_msg');
			wplj(ajax_loader_element).html('');
		}
	});
}

function wpl_renew_user(id)
{
	/** Show AJAX loader **/
    var wpl_ajax_loader = Realtyna.ajaxLoader.show('#wpl_user_renew'+id, 'tiny', 'rightIn');
	var request_str = 'wpl_format=b:users:ajax&wpl_function=renew_membership&id='+id+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
	ajax.success(function(data)
	{
        /** Remove AJAX loader **/
        Realtyna.ajaxLoader.hide(wpl_ajax_loader);
        
		if(data.success == 1)
		{
            wplj('#wpl_user_expiry_date'+id).html(data.data.expiry_date);
		}
		else if(data.success != 1)
		{
		}
	});
}

function wpl_expire_user(id)
{
	/** Show AJAX loader **/
    var wpl_ajax_loader = Realtyna.ajaxLoader.show('#wpl_user_expire'+id, 'tiny', 'rightIn');
	var request_str = 'wpl_format=b:users:ajax&wpl_function=expire_membership&id='+id+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
	ajax.success(function(data)
	{
        /** Remove AJAX loader **/
        Realtyna.ajaxLoader.hide(wpl_ajax_loader);

        if(data.success == 1)
		{
            wpl_show_messages(data.message, ' .wpl_show_message', 'wpl_green_msg');
		}
		else
		{
			wpl_show_messages(data.message, ' .wpl_show_message', 'wpl_red_msg');
		}
	});
}

function wpl_get_tinymce_content(html_element_id)
{
	if(wplj("#wp-"+html_element_id+"-wrap").hasClass("tmce-active"))
	{
		return tinyMCE.activeEditor.getContent();
	}
	else
	{
		return wplj("#"+html_element_id).val();
	}
}

function wpl_reset_users_form()
{
    wplj('#sf_filter').val('');
    wplj('#show_all').val('');
    wplj('#membership_id').val('');
    wplj('#wpl_users_search_form').submit();
}

function wpl_membership_toggle(selector)
{
    wplj(selector).toggle();
}

function wpl_toggle_direct_contact_users()
{
	var notification_status = wplj('#maccess_direct_contact').val();
	if(notification_status == 0)
		wplj('#maccess_direct_contact_users').show();
	else
		wplj('#maccess_direct_contact_users').hide();
}
</script>