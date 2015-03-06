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
    $wpsc_fields_type = esc_sql($_POST['wpsc_fields_type']);
    $wpsc_fields_information_type = esc_sql($_POST['wpsc_fields_information_type']);
    $wpsc_fields_required = esc_sql($_POST['wpsc_fields_required']);
    $wpsc_fields_default_value = esc_sql($_POST['wpsc_fields_default_value']);
    $wpsc_fields_desc = esc_sql($_POST['wpsc_fields_desc']);
    $wpsc_fields_name = esc_sql($_POST['wpsc_fields_name']);
    $wpsc_fields_isactive = esc_sql($_POST['wpsc_fields_isactive']);
    
    if(@isset($_POST['wpsc_edit_field_primkey'])) {
        $field_key = esc_sql($_POST['wpsc_edit_field_primkey']);
        $insert = "
        UPDATE `{$table_name}` 
            SET
            `productkey`='{$wpsc_fields_product_primkey}', 
            `type`='{$wpsc_fields_type}', 
            `information`='{$wpsc_fields_information_type}', 
            `required`='{$wpsc_fields_required}', 
            `defaultvalue`='{$wpsc_fields_default_value}', 
            `desc`='{$wpsc_fields_desc}', 
            `name`='{$wpsc_fields_name}', 
            `isactive`='{$wpsc_fields_isactive}' 
            WHERE `primkey` = '{$field_key}';
        ";

        $wpdb->query($insert);
        echo $field_key;
    } else {
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
    
    



}
?>