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

    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    $table_name = $wpdb->prefix . "wpstorecart_field_def";
    $wpsc_fields_product_primkey = intval($_POST['wpsc_fields_edit_primkey']);



    $results = $wpdb->get_results("SELECT * FROM `{$table_name}` WHERE `primkey`='{$wpsc_fields_product_primkey}';", ARRAY_A);
    if(@isset($results[0]['primkey'])) {
        header('Content-type: application/json');

        print json_encode($results[0]);

        
    }



}
?>