<?php
/*
Plugin Name: wpStoreCart 3+ Authorize.NET SIM Payment Gateway
Plugin URI: http://wpstorecart.com/
Description: Adds the ability to accept Authorize.NET payments on your wpStoreCart 3 powered stores.
Version: 2.0.1
Author: wpStoreCart, LLC
Author URI: http://wpstorecart.com/
*/

/*
 * How to create payment gateway options on the wp-admin > wpStoreCart > Settings > Payment > tab:
 */
function wpscAuthorizeNetSimSettingsFunction() {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();

    echo '
    <h2>Authorize.NET Gateway</h2>
    <table class="widefat wpsc5table">
    <thead><tr><th>'.__('Option','wpstorecart').'</th><th>'.__('Description','wpstorecart').'</th><th>'.__('Value','wpstorecart').'</th></tr></thead><tbody>

    <tr><td><p>Accept Authorize.NET Payments? <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-70099" /><div class="tooltip-content" id="example-content-70099">Want to accept Authorize.NET payments?  Then set this to yes!</div></p></td>
    <td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using Authorize.NET.</p></td>
    <td><p><label for="allowauthorizenet"><input type="radio" id="allowauthorizenet_yes" name="allowauthorizenet" value="true" '; if ($wpStoreCartOptions['allowauthorizenet'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('Yes','wpstorecart').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowauthorizenet_no"><input type="radio" id="allowauthorizenet_no" name="allowauthorizenet" value="false" '; if ($wpStoreCartOptions['allowauthorizenet'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('No','wpstorecart').'</label></p>
    </td></tr>

    <tr><td><p>Turn on Authorize.NET Test Mode? <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-81111" /><div class="tooltip-content" id="example-content-81111">If you need to do tests with Authorize.NET then set this to yes.  Warning!  These payments are not real, so any purchases made under the Sandbox will result in live sales with no money!  You can delete test orders in the Orders tab above.</div></p></td>
    <td class="tableDescription"><p>If set to Yes, all transactions are tests done using Authorize.NET.</p></td>
    <td><p><label for="authorizenettestmode"><input type="radio" id="authorizenettestmode_yes" name="authorizenettestmode" value="true" '; if ($wpStoreCartOptions['authorizenettestmode'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('Yes','wpstorecart').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="authorizenettestmode_no"><input type="radio" id="authorizenettestmode_no" name="authorizenettestmode" value="false" '; if ($wpStoreCartOptions['authorizenettestmode'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('No','wpstorecart').'</label></p>
    </td></tr>

    <tr><td><p>API Login ID <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-9666" /><div class="tooltip-content" id="example-content-9666">The Authorize.NET API Login ID assigned to you.  </div></p></td>
    <td class="tableDescription"><p>The API Login ID you are assigned to use access your Authorize.NET account.</p></td>
    <td><input type="text" name="authorizenetemail" value="'; _e(apply_filters('format_to_edit',$wpStoreCartOptions['authorizenetemail']), 'wpStoreCart'); echo'" />
    </td></tr>

    <tr><td><p>Secret Key <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-9667" /><div class="tooltip-content" id="example-content-9667">The Authorize.NET secret key which is used to authenticate your shop.</div></p></td>
    <td class="tableDescription"><p>The Authorize.NET secret key md5 hash value.</p></td>
    <td><input type="text" name="authorizenetsecretkey" value="'; _e(apply_filters('format_to_edit',$wpStoreCartOptions['authorizenetsecretkey']), 'wpStoreCart'); echo'" />
    </td></tr>
    </table>
    <br style="clear:both;" /><br />

    ';
}

add_action('wpsc_admin_payment_options_page', 'wpscAuthorizeNetSimSettingsFunction');


/**
 * How to save the custom settings you created for your payment gateway
 */
function wpscAuthorizeNetSimSaveFunction() {
    global $wpstorecart_settings_obj, $wpdb;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();

    if(isset($_POST['allowauthorizenet'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['allowauthorizenet'] = esc_sql($_POST['allowauthorizenet']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }
	
    if(isset($_POST['authorizenettestmode'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['authorizenettestmode'] = esc_sql($_POST['authorizenettestmode']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
	
    if(isset($_POST['authorizenetemail'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['authorizenetemail'] = esc_sql($_POST['authorizenetemail']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
	
    if(isset($_POST['authorizenetsecretkey'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['authorizenetsecretkey'] = esc_sql($_POST['authorizenetsecretkey']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }		
}

add_action('wpsc_admin_save_settings', 'wpscAuthorizeNetSimSaveFunction');



/*
 * Default custom settings values samples
 */
function wpscAuthorizeNetSimDefaultValuesFunction() {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();
    
    if(!isset($wpStoreCartOptions['allowauthorizenet'])) { // If the setting hasn't been initialized previously, give it a default value
        $wpStoreCartOptions['allowauthorizenet'] = 'false'; // Default value.
		$wpStoreCartOptions['authorizenettestmode'] = 'false'; // Default value.
		$wpStoreCartOptions['authorizenetemail'] = ''; // Default value.
		$wpStoreCartOptions['authorizenetsecretkey'] = ''; // Default value.
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    }    
}

add_action('wpsc_admin', 'wpscAuthorizeNetSimDefaultValuesFunction');



function wpscAuthorizeNetSimCheckoutButtonFunction($output) {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();    
    
    if($wpStoreCartOptions['allowauthorizenet']=='true') {
            $output .= '<input type="submit" value="'.$wpStoreCartOptions['checkout_authorizenet_button'].'" class="wpsc-button wpsc-checkout-button wpsc-authorizenetcheckout '.$wpStoreCartOptions['button_classes_checkout'].'" onclick=" jQuery(\'#paymentGateway\').val(\'authorize.net\');" onsubmit=" jQuery(\'#paymentGateway\').val(\'authorize.net\');"></input>';
    }
    
    return $output;
}

add_filter('wpsc_final_checkout_buttons','wpscAuthorizeNetSimCheckoutButtonFunction', 10, 1);



include_once(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/payment/PaymentGateway.php');
function process_wpscAuthorizeNetSim_payments() {
    global $wpscPaymentGateway, $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions(); // Grabs the settings    
    

    // Authorize.net
    if($wpscPaymentGateway['payment_gateway'] == 'authorize.net') { 
        include_once(WP_PLUGIN_DIR.'/wpsc-authorize-net/authorize.net/gateway.authorize.net.php');
        include_once(WP_PLUGIN_DIR.'/wpsc-authorize-net/saStoreCartPro/anet_php_sdk/AuthorizeNet.php');
        $myAuthorize = new Authorize();
        $fp_timestamp = time();
        $fp_sequence = $wpscPaymentGateway['order_id'] . time(); // Enter an invoice or other unique number.
        $fingerprint = AuthorizeNetSIM_Form::getFingerprint($wpStoreCartOptions['authorizenetemail'],$wpStoreCartOptions['authorizenetsecretkey'], $wpscPaymentGateway['final_price'], $fp_sequence, $fp_timestamp);

        $myAuthorize->setUserInfo($wpStoreCartOptions['authorizenetemail'], $wpStoreCartOptions['authorizenetsecretkey']);
        $myAuthorize->addField('x_receipt_link_url', $wpscPaymentGateway['success_permalink']);
        $myAuthorize->addField('x_relay_url', plugins_url().'/wpsc-authorize-net/authorize.net/ipn.authorize.net.php');
        $myAuthorize->addField('x_description', $wpscPaymentGateway['cart_description']);
        $myAuthorize->addField('x_amount', $wpscPaymentGateway['final_price_with_discounts']);
        $myAuthorize->addField('x_invoice_num', $wpscPaymentGateway['order_id']);
        $myAuthorize->addField('x_cust_id', $wpscPaymentGateway['customer_user_id']);
        $myAuthorize->addField('x_fp_hash', $fingerprint);
        $myAuthorize->addField('x_fp_timestamp', $fp_timestamp);
        $myAuthorize->addField('x_fp_sequence', $fp_sequence);
        $myAuthorize->addField('x_version', '3.1');
        if($wpStoreCartOptions['authorizenettestmode']=='true') {
            $myAuthorize->enableTestMode();
        }
        $myAuthorize->submitPayment();        

    }	
	
	
	
}

add_action('wpsc_process_payment_gateways', 'process_wpscAuthorizeNetSim_payments');


?>