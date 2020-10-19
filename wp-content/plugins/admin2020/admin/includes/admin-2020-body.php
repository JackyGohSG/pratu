<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Body{

  public function run(){

    add_filter('admin_body_class', array($this, 'ma_admin_body_class'));
    add_filter( 'wp_die_handler', function( $handler ) {
    	return  is_admin() ? array($this,'admin_error_pages') : $handler;
    }, 999 );

  }

  //OUTPUT BODY CLASSES
  public function ma_admin_body_class($classes) {

      $userid = get_current_user_id();
      $current = get_user_meta($userid, 'darkmode', true);
      $switch = get_user_meta($userid, 'ma-admin-switch', true);

      $utils = new Admin2020_Util();
      $adminbar = $utils->get_option('admin2020_show_quick_links');



      global $darkmode;
      $darkmode = $current;
      $bodyclass = '';

      if ($current === 'true') {
          $bodyclass = " uk-light ma-admin-dark ";
      }
      if ($switch === 'true') {
          $bodyclass = $bodyclass . " ma-admin-menu-shrink ";
      }
      if (!$adminbar){
          $bodyclass = $bodyclass . " ma-admin-show-admin-bar ";
      }
      return $bodyclass;
  }

  public function admin_error_pages($message, $title = '', $args = array()){

    $defaults = array( 'response' => 500 );
  	$r = wp_parse_args($args, $defaults);

  	if ( function_exists( 'is_wp_error' ) && is_wp_error( $message ) ) {
  		$errors = $message->get_error_messages();
  		switch ( count( $errors ) ) {
  			case 0 :
  				$message = '';
  				break;
  			case 1 :
  				$message = $errors[0];
  				break;
  			default :
  				$message = "<ul>\n\t\t<li>" . join( "</li>\n\t\t<li>", $errors ) . "</li>\n\t</ul>";
  				break;
  		}

  	} else {
  		$message = strip_tags( $message );
  	}

    require_once plugin_dir_path( dirname( __FILE__ ) ) . '/includes/admin-2020-error.php';

  	die();
  }

}
