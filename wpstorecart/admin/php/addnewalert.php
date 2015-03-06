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


    $clearable = esc_sql($_POST['wpsc-new-alert-clearable']);
    $conditions = esc_sql($_POST['wpsc-new-alert-conditions']);
    $description = esc_sql($_POST['wpsc-new-alert-description']);
    $desktop	 = esc_sql($_POST['wpsc-new-alert-desktop']);
    $email	 = esc_sql($_POST['wpsc-new-alert-email']);
    $groupable = esc_sql($_POST['wpsc-new-alert-groupable']);
    $image	 =  esc_sql($_POST['wpsc-new-alert-image']);
    $qty	 = esc_sql($_POST['wpsc-new-alert-qty']);
    $severity	 = esc_sql($_POST['wpsc-new-alert-severity']);
    $status	 = esc_sql($_POST['wpsc-new-alert-status']);
    $title	 = esc_sql($_POST['wpsc-new-alert-title']);
    $txtmsg	 = esc_sql($_POST['wpsc-new-alert-txt-msg']);
    $url	 = esc_sql($_POST['wpsc-new-alert-url']);
    $user	 =  esc_sql($_POST['wpsc-new-alert-user']);
    $wpadmin	 = esc_sql($_POST['wpsc-new-alert-wp-admin']);
    
    $table_name = $wpdb->prefix . "wpstorecart_alerts";

    $insert = "INSERT INTO `{$table_name}` (`primkey`, `title`, `description`, `conditions`, `severity`, `image`, `url`, `qty`, `groupable`, `clearable`, `status`, `userid`, `adminpanel`, `textmessage`, `emailalert`, `desktop`) VALUES (NULL, '{$title}', '{$description}', '{$conditions}', '{$severity}', '{$image}', '{$url}', '{$qty}', '{$groupable}', '{$clearable}', '{$status}', '{$user}', '{$wpadmin}', '{$txtmsg}', '{$email}', '{$desktop}');";

    $wpdb->query($insert);

    echo $insert;
}
?>