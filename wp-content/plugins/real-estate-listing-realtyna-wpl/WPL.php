<?php
/**
	Plugin Name: WPL
	Plugin URI: https://realtyna.com/
	Description: WPL is a professional WordPress real estate plugin created by Realtyna.
	Author: Realtyna
	Project Manager: howard@realtyna.com
	Version: 4.6.0
    Text Domain: real-estate-listing-realtyna-wpl
    Domain Path: /languages
	Author URI: https://realtyna.com/
**/

// WPL Execution
define('_WPLEXEC', 1);

// Directory Separator
if(!defined('DS')) define('DS', DIRECTORY_SEPARATOR);

// WPL ABS PATH
define('WPL_ABSPATH', dirname(__FILE__) .DS);
define('WPL_BASENAME', basename(WPL_ABSPATH));
define('WPL_UP_ABSPATH', ABSPATH .'wp-content' .DS. 'uploads' .DS. 'WPL' .DS);

/**
 * WPL textdomain for language
 * @deprecated since version 3.0.0
 */
define('WPL_TEXTDOMAIN', 'real-estate-listing-realtyna-wpl');
define('WPL_VERSION', '4.6.0'); // WPL Version

require WPL_ABSPATH.'config.php';

_wpl_import('global');
_wpl_import('libraries.settings');
_wpl_import('libraries.request');
_wpl_import('libraries.file');
_wpl_import('libraries.folder');
_wpl_import('libraries.db');
_wpl_import('libraries.html');
_wpl_import('libraries.sef');
_wpl_import('libraries.property');
_wpl_import('libraries.users');
_wpl_import('libraries.widgets');
_wpl_import('libraries.filters');
_wpl_import('libraries.events');
_wpl_import('controller');
_wpl_import('extensions');

// Initialize the WPL API
_wpl_import('api.init');

// Request Controller
_wpl_import('request_controller');