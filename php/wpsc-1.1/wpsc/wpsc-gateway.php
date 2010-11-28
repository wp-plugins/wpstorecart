<?php

// wpsc library, v1.1a by wpStoreCart.com
// Forked from jcart v1.1 http://conceptlogic.com/jcart/

// THIS FILE IS CALLED WHEN ANY BUTTON ON THE CHECKOUT PAGE (PAYPAL CHECKOUT, UPDATE, OR EMPTY) IS CLICKED
// WE CAN ONLY DEFINE ONE FORM ACTION, SO THIS FILE ALLOWS US TO FORK THE FORM SUBMISSION DEPENDING ON WHICH BUTTON WAS CLICKED
// ALSO ALLOWS US TO VERIFY PRICES BEFORE SUBMITTING TO PAYPAL

// INCLUDE wpsc BEFORE SESSION START

//error_reporting(E_ALL);
error_reporting(0);
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
$cart =& $_SESSION['wpsc']; if(!is_object($cart)) $cart = new wpsc();

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

// THE VISITOR HAS CLICKED THE PAYPAL CHECKOUT BUTTON
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

                global $current_user, $wpdb, $paymentGateway, $paymentGatewayOptions;
                get_currentuserinfo();

                $paymentGateway = 'checkmoneyorder';

                if(@isset($_POST['paymentGateway'])) {
                    $paymentGateway = $_POST['paymentGateway'];
                }

                if($paymentGateway == 'checkmoneyorder') {
                    $cartContents = '';
                    $totalPrice = 0;
                    $totalShipping = 0;
                    $amountToSubtractFromCart = 0;
                    foreach ($cart->get_contents() as $item) {
                            // Implement shipping here if needed
                            $table_name = $wpdb->prefix . "wpstorecart_products";
                            $results = $wpdb->get_results( "SELECT `shipping` FROM {$table_name} WHERE `primkey`={$item['id']} LIMIT 0, 1;", ARRAY_A );
                            if(isset($results)) {
                                if($results[0]['shipping']!='0.00') {
                                    $totalShipping = $totalShipping + round($results[0]['shipping'] * $item['qty'], 2);
                                }
                            }

                            // Check for a coupon
                            if(@!isset($_SESSION)) {
                                    @session_start();
                            }
                            if(@$_SESSION['validcouponid']==$item['id']) {
                                @$amountToSubtractFromCart  = $_SESSION['validcouponamount'];
                            }


                            $cartContents = $cartContents . $item['id'] .'*'.$item['qty'].',';
                            $totalPrice = $totalPrice + $item['price'];

                    }

                    $totalPrice = $totalPrice - $amountToSubtractFromCart; // Apply the coupon
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
                    (NULL, 'Pending', '{$cartContents}', 'Check/Money Order', '{$totalPrice}', '{$totalShipping}', '{$current_user->ID}', '{$current_user->user_email}', '{$affiliateid}', '{$timestamp}');
                    ";
                }
                
                if($paymentGateway == 'authorize.net') {
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/php/payment/Authorize.php');
                    $paymentGatewayOptions['ipn'] = WP_PLUGIN_URL.'/wpstorecart/php/payment/authorize_ipn.php';
                    $paymentGatewayOptions['success'] = WP_PLUGIN_URL.'/wpstorecart/php/payment/authorize_success.php';
                    $paymentGatewayOptions['authorizenettestmode'] = $devOptions['authorizenettestmode'];
                    $paymentGatewayOptions['authorizenetemail'] = $devOptions['authorizenetemail'];
                    $paymentGatewayOptions['authorizenetsecretkey'] = $devOptions['authorizenetsecretkey'];
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/payments.pro.php');
                }

                if($paymentGateway == '2co') {
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/php/payment/TwoCo.php');
                    $paymentGatewayOptions['ipn'] = WP_PLUGIN_URL.'/wpstorecart/php/payment/2co_ipn.php';
                    $paymentGatewayOptions['2checkouttestmode'] = $devOptions['2checkouttestmode'];
                    $paymentGatewayOptions['2checkoutemail'] = $devOptions['2checkoutemail'];
                    include_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/payments.pro.php');
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

                    $myPaypal->addField('cmd', '_cart');
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
                            $results = $wpdb->get_results( "SELECT `shipping` FROM {$table_name} WHERE `primkey`={$item['id']} LIMIT 0, 1;", ARRAY_A );
                            if(isset($results)) {
                                if($results[0]['shipping']!='0.00') {
                                    $myPaypal->addField('shipping_' . $paypal_count, round($results[0]['shipping'] * $item['qty'],2));
                                    $totalShipping = $totalShipping + round($results[0]['shipping'] * $item['qty'], 2);
                                }
                            }

                            // Check for a coupon
                            if(@!isset($_SESSION)) {
                                    @session_start();
                            }
                            if(@$_SESSION['validcouponid']==$item['id']) {
                                @$myPaypal->addField('discount_amount_cart', $_SESSION['validcouponamount']);
                            }


                            $cartContents = $cartContents . $item['id'] .'*'.$item['qty'].',';
                            $totalPrice = $totalPrice + $item['price'];

                            // INCREMENT THE COUNTER
                            ++$paypal_count;
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
                    (NULL, 'Pending', '{$cartContents}', 'PayPal', '{$totalPrice}', '{$totalShipping}', '{$current_user->ID}', '{$current_user->user_email}', '{$affiliateid}', '{$timestamp}');
                    ";

                    $results = $wpdb->query( $insert );
                    $lastID = $wpdb->insert_id;
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