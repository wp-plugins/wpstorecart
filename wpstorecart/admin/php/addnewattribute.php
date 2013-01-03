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
    
    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    $title = $wpdb->escape($_POST['wpsc-new-attribute-title']);
    $group = $wpdb->escape($_POST['wpsc-new-attribute-group']);
    if($group=='CREATENEWGROUP') {
        $group = $wpdb->escape($_POST['wpsc-new-attribute-new-group']);
    }
    $useinventory = $wpdb->escape($_POST['wpsc-new-attribute-inventory']);
    $pricedifference = $wpdb->escape($_POST['wpsc-new-attribute-price-difference']);
    $productkey = $wpdb->escape($_POST['wpsc-new-attribute-parent-key']);
    
    
    $insert = "INSERT INTO `{$wpdb->prefix}wpstorecart_quickvar` (`primkey`, `productkey`, `values`, `price`, `type`, `title`, `group`, `useinventory`) VALUES (NULL, {$productkey}, '', '{$pricedifference}', 'dropdown', '{$title}', '{$group}', '{$useinventory}');";

    $wpdb->query($insert);
    $lastID = $wpdb->insert_id;
    
    echo '
    {
        "primkey": "'.$lastID.'",
        "group": "'.$group.'",            
        "useinventory": "'.$useinventory.'",  
        "pricedifference": "'.$pricedifference.'",  
        "productkey": "'.$productkey.'", 
        "title": "'.$title.'"
    }
    ';
    
}
?>