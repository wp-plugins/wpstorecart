<?php


if(!function_exists('wpscEmail')) {
    /**
     *
     * Wrapper for wp_mail Wordpress function
     * 
     * @global type $wpstorecart_settings
     * @param type $to
     * @param type $subject
     * @param type $message
     * @param string $headers
     * @param type $attachments
     * @param type $contenttype
     * @return type 
     */
    function wpscEmail($to, $subject, $message, $headers = NULL, $attachments=NULL, $contenttype=NULL) {

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        
        if($contenttype==NULL) {
            add_filter('wp_mail_content_type',create_function('', 'return "text/html";'));
        } else {
            add_filter('wp_mail_content_type',create_function('', 'return "'.$contenttype.'";'));
        }
        if($headers==NULL && !headers_sent()) {
            $site_name = preg_replace("/[^a-zA-Z0-9\s]/", "", get_bloginfo('name'));
            $headers = 'From: '.$site_name.' <'.$wpStoreCartOptions['wpStoreCartEmail'].'>' . "\r\n".
                    'Reply-To: '.$wpStoreCartOptions['wpStoreCartEmail'] . "\r\n" .
                    'X-Mailer: PHP/' . phpversion();
            
        }
        if($attachments==NULL) {
            return wp_mail($to, $subject, $message, $headers);
        } else { 
            return wp_mail($to, $subject, $message, $headers, $attachments);        
        }
    }
}


if(!function_exists('wpscMakeEmailTxt')) {
    /**
     *
     * @global type $current_user
     * @global type $wpdb
     * @param type $theEmail
     * @param type $theEmailAddressOrderID
     * @return type 
     */
    function wpscMakeEmailTxt($theEmail, $theEmailAddressOrderID = 0) {
        global $current_user, $wpdb;
        get_currentuserinfo();

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        if($theEmailAddressOrderID == 0) {
            if ( 0 == $current_user->ID ) { // Guest
                $theEmail = str_replace("[customername]", __('valued customer', 'wpstorecart'), $theEmail);
            } else {
                $theEmail = str_replace("[customername]", $current_user->display_name, $theEmail);
            }
        } else {
            $table_name = $wpdb->prefix . "wpstorecart_orders";
            $sql = "SELECT `email` FROM `{$table_name}` WHERE `primkey`={$theEmailAddressOrderID};";
            $results = $wpdb->get_results( $sql , ARRAY_A );
            $theEmailAddress = __('valued customer', 'wpstorecart');
            if(isset($results)) {
                $theEmailAddress = $results[0]['email'];
            }
            $theEmail = str_replace("[customername]", $theEmailAddress, $theEmail);
        }
        $theEmail = str_replace("[sitename]", get_bloginfo(), $theEmail);
        if(trim($wpStoreCartOptions['orderspage'])!='') {
            $theEmail = str_replace("[downloadurl]", get_permalink($wpStoreCartOptions['orderspage']), $theEmail);
        } else {
            if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=orders';
            } else {
                $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=orders';
            }
            $theEmail = str_replace("[downloadurl]", $permalink, $theEmail);
        }

        return $theEmail;
    }
}

if(!function_exists('wpscSendSuccessfulPurchaseEmail')) {
    function wpscSendSuccessfulPurchaseEmail($wpscEmail=NULL) {
        global $current_user, $wpdb, $wpstorecart_version;
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        
            // Let's send them an email telling them their purchase was successful
            // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap(wpscMakeEmailTxt($wpStoreCartOptions['emailonapproval']) . wpscMakeEmailTxt($wpStoreCartOptions['emailsig']), 70);

        $headers = 'From: '.$wpStoreCartOptions['wpStoreCartEmail'] . "\r\n" .
            'Reply-To: ' .$wpStoreCartOptions['wpStoreCartEmail']. "\r\n" .
            'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;


        // Send an email when purchase is submitted

        if($current_user->ID != 0) {
            wpscEmail($current_user->user_email, __('Your order has been fulfilled!','wpstorecart'), $message, $headers);
        } else {
            // Send an email when purchase is submitted
            if(isset($wpscEmail)) {
                wpscEmail($wpscEmail, 'Your order has been fulfilled!', $message, $headers);
            } else {                
                if(@isset($_SESSION['wpsc_email'])) {
                    wpscEmail(esc_sql($_SESSION['wpsc_email']), __('Your order has been fulfilled!','wpstorecart'), $message, $headers);
                } elseif(@isset($_POST['payer_email'])) {
                    wpscEmail(esc_sql($_POST['payer_email']), __('Your order has been fulfilled!','wpstorecart'), $message, $headers);
                }
            }
        }
    }
}

if(!function_exists('wpscTextMessage')) {
    function wpscTextMessage($emailtotext, $message) {

        wpscEmail($emailtotext, '', $message);

    }
}

?>