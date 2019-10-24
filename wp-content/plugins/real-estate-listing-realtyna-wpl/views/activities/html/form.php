<?php
/** no direct access * */
defined('_WPLEXEC') or die('Restricted access');
?>
<div class="fanc-row">
    <label for="wpl_o_html"><?php echo __('HTML', 'real-estate-listing-realtyna-wpl'); ?></label>
    <textarea class="long" name="option[html]" id="wpl_o_html"><?php echo isset($this->options->html) ? stripslashes($this->options->html) : ''; ?></textarea>
</div>