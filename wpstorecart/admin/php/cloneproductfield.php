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
    $wpsc_fields_product_primkey = intval($_POST['wpsc_fields_product_primkey']);
    $wpsc_fields_primkey = esc_sql($_POST['wpsc_fields_primkey']);

    $results = $wpdb->get_results("SELECT * FROM `{$table_name}` WHERE `primkey`='{$wpsc_fields_primkey}';", ARRAY_A);
    if (@isset($results[0]['primkey'])) {
        $insert = "
        INSERT INTO `{$table_name}` (
            `primkey`, `productkey`, `type`, `information`, `required`, `defaultvalue`, `desc`, `name`, `availableoptions`, `isactive`
        ) VALUES (
            NULL, 
            '{$wpsc_fields_product_primkey}', 
            '{$results[0]['type']}', 
            '{$results[0]['information']}', 
            '{$results[0]['required']}', 
            '{$results[0]['defaultvalue']}', 
            '{$results[0]['desc']}', 
            '{$results[0]['name']}', 
            '{$results[0]['availableoptions']}', 
            '{$results[0]['isactive']}'
        );
        ";

        $wpdb->query($insert);
        $lastID = $wpdb->insert_id;
        echo $lastID;        
    }
    
    



}
?>