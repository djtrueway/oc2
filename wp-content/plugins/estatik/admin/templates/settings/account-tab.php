<?php do_action( 'es_settings_before_account_tab' );

echo Es_Html_Helper::render_settings_field( __( 'Enable Buyers', 'es-plugin' ), 'es_settings[buyers_enabled]', 'checkbox', array(
	'checked' => (bool) $es_settings->buyers_enabled ? 'checked' : false,
	'value' => 1,
	'class' => 'es-switch-input',
) );

echo Es_Html_Helper::render_settings_field( __( 'Registration page', 'es-plugin' ), 'es_settings[registration_page_id]', 'list', array(
	'value' => $es_settings->registration_page_id,
	'values' => $list_pages,
) );

echo Es_Html_Helper::render_settings_field( __( 'Login page', 'es-plugin' ), 'es_settings[login_page_id]', 'list', array(
	'value' => $es_settings->login_page_id,
	'values' => $list_pages,
) );

echo Es_Html_Helper::render_settings_field( __( 'Reset password page', 'es-plugin' ), 'es_settings[reset_password_page_id]', 'list', array(
	'value' => $es_settings->reset_password_page_id,
	'values' => $list_pages,
) );

do_action( 'es_settings_after_account_tab' );
