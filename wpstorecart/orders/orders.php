<?php

if(!function_exists('wpscSplitOrderIntoProductKeys')) {
    /**
     * 
     * 
     * @global object $wpdb
     * @param integer $keyToLookup
     * @return array Returns an array of product IDs from 
     */
    function wpscSplitOrderIntoProductKeys($keyToLookup) {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpstorecart_orders";
        $table_name2 = $wpdb->prefix . "wpstorecart_products";

        
        $return_array = array();
        
        $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name}` WHERE `primkey`={$keyToLookup};";
        $results = $wpdb->get_results( $sql , ARRAY_A );
        if(isset($results)) {
            $specific_items = explode(",", $results[0]['cartcontents']);
            foreach($specific_items as $specific_item) {
                if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                    $current_item = explode('*', $specific_item);

                    if(isset($current_item[0]) && isset($current_item[1])) {
                                    
                        $sql2 = "SELECT `primkey` FROM `{$table_name2}` WHERE `primkey`={$current_item[0]};";
                        $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
                        if(isset($moreresults[0])) {
                            
                            $current_value = 1;
                            while($current_value <= $current_item[1]) { // 
                                $return_array[] = $current_item[0];
                                $current_value++;
                            }
                        }
                    }
                }
            }
        } 
        
        return $return_array;
    }
}

if(!function_exists('wpscSplitOrderIntoProduct')) {
    /**
        *
        * Returns a string containing the specific products associated with the order specified with $keyToLookup 
        * 
        * @global object $wpdb
        * @param integer $keyToLookup
        * @param string $type
        * @return string 
        */
    function wpscSplitOrderIntoProduct($keyToLookup, $type="default") {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpstorecart_orders";
        $table_name2 = $wpdb->prefix . "wpstorecart_products";

        $output = NULL;
        $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name}` WHERE `primkey`={$keyToLookup};";
        $results = $wpdb->get_results( $sql , ARRAY_A );
        if(isset($results)) {
            $specific_items = explode(",", $results[0]['cartcontents']);
            foreach($specific_items as $specific_item) {
                if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                    $current_item = explode('*', $specific_item);

                    if(isset($current_item[0]) && isset($current_item[1])) {

                        $sql2 = "SELECT `primkey`, `name`, `download`, `postid`, `producttype` FROM `{$table_name2}` WHERE `primkey`={$current_item[0]};";
                        $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
                        if($moreresults[0]['producttype']=='variation' || $moreresults[0]['producttype']=='attribute') {
                            $sql3 = "SELECT `name` FROM `{$table_name2}` WHERE `primkey`={$moreresults[0]['postid']};";
                            $evenmoreresults = $wpdb->get_results( $sql3 , ARRAY_A );    
                            if(isset($evenmoreresults[0]['name'])) {
                                $theFinalName = $evenmoreresults[0]['name'] . ' - ' . $moreresults[0]['name'];
                            } else {
                                $theFinalName = $moreresults[0]['name'];
                            }
                        } else {
                            $theFinalName = $moreresults[0]['name'];
                        }                        
                        
                        if($type=="default" && isset($moreresults[0])) {
                            $output .= ', ';
                            if($output==', ') {$output = '';}
                            $output .= $theFinalName;
                        }
                        if($type=="download" && isset($moreresults[0])) {
                            $output .= ', <br />';
                            if($output==', <br />') {$output = '';}
                            if($moreresults[0]['download']=='' || $results[0]['orderstatus']!='Completed') { // Non-downloads products below:
                                $output .= $theFinalName;
                            } else { // Download products below:

                                $multidownloads = explode('||', $moreresults[0]['download']);
                                if(@isset($multidownloads[0]) && @isset($multidownloads[1])) {
                                    $downloadcount = 0;
                                    foreach($multidownloads as $multidownload) {
                                        if($multidownload!='') {
                                            $output .= '<a href="'.plugins_url().'/wpstorecart/wpstorecart/download/download.php?file='.$moreresults[0]['primkey'].'&part='.$downloadcount.'"><img src="'.plugins_url().'/wpstorecart/images/disk.png">'.$theFinalName.' '.$thevariationdetail[0].' '.$thevariationdetail[1].' #'.$downloadcount.'</a><br />';
                                        }
                                            $downloadcount++;
                                    }
                                } else {
                                    $output .= '<a href="'.plugins_url().'/wpstorecart/wpstorecart/download/download.php?file='.$moreresults[0]['primkey'].'"><img src="'.plugins_url().'/wpstorecart/images/disk.png">'.$theFinalName.'</a>';
                                }
                            }
                            if($current_item[1]!=1) {
                                $output .= ' (x'.$current_item[1].')';
                            }
                        }
                        if($type=="edit" && isset($moreresults[0])) {
                           
                            $output .= '<div id="delIcon'.$current_item[0].$keyToLookup.'"><a href="#" onclick="wpscDeleteItemInCart('.$current_item[0].', '.$keyToLookup.', '.$current_item[1].');return false;"><img src="'.plugins_url().'/wpstorecart/images/cross.png"></a>'.$theFinalName;
                            if($current_item[1]!=1) {
                                $output .= '(x'.$current_item[1].')';
                            }
                            $output .= '<br /></div>';
                        }
                    }
                }
            }
        }
        return $output;
    }
}




if(!function_exists('wpscAssignSerialNumber')) {
            /**
            *  Assign a serial number to an order and email them the serial number
            *
            * @global object $wpdb
            * @param integer $productid The product that you are pulling the serial number from
            * @param integer $orderid The order that has the serial number associated with it
            */
    function wpscAssignSerialNumber($productid, $orderid=0) {
        global $wpdb, $wpstorecart_version;
        $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
        $table_name2 = $wpdb->prefix . "wpstorecart_products";
        $table_name = $wpdb->prefix . "wpstorecart_orders";

        $wpStoreCartOptions = $this->getAdminOptions();

        // Grabs the serial numbers
        $results_serial_numbers = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='serialnumbers' AND `foreignkey`={$productid};", ARRAY_N);
        $results_serial_numbers_used = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='serialnumbersused' AND `foreignkey`={$productid};", ARRAY_N);
        if($results_serial_numbers!=false ) {
            $wpStoreCartproduct_serial_numbers = base64_decode($results_serial_numbers[0][0]);
            if($results_serial_numbers!=false ) {
                $wpStoreCartproduct_serial_numbers_used  = base64_decode($results_serial_numbers_used[0][0]);
            } else {
                $wpStoreCartproduct_serial_numbers_used  = '';
            }
            $grab_one = explode("\n",$wpStoreCartproduct_serial_numbers);
            $wpStoreCartproduct_serial_numbers_used = $grab_one[0]."\n".$wpStoreCartproduct_serial_numbers_used;
            $wpStoreCartproduct_serial_numbers = str_replace($grab_one[0]."\n", "", $wpStoreCartproduct_serial_numbers);
            $results111 = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".base64_encode($grab_one[0])."', 'serialnumberassigned', '{$orderid}');");
            $results222 = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = '".base64_encode($wpStoreCartproduct_serial_numbers)."' WHERE `type`='serialnumbers' AND `foreignkey` = {$productid};");
            if($results_serial_numbers!=false ) {
                $results333 = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = '".base64_encode($wpStoreCartproduct_serial_numbers_used)."' WHERE `type`='serialnumbersused' AND `foreignkey` = {$productid};");
            } else {
                $results333 = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".base64_encode($wpStoreCartproduct_serial_numbers_used)."', 'serialnumberassigned', '{$productid}');");
            }
            if($results111 && $results222 && $results333 && $orderid!=0) {
                // Do the email here
                $sql2 = "SELECT `name` FROM `{$table_name2}` WHERE `primkey`={$productid};";
                $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
                $theProductsName = $wpStoreCartOptions['single_item'];
                if(isset($moreresults) && $moreresults[0]['name']!='') {
                        $theProductsName = $moreresults[0]['name'];
                }
                $theEmail = $wpStoreCartOptions['emailserialnumber'];
                $theEmail = str_replace("[productname]", $theProductsName, $theEmail);
                $theEmail = str_replace("[serialnumber]", $grab_one[0], $theEmail);
                $message = wpscMakeEmailTxt($theEmail, $orderid);

                $headers = 'From: '.$wpStoreCartOptions['wpStoreCartEmail'] . "\r\n" .
                    'Reply-To: ' .$wpStoreCartOptions['wpStoreCartEmail']. "\r\n" .
                    'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;

                // Send an email when purchase is submitted
                $sql = "SELECT `email` FROM `{$table_name}` WHERE `primkey`={$orderid};";
                $results = $wpdb->get_results( $sql , ARRAY_A );
                if(isset($results)) {
                    wpscEmail($results[0]['email'], __('The serial number for your recent purchase','wpstorecart'), $message, $headers);
                }
            }
        }

    }
}

if(!function_exists('wpscHasPurchased')) {
    function wpscHasPurchased($primkey, $email=NULL) {
        global $wpdb, $current_user;
        
        $email = esc_sql($email);
        $haspurchased = false;
	
        wp_get_current_user();

        if($email==NULL) { 
            if ( 0 == $current_user->ID ) {

            } else {
                $table_name99 = $wpdb->prefix . "wpstorecart_orders";
                $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name99}` WHERE `wpuser`={$current_user->ID};";
                $results = $wpdb->get_results( $sql , ARRAY_A );
                if(isset($results)) {
                    foreach($results as $result) {
                        $specific_items = explode(",", $result['cartcontents']);
                        foreach($specific_items as $specific_item) {
                            if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                                $current_item = explode('*', $specific_item);
                                if(isset($current_item[0]) && $current_item[0]==$primkey && $result['orderstatus']=='Completed') {
                                        $haspurchased = true;
                                }
                            }
                        }
                    }
                }
            }   
        } else { // Here we look up an order by email only!
            $table_name99 = $wpdb->prefix . "wpstorecart_orders";
            $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name99}` WHERE `email`='{$email}';";
            $results = $wpdb->get_results( $sql , ARRAY_A );
            if(isset($results)) {
                foreach($results as $result) {
                    $specific_items = explode(",", $result['cartcontents']);
                    foreach($specific_items as $specific_item) {
                        if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                            $current_item = explode('*', $specific_item);
                            if(isset($current_item[0]) && $current_item[0]==$primkey && $result['orderstatus']=='Completed') {
                                    $haspurchased = true;
                            }
                        }
                    }
                }
            }                        
        }
        return $haspurchased;
    }
}

?>