<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Front{

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function load(){
    add_action('wp_enqueue_scripts', array($this,'ma_admin_load_front'));
    add_action( 'admin_bar_menu', array($this,'admin_load_logo_front'), -999 );
  }

  ///LOAD ADMIN BAR ON FRONT END
  public function ma_admin_load_front() {

    if (!is_admin_bar_showing()){
      return;
    }

    wp_register_style('admin2020_front', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-front.min.css', array(), $this->version);
    wp_enqueue_style('admin2020_front');

  }



  public function admin_load_logo_front( $wp_admin_bar ) {

    $utils = new Admin2020_Util();
    $logo = $utils->get_logo();

    $content =  "<img src='".$logo."' class='admin2020_front_logo'>";
  	$args = array(
  		'id'    => 'admin2020_site_logo',
  		'title' => $content,
  		'href'  => admin_url(),
  	);
  	$wp_admin_bar->add_node( $args );
  }



}
