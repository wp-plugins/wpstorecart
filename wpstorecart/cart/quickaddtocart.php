<?php

if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}

global $wpdb;

$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

$item_id = intval($_GET['wpsc_pid']);
$item_qty = intval($_GET['wpsc_qty']);
$item_price = wpscProductGetPrice($item_id);

$results = $wpdb->get_results("SELECT `name`, `postid`, `producttype`, `shipping`, `thumbnail` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$item_id}';", ARRAY_A);
if($results[0]['producttype']=='product') {
    $item_name = $results[0]['name'];
}
if($results[0]['producttype']=='variation' || $results[0]['producttype']=='attribute') {
    $varresults = $wpdb->get_results("SELECT `name` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$results[0]['postid']}';", ARRAY_A);
    $item_name = $varresults[0]['name'] . ' - ' . $results[0]['name'];
}
$item_shipping = $results[0]['shipping'];
$item_img = $results[0]['thumbnail'];
$item_url = get_permalink($results[0]['postid']);

$wpsc_shoppingcart = new wpsc_shoppingcart();

$wpsc_shoppingcart->add_item($item_id, $item_qty, $item_price, $item_name, $item_shipping, $item_img, $item_url, $item_img);

if(@isset($_GET['wpsc-app-store'])) {
    if(strpos(get_permalink($wpStoreCartOptions['checkoutpage']),'?')===false) {
        $permalink = get_permalink($wpStoreCartOptions['checkoutpage']) .'?wpsc-app-store=1';
    } else {
        $permalink = get_permalink($wpStoreCartOptions['checkoutpage']) .'&wpsc-app-store=1';
    }    
} else {
    $permalink = get_permalink($wpStoreCartOptions['checkoutpage']);
}

echo '
<script type="text/javascript">
/* <![CDATA[ */
window.location = "'.$permalink.'"
/* ]]> */
</script>
';          
exit;
 

?>