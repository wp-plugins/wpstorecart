<?php

if(!class_exists('wpscSettings')) {
        /**
         * wpscSettings class
         */
	class wpscSettings {
		/**  
		 * @var string $adminOptionsName Just the name of the wpStoreCart options in the Wordpress database 
		 */
		var $adminOptionsName = "wpStoreCartAdminOptions";
		
		/**
		 * @var array $wpStoreCartSettings The actual wpStoreCart options.
		 */
		var $wpStoreCartSettings = null;	
		
		/**
		 * Constructor method, returns options
		 */
		function __construct() {
			$this->wpStoreCartSettings = $this->getAdminOptions('flush');
		}
		
		/**
		*
		* Returns an array of admin options
		*
		* @param string $action
		* @return array
		*/
		function getAdminOptions($action=NULL) {
		
			$apAdminOptions = array(    'mainpage' => '',
		                                    'checkoutpage' => '',
		                                    'orderspage' => '',
		                                    'checkoutpageurl' => '',
		                                    'turnon_wpstorecart' => 'true',
		                                    'wpStoreCartEmail' => get_bloginfo('admin_email'),
		                                    'wpStoreCartheight' => '100',
		                                    'wpStoreCartwidth' => '100',
		                                    'showproductthumbnail' => 'true',
		                                    'showproductdescription' => 'true',
		                                    'wpscCss' => 'small-grey.css',
		                                    'frontpageDisplays' => 'List all products',
		                                    'displayThumb' => 'true',
		                                    'displayTitle' => 'true',
		                                    'displayintroDesc' => 'true',
		                                    'displayFullDesc' => 'false',
		                                    'displayType' => 'grid',
		                                    'displayAddToCart' => 'true',
		                                    'displayBuyNow' => 'true',
		                                    'displayPrice' => 'true',
		                                    'allowcheckmoneyorder' => 'false',
		                                    'checkmoneyordertext' => __('Please send a check or money order for the above amount to:<br /><br /><strong>My Business Name<br />1234 My Address, Suite ABC<br />New York, NY 24317, USA</strong><br /><br />Please allow 4 to 6 weeks for delivery.', 'wpstorecart'),
		                                    'paypalemail' => get_bloginfo('admin_email'),
		                                    'paypaltestmode' => 'false',
		                                    'allowpaypal' => 'true',
		                                    'allowauthorizenet' => 'false',
		                                    'authorizenetemail' => '',
		                                    'authorizenetsecretkey' => '',
		                                    'authorizenettestmode' => 'false',
		                                    'allow2checkout' => 'false',
		                                    '2checkoutemail' => '',
		                                    '2checkouttestmode' => 'false',
		                                    'allowlibertyreserve' => 'false',
		                                    'libertyreserveaccount' => '',
		                                    'libertyreservestore' => '',
		                                    'emailonpurchase' => __('Dear [customername], thanks for your recent order from [sitename].  Your order has been submitted to our staff for approval.  This process can take as little as an hour to as long as a few weeks depending on how quickly your payment clears, and whether there are other issues which may cause a delay.  You can view your order status here: [downloadurl] ', 'wpstorecart'),
		                                    'emailonapproval' => __('Dear [customername], thanks again for your recent order from [sitename].  This email is to inform you that your order has been approved.  For physical products, this does not mean that they have been shipped yet; as you will get another email when the order is shipped.  If you ordered a digital download, your download is now available.  You can view your order status here: [downloadurl] ', 'wpstorecart'),
		                                    'emailonshipped'  => __('Dear [customername], thanks again for your recent order from [sitename].  This email is to inform you that your order has been shipped.', 'wpstorecart'),
		                                    'emailsig' => __('Thanks again, [sitename] Management', 'wpstorecart'),
		                                    'emailserialnumber' => __('Dear [customername], thanks for your recent order from [sitename].  You can view your order status here: [downloadurl]   A serial number for [productname] has been issued to you.  Please keep this email for future reference.  Your serial number is [serialnumber] ', 'wpstorecart'),
		                                    'cart_title' => __('Shopping Cart', 'wpstorecart'),
		                                    'single_item' => __('Item', 'wpstorecart'),
		                                    'multiple_items' => __('Items', 'wpstorecart'),
		                                    'currency_symbol' => __('$', 'wpstorecart'),
		                                    'currency_symbol_right' => __(' USD', 'wpstorecart'),
		                                    'subtotal' => __('Subtotal', 'wpstorecart'),
		                                    'update_button' => __('update', 'wpstorecart'),
		                                    'checkout_button' => __('checkout', 'wpstorecart'),
		                                    'currency_code' => 'USD',
		                                    'checkout_checkmoneyorder_button' => __('Checkout with Check/Money Order', 'wpstorecart'),
		                                    'checkout_paypal_button' => __('Checkout with PayPal', 'wpstorecart'),
		                                    'checkout_authorizenet_button' => __('Checkout with Authorize.NET', 'wpstorecart'),
		                                    'checkout_2checkout_button' => __('Checkout with 2CheckOut', 'wpstorecart'),
		                                    'checkout_libertyreserve_button' => __('Checkout with Liberty Reserve', 'wpstorecart'),
		                                    'checkout_moneybookers_button' => __('Checkout with Moneybookers', 'wpstorecart'),
		                                    'remove_link' => __('remove', 'wpstorecart'),
		                                    'empty_button' => __('empty', 'wpstorecart'),
		                                    'empty_message' => __('Your cart is empty!', 'wpstorecart'),
		                                    'item_added_message' => __('Item added!', 'wpstorecart'),
		                                    'enter_coupon' => __('Enter coupon:', 'wpstorecart'),
		                                    'price_error' => __('Invalid price format!', 'wpstorecart'),
		                                    'quantity_error' => __('Item quantities must be whole numbers!', 'wpstorecart'),
		                                    'checkout_error' => __('Your order could not be processed!', 'wpstorecart'),
		                                    'success_text' => __('Dear [customername], thanks for your recent order from [sitename].  Your order has been submitted to our staff for approval.  This process can take as little as an hour to as long as a few weeks depending on how quickly your payment clears, and whether there are other issues which may cause a delay.', 'wpstorecart'),
		                                    'failed_text' => __('Dear [customername], thanks for your recent order from [sitename].  However, we encountered problems with your order and are unable to fulfill it at this time.  Please contact us for more information.', 'wpstorecart'),
		                                    'add_to_cart' => __('Add to Cart', 'wpstorecart'),
		                                    'out_of_stock' => __('Out of Stock!', 'wpstorecart'),
		                                    'ga_trackingnum' => '',
		                                    'database_version' => NULL,
		                                    'minimumAffiliatePayment' => '0.00',
		                                    'minimumDaysBeforePaymentEligable' => '30',
		                                    'affiliateInstructions'=> __('Welcome to our affiliate program.  Here, you can review successful affiliate sales as well as grab links to all the products in our store that include your affiliate code.', 'wpstorecart'),
		                                    'wpscjQueryUITheme' =>'',
		                                    'run_updates' => 'true',
		                                    'shipping_zip_origin' => '99202',
		                                    'enableusps' => 'false',
		                                    'enableups' => 'false',
		                                    'enablefedex' => 'false',
		                                    'storetype' => 'Mixed (physical and digital)',
		                                    'checkcurl' => 'true',
		                                    'uspsapiname' => '',
		                                    'displayshipping' => 'true',
		                                    'displaysubtotal' => 'true',
		                                    'displaytotal' => 'true',
		                                    'total' => __('Total', 'wpstorecart'),
		                                    'shipping' => __('Shipping', 'wpstorecart'),
		                                    'requireregistration' => 'true',
		                                    'enablecoupons' => 'true',
		                                    'login' => __('Login', 'wpstorecart'),
		                                    'register' => __('Register', 'wpstorecart'),
		                                    'logout' => __('Logout', 'wpstorecart'),
		                                    'username' => __('Username', 'wpstorecart'),
		                                    'password' => __('Password', 'wpstorecart'),
		                                    'email' => __('Email', 'wpstorecart'),
		                                    'myordersandpurchases' => __('My Orders &amp; Purchases', 'wpstorecart'),
		                                    'required_symbol' => '*',
		                                    'required_help' => __('* - Fields with an asterick are required.', 'wpstorecart'),
		                                    'flatrateshipping' => 'individual',
		                                    'flatrateamount' => '0.00',
		                                    'calculateshipping' => __('Calculate Shipping', 'wpstorecart'),
		                                    'itemsperpage' => '10',
		                                    'libertyreservesecretword' => '',
		                                    'guestcheckout' => __('Guest Checkout', 'wpstorecart'),
		                                    'shareyourcart_secret' => '',
		                                    'shareyourcart_clientid' => '',
		                                    'shareyourcart_appid' => '',
		                                    'shareyourcart_activate' => 'false',
		                                    'shareyourcart_skin' => 'orange',
		                                    'useimagebox' => 'none',
		                                    'shareyourcart_failedreg' => 'true',
		                                    'plugin_error' => '',
		                                    'wpsc_api_key' => '',
		                                    'wpsc_secret_hash' => '',
		                                    'showproductgallery' => 'true',
		                                    'showproductgallerywhere' => 'Directly after the Description',
		                                    'displaytaxes' => 'true',
		                                    'taxes' => '',
		                                    'tax' => 'Tax',
		                                    'checkoutimages' => 'true',
		                                    'checkoutimagewidth' => '25',
		                                    'checkoutimageheight' => '25',
		                                    'checkoutlinktoproduct' => 'false',
		                                    'displaypriceonview' => 'true',
		                                    'menu_style' => 'version3',
		                                    'admin_capability' => 'manage_options',
		                                    'orders_profile' => 'display',
		                                    'allowqbms' => 'false',
		                                    'qbms_ticket' => '',
		                                    'qbms_login' => '',
		                                    'qbms_testingmode' => 'false',
		                                    'cc_name' => __('Full name on card', 'wpstorecart'),
		                                    'cc_number' => __('Credit Card #', 'wpstorecart'),
		                                    'cc_expires' => __('Expires', 'wpstorecart'),
		                                    'cc_expires_month' => __('Month', 'wpstorecart'),
		                                    'cc_expires_year' => __('Year', 'wpstorecart'),
		                                    'cc_address' => __('Address of Credit Card', 'wpstorecart'),
		                                    'cc_postalcode' => __('Zipcode of Credit Card', 'wpstorecart'),
		                                    'cc_cvv' => __('CVV', 'wpstorecart'),
		                                    'checkout_xhtml_type' => 'table',
		                                    'disable_inline_styles' => 'false',
		                                    'field_order_0' => '0',
		                                    'field_order_1' => '1',
		                                    'field_order_2' => '2',
		                                    'field_order_3' => '3',
		                                    'field_order_4' => '4',
		                                    'pcicompliant' => 'false',
		                                    'paypalipnurl' => plugins_url().'/wpstorecart/wpstorecart/payment/gateway.paypal.php',
		                                    'button_classes_addtocart' => '',
		                                    'button_classes_checkout' => '',
		                                    'button_classes_meta' => '',
		                                    'trial_period_1' => __('Trial Period:', 'wpstorecart'), 
		                                    'trial_period_2' => __('2nd Trial Period:', 'wpstorecart'), 
		                                    'subscription_price' => __('Subscription Price:', 'wpstorecart'), 
		                                    'for' => __('for', 'wpstorecart'), 
		                                    'afterwards' => __('afterwards', 'wpstorecart'), 
		                                    'every' => __('every', 'wpstorecart'), 
		                                    'free' => __('FREE', 'wpstorecart'), 
		                                    'day' => __('day(s)', 'wpstorecart'), 
		                                    'week' => __('week(s)', 'wpstorecart'), 
		                                    'month' => __('month(s)', 'wpstorecart'), 
		                                    'year' => __('year(s)', 'wpstorecart'), 
		                                    'buy_now' => __('Buy Now', 'wpstorecart'), 
		                                    'page_mode' => 'sort',
		                                    'allowmb' => 'false',
		                                    'mb_login' => get_bloginfo('admin_email'),
		                                    'mb_secretword' => '',
		                                    'mb_logo' => 'https://',
		                                    'mb_currency' => 'USD',
		                                    'gd_enable' => 'false',
		                                    'gd_display' => 'false',
		                                    'gd_saleprice' => 'true',
		                                    'show_price_to_guests' => 'true',
		                                    'logged_out_price' => '?.??',
		                                    'uninstall' => 'false',
		                                    'qty' => 'Qty:',
		                                    'combo_enable' => 'true',
		                                    'combo_display_prices' => 'true',
		                                    'combo_display_links' => 'true',
		                                    'combo_display_thumbs' => 'true',
		                                    'redirect_to_checkout' => 'true',
                                                    'available_in_stock' => __('# available in stock:', 'wpstorecart'), 
		                                    'debug_parameter' => 'X_DEBUG_START_SESSION=default',
		                                    'where_to_display_accessories' => 'At the very bottom of the page',
                                                    'product_designer_css' => 'wpstorecart.custom.css',
                                                    'product_designer_order' => '1,2,3,4,5,6,0',
                                                    'product_single_designer_css' => 'wpstorecart.custom.css',
                                                    'product_single_designer_order' => '1,2,3,4,5,6,7,0',  
                                                    'product_listitem_designer_order' => '1,2,3,4,5,0',
                                                    'completely_disable_shareyourcart' => 'false',
                                                    'piwik_url' => '',
                                                    'piwik_enabled' => 'false',
                                                    'piwik_siteid' => '1',
                                                    'alert_clear_period' => '1 hour',
                                                    'wpstorecart_download_hash' => sha1(rand(-4096, 4096)),
                                                    'wpsc3_first_run' => 'true'
			);
		
		
			if($this->wpStoreCartSettings!=NULL) {
				if($action!='flush') {
					$wpStoreCartOptions = $this->wpStoreCartSettings;
					$wpStoreCartOptions['menu_style']='version3';
				} else {
					$this->wpStoreCartSettings = NULL;
					$wpStoreCartOptions = get_option($this->adminOptionsName);
				}
			} else {
				$wpStoreCartOptions = get_option($this->adminOptionsName);
			}
		
			if (!empty($wpStoreCartOptions)) {
				foreach ($wpStoreCartOptions as $key => $option) {
					$apAdminOptions[$key] = $option;
				}
			}
                        
			//$apAdminOptions['menu_style']='version3'; // This hardcodes the new menu system as the default
			update_option($this->adminOptionsName, $apAdminOptions);
		
			return $apAdminOptions;
		}
                
                
                function setAdminOptions() {
                    global $wpdb;
                            $wpStoreCartOptions = get_option($this->adminOptionsName);
                      
                            if (isset($_POST['alert_clear_period'])) {
                                    $wpStoreCartOptions['alert_clear_period'] = $wpdb->escape($_POST['alert_clear_period']);
                            }                             
                            if (isset($_POST['wpStoreCartmainpage'])) {
                                    $wpStoreCartOptions['mainpage'] = $wpdb->escape($_POST['wpStoreCartmainpage']);
                            } 		
                            if (isset($_POST['checkoutpage'])) {
                                    $wpStoreCartOptions['checkoutpage'] = $wpdb->escape($_POST['checkoutpage']);
                                    $wpStoreCartOptions['checkoutpageurl'] = get_permalink($wpStoreCartOptions['checkoutpage']);
                            }
                            if (isset($_POST['orderspage'])) {
                                    $wpStoreCartOptions['orderspage'] = $wpdb->escape($_POST['orderspage']);
                            }
                            if (isset($_POST['turnwpStoreCartOn'])) {
                                    $wpStoreCartOptions['turnon_wpstorecart'] = $wpdb->escape($_POST['turnwpStoreCartOn']);
                            }   
                            if (isset($_POST['wpStoreCartEmail'])) {
                                    $wpStoreCartOptions['wpStoreCartEmail'] = $wpdb->escape($_POST['wpStoreCartEmail']);
                            }   				
                            if (isset($_POST['wpStoreCartwidth'])) {
                                    $wpStoreCartOptions['wpStoreCartwidth'] = $wpdb->escape($_POST['wpStoreCartwidth']);
                            }
                            if (isset($_POST['wpscCss'])) {
                                    $wpStoreCartOptions['wpscCss'] = $wpdb->escape($_POST['wpscCss']);
                            }

                            if (isset($_POST['frontpageDisplays'])) {
                                    $wpStoreCartOptions['frontpageDisplays'] = $wpdb->escape($_POST['frontpageDisplays']);
                            }
                            if (isset($_POST['displayThumb'])) {
                                    $wpStoreCartOptions['displayThumb'] = $wpdb->escape($_POST['displayThumb']);
                            }
                            if (isset($_POST['displayTitle'])) {
                                    $wpStoreCartOptions['displayTitle'] = $wpdb->escape($_POST['displayTitle']);
                            }
                            if (isset($_POST['displayintroDesc'])) {
                                    $wpStoreCartOptions['displayintroDesc'] = $wpdb->escape($_POST['displayintroDesc']);
                            }
                            if (isset($_POST['displayFullDesc'])) {
                                    $wpStoreCartOptions['displayFullDesc'] = $wpdb->escape($_POST['displayFullDesc']);
                            }
                            if (isset($_POST['displayType'])) {
                                    $wpStoreCartOptions['displayType'] = $wpdb->escape($_POST['displayType']);
                            }
                            if (isset($_POST['displayAddToCart'])) {
                                    $wpStoreCartOptions['displayAddToCart'] = $wpdb->escape($_POST['displayAddToCart']);
                            }
                            if (isset($_POST['displayBuyNow'])) {
                                    $wpStoreCartOptions['displayBuyNow'] = $wpdb->escape($_POST['displayBuyNow']);
                            }
                            if (isset($_POST['displayPrice'])) {
                                    $wpStoreCartOptions['displayPrice'] = $wpdb->escape($_POST['displayPrice']);
                            }

                            if (isset($_POST['wpStoreCartheight'])) {
                                    $wpStoreCartOptions['wpStoreCartheight'] = $wpdb->escape($_POST['wpStoreCartheight']);
                            }		
                            if (isset($_POST['showproductthumbnail'])) {
                                    $wpStoreCartOptions['showproductthumbnail'] = $wpdb->escape($_POST['showproductthumbnail']);
                            }
                            if (isset($_POST['showproductdescription'])) {
                                    $wpStoreCartOptions['showproductdescription'] = $wpdb->escape($_POST['showproductdescription']);
                            }

                            if (isset($_POST['allowcheckmoneyorder'])) {
                                    $wpStoreCartOptions['allowcheckmoneyorder'] = $wpdb->escape($_POST['allowcheckmoneyorder']);
                            }
                            if (isset($_POST['checkmoneyordertext'])) {
                                    $wpStoreCartOptions['checkmoneyordertext'] = $wpdb->escape($_POST['checkmoneyordertext']);
                            }

                            if (isset($_POST['allowpaypal'])) {
                                    $wpStoreCartOptions['allowpaypal'] = $wpdb->escape($_POST['allowpaypal']);
                            }
                            if (isset($_POST['paypalemail'])) {
                                    $wpStoreCartOptions['paypalemail'] = $wpdb->escape($_POST['paypalemail']);
                            }
                            if (isset($_POST['paypaltestmode'])) {
                                    $wpStoreCartOptions['paypaltestmode'] = $wpdb->escape($_POST['paypaltestmode']);
                            }

                            if (isset($_POST['allowauthorizenet'])) {
                                    $wpStoreCartOptions['allowauthorizenet'] = $wpdb->escape($_POST['allowauthorizenet']);
                            }
                            if (isset($_POST['authorizenettestmode'])) {
                                    $wpStoreCartOptions['authorizenettestmode'] = $wpdb->escape($_POST['authorizenettestmode']);
                            }
                            if (isset($_POST['authorizenetemail'])) {
                                    $wpStoreCartOptions['authorizenetemail'] = $wpdb->escape($_POST['authorizenetemail']);
                            }
                            if (isset($_POST['authorizenetsecretkey'])) {
                                    $wpStoreCartOptions['authorizenetsecretkey'] = $wpdb->escape($_POST['authorizenetsecretkey']);
                            }


                            if (isset($_POST['allow2checkout'])) {
                                    $wpStoreCartOptions['allow2checkout'] = $wpdb->escape($_POST['allow2checkout']);
                            }
                            if (isset($_POST['2checkouttestmode'])) {
                                    $wpStoreCartOptions['2checkouttestmode'] = $wpdb->escape($_POST['2checkouttestmode']);
                            }
                            if (isset($_POST['2checkoutemail'])) {
                                    $wpStoreCartOptions['2checkoutemail'] = $wpdb->escape($_POST['2checkoutemail']);
                            }


                            if (isset($_POST['emailonpurchase'])) {
                                    $wpStoreCartOptions['emailonpurchase'] = esc_attr($_POST['emailonpurchase']);
                            }	
                            if (isset($_POST['emailonapproval'])) {
                                    $wpStoreCartOptions['emailonapproval'] = esc_attr($_POST['emailonapproval']);
                            }	
                            if (isset($_POST['emailonshipped'])) {
                                    $wpStoreCartOptions['emailonshipped'] = esc_attr($_POST['emailonshipped']);
                            }	
                            if (isset($_POST['emailsig'])) {
                                    $wpStoreCartOptions['emailsig'] = esc_attr($_POST['emailsig']);
                            }
                            if (isset($_POST['emailserialnumber'])) {
                                    $wpStoreCartOptions['emailserialnumber'] = esc_attr($_POST['emailserialnumber']);
                            }
                            if (isset($_POST['cart_title'])) {
                                    $wpStoreCartOptions['cart_title'] = $wpdb->escape($_POST['cart_title']);
                            }
                            if (isset($_POST['single_item'])) {
                                    $wpStoreCartOptions['single_item'] = $wpdb->escape($_POST['single_item']);
                            }
                            if (isset($_POST['multiple_items'])) {
                                    $wpStoreCartOptions['multiple_items'] = $wpdb->escape($_POST['multiple_items']);
                            }
                            if (isset($_POST['currency_symbol'])) {
                                    $wpStoreCartOptions['currency_symbol'] = $wpdb->escape($_POST['currency_symbol']);
                            }
                            if (isset($_POST['currency_symbol_right'])) {
                                    $wpStoreCartOptions['currency_symbol_right'] = $wpdb->escape($_POST['currency_symbol_right']);
                            }
                            if (isset($_POST['subtotal'])) {
                                    $wpStoreCartOptions['subtotal'] = $wpdb->escape($_POST['subtotal']);
                            }
                            if (isset($_POST['update_button'])) {
                                    $wpStoreCartOptions['update_button'] = $wpdb->escape($_POST['update_button']);
                            }
                            if (isset($_POST['checkout_button'])) {
                                    $wpStoreCartOptions['checkout_button'] = $wpdb->escape($_POST['checkout_button']);
                            }
                            if (isset($_POST['currency_code'])) {
                                    $wpStoreCartOptions['currency_code'] = $wpdb->escape($_POST['currency_code']);
                            }

                            if (isset($_POST['checkout_checkmoneyorder_button'])) {
                                    $wpStoreCartOptions['checkout_checkmoneyorder_button'] = $wpdb->escape($_POST['checkout_checkmoneyorder_button']);
                            }

                            if (isset($_POST['checkout_paypal_button'])) {
                                    $wpStoreCartOptions['checkout_paypal_button'] = $wpdb->escape($_POST['checkout_paypal_button']);
                            }
                            if (isset($_POST['checkout_authorizenet_button'])) {
                                    $wpStoreCartOptions['checkout_authorizenet_button'] = $wpdb->escape($_POST['checkout_authorizenet_button']);
                            }
                            if (isset($_POST['checkout_2checkout_button'])) {
                                    $wpStoreCartOptions['checkout_2checkout_button'] = $wpdb->escape($_POST['checkout_2checkout_button']);
                            }
                            if (isset($_POST['remove_link'])) {
                                    $wpStoreCartOptions['remove_link'] = $wpdb->escape($_POST['remove_link']);
                            }
                            if (isset($_POST['empty_button'])) {
                                    $wpStoreCartOptions['empty_button'] = $wpdb->escape($_POST['empty_button']);
                            }
                            if (isset($_POST['empty_message'])) {
                                    $wpStoreCartOptions['empty_message'] = $wpdb->escape($_POST['empty_message']);
                            }
                            if (isset($_POST['item_added_message'])) {
                                    $wpStoreCartOptions['item_added_message'] = $wpdb->escape($_POST['item_added_message']);
                            }
                            if (isset($_POST['enter_coupon'])) {
                                    $wpStoreCartOptions['enter_coupon'] = $wpdb->escape($_POST['enter_coupon']);
                            }
                            if (isset($_POST['price_error'])) {
                                    $wpStoreCartOptions['price_error'] = $wpdb->escape($_POST['price_error']);
                            }
                            if (isset($_POST['quantity_error'])) {
                                    $wpStoreCartOptions['quantity_error'] = $wpdb->escape($_POST['quantity_error']);
                            }
                            if (isset($_POST['checkout_error'])) {
                                    $wpStoreCartOptions['checkout_error'] = $wpdb->escape($_POST['checkout_error']);
                            }
                            if (isset($_POST['success_text'])) {
                                    $wpStoreCartOptions['success_text'] = $wpdb->escape($_POST['success_text']);
                            }
                            if (isset($_POST['failed_text'])) {
                                    $wpStoreCartOptions['failed_text'] = $wpdb->escape($_POST['failed_text']);
                            }
                            if (isset($_POST['add_to_cart'])) {
                                    $wpStoreCartOptions['add_to_cart'] = $wpdb->escape($_POST['add_to_cart']);
                            }
                            if (isset($_POST['out_of_stock'])) {
                                    $wpStoreCartOptions['out_of_stock'] = $wpdb->escape($_POST['out_of_stock']);
                            }
                            if (isset($_POST['ga_trackingnum'])) {
                                    $wpStoreCartOptions['ga_trackingnum'] = $wpdb->escape($_POST['ga_trackingnum']);
                            }
                            if (isset($_POST['wpscjQueryUITheme'])) {
                                    $wpStoreCartOptions['wpscjQueryUITheme'] = $wpdb->escape($_POST['wpscjQueryUITheme']);
                            }
                            if (isset($_POST['shipping_zip_origin'])) {
                                    $wpStoreCartOptions['shipping_zip_origin'] = $wpdb->escape($_POST['shipping_zip_origin']);
                            }
                            if (isset($_POST['enableusps'])) {
                                    $wpStoreCartOptions['enableusps'] = $wpdb->escape($_POST['enableusps']);
                            }
                            if (isset($_POST['enableups'])) {
                                    $wpStoreCartOptions['enableups'] = $wpdb->escape($_POST['enableups']);
                            }
                            if (isset($_POST['enablefedex'])) {
                                    $wpStoreCartOptions['enablefedex'] = $wpdb->escape($_POST['enablefedex']);
                            }
                            if (isset($_POST['storetype'])) {
                                    $wpStoreCartOptions['storetype'] = $wpdb->escape($_POST['storetype']);
                            }
                            if (isset($_POST['uspsapiname'])) {
                                    $wpStoreCartOptions['uspsapiname'] = $wpdb->escape($_POST['uspsapiname']);
                            }

                            if (isset($_POST['displayshipping'])) {
                                    $wpStoreCartOptions['displayshipping'] = $wpdb->escape($_POST['displayshipping']);
                            }
                            if (isset($_POST['displaysubtotal'])) {
                                    $wpStoreCartOptions['displaysubtotal'] = $wpdb->escape($_POST['displaysubtotal']);
                            }
                            if (isset($_POST['displaytotal'])) {
                                    $wpStoreCartOptions['displaytotal'] = $wpdb->escape($_POST['displaytotal']);
                            }
                            if (isset($_POST['total'])) {
                                    $wpStoreCartOptions['total'] = $wpdb->escape($_POST['total']);
                            }
                            if (isset($_POST['shipping'])) {
                                    $wpStoreCartOptions['shipping'] = $wpdb->escape($_POST['shipping']);
                            }

                            if (isset($_POST['requireregistration'])) {
                                    $wpStoreCartOptions['requireregistration'] = $wpdb->escape($_POST['requireregistration']);
                            }
                            if (isset($_POST['enablecoupons'])) {
                                    $wpStoreCartOptions['enablecoupons'] = $wpdb->escape($_POST['enablecoupons']);
                            }
                            if (isset($_POST['login'])) {
                                    $wpStoreCartOptions['login'] = $wpdb->escape($_POST['login']);
                            }
                            if (isset($_POST['register'])) {
                                    $wpStoreCartOptions['register'] = $wpdb->escape($_POST['register']);
                            }

                            if (isset($_POST['logout'])) {
                                    $wpStoreCartOptions['logout'] = $wpdb->escape($_POST['logout']);
                            }
                            if (isset($_POST['username'])) {
                                    $wpStoreCartOptions['username'] = $wpdb->escape($_POST['username']);
                            }
                            if (isset($_POST['password'])) {
                                    $wpStoreCartOptions['password'] = $wpdb->escape($_POST['password']);
                            }
                            if (isset($_POST['email'])) {
                                    $wpStoreCartOptions['email'] = $wpdb->escape($_POST['email']);
                            }
                            if (isset($_POST['myordersandpurchases'])) {
                                    $wpStoreCartOptions['myordersandpurchases'] = $wpdb->escape($_POST['myordersandpurchases']);
                            }

                            if (isset($_POST['required_symbol'])) {
                                    $wpStoreCartOptions['required_symbol'] = $wpdb->escape($_POST['required_symbol']);
                            }
                            if (isset($_POST['required_help'])) {
                                    $wpStoreCartOptions['required_help'] = $wpdb->escape($_POST['required_help']);
                            }

                            if (isset($_POST['flatrateshipping'])) {
                                    $wpStoreCartOptions['flatrateshipping'] = $wpdb->escape($_POST['flatrateshipping']);
                            }
                            if (isset($_POST['flatrateamount'])) {
                                    $wpStoreCartOptions['flatrateamount'] = $wpdb->escape($_POST['flatrateamount']);
                            }
                            if (isset($_POST['calculateshipping'])) {
                                    $wpStoreCartOptions['calculateshipping'] = $wpdb->escape($_POST['calculateshipping']);
                            }
                            if (isset($_POST['itemsperpage'])) {
                                    $wpStoreCartOptions['itemsperpage'] = $wpdb->escape($_POST['itemsperpage']);
                            }
                            if (isset($_POST['allowlibertyreserve'])) {
                                    $wpStoreCartOptions['allowlibertyreserve'] = $wpdb->escape($_POST['allowlibertyreserve']);
                            }
                            if (isset($_POST['allowqbms'])) {
                                    $wpStoreCartOptions['allowqbms'] = $wpdb->escape($_POST['allowqbms']);
                            }
                            if (isset($_POST['qbms_ticket'])) {
                                    $wpStoreCartOptions['qbms_ticket'] = $wpdb->escape($_POST['qbms_ticket']);
                            }
                            if (isset($_POST['qbms_login'])) {
                                    $wpStoreCartOptions['qbms_login'] = $wpdb->escape($_POST['qbms_login']);
                            }
                            if (isset($_POST['qbms_testingmode'])) {
                                    $wpStoreCartOptions['qbms_testingmode'] = $wpdb->escape($_POST['qbms_testingmode']);
                            }

                            if (isset($_POST['libertyreserveaccount'])) {
                                    $wpStoreCartOptions['libertyreserveaccount'] = $wpdb->escape($_POST['libertyreserveaccount']);
                            }
                            if (isset($_POST['libertyreservestore'])) {
                                    $wpStoreCartOptions['libertyreservestore'] = $wpdb->escape($_POST['libertyreservestore']);
                            }
                            if (isset($_POST['checkout_libertyreserve_button'])) {
                                    $wpStoreCartOptions['checkout_libertyreserve_button'] = $wpdb->escape($_POST['checkout_libertyreserve_button']);
                            }
                            if (isset($_POST['libertyreservesecretword'])) {
                                    $wpStoreCartOptions['libertyreservesecretword'] = $wpdb->escape($_POST['libertyreservesecretword']);
                            }
                            if (isset($_POST['guestcheckout'])) {
                                    $wpStoreCartOptions['guestcheckout'] = $wpdb->escape($_POST['guestcheckout']);
                            }
                            if (isset($_POST['useimagebox'])) {
                                    $wpStoreCartOptions['useimagebox'] = $wpdb->escape($_POST['useimagebox']);
                            }
                            if (isset($_POST['showproductgallery'])) {
                                    $wpStoreCartOptions['showproductgallery'] = $wpdb->escape($_POST['showproductgallery']);
                            }
                            if (isset($_POST['showproductgallerywhere'])) {
                                    $wpStoreCartOptions['showproductgallerywhere'] = $wpdb->escape($_POST['showproductgallerywhere']);
                            }

                            if (isset($_POST['displaytaxes'])) {
                                    $wpStoreCartOptions['displaytaxes'] = $wpdb->escape($_POST['displaytaxes']);
                            }
                            if (isset($_POST['tax'])) {
                                    $wpStoreCartOptions['tax'] = $wpdb->escape($_POST['tax']);
                            }
                            if (isset($_POST['checkoutimages'])) {
                                    $wpStoreCartOptions['checkoutimages'] = $wpdb->escape($_POST['checkoutimages']);
                            }
                            if (isset($_POST['checkoutimagewidth'])) {
                                    $wpStoreCartOptions['checkoutimagewidth'] = $wpdb->escape($_POST['checkoutimagewidth']);
                            }
                            if (isset($_POST['checkoutimageheight'])) {
                                    $wpStoreCartOptions['checkoutimageheight'] = $wpdb->escape($_POST['checkoutimageheight']);
                            }
                            if (isset($_POST['checkoutlinktoproduct'])) {
                                    $wpStoreCartOptions['checkoutlinktoproduct'] = $wpdb->escape($_POST['checkoutlinktoproduct']);
                            }
                            if (isset($_POST['displaypriceonview'])) {
                                    $wpStoreCartOptions['displaypriceonview'] = $wpdb->escape($_POST['displaypriceonview']);
                            }
                            if (isset($_POST['menu_style'])) {
                                    $wpStoreCartOptions['menu_style'] = $wpdb->escape($_POST['menu_style']);
                            }
                            if (isset($_POST['orders_profile'])) {
                                    $wpStoreCartOptions['orders_profile'] = $wpdb->escape($_POST['orders_profile']);
                            }
                            if (isset($_POST['cc_name'])) {
                                    $wpStoreCartOptions['cc_name'] = $wpdb->escape($_POST['cc_name']);
                            }
                            if (isset($_POST['cc_number'])) {
                                    $wpStoreCartOptions['cc_number'] = $wpdb->escape($_POST['cc_number']);
                            }
                            if (isset($_POST['cc_expires'])) {
                                    $wpStoreCartOptions['cc_expires'] = $wpdb->escape($_POST['cc_expires']);
                            }
                            if (isset($_POST['cc_expires_month'])) {
                                    $wpStoreCartOptions['cc_expires_month'] = $wpdb->escape($_POST['cc_expires_month']);
                            }
                            if (isset($_POST['cc_expires_year'])) {
                                    $wpStoreCartOptions['cc_expires_year'] = $wpdb->escape($_POST['cc_expires_year']);
                            }
                            if (isset($_POST['cc_address'])) {
                                    $wpStoreCartOptions['cc_address'] = $wpdb->escape($_POST['cc_address']);
                            }
                            if (isset($_POST['cc_postalcode'])) {
                                    $wpStoreCartOptions['cc_postalcode'] = $wpdb->escape($_POST['cc_postalcode']);
                            }
                            if (isset($_POST['cc_cvv'])) {
                                    $wpStoreCartOptions['cc_cvv'] = $wpdb->escape($_POST['cc_cvv']);
                            }
                            if (isset($_POST['checkout_xhtml_type'])) {
                                    $wpStoreCartOptions['checkout_xhtml_type'] = $wpdb->escape($_POST['checkout_xhtml_type']);
                            }
                            if (isset($_POST['disable_inline_styles'])) {
                                    $wpStoreCartOptions['disable_inline_styles'] = $wpdb->escape($_POST['disable_inline_styles']);
                            }
                            if (isset($_POST['field_order_0'])) {
                                    $wpStoreCartOptions['field_order_0'] = $wpdb->escape($_POST['field_order_0']);
                            }
                            if (isset($_POST['field_order_1'])) {
                                    $wpStoreCartOptions['field_order_1'] = $wpdb->escape($_POST['field_order_1']);
                            }
                            if (isset($_POST['field_order_2'])) {
                                    $wpStoreCartOptions['field_order_2'] = $wpdb->escape($_POST['field_order_2']);
                            }
                            if (isset($_POST['field_order_3'])) {
                                    $wpStoreCartOptions['field_order_3'] = $wpdb->escape($_POST['field_order_3']);
                            }
                            if (isset($_POST['field_order_4'])) {
                                    $wpStoreCartOptions['field_order_4'] = $wpdb->escape($_POST['field_order_4']);
                            }
                            if (isset($_POST['pcicompliant'])) {
                                    $wpStoreCartOptions['pcicompliant'] = $wpdb->escape($_POST['pcicompliant']);
                            }
                            if (isset($_POST['paypalipnurl'])) {
                                    $wpStoreCartOptions['paypalipnurl'] = $wpdb->escape($_POST['paypalipnurl']);
                            }

                            if (isset($_POST['button_classes_addtocart'])) {
                                    $wpStoreCartOptions['button_classes_addtocart'] = $wpdb->escape($_POST['button_classes_addtocart']);
                            }
                            if (isset($_POST['button_classes_checkout'])) {
                                    $wpStoreCartOptions['button_classes_checkout'] = $wpdb->escape($_POST['button_classes_checkout']);
                            }
                            if (isset($_POST['button_classes_meta'])) {
                                    $wpStoreCartOptions['button_classes_meta'] = $wpdb->escape($_POST['button_classes_meta']);
                            }

                            if (isset($_POST['trial_period_1'])) {
                                    $wpStoreCartOptions['trial_period_1'] = $wpdb->escape($_POST['trial_period_1']);
                            }
                            if (isset($_POST['trial_period_2'])) {
                                    $wpStoreCartOptions['trial_period_2'] = $wpdb->escape($_POST['trial_period_2']);
                            }
                            if (isset($_POST['subscription_price'])) {
                                    $wpStoreCartOptions['subscription_price'] = $wpdb->escape($_POST['subscription_price']);
                            }
                            if (isset($_POST['for'])) {
                                    $wpStoreCartOptions['for'] = $wpdb->escape($_POST['for']);
                            }
                            if (isset($_POST['afterwards'])) {
                                    $wpStoreCartOptions['afterwards'] = $wpdb->escape($_POST['afterwards']);
                            }
                            if (isset($_POST['every'])) {
                                    $wpStoreCartOptions['every'] = $wpdb->escape($_POST['every']);
                            }
                            if (isset($_POST['free'])) {
                                    $wpStoreCartOptions['free'] = $wpdb->escape($_POST['free']);
                            }

                            if (isset($_POST['day'])) {
                                    $wpStoreCartOptions['day'] = $wpdb->escape($_POST['day']);
                            }
                            if (isset($_POST['week'])) {
                                    $wpStoreCartOptions['week'] = $wpdb->escape($_POST['week']);
                            }
                            if (isset($_POST['month'])) {
                                    $wpStoreCartOptions['month'] = $wpdb->escape($_POST['month']);
                            }
                            if (isset($_POST['year'])) {
                                    $wpStoreCartOptions['year'] = $wpdb->escape($_POST['year']);
                            }
                            if (isset($_POST['buy_now'])) {
                                    $wpStoreCartOptions['buy_now'] = $wpdb->escape($_POST['buy_now']);
                            }

                            if (isset($_POST['allowmb'])) {
                                    $wpStoreCartOptions['allowmb'] = $wpdb->escape($_POST['allowmb']);
                            }
                            if (isset($_POST['mb_login'])) {
                                    $wpStoreCartOptions['mb_login'] = $wpdb->escape($_POST['mb_login']);
                            }
                            if (isset($_POST['mb_secretword'])) {
                                    $wpStoreCartOptions['mb_secretword'] = $wpdb->escape($_POST['mb_secretword']);
                            }
                            if (isset($_POST['mb_logo'])) {
                                    $wpStoreCartOptions['mb_logo'] = $wpdb->escape($_POST['mb_logo']);
                            }
                            if (isset($_POST['mb_currency'])) {
                                    $wpStoreCartOptions['mb_currency'] = $wpdb->escape($_POST['mb_currency']);
                            }

                            if (isset($_POST['checkout_moneybookers_button'])) {
                                    $wpStoreCartOptions['checkout_moneybookers_button'] = $wpdb->escape($_POST['checkout_moneybookers_button']);
                            }

                            if (isset($_POST['show_price_to_guests'])) {
                                    $wpStoreCartOptions['show_price_to_guests'] = $wpdb->escape($_POST['show_price_to_guests']);
                            }
                            if (isset($_POST['logged_out_price'])) {
                                    $wpStoreCartOptions['logged_out_price'] = $wpdb->escape($_POST['logged_out_price']);
                            }
                            if (isset($_POST['uninstall'])) {
                                    $wpStoreCartOptions['uninstall'] = $wpdb->escape($_POST['uninstall']);
                            }
                            if (isset($_POST['qty'])) {
                                    $wpStoreCartOptions['qty'] = $wpdb->escape($_POST['qty']);
                            }                                
                            if (isset($_POST['combo_enable'])) {
                                    $wpStoreCartOptions['combo_enable'] = $wpdb->escape($_POST['combo_enable']);
                            }  
                            if (isset($_POST['combo_display_prices'])) {
                                    $wpStoreCartOptions['combo_display_prices'] = $wpdb->escape($_POST['combo_display_prices']);
                            }  
                            if (isset($_POST['combo_display_links'])) {
                                    $wpStoreCartOptions['combo_display_links'] = $wpdb->escape($_POST['combo_display_links']);
                            }  
                            if (isset($_POST['combo_display_thumbs'])) {
                                    $wpStoreCartOptions['combo_display_thumbs'] = $wpdb->escape($_POST['combo_display_thumbs']);
                            }                                  
                            if (isset($_POST['redirect_to_checkout'])) {
                                    $wpStoreCartOptions['redirect_to_checkout'] = $wpdb->escape($_POST['redirect_to_checkout']);
                            }        
                            if (isset($_POST['debug_parameter'])) {
                                    $wpStoreCartOptions['debug_parameter'] = $wpdb->escape($_POST['debug_parameter']);
                            }               
                            if (isset($_POST['where_to_display_accessories'])) {
                                    $wpStoreCartOptions['where_to_display_accessories'] = $wpdb->escape($_POST['where_to_display_accessories']);
                            }                            
                            if (isset($_POST['completely_disable_shareyourcart'])) {
                                    $wpStoreCartOptions['completely_disable_shareyourcart'] = $wpdb->escape($_POST['completely_disable_shareyourcart']);
                            }   
                            if (isset($_POST['piwik_url'])) {
                                    $wpStoreCartOptions['piwik_url'] = $wpdb->escape($_POST['piwik_url']);
                            }                            
                            if (isset($_POST['piwik_enabled'])) {
                                    $wpStoreCartOptions['piwik_enabled'] = $wpdb->escape($_POST['piwik_enabled']);
                            }                  
                            if (isset($_POST['piwik_siteid'])) {
                                    $wpStoreCartOptions['piwik_siteid'] = $wpdb->escape($_POST['piwik_siteid']);
                            }  

                            if (isset($_POST['admin_capability'])) {
                                    global $wp_roles;
                                    $wpStoreCartOptions['admin_capability'] = $wpdb->escape($_POST['admin_capability']);
                                    if($wpStoreCartOptions['admin_capability']=='administrator') {
                                        $wp_roles->remove_cap( 'editor', 'manage_wpstorecart' );
                                        $wp_roles->remove_cap( 'author', 'manage_wpstorecart' );
                                        $wp_roles->remove_cap( 'contributor', 'manage_wpstorecart' );
                                    }
                                    if($wpStoreCartOptions['admin_capability']=='editor') {
                                        $wp_roles->add_cap( 'administrator', 'manage_wpstorecart' );
                                        $wp_roles->add_cap( 'editor', 'manage_wpstorecart' );
                                        $wp_roles->remove_cap( 'author', 'manage_wpstorecart' );
                                        $wp_roles->remove_cap( 'contributor', 'manage_wpstorecart' );
                                    }
                                    if($wpStoreCartOptions['admin_capability']=='author') {
                                        $wp_roles->add_cap( 'administrator', 'manage_wpstorecart' );
                                        $wp_roles->add_cap( 'editor', 'manage_wpstorecart' );
                                        $wp_roles->add_cap( 'author', 'manage_wpstorecart' );
                                        $wp_roles->remove_cap( 'contributor', 'manage_wpstorecart' );
                                    }
                                    if($wpStoreCartOptions['admin_capability']=='contributor') {
                                        $wp_roles->add_cap( 'administrator', 'manage_wpstorecart' );
                                        $wp_roles->add_cap( 'editor', 'manage_wpstorecart' );
                                        $wp_roles->add_cap( 'author', 'manage_wpstorecart' );
                                        $wp_roles->add_cap( 'contributor', 'manage_wpstorecart' );
                                    }
                            }

                            
                            update_option($this->adminOptionsName, $wpStoreCartOptions);

                            if (isset($_POST['required_info_key']) && isset($_POST['required_info_name']) && isset($_POST['required_info_type'])) {
                                $arrayCounter = 0;
                                $table_name777 = $wpdb->prefix . "wpstorecart_meta";
                                foreach ($_POST['required_info_key'] as $currentKey) {
                                    $updateSQL = "UPDATE  `{$table_name777}` SET  `value` =  '{$_POST['required_info_name'][$arrayCounter]}||{$_POST['required_info_required_'.$currentKey]}||{$_POST['required_info_type'][$arrayCounter]}' WHERE  `primkey` ={$currentKey};";
                                    $wpdb->query($updateSQL);
                                    $arrayCounter++;
                                }
                            }                    
                
                
                
                }
		
		
	}
}

?>