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
    $wpsc_cust_current_id = intval($_POST['wpsc_cust_current_id']);
    $wpsc_cust_updated_x = intval($_POST['wpsc_cust_updated_x']);
    $wpsc_cust_updated_y = intval($_POST['wpsc_cust_updated_y']);
    $wpsc_cust_updated_width = intval($_POST['wpsc_cust_updated_width']);
    $wpsc_cust_updated_height = intval($_POST['wpsc_cust_updated_height']);
    $wpsc_cust_updated_types = esc_sql($_POST['wpsc_cust_updated_types']);

    $table_name = $wpdb->prefix . "wpstorecart_custom_def";

    $insert = "UPDATE `{$table_name}` SET `allowedcustomizations`='{$wpsc_cust_updated_types}', `custkey`='{$wpsc_cust_current_id}', `x`='{$wpsc_cust_updated_x}', `y`='$wpsc_cust_updated_y', `width`='$wpsc_cust_updated_width', `height`='{$wpsc_cust_updated_height}' WHERE `primkey`='{$wpsc_primkey}';";

    $wpdb->query($insert);

}
?>