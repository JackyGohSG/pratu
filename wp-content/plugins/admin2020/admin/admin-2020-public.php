<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Public_Actions {

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function run() {

    $this->load_admin_login();
    $this->load_admin_bar_front();

  }



  public function load_admin_login(){

    $admin_2020_front = new Admin_2020_Login($this->version);
    $admin_2020_front->load();

  }

  public function load_admin_bar_front(){


    $utils = new Admin2020_Util();
    $loadfront = $utils->get_option('admin2020_loadfrontend_field_2');


    if ($loadfront) {
      $admin_2020_front = new Admin_2020_Front($this->version);
      $admin_2020_front->load();
    }

  }


}
