<?php

// wpStoreCart, (c) 2010-2012 wpStoreCart.com.  All rights reserved.

global $wpsc_testing_mode;
if($wpsc_testing_mode==false) {
    error_reporting(0);
}

global $wpdb;

if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}
$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');


// Include the paypal library
include_once(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/payment/gateway.paypal.php');

// Create an instance of the paypal library
$myPaypal = new Paypal();

// Log the IPN results
$myPaypal->ipnLog = TRUE;

if($wpStoreCartOptions['paypaltestmode']=='true') {
        $myPaypal->enableTestMode();
}

// Check validity and write down it
if ($myPaypal->validateIpn()) {
     $keyToLookup = $myPaypal->ipnData['custom'];
     if(is_numeric($keyToLookup)) {
            $table_name = $wpdb->prefix . "wpstorecart_orders";
            $table_name2 = $wpdb->prefix . "wpstorecart_products";
            if($myPaypal->ipnData['payment_status']=='Canceled_Reversal' || $myPaypal->ipnData['payment_status']=='Completed') {
                 $insert = "
                UPDATE `{$table_name}` SET `orderstatus` = 'Completed' WHERE `primkey` ={$keyToLookup};
                ";
            } else {
                $insert = "
                UPDATE `{$table_name}` SET `orderstatus` = '{$myPaypal->ipnData['payment_status']}' WHERE `primkey` ={$keyToLookup};
                ";
            }

            $stop = false;
            if($myPaypal->ipnData['txn_type']=='subscr_signup') { // This will count against inventory, count as a new sale, and count towards affiliates
                $insert = "UPDATE `{$table_name}` SET `orderstatus` = 'Completed' WHERE `primkey` ={$keyToLookup};";
            }
            if($myPaypal->ipnData['txn_type']=='subscr_cancel') {
                $insert = '';
                $stop = true; // Don't recount this as a new sale, don't decrease inventory, don't credit affiliate
                exit();
            }
            if($myPaypal->ipnData['txn_type']=='subscr_modify') {
                $insert = '';
                $stop = true; // Don't recount this as a new sale, don't decrease inventory, don't credit affiliate
                exit();
            }
            if($myPaypal->ipnData['txn_type']=='subscr_payment') {
                $insert = "UPDATE `{$table_name}` SET `orderstatus` = 'Completed' WHERE `primkey` ={$keyToLookup};";
                $stop = true; // Don't recount this as a new sale, don't decrease inventory, don't credit affiliate
                $results = $wpdb->query( $insert );
                exit();
            }
            if($myPaypal->ipnData['txn_type']=='subscr_failed') {
                $insert = '';
                $stop = true; // Don't recount this as a new sale, don't decrease inventory, don't credit affiliate
                $results = $wpdb->query( $insert );
                exit();
            }
            if($myPaypal->ipnData['txn_type']=='subscr_eot') {
                $insert = "UPDATE `{$table_name}` SET `orderstatus` = 'Expired' WHERE `primkey` ={$keyToLookup};";
                $stop = true; // Don't recount this as a new sale, don't decrease inventory, don't credit affiliate
                $results = $wpdb->query( $insert );
                exit();
            }

            if($insert != '') {
                $results = $wpdb->query( $insert );
            }
            // If we've got a successful payment and we are using the inventory:
            if($myPaypal->ipnData['payment_status']=='Completed' && $stop == false) {
                $sql = "SELECT `cartcontents`, `email` FROM `{$table_name}` WHERE `primkey`={$keyToLookup};";
		$results = $wpdb->get_results( $sql , ARRAY_A );
                if(isset($results)) {
                    $specific_items = explode(",", $results[0]['cartcontents']);
                    foreach($specific_items as $specific_item) {
                        if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                            $current_item = explode('*', $specific_item);
                            if(isset($current_item[0]) && isset($current_item[1])) {
                                $sql2 = "SELECT `primkey`, `inventory`, `useinventory` FROM `{$table_name2}` WHERE `primkey`={$current_item[0]};";
                                wpscAssignSerialNumber($current_item[0], $keyToLookup);
                                $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
                                if(isset($moreresults) && $moreresults[0]['useinventory']==1) {
                                        $newInventory = $moreresults[0]['inventory'] - $current_item[1];
                                        $wpdb->query("UPDATE `{$table_name2}` SET `inventory` = '{$newInventory}' WHERE `primkey` = {$moreresults[0]['primkey']} LIMIT 1 ;");
                                }
                            }
                        }
                    }
                }

                wpscSendSuccessfulPurchaseEmail($results[0]['email']);

            }
     }
}