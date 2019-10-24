<?php

defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.wpl_property_types');
_wpl_import('libraries.wpl_locations');

class idx_property_mapper
{

    protected $preConfig = array(
        'name' => 'Property Title',
        'description' => 'Property Description',
        'bedrooms' => 'Bedrooms',
        'bathrooms' => 'Bathrooms',
        'features' => 'Features',
        'price_period' => 'Price Type',
        'property_type' => 'Property Type',
        'listing_type' => 'Listing Type',
        'street_address' => 'Street',
        'postal_code' => 'Postal Code',
        'listing_price' => 'Price',
        'build_year' => 'Year Built',
        'square_feet' => 'Square Footage'
    );

    public function map(array $fields)
    {
        $preparedKeys = $this->prepareKeys(array_keys($fields));

        $dbstValues = $this->getDbstValues($preparedKeys);

        $dataToInsert = array();

        foreach ($dbstValues as $key => $value) {
            if (!empty($value)) {
                $dataToInsert[$value] = $fields[$key];
            }
        }

        $listingType = self::_('listing_type', $fields);

        if (!empty($listingType)) {
            $dataToInsert['listing'] = $this->getType($listingType);
        }

        $propertyType = self::_('property_type', $fields);

        if (!empty($propertyType)) {
            $dataToInsert['property_type'] = $this->getType($propertyType, 'property');
        }

        $dataToInsert['mls_id'] = self::_('listing_id', $fields);

        return $dataToInsert;
    }

    public function getLocationKeysForUpdate($jsondata)
    {
        $addressKeys = array('country', 'zipcode', 'street', 'city', 'state', 'street_number', 'street_suffix');

        $locations = array();

        foreach ($jsondata as $key => $value) {
            if (in_array($key, $addressKeys)) {
                $locations[$key] = $value;
            }
        }

        return $locations;
    }

    protected function mapSelectFromDbst($Values)
    {
        if (!is_array($Values)) {
            return array();
        }

        $mappings = array();

        foreach ($Values as $name => $val) {

            $query = wpl_db::select("SELECT `name`,`table_name`,`table_column`,`options`,`type` FROM `#__wpl_dbst` WHERE `name` = '{$name}' AND `enabled` != 0 ", 'loadAssoc');

            if (is_null($query) || !is_array($query)) {
                continue;
            }

            if ($query['type'] !== 'select') {
                continue;
            }

            if ($query['table_name'] !== 'wpl_properties') {
                continue;
            }

            $options = self::_('params', json_decode(self::_('options', $query, array()), true), array());


            foreach ($options as $option) {
                if (strtolower($option['value']) == strtolower($val) && $option['enabled'] == 1) {
                    $mappings[$query['table_column']] = $option['key'];
                }
            }
        }

        return $mappings;
    }

    protected function addIDXFields()
    {

        $fieldsToAdd = array(
            'Property Status' => array(
                'type' => 'select',
                'extra' => array(),
                'options' => array(
                    'params' => array(
                        array(
                            'value' => 'A',
                            'enabled' => 1,
                            'key' => 1
                        ),
                        array(
                            'value' => 'Active',
                            'enabled' => 1,
                            'key' => 2
                        ),
                        array(
                            'value' => 'Active Under Contract',
                            'enabled' => 1,
                            'key' => 3
                        ),
                        array(
                            'value' => 'BACK ON MARKET',
                            'enabled' => 1,
                            'key' => 4
                        ),
                        array(
                            'value' => 'Closed',
                            'enabled' => 1,
                            'key' => 5
                        ),
                        array(
                            'value' => 'CONTINGENT',
                            'enabled' => 1,
                            'key' => 6
                        ),
                        array(
                            'value' => 'Hold',
                            'enabled' => 1,
                            'key' => 7
                        ),
                        array(
                            'value' => 'Pending',
                            'enabled' => 1,
                            'key' => 8
                        ),
                        array(
                            'value' => 'Pendings',
                            'enabled' => 1,
                            'key' => 9
                        ),
                        array(
                            'value' => 'U',
                            'enabled' => 1,
                            'key' => 10
                        ),
                        array(
                            'value' => 'Sold',
                            'enabled' => 1,
                            'key' => 11
                        )
                    )
                )
            ),
            'Office Id' => array(
                'type' => 'text',
                'extra' => array(
                    'enabled' => 0
                ),
                'options' => array()
            ),
            'Agent Id' => array(
                'type' => 'text',
                'extra' => array(
                    'enabled' => 0
                ),
                'options' => array()
            ),
            'Office Name' => array(
                'type' => 'text',
                'extra' => array(
                    'enabled' => 0
                ),
                'options' => array()
            )
        );

        $categoryToSearch = 'Basic Details';

        // Create DBST Records 

        foreach ($fieldsToAdd as $field => $fieldParams) {

            if ($this->dbstExists($field, $fieldParams['type'])) {
                continue;
            }

            // create
            $this->makeDbst(
                    $field, $fieldParams['type'], $categoryToSearch, $fieldParams['options'], $fieldParams['extra']
            );
        }
    }

    private function makeDbst($name, $type, $category, array $options = array(), array $additionalParams = array())
    {

        if ( empty($name) || empty($type) || empty($category)  ) {
            return;
        }

        $db = wpl_db::get_DBO();

        $dbstId = wpl_flex::create_default_dbst();

        $dbstArray = array(
            'name' => $name,
            'type' => $type,
            'category' => $this->getFlexCategoryIdByName($category)
        );

        if (!empty($options)) {
            $dbstArray['options'] = json_encode($options);
        }

        if (!empty($additionalParams)) {
            foreach ($additionalParams as $addKey => $addValue) {
                if (isset($dbstArray[$addKey])) {
                    continue;
                }

                $dbstArray[$addKey] = $addValue;
            }
        }

        $db->update($db->prefix . 'wpl_dbst', $dbstArray
                , array('id' => $dbstId));

        return wpl_flex::run_dbst_type_queries($dbstId, $type, '0');
    }

    protected function getFlexCategoryIdByName($categoryName)
    {
        $catID = 1;

        if (empty($categoryName) || !is_string($categoryName)) {
            return $catID;
        }

        $categoryList = wpl_flex::get_categories();

        foreach ($categoryList as $category) {

            if (self::_('name', $category) != trim($categoryName)) {
                continue;
            }

            $catID = $category->id;
            break;
        }

        return $catID;
    }

    protected function getType($String, $type = 'listing')
    {
        $id = '';
        if ($type == 'listing') {
            foreach (wpl_listing_types::get_listing_types() as $i => $value) {
                if (self::_('name', $value) == $String) {
                    $id = self::_('id', $value);
                    break;
                }
            }
        } elseif ($type == 'property') {
            $query = wpl_db::select("SELECT * FROM `#__wpl_property_types`", 'loadAssocList');

            foreach ($query as $i => $value) {
                if (self::_('name', $value) == $String) {
                    $id = self::_('id', $value);
                    break;
                }
            }
        }
        return $id;
    }

    protected function getDbstValues($vArr)
    {
        $matchedList = array();
        foreach ($vArr as $key => $name) {
            $query = wpl_db::select("SELECT `name`,`table_name`,`table_column` FROM `#__wpl_dbst` WHERE `name` = '{$name}'", 'loadAssoc');

            if (is_null($query)) {
                $anotherTry = self::_($key, array_reverse($this->preConfig));
                if (!empty($anotherTry)) {
                    $query = wpl_db::select("SELECT `name`,`table_name`,`table_column` FROM `#__wpl_dbst` WHERE `name` = '{$anotherTry}'", 'loadAssoc');
                }
            }

            $tableName = self::_('table_name', $query);

            if ($tableName != 'wpl_properties') {
                continue;
            }

            $matchedList[$key] = self::_('table_column', $query);
        }
        return $matchedList;
    }

    protected function prepareKeys(array $kArr)
    {

        $pKeys = array();

        foreach ($kArr as $string) {
            $strArr = explode('_', $string);

            if (count($strArr) > 1) {
                $pKeys[$string] = implode(' ', array_map(function($k) {
                            return ucfirst($k);
                        }, $strArr));
                continue;
            }
            $pKeys[$string] = ucfirst($string);
        }

        return $pKeys;
    }

    protected function saveExternalImages($imgs, $pid, $db)
    {

        if (is_array($imgs) && count($imgs) > 0) {
            foreach ($imgs as $img) {
                $db->insert($db->prefix . 'wpl_items', array(
                    'parent_id' => $pid,
                    'creation_date' => date("Y-m-d H:i:s"),
                    'item_type' => 'gallery',
                    'item_cat' => 'external',
                    'item_name' => 'external_image' . $pid,
                    'item_extra3' => $img
                ));
            }
        }

        return $this;
    }

    protected function getFeatureParamIds(array $dbIds, $values)
    {
        $matchedIds = array();

        if (!is_string($values)) {
            return $matchedIds;
        }

        $features = explode(',', $values);

        foreach ($dbIds as $dbArray) {
            if (in_array($dbArray['value'], $features)) {
                $matchedIds[] = $dbArray['key'];
            }
        }

        return $matchedIds;
    }

    protected function setFeatures($featured, $pid, $db)
    {

        if (is_array($featured) && count($featured) > 0) {

            foreach ($featured as $ft => $featureValue) {

                if (empty($featureValue)) {
                    continue;
                }

                $featureName = $this->getFeatureName($ft);

                $sql = "SELECT `name`,`table_name`,`table_column`,`options` "
                        . "FROM `#__wpl_dbst` WHERE `name` = '{$featureName}' AND"
                        . " `type`='feature' AND `enabled` != 0";

                $query = wpl_db::select($sql, 'loadAssoc');

                if (is_null($query) || !is_array($query)) {
                    continue;
                }


                if ($query['table_name'] !== 'wpl_properties') {
                    continue;
                }

                $optionsType = self::_('type', json_decode($query['options'], true));

                // If Feature does not have any options
                if ($optionsType == 'none') {
                    continue;
                }


                $options = self::_('values', json_decode($query['options'], true));

                if (empty($featureValue)) {
                    continue;
                }

                if (is_null($options)) {
                    continue;
                }

                $paramIds = $this->getFeatureParamIds($options, $featureValue);


                if (empty($paramIds)) {
                    continue;
                }

                $optionsColumn = $query['table_column'] . '_options';

                $data = array(
                    $optionsColumn => implode(',', $paramIds),
                    $query['table_column'] => 1
                );

                $db->update($db->prefix . 'wpl_properties', $data, array(
                    'id' => $pid
                ));
            }
        }

        return $this;
    }

    private function getFeatureName($feature)
    {
        if (empty($feature) || !is_string($feature)) {
            return '';
        }

        $search = 'Features';
        $strpos = strpos($feature, $search);
        if (!$strpos) {
            return $feature;
        }

        return substr($feature, 0, $strpos);
    }

    protected function setPricePeriod($pricePeriod, $pid, $db)
    {
        if (is_string($pricePeriod) && !empty($pricePeriod)) {
            $query = wpl_db::select("SELECT `options` FROM `#__wpl_dbst` WHERE `table_column` = 'price_period'", 'loadAssoc');

            $options = json_decode(self::_('options', $query), true);

            if (isset($options['params'])) {
                foreach ($options['params'] as $param) {
                    if (self::_('value', $param) == $pricePeriod && self::_('enabled', $param) == 1) {

                        $insertArray = array(
                            'price_period' => $param['key']
                        );

                        $db->update($db->prefix . 'wpl_properties', $insertArray, array(
                            'id' => $pid
                        ));


                        break;
                    }
                }
            }
        }

        return $this;
    }

    public function getLotAreaUnitId($lUnit, $pid, $db)
    {
        if (is_string($lUnit) && !empty($lUnit)) {
            $query = wpl_db::select("SELECT * FROM `#__wpl_units`
             WHERE `enabled` = 1 and `name` = '{$lUnit}' and `type` = 2 ", 'loadAssoc');
            if (is_null($query)) {
                return;
            }

            $insertArray = array(
                'lot_area_unit' => $query['id']
            );

            $db->update($db->prefix . 'wpl_properties', $insertArray, array(
                'id' => $pid
            ));
        }
    }

    protected function setLocations($adr, $pid, $db)
    {

        if (is_array($adr) && count($adr) > 0) {
            $knownLocationsKeywords = array();
            $allowedArr = array('country', 'state', 'county', 'city');
            foreach ($adr as $k => $v) {

                if (!in_array($k, $allowedArr)) {
                    continue;
                }

                $query = wpl_db::select("SELECT `setting_name`,`setting_value` FROM `#__wpl_settings` WHERE `setting_value` = '" . ucfirst($k) . "'", 'loadAssoc'
                );

                if (!is_null($query)) {
                    $locationSettingName = self::_('setting_name', $query);
                    $knownLocationsKeywords[str_replace('_keyword', '_name', $locationSettingName)] = $v;
                    $key = strtolower($k);

                    if (in_array($key, array('country', 'state'))) {
                        $locationLevel = $this->getLoctionLevel($locationSettingName);
                        $knownLocationsKeywords[str_replace('_keyword', '_id', $locationSettingName)] = $this->findLocationId($adr[$k], $locationLevel, $key);
                    }

                    unset($adr[$k]);
                }
            }

            $prepareLeft = $this->prepareKeys(array_keys($adr));
            $leftDbStVals = $this->getDbstValues($prepareLeft);

            foreach ($leftDbStVals as $kkk => $vvv) {
                $knownLocationsKeywords[$vvv] = $adr[$kkk];
            }

            if (count($knownLocationsKeywords)) {
                $db->update($db->prefix . 'wpl_properties', $knownLocationsKeywords, array(
                    'id' => $pid
                ));
            }
        }

        return $this;
    }

    protected function getLoctionLevel($locationNameString)
    {

        if (empty($locationNameString) || !is_string($locationNameString)) {
            return;
        }

        $expStr = explode('_', $locationNameString);

        if (empty($expStr)) {
            return;
        }

        $keyword = self::_(0, $expStr);

        $level = substr($keyword, strlen($keyword) - 1);

        return $level;
    }

    protected function findLocationId($locationName, $locationLevel = 1, $is = 'country')
    {

        if (empty($locationName) || !is_string($locationName) ||
                !in_array($is, array('country', 'state'))) {
            return;
        }

        if ($is == 'country') {
            return wpl_locations::get_location_id($locationName, null, $locationLevel);
        } else {
            $fullNameOfState = wpl_locations::get_location_name_by_abbr($locationName, $locationLevel);
            return wpl_locations::get_location_id($fullNameOfState, null, $locationLevel);
        }
    }

    protected function mapPTypes(array $pTypes)
    {


        $currentPTypes = wpl_db::select("SELECT * FROM `#__wpl_property_types`", 'loadAssocList');

        $pNames = array_map(function ($pIndex) {
            return self::_('name', $pIndex);
        }, $currentPTypes);


        foreach ($pTypes as $propertyType) {
            if (!in_array($propertyType, $pNames)) {
                $parentId = self::getPropertyParentId($currentPTypes, $propertyType);
                wpl_property_types::insert_property_type($parentId, $propertyType);
            }
        }
    }

    protected function populateFeatures(array $featureList)
    {

        if (empty($featureList)) {
            return;
        }

        $features = array();

        $lastCounts = array();

        foreach ($featureList as $feature) {

            $featureName = $this->getFeatureName($feature['category']);

            if (empty($featureName)) {
                continue;
            }

            if (array_key_exists($featureName, $lastCounts)) {
                $lastCounts[$featureName] ++;
            } else {
                $lastCounts[$featureName] = 1;
            }

            if (!array_key_exists('type', $features[$featureName])) {
                $features[$featureName]['type'] = 'multiple';
            }

            $features[$featureName]['values'][] = array(
                'key' => $lastCounts[$featureName],
                'value' => $feature['name']
            );
        }


        if (empty($features)) {
            return;
        }

        $this->saveMappedFeatures($features);
    }

    protected function dbstExists($dbstName, $dbstType)
    {
        if (!is_string($dbstName) || !is_string($dbstType)) {
            return false;
        }

        $sql = "SELECT `id` FROM `#__wpl_dbst` WHERE `name` = '{$dbstName}' AND `type`='{$dbstType}'";

        $query = wpl_db::select($sql, 'loadAssoc');

        return (is_null($query)) ? false : true;
    }

    private function saveMappedFeatures(array $mappedFeatures)
    {
        $categoryToSearch = 'Features';
        $type = 'feature';

        foreach ($mappedFeatures as $feature => $fValues) {
            if ($this->dbstExists($feature, $type)) {
                continue;
            }

            $this->makeDbst($feature, $type, $categoryToSearch, $fValues);
        }
    }

    private static function getPropertyParentId(array $currentPTypes, $pName)
    {

        $pId = 1;

        if (!count($currentPTypes)) {
            return $pId;
        }

        foreach ($currentPTypes as $k => $v) {
            $expectedPropertyParent = self::_('0', explode(' ', $pName));

            if ($expectedPropertyParent == $v['name']) {
                $pId = $v['id'];
                break;
            }
        }

        return $pId;
    }

    protected static function _($Key, $Collection, $Default = '')
    {
        $Keys = explode('.', $Key);
        $Data = $Collection;

        foreach ($Keys as $kkk) {
            if (is_object($Data)) {

                $Data = (array) $Data;
            }
            if (!isset($Data[$kkk])) {
                return $Default;
            }

            $Data = $Data[$kkk];
        }
        return $Data;
    }

}