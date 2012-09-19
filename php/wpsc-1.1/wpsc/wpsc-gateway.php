<?php

// wpsc library, v1.1a by wpStoreCart.com
// Forked from jcart v1.1 http://conceptlogic.com/jcart/

// THIS FILE IS CALLED WHEN ANY BUTTON ON THE CHECKOUT PAGE (PAYPAL CHECKOUT, UPDATE, OR EMPTY) IS CLICKED
// WE CAN ONLY DEFINE ONE FORM ACTION, SO THIS FILE ALLOWS US TO FORK THE FORM SUBMISSION DEPENDING ON WHICH BUTTON WAS CLICKED
// ALSO ALLOWS US TO VERIFY PRICES BEFORE SUBMITTING TO PAYPAL

// INCLUDE wpsc BEFORE SESSION START

global $wpsc_error_reporting, $wpsc_cart_type;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}
include_once(ABSPATH . 'wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');

if(!isset($_SESSION)) {
	session_start();
}

if(isset($wpStoreCart)) {
	$devOptions = $wpStoreCart->getAdminOptions();
} else {
	exit();
}

// INITIALIZE wpsc AFTER SESSION START
if($wpsc_cart_type == 'session') {
    $cart =& $_SESSION['wpsc']; if(!is_object($cart)) $cart = new wpsc();
}
if($wpsc_cart_type == 'cookie') {
    if(@!is_object($cart)) {
        if(isset($_COOKIE['wpsccart'])) { @$cart =& unserialize(base64_decode($_COOKIE['wpsccart'])); }
        if(@!is_object($cart) && !isset($_COOKIE['wpsccart'])) {
            $cart = new wpsc();
            $xdomain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;setcookie('wpsccart', base64_encode(serialize($cart)), time()+7222, '/', $xdomain, false);
        }
    }
}

// WHEN JAVASCRIPT IS DISABLED THE UPDATE AND EMPTY BUTTONS ARE DISPLAYED
// RE-DISPLAY THE CART IF THE VISITOR CLICKS EITHER BUTTON
if (isset($_POST['wpsc_update_cart'])  || isset($_POST['wpsc_empty']))
	{

	// UPDATE THE CART
	if (isset($_POST['wpsc_update_cart']))
		{
		$cart_updated = $cart->update_cart();
		if ($cart_updated !== true)
			{
			$_SESSION['quantity_error'] = true;
			}
		}

	// EMPTY THE CART
	if (isset($_POST['wpsc_empty']))
		{
		$cart->empty_cart();
		}

	// REDIRECT BACK TO THE CHECKOUT PAGE
	header('Location: ' . $_POST['wpsc_checkout_page']);
	exit;
	}

// THE VISITOR HAS CLICKED THE CHECKOUT BUTTON
else
	{

	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////
	/*

	A malicious visitor may try to change item prices before checking out,
	either via javascript or by posting from an external script.

	Here you can add PHP code that validates the submitted prices against
	your database or validates against hard-coded prices.

	The cart data has already been sanitized and is available thru the
	$cart->get_contents() function. For example:

	foreach ($cart->get_contents() as $item)
		{
		$item_id	= $item['id'];
		$item_name	= $item['name'];
		$item_price	= $item['price'];
		$item_qty	= $item['qty'];
		}

	*/
	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////

	$valid_prices = true;

	///////////////////////////////////////////////////////////////////////
	///////////////////////////////////////////////////////////////////////

	// IF THE SUBMITTED PRICES ARE NOT VALID
	if ($valid_prices !== true)
		{
		// KILL THE SCRIPT
		die($wpsc['text']['checkout_error']);
		}

	// PRICE VALIDATION IS COMPLETE
	// SEND CART CONTENTS TO PAYPAL USING THEIR UPLOAD METHOD, FOR DETAILS SEE http://tinyurl.com/djoyoa
	else if ($valid_prices === true)
		{

                global $current_user, $wpdb, $paymentGateway, $paymentGatewayOptions,$cart;
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
                                    if($cart->itemcount > 0) {

                                            $newsplit = explode('-', $item['id'] );
                                            $item['id'] = $newsplit[0];

                                            // DISPLAY LINE ITEMS
                                            foreach($cart->get_contents() as $item) {
                                                $results = $wpdb->get_results('SELECT `weight` FROM `'.$table_name_products.'` WHERE `primkey`='.$item['id'].';', ARRAY_N);
                                                $totalweight = $totalweight + $results[0][0];
                                                unset($results);
                                            }

                                    }

                    if(!isset($_SESSION)) {
                            @session_start();
                    }
                    $_SESSION['wpsc_zipcode'] = $_POST['wpsc-zipcode-input'];

                    // USPS
                    $totalshippingcalculated = $wpStoreCart->USPSParcelRate($totalweight, $_SESSION['wpsc_zipcode'] );

                    $usps_shipping_total = number_format($totalshippingcalculated, 2);
                    
                }

                // Allows us to bypass registration and have guest only checkout
                if($devOptions['requireregistration']=='false') {
                    if(@isset($_SESSION['wpsc_email'])) {
                        $purchaser_user_id = 0;
                        $purchaser_email = $wpdb->escape($_SESSION['wpsc_email']);
                        $purchasing_display_name = 'Guest ('.$_SERVER['REMOTE_ADDR'].')';
                    } else {
                        $purchaser_user_id = $current_user->ID;
                        $purchaser_email = $current_user->user_email;
                        $purchasing_display_name = '%user_display_name_with_link%';
                    }
                } else {
                        $purchaser_user_id = $current_user->ID;
                        $purchaser_email = $current_user->user_email;
                        $purchasing_display_name = '%user_display_name_with_link%';
                }


                if(class_exists('ThreeWP_Activity_Monitor')) {
                    do_action('threewp_activity_monitor_new_activity', array(
                        'activity_type' => 'wpsc-checkout',
                        'tr_class' => '',
                        'activity' => array(
                            "" => "{$purchasing_display_name} has finished checkout and has been sent to pay for their shopping cart.",
                            "Email" => $purchaser_email,
                            "Payment Gateway: " => "{$wpdb->escape($_POST['paymentGateway'])}",
                            "Cart price: " => $cart->itemcount. ' item costing '.$devOptions['currency_symbol'] .number_format($cart->total, 2) .$devOptions['currency_symbol_right'],
                        ),
                    ));
                }


                $paymentGateway = 'checkmoneyorder';

                if(@isset($_POST['paymentGateway'])) {
                    $paymentGateway = $wpdb->escape($_POST['paymentGateway']);
                }

                if($paymentGateway == 'checkmoneyorder') {
                    $cartContents = '';
                    $totalPrice = 0;
                    $totalShipping = 0;
                    $amountToSubtractFromCart = 0;
                    foreach ($cart->get_contents() as $item) {

                            if(($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                // Implement shipping here if needed
                                $table_name = $wpdb->prefix . "wpstorecart_products";
                                $results = $wpdb->get_results( "SELECT `shipping` FROM {$table_name} WHERE `primkey`={$item['id']} LIMIT 0, 1;", ARRAY_A );
                                if(isset($results)) {
                                    if($results[0]['shipping']!='0.00') {
                                        $totalShipping = $totalShipping + round($results[0]['shipping'] * $item['qty'], 2);
                                    }
                                }
                            } else {
                                $totalShipping = 0;
                            }

                            if($devOptions['flatrateshipping']=='all_global' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $totalShipping = $devOptions['flatrateamount'];
                            }
                            if($devOptions['flatrateshipping']=='all_single' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $totalShipping = round($devOptions['flatrateamount'] * $item['qty'], 2);
                            }
                            if($shipping_type=='shipping_offered_by_usps' || $shipping_type_widget=='shipping_offered_by_usps') {
                                    $totalShipping = $usps_shipping_total; // We use the calculated USPS shipping total if applicable
                            }

                            // Check for a coupon
                            if(@!isset($_SESSION)) {
                                    @session_start();
                            }
                            if(@$_SESSION['validcouponid']==$item['id']) {
                                @$amountToSubtractFromCart  = $_SESSION['validcouponamount'];
                            }


                            $cartContents = $cartContents . $item['id'] .'*'.$item['qty'].',';
                            $totalPrice = $totalPrice + ($item['price'] * $item['qty']);

                    }

                    $totalPrice = $totalPrice - $amountToSubtractFromCart; // Apply the coupon plus shipping
                    $cartContents = $cartContents . '0*0';

                    // Insert the order into the database
                    $table_name = $wpdb->prefix . "wpstorecart_orders";
                    $timestamp = date('Ymd');
                    if(!isset($_COOKIE['wpscPROaff']) || !is_numeric($_COOKIE['wpscPROaff'])) {
                        $affiliateid = 0;
                    } else {
                        $affiliateid = $_COOKIE['wpscPROaff'];
                        //setcookie ("wpscPROaff", "", time() - 3600); // Remove the affiliate ID
                    }
                    $insert = "
                    INSERT INTO `{$table_name}`
                    (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`,
                    `wpuser`, `email`, `affiliate`, `date`) VALUES
                    (NULL, 'Pending', '{$cartContents}', 'Check/Money Order', '{$totalPrice}', '{$totalShipping}', '{$purchaser_user_id}', '{$purchaser_email}', '{$affiliateid}', '{$timestamp}');
                    ";

                    $results = $wpdb->query( $insert );
                    $lastID = $wpdb->insert_id;
                    if(isset($_COOKIE['wpscPROaff']) || is_numeric($_COOKIE['wpscPROaff'])) { // More affiliate code
                        $wpdb->query( "INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '0.00', 'affiliatepayment', '{$lastID}');");
                    }
                    $keytoedit = $lastID;

                    $totalPrice = $totalPrice  + $totalShipping; // Apply the coupon plus shipping

                    $cart->empty_cart();
                    
                    @header ('HTTP/1.1 301 Moved Permanently');
                    if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                        @header ('Location: '.get_permalink($devOptions['mainpage']).'?wpsc=manual&order='.$keytoedit.'&price='.$totalPrice);
                    } else {
                        @header ('Location: '.get_permalink($devOptions['mainpage']).'&wpsc=manual&order='.$keytoedit.'&price='.$totalPrice);
                    }

                    echo '<script type="text/javascript">
                    /* <![CDATA[ */';
                    if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                        echo 'window.location = "'.get_permalink($devOptions['mainpage']).'?wpsc=manual&order='.$keytoedit.'&price='.$totalPrice.'";';
                    } else {
                        echo 'window.location = "'.get_permalink($devOptions['mainpage']).'&wpsc=manual&order='.$keytoedit.'&price='.$totalPrice.'";';
                    }
                    echo '/* ]]> */
                    </script>
                    ';
                }
                
                if($paymentGateway == 'authorize.net') {
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/php/payment/Authorize.php');
                    $paymentGatewayOptions['ipn'] = WP_PLUGIN_URL.'/wpstorecart/php/payment/authorize_ipn.php';
                    $paymentGatewayOptions['success'] = WP_PLUGIN_URL.'/wpstorecart/php/payment/authorize_success.php';
                    $paymentGatewayOptions['authorizenettestmode'] = $devOptions['authorizenettestmode'];
                    $paymentGatewayOptions['authorizenetemail'] = $devOptions['authorizenetemail'];
                    $paymentGatewayOptions['authorizenetsecretkey'] = $devOptions['authorizenetsecretkey'];
                    $paymentGatewayOptions['theCartNames'] = '';
                    $paymentGatewayOptions['theCartPrice'] = 0.00;
                    $cartContents = '';
                    $paymentGatewayOptions['totalPrice'] = 0;
                    $paymentGatewayOptions['totalShipping'] = 0;
                    foreach ($cart->get_contents() as $item) {
                            $paymentGatewayOptions['theCartNames'] = $paymentGatewayOptions['theCartNames'] . $item['name'] .' x'.$item['qty'].', ';
                            $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] + ($item['price'] * $item['qty']);

                            if(($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                // Implement shipping here if needed
                                $table_name = $wpdb->prefix . "wpstorecart_products";
                                $results = $wpdb->get_results( "SELECT `shipping` FROM {$table_name} WHERE `primkey`={$item['id']} LIMIT 0, 1;", ARRAY_A );
                                if(isset($results)) {
                                    if($results[0]['shipping']!='0.00') {
                                        $paymentGatewayOptions['totalShipping'] = $paymentGatewayOptions['totalShipping'] + round($results[0]['shipping'] * $item['qty'], 2);
                                    }
                                }
                            } else {
                                $paymentGatewayOptions['totalShipping'] = 0;
                            }

                            if($devOptions['flatrateshipping']=='all_global' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $paymentGatewayOptions['totalShipping'] = $devOptions['flatrateamount'];
                            }
                            if($devOptions['flatrateshipping']=='all_single' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $paymentGatewayOptions['totalShipping'] = round($devOptions['flatrateamount'] * $item['qty'], 2);
                            }
                            if($shipping_type=='shipping_offered_by_usps' || $shipping_type_widget=='shipping_offered_by_usps') {
                                    $paymentGatewayOptions['totalShipping'] = $usps_shipping_total; // We use the calculated USPS shipping total if applicable
                            }

                            // Check for a coupon
                            if(@!isset($_SESSION)) {
                                    @session_start();
                            }
                            if(@$_SESSION['validcouponid']==$item['id']) {
                                $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] - $_SESSION['validcouponamount'];
                            }


                            $cartContents = $cartContents . $item['id'] .'*'.$item['qty'].',';
                            $totalPrice = $totalPrice + ($item['price'] * $item['qty']);

                    }

                    $paymentGatewayOptions['theCartNames'] = $paymentGatewayOptions['theCartNames'] . 'shipping: '.$paymentGatewayOptions['totalShipping'];

                    $cartContents = $cartContents . '0*0';

                    // Insert the order into the database
                    $table_name = $wpdb->prefix . "wpstorecart_orders";
                    $timestamp = date('Ymd');
                    if(!isset($_COOKIE['wpscPROaff']) || !is_numeric($_COOKIE['wpscPROaff'])) {
                        $affiliateid = 0;
                    } else {
                        $affiliateid = $_COOKIE['wpscPROaff'];
                        //setcookie ("wpscPROaff", "", time() - 3600); // Remove the affiliate ID
                    }
                    $paymentGatewayOptions['userid'] = $purchaser_user_id;

                    $insert = "
                    INSERT INTO `{$table_name}`
                    (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`,
                    `wpuser`, `email`, `affiliate`, `date`) VALUES
                    (NULL, 'Pending', '{$cartContents}', 'Authorize.Net', '{$paymentGatewayOptions['theCartPrice']}', '{$paymentGatewayOptions['totalShipping']}', '{$purchaser_user_id}', '{$purchaser_email}', '{$affiliateid}', '{$timestamp}');
                    ";

                    $results = $wpdb->query( $insert );
                    $lastID = $wpdb->insert_id;
                    if(isset($_COOKIE['wpscPROaff']) || is_numeric($_COOKIE['wpscPROaff'])) { // More affiliate code
                        $wpdb->query( "INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '0.00', 'affiliatepayment', '{$lastID}');");
                    }
                    $keytoedit = $lastID;

                    // Specify any custom value, here we send the primkey of the order record
                    $paymentGatewayOptions['invoice'] = $lastID;
                    $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] + $paymentGatewayOptions['totalShipping'];

                    //
                    $cart->empty_cart();
                    $paymentGatewayOptions['path']=WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/';
                    include_once(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php');
                }

                if($paymentGateway == '2checkout') {
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/php/payment/TwoCo.php');
                    $paymentGatewayOptions['ipn'] = WP_PLUGIN_URL.'/wpstorecart/php/payment/2co_ipn.php';
                    $paymentGatewayOptions['2checkouttestmode'] = $devOptions['2checkouttestmode'];
                    $paymentGatewayOptions['2checkoutemail'] = $devOptions['2checkoutemail'];
                    $paymentGatewayOptions['theCartNames'] = '';
                    $paymentGatewayOptions['theCartPrice'] = 0.00;
                    $cartContents = '';
                    $paymentGatewayOptions['totalPrice'] = 0;
                    $paymentGatewayOptions['totalShipping'] = 0;
                    foreach ($cart->get_contents() as $item) {
                            $paymentGatewayOptions['theCartNames'] = $paymentGatewayOptions['theCartNames'] . $item['name'] .' x'.$item['qty'].', ';
                            $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] + ($item['price'] * $item['qty']);

                            if(($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                // Implement shipping here if needed
                                $table_name = $wpdb->prefix . "wpstorecart_products";
                                $results = $wpdb->get_results( "SELECT `shipping` FROM {$table_name} WHERE `primkey`={$item['id']} LIMIT 0, 1;", ARRAY_A );
                                if(isset($results)) {
                                    if($results[0]['shipping']!='0.00') {
                                        $paymentGatewayOptions['totalShipping'] = $paymentGatewayOptions['totalShipping'] + round($results[0]['shipping'] * $item['qty'], 2);
                                    }
                                }
                            } else {
                                $paymentGatewayOptions['totalShipping'] = 0;
                            }
                            if($devOptions['flatrateshipping']=='all_global' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $paymentGatewayOptions['totalShipping'] = $devOptions['flatrateamount'];
                            }
                            if($devOptions['flatrateshipping']=='all_single' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $paymentGatewayOptions['totalShipping'] = round($devOptions['flatrateamount'] * $item['qty'], 2);
                            }
                            if($shipping_type=='shipping_offered_by_usps' || $shipping_type_widget=='shipping_offered_by_usps') {
                                    $paymentGatewayOptions['totalShipping'] = $usps_shipping_total; // We use the calculated USPS shipping total if applicable
                            }

                            // Check for a coupon
                            if(@!isset($_SESSION)) {
                                    @session_start();
                            }
                            if(@$_SESSION['validcouponid']==$item['id']) {
                                $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] - $_SESSION['validcouponamount'];
                            }


                            $cartContents = $cartContents . $item['id'] .'*'.$item['qty'].',';
                            $totalPrice = $totalPrice + ($item['price'] * $item['qty']);

                    }

                    $paymentGatewayOptions['theCartNames'] = $paymentGatewayOptions['theCartNames'] . 'shipping: '.$paymentGatewayOptions['totalShipping'];

                    $cartContents = $cartContents . '0*0';

                    // Insert the order into the database
                    $table_name = $wpdb->prefix . "wpstorecart_orders";
                    $timestamp = date('Ymd');
                    if(!isset($_COOKIE['wpscPROaff']) || !is_numeric($_COOKIE['wpscPROaff'])) {
                        $affiliateid = 0;
                    } else {
                        $affiliateid = $_COOKIE['wpscPROaff'];
                        //setcookie ("wpscPROaff", "", time() - 3600); // Remove the affiliate ID
                    }
                    $paymentGatewayOptions['userid'] = $purchaser_user_id;

                    $insert = "
                    INSERT INTO `{$table_name}`
                    (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`,
                    `wpuser`, `email`, `affiliate`, `date`) VALUES
                    (NULL, 'Pending', '{$cartContents}', '2CheckOut', '{$paymentGatewayOptions['theCartPrice']}', '{$paymentGatewayOptions['totalShipping']}', '{$purchaser_user_id}', '{$purchaser_email}', '{$affiliateid}', '{$timestamp}');
                    ";

                    $results = $wpdb->query( $insert );
                    $lastID = $wpdb->insert_id;
                    if(isset($_COOKIE['wpscPROaff']) || is_numeric($_COOKIE['wpscPROaff'])) { // More affiliate code
                        $wpdb->query( "INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '0.00', 'affiliatepayment', '{$lastID}');");
                    }
                    $keytoedit = $lastID;
                    $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] + $paymentGatewayOptions['totalShipping'];

                    // Specify any custom value, here we send the primkey of the order record
                    $paymentGatewayOptions['invoice'] = $lastID;

                    $cart->empty_cart();

                    $paymentGatewayOptions['path']=WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/';
                    $paymentGatewayOptions['shortpath']=WP_PLUGIN_DIR.'/wpsc-payments-pro/';
                    include_once(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php');
                }

                if($paymentGateway == 'libertyreserve') {
                    @include_once(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/libertyreserve/lb.php');
                    $paymentGatewayOptions['ipn'] = WP_PLUGIN_URL.'/wpsc-payments-pro/lr/lbi.php';
                    $paymentGatewayOptions['success'] = WP_PLUGIN_URL.'/wpsc-payments-pro/lr/lbs.php';
                    $paymentGatewayOptions['failure'] = WP_PLUGIN_URL.'/wpsc-payments-pro/lr/lbf.php';
                    $paymentGatewayOptions['libertyreserveaccount'] = $devOptions['libertyreserveaccount'];
                    $paymentGatewayOptions['libertyreservestore'] = $devOptions['libertyreservestore'];
                    $paymentGatewayOptions['authorizenetsecretkey'] = $devOptions['authorizenetsecretkey'];
                    $paymentGatewayOptions['theCartNames'] = '';
                    $paymentGatewayOptions['theCartPrice'] = 0.00;
                    $cartContents = '';
                    $paymentGatewayOptions['totalPrice'] = 0;
                    $paymentGatewayOptions['totalShipping'] = 0;
                    foreach ($cart->get_contents() as $item) {
                            $paymentGatewayOptions['theCartNames'] = $paymentGatewayOptions['theCartNames'] . $item['name'] .' x'.$item['qty'].', ';
                            $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] + ($item['price'] * $item['qty']);

                            if(($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                // Implement shipping here if needed
                                $table_name = $wpdb->prefix . "wpstorecart_products";
                                $results = $wpdb->get_results( "SELECT `shipping` FROM {$table_name} WHERE `primkey`={$item['id']} LIMIT 0, 1;", ARRAY_A );
                                if(isset($results)) {
                                    if($results[0]['shipping']!='0.00') {
                                        $paymentGatewayOptions['totalShipping'] = $paymentGatewayOptions['totalShipping'] + round($results[0]['shipping'] * $item['qty'], 2);
                                    }
                                }
                            } else {
                                $paymentGatewayOptions['totalShipping'] = 0;
                            }

                            if($devOptions['flatrateshipping']=='all_global' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $paymentGatewayOptions['totalShipping'] = $devOptions['flatrateamount'];
                            }
                            if($devOptions['flatrateshipping']=='all_single' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $paymentGatewayOptions['totalShipping'] = round($devOptions['flatrateamount'] * $item['qty'], 2);
                            }
                            if($shipping_type=='shipping_offered_by_usps' || $shipping_type_widget=='shipping_offered_by_usps') {
                                    $paymentGatewayOptions['totalShipping'] = $usps_shipping_total; // We use the calculated USPS shipping total if applicable
                            }

                            // Check for a coupon
                            if(@!isset($_SESSION)) {
                                    @session_start();
                            }
                            if(@$_SESSION['validcouponid']==$item['id']) {
                                $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] - $_SESSION['validcouponamount'];
                            }


                            $cartContents = $cartContents . $item['id'] .'*'.$item['qty'].',';
                            $totalPrice = $totalPrice + ($item['price'] * $item['qty']);

                    }

                    $paymentGatewayOptions['theCartNames'] = $paymentGatewayOptions['theCartNames'] . 'shipping: '.$paymentGatewayOptions['totalShipping'];

                    $cartContents = $cartContents . '0*0';

                    // Insert the order into the database
                    $table_name = $wpdb->prefix . "wpstorecart_orders";
                    $timestamp = date('Ymd');
                    if(!isset($_COOKIE['wpscPROaff']) || !is_numeric($_COOKIE['wpscPROaff'])) {
                        $affiliateid = 0;
                    } else {
                        $affiliateid = $_COOKIE['wpscPROaff'];
                        //setcookie ("wpscPROaff", "", time() - 3600); // Remove the affiliate ID
                    }
                    $paymentGatewayOptions['userid'] = $purchaser_user_id;

                    $insert = "
                    INSERT INTO `{$table_name}`
                    (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`,
                    `wpuser`, `email`, `affiliate`, `date`) VALUES
                    (NULL, 'Pending', '{$cartContents}', 'Liberty Reserve', '{$paymentGatewayOptions['theCartPrice']}', '{$paymentGatewayOptions['totalShipping']}', '{$purchaser_user_id}', '{$purchaser_email}', '{$affiliateid}', '{$timestamp}');
                    ";

                    $results = $wpdb->query( $insert );
                    $lastID = $wpdb->insert_id;
                    if(isset($_COOKIE['wpscPROaff']) || is_numeric($_COOKIE['wpscPROaff'])) { // More affiliate code
                        $wpdb->query( "INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '0.00', 'affiliatepayment', '{$lastID}');");
                    }
                    $keytoedit = $lastID;

                    // Specify any custom value, here we send the primkey of the order record
                    $paymentGatewayOptions['invoice'] = $lastID;
                    $paymentGatewayOptions['theCartPrice'] = $paymentGatewayOptions['theCartPrice'] + $paymentGatewayOptions['totalShipping'];

                    //
                    $cart->empty_cart();
                    $paymentGatewayOptions['path']=WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/';
                    $paymentGatewayOptions['shortpath']=WP_PLUGIN_DIR.'/wpsc-payments-pro/';
                    include_once(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php');
                }

                if($paymentGateway == 'paypal') {
                    // PAYPAL COUNT STARTS AT ONE INSTEAD OF ZERO
                    // Include the paypal library
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/php/payment/Paypal.php');


                    // Create an instance of the paypal library
                    $myPaypal = new Paypal();

                    // Specify your paypal email
                    $myPaypal->addField('business', $devOptions['paypalemail']);

                    // Specify the currency
                    $myPaypal->addField('currency_code', $devOptions['currency_code']);

                    $myPaypal->addField('rm', '2'); // Return method = POST

                    
                    $myPaypal->addField('upload', '1');

                    // Specify the url where paypal will send the user on success/failure
                    $myPaypal->addField('return', WP_PLUGIN_URL.'/wpstorecart/php/payment/paypal_success.php');
                    $myPaypal->addField('cancel_return', WP_PLUGIN_URL.'/wpstorecart/php/payment/paypal_failure.php');

                    // Specify the url where paypal will send the IPN
                    $myPaypal->addField('notify_url', WP_PLUGIN_URL.'/wpstorecart/php/payment/paypal_ipn.php');

                    // Enable test mode if needed
                    if($devOptions['paypaltestmode']=='true') {
                            $myPaypal->enableTestMode();
                    }

                    $paypal_count = 1;
                    $items_query_string;
                    $cartContents = '';
                    $totalPrice = 0;
                    $totalShipping = 0;
                    $couponset = false;
                    $donation = false;
                    foreach ($cart->get_contents() as $item) {
                            // BUILD THE QUERY STRING
                            // Specify the product information
                            // Put the coupon coding here too
                            $myPaypal->addField('item_name_' . $paypal_count, $item['name']);
                            $myPaypal->addField('amount_' . $paypal_count, $item['price']);
                            $myPaypal->addField('item_number_' . $paypal_count, $paypal_count);
                            $myPaypal->addField('quantity_' . $paypal_count, $item['qty']);


                            // Implement shipping here if needed
                            $table_name = $wpdb->prefix . "wpstorecart_products";
                            $results = $wpdb->get_results( "SELECT `shipping`, `donation` FROM {$table_name} WHERE `primkey`={$item['id']} LIMIT 0, 1;", ARRAY_A );
                            if(isset($results)) {
                                if($results[0]['donation']=='1') {
                                    $donation = true;
                                }
                                if(($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                    if($results[0]['shipping']!='0.00') {
                                        $myPaypal->addField('shipping_' . $paypal_count, round($results[0]['shipping'] * $item['qty'],2));
                                        $totalShipping = $totalShipping + round($results[0]['shipping'] * $item['qty'], 2);
                                    }
                                } else {
                                    $totalShipping = 0;
                                }
                            }
                            if($devOptions['flatrateshipping']=='all_global' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $totalShipping = $devOptions['flatrateamount'];
                                $myPaypal->addField('shipping_' . $paypal_count, round($totalShipping, 2));
                            }
                            if($devOptions['flatrateshipping']=='all_single' && ($shipping_type=='shipping_offered_by_flatrate' || $shipping_type_widget=='shipping_offered_by_flatrate')) {
                                $totalShipping = round($devOptions['flatrateamount'] * $item['qty'], 2);
                                $myPaypal->addField('shipping_' . $paypal_count, round($totalShipping, 2));
                            }


                            // Check for a coupon
                            if(@!isset($_SESSION)) {
                                    @session_start();
                            }
                            if(@$_SESSION['validcouponid']==$item['id']) {
                                @$myPaypal->addField('discount_amount_cart', $_SESSION['validcouponamount']);
                                $couponset = true;
                            }


                            $cartContents = $cartContents . $item['id'] .'*'.$item['qty'].',';
                            $totalPrice = $totalPrice + ($item['price'] * $item['qty']);

                            // INCREMENT THE COUNTER
                            ++$paypal_count;
                    }

                    if($shipping_type=='shipping_offered_by_usps' || $shipping_type_widget=='shipping_offered_by_usps') {
                        $totalShipping = $usps_shipping_total; // We use the calculated USPS shipping total if applicable
                        $myPaypal->addField('shipping_1', round($totalShipping, 2));
                    }

                    if($donation==true) {
                        $myPaypal->addField('cmd', '_donations');
                    } else {
                        $myPaypal->addField('cmd', '_cart');
                    }
                    

                    if(@isset($_SESSION['validcouponamount']) && $couponset==false) {
                        @$myPaypal->addField('discount_amount_cart', $_SESSION['validcouponamount']);
                        $couponset = true;
                    }

                    $cartContents = $cartContents . '0*0';

                    // Insert the order into the database
                    $table_name = $wpdb->prefix . "wpstorecart_orders";
                    $timestamp = date('Ymd');
                    if(!isset($_COOKIE['wpscPROaff']) || !is_numeric($_COOKIE['wpscPROaff'])) {
                        $affiliateid = 0;
                    } else {
                        $affiliateid = $_COOKIE['wpscPROaff'];
                        //setcookie ("wpscPROaff", "", time() - 3600); // Remove the affiliate ID
                    }
                    $insert = "
                    INSERT INTO `{$table_name}`
                    (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`,
                    `wpuser`, `email`, `affiliate`, `date`) VALUES
                    (NULL, 'Pending', '{$cartContents}', 'PayPal', '{$totalPrice}', '{$totalShipping}', '{$purchaser_user_id}', '{$purchaser_email}', '{$affiliateid}', '{$timestamp}');
                    ";

                    $results = $wpdb->query( $insert );
                    $lastID = $wpdb->insert_id;
                    if(isset($_COOKIE['wpscPROaff']) || is_numeric($_COOKIE['wpscPROaff'])) { // More affiliate code
                        $wpdb->query( "INSERT INTO `{$wpdb->prefix}wpstorecart_meta` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '0.00', 'affiliatepayment', '{$lastID}');");
                    }
                    $keytoedit = $lastID;

                    // Specify any custom value, here we send the primkey of the order record
                    $myPaypal->addField('custom', $lastID);

                    //

                    if(isset($wpsc['paypal_id']))
                            {
                            // REDIRECT TO PAYPAL WITH MERCHANT ID AND CART CONTENTS
                            // Let's start the train!
                            echo '<center><img src="../../../images/redirect.gif" alt="redirecting" />';
                            $myPaypal->submitPayment();
                            //$myPaypal->echoFields(); // Uncomment this and comment the line above if you need to diagnose a problem with the paypal payment gateway
                            //if(session_id()){echo "Session started!";}else{echo "Session not started!";} // To see if sessions are started here, uncomment this and comment the line that is 2 above this one, which reads: $myPaypal->submitPayment();
                            //print_r ($_SESSION); print_r ($cart); // To see what's in the cart and sessions

                            echo '</center>';
                            // EMPTY THE CART
                            $cart->empty_cart();
                            
                            exit;
                            }
                    else
                            // THE USER HAS NOT CONFIGURED A PAYPAL ID
                            // DISPLAY THE PAYPAL URL WITH AN ERROR MESSAGE
                            {
                            echo 'PayPal integration requires a secure merchant ID.  Please add your email address to the wpStoreCart options page.<br /><br />';

                            exit;
                            }
                    }
                } // End PayPal code
	}

?>