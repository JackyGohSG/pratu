<?php

function myhome_php_error() {
	echo '<div style="background: red; padding: 24px; font-size: 36px; line-height: 1.5; margin: 10px 20px 20px 0; position: relative; color: #fff;">';
	echo 'Your PHP version is ' . PHP_VERSION . '. MyHome requires server PHP version 5.6. or higher. ';
	echo '<a style="color:#fff" target="_blank" href="https://myhometheme.zendesk.com/hc/en-us/articles/360001522353-Server-requirements-PHP-version">Click here to read more about this problem and how to fix it</a>.';
	echo '</div>';
}

if ( version_compare( PHP_VERSION, '5.6.0', '<' ) ) {
	add_action( 'admin_notices', 'myhome_php_error' );

	return;
}

define( 'DISABLE_ULTIMATE_GOOGLE_MAP_API', true );

if ( ! isset( $content_width ) ) {
	$content_width = 1920;
}

require_once get_template_directory() . '/includes/class-myhome.php';

function My_Home_Theme() {
	return My_Home::get_instance();
}

/* added this code to add a new option to user role editor plugin admin option under additional options */
function ure_add_block_admin_notices_option($items) {
    $item = URE_Role_Additional_Options::create_item('block_admin_notices', esc_html__('Hide admin notices', 'user-role-editor'), 'admin_init', 'ure_block_admin_notices');
    $items[$item->id] = $item;
     
    return $items;
}
function ure_block_admin_notices() {
    add_action('admin_print_scripts', 'ure_remove_admin_notices');    
}
function ure_remove_admin_notices() {
    global $wp_filter;
    if (is_user_admin()) {
        if (isset($wp_filter['user_admin_notices'])) {
            unset($wp_filter['user_admin_notices']);
        }
    } elseif (isset($wp_filter['admin_notices'])) {
        unset($wp_filter['admin_notices']);
    }
    if (isset($wp_filter['all_admin_notices'])) {
        unset($wp_filter['all_admin_notices']);
    }
}
 
add_filter('ure_role_additional_options', 'ure_add_block_admin_notices_option', 10, 1);

// initiate MyHome theme
My_Home_Theme()->init();
