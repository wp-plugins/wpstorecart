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

    error_reporting(0);
    
    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }
    
    
    $current = intval($_POST['wpsc-rect-count']);
    $productkey = intval($_POST['wpsc-keytoedit']);
    $allowed = esc_sql($_POST['wpsc-customize-edit-record-allowed-types-val-'.$current]);
    $x = intval($_POST['wpsc-customize-edit-record-x-val-'.$current]);
    $y = intval($_POST['wpsc-customize-edit-record-y-val-'.$current]);
    $width = intval($_POST['wpsc-customize-edit-record-width-val-'.$current]);
    $height = intval($_POST['wpsc-customize-edit-record-height-val-'.$current]); 
    
    $table_name = $wpdb->prefix . "wpstorecart_custom_def";

    $insert = "INSERT INTO `{$table_name}` (`primkey`, `productkey`, `custkey`, `allowedcustomizations`, `x`, `y`, `width`, `height`) VALUES (NULL, '{$productkey}', '{$current}', '{$allowed}', '{$x}', '{$y}', '{$width}', '{$height}');";

    $wpdb->query($insert);
    $keyToLookup = $wpdb->insert_id;
 
    echo $keyToLookup;
    
}
?>