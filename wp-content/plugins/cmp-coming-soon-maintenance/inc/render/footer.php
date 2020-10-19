<?php 


/**
 * Detect plugin. For use on Front End only.
 */
include_once( ABSPATH . 'wp-admin/includes/plugin.php' );

// Plugin Name: Insert Headers and Footers
if ( class_exists('InsertHeadersAndFooters') ) {
    $ihaf = new InsertHeadersAndFooters();
    $ihaf->frontendFooter();
}

// Plugin Name: Cookie Notice
if ( class_exists('Cookie_Notice') && get_option('cmp_cookie_notice_comp', '1') === '1' ) {
    $cookie_notice = Cookie_Notice();
    $cookie_notice->add_cookie_notice();
}

// Plugin Name: SimpleAnalytics
if ( is_plugin_active( 'simpleanalytics/simple-analytics.php' ) ) {
    echo '<script src="https://cdn.simpleanalytics.io/hello.js"></script>' . PHP_EOL;
}

// Plugin Name: MailOption
if ( get_option('niteoCS_subscribe_type', '2') === '3' && defined('MAILOPTIN_VERSION_NUMBER') )  {

    $optin_id = get_option('niteoCS_mailoptin_selected');
    $campaign_type = MailOptin\Core\Repositories\OptinCampaignsRepository::get_optin_campaign_by_id($optin_id);

    if ( $campaign_type['optin_type'] === 'lightbox') {
        if ( !$this->jquery ) {
            echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" Crossorigin="anonymous"></script>';
            $this->jquery = TRUE;
        }
        echo MailOptin\Core\Admin\Customizer\OptinForm\OptinFormFactory::build(absint($optin_id));
    } ?>

    <script type='text/javascript'>
    /* <![CDATA[ */
    var mailoptin_globals = {"mailoptin_ajaxurl":"?mailoptin-ajax=%%endpoint%%","is_customize_preview":"false","disable_impression_tracking":"false","sidebar":"0"};
    /* ]]> */
    </script>
    <script type='text/javascript' src='<?php echo MAILOPTIN_ASSETS_URL;?>js/mailoptin.min.js?ver=<?php echo MAILOPTIN_VERSION_NUMBER;?>'></script>
    <?php
} 