<?php
error_reporting(0);
if (!function_exists('add_action'))
{
    require_once("../../../../wp-config.php");
}

global $current_user,$wpdb;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
            die(__('Cheatin&#8217; uh?'));
    }

    $table_name = $wpdb->prefix . "wpstorecart_meta";
    $createnewvar = $wpdb->escape($_POST['createnewvar']);
    $varvalue = $wpdb->escape($_POST['varvalue']);
    $varprice = $wpdb->escape($_POST['varprice']);
    $vardesc = $wpdb->escape($_POST['vardesc']);
	$vardownloads = $wpdb->escape($_POST['vardownloads']);
    $keytoedit = $wpdb->escape($_POST['keytoedit']);

    $insert = "
    INSERT INTO `{$table_name}` (
    `primkey`, `value`, `type`, `foreignkey`)
    VALUES (
            NULL,
            '{$createnewvar}||{$varvalue}||{$varprice}||{$vardesc}||{$vardownloads}',
            'productvariation',
            '{$keytoedit}'
    );
    ";

    $results = $wpdb->query($insert);
    $lastID = $wpdb->insert_id;
    echo $lastID;


}
?>