<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Dashboard{

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function run(){

    add_action( 'admin_menu', array($this,'add_menu_item') );

    if (isset($_GET['page'])) {
        if($_GET['page']=='admin_2020_dashboard'){
          add_action('admin_head', array($this,'add_to_head'),0);
        }
    }

    add_action('wp_ajax_admin2020_save_dash_order', array($this,'admin2020_save_dash_order'));
    add_action('wp_ajax_admin2020_save_visibility', array($this,'admin2020_save_visibility'));
    add_action('admin2020_add_dash_card_start', array($this, 'admin2020_add_dash_card_start'));
    add_action('admin2020_add_dash_card_end', array($this, 'admin2020_add_dash_card_end'));

    add_action('wp_ajax_admin2020_get_analytics', array($this,'admin2020_get_analytics'));

    add_action('wp_ajax_admin2020_rebuild_dash', array($this,'admin2020_rebuild_dash'));

    add_filter('admin2020_register_dash_card', array($this,'register_the_cards'));

  }

  public function admin2020_rebuild_dash(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-dash-security-nonce', 'security') > 0) {

      $startdate = $_POST['sd'];
      $enddate = $_POST['ed'];

      echo $this->construct_cards($startdate,$enddate);

    }
    die();

  }

  public function admin2020_get_analytics(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-dash-security-nonce', 'security') > 0) {

      $start_date = $_POST['sd'];
      $end_date = $_POST['ed'];
      $data = $this->admin2020_get_analytics_request($start_date,$end_date);

      if (!$data){
        echo json_encode(array(false));
        return;
      }

      echo json_encode($data);

    }
    die();

  }

  public function admin2020_get_analytics_request($start_date,$end_date){

    $options = get_option( 'admin2020_settings' );

    if (isset($options['admin2020_analytics_token']) && isset($options['admin2020_analytics_view'])){
			$token = $options['admin2020_analytics_token'];
      $view = $options['admin2020_analytics_view'];

      if ($token == "" && $view == ""){

        $returndata = false;
        return $returndata;

      }

		} else {

      $returndata = false;
      return $returndata;

    }

    $remote = wp_remote_get( 'https://admintwentytwenty.com/analytics/fetch.php?code='.$token.'&view='.$view.'&sd='.$start_date.'&ed='.$end_date, array(
      'timeout' => 10,
      'headers' => array(
        'Accept' => 'application/json'
      ) )
    );

    if ( ! is_wp_error( $remote ) && isset( $remote['response']['code'] ) && $remote['response']['code'] == 200 && ! empty( $remote['body'] ) ) {

      $remote = json_decode( $remote['body'] );



      return $remote;

    }  else {

      $returndata = false;
      return $returndata;

    }

  }


  public function admin2020_save_dash_order(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-dash-security-nonce', 'security') > 0) {

        $dash_order = $_POST['order'];
        $userid = get_current_user_id();

        update_user_meta($userid, 'admin2020_dash_order', $dash_order);

        echo json_encode(get_user_meta($userid, 'admin2020_dash_order',true));

    }
    die();

  }

  public function admin2020_save_visibility(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-dash-security-nonce', 'security') > 0) {

        $visibility = $_POST['visibility'];
        $userid = get_current_user_id();

        update_user_meta($userid, 'admin2020_dash_visibility', $visibility);

        echo __("Preferences Saved","admin2020");

    }
    die();

  }


  public function add_menu_item() {

    $utils = new Admin2020_Util();
    if($utils->check_for_disarm()){
      return;
    }

    $slug ='admin_2020_dashboard';
    add_menu_page( '2020 Dashboard', __('Overview',"admin2020"), 'read', $slug, array($this,'admin_dashboard_render'),'dashicons-admin2020-grid',0 );
    return;

  }

  public function add_to_head(){

    $options = get_option( 'admin2020_settings' );
    if (isset($options['admin2020_admin2020_google_apikey'])){
      $apikey = $options['admin2020_admin2020_google_apikey'];
    }

    ?>
    <meta name="google-signin-client_id" content="<?php echo $apikey?>">
    <meta name="google-signin-scope" content="https://www.googleapis.com/auth/analytics.readonly">
    <?php
  }

//admin.php?page=myplugin%2Fmyplugin-admin-page.php


	public function admin_dashboard_render() {

    $this->build_grid();

	}

  public function build_grid(){

    $this->googleloader = false;
    $options = get_option( 'admin2020_settings' );

    if (isset($options['admin2020_analytics_token']) && isset($options['admin2020_analytics_view'])) {
      if ($options['admin2020_analytics_token'] != "" && $options['admin2020_analytics_view'] != ""){
          $this->googleloader = true;
          $this->adminC1 = $options['admin2020_analytics_token'];
        }
    }
		if (isset($options['admin2020_analytics_view'])){
      $this->gviewid = $options['admin2020_analytics_view'];
		} else {
      $this->gviewid = '';
		}

    $userid = get_current_user_id();
    $user_info = get_userdata($userid);
    $first_name = $user_info->first_name;
    $user = wp_get_current_user();
    $imgurl = esc_url( get_avatar_url( $user->ID ) );
    $greeting = $this->ma_admin_get_welcome();

    /// IF NO NAME SET USE USERNAME
    if (!$first_name) {
        $first_name = $user_info->user_login;
    }




    $cards = array();
    $extended_cards = apply_filters( 'admin2020_register_dash_card', $cards );

    $userid = get_current_user_id();
    $card_visibility = get_user_meta($userid, 'admin2020_dash_visibility', true);

    if(!$card_visibility){
      $card_visibility = array();
    }

		?>
    <p class="notice" id="gverror" style="display:none;"></p>

    <div uk-filter="target: #admin2020_overview" class="uk-width-1-1">

      <div uk-grid style="float:left:width:100%">

        <div class="uk-width-auto">
            <img class="uk-border-circle" width="60" height="60" src="<?php echo $imgurl?>">
        </div>
        <div class="uk-width-expand">
    			<h2 class="uk-h2 uk-margin-remove "><?php echo $greeting.', '.$first_name?></h2>
          <p class="uk-text-meta uk-margin-remove-top uk-margin-bottom"><?php echo date('jS F Y')?></p>
        </div>




        <div class="uk-width-auto admin2020daterange">


          <div class="uk-inline">
              <span class="uk-form-icon" uk-icon="icon: calendar"></span>
              <input class="uk-input " type="text" style="border-radius:4px;"id="admin2020-date-range"></input>
          </div>

          <button class="uk-button uk-button-default" style="padding: 0 10px;">
            <span uk-icon="menu"></span>
          </button>

          <div uk-dropdown="mode: click;pos: bottom-right">
            <div class="uk-h5 uk-margin-bottom"><?php _e('Categories','admin2020')?></div>
            <ul class="uk-nav uk-nav-default">
                <li class="uk-active" uk-filter-control><a style="background:none;" href="#"><?php _e("All","admin2020")?></a></li>
                <li class="uk-nav-divider"></li>
                <?php
                $tick_array = array();

                usort($extended_cards, function($a, $b){
                    return strcmp($a[3], $b[3]);
                });

                foreach ($extended_cards as $filter){
                  $category = $filter[3];
                  if(!in_array($category, $tick_array)){
                    ?>
                    <li uk-filter-control="[card-type='<?php echo strtolower($category) ?>']"><a style="background:none;" href="#"><?php echo ucwords($category) ?></a></li>
                    <?php
                  }
                  array_push($tick_array,$category);
                }

                ?>
            </ul>
          </div>




          <button class="uk-button uk-button-default" style="padding: 0 10px;">
            <span uk-icon="settings"></span>
          </button>

          <div uk-dropdown="mode: click;pos: bottom-right">
            <div class="uk-h5 uk-margin-bottom"><?php _e('Active Cards','admin2020')?></div>
            <ul class="uk-nav uk-nav-default" id="admin2020-visible-cards"style="max-height:500px;overflow:auto;">

                <?php
                $cat_check = array();

                foreach ($extended_cards as $card){

                  $category = $card[3];
                  if(!in_array($category,$cat_check)){
                    ?>
                    <li class="uk-nav-header"><?php echo ucwords($category) ?></li>
                    <?php
                  }
                  array_push($cat_check,$category);

                  $visible = 'checked';
                  if(in_array($card[1],$card_visibility)){
                    $visible = '';
                  }
                  ?>
                  <li >
                    <a style="background:none;" href="#">
                      <label><input class="uk-checkbox uk-margin-small-right" <?php echo $visible ?> value='1' type="checkbox" name="<?php echo $card[1] ?>"> <?php echo $card[2]?></label>
                    </a>
                  </li>
                  <?php
                }

                ?>
            </ul>
            <button class="uk-button uk-button-primary uk-margin-top uk-width-1-1" type="button" onclick="admin2020_save_visibility()"><?php _e("Save","admin2020")?></button>
          </div>

        </div>

        <div class="uk-width-1-1">

          <div uk-grid="masonry: true" id="admin2020_overview" uk-sortable>

            <?php
            $this->construct_cards();
            ?>

          </div>

        </div>

      </div>

    </div>
    <?php

  }



  public function construct_cards($startdate = null, $enddate = null){


    if ($startdate == null || $enddate == null){
      $enddate = date('Y-m-d');
      $startdate = date('Y-m-d',strtotime($enddate.' -1 month'));
    }


    $this->googleloader = false;
    $options = get_option( 'admin2020_settings' );

    if (isset($options['admin2020_analytics_token']) && isset($options['admin2020_analytics_view'])) {
      if ($options['admin2020_analytics_token'] != "" && $options['admin2020_analytics_view'] != ""){
          $this->googleloader = true;
          $this->adminC1 = $options['admin2020_analytics_token'];
        }
    }


    $userid = get_current_user_id();
    $dashorder = get_user_meta($userid, 'admin2020_dash_order', true);


    $card_visibility = get_user_meta($userid, 'admin2020_dash_visibility', true);

    if(!$card_visibility){
      $card_visibility = array();
    }

    $dash_cards = array();
    $extended_cards = apply_filters( 'admin2020_register_dash_card', $dash_cards );

    ///CHECK IF CUSTOM ORDER EXISTS
    if($dashorder && count($dashorder) > 0){

      ///LOOP THROUGH CUSTOM ORDER
      foreach($dashorder as $dashitem){

        if(in_array($dashitem,$card_visibility)){
          continue;
        }

        foreach ($extended_cards as $extended_card){
          if(is_array($extended_card)){
            $item = $extended_card[0];
            $the_function = $extended_card[1];

            if($dashitem == $the_function && $item != ""){
              $item->$the_function($startdate,$enddate);
            } else if (function_exists($the_function)) {
              $the_function($startdate,$enddate);
            }

          }
        }

      }
      /// CHECK IF NEW EXTENDED
      foreach ($extended_cards as $extended_card){

        if(is_array($extended_card)){
          $the_function = $extended_card[1];
        } else {
          $the_function = $extended_card;
        }

        if(in_array($the_function,$card_visibility)){
          continue;
        }

        if (!in_array($the_function,$dashorder)){

            if(is_array($extended_card)){

              $item = $extended_card[0];
              $the_function = $extended_card[1];

              if($item != ""){
                $item->$the_function($startdate,$enddate);
              } else if(function_exists($the_function)){
                $the_function($startdate,$enddate);
              }

            } else {

              if (function_exists($extended_card)) {

                $the_function($startdate,$enddate);

              }
          }
        }
      }

    } else {
      ///NO CUSTOM ORDER
      foreach ($extended_cards as $extended_card){

          if(is_array($extended_card)){
            $item = $extended_card[0];
            $the_function = $extended_card[1];

            if(in_array($the_function,$card_visibility)){
              continue;
            }

            $item->$the_function($startdate,$enddate);


          } else if (function_exists($extended_card)) {

            if(in_array($extended_card,$card_visibility)){
              continue;
            }

            $extended_card($startdate,$enddate);
          }
      }


    }

    if($this->googleloader){
      ?>
      <script>
      jQuery(document).ready(function($) {
        admin_2020_build_charts();
      })
      </script>
      <?php
    }


  }



  public function total_posts($startdate = null, $enddate = null){


    if(!$this->checksettings('admin2020_overview_total_posts')){
      return;
    }

    if($startdate != null && $enddate != null){

      $args = array(
      'date_query' => array(
          array(
              'after'     => $startdate,
              'before'    => $enddate,
              'inclusive' => true,
              ),
          ),
      );

      $query = new WP_Query( $args );
      $totalposts = $query->found_posts;

    } else {

      $tempcount = wp_count_posts("post");
      $totalposts = $tempcount->publish;

    }

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_posts" card-type='general'>
      <div class="uk-card uk-card-default uk-card-body">
            <span class="uk-text-emphasis"><?php _e('Total Posts','admin2020') ?></span>
          <div class="uk-h3 uk-text-primary uk-margin-remove"> <?php echo number_format($totalposts)?></div>
      </div>
    </div>
    <?php
  }

  public function total_pages($startdate = null, $enddate = null){

    if(!$this->checksettings('admin2020_overview_total_pages')){
      return;
    }

    if($startdate != null && $enddate != null){

      $args = array(
        'numberposts' => -1,
        'post_type'   => 'page',
        'date_query' => array(
            array(
                'after'     => $startdate,
                'before'    => $enddate,
                'inclusive' => true,
                ),
            ),
      );

    } else {

      $args = array(
        'numberposts' => -1,
        'post_type'   => 'page'
      );

    }
    $allpages = get_posts( $args );

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_pages" card-type='general'>
      <div class="uk-card uk-card-default uk-card-body">
            <span class="uk-text-emphasis"><?php _e('Total Pages','admin2020') ?></span>
          <div class="uk-h3 uk-text-primary uk-margin-remove"> <?php echo number_format(count($allpages))?></div>
      </div>
    </div>
    <?php
  }

  public function total_comments($startdate = null, $enddate = null){

    if(!$this->checksettings('admin2020_overview_total_comments')){
      return;
    }

    $totalcomments = wp_count_comments();
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_comments" card-type='general'>
      <div class="uk-card uk-card-default uk-card-body">
            <span class="uk-text-emphasis"><?php _e('Total Comments','admin2020') ?></span>
          <div class="uk-h3 uk-text-primary uk-margin-remove"> <?php echo number_format($totalcomments->approved)?></div>
      </div>
    </div>
    <?php
  }
  public function recent_comments($startdate = null, $enddate = null){

    if(!$this->checksettings('admin2020_overview_recent_comments')){
      return;
    }

    $args = array(
        'number'  => '5',
    );
    $comments = get_comments( $args );
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="recent_comments" card-type='general'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-bottom uk-text-emphasis"><?php _e('Recent Comments','admin2020') ?></div>
          <?php


          if(count($comments)<1){
            ?>
            <p><?php _e('No comments yet','admin2020') ?></p>

            <?php

          } else {

            foreach ( $comments as $comment ){

                $commentdate = human_time_diff( get_comment_date( 'U', $comment->comment_ID ),current_time( 'timestamp' ) );
                $user = get_user_by( 'login', $comment->comment_author );

                if (isset($user->ID)){
                  $img = get_avatar_url($user->ID);
                } else {
                  $img = "";
                }
                $commentlink = get_comment_link($comment);
                ?>

                <div class="uk-grid-small uk-flex-middle " uk-grid>
                    <div class="uk-width-auto" style="width:50px">
                        <img class="uk-border-circle" width="30" height="30" src="<?php echo $img?>">
                    </div>
                    <div class="uk-width-expand">
                        <span class=" uk-margin-remove-bottom"><?php echo $comment->comment_author ?></span>
                        <p class="uk-text-meta uk-margin-remove-top"><?php echo $commentdate?> <?php _e('ago','admin2020') ?></p>
                    </div>
                </div>
                <div class="uk-grid-small uk-margin-bottom" uk-grid>
                    <div class="uk-width-auto" style="width:50px">
                    </div>
                    <div class="uk-width-expand">
                      <a class="" href="<?php echo $commentlink?>">
                        <p class="uk-text-meta uk-margin-remove-bottom"><?php echo substr(esc_html($comment->comment_content),0,50)?>...</p>
                      </a>
                    </div>
                </div>

                <?php
            };
          }
           ?>
      </div>
    </div>
    <?php

  }
  public function visitors_chart($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s"  id="visitors_chart" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis"><span class="uk-text-meta overview_card_icon" uk-icon="icon: google;ratio:0.8"></span> <?php _e('Website Users','admin2020') ?></div>
        <div class="uk-text-meta">
          <span class="admin2020circle"></span>
          <span id="total-vists" class="uk-margin-bottom"></span>
        </div>
        <canvas id="traffic_visits" style="height:250px;max-height:250px;" class="uk-margin-top"></canvas>
      </div>
    </div>

    <?php
  }

  public function site_speed_chart($startdate = null, $enddate = null){



    if ($this->googleloader != true){
      return;
    }

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s"  id="site_speed_chart" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis"><span class="uk-text-meta overview_card_icon" uk-icon="icon: google;ratio:0.8"></span> <?php _e('Site Speed','admin2020') ?></div>
        <div class="uk-text-meta">
          <span class="admin2020circle" style="background:rgb(50 210 150)"></span>
          <span id="site_speed_average" class="uk-margin-bottom"></span>
        </div>
        <canvas id="site_speed" style="height:250px;max-height:250px;" class="uk-margin-top"></canvas>
      </div>
    </div>

    <?php
  }

  public function total_sessions($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_sessions" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis">

          <span class="uk-text-meta uk-margin-bottom overview_card_icon" uk-icon="icon: google;ratio:0.8"></span>  <?php _e('Total Page Views','admin2020') ?></div>

          <div class="uk-h3 uk-text-primary uk-margin-remove" id="admin2020_total_sessions"></div>

          <div class="uk-text-meta uk-margin-small-top ga_change_wrap uk-text-bold" id="total_page_views_change">
            <span class="change-text"></span>
            <span uk-icon="icon:chevron-up;" class="uk-text-success"></span>
            <span uk-icon="icon:chevron-down;" class="uk-text-danger"></span>

          </div>
          <div class="uk-text-meta">
            <span id="totalsessions_text"></span>
          </div>

      </div>
    </div>
    <?php
  }

  public function total_site_speed($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_site_speed" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis">

          <span class="uk-text-meta uk-margin-bottom overview_card_icon" uk-icon="icon: google;ratio:0.8"></span>  <?php _e('Average Site Speed','admin2020') ?></div>

          <div class="uk-h3 uk-text-primary uk-margin-remove" id="admin2020_total_site_speed"></div>

          <div class="uk-text-meta uk-margin-small-top ga_change_wrap uk-text-bold" id="admin2020_total_site_speed_change">
            <span class="change-text"></span>
            <span uk-icon="icon:chevron-down;" class="uk-text-success"></span>
            <span uk-icon="icon:chevron-up;" class="uk-text-danger"></span>

          </div>
          <div class="uk-text-meta">
            <span id="admin2020_total_site_speed_text"></span>
          </div>

      </div>
    </div>
    <?php
  }

  public function total_bounce_rate($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_bounce_rate" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis">
          <span class="uk-text-meta uk-margin-small-bottom overview_card_icon" uk-icon="icon: google;ratio:0.8"></span>  <?php _e('Bounce Rate','admin2020') ?>
        </div>

          <div class="uk-h3 uk-text-primary uk-margin-remove" id="admin2020_total_bounce_rate"></div>

          <div class="uk-text-meta uk-margin-small-top ga_change_wrap uk-text-bold" id="total_bounce_rate_change">
            <span class="change-text"></span>
            <span uk-icon="icon:chevron-down;" class="uk-text-success"></span>
            <span uk-icon="icon:chevron-up;" class="uk-text-danger"></span>

          </div>
          <div class="uk-text-meta">
            <span id="total_bounce_rate_text"></span>
          </div>
      </div>
    </div>
    <?php
  }

  public function session_duration($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="session_duration" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis">
          <span class="uk-text-meta uk-margin-bottom overview_card_icon" uk-icon="icon: google;ratio:0.8"></span>  <?php _e('Session Duration','admin2020') ?></div>

          <div class="uk-h3 uk-text-primary uk-margin-remove" id="admin2020_average_session_duration"></div>

          <div class="uk-text-meta uk-margin-small-top ga_change_wrap uk-text-bold" id="total_session_duration_change">
            <span class="change-text"></span>
            <span uk-icon="icon:chevron-up;" class="uk-text-success"></span>
            <span uk-icon="icon:chevron-down;" class="uk-text-danger"></span>

          </div>
          <div class="uk-text-meta">
            <span id="total_session_duration_text"></span>
          </div>

      </div>
    </div>
    <?php
  }

  public function total_pageviews($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_pageviews" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis"><span class="uk-text-meta overview_card_icon" uk-icon="icon: google;ratio:0.8"></span>  <?php _e('Page Views','admin2020') ?></div>
        <div class="uk-text-meta">
          <span class="admin2020circle pageviews"></span>
          <span id="total-sessions"></span>
        </div>
        <canvas id="session_visits" style="height:250px;max-height:250px;" class="uk-margin-top"></canvas>
      </div>
    </div>

    <?php
  }

  public function sessions_by_country($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="sessions_by_country" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis"><span class="uk-text-meta overview_card_icon" uk-icon="icon: google;ratio:0.8"></span> <?php _e('Top Countries','admin2020') ?></div>
        <table class="uk-table uk-table-justify uk-table-small" id="total-sessions-counntry">
          <thead>
            <tr>
              <th><?php  _e('Country','admin2020')?></th>
              <th><?php  _e('Visits','admin2020')?></th>
              <th class="uk-text-right"><?php  _e('Change','admin2020')?></th>
            </tr>
          </thead>
          <tbody class="uk-text-meta">
          </tbody>

        </table>
      </div>
    </div>

    <?php
  }

  public function sessions_by_page($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="sessions_by_page" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis"><span class="uk-text-meta overview_card_icon" uk-icon="icon: google;ratio:0.8"></span> <?php _e('Views By Page','admin2020') ?></div>
        <table class="uk-table uk-table-justify uk-table-small" id="total-sessions-page">
          <thead>
            <tr>
              <th><?php  _e('Country','admin2020')?></th>
              <th><?php  _e('Visits','admin2020')?></th>
              <th class="uk-text-right"><?php  _e('Change','admin2020')?></th>
            </tr>
          </thead>
          <tbody class="uk-text-meta">
          </tbody>

        </table>
      </div>
    </div>

    <?php
  }


  public function sessions_by_referer($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="sessions_by_referer" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis"><span class="uk-text-meta overview_card_icon" uk-icon="icon: google;ratio:0.8"></span> <?php _e('Traffic Sources','admin2020') ?></div>
        <table class="uk-table uk-table-justify uk-table-small" id="total-sessions-referer">
          <thead>
            <tr>
              <th><?php _e("Source","admin2020")?></th>
              <th ><?php _e("Visits","admin2020")?></th>
              <th class="uk-text-right"><?php  _e('Change','admin2020')?></th>
            </tr>
          </thead>
          <tbody class="uk-text-meta">
          </tbody>

        </table>
      </div>
    </div>

    <?php
  }

  public function device_breakdown($startdate = null, $enddate = null){

    if ($this->googleloader != true){
      return;
    }
    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="device_breakdown" card-type='analytics'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis uk-margin-bottom"><span class="uk-text-meta overview_card_icon" uk-icon="icon: google;ratio:0.8"></span>  <?php _e('Device Breakdown','admin2020') ?></div>

        <canvas id="device_visits" style="height:200px;max-height:200px;" class="uk-margin-top"></canvas>
      </div>
    </div>

    <?php
  }





  public function most_commented_posts($startdate = null, $enddate = null){

    if(!$this->checksettings('admin2020_overview_most_commented')){
      return;
    }

    $posttypes = get_post_types();
    $newPT = array();
    foreach($posttypes as $type){
      array_push($newPT,$type);
    }

    if($startdate != null && $enddate != null){

      $args = array(
        'numberposts' => 5,
        'post_type'   => $newPT,
        'orderby'     => 'comment_count',
        'date_query' => array(
            array(
                'after'     => $startdate,
                'before'    => $enddate,
                'inclusive' => true,
                ),
            ),
      );

    } else {

      $args = array(
        'numberposts' => 5,
        'post_type'   => $newPT,
        'orderby'     => 'comment_count'
      );

    }



    $mostcommented = get_posts( $args );

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="most_commented_posts" card-type='general'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-bottom uk-text-emphasis"><?php _e('Most Commented Posts','admin2020') ?></div>
        <?php
          if (count($mostcommented)>0){
            foreach($mostcommented as $post){
              ?>
              <div class="uk-margin-bottom" style="position:relative;">
                <a class="uk-link uk-link-muted" href="<?php echo get_permalink($post)?>"><?php echo get_the_title($post); ?></a>
                <div class=" uk-badge uk-position-right">
                  <?php echo get_comments_number($post)?>
                </div>
              </div>
              <?php
            }
          } else {
            ?>
            <p><?php _e('No comments yet','admin2020') ?></p>
            <?php
          }
           ?>

      </div>
    </div>
    <?php
  }

  public function new_users($startdate = null, $enddate = null){

    if(!$this->checksettings('admin2020_overview_new_users')){
      return;
    }

    $allusers = count_users();
    $allusers = $allusers['total_users'];

    $args = array (
        'date_query'    => array(
            array(
                'after'     => '2 weeks ago',
                'inclusive' => true,
            ),
         ),
    );

    $user_query = get_users( $args );

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="new_users" card-type='general'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-text-emphasis"><?php _e('New Users','admin2020') ?></div>
        <div class="uk-text-meta uk-margin-bottom"><span id="total-vists"><?php echo count($user_query)?></span> <?php _e('new users in the last 14 days','admin2020') ?></div>
          <div class="uk-h3 uk-text-primary uk-margin-remove" id=""><?php echo $allusers?></div>

      </div>
    </div>
    <?php

  }

  public function system_info($startdate = null, $enddate = null){

    if(!$this->checksettings('admin2020_overview_system_info')){
      return;
    }

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="system_info" card-type='general'>
      <div class="uk-card uk-card-default uk-card-body">
        <div class="uk-margin-bottom uk-text-emphasis"><?php _e('System Info','admin2020') ?></div>
          <div class="uk-grid uk-text-meta" id="admin2020syteminfo">
            <div class="uk-width-2-3">
              WordPress Version:
            </div>
            <div class="uk-width-1-3">
              <?php echo get_bloginfo( 'version' );?>
            </div>
            <div class="uk-width-2-3">
              PHP Version:
            </div>
            <div class="uk-width-1-3">
              <?php echo phpversion();?>
            </div>
            <div class="uk-width-2-3">
              Admin 2020 Version:
            </div>
            <div class="uk-width-1-3">
              <?php echo $this->version;?>
            </div>
          </div>

      </div>
    </div>
    <?php
  }

  public function register_the_cards($dashitems){
    $admin2020_cards = array(
      array('total_posts','Total Posts','General'),
      array('total_pages','Total Pages','General'),
      array('total_comments','Total Comments','General'),
      array('recent_comments','Recent Comments','General'),
      array('visitors_chart','Visitors Chart','Analytics'),
      array('total_sessions','Total Sessions','Analytics'),
      array('session_duration','Session Duration','Analytics'),
      array('device_breakdown','Device Breakdown','Analytics'),
      array('sessions_by_country','Sessions By Country','Analytics'),
      array('total_pageviews','Total Page Views','Analytics'),
      array('sessions_by_page','Sessions By Page','Analytics'),
      array('most_commented_posts','Most Commented Posts','General'),
      array('new_users','New Users','General'),
      array('system_info','System Info','General'),
      array('total_bounce_rate','Bounce Rate','General'),
      array('sessions_by_referer','Sessions By Referer','Analytics'),
      array('site_speed_chart','Page Speed','Analytics'),
      array('total_site_speed','Average Page Speed','Analytics'),
    );

    foreach ($admin2020_cards as $card){
      $function = $card[0];
      $name = $card[1];
      $category = $card[2];
      array_push($dashitems,array($this,$function,$name,$category));
    }

    return $dashitems;
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

  public function checksettings($optionname){

    $utils = new Admin2020_Util();
    $disabled_widget = $utils->get_option($optionname);

		if ($disabled_widget){
			$value = false;
		} else {
			$value = true;
		}
    return $value;

  }

}
