<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

$output = NULL;

$wpsc_cat_key = intval($_GET['wpsc_cat_key']);
if($wpsc_cat_key==0) {
    $wpsc_cat_key = null;
}


$output .= wpscProductGetToolbar( $wpsc_cat_key );

echo $output;

?>