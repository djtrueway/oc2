<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

if($type == 'gallery' and !$done_this)
{
    $extentions = explode(',', $options['ext_file']);
	$ext_str = '';
    foreach($extentions as $extention) $ext_str .= $extention . '|';

    // remove last |
    $ext_str = substr($ext_str, 0, -1);
    $ext_str = rtrim($ext_str, ';');
    $max_size = $options['file_size'];

    // Load Handlebars Templates
    echo wpl_global::load_js_template('dbst-wizard-gallery');
?>
<div class="video-tabs-wp" id="gallery-tabs-wp-container">
	<ul>
        <li id="wpl_gallery_uploader_tab" onclick="wpl_gallery_select_tab('wpl_gallery_uploader_tab', 'wpl_gallery_uploader'); return false;" class="active"><a href="#wpl_gallery_uploader"><?php echo __('Image uploader', 'real-estate-listing-realtyna-wpl'); ?></a></li>
		<li id="wpl_gallery_external_tab" onclick="wpl_gallery_select_tab('wpl_gallery_external_tab', 'wpl_gallery_external'); return false;"><a href="#wpl_gallery_external"><?php echo __('External images', 'real-estate-listing-realtyna-wpl'); ?></a></li>
	</ul>
</div>

<div class="gallary-btn-wp">
    <div id="wpl_gallery_uploader" class="wpl_gallery_method_container">
        <div class="wpl-button button-1 button-upload">
            <span><?php echo __('Select files', 'real-estate-listing-realtyna-wpl'); ?></span>
            <!-- The file input field used as target for the file upload widget -->
            <input id="fileupload" type="file" name="files[]" multiple="multiple" />
        </div>
        <div class="field-desc">
            <?php echo __('To select images click on "Select files."', 'real-estate-listing-realtyna-wpl'); ?>
        </div>
    </div>
    <div id="wpl_gallery_external" class="wpl_gallery_method_container" style="display: none;">
        <?php if(!wpl_global::check_addon('pro')): ?>
        <div class="field-desc">
            <?php echo __('Pro addon must be installed for this!', 'real-estate-listing-realtyna-wpl'); ?>
        </div>
        <?php else: ?>
        <button class="wpl-button button-1" onclick="add_external_image();"><?php echo __('Add image', 'real-estate-listing-realtyna-wpl') ?></button>
        <div class="field-desc">
            <?php echo __('To insert images click on "Add image."', 'real-estate-listing-realtyna-wpl'); ?>
        </div>
        <div id="wpl_gallery_external_cnt" style="margin-top: 10px; display: none;">
            <div class="gallery-external-wp" id="gallery-external-cnt">
                <div class="row">
                    <label for="gallery_external_link"><?php echo __('Image links', 'real-estate-listing-realtyna-wpl'); ?></label>
                    <textarea name="gallery_external_link${count}" rows="8" cols="50" id="gallery_external_link" placeholder="<?php echo __('Enter each image link in a new line', 'real-estate-listing-realtyna-wpl'); ?>"></textarea>
                    <button class="wpl-button button-1" onclick="wpl_gallery_external_save();"><?php echo __('Save', 'real-estate-listing-realtyna-wpl'); ?></button>
                </div>
            </div>
        </div>
        <?php endif; ?>
    </div>
</div>

<!-- The global progress bar -->
<div id="progress_img">
    <div id="progress" class="progress progress-success progress-striped">
        <div class="bar"></div>
    </div>
</div>

<div class="error_uploaded_message" id="error_ajax_img">
</div>

<!-- The container for the uploaded files -->
<div id="files" class="gallary-images-wp wpl_files_container">
    <div class="wpl-mass-actions">
        <button class="wpl-button button-1" onclick="wpl_gallery_delete_all();"><?php echo __('Delete all images', 'real-estate-listing-realtyna-wpl'); ?></button>
    </div>
    <ul class="ui-sortable" id="ajax_gal_sortable">
        <?php
        // get uploaded images and show them
        $gall_items = wpl_items::get_items($item_id, 'gallery', $this->kind, '', '');
		
        // Get blog ID of property
        $blog_id = wpl_property::get_blog_id($item_id);
        
        // Media Confirm Status
        $media_confirm = wpl_global::get_setting('listing_media_confirm');
    
        $image_folder = wpl_items::get_folder($item_id, $this->kind, $blog_id);
        $image_path = wpl_items::get_path($item_id, $this->kind, $blog_id);
        $image_categories = wpl_items::get_item_categories('gallery', $this->kind);
        $max_img_index = 0;
		
        foreach($gall_items as $image)
		{
            $image->index = intval($image->index);
            if($max_img_index < $image->index) $max_img_index = $image->index;
			
			/** set resize method parameters **/
			$params = array();
			$params['image_name'] = $image->item_name;
			$params['image_parentid'] = $image->parent_id;
			$params['image_parentkind'] = $image->parent_kind;
			$params['image_source'] = $image_path.$image->item_name;
			
			$image_thumbnail_url = wpl_images::create_gallery_image(80, 60, $params, 1, 0);
            if($image->item_cat == 'external') $image_thumbnail_url = $image->item_extra3;
            ?>

            <li class="ui-state-default" id="ajax_gallery<?php echo $image->index; ?>">
                <input type="hidden" class="gal_name" value="<?php echo $image->item_name; ?>" />
                <div class="image-box-wp">
                    <div class="image-wp">
                        <img src="<?php echo $image_thumbnail_url; ?>" width="80" height="60" />
                    </div>
                    <div class="info-wp">
                        <div class="row">
                            <label for=""><?php echo __('Image Title', 'real-estate-listing-realtyna-wpl') ?>:</label>
                            <input type="text" class="gal_title" value="<?php echo $image->item_extra1; ?>" onchange="ajax_gallery_title_update('<?php echo $image->item_name; ?>', this.value);" size="20" />
                        </div>
                        <div class="row">
                            <label for=""><?php echo __('Image Description', 'real-estate-listing-realtyna-wpl'); ?>:</label>
                            <input type="text" class="gal_desc" value="<?php echo $image->item_extra2; ?>" onchange="ajax_gallery_desc_update('<?php echo $image->item_name; ?>', this.value);" size="50" />
                        </div>
                        <div class="row">
                            <label for=""><?php echo __('Image Category', 'real-estate-listing-realtyna-wpl'); ?>:</label>
                            <select name="img_cat" class="gal_cat" onchange="ajax_gallery_cat_update('<?php echo $image->item_name; ?>', this.value);">
							<?php
								foreach($image_categories as $img_cat)
								{
									echo '<option value="'.$img_cat->category_name.'"';
									if($image->item_cat == $img_cat->category_name) echo ' selected="selected"';
									echo '>'.$img_cat->category_name.'</option>';
								}
                            ?>
                            </select>
                        </div>
                    </div>
                    <div class="actions-wp">
                        <div class="action-gal-btn">
                            <i class="action-btn icon-move"></i>
                        </div>
                        <div class="action-gal-btn ajax_gallery_middle_td" onclick="ajax_gallery_image_delete('<?php echo $image->item_name; ?>', 'ajax_gallery<?php echo $image->index ?>');">
                            <i class="action-btn icon-recycle"></i>
                        </div>
                        <?php
                            if($image->enabled and $media_confirm) echo '<div class="action-gal-btn" id="active_image_tag_' . $image->index . '" onclick="wpl_image_enabled(\'' . $image->item_name . '\',' . $image->index . ');"><i class="action-btn icon-enabled" title="'.__('Enabled', 'real-estate-listing-realtyna-wpl').'"></i></div>';
                            elseif($media_confirm) echo '<div class="action-gal-btn" id="active_image_tag_' . $image->index . '" onclick="wpl_image_enabled(\'' . $image->item_name . '\',' . $image->index . ');"><i class="action-btn icon-disabled" title="'.__('Disabled', 'real-estate-listing-realtyna-wpl').'"></i></div>';
                        ?>
                        <input type="hidden" id="enabled_image_field_<?php echo $image->index; ?>" value="<?php echo $image->enabled; ?>"/>
                    </div>
                </div>
            </li>
            <?php
        }
        ?>
    </ul>
    <?php
		$image_categories_html = '';
		foreach ($image_categories as $img_cat) {
			$image_categories_html .= ' <option value="' . $img_cat->category_name . '">' . __($img_cat->category_name, 'real-estate-listing-realtyna-wpl') . '</option>';
		}
    ?>
</div>

<script type="text/javascript">
jQuery(document).ready(function()
{
	wpl_gallery_sortable();
});

function wpl_gallery_sortable(destroy)
{
    if(typeof destroy == 'undefined') destroy = false;
    if(destroy) wplj("#ajax_gal_sortable").sortable('destroy');
    
    wplj("#ajax_gal_sortable").sortable(
	{
		placeholder: "wpl-sortable-placeholder",
        opacity: 0.7,
        forcePlaceholderSize: true,
        cursor: "move",
        axis: "y",
		stop: function(event, ui)
		{
			sort_str = "";
			wplj("#ajax_gal_sortable .gal_name").each(function(ind, elm) {
				sort_str += elm.value + ",";
			});
	
			wplj.post("<?php echo wpl_global::get_full_url(); ?>", "&wpl_format=b:listing:gallery&wpl_function=sort_images&pid=<?php echo $item_id; ?>&order=" + sort_str + '&_wpnonce=<?php echo $nonce; ?>', function(data) {
			});
		}
	});
}

var img_counter = parseInt(<?php echo $max_img_index ?>) + 1;

jQuery(document).ready(function()
{
	var url = '<?php echo wpl_global::get_full_url(); ?>&wpl_format=b:listing:gallery&wpl_function=upload&pid='+<?php echo $item_id; ?>+'&kind=<?php echo $this->kind; ?>&type=gallery&_wpnonce=<?php echo $nonce; ?>';

    wplj('#fileupload').fileupload(
    {
        url: url,
        acceptFileTypes: /(<?php echo $ext_str; ?>)$/i,
        dataType: 'json',
        maxFileSize: <?php echo $max_size * 1000; ?>,
        sequentialUploads: true,
        done: function(e, data)
        {
            wplj(data.result.files).each(function(index, file)
            {
                if (file.error !== undefined)
                {
                    wplj('<div class="row"/>').text(file.error).appendTo('#files');
                }
                else if (file.thumbnailUrl !== undefined) {

                    var hbSource   = wplj("#dbst-wizard-gallery").html();
                    var hbTemplate = Handlebars.compile(hbSource);
                    var hbHTML     = hbTemplate({
                        index: img_counter,
                        name: file.name,
                        enabled_title: "<?php echo addslashes(__('Enabled', 'real-estate-listing-realtyna-wpl')) ?>",
                        selectOptions: "<?php echo addslashes($image_categories_html) ?>",
                        imageFolder: "<?php echo addslashes($image_folder); ?>",
                        lblImageTitle: "<?php echo addslashes(__('Image Title', 'real-estate-listing-realtyna-wpl')); ?>",
                        lblImageDesc: "<?php echo addslashes(__('Image Description', 'real-estate-listing-realtyna-wpl')); ?>",
                        lblImageCat: "<?php echo addslashes(__('Image Category', 'real-estate-listing-realtyna-wpl')); ?>",
                        mediaConfirm: <?php echo ($media_confirm ? 'true' : 'false'); ?>
                    });

                    wplj(hbHTML).hide().appendTo('#ajax_gal_sortable').slideDown();
                    img_counter++;
                }
                else
                    wplj('<div class="row"/>').text(file.name).appendTo('#files');
            }).promise().done(function()
            {
                wplj('#progress_img').hide();
                wpl_gallery_sortable(true);
            });
        },
        progressall: function(e, data)
        {
            wplj('#progress_img').show();
            var progress = parseInt(data.loaded / data.total * 100, 10);
            wplj('#progress_img #progress .bar').css('width', progress + '%');
            wplj("#error_ajax_img").html("");
        },
        processfail: function(e, data)
        {
            wplj('#progress_img').hide();
            wplj("#error_ajax_img").html("<span color='red'><?php echo addslashes(__('Error occured', 'real-estate-listing-realtyna-wpl')) ?> : " + data.files[data.index].name + " " + data.files[data.index].error + "</span>");
            wplj("#error_ajax_img").show('slow');
        }
    });
});

function ajax_gallery_title_update(image, value)
{
	ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:gallery&wpl_function=title_update&pid=<?php echo $item_id; ?>&image="+image+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_gallery_desc_update(image, value)
{
	ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:gallery&wpl_function=desc_update&pid=<?php echo $item_id; ?>&image="+image+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_gallery_cat_update(image, value)
{
	ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:gallery&wpl_function=cat_update&pid=<?php echo $item_id ?>&image="+image+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_gallery_image_delete(image, id)
{
	if(!confirm("<?php echo addslashes(__('Are you sure?', 'real-estate-listing-realtyna-wpl')); ?>")) return;
	
    ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:gallery&wpl_function=delete_image&pid=<?php echo $item_id; ?>&image="+encodeURIComponent(image)+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
    ajax.success(function()
    {
        wplj("#" + id).slideUp(400, function(){
            wplj(this).remove();
        });
    });
}

function wpl_gallery_delete_all()
{
    if(!confirm("<?php echo addslashes(__('Are you sure?', 'real-estate-listing-realtyna-wpl')); ?>")) return;
	
    ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:gallery&wpl_function=delete_all_images&pid=<?php echo $item_id; ?>&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
    ajax.success(function()
    {
        wplj("#ajax_gal_sortable li").slideUp(400, function(){
            wplj(this).remove();
        });
    });
}

function wpl_image_enabled(gallery, id)
{
	var status = Math.abs(wplj("#enabled_image_field_" + id).val() - 1);
	wplj("#enabled_image_field_" + id).val(status);
    
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:gallery&wpl_function=change_status&pid=<?php echo $item_id; ?>&image="+encodeURIComponent(gallery)+"&enabled="+status+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
	ajax.success(function(data)
	{
		if (status == 0)
			wplj("#active_image_tag_" + id).html('<i class="action-btn icon-disabled" title="<?php echo addslashes(__('Disabled', 'real-estate-listing-realtyna-wpl')); ?>"></i>');
		else
			wplj("#active_image_tag_" + id).html('<i class="action-btn icon-enabled" title="<?php echo addslashes(__('Enabled', 'real-estate-listing-realtyna-wpl')); ?>"></i>');
	});
}

function wpl_gallery_select_tab(tab_id, container_id)
{
    wplj('#gallery-tabs-wp-container li').removeClass('active');
    wplj('#gallery-tabs-wp-container li#'+tab_id).addClass('active');
	
    wplj('.wpl_gallery_method_container').hide();
    wplj('#'+container_id).show();
}

function add_external_image(i)
{
    wplj('#wpl_gallery_external_cnt').show();
}

function wpl_gallery_external_save()
{
    var external_link = wplj('#gallery_external_link').val();
    
    ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:gallery&wpl_function=save_external_images&pid=<?php echo $item_id; ?>&kind=<?php echo $this->kind; ?>&links="+external_link+"&_wpnonce=<?php echo $nonce; ?>");
	ajax.success(function (data)
    {
        var url = '<?php echo wpl_global::add_qs_var('pid', $item_id, wpl_global::get_full_url()); ?>';
        window.location = url;
	});
}
</script>
<?php
    $done_this = true;
}
elseif($type == 'attachments' and !$done_this)
{
    $extentions = explode(',', $options['ext_file']);
	$ext_str = '';
    foreach($extentions as $extention) $ext_str .= $extention . '|';

    // remove last |
    $ext_str = substr($ext_str, 0, -1);
    $ext_str = rtrim($ext_str, ';');
    $max_size = $options['file_size'];

    // Load Handlebars Templates
    echo wpl_global::load_js_template('dbst-wizard-attachment');
?>
<div class="attach-btn-wp">
	<div class="wpl-button button-1 button-upload">
		<span><?php echo __('Select files', 'real-estate-listing-realtyna-wpl'); ?></span>
		<!-- The file input field used as target for the file upload widget -->
		<input id="attachment_upload" type="file" name="files[]" multiple>
	</div>
	<div class="field-desc">
		<?php echo __('To attach files click on the "Select files" button.', 'real-estate-listing-realtyna-wpl'); ?>
	</div>
</div>

<!-- The global progress bar -->
<div id="progress_att">
	<div id="progress" class="progress progress-success progress-striped">
		<div class="bar"></div>
	</div>
</div>

<div class="error_uploaded_message" id="error_ajax_att">
</div>

<!-- The container for the uploaded files -->
<div id="attaches" class="attachment-wp wpl_files_container">
    <div class="wpl-mass-actions">
        <button class="wpl-button button-1" onclick="wpl_attachment_delete_all();"><?php echo __('Delete all attachments', 'real-estate-listing-realtyna-wpl'); ?></button>
    </div>
	<ul class="ui-sortable" id="ajax_att_sortable">
	<?php
    // get uploaded attachments and show them
    $att_items = wpl_items::get_items($item_id, 'attachment', $this->kind, '', '');
    
    // Get blog ID of property
    $blog_id = wpl_property::get_blog_id($item_id);
    
    // Media Confirm Status
    $media_confirm = wpl_global::get_setting('listing_media_confirm');
    
    $att_folder = wpl_items::get_folder($item_id, $this->kind, $blog_id);
    $attachment_categories = wpl_items::get_item_categories('attachment', $this->kind);
    $max_index_att = 0;
    
    foreach ($att_items as $attachment)
    {
        $attachment->index = intval($attachment->index);
        if($max_index_att < $attachment->index) $max_index_att = $attachment->index;
        ?>
        <li class="ui-state-default" id="ajax_attachment<?php echo $attachment->index; ?>">
            <input type="hidden" class="att_name" value="<?php echo $attachment->item_name ?>"/>

            <div class="image-box-wp">
                <div class="icon-wp">
                    <div class="wpl-attach-icon wpl-att-<?php echo pathinfo($attachment->item_name, PATHINFO_EXTENSION); ?>"></div>
                </div>
                <div class="info-wp">
                    <div class="row">
                        <label for=""><?php echo __('Attachment Title', 'real-estate-listing-realtyna-wpl') ?>:</label>
                        <input type="text" class="att_title" value="<?php echo $attachment->item_extra1; ?>" onchange="ajax_attachment_title_update('<?php echo $attachment->item_name; ?>', this.value);" size="20" />
                    </div>
                    <div class="row">
                        <label for=""><?php echo __('Attachment Description', 'real-estate-listing-realtyna-wpl') ?>:</label>
                        <input type="text" class="att_desc" value="<?php echo $attachment->item_extra2; ?>" onchange="ajax_attachment_desc_update('<?php echo $attachment->item_name; ?>', this.value);" size="50" />
                    </div>
                    <div class="row">
                        <label for=""><?php echo __('Attachment Category', 'real-estate-listing-realtyna-wpl') ?>:</label>
                        <select name="att_cat" class="att_cat" onchange="ajax_attachment_cat_update('<?php echo $attachment->item_name; ?>', this.value);">
                            <?php
                            foreach ($attachment_categories as $att_cat)
                            {
                                echo ' <option value="' . $att_cat->category_name . '"';
                                if($attachment->item_cat == $att_cat->category_name) echo ' selected="selected"';
                                echo '>' . $att_cat->category_name . '</option>';
                            }
                            ?>
                        </select>
                    </div>
                </div>
                <div class="actions-wp">
                    <div class="action-gal-btn ajax_attachment_move_icon">
                        <i class="action-btn icon-move wpl_actions_btn"></i>
                    </div>
                    <div class="action-gal-btn ajax_gallery_middle_td " onclick="ajax_attachment_delete('<?php echo $attachment->item_name; ?>','ajax_attachment<?php echo $attachment->index; ?>');" >
                        <i class="action-btn icon-recycle"></i>
                    </div>

                    <?php
                        if($attachment->enabled and $media_confirm) echo '<div class="action-gal-btn" id="active_attachment_tag_' . $attachment->index . '" onclick="wpl_attachment_enabled(\'' . $attachment->item_name . '\',' . $attachment->index . ');"><i class="action-btn icon-enabled wpl_actions_btn wpl_show" title="'.__('Enabled', 'real-estate-listing-realtyna-wpl').'"></i></div>';
                        elseif($media_confirm) echo '<div class="action-gal-btn" id="active_attachment_tag_' . $attachment->index . '" onclick="wpl_attachment_enabled(\'' . $attachment->item_name . '\',' . $attachment->index . ');"><i class="action-btn icon-disabled wpl_actions_btn  wpl_show" title="'.__('Disabled', 'real-estate-listing-realtyna-wpl').'"></i></div>';
                    ?>

                    <input type="hidden" id="enabled_attachment_field_<?php echo $attachment->index; ?>" value="<?php echo $attachment->enabled; ?>"/>
                </div>
            </div>
        </li>
		<?php
    }
    ?>
	</ul>
	<?php
	$attachment_categories_html = '';
	foreach ($attachment_categories as $att_cat)
	{
		$attachment_categories_html .= ' <option value="' . $att_cat->category_name . '">' . __($att_cat->category_name) . '</option>';
	}
	?>
</div>
<script type="text/javascript">
jQuery(document).ready(function()
{
	wpl_att_sortable();
});

function wpl_att_sortable(destroy)
{
    if(typeof destroy == 'undefined') destroy = false;
    if(destroy) wplj("#ajax_att_sortable").sortable('destroy');
    
    wplj("#ajax_att_sortable").sortable(
	{
        placeholder: "wpl-sortable-placeholder",
        opacity: 0.7,
        forcePlaceholderSize: true,
        cursor: "move",
        axis: "y",
		stop: function (event, ui)
		{
			sort_str = "";
			wplj("#ajax_att_sortable .att_name").each(function (ind, elm)
			{
				sort_str += elm.value + ",";
			});

			wplj.post("<?php echo wpl_global::get_full_url(); ?>", "&wpl_format=b:listing:attachments&wpl_function=sort_attachments&pid=<?php echo $item_id; ?>&order="+sort_str+"&kind=<?php echo $this->kind; ?>"+"&_wpnonce=<?php echo $nonce; ?>", function (data) {});
		}
	});
}

var att_counter = parseInt(<?php echo $max_index_att ?>) + 1;
jQuery(document).ready(function()
{
	var url = '<?php echo wpl_global::get_full_url(); ?>&wpl_format=b:listing:attachments&wpl_function=upload&pid='+<?php echo $item_id; ?>+'&kind=<?php echo $this->kind; ?>&type=attachment&_wpnonce=<?php echo $nonce; ?>';

    wplj('#attachment_upload').fileupload(
    {
        url: url,
        acceptFileTypes: /(<?php echo $ext_str; ?>)$/i,
        dataType: 'json',
        maxFileSize: <?php echo $max_size * 1000; ?>,
        sequentialUploads: true,
        done: function (e, data)
        {
            wplj(data.result.files).each(function (index, file)
            {
                if (file.error !== undefined)
                {
                    wplj('<p/>').text(file.error).appendTo('#attaches');
                }
                else
                {

                    var hbSource   = wplj("#dbst-wizard-attachment").html();
                    var hbTemplate = Handlebars.compile(hbSource);
                    var hbHTML     = hbTemplate({
                        att_counter: att_counter,
                        fileName: file.name,
                        enabled_title: "<?php echo addslashes(__('Enabled', 'real-estate-listing-realtyna-wpl')); ?>",
                        subFileName: file.name.substr((file.name.lastIndexOf('.') + 1)),
                        lblTitle: "<?php echo addslashes(__('Attachment Title', 'real-estate-listing-realtyna-wpl')); ?>",
                        lblDesc: "<?php echo addslashes(__('Attachment Description', 'real-estate-listing-realtyna-wpl')); ?>",
                        lblCat: "<?php echo addslashes(__('Attachment Category', 'real-estate-listing-realtyna-wpl')); ?>",
                        attachCat: "<?php echo addslashes($attachment_categories_html); ?>",
                        mediaConfirm: <?php echo ($media_confirm ? 'true' : 'false'); ?>
                    });

                    wplj(hbHTML).hide().appendTo('#ajax_att_sortable').slideDown();

                    att_counter++;
                }

                rta.internal.initChosen();

            }).promise().done(function()
            {
                wplj('#progress_att').hide();
                wpl_att_sortable(true);
            });
        },
        progressall: function (e, data)
        {
            wplj("#progress_att").show('fast');
            var progress = parseInt(data.loaded / data.total * 100, 10);

            wplj('#progress_att #progress .bar').css('width', progress + '%');

            wplj("#error_ajax_att").html("");
            wplj("#error_ajax_att").hide('slow');
        },
        processfail: function (e, data)
        {
            wplj("#progress_att").hide('slow');
            wplj("#error_ajax_att").html("<span color='red'><?php echo __('Error occured', 'real-estate-listing-realtyna-wpl') ?> : " + data.files[data.index].name + " " + data.files[data.index].error + "</span>");
            wplj("#error_ajax_att").show('slow');
        }
    });
});

function ajax_attachment_title_update(attachment, value)
{
	ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:attachments&wpl_function=title_update&pid=<?php echo $item_id; ?>&attachment="+encodeURIComponent(attachment)+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_attachment_desc_update(attachment, value)
{
	ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:attachments&wpl_function=desc_update&pid=<?php echo $item_id; ?>&attachment="+encodeURIComponent(attachment)+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_attachment_cat_update(attachment, value)
{
	ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:attachments&wpl_function=cat_update&pid=<?php echo $item_id ?>&attachment="+encodeURIComponent(attachment)+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_attachment_delete(attachment, id)
{
	if(confirm('<?php _e('Are you sure?', 'real-estate-listing-realtyna-wpl') ?>'))
	{
		ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:attachments&wpl_function=delete_attachment&pid=<?php echo $item_id; ?>&attachment="+encodeURIComponent(attachment)+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
		ajax.success(function (data)
		{
			wplj("#" + id).slideUp(function(){
                wplj(this).remove();
            });
		});
	}
}

function wpl_attachment_delete_all()
{
    if(!confirm("<?php echo addslashes(__('Are you sure?', 'real-estate-listing-realtyna-wpl')); ?>")) return;
	
    ajax = wpl_run_ajax_query("<?php echo wpl_global::get_full_url(); ?>", "wpl_format=b:listing:attachments&wpl_function=delete_all_attachments&pid=<?php echo $item_id; ?>&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
    ajax.success(function()
    {
        wplj("#ajax_att_sortable li").slideUp(400, function(){
            wplj(this).remove();
        });
    });
}

function wpl_attachment_enabled(attachment, id)
{
	var status = Math.abs(wplj("#enabled_attachment_field_" + id).val() - 1);
	wplj("#enabled_attachment_field_" + id).val(status);
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:attachments&wpl_function=change_status&pid=<?php echo $item_id; ?>&attachment="+encodeURIComponent(attachment)+"&enabled="+status+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");

	ajax.success(function (data)
	{
		if (status == 0) wplj("#active_attachment_tag_" + id).html('<i class="action-btn icon-disabled wpl_actions_btn wpl_show" title="<?php echo addslashes(__('Disabled', 'real-estate-listing-realtyna-wpl')); ?>"></i>');
		else wplj("#active_attachment_tag_" + id).html('<i class="action-btn icon-enabled wpl_actions_btn wpl_show" title="<?php echo addslashes(__('Enabled', 'real-estate-listing-realtyna-wpl')); ?>"></i>');
	});
}
</script>
<?php
    $done_this = true;
}
elseif($type == 'addon_video' and !$done_this)
{
    $ext_str = trim(str_replace(',', '|', $options['ext_file']), '|,; ');
    $max_size = $options['file_size'];

    $vid_embed_items = wpl_items::get_items($item_id, 'video', $this->kind, 'video_embed', 1);
    $vid_embed_count = 1;

	// Load Handlebars Templates
	echo wpl_global::load_js_template('dbst-wizard-videos');
?>

<div class="video-tabs-wp" id="video-tabs">
	<ul>
		<li class="active"><a id="embed-tab" href="#embed" onclick="video_select_tab(0); return false;"><?php echo __('Embed code', 'real-estate-listing-realtyna-wpl'); ?></a></li>
		<?php if(wpl_settings::get('video_uploader')): ?>
		<li><a id="uploader-tab" href="#uploader" onclick="video_select_tab(1); return false;"><?php echo __('Video uploader', 'real-estate-listing-realtyna-wpl'); ?></a></li>
		<?php endif; ?>
	</ul>
</div>

<div class="video-content-wp">

<div class="content-wp" id="embed">
	<button class="wpl-button button-1" onclick="add_embed_video();"><?php echo __('Add video', 'real-estate-listing-realtyna-wpl') ?></button>

	<?php foreach ($vid_embed_items as $vid_embed_item): ?>
    <div class="video-embed-wp" id="video-embed-<?php echo $vid_embed_count; ?>">
        <div class="row">
            <label id="title_label" for="embed_vid_title<?php echo $vid_embed_count; ?>"><?php echo __('Video title', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="text" name="embed_vid_title<?php echo $vid_embed_count; ?>" id="embed_vid_title<?php echo $vid_embed_count; ?>" value="<?php echo $vid_embed_item->item_name; ?>" onblur="video_embed_save(<?php echo $vid_embed_count; ?>);" />
        </div>
        <div class="row">
            <label id="desc_label" for="embed_vid_desc<?php echo $vid_embed_count; ?>"><?php echo __('Video description', 'real-estate-listing-realtyna-wpl'); ?></label>
            <input type="text" name="embed_vid_desc<?php echo $vid_embed_count; ?>" id="embed_vid_desc<?php echo $vid_embed_count; ?>" value="<?php echo $vid_embed_item->item_extra1; ?>" onblur="video_embed_save(<?php echo $vid_embed_count; ?>);" />
        </div>
        <div class="row">
            <a class="button_help" href="https://support.realtyna.com/index.php?/Default/Knowledgebase/Article/View/792/28/how-to-get-embed-code-from-youtube-website" target="_blank">
                <?php echo __('Need help?', 'real-estate-listing-realtyna-wpl') ?>
            </a>
            <label id="embed_label" for="embed_vid_code<?php echo $vid_embed_count; ?>"><?php echo __('Video embed code', 'real-estate-listing-realtyna-wpl'); ?></label>
            <textarea name="embed_vid_code<?php echo $vid_embed_count; ?>" rows="5" cols="50" id="embed_vid_code<?php echo $vid_embed_count; ?>" onblur="video_embed_save(<?php echo $vid_embed_count; ?>);" placeholder="<?php echo sprintf(__('Sample: %s', 'real-estate-listing-realtyna-wpl'), "<iframe width='560' height='350' src='http://youtube.com' frameborder='0' allowfullscreen></iframe>"); ?>"><?php echo $vid_embed_item->item_extra2; ?></textarea>
        </div>
        <div class="actions-wp"><a onclick="embed_video_delete('<?php echo $vid_embed_count; ?>');"><i class="action-btn icon-recycle"></i></a></div>
        <input type="hidden" id="vid_emb<?php echo $vid_embed_count; ?>" value="<?php echo $vid_embed_item->id; ?>" />
    </div>
    <?php $vid_embed_count++; endforeach; ?>
</div>

<script type="text/javascript">
var vid_embed_count = <?php echo $vid_embed_count; ?>;
function add_embed_video()
{
	var embedVideo = rta.template.bind({
		count: vid_embed_count,
		title: "<?php echo addslashes(__('Video title', 'real-estate-listing-realtyna-wpl')); ?>",
		desc: "<?php echo addslashes(__('Video description', 'real-estate-listing-realtyna-wpl')); ?>",
		embedCode: "<?php echo addslashes(__('Video embed code', 'real-estate-listing-realtyna-wpl')); ?>",
		item_name: '',
		item_extra1: '',
		item_extra2: '',
        help_link: "<?php echo __('Need help?', 'real-estate-listing-realtyna-wpl') ?>",
        placeholder: "<?php echo sprintf(__('Sample: %s', 'real-estate-listing-realtyna-wpl'), "<iframe width='560' height='350' src='http://youtube.com' frameborder='0' allowfullscreen></iframe>"); ?>",
		id: '-1'
	}, 'add-listing-video-embed');
    
	wplj(embedVideo).appendTo('#embed');
	vid_embed_count++;
}

function video_embed_save(id)
{
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:videos&wpl_function=embed_video&pid=<?php echo $item_id; ?>&kind=<?php echo $this->kind; ?>&item_id="+wplj("#vid_emb"+id).val()+"&title="+wplj("#embed_vid_title"+id).val()+"&desc="+wplj("#embed_vid_desc"+id).val()+"&_wpnonce=<?php echo $nonce; ?>&embedcode="+encodeURIComponent(wplj("#embed_vid_code"+id).val()));
	ajax.success(function (data)
    {
		if(wplj("#vid_emb" + id).val() == -1) wplj("#vid_emb" + id).val(data);
	});
}

function embed_video_delete(id)
{
	if (confirm("<?php echo addslashes(__('Are you sure?', 'real-estate-listing-realtyna-wpl')); ?>"))
    {
		ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:videos&wpl_function=del_embed_video&pid=<?php echo $item_id; ?>&kind=<?php echo $this->kind; ?>&item_id="+wplj("#vid_emb"+id).val()+"&_wpnonce=<?php echo $nonce; ?>");
		ajax.success(function (data)
        {
			wplj("#video-embed-" + id).fadeOut(500, function ()
            {
				wplj(this).remove();
			});
		});
	}
}

function video_select_tab(id)
{
	wplj('#video-tabs').find('li').removeClass('active').eq(id).addClass('active');
	var _this = wplj('#video-tabs').find('li:eq(' + id + ') > a');
	wplj('.video-content-wp').find('> div').hide().filter(_this.attr('href')).fadeIn(600);
}
</script>

<?php
if(wpl_settings::get('video_uploader'))
{
?>
<div class="content-wp wpl-util-hidden" id="uploader">
	<div class="upload-btn-wp">
		<div class="wpl-button button-1 button-upload">
			<span><?php echo __('Select Files', 'real-estate-listing-realtyna-wpl'); ?></span>
			<input id="video_upload" type="file" name="files[]" multiple="multiple"/>
		</div>
		<div class="field-desc">
			<?php echo __('Choose videos by clicking on "Select Files."', 'real-estate-listing-realtyna-wpl'); ?>
		</div>
	</div>
	<!-- The global progress bar -->
	<div id="progress_vid">
		<div id="progress" class="progress progress-success progress-striped">
			<div class="bar"></div>
		</div>
	</div>
	<div class="error_uploaded_message" id="error_ajax_vid">
	</div>
	<!-- The container for the uploaded files -->
	<div id="video" class="video-list-wp wpl_files_container">
		<ul class="ui-sortable" id="ajax_vid_sortable">
			<?php
			// get uploaded videos and show them
			$vid_items = wpl_items::get_items($item_id, 'video', $this->kind, 'video', '');
            
            // Get blog ID of property
            $blog_id = wpl_property::get_blog_id($item_id);
            
            // Media Confirm Status
            $media_confirm = wpl_global::get_setting('listing_media_confirm');
        
			$vid_folder = wpl_items::get_folder($item_id, $this->kind, $blog_id);
			$video_categories = wpl_items::get_item_categories('addon_video', $this->kind);
			$max_index_vid = 0;

			foreach ($vid_items as $video)
			{
				$video->index = intval($video->index);
				if($max_index_vid < $video->index)
					$max_index_vid = $video->index;
				?>
				<li class="ui-state-default" id="ajax_video<?php echo $video->index; ?>">
					<input type="hidden" class="vid_name" value="<?php echo $video->item_name; ?>"/>

					<div class="image-box-wp">
						<div class="info-wp">
							<div class="row">
								<label for=""><?php echo __('Video Title', 'real-estate-listing-realtyna-wpl'); ?>:</label>
								<input type="text" class="vid_title" value="<?php echo $video->item_extra1; ?>" onchange="ajax_video_title_update('<?php echo $video->item_name; ?>', this.value);" size="20" />
							</div>
							<div class="row">
								<label for=""><?php echo __('Video Description', 'real-estate-listing-realtyna-wpl'); ?>:</label>
								<input type="text" class="vid_desc" value="<?php echo $video->item_extra2; ?>" onchange="ajax_video_desc_update('<?php echo $video->item_name; ?>', this.value);" size="50" />
							</div>
							<div class="row">
								<label for=""><?php echo __('Video Category', 'real-estate-listing-realtyna-wpl'); ?>:</label>
								<select name="vid_cat" class="vid_cat" onchange="ajax_video_cat_update('<?php echo $video->item_name; ?>', this.value);">
									<?php
									foreach ($video_categories as $vid_cat)
									{
										echo ' <option value="' . $vid_cat->category_name . '"';
										if($video->item_cat == $vid_cat->category_name)
											echo ' selected="selected"';
										echo '>' . $vid_cat->category_name . '</option>';
									}
									?>
								</select>
							</div>
						</div>
						<div class="actions-wp">
							<div class="action-gal-btn">
								<i class="action-btn icon-move"></i>
							</div>
							<div class="action-gal-btn ajax_gallery_middle_td"
								 onclick="ajax_video_delete('<?php echo $video->item_name; ?>', 'ajax_video<?php echo $video->index; ?>');">
								<i class="action-btn icon-recycle"></i>
							</div>
							<?php
                                if($video->enabled and $media_confirm) echo '<div class="action-gal-btn" id="active_video_tag_' . $video->index . '" onclick="wpl_video_enabled(\'' . $video->item_name . '\',' . $video->index . ');"><i class="action-btn icon-enabled"></i></div>';
                                elseif($media_confirm) echo '<div class="action-gal-btn" id="active_video_tag_' . $video->index . '" onclick="wpl_video_enabled(\'' . $video->item_name . '\',' . $video->index . ');"><i class="action-btn icon-disabled"></i></div>';
							?>
							<input type="hidden" id="enabled_video_field_<?php echo $video->index; ?>" value="<?php echo $video->enabled; ?>" />
						</div>
					</div>
				</li>
			<?php
			}
			?>
		</ul>
		<?php
		$video_categories_html = '';
		foreach ($video_categories as $vid_cat)
		{
			$video_categories_html .= ' <option value="' . $vid_cat->category_name . '">' . __($vid_cat->category_name, 'real-estate-listing-realtyna-wpl') . '</option>';
		}
		?>
	</div>
</div>

<script type="text/javascript">
jQuery(document).ready(function()
{
    wpl_vid_sortable();
});

function wpl_vid_sortable(destroy)
{
    if(typeof destroy == 'undefined') destroy = false;
    if(destroy) wplj("#ajax_vid_sortable").sortable('destroy');
    
    wplj("#ajax_vid_sortable").sortable(
	{
		placeholder: "wpl-sortable-placeholder",
		opacity: 0.7,
		forcePlaceholderSize: true,
		cursor: "move",
		axis: "y",
		stop: function (event, ui)
		{
			sort_str = "";
			wplj("#ajax_vid_sortable .vid_name").each(function (ind, elm)
			{
				sort_str += elm.value + ",";
			});
	
			wplj.post("<?php echo wpl_global::get_full_url(); ?>", "&wpl_format=b:listing:videos&wpl_function=sort_videos&pid=<?php echo $item_id; ?>&order="+sort_str+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>", function (data)
			{
			});
		}
	});
}

var vid_counter = parseInt(<?php echo $max_index_vid; ?>) + 1;
jQuery(document).ready(function()
{
	var url = '<?php echo wpl_global::get_full_url(); ?>&wpl_format=b:listing:videos&wpl_function=upload&pid='+<?php echo $item_id; ?>+'&kind=<?php echo $this->kind; ?>&type=video&_wpnonce=<?php echo $nonce; ?>';

    wplj('#video_upload').fileupload(
    {
        url: url,
        acceptFileTypes: /(<?php echo $ext_str; ?>)$/i,
        dataType: 'json',
        maxFileSize: <?php echo $max_size * 1000; ?>,
        sequentialUploads: true,
        done: function (e, data)
        {
            wplj(data.result.files).each(function (index, file)
            {
                if(file.error !== undefined)
                {
                    wplj('<p/>').text(file.error).appendTo('#video');
                }
                else
                {
					var hbSource   = wplj("#dbst-wizard-videos").html();
					var hbTemplate = Handlebars.compile(hbSource);
					var hbHTML     = hbTemplate({
						vid_counter: vid_counter,
						lblTitle: "<?php echo addslashes(__('Video Title', 'real-estate-listing-realtyna-wpl')); ?>",
						lblDesc: "<?php echo addslashes(__('Video Description', 'real-estate-listing-realtyna-wpl')); ?>",
						lblCat: "<?php echo addslashes(__('Video Category', 'real-estate-listing-realtyna-wpl')); ?>",
                        name: file.name,
						select: "<?php echo addslashes($video_categories_html); ?>",
                        mediaConfirm: <?php echo ($media_confirm ? 'true' : 'false'); ?>
					});

					wplj(hbHTML).hide().appendTo('#ajax_vid_sortable').slideDown();
                    vid_counter++;
                }
            }).promise().done(function ()
            {
				wplj('#progress_vid').hide();
                wpl_vid_sortable();
			});
        },
        progressall: function (e, data)
        {
            wplj("#progress_vid").show('fast');
            var progress = parseInt(data.loaded / data.total * 100, 10);
            wplj('#progress_vid #progress .bar').css('width', progress + '%');
            wplj("#error_ajax_vid").html("");
            wplj("#error_ajax_vid").hide('slow');
        },
        processfail: function (e, data)
        {
            wplj("#progress_vid").hide('slow');
            wplj("#error_ajax_vid").html("<span color='red'><?php echo __('Error occured', 'real-estate-listing-realtyna-wpl'); ?> : " + data.files[data.index].name + " " + data.files[data.index].error + "</span>");
            wplj("#error_ajax_vid").show('slow');
        }
    }); // End of FileUpload
});

function ajax_video_title_update(video, value)
{
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:videos&wpl_function=title_update&pid=<?php echo $item_id; ?>&video="+video+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_video_desc_update(video, value)
{
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:videos&wpl_function=desc_update&pid=<?php echo $item_id; ?>&video="+video+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_video_cat_update(video, value)
{
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:videos&wpl_function=cat_update&pid=<?php echo $item_id; ?>&video="+video+"&value="+value+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
}

function ajax_video_delete(video, id)
{
	if (confirm("<?php echo addslashes(__('Are you sure?', 'real-estate-listing-realtyna-wpl')); ?>"))
	{
		ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:videos&wpl_function=delete_video&pid=<?php echo $item_id; ?>&video="+encodeURIComponent(video)+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
		ajax.success(function (data)
		{
			wplj("#" + id).slideUp(function ()
			{
				wplj(this).remove();
			});
		});
	}
}

function wpl_video_enabled(video, id)
{
	var status = Math.abs(wplj("#enabled_video_field_" + id).val() - 1);
	wplj("#enabled_video_field_" + id).val(status);
	ajax = wpl_run_ajax_query('<?php echo wpl_global::get_full_url(); ?>', "wpl_format=b:listing:videos&wpl_function=change_status&pid=<?php echo $item_id; ?>&video="+encodeURIComponent(video)+"&enabled="+status+"&kind=<?php echo $this->kind; ?>&_wpnonce=<?php echo $nonce; ?>");
	ajax.success(function (data)
    {
		if (status == 0) wplj("#active_video_tag_" + id).html('<i class="action-btn icon-disabled"></i>');
		else wplj("#active_video_tag_" + id).html('<i class="action-btn icon-enabled"></i>');
	});
}
</script>
<?php
}
?>
</div>

<?php
    $done_this = true;
}