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

    $primkey = esc_sql($_POST['primkey']);
    $orderid = esc_sql($_POST['orderid']);
    $qty = esc_sql($_POST['qty']);

    $table_name = $wpdb->prefix . "wpstorecart_orders";
    $findx = $wpdb->get_results("SELECT `cartcontents` FROM `{$table_name}` WHERE `primkey`='{$orderid}';", ARRAY_A);

    $newvalue = str_replace($primkey.'*'.$qty.',', '', $findx[0]['cartcontents']);
    $update = "UPDATE `{$table_name}` SET `cartcontents`='{$newvalue}' WHERE `primkey`={$orderid};";

    $results = $wpdb->query($update);

}
 
?>