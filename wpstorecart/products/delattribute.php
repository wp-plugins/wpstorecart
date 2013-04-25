<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
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

    $keytodelete = intval($_POST['keytodelete']);

    $preresults = $wpdb->get_results("SELECT `productkey` FROM `{$wpdb->prefix}wpstorecart_quickvar` WHERE `primkey`='{$keytodelete}';", ARRAY_A);
    if(isset($preresults[0]['productkey'])) {

        $wpdb->query("DELETE FROM `{$wpdb->prefix}wpstorecart_quickvar` WHERE `primkey`='{$keytodelete}';");

        $results = $wpdb->get_results("SELECT `primkey`, `status` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `producttype`='attribute' AND  `postid`='{$preresults[0]['productkey']}' ;", ARRAY_A);
        if(isset($results[0]['primkey'])) {
            foreach($results as $result) {
                $exploder = explode('A', $result['status']);
                foreach ($exploder as $exploded) {
                    if($exploded==$keytodelete) {
                        $wpdb->query("DELETE FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$result['primkey']}';");
                    }
                }
            }
        }
        
    }
}

?>