<?php 

defined( 'ABSPATH' ) or die( 'No script kiddies please!' );

// set option for plugin update process
if ( !get_option( 'niteoCS_version' ) ) {
	update_option( 'niteoCS_version', CMP_VERSION );
}

if ( version_compare( get_option('niteoCS_version'), CMP_VERSION ) < 0 ) {

	$current_theme 	= get_option('niteoCS_theme', 'countdown');
	
	$premium_themes = $this->cmp_premium_themes_installed();

	// delete transients for theme updates, to ensure the updates for latest cmp versions runs again
	foreach ( $premium_themes as $theme_slug ) {
		delete_transient( $theme_slug.'_updatecheck' );
	}

	// add translation string
	if ( get_option('niteoCS_translation') ) {
		$translation = json_decode( get_option('niteoCS_translation') );

		if ( !isset( $translation[12] ) ) {
			$new_translation = array('id' => 12, 'string' => 'Subscribe', 'translation' => 'Subscribe' );
			array_push( $translation, $new_translation );
			update_option('niteoCS_translation', wp_json_encode( $translation ));
		}
	}

	// migrate google analytics options
	if ( get_option('niteoCS_analytics') && get_option('niteoCS_analytics') != '' ) {
		update_option('niteoCS_analytics_status', 'google');
	}

	// add social icons to social settings 
	if ( get_option('niteoCS_socialmedia') ) {
		$niteoCS_socialmedia = stripslashes( get_option('niteoCS_socialmedia') );
		$socialmedia = json_decode( $niteoCS_socialmedia, true );
		$update = false;

		// add soundcloud and phone social media in 2.2 update
		if ( !$this->niteo_in_array_r( 'soundcloud', $socialmedia, true ) ) {
			$soundcloud  = array(
				'name' 		=> 'soundcloud',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 17,
			);
			array_push( $socialmedia, $soundcloud );
			$update = true;
		}

		// add whatsapp and phone social media in 2.3 update
		if ( !$this->niteo_in_array_r( 'whatsapp', $socialmedia, true ) ) {
			$whatsapp  = array(
				'name' 		=> 'whatsapp',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 18,
			);
			array_push( $socialmedia, $whatsapp );

			$phone  = array(
				'name' 		=> 'phone',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 19,
			);
			array_push( $socialmedia, $phone );
			$update = true;
		}

		// add telegram social media in 2.6.6 update
		if ( !$this->niteo_in_array_r( 'telegram', $socialmedia, true ) ) {
			$telegram  = array(
				'name' 		=> 'telegram',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 20,
			);
			array_push( $socialmedia, $telegram );
			$update = true;
		}

		// add telegram social media in 2.8.7 update
		if ( !$this->niteo_in_array_r( 'xing', $socialmedia, true ) ) {
			$xing  = array(
				'name' 		=> 'xing',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 21,
			);
			array_push( $socialmedia, $xing );
			$update = true;
		}

		// add github social media in 3.1 update
		if ( !$this->niteo_in_array_r( 'github', $socialmedia, true ) ) {
			$github  = array(
				'name' 		=> 'github',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 22,
			);
			array_push( $socialmedia, $github );
			$update = true;
		}

		// add snapchat social media in 3.1 update
		if ( !$this->niteo_in_array_r( 'snapchat', $socialmedia, true ) ) {
			$snapchat  = array(
				'name' 		=> 'snapchat',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 23,
			);
			array_push( $socialmedia, $snapchat );
			$update = true;
		}

		// add spotify social media in 3.3.2 update
		if ( !$this->niteo_in_array_r( 'spotify', $socialmedia, true ) ) {
			$spotify  = array(
				'name' 		=> 'spotify',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 24,
			);
			array_push( $socialmedia, $spotify );
			$update = true;
		}
		// add Discord social media in 3.8.4 update
		if ( !$this->niteo_in_array_r( 'discord', $socialmedia, true ) ) {
			$discord  = array(
				'name' 		=> 'discord',
				'url' 		=> '',
				'active' 	=> '1',
				'hidden' 	=> '1',
				'order' 	=> 24,
			);
			array_push( $socialmedia, $discord );
			$update = true;
		}

		if ( $update == true ) {
			update_option('niteoCS_socialmedia', json_encode( $socialmedia) );
		}
	}

	// check for < 1.8 version where subscriber ID was not set
	if ( get_option('niteoCS_subscribers_list') ) {
		$subscribe_list = get_option('niteoCS_subscribers_list');

		if ( is_array( $subscribe_list ) && count( $subscribe_list ) > 0 && !array_key_exists ('id', $subscribe_list[0]) ) {
			$i = 1;
			foreach( $subscribe_list as &$sub ){
			    $sub['id'] = $i;
			    $sub = array('id' => $sub['id']) + $sub;
			    // check if ip address is set
			    if (!array_key_exists('ip_address', $sub)) {
			    	$sub['ip_address'] = 'nodata';
			    }
			    $i++;
			}
			update_option('niteoCS_subscribers_list', $subscribe_list);
		}
	}

	// migrate overlay color and opacity settings after update 2.8
	$overlay_color 		= get_option('niteoCS_overlay_color['.$current_theme.']');
	$overlay_opacity 	= get_option('niteoCS_overlay_opacity['.$current_theme.']');

	if ( $overlay_color ) {
		update_option( 'niteoCS_overlay['.$current_theme.']', 'color' );
		update_option( 'niteoCS_overlay['.$current_theme.'][color]', $overlay_color );
		delete_option('niteoCS_overlay_color['.$current_theme.']');
	}

	if ( $overlay_opacity ) {
		update_option( 'niteoCS_overlay['.$current_theme.'][opacity]', $overlay_opacity );
		delete_option('niteoCS_overlay_opacity['.$current_theme.']');
	}

	// 2.9.3 version - migrate logo per theme settings to only one settings for all themes
	$logo_type 			= get_option('niteoCS_logo_type['.$current_theme.']');
	$niteoCS_logo_id 	= get_option('niteoCS_logo_id['.$current_theme.']');
	$niteoCS_text_logo 	= get_option('niteoCS_text_logo['.$current_theme.']');

	if ( $logo_type ) {
		update_option( 'niteoCS_logo_type', $logo_type );
		delete_option('niteoCS_logo_type['.$current_theme.']');
	}

	if ( $niteoCS_logo_id ) {
		update_option( 'niteoCS_logo_id', $niteoCS_logo_id );
		delete_option('niteoCS_logo_id['.$current_theme.']');
	}

	if ( $niteoCS_text_logo ) {
		update_option( 'niteoCS_text_logo', $niteoCS_text_logo );
		delete_option('niteoCS_text_logo['.$current_theme.']');
	}

	// 2.9.5 update - migrate graphic background to indepenedent theme settings
	$settings = array( 'niteoCS_banner['.$current_theme.']', 'niteoCS_banner_id['.$current_theme.']', 'niteoCS_unsplash_feed['.$current_theme.']', 'niteoCS_unsplash_0['.$current_theme.']', 'niteoCS_unsplash_2['.$current_theme.']', 'niteoCS_unsplash_3['.$current_theme.']', 'niteoCS_unsplash_1['.$current_theme.']', 'niteoCS_banner_video['.$current_theme.']', 'niteoCS_youtube_url['.$current_theme.']', 'niteoCS_vimeo_url['.$current_theme.']', 'niteoCS_video_file_url['.$current_theme.']', 'niteoCS_video_thumb['.$current_theme.']', 'niteoCS_banner_pattern['.$current_theme.']', 'niteoCS_banner_pattern_custom['.$current_theme.']', 'niteoCS_banner_color['.$current_theme.']', 'niteoCS_gradient['.$current_theme.']', 'niteoCS_banner_gradient_one['.$current_theme.']', 'niteoCS_banner_gradient_two['.$current_theme.']', 'niteoCS_effect_blur['.$current_theme.']', 'niteoCS_overlay['.$current_theme.']', 'niteoCS_overlay['.$current_theme.'][color]', 'niteoCS_overlay['.$current_theme.'][gradient]', 'niteoCS_overlay['.$current_theme.'][gradient_one]', 'niteoCS_overlay['.$current_theme.'][gradient_two]', 'niteoCS_overlay['.$current_theme.'][opacity]', 'niteoCS_banner['.$current_theme.']', 'niteoCS_slider_count['.$current_theme.']', 'niteoCS_slider_effect['.$current_theme.']', 'niteoCS_slider_auto['.$current_theme.']', 'niteoCS_special_effect['.$current_theme.']', 'niteoCS_special_effect['.$current_theme.'][constellation][color]' );

	foreach ( $settings as $name ) {
		$value = get_option( $name );
		if ( $value !== false ) {
			$new_settings = str_replace('['.$current_theme.']', '', $name);
			update_option( $new_settings, $value );
			if ( $name != 'niteoCS_banner['.$current_theme.']' ) {
				delete_option( $name );
			}
		}	
	}

	// update CMP activation status from false to 0 after update 3.1.2
	if ( get_option('niteoCS_status') == false ) {
		update_option('niteoCS_status', '0');
	}


	$translation = json_decode( get_option('niteoCS_translation'), true );
	if ( $translation ) {
		$translation[0]['name'] = 'Counter Seconds Label';
		$translation[1]['name'] = 'Counter Minutes Label';
		$translation[2]['name'] = 'Counter Hours Label';
		$translation[3]['name'] = 'Counter Days Label';
		$translation[4]['name'] = 'Subscribe Form Placeholder';
		$translation[5]['name'] = 'Subscribe Response Duplicate';
		$translation[6]['name'] = 'Subscribe Response Not Valid';
		$translation[7]['name'] = 'Subscribe Response Thanks';
		$translation[8]['name'] = 'Subscribe Submit Button Label';
		$translation[9]['name'] = 'CMP Eclipse Theme: Scroll Text';
		$translation[10]['name'] = 'Subscribe Form First Name Placeholder';
		$translation[11]['name'] = 'Subscribe Form Last Name Placeholder';
		$translation[12]['name'] = 'Subscribe';
		update_option('niteoCS_translation', wp_json_encode( $translation ));
	}

	// delete unzip theme from cmp-premium-dir
	if ( file_exists( CMP_PREMIUM_THEMES_DIR ) ) {
		$files = glob(CMP_PREMIUM_THEMES_DIR . '/*');
		$premium_themes = array_column( $this->cmp_premium_themes(), 'name' );

		foreach( $files as $file ) {
			if ( is_file($file) ){
				unlink($file);
			} else {
				if ( !in_array(basename($file), $premium_themes)) {
					array_map('unlink', glob("$file/*.*"));
					rmdir($file);
				}
				
			}
		}
	}

	// bump version for next udpate check
	update_option( 'niteoCS_version', CMP_VERSION );
}