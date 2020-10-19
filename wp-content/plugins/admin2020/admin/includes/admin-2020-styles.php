<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Styles {

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function load(){

    add_action('admin_init', array( $this, 'register_actions' ),0);

  }



  public function register_actions(){

    $utils = new Admin2020_Util();
    if($utils->deactivate_admin_on_page()){
      return;
    }

    add_action('admin_enqueue_scripts', array( $this, 'add_styles' ),0);
    add_action('admin_enqueue_scripts', array( $this, 'add_themes' ),99);

    add_action('admin_init', array( $this, 'remove_styles' ),9999);
    add_action('login_enqueue_scripts', array( $this, 'add_login_styles' ),10);
    add_action('admin_head', array( $this, 'change_primary_link_color' ),0);
    add_action('admin_head', array( $this, 'admin_2020_user_custom_styles' ),0);

    add_filter( 'admin2020_register_theme', array($this,'register_themes'));

    //WORKAROUND TO STOP MAIL POET DISABLING CUSTOM STYLES
    add_filter('mailpoet_conflict_resolver_whitelist_style',array($this,'admin2020_mailPoet_workaround'),-999);
    add_filter('mailpoet_conflict_resolver_whitelist_script',array($this,'admin2020_mailPoet_workaround_scripts'),-999);

  }

  public function register_themes($themes){

    $path = plugin_dir_url(__DIR__) . 'assets/css/themes/admin2020-midnight-blue.min.css';
    $midnight = array('name'=>'Midnight Blue','path'=>$path,'id'=>'admin2020_midnight_blue');

    $path = plugin_dir_url(__DIR__) . 'assets/css/themes/admin2020-purple-rain.min.css';
    $earthgrey = array('name'=>'Purple Rain','path'=>$path,'id'=>'admin2020_purple_rain');

    array_push($themes,$midnight,$earthgrey);

    return $themes;

  }

  public function add_themes(){

    $themes = array();
		$available_themes = apply_filters( 'admin2020_register_theme', $themes );

    $utils = new Admin2020_Util();
    $selected_theme = $utils->get_option('admin2020_appearance_theme');

    if ($selected_theme != ""){

  		foreach ($available_themes as $theme){

  			$id = $theme['id'];
        $path = $theme['path'];

        if($id == $selected_theme){

          wp_register_style($id, $path, array('uikitcss'), $this->version);
          wp_enqueue_style($id);

        }

  		}

    }

  }


  public function get_meta_boxes( $screen = null, $context = 'advanced' ) {


      global $wp_meta_boxes;

      if ( empty( $screen ) )
          $screen = get_current_screen();
      elseif ( is_string( $screen ) )
          $screen = convert_to_screen( $screen );

      $page = $screen->id;

      return get_hidden_meta_boxes( "dashboard" );
  }




  public function admin_2020_user_custom_styles(){



    $utils = new Admin2020_Util();
    if($utils->check_for_disarm()){
      return;
    }

    $userid = get_current_user_id();
    $current = get_user_meta($userid, 'darkmode', true);

    global $darkmode;
    $darkmode = $current;

    if ($current === 'true') {
        echo '<style type="text/css">';
        echo 'html{background:#111111}';
        echo '</style>';
    }

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

    $disable_notices = $utils->get_option('admin2020_disable_admin_notices');

    if ($disable_notices){
      echo '<style type="text/css">';
      echo '.update-nag, .updated, .error, .is-dismissible { display: none !important; }';
      echo '</style>';
		}



  }


  /// FIX FOR MAIL POET

  public function admin2020_mailPoet_workaround($whitelistedStyles) {

      $requiredcss = array("ma-admin-color.css","ma-admin-common.css","ma-admin-head.css","ma-admin-menu.css","ma-admin-mobile.css","uikit.min.css","mailpoet.min.css");

      foreach($requiredcss as $stylesheet){
        $whitelistedStyles[] = $stylesheet;
      }

	    return $whitelistedStyles;
  }

  public function admin2020_mailPoet_workaround_scripts($whitelistedScripts) {

      $requiredscripts = array("uikit-icons.min.js","uikit.min.js","ma.admin.min.js");

      foreach($requiredscripts as $script){
        $whitelistedScripts[] = $script;
      }

	    return $whitelistedScripts;
  }



  public function change_primary_link_color(){


    $utils = new Admin2020_Util();
    $color = $utils->get_option('admin2020_overiew_primary_color');

    if ($color == ""){
      $color = false;
    }

    if ($color != false){
      $darkercolor = $this->color_luminance($color,-0.3);
      $much_darkercolor = $this->color_luminance($color,.8);
      echo '<style type="text/css"> .uk-link, .uk-text-primary, .uk-nav-default>li.uk-active>a {  color:'.$color.'  !important; border-color:'.$color.'  !important; }
            #adminmenuwrap .uk-nav-default>li:not(.ma-admin-shrink-wrap):hover>a {border-color:'.$color.'  !important;}
            input[type=checkbox]:checked { background-color:'.$color.'  !important; }
            .category-tabs li a.current, .filter-links li a.current, .nav-tab-wrapper a.current, .subsubsub li a.current { background-color:'.$color.'  !important; }
            .uk-tab>.uk-active>a { border-color:'.$color.'  !important; }
            .uk-button-primary, .button-primary, .page-title-action { background-color:'.$color.'  !important; border-color: '.$color.';}
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




  public function add_styles(){

    $utils = new Admin2020_Util();
    if($utils->check_for_disarm()){
      return;
    }


    global $pagenow;
		$required_styles = array('editor','head','menu','mobile','color');
    $this->keepstyles = array();

		///LOAD REQUIRED SCRIPTS
    //LOAD UIKIT
    wp_register_style('uikitcss', 'https://cdn.jsdelivr.net/npm/uikit@3.4.2/dist/css/uikit.min.css', false);
    wp_enqueue_style('uikitcss');
    wp_enqueue_script('uikit', 'https://cdn.jsdelivr.net/npm/uikit@3.4.2/dist/js/uikit.min.js', array());
    wp_enqueue_script('uikiticons', 'https://cdn.jsdelivr.net/npm/uikit@3.4.2/dist/js/uikit-icons.min.js', array());
    ////LOAD CHART.JS

    if(is_rtl()){
      wp_register_style('uikitcss_rtl', plugin_dir_url(__DIR__) . 'assets/css/uikit-rtl.min.css', array(), $this->version);
      wp_enqueue_style('uikitcss_rtl');
      wp_register_style('admin2020_rtl', plugin_dir_url(__DIR__) . 'assets/css/admin2020-rtl.min.css', array(), $this->version);
      wp_enqueue_style('admin2020_rtl');
    }


    if (isset($_GET['page'])) {
        if($_GET['page']=='admin_2020_dashboard'){
          wp_enqueue_script('admin-chart-js', 'https://cdn.jsdelivr.net/npm/chart.js@2.9.3/dist/Chart.min.js', array('jquery'), $this->version);
          wp_enqueue_script('ma-admin-dashboard', plugin_dir_url(__DIR__) . 'assets/js/ma-admin-dashboard.min.js', array('jquery'), $this->version);
          wp_localize_script('ma-admin-dashboard', 'ma_admin_dash_ajax', array('ajax_url' => admin_url('admin-ajax.php'), 'security' => wp_create_nonce('ma-admin-dash-security-nonce')));


          wp_enqueue_script('ma-admin-moment', 'https://cdn.jsdelivr.net/momentjs/latest/moment.min.js', array());
          wp_enqueue_script('ma-admin-daterangepicker', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.min.js', array('jquery'));
          wp_register_style('ma-admin-daterange-css', 'https://cdn.jsdelivr.net/npm/daterangepicker/daterangepicker.css', false);
          wp_enqueue_style('ma-admin-daterange-css');
      }
      if($_GET['page']=='admin_2020_media'){

          global $wp_query;

          wp_enqueue_script('ma-admin-media', plugin_dir_url(__DIR__) . 'assets/js/ma-admin-media.min.js', array('jquery'), $this->version);
          wp_localize_script('ma-admin-media', 'ma_admin_media_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('ma-admin-media-security-nonce'),
        		'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
            'page_now' => 'admin_2020_media',
          ));

          wp_enqueue_script('ma-admin-folders', plugin_dir_url(__DIR__) . 'assets/js/ma-admin-folders.min.js', array('jquery'), $this->version);
          wp_localize_script('ma-admin-folders', 'ma_admin_folder_ajax', array(
            'ajax_url' => admin_url('admin-ajax.php'),
            'security' => wp_create_nonce('ma-admin-folder-security-nonce'),
            'page_now' => 'admin_2020_media',
          ));

          wp_register_style('custom_wp_media_css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-media.min.css', array(), $this->version);
          wp_enqueue_style('custom_wp_media_css');

          ///LOAD TOAST UI IMAGE EDITOR
          wp_register_style('tui-image-editor-css', 'https://uicdn.toast.com/tui-image-editor/latest/tui-image-editor.css', array(), $this->version);
          wp_enqueue_style('tui-image-editor-css');
          wp_register_style('tui-image-editor-color-picker-css', 'https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.css', array(), $this->version);
          wp_enqueue_style('tui-image-editor-color-picker-css');

          wp_enqueue_script('tui-image-editor-fabric-js', plugin_dir_url(__DIR__) . 'assets/image-editor/fabric.min.js', array('jquery'), $this->version);
          wp_enqueue_script('tui-image-editor-code-snippet-js', 'https://uicdn.toast.com/tui.code-snippet/latest/tui-code-snippet.min.js', array('jquery'));
          wp_enqueue_script('tui-image-editor-color-picker-js', 'https://uicdn.toast.com/tui-color-picker/latest/tui-color-picker.js', array('jquery'));
          wp_enqueue_script('tui-image-editor-file-saver-js', 'https://cdn.jsdelivr.net/g/filesaver.js', array('jquery'));
          wp_enqueue_script('tui-image-editor-js', 'https://uicdn.toast.com/tui-image-editor/latest/tui-image-editor.js', array('jquery'));
          wp_enqueue_script('tui-image-editor-theme-js', plugin_dir_url(__DIR__) . 'assets/image-editor/black-theme.min.js', array('jquery'), $this->version);


      }

      if($_GET['page']=='admin_2020_content'){

        wp_enqueue_script('ma-admin-media', plugin_dir_url(__DIR__) . 'assets/js/ma-admin-media.min.js', array('jquery'), $this->version);
        wp_localize_script('ma-admin-media', 'ma_admin_media_ajax', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'security' => wp_create_nonce('ma-admin-media-security-nonce'),
          'current_page' => get_query_var( 'paged' ) ? get_query_var('paged') : 1,
          'page_now' => 'admin_2020_content',
        ));

        wp_enqueue_script('ma-admin-folders', plugin_dir_url(__DIR__) . 'assets/js/ma-admin-folders.min.js', array('jquery'), $this->version);
        wp_localize_script('ma-admin-folders', 'ma_admin_folder_ajax', array(
          'ajax_url' => admin_url('admin-ajax.php'),
          'security' => wp_create_nonce('ma-admin-folder-security-nonce'),
          'page_now' => 'admin_2020_content',
        ));

        wp_register_style('custom_wp_media_css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-media.min.css', array(), $this->version);
        wp_enqueue_style('custom_wp_media_css');
        wp_register_style('custom_wp_content_css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-content.min.css', array(), $this->version);
        wp_enqueue_style('custom_wp_content_css');

      }

      if($_GET['page']=='admin_2020'){

          wp_enqueue_media();

          wp_enqueue_style( 'wp-color-picker' );
          wp_enqueue_script( 'wp-color-picker');

          wp_enqueue_script('codemirror_js', plugin_dir_url(__DIR__) . 'assets/codemirror/lib/codemirror.js', array());
          wp_enqueue_script('codemirror_mode_js', plugin_dir_url(__DIR__) . 'assets/codemirror/mode/javascript/javascript.js', array());
          wp_enqueue_script('codemirror_mode_css', plugin_dir_url(__DIR__) . 'assets/codemirror/mode/javascript/css.js', array());
          wp_register_style('codemirror_css', plugin_dir_url(__DIR__) . 'assets/codemirror/lib/codemirror.css', array(), $this->version);
          wp_enqueue_style('codemirror_css');

          $userid = get_current_user_id();
          $current = get_user_meta($userid, 'darkmode', true);

          if ($current === 'true'){
            wp_register_style('codemirror_theme', plugin_dir_url(__DIR__) . 'assets/codemirror/theme/material-ocean.css', array(), $this->version);
          } else {
            wp_register_style('codemirror_theme', plugin_dir_url(__DIR__) . 'assets/codemirror/theme/xq-light.css', array(), $this->version);
          }
          wp_enqueue_style('codemirror_theme');
      }
    }
    //LOAD MEDIA HELPER
    wp_register_script('media-uploader', plugin_dir_url(__DIR__) . 'assets/js/media-uploader.min.js', array('jquery'), $this->version);
    wp_enqueue_script('media-uploader');
		///LOAD GOOGLE FONT
		wp_register_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Sen:wght@400;700&display=swap', array());
    wp_enqueue_style('custom-google-fonts');
		//LOAD ADMIN 2020 JS
    wp_enqueue_script('ma-admin', plugin_dir_url(__DIR__) . 'assets/js/ma-admin.min.js', array('jquery'), $this->version);
    wp_localize_script('ma-admin', 'ma_admin_ajax', array('ajax_url' => admin_url('admin-ajax.php'), 'security' => wp_create_nonce('ma-admin-security-nonce')));

		// LOAD ADMIN 2020 STYLES
		foreach ($required_styles as $style){
			wp_register_style('ma_admin_'.$style.'_css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-'.$style.'.min.css', array('uikitcss'), $this->version);
	    wp_enqueue_style('ma_admin_'.$style.'_css');
		}



		/// CHECK FOR COMPATIBILITY MODE
    $utils = new Admin2020_Util();
    $disablestyles = $utils->get_option('admin2020_disablestyles_field_2');

    if ($disablestyles == ""){
      $disablestyles = false;
    }

		// CONDITIONALLY LOAD EXTRA STYLES
    if ($disablestyles === '1' && $pagenow === "admin.php" && $_GET['page'] != "admin_2020_dashboard" && $_GET['page'] != "admin_2020_media" && $_GET['page'] != "admin_2020" && $_GET['page'] != "admin_2020_content")  {
        wp_register_style('custom_wp_admin_css_compatability', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-compatibility.min.css', array(), $this->version);
        wp_enqueue_style('custom_wp_admin_css_compatability');
    } else if ($pagenow != "customize.php") {
        wp_register_style('custom_wp_admin_css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-common.min.css', array(), $this->version);
        wp_enqueue_style('custom_wp_admin_css');
    }

    ///CHECK FOR MULTISITE AND ADD ULTIMO
    if(is_multisite()){
      $filelocation = plugin_dir_url(__DIR__) . 'assets/css/extra-plugin/ma-admin-wp-ultimo.min.css';
      wp_register_style('ma_admin_wp_ultimo_css', $filelocation, array(), $this->version);
      wp_enqueue_style('ma_admin_wp_ultimo_css');
    }

		/// CHECK COMPATIBILITY STYLE SHEETS AGAINST INNSTALLED PLUGINS
    $activeplugins = get_option('active_plugins');
    foreach ($activeplugins as $plugin) {
        $string = explode('/', $plugin);
        $pluginname = $string[0];
        $filelocation = plugin_dir_url(__DIR__) . 'assets/css/extra-plugin/ma-admin-' . $pluginname . '.min.css';
        $filepath = plugin_dir_path(__DIR__) . 'assets/css/extra-plugin/ma-admin-' . $pluginname . '.min.css';
        if (file_exists($filepath)) {
            wp_register_style('ma_admin_' . $pluginname . '_css', $filelocation, array(), $this->version);
            wp_enqueue_style('ma_admin_' . $pluginname . '_css');
        }
    }

    /// CHECK COMPATIBILITY STYLE SHEETS AGAINST INNSTALLED THEMES
    $allthemes = wp_get_themes();
    foreach ($allthemes as $theme) {
        $name = strtolower($theme);
        $name = explode(" ", $name);
        $name = $name[0];
        $filelocation = plugin_dir_url(__DIR__) . 'assets/css/extra-plugin/ma-admin-' . $name . '.min.css';
        $filepath = plugin_dir_path(__DIR__) . 'assets/css/extra-plugin/ma-admin-' . $name . '.min.css';
        if (file_exists($filepath)) {
            wp_register_style('ma_admin_' . $name . '_css', $filelocation, array(), $this->version);
            wp_enqueue_style('ma_admin_' . $name . '_css');
        }
    }
    wp_register_style('ma_admin_mobile_css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-mobile.min.css', array('custom_wp_admin_css'), $this->version);
    wp_enqueue_style('ma_admin_mobile_css');



  }

  public function remove_styles(){

    $utils = new Admin2020_Util();
    if($utils->check_for_disarm()){
      return;
    }

    global $pagenow;

    // CHECK FOR COMPATIBILITY MODE
    $utils = new Admin2020_Util();
    $disablestyles = $utils->get_option('admin2020_disablestyles_field_2');

    if ($disablestyles == ""){
      $disablestyles = false;
    }

		/// ARRAY OF STYLES TO DISABLE
    $adminstyles = array("wp-admin", "buttons", "thickbox","media-views");
    $compatability = array("thickbox", "admin-menu");

		/// STYLES TO LOAD
    $extrastyles = array("common", "forms");
    $customizestyles = array("buttons", "customize-controls", "customize-widgets", "media-views", "customize-nav-menus", "wp-color-picker", "code-editor");

		//CONDITIONALLY DISABLE STYLES
    if ($disablestyles === '1' && $pagenow === "admin.php" && $_GET['page'] != "admin_2020_dashboard" && $_GET['page'] != "admin_2020_media" && $_GET['page'] != "admin_2020" && $_GET['page'] != "admin_2020_content") {
        foreach ($compatability as $style) {
            wp_deregister_style($style);
        }
        foreach ($extrastyles as $style) {
            wp_register_style($style, admin_url() . '/css/' . $style . '.css', false, $this->version);
            wp_enqueue_style($style);
        }
    } else if ($pagenow != "customize.php") {
        foreach ($adminstyles as $style) {
          $disable = true;
            ///FIX FOR THRIVE SITE ORIGIN EDITOR // REQUIRES BUTTONS STYLE FOR SOME REASON
            if ($style == "buttons"){
              $plugins = get_plugins();
              foreach ($plugins as $plugin){
                if ("Page Builder by SiteOrigin" == $plugin['Name']){
                  if (isset($_GET["action"])){
                    if($_GET["action"] == "edit"){
                      $disable = false;
                    }
                  }
                }
              }
            }
            wp_deregister_style($style);

            if(!$disable){
              $filelocation = plugin_dir_url(__DIR__) . 'assets/css/extra-plugin/ma-admin-' . 'button-replacer' . '.min.css';
              wp_register_style('buttons', $filelocation, false, $this->version);
              wp_enqueue_style('buttons');
            }
        }
    }

    ///FIX FOR THRIVE ARCHITECT MEDIA VIEWS

    if (isset($_GET["action"])){
      if($_GET["action"] == "architect"){
        wp_register_style('admin2020-media-views', home_url() . '/wp-includes/css/media-views.min.css', false, $this->version);
        wp_enqueue_style('admin2020-media-views');
      }
    }






  }


  public function add_login_styles() {

      wp_register_style('uikitcss', plugin_dir_url(__DIR__) . 'assets/css/uikit.min.css', false, '1.0.0');
      wp_enqueue_style('uikitcss');
      wp_enqueue_script('uikit', plugin_dir_url(__DIR__) . 'assets/js/uikit.min.js', array(), '1.0');
      wp_enqueue_script('uikiticons', plugin_dir_url(__DIR__) . 'assets/js/uikit-icons.min.js', array(), '1.0');
      wp_enqueue_style('custom-google-fonts', 'https://fonts.googleapis.com/css2?family=Sen:wght@400;700&display=swap', false);
      wp_register_style('ma-admin-login-css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-common.min.css', false, '1.0.0');
      wp_enqueue_style('ma-admin-login-css');
      wp_deregister_style('login');

  }


  public function get_version() {
    return $this->version;
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
