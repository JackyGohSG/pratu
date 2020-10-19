<?php 
$html = '';
$ulclass = ( $ulclass != '' ) ? ' ' . $ulclass : $ulclass;
$liclass = ( $liclass != '' ) ? ' ' . $liclass : $liclass;

// migrate social media to new option after update 1.4.0
if ( get_option('niteoCS_socialmedia') ) {

    $socialmedia = stripslashes( get_option('niteoCS_socialmedia') );
    $socialmedia = json_decode( $socialmedia, true );
    //sort social icons array by hidden, then order key
    uasort( $socialmedia  , array($this,'sort_social') );

    $theme_html = ( $themeslug == 'stylo' || $themeslug == 'mosaic') ? '<svg xmlns="http://www.w3.org/2000/svg" xmlns:xlink="http://www.w3.org/1999/xlink" x="0px" y="0px" width="3em" height="3em" viewBox="0 0 80 80" xml:space="preserve"><circle transform="rotate(-90 40 40)" class="another-circle" cx="40" cy="40" r="36" /></svg>' : '';
    
    if ( $title == true ) {
        $soc_title = $this->cmp_wpml_translate_string( stripslashes( get_option('niteoCS_soc_title', 'GET SOCIAL WITH US') ), 'Social Icons Title' );;

        $html = ( $soc_title == '' ) ? '' : '<h2 class="soc-title">' . esc_html( $soc_title ) . '</h2>';
    }

    ob_start();

    foreach ( $socialmedia as $social ) {

        if ( $social['hidden'] == '0' && $social['active'] == '1') {
            $href = '';
            $name = ucfirst($social['name']);
            $icon = 'fab fa-' . $social['name'];

            switch ($social['name']) {
                case 'envelope-o':
                    $href = 'mailto:';
                    $name = __('Email', 'cmp-coming-soon-maintenance');
                    $icon = 'far fa-envelope';
                    break;
                case 'phone':
                    $href = 'tel:';
                    $name = __('Phone', 'cmp-coming-soon-maintenance');
                    $icon = 'fas fa-phone';
                    break;
                case 'whatsapp':
                    $name = 'WhatsApp';
                    $href = 'https://wa.me/';
                    $social['url'] = preg_replace('/[^0-9]/', '', $social['url']);
                    break;
                case 'linkedin':
                    $name = 'LinkedIn';
                    break;
                case 'deviantart':
                    $name = 'DeviantArt';
                    break;
                case 'soundcloud':
                    $name = 'SoundCloud';
                    break;
                case 'vk':
                    $name = 'VK';
                    break;
                case 'github':
                    $name = 'GitHub';
                    break;
                default:
                    break;
            } 

            echo ( $mode == 'text' ) ? '<li class="social-child' . $liclass . '"><a href="'.$href.esc_attr($social['url']).'" target="top" class="social-'.$social['name'].'">'.$name.'</a></li>' : '<li class="social-child' . $liclass . '"><a href="'.$href.esc_attr($social['url']).'" target="top" class="social-'.$social['name'].'">'.$theme_html.'<i class="'.$icon.'" aria-hidden="true"></i></a></li>';
        } 
    }

    $social_list = ob_get_clean();

    if ( $social_list != '' ) {
        $html = $html . '<ul class="social-list' . $ulclass . '">' . $social_list.'</ul>';
    }
}