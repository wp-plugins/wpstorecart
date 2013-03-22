<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpdb;

$table_name = $wpdb->prefix . "wpstorecart_products";

$theResults = $wpdb->get_results("SELECT `name`, `primkey` FROM `{$table_name}` WHERE `producttype`='product' LIMIT 0, 15;", ARRAY_A);

$output = null;

if(isset($theResults[0]['name'])) {

    $output .= '<ul id="wpscEditProductsSubmenuUL">';
    
    foreach($theResults as $theResult) {
        $output .= '<li><img src="'. plugins_url() . '/wpstorecart/images/bullet_green.png" class="wpsc-admin-submenu-icon" /> <a href="admin.php?page=wpstorecart-new-product&keytoedit='.$theResult['primkey'].'">'.$theResult['name'].'</a></li>';
    }
    $output .= '</ul>';

}

echo $output;

?>