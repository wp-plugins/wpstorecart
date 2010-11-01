<?php

// wpStoreCart, (c) 2010 wpStoreCart.com.  All rights reserved.

error_reporting(0);
global $wpdb, $wpStoreCart;

if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}
$devOptions = $wpStoreCart->getAdminOptions();


// Include the paypal library
include_once ('Paypal.php');

// Create an instance of the paypal library
$myPaypal = new Paypal();

// Log the IPN results
$myPaypal->ipnLog = TRUE;

if($devOptions['paypaltestmode']=='true') {
        $myPaypal->enableTestMode();
}

// Check validity and write down it
if ($myPaypal->validateIpn())
{
     $keyToLookup = $myPaypal->ipnData['custom'];
     if(is_numeric($keyToLookup)) {
            $table_name = $wpdb->prefix . "wpstorecart_orders";
            $table_name2 = $wpdb->prefix . "wpstorecart_products";
            $insert = "
            UPDATE `{$table_name}` SET `orderstatus` = '{$myPaypal->ipnData['payment_status']}' WHERE `primkey` ={$keyToLookup};
            ";

            $results = $wpdb->query( $insert );

            // If we've got a successful payment and we are using the inventory:
            if($myPaypal->ipnData['payment_status']=='Completed') {
                $sql = "SELECT `cartcontents` FROM `{$table_name}` WHERE `primkey`={$keyToLookup};";
		$results = $wpdb->get_results( $sql , ARRAY_A );
                if(isset($results)) {
                    $specific_items = explode(",", $results[0]['cartcontents']);
                    foreach($specific_items as $specific_item) {
                        if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                            $current_item = explode('*', $specific_item);
                            if(isset($current_item[0]) && isset($current_item[1])) {
                                $sql2 = "SELECT `primkey`, `inventory`, `useinventory` FROM `{$table_name2}` WHERE `primkey`={$current_item[0]};";
                                $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
                                if(isset($moreresults) && $moreresults[0]['useinventory']==1) {
                                        $newInventory = $moreresults[0]['inventory'] - $current_item[1];
                                        $wpdb->query("UPDATE `{$table_name2}` SET `inventory` = '{$newInventory}' WHERE `primkey` = {$moreresults[0]['primkey']} LIMIT 1 ;");
                                }
                            }
                        }
                    }
                }

             // Let's send them an email telling them their purchase was successful
             // In case any of our lines are larger than 70 characters, we should use wordwrap()
            $message = wordwrap($wpStoreCart->makeEmailTxt($devOptions['emailonapproval']) . $wpStoreCart->makeEmailTxt($devOptions['emailsig']), 70);

            $headers = 'From: '.$current_user->user_email . "\r\n" .
                'Reply-To: ' .$current_user->user_email. "\r\n" .
                'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;

            // Send an email when purchase is submitted
            mail($current_user->user_email, 'Your order has been fulfilled!', $message, $headers);

            }
     }
}