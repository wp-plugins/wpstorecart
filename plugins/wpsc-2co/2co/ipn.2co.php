<?php

// wpStoreCart, LLC (c) 2010, 2011 wpStoreCart.com.  All rights reserved.

global $wpsc_testing_mode;
if($wpsc_testing_mode==false) {
    error_reporting(0);
}
global $wpdb;
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}
$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

// Include the paypal library
include_once ('gateway.2co.php');

// Create an instance of the authorize.net library
$my2CO = new TwoCo();

// Log the IPN results
$my2CO->ipnLog = TRUE;

// Specify your authorize login and secret
$my2CO->setSecret($wpStoreCartOptions['2checkoutemail']);

if($wpStoreCartOptions['2checkouttestmode']=='true') {
    // Enable test mode if needed
    $my2CO->enableTestMode();
}
// Check validity and write down it
if ($my2CO->validateIpn())
{
    file_put_contents('2co.txt', 'SUCCESS');
     $keyToLookup = $my2CO->ipnData['cart_order_id'];
     if(is_numeric($keyToLookup)) {
            $table_name = $wpdb->prefix . "wpstorecart_orders";
            $table_name2 = $wpdb->prefix . "wpstorecart_products";
            $status = $my2CO->ipnData['credit_card_processed'];
            if ($my2CO->ipnData['credit_card_processed']=='y' || $my2CO->ipnData['credit_card_processed']=='Y') {
                $status = 'Completed';
            }
            if ($my2CO->ipnData['credit_card_processed']=='k' || $my2CO->ipnData['credit_card_processed']=='K') {
                $status = 'Pending';
            }

            $insert = "
            UPDATE `{$table_name}` SET `orderstatus` = '{$status}' WHERE `primkey` ={$keyToLookup};
            ";

            $results = $wpdb->query( $insert );

            // If we've got a successful payment and we are using the inventory:
            if ($my2CO->ipnData['credit_card_processed']=='y' || $my2CO->ipnData['credit_card_processed']=='Y') {
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

            if(isset($results)) {
                wpscSendSuccessfulPurchaseEmail($results[0]['email']);
                
                header ('HTTP/1.1 301 Moved Permanently');
                if($status == 'Completed') { 
                    if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                        if(!headers_sent()) {
                            header ('Location: '.get_permalink($wpStoreCartOptions['mainpage']).'?wpsc=success');
                        } else {
                            echo '<script type="text/javascript">
                            <!--
                            window.location = "'.get_permalink($wpStoreCartOptions['mainpage']).'?wpsc=success"
                            //-->
                            </script>';
                        }
                    } else {
                        if(!headers_sent()) {
                            header ('Location: '.get_permalink($wpStoreCartOptions['mainpage']).'&wpsc=success');
                        } else { 
                            echo '<script type="text/javascript">
                            <!--
                            window.location = "'.get_permalink($wpStoreCartOptions['mainpage']).'&wpsc=success"
                            //-->
                            </script>';  
                        }
                    }
                } else {
                    if(!headers_sent()) {
                        header ('Location: '.get_permalink($wpStoreCartOptions['mainpage']));
                    } else { 
                        echo '<script type="text/javascript">
                        <!--
                        window.location = "'.get_permalink($wpStoreCartOptions['mainpage']).'"
                        //-->
                        </script>';  
                    }                    
                }
            }

            }
     }
}
else
{
	@wpscLog(NULL, '2CO IPN Failure', $my2CO->ipnData);
}