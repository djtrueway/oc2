<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');
_wpl_import('libraries.notifications.notifications');

class wpl_notifications_controller extends wpl_controller
{
    public $tpl_path = 'views.backend.notifications.tmpl';
    public $tpl;

    public function home()
    {
        /** check permission **/
        wpl_global::min_access('administrator');
        $this->tpl = wpl_request::getVar('tpl', 'default');
        
        // Create Nonce
        $this->nonce = wpl_security::create_nonce('wpl_notifications');
        
        if($this->tpl == 'modify')
        {
            $this->id = wpl_request::getVar('id', 0);
            $this->modify();
        }
        else
        {
            $this->notifications = wpl_notifications::get_notifications();
            parent::render($this->tpl_path, $this->tpl);
        }
    }
    
    public function modify()
    {
        $this->notification = wpl_notifications::get_notifications("AND `id`='".wpl_db::escape($this->id)."'", 'loadObject');
        $this->additional_memberships = explode(',', $this->notification->additional_memberships);
		$this->additional_users = explode(',', $this->notification->additional_users);
		$this->additional_emails = explode(',', $this->notification->additional_emails);
        
		$this->users = wpl_users::get_wpl_users();
		$this->memberships = wpl_users::get_wpl_memberships();
		$this->memberships_array = self::unset_additional_receipts($this->additional_memberships, $this->memberships);
		$this->users_array = self::unset_additional_receipts($this->additional_users, $this->users);

		$this->template_path = wpl_notifications::get_template_path($this->notification->template);
        $this->template = wpl_notifications::get_template_content($this->template_path, true);

        $this->template = '<meta http-equiv="Content-Type" content="text/html; charset=UTF-8">'.$this->template;

        /** Check SMS add-on */
        if(wpl_global::check_addon('sms'))
        {
            _wpl_import('libraries.addon_sms');
            $addon_sms = new wpl_addon_sms;

            $this->sms_additional_memberships = explode(',', $this->notification->sms_additional_memberships);
            $this->sms_additional_users = explode(',', $this->notification->sms_additional_users);
            $this->sms_additional_mobile = explode(',', $this->notification->sms_additional_mobile);

            $this->sms_memberships = wpl_users::get_wpl_memberships();
            $this->sms_memberships_array = self::unset_additional_receipts($this->sms_additional_memberships, $this->sms_memberships);
            $this->sms_users_array = self::unset_additional_receipts($this->sms_additional_users, $this->users);

            $this->sms_template_path = $addon_sms->get_sms_template_path($this->notification->template);
            $this->sms_template = $addon_sms->get_sms_template_content($this->sms_template_path);
            
            $this->sms_template = str_replace(array('\r\n','\n', '\r'), '&#013;&#010;', $this->sms_template);

        }

        parent::render($this->tpl_path, $this->tpl);
    }
    
    protected function unset_additional_receipts($additional, $default)
	{
		foreach($additional as $val) unset($default[$val]);
		return $default;
	}
    
    protected function generate_basic_options()
    {
        parent::render($this->tpl_path, 'internal_basic');
    }
    
    protected function generate_advanced_options()
    {
        parent::render($this->tpl_path, 'internal_advanced');
    }

    protected function generate_sms_advanced_options()
    {
        parent::render($this->tpl_path, 'internal_sms_advanced');
    }
}