<?php

if(!function_exists('wpscProductGetShippingServicesAvailable')) {
    /**
     * 
     * 
     * @global object $wpdb
     * @param integer $product_id The primary key of the product to lookup
     * @return array
     */
    function wpscProductGetShippingServicesAvailable($product_id) {
        global $wpdb;
        $return_value = null;
        $results = $wpdb->get_results("SELECT `shippingservices` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`={$product_id};", ARRAY_A);
        if(@isset($results[0]['shippingservices'])) {
            $return_value = array();
            if(strpos($results[0]['shippingservices'], '||') !== false ) {
                $return_value = explode('||', $results[0]['shippingservices']);
            } else {
                $return_value[0] = $results[0]['shippingservices'];
            }
        }
        return $return_value;
    }
}

if(!function_exists('wpscProductIsShippingServiceAvailable')) {
    /**
     *
     * @param integer $product_id The primary key of the product to lookup
     * @param string $shipping_service The unique identifying string of the shipping service
     * @return boolean 
     */
    function wpscProductIsShippingServiceAvailable($product_id, $shipping_service) {
        $getallshipping = wpscProductGetShippingServicesAvailable($product_id);
        $isShippingServiceAvailable = false;
        if($getallshipping!=NULL) {
            foreach ($getallshipping as $current) {
                if($current == $shipping_service) {
                    $isShippingServiceAvailable = true;
                }
            }
        }
        return $isShippingServiceAvailable;
    }
}

if(!function_exists('wpscShippingAPIAddToChecklist')) {
    /**
     *
     * @param type $product_id
     * @param type $wpscGlobalShippingOptionName
     * @param type $wpStoreCartProductShippingServicesName
     * @param string $caption 
     */
    function wpscShippingAPIAddToChecklist($product_id, $wpscGlobalShippingOptionName, $wpStoreCartProductShippingServicesName, $caption) {
	$wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 
        $return_value = null;
	if($wpStoreCartOptions['storetype']!='Digital Goods Only' && ($wpStoreCartOptions[$wpscGlobalShippingOptionName]=='true' || $wpStoreCartOptions[$wpscGlobalShippingOptionName]===true) ) { 
		$return_value .= '<br /><input type="checkbox" name="wpsc_product_shipping_toggle[]" value="'.$wpStoreCartProductShippingServicesName.'" '; 
		if ( wpscProductIsShippingServiceAvailable(intval($product_id), $wpStoreCartProductShippingServicesName) ) {$return_value .= 'checked="checked"';} 
		$return_value .= ' /> '.$caption;
	}   
        return $return_value;
    }
}

if(!function_exists('wpscProductEnableShippingService')) {
    /**
     * Enable the shipping service to the specified product
     * 
     * @global object $wpdb
     * @param integer $product_id
     * @param string $shipping_service
     * @param string $action 
     */
    function wpscProductEnableShippingService($product_id, $shipping_service, $action='add') {
        global $wpdb;
       
        $product_id = intval($product_id);
        
        $isAlreadyAvailable = wpscProductIsShippingServiceAvailable($product_id, $shipping_service); // Determine if the product already has this shipping method enabled.
        $append_value = null; // Save the other shipping service selections
        $remove_value = null;
        
        $results = $wpdb->get_results("SELECT `shippingservices` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`={$product_id};", ARRAY_N);
        if(@isset($results[0]['shippingservices']) && $results[0]['shippingservices']!='') {
            $append_value = $results[0]['shippingservices'].'||'; // Save the other shipping service selections
            $remove_value = $results[0]['shippingservices']; // Save the other shipping service selections
        }        
        
        if($action=='add') { // Add the shipping service to the product if needed
            if(!$isAlreadyAvailable) { // We only need to update the database if the shipping service is not already available
                $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `shippingservices`='{$append_value}{$shipping_service}' WHERE `primkey`='{$product_id}';");
            }
        }
        
        if($action=='remove') { // Remove the shipping service from the product if needed
            if($isAlreadyAvailable) { // We only need to update the database if the shipping service is already available
                $newvalue = str_replace($shipping_service.'||', '', $remove_value);
                $newvalue = str_replace($shipping_service, '', $newvalue);
                $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `shippingservices`='{$newvalue}' WHERE `primkey`='{$product_id}';");
            }            
        }        
    }
}

if(!function_exists('wpscProductDisableShippingService')) {
    function wpscProductDisableShippingService($product_id, $shipping_service) {
        wpscProductEnableShippingService(intval($product_id), $shipping_service, 'remove');
    }   
}


if(!function_exists('wpscProductToggleShippingService')) {
    function wpscProductToggleShippingService($product_id, $shipping_service) {
        $isEnabled = false;
        if(@isset($_POST['wpsc_product_shipping_toggle'])) {
            foreach($_POST['wpsc_product_shipping_toggle'] as $current) {
                if($current == $shipping_service) {
                    $isEnabled = true;
                }
            }
            if($isEnabled) {
                wpscProductEnableShippingService(intval($product_id), $shipping_service);
            } 
        }
        if(!$isEnabled) {
            wpscProductDisableShippingService(intval($product_id), $shipping_service);
        }
            
    }   
}







if(!function_exists('wpscShippingAPICheckoutDropdown')) {
    /**
     *
     * @param type $cart_contents 
     */
    function wpscShippingAPICheckoutDropdown($cart_contents) {
        $output = null;
        $savedShippingServices = array();
        foreach($cart_contents as $item) {
            $allShippingServicesAvailableForThisProduct = wpscProductGetShippingServicesAvailable($item['id']); // Grab all the available shipping services for this product
            foreach($allShippingServicesAvailableForThisProduct as $currentlyAvailable) {
                if($currentlyAvailable!='') {
                    if(!isset($savedShippingServices[$item['id']][$currentlyAvailable])) {
                        $savedShippingServices[$item['id']][$currentlyAvailable] = true;
                    }
                }
            }
        }
        
        return $savedShippingServices;
    }
}
    







if(!function_exists('wpscUPSParcelRate')) {
    function wpscUPSParcelRate() {
        require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/shipping/ups/class.shipping.php');
        $rate = new Ups;
        $rate->upsProduct("2DA"); // See upsProduct() function for codes
        $rate->origin("32825", "US"); // Use ISO country codes!
        $rate->dest("87540", "US"); // Use ISO country codes!
        $rate->rate("CC"); // See the rate() function for codes
        $rate->container("CP"); // See the container() function for codes
        $rate->weight("3");
        $rate->rescom("COM"); // See the rescom() function for codes
        $quote = $rate->getQuote();

        $rate->setSelectRate('2DA', $quote);
        $rate->upsProduct("3DS");
        $quote = $rate->getQuote();
        $rate->setSelectRate('3DS', $quote);
        $rate->upsProduct("GND");
        $quote = $rate->getQuote();
        $rate->setSelectRate('GND', $quote);

        return $rate->displayRatesHtml("data[shipping-type]", "radio");        
    }
}

if(!function_exists('wpscUSPSParcelRate')) {
    /**
        *
        * USPSParcelRate
        *
        * @param <type> $weight
        * @param <type> $dest_zip
        * @return array
        */
    function wpscUSPSParcelRate($weight,$dest_zip) {

        $devOptions = get_option('wpStoreCartAdminOptions'); 

        // This script was written by Mark Sanborn at http://www.marksanborn.net
        // If this script benefits you are your business please consider a donation
        // You can donate at http://www.marksanborn.net/donate.

        // ========== CHANGE THESE VALUES TO MATCH YOUR OWN ===========

        $userName = $devOptions['uspsapiname']; // Your USPS Username
        $orig_zip = $devOptions['shipping_zip_origin']; // Zipcode you are shipping FROM

        // =============== DON'T CHANGE BELOW THIS LINE ===============

        $url = "http://Production.ShippingAPIs.com/ShippingAPI.dll";
        $ch = curl_init();

        // set the target url
        curl_setopt($ch, CURLOPT_URL,$url);
        curl_setopt($ch, CURLOPT_HEADER, 1);
        curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

        // parameters to post
        curl_setopt($ch, CURLOPT_POST, 1);

        $data = "API=RateV3&XML=<RateV3Request USERID=\"$userName\"><Package ID=\"1ST\"><Service>PRIORITY</Service><ZipOrigination>$orig_zip</ZipOrigination><ZipDestination>$dest_zip</ZipDestination><Pounds>$weight</Pounds><Ounces>0</Ounces><Size>REGULAR</Size><Machinable>TRUE</Machinable></Package></RateV3Request>";

        // send the POST values to USPS
        curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

        $result=curl_exec ($ch);
        $data = strstr($result, '<?');
        // echo '<!-- '. $data. ' -->'; // Uncomment to show XML in comments
        $xml_parser = xml_parser_create();
        xml_parse_into_struct($xml_parser, $data, $vals, $index);
        xml_parser_free($xml_parser);
        $params = array();
        $level = array();
        foreach ($vals as $xml_elem) {
                if ($xml_elem['type'] == 'open') {
                        if (array_key_exists('attributes',$xml_elem)) {
                                list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
                        } else {
                        $level[$xml_elem['level']] = $xml_elem['tag'];
                        }
                }
                if ($xml_elem['type'] == 'complete') {
                $start_level = 1;
                $php_stmt = '$params';
                while($start_level < $xml_elem['level']) {
                        $php_stmt .= '[$level['.$start_level.']]';
                        $start_level++;
                }
                $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
                eval($php_stmt);
                }
        }
        curl_close($ch);
        //echo '<pre>'; print_r($params); echo'</pre>'; // Uncomment to see xml tags
        return $params['RATEV3RESPONSE']['1ST']['1']['RATE'];
    }
}


?>