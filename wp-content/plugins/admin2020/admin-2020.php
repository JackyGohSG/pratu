<?php
/*
Plugin Name: Admin 2020
Plugin URI: https://admintwentytwenty.com
Description: A clean and modern wordpress dashboard theme with a streamlined dashboard, dark mode, extended search and support for other plugins.
Version: 1.3
Author: ADMIN 2020
Text Domain: admin2020
Domain Path: /languages
Author URI: https://admintwentytwenty.com
*/

// If this file is called directly, abort.
if ( ! defined( 'ABSPATH' ) ) exit;

///Main Plugin Class
require plugin_dir_path( __FILE__ ) . 'admin/class-admin-2020.php';

function run_admin_2020() {

	$plugin = new Admin_2020();
	$plugin->run();

}
run_admin_2020();


/// SHOW ERRORS
//ini_set('display_errors', 1);
//ini_set('display_startup_errors', 1);
//error_reporting(E_ALL);
