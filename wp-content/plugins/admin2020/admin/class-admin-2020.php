<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020{

  public function __construct() {

    $this->version = '1.3';
		$this->plugin_name = 'admin-2020';
    $this->productid = "5ebc6198701f5d0a0812c618";

  }



  public function run() {


    if(!$this->version_check()) return;


    $this->load_dependicies();

    $this->load_required_files();

    if ( is_admin() ) {
      $this->load_admin_dependicies();
      $this->load_private_actions();
    } else{
      $this->load_public_dependicies();
      $this->load_public_actions();
    }

    $this->deactivate_color_scheme();
    $this->enable_languages();


	}



  private function load_dependicies() {

    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-bar.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin-2020-i18n.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin-2020-register-components.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-util.php';

  }

  private function load_admin_dependicies() {

    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin-2020-admin.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-menu.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-bar.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-styles.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-tables.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-ajax.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-settings.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-network-settings.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-body.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-dashboard.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-media.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-content.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-woocommerce.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-folders.php';

  }



  private function load_public_dependicies() {

    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/admin-2020-public.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-front.php';
    require_once plugin_dir_path( dirname( __FILE__ ) ) . 'admin/includes/admin-2020-login.php';

  }

  private function load_private_actions() {

    $this->actions = new Admin_2020_Private_Actions($this->get_version());
  	$this->actions->run();

  }

  private function load_public_actions() {

    $this->actions = new Admin_2020_Public_Actions($this->get_version());
  	$this->actions->run();

  }

  public function get_version() {
		return $this->version;
	}

  public function version_check() {

    $minimumPhpVersion = 5.4;
    $minimumWPVersion = 5;
    $currentwpversion = get_bloginfo( 'version' );
    $phpVersion = phpversion();

    if ($phpVersion < $minimumPhpVersion){

      add_action('admin_notices', function(){
        echo '<div class="notice notice-warning is-dismissible"><p>';
        _e('Admin 2020 requires PHP 5.4 or higher, please update your PHP version.');
        echo  '</p></div>';
      });

      return false;

    } else

    if ($currentwpversion < $minimumWPVersion){

      add_action('admin_notices', function(){
        echo '<div class="notice notice-warning is-dismissible"><p>';
        _e('Admin 2020 requires Wordpress 5 or higher, please update your Wordpress version.');
        echo  '</p></div>';
      });

      return false;

    }
    return  true;

  }

  public function load_required_files(){
    $options = get_option( 'admin2020_settings' );
    $settingsurl = get_admin_url().'admin.php?page=admin_2020';
    if (is_multisite()){
      $settingsurl = network_admin_url().'admin.php?page=admin_2020';
    }

		if (isset($options['admin2020_pluginPage_licence_key'])){
			$value = $options['admin2020_pluginPage_licence_key'];
      if ($value == "" || $value == null){
        $admin2020_components = new Admin_2020_Register_Components($this->version, $this->productid);
        $admin2020_components->load();
      }
		} else if (!is_network_admin() && is_multisite()){

      $options = get_blog_option(get_main_network_id(), 'admin2020_network_settings');
      //echo $options;

      if (isset($options['admin2020_pluginPage_licence_key_network'])){
  			$value = $options['admin2020_pluginPage_licence_key_network'];
        if ($value == "" || $value == null){
        }
  		}
		}
  }

  public function deactivate_color_scheme(){

    remove_action('admin_color_scheme_picker', 'admin_color_scheme_picker');

  }

  public function enable_languages(){

    $this->languages = new Admin_2020_i18n();
  	$this->languages->load_plugin_textdomain();

  }

  public function new_notice($message){

    $this->message = $message;
    add_action('admin_notices', function(){
      echo '<div class="notice notice-warning" style="display: block !important;visibility: visible !important;z-index:9999999 !important;opacity: 1 !important;"><p>';
      echo "$this->message";
      echo  '</p></div>';
    });

  }






}
