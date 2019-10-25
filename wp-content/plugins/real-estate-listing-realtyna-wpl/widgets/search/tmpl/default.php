<?php
defined('_WPLEXEC') or die('Restricted access');

if(wpl_global::check_addon('membership')) $this->membership = new wpl_addon_membership();
?>
<div id="wpl_default_search_<?php echo $widget_id; ?>">
    <form action="<?php echo wpl_property::get_property_listing_link(); ?>" id="wpl_search_form_<?php echo $widget_id; ?>" method="GET" onsubmit="return wpl_do_search_<?php echo $widget_id; ?>('wpl_searchwidget_<?php echo $widget_id; ?>');" class="wpl_search_from_box clearfix wpl_search_kind<?php echo $this->kind; ?> <?php echo $this->style.' '.$this->css_class; ?>">

        <!-- Do not change the ID -->
        <div id="wpl_searchwidget_<?php echo $widget_id; ?>" class="clearfix">
            <?php
            $top_div = '';
            $bott_div = '';
            $bott_div_open = false;

            $is_separator = false;
            $top_array = array();

            $counter = 1;
            foreach($this->rendered as $data)
            {
                if(($data['field_data']['type'] == 'separator') && $counter > 1)
                {
                    $is_separator = true;
                    break;
                }

                $counter++;
            }

            if(!$is_separator) $top_array = array(41, 3, 6, 8, 9, 2);

            $counter = 1;
            foreach($this->rendered as $data)
            {
                if($is_separator or (!$is_separator and in_array($data['id'], $top_array))) $top_div .= $data['html'];
                else
                {
                    if(is_string($data['current_value']) and trim($data['current_value']) and $data['current_value'] != '-1') $bott_div_open = true;
                    $bott_div .= $data['html'];
                }

                if($data['field_data']['type'] == 'separator' and $counter > 1) $is_separator = false;
                $counter++;
            }
            ?>
            <div class="wpl_search_from_box_top">
                <?php echo $top_div; ?>
                <?php if($this->show_reset_button): ?>
                    <div class="wpl_search_reset" onclick="wpl_do_reset<?php echo $this->widget_id; ?>([], <?php echo ($this->ajax == 2 ? 'true' : 'false'); ?>);" id="wpl_search_reset<?php echo $widget_id; ?>"><?php echo __('Reset', 'real-estate-listing-realtyna-wpl'); ?></div>
                <?php endif; ?>
                <div class="search_submit_box">
                    <input id="wpl_search_widget_submit<?php echo $widget_id; ?>" class="wpl_search_widget_submit" type="submit" value="<?php echo __('Search', 'real-estate-listing-realtyna-wpl'); ?>" />
                    <?php if($this->show_total_results == 1): ?><span id="wpl_total_results<?php echo $widget_id; ?>" class="wpl-total-results">(<span></span>)</span><?php endif; ?>
                </div>
                <?php if($this->show_total_results == 2): ?><span id="wpl_total_results<?php echo $widget_id; ?>" class="wpl-total-results-after"><?php echo sprintf('%s listings', '<span></span>'); ?></span><?php endif; ?>
                <?php if(wpl_global::check_addon('membership') and ($this->kind == 0 or $this->kind == 1)): ?>
                    <div class="wpl_dashboard_links_container">
                        <?php if(wpl_global::check_addon('save_searches') and ($this->show_saved_searches)) : ?>
                            <a class="wpl-addon-save-searches-link" href="<?php echo $this->membership->URL('searches'); ?>"><?php echo __('Saved Searches', 'real-estate-listing-realtyna-wpl'); ?>
                                <span id="wpl-addon-save-searches-count<?php echo $widget_id; ?>"><?php echo $this->saved_searches_count; ?></span>
                            </a>
                        <?php endif; ?>
                        <?php if($this->show_favorites): ?>
                            <a class="wpl-widget-favorites-link" href="<?php echo $this->membership->URL('favorites'); ?>"><?php echo __('Favorites', 'real-estate-listing-realtyna-wpl'); ?>
                                <span id="wpl-widget-favorites-count<?php echo $widget_id; ?>"><?php echo $this->favorites_count; ?></span>
                            </a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
            <div class="wpl_search_from_box_bot" id="wpl_search_from_box_bot<?php echo $widget_id; ?>">
                <?php echo $bott_div; ?>
            </div>
        </div>
        <?php if($bott_div): ?>
        <div class="more_search_option" data-widget-id="<?php echo $widget_id; ?>" id="more_search_option<?php echo $widget_id; ?>"><?php echo __('More options', 'real-estate-listing-realtyna-wpl'); ?></div>
        <?php endif; ?>
    </form>
</div>

<?php if($this->more_options_type): ?>
<!-- Advanced Search -->
<div id="wpl_advanced_search<?php echo $widget_id; ?>" class="wpl-advanced-search-wp wpl-util-hidden">
    <div class="container">
        <div id="wpl_form_override_search<?php echo $widget_id; ?>" class="wpl-advanced-search-popup"></div>
    </div>
</div>
<?php endif;
// Import JS Codes
$this->_wpl_import('widgets.search.scripts.js', true, true);