<?php

global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpStoreCart;

if (isset($wpStoreCart)) {
	$devOptions = $wpStoreCart->getAdminOptions();

	
	///////////////////////////////////////////////////////////////////////
	// REQUIRED SETTINGS

	// THE HTML NAME ATTRIBUTES USED IN YOUR ADD-TO-CART FORM
	$wpsc['item_id']		= 'my-item-id';			// ITEM ID
	$wpsc['item_name']		= 'my-item-name';		// ITEM NAME
	$wpsc['item_price']             = 'my-item-price';		// ITEM PRICE
	$wpsc['item_qty']		= 'my-item-qty';		// ITEM QTY
	$wpsc['item_add']		= 'my-add-button';		// ADD-TO-CART BUTTON
        $wpsc['item_shipping']          = 'my-item-shipping';           // SHIPPING

	// PATH TO THE DIRECTORY CONTAINING WPSC FILES
	$wpsc['path'] =  WP_PLUGIN_URL.'/wpstorecart/php/wpsc-1.1/wpsc/';

	// THE PATH AND FILENAME WHERE SHOPPING CART CONTENTS SHOULD BE POSTED WHEN A VISITOR CLICKS THE CHECKOUT BUTTON
	// USED AS THE ACTION ATTRIBUTE FOR THE SHOPPING CART FORM
	$wpsc['form_action']	= $devOptions['checkoutpageurl'];

	// YOUR PAYPAL SECURE MERCHANT ACCOUNT ID
	$wpsc['paypal_id']		= $devOptions['paypalemail'];


	///////////////////////////////////////////////////////////////////////
	// OPTIONAL SETTINGS

	// OVERRIDE DEFAULT CART TEXT
	$wpsc['text']['cart_title']				= $devOptions['cart_title'];		// Shopping Cart
	$wpsc['text']['single_item']				= $devOptions['single_item'];		// Item
	$wpsc['text']['multiple_items']			= $devOptions['multiple_items'];		// Items
	$wpsc['text']['currency_symbol']			= $devOptions['currency_symbol'];		// $
	$wpsc['text']['subtotal']					= $devOptions['subtotal'];		// Subtotal
        $wpsc['text']['total']					= $devOptions['total'];		//Total
        $wpsc['text']['shipping']					= $devOptions['shipping'];		// Shipping
        $wpsc['text']['login']					= $devOptions['login'];
        $wpsc['text']['logout']					= $devOptions['logout'];
        $wpsc['text']['register']					= $devOptions['register'];
        $wpsc['text']['username']					= $devOptions['username'];
        $wpsc['text']['password']					= $devOptions['password'];
        $wpsc['text']['email']					= $devOptions['email'];
        $wpsc['text']['required_symbol']					= $devOptions['required_symbol'];
        $wpsc['text']['required_help']					= $devOptions['required_help'];
        $wpsc['text']['calculateshipping']					= $devOptions['calculateshipping'];

        $wpsc['text']['currency_symbol']					= $devOptions['currency_symbol'];
        $wpsc['text']['currency_symbol_right']					= $devOptions['currency_symbol_right'];		

	$wpsc['text']['update_button']				= $devOptions['update_button'];		// update
	$wpsc['text']['checkout_button']			= $devOptions['checkout_button'];
        $wpsc['text']['guestcheckout']			= $devOptions['guestcheckout'];// checkout
	$wpsc['text']['checkout_paypal_button']	= $devOptions['checkout_paypal_button'];		// Checkout with PayPal
        @$wpsc['text']['checkout_authorizenet_button']	= $devOptions['checkout_authorizenet_button'];
        @$wpsc['text']['checkout_2checkout_button']	= $devOptions['checkout_2checkout_button'];
        @$wpsc['text']['checkout_checkmoneyorder_button']	= $devOptions['checkout_checkmoneyorder_button'];
        @$wpsc['text']['checkout_libertyreserve_button']	= $devOptions['checkout_libertyreserve_button'];
	$wpsc['text']['remove_link']				= $devOptions['remove_link'];		// remove
	$wpsc['text']['empty_button']				= $devOptions['empty_button'];		// empty
	$wpsc['text']['empty_message']				= $devOptions['empty_message'];		// Your cart is empty!
	$wpsc['text']['item_added_message']		= $devOptions['item_added_message'];		// Item added!
        $wpsc['text']['enter_coupon']		= $devOptions['enter_coupon'];

	$wpsc['text']['price_error']				= $devOptions['price_error'];		// Invalid price format!
	$wpsc['text']['quantity_error']			= $devOptions['quantity_error'];		// Item quantities must be whole numbers!
	$wpsc['text']['checkout_error']			=$devOptions['checkout_error'];		// Your order could not be processed!
        $wpsc['text']['add_to_cart']			=$devOptions['add_to_cart'];		// Add to cart

	// OVERRIDE THE DEFAULT BUTTONS WITH YOUR IMAGES BY SETTING THE PATH FOR EACH IMAGE
	$wpsc['button']['checkout']				= '';
	$wpsc['button']['paypal_checkout']			= '';
	$wpsc['button']['update']					= '';
	$wpsc['button']['empty']					= '';
}
	
?>