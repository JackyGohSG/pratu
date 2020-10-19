<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Content{

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function run(){

    add_action( 'admin_menu', array($this,'add_menu_item') );


  }





  public function add_menu_item() {

    $utils = new Admin2020_Util();
    if($utils->check_for_disarm()){
      return;
    }

    $slug ='admin_2020_content';
    $media_object = new Admin_2020_Media($this->version);
    add_menu_page( 'Content', __('Content','admin2020'), 'read', $slug, array($media_object,'admin_media_render'),'dashicons-admin2020-content',4 );

    return;

  }










  /////END OF CLASS

}
