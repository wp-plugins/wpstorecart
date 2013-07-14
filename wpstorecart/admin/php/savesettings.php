<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $current_user, $wpstorecart_settings_obj;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    error_reporting(E_ALL);
    
    wpsc_admin_save_settings(); // Action hook for when admin settings are saved.
    
    $wpstorecart_settings_obj->setAdminOptions();
    
}
?>