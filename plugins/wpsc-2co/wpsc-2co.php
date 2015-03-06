<?php
/*
Plugin Name: wpStoreCart 3+ 2Checkout Payment Gateway
Plugin URI: http://wpstorecart.com/
Description: Adds the ability to accept 2Checkout payments on your wpStoreCart 3 powered stores.
Version: 2.0.1
Author: wpStoreCart, LLC
Author URI: http://wpstorecart.com/
*/

/*
 * How to create payment gateway options on the wp-admin > wpStoreCart > Settings > Payment > tab:
 */
function wpsc2COSettingsFunction() {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();

    echo '
    <h2>2CheckOut Gateway</h2>
    <table class="widefat">
    <thead><tr><th>'.__('Option','wpstorecart').'</th><th>'.__('Description','wpstorecart').'</th><th>'.__('Value','wpstorecart').'</th></tr></thead><tbody>

    <tr><td><p>Accept 2CheckOut Payments? <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-700992" /><div class="tooltip-content" id="example-content-700992">Want to accept 2CheckOut payments?  Then set this to yes!</div></p></td>
    <td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using 2CheckOut.</p></td>
    <td><p><label for="allow2checkout"><input type="radio" id="allow2checkout_yes" name="allow2checkout" value="true" '; if ($wpStoreCartOptions['allow2checkout'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('Yes','wpstorecart').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow2checkout_no"><input type="radio" id="allow2checkout_no" name="allow2checkout" value="false" '; if ($wpStoreCartOptions['allow2checkout'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('No','wpstorecart').'</label></p>
    </td></tr>

    <tr><td><p>Turn on 2CheckOut Test Mode? <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-8111166" /><div class="tooltip-content" id="example-content-8111166">If you need to do tests with 2CheckOut then set this to yes.  Warning!  These payments are not real, so any purchases made under the Sandbox will result in live sales with no money!  You can delete test orders in the Orders tab above.</div></p></td>
    <td class="tableDescription"><p>If set to Yes, all transactions are tests done using 2CheckOut.</p></td>
    <td><p><label for="2checkouttestmode"><input type="radio" id="2checkouttestmode_yes" name="2checkouttestmode" value="true" '; if ($wpStoreCartOptions['2checkouttestmode'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('Yes','wpstorecart').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="2checkouttestmode_no"><input type="radio" id="2checkouttestmode_no" name="2checkouttestmode" value="false" '; if ($wpStoreCartOptions['2checkouttestmode'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('No','wpstorecart').'</label></p>
    </td></tr>

    <tr><td><p>2CheckOut Vendor ID <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-966644" /><div class="tooltip-content" id="example-content-966644">The 2CheckOut Vendor ID assigned to you.  </div></p></td>
    <td class="tableDescription"><p>The 2CheckOut Vendor ID you are assigned to use access your 2CheckOut account.</p></td>
    <td><input type="text" name="2checkoutemail" value="'; _e(apply_filters('format_to_edit',$wpStoreCartOptions['2checkoutemail']), 'wpStoreCart'); echo'" />
    </td></tr>

    </table>
    <br style="clear:both;" /><br />

    ';
}

add_action('wpsc_admin_payment_options_page', 'wpsc2COSettingsFunction');


/**
 * How to save the custom settings you created for your payment gateway
 */
function wpsc2COSaveFunction() {
    global $wpstorecart_settings_obj, $wpdb;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();

    if(isset($_POST['allow2checkout'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['allow2checkout'] = esc_sql($_POST['allow2checkout']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }
	
    if(isset($_POST['2checkouttestmode'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['2checkouttestmode'] = esc_sql($_POST['2checkouttestmode']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
	
    if(isset($_POST['2checkoutemail'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['2checkoutemail'] = esc_sql($_POST['2checkoutemail']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
}

add_action('wpsc_admin_save_settings', 'wpsc2COSaveFunction');



/*
 * Default custom settings values samples
 */
function wpsc2CODefaultValuesFunction() {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();
    
    if(!isset($wpStoreCartOptions['allow2checkout'])) { // If the setting hasn't been initialized previously, give it a default value
        $wpStoreCartOptions['allow2checkout'] = 'false'; // Default value.
		$wpStoreCartOptions['2checkouttestmode'] = 'false'; // Default value.
		$wpStoreCartOptions['2checkoutemail'] = ''; // Default value.
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    }    
}

add_action('wpsc_admin', 'wpsc2CODefaultValuesFunction');



function wpsc2COCheckoutButtonFunction($output) {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();    
    
    if($wpStoreCartOptions['allow2checkout']=='true') {
        $output .= '<input type="submit" value="'.$wpStoreCartOptions['checkout_2checkout_button'].'" class="wpsc-button wpsc-checkout-button wpsc-2checkoutcheckout '.$wpStoreCartOptions['button_classes_checkout'].'" onclick=" jQuery(\'#paymentGateway\').val(\'2checkout\');" onsubmit="jQuery(\'#paymentGateway\').val(\'2checkout\');"></input>';
    }
    
    return $output;
}

add_filter('wpsc_final_checkout_buttons','wpsc2COCheckoutButtonFunction', 10, 1);



include_once(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/payment/PaymentGateway.php');
function process_2co_payments() {
    global $wpscPaymentGateway, $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions(); // Grabs the settings    
    
	    // 2CheckOut
    if(@$wpscPaymentGateway['payment_gateway'] == '2checkout') {
        include_once(WP_PLUGIN_DIR.'/wpsc-2co/2co/gateway.2co.php');
        $my2CO = new TwoCo();
        $my2CO->addField('sid', $wpStoreCartOptions['2checkoutemail']);
        $my2CO->addField('cart_order_id', $wpscPaymentGateway['order_id']);
        $my2CO->addField('total', $wpscPaymentGateway['final_price_with_discounts']);
        $my2CO->addField('x_receipt_link_url', plugins_url().'/wpsc-2co/2co/ipn.2co.php');
        $my2CO->addField('tco_currency', 'USD');
        $my2CO->addField('custom', $wpscPaymentGateway['customer_user_id']);
        if($wpStoreCartOptions['2checkouttestmode']=='true') {
            $my2CO->enableTestMode();
        }
        $my2CO->submitPayment();
    } 
	
}

add_action('wpsc_process_payment_gateways', 'process_2co_payments');


?>