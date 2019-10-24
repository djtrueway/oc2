<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($show == 'advanced_locationtextsearch' and !$done_this)
{
	/** add scripts and style sheet **/
	wp_enqueue_script('jquery-ui-autocomplete');
		
	/** current value **/
	$current_value = stripslashes(wpl_request::getVar('sf_advancedlocationtextsearch', ''));
	$current_column_value = stripslashes(wpl_request::getVar('sf_advancedlocationcolumn', ''));

	/** element id **/
	$element_id = 'sf'.$widget_id.'_advancedlocationtextsearch';
	$element_column_id = 'sf'.$widget_id.'_advancedlocationcolumn';
	
	$html .= '<div class="wpl_search_widget_location_level_container" id="wpl'.$widget_id.'_search_widget_location_level_container_advanced_location_text">';
	$html .= '<input class="wpl_search_widget_location_textsearch" value="'.$current_value.'" name="'.$element_id.'" id="'.$element_id.'" placeholder="'.__($placeholder, 'real-estate-listing-realtyna-wpl').'" />';
	$html .= '<input type="hidden" value="'.$current_column_value.'" name="'.$element_column_id.'" id="'.$element_column_id.'" />';
	
	wpl_html::set_footer('<script type="text/javascript">
	var autocomplete_cache = {};
	(function($){$(function()
    {    
		$.widget( "custom.wpl_catcomplete", $.ui.autocomplete,
		{
			create: function()
			{
				this._super();
				this.widget().menu("option", "items", "> :not(.ui-autocomplete-category)");
			},
			_renderMenu: function(ul, items)
			{
				var that = this, currentCategory = "";
				$.each(items, function(index, item)
				{
				    var li;
				    if(item.title != currentCategory)
					{
						ul.append( "<li class=\'ui-autocomplete-category\'>" + item.title + "</li>" );
						currentCategory = item.title;
                    }
                    
                    li = that._renderItemData(ul, item);
                    if(item.title)
                    {
                       li.attr( "aria-label", item.title + " : " + item.value );
                    }
				});
			 }
		});
		
		$("#'.$element_id.'").wpl_catcomplete(
		{
			search : function(){},
			open : function(){$(this).removeClass("ui-corner-all").addClass("ui-corner-top");},
			close : function(){$(this).removeClass("ui-corner-top").addClass("ui-corner-all");},
			select: function (event, ui) 
			{
				wplj("#'.$element_id.'").val(ui.item.value);
				wplj("#'.$element_column_id.'").val(ui.item.column);
				wpl_do_search_'.$widget_id.'();
			},
			source: function(request, response)
			{
				var term = request.term.toUpperCase(), items = [];
				for(var key in autocomplete_cache)
				{
					if(key === term)
					{
						response(autocomplete_cache[key]);
						return;
					}
				}
				
				$.ajax(
				{
					type: "GET",
					url: "'.wpl_global::get_wp_site_url().'?wpl_format=f:property_listing:ajax&wpl_function=advanced_locationtextsearch_autocomplete&term="+request.term,
					contentType: "application/json; charset=utf-8",
					success: function (msg)
					{
					   response($.parseJSON(msg));
					   autocomplete_cache[request.term.toUpperCase()] = $.parseJSON(msg);
					},
					error: function (msg)
					{
					}
				});
			},
			width: 260,
			matchContains: true,
			minChars: 1,
			delay: 300
			});
		});
	})(jQuery);
	</script>');
    
	$html .= '</div>';
	$done_this = true;
}