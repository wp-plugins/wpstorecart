<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

global $wpdb;

$productkey = intval($_POST['productkey']);

$wpdb->query("INSERT `{$wpdb->prefix}wpstorecart_packages` (`primkey`, `productkey`, `weight`, `length`, `width`, `depth`, `options`) VALUES (NULL, '{$productkey}', '0', '0', '0', '0', '');");

echo $wpdb->insert_id;

?>