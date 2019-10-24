<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');

if($type == 'googlemap' and !$done_this)
{
    /** WPL Demographic addon **/
    $demographic_addon_objects = array();
    $demographic_objects = array();
    if(wpl_global::check_addon('demographic'))
    {
        _wpl_import('libraries.addon_demographic');
        $demographic = new wpl_addon_demographic();
        $demographic_addon_objects = $demographic->get_all('id, name');

        $demographic_objects = wpl_items::get_items($item_id, 'demographic', $kind);
    }
    
    $w = 450;
    $h = 300;
    $ln_table_col = 'googlemap_ln';
    $lt_table_col = 'googlemap_lt';
    
    // Load Google Maps API
    wpl_global::include_google_maps();
?>
<script type="text/javascript">
jQuery(document).ready(function()
{
    try
    {
        wplj(".wpl_listing_all_location_container_locations, .wpl_c_field_42, .wpl_c_post_code, .wpl_c_street_no").change(function()
        {
            wpl_address_creator();
            wpl_code_address(wplj("#wpl_map_address<?php echo $field->id; ?>").val());
        });
    }
    catch (err) {}
});

var pw_map = '';
var pw_marker = '';
var polygonsArray = [];
var polylinesArray = [];
var bounds;

function wpl_initialize()
{
    if (pw_map != '') return;
    
    var lt_orig = '<?php echo $values['googlemap_lt']; ?>';
    var ln_orig = '<?php echo $values['googlemap_ln']; ?>';

    if (lt_orig == 0 || ln_orig == 0)
    {
        lt = 90;
        ln = 90;
    }
    else
    {
        lt = lt_orig;
        ln = ln_orig;
    }
    
    /** create empty LatLngBounds object **/
    bounds = new google.maps.LatLngBounds();
    
    var marker_position = new google.maps.LatLng(lt, ln);
    var myOptions = {
        scrollwheel: false,
        zoom: <?php echo (int) wpl_global::get_setting('wizard_map_zoomlevel'); ?>,
        center: marker_position,
        mapTypeId: google.maps.MapTypeId.ROADMAP
    }

    pw_map = new google.maps.Map(document.getElementById("wpl_map_canvas<?php echo $field->id; ?>"), myOptions);
    
    <?php if(wpl_global::check_addon('demographic')): ?>
    /** restore the zoom level after the map is done scaling **/
    var pw_listener = google.maps.event.addListener(pw_map, 'idle', function(event)
    {
        pw_map.fitBounds(bounds);
        google.maps.event.removeListener(pw_listener);
    });
    <?php endif; ?>
    
    /** marker **/
    pw_marker = new google.maps.Marker(
    {
        position: marker_position,
        map: pw_map,
        draggable: true,
        title: "<?php echo addslashes(__('Position of property', 'real-estate-listing-realtyna-wpl')); ?>"
    });
    
    /** extend the bounds **/
    bounds.extend(pw_marker.position);
    
    google.maps.event.addListener(pw_marker, "dragend", function(event)
    {
        var curpos = event.latLng;
        var x = curpos.lng();
        var y = curpos.lat();

        wplj(".wpl_c_googlemap_ln").attr('value', x);
        wplj(".wpl_c_googlemap_lt").attr('value', y);

        ajax_save('wpl_properties', '<?php echo $lt_table_col; ?>', y, <?php echo $item_id; ?>);
        ajax_save('wpl_properties', '<?php echo $ln_table_col; ?>', x, <?php echo $item_id; ?>);
    });

    if (lt_orig == 0 || ln_orig == 0)
    {
        address = wplj('#wpl_map_address<?php echo $field->id; ?>').val();
        wpl_code_address(address);
    }
    
    <?php if(wpl_global::check_addon('demographic')): ?>
    init_dmgfc();
    <?php endif; ?>
}

function wpl_code_address(address)
{
    if (wplj.trim(address) == '') return;
    if (pw_map == '') return;

    geocoder = new google.maps.Geocoder();
    geocoder.geocode({'address': address}, function(results, status)
    {
        if (status === google.maps.GeocoderStatus.OK)
        {
            pw_map.setCenter(results[0].geometry.location);
            pw_marker.setPosition(results[0].geometry.location);

            var curpos = pw_marker.getPosition();
            var x = curpos.lng();
            var y = curpos.lat();

            wplj(".wpl_c_googlemap_ln").attr('value', x);
            wplj(".wpl_c_googlemap_lt").attr('value', y);

            ajax_save('wpl_properties', '<?php echo $lt_table_col; ?>', y, <?php echo $item_id; ?>);
            ajax_save('wpl_properties', '<?php echo $ln_table_col; ?>', x, <?php echo $item_id; ?>);
        }
        else
        {
            wpl_show_messages("<?php echo addslashes(__('Geocode was not successful for the following reason:', 'real-estate-listing-realtyna-wpl')); ?> : " + status, '.wpl_pwizard_googlemap_message .wpl_show_message', 'wpl_gold_msg');
            setTimeout(function(){wpl_remove_message('.wpl_pwizard_googlemap_message .wpl_show_message')}, 3000);
        }
    });
}

jQuery(document).ready(function()
{
    if(wplj('#wpl_map_canvas<?php echo $field->id; ?>').is(':visible')) wpl_initialize();
    
    wplj("#wpl_slide_label_id<?php echo $field->category; ?>").click(function()
    {
        wpl_initialize();
    });

    wpl_address_creator();

    wplj('.autocomplete-w1').click(function()
    {
        wpl_address_creator();
    });
});

function wpl_address_creator()
{
    var orig_address = wplj('#wpl_map_address<?php echo $field->id; ?>').val();
    var address = '';

    // Location levels
    for (i = 7; i >= 1; i--)
    {
        try
        {
            if (wplj("#wpl_listing_location" + i + "_select").val() != '0' && wplj.trim(wplj("#wpl_listing_location" + i + "_select").val()) != '')
            {
                if (!isNaN(wplj("#wpl_listing_location" + i + "_select").val()))
                    address += wplj("#wpl_listing_location" + i + "_select option:selected").text() + ', ';
                else
                    address += wplj("#wpl_listing_location" + i + "_select").val() + ', ';
            }

        }
        catch (err) {}
    }

    // Zipcode
    try
    {
        if (wplj("#wpl_listing_locationzips_select").val() != '0' && wplj.trim(wplj("#wpl_listing_locationzips_select").val()) != '')
        {
            if (wplj("#wpl_listing_locationzips_select").prop('tagName').toLowerCase() == 'select')
                address = wplj("#wpl_listing_locationzips_select option:selected").text() + ', ' + address;
            else
                address = wplj("#wpl_listing_locationzips_select").val() + ', ' + address;
        }
    }
    catch (err) {}

    // Street
    try
    {
        if (wplj(".wpl_c_field_42").length && wplj.trim(wplj(".wpl_c_field_42").val()) != '')
            address = wplj(".wpl_c_field_42").val() + ', ' + address;
    }
    catch (err) {}

    // Street number
    try
    {
        if (wplj(".wpl_c_street_no").length && wplj.trim(wplj(".wpl_c_street_no").val()) != '')
            address = wplj(".wpl_c_street_no").val() + ', ' + address;
    }
    catch (err) {}

    // Postal Code
    try
    {
        if (wplj(".wpl_c_post_code").length && wplj.trim(wplj(".wpl_c_post_code").val()) != '')
            address = wplj(".wpl_c_post_code").val() + ', ' + address;
    }
    catch (err) {}

    if (address.substring(address.length - 2) == ', ')
        address = address.substring(0, address.length - 2);
    
    wplj('#wpl_map_address<?php echo $field->id; ?>').val(address);
    if (orig_address != address) wpl_code_address(address);
}

function init_dmgfc()
{
    drawingManager = new google.maps.drawing.DrawingManager(
    {
        drawingControl: true,
        drawingControlOptions:
        {
            position: google.maps.ControlPosition.TOP_CENTER,
            drawingModes: [
                google.maps.drawing.OverlayType.POLYGON,
                google.maps.drawing.OverlayType.POLYLINE
            ]
        },
        polygonOptions:
        {
            strokeColor: '#1e74c7',
            strokeOpacity: 0.6,
            strokeWeight: 1,
            editable: true,
            fillColor: '#1e90ff',
            fillOpacity: 0.3
        },
        polylineOptions:
        {
            strokeColor: '#1e74c7',
            strokeOpacity: 1.0,
            strokeWeight: 2,
            editable: true
        },
        map: pw_map
    });
    
    wplj('#wpl_map_canvas<?php echo $field->id; ?>').addClass('wpl-dmgfc-addon');
    wplj('.wpl-map-add-ons').prepend('<div class="wpl_dmgfc_container"></div>');
    
    google.maps.event.addListener(drawingManager, 'overlaycomplete', function(event)
    {
        drawingManager.setOptions({drawingMode: null});
        
        var overlay = event.overlay;
        wpl_dmgfc_set_boundaries(overlay, event.type);
        
        if(event.type === google.maps.drawing.OverlayType.POLYGON)
        {
            /** delete overlays **/
            for(var i = 0; i < polygonsArray.length; i++) polygonsArray[i].setMap(null);
            polygonsArray = new Array();
            
            /** push to array **/
            polygonsArray.push(overlay);
        }
        else if(event.type === google.maps.drawing.OverlayType.POLYLINE)
        {
            /** delete overlays **/
            for(var i = 0; i < polylinesArray.length; i++) polylinesArray[i].setMap(null);
            polylinesArray = new Array();
            
            /** push to array **/
            polylinesArray.push(overlay);
        }
        
        /** POLYGON **/
        if(event.type === google.maps.drawing.OverlayType.POLYGON)
        {
            overlay.getPaths().forEach(function(path, index)
            {
                google.maps.event.addListener(path, 'insert_at', function()
                {
                    wpl_dmgfc_set_boundaries(overlay, google.maps.drawing.OverlayType.POLYGON);
                });

                google.maps.event.addListener(path, 'remove_at', function()
                {
                    wpl_dmgfc_set_boundaries(overlay, google.maps.drawing.OverlayType.POLYGON);
                });

                google.maps.event.addListener(path, 'set_at', function()
                {
                    wpl_dmgfc_set_boundaries(overlay, google.maps.drawing.OverlayType.POLYGON);
                });
            });
        }
        else if(event.type === google.maps.drawing.OverlayType.POLYLINE)
        {
            google.maps.event.addListener(overlay.getPath(), 'insert_at', function()
            {
                wpl_dmgfc_set_boundaries(overlay, google.maps.drawing.OverlayType.POLYLINE);
            });

            google.maps.event.addListener(overlay.getPath(), 'remove_at', function()
            {
                wpl_dmgfc_set_boundaries(overlay, google.maps.drawing.OverlayType.POLYLINE);
            });

            google.maps.event.addListener(overlay.getPath(), 'set_at', function()
            {
                wpl_dmgfc_set_boundaries(overlay, google.maps.drawing.OverlayType.POLYLINE);
            });
        }
        
        wpl_dmgfc_toggle_remove_shapes_button('show');
    });
    
    <?php
    foreach($demographic_objects as $demographic_object)
    {
        $boundaries = $demographic->toBoundaries($demographic_object->item_extra1);
        ?>
            var demographicCoords = [];
            <?php foreach($boundaries as $boundary): ?>
            var position = new google.maps.LatLng(<?php echo $boundary['lat']; ?>, <?php echo $boundary['lng']; ?>);
            demographicCoords.push(position);
            bounds.extend(position);
            <?php endforeach; ?>
        <?php
        if(strtolower($demographic_object->item_cat) == 'polygon')
        {
        ?>
            var polygon = new google.maps.Polygon(
            {
                paths: demographicCoords,
                strokeColor: '#1e74c7',
                strokeOpacity: 0.6,
                strokeWeight: 1,
                editable: true,
                fillColor: '#1e90ff',
                fillOpacity: 0.3
            });
    
            polygon.setMap(pw_map);
    
            /** push to array **/
            polygonsArray.push(polygon);

            polygon.getPaths().forEach(function(path, index)
            {
                google.maps.event.addListener(path, 'insert_at', function()
                {
                    wpl_dmgfc_set_boundaries(polygon, google.maps.drawing.OverlayType.POLYGON);
                });

                google.maps.event.addListener(path, 'remove_at', function()
                {
                    wpl_dmgfc_set_boundaries(polygon, google.maps.drawing.OverlayType.POLYGON);
                });

                google.maps.event.addListener(path, 'set_at', function()
                {
                    wpl_dmgfc_set_boundaries(polygon, google.maps.drawing.OverlayType.POLYGON);
                });
            });
        <?php
        }
        elseif(strtolower($demographic_object->item_cat) == 'polyline')
        {
        ?>
            var polyline = new google.maps.Polyline({
                path: demographicCoords,
                strokeColor: '#1e74c7',
                strokeOpacity: 1.0,
                strokeWeight: 2,
                editable: true
            });
            
            polyline.setMap(pw_map);
    
            /** push to array **/
            polylinesArray.push(polyline);
            
            google.maps.event.addListener(polyline.getPath(), 'insert_at', function()
            {
                wpl_dmgfc_set_boundaries(polyline, google.maps.drawing.OverlayType.POLYLINE);
            });

            google.maps.event.addListener(polyline.getPath(), 'remove_at', function()
            {
                wpl_dmgfc_set_boundaries(polyline, google.maps.drawing.OverlayType.POLYLINE);
            });

            google.maps.event.addListener(polyline.getPath(), 'set_at', function()
            {
                wpl_dmgfc_set_boundaries(polyline, google.maps.drawing.OverlayType.POLYLINE);
            });
        <?php
        }
    }
    
    if(count($demographic_objects)) echo 'wpl_dmgfc_toggle_remove_shapes_button("show");';
    ?>
}

function wpl_dmgfc_set_boundaries(overlay, type)
{
    var paths = [];
    
    if(type == google.maps.drawing.OverlayType.POLYGON)
    {
        overlay.getPaths().forEach(function(path, index)
        {
            var points = path.getArray();
            for(b in points)
            {
                paths.push(new google.maps.LatLng(points[b].lat(), points[b].lng()));
            }
        });
    }
    else if(type == google.maps.drawing.OverlayType.POLYLINE)
    {
        overlay.getPath().forEach(function(path, index)
        {
            paths.push(new google.maps.LatLng(path.lat(), path.lng()));
        });
    }
    
    item_save('', <?php echo $item_id; ?>, 0, 'demographic', type, encodeURIComponent(paths.toString()));
}

function wpl_dmgfc_toggle_remove_shapes_button(method)
{
    if(typeof method == 'undefined') method = 'hide';
    
    if(method == 'hide')
    {
        wplj("#wpl_dmgfc_remove_shapes_button").remove();
    }
    else if(method == 'show')
    {
        if(!wplj('.wpl_dmgfc_container #wpl_dmgfc_remove_shapes_button').length) wplj('.wpl_dmgfc_container').append('<div id="wpl_dmgfc_remove_shapes_button" class="wpl-dmgfc-remove-shapes-btn"><button type="button" class="btn btn-primary" onclick="wpl_dmgfc_remove_shapes();"><?php echo addslashes(__('Remove Shapes!', 'real-estate-listing-realtyna-wpl')); ?></button></div>');
    }
}

function wpl_dmgfc_remove_shapes()
{
    /** Remove Polygons **/
    for(var i = 0; i < polygonsArray.length; i++) polygonsArray[i].setMap(null);
    polygonsArray = new Array();
    
    /** Remove Polylines **/
    for(var i = 0; i < polylinesArray.length; i++) polylinesArray[i].setMap(null);
    polylinesArray = new Array();
    
    wpl_dmgfc_toggle_remove_shapes_button('hide');
    
    var request_str = 'wpl_format=b:listing:ajax&wpl_function=remove_items&item_id=<?php echo $item_id; ?>&item_type=demographic&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>';
    
    /** run ajax query **/
    var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
    ajax.success(function(data)
    {
        if(data.success == 1)
        {
        }
        else if(data.success != 1)
        {
            try{eval(data.js)} catch(err){}
        }
    });
}

function wpl_dmgfc_apply_shapes()
{
    var id = wplj('#wpl_dmgfc_objects<?php echo $field->id; ?>').val();
    var request_str = 'wpl_format=b:addon_demographic:ajax&wpl_function=get_demographic&id='+id;
    wplj("#wpl_dmgfc_objects_loading").html('<img src="<?php echo wpl_global::get_wpl_asset_url('img/ajax-loader3.gif'); ?>" />');

    /** run ajax query **/
    var ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', request_str);
    ajax.success(function(data)
    {
        wpl_dmgfc_remove_shapes();

        var demographicCoords = [];
        var value = data.boundary;
        console.log(value);
        var obj = JSON.parse(value);

        for (var i = obj.length - 1; i >= 0; i--) {
            var coords = obj[i].split(',');
            var position = new google.maps.LatLng(coords[0], coords[1]);
            demographicCoords.push(position);
            bounds.extend(position);
        }

        var polygon = new google.maps.Polygon(
        {
            paths: demographicCoords,
            strokeColor: '#1e74c7',
            strokeOpacity: 0.6,
            strokeWeight: 1,
            editable: true,
            fillColor: '#1e90ff',
            fillOpacity: 0.3
        });

        polygon.setMap(pw_map);

        /** push to array **/
        polygonsArray.push(polygon);

        polygon.getPaths().forEach(function(path, index)
        {
            google.maps.event.addListener(path, 'insert_at', function()
            {
                wpl_dmgfc_set_boundaries(polygon, google.maps.drawing.OverlayType.POLYGON);
            });

            google.maps.event.addListener(path, 'remove_at', function()
            {
                wpl_dmgfc_set_boundaries(polygon, google.maps.drawing.OverlayType.POLYGON);
            });

            google.maps.event.addListener(path, 'set_at', function()
            {
                wpl_dmgfc_set_boundaries(polygon, google.maps.drawing.OverlayType.POLYGON);
            });
        });

        item_save('', <?php echo $item_id; ?>, 0, 'demographic', google.maps.drawing.OverlayType.POLYGON, encodeURIComponent(demographicCoords.toString()));
        wpl_dmgfc_toggle_remove_shapes_button('show');

        wplj("#wpl_dmgfc_objects_loading").html('');
    });
    
}
</script>
<div class="google-map-wp">
    <div class="wpl_pwizard_googlemap_message"><div class="wpl_show_message"></div></div>
    <div class="map-form-wp">
        <label for="wpl_map_address<?php echo $field->id; ?>"><?php echo __('Map point', 'real-estate-listing-realtyna-wpl'); ?> :</label>
        <input class="text-address" id="wpl_map_address<?php echo $field->id; ?>" type="text" name="address" value="" />
        <button class="wpl-button button-1" onclick="wpl_code_address(wplj('#wpl_map_address<?php echo $field->id; ?>').val());"><?php echo addslashes(__('Go', 'real-estate-listing-realtyna-wpl')); ?></button>
    </div>
    <div class="wpl-map-add-ons"></div>
    <div class="map-canvas-wp">
        <div id="wpl_map_canvas<?php echo $field->id; ?>"></div>
    </div>

    <?php if(wpl_global::check_addon('demographic') and count($demographic_addon_objects)): ?>
    <div class="dmgfc-objects-wp" style="margin-top: 10px;">
        <label for="wpl_dmgfc_objects<?php echo $field->id; ?>"><?php echo __('Apply boundary from Demographic addon', 'real-estate-listing-realtyna-wpl'); ?></label>
        <select id="wpl_dmgfc_objects<?php echo $field->id; ?>">
            <?php foreach ($demographic_addon_objects as $object): ?>
                <option value="<?php echo $object->id; ?>"><?php echo $object->name; ?></option>
            <?php endforeach; ?>
        </select>
        <input type="button" class="wpl-button button-1" onclick="wpl_dmgfc_apply_shapes()" value="<?php echo __('Apply', 'real-estate-listing-realtyna-wpl'); ?>">
        <div id="wpl_dmgfc_objects_loading"></div>
    </div>
    <?php endif; ?>
</div>
<?php
    $done_this = true;
}
