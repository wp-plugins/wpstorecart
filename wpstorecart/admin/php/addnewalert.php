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


    $clearable = $wpdb->escape($_POST['wpsc-new-alert-clearable']);
    $conditions = $wpdb->escape($_POST['wpsc-new-alert-conditions']);
    $description = $wpdb->escape($_POST['wpsc-new-alert-description']);
    $desktop	 = $wpdb->escape($_POST['wpsc-new-alert-desktop']);
    $email	 = $wpdb->escape($_POST['wpsc-new-alert-email']);
    $groupable = $wpdb->escape($_POST['wpsc-new-alert-groupable']);
    $image	 =  $wpdb->escape($_POST['wpsc-new-alert-image']);
    $qty	 = $wpdb->escape($_POST['wpsc-new-alert-qty']);
    $severity	 = $wpdb->escape($_POST['wpsc-new-alert-severity']);
    $status	 = $wpdb->escape($_POST['wpsc-new-alert-status']);
    $title	 = $wpdb->escape($_POST['wpsc-new-alert-title']);
    $txtmsg	 = $wpdb->escape($_POST['wpsc-new-alert-txt-msg']);
    $url	 = $wpdb->escape($_POST['wpsc-new-alert-url']);
    $user	 =  $wpdb->escape($_POST['wpsc-new-alert-user']);
    $wpadmin	 = $wpdb->escape($_POST['wpsc-new-alert-wp-admin']);
    
    $table_name = $wpdb->prefix . "wpstorecart_alerts";

    $insert = "INSERT INTO `{$table_name}` (`primkey`, `title`, `description`, `conditions`, `severity`, `image`, `url`, `qty`, `groupable`, `clearable`, `status`, `userid`, `adminpanel`, `textmessage`, `emailalert`, `desktop`) VALUES (NULL, '{$title}', '{$description}', '{$conditions}', '{$severity}', '{$image}', '{$url}', '{$qty}', '{$groupable}', '{$clearable}', '{$status}', '{$user}', '{$wpadmin}', '{$txtmsg}', '{$email}', '{$desktop}');";

    $wpdb->query($insert);

    echo $insert;
}
?>