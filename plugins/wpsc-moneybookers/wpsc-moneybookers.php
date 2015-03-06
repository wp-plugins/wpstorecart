<?php
/*
Plugin Name: wpStoreCart 3+ Moneybookers/Skrill Payment Gateway
Plugin URI: http://wpstorecart.com/
Description: Adds the ability to accept Moneybookers/Skrill payments on your wpStoreCart 3 powered stores.
Version: 2.0.1
Author: wpStoreCart, LLC
Author URI: http://wpstorecart.com/
*/

/*
 * How to create payment gateway options on the wp-admin > wpStoreCart > Settings > Payment > tab:
 */
function wpscMoneybookersSettingsFunction() {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $devOptions = $wpstorecart_settings_obj->getAdminOptions();

    echo '
	<h2>Skrill/Moneybookers</h2>
	<table class="widefat wpsc5table">
	<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

	<tr><td><p>Accept Skrill/Moneybookers Payments?</p></td>
	<td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using Skrill/Moneybookers.</p></td>
	<td><p><label for="allowmb"><input type="radio" id="allowmb_yes" name="allowmb" value="true" '; if ($devOptions['allowmb'] == "true") { echo'checked="checked"'; }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowmb_no"><input type="radio" id="allowmb_no" name="allowmb" value="false" '; if ($devOptions['allowmb'] == "false") { echo'checked="checked"'; }; echo '/> No</label></p>
	</td></tr>

	<tr><td><p>Pay to Email</p></td>
	<td class="tableDescription"><p>The Skrill/Moneybookers account email where you wish to receive payment</a></p></td>
	<td><input type="text" name="mb_login" value="'. $devOptions['mb_login'].'" />
	</td></tr>

	<tr><td><p>Secret Word</p></td>
	<td class="tableDescription"><p>The Secret Word</p></td>
	<td><input type="text" name="mb_secretword" value="'. $devOptions['mb_secretword'].'" />
	</td></tr>

	<tr><td><p>Logo Image</p></td>
	<td class="tableDescription"><p>Must use SSL &amp; the URL must start with <strong>https://</strong> also must be at most 200px wide and 50px tall.</p></td>
	<td><input type="text" name="mb_logo" value="'. $devOptions['mb_logo'].'" />
	</td></tr>

	<tr><td><p>Currency</p></td>
	<td class="tableDescription"><p>The currency you wish to receive all payments in.</p></td>
	<td><select name="mb_currency">
            <option value="USD" ';if($devOptions['mb_currency']=='USD'){echo ' selected="selected"';} echo'>U.S. Dollar</option>
            <option value="EUR" ';if($devOptions['mb_currency']=='EUR'){echo ' selected="selected"';} echo'>Euro</option>
            <option value="GBP" ';if($devOptions['mb_currency']=='GBP'){echo ' selected="selected"';} echo'>British Pound</option>
            <option value="JPY" ';if($devOptions['mb_currency']=='JPY'){echo ' selected="selected"';} echo'>Japanese Yen</option>
            <option value="CHF" ';if($devOptions['mb_currency']=='CHF'){echo ' selected="selected"';} echo'>Swiss Franc</option>
            <option value="HKD" ';if($devOptions['mb_currency']=='HKD'){echo ' selected="selected"';} echo'>Hong Kong Dollar</option>
            <option value="SGD" ';if($devOptions['mb_currency']=='SGD'){echo ' selected="selected"';} echo'>Singapore Dollar</option>
            <option value="CAD" ';if($devOptions['mb_currency']=='CAD'){echo ' selected="selected"';} echo'>Canadian Dollar</option>
            <option value="AUD" ';if($devOptions['mb_currency']=='AUD'){echo ' selected="selected"';} echo'>Australian Dollar</option>
            <option value="DKK" ';if($devOptions['mb_currency']=='DKK'){echo ' selected="selected"';} echo'>Danish Krone</option>
            <option value="SEK" ';if($devOptions['mb_currency']=='SEK'){echo ' selected="selected"';} echo'>Swedish Krona</option>
            <option value="NOK" ';if($devOptions['mb_currency']=='NOK'){echo ' selected="selected"';} echo'>Norwegian Krone</option>
            <option value="ILS" ';if($devOptions['mb_currency']=='ILS'){echo ' selected="selected"';} echo'>Israeli Shekel</option>
            <option value="MYR" ';if($devOptions['mb_currency']=='MYR'){echo ' selected="selected"';} echo'>Malaysian Ringgit</option>
            <option value="NZD" ';if($devOptions['mb_currency']=='NZD'){echo ' selected="selected"';} echo'>New Zealand Dollar</option>
            <option value="TRY" ';if($devOptions['mb_currency']=='TRY'){echo ' selected="selected"';} echo'>New Turkish Lira</option>
            <option value="AED" ';if($devOptions['mb_currency']=='AED'){echo ' selected="selected"';} echo'>United Arab Emirates Dirham</option>
            <option value="MAD" ';if($devOptions['mb_currency']=='MAD'){echo ' selected="selected"';} echo'>Moroccan Dirham</option>
            <option value="QAR" ';if($devOptions['mb_currency']=='QAR'){echo ' selected="selected"';} echo'>Qatari Rial</option>
            <option value="SAR" ';if($devOptions['mb_currency']=='SAR'){echo ' selected="selected"';} echo'>Saudi Riyal</option>
            <option value="TWD" ';if($devOptions['mb_currency']=='TWD'){echo ' selected="selected"';} echo'>Taiwan Dollar</option>
            <option value="THB" ';if($devOptions['mb_currency']=='THB'){echo ' selected="selected"';} echo'>Thailand Baht</option>
            <option value="CZK" ';if($devOptions['mb_currency']=='CZK'){echo ' selected="selected"';} echo'>Czech Koruna</option>
            <option value="HUF" ';if($devOptions['mb_currency']=='HUF'){echo ' selected="selected"';} echo'>Hungarian Forint</option>
            <option value="SKK" ';if($devOptions['mb_currency']=='SKK'){echo ' selected="selected"';} echo'>Slovakian Koruna</option>
            <option value="EEK" ';if($devOptions['mb_currency']=='EEK'){echo ' selected="selected"';} echo'>Estonian Kroon</option>
            <option value="BGN" ';if($devOptions['mb_currency']=='BGN'){echo ' selected="selected"';} echo'>Bulgarian Leva</option>
            <option value="PLN" ';if($devOptions['mb_currency']=='PLN'){echo ' selected="selected"';} echo'>Polish Zloty</option>
            <option value="ISK" ';if($devOptions['mb_currency']=='ISK'){echo ' selected="selected"';} echo'>Iceland Krona</option>
            <option value="INR" ';if($devOptions['mb_currency']=='INR'){echo ' selected="selected"';} echo'>Indian Rupee</option>
            <option value="LVL" ';if($devOptions['mb_currency']=='LVL'){echo ' selected="selected"';} echo'>Latvian Lat</option>
            <option value="KRW" ';if($devOptions['mb_currency']=='KRW'){echo ' selected="selected"';} echo'>South-Korean Won</option>
            <option value="ZAR" ';if($devOptions['mb_currency']=='ZAR'){echo ' selected="selected"';} echo'>South-African Rand</option>
            <option value="RON" ';if($devOptions['mb_currency']=='RON'){echo ' selected="selected"';} echo'>Romanian Leu New</option>
            <option value="HRK" ';if($devOptions['mb_currency']=='HRK'){echo ' selected="selected"';} echo'>Croatian Kuna</option>
            <option value="LTL" ';if($devOptions['mb_currency']=='LTL'){echo ' selected="selected"';} echo'>Lithuanian Litas</option>
            <option value="JOD" ';if($devOptions['mb_currency']=='JOD'){echo ' selected="selected"';} echo'>Jordanian Dinar</option>
            <option value="OMR" ';if($devOptions['mb_currency']=='OMR'){echo ' selected="selected"';} echo'>Omani Rial</option>
            <option value="RSD" ';if($devOptions['mb_currency']=='RSD'){echo ' selected="selected"';} echo'>Serbian dinar</option>
            <option value="TND" ';if($devOptions['mb_currency']=='TND'){echo ' selected="selected"';} echo'>Tunisian Dinar</option>
        </select>
	</td></tr>

	</table>
	<br style="clear:both;" /><br />';
}

add_action('wpsc_admin_payment_options_page', 'wpscMoneybookersSettingsFunction');


/**
 * How to save the custom settings you created for your payment gateway
 */
function wpscMoneybookersSaveFunction() {
    global $wpstorecart_settings_obj, $wpdb;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();

    if(isset($_POST['allowmb'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['allowmb'] = esc_sql($_POST['allowmb']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }
	
    if(isset($_POST['mb_login'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['mb_login'] = esc_sql($_POST['mb_login']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
	
    if(isset($_POST['mb_secretword'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['mb_secretword'] = esc_sql($_POST['mb_secretword']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
	
    if(isset($_POST['mb_logo'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['mb_logo'] = esc_sql($_POST['mb_logo']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	

    if(isset($_POST['mb_currency'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['mb_currency'] = esc_sql($_POST['mb_currency']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
}

add_action('wpsc_admin_save_settings', 'wpscMoneybookersSaveFunction');



/*
 * Default custom settings values samples
 */
function wpscMoneybookersDefaultValuesFunction() {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();
    
    if(!isset($wpStoreCartOptions['allowmb'])) { // If the setting hasn't been initialized previously, give it a default value
        $wpStoreCartOptions['allowmb'] = 'false'; // Default value.
		$wpStoreCartOptions['mb_login'] = ''; // Default value.
		$wpStoreCartOptions['mb_secretword'] = ''; // Default value.
		$wpStoreCartOptions['mb_logo'] = ''; // Default value.
		$wpStoreCartOptions['mb_currency'] = 'USD'; // Default value.
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    }    
}

add_action('wpsc_admin', 'wpscMoneybookersDefaultValuesFunction');



function wpscMoneybookersCheckoutButtonFunction($output) {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();    
    
    if($wpStoreCartOptions['allowmb']=='true') {
        $output .= '<input type="submit" value="'.$wpStoreCartOptions['checkout_moneybookers_button'].'" class="wpsc-button wpsc-checkout-button wpsc-moneybookerscheckout '.$wpStoreCartOptions['button_classes_checkout'].'" onclick=" jQuery(\'#paymentGateway\').val(\'moneybookers\');" onsubmit="jQuery(\'#paymentGateway\').val(\'moneybookers\');"></input>';
 
    }    
    
    return $output;
}

add_filter('wpsc_final_checkout_buttons','wpscMoneybookersCheckoutButtonFunction', 10, 1);



include_once(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/payment/PaymentGateway.php');
function process_wpscMoneybookers_payments() {
    global $wpscPaymentGateway, $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions(); // Grabs the settings    
    
	    // 2CheckOut
    if(@$wpscPaymentGateway['payment_gateway'] == 'moneybookers') {
		echo "<html>\n";
		echo "<head><title>Processing Payment...</title></head>\n";
		echo "<body onload=\"document.forms['gateway_form'].submit();\">\n";
		echo "<p style=\"text-align:center;\"><h2>Please wait, your order is being processed and you";
		echo " will be redirected to the payment website.</h2></p>\n";

		echo '
		<form action="https://www.moneybookers.com/app/payment.pl" method="post" name="gateway_form">
			<input type="hidden" name="pay_to_email" value="'.$wpStoreCartOptions['mb_login'] .'"/>
			<input type="hidden" name="status_url" value="'.plugins_url().'/wpsc-moneybookers/moneybookers/mb_ipn.php"/>
			<input type="hidden" name="language" value="EN"/>
			<input type="hidden" name="merchant_fields" value="order_id">
			<input type="hidden" name="order_id" value="'.$wpscPaymentGateway['order_id'] .'"/>
			<input type="hidden" name="amount" value="'.$wpscPaymentGateway['final_price_with_discounts'] .'"/>
			<input type="hidden" name="currency" value="'.$wpStoreCartOptions['mb_currency'].'"/>
			<input type="hidden" name="detail1_description" value="'.$wpscPaymentGateway['cart_description'] .'"/>
			<input type="hidden" name="detail1_text" value="'.$wpscPaymentGateway['order_id'] .'"/>';
			if(trim(strtolower($wpStoreCartOptions['mb_logo']))!='https://' && trim($wpStoreCartOptions['mb_logo'])!='' && strtolower(substr($wpStoreCartOptions['mb_logo'], 0, 8))=='https://') {
				echo '<input type="hidden" name="logo_url" value="'.$wpStoreCartOptions['mb_logo'].'"/>';
			}

		echo "<p style=\"text-align:center;\"><br/><br/>If you are not automatically redirected to ";
		echo "payment website within 5 seconds...<br/><br/>\n";
		echo "<input type=\"submit\" value=\"Click Here\"></p>\n";

		echo "</form>\n";
		echo "</body></html>\n";

    } 
	
}

add_action('wpsc_process_payment_gateways', 'process_wpscMoneybookers_payments');


?>