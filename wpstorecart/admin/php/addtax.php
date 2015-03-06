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

    $table_name = $wpdb->prefix . "wpstorecart_meta";
    $taxname = esc_sql($_POST['taxname']);
    $countriestotax = esc_sql($_POST['countriestotax']);
    $statestotax = esc_sql($_POST['statestotax']);
    $taxpercent = esc_sql($_POST['taxpercent']);
    $taxprimkey = esc_sql($_POST['taxprimkey']);

    $insert = NULL;
    if($taxprimkey==0) {
        $insert = "
        INSERT INTO `{$table_name}` (
        `primkey`, `value`, `type`, `foreignkey`)
        VALUES (
                NULL,
                '{$taxname}||{$countriestotax}||{$statestotax}||{$taxpercent}',
                'tax',
                '0'
        );
        ";

        $results = $wpdb->query($insert);
        $lastID = $wpdb->insert_id;
        echo $lastID;
    } else {
       if(is_numeric($taxprimkey)) {
            $insert = "
            UPDATE `{$table_name}` SET `value`='{$taxname}||{$countriestotax}||{$statestotax}||{$taxpercent}' WHERE `primkey`='{$taxprimkey}';
            ";
            $results = $wpdb->query($insert);
            echo $taxprimkey;
       }
    }




}
?>