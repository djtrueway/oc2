<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.addon_idx');

class wpl_addon_idx_controller extends wpl_controller
{
    public $tpl_path = 'views.backend.addon_idx.tmpl';
    public $tpl;

    public function home()
    {
        wpl_global::min_access('administrator');

        //$this->mls_servers = wpl_addon_mls::get_servers();
        parent::render($this->tpl_path, $this->tpl);
    }
    public function wizard()
    {
        wpl_global::min_access('administrator');

        $this->tpl = 'wizard';
        //$this->idx_plan = wpl_request::getVar('idx_plan', 'valid');
        parent::render($this->tpl_path, $this->tpl);
    }
}