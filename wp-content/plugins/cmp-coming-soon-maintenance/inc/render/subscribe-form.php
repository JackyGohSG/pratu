<?php

// process emails first
$response = $this->niteo_subscribe( true );

$html = '';


// get current theme
$theme = $this->cmp_selectedTheme();

// get type of susbscribe
$subscribe_type = get_option('niteoCS_subscribe_type', '2');

// if subscribers is 3rd party plugin, render form by shortcode
switch ($subscribe_type) {
    // custom shortcode
    case '1':
        $replace  = array('<p>', '</p>' );
        $html =  str_replace($replace, '', do_shortcode( stripslashes( get_option('niteoCS_subscribe_code') ))) ; 
        break;
    // CMP subscribe form
    case '2':
        // get GDPR message
        $niteoCS_subscribe_label = $this->cmp_wpml_translate_string( stripslashes( get_option('niteoCS_subscribe_label') ), 'Subscribe GDPR Message' );
        
        //  get translation if exists
        $translation = $this->cmp_wpml_niteoCS_translation();

        $placeholder            = stripslashes( $translation[4]['translation'] );
        $placeholder_firstname  = stripslashes( $translation[10]['translation'] );
        $placeholder_lastname   = stripslashes( $translation[11]['translation'] );
        
        $subscribe              = stripslashes( $translation[12]['translation'] );
        $gdpr_checkbox          = get_option( 'niteoCS_subscribe_gdpr_checkbox', '0' );

        // overwrite it with theme specific requirements
        if ( $theme == 'stylo' ) {
            $placeholder            =  '&#xf0e0;  ' . $placeholder;
            $placeholder_firstname  =  '&#xf007;  ' . $placeholder_firstname;
            $placeholder_lastname   =  '&#xf007;  ' . $placeholder_lastname;
        }

        // overwrite it with theme specific requirements
        if ( $theme == 'pluto' ) {
            $placeholder            =  '&#xf0e0;  ' . $placeholder;
        }

        // set submit icon / text
        switch ($theme) {
            case 'postery':
                $submit = '&#xf1d8;';
                break;
            case 'juno':
                $submit = '&#xf1d8;';
                break;
            case 'agency':
                $submit = '&#xf105;';
                break;
            case 'libra':
                $submit = '&#xf1d8;';
                break;
            
            default:
                $submit = stripslashes( $translation[8]['translation'] );
                break;
        }   ?>
        
        <form id="subscribe-form" method="post" class="cmp-subscribe">
            <div class="cmp-form-inputs">

                <?php wp_nonce_field('save_options','save_options_field'); ?>
                <?php
                // display placeholders or labels
                switch ( $label ) {
                    case TRUE:
                        if ( $firstname === TRUE ) { ?>
                            <div class="firstname input-wrapper">
                                <label for="firstname-subscribe"><?php echo esc_attr( $placeholder_firstname );?></label>
                                <input type="text" id="firstname-subscribe" name="cmp_firstname">
                            </div>
                            <?php 
                        }

                        if ( $lastname === TRUE ) { ?>
                            <div class="lastname input-wrapper">
                                <label for="lastname-subscribe"><?php echo esc_attr( $placeholder_lastname );?></label>
                                <input type="text" id="lastname-subscribe" name="cmp_lastname">
                            </div>
                            <?php 
                        } ?>
                        <div class="email input-wrapper">
                            <label for="email-subscribe"><?php echo esc_attr( $placeholder );?></label>
                            <input type="email" id="email-subscribe" name="email" required>
                        </div>
                        <?php 
                        break;

                    case FALSE: 
                        if ( $firstname === TRUE ) { ?>
                            <input type="text" id="firstname-subscribe" name="cmp_firstname" placeholder="<?php echo esc_attr( $placeholder_firstname );?>">
                            <?php 
                        }

                        if ( $lastname === TRUE ) { ?>
                            <input type="text" id="lastname-subscribe" name="cmp_lastname" placeholder="<?php echo esc_attr( $placeholder_lastname );?>">
                            <?php 
                        } ?>

                        <input type="email" id="email-subscribe" name="email" placeholder="<?php echo esc_attr( $placeholder );?>" required> 
                        <?php 
                        break;

                    default:
                        break;
                } 

                switch ( $theme ) {
                    case 'mercury': ?>
                        <button type="submit" id="submit-subscribe" <?php echo $gdpr_checkbox ? 'disabled' : '';?>><?php echo esc_attr( $submit );?></button>
                        <?php
                        break;
                    case 'headliner': ?>
                        <input type="submit" id="submit-subscribe" value="<?php echo esc_attr( $subscribe );?>" data-subscribe="<?php echo esc_attr( $submit );?>" <?php echo $gdpr_checkbox ? 'disabled' : '';?>>
                        <?php
                        break;
                    
                    default: ?>
                        <input type="submit" id="submit-subscribe" value="<?php echo esc_attr( $submit );?>" <?php echo $gdpr_checkbox ? 'disabled' : '';?>>
                        <?php
                        break;
                } ?>

                <div style="display: none;">
                    <input type="text" name="form_honeypot" value="" tabindex="-1" autocomplete="off">
                </div>

                <div id="subscribe-response"><?php echo isset( $response ) ? $response : '';?></div>

                <div id="subscribe-overlay"></div>
            </div>

            <?php 
            // render Subscribe form Message/GDPR
            if ( $niteoCS_subscribe_label != '' ) {

                $checkbox = '';

                $allowed_html = array(
                    'a' => array(
                        'href' => array(),
                        'title' => array()
                    ),
                    'input' => array(
                        'type' => array(),
                        'checked' => array(),
                        'id' => array(),
                        'name' => array(),
                        'required' => array(),
                    )
                );

                if ( $gdpr_checkbox ) {
                    $checkbox = '<input type="checkbox" id="gdpr-checkbox" name="gdpr-checkbox" required /> ';

                } ?>

                <div class="cmp-form-notes">
                <?php echo wpautop(wp_kses( $checkbox . $niteoCS_subscribe_label, $allowed_html )); ?>
                </div>
                <?php 
            } ?>

        </form>

        <?php 
        if ( get_option( 'niteoCS_recaptcha_status', '1' ) === '1' ) {
            $site_key = get_option('niteoCS_recaptcha_site', '');
            echo '<script src="https://www.google.com/recaptcha/api.js?render='.esc_attr($site_key).'" async defer></script>';
        } ?>

        <script>
            /* Subscribe form script */
            <?php 
            $url = parse_url( admin_url() );
            $path = isset($url['path']) ? $url['path'] : '/wp-admin/';
            ?>
            
            var ajaxurl = '<?php echo esc_attr($path);?>admin-ajax.php';
            var security = '<?php echo wp_create_nonce( 'cmp-subscribe-action' );?>';
            var msg = '';
            

            window.addEventListener('DOMContentLoaded',function(event) {

                const submitButton = document.getElementById('submit-subscribe');
                const form = document.getElementById('subscribe-form');
                const resultElement = document.getElementById('subscribe-response');
                const emailInput =  document.getElementById('email-subscribe');
                const firstnameInput =  document.getElementById('firstname-subscribe');
                const lastnameInput =  document.getElementById('lastname-subscribe');
                const gdprCheckbox = document.getElementById('gdpr-checkbox');

                if ( gdprCheckbox ) {
                    gdprCheckbox.addEventListener('change', function() {
                        if ( this.checked ) {
                            submitButton.disabled = false;
                        } else {
                            submitButton.disabled = true;
                        }
                    })
                }

                submitButton.onclick = function( e ) {
                    e.preventDefault();
                    <?php 
                    if ( get_option( 'niteoCS_recaptcha_status', '1' ) === '1' ) { ?>
                    grecaptcha.ready(function() {
                        grecaptcha.execute('<?php echo esc_attr($site_key);?>').then(function(token) {
                            subForm( form, resultElement, emailInput, firstnameInput, lastnameInput, token );
                        });
                    });

                    <?php 
                    } else { ?> 
                        subForm( form, resultElement, emailInput, firstnameInput, lastnameInput );
                        <?php 
                    } ?>
                    
                }

                form.onsubmit = function(){ // Prevent page refresh
                    return false;
                }
                
            });

            subForm = function( form, resultElement, emailInput, firstnameInput, lastnameInput, token = '' ) {

                if ( emailInput.value !== '' ) {
                    const firstname = firstnameInput === null ? '' : firstnameInput.value;
                    const lastname = lastnameInput === null ? '' : lastnameInput.value;

                    fetch( ajaxurl, {
                        method: 'POST',
                        headers: {
                            'Content-Type': 'application/x-www-form-urlencoded; charset=utf-8',
                            'Access-Control-Allow-Origin': '*',
                        },
                        body: `action=niteo_subscribe&ajax=true&form_honeypot=&email=${ emailInput.value }&firstname=${ firstname }&lastname=${ lastname }&security=${ security }&token=${ token }`,
                        credentials: 'same-origin'
                    } )
                    .then( (res) => {
                        return res.json();
                    } )
                    .then( (data) => {
                        resultElement.innerHTML = data.message; // Display the result inside result element.
                        form.classList.add('-subscribed');
                        if (data.status == 1) {
                            form.classList.remove('-subscribe-failed');
                            form.classList.add('-subscribe-successful');
                            emailInput.value = '';
                            firstnameInput ? firstnameInput.value = '' : null;
                            lastnameInput ? lastnameInput.value = '' : null;
                            <?php do_action('cmp-successfull-subscribe-action'); ?>

                        } else {
                            form.classList.add('-subscribe-failed');
                        }
                    } )
                    .catch(function(error) { console.log(error.message); });
                }
            }
        </script>
        <?php 
        break;
    // MailOPtin
    case '3':

        if ( defined('MAILOPTIN_VERSION_NUMBER') ) {

            $campaign_id = get_option('niteoCS_mailoptin_selected');
            $campaign= MailOptin\Core\Repositories\OptinCampaignsRepository::get_optin_campaign_by_id($campaign_id);
            if ( $campaign['optin_type'] !== 'lightbox' ) {
                if ( !$this->jquery ) {
                    echo '<script src="https://code.jquery.com/jquery-1.12.4.min.js" integrity="sha256-ZosEbRLbNQzLpnKIkEdrPv7lOy9C27hHQ+Xp8a4MxAQ=" Crossorigin="anonymous"></script>';
                    $this->jquery = TRUE;
                }
                $html = do_shortcode( '[mo-optin-form id="'. get_option('niteoCS_mailoptin_selected') .'"]' );
            }
        }
        break;
    default:
        break;
}

return $html;