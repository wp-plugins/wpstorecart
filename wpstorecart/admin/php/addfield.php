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

    $table_name = $wpdb->prefix . "wpstorecart_meta";
    $createnewfieldname = $wpdb->escape($_POST['createnewfieldname']);
    $createnewfieldtype = $wpdb->escape($_POST['createnewfieldtype']);
    $createnewfieldrequired = $wpdb->escape($_POST['createnewfieldrequired']);

    $insert = "
    INSERT INTO `{$table_name}` (
    `primkey`, `value`, `type`, `foreignkey`)
    VALUES (
            NULL,
            '{$createnewfieldname}||{$createnewfieldrequired}||{$createnewfieldtype}',
            'requiredinfo',
            '0'
    );
    ";

    $results = $wpdb->query($insert);
    $lastID = $wpdb->insert_id;
    echo $lastID;


}
?>