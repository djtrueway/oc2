<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
?>
<div id="wpl_flex_top_tabs_container">
    <ul class="wpl-tabs">
        <?php foreach($this->kinds as $kind): ?>
        <li class="<?php echo ($this->kind == $kind['id'] ? 'wpl-selected-tab' : ''); ?>" id="wplkind<?php echo $kind['id']; ?>">
            <a href="<?php echo wpl_global::add_qs_var('kind', $kind['id'], wpl_global::remove_qs_var('wpltour')); ?>"><?php echo __($kind['name'], 'real-estate-listing-realtyna-wpl'); ?></a>
        </li>
        <?php endforeach; ?>
    </ul>
</div>