<?php

// Copyright (c) 2011, 2012 wpStoreCart, LLC.  All rights reserved.

global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
global $wpdb;

if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

$wpstorecart_settings_obj = new wpscSettings();
$devOptions = $wpstorecart_settings_obj->getAdminOptions(); // Grabs the settings   

$table_name = $wpdb->prefix . "wpstorecart_orders";
$table_name2 = $wpdb->prefix . "wpstorecart_products";


$concatFields = $_POST['merchant_id'].$_POST['transaction_id'].strtoupper(md5($devOptions['mb_secretword'])).$_POST['mb_amount'].$_POST['mb_currency'].$_POST['status'];
$MBEmail = $devOptions['mb_login'];

if (strtoupper(md5($concatFields)) == $_POST['md5sig'] && $_POST['pay_to_email'] == $MBEmail) {
    // Valid transaction.
    $order_id = intval($_POST['order_id']);
    $keyToLookup = intval($_POST['order_id']);

    if($_POST['status'] == 2) {
        // ALL COOL, mark the order paid
        $sql = "UPDATE `{$table_name}` SET `orderstatus` = 'Completed' WHERE `primkey` = {$order_id}";
        $wpdb->query ($sql);
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

        // Let's send them an email telling them their purchase was successful
        // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap(wpscMakeEmailTxt($devOptions['emailonapproval']) . wpscMakeEmailTxt($devOptions['emailsig']), 70);

        $headers = 'From: '.$devOptions['wpStoreCartEmail'] . "\r\n" .
        'Reply-To: ' .$devOptions['wpStoreCartEmail']. "\r\n" .
        'X-Mailer: PHP/wpStoreCart';

        // Send an email when purchase is submitted
        if(isset($results)) {
                mail($results[0]['email'], 'Your order has been fulfilled!', $message, $headers);
        }
    }
    if($_POST['status'] == 0) {
        //Pending
        $sql = "UPDATE `{$table_name}` SET `orderstatus` = 'Pending' WHERE `primkey` = {$order_id}";
        $wpdb->query ($sql);
    }
    if($_POST['status'] == -1) {
        //Cancelled
        $sql = "UPDATE `{$table_name}` SET `orderstatus` = 'Cancelled' WHERE `primkey` = {$order_id}";
        $wpdb->query ($sql);
    }
    if($_POST['status'] == -3) {
        //Chargeback
        $sql = "UPDATE `{$table_name}` SET `orderstatus` = 'Chargeback' WHERE `primkey` = {$order_id}";
        $wpdb->query ($sql);
    }
}
else
{
    // Invalid transaction. Bail out
    exit;
}
?>