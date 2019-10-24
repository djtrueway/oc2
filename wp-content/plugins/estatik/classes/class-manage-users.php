<?php

/**
 * Class Es_Manage_Agents
 */
class Es_Manage_Users extends Es_Object
{
	/**
	 * @inheritdoc
	 */
	public function actions()
	{
		add_action( 'init', array( $this, 'restore_password' ) );
		add_action( 'init', array( $this, 'check_active_state' ) );
		add_action( 'register_new_user', array( $this, 'send_new_user_notifications'), 9 );
		add_action( 'edit_user_created_user', array( $this, 'send_new_user_notifications' ), 1, 2 );
		add_action( 'init', array( $this, 'register' ) );
		add_action( 'admin_notices', array( $this, 'admin_notices' ) );
		add_action( 'wp_logout', array( $this, 'logout_message' ) );
		add_action( 'init', array( $this, 'save_profile' ) );

		$login = ! empty( $_REQUEST[ 'es-login' ] ) ? $_REQUEST[ 'es-login' ] : '';
		$login = sanitize_key( $login );

		if ( empty( $login ) ) {
			add_action( 'wp_login', array( $this, 'user_login' ), 1, 2 );
		}

		parent::actions();
	}

	public function save_profile() {

	    $nonce = 'es_save_profile';

	    if ( isset( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], $nonce ) ) {

		    // Include helper wordpress functions for saving agent.
		    if ( ! function_exists( 'edit_user' ) ) {
			    include ( ABSPATH . 'wp-admin/includes/user.php' );
		    }

		    // Set confirmed password variable.
		    $_POST['nickname'] = sanitize_text_field( $_POST['user_login'] );

		    // Get user ID.
		    $id = get_current_user_id();

		    do_action( 'es_before_edit_user', $id );

		    // Save user.
		    $id = edit_user( $id );

		    $msg = new Es_Messenger( 'es_profile' );

		    if ( ! empty( $id ) && ! $id instanceof WP_Error ) {
			    $agent = es_get_user_entity( $id );
			    $post_data = filter_input( INPUT_POST, 'es_profile', FILTER_DEFAULT, FILTER_REQUIRE_ARRAY );
			    $fields = $agent::get_fields();

			    if ( ! empty( $_FILES['agent_photo'] ) ) {
				    if ( ! function_exists( 'wp_handle_upload' ) ) {
					    require_once( ABSPATH . 'wp-admin/includes/image.php' );
					    require_once( ABSPATH . 'wp-admin/includes/file.php' );
					    require_once( ABSPATH . 'wp-admin/includes/media.php' );
				    }

				    if ( $attachment_id = media_handle_sideload( $_FILES['agent_photo'], -1 ) ) {
					    if ( ! $attachment_id instanceof WP_Error ) {
						    $post_data['profile_attachment_id'] = $attachment_id;
					    }
				    }
			    }

			    foreach ( $fields as $key => $field ) {
				    $value = isset( $post_data[$key] ) ? $post_data[$key] : $agent->{$key};
				    $agent->save_field_value( $key, $value );
			    }

			    if ( empty( $post_data['profile_attachment_id'] ) ) {
				    $agent->delete_field_value( 'profile_attachment_id' );
			    }

			    $msg->set_message( __( 'User successfully saved', 'es-plugin' ), 'success' );

			    do_action( 'es_after_save_user', $id );

			    $url = add_query_arg( 'id', $id, $_SERVER['HTTP_REFERER'] );
			    wp_redirect( apply_filters( 'es_redirect_after_save_user', $url, $id ) );

			    exit;

		    } else {
			    if  ( ! empty( $id->errors ) ) {

				    foreach ( $id->errors as $error_list ) {
					    if ( ! $error_list ) continue;

					    foreach ( $error_list as $error ) {
						    $msg->set_message( $error, 'error' );
					    }
				    }
			    } else {
				    $msg->set_message( __( 'User doesn\'t save', 'es-plugin' ), 'error' );
			    }
		    }
        }
    }

	/**
	 * Logout unactive user.
	 *
	 * @return void
	 */
	public function check_active_state()
	{
		if ( is_user_logged_in() ) {
			$user = es_get_user_entity();

			if ( $user && in_array( $user::get_role_name(), $user->get_entity()->roles ) && ! $user->status ) {
				wp_logout();
			}
		}
	}

	/**
	 * After login check to see if user account is disabled.
	 *
	 * @param string $user_login
	 * @param object $user
	 */
	public function user_login( $user_login, $user = null ) {

		if ( ! $user ) {
			$user = get_user_by( 'login', $user_login );
		}

		if ( ! $user ) {
			// not logged in - definitely not disabled
			return;
		}

		$user_entity = es_get_user_entity( $user->ID );

		// Is the use logging in disabled?
		if ( $user_entity && in_array( $user_entity::get_role_name(), $user->roles ) ) {

		    if ( empty( $user_entity->status ) || $user_entity->status == Es_User::STATUS_DISABLED ) {
			    wp_clear_auth_cookie();

			    // Build login URL and then redirect
			    $login_url = site_url( 'wp-login.php', 'login' );
			    $login_url = add_query_arg( 'disabled', '1', $login_url );
			    wp_redirect( $login_url );
			    exit;
            }
		}
	}

	/**
	 * Show a notice to users who try to login and are disabled
	 *
	 * @since 1.0.0
	 * @param string $message
	 * @return string
	 */
	public function user_login_message( $message ) {

		// Show the error message if it seems to be a disabled user
        $disabled = sanitize_key( filter_input( INPUT_GET, 'disabled' ) );

		if ( $disabled == 1 )
			$message =  '<div id="login_error">' . __( 'Account disabled.', 'es-plugin' ) . '</div>';

		return $message;
	}

	/**
	 * @inheritdoc
	 */
	public function filters()
	{
		add_filter( 'retrieve_password_message', array( $this, 'retrieve_password_message' ), 10, 4 );
		add_filter( 'login_message', array( $this, 'user_login_message' ) );
		parent::filters();
	}

	/**
	 * Customize WP standard reset password message.
	 *
	 * @param $message
	 * @param $key
	 * @param $user_login
	 * @param $user_data
	 * @return string
	 */
	public function retrieve_password_message( $message, $key, $user_login, $user_data )
	{
		global $es_settings;
		$pid = $es_settings->reset_password_page_id;

		$user_id = $user_data instanceof WP_User ? $user_data->ID : $user_data;

		if ( es_get_user_entity( $user_id ) && ! empty( $pid ) && get_post( $pid ) ) {
			global $es_settings;

			$message = __( 'Someone has requested a password reset for the following account:', 'es-plugin' ) . "<br>";
			$message .= network_home_url( '/' ) . "<br>";
			$message .= sprintf(__( 'Username: %s', 'es-plugin' ), $user_login) . "<br>";
			$message .= __( 'If this was a mistake, just ignore this email and nothing will happen.', 'es-plugin' ) . "<br><br>";
			$message .= __( 'To reset your password, visit the following address:', 'es-plugin' ) . "<br>";
			$message .= add_query_arg( array( '_key' => $key, '_login' => rawurlencode($user_login) ),
					get_permalink( $es_settings->reset_password_page_id ) );
		}

		return $message;
	}

	/**
	 * Add admin notices.
	 *
	 * @return void
	 */
	public function admin_notices()
	{
		global $es_settings;
		$notice_type = 'notice-warning';

		// If registration is enabled and registration page doesn't exist.
		if ( get_option( 'users_can_register' ) && empty( $es_settings->registration_page_id ) && ! get_option( 'es-register-shortcode' ) ) : ?>
			<div data-notice="es-register-shortcode" class="es-notice notice <?php echo $notice_type; ?> is-dismissible">
				<p>
					<b><?php _e( 'Estatik', 'es-plugin' ); ?></b>:
					<?php echo sprintf(
						__( 'Please, create a registration page with <b>[es_register]</b> shortcode and set it on %s page.', 'es-plugin' ),
						'<a href="' . es_admin_settings_uri() . '" target="_blank">' . __( 'Estatik General settings' ) . '</a>'
					); ?>
				</p>
			</div>
		<?php endif;

		// If login page doesn't exist.
		if ( empty( $es_settings->login_page_id ) && ! get_option( 'es-login-shortcode' ) ) : ?>
			<div data-notice="es-login-shortcode" class="es-notice notice <?php echo $notice_type; ?> is-dismissible">
				<p>
					<b><?php _e( 'Estatik', 'es-plugin' ); ?></b>:
					<?php echo sprintf(
						__( 'Please, create a login page with <b>[es_login]</b> shortcode and set it on %s page.', 'es-plugin' ),
						'<a href="' . es_admin_settings_uri() . '" target="_blank">' . __( 'Estatik General settings' ) . '</a>'
					); ?>
				</p>
			</div>
		<?php endif;

		// If reset pwd page doesn't exist.
		if ( empty( $es_settings->reset_password_page_id ) && ! get_option( 'es-reset-password-shortcode' ) ) : ?>
			<div data-notice="es-reset-password-shortcode" class="es-notice notice <?php echo $notice_type; ?> is-dismissible">
				<p>
					<b><?php _e( 'Estatik', 'es-plugin' ); ?></b>:
					<?php echo sprintf(
						__( 'Please, create a password reset page with <b>[es_reset_pwd]</b> shortcode and set it on %s page.', 'es-plugin' ),
						'<a href="' . es_admin_settings_uri() . '" target="_blank">' . __( 'Estatik General settings' ) . '</a>'
					); ?>
				</p>
			</div>
		<?php endif;
	}

	/**
	 * Register new agent action.
	 *
	 * @return void
	 */
	public function register()
	{
		$nonce = 'es_user_registration';

		if ( ! empty( $_POST[ $nonce ] ) && wp_verify_nonce( $_POST[ $nonce ], $nonce ) && ! empty( $_POST[ 'es_user' ] ) ) {

			// Messages after registration.
			$logger = new Es_Messenger( 'es_user_register' );

			if ( es_verify_recaptcha() ) {

			    $valid_roles = apply_filters( 'es_valid_user_roles', array( 'es_buyer' ) );
			    $role = ! empty( $_POST['es_user']['role'] ) ? $_POST['es_user']['role'] : 'es_buyer';

			    if ( in_array( $role, $valid_roles ) ) {

				    $user_info = $_POST[ 'es_user' ];

				    foreach ( $user_info as $key => $field ) {
				        $user_info[ $key ] = sanitize_text_field( $field );
                    }

				    // Get agent class.
				    $user = es_get_user_entity( null, $role );
				    // Register new agent.
				    $registered = $user->register( $user_info );

				    if ( ! is_wp_error( $registered ) ) {
					    $user = es_get_user_entity( $registered->getID() );
					    $user->change_status( $user::STATUS_ACTIVE );

					    $logger->set_message( __( 'You are registered at our website. <br>Your username and password were sent to your email address.', 'es-plugin' ), 'success' );
				    } else if ( is_wp_error( $registered ) ) {
					    /** @var $registered WP_Error */
					    $logger->set_message( $registered->get_error_message(), 'error' );
				    } else {
					    $logger->set_message( __( 'You are not registered to our website. <br>Please, contact a support.', 'es-plugin' ), 'error' );
				    }
                } else {
				    $logger->set_message( __( 'You are not registered to our website. <br>Invalid user role.', 'es-plugin' ), 'error' );
                }
			} else {
				$logger->set_message( __( 'Invalid recaptcha.', 'es-plugin' ), 'error' );
            }

			wp_redirect( esc_url( $_SERVER['HTTP_REFERER'] ) ); die;
		}
	}

	/**
	 * Set success message on user logout.
	 *
	 * @return void
	 */
	public function logout_message() {
		$messenger = new Es_Messenger( 'login' );
		$messenger->clean_container();
		$messenger->set_message( __( 'You are logged out.', 'es-plugin' ), 'success' );
	}

	/**
	 * Handles sending password retrieval email to user.
	 *
	 * METHOD COPIED FROM wp-login.php BECAUSE WHEN FILE IS INCLUDED - HE SENDS HEADERS.
	 *
	 * @return bool|WP_Error True: when finish. WP_Error on error
	 */
	function retrieve_password() {
		$errors = new WP_Error();

		if ( empty( $_POST['user_login'] ) ) {
			$errors->add('empty_username', __('<strong>ERROR</strong>: Enter a username or email address.'));
		} elseif ( strpos( $_POST['user_login'], '@' ) ) {
			$user_data = get_user_by( 'email', sanitize_email( $_POST['user_login'] ) );
			if ( empty( $user_data ) )
				$errors->add('invalid_email', __('<strong>ERROR</strong>: There is no user registered with that email address.'));
		} else {
			$login = sanitize_text_field( $_POST['user_login'] );
			$user_data = get_user_by( 'login', $login );
		}

		/**
		 * Fires before errors are returned from a password reset request.
		 *
		 * @since 2.1.0
		 * @since 4.4.0 Added the `$errors` parameter.
		 *
		 * @param WP_Error $errors A WP_Error object containing any errors generated
		 *                         by using invalid credentials.
		 */
		do_action( 'lostpassword_post', $errors );

		if ( $errors->get_error_code() )
			return $errors;

		if ( empty( $user_data ) ) {
			$errors->add('invalidcombo', __('<strong>ERROR</strong>: Invalid username or email.'));
			return $errors;
		}

		// Redefining user_login ensures we return the right case in the email.
		$user_login = $user_data->user_login;
		$user_email = $user_data->user_email;
		$key = get_password_reset_key( $user_data );

		if ( is_wp_error( $key ) ) {
			return $key;
		}

		$message = __('Someone has requested a password reset for the following account:') . "<br>";
		$message .= network_home_url( '/' ) . "<br>";
		$message .= sprintf(__('Username: %s'), $user_login) . "<br>";
		$message .= __('If this was a mistake, just ignore this email and nothing will happen.') . "<br>";
		$message .= __('To reset your password, visit the following address:') . "<br>";
		$message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user_login), 'login');

		if ( is_multisite() ) {
			$blogname = get_network()->site_name;
		} else {
			/*
			 * The blogname option is escaped with esc_html on the way into the database
			 * in sanitize_option we want to reverse this for the plain text arena of emails.
			 */
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);
		}

		/* translators: Password reset email subject. 1: Site name */
		$title = sprintf( __('[%s] Password Reset'), $blogname );

		/**
		 * Filters the subject of the password reset email.
		 *
		 * @since 2.8.0
		 * @since 4.4.0 Added the `$user_login` and `$user_data` parameters.
		 *
		 * @param string  $title      Default email title.
		 * @param string  $user_login The username for the user.
		 * @param WP_User $user_data  WP_User object.
		 */
		$title = apply_filters( 'retrieve_password_title', $title, $user_login, $user_data );

		/**
		 * Filters the message body of the password reset mail.
		 *
		 * @since 2.8.0
		 * @since 4.1.0 Added `$user_login` and `$user_data` parameters.
		 *
		 * @param string  $message    Default mail message.
		 * @param string  $key        The activation key.
		 * @param string  $user_login The username for the user.
		 * @param WP_User $user_data  WP_User object.
		 */
		$message = apply_filters( 'retrieve_password_message', $message, $key, $user_login, $user_data );

        $message = es_email_content( 'emails/password-request.php', array( 'message' => $message ) );

		if ( $message && !wp_mail( $user_email, wp_specialchars_decode( $title ), $message ) )
			wp_die( __('The email could not be sent.') . "<br />\n" . __('Possible reason: your host may have disabled the mail() function.') );

		return true;
	}

	/**
	 * Reset password action.
	 *
	 * @return void
	 */
	public function restore_password()
	{
		$nonce_name = 'es-restore-pwd';
		$nonce = sanitize_key( filter_input( INPUT_POST, $nonce_name ) );

		if ( $nonce && wp_verify_nonce( $nonce, $nonce_name ) ) {
			$messenger = new Es_Messenger( 'login' );

			$action = sanitize_key( filter_input( INPUT_POST, 'action' ) );
			$key = sanitize_text_field( filter_input( INPUT_POST, '_key' ) );
			$login = sanitize_text_field( filter_input( INPUT_POST, '_login' ) );

			if ( $action && $key && $login && $action == 'fill_password' ) {
				$user = check_password_reset_key( $key, $login );
				if ( $user instanceof WP_Error ) {
					$messenger->set_message( $user->get_error_message(), 'error' );
				} else {
					reset_password( $user, sanitize_text_field( $_POST['pwd'] ) );
					$messenger->set_message( __( 'Password successfully changed.', 'es-plugin' ), 'success' );
				}
			} else {

				add_filter( 'wp_die_handler', function() use ( $messenger ) {
					$messenger->set_message( __( 'Email didn\'t sent. PHP Mail is doesn\'t work.' ) , 'error' );
					wp_redirect( esc_url( $_POST['redirect'] ) ); die;
				} );

				$result = $this->retrieve_password();

				if ( $result instanceof WP_Error ) {
					$messenger->set_message( $result->get_error_message() , 'error' );
				} else {
					$messenger->set_message( __( 'New password emailed to you.', 'es-plugin' ) , 'success' );
				}
			}

			wp_redirect( esc_url( $_POST['redirect'] ) ); die;
		}
	}

	/**
	 * @param null $user_id
	 * @param string $notify
	 */
	public function send_new_user_notifications( $user_id = null, $notify = 'both' ) {

	    $user_entity = es_get_user_entity( $user_id );

		if ( $user_entity ) {

			remove_action( 'register_new_user', 'wp_send_new_user_notifications' );

			global $wpdb, $wp_hasher;
			$user = get_userdata( $user_id );

			// The blogname option is escaped with esc_html on the way into the database in sanitize_option
			// we want to reverse this for the plain text arena of emails.
			$blogname = wp_specialchars_decode(get_option('blogname'), ENT_QUOTES);

			if ( 'user' !== $notify ) {
				$switched_locale = switch_to_locale( get_locale() );
				$message  = sprintf( __( 'New user registration on your site %s:' ), $blogname ) . "<br>";
				$message .= sprintf( __( 'Username: %s' ), $user->user_login ) . "<br>";
				$message .= sprintf( __( 'Email: %s' ), $user->user_email ) . "<br>";

				$message = es_email_content( 'emails/user-registered.php', array( 'message' => $message, 'title' => __( 'New user registered', 'es-plugin' ) ) );

				$message = apply_filters( 'es_user_registered_email_message', $message, $user, $notify );

				@wp_mail( get_option( 'admin_email' ), sprintf( __( '[%s] New User Registration' ), $blogname ), $message );

				if ( $switched_locale ) {
					restore_previous_locale();
				}
			}

			// `$deprecated was pre-4.3 `$plaintext_pass`. An empty `$plaintext_pass` didn't sent a user notification.
			if ( 'admin' === $notify || ( empty( $deprecated ) && empty( $notify ) ) ) {
				return;
			}

			// Generate something random for a password reset key.
			$key = wp_generate_password( 20, false );

			/** This action is documented in wp-login.php */
			do_action( 'retrieve_password_key', $user->user_login, $key );

			// Now insert the key, hashed, into the DB.
			if ( empty( $wp_hasher ) ) {
				$wp_hasher = new PasswordHash( 8, true );
			}
			$hashed = time() . ':' . $wp_hasher->HashPassword( $key );
			$wpdb->update( $wpdb->users, array( 'user_activation_key' => $hashed ), array( 'user_login' => $user->user_login ) );

			$switched_locale = switch_to_locale( get_user_locale( $user ) );

			$message = sprintf(__('<b>Username</b>: %s'), $user->user_login) . "<br>";
			$message .= __('To set your password, visit the following address:') . "<br>";

			global $es_settings;

			if ( $es_settings->reset_password_page_id && get_post( $es_settings->reset_password_page_id ) ) {
				$message .= add_query_arg( array( '_key' => $key, '_login' => rawurlencode($user->user_login) ), get_permalink( $es_settings->reset_password_page_id ) );
			} else {
				$message .= network_site_url("wp-login.php?action=rp&key=$key&login=" . rawurlencode($user->user_login), 'login');
			}

			$message = apply_filters( 'es_user_registered_email_message', $message, $user, $notify );

			$message = es_email_content( 'emails/user-registered.php', array( 'message' => $message, 'title' => __( 'New user registered', 'es-plugin' ) ) );

			wp_mail($user->user_email, sprintf(__('[%s] Your username and password info'), $blogname), $message);

			if ( $switched_locale ) {
				restore_previous_locale();
			}
		}
	}
}
