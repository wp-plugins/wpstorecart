<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
@header('Content-type: application/json');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

global $wpdb;
 
$wpscVarKey = intval($_POST['wpscVarKey']);

$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$wpscVarKey}';", ARRAY_A);
if(isset($results[0]['primkey'])) {
    echo json_encode($results[0]);
}

?>