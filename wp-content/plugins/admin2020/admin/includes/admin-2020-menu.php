<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Menu{

  public function build(){

    add_action('admin_init', array( $this, 'register_actions' ),0);

  }



  public function register_actions(){

    $utils = new Admin2020_Util();
    if($utils->deactivate_admin_on_page()){
      return;
    }

    add_filter('parent_file', array( $this, 'admin_2020_rebbuild_menu'),999);
    add_action('adminmenu', array( $this, 'admin_2020_adminmenu' ));

  }


  public function admin_2020_rebbuild_menu() {



  		/// GET AND SET REQUIRED GLOBAL VARIABLES
      global $menu, $submenu, $pagenow, $maadminmenu, $wp, $maAdminMenuArray, $maAdminSubMenuArray;
  		$newmenu = array();
      $this->old_menu = $menu;
      $this->old_submenu = $submenu;

  		// GET CURRENT SCREEN
      $screen = get_current_screen();
      $screenid = $screen->parent_file;
      $maAdminMenuArray = $menu;
      $maAdminSubMenuArray = $submenu;

      $utils = new Admin2020_Util();
      if($utils->check_for_disarm()){
        return;
      }

  		/// LIST OF AVAILABLE MENU ICONS
      $icons = array('dashicons-dashboard' => 'grid',
      'dashicons-admin-post' => 'file-text',
      'dashicons-admin2020-media' => 'image',
      'dashicons-admin2020-content' => 'database',
      'dashicons-admin-media' => 'image',
      'dashicons-admin-page' => 'album',
      'dashicons-admin-comments' => 'comment',
      'dashicons-admin-appearance' => 'paint-bucket',
      'dashicons-admin-plugins' => 'bolt',
      'dashicons-admin-users' => 'users',
      'dashicons-admin-tools' => 'cog',
      'dashicons-admin2020-grid' => 'thumbnails',
      'dashicons-admin-settings' => 'settings');
  		///GET ADMIN LOGO
      $logo = $this->ma_admin_get_logo();

      ///check if search is disabled

      $utils = new Admin2020_Util();
      $searchdisabled = $utils->get_option('admin2020_disable_menu_search');

      if ($searchdisabled == ""){
        $searchdisabled = false;
      }


  		/// START OUTPUT BUFFERING
      ob_start();
  ?>
    <div class="uk-padding ma-admin-menu-wrap ">
      <ul class="uk-nav-default uk-nav-parent-icon ma-admin-main-menu" uk-nav id="ma-admin-menu-list">

    		<!-- ADD MENU SEARCH TO THE TOP OF MENU -->

        <?php if (!$searchdisabled){?>
    		<li id="ma-admin-searchtab">
    			<div class="uk-margin-small">
    					<div class="uk-inline">
    							<span class="uk-form-icon" uk-icon="icon: search" ></span>
    							<input class="ma-admin-shrink uk-input ma-menu-search" type="search" placeholder="<?php _e('Search Menu...','admin2020')?>" id="ma-admin-menu-search">
    					</div>
    			</div>
    		</li>
      <?php }?>

    	<?php
    		//START LOOP THROUGH GLOBAL MENU ITEMS
        $options = get_option( 'admin2020_settings' );
        $user = wp_get_current_user();
        $userroles = $user->roles;
        $absolutepath = ABSPATH . '/wp-admin'."/";
        $files = array_diff(scandir($absolutepath), array('.', '..'));

        if (is_multisite()){
          $pathtonetwork = ABSPATH . '/wp-admin'."/network/";
          $networkfiles = array_diff(scandir($pathtonetwork), array('.', '..'));
          $files = array_merge($files,$networkfiles);
        }


        /////SORT MENU
        $blankarray = array();
        $hold_array = array();
        $counter = 0;
        $no_order = true;

        foreach($menu as $item){

          if (isset($options['admin2020_menu_order_'.$item[2]])){
            $current_posis = $options['admin2020_menu_order_'.$item[2]];
            if($current_posis != ""){
              $item['order'] = $current_posis;
              $no_order = false;
            } else {
              $item['order'] = $counter;
            }
          } else {
            $item['order'] = $counter;
          }
          $counter = $counter + 1;
          array_push($blankarray,$item);

        }

        if($no_order == false){
          usort($blankarray, function($a, $b) {
          return $a['order'] <=> $b['order'];
          });
          $menu = $blankarray;
        }

        foreach ($menu as $item) {





            $hidden = 'false';
            $title = $item[0];

            if ($title) {
              foreach ($userroles as $role){

                $lcrole = strtolower($role);
                $lcrole = str_replace(" ","_",$lcrole);
                $lcparentname = strip_tags(strtolower($item[5]));
                $lcparentname = str_replace(" ","_",$lcparentname);

                if (isset($options['admin2020_menu_'.$lcrole.'_'.$lcparentname])){
                  if ($options['admin2020_menu_'.$lcrole.'_'.$lcparentname] == '1'){
                    $hidden = 'true';
                    break;
                  }
                }
              }
            }

            if ($hidden == 'true'){
              continue;
            }

            $utils = new Admin2020_Util();
            $disablemedia = $utils->get_option('admin2020_overiew_media_gallery');

            if ($disablemedia == "") {
              if (isset($item[6])){
                if ($item[6] == 'dashicons-admin-media'){
                  continue;
                }
              }
            }


            /////CHECK FOR RENAMEING
            if ($title) {

                $lcparentname = strip_tags(strtolower($item[5]));
                $lcparentname = str_replace(" ","_",$lcparentname);

                if (isset($options['admin2020_menu_rename_'.$lcparentname])){

                  if ($options['admin2020_menu_rename_'.$lcparentname] != ""){
                    $main_menu_name = $options['admin2020_menu_rename_'.$lcparentname];
                  } else {
                    $main_menu_name = $item[0];
                  }

                } else {
                  $main_menu_name = $item[0];
                }

            }


            $link = $item[2];
            if(isset($submenu[$link])){
              $subitems = $submenu[$link];
            } else {
              $subitems = array();
            }

    				/// CHECK IF DIVIDER / PARENT / SINGLE
            if (!$title) {

                $option_menu_string = 'admin2020_menu_rename_'.$item[2];
                $divider_name = $utils->get_option($option_menu_string);

                ///CHECK IF DISABLED
                $hide_divider = false;
                foreach ($userroles as $role){

                  $rolelowercase = strtolower($role);
                  $rolelowercase = str_replace(" ","_",$rolelowercase);

                  if (isset($options['admin2020_menu_'.$rolelowercase.'_'.$item[2]])){
                    $value = $options['admin2020_menu_'.$rolelowercase.'_'.$item[2]];
                    if($value){
                      $hide_divider =  true;
                      break;
                    }
                  }

                }

                if ($hide_divider){
                  continue;
                }

                if  ($divider_name !=  ""){
                  ?>
                  <li class="uk-nav-header"><?php echo $divider_name?></li>
                  <li class="uk-nav-divider show-on-shrink"></li>
                  <?php
                  continue;
                } else {
                  ?>
                  <li class="uk-nav-divider"></li>
                  <?php
                  continue;
                }

            } else {
                $theclass = 'uk-parent wp-has-submenu';
                if (count($subitems) < 1) {
                    $theclass = '';
                }
            }

    				// SET MENU ICON
            $theicon = '';
            $wpicon = $item[6];

            if(isset($icons[$wpicon])){
              $theicon = $icons[$wpicon];
              $icons[$wpicon] = "";
            }


            $option_icon_string = 'admin2020_icon_'.$lcparentname;
            $user_icon = $utils->get_option($option_icon_string);

            if($user_icon != ""){
              $theicon = $user_icon;
            }

            if ($theicon) {
                $theicon = '<span class="uk-margin-right ma-admin-option-icon" uk-icon="icon: ' . $theicon . '"></span>';
            }
            if (!$theicon && $title) {
                if (strpos($item[6], 'http') !== false || strpos($item[6], 'data:') !== false) {
                    $theicon = '<img class="uk-image uk-margin-right ma-admin-menu-icon ma-admin-option-icon" src="' . $item[6] . '">';
                } else {
                    $theicon = '<div class="wp-menu-image dashicons-before uk-margin-right ma-admin-option-icon '.$item[6].'"></div>';
                }
            }


    				///REPLACE COMMENT COUNT CLASS
            $title = str_replace('pending-count', 'uk-badge uk-align-right uk-text-center uk-margin-remove', $main_menu_name);


    				// GET CURRENT URL QUERY TO FIND ACTIVE PAGE
            $currentquery = $_SERVER['QUERY_STRING'];
            if ($currentquery) {
                $currentquery = '?' . $currentquery;
            }
            $wholestring = $pagenow . $currentquery;
            $visibility = 'hidden';
            $open = 'wp-not-current-submenu';
            $linkclass = '';

    				/// SET MENU LINKS


            if ($title && count($subitems) > 0) {


                foreach ($subitems as $sub) {
                    if (strpos($sub[2], '.php') !== false) {
                        $link = $sub[2];

                        $querypieces = explode("?", $link);
                        $temp = $querypieces[0];

                        if( !in_array( $temp ,$files )){
                            $link = 'admin.php?page=' . $sub[2];
                        }
                    } else {
                        $link = 'admin.php?page=' . $sub[2];
                    }

                    $linkclass = '';
                    if ($wholestring == $link && $theclass != 'uk-nav-divider') {
                        $linkclass = "wp-has-current-submenu wp-menu-open";
                        $open = 'uk-active uk-open wp-has-current-submenu';
                        $visibility = '';
                        break;
                    }
                }
            }


            $nosub = '#';
            if ($theclass === "") {
                $nosub = $item[2];
                if(strpos($nosub, 'https://') !== false || strpos($nosub, 'http://') !== false) {

                    $nosub = $nosub;

                } else if (strpos($nosub, '.php') !== false) {

                  $nosub = $item[2];

                  $querypieces = explode("?", $nosub);
                  $temp = $querypieces[0];

                  if( !in_array( $temp ,$files )){
                      $nosub = 'admin.php?page=' . $nosub;
                  }

                } else {
                    $nosub = 'admin.php?page='.$nosub;
                }
            }

            if (count($subitems) === 0 && $nosub === $wholestring) {
                $open = 'uk-active';
            }

    				/// ADJUST FOR LEGACY LINKS
            if(array_key_exists(5,$item)){
              $itemclasses = $item[5];
            } else {
              $itemclasses = '';
            }
            $correctlinks = str_replace("/", "-", $itemclasses);
            $correctlinks = str_replace("=", "-", $correctlinks);
            $correctlinks = str_replace("&", "-", $correctlinks);



            ?>


    					<!-- BUILD PARENT LI -->
    	        <li class="<?php echo $theclass . ' ' . $open . ' ' . $item[4] ?>" id="<?php echo $correctlinks ?>">
    	            <a href="<?php echo $nosub ?>" class="<?php echo $item[4].' '.$theclass.' '.$linkclass ?> ma-admin-shrink-viewer">
    		            <?php if ($theicon) {
    				            echo $theicon;
    				        }
                    ?>

    		            <span class="ma-admin-shrink wp-menu-name"><?php echo $title; ?></span>
    		        </a>

    		        <?php


    				///BUILD SUB MENU IF EXISTS
            if (count($subitems) > 0) {
    ?>

    	          <ul class="uk-nav-sub uk-margin-bottom wp-submenu wp-submenu-wrap" <?php echo $visibility ?>>

    		        <?php
                $count = 0;


                ///SORT ARRAY
                $blankarray = array();

                foreach($subitems as $item){

                  $itemname = strip_tags(strtolower($item[0]));
                  $itemname = str_replace(" ","_",$itemname);
                  $order_count = 0;

                  if (isset($options['admin2020_submenu_order_'.$lcparentname.$itemname])){
                    if($current_posis != ""){
                      $current_posis = $options['admin2020_submenu_order_'.$lcparentname.$itemname];
                      $item['order'] = $current_posis;
                    } else {
                      $item['order'] = $order_count;
                    }
                  } else {
                    $item['order'] = $order_count;
                  }
                  $order_count = $order_count + 1;

                  array_push($blankarray,$item);

                }

                usort($blankarray, function($a, $b) {
                return $a['order'] <=> $b['order'];
                });

                $subitems = $blankarray;

                foreach ($subitems as $sub) {


                    $hidden = 'false';
                    $title = $item[0];
                    $sub_menu_name = $sub[0];


                    ////CHECK FOR HIDDEN MENU ITEMS BY ROLE
                    if ($title) {
                      foreach ($userroles as $role){

                        $lcrole = strtolower($role);
                        $lcrole = str_replace(" ","_",$lcrole);
                        $itemname = strip_tags(strtolower($sub[0]));
                        $itemname = str_replace(" ","_",$itemname);

                        if (isset($options['admin2020_submenu_'.$lcrole.'_'.$lcparentname.$itemname])){
                          if ($options['admin2020_submenu_'.$lcrole.'_'.$lcparentname.$itemname] == '1'){
                            $hidden = 'true';
                            break;
                          }
                        }
                      }
                    }

                    if ($title) {

                        $itemname = strip_tags(strtolower($sub[0]));
                        $itemname = str_replace(" ","_",$itemname);

                        if (isset($options['admin2020_submenu_rename_'.$lcparentname.$itemname])){

                          if ($options['admin2020_submenu_rename_'.$lcparentname.$itemname] != ""){
                            $sub_menu_name = $options['admin2020_submenu_rename_'.$lcparentname.$itemname];
                          } else {
                            $sub_menu_name = $sub[0];
                          }

                        } else {
                           $sub_menu_name = $sub[0];
                        }

                    }
                    //echo $hidden;

                    if ($hidden == 'true'){
                      continue;
                    }


    								/// BUILD LINKS AND CLASSES
                    $firstitem = '';
                    if ($count == 0) {
                        $firstitem = 'wp-first-item';
                    }
                    $count = $count + 1;
                    if ($sub[0] == 'Background' ) {
                        continue;///HIDE TOP
                    }
                    $active = '';

                    if (strpos($sub[2], 'admin.php') !== false) {
                        $link = $sub[2];
                    } else if (strpos($sub[2], '.php') !== false) {
                        $link = $sub[2];
                        if (strpos($sub[2], '/') !== false) {
                            $pieces = explode("/", $sub[2]);
                            if (strpos($pieces[0], '.php') !== true || !file_exists(get_admin_url().$sub[2])) {
                                $link = 'admin.php?page=' . $sub[2];
                            }
                        }

                        $querypieces = explode("?", $link);
                        $temp = $querypieces[0];

                        if( !in_array( $temp ,$files )){
                            $link = 'admin.php?page=' . $sub[2];
                        }

                    }  else {
                        $link = 'admin.php?page=' . $sub[2];
                    }

                    if (strpos($sub[2], "/wp-content/") !== false) {
                      $link = 'admin.php?page=' . $sub[2];
                    }

                    //CHECK IF INTERNAL URL
                    if (strpos($sub[2], get_site_url()) !== false) {
                      $link = $sub[2];
                    }

                    ///CHECK IF EXTERNAL LINK
                    if(strpos($sub[2], 'https://') !== false || strpos($sub[2], 'http://') !== false) {
                      $link = $sub[2];
                    }

                    if ($wholestring == $link) {
                        $active = "uk-active current";
                    }
    								?>


    							<!-- SUBMENU ITEMS -->
    						 <li class="<?php echo $firstitem.' '.$active ?>">

    						 	<a href="<?php echo $link ?>"  class="<?php echo $firstitem.' '.$active ?>"><?php echo $sub_menu_name ?></a>

    						 </li>



    						 <?php
    					  } /// END OF SUBBMENU LOOP
    						?>

    	            </ul>

    	            <?php
            } /// END OF SUBBMENU COUNT IF?>

    	        </li>


    	    <?php
        }/// END OF MAIN MENU LOOP
    ?>

    		  <!-- ADD MENU SHRINK ICONS -->
          <li class="uk-position-bottom uk-padding ma-admin-shrink-wrap">
      		    <a href="#"  class="uk-align-left uk-margin-remove ma-admin-shrinker uk-visible@m">
                <span uk-icon="icon:shrink" style="float:left;"></span>
                <span class="ma-admin-shrink" style="margin-left:25px;"><?php _e("Collapse Menu","admin2020")?></span>
              </a>

      		    <a href="#" class="ma-admin-expander uk-visible@m">
                <span uk-icon="icon:expand" style="float:left;"></span>
                <span class="ma-admin-shrink"><?php _e("Expand Menu","admin2020")?></span>
              </a>
      	  </li>

        </ul>
      </div>
  		<!-- END OF MENU -->



      <?php
      $menu = array();
      //$submenu = array();
      $maadminmenu = ob_get_clean();
  }

  /// OUTPUT MAIN MENU
  public function admin_2020_adminmenu() {

      $utils = new Admin2020_Util();
      if($utils->check_for_disarm()){
        return;
      }

      global $maadminmenu,$menu,$submenu;
      echo $maadminmenu;
      $menu = $this->old_menu;
      $submenu = $this->old_submenu;
  }

  public function ma_admin_get_logo(){
    $options = get_option('admin2020_settings');
    if (isset($options['admin2020_image_field_0'])){
      $logo = $options['admin2020_image_field_0'];
    } else {
      $logo = esc_url(plugins_url('/assets/img/LOGO-BLUE.png', __DIR__));
    }
    if (!$logo) {
        $logo = esc_url(plugins_url('/assets/img/LOGO-BLUE.png', __DIR__));
    }
    return $logo;
  }




}
