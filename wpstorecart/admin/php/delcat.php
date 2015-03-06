<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpdb, $current_user, $wpstorecart_settings_obj;

$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    $delete = esc_sql($_POST['delete']);

    $table_name = $wpdb->prefix . "wpstorecart_categories";
    $insert = "DELETE FROM `{$table_name}` WHERE `primkey`={$delete};";

    $results = $wpdb->query($insert);

}
?>