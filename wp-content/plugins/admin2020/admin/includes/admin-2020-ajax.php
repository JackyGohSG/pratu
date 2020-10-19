<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Ajax{

  public function register(){

    add_action('wp_ajax_ma_admin_shrink_menu', array($this,'ma_admin_shrink_menu'));
    add_action('wp_ajax_ma_admin_switch_dark_mode', array($this,'ma_admin_switch_dark_mode'));
    add_action('wp_ajax_ma_admin_search_admin_posts', array($this,'ma_admin_search_admin_posts'));
    add_action('wp_ajax_admin2020_export_settings', array($this,'admin2020_export_settings'));
    add_action('wp_ajax_admin2020_import_settings', array($this,'admin2020_import_settings'));
    add_action('wp_ajax_admin2020_reset_menu_settings', array($this,'admin2020_reset_menu_settings'));

    add_action('wp_ajax_admin2020_set_user_prefs', array($this,'admin2020_set_user_prefs'));

    add_action('wp_ajax_admin2020_set_google_data', array($this,'admin2020_set_google_data'));

    add_action('wp_ajax_admin2020_export_settings_network', array($this,'admin2020_export_settings_network'));
    add_action('wp_ajax_admin2020_import_settings_network', array($this,'admin2020_import_settings_network'));



  }

  public function admin2020_set_user_prefs() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

          $pref_name = $_POST['pref_name'];
          $value = $_POST['value'];

          $userid = get_current_user_id();
          $current = get_user_meta($userid, 'admin2020_preferences', true);

          if(is_array($current)){
            $current[$pref_name] = $value;
          } else {
            $current = array();
            $current[$pref_name] = $value;
          }

          update_user_meta($userid, 'admin2020_preferences', $current);

      }
      die();
  }

  ////EXPORT SETTINGS
  public function admin2020_export_settings() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

          $options = get_option( 'admin2020_settings' );
          $formatted = json_encode($options);

          echo $formatted;

      }
      die();
  }

  public function admin2020_export_settings_network() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

          $options = get_option( 'admin2020_network_settings' );
          $formatted = json_encode($options);

          echo "$formatted";

      }
      die();
  }

  public function admin2020_import_settings() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

          $options = $_POST['admin2020_settings'];

          if(is_array($options)){
            update_option( 'admin2020_settings', $options);
          }

          echo __("Settings succesfully Imported","admin2020");

      }
      die();
  }

  public function admin2020_set_google_data() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

          $view = $_POST['view'];
          $token = $_POST['token'];

          $options = get_option( 'admin2020_settings' );

          $options['admin2020_analytics_token'] = $token;
          $options['admin2020_analytics_view'] = $view;

          update_option( 'admin2020_settings', $options);


          echo __("Account Connected!","admin2020");

      }
      die();
  }

  public function admin2020_import_settings_network() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

          $options = $_POST['admin2020_settings'];

          if(is_array($options)){
            update_option( 'admin2020_network_settings', $options);
          }

          echo __("Settings succesfully Imported","admin2020");

      }
      die();
  }
  // SAVE USER SETTING - MENU SHRINK
  public function ma_admin_shrink_menu() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {
          $userid = get_current_user_id();
          $current = get_user_meta($userid, 'ma-admin-switch', true);
          if ($current === 'true') {
              update_user_meta($userid, 'ma-admin-switch', 'false');
          } else {
              update_user_meta($userid, 'ma-admin-switch', 'true');
          }
          echo '';
      }
      die();
  }

  ///RESET MENU STYLES
  public function admin2020_reset_menu_settings() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {


          $options = get_option( 'admin2020_settings' );

          foreach($options as $key=>$value) {

            $thename = $key;
            $thevalue = $value;

            if (strpos($thename, 'menu') !== false || strpos($thename, 'submenu') !== false || strpos($thename, 'admin2020_disabled') !== false) {
              $options[$thename] = "";
            }

          }

          update_option( 'admin2020_settings', $options);
          echo $thename;
      }
      die();
  }


  // SAVE USER SETTING - DARK MODE
  public function ma_admin_switch_dark_mode() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {
          $userid = get_current_user_id();
          $current = get_user_meta($userid, 'darkmode', true);
          if ($current === 'true') {
              update_user_meta($userid, 'darkmode', 'false');
          } else {
              update_user_meta($userid, 'darkmode', 'true');
          }
          echo get_user_meta($userid, 'darkmode', true);
      }
      die();
  }

  /// AJAX SEARCH PAGE POST AND CATEGORIES
  public function ma_admin_search_admin_posts() {

  		// CHECK FOR AJAX AND SECURITY NONCE
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

  				//GET POST TYPES AND CATEGORIES FILTERS
          $posttypes = array_map( 'sanitize_text_field', $_POST['jsonfilters'] );
          $searchterm = sanitize_text_field($_POST['searchterm']);



          if(isset($_POST['categoryfilters'])){
            $categories = array_map( 'sanitize_text_field', $_POST['categoryfilters'] );
          } else {
            $categories = array();
          }

  				//BUILD SEARCH ARGS
          $args = array('numberposts' => - 1, 's' => $searchterm, 'post_type' => $posttypes, 'category' => $categories,);
          $foundposts = get_posts($args);

  				///START OUTPUT BUFFERING
          ob_start();

          if (!$posttypes){
            ?>
            <p class=""><?php _e('No post types selected','admin2020')?></p>
            <?php
          }

  				//CHECK IF FOUND
          if (count($foundposts) > 0) {
  ?>
  			<!-- DISPLAY SEARCH INFORMATION. -->
  			<p class="">
  				<strong><?php echo count($foundposts) ?></strong><?php _e(' items found for ','admin2020')?><strong>"<?php echo $_POST['searchterm'] ?>"</strong>
  			</p>
  			<?php
  						///LOOP THROUGH FOUND POSTS
              foreach ($foundposts as $apost) {
                  $excerpt = get_the_excerpt($apost);
                  $excerpt = substr($excerpt, 0, 100);
                  $result = substr($excerpt, 0, strrpos($excerpt, ' '));
  ?>
  								<!-- BUILD FOUND CONTAINER. -->
  								<div class="ma-admin-search-result">
  									<a href="<?php echo get_edit_post_link($apost) ?>">
  										<h4 class="uk-margin-remove-bottom"><div class="uk-label uk-margin-small-right"><?php echo get_post_type($apost) ?></div><?php echo get_the_title($apost); ?></h4>
  										<p class="uk-margin-remove uk-text-muted"><?php echo $result ?></p>
  									</a>
  									<a href="<?php echo get_permalink($apost) ?>" class="uk-text-meta uk-margin-small-bottom"><span class="uk-margin-small-right" uk-icon="icon: link"></span><?php echo get_permalink($apost) ?></a>
  								</div>



  			<?php
              }
          } else {
  ?>
  			<!-- Nothing Found. -->
  			<p class=""><strong>0</strong> <?php _e(' items found for','admin2020')?> <strong>"<?php echo $_POST['searchterm'] ?>"</strong></p>
  			<?php
          }
          echo ob_get_clean();
      }
      die();
  }

  public function get_editable_roles() {
      global $wp_roles;

      $all_roles = $wp_roles->roles;
      $editable_roles = apply_filters('editable_roles', $all_roles);

      return $editable_roles;
  }


}
