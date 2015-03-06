<?php

/**********************************************************************

wpStoreCart Cart v3.0.  Licensed under the LGPL.

With parts based on, jcart http://conceptlogic.com/jcart/
Which was based on Webforce Cart v.1.5 (c) 2004-2005 Webforce Ltd, NZ http://www.webforcecart.com/

**********************************************************************/

global $wpsc;

$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');  

// THE HTML NAME ATTRIBUTES USED IN YOUR ADD-TO-CART FORM
$wpsc['item_id']		= 'wpstorecart-item-id';			// ITEM ID
$wpsc['item_name']		= 'wpstorecart-item-name';		// ITEM NAME
$wpsc['item_price']             = 'wpstorecart-item-price';		// ITEM PRICE
$wpsc['item_qty']		= 'wpstorecart-item-qty';		// ITEM QTY
$wpsc['item_add']		= 'wpstorecart-add-to-cart';		// ADD-TO-CART BUTTON
$wpsc['item_shipping']          = 'wpstorecart-item-shipping';           // SHIPPING
$wpsc['item_img']               = 'wpstorecart-item-img';                // THUMBNAILS
$wpsc['item_url']               = 'wpstorecart-item-url';                // LINK TO PRODUCT
$wpsc['item_tax']               = 'wpstorecart-item-tax';                // TAXES
$wpsc['path']                   =  plugins_url().'/wpstorecart/wpstorecart/cart/'; // PATH TO THE DIRECTORY CONTAINING WPSC FILES
$wpsc['form_action']            = $wpStoreCartOptions['checkoutpageurl']; // USED AS THE ACTION ATTRIBUTE FOR THE SHOPPING CART FORM
$wpsc['paypal_id']		= $wpStoreCartOptions['paypalemail']; // YOUR PAYPAL SECURE MERCHANT ACCOUNT ID
$wpsc['text']['cart_title']	= $wpStoreCartOptions['cart_title'];		// Shopping Cart
$wpsc['text']['single_item']	= $wpStoreCartOptions['single_item'];		// Item
$wpsc['text']['multiple_items']	= $wpStoreCartOptions['multiple_items'];		// Items
$wpsc['text']['currency_symbol']= $wpStoreCartOptions['currency_symbol'];		// $
$wpsc['text']['subtotal']	= $wpStoreCartOptions['subtotal'];		// Subtotal
$wpsc['text']['total']		= $wpStoreCartOptions['total'];		//Total
$wpsc['text']['shipping']	= $wpStoreCartOptions['shipping'];		// Shipping
$wpsc['text']['tax']		= $wpStoreCartOptions['tax'];
$wpsc['text']['login']		= $wpStoreCartOptions['login'];
$wpsc['text']['logout']		= $wpStoreCartOptions['logout'];
$wpsc['text']['register']	= $wpStoreCartOptions['register'];
$wpsc['text']['username']	= $wpStoreCartOptions['username'];
$wpsc['text']['password']	= $wpStoreCartOptions['password'];
$wpsc['text']['email']		= $wpStoreCartOptions['email'];
$wpsc['text']['required_symbol']= $wpStoreCartOptions['required_symbol'];
$wpsc['text']['required_help']	= $wpStoreCartOptions['required_help'];
$wpsc['text']['calculateshipping'] = $wpStoreCartOptions['calculateshipping'];
$wpsc['text']['currency_symbol']= $wpStoreCartOptions['currency_symbol'];
$wpsc['text']['currency_symbol_right']					= $wpStoreCartOptions['currency_symbol_right'];		
$wpsc['text']['update_button']				= $wpStoreCartOptions['update_button'];		// update
$wpsc['text']['checkout_button']			= $wpStoreCartOptions['checkout_button'];
$wpsc['text']['guestcheckout']			= $wpStoreCartOptions['guestcheckout'];// checkout
$wpsc['text']['checkout_paypal_button']	= $wpStoreCartOptions['checkout_paypal_button'];		// Checkout with PayPal
@$wpsc['text']['checkout_authorizenet_button']	= $wpStoreCartOptions['checkout_authorizenet_button'];
@$wpsc['text']['checkout_2checkout_button']	= $wpStoreCartOptions['checkout_2checkout_button'];
@$wpsc['text']['checkout_checkmoneyorder_button']	= $wpStoreCartOptions['checkout_checkmoneyorder_button'];
@$wpsc['text']['checkout_libertyreserve_button']	= $wpStoreCartOptions['checkout_libertyreserve_button'];
@$wpsc['text']['checkout_moneybookers_button']	= $wpStoreCartOptions['checkout_moneybookers_button'];
@$wpsc['text']['cc_name']	= $wpStoreCartOptions['cc_name'];
@$wpsc['text']['cc_number']	= $wpStoreCartOptions['cc_number'];
@$wpsc['text']['cc_expires']	= $wpStoreCartOptions['cc_expires'];
@$wpsc['text']['cc_expires_month']	= $wpStoreCartOptions['cc_expires_month'];
@$wpsc['text']['cc_expires_year']	= $wpStoreCartOptions['cc_expires_year'];
@$wpsc['text']['cc_address']	= $wpStoreCartOptions['cc_address'];
@$wpsc['text']['cc_postalcode']	= $wpStoreCartOptions['cc_postalcode'];
@$wpsc['text']['cc_cvv']	= $wpStoreCartOptions['cc_cvv'];
$wpsc['text']['remove_link']				= $wpStoreCartOptions['remove_link'];		// remove
$wpsc['text']['empty_button']				= $wpStoreCartOptions['empty_button'];		// empty
$wpsc['text']['empty_message']				= $wpStoreCartOptions['empty_message'];		// Your cart is empty!
$wpsc['text']['item_added_message']		= $wpStoreCartOptions['item_added_message'];		// Item added!
$wpsc['text']['enter_coupon']		= $wpStoreCartOptions['enter_coupon'];
$wpsc['text']['price_error']				= $wpStoreCartOptions['price_error'];		// Invalid price format!
$wpsc['text']['quantity_error']			= $wpStoreCartOptions['quantity_error'];		// Item quantities must be whole numbers!
$wpsc['text']['checkout_error']			=$wpStoreCartOptions['checkout_error'];		// Your order could not be processed!
$wpsc['text']['add_to_cart']			=$wpStoreCartOptions['add_to_cart'];		// Add to cart
$wpsc['button']['checkout']				= '';
$wpsc['button']['paypal_checkout']			= '';
$wpsc['button']['update']					= '';
$wpsc['button']['empty']					= '';

// wpsc
class wpsc_shoppingcart {
	var $total = 0;
	var $itemcount = 0;
	var $items = array();
	var $itemprices = array();
	var $itemqtys = array();
	var $itemname = array();
        var $itemshipping = array(); // Added in wpStoreCart 2.2.0
        var $itemtax = array(); // Added in wpStoreCart 2.3.2
        var $itemurl = array(); // Added in wpStoreCart 2.3.2
        var $itemimg = array(); // Added in wpStoreCart 2.3.2
        var $options = array(); // Added in wpStoreCart 3.9.26

	// CONSTRUCTOR FUNCTION
        function __construct() {
            $this->restore_cart();
        }

     
	function cart() {;}

        function restore_cart() {
            global $wpdb;


                global $current_user;
                wp_get_current_user();
                if ( 0 == $current_user->ID ) {
                    // Not logged in.
                    $theuser = 0;
                } else {
                    $theuser = $current_user->ID;
                }


                $sql = 'SELECT * FROM `'.$wpdb->prefix.'wpstorecart_cart` WHERE `ipaddress`=\''.$this->get_ip_address().'\';';
                $results = $wpdb->get_results( $sql , ARRAY_A );

                if(isset($results[0]['primkey'])) {
                    $this->total = $results[0]['total'];
                    $this->itemcount = $results[0]['itemcount'];
                    $this->items = unserialize(base64_decode($results[0]['items']));
                    $this->itemprices = unserialize(base64_decode($results[0]['itemprices']));
                    $this->itemqtys = unserialize(base64_decode($results[0]['itemqtys']));
                    $this->itemname = unserialize(base64_decode($results[0]['itemname']));
                    $this->itemshipping = unserialize(base64_decode($results[0]['itemshipping']));
                    $this->itemtax = unserialize(base64_decode($results[0]['itemtax']));
                    $this->itemurl = unserialize(base64_decode($results[0]['itemurl']));
                    $this->itemimg = unserialize(base64_decode($results[0]['itemimg']));
                    $this->options = unserialize(base64_decode($results[0]['options']));
                }



            
        }

        /**
         * Returns the best guess for the user's IP address
         * 
         * @global object $wpdb
         * @return string  
         */
        function get_ip_address() {
            global $wpdb;
            if ( isset($_SERVER["REMOTE_ADDR"]) )    {
                return esc_sql($_SERVER["REMOTE_ADDR"]);
            } else if ( isset($_SERVER["HTTP_X_FORWARDED_FOR"]) )    {
                return esc_sql($_SERVER["HTTP_X_FORWARDED_FOR"]);
            } else if ( isset($_SERVER["HTTP_CLIENT_IP"]) )    {
                return esc_sql($_SERVER["HTTP_CLIENT_IP"]);
            }
            return 0;
        }


        /**
         * Get the contents of the current shopping cart
         * 
         * @return array 
         */
	function get_contents() {
		$items = array();
		foreach($this->items as $tmp_item)
			{
			$item = FALSE;
 
			$item['id'] = $tmp_item;
			$item['qty'] = $this->itemqtys[$tmp_item];
                        $item['price'] = $this->itemprices[$tmp_item];
			$item['name'] = $this->itemname[$tmp_item];
                        $item['shipping'] = $this->itemshipping[$tmp_item]; // Added in wpStoreCart 2.2.0
                        $item['tax'] = $this->itemtax[$tmp_item]; // Added in wpStoreCart 2.3.2
                        $item['url'] = $this->itemurl[$tmp_item]; // Added in wpStoreCart 2.3.2
                        $item['img'] = $this->itemimg[$tmp_item]; // Added in wpStoreCart 2.3.2
                        $item['options'] = $this->options[$tmp_item]; // Added in wpStoreCart 2.3.2
                        $item['subtotal'] = $item['qty'] * $item['price'];
                        
			$items[] = $item;
			}
		return $items;
	}

        /**
         *
         * Adds multiple items to the cart at once
         * 
         * @global type $wpdb
         * @global type $current_user
         * @global type $wpStoreCart
         * @param type $items_to_add
         * @param type $qty
         * @param type $masterProduct 
         */
        function multi_add_item($items_to_add, $qty=1, $masterProduct=NULL) {
            global $wpdb, $current_user;

            
            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');  
            
            wp_get_current_user();
            if ( 0 == $current_user->ID ) {
                // Not logged in.
                $theuser = 0;
            } else {
                $theuser = $current_user->ID;
            }                
            
            // Combos discounts are calculated here
            if($masterProduct!=NULL) { 
                $theAccessories = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `type`='productcombo' AND `foreignkey`='{$masterProduct}';", ARRAY_A);
                foreach($theAccessories as $theAccessory) {
                    $exploded = explode('||', $theAccessory['value']);
                    $theComboPrice[$exploded[0]] = $exploded[1];
                }
            }             
            
            
            foreach ($items_to_add as $item_to_add) {

                if(($item_to_add['discountprice'] < $item_to_add['price']) && $item_to_add['discountprice']!='0.00') {
                    $thePrice = $item_to_add['discountprice'];
                } else {
                    $thePrice = $item_to_add['price'];
                }
                
                if(isset($theComboPrice[$item_to_add['primkey']])) {
                        if($item_to_add['discountprice'] < $theComboPrice[$item_to_add['primkey']]) {
                            $thePrice = $item_to_add['discountprice'];
                        } else {
                            $thePrice = $theComboPrice[$item_to_add['primkey']];
                        }
                }
                
                $groupDiscount = wpscGroupDiscounts($item_to_add['category'], $theuser);
                
                // Group discounts
                if ($groupDiscount['can_have_discount']==true && $wpStoreCartOptions['gd_enable']=='true') {
                    $percentDiscount = $groupDiscount['discount_amount'] / 100;
                    if($groupDiscount['gd_saleprice']==true) {
                        if($item_to_add['discountprice']=='0.00') { // Group discount calculated if we're basing the discount off of the regular price
                            $discountToSubtract = $item_to_add['price'] * $percentDiscount;
                            $gdDiscountPrice = number_format($item_to_add['price'] - $discountToSubtract, 2);
                        } else { // Group discount calculated if we're basing the discount off of the discounted price
                            $discountToSubtract = $item_to_add['discountprice'] * $percentDiscount;
                            $gdDiscountPrice = number_format($item_to_add['discountprice'] - $discountToSubtract, 2);
                        }
                    }                                                                      
                    if($gdDiscountPrice==0) { 
                        // No change
                    } else {
                        if($gdDiscountPrice < $thePrice) {
                            $thePrice = $gdDiscountPrice;
                        }
                    }
                }   
                // end group discount                  
                
                                 
                $this->add_item($item_to_add['primkey'], $qty, $thePrice, $item_to_add['name'], $item_to_add['shipping'], 0, get_permalink($item_to_add['postid']), $item_to_add['thumbnail'], '0.00', true);
            }
        }

        /**
         * Add an item to the current cart
         * 
         * @global object $wpStoreCart
         * @param int $item_id
         * @param int $item_qty
         * @param float $item_price
         * @param string $item_name
         * @param float $item_shipping
         * @param float $item_tax
         * @param string $item_url
         * @param string $item_img
         * @param mixed $item_subscriptionprice
         * @param bool $is_multi_add
         * @param string $options
         * @return mixed
         */
	function add_item($item_id, $item_qty=1, $item_price=0, $item_name='', $item_shipping=0, $item_tax=0, $item_url='', $item_img='', $item_subscriptionprice='0.00', $is_multi_add = false, $options=null) {
                
                $qtyStart = 0;
                while($qtyStart < $item_qty) {
                    wpscProductIncreaseProductAddToCartStatistic($item_id);
                    $qtyStart++;
                }
                
                $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');  
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
			if(@$this->itemqtys[$item_id] > 0) {
				$this->itemqtys[$item_id] = $item_qty + $this->itemqtys[$item_id];
				$this->_update_total();
			} else { // THIS IS A NEW ITEM
				$this->items[] = $item_id;
				$this->itemqtys[$item_id] = $item_qty;
				$this->itemprices[$item_id] = $item_price;
				$this->itemname[$item_id] = $item_name;
                                $this->itemshipping[$item_id] = $item_shipping;
                                $this->itemtax[$item_id] = $item_tax;
                                $this->itemurl[$item_id] = $item_url;
                                $this->itemimg[$item_id] = $item_img;
				$this->options[$item_id] = $options;
			}
			$this->_update_total();
                        
                        if(!$is_multi_add && $wpStoreCartOptions['redirect_to_checkout']=='true' && $wpStoreCartOptions['checkoutpageurl']!='' ) {
                            
                            if(@isset($_GET['wpsc-app-store'])) {
                                if(strpos(get_permalink($wpStoreCartOptions['checkoutpage']),'?')===false) {
                                    $permalink = get_permalink($wpStoreCartOptions['checkoutpage']) .'?wpsc-app-store=1';
                                } else {
                                    $permalink = get_permalink($wpStoreCartOptions['checkoutpage']) .'&wpsc-app-store=1';
                                }    
                            } else {
                                $permalink = get_permalink($wpStoreCartOptions['checkoutpage']);
                            }                            
                            
                            if (!headers_sent()) {
                                header('Location: '.$permalink);
                                exit;
                            } else {
                                echo '
                                <script type="text/javascript">
                                /* <![CDATA[ */
                                window.location = "'.$permalink.'"
                                /* ]]> */
                                </script>
                                ';          
                                exit;
                            }                        
                        }

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
                $couponid = NULL;
		if(@!isset($_SESSION)) {
			@session_start();
		}
		
		if(@isset($_POST['ccoupon'])) {
			global $wpdb;

			$thecoupon = esc_sql($_POST['ccoupon']);
		
			$table_name = $wpdb->prefix . "wpstorecart_coupons";
			// Try adding the coupon hooks here
			$grabrecord = "SELECT * FROM {$table_name} WHERE `startdate` < ".date("Ymd")." AND `enddate` > ".date("Ymd")." AND `code`='{$thecoupon}' AND `product`={$item_id};";					
			

                        $recordFound = false;
			$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
			if(isset($results)) { // This will give us any valid coupons for the specific product
				@$_SESSION['validcoupon'] = $_POST['ccoupon'];
				
				foreach ($results as $result) {
					$discount_price = $result['amount'];
					$discount_percent = $result['percent'];
					
					@$_SESSION['validcouponid'] = $item_id;
                                        @$_SESSION['validcouponamount'] = $result['amount'];
                                        @$_SESSION['validcouponpercent'] = $result['percent'];
                                        if($discount_percent > 0) {
                                            $discount_price = $discount_price + ($this->itemqtys[$item_id] * $this->itemprices[$item_id]) * ($discount_percent / 100); // Here we calculate the amount deducted
                                        }
                                        $couponid = $result['primkey'];
                                        $recordFound = true;
				}

			} 
                        
                        if($recordFound == false){ // Down here we're looking for valid coupons that are global
                            $grabrecord = "SELECT * FROM {$table_name} WHERE `startdate` < ".date("Ymd")." AND `enddate` > ".date("Ymd")." AND `code`='{$thecoupon}' AND `product`=0;";

                            $results2 = $wpdb->get_results( $grabrecord , ARRAY_A );
                            if(isset($results2)) { // This will give us any valid coupons for the specific product
                                    @$_SESSION['validcoupon'] = $_POST['ccoupon'];

                                    $theRunningtotal = 0;
                                    if(sizeof($this->items > 0)) {
                                            foreach($this->items as $item) {
                                                        $theRunningtotal = $theRunningtotal + ($this->itemprices[$item] * $this->itemqtys[$item]);
                                            }
                                    }

                                    foreach ($results2 as $result) {
                                            $discount_price = $result['amount'];
                                            $discount_percent = $result['percent'];

                                            @$_SESSION['validcouponid'] = 0;
                                            @$_SESSION['validcouponamount'] = $result['amount'];
                                            @$_SESSION['validcouponpercent'] = $result['percent']; 
                                            if($discount_percent > 0) {
                                                $discount_price = $discount_price + $theRunningtotal * ($discount_percent / 100); // Here we calculate the amount deducted
                                            }
                                            $recordFound = true;
                                            $couponid = $result['primkey'];


                                    }

                            }
                        }                       
                        
                        // If we had a coupon, let's log it.
                        if($recordFound) {                        
                            wpscLog(NULL, 'couponused', $thecoupon, $couponid, date('Ymd'));
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
	function del_item($item_id) {
		$ti = array();
                unset($this->items[$item_id]);
                unset($this->itemprices[$item_id]);
                unset($this->itemqtys[$item_id]);
                unset($this->itemname[$item_id]);
                unset($this->itemshipping[$item_id]);
                unset($this->itemtax[$item_id]);
                unset($this->itemurl[$item_id]);
                unset($this->itemimg[$item_id]);
                unset($this->options[$item_id]);
		foreach($this->items as $item)
			{
			if($item != $item_id)
				{
				$ti[] = $item;
				}
			}
		$this->items = $ti;
		$this->_update_total();
                
                // Combos discounts are calculated here
                global $wpdb;
                $theAccessories = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `type`='productcombo' AND `foreignkey`='{$item_id}';", ARRAY_A);
                foreach($theAccessories as $theAccessory) {
                    $exploded = explode('||', $theAccessory['value']);
                    $this->del_item($exploded[0]);
                }                
                
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
                    $this->itemtax = array();
                    $this->itemurl = array();
                    $this->itemimg = array();
                    $this->options = array();
                    $this->_update_total();

                }


	// INTERNAL FUNCTION TO RECALCULATE TOTAL
	function _update_total() {
		$this->itemcount = 0;
		$this->total = 0;
		if(sizeof($this->items > 0))
                    {
                        $couponUsed = false;
			foreach($this->items as $item) {
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

                global $wpdb, $current_user;

                    // This chunk of code saves our cart to the db
                
                    wp_get_current_user();
                    if ( 0 == $current_user->ID ) {
                        // Not logged in.
                        $theuser = 0;
                    } else {
                        $theuser = $current_user->ID;
                    }

                        $sql = 'SELECT * FROM `'.$wpdb->prefix.'wpstorecart_cart` WHERE `ipaddress`=\''.$this->get_ip_address().'\';';
                        $results = $wpdb->get_results( $sql , ARRAY_A );

                        if(isset($results[0]['primkey'])) {
                            $sql = 'UPDATE `'.$wpdb->prefix.'wpstorecart_cart` SET
                            `total` = "'.$this->total.'",
                            `itemcount` = "'.$this->itemcount.'",
                            `items` = "'.base64_encode(serialize($this->items)).'",
                            `itemprices` = "'.base64_encode(serialize($this->itemprices)).'",
                            `itemqtys` = "'.base64_encode(serialize($this->itemqtys)).'",
                            `itemname` = "'.base64_encode(serialize($this->itemname)).'",
                            `itemshipping` = "'.base64_encode(serialize($this->itemshipping)).'",
                            `itemtax` = "'.base64_encode(serialize($this->itemtax)).'",
                            `itemurl` = "'.base64_encode(serialize($this->itemurl)).'",
                            `itemimg` = "'.base64_encode(serialize($this->itemimg)).'",
                            `options` = "'.base64_encode(serialize($this->options)).'"
                            WHERE `ipaddress`="'.$this->get_ip_address().'";';
                            $results = $wpdb->query( $sql );
                        
                     } else {

                        $sql = 'INSERT INTO `'.$wpdb->prefix.'wpstorecart_cart`
                            (`primkey`, `total`, `itemcount`, `items`, `itemprices`, `itemqtys`, `itemname`, `itemshipping`, `itemtax`, `itemurl`, `itemimg`, `user_id`, `options`, `ipaddress`)
                            VALUES (
                            NULL,
                            \''.$this->total.'\',
                            \''.$this->itemcount.'\',
                            \''.base64_encode(serialize($this->items)).'\',
                            \''.base64_encode(serialize($this->itemprices)).'\',
                            \''.base64_encode(serialize($this->itemqtys)).'\',
                            \''.base64_encode(serialize($this->itemname)).'\',
                            \''.base64_encode(serialize($this->itemshipping)).'\',
                            \''.base64_encode(serialize($this->itemtax)).'\',
                            \''.base64_encode(serialize($this->itemurl)).'\',
                            \''.base64_encode(serialize($this->itemimg)).'\',
                            \''.$theuser.'\',
                            \''.base64_encode(serialize($this->options)).'\',
                            \''.$this->get_ip_address().'\');';
                            $results = $wpdb->query( $sql );

                    }

                



	}
        
        
        function calculate_totals() {
            
        }


	// PROCESS AND DISPLAY CART
	function display_cart($wpsc_input=NULL, $hidden=false) {
		global $wpsc, $is_checkout, $wpStoreCartOptions, $wpscCarthasBeenCalled, $wpscWidgetSettings, $wpscIsCheckoutPage, $wpdb;

                $top_already_display = false;
                
		if(@!isset($_SESSION)) {
			@session_start();
		}
                
                $output = '';

		// wpsc ARRAY HOLDS USER CONFIG SETTINGS
		extract($wpsc);

                // We use this global variable to fix a bug
                $wpscCarthasBeenCalled = true;
                

		// ASSIGN USER CONFIG VALUES AS POST VAR LITERAL INDICES
		// INDICES ARE THE HTML NAME ATTRIBUTES FROM THE USERS ADD-TO-CART FORM
		@$item_id = $_POST[$item_id];
		@$item_qty = $_POST[$item_qty];
		@$item_price = $_POST[$item_price];
		@$item_name = stripslashes($_POST[$item_name]);
                @$item_shipping = $_POST[$item_shipping];
                @$item_tax = $_POST[$item_tax];
                @$item_url = $_POST[$item_url];
                @$item_img = $_POST[$item_img];
                //@$options = $_POST['wpstorecart_product_options'];
                foreach(@$_POST['wpstorecart_product_options'] as $value) {
                    @$options .= $value;
                }


                
		// ADD AN ITEM
		if (isset($_POST[$item_add])) {
                $item_added = $this->add_item($item_id, $item_qty, $item_price, $item_name, $item_shipping, $item_tax, $item_url, $item_img, $options);
                    // IF NOT TRUE THE ADD ITEM FUNCTION RETURNS THE ERROR TYPE
                    if ($item_added !== true) {
                        $error_type = $item_added;
                        switch($error_type) {
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
			@$is_checkout = strpos($_SERVER['REQUEST_URI'], $form_action);
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
                            $appstore = '';
                            if(@isset($_GET['wpsc-app-store'])) {
                                $appstore = '?wpsc-app-store=1';
                            }
                            $form_action = $path . 'wpsc-gateway.php'.$appstore;
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
                           
                                $output .= "<!-- BEGIN wpsc --><div id='wpsc"; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .="' class='wpsc-checkout-page-contents'>";
                            } else {
                                $output .= "<!-- BEGIN wpsc --><div id='wpsc"; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .="'>";
                            }
                        } else {
                            $output .= '<!-- BEGIN wpsc -->
                                <div id="wpsc" style="display:none;">
                                ';
                        }
		if (isset($error_message)) {
			$output .= "$error_message";
		}

                $disable_inline_styles = ' style="float:left;"';
                if($wpStoreCartOptions['disable_inline_styles']=='true') {
                    $disable_inline_styles = '';
                }

                $isLoggedIn = NULL;

                $alreadyDisplayedForm = false;
                if ($is_checkout == true) {
                    if ( is_user_logged_in() ) {
                        $isLoggedIn = true;
                    } else {

                      
                        // ** Here's where we disable the user login system during checkout if registration is not required
                        if($wpStoreCartOptions['requireregistration']=='false' || $wpStoreCartOptions['requireregistration']=='disable') {
                            if(@isset($_POST['guest_email'])) {
                                $_SESSION['wpsc_email'] = esc_sql($_POST['guest_email']);
                            }
                            if(@isset($_SESSION['wpsc_email'])) {
                                $isLoggedIn = true;
                            } else {
                                
                                if ($is_checkout == true && $this->itemcount > 0) { 
                                    $top_already_display = true;
                                    $output .= '<ul id="wpsc-checkout-steps">
                                        <li id="wpscCheckoutStepDesc0" class="current">
                                        '.__('Step 1', 'wpstorecart').'
                                        <span>'.__('Your Info', 'wpstorecart').'</span>
                                        </li>
                                        <li id="wpscCheckoutStepDesc1">
                                        '.__('Step 2', 'wpstorecart').'
                                        <span>'.__('Review Your Order', 'wpstorecart').'</span>
                                        </li>
                                        <li id="wpscCheckoutStepDesc2">
                                        '.__('Step 3', 'wpstorecart').'
                                        <span>'.__('Pay', 'wpstorecart').'</span>
                                        </li>
                                    </ul>';                                
                                }
                                
                                $output .= '
                                    <div class="wpsc-checkout-guests">
                                        <form name="wpsc-registerform" id="wpsc-guestcheckoutform" action="#" method="post" onsubmit="var re = /\\S+@\\S+\\.\\S+/; if(re.test(jQuery(\'#wpsc_guest_email\').val())===false){alert(\''.__('Invalid email address.  Please try again.', 'wpstorecart').'\'); return false;}">
                                            <table>
                                            <tr><td>'. $text['guestcheckout'] .'</td><td></td></tr>
                                            <tr><td>'. $text['email'] .' <ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$text['required_symbol'].'</div></ins></td><td><input onblur="var re = /\\S+@\\S+\\.\\S+/; if(re.test(jQuery(this).val())===false){alert(\''.__('Invalid email address.  Please try again.', 'wpstorecart').'\'); return false;}" type="text" id="wpsc_guest_email" name="guest_email" value="'.$_SESSION['wpsc_email'].'" /></td></tr>
                                            ';
                                            $output .= wpscShowCustomRegistrationFields();
                                            $output .='
                                            <tr><td></td><td><input type="submit" value="'. $text['checkout_button'] .'" class="wpsc-button wpsc-checkout '.$wpStoreCartOptions['button_classes_checkout'].'" /></td></tr>
                                            </table>
                                        </form>
                                    </div>
                                    <br />
                                    ';
                                $isLoggedIn = false;

                            }
                        } else {
                           $isLoggedIn = false;
                        }
                        
                     // Only shown if the user is not logged in and registration is okay
                     if($isLoggedIn == false && $wpStoreCartOptions['requireregistration']!='disable') {
                            /*
                             * Show error messages, then remove the wpscregerror from the URI
                             */
                            $servrequest_uri = $_SERVER['REQUEST_URI'] ;
                            if(@isset($_GET['wpscregerror'])) {
                                if($_GET['wpscregerror']=='1') {
                                    $output .= '<div class="wpsc-error">'. $text['username'] .' taken.</div>';
                                    $servrequest_uri = str_replace("&wpscregerror=1", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=1", "", $servrequest_uri);
                                }
                                if($_GET['wpscregerror']=='2') {
                                    $output .= '<div class="wpsc-error">'. $text['username'] .' invalid.</div>';
                                    $servrequest_uri = str_replace("&wpscregerror=2", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=2", "", $servrequest_uri);
                                }
                                if($_GET['wpscregerror']=='3') {
                                   $output .= '<div class="wpsc-error">'. $text['email'] .' is invalid.</div>';
                                    $servrequest_uri = str_replace("&wpscregerror=3", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=3", "", $servrequest_uri);
                                }
                                if($_GET['wpscregerror']=='4') {
                                    $servrequest_uri = str_replace("&wpscregerror=4", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=4", "", $servrequest_uri);
                                    $output .= '<div class="wpsc-error">'. $text['email'] .' - ' .__('Email is already registered.', 'wpstorecart').'</div>';
                                }
                                if($_GET['wpscregerror']=='5') {
                                    $servrequest_uri = str_replace("&wpscregerror=5", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=5", "", $servrequest_uri);
                                    $output .= '<div class="wpsc-error">'.__('Wordpress could not create the account, alert the admin to enable registrations.', 'wpstorecart').'</div>';
                                }
                                if($_GET['wpscregerror']=='6') {
                                    $servrequest_uri = str_replace("&wpscregerror=6", "", $servrequest_uri );
                                    $servrequest_uri = str_replace("?wpscregerror=6", "", $servrequest_uri);
                                    $output .= '<div class="wpsc-error">'.__('Not all of the required fields were filled out.  Please fill out all the required information and try again.', 'wpstorecart').'</div>';
                                }
                            }

                            if ($is_checkout == true && $this->itemcount > 0 && !$top_already_display) {
                                $output .= '<ul id="wpsc-checkout-steps">
                                    <li id="wpscCheckoutStepDesc0" class="current">
                                    '.__('Step 1', 'wpstorecart').'
                                    <span>'.__('Your Info', 'wpstorecart').'</span>
                                    </li>
                                    <li id="wpscCheckoutStepDesc1">
                                    '.__('Step 2', 'wpstorecart').'
                                    <span>'.__('Review Your Order', 'wpstorecart').'</span>
                                    </li>
                                    <li id="wpscCheckoutStepDesc2">
                                    '.__('Step 3', 'wpstorecart').'
                                    <span>'.__('Pay', 'wpstorecart').'</span>
                                    </li>
                                </ul>';
                            }
                            
                            // registration
                            $output .= '
                            <div class="wpsc-checkout-registration-login">
                                <form name="wpsc-loginform" id="wpsc-loginform" method="post" action="'. wp_login_url( get_permalink() ) .'">
                                    <br /><strong>'. $text['login'] .'</strong><br />
                                            <table>
                                            <tr><td>'. $text['username'] .'</td><td><input type="text" value="" name="log" /></td></tr>
                                            <tr><td>'. $text['password'] .' </td><td><input type="password" value="" name="pwd"  /></td></tr>
                                            <tr><td></td><td><input type="hidden" name="redirect_to" value="'.$servrequest_uri.'" /><input type="submit" value="'. $text['login'] .'" class="wpsc-button wpsc-login-button '.$wpStoreCartOptions['button_classes_meta'].'" /></td></tr>
                                            </table>
                                </form>
                                <br />
                                <form name="wpsc-registerform" id="wpsc-registerform" action="'.plugins_url().'/wpstorecart/wpstorecart/registration/register.php" method="post" onsubmit="var re = /\\S+@\\S+\\.\\S+/; if(re.test(jQuery(\'#email\').val())===false){alert(\''.__('Invalid email address.  Please try again.', 'wpstorecart').'\'); return false;}">
                                    <br /><strong>'. $text['register'] .'</strong><br />
                                                <table>
                                                <tr><td>'. $text['email'] .' <ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$text['required_symbol'].'</div></ins></td><td><input type="text" name="email" id="email" value="'.$_SESSION['wpsc_email'].'" /></td></tr>
                                                <tr><td>'. $text['password'] .'<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$text['required_symbol'].'</div></ins></td><td><input type="password" name="user_pass" value="'.$_SESSION['wpsc_password'].'" /></td></tr>';

                                                $output .= wpscShowCustomRegistrationFields();

                                $output .= '          <input type="hidden" name="redirect_to" value="'.$servrequest_uri.'" />
                                                <tr><td></td><td><input type="submit" name="wp-submit" value="'. $text['register'] .'" class="wpsc-button wpsc-register-button  '.$wpStoreCartOptions['button_classes_meta'].'" /></td></tr>
                                                    </table>
                                                <br /><span class="wpsc-required-help">'.$text['required_help'].'</span>
                                </form>
                            </div>
                            <br />
                            ';
                            $alreadyDisplayedForm = true;
                        }
                    }

              
            }
            
              

                if( $isLoggedIn == true || $is_checkout==false) {
                    $continue = true;
                    
                    
                    // Checking to see if this logged in user also has all required fields filled out
                    if(is_user_logged_in() && $is_checkout == true && $this->itemcount > 0) {
                        
                        wpscSaveFields(wp_get_current_user()->ID); // Save fields, if necessary
                        
                        // Redisplay the custom fields if a user doesn't have the required fields filled out
                        if(wpscVerifyNeedToShowRequiredCustomRegistrationFields(wp_get_current_user()->ID)) {
                            $output .= '<ul id="wpsc-checkout-steps">
                                <li id="wpscCheckoutStepDesc0" class="current">
                                '.__('Step 1', 'wpstorecart').'
                                <span>'.__('Your Info', 'wpstorecart').'</span>
                                </li>
                                <li id="wpscCheckoutStepDesc1">
                                '.__('Step 2', 'wpstorecart').'
                                <span>'.__('Review Your Order', 'wpstorecart').'</span>
                                </li>
                                <li id="wpscCheckoutStepDesc2">
                                '.__('Step 3', 'wpstorecart').'
                                <span>'.__('Pay', 'wpstorecart').'</span>
                                </li>
                            </ul>';                             
                            $output .= '
                            <br />
                            <form name="wpsc-registerform" id="wpsc-registerform" action="'.$servrequest_uri.'" method="post">
                                <br /><strong>'. __('You are missing required information', 'wpstorecart') .'</strong><br />
                                            <table>';

                                $output .= wpscShowCustomRegistrationFields();

                                $output .= '<input type="hidden" name="redirect_to" value="'.$servrequest_uri.'" />
                                            <tr><td></td><td><input type="submit" name="wp-submit" value="'. $text['update_button'] .'" class="wpsc-button wpsc-register-button  '.$wpStoreCartOptions['button_classes_meta'].'" /></td></tr>
                                                </table>
                                            <br /><span class="wpsc-required-help">'.$text['required_help'].'</span>
                            </form>';
                                $continue = false;
                        } 
                    }
                    
                    
                    // Checking to see if this guest user also has all required fields filled out
                    if(!is_user_logged_in() && $is_checkout == true && $this->itemcount > 0) {
                        
                        wpscSaveFields(0); // Save fields, if necessary
                        
                        // Redisplay the custom fields if a user or guest doesn't have the required fields filled out
                        if(wpscVerifyNeedToShowRequiredCustomRegistrationFields(0)) {
                            $output .= '<ul id="wpsc-checkout-steps">
                                <li id="wpscCheckoutStepDesc0" class="current">
                                '.__('Step 1', 'wpstorecart').'
                                <span>'.__('Your Info', 'wpstorecart').'</span>
                                </li>
                                <li id="wpscCheckoutStepDesc1">
                                '.__('Step 2', 'wpstorecart').'
                                <span>'.__('Review Your Order', 'wpstorecart').'</span>
                                </li>
                                <li id="wpscCheckoutStepDesc2">
                                '.__('Step 3', 'wpstorecart').'
                                <span>'.__('Pay', 'wpstorecart').'</span>
                                </li>
                            </ul>';                             
                            $output .= '
                            <br />
                            <form name="wpsc-registerform" id="wpsc-registerform" action="'.$servrequest_uri.'" method="post">
                                <br /><strong>'. __('You are missing required information', 'wpstorecart') .'</strong><br />
                                            <table>';

                                $output .= wpscShowCustomRegistrationFields();

                                $output .= '<input type="hidden" name="redirect_to" value="'.$servrequest_uri.'" />
                                            <tr><td></td><td><input type="submit" name="wp-submit" value="'. $text['update_button'] .'" class="wpsc-button wpsc-register-button  '.$wpStoreCartOptions['button_classes_meta'].'" /></td></tr>
                                                </table>
                                            <br /><span class="wpsc-required-help">'.$text['required_help'].'</span>
                            </form>';
                                $continue = false;
                        } 
                    }                    
                    
                    
                    
                    if($continue) { // If all required information is entered, we can continue through checkout
                        if ($is_checkout == true && $this->itemcount > 0) {
                            $output .= '<ul id="wpsc-checkout-steps">
                                <li id="wpscCheckoutStepDesc0">
                                '.__('Step 1', 'wpstorecart').'
                                <span>'.__('Your Info', 'wpstorecart').'</span>
                                </li>
                                <li id="wpscCheckoutStepDesc1"  class="current">
                                '.__('Step 2', 'wpstorecart').'
                                <span>'.__('Review Your Order', 'wpstorecart').'</span>
                                </li>
                                <li id="wpscCheckoutStepDesc2">
                                '.__('Step 3', 'wpstorecart').'
                                <span>'.__('Pay', 'wpstorecart').'</span>
                                </li>
                            </ul>';                
                        }

                        $target = '';
                        if(@isset($_GET['wpsc-app-store'])) {
                            $target = ' target="_blank" ';
                        }
                        $output .= " <form method=\"post\" $target action=\"$form_action\"> ";

                        $output .= "<strong id='wpsc-title'>" . $text['cart_title'] . "</strong> (" . $this->itemcount . "&nbsp;" . $text['items_in_cart'] .")<br />";


                        // IF ANY ITEMS IN THE CART
                        if($this->itemcount > 0) {
                            
                                wpscLoadFields(); // Get our customer information into sessions

                                $totalshipping = 0; // set shipping to zero
                                $shipping_needs_calculation = false; // By default we'll assume shipping doesn't need calculations
                                $shipping_offered_by_flatrate = true; // By default, we'll assume we can use flatrate shipping
                                $shipping_offered_by_usps = true; // By default, we'll assume we can use USPS shipping
                                $shipping_offered_by_ups = true; // By default, we'll assume we can use UPS shipping
                                $shipping_offered_by_fedex = true; // By default, we'll assume we can't use FedEx shipping
                                $table_name_meta = $wpdb->prefix . "wpstorecart_meta";

                                if($wpStoreCartOptions['enableusps']=='false' || $wpStoreCartOptions['storetype']=='Digital Goods Only') {
                                    $shipping_offered_by_usps = false;
                                }
                                if($wpStoreCartOptions['enableups']=='false' || $wpStoreCartOptions['storetype']=='Digital Goods Only') {
                                    $shipping_offered_by_ups = false;
                                }
                                if($wpStoreCartOptions['enablefedex']=='false' || $wpStoreCartOptions['storetype']=='Digital Goods Only') {
                                    $shipping_offered_by_fedex = false;
                                }
                                if($wpStoreCartOptions['flatrateshipping']=='off' || $wpStoreCartOptions['storetype']=='Digital Goods Only') {
                                    $shipping_offered_by_flatrate = false;
                                }

                                if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {
                                    $output .= '<table class="wpsc-checkout-table">';
                                }

                                // DISPLAY LINE ITEMS
                                foreach($this->get_contents() as $item) {

                                        $totalshipping = $totalshipping + ($item['shipping'] * $item['qty']); // Added in 2.2

                                        $output_qty = '';
                                        $output_pic = '';
                                        $output_name = '';
                                        $output_price = '';
                                        $output_remove = '';

                                        if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {
                                            $output .= "<tr>";
                                        }

                                        // Qty
                                        if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {
                                            $output_qty .= "<td><input type='text' class='wpsc-checkout-qty' size='2' id='wpsc-item-id-" . $item['id'] . "' name='wpsc_item_qty[ ]' value='" . $item['qty'] . "' /></td>";
                                        } else {
                                            $output_qty .= "<input type='text' class='wpsc-checkout-qty' size='2' id='wpsc-item-id-" . $item['id'] . "' name='wpsc_item_qty[ ]' value='" . $item['qty'] . "' />";
                                        }

                                        // Img
                                        if($wpStoreCartOptions['checkoutimages']=='true' && $is_checkout==true) {
                                            if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {$output_pic .='<td>';};$output_pic .= "<img class=\"wpsc-checkout-thumbnail\" src=\"{$item['img']}\" alt=\"".htmlentities($item['name'])."\" style=\"width:{$wpStoreCartOptions['checkoutimagewidth']}px;max-width:{$wpStoreCartOptions['checkoutimagewidth']}px;height:{$wpStoreCartOptions['checkoutimageheight']}px;max-height:{$wpStoreCartOptions['checkoutimageheight']}px;\" />";if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {$output_pic .='</td>';}
                                        }

                                        // Name
                                        if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {
                                            $output_name .= "<td>" . $item['name'] . "<input type='hidden' name='wpsc_item_name[ ]' value='" . $item['name'] . "' /><input type='hidden' name='wpsc_item_id[ ]' value='" . $item['id'] . "' /></td>";
                                        } else {
                                            $output_name .= "";
                                            $output_name .= "" . $item['name'] . "<input type='hidden' name='wpsc_item_name[ ]' value='" . $item['name'] . "' />";
                                            $output_name .= "<input type='hidden' name='wpsc_item_id[ ]' value='" . $item['id'] . "' />";
                                        }

                                        if(@!isset($_SESSION)) {
                                                @session_start();
                                        }

                                        $finalAmount = number_format($item['subtotal'], 2);
                                        $newAmount = number_format($item['subtotal'] - $this->update_coupon($item['id']),2);

                                        if ($newAmount != $finalAmount) {
                                                $tempAmount = '<strike>'.number_format($item['subtotal'],2).'</strike> '. $newAmount;
                                                $finalAmount = $tempAmount;
                                        }

                                        // Price & remove
                                        if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {
                                            if($item['price']==0) {
                                                $output_price .= "<td><span> </span><input type='hidden' name='wpsc_item_price[ ]' value='" . $item['price'] . "' /></td>";
                                                $output_remove .= "<td><a class='wpsc-remove' href='?wpsc_remove=" . $item['id'] . "'>" . $text['remove_link'] . "</a></td>";
                                            } else {
                                                if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) {
                                                    $output_price .= "<td><span>" . $text['currency_symbol'] . $wpStoreCartOptions['logged_out_price'] . $text['currency_symbol_right']."</span><input type='hidden' name='wpsc_item_price[ ]' value='" . $item['price'] . "' /></td>";
                                                } else {
                                                    $output_price .= "<td><span>" . $text['currency_symbol'] . $finalAmount . $text['currency_symbol_right']."</span><input type='hidden' name='wpsc_item_price[ ]' value='" . $item['price'] . "' /></td>";
                                                }
                                                $output_remove .= "<td><a class='wpsc-remove' href='?wpsc_remove=" . $item['id'] . "'>" . $text['remove_link'] . "</a></td>";
                                            }
                                        } else {
                                            if($item['price']==0) {
                                                $output_price .= "<span> </span><input type='hidden' name='wpsc_item_price[ ]' value='" . $item['price'] . "' />";
                                                $output_remove .= "<a class='wpsc-remove' href='?wpsc_remove=" . $item['id'] . "'>" . $text['remove_link'] . "</a><br />";
                                            } else {
                                                if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) {
                                                    $output_price .= "<span>" . $text['currency_symbol'] . $wpStoreCartOptions['logged_out_price'] . $text['currency_symbol_right']. "</span><input type='hidden' name='wpsc_item_price[ ]' value='" . $item['price'] . "' />";
                                                } else {
                                                    $output_price .= "<span>" . $text['currency_symbol'] . $finalAmount . $text['currency_symbol_right']."</span><input type='hidden' name='wpsc_item_price[ ]' value='" . $item['price'] . "' />";
                                                }
                                                $output_remove .= "<a class='wpsc-remove' href='?wpsc_remove=" . $item['id'] . "'>" . $text['remove_link'] . "</a><br />";
                                            }
                                        }

                                        if($wpStoreCartOptions['field_order_0']=='0') {$output.=$output_qty;}
                                        if($wpStoreCartOptions['field_order_0']=='1') {$output.=$output_pic;}
                                        if($wpStoreCartOptions['field_order_0']=='2') {$output.=$output_name;}
                                        if($wpStoreCartOptions['field_order_0']=='3') {$output.=$output_price;}
                                        if($wpStoreCartOptions['field_order_0']=='4') {$output.=$output_remove;}
                                        if($wpStoreCartOptions['field_order_1']=='0') {$output.=$output_qty;}
                                        if($wpStoreCartOptions['field_order_1']=='1') {$output.=$output_pic;}
                                        if($wpStoreCartOptions['field_order_1']=='2') {$output.=$output_name;}
                                        if($wpStoreCartOptions['field_order_1']=='3') {$output.=$output_price;}
                                        if($wpStoreCartOptions['field_order_1']=='4') {$output.=$output_remove;}
                                        if($wpStoreCartOptions['field_order_2']=='0') {$output.=$output_qty;}
                                        if($wpStoreCartOptions['field_order_2']=='1') {$output.=$output_pic;}
                                        if($wpStoreCartOptions['field_order_2']=='2') {$output.=$output_name;}
                                        if($wpStoreCartOptions['field_order_2']=='3') {$output.=$output_price;}
                                        if($wpStoreCartOptions['field_order_2']=='4') {$output.=$output_remove;}
                                        if($wpStoreCartOptions['field_order_3']=='0') {$output.=$output_qty;}
                                        if($wpStoreCartOptions['field_order_3']=='1') {$output.=$output_pic;}
                                        if($wpStoreCartOptions['field_order_3']=='2') {$output.=$output_name;}
                                        if($wpStoreCartOptions['field_order_3']=='3') {$output.=$output_price;}
                                        if($wpStoreCartOptions['field_order_3']=='4') {$output.=$output_remove;}
                                        if($wpStoreCartOptions['field_order_4']=='0') {$output.=$output_qty;}
                                        if($wpStoreCartOptions['field_order_4']=='1') {$output.=$output_pic;}
                                        if($wpStoreCartOptions['field_order_4']=='2') {$output.=$output_name;}
                                        if($wpStoreCartOptions['field_order_4']=='3') {$output.=$output_price;}
                                        if($wpStoreCartOptions['field_order_4']=='4') {$output.=$output_remove;}

                                        if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {
                                            $output .= "</tr>";
                                        }
                                }
                                $cart_is_empty = false;
                            if($wpStoreCartOptions['checkout_xhtml_type']=='table' && $is_checkout==true) {
                                $output .= '</table>';
                            }
                        } else { // // THE CART IS EMPTY

                                $output .= "" . $text['empty_message'] . "<br />";
                                $cart_is_empty = true;
                        }

                        // DISPLAY THE CART FOOTER


                        // IF THIS IS THE CHECKOUT HIDE THE CART CHECKOUT BUTTON
                        if(!isset($src)) {
                                $src = NULL;
                        }
                        if ($is_checkout !== true) {
                            if ($button['checkout']) { $input_type = 'image'; $src = ' src="' . $button['checkout'] . '" alt="' . $text['checkout_button'] . '" title="" ';	}

                            $output .= "<input type='" . $input_type . "' " . $src . "id='wpsc-checkout' name='wpsc_checkout' class='wpsc-button wpsc-checkout ".$wpStoreCartOptions['button_classes_checkout']."' value='" . $text['checkout_button'] . "' /><br />";
                        }

                        if ($is_checkout == true && $wpStoreCartOptions['enablecoupons']=='true') {

                                if(@isset($_SESSION['validcoupon'])) {
                                        $output .= "<div id='wpsc-footer' colspan='3'>{$text['enter_coupon']}<input type=\"text\" value=\"{$_SESSION['validcoupon']}\" name=\"ccoupon\" class=\"wpsc-coupon\" /></div>";
                                } else {
                                        $output .= "<div id='wpsc-footer' colspan='3'>{$text['enter_coupon']}<input type=\"text\" value=\"\" name=\"ccoupon\" class=\"wpsc-coupon\" /></div>";
                                }
                        }

                        $output .= '<br />';
                        
                        // Subtotal
                        if(($wpStoreCartOptions['displaysubtotal']=='true' && $wpscWidgetSettings['iswidget']!='true') || $wpscWidgetSettings['widgetShowSubtotal']=='true' ) {
                            if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) {
                                $output .= "<span id='wpsc-subtotal"; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .="'>" . $text['subtotal'] . ": <strong>" . $text['currency_symbol'] . $wpStoreCartOptions['logged_out_price'] . $text['currency_symbol_right'] ."</strong></span><br />";
                            } else {
                                $output .= "<span id='wpsc-subtotal"; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .="'>" . $text['subtotal'] . ": <strong>" . $text['currency_symbol'] . number_format($this->total,2) . $text['currency_symbol_right'] ."</strong></span><br />";
                            }
                            $output .= '<input type="hidden" name="wpsc_cart_temp_subtotal" id="wpsc_cart_temp_subtotal" value="'.number_format($this->total,2,'.','').'" />';
                        }

                        // Shipping 
                        if(!isset($totalshipping)) {
                            $totalshipping = 0;
                        }                        
                        if($is_checkout == true && $wpStoreCartOptions['storetype']!='Digital Goods Only' && (($wpStoreCartOptions['displayshipping']=='true' && $wpscWidgetSettings['iswidget']!='true')|| $wpscWidgetSettings['widgetShowShipping']=='true') ) {
                            


                            $output .= '<div id="wpsc-shipping-calculation-form'; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .='"> ';
                            $output .= '<span id="wpsc-shipping">'.$text['shipping'].': <span id="wpsc-shipping-amount"><strong>'.$text['currency_symbol'].number_format($totalshipping, 2).$text['currency_symbol_right'].'</strong></span> &nbsp;';
                            $output .= wpscShippingAPIAvailbleServicesDropdown($this->get_contents());
                            $output .= '<input type="hidden" name="wpsc_cart_temp_shipping" id="wpsc_cart_temp_shipping" value="" />';
                            $output .= '</span></div>';
                        }                        
                        
                        // Tax is calculated and displayed here

                        $taxamount = wpscCalculateTaxes($this->total);
                        if($taxamount > 0) {
                            if(($wpStoreCartOptions['displaytaxes']=='true' && ($wpscWidgetSettings['iswidget']!='true') || $wpscWidgetSettings['widgetShowTax']=='true') ) {
                                if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) {
                                    $output .= "<span id='wpsc-tax"; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .="'>" . $text['tax'] . ": <strong>" . $text['currency_symbol'] . $wpStoreCartOptions['logged_out_price'] . $text['currency_symbol_right'] ."</strong></span><br />";
                                } else {
                                    $output .= "<span id='wpsc-tax"; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .="'>" . $text['tax'] . ": <strong>" . $text['currency_symbol'] . number_format($taxamount,2) . $text['currency_symbol_right'] ."</strong></span><br />";
                                }
                            }
                        }
                        $output .= '<input type="hidden" name="wpsc_cart_temp_tax" id="wpsc_cart_temp_tax" value="'.number_format($taxamount,2).'" />';
                            

                        if(($wpStoreCartOptions['displaytotal']=='true' && $wpscWidgetSettings['iswidget']!='true') || $wpscWidgetSettings['widgetShowTotal']=='true' ) {
                            if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) {
                                $output .= "<span id='wpsc-total"; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .="'>" . $text['total'] . ": <strong>" . $text['currency_symbol'] . $wpStoreCartOptions['logged_out_price'] . $text['currency_symbol_right'] ."</strong></span><br />";
                            } else {
                                $output .= "<span id='wpsc-total"; if(isset($wpscWidgetSettings)) {$output .= '-widget';} $output .="'>" . $text['total'] . ": <strong>" . $text['currency_symbol'] . number_format($this->total + $totalshipping + $taxamount,2) . $text['currency_symbol_right'] ."</strong></span><br />";
                            }
                        }
                        $output .= '<input type="hidden" name="wpsc_cart_temp_total" id="wpsc_cart_temp_total" value="'.number_format($this->total + $totalshipping + $taxamount,2).'" />';

                        if(!$cart_is_empty) {
                            if ($button['update']) { $input_type = 'image'; $src = ' src="' . $button['update'] . '" alt="' . $text['update_button'] . '" title="" ';	}
                            $output .= "<input type='" . $input_type . "' " . $src ."name='wpsc_update_cart' value='" . $text['update_button'] . "' class='wpsc-button wpsc-update  ".$wpStoreCartOptions['button_classes_meta']."' />";
                        }
                        $output .= "<div class='wpsc-hide'>";
                        if ($is_checkout == false) {
                                if ($button['empty']) { $input_type = 'image'; $src = ' src="' . $button['empty'] . '" alt="' . $text['empty_button'] . '" title="" ';	}
                                $output .= "<input type='" . $input_type . "' " . $src ."name='wpsc_empty' value='" . $text['empty_button'] . "' class='wpsc-button wpsc-empty ".$wpStoreCartOptions['button_classes_meta']."' />";
                        }

                        $output .= "</div>";


                        // IF THIS IS THE CHECKOUT AND THERE ARE ITEMS IN THE CART THEN DISPLAY CHECKOUT BUTTONS
                        if ($is_checkout == true && !$cart_is_empty) {

                                    $servrequest_uri = $_SERVER['REQUEST_URI'] ;

                                    // HIDDEN INPUT ALLOWS US TO DETERMINE IF WE'RE ON THE CHECKOUT PAGE
                                    // WE NORMALLY CHECK AGAINST REQUEST URI BUT AJAX UPDATE SETS VALUE TO wpsc-relay.php
                                    $output .= "<input type='hidden' id='wpsc-is-checkout' name='wpsc_is_checkout' value='true' />";

                                    // SEND THE URL OF THE CHECKOUT PAGE TO wpsc-gateway.php
                                    // WHEN JAVASCRIPT IS DISABLED WE USE A HEADER REDIRECT AFTER THE UPDATE OR EMPTY BUTTONS ARE CLICKED
                                    $protocol = 'http://'; if (!empty($_SERVER['HTTPS'])) { $protocol = 'https://'; }
                                    $output .= "<input type='hidden' id='wpsc-checkout-page' name='wpsc_checkout_page' value='" . $protocol . $_SERVER['HTTP_HOST'] . $servrequest_uri . "' />";
                                    $output .= '<input type="hidden" name="paymentGateway" id="paymentGateway" value="" />';

                                    if($wpStoreCartOptions['allowqbms']=='true' && $isLoggedIn == true) {

                                        $year1 = date('Y') + 1;
                                        $year2 = date('Y') + 2;
                                        $year3 = date('Y') + 3;
                                        $year4 = date('Y') + 4;
                                        $year5 = date('Y') + 5;
                                        $year6 = date('Y') + 6;
                                        $year7 = date('Y') + 7;
                                        $year8 = date('Y') + 8;
                                        $year9 = date('Y') + 9;

                                        $output .= '<br /><table id="wpsc-creditcard-form">
                                            <tr><td>'.$text['cc_name'].'</td><td><input type="text" name="cc_name_input" id="cc_name_input" value="" /></td></tr>
                                            <tr><td>'.$text['cc_number'].'</td><td><input type="text" name="cc_number_input" id="cc_number_input" value="" /></td></tr>
                                            <tr><td>'.$text['cc_cvv'].'</td><td><input type="text" name="cc_cvv_input" id="cc_cvv_input" value="" /></td></tr>
                                            <tr><td>'.$text['cc_expires'].'</td><td><table><tr><td>'.$text['cc_expires_month'].' <select name="cc_expires_month_input" id="cc_expires_month_input"><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option></select></td><td> '.$text['cc_expires_year'].'</td><td><select name="cc_expires_year_input" id="cc_expires_year_input"><option value="'.date('Y').'">'.date('Y').'</option><option value="'.$year1.'">'.$year1.'</option><option value="'.$year2.'">'.$year2.'</option><option value="'.$year3.'">'.$year3.'</option><option value="'.$year4.'">'.$year4.'</option><option value="'.$year5.'">'.$year5.'</option><option value="'.$year6.'">'.$year6.'</option><option value="'.$year7.'">'.$year7.'</option><option value="'.$year8.'">'.$year8.'</option><option value="'.$year9.'">'.$year9.'</option></select></td></tr></table></td></tr>
                                            <tr><td>'.$text['cc_address'].'</td><td><input type="text" name="cc_address_input" id="cc_address_input" value="" /></td></tr>
                                            <tr><td>'.$text['cc_postalcode'].'</td><td><input type="text" name="cc_postalcode_input" id="cc_postalcode_input" value="" /></td></tr>
                                            <tr><td></td><td><input type="submit" value="'.$text['checkout_button'].'" class="wpsc-button wpsc-checkout-button wpsc-qbmscheckout '.$wpStoreCartOptions['button_classes_checkout'].'" onclick=" jQuery(\'#paymentGateway\').val(\'qbms\');" onsubmit="jQuery(\'#paymentGateway\').val(\'qbms\');"></input></td></tr>
                                        </table>';

                                    }


                                        if($wpStoreCartOptions['allowcheckmoneyorder']=='true' && $isLoggedIn == true) {
                                                if(!isset($_POST['ispaypal'])) {
                                                        $output .= '<input type="submit" value="'.$text['checkout_checkmoneyorder_button'].'" class="wpsc-button wpsc-checkout-button wpsc-checkmoneyordercheckout  '.$wpStoreCartOptions['button_classes_checkout'].'" onclick=" jQuery(\'#paymentGateway\').val(\'checkmoneyorder\');" onsubmit="jQuery(\'#paymentGateway\').val(\'checkmoneyorder\');"></input>';
                                                }
                                        }

                                        if($wpStoreCartOptions['allowpaypal']=='true' && $isLoggedIn == true) {
                                                if(!isset($_POST['ispaypal'])) {
                                                        $output .= '<input type="submit" value="'.$text['checkout_paypal_button'].'" class="wpsc-button wpsc-checkout-button wpsc-paypalcheckout '.$wpStoreCartOptions['button_classes_checkout'].'" onclick=" jQuery(\'#paymentGateway\').val(\'paypal\');" onsubmit="jQuery(\'#paymentGateway\').val(\'paypal\');"></input>';
                                                }
                                        }

                                        if($isLoggedIn == true && !isset($_POST['ispaypal'])) {
                                            $output .= apply_filters ('wpsc_final_checkout_buttons' , '' ); // Filter hook to allow more checkout buttons
                                        }


                                        if($shipping_needs_calculation==true  && $wpStoreCartOptions['storetype']!='Digital Goods Only') {
                                            $output .= '  <script type="text/javascript">
                                                    /* <![CDATA[ */
                                                        jQuery(".wpsc-checkout-button").hide();
                                                    /* ]]> */
                                                    </script>
                                                ';
                                        }

                                }

                        $output .= "</form>";

                    }
                }

		// IF UPDATING AN ITEM, FOCUS ON ITS QTY INPUT AFTER THE CART IS LOADED (DOESN'T SEEM TO WORK IN IE7)
		if (isset($_POST['wpsc_update_item'])) {
                $output .= '<script type="text/javascript">
                                /* <![CDATA[ */
                                jQuery(function(){jQuery("#wpsc-item-id-' . $_POST['item_id'] . '").focus()});
                                /* ]]> */
                            </script>';
                }

		$output .= "</div><!-- END wpsc -->";

                return $output;
		}

                
	}
        
        
if(!function_exists('wpstorecartNeedsToStartSessionsBeforeAnythingElse')) {        
        /**
         *
         * Attempts to start sessions before anything else gets a chance to.  Not a big deal anymore ever since the dragon cart update.
         * 
         * @global type $cart
         * @global string $wpsc_cart_type 
         */
        function wpstorecartNeedsToStartSessionsBeforeAnythingElse() {
                global $cart, $wpsc_cart_type;
                if($wpsc_cart_type == 'session') {
                    if(@!isset($_SESSION)) {
                            @session_start();
                    }
                    if(@!is_object($cart)) {
                        $cart =& $_SESSION['wpsc'];
                        if(@!is_object($cart)) {
                            $cart = new wpsc_shoppingcart();
                        }
                    }
                }
        }
}        
        
add_action('plugins_loaded', 'wpstorecartNeedsToStartSessionsBeforeAnythingElse', 1); // Trys to insure that wpStoreCart is the first plugin that starts a session,  but may not be possible :(        
        
      
?>