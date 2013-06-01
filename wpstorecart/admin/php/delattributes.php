<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpdb, $current_user;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    error_reporting(E_ALL);
    
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    
    if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }		
    global $wpdb;
    
    

}
?>