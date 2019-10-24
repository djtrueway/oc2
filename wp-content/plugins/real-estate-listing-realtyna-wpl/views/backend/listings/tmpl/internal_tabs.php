<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div id="wpl_listings_top_tabs_container">
    <ul class="wpl-tabs">
        <?php foreach($this->kinds as $kind): ?>
        <?php if(($kind['id'] == 1 and !wpl_users::check_access('complex_addon')) or ($kind['id'] == 4 and !wpl_users::check_access('neighborhoods'))) continue; ?>
        <li class="<?php echo ($this->kind == $kind['id'] ? 'wpl-selected-tab' : ''); ?>" id="wplkind<?php echo $kind['id']; ?>">
            <a href="<?php echo wpl_global::add_qs_var('kind', $kind['id']); ?>"><?php echo __($kind['name'], 'real-estate-listing-realtyna-wpl'); ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>