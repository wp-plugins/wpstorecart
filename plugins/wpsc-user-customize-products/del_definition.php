<?php
if (!function_exists('add_action'))
{
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
    
    
    $wpsc_primkey = intval($_POST['wpsc_primkey']);

    $table_name = $wpdb->prefix . "wpstorecart_custom_def";

    $insert = "DELETE FROM `{$table_name}` WHERE `primkey`='{$wpsc_primkey}';";

    $wpdb->query($insert);

}
?>