<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');
@header('Content-type: application/json');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

global $wpdb;
 
$wpscAttributeKey = esc_sql($_POST['wpscAttributeKey']);

$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `producttype`='attribute' AND `status`='{$wpscAttributeKey}';", ARRAY_A);
if(isset($results[0]['primkey'])) {
    echo json_encode($results[0]);
}

?>