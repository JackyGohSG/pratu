<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Admin_Bar{



  public function build(){

    add_action('admin_init', array( $this, 'register_actions' ),0);

  }

  public function register_actions(){

    $utils = new Admin2020_Util();
    if($utils->deactivate_admin_on_page()){
      return;
    }

    add_action('admin_head', array( $this, 'render_gutenberg_logo' ),0);
    add_action('admin_head', array( $this, 'admin_2020_bar_rebuild' ),9999);

  }




  public function render_gutenberg_logo(){

    $logo = $this->ma_admin_get_logo();

    $userid = get_current_user_id();
    $darkmode = get_user_meta($userid, 'darkmode', true);

    if ($darkmode == 'true'){
      $logo = $this->ma_admin_get_logo_dark();
    }
    ?>
    <style>
    .edit-post-fullscreen-mode-close{
      background-color: none !important;
      background-image: url('<?php echo $logo?>') !important;
    }
    .edit-post-fullscreen-mode-close:hover{
      background-color: none !important;
      background-image: url('<?php echo $logo?>') !important;
      background-repeat: no-repeat !important;
      background-position: center !important;
      background-size: 80% !important;
    }
    .edit-post-fullscreen-mode-close.has-icon:hover{
      background-color: none !important;
      background-image: url('<?php echo $logo?>') !important;
      background-repeat: no-repeat !important;
      background-position: center !important;
      background-size: 80% !important;
    }

    </style>
    <?php
  }
///REBUILD ADMIN MENU AND BAR
  public function admin_2020_bar_rebuild() {

      if (!is_admin_bar_showing()){
        return;
      }

      $utils = new Admin2020_Util();
      if($utils->check_for_disarm()){
        return;
      }


      global $wp_admin_bar;
      //wp_admin_bar_render();
      //print_r(get_plugins());

      ///GET USER DETAILS
      $current_user = wp_get_current_user();
      $userid = get_current_user_id();
      $user_info = get_userdata($userid);
      $first_name = $user_info->first_name;

      $adminurl = get_admin_url();
      $homeurl = $adminurl;

      $options = get_option( 'admin2020_settings' );
      if (isset($options['admin2020_overiew_homepage'])){
        if (isset($options['admin2020_disable_overview'])){
            //OVERVIEW IS DISABLED
        } else {
          $homeurl = $adminurl.'admin.php?page=admin_2020_dashboard';
        }
      }

      ///GET POST TYPES AND CATEGORIES FOR SEARCH
      $args = array('public' => true,);
      $posttypes = get_post_types($args);
      $categories = get_categories();


      if (is_super_admin() && is_admin()){
        ////GET UPDATES
        $pluginupdates = get_plugin_updates();
        $themeupdates = get_theme_updates();
        $wordpressupdates = get_core_updates();

        if(isset($wordpressupdates[0])){
          $wpversion =  $wordpressupdates[0]->version;
          global $wp_version;

          if ($wpversion > $wp_version){
            $wordpressupdates = 1;
          } else {
            $wordpressupdates = 0;
          }
        } else {
          $wordpressupdates = 0;
        }

        $totalupdates = count($pluginupdates) + count($themeupdates) + $wordpressupdates;

      }



      /// IF NO NAME SET USE USERNAME
      if (!$first_name) {
          $first_name = $user_info->user_login;
      }

      /// GET WELCOME GREETING
      $greeting = $this->ma_admin_get_welcome();


      /// GET ADMIN LOGO
      $logo = $utils->get_logo();
      $logodark = $utils->get_dark_logo();

      ///CHECK FOR SEARCH DISABLE
      $utils = new Admin2020_Util();
      $searchenabled_check = $utils->get_option('admin2020_adminbar_disable_search');

  		if ($searchenabled_check){
  			$searchenabled = false;
  		} else {
  			$searchenabled = true;
  		}


      /// START MENU BUILD
      ob_start();
  ?>
    <!-- BUILD ADMIN BAR -->
  	<div uk-sticky="sel-target: .ma-admin-bar;" id="">
  	<nav class="uk-navbar-container uk-navbar-transparent uk-background-default ma-admin-bar uk-padding uk-padding-remove-vertical" id="" uk-navbar>

  	    <div class="uk-navbar-left">
  	        <ul class="uk-navbar-nav">
  	            <li class="uk-active uk-visible@m">
  	            <a href="<?php echo $homeurl ?>" class="uk-padding-remove-horizontal ma-admin-site-logo">
                  <img alt="Site Logo" class="light" src="<?php echo $logo ?>">
                  <img alt="Site Logo" class="dark" src="<?php echo $logodark ?>">
                </a>
  	            </li>

                <li class="uk-hidden@m">
                  <a href="#" uk-icon="icon: list" class="uk-padding-remove-horizontal" uk-toggle="target: #adminmenumain; animation: uk-animation-slide-top;cls: ma-admin-menu-visible" ></a>
                </li>
  	        </ul>
  	    </div>

  	    <div class="uk-navbar-right">

  	        <ul class="uk-navbar-nav">

              <?php
              $utils = new Admin2020_Util();
              $quick_links = $utils->get_option('admin2020_show_quick_links');

          		if (!$quick_links){
                ?>
                <div class="admin2020_legacy_admin">
                  <?php echo wp_admin_bar_render()?>
                </div>
                <?php
              }

              if ($searchenabled){ ?>
  		        <div>
  		            <a class="uk-navbar-toggle" uk-search-icon href="#" uk-toggle="target: .ma-admin-search-results; animation: uk-animation-slide-top"></a>
  		        </div>
            <?php } ?>


              <?php

              $quickactions = $utils->get_option('admin2020_overiew_quick_actions');

          		if ($quickactions == ""){
          			?>
                <li>
                  <a href="#">
                    <span class="" uk-icon="icon: bolt"></span>

                        <div uk-dropdown="pos: bottom-left;" style="max-height: 600px;overflow: auto;padding:0 !important;z-index:1000;">
                        <div class="uk-nav uk-dropdown-nav admin2020_quick_actions uk-text-right">
                          <div class="uk-padding-small"><a href="#" class="uk-link-muted" id="toggleAdmin2020"><?php _e("Toggle Admin 2020 Style","admin2020")?></a></div>
                          <div class="uk-padding-small" style="padding-top:0;"><a href="<?php echo get_home_url() ?>" class="uk-link-muted" id="toggleAdmin2020"><?php _e("View Website","admin2020")?></a></div>

                        </div>
                    </div>

                  </a>
                </li>
                <?php
          		}
               ?>


              <?php
              $screenoptions = $utils->get_option('admin2020_overiew_screen_options');

              if ($screenoptions){
                ?>
                <li><a href="#" id="maAdminToggleScreenOptions"><span class="" uk-icon="icon: cog"></span></a></li>
                <?php
              }

              ?>




	            <li class="uk-active" uk-toggle="target: #offcanvas-user-menu" style="position:relative">
  	            <a href="#" class="ma-admin-profile-img">
                  <div style="position:relative;">
                    <img src="<?php echo get_avatar_url($userid); ?>">
                    <?php
                    if (is_super_admin() && !$utils->get_option('admin2020_flyout_update_link')){
                        if ($totalupdates > 0){?>
                        <span class="uk-badge uk-position-top-right-out admin2020notificationBadge" style="left: 17px;top: -5px;background:#f0506e;color:#fff"><?php echo $totalupdates?></span>
                      <?php }
                    }?>
                  </div>
                </a>
	            </li>

  	        </ul>

  	    </div>

  	</nav>
    <?php

    $loader_enabled = $utils->get_option('admin2020_admin2020_loader');
		if ($loader_enabled == ""){

      ?>
      <div class="admin2020loaderwrap" id="admin2020siteloader">
        <div class="admin2020loader"></div>
      </div>
      <?php

    }
    ?>

  	</div>
    <!-- END OF ADMIN BAR -->

    <!-- BUILD SEARCH DROP -->
  	<div class="uk-padding-large uk-background-default ma-admin-search-results" aria-hidden="true" hidden>

  		<div class="uk-padding uk-position-top-right">
  		<button class="uk-close-large" type="button" uk-close uk-toggle="target: .ma-admin-search-results; animation: uk-animation-slide-bottom"></button>
  		</div>

  		<div class="uk-grid uk-grid-large">

  			<div class="uk-width-1-3@m uk-width-1-1@s">

  				<div class="uk-margin uk-grid-small uk-child-width-1-1 uk-grid" id="ma-admin-search-filters">
  					<h4 class="uk-margin"><span class="uk-margin-small-right" uk-icon="icon: search"></span><?php _e('Search:','admin2020')?></h4>
  					<div class="uk-margin-bottom">
  					    <form class="uk-search uk-search-default uk-width-1-1">
  					        <span uk-search-icon></span>
  					        <input class="uk-search-input" type="search" placeholder="<?php _e('Search...','admin2020')?>" id="ma-admin-search" autofocus>
  					    </form>
  					</div>

  			         <h4 class="uk-margin-bottom"><span class="uk-margin-small-right" uk-icon="icon: settings"></span><?php _e('Filters:','admin2020')?></h4>

  			         <ul class="uk-list">
  				         <li class="uk-list-title"><?php _e('Post Types','admin2020')?></li>


  			         <?php

                 $utils = new Admin2020_Util();
                 $posttypes = $utils->get_option('admin2020_search_included_posts');

               		if ($posttypes == ""){
               			$posttypes = array("post","page");
               		}

                 foreach ($posttypes as $posttype) {
                   $uppercase = ucfirst($posttype);
                   ?>

  				         <li><label><input class="ma-admin-filter ma-admin-post-types" type="checkbox" id="" checked="" ma-admin-filter="<?php echo $posttype ?>"> <?php echo $uppercase ?></label></li>

  				         <?php
                 }
                 ?>
  			         </ul>

  			         <ul class="uk-list">
  				         <li class="uk-list-title uk-margin-top"><?php _e('Categories:','admin2020')?></li>


  			         <?php foreach ($categories as $cats) {
                   $uppercase = ucfirst($cats->name);
                   ?>

  				         <li><label><input class="ma-admin-filter ma-admin-categories" type="checkbox" id="" ma-admin-filter="<?php echo $cats->term_id ?>"> <?php echo $uppercase ?></label></li>

  				         <?php
                 }
                 ?>
  			         </ul>

  			         <div><button class="uk-button uk-button-default uk-margin-large-top ma-admin-apply-filters"><?php _e( 'Apply Filters','admin2020')?></button></div>
  				</div>

  			</div>

  			<div class="uk-width-2-3@m uk-width-1-1@s">
  				<div class="uk-text-meta" id="searchcount"></div>
  				<div id="admin_search_results"></div>
  			</div>

  		</div>

  	</div>
    <!-- END OF SEARCH DROP -->





    <!-- OFFCANVAS USER MENU -->
  	<div id="offcanvas-user-menu" uk-offcanvas="flip: true; overlay: true;">
  	    <div class="uk-offcanvas-bar">

  	        <button class="uk-offcanvas-close" type="button" uk-close></button>

  	        <h3 class="uk-margin-remove-top"><?=sprintf(__( '%s, %s', 'admin-2020' ), $greeting, $first_name)?></h3>


  	        <ul class="uk-nav uk-nav-default">


              <?php if (!$utils->get_option('admin2020_flyout_website_link')){?>
  				          <li><a href="<?php echo get_home_url() ?>"><span class="uk-margin-right" uk-icon="icon: link"></span><?php _e('View Website','admin2020')?></a></li>
  		              <li class="uk-nav-divider ma-admin-smaller-divider"></li>
              <?php } ?>

              <?php if (!$utils->get_option('admin2020_flyout_profile_link')){?>
  		              <li><a href="<?php echo get_edit_profile_url($userid) ?>"><span class="uk-margin-right" uk-icon="icon: user"></span><?php _e('View Profile','admin2020')?></a></li>
  		              <li><a href="<?php echo get_edit_profile_url($userid) ?>"><span class="uk-margin-right" uk-icon="icon: settings"></span><?php _e('Edit Profile','admin2020')?></a></li>
              <?php } ?>

              <?php if (!$utils->get_option('admin2020_flyout_darkmode_link')){?>

                <?php
                $userid = get_current_user_id();
                $darkmode = get_user_meta($userid, 'darkmode', true);
                ?>
  		              <li><a href="#" ><span class="uk-margin-right" uk-icon="icon: cog"></span><?php _e('Dark Mode','admin2020')?>
                      <label class="admin2020_switch uk-margin-left">
                        <input type="checkbox" id="maAdminSwitchDarkMode" <?php checked( $darkmode, 'true' ); ?>>
                        <span class="admin2020_slider constant_dark"></span>
                      </label>
                    </a></li>
              <?php }

              if (is_super_admin() && !$utils->get_option('admin2020_flyout_update_link')){
                   if ($totalupdates > 0){?>
                  <li class="uk-nav-divider ma-admin-smaller-divider"></li>
                  <li>
                    <a href="<?php echo $adminurl.'update-core.php'?>" id="" style="position:relative;">
                      <span class="uk-margin-right" uk-icon="icon: refresh"></span><?php _e('All Updates','admin2020')?>
                      <span class="uk-badge uk-position-center-right uk-text-primary" style="background:#f0506e"><?php echo $totalupdates?></span>
                    </a>
                  </li>

                  <?php if ($wordpressupdates > 0){?>
                  <li>
                    <a href="<?php echo $adminurl.'update-core.php'?>" id="" style="position:relative;">
                      <span class="uk-margin-right" uk-icon="icon: wordpress"></span><?php _e('WordPress','admin2020')?>
                      <span class="uk-badge uk-position-center-right uk-text-primary" style="background:#f0506e"><?php echo $wordpressupdates?></span>
                    </a>
                  </li>
                  <?php } ?>

                  <?php if (count($pluginupdates) > 0){?>
                  <li>
                    <a href="<?php echo $adminurl.'plugins.php'?>" id="" style="position:relative;">
                      <span class="uk-margin-right" uk-icon="icon: bolt"></span><?php _e('Plugins','admin2020')?>
                      <span class="uk-badge uk-position-center-right uk-text-primary" style="background:#f0506e"><?php echo count($pluginupdates)?></span>
                    </a>
                  </li>
                <?php } ?>

                  <?php if (count($themeupdates) > 0){?>
                  <li>
                    <a href="<?php echo $adminurl.'themes.php'?>" id="" style="position:relative;">
                      <span class="uk-margin-right" uk-icon="icon: paint-bucket"></span><?php _e('Themes','admin2020')?>
                      <span class="uk-badge uk-position-center-right uk-text-primary" style="background:#f0506e"><?php echo count($themeupdates)?></span>
                    </a>
                  </li>
                <?php }
              }?>

            <?php }?>

  		        <li class="uk-nav-divider ma-admin-smaller-divider"></li>
  		        <li><a href="<?php echo wp_logout_url() ?>"><span class="uk-margin-right" uk-icon="icon: sign-out"></span><?php _e('Logout','admin2020')?></a></li>
  		    </ul>

  	    </div>
  	</div>
    <!-- END OF OFFCANVAS USER MENU -->

    <!-- ECHO ADMIN MENU -->
  	<?php
      $utils = new Admin2020_Util();
      $quick_links = $utils->get_option('admin2020_show_quick_links');

  		if ($quick_links){
        $wp_admin_bar = ob_get_clean();
        echo $wp_admin_bar;
  		} else {
        echo ob_get_clean();
  		}

  }


  public function ma_admin_get_welcome(){
    $time = current_time("H");
    if ($time < "12") {
        $greeting = __("Good morning", "admin2020");
    } else
    if ($time >= "12" && $time < "17") {
        $greeting = __("Good afternoon", "admin2020");
    } else
    if ($time >= "17") {
        $greeting = __("Good evening", "admin2020");
    }
    return $greeting;
  }

  public function ma_admin_get_logo(){

    $utils = new Admin2020_Util();
    $logo = $utils->get_option('admin2020_image_field_0');

    if ($logo == ""){
      $logo = esc_url(plugins_url('/assets/img/LOGO-BLUE.png', __DIR__));
    }

    return $logo;
  }

  public function ma_admin_get_logo_dark(){

    $utils = new Admin2020_Util();
    $logo = $utils->get_option('admin2020_image_field_dark');

    if ($logo == ""){
      $logo = $this->ma_admin_get_logo();
    }

    return $logo;
  }

}
