<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="wpl_maintenance"><div class="wpl_show_message"></div></div>
<div class="wpl-maintenance-container">

    <h3 class="wpl-clear-cache-subject"><?php echo __('Clear WPL Caches', 'real-estate-listing-realtyna-wpl'); ?></h3>
    <hr>
    <form class="wpl-clear-cache-form" id="wpl_clear_cache_form">
        <ul>
            <li>
                <input type="checkbox" name="cache[wpl_cache_directory]" value="1" checked="checked" id="wpl_cache_wpl_cache_directory" />
                <span class="title">
                    <label for="wpl_cache_wpl_cache_directory"><?php echo __('Purge WPL cache directory', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[unfinalized_properties]" value="1" id="wpl_cache_unfinalized_properties" />
                <span class="title">
                    <label for="wpl_cache_unfinalized_properties"><?php echo __('Purge unfinalized listings', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[properties_title]" value="1" id="wpl_cache_properties_title" />
                <span class="title">
                    <label for="wpl_cache_properties_title"><?php echo __('Clear properties titles', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[properties_page_title]" value="1" id="wpl_cache_properties_page_title" />
                <span class="title">
                    <label for="wpl_cache_properties_page_title"><?php echo __('Clear properties page titles', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <?php if(wpl_global::check_addon('complex')): ?>
            <li>
                <input type="checkbox" name="cache[complexes_title]" value="1" id="wpl_cache_complexes_title" />
                <span class="title">
                    <label for="wpl_cache_complexes_title"><?php echo __('Clear complexes titles', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[complexes_page_title]" value="1" id="wpl_cache_complexes_page_title" />
                <span class="title">
                    <label for="wpl_cache_complexes_page_title"><?php echo __('Clear complexes page titles', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <?php endif; ?>
            <?php if(wpl_global::check_addon('neighborhoods')): ?>
            <li>
                <input type="checkbox" name="cache[neighborhoods_title]" value="1" id="wpl_cache_neighborhoods_title" />
                <span class="title">
                    <label for="wpl_cache_neighborhoods_title"><?php echo __('Clear neighborhoods titles', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[neighborhoods_page_title]" value="1" id="wpl_cache_neighborhoods_page_title" />
                <span class="title">
                    <label for="wpl_cache_neighborhoods_page_title"><?php echo __('Clear neighborhoods page titles', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <?php endif; ?>
            <li>
                <input type="checkbox" name="cache[properties_cached_data]" value="1" checked="checked" id="wpl_cache_properties_cached_data" />
                <span class="title">
                    <label for="wpl_cache_properties_cached_data"><?php echo __('Clear listings cached data', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[listings_meta_keywords]" value="1" id="wpl_cache_listings_meta_keywords" />
                <span class="title">
                    <label for="wpl_cache_listings_meta_keywords"><?php echo __('Listings meta keywords', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[listings_meta_description]" value="1" id="wpl_cache_listings_meta_description" />
                <span class="title">
                    <label for="wpl_cache_listings_meta_description"><?php echo __('Listings meta description', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[location_texts]" value="1" checked="checked" id="wpl_cache_location_texts" />
                <span class="title">
                    <label for="wpl_cache_location_texts"><?php echo __('Clear listings cached location texts', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[listings_thumbnails]" value="1" id="wpl_cache_listings_thumbnails" />
                <span class="title">
                    <label for="wpl_cache_listings_thumbnails"><?php echo __('Clear listing thumbnails', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[users_cached_data]" value="1" checked="checked" id="wpl_cache_users_cached_data" />
                <span class="title">
                    <label for="wpl_cache_users_cached_data"><?php echo __('Clear users cached data', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li>
                <input type="checkbox" name="cache[users_thumbnails]" value="1" id="wpl_cache_users_thumbnails" />
                <span class="title">
                    <label for="wpl_cache_users_thumbnails"><?php echo __('Clear user thumbnails', 'real-estate-listing-realtyna-wpl'); ?></label>
                </span>
            </li>
            <li class="wpl-clear-cache-form-submit">
                <input type="hidden" id="wpl_clear_cache_confirm" value="0" />
                <button type="submit" class="wpl-button button-1" id="wpl_clear_cache_form_submit"><?php echo __('Clear', 'real-estate-listing-realtyna-wpl'); ?></button>
            </li>
        </ul>
    </form>
    <?php if(wpl_global::check_addon('calendar')): ?>
    <h3 class="wpl-clear-cache-subject"><?php echo __('Clear Listing Calendar data', 'real-estate-listing-realtyna-wpl'); ?></h3>
    <hr>
    <ul>
        <li onclick="wpl_clear_calendar_data(0);">
            <span class="title" id="wpl_maintenance_clear_calendar_data">
                 <i class="icon-trash"></i>
                <?php echo __('Clear listings calendar data', 'real-estate-listing-realtyna-wpl'); ?>
            </span>
        </li>
    </ul>
    <?php endif; ?>
</div>