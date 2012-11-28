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

    $wpscneworderaffiliate = $wpdb->escape($_POST['wpsc-new-order-affiliate']);
    $wpscnewordercartcontents = $wpdb->escape($_POST['wpsc-new-order-cart-contents']);
    $wpscneworderdate = $wpdb->escape($_POST['wpsc-new-order-date']);
    $wpscneworderemail = $wpdb->escape($_POST['wpsc-new-order-email']);
    $wpscneworderpaymentprocessor = $wpdb->escape($_POST['wpsc-new-order-payment-processor']);
    $wpscneworderprice = $wpdb->escape($_POST['wpsc-new-order-price']);
    $wpscnewordershipping = $wpdb->escape($_POST['wpsc-new-order-shipping']);
    $wpscneworderstatus = $wpdb->escape($_POST['wpsc-new-order-status']);
    $wpscneworderuser = $wpdb->escape($_POST['wpsc-new-order-user']);
    $wpscproductdecreaseinventoryneworder = $_POST['wpsc-product-decrease-inventory-new-order'];
    
    $table_name = $wpdb->prefix . "wpstorecart_orders";

    $insert = "INSERT INTO `{$table_name}` (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`, `wpuser`, `email`, `affiliate`, `date`) VALUES (NULL, '{$wpscneworderstatus}', '{$wpscnewordercartcontents}', '{$wpscneworderpaymentprocessor}', '{$wpscneworderprice}', '{$wpscnewordershipping}', '{$wpscneworderuser}', '{$wpscneworderemail}', '{$wpscneworderaffiliate}', '{$wpscneworderdate}');";

    $wpdb->query($insert);
    $keyToLookup = $wpdb->insert_id;
    
    if($_POST['wpsc-new-order-log']==1 || $wpscproductdecreaseinventoryneworder=='yes') {
        $productIds = wpscSplitOrderIntoProductKeys($keyToLookup);
        foreach($productIds as $productId) {
            if($_POST['wpsc-new-order-log']==1) {
                increaseProductPurchasedStatistic($productId);
            }
            if($wpscproductdecreaseinventoryneworder=='yes') {
                wpscProductDecreaseProductInventory($productId);
            }
        }
    }
    
    echo $insert;
}
?>