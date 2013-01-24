<?php
global $wpsc_testing_mode;
if($wpsc_testing_mode==false) {
    error_reporting(0);
}
if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}

global $wpdb;



$table_name = $wpdb->prefix . "wpstorecart_products";

$productsToAdd = $_POST['productsToAddArray'];
$masterProduct = $productsToAdd[0]; //This is the master product, we will use this to see if there are accessory discounts to add

$wpsc_shoppingcart = new wpsc_shoppingcart();


$sql = "SELECT * FROM `{$table_name}` WHERE `primkey`=0 ";
foreach ($productsToAdd as $productToAdd ) {
    $sql .= ' OR `primkey`='.$productToAdd.' ';
}
$sql .= ' OR `primkey`=0;';

$theResults = $wpdb->get_results($sql, ARRAY_A);
$wpsc_shoppingcart->multi_add_item($theResults, 1, $masterProduct);


?>