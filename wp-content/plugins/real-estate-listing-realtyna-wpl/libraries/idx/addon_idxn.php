<?php

defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.idx.idx_property_mapper');

class addon_idxn extends idx_property_mapper
{
    /*
     * @var string
     */

   protected $_api = 'https://idx.realtyfeed.com/';
    
    /*
     * @var array
     */
    protected $idxUserCredentials;

    /*
     * @var array
     */
    protected $mlsInfo;


    /*
     * @var int
     */
    protected $stepsDone;

    /*
     * @var int
     */
    protected $userId;

    /*
     * @var string
     */
    protected $token;

    /*
     * @var int
     */
    protected $wpUserId;
    protected $configStatus;

    public function __construct()
    {
        $this->idxUserCredentials = get_option('wpl_addon_idx_user_credentials');
        $this->stepsDone = get_option('wpl_addon_idx_user_steps_done');
        $this->mlsInfo = get_option('wpl_addon_idx_mls_data');

        if ($this->stepsDone >= 1 && false == $this->idxUserCredentials && !is_array($this->idxUserCredentials)) {
            wp_send_json(array(
                'status' => 404,
                'message' => 'Authorize First'
            ));
        }

        if ($this->stepsDone >= 2 && false == $this->mlsInfo && !is_array($this->mlsInfo)) {
            wp_send_json(array(
                'status' => 404,
                'message' => 'First you have to choose MLS Provider.'
            ));
        }


        $this->userId = self::_('user_id', $this->idxUserCredentials);
        $this->token = self::_('token', $this->idxUserCredentials);
        $this->wpUserId = self::_('wp_user_id', $this->idxUserCredentials);
        $this->configStatus = ( intval(get_option('wpl_addon_idx_user_config_status', false)) === 1 ) ? true : false;
    }

    /*
     * @desc register user in idx cache server
     * @param array $fields
     * @return array 
     */

    public static function register(array $fields)
    {
        if (empty($fields)) {
            return array(
                'status' => 500,
                'message' => 'Fill all required fields'
            );
        }

        $apiEndpoint = (new static)->_api . 'api/create-user/';

        $fields['phone_number'] = '+' . $fields['phone_number'];

        $request = wp_remote_post($apiEndpoint, array(
            'timeout' => 60,
            'body' => $fields
        ));



        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 201) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        # if user registered succesfully

        $idxUser = (array) json_decode(self::_('body', $request));
        $idxUser['wp_user_id'] = get_current_user_id();

        self::addOption('wpl_addon_idx_user_credentials', $idxUser);
        self::addOption('wpl_addon_idx_user_steps_done', 1);


        return array(
            'status' => 201,
            'message' => 'Idx user created succesfully'
        );
    }

    /*
     * @desc Get all available MLS Providers
     * @return array 
     */

    public function getProviders()
    {

        $apiEndpoint = $this->_api . 'api/providers/';

        $request = wp_remote_get($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            )
        ));

        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 200) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        return array(
            'status' => $statusCode,
            'message' => json_decode(self::_('body', $request))
        );
    }

    /*
     * @desc Save chosen provider in idx cache server
     * @param array $fields
     * @return array 
     */

    public function save(array $fields)
    {
        if (empty($fields)) {
            return array(
                'status' => 500,
                'message' => 'Fill all required fields'
            );
        }

        $postBody = array(
            'user_id' => $this->userId,
            'mls_id' => self::_('mls_id', $fields)
        );

        $apiEndpoint = $this->_api . 'api/choose-provider/';

        $request = wp_remote_post($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            ),
            'body' => $postBody
        ));

        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 201) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        self::addOption('wpl_addon_idx_mls_data', $fields);
        self::addOption('wpl_addon_idx_user_steps_done', 2);

        return array(
            'status' => 200,
            'message' => 'MLS data chosen sucessfully'
        );
    }

    /*
     * @desc Get chosen MLS Provider
     * @return array 
     */

    public function getChosenProvider()
    {
        $apiEndpoint = $this->_api . 'api/selected-provider/';

        $request = wp_remote_post($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            ),
            'body' => array(
                'user_id' => $this->userId
            )
        ));

        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 200) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        return array(
            'status' => 200,
            'message' => json_decode(self::_('body', $request))
        );
    }

    /*
     * @desc Get required fields for payment
     * @return array 
     */

    public function getPaymentCreds()
    {
        $mlsId = self::_('mls_id', $this->mlsInfo);
        $mlsProvider = self::_('provider', $this->mlsInfo);

        return array(
            'status' => 200,
            'message' => array(
                'user_id' => self::_('user_id', $this->idxUserCredentials),
                'provider_id' => $mlsId,
                'mls_provider' => $mlsProvider,
                'token' => self::_('token', $this->idxUserCredentials)
            )
        );
    }

    /*
     * @desc Check if user paid 
     * @return array 
     */

    public function checkPayment()
    {
        $apiEndpoint = $this->_api . 'api/check-payment/' . $this->userId;

        $request = wp_remote_get($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            )
        ));

        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        return array(
            'status' => $statusCode,
            'message' => self::_('response.message', $request)
        );
    }

    protected function addFeatures()
    {

        $mlsId = self::_('mls_id', $this->mlsInfo);
        $apiEndpoint = $this->_api . 'rest-auth/template/features/server=' . $mlsId;

        //Make Request
        $request = wp_remote_get($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            )
        ));

        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 200) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        $result = wp_remote_retrieve_body($request);

        $jsonBody = json_decode($result, true);

        if (is_null($jsonBody)) {
            return;
        }

        $featureList = self::_('results', $jsonBody, array());

        $this->populateFeatures($featureList);
    }

    /*
     * @desc Listing Configuration
     * @param array $fields
     * @return array 
     */

    public function configure(array $fields)
    {

        if (empty($fields)) {
            return array(
                'status' => 500,
                'message' => 'Fill all required fields'
            );
        }

        $apiEndpoint = $this->_api . 'api/configuration/';

        $fields['user_id'] = $this->userId;
        $fields['provider_id'] = self::_('mls_id', $this->mlsInfo);
        $userConfigStatus = ( $fields['property_status'] === 1 ) ? 0 : 1;

        $request = wp_remote_post($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            ),
            'body' => $fields
        ));

        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 201) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        self::addOption('wpl_addon_idx_user_steps_done', 4);
        self::addOption('wpl_addon_idx_user_config_status', $userConfigStatus);

        $this->mapMlsProperties();
        $this->addFeatures();
        $this->addIDXFields();

        return array(
            'status' => $statusCode,
            'message' => 'User configuration saved succesfully'
        );
    }

    protected function mapMlsProperties($fromTrial = false)
    {
        $apiEndpoint = $this->_api . 'api/listing-types/';

        $mlsId = ($fromTrial) ? 1 : intval(self::_('mls_id', $this->mlsInfo));

        $request = wp_remote_post($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            ),
            'body' => array(
                'mls_id' => $mlsId
            )
        ));

        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 200) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        $jsonBody = json_decode(self::_('body', $request), true);

        $propertyTypes = array();

        if (count($jsonBody)) {

            foreach ($jsonBody as $propertyData) {
                $propertyTypes[] = self::_('property_type', $propertyData);
            }


            if (!count($propertyTypes)) {
                return;
            }


            $this->mapPTypes($propertyTypes);
        }
    }

    public function getStatus()
    {
        $chosenMlsData = $this->getChosenProvider();

        if (isset($chosenMlsData['message']) && $chosenMlsData['status'] == 200) {
            $chosenMlsData['message']->configStatus = self::_('status', $this->checkConfigStatus());
        }

        return $chosenMlsData;
    }

    public function checkConfigStatus()
    {
        $apiEndpoint = $this->_api . 'api/check-status/' . $this->userId;
        $request = wp_remote_get($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            )
        ));

        self::checkRequestError($request);

        return json_decode(self::_('body', $request));
    }

    public function backStep($stepValue)
    {
        $allowedActions = array(
            'register',
            'provider'
        );

        if (!in_array($stepValue, $allowedActions)) {
            wp_send_json(array(
                'status' => 404,
                'message' => 'Step Not Allowed'
            ));
        }


        $apiEndpoint = $this->_api . 'api/delete-activity/' . trim($stepValue);

        $request = wp_remote_get($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            )
        ));

        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 200) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        self::addOption('wpl_addon_idx_user_steps_done', $this->stepsDone - 1);

        if ($stepValue == 'register') {
            delete_option('wpl_addon_idx_user_credentials');
        } elseif ($stepValue == 'provider') {
            delete_option('wpl_addon_idx_mls_data');
        }

        return array(
            'status' => 200,
            'message' => 'previous step'
        );
    }

    private static function addOption($optionName, $data)
    {

        return ( get_option($optionName) === false) ? add_option($optionName, $data) : update_option($optionName, $data);
    }

    private static function checkRequestError($request)
    {
        if (is_wp_error($request)) {
            wp_send_json(array(
                'status' => 500,
                'message' => $request->get_error_message()
            ));
        }
    }

    protected function checkRequestType($request)
    {
        if (!$request instanceof WP_REST_Request) {
            wp_send_json_error(array(
                'message' => 'Bad Request'
                    ), 400);
        }

        if (!is_array($request->get_json_params()) || $request->get_header('content-type') != 'application/json') {
            wp_send_json_error(array(
                'message' => 'Wrong request type, Content type have to be application/json'
                    ), 400);
        }
    }

    /*
     * @desc Import Listings from cache server,
     */

    public function import($request, $fromAPI = true)
    {
        if ($fromAPI) {
            $this->checkAccess(self::_('token', $request->get_params()));
            $this->checkRequestType($request);

            $jsonProperty = $request->get_json_params();
        } else {
            $jsonProperty = $request;
        }

        global $wpdb;

        // check if property exists on listing_id
        $listingId = self::_('listing_id', $jsonProperty);

        $propertyExists = wpl_property::pid($listingId);

        if (is_null($propertyExists) === false) {
            if ($fromAPI) {
                wp_send_json_error(array(
                    'message' => 'Property with mls id ' . $listingId . ' already exists'
                        ), 409);
            }

            return array(
                'message' => 'Property with mls id ' . $listingId . ' already exists'
            );
        }

        //$extraInfo = self::_('extra_data', $jsonProperty, array());

        $pid = wpl_property::create_property_default($this->wpUserId);

        $selectTypes = array(
            'Property Status' => self::_('status', $jsonProperty, '')
        );

        $mappedResult = $this->map($jsonProperty);
        $mappedSelects = $this->mapSelectFromDbst($selectTypes);


        if (!empty($mappedSelects)) {
            foreach ($mappedSelects as $field => $fieldValue) {
                $mappedResult[$field] = $fieldValue;
            }
        }


        $updateQuery = $wpdb->update($wpdb->prefix . 'wpl_properties', $mappedResult, array(
            'id' => $pid
        ));

        if (!$updateQuery) {
            if ($fromAPI) {
                wp_send_json_error(array(
                    'message' => 'There was an error. Please contact administrator'
                        ), 500);
            }

            return array(
                'status' => 500,
                'message' => 'There was an error. Please contact administrator'
            );
        }

        $this->setLocations(self::_('address.0', $jsonProperty), $pid, $wpdb)
                ->saveExternalImages(self::_('images', $jsonProperty), $pid, $wpdb)
                ->setFeatures(self::_('features.0', $jsonProperty), $pid, $wpdb)
                ->setPricePeriod(self::_('price_period', $jsonProperty), $pid, $wpdb)
                ->getLotAreaUnitId(self::_('lot_area_unit', $jsonProperty), $pid, $wpdb);



        if (!wpl_property::finalize($pid, 'edit', $this->wpUserId)) {
            if ($fromAPI) {
                wp_send_json_error(array(
                    'message' => 'Cant finalize property'
                ));
            }
            return array(
                'status' => 500,
                'message' => 'Cant finalize property'
            );
        }

        if ($fromAPI) {
            wp_send_json_success(array(
                'message' => 'Property created succesfully'
                    ), 201);
        }
        return array(
            'status' => 201,
            'pid' => $pid,
            'message' => 'Property created succesfully'
        );
    }

    public function getIdxUserCredentials()
    {
        return $this->idxUserCredentials;
    }

    public function importTrialListings()
    {


        $apiEndpoint = $this->_api . 'api/trial-data';

        $request = wp_remote_get($apiEndpoint, array(
            'timeout' => 120,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            )
        ));


        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 200) {
            return array(
                'status' => $statusCode,
                'message' => "Cannot Fetch Listings"
            );
        }

        $jsonBody = json_decode(self::_('body', $request), true);

        if (!count($jsonBody)) {
            return array(
                'status' => 404,
                'message' => "There is not active listing to import"
            );
        }

        $this->mapMlsProperties(true);

        $lastIimportError = array();
        $savedTrialPids = array();



        foreach ($jsonBody as $key => $json) {
            $imported = $this->import($json, false);
            $savedTrialPids[] = self::_('pid', $imported);
            if (self::_('status', $imported) != 201) {
                $lastIimportError = $imported;
            }
        }

        if (count($lastIimportError) > 0) {
            return $lastIimportError;
        }


        self::addOption('wpl_idx_addon_trial_imported', 1);
        self::addOption('wpl_idx_addon_saved_trial_pids', $savedTrialPids);

        return array(
            'status' => 201,
            'message' => 'Properties imported succesfully'
        );
    }

    public function resetTrialListings()
    {
        $trialPids = get_option('wpl_idx_addon_saved_trial_pids');

        if (get_option('wpl_idx_addon_trial_imported') != 1 || $trialPids === false) {
            return array(
                'status' => 404,
                'message' => 'Trial listings has not imported yet'
            );
        }

        if (get_option('wpl_addon_idx_trial_reseted') == 1) {
            return array(
                'status' => 401,
                'message' => 'You already reseted trial version.'
            );
        }

        $isError = false;

        foreach ($trialPids as $pid) {
            if (wpl_property::delete($pid) != true) {
                $isError = true;
            }
        }

        if ($isError) {
            return array(
                'status' => 500,
                'message' => 'There was an error when trying to delete properties'
            );
        }

        self::addOption('wpl_addon_idx_trial_reseted', 1);
        return array(
            'status' => 201,
            'message' => 'Listings deleted succesfully'
        );
    }

    public function requestProvider(array $fields)
    {
        if (empty($fields)) {
            return array(
                'status' => 500,
                'message' => 'Fill all required fields'
            );
        }

        $fields['user_id'] = $this->userId;

        $apiEndpoint = $this->_api . 'api/request-provider/';

        $request = wp_remote_post($apiEndpoint, array(
            'timeout' => 60,
            'headers' => array(
                'Authorization' => 'Token ' . $this->token
            ),
            'body' => $fields
        ));



        self::checkRequestError($request);

        $statusCode = wp_remote_retrieve_response_code($request);

        if ($statusCode != 201) {
            return array(
                'status' => $statusCode,
                'message' => json_decode(self::_('body', $request), true)
            );
        }

        self::addOption('wpl_addon_idx_requested_provider', $fields);

        return array(
            'status' => $statusCode,
            'message' => 'Provider requested succesfully'
        );
    }

    protected function shouldBeDeleted($propertyStatus)
    {

        if (!is_bool($this->configStatus) || false === $this->configStatus) {
            return false;
        }


        // If user config is true lets check property Status
        $activeValues = array(
            'active', 'a'
        );


        return ( in_array(strtolower($propertyStatus), $activeValues) ) ? false : true;
    }

    protected function purgeListing($pid, $fromAPI = true)
    {

        if (!is_string($pid) || !is_numeric($pid)) {
            $msg = 'Wrong Param type. Listing ID should be string';
            return ($fromAPI) ?
                    wp_send_json_error(array(
                        'message' => $msg
                            ), 400) :
                array(
                  'status' => false,
                  'message' => $msg
                );
        }

        $purged = wpl_property::purge($pid);

        if ( $fromAPI ) {
            ($purged) ?
                        wp_send_json_success(array(
                            'message' => 'Property deleted succesfully'
                        )) :
                        wp_send_json_error(array(
                            'message' => 'Cannot delete property, contact administrator'
                                ), 500);
            
        }else {
            return ($purged) ?
                        array(
                            'status' => true,
                            'type' => 'delete',
                            'message' => 'Property deleted succesfully'
                        ) :
                        array(
                            'status' => false,
                            'message' => 'Cannot delete property, contact administrator'
                                );
        }
    }

    public function update($request, $fromAPI = true)
    {
        
        if ($fromAPI) {
            $this->checkRequestType($request);
            $this->checkAccess(self::_('token', $request->get_params()));
            
          $jsonData = $request->get_json_params();
        } else {
            $jsonData = $request;
        }

        $propertyStatus = self::_('status', $jsonData);
        
        $listingId = self::_('listing_id', $jsonData);
        
        $pid = wpl_property::pid($listingId);
        
        if (is_null($pid)) {
                $message = 'Property with given listing ID does not exists'; 
                return ($fromAPI) ?
                wp_send_json_error(array(
                    'message' => $message
                 ), 404)
                : array(
                    'status' => false,
                    'message' => $message
                );
            }
        
            
        if ($this->shouldBeDeleted($propertyStatus)) {
           return $this->purgeListing($pid,$fromAPI);
        }
        
        // Update 

        $locations = $this->getLocationKeysForUpdate($jsonData);
        $mappedResult = $this->map($jsonData);

        $selectTypes = array(
            'Property Status' => self::_('status', $jsonData, '')
        );


        $mappedSelects = $this->mapSelectFromDbst($selectTypes);

        if (!empty($mappedSelects)) {
            foreach ($mappedSelects as $field => $fieldValue) {
                $mappedResult[$field] = $fieldValue;
            }
        }

        global $wpdb;

        $wpdb->update($wpdb->prefix . 'wpl_properties', $mappedResult, array(
            'id' => $pid
        ));

        $this->setLocations($locations, $pid, $wpdb);

        if ($fromAPI) {
            wp_send_json_success(array(
                'message' => 'Property updated'
            ));
        }else {
            return array(
                'status' => true,
                'type' => 'update',
                'message' => 'Property updated'
            );
        }
    }

    public function updateViaJsonFile($request)
    {
        if (!$request instanceof WP_REST_Request) {
            wp_send_json_error(array(
                'message' => 'Bad Request'
                    ), 400);
        }
        
        $this->checkAccess(self::_('token', $request->get_params()));
        
        
        
        // Check File
        $file = $request->get_file_params();

        
        if (empty($file)) {
            wp_send_json_error(array(
                'message' => 'You should provide Json file to update'
                    ), 400);
        }


        $fileInfo = self::_('jsonfile', $file);

        if (empty($fileInfo) || !is_array($fileInfo)) {
            wp_send_json_error(array(
                'message' => 'json file not found'
                    ), 400);
        }

        if ($fileInfo['type'] !== 'application/json' || $fileInfo['error'] != 0) {
            wp_send_json_error(array(
                'message' => 'update file must be json'
                    ), 400);
        }

        $actualFile = $file['jsonfile']['tmp_name'];
        
        if (!file_exists( $actualFile )) {
           wp_send_json_error(array(
               'message' => 'Error during file upload, please contact administrator'
           ),400);   
        }
        
        $fileJsonData = json_decode(file_get_contents($actualFile), true);
        
        if (is_null($fileJsonData) || !is_array($fileJsonData)) {
            wp_send_json_error(array(
                'message' => 'Cannot parse json file for update'
            ), 400);
        }

        
        $listOfUpdateTypes = array('update' => 0,'delete' => 0);
        
        foreach ($fileJsonData as $propertyInfo) {
            $update = $this->update($propertyInfo, false);
            
            if ( $update['status'] === false ) {
                continue;
            }
            
            $updateType = self::_('type',$update,null);
            
            if ( in_array( $updateType, $listOfUpdateTypes ) ) {
                $listOfUpdateTypes[$updateType]++;
            }
            
        }
        
       @unlink($actualFile);
       wp_send_json_success(array(
           'message' => $listOfUpdateTypes['update'].' properties updated and '.$listOfUpdateTypes['delete'].' deleted'
       )); 
    }

    private function checkAccess($token)
    {
        if ($this->stepsDone < 4) {
            wp_send_json_error(array(
                'message' => 'Not all steps are done.'
                    ), 400);
        }

        if ($token !== $this->token) {
            wp_send_json_error(array(
                'message' => 'Wrong Token!'
                    ), 401);
        }
    }

}
