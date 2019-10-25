<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

/** activity class **/
class wpl_activity_main_agent_info extends wpl_activity
{
    public $tpl_path = 'views.activities.agent_info.tmpl';

	public function start($layout, $params)
	{
		/** global settings **/
		$this->settings = wpl_settings::get_settings();
		$this->microdata = isset($this->settings['microdata']) ? $this->settings['microdata'] : 0;
		$this->itemscope = ($this->microdata) ? 'itemscope' : '';
		$this->itemprop_name = ($this->microdata) ? 'itemprop="name"' : '';
		$this->itemprop_value = ($this->microdata) ? 'itemprop="value"' : '';
		$this->itemprop_url = ($this->microdata) ? 'itemprop="url"' : '';
		$this->itemprop_telephone = ($this->microdata) ? 'itemprop="telephone"' : '';
		$this->itemprop_faxNumber = ($this->microdata) ? 'itemprop="faxNumber"' : '';
		$this->itemprop_image = ($this->microdata) ? 'itemprop="image"' : '';
		$this->itemprop_logo = ($this->microdata) ? 'itemprop="logo"' : '';
		$this->itemprop_address = ($this->microdata) ? 'itemprop="address"' : '';
        $this->itemprop_email  = ($this->microdata) ? 'itemprop="email"' : '';
		$this->itemprop_description = ($this->microdata) ? 'itemprop="description"' : '';
		$this->itemprop_additionalProperty = ($this->microdata) ? 'itemprop="additionalProperty"' : '';
		$this->itemprop_addressLocality = ($this->microdata) ? 'itemprop="addressLocality"' : '';
		$this->itemtype_PostalAddress = ($this->microdata) ? 'itemtype="http://schema.org/PostalAddress"' : '';
		$this->itemtype_PropertyValue = ($this->microdata) ? 'itemtype="http://schema.org/PropertyValue"' : '';
		$this->itemtype_RealEstateAgent = ($this->microdata) ? 'itemtype="http://schema.org/RealEstateAgent"' : '';

		/** include layout **/
		$layout_path = _wpl_import($layout, true, true);
		include $layout_path;
	}
}