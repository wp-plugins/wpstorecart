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

    $title = esc_sql($_POST['wpsc-new-attribute-title']);
    $group = esc_sql($_POST['wpsc-new-attribute-group']);
    if($group=='CREATENEWGROUP') {
        $group = esc_sql($_POST['wpsc-new-attribute-new-group']);
    }
    
    // Attributes inventory toggle
    if(@$_POST['wpscuseinventoryonattributes']=='true') {
        $useinventory = 1;
    } else {
        $useinventory = 0;
    }       
    
    $pricedifference = esc_sql($_POST['wpsc-new-attribute-price-difference']);
    $productkey = esc_sql($_POST['wpsc-new-attribute-parent-key']);
    
    
    $insert = "INSERT INTO `{$wpdb->prefix}wpstorecart_quickvar` (`primkey`, `productkey`, `values`, `price`, `type`, `title`, `group`, `useinventory`) VALUES (NULL, {$productkey}, '', '{$pricedifference}', 'dropdown', '{$title}', '{$group}', '{$useinventory}');";

    $wpdb->query($insert);
    $lastID = $wpdb->insert_id;
    
    echo '
    {
        "primkey": "'.$lastID.'",
        "group": "'.wpscSlug($group).'",            
        "useinventory": "'.$useinventory.'",  
        "pricedifference": "'.$pricedifference.'",  
        "productkey": "'.$productkey.'", 
        "title": "'.$title.'"
    }
    ';
    
}
?>