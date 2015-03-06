<?php

/**
 Format of data:
column:	2
id:	
row_id:	wpscid-wpstorecart_orders-1
value:	Cart Contents
*/

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

    $exploded = explode('-', $_POST['row_id']);
    if($exploded[0]=='wpscid' && ($exploded[1]=='wpstorecart_orders' || $exploded[1]=='wpstorecart_alerts' || $exploded[1]=='wpstorecart_coupons' || $exploded[1]=='wpstorecart_quickvar') && is_numeric($exploded[2]) && is_numeric($_POST['column'])) {

        if($exploded[1]=='wpstorecart_orders') {
            $aColumns = array( 'primkey', 'orderstatus', 'cartcontents', 'paymentprocessor', 'price', 'shipping', 'wpuser', 'email', 'affiliate', 'date' );
        }
        if($exploded[1]=='wpstorecart_alerts') {
            $aColumns = array( 'primkey', 'title', 'description', 'conditions', 'severity', 'image', 'url', 'qty', 'groupable', 'clearable', 'status', 'userid', 'adminpanel', 'textmessage', 'emailalert', 'desktop' );
        }   
        if($exploded[1]=='wpstorecart_coupons') {
            $aColumns = array( 'primkey', 'code', 'amount', 'percent', 'description', 'product', 'startdate', 'enddate' );
        }   
        if($exploded[1]=='wpstorecart_quickvar') {
            $aColumns = array( 'primkey', 'productkey', 'values', 'price', 'type', 'title', 'group', 'useinventory' );
        }         

        $value = esc_sql($_POST['value']);

        $wpdb->query("UPDATE `{$wpdb->prefix}{$exploded[1]}` SET `{$aColumns[$_POST['column']]}`='{$value}' WHERE `primkey`={$exploded[2]};");
       

    } else {
        _e('ERROR: Malformed row ID.  Could not complete ajax edit.', 'wpstorecart');
    }

}
?>