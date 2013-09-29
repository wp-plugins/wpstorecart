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
    $wpsc_fields_type = $wpdb->escape($_POST['wpsc_fields_type']);
    $wpsc_fields_information_type = $wpdb->escape($_POST['wpsc_fields_information_type']);
    $wpsc_fields_required = $wpdb->escape($_POST['wpsc_fields_required']);
    $wpsc_fields_default_value = $wpdb->escape($_POST['wpsc_fields_default_value']);
    $wpsc_fields_desc = $wpdb->escape($_POST['wpsc_fields_desc']);
    $wpsc_fields_name = $wpdb->escape($_POST['wpsc_fields_name']);
    $wpsc_fields_isactive = $wpdb->escape($_POST['wpsc_fields_isactive']);
    
    $insert = "
    INSERT INTO `{$table_name}` (
        `primkey`, `productkey`, `type`, `information`, `required`, `defaultvalue`, `desc`, `name`, `availableoptions`, `isactive`
    ) VALUES (
        NULL, 
        '{$wpsc_fields_product_primkey}', 
        '{$wpsc_fields_type}', 
        '{$wpsc_fields_information_type}', 
        '{$wpsc_fields_required}', 
        '{$wpsc_fields_default_value}', 
        '{$wpsc_fields_desc}', 
        '{$wpsc_fields_name}', 
        '', 
        '{$wpsc_fields_isactive}'
    );
    ";

    $wpdb->query($insert);
    $lastID = $wpdb->insert_id;
    echo $lastID;


}
?>