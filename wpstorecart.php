<?php
/*
Plugin Name: wpStoreCart
Plugin URI: http://www.wpstorecart.com/
Description: <a href="http://www.wpstorecart.com/" target="blank">wpStoreCart</a> is a full e-commerce Wordpress plugin that accepts PayPal out of the box. It includes multiple widgets, dashboard widgets, shortcodes, and works using Wordpress pages to keep everything nice and simple. 
Version: 2.1.1
Author: wpStoreCart.com
Author URI: http://www.wpstorecart.com/
License: LGPL
*/

/*  
Copyright 2010 wpStoreCart.com  (email : admin@wpstorecart.com)

This library is free software; you can redistribute it and/or modify it under the terms 
of the GNU Lesser General Public License as published by the Free Software Foundation; 
either version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
without even the implied warranty of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. 
See the GNU Lesser General Public License for more details.

You should have received a copy of the GNU Lesser General Public License along with this 
library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, 
Boston, MA 02111-1307 USA 
*/

global $wpStoreCart, $cart, $wpsc, $wpstorecart_version, $wpstorecart_version_int, $testing_mode, $wpstorecart_db_version;

//Global variables:
$wpstorecart_version = '2.1.1';
$wpstorecart_version_int = 201001; // M_m_u_ which is 2 digits for Major, minor, and updates, so version 2.0.14 would be 200014
$wpstorecart_db_version = '2.1.0'; // Indicates the last version in which the database schema was altered
$testing_mode = false; // Enables or disable testing mode.  Should be set to false unless using on a test site, with test data, with no actual customers
$APjavascriptQueue = NULL;

// Pre-2.6 compatibility, which is actually frivilous since we use the 2.8+ widget technique
if ( ! defined( 'WP_CONTENT_URL' ) )
	define( 'WP_CONTENT_URL', get_option( 'siteurl' ) . '/wp-content' );
if ( ! defined( 'WP_CONTENT_DIR' ) )
	define( 'WP_CONTENT_DIR', ABSPATH . 'wp-content' );
if ( ! defined( 'WP_PLUGIN_URL' ) )
	define( 'WP_PLUGIN_URL', WP_CONTENT_URL. '/plugins' );
if ( ! defined( 'WP_PLUGIN_DIR' ) )
	define( 'WP_PLUGIN_DIR', WP_CONTENT_DIR . '/plugins' );

// Create the proper directory structure if it is not already created
if(!is_dir(WP_CONTENT_DIR . '/uploads/')) {
	@mkdir(WP_CONTENT_DIR . '/uploads/', 0777, true);
}
if(!is_dir(WP_CONTENT_DIR . '/uploads/wpstorecart/')) {
	@mkdir(WP_CONTENT_DIR . '/uploads/wpstorecart/', 0777, true);
}
	

 /**
 * ===============================================================================================================
 * Main wpStoreCart Class
 */	
if (!class_exists("wpStoreCart")) {

    class wpStoreCart {
		var $adminOptionsName = "wpStoreCartAdminOptions";
		
        function wpStoreCart() { //constructor
            global $wpdb, $wpstorecart_db_version;

            $devOptions = $this->getAdminOptions();

            // Upgrade the database schema if they're running 2.0.2 or below:
            if($devOptions['database_version']==NULL) { // 2.0.2 - Database schema update for version 2.0.1 and below
                $table_name = $wpdb->prefix . "wpstorecart_categories";
                $sql = "ALTER TABLE `{$table_name}` ADD `thumbnail` VARCHAR( 512 ) NOT NULL, ADD `description` TEXT NOT NULL, ADD `postid` INT NOT NULL ";
                $results = $wpdb->query( $sql );
                $devOptions['database_version'] = $wpstorecart_db_version;
                update_option('wpStoreCartAdminOptions', $devOptions);
            }

            if($devOptions['database_version']==NULL || $devOptions['database_version']=='2.0.2') { // 2.0.11 - Database schema update for version 2.0.10 and below
		   $table_name = $wpdb->prefix . "wpstorecart_meta";
		   if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {

			$sql = "
				CREATE TABLE {$table_name} (
				`primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
				`value` TEXT NOT NULL,
				`type` VARCHAR(32) NOT NULL,
				`foreignkey` INT NOT NULL
				);
				";


			$results = $wpdb->query( $sql );
                        $devOptions['database_version'] = $wpstorecart_db_version;
                        update_option('wpStoreCartAdminOptions', $devOptions);
			}
            }

            if($devOptions['database_version']==NULL || $devOptions['database_version']=='2.0.2' || $devOptions['database_version']=='2.0.11') { // 2.1.0 - Database schema update for 2.0.13 and below
                        $table_name = $wpdb->prefix . "wpstorecart_products";
                        $sql = "ALTER TABLE `{$table_name}` ADD `donation` BOOLEAN NOT NULL DEFAULT '0';";
                        $results = $wpdb->query( $sql );
                        $devOptions['database_version'] = $wpstorecart_db_version;
                        update_option('wpStoreCartAdminOptions', $devOptions);
            }

            // This increments the add to cart counter for the product statistics
            if(isset($_POST['my-item-id'])) {
                $primkey = $_POST['my-item-id'];

                // Product variations have their primkey's like this: 1-59 where 1 is the product's primkey and the 59 represents the variations primkey
                if(is_numeric($primkey)) {
                        $newprimkey = $primkey;
                } else {
                        $explodeprimkey = explode('-', $primkey);
                        $newprimkey = intval($explodeprimkey[0]);
                }

                if(is_numeric($newprimkey)) {
                        $table_name = $wpdb->prefix . "wpstorecart_products";
                        $sql = "SELECT * FROM `{$table_name}` WHERE `primkey`={$newprimkey};";
                        $results = $wpdb->get_results( $sql , ARRAY_A );
                        
                        if(isset($results)) {
                                $newTimesAddedToCart = $results[0]['timesaddedtocart'] + 1;
                                $wpdb->query("UPDATE `{$table_name}` SET `timesaddedtocart` = '{$newTimesAddedToCart}' WHERE `primkey` = {$newprimkey} LIMIT 1 ;");
                        }
                }

            }
        }

       function wpscError($theError='unknown', $variables=NULL) {
           $output = "<div id='wpsc-warning' class='updated fade'><p>";
           if($theError=='nopage') {
               $output .= __('<strong>wpStoreCart is not properly configured at this time.</strong>  You\'ll need to either have wpStoreCart automatically create a "main page" and a "checkout page" for your store by <a href="?page=wpstorecart-admin&wpscaction=createpages">clicking here</a>, or you can create your own and then visit <a href="?page=wpstorecart-settings">the settings page</a> to tell wpStoreCart which pre-existing pages to use.  See <a href="http://wpstorecart.com/documentation/error-messages/" target="_blank">this help entry</a> for more details.');
           }
           if($theError=='register_globals') {
               $output .= __('<strong>wpStoreCart has detected that register_globals is set to ON.</strong>  This is a major security risk that can make it much easier for a hacker to gain full access to your website and it\'s data.  Please disable register_globals by following <a href="http://wpstorecart.com/forum/viewtopic.php?f=2&t=2" target="_blank">the directions here</a> before using wpStoreCart. Your shopping cart, checkout, and add to cart functionality will not work while register_globals is set to On. See <a href="http://wpstorecart.com/documentation/error-messages/" target="_blank">this help entry</a> for more details.');
           }
           if($theError=='nouploadsdir') {
               $output .= '<strong>wpStoreCart has detected that a required folder is missing and we could not automatically create it.</strong>  Please manually create this folder and give it 0777 permissions: '.$variables;
           }
           if($theError=='testingmode') {
               $output .= __('<strong>wpStoreCart "<a href="http://wpstorecart.com/documentation/advanced-technical-topics/testing-mode/" target="_blank">Testing Mode</a>" enabled. DO NOT USE TESTING MODE ON A SERVER THAT IS CONNECTED TO THE INTERNET. DO NOT USE IT ON A LIVE WEBSITE. DO NOT USE IT WITH ACTUAL CUSTOMERS OR EVEN ACTUAL CUSTOMER DATA. ONLY USE TESTING MODE ON A TEST SERVER, WITH TEST DATA.</strong>  Visit <a href="http://wpstorecart.com/documentation/advanced-technical-topics/testing-mode/" target="_blank">this topic</a> for information on how to disable Testing Mode and this message.  ');
           }
           $output .= "</p></div>";
           return $output;
       }

        function wpscErrorRegisterGlobals() {
            echo $this->wpscError('register_globals');
        }

        function wpscErrorNoPage() {
            if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
                echo $this->wpscError('nopage');
            }
        }

        function wpscErrorTestingMode() {
            echo $this->wpscError('testingmode');
        }

        function wpscErrorNoUploadDir() {
            echo $this->wpscError('nouploadsdir',WP_CONTENT_DIR . '/uploads/');
        }

        function wpscErrorNoUploadWpDir() {
            echo $this->wpscError('nouploadsdir',WP_CONTENT_DIR . '/uploads/wpstorecart/');
        }

        function register_custom_init() {
            global $testing_mode;

            $devOptions = $this->getAdminOptions();

            if($testing_mode==true) {
                add_action('admin_notices', array(&$this, 'wpscErrorTestingMode'));
            }

            if(!is_dir(WP_CONTENT_DIR . '/uploads/')) {
                    add_action('admin_notices', array(&$this, 'wpscErrorNoUploadDir'));
            }
            if(!is_dir(WP_CONTENT_DIR . '/uploads/wpstorecart/')) {
                   add_action('admin_notices', array(&$this, 'wpscErrorNoUploadWpDir'));
            }

            if (@ini_get('register_globals')==1) {
                add_action('admin_notices', array(&$this, 'wpscErrorRegisterGlobals'));
            }
            if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
                add_action('admin_notices', array(&$this, 'wpscErrorNoPage'));
            }

            // This block of code is for incrementing the add to cart log
            if(isset($_POST['my-item-id'])) {
                $primkey = $_POST['my-item-id'];

                // Product variations have their primkey's like this: 1-59 where 1 is the product's primkey and the 59 represents the variations primkey
                if(is_numeric($primkey)) {
                        $newprimkey = $primkey;
                } else {
                        $explodeprimkey = explode('-', $primkey);
                        $newprimkey = intval($explodeprimkey[0]);
                }

                if(is_numeric($newprimkey)) {
                    global $current_user, $wpdb;
                    wp_get_current_user();
                    if ( 0 == $current_user->ID ) {
                        // Not logged in.
                        $theuser = 0;
                    } else {
                        $theuser = $current_user->ID;
                    }
                    $wpdb->query("INSERT INTO `{$wpdb->prefix}wpstorecart_log` (`primkey` ,`action` ,`data` ,`foreignkey` ,`date` ,`userid`) VALUES (NULL, 'addtocart', '{$_SERVER['REMOTE_ADDR']}', '{$newprimkey}', '".date('Ymd')."', '{$theuser}');");
                }
            }
            /* The use of custom post types will be implemented in a future version of wpsc
            if(get_bloginfo('version') >= 3) { // If we're using Wordpress 3 or higher, use custom post types
                $labels = array(
                'name' => _x('Products', 'post type general name'),
                'singular_name' => _x('Product', 'post type singular name'),
                'add_new' => _x('Add New', 'product'),
                'add_new_item' => __('Add New Product'),
                'edit_item' => __('Edit Product'),
                'new_item' => __('New Product'),
                'view_item' => __('View Product'),
                'search_items' => __('Search Products'),
                'not_found' =>  __('No products found'),
                'not_found_in_trash' => __('No products found in Trash'),
                'parent_item_colon' => ''
                );
                $args = array(
                'labels' => $labels,
                'public' => true,
                'publicly_queryable' => true,
                'show_ui' => true,
                'query_var' => true,
                'rewrite' => true,
                'capability_type' => 'page',
                'hierarchical' => true,
                'menu_position' => null,
                'supports' => array('title','editor','author','thumbnail','excerpt','comments')
                );
                register_post_type('wpsc_product',$args);

            }
             *
             */
        }

		function  init() {
            $this->getAdminOptions();
        }
		
		function spHeader() {
                        global $wpstorecart_version_int, $testing_mode;
                        echo'
			
			<div class="wrap">
			<div style="padding: 20px 10px 10px 10px;">
			<div style="float:left;"><a href="http://wpstorecart.com" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/logo.png" alt="wpstorecart" /></a><br />';if(!file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.pro.php')) { echo '<a href="http://wpstorecart.com/store/business-support-wpstorecart-pro/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/order_pro.png" alt="wpstorecart" /></a>';}
                        echo'</div>';
                        if(!file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.pro.php')) {
                            echo '
			<div style="float:right;">
				
                                <a style="position:absolute;top:50px;margin-left:-200px;" href="http://wpstorecart.com/design-mods-support/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/hire_us.png" alt="wpstorecart" /></a>
			</div>
                        ';
                        }

                        echo '
			<br style="clear:both;" />
			<ul id="jsddm">
				<li class="tab"style="border-left:1px solid #999;"><a href="admin.php?page=wpstorecart-admin" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/controller.png" /> &nbsp;</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-settings" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/application_form_edit.png" /> Settings</a>
                                    <ul>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab1" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/application_form_edit.png" /> General</a>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab2" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/email.png" /> E-Mail</a>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab3" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/css.png" /> Display</a>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab4" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/creditcards.png" /> Payment</a>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab5" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/text_padding_top.png" /> Language</a>
                                    </ul>
                                </li>
				<li class="tab"><a href="admin.php?page=wpstorecart-add-products" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/basket_add.png" />Products</a>
                                    <ul>
                                        <li><a href="admin.php?page=wpstorecart-add-products" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/basket_add.png" /> Add Product</a></li>
                                        <li><a href="admin.php?page=wpstorecart-edit-products" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/basket_edit.png" /> Edit Products</a></li>
                                        ';
                                        if($testing_mode==true || $wpstorecart_version_int >= 202000) { // Bleeding edge until 2.2, at which time this code block will automatically be enabled
                                            echo '<li><a href="admin.php?page=wpstorecart-import" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/server_go.png" /> Import/Export</a></li>';
                                        }

                                    echo '
                                    </ul>
                                </li>
				
				<li class="tab"><a href="admin.php?page=wpstorecart-categories" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/table.png" /> Categories</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-orders" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/cart_go.png" /> Orders</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-coupon" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/money.png" /> Marketing</a>
                                    <ul>
                                        <li class="tab"><a href="admin.php?page=wpstorecart-coupon" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/money.png" /> Coupons</a></li>
                                    </ul>
                                </li>
				<li class="tab"><a href="admin.php?page=wpstorecart-affiliates" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/user_suit.png" /> Affiliates</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-statistics" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/chart_bar.png" /> Statistics</a></li>
				<li class="tab" style="border-right:1px solid #999;"><a href="http://wpstorecart.com/help-support/" target="_blank" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" /> Help</a>
                                    <ul>
                                        <li><a href="http://wpstorecart.com/forum/" class="spmenu"  target="_blank">Support Forum</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/initial-settings/" class="spmenu"  target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/application_form_edit.png" /> Initial Settings</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/adding-editing-products/" class="spmenu"  target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/basket_add.png" /> Products</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/widgets/" class="spmenu"  target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/text_padding_top.png" /> Widgets</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/coupons/" class="spmenu"  target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/money.png" /> Coupons</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/shortcodes/" class="spmenu"  target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/text_padding_top.png" /> Shortcodes</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/error-messages/" class="spmenu"  target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/cross.png" /> Error Messages</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/styles-designs/" class="spmenu"  target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/css.png" /> Styles &amp; Design</a></li>
                                        <li><a href="http://wpstorecart.com/faq/" class="spmenu"  target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" /> FAQ</a></li>
                                        <li><a href="http://wpstorecart.com/help-support/" class="spmenu"  target="_blank">More Help</a></li>

                                    </ul>
                                </li>
			</ul>
			<br style="clear:both;" />

			';

		}		
		
		
		//Returns an array of admin options
        function getAdminOptions() {
		
            $apAdminOptions = array('mainpage' => '',
                                    'checkoutpage' => '',
                                    'checkoutpageurl' => '',
                                    'turnon_wpstorecart' => 'false',
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
                                    'checkmoneyordertext' => 'Please send a check or money order for the above amount to:<br /><br /><strong>My Business Name<br />1234 My Address, Suite ABC<br />New York, NY 24317, USA</strong><br /><br />Please allow 4 to 6 weeks for delivery.',
                                    'paypalemail' => get_bloginfo('admin_email'),
                                    'paypaltestmode' => 'false',
                                    'allowauthorizenet' => 'false',
                                    'authorizenetemail' => '',
                                    'authorizenetsecretkey' => '',
                                    'authorizenettestmode' => 'false',
                                    'allow2checkout' => 'false',
                                    '2checkoutemail' => '',
                                    '2checkouttestmode' => 'false',
                                    'emailonpurchase' => 'Dear [customername], thanks for your recent order from [sitename].  Your order has been submitted to our staff for approval.  This process can take as little as an hour to as long as a few weeks depending on how quickly your payment clears, and whether there are other issues which may cause a delay.',
                                    'emailonapproval' => 'Dear [customername], thanks again for your recent order from [sitename].  This email is to inform you that your order has been approved.  For physical products, this does not mean that they have been shipped yet; as you will get another email when the order is shipped.  If you ordered a digital download, your download is now available.  .',
                                    'emailonshipped'  => 'Dear [customername], thanks again for your recent order from [sitename].  This email is to inform you that your order has been shipped.',
                                    'emailsig' => 'Thanks again, [sitename] Management',
                                    'cart_title' => 'Shopping Cart',
                                    'single_item' => 'Item',
                                    'multiple_items' => 'Items',
                                    'currency_symbol' => '$',
                                    'currency_symbol_right' => '',
                                    'subtotal' => 'Subtotal',
                                    'update_button' => 'update',
                                    'checkout_button' => 'checkout',
                                    'currency_code' => 'USD',
                                    'checkout_checkmoneyorder_button' => 'Checkout with Check/Money Order',
                                    'checkout_paypal_button' => 'Checkout with PayPal',
                                    'checkout_authorizenet_button' => 'Checkout with Authorize.NET',
                                    'checkout_2checkout_button' => 'Checkout with 2CheckOut',
                                    'remove_link' => 'remove',
                                    'empty_button' => 'empty',
                                    'empty_message' => 'Your cart is empty!',
                                    'item_added_message' => 'Item added!',
                                    'enter_coupon' => 'Enter coupon:',
                                    'price_error' => 'Invalid price format!',
                                    'quantity_error' => 'Item quantities must be whole numbers!',
                                    'checkout_error' => 'Your order could not be processed!',
                                    'success_text' => 'Dear [customername], thanks for your recent order from [sitename].  Your order has been submitted to our staff for approval.  This process can take as little as an hour to as long as a few weeks depending on how quickly your payment clears, and whether there are other issues which may cause a delay.',
                                    'failed_text' => 'Dear [customername], thanks for your recent order from [sitename].  However, we encountered problems with your order and are unable to fulfill it at this time.  Please contact us for more information.',
                                    'add_to_cart' => 'Add to Cart',
                                    'out_of_stock' => 'Out of Stock!',
                                    'ga_trackingnum' => '',
                                    'database_version' => NULL,
                                    'minimumAffiliatePayment' => '0.00',
                                    'minimumDaysBeforePaymentEligable' => '30',
                                    'affiliateInstructions'=>'Welcome to our affiliate program.  Here, you can review successful affiliate sales as well as grab links to all the products in our store that include your affiliate code.',
                                    'wpscjQueryUITheme' =>''
                                    );

            $devOptions = get_option($this->adminOptionsName);
            if (!empty($devOptions)) {
                foreach ($devOptions as $key => $option) {
                    $apAdminOptions[$key] = $option;
                }
            }            
            update_option($this->adminOptionsName, $apAdminOptions);
            return $apAdminOptions;
        }
		
		//Prints out the admin page ================================================================================
        function printAdminPage() {
			global $wpdb;

                //must check that the user has the required capability
                if (!current_user_can('manage_options'))
                {
                  wp_die( __('wpStoreCart: You do not have sufficient permissions to access this page.') );
                }


                        $devOptions = $this->getAdminOptions();
		
			if (isset($_POST['update_wpStoreCartSettings'])) {
				if (isset($_POST['wpStoreCartmainpage'])) {
					$devOptions['mainpage'] = $wpdb->escape($_POST['wpStoreCartmainpage']);
				} 		
				if (isset($_POST['checkoutpage'])) {
					$devOptions['checkoutpage'] = $wpdb->escape($_POST['checkoutpage']);
					$devOptions['checkoutpageurl'] = get_permalink($devOptions['checkoutpage']);
				} 					
				if (isset($_POST['turnwpStoreCartOn'])) {
					$devOptions['turnon_wpstorecart'] = $wpdb->escape($_POST['turnwpStoreCartOn']);
				}   
				if (isset($_POST['wpStoreCartEmail'])) {
					$devOptions['wpStoreCartEmail'] = $wpdb->escape($_POST['wpStoreCartEmail']);
				}   				
				if (isset($_POST['wpStoreCartwidth'])) {
					$devOptions['wpStoreCartwidth'] = $wpdb->escape($_POST['wpStoreCartwidth']);
				}
				if (isset($_POST['wpscCss'])) {
					$devOptions['wpscCss'] = $wpdb->escape($_POST['wpscCss']);
				}

				if (isset($_POST['frontpageDisplays'])) {
					$devOptions['frontpageDisplays'] = $wpdb->escape($_POST['frontpageDisplays']);
				}
				if (isset($_POST['displayThumb'])) {
					$devOptions['displayThumb'] = $wpdb->escape($_POST['displayThumb']);
				}
				if (isset($_POST['displayTitle'])) {
					$devOptions['displayTitle'] = $wpdb->escape($_POST['displayTitle']);
				}
				if (isset($_POST['displayintroDesc'])) {
					$devOptions['displayintroDesc'] = $wpdb->escape($_POST['displayintroDesc']);
				}
				if (isset($_POST['displayFullDesc'])) {
					$devOptions['displayFullDesc'] = $wpdb->escape($_POST['displayFullDesc']);
				}
				if (isset($_POST['displayType'])) {
					$devOptions['displayType'] = $wpdb->escape($_POST['displayType']);
				}
				if (isset($_POST['displayAddToCart'])) {
					$devOptions['displayAddToCart'] = $wpdb->escape($_POST['displayAddToCart']);
				}
				if (isset($_POST['displayBuyNow'])) {
					$devOptions['displayBuyNow'] = $wpdb->escape($_POST['displayBuyNow']);
				}
				if (isset($_POST['displayPrice'])) {
					$devOptions['displayPrice'] = $wpdb->escape($_POST['displayPrice']);
				}

				if (isset($_POST['wpStoreCartheight'])) {
					$devOptions['wpStoreCartheight'] = $wpdb->escape($_POST['wpStoreCartheight']);
				}		
				if (isset($_POST['showproductthumbnail'])) {
					$devOptions['showproductthumbnail'] = $wpdb->escape($_POST['showproductthumbnail']);
				}
				if (isset($_POST['showproductdescription'])) {
					$devOptions['showproductdescription'] = $wpdb->escape($_POST['showproductdescription']);
				}

				if (isset($_POST['allowcheckmoneyorder'])) {
					$devOptions['allowcheckmoneyorder'] = $wpdb->escape($_POST['allowcheckmoneyorder']);
				}
				if (isset($_POST['checkmoneyordertext'])) {
					$devOptions['checkmoneyordertext'] = $wpdb->escape($_POST['checkmoneyordertext']);
				}

				if (isset($_POST['allowpaypal'])) {
					$devOptions['allowpaypal'] = $wpdb->escape($_POST['allowpaypal']);
				}
				if (isset($_POST['paypalemail'])) {
					$devOptions['paypalemail'] = $wpdb->escape($_POST['paypalemail']);
				}
				if (isset($_POST['paypaltestmode'])) {
					$devOptions['paypaltestmode'] = $wpdb->escape($_POST['paypaltestmode']);
				}

				if (isset($_POST['allowauthorizenet'])) {
					$devOptions['allowauthorizenet'] = $wpdb->escape($_POST['allowauthorizenet']);
				}
				if (isset($_POST['authorizenettestmode'])) {
					$devOptions['authorizenettestmode'] = $wpdb->escape($_POST['authorizenettestmode']);
				}
				if (isset($_POST['authorizenetemail'])) {
					$devOptions['authorizenetemail'] = $wpdb->escape($_POST['authorizenetemail']);
				}
				if (isset($_POST['authorizenetsecretkey'])) {
					$devOptions['authorizenetsecretkey'] = $wpdb->escape($_POST['authorizenetsecretkey']);
				}

                                
				if (isset($_POST['allow2checkout'])) {
					$devOptions['allow2checkout'] = $wpdb->escape($_POST['allow2checkout']);
				}
				if (isset($_POST['2checkouttestmode'])) {
					$devOptions['2checkouttestmode'] = $wpdb->escape($_POST['2checkouttestmode']);
				}
				if (isset($_POST['2checkoutemail'])) {
					$devOptions['2checkoutemail'] = $wpdb->escape($_POST['2checkoutemail']);
				}


				if (isset($_POST['emailonpurchase'])) {
					$devOptions['emailonpurchase'] = $wpdb->escape($_POST['emailonpurchase']);
				}	
				if (isset($_POST['emailonapproval'])) {
					$devOptions['emailonapproval'] = $wpdb->escape($_POST['emailonapproval']);
				}	
				if (isset($_POST['emailonshipped'])) {
					$devOptions['emailonshipped'] = $wpdb->escape($_POST['emailonshipped']);
				}	
				if (isset($_POST['emailsig'])) {
					$devOptions['emailsig'] = $wpdb->escape($_POST['emailsig']);
				}					
				if (isset($_POST['cart_title'])) {
 					$devOptions['cart_title'] = $wpdb->escape($_POST['cart_title']);
				}
				if (isset($_POST['single_item'])) {
 					$devOptions['single_item'] = $wpdb->escape($_POST['single_item']);
				}
				if (isset($_POST['multiple_items'])) {
 					$devOptions['multiple_items'] = $wpdb->escape($_POST['multiple_items']);
				}
				if (isset($_POST['currency_symbol'])) {
 					$devOptions['currency_symbol'] = $wpdb->escape($_POST['currency_symbol']);
				}
				if (isset($_POST['currency_symbol_right'])) {
 					$devOptions['currency_symbol_right'] = $wpdb->escape($_POST['currency_symbol_right']);
				}
				if (isset($_POST['subtotal'])) {
 					$devOptions['subtotal'] = $wpdb->escape($_POST['subtotal']);
				}
				if (isset($_POST['update_button'])) {
 					$devOptions['update_button'] = $wpdb->escape($_POST['update_button']);
				}
				if (isset($_POST['checkout_button'])) {
 					$devOptions['checkout_button'] = $wpdb->escape($_POST['checkout_button']);
				}
				if (isset($_POST['currency_code'])) {
 					$devOptions['currency_code'] = $wpdb->escape($_POST['currency_code']);
				}

				if (isset($_POST['checkout_checkmoneyorder_button'])) {
 					$devOptions['checkout_checkmoneyorder_button'] = $wpdb->escape($_POST['checkout_checkmoneyorder_button']);
				}

				if (isset($_POST['checkout_paypal_button'])) {
 					$devOptions['checkout_paypal_button'] = $wpdb->escape($_POST['checkout_paypal_button']);
				}
				if (isset($_POST['checkout_authorizenet_button'])) {
 					$devOptions['checkout_authorizenet_button'] = $wpdb->escape($_POST['checkout_authorizenet_button']);
				}
				if (isset($_POST['checkout_2checkout_button'])) {
 					$devOptions['checkout_2checkout_button'] = $wpdb->escape($_POST['checkout_2checkout_button']);
				}
				if (isset($_POST['remove_link'])) {
 					$devOptions['remove_link'] = $wpdb->escape($_POST['remove_link']);
				}
				if (isset($_POST['empty_button'])) {
 					$devOptions['empty_button'] = $wpdb->escape($_POST['empty_button']);
				}
				if (isset($_POST['empty_message'])) {
 					$devOptions['empty_message'] = $wpdb->escape($_POST['empty_message']);
				}
				if (isset($_POST['item_added_message'])) {
 					$devOptions['item_added_message'] = $wpdb->escape($_POST['item_added_message']);
				}
				if (isset($_POST['enter_coupon'])) {
 					$devOptions['enter_coupon'] = $wpdb->escape($_POST['enter_coupon']);
				}
				if (isset($_POST['price_error'])) {
 					$devOptions['price_error'] = $wpdb->escape($_POST['price_error']);
				}
				if (isset($_POST['quantity_error'])) {
 					$devOptions['quantity_error'] = $wpdb->escape($_POST['quantity_error']);
				}
				if (isset($_POST['checkout_error'])) {
 					$devOptions['checkout_error'] = $wpdb->escape($_POST['checkout_error']);
				}
				if (isset($_POST['success_text'])) {
 					$devOptions['success_text'] = $wpdb->escape($_POST['success_text']);
				}
				if (isset($_POST['failed_text'])) {
 					$devOptions['failed_text'] = $wpdb->escape($_POST['failed_text']);
				}
				if (isset($_POST['add_to_cart'])) {
 					$devOptions['add_to_cart'] = $wpdb->escape($_POST['add_to_cart']);
				}
				if (isset($_POST['out_of_stock'])) {
 					$devOptions['out_of_stock'] = $wpdb->escape($_POST['out_of_stock']);
				}
				if (isset($_POST['ga_trackingnum'])) {
 					$devOptions['ga_trackingnum'] = $wpdb->escape($_POST['ga_trackingnum']);
				}
				if (isset($_POST['wpscjQueryUITheme'])) {
 					$devOptions['wpscjQueryUITheme'] = $wpdb->escape($_POST['wpscjQueryUITheme']);
				}

				update_option($this->adminOptionsName, $devOptions);
			   
				echo '<div id="wpscFadeUpdate" class="updated fade"><p><strong>';
				_e("Settings Updated.", "wpStoreCart");
				echo '</strong></p></div>
                        <script type="text/javascript">
			//<![CDATA[
                                jQuery("#wpscFadeUpdate").hide().fadeIn(2000).fadeOut(2000);
                        //]]>
                        </script>
                                ';
			
			}
			
			echo '
                        <script type="text/javascript">
			//<![CDATA[
                        ';


                        if(@!isset($_POST['theCurrentTab']) || @$_POST['theCurrentTab']=='') {
                            $theCurrentTab = '#tab1';
                        } else {
                            $theCurrentTab = $_POST['theCurrentTab'];
                        }
                        if(@isset($_GET['theCurrentTab']) || @$_GET['theCurrentTab']!='') {
                            $theCurrentTab = '#'.$_GET['theCurrentTab'];
                        }
                        echo 'var theCurrentTab = \''.$theCurrentTab.'\';';
                        
                        echo '
			jQuery(document).ready(function($) {
                                //When page loads...
                                $(".tab_content").hide(); //Hide all content
                                ';

                        
                        echo '
                                $("ul.tabs "+theCurrentTab).addClass("active").show(); //Activate first tab
                                $(theCurrentTab).show(); //Show first tab content
                        ';
                        
                        echo '
                                //On Click Event
                                $("ul.tabs li").click(function() {

                                        $("ul.tabs li").removeClass("active"); //Remove any "active" class
                                        $(this).addClass("active"); //Add "active" class to selected tab
                                        $(".tab_content").hide(); //Hide all tab content

                                        var activeTab = $(this).find("a").attr("href"); //Find the href attribute value to identify the active tab + content
                                        $(activeTab).fadeIn(); //Fade in the active ID content
                                        return false;
                                });

                        });
                        //]]>
                        </script>
			<style type="text/css">
				.tableDescription {
					width:200px;
					max-width:200px;
				}

                        .tabs {
                            position:relative;
                            z-index:1;
                        }

                        ul.tabs {
                                margin: 0 0 10px 8px;
                                padding: 0;
                                float: left;
                                list-style: none;
                                height: 74px; /*--Set height of tabs--*/
                                width: 100%;
                                width:770px;
                                min-width:770px;
                            position:relative;
                            z-index:1;
                        }
                        ul.tabs li {
                                float: left;
                                margin: 0;
                                padding: 0;
                                height: 73px; /*--Subtract 1px from the height of the unordered list--*/
                                line-height: 73px; /*--Vertically aligns the text within the tab--*/
                                border:  none;
                                margin-bottom: -1px; /*--Pull the list item down 1px--*/
                                overflow: hidden;
                                position: relative;
                                z-index:1;
                        }
                        ul.tabs li a {
                                text-decoration: none;
                                color: #000;
                                display: block;
                                font-size: 1.2em;
                                position: relative;
                                z-index:1;
                                outline: none;
                        }
                        ul.tabs li a:hover {
                                opacity:.80;
                        }
                        html ul.tabs li.active, html ul.tabs li.active a:hover  { /*--Makes sure that the active tab does not listen to the hover properties--*/
                                
                                
                        }
                        .tab_container {
                                border: none;
                                overflow: hidden;
                                clear: both;
                                float: left; width: 100%;
                                position: relative;
                                z-index:1;
                        }
                        .tab_content {
                                padding: 20px;

                        }
			</style>';

			$this->spHeader();
			
			echo'
                            <h2> </h2>
			<form method="post" action="'. $_SERVER["REQUEST_URI"].'">
                            <input type="hidden" name="theCurrentTab" id="theCurrentTab" value="" />
                        <ul class="tabs">
                            <li><a href="#tab1" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab1\');"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_general.jpg" /></a></li>
                            <li><a href="#tab2" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab2\');"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_email.jpg" /></a></li>
                            <li><a href="#tab3" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab3\');"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_product.jpg" /></a></li>
                            <li><a href="#tab4" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab4\');"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_payment.jpg" /></a></li>
                            <li><a href="#tab5" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab5\');"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_text.jpg" /></a></li>
                        </ul>
                        <div style="clear:both;"></div>
                        <div id="tab1" class="tab_content">
			<h2>wpStoreCart General Options <a href="http://wpstorecart.com/documentation/settings/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>
			';
			
			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>
			';			

			echo '
			<tr><td><h3>wpStoreCart Main Page: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1">wpStoreCart uses pages, and needs a single pre-existing page to act as the main page from which most other wpStoreCart pages descend from.  For example, all product pages will be subpages of this page.</div></h3></td>
			<td class="tableDescription"><p>You need to use a Page as the base for wpStoreCart.  Insert the POST ID of that page here: </p></td>
			<td><select name="wpStoreCartmainpage"> 
			 <option value="">
						';
			  attribute_escape(__('Select page')); 
			  echo '</option>'; 
			  
			  $pages = get_pages(); 
			  foreach ($pages as $pagg) {
				$option = '<option value="'.$pagg->ID.'"';
				if($pagg->ID==$devOptions['mainpage']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $pagg->post_title;
				$option .= '</option>';
				echo $option;
			  }

			echo '
			</select>
			</td></tr>

			<tr><td><h3>Checkout Page: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2">The checkout page can be any page you specify.  This is the page customers will visit to pay for the products they have added to their cart.</div></h3></td>
			<td class="tableDescription"><p>You need to use a Page that customers will use during checkout.  Insert the POST ID of that page here:</p></td>
			<td><select name="checkoutpage"> 
			 <option value="">
						';
			  attribute_escape(__('Select page')); 
			  echo '</option>'; 
			  
			  $pages = get_pages(); 
			  foreach ($pages as $pagg) {
				$option = '<option value="'.$pagg->ID.'"';
				if($pagg->ID==$devOptions['checkoutpage']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $pagg->post_title;
				$option .= '</option>';
				echo $option;
			  }

			echo '
			</select>
			</td></tr>			
			
			<tr><td><h3>Turn wpStoreCart on? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3">If you want to disable wpStoreCart without deactivating the plugin, then set this to No.  This is useful if you want to disable products and purchasing, but not remove the records or uninstall anything.</div></h3></td>
			<td class="tableDescription"><p>Selecting "No" will turn off wpStoreCart, but will not deactivate it.</p></td>
			<td><p><label for="turnwpStoreCartOn_yes"><input type="radio" id="turnwpStoreCartOn_yes" name="turnwpStoreCartOn" value="true" '; if ($devOptions['turnon_wpstorecart'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="turnwpStoreCartOn_no"><input type="radio" id="turnwpStoreCartOn_no" name="turnwpStoreCartOn" value="false" '; if ($devOptions['turnon_wpstorecart'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p></td>
			</td></tr>

			<tr><td><h3>Google Analytics UA: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-4000" /><div class="tooltip-content" id="example-content-4000">Insert your Google Analytics UA code in order to track ecommerce conversions using Google Analytics.  Leave this blank if you\'re not using Google Analytics.  Note, this does not insert tracking code anywhere except when a customer purchases something.</div></h3></td>
			<td class="tableDescription"><p>Insert your Google Analytics UA-XXXXX-XX code here to keep track of sales using Google Analytics.  Leave blank if you don\'t use Google Analytics.</p></td>
			<td><input type="text" name="ga_trackingnum" value="'; _e(apply_filters('format_to_edit',$devOptions['ga_trackingnum']), 'wpStoreCart'); echo'" />
			</td></tr>

			</table>
			<br style="clear:both;" /><br />
                        </div>
                        <div id="tab2" class="tab_content">
			<h2>EMail Options <a href="http://wpstorecart.com/documentation/settings/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>';

			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

			<tr><td><h3>Email Address <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4">wpStoreCart attempts to send emails when a customer purchasing something.  Whatever email address you enter here will be used as the FROM address.  Set this to an email address where you will expect to receive customer replies.</div></h3></td>
			<td class="tableDescription"><p>The email address that you wish to send and recieve all customer emails.</p></td>
			<td><input type="text" name="wpStoreCartEmail" value="'; _e(apply_filters('format_to_edit',$devOptions['wpStoreCartEmail']), 'wpStoreCart'); echo'" />
			</td></tr>	

			<tr><td><h3>Email Sent On Purchase <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-40" /><div class="tooltip-content" id="example-content-40">wpStoreCart attempts to send an email directly after a purchase is made.  This gives the customer feedback that their purchase was successful, and should also inform them that there will be a delay pending the approval of the purchase from a store admin.</div></h3></td>
			<td class="tableDescription"><p>The email to send when a customer purchases something.</p></td>
			<td><textarea name="emailonpurchase" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailonpurchase']), 'wpStoreCart'); echo'</textarea>
			</td></tr>	

			<tr><td><h3>Email Sent On Approval <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-41" /><div class="tooltip-content" id="example-content-41">wpStoreCart attempts to send an email once the order has been approved by an admin.  This lets the customer know that their order is fulfilled, and for digital downloads, it means they now have immediate access to their order.  Physical products are not yet shipped at this stage.</div></h3></td>
			<td class="tableDescription"><p>The email to send when an admin approves an order.</p></td>
			<td><textarea name="emailonapproval" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailonapproval']), 'wpStoreCart'); echo'</textarea>
			</td></tr>	

			<tr><td><h3>Email Sent When Shipped <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-42" /><div class="tooltip-content" id="example-content-42">wpStoreCart attempts to send an email after you\'ve marked an order shipped.  This let\'s customers know the status of their order.  You will need to manually send or update tracking information at this time.</div></h3></td>
			<td class="tableDescription"><p>The email address that you wish to send and recieve all customer emails.</p></td>
			<td><textarea name="emailonshipped" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailonshipped']), 'wpStoreCart'); echo'</textarea>
			</td></tr>				
			
			<tr><td><h3>Email Signature <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-43" /><div class="tooltip-content" id="example-content-43">The bottom of your emails sent will always contain the same footer or signiture.  Fill that out here.</div></h3></td>
			<td class="tableDescription"><p>This is always included at the bottom of each email sent out.</p></td>
			<td><textarea name="emailsig" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailsig']), 'wpStoreCart'); echo'</textarea>
			</td></tr>				
			
			</table>
			<br style="clear:both;" /><br />
                        </div>
                        <div id="tab3" class="tab_content">
			<h2>Display Options <a href="http://wpstorecart.com/documentation/settings/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>
			';
			
			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>';

                        echo '
			<tr><td><h3>jQuery UI Theme <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-43133" /><div class="tooltip-content" id="example-content-43133">You can style your shopping cart, products, and other wpStoreCart related elements here using jQuery UI.</div></h3></td>
			<td class="tableDescription"><p>jQuery UI Theme</p></td>
			<td>
                        <select name="wpscjQueryUITheme">
			 <option value=""></option>';

                        $olddir = getcwd();
                        $dir = WP_PLUGIN_DIR .'/wpstorecart/jqueryui/css/';
                        chdir($dir);
                        $dir = getcwd();

                        $dh  = opendir($dir);
                        $icounter = 0;
                        while (false !== ($filename2 = readdir($dh))) {
                                $files2[] = $filename2;
                                $icounter++;
                        }
                        $rcounter = 0;

                        while ($rcounter != $icounter) {
                            if (filetype($dir .'/'. $files2[$rcounter])== 'dir' && $files2[$rcounter]!='.' && $files2[$rcounter]!='..') {
				$option = '<option value="'.$files2[$rcounter].'"';
				if($files2[$rcounter] == $devOptions['wpscjQueryUITheme']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $files2[$rcounter];
				$option .= '</option>';
				echo $option;
                            }
                            $rcounter++;
                        }

                        chdir($olddir);
                        $option = NULL;
			echo '
			</select>
			</td></tr>

			<tr><td><h3>wpStoreCart Theme <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-431" /><div class="tooltip-content" id="example-content-431">You can style your shopping cart, products, and other wpStoreCart related elements here, but is recommended that you do it in your theme\'s CSS file to keep all CSS in one place.</div></h3></td>
			<td class="tableDescription"><p>Optional CSS styles for wpStoreCart. Choose the CSS file to theme wpStoreCart with.  For a full list of IDs and Classes to use with wpStoreCart, check out <a href="http://wpstorecart.com/documentation/styles-designs/" target="_blank">this webpage.</a></p></td>
			<td>
                        <select name="wpscCss">
			 <option value=""></option>';

                        $olddir = getcwd();
                        $dir = WP_PLUGIN_DIR .'/wpstorecart/themes/';
                        chdir($dir);
                        $dir = getcwd();

                        $dh  = opendir($dir);
                        $icounter = 0;
                        while (false !== ($filename = readdir($dh))) {
                                $files[] = $filename;
                                $icounter++;
                        }
                        $rcounter = 0;

                        while ($rcounter != $icounter) {
                            if (filetype($dir .'/'. $files[$rcounter])!= 'dir' && strtolower(substr($files[$rcounter], -4))=='.css') {
				$option = '<option value="'.$files[$rcounter].'"';
				if($files[$rcounter] == $devOptions['wpscCss']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $files[$rcounter];
				$option .= '</option>';
				echo $option;
                            }
                            $rcounter++;
                        }

                        chdir($olddir);
			echo '
			</select>
			</td></tr>

			<tr><td><h3>Max Thumb Width & Height</h3></td>
			<td class="tableDescription"><p>All this value does is determine the max width and height of product images on product pages.</p></td>
			<td>Width: <input type="text" name="wpStoreCartwidth" style="width: 58px;" value="'; _e(apply_filters('format_to_edit',$devOptions['wpStoreCartwidth']), 'wpStoreCart'); echo'" />  <br />Height: <input type="text" name="wpStoreCartheight" style="width: 58px;" value="'; _e(apply_filters('format_to_edit',$devOptions['wpStoreCartheight']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>(Product Page) Display thumbnail under product? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5">This effects the product short tag (and thus, the default product pages as well.)  If set to yes, the products thumbnail will be displayed underneath the product.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the thumbnail for the product will be displayed underneath the product itself</p></td>
			<td><p><label for="showproductthumbnail"><input type="radio" id="showproductthumbnail_yes" name="showproductthumbnail" value="true" '; if ($devOptions['showproductthumbnail'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="showproductthumbnail_no"><input type="radio" id="showproductthumbnail_no" name="showproductthumbnail" value="false" '; if ($devOptions['showproductthumbnail'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>		
			</td></tr>

			<tr><td><h3>(Product Page) Display description under product? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6">This also effects the product short tag (including the default product pages.)  If set to yes, the products description will be written underneath the product thumbnail (if its enabled.)</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the description for the product is written underneath the product, after the thumbnail.</p></td>
			<td><p><label for="showproductdescription"><input type="radio" id="showproductdescription_yes" name="showproductdescription" value="true" '; if ($devOptions['showproductdescription'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="showproductdescription_no"><input type="radio" id="showproductdescription_no" name="showproductdescription" value="false" '; if ($devOptions['showproductdescription'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>


			<tr><td><h3>(Main Page) Content of the Main Page <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-6999" /><div class="tooltip-content" id="example-content-6999">The main page of your store can either list products, or the categories of the site.  It can also display the products either by newest first, or most popular first.</div></h3></td>
			<td class="tableDescription"><p>Changing this will effect what is displayed on the main page of your store.</p></td>
			<td>
                        <select name="frontpageDisplays">
';

                        $theOptions[0] = 'List all products';
                        $theOptions[1] = 'List all categories';
                        $theOptions[2] = 'List newest products';
                        $theOptions[3] = 'List most popular products';
                        foreach ($theOptions as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['frontpageDisplays']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOption;
				$option .= '</option>';
				echo $option;
                        }

   			echo '
			</select>
			</td></tr>

			<tr><td><h3>(Main Page) Display thumbnails on Main Page? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-5554" /><div class="tooltip-content" id="example-content-5554">This effects the main wpStoreCart short tag (and thus, the default Main Page and Category pages as well.)  If set to yes, the product or category thumbnails will be displayed on the Main Page/Category page.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the thumbnail for the products or categories will be displayed on the Main Page and Category pages.</p></td>
			<td><p><label for="displayThumb"><input type="radio" id="displayThumb_yes" name="displayThumb" value="true" '; if ($devOptions['displayThumb'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displayThumb_no"><input type="radio" id="displayThumb_no" name="displayThumb" value="false" '; if ($devOptions['displayThumb'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>(Main Page) Display titles on Main Page? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-55544" /><div class="tooltip-content" id="example-content-55544">This effects the main wpStoreCart short tag (and thus, the default Main Page and Category pages as well.)  If set to yes, the product or category title will be displayed on the Main Page/Category page.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the title of the products or categories will be displayed on the Main Page and Category pages.</p></td>
			<td><p><label for="displayTitle"><input type="radio" id="displayTitle_yes" name="displayTitle" value="true" '; if ($devOptions['displayTitle'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displayTitle_no"><input type="radio" id="displayTitle_no" name="displayTitle" value="false" '; if ($devOptions['displayTitle'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>(Main Page) Display small descriptions on Main Page? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-55545" /><div class="tooltip-content" id="example-content-55545">This effects the main wpStoreCart short tag (and thus, the default Main Page and Categpoy pages as well.)  If set to yes, the product or category introductory description will be displayed on the Main Page/Category page.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the introductory description of the products or categories will be displayed on the Main Page and Category pages.</p></td>
			<td><p><label for="displayintroDesc"><input type="radio" id="displayintroDesc_yes" name="displayintroDesc" value="true" '; if ($devOptions['displayintroDesc'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displayintroDesc_no"><input type="radio" id="displayintroDesc_no" name="displayintroDesc" value="false" '; if ($devOptions['displayintroDesc'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>(Main Page) Display Type <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-7999" /><div class="tooltip-content" id="example-content-7999">This effects the main wpStoreCart short tag (and thus, the default Main Page and Categpoy pages as well.)  If set to grid, the product or category will be displayed within a grid format, or if it\'s set to list, they will be presented in a top down, one at a time list view on the Main Page/Category page.</div></h3></td>
			<td class="tableDescription"><p>If set to grid, will display products or categories in a grid format, if set to list, will display them in an ordered list.</p></td>
			<td>
                        <select name="displayType">
';

                        $theOptionz[0] = 'grid';
                        $theOptionz[1] = 'list';
                        foreach ($theOptionz as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['displayType']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOption;
				$option .= '</option>';
				echo $option;
                        }

   			echo '
			</select>
			</td></tr>
			</table>
			<br style="clear:both;" /><br />
                        </div>
                        <div id="tab4" class="tab_content">
			<h2>Payment Options <a href="http://wpstorecart.com/documentation/settings/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>';


                        if(file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/updater.pro.php') ) {
                            include_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/payments.pro.php');
                            include_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/updater.pro.php');
                        }

                        echo '
                        <h3>PayPal Payment Gateway</h3>
                        <table class="widefat">
                                                <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>
			<tr><td><h3>Accept PayPal Payments? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7">Want to accept PayPal payments?  Then set this to yes!</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using PayPal.</p></td>
			<td><p><label for="allowpaypal"><input type="radio" id="allowpaypal_yes" name="allowpaypal" value="true" '; if ($devOptions['allowpaypal'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowpaypal_no"><input type="radio" id="allowpaypal_no" name="allowpaypal" value="false" '; if ($devOptions['allowpaypal'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>		
			</td></tr>

			<tr><td><h3>Turn on PayPal Test Mode? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-8" /><div class="tooltip-content" id="example-content-8">If you need to do tests with the PayPal Sandbox then set this to yes.  Warning!  These payments are not real, so any purchases made under the Sandbox will result in live sales with no money!  You can delete test orders in the Orders tab above.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, all transactions are done using the PayPal sandbox.</p></td>
			<td><p><label for="paypaltestmode"><input type="radio" id="paypaltestmode_yes" name="paypaltestmode" value="true" '; if ($devOptions['paypaltestmode'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="paypaltestmode_no"><input type="radio" id="paypaltestmode_no" name="paypaltestmode" value="false" '; if ($devOptions['paypaltestmode'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>		
			</td></tr>			
			
			<tr><td><h3>PayPal Email Address <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-9" /><div class="tooltip-content" id="example-content-9">The PayPal email address you wish to recieve payments to.  Make sure you have already registered this email address with PayPal.</div></h3></td>
			<td class="tableDescription"><p>The email address you wish to receive PayPal payments.</p></td>
			<td><input type="text" name="paypalemail" value="'; _e(apply_filters('format_to_edit',$devOptions['paypalemail']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Currency <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-941" /><div class="tooltip-content" id="example-content-941">Change this to whatever currency your shop is in.  Note that this is currently only supported in PayPal payments.</div></h3></td>
			<td class="tableDescription"><p>The type of currency that your store uses.</p></td>
			<td>
                        <select name="currency_code">
';

                        $theOptionsz[0] = 'USD';$theOptionszName[0] = 'U.S. Dollars ($)';
                        $theOptionsz[1] = 'AUD';$theOptionszName[1] = 'Australian Dollars (A $)';
                        $theOptionsz[2] = 'CAD';$theOptionszName[2] = 'Canadian Dollars (C $)';
                        $theOptionsz[3] = 'EUR';$theOptionszName[3] = 'Euros (�)';
                        $theOptionsz[4] = 'GBP';$theOptionszName[4] = 'Pounds Sterling (�)';
                        $theOptionsz[5] = 'JPY';$theOptionszName[5] = 'Yen (�)';
                        $theOptionsz[6] = 'NZD';$theOptionszName[6] = 'New Zealand Dollar ($)';
                        $theOptionsz[7] = 'CHF';$theOptionszName[7] = 'Swiss Franc';
                        $theOptionsz[8] = 'HKD';$theOptionszName[8] = 'Hong Kong Dollar ($)';
                        $theOptionsz[9] = 'SGD';$theOptionszName[9] = 'Singapore Dollar ($)';
                        $theOptionsz[10] = 'SEK';$theOptionszName[10] = 'Swedish Krona';
                        $theOptionsz[11] = 'DKK';$theOptionszName[11] = 'Danish Krone';
                        $theOptionsz[12] = 'PLN';$theOptionszName[12] = 'Polish Zloty';
                        $theOptionsz[13] = 'NOK';$theOptionszName[13] = 'Norwegian Krone';
                        $theOptionsz[14] = 'HUF';$theOptionszName[14] = 'Hungarian Forint';
                        $theOptionsz[15] = 'CZK';$theOptionszName[15] = 'Czech Koruna';
                        $theOptionsz[16] = 'ILS';$theOptionszName[16] = 'Israeli Shekel';
                        $theOptionsz[17] = 'MXN';$theOptionszName[17] = 'Mexican Peso';
                        $theOptionsz[18] = 'BRL';$theOptionszName[18] = 'Brazilian Real (only for Brazilian users)';
                        $theOptionsz[19] = 'MYR';$theOptionszName[19] = 'Malaysian Ringgits (only for Malaysian users)';
                        $theOptionsz[20] = 'PHP';$theOptionszName[20] = 'Philippine Pesos';
                        $theOptionsz[21] = 'TWD';$theOptionszName[21] = 'Taiwan New Dollars';
                        $theOptionsz[22] = 'THB';$theOptionszName[22] = 'Thai Baht';
                        $icounter = 0;
                        foreach ($theOptionsz as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['currency_code']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionszName[$icounter];
				$option .= '</option>';
				echo $option;
                                $icounter++;
                        }

   			echo '
			</select>
			</td></tr>

			<tr><td><h3>Currency Symbol</h3></td>
			<td class="tableDescription"><p>Left Symbol Default: <i>$</i></p><p>Right Symbol Default: </p></td>
			<td>Left symbol: <input type="text" name="currency_symbol" value="'; _e(apply_filters('format_to_edit',$devOptions['currency_symbol']), 'wpStoreCart'); echo'" />
                        <br />Right symbol: <input type="text" name="currency_symbol_right" value="'; _e(apply_filters('format_to_edit',$devOptions['currency_symbol_right']), 'wpStoreCart'); echo'" />
			</td></tr>

                        </table>
                        <br style="clear:both;" /><br />
                        ';

                        echo '
                        <h3>Check/Money Order/COD Payments</h3>
                        <table class="widefat">
                                                <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>
			<tr><td><h3>Accept Payments via Mail? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-78777" /><div class="tooltip-content" id="example-content-78777">Want to accept payments through the mail from check, money orders, or cash on delivery (COD)?  You can even use this to record your cash transactions in your brick and mortar store if you wish.  Remember, don\'t send anything until the payment clears!</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, customers can purchase using Check, Money Order or COD</p></td>
			<td><p><label for="allowcheckmoneyorder"><input type="radio" id="allowcheckmoneyorder_yes" name="allowcheckmoneyorder" value="true" '; if ($devOptions['allowcheckmoneyorder'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowcheckmoneyorder_no"><input type="radio" id="allowcheckmoneyorder_no" name="allowcheckmoneyorder" value="false" '; if ($devOptions['allowcheckmoneyorder'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Text to Display <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-415555" /><div class="tooltip-content" id="example-content-415555">You should place instructions here as to what address the customer should send their check or money orders to.  Be complete and accurate, and be sure to tell them how long they should wait and who they can contact about their order.</div></h3></td>
			<td class="tableDescription"><p>The text/html that is displayed to customers who choose to pay via check or money order.</p></td>
			<td><textarea name="checkmoneyordertext" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['checkmoneyordertext']), 'wpStoreCart'); echo'</textarea>
			</td></tr>

                        </table>
                        <br style="clear:both;" /><br />
                        ';

                        if(file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/payments.pro.php')) {
                            echo '
                            <h3>Authorize.NET Gateway (wpStoreCart PRO)</h3>
                            <table class="widefat">
                            <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

                            <tr><td><h3>Accept Authorize.NET Payments? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-70099" /><div class="tooltip-content" id="example-content-70099">Want to accept Authorize.NET payments?  Then set this to yes!</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using Authorize.NET.</p></td>
                            <td><p><label for="allowauthorizenet"><input type="radio" id="allowauthorizenet_yes" name="allowauthorizenet" value="true" '; if ($devOptions['allowauthorizenet'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowauthorizenet_no"><input type="radio" id="allowauthorizenet_no" name="allowauthorizenet" value="false" '; if ($devOptions['allowauthorizenet'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr><td><h3>Turn on Authorize.NET Test Mode? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-81111" /><div class="tooltip-content" id="example-content-81111">If you need to do tests with Authorize.NET then set this to yes.  Warning!  These payments are not real, so any purchases made under the Sandbox will result in live sales with no money!  You can delete test orders in the Orders tab above.</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, all transactions are tests done using Authorize.NET.</p></td>
                            <td><p><label for="authorizenettestmode"><input type="radio" id="authorizenettestmode_yes" name="authorizenettestmode" value="true" '; if ($devOptions['authorizenettestmode'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="authorizenettestmode_no"><input type="radio" id="authorizenettestmode_no" name="authorizenettestmode" value="false" '; if ($devOptions['authorizenettestmode'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr><td><h3>API Login ID <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-9666" /><div class="tooltip-content" id="example-content-9666">The Authorize.NET API Login ID assigned to you.  </div></h3></td>
                            <td class="tableDescription"><p>The API Login ID you are assigned to use access your Authorize.NET account.</p></td>
                            <td><input type="text" name="authorizenetemail" value="'; _e(apply_filters('format_to_edit',$devOptions['authorizenetemail']), 'wpStoreCart'); echo'" />
                            </td></tr>

                            <tr><td><h3>Secret Key <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-9667" /><div class="tooltip-content" id="example-content-9667">The Authorize.NET secret key which is used to authenticate your shop.</div></h3></td>
                            <td class="tableDescription"><p>The Authorize.NET secret key md5 hash value.</p></td>
                            <td><input type="text" name="authorizenetsecretkey" value="'; _e(apply_filters('format_to_edit',$devOptions['authorizenetsecretkey']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            </table>
                            <br style="clear:both;" /><br />


                            <h3>2CheckOut Gateway (wpStoreCart PRO)</h3>
                            <table class="widefat">
                            <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

                            <tr><td><h3>Accept 2CheckOut Payments? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-700992" /><div class="tooltip-content" id="example-content-700992">Want to accept 2CheckOut payments?  Then set this to yes!</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using 2CheckOut.</p></td>
                            <td><p><label for="allow2checkout"><input type="radio" id="allow2checkout_yes" name="allow2checkout" value="true" '; if ($devOptions['allow2checkout'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow2checkout_no"><input type="radio" id="allow2checkout_no" name="allow2checkout" value="false" '; if ($devOptions['allow2checkout'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr><td><h3>Turn on 2CheckOut Test Mode? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-8111166" /><div class="tooltip-content" id="example-content-8111166">If you need to do tests with 2CheckOut then set this to yes.  Warning!  These payments are not real, so any purchases made under the Sandbox will result in live sales with no money!  You can delete test orders in the Orders tab above.</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, all transactions are tests done using 2CheckOut.</p></td>
                            <td><p><label for="2checkouttestmode"><input type="radio" id="2checkouttestmode_yes" name="2checkouttestmode" value="true" '; if ($devOptions['2checkouttestmode'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="2checkouttestmode_no"><input type="radio" id="2checkouttestmode_no" name="2checkouttestmode" value="false" '; if ($devOptions['2checkouttestmode'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr><td><h3>2CheckOut Vendor ID <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-966644" /><div class="tooltip-content" id="example-content-966644">The 2CheckOut Vendor ID assigned to you.  </div></h3></td>
                            <td class="tableDescription"><p>The 2CheckOut Vendor ID you are assigned to use access your 2CheckOut account.</p></td>
                            <td><input type="text" name="2checkoutemail" value="'; _e(apply_filters('format_to_edit',$devOptions['2checkoutemail']), 'wpStoreCart'); echo'" />
                            </td></tr>

                            </table>
                            <br style="clear:both;" /><br />


                            ';
                        }

                        echo '
        		
			
			
                        </div>
                        <div id="tab5" class="tab_content">
                        <h2>Text &amp; Language Options <a href="http://wpstorecart.com/documentation/settings/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>';


			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

			<tr><td><h3>Successful Payment Text <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-400" /><div class="tooltip-content" id="example-content-400">After the customer is redirected to the payment gateway, such as PayPal, this is the text they will see after successfully completing the payment.</div></h3></td>
			<td class="tableDescription"><p>The text and HTML that is displayed when a customers returns from the payment gateway after successfully paying.</p></td>
			<td><textarea name="success_text" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['success_text']), 'wpStoreCart'); echo'</textarea>
			</td></tr>

			<tr><td><h3>Failed Payment Text<img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-401" /><div class="tooltip-content" id="example-content-401">After the customer is redirected to the payment gateway, such as PayPal, this is the text they will see after failing to complete the payment.</div></h3></td>
			<td class="tableDescription"><p>The text and HTML that is displayed when a customers returns from the payment gateway after failing the payment process.</p></td>
			<td><textarea name="failed_text" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['failed_text']), 'wpStoreCart'); echo'</textarea>
			</td></tr>

			<tr><td><h3>Cart Title</h3></td>
			<td class="tableDescription"><p>Default: <i>Shopping Cart</i></p></td>
			<td><input type="text" name="cart_title" value="'; _e(apply_filters('format_to_edit',$devOptions['cart_title']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Single Item</h3></td>
			<td class="tableDescription"><p>Default: <i>Item</i></p></td>
			<td><input type="text" name="single_item" value="'; _e(apply_filters('format_to_edit',$devOptions['single_item']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Multiple Items</h3></td>
			<td class="tableDescription"><p>Default: <i>Items</i></p></td>
			<td><input type="text" name="multiple_items" value="'; _e(apply_filters('format_to_edit',$devOptions['multiple_items']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Subtotal</h3></td>
			<td class="tableDescription"><p>Default: <i>Subtotal</i></p></td>
			<td><input type="text" name="subtotal" value="'; _e(apply_filters('format_to_edit',$devOptions['subtotal']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Update Button</h3></td>
			<td class="tableDescription"><p>Default: <i>update</i></p></td>
			<td><input type="text" name="update_button" value="'; _e(apply_filters('format_to_edit',$devOptions['update_button']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Checkout Button</h3></td>
			<td class="tableDescription"><p>Default: <i>checkout</i></p></td>
			<td><input type="text" name="checkout_button" value="'; _e(apply_filters('format_to_edit',$devOptions['checkout_button']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Checkout Check/Money Order Button</h3></td>
			<td class="tableDescription"><p>Default: <i>Checkout with Check/Money Order</i></p></td>
			<td><input type="text" name="checkout_checkmoneyorder_button" value="'; _e(apply_filters('format_to_edit',$devOptions['checkout_checkmoneyorder_button']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Checkout PayPal Button</h3></td>
			<td class="tableDescription"><p>Default: <i>Checkout with PayPal</i></p></td>
			<td><input type="text" name="checkout_paypal_button" value="'; _e(apply_filters('format_to_edit',$devOptions['checkout_paypal_button']), 'wpStoreCart'); echo'" />
			</td></tr>
                        ';

                        if(file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/payments.pro.php')) {
                            echo '
                            <tr><td><h3>Checkout Authorize.NET Button</h3></td>
                            <td class="tableDescription"><p>Default: <i>Checkout with Authorize.NET</i></p></td>
                            <td><input type="text" name="checkout_authorizenet_button" value="'; _e(apply_filters('format_to_edit',$devOptions['checkout_authorizenet_button']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>Checkout 2checkout Button</h3></td>
                            <td class="tableDescription"><p>Default: <i>Checkout with 2Checkout</i></p></td>
                            <td><input type="text" name="checkout_2checkout_button" value="'; _e(apply_filters('format_to_edit',$devOptions['checkout_2checkout_button']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                        }

                        echo '
			<tr><td><h3>Remove Link</h3></td>
			<td class="tableDescription"><p>Default: <i>remove</i></p></td>
			<td><input type="text" name="remove_link" value="'; _e(apply_filters('format_to_edit',$devOptions['remove_link']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Empty Button</h3></td>
			<td class="tableDescription"><p>Default: <i>empty</i></p></td>
			<td><input type="text" name="empty_button" value="'; _e(apply_filters('format_to_edit',$devOptions['empty_button']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Empty Message</h3></td>
			<td class="tableDescription"><p>Default: <i>Your cart is empty!</i></p></td>
			<td><input type="text" name="empty_message" value="'; _e(apply_filters('format_to_edit',$devOptions['empty_message']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Item Added Message</h3></td>
			<td class="tableDescription"><p>Default: <i>Item added!</i></p></td>
			<td><input type="text" name="item_added_message" value="'; _e(apply_filters('format_to_edit',$devOptions['item_added_message']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Enter Coupon</h3></td>
			<td class="tableDescription"><p>Default: <i>Enter coupon:</i></p></td>
			<td><input type="text" name="enter_coupon" value="'; _e(apply_filters('format_to_edit',$devOptions['enter_coupon']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Price Error</h3></td>
			<td class="tableDescription"><p>Default: <i>Invalid price format!</i></p></td>
			<td><input type="text" name="price_error" value="'; _e(apply_filters('format_to_edit',$devOptions['price_error']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Quantity Error</h3></td>
			<td class="tableDescription"><p>Default: <i>Item quantities must be whole numbers!</i></p></td>
			<td><input type="text" name="quantity_error" value="'; _e(apply_filters('format_to_edit',$devOptions['quantity_error']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Checkout Error</h3></td>
			<td class="tableDescription"><p>Default: <i>Your order could not be processed!</i></p></td>
			<td><input type="text" name="checkout_error" value="'; _e(apply_filters('format_to_edit',$devOptions['checkout_error']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Add to Cart</h3></td>
			<td class="tableDescription"><p>Default: <i>Add to Cart</i></p></td>
			<td><input type="text" name="add_to_cart" value="'; _e(apply_filters('format_to_edit',$devOptions['add_to_cart']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Out of Stock</h3></td>
			<td class="tableDescription"><p>Default: <i>Out of Stock</i></p></td>
			<td><input type="text" name="out_of_stock" value="'; _e(apply_filters('format_to_edit',$devOptions['out_of_stock']), 'wpStoreCart'); echo'" />
			</td></tr>

			</table>
			<br style="clear:both;" />
                        </div><br />';

                        echo '
			<div class="submit">
			<input type="submit" name="update_wpStoreCartSettings" value="'; _e('Update Settings', 'wpStoreCart'); echo'" /></div>
			</form>

			 </div>';		
		
		}
		//END Prints out the admin page ================================================================================		
		
		
		//Prints out the Import/Export admin page =======================================================================
        function printAdminPageImport() {
			global $wpdb, $user_level,$wpstorecart_version_int,$testing_mode;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}

			$devOptions = $this->getAdminOptions();
			$table_name = $wpdb->prefix . "wpstorecart_products";
                        $this->spHeader();
                        echo '<h2>Import/Export</h2>';
                        if($testing_mode==true && $wpstorecart_version_int < 202000) {
                            echo '<div id="wpsc-warning" class="updated fade"><p><strong>This feature is intended for wpStoreCart 2.2 and above.  You can only access it right now because you are using Testing Mode.  Currently, this feature may be incomplete or non functional, and using this feature may destroy your data, your website, even your life!</strong></p></div>';
                        }

			

			if (session_id() == "") {@session_start();};

                        echo '
                            <style type="text/css">
                                #upload-progressbar-container4 {
                                    min-width:200px;
                                    max-width:200px;
                                    min-height:20px;
                                    max-height:20px;
                                    background-color:#FFF;
                                    display:block;
                                }
                                #upload-progressbar4 {
                                    min-height:20px;
                                    max-height:20px;
                                    background-color:#6ba6ff;
                                    width:0px;
                                    display:none;
                                    border:1px solid #1156be;
                                }
                            </style>
                            <script type="text/javascript">



                                    var productUploadStartEventHandler = function (file) {
                                            var continue_with_upload;

                                            continue_with_upload = true;

                                            return continue_with_upload;
                                    };

                                    var productUploadSuccessEventHandler = function (file, server_data, receivedResponse) {
                                            document.theimportform.importthisfile.value = file.name;

                                    };


                                    function uploadError(file, errorCode, message) {
                                            try {

                                                    switch (errorCode) {
                                                    case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
                                                            alert("Error Code: HTTP Error, File name. Message: " + message);
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
                                                            alert("Error Code: No backend file. Message: " + message);
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
                                                            alert("Error Code: Upload Failed. Message: " + message);
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.IO_ERROR:
                                                            alert("Error Code: IO Error. Message: " + message);
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
                                                            alert("Error Code: Security Error. Message: " + message);
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
                                                            alert("Error Code: Upload Limit Exceeded. Message: " + message);
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
                                                            alert("Error Code: The file was not found. Message: " + message);
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
                                                            alert("Error Code: File Validation Failed. Message: " + message);
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
                                                            break;
                                                    case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
                                                            break;
                                                    default:
                                                            alert("Error Code: " + errorCode + ". Message: " + message);
                                                            break;
                                                    }
                                            } catch (ex) {
                                                    this.debug(ex);
                                            }
                                    }

                                    function uploadProgress(file, bytesLoaded, bytesTotal) {
                                        try {
                                            var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
                                            jQuery("#upload-progressbar4").css("display", "block");
                                            jQuery("#upload-progressbar4").css("width", percent+"%");
                                            jQuery("#upload-progressbar4").html("<center>"+ percent+"%</center>");
                                        } catch (e) {
                                        }
                                    }

                                    function beginTheUpload(selected, addtoqueue, inqueuealready) {
                                            this.startUpload();
                                    }

                                    function debugSWFUpload (message) {
                                            try {
                                                    if (window.console && typeof(window.console.error) === "function" && typeof(window.console.log) === "function") {
                                                            if (typeof(message) === "object" && typeof(message.name) === "string" && typeof(message.message) === "string") {
                                                                    window.console.error(message);
                                                            } else {
                                                                    window.console.log(message);
                                                            }
                                                    }
                                            } catch (ex) {
                                            }
                                            try {
                                                    if (this.settings.debug) {
                                                            this.debugMessage(message);
                                                    }
                                            } catch (ex1) {
                                            }
                                    }

                                    var swfu;
                                    window.onload = function () {
                                            var settings_object = {
                                                    upload_url : "'.WP_PLUGIN_URL.'/wpstorecart/php/upload.php",
                                                    post_params: {"PHPSESSID" : "'.session_id().'"},
                                                    flash_url : "'.get_option( 'siteurl' ).'/wp-includes/js/swfupload/swfupload.swf",
                                                    file_size_limit : "2048 MB",
                                                    file_types : "*.*",
                                                    file_types_description : "Any file type",
                                                    file_upload_limit : "1",
                                                    file_post_name: "Filedata",
                                                    button_placeholder_id : "spanSWFUploadButton4",
                                                    button_image_url : "'.WP_PLUGIN_URL.'/wpstorecart/images/XPButtonUploadText_61x22.png",
                                                    button_width: 61,
                                                    button_height: 22,
                                                    debug : false,
                                                    debug_handler : debugSWFUpload,
                                                    file_dialog_complete_handler: beginTheUpload,
                                                    upload_progress_handler: uploadProgress,
                                                    upload_start_handler : productUploadStartEventHandler,
                                                    upload_success_handler : productUploadSuccessEventHandler,
                                                    upload_error_handler : uploadError
                                            };

                                            swfu = new SWFUpload(settings_object); 
                                     };

                                jQuery(document).ready(function() {
                                    jQuery("#importformatx").toggle();

                                });

                                    function theOperation(theOption) {
                                        if(theOption=="import") {
                                            jQuery("#importformatx").toggle();
                                            jQuery("#exportformatx").toggle();
                                        }
                                        if(theOption=="export") {
                                            jQuery("#importformatx").toggle();
                                            jQuery("#exportformatx").toggle();
                                        }
                                        return true;
                                    }

                            </script>
                            <br />';

                        if(@$_POST['isreal']=='true') {
                            echo '<h3>Attempting to '.$_POST['typeofoperation'] .' using '; if($_POST['typeofoperation']=='export') {echo $_POST['exportformat'];} else {echo $_POST['importformat'];} echo ' file...</h3>';

                            // Export routines here:
                            if($_POST['typeofoperation']=='export') {
                                if($_POST['exportformat']=='csv') {
                                    echo '
                                        <script type="text/javascript">
                                        <!--
                                        window.open("'.WP_PLUGIN_URL.'/wpstorecart/php/exportcsv.php");
                                        //-->
                                        </script>
                                        ';
                                }

                                if($_POST['exportformat']=='sql') {
                                    echo '
                                        <script type="text/javascript">
                                        <!--
                                        window.open("'.WP_PLUGIN_URL.'/wpstorecart/php/exportsql.php");
                                        //-->
                                        </script>
                                        ';
                                }

                            }

                            // Import routines here:
                            if($_POST['typeofoperation']=='import') {

                            }


                        }

                        echo '
                            <form action="" name="theimportform" method="post">
                                <div>Type of operation: <select name="typeofoperation"  onchange="theOperation(this.value);">
                                  <option value="export">Export</option>
                                  <option value="import">Import</option>
                                </select>
                                <div id="exportformatx">File format for export <select name="exportformat">
                                  <option value="sql">SQL file</option>
                                  <option value="csv">CSV file</option>
                                </select>                                
                                </div><br />
                                
                                <div id="importformatx">File format for input <select name="importformat">
                                  <option value="sql">SQL file</option>
                                  <option value="csv">CSV file</option>
                                </select>
                                <input type="text" id="importthisfile" name="importthisfile" style="width: 200px;" value="" />
                                Upload a file: <span id="spanSWFUploadButton4"></span>
                                <div id="upload-progressbar-container4">
                                    <div id="upload-progressbar4">
                                    </div>
                                </div>
                                </div><br />
                                <input type="hidden" name="isreal" value="true" />
                                <input type="submit" value="Begin >" />
                             </form>
                             ';

        }
		//END Prints out the admin page ================================================================================
		
		
		//Prints out the Add products admin page =======================================================================
        function printAdminPageAddproducts() {
			global $wpdb, $user_level;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			$devOptions = $this->getAdminOptions();
			$table_name = $wpdb->prefix . "wpstorecart_products";
			

			
			// For new products
			if(!isset($_GET['keytoedit'])) {
				// Default form values
				$wpStoreCartproduct_name = '';
				$wpStoreCartproduct_introdescription = '';
				$wpStoreCartproduct_description = '';
				$wpStoreCartproduct_thumbnail = '';
				$wpStoreCartproduct_price = 0.00;
				$wpStoreCartproduct_shipping = 0.00;
				$wpStoreCartproduct_download = '';
				$wpStoreCartproduct_tags = '';
				$wpStoreCartproduct_category = 0;
				$wpStoreCartproduct_inventory = 0;
                                $wpStoreCartproduct_useinventory = 1;
				$keytoedit=0;
				$_GET['keytoedit'] = 0;
                                $wpStoreCartproduct_donation = 'false';
			} 
			
			
			// To edit a previous product
			$isanedit = false;
			if ($_GET['keytoedit']!=0 && is_numeric($_GET['keytoedit'])) {
				$isanedit = true;
				
				if (isset($_POST['wpStoreCartproduct_name']) && isset($_POST['wpStoreCartproduct_introdescription']) && isset($_POST['wpStoreCartproduct_description']) && isset($_POST['wpStoreCartproduct_thumbnail']) && isset($_POST['wpStoreCartproduct_price']) && isset($_POST['wpStoreCartproduct_shipping']) && isset($_POST['wpStoreCartproduct_download']) && isset($_POST['wpStoreCartproduct_tags']) && isset($_POST['wpStoreCartproduct_category']) && isset($_POST['wpStoreCartproduct_inventory'])) {
					$wpStoreCartproduct_name = $wpdb->prepare($_POST['wpStoreCartproduct_name']);
					$wpStoreCartproduct_introdescription = $wpdb->prepare($_POST['wpStoreCartproduct_introdescription']);
					$wpStoreCartproduct_description = $wpdb->prepare($_POST['wpStoreCartproduct_description']);
					$wpStoreCartproduct_thumbnail = $wpdb->escape($_POST['wpStoreCartproduct_thumbnail']);
					$wpStoreCartproduct_price = $wpdb->escape($_POST['wpStoreCartproduct_price']);
					$wpStoreCartproduct_shipping = $wpdb->escape($_POST['wpStoreCartproduct_shipping']);
					$wpStoreCartproduct_download = $wpdb->escape($_POST['wpStoreCartproduct_download']);	
					$timestamp = date('Ymd');
					$wpStoreCartproduct_tags = $wpdb->escape($_POST['wpStoreCartproduct_tags']);
					$wpStoreCartproduct_category = $wpdb->escape($_POST['wpStoreCartproduct_category']);
					$wpStoreCartproduct_inventory = $wpdb->escape($_POST['wpStoreCartproduct_inventory']);
                                        $wpStoreCartproduct_useinventory = $wpdb->escape($_POST['wpStoreCartproduct_useinventory']);
                                        $wpStoreCartproduct_donation = $wpdb->escape($_POST['wpStoreCartproduct_donation']);
					$cleanKey = $wpdb->escape($_GET['keytoedit']);
		

					$updateSQL = "
					UPDATE `{$table_name}` SET 
					`name` = '{$wpStoreCartproduct_name}', 
					`introdescription` = '{$wpStoreCartproduct_introdescription}', 
					`description` = '{$wpStoreCartproduct_description}', 
					`thumbnail` = '{$wpStoreCartproduct_thumbnail}', 
					`price` = '{$wpStoreCartproduct_price}', 
					`shipping` = '{$wpStoreCartproduct_shipping}', 
					`download` = '{$wpStoreCartproduct_download}', 
					`tags` = '{$wpStoreCartproduct_tags}', 
					`category` = '{$wpStoreCartproduct_category}', 
					`inventory` = '{$wpStoreCartproduct_inventory}',
                                        `useinventory` = '{$wpStoreCartproduct_useinventory}',
                                        `donation` =  '{$wpStoreCartproduct_donation}'
					WHERE `primkey` ={$cleanKey} LIMIT 1 ;				
					";

					$results = $wpdb->query($updateSQL);
					
					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '<div class="updated"><p><strong>';
						_e("Edit successful!  Your product details have been saved.", "wpStoreCart");
						echo '</strong></p></div>';	
					} 
					
				}
				
				
				
				$keytoedit=$_GET['keytoedit'];	
				$grabrecord = "SELECT * FROM {$table_name} WHERE `primkey`={$keytoedit};";					
				
				$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
				if(isset($results)) {
					foreach ($results as $result) {
						
						$wpStoreCartproduct_name = stripslashes($result['name']);
						$wpStoreCartproduct_introdescription = stripslashes($result['introdescription']);
						$wpStoreCartproduct_description = stripslashes($result['description']);
						$wpStoreCartproduct_thumbnail = stripslashes($result['thumbnail']);
						$wpStoreCartproduct_price = stripslashes($result['price']);
						$wpStoreCartproduct_shipping = stripslashes($result['shipping']);
						$wpStoreCartproduct_download = stripslashes($result['download']);
						$wpStoreCartproduct_tags = stripslashes($result['tags']);
						$wpStoreCartproduct_category = stripslashes($result['category']);
						$wpStoreCartproduct_inventory = stripslashes($result['inventory']);
                                                $wpStoreCartproduct_useinventory = stripslashes($result['useinventory']);
                                                $wpStoreCartproduct_donation =  stripslashes($result['donation']);
					}
				} else {
					echo '<div class="updated"><p><strong>';
					echo "There was a problem loading the product you wish to edit.  The query was: {$grabrecord} ";
					echo '</strong></p></div>';					
				}
			}
			
			if (isset($_POST['addNewwpStoreCart_product']) && $isanedit == false) {
			
				if (isset($_POST['wpStoreCartproduct_name']) && isset($_POST['wpStoreCartproduct_introdescription']) && isset($_POST['wpStoreCartproduct_description']) && isset($_POST['wpStoreCartproduct_thumbnail']) && isset($_POST['wpStoreCartproduct_price']) && isset($_POST['wpStoreCartproduct_shipping']) && isset($_POST['wpStoreCartproduct_download']) && isset($_POST['wpStoreCartproduct_tags']) && isset($_POST['wpStoreCartproduct_category']) && isset($_POST['wpStoreCartproduct_inventory'])) {
					$wpStoreCartproduct_name = $wpdb->prepare($_POST['wpStoreCartproduct_name']);
					$wpStoreCartproduct_introdescription = $wpdb->prepare($_POST['wpStoreCartproduct_introdescription']);
					$wpStoreCartproduct_description = $wpdb->prepare($_POST['wpStoreCartproduct_description']);
					$wpStoreCartproduct_thumbnail = $wpdb->escape($_POST['wpStoreCartproduct_thumbnail']);
					$wpStoreCartproduct_price = $wpdb->escape($_POST['wpStoreCartproduct_price']);
					$wpStoreCartproduct_shipping = $wpdb->escape($_POST['wpStoreCartproduct_shipping']);
					$wpStoreCartproduct_download = $wpdb->escape($_POST['wpStoreCartproduct_download']);	
					$timestamp = date('Ymd');
					$wpStoreCartproduct_tags = $wpdb->escape($_POST['wpStoreCartproduct_tags']);
					$wpStoreCartproduct_category = $wpdb->escape($_POST['wpStoreCartproduct_category']);
					$wpStoreCartproduct_inventory = $wpdb->escape($_POST['wpStoreCartproduct_inventory']);
                                        $wpStoreCartproduct_useinventory = $wpdb->escape($_POST['wpStoreCartproduct_useinventory']);
                                        $wpStoreCartproduct_donation = $wpdb->escape($_POST['wpStoreCartproduct_donation']);
	
					$devOptions = $this->getAdminOptions();
					
					// Create our PAGE in draft mode in order to get the POST ID
					$my_post = array();
					$my_post['post_title'] = stripslashes($wpStoreCartproduct_name);
					$my_post['post_type'] = 'page';
					$my_post['post_content'] = '';
					$my_post['post_status'] = 'draft';
					$my_post['post_author'] = 1;
					$my_post['post_parent'] = $devOptions['mainpage'];

					// Insert the PAGE into the WP database
					$thePostID = wp_insert_post( $my_post );	
					if($thePostID==0) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 4: wpStoreCart didn't like your data and failed to create a page for it!", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';	
						return false;
					}
	
					// Now insert the product into the wpStoreCart database
					$insert = "
					INSERT INTO {$table_name} (`primkey`, `name`, `introdescription`, `description`, `thumbnail`, `price`, `shipping`, `download`, `tags`, `category`, `inventory`, `dateadded`, `postid`, `timesviewed`, `timesaddedtocart`, `timespurchased`, `useinventory`, `donation`) VALUES
					(NULL, 
					'{$wpStoreCartproduct_name}', 
					'{$wpStoreCartproduct_introdescription}', 
					'{$wpStoreCartproduct_description}', 
					'{$wpStoreCartproduct_thumbnail}', 
					'{$wpStoreCartproduct_price}', 
					'{$wpStoreCartproduct_shipping}', 
					'{$wpStoreCartproduct_download}', 
					'{$wpStoreCartproduct_tags}', 
					'{$wpStoreCartproduct_category}', 
					'{$wpStoreCartproduct_inventory}', 
					'{$timestamp}', 
					'{$thePostID}',
					0,
					0,
					0,
                                        {$wpStoreCartproduct_useinventory},
                                        {$wpStoreCartproduct_donation});
					";					
					
					$results = $wpdb->query( $insert );
					$lastID = $wpdb->insert_id;
					$keytoedit = $lastID;
	
					// Now that we've inserted both the PAGE and the product, let's update and publish our post with the correct content
					$my_post = array();
					$my_post['ID'] = $thePostID;
					$my_post['post_content'] = '[wpstorecart display="product" primkey="'.$lastID.'"]';
					$my_post['post_status'] = 'publish';
					wp_update_post( $my_post );

	

					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '<div class="updated"><p><strong>';
						_e("Your product details have been saved.", "wpStoreCart");
						echo '</strong></p></div>';	
					}  
	
				} else {
				
					echo '<div class="updated"><p><strong>';
					_e("There was a problem with your form!  Did not save data.", "wpStoreCart");
					echo '</strong></p></div>';				
				
				}


			
			}
		
			echo '
			<style type="text/css">
				.wpstorecartoptions {
					float:left;
					border:1px solid #CCCCCC;
					padding: 4px 4px 4px 4px;
					margin: 2px 2px 2px 2px;
					width:300px;
					max-width:300px;
					min-height:110px;
				}
			</style>

			';

			$this->spHeader();
			
			if($isanedit==true) { // An edit's REQUEST_URL will already have the key appended, while a new product won't
				$codeForKeyToEdit = NULL;
			} else {
				$codeForKeyToEdit = '&keytoedit='.$keytoedit;
			}
			if(isset($lastID)) {
				$codeForKeyToEdit = '&keytoedit='.$lastID;
			}
			
			echo '
			<form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">';

                        if($isanedit != true) {
                            echo '<h2>Add';
                        } else {
                            echo '<h2>Edit';
                        }
			echo ' a Product <a href="http://wpstorecart.com/documentation/adding-editing-products/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>';

                        if($isanedit == true) {
                            echo ' <a href="'.get_permalink($result['postid']).'" target="_blank">View Product Page</a>';
                        }

			echo '<table class="widefat">
			<thead><tr><th>Product Attribute</th><th>Value</th><th>Description</th></tr></thead><tbody>
			';
			
			echo '
			<tr>
			<td><h3>Product Name: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1">The name of the product.  We do not recommend stuffing this with keywords, unless you don\'t mind those keywords being repeated everytime the product is mentioned.  Instead, simply keep this as the actual name of the product.</div></h3></td>
			<td><input type="text" name="wpStoreCartproduct_name" style="width: 80%;" value="'.$wpStoreCartproduct_name.'" /></td>
			<td><div style="width:300px;">The title of the product.</div></td>
			</tr>';			

			echo '
			<tr>
			<td><h3>Price & Shipping: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2">The price you wish to charge for the product before tax and shipping charges.  In this early version of wpStoreCart, only flat shipping is available.  In future version, full shipping options and providers will be added.  Set shipping to 0 for digital downloads.</div></h3></td>
			<td>Price: <input type="text" name="wpStoreCartproduct_price" style="width: 58px;" value="'.$wpStoreCartproduct_price.'" />  &nbsp; &nbsp; &nbsp; &nbsp; Shipping: <input type="text" name="wpStoreCartproduct_shipping" style="width: 58px;" value="'.$wpStoreCartproduct_shipping.'" /><p><strong>Accept Donations? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-333777" /><div class="tooltip-content" id="example-content-333777">Note that this feature is only supported in the PayPal payment module currently.  If "Yes" is selected, this product is only given away when donations are made.  Note that the price you set above now becomes the minimum suggested donation amount.</div></strong><label for="wpStoreCartproduct_donation_yes"><input type="radio" id="wpStoreCartproduct_donation_yes" name="wpStoreCartproduct_donation" value="1" '; if ($wpStoreCartproduct_donation == 1) { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="wpStoreCartproduct_donation_no"><input type="radio" id="wpStoreCartproduct_donation_no" name="wpStoreCartproduct_donation" value="false" '; if ($wpStoreCartproduct_donation == 0) { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p></td>
			<td><div style="width:300px;">The price and shipping cost of the product.</div></td>
			</tr>';			


			echo '
			<tr>
			<td><h3>Introduction Description: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3">Keep this short and concise, as this text will be used in several places as a quick description of the product.  For higher sales and conversions, sum up the main features and benefits and include a direct call to action.</div></h3></td>
			<td><textarea class="wpStoreCartproduct_introdescription" id="wpStoreCartproduct_introdescription" name="wpStoreCartproduct_introdescription" style="width: 80%;">'.$wpStoreCartproduct_introdescription.'</textarea>  </td>
			<td><div style="width:300px;">A short introduction to the product. </div></td>
			</tr>';	
			
			
			echo '
			<tr>
			<td><h3>Description: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4">Put your complete sales pitch here.  There are many techniques which can help make your product\'s sale page more effective.  At the very least, most sales pages include at least some of the features and benefits of the product, and include one or more calls to action.</div></h3></td>
			<td><textarea class="wpStoreCartproduct_description" id="wpStoreCartproduct_description" name="wpStoreCartproduct_description" style="width: 80%;">'.$wpStoreCartproduct_description.'</textarea>  </td>
			<td><div style="width:300px;">You should be very detailed and include not only the backstory of the product, but also helpful information like instructions, controls, and objectives.</div></td>
			</tr>';			

                        echo '
			<tr><td><h3>Use Inventory? <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-333" /><div class="tooltip-content" id="example-content-333">Does this product have a limited number available?  If so, set this to yes to use the inventory to tell customers when your product is out of stock.</div></h3></td>
			<td><p><label for="wpStoreCartproduct_useinventory_yes"><input type="radio" id="wpStoreCartproduct_useinventory_yes" name="wpStoreCartproduct_useinventory" value="1" '; if ($wpStoreCartproduct_useinventory == 1) { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="wpStoreCartproduct_useinventory_no"><input type="radio" id="wpStoreCartproduct_useinventory_no" name="wpStoreCartproduct_useinventory" value="false" '; if ($wpStoreCartproduct_useinventory == 0) { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p></td>
			<td class="tableDescription"><p>Set to no for unlimited purchases or<br /> yes if you have a limited amount to sell.</p></td>
			</td></tr>
                        ';

			echo '
			<tr>
			<td><h3>Inventory Quantity: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5">If you\'re selling a limited number of a product (for example, a tangible item with a limited stock, or a digital product that you are only selling a limited number of copies.)</div></h3></td>
			<td><input type="text" name="wpStoreCartproduct_inventory" style="width: 120px;" value="'.$wpStoreCartproduct_inventory.'" />  </td>
			<td><div style="width:300px;">The quantity of items</div></td>
			</tr>';	

			echo '
			<tr>
			<td><h3>Category <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6">Categories allow you to keep products in logically seperated order so that they are easier to find.</div></h3></td>
			<td><select name="wpStoreCartproduct_category"> 
			 <option value="">
						';
			
			attribute_escape(__('Select page')); 
			echo '</option>'; 
			
			$table_name2 = $wpdb->prefix . "wpstorecart_categories";
			$grabCats = "SELECT * FROM `{$table_name2}`;";
			$results2 = $wpdb->get_results( $grabCats , ARRAY_A );
			if(isset($results2)) {
				foreach ($results2 as $pagg) {
					$option = '<option value="'.$pagg['primkey'].'"';
					if($wpStoreCartproduct_category==$pagg['primkey']) {
						$option .= ' selected="selected"';
					}
					$option .='>';
					$option .= $pagg['category'];
					$option .= '</option>';
					echo $option;
				}
			}
			echo '
			</select></td>
			<td><div style="width:300px;">The category the product belongs to.</div></td>
			</tr>';	
			
			echo '
			<tr>
			<td><h3>Tags <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7">Think of a word or phrase that describes your product: that is a tag.  Now use a comma to seperate each of these tags.</div></h3></td>
			<td><input type="text" name="wpStoreCartproduct_tags" style="width: 200px;" value="'.$wpStoreCartproduct_tags.'" />  </td>
			<td><div style="width:300px;">Comma seperated list of tags.  In wpStoreCart, tags serve as an additional way to add organization to your products.</div></td>
			</tr>';	
	
			echo '
			<tr>
			<td><h3>Downloadable Files: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-8" /><div class="tooltip-content" id="example-content-8">If your product is digital in nature, then you can distribute it as a digital download.  If you need to upload more than one file, just select them all in the file selection dialog.  All uploads are stored at: '.WP_CONTENT_DIR . '/uploads/wpstorecart/</div></h3></td>
			<td>File: <input type="text" name="wpStoreCartproduct_download" style="width: 200px;" value="'.$wpStoreCartproduct_download.'" /> or<br />
			Upload a file: <span id="spanSWFUploadButton"></span>
                        <div id="upload-progressbar-container">
                            <div id="upload-progressbar">
                            </div>
                        </div>
			</td>
			<td><div style="width:300px;">The filename of a downloadable product.  Leave this blank for physical products.  Max filesize is either: <strong>'.ini_get('post_max_size').' or '.ini_get('upload_max_filesize').'</strong>, whichever is lower. Do not put URLs or full paths here, only use the upload box.</div></td>
			</tr>';			
			
                        if($wpStoreCartproduct_thumbnail==''||!isset($wpStoreCartproduct_thumbnail)) {
                            $wpStoreCartproduct_thumbnail = WP_PLUGIN_URL.'/wpstorecart/images/default_product_img.jpg';
                        }
			echo '
			<tr>
			<td><h3>Product Thumbnail: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-9" /><div class="tooltip-content" id="example-content-9">The main product image.  It will be used in multiple places.  It is recommend that the image have a 1:1 width and height ratio.  For example, 100px X 100px.</div></h3></td>
			<td>URL: <input type="text" name="wpStoreCartproduct_thumbnail" style="width: 250px;" value="'.$wpStoreCartproduct_thumbnail.'" /> or<br />
			Upload a file: <span id="spanSWFUploadButton2"></span>
                        <div id="upload-progressbar-container2">
                            <div id="upload-progressbar2">
                            </div>
                        </div>
			</td>
			<td><div style="width:300px;">Either a full URL to an image file, or use the upload form to select an image file from your computer.</div></td>
			</tr>';			
			
			if ($_GET['keytoedit']!=0 && is_numeric($_GET['keytoedit'])) {
                            $codeForKeyToEditAjax = '&keytoedit='.$_GET['keytoedit'];
                        }

			echo '
			</tbody></table>';

                        if($isanedit) {
                            echo '
                            <script type="text/javascript">

                                function delvar(keytodel) {
                                    jQuery.ajax({ url: "'.WP_PLUGIN_URL.'/wpstorecart/php/delvar.php", type:"POST", data:"delete="+keytodel, success: function(){
                                        jQuery("#"+keytodel).remove();
                                    }});
                                }

                                function addvar() {
                                    jQuery.ajax({ url: "'.WP_PLUGIN_URL.'/wpstorecart/php/addvar.php", type:"POST", data:"createnewvar="+jQuery("#createnewvar").val()+"&varvalue="+jQuery("#varvalue").val()+"&varprice="+jQuery("#varprice").val()+"&vardesc="+jQuery("#vardesc").val()+"'.$codeForKeyToEditAjax.'&vardownloads="+jQuery("#wpStoreCartproduct_variation").val(), success: function(txt){
                                        jQuery("#varholder").append("<tr id=\'"+txt+"\'><td><img onclick=\'delvar("+txt+");\' style=\'cursor:pointer;\' src=\''.WP_PLUGIN_URL.'/wpstorecart/images/cross.png\' /> "+jQuery("#createnewvar").val()+"</td><td>"+jQuery("#varvalue").val()+"</td><td>"+jQuery("#varprice").val()+"</td><td>"+jQuery("#vardesc").val()+"</td></tr>");
                                    }});
                                }
                            </script>
                            
                            <br style="clear:both;" />
                            <h2>Product Variations &amp; Attributes</h2>
                            <table class="widefat">
                            <thead><tr><th>Variation Category</th><th>One Possible Value</th><th>Price Variation</th><th>Description</th><th>Downloads</th></tr></thead><tbody>
                            <tr><td><img onclick="addvar();" style="cursor:pointer;" src="'.WP_PLUGIN_URL.'/wpstorecart/images/add.png" /> <input type="text" style="width:80%;" name="createnewvar" id="createnewvar" /><br /><i>The name of the variation or attribute, for example: color, size, version, etc.</i></td><td><input type="text" name="varvalue" style="width:80%;" id="varvalue" /><br /><i>Here you should put one of the possible variations.  For example, if your variation was <strong>Color</strong>, then here you put a color, such as <strong>Red</strong>.</i></td><td><input type="text" name="varprice" id="varprice" value="0.00" /><br /><i>The amount that the price changes when a customer selects this variation.  Put 0 here if the price is the same as normal, put -21.90 to subtract from the total, or 35.99 to add to the cost of the item.</i></td><td><textarea id="vardesc" name="vardesc" style="width:80%;"></textarea><br /><i>An explaination of the variation so that customers know what to choose.</i></td><td>
                            <input type="text" id="wpStoreCartproduct_variation" name="wpStoreCartproduct_variation" style="width: 200px;" value="" />
                            Upload a file: <span id="spanSWFUploadButton3"></span>
                            <div id="upload-progressbar-container3">
                                <div id="upload-progressbar3">
                                </div>
                            </div>
                            </td></tr>
                            ';

                            echo '
                            </tbody>
                            </table>

                            <br style="clear:both;" />
                            <table class="widefat" id="varholder">
                                <thead><tr><th>Variation Category</th><th>One Possible Value</th><th>Price Variation</th><th>Description</th></tr></thead><tbody>';

                                $table_name3 = $wpdb->prefix . "wpstorecart_meta";
                                $grabrecord = "SELECT * FROM `{$table_name3}` WHERE `type`='productvariation' AND `foreignkey`={$_GET['keytoedit']};";

                                $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                                if(isset($results)) {
                                        foreach ($results as $result) {
                                            $theKey = $result['primkey'];
                                            $exploder = explode('||', $result['value']);
                                            echo '<tr id="'.$theKey.'"><td><img onclick="delvar('.$theKey.');" style="cursor:pointer;" src="'.WP_PLUGIN_URL.'/wpstorecart/images/cross.png" /> '.$exploder[0].'</td><td>'.$exploder[1].'</td><td>'.$exploder[2].'</td><td>'.$exploder[3].'</td></tr>';
                                        }
                                }

                            echo '
                            </table>
                            <br style="clear:both;" />
                            ';
                        } else {
                            echo '
                            <br style="clear:both;" />
                            <h2>Product Variations &amp; Attributes</h2>
                            <p>Once you\'ve created your product, then you are able to create variations such as multiple sizes, colors, versions, upgrades, and downgrades, all with specific prices.  Save the product now to begin adding variations to it.</p>
                            ';
                        }
                        
                        echo '
			<div class="submit">
			<input type="submit" name="addNewwpStoreCart_product" value="'; _e('Submit product', 'wpStoreCart'); echo'" /></div>
			</form>
        		 </div>';
		
		}	
		// END Prints out the Add products admin page =======================================================================		
		
		
		
		
		//Prints out the Edit products admin page =======================================================================
        function printAdminPageEditproducts() {
			global $wpdb, $user_level;


			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}			
			
			$table_name = $wpdb->prefix . "wpstorecart_products";
			
			// Multi delete
			if(isset($_POST['myCheckbox']) && $_POST['bulkactions']=='delete') {
				$myCheckboxes = $_POST['myCheckbox'];

				if(is_array($_POST['myCheckbox'])) {
					foreach ($myCheckboxes as $myCheckbox) {
						if(is_numeric($myCheckbox)) {
							$sqlbeforedelete = "SELECT `postid` FROM {$table_name} WHERE `primkey`={$myCheckbox};";
							$theDeleteResults = $wpdb->get_results( $sqlbeforedelete , ARRAY_A );		
							
							if(isset($theDeleteResults)) { // Delete the post
								wp_delete_post($theDeleteResults[0]['postid']);
							}
							
							$wpdb->query("DELETE FROM `{$table_name}` WHERE `primkey`={$myCheckbox};");		
						}
					}
				}
			}
			
			if(isset($_GET['keytodelete']) && is_numeric($_GET['keytodelete']))  {
				$sqlbeforedelete = "SELECT `postid` FROM {$table_name} WHERE `primkey`={$_GET['keytodelete']};";
				$theDeleteResults = $wpdb->get_results( $sqlbeforedelete , ARRAY_A );		
				
				if(isset($theDeleteResults)) { // Delete the post
					wp_delete_post($theDeleteResults[0]['postid']);
				}
				
				$wpdb->query("DELETE FROM `{$table_name}` WHERE `primkey`={$_GET['keytodelete']};");
				echo '<div class="updated"><p><strong>';
				_e("Product was removed from the database.", "wpStoreCart");
				echo '</strong></p></div>';					
			}

			$this->spHeader();
			
			echo '
		
			<script type="text/javascript">	
			
			var ischecked = false;
			
			function SetAllCheckBoxes(FormName, FieldName, CheckValue)
				{
					if(!document.forms[FormName])
						return;
					var objCheckBoxes = document.forms[FormName].elements[FieldName];
					if(!objCheckBoxes)
						return;
					var countCheckBoxes = objCheckBoxes.length;
					if(!countCheckBoxes)
						objCheckBoxes.checked = CheckValue;
					else
						// set the check value for all check boxes
						for(var i = 0; i < countCheckBoxes; i++)
							objCheckBoxes[i].checked = CheckValue;
				}

			</script>
			
			<h2>Edit products <a href="http://wpstorecart.com/documentation/adding-editing-products/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>
			
			<form method="post" name="myForm">
			<select name="bulkactions">
				<option value="null">Bulk actions:</option>
				<option value="delete">Delete</option>
				<!--<option value="category">Change Category</option>
				<option value="changeprice">Change Price</option>
				<option value="changeshipping">Change Shipping</option>-->
			</select>
			<input type="submit" name="submitter" class="button-secondary action" value="Apply" onclick="if (! confirm(\'Are you sure you want to do this permanent bulk action?\')) { return false;}" />
			<table class="widefat">
			<thead><tr><th><input type="checkbox" name="selectall" onclick="if (ischecked == false){ SetAllCheckBoxes(\'myForm\', \'myCheckbox\', true);ischecked=true;} else {SetAllCheckBoxes(\'myForm\', \'myCheckbox\', false);ischecked=false;}" /> Action</th><th>Name</th><th>Intro Description</th><th>Description</th><th>Thumbnail</th><th>Price</th><th>Shipping</th><th>Downloads</th><th>Tags</th><th>Category</th><th>Inventory</th></tr></thead><tbody>
			';
			
		
			$startrecord = 0;
			if(isset($_GET['startrecord']) && is_numeric($_GET['startrecord'])) {
				$startrecord = $_GET['startrecord'];
			}
			$numberofrecords = 10;
			
			$totalrecordssql = "SELECT COUNT(`primkey`) AS num FROM `{$table_name}`";
			$totalrecordsres = $wpdb->get_results( $totalrecordssql , ARRAY_A );
			$totalrecords = $totalrecordsres[0]['num'];
			$numberofpages = ceil($totalrecords / $numberofrecords);
			
								

			echo '<div> Pages: ';
			$icounter = 0;
			while ($icounter < $numberofpages) {
				$pagenum = $icounter + 1;
				$offeset = $icounter * $numberofrecords;
				echo '<a href="admin.php?page=wpstorecart-edit-products&startrecord='.$offeset.'">'.$pagenum.'</a> ';
				$icounter++;
			}
			echo '</div><br />';
			
			$grabrecord = "SELECT * FROM `{$table_name}` LIMIT {$startrecord}, {$numberofrecords};";
			
			$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
			if(isset($results)) {
				foreach ($results as $result) {

					$currentCat = '<i>None</i>';
					$table_name2 = $wpdb->prefix . "wpstorecart_categories";
					$grabCats = "SELECT * FROM `{$table_name2}` WHERE `primkey`='{$result['category']}';";
					$results2 = $wpdb->get_results( $grabCats , ARRAY_A );
					if(isset($results2)) {
						foreach ($results2 as $pagg) {
							$currentCat = $pagg['category'];

						}
					}
		
					echo "<tr><td><input type=\"checkbox\" name=\"myCheckbox[]\" value=\"{$result['primkey']}\" /> [ <a href=\"admin.php?page=wpstorecart-add-products&keytoedit={$result['primkey']}\">Edit</a> | <a onclick=\"if (! confirm('Are you sure you want to delete this product?')) { return false;}\" href=\"admin.php?page=wpstorecart-edit-products&keytodelete={$result['primkey']}\">Delete</a> ]</td><td>".stripslashes($result['name'])."</td><td>".stripslashes(substr($result['introdescription'],0,128))."</td><td>".stripslashes(substr($result['description'],0,128))."</td><td><img src=\"{$result['thumbnail']}\" alt=\"\" style=\"max-width:50px;max-height:50px;\" /></td><td>{$result['price']}</td><td>{$result['shipping']}</td><td>".str_replace('||',', ',stripslashes($result['download']))."</td><td>".stripslashes($result['tags'])."</td><td>".stripslashes($currentCat)."</td><td>".stripslashes($result['inventory'])."</td></tr>";
				

				}
			}			
			
			echo '
			</tbody></table>
			</form>
			</div>
			';
		
		}		
		//END Prints out the Edit products admin page =======================================================================
		


		//Prints out the Orders admin page =======================================================================
        function printAdminPageOrders() {
			global $wpdb, $user_info3;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			$devOptions = $this->getAdminOptions();
			$table_name = $wpdb->prefix . "wpstorecart_orders";
			

			// @todo Theres a bug here where this will keep saying this over and over again.
			if(isset($_GET['keytodelete']) && is_numeric($_GET['keytodelete']))  {
				$wpdb->query("DELETE FROM `{$table_name}` WHERE `primkey`={$_GET['keytodelete']};");
				echo '<div class="updated"><p><strong>';
				_e("The order was removed from the database.", "wpStoreCart");
				echo '</strong></p></div>';					
			}
			
			// For new products
			if(!isset($_GET['keytoedit'])) {
				// Default form values
				$wpStoreCartorderstatus = 'Dropped';
				$wpStoreCartcartcontents = '';
				$wpStoreCartpaymentprocessor = 'PayPal';
				$wpStoreCartprice = 0.00;
				$wpStoreCartshipping = 0.00;
				$wpStoreCartwpuser = 0;
				$wpStoreCartemail = '';
				$wpStoreCartaffiliate = 0;
				$keytoedit=0;
			} 
			
			
			// To edit a previous product
			$isanedit = false;
			if(!isset($_GET['keytoedit'])) {$_GET['keytoedit'] = 0;}
			if ($_GET['keytoedit']!=0 && is_numeric($_GET['keytoedit'])) {
				$isanedit = true;
				
				if (isset($_POST['wpStoreCartorderstatus']) && isset($_POST['wpStoreCartcartcontents'])&& isset($_POST['wpStoreCartpaymentprocessor'])&& isset($_POST['wpStoreCartprice'])&& isset($_POST['wpStoreCartshipping'])&& isset($_POST['wpStoreCartwpuser'])&& isset($_POST['wpStoreCartemail'])&& isset($_POST['wpStoreCartaffiliate'])) {
					$wpStoreCartorderstatus = $wpdb->escape($_POST['wpStoreCartorderstatus']);
					$wpStoreCartcartcontents = $wpdb->escape($_POST['wpStoreCartcartcontents']);
					$wpStoreCartpaymentprocessor = $wpdb->escape($_POST['wpStoreCartpaymentprocessor']);
					$wpStoreCartprice = $wpdb->escape($_POST['wpStoreCartprice']);
					$wpStoreCartshipping = $wpdb->escape($_POST['wpStoreCartshipping']);
					$wpStoreCartwpuser = $wpdb->escape($_POST['wpStoreCartwpuser']);
                                        if($wpStoreCartwpuser!='') {
                                            global $userinfo2;
                                            $userinfo2 = get_userdatabylogin($wpStoreCartwpuser);
                                            @$wpStoreCartwpuser = $userinfo2->ID;
                                        }
					$wpStoreCartemail = $wpdb->escape($_POST['wpStoreCartemail']);
					$wpStoreCartaffiliate = $wpdb->escape($_POST['wpStoreCartaffiliate']);					
					$cleanKey = $wpdb->escape($_GET['keytoedit']);
		

					$updateSQL = "
					UPDATE `{$table_name}` SET 
					`orderstatus` = '{$wpStoreCartorderstatus}', 
					`cartcontents` = '{$wpStoreCartcartcontents}', 
					`paymentprocessor` = '{$wpStoreCartpaymentprocessor}', 
					`price` = '{$wpStoreCartprice}', 
					`shipping` = '{$wpStoreCartshipping}', 
					`wpuser` = '{$wpStoreCartwpuser}', 
					`email` = '{$wpStoreCartemail}', 
					`affiliate` = '{$wpStoreCartaffiliate}' 
					WHERE `primkey` = {$cleanKey} LIMIT 1;
					";

					$results = $wpdb->query($updateSQL);
					
					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '<div class="updated"><p><strong>';
						_e("Edit successful!  Your order details have been saved.", "wpStoreCart");
						echo '</strong></p></div>';	
					} 
					
				}
				
				
				
				$keytoedit=$_GET['keytoedit'];	
				$grabrecord = "SELECT * FROM {$table_name} WHERE `primkey`={$keytoedit};";					
				
				$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
				if(isset($results)) {
					foreach ($results as $result) {
						$wpStoreCartorderstatus = stripslashes($result['orderstatus']);
						$wpStoreCartcartcontents = stripslashes($result['cartcontents']);
						$wpStoreCartpaymentprocessor = stripslashes($result['paymentprocessor']);
						$wpStoreCartprice = stripslashes($result['price']);
						$wpStoreCartshipping = stripslashes($result['shipping']);
						$wpStoreCartwpuser = stripslashes($result['wpuser']);
						$wpStoreCartemail = stripslashes($result['email']);
						$wpStoreCartaffiliate = stripslashes($result['affiliate']);		
					}
				} else {
					echo '<div class="updated"><p><strong>';
					echo "There was a problem loading the order you wish to edit.  The query was: {$grabrecord} ";
					echo '</strong></p></div>';					
				}
			}
			
			if (isset($_POST['addNewwpStoreCart_product']) && $isanedit == false) {
			
				if (isset($_POST['wpStoreCartorderstatus']) && isset($_POST['wpStoreCartcartcontents'])&& isset($_POST['wpStoreCartpaymentprocessor'])&& isset($_POST['wpStoreCartprice'])&& isset($_POST['wpStoreCartshipping'])&& isset($_POST['wpStoreCartwpuser'])&& isset($_POST['wpStoreCartemail'])&& isset($_POST['wpStoreCartaffiliate'])) {
					$wpStoreCartorderstatus = $wpdb->escape($_POST['wpStoreCartorderstatus']);
					$wpStoreCartcartcontents = $wpdb->escape($_POST['wpStoreCartcartcontents']);
					$wpStoreCartpaymentprocessor = $wpdb->escape($_POST['wpStoreCartpaymentprocessor']);
					$wpStoreCartprice = $wpdb->escape($_POST['wpStoreCartprice']);
					$wpStoreCartshipping = $wpdb->escape($_POST['wpStoreCartshipping']);
					$wpStoreCartwpuser = $wpdb->escape($_POST['wpStoreCartwpuser']);
					$wpStoreCartemail = $wpdb->escape($_POST['wpStoreCartemail']);
					$wpStoreCartaffiliate = $wpdb->escape($_POST['wpStoreCartaffiliate']);
					
					$devOptions = $this->getAdminOptions();
					


					// Now insert the category into the wpStoreCart database
					$insert = "
					INSERT INTO `{$table_name}` (
					`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`, `wpuser`, `email`, `affiliate`, `date`) 
					VALUES (
						NULL, 
						'{$wpStoreCartorderstatus}', 
						'{$wpStoreCartcartcontents}', 
						'{$wpStoreCartpaymentprocessor}', 
						'{$wpStoreCartprice}', 
						'{$wpStoreCartshipping}', 
						'{$wpStoreCartwpuser}', 
						'{$wpStoreCartemail}', 
						'{$wpStoreCartaffiliate}',
						'".date("Ymd")."'
					);
					";					
	
					$results = $wpdb->query($insert);

					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '<div class="updated"><p><strong>';
						_e("Your order details have been saved.", "wpStoreCart");
						echo '</strong></p></div>';	
						$keytoedit = $wpdb->insert_id;
					}  
	
				} else {
				
					echo '<div class="updated"><p><strong>';
					_e("There was a problem with your form!  Did not save data.", "wpStoreCart");
					echo '</strong></p></div>';				
				
				}


			
			}
		
			echo '
			<style type="text/css">
				.wpstorecartoptions {
					float:left;
					border:1px solid #CCCCCC;
					padding: 4px 4px 4px 4px;
					margin: 2px 2px 2px 2px;
					width:300px;
					max-width:300px;
					min-height:110px;
				}
			</style>
			';
			
			if($isanedit==true) { // An edit's REQUEST_URL will already have the key appended, while a new product won't
				$codeForKeyToEdit = NULL;
			} else {
				if(isset($keytoedit)) {
					$codeForKeyToEdit = '&keytoedit='.$keytoedit;
				} else {
					$codeForKeyToEdit = NULL;
				}
			}
			if(isset($lastID)) {
				$codeForKeyToEdit = '&keytoedit='.$lastID;
			}
			
			$this->spHeader();
			
			echo '
			<form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">
			';
			if ($isanedit != true) {
				echo '<h2>Add a Order</h2>';
			} else {
				echo '<h2>Edit a Order</h2>Add a new order by <a href="admin.php?page=wpstorecart-orders">clicking here</a>.<br />';
			}
			
			echo '
                        <script type="text/javascript">
                            function deleteItemInCart(primkey) {
                                var re = new RegExp(primkey + "\\\*[0-9]+," );
                                if(jQuery("#wpStoreCartcartcontents").val().match(re)) {
                                    //This means we found the product to delete
                                    jQuery("#wpStoreCartcartcontents").val(jQuery("#wpStoreCartcartcontents").val().replace(re, ""));
                                    jQuery("#delIcon"+primkey).remove();
                                }
                            }

                            function addItemToCart(primkey) {
                                if(!isNaN(primkey) && primkey > 0) {
                                    var theQuantity = prompt("How many do you want to add?", "1");
                                    if(!isNaN(theQuantity)) {
                                        jQuery("#wpStoreCartcartcontents").val(primkey+"*"+theQuantity+"," + jQuery("#wpStoreCartcartcontents").val());
                                        jQuery("#wpstorecartaddproductform").submit();
                                    }
                                }
                            }
                        </script>
                        <table class="widefat">
			<thead><tr><th> </th><th>Order Status <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>"Dropped" means they added it to their cart, but never completed checkout.  "Order Recieved" means the customer successfully completed the checkout process, but an admin hasn\'t verified and approved the order yet.  "Pending" means the order is delayed until an admin changes the order status.  "Canceled" means the order was manually canceled by an admin.  "Completed" means the order is fulfilled, the payment was successfully recieved and approved.</h3></div></th><th>Cart Contents <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>The items that were in the customers shopping cart.  You can add or remove items if you need to modify or fulfill an order manually.</h3></div></th><th>Processor <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3"><h3>The payment gateway that was used in the transaction.</h3></div></th><th>Price <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4"><h3>The total price of everything added together in the shopping cart.</h3></div></th><th>Shipping <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5"><h3>The total shipping of everything in the shopping cart.</h3></div></th><th>User <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6"><h3>The Wordpress User ID of the purchaser.</h3></div></th><th>Email <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-8" /><div class="tooltip-content" id="example-content-8"><h3>The email address the customer used to make the purchase.</h3></div></th><th>Affiliate <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7"><h3>The Wordpress user ID of the affiliate who is credited with driving the sale.</h3></div></th></tr></thead><tbody>
			';
			
			echo '
			<tr>
			<td></td>
			<td>';

                        /*
                        echo '
			<select name="wpStoreCartorderstatus"> 
			 <option value="">
						';
			
			attribute_escape(__('Select page')); 
			echo '</option>'; 
			
			$icounter = 0;
			$result2 = array();
			$result2[0][0]='Sent to PayPal';
			$result2[1][1]='Refunded';
			$result2[2][2]='Pending';
			$result2[3][3]='Canceled';
			$result2[4][4]='Completed';
                        $result2[5][5]='Reversed';
			foreach ($result2 as $pagg) {
				$option = '<option value="'.$pagg[$icounter].'"';
				if($pagg[$icounter]==$wpStoreCartorderstatus) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $pagg[$icounter];
				$option .= '</option>';
				echo $option;
				$icounter++;
			}

                        $user_info = get_userdata($wpStoreCartwpuser);


			echo '
			</select>';
                         * 
                         */

                        echo '<input name="wpStoreCartorderstatus" id="wpStoreCartorderstatus" type="text" value="'.$wpStoreCartorderstatus.'">';

                         echo '</td>

			<td><input type="hidden" id="wpStoreCartcartcontents" name="wpStoreCartcartcontents" style="width: 80%;" value="'.$wpStoreCartcartcontents.'" />';
                        if ($isanedit == true) {
                            echo'<select name="addNewProduct" id="addNewProduct">
                             <option value="">
                                                    ';

                            attribute_escape(__('Select product'));
                            echo '</option>';

                            $table_name2 = $wpdb->prefix . "wpstorecart_products";
                            $grabCats = "SELECT * FROM `{$table_name2}`;";
                            $results2 = $wpdb->get_results( $grabCats , ARRAY_A );
                            if(isset($results2)) {
                                    foreach ($results2 as $pagg) {
                                            $option = '<option value="'.$pagg['primkey'].'"';
                                            if($wpStoreCartproduct==$pagg['primkey']) {
                                                    $option .= ' selected="selected"';
                                            }
                                            $option .='>';
                                            $option .= $pagg['name'];
                                            $option .= '</option>';
                                            echo $option;
                                    }
                            }
                            echo '
                            </select><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/add.png" style="cursor:pointer;" onclick="addItemToCart(jQuery(\'#addNewProduct\').val());" ></a><br />'.$this->splitOrderIntoProduct($keytoedit, 'edit');
                        }
                        echo '</td>
			<td><input type="text" name="wpStoreCartpaymentprocessor" style="width: 80%;" value="'.$wpStoreCartpaymentprocessor.'" /></td>
			<td><input type="text" name="wpStoreCartprice" style="width: 80%;" value="'.$wpStoreCartprice.'" /></td>
			<td><input type="text" name="wpStoreCartshipping" style="width: 80%;" value="'.$wpStoreCartshipping.'" /></td>
			<td><input type="text" name="wpStoreCartwpuser" style="width: 80%;" value="';if ($isanedit == true) {echo @$user_info->user_login;}; echo'" /></td>
			<td><input type="text" name="wpStoreCartemail" style="width: 80%;" value="'.$wpStoreCartemail.'" /></td>
			<td><input type="text" name="wpStoreCartaffiliate" style="width: 80%;" value="'.$wpStoreCartaffiliate.'" /></td>
			</tr>';			
			
			echo '
			</tbody>
			</table>
			<br style="clear:both;" />
			<div class="submit">
			<input type="submit" name="addNewwpStoreCart_product" value="'; _e('Submit Order', 'wpStoreCart'); echo'" /></div>
			</form>
			</div>';	
			
			echo '
			<br style="clear:both;" /><br />
			<h2>Edit Orders</h2>';

                        echo '<form action="" method="post">Sort by <select name="orderby"><option value="`date`">date</option><option value="`wpuser`">user</option><option value="`orderstatus`">order status</option><option value="`affiliate`">affiliate</option><option value="`paymentprocessor`">processor</option><option value="`price`">price</option></select> in <select name="ordersort"><option value="DESC">descending</option><option value="ASC">ascending</option></select> order. <input type="submit" value="Submit"></input></form>';
			echo '<table class="widefat">
			<thead><tr><th> </th><th>Order Status</th><th>Cart Contents</th><th>Processor</th><th>Price</th><th>Shipping</th><th>User</th><th>Email</th><th>Affiliate</th></tr></thead><tbody>
			';


                        if(@!isset($_POST['orderby'])) {
                            $orderby = '`date`';
                        } else {
                            $orderby = $wpdb->prepare($_POST['orderby']);
                        }
                        if(@!isset($_POST['ordersort'])) {
                            $ordersort = 'DESC';
                        } else {
                            $ordersort = $wpdb->prepare($_POST['ordersort']);
                        }
			$grabrecord = "SELECT * FROM `{$table_name}` ORDER BY {$orderby} {$ordersort};";
			
			$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
			if(isset($results)) {
				foreach ($results as $result) {
					$wpStoreCartorderstatus = $result['orderstatus'];
					$wpStoreCartcartcontents = $result['cartcontents'];
					$wpStoreCartpaymentprocessor = $result['paymentprocessor'];
					$wpStoreCartprice = $result['price'];
					$wpStoreCartshipping = $result['shipping'];
					$wpStoreCartwpuser = $result['wpuser'];
					$wpStoreCartemail = $result['email'];
					$wpStoreCartaffiliate = $result['affiliate'];
                                        if(isset($wpStoreCartwpuser) && $wpStoreCartwpuser!=0) {
                                            $user_info3 = get_userdata($wpStoreCartwpuser);
                                        }
										
					echo "
					<tr>
					<td>[ {$result['primkey']} | <a href=\"admin.php?page=wpstorecart-orders&keytoedit={$result['primkey']}\">Edit</a> | <a onclick=\"if (! confirm('Are you sure you want to delete this order?')) { return false;}\" href=\"admin.php?page=wpstorecart-orders&keytodelete={$result['primkey']}\">Delete</a> ]</td>
					<td>{$wpStoreCartorderstatus}</td>
					<td>".$this->splitOrderIntoProduct($result['primkey'])."</td>
					<td>{$wpStoreCartpaymentprocessor}</td>
					<td>{$wpStoreCartprice}</td>
					<td>{$wpStoreCartshipping}</td>
					<td>";
                                        if(isset($wpStoreCartwpuser) && $wpStoreCartwpuser!=0) {
                                            echo "<a href=\"user-edit.php?user_id={$wpStoreCartwpuser}\">{$user_info3->user_login}</a></td>";
                                        }
                                        echo "
					<td>{$wpStoreCartemail}</td>
					<td>{$wpStoreCartaffiliate}</td>
					</tr>";	
					

				}
			}					
			
			
		

			
			echo '
			</tbody>
			</table>
			<br style="clear:both;" />';	
		
		}	
		// END Prints out the Orders admin page =======================================================================		
				
		



	
		//Prints out the Categories admin page =======================================================================
        function printAdminPageCategories() {
			global $wpdb;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			$devOptions = $this->getAdminOptions();
			$table_name = $wpdb->prefix . "wpstorecart_categories";
			


			// @todo Theres a bug here where this will keep saying this over and over again.
			if(isset($_GET['keytodelete']) && is_numeric($_GET['keytodelete']))  {
				$wpdb->query("DELETE FROM `{$table_name}` WHERE `primkey`={$_GET['keytodelete']};");
				echo '<div class="updated"><p><strong>';
				_e("Category was removed from the database.", "wpStoreCart");
				echo '</strong></p></div>';					
			}
			
			// For new products
			if(!isset($_GET['keytoedit'])) {
				// Default form values
				$wpStoreCartCategory = '';
				$wpStoreCartCategoryParent = 0;
				$wpStoreCartCategoryThumbnail = '';
				$wpStoreCartCategoryDescription = '';
				$wpStoreCartCategoryPostID = 0;				
				$keytoedit=0;
			} 
			
			
			// To edit a previous category
			$isanedit = false;
			if(!isset($_GET['keytoedit'])) {$_GET['keytoedit'] = 0;}
			if ($_GET['keytoedit']!=0 && is_numeric($_GET['keytoedit'])) {
				$isanedit = true;
				
				if (isset($_POST['wpStoreCartCategory'])) {
					$wpStoreCartCategory = $wpdb->escape($_POST['wpStoreCartCategory']);
					$wpStoreCartCategoryParent = $wpdb->escape($_POST['wpStoreCartCategoryParent']);
					$wpStoreCartCategoryThumbnail = $wpdb->escape($_POST['wpStoreCartCategoryThumbnail']);
					$wpStoreCartCategoryDescription = $wpdb->prepare($_POST['wpStoreCartCategoryDescription']);
					$wpStoreCartCategoryPostID = $wpdb->prepare($_POST['wpStoreCartCategoryPostID']);
					$cleanKey = $wpdb->escape($_GET['keytoedit']);
		

					$updateSQL = "
					UPDATE `{$table_name}` SET 
					`parent` = '{$wpStoreCartCategoryParent}', 
					`category` = '{$wpStoreCartCategory}',
					`thumbnail` = '{$wpStoreCartCategoryThumbnail}',
					`description` = '{$wpStoreCartCategoryDescription}',
					`postid` = '{$wpStoreCartCategoryPostID}'
					WHERE `primkey` ={$cleanKey} LIMIT 1 ;				
					";

					$results = $wpdb->query($updateSQL);
					
					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '<div class="updated"><p><strong>';
						_e("Edit successful!  Your category details have been saved.", "wpStoreCart");
						echo '</strong></p></div>';	
					} 
					
				}
				
				
				
				$keytoedit=$_GET['keytoedit'];	
				$grabrecord = "SELECT * FROM {$table_name} WHERE `primkey`={$keytoedit};";					
				
				$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
				if(isset($results)) {
					foreach ($results as $result) {
						
						$wpStoreCartCategoryParent = stripslashes($result['parent']);
						$wpStoreCartCategory = stripslashes($result['category']);
						$wpStoreCartCategoryThumbnail = stripslashes($result['thumbnail']);
						$wpStoreCartCategoryDescription = stripslashes($result['description']);
						$wpStoreCartCategoryPostID = stripslashes($result['postid']);						
			
					}
				} else {
					echo '<div class="updated"><p><strong>';
					echo "There was a problem loading the category you wish to edit.  The query was: {$grabrecord} ";
					echo '</strong></p></div>';					
				}
			}
			
			if (isset($_POST['addNewwpStoreCart_product']) && $isanedit == false) {
			
				if (isset($_POST['wpStoreCartCategoryParent']) && isset($_POST['wpStoreCartCategory'])) {
					$wpStoreCartCategoryParent = $wpdb->escape($_POST['wpStoreCartCategoryParent']);
					$wpStoreCartCategory = $wpdb->escape($_POST['wpStoreCartCategory']);
					$wpStoreCartCategoryThumbnail = $wpdb->escape($_POST['wpStoreCartCategoryThumbnail']);
					$wpStoreCartCategoryDescription = $wpdb->prepare($_POST['wpStoreCartCategoryDescription']);
					$wpStoreCartCategoryPostID = $wpdb->prepare($_POST['wpStoreCartCategoryPostID']);					
	
					$devOptions = $this->getAdminOptions();
					


					// Now insert the category into the wpStoreCart database
					$insert = "
					INSERT INTO `{$table_name}` (
					`primkey` ,
					`parent` ,
					`category`,
					`thumbnail`,
					`description`,
					`postid`
					)
					VALUES (
					NULL , '{$wpStoreCartCategoryParent}', '{$wpStoreCartCategory}', '{$wpStoreCartCategoryThumbnail}', '{$wpStoreCartCategoryDescription}', '{$wpStoreCartCategoryPostID}'
					);
					";					
	
					$results = $wpdb->query($insert);

					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '<div class="updated"><p><strong>';
						_e("Your category details have been saved.", "wpStoreCart");
						echo '</strong></p></div><br />';
						$keytoedit = $wpdb->insert_id;
					}  
	
				} else {
				
					echo '<div class="updated"><p><strong>';
					_e("There was a problem with your form!  Did not save data.", "wpStoreCart");
					echo '</strong></p></div>';				
				
				}


			
			}
		
			echo '
			<style type="text/css">
				.wpstorecartoptions {
					float:left;
					border:1px solid #CCCCCC;
					padding: 4px 4px 4px 4px;
					margin: 2px 2px 2px 2px;
					width:300px;
					max-width:300px;
					min-height:110px;
				}
			</style>
			';
			
			if($isanedit==true) { // An edit's REQUEST_URL will already have the key appended, while a new product won't
				$codeForKeyToEdit = NULL;
			} else {
				if(isset($keytoedit)) {
					$codeForKeyToEdit = '&keytoedit='.$keytoedit;
				} else {
					$codeForKeyToEdit = NULL;
				}
			}
			if(isset($lastID)) {
				$codeForKeyToEdit = '&keytoedit='.$lastID;
			}
			
			$this->spHeader();
			
			echo '
			<form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">
			';
			if ($isanedit != true) {
				echo '<h2>Add a Category</h2>Add a new category by <a href="admin.php?page=wpstorecart-categories">clicking here</a>.<br />';
			} else {
				echo '<h2>Edit a Category</h2>Add a new category by <a href="admin.php?page=wpstorecart-categories">clicking here</a>.<br />';
			}
			
			echo '<table class="widefat">
			<thead><tr><th> </th><th>Category <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>The name of the category.  Essentially, if you\'re selling a bunch of hats, make a category called hats.  It\'s that easy!</h3></div></th><th>Parent <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>If you select a parent category, then the category you are creating is a child category.  For example, if you sold red and blue hats, you would select hats as the parent.</h3></div></th><th>Thumb</th><th>Description</th><th>Page</th></tr></thead><tbody>
			';
			
			echo '
			<tr>
			<td></td>
			<td><input type="text" name="wpStoreCartCategory" style="width: 80%;" value="'.$wpStoreCartCategory.'" /></td>
			<td><select name="wpStoreCartCategoryParent"> 
			 <option value="">
						';
			
			attribute_escape(__('Select page')); 
			echo '</option>'; 
			
			$table_name2 = $wpdb->prefix . "wpstorecart_categories";
			$grabCats = "SELECT * FROM `{$table_name2}`;";
			$results2 = $wpdb->get_results( $grabCats , ARRAY_A );
			if(isset($results2)) {
				foreach ($results2 as $pagg) {
					$option = '<option value="'.$pagg['primkey'].'"';
					if($wpStoreCartCategoryParent==$pagg['primkey']) {
						$option .= ' selected="selected"';
					}
					$option .='>';
					$option .= $pagg['category'];
					$option .= '</option>';
					echo $option;
				}
			}
			echo '
			</select></td>			
			<td><input type="text" name="wpStoreCartCategoryThumbnail" style="width: 80%;" value="'.$wpStoreCartCategoryThumbnail.'" /></td>
			<td><input type="text" name="wpStoreCartCategoryDescription" style="width: 80%;" value="'.$wpStoreCartCategoryDescription.'" /></td>
			<td><input type="text" name="wpStoreCartCategoryPostID" style="width: 80%;" value="'.$wpStoreCartCategoryPostID.'" /></td>
			</tr>';			
			
			echo '
			</tbody>
			</table>
			<br style="clear:both;" />
			<div class="submit">
			<input type="submit" name="addNewwpStoreCart_product" value="'; _e('Submit Category', 'wpStoreCart'); echo'" /></div>
			</form>
			</div>';	
			
			echo '
			<br style="clear:both;" /><br />
			<h2>Edit Categories</h2>';
			
			echo '<table class="widefat">
			<thead><tr><th>Action</th><th>Category</th><th>Parent</th></tr></thead><tbody>
			';


			$grabrecord = "SELECT * FROM `{$table_name}`;";
			
			$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
			if(isset($results)) {
				foreach ($results as $result) {
					$wpStoreCartCategoryParent = $result['parent'];
					$wpStoreCartCategory = $result['category'];
										
					$currentCat = '<i>None</i>';
					$table_name2 = $wpdb->prefix . "wpstorecart_categories";
					$grabCats = "SELECT * FROM `{$table_name2}` WHERE `primkey`='{$result['parent']}';";
					$results2 = $wpdb->get_results( $grabCats , ARRAY_A );
					if(isset($results2)) {
						foreach ($results2 as $pagg) {
							$currentCat = $pagg['category'];

						}
					}										
										
					echo "
					<tr>
					<td>[ {$result['primkey']} | <a href=\"admin.php?page=wpstorecart-categories&keytoedit={$result['primkey']}\">Edit</a> | <a onclick=\"if (! confirm('Are you sure you want to delete this category?')) { return false;}\" href=\"admin.php?page=wpstorecart-categories&keytodelete={$result['primkey']}\">Delete</a> ]</td>
					<td><a href=\"admin.php?page=wpstorecart-categories&keytoedit={$result['primkey']}\">{$result['category']}</a></td>
					<td><div style=\"width:300px;\">{$currentCat}</div></td>
					</tr>";	
					

				}
			}					
			
			
		

			
			echo '
			</tbody>
			</table>
			<br style="clear:both;" />';	
		
		}	
		// END Prints out the Categories admin page =======================================================================		
				
		


		
		
		
		//Prints out the Coupons admin page =======================================================================
        function printAdminPageCoupons() {
			global $wpdb;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			$devOptions = $this->getAdminOptions();
			$table_name = $wpdb->prefix . "wpstorecart_coupons";
			


			// @todo Theres a bug here where this will keep saying this over and over again.
			if(isset($_GET['keytodelete']) && is_numeric($_GET['keytodelete']))  {
				$wpdb->query("DELETE FROM `{$table_name}` WHERE `primkey`={$_GET['keytodelete']};");
				echo '<div class="updated"><p><strong>';
				_e("Coupon was removed from the database.", "wpStoreCart");
				echo '</strong></p></div>';					
			}
			
			// For new products
			if(!isset($_GET['keytoedit'])) {
				// Default form values
				$wpStoreCartcode = '';
				$wpStoreCartamount = 0.00;
				$wpStoreCartpercent = 0;
				$wpStoreCartdescription = 'Describe your coupon here';
				$wpStoreCartproduct = 0;
				$wpStoreCartstartdate = date("Ymd");
				$wpStoreCartenddate = date("Ymd");
				$keytoedit=0;
			} 
			
			
			// To edit a previous product
			$isanedit = false;
			if(!isset($_GET['keytoedit'])) {$_GET['keytoedit'] = 0;}
			if ($_GET['keytoedit']!=0 && is_numeric($_GET['keytoedit'])) {
				$isanedit = true;
				
				if (isset($_POST['wpStoreCartcode']) && isset($_POST['wpStoreCartamount']) && isset($_POST['wpStoreCartpercent']) && isset($_POST['wpStoreCartdescription']) && isset($_POST['wpStoreCartproduct']) && isset($_POST['wpStoreCartstartdate']) && isset($_POST['wpStoreCartenddate'])) {
					$wpStoreCartcode = $wpdb->escape($_POST['wpStoreCartcode']);
					$wpStoreCartamount = $wpdb->escape($_POST['wpStoreCartamount']);
					$wpStoreCartpercent = $wpdb->escape($_POST['wpStoreCartpercent']);
					$wpStoreCartdescription = $wpdb->escape($_POST['wpStoreCartdescription']);
					$wpStoreCartproduct = $wpdb->escape($_POST['wpStoreCartproduct']);
					$wpStoreCartstartdate = $wpdb->escape($_POST['wpStoreCartstartdate']);
					$wpStoreCartenddate = $wpdb->escape($_POST['wpStoreCartenddate']);
					$cleanKey = $wpdb->escape($_GET['keytoedit']);
		

					$updateSQL = "
					UPDATE `{$table_name}` SET 
					`code` = '{$wpStoreCartcode}', 
					`amount` = '{$wpStoreCartamount}', 
					`percent` = '{$wpStoreCartpercent}', 
					`description` = '{$wpStoreCartdescription}', 
					`product` = '{$wpStoreCartproduct}' ,
					`startdate` = '{$wpStoreCartstartdate}',
					`enddate` = '{$wpStoreCartenddate}'
					WHERE `primkey` ={$cleanKey} LIMIT 1 ;				
					";

					$results = $wpdb->query($updateSQL);
					
					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '<div class="updated"><p><strong>';
						_e("Edit successful!  Your coupon details have been saved.", "wpStoreCart");
						echo '</strong></p></div>';	
					} 
					
				}
				
				
				
				$keytoedit=$_GET['keytoedit'];	
				$grabrecord = "SELECT * FROM {$table_name} WHERE `primkey`={$keytoedit};";					
				
				$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
				if(isset($results)) {
					foreach ($results as $result) {
						$wpStoreCartcode = stripslashes($result['code']);
						$wpStoreCartamount = stripslashes($result['amount']);
						$wpStoreCartpercent = stripslashes($result['percent']);
						$wpStoreCartdescription = stripslashes($result['description']);
						$wpStoreCartproduct = stripslashes($result['product']);						
						$wpStoreCartstartdate = stripslashes($result['startdate']);
						$wpStoreCartenddate = stripslashes($result['enddate']);
					}
				} else {
					echo '<div class="updated"><p><strong>';
					echo "There was a problem loading the coupon you wish to edit.  The query was: {$grabrecord} ";
					echo '</strong></p></div>';					
				}
			}
			
			if (isset($_POST['addNewwpStoreCart_product']) && $isanedit == false) {
			
				if (isset($_POST['wpStoreCartcode']) && isset($_POST['wpStoreCartamount']) && isset($_POST['wpStoreCartpercent']) && isset($_POST['wpStoreCartdescription']) && isset($_POST['wpStoreCartproduct']) && isset($_POST['wpStoreCartstartdate'])&& isset($_POST['wpStoreCartenddate'])) {
					$wpStoreCartcode = $wpdb->escape($_POST['wpStoreCartcode']);
					$wpStoreCartamount = $wpdb->escape($_POST['wpStoreCartamount']);
					$wpStoreCartpercent = $wpdb->escape($_POST['wpStoreCartpercent']);
					$wpStoreCartdescription = $wpdb->escape($_POST['wpStoreCartdescription']);
					$wpStoreCartproduct = $wpdb->escape($_POST['wpStoreCartproduct']);
					$wpStoreCartstartdate = $wpdb->escape($_POST['wpStoreCartstartdate']);
					$wpStoreCartenddate = $wpdb->escape($_POST['wpStoreCartenddate']);
	
					$devOptions = $this->getAdminOptions();

					// Now insert the category into the wpStoreCart database
					$insert = "
					INSERT INTO `{$table_name}` (`primkey`, `code`, `amount`, `percent`, `description`, `product`, `startdate`, `enddate`) VALUES (
					NULL, 
					'{$wpStoreCartcode}', 
					'{$wpStoreCartamount}', 
					'{$wpStoreCartpercent}', 
					'{$wpStoreCartdescription}', 
					'{$wpStoreCartproduct}',
					'{$wpStoreCartstartdate}',
					'{$wpStoreCartenddate}');
					";					
	
					$results = $wpdb->query($insert);

					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '<div class="updated"><p><strong>';
						_e("Your coupon details have been saved.", "wpStoreCart");
						echo '</strong></p></div>';	
						$keytoedit = $wpdb->insert_id;
					}  
	
				} else {
				
					echo '<div class="updated"><p><strong>';
					_e("There was a problem with your form!  Did not save data.", "wpStoreCart");
					echo '</strong></p></div>';				
				
				}


			
			}
		
			echo '
			<style type="text/css">
				.wpstorecartoptions {
					float:left;
					border:1px solid #CCCCCC;
					padding: 4px 4px 4px 4px;
					margin: 2px 2px 2px 2px;
					width:300px;
					max-width:300px;
					min-height:110px;
				}
			</style>
			';
			
			if($isanedit==true) { // An edit's REQUEST_URL will already have the key appended, while a new product won't
				$codeForKeyToEdit = NULL;
			} else {
				if(isset($keytoedit)) {
					$codeForKeyToEdit = '&keytoedit='.$keytoedit;
				} else {
					$codeForKeyToEdit = NULL;
				}
			}
			if(isset($lastID)) {
				$codeForKeyToEdit = '&keytoedit='.$lastID;
			}
			
			$this->spHeader();
			
			echo '
			<form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">
			';
			
			if ($isanedit != true) {
				echo '<h2>Add a Coupon <a href="http://wpstorecart.com/documentation/coupons/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>';
			} else {
				echo '<h2>Edit a Coupon <a href="http://wpstorecart.com/documentation/coupons/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/bighelp.png" /></a></h2>Add a new coupon by <a href="admin.php?page=wpstorecart-coupon">clicking here</a>.<br />';
			}
			
			echo '<table class="widefat">
			<thead><tr><th>Coupon Code <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>Don\'t use spaces! This is what people should type or paste into the coupon box during checkout in order to recieve a discount.  As such, this should be a short code, with no spaces, all alpha numeric characters, etc.</h3></div></th><th>Flat Discount <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>A flat amount to deduct when the coupon code is used.  You can combine this with the Pecentage Discount, but for simplicities sake, we recommend choosing either a flat discount or a percentage, but not both.</h3></div></th><!--<th>Percentage Dicount <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3"><h3>The percentage of the price to deduct from the purchase.</h3></div></th>--><th>Description <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4"><h3>Take a note of what your coupon is meant to do by writing a description here.</h3></div></th><th>Product <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5"><h3>The product you want the coupon to apply to.  <!--Set to 0 for the coupon to work on all products in the store.--></h3></div></th><th>Start Date <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6"><h3>The day which the coupon starts working.  Before this date, the coupon is invalid.</h3></div></th><th>Expiration Date <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7"><h3>The date which the coupon code stops working.  After this date, the coupon is invalid.</h3></div></th></tr></thead><tbody>
			';

			
			echo '
			<tr>
			<td><input type="text" name="wpStoreCartcode" style="width: 80%;" value="'.$wpStoreCartcode.'" /></td>
			<td><input type="text" name="wpStoreCartamount" style="width: 80%;" value="'.$wpStoreCartamount.'" /></td>
			<td style="display:none;"><input type="hidden" name="wpStoreCartpercent" style="width: 80%;" value="'.$wpStoreCartpercent.'" /></td>
			<td><input type="text" name="wpStoreCartdescription" style="width: 80%;" value="'.$wpStoreCartdescription.'" /></td>
			<td>
<!--<input type="text" name="wpStoreCartproduct" style="width: 80%;" value="'.$wpStoreCartproduct.'" />-->
<select name="wpStoreCartproduct">
			 <option value="">
						';
			
			attribute_escape(__('Select product'));
			echo '</option>'; 
			
			$table_name2 = $wpdb->prefix . "wpstorecart_products";
			$grabCats = "SELECT * FROM `{$table_name2}`;";
			$results2 = $wpdb->get_results( $grabCats , ARRAY_A );
			if(isset($results2)) {
				foreach ($results2 as $pagg) {
					$option = '<option value="'.$pagg['primkey'].'"';
					if($wpStoreCartproduct==$pagg['primkey']) {
						$option .= ' selected="selected"';
					}
					$option .='>';
					$option .= $pagg['name'];
					$option .= '</option>';
					echo $option;
				}
			}
			echo '
			</select>
</td>
			<td><input type="text" name="wpStoreCartstartdate" id="wpStoreCartstartdate" style="width: 80%;" value="'.$wpStoreCartstartdate.'" /></td>
			<td><input type="text" name="wpStoreCartenddate" id="wpStoreCartenddate" style="width: 80%;" value="'.$wpStoreCartenddate.'" /></td>			
			</tr>';			
			
			echo '
			</tbody>
			</table>
			
			<script type="text/javascript">
			  AnyTime.picker( "wpStoreCartstartdate", 
				  { format: "%Y%m%d" } );
			  AnyTime.picker( "wpStoreCartenddate", 
				  { format: "%Y%m%d" } );
			</script>
			
			<br style="clear:both;" />
			<div class="submit">
			<input type="submit" name="addNewwpStoreCart_product" value="'; _e('Submit Coupon', 'wpStoreCart'); echo'" /></div>
			</form>
			</div>';	
			
			echo '
			<br style="clear:both;" /><br />
			<h2>Edit Coupons</h2>';
			
			echo '<table class="widefat">
			<thead><tr><th>Key</th><th>The Coupon Code</th><th>Flat Discount</th><!--<th>Percentage Dicount</th>--><th>Description</th><th>Product</th><th>Start Date</th><th>Expiration Date</th></tr></thead><tbody>
			';


			$grabrecord = "SELECT * FROM `{$table_name}`;";
			
			$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
			if(isset($results)) {
				foreach ($results as $result) {
					
					echo "
					<tr>
					<td>[ ".$result['primkey']." | <a href=\"admin.php?page=wpstorecart-coupon&keytoedit=".$result['primkey']."\">Edit</a> | <a onclick=\"if (! confirm('Are you sure you want to delete this coupon?')) { return false;}\" href=\"admin.php?page=wpstorecart-coupon&keytodelete=".$result['primkey']."\">Delete</a> ]</td>
					<td>".$result['code']."</td>
					<td>".$result['amount']."</td>
					<!--<td>".$result['percent']."</td>-->
					<td>".$result['description']."</td>
					<td>".$result['product']."</td>
					<td>".$result['startdate']."</td>
					<td>".$result['enddate']."</td>	
					</tr>";	
					

				}
			}					
			
			
		

			
			echo '
			</tbody>
			</table>
			<br style="clear:both;" />';	
		
		}	
		// END Prints out the Coupons admin page =======================================================================			
		
		
		
		//Prints out the Statistics admin page =======================================================================
        function printAdminPageStatistics() {
			global $wpdb, $devOptions;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			$devOptions = $this->getAdminOptions();
			
                        $this->spHeader();
		
			require_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/statistics.php');
		
		}
		// ==========================================================================================================		
		

		//Prints out the Overview admin page =======================================================================
        function printAdminPageOverview() {
			global $wpdb;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		


			$devOptions = $this->getAdminOptions();

                        if(isset($_GET['wpscaction']) && $_GET['wpscaction']=='createpages') {
                            if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage']) || $devOptions['mainpage']==0) {
                                // Insert the PAGE into the WP database
                                $my_post = array();
                                $my_post['post_title'] = 'Store';
                                $my_post['post_type'] = 'page';
                                $my_post['post_author'] = 1;
                                $my_post['post_parent'] = 0;
                                $my_post['post_content'] = '[wpstorecart]';
                                $my_post['post_status'] = 'publish';
                                $thePostIDx = wp_insert_post( $my_post );

                                if($thePostIDx==0) {
                                        echo '<div class="updated"><p><strong>';
                                        _e("ERROR 4: wpStoreCart didn't like your data and failed to create a page for it!", "wpStoreCart");
                                        echo $wpdb->print_error();
                                        echo '</strong></p></div>';
                                        return false;
                                } 
                                @$devOptions['mainpage']=$thePostIDx;
                                update_option('wpStoreCartAdminOptions', $devOptions);
                                
                                

                            }
                            if(!isset($devOptions['checkoutpage']) || !is_numeric($devOptions['checkoutpage']) || $devOptions['checkoutpage']==0) {
                                // Insert the PAGE into the WP database
                                $my_post = array();
                                $my_post['post_title'] = 'Checkout';
                                $my_post['post_type'] = 'page';
                                $my_post['post_author'] = 1;
                                $my_post['post_parent'] = 0;
                                $my_post['post_content'] = '[wpstorecart display="checkout"]';
                                $my_post['post_status'] = 'publish';
                                $thePostIDy = wp_insert_post( $my_post );

                                if($thePostIDy==0) {
                                        echo '<div class="updated"><p><strong>';
                                        _e("ERROR 4: wpStoreCart didn't like your data and failed to create a page for it!", "wpStoreCart");
                                        echo $wpdb->print_error();
                                        echo '</strong></p></div>';
                                        return false;
                                } 
                                @$devOptions['checkoutpage']=$thePostIDy;
                                @$devOptions['checkoutpageurl'] = get_permalink($devOptions['checkoutpage']);
                                update_option('wpStoreCartAdminOptions', $devOptions);

                                
                            }
                        }

		
			$this->spHeader();

                        echo '<h2>Overview</h2>';

			$this->wpstorecart_main_dashboard_widget_function();

                        echo '
<table >
	<caption>This Week In Sales</caption>
	<thead>
		<tr>
			<td></td>
			<th scope="col">'.date("D", strtotime("5 days ago")).'</th>
			<th scope="col">'.date("D", strtotime("4 days ago")).'</th>
			<th scope="col">'.date("D", strtotime("3 days ago")).'</th>
			<th scope="col">'.date("D", strtotime("2 days ago")).'</th>
			<th scope="col">'.date("D", strtotime("yesterday")).'</th>
			<th scope="col">Today ('.date("D", strtotime("now")).')</th>
		</tr>
	</thead>
	<tbody>

		<tr>
			<th scope="row">Sales</th>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("5 days ago"))).'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("4 days ago"))).'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("3 days ago"))).'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("2 days ago"))).'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("yesterday"))).'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("now"))).'</td>
		</tr>
		<tr>
			<th scope="row">Add To Cart</th>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("5 days ago")), 'cart').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("4 days ago")), 'cart').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("3 days ago")), 'cart').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("2 days ago")), 'cart').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("yesterday")), 'cart').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("now")), 'cart').'</td>
		</tr>
		<tr>
			<th scope="row">Product Views</th>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("5 days ago")), 'views').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("4 days ago")), 'views').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("3 days ago")), 'views').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("2 days ago")), 'views').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("yesterday")), 'views').'</td>
			<td>'.$this->numberOfSales(date("Ymd", strtotime("now")), 'views').'</td>
		</tr>

	</tbody>
</table>

';

		
		}
		// ==========================================================================================================
		
	function numberOfSales($dateToLookup, $typeToLookup = 'sales') {
            global $wpdb;
            $output = 0;
            $grabrecord = NULL;
            if($typeToLookup=='sales') {
                $table_name = $wpdb->prefix . "wpstorecart_orders";
                $grabrecord = "SELECT COUNT(primkey) AS `num` FROM `{$table_name}` WHERE `orderstatus`='Completed' AND `date`='$dateToLookup';";
                $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                foreach($results as $result) {
                    if(!isset($result['num'])) {
                        $output = 0;
                    } else {
                        $output = $result['num'];
                    }
                }
            }
            if($typeToLookup=='cart') {
                $table_name = $wpdb->prefix . "wpstorecart_log";
                $grabrecord = "SELECT COUNT(primkey) AS `num` FROM `{$table_name}` WHERE `action`='addtocart' AND `date`='$dateToLookup';";
                $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                foreach($results as $result) {
                    if(!isset($result['num'])) {
                        $output = 0;
                    } else {
                        $output = $result['num'];
                    }
                }
            }
            if($typeToLookup=='views') {
                $table_name = $wpdb->prefix . "wpstorecart_log";
                $grabrecord = "SELECT COUNT(primkey) AS `num` FROM `{$table_name}` WHERE `action`='productview' AND `date`='$dateToLookup';";
                $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                foreach($results as $result) {
                    if(!isset($result['num'])) {
                        $output = 0;
                    } else {
                        $output = $result['num'];
                    }
                }
            }

            

            return $output;
        }


		//Prints out the Affiliate admin page =======================================================================
        function printAdminPageAffiliates() {
			global $wpdb;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}


                        $this->spHeader();
		
			require_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.php');
		
		}
		// ==========================================================================================================
		

		//Prints out the Help admin page =======================================================================
        function printAdminPageHelp() {
			global $wpdb;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			$devOptions = $this->getAdminOptions();
			
			

		
			$this->spHeader();
			
                        echo '
                        <script type="text/javascript">
                        //<![CDATA[
                        window.open(\'http://wpstorecart.com/help-support/\',\'_newtab\');
                        window.location = "admin.php?page=wpstorecart-admin";
                        //]]>
                        </script>';
			
		
		}
		// ==========================================================================================================
		
		
		
		// Dashboard widget code=======================================================================
		function wpstorecart_main_dashboard_widget_function() {
			global $wpdb, $wpstorecart_version;

                        /*
                         * @todo Add the ability to specify user permission levels for dashboard.  This is not a priority, but more of an after thought
                         */
			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) { // Remove the main dashboard widget from end users
				exit();
			}
                        
			$devOptions = $this->getAdminOptions();
			
			$table_name = $wpdb->prefix . "wpstorecart_products";
			$table_name_orders = $wpdb->prefix . "wpstorecart_orders";
			
			$totalrecordssql = "SELECT COUNT(`primkey`) AS num FROM `{$table_name}`";
			$totalrecordsres = $wpdb->get_results( $totalrecordssql , ARRAY_A );
			if(isset($totalrecordsres)) {
				$totalrecords = $totalrecordsres[0]['num'];		
			} else {
				$totalrecords = 0;
			}
			
			$totalrecordssqlorder = "SELECT COUNT(`primkey`) AS num FROM `{$table_name_orders}`";
			$totalrecordsresorder = $wpdb->get_results( $totalrecordssqlorder , ARRAY_A );
			if(isset($totalrecordsresorder)) {
				$totalrecordsorder = $totalrecordsresorder[0]['num'];		
			} else {
				$totalrecordsorder = 0;
			}			
			
			$totalrecordssqlordercompleted = "SELECT COUNT(`primkey`) AS num FROM `{$table_name_orders}` WHERE `orderstatus`='Completed';";
			$totalrecordsresordercompleted = $wpdb->get_results( $totalrecordssqlordercompleted , ARRAY_A );
			if(isset($totalrecordsresordercompleted)) {
				$totalrecordsordercompleted = $totalrecordsresordercompleted[0]['num'];		
			} else {
				$totalrecordsordercompleted = 0;
			}						
			
			$permalink = get_permalink( $devOptions['mainpage'] );
			
			$orderpercentage = @round($totalrecordsordercompleted / $totalrecordsorder * 100);

                        $startdate =date("Ymd", strtotime("30 days ago"));
                        $enddate = date("Ymd");

                        $theSQL = "SELECT SUM(`price`) AS `thetotal` FROM `{$table_name_orders}` WHERE `orderstatus`='Completed';";
			$salesAllTime = $wpdb->get_results( $theSQL , ARRAY_A );
                        $allTimeGrossRevenue = 0;
                        foreach ($salesAllTime as $sat) {
                            $allTimeGrossRevenue = $sat['thetotal'];
                        }

                        $theSQL = "SELECT `date`, `price` FROM `{$table_name_orders}` WHERE `date` > {$startdate} AND `date` <= {$enddate} AND `orderstatus`='Completed' ORDER BY `date` DESC;";
			$salesThisMonth = $wpdb->get_results( $theSQL , ARRAY_A );
                        $currentDay = $enddate;
                        $dayAgo = 0 ;
                        $highestNumber = 0;
						$totalearned = 0;
                        while($currentDay != $startdate) {
                            $salesOnDay[$currentDay] = 0;
                            foreach($salesThisMonth as $currentSale) {
                                if($currentDay == $currentSale['date']) {
                                    $salesOnDay[$currentDay] = $salesOnDay[$currentDay] + 1;
                                    $totalearned = $totalearned + $currentSale['price'];
                                }
                            }
                            if($salesOnDay[$currentDay] > $highestNumber) {
                                $highestNumber = $salesOnDay[$currentDay];
                            }
                            $dayAgo++;
                            $currentDay = date("Ymd", strtotime("{$dayAgo} days ago"));
							
                        }
                        $dayAgo = 29 ;
                        $currentDay = $startdate;

			// inlinebar
			// 
			$lastrecordssql = "SELECT * FROM `{$table_name_orders}` ORDER BY `date` DESC LIMIT 0, 30";
			$lastrecords = $wpdb->get_results( $lastrecordssql , ARRAY_A );
			
			echo '<ul>';
                        echo '<li><u><span style="font-size:115%;"><strong>wpStoreCart v'.$wpstorecart_version.' :</strong></span> <a href="'.$permalink.'" target="_blank">'.get_bloginfo('name').'</a> with '.$totalrecords.' product(s).</u></li>';
                        echo '<li><strong>Gross Revenue last 30 days: <span style="font-size:170%;">'.$devOptions['currency_symbol'].number_format($totalearned).$devOptions['currency_symbol_right'].'</span></strong></li>';
                        echo '<li><strong>All Time Gross Revenue: <span style="font-size:170%;">'.$devOptions['currency_symbol'].number_format($allTimeGrossRevenue).$devOptions['currency_symbol_right'].'</span></strong></li>';
                        echo "<li><span style=\"float:left;padding:0 10px 0 0;border-right:1px #CCC solid;\"><strong>Completed Orders / Total:</strong>  {$totalrecordsordercompleted}/{$totalrecordsorder} ({$orderpercentage}%) <br /><img src=\"http://chart.apis.google.com/chart?chs=200x50&cht=p3&chco=224499,BBCCED&chd=s:Uf&chdl=$totalrecordsordercompleted|$totalrecordsorder\"></span> </li>";
			echo "<li><span style=\"float:left;padding:0 0 0 10px;\"><strong>Sales last 30 days:</strong> <br /><img src=\"http://chart.apis.google.com/chart?chxt=y&chbh=a,2&chs=200x50&cht=bvg&chco=224499&chds=0,{$highestNumber}&chd=t:0";while($currentDay != $enddate) {echo $salesOnDay[$currentDay].',';$dayAgo--;$currentDay = date("Ymd", strtotime("{$dayAgo} days ago"));} echo"0\" alt=\"\" /></span><div style=\"clear:both;\"></div></li>";
        		echo '</ul>';
		} 
		
		// Create the function use in the action hook
		function wpstorecart_main_add_dashboard_widgets() {
			wp_add_dashboard_widget('wpstorecart_main_dashboard_widgets', 'wpStoreCart Overview', array(&$this, 'wpstorecart_main_dashboard_widget_function'));	
		} 
		
		
		
		function  addHeaderCode() {
                        
			//echo '<!-- wpStoreCart BEGIN -->';
			wp_enqueue_script('wpsc', WP_PLUGIN_URL.'/wpstorecart/php/wpsc-1.1/wpsc/wpsc-javascript.php', array('jquery'),'1.3.2' );
                    

                        $devOptions = $this->getAdminOptions();

                        if($devOptions['wpscjQueryUITheme']!='') {
                            $myStyleUrl = WP_PLUGIN_URL . '/jqueryui/css/'.$devOptions['wpscjQueryUITheme'].'/jquery-ui-1.8.7.custom.css';
                            $myStyleFile = WP_PLUGIN_DIR . '/jqueryui/css/'.$devOptions['wpscjQueryUITheme'].'/jquery-ui-1.8.7.custom.css';
                            if ( file_exists($myStyleFile) ) {
                                wp_register_style('myStyleSheets', $myStyleUrl);
                                wp_enqueue_style( 'myStyleSheets');
                            }

                            wp_enqueue_script('jqueryui-new', WP_PLUGIN_URL.'/wpstorecart/jqueryui/js/jquery-ui-1.8.7.custom.min.js', array('jquery'),'1.3.2' );

                            echo '
                            <script type="text/javascript">
                            //<![CDATA[
                                    jQuery(document).ready(function($) {
                                            $(".wpsc-button").button();
                                            $(".wpsc-button").hover(
                                                    function(){
                                                            $(this).addClass("ui-state-hover");
                                                    },
                                                    function(){
                                                            $(this).removeClass("ui-state-hover");
                                                    }
                                            );

                                    });
                            //]]>
                            </script>
                                ';

                        }

			//echo '<!-- wpStoreCart END -->';
        }

        function addFooterCode(){
                        global $is_checkout, $cart, $wpscCarthasBeenCalled, $wpsc;

                        $output = '';

                        if($wpscCarthasBeenCalled==false) {
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
                            if(!isset($_SESSION)) {
                                    @session_start();

                            }
                            if(@!is_object($cart)) {
                                $cart =& $_SESSION['wpsc'];
                                if(@!is_object($cart)) {
                                    $cart = new wpsc();
                                }
                            }
                            $old_checkout = $is_checkout;
                            $is_checkout = false;
                            $output= $cart->display_cart($wpsc, true);
                            $is_checkout = $old_checkout;
                            $wpscCarthasBeenCalled = true;
                        }

                        return $output;
        }

		
		function  addContent($content = '') {
            $content .= "<p>wpStoreCart</p>";
            return $content;
        }
				

		// Installation ==============================================================================================		
		function wpstorecart_install() {
		   global $wpdb;
		   global $wpstorecart_db_version;

		   $table_name = $wpdb->prefix . "wpstorecart_products";
		   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			  
			$sql = "
				CREATE TABLE {$table_name} (
				`primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
				`name` VARCHAR(512) NOT NULL, 
				`introdescription` TEXT NOT NULL, 
				`description` TEXT NOT NULL, 
				`thumbnail` VARCHAR(512) NOT NULL, 
				`price` DECIMAL(9,2) NOT NULL, 
				`shipping` DECIMAL(9,2) NOT NULL, 
				`download` VARCHAR(512) NOT NULL, 
				`tags` TEXT NOT NULL, `category` INT NOT NULL, 
				`inventory` INT NOT NULL,
				`dateadded` INT( 8 ) NOT NULL,
				`postid` INT NOT NULL,
				`timesviewed` INT NOT NULL,  
				`timesaddedtocart` INT NOT NULL, 
				`timespurchased` INT NOT NULL,
                                `useinventory` BOOL NOT NULL DEFAULT '1'
				);			
				";
			  

			  require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			  dbDelta($sql);
			}

		   $table_name = $wpdb->prefix . "wpstorecart_coupons";
		   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			  
			$sql = "
				CREATE TABLE `{$table_name}` (
				`primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
				`code` VARCHAR(128) NOT NULL, 
				`amount` DECIMAL(8,2) NOT NULL, 
				`percent` INT(3) NOT NULL, 
				`description` VARCHAR(512) NOT NULL, 
				`product` INT NOT NULL,
				`startdate` INT(8) NOT NULL,
				`enddate` INT(8) NOT NULL
				);		
				";
			  

			  dbDelta($sql);
		   }
		   
		   $table_name = $wpdb->prefix . "wpstorecart_orders";
		   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			  
			$sql = "
				CREATE TABLE `{$table_name}` (
				`primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
				`orderstatus` VARCHAR(64) NOT NULL, 
				`cartcontents` VARCHAR(255) NOT NULL, 
				`paymentprocessor` VARCHAR(128) NOT NULL, 
				`price` DECIMAL(8,2) NOT NULL, 
				`shipping` DECIMAL(8,2) NOT NULL, 
				`wpuser` INT NOT NULL, 
				`email` VARCHAR(255) NOT NULL, 
				`affiliate` INT NOT NULL,
				`date` INT( 8 ) NOT NULL);
				";
			  

			  dbDelta($sql);
		   }	


		   $table_name = $wpdb->prefix . "wpstorecart_categories";
		   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			  
			$sql = "
				CREATE TABLE `{$table_name}` (
				`primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
				`parent` INT NOT NULL ,
				`category` VARCHAR( 255 ) NOT NULL,
                                `thumbnail` VARCHAR( 512 ) NOT NULL,
                                `description` TEXT NOT NULL,
                                `postid` INT NOT NULL
                                );
				";
			  

			  dbDelta($sql);
		   }		   

		   $table_name = $wpdb->prefix . "wpstorecart_log";
		   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
			  
			$sql = "
                                CREATE TABLE `{$table_name}` (
                                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                                `action` VARCHAR( 32 ) NOT NULL ,
                                `data` VARCHAR( 255 ) NOT NULL ,
                                `foreignkey` INT NOT NULL ,
                                `date` INT( 8 ) NOT NULL ,
                                `userid` INT NOT NULL
                                );		
				";
			  

			  dbDelta($sql);
		   }

		   
		}
		// END Installation ==============================================================================================
				

		// Shortcode =========================================
		function wpstorecart_mainshortcode($atts, $content = null) {
			global $wpdb, $cart, $wpsc, $is_checkout, $current_user;

                        $statset = false;
			$table_name = $wpdb->prefix . "wpstorecart_products";
		
			$devOptions = $this->getAdminOptions();		
                         wp_get_current_user();
	
			extract(shortcode_atts(array(
				'display' => NULL,
				'primkey' => '0',
				'quantity' => '10',
				'usetext' => 'true',
				'usepictures' => 'false',
                                'thecategory' => '',
			), $atts));

                        // Adds this shortcode: [wpstorecart display="orders"]
                        if ($display=='orders') {
                            $display = NULL;
                            $_GET['wpsc']='orders';
                        }

                        // Adds this shortcode: [wpstorecart display="affiliate"]
                        if ($display=='affiliate') {
                            $display = NULL;
                            $_GET['wpsc']='affiliate';
                        }

                        // Lists the products in a category
                        if (@isset($_GET['wpsc'])) {
                            if($_GET['wpsc']=='lc' && @is_numeric($_GET['wpsccat'])){
                                $display = 'categories';
                                $thecategory = $_GET['wpsccat'];
                            }
                        }

			$output = '';
			switch ($display) {
				case 'haspurchased': // Categories shortcode =========================================================
                                        if ( 0 == $current_user->ID ) {
                                            // Not logged in.
                                        } else {
                                            $table_name99 = $wpdb->prefix . "wpstorecart_orders";
                                            $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name99}` WHERE `wpuser`={$current_user->ID};";
                                            $results = $wpdb->get_results( $sql , ARRAY_A );
                                            if(isset($results)) {
                                                foreach($results as $result) {
                                                    $specific_items = explode(",", $result['cartcontents']);
                                                    foreach($specific_items as $specific_item) {
                                                        if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                                                            $current_item = explode('*', $specific_item);
                                                            if(isset($current_item[0]) && $current_item[0]==$primkey && $result['orderstatus']=='Completed') {
                                                                    $output .= $content;
                                                                    break;
                                                            }
                                                        }
                                                    }
                                                }
                                            }
                                        }
					break;
                                case 'checkout': // Checkout shortcode =========================================================
					$is_checkout = true;

                                        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
                                        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
                                        if(@!is_object($cart)) {
                                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
                                            if(!isset($_SESSION)) {
                                                    @session_start();
                                            }
                                            $cart =& $_SESSION['wpsc']; if(!is_object($cart)) $cart = new wpsc();
                                        }
					$output .= $cart->display_cart($wpsc);
					break;			
				case 'recentproducts': // Recent product shortcode =========================================================
					$output .= '<div class="wpsc-recent-products">';
                                        if(is_numeric($quantity)){
						$sql = "SELECT * FROM `{$table_name}` ORDER BY `dateadded` DESC LIMIT 0, {$quantity};";
						$results = $wpdb->get_results( $sql , ARRAY_A );
						if(isset($results)) {
							foreach ($results as $result) {
								$permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
								if($usepictures=='true') {
									$output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['name'].'" /></a>';
								}
								if($usetext=='true') {
									$output .= '<p><a href="'.$permalink.'">'.$result['name'].'</a></p>';
								}
							}
						}
					} else {
						$output .= '<div class="wpsc-error">wpStoreCart did not like your recentproducts shortcode!  The quantity field contained non-numeric data. Please fix your page or consult the wpStoreCart documentation for help.</div>';
					}
                                        $output .= '</div>';
					break;
				case 'topproducts': // Top product shortcode =========================================================
					$output .= '<div class="wpsc-top-products">';
                                        if(is_numeric($quantity)){
						$sql = "SELECT * FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT 0, {$quantity};";
						$results = $wpdb->get_results( $sql , ARRAY_A );
						if(isset($results)) {
							foreach ($results as $result) {
								$permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
								if($usepictures=='true') {
									$output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['name'].'" /></a>';
								}
								if($usetext=='true') {
									$output .= '<p><a href="'.$permalink.'">'.$result['name'].'</a></p>';
								}
							}
						}
					} else {
						$output .= '<div class="wpsc-error">wpStoreCart did not like your topproducts shortcode!  The quantity field contained non-numeric data. Please fix your page or consult the wpStoreCart documentation for help.</div>';
					}
                                        $output .= '</div>';
					break;					
				case 'categories': // Categories shortcode =========================================================
					$output .= '<div class="wpsc-by-category">';
                                        if($devOptions['showproductthumbnail']=='true') {
                                            $usepictures='true';
                                            $maxImageWidth = $devOptions['wpStoreCartwidth'];
                                            $maxImageHeight = $devOptions['wpStoreCartheight'];
                                        }
                                        if(is_numeric($quantity) && is_numeric($thecategory)){
						$sql = "SELECT * FROM `{$table_name}` WHERE `category`={$thecategory} ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT 0, {$quantity};";
						$results = $wpdb->get_results( $sql , ARRAY_A );
                                            if(isset($results)) {
                                                    foreach ($results as $result) {
                                                            $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                            if($devOptions['displayType']=='grid'){
                                                                    $output .= '<div class="wpsc-grid wpsc-categories">';
                                                            }
                                                            if($devOptions['displayType']=='list'){
                                                                    $output .= '<div class="wpsc-list wpsc-categories">';
                                                            }
                                                            if($usepictures=='true') {
                                                                    $output .= '<a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$result['thumbnail'].'" alt="'.$result['name'].'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
                                                            }
                                                            if($usetext=='true') {
                                                                    $output .= '<a href="'.$permalink.'"><h1 class="wpsc-h1">'.$result['name'].'</h1></a>';
                                                            }
                                                            if($devOptions['displayintroDesc']=='true'){
                                                                    $output .= '<p>'.$result['introdescription'].'</p>';
                                                            }
                                                            if($devOptions['displayAddToCart']=='true'){
                                                                    $output .= '
                                                                    <form method="post" action="">

                                                                            <input type="hidden" name="my-item-id" value="'.$result['primkey'].'" />
                                                                            <input type="hidden" name="my-item-primkey" value="'.$result['primkey'].'" />
                                                                            <input type="hidden" name="my-item-name" value="'.$result['name'].'" />
                                                                            <input type="hidden" name="my-item-price" value="'.$result['price'].'" />
                                                                            <label class="wpsc-qtylabel">Qty: <input type="text" name="my-item-qty" value="1" size="3" class="wpsc-qty" /></label>

                                                                    ';

                                                                    if($result['useinventory']==0 || ($result['useinventory']==1 && $result['inventory'] > 0) ) {
                                                                        $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart" />';
                                                                    } else {
                                                                        $output .= $devOptions['out_of_stock'];
                                                                    }


                                                                    $output .= '
                                                                    </form>
                                                                    ';
                                                            }

                                                            $output .= '</div>';
                                                    }
                                                    $output .= '<div class="wpsc-clear"></div>';
                                            }
					} else {
						$output .= '<div class="wpsc-error">wpStoreCart did not like your categories shortcode!  The quantity and/or category field contained non-numeric data. Please fix your page or consult the wpStoreCart documentation for help.</div>';
					}
                                        $output .= '</div>';
					break;
				case 'product': // Individual product shortcode =========================================================
					if(isset($primkey) && is_numeric($primkey)) {
						$sql = "SELECT * FROM `{$table_name}` WHERE `primkey`={$primkey};";
						$results = $wpdb->get_results( $sql , ARRAY_A );			
						if(isset($results)) {

							$newTimesViewed = $results[0]['timesviewed'] + 1;
                                                        wp_get_current_user();
                                                        if ( 0 == $current_user->ID ) {
                                                            // Not logged in.
                                                            $theuser = 0;
                                                        } else {
                                                            $theuser = $current_user->ID;
                                                        }
                                                        
							$wpdb->query("UPDATE `{$table_name}` SET `timesviewed` = '{$newTimesViewed}' WHERE `primkey` = {$results[0]['primkey']} LIMIT 1 ;");
                                                        if($primkey == $results[0]['primkey'] && $statset == false) {
                                                            $wpdb->query("INSERT INTO `{$wpdb->prefix}wpstorecart_log` (`primkey` ,`action` ,`data` ,`foreignkey` ,`date` ,`userid`) VALUES (NULL, 'productview', '{$_SERVER['REMOTE_ADDR']}', '{$primkey}', '".date('Ymd')."', '{$theuser}');");
                                                            $statset = true;
                                                        }
							if($devOptions['showproductthumbnail']=='true') {
                                                                if($devOptions['wpStoreCartwidth']!=0 && $devOptions['wpStoreCartheight']!= 0) {
                                                                    $output .= '<img class="wpsc-product-img" src="'.$results[0]['thumbnail'].'" alt="'.$results[0]['name'].'" style="max-width:'.$devOptions['wpStoreCartwidth'].'px;max-height:'.$devOptions['wpStoreCartheight'].'px;" /><br />';
                                                                } else { // If width or height are zero, let's just display the image without css resizing
                                                                    $output .= '<img class="wpsc-product-img" src="'.$results[0]['thumbnail'].'" alt="'.$results[0]['name'].'" /><br />';
                                                                }
							}

                                                        // Product variations
                                                        $table_name30 = $wpdb->prefix . "wpstorecart_meta";
                                                        $grabrecord = "SELECT * FROM `{$table_name30}` WHERE `type`='productvariation' AND `foreignkey`={$primkey};";

                                                        $vresults = $wpdb->get_results( $grabrecord , ARRAY_A );

                                                        if(isset($vresults)) {
                                                            $voutput = NULL;
                                                            $variationStorage = array();
                                                            $varStorageCounter = 0;
                                                            foreach ($vresults as $vresult) {
                                                                $theKey = $vresult['primkey'];
                                                                $exploder = explode('||', $vresult['value']);
                                                                $variationStorage[$varStorageCounter]['variationkey'] = $theKey;
                                                                $variationStorage[$varStorageCounter]['variationname'] = $exploder[0];
                                                                $variationStorage[$varStorageCounter]['variationvalue'] = $exploder[1];
                                                                $variationStorage[$varStorageCounter]['variationprice'] = $exploder[2];
                                                                $variationStorage[$varStorageCounter]['variationdesc'] = $exploder[3];
                                                                //$voutput .= '<li>'.$exploder[0].' '.$exploder[1].' '.$exploder[2].' '.$exploder[3].'</li>';
                                                                $varStorageCounter++;
                                                            }
                                                        }

                                                        $output .= '
                                                        <script type="text/javascript">
                                                            var alteredPrice = [];var alteredName = [];
                                                            alteredPrice[0] = 0;alteredName[0]="";
                                                            alteredPrice[1] = 0;alteredName[1]="";
                                                            alteredPrice[2] = 0;alteredName[2]="";
                                                            alteredPrice[3] = 0;alteredName[3]="";
                                                            alteredPrice[4] = 0;alteredName[4]="";
                                                            alteredPrice[5] = 0;alteredName[5]="";
                                                            alteredPrice[6] = 0;alteredName[6]="";
                                                            alteredPrice[7] = 0;alteredName[7]="";
                                                            alteredPrice[8] = 0;alteredName[8]="";
                                                            alteredPrice[9] = 0;alteredName[9]="";
                                                            alteredPrice[10] = 0;alteredName[10]="";
                                                            alteredPrice[11] = 0;alteredName[11]="";
                                                            alteredPrice[12] = 0;alteredName[12]="";
                                                            alteredPrice[13] = 0;alteredName[13]="";
                                                        </script>
                                                        ';
                                                        $variationTest = array();
                                                        $variationCounter = 0;
                                                        if(@is_array($variationStorage) && @isset($variationStorage[0])) {
                                                            if(isset($variationStorage)) {
                                                                    foreach ($variationStorage as $variationStorageCycle) {
                                                                        if(@!isset($variationTest[$variationStorageCycle['variationname']])) {
                                                                        $output .= '
                                                                        <script type="text/javascript">
                                                                            alteredPrice['.$variationCounter.'] = 0;
                                                                            alteredName['.$variationCounter.'] = "";

                                                                            function changePrice'.$variationCounter.'(amount) {
                                                                                price = amount.split("||");
                                                                                theprice = parseFloat(price[0]);
                                                                                thename = price[1];
                                                                                thekey = price[2];
                                                                                alteredPrice['.$variationCounter.'] = theprice;
                                                                                alteredName['.$variationCounter.'] = thename;
                                                                                oldAmount = parseFloat('.$results[0]['price'].');
                                                                                newAmount = Math.round((oldAmount + alteredPrice[0] + alteredPrice[1] + alteredPrice[2] + alteredPrice[3] + alteredPrice[4] + alteredPrice[5] + alteredPrice[6] + alteredPrice[7] + alteredPrice[8] + alteredPrice[9] + alteredPrice[10] + alteredPrice[11] + alteredPrice[12] + alteredPrice[13]) *100)/100;
                                                                                newName = alteredName[0] + " " + alteredName[1] + " " + alteredName[2] + " " + alteredName[3] + " " + alteredName[4] + " " + alteredName[5] + " " + alteredName[6] + " " + alteredName[7] + " " + alteredName[8] + " " + alteredName[9] + " " + alteredName[10] + " " + alteredName[11] + " " + alteredName[12] + " " + alteredName[13];
                                                                                jQuery("#list-item-price").replaceWith("<li id=\'list-item-price\'>Price: "+ newAmount.toFixed(2) + "</li>");
                                                                                jQuery("#my-item-price").val(newAmount.toFixed(2));
                                                                                jQuery("#my-item-name").val("'.$results[0]['name'].' - " + newName);
                                                                                jQuery("#my-item-id").val("'.$results[0]['primkey'].'-" + thekey);
                                                                                jQuery("#my-item-primkey").val("'.$results[0]['primkey'].'-" + thekey);
                                                                                
                                                                            }
                                                                        </script>
                                                                        ';
                                                                         $voutput .= '
                                                                            <li>'.$variationStorageCycle['variationname'].' - '.$variationStorageCycle['variationdesc'].'  <select name="variation_'.$variationStorageCycle['variationname'].'" onclick="changePrice'.$variationCounter.'(this.value);" onchange="changePrice'.$variationCounter.'(this.value);">';
                                                                            $variationTest[$variationStorageCycle['variationname']] = true;
                                                                        }
                                                                        if(isset($variationStorage)) {
                                                                                foreach ($variationStorage as $currentVariation) {
                                                                                        if ($currentVariation['variationname']==$variationStorageCycle['variationname']) {
                                                                                            $option = '<option value="'.$currentVariation['variationprice'].'||'.$currentVariation['variationvalue'].'||'.$currentVariation['variationkey'].'"';
                                                                                            $option .='>';
                                                                                            $option .= $currentVariation['variationvalue'] .' ('. $currentVariation['variationprice'].')';
                                                                                            $option .= '</option>';
                                                                                            $voutput .=  $option;
                                                                                        }
                                                                                }
                                                                        }
                                                                        $voutput .=  '
                                                                        </select>   </li>';
                                                                        $variationCounter++;
                                                                    }
                                                                }
                                                        }
                                                        // Product variations

							$output .= '
							<form method="post" action="">
							 
								<input type="hidden" id="my-item-id" name="my-item-id" value="'.$results[0]['primkey'].'" />
								<input type="hidden" id="my-item-primkey" name="my-item-primkey" value="'.$results[0]['primkey'].'" />
								<input type="hidden" id="my-item-name" name="my-item-name" value="'.$results[0]['name'].'" />
								<input type="hidden" id="my-item-price" name="my-item-price" value="'.$results[0]['price'].'" />
                                                                <input type="hidden" id="my-item-variation" name="my-item-variation" value="0" />

								<ul class="wpsc-product-info">
								  <li id="list-item-name"><strong>'.$results[0]['name'].'</strong></li>
								  <li id="list-item-price">Price: '.$results[0]['price'].'</li>
								  <li id="list-item-qty"><label class="wpsc-individualqtylabel">Qty: <input type="text" name="my-item-qty" value="1" size="3"  class="wpsc-individualqty" /></label>					   </li>';

                                                                if($voutput!=NULL) {
                                                                    $output .= $voutput;
                                                                }

                                                        $output .= '
								 </ul>
                                                        ';

                                                        if($results[0]['useinventory']==0 || ($results[0]['useinventory']==1 && $results[0]['inventory'] > 0) ) {
                                                            $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart" />';
                                                        } else {
                                                            $output .= $devOptions['out_of_stock'];
                                                        }

                                                        $output .= '
							</form> 
							';  
							
							if($devOptions['showproductdescription']=='true') {
								$output .= $results[0]['introdescription'] . '&nbsp; &nbsp;';
								$output .= $results[0]['description'];
							}							
							  
						} else {
							$output .= '<div class="wpsc-error">This product has been removed, but the shortcode associated with it was not.</div>';
						}
					} else {
						$output .= 'wpStoreCart did not like the primkey in your shortcode!  The primkey field contained non-numeric data. Please fix your page or consult the wpStoreCart documentation for help.';
					}
					break;
                                default: // Default shortcode
                                    if(!isset($_GET['wpsc'])) {
                                        if($devOptions['frontpageDisplays']=='List all products' || $devOptions['frontpageDisplays']=='List newest products') {
                                            $sql = "SELECT * FROM `{$table_name}` ORDER BY `dateadded` DESC LIMIT 0, {$quantity};";
                                        }
                                        if($devOptions['frontpageDisplays']=='List most popular products') {
                                            $sql = "SELECT * FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT 0, {$quantity};";
                                        }
                                        if($devOptions['frontpageDisplays']=='List all categories') {
                                            $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` DESC LIMIT 0, {$quantity};";
                                            $secondcss = 'wpsc-categories';
                                        } else {
                                            $secondcss = 'wpsc-products';
                                        }
                                        $results = $wpdb->get_results( $sql , ARRAY_A );

                                        if($devOptions['displayThumb']=='true') {
                                            $usepictures='true';
                                            $maxImageWidth = $devOptions['wpStoreCartwidth'];
                                            $maxImageHeight = $devOptions['wpStoreCartheight'];
                                        }
                                        if($devOptions['displayintroDesc']=='true') {
                                            $usetext='true';
                                        }

                                        // If we're dealing with categories, we have different fields to deal with than products.
                                        if($devOptions['frontpageDisplays']=='List all categories') {
                                            if(isset($results)) {
                                                    foreach ($results as $result) {
                                                            if(trim($result['thumbnail']=='')) {
                                                                $result['thumbnail'] = WP_PLUGIN_URL.'/wpstorecart/images/default_product_img.jpg';
                                                            }
                                                            if($result['postid'] == 0 || $result['postid'] == '') { // If there's no dedicated category pages, use the default
                                                                if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                                                                    $permalink = get_permalink($devOptions['mainpage']) .'?wpsc=lc&wpsccat='.$result['primkey'];
                                                                } else {
                                                                    $permalink = get_permalink($devOptions['mainpage']) .'&wpsc=lc&wpsccat='.$result['primkey'];
                                                                }
                                                            } else {
                                                                $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                            }
                                                            if($devOptions['displayType']=='grid'){
                                                                    $output .= '<div class="wpsc-grid '.$secondcss.'">';
                                                            }
                                                            if($devOptions['displayType']=='list'){
                                                                    $output .= '<div class="wpsc-list '.$secondcss.'">';
                                                            }
                                                            if($usepictures=='true' || $result['thumbnail']!='' ) {
                                                                    $output .= '<a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$result['thumbnail'].'" alt="'.$result['category'].'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
                                                            }
                                                            if($usetext=='true') {
                                                                    $output .= '<p><a href="'.$permalink.'">'.$result['category'].'</a></p>';
                                                            }
                                                            if($devOptions['displayintroDesc']=='true'){
                                                                    $output .= '<p>'.$result['description'].'</p>';
                                                            }
                                                            $output .= '</div>';
                                                    }
                                                    $output .= '<div class="wpsc-clear"></div>';
                                            }
                                        } else { // This is for products:
                                            if(isset($results)) {
                                                    foreach ($results as $result) {
                                                            $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                            if($devOptions['displayType']=='grid'){
                                                                    $output .= '<div class="wpsc-grid wpsc-products">';
                                                            }
                                                            if($devOptions['displayType']=='list'){
                                                                    $output .= '<div class="wpsc-list wpsc-products">';
                                                            }
                                                            if($usepictures=='true') {
                                                                    $output .= '<a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$result['thumbnail'].'" alt="'.$result['name'].'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
                                                            }
                                                            if($usetext=='true') {
                                                                    $output .= '<a href="'.$permalink.'"><h1 class="wpsc-h1">'.$result['name'].'</h1></a>';
                                                            }
                                                            if($devOptions['displayintroDesc']=='true'){
                                                                    $output .= '<p>'.$result['introdescription'].'</p>';
                                                            }
                                                            if($devOptions['displayAddToCart']=='true'){
                                                                    $output .= '
                                                                    <form method="post" action="">

                                                                            <input type="hidden" name="my-item-id" value="'.$result['primkey'].'" />
                                                                            <input type="hidden" name="my-item-primkey" value="'.$result['primkey'].'" />
                                                                            <input type="hidden" name="my-item-name" value="'.$result['name'].'" />
                                                                            <input type="hidden" name="my-item-price" value="'.$result['price'].'" />
                                                                            <label class="wpsc-qtylabel">Qty: <input type="text" name="my-item-qty" value="1" size="3" class="wpsc-qty" /></label>

                                                                    ';

                                                                    if($result['useinventory']==0 || ($result['useinventory']==1 && $result['inventory'] > 0) ) {
                                                                        $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart" />';
                                                                    } else {
                                                                        $output .= $devOptions['out_of_stock'];
                                                                    }
                                                            

                                                                    $output .= '
                                                                    </form>
                                                                    ';
                                                            }

                                                            $output .= '</div>';
                                                    }
                                                    $output .= '<div class="wpsc-clear"></div>';
                                            }
                                        }
                                    } else {
                                        if($_GET['wpsc']=='affiliate') {
                                            global $affiliatemanager, $affiliatesettings, $affiliatepurchases;
                                            $affiliatemanager = true;
                                            $affiliatesettings['current_user'] = $current_user->ID;
                                            $affiliatesettings['available_products']  = NULL;
                                            $affiliatesettings['product_urls'] = NULL;
                                            $affiliatesettings['minimumAffiliatePayment'] = $devOptions['minimumAffiliatePayment'];
                                            $affiliatesettings['minimumDaysBeforePaymentEligable'] = $devOptions['minimumDaysBeforePaymentEligable'];
                                            $affiliatesettings['affiliateInstructions'] = $devOptions['affiliateInstructions'];
                                            
                                            $table_name_products = $wpdb->prefix . "wpstorecart_products";
                                            $sql = "SELECT `primkey`, `postid` FROM `{$table_name_products}` ORDER BY `primkey` ASC;";
                                            $results = $wpdb->get_results( $sql , ARRAY_A );
                                            $affiliatesettings['base_url'] = WP_PLUGIN_URL;
                                            if(isset($results)) {
                                                foreach ($results as $result) {
                                                    $affiliatesettings['available_products'] = $affiliatesettings['available_products'] . $result['primkey'] . ',';
                                                    $affiliatesettings['product_urls'] = $affiliatesettings['product_urls']  . urlencode(get_permalink($result['postid'])) . '|Z|Z|Z|';
                                                }
                                                $affiliatesettings['available_products'] = substr($affiliatesettings['available_products'], 0, -1);
                                                $affiliatesettings['product_urls'] = substr($affiliatesettings['product_urls'], 0, -7);
                                            }
                                            $table_name = $wpdb->prefix . "wpstorecart_orders";
                                            $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                                            $sql = "SELECT * FROM `{$table_name}`, `{$table_name_meta}` WHERE  `{$table_name}`.`affiliate`='{$affiliatesettings['current_user']}' AND  `{$table_name}`.`orderstatus`='Completed' AND `{$table_name}`.`primkey`=`{$table_name_meta}`.`foreignkey` ORDER BY  `{$table_name}`.`affiliate`,  `{$table_name}`.`date` DESC;";
                                            $results = $wpdb->get_results( $sql , ARRAY_A );
                                            $icounter = 0;
                                            foreach ($results as $result) {
                                                global $userinfo2;
                                                $affiliatepurchases[$icounter]['cartcontents'] = $this->splitOrderIntoProduct($result['primkey']);
                                                $affiliatepurchases[$icounter]['amountpaid'] = $result['value'];
                                                $affiliatepurchases[$icounter]['primkey'] = $result['primkey'];
                                                $affiliatepurchases[$icounter]['price'] = $result['price'];
                                                $affiliatepurchases[$icounter]['date'] = $result['date'];
                                                $affiliatepurchases[$icounter]['orderstatus'] = $result['orderstatus'];
                                                $userinfo2 = get_userdata($result['affiliate']);
                                                @$affiliatepurchases[$icounter]['affiliateusername'] = $userinfo2->user_login;
                                                $icounter++;
                                            }
                                            @include_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.pro.php');
                                            echo @wpscAffiliates();
                                            $affiliatemanager = false;
                                        }
                                        if($_GET['wpsc']=='orders') {
                                            $output .= 'Your orders';
                                            
                                            if ( 0 == $current_user->ID ) {
                                                // Not logged in.
                                            } else {
                                                // Logged in.
                                                $table_name3 = $wpdb->prefix . "wpstorecart_orders";
                                                $sql = "SELECT * FROM `{$table_name3}` WHERE `wpuser`='{$current_user->ID}' ORDER BY `date` DESC;";
                                                $results = $wpdb->get_results( $sql , ARRAY_A );
                                                if(isset($results)) {
                                                        $output .= '<table><tr><td>Order Status</td><td>Date</td><td>Items</td><td>Total Price</td></tr>';
                                                        foreach ($results as $result) {
                                                            $output .= '<tr><td>'.$result['orderstatus'].'</td><td>'.$result['date'].'</td><td>'.$this->splitOrderIntoProduct($result['primkey'], 'download').'</td><td>'.$result['price'].'</td></tr>';
                                                        }
                                                        $output .= '</table>';
                                                }
                                                
                                                $output .= '<br />';
                                                $output .= 'Username: ' . $current_user->user_login . '<br />';
                                                $output .= 'Email: ' . $current_user->user_email . '<br />';
                                                $output .= 'First name: ' . $current_user->user_firstname . '<br />';
                                                $output .= 'Last name: ' . $current_user->user_lastname . '<br />';
                                                $output .= 'Display name: ' . $current_user->display_name . '<br />';
                                                $output .= 'User ID: ' . $current_user->ID . '<br />';

                                            }
                                        }
                                        if($_GET['wpsc']=='manual') {
                                            $output .= '<h2>Order total: '. $_GET['price'] .'</h2>';
                                            $output .= $devOptions['checkmoneyordertext'];
                                            if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                                                $permalink = get_permalink($devOptions['mainpage']) .'?wpsc=manualresponse&order='.$_GET['order'];
                                            } else {
                                                $permalink = get_permalink($devOptions['mainpage']) .'&wpsc=manualresponse&order='.$_GET['order'];
                                            }
                                            $output .= '<form action="'.$permalink.'" method="post"><textarea class="wpsc-textarea" name="manualresponsetext"></textarea><input type="submit" class="wpsc-button" value="Submit" /> </form>';
                                        }
                                        if($_GET['wpsc']=='manualresponse') {
                                            global $wpstorecart_version;
                                            if(is_numeric($_GET['order'])) {
                                                $orderNumber = intval($_GET['order']);
                                                @$orderText = $wpdb->prepare($_POST['manualresponsetext']);
                                                $table_name3 = $wpdb->prefix . "wpstorecart_orders";
                                                $sql = "SELECT * FROM `{$table_name3}` WHERE `wpuser`='{$current_user->ID}' AND `primkey`={$orderNumber};";
                                                $results = $wpdb->get_results( $sql , ARRAY_A );
                                                if(isset($results)) {
                                                    $table_name3 = $wpdb->prefix . "wpstorecart_meta";
                                                    $sql = "INSERT INTO `{$table_name3}` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '{$orderText}', 'ordernote', '{$orderNumber}');";
                                                    $wpdb->query( $sql );
                                                }
                                                $output .= $this->makeEmailTxt($devOptions['success_text']);
                                                 // Let's send them an email telling them their purchase was successful
                                                 // In case any of our lines are larger than 70 characters, we should use wordwrap()
                                                $message = wordwrap($this->makeEmailTxt($devOptions['emailonapproval']) . $this->makeEmailTxt($devOptions['emailsig']), 70);

                                                $headers = 'From: '.$devOptions['wpStoreCartEmail'] . "\r\n" .
                                                    'Reply-To: ' .$devOptions['wpStoreCartEmail']. "\r\n" .
                                                    'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;

                                                // Send an email when purchase is submitted
                                                @mail($current_user->user_email, 'Your order has been fulfilled!', $message, $headers);

                                                $message = wordwrap("A note was added to a recent order. Here is the contents:<br /> {$orderText}", 70);

                                                $headers = 'From: '.$devOptions['wpStoreCartEmail'] . "\r\n" .
                                                    'Reply-To: ' .$devOptions['wpStoreCartEmail']. "\r\n" .
                                                    'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;

                                                // Send an email when purchase is submitted
                                                @mail($devOptions['wpStoreCartEmail'], 'A note was added to a recent order!', $message, $headers);
                                            }
                                        }
                                        if($_GET['wpsc']=='success') {
                                            $output .= $this->makeEmailTxt($devOptions['success_text']);
                                            if($devOptions['ga_trackingnum']!='' && isset($_POST['item_name'])) {
                                                @$item_name = $_POST['item_name'];
                                                @$business = $_POST['business'];
                                                @$item_number = $_POST['item_number'];
                                                @$payment_status = $_POST['payment_status'];
                                                @$mc_gross = $_POST['mc_gross'];
                                                @$payment_currency = $_POST['mc_currency'];
                                                @$txn_id = $_POST['txn_id'];
                                                @$receiver_email = $_POST['receiver_email'];
                                                @$receiver_id = $_POST['receiver_id'];
                                                @$quantity = $_POST['quantity'];
                                                @$num_cart_items = $_POST['num_cart_items'];
                                                @$payment_date = $_POST['payment_date'];
                                                @$first_name = $_POST['first_name'];
                                                @$last_name = $_POST['last_name'];
                                                @$payment_type = $_POST['payment_type'];
                                                @$payment_status = $_POST['payment_status'];
                                                @$payment_gross = $_POST['payment_gross'];
                                                @$payment_fee = $_POST['payment_fee'];
                                                @$settle_amount = $_POST['settle_amount'];
                                                @$memo = $_POST['memo'];
                                                @$payer_email = $_POST['payer_email'];
                                                @$txn_type = $_POST['txn_type'];
                                                @$payer_status = $_POST['payer_status'];
                                                @$address_street = $_POST['address_street'];
                                                @$address_city = $_POST['address_city'];
                                                @$address_state = $_POST['address_state'];
                                                @$address_zip = $_POST['address_zip'];
                                                @$address_country = $_POST['address_country'];
                                                @$address_status = $_POST['address_status'];
                                                @$item_number = $_POST['item_number'];
                                                @$tax = $_POST['tax'];
                                                @$option_name1 = $_POST['option_name1'];
                                                @$option_selection1 = $_POST['option_selection1'];
                                                @$option_name2 = $_POST['option_name2'];
                                                @$option_selection2 = $_POST['option_selection2'];
                                                @$for_auction = $_POST['for_auction'];
                                                @$invoice = $_POST['invoice'];
                                                @$custom = $_POST['custom'];
                                                @$notify_version = $_POST['notify_version'];
                                                @$verify_sign = $_POST['verify_sign'];
                                                @$payer_business_name = $_POST['payer_business_name'];
                                                @$payer_id =$_POST['payer_id'];
                                                @$mc_currency = $_POST['mc_currency'];
                                                @$mc_fee = $_POST['mc_fee'];
                                                @$exchange_rate = $_POST['exchange_rate'];
                                                @$settle_currency = $_POST['settle_currency'];
                                                @$parent_txn_id = $_POST['parent_txn_id'];
                                                @$pending_reason = $_POST['pending_reason'];
                                                @$reason_code = $_POST['reason_code'];
                                            echo '
                                            <script type="text/javascript">
                                              var gaJsHost = (("https:" == document.location.protocol ) ? "https://ssl." : "http://www.");
                                              document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
                                            </script>';
                                            echo '
                                            <script type="text/javascript">
                                            try{
                                              var pageTracker = _gat._getTracker("'.$devOptions['ga_trackingnum'].'");
                                              pageTracker._trackPageview();
                                              pageTracker._addTrans(
                                                  "'.$custom.'",            // order ID - required
                                                  "'.get_bloginfo('name').'",  // affiliation or store name
                                                  "'.$payment_gross.'",           // total - required
                                                  "0.00",            // tax
                                                  "0.00",           // shipping
                                                  "'.$address_city.'",        // city
                                                  "'.$address_state.'",      // state or province
                                                  "'.$address_country.'"              // country
                                                );


                                               // add item might be called for every item in the shopping cart
                                               // where your ecommerce engine loops through each item in the cart and
                                               // prints out _addItem for each
                                               pageTracker._addItem(
                                                  "'.$custom.'",           // order ID - necessary to associate item with transaction
                                                  "'.$custom.'",           // SKU/code - required
                                                  "'.$item_name.'",        // product name
                                                  "",   // category or variation
                                                  "'.$payment_gross.'",          // unit price - required
                                                  "'.$quantity.'"               // quantity - required
                                               );

                                               pageTracker._trackTrans(); //submits transaction to the Analytics servers
                                            } catch(err) {}
                                            </script>
                                            ';
                                            }
                                        }
                                        if($_GET['wpsc']=='failure') {
                                            $output .= $this->makeEmailTxt($devOptions['failed_text']);
                                        }
                                    }
                                    break;
			}			




			return $output;
		}
		// END SHORTCODE ================================================

		function add_script_swfobject($posts){
			if (empty($posts)) return $posts;
		 
			wp_enqueue_script('swfobject');

			return $posts;
		}		

                function my_import_scripts() {
                    wp_enqueue_script('swfupload');
                }

		function my_mainpage_scripts() {
			global $APjavascriptQueue;

                        /*
			wp_enqueue_script('sparkline',WP_PLUGIN_URL . '/wpstorecart/js/jquery.sparkline.min.js',array('jquery'),'1.4' );
		
			$APjavascriptQueue .= '
			<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready(function($) {
					$(".inlinepie").sparkline("html", {type: "pie", width: "16px", height:"16px"} );
					$(".inlinepie").sparkline(); 				
					$(".inlinebar").sparkline("html", {type: "bar", barColor: "red", width: "10px", height:"16px"} );
					$(".inlinebar").sparkline(); 
					
				});
			//]]>
			</script>					
			';
                        */
                        
                        $APjavascriptQueue .= '
                            <link href="'.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/charting/css/basic.css" type="text/css" rel="stylesheet" />
	<script type="text/javascript" src="'.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/_shared/EnhanceJS/enhance.js"></script>
	<script type="text/javascript">
		// Run capabilities test
var $ = jQuery.noConflict();
$(document).ready(function() {
		enhance({
			loadScripts: [
				\''.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/charting/js/excanvas.js\',
				\''.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/charting/js/visualize.jQuery.js\',
				\''.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/charting/js/example.js\'
			],
			loadStyles: [
				\''.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/charting/css/visualize.css\',
				\''.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/charting/css/visualize-light.css\'
			]
		});
});
    </script>
                        ';

		}		
		
		function my_tooltip_script() {
			global $APjavascriptQueue;
		
			wp_enqueue_script('ezpz_tooltip',WP_PLUGIN_URL . '/wpstorecart/js/jquery.ezpz_tooltip.js',array('jquery'),'1.4' );

			$APjavascriptQueue .= '
			<style type="text/css">
				.tooltip-content {
					display: none;        /* required */
					position: absolute;   /* required */
					padding: 30px 10px 10px 10px;
					border: 1px solid black;
					background-color: white;
					max-width:345px;
					background: #FFFFFF url(\''.WP_PLUGIN_URL . '/wpstorecart/images/tooltip001.jpg\') top left no-repeat;
                                        z-index:999999;
				}			
			</style>
			
			<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready(function($) {
					$(".tooltip-target").ezpz_tooltip();
				});
			//]]>
			</script>
			';
			
		}


		function admin_script_anytime() {
			global $APjavascriptQueue;
		
			wp_enqueue_script('ezpz_tooltip',WP_PLUGIN_URL . '/wpstorecart/js/jquery.ezpz_tooltip.js',array('jquery'),'1.4' );
			wp_enqueue_script('anytime',WP_PLUGIN_URL . '/wpstorecart/js/anytime/anytimec.js',array('jquery'),'1.4' );
		
			$APjavascriptQueue .= '<link type="text/css" rel="stylesheet" href="' . WP_PLUGIN_URL . '/wpstorecart/js/anytime/anytimec.css" />
			<style type="text/css">
				.tooltip-content {
					display: none;        /* required */
					position: absolute;   /* required */
					padding: 30px 10px 10px 10px;
					border: 1px solid black;
					background-color: white;
					width:345px;
					min-width:345px;
					max-width:345px;
					background: #FFFFFF url(\''.WP_PLUGIN_URL . '/wpstorecart/images/tooltip001.jpg\') top left no-repeat;
                                        z-index:999999;
				}			
			</style>
			
			<script type="text/javascript">
			//<![CDATA[
				jQuery(document).ready(function($) {
					$(".tooltip-target").ezpz_tooltip();
				});
			//]]>
			</script>
			';
			
		}

                function enqueue_my_styles() {
                    $devOptions = $this->getAdminOptions();

                    if($devOptions['wpscCss']!='') {
                        $myStyleUrl = WP_PLUGIN_URL . '/wpstorecart/themes/'.$devOptions['wpscCss'];
                        $myStyleFile = WP_PLUGIN_DIR . '/wpstorecart/themes/'.$devOptions['wpscCss'];
                        if ( file_exists($myStyleFile) ) {
                            wp_register_style('myStyleSheets', $myStyleUrl);
                            wp_enqueue_style( 'myStyleSheets');
                        }
                    }

                    if($devOptions['wpscjQueryUITheme']!='') {
                        $myStyleUrljQUI = WP_PLUGIN_URL . '/wpstorecart/jqueryui/css/'.$devOptions['wpscjQueryUITheme'].'/jquery-ui-1.8.7.custom.css';
                        $myStyleFilejQUI = WP_PLUGIN_DIR . '/wpstorecart/jqueryui/css/'.$devOptions['wpscjQueryUITheme'].'/jquery-ui-1.8.7.custom.css';
                        if ( file_exists($myStyleFilejQUI) ) {
                            wp_register_style('myStyleSheetsjQUI', $myStyleUrljQUI);
                            wp_enqueue_style( 'myStyleSheetsjQUI');
                        }
                    }

                }

		function my_admin_scripts(){
			global $APjavascriptQueue;
		 
			wp_tiny_mce( false , // true makes the editor "teeny"
				array(
					"editor_selector" => "wpStoreCartproduct_description"
				)
			);		 
		 
			wp_enqueue_script('swfupload');
			wp_enqueue_script('ezpz_tooltip',WP_PLUGIN_URL . '/wpstorecart/js/jquery.ezpz_tooltip.js',array('jquery'),'1.4' );
			

			if (session_id() == "") {@session_start();};
			
			$APjavascriptQueue = '';
			
			$APjavascriptQueue .= '
			<style type="text/css">
				.tooltip-content {
					display: none;        /* required */
					position: absolute;   /* required */
					padding: 30px 10px 10px 10px;
					border: 1px solid black;
					background-color: white;
					width:345px;
					min-width:345px;
					max-width:345px;
					background: #FFFFFF url(\''.WP_PLUGIN_URL . '/wpstorecart/images/tooltip001.jpg\') top left no-repeat;
                                        z-index:999999;
				}

                            #upload-progressbar-container, #upload-progressbar-container2, #upload-progressbar-container3 {
                                min-width:200px;
                                max-width:200px;
                                min-height:20px;
                                max-height:20px;
                                background-color:#FFF;
                                display:block;
                            }
                            #upload-progressbar, #upload-progressbar2, #upload-progressbar3 {
                                min-height:20px;
                                max-height:20px;
                                background-color:#6ba6ff;
                                width:0px;
                                display:none;
                                border:1px solid #1156be;
                            }

			</style>
			
			<script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function($) {
				$(".tooltip-target").ezpz_tooltip();
			});
			
			
			var productUploadStartEventHandler = function (file) { 
				var continue_with_upload; 
				
				continue_with_upload = true; 

				return continue_with_upload; 
			}; 

			var productUploadSuccessEventHandler = function (file, server_data, receivedResponse) {
                                jQuery("#uploadimage1").attr("src","'.WP_PLUGIN_URL.'/wpstorecart/images/white.gif");
				document.wpstorecartaddproductform.wpStoreCartproduct_download.value = document.wpstorecartaddproductform.wpStoreCartproduct_download.value + file.name + "||";
                                this.startUpload();
			}; 
			
			var productUploadSuccessEventHandler2 = function (file, server_data, receivedResponse) {
                                jQuery("#uploadimage2").attr("src","'.WP_PLUGIN_URL.'/wpstorecart/images/white.gif");
				document.wpstorecartaddproductform.wpStoreCartproduct_thumbnail.value = "'.WP_CONTENT_URL.'/uploads/wpstorecart/" + file.name;
                                this.startUpload();
			}; 			

			var productUploadSuccessEventHandler3 = function (file, server_data, receivedResponse) {
                                jQuery("#uploadimage3").attr("src","'.WP_PLUGIN_URL.'/wpstorecart/images/white.gif");
				document.wpstorecartaddproductform.wpStoreCartproduct_variation.value = document.wpstorecartaddproductform.wpStoreCartproduct_variation.value + file.name + "****";
                                this.startUpload();
			};

			function uploadError(file, errorCode, message) {
				try {

					switch (errorCode) {
					case SWFUpload.UPLOAD_ERROR.HTTP_ERROR:
						alert("Error Code: HTTP Error, File name. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.MISSING_UPLOAD_URL:
						alert("Error Code: No backend file. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_FAILED:
						alert("Error Code: Upload Failed. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.IO_ERROR:
						alert("Error Code: IO Error. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.SECURITY_ERROR:
						alert("Error Code: Security Error. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_LIMIT_EXCEEDED:
						alert("Error Code: Upload Limit Exceeded. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.SPECIFIED_FILE_ID_NOT_FOUND:
						alert("Error Code: The file was not found. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.FILE_VALIDATION_FAILED:
						alert("Error Code: File Validation Failed. Message: " + message);
						break;
					case SWFUpload.UPLOAD_ERROR.FILE_CANCELLED:
						break;
					case SWFUpload.UPLOAD_ERROR.UPLOAD_STOPPED:
						break;
					default:
						alert("Error Code: " + errorCode + ". Message: " + message);
						break;
					}
				} catch (ex) {
					this.debug(ex);
				}
			}

                        function uploadProgress(file, bytesLoaded, bytesTotal) {
                            try {
                                var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
                                jQuery("#upload-progressbar").css("display", "block");
                                jQuery("#upload-progressbar").css("width", percent+"%");
                                jQuery("#upload-progressbar").html("<center>"+ percent+"%</center>");
                            } catch (e) {
                            }
                        }

                        function uploadProgress2(file, bytesLoaded, bytesTotal) {
                            try {
                                var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
                                jQuery("#upload-progressbar2").css("display", "block");
                                jQuery("#upload-progressbar2").css("width", percent+"%");
                                jQuery("#upload-progressbar2").html("<center>"+ percent+"%</center>");
                            } catch (e) {
                            }
                        }

                        function uploadProgress3(file, bytesLoaded, bytesTotal) {
                            try {
                                var percent = Math.ceil((bytesLoaded / bytesTotal) * 100);
                                jQuery("#upload-progressbar3").css("display", "block");
                                jQuery("#upload-progressbar3").css("width", percent+"%");
                                jQuery("#upload-progressbar3").html("<center>"+ percent+"%</center>");
                            } catch (e) {
                            }
                        }

			function beginTheUpload(selected, addtoqueue, inqueuealready) {
				this.startUpload();
			}
			
			function debugSWFUpload (message) {
				try {
					if (window.console && typeof(window.console.error) === "function" && typeof(window.console.log) === "function") {
						if (typeof(message) === "object" && typeof(message.name) === "string" && typeof(message.message) === "string") {
							window.console.error(message);
						} else {
							window.console.log(message);
						}
					}
				} catch (ex) {
				}
				try {
					if (this.settings.debug) {
						this.debugMessage(message);
					}
				} catch (ex1) {
				}
			}
			
			var swfu; 
			var swfu2;
			window.onload = function () { 
				var settings_object = { 
					upload_url : "'.WP_PLUGIN_URL.'/wpstorecart/php/upload.php", 
					post_params: {"PHPSESSID" : "'.session_id().'"},
					flash_url : "'.get_option( 'siteurl' ).'/wp-includes/js/swfupload/swfupload.swf", 
					file_size_limit : "2048 MB",
					file_types : "*.*",
					file_types_description : "Any file type",
					file_upload_limit : "0",
					file_post_name: "Filedata",					
					button_placeholder_id : "spanSWFUploadButton",
					button_image_url : "'.WP_PLUGIN_URL.'/wpstorecart/images/XPButtonUploadText_61x22.png",
					button_width: 61,
					button_height: 22,
					debug : false, 
					debug_handler : debugSWFUpload,
					file_dialog_complete_handler: beginTheUpload,
                                        upload_progress_handler: uploadProgress,
					upload_start_handler : productUploadStartEventHandler, 
					upload_success_handler : productUploadSuccessEventHandler,
					upload_error_handler : uploadError
				}; 
				
				var settings_object2 = { 
					upload_url : "'.WP_PLUGIN_URL.'/wpstorecart/php/upload.php", 
					post_params: {"PHPSESSID" : "'.session_id().'"},
					flash_url : "'.get_option( 'siteurl' ).'/wp-includes/js/swfupload/swfupload.swf", 
					file_size_limit : "9999 MB",
					file_types : "*.jpg;*.gif;*.png;*.jpeg;*.bmp;*.tiff;",
					file_types_description : "Image files",
					file_upload_limit : "0",
					file_post_name: "Filedata",					
					button_placeholder_id : "spanSWFUploadButton2",
					button_image_url : "'.WP_PLUGIN_URL.'/wpstorecart/images/XPButtonUploadText_61x22.png",
					button_width: 61,
					button_height: 22,
					debug : false, 
					debug_handler : debugSWFUpload,
					file_dialog_complete_handler: beginTheUpload,
                                        upload_progress_handler: uploadProgress2,
					upload_start_handler : productUploadStartEventHandler, 
					upload_success_handler : productUploadSuccessEventHandler2,
					upload_error_handler : uploadError
				}; 				

				var settings_object3 = {
					upload_url : "'.WP_PLUGIN_URL.'/wpstorecart/php/upload.php",
					post_params: {"PHPSESSID" : "'.session_id().'"},
					flash_url : "'.get_option( 'siteurl' ).'/wp-includes/js/swfupload/swfupload.swf",
					file_size_limit : "2048 MB",
					file_types : "*.*",
					file_types_description : "Any file type",
					file_upload_limit : "0",
					file_post_name: "Filedata",
					button_placeholder_id : "spanSWFUploadButton3",
					button_image_url : "'.WP_PLUGIN_URL.'/wpstorecart/images/XPButtonUploadText_61x22.png",
					button_width: 61,
					button_height: 22,
					debug : false,
					debug_handler : debugSWFUpload,
					file_dialog_complete_handler: beginTheUpload,
                                        upload_progress_handler: uploadProgress3,
					upload_start_handler : productUploadStartEventHandler,
					upload_success_handler : productUploadSuccessEventHandler3,
					upload_error_handler : uploadError
				};

				swfu = new SWFUpload(settings_object); 
				swfu2 = new SWFUpload(settings_object2);
                                swfu3 = new SWFUpload(settings_object3);
			};


			//]]>
			</script>			
			';

		}			
				
		function placeAdminHeaderCode() {
			global $APjavascriptQueue;

                        wp_enqueue_script('ezpz_tooltip',WP_PLUGIN_URL . '/wpstorecart/js/jquery.ezpz_tooltip.js',array('jquery'),'1.4' );

                        $APjavascriptQueue .= '
                        <style type="text/css">
                        /* menu styles */
                        #jsddm
                        {	margin: 0;
                                padding: 0
                                position:relative;
                                z-index:99999;
                                font: 12px Tahoma, Arial
                        }

                                #jsddm li
                                {	float: left;
                                        list-style: none;
                                        
                                position:relative;
                                z-index:99999;
}

                                #jsddm li a
                                {	display: block;
                                        background: #FFF url("'.get_option( 'siteurl' ).'/wp-admin/images/gray-grad.png") repeat;
                                        padding: 5px 12px;
                                        text-decoration: none;
                                        border-right: 1px solid white;
                                        color: #000;
                                        white-space: nowrap;
                                position:relative;
                                z-index:99999;
                                }



                                .tab {
                                 border-top:1px solid #999;
                                border-bottom:1px solid #999;
                                }

                                #jsddm li a:hover
                                {	background: #787878}

                                        #jsddm li ul
                                        {	margin: 0;
                                                padding: 0;
                                                position: absolute;
                                                visibility: hidden;
                                                border-top: 1px solid white
                                                z-index:99999;
                                        }

                                                #jsddm li ul li
                                                {	float: none;
                                                        display: inline}

                                                #jsddm li ul li a
                                                {	width: auto;
                                                        background: #A9C251;
                                                        color: #24313C}

                                                #jsddm li ul li a:hover
                                                {	background: #8EA344}
                        </style>
                        <script type="text/javascript">
                            var timeout    = 1000;
                            var closetimer = 0;
                            var ddmenuitem = 0;

                            function jsddm_open()
                            {  jsddm_canceltimer();
                               jsddm_close();
                               ddmenuitem = jQuery(this).find(\'ul\').css(\'visibility\', \'visible\');}

                            function jsddm_close()
                            {  if(ddmenuitem) ddmenuitem.css(\'visibility\', \'hidden\');}

                            function jsddm_timer()
                            {  closetimer = window.setTimeout(jsddm_close, timeout);}

                            function jsddm_canceltimer()
                            {  if(closetimer)
                               {  window.clearTimeout(closetimer);
                                  closetimer = null;}}

                            jQuery(document).ready(function()
                            {  jQuery(\'#jsddm > li\').bind(\'mouseover\', jsddm_open)
                               jQuery(\'#jsddm > li\').bind(\'mouseout\',  jsddm_timer)});

                            document.onclick = jsddm_close;
                        </script>
                        ';

			echo $APjavascriptQueue;
		}
				
                function makeEmailTxt($theEmail) {
                    global $current_user, $wpdb;
                    get_currentuserinfo();
                    
                    $theEmail = str_replace("[customername]", $current_user->display_name, $theEmail);
                    $theEmail = str_replace("[sitename]", get_bloginfo(), $theEmail);

                    return $theEmail;
                }

                function splitOrderIntoProduct($keyToLookup, $type="default") {
                    global $wpdb;
                    $table_name = $wpdb->prefix . "wpstorecart_orders";
                    $table_name2 = $wpdb->prefix . "wpstorecart_products";
                    $table_name3 = $wpdb->prefix . "wpstorecart_meta";

                    $output = NULL;
                    $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name}` WHERE `primkey`={$keyToLookup};";
                    $results = $wpdb->get_results( $sql , ARRAY_A );
                    if(isset($results)) {
                        $specific_items = explode(",", $results[0]['cartcontents']);
                        foreach($specific_items as $specific_item) {
                            if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                                $current_item = explode('*', $specific_item);
                                $thevariationdetail[0] = NULL;
                                $thevariationdetail[1] = NULL;
                                if(isset($current_item[0]) && isset($current_item[1])) {
                                    if(!is_numeric($current_item[0])) { // This code adds support for product variations
                                        $newsplit = explode('-', $current_item[0]);
                                        $current_item[0] = $newsplit[0];
                                        if(@isset($newsplit[1]) && @is_numeric($newsplit[1])) {
                                            $sql3 = "SELECT * FROM `{$table_name3}` WHERE `type`='productvariation' AND `primkey`={$newsplit[1]};";
                                            $moreresults3 = $wpdb->get_results( $sql3 , ARRAY_A );
                                            if(@isset($moreresults3[0])) {
                                                $thevariationdetail = explode('||',$moreresults3[0]['value']);
                                                if(@isset($thevariationdetail[4])) { // If the variation has downloads associated with it
                                                    $variationdownloads = explode('****',$thevariationdetail[4]);
                                                }
                                            }
                                        } 
                                    }
                                    $sql2 = "SELECT `primkey`, `name`, `download`, `postid` FROM `{$table_name2}` WHERE `primkey`={$current_item[0]};";
                                    $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
                                    if($type=="default" && isset($moreresults[0])) {
                                        $output .= ', ';
                                        if($output==', ') {$output = '';}
                                        $output .= $moreresults[0]['name'] .' '.$thevariationdetail[0].' '.$thevariationdetail[1];
                                        if($current_item[1]!=1) {
                                            $output .= ' (x'.$current_item[1].')';
                                        }
                                    }
                                    if($type=="download" && isset($moreresults[0])) {
                                        $output .= ', <br />';
                                        if($output==', <br />') {$output = '';}
                                        if($moreresults[0]['download']=='' || $results[0]['orderstatus']!='Completed') { // Non-downloads products below:
                                            $output .= $moreresults[0]['name'].' '.$thevariationdetail[0].' '.$thevariationdetail[1];
                                        } else { // Download products below:
                                            if(@isset($variationdownloads)) { // If we've got variations that have downloads
                                                foreach ($variationdownloads as $variationdownload) {
                                                    $output .= '<a href="'.WP_PLUGIN_URL.'/wpstorecart/php/download.php?file='.$moreresults[0]['primkey'].'&isvariation=true&variationdl='.$variationdownload.'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png">'.$moreresults[0]['name'].' '.$thevariationdetail[0].' '.$thevariationdetail[1].'</a><br />';
                                                }
                                            }
                                            
                                            $multidownloads = explode('||', $moreresults[0]['download']);
                                            if(@isset($multidownloads[0]) && @isset($multidownloads[1])) {
                                                $downloadcount = 0;
                                                foreach($multidownloads as $multidownload) {
                                                    if($multidownload!='') {
                                                        $output .= '<a href="'.WP_PLUGIN_URL.'/wpstorecart/php/download.php?file='.$moreresults[0]['primkey'].'&part='.$downloadcount.'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png">'.$moreresults[0]['name'].' '.$thevariationdetail[0].' '.$thevariationdetail[1].' #'.$downloadcount.'</a><br />';
                                                    }
                                                        $downloadcount++;
                                                }
                                            } else {
                                                $output .= '<a href="'.WP_PLUGIN_URL.'/wpstorecart/php/download.php?file='.$moreresults[0]['primkey'].'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png">'.$moreresults[0]['name'].'</a>';
                                            }
                                        }
                                        if($current_item[1]!=1) {
                                            $output .= ' (x'.$current_item[1].')';
                                        }
                                    }
                                    if($type=="edit" && isset($moreresults[0])) {
                                        $output .= '<div id="delIcon'.$current_item[0].'">'.$moreresults[0]['name'].' '.$thevariationdetail[0].' '.$thevariationdetail[1];
                                        if($current_item[1]!=1) {
                                            $output .= '(x'.$current_item[1].')';
                                        }
                                        $output .= '<a href="#" onclick="deleteItemInCart('.$current_item[0].');"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/cross.png"></a><br /></div>';
                                    }
                                }
                            }
                        }
                    }
                    return $output;
                }


        function add_custom_contactmethod( $contactmethods ) {
            // Add Contact Fields
            $contactmethods['address'] = 'Address';
            $contactmethods['city'] = 'City';
            $contactmethods['state'] = 'State';
            $contactmethods['postalcode'] = 'Postal Code';

            return $contactmethods;
        }

        function show_first_name_field(){

            echo '
            First Name
            <input id="user_email" type="text" size="25" value="" name="first" />

            Last Name
            <input id="user_email" type="text" size="25" value="" name="last" />

            Address
            <input id="user_email" type="text" size="25" value="" name="address" />

            City
            <input id="user_email" type="text" size="25" value="" name="city" />

            State
<select name="state">
<option value="" selected="selected">Select a State</option>
<option value="not applicable">Other (Non-US)</option>
<option value="AL">Alabama</option>
<option value="AK">Alaska</option>
<option value="AZ">Arizona</option>
<option value="AR">Arkansas</option>
<option value="CA">California</option>
<option value="CO">Colorado</option>
<option value="CT">Connecticut</option>
<option value="DE">Delaware</option>
<option value="DC">District Of Columbia</option>
<option value="FL">Florida</option>
<option value="GA">Georgia</option>
<option value="HI">Hawaii</option>
<option value="ID">Idaho</option>
<option value="IL">Illinois</option>
<option value="IN">Indiana</option>
<option value="IA">Iowa</option>
<option value="KS">Kansas</option>
<option value="KY">Kentucky</option>
<option value="LA">Louisiana</option>
<option value="ME">Maine</option>
<option value="MD">Maryland</option>
<option value="MA">Massachusetts</option>
<option value="MI">Michigan</option>
<option value="MN">Minnesota</option>
<option value="MS">Mississippi</option>
<option value="MO">Missouri</option>
<option value="MT">Montana</option>
<option value="NE">Nebraska</option>
<option value="NV">Nevada</option>
<option value="NH">New Hampshire</option>
<option value="NJ">New Jersey</option>
<option value="NM">New Mexico</option>
<option value="NY">New York</option>
<option value="NC">North Carolina</option>
<option value="ND">North Dakota</option>
<option value="OH">Ohio</option>
<option value="OK">Oklahoma</option>
<option value="OR">Oregon</option>
<option value="PA">Pennsylvania</option>
<option value="RI">Rhode Island</option>
<option value="SC">South Carolina</option>
<option value="SD">South Dakota</option>
<option value="TN">Tennessee</option>
<option value="TX">Texas</option>
<option value="UT">Utah</option>
<option value="VT">Vermont</option>
<option value="VA">Virginia</option>
<option value="WA">Washington</option>
<option value="WV">West Virginia</option>
<option value="WI">Wisconsin</option>
<option value="WY">Wyoming</option>
</select>

            Postal Code
            <input id="user_email" type="text" size="25" value="" name="postalcode" />
            ';

        }

        function register_extra_fields($user_id, $password="", $meta=array()) {
            global $wpdb;
            if ( !current_user_can( 'edit_user', $user_id ) ) { 
                return false;
            } else {
                $userdata = array();
                $userdata['ID'] = $user_id;
                $userdata['first_name'] = $_POST['first'];
                $userdata['last_name'] = $_POST['last'];
                wp_update_user($userdata);
                update_usermeta( $user_id, 'address', $wpdb->escape($_POST['address']) );
                update_usermeta( $user_id, 'city', $wpdb->escape($_POST['city']) );
                update_usermeta( $user_id, 'state', $wpdb->escape($_POST['state']) );
                update_usermeta( $user_id, 'postalcode', $wpdb->escape($_POST['postalcode']) );
            }
        }


    }

 /**
 * ===============================================================================================================
 * End Main wpStoreCart Class
 */	
} 
// The end of the IF statement

if (class_exists("wpStoreCart")) {
    $wpStoreCart = new wpStoreCart();
}
 
 
 
/**
 * ===============================================================================================================
 * wpStoreCartCheckoutWidget SIDEBAR WIDGET
 */
if (class_exists("WP_Widget")) {

	// ------------------------------------------------------------------
	// ------------------------------------------------------------------

	class wpStoreCartCategoryWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartCategoryWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Categories');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {
			global $wpdb, $wpStoreCart;
			$output = NULL;
			$table_name = $wpdb->prefix . "wpstorecart_categories";

                        $devOptions = $wpStoreCart->getAdminOptions();

			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
			$numberOfproductsToDisplay = empty($instance['numberOfproductsToDisplay']) ? '10' : $instance['numberOfproductsToDisplay'];
			$widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
                        $maxImageWidth = empty($instance['maxImageWidth']) ? 'false' : $instance['maxImageWidth'];
                        $maxImageHeight = empty($instance['maxImageHeight']) ? 'false' : $instance['maxImageHeight'];

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			if(is_numeric($numberOfproductsToDisplay)){
				$sql = "SELECT * FROM `{$table_name}` ORDER BY `parent` DESC LIMIT 0, {$numberOfproductsToDisplay};";
				$results = $wpdb->get_results( $sql , ARRAY_A );
				if(isset($results)) {
					foreach ($results as $result) {
                                                if($result['postid'] == 0 || $result['postid'] == '') { // If there's no dedicated category pages, use the default
                                                    if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                                                        $permalink = get_permalink($devOptions['mainpage']) .'?wpsc=lc&wpsccat='.$result['primkey'];
                                                    } else {
                                                        $permalink = get_permalink($devOptions['mainpage']) .'&wpsc=lc&wpsccat='.$result['primkey'];
                                                    }
                                                } else {
                                                    $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                }
						if($widgetShowproductImages=='true') {
                                                        if(trim($result['thumbnail']=='')) {
                                                            $result['thumbnail'] = WP_PLUGIN_URL.'/wpstorecart/images/default_product_img.jpg';
                                                        }
							$output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['category'].'"'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
						}
						$output .= '<p><a href="'.$permalink.'">'.$result['category'].'</a></p>';
					}
				}
			} else {
				$output .= 'wpStoreCart did not like your widget!  The number of categories to display contained non-numeric data. Please fix your widget or consult the wpStoreCart documentation for help.';
			}
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
			$instance['numberOfproductsToDisplay'] = strip_tags(stripslashes($new_instance['numberOfproductsToDisplay']));
			$instance['widgetShowproductImages'] = strip_tags(stripslashes($new_instance['widgetShowproductImages']));
                        $instance['maxImageWidth'] = strip_tags(stripslashes($new_instance['maxImageWidth']));
                        $instance['maxImageHeight'] = strip_tags(stripslashes($new_instance['maxImageHeight']));
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {
			@$title = esc_attr($instance['title']);
			@$numberOfproductsToDisplay = htmlspecialchars($instance['numberOfproductsToDisplay']);
			@$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
                        @$maxImageWidth = htmlspecialchars($instance['maxImageWidth']);
                        @$maxImageHeight = htmlspecialchars($instance['maxImageHeight']);

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name('numberOfproductsToDisplay') . '">' . __('Number of categories to display:') . ' <input style="width: 80px;" id="' . $this->get_field_id('numberOfproductsToDisplay') . '" name="' . $this->get_field_name('numberOfproductsToDisplay') . '" type="text" value="' . $numberOfproductsToDisplay . '" /></label></p>';
			//echo '<p style="text-align:left;"><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . ' <input style="width: 200px;" id="' . $this->get_field_id('widgetShowproductImages') . '" name="' . $this->get_field_name('widgetShowproductImages') . '" type="text" value="' . $widgetShowproductImages . '" /></label></p>';
			echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . '<label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageWidth') . '">' . __('Max thumb width:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageWidth') . '" name="' . $this->get_field_name('maxImageWidth') . '" type="text" value="' . $maxImageWidth . '" /> px</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageHeight') . '">' . __('Max thumb height:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageHeight') . '" name="' . $this->get_field_name('maxImageHeight') . '" type="text" value="' . $maxImageHeight . '" /> px</label></p>';
		}

	}
	// ------------------------------------------------------------------
	// ------------------------------------------------------------------



	class wpStoreCartCheckoutWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartCheckoutWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Cart Contents');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {
			global $wpdb, $cart, $wpsc, $is_checkout,$wpscCarthasBeenCalled;
                        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
                        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
                        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
                        if(!isset($_SESSION)) {
                                @session_start();

                        }
                        if(@!is_object($cart)) {
                            $cart =& $_SESSION['wpsc'];
                            if(@!is_object($cart)) {
                                $cart = new wpsc();
                            }
                        }
			$output = NULL;
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
                        $widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			$old_checkout = $is_checkout;
			$is_checkout = false;
                        if($widgetShowproductImages=='true') {
                           $is_checkout = true;
                        }
			$output = $cart->display_cart($wpsc);
			$is_checkout = $old_checkout;
                        $wpscCarthasBeenCalled = true;
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
                        $instance['widgetShowproductImages'] = strip_tags(stripslashes($new_instance['widgetShowproductImages']));
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {
			@$title = esc_attr($instance['title']);
                        @$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Use as the final checkout:') . '<label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
		}

	}

	// ------------------------------------------------------------------
	// ------------------------------------------------------------------


        class wpStoreCartLoginWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartLoginWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart User Account/Login');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {		
			global $wpdb, $wpStoreCart;
			$output = NULL;
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);

                        $devOptions = $wpStoreCart->getAdminOptions();

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }

                        if ( is_user_logged_in() ) {
                                if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                                    $permalink = get_permalink($devOptions['mainpage']) .'?wpsc=orders';
                                } else {
                                    $permalink = get_permalink($devOptions['mainpage']) .'&wpsc=orders';
                                }
                                $output .= '<ul>';
                                $output .= '<li><a href="'.$permalink.'">My Orders &amp; Purchases</a></li>';
                                $output .= '<li><a href="'.wp_logout_url(get_permalink()).'">Logout</a></li>';
                                $output .= '</ul>';
                        } else {

                             $output .= '
<strong>Login</strong><br />
<form id="login" method="post" action="'. wp_login_url( get_permalink() ) .'">
                                        <fieldset>
                                                <label>Username
                                                <input type="text" value="" name="log" /></label>
                                                <label>Password</label>
                                                <input type="password" value="" name="pwd"  /></label>
                                                <input type="submit" value="Login" />
                                        </fieldset>
                                </form>
<br />
<strong>Register</strong><br />
<form name="registerform" action="'. WP_PLUGIN_URL.'/wpstorecart/php/register.php" method="post">
	<fieldset>
		<label>E-mail
		<input type="text" name="email" value="" /></label>
                <input type="hidden" name="redirect_to" value="'.$_SERVER['REQUEST_URI'].'" />
                <label>Password
		<input type="password" name="user_pass" value="" /></label>
<select name="wpstate" style="display:none;">
<option value="" selected="selected">Select a State</option>
<option value="not applicable">Other (Non-US)</option>
<option value="AL">Alabama</option>
<option value="AK">Alaska</option>
<option value="AZ">Arizona</option>
<option value="AR">Arkansas</option>
<option value="CA">California</option>
<option value="CO">Colorado</option>
<option value="CT">Connecticut</option>
<option value="DE">Delaware</option>
<option value="DC">District Of Columbia</option>
<option value="FL">Florida</option>
<option value="GA">Georgia</option>
<option value="HI">Hawaii</option>
<option value="ID">Idaho</option>
<option value="IL">Illinois</option>
<option value="IN">Indiana</option>
<option value="IA">Iowa</option>
<option value="KS">Kansas</option>
<option value="KY">Kentucky</option>
<option value="LA">Louisiana</option>
<option value="ME">Maine</option>
<option value="MD">Maryland</option>
<option value="MA">Massachusetts</option>
<option value="MI">Michigan</option>
<option value="MN">Minnesota</option>
<option value="MS">Mississippi</option>
<option value="MO">Missouri</option>
<option value="MT">Montana</option>
<option value="NE">Nebraska</option>
<option value="NV">Nevada</option>
<option value="NH">New Hampshire</option>
<option value="NJ">New Jersey</option>
<option value="NM">New Mexico</option>
<option value="NY">New York</option>
<option value="NC">North Carolina</option>
<option value="ND">North Dakota</option>
<option value="OH">Ohio</option>
<option value="OK">Oklahoma</option>
<option value="OR">Oregon</option>
<option value="PA">Pennsylvania</option>
<option value="RI">Rhode Island</option>
<option value="SC">South Carolina</option>
<option value="SD">South Dakota</option>
<option value="TN">Tennessee</option>
<option value="TX">Texas</option>
<option value="UT">Utah</option>
<option value="VT">Vermont</option>
<option value="VA">Virginia</option>
<option value="WA">Washington</option>
<option value="WV">West Virginia</option>
<option value="WI">Wisconsin</option>
<option value="WY">Wyoming</option>
</select>
		<input type="submit" name="wp-submit" value="Register" />
	</fieldset>
</form>
';
                        }

			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {	
			$instance['title']= strip_tags(stripslashes($new_instance['title']));

			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {				
			@$title = esc_attr($instance['title']);

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';

		}

	}
	
	// ------------------------------------------------------------------
	// ------------------------------------------------------------------ 
 
	class wpStoreCartTopproductsWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartTopproductsWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Top Products');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {		
			global $wpdb;
			$output = NULL;
			$table_name = $wpdb->prefix . "wpstorecart_products";
		
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
			$numberOfproductsToDisplay = empty($instance['numberOfproductsToDisplay']) ? '10' : $instance['numberOfproductsToDisplay'];
			$widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
                        $maxImageWidth = empty($instance['maxImageWidth']) ? 'false' : $instance['maxImageWidth'];
                        $maxImageHeight = empty($instance['maxImageHeight']) ? 'false' : $instance['maxImageHeight'];

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			if(is_numeric($numberOfproductsToDisplay)){
				$sql = "SELECT * FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT 0, {$numberOfproductsToDisplay};";
				$results = $wpdb->get_results( $sql , ARRAY_A );
				if(isset($results)) {
					foreach ($results as $result) {
						$permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
						if($widgetShowproductImages=='true') {
							$output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['name'].'"'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
						}
						$output .= '<p><a href="'.$permalink.'">'.$result['name'].'</a></p>';
					}
				}
			} else {
				$output .= 'wpStoreCart did not like your widget!  The number of products to display contained non-numeric data. Please fix your widget or consult the wpStoreCart documentation for help.';
			}
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {	
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
			$instance['numberOfproductsToDisplay'] = strip_tags(stripslashes($new_instance['numberOfproductsToDisplay']));
			$instance['widgetShowproductImages'] = strip_tags(stripslashes($new_instance['widgetShowproductImages']));
                        $instance['maxImageWidth'] = strip_tags(stripslashes($new_instance['maxImageWidth']));
                        $instance['maxImageHeight'] = strip_tags(stripslashes($new_instance['maxImageHeight']));
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {				
			@$title = esc_attr($instance['title']);
			@$numberOfproductsToDisplay = htmlspecialchars($instance['numberOfproductsToDisplay']);
			@$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
                        @$maxImageWidth = htmlspecialchars($instance['maxImageWidth']);
                        @$maxImageHeight = htmlspecialchars($instance['maxImageHeight']);

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name('numberOfproductsToDisplay') . '">' . __('Number of products to display:') . ' <input style="width: 80px;" id="' . $this->get_field_id('numberOfproductsToDisplay') . '" name="' . $this->get_field_name('numberOfproductsToDisplay') . '" type="text" value="' . $numberOfproductsToDisplay . '" /></label></p>';
			//echo '<p style="text-align:left;"><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . ' <input style="width: 200px;" id="' . $this->get_field_id('widgetShowproductImages') . '" name="' . $this->get_field_name('widgetShowproductImages') . '" type="text" value="' . $widgetShowproductImages . '" /></label></p>';
			echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . '<label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageWidth') . '">' . __('Max thumb width:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageWidth') . '" name="' . $this->get_field_name('maxImageWidth') . '" type="text" value="' . $maxImageWidth . '" /> px</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageHeight') . '">' . __('Max thumb height:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageHeight') . '" name="' . $this->get_field_name('maxImageHeight') . '" type="text" value="' . $maxImageHeight . '" /> px</label></p>';
		}

	} 
	// ------------------------------------------------------------------
	// ------------------------------------------------------------------
	
	class wpStoreCartRecentproductsWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartRecentproductsWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Recent Products');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {		
			global $wpdb;
			$output = NULL;
			$table_name = $wpdb->prefix . "wpstorecart_products";
		
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
			$numberOfproductsToDisplay = empty($instance['numberOfproductsToDisplay']) ? '10' : $instance['numberOfproductsToDisplay'];
			$widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
                        $maxImageWidth = empty($instance['maxImageWidth']) ? 'false' : $instance['maxImageWidth'];
                        $maxImageHeight = empty($instance['maxImageHeight']) ? 'false' : $instance['maxImageHeight'];

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			if(is_numeric($numberOfproductsToDisplay)){
				$sql = "SELECT * FROM `{$table_name}` ORDER BY `dateadded` DESC LIMIT 0, {$numberOfproductsToDisplay};";
				$results = $wpdb->get_results( $sql , ARRAY_A );
				if(isset($results)) {
					foreach ($results as $result) {
						$permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
						if($widgetShowproductImages=='true') {
							$output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['name'].'"'; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
						}
						$output .= '<p><a href="'.$permalink.'">'.$result['name'].'</a></p>';
					}
				}
			} else {
				$output .= 'wpStoreCart did not like your widget!  The number of products to display contained non-numeric data. Please fix your widget or consult the wpStoreCart documentation for help.';
			}
			echo $output;
			echo $after_widget;
		}

		/** @see WP_Widget::update */
		function update($new_instance, $old_instance) {	
			$instance['title']= strip_tags(stripslashes($new_instance['title']));
			$instance['numberOfproductsToDisplay'] = strip_tags(stripslashes($new_instance['numberOfproductsToDisplay']));
			$instance['widgetShowproductImages'] = strip_tags(stripslashes($new_instance['widgetShowproductImages']));
                        $instance['maxImageWidth'] = strip_tags(stripslashes($new_instance['maxImageWidth']));
                        $instance['maxImageHeight'] = strip_tags(stripslashes($new_instance['maxImageHeight']));
                        
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {				
			@$title = esc_attr($instance['title']);
			@$numberOfproductsToDisplay = htmlspecialchars($instance['numberOfproductsToDisplay']);
			@$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
                        @$maxImageWidth = htmlspecialchars($instance['maxImageWidth']);
                        @$maxImageHeight = htmlspecialchars($instance['maxImageHeight']);

			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
			echo '<p style="text-align:left;"><label for="' . $this->get_field_name('numberOfproductsToDisplay') . '">' . __('Number of products to display:') . ' <input style="width: 80px;" id="' . $this->get_field_id('numberOfproductsToDisplay') . '" name="' . $this->get_field_name('numberOfproductsToDisplay') . '" type="text" value="' . $numberOfproductsToDisplay . '" /></label></p>';
			//echo '<p style="text-align:left;"><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . ' <input style="width: 200px;" id="' . $this->get_field_id('widgetShowproductImages') . '" name="' . $this->get_field_name('widgetShowproductImages') . '" type="text" value="' . $widgetShowproductImages . '" /></label></p>';
			echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Show images:') . '<label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageWidth') . '">' . __('Max thumb width:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageWidth') . '" name="' . $this->get_field_name('maxImageWidth') . '" type="text" value="' . $maxImageWidth . '" /> px</label></p>';
                        echo '<p style="text-align:left;"><label for="' . $this->get_field_name('maxImageHeight') . '">' . __('Max thumb height:') . ' <input style="width: 80px;" id="' . $this->get_field_id('maxImageHeight') . '" name="' . $this->get_field_name('maxImageHeight') . '" type="text" value="' . $maxImageHeight . '" /> px</label></p>';

		}

	} 	
	
	
}
/**
 * ===============================================================================================================
 * END wpStoreCartTopproductsWidget SIDEBAR WIDGET
 */

 
 
 

 /**
 * ===============================================================================================================
 * Initialize the admin panel
 */
if (!function_exists("wpStoreCartAdminPanel")) {
    function wpStoreCartAdminPanel() {
        global $wpStoreCart, $testing_mode, $wpstorecart_version_int;
        if (!isset($wpStoreCart)) {
            return;
        }
        if (function_exists('add_menu_page')) {
			$mainPage = add_menu_page('wpStoreCart - Open Source WP Shopping Cart &amp; eCommerce Plugin', 'wpStoreCart', 'activate_plugins', 'wpstorecart-admin', array(&$wpStoreCart, 'printAdminPageOverview'), WP_PLUGIN_URL.'/wpstorecart/images/controller.png');
			$settingsPage = add_submenu_page('wpstorecart-admin','Settings - wpStoreCart ', 'Settings', 'activate_plugins', 'wpstorecart-settings', array(&$wpStoreCart, 'printAdminPage'));
			$page = add_submenu_page('wpstorecart-admin','Add product - wpStoreCart ', 'Add product', 'activate_plugins', 'wpstorecart-add-products', array(&$wpStoreCart, 'printAdminPageAddproducts'));
			add_submenu_page('wpstorecart-admin','Edit products - wpStoreCart ', 'Edit products', 'activate_plugins', 'wpstorecart-edit-products', array(&$wpStoreCart, 'printAdminPageEditproducts'));
			if($testing_mode==true || $wpstorecart_version_int >= 202000) { // Bleeding edge until 2.2, at which time this code block will automatically be enabled
				$importpage = add_submenu_page('wpstorecart-admin','Import and Export - wpStoreCart ', 'Import/Export', 'activate_plugins', 'wpstorecart-import', array(&$wpStoreCart, 'printAdminPageImport'));
				add_action("admin_print_scripts-$importpage", array(&$wpStoreCart, 'my_import_scripts') );
			}
			$categoriesPage = add_submenu_page('wpstorecart-admin','Categories - wpStoreCart ', 'Categories', 'activate_plugins', 'wpstorecart-categories', array(&$wpStoreCart, 'printAdminPageCategories'));
			$ordersPage = add_submenu_page('wpstorecart-admin','Orders &amp; Customers - wpStoreCart', 'Orders', 'activate_plugins', 'wpstorecart-orders', array(&$wpStoreCart, 'printAdminPageOrders'));
			$page2 = add_submenu_page('wpstorecart-admin','Coupons &amp; Discounts - wpStoreCart ', 'Coupons', 'activate_plugins', 'wpstorecart-coupon', array(&$wpStoreCart, 'printAdminPageCoupons'));
			add_submenu_page('wpstorecart-admin','Affiliates - wpStoreCart PRO', 'Affiliates', 'activate_plugins', 'wpstorecart-affiliates', array(&$wpStoreCart, 'printAdminPageAffiliates'));
			$statsPage = add_submenu_page('wpstorecart-admin','Statistics - wpStoreCart PRO', 'Statistics', 'activate_plugins', 'wpstorecart-statistics', array(&$wpStoreCart, 'printAdminPageStatistics'));
			add_submenu_page('wpstorecart-admin','Help - wpStoreCart PRO', 'Help', 'activate_plugins', 'wpstorecart-help', array(&$wpStoreCart, 'printAdminPageHelp'));
			add_action("admin_print_scripts-$settingsPage", array(&$wpStoreCart, 'my_tooltip_script') );
			add_action("admin_print_scripts-$categoriesPage", array(&$wpStoreCart, 'my_tooltip_script') );
			add_action("admin_print_scripts-$ordersPage", array(&$wpStoreCart, 'my_tooltip_script') );
			add_action("admin_print_scripts-$page", array(&$wpStoreCart, 'my_admin_scripts') );
			add_action("admin_print_scripts-$page2", array(&$wpStoreCart, 'admin_script_anytime'), 1);
			add_action("admin_print_scripts-$mainPage", array(&$wpStoreCart, 'my_mainpage_scripts') );
            add_action("admin_print_scripts-$statsPage", array(&$wpStoreCart, 'my_mainpage_scripts') );
        }
    }   
}
 /**
 * ===============================================================================================================
 * END Initialize the admin panel
 */
 
 
 /**
 * ===============================================================================================================
 * Call everything
 */




//Actions and Filters   
if (isset($wpStoreCart)) {
    //Actions
        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
	require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
	if(!isset($_SESSION)) {
		@session_start();
		
	}
        if(@!is_object($cart)) {
            $cart =& $_SESSION['wpsc'];
            if(@!is_object($cart)) {
                $cart = new wpsc();
            }
        }

	register_activation_hook(__FILE__, array(&$wpStoreCart, 'wpstorecart_install')); // Install DB schema
	add_action('init', array(&$wpStoreCart, 'register_custom_init')); //
        add_action('wpstorecart/wpstorecart.php',  array(&$wpStoreCart, 'init')); // Create options on activation
	add_action('admin_menu', 'wpStoreCartAdminPanel'); // Create admin panel
	add_action('wp_dashboard_setup', array(&$wpStoreCart, 'wpstorecart_main_add_dashboard_widgets') ); // Dashboard widget
        add_action('wp_footer', array(&$wpStoreCart, 'addFooterCode'), 1); // Place wpStoreCart comment into header
        add_action('wp_head', array(&$wpStoreCart, 'addHeaderCode'), 1); // Place wpStoreCart comment into header
	add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartCheckoutWidget");')); // Register the widget: wpStoreCartTopproductsWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartLoginWidget");')); // Register the widget: wpStoreCartTopproductsWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartTopproductsWidget");')); // Register the widget: wpStoreCartTopproductsWidget
	add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartRecentproductsWidget");')); // Register the widget: wpStoreCartRecentproductsWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartCategoryWidget");')); // Register the widget: wpStoreCartCategoryWidget

	add_shortcode('wpstorecart', array(&$wpStoreCart, 'wpstorecart_mainshortcode'));
	add_action('admin_head', array(&$wpStoreCart, 'placeAdminHeaderCode')); // Place wpStoreCart comment into header
        add_action( 'wp_print_styles', array(&$wpStoreCart, 'enqueue_my_styles') );

        add_filter('user_contactmethods', array(&$wpStoreCart, 'add_custom_contactmethod'),10,1);
        add_action('register_form', array(&$wpStoreCart, 'show_first_name_field'));
        //add_action('register_post','check_fields',10,3);
        add_action('user_register',  array(&$wpStoreCart, 'register_extra_fields'));

    //Filters
	add_filter('the_posts', array(&$wpStoreCart, 'add_script_swfobject')); 


}
 /**
 * ===============================================================================================================
 */



?>