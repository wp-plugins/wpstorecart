<?php

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

if (!is_user_logged_in()) {
    header('Location: '.wp_login_url( plugins_url().'/wpstorecart/wpstorecart/alerts/alertsfeed.php' ));
    die();
}

global $current_user;
$current_user = wp_get_current_user();

if(@$_GET['return']=='plain') {
    echo wpscDisplayAlerts($current_user->ID, 'plain', false);
} else {
    ?><!DOCTYPE html>
    <html xmlns="http://www.w3.org/1999/xhtml" lang="en-US" id="wpsc-alerts-feed" dir="ltr">
    <head>
    <meta http-equiv="Content-Type" content="text/html; charset=UTF-8" />
    </head>
        <body>

        <?php
        echo wpscDisplayAlerts($current_user->ID, 'desktop');
        ?>

        </body>
    </html><?php
}

die();
?>