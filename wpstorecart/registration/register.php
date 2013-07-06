<?php


define('WP_USE_THEMES', false);
require('../../../../../wp-blog-header.php');
require_once( ABSPATH . WPINC . '/pluggable.php');

require_once( ABSPATH . WPINC . '/registration.php');
if('POST' != $_SERVER['REQUEST_METHOD']){@wp_safe_redirect($_POST['redirect_to']);exit();}


$user_login = sanitize_user($_POST['email']);
$user_email=$_POST['email'];
$user_pass = $_POST['user_pass'];
$redirect_to = $_POST['redirect_to'];

if(@!isset($_SESSION)) {
        @session_start();
}

// Allows us to save this information in case we need to redisplay it later.
$_SESSION['wpsc_email'] = $user_email;
$_SESSION['wpsc_password'] = $user_pass;

$invalid_detected = false; // If there's an invalid item, this records it.

if(username_exists( $user_login )) {
    $invalid_detected = 1;
}

if(!validate_username( $user_login )){
    $invalid_detected = 2;
}

if (!is_email( $user_email )) {
    $invalid_detected = 3;
}

if (email_exists( $user_email )) {
    $invalid_detected = 4;
}

$user_id = wp_create_user( $user_login, $user_pass, $user_email );
if ( !$user_id )   {
    $invalid_detected = 5;
}

$fields = wpscGrabCustomRegistrationFields();
foreach ($fields as $field) {
    $specific_items = explode("||", $field['value']);
    if($specific_items[2]!='separator' && $specific_items[2]!='header' && $specific_items[2]!='text') {
        $current_field = trim($_POST[wpscSlug($specific_items[0])]);
        if($specific_items[2]=='zipcode') {
            $current_field = trim($_POST['wpsc_shipping_zipcode']);
        }
        if($specific_items[2]=='lastname') {
            $current_field = trim($_POST['wpsc_shipping_lastname']);
        }   
        if($specific_items[2]=='firstname') {
            $current_field = trim($_POST['wpsc_shipping_firstname']);
        }   
        if($specific_items[2]=='shippingaddress') {
            $current_field = trim($_POST['wpsc_shipping_address']);
        }    
        if($specific_items[2]=='taxstates') {
            $current_field = trim($_POST['taxstate']);
        }        
        if($specific_items[2]=='taxcountries') {
            $current_field = trim($_POST['taxcountries']);
        }    
        if($specific_items[2]=='shippingcity') {
            $current_field = trim($_POST['wpsc_shipping_city']);
        }
        
        $_SESSION['wpsc_'.wpscSlug($specific_items[0])]=$_POST[wpscSlug($specific_items[0])]; // This allows us to save data in case the form needs to be refilled out due to it being incomplete
        if ($specific_items[1]=='required' && $current_field=='') {
            $invalid_detected = 6;
        }
    }
}





if($invalid_detected==false) {
    wp_new_user_notification($user_id, $user_pass);
    $credentials=array('remember'=>true,'user_login'=>$user_login,'user_password'=>$user_pass);
    wp_signon($credentials);

    wpscSaveFields($user_id);

    wp_safe_redirect($redirect_to);
    exit();
}

if(strpos($redirect_to,'?')===false) {
    $redirect_to_with_errors = $redirect_to .'?wpscregerror='.urlencode($invalid_detected);
} else {
    $redirect_to_with_errors = $redirect_to .'&wpscregerror='.urlencode($invalid_detected);
}




wp_safe_redirect($redirect_to_with_errors);
exit();
?>