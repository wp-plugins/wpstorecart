<?php
/*
Plugin Name: wpStoreCart Free Shipping
Plugin URI: http://wpstorecart.com/
Description: Offer free shipping when an order is over a certain monetary value.
Version: 1.0.0
Author: wpStoreCart, LLC
Author URI: http://wpstorecart.com/
*/
 

// Add custom shipping options
function wpscFreeShippingOverXShippingOptionsFunction() {
    
	$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');


	echo '
	<br /><br />
	Free Shipping On Orders Over _____.
	
	<table class="widefat wpsc5table">
	<thead><tr><th>'.__('Option','wpstorecart').'</th><th class="tableDescription">'.__('Description','wpstorecart').'</th><th>'.__('Value','wpstorecart').'</th></tr></thead><tbody>
	
	<tr><td><p>'.__('Enable Free Shipping Threshold?','wpstorecart').' <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-81235090909" /><div class="tooltip-content" id="example-content-81235090909">'.__('This allows you automatically enable free shipping when the total value of the order meets or exceeds the threshold that you set.','wpstorecart').'</div></p></td>
	<td class="tableDescription"><p>'.__('If set to Yes, will allow customers to have free shipping if they spend a certain amount on an order.','wpstorecart').'</p></td>
	<td><p><label for="enableFreeShippingOverX"><input type="radio" id="enableFreeShippingOverX_yes" name="enableFreeShippingOverX" value="true" '; if ($wpStoreCartOptions['enableFreeShippingOverX'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('Yes','wpstorecart').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enableFreeShippingOverX_no"><input type="radio" id="enableFreeShippingOverX_no" name="enableFreeShippingOverX" value="false" '; if ($wpStoreCartOptions['enableFreeShippingOverX'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('No','wpstorecart').'</label></p>
	</td></tr>
	
	<tr><td><p>'.__('Amount Per Order to Trigger Free Shipping?','wpstorecart').' <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-812359879879090" /><div class="tooltip-content" id="example-content-812359879879090">'.__('The amount that the customer needs to meet or exceed in order to trigger free shipping.','wpstorecart').'</div></p></td>
	<td class="tableDescription"><p>'.__('This is the free shipping threshold that must be met or exceeded, in order to enable free shipping for the order.','wpstorecart').'</p></td>
	<td>
		'.$wpStoreCartOptions['currency_symbol'].'<input type="text" name="FreeShippingOverXThreshold" value="'; _e(apply_filters('format_to_edit',$wpStoreCartOptions['FreeShippingOverXThreshold']), 'wpStoreCart'); echo'" /> '.$wpStoreCartOptions['currency_symbol_right'].'
	</td></tr>	
	
	</table>
        <br /><br />
	';
	
}
add_action('wpsc_admin_shipping_options_page', 'wpscFreeShippingOverXShippingOptionsFunction');


// Save custom options
function wpscFreeShippingOverXSaveShippingFunction() {
    global $wpdb;
	wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
	
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        if(!isset($wpStoreCartOptions['enableFreeShippingOverX'])) { // If the setting hasn't been initialized previously, give it a default value
            $wpStoreCartOptions['enableFreeShippingOverX'] = 'false'; // Default value.
            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
        }
        if(isset($_POST['enableFreeShippingOverX'])) { // Here's where you update the value of the variable
            $wpStoreCartOptions['enableFreeShippingOverX'] = esc_sql($_POST['enableFreeShippingOverX']); // Changes the variable in the settings
            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
        }

        if(!isset($wpStoreCartOptions['FreeShippingOverXThreshold'])) { // If the setting hasn't been initialized previously, give it a default value
            $wpStoreCartOptions['FreeShippingOverXThreshold'] = '100.00'; // Default value.
            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
        }
        if(isset($_POST['FreeShippingOverXThreshold'])) { // Here's where you update the value of the variable
            $wpStoreCartOptions['FreeShippingOverXThreshold'] = esc_sql($_POST['FreeShippingOverXThreshold']); // Changes the variable in the settings
            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
        }    


	update_option('wpStoreCartAdminOptions', $wpStoreCartOptions); 
	
}
add_action('wpsc_admin_save_settings', 'wpscFreeShippingOverXSaveShippingFunction');



// ========== Free Shipping Over X =============
function wpscFreeShippingOverXCheckbox() {
	echo wpscShippingAPIAddToChecklist($_GET['keytoedit'], 'enableFreeShippingOverX', 'FreeShippingOverX', __('Enable Free Shipping Threshold?', 'wpstorecart'));
}
add_action('wpsc_admin_shipping_product_checkboxes', 'wpscFreeShippingOverXCheckbox');

// Provides a way to save the selection
function wpscFreeShippingOverXSaveCheckbox() {
	wpscProductToggleShippingService($_POST['wpsc-keytoedit'], 'FreeShippingOverX');
}
add_action('wpsc_admin_save_product', 'wpscFreeShippingOverXSaveCheckbox');

// This is for the checkout page, it will output allowed UPS options
function wpscShippingAPIGetOption_FreeShippingOverX($cart_contents) {
	$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        $total_cart_price = 0;
        foreach($cart_contents as $cart_content) {
            $total_cart_price = $total_cart_price + ($cart_content[1] * $cart_content[2]); // qty * price
        }
        
        $output = null;
				
        if(floatval($total_cart_price) >= floatval($wpStoreCartOptions['FreeShippingOverXThreshold']) ) {
                $output .= '<option value="FreeShippingOverX[FREE]">Free Shipping ['.number_format('0',2).']</option>';
        } else {
                $output .= NULL;
        }

	
	return $output;
}

// This is for verifying on wpsc-gateway.php
function wpscShippingAPIFinalGateway_FreeShippingOverX($cart_contents, $producttype) {
	$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        $total_cart_price = 0;
        foreach($cart_contents as $cart_content) {
            $total_cart_price = $total_cart_price + ($cart_content[1] * $cart_content[2]); // qty * price
        }
        
        if(floatval($total_cart_price) >= floatval($wpStoreCartOptions['FreeShippingOverXThreshold']) ) {
            return 0;
        }
}

function wpscShippingAPICheckIfServiceEnabled_FreeShippingOverX() {
	    $devOptions = get_option('wpStoreCartAdminOptions'); 
            if ($devOptions['enableFreeShippingOverX'] == "true") {
                    return true;
            }
            if ($devOptions['enableFreeShippingOverX'] == "false") {
                    return false;
            }		
}

?>