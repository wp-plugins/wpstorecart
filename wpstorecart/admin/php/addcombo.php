<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpdb, $current_user;

$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    $table_name = $wpdb->prefix . "wpstorecart_meta";
    $wpsc_combo_product_names = esc_sql($_POST['wpsc_combo_product_names']);
    $exploded = explode('||', $wpsc_combo_product_names);
    $wpsc_combo_discount_price = esc_sql($_POST['wpsc_combo_discount_price']);
    $wpsc_combo_primkey = esc_sql($_POST['wpsc_combo_primkey']);


    $insert = "
    INSERT INTO `{$table_name}` (
    `primkey`, `value`, `type`, `foreignkey`)
    VALUES (
            NULL,
            '{$exploded[0]}||{$wpsc_combo_discount_price}',
            'productcombo',
            '{$wpsc_combo_primkey}'
    );
    ";

    $results = $wpdb->query($insert);
    $lastID = $wpdb->insert_id;
    echo $lastID;


}
?>