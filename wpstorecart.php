<?php
/*
Plugin Name: wpStoreCart
Plugin URI: http://www.wpstorecart.com/
Description: <a href="http://www.wpstorecart.com/" target="blank">wpStoreCart</a> is a full e-commerce Wordpress plugin that accepts PayPal out of the box. It includes multiple widgets, dashboard widgets, shortcodes, and works using Wordpress pages to keep everything nice and simple. 
Version: 2.0.3
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

global $wpStoreCart, $cart, $wpsc;

//Global variables:
$wpstorecart_version = '2.0.3';
$wpstorecart_db_version = '2.0.2';
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
	mkdir(WP_CONTENT_DIR . '/uploads/', 0777, true);
}
if(!is_dir(WP_CONTENT_DIR . '/uploads/wpstorecart/')) {
	mkdir(WP_CONTENT_DIR . '/uploads/wpstorecart/', 0777, true);
}
	

 /**
 * ===============================================================================================================
 * Main wpStoreCart Class
 */	
if (!class_exists("wpStoreCart")) {
    class wpStoreCart {
		var $adminOptionsName = "wpStoreCartAdminOptions";
		
        function wpStoreCart() { //constructor
            global $wpdb;

            $devOptions = $this->getAdminOptions();

            // Upgrade the database schema if they're running 2.0.2 or below:
            if($devOptions['database_version']==NULL) {
                $table_name = $wpdb->prefix . "wpstorecart_categories";
                $sql = "ALTER TABLE `{$table_name}` ADD `thumbnail` VARCHAR( 512 ) NOT NULL, ADD `description` TEXT NOT NULL, ADD `postid` INT NOT NULL ";
                $results = $wpdb->query( $sql );
                $devOptions['database_version'] = $wpstorecart_db_version;
            }

            // This increments the add to cart counter for the product statistics
            if(isset($_POST['my-item-id'])) {
                $primkey = $_POST['my-item-id'];

                if(is_numeric($primkey)) {


                        $table_name = $wpdb->prefix . "wpstorecart_products";
                        $sql = "SELECT * FROM `{$table_name}` WHERE `primkey`={$primkey};";
                        $results = $wpdb->get_results( $sql , ARRAY_A );
                        if(isset($results)) {
                                $newTimesAddedToCart = $results[0]['timesaddedtocart'] + 1;
                                $wpdb->query("UPDATE `{$table_name}` SET `timesaddedtocart` = '{$newTimesAddedToCart}' WHERE `primkey` = {$primkey} LIMIT 1 ;");

                        }


                } else {
                        //exit();
                }
            }
        }

        function register_custom_init() {
            // This block of code is for incrementing the add to cart log

            if(isset($_POST['my-item-id'])) {
                $primkey = $_POST['my-item-id'];

                if(is_numeric($primkey)) {
                    global $current_user, $wpdb;
                    wp_get_current_user();
                    if ( 0 == $current_user->ID ) {
                        // Not logged in.
                        $theuser = 0;
                    } else {
                        $theuser = $current_user->ID;
                    }
                    $wpdb->query("INSERT INTO `{$wpdb->prefix}wpstorecart_log` (`primkey` ,`action` ,`data` ,`foreignkey` ,`date` ,`userid`) VALUES (NULL, 'addtocart', '{$_SERVER['REMOTE_ADDR']}', '{$primkey}', '".date('Ymd')."', '{$theuser}');");
                }
            }
            /* The use of custom post types will be implemented in a future version of wpsc
            if(get_bloginfo('version') >= 3) { // If we're using Wordpress 3 or higher, use custom post types
                $labels = array(
                'name' => _x('Products', 'post type general name'),
                'singular_name' => _x('Product', 'post type singular name'),
                'add_new' => _x('Add New', 'book'),
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
			echo '
			<style type="text/css">
				ul#tabnav { 
					text-align: left;
					margin: 0.8em 0 0.8em 0;
					font: bold 10px verdana, arial, sans-serif;
					list-style-type: none;
					padding: 7px 10px 3px 10px; 
				}

				ul#tabnav li { 
					display: block;
					float:left;
					margin:0 1px 0 1px; 
				}

				body#tab li.tab { 
					border-bottom: 1px solid #bbb; 
				}

				ul#tabnav li a { 
					padding: 7px 4px 3px 4px;
					border: 1px solid #bbb; 
					background: #FFF url("'.get_option( 'siteurl' ).'/wp-admin/images/gray-grad.png") repeat;
					color: #666; 
					margin-right: 0px; 
					text-decoration: none;
					border-bottom: 1px solid #999;
				}

				ul#tabnav a:hover {
					background: #DDD; 
				}

			</style>
			
			<div class="wrap">
			<div style="padding: 20px 10px 10px 10px;">
			<div style="float:left;"><a href="http://wpstorecart.com" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/logo.png" alt="wpstorecart" /></a><br /><a href="http://wpstorecart.com/store/business-support-wpstorecart-pro/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/order_pro.png" alt="wpstorecart" /></a></div>
			<div style="float:right;">
				
                                <a style="position:absolute;top:50px;margin-left:-200px;" href="http://wpstorecart.com/design-mods-support/" target="_blank"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/hire_us.png" alt="wpstorecart" /></a>
			</div>

			<br style="clear:both;" />
			<ul id="tabnav">
				<li class="tab"><a href="admin.php?page=wpstorecart-admin" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/controller.png" /></a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-settings" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/application_form_edit.png" /> Settings</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-add-products" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/basket_add.png" /> Add Product</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-edit-products" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/basket_edit.png" /> Edit Products</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-categories" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/table.png" /> Categories</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-orders" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/cart_go.png" /> Orders</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-coupon" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/money.png" /> Coupons</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-affiliates" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/user_suit.png" /> Affiliates</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-statistics" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/chart_bar.png" /> Statistics</a></li>
				<li class="tab"><a href="http://wpstorecart.com/help-support/" target="_blank" class="spmenu"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" /></a></li>
			</ul>
			<br />

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
                                    'wpscCss' => 'bigbuttons.css',
                                    'frontpageDisplays' => 'List all products',
                                    'displayThumb' => 'true',
                                    'displayTitle' => 'true',
                                    'displayintroDesc' => 'true',
                                    'displayFullDesc' => 'false',
                                    'displayType' => 'grid',
                                    'displayAddToCart' => 'true',
                                    'displayBuyNow' => 'true',
                                    'displayPrice' => 'true',
                                    'allowpaypal' => 'true',
                                    'paypalemail' => get_bloginfo('admin_email'),
                                    'paypaltestmode' => 'false',
                                    'emailonpurchase' => 'Dear [customername], thanks for your recent order from [sitename].  Your order has been submitted to our staff for approval.  This process can take as little as an hour to as long as a few weeks depending on how quickly your payment clears, and whether there are other issues which may cause a delay.',
                                    'emailonapproval' => 'Dear [customername], thanks again for your recent order from [sitename].  This email is to inform you that your order has been approved.  For physical products, this does not mean that they have been shipped yet; as you will get another email when the order is shipped.  If you ordered a digital download, your download is now available.  .',
                                    'emailonshipped'  => 'Dear [customername], thanks again for your recent order from [sitename].  This email is to inform you that your order has been shipped.',
                                    'emailsig' => 'Thanks again, [sitename] Management',
                                    'cart_title' => 'Shopping Cart',
                                    'single_item' => 'Item',
                                    'multiple_items' => 'Items',
                                    'currency_symbol' => '$',
                                    'subtotal' => 'Subtotal',
                                    'update_button' => 'update',
                                    'checkout_button' => 'checkout',
                                    'checkout_paypal_button' => 'Checkout with PayPal',
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
                                    'database_version' => NULL
                                    );

            $devOptions = get_option($this->adminOptionsName);
            if (!empty($devOptions)) {
                foreach ($devOptions as $key => $option)
                    $apAdminOptions[$key] = $option;
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
				if (isset($_POST['allowpaypal'])) {
					$devOptions['allowpaypal'] = $wpdb->escape($_POST['allowpaypal']);
				}
				if (isset($_POST['paypalemail'])) {
					$devOptions['paypalemail'] = $wpdb->escape($_POST['paypalemail']);
				}
				if (isset($_POST['paypaltestmode'])) {
					$devOptions['paypaltestmode'] = $wpdb->escape($_POST['paypaltestmode']);
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
				if (isset($_POST['subtotal'])) {
 					$devOptions['subtotal'] = $wpdb->escape($_POST['subtotal']);
				}
				if (isset($_POST['update_button'])) {
 					$devOptions['update_button'] = $wpdb->escape($_POST['update_button']);
				}
				if (isset($_POST['checkout_button'])) {
 					$devOptions['checkout_button'] = $wpdb->escape($_POST['checkout_button']);
				}
				if (isset($_POST['checkout_paypal_button'])) {
 					$devOptions['checkout_paypal_button'] = $wpdb->escape($_POST['checkout_paypal_button']);
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

				update_option($this->adminOptionsName, $devOptions);
			   
				echo '<div class="updated"><p><strong>';
				_e("Settings Updated.", "wpStoreCart");
				echo '</strong></p></div>';
			
			}
			
			echo '
                        <script type="text/javascript">
			//<![CDATA[
			jQuery(document).ready(function($) {
                                //When page loads...
                                $(".tab_content").hide(); //Hide all content
                                $("ul.tabs li:first").addClass("active").show(); //Activate first tab
                                $(".tab_content:first").show(); //Show first tab content

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

                        ul.tabs {
                                margin: 0 0 10px 8px;
                                padding: 0;
                                float: left;
                                list-style: none;
                                height: 74px; /*--Set height of tabs--*/
                                width: 100%;
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
                        }
                        ul.tabs li a {
                                text-decoration: none;
                                color: #000;
                                display: block;
                                font-size: 1.2em;
                                
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
                        }
                        .tab_content {
                                padding: 20px;

                        }
			</style>';

			$this->spHeader();
			
			echo'
			<form method="post" action="'. $_SERVER["REQUEST_URI"].'">
                        <ul class="tabs">
                            <li><a href="#tab1"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_general.jpg" /></a></li>
                            <li><a href="#tab2"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_email.jpg" /></a></li>
                            <li><a href="#tab3"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_product.jpg" /></a></li>
                            <li><a href="#tab4"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_payment.jpg" /></a></li>
                            <li><a href="#tab5"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/buttons_text.jpg" /></a></li>
                        </ul>


                        <div id="tab1" class="tab_content">
			<h2>wpStoreCart General Options</h2>
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
			<h2>EMail Options</h2>';

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
			<h2>Display Options</h2>			
			';
			
			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>';

                        echo '
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
			<h2>Payment Options</h2>';

			echo '<table class="widefat">
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

        		</table>
			
			<br style="clear:both;" /><br />
                        </div>
                        <div id="tab5" class="tab_content">
                        <h2>Text &amp; Language Options</h2>';


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

			<tr><td><h3>Currency Symbol</h3></td>
			<td class="tableDescription"><p>Default: <i>$</i></p></td>
			<td><input type="text" name="currency_symbol" value="'; _e(apply_filters('format_to_edit',$devOptions['currency_symbol']), 'wpStoreCart'); echo'" />
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

			<tr><td><h3>Checkout PayPal Button</h3></td>
			<td class="tableDescription"><p>Default: <i>Checkout with PayPal</i></p></td>
			<td><input type="text" name="checkout_paypal_button" value="'; _e(apply_filters('format_to_edit',$devOptions['checkout_paypal_button']), 'wpStoreCart'); echo'" />
			</td></tr>

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
		
		
		
		
		
		//Prints out the Add products admin page =======================================================================
        function printAdminPageAddproducts() {
			global $wpdb, $user_level;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			$devOptions = $this->getAdminOptions();
			$table_name = $wpdb->prefix . "wpstorecart_products";
			
			if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
				echo '<br /><br /><h3>ERROR: wpStoreCart is configured incorrectly.  Visit the wpStoreCart options page and set the main page to the numeric POST ID of a dedicated PAGE that you have created for your arcade.</h3>';
				return false;
			}
			
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
			} 
			
			
			// To edit a previous product
			$isanedit = false;
			if ($_GET['keytoedit']!=0 && is_numeric($_GET['keytoedit'])) {
				$isanedit = true;
				
				if (isset($_POST['wpStoreCartproduct_name']) && isset($_POST['wpStoreCartproduct_introdescription']) && isset($_POST['wpStoreCartproduct_description']) && isset($_POST['wpStoreCartproduct_thumbnail']) && isset($_POST['wpStoreCartproduct_price']) && isset($_POST['wpStoreCartproduct_shipping']) && isset($_POST['wpStoreCartproduct_download']) && isset($_POST['wpStoreCartproduct_tags']) && isset($_POST['wpStoreCartproduct_category']) && isset($_POST['wpStoreCartproduct_inventory'])) {
					$wpStoreCartproduct_name = $wpdb->escape($_POST['wpStoreCartproduct_name']);
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
                                        `useinventory` = '{$wpStoreCartproduct_useinventory}'
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
					}
				} else {
					echo '<div class="updated"><p><strong>';
					echo "There was a problem loading the product you wish to edit.  The query was: {$grabrecord} ";
					echo '</strong></p></div>';					
				}
			}
			
			if (isset($_POST['addNewwpStoreCart_product']) && $isanedit == false) {
			
				if (isset($_POST['wpStoreCartproduct_name']) && isset($_POST['wpStoreCartproduct_introdescription']) && isset($_POST['wpStoreCartproduct_description']) && isset($_POST['wpStoreCartproduct_thumbnail']) && isset($_POST['wpStoreCartproduct_price']) && isset($_POST['wpStoreCartproduct_shipping']) && isset($_POST['wpStoreCartproduct_download']) && isset($_POST['wpStoreCartproduct_tags']) && isset($_POST['wpStoreCartproduct_category']) && isset($_POST['wpStoreCartproduct_inventory'])) {
					$wpStoreCartproduct_name = $wpdb->escape($_POST['wpStoreCartproduct_name']);
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
					INSERT INTO {$table_name} (`primkey`, `name`, `introdescription`, `description`, `thumbnail`, `price`, `shipping`, `download`, `tags`, `category`, `inventory`, `dateadded`, `postid`, `timesviewed`, `timesaddedtocart`, `timespurchased`, `useinventory`) VALUES
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
                                        {$wpStoreCartproduct_useinventory});
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
			<form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">

			<h2>Add or Edit a Product</h2>';
			
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
			<td>Price: <input type="text" name="wpStoreCartproduct_price" style="width: 58px;" value="'.$wpStoreCartproduct_price.'" />  &nbsp; &nbsp; &nbsp; &nbsp; Shipping: <input type="text" name="wpStoreCartproduct_shipping" style="width: 58px;" value="'.$wpStoreCartproduct_shipping.'" /></td>
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
			<!--<td><input type="text" name="wpStoreCartproduct_category" style="width: 120px;" value="'.$wpStoreCartproduct_category.'" />  </td>-->
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
			<td><h3>Downloadable File: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-8" /><div class="tooltip-content" id="example-content-8">If your product is digital in nature, then you can distribute it as a digital download.  If you need to upload more than one file, use something like 7zip or WinRAR to make a single file archive from the multiple files.  Enter a file name here or upload a new one.  All uploads are stored at: '.WP_CONTENT_DIR . '/uploads/wpstorecart/</div></h3></td>
			<td>File: <input type="text" name="wpStoreCartproduct_download" style="width: 200px;" value="'.$wpStoreCartproduct_download.'" /> or<br />
			Upload a file: <span id="spanSWFUploadButton"></span>
			</td>
			<td><div style="width:300px;">The filename or upload of a downloadable product.  Leave this blank for physical products.  Max filesize is either: '.ini_get('post_max_size').' or '.ini_get('upload_max_filesize').', whichever is lower. Do not put URLs or full paths here, only a filename.</div></td>
			</tr>';			
			
                        if($wpStoreCartproduct_thumbnail==''||!isset($wpStoreCartproduct_thumbnail)) {
                            $wpStoreCartproduct_thumbnail = WP_PLUGIN_URL.'/wpstorecart/images/default_product_img.jpg';
                        }
			echo '
			<tr>
			<td><h3>Product Thumbnail: <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-9" /><div class="tooltip-content" id="example-content-9">The main product image.  It will be used in multiple places.  It is recommend that the image have a 1:1 width and height ratio.  For example, 100px X 100px.</div></h3></td>
			<td>URL: <input type="text" name="wpStoreCartproduct_thumbnail" style="width: 250px;" value="'.$wpStoreCartproduct_thumbnail.'" /> or<br />
			Upload a file: <span id="spanSWFUploadButton2"></span>
			</td>
			<td><div style="width:300px;">Either a full URL to an image file, or use the upload form to select an image file from your computer.</div></td>
			</tr>';			
			
			
			echo '
			</tbody>
			</table>
			<br style="clear:both;" />
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
			
			<h2>Edit products</h2>
			
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
			<thead><tr><th><input type="checkbox" name="selectall" onclick="if (ischecked == false){ SetAllCheckBoxes(\'myForm\', \'myCheckbox\', true);ischecked=true;} else {SetAllCheckBoxes(\'myForm\', \'myCheckbox\', false);ischecked=false;}" /> Action</th><th>Name</th><th>Intro Description</th><th>Description</th><th>Thumbnail</th><th>Price</th><th>Shipping</th><th>Download</th><th>Tags</th><th>Category</th><th>Inventory</th></tr></thead><tbody>
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
		
					echo "<tr><td><input type=\"checkbox\" name=\"myCheckbox[]\" value=\"{$result['primkey']}\" /> [ <a href=\"admin.php?page=wpstorecart-add-products&keytoedit={$result['primkey']}\">Edit</a> | <a onclick=\"if (! confirm('Are you sure you want to delete this product?')) { return false;}\" href=\"admin.php?page=wpstorecart-edit-products&keytodelete={$result['primkey']}\">Delete</a> ]</td><td>".stripslashes($result['name'])."</td><td>".stripslashes(substr($result['introdescription'],0,128))."</td><td>".stripslashes(substr($result['description'],0,128))."</td><td><img src=\"{$result['thumbnail']}\" alt=\"\" style=\"max-width:50px;max-height:50px;\" /></td><td>{$result['price']}</td><td>{$result['shipping']}</td><td>".stripslashes($result['download'])."</td><td>".stripslashes($result['tags'])."</td><td>".stripslashes($currentCat)."</td><td>".stripslashes($result['inventory'])."</td></tr>";
				

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
			
			if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
				echo '<br /><br /><h3>ERROR: wpStoreCart is configured incorrectly.  Visit the wpStoreCart options page and set the main page to the numeric POST ID of a dedicated PAGE that you have created for your arcade.</h3>';
				return false;
			}

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
			<td>
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
			</select></td>
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
			
			echo '<table class="widefat">
			<thead><tr><th> </th><th>Order Status</th><th>Cart Contents</th><th>Processor</th><th>Price</th><th>Shipping</th><th>User</th><th>Email</th><th>Affiliate</th></tr></thead><tbody>
			';


			$grabrecord = "SELECT * FROM `{$table_name}`;";
			
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
			
			if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
				echo '<br /><br /><h3>ERROR: wpStoreCart is configured incorrectly.  Visit the wpStoreCart options page and set the main page to the numeric POST ID of a dedicated PAGE that you have created for your arcade.</h3>';
				return false;
			}

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
				$keytoedit=0;
			} 
			
			
			// To edit a previous product
			$isanedit = false;
			if(!isset($_GET['keytoedit'])) {$_GET['keytoedit'] = 0;}
			if ($_GET['keytoedit']!=0 && is_numeric($_GET['keytoedit'])) {
				$isanedit = true;
				
				if (isset($_POST['wpStoreCartCategory']) && isset($_POST['wpStoreCartCategoryParent'])) {
					$wpStoreCartCategory = $wpdb->escape($_POST['wpStoreCartCategory']);
					$wpStoreCartCategoryParent = $wpdb->escape($_POST['wpStoreCartCategoryParent']);
					$cleanKey = $wpdb->escape($_GET['keytoedit']);
		

					$updateSQL = "
					UPDATE `{$table_name}` SET 
					`parent` = '{$wpStoreCartCategoryParent}', 
					`category` = '{$wpStoreCartCategory}'
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
	
					$devOptions = $this->getAdminOptions();
					


					// Now insert the category into the wpStoreCart database
					$insert = "
					INSERT INTO `{$table_name}` (
					`primkey` ,
					`parent` ,
					`category`
					)
					VALUES (
					NULL , '{$wpStoreCartCategoryParent}', '{$wpStoreCartCategory}'
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
						echo '</strong></p></div><br /><p>Add a new category by <a href="admin.php?page=wpstorecart-categories">clicking here</a>.</p>';
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
				echo '<h2>Add a Category</h2>';
			} else {
				echo '<h2>Edit a Category</h2>Add a new category by <a href="admin.php?page=wpstorecart-categories">clicking here</a>.<br />';
			}
			
			echo '<table class="widefat">
			<thead><tr><th> </th><th>Category <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>The name of the category.  Essentially, if you\'re selling a bunch of hats, make a category called hats.  It\'s that easy!</h3></div></th><th>Parent <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>If you select a parent category, then the category you are creating is a child category.  For example, if you sold red and blue hats, you would select hats as the parent.</h3></div></th></tr></thead><tbody>
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
			
			if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
				echo '<br /><br /><h3>ERROR: wpStoreCart is configured incorrectly.  Visit the wpStoreCart options page and set the main page to the numeric POST ID of a dedicated PAGE that you have created for your arcade.</h3>';
				return false;
			}

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
				echo '<h2>Add a Coupon</h2>';
			} else {
				echo '<h2>Edit a Coupon</h2>Add a new coupon by <a href="admin.php?page=wpstorecart-coupon">clicking here</a>.<br />';
			}
			
			echo '<table class="widefat">
			<thead><tr><th>Coupon Code <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>Don\'t use spaces! This is what people should type or paste into the coupon box during checkout in order to recieve a discount.  As such, this should be a short code, with no spaces, all alpha numeric characters, etc.</h3></div></th><th>Flat Discount <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>A flat amount to deduct when the coupon code is used.  You can combine this with the Pecentage Discount, but for simplicities sake, we recommend choosing either a flat discount or a percentage, but not both.</h3></div></th><!--<th>Percentage Dicount <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3"><h3>The percentage of the price to deduct from the purchase.</h3></div></th>--><th>Description <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4"><h3>Take a note of what your coupon is meant to do by writing a description here.</h3></div></th><th>Product <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5"><h3>The product you want the coupon to apply to.  <!--Set to 0 for the coupon to work on all products in the store.--></h3></div></th><th>Start Date <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6"><h3>The day which the coupon starts working.  Before this date, the coupon is invalid.</h3></div></th><th>Expiration Date <img src="'.WP_PLUGIN_URL.'/wpstorecart/images/help.png" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7"><h3>The date which the coupon code stops working.  After this date, the coupon is invalid.</h3></div></th></tr></thead><tbody>
			';

			
			echo '
			<tr>
			<td><input type="text" name="wpStoreCartcode" style="width: 80%;" value="'.$wpStoreCartcode.'" /></td>
			<td><input type="text" name="wpStoreCartamount" style="width: 80%;" value="'.$wpStoreCartamount.'" /></td>
			<!--<td><input type="text" name="wpStoreCartpercent" style="width: 80%;" value="'.$wpStoreCartpercent.'" /></td>-->
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
			global $wpdb;

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			$devOptions = $this->getAdminOptions();
			
			
			if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
				echo '<br /><br /><h3>ERROR: wpStoreCart is configured incorrectly.  Visit the wpStoreCart options page and set the main page to the numeric POST ID of a dedicated PAGE that you have created for your arcade.</h3>';
				return false;
			}
		
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
			
			
			if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
				echo '<br /><br /><h3>ERROR: wpStoreCart is configured incorrectly.  Visit the wpStoreCart options page and set the main page to the numeric POST ID of a dedicated PAGE that you have created for your arcade.</h3>';
				return false;
			}
		
			$this->spHeader();
			
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
		
			$devOptions = $this->getAdminOptions();
			$table_name = $wpdb->prefix . "wpstorecart_coupons";
			
			if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
				echo '<br /><br /><h3>ERROR: wpStoreCart is configured incorrectly.  Visit the wpStoreCart options page and set the main page to the numeric POST ID of a dedicated PAGE that you have created for your arcade.</h3>';
				return false;
			}
		
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
			
			
			if(!isset($devOptions['mainpage']) || !is_numeric($devOptions['mainpage'])) {
				echo '<br /><br /><h3>ERROR: wpStoreCart is configured incorrectly.  Visit the wpStoreCart options page and set the main page to the numeric POST ID of a dedicated PAGE that you have created for your arcade.</h3>';
				return false;
			}
		
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
			
			// inlinebar
			// 
			$lastrecordssql = "SELECT * FROM `{$table_name_orders}` ORDER BY `date` DESC LIMIT 0, 30";
			$lastrecords = $wpdb->get_results( $lastrecordssql , ARRAY_A );
			
			echo '<ul>';
                        echo '<li>wpStoreCart version '.$wpstorecart_version.'</li>';
			echo '<li><strong>wpStoreCart main page:</strong> <a href="'.$permalink.'" target="_blank">here</a> </li>';
			echo "<li><strong>Completed Orders / Total:</strong>  {$totalrecordsordercompleted}/{$totalrecordsorder} ({$orderpercentage}%) <span class=\"inlinepie\">{$totalrecordsordercompleted},{$totalrecordsorder}</span> </li>";
			echo "<li><strong>Number of Products:</strong> {$totalrecords} </li>";
			//echo "<li><strong>Sales this month:</strong> <span class=\"inlinebar\">1,2,3,4,5,4,3,2,1</span></li>";
			echo '</ul>';
		} 
		
		// Create the function use in the action hook
		function wpstorecart_main_add_dashboard_widgets() {
			wp_add_dashboard_widget('wpstorecart_main_dashboard_widgets', 'wpStoreCart Overview', array(&$this, 'wpstorecart_main_dashboard_widget_function'));	
		} 
		
		
		
		function  addHeaderCode() {
                        
			//echo '<!-- wpStoreCart BEGIN -->';
			wp_enqueue_script('wpsc', WP_PLUGIN_URL.'/wpstorecart/php/wpsc-1.1/wpsc/wpsc-javascript.php', array('jquery'),'1.1' );
                        
			//echo '<!-- wpStoreCart END -->';
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
                                case 'checkout': // Categories shortcode =========================================================
					$is_checkout = true;
					//if(!is_array($wpsc)) {
						require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
						require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');					
					//}
					
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
                                        if(is_numeric($quantity) && is_numeric($thecategory)){
						$sql = "SELECT * FROM `{$table_name}` WHERE `category`={$thecategory} ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT 0, {$quantity};";
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
							
							$output .= '
							<form method="post" action="">
							 
								<input type="hidden" name="my-item-id" value="'.$results[0]['primkey'].'" />
								<input type="hidden" name="my-item-primkey" value="'.$results[0]['primkey'].'" />
								<input type="hidden" name="my-item-name" value="'.$results[0]['name'].'" />
								<input type="hidden" name="my-item-price" value="'.$results[0]['price'].'" />

								<ul class="wpsc-product-info">
								  <li><strong>'.$results[0]['name'].'</strong></li>
								  <li>Price: '.$results[0]['price'].'</li>
								  <li>
									<label>Qty: <input type="text" name="my-item-qty" value="1" size="3" /></label>
								   </li>
								 </ul>
                                                        ';

                                                        if($results[0]['useinventory']==0 || ($results[0]['useinventory']==1 && $results[0]['inventory'] > 0) ) {
                                                            $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button" />';
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
                                            $sql = "SELECT * FROM `{$table_name}` ORDER BY `dateadded` DESC LIMIT 0, 10;";
                                        }
                                        if($devOptions['frontpageDisplays']=='List most popular products') {
                                            $sql = "SELECT * FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT 0, 10;";
                                        }
                                        if($devOptions['frontpageDisplays']=='List all categories') {
                                            $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_orders` WHERE `parent`=0 DESC LIMIT 0, 10;";
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
                                                            $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                            if($devOptions['displayType']=='grid'){
                                                                    $output .= '<div class="wpsc-grid">';
                                                            }
                                                            if($devOptions['displayType']=='list'){
                                                                    $output .= '<div class="wpsc-list">';
                                                            }
                                                            if($usepictures=='true') {
                                                                    $output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['name'].'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
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
                                                                    $output .= '<div class="wpsc-grid">';
                                                            }
                                                            if($devOptions['displayType']=='list'){
                                                                    $output .= '<div class="wpsc-list">';
                                                            }
                                                            if($usepictures=='true') {
                                                                    $output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.$result['name'].'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
                                                            }
                                                            if($usetext=='true') {
                                                                    $output .= '<a href="'.$permalink.'"><h1>'.$result['name'].'</h1></a>';
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
                                                                            <label>Qty: <input type="text" name="my-item-qty" value="1" size="3" /></label>

                                                                    ';

                                                                    if($result['useinventory']==0 || ($result['useinventory']==1 && $result['inventory'] > 0) ) {
                                                                        $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button" />';
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
		
		function my_mainpage_scripts() {
			global $APjavascriptQueue;
		
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
				document.wpstorecartaddproductform.wpStoreCartproduct_download.value = file.name;
			}; 
			
			var productUploadSuccessEventHandler2 = function (file, server_data, receivedResponse) { 
				document.wpstorecartaddproductform.wpStoreCartproduct_thumbnail.value = "'.WP_CONTENT_URL.'/uploads/wpstorecart/" + file.name;
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
					file_size_limit : "200 MB",
					file_types : "*.*",
					file_types_description : "Any file type",
					file_upload_limit : "1",
					file_post_name: "Filedata",					
					button_placeholder_id : "spanSWFUploadButton",
					button_image_url : "'.WP_PLUGIN_URL.'/wpstorecart/images/XPButtonUploadText_61x22.png",
					button_width: 61,
					button_height: 22,
					debug : false, 
					debug_handler : debugSWFUpload,
					file_dialog_complete_handler: beginTheUpload,
					upload_start_handler : productUploadStartEventHandler, 
					upload_success_handler : productUploadSuccessEventHandler,
					upload_error_handler : uploadError
				}; 
				
				var settings_object2 = { 
					upload_url : "'.WP_PLUGIN_URL.'/wpstorecart/php/upload.php", 
					post_params: {"PHPSESSID" : "'.session_id().'"},
					flash_url : "'.get_option( 'siteurl' ).'/wp-includes/js/swfupload/swfupload.swf", 
					file_size_limit : "200 MB",
					file_types : "*.jpg;*.gif;*.png;",
					file_types_description : "Image files",
					file_upload_limit : "1",
					file_post_name: "Filedata",					
					button_placeholder_id : "spanSWFUploadButton2",
					button_image_url : "'.WP_PLUGIN_URL.'/wpstorecart/images/XPButtonUploadText_61x22.png",
					button_width: 61,
					button_height: 22,
					debug : false, 
					debug_handler : debugSWFUpload,
					file_dialog_complete_handler: beginTheUpload,
					upload_start_handler : productUploadStartEventHandler, 
					upload_success_handler : productUploadSuccessEventHandler2,
					upload_error_handler : uploadError
				}; 				
				
				swfu = new SWFUpload(settings_object); 
				swfu2 = new SWFUpload(settings_object2); 
			};
			//]]>
			</script>			
			';

		}			
				
		function placeAdminHeaderCode() {
			global $APjavascriptQueue;
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

                    $output = NULL;
                    $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name}` WHERE `primkey`={$keyToLookup};";
                    $results = $wpdb->get_results( $sql , ARRAY_A );
                    if(isset($results)) {
                        $specific_items = explode(",", $results[0]['cartcontents']);
                        foreach($specific_items as $specific_item) {
                            if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                                $current_item = explode('*', $specific_item);
                                if(isset($current_item[0]) && isset($current_item[1])) {
                                    $sql2 = "SELECT `primkey`, `name`, `download`, `postid` FROM `{$table_name2}` WHERE `primkey`={$current_item[0]};";
                                    $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
                                    if($type=="default" && isset($moreresults[0])) {
                                        $output .= ', ';
                                        if($output==', ') {$output = '';}
                                        $output .= $moreresults[0]['name'];
                                        if($current_item[1]!=1) {
                                            $output .= ' (x'.$current_item[1].')';
                                        }
                                    }
                                    if($type=="download" && isset($moreresults[0])) {
                                        $output .= ', <br />';
                                        if($output==', <br />') {$output = '';}
                                        if($moreresults[0]['download']=='' || $results[0]['orderstatus']!='Completed') {
                                            $output .= $moreresults[0]['name'];
                                        } else {
                                            $output .= '<a href="'.WP_PLUGIN_URL.'/wpstorecart/php/download.php?file='.$moreresults[0]['primkey'].'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png">'.$moreresults[0]['name'].'</a>';
                                        }
                                        if($current_item[1]!=1) {
                                            $output .= ' (x'.$current_item[1].')';
                                        }
                                    }
                                    if($type=="edit" && isset($moreresults[0])) {
                                        $output .= '<div id="delIcon'.$current_item[0].'">'.$moreresults[0]['name'];
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
            if ( !current_user_can( 'edit_user', $user_id ) ) { return false; }
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
	class wpStoreCartCheckoutWidget extends WP_Widget {
		/** constructor */
		function wpStoreCartCheckoutWidget() {
			parent::WP_Widget(false, $name = 'wpStoreCart Cart Contents');
		}

		/** @see WP_Widget::widget */
		function widget($args, $instance) {
			global $wpdb, $cart, $wpsc, $is_checkout;
			$output = NULL;
			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);

			echo $before_widget;
			if ( $title ) { echo $before_title . $title . $after_title; }
			$old_checkout = $is_checkout;
			$is_checkout = false;
			$output = $cart->display_cart($wpsc);
			$is_checkout = $old_checkout;
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
                                $output .= '<ul>';
                                $output .= '<li><a href="'.get_permalink($devOptions['mainpage']).'?wpsc=orders">My Orders &amp; Purchases</a></li>';
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
        global $wpStoreCart;
        if (!isset($wpStoreCart)) {
            return;
        }
        if (function_exists('add_menu_page')) {
			$mainPage = add_menu_page('wpStoreCart - Open Source WP Shopping Cart &amp; eCommerce Plugin', 'wpStoreCart', 'activate_plugins', 'wpstorecart-admin', array(&$wpStoreCart, 'printAdminPageOverview'), WP_PLUGIN_URL.'/wpstorecart/images/controller.png');
			$settingsPage = add_submenu_page('wpstorecart-admin','Settings - wpStoreCart ', 'Settings', 'activate_plugins', 'wpstorecart-settings', array(&$wpStoreCart, 'printAdminPage'));
			$page = add_submenu_page('wpstorecart-admin','Add product - wpStoreCart ', 'Add product', 'activate_plugins', 'wpstorecart-add-products', array(&$wpStoreCart, 'printAdminPageAddproducts'));
			add_submenu_page('wpstorecart-admin','Edit products - wpStoreCart ', 'Edit products', 'activate_plugins', 'wpstorecart-edit-products', array(&$wpStoreCart, 'printAdminPageEditproducts'));
			$categoriesPage = add_submenu_page('wpstorecart-admin','Categories - wpStoreCart ', 'Categories', 'activate_plugins', 'wpstorecart-categories', array(&$wpStoreCart, 'printAdminPageCategories'));
			$ordersPage = add_submenu_page('wpstorecart-admin','Orders &amp; Customers - wpStoreCart', 'Orders', 'activate_plugins', 'wpstorecart-orders', array(&$wpStoreCart, 'printAdminPageOrders'));
			$page2 = add_submenu_page('wpstorecart-admin','Coupons &amp; Discounts - wpStoreCart ', 'Coupons', 'activate_plugins', 'wpstorecart-coupon', array(&$wpStoreCart, 'printAdminPageCoupons'));
			add_submenu_page('wpstorecart-admin','Affiliates - wpStoreCart PRO', 'Affiliates', 'activate_plugins', 'wpstorecart-affiliates', array(&$wpStoreCart, 'printAdminPageAffiliates'));
			add_submenu_page('wpstorecart-admin','Statistics - wpStoreCart PRO', 'Statistics', 'activate_plugins', 'wpstorecart-statistics', array(&$wpStoreCart, 'printAdminPageStatistics'));
			add_submenu_page('wpstorecart-admin','Help - wpStoreCart PRO', 'Help', 'activate_plugins', 'wpstorecart-help', array(&$wpStoreCart, 'printAdminPageHelp'));
			add_action("admin_print_scripts-$settingsPage", array(&$wpStoreCart, 'my_tooltip_script') );
			add_action("admin_print_scripts-$categoriesPage", array(&$wpStoreCart, 'my_tooltip_script') );
			add_action("admin_print_scripts-$ordersPage", array(&$wpStoreCart, 'my_tooltip_script') );
			add_action("admin_print_scripts-$page", array(&$wpStoreCart, 'my_admin_scripts') );
			add_action("admin_print_scripts-$page2", array(&$wpStoreCart, 'admin_script_anytime'), 1);
			add_action("admin_print_scripts-$mainPage", array(&$wpStoreCart, 'my_mainpage_scripts') );
                        

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
	require_once(ABSPATH . 'wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
	if(!isset($_SESSION)) {
		@session_start();
		$cart =& $_SESSION['wpsc']; if(!is_object($cart)) $cart = new wpsc();
	}	



	register_activation_hook(__FILE__, array(&$wpStoreCart, 'wpstorecart_install')); // Install DB schema
	add_action('init', array(&$wpStoreCart, 'register_custom_init')); //
        add_action('wpstorecart/wpstorecart.php',  array(&$wpStoreCart, 'init')); // Create options on activation
	add_action('admin_menu', 'wpStoreCartAdminPanel'); // Create admin panel
	add_action('wp_dashboard_setup', array(&$wpStoreCart, 'wpstorecart_main_add_dashboard_widgets') ); // Dashboard widget
        add_action('wp_head', array(&$wpStoreCart, 'addHeaderCode'), 1); // Place wpStoreCart comment into header
	add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartCheckoutWidget");')); // Register the widget: wpStoreCartTopproductsWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartLoginWidget");')); // Register the widget: wpStoreCartTopproductsWidget
        add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartTopproductsWidget");')); // Register the widget: wpStoreCartTopproductsWidget
	add_action('widgets_init', create_function('', 'return register_widget("wpStoreCartRecentproductsWidget");')); // Register the widget: wpStoreCartRecentproductsWidget
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