<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Login{

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function load(){

    $utils = new Admin2020_Util();
    $homepage = $utils->get_option('admin2020_overiew_homepage');
    $disableoverview = $utils->get_option('admin2020_disable_overview');
    $disablelogin = $utils->get_option('admin2020_disable_login_styles');

		if ($disablelogin){
			return;
		}

    add_action('login_init', array($this,'deregister_default'));
    add_action('login_head', array($this,'ma_admin_login_style'),0);
    add_action('login_head', array($this,'ma_admin_login_logo'));



    if ($homepage){

      if ($disableoverview){
          //OVERVIEW IS DISABLED
      } else {
        add_filter( 'login_redirect', array($this,'redirectToOverview') );
      }
    }

  }

  public function redirectToOverview(){
		return home_url() . "/wp-admin/admin.php?page=admin_2020_dashboard";
	}

  public function deregister_default(){
    wp_deregister_style('login');
    wp_deregister_style('buttons');
    wp_deregister_style('wp-admin');
  }
  ///LOAD STYLES TO LOGIN PAGE
  public function ma_admin_login_style() {

    wp_register_style('uikitcss', 'https://cdn.jsdelivr.net/npm/uikit@3.4.2/dist/css/uikit.min.css', false, $this->version);
    wp_enqueue_style('uikitcss');
    wp_enqueue_script('uikit', 'https://cdn.jsdelivr.net/npm/uikit@3.4.2/dist/js/uikit.min.js', array(), $this->version);
    wp_enqueue_script('uikiticons', 'https://cdn.jsdelivr.net/npm/uikit@3.4.2/dist/js/uikit-icons.min.js', array(), $this->version);
    wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Sen:wght@400;700&display=swap', false);

    wp_register_style('ma-admin-login-css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-common.min.css', false, '1.0.0');
    wp_enqueue_style('ma-admin-login-css');

    wp_register_style('ma-admin-color-css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-color.min.css', false, '1.0.0');
    wp_enqueue_style('ma-admin-color-css');

    add_filter( 'login_headerurl', array($this,'admin2020_custom_login_logo') );

    $utils = new Admin2020_Util();
    $custom_css = $utils->get_option('admin2020_custom_css');
    $custom_js = $utils->get_option('admin2020_custom_js');

		if ($custom_css != ""){
      echo '<style type="text/css">';
      echo $custom_css;
      echo '</style>';
		}

		if ($custom_js != ""){
      echo '<script>';
      echo $custom_js;
      echo '</script>';
		}

  }



  public function admin2020_custom_login_logo($url) {

       return get_site_url();

  }

  ///LOAD LOGO TO LOGIN PAGE
  public function ma_admin_login_logo() {

      $utils = new Admin2020_Util();
      $logo = $utils->get_option('admin2020_image_field_0');
      $backgroundimage = $utils->get_option('admin2020_login_background');
      $color = $utils->get_option('admin2020_overiew_primary_color');

      if ($logo == ""){
        $logo = esc_url(plugins_url('/assets/img/LOGO-BLUE.png', __DIR__));
      }

      if ($backgroundimage != ""){
        echo '<style type="text/css"> body {  background-image:url(' . $backgroundimage . ')  !important; } </style>';
      }

      echo '<style type="text/css"> h1 a {  background-image:url(' . $logo . ')  !important; } </style>';


      if ($color != false){
        $darkercolor = $utils->color_luminance($color,-0.3);
        $much_darkercolor = $utils->color_luminance($color,.8);
        echo '<style type="text/css"> .uk-link, .uk-text-primary, .uk-nav-default>li.uk-active>a {  color:'.$color.'  !important; border-color:'.$color.'  !important; }
              #adminmenuwrap .uk-nav-default>li:not(.ma-admin-shrink-wrap):hover>a {border-color:'.$color.'  !important;}
              input[type=checkbox]:checked { background-color:'.$color.'  !important; }
              .category-tabs li a.current, .filter-links li a.current, .nav-tab-wrapper a.current, .subsubsub li a.current { background-color:'.$color.'  !important; }
              .uk-tab>.uk-active>a { border-color:'.$color.'  !important; }
              .uk-button-primary, .button-primary, .page-title-action { background-color:'.$color.'  !important; border-color: '.$color.' !important;}
              .uk-button-primary:hover, .button-primary:hover, .page-title-action:hover { background-color:'.$darkercolor.'  !important; border-color: '.$darkercolor.';}
              .uk-badge:not(.admin2020notificationBadge):not(.admin2020folderCount):not(.admin2020totalCount){ background-color:'.$color.'  !important; }

              .admin2020loaderwrap .admin2020loader{background:'.$much_darkercolor.'  !important;}
              .admin2020loader::after {background:'.$color.'  !important;}
              a {  color:'.$color.'; }
              a:hover {  color:'.$darkercolor.'; }
              .row-title::after, .uk-link::after {background:'.$darkercolor.'  !important;}
              .ma-admin-dark .uk-button-primary, .ma-admin-dark .uk-badge{
                  color: #fff !important;
                }
                .uk-offcanvas-bar .uk-button-primary, .uk-offcanvas-bar .uk-badge{
                    color: #fff !important;
                  }
        </style>';
      }

  }

  public function color_luminance( $hex, $percent ) {

    	// validate hex string

    	$hex = preg_replace( '/[^0-9a-f]/i', '', $hex );
    	$new_hex = '#';

    	if ( strlen( $hex ) < 6 ) {
    		$hex = $hex[0] + $hex[0] + $hex[1] + $hex[1] + $hex[2] + $hex[2];
    	}

    	// convert to decimal and change luminosity
    	for ($i = 0; $i < 3; $i++) {
    		$dec = hexdec( substr( $hex, $i*2, 2 ) );
    		$dec = min( max( 0, $dec + $dec * $percent ), 255 );
    		$new_hex .= str_pad( dechex( $dec ) , 2, 0, STR_PAD_LEFT );
    	}

    	return $new_hex;
    }


}
