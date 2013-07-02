<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

 
$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

$output = NULL;

$wpsc_cat_key = intval($_GET['wpsc_cat_key']);
if($wpsc_cat_key==0) {
    $wpsc_cat_key = null;
}
  

$output .= wpscProductGetCatalog($wpStoreCartOptions['itemsperpage'], $wpsc_cat_key, $wpStoreCartOptions['frontpageDisplays']);

echo $output;

?>