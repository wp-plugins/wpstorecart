<?php
/*
Plugin Name: wpStoreCart - Ajax Ecommerce
Plugin URI: http://wpstorecart.com/
Description: <a href="http://wpstorecart.com/" target="blank">wpStoreCart</a> is a powerful, yet simple to use ecommerce Wordpress plugin that accepts PayPal & more out of the box. It includes multiple widgets, dashboard widgets, shortcodes, and works using Wordpress pages to keep everything nice and simple.
Version: 3.9.13
Author: wpStoreCart, LLC
Author URI: http://wpstorecart.com/
License: LGPL
Text Domain: wpstorecart
*/

/*  
Copyright 2010, 2011, 2012, 2013 wpStoreCart, LLC  (email : admin@wpstorecart.com)

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
 * wpStoreCart 3
 *
 * @package wpstorecart
 * @version 3.9.13
 * @author wpStoreCart, LLC <admin@wpstorecart.com>
 * @copyright Copyright &copy; 2010-2013 wpStoreCart, LLC.  All rights reserved.
 * @link http://wpstorecart.com/
 * @global string $wpstorecart_version - The current version of wpStoreCart as a string
 * @global integer $wpstorecart_version_int -  M_m_u_ which is 2 digits for Major, minor, and updates, so version 2.0.14 would be 200014
 * @global boolean $wpstorecart_benchmark - whether or not benchmarking is on
 */
global $wpstorecart_version, $wpstorecart_version_int, $wpstorecart_benchmark, $wpstorecart_settings_obj, $wpstorecart_upload_dir, $wpsc_wordpress_upload_dir, $wpsc_testing_mode, $wp_roles;


/* Global variables: */
$wpstorecart_version = '3.9.13';
$wpstorecart_version_int = 309013; // Mm_p__ which is 1 digit for Major, 2 for minor, and 3 digits for patch updates, so version 2.0.14 would be 200014
$wpstorecart_benchmark = false; // This does a basic benchmark on how long wpStoreCart takes to execute
$wpsc_testing_mode = false; // Set to true if debugging.  Note, that this is for wpStoreCart core developers, and is not meant for addon developers
$wpsc_wordpress_upload_dir = wp_upload_dir();
$wpstorecart_upload_dir = $wpsc_wordpress_upload_dir['basedir'].'/wpstorecart';
@include_once($wpstorecart_upload_dir.'/customize.php'); // <-- In this file, end users can over ride virtually any wpStoreCart function

if($wpsc_testing_mode) {error_reporting(E_ALL);}

if(@is_object($wp_roles)) {@$wp_roles->add_cap( 'administrator', 'manage_wpstorecart' ); } // Administrator always has manage_wpstorecart capability 
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/log/log.php'); // Logging is loaded first
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/benchmarks/benchmarks.php'); // Loads the wpStoreCart benchmarks
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/debugger/debugger.php'); // Provides debugging
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
//require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/piwik/piwik.php'); // Makes Piwik tracking tools available
require_once(WP_PLUGIN_DIR . '/wpstorecart/wpstorecart/shipping/shipping.php'); // Makes shipping available
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
if(@!isset($_SESSION)) {
    @session_start();
}
print_r($_SESSION);
*/

nocache_headers();

wpsc_end(); // Last action hook available.

?>