<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Register_Components {

  private $version;

  public function __construct( $theversion, $productid ) {

    $this->version = $theversion;
    $this->productid = $productid;

  }

  public function load(){
    $options = get_option( 'admin2020_settings' );
		$value = $options['admin2020_pluginPage_licence_key'];
    $this->productkey = $value;
    $this->run_validation($value);

    add_filter('plugins_api', array($this,'checkforupdates'), 20, 3);
    add_filter('site_transient_update_plugins', array($this,'admin2020_push_update') );
    add_action( 'upgrader_process_complete', array($this,'admin2020_after_update'), 10, 2 );
  }



  public function run_validation($k){

    if(!get_transient( 'admin2020_components')){
            set_transient( 'admin2020_components', true, 12 * HOUR_IN_SECONDS );
            return;

    } else {
      return;
    }

  }

  public function new_notice($message){

    $this->message = $message;

    add_action('admin_notices', function($message){
      echo '<div class="notice notice-warning" style="display: block !important;visibility: visible !important;"><p>';
      echo $this->message;
      echo  '</p></div>';
    });

  }



/*
 * $res empty at this step
 * $action 'plugin_information'
 * $args stdClass Object ( [slug] => woocommerce [is_ssl] => [fields] => Array ( [banners] => 1 [reviews] => 1 [downloaded] => [active_installs] => 1 ) [per_page] => 24 [locale] => en_US )
 */
  public function checkforupdates( $res, $action, $args ){


  	// do nothing if this is not about getting plugin information
  	if( 'plugin_information' !== $action ) {
  		return false;
  	}

  	$plugin_slug = 'admin-2020'; // we are going to use it in many places in this function

  	// do nothing if it is not our plugin
  	if( $plugin_slug !== $args->slug ) {
  		return false;
  	}

  	// trying to get from cache first
  	if( false == $remote = get_transient( 'admin2020_update_' . $plugin_slug ) ) {


      if (!$this->productid || !$this->productkey){
        return;
      }


      $domain = get_home_url();

      $remote = wp_remote_get( 'https://admintwentytwenty.com/validate/update.php?id='.$this->productid.'&k='.$this->productkey.'&d='.$domain, array(
  			'timeout' => 10,
  			'headers' => array(
  				'Accept' => 'application/json'
  			) )
  		);


  		if ( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {
  			set_transient( 'admin2020_update_' . $plugin_slug, $remote, 43200 ); // 12 hours cache

        $remote = json_decode( $remote['body'] );
        $state = $remote->state;

        if ($state == "false"){
          return;
        }

  		}
  	}

  	if( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {

  		$remote = json_decode( $remote['body'] );


  		$res = new stdClass();

  		$res->name = $remote->name;
  		$res->slug = $plugin_slug;
  		$res->version = $remote->version;
  		$res->tested = $remote->tested;
  		$res->requires = $remote->requires;
  		$res->author = '<a href="https://admintwentytwenty.com">Admin 2020</a>';
  		$res->author_profile = 'https://admintwentytwenty.com';
  		$res->download_link = $remote->download_url;
  		$res->trunk = $remote->download_url;
  		$res->requires_php = '5.3';
  		$res->last_updated = $remote->last_updated;
  		$res->sections = array(
  			'description' => $remote->sections->description,
  			'installation' => $remote->sections->installation
  			// you can add your custom sections (tabs) here
  		);

  		// in case you want the screenshots tab, use the following HTML format for its content:
  		// <ol><li><a href="IMG_URL" target="_blank"><img src="IMG_URL" alt="CAPTION" /></a><p>CAPTION</p></li></ol>
  		if( !empty( $remote->sections->screenshots ) ) {
  			$res->sections['screenshots'] = $remote->sections->screenshots;
  		}

  		$res->banners = array(
  			'low' => $remote->banners->low,
  			'high' => $remote->banners->high
  		);
  		return $res;

  	}

  	return false;

  }




  function admin2020_push_update( $transient ){



  	if ( empty($transient->checked ) ) {
              return $transient;
    }

  	if( false == $remote = get_transient( 'update_admin-2020' ) ) {

      $domain = get_home_url();

      $remote = wp_remote_get( 'https://admintwentytwenty.com/validate/update.php?id='.$this->productid.'&k='.$this->productkey.'&d='.$domain, array(
  			'timeout' => 10,
  			'headers' => array(
  				'Accept' => 'application/json'
  			) )
  		);


  		if ( !is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && !empty( $remote['body'] ) ) {
  			set_transient( 'update_admin-2020', $remote, 43200 ); // 12 hours cache

  		}

  	}



  	if($remote && !is_wp_error( $remote ) && isset(json_decode( $remote['body'])->state)) {

  		$remote = json_decode( $remote['body']);

      //print_r($remote);

      $state = $remote->state;

      if ($state == "false"){
        return;
      }

      if (isset($remote->version)){

    		if( $remote && version_compare( $this->version, $remote->version, '<' )) {
    			$res = new stdClass();
    			$res->slug = 'admin-2020';
    			$res->plugin = 'admin-2020/admin-2020.php';
    			$res->new_version = $remote->version;
    			$res->tested = $remote->tested;
    			$res->package = $remote->download_url;
          $transient->response[$res->plugin] = $res;
        }

      }

  	}
    return $transient;
  }

  public function admin2020_after_update( $upgrader_object, $options ) {
  	if ( $options['action'] == 'update' && $options['type'] === 'plugin' )  {
  		delete_transient( 'update_admin-2020' );
  	}
  }




}
