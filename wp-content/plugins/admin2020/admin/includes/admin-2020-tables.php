<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin_2020_Table_Actions {

  public function run(){

    add_action('admin_init', array( $this, 'register_actions' ),0);

  }



  public function register_actions(){

    $utils = new Admin2020_Util();

    if($utils->deactivate_admin_on_page()){
      return;
    }

    $quickactions = $utils->get_option('admin2020_disable_quick_actions');

		if ($quickactions == ""){

      add_action('admin_head',  array( $this, 'ma_admin_load_table_functions'));
      add_action( 'manage_comments_custom_column',  array( $this, 'ma_admin_add_comments_column_action'), 999, 2 );
      add_action( 'manage_users_custom_column',  array( $this, 'ma_admin_add_comments_column_action'), 999, 2 );
      add_filter( 'list_table_primary_column',  array( $this, 'ma_admin_list_table_primary_column'), 999, 2 );

		}


    add_action( 'manage_posts_extra_tablenav', array($this, 'add_custom_table_filter'), 20, 1 );
    add_action( 'manage_users_extra_tablenav', array($this, 'add_custom_table_filter'), 20, 1 );

  }


  function add_custom_table_filter( $which ) {
      global $typenow;

      if ('top' === $which ) {
          ?>
          <div class="toggle_filters">
            <span class="actionfilter_trigger dashicons dashicons-filter"></span>
          </div>
          <div class="close_filters">
            <span class="uk-link"><?php _e('Close Filters',"admin2020")?></span>
          </div>
          <?php
      }
  }


  public function ma_admin_load_table_functions(){
      $screen = get_current_screen();

      if ($screen->id == "plugins"){

        add_filter( 'plugin_row_meta' , array($this,'change_plugin_names'),10,2 );

      } else {

        add_filter("manage_{$screen->id}_columns", array( $this,'ma_admin_add_table_columns'),999);
        add_action("manage_{$screen->post_type}_posts_custom_column", array( $this,'ma_admin_add_action_button'),999, 1);
        add_action("manage_{$screen->id}_custom_column", array( $this,'ma_admin_add_action_button'),999, 1);
        add_action("manage_users_custom_column", array( $this,'ma_admin_add_action_button_comments'),999, 3);

        if (is_multisite()){
          add_action("manage_sites_custom_column", array( $this,'ma_admin_add_action_button'),999, 1);
          add_action("manage_plugins_custom_column", array( $this,'ma_admin_add_action_button'),999, 1);
          add_action("manage_themes_custom_column", array( $this,'ma_admin_add_action_button'),999, 1);
        }

      }


  }


  public function change_plugin_names( $plugin_meta, $plugin_file_name ) {


    if ($plugin_file_name == "admin-2020/admin-2020.php"){
      $plugin_meta[] = '<a href="admin.php?page=admin_2020">'.__('Settings','admin2020').'</a>';
    }
    return $plugin_meta;
  }
  
  ////ADD ACTIONS COLUMN TO TABLES
  public function ma_admin_add_table_columns($cols){
      $cols['columnaction'] = 'Actions';
      return $cols;
  }

  ////ADD ICON TO COLUMN
  public function ma_admin_add_action_button( $column ) {

    switch ( $column ) :
      case 'columnaction' : {
        echo  '<span class="post-action" uk-icon="icon: more"></span>'; // or echo $comment->comment_ID;
        break;
      }
    endswitch;

  }

  ////ADD ICON TO USER COLUMN
  public function ma_admin_add_action_button_comments( $value, $column_name ) {

    if ( 'columnaction' === $column_name ) {
      $value = '<span class="post-action" uk-icon="icon: more"></span>'; ;
    }

    return $value;
  }


  public function ma_admin_add_comments_column_action( $column, $comment_ID ) {
  	switch ( $column ) :
  		case 'columnaction' : {
  			echo  '<span class="post-action" uk-icon="icon: more"></span>'; // or echo $comment->comment_ID;
  			break;
  		}
  	endswitch;
  }


  ////MAKE ACTION COLUMN PRIMARY
  public function ma_admin_list_table_primary_column( $default, $screen ) {

      $default = 'columnaction';
      return $default;

  }

}
