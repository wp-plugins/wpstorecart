<?php

function wpscBasicStatsWidgets() {
    global $wpdb, $wpstorecart_settings;
    
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    $table_name = $wpdb->prefix . "wpstorecart_products";
    $table_name_orders = $wpdb->prefix . "wpstorecart_orders";

    $totalrecordssql = "SELECT COUNT(`primkey`) AS num FROM `{$table_name}`";
    $totalrecordsres = $wpdb->get_results( $totalrecordssql , ARRAY_A );
    if(isset($totalrecordsres)) {
            $totalrecords = $totalrecordsres[0]['num'];
    } else {
            $totalrecords = 0;
    }

    $totalrecordssqlorder = "SELECT COUNT(`primkey`) AS num FROM `{$table_name_orders}`";
    $totalrecordsresorder = $wpdb->get_results( $totalrecordssqlorder , ARRAY_A );
    if(isset($totalrecordsresorder)) {
            $totalrecordsorder = $totalrecordsresorder[0]['num'];
    } else {
            $totalrecordsorder = 0;
    }

    $totalrecordssqlordercompleted = "SELECT COUNT(`primkey`) AS num FROM `{$table_name_orders}` WHERE `orderstatus`='Completed';";
    $totalrecordsresordercompleted = $wpdb->get_results( $totalrecordssqlordercompleted , ARRAY_A );
    if(isset($totalrecordsresordercompleted)) {
            $totalrecordsordercompleted = $totalrecordsresordercompleted[0]['num'];
    } else {
            $totalrecordsordercompleted = 0;
    }


    $orderpercentage = @round($totalrecordsordercompleted / $totalrecordsorder * 100);

    $startdate =date("Ymd", strtotime("30 days ago"));
    $enddate = date("Ymd");

    $theSQL = "SELECT SUM(`price`) AS `thetotal` FROM `{$table_name_orders}` WHERE `orderstatus`='Completed';";
    $salesAllTime = $wpdb->get_results( $theSQL , ARRAY_A );
    $allTimeGrossRevenue = 0;
    foreach ($salesAllTime as $sat) {
        $allTimeGrossRevenue = $sat['thetotal'];
    }

    $theSQL = "SELECT `date`, `price` FROM `{$table_name_orders}` WHERE `date` > {$startdate} AND `date` <= {$enddate} AND `orderstatus`='Completed' ORDER BY `date` DESC;";
    $salesThisMonth = $wpdb->get_results( $theSQL , ARRAY_A );
    $currentDay = $enddate;
    $dayAgo = 0 ;
    $highestNumber = 0;
                            $totalearned = 0;
    while($currentDay != $startdate) {
        $salesOnDay[$currentDay] = 0;
        foreach($salesThisMonth as $currentSale) {
            if($currentDay == $currentSale['date']) {
                $salesOnDay[$currentDay] = $salesOnDay[$currentDay] + 1;
                $totalearned = $totalearned + $currentSale['price'];
            }
        }
        if($salesOnDay[$currentDay] > $highestNumber) {
            $highestNumber = $salesOnDay[$currentDay];
        }
        $dayAgo++;
        $currentDay = date("Ymd", strtotime("{$dayAgo} days ago"));

    }
    $dayAgo = 29 ;
    $currentDay = $startdate;

    $dailyAverage = $totalearned / 30;

    // inlinebar
    //
    $lastrecordssql = "SELECT * FROM `{$table_name_orders}` ORDER BY `date` DESC LIMIT 0, 30";
    $lastrecords = $wpdb->get_results( $lastrecordssql , ARRAY_A );

    echo '30 days: <strong><span>'.$wpStoreCartOptions['currency_symbol'].number_format($totalearned).$wpStoreCartOptions['currency_symbol_right'].'</span></strong> ('.$wpStoreCartOptions['currency_symbol'].number_format($dailyAverage).$wpStoreCartOptions['currency_symbol_right'].'/day) All Time: <strong><span>'.$wpStoreCartOptions['currency_symbol'].number_format($allTimeGrossRevenue).$wpStoreCartOptions['currency_symbol_right'].'</span></strong><br />';
    echo "<span style=\"float:left;padding:0 0 0 10px;\"><strong>Sales last 30 days:</strong> <br /><img src=\"https://chart.googleapis.com/chart?chxt=y&chbh=a,2&chs=200x50&cht=bvg&chco=224499&chds=0,{$highestNumber}&chd=t:0";while($currentDay != $enddate) {echo $salesOnDay[$currentDay].',';$dayAgo--;$currentDay = date("Ymd", strtotime("{$dayAgo} days ago"));} echo"0\" alt=\"\" /></span><div style=\"clear:both;\"></div>";

    
}



?>