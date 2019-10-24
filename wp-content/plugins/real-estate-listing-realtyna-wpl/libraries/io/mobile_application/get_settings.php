<?php
/** no direct access **/
defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.sort_options');

class wpl_io_cmd_get_settings extends wpl_io_cmd_base
{
    protected $built;

    /**
     * Building the command
     * @author Chris H. <chris@realtyna.com>
     * @return mixed
     */
    public function build()
    {
        $this->built['wizard_fragment'] = $this->create_wizard_fragment();
        $this->built['filter_fragment'] = $this->create_filter_fragment();
        $this->built['settings']['general'] = wpl_settings::get_settings(15);
        $this->built['settings']['listing_types'] = $this->get_listing_types();
        $this->built['settings']['listing_sorts'] = $this->get_listing_sorts();
        
        return $this->built;
    }

    /**
     * validation params
     * @author Chris H. <chris@realtyna.com>
     * @return bool
     */
    public function validate()
    {
        if(wpl_global::check_addon('mobile_application') == false)
        {
            $this->error = "Add-on mobile application is not installed";
            return false;
        }
        
        return true;
    }

    /**
     * Creating filter section
     * @author Steve A. <steve@realtyna.com>
     * @return array
     */
    private function create_filter_fragment()
    {
        $filter = array();
        $listing_types = $this->prepare_array(wpl_global::get_listings());
        $property_types = $this->prepare_array(wpl_global::get_property_types());
        $appliances = $this->get_fields('5', 'feature');
        $neighborhood = $this->get_fields('6', 'neighborhood');
        $tags = $this->get_fields('11', 'tag');
        
        $rooms = array(
                    array('text' => 'ANY', 'min' => '0', 'max' => '10', 'selected' => true),
                    array('text' => '+1', 'min' => '1','max' => '10'),
                    array('text' => '+2', 'min' => '2','max' => '10'),
                    array('text' => '+3', 'min' => '3','max' => '10'),
                    array('text' => '+4', 'min' => '4','max' => '10'),
                    array('text' => '+5', 'min' => '5','max' => '10')
                );

        if(count($listing_types) > 2)
        {
            $filter[] = array('section_type' => 'spinner', 
                              'column_name' => 'sf_select_listing', 
                              'title' => 'LISTING_TYPE', 
                              'items' => $listing_types);
        }

        if(count($property_types) > 2)
        {
            $filter[] = array('section_type' => 'spinner',
                              'column_name' => 'sf_select_property_type',
                              'title' => 'PROPERTY_TYPE',
                              'items' => $property_types);
        }

        $filter[] = array('section_type' => 'minmax_text',
                          'title' => 'PRICE_RANGE',
                          'column_name' => 'price',
                          'min_prefix' => 'sf_tmin_',
                          'max_prefix' => 'sf_tmax_');

        $filter[] = array('section_type' => 'range_buttons',
                          'title' => 'BEDROOMS',
                          'column_name' => 'bedrooms',
                          'min_prefix' => 'sf_tmin_',
                          'max_prefix' => 'sf_tmax_',
                          'buttons' => $rooms);

        $filter[] = array('section_type' => 'range_buttons',
                          'title' => 'BATHROOMS',
                          'column_name' => 'bathrooms',
                          'min_prefix' => 'sf_tmin_',
                          'max_prefix' => 'sf_tmax_',
                          'buttons' => $rooms);

        $filter[] = array('section_type' => 'minmax_text',
                          'title' => 'LIVING_AREA',
                          'column_name' => 'living_area',
                          'min_prefix' => 'sf_tmin_',
                          'max_prefix' => 'sf_tmax_');

        $filter[] = array('section_type' => 'edit_text',
                          'title' => 'KEYWORDS',
                          'column_name' => 'sf_textmeta_keywords',
                          'placeholder' => '',
                          'default_text' => '');

        $checkboxes = array();
        if(count($appliances)) $checkboxes[] = array('title' => 'APPLIANCES', 'items' => $appliances);
        if(count($neighborhood)) $checkboxes[] = array('title' => 'NEIGHBORHOOD', 'items' => $neighborhood);
        if(count($tags)) $checkboxes[] = array('title' => 'PROPERTY_TAGS', 'items'=> $tags);
        if(count($checkboxes)) $filter[] = array('section_type' => 'checkbox_group', 'title' => '', 'groups' => $checkboxes);

        return $filter;
    }

    /**
     * Creating property wizard section
     * @author Steve A. <steve@realtyna.com>
     * @return array
     */
    private function create_wizard_fragment()
    {
        $wizard = array('cats' => array(), 'fields' => array());
        $category_ids = '1, 2, 3, 4, 5, 6, 11';
        $field_types = "'text', 'textarea', 'number', 'area', 'price', 'select', 'listings', 'property_types', 'date', 'locations', 'checkbox', 'boolean', 'neighborhood', 'feature', 'tag', 'gallery'";

        $categories = wpl_flex::get_categories(1, 0, "AND `enabled` >= '1' AND `kind` = '0' AND `id` IN ({$category_ids})");
        $fields = array();

        foreach ($categories as $category)
        {
            $wizard['cats'][$category->id] = $category->name;

            $cat_fields = wpl_flex::get_fields('', 0, 0, '', '', "AND `category` = '{$category->id}' AND `enabled` >= '1' AND `addon_mobile_wizard` >= '1' AND `kind` = '0' AND `category` IN ({$category_ids}) AND `type` IN ({$field_types})");

            $fields = array_merge($fields, $cat_fields);
        }

        $index = 0;
        foreach ($fields as $field)
        {
            $index++;
            $wizard['fields'][$field->id] = array('id' => $field->id, 'mandatory' => $field->mandatory ? true : false,
                                                  'name' => $field->name, 'type' => $field->type, 
                                                  'values' => array(), 'table_column' => $field->table_column, 
                                                  'category' => $field->category, 'index' => $index);

            if($field->type == 'select')
            {
                $values = array();
                $options = json_decode($field->options);

                foreach ($options->params as $param) 
                {
                    if($param->enabled) $values[$param->key] = $param->value;
                }

                $wizard['fields'][$field->id]['values'] = $values;
            }
            elseif($field->type == 'listings')
            {
                $values = array();
                $listing_types = wpl_global::get_listings();

                foreach ($listing_types as $listing_type) 
                {
                    $values[$listing_type['id']] = $listing_type['name'];
                }

                $wizard['fields'][$field->id]['values'] = $values;
            }
            elseif($field->type == 'property_types')
            {
                $values = array();
                $property_types = wpl_global::get_property_types();

                foreach ($property_types as $property_type) 
                {
                    $values[$property_type['id']] = $property_type['name'];
                }

                $wizard['fields'][$field->id]['values'] = $values;
            }
            elseif($field->type == 'locations')
            {
                $values = array();
                $location_settings = wpl_settings::get_settings(3);
                $parent = '';

                for($i = 1; $i <= 7; $i++)
                {
                    if(!$location_settings["location{$i}_keyword"]) break;
                    $locations = ($i == 1) ? wpl_locations::get_locations($i, $parent, 1) : array();
                    $type = ($location_settings['location_method'] == 1 and $i > 2) ? 'text' : 'select';
                    $column = ($type == 'text') ? "location{$i}_name" : "location{$i}_id";
                    $values[$i] = array('level' => $i, 'name' => $location_settings["location{$i}_keyword"], 'type' => $type, 'table_column' => $column, 'values' => $locations);
                    
                    $parent = '';
                    if($type == 'select' and count($locations) == 1)
                        $parent = array_keys($locations)[0];
                }

                $wizard['fields'][$field->id]['values'] = $values;
            }
            elseif($field->type == 'price')
            {
                $values = array();
                $units = wpl_units::get_units(4);

                foreach ($units as $unit) 
                {
                    $values[$unit['id']] = $unit['name'];
                }

                $wizard['fields'][$field->id]['values'] = $values;
            }
            elseif($field->type == 'area')
            {
                $values = array();
                $units = wpl_units::get_units(2);

                foreach ($units as $unit) 
                {
                    $values[$unit['id']] = $unit['name'];
                }

                $wizard['fields'][$field->id]['values'] = $values;
            }
        }

        return $wizard;
    }

    /**
     * Prepare array for Listing and Property types
     * @author Steve A. <steve@realtyna.com>
     * @param  array  $objects  Input objects
     * @return array
     */
    private function prepare_array($objects)
    {
        $array = array(array('value' => '-1', 'text' => 'ANY'));

        foreach ($objects as $obj) 
        {
            $array[] = array('value' => $obj['id'], 'text' => __($obj['name'], 'real-estate-listing-realtyna-wpl'));
        }

        return $array;
    }

    /**
     * Get flex fields
     * @author Steve A. <steve@realtyna.com>
     * @param  integer $category Category ID
     * @param  string  $type     Field Type
     * @return array
     */
    private function get_fields($category, $type)
    {
        $array = array();
        $fields = wpl_flex::get_fields('', 0, 0, '', '', "AND `category` = '$category' AND `type` = '$type' AND `kind` = 0 AND `enabled` >= 1");

        foreach ($fields as $field) 
        {
            $array[] = array('column_name' => 'sf_select_'.$field->table_column, 'value' => __($field->name, 'real-estate-listing-realtyna-wpl'));
        }

        return $array;
    }

    /**
     * Get listing types
     * @author Steve A. <steve@realtyna.com>
     * @return array
     */
    private function get_listing_types()
    {
        $selected = 1;
        $listing_types = wpl_global::get_listings();
        $listings = array();

        foreach($listing_types as $list)
        {
            $name = __($list['name'], 'real-estate-listing-realtyna-wpl');
            $listings[] = array(
                'id' => $list['id'],
                'name' => $name,
                'selected' => $selected,
                'marker_name' => 'ic_for_sale.9',
                'bubble_name' => 'ic_bubble_0_for_sale',
                'most_densely_bubble_name' => 'ic_bubble_1_for_sale',
                'notification_icon_name' => 'ic_for_sale_notification',
            );

            $selected = 0;
        }
        
        return $listings;
    }

    /**
     * Get listing sort options
     * @author Steve A. <steve@realtyna.com>
     * @return array
     */
    private function get_listing_sorts()
    {
        $array = array();
        $sorts = wpl_sort_options::get_sort_options(1);
        
        foreach ($sorts as $sort)
        {
            $array[] = array('id' => $sort['id'], 'language_keyword' => __($sort['name'], 'real-estate-listing-realtyna-wpl'), 'column_name' => $sort['field_name']);
        }
        
        return $array;
    }
}
