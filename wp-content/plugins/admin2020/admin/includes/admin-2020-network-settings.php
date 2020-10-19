<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Network_Plugin_Options{

	/// BUILD ADMIN 2020 SETTINGS PAGE
	public function run(){

		add_action( 'network_admin_menu', array($this,'admin2020_add_network_admin_menu') );
		add_action( 'admin_init',  array($this,'admin2020_network_settings') );

	}



	public function admin2020_add_network_admin_menu(  ) {

		$utils = new Admin2020_Util();
    if(!$utils->check_for_user_disarm()){
      return;
    }

		add_menu_page( 'Admin 2020', 'Admin 2020', 'manage_options', 'admin_2020', array($this,'admin2020_options_page'),"admin2020-network-page"  );

	}


	public function admin2020_network_settings(  ) {


		///////APPEARANCE SECTION
		register_setting( 'admin2020_appearance_settings_network', 'admin2020_network_settings' );

		add_settings_section(
			'admin2020_pluginPage_section_network',
			"",
			"",
			'admin2020_appearance_settings_network'
		);

		add_settings_field(
			'admin2020_overiew_appearance_breaker_network',
			'<h4 class="">'.esc_html__( 'Appearance', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_appearance_settings_network',
			'admin2020_pluginPage_section_network'
		);
		///NORMAL LOGO
	  add_settings_field(
			'admin2020_image_field_0_network',
			esc_html__( 'Admin Logo', 'admin2020' ),
			array($this,'admin2020_image_field_0_render'),
			'admin2020_appearance_settings_network',
			'admin2020_pluginPage_section_network'
		);
		///DARK LOGO
		add_settings_field(
			'admin2020_image_field_dark_network',
			esc_html__( 'Optional logo for dark mode', 'admin2020' ),
			array($this,'admin2020_image_field_dark_render'),
			'admin2020_appearance_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_login_background_network',
			esc_html__( 'Login background Image', 'admin2020' ),
			array($this,'admin2020_login_background_render'),
			'admin2020_appearance_settings_network',
			'admin2020_pluginPage_section_network'
		);



		add_settings_field(
			'admin2020_disablestyles_field_2_network',
			esc_html__( 'Disabled Admin 2020 styles on plugin pages?', 'admin2020' ),
			array($this,'admin2020_disablestyles_field_2_render'),
			'admin2020_appearance_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_overiew_primary_color_network',
			esc_html__( 'Change primary link color', 'admin2020' ),
			array($this,'admin2020_overiew_primary_color_render'),
			'admin2020_appearance_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_appearance_theme_network',
			esc_html__( 'Theme', 'admin2020' ),
			array($this,'admin2020_appearance_theme_render'),
			'admin2020_appearance_settings_network',
			'admin2020_pluginPage_section_network'
		);





		///////ADMIN GENERAL SECTION
		register_setting( 'admin2020_general_settings_network', 'admin2020_network_settings' );

		add_settings_section(
			'admin2020_pluginPage_section_network',
			"",
			"",
			'admin2020_general_settings_network'
		);

		add_settings_field(
			'admin2020_overiew_general_breaker_network',
			'<h4 class="">'.esc_html__( 'General', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		if (is_network_admin() || !is_multisite()){

			add_settings_field(
				'admin2020_pluginPage_licence_key_network',
				esc_attr__( 'Product Licence KeyNW:', 'admin2020' ),
				array($this,'admin2020_licence_key_render'),
				'admin2020_general_settings_network',
				'admin2020_pluginPage_section_network'
			);

		} else {
			register_setting( 'admin2020_pluginPage_section_network', 'admin2020_pluginPage_licence_key' );
		}

		add_settings_field(
			'admin2020_network_override',
			esc_html__( 'Override all subsite settings?', 'admin2020' ),
			array($this,'admin2020_network_override_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_edit_admin2020_by_user_network',
			esc_html__( 'Which roles can edit / view Admin 2020 settings?', 'admin2020' ),
			array($this,'admin2020_edit_admin2020_by_user_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_disable_menu_search_network',
			esc_html__( 'Disable Admin Menu Search?', 'admin2020' ),
			array($this,'admin2020_disable_menu_search_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_overiew_homepage_network',
			esc_html__( 'Set overview page as Admin homepage?', 'admin2020' ),
			array($this,'admin2020_overiew_homepage_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_overiew_media_gallery_network',
			esc_html__( 'Use default wordpress media gallery?', 'admin2020' ),
			array($this,'admin2020_overiew_media_gallery_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_disable_quick_actions_network',
			esc_html__( 'Keep quick actions below title on tables?', 'admin2020' ),
			array($this,'admin2020_disable_quick_actions_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);
		add_settings_field(
			'admin2020_disable_login_styles_network',
			esc_html__( 'Disable Admin 2020 login styles?', 'admin2020' ),
			array($this,'admin2020_disable_login_styles_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_disable_admin_notices_network',
			esc_html__( 'Disable Admin Notices?', 'admin2020' ),
			array($this,'admin2020_disable_admin_notices_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);


		///////ADMIN BAR SECTION
		add_settings_field(
			'admin2020_overiew_breaker_network',
			'<h4 class="uk-margin-top">'.esc_html__( 'Admin Bar', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_overiew_screen_options_network',
			esc_html__( 'Show screen options?', 'admin2020' ),
			array($this,'admin2020_overiew_screen_options_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_overiew_quick_actions_network',
			esc_html__( 'Disable Quick Actions in the Admin Bar?', 'admin2020' ),
			array($this,'admin2020_overiew_quick_actions_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
		 		'admin2020_search_included_posts_network',
		 		esc_html__( 'Post types available in search?', 'admin2020' ),
		 		array($this,'admin2020_search_included_posts_render'),
		 		'admin2020_general_settings_network',
		 		'admin2020_pluginPage_section_network'
		 	);

		add_settings_field(
			'admin2020_adminbar_disable_search_network',
			esc_html__( 'Disable Search in the Admin Bar?', 'admin2020' ),
			array($this,'admin2020_adminbar_disable_search_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_admin2020_loader_network',
			__( 'Disable loading bar in admin bar?', 'admin2020' ),
			array($this,'admin2020_admin2020_loader_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);
		add_settings_field(
	    'admin2020_loadfrontend_field_2_network',
	    esc_html__( 'Load Admin Menu style on front end?', 'admin2020' ),
	    array($this,'admin2020_loadfrontend_field_2_render'),
	    'admin2020_general_settings_network',
	    'admin2020_pluginPage_section_network'
	  );
		add_settings_field(
	    'admin2020_show_quick_links_network',
	    esc_html__( 'Hide admin bar quick links?', 'admin2020' ),
	    array($this,'admin2020_show_quick_links_render'),
	    'admin2020_general_settings_network',
	    'admin2020_pluginPage_section_network'
	  );


		///////ADMIN BAR SECTION
		add_settings_field(
			'admin2020_flyout_breaker_network',
			'<h4 class="uk-margin-top">'.esc_html__( 'User Flyout Menu', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_flyout_website_link_network',
			esc_html__( 'Hide View Website Link?', 'admin2020' ),
			array($this,'admin2020_flyout_website_link_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_flyout_profile_link_network',
			esc_html__( 'Hide Profile Links?', 'admin2020' ),
			array($this,'admin2020_flyout_profile_link_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_flyout_update_link_network',
			esc_html__( 'Hide Update Links?', 'admin2020' ),
			array($this,'admin2020_flyout_update_link_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);

		add_settings_field(
			'admin2020_flyout_darkmode_link_network',
			esc_html__( 'Hide Dark Mode toggle?', 'admin2020' ),
			array($this,'admin2020_flyout_darkmode_link_render'),
			'admin2020_general_settings_network',
			'admin2020_pluginPage_section_network'
		);





		////////////////////
		////OVERVIEW SECTION
		////////////////////

		register_setting( 'admin2020_overview_settings_network', 'admin2020_network_settings' );

		add_settings_section(
			'admin2020_pluginPage_section_network_overview',
			"",
			"",
			'admin2020_overview_settings_network'
		);

		add_settings_field(
			'admin2020_overiew_page_breaker_network',
			'<h4 class="">'.esc_html__( 'Overview', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_overview_settings_network',
			'admin2020_pluginPage_section_network_overview'
		);

		add_settings_field(
			'admin2020_disable_overview_network',
			esc_html__( 'Disable overview page?', 'admin2020' ),
			array($this,'admin2020_disable_overview_render'),
			'admin2020_overview_settings_network',
			'admin2020_pluginPage_section_network_overview'
		);

		add_settings_field(
			'admin2020_break_overview',
			'------------------------',
			"",
			'admin2020_overview_settings_network',
			'admin2020_pluginPage_section_network_overview'
		);


		////TOTAL POSTS
		add_settings_field(
		 'admin2020_overview_total_posts_network',
		 esc_html__( 'Hide Total Posts', 'admin2020' ),
		 array($this,'admin2020_overview_total_posts_render'),
		 'admin2020_overview_settings_network',
		 'admin2020_pluginPage_section_network_overview'
	 );
	 ////TOTAL PAGES
	 add_settings_field(
			'admin2020_overview_total_pages_network',
			esc_html__( 'Hide Total Pages', 'admin2020' ),
			array($this,'admin2020_overview_total_pages_render'),
			'admin2020_overview_settings_network',
			'admin2020_pluginPage_section_network_overview'
		);
		////TOTAL COMMENTS
 	 	add_settings_field(
 			'admin2020_overview_total_comments_network',
 			esc_html__( 'Hide Total Comments', 'admin2020' ),
 			array($this,'admin2020_overview_total_comments_render'),
 			'admin2020_overview_settings_network',
 			'admin2020_pluginPage_section_network_overview'
 		);
		////RECENT COMMENTS
 	 	add_settings_field(
 			'admin2020_overview_recent_comments_network',
 			esc_html__( 'Hide Recent Comments', 'admin2020' ),
 			array($this,'admin2020_overview_recent_comments_render'),
 			'admin2020_overview_settings_network',
 			'admin2020_pluginPage_section_network_overview'
 		);
		////COMMENTS LAST 7 DAYS
 	 	add_settings_field(
 			'admin2020_overview_total_recent_comments_network',
 			esc_html__( 'Hide total comments in the last 7 days', 'admin2020' ),
 			array($this,'admin2020_overview_total_recent_comments_render'),
 			'admin2020_overview_settings_network',
 			'admin2020_pluginPage_section_network_overview'
 		);
		////COMMENTS MOST COMMENTED
 	 	add_settings_field(
 			'admin2020_overview_most_commented_network',
 			esc_html__( 'Hide most commented posts', 'admin2020' ),
 			array($this,'admin2020_overview_most_commented_render'),
 			'admin2020_overview_settings_network',
 			'admin2020_pluginPage_section_network_overview'
 		);
		////NEW USERS
 	 	add_settings_field(
 			'admin2020_overview_new_users_network',
 			esc_html__( 'Hide new users', 'admin2020' ),
 			array($this,'admin2020_overview_new_users_render'),
 			'admin2020_overview_settings_network',
 			'admin2020_pluginPage_section_network_overview'
 		);
		////SYSTEM INFO
 	 	add_settings_field(
 			'admin2020_overview_system_info_network',
 			esc_html__( 'Hide system info', 'admin2020' ),
 			array($this,'admin2020_overview_system_info_render'),
 			'admin2020_overview_settings_network',
 			'admin2020_pluginPage_section_network_overview'
 		);
		////WIDGETS
 	 	add_settings_field(
 			'admin2020_overview_widgets_network',
 			esc_html__( 'Add widgets to overview page', 'admin2020' ),
 			array($this,'admin2020_overview_widgets_render'),
 			'admin2020_overview_settings_network',
 			'admin2020_pluginPage_section_network_overview'
 		);




		////////////////////
		////CONTENT SECTION
		////////////////////
		register_setting( 'admin2020_content_settings_network', 'admin2020_network_settings' );



		add_settings_section(
			'admin2020_pluginPage_section_network_content',
			"",
			"",
			'admin2020_content_settings_network'
		);

		add_settings_field(
			'admin2020_overiew_page_breaker_network',
			'<h4 class="">'.esc_html__( 'Content Page', 'admin2020' )."</h4>",
			array($this,'admin2020_overiew_breaker_render'),
			'admin2020_content_settings_network',
			'admin2020_pluginPage_section_network_content'
		);
		////DISABLE CONTENT PAGE
		add_settings_field(
		 'admin2020_content_show_content_network',
		 esc_html__( 'Disable Content Page?', 'admin2020' ),
		 array($this,'admin2020_content_show_content_render'),
		 'admin2020_content_settings_network',
		 'admin2020_pluginPage_section_network_content'
	 );

	 ////CONTENT TO SHOW ON CONTENT PAGE
	 add_settings_field(
		'admin2020_content_included_posts_network',
		esc_html__( 'Post types to include?', 'admin2020' ),
		array($this,'admin2020_content_included_posts_render'),
		'admin2020_content_settings_network',
		'admin2020_pluginPage_section_network_content'
	);



		////////////////////
		////CUSTOM CODE SECTION
		////////////////////

		register_setting( 'admin2020_advanced_settings_network', 'admin2020_network_settings' );

		add_settings_section(
			'admin2020_pluginPage_section_network_advanced',
			'',
			'',
			'admin2020_advanced_settings_network'
		);

		add_settings_field(
			'admin2020_overiew_page_breaker_network',
			'<h4 class="">'.esc_html__( 'Advanced', 'admin2020' )."</h4>",
			array($this,'admin2020_overview_breaker_render'),
			'admin2020_advanced_settings_network',
			'admin2020_pluginPage_section_network_advanced'
		);
		////CUSTOM CSS
		add_settings_field(
		 'admin2020_custom_css_network',
		 esc_html__( 'Custom CSS', 'admin2020' ),
		 array($this,'admin2020_custom_css_render'),
		 'admin2020_advanced_settings_network',
		 'admin2020_pluginPage_section_network_advanced'
	 );
	 ////CUSTOM JS
	 add_settings_field(
		'admin2020_custom_js_network',
		esc_html__( 'Custom JS', 'admin2020' ),
		array($this,'admin2020_custom_js_render'),
		'admin2020_advanced_settings_network',
		'admin2020_pluginPage_section_network_advanced'
	);




	}



	public function admin2020_licence_key_render(  ) {


		if (!current_user_can('administrator')){
			return;
		}

		$options = get_option( 'admin2020_network_settings' );

		if (isset($options['admin2020_pluginPage_licence_key_network'])){
			$value = $options['admin2020_pluginPage_licence_key_network'];
		} else {
			$value = "";
		}

		?>
		<input type='password' style="width:100%;margin-bottom:15px;" name='admin2020_network_settings[admin2020_pluginPage_licence_key_network]' placeholder="xxxx-xxxx-xxxx-xxxx" value="<?php echo $value?>">
		<?php

	}

	public function admin2020_overiew_breaker_render(  ) {

	}


	public function admin2020_network_override_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_network_override'])){
			$value = $options['admin2020_network_override'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_network_override]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>
		<span uk-icon="info"></span>
		<p uk-dropdown class="uk-text-meta uk-width-medium"><?php _e('If this is enabled, all settings applied here will apply to all sub sites.','admin2020') ?></p>
		<?php

	}

	public function admin2020_image_field_0_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_image_field_0_network'])){
			$value = $options['admin2020_image_field_0_network'];
		} else {
			$value = "";
		}
		?>
		<div class="ma-admin-backend-logo-holder">
		<img src="<?php echo $value ?>" class="ma-admin-backend-logo" >
		</div>
	  <input id="background_image" type="text" name="admin2020_network_settings[admin2020_image_field_0_network]" value="<?php echo $value ?>" hidden/>
	  <input id="upload_image_button" type="button" class="button-primary" value="Insert Image" hidden />

		<?php

	}


	public function admin2020_image_field_dark_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_image_field_dark_network'])){
			$value = $options['admin2020_image_field_dark_network'];
		} else {
			$value = "";
		}
		?>
		<div class="ma-admin-backend-logo-holder">
		<img src="<?php echo $value ?>" class="ma-admin-backend-logo-dark" style="width: 100px;min-width: 100px;min-height: 40px;">
		</div>
		<input id="background_image_dark" type="text" name="admin2020_network_settings[admin2020_image_field_dark_network]" value="<?php echo $value ?>" hidden/>
		<input id="upload_image_button" type="button" class="button-primary" value="Insert Image" hidden />
		<span uk-icon="info"></span>
	  <p uk-dropdown class="uk-text-meta uk-width-medium"><?php _e("If no logo is set for dark mode, then it will fall back to the main logo.","admin2020") ?></p>

		<?php

	}

	public function admin2020_login_background_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_login_background_network'])){
			$value = $options['admin2020_login_background_network'];
		} else {
			$value = "";
		}
		?>
		<div class="ma-admin-backend-logo-holder">
		<img src="<?php echo $value ?>" class="ma-admin-backend-background-image" style="width: 100px;min-width: 100px;min-height: 40px;">
		</div>
		<input id="login_background_image" type="text" name="admin2020_network_settings[admin2020_login_background_network]" value="<?php echo $value ?>" hidden/>
		<input id="login_upload_image_button" type="button" class="button-primary" value="Insert Image" hidden />
		<p style="position: relative;width: 100%;float: left;"><button class="uk-button uk-button-link" type="button" style="float:left;" onclick="jQuery('.ma-admin-backend-background-image').attr('src','');jQuery('#login_background_image').val('');"><?php _e('Remove Background Image')?></button></p>

		<?php

	}





	public function admin2020_disablestyles_field_2_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_disablestyles_field_2_network'])){
			$value = $options['admin2020_disablestyles_field_2_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_disablestyles_field_2_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>
		<span uk-icon="info"></span>
		<p uk-dropdown class="uk-text-meta uk-width-medium"><?php _e('If Admin 2020 is causing issues on other plugin option pages you can choose to disable Admin 2020 styles on these pages. This can help with visibility issues that may arrise.','admin2020') ?></p>


	  <?php

	}

	public function admin2020_loadfrontend_field_2_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_loadfrontend_field_2_network'])){
			$value = $options['admin2020_loadfrontend_field_2_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_loadfrontend_field_2_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

	  <span uk-icon="info"></span>
	  <p uk-dropdown class="uk-text-meta uk-width-medium"><?php _e('Enabling this may cause issues with your current theme.','admin2020')?></P>
	  <?php

	}

	public function admin2020_show_quick_links_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_show_quick_links_network'])){
			$value = $options['admin2020_show_quick_links_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_show_quick_links_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_flyout_website_link_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_flyout_website_link_network'])){
			$value = $options['admin2020_flyout_website_link_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_flyout_website_link_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_flyout_update_link_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_flyout_update_link_network'])){
			$value = $options['admin2020_flyout_update_link_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_flyout_update_link_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_flyout_profile_link_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_flyout_profile_link_network'])){
			$value = $options['admin2020_flyout_profile_link_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_flyout_profile_link_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_flyout_darkmode_link_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_flyout_darkmode_link_network'])){
			$value = $options['admin2020_flyout_darkmode_link_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_flyout_darkmode_link_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_admin2020_loader_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_admin2020_loader_network'])){
			$value = $options['admin2020_admin2020_loader_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_admin2020_loader_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_total_posts_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_total_posts_network'])){
			$value = $options['admin2020_overview_total_posts_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overview_total_posts_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_total_pages_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_total_pages_network'])){
			$value = $options['admin2020_overview_total_pages_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overview_total_pages_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_total_comments_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_total_comments_network'])){
			$value = $options['admin2020_overview_total_comments_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overview_total_comments_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_recent_comments_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_recent_comments_network'])){
			$value = $options['admin2020_overview_recent_comments_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overview_recent_comments_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_total_recent_comments_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_total_recent_comments_network'])){
			$value = $options['admin2020_overview_total_recent_comments_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overview_total_recent_comments_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_most_commented_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_most_commented_network'])){
			$value = $options['admin2020_overview_most_commented_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overview_most_commented_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_new_users_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_new_users_network'])){
			$value = $options['admin2020_overview_new_users_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overview_new_users_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_system_info_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_system_info_network'])){
			$value = $options['admin2020_overview_system_info_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overview_system_info_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overview_widgets_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overview_widgets_network'])){
			$value = $options['admin2020_overview_widgets_network'];
		} else {
			$value = array();
		}


		$widgets = array_keys( $GLOBALS['wp_widget_factory']->widgets );
		?>

		<button class="uk-button uk-button-default" type="button"><?php echo count($value)." ".__("Selected","admin2020") ?></button>
		<div uk-dropdown="mode: click">
			<select class="uk-select admin2020_select_multiple" name='admin2020_network_settings[admin2020_overview_widgets_network][]' multiple='multiple' style="max-height: 300px;overflow: scroll;">
				<?php
				foreach($widgets as $widget){

					$shortname = $widget;
					$withoutunderscores = str_replace("_"," ",$shortname);

					$selected = "";
					if (in_array($shortname,$value)){
						$selected = "selected";
					}

					?><option <?php echo $selected ?> value="<?php echo $shortname?>"><?php echo $withoutunderscores?></option><?php

				} ?>
	    </select>
		</div>

		<?php

	}

	public function admin2020_content_show_content_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_content_show_content_network'])){
			$value = $options['admin2020_content_show_content_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_content_show_content_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_content_included_posts_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_content_included_posts_network'])){
			$value = $options['admin2020_content_included_posts_network'];
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
			<select class="uk-select admin2020_select_multiple" name='admin2020_network_settings[admin2020_content_included_posts_network][]' multiple='multiple'>
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

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_search_included_posts_network'])){
			$value = $options['admin2020_search_included_posts_network'];
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
			<select class="uk-select admin2020_select_multiple" name='admin2020_network_settings[admin2020_search_included_posts_network][]' multiple='multiple'>
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

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_disable_admin2020_by_user_network'])){
			$value = $options['admin2020_disable_admin2020_by_user_network'];
		} else {
			$value = array();
		}

		$roles = $this->get_editable_roles();

		?>
		<button class="uk-button uk-button-default" type="button"><?php echo count($value)." ".__("Selected","admin2020") ?></button>
		<div uk-dropdown="mode: click">
			<select class="uk-select admin2020_select_multiple" style="overflow:auto" name='admin2020_network_settings[admin2020_disable_admin2020_by_user_network][]' multiple='multiple'>
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

	public function admin2020_edit_admin2020_by_user_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_edit_admin2020_by_user_network'])){
			$value = $options['admin2020_edit_admin2020_by_user_network'];
		} else {
			$value = array("Administrator");
		}

		$roles = $this->get_editable_roles();
		?>
		<button class="uk-button uk-button-default" type="button"><?php echo count($value)." ".__("Selected","admin2020") ?></button>
		<div uk-dropdown="mode: click">
			<select class="uk-select admin2020_select_multiple" style="overflow:auto" name='admin2020_network_settings[admin2020_edit_admin2020_by_user_network][]' multiple='multiple'>
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

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_disable_overview_network'])){
			$value = $options['admin2020_disable_overview_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_disable_overview_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_disable_menu_search_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_disable_menu_search_network'])){
			$value = $options['admin2020_disable_menu_search_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_disable_menu_search_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>
		<?php

	}

	public function admin2020_overiew_homepage_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overiew_homepage_network'])){
			$value = $options['admin2020_overiew_homepage_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overiew_homepage_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}


	public function admin2020_overiew_media_gallery_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overiew_media_gallery_network'])){
			$value = $options['admin2020_overiew_media_gallery_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overiew_media_gallery_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overiew_primary_color_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overiew_primary_color_network'])){
			$value = $options['admin2020_overiew_primary_color_network'];
		} else {
			$value = "";
		}
		?>
		<input type='text' class="colorpicker" name='admin2020_network_settings[admin2020_overiew_primary_color_network]' value="<?php echo $value; ?>" >
		<script>
		jQuery(document).ready(function($){
		    $('.colorpicker').wpColorPicker();
		});
		</script>
		<?php

	}

	public function admin2020_appearance_theme_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_appearance_theme_network'])){
			$value = $options['admin2020_appearance_theme_network'];
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

		<select class="uk-select" name='admin2020_network_settings[admin2020_appearance_theme_network]'>
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

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overiew_screen_options_network'])){
			$value = $options['admin2020_overiew_screen_options_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overiew_screen_options_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_overiew_quick_actions_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_overiew_quick_actions_network'])){
			$value = $options['admin2020_overiew_quick_actions_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_overiew_quick_actions_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_adminbar_disable_search_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_adminbar_disable_search_network'])){
			$value = $options['admin2020_adminbar_disable_search_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_adminbar_disable_search_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_disable_login_styles_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_disable_login_styles_network'])){
			$value = $options['admin2020_disable_login_styles_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_disable_login_styles_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_disable_admin_notices_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_disable_admin_notices_network'])){
			$value = $options['admin2020_disable_admin_notices_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_disable_admin_notices_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

		<?php

	}

	public function admin2020_disable_quick_actions_render(  ) {

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_disable_quick_actions_network'])){
			$value = $options['admin2020_disable_quick_actions_network'];
		} else {
			$value = 0;
		}
		?>
		<label class="admin2020_switch">
				<input type='checkbox' name='admin2020_network_settings[admin2020_disable_quick_actions_network]' <?php checked( $value, 1 ); ?> value='1'>
				<span class="admin2020_slider"></span>
		</label>

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

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_custom_css_network'])){
			$value = $options['admin2020_custom_css_network'];
		} else {
			$value = "/* custom css here */";
		}
		?>
		<p class="uk-text-meta"><?php _e("To target styles only in dark mode, use class","admin2020")?> <code>ma-admin-dark</code> <?php _e("which is applied to the body.","admin2020") ?></p>
		<textarea id="cssholder" name='admin2020_network_settings[admin2020_custom_css_network]' style="display:none;"><?php echo $value?></textarea>
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

		$options = get_option( 'admin2020_network_settings' );
		if (isset($options['admin2020_custom_js_network'])){
			$value = $options['admin2020_custom_js_network'];
		} else {
			$value = "//custom js here";
		}
		?>
		<textarea id="jsholder" name='admin2020_network_settings[admin2020_custom_js_network]' style="display:none;"><?php echo $value?></textarea>
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

	public function admin2020_pluginPage_section_network_overview_callback() {



		_e( 'Change what displays on the overview page', 'admin2020' );

	}

	public function admin2020_pluginPage_section_network_content_callback() {



		_e( 'Content page settings', 'admin2020' );

	}

	public function admin2020_settings_section_google_callback() {



		_e( 'Here you can add your google details for dashboard reports', 'admin2020' );
		?>
		<p><?php _e('For instructions of how to get the below details, please see',"admin2020")?> <a href="https://admintwentytwenty.com/blog/activating-google-analytics-in-admin-2020" target="_blank" class="uk-link"><?php _e('here', 'admin2020')?></a></p>
		<?php

	}

	public function admin2020_pluginPage_section_network_advanced_callback() {



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
				<h2  style="float:left;margin-top:0;">Admin 2020</h2>

				<div class="admin2020topbutton" style="float:right;">
					<div class="js-upload" uk-form-custom>
					    <input type="file" single id="admin2020_export_settings" onchange="admin2020_import_settings_network()">
					    <button class="uk-button uk-button-primary" type="button" tabindex="-1">Import</button>
					</div>
					<button class="uk-button uk-button-primary" onclick="admin2020_export_settings_json_network()" type="button">Export</button>
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
											<li><a href="#" id="trigger_code_editors"><?php _e('Advanced','admin2020')?></a></li>
	                </ul>
	            </div>
	            <div class="uk-width-expand" id="admin2020_settings_area">
	                <ul id="component-tab-left" class="uk-switcher">

										<li>
											<?php
											settings_fields( 'admin2020_general_settings_network' );
											do_settings_sections( 'admin2020_general_settings_network' );
											?>
										</li>

										<li>
											<?php
											settings_fields( 'admin2020_appearance_settings_network' );
											do_settings_sections( 'admin2020_appearance_settings_network' );
											?>
										</li>

										<li>
											<?php
											settings_fields( 'admin2020_overview_settings_network' );
											do_settings_sections( 'admin2020_overview_settings_network' );
											?>
										</li>

										<li>
											<?php
											settings_fields( 'admin2020_content_settings_network' );
											do_settings_sections( 'admin2020_content_settings_network' );
											?>
										</li>

										<li>
											<?php
											settings_fields( 'admin2020_advanced_settings_network' );
											do_settings_sections( 'admin2020_advanced_settings_network' );
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
