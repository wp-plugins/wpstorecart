<?php

// Add custom shipping options
function wpscFlatRateShippingOptionsFunction() {
    
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

    echo '<br />
    <h2>'.__('Flat Rate Shipping','wpstorecart').'</h2>
    <table class="widefat wpsc5table">
    <thead><tr><th>'.__('Option','wpstorecart').'</th><th class="tableDescription">'.__('Description','wpstorecart').'</th><th>'.__('Value','wpstorecart').'</th></tr></thead><tbody>

    <tr><td><p>'.__('Allow Flat Rate Shipping','wpstorecart').'</p></td>
    <td class="tableDescription"><p>'.__('If set to Yes, customers can use flat rate shipping services.','wpstorecart').'</p></td>
    <td><p><label for="allowwpscFlatRate"><input type="radio" id="allowwpscFlatRate_yes" name="allowwpscFlatRate" value="true" '; if (@$wpStoreCartOptions['allowwpscFlatRate'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('Yes','wpstorecart').'</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowwpscFlatRate_no"><input type="radio" id="allowwpscFlatRate_no" name="allowwpscFlatRate" value="false" '; if (@$wpStoreCartOptions['allowwpscFlatRate'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> '.__('No','wpstorecart').'</label></p>
    </td></tr>
    
    </table>
    ';
}
add_action('wpsc_admin_shipping_options_page', 'wpscFlatRateShippingOptionsFunction');




// Save custom options
function wpscFlatRateSaveShippingFunction() {
    global $wpdb;

    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
	
    if(!isset($wpStoreCartOptions['allowwpscFlatRate'])) { // If the setting hasn't been initialized previously, give it a default value
        $wpStoreCartOptions['allowwpscFlatRate'] = 'false'; // Default value.
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    }
    if(isset($_POST['allowwpscFlatRate'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['allowwpscFlatRate'] = esc_sql($_POST['allowwpscFlatRate']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }
}
add_action('wpsc_admin_save_settings', 'wpscFlatRateSaveShippingFunction');




// Sets a default value
function wpscFlatRateSaveShippingDefaultValuesFunction() {
    
	$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    
    if(!isset($wpStoreCartOptions['allowwpscFlatRate'])) { // If the setting hasn't been initialized previously, give it a default value
        $wpStoreCartOptions['allowwpscFlatRate'] = 'false'; // Default value.
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    }    
}
add_action('wpsc_admin', 'wpscFlatRateSaveShippingDefaultValuesFunction');



// Provides a way to enable/disable this shipping service per product
function wpscFlatRateProductCheckbox() {
	global $wpdb;
	$keytoedit=intval($_GET['keytoedit']);	
	$grabrecord = $wpdb->get_results("SELECT `shipping` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`={$keytoedit};", ARRAY_A);
	if(@isset($grabrecord[0]['shipping'])) {
		$flat_rate_shipping_amount = $grabrecord[0]['shipping'];
	} else {
		$flat_rate_shipping_amount = '0.00';
	}
	echo wpscShippingAPIAddToChecklist($_GET['keytoedit'], 'allowwpscFlatRate', 'wpscFlatRate', __('Enable Flat Rate Shipping?', 'wpstorecart').'<br />'.__('Flat Amount', 'wpstorecart').': <input type="text" name="wpStoreCartproduct_shipping" id="wpStoreCartproduct_shipping" value="'.$flat_rate_shipping_amount.'" onsubmit="" onblur="" />');
}
add_action('wpsc_admin_shipping_product_checkboxes', 'wpscFlatRateProductCheckbox');


// Provides a way to save the selection
function wpscFlatRateProductSaveCheckbox() {
	wpscProductToggleShippingService($_POST['wpsc-keytoedit'], 'wpscFlatRate');
}
add_action('wpsc_admin_save_product', 'wpscFlatRateProductSaveCheckbox');

// Calculates Flat Rate Shipping
function wpscFlatRateAmountForCartContents($cart_contents) {
	$shippingAmount = 0;

	foreach($cart_contents as $item) {
		$shippingAmount = $shippingAmount + $item[4];
	}
	
	return $shippingAmount;
}


function wpscShippingAPIGetOption_wpscFlatRate($cart_contents) {
	return '<option value="wpscFlatRate_shipping">'.__('Flat Rate','wpstorecart').' ['.wpscFlatRateAmountForCartContents($cart_contents).']</option>';
}

function wpscShippingAPICheckIfServiceEnabled_wpscFlatRate() {
	    $devOptions = get_option('wpStoreCartAdminOptions'); 
		if ($devOptions['allowwpscFlatRate'] == "true") {
			return true;
		}
		if ($devOptions['allowwpscFlatRate'] == "false") {
			return false;
		}		
}


?>