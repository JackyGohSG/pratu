<?php
if ( ! defined( 'ABSPATH' ) ) exit;

if ( !headers_sent() ) {
	status_header( $r['response'] );
	nocache_headers();
	header( 'Content-Type: text/html; charset=utf-8' );
}

$text_direction = 'ltr';
if ( ( isset($r['text_direction']) && 'rtl' == $r['text_direction'] ) || ( function_exists( 'is_rtl' ) && is_rtl() ) ) :
	$text_direction = 'rtl';
endif;

$userid = get_current_user_id();
$darkmode = get_user_meta($userid, 'darkmode', true);

$utils = new Admin2020_Util();
$logo = $utils->get_logo();
$background = "uk-background-muted";
$cardbackground =  "uk-card-default";

if ($darkmode == 'true'){
  $logo = $utils->get_dark_logo();
  $background = "uk-background-verydark uk-light";
  $cardbackground =  "uk-card-secondary";
}

?><!DOCTYPE html>
<html xmlns="http://www.w3.org/1999/xhtml" <?php if ( function_exists( 'language_attributes' ) && function_exists( 'is_rtl' ) ) language_attributes(); else echo "dir='$text_direction'"; ?>>
	<head>
		<meta http-equiv="Content-Type" content="text/html; charset=utf-8" />
		<meta name="viewport" content="width=device-width">
		<?php
		if ( function_exists( 'wp_no_robots' ) ) {
			wp_no_robots();
		}
		?>
		<title>Keyboard Action Error</title>

    <!-- UIkit CSS -->
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/uikit@3.5.6/dist/css/uikit.min.css" />

    <!-- UIkit JS -->
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.5.6/dist/js/uikit.min.js"></script>
    <script src="https://cdn.jsdelivr.net/npm/uikit@3.5.6/dist/js/uikit-icons.min.js"></script>

		<style type="text/css">
			@import url(https://fonts.googleapis.com/css?family=Sen);

			/* SELECTED TEXT */
			::selection { background: #ff5e99; color: #FFFFFF; text-shadow: 0; }
			::-moz-selection { background: #ff5e99; color: #FFFFFF; }
			html {
				font-size: 18px;font-size: 1.13rem;
				-webkit-text-size-adjust: 100%;
				-ms-text-size-adjust: 100%;
			}
			html, input { font-family: "sen", "Helvetica Neue", Helvetica, Arial, sans-serif; }

      .uk-background-verydark{
        background: #111;
      }
		</style>
	</head>

	<body id="error-page ">

    <div class="uk-flex uk-flex-center uk-flex-middle uk-height-viewport uk-width-1-1 <?php echo $background ?>">
      <div class="uk-card <?php echo $cardbackground?> uk-card-body uk-width-large uk-text-center" style="border-radius:10px;" >
        <img src="<?php echo $logo?>" style="max-height:100px;">
        <h1 class="">Oops!</h1>
        <p><?php echo $message; ?></p>
				<p><a class="uk-button uk-button-primary" href="javascript:history.back()" style="border-radius:4px;"><?php _e('Go Back','admin2020') ?></a></p>
      </div>
    </div>

	</body>

</html>
