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

    $wpscneworderaffiliate = esc_sql($_POST['wpsc-new-order-affiliate']);
    $wpscnewordercartcontents = esc_sql($_POST['wpsc-new-order-cart-contents']);
    $wpscneworderdate = esc_sql($_POST['wpsc-new-order-date']);
    $wpscneworderemail = esc_sql($_POST['wpsc-new-order-email']);
    $wpscneworderpaymentprocessor = esc_sql($_POST['wpsc-new-order-payment-processor']);
    $wpscneworderprice = esc_sql($_POST['wpsc-new-order-price']);
    $wpscnewordershipping = esc_sql($_POST['wpsc-new-order-shipping']);
    $wpscneworderstatus = esc_sql($_POST['wpsc-new-order-status']);
    $wpscneworderuser = esc_sql($_POST['wpsc-new-order-user']);
    $wpscproductdecreaseinventoryneworder = $_POST['wpsc-product-decrease-inventory-new-order'];
    
    $table_name = $wpdb->prefix . "wpstorecart_orders";

    $insert = "INSERT INTO `{$table_name}` (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`, `wpuser`, `email`, `affiliate`, `date`) VALUES (NULL, '{$wpscneworderstatus}', '{$wpscnewordercartcontents}', '{$wpscneworderpaymentprocessor}', '{$wpscneworderprice}', '{$wpscnewordershipping}', '{$wpscneworderuser}', '{$wpscneworderemail}', '{$wpscneworderaffiliate}', '{$wpscneworderdate}');";

    $wpdb->query($insert);
    $keyToLookup = $wpdb->insert_id;
    
    if(@$_POST['wpsc-new-order-log']==1) {
        $productIds = wpscSplitOrderIntoProductKeys($keyToLookup);
        foreach($productIds as $productId) {
            increaseProductPurchasedStatistic($productId);
        }
        wpscSendSuccessfulPurchaseEmail($wpscneworderemail);
    }
    
    if($wpscproductdecreaseinventoryneworder=='yes') {
        $productIds = wpscSplitOrderIntoProductKeys($keyToLookup);
        foreach($productIds as $productId) {
            wpscProductDecreaseProductInventory($productId);
        }
    }
    
    echo $insert;
}
?>