<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$this->_wpl_import($this->tpl_path.'.scripts.js', true, true);
$this->_wpl_import($this->tpl_path.'.scripts.css', true, true);
?>
<div class="wrap">
    <div class="wpl_message_container" id="wpl_message_container">
        <?php echo $this->message; ?>
	</div>

    <?php if(isset($this->error_code) and $this->error_code == 401 and wpl_global::check_addon('membership')): ?>
    <div class="wpl-unauthorized-container">
        <?php if(is_active_sidebar('wpl-unauthorized-area')): ?>
        <div class="wpl_plisting_top_sidebar_container">
            <?php dynamic_sidebar('wpl-unauthorized-area'); ?>
        </div>
        <?php endif; ?>
    </div>
    <?php endif; ?>
</div>
