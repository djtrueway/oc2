<?php

defined('_WPLEXEC') or die('Restricted access');

_wpl_import('libraries.idx.addon_idxn');

class wpl_addon_idx_controller extends wpl_controller
{
    public function display()
    {
        // first check php version
        if ( version_compare(PHP_VERSION, '5.5', '<') ) {

            $return = array(
                'message'   => 'PHP >= 5.5 is required.',
                'status'    =>  500
            );

            wp_send_json($return);
        }
      
        wpl_global::min_access('guest');
        
        if (!is_user_logged_in()) {
           wp_send_json(array(
             'status' => 500,
             'message' => 'Log in please'
           ));
        }
        
        // init function name
        $function = wpl_request::getVar('wpl_function');

        
        // Execute it
       if ( $function == 'registration' ) {
          self::$function();
       }else {

         self::$function(new addon_idxn);
       }
    }

    // The user registration API call can be used to create user accounts in the application.
    // Additionally, the “email”,”password”,”name”,”re_password” fields are required.
    protected static function registration()
    {
        
        $current_user = wp_get_current_user();
     
        if($current_user->user_email)
        {

            $formparams = array(
                'name'        => wpl_request::getVar('name'),
                'second_email'=> wpl_request::getVar('second_email'),
                'email'       => $current_user->user_email, // system email
                'phone_number'       => wpl_request::getVar('phone')
            );

            wp_send_json(addon_idxn::register( $formparams ));
         }

        $return = array(
            'message'   => 'email address could not be found',
            'status'    => 404
        );

        wp_send_json($return);

    }

    // Using this method we will receive information about different providers, such as provider name,provider short description,
    // listing name and property type,note that you are required to send a valid Header in your request, in which the key should be Authorization and
    // the value should be the token that you recieved
    protected static function providers( addon_idxn $idxClient)
    {

        $current_user = wp_get_current_user();

        if(!$current_user->user_email)
        {
             $return = array(
               'message'   => 'email address could not be found',
                'status'    => 404
             );

            wp_send_json($return);
        }

         
         wp_send_json($idxClient->getProviders());
       
    }

    //Using this method you will recieve selected information about the provider or providers, chosen by the IDX client,
    // such as provider name, short description of the provider, listing name and property type.
    protected static function save( addon_idxn $idxClient )
    {

        $current_user = wp_get_current_user();
        
        if(!$current_user->user_email)
        {


        $return = array(
            'message'   => 'email address could not be found',
            'status'    => 404
        );

        wp_send_json($return);

      }

            $formparams = array(
                'mls_id'        => wpl_request::getVar('mls_id'),
                'name'          => wpl_request::getVar('name'),
                'provider'      => wpl_request::getVar('provider')
            );

           
        
           wp_send_json($idxClient->save( $formparams ));
    }

    // Using this method you will recieve information about price list according to your chosen MLS provider(s).
    protected static function price(addon_idxn $idxClient)
    {

          $current_user = wp_get_current_user();

        if(!$current_user->user_email)
        {
            $return = array(
            'message'   => 'email address could not be found',
            'status'    => 404
        );

         wp_send_json($return);
        }

       wp_send_json($idxClient->getChosenProvider());
    }

    

    // Using this method we will save client current configuration on cache server
    protected static function configuration(addon_idxn $idxClient)
    {

        $current_user = wp_get_current_user();

        if($current_user->user_email)
        {
            $formparams = array(
                //'mls_id'         => wpl_request::getVar('mls_id'), // Provider id
           
                
                'agent_id'       => wpl_request::getVar('agent_id'), // Agent id
                'office_id'      => wpl_request::getVar('office_id'), // Office id
                'agent_name'     => wpl_request::getVar('agent_name'),
                'office_name'    => wpl_request::getVar('office_name'),
                'property_status'  => (wpl_request::getVar('all_listing') == 1) ? 1 : wpl_request::getVar('import_status'),
                'agent_listing'    => wpl_request::getVar('agent_listing'),
                'office_listing'    => wpl_request::getVar('office_listing'),
                'service_url'    => get_site_url(), // full site URL for web service
            );
            
            wp_send_json($idxClient->configure( $formparams ));
        }

        $return = array(
            'message'   => 'email address could not be found',
            'status'    => 404
        );

        wp_send_json($return);
    }

    protected static function get_step($last_step = 4)
    {



        $current_user = wp_get_current_user();
          
        if (!$current_user->user_email ) {
               $return = array(
                    'message'   => 'email address could not be found ',
                    'status'    => 404
              );

              wp_send_json($return);
          }

          $steps_done = get_option('wpl_addon_idx_user_steps_done');
          
          if (!$steps_done) {
              $steps_done = 0;
          }         

         if ( $steps_done == 4 ) {
           wp_send_json(array(
              'message' => 'Finished'
           ));
         }


         wp_send_json(array(
            'step_value' => $steps_done + 1
         ));
        
    }


    // calculate data and time and status
    protected static function status( addon_idxn $idxClient)
    {

        $current_user = wp_get_current_user();

        if(!$current_user->user_email)
        {
          $return = array(
                'message'   => 'email address could not be found',
                'status'    => 404
            );

            wp_send_json($return);
        }
        
        wp_send_json($idxClient->getStatus());
  
    }

    protected static function get_keys(addon_idxn $idxClient)
    {
        $current_user = wp_get_current_user();
     
        if($current_user->user_email)
        {

            wp_send_json($idxClient->getPaymentCreds());
        }

        $return = array(
            'message'   => 'email address could not be found',
            'status'    => 404
        );

        wp_send_json($return);
    }

    protected function check_payment(addon_idxn $idxClient)
    {
        $current_user = wp_get_current_user();

        if(!$current_user->user_email)
        {
             $return = array(
               'message'   => 'email address could not be found',
                'status'    => 404
             );

            wp_send_json($return);
        }

         
         wp_send_json($idxClient->checkPayment());
    }

      
    

    protected static function back_step( addon_idxn $idxClient)
    {

        

        $step = trim(wpl_request::getVar('step_name'));

      

        $current_user = wp_get_current_user();

        if(!$current_user->user_email)
        {
            
            $return = array(
                'message'   => 'email address could not be found',
                'status'    => 404
            );

            wp_send_json($return);
        }

        wp_send_json($idxClient->backStep( $step ));

    }

    public static function is_user_registered(addon_idxn $idxClient)
    {
        $current_user = wp_get_current_user();
          
        if (!$current_user->user_email ) {
               $return = array(
                    'message'   => 'email address could not be found ',
                    'status'    => 404
              );

              wp_send_json($return);
          }
        
        ( is_array($idxClient->getIdxUserCredentials()) ) ? 
           wp_send_json(array(
              'status' => 200,
              'message' => 'User already Registered'
           ))
           :
              wp_send_json(array(
              'status' => 401,
              'message' => 'IDX user not yet registered'
           ));
    }

    public static function load_trial_data(addon_idxn $idxClient)
    {
        $current_user = wp_get_current_user();
          
        if (!$current_user->user_email ) {
               $return = array(
                    'message'   => 'email address could not be found ',
                    'status'    => 404
              );

              wp_send_json($return);
          }

          wp_send_json($idxClient->importTrialListings());
    }

  public static function protect_idx_trial(addon_idxn $idxClient)
  {
      $current_user = wp_get_current_user();
          
        if (!$current_user->user_email ) {
               $return = array(
                    'message'   => 'email address could not be found ',
                    'status'    => 404
              );

              wp_send_json($return);
       }
      
      $isTrialListingsImported = get_option('wpl_idx_addon_trial_imported');

      if ( $isTrialListingsImported ) {
          wp_send_json( array(
            'status' => 200,
            'message' => "Trial listings are already imported"
          ));
      }

      wp_send_json($idxClient->checkPayment());
 }

 public static function reset_trial  (addon_idxn $idxClient ) 
 {
    $current_user = wp_get_current_user();
          
        if (!$current_user->user_email ) {
               $return = array(
                    'message'   => 'email address could not be found ',
                    'status'    => 404
              );

              wp_send_json($return);
       }


       wp_send_json($idxClient->resetTrialListings());
 }   

 public static function save_client_request (addon_idxn $idxClient) 
 {
    $current_user = wp_get_current_user();
          
        if (!$current_user->user_email ) {
               $return = array(
                    'message'   => 'email address could not be found ',
                    'status'    => 404
              );

              wp_send_json($return);
       }

           $formparams = array(
                'provider'        => wpl_request::getVar('provider'),
                'state'=> wpl_request::getVar('state')
            );

           wp_send_json($idxClient->requestProvider( $formparams ));
 }


}