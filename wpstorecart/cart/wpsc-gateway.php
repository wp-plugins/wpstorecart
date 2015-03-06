<?php

// wpsc library, v3.0 by wpStoreCart.com
// Forked from jcart v1.1 http://conceptlogic.com/jcart/

// THIS FILE IS CALLED WHEN ANY BUTTON ON THE CHECKOUT PAGE (PAYPAL CHECKOUT, UPDATE, OR EMPTY) IS CLICKED
// WE CAN ONLY DEFINE ONE FORM ACTION, SO THIS FILE ALLOWS US TO FORK THE FORM SUBMISSION DEPENDING ON WHICH BUTTON WAS CLICKED
// ALSO ALLOWS US TO VERIFY PRICES BEFORE SUBMITTING TO PAYPAL

// INCLUDE wpsc BEFORE SESSION START



global $wpsc_testing_mode, $wpsc_shoppingcart, $wpStoreCartOptions, $wpdb;
if($wpsc_testing_mode==false) {
    error_reporting(0);
}

//error_reporting(E_ALL);

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

if(!isset($_SESSION)) {
	session_start();
}

$wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 

$wpsc_shoppingcart = new wpsc_shoppingcart();

// WHEN JAVASCRIPT IS DISABLED THE UPDATE AND EMPTY BUTTONS ARE DISPLAYED
// RE-DISPLAY THE CART IF THE VISITOR CLICKS EITHER BUTTON
if (isset($_POST['wpsc_update_cart'])  || isset($_POST['wpsc_empty'])) {

	// UPDATE THE CART
	if (isset($_POST['wpsc_update_cart']))
		{
		$wpsc_shoppingcart_updated = $wpsc_shoppingcart->update_cart();
		if ($wpsc_shoppingcart_updated !== true)
			{
			$_SESSION['quantity_error'] = true;
			}
		}

	// EMPTY THE CART
	if (isset($_POST['wpsc_empty']))
		{
		$wpsc_shoppingcart->empty_cart();
		}

	// REDIRECT BACK TO THE CHECKOUT PAGE
	header('Location: ' . $_POST['wpsc_checkout_page']);
	exit;
        
} else {
    
        // THE VISITOR HAS CLICKED THE CHECKOUT BUTTON
	$valid_prices = true;

	if ($valid_prices !== true) { 	// IF THE SUBMITTED PRICES ARE NOT VALID
            die($wpsc['text']['checkout_error']); // KILL THE SCRIPT
        } else if ($valid_prices === true) {

            // SEND CART CONTENTS TO PAYPAL USING THEIR UPLOAD METHOD, FOR DETAILS SEE http://tinyurl.com/djoyoa
                global $current_user, $wpdb, $wpsc_shoppingcart;
                get_currentuserinfo();




                if(isset($_POST['wpsc-shipping-type']) ||isset($_POST['wpsc-shipping-type-widget']) ) {
                    @$shipping_type = $_POST['wpsc-shipping-type'];
                    @$shipping_type_widget = $_POST['wpsc-shipping-type-widget'];
                } else {
                    $shipping_type='shipping_offered_by_flatrate';
                    $shipping_type_widget='shipping_offered_by_flatrate';
                }
                
                // USPS shipping calculations are done here, if applicable.
                if($shipping_type=='shipping_offered_by_usps' || $shipping_type_widget=='shipping_offered_by_usps') {
                    $table_name_products = $wpdb->prefix . "wpstorecart_products";
                    $totalweight = 0;

                                    // IF ANY ITEMS IN THE CART
                                    if($wpsc_shoppingcart->itemcount > 0) {

                                            $newsplit = explode('-', $item['id'] );
                                            $item['id'] = $newsplit[0];

                                            // DISPLAY LINE ITEMS
                                            foreach($wpsc_shoppingcart->get_contents() as $item) {
                                                $results = $wpdb->get_results('SELECT `weight` FROM `'.$table_name_products.'` WHERE `primkey`='.$item['id'].';', ARRAY_N);
                                                $totalweight = $totalweight + $results[0][0];
                                                unset($results);
                                            }

                                    }


                    // USPS
                    $totalshippingcalculated = wpscUSPSParcelRate($totalweight, $_SESSION['wpsc_shipping_zipcode'] );

                    $usps_shipping_total = number_format($totalshippingcalculated, 2);
                    
                }

                
                
                
                // Allows us to bypass registration and have guest only checkout
                if($wpStoreCartOptions['requireregistration']=='false' || $wpStoreCartOptions['requireregistration']=='disable') {
                    if(@isset($_SESSION['wpsc_email'])) {
                        $purchaser_user_id = 0;
                        $purchaser_email = esc_sql($_SESSION['wpsc_email']);
                        $purchasing_display_name = 'Guest ('.esc_sql($_SERVER['REMOTE_ADDR']).')';
                    } 
                    if ( @isset($current_user->ID) && ( @$current_user->ID > 0 )){
                        $purchaser_user_id = $current_user->ID;
                        $purchaser_email = $current_user->user_email;
                        $purchasing_display_name = '%user_display_name_with_link%';
                    }
                } else { // Logged in users only
                        $purchaser_user_id = $current_user->ID;
                        $purchaser_email = $current_user->user_email;
                        $purchasing_display_name = '%user_display_name_with_link%';
                }



                // Added in wpStoreCart 3, this area calculates everything for our payment gateways
                global $wpscPaymentGateway;
                $wpscPaymentGateway = array(); 
                
                if(@isset($_POST['paymentGateway'])) {
                    $wpscPaymentGateway['payment_gateway'] = esc_sql($_POST['paymentGateway']);
                } else {
                    exit(); // if we have no payment gateway to process, then lets exit
                }

                // First, let's get our FAILED permalink
                if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                    $wpscPaymentGateway['failed_permalink'] = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=failed';
                } else {
                    $wpscPaymentGateway['failed_permalink'] = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=failed';
                }
                
                $wpscPaymentGateway['payment_gateway_item_count'] = 1; // The number of unique products in the cart.  So the first item in the cart is item 1, not item 0.
                $wpscPaymentGateway['cart_dbf'] = ''; // The cart contents in database format: 0*0
                $wpscPaymentGateway['cart_description'] = ''; // A comma separated list of item names and quantity of the entire cart, for example: Item Name One (x2), Item Two
                $wpscPaymentGateway['total_price'] = 0; // The total price of the cart, without shipping or taxes
                $wpscPaymentGateway['total_shipping'] = 0; // The total shipping for the cart
                $wpscPaymentGateway['total_price_with_shipping'] = 0; // The total price of the cart, including shipping charges
                $wpscPaymentGateway['final_price'] = 0; // The total price + the shipping price + calculated taxes
                $wpscPaymentGateway['final_price_with_discounts'] = 0; // The total price + the shipping price + calculated taxes + discounts
                $wpscPaymentGateway['is_coupon_set'] = false;  // False if no coupon has been set, true if there has been
                $wpscPaymentGateway['discount_amount'] = 0; // The amount to subtract from the total
                $wpscPaymentGateway['discount_percent'] = 0; // The percent to subtract from the total
                $wpscPaymentGateway['is_donation'] = false; // False if not a donation, true if it is
                $wpscPaymentGateway['customer_user_id'] = $purchaser_user_id; // The Wordpress user id of the customer, where 0 is a guest
                $wpscPaymentGateway['customer_email'] = $purchaser_email; // The email of the customer
                $wpscPaymentGateway['customer_username'] = $purchasing_display_name; // The display name of the customer.  This is their username, not their actual names
                $wpscPaymentGateway['affiliate_user_id'] = 0; // The Wordpress user id of the affiliate who is credited with referring the order, where 0 means no affiliate is credited
                $wpscPaymentGateway['order_id'] = 0; // The unique key associated with this order
                $wpscPaymentGateway['ordernote'] = null; // Order note with custom information
                
                foreach ($wpsc_shoppingcart->get_contents() as $item) {

                    $wpscPaymentGateway['cart_description'] = $wpscPaymentGateway['cart_description'] . $item['name'] .' (x'.$item['qty'].'), ';
                    $wpscPaymentGateway['cart'][$wpscPaymentGateway['payment_gateway_item_count']]['item_number'] = $wpscPaymentGateway['payment_gateway_item_count']; 
                    $wpscPaymentGateway['cart'][$wpscPaymentGateway['payment_gateway_item_count']]['id'] = $item['id'];
                    $wpscPaymentGateway['cart'][$wpscPaymentGateway['payment_gateway_item_count']]['name'] = $item['name']; 
                    $wpscPaymentGateway['cart'][$wpscPaymentGateway['payment_gateway_item_count']]['price'] = $item['price']; 
                    $wpscPaymentGateway['cart'][$wpscPaymentGateway['payment_gateway_item_count']]['shipping'] = $item['shipping']; // individual, flat rate only 
                    $wpscPaymentGateway['cart'][$wpscPaymentGateway['payment_gateway_item_count']]['qty'] = $item['qty']; 
                    $wpscPaymentGateway['cart'][$wpscPaymentGateway['payment_gateway_item_count']]['url'] = $item['url'];
                    $wpscPaymentGateway['cart'][$wpscPaymentGateway['payment_gateway_item_count']]['img'] = $item['img'];
                    
                    $wpscPaymentGateway['ordernote'] = $wpscPaymentGateway['ordernote'] . $item['id']. '-' .$item['name'] . ' - '. $item['options'] . ' ';
                    
                    // Implement shipping here if needed
                    $table_name = $wpdb->prefix . "wpstorecart_products";
                    $results = $wpdb->get_results( "SELECT `shipping`, `donation` FROM {$table_name} WHERE `primkey`={$item['id']} LIMIT 0, 1;", ARRAY_A );
                    if(isset($results)) {
                        if($results[0]['donation']=='1') {
                            $wpscPaymentGateway['is_donation'] = true;
                        }
                        if(($wpStoreCartOptions['storetype']!='Digital Goods Only' && $wpStoreCartOptions['flatrateshipping']=='individual') && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                            if($results[0]['shipping']!='0.00') {
                                $wpscPaymentGateway['total_shipping'] = number_format(round($wpscPaymentGateway['total_shipping'] + round($results[0]['shipping'] * $item['qty'], 2), 2), 2,'.' ,'');
                            }
                        } else {
                            $wpscPaymentGateway['total_shipping'] = 0;
                        }
                    }

                    if($wpStoreCartOptions['flatrateshipping']=='all_global' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                        $wpscPaymentGateway['total_shipping'] = number_format(round($wpStoreCartOptions['flatrateamount'], 2), 2,'.' ,'');
                    }
                    if($wpStoreCartOptions['flatrateshipping']=='all_single' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                        $wpscPaymentGateway['total_shipping'] = number_format(round($wpStoreCartOptions['flatrateamount'] * $item['qty'], 2), 2,'.' ,'');

                    }


                    // Check for a coupon
                    if(@!isset($_SESSION)) {
                            @session_start();
                    }

                    if($wpscPaymentGateway['is_coupon_set']==false && (@$_SESSION['validcouponid']==$item['id'])) {
                        if(isset($_SESSION['validcouponamount'])) {
                            $wpscPaymentGateway['discount_amount'] = number_format(round($_SESSION['validcouponamount'], 2), 2,'.' ,'');
                        }
                        if(isset($_SESSION['validcouponpercent']) && $_SESSION['validcouponpercent'] != 0) { //
                            $discount_priceper = round(($item['qty'] * $item['price']) * ($_SESSION['validcouponpercent'] / 100), 2);
                            $wpscPaymentGateway['discount_amount'] = number_format(round($discount_priceper, 2), 2,'.' ,'');
                        }
                        $wpscPaymentGateway['is_coupon_set'] = true;
                    }


                    $wpscPaymentGateway['cart_dbf'] = $wpscPaymentGateway['cart_dbf'] . $item['id'] .'*'.$item['qty'].',';
                    $wpscPaymentGateway['total_price'] = $wpscPaymentGateway['total_price'] + ($item['price'] * $item['qty']);


                    // INCREMENT THE COUNTER
                    ++$wpscPaymentGateway['payment_gateway_item_count'];
                }

                if($shipping_type=='shipping_offered_by_usps' || $shipping_type_widget=='shipping_offered_by_usps') {
                    $wpscPaymentGateway['total_shipping'] = number_format(round($usps_shipping_total, 2), 2,'.' ,''); // We use the calculated USPS shipping total if applicable

                }
                
                if(@isset($_POST['wpsc-shipping-dropdown'])) {
                    // Recalculates shipping server side, to prevent client side manipulation
                    $finalApprovedShippingServicesKey = strstr($_POST['wpsc-shipping-dropdown'], '[', true);
                    
                    preg_match_all("^\[(.*?)\]^",$_POST['wpsc-shipping-dropdown'],$fields, PREG_PATTERN_ORDER);
                    
                    $wpscCurrentShippingFunction = 'wpscShippingAPIFinalGateway_'.$finalApprovedShippingServicesKey;

                    if(@function_exists($wpscCurrentShippingFunction)) {
                        $wpscPaymentGateway['total_shipping'] = @$wpscCurrentShippingFunction($wpsc_shoppingcart->get_contents(), $fields[1][0]); // Magically calls the function
                    }     
                }

                
                if(@isset($_SESSION['validcouponamount']) && $wpscPaymentGateway['is_coupon_set']==false) {
                    if(isset($_SESSION['validcouponamount'])) {
                        $wpscPaymentGateway['discount_amount'] = number_format(round($_SESSION['validcouponamount'], 2), 2,'.' ,'');
                    }
                    if(isset($_SESSION['validcouponpercent']) && $_SESSION['validcouponpercent'] != 0) { //
                        $wpscPaymentGateway['discount_percent'] = $_SESSION['validcouponpercent'];
                    }
                    $wpscPaymentGateway['is_coupon_set'] = true;
                }

                $wpscPaymentGateway['cart_dbf'] = $wpscPaymentGateway['cart_dbf'] . '0*0';

                // Insert the order into the database
                $table_name = $wpdb->prefix . "wpstorecart_orders";
                $timestamp = date('Ymd');
                if(!isset($_COOKIE['wpscPROaff']) || !is_numeric($_COOKIE['wpscPROaff'])) {
                    $wpscPaymentGateway['affiliate_user_id'] = 0;
                } else {
                    $wpscPaymentGateway['affiliate_user_id'] = $_COOKIE['wpscPROaff'];
                    //setcookie ("wpscPROaff", "", time() - 3600); // Remove the affiliate ID
                }
                $insert = "
                INSERT INTO `{$table_name}`
                (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`, `wpuser`, `email`, `affiliate`, `date`) VALUES
                (NULL, 'Pending', '{$wpscPaymentGateway['cart_dbf']}', '{$wpscPaymentGateway['payment_gateway']}', '{$wpscPaymentGateway['total_price']}', '{$wpscPaymentGateway['total_shipping']}', '{$wpscPaymentGateway['customer_user_id']}', '{$wpscPaymentGateway['customer_email']}', '{$wpscPaymentGateway['affiliate_user_id']}', '{$timestamp}');
                ";

                $results = $wpdb->query( $insert );
                $wpscPaymentGateway['order_id'] = $wpdb->insert_id;
                
                
                // Order note:
                if($wpscPaymentGateway['ordernote']!=null) {
                    $sql = "INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '".esc_sql($wpscPaymentGateway['ordernote'])."', 'ordernote', '{$wpscPaymentGateway['order_id']}');";
                    $wpdb->query( $sql );                
                }
                
                if(@isset($_COOKIE['wpscPROaff']) || @is_numeric($_COOKIE['wpscPROaff'])) { // More affiliate code
                    $wpdb->query( "INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '0.00', 'affiliatepayment', '{$wpscPaymentGateway['order_id']}');");
                }


                // Grab the SUCCESS permalink
                if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                    $wpscPaymentGateway['success_permalink'] = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=success';
                } else {
                    $wpscPaymentGateway['success_permalink'] = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=success';
                }                 

                // Price with shipping
                $wpscPaymentGateway['total_price_with_shipping']  = number_format(round($wpscPaymentGateway['total_price'] + $wpscPaymentGateway['total_shipping'], 2), 2,'.' ,'');
                
                // Tax
                $wpscPaymentGateway['order_tax'] = wpscCalculateTaxes($wpscPaymentGateway['total_price_with_shipping']);
                if($wpscPaymentGateway['order_tax'] > 0) {
                    $wpdb->query( "INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '{$wpscPaymentGateway['order_tax']}', 'ordertax', '{$wpscPaymentGateway['order_id']}');" );
                }

                // Calculate final tallys
                $wpscPaymentGateway['final_price'] = number_format(round($wpscPaymentGateway['total_price_with_shipping'] + $wpscPaymentGateway['order_tax'], 2), 2,'.' ,'');
                $wpscPaymentGateway['final_price_with_discounts'] = $wpscPaymentGateway['final_price']; // Same as final_price if no discounts are applied
                
                // Calculate final tally with discounts applied
                if($wpscPaymentGateway['discount_amount'] > 0) {
                    $wpscPaymentGateway['final_price_with_discounts'] = number_format(round($wpscPaymentGateway['final_price'] - $wpscPaymentGateway['discount_amount'], 2), 2,'.' ,'');
                }
                
                
                wpsc_process_payment_gateways(); // Action hook to process payments
                
                
                $wpsc_shoppingcart->empty_cart();     // EMPTY THE CART

      
                
                
                
                // Manual Payment gateway start
                if($wpscPaymentGateway['payment_gateway'] == 'checkmoneyorder') {

                    @header ('HTTP/1.1 301 Moved Permanently');
                    if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                        @header ('Location: '.get_permalink($wpStoreCartOptions['mainpage']).'?wpsc=manual&order='.$wpscPaymentGateway['order_id'].'&price='.$wpscPaymentGateway['final_price']);
                    } else {
                        @header ('Location: '.get_permalink($wpStoreCartOptions['mainpage']).'&wpsc=manual&order='.$wpscPaymentGateway['order_id'].'&price='.$wpscPaymentGateway['final_price']);
                    }

                    echo '<script type="text/javascript">
                    /* <![CDATA[ */';
                    if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                        echo 'window.location = "'.get_permalink($wpStoreCartOptions['mainpage']).'?wpsc=manual&order='.$wpscPaymentGateway['order_id'].'&price='.$wpscPaymentGateway['final_price'].'";';
                    } else {
                        echo 'window.location = "'.get_permalink($wpStoreCartOptions['mainpage']).'&wpsc=manual&order='.$wpscPaymentGateway['order_id'].'&price='.$wpscPaymentGateway['final_price'].'";';
                    }
                    echo '/* ]]> */
                    </script>
                    ';
                    
                    exit();
                }
                // Manual Payment gateway end
                

                
                if($wpscPaymentGateway['payment_gateway'] == 'paypal') {
                    // PAYPAL COUNT STARTS AT ONE INSTEAD OF ZERO
                    // Include the paypal library
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/payment/PaymentGateway.php');
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/payment/gateway.paypal.php');

                    $myPaypal = new Paypal(); // Create an instance of the paypal library
                    
                    $myPaypal->addField('business', $wpStoreCartOptions['paypalemail']); // Specify your paypal email
                    $myPaypal->addField('currency_code', $wpStoreCartOptions['currency_code']); // Specify the currency
                    $myPaypal->addField('rm', '2'); // Return method = POST
                    $myPaypal->addField('upload', '1');
                    $myPaypal->addField('cancel_return', $wpscPaymentGateway['failed_permalink']);
                    $myPaypal->addField('notify_url', $wpStoreCartOptions['paypalipnurl']); // Specify the url where paypal will send the IPN
                    if($wpStoreCartOptions['paypaltestmode']=='true') { $myPaypal->enableTestMode(); } // Enable test mode if needed

                    foreach ($wpscPaymentGateway['cart'] as $item) {
                                // BUILD THE QUERY STRING
                                // Specify the product information
                                // Put the coupon coding here too
                                $myPaypal->addField('item_name_' . $item['item_number'], $item['name']);
                                $myPaypal->addField('amount_' . $item['item_number'], $item['price']);
                                $myPaypal->addField('item_number_' . $item['item_number'], $item['item_number']);
                                $myPaypal->addField('quantity_' . $item['item_number'], $item['qty']);



                    }

                    // Implement shipping here if needed
                    if($wpscPaymentGateway['total_shipping'] > 0) {
                        $myPaypal->addField('shipping_1', $wpscPaymentGateway['total_shipping']);
                    }                    
                    
                    if($wpscPaymentGateway['is_donation'] == true) {
                        $myPaypal->addField('cmd', '_donations');
                    } else {
                        $myPaypal->addField('cmd', '_cart');
                    }

                    if($wpscPaymentGateway['discount_amount'] > 0) {
                        @$myPaypal->addField('discount_amount_cart', $wpscPaymentGateway['discount_amount']);
                    }
                    if($wpscPaymentGateway['discount_percent'] > 0) { //
                        @$myPaypal->addField('discount_rate_cart', $wpscPaymentGateway['discount_percent']);
                    }

                    

                    $myPaypal->addField('return', $wpscPaymentGateway['success_permalink']);                    
                    
                    // Specify any custom value, here we send the primkey of the order record
                    $myPaypal->addField('custom', $wpscPaymentGateway['order_id']);

                    // Tax
                    if($wpscPaymentGateway['order_tax'] > 0) {
                        $myPaypal->addField('tax_cart', $wpscPaymentGateway['order_tax']);
                    }


                    //

                    if(isset($wpsc['paypal_id'])) {
 
                            $myPaypal->submitPayment();
 
                            exit;
                        } else {
                        // THE USER HAS NOT CONFIGURED A PAYPAL ID
                        // DISPLAY THE PAYPAL URL WITH AN ERROR MESSAGE

                        _e('PayPal integration requires a secure merchant ID.  Please add your email address to the wpStoreCart options page.', 'wpstorecart');

                        exit;
                        }
                    }
                } // End PayPal code
	}

?>