<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpdb, $current_user;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    $wpscorderid = esc_sql($_POST['wpsc-orderid']);
    $wpscaddnewproduct = esc_sql($_POST['wpsc-add-new-product']);
    $wpscproductqtyaddtoorder = esc_sql($_POST['wpsc-product-qty-add-to-order']);    
    $wpscDecreaseInventory = $_POST['wpsc-product-decrease-inventory-add-to-order'];
    if($wpscDecreaseInventory=='yes') {
        wpscProductDecreaseProductInventory($wpscaddnewproduct, $wpscproductqtyaddtoorder);
    }
    
    $table_name = $wpdb->prefix . "wpstorecart_orders";
    $findx = $wpdb->get_results("SELECT `cartcontents` FROM `{$table_name}` WHERE `primkey`='{$wpscorderid}';", ARRAY_A);

    $update = $wpscaddnewproduct.'*'.$wpscproductqtyaddtoorder.','.$findx[0]['cartcontents'];
    $test = $wpscaddnewproduct.'*'.$wpscproductqtyaddtoorder.',';
    
    if($test!='*,' && (is_numeric($wpscaddnewproduct) && is_numeric($wpscproductqtyaddtoorder)) && (strlen($test)>=4)) {
        $insert = "UPDATE `{$table_name}` SET `cartcontents`='{$update}' WHERE `primkey`='{$wpscorderid}';";

        $wpdb->query($insert);
    }

}
?>