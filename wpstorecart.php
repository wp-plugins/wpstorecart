<?php
/*
Plugin Name: IDB Ecommerce (wpStoreCart 5)
Plugin URI: http://indiedevbundle.com/bundles/webdev/idb-ultimate-wordpress-bundle/
Description: <a href="http://indiedevbundle.com/bundles/webdev/idb-ultimate-wordpress-bundle/idb-ecommerce-wordpress-plugin/" target="blank">IDB Ecommerce</a> is a feature packed ecommerce Wordpress plugin that accepts PayPal, Authorize.NET, Skrill & more out of the box. It includes multiple widgets, dashboard widgets, shortcodes, statistics, affiliates, customizable products and works using Wordpress pages to keep everything nice and simple.
Version: 5.0.7
Author: IndieDevBundle.com
Author URI: http://indiedevbundle.com/bundles/webdev/idb-ultimate-wordpress-bundle/
License: LGPL
Text Domain: wpstorecart
*/

/*  
Copyright 2010, 2011, 2012, 2013, 2014, 2015 Jeff Quindlen 

This library is free software; you can redistribute it and/or modify it under the terms 
of the GNU Lesser General Public License as published by the Free Software Foundation; 
either version 2.1 of the License, or (at your option) any later version.

This library is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; 
See the GNU Lesser General Public License for more details. 

You should have received a copy of the GNU Lesser General Public License along with this 
library; if not, write to the Free Software Foundation, Inc., 59 Temple Place, Suite 330, 
Boston, MA 02111-1307 USA 
*/

/**
 * IDB Ecommerce (wpStoreCart 5)
 *
 * @package wpstorecart
 * @version 5.0.7
 * @author IndieDevBundle.com
 * @copyright Copyright &copy; 2010-2015 Jeff Quindlen.  All rights reserved.
 * @link http://indiedevbundle.com/bundles/webdev/idb-ultimate-wordpress-bundle/
 * @global string $wpstorecart_version - The current version of wpStoreCart as a string
 * @global integer $wpstorecart_version_int -  M_m_u_ which is 2 digits for Major, minor, and updates, so version 2.0.14 would be 200014
 * @global boolean $wpstorecart_benchmark - whether or not benchmarking is on
 */
global $wpstorecart_version, $wpstorecart_version_int, $wpstorecart_benchmark, $wpstorecart_settings_obj, $wpstorecart_upload_dir, $wpsc_wordpress_upload_dir, $wpsc_testing_mode, $wp_roles;


/* Global variables: */
$wpstorecart_version = '5.0';
$wpstorecart_version_int = 500007; // Mm_p__ which is 1 digit for Major, 2 for minor, and 3 digits for patch updates, so version 2.0.14 would be 200014
$wpstorecart_benchmark = false; // This does a basic benchmark on how long wpStoreCart takes to execute
$wpsc_testing_mode = false; // Depreciated in 4.6.0
$wpsc_wordpress_upload_dir = wp_upload_dir();
$wpstorecart_upload_dir = $wpsc_wordpress_upload_dir['basedir'].'/wpstorecart';
//@include_once($wpstorecart_upload_dir.'/customize.php'); // <-- In this file, end users can over ride virtually any wpStoreCart function

if(@is_object($wp_roles)) {@$wp_roles->add_cap( 'administrator', 'manage_wpstorecart' ); } // Administrator always has manage_wpstorecart capability 
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/log/log.php'); // Logging is loaded first
if($wpstorecart_benchmark) {
    require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/benchmarks/benchmarks.php'); // Loads the wpStoreCart benchmarks
}
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/language/language.php'); // Allows multiple translations
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/actions/actions.php'); // Loads all the wpStoreCart actions

wpsc_loaded(); // Action hook, once we've established some of the basics, in case something needs to go here

/**
 * Initialization, installation, upgrades, and settings
 */
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/email/email.php'); // Makes email functions available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/alerts/alerts.php'); // Alerts are loaded early on so that they can be used to report compatibility, installation, and settings issues
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/compatibility/compatibility.php'); // Insures that wpStoreCart has a compatible environment, and makes adjustments if necessary
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/installer/installer.php'); // Checks to see if installation or upgrades are necessary and then installs
register_activation_hook(__FILE__, 'wpscInstall'); // Install wpStoreCart DB schema
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/settings/settings.php'); // For checking the store's settings
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/wizard/wizard.php'); // For easily configuring and changing the settings
$wpstorecart_settings_obj = new wpscSettings();

/**
 * Store stuff, like products, shopping cart, etc
 */
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/groupdiscounts/groupdiscounts.php'); // Makes group discount functions available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/products/products.php'); // Makes products available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/registration/registration.php'); // Makes registration available (needed for checkout)
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/orders/orders.php'); // Makes order data and functions available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/shipping/shipping.php'); // Makes shipping available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/shipping/flatrateshipping.php'); // Makes flat rate shipping available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/shipping/freeshipping.php'); // Makes free shipping available
require_once(WP_PLUGIN_DIR . '/wpstorecart/plugins/wpsc-ups-shipping/wpsc-ups-shipping.php'); // Makes UPS shipping available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/taxes/taxes.php'); // Makes taxes available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/cart/cart.php'); // Makes shopping cart and checkout available

/**
 * User land stuff, admin pages, admin bar, shortcodes, widgets
 */
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/admin/admin.php'); // Makes admin functions available, provides the admin panel if the user has proper permissions
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/error/error.php'); // Provides non-PHP related errors, as in wpStoreCart functionality/configuration error messages
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/menubar/menubar.php'); // Makes the admin bar available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/shortcodes/shortcodes.php'); // Makes shortcodes available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/widgets/widgets.php'); // Makes widgets available

/**
 * Plugins that are now included by default in IDB Ecommerce 5
 */
require_once(WP_PLUGIN_DIR . '/wpstorecart/plugins/wpsc-user-customize-products/wpsc-user-customize-products.php'); // Makes User Customized Products available
register_activation_hook(__FILE__, 'wpscCustomizeProductInstall'); // Install wpStoreCart User Customized Products DB schema
require_once(WP_PLUGIN_DIR . '/wpstorecart/plugins/wpsc-moneybookers/wpsc-moneybookers.php');
require_once(WP_PLUGIN_DIR . '/wpstorecart/plugins/wpsc-authorize-net/wpsc-authorize-net.php');
require_once(WP_PLUGIN_DIR . '/wpstorecart/plugins/wpsc-qbms/wpsc-qbms.php');
require_once(WP_PLUGIN_DIR . '/wpstorecart/plugins/wpsc-2co/wpsc-2co.php');

nocache_headers();

wpsc_end(); // Last action hook available.

?>