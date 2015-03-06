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

    $parentPrimkey = esc_sql($_POST['parentPrimkey']);
    $wpscVariationGrouping = esc_sql($_POST['wpscVariationGrouping']);
    
    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `options`='{$wpscVariationGrouping}' WHERE `producttype`='variation' AND `postid`='$parentPrimkey';");
    
    echo wpscProductClone($parentPrimkey, NULL, 'variation', 'dropdown', $wpscVariationGrouping);
    
}
?>