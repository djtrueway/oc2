<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

$result = NULL;

$type = isset($params['type']) ? $params['type'] : 1; # 1 == ul and 0 == selectbox
$return_array = isset($params['return_array']) ? $params['return_array'] : 0;

$sort_options = isset($params['sort_options']) ? $params['sort_options'] : wpl_sort_options::get_sort_options(2, 1);

$result_array = array();
foreach($sort_options as $sort_option)
{
	$result_array['sort_options'][] = array
	(
		'field_name' => $sort_option['field_name'],
		'url' => '',
		'active' => $this->orderby == $sort_option['field_name'] ? 1 : 0,
		'order' => ($this->order == 'DESC' and $this->orderby == $sort_option['field_name']) ? 'ASC' : 'DESC',
		'name' => $sort_option['name']
	);
}

$html = '';
if($type == 0)
{
	$html .= '<select class="wpl_plist_sort" onchange="wpl_page_sortchange(this.value);">';
	
	foreach($sort_options as $sort_option)
	{
        $asc_label = sprintf(__('%s ascending', 'real-estate-listing-realtyna-wpl'), __($sort_option['name'], 'real-estate-listing-realtyna-wpl'));
        $desc_label = sprintf(__('%s descending', 'real-estate-listing-realtyna-wpl'), __($sort_option['name'], 'real-estate-listing-realtyna-wpl'));

        if(isset($sort_option['asc_label']) and trim($sort_option['asc_label'])) $asc_label = __($sort_option['asc_label'], 'real-estate-listing-realtyna-wpl');
        if(isset($sort_option['desc_label']) and trim($sort_option['desc_label'])) $desc_label = __($sort_option['desc_label'], 'real-estate-listing-realtyna-wpl');

		if(!isset($sort_option['asc_enabled']) or (isset($sort_option['asc_enabled']) and $sort_option['asc_enabled'])) $html .= '<option value="wplorderby='.urlencode($sort_option['field_name']).'&amp;wplorder=ASC" '.(($this->orderby == $sort_option['field_name'] and $this->order == 'ASC') ? 'selected="selected"' : '').'>'.$asc_label.'</option>';
		if(!isset($sort_option['desc_enabled']) or (isset($sort_option['desc_enabled']) and $sort_option['desc_enabled'])) $html .= '<option value="wplorderby='.urlencode($sort_option['field_name']).'&amp;wplorder=DESC" '.(($this->orderby == $sort_option['field_name'] and $this->order == 'DESC') ? 'selected="selected"' : '').'>'.$desc_label.'</option>';
	}
	
	$html .= '</select>';
}
elseif($type == 1)
{
	$html .= '<ul>';
	$sort_type = '';

	foreach($sort_options as $sort_option)
	{
		$class = "wpl_plist_sort";
        $order = isset($sort_option['default_order']) ? $sort_option['default_order'] : 'DESC';
        $current_order = $order;
		
		if($this->orderby == $sort_option['field_name'])
        {
            $class = "wpl_plist_sort wpl_plist_sort_active";
            $order = ($this->order == 'ASC' ? 'DESC' : 'ASC');
            
            $current_order = $this->order;
        }
        
		$label = __($sort_option['name'], 'real-estate-listing-realtyna-wpl');
        
        if($current_order == 'ASC' and isset($sort_option['asc_label']) and trim($sort_option['asc_label'])) $label = __($sort_option['asc_label'], 'real-estate-listing-realtyna-wpl');
        if($current_order == 'DESC' and isset($sort_option['desc_label']) and trim($sort_option['desc_label'])) $label = __($sort_option['desc_label'], 'real-estate-listing-realtyna-wpl');
        
		$html .= '<li><div class="'.$class;
		
		if($this->orderby == $sort_option['field_name'])
		{
			if($this->order == 'ASC') $sort_type = 'sort_up';
			else $sort_type = 'sort_down';
			
			$html .= ' '.$sort_type;
		}
		
		$html .= '" onclick="wpl_page_sortchange(\'wplorderby='.urlencode($sort_option['field_name']).'&amp;wplorder='.$order.'\');">'.$label;
		$html .= '</div></li>';
	}
	
	$html .= '</ul>';
}

$result_array['html'] = $html;

if($return_array) $result = $result_array;
else $result = $html;