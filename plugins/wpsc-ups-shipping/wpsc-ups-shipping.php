<?php
/*
Plugin Name: UPS Shipping For wpStoreCart
Plugin URI: http://wpstorecart.com/
Description: Adds UPS shipping to wpStoreCart powered stores.
Version: 1.0.0
Author: wpStoreCart, LLC
Author URI: http://wpstorecart.com/
*/


// Parcel rate
if(!function_exists('wpscUPSParcelRate')) {
    function wpscUPSParcelRate($destinationzip, $cart_contents, $producttype='2DA') {
		global $wpdb;

		$total_quote = 0;
        $devOptions = get_option('wpStoreCartAdminOptions'); 


			if(@!isset($_SESSION)) {
				@session_start();
			}			
			@$country_code = wpscCountryCodes($_SESSION['wpsc_taxcountries']);
			
			require_once(WP_PLUGIN_DIR . '/wpstorecart/plugins/wpsc-ups-shipping/class.shipping.php');
			
			foreach($cart_contents as $cart_content) {
			
				if(@isset($cart_content[0])) {
					$key = $cart_content[0];
				}		
				if(@isset($cart_content['id'])) {
					$key = $cart_content['id'];
				}
				
				if(@isset($cart_content[1])) {
					$qty = $cart_content[1];
				}		
				if(@isset($cart_content['qty'])) {
					$qty = $cart_content['qty'];
				}			

				@$results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_packages` WHERE `productkey`='{$key}';", ARRAY_A);
				if(@isset($results[0]['weight'])) {
					$rate = new Ups;
					$rate->upsProduct($producttype); // See upsProduct() function for codes
					$rate->origin($devOptions['shipping_zip_origin'], "US"); // Use ISO country codes!
					$rate->dest($destinationzip, $country_code); // Use ISO country codes!
					$rate->rate("CC"); // See the rate() function for codes
					$rate->container("CP"); // See the container() function for codes
					@$rate->weight($results[0]['weight']);
					$rate->rescom("DET"); // See the rescom() function for codes
					$quote = $rate->getQuote(); 
					if(trim($quote)!='error') {
						$final_quote = $quote * $qty; //Quote X Quantity
						$total_quote = $total_quote + $final_quote;
						$quote = 0;
						$final_quote = 0;
					} else {
						$total_quote=$quote;
					}
				}
			}
		
		
        return $total_quote;     

    }
}




// Add custom shipping options
function wpscUPSShippingOptionsFunction() {
    
	$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

	if (@!extension_loaded('curl')) {
		$curl_is_disabled = true;
	} else {
		if (@!function_exists('curl_init')) {
			$curl_is_disabled = true;
		} else {
			$curl_is_disabled = false;
		}
	}	
	
	echo '
	<br /><br />
	<h2>'.__('UPS Shipping Integration','wpstorecart').'</h2>
	
	<table class="widefat wpsc5table">
	<thead><tr><th>'.__('Option','wpstorecart').'</th><th class="tableDescription">'.__('Description','wpstorecart').'</th><th>'.__('Value','wpstorecart').'</th></tr></thead><tbody>
	
	<tr><td><p>'.__('Enable UPS Shipping?','wpstorecart').' <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-81235" /><div class="tooltip-content" id="example-content-81235">'.__('This allows you to ship via UPS and allows the customer to calculate the shipping rates before purchase.','wpstorecart').'</div></p></td>
	<td class="tableDescription"><p>'.__('If set to Yes, will allow customers to select UPS as a shipping option and will give shipping price quotes for UPS.','wpstorecart').'</p></td>
	<td><p><label for="enableups"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enableups_yes" name="enableups" value="true" '; if ($wpStoreCartOptions['enableups'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('Yes','wpstorecart').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enableups_no"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enableups_no" name="enableups" value="false" '; if ($wpStoreCartOptions['enableups'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('No','wpstorecart').'</label></p>
	</td></tr>
	
	<tr><td><p>'.__('What UPS Products Are Available?','wpstorecart').' <img src="'.plugins_url() . '/wpstorecart/images/help.png" class="tooltip-target" id="example-target-81235987987" /><div class="tooltip-content" id="example-content-81235987987">'.__('Choose what UPS shipping options are available for products that support UPS shipping.','wpstorecart').'</div></p></td>
	<td class="tableDescription"><p>'.__('Choose the UPS shipping products you wish to have available.','wpstorecart').'</p></td>
	<td>
		<table>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_0" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_0']) {echo 'checked="checked"';} echo ' value="1DM" /> '.__('Next Day Air Early AM','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_1" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_1']) {echo 'checked="checked"';} echo ' value="1DA" /> '.__('Next Day Air','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_2" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_2']) {echo 'checked="checked"';} echo ' value="1DP" /> '.__('Next Day Air Saver','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_3" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_3']) {echo 'checked="checked"';} echo ' value="2DM" /> '.__('2nd Day Air Early AM','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_4" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_4']) {echo 'checked="checked"';} echo ' value="2DA" /> '.__('2nd Day Air','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_5" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_5']) {echo 'checked="checked"';} echo ' value="3DS" /> '.__('3 Day Select','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_6" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_6']) {echo 'checked="checked"';} echo ' value="GND" /> '.__('Ground','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_7" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_7']) {echo 'checked="checked"';} echo ' value="STD" /> '.__('Canada Standard','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_8" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_8']) {echo 'checked="checked"';} echo ' value="XPR" /> '.__('Worldwide Express','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_9" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_9']) {echo 'checked="checked"';} echo ' value="XDM" /> '.__('Worldwide Express Plus','wpstorecart').' </label></td></tr>
			<tr><td><label> <input type="checkbox" name="wpsc_ups_shipping_products_10" '; if($wpStoreCartOptions['wpsc_ups_shipping_products_10']) {echo 'checked="checked"';} echo ' value="XPD" /> '.__('Worldwide Expedited','wpstorecart').' </label></td></tr>
		</table>
	</td></tr>	
	
	</table>
	';
	
}
add_action('wpsc_admin_shipping_options_page', 'wpscUPSShippingOptionsFunction');




// Save custom options
function wpscUPSSaveShippingFunction() {
    global $wpdb;
	wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
	
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
	
    if(!isset($wpStoreCartOptions['enableups'])) { // If the setting hasn't been initialized previously, give it a default value
        $wpStoreCartOptions['enableups'] = 'false'; // Default value.
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    }
    if(isset($_POST['enableups'])) { // Here's where you update the value of the variable
        $wpStoreCartOptions['enableups'] = esc_sql($_POST['enableups']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }

	$counter = 0;
	while ($counter < 11) {
		if(isset($_POST['wpsc_ups_shipping_products_'.$counter])) {
			$wpStoreCartOptions['wpsc_ups_shipping_products_'.$counter] = true;
		} else {
			$wpStoreCartOptions['wpsc_ups_shipping_products_'.$counter] = false;
		}
		$counter++;
	}
	update_option('wpStoreCartAdminOptions', $wpStoreCartOptions); 
	
}
add_action('wpsc_admin_save_settings', 'wpscUPSSaveShippingFunction');



/**
// Sets a default value
function myCustomSaveShippingDefaultValuesFunction() {
    
	$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    
    if(!isset($wpStoreCartOptions['allowAbcXyzShipping'])) { // If the setting hasn't been initialized previously, give it a default value
        $wpStoreCartOptions['allowAbcXyShippingz'] = 'false'; // Default value.
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    }    
}
add_action('wpsc_admin', 'myCustomSaveShippingDefaultValuesFunction');
*/


// ========== UPS =============
function wpscUPSCheckbox() {
	echo wpscShippingAPIAddToChecklist($_GET['keytoedit'], 'enableups', 'UPS', __('Enable UPS Shipping?', 'wpstorecart'));
}
add_action('wpsc_admin_shipping_product_checkboxes', 'wpscUPSCheckbox');

// Provides a way to save the selection
function wpscUPSSaveCheckbox() {
	wpscProductToggleShippingService($_POST['wpsc-keytoedit'], 'UPS');
}
add_action('wpsc_admin_save_product', 'wpscUPSSaveCheckbox');


// This is for the checkout page, it will output allowed UPS options
function wpscShippingAPIGetOption_UPS($cart_contents) {
	$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
	$output = '';
	
		if(@!isset($_SESSION)) {
			@session_start();
		}	
		$destinationzip = $_SESSION['wpsc_shipping_zipcode'];
		
		$counter = 0;
		while ($counter <= 10) {
			if($wpStoreCartOptions['wpsc_ups_shipping_products_'.$counter]) {
				switch($counter) {
					case 0 :
						$counter_value = "1DM";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS Next Day Air Early AM','wpstorecart');
					break;
					case 1 :
						$counter_value = "1DA";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS Next Day Air','wpstorecart');
					break;
					case 2 :
						$counter_value = "1DP";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS Next Day Air Saver','wpstorecart');
					break;				
					case 3 :
						$counter_value = "2DM";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS 2nd Day Air Early AM','wpstorecart');
					break;	
					case 4 :
						$counter_value = "2DA";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS 2nd Day Air','wpstorecart');
					break;
					case 5 :
						$counter_value = "3DS";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS 3 Day Select','wpstorecart');
					break;			
					case 6 :
						$counter_value = "GND";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS Ground','wpstorecart');
					break;	
					case 7 :
						$counter_value = "STD";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS Canada Standard','wpstorecart');
					break;			
					case 8 :
						$counter_value = "XPR";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS Worldwide Express','wpstorecart');
					break;			
					case 9 :
						$counter_value = "XDM";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS Worldwide Express Plus','wpstorecart');
					break;
					case 10 :
						$counter_value = "XPD";
						$counter_quote = wpscUPSParcelRate($destinationzip, $cart_contents, $counter_value);
						$counter_name = __('UPS Worldwide Expedited','wpstorecart');
					break;				
				}
				
				if(trim($counter_quote)!='error') {
					$output .= '<option value="UPS['.$counter_value.']">'.$counter_name.' ['.number_format($counter_quote,2).']</option>';
				} else {
					$output .= '<!-- ERROR WITH UPS CALC. DEST-ZIP: '.$destinationzip.' -->';
				}
			}
			$counter++;
			

	}
	
	return $output;
}

// This is for verifying on wpsc-gateway.php
function wpscShippingAPIFinalGateway_UPS($cart_contents, $producttype) {

	if(@!isset($_SESSION)) {
		@session_start();
	}

	return wpscUPSParcelRate($_SESSION['wpsc_shipping_zipcode'], $cart_contents, $producttype);
}

function wpscShippingAPICheckIfServiceEnabled_UPS() {
	    $devOptions = get_option('wpStoreCartAdminOptions'); 
		if ($devOptions['enableups'] == "true") {
			return true;
		}
		if ($devOptions['enableups'] == "false") {
			return false;
		}		
}
?>