<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

global $wpdb;

$keytodelete = intval($_POST['keytodelete']);

$wpdb->query("DELETE FROM `{$wpdb->prefix}wpstorecart_packages` WHERE `primkey`={$keytodelete};");

return $wpdb->insert_id;

?>