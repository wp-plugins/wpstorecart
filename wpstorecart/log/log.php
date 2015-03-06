<?php


if(!function_exists('wpscLog')) {
    /**
     *
     * Note: Logging will fail on the very first initialization when a user upgrades from wpStoreCart 2.x, to 3.x.  It is crucial that this error be handled gracefully without triggering issues with activation.
     * 
     * @param integer $primkey
     * @param string $action
     * @param string $data
     * @param integer $foreignkey
     * @param integer $date
     * @param integer $userid
     * @param type $time 
     */
    function wpscLog($primkey=NULL, $action='', $data='', $foreignkey=0, $date=NULL, $userid=NULL, $time=NULL) {
        global $wpdb;
        if($time==NULL) {
            $time = 'CURRENT_TIMESTAMP'; // If updating with the current timestamp
        } else {
            $time = "'".esc_sql($time)."'"; // If updating from the supplied parameter
        }
        if($date==NULL) {
           $date = date('Ymd') ;  
        }
        if($userid==NULL) {
            global $current_user;
            wp_get_current_user();
            if ( 0 == $current_user->ID ) {
                // Not logged in.
                $userid = 0;
            } else {
                $userid = $current_user->ID;
            }                 
        }
        if($primkey==NULL) { // If no primkey is specified we create a new log entry
            try {
                @$wpdb->query("INSERT INTO `{$wpdb->prefix}wpstorecart_log`  (`primkey`, `action`, `data`, `foreignkey`, `date`, `userid`, `time`) VALUES (NULL, '".esc_sql($action)."', '".esc_sql($data)."', '".intval($foreignkey)."', '".esc_sql($date)."', '".intval($userid)."', {$time});");
            } catch (Exception $e) {
                return false;
            }            
        } else {
            try {
                @$wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_log` SET `action` = '".esc_sql($action)."', `data` = '".esc_sql($data)."', `foreignkey` = '".intval($foreignkey)."', `date` = '".esc_sql($date)."', `userid` = '".intval($userid)."', `time` = {$time} WHERE `primkey` = ".intval($primkey).";");
            } catch (Exception $e) {
                return false;
            }                        
        }
    }
}


?>