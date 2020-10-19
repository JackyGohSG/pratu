<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Media{

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function run(){

    add_action( 'admin_menu', array($this,'add_menu_item') );
    ///AJAX
    add_action('wp_ajax_admin2020_save_attachment', array($this,'admin2020_save_attachment'));
    add_action('wp_ajax_admin2020_delete_attachment', array($this,'admin2020_delete_attachment'));
    add_action('wp_ajax_admin2020_delete_multiple_attachment', array($this,'admin2020_delete_multiple_attachment'));
    add_action('wp_ajax_admin2020_upload_attachment', array($this,'admin2020_upload_attachment'));
    add_action('wp_ajax_admin2020_upload_edited_image', array($this,'admin2020_upload_edited_image'));
    add_action('wp_ajax_admin2020_upload_edited_image_as_copy', array($this,'admin2020_upload_edited_image_as_copy'));
    add_action('wp_ajax_admin2020_get_media', array($this,'admin2020_get_media'));
    add_action('wp_ajax_admin2020_build_media_page', array($this,'admin2020_build_media_page'));
    add_action('wp_ajax_admin2020_build_media_filter', array($this,'admin2020_build_media_filter'));
    add_action('wp_ajax_admin2020_get_attachment_view', array($this,'admin2020_get_attachment_view'));
    add_action('wp_ajax_admin2020_add_batch_rename_item', array($this,'admin2020_add_batch_rename_item'));
    add_action('wp_ajax_admin2020_batch_rename', array($this,'admin2020_batch_rename'));
    add_action('wp_ajax_admin2020_duplicate_post', array($this,'admin2020_duplicate_post'));
    add_action('wp_ajax_admin2020_save_post', array($this,'admin2020_save_post'));

    ///PULL FOLDERS TO MEDIA MODAL
    add_filter( 'wp_prepare_attachment_for_js', array($this,'admin2020_pull_meta_to_attachments'), 10, 3 );
    add_action( 'wp_enqueue_media', array($this,'admin2020_add_media_overrides') );


  }


  public function admin2020_add_media_overrides() {

      add_action( 'admin_footer', array($this,'override_media_templates') );
      wp_enqueue_script('ma-admin-media', plugin_dir_url(__DIR__) . 'assets/js/ma-admin-media.min.js', array('jquery'), $this->version);
      wp_localize_script('ma-admin-media', 'ma_admin_media_ajax', array(
        'ajax_url' => admin_url('admin-ajax.php'),
        'security' => wp_create_nonce('ma-admin-media-security-nonce')
      ));
      wp_register_style('ma-admin-media_css', plugin_dir_url(__DIR__) . 'assets/css/ma-admin-media.min.css', array(), $this->version);
      wp_enqueue_style('ma-admin-media_css');
  }

  public function admin2020_pull_meta_to_attachments(  $response, $attachment, $meta ) {
      $mimetype = get_post_mime_type($attachment->ID);
      $pieces = explode("/", $mimetype);
      $type = $pieces[0];
      $folderid = get_post_meta( $attachment->ID, 'admin2020_folder', true );
      $response[ 'folderid' ] = $folderid." filter-".$type;
      return $response;
  }



  public function add_menu_item() {

    $utils = new Admin2020_Util();
    if($utils->check_for_disarm()){
      return;
    }

    $slug ='admin_2020_media';
    add_menu_page( 'Media Library', __('Media','admin2020'), 'read', $slug, array($this,'admin_media_render'),'dashicons-admin2020-media',9 );
    return;

  }

  public function admin2020_get_media() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

          echo $this->build_media();

      }
      die();
  }

  public function admin2020_upload_attachment() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $folderid = $_POST['folderid'];

          foreach ($_FILES as $file){

            $uploadedfile = $file;
            $upload_overrides = array(
              'test_form' => false
            );


            $movefile = wp_handle_upload( $uploadedfile, $upload_overrides );
            $movefile;
            ////ADD Attachment

            $wp_upload_dir = wp_upload_dir();
            $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $uploadedfile['name']);

            	$attachment = array(
            		"guid" => $movefile['url'],
            		"post_mime_type" => $movefile['type'],
            		"post_title" => $withoutExt,
            		"post_content" => "",
            		"post_status" => "published",
            	);

            	$id = wp_insert_attachment( $attachment, $movefile['file'],0);

            	$attach_data = wp_generate_attachment_metadata( $id, $movefile['file'] );
            	wp_update_attachment_metadata( $id, $attach_data );
              update_post_meta($id, "admin2020_folder",$folderid);

            ////END ATTACHMENT


          }
          //echo $this->build_media();
          echo json_encode($movefile);
           //echo '<pre>' . print_r( $_FILES, true ) . '</pre>';
      }
      die();
  }



  public function admin2020_upload_edited_image() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $current_imageid = $_POST['attachmentid'];
        $new_file =  $_FILES['ammended_image'];

        $upload_overrides = array(
          'test_form' => false
        );


        $movefile = wp_handle_upload( $new_file, $upload_overrides );
        ////ADD Attachment

        update_attached_file($current_imageid,$movefile['url']);

        $attach_data = wp_generate_attachment_metadata( $current_imageid, $movefile['file'] );
        wp_update_attachment_metadata( $current_imageid, $attach_data );

        ////END ATTACHMENT
        echo $this->build_media();
          //echo json_encode($movefile);
           //echo '<pre>' . print_r( $_FILES, true ) . '</pre>';
      }
      die();
  }

  public function admin2020_upload_edited_image_as_copy() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {

        require_once( ABSPATH . 'wp-admin/includes/image.php' );
        require_once( ABSPATH . 'wp-admin/includes/file.php' );

        $current_imageid = $_POST['attachmentid'];
        $new_file =  $_FILES['ammended_image'];
        $filename =  $_POST['file_name'];
        $withoutExt = preg_replace('/\\.[^.\\s]{3,4}$/', '', $filename);

        $currentfolder = get_post_meta($current_imageid , 'admin2020_folder', true);

        $upload_overrides = array(
          'test_form' => false
        );


        $movefile = wp_handle_upload( $new_file, $upload_overrides );
        ////ADD Attachment


        $attachment = array(
          "guid" => $movefile['url'],
          "post_mime_type" => $movefile['type'],
          "post_title" => $withoutExt,
          "post_content" => "",
          "post_status" => "published",
        );

        $id = wp_insert_attachment( $attachment, $movefile['file'],0);

        $attach_data = wp_generate_attachment_metadata( $id, $movefile['file'] );
        wp_update_attachment_metadata( $id, $attach_data );
        update_post_meta($id, "admin2020_folder",$currentfolder);

        ////END ATTACHMENT
        echo $this->build_media();
          //echo json_encode($movefile);
           //echo '<pre>' . print_r( $_FILES, true ) . '</pre>';
      }
      die();
  }


  public function admin2020_delete_multiple_attachment() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

          $attachmentids = $_POST['theids'];

          foreach ($attachmentids as $attachmentid){

            if(get_post_type($attachmentid) == 'attachment'){

              wp_delete_attachment($attachmentid);

            } else {

              wp_delete_post($attachmentid);

            }

          }

          echo "Deleted";
      }
      die();
  }



  public function admin2020_delete_attachment() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

          $attachmentid = $_POST['imgid'];

          if(get_post_type($attachmentid) == 'attachment'){

            wp_delete_attachment($attachmentid);

          } else {

            wp_delete_post($attachmentid);

          }

          echo "Deleted";

      }
      die();
  }









  public function admin2020_duplicate_post() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

          global $wpdb;
          $post_id = $_POST['postid'];
          $post = get_post( $post_id );

          $current_user = wp_get_current_user();
          $new_post_author = $current_user->ID;

          $args = array(
            'comment_status' => $post->comment_status,
            'ping_status'    => $post->ping_status,
            'post_author'    => $new_post_author,
            'post_content'   => $post->post_content,
            'post_excerpt'   => $post->post_excerpt,
            'post_name'      => $post->post_name,
            'post_parent'    => $post->post_parent,
            'post_password'  => $post->post_password,
            'post_status'    => 'draft',
            'post_title'     => $post->post_title.' (copy)',
            'post_type'      => $post->post_type,
            'to_ping'        => $post->to_ping,
            'menu_order'     => $post->menu_order
          );

          $new_post_id = wp_insert_post( $args );

          $taxonomies = get_object_taxonomies($post->post_type); // returns array of taxonomy names for post type, ex array("category", "post_tag");
          foreach ($taxonomies as $taxonomy) {
            $post_terms = wp_get_object_terms($post_id, $taxonomy, array('fields' => 'slugs'));
            wp_set_object_terms($new_post_id, $post_terms, $taxonomy, false);
          }

          $post_meta_infos = $wpdb->get_results("SELECT meta_key, meta_value FROM $wpdb->postmeta WHERE post_id=$post_id");
          if (count($post_meta_infos)!=0) {
            $sql_query = "INSERT INTO $wpdb->postmeta (post_id, meta_key, meta_value) ";
            foreach ($post_meta_infos as $meta_info) {
              $meta_key = $meta_info->meta_key;
              if( $meta_key == '_wp_old_slug' ) continue;
              $meta_value = addslashes($meta_info->meta_value);
              $sql_query_sel[]= "SELECT $new_post_id, '$meta_key', '$meta_value'";
            }
            $sql_query.= implode(" UNION ALL ", $sql_query_sel);
            $wpdb->query($sql_query);
          }

          echo $this->build_single_attachment($new_post_id);

      }
      die();
  }



  public function admin2020_save_post() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

          $title = $_POST['title'];
          $content = $_POST['content'];
          $postid = $_POST['postid'];

          $my_post = array(
              'ID'           => $postid,
              'post_title'   => $title,
              'post_content' => $content,
          );

          wp_update_post( $my_post );

          //print_r($errors);
          echo $this->build_single_attachment($postid);

      }
      die();
  }


  public function admin2020_save_attachment() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-security-nonce', 'security') > 0) {
          $title = $_POST['title'];
          $imgalt = $_POST['imgalt'];
          $caption = $_POST['caption'];
          $description = $_POST['description'];
          $imgid = $_POST['imgid'];

          $attachment = array(
            'ID' => strip_tags($imgid),
            'post_title' => strip_tags($title),
            'post_content' => strip_tags($description),
            'post_excerpt' => strip_tags($caption),
          );
          update_post_meta( $imgid, '_wp_attachment_image_alt', $imgalt);

          wp_update_post( $attachment);
          //print_r($errors);
          echo $this->build_single_attachment($imgid);

      }
      die();
  }

  public function admin_media_render() {

    $this->attachment_type = 'attachment';
    $this->page = 'media';
    $this->post_status = 'inherit';
    global $content_page;
    $content_page = false;

    if(isset($_GET['page'])){
      if($_GET['page'] == "admin_2020_content"){


        $this->page = 'content';

        $content_page = true;

        $utils = new Admin2020_Util();
        $selected_post_types = $utils->get_option('admin2020_content_included_posts');

        if (!is_array($selected_post_types)){
          $selected_post_types = array("post","page");
        }

        $this->attachment_type = $selected_post_types;
        $this->post_status = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit');
      }
    }


    ?>
    <div uk-filter="target: .admin2020_media_gallery" id="admin2020_media_gallery_filter">
      <?php
      $this->build_nav();

      $userid = get_current_user_id();
      $current = get_user_meta($userid, 'admin2020_preferences', true);
      $open_closed = 'aria-hidden="true" hidden';

      if($current){

        if(isset($current['folders'])){
          $status = $current['folders'];

          if($status == 'open'){
            $open_closed = 'aria-hidden="false"';
          }
        }
      }

      ?>
      <div class="uk-grid">

        <div class="uk-width-large" id="admin2020_mediapanel" <?php echo $open_closed?>>

          <div class="holder" uk-sticky="media: 640;top: 200;offset:140;cls-active: foldersfixed">

            <div class="admin2020folders" style="width:400px;padding-right:30px;">
              <?php
              $addmin2020_folders = new Admin_2020_Folders($this->version);

              if($this->page == 'content'){
                $addmin2020_folders->build_folder_panel('content');
              } else {
                $addmin2020_folders->build_folder_panel();
              }

              $this->build_filters(); ?>
            </div>

          </div>

        </div>

        <div uk-grid="" class="admin2020_media_gallery uk-width-expand">
        <?php $this->build_media();?>
        </div>

      </div>

    </div>

    <div class="admin2020_image_edit_wrap">
      <div class="admin2020_image_edit_header uk-padding-small uk-position-small uk-position-top-right" style="z-index:9">
        <a href="#" uk-icon="icon: close" onclick="jQuery('.admin2020_image_edit_wrap').hide();" style="float:right"></a>
      </div>
      <div id="admin2020_image_edit_area"></div>
    </div>


    <?php

    $this->build_viewer();
    $this->build_uploader();
    $this->loadscripts();
    $this->load_scroll_helper();

  }

  public function build_filters(){

    ?>
    <div class="uk-width-1-1"><hr></div>
    <div class="uk-width-1-1" style="float:left;" >

      <div id="selectedfilters">
        <span id="month"></span>
        <span id="year"></span>
        <span id="user"></span>
        <span class="uk-label admin2020filterControlalll" uk-filter-control id="clearall" style="cursor:pointer;display:none;"><?php _e('Clear Filters','admin2020')?></span>
      </div>
      <ul uk-accordion>
          <li>

              <a class="uk-accordion-title uk-text-meta" href="#"><?php _e('Advanced Filters','admin2020')?></a>

              <div class="uk-accordion-content">

                <button class="uk-button uk-button-default"><?php _e('Order By','admin2020')?></button>
                <div uk-dropdown="mode: click">
                    <ul class="uk-nav uk-dropdown-nav">
                      <li uk-filter-control="sort: admin2020_uploaded_on;group: orderby"><a href="#"><?php _e('Date Created (Oldest first)','admin2020') ?></a></li>
                      <li uk-filter-control="sort: admin2020_uploaded_on;order: desc;group: orderby"><a href="#"><?php _e('Date Created (Newest first)','admin2020') ?></a></li>
                      <li uk-filter-control="sort: admin2020_file_size_order;group: orderby;filter: .admin2020_attachment"><a href="#"><?php _e('File Size (Smallest First)','admin2020') ?></a></li>
                      <li uk-filter-control="sort: admin2020_file_size_order;order: desc;group: orderby;filter: .admin2020_attachment"><a href="#"><?php _e('File Size (Biggest First)','admin2020') ?></a></li>
                    </ul>
                </div>

                <div class="uk-width-1-1 uk-margin-top uk-grid-small" uk-grid>

                  <div class="uk-text-meta uk-width-1-1"><?php _e('Filter By Date','admin2020')?>:</div>

                  <div class="uk-width-1-2">
                    <button class="uk-button uk-button-default uk-width-1-1"><?php _e('Month','admin2020')?></button>
                    <div uk-dropdown="mode: click">
                        <ul class="uk-nav uk-dropdown-nav">
                          <li class="admin2020filterControlmonth" uk-filter-control="group: months"><a href="#"><?php _e('All','admin2020')?></a></li>
                          <li class="uk-nav-divider" style="margin: 10px 0;"></li>
                          <?php
                          for($m=1; $m<=12; ++$m){
                              $month = date('F', mktime(0, 0, 0, $m, 1));
                              ?>
                              <li class="admin2020filterControlmonth" uk-filter-control="filter: [admin2020_month_filter='<?php echo $month ?>'];group: months"><a href="#"><?php echo $month ?></a></li>
                              <?php
                          }
                           ?>
                        </ul>
                    </div>
                  </div>

                  <div class="uk-width-1-2">
                    <button class="uk-button uk-button-default uk-width-1-1"><?php _e('Year','admin2020')?></button>
                    <div uk-dropdown="mode: click">
                      <ul class="uk-nav uk-dropdown-nav">
                        <li class="admin2020filterControlmonth" uk-filter-control="group: years"><a href="#"><?php _e('All','admin2020')?></a></li>
                        <li class="uk-nav-divider" style="margin: 10px 0;"></li>
                        <?php
                        $today = date("Y-m-d");
                        for($m=0; $m<=5; ++$m){

                            $year = date('Y', strtotime($today." - ".$m." years"));
                            ?>
                            <li class="admin2020filterControlyear" uk-filter-control="filter: [admin2020_year_filter='<?php echo $year ?>'];group: years"><a href="#"><?php echo $year ?></a></li>
                            <?php
                        }
                         ?>
                      </ul>
                    </div>
                  </div>

                </div>


                <div class="uk-width-1-1 uk-margin-top uk-grid-small" uk-grid>

                  <div class="uk-text-meta uk-width-1-1"><?php _e('Uploaded By','admin2020')?>:</div>

                  <div class="uk-width-1-1">
                    <button class="uk-button uk-button-default uk-width-1-1"><?php _e('Username','admin2020')?></button>
                    <div uk-dropdown="mode: click">
                        <ul class="uk-nav uk-dropdown-nav">
                          <li class="admin2020filterControluser" uk-filter-control="group: user"><a href="#"><?php _e('All','admin2020')?></a></li>
                          <li class="uk-nav-divider" style="margin: 10px 0;"></li>
                          <?php

                          $blogusers = get_users();

                          foreach($blogusers as $user){
                              $username = $user->display_name;
                              ?>
                              <li class="admin2020filterControluser" uk-filter-control="filter: [admin2020_user_filter='<?php echo $username ?>'];group: user"><a href="#"><?php echo $username ?></a></li>
                              <?php
                          }
                           ?>
                        </ul>
                    </div>
                  </div>

                </div>

              </div>
          </li>

      </ul>
    </div>
    <?php

  }



  public function admin2020_batch_rename(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

      $attachments = $_POST['ids'];
      $name_structure = $_POST['structure'];
      $name_values = $_POST['values'];
      $attrtibute = wp_strip_all_tags($_POST['item_to_rename']);

      $returndata = array();

      if (count($attachments) < 1){
        $returndata['error'] = __('No attachments selected','admin2020');
        echo json_encode($returndata);
        die();
      }

      $sequence_number = 0;

      foreach ($attachments as $attachment_id){

        $attachment_id = wp_strip_all_tags($attachment_id);

        $current_title = get_the_title($attachment_id);
        $post_date = get_the_date($attachment_id);
        $alt_text = get_post_meta($attachment_id , '_wp_attachment_image_alt', true);
        $attachment_url = wp_get_attachment_url($attachment_id);
        $filetype = wp_check_filetype($attachment_url);
        $extension = $filetype['ext'];

        $newname = "";
        $counter = 0;
        $test = "";

        foreach ($name_structure as $structure){

          $structure = wp_strip_all_tags($structure);
          $the_value = wp_strip_all_tags($name_values[$counter]);

          if($structure == 'filename'){
            $newname = $newname . $current_title;
          }
          if($structure == 'text'){
            $newname = $newname . $the_value;
          }
          if($structure == 'date'){

            if(date($the_value)){
              $newname = $newname . get_the_date($the_value,$attachment_id);
            } else {
              $returndata['error'] = __('Invalid Date Format','admin2020');
              echo json_encode($returndata);
              die();
            }

          }
          if($structure == 'original_alt'){
            $newname = $newname . $alt_text;
          }
          if($structure == 'extension'){
            $newname = $newname . $extension;
          }
          if($structure == 'sequence'){
            $start_number = $the_value;
            if(!is_numeric($start_number)){
              $start_number = 0;
            }
            $newname = $newname . ($sequence_number + $start_number);
          }
          if($structure == 'meta'){
            $meta_item = get_post_meta( $attachment_id, $the_value, true );
            if($meta_item){
              $newname = $newname . $meta_item;
            }
          }

          $counter = $counter + 1;


        }

        $sequence_number = $sequence_number + 1;

        if($attrtibute == "name"){

          $my_post = array(
              'ID'           => $attachment_id,
              'post_title'   => $newname,
          );
          wp_update_post( $my_post );

        }

        if($attrtibute == "alt"){

          update_post_meta( $attachment_id, '_wp_attachment_image_alt', $newname );

        }

      }
      $returndata['message'] = __('Attachments successfully renamed','admin2020');
      echo json_encode($returndata);


    }

    die();

  }

  public function admin2020_add_batch_rename_item(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

      $itemtoadd = $_POST['itemtoadd'];

      if($itemtoadd == 'filename'){
        ob_start();
        ?>
        <div class="uk-grid-small uk-child-width-1- rename_item uk-flex uk-flex-middle" uk-grid>
          <div class="uk-width-2-5">
            <span name="<?php echo $itemtoadd?>" class="batch_rename_option">
              <span uk-icon="grid" class="uk-margin-small-right rename_drag"></span>
              <?php _e('Filename')?>:
            </span>
          </div>

          <div class="uk-width-expand">
            <input class="uk-input" placeholder="<?php _e("Current Filename","admin2020")?>" disabled>
          </div>

          <div class="uk-text-right uk-flex uk-flex-middle uk-flex-right uk-width-auto">
            <a href="#" onclick="jQuery(this).parent().parent().remove();build_batch_rename_preview()"><span uk-icon="minus-circle"></span></a>
          </div>

        </div>
        <?php
      }

      if($itemtoadd == 'text'){
        ob_start();
        ?>
        <div class="uk-grid-small rename_item uk-flex uk-flex-middle" uk-grid>
          <div class="uk-width-2-5">
            <span name="<?php echo $itemtoadd?>" class="batch_rename_option">
              <span uk-icon="grid" class="uk-margin-small-right rename_drag"></span>
              <?php _e('Text')?>:
            </span>
          </div>

          <div class="uk-width-expand">
            <input onkeyup="build_batch_rename_preview()" class="uk-input" placeholder="<?php _e("New text","admin2020")?>">
          </div>

          <div class="uk-text-right uk-flex uk-flex-middle uk-flex-right uk-width-auto">
            <a href="#" onclick="jQuery(this).parent().parent().remove();build_batch_rename_preview()"><span uk-icon="minus-circle"></span></a>
          </div>

        </div>
        <?php
      }

      if($itemtoadd == 'date'){
        ob_start();
        ?>
        <div class="uk-grid-small rename_item uk-flex uk-flex-middle" uk-grid>
          <div class="uk-width-2-5">
            <span name="<?php echo $itemtoadd?>" class="batch_rename_option">
              <span uk-icon="grid" class="uk-margin-small-right rename_drag"></span>
              <?php _e('Date Uploaded')?>:
            </span>
          </div>

          <div class="uk-width-expand">
            <input class="uk-input" onkeyup="build_batch_rename_preview()" placeholder="<?php _e("Format","admin2020")?>">
          </div>

          <div class="uk-text-right uk-flex uk-flex-middle uk-flex-right uk-width-auto">
            <a href="#" onclick="jQuery(this).parent().parent().remove();build_batch_rename_preview()"><span uk-icon="minus-circle"></span></a>
          </div>

        </div>
        <?php
      }


      if($itemtoadd == 'original_alt'){
        ob_start();
        ?>
        <div class="uk-grid-small rename_item uk-flex uk-flex-middle" uk-grid>
          <div class="uk-width-2-5">
            <span name="<?php echo $itemtoadd?>" class="batch_rename_option">
              <span uk-icon="grid" class="uk-margin-small-right rename_drag"></span>
              <?php _e('Alt')?>:
            </span>
          </div>

          <div class="uk-width-expand">
            <input class="uk-input" placeholder="<?php _e("Current Alt","admin2020")?>" disabled>
          </div>

          <div class="uk-text-right uk-flex uk-flex-middle uk-flex-right uk-width-auto">
            <a href="#" onclick="jQuery(this).parent().parent().remove();build_batch_rename_preview()"><span uk-icon="minus-circle"></span></a>
          </div>

        </div>
        <?php
      }

      if($itemtoadd == 'extension'){
        ob_start();
        ?>
        <div class="uk-grid-small rename_item uk-flex uk-flex-middle" uk-grid>
          <div class="uk-width-2-5">
            <span name="<?php echo $itemtoadd?>" class="batch_rename_option">
              <span uk-icon="grid" class="uk-margin-small-right rename_drag"></span>
              <?php _e('Extension')?>:
            </span>
          </div>

          <div class="uk-width-expand">
            <input class="uk-input" placeholder="<?php _e("Current Extension","admin2020")?>" disabled>
          </div>

          <div class="uk-text-right uk-flex uk-flex-middle uk-flex-right uk-width-auto">
            <a href="#" onclick="jQuery(this).parent().parent().remove();build_batch_rename_preview()"><span uk-icon="minus-circle"></span></a>
          </div>

        </div>
        <?php
      }

      if($itemtoadd == 'sequence'){
        ob_start();
        ?>
        <div class="uk-grid-small rename_item uk-flex uk-flex-middle" uk-grid>
          <div class="uk-width-2-5">
            <span name="<?php echo $itemtoadd?>" class="batch_rename_option">
              <span uk-icon="grid" class="uk-margin-small-right rename_drag"></span>
              <?php _e('Sequence start num')?>:
            </span>
          </div>

          <div class="uk-width-expand">
            <input class="uk-input" placeholder="<?php _e("Start Number","admin2020")?>" value="0">
          </div>

          <div class="uk-text-right uk-flex uk-flex-middle uk-flex-right uk-width-auto">
            <a href="#" onclick="jQuery(this).parent().parent().remove();build_batch_rename_preview()"><span uk-icon="minus-circle"></span></a>
          </div>

        </div>
        <?php
      }

      if($itemtoadd == 'meta'){
        ob_start();
        ?>
        <div class="uk-grid-small rename_item uk-flex uk-flex-middle" uk-grid>
          <div class="uk-width-2-5">
            <span name="<?php echo $itemtoadd?>" class="batch_rename_option">
              <span uk-icon="grid" class="uk-margin-small-right rename_drag"></span>
              <?php _e('Meta key')?>:
            </span>
          </div>

          <div class="uk-width-expand">
            <input class="uk-input" placeholder="<?php _e("Meta Key","admin2020")?>" value="">
          </div>

          <div class="uk-text-right uk-flex uk-flex-middle uk-flex-right uk-width-auto">
            <a href="#" onclick="jQuery(this).parent().parent().remove();build_batch_rename_preview()"><span uk-icon="minus-circle"></span></a>
          </div>

        </div>
        <?php
      }

      echo ob_get_clean();

    }

    die();

  }

  public function build_nav(){

    if($this->page == 'content'){
      $title = __('Content','admin2020');
    } else {
      $title = __('Media Library','admin2020');
    }
    ?>
    <h1 class="uk-margin-remove-top uk-margin-bottom"><?php echo $title ?></h1>
    <?php if($this->page == 'media'){ ?>
      <div class="uk-position-top-right uk-padding-large admin2020topbutton">
        <button class="page-title-action" uk-toggle="target: #admin2020uploader"><?php _e('Upload','admin2020') ?></button>
      </div>
    <?php } ?>
    <div class="uk-padding uk-padding-remove-horizontal uk-flex uk-flex-between" style="width:100%;">

      <ul class="uk-iconnav">
      <li><button class="uk-button uk-button-default" onclick="admin2020_set_prefs('folders')" uk-toggle="target: #admin2020_mediapanel" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: folder"></span></button></li>
      <li><button class="uk-button uk-button-default" id="admin2020listView" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: list"></span></button></li>
      <li><button class="uk-button uk-button-default" id="admin2020gridView" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: grid"></span></button></li>
      <li><button class="uk-button uk-button-default" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: search"></span></button></li>
      <div uk-drop="mode: click;pos:right-center">
        <div class="uk-inline">
            <span class="uk-form-icon" uk-icon="icon: search"></span>
            <input class="uk-input" id="admin2020mediaSearch" placeholder="Search media..." autofocus>
        </div>
      </div>
      </ul>

      <ul class="uk-iconnav">

        <li><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="group: type" style="padding-left:10px;padding-right:10px;"><?php _e('All','admin2020')?></button></li>

        <?php
        if($this->page == 'content'){

          foreach($this->attachment_type as $type){
            ?>
            <li><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: [admin2020_file_filter='<?php echo $type?>'];group: type" style="padding-left:10px;padding-right:10px;"><?php echo $type?></button></li>
            <?php

          }


        } else {
          ?>


          <li><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: [admin2020_file_filter='image'];group: type" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: image"></span></button></li>
          <li><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: [admin2020_file_filter='video'];group: type" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: video-camera"></span></button></li>
          <li><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: [admin2020_file_filter='audio'];group: type" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: microphone"></span></button></li>
          <li><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: [admin2020_file_filter='application'];group: type" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: file-pdf"></span></button></li>

          <li>
            <div style="height:100%;width:1px;margin-left:15px;margin-right:15px;background:rgba(197,197,197,0.2)"></div>
          </li>

          <li><button class="uk-button uk-button-primary" style="padding-left:10px;padding-right:10px;" type="button" href="#batch-rename" uk-toggle><span uk-icon="icon:file-edit"></span></button></li>

        <?php } ?>
        <li><button class="uk-button uk-button-danger hidden admin2020_delete_multiple" style="padding-left:10px;padding-right:10px;" type="button" onclick="admin2020_delete_multiple_attachment()"><span uk-icon="icon:trash"></span></button></li>
      </ul>

      <div id="batch-rename" class="uk-flex-top" uk-modal>
          <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">

              <button class="uk-modal-close-default" type="button" uk-close></button>

              <div class="uk-h4"><?php _e("Batch Rename",'admin2020')?></div>

              <form class="uk-form-stacked uk-grid-small" uk-grid>


                <div class="uk-width-1-1">
                    <label class="uk-form-label" for="form-stacked-select"><?php _e('Attribute to rename','admin2020')?></label>
                    <div class="uk-form-controls">
                        <select class="uk-select" id="form-stacked-select">
                            <option value="name"><?php _e('Name','admin2020')?></option>
                            <option value="alt"><?php _e('Alt Tag','admin2020')?></option>
                        </select>
                    </div>
                </div>

                <div class="uk-width-1-1">
                  <div class=""><?php _e('New Name','admin2020')?></div>
                </div>

                <div class="uk-width-1-3">
                    <div class="uk-form-controls">
                        <select class="uk-select" id="batch_name_chooser">
                            <option value="filename"><?php _e('Original Filename','admin2020')?></option>
                            <option value="text"><?php _e('Text','admin2020')?></option>
                            <option value="date"><?php _e('Date Uploaded','admin2020')?></option>
                            <option value="original_alt"><?php _e('Original Alt','admin2020')?></option>
                            <option value="extension"><?php _e('File Extension','admin2020')?></option>
                            <option value="sequence"><?php _e('Sequence Number','admin2020')?></option>
                            <option value="meta"><?php _e('Meta Value','admin2020')?></option>
                        </select>
                    </div>
                </div>

                <div class="uk-width-1-3">
                    <button class="uk-button uk-button-default" type="button" onclick="add_batch_rename_item()"><?php _e('Add','admin2020')?></button>
                </div>

                <div class="uk-width-1-1">
                  <hr style="margin: 30px 0;">
                </div>

                <div class="uk-width-1-1" id="batch_rename_builder" uk-sortable="handle: .rename_drag">

                </div>



                <div class="uk-width-1-1">
                  <hr style="margin: 30px 0 0 0;">
                </div>

                <div class="uk-width-2-3 uk-flex uk-flex-middle" >
                  <span><?php _e('Preview','admin2020')?>: </span>
                  <span class="uk-text-bold" id="batch_rename_preview"></span>
                </div>

                <div class="uk-width-1-3 uk-flex uk-flex-right">
                  <button class="uk-button uk-button-primary" type="button" onclick="batch_rename_process();"><?php _e('Rename','admin2020') ?></button>
                </div>

            </form>

          </div>
      </div>


    </div>
    <?php
  }

  public function build_media(){

    $args = array(
      'post_type' => $this->attachment_type,
      'post_status' => $this->post_status,
      'posts_per_page' => 30,
    );
    wp_reset_query();
    $attachments = new WP_Query($args);


    $this->build_attachment_list($attachments);

  }

  public function admin2020_build_media_page(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

      $page_now = $_POST['page_now'];


      if($page_now == "admin_2020_content"){


        $this->page = 'content';

        $content_page = true;

        $utils = new Admin2020_Util();
        $selected_post_types = $utils->get_option('admin2020_content_included_posts');

        if (!is_array($selected_post_types)){
          $selected_post_types = array("post","page");
        }

        $this->attachment_type = $selected_post_types;
        $this->post_status = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit');
      } else {

        $this->page = 'media';

        $content_page = false;
        $selected_post_types = 'attachment';

        $this->attachment_type = $selected_post_types;
        $this->post_status = 'inherit';

      }


      $filters = $_POST['filters'];

      $folderid = $filters['folderid'];
      $year = $filters['uploadyear'];
      $month = $filters['uploadmonth'];
      $username = $filters['uploaduser'];
      $search = $filters['searchterm'];

      $metaquery = array('relation' => 'AND');
      $datequery = array('relation' => 'AND');
      $thedate = array();

      if ($year != ""){

        $thedate['year'] = $year;

      }

      if ($month != ""){

        $date = date_parse($month);
        $thedate['month'] = $date['month'];

      }

      array_push($datequery,$thedate);

      if ($folderid != ""){

        array_push($metaquery,array(
              'key' => 'admin2020_folder',
              'value' => $folderid,
              'compare' => '=='
          )
        );

      }

      if ($username != ""){
        $user = get_user_by('login', $username);
        $authorid = $user->ID;
      } else {
        $authorid = "";
      }


      $args = array(
        'post_type' => $this->attachment_type,
        'post_status' => $this->post_status,
        'posts_per_page' => 30,
        's' => $search,
        'author' => $authorid,
        'meta_query' => $metaquery,
        'date_query' => $datequery,
        'paged' => $_POST['page'] + 1,
      );

    	$attachments = new WP_Query( $args );
      //echo json_encode($args);
      echo $this->build_attachment_list($attachments);

    }

    die();

  }

  public function admin2020_get_attachment_view(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

      $attachment_id = $_POST['id'];

      $posttype = get_post_type($attachment_id);

      if($posttype == 'attachment'){
        echo $this->admin2020_build_attachment_view($attachment_id);
      } else {
        echo $this->admin2020_build_post_view($attachment_id);
      }

    }

    die();

  }

  public function admin2020_build_post_view($attachment_id){

    $thepost = get_post($attachment_id);
    $post_url = get_the_permalink($attachment_id);
    $post_title = get_the_title($attachment_id);
    $date_format = get_option('date_format');
    $posted_date = get_the_date($date_format,$attachment_id);
    $author_id = $thepost->post_author;
    $user_info = get_userdata($author_id);
    $username = $user_info->user_login;
    $meta_string = $posted_date.', '.$username;

    $edit_url = get_edit_post_link($attachment_id);


    ?>


        <span style="display:none" id="admin2020_viewer_currentid"><?php echo $attachment_id?></span>
        <button class="uk-offcanvas-close" type="button" uk-close></button>

        <div style="float: left;position: relative;width: 100%;margin-bottom:30px;">

          <iframe src="<?php echo $post_url?>" id="admin2020_post_preview" width="100%" height="300" style="border:none;">
          </iframe>

          <button class="uk-position-small uk-position-center-left admin2020imageshift" type="button" onclick="switchinfo('left')">
            <span uk-icon="icon:chevron-left"></span>
          </button>
          <button class="uk-position-small uk-position-center-right admin2020imageshift" type="button" onclick="switchinfo('right')">
            <span uk-icon="icon:chevron-right"></span>
          </button>
        </div>

        <div class="uk-padding">

          <div class="uk-child-width-1-2">
          <ul class="uk-iconnav" style="float:left;">

            <?php if(current_user_can( 'edit_posts' , $attachment_id)){ ?>
              <li><a href="<?php echo $edit_url?>" id="admin2020_edit_post" uk-icon="icon: file-edit" uk-tooltip="<?php _e('Edit','admin2020')?>"></a></li>
            <?php } ?>
              <li><a href="#" id="admin2020_duplicate_post" uk-icon="icon: copy" uk-tooltip="<?php _e('Duplicate','admin2020')?>" onclick="admin2020_duplicate_post()"></a></li>
              <li><a href="<?php echo $post_url?>" target="_blank" id="admin2020_view_post" uk-icon="icon: link" uk-tooltip="<?php _e('View','admin2020')?>"></a></li>
              <li><a href="#" id="admin2020_expand_post" uk-icon="icon: expand" onclick="jQuery('#admin2020MediaViewer').toggleClass('admin2020_fullscreen');" uk-tooltip="<?php _e('Expand','admin2020')?>"></a></li>
              <li><a href="#" id="admin2020_shrink_post" uk-icon="icon: shrink" onclick="jQuery('#admin2020MediaViewer').toggleClass('admin2020_fullscreen');" uk-tooltip="<?php _e('Expand','admin2020')?>"></a></li>
            <?php if(current_user_can( 'delete_post' , $attachment_id)){ ?>
              <li style="padding-left: 15px;margin-left: 15px;border-left: 1px solid grey;"><a href="#" id="admin2020_delete_post" uk-icon="icon: trash" uk-tooltip="<?php _e('Delete','admin2020')?>" onclick="admin2020_delete_attachment()"></a></li>
            <?php } ?>
          </ul>
          <ul class="uk-iconnav" style="float:left;justify-content:flex-end;">
              <li><button class="uk-button uk-button-primary uk-button-small" id="admin2020_save_post" onclick="admin2020_save_post()"><?php _e('Save','admin2020')?></button></li>
          </ul>
        </div>

          <div class="uk-text-meta uk-margin-top uk-margin-top uk-width-1-1" id="admin2020_viewer_meta" style="float:left"><?php echo $meta_string?></div>
          <textarea class="uk-textarea admin2020_input_editable" id="admin2020_viewer_title" value="<?php echo $post_title?>"><?php echo $post_title?></textarea>




           <div onclick="admin_2020_enable_post_edit()" id="admin_2020_post_preview">
             <?php echo $thepost->post_content ?>
          </div>
        </div>

    <?php


  }

  public function admin2020_build_attachment_view($attachment_id){

    $attachment = get_post($attachment_id);
    $attchmenttitle = get_the_title($attachment_id);
    $attachment_url = wp_get_attachment_url($attachment_id);
    ///$fields = get_attachment_fields_to_edit($attachment_object);
    //return json_encode($fields);
    //$checker = get_attachment_fields_to_edit($attachment);
    //$fields = apply_filters('attachment_fields_to_save', $checker,wp_get_attachment_metadata($attachment_id));

    $postdate = $attachment->post_date;
    $attachmenttype = $attachment->post_mime_type;
    $pieces = explode("/", $attachmenttype);
    $maintype = $pieces[0];

    $alt_text = get_post_meta($attachment_id , '_wp_attachment_image_alt', true);

    $filesize = filesize( get_attached_file( $attachment_id ) );

    $attachmentinfo = wp_get_attachment_image_src($attachment_id, 'full');
    $width = $attachmentinfo[1];
    $height = $attachmentinfo[2];

    if (strpos($attachmenttype, 'image') !== false || strpos($attachmenttype, 'video') !== false ) {
      $dimensions = $width."x".$height;
    } else {
      $dimensions = false;
    }

    $utils = new Admin2020_Util();
    $filesize = $utils->formatBytes($filesize);

    ?>

            <span style="display:none" id="admin2020_viewer_currentid"><?php echo $attachment_id?></span>
            <button class="uk-offcanvas-close" type="button" uk-close></button>

            <div style="float: left;position: relative;width: 100%;">


              <?php
              if (strpos($attachmenttype, 'image') !== false) {

                ?><img id="admin2020imgViewer" src="<?php echo $attachment_url?>" class="uk-image" style="width:100%;"></><?php

              } else if (strpos($attachmenttype, 'video') !== false) {

                ?><video id="admin2020videoViewer" src="<?php echo $attachment_url?>" playsinline controls uk-video="autoplay: false" style="width:100%;"></video><?php

              } else if (strpos($attachmenttype, 'application') !== false) {

                ?><div id="admin2020docViewer" class="uk-flex uk-flex-center uk-flex-middle  uk-margin-bottom" style="width:100%;height:200px;float:left;">
                  <span uk-icon="icon: file-pdf;ratio:4"></span>
                </div><?php

              } else if (strpos($attachmenttype, 'audio') !== false) {

                ?><div class="uk-padding" id="admin2020audioViewer" style="width:100%;display:none;float:left">
                  <video id="admin2020audioplayer" src="<?php echo $attachment_url?>" playsinline controls uk-video="autoplay: false" style="min-width:100%;float:left;width:100%;"></video>
                </div><?php

              }
              ?>

              <button class="uk-position-small uk-position-center-left admin2020imageshift" type="button" onclick="switchinfo('left')">
                <span uk-icon="icon:chevron-left"></span>
              </button>
              <button class="uk-position-small uk-position-center-right admin2020imageshift" type="button" onclick="switchinfo('right')">
                <span uk-icon="icon:chevron-right"></span>
              </button>

              <?php
              if (strpos($attachmenttype, 'audio') !== false || strpos($attachmenttype, 'video') !== false || strpos($attachmenttype, 'application') !== false) {

              } else {
                ?>
                <div class="uk-position-bottom uk-width-1-1" style="background:rgba(0,0,0,0.4);padding: 15px 40px;">
                  <h4 class="uk-margin-remove" style="float:left;max-width:80%" id=""><?php echo $attchmenttitle?></h4>
                  <div uk-lightbox>
                      <a style="float:right" href="<?php echo $attachment_url?>" ><span uk-icon="expand"></span></a>
                  </div>
                </div>
                <?php
              } ?>
            </div>

            <div style="float:left;width:100%;position:relative;"  class="uk-margin-bottom">
              <div class="admin2020loaderwrap" id="admin2020_media_loader" style="display: none;top:0;">
                <div class="admin2020loader"></div>
              </div>
            </div>

            <div class="uk-padding">



              <ul uk-tab>
                  <li><a href="#"><?php _e("Attributes")?></a></li>
                  <li><a href="#"><?php _e("Meta")?></a></li>
                  <?php
                  if (strpos($attachmenttype, 'image') !== false) {
                   ?>
                    <li><a href="#" onclick="edit_image('<?php echo $attachment_url?>','<?php echo $attchmenttitle?>')" ><?php _e("Edit","admin2020") ?> </a></li>
                  <?php }?>
              </ul>

              <ul class="uk-switcher uk-margin">

                  <li><!-- SETTINGS -->


                    <form class="uk-form-stacked" style="margin-top:40px;">
                      <div uk-grid class="uk-grid-small">

                        <div class="uk-width-1-2">
                            <label class="uk-form-label" for="form-stacked-text"><?php _e('Title','admin2020')?></label>
                            <div class="uk-form-controls">
                                <input class="uk-input" id="admin2020_viewer_input_title" type="text" placeholder="Title..." value="<?php echo esc_html($attchmenttitle)?>">
                            </div>
                        </div>

                        <div class="uk-width-1-2">
                            <label class="uk-form-label" for="form-stacked-text"><?php _e('Alt Text','admin2020')?></label>
                            <div class="uk-form-controls">
                                <input class="uk-input" id="admin2020_viewer_altText" type="text" placeholder="Alt Text" value="<?php echo esc_html($alt_text)?>">
                            </div>
                        </div>

                        <div class="uk-width-1-1">
                            <label class="uk-form-label" for="form-stacked-text"><?php _e('Caption','admin2020')?></label>
                            <div class="uk-form-controls">
                                <textarea class="uk-input" style="height:60px;" rows="2" id="admin2020_viewer_caption" type="text" placeholder="Caption..."><?php echo esc_html($attachment->post_excerpt);?></textarea>
                            </div>
                        </div>

                        <div class="uk-width-1-1">
                            <label class="uk-form-label" for="form-stacked-text"><?php _e('Description','admin2020')?></label>
                            <div class="uk-form-controls">
                                <textarea class="uk-input" style="height:60px;" rows="2" id="admin2020_viewer_description" type="text" placeholder="Description..."><?php echo esc_html($attachment->post_content)?></textarea>
                            </div>
                        </div>


                        <div class="uk-width-1-1">
                            <label class="uk-form-label" for="form-stacked-text">URL</label>
                            <div class="uk-form-controls">
                              <div class="uk-inline uk-width-1-1" onclick="copythis(this)" style="cursor:pointer">
                                <span class="uk-form-icon" uk-icon="icon:copy"></span>
                                <input class="uk-input" id="admin2020_viewer_fullLink" value="<?php echo $attachment_url?>">
                              </div>
                              <span class="uk-text-success" id="linkcopied" style="display:none;float:left;margin-top:15px"><?php _e('Link copied to clipboard','admin2020')?></span>
                            </div>
                        </div>

                      </div>

                      <div class="uk-margin-large uk-margin-remove-bottom">
                        <button class="uk-button uk-button-secondary" type="button" onclick="admin2020_save_attachment(<?php echo $attachment_id?>)"><?php _e('Save','admin2020')?></button>
                        <button class="uk-button uk-button-danger uk-align-right uk-margin-remove" type="button" onclick="admin2020_delete_attachment()"><span  uk-icon="icon:trash"></span></button>
                      </div>

                    </form>
                  </li><!-- END OF SETTINGS -->

                  <li><!-- META -->

                    <div id="admin2020MainMeta" class="" style="margin-top:40px;">


                      <table class="uk-table uk-table-small">
                        <tbody>
                            <tr>
                                <td><?php _e('File Type',"admin2020")?>:</td>
                                <td><?php echo $maintype?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Uploaded',"admin2020")?>:</td>
                                <td><?php echo get_the_date(get_option('date_format'),$attachment_id)?></td>
                            </tr>
                            <tr>
                                <td><?php _e('Size',"admin2020")?>:</td>
                                <td><?php echo $filesize?></td>
                            </tr>
                            <?php
                            if ($dimensions != false){ ?>
                              <tr>
                                  <td><?php _e('Dimensions',"admin2020")?>:</td>
                                  <td><?php echo $dimensions?></td>
                              </tr>
                            <?php }?>
                        </tbody>
                    </table>

                    </div>

                  </li><!-- END OF META -->


                  <li><!-- EDIT -->


                  </li><!-- END OF EDIT -->

              </ul>





            </div>


    <?php

  }

  public function admin2020_build_media_filter(){

    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-media-security-nonce', 'security') > 0) {

      $filters = $_POST['filters'];
      $page_id = $_POST['page_id'];

      $folderid = $filters['folderid'];
      $year = $filters['uploadyear'];
      $month = $filters['uploadmonth'];
      $username = $filters['uploaduser'];
      $search = $filters['searchterm'];


      if($page_id == "admin_2020_content"){


        $this->page = 'content';

        $content_page = true;

        $utils = new Admin2020_Util();
        $selected_post_types = $utils->get_option('admin2020_content_included_posts');

        if (!is_array($selected_post_types)){
          $selected_post_types = array("post","page");
        }

        $this->attachment_type = $selected_post_types;
        $this->post_status = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private', 'inherit');
      } else {

        $this->page = 'media';

        $content_page = false;
        $selected_post_types = 'attachment';

        $this->attachment_type = $selected_post_types;
        $this->post_status = 'inherit';

      }

      $metaquery = array('relation' => 'AND');
      $datequery = array('relation' => 'AND');
      $thedate = array();

      if ($year != ""){

        $thedate['year'] = $year;

      }

      if ($month != ""){

        $date = date_parse($month);
        $thedate['month'] = $date['month'];

      }

      array_push($datequery,$thedate);

      if ($folderid != ""){

        array_push($metaquery,array(
              'key' => 'admin2020_folder',
              'value' => $folderid,
              'compare' => '=='
          )
        );

      }

      if ($username != ""){
        $user = get_user_by('login', $username);
        $authorid = $user->ID;
      } else {
        $authorid = "";
      }



      $args = array(
        'post_type' => $this->attachment_type,
        'post_status' => $this->post_status,
        'posts_per_page' => 30,
        's' => $search,
        'author' => $authorid,
        'meta_query' => $metaquery,
        'date_query' => $datequery,
        'paged' => 1,
      );

      wp_reset_query();
      $attachments = new WP_Query( $args );
      //echo json_encode($args);
      echo $this->build_attachment_list($attachments);

    }

    die();

  }

  public function build_attachment_list($attachments){

    $tracker = "";
    $count = 0;

      if ( $attachments->have_posts() ) {

        while ( $attachments->have_posts() ) {

          $attachments->the_post();
          $attachmentid = get_the_id();
          $attachment = get_post($attachmentid);



          $postdate = $attachment->post_date;
          $attachmenttype = $attachment->post_mime_type;
          $pieces = explode("/", $attachmenttype);
          $maintype = $pieces[0];

          if (date('d/m/Y',strtotime($postdate)) == date('d/m/Y')){
            $stamp = "Today";
          } else {
            $stamp = human_time_diff( date('U',strtotime($postdate)), current_time('timestamp') )  . ' ago';
          }

          if ($stamp != $tracker){
            ?>
            <div admin2020_file_size_order="" admin2020_uploaded_on="<?php echo date("Y-m-d",strtotime($postdate))?>" class="uk-width-1-1 uk-text-meta admin2020dateSep"><?php echo $stamp ?></div><?php
          }

          $tracker = $stamp;

          $this->build_single_attachment($attachmentid);

          $count = $count + 1;

        }
      }
  }

  public function build_single_post($attachmentid){

    $attachment = get_post($attachmentid);
    $caption = $attachment->post_excerpt;
    $filesize = filesize( get_attached_file( $attachment->ID ) );

    $post_type = get_post_type($attachmentid);

    $posttitle = get_the_title($attachment->ID);
    $postdate = $attachment->post_date;
    $uploadedon = date('Y-m-d',strtotime($postdate));
    $meta_date = date(get_option('date_format'),strtotime($postdate));
    $month =date('F',strtotime($postdate));
    $year =date('Y',strtotime($postdate));
    $attachmentFullSize = wp_get_attachment_url($attachmentid);
    $alt_text = get_post_meta($attachmentid , '_wp_attachment_image_alt', true);
    $folders = get_post_meta($attachmentid , 'admin2020_folder', true);
    $userid = $attachment->post_author;
    $user = get_user_by('ID',$userid);
    $post_thumbnail = get_the_post_thumbnail_url($attachment->ID);
    $post_link = get_permalink($attachment->ID);
    $post_edit_link = get_edit_post_link($attachment->ID);
    $content_post = get_post($attachment->ID);
    $content = $content_post->post_content;
    $content = apply_filters('the_content', $content);

    $metacontent = $meta_date." by ".$user->display_name;

    $canedit = current_user_can('edit_post', $attachment->ID);
    $candelete = current_user_can('delete_post', $attachment->ID);

    $status = $attachment->post_status;
    $fullstatus = get_post_status_object($status);
    $fullstatus = $fullstatus->label;

    $folders = get_post_meta($attachmentid , 'admin2020_folder', true);
    $color = get_post_meta($folders,"color_tag",true);
    ?>
    <div draggable="true" ondragstart="admin2020mediadrag(event)"
    id="attachment<?php echo esc_html($attachment->ID)?>"
    admin2020_filename="<?php echo esc_html($attachment->post_title)?>"
    admin2020_folders="<?php echo esc_html($folders)?>"
    admin2020_file_filter="<?php echo esc_html($post_type)?>"
    admin2020_attachmentid="<?php echo esc_html($attachment->ID)?>"
    admin2020_uploaded_on="<?php echo esc_html($uploadedon)?>"
    admin2020_status_filter="<?php echo esc_html($fullstatus)?>"
    admin2020_month_filter="<?php echo esc_html($month)?>"
    admin2020_year_filter="<?php echo esc_html($year)?>"
    admin2020_user_filter="<?php echo esc_html($user->display_name)?>"
    class="admin2020_attachment admin_2020_content_item" onclick="admin2020_attachment_info(this,event)">

      <div class="uk-card uk-card-default">

        <div class="uk-position-top-left uk-padding-small admin2020_media_select_holder" style="z-index: 1;">
          <input admin2020_attachmentid="<?php echo esc_html($attachmentid)?>" type="checkbox" class="uk-input admin2020_media_select" onclick="admin2020_multiple_select()">
        </div>

          <div class="uk-card-media-top">
            <?php
            if(!$post_thumbnail){
              ?><div class="uk-background-muted uk-flex uk-flex-center uk-padding">
                <span uk-icon="icon:file-text;ratio:2"></span>
              </div><?php
            } else {
              ?><img draggable="false" src="<?php echo $post_thumbnail?>" alt="post_thumbnail"><?php
            }
            ?>

          </div>
          <div class="uk-card-body uk-padding-small">
              <h5 class="uk-margin-remove-bottom"><?php echo esc_html($posttitle)?></h5>
              <span class="uk-text-meta"><?php echo esc_html($meta_date)?></span>

              <div class="admin2020_status_holder">
                <span class="uk-label uk-margin-small-top"><?php echo esc_html(strtoupper($post_type))?></span>
                <?php
                if ($attachment->post_status == "draft" || $attachment->post_status == "auto-draft"){
                  ?><span class="uk-label uk-margin-small-top admin2020_poststatus" style="background:#faa05a"><?php echo esc_html(strtoupper($attachment->post_status))?></span><?php
                }
                ?>
              </div>

          </div>
      </div>

      <div class="uk-position-small uk-position-bottom-right">
        <span class="folder_color_icon" style="background:<?php echo $color?>"></span>
      </div>

    </div>

    <?php

  }


  public function build_single_attachment($attachmentid){


    $post_type = get_post_type($attachmentid);

    if($post_type != 'attachment'){
      return $this->build_single_post($attachmentid);
    }

    $tracker = "";
    $count = 0;

    $attachment = get_post($attachmentid);



    $postdate = $attachment->post_date;
    $attachmenttype = $attachment->post_mime_type;
    $pieces = explode("/", $attachmenttype);
    $maintype = $pieces[0];

    if (date('d/m/Y',strtotime($postdate)) == date('d/m/Y')){
      $stamp = "Today";
    } else {
      $stamp = human_time_diff( date('U',strtotime($postdate)), current_time('timestamp') )  . ' ago';
    }



    $tracker = $stamp;

    //$attachmentmeta = wp_get_attachment_metadata( $attachment->ID);
    //print_r($attachmentmeta);
    //echo '<pre>' . print_r( $attachmentmeta, true ) . '</pre>';
    $caption = $attachment->post_excerpt;
    $filesize = filesize( get_attached_file( $attachment->ID ) );

    $attachmentid = $attachment->ID;
    $postdate = $attachment->post_date;
    $uploadedon =date('Y-m-d',strtotime($postdate));
    $month =date('F',strtotime($postdate));
    $year =date('Y',strtotime($postdate));
    $attachmentFullSize = wp_get_attachment_url($attachmentid);
    $alt_text = get_post_meta($attachmentid , '_wp_attachment_image_alt', true);
    $folders = get_post_meta($attachmentid , 'admin2020_folder', true);
    $color = get_post_meta($folders,"color_tag",true);
    $userid = $attachment->post_author;
    $user = get_user_by('ID',$userid);



    if (strpos($attachmenttype, 'image') !== false) {

      $attachmentinfo = wp_get_attachment_image_src($attachmentid, 'medium');
      $src = $attachmentinfo[0];
      $width = $attachmentinfo[1];
      $height = $attachmentinfo[2];
      $dimensions = $width."px ".$height."px";
      ?>
      <div draggable="true" ondragstart="admin2020mediadrag(event)" id="attachment<?php echo esc_html($attachmentid)?>" admin2020_filename="<?php echo esc_html($attachment->post_title)?>"
      admin2020_folders="<?php echo esc_html($folders)?>"
      admin2020_attachmentid="<?php echo esc_html($attachmentid)?>"
      admin2020_uploaded_on="<?php echo esc_html($uploadedon)?>"
      admin2020_file_size_order="<?php echo esc_html($filesize)?>"
      admin2020_file_filter="<?php echo esc_html($maintype)?>"
      admin2020_month_filter="<?php echo esc_html($month)?>"
      admin2020_year_filter="<?php echo esc_html($year)?>"
      admin2020_user_filter="<?php echo esc_html($user->display_name)?>"
      class="admin2020_attachment" onclick="admin2020_attachment_info(this,event)">
        <div style="position:relative;height:100%">
          <img data-src="<?php echo $src?>" class="admin2020_attachment_preview" style="max-height:150px" uk-img>
          <div class="admin2020_meta">
            <div class="uk-position-top-left uk-padding-small admin2020_media_select_holder" style="z-index: 1;">
              <input admin2020_attachmentid="<?php echo esc_html($attachmentid)?>" type="checkbox" class="uk-input admin2020_media_select" onclick="admin2020_multiple_select()">
            </div>
            <div class="uk-position-bottom uk-padding-small admin2020attachmenttext uk-light">
              <span class="title"><?php echo esc_html($attachment->post_title)?></span>
              <span class="uk-text-meta"><?php echo esc_html($attachment->post_excerpt)?></span>
            </div>
          </div>
        </div>
        <div class="uk-position-small uk-position-bottom-right">
          <span class="folder_color_icon" style="background:<?php echo $color?>"></span>
        </div>
      </div>
      <?php
    } else if (strpos($attachmenttype, 'video') !== false) {
      $attachmentid = $attachment->ID;
      $src = $attachment->guid;
      //echo $video_meta['length_formatted'];
      ?>
      <div draggable="true" ondragstart="admin2020mediadrag(event)" id="attachment<?php echo esc_html($attachmentid)?>" admin2020_filename="<?php echo esc_html($attachment->post_title)?>"
      admin2020_folders="<?php echo esc_html($folders)?>"
      admin2020_attachmentid="<?php echo esc_html($attachmentid)?>"
      admin2020_uploaded_on="<?php echo esc_html($uploadedon)?>"
      admin2020_file_size_order="<?php echo esc_html($filesize)?>"
      admin2020_file_filter="<?php echo esc_html($maintype)?>"
      admin2020_month_filter="<?php echo esc_html($month)?>"
      admin2020_year_filter="<?php echo esc_html($year)?>"
      admin2020_user_filter="<?php echo esc_html($user->display_name)?>"
      class="admin2020_attachment" onclick="admin2020_attachment_info(this,event)">
          <div style="position: relative;height:100%">
            <video class="admin2020_attachment_preview" src="<?php echo $src?>" playsinline controls uk-video="autoplay: false" style="max-height:150px"></video>
            <div class="admin2020_meta">
              <div class="uk-position-top-left uk-padding-small admin2020_media_select_holder" style="z-index: 1;">
                <input admin2020_attachmentid="<?php echo esc_html($attachmentid)?>" type="checkbox" class="uk-input admin2020_media_select" onclick="admin2020_multiple_select()">
              </div>
              <div class="uk-position-bottom uk-padding-small admin2020attachmenttext uk-light">
                <span class="title"><?php echo esc_html($attachment->post_title)?></span>
                <span class="uk-text-meta"><?php echo esc_html($attachment->post_excerpt)?></span>
              </div>
            </div>
         </div>
         <div class="uk-position-small uk-position-bottom-right">
           <span class="folder_color_icon" style="background:<?php echo $color?>"></span>
         </div>
      </div>
      <?php
    } else if (strpos($attachmenttype, 'application') !== false) {
      $attachmentid = $attachment->ID;
      $src = $attachment->guid;
      //echo $video_meta['length_formatted'];
      ?>
      <div draggable="true" ondragstart="admin2020mediadrag(event)" id="attachment<?php echo esc_html($attachmentid)?>" admin2020_filename="<?php echo esc_html($attachment->post_title)?>"
      admin2020_folders="<?php echo esc_html($folders)?>"
      admin2020_attachmentid="<?php echo esc_html($attachmentid)?>"
      admin2020_uploaded_on="<?php echo esc_html($uploadedon)?>"
      admin2020_file_size_order="<?php echo esc_html($filesize)?>"
      admin2020_file_filter="<?php echo esc_html($maintype)?>"
      admin2020_month_filter="<?php echo esc_html($month)?>"
      admin2020_year_filter="<?php echo esc_html($year)?>"
      admin2020_user_filter="<?php echo esc_html($user->display_name)?>"
      class="admin2020_attachment" onclick="admin2020_attachment_info(this,event)">
          <div class="uk-flex uk-flex-center uk-flex-middle admin2020standardBackground admin2020_attachment_preview" style="height:150px;width:150px">
            <span uk-icon="icon: file-pdf;ratio:3"></span>
          </div>
          <div class="admin2020_meta" style="height:150px;width:150px">
            <div class="uk-position-top-left uk-padding-small admin2020_media_select_holder" style="z-index: 1;">
              <input admin2020_attachmentid="<?php echo esc_html($attachmentid)?>" type="checkbox" class="uk-input admin2020_media_select" onclick="admin2020_multiple_select()">
            </div>
            <div class="uk-position-bottom uk-padding-small admin2020attachmenttext uk-light">
              <span class="title"><?php echo esc_html($attachment->post_title)?></span>
              <span class="uk-text-meta"><?php echo esc_html($attachment->post_excerpt)?></span>
            </div>
          </div>
          <div class="uk-position-small uk-position-bottom-right">
            <span class="folder_color_icon" style="background:<?php echo $color?>"></span>
          </div>
      </div>
      <?php
    } else if (strpos($attachmenttype, 'audio') !== false) {
      $attachmentid = $attachment->ID;
      $src = $attachment->guid;
      //echo $video_meta['length_formatted'];
      ?>
      <div draggable="true" ondragstart="admin2020mediadrag(event)" id="attachment<?php echo esc_html($attachmentid)?>" admin2020_filename="<?php echo esc_html($attachment->post_title)?>"
      admin2020_folders="<?php echo esc_html($folders)?>"
      admin2020_attachmentid="<?php echo esc_html($attachmentid)?>"
      admin2020_uploaded_on="<?php echo esc_html($uploadedon)?>"
      admin2020_file_size_order="<?php echo esc_html($filesize)?>"
      admin2020_file_filter="<?php echo esc_html($maintype)?>"
      admin2020_month_filter="<?php echo esc_html($month)?>"
      admin2020_year_filter="<?php echo esc_html($year)?>"
      admin2020_user_filter="<?php echo esc_html($user->display_name)?>"
      class="admin2020_attachment" onclick="admin2020_attachment_info(this,event)">
          <div class="uk-flex uk-flex-center uk-flex-middle admin2020standardBackground admin2020_attachment_preview" style="height:150px;width:150px">
            <span uk-icon="icon: microphone;ratio:3"></span>
          </div>
          <div class="admin2020_meta" style="height:150px;width:150px">
            <div class="uk-position-top-left uk-padding-small admin2020_media_select_holder" style="z-index: 1;">
              <input admin2020_attachmentid="<?php echo esc_html($attachmentid)?>" type="checkbox" class="uk-input admin2020_media_select" onclick="admin2020_multiple_select()">
            </div>
            <div class="uk-position-bottom uk-padding-small admin2020attachmenttext uk-light">
              <span><?php echo esc_html($attachment->post_title)?></span>
              <span class="uk-text-meta"><?php echo esc_html($attachment->post_excerpt)?></span>
            </div>
          </div>
          <div class="uk-position-small uk-position-bottom-right">
            <span class="folder_color_icon" style="background:<?php echo $color?>"></span>
          </div>
      </div>
      <?php
    }

  }

  public function build_viewer(){

    ?>
    <div id="admin2020MediaViewer" uk-offcanvas="flip:true;overlay:true" >

      <div class="uk-offcanvas-bar uk-padding-remove uk-box-shadow-large"  style="width:700px;">

        <div id="admin2020MediaViewer_content">

        </div>

        <?php

        if($this->page == 'content'){

          ?><div class="uk-padding" style="padding-top:0;"><?php

            $content   = '';
            $editor_id = 'post_preview_editor';
            $settings = array(
                'media_buttons' => false,
                'tinymce' => array(
                  'toolbar1' => 'bold, italic, underline,|,fontsizeselect',
                  'toolbar2'=>false
                ),
            );

            wp_editor( $content, $editor_id, $settings );

          ?></div><?php

        }

        ?>

      </div>
    </div>

    <?php


  }


  public function build_uploader(){


    $maxupload = $this->file_upload_max_size();

    ?>

    <div class="admin2020_loader_wrapper" style="display:none" onclick="UIkit.offcanvas('#admin2020uploader').show();">
      <div class="admin2020uploadMonitor uk-box-shadow-large">
        <span class="status uk-text-meta ">
          <span uk-icon="icon: cloud-upload"></span>
          <span class="admin2020upstat"></span>
        </span>
      </div>
    </div>

    <div id="admin2020uploader" uk-offcanvas="flip:true;overlay:true" >
        <div class="uk-offcanvas-bar uk-box-shadow-large" style="width:500px;">

          <div class="js-upload uk-placeholder uk-text-center" id="admin2020droparea" style="border-radius:4px;">
              <span uk-icon="icon: cloud-upload"></span>
              <span class="uk-text-middle"><?php _e('Drop items here or','admin2020') ?> </span>
              <div uk-form-custom>
                  <input id="admin2020upload" type="file" multiple>
                  <span class="uk-link"><?php _e('select them','admin2020') ?></span>
              </div>
          </div>

          <div id="max-upload-size" style="display:none;"><?php echo $maxupload?></div>

          <div class="admin2020uploadItems uk-margin-large-top uk-grid-small uk-child-width-1-1" uk-grid>


          </div>


        </div>
    </div>


    <?php


  }

  public function loadscripts(){
    ?>
    <script>
    dropArea = document.getElementById('admin2020droparea');
    functions = ['dragenter', 'dragover', 'dragleave', 'drop'];
    ins = ['dragenter', 'dragover'];
    outs = ['dragleave', 'drop'];

    jQuery.each(functions, function(i, obj) {
      dropArea.addEventListener(obj, preventDefaults, false)
    })

    function preventDefaults (e) {
      e.preventDefault()
      e.stopPropagation()
    }

    jQuery.each(ins, function(i, obj) {
      dropArea.addEventListener(obj, highlight, false)
    })

    jQuery.each(outs, function(i, obj) {
      dropArea.addEventListener(obj, unhighlight, false)
    })

    function highlight(e) {
      dropArea.classList.add('highlight')
    }

    function unhighlight(e) {
      dropArea.classList.remove('highlight')
    }

    dropArea.addEventListener('drop', handleDrop, false)

    function handleDrop(e) {
      dt = e.dataTransfer
      files = dt

      preupload(files);
    }
    </script>
    <?php
  }


  public function formatBytes($size, $precision = 0){
      $base = log($size, 1024);
      $suffixes = array('', 'Kb', 'Mb', 'Gb', 'Tb');

      return round(pow(1024, $base - floor($base)), $precision) .' '. $suffixes[floor($base)];
  }


  public function file_upload_max_size() {
    static $max_size = -1;

    if ($max_size < 0) {

      $post_max_size = $this->parse_size(ini_get('post_max_size'));
      if ($post_max_size > 0) {
        $max_size = $post_max_size;
      }

      $upload_max = $this->parse_size(ini_get('upload_max_filesize'));
      if ($upload_max > 0 && $upload_max < $max_size) {
        $max_size = $upload_max;
      }
    }
    return $max_size;
  }

  public function parse_size($size) {
    $unit = preg_replace('/[^bkmgtpezy]/i', '', $size); // Remove the non-unit characters from the size.
    $size = preg_replace('/[^0-9\.]/', '', $size); // Remove the non-numeric characters from the size.
    if ($unit) {
      return round($size * pow(1024, stripos('bkmgtpezy', $unit[0])));
    } else {
      return round($size);
    }
  }

  function override_media_templates(){
      ?>
      <!-- BUILD FOLDERS IN MODAL -->
      <script type="text/html" id="tmpl-media-frame_custom">
      		<div class="media-frame-title" id="media-frame-title"></div>
      		<h2 class="media-frame-menu-heading"><?php _ex( 'Actions', 'media modal menu actions' ); ?></h2>
      		<button type="button" class="button button-link media-frame-menu-toggle" aria-expanded="false">
      			<?php _ex( 'Menu', 'media modal menu' ); ?>
      			<span class="dashicons dashicons-arrow-down" aria-hidden="true"></span>
      		</button>
      		<div class="media-frame-menu"></div>
      		<div class="media-frame-tab-panel">
      			<div class="media-frame-router"></div>

            <div class="uk-grid-collapse" uk-grid uk-filter="target: .attachments" style="max-height: 100%;overflow: auto;height:100%">
              <div class=" uk-padding" id="admin2020_settings_column" style="width:400px;">
                <div >

                  <div style="float:left;margin-top:80px;">
                    <ul class="uk-iconnav admin2020iconnav uk-margin">
                      <li ><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="group: type" style="padding-left:10px;padding-right:10px;">ALL</button></li>
                      <li ><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: .filter-image;group: type" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: image"></span></button></li>
                      <li ><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: .filter-video;group: type" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: video-camera"></span></button></li>
                      <li ><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: .filter-audio;group: type" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: microphone"></span></button></li>
                      <li ><button class="uk-button uk-button-default uk-visible@m" uk-filter-control="filter: .filter-application;group: type" style="padding-left:10px;padding-right:10px;"><span href="#" uk-icon="icon: file-pdf"></span></button></li>
                    </ul>

                    <ul class="uk-iconnav admin2020iconnav">
                      <div class="uk-margin">
                          <div class="uk-inline">
                              <span class="uk-form-icon" uk-icon="icon: search"></span>
                              <input class="uk-input" type="text" id="admin2020mediaSearchModal" onkeyup="admin2020searchAtachments(this)" placeholder="Search media..." autofocus>
                          </div>
                      </div>
                    </ul>
                  </div>

                  <div id="admin2020folderswrap" style="width:100%">
                    <?php
                    $admin2020_folders = new Admin_2020_Folders($this->version);
                    $admin2020_folders->build_folder_panel('modal');
                    ?>
                  </div>
                </div>
              </div>
              <div class="uk-width-expand uk-overflow-hidden" style="max-height:100%;" >
          			<div class="media-frame-content"></div>
              </div>
            </div>
      		</div>
      		<h2 class="media-frame-actions-heading screen-reader-text">
      		<?php
      			/* translators: Accessibility text. */
      			_e( 'Selected media actions' );
      		?>
      		</h2>
      		<div class="media-frame-toolbar"></div>
      		<div class="media-frame-uploader"></div>
      	</script>
      <script>
          jQuery(document).ready( function($) {

              if( typeof wp.media.view.Attachment != 'undefined' ){
                  //console.log(wp.media.view);
                  //wp.media.view.Attachment.prototype.template = wp.media.template( 'attachment_custom' );
                  wp.media.view.MediaFrame.prototype.template = wp.media.template( 'media-frame_custom' );

                  wp.media.view.Attachment.Library = wp.media.view.Attachment.Library.extend({
                    className: function () { return 'attachment folder' + this.model.get( 'folderid' ); },
                    folderName: function () { return 'attachment ' + this.model.get( 'folderid' ); },
                  });

                  wp.media.view.Modal.prototype.on('open', function() {
                    //MODAL OPEN
              			//refreshFolderCountModal();
              		});


              }
          });
      </script>
      <?php
  }


  public function load_scroll_helper(){
    ?>
    <script type="text/javascript">

    jQuery(function($){
    	var canBeLoaded = true,
    	bottomOffset = 1500;


    	$(window).scroll(function(){


    		if( $(document).scrollTop() > ( $(document).height() - bottomOffset ) && canBeLoaded == true ){

          if(!ma_admin_media_ajax.current_page){
            ma_admin_media_ajax.current_page = 1;
          }

          $.ajax({
              url: ma_admin_ajax.ajax_url,
              type: 'post',
              data: {
                  action: 'admin2020_build_media_page',
                  security: ma_admin_media_ajax.security,
                  query: ma_admin_media_ajax.posts,
                  page: ma_admin_media_ajax.current_page,
                  filters:  get_active_filters(),
                  page_now: ma_admin_media_ajax.page_now
              },
              beforeSend: function( xhr ){
                canBeLoaded = false;
                $(".admin2020loaderwrap").show();
              },
              success: function(response) {


                if( response ) {
                  canBeLoaded = true;
                  ma_admin_media_ajax.current_page++;
                  jQuery('.admin2020_media_gallery').append(response);
                } else {
                  canBeLoaded = false;
                }
                $(".admin2020loaderwrap").hide();
              }
          });

    		}
    	});
    });

    </script>

    <?php
  }

  /////END OF CLASS

}
