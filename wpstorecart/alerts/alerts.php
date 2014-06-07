<?php

wpsc_alert(); // Hook for executing the wpStoreCart Alert system

/**
 * Returns the current alerts for the current user with XHTML formatting
 * @param string $display The format to return the alerts
 * @param boolean $images Displays images or not 
 * @param integer $userid the user ID. If no user ID was specified, then try to grab the current user
 */
if(!function_exists('wpscDisplayAlerts')) {
	function wpscDisplayAlerts($userid=NULL, $display=NULL, $images=true, $style=NULL) {
            global $wpdb;

		$output = NULL;
                $atLeastOne = false;
                $alertCount = 0;
                $isWpAdmin = false; // We use this flag so that we can show/hide alerts for the wp-admin area only 

                if($display=='dashboard') {
                    $isWpAdmin = true;
                    $display='table';
                }
                if($display=='number') {
                    $isWpAdmin = true;
                }

                if($display==NULL) {$output.='<div class="wpsc_alerts">';}
                if($display=='desktop') {$output.='<ul class="wpsc_alerts" style="font-size:18px;font-family:\'Segoe UI\',Arial,serif;list-style:none;">';}
		if($display=='ul') {$output.='<ul class="wpsc_alerts">';}
		if($display=='ol') {$output.='<ol class="wpsc_alerts">';}
                if($display=='table') {$output.='<table class="wpsc_alerts">';}
                if($display=='gritter') {
                    $isWpAdmin = true;
                    $output.='<script type="text/javascript"> //<![CDATA[

                    jQuery(document).ready(function(){

                        jQuery.extend(jQuery.gritter.options, {
                                position: "bottom-right",
                                fade_in_speed: "medium"
                        });
                    ';
                }
                $alerts = wpscGetAlerts($userid);
                foreach ($alerts as $alert) {
                    if($isWpAdmin) {
                        $curAlerts = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_alerts` WHERE `primkey`='{$alert['foreignkey']}' AND `adminpanel`='1' ;", ARRAY_A);
                    } else {
                        $curAlerts = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_alerts` WHERE `primkey`='{$alert['foreignkey']}';", ARRAY_A);
                    }
                    if(isset($curAlerts[0])) {
                        foreach($curAlerts as $curAlert) {
                            $atLeastOne = true;
                            if($display=='gritter') {
                                $output .= "
                                    jQuery.gritter.add({
                                            after_close: function(){
                                                    jQuery.ajax({type:'POST', url: '".plugins_url()."/wpstorecart/wpstorecart/alerts/clearalerts.php', data:{ alert_id: '{$curAlert['primkey']}'}});
                                            },                                
                                            title: '{$curAlert['title']}',
                                            sticky: true,
                                            ";
                                            if($images) {
                                                $output .= "image: '".plugins_url()."/wpstorecart/images/alerts/{$curAlert['image']}',
                                                ";
                                            }                                            
                                            $output .= "text: '{$curAlert['status']}'
                                    });                                   
                                    ";
                            } else {
                            
                                if($display==NULL) {$output.='<p>';}                            
                                if($display=='ul' || $display=='ol') {$output.='<li>';}
                                if($display=='table') {$output.='<tr id="wpsc_alert_row_'.$curAlert['primkey'].'"><td>';}      
                                if($display=='desktop') {$output.='<li style="clear:both;">';}
                                if(trim($curAlert['url'])!='' && $display!='plain' && $display!='desktop' && $display!='table') {
                                    $output .= '<a href="'.$curAlert['url'].'">';
                                }
                                if($images) {
                                    $output .= '<img src="'.plugins_url().'/wpstorecart/images/alerts/'.$curAlert['image'].'" alt=""';
                                    if($display=='desktop') {
                                        $output .= ' style="float:left;width:32px;height:32px;max-width:32px;max-height:32px;border:none;" '; 
                                    }
                                    $output.=' /> ';
                                }
                                if($display=='table') {$output.='</td><td>';} 
                                $output .= $curAlert['status'];
                                if(trim($curAlert['url'])!=''  && $display!='plain' && $display!='desktop' && $display!='table') {
                                    $output .= '</a>';
                                }                         
                                
                                if($display=='ul' || $display=='ol' || $display=='desktop') {$output.='</li>';}
                                if($display=='table') {$output.=' <img src="'.plugins_url().'/wpstorecart/images/cross.png" alt="" style="cursor:pointer;" onclick="jQuery.ajax({type:\'POST\', url: \''.plugins_url().'/wpstorecart/wpstorecart/alerts/clearalerts.php\', data:{ alert_id: \''.$curAlert['primkey'].'\'}}); jQuery(\'#wpsc_alert_row_'.$curAlert['primkey'].'\').hide(); " /></td></tr>';} 
                                if($display==NULL) {$output.='</p>';}   
                                if($display=='plain') {$output.="
";}   
                                
                            }
                            
                            $alertCount++;
                            
                        }
                    }  
                }
                if(!isset($alerts[0]) || !$atLeastOne) { // If there are no alerts, lets also communicate that fact
                    if($display!='gritter') { // gritter should not report no alerts
                        if($display==NULL) {$output.='<p>';}                            
                        if($display=='ul' || $display=='ol') {$output.='<li>';}
                        if($display=='table') {$output.='<tr><td>';} 

                        if($images) {
                            $output .= '<img src="'.plugins_url().'/wpstorecart/images/alerts/Help.png" alt=""';
                            if($display=='desktop') {
                               $output .= ' style="width:32px;height:32px;max-width:32px;max-height:32px;" '; 
                            }
                            $output.=' /> ';
                        }
                        if($display=='table') {$output.='</td><td>';} 
                        $output .= __('No alerts to report at this time.','wpstorecart');

                        if($display=='table') {$output.='</td></tr>';} 
                        if($display=='ul' || $display=='ol') {$output.='</li>';}
                        if($display==NULL) {$output.='</p>';}   
                    }
                }
                if($display=='gritter') {$output.='}); //]]> </script>';}
                if($display=='table') {$output.='</table>';}
		if($display=='ol') {$output.='</ol>';}
		if($display=='ul') {$output.='</ul>';}
                if($display=='desktop') {$output.='</ul>';}
                if($display==NULL) {$output.='</div>';}
                if($display=='number') { $output = $alertCount ;} // When seeking the number of alerts
		
		return $output;
	}
}

if(!function_exists('wpscAlertsDashboardWidget')) {
    function wpscAlertsDashboardWidget() {
        global $current_user;
        wp_get_current_user();
        echo '
            <style type="text/css">
                .wpsc_alerts img {max-width:48px;max-height:48px;}
                .wpsc_alerts {font-size:18px;}
            </style>
            ';
        echo wpscDisplayAlerts($current_user->ID, 'dashboard');
    }
}

if(!function_exists('wpscGetAlerts')) {
    /**
     * Grabs alerts that are current active and triggered for the specified user
     * 
     * @global type $wpdb
     * @global type $current_user
     * @param type $userid
     * @return type 
     */
    function wpscGetAlerts($userid=NULL) {
        global $wpdb, $current_user;
        if($userid==NULL) { // If no user ID was specified, then try to grab the current user
            wp_get_current_user();
            $userid = $current_user->ID;
        }
        $output = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_log` WHERE `userid`='{$userid}' AND `action`='alert';", ARRAY_A);
        return $output;
    }
}

if(!function_exists('wpscGetAlertWatchlist')) {
    /**
     * Grabs the watchlist of Alerts
     * @global type $wpdb
     * @global type $current_user
     * @param type $userid
     * @return type 
     */
    function wpscGetAlertWatchlist($userid=NULL) {
        global $wpdb, $current_user;
        if($userid==NULL) { // If no user ID was specified, then try to grab the current user
            wp_get_current_user();
            $userid = $current_user->ID;
        }
        $output = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_alerts` WHERE `userid`='{$userid}' OR `userid`=0; ", ARRAY_A);
        return $output;
    }
}

if(!function_exists('wpscSendTextAlert')) {
    function wpscSendTextAlert($userid=NULL, $message=NULL) {
        $wpsc_full_alert_email = get_user_meta($userid, 'wpsc_full_alert_email', true);
        if($wpsc_full_alert_email!='') {
            wpscTextMessage($wpsc_full_alert_email, $message);
        }
    }
}

if(!function_exists('wpscScanForAlerts')) {
    /**
     * Scans for alerts that need to be triggered.
     * @param integer $userid the user ID. If no user ID was specified, then try to grab the current user
     */
    function wpscScanForAlerts($userid=NULL) {
        global $wpdb;

            $theList = wpscGetAlertWatchlist($userid);
            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

            if(isset($theList[0]['conditions'])) {
                foreach ($theList as $theAlert) {
                    $test = $wpdb->get_results("SELECT `primkey`, `action`, `time` FROM `{$wpdb->prefix}wpstorecart_log` WHERE (`action`='alert' OR `action`='alert_delayed') AND `userid`='{$userid}' AND `foreignkey`='{$theAlert['primkey']}'", ARRAY_A);
                    //$test = $wpdb->get_results("SELECT `primkey` FROM `{$wpdb->prefix}wpstorecart_log` WHERE `userid`='{$userid}' AND `action`='alert' AND `foreignkey`='{$theAlert['primkey']}'; ", ARRAY_A); // Test to see if we already created an alert
                    if(wpscAlertConditionParser($theAlert['conditions'], $userid)) { // If the alert is triggered:
                        if(@!isset($test[0]['primkey'])) { // If we haven't created an alert, then do so now:
                            $wpdb->query("INSERT INTO `{$wpdb->prefix}wpstorecart_log` (`primkey`, `action`, `data`, `foreignkey`, `date`, `userid`, `time`) VALUES (NULL, 'alert', '', '{$theAlert['primkey']}', '".date('Ymd')."', '{$userid}', '".date( 'Y-m-d H:i:s')."');");
                            if($theAlert['textmessage']==1 && $userid!=NULL) { // If we need to send a text message, do it here
                                wpscSendTextAlert($userid, $theAlert['status']);
                            }
                            if($theAlert['emailalert']==1 && $userid!=NULL) { // If we need to send an email alert, do it here
                                $the_users_data = get_userdata($userid);
                                wpscEmail($the_users_data->user_email , $theAlert['title'], $theAlert['status']);
                            }                        
                        }
                        if(@$test[0]['action']=='alert_delayed') { // If we had a delayed alert, lets check to see if the delay period has been exceeded
                            $timeToCheck = strtotime($test[0]['time']);
                            $baseTime = strtotime('+ '.$wpStoreCartOptions['alert_clear_period'], $timeToCheck);
                            $currentTime = strtotime('now');

                            if(@$baseTime <= $currentTime) { // If our delay period has expired, let's re-alert regarding this
                                $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_log` SET `action`='alert' WHERE `primkey`='{$test[0]['primkey']}' ;");
                                if($theAlert['textmessage']==1 && $userid!=NULL) { // If we need to send a text message, do it here
                                    wpscSendTextAlert($userid, $theAlert['status']);
                                }        
                                if($theAlert['emailalert']==1 && $userid!=NULL) { // If we need to send an email alert, do it here
                                    $the_users_data = get_userdata($userid);
                                    wpscEmail($the_users_data->user_email , $theAlert['title'], $theAlert['status']);
                                }                             
                            }
                        }
                    } else { // The condition failed, and there should be no alert for this
                        if(isset($test[0]['primkey'])) { // There's an alert for this that needs to be cleared
                            $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_log` SET `action`='alert_cleared' WHERE `primkey`='{$test[0]['primkey']}' ;");
                        }
                    }
                }
            }
     
        }
        
    
}

if(!function_exists('wpscScanForAlertsAllUsers')) {
    /**
     * Scans all active alerts users and updates alerts as necessary.  This is the best function to call to insure alerts refresh.
     */
    function wpscScanForAlertsAllUsers() {
        global $wpdb;
        $scans = $wpdb->get_results("SELECT COUNT( * ) AS `Rows` , `userid` FROM `{$wpdb->prefix}wpstorecart_alerts` GROUP BY `userid` ORDER BY `userid`;", ARRAY_A); // Grab the user IDs of users with active alerts
        if(isset($scans[0])) {
           foreach($scans as $scan) { 
                wpscScanForAlerts($scan['userid']);
           }
        }
    }
}

if(!function_exists('wpscTimestampToMySQL')) {
    function wpscTimestampToMySQL($timestamp) {
        return date('Y-m-d H:i:s', $timestamp);
    }
}

if(!function_exists('wpscAlertConditionParser')) {
    /**
     *
     * @param string $code Parses code according to the Alerts API scripting language. 
     * @return int Returns false if the parser determined the alert has NOT been triggered, otherwise it returns true
     */
    function wpscAlertConditionParser($code, $userid=NULL) {
        global $wpdb;    
        $codebyline = explode(';', $code); // Breaks down the code into line by line

        // Initialize arrays
        $cmd = array();$op = array();$value = array();
        // Set all of our main variables to NULL
        $cmd[0] = NULL;$cmd[1] = NULL;$cmd[2] = NULL;
        $op[0] = NULL;$op[1]=NULL;
        $value[0] = NULL;$value[1] = NULL;$value[2] = NULL;
        
        $return_value_0 = false; // Default return value
        $return_value_final = false; // Default return value        
        
        $codelinenumber = 0;
        $return_value_cln = array();
        foreach($codebyline as $codeline) { // Now we'll look at each line of code and execute it
            if(trim($codeline)!='') { // If we're not dealing with a blank command, then continue 

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
                $commands = preg_split( "/ (@|=|>|<|!) /", $codeline );
                
                
                $icounter = 0;
                foreach ($commands as $command) {
                    //echo '['.$icounter.'] '; 
                    if(trim($command)!='') { // If we have a real command (not a blank one)
                        //echo trim($command).'('.$value[$icounter].') ';
                        $cmd[$icounter] = trim($command);
                    
                        if($icounter==0) { // If we're on the first command, then we need to figure out the operator
                            if($operator['equal']) {
                                //echo '= ';
                                $op[0] = '=';
                            }
                            if($operator['lessthan']) {
                                //echo '< ';
                                $op[0] = '<';
                            }
                            if($operator['greaterthan']) {
                                //echo '> ';
                                $op[0] = '>';
                            }
                            if($operator['notequal']) {
                                //echo '! ';
                                $op[0] = '!';
                            }  
                            if($operator['time']) {
                                if(!$operator['equal'] && !$operator['lessthan'] && !$operator['greaterthan'] && !$operator['notequal']) {
                                    //echo '@ ';
                                    $op[0] = '@';
                                }
                            }                     
                        } 
                        if($icounter==1) { //and the only operator available now is time.
                            if($operator['time']) {
                                if($operator['equal'] || $operator['lessthan'] || $operator['greaterthan'] || $operator['notequal']) {
                                    //echo '@ ';
                                    $op[1] = '@';
                                }
                            }
                        }
                    }
                    $icounter++;
                }
                
                /**         
                            echo '$cmd[0] : '.$cmd[0] .'
                ';
                            echo '$value[0] : '.$value[0] .'
                ';            
                            echo '$op[0] : '.$op[0] .'
                ';
                            echo '$cmd[1] : '.$cmd[1] .'
                ';
                            echo '$value[1] : '.$value[1] .'
                ';            
                            echo '$op[1] : '.$op[1] .'
                ';             
                            echo '$cmd[2] : '.$cmd[2] .'
                ';
                            echo '$value[2] : '.$value[2] .'
                ';         
                // $cmd[0]($value[0]) $op[0] $cmd[1]($value[1]) $op[1] $cmd[2]($value[2]);
                // newticket(0) = true() @ hours(72);
                */        


                // Time based commands
                $commands_time = false; // All time commands are converted to the single time command.  If it remains false, no time period is specified (other than since the last time the alert was cleared.)
                $timesql = NULL; // Time SQL modifier 

                if(isset($cmd[2])) {
                    $commands_time = wpscTimestampToMySQL(strtotime("-{$value[2]} {$cmd[2]}")); // All time commands are converted to the single time command.  If it remains false, no time period is specified (other than since the last time the alert was cleared.)
                }

                if($op[0]=='@') { // If the first operator is a time operator, lets assume the rest is good to go:
                    $commands_time = wpscTimestampToMySQL(strtotime("-{$value[1]} {$cmd[1]}")); // All time commands are converted to the single time command.  If it remains false, no time period is specified (other than since the last time the alert was cleared.)
                }

                if($commands_time!=false) {
                    $timesql = " AND `time` > '{$commands_time}' ";
                }



                switch ($cmd[0]) { // Time to interpret the first command
                    case 'newsales': // Boolean return value
                        
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
                        
                        // Compares the historic data with the current data, and if there are more sales since the historic data was last updated, return as true and update the historic records to the new number
                        if($current_completed_orders > $historic_record_of_completed_orders) {
                                $return_value_0 = true;
                                $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_meta` SET `value`='{$current_completed_orders}' WHERE `type`='totalsalesamount' AND `foreignkey`='{$userid}';");
                        }  
                        
                    break;
                    case 'newviews':

                    break;
                    case 'newaddtocart':

                    break;
                    case 'newshipping':

                    break; 
                    /** @todo
                    case 'newcheckout':

                    break;
                    case 'newticket':

                    break;       

                    case 'shipping':

                    break;       
                    * 
                    */
                    case 'nosales':
                        $workingset = $wpdb->get_results("SELECT COUNT(*) AS 'current_amount' FROM `{$wpdb->prefix}wpstorecart_log` WHERE `action`='purchase' {$timesql};", ARRAY_A);
                        if(!isset($workingset[0]['current_amount']) || $workingset[0]['current_amount']==0) {
                             if( (@isset($cmd[2]) && @isset($value[2])) || @$op[0]=='@') { // looking for time related commands
                                 
                                $dateCheckTime = null;
                                
                                if($op[0]=='@') { // find the time command and get the correct
                                    $timeToCheck = strtotime("-{$value[1]} {$cmd[1]}");
                                    $dateCheckTime = " AND `date` >= '".date('Ymd', $timeToCheck)."' ";
                                } else {
                                    $timeToCheck = strtotime("-{$value[2]} {$cmd[2]}");
                                    $dateCheckTime = " AND `date` >= '".date('Ymd', $timeToCheck)."' ";
                                }
                                
                                $check_manual_orders = $wpdb->get_results("SELECT COUNT(*) AS 'current_amount' FROM `{$wpdb->prefix}wpstorecart_orders` WHERE `orderstatus`='Completed' {$dateCheckTime};", ARRAY_A);
                                if(!isset($check_manual_orders[0]['current_amount']) || $check_manual_orders[0]['current_amount']==0) {
                                    // If none were found, report as true!
                                    $return_value_0 = true;
                                }                                
                             } else {
                                $return_value_0 = true;
                             }
                        }                
                    break;           
                    case 'noviews':
                        $workingset = $wpdb->get_results("SELECT COUNT(*) AS 'current_amount' FROM `{$wpdb->prefix}wpstorecart_log` WHERE `action`='productview' {$timesql};", ARRAY_A);
                        if(!isset($workingset[0]['current_amount']) || $workingset[0]['current_amount']==0) {
                             // If none were found, report as true!
                                $return_value_0 = true;
                        }               
                    break;  
                    case 'noaddtocart':
                        $workingset = $wpdb->get_results("SELECT COUNT(*) AS 'current_amount' FROM `{$wpdb->prefix}wpstorecart_log` WHERE `action`='addtocart' {$timesql};", ARRAY_A);
                        if(!isset($workingset[0]['current_amount']) || $workingset[0]['current_amount']==0) {
                             // If none were found, report as true!
                                $return_value_0 = true;
                        }                 
                    break;  
                    /** @todo 
                    case 'nocheckout':
                        if(isset($workingset[0]['current_amount'])) {
                            if($workingset[0]['current_amount']==0) { // If no checkout actions were found, report nocheckout as true!
                                $return_value = true;
                            }
                        }                 
                    break;
                    * 
                    */           
                    case 'sales': // Returns false if no sale was ever made, otherwise returns all sales, or if a time modifier was used it returns all sales during the specified time period
                        $workingset = $wpdb->get_results("SELECT COUNT(`primkey`) AS `current_amount` FROM `{$wpdb->prefix}wpstorecart_orders` WHERE `orderstatus`='Completed' {$timesql};", ARRAY_A);
                        if(isset($workingset[0]['current_amount'])) {
                            $return_value_0 = $workingset[0]['current_amount'];
                        }
                    break;     
                    case 'views': // Returns false if no product views were ever made, otherwise returns all product views, or if a time modifier was used it returns all product views during the specified time period
                        $workingset = $wpdb->get_results("SELECT COUNT(*) AS 'current_amount' FROM `{$wpdb->prefix}wpstorecart_log` WHERE `action`='productview' {$timesql};", ARRAY_A);
                        if(isset($workingset[0]['current_amount'])) {
                            $return_value_0 = $workingset[0]['current_amount'];
                        }                
                    break;     
                    case 'addtocart': // Returns false if no add to cart actions were ever made, otherwise returns all add to cart actions, or if a time modifier was used it returns all add to cart actions during the specified time period
                        $workingset = $wpdb->get_results("SELECT COUNT(*) AS 'current_amount' FROM `{$wpdb->prefix}wpstorecart_log` WHERE `action`='addtocart' {$timesql};", ARRAY_A);
                        if(isset($workingset[0]['current_amount'])) {
                            $return_value_0 = $workingset[0]['current_amount'];
                        }                
                    break;             
                }


                // Command 2
                if($cmd[1]=='true' || $cmd[1]=='false') {
                    if($cmd[1]=='true') { 
                        if($return_value_0!=false && $op[0] == '=') {
                            $return_value_final = true;
                        }
                        if($return_value_0==false && $op[0] == '!') {
                            $return_value_final = true;
                        }                        
                    }
                    if($cmd[1]=='false') { 
                        if($return_value_0==false && $op[0] == '=') { // If we were looking for an item to equal false, then we return true here (confusing, huh?)
                            $return_value_final = true; 
                        }
                        if($return_value_0!=false && $op[0] == '!') { // If we're looking for not equal false, then we return true here.
                            $return_value_final = true; 
                        }                        
                    }    
                } else { // We're dealing with a numeric value then
                    if($op[0] == '=') {
                        if($cmd[1] == $return_value_0) {
                            $return_value_final = true;
                        }
                    }
                    if($op[0] == '!') {
                        if($cmd[1] != $return_value_0) {
                            $return_value_final = true;
                        }
                    }   
                    if($op[0] == '>') {
                        if($cmd[1] > $return_value_0) {
                            $return_value_final = true;
                        }
                    }  
                    if($op[0] == '<') {
                        if($cmd[1] > $return_value_0) {
                            $return_value_final = true;
                        }
                    }  
                    if($op[0]=='@') {            
                        $return_value_final = $return_value_0;
                    }
                }                
            
            $return_value_cln[$codelinenumber] = $return_value_final;    
            $codelinenumber++;
            }           
            
        }
        
        // The multiple commands are processed here
        $overall_result = true; // Assume true
        if(!isset($return_value_cln[0])) { // But if no commands were processed, it is false
            $overall_result = false;
        }
        foreach($return_value_cln as $examineCommand) { // Now if any of the commands return false, the entire group of statements turns false
            if($examineCommand==false) {
                $overall_result = false;
            }
        }
        
        return $overall_result;
        
    }
}


wpscScanForAlertsAllUsers();  

?>