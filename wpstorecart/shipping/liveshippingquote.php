<?php
@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');


if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

global $wpdb;

$wpsc_json_cart = $_POST['wpsc_json_cart'];

//print_r($wpsc_json_cart);
/*

This gives us an array with all products in cart like such:

Array
(
    [0] => Array
        (
            [0] => 12
            [1] => 1
            [2] => 7.99
            [3] => wpsc Statistics PRO
            [4] => 0.00
            [5] => 0
            [6] => http://127.0.0.1/wordpress2/store-13/wpsc-statistics-pro/
            [7] => http://wpstorecart.com/wp-content/uploads/wpstorecart/wpsc_Statistics_PRO.jpg
            [8] => 7.99
        )

    [1] => Array
        (
            [0] => 11
            [1] => 1
            [2] => 14.99
            [3] => wpsc Affiliate PRO8
            [4] => 0.00
            [5] => 0
            [6] => http://127.0.0.1/wordpress2/store-13/wpsc-affiliate-pro8/
            [7] => http://127.0.0.1/wordpress2/wp-content/uploads/wpstorecart/stats.png
            [8] => 14.99
        )

)


 */

$wpsc_shipping_selection = $_POST['wpsc_shipping_selection'];
$wpscCurrentShippingFunction = 'wpscShippingAPIGetQuote_'.$wpsc_shipping_selection;
if(@function_exists($wpscCurrentShippingFunction)) {
    $totalshippingcalculated .= @$wpscCurrentShippingFunction(); // Magically calls the function
}

echo number_format($totalshippingcalculated, 2);



?>