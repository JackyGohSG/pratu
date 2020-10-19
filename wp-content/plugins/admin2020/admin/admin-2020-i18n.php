<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_i18n {

	public function load_plugin_textdomain() {

		add_action( 'admin_init', array($this,'admin2020_lang_loader'),-999 );

	}

	public function admin2020_lang_loader(){

		load_plugin_textdomain(
			'admin2020',
			false,
			dirname( dirname( plugin_basename( __FILE__ ) ) ) . '/languages/'
		);


	}



}
