<?php

/**********************************************************************

wpStoreCart

With parts based on:
jcart v1.1
http://conceptlogic.com/jcart/

Which was based on Webforce Cart v.1.5
(c) 2004-2005 Webforce Ltd, NZ
http://www.webforce.co.nz/cart/

**********************************************************************/

global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}



if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}
global $wpStoreCart, $devOptions;

if(isset($wpStoreCart)) {
	$devOptions = $wpStoreCart->getAdminOptions();
} else {
	exit();
}



global $wpsc;
// USER CONFIG
require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');

// DEFAULT CONFIG VALUES
require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');




// wpsc
class wpsc {
	var $total = 0;
	var $itemcount = 0;
	var $items = array();
	var $itemprices = array();
	var $itemqtys = array();
	var $itemname = array();
        var $itemshipping = array(); // Added in wpStoreCart 2.2.0

	// CONSTRUCTOR FUNCTION
	function cart() {}


	
	// GET CART CONTENTS
	function get_contents()
		{


		$items = array();
		foreach($this->items as $tmp_item)
			{
			$item = FALSE;
                        $thisTime = false;
 
			$item['id'] = $tmp_item;
			$item['qty'] = $this->itemqtys[$tmp_item];
                        
                        $item['price'] = $this->itemprices[$tmp_item];

			$item['name'] = $this->itemname[$tmp_item];
			
                        $item['shipping'] = $this->itemshipping[$tmp_item]; // Added in wpStoreCart 2.2.0
                        
                        $item['subtotal'] = $item['qty'] * $item['price'];
                        
			$items[] = $item;
			}
		return $items;
		}


	// ADD AN ITEM
	function add_item($item_id, $item_qty=1, $item_price=0, $item_name='', $item_shipping=0)
		{
		// VALIDATION
		$valid_item_qty = $valid_item_price = false;

		// IF THE ITEM QTY IS AN INTEGER, OR ZERO
		if (preg_match("/^[0-9-]+$/i", $item_qty))
			{
			$valid_item_qty = true;
			}
		// IF THE ITEM PRICE IS A FLOATING POINT NUMBER
		if (is_numeric($item_price))
			{
			$valid_item_price = true;
			}

		// ADD THE ITEM
		if ($valid_item_qty !== false && $valid_item_price !== false)
			{
			// IF THE ITEM IS ALREADY IN THE CART, INCREASE THE QTY
			if(@$this->itemqtys[$item_id] > 0)
				{
				$this->itemqtys[$item_id] = $item_qty + $this->itemqtys[$item_id];
				$this->_update_total();
				}
			// THIS IS A NEW ITEM
			else
				{
				$this->items[] = $item_id;
				$this->itemqtys[$item_id] = $item_qty;
				$this->itemprices[$item_id] = $item_price;
				$this->itemname[$item_id] = $item_name;
                                $this->itemshipping[$item_id] = $item_shipping; 
				
				}
			$this->_update_total();
			return true;
			}

		else if	($valid_item_qty !== true)
			{
			$error_type = 'qty';
			return $error_type;
			}
		else if	($valid_item_price !== true)
			{
			$error_type = 'price';
			return $error_type;
			}
		}

	// COUPON CODE, returns the discounted price if any
	function update_coupon($item_id) {
		// Coupon main code
		$discount_price = 0;
		$discount_percent = 0;	
		if(@!isset($_SESSION)) {
			@session_start();
		}
		
		if(@isset($_POST['ccoupon'])) {
			global $wpdb;

			
			/*if (@isset($_SESSION['validcoupon'])) {
				$thecoupon = $_SESSION['validcoupon'];
			} else
			*/
			if (@isset($_POST['ccoupon'])) {
				$thecoupon = $_POST['ccoupon'];
			}			
			$table_name = $wpdb->prefix . "wpstorecart_coupons";
			// Try adding the coupon hooks here
			$grabrecord = "SELECT * FROM {$table_name} WHERE `startdate` < ".date("Ymd")." AND `enddate` > ".date("Ymd")." AND `code`='{$thecoupon}' AND `product`={$item_id};";					
			//echo $grabrecord;
			//exit;
			

			$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
			if(isset($results)) {
				@$_SESSION['validcoupon'] = $_POST['ccoupon'];
				
				foreach ($results as $result) {
					$discount_price = $result['amount'];
					$discount_percent = $result['percent'];
					
					@$_SESSION['validcouponid'] = $result['product'];
                                        @$_SESSION['validcouponamount'] = $result['amount'];
                                        @$_SESSION['validcouponpercent'] = $result['percent'];
                                        
				}

			}

		}


		return $discount_price;
	}

	// UPDATE AN ITEM
	function update_item($item_id, $item_qty)
		{

		// IF THE ITEM QTY IS AN INTEGER, OR ZERO
		// UPDATE THE ITEM
		if (preg_match("/^[0-9-]+$/i", $item_qty))
			{
			if($item_qty < 1)
				{
				$this->del_item($item_id);
				}
			else
				{
				$this->itemqtys[$item_id] = $item_qty;
				}
			$this->_update_total();
			return true;
			}
		}


	// UPDATE THE ENTIRE CART
	// VISITOR MAY CHANGE MULTIPLE FIELDS BEFORE CLICKING UPDATE
	// ONLY USED WHEN JAVASCRIPT IS DISABLED
	// WHEN JAVASCRIPT IS ENABLED, THE CART IS UPDATED ONKEYUP
	function update_cart()
		{
		// POST VALUE IS AN ARRAY OF ALL ITEM IDs IN THE CART
		if(isset($_POST['wpsc_item_ids'])) {
		if (is_array($_POST['wpsc_item_ids']))
			{
			// TREAT VALUES AS A STRING FOR VALIDATION
			$item_ids = implode($_POST['wpsc_item_ids']);
			}
		}

		// POST VALUE IS AN ARRAY OF ALL ITEM QUANTITIES IN THE CART
		if(isset($_POST['wpsc_item_qty'])) {
		if (is_array($_POST['wpsc_item_qty']))
			{
			// TREAT VALUES AS A STRING FOR VALIDATION
			$item_qtys = implode($_POST['wpsc_item_qty']);
			}
		}
		
		// IF NO ITEM IDs, THE CART IS EMPTY
		if (isset($_POST['wpsc_item_id']))
			{
			// IF THE ITEM QTY IS AN INTEGER, OR ZERO, OR EMPTY
			// UPDATE THE ITEM
			if (preg_match("/^[0-9-]+$/i", $item_qtys) || $item_qtys == '')
				{
				// THE INDEX OF THE ITEM AND ITS QUANTITY IN THEIR RESPECTIVE ARRAYS
				$count = 0;

				// FOR EACH ITEM IN THE CART
				foreach ($_POST['wpsc_item_id'] as $item_id)
					{
					// GET THE ITEM QTY AND DOUBLE-CHECK THAT THE VALUE IS AN INTEGER
					$update_item_qty = intval($_POST['wpsc_item_qty'][$count]);

					if($update_item_qty < 1)
						{
						$this->del_item($item_id);
						}
					else
						{
						// UPDATE THE ITEM
						$this->update_item($item_id, $update_item_qty);
						}

					// INCREMENT INDEX FOR THE NEXT ITEM
					$count++;
					}
				return true;
				}
			}
		// IF NO ITEMS IN THE CART, RETURN TRUE TO PREVENT UNNECSSARY ERROR MESSAGE
		else if (!isset($_POST['wpsc_item_id']))
			{
			//@$_SESSION['validcoupon'] = NULL;
			return true;
			}
		}


	// REMOVE AN ITEM
	/*
	GET VAR COMES FROM A LINK, WITH THE ITEM ID TO BE REMOVED IN ITS QUERY STRING
	AFTER AN ITEM IS REMOVED ITS ID STAYS SET IN THE QUERY STRING, PREVENTING THE SAME ITEM FROM BEING ADDED BACK TO THE CART
	SO WE CHECK TO MAKE SURE ONLY THE GET VAR IS SET, AND NOT THE POST VARS

	USING POST VARS TO REMOVE ITEMS DOESN'T WORK BECAUSE WE HAVE TO PASS THE ID OF THE ITEM TO BE REMOVED AS THE VALUE OF THE BUTTON
	IF USING AN INPUT WITH TYPE SUBMIT, ALL BROWSERS DISPLAY THE ITEM ID, INSTEAD OF ALLOWING FOR USER FRIENDLY TEXT SUCH AS 'remove'
	IF USING AN INPUT WITH TYPE IMAGE, INTERNET EXPLORER DOES NOT SUBMIT THE VALUE, ONLY X AND Y COORDINATES WHERE BUTTON WAS CLICKED
	CAN'T USE A HIDDEN INPUT EITHER SINCE THE CART FORM HAS TO ENCOMPASS ALL ITEMS TO RECALCULATE TOTAL WHEN A QUANTITY IS CHANGED, WHICH MEANS THERE ARE MULTIPLE REMOVE BUTTONS AND NO WAY TO ASSOCIATE THEM WITH THE CORRECT HIDDEN INPUT
	*/
	function del_item($item_id)
		{
		$ti = array();
		$this->itemqtys[$item_id] = 0;
		foreach($this->items as $item)
			{
			if($item != $item_id)
				{
				$ti[] = $item;
				}
			}
		$this->items = $ti;
		$this->_update_total();
		}


	// EMPTY THE CART
	function empty_cart()
		{
		$this->total = 0;
		$this->itemcount = 0;
		$this->items = array();
		$this->itemprices = array();
		$this->itemqtys = array();
		$this->itemname = array();
                $this->itemshipping = array();
		}


	// INTERNAL FUNCTION TO RECALCULATE TOTAL
	function _update_total()
		{
		$this->itemcount = 0;
		$this->total = 0;
		if(sizeof($this->items > 0))
                    {
                        $couponUsed = false;
			foreach($this->items as $item)
				{
				if($couponUsed == true) {
                                    $this->total = $this->total + ($this->itemprices[$item] * $this->itemqtys[$item]);
                                } else {
                                    $this->total = $this->total + (($this->itemprices[$item] * $this->itemqtys[$item]) - $this->update_coupon($item));
                                    if((($this->itemprices[$item] * $this->itemqtys[$item]) - $this->update_coupon($item))!= ($this->itemprices[$item] * $this->itemqtys[$item])){
                                        $couponUsed = true;
                                    }
                                }

				// TOTAL ITEMS IN CART (ORIGINAL wfCart COUNTED TOTAL NUMBER OF LINE ITEMS)
				$this->itemcount += $this->itemqtys[$item];
				}
			}
		}


	// PROCESS AND DISPLAY CART
	function display_cart($wpsc, $hidden=false)
		{
		global $wpsc, $is_checkout, $devOptions, $wpscCarthasBeenCalled, $wpscWidgetSettings, $wpStoreCart, $wpscIsCheckoutPage, $wpdb;
		// wpsc ARRAY HOLDS USER CONFIG SETTINGS
		extract($wpsc);

                // We use this global variable to fix a bug
                $wpscCarthasBeenCalled = true;
                

		// ASSIGN USER CONFIG VALUES AS POST VAR LITERAL INDICES
		// INDICES ARE THE HTML NAME ATTRIBUTES FROM THE USERS ADD-TO-CART FORM
		@$item_id = $_POST[$item_id];
		@$item_qty = $_POST[$item_qty];
		@$item_price = $_POST[$item_price];
		@$item_name = $_POST[$item_name];
                @$item_shipping = $_POST[$item_shipping];

		// ADD AN ITEM
		if (isset($_POST[$item_add]))
			{
			$item_added = $this->add_item($item_id, $item_qty, $item_price, $item_name, $item_shipping);
			// IF NOT TRUE THE ADD ITEM FUNCTION RETURNS THE ERROR TYPE
			if ($item_added !== true)
				{
				$error_type = $item_added;
				switch($error_type)
					{
					case 'qty':
						$error_message = $text['quantity_error'];
						break;
					case 'price':
						$error_message = $text['price_error'];
						break;
					}
				}
			}

		// UPDATE A SINGLE ITEM
		// CHECKING POST VALUE AGAINST $text ARRAY FAILS?? HAVE TO CHECK AGAINST $wpsc ARRAY
		if (isset($_POST['wpsc_update_item']) && isset($wpsc['text']['update_button'])) {
		if ($_POST['wpsc_update_item'] == $wpsc['text']['update_button'])
			{
			$item_updated = $this->update_item($_POST['item_id'], $_POST['item_qty']);
			if ($item_updated !== true)
				{
				$error_message = $text['quantity_error'];
				}
			}
		}

		// UPDATE ALL ITEMS IN THE CART
		if(isset($_POST['wpsc_update_cart']) || isset($_POST['wpsc_checkout']))
			{
			$cart_updated = $this->update_cart();
			if ($cart_updated !== true)
				{
				$error_message = $text['quantity_error'];
				}
			}

		// REMOVE AN ITEM
		if(isset($_GET['wpsc_remove']) && !isset($_POST[$item_add]) && !isset($_POST['wpsc_update_cart']) && !isset($_POST['wpsc_check_out']))
			{
			$this->del_item($_GET['wpsc_remove']);
			}

		// EMPTY THE CART
		if(isset($_POST['wpsc_empty']))
			{
                        $totalshipping = 0;
			$this->empty_cart();
			}

		// DETERMINE WHICH TEXT TO USE FOR THE NUMBER OF ITEMS IN THE CART
		if ($this->itemcount >= 0)
			{
			$text['items_in_cart'] = $text['multiple_items'];
			}
		if ($this->itemcount == 1)
			{
			$text['items_in_cart'] = $text['single_item'];
			}

		// DETERMINE IF THIS IS THE CHECKOUT PAGE
		// WE FIRST CHECK THE REQUEST URI AGAINST THE USER CONFIG CHECKOUT (SET WHEN THE VISITOR FIRST CLICKS CHECKOUT)
		// WE ALSO CHECK FOR THE REQUEST VAR SENT FROM HIDDEN INPUT SENT BY AJAX REQUEST (SET WHEN VISITOR HAS JAVASCRIPT ENABLED AND UPDATES AN ITEM QTY)
		if(!isset($is_checkout)) {
			$is_checkout = strpos($_SERVER['REQUEST_URI'], $form_action);
		}
		if(!isset($is_checkout) && isset($_REQUEST['wpsc_is_checkout'])) {
			if ($is_checkout !== false || $_REQUEST['wpsc_is_checkout'] == 'true')
				{
				$is_checkout = true;
				}
			else
				{
				$is_checkout = false;
				}
		}
		// OVERWRITE THE CONFIG FORM ACTION TO POST TO wpsc-gateway.php INSTEAD OF POSTING BACK TO CHECKOUT PAGE
		// THIS ALSO ALLOWS US TO VALIDATE PRICES BEFORE SENDING CART CONTENTS TO PAYPAL
		if ($is_checkout == true)
			{
			$form_action = $path . 'wpsc-gateway.php';
			}

		// DEFAULT INPUT TYPE
		// CAN BE OVERRIDDEN IF USER SETS PATHS FOR BUTTON IMAGES
		$input_type = 'submit';

		// IF THIS ERROR IS TRUE THE VISITOR UPDATED THE CART FROM THE CHECKOUT PAGE USING AN INVALID PRICE FORMAT
		// PASSED AS A SESSION VAR SINCE THE CHECKOUT PAGE USES A HEADER REDIRECT
		// IF PASSED VIA GET THE QUERY STRING STAYS SET EVEN AFTER SUBSEQUENT POST REQUESTS
		if (isset($_SESSION['quantity_error'])) {
			if ($_SESSION['quantity_error'] == true)
				{
				$error_message = $text['quantity_error'];
				unset($_SESSION['quantity_error']);
				}
		}
		// OUTPUT THE CART

		// IF THERE'S AN ERROR MESSAGE WRAP IT IN SOME HTML
		if (isset($error_message))
			{
			$error_message = "<p class='wpsc-error'>$error_message</p>";
			}

		// DISPLAY THE CART HEADER
                        if($hidden==false) {
                            if($wpscIsCheckoutPage==true) {
                                echo "<!-- BEGIN wpsc -->\n<div id='wpsc"; if(isset($wpscWidgetSettings)) {echo '-widget';} echo"' class='wpsc-checkout-page-contents'>\n";
                            } else {
                                echo "<!-- BEGIN wpsc -->\n<div id='wpsc"; if(isset($wpscWidgetSettings)) {echo '-widget';} echo"'>\n";
                            }
                        } else {
                            echo '<!-- BEGIN wpsc -->
                                <div id="wpsc" style="display:none;">
                                ';
                        }
		if (isset($error_message)) {
			echo "\t$error_message\n";
		}

                $isLoggedIn = NULL;
                if ($is_checkout == true) {
                    if ( is_user_logged_in() ) {
                        $isLoggedIn = true;
                    } else {


                        // ** Here's where we disable the user login system during checkout if registration is not required
                        if($devOptions['requireregistration']=='false') {
                            if(@isset($_POST['guest_email'])) {
                                $_SESSION['wpsc_email'] = $wpdb->escape($_POST['guest_email']);
                            }
                            if(@isset($_SESSION['wpsc_email'])) {
                                $isLoggedIn = true;
                            } else {
                                echo '
                                    <form name="wpsc-registerform" id="wpsc-guestcheckoutform" action="#" method="post">
                                        <br /><strong>'. $text['guestcheckout'] .'</strong><br />
                                        <label><span>'. $text['email'] .' <ins><div class="wpsc-required-symbol">'.$text['required_symbol'].'</div></ins></span><input type="text" name="guest_email" value="'.$_SESSION['wpsc_email'].'" /></label>
                                        <input type="submit" value="'. $text['checkout_button'] .'" class="wpsc-button wpsc-checkout" />
                                    </form>
                                    <br />
                                    ';
                                $isLoggedIn = false;

                            }
                        } else {
                           $isLoggedIn = false;
                        }
                        
                     // Only shown if the user is not logged in
                     if($isLoggedIn == false) {
                            /*
                             * Show error messages, then remove the wpscregerror from the URI
                             * @todo Add these into the language options of wpStoreCart
                             */
                            $servrequest_uri = $_SERVER['REQUEST_URI'] ;
                            if(@isset($_GET['wpscregerror'])) {
                                if($_GET['wpscregerror']=='1') {
                                    echo '<div class="wpscerror">'. $text['username'] .' taken.</div>';
                                    $servrequest_uri = str_replace("&wpscregerror=1", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=1", "", $servrequest_uri);
                                }
                                if($_GET['wpscregerror']=='2') {
                                    echo '<div class="wpscerror">'. $text['username'] .' invalid.</div>';
                                    $servrequest_uri = str_replace("&wpscregerror=2", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=2", "", $servrequest_uri);
                                }
                                if($_GET['wpscregerror']=='3') {
                                   echo '<div class="wpscerror">'. $text['email'] .' is invalid.</div>';
                                    $servrequest_uri = str_replace("&wpscregerror=3", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=3", "", $servrequest_uri);
                                }
                                if($_GET['wpscregerror']=='4') {
                                    $servrequest_uri = str_replace("&wpscregerror=4", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=4", "", $servrequest_uri);
                                    echo '<div class="wpscerror">'. $text['email'] .' is already registered.</div>';
                                }
                                if($_GET['wpscregerror']=='5') {
                                    $servrequest_uri = str_replace("&wpscregerror=5", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=5", "", $servrequest_uri);
                                    echo '<div class="wpscerror">Wordpress could not create the account, alert the admin to enable registrations.</div>';
                                }
                                if($_GET['wpscregerror']=='6') {
                                    $servrequest_uri = str_replace("&wpscregerror=6", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=6", "", $servrequest_uri);
                                    echo '<div class="wpscerror">Not all of the required fields were filled out.  Please fill out all the required information and try again.</div>';
                                }
                            }

                            echo '
                            <form name="wpsc-loginform" id="wpsc-loginform" method="post" action="'. wp_login_url( get_permalink() ) .'">
                                <br /><strong>'. $text['login'] .'</strong><br />
                                        <label><span>'. $text['username'] .' </span><input type="text" value="" name="log" /></label>
                                        <label><span>'. $text['password'] .' </span><input type="password" value="" name="pwd"  /></label>
                                        <input type="submit" value="'. $text['login'] .'" class="wpsc-button wpsc-login-button" />
                                        <input type="hidden" name="redirect_to" value="'.$servrequest_uri.'" />
                            </form>
                            <br />
                            <form name="wpsc-registerform" id="wpsc-registerform" action="'.plugins_url('/wpstorecart/php/register.php').'" method="post">
                                <br /><strong>'. $text['register'] .'</strong><br />
                                            <label><span>'. $text['email'] .' <ins><div class="wpsc-required-symbol">'.$text['required_symbol'].'</div></ins></span><input type="text" name="email" value="'.$_SESSION['wpsc_email'].'" /></label>
                                            <label><span>'. $text['password'] .'<ins><div class="wpsc-required-symbol">'.$text['required_symbol'].'</div></ins></span><input type="password" name="user_pass" value="'.$_SESSION['wpsc_password'].'" /></label>';

                                            $wpStoreCart->show_custom_reg_fields();

                            echo '          <input type="hidden" name="redirect_to" value="'.$servrequest_uri.'" />
                                            <label><span class="wpsc-required-help">'.$text['required_help'].'</span><input type="submit" name="wp-submit" value="'. $text['register'] .'" class="wpsc-button wpsc-register-button" /></label>
                            </form>
                            <br />
                            ';
                        }
                    }
                }

                if( $isLoggedIn == true || $is_checkout==false) {


                    echo "\t<form method='post' action='$form_action'>\n";

                    echo "\t\t\t\t\t\t<strong id='wpsc-title'>" . $text['cart_title'] . "</strong> (" . $this->itemcount . "&nbsp;" . $text['items_in_cart'] .")<br />\n";


                    // IF ANY ITEMS IN THE CART
                    if($this->itemcount > 0)
                            {

                            $totalshipping = 0; // set shipping to zero
                            $shipping_needs_calculation = false; // By default we'll assume shipping doesn't need calculations
                            $shipping_offered_by_flatrate = true; // By default, we'll assume we can use flatrate shipping
                            $shipping_offered_by_usps = true; // By default, we'll assume we can use USPS shipping
                            $shipping_offered_by_ups = true; // By default, we'll assume we can use UPS shipping
                            $shipping_offered_by_fedex = true; // By default, we'll assume we can't use FedEx shipping
                            $table_name_meta = $wpdb->prefix . "wpstorecart_meta";

                            if($devOptions['enableusps']=='false' || $devOptions['storetype']=='Digital Goods Only') {
                                $shipping_offered_by_usps = false;
                            }
                            if($devOptions['enableups']=='false' || $devOptions['storetype']=='Digital Goods Only') {
                                $shipping_offered_by_ups = false;
                            }
                            if($devOptions['enablefedex']=='false' || $devOptions['storetype']=='Digital Goods Only') {
                                $shipping_offered_by_fedex = false;
                            }
                            if($devOptions['flatrateshipping']=='off' || $devOptions['storetype']=='Digital Goods Only') {
                                $shipping_offered_by_flatrate = false;
                            }

                            // DISPLAY LINE ITEMS
                            foreach($this->get_contents() as $item)
                                    {

                                    $newsplit = explode('-', $item['id'] );
                                    $item['id'] = $newsplit[0];

                                    if($shipping_offered_by_flatrate) {
                                        $results_flatrateshipping = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='wpsc_product_flatrateshipping' AND `foreignkey`={$item['id']};", ARRAY_N);
                                        if(@$results_flatrateshipping[0][0]=='yes'){$shipping_offered_by_flatrate = true;} else {$shipping_offered_by_flatrate = false;}
                                    }
                                    if($shipping_offered_by_usps) {
                                        $results_usps = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='wpsc_product_usps' AND `foreignkey`={$item['id']};", ARRAY_N);
                                        if(@$results_usps[0][0]=='yes'){$shipping_offered_by_usps = true;} else {$shipping_offered_by_usps = false;}
                                    }
                                    if($shipping_offered_by_ups) {
                                        $results_ups = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='wpsc_product_ups' AND `foreignkey`={$item['id']};", ARRAY_N);
                                        if(@$results_ups[0][0]=='yes'){$shipping_offered_by_ups = true;} else {$shipping_offered_by_ups = false;}
                                    }

                                    if($shipping_offered_by_fedex) {
                                        $results_fedex = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='wpsc_product_fedex' AND `foreignkey`={$item['id']};", ARRAY_N);
                                        if(@$results_fedex[0][0]=='yes'){$shipping_offered_by_fedex = true;} else {$shipping_offered_by_fedex = false;}
                                    }

                                    // If flat rate is disabled and any of the other shipping services are enabled, then we need to calculate shipping
                                    if(($shipping_offered_by_flatrate==false || $results_flatrateshipping[0][0]=='no') && (($results_usps || $results_usps[0][0]=='yes') || ($results_ups || $results_ups[0][0]='yes') || ($results_fedex || $results_fedex[0][0]=='yes'))) {
                                        $shipping_needs_calculation = true;
                                    }


                                    $totalshipping = $totalshipping + ($item['shipping'] * $item['qty']); // Added in 2.2

                                    echo "\t\t\t\t\t\t<input type='text' size='2' id='wpsc-item-id-" . $item['id'] . "' name='wpsc_item_qty[ ]' value='" . $item['qty'] . "' />\n";

                                    echo "\t\t\t\t\t\t" . $item['name'] . "<input type='hidden' name='wpsc_item_name[ ]' value='" . $item['name'] . "' />\n";
                                    echo "\t\t\t\t\t\t<input type='hidden' name='wpsc_item_id[ ]' value='" . $item['id'] . "' />\n";

                                    if(@!isset($_SESSION)) {
                                            @session_start();
                                    }

                                    $finalAmount = number_format($item['subtotal'], 2);
                                    $newAmount = number_format($item['subtotal'] - $this->update_coupon($item['id']),2);


                                    if ($newAmount != $finalAmount) {

                                            //if(number_format($item['subtotal'],2) != (number_format(($item['subtotal'] - $this->update_coupon($item['id'])),2) )){
                                            $tempAmount = '<strike>'.number_format($item['subtotal'],2).'</strike> '. $newAmount;
                                            $finalAmount = $tempAmount;
                                    }
                                    echo "\t\t\t\t\t\t<span>" . $text['currency_symbol'] . $finalAmount . "</span><input type='hidden' name='wpsc_item_price[ ]' value='" . $item['price'] . "' />\n";
                                    echo "\t\t\t\t\t\t<a class='wpsc-remove' href='?wpsc_remove=" . $item['id'] . "'>" . $text['remove_link'] . "</a><br />\n";
                                    //echo "\t\t\t\t\t</td>\n";
                                    //echo "\t\t\t\t</tr>\n";
                                    }
                                    $cart_is_empty = false;
                            }

                    // THE CART IS EMPTY
                    else
                            {
                            //echo "\t\t\t\t<tr><td colspan='3' class='empty'>" . $text['empty_message'] . "</td></tr>\n";
                            echo "\t\t\t\t" . $text['empty_message'] . "\n<br />";
                            $cart_is_empty = true;
                            }

                    // DISPLAY THE CART FOOTER
                    //echo "\t\t\t\t<tr>\n";
                    //echo "\t\t\t\t\t<th id='wpsc-footer' colspan='3'>\n";

                    // IF THIS IS THE CHECKOUT HIDE THE CART CHECKOUT BUTTON
                    if(!isset($src)) {
                            $src = NULL;
                    }
                    if ($is_checkout !== true) {
                        if ($button['checkout']) { $input_type = 'image'; $src = ' src="' . $button['checkout'] . '" alt="' . $text['checkout_button'] . '" title="" ';	}

                        echo "\t\t\t\t\t\t<input type='" . $input_type . "' " . $src . "id='wpsc-checkout' name='wpsc_checkout' class='ui-state-default ui-corner-all wpsc-button wpsc-checkout' value='" . $text['checkout_button'] . "' /><br />\n";
                    }

                    if ($is_checkout == true && $devOptions['enablecoupons']=='true') {
                            //echo "<tr><th id='wpsc-footer' colspan='3'>Enter Coupon:<input type=\"text\" value=\"\" name=\"\" /></th></tr>";
                            if(@isset($_SESSION['validcoupon'])) {
                                    echo "<div id='wpsc-footer' colspan='3'>{$text['enter_coupon']}<input type=\"text\" value=\"{$_SESSION['validcoupon']}\" name=\"ccoupon\" /></div>";
                            } else {
                                    echo "<div id='wpsc-footer' colspan='3'>{$text['enter_coupon']}<input type=\"text\" value=\"\" name=\"ccoupon\" /></div>";
                            }
                    }

                    echo '<br />';

                    if(!isset($totalshipping)) {
                        $totalshipping = 0;
                    }
                    if($devOptions['storetype']=='Digital Goods Only') {
                        // if we're dealing with digital goods, let's put shipping down to 0
                        $totalshipping = 0;
                    }
                    if($devOptions['flatrateshipping']=='all_global' || $devOptions['flatrateshipping']=='all_single') {
                        if($this->itemcount > 0) {
                            if($devOptions['flatrateshipping']=='all_global') {
                                $totalshipping = number_format($devOptions['flatrateamount'], 2);
                            } else {
                                $totalshipping = number_format($devOptions['flatrateamount'] * $this->itemcount, 2);
                            }
                            $shipping_needs_calculation = false;
                        } else {
                            $totalshipping = 0;
                        }
                    }

                    // Disable shipping calculations if all items can be shipped by flatrate, or if it's a digital only store, or if no shipping options are enabled.
                    if($devOptions['storetype']=='Digital Goods Only' || (!$shipping_offered_by_flatrate && !$shipping_offered_by_usps && !$shipping_offered_by_ups && !$shipping_offered_by_fedex)) {
                        $shipping_needs_calculation = false;
                    }

                    if($shipping_needs_calculation == true) {
                        $totalshipping = 0; // We don't know the shipping value yet, so let's return to the original 0 for shipping until we find out.
                    }

                    if($devOptions['storetype']!='Digital Goods Only' && (($devOptions['displayshipping']=='true' && $wpscWidgetSettings['iswidget']!='true')|| $wpscWidgetSettings['widgetShowShipping']=='true') ) {
                        echo '<div id="wpsc-shipping-calculation-form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'">';
                        $firstone = true;
                        if($shipping_offered_by_flatrate) {echo '<input class="wpsc-shipping-form-radio" type="radio" '; if($firstone){echo 'checked="checked" ';} echo 'name="wpsc-shipping-type'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'" value="shipping_offered_by_flatrate" /> Flat rate shipping<br />';$firstone = false;}
                        if($shipping_offered_by_usps) {echo '<input class="wpsc-shipping-form-radio" type="radio" '; if($firstone){echo 'checked="checked" ';} echo 'name="wpsc-shipping-type'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'" value="shipping_offered_by_usps" /> United States Postal Service <div id="wpsc-zipcode'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'"> Zipcode: <input type="text" id="wpsc-zipcode-input'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'" name="wpsc-zipcode-input" /></div><br />';$firstone = false;}
                        if($shipping_offered_by_ups) {echo '<input class="wpsc-shipping-form-radio" type="radio" '; if($firstone){echo 'checked="checked" ';} echo 'name="wpsc-shipping-type'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'" value="shipping_offered_by_ups" /> UPS Shipping<br />';$firstone = false;}
                        if($shipping_offered_by_fedex) {echo '<input class="wpsc-shipping-form-radio" type="radio" '; if($firstone){echo 'checked="checked" ';} echo 'name="wpsc-shipping-type'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'" value="shipping_offered_by_fedex" /> FedEx Shipping<br />';$firstone = false;}
                        if($shipping_offered_by_usps || $shipping_offered_by_ups || $shipping_offered_by_fedex) {
                            echo '<button id="wpsc-calculate-shipping-button'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'">'. $text['calculateshipping'] .'</button>';
                        } else {
                            echo '  <script type="text/javascript">
                                        /* <![CDATA[ */
                                        jQuery(document).ready(function($) {
                                            $("#toggle_shipping_form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").hide();
                                        });
                                        /* ]]> */
                                    </script>';
                        }
                        echo '</div>';
                        echo '  <script type="text/javascript">
                                    /* <![CDATA[ */
                                    jQuery(document).ready(function($) {
                                        $("#wpsc-zipcode'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").toggle();
                                        $("#wpsc-shipping-calculation-form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").toggle();
                                        function wpscCreateShippingForm'; if(isset($wpscWidgetSettings)) {echo 'widget';} echo'() {
                                            if( $("input[@name=wpsc-shipping-type'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo']:checked").val() == "shipping_offered_by_usps") {
                                                $("#wpsc-zipcode'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").show("drop", { direction: "down" }, 1000);
                                            } else {
                                                if($("#wpsc-zipcode'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").is(":hidden")) {
                                                    //
                                                } else {
                                                    $("#wpsc-zipcode'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").hide("drop", { direction: "down" }, 1000);
                                                }
                                            }
                                        }
                                        wpscCreateShippingForm'; if(isset($wpscWidgetSettings)) {echo 'widget';} echo'();
                                        $(".wpsc-shipping-form-radio").click(function() {
                                            wpscCreateShippingForm'; if(isset($wpscWidgetSettings)) {echo 'widget';} echo'();
                                        });

                                        $("#toggle_shipping_form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").click(function() {
                                            $("#wpsc-shipping-calculation-form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").slideToggle("slow");
                                        });

                                        $("#wpsc-calculate-shipping-button'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").click(function() {
                                            $.ajax(
                                            {
                                                type: "POST",
                                                url: "'. plugins_url('/wpstorecart/php/calculateshipping.php').'",
                                                data: "zipcode=" + $(\'#wpsc-zipcode-input'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'\').val(),
                                                dataType: "html",
                                                success: function(data, status)
                                                {
                                                    $("#wpsc-shipping-calculation-form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").hide("explode", 1000);
                                                    $("#wpsc-shipping'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").replaceWith("\t\t\t\t\t\t<span id=\'wpsc-shipping'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'\'>'.$devOptions['shipping'] . ': <strong>' . $devOptions['currency_symbol'] . '"+data+"'. $devOptions['currency_symbol_right'] . '</strong>&nbsp;<img src=\''.plugins_url('/wpstorecart/images/package_go.png').'\' id=\'toggle_shipping_form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'\' alt=\'\' onclick=\'jQuery(\\"#wpsc-shipping-calculation-form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'\\").slideToggle(\\"slow\\");\' /><span>");
                                                    $("#wpsc-shipping'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").show("drop", { direction: "down" }, 1000);';
                                                    if(($devOptions['displaytotal']=='true' && $wpscWidgetSettings['iswidget']!='true') || $wpscWidgetSettings['widgetShowTotal']=='true' ) {
                                                        echo 'var newtotal = Number(data) + Number('.number_format($this->total,2).');
                                                              newtotal = newtotal.toFixed(2);
                                                              $("#wpsc-total'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'").replaceWith("\t\t\t\t\t\t<span id=\'wpsc-total'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'\'>'.$text['total'] . ': <strong>' . $text['currency_symbol'] . '"+newtotal+"' . $text['currency_symbol_right'] .'</strong></span>");';
                                                    }
                                                    echo'
                                                    $(".wpsc-checkmoneyordercheckout").show("drop", { direction: "down" }, 1000);
                                                    $(".wpsc-paypalcheckout").show("drop", { direction: "down" }, 1000);
                                                    $(".wpsc-authorizenetcheckout").show("drop", { direction: "down" }, 1000);
                                                    $(".wpsc-2checkoutcheckout").show("drop", { direction: "down" }, 1000);
                                                    $(".wpsc-libertyreservecheckout").show("drop", { direction: "down" }, 1000);
                                                }
                                            });

                                            return false;
                                        });
                                    });
                                    /* ]]> */
                                </script>';


                        if($shipping_needs_calculation == false ) {
                            echo "\t\t\t\t\t\t<span id='wpsc-shipping"; if(isset($wpscWidgetSettings)) {echo '-widget';} echo"'>" . $text['shipping'] . ": <strong>" . $text['currency_symbol'] . number_format($totalshipping, 2) . $text['currency_symbol_right'] . '</strong>&nbsp;<img src="'.plugins_url('/wpstorecart/images/package_go.png').'" id="toggle_shipping_form'; if(isset($wpscWidgetSettings)) {echo '-widget';} echo'" alt="" /></span><br />';

                        } else {
                            echo "\t\t\t\t\t\t<span id='wpsc-shipping"; if(isset($wpscWidgetSettings)) {echo '-widget';} echo"'>" . $text['shipping'] . ": <strong><a href=\"\" class=\"wpsc-calculate-shipping\" onclick=\"jQuery('#wpsc-shipping-calculation-form"; if(isset($wpscWidgetSettings)) {echo '-widget';} echo"').show('drop', { direction: 'down' }, 1000);jQuery(this).hide('drop', { direction: 'down' }, 1000);jQuery('#wpsc-shipping"; if(isset($wpscWidgetSettings)) {echo '-widget';} echo"').hide('drop', { direction: 'down' }, 1000);return false;\">" . $text['calculateshipping'] . "</a></strong></span><br />\n";
                        }
                    }

                    if(($devOptions['displaysubtotal']=='true' && $wpscWidgetSettings['iswidget']!='true') || $wpscWidgetSettings['widgetShowSubtotal']=='true' ) {
                        echo "\t\t\t\t\t\t<span id='wpsc-subtotal"; if(isset($wpscWidgetSettings)) {echo '-widget';} echo"'>" . $text['subtotal'] . ": <strong>" . $text['currency_symbol'] . number_format($this->total,2) . $text['currency_symbol_right'] ."</strong></span><br />\n";
                    }

                    if(($devOptions['displaytotal']=='true' && $wpscWidgetSettings['iswidget']!='true') || $wpscWidgetSettings['widgetShowTotal']=='true' ) {
                        echo "\t\t\t\t\t\t<span id='wpsc-total"; if(isset($wpscWidgetSettings)) {echo '-widget';} echo"'>" . $text['total'] . ": <strong>" . $text['currency_symbol'] . number_format($this->total + $totalshipping,2) . $text['currency_symbol_right'] ."</strong></span><br />\n";
                    }

                    if(!$cart_is_empty) {
                        if ($button['update']) { $input_type = 'image'; $src = ' src="' . $button['update'] . '" alt="' . $text['update_button'] . '" title="" ';	}
                        echo "\t\t\t\t<input type='" . $input_type . "' " . $src ."name='wpsc_update_cart' value='" . $text['update_button'] . "' class='ui-state-default ui-corner-all wpsc-button wpsc-update ' />\n";
                    }
                    echo "<div class='wpsc-hide'>";
                    if ($is_checkout == false) {
                            if ($button['empty']) { $input_type = 'image'; $src = ' src="' . $button['empty'] . '" alt="' . $text['empty_button'] . '" title="" ';	}
                            echo "\t\t\t\t<input type='" . $input_type . "' " . $src ."name='wpsc_empty' value='" . $text['empty_button'] . "' class='ui-state-default ui-corner-all wpsc-button wpsc-empty' />\n";
                    }

                    echo "</div>";
                    //echo "\t\t</fieldset>\n";

                    // IF THIS IS THE CHECKOUT AND THERE ARE ITEMS IN THE CART THEN DISPLAY CHECKOUT BUTTONS
                    if ($is_checkout == true && !$cart_is_empty) {

                                $servrequest_uri = $_SERVER['REQUEST_URI'] ;

                                // HIDDEN INPUT ALLOWS US TO DETERMINE IF WE'RE ON THE CHECKOUT PAGE
                                // WE NORMALLY CHECK AGAINST REQUEST URI BUT AJAX UPDATE SETS VALUE TO wpsc-relay.php
                                echo "\t\t\t<input type='hidden' id='wpsc-is-checkout' name='wpsc_is_checkout' value='true' />\n";

                                // SEND THE URL OF THE CHECKOUT PAGE TO wpsc-gateway.php
                                // WHEN JAVASCRIPT IS DISABLED WE USE A HEADER REDIRECT AFTER THE UPDATE OR EMPTY BUTTONS ARE CLICKED
                                $protocol = 'http://'; if (!empty($_SERVER['HTTPS'])) { $protocol = 'https://'; }
                                echo "\t\t\t<input type='hidden' id='wpsc-checkout-page' name='wpsc_checkout_page' value='" . $protocol . $_SERVER['HTTP_HOST'] . $servrequest_uri . "' />\n";
                                echo '<input type="hidden" name="paymentGateway" id="paymentGateway" value="" />';


                                    if($devOptions['allowcheckmoneyorder']=='true' && $isLoggedIn == true) {
                                            if(!isset($_POST['ispaypal'])) {
                                                    echo '<input type="submit" value="'.$text['checkout_checkmoneyorder_button'].'" class=" ui-state-default ui-corner-all wpsc-button wpsc-checkmoneyordercheckout" onclick=" jQuery(\'#paymentGateway\').val(\'checkmoneyorder\');" onsubmit="jQuery(\'#paymentGateway\').val(\'checkmoneyorder\');"></input>';
                                            }
                                    }

                                    if($devOptions['allowpaypal']=='true' && $isLoggedIn == true) {
                                            if(!isset($_POST['ispaypal'])) {
                                                    echo '<input type="submit" value="'.$text['checkout_paypal_button'].'" class=" ui-state-default ui-corner-all wpsc-button wpsc-paypalcheckout" onclick=" jQuery(\'#paymentGateway\').val(\'paypal\');" onsubmit="jQuery(\'#paymentGateway\').val(\'paypal\');"></input>';
                                            }
                                    }

                                    if($devOptions['allowauthorizenet']=='true' && $isLoggedIn == true) {
                                            if(!isset($_POST['ispaypal'])) {
                                                    echo '<input type="submit" value="'.$text['checkout_authorizenet_button'].'" class=" ui-state-default ui-corner-all wpsc-button wpsc-authorizenetcheckout" onclick=" jQuery(\'#paymentGateway\').val(\'authorize.net\');" onsubmit=" jQuery(\'#paymentGateway\').val(\'authorize.net\');"></input>';
                                            }
                                    }

                                    if($devOptions['allow2checkout']=='true' && $isLoggedIn == true) {
                                            if(!isset($_POST['ispaypal'])) {
                                                    echo '<input type="submit" value="'.$text['checkout_2checkout_button'].'" class=" ui-state-default ui-corner-all wpsc-button wpsc-2checkoutcheckout" onclick=" jQuery(\'#paymentGateway\').val(\'2checkout\');" onsubmit="jQuery(\'#paymentGateway\').val(\'2checkout\');"></input>';
                                            }
                                    }

                                    if($devOptions['allowlibertyreserve']=='true' && $isLoggedIn == true) {
                                            if(!isset($_POST['ispaypal'])) {
                                                    echo '<input type="submit" value="'.$text['checkout_libertyreserve_button'].'" class=" ui-state-default ui-corner-all wpsc-button wpsc-libertyreservecheckout" onclick=" jQuery(\'#paymentGateway\').val(\'libertyreserve\');" onsubmit="jQuery(\'#paymentGateway\').val(\'libertyreserve\');"></input>';
                                            }
                                    }
                                

                                    if($shipping_needs_calculation==true  && $devOptions['storetype']!='Digital Goods Only') {
                                        echo '  <script type="text/javascript">
                                                /* <![CDATA[ */
                                                    jQuery(".wpsc-checkmoneyordercheckout").hide();
                                                    jQuery(".wpsc-paypalcheckout").hide();
                                                    jQuery(".wpsc-authorizenetcheckout").hide();
                                                    jQuery(".wpsc-2checkoutcheckout").hide();
                                                    jQuery(".wpsc-libertyreservecheckout").hide();
                                                /* ]]> */
                                                </script>
                                            ';
                                    }

                            }

                    echo "\t</form>\n";
                }

		// IF UPDATING AN ITEM, FOCUS ON ITS QTY INPUT AFTER THE CART IS LOADED (DOESN'T SEEM TO WORK IN IE7)
		if (isset($_POST['wpsc_update_item']))
			{
			echo "\t" . '<script type="text/javascript">
                                        /* <![CDATA[ */
                                        jQuery(function(){jQuery("#wpsc-item-id-' . $_POST['item_id'] . '").focus()});
                                        /* ]]> */
                                    </script>' . "\n";
			}

		echo "</div>\n<!-- END wpsc -->\n";
		}
	}
?>