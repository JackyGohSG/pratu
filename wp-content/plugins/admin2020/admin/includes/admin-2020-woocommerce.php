<?php
if ( ! defined( 'ABSPATH' ) ) exit;

class Admin2020_woocommerce{

  private $version;

  public function __construct( $theversion ) {

    $this->version = $theversion;

  }

  public function build(){
    add_filter('admin2020_register_dash_card', array($this,'register_the_cards'));
  }


  public function register_the_cards($dashitems){

    $admin2020_cards = array(
//remove woocommerce overview cards -- Jacky 
//      array('total_sales','Total Sales','Woocommerce'),
//      array('total_orders','Total Orders','Woocommerce'),
//      array('average_order_value','Average Order Value','Woocommerce')
    );

    foreach ($admin2020_cards as $card){
      $function = $card[0];
      $name = $card[1];
      $category = $card[2];
      array_push($dashitems,array($this,$function,$name,$category));
    }
    return $dashitems;
  }

  public function total_sales($startdate = null, $enddate = null){


    ///GET ARRAY OF DATES
    $utils = new Admin2020_Util();
    $dates = $utils->date_array($startdate,$enddate);
    $json_dates = json_encode($dates);

    global  $woocommerce;
    $curreny_symbol = get_woocommerce_currency_symbol();

    $args = [
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'date_query' => array(
            array(
                'after'     => $startdate,
                'before'    => $enddate,
                'inclusive' => true,
                ),
              ),
    ];
    $my_query = new WP_Query($args);

    $total = 0;

    $orders = $my_query->posts;
    $total_orders = $my_query->post_count;
    $logo = esc_url(plugins_url('/assets/img/woocommerce.png', __DIR__));
    $array_orders_totals = array();
    $array_orders_counts = array();

    foreach ($dates as $date){
      $array_orders_totals[$date] = 0;
      $array_orders_counts[$date] = 0;
    }


    foreach ($orders as $ctr => $value)
    {
        $order_id = $value->ID;

        $order = wc_get_order($order_id);
        $order_total = $order->get_total();
        $order_date = date("d/m/Y",strtotime($order->order_date));

        $array_orders_totals[$order_date] += $order_total;
        $array_orders_counts[$order_date] += 1;

        $total_sales += $order_total;
    }

    $temparray = array();
    foreach($array_orders_totals as $item){
      array_push($temparray,$item);
    }
    $array_orders_totals = $temparray;

    $temparray = array();
    foreach($array_orders_counts as $item){
      array_push($temparray,$item);
    }
    $array_orders_counts = $temparray;

    $json_orders_value = json_encode($array_orders_totals);
    $json_orders_count = json_encode($array_orders_counts);

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_sales" card-type='woocommerce'>

        <div class="uk-card uk-card-default uk-card-body">

          <div class="uk-text-emphasis">
            <span class="uk-icon uk-icon-image" style="background-image: url(<?php echo $logo?>);"></span>
            <?php _e("Total Sales","admin2020")?>
          </div>


          <?php
          if($total_orders < 1){
            ?>

            <p><?php _e("No orders yet","admin2020");?> </p>

            <?php
          } else {
          ?>

            <div class="uk-h2 uk-text-primary uk-margin-remove"><?php echo $curreny_symbol.number_format($total_sales)?></div>

            <div class="uk-width-1-1">
              <canvas id="woocommerce_sales_chart" style="height:250px;max-height:250px;" ></canvas>
            </div>

            <script>
            jQuery(document).ready(function($) {

              var temp = [];

              temp.label = 'Sales';
              temp.data = <?php echo $json_orders_value?>;
              temp.backgroundColor = "rgba(180, 118, 255, 0.2)";
              temp.borderColor = "rgb(180 118 255)";
              temp.pointBackgroundColor = "rgba(180, 118, 255, 0.2)";
              temp.pointBorderColor = "rgb(180 118 255)";


              newchart('woocommerce_sales_chart','bar',<?php echo $json_dates?>,temp);

            })

            </script>

          <?php } ?>

        </div>
    </div>


    <?php
  }

  public function total_orders($startdate = null, $enddate = null){

    ///GET ARRAY OF DATES
    $utils = new Admin2020_Util();
    $dates = $utils->date_array($startdate,$enddate);
    $json_dates = json_encode($dates);

    global  $woocommerce;
    $curreny_symbol = get_woocommerce_currency_symbol();

    $args = [
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'date_query' => array(
            array(
                'after'     => $startdate,
                'before'    => $enddate,
                'inclusive' => true,
                ),
              ),
    ];
    $my_query = new WP_Query($args);

    $total = 0;

    $orders = $my_query->posts;
    $total_orders = $my_query->post_count;

    $logo = esc_url(plugins_url('/assets/img/woocommerce.png', __DIR__));
    $array_orders_totals = array();
    $array_orders_counts = array();

    foreach ($dates as $date){
      $array_orders_totals[$date] = 0;
      $array_orders_counts[$date] = 0;
    }


    foreach ($orders as $ctr => $value)
    {
        $order_id = $value->ID;

        $order = wc_get_order($order_id);
        $order_total = $order->get_total();
        $order_date = date("d/m/Y",strtotime($order->order_date));

        $array_orders_totals[$order_date] += $order_total;
        $array_orders_counts[$order_date] += 1;

        $total_sales += $order_total;
    }

    $temparray = array();
    foreach($array_orders_totals as $item){
      array_push($temparray,$item);
    }
    $array_orders_totals = $temparray;

    $temparray = array();
    foreach($array_orders_counts as $item){
      array_push($temparray,$item);
    }
    $array_orders_counts = $temparray;

    $json_orders_value = json_encode($array_orders_totals);
    $json_orders_count = json_encode($array_orders_counts);

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="total_sales" card-type='woocommerce'>

        <div class="uk-card uk-card-default uk-card-body">

          <div class="uk-text-emphasis">
            <span class="uk-icon uk-icon-image" style="background-image: url(<?php echo $logo?>);"></span>
            <?php _e("Total Orders","admin2020")?>
          </div>


          <?php
          if($total_orders < 1){
            ?>

            <p><?php _e("No orders yet","admin2020");?> </p>

            <?php
          } else {
          ?>

            <div class="uk-h2 uk-text-primary uk-margin-remove"><?php echo number_format($total_orders)?></div>

            <div class="uk-width-1-1">
              <canvas id="woocommerce_orders_chart" style="height:250px;max-height:250px;" ></canvas>
            </div>

            <script>
            jQuery(document).ready(function($) {

              var temp = [];

              temp.label = 'Sales';
              temp.data = <?php echo $json_orders_count?>;
              temp.backgroundColor = "rgba(50, 210, 150, 0.2)";
              temp.borderColor = "rgb(50 210 150)";
              temp.pointBackgroundColor = "rgba(50, 210, 150, 0.2)";
              temp.pointBorderColor = "rgb(50 210 150)";


              newchart('woocommerce_orders_chart','bar',<?php echo $json_dates?>,temp);

            })

            </script>

          <?php } ?>

        </div>
    </div>


    <?php
  }


  public function average_order_value($startdate = null, $enddate = null){

    ///GET ARRAY OF DATES

    global  $woocommerce;
    $curreny_symbol = get_woocommerce_currency_symbol();

    $args = [
        'post_type' => 'shop_order',
        'posts_per_page' => '-1',
        'post_status' => 'wc-completed',
        'date_query' => array(
            array(
                'after'     => $startdate,
                'before'    => $enddate,
                'inclusive' => true,
                ),
              ),
    ];
    $my_query = new WP_Query($args);

    $total_sales = 0;

    $orders = $my_query->posts;
    $total_orders = $my_query->post_count;
    $logo = esc_url(plugins_url('/assets/img/woocommerce.png', __DIR__));
    $array_orders_totals = array();
    $array_orders_counts = array();


    foreach ($orders as $ctr => $value)
    {
        $order_id = $value->ID;

        $order = wc_get_order($order_id);
        $order_total = $order->get_total();
        $order_date = date("d/m/Y",strtotime($order->order_date));

        $array_orders_totals[$order_date] += $order_total;
        $array_orders_counts[$order_date] += 1;

        $total_sales += $order_total;
    }

    $temparray = array();
    foreach($array_orders_totals as $item){
      array_push($temparray,$item);
    }
    $array_orders_totals = $temparray;

    $temparray = array();
    foreach($array_orders_counts as $item){
      array_push($temparray,$item);
    }
    $array_orders_counts = $temparray;

    $json_orders_value = json_encode($array_orders_totals);
    $json_orders_count = json_encode($array_orders_counts);

    $average_order = $total_sales / $total_orders;

    ?>
    <div class="uk-width-1-3@m uk-width-1-1@s" id="average_order_value" card-type='woocommerce'>

        <div class="uk-card uk-card-default uk-card-body">



            <div class="uk-grid uk-child-width-1-1">
              <div>
                <span class="uk-text-emphasis">
                  <span class="uk-icon uk-icon-image" style="background-image: url(<?php echo $logo?>);"></span>
                  <?php _e("Average Order Value","admin2020")?>
                </span>

                <?php
                if($total_orders < 1){
                  ?>
                  <p><?php _e("No orders yet","admin2020");?> </p>
                  <?php
                } else {
                ?>

                <div class="uk-h2 uk-text-primary uk-margin-remove"><?php echo $curreny_symbol.number_format($average_order)?></div>

                <?php
              } ?>
              </div>
            </div>



        </div>
    </div>
    <?php
  }

}
