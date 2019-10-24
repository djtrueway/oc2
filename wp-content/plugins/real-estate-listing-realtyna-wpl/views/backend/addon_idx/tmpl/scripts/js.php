<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<script type="text/javascript">
    wplj(document).ready(function()
    {
        // If client leave the wizard in between, this will find out the step in the page load and will jump to that step
        //Start: Get URL
        wplj.urlParam = function (name) {
            var results = new RegExp('[\?&]' + name + '=([^&#]*)').exec(window.location.href);
            if (results == null) {
                return null;
            }
            else {
                return results[1] || 0;
            }
        }
        if(!wplj.urlParam('tpl'))
        {
            var step = wpl_idx_get_step();
            if(step > 3)
            {
                window.location.replace("<?php echo wpl_global::add_qs_var('tpl', 'valid'); ?>");
            }
        }
        if(wplj.urlParam('tpl') == 'valid')
        {
            var step = wpl_idx_get_step();
            wpl_idx_goto_step(step);
        }
        if(wplj.urlParam('tpl') == 'setting')
        {
            wpl_idx_setting_table();
        }
        if(wplj.urlParam('tpl') == 'trial')
        {  
            wpl_idx_protect_trial();
            
        }
        //Back event in idx wizard
        wplj('.wpl-idx-wizard-navigation .back').on('click',function(){

            if(wplj('.wpl-wizard-tabs .wpl-column.current').prev().length)
            {
                wplj('.wpl-wizard-tabs .wpl-column.current').removeClass('current').removeClass('active').prev().addClass('current');
                wplj('.wpl-wizard-section.current').removeClass('current').prev().addClass('current');
            }
        });

        //Search through mls provider table
        wplj("#wpl-idx-search-mls-provider").keyup(function()
        {
            var term = wplj(this).val().toLowerCase();

            if(term != "")
            {
                wplj("#wpl-idx-all-mls-providers tbody tr").hide();
                wplj("#wpl-idx-all-mls-providers tbody tr").filter(function()
                {
                    var activity_values = wplj(this)
                        .children('td.provider, td.name')
                        .text();

                    return activity_values.toLowerCase().indexOf(term) > -1;
                }).show();
            }
            else
            {
                wplj("#wpl-idx-all-mls-providers tbody tr").show();
            }
        });
        // mls provider table checkboxes
        wplj(document).on('click','#wpl-idx-all-mls-providers .wpl-idx-table-checkbox',function(){
            if(wplj(this).is(':checked'))
            {
                wplj('#wpl-idx-all-mls-providers .wpl-idx-table-checkbox').removeAttr('checked');
                wplj(this).attr('checked','checked');
            }
        });
        //Calculate total amount of what client should pay
        wplj(document).on('click','.wpl-idx-table-checkbox',function(){
            wplj('#wpl-idx-total-price-choose-mls .price').html(wpl_idx_total_amount()+'$');
        });

        wplj(document).on('click','#active_listings_checkbox',function(){
            wplj(this).parents('.wpl-idx-addon-table-row').find('#configure_checkbox').trigger('click');
        });
        wplj(document).on('click','#configure_checkbox',function(){
            wplj(this).parents('.wpl-idx-addon-table-row').find('.wpl-idx-config-form-part2').toggle();
            if(wplj(this).is(':checked'))
            {
                wplj(this).parents('.wpl-idx-addon-table-row').find('#active_listings_checkbox').removeAttr('checked');
            }
            else
            {
                wplj(this).parents('.wpl-idx-addon-table-row').find('#active_listings_checkbox').attr('checked','checked');
                wplj(this).parents('.wpl-idx-addon-table-row').find('.wpl-idx-config-form-part2 input[type="checkbox"]').removeAttr('checked');
                wplj(this).parents('.wpl-idx-addon-table-row').find('.wpl-idx-config-form-part2 input[type="number"]').val("");
            }
        });
        wplj(document).on('click','#office_listing',function(){
            if(wplj(this).is(':checked'))
            {
                wplj(this).parents('.wpl-idx-addon-table-row').find('#all_listing').removeAttr('checked');
                wplj(this).parents('.wpl-idx-addon-table-row').find('#agent_listing').removeAttr('checked');
            }
        });
        wplj(document).on('click','#all_listing',function(){
            if(wplj(this).is(':checked'))
            {
                wplj(this).parents('.wpl-idx-addon-table-row').find('#office_listing').removeAttr('checked');
                wplj(this).parents('.wpl-idx-addon-table-row').find('#agent_listing').removeAttr('checked');
            }
        });
        wplj(document).on('click','#agent_listing',function(){
            if(wplj(this).is(':checked'))
            {
                wplj(this).parents('.wpl-idx-addon-table-row').find('#all_listing').removeAttr('checked');
                wplj(this).parents('.wpl-idx-addon-table-row').find('#office_listing').removeAttr('checked');
            }
        });
    });

    // IDX addon wizard jump to specific step
    function wpl_idx_goto_step(step)
    {
        for(var i=1;i<=step;i++) {
            wplj("#wpl-idx-wizard-step" + i).addClass('active').removeClass('current');
        }
        wplj("#wpl-idx-wizard-step" + step).addClass('current');

        if(step == 2)
        {
            wpl_idx_providers();
        }
        if(step == 3)
        {
            wpl_idx_check_payment();
        }
        if(step == 4)
        {
            wpl_idx_show_configuration_list();
        }
        wplj('.wpl-wizard-section').removeClass('current');
        wplj('#wpl-wizard-section'+step).addClass('current');

    }
    // IDX addon wizard next button action
    function wpl_idx_next_step()
    {

        wplj('.wpl-wizard-tabs .wpl-column.current').addClass('active');

        if(wplj('.wpl-wizard-tabs .wpl-column.current').hasClass('active') && wplj('.wpl-wizard-tabs .wpl-column.current').next().length)
        {
            wplj('.wpl-wizard-tabs .wpl-column.current').removeClass('current').next().addClass('current');
            wplj('.wpl-wizard-section.current').removeClass('current').next().addClass('current');
        }
    }
    // Get the wizard get current Step.It returns step from database. if the client leave the wizard in the middle.
    function wpl_idx_get_step()
    {

        wpl_remove_message('.wpl_show_message_idx');

        var loader = Realtyna.ajaxLoader.show('.wpl-idx-addon .panel-body', 'normal', 'center', true);
        var request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=get_step';
        var step = 0;
        /** run ajax query **/
        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                async: false,
                success: function(data)
                {
                    step = data.step_value;
                    if(data.message == 'Finished')
                    {
                        wpl_idx_wizard_thank_you();
                    }
                    if(data.message == 'idx user already exists')
                    {
                        wpl_idx_wizard_already_registered();
                    }
                    if(data.message == 'PHP >= 5.5 is required.')
                    {
                        wpl_idx_wizard_php_version();
                    }
                    Realtyna.ajaxLoader.hide(loader);

                }
            });
        return step;
    }
    // Calculate Total amount of selected mls packages that client purchase
    function wpl_idx_total_amount()
    {
        var total_amount = 0;
        var price = '';
        wplj('.wpl-idx-table-checkbox').each(function () {
            if(wplj(this).is(':checked')){


                price = wplj(this).parents('.wpl-idx-addon-table-row').find('.price').html();
                price = price.split('$');
                total_amount += parseInt(price[0]);
            }
        });
        return total_amount;
    }
    // Form valication
    function wpl_idx_form_validation(form,step)
    {
        wpl_remove_message('.wpl_show_message_idx');
        var valid = 1;

        wplj(form).find('input').each(function(){
            if(!wplj(this).val()){
                wplj(this).addClass('required');
                valid = 0;
            }
            else
            {
                wplj(this).removeClass('required');
            }
        });
        if(valid)
        {
            if (step == 'registration')
            {
                wpl_idx_registration();
            }
            if (step =='payment')
            {
                wpl_idx_payment();
            }
            if (step == 'configuration')
            {
                wpl_idx_configuration();
            }
        }
        else
        {
            wpl_show_messages('<?php echo __("All fields are required!"); ?>', '.wpl_show_message_idx', 'wpl_red_msg');
        }
    }
    // New user registration -- Sign up step
    function wpl_idx_registration()
    {

        wpl_remove_message('.wpl_show_message_idx');

        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=registration';
        var name = wplj('#name').val();
        var email = wplj('#email').val();
        var phone = wplj('#phone').val();
        var errors = '';

        request_str += "&name="+name+"&second_email="+email+"&phone="+phone;

        /** run ajax query **/
        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function(data)
                {

                    if(data.status != 201)
                    {
                        Realtyna.ajaxLoader.hide(loader);
                        wplj.each(data.message, function (key, value) {
                            errors += value;
                            errors += '<br/>';
                        });
                        wpl_show_messages(errors, '.wpl_show_message_idx', 'wpl_red_msg');
                       
                    }
                    if(data.status == 201)
                    {
                        wpl_show_messages(data.error, '.wpl_show_message_idx', 'wpl_green_msg');
                        Realtyna.ajaxLoader.hide(loader);
                        
                        if(wplj.urlParam('tpl') == 'valid'){
                           wpl_idx_next_step();
                           wpl_idx_providers();
                        } 
                        if(wplj.urlParam('tpl') == 'trial') wpl_idx_load_trial_data();
                    }
                }
            });
    }
    // Showing all providers in the table -- Choose mls Step
    function wpl_idx_providers() {

        wpl_remove_message('.wpl_show_message_idx');

        /*If the providers table already loaded*/
        if (wplj('#wpl-idx-all-mls-providers').hasClass('loaded')) return false;

        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=providers';

        /** run ajax query **/
        var mlsProviders = [];
        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {

                    mlsProviders += '<tr class="wpl-idx-addon-table-row">';
                    mlsProviders += '<td class="wpl-idx-addon-table-title"></td>'
                    mlsProviders += '<td class="wpl-idx-addon-table-title" colspan="3"><?php echo __('MLS Provider','real-estate-listing-realtyna-wpl'); ?></td>';
                    mlsProviders += '<td class="wpl-idx-addon-table-title"><?php echo __('Price','real-estate-listing-realtyna-wpl'); ?></td>';
                    mlsProviders += '</tr>';
                    wplj.each(data.message, function (key, value) {
                        mlsProviders += '<tr class="wpl-idx-addon-table-row">';
                        mlsProviders += '<td class="mls_id" width="40"><input id='+value.id+' class="wpl-idx-table-checkbox" type="radio" /></td>';
                        mlsProviders += '<td class="logo" width="40"><img height="25" src="'+ value.image_url +'" /></td>';
                        mlsProviders += '<td class="provider">'+ value.short_name +'</td>';
                        mlsProviders += '<td class="name">'+ value.name +'</td>';
                        mlsProviders += '<td class="price">'+ value.price +'$</td>';
                        mlsProviders += '</tr>';
                    });
                    Realtyna.ajaxLoader.hide(loader);
                    wplj('#wpl-idx-all-mls-providers tbody').html("");
                    wplj('#wpl-idx-all-mls-providers tbody').append(mlsProviders);
                    wplj('#wpl-idx-all-mls-providers').addClass("loaded");
                }
            });
    }
    // Adding mls package information that client choose -- Choose mls step
    function wpl_idx_save()
    {

        wpl_remove_message('.wpl_show_message_idx');

        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var name= '';
        var provider = '';
        var mls_id = '';
        var request_str = "";

        if(!wplj('.wpl-idx-table-checkbox:checked').length)
        {
            wpl_show_messages('<?php echo __("Please choose a mls provider"); ?>', '.wpl_show_message_idx', 'wpl_red_msg');
            Realtyna.ajaxLoader.hide(loader);
            return false;
        }

        wplj('.wpl-idx-table-checkbox').each(function () {
            if(wplj(this).is(':checked')){

                mls_id = wplj(this).attr('id');
                name = wplj(this).parents('.wpl-idx-addon-table-row').children('.name').html();
                provider = wplj(this).parents('.wpl-idx-addon-table-row').children('.provider').html();

                request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=save';
                request_str += "&mls_id="+mls_id+"&name="+name+"&provider="+provider;

                /** run ajax query **/
                wplj.ajax(
                    {
                        type: "POST",
                        url: '<?php echo wpl_global::get_full_url(); ?>',
                        data: request_str,
                        success: function (data) {
                            if(data.status == 500)
                            {
                                Realtyna.ajaxLoader.hide(loader);
                                wpl_show_messages(data.error, '.wpl_show_message_idx', 'wpl_red_msg');
                            }
                            if(data.status == 200 || data.status == 201)
                            {
                                Realtyna.ajaxLoader.hide(loader);
                                wpl_show_messages(data.error, '.wpl_show_message_idx', 'wpl_green_msg');
                                wpl_idx_next_step();
                                wpl_idx_calculate_price();
                            }

                        }
                    });
            }
        });
    }
    // Showing mls package information that client choose in the choose mls section -- Payment step
    function wpl_idx_calculate_price()
    {
        wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var totalAmount = 0;
        request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=price';

        /** run ajax query **/
        var mlsProviders = [];
        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                    
                    //wplj.each(data, function (key, value) {
                    mlsProviders += '<tr class="wpl-idx-addon-table-row">';
                    mlsProviders += '<td class="logo" width="40"><img height="25" src="' + data.message.logo + '" /></td>';
                    mlsProviders += '<td class="provider" width="40">' + data.message.short_name + '</td>';
                    mlsProviders += '<td class="price_total">' + data.message.price + '$ <?php echo __("Per Month"); ?> ' + '<input type="hidden" value="'+50 +'"></td>';
                    mlsProviders += '</tr>';
                    totalAmount = parseInt(data.message.price);

                    //});
                    Realtyna.ajaxLoader.hide(loader);
                    wplj('#wpl-idx-selected-mls-providers tbody').html("");
                    wplj('#wpl-idx-selected-mls-providers tbody').append(mlsProviders);
                    wplj('#wpl-idx-total-price-payment .price').html(totalAmount+'$');
                }
            });
    }
    /*Configuration table*/
    function wpl_idx_show_configuration_list()
    {
        /*If the configuration table already loaded*/
        if (wplj('#wpl-idx-selected-mls-providers-configuration').hasClass('loaded')) return false;

        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var config_form ="";
        wpl_remove_message('.wpl_show_message_idx');
        request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=price';

        /** run ajax query **/
        var mlsProviders = [];
        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                    //wplj.each(data.price_list, function (key, value) {
                    config_form = wpl_idx_generate_config_form(data);
                     
                    mlsProviders += '<div id="'+data.message.mls_id+'" class="wpl-idx-addon-table-row">';
                    mlsProviders += '<div class="mls_info">';
                    mlsProviders += '<span class="logo" width="40"><img height="25" src="' + data.message.logo + '" /></span>';
                    mlsProviders += '<span id="provider" class="provider">' + data.message.short_name + '</span>';
                    mlsProviders += '<span class="provider_full_name">' + data.message.name + '</span>';
                    mlsProviders += '</div>';
                    mlsProviders += '<div id="config_form" class="wpl-idx-config-row">'+config_form+'</div>';
                    mlsProviders += '</div>';
                    //});
                    Realtyna.ajaxLoader.hide(loader);
                    wplj('#wpl-idx-selected-mls-providers-configuration').html("");
                    wplj('#wpl-idx-selected-mls-providers-configuration').append(mlsProviders).addClass("loaded");
                }
            });
    }
    /*Payment*/
    function wpl_idx_payment()
    {
        wpl_remove_message('.wpl_show_message_idx');

        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var request_str = '';
        var messages = '';
        var row = '#wpl-idx-selected-mls-providers .wpl-idx-addon-table-row';
        var url = window.location;

        wplj(row).each(function(){
            var mls = wplj(this).find('.provider').html();
            request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=get_keys';
            /** run ajax query **/
            wplj.ajax(
                {
                    type: "POST",
                    url: '<?php echo wpl_global::get_full_url(); ?>',
                    data: request_str,
                    success: function (data) {
                        if (data.status == 200) {
                        Realtyna.ajaxLoader.hide(loader);
                        encodedurl = btoa(window.location);
                        window.location.replace("https://payment.realtyna.com/"+data.message.user_id+'/'+data.message.provider_id+'/'+data.message.token+'/'+encodedurl);
                        }
                        
                    }
                });
        });
    }
    /*Insert Configuration*/
    function wpl_idx_configuration()
    {

        wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var request_str = "";
        var row = '#wpl-idx-selected-mls-providers-configuration .wpl-idx-addon-table-row';

        //wplj('#wpl-idx-selected-mls-providers-configuration .wpl-idx-addon-table-row').each(function(){
        var mls_id         = wplj(row).attr('id');
        var provider       = wplj(row).find('#provider').html();
        var agent_id       = wplj(row).find('#agent_id').val();
        var office_id      = wplj(row).find('#office_id').val();
        var agent_name     = wplj(row).find('#agent_name').val();
        var office_name    = wplj(row).find('#office_name').val();

        var import_status  = (wplj(row).find('#import_status').is(':checked')) ? 1 : 0;
        var listing_status = (wplj(row).find('#listing_status').is(':checked')) ? 0 : 1;
        var office_listing = (wplj(row).find('#office_listing').is(':checked')) ? 1 : 0;
        var agent_listing  = (wplj(row).find('#agent_listing').is(':checked')) ? 1 : 0;
        var all_listing    = (wplj(row).find('#all_listing').is(':checked')) ? 1 : 0;

        var property_type;
        if(!wplj("#category").val()) property_type = ""; else property_type = wplj("#category").val();

        var errors = '';

        request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=configuration';
        request_str += '&mls_id='+mls_id+'&provider='+provider+'&agent_id='+agent_id+'&office_id='+office_id+
        '&agent_name='+agent_name+'&office_name='+office_name+
        '&property_type='+property_type+
        '&import_status='+import_status+'&listing_status='+listing_status+'&office_listing='+office_listing+
        '&agent_listing='+agent_listing+'&all_listing='+all_listing;

        /** run ajax query **/
        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {

                    if(data.status == 400)
                    {
                        if(typeof data.message == 'string')
                        {
                            errors = data.message;
                        }
                        else
                        {
                            wplj.each(data.message, function (key, value) {
                                errors += key+': '+value;
                                errors += '<br/>';
                            });
                        }
                        wpl_show_messages(errors, '.wpl_show_message_idx', 'wpl_red_msg');
                    }
                    if(data.status == 200 || data.status == 201)
                    {
                        wpl_show_messages(data.message, '.wpl_show_message_idx', 'wpl_green_msg');
                        wpl_idx_wizard_thank_you();
                    }

                    Realtyna.ajaxLoader.hide(loader);
                }

            });
        //});
    }
    /*Generate configuration form*/
    function wpl_idx_generate_config_form(property_types)
    {
        var options;

        wplj.each(property_types.category, function (key, value) {
            options +=' <option value="'+value.category+'">'+value.category+'</option>';
        });

        var config_form = '<div class="wpl-idx-config-form" style="display: none">';
        config_form += '<div class="wpl-idx-config-form-part1 clearfix">';
        config_form +='<div class="wpl-small-12 wpl-medium-6 wpl-large-6 wpl-column">';
        config_form +='<div class="wpl-idx-form-checkbox">';
        config_form +='<input id="active_listings_checkbox" type="checkbox" class="yesno" checked="checked">';
        config_form += '<?php echo __('Import all active listings', 'real-estate-listing-realtyna-wpl')?>';
        config_form +='</div>';
        config_form +='<div class="wpl-idx-form-checkbox">';
         config_form +='<input id="configure_checkbox" type="checkbox" class="yesno">';
         config_form += '<?php echo __('Configure', 'real-estate-listing-realtyna-wpl')?>';
        config_form +='</div>';
        config_form +='</div>';
        config_form +='<div class="wpl-small-12 wpl-medium-6 wpl-large-6 wpl-column">';
        config_form +='<div class="wpl-idx-form wpl-row">';
        config_form +='<div class="wpl-small-6 wpl-medium-6 wpl-large-6 wpl-column">';
        config_form +='<div class="wpl-idx-form-element">';
        config_form +='<span class="wpl-idx-icon agent-icon"></span>';
        //config_form +='<span class="wpl-idx-icon tooltip-icon wpl_setting_form_tooltip wpl_help">';
        //config_form += '<span class="wpl_help_description"><?php echo __('The agent id should be real, In order to find out about it your should click here.', 'real-estate-listing-realtyna-wpl')?></span>';
        //config_form += '</span></span>';
        config_form +='<input id="agent_name" type="text" placeholder="Agent Name">';
        config_form +='</div></div>';
        config_form +='<div class="wpl-small-6 wpl-medium-6 wpl-large-6 wpl-column">';
        config_form +='<div class="wpl-idx-form-element">';
        config_form +='<span class="wpl-idx-icon agent-icon"></span>';
        config_form +='<input id="agent_id" type="text" placeholder="Agent ID">';
        config_form +='</div></div>';
        config_form +='<div class="wpl-small-6 wpl-medium-6 wpl-large-6 wpl-column">';
        config_form +='<div class="wpl-idx-form-element">';
        config_form +='<span class="wpl-idx-icon office-icon"></span>';
        config_form +='<input id="office_name" type="text" placeholder="Office Name">';
        config_form +='</div></div>';
        config_form +='<div class="wpl-small-6 wpl-medium-6 wpl-large-6 wpl-column">';
        config_form +='<div class="wpl-idx-form-element">';
        config_form +='<span class="wpl-idx-icon office-icon"></span>';
        config_form +='<input id="office_id" type="text" placeholder="Office ID">';
        config_form +='</div></div>';
        config_form +='</div>';
        config_form +='</div>';
        config_form +='</div>';
        config_form +='<div class="wpl-idx-config-form-part2 clearfix" style="display: none">';
        config_form +='<div class="wpl-small-12 wpl-medium-6 wpl-large-3 wpl-column">';
        config_form +='<div class="wpl-idx-form-checkbox">';
        config_form +='<input id="all_listing" type="checkbox" class="yesno">';
        config_form += '<?php echo __('Import all the listings', 'real-estate-listing-realtyna-wpl')?>';
        config_form +='</div>';
        config_form +='<div class="wpl-idx-form-checkbox">';
        config_form +='<input id="office_listing" type="checkbox" class="yesno">';
        config_form += '<?php echo __('Import office listings only', 'real-estate-listing-realtyna-wpl')?>';
        config_form +='</div>';
        config_form +='<div class="wpl-idx-form-checkbox">';
        config_form +='<input id="agent_listing" type="checkbox" class="yesno">';
        config_form += '<?php echo __('Import agent listings only', 'real-estate-listing-realtyna-wpl')?>';
        config_form +='</div>';
        config_form +='<div class="wpl-idx-form-checkbox">';
        config_form +=' <input id="listing_status" type="checkbox" class="yesno">';
        config_form += '<?php echo __('I want the sold data as well', 'real-estate-listing-realtyna-wpl')?>';
        config_form +='</div>';
        config_form +='</div>';

        return config_form;

    }
    /*messages*/
    function wpl_idx_wizard_thank_you() {
        wplj('.wpl-idx-wizard').remove();
        wplj('.wpl-idx-thank-you').remove();

        var success = '<div class="wpl-idx-thank-you">';
        success += '<h4 class="title"><?php echo __('Thank You!','real-estate-listing-realtyna-wpl'); ?></h4>';
        success += '<p><?php echo __('Your request for adding MLS properties has been received. In order to check the status of importing properties click here:','real-estate-listing-realtyna-wpl'); ?></p>';
        success += '<a class="wpl-button button-1" href="<?php echo wpl_global::add_qs_var('tpl', 'setting'); ?>"><?php echo __('Check status','real-estate-listing-realtyna-wpl'); ?></a>';
        success += '<div>';

        wplj('.wpl-idx-wizard-main .panel-body').append(success);
    }
    function wpl_idx_wizard_already_registered() {
        wplj('.wpl-idx-wizard').remove();
        wplj('.wpl-idx-thank-you').remove();

        var success = '<div class="wpl-idx-thank-you">';
        success += '<h4 class="title"><?php echo __('You Already Registered!','real-estate-listing-realtyna-wpl'); ?></h4>';
        success += '<p><?php echo __('Your request for adding MLS properties has been already registered in the system. In order to check the status of importing properties click here:','real-estate-listing-realtyna-wpl'); ?></p>';
        success += '<a class="wpl-button button-1" href="<?php echo wpl_global::add_qs_var('tpl', 'setting'); ?>"><?php echo __('Check status','real-estate-listing-realtyna-wpl'); ?></a>';
        success += '<div>';
        wplj('.wpl-idx-wizard-main .panel-body').append(success);
    }
    function wpl_idx_wizard_thank_you_trial() {
        wplj('.wpl-idx-wizard').remove();
        wplj('.wpl-idx-thank-you').remove();

        var success = '<div class="wpl-idx-thank-you">';
        success += '<h4 class="title"><?php echo __('Thank You!','real-estate-listing-realtyna-wpl'); ?></h4>';
        success += '<p><?php echo __('All properties are imported. In order to see your properties please click here:','real-estate-listing-realtyna-wpl'); ?></p>';
        success += '<a class="wpl-button button-1" href="<?php echo wpl_global::get_wpl_admin_menu('wpl_admin_listings'); ?>"><?php echo __('Listing Manager','real-estate-listing-realtyna-wpl'); ?></a>';
        success += '<div>';

        wplj('.wpl-idx-wizard-main .panel-body').append(success);
    }
    function wpl_idx_wizard_already_used_trial()
    {
        wplj('.wpl-idx-wizard').remove();
        wplj('.wpl-idx-thank-you').remove();

        var success = '<div class="wpl-idx-thank-you">';
        success += '<h4 class="title"><?php echo __('You already used trial version!','real-estate-listing-realtyna-wpl'); ?></h4>';
        success += '<p><?php echo __('You may already used the trial version or you may have valid version purchased.','real-estate-listing-realtyna-wpl'); ?></p>';
        success += '<a class="wpl-button button-1" href="<?php echo wpl_global::get_wpl_admin_menu('wpl_admin_listings'); ?>"><?php echo __('Listing Manager','real-estate-listing-realtyna-wpl'); ?></a>';
        //check if trial is reseted
         var is_trial_reset = <?php echo (get_option('wpl_addon_idx_trial_reseted') == 1) ? 1: 0; ?>
         
         if (is_trial_reset == 0) {
            success += '<a class="wpl-button button-1" href="#" onclick="wpl_idx_reset_trial();"><?php echo __('Reset','real-estate-listing-realtyna-wpl'); ?></a>';
         }

        success += '<div>';

        wplj('.wpl-idx-wizard-main .panel-body').append(success);
    }


    function wpl_idx_wizard_php_version() {
        wplj('.wpl-idx-wizard').remove();
        wplj('.wpl-idx-thank-you').remove();

        var success = '<div class="wpl-idx-thank-you">';
        success += '<h4 class="title"><?php echo __('PHP >= 5.5 is required.','real-estate-listing-realtyna-wpl'); ?></h4>';
        success += '<div>';
        wplj('.wpl-idx-wizard-main .panel-body').append(success);
    }
    /*import sample properties in Trial version*/
    function wpl_idx_load_trial_data() {
        wpl_idx_next_step();  
        wpl_remove_message('.wpl_show_message_idx');
        //var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=load_trial_data';

        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                   if (data.status == 201) {
                    wpl_idx_wizard_thank_you_trial();
                   }
                   
                }
             });
    }
    /*Check if the Trial version is used once go to thank you page*/
    function wpl_idx_protect_trial()
    {
        wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=protect_idx_trial';

        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                    Realtyna.ajaxLoader.hide(loader);
                    
                    if(data.status == 200)
                    {
                        wpl_idx_wizard_already_used_trial();
                    }else{
                        wpl_idx_check_trial_registration();
                    }
                }

            });

    }
    /*Settings page*/
    function wpl_idx_setting_table() {

        wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main #wpl-idx-setting-table .message', 'normal', 'center', true);
        var totalAmount = 0;
        request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=status';

        /** run ajax query **/
        var mlsProviders = [];
        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {

                    // wplj.each(data.response, function (key, value) {
                       
                       if( data.status == 200) {
                        value = data.message;

                          mlsProviders += '<tr class="wpl-idx-addon-table-row">';
                        mlsProviders += '<td class="logo" width="40"><img height="25" src="' + value.logo + '"/></td>';
                        mlsProviders += '<td class="provider" width="40">' + value.short_name + '</td>';
                        mlsProviders += '<td class="provider-full-name">' + value.name + '</td>';
                        mlsProviders += '<td class="status '+ value.configStatus +'">' + value.configStatus + '</td>';
                        // mlsProviders += '<td class="actions"><a href="#" onclick="wpl_idx_delete(0);"><?php echo __('delete','real-estate-listing-realtyna-wpl'); ?></a></td>';
                        mlsProviders += '</tr>';
                       }
                    // });
                    Realtyna.ajaxLoader.hide(loader);
                    if(mlsProviders.length)
                    {
                        wplj('#wpl-idx-setting-table tbody').html("");
                        wplj('#wpl-idx-setting-table tbody').append(mlsProviders);
                    }
                }
            });
    }
    /*Delete the whole idx request. All configuration will be reset*/
    function wpl_idx_delete(confirmed)
    {
        if (!confirmed)
        {
            message = "<?php echo __('Are you sure you want to remove this item?', 'real-estate-listing-realtyna-wpl'); ?>";
            message += '&nbsp;<span class="wpl_actions" onclick=" wpl_idx_delete(1);"><?php echo __('Yes', 'real-estate-listing-realtyna-wpl'); ?></span>&nbsp;<span class="wpl_actions" onclick="wpl_remove_message();"><?php echo __('No', 'real-estate-listing-realtyna-wpl'); ?></span>';
            wpl_show_messages(message, '.wpl_idx_servers_list .wpl_show_message');
            return false;
        }
        else
        {
            wpl_remove_message();
            var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main #wpl-idx-setting-table', 'normal', 'center', true);
            request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=delete';

            /** run ajax query **/
            wplj.ajax(
                {
                    type: "POST",
                    url: '<?php echo wpl_global::get_full_url(); ?>',
                    data: request_str,
                    success: function (data) {
                        wplj('.wpl-idx-wizard-main #wpl-idx-setting-table tbody').html('<tr><td colspan="4"><div class="message"><?php echo __('No MLS Provider is Found! In order to add one please ', 'real-estate-listing-realtyna-wpl').'<a href="'.wpl_global::get_wpl_admin_menu('wpl_addon_idx').'">'.__('Click here', 'real-estate-listing-realtyna-wpl').'</a>';?></div></td></tr>');
                        Realtyna.ajaxLoader.hide(loader);
                    }
                });
        }
    }
    /*Remove idx trial properties*/
    function wpl_idx_reset_trial() {
        wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=reset_trial';

        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                    Realtyna.ajaxLoader.hide(loader);
                    if (data.status == 201) {
                        wpl_show_messages(data.mesage, '.wpl_show_message_idx', 'wpl_green_msg');
                        window.location.reload();
                    }
                }

            });
    }
    /*Back button in the wizard*/
    function wpl_idx_back_step(step_name)
    {
        wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=back_step&step_name='+step_name;

        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                    Realtyna.ajaxLoader.hide(loader);
                    if(step_name == 'save_action')
                    {
                        wpl_idx_providers();
                    }
                }

            });
    }
    /*Request a new mls provider which is not in the list. It will send an email to info@realtyna.com*/
    function wpl_idx_request_mls()
    {
        wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);

        var state = wplj('#wpl_request_mls_state').val();
        var provider = wplj('#wpl_request_mls_provider').val();
        var request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=save_client_request&provider='+provider+'&state='+state;

        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                    Realtyna.ajaxLoader.hide(loader);
                    wplj._realtyna.lightbox.close();
                    if(data.status == '404')
                    {
                        wpl_show_messages(data.error, '.wpl_show_message_idx', 'wpl_red_msg');
                    }
                    if(data.status == '200' || data.status == '201')
                    {
                        wpl_show_messages(data.response, '.wpl_show_message_idx', 'wpl_green_msg');
                    }
                }

            });
    }
    /*Check if the payment is done skip the payment section*/
    function wpl_idx_check_payment()
    {
        wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=check_payment';

        wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                    Realtyna.ajaxLoader.hide(loader);
                    if(data.status == "200")
                    {
                        wpl_idx_goto_step(4);
                    }
                    else
                    {
                        wpl_idx_calculate_price();
                    }
                }
            });
    }

  function wpl_idx_check_trial_registration()
  {
    wpl_remove_message('.wpl_show_message_idx');
        var loader = Realtyna.ajaxLoader.show('.wpl-idx-wizard-main .panel-body', 'normal', 'center', true);
        var request_str = 'wpl_format=b:addon_idx:ajax&wpl_function=is_user_registered';
       wplj.ajax(
            {
                type: "POST",
                url: '<?php echo wpl_global::get_full_url(); ?>',
                data: request_str,
                success: function (data) {
                    Realtyna.ajaxLoader.hide(loader);
                    if (data.status == 200) {
                        wpl_idx_load_trial_data();
                    }
                    
                }
            });
  }
</script>