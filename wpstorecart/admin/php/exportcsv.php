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

    $table_name = $wpdb->prefix . "wpstorecart_products";
    $updateSQL = "SHOW COLUMNS FROM {$table_name};";

    $results = $wpdb->get_results($updateSQL, ARRAY_A);

    $i = 0;
    $headers = array();
    foreach ($results as $result) {
        $headers[] = $result['Field'];
        $i++;
    }
    
    

    $updateSQL2 = "SELECT * FROM `{$table_name}` WHERE `producttype`='product';";

    $results2 = $wpdb->get_results($updateSQL2, ARRAY_N);

    $fp = fopen('php://output', 'w');
    $filename = "wpscExport_".date("Y-m-d_H-i",time());
    header("Content-type: application/vnd.ms-excel");
    header("Content-disposition: csv" . date("Y-m-d") . ".csv");
    header( "Content-disposition: filename=".$filename.".csv");
    fputcsv($fp, $headers, ',', '"');
    foreach ($results2 as $row) {
        fputcsv($fp, array_values($row), ',', '"');
    }
    fclose($fp);
    
    exit;

}


?>