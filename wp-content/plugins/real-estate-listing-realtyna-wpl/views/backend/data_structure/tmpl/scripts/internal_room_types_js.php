 <?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
	wplj( ".sortable_room_types").sortable(
    {
        handle: 'span.icon-move',
        cursor: "move" ,
        update : function(e, ui)
        {
            var stringDiv = "";
            wplj(this).children("tr").each(function(i)
            {
                var tr = wplj(this);
                var tr_id = tr.attr("id").split("_");
                if(i != 0) stringDiv += ",";
                stringDiv += tr_id[3];
            });

            request_str = 'wpl_format=b:data_structure:ajax_room_types&wpl_function=sort_rooms&sort_ids='+stringDiv+'&_wpnonce=<?php echo $this->nonce; ?>';

            wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function(data)
                {
                },
                error: function(jqXHR, textStatus, errorThrown)
                {
                    wpl_show_messages('<?php echo addslashes(__('Error Occured.', 'real-estate-listing-realtyna-wpl')); ?>', '.wpl_data_structure_list .wpl_show_message', 'wpl_red_msg');

                }
            });
        }
    });
});

// change enabled state enabled/disabled
function wpl_room_types_enabled_change(id)
{	
	if(!id)
	{
		wpl_show_messages("<?php echo addslashes(__('Invalid field', 'real-estate-listing-realtyna-wpl')); ?>", '.wpl_show_message');
		return false;
	}
	
	ajax_loader_element = '#wpl_ajax_loader_rooms_'+id;
	ajax_flag = '#wpl_ajax_flag_rooms_'+id;
	
	// get status for whene repate the state
	var enabled_status=null;
	if(wplj(ajax_flag).hasClass('icon-enabled'))
	{
		enabled_status = 0;
	}
	else if(wplj(ajax_flag).hasClass('icon-disabled'))
	{
		enabled_status = 1;
	}
	
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	request_str = 'wpl_format=b:data_structure:ajax_room_types&wpl_function=room_types_enabled_state_change&id='+id+'&enabled_status='+enabled_status+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
	
	ajax.success(function(data)
	{
		if(data.success == 1)
		{
			if(enabled_status == 1)
			{
				wplj(ajax_flag).removeClass('icon-disabled').addClass('icon-enabled');
			}
			else
			{
				wplj(ajax_flag).removeClass('icon-enabled').addClass('icon-disabled');
			}
			
			wplj(ajax_loader_element).html('');
		}
		else if(data.success != 1)
		{
			wpl_show_messages(data.message, '.wpl_data_structure_list .wpl_show_message', 'wpl_red_msg');
			wplj(ajax_loader_element).html('');
		}
	});
}

function wpl_remove_room_type(room_type_id, confirmed)
{
	if(!room_type_id)
	{
		wpl_show_messages("<?php echo addslashes(__('Invalid room type!', 'real-estate-listing-realtyna-wpl')); ?>", '.wpl_data_structure_list .wpl_show_message');
		return false;
	}
	
	if(!confirmed)
	{
		message = "<?php echo addslashes(__('Are you sure you want to remove this item?', 'real-estate-listing-realtyna-wpl')); ?>&nbsp;(<?php echo addslashes(__('ID', 'real-estate-listing-realtyna-wpl')); ?>:"+room_type_id+")&nbsp;";
		message += '<span class="wpl_actions" onclick="wpl_remove_room_type(\''+room_type_id+'\', 1);"><?php echo addslashes(__('Yes', 'real-estate-listing-realtyna-wpl')); ?></span>&nbsp;<span class="wpl_actions" onclick="wpl_remove_message();"><?php echo addslashes(__('No', 'real-estate-listing-realtyna-wpl')); ?></span>';
		
		wpl_show_messages(message, '.wpl_data_structure_list .wpl_show_message');
		return false;
	}
	else if(confirmed) wpl_remove_message();
	
	ajax_loader_element = '#rooms_items_row_'+room_type_id;
	request_str = 'wpl_format=b:data_structure:ajax_room_types&wpl_function=remove_room_type&room_type_id='+room_type_id+'&wpl_confirmed='+confirmed+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str, ajax_loader_element);
	
	ajax.success(function(data)
	{
		if(data.success == 1)
		{
			wpl_show_messages(data.message, '.wpl_data_structure_list .wpl_show_message', 'wpl_green_msg');
			wplj(ajax_loader_element).slideUp(500);
		}
		else if(data.success != 1)
		{
			wpl_show_messages(data.message, '.wpl_data_structure_list .wpl_show_message', 'wpl_red_msg');
		}
	});
}

function wpl_generate_new_room_type()
{
	wpl_remove_message('.wpl_show_message');
	request_str = 'wpl_format=b:data_structure:ajax_room_types&wpl_function=generate_new_room_type&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	wplj.ajax(
	{
		type: "POST",
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
		success: function(data)
		{
			wplj("#wpl_new_room_type").html(data);
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			wpl_show_messages('<?php echo addslashes(__('Error Occured.', 'real-estate-listing-realtyna-wpl')); ?>', '.wpl_data_structure_list .wpl_show_message', 'wpl_red_msg');
			wplj._realtyna.lightbox.close();
		}
	});
}

function wpl_change_room_type_name(id, name)
{
	wpl_remove_message('.wpl_show_message');
	ajax_loader_element = '#wpl_ajax_loader_room_name_'+id;
	wplj(ajax_loader_element).html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');
	
	request_str = 'wpl_format=b:data_structure:ajax_room_types&wpl_function=change_room_type_name&id='+id+'&name='+name+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	/** run ajax query **/
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
	
	ajax.success(function(data)
	{
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

function wpl_ajax_save_room_type()
{	
	wpl_remove_message('.wpl_data_structure_list .wpl_show_message');
	
	if(!wplj("#name").val())
	{
		wpl_alert("<?php echo addslashes(__('Invalid Room type name', 'real-estate-listing-realtyna-wpl')); ?>");
		return false;
	}
	
	var name = wplj('#name').val();
	request_str = 'wpl_format=b:data_structure:ajax_room_types&wpl_function=save_room_type&name='+name+'&_wpnonce=<?php echo $this->nonce; ?>';
	
	wpl_remove_message('.wpl_data_structure_list .wpl_show_message');
	
	/** run ajax query **/
	wplj.ajax(
	{
		type: "POST",
		url: '<?php echo wpl_global::get_full_url(); ?>',
		data: request_str,
		success: function(data)
		{
			wpl_show_messages('<?php echo addslashes(__('Room type added.', 'real-estate-listing-realtyna-wpl')); ?>', '.wpl_data_structure_list .wpl_show_message', 'wpl_green_msg');
			wplj._realtyna.lightbox.close();
		},
		error: function(jqXHR, textStatus, errorThrown)
		{
			wpl_show_messages('<?php echo addslashes(__('Error Occured.', 'real-estate-listing-realtyna-wpl')); ?>', '.wpl_data_structure_list .wpl_show_message', 'wpl_red_msg');
			wplj._realtyna.lightbox.close();
		}
	});
}
</script>