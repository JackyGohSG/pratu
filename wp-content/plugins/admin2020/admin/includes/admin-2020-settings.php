<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Plugin_Options{

	private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

	/// BUILD ADMIN 2020 SETTINGS PAGE
	public function run(){

		add_filter( 'admin_init', array($this,'custom_restrict_pages_from_admin') );

		add_action( 'admin_menu', array($this,'admin2020_add_admin_menu') );

		add_action( 'admin_init',  array($this,'admin2020_settings_init') );

		add_filter( 'gettext', array($this,'rename_active_theme'), 999, 3 );

	}

	public function rename_active_theme($translated, $original, $domain){

		$my_theme = wp_get_theme();
		$theme_name = $my_theme['Name'];

		$options = get_option( 'admin2020_settings' );

		if (isset($options['admin2020_active_theme_name'])){

			if ($options['admin2020_active_theme_name'] != ""){

				$rename = $options['admin2020_active_theme_name'];

				$strings = array(
		    	$theme_name => $rename,
				);

				if ( isset( $strings[$original] ) && is_admin() ) {
				    $translations = &get_translations_for_domain( $domain );
				    $translated = $translations->translate( $strings[$original] );
				}

				return $translated;

			} else {
				return $original;
			}
		} else {
			return $original;
		}

	}


	public function custom_restrict_pages_from_admin() {
    global $pagenow, $menu, $submenu, $screen;

		$currentpage = $pagenow;


		if(isset($_GET["page"])){
			$page_id = $_GET["page"];
		} else {
			$page_id = "";
		}

		if(isset($_GET["post_type"])){
			$post_type = $_GET["post_type"];
		} else {
			$post_type = "";
		}

		if($pagenow == 'edit.php' || $pagenow == 'post-new.php'){
			if ($post_type != ""){
				$currentpage = $pagenow."?post_type=".$post_type;
			}
		}

		$restrictedpages = array();

		foreach ($menu as $item){

			$user = wp_get_current_user();
			$userroles = $user->roles;

			if(!isset($item[5])){
				continue;
			}

			$title = $item[5];
			$options = get_option( 'admin2020_settings' );
			$parent_hidden = false;

			if ($title) {
				foreach ($userroles as $role){

					$lcrole = strtolower($role);
					$lcrole = str_replace(" ","_",$lcrole);
					$lcparentname = strip_tags(strtolower($title));
					$lcparentname = str_replace(" ","_",$lcparentname);

					$optionname = 'admin2020_menu_'.$lcrole.'_'.$lcparentname;


					if (isset($options[$optionname])){

						if ($options[$optionname] == true){
							array_push($restrictedpages,$item[2]);
							$parent_hidden = true;
						}

					}

				}
			}


			if(isset($submenu[$item[2]])){
				$subitems = $submenu[$item[2]];
			} else {
				$subitems = array();
			}

			//echo '<pre>' . print_r( $subitems, true ) . '</pre>';
			//echo $pagenow;
			//return;

			foreach ($subitems as $sub){

				$hidden = 'false';
				$title = $item[0];
				$sub_menu_name = $sub[0];


				////CHECK FOR HIDDEN MENU ITEMS BY ROLE
				foreach ($userroles as $role){

					$lcrole = strtolower($role);
					$lcrole = str_replace(" ","_",$lcrole);
					$itemname = strip_tags(strtolower($sub[0]));
					$itemname = str_replace(" ","_",$itemname);
					$sub_option_name = 'admin2020_submenu_'.$lcrole.'_'.$lcparentname.$itemname;

					if($parent_hidden == true){

						array_push($restrictedpages,$sub[2]);

					} else {

						if (isset($options[$sub_option_name])){
							if ($options[$sub_option_name] == true){
								array_push($restrictedpages,$sub[2]);
							}
						}

					}

				}///END OF ROLE LOOP

			}////END OF SUBMENU LOOP


		}/// END OF MENU LOOP

		if(!is_super_admin()){
	    if(in_array( $currentpage, $restrictedpages  ) || in_array( $page_id, $restrictedpages)) {
					$message = __("You don't have access to this page","admin2020");
					wp_die($message);
	    }
		}

	}



	public function admin2020_add_admin_menu(  ) {

		$utils = new Admin2020_Util();
    if(!$utils->check_for_user_disarm()){
      return;
    }

		add_options_page( 'Admin 2020', 'Admin 2020', 'manage_options', 'admin_2020', array($this,'admin2020_options_page') );

	}





	public function admin2020_settings_init(  ) {





		///////APPEARANCE SECTION
		register_setting( 'admin2020_appearance_settings', 'admin2020_settings' );

		add_settings_section(
			'admin2020_pluginPage_section',
			"",
			"",
			'admin2020_appearance_settings'
		);

		add_settings_field(
			'admin2020_overiew_appearance_breaker',
			'<h4 class="">'.esc_html__( 'Appearance', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);
		///NORMAL LOGO
	  add_settings_field(
			'admin2020_image_field_0',
			esc_html__( 'Admin Logo', 'admin2020' ),
			array($this,'admin2020_image_field_0_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);
		///DARK LOGO
		add_settings_field(
			'admin2020_image_field_dark',
			esc_html__( 'Optional logo for dark mode', 'admin2020' ),
			array($this,'admin2020_image_field_dark_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_login_background',
			esc_html__( 'Login background Image', 'admin2020' ),
			array($this,'admin2020_login_background_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);



		add_settings_field(
			'admin2020_disablestyles_field_2',
			esc_html__( 'Disabled Admin 2020 styles on plugin pages?', 'admin2020' ),
			array($this,'admin2020_disablestyles_field_2_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_overiew_primary_color',
			esc_html__( 'Change primary link color', 'admin2020' ),
			array($this,'admin2020_overiew_primary_color_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_appearance_theme',
			esc_html__( 'Theme', 'admin2020' ),
			array($this,'admin2020_appearance_theme_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);


		add_settings_field(
			'admin2020_overiew_appearance_white_label',
			'<h4 class="uk-margin-top">'.esc_html__( 'White Label', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_active_theme_name',
			esc_html__( 'Rename Active Theme Name', 'admin2020' ),
			array($this,'admin2020_active_theme_name_render'),
			'admin2020_appearance_settings',
			'admin2020_pluginPage_section'
		);

		///////ADMIN GENERAL SECTION
		register_setting( 'admin2020_general_settings', 'admin2020_settings' );

		add_settings_section(
			'admin2020_pluginPage_section',
			"",
			"",
			'admin2020_general_settings'
		);


		add_settings_field(
			'admin2020_overiew_general_breaker',
			'<h4 class="">'.esc_html__( 'General', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		if (is_network_admin() || !is_multisite()){

			add_settings_field(
				'admin2020_pluginPage_licence_key',
				esc_attr__( 'Product Licence KeySET:', 'admin2020' ),
				array($this,'admin2020_licence_key_render'),
				'admin2020_general_settings',
				'admin2020_pluginPage_section'
			);

		} else {
			register_setting( 'admin2020_pluginPage_section', 'admin2020_pluginPage_licence_key' );
		}

		add_settings_field(
			'admin2020_disable_admin2020_by_user',
			esc_html__( 'Disable Admin 2020 by user role:', 'admin2020' ),
			array($this,'admin2020_disable_admin2020_by_user_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);



		add_settings_field(
			'admin2020_disable_menu_search',
			esc_html__( 'Disable Admin Menu Search?', 'admin2020' ),
			array($this,'admin2020_disable_menu_search_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_overiew_homepage',
			esc_html__( 'Set overview page as Admin homepage?', 'admin2020' ),
			array($this,'admin2020_overiew_homepage_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_overiew_media_gallery',
			esc_html__( 'Use default wordpress media gallery?', 'admin2020' ),
			array($this,'admin2020_overiew_media_gallery_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_disable_quick_actions',
			esc_html__( 'Keep quick actions below title on tables?', 'admin2020' ),
			array($this,'admin2020_disable_quick_actions_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);
		add_settings_field(
			'admin2020_disable_login_styles',
			esc_html__( 'Disable Admin 2020 login styles?', 'admin2020' ),
			array($this,'admin2020_disable_login_styles_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_disable_admin_notices',
			esc_html__( 'Disable Admin Notices?', 'admin2020' ),
			array($this,'admin2020_disable_admin_notices_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);


		///////ADMIN BAR SECTION
		add_settings_field(
			'admin2020_overiew_breaker',
			'<h4 class="uk-margin-top">'.esc_html__( 'Admin Bar', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_overiew_screen_options',
			esc_html__( 'Show screen options?', 'admin2020' ),
			array($this,'admin2020_overiew_screen_options_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_overiew_quick_actions',
			esc_html__( 'Disable Quick Actions in the Admin Bar?', 'admin2020' ),
			array($this,'admin2020_overiew_quick_actions_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
		 		'admin2020_search_included_posts',
		 		esc_html__( 'Post types available in search?', 'admin2020' ),
		 		array($this,'admin2020_search_included_posts_render'),
		 		'admin2020_general_settings',
		 		'admin2020_pluginPage_section'
		 	);

		add_settings_field(
			'admin2020_adminbar_disable_search',
			esc_html__( 'Disable Search in the Admin Bar?', 'admin2020' ),
			array($this,'admin2020_adminbar_disable_search_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_admin2020_loader',
			__( 'Disable loading bar in admin bar?', 'admin2020' ),
			array($this,'admin2020_admin2020_loader_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);
		add_settings_field(
	    'admin2020_loadfrontend_field_2',
	    esc_html__( 'Load Admin Menu style on front end?', 'admin2020' ),
	    array($this,'admin2020_loadfrontend_field_2_render'),
	    'admin2020_general_settings',
	    'admin2020_pluginPage_section'
	  );
		add_settings_field(
	    'admin2020_show_quick_links',
	    esc_html__( 'Hide admin bar quick links?', 'admin2020' ),
	    array($this,'admin2020_show_quick_links_render'),
	    'admin2020_general_settings',
	    'admin2020_pluginPage_section'
	  );


		///////ADMIN BAR SECTION
		add_settings_field(
			'admin2020_flyout_breaker',
			'<h4 class="uk-margin-top">'.esc_html__( 'User Flyout Menu', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_flyout_website_link',
			esc_html__( 'Hide View Website Link?', 'admin2020' ),
			array($this,'admin2020_flyout_website_link_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_flyout_profile_link',
			esc_html__( 'Hide Profile Links?', 'admin2020' ),
			array($this,'admin2020_flyout_profile_link_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_flyout_update_link',
			esc_html__( 'Hide Update Links?', 'admin2020' ),
			array($this,'admin2020_flyout_update_link_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);

		add_settings_field(
			'admin2020_flyout_darkmode_link',
			esc_html__( 'Hide Dark Mode toggle?', 'admin2020' ),
			array($this,'admin2020_flyout_darkmode_link_render'),
			'admin2020_general_settings',
			'admin2020_pluginPage_section'
		);





		////////////////////
		////OVERVIEW SECTION
		////////////////////

		register_setting( 'admin2020_overview_settings', 'admin2020_settings' );

		add_settings_section(
			'admin2020_pluginPage_section_overview',
			"",
			"",
			'admin2020_overview_settings'
		);

		add_settings_field(
			'admin2020_overiew_page_breaker',
			'<h4 class="">'.esc_html__( 'Overview', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_overview_settings',
			'admin2020_pluginPage_section_overview'
		);

		add_settings_field(
			'admin2020_disable_overview',
			esc_html__( 'Disable overview page?', 'admin2020' ),
			array($this,'admin2020_disable_overview_render'),
			'admin2020_overview_settings',
			'admin2020_pluginPage_section_overview'
		);

		add_settings_field(
			'admin2020_break_overview',
			'------------------------',
			array($this,"admin2020_blank_render"),
			'admin2020_overview_settings',
			'admin2020_pluginPage_section_overview'
		);


		////TOTAL POSTS
		add_settings_field(
		 'admin2020_overview_total_posts',
		 esc_html__( 'Hide Total Posts', 'admin2020' ),
		 array($this,'admin2020_overview_total_posts_render'),
		 'admin2020_overview_settings',
		 'admin2020_pluginPage_section_overview'
	 );
	 ////TOTAL PAGES
	 add_settings_field(
			'admin2020_overview_total_pages',
			esc_html__( 'Hide Total Pages', 'admin2020' ),
			array($this,'admin2020_overview_total_pages_render'),
			'admin2020_overview_settings',
			'admin2020_pluginPage_section_overview'
		);
		////TOTAL COMMENTS
 	 	add_settings_field(
 			'admin2020_overview_total_comments',
 			esc_html__( 'Hide Total Comments', 'admin2020' ),
 			array($this,'admin2020_overview_total_comments_render'),
 			'admin2020_overview_settings',
 			'admin2020_pluginPage_section_overview'
 		);
		////RECENT COMMENTS
 	 	add_settings_field(
 			'admin2020_overview_recent_comments',
 			esc_html__( 'Hide Recent Comments', 'admin2020' ),
 			array($this,'admin2020_overview_recent_comments_render'),
 			'admin2020_overview_settings',
 			'admin2020_pluginPage_section_overview'
 		);
		////COMMENTS LAST 7 DAYS
 	 	add_settings_field(
 			'admin2020_overview_total_recent_comments',
 			esc_html__( 'Hide total comments in the last 7 days', 'admin2020' ),
 			array($this,'admin2020_overview_total_recent_comments_render'),
 			'admin2020_overview_settings',
 			'admin2020_pluginPage_section_overview'
 		);
		////COMMENTS MOST COMMENTED
 	 	add_settings_field(
 			'admin2020_overview_most_commented',
 			esc_html__( 'Hide most commented posts', 'admin2020' ),
 			array($this,'admin2020_overview_most_commented_render'),
 			'admin2020_overview_settings',
 			'admin2020_pluginPage_section_overview'
 		);
		////NEW USERS
 	 	add_settings_field(
 			'admin2020_overview_new_users',
 			esc_html__( 'Hide new users', 'admin2020' ),
 			array($this,'admin2020_overview_new_users_render'),
 			'admin2020_overview_settings',
 			'admin2020_pluginPage_section_overview'
 		);
		////SYSTEM INFO
 	 	add_settings_field(
 			'admin2020_overview_system_info',
 			esc_html__( 'Hide system info', 'admin2020' ),
 			array($this,'admin2020_overview_system_info_render'),
 			'admin2020_overview_settings',
 			'admin2020_pluginPage_section_overview'
 		);
		////WIDGETS





		////////////////////
		////CONTENT SECTION
		////////////////////
		register_setting( 'admin2020_content_settings', 'admin2020_settings' );



		add_settings_section(
			'admin2020_pluginPage_section_content',
			"",
			"",
			'admin2020_content_settings'
		);

		add_settings_field(
			'admin2020_overiew_page_breaker',
			'<h4 class="">'.esc_html__( 'Content Page', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_content_settings',
			'admin2020_pluginPage_section_content'
		);
		////DISABLE CONTENT PAGE
		add_settings_field(
		 'admin2020_content_show_content',
		 esc_html__( 'Disable Content Page?', 'admin2020' ),
		 array($this,'admin2020_content_show_content_render'),
		 'admin2020_content_settings',
		 'admin2020_pluginPage_section_content'
	 );

	 ////CONTENT TO SHOW ON CONTENT PAGE
	 add_settings_field(
		'admin2020_content_included_posts',
		esc_html__( 'Post types to include?', 'admin2020' ),
		array($this,'admin2020_content_included_posts_render'),
		'admin2020_content_settings',
		'admin2020_pluginPage_section_content'
	);


	 ////////////////////
	 ////MENU SECTION
	 ////////////////////

	register_setting( 'admin2020_menu_settings', 'admin2020_settings' );

	add_settings_section(
		'admin2020_pluginPage_section_menu',
		"",
		"",
		'admin2020_menu_settings'
	);


	add_settings_field(
		'admin2020_usermenu_fields',
		"",
		array($this,'admin2020_usermenu_fields_render'),
		'admin2020_menu_settings',
		'admin2020_pluginPage_section_menu'
	);

		////////////////////
 	 ////GOOGLE ANALYTICS
 	 ////////////////////

	register_setting( 'admin2020_google_settings', 'admin2020_settings' );

	add_settings_section(
		'admin2020_pluginPage_section_google',
		'',
		'',
		'admin2020_google_settings'
	);

	add_settings_field(
		'admin2020_overiew_page_breaker',
		'',
		array($this,'admin2020_google_breaker_render'),
		'admin2020_google_settings',
		'admin2020_pluginPage_section_google'
	);

	add_settings_field(
		'admin2020_analytics_token',
		esc_html__( 'Google Analytics Account:', 'admin2020' ),
		array($this,'admin2020_analytics_token_render'),
		'admin2020_google_settings',
		'admin2020_pluginPage_section_google'
	);

	add_settings_field(
		'admin2020_analytics_view',
		"",
		array($this,'admin2020_blank_render'),
		'admin2020_google_settings',
		'admin2020_pluginPage_section_google'
	);

	////////////////////
	////CUSTOM CODE SECTION
	////////////////////

	register_setting( 'admin2020_advanced_settings', 'admin2020_settings' );

	add_settings_section(
		'admin2020_pluginPage_section_advanced',
		'',
		array($this,"admin2020_blank_render"),
		'admin2020_advanced_settings'
	);

	add_settings_field(
		'admin2020_overiew_page_breaker',
		'<h4 class="">'.esc_html__( 'Advanced', 'admin2020' )."</h4>",
		array($this,'admin2020_overview_breaker_render'),
		'admin2020_advanced_settings',
		'admin2020_pluginPage_section_advanced'
	);
		////CUSTOM CSS
	add_settings_field(
		 'admin2020_custom_css',
		 esc_html__( 'Custom CSS', 'admin2020' ),
		 array($this,'admin2020_custom_css_render'),
		 'admin2020_advanced_settings',
		 'admin2020_pluginPage_section_advanced'
	 );
	 ////CUSTOM JS
	 add_settings_field(
		'admin2020_custom_js',
		esc_html__( 'Custom JS', 'admin2020' ),
		array($this,'admin2020_custom_js_render'),
		'admin2020_advanced_settings',
		'admin2020_pluginPage_section_advanced'
	);




	}

	public function admin2020_blank_render(){

	}



	public function admin2020_licence_key_render(  ) {

		if (!current_user_can('administrator')){
			return;
		}

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_pluginPage_licence_key'])){
			$value = $options['admin2020_pluginPage_licence_key'];
		} else {
			$value = "";
		}

		?>
		<input type='password' style="width:100%;margin-bottom:15px;" name='admin2020_settings[admin2020_pluginPage_licence_key]' placeholder="xxxx-xxxx-xxxx-xxxx" value="<?php echo $value?>">
		<?php

	}

	public function admin2020_overiew_breaker_render(  ) {

	}
	public function admin2020_google_breaker_render(  ) {
		?>
		<h4>Google Analytics</h4>

		<?php
	}

	public function admin2020_overview_breaker_render(  ) {
	}




	public function admin2020_image_field_0_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_image_field_0'])){
			$value = $options['admin2020_image_field_0'];
		} else {
			$value = "";
		}
		?>
		<div class="ma-admin-backend-logo-holder">
		<img src="<?php echo $options['admin2020_image_field_0']; ?>" class="ma-admin-backend-logo" >
		</div>
	  <input id="background_image" type="text" name="admin2020_settings[admin2020_image_field_0]" value="<?php echo $options['admin2020_image_field_0']; ?>" hidden/>
	  <input id="upload_image_button" type="button" class="button-primary" value="Insert Image" hidden />

		<?php

	}


	public function admin2020_image_field_dark_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_image_field_dark'])){
			$value = $options['admin2020_image_field_dark'];
		} else {
			$value = "";
		}
		?>
		<div class="ma-admin-backend-logo-holder">
		<img src="<?php echo $options['admin2020_image_field_dark']; ?>" class="ma-admin-backend-logo-dark" style="width: 100px;min-width: 100px;min-height: 40px;">
		</div>
		<input id="background_image_dark" type="text" name="admin2020_settings[admin2020_image_field_dark]" value="<?php echo $options['admin2020_image_field_dark']; ?>" hidden/>
		<input id="upload_image_button" type="button" class="button-primary" value="Insert Image" hidden />
		<span uk-icon="info"></span>
	  <p uk-dropdown class="uk-text-meta uk-width-medium"><?php _e("If no logo is set for dark mode, then it will fall back to the main logo.","admin2020") ?></p>

		<?php

	}

	public function admin2020_login_background_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_login_background'])){
			$value = $options['admin2020_login_background'];
		} else {
			$value = "";
		}
		?>
		<div class="ma-admin-backend-logo-holder">
		<img src="<?php echo $value ?>" class="ma-admin-backend-background-image" style="width: 100px;min-width: 100px;min-height: 40px;">
		</div>
		<input id="login_background_image" type="text" name="admin2020_settings[admin2020_login_background]" value="<?php echo $value ?>" hidden/>
		<input id="login_upload_image_button" type="button" class="button-primary" value="Insert Image" hidden />
		<p style="position: relative;width: 100%;float: left;"><button class="uk-button uk-button-link" type="button" style="float:left;" onclick="jQuery('.ma-admin-backend-background-image').attr('src','');jQuery('#login_background_image').val('');"><?php _e('Remove Background Image')?></button></p>

		<?php

	}





	public function admin2020_disablestyles_field_2_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_disablestyles_field_2'])){
			$value = $options['admin2020_disablestyles_field_2'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_disablestyles_field_2]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>
		<span uk-icon="info"></span>
		<p uk-dropdown class="uk-text-meta uk-width-medium"><?php _e('If Admin 2020 is causing issues on other plugin option pages you can choose to disable Admin 2020 styles on these pages. This can help with visibility issues that may arrise.','admin2020') ?></p>


	  <?php

	}

	public function admin2020_loadfrontend_field_2_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_loadfrontend_field_2'])){
			$value = $options['admin2020_loadfrontend_field_2'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_loadfrontend_field_2]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

	  <span uk-icon="info"></span>
	  <p uk-dropdown class="uk-text-meta uk-width-medium"><?php _e('Enabling this may cause issues with your current theme.','admin2020')?></P>
	  <?php

	}

	public function admin2020_show_quick_links_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_show_quick_links'])){
			$value = $options['admin2020_show_quick_links'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_show_quick_links]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_flyout_website_link_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_flyout_website_link'])){
			$value = $options['admin2020_flyout_website_link'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_flyout_website_link]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_flyout_update_link_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_flyout_update_link'])){
			$value = $options['admin2020_flyout_update_link'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_flyout_update_link]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_flyout_profile_link_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_flyout_profile_link'])){
			$value = $options['admin2020_flyout_profile_link'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_flyout_profile_link]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_flyout_darkmode_link_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_flyout_darkmode_link'])){
			$value = $options['admin2020_flyout_darkmode_link'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_flyout_darkmode_link]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_admin2020_loader_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_admin2020_loader'])){
			$value = $options['admin2020_admin2020_loader'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_admin2020_loader]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_total_posts_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overview_total_posts'])){
			$value = $options['admin2020_overview_total_posts'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overview_total_posts]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_total_pages_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overview_total_pages'])){
			$value = $options['admin2020_overview_total_pages'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overview_total_pages]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_total_comments_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overview_total_comments'])){
			$value = $options['admin2020_overview_total_comments'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overview_total_comments]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_recent_comments_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overview_recent_comments'])){
			$value = $options['admin2020_overview_recent_comments'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overview_recent_comments]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_total_recent_comments_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overview_total_recent_comments'])){
			$value = $options['admin2020_overview_total_recent_comments'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overview_total_recent_comments]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_most_commented_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overview_most_commented'])){
			$value = $options['admin2020_overview_most_commented'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overview_most_commented]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_new_users_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overview_new_users'])){
			$value = $options['admin2020_overview_new_users'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overview_new_users]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_system_info_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overview_system_info'])){
			$value = $options['admin2020_overview_system_info'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overview_system_info]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}



	public function admin2020_content_show_content_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_content_show_content'])){
			$value = $options['admin2020_content_show_content'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_content_show_content]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_content_included_posts_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_content_included_posts'])){
			$value = $options['admin2020_content_included_posts'];
		} else {
			$value = array("post","page");
		}

		$args = array(
		   'public'   => true,
		);

		$output = 'objects';
		$post_types = get_post_types( $args, $output );

		?>
		<button class="uk-button uk-button-default" type="button"><?php echo count($value)." ".__("Selected","admin2020") ?></button>
		<div uk-dropdown="mode: click">
			<select class="uk-select admin2020_select_multiple" name='admin2020_settings[admin2020_content_included_posts][]' multiple='multiple'>
				<?php
				foreach($post_types as $post_type){

					$shortname = $post_type->name;
					$fullname = $post_type->label;

					if($shortname == 'attachment'){
						continue;
					}

					$selected = "";
					if (in_array($shortname,$value)){
						$selected = "selected";
					}

					?><option <?php echo $selected ?> value="<?php echo $shortname?>"><?php echo $fullname?></option><?php

				} ?>
	    </select>
		</div>
		<?php

	}


	public function admin2020_search_included_posts_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_search_included_posts'])){
			$value = $options['admin2020_search_included_posts'];
		} else {
			$value = array("post","page");
		}

		$args = array(
		   'public'   => true,
		);

		$output = 'objects';
		$post_types = get_post_types( $args, $output );

		?>
		<button class="uk-button uk-button-default" type="button"><?php echo count($value)." ".__("Selected","admin2020") ?></button>
		<div uk-dropdown="mode: click">
			<select class="uk-select admin2020_select_multiple" name='admin2020_settings[admin2020_search_included_posts][]' multiple='multiple'>
				<?php
				foreach($post_types as $post_type){

					$shortname = $post_type->name;
					$fullname = $post_type->label;

					if($shortname == 'attachment'){
						continue;
					}

					$selected = "";
					if (in_array($shortname,$value)){
						$selected = "selected";
					}

					?><option <?php echo $selected ?> value="<?php echo $shortname?>"><?php echo $fullname?></option><?php

				} ?>
	    </select>
		</div>
		<?php

	}

	public function admin2020_disable_admin2020_by_user_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_disable_admin2020_by_user'])){
			$value = $options['admin2020_disable_admin2020_by_user'];
		} else {
			$value = array();
		}

		$roles = $this->get_editable_roles();

		?>
		<button class="uk-button uk-button-default" type="button"><?php echo count($value)." ".__("Selected","admin2020") ?></button>
		<div uk-dropdown="mode: click">
			<select class="uk-select admin2020_select_multiple" style="overflow:auto" name='admin2020_settings[admin2020_disable_admin2020_by_user][]' multiple='multiple'>
				<?php
				foreach($roles as $role){

					$shortname = $role['name'];

					$selected = "";
					if (in_array($shortname,$value)){
						$selected = "selected";
					}

					?><option <?php echo $selected ?> value="<?php echo $shortname?>"><?php echo $shortname?></option><?php

				} ?>
	    </select>
		</div>
		<?php

	}



	public function admin2020_disable_overview_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_disable_overview'])){
			$value = $options['admin2020_disable_overview'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_disable_overview]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_disable_menu_search_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_disable_menu_search'])){
			$value = $options['admin2020_disable_menu_search'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_disable_menu_search]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>
		<?php

	}

	public function admin2020_overiew_homepage_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overiew_homepage'])){
			$value = $options['admin2020_overiew_homepage'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overiew_homepage]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}


	public function admin2020_overiew_media_gallery_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overiew_media_gallery'])){
			$value = $options['admin2020_overiew_media_gallery'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overiew_media_gallery]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overiew_primary_color_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overiew_primary_color'])){
			$value = $options['admin2020_overiew_primary_color'];
		} else {
			$value = "";
		}
		?>
		<input type='text' class="colorpicker" name='admin2020_settings[admin2020_overiew_primary_color]' value="<?php echo $value; ?>" >
		<script>
		jQuery(document).ready(function($){
		    $('.colorpicker').wpColorPicker();
		});
		</script>
		<?php

	}

	public function admin2020_appearance_theme_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_appearance_theme'])){
			$value = $options['admin2020_appearance_theme'];
		} else {
			$value = "";
		}

		//$directory = plugin_dir_path( dirname( __FILE__ ) ) . 'assets/css/themes';
		//$available_themes = array_diff(scandir($directory), array('..', '.'));
		$themes = array();
		$available_themes = apply_filters( 'admin2020_register_theme', $themes );

		if(count($available_themes) < 1){
			?>
			<p>No themes available</p>
			<?php
			return;
		}
		?>

		<select class="uk-select" name='admin2020_settings[admin2020_appearance_theme]'>
			<option value="default" selected><?php _e('Default','admin2020')?></option>
			<?php

			foreach ($available_themes as $theme){

				$name = $theme['name'];
				$path = $theme['path'];
				$id = $theme['id'];
				$selected = "";

				if ($value == $id){
					$selected = "selected";
				}
				?>
				<option <?php echo $selected?> value="<?php echo $id?>">
					<?php echo $name ?>
				</option>
				<?php

			}
			?>
		</select>


		<?php

	}

	public function admin2020_overiew_screen_options_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overiew_screen_options'])){
			$value = $options['admin2020_overiew_screen_options'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overiew_screen_options]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overiew_quick_actions_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_overiew_quick_actions'])){
			$value = $options['admin2020_overiew_quick_actions'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_overiew_quick_actions]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_adminbar_disable_search_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_adminbar_disable_search'])){
			$value = $options['admin2020_adminbar_disable_search'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_adminbar_disable_search]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_disable_login_styles_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_disable_login_styles'])){
			$value = $options['admin2020_disable_login_styles'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_disable_login_styles]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_disable_admin_notices_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_disable_admin_notices'])){
			$value = $options['admin2020_disable_admin_notices'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_disable_admin_notices]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_active_theme_name_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_active_theme_name'])){
			$value = $options['admin2020_active_theme_name'];
		} else {
			$value = "";
		}
		?>
		<input type='text' name='admin2020_settings[admin2020_active_theme_name]'  placeholder="<?php _e('New Theme Name','admin2020')?>" value='<?php echo $value ?>'>

		<?php

	}

	public function admin2020_disable_quick_actions_render(  ) {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_disable_quick_actions'])){
			$value = $options['admin2020_disable_quick_actions'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_settings[admin2020_disable_quick_actions]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}


	public function admin2020_analytics_token_render() {

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_analytics_token'])){
			$value = $options['admin2020_analytics_token'];
		} else {
			$value = "";
		}

		if (isset($options['admin2020_analytics_view'])){
			$view = $options['admin2020_analytics_view'];
		} else {
			$view = "";
		}


		if($value != ""){
			?>
			<p><?php _e("Admin 2020 is connected to analytics. To change account just click the button below and follow the steps.","admin2020"); ?></p>
			<?php
		} else {
		}

		$google_icon = esc_url(plugins_url('/assets/img/btn_google_signin_dark_normal_web@2x.png', __DIR__));
		$google_icon_hover = esc_url(plugins_url('/assets/img/btn_google_signin_dark_pressed_web@2x.png', __DIR__));

		?>

		<a class="admin2020_google_sign_in" href="javascript:gauthWindow('https://accounts.google.com/o/oauth2/auth?response_type=code&client_id=583702447211-6qiibg31fdkiug7r41qobqi1c1js1jps.apps.googleusercontent.com&redirect_uri=https://admintwentytwenty.com/analytics/view.php&scope=https://www.googleapis.com/auth/analytics.readonly&access_type=offline&approval_prompt=force');">

			<img class="admin2020_icon_no_hover" width='191' src="<?php echo $google_icon?>">
			<img class="admin2020_icon_hover" width='191' src="<?php echo $google_icon_hover?>">

		</a>

		<input class="hidden" name='admin2020_settings[admin2020_analytics_token]' id="admin2020_token" value="<?php echo $value?>">
		<input class="hidden" name='admin2020_settings[admin2020_analytics_view]' id="admin2020_view" value="<?php echo $view?>">

		<script type="text/javascript">

		function gauthWindow (url) {

      var newWindow = window.open(url, 'name', 'height=600,width=450');

      if (window.focus) {
        newWindow.focus();
      }

	     window.onmessage = function (e) {

	      if (e.origin == 'https://admintwentytwenty.com'  && e.data) {
					try{

	        	var analyticsdata = JSON.parse(e.data);

						if (analyticsdata.code && analyticsdata.view){
							newWindow.close();
							admin2020_set_google_data(analyticsdata.view,analyticsdata.code);
		        }

					} catch(err){
						///ERROR
					}



	      }
	  	}
		}

		</script>
		<?php

	}




	public function admin2020_usermenu_fields_render() {

		$options = get_option( 'admin2020_settings' );
		$roles = $this->get_editable_roles();
		$utils = new Admin2020_Util();
		global $maAdminMenuArray, $maAdminSubMenuArray,$menu, $submenu;

		?>
		<button style="float:right;" onclick="admin2020_reset_menu_settings()" class="uk-button uk-button-default" type="button"><?php _e("Reset Menu","admin2020")?></button>
		<div class="uk-width-xlarge"  uk-sortable="handle: .admin2020_drag_handle" id="admin2020_menu_editor">

			<?php
			///SORT ARRAY
			$blankarray = array();
			$the_counter = 0;

			foreach($maAdminMenuArray as $item){

				if (isset($options['admin2020_menu_order_'.$item[2]])){
					$current_posis = $options['admin2020_menu_order_'.$item[2]];
					if($current_posis != ""){
						$item['order'] = $current_posis;
					} else {
						$item['order'] = $the_counter;
					}
				} else {
					$item['order'] = $the_counter;
				}

				$the_counter = $the_counter + 1;

				array_push($blankarray,$item);

			}

			usort($blankarray, function($a, $b) {
    	return $a['order'] <=> $b['order'];
			});

			$maAdminMenuArray = $blankarray;

			foreach($maAdminMenuArray as $item){

				$menu_order_name = 'admin2020_settings[admin2020_menu_order_'.$item[2].']';

				if (isset($options['admin2020_menu_order_'.$item[2]])){
					$current_posis = $options['admin2020_menu_order_'.$item[2]];
				} else {
					$current_posis = "";
				}

				if (strpos($item[2],"separator") !== false  && !$item[0]){

					///ITS A SEPERATOR
					?>
					<div class="uk-card uk-card-default uk-card-small uk-box-shadow-small admin2020-border uk-margin-small-bottom admin2020_menu_item" style="padding:15px;">
						<input type="number" name="<?php echo $menu_order_name ?>" class="top_level_order" value="<?php echo $current_posis?>" style="display:none;">
						<ul uk-accordion class="uk-margin-remove">
					    <li>
					        <a class="uk-accordion-title uk-margin-remove uk-text-small" href="#">
										<span uk-icon="grid" class="admin2020_drag_handle" style="margin-right:15px"></span>
										<?php _e("Seperator","admin2020")?>
									</a>
					        <div class="uk-accordion-content uk-margin-top">

										<?php
										$option_menu_string = 'admin2020_settings[admin2020_menu_rename_'.$item[2].']';


										if (isset($options['admin2020_menu_rename_'.$item[2]])){
											$parentname = $options['admin2020_menu_rename_'.$item[2]];
										} else {
											$parentname = "";
										}


										?>
										<div class="uk-text-meta uk-margin-small-bottom"><?php _e("Change to label","admin2020")?>:</div>

										<input class="uk-input" type="text" name="<?php echo $option_menu_string ?>" value="<?php echo $parentname?>" placeholder="<?php _e("New name","admin2020")?>...">


										<div class="uk-text-meta uk-margin-small-bottom uk-margin-top"><?php _e("Hidden for roles","admin2020")?>:</div>

										<div class="uk-width-1-1"	>
												<?php


													foreach ($roles as $role){

														$rolename = $role['name'];

														$rolelowercase = strtolower($rolename);
														$rolelowercase = str_replace(" ","_",$rolelowercase);

														if (isset($options['admin2020_menu_'.$rolelowercase.'_'.$item[2]])){
															$value = $options['admin2020_menu_'.$rolelowercase.'_'.$item[2]];
														} else {
															$value = 0;
														}

														$optionstring = 'admin2020_settings[admin2020_menu_'.$rolelowercase.'_'.$item[2].']';

														?>
														<div style="float:left;width:100%;margin-bottom:5px;">
															<input style="margin-right:15px;" name="<?php echo $optionstring ?>" class="ma-admin-menu-checkbox" type="checkbox" <?php checked( $value, 1 ); ?> value="1">
															<span><?php echo $rolename?></span>
														</div>

														<?php

													}?>
										</div>



					        </div>
					    </li>
						</ul>
        	</div>
					<?php

				} else {

					///ITS NOT A SEPERATOR
					?>
					<div class="uk-card uk-card-default uk-card-small uk-box-shadow-small admin2020-border uk-margin-small-bottom admin2020_menu_item" style="padding:15px;">
						<input type="number" name="<?php echo $menu_order_name ?>" class="top_level_order" value="<?php echo $current_posis?>" style="display:none;">
						<ul uk-accordion class="uk-margin-remove">
					    <li>
					        <a class="uk-accordion-title uk-margin-remove uk-text-small" href="#">
										<span uk-icon="grid" class="admin2020_drag_handle" style="margin-right:15px"></span>
										<?php echo $item[0]?>
									</a>
					        <div class="uk-accordion-content uk-margin-top">

										<ul uk-tab>
										    <li><a href="#"><?php _e("Settings","admin2020")?></a></li>
										    <li><a href="#"><?php _e("Sub Menu","admin2020")?></a></li>
										</ul>

										<ul class="uk-switcher uk-margin">
											<!--SETTINGS MENU TAB -->
											<li>
												<?php
												$parentitemname = strip_tags(strtolower($item[5]));
												$parentitemname = str_replace(" ","_",$parentitemname);
												$option_menu_string = 'admin2020_settings[admin2020_menu_rename_'.$parentitemname.']';
												$option_icon_string = 'admin2020_settings[admin2020_icon_'.$parentitemname.']';
												$disabled_string = 'admin2020_settings[admin2020_disabled_'.$parentitemname.']';

												if (isset($options['admin2020_menu_rename_'.$parentitemname])){
													$parentname = $options['admin2020_menu_rename_'.$parentitemname];
												} else {
													$parentname = "";
												}

												if (isset($options['admin2020_icon_'.$parentitemname])){
													$icon = $options['admin2020_icon_'.$parentitemname];
												} else {
													$icon = "";
												}

												if (isset($options['admin2020_disabled_'.$parentitemname])){
													$disabled = $options['admin2020_disabled_'.$parentitemname];
												} else {
													$disabled = "";
												}
												?>
												<div class="uk-text-meta uk-margin-small-bottom"><?php _e("Rename as","admin2020")?>:</div>

												<input class="uk-input" type="text" name="<?php echo $option_menu_string ?>" value="<?php echo $parentname?>" placeholder="<?php _e("New name","admin2020")?>...">

												<div class="uk-text-meta uk-margin-small-bottom uk-margin-top"><?php _e("Set custom icon","admin2020")?>:</div>

												<div onclick="open_icon_chooser(this)">


													<button class="uk-button uk-button-default" type="button">
														<?php _e("Choose Icon","admin2020")?>
														<input class="uk-input admin2020_icon_value hidden" type="text" name="<?php echo $option_icon_string ?>" value="<?php echo $icon?>">
													</button>

													<span class="uk-margin-left admin2020_icon_display" uk-icon="<?php echo $icon?>"></span>

												</div>

												<div class="uk-grid-divider uk-child-width-1-2" uk-grid>

													<div>
														<div class="uk-text-meta uk-margin-small-bottom uk-margin-top"><?php _e("Hidden for roles","admin2020")?>:</div>

														<div class="uk-width-1-1"	>
																<?php


																	foreach ($roles as $role){

																		$rolename = $role['name'];

																		$rolelowercase = strtolower($rolename);
																		$rolelowercase = str_replace(" ","_",$rolelowercase);

																		if (isset($options['admin2020_menu_'.$rolelowercase.'_'.$parentitemname])){
																			$value = $options['admin2020_menu_'.$rolelowercase.'_'.$parentitemname];
																		} else {
																			$value = 0;
																		}

																		$optionstring = 'admin2020_settings[admin2020_menu_'.$rolelowercase.'_'.$parentitemname.']';

																		?>
																		<div style="float:left;width:100%;margin-bottom:5px;">
																			<input style="margin-right:15px;" name="<?php echo $optionstring ?>" class="ma-admin-menu-checkbox" type="checkbox" <?php checked( $value, 1 ); ?> value="1">
																			<span><?php echo $rolename?></span>
																		</div>

																		<?php

																	}?>
														</div>
													</div>

													<div>

														<div class="uk-text-meta uk-margin-small-bottom uk-margin-top"><?php _e("Disable Admin 2020 on this page?","admin2020")?></div>

														<label class="admin2020_switch">
																<input type='checkbox' name='<?php echo $disabled_string?>' <?php checked( $disabled, 1 ); ?> value='1'>
																<span class="admin2020_slider"></span>
														</label>

													</div>

												</div>

											</li>
											<!--SUBB MENU TAB -->
											<li>
												<?php
												///CHECK FOR SUBS
												$link = $item[2];
												if(isset($submenu[$link])){
													$subitems = $submenu[$link];
												} else {
						              $subitems = array();
						            }

												if (count($subitems)>0 && $item[0]){

													///SORT ARRAY
													$blankarray = array();

													foreach($subitems as $item){

														$itemname = strip_tags(strtolower($item[0]));
														$itemname = str_replace(" ","_",$itemname);
														$counter = 0;

														if (isset($options['admin2020_submenu_order_'.$parentitemname.$itemname])){
															$current_posis = $options['admin2020_submenu_order_'.$parentitemname.$itemname];
															if($current_posis != ""){
																$item['order'] = $current_posis;
															} else {
																$item['order'] = $counter;
															}
														} else {
															$item['order'] = $counter;
														}

														$counter = $counter + 1;

														array_push($blankarray,$item);

													}

													usort($blankarray, function($a, $b) {
										    	return $a['order'] <=> $b['order'];
													});

													$subitems = $blankarray;

													?>
													<div class="admin2020_sub_item_wrap" uk-sortable="handle: .admin2020_drag_handle">
													<?php

													foreach($subitems as $sub){

														if (!$sub[0]){
															continue;
														}

														$itemname = strip_tags(strtolower($sub[0]));
														$itemname = str_replace(" ","_",$itemname);



														if (isset($options['admin2020_submenu_rename_'.$parentitemname.$itemname])){
															$subname = $options['admin2020_submenu_rename_'.$parentitemname.$itemname];
														} else {
															$subname = "";
														}


														$option_sub_menu_string = 'admin2020_settings[admin2020_submenu_rename_'.$parentitemname.$itemname.']';

														$menu_order_name = 'admin2020_settings[admin2020_submenu_order_'.$parentitemname.$itemname.']';
														$disabled_sub_string = 'admin2020_settings[admin2020_disabled_sub_'.$parentitemname.$itemname.']';

														if (isset($options['admin2020_submenu_order_'.$parentitemname.$itemname])){
															$current_posis = $options['admin2020_submenu_order_'.$parentitemname.$itemname];
														} else {
															$current_posis = "";
														}

														if (isset($options['admin2020_disabled_sub_'.$parentitemname.$itemname])){
															$disabled_sub = $options['admin2020_disabled_sub_'.$parentitemname.$itemname];
														} else {
															$disabled_sub = "";
														}

														?>
														<div class="uk-card uk-card-default uk-card-small uk-box-shadow-small admin2020-border uk-margin-small-bottom admin2020_submenu_item" style="padding:15px;">
															<input type="number" name="<?php echo $menu_order_name ?>" class="sub_level_order" value="<?php echo $current_posis?>" style="display:none;">
															<ul uk-accordion class="uk-margin-remove">
														    <li>
														        <a class="uk-accordion-title uk-margin-remove uk-text-small" href="#">
																			<span uk-icon="grid" class="admin2020_drag_handle" style="margin-right:15px"></span>
																			<?php echo $sub[0]?>
																		</a>
														        <div class="uk-accordion-content uk-margin-top">


																			<div class="uk-text-meta uk-margin-small-bottom"><?php _e("Rename as","admin2020")?>:</div>

																			<input class="uk-input" type="text" name="<?php echo $option_sub_menu_string ?>" value="<?php echo $subname?>" placeholder="<?php _e("New name","admin2020")?>...">

																			<div class="uk-grid-divider uk-child-width-1-2" uk-grid>

																					<div>

																						<div class="uk-text-meta uk-margin-small-bottom uk-margin-top"><?php _e("Hidden for roles","admin2020")?>:</div>
																						<div class="uk-width-1-1"	>
																								<?php


																									foreach ($roles as $role){

																										$rolename = $role['name'];

																										$rolelowercase = strtolower($rolename);
																										$rolelowercase = str_replace(" ","_",$rolelowercase);


																										if (isset($options['admin2020_submenu_'.$rolelowercase.'_'.$parentitemname.$itemname])){
																											$value = $options['admin2020_submenu_'.$rolelowercase.'_'.$parentitemname.$itemname];
																										} else {
																											$value = 0;
																										}

																										$optionstring = 'admin2020_settings[admin2020_submenu_'.$rolelowercase.'_'.$parentitemname.$itemname.']';

																										?>
																										<div style="float:left;width:100%;margin-bottom:5px;">
																											<input style="margin-right:15px;" name="<?php echo $optionstring ?>" class="ma-admin-menu-checkbox" type="checkbox" <?php checked( $value, 1 ); ?> value="1">
																											<span><?php echo $rolename?></span>
																										</div>

																										<?php

																									}?>
																						</div>
																					</div>

																					<div>

																						<div class="uk-text-meta uk-margin-small-bottom uk-margin-top"><?php _e("Disable Admin 2020 on this page?","admin2020")?></div>

																						<label class="admin2020_switch">
																								<input type='checkbox' name='<?php echo $disabled_sub_string?>' <?php checked( $disabled_sub, 1 ); ?> value='1'>
																								<span class="admin2020_slider"></span>
																						</label>

																					</div>

																			</div>


														      </div>
														    </li>
															</ul>
									        	</div>

														<?php

													}////END OF SUB ITEMS LOOP

													?></div><?php
												} else {
													?><span class="uk-text-meta"><?php _e("No sub menu items","admin2020")?></span><?php
												}////END OF SUB ITEMS CHECK
												?>
											</li>

										</ul>
										<!--END OF SWITCHER -->

					        </div>
					    </li>
						</ul>
        	</div>


					<?php




				}

			}



			?>


		</div>

		<div id="icon-list" uk-modal>
			<div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical" style="width:70%;border-radius:4px">
				<h2 class="uk-modal-title">Icons</h2>
				<div class="uk-grid-small uk-child-width-1-2@s uk-child-width-1-4@m uk-grid uk-height-large uk-overflow-auto" uk-grid="" id="admin2020_icon_select">
										<div>

											<ul class="uk-list">

												<!-- App -->
												<li class="uk-text-warning"></span> Use Default <span class="uk-margin-small-right uk-icon" uk-icon="noicon"></li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="home"></span> home</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="sign-in"></span> sign-in</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="sign-out"></span> sign-out</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="user"></span> user</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="users"></span> users</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="lock"></span> lock</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="unlock"></span> unlock</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="settings"></span> settings</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="cog"></span> cog</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="nut"></span> nut</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="comment"></span> comment</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="commenting"></span> commenting</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="comments"></span> comments</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="hashtag"></span> hashtag</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="tag"></span> tag</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="cart"></span> cart</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="credit-card"></span> credit-card</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="mail"></span> mail</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="receiver"></span> receiver</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="print"></span> print</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="search"></span> search</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="location"></span> location</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="bookmark"></span> bookmark</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="code"></span> code</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="paint-bucket"></span> paint-bucket</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="camera"></span> camera</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="video-camera"></span> video-camera</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="bell"></span> bell</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="microphone"></span> microphone</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="bolt"></span> bolt</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="star"></span> star</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="heart"></span> heart</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="happy"></span> happy</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="lifesaver"></span> lifesaver</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="rss"></span> rss</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="social"></span> social</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="git-branch"></span> git-branch</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="git-fork"></span> git-fork</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="world"></span> world</li>

											</ul>

										</div>
										<div>

											<ul class="uk-list">

												<!-- App -->
												<li><span class="uk-margin-small-right uk-icon" uk-icon="calendar"></span> calendar</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="clock"></span> clock</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="history"></span> history</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="future"></span> future</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="pencil"></span> pencil</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="trash"></span> trash</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="move"></span> move</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="link"></span> link</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="question"></span> question</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="info"></span> info</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="warning"></span> warning</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="image"></span> image</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="thumbnails"></span> thumbnails</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="table"></span> table</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="list"></span> list</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="menu"></span> menu</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="grid"></span> grid</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="more"></span> more</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="more-vertical"></span> more-vertical</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="plus"></span> plus</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="plus-circle"></span> plus-circle</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="minus"></span> minus</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="minus-circle"></span> minus-circle</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="close"></span> close</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="check"></span> check</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="ban"></span> ban</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="refresh"></span> refresh</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="play"></span> play</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="play-circle"></span> play-circle</li>

												<!-- Devices -->
												<li><span class="uk-margin-small-right uk-icon" uk-icon="tv"></span> tv</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="desktop"></span> desktop</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="laptop"></span> laptop</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="tablet"></span> tablet</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="phone"></span> phone</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="tablet-landscape"></span> tablet-landscape</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="phone-landscape"></span> phone-landscape</li>


											</ul>

										</div>
										<div>

											<ul class="uk-list">

												<!-- Storage -->
												<li><span class="uk-margin-small-right uk-icon" uk-icon="file"></span> file</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="file-text"></span> file-text</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="file-pdf"></span> file-pdf</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="copy"></span> copy</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="file-edit"></span> file-edit</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="folder"></span> folder</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="album"></span> album</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="push"></span> push</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="pull"></span> pull</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="server"></span> server</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="database"></span> database</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="cloud-upload"></span> cloud-upload</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="cloud-download"></span> cloud-download</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="download"></span> download</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="upload"></span> upload</li>

												<!-- Direction -->
												<li><span class="uk-margin-small-right uk-icon" uk-icon="reply"></span> reply</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="forward"></span> forward</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="expand"></span> expand</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="shrink"></span> shrink</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="arrow-up"></span> arrow-up</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="arrow-down"></span> arrow-down</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="arrow-left"></span> arrow-left</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="arrow-right"></span> arrow-right</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="chevron-up"></span> chevron-up</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="chevron-down"></span> chevron-down</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="chevron-left"></span> chevron-left</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="chevron-right"></span> chevron-right</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="chevron-double-left"></span> chevron-double-left</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="chevron-double-right"></span> chevron-double-right</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="triangle-up"></span> triangle-up</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="triangle-down"></span> triangle-down</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="triangle-left"></span> triangle-left</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="triangle-right"></span> triangle-right</li>

											</ul>

										</div>
										<div>

											<ul class="uk-list">

												<!-- Editor -->
												<li><span class="uk-margin-small-right uk-icon" uk-icon="bold"></span> bold</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="italic"></span> italic</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="strikethrough"></span> strikethrough</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="quote-right"></span> quote-right</li>

												<!-- Brands -->
												<li><span class="uk-margin-small-right uk-icon" uk-icon="500px"></span> 500px</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="behance"></span> behance</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="dribbble"></span> dribbble</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="etsy"></span> etsy</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="facebook"></span> facebook</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="flickr"></span> flickr</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="foursquare"></span> foursquare</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="github"></span> github</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="github-alt"></span> github-alt</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="gitter"></span> gitter</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="google"></span> google</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="google-plus"></span> google-plus</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="instagram"></span> instagram</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="joomla"></span> joomla</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="linkedin"></span> linkedin</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="pagekit"></span> pagekit</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="pinterest"></span> pinterest</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="reddit"></span> reddit</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="soundcloud"></span> soundcloud</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="tripadvisor"></span> tripadvisor</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="tumblr"></span> tumblr</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="twitter"></span> twitter</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="uikit"></span> uikit</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="vimeo"></span> vimeo</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="whatsapp"></span> whatsapp</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="wordpress"></span> wordpress</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="xing"></span> xing</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="yelp"></span> yelp</li>
												<li><span class="uk-margin-small-right uk-icon" uk-icon="youtube"></span> youtube</li>
											</ul>

										</div>
									</div>
				<p class="uk-text-right">
					<button class="uk-button uk-button-default uk-modal-close" type="button"><?php _e("Cancel","admin2020")?></button>
					<button class="uk-button uk-button-primary" type="button" id="icon_selected"><?php _e("Select","admin2020")?></button>
				</p>
			</div>
		</div>

		<script type="text/javascript">
		jQuery("#admin2020_icon_select li").on("click", function(){
			jQuery("#admin2020_icon_select li").removeClass('iconselected');
			jQuery(this).addClass('iconselected');
		})
		</script>

		<script type="text/javascript">
		jQuery("#admin2020_menu_editor").on("moved",function(){

			jQuery("#admin2020_menu_editor .admin2020_menu_item").each(function() {
			  index = jQuery( this ).index();
				jQuery( this ).find('.top_level_order').val(index);
			});

		});

		jQuery(".admin2020_sub_item_wrap").on("moved",function(){

			jQuery(".admin2020_sub_item_wrap .admin2020_submenu_item").each(function() {
			  index = jQuery( this ).index();
				jQuery( this ).find('.sub_level_order').attr('value', index);
			});

		});
		</script>


		<?php

	}

	public function admin2020_custom_css_render( ) {

		$userid = get_current_user_id();
		$current = get_user_meta($userid, 'darkmode', true);

		if ($current === "true"){
			$theme = "material-ocean";
		} else {
			$theme = "xq-light";
		}

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_custom_css'])){
			$value = $options['admin2020_custom_css'];
		} else {
			$value = "/* custom css here */";
		}
		?>
		<p class="uk-text-meta"><?php _e("To target styles only in dark mode, use class","admin2020")?> <code>ma-admin-dark</code> <?php _e("which is applied to the body.","admin2020") ?></p>
		<textarea id="cssholder" name='admin2020_settings[admin2020_custom_css]' style="display:none;"><?php echo $value?></textarea>
		<div class="admin2020_code_editor uk-margin-bottom" id="admin2020css"></div>
		<script>

		jQuery("#trigger_code_editors").one( "click", function() {

			setTimeout(function() {

				var myCodeMirror = CodeMirror(document.getElementById("admin2020css"), {
				  value: jQuery("#cssholder").val(),
				  mode:  "css",
					theme: "<?php echo $theme?>",
					lineNumbers: true,
				}).on("change",function(editor){
					css = editor.getDoc().getValue("\n");
					jQuery("#cssholder").val(css);
				});

			},300);


		})

		</script>
		<?php

	}


	public function admin2020_custom_js_render( ) {

		$userid = get_current_user_id();
		$current = get_user_meta($userid, 'darkmode', true);

		if ($current === "true"){
			$theme = "material-ocean";
		} else {
			$theme = "xq-light";
		}

		$options = get_option( 'admin2020_settings' );
		if (isset($options['admin2020_custom_js'])){
			$value = $options['admin2020_custom_js'];
		} else {
			$value = "//custom js here";
		}
		?>
		<textarea id="jsholder" name='admin2020_settings[admin2020_custom_js]' style="display:none;"><?php echo $value?></textarea>
		<div class="admin2020_code_editor" id="admin2020js"></div>
		<script>

		jQuery("#trigger_code_editors").one( "click", function() {

			setTimeout(function() {

				var myCodeMirror = CodeMirror(document.getElementById("admin2020js"), {
				  value: jQuery("#jsholder").val(),
				  mode:  "javascript",
					theme: "<?php echo $theme?>",
					lineNumbers: true,
				}).on("change",function(editor){
					css = editor.getDoc().getValue("\n");
					jQuery("#jsholder").val(css);
				});

			},300);

		});

		</script>
		<?php

	}



	public function admin2020_settings_section_callback() {


		return;

	}

	public function admin2020_settings_section_menu_callback() {



		_e( 'Change what areas of wordpress your users can access', 'admin2020' );

	}

	public function admin2020_pluginPage_section_overview_callback() {



		_e( 'Change what displays on the overview page', 'admin2020' );

	}

	public function admin2020_pluginPage_section_content_callback() {



		_e( 'Content page settings', 'admin2020' );

	}

	public function admin2020_settings_section_google_callback() {



		_e( 'Here you can add your google details for dashboard reports', 'admin2020' );
		?>
		<p><?php _e('For instructions of how to get the below details, please see',"admin2020")?> <a href="https://admintwentytwenty.com/blog/activating-google-analytics-in-admin-2020" target="_blank" class="uk-link"><?php _e('here', 'admin2020')?></a></p>
		<?php

	}

	public function admin2020_pluginPage_section_advanced_callback() {



		_e( 'Further customise your dashboard with custom CSS and Javascript', 'admin2020' );

	}


	public function admin2020_options_page(  ) {

			if (is_network_admin()){
				$action = admin_url().'options.php';
			} else {
				$action = 'options.php';
			}
			?>

			<form action='<?php echo $action ?>' method='post' style="padding-bottom:70px;">

				<img style="float:left;margin-right:15px" src="https://admintwentytwenty.com/wp-content/uploads/LOGO-WHITE-circle.png" width="40" >
				<h2  style="float:left;margin-top:0;">Admin 2020
					<span class="uk-text-meta"><?php echo __('Version','admin2020').': '.$this->version?></span>
				</h2>


				<div class="admin2020topbutton" style="float:right;">
					<div class="js-upload" uk-form-custom>
					    <input type="file" single id="admin2020_export_settings" onchange="admin2020_import_settings()">
					    <button class="uk-button uk-button-primary" type="button" tabindex="-1">Import</button>
					</div>
					<button class="uk-button uk-button-primary" onclick="admin2020_export_settings_json()" type="button">Export</button>
					<a id="admin2020_download_settings" style="display:none"></a>
      	</div>

				<div class="uk-width-1-1 uk-margin-large-top" style="float:left;">
	        <div uk-grid>
	            <div class="uk-width-1-4">
	                <ul class="uk-tab-left" uk-tab="connect: #component-tab-left; animation: uk-animation-fade;">


	                    <li><a href="#"><?php _e('General','admin2020')?></a></li>
											<li><a href="#"><?php _e('Appearance','admin2020')?></a></li>
											<li><a href="#"><?php _e('Overview Page','admin2020')?></a></li>
	                    <li><a href="#"><?php _e('Content Page','admin2020')?></a></li>
											<li><a href="#"><?php _e('Menu Editor','admin2020')?></a></li>
											<li><a href="#"><?php _e('Google Analytics','admin2020')?></a></li>
											<li><a href="#" id="trigger_code_editors"><?php _e('Advanced','admin2020')?></a></li>
	                </ul>
	            </div>
	            <div class="uk-width-expand" id="admin2020_settings_area">
	                <ul id="component-tab-left" class="uk-switcher">

	                    <li>
												<?php
												settings_fields( 'admin2020_general_settings' );
												do_settings_sections( 'admin2020_general_settings' );
												?>
											</li>

											<li>
												<?php
												settings_fields( 'admin2020_appearance_settings' );
												do_settings_sections( 'admin2020_appearance_settings' );
												?>
											</li>

											<li>
												<?php
												settings_fields( 'admin2020_overview_settings' );
												do_settings_sections( 'admin2020_overview_settings' );
												?>
											</li>

											<li>
												<?php
												settings_fields( 'admin2020_content_settings' );
												do_settings_sections( 'admin2020_content_settings' );
												?>
											</li>

											<li id="admin2020_settings_menu_panel">
												<?php
												settings_fields( 'admin2020_menu_settings' );
												do_settings_sections( 'admin2020_menu_settings' );
												?>
											</li>

											<li>
												<?php
												settings_fields( 'admin2020_google_settings' );
												do_settings_sections( 'admin2020_google_settings' );
												?>
											</li>

											<li>
												<?php
												settings_fields( 'admin2020_advanced_settings' );
												do_settings_sections( 'admin2020_advanced_settings' );
												?>
											</li>
	                </ul>
	            </div>
	        </div>
				</div>

				<div class="admin2020_save_float">
					<?php submit_button();?>
				</div>

			</form>

			<?php


	}

	public function get_editable_roles() {
	    global $wp_roles;

	    $all_roles = $wp_roles->roles;
	    $editable_roles = apply_filters('editable_roles', $all_roles);

	    return $editable_roles;
	}

}
