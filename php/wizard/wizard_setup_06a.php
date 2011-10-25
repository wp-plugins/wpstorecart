<?php

// wpStoreCart, (c) 2011 wpStoreCart.com.  All rights reserved.

global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
global $wpdb, $wpStoreCart;

if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}
$devOptions = $wpStoreCart->getAdminOptions();

if(current_user_can('administrator')) {

    $devOptions['checkmoneyordertext'] = $_POST['paypalemail'];
    $devOptions['allowcheckmoneyorder'] = 'true';
    update_option('wpStoreCartAdminOptions', $devOptions);
    header("HTTP/1.1 301 Moved Permanently");
    header ('Location: '.WP_PLUGIN_URL.'/wpstorecart/php/wizard/wizard_setup_06.php');
    exit();

}

?>