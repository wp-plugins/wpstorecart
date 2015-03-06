<?php

global $wpStoreCartOptions, $wpdb, $current_user;


if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}


$wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 

$alert_id = intval(esc_sql($_POST['alert_id'])); // The Alert we're dealing with

$curAlerts = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_alerts` WHERE `primkey`='{$alert_id}';", ARRAY_A);
if(isset($curAlerts[0])) {
    
    $current_user = wp_get_current_user();
    if ( $current_user->ID != $curAlerts[0]['userid'] ) { // This needs to be the current logged in user who is clearing this alert.  If they don't match, the script terminates
        _e('Unauthorized Access', 'wpstorecart');
        exit();
    } else {
        $userid = $current_user->ID;
        _e('User validated', 'wpstorecart');
    }


    $codeline = $curAlerts[0]['conditions'];
    
    // This code lets us know what type of operations are required
    $operator['equal']=false;$operator['lessthan']=false; $operator['greaterthan']=false; $operator['notequal']=false; $operator['time']=false;             
    if(strpos($codeline, '=')!==false) {$operator['equal']=true;}
    if(strpos($codeline, '<')!==false) {$operator['lessthan']=true;}
    if(strpos($codeline, '>')!==false) {$operator['greaterthan']=true;}
    if(strpos($codeline, '!')!==false) {$operator['notequal']=true;}
    if(strpos($codeline, '@')!==false) {$operator['time']=true;}            

    // Grabs our possible parameter values from the codeline
    $value[0] = NULL; $value[1] = NULL; $value[2] = NULL;
    preg_match_all('#\((.*?)\)#', $codeline, $parameters); // Max, 3 parameters per command, this regex grabs the values from inside paranthesis()
    if(isset($parameters[1][0])) {$value[0] = trim($parameters[1][0]);$codeline = str_replace($parameters[0][0], '', $codeline);}
    if(isset($parameters[1][1])) {$value[1] = trim($parameters[1][1]);$codeline = str_replace($parameters[0][1], '', $codeline);}  
    if(isset($parameters[1][2])) {$value[2] = trim($parameters[1][2]);$codeline = str_replace($parameters[0][2], '', $codeline);}      
    
    //Splits the commands up based on the allowed operators         
    $commands = preg_split( "/ (@|=|>|<|!) /", $codeline);    
    
    echo $commands[0];
    
    if($commands[0]=='newsales') { // If we're clearing a new sale alert, we need to 
        // Get the record for number of sales
        $historic_record_of_completed_orders_res = $wpdb->get_results("SELECT `primkey`, `value` FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `type`='totalsalesamount' AND `foreignkey`='{$userid}';" , ARRAY_A);
        if(@isset($historic_record_of_completed_orders_res[0]['value'])) {
            $historic_record_of_completed_orders = $historic_record_of_completed_orders_res[0]['value'];
            $historic_record_of_completed_orders_key = $historic_record_of_completed_orders_res[0]['primkey'];
        } else {
            $historic_record_of_completed_orders = 0;
            $historic_record_of_completed_orders_key = NULL;
        }

        // Get the current number of sales
        $totalrecordsresordercompleted = $wpdb->get_results( "SELECT COUNT(`primkey`) AS num FROM `{$wpdb->prefix}wpstorecart_orders` WHERE `orderstatus`='Completed';" , ARRAY_A );
        if(isset($totalrecordsresordercompleted)) {
                $current_completed_orders = $totalrecordsresordercompleted[0]['num'];
        } else {
                $current_completed_orders = 0;
        }

        // Compares the historic data with the current data, and if there are more sales since the historic data was last updated, update the historic records to the new number
        if($current_completed_orders > $historic_record_of_completed_orders) {
                if($historic_record_of_completed_orders_key==NULL) {
                    $wpdb->query("INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` , `value` , `type` , `foreignkey` ) VALUES (NULL , '{$current_completed_orders}', 'totalsalesamount', '{$userid}');");
                } else {
                    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_meta` SET `value` = '{$current_completed_orders}' WHERE `primkey` ='{$historic_record_of_completed_orders_key}';");
                }
        }          
    } 

    
    // This is a catch all to clear other alerts
    @$wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_log` SET `action` = 'alert_delayed', `time`='".date( 'Y-m-d H:i:s')."' WHERE `foreignkey` ='{$alert_id}' AND `userid`='{$userid}' AND `action`='alert';");
    
    
}


?>