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

    $table_name = $wpdb->prefix . esc_sql($_POST['tablename']);
    $primkey = esc_sql($_POST['primkey']);
    
    $insert = "DELETE FROM `{$table_name}` WHERE `primkey`={$primkey};";

    $wpdb->query($insert);

}
?>