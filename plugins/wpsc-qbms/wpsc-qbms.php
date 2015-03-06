<?php
/*
Plugin Name: wpStoreCart 3+ Quickbooks Merchant Services Payment Gateway
Plugin URI: http://wpstorecart.com/
Description: Adds the ability to accept Quickbooks Merchant Services payments on your wpStoreCart 3 powered stores.
Version: 3.0.0
Author: wpStoreCart, LLC
Author URI: http://wpstorecart.com/
*/

/*
 * How to create payment gateway options on the wp-admin > wpStoreCart > Settings > Payment > tab:
 */
function wpscQBMSSettingsFunction() {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $devOptions = $wpstorecart_settings_obj->getAdminOptions();

    echo '
	<h2>Quickbooks Merchant Services (QBMS)</h2>
	<table class="widefat wpsc5table">
	<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

	<tr><td><p>Quickbooks Merchant Services (QBMS) Payments?</p></td>
	<td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using Quickbooks Merchant Services.</p></td>
	<td><p><label for="allowqbms"><input type="radio" id="allowqbms_yes" name="allowqbms" value="true" '; if ($devOptions['allowqbms'] == "true") { echo'checked="checked"'; }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowqbms_no"><input type="radio" id="allowqbms_no" name="allowqbms" value="false" '; if ($devOptions['allowqbms'] == "false") { echo'checked="checked"'; }; echo '/> No</label></p>
	</td></tr>

	<tr><td><p>Application Login</p></td>
	<td class="tableDescription"><p>The AppLogin.  Acquire this here: <a href="http://appreg.intuit.com" target="_blank">http://appreg.intuit.com</a></p></td>
	<td><input type="text" name="qbms_login" value="'. $devOptions['qbms_login'].'" />
	</td></tr>

	<tr><td><p>Connection Ticket</p></td>
	<td class="tableDescription"><p>The connection ticket (QBMS Desktop connection model)</p></td>
	<td><input type="text" name="qbms_ticket" value="'. $devOptions['qbms_ticket'].'" />
	</td></tr>

	<tr><td><p>Turn on Test Mode? </p></td>
	<td class="tableDescription"><p>If set to Yes, all transactions are done using the test payment gateway.</p></td>
	<td><p><label for="qbms_testingmode"><input type="radio" id="qbms_testingmode_yes" name="qbms_testingmode" value="true" '; if ($devOptions['qbms_testingmode'] == "true") { _e('checked="checked"', "wpstorecart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="qbms_testingmode_no"><input type="radio" id="qbms_testingmode_no" name="qbms_testingmode" value="false" '; if ($devOptions['qbms_testingmode'] == "false") { _e('checked="checked"', "wpstorecart"); }; echo '/> No</label></p>
	</td></tr>

	</table>
	<br style="clear:both;" /><br />

    ';
}

add_action('wpsc_admin_payment_options_page', 'wpscQBMSSettingsFunction');


/**
 * How to save the custom settings you created for your payment gateway
 */
function wpscQBMSSaveFunction() {
    global $wpstorecart_settings_obj, $wpdb;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();

    if(isset($_POST['allowqbms'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['allowqbms'] = esc_sql($_POST['allowqbms']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }
	
    if(isset($_POST['qbms_login'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['qbms_login'] = esc_sql($_POST['qbms_login']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
	
    if(isset($_POST['qbms_ticket'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['qbms_ticket'] = esc_sql($_POST['qbms_ticket']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }

    if(isset($_POST['qbms_testingmode'])) { // Here's where you update the value of the variable
        wpscCheckAdminPermissions(); // This insures that only wpStoreCart Managers can edit the settings
        $wpStoreCartOptions['qbms_testingmode'] = esc_sql($_POST['qbms_testingmode']); // Changes the variable in the settings
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);    
    }	
}

add_action('wpsc_admin_save_settings', 'wpscQBMSSaveFunction');



/*
 * Default custom settings values samples
 */
function wpscQBMSDefaultValuesFunction() {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions();
    
    if(!isset($wpStoreCartOptions['allowqbms'])) { // If the setting hasn't been initialized previously, give it a default value
        $wpStoreCartOptions['allowqbms'] = 'false'; // Default value.
		$wpStoreCartOptions['qbms_login'] = ''; // Default value.
		$wpStoreCartOptions['qbms_ticket'] = ''; // Default value.
		$wpStoreCartOptions['qbms_testingmode'] = ''; // Default value.
        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    }    
}

add_action('wpsc_admin', 'wpscQBMSDefaultValuesFunction');



function wpscQBMSCheckoutButtonFunction($output) {
    global $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $devOptions = $wpstorecart_settings_obj->getAdminOptions();    
    

	if($devOptions['allowqbms']=='true' && $isLoggedIn == true) {

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
			<tr><td>'.$devOptions['cc_name'].'</td><td><input type="text" name="cc_name_input" id="cc_name_input" value="" /></td></tr>
			<tr><td>'.$devOptions['cc_number'].'</td><td><input type="text" name="cc_number_input" id="cc_number_input" value="" /></td></tr>
			<tr><td>'.$devOptions['cc_cvv'].'</td><td><input type="text" name="cc_cvv_input" id="cc_cvv_input" value="" /></td></tr>
			<tr><td>'.$devOptions['cc_expires'].'</td><td><table><tr><td>'.$devOptions['cc_expires_month'].' <select name="cc_expires_month_input" id="cc_expires_month_input"><option value="01">01</option><option value="02">02</option><option value="03">03</option><option value="04">04</option><option value="05">05</option><option value="06">06</option><option value="07">07</option><option value="08">08</option><option value="09">09</option><option value="10">10</option><option value="11">11</option><option value="12">12</option></select></td><td> '.$devOptions['cc_expires_year'].'</td><td><select name="cc_expires_year_input" id="cc_expires_year_input"><option value="'.date('Y').'">'.date('Y').'</option><option value="'.$year1.'">'.$year1.'</option><option value="'.$year2.'">'.$year2.'</option><option value="'.$year3.'">'.$year3.'</option><option value="'.$year4.'">'.$year4.'</option><option value="'.$year5.'">'.$year5.'</option><option value="'.$year6.'">'.$year6.'</option><option value="'.$year7.'">'.$year7.'</option><option value="'.$year8.'">'.$year8.'</option><option value="'.$year9.'">'.$year9.'</option></select></td></tr></table></td></tr>
			<tr><td>'.$devOptions['cc_address'].'</td><td><input type="text" name="cc_address_input" id="cc_address_input" value="" /></td></tr>
			<tr><td>'.$devOptions['cc_postalcode'].'</td><td><input type="text" name="cc_postalcode_input" id="cc_postalcode_input" value="" /></td></tr>
			<tr><td></td><td><input type="submit" value="'.$devOptions['checkout_button'].'" class="wpsc-button wpsc-qbmscheckout '.$devOptions['button_classes_checkout'].'" onclick=" jQuery(\'#paymentGateway\').val(\'qbms\');" onsubmit="jQuery(\'#paymentGateway\').val(\'qbms\');"></input></td></tr>
		</table>';

	}
    
    
    return $output;
}

add_filter('wpsc_final_checkout_buttons','wpscQBMSCheckoutButtonFunction', 10, 1);


function process_qbms_payments() {
    global $wpscPaymentGateway, $wpstorecart_settings_obj;

    $wpstorecart_settings_obj = new wpscSettings();
    $wpStoreCartOptions = $wpstorecart_settings_obj->getAdminOptions(); // Grabs the settings 
	$devOptions = $wpStoreCartOptions;
    
	    // 2CheckOut
    if(@$wpscPaymentGateway['payment_gateway'] == 'qbms') {
		global $QBMSTransaction, $QBMSErrorMessage, $QBMSStatus, $QBMSTransData;

		require_once(WP_PLUGIN_DIR.'/wpstorecart/plugins/wpsc-qbms/qbms/quickbooks-php-devkit/QuickBooks.php');

		$dsn = null;
		$path_to_private_key_and_certificate = null;
		$application_login = $wpStoreCartOptions['qbms_login'];
		$connection_ticket = $wpStoreCartOptions['qbms_ticket'];

		$MS = new QuickBooks_MerchantService($dsn, $path_to_private_key_and_certificate, $application_login, $connection_ticket);
		if ($wpStoreCartOptions['qbms_testingmode'] == "true") {
			$MS->useTestEnvironment(true);
			//$MS->useDebugMode(true);
		}

		// Now, let's create a credit card object, and authorize an amount against the card
		$name = $_POST['cc_name_input'];
		$number = $_POST['cc_number_input'];// in this format: '5105105105105100';
		$expyear = $_POST['cc_expires_year_input'];
		$expmonth = $_POST['cc_expires_month_input'];
		$address = $_POST['cc_address_input'];
		$postalcode = $_POST['cc_postalcode_input'];
		$cvv = $_POST['cc_cvv_input'];

		// Create the CreditCard object
		$Card = new QuickBooks_MerchantService_CreditCard($name, $number, $expyear, $expmonth, $address, $postalcode, $cvv);

		// We're going to authorize the amount
		$amount = $wpscPaymentGateway['final_price_with_discounts'] ;

		if ($QBMSTransaction = $MS->authorize($Card, $amount)) {


				$QBMSTransData = $QBMSTransaction->serialize();
				$QBMSStatus = 'authorize';

			if ($QBMSTransaction = $MS->capture($QBMSTransaction, $amount))
			{
						$QBMSStatus = 'capture';
						//print('Card captured!' . "\n");
						//print_r($QBMSTransaction);

						// Let's print that qbXML bit again because it'll have more data now
						//$qbxml = $QBMSTransaction->toQBXML();
						//print('qbXML transaction info: ' . $qbxml . "\n\n");
			}
			else
			{
						$QBMSStatus = 'failedcapture';
				$QBMSErrorMessage = 'An error occured during capture: ' . $MS->errorNumber() . ': ' . $MS->errorMessage() . "\n";
			}
		}
		else
		{
				$QBMSStatus = 'failedauthorize';
			$QBMSErrorMessage = 'An error occured during authorization: ' . $MS->errorNumber() . ': ' . $MS->errorMessage() . "\n";
		}
    }

	if($QBMSStatus == 'failedauthorize' || $QBMSStatus == 'failedcapture') {
		if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
			$permalink = get_permalink($devOptions['mainpage']) .'?wpsc=failed&wpscerror='.urlencode($QBMSErrorMessage);
		} else {
			$permalink = get_permalink($devOptions['mainpage']) .'&wpsc=failed&wpscerror='.urlencode($QBMSErrorMessage);
		}
		wp_safe_redirect($permalink);
		exit();

	}
	if($QBMSStatus == 'capture') {
		$table_name2 = $wpdb->prefix . "wpstorecart_products";
		$cart = new wpsc_shoppingcart();
		$cart->empty_cart();
		// ALL COOL, mark the order paid
		$sql = "UPDATE `{$table_name}` SET `orderstatus` = 'Completed' WHERE `primkey` = {$keytoedit}";
		$wpdb->query ($sql);
		$sql = "SELECT `cartcontents`, `email` FROM `{$table_name}` WHERE `primkey`={$keytoedit};";
		$results = $wpdb->get_results( $sql , ARRAY_A );
		if(isset($results)) {
				$specific_items = explode(",", $results[0]['cartcontents']);
				foreach($specific_items as $specific_item) {
						if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
								$current_item = explode('*', $specific_item);
								if(isset($current_item[0]) && isset($current_item[1])) {
										$sql2 = "SELECT `primkey`, `inventory`, `useinventory` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`={$current_item[0]};";
										wpscAssignSerialNumber($current_item[0], $keytoedit);
										$moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
										if(isset($moreresults[0])){
											if( $moreresults[0]['useinventory']==1) {
												$newInventory = $moreresults[0]['inventory'] - $current_item[1];
												$wpdb->query("UPDATE `{$table_name2}` SET `inventory` = '{$newInventory}' WHERE `primkey` = {$moreresults[0]['primkey']} LIMIT 1 ;");
											}
										}
								}
						}
				}
		}
		if($devOptions['pcicompliant']=='true') {
			$table_name_meta = $wpdb->prefix . "wpstorecart_meta";
			$results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".$QBMSTransData."', 'qbms_transaction_record', '{$keytoedit}');");
		}

		 // Let's send them an email telling them their purchase was successful
		 // In case any of our lines are larger than 70 characters, we should use wordwrap()
		$message = wordwrap(wpscMakeEmailTxt($devOptions['emailonapproval']) . wpscMakeEmailTxt($devOptions['emailsig']), 70);

		$headers = 'From: '.$devOptions['wpStoreCartEmail'] . "\r\n" .
				'Reply-To: ' .$devOptions['wpStoreCartEmail']. "\r\n" .
				'X-Mailer: PHP/wpStoreCart';

		// Send an email when purchase is submitted
		if(isset($results)) {
				@ini_set("sendmail_from", $devOptions['wpStoreCartEmail']);
				wpscEmail($results[0]['email'], 'Your order has been fulfilled!', $message, $headers);
		}

		if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
			$permalink = get_permalink($devOptions['mainpage']) .'?wpsc=success';
		} else {
			$permalink = get_permalink($devOptions['mainpage']) .'&wpsc=success';
		}
		wp_safe_redirect($permalink);
		exit();

	}	
	
}

add_action('wpsc_process_payment_gateways', 'process_qbms_payments');


?>