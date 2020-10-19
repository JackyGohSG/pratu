<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin2020_Util{

  public function get_option($optionname){

    $options = get_option('admin2020_settings');
    $network_override = false;

    if(is_multisite()){
      $network_options = get_blog_option(get_main_network_id(), 'admin2020_network_settings');
      if(isset($network_options['admin2020_network_override'])){
        $network_override = true;
      } else {
        $network_override = false;
      }
    }

    if (is_network_admin()){

      if (isset($network_options[$optionname.'_network'])){
        $option = $network_options[$optionname.'_network'];
      } else {
        $option = "";
      }

    } else if ($network_override && isset($network_options[$optionname.'_network'])){

      if (isset($network_options[$optionname.'_network'])){
        $option = $network_options[$optionname.'_network'];
      } else {
        $option = "";
      }

    } else {

      if (isset($options[$optionname])){
        $option = $options[$optionname];
      } else {
        $option = "";
      }

    }

    return $option;

  }


  public function check_for_disarm(){

    $disabledroles = $this->get_option('admin2020_disable_admin2020_by_user');

    $status = false;

		if ($disabledroles != ""){

      $user = wp_get_current_user();
      foreach ($disabledroles as $role){

        if (in_array(strtolower($role), $user->roles)){
          $status = true;
        }

      }

		}

    return $status;

  }

  public function check_for_user_disarm(){

    $disabledroles = $this->get_option('admin2020_edit_admin2020_by_user');

    $status = false;

    if ($disabledroles != ""){

      $user = wp_get_current_user();
      foreach ($disabledroles as $role){

        if (in_array(strtolower($role), $user->roles)){
          $status = true;
        }

      }

    } else {
      $status = true;
    }

    if (is_super_admin()){
      $status = true;
    }

    return $status;

  }

  public function get_logo(){

    $logo = $this->get_option('admin2020_image_field_0');

    if ($logo == ""){
      $logo = esc_url(plugins_url('/assets/img/LOGO-BLUE.png', __DIR__));
    }

    return $logo;
  }

  public function get_dark_logo(){

    $logo = $this->get_option('admin2020_image_field_dark');

    if ($logo == ""){
      $logo = $this->get_logo();
    }

    return $logo;
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

    public function date_array($startdate,$enddate){

      $period = new DatePeriod(
           new DateTime($startdate),
           new DateInterval('P1D'),
           new DateTime($enddate)
      );

      $date_array = array();

      foreach ($period as $key => $value) {
          $the_date = $value->format('d/m/Y');
          array_push($date_array,$the_date);
      }

      return $date_array;

    }


    public function deactivate_admin_on_page() {
      global $pagenow, $menu, $submenu, $screen;

      if(!is_array($menu)){
        return;
      }

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

        if(!isset($item[5])){
          continue;
        }
  			$title = $item[5];
  			$options = get_option( 'admin2020_settings' );
  			$parent_hidden = false;

  			if ($title) {

  					$lcparentname = strip_tags(strtolower($title));
  					$lcparentname = str_replace(" ","_",$lcparentname);

  					$optionname = 'admin2020_disabled_'.$lcparentname;

  					if (isset($options[$optionname])){

  						if ($options[$optionname] == true){
  							array_push($restrictedpages,$item[2]);
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



  					$itemname = strip_tags(strtolower($sub[0]));
  					$itemname = str_replace(" ","_",$itemname);
  					$sub_option_name = 'admin2020_disabled_sub_'.$lcparentname.$itemname;

  					if($parent_hidden == true){

  						array_push($restrictedpages,$sub[2]);

  					} else {

  						if (isset($options[$sub_option_name])){
  							if ($options[$sub_option_name] == true){
  								array_push($restrictedpages,$sub[2]);
  							}
  						}

  					}


  			}////END OF SUBMENU LOOP


  		}/// END OF MENU LOOP


      if(in_array( $currentpage, $restrictedpages  ) || in_array( $page_id, $restrictedpages)) {
  			return true;
      } else {
        return false;
      }


  	}


    public function formatBytes($bytes, $precision = 2) {
        $units = array('B', 'KB', 'MB', 'GB', 'TB');

        $bytes = max($bytes, 0);
        $pow = floor(($bytes ? log($bytes) : 0) / log(1024));
        $pow = min($pow, count($units) - 1);

        // Uncomment one of the following alternatives
        $bytes /= pow(1024, $pow);
        // $bytes /= (1 << (10 * $pow));

        return round($bytes, $precision) . ' ' . $units[$pow];
    }

}
