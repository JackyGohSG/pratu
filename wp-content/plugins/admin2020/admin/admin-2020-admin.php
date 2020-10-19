<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Private_Actions {

  private $version;

  public function __construct( $theversion = null ) {

		$this->version = $theversion;

	}

  public function run() {

    ///CHECK FOR STYLE TOGGLE
    if (isset($_GET["admin2020"])){
      if($_GET["admin2020"] == "false"){
        return;
      }
    }
    //WORKAROUND FOR UX BUILDER
    if (isset($_GET["app"])){
      if($_GET["app"] == "uxbuilder"){
        return;
      }
    }
    //WORKAROUND FOR MONSTER INSIGHTS AND WOOCOMMERCE
    if (isset($_GET["page"])){
      if($_GET["page"] == "monsterinsights-onboarding" || $_GET["page"] == "wc-setup"){
        return;
      }
    }

    $this->load_styles();
    $this->load_admin_menu();
    $this->load_admin_bar();
    $this->edit_wp_tables();
    $this->register_ajax_functions();
    $this->add_plugin_network_options();
    $this->add_plugin_options();
    $this->add_body_classes();
    $this->build_dashboard();
    $this->build_media();
    $this->build_content();
    $this->build_folders();

    if ( in_array( 'woocommerce/woocommerce.php', apply_filters( 'active_plugins', get_option( 'active_plugins' ) ) ) ) {
        $this->build_woocommerce();
    }

  }






  public function load_styles(){

    $admin_2020_styles = new Admin_2020_Styles($this->version);
    $admin_2020_styles->load();

  }

  public function load_admin_menu(){

    $admin_2020_menu = new Admin_2020_Menu();
    $admin_2020_menu->build();

  }

  public function load_admin_bar(){

    $admin_2020_Admin_Bar = new Admin_2020_Admin_Bar();
    $admin_2020_Admin_Bar->build();

  }

  public function edit_wp_tables(){

    $admin_2020_styles = new Admin_2020_Table_Actions();
    $admin_2020_styles->run();

  }

  public function register_ajax_functions(){

    $admin_2020_ajax = new Admin_2020_Ajax();
    $admin_2020_ajax->register();

  }

  public function add_plugin_options(){

    $admin_2020_plugin_options = new Admin_2020_Network_Plugin_Options();
    $admin_2020_plugin_options->run();

  }

  public function add_plugin_network_options(){

    $admin_2020_plugin_network_options = new Admin_2020_Plugin_Options($this->version);
    $admin_2020_plugin_network_options->run();

  }

  public function add_body_classes(){

    $admin_2020_body = new Admin_2020_Body();
    $admin_2020_body->run();

  }

  public function build_dashboard(){

    $utils = new Admin2020_Util();
    $disableoverview = $utils->get_option('admin2020_disable_overview');

		if ($disableoverview){
			//OVERVIEW DISABLED
		} else {
      $admin_2020_dashbboard = new Admin_2020_Dashboard($this->version);
      $admin_2020_dashbboard->run();
		}

  }

  public function build_media(){

    $utils = new Admin2020_Util();
    $disablemedia= $utils->get_option('admin2020_overiew_media_gallery');

		if ($disablemedia){
      //MEDIA DISABLED
    } else {
      $admin_2020_media = new Admin_2020_Media($this->version);
      $admin_2020_media->run();
    }

  }

  public function build_content(){

    $utils = new Admin2020_Util();
    $disabled_content = $utils->get_option('admin2020_content_show_content');
    if ($disabled_content){
      //CONTENT IS DISABLED
    } else {
      $admin_2020_content = new Admin_2020_Content($this->version);
      $admin_2020_content->run();
    }

  }

  public function build_woocommerce(){

    $admin_2020_content = new Admin2020_woocommerce($this->version);
    $admin_2020_content->build();

  }

  public function build_folders(){

    $addmin2020_folders = new Admin_2020_Folders($this->version);
    $addmin2020_folders->run();

  }


}
