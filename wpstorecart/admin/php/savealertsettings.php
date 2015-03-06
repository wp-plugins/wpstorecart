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

    error_reporting(E_ALL);
    
    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    @add_user_meta( $current_user->ID, 'wpsc_alert_text_phone_number', esc_sql($_POST['wpsc-alert-text-phone-number']), true);
    @update_user_meta($current_user->ID, 'wpsc_alert_text_phone_number', esc_sql($_POST['wpsc-alert-text-phone-number']));
    @add_user_meta( $current_user->ID, 'wpsc_alert_carrier', esc_sql($_POST['wpsc-alert-carrier']), true);
    @update_user_meta( $current_user->ID, 'wpsc_alert_carrier', esc_sql($_POST['wpsc-alert-carrier']));
    @add_user_meta( $current_user->ID, 'wpsc_full_alert_email', esc_sql($_POST['wpsc-full-alert-email'], true));
    @update_user_meta( $current_user->ID, 'wpsc_full_alert_email', esc_sql($_POST['wpsc-full-alert-email']));


}
?>