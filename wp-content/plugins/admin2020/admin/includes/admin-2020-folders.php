<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Folders{

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function run(){

      add_action( 'init', array($this,'admin2020_create_folders_cpt') );
      ///FOLDER AJAX
      add_action('wp_ajax_admin2020_create_folder', array($this,'admin2020_create_folder'));
      add_action('wp_ajax_admin2020_delete_folder', array($this,'admin2020_delete_folder'));
      add_action('wp_ajax_admin2020_rename_folder', array($this,'admin2020_rename_folder'));
      add_action('wp_ajax_admin2020_move_to_folder', array($this,'admin2020_move_to_folder'));
      add_action('wp_ajax_admin2020_move_folder_into_folder', array($this,'admin2020_move_folder_into_folder'));

      add_action('wp_ajax_admin2020_refresh_all_folders', array($this,'admin2020_refresh_all_folders'));

  }



  public function admin2020_create_folders_cpt(){

     $labels = array(
      'name'               => _x( 'Folder', 'post type general name', 'admin2020' ),
      'singular_name'      => _x( 'folder', 'post type singular name', 'admin2020' ),
      'menu_name'          => _x( 'Folders', 'admin menu', 'admin2020' ),
      'name_admin_bar'     => _x( 'Folder', 'add new on admin bar', 'admin2020' ),
      'add_new'            => _x( 'Add New', 'folder', 'admin2020' ),
      'add_new_item'       => __( 'Add New Folder', 'admin2020' ),
      'new_item'           => __( 'New Folder', 'admin2020' ),
      'edit_item'          => __( 'Edit Folder', 'admin2020' ),
      'view_item'          => __( 'View Folder', 'admin2020' ),
      'all_items'          => __( 'All Folders', 'admin2020' ),
      'search_items'       => __( 'Search Folders', 'admin2020' ),
      'not_found'          => __( 'No Folders found.', 'admin2020' ),
      'not_found_in_trash' => __( 'No Folders found in Trash.', 'admin2020' )
    );
     $args = array(
      'labels'             => $labels,
      'description'        => __( 'Description.', 'Add New Folder' ),
      'public'             => false,
      'publicly_queryable' => false,
      'show_ui'            => false,
      'show_in_menu'       => false,
      'query_var'          => false,
      'has_archive'        => false,
      'hierarchical'       => false,
    );
    register_post_type( 'admin2020folders', $args );
  }



  public function build_folder_panel($view = null){

    $this->view = $view;

    ?>
      <div class="uk-grid-small" uk-grid>


        <?php
        if($this->view != 'modal'){
          echo $this->get_add_new_folder();
        }
        ?>

        <?php echo $this->get_default_folders()?>

        <div class="uk-width-1-1"><hr></div>

        <div id="admin2020folderswrap">

          <?php $this->get_user_folders()?>

        </div>

      </div>

    <?php

  }


  public function get_default_folders(){


    $attachment_count = wp_count_attachments();
    $total = 0;


    foreach($attachment_count as $count){
      $total += $count;
    }

    if(isset($_GET['page'])){
      if ($_GET['page'] == 'admin_2020_content'){

        $utils = new Admin2020_Util();
        $selected_post_types = $utils->get_option('admin2020_content_included_posts');

        if ($selected_post_types == ""){
    			$selected_post_types = array("post","page");
    		}

        $total = 0;
    		foreach($selected_post_types as $type){
          $total += wp_count_posts($type)->publish;
          $total += wp_count_posts($type)->future;
          $total += wp_count_posts($type)->draft;
          $total += wp_count_posts($type)->pending;
          $total += wp_count_posts($type)->private;
        }
      }
    }


    ?>

    <div class="admin2020allFolders uk-width-1-1" onclick="media_folder_change('')">

      <div class="uk-grid-small" uk-grid>

        <div class="uk-width-expand">
          <a uk-filter-control="group: folders" href="#" class="admin2020folderTitle uk-link-muted">
            <span class="uk-icon-button uk-margin-small-right" style="width:25px;height:25px;background:rgba(197,197,197,0.2)" uk-icon="icon:folder;ratio:0.8"></span>
            <?php _e('All','admin2020') ?>
          </a>
        </div>

        <div class="uk-width-auto uk-flex uk-flex-middle uk-flex-right">
          <span class="uk-icon-button uk-text-meta" style="width:25px;height:25px;background:rgba(197,197,197,0.2)"><?php echo number_format($total)?></span>
        </div>

      </div>
      <?php if($this->view != 'modal'){ ?>
      <div class="uk-grid-small" uk-grid>

        <div class="uk-width-expand">

          <div class="uk-width-1-1 admin2020allFolders" onclick="media_folder_change('')">
            <a uk-filter-control="filter: [admin2020_folders=''];group: folders" href="#" class="admin2020folderTitle uk-link-muted uk-width-1-1">
              <span class="uk-icon-button uk-margin-small-right" style="width:25px;height:25px;background:rgba(197,197,197,0.2)" uk-icon="icon:folder;ratio:0.8"></span>
              <?php _e('Uncategorised','admin2020') ?>
            </a>
          </div>

        </div>
      </div>
    <?php } ?>

    </div>
    <?php


  }

  public function get_add_new_folder(){
    ?>
    <div class="uk-margin-bottom">

      <button class="uk-button uk-button-default " uk-toggle="target: .admin2020createfolder"><?php _e('New Folder','admin2020') ?></button>

      <div  class="uk-flex-top admin2020createfolder"  uk-modal>
        <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
          <div class="uk-h4"><?php _e('New Folder','admin2020')?></div>
          <input type="text" class="uk-input uk-margin-bottom" id="foldername" placeholder="<?php _e('Folder Name','admin2020')?>">
          <span class="uk-text-meta"><?php _e('Colour Tag','admin2020')?></span>

          <div class="uk-margin uk-child-width-auto" id="admin2020_foldertag">
            <input class="uk-radio" type="radio" name="color_tag" checked value="#1e87f0" style="border-color:#1e87f0;background-color:#1e87f0">
            <input class="uk-radio" type="radio" name="color_tag" value="#32d296" style="border-color:#32d296;background-color:#32d296">
            <input class="uk-radio" type="radio" name="color_tag" value="#faa05a" style="border-color:#faa05a;background-color:#faa05a">
            <input class="uk-radio" type="radio" name="color_tag" value="#f0506e" style="border-color:#f0506e;background-color:#f0506e">
            <input class="uk-radio" type="radio" name="color_tag" value="#ff9ff3" style="border-color:#ff9ff3;background-color:#ff9ff3">
          </div>

          <button class="uk-button uk-button-primary uk-width-1-1" onclick="admin2020newfolder()" type="button"><?php _e('Create','admin2020') ?></button>
        </div>

      </div>
    </div>
    <?php
  }


  public function foldertemplate($folder,$folders,$request_page = null){

    $foldercolor = get_post_meta($folder->ID, "color_tag",true);
    $top_level = get_post_meta($folder->ID, "parent_folder",true);
    $folder_id = $folder->ID;
    $the_class = '';
    $post_type = 'attachment';
    $ondrop = 'admin2020mediadrop';
    $onclick = "media_folder_change(". $folder->ID.")";
    $this->post_status = 'inherit';

    if(isset($_GET['page'])){
      $checker = $_GET['page'];
    } else {
      $checker = '';
    }


    if ($request_page == "admin_2020_content" || $checker == 'admin_2020_content'){

      $utils = new Admin2020_Util();
      $selected_post_types = $utils->get_option('admin2020_content_included_posts');

      if (!is_array($selected_post_types)){
  			$selected_post_types = array("post","page");
  		}

      $total = 0;
      $post_type = array();
  		foreach($selected_post_types as $type){
        array_push($post_type,$type);
      }

      $ondrop = 'admin2020postdrop';
      $onclick = "";

      $this->post_status = array('publish', 'pending', 'draft', 'auto-draft', 'future', 'private');
    }


    $args = array(
      'post_type' => $post_type,
      'post_status' => $this->post_status,
      'posts_per_page' => -1,
      'fields' => 'ids',
      'meta_query' => array(
       array(
               'key' => 'admin2020_folder',
               'value' => $folder_id,
               'compare' => '=',
           )
       )
    );

    $theattachments = get_posts( $args );

    if($theattachments){
      $folder_count = number_format(count($theattachments));
    } else {
      $folder_count = 0;
    }

    if(!$foldercolor){
      $foldercolor = '#1e87f0';
    }

    if(!$top_level){
      $the_class = 'admin2020_top_level_folder';
    }

    $count = 0;
    foreach($folders as $sub_folder){

      $parent_folder = get_post_meta($sub_folder->ID, "parent_folder",true);

      if($parent_folder == $folder_id){
        $count = $count + 1;
      }

    }
    $filter_string = "[admin2020_folders='".$folder->ID."']";

    if($this->view == 'modal'){

      $filter_string = ".folder".$folder->ID;

    }

    ob_start();

    ?>
    <div class="admin2020folder <?php echo $the_class.' '.$request_page?>" folder-id="<?php echo $folder->ID?>" ondrop="<?php echo $ondrop?>(event)" ondragover="admin2020mediaAllowDrop(event)" ondragleave="admin2020mediaDropOut(event)"
       draggable="true" ondragstart="admin2020folderdrag(event)" id="folder<?php echo $folder->ID?>">
      <div class="uk-grid-small" ondblclick="admin2020_edit_folder(<?php echo $folder->ID?>)" uk-grid>

        <div class="uk-width-auto uk-flex uk-flex-middle">
          <span class="uk-icon-button" style="width:25px;height:25px;background:rgba(197,197,197,0.2)" uk-icon="icon:folder;ratio:0.8"></span>
        </div>

        <div class="uk-width-auto uk-flex uk-flex-middle">
          <span class="folder_tag" style="width:10px;height:10px;border-radius: 50%;background-color:<?php echo $foldercolor?>" value="<?php echo $foldercolor?>"></span>
        </div>

        <div class="uk-width-expand uk-flex uk-flex-middle">
          <a class="uk-link-muted folder_title" href="#" onclick="<?php echo $onclick?>" uk-filter-control="filter: <?php echo $filter_string?>;group: folders"><?php echo $folder->post_title ?></a>
        </div>

        <div class="uk-width-auto uk-flex uk-flex-right uk-flex-middle">
          <span class="uk-icon-button uk-text-meta" style="width:25px;height:25px;background:rgba(197,197,197,0.2)"><?php echo $folder_count?></span>
        </div>


        <div class="uk-width-auto uk-flex uk-flex-right uk-flex-middle">
          <?php if($count > 0) { ?>
            <span class="folder_icon"  onclick="jQuery(this).parent().parent().parent().toggleClass('sub_open');" uk-icon="chevron-down"></span>
          <?php } else { ?>
            <span class="folder_icon"  style="width:20px;height:20px;"></span>
          <?php } ?>

        </div>

      </div>

      <?php


      if($count > 0){
        ?>
        <div class="admin_folders_sub">
          <?php
            foreach($folders as $sub_folder){

              $parent_folder = get_post_meta($sub_folder->ID, "parent_folder",true);

              if($parent_folder == $folder_id){

                echo $this->foldertemplate($sub_folder,$folders,$request_page);

              }

            }
          ?>
        </div>
      <?php
      }
      ?>
    </div>

    <?php

    return ob_get_clean();

  }


  public function get_user_folders($page_now = null){

    $args = array(
      'numberposts' => -1,
      'post_type'   => 'admin2020folders',
      'orderby' => 'title',
      'order'   => 'ASC',
    );

    $folders = get_posts( $args );

    if (count($folders) < 1){
      ?>
      <p class="uk-text-meta"><?php _e('No folders yet, why not create one?','admin2020') ?></p>
      <?php
      return;
    }

    foreach ($folders as $folder){

      $parent_folder = get_post_meta($folder->ID, "parent_folder",true);
      if(!$parent_folder){
        echo $this->foldertemplate($folder,$folders,$page_now);
      }
      continue;

    }

    ?>
    <div class="admin2020folder set_as_top" style="min-height:30px" folder-id="false" ondrop="admin2020mediadrop(event)" ondragover="admin2020mediaAllowDrop(event)" ondragleave="admin2020mediaDropOut(event)"
      draggable="true" ondragstart="admin2020folderdrag(event)" id="folderfalse">
    </div>

    <div  class="uk-flex-top" id="admin2020_edit_folder"  uk-modal>
      <div class="uk-modal-dialog uk-modal-body uk-margin-auto-vertical">
        <div class="uk-h4"><?php _e('Edit Folder','admin2020')?></div>
        <input type="text" class="uk-input uk-margin-bottom" id="foldername_update" placeholder="<?php _e('Folder Name','admin2020')?>">
        <span class="uk-text-meta"><?php _e('Colour Tag','admin2020')?></span>

        <div class="uk-margin uk-child-width-auto" id="admin2020_folder_tag_update">
          <input class="uk-radio" type="radio" name="color_tag" value="#1e87f0" style="border-color:#1e87f0;background-color:#1e87f0">
          <input class="uk-radio" type="radio" name="color_tag" value="#32d296" style="border-color:#32d296;background-color:#32d296">
          <input class="uk-radio" type="radio" name="color_tag" value="#faa05a" style="border-color:#faa05a;background-color:#faa05a">
          <input class="uk-radio" type="radio" name="color_tag" value="#f0506e" style="border-color:#f0506e;background-color:#f0506e">
          <input class="uk-radio" type="radio" name="color_tag" value="#ff9ff3" style="border-color:#ff9ff3;background-color:#ff9ff3">
        </div>
        <div class="uk-grid-small" uk-grid>
          <div class="uk-width-1-1 uk-margin-small-bottom">
          </div>
          <div class="uk-width-1-2 ">
            <button class="uk-button uk-button-danger" id="delete_the_folder" type="button"><?php _e('Delete','admin2020') ?></button>
          </div>
          <div class="uk-width-1-2 uk-flex uk-flex-right">
            <button class="uk-button uk-button-primary" id="update_the_folder" type="button"><?php _e('Save','admin2020') ?></button>
          </div>
        </div>
      </div>

    </div>

    <?php

  }



  public function admin2020_create_folder() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-folder-security-nonce', 'security') > 0) {

          $foldername = wp_strip_all_tags($_POST['title']);
          $foldertag = wp_strip_all_tags($_POST['foldertag']);

          $my_post = array(
              'post_title'    => $foldername,
              'post_status'   => 'publish',
              'post_type'     => 'admin2020folders'
          );

          // Insert the post into the database.
          $thefolder = wp_insert_post( $my_post );
          update_post_meta($thefolder,"color_tag",$foldertag);
          //update_post_meta($thefolder,"parent_folder",161);

          echo $this->build_individual_folder_stack($thefolder);

      }
      die();
  }


  public function admin2020_rename_folder() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-folder-security-nonce', 'security') > 0) {

          $foldername = $_POST['title'];
          $folderid = $_POST['folderid'];
          $foldertag = $_POST['foldertag'];

          $my_post = array(
              'post_title'    => $foldername,
              'post_status'   => 'publish',
              'ID'            => $folderid,
          );

          // Insert the post into the database.
          $thefolder = wp_update_post( $my_post );

          if(!$thefolder){
            $returndata = array();
            $returndata['error'] = __('Something went wrong','admin2020');
            echo json_encode($returndata);
            die();
          }

          update_post_meta($folderid,"color_tag",$foldertag);

          $returndata = array();
          $returndata['message'] = __('Folder succesfully renamed','admin2020');
          $returndata['html'] = $this->build_individual_folder_stack($thefolder);
          echo json_encode($returndata);

      }
      die();
  }


  public function admin2020_delete_folder() {
      if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-folder-security-nonce', 'security') > 0) {

          $folderid = $_POST['folderid'];
          $status = wp_delete_post($folderid);

          if(!$status){
            $returndata = array();
            $returndata['error'] = __('Something went wrong','admin2020');
            echo json_encode($returndata);
            die();
          }

          $args = array(
            'post_type' => 'admin2020folders',
            'posts_per_page' => -1,
            'fields' => 'ids',
            'meta_query' => array(
             array(
                     'key' => 'parent_folder',
                     'value' => $folderid,
                     'compare' => '=',
                 )
             )
          );

          $thechildren = get_posts($args);

          foreach($thechildren as $child){

            wp_delete_post($child);

          }

          $returndata = array();
          $returndata['message'] = __('Folder succesfully deleted','admin2020');
          echo json_encode($returndata);

      }
      die();
}



public function admin2020_move_to_folder() {
    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-folder-security-nonce', 'security') > 0) {

        $attachmentids = $_POST['theids'];
        $folderid = $_POST['folderid'];
        $request_page = $_POST['page_id'];

        foreach ($attachmentids as $attachmentid){

          //wp_delete_attachment($attachmentid);
          update_post_meta($attachmentid, "admin2020_folder",$folderid);

        }

        echo $this->build_individual_folder_stack($folderid,$request_page);
    }
    die();
}

public function admin2020_refresh_all_folders(){
  if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-folder-security-nonce', 'security') > 0) {

    $page_id = $_POST['page_id'];


    echo $this->get_user_folders($page_id);

  }
  die();
}
public function admin2020_move_folder_into_folder() {
    if (defined('DOING_AJAX') && DOING_AJAX && check_ajax_referer('ma-admin-folder-security-nonce', 'security') > 0) {

        $destination_id = $_POST['destination_id'];
        $origin_folder_id = $_POST['origin_id'];
        $page_id = $_POST['page_id'];

        if($destination_id == 'false'){
          $destination_id = "";
        }

        $current_value = get_post_meta( $origin_folder_id, "parent_folder", true );

        if($current_value == $destination_id){
          $senddata = array();
          $senddata['error'] = __('Folder is already there','admin2020');
          echo json_encode($senddata);
          die();
        }


        if(!$origin_folder_id){
          $senddata = array();
          $senddata['error'] = __('No source or destination provided','admin2020');
          echo json_encode($senddata);
          die();
        }



        $success = update_post_meta($origin_folder_id, "parent_folder",$destination_id);

        if(!$success){
          $senddata = array();
          $senddata['error'] = __('Something went wrong','admin2020');
          echo json_encode($senddata);
          die();
        }

        if($destination_id == ""){
          $destination_id = $origin_folder_id;
        }

        $senddata = array();
        $senddata['message'] = __('Folder Moved','admin2020');
        $senddata['html'] = $this->build_individual_folder_stack($destination_id,$page_id);


        echo json_encode($senddata);
    }
    die();
}

public function build_individual_folder_stack($folderid, $request_page = null){

  $args = array(
    'numberposts' => -1,
    'post_type'   => 'admin2020folders',
    'orderby' => 'title',
    'order'   => 'ASC',
  );

  $folders = get_posts( $args );
  $folder = get_post($folderid);

  if($folderid == "" || $folderid == null){

    $data = "";

    foreach($folders as $folder){
      $parent_folder = get_post_meta($folder->ID, "parent_folder",true);
      if(!$parent_folder){
        $data = $data . $this->foldertemplate($folder,$folders);
      }
    }

    return $data;

  } else {

    return $this->foldertemplate($folder,$folders,$request_page);

  }

}

}
