<?php

// Add custom shipping options
#if !function_exists('wpscFreeShippingOverXShippingOptionsFunction') {
	function wpscXFreeShippingOverXXShippingOptionsFunction() {
		
		$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');


		echo '
		<br /><br />
		<h2>Free Shipping On Orders Over _____.</h2>
		
		<table class="widefat wpsc5table">
		<thead><tr><th>'.__('Option','wpstorecart').'</th><th class="tableDescription">'.__('Description','wpstorecart').'</th><th>'.__('Value','wpstorecart').'</th></tr></thead><tbody>
		
		<tr><td><p>'.__('Enable Free Shipping Threshold?','wpstorecart').' <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-81235090909" /><div class="tooltip-content" id="example-content-81235090909">'.__('This allows you automatically enable free shipping when the total value of the order meets or exceeds the threshold that you set.','wpstorecart').'</div></p></td>
		<td class="tableDescription"><p>'.__('If set to Yes, will allow customers to have free shipping if they spend a certain amount on an order.','wpstorecart').'</p></td>
		<td><p><label for="enableFreeShippingOverXX"><input type="radio" id="enableFreeShippingOverXX_yes" name="enableFreeShippingOverXX" value="true" '; if ($wpStoreCartOptions['enableFreeShippingOverXX'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('Yes','wpstorecart').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enableFreeShippingOverXX_no"><input type="radio" id="enableFreeShippingOverXX_no" name="enableFreeShippingOverXX" value="false" '; if ($wpStoreCartOptions['enableFreeShippingOverXX'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('No','wpstorecart').'</label></p>
		</td></tr>
		
		<tr><td><p>'.__('Amount Per Order to Trigger Free Shipping?','wpstorecart').' <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-812359879879090" /><div class="tooltip-content" id="example-content-812359879879090">'.__('The amount that the customer needs to meet or exceed in order to trigger free shipping.','wpstorecart').'</div></p></td>
		<td class="tableDescription"><p>'.__('This is the free shipping threshold that must be met or exceeded, in order to enable free shipping for the order.','wpstorecart').'</p></td>
		<td>
			'.$wpStoreCartOptions['currency_symbol'].'<input type="text" name="FreeShippingOverXXThreshold" value="'; _e(apply_filters('format_to_edit',$wpStoreCartOptions['FreeShippingOverXXThreshold']), 'wpStoreCart'); echo'" /> '.$wpStoreCartOptions['currency_symbol_right'].'
		</td></tr>	
		
		</table>
			<br /><br />
		';
		
	}
	add_action('wpsc_admin_shipping_options_page', 'wpscXFreeShippingOverXXShippingOptionsFunction');


	// Save custom options
	function wpscXFreeShippingOverXXSaveShippingFunction() {
		global $wpdb;
		wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
		
			$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

			if(!isset($wpStoreCartOptions['enableFreeShippingOverXX'])) { // If the setting hasn't been initialized previously, give it a default value
				$wpStoreCartOptions['enableFreeShippingOverXX'] = 'false'; // Default value.
				update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
			}
			if(isset($_POST['enableFreeShippingOverXX'])) { // Here's where you update the value of the variable
				$wpStoreCartOptions['enableFreeShippingOverXX'] = esc_sql($_POST['enableFreeShippingOverXX']); // Changes the variable in the settings
				update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
			}

			if(!isset($wpStoreCartOptions['FreeShippingOverXXThreshold'])) { // If the setting hasn't been initialized previously, give it a default value
				$wpStoreCartOptions['FreeShippingOverXXThreshold'] = '100.00'; // Default value.
				update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
			}
			if(isset($_POST['FreeShippingOverXXThreshold'])) { // Here's where you update the value of the variable
				$wpStoreCartOptions['FreeShippingOverXXThreshold'] = esc_sql($_POST['FreeShippingOverXXThreshold']); // Changes the variable in the settings
				update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
			}    


		update_option('wpStoreCartAdminOptions', $wpStoreCartOptions); 
		
	}
	add_action('wpsc_admin_save_settings', 'wpscXFreeShippingOverXXSaveShippingFunction');



	// ========== Free Shipping Over X =============
	function wpscXFreeShippingOverXXCheckbox() {
		echo wpscShippingAPIAddToChecklist($_GET['keytoedit'], 'enableFreeShippingOverXX', 'FreeShippingOverXX', __('Enable Free Shipping Threshold?', 'wpstorecart'));
	}
	add_action('wpsc_admin_shipping_product_checkboxes', 'wpscXFreeShippingOverXXCheckbox');

	// Provides a way to save the selection
	function wpscXFreeShippingOverXXSaveCheckbox() {
		wpscProductToggleShippingService($_POST['wpsc-keytoedit'], 'FreeShippingOverXX');
	}
	add_action('wpsc_admin_save_product', 'wpscXFreeShippingOverXXSaveCheckbox');

	// This is for the checkout page, it will output allowed UPS options
	function wpscShippingAPIGetOption_FreeShippingOverXX($cart_contents) {
		$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

			$total_cart_price = 0;
			foreach($cart_contents as $cart_content) {
				$total_cart_price = $total_cart_price + ($cart_content[1] * $cart_content[2]); // qty * price
			}
			
			$output = null;
					
			if(floatval($total_cart_price) >= floatval($wpStoreCartOptions['FreeShippingOverXXThreshold']) ) {
					$output .= '<option value="FreeShippingOverXX[FREE]">Free Shipping ['.number_format('0',2).']</option>';
			} else {
					$output .= NULL;
			}

		
		return $output;
	}

	// This is for verifying on wpsc-gateway.php
	function wpscShippingAPIFinalGateway_FreeShippingOverXX($cart_contents, $producttype) {
		$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
			$total_cart_price = 0;
			foreach($cart_contents as $cart_content) {
				$total_cart_price = $total_cart_price + ($cart_content[1] * $cart_content[2]); // qty * price
			}
			
			if(floatval($total_cart_price) >= floatval($wpStoreCartOptions['FreeShippingOverXXThreshold']) ) {
				return 0;
			}
	}

	function wpscShippingAPICheckIfServiceEnabled_FreeShippingOverXX() {
			$devOptions = get_option('wpStoreCartAdminOptions'); 
				if ($devOptions['enableFreeShippingOverXX'] == "true") {
						return true;
				}
				if ($devOptions['enableFreeShippingOverXX'] == "false") {
						return false;
				}		
	}

#}

?>