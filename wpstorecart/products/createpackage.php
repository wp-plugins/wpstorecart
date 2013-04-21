<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

global $wpdb, $current_user;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    error_reporting(E_ALL);
    
    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    $productkey = intval($_POST['productkey']);

    $wpdb->query("INSERT `{$wpdb->prefix}wpstorecart_packages` (`primkey`, `productkey`, `weight`, `length`, `width`, `depth`, `options`) VALUES (NULL, '{$productkey}', '0', '0', '0', '0', '');");

    echo $wpdb->insert_id;

}

?>