<?php
/*
Plugin Name: wpStoreCart
Plugin URI: http://wpstorecart.com/
Description: <a href="http://wpstorecart.com/" target="blank">wpStoreCart</a> is a powerful, yet simple to use e-commerce Wordpress plugin that accepts PayPal & more out of the box. It includes multiple widgets, dashboard widgets, shortcodes, and works using Wordpress pages to keep everything nice and simple.
Version: 2.4.2
Author: wpStoreCart.com
Author URI: http://wpstorecart.com/
License: LGPL
*/

/*  
Copyright 2010, 2011 wpStoreCart.com  (email : admin@wpstorecart.com)

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

/**
 * wpStoreCart
 *
 * @package wpstorecart
 * @version 2.4.2
 * @author wpStoreCart.com <admin@wpstorecart.com>
 * @copyright Copyright &copy; 2010, 2011 wpStoreCart.com.  All rights reserved.
 * @link http://wpstorecart.com/
 *
 */

if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {
    require_once(ABSPATH . 'wp-includes/pluggable.php');
}

/**
 * @global object $wpStoreCart - The wpStoreCart object
 * @global object $cart - The shopping cart object
 * @global string $wpstorecart_version - The current version of wpStoreCart as a string
 * @global integer $wpstorecart_version_int -  M_m_u_ which is 2 digits for Major, minor, and updates, so version 2.0.14 would be 200014
 * @global boolean $testing_mode - Enables or disables testing mode.  Should be set to false unless using on a test site, with test data, with no actual customers
 * @global boolean $wpsc_error_reporting - Enables or disables the advanced error reporting utilities included with wpStoreCart.  Should be set to false unless using on a test site, with test data, with no actual customers
 * @global string $wpstorecart_db_version - Enables or disable testing mode.  Should be set to false unless using on a test site, with test data, with no actual customers
 */
global $wpStoreCart, $cart, $wpsc, $wpstorecart_version, $wpstorecart_version_int, $testing_mode, $wpstorecart_db_version, $wpsc_error_reporting, $wpsc_error_level, $wpsc_cart_type;

//Global variables:
$wpstorecart_version = '2.4.2';
$wpstorecart_version_int = 204002; // Mm_p__ which is 1 digit for Major, 2 for minor, and 3 digits for patch updates, so version 2.0.14 would be 200014
$wpstorecart_db_version = $wpstorecart_version_int; // Legacy, used to check db version
$testing_mode = false; // Enables or disables testing mode.  Should be set to false unless using on a test site, with test data, with no actual customers
$wpsc_error_reporting = false; // Enables or disables the advanced error reporting utilities included with wpStoreCart.  Should be set to false unless using on a test site, with test data, with no actual customers
$wpsc_error_level = E_ALL; // The error level to use if wpsc_error_reporting is set to true.  Default is E_ALL
$APjavascriptQueue = NULL;
$wpsc_cart_type = 'session';

/**
 * Let's make sure the manage_wpstorecart role is added to the administrator.
 */
if(is_user_logged_in()) {
    if(is_super_admin() || is_admin() ) {
        global $wp_roles;
        $wp_roles->add_cap( 'administrator', 'manage_wpstorecart' );
    }
}
if($wpsc_error_reporting==true) {

        error_reporting($wpsc_error_level); // Let's manually set the PHP error reporting level

        if (!function_exists('getUncaughtEx')) {
                /**
                     * wpStoreCart advanced error reporting
                     *
                     * @return NULL
                     */
                function getUncaughtEx() {
                        // if error has been supressed with an @
                        if (error_reporting() == 0) {
                                return;
                        }
                        $arr = get_defined_vars();
                        echo '<div style="padding:25px 25px 25px 25px;border:1px solid #999999;font-family:Courier New,Terminal,Fixedsys,Courier;position:absolute;top:1px;left:1px;z-index:999999;">';
                        echo '<h1>wpscAdvancedError Error Report</h1>';
                        echo '<br />' . $additionalMessage. ' <br /><br />';
                        echo '<b>ERROR MESSAGE:</b> ' . $e->getMessage() . '<br /><br />';
                        echo '<b>CODE:</b> ' . $e->getCode() . '<br /><br />';
                        echo '<b>FILENAME:</b> ' . $e->getFile() . '<br /><br />';
                        echo '<b>ON LINE #</b>' . $e->getLine() . '<br /><br />';
                        echo '<b>BACKTRACE:</b> <pre>'; print_r($e->getTrace()); echo '</pre><br /><br />';
                        echo '<b>BACKTRACE: </b>'. $e->getTraceAsString(). '<br /><br />';
                        echo '<b>VARIABLES:</b> <pre>';print_r(array_keys(get_defined_vars())); echo '</pre><br /><br />';
                        echo '<b>VARIABLE VALUES:</b> <pre>';print_r($arr); echo '</pre><br /><br />';
                        echo '</div>';
                        die('FATAL ERROR: UNCAUGHT EXCEPTION!');
                }
                set_exception_handler('getUncaughtEx');
        }

        if (!function_exists('wpscErrorReport')) {
        /**
             * wpStoreCart advanced error reporting
             *
             * @return NULL
             */
            function wpscErrorReport($e, $additionalMessage = NULL) {

                    // if error has been supressed with an @
                    if (error_reporting() == 0) {
                            return;
                    }

                            $arr = get_defined_vars();
                            echo '<div style="padding:25px 25px 25px 25px;border:1px solid #999999;font-family:Courier New,Terminal,Fixedsys,Courier;background:#eff5fe;color:#000000;position:absolute;top:1px;left:1px;z-index:999999;">';
                            echo '<h1>wpscAdvancedError Error Report</h1>';
                            echo '<br />' . $additionalMessage. ' <br /><br />';
                            echo '<b>ERROR MESSAGE:</b> ' . $e->getMessage() . '<br /><br />';
                            echo '<b>CODE:</b> ' . $e->getCode() . '<br /><br />';
                            echo '<b>FILENAME:</b> ' . $e->getFile() . '<br /><br />';
                            echo '<b>ON LINE #</b>' . $e->getLine() . '<br /><br />';
                            echo '<b>BACKTRACE:</b> <pre>'; print_r($e->getTrace()); echo '</pre><br /><br />';
                            echo '<b>BACKTRACE:</b> '. $e->getTraceAsString(). '<br /><br />';
                            echo '<b>VARIABLES:</b> <pre>';print_r(array_keys(get_defined_vars())); echo '</pre><br /><br />';
                            echo '<b>VARIABLE VALUES:</b> <pre>';print_r($arr); echo '</pre><br /><br />';
                            echo '</div>';

            }
        }

        if (!function_exists('myErrorHandler')) {
              /**
                 * wpStoreCart error handler
                 *
                 * @param integer $errno
                 * @param string $errstr
                 * @param string $errfile
                 * @param integer $errline
                 * @return boolean
                 */
                function myErrorHandler($errno, $errstr, $errfile, $errline) {
                        try {

                                // if error has been supressed with an @
                                if (error_reporting() == 0) {
                                        return;
                                }

                                switch ($errno) {
                                    case E_USER_ERROR:
                                            throw new Exception("<b>ERROR</b> [$errno] $errstr<br />\n  Fatal error on line $errline in file $errfile , PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n Aborting...<br />\n");
                                            break;

                                    case E_USER_WARNING:
                                            throw new Exception("<b>WARNING</b> [$errno] $errstr<br />\n  Warning on line $errline in file $errfile , PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n Aborting...<br />\n");
                                            break;

                                    case E_USER_NOTICE:
                                            throw new Exception("<b>NOTICE</b> [$errno] $errstr<br />\n  Notice on line $errline in file $errfile , PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n Aborting...<br />\n");
                                            break;

                                    case E_PARSE:
                                            throw new Exception("<b>PARSE</b> [$errno] $errstr<br />\n  Parsing issue on line $errline in file $errfile , PHP " . PHP_VERSION . " (" . PHP_OS . ")<br />\n Aborting...<br />\n");
                                            break;

                                }
                                return true;
                        } catch(Exception $e) {
                                wpscErrorReport($e, 'EXCEPTION.');
                                die;
                        }
                }
                set_error_handler('myErrorHandler'); 
        }
} else {
        error_reporting(0); // Turns error reporting off
        restore_exception_handler(); // Restore regular exception handler
        restore_error_handler(); // Restore error handler
}


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
 * copyr
 *
 * Copy a file, or recursively copy a folder and its contents
 * The public domain license applies only to the copyr function
 *
 * @author      Aidan Lister <aidan@php.net>
 * @version     1.0.1
 * @link        http://aidanlister.com/2004/04/recursively-copying-directories-in-php/
 * @param       string   $source    Source path
 * @param       string   $dest      Destination path
 * @return      bool     Returns TRUE on success, FALSE on failure
 * @license     public domain
 */
if(!function_exists('copyr')) {
    function copyr($source, $dest) {
        // Check for symlinks
        if (is_link($source)) {
            return symlink(readlink($source), $dest);
        }

        // Simple copy for a file
        if (is_file($source)) {
            return copy($source, $dest);
        }

        // Make destination directory
        if (!is_dir($dest)) {
            mkdir($dest);
        }

        // Loop through the folder
        $dir = dir($source);
        while (false !== $entry = $dir->read()) {
            // Skip pointers
            if ($entry == '.' || $entry == '..') {
                continue;
            }

            // Deep copy directories
            copyr("$source/$entry", "$dest/$entry");
        }

        // Clean up
        $dir->close();
        return true;
    }
}

// Copy the theme if needed
/**
if(!file_exists(WP_CONTENT_DIR.'/themes/wpStoreCartTheme/version.php')) {
    @copyr(WP_CONTENT_DIR.'/plugins/wpstorecart/wpStoreCartTheme/', WP_CONTENT_DIR.'/themes/wpStoreCartTheme/');
} else {
    require_once(WP_CONTENT_DIR.'/themes/wpStoreCartTheme/version.php');
    global $wpsc_default_theme_version;
    $wpsc_default_theme_old_version = $wpsc_default_theme_version;
    require_once(WP_CONTENT_DIR.'/plugins/wpstorecart/wpStoreCartTheme/version.php');
    global $wpsc_default_theme_version;
    $wpsc_default_theme_new_version = $wpsc_default_theme_version;
    if($wpsc_default_theme_new_version > $wpsc_default_theme_old_version) {
        @copyr(WP_CONTENT_DIR.'/plugins/wpstorecart/wpStoreCartTheme/', WP_CONTENT_DIR.'/themes/wpStoreCartTheme/');
    }
}
*/

// Try and fix things for people who have magic quotes on
if (@get_magic_quotes_gpc()) {
    $process = array(&$_GET, &$_POST, &$_COOKIE, &$_REQUEST);
    while (list($key, $val) = each($process)) {
        foreach ($val as $k => $v) {
            unset($process[$key][$k]);
            if (is_array($v)) {
                $process[$key][stripslashes($k)] = $v;
                $process[] = &$process[$key][stripslashes($k)];
            } else {
                $process[$key][stripslashes($k)] = stripslashes($v);
            }
        }
    }
    unset($process);
}

	
if (!class_exists("wpStoreCart")) {
    /**
     * wpStoreCart
     *
     * The main class of the wpStoreCart application
     *
     * @package wpstorecart
     * @license lgpl 2.0
     *
     */
    class wpStoreCart {
        /**
         * 
         * @var string $adminOptionsName Just the name of the wpStoreCart options in the Wordpress database
         */
	var $adminOptionsName = "wpStoreCartAdminOptions";

        /**
         *
         * @var array $wpStoreCartSettings The actual wpStoreCart options.
         */
        var $wpStoreCartSettings = null;

        /**
         *
         * @var object $wpStoreCartRegistrationFields The registration fieldss
         */
        var $wpStoreCartRegistrationFields = null;

        /**
         * wpStoreCart() Constructor Method
         *
         * The initial constructor call that is initialized when the wpStoreCart object is invoked
         *
         * @global object $wpdb
         * @global string $wpstorecart_db_version
         */
        function wpStoreCart() { //constructor
            global $wpdb, $wpstorecart_db_version, $wpstorecart_version_int, $wp_roles;

            $devOptions = $this->getAdminOptions();

            // New roles and capabilities code added in 2.3.7 (was intended for 3.0, but came a but early!)
            add_role( 'wpstorecart_manager', 'wpStoreCart Manager', array( 'manage_wpstorecart', 'read', 'upload_files', 'publish_posts', 'edit_published_posts', 'publish_pages', 'edit_publish_pages' ) );
            $wp_roles->add_cap( 'administrator', 'manage_wpstorecart' );
            $wp_roles->add_cap( 'wpstorecart_manager', 'read' );
            $wp_roles->add_cap( 'wpstorecart_manager', 'upload_files' );
            $wp_roles->add_cap( 'wpstorecart_manager', 'publish_pages' );
            $wp_roles->add_cap( 'wpstorecart_manager', 'publish_posts' );
            $wp_roles->add_cap( 'wpstorecart_manager', 'manage_wpstorecart' );

            /**
             * ShareYourCart integration
             */
            if($devOptions['shareyourcart_secret']=='') { // Let's try to activate ShareYourCart.com the first time it is installed
                $devOptions['shareyourcart_secret']='93d66d12-7c6c-11e0-8e44-0018518d6618'; // If we haven't registered, let's try to!
                update_option($this->adminOptionsName, $devOptions);
                require_once(WP_PLUGIN_DIR.'/wpstorecart/php/shareyourcart/shareyourcart-sdk.php');
                if(trim($devOptions['shareyourcart_clientid'])=='' ||  trim($devOptions['shareyourcart_appid'])=='') {
                    if (!function_exists('curl_init')) {
                        // We'll stop trying to use ShareYourCart if cURL is not availble.
                    } else {
                        ob_start();
                        try {
                            $new_client = shareyourcart_registerAPI(trim($devOptions['shareyourcart_secret']), trim('http://'.$_SERVER['HTTP_HOST']), trim($devOptions['wpStoreCartEmail']));
                        } catch (Exception $e) {
                            ob_end_clean();
                            echo $e->getMessage();
                            $new_client = false;
                        }
                        if(!$new_client) {
                            if(is_admin()) {
                                $devOptions['shareyourcart_failedreg'] = 'true';
                            }
                        } else {
                          $devOptions['shareyourcart_clientid'] = $new_client['client_id'];
                          $devOptions['shareyourcart_appid'] = $new_client['app_key'];
                          $devOptions['shareyourcart_activate'] = 'true';
                          $devOptions['shareyourcart_failedreg'] = 'false';
                        }
                        update_option($this->adminOptionsName, $devOptions);
                    }
                }
            }
            // End ShareYourCart integration

            $devOptions['run_updates']='true';
            if (intval(str_replace('.','',$devOptions['database_version']))==$wpstorecart_version_int) { // This will force wpStoreCart to run the Update method if we're not using the latest version. the intval/str_replace stuff is there because we used to use a 2.1.9 format, and now we use 201009 format.
                $devOptions['run_updates']='false';
            }

            // UPDATES ARE PUSHED HERE.  Moving the updates to it's method now allows us to force an update at anytime
            if($devOptions['run_updates']=='true') {
                $this->wpscUpdate();
            } // End updates

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

        /**
         * __destruct() Destructor Method
         *
         * Restores the error reporting after wpStoreCart is done
         *
         */
       function __destruct() {
           	error_reporting(0); // Turns error reporting off
		restore_exception_handler(); // Restore regular exception handler
		restore_error_handler(); // Restore error handler
       }

        /**
         * wpscUpdate() Method
         *
         * This method handles all the special actions that need to be taken when upgrading from an older version of wpStoreCart (no matter how old) to the latest version.
         *
         * @global object $wpdb
         * @global string $wpstorecart_db_version
         */
       function wpscUpdate() {
            global $wpdb, $wpstorecart_db_version, $wpstorecart_version_int;

            $devOptions = $this->getAdminOptions();

            $this->add_column_if_not_exist($wpdb->prefix . "wpstorecart_products", "donation", "BOOLEAN NOT NULL DEFAULT '0'" );
            $this->add_column_if_not_exist($wpdb->prefix . "wpstorecart_products", "weight", "INT( 7 ) NOT NULL DEFAULT  '0'" );
            $this->add_column_if_not_exist($wpdb->prefix . "wpstorecart_products", "length", "INT( 7 ) NOT NULL DEFAULT  '0'" );
            $this->add_column_if_not_exist($wpdb->prefix . "wpstorecart_products", "width", "INT( 7 ) NOT NULL DEFAULT  '0'" );
            $this->add_column_if_not_exist($wpdb->prefix . "wpstorecart_products", "height", "INT( 7 ) NOT NULL DEFAULT  '0'" );
            $this->add_column_if_not_exist($wpdb->prefix . "wpstorecart_products", "discountprice", "DECIMAL(9,2) NOT NULL" );

            // Upgrade the database schema if they're running 2.0.2 or below:
            if($devOptions['database_version']==NULL) { // 2.0.2 - Database schema update for version 2.0.1 and below
                $table_name = $wpdb->prefix . "wpstorecart_categories";
                $sql = "ALTER TABLE `{$table_name}` ADD `thumbnail` VARCHAR( 512 ) NOT NULL, ADD `description` TEXT NOT NULL, ADD `postid` INT NOT NULL ";
                $results = $wpdb->query( $sql );
            }

          /**
             * Let's make sure the the meta table exists for those who are upgrading from a previous version
             */
           $table_name = $wpdb->prefix . "wpstorecart_meta";
           if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {

                $sql = "
                        CREATE TABLE IF NOT EXISTS {$table_name} (
                        `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                        `value` TEXT NOT NULL,
                        `type` VARCHAR(32) NOT NULL,
                        `foreignkey` INT NOT NULL
                        );
                        ";


                $results = $wpdb->query( $sql );
            }

          /**
             * Let's make sure the the av table exists for those who are upgrading from a previous version
             */
           $table_name = $wpdb->prefix . "wpstorecart_av";
           if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {

                $sql = "
                        CREATE TABLE IF NOT EXISTS {$table_name} (
                            `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                            `productkey` INT NOT NULL ,
                            `values` TEXT NOT NULL ,
                            `price` DECIMAL( 9, 2 ) NOT NULL
                        );
                        ";


                $results = $wpdb->query( $sql );
            }


            // This little block of code insures that we don't run this update routine again until the next time wpStoreCart is updated.
            $devOptions['database_version'] = $wpstorecart_version_int;
            $devOptions['run_updates']='false'; // These updates only need to be ran once.
            update_option('wpStoreCartAdminOptions', $devOptions);
       }

       /**
        *
        * wpStoreCart non-fatal error messages
        *
        * @param string $theError
        * @param mixed $variables
        * @return string
        */
       function wpscError($theError='unknown', $variables=NULL) {
           $output = "<div id='wpsc-warning' class='updated fade'><p>";
           if($variables=='custom') {
               $output .= $theError;
           }
           if($theError=='nopage') {
               $output .= __('<div style="float:left;margin-top:10px;"><a href="'.plugins_url('/php/wizard/wizard_setup_01.php' , __FILE__).'" rel="#overlay" style="text-decoration:none;"><img src="'.plugins_url('/images/wizard/button_setup_wizard2.png' , __FILE__).'" /></a></div><div style="float:left;width:77%;margin-left:10px;"><strong>wpStoreCart is almost ready! Configuration required.</strong>  The <i>easiest</i> and <i>best way</i> to configure wpStoreCart is to <a href="'.plugins_url('/php/wizard/wizard_setup_01.php' , __FILE__).'" rel="#overlay">click here</a> to run the <a href="'.plugins_url('/php/wizard/wizard_setup_01.php' , __FILE__).'" rel="#overlay">Setup Wizard</a>.  You can also automatically create a "main page" and a "checkout page" for your store by <a href="admin.php?page=wpstorecart-admin&wpscaction=createpages">clicking here</a>, or you can create your own pages first &amp; then visit <a href="admin.php?page=wpstorecart-settings">the settings page</a> to specify which pre-existing pages to use.  See <a href="http://wpstorecart.com/documentation/error-messages/" target="_blank">this help entry</a> for more details.</div><br style="clear:both;" />');
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
           if($theError=='no_curl' ){
               $output .= __('<strong>wpStoreCart has detected that CURL is not enabled.</strong>  CURL is required if you wish to allow customers to calculate shipping costs with USPS, FedEx, and UPS.  Please have a system administrator install and/or configure CURL so that you can use those features.  Until that happens, you must use either flat rate shipping or have PayPal or your payment processor calculate shipping for you.  <a href="?page=wpstorecart-admin&wpscaction=removecurl">Click here to remove this message</a>');
           }
           if($theError=='uspsnotconfigured' ){
               $output .= __('<strong>wpStoreCart has noticed a serious problem!</strong>  You\'ve selected to offer shipping through the United States Postal Service (USPS) but did not enter a USPS API key.  If you already have a USPS API key, please visit the <a href="?page=wpstorecart-settings&theCurrentTab=tab6">wpStoreCart > Settings > Shipping admin page</a>, enter the API name in the form, and click the "Update Settings" button.  If you do not have a USPS API key, visit this URL now: <a href="https://secure.shippingapis.com/registration/" target="_blank">https://secure.shippingapis.com/registration/</a>, complete the registration process by filling out the form and click the "Submit" button, then visit the <a href="?page=wpstorecart-settings&theCurrentTab=tab6">wpStoreCart > Settings > Shipping admin page</a> enter the API name in the form, and click the "Update Settings" button. If you only want to use flat rate shipping for all products (regardless of shipping provider) then simply disable USPS as a shipping service to dismiss this message, and use the flat rate shipping option instead.');
           }
           $output .= "</p></div>";
           return $output;
       }

        function wpscErrorNoCURL() {
            echo $this->wpscError('no_curl');
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

        function wpscErrorUSPS() {
            echo $this->wpscError('uspsnotconfigured');
        }

        function save_error(){
            global $testing_mode;
            if($testing_mode==true) {
                $devOptions = $this->getAdminOptions();
                $devOptions['plugin_error']=ob_get_contents();
                update_option($this->adminOptionsName, $devOptions);
            }
        }


        function register_custom_init() {
            global $testing_mode;


            $devOptions = $this->getAdminOptions();
            if($devOptions['checkcurl']=='true') {
                if (@!extension_loaded('curl')) {
                    add_action('admin_notices', array(&$this, 'wpscErrorNoCURL'));
                } else {
                    if (@!function_exists('curl_init')) {
                        add_action('admin_notices', array(&$this, 'wpscErrorNoCURL'));
                    }
                }
            }

            if($testing_mode==true) {
                add_action('admin_notices', array(&$this, 'wpscErrorTestingMode'));
            }

            if($devOptions['enableusps']=='true' && $devOptions['uspsapiname']=='') {
                add_action('admin_notices', array(&$this, 'wpscErrorUSPS'));
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

        }

		function  init() {
                    $this->getAdminOptions();
                }

                /**
                 *
                 * Checks to see if a column exists in a mysql table, if not, it creates it
                 *
                 * @global object $wpdb
                 * @param <type> $db
                 * @param <type> $column
                 * @param <type> $column_attr
                 */
		function add_column_if_not_exist($db, $column, $column_attr = "VARCHAR( 255 ) NULL" ){
			global $wpdb;
			$exists = false;
			$columns = $wpdb->get_results( "show columns from $db" , ARRAY_A );
			foreach($columns as $c) {
				if($c['Field'] == $column){
					$exists = true;
					break;
				}
			}      
			if(!$exists){
				$wpdb->query("ALTER TABLE `$db` ADD `$column`  $column_attr");
			}
		}		

                function spSettings() {
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
                                margin: 0 0 -5px 8px;
                                padding: 0;
                                float: left;
                                list-style: none;
                                height: 40px;
                                max-height: 40px;
                                width: 100%;
                                width:812px;
                                min-width:812px;
                            position:relative;
                            z-index:1;
                        }
                        ul.tabs li {
                                float: left;
                                margin: 0;
                                padding: 0;
                                height: 39px; /*--Subtract 1px from the height of the unordered list--*/
                                line-height: 39px; /*--Vertically aligns the text within the tab--*/
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
                                padding: 10px;

                        }
			</style>';


                }

		function spHeader() {
                        global $wpstorecart_version_int, $testing_mode;


                        $devOptions = $this->getAdminOptions();
                        $logofile = 'logo.png';
                        
                        $logofilelarge = 'logo_large.png';
                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-affiliates-pro/saStoreCartPro/affiliates.pro.php')) {
                            if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php')) {
                                if(file_exists(WP_PLUGIN_DIR.'/wpsc-statistics-pro/saStoreCartPro/statistics.pro.php')) {
                                    $logofile = 'logo_pro.png';
                                    $logofilelarge = 'logo_pro_large.png';
                                }
                            }
                        }
                        if(get_option('db_version') >= 18226) {$logofile = 'controller.png';}

                        echo'
                            
			<!-- overlayed element -->
                        <div class="apple_overlay" id="overlay">

                                <!-- the external content is loaded inside this tag -->
                                <div class="contentWrap"></div>

                        </div>
                        
			<!--<div class="wrap">-->
			<div style="padding: 0px 10px 10px 10px;">
                        ';

                        if($devOptions['menu_style']=='classic' || $devOptions['menu_style']=='both') {
                            if($logofilelarge != 'logo_pro_large.png') {
                                echo '
                            <div style="float:right;">

                                    <a style="position:absolute;top:50px;margin-left:-222px;" href="http://wpstorecart.com/store/business-support-wpstorecart-pro/" target="_blank"><img src="'.plugins_url('/images/hire_us.png' , __FILE__).'" alt="wpstorecart" /></a>
                            </div>

                            ';
                            }
                        }


                        echo ' <br style="clear:both;" />';
                        if(($devOptions['menu_style']=='version3' || $devOptions['menu_style']=='both') && get_option('db_version') < 18226) {
                            echo '<script type="text/javascript">
                                jQuery("#header-logo").css("background-image", "url('.plugins_url('/images/'.$logofile , __FILE__).')");
                                jQuery("#header-logo").css( {width : "126px", height : "31px"} );
                                </script>
                            ';
                         }
                        if(get_option('db_version') >= 18226) {
                            echo '<script type="text/javascript">
                                jQuery("#header-logo").css("background-image", "url('.plugins_url('/images/'.$logofile , __FILE__).')");
                                </script>
                            ';
                        }

                        echo '

                        <ul id="wpsc-dashboard-drop-down" style="display:none;" class="jeegoocontext cm_default">
                            <li><a href="admin.php?page=wpstorecart-admin" class="spmenu">Overview</a></li>
                            <li><a href="'.plugins_url('/php/wizard/wizard_setup_01.php' , __FILE__).'" rel="#overlay" class="spmenu">Setup Wizard</a></li>
                            <li><a href="'.plugins_url('/php/wizard/wizard_setup_04.php' , __FILE__).'" rel="#overlay" class="spmenu">Payments Wizard</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/application_form_edit.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-diagnostics" class="spmenu">Diagnostics</a></li>
                        </ul>
                        
                        <script type="text/javascript">
                            //<![CDATA[
                                jQuery("#wpsc-dashboard-link").jeegoocontext("wpsc-dashboard-drop-down", {openBelowContext: true, event: \'mouseover\', ignoreHeightOverflow: true, submenuLeftOffset: -1, startTopOffset: -1, fadeIn: 0, autoHide: true});
                            //]]>
                        </script>

                        <ul id="wpsc-settings-drop-down" style="display:none;" class="jeegoocontext cm_default">
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/application_form_edit.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab1" class="spmenu">General</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/email.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab2" class="spmenu">E-Mail</a></li>';
                            echo '<li class="icon"><span class="icon"><img src="'.plugins_url('/images/css.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab3" class="spmenu">Display</a></li>';

                            if($devOptions['storetype']!='Digital Goods Only') { // Hide shipping if digital only store
                                echo '<li class="icon"><span class="icon"><img src="'.plugins_url('/images/package_go.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab6" class="spmenu">Shipping</a></li>';
                            }
                            echo '<li class="icon"><span class="icon"><img src="'.plugins_url('/images/creditcards.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab4" class="spmenu">Payment</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/text_padding_top.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab5" class="spmenu">Language</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/user_suit.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab7" class="spmenu">Customers</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/money_delete.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab8" class="spmenu">Taxes</a></li>
                        </ul>

                        <script type="text/javascript">
                            //<![CDATA[
                                jQuery("#wpsc-settings-link").jeegoocontext("wpsc-settings-drop-down", {openBelowContext: true, event: \'mouseover\', ignoreHeightOverflow: true, submenuLeftOffset: -1, startTopOffset: -1, fadeIn: 0, autoHide: true});
                            //]]>
                        </script>

                        <ul id="wpsc-add-product-drop-down" style="display:none;" class="jeegoocontext cm_default">
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/basket_add.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-add-products" class="spmenu">Add Product</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/basket_edit.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-edit-products" class="spmenu">Edit Products</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/table.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-categories" class="spmenu">Categories</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/server_go.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-import" class="spmenu">Import/Export</a></li>
                        </ul>

                        <script type="text/javascript">
                            //<![CDATA[
                                jQuery("#wpsc-add-product-link").jeegoocontext("wpsc-add-product-drop-down", {openBelowContext: true, event: \'mouseover\', ignoreHeightOverflow: true, submenuLeftOffset: -1, startTopOffset: -1, fadeIn: 0, autoHide: true});
                            //]]>
                        </script>

                        <ul id="wpsc-orders-drop-down" style="display:none;" class="jeegoocontext cm_default">
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/cart_go.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-orders" class="spmenu">All Orders</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/bullet_green.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-orders&show=completed" class="spmenu">Completed Orders</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/bullet_orange.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-orders&show=pending" class="spmenu">Pending Orders</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/bullet_red.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-orders&show=refunded" class="spmenu">Refunded Orders</a></li>
                        </ul>

                        <script type="text/javascript">
                            //<![CDATA[
                                jQuery("#wpsc-orders-link").jeegoocontext("wpsc-orders-drop-down", {openBelowContext: true, event: \'mouseover\', ignoreHeightOverflow: true, submenuLeftOffset: -1, startTopOffset: -1, fadeIn: 0, autoHide: true});
                            //]]>
                        </script>

                        <ul id="wpsc-marketing-drop-down" style="display:none;" class="jeegoocontext cm_default">
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/money.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-coupon" class="spmenu">Coupons</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/shareyourcart.png' , __FILE__).'" /></span><a href="admin.php?page=wpstorecart-shareyourcart" class="spmenu">ShareYourCart&#8482;</a></li>
                        </ul>

                        <script type="text/javascript">
                            //<![CDATA[
                                jQuery("#wpsc-marketing-link").jeegoocontext("wpsc-marketing-drop-down", {openBelowContext: true, event: \'mouseover\', ignoreHeightOverflow: true, submenuLeftOffset: -1, startTopOffset: -1, fadeIn: 0, autoHide: true});
                            //]]>
                        </script>

                        <ul id="contextual-help-link-drop-down" style="display:none;" class="jeegoocontext cm_default">
                            <li><a href="http://wpstorecart.com/forum/"  target="_blank">Support Forum</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/application_form_edit.png' , __FILE__).'" /></span><a href="http://wpstorecart.com/documentation/initial-settings/"  target="_blank">Initial Settings</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/basket_add.png' , __FILE__).'" /></span><a href="http://wpstorecart.com/documentation/adding-editing-products/"   target="_blank">Products</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/text_padding_top.png' , __FILE__).'" /></span><a href="http://wpstorecart.com/documentation/widgets/"   target="_blank">Widgets</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/money.png' , __FILE__).'" /></span><a href="http://wpstorecart.com/documentation/coupons/"  target="_blank">Coupons</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/text_padding_top.png' , __FILE__).'" /></span><a href="http://wpstorecart.com/documentation/shortcodes/"  target="_blank">Shortcodes</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/cross.png' , __FILE__).'" /></span><a href="http://wpstorecart.com/documentation/error-messages/"   target="_blank">Error Messages</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/css.png' , __FILE__).'" /></span><a href="http://wpstorecart.com/documentation/styles-designs/"   target="_blank">Styles &amp; Design</a></li>
                            <li class="icon"><span class="icon"><img src="'.plugins_url('/images/help.png' , __FILE__).'" /></span><a href="http://wpstorecart.com/faq/"   target="_blank">FAQ</a></li>
                            <li><a href="http://wpstorecart.com/help-support/"  target="_blank">More Help</a></li>

                        </ul>

                        <script type="text/javascript">
                            //<![CDATA[
                                jQuery("#contextual-help-link").jeegoocontext("contextual-help-link-drop-down", {openBelowContext: true, event: \'mouseover\', ignoreHeightOverflow: true, submenuLeftOffset: -1, startTopOffset: -1, fadeIn: 0, autoHide: true});
                            //]]>
                        </script>



';

                    global $testing_mode;
                    if($testing_mode==true && trim($devOptions['plugin_error'])!='') {
                        echo $this->wpscError('TESTING MODE: '.$devOptions['plugin_error'],'custom');
                    }

		}		
		
		
		//Returns an array of admin options
        function getAdminOptions() {
		
            $apAdminOptions = array('mainpage' => '',
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
                                    'checkmoneyordertext' => 'Please send a check or money order for the above amount to:<br /><br /><strong>My Business Name<br />1234 My Address, Suite ABC<br />New York, NY 24317, USA</strong><br /><br />Please allow 4 to 6 weeks for delivery.',
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
                                    'emailonpurchase' => 'Dear [customername], thanks for your recent order from [sitename].  Your order has been submitted to our staff for approval.  This process can take as little as an hour to as long as a few weeks depending on how quickly your payment clears, and whether there are other issues which may cause a delay.  You can view your order status here: [downloadurl] ',
                                    'emailonapproval' => 'Dear [customername], thanks again for your recent order from [sitename].  This email is to inform you that your order has been approved.  For physical products, this does not mean that they have been shipped yet; as you will get another email when the order is shipped.  If you ordered a digital download, your download is now available.  You can view your order status here: [downloadurl] ',
                                    'emailonshipped'  => 'Dear [customername], thanks again for your recent order from [sitename].  This email is to inform you that your order has been shipped.',
                                    'emailsig' => 'Thanks again, [sitename] Management',
                                    'emailserialnumber' => 'Dear [customername], thanks for your recent order from [sitename].  You can view your order status here: [downloadurl]   A serial number for [productname] has been issued to you.  Please keep this email for future reference.  Your serial number is [serialnumber] ',
                                    'cart_title' => 'Shopping Cart',
                                    'single_item' => 'Item',
                                    'multiple_items' => 'Items',
                                    'currency_symbol' => '$',
                                    'currency_symbol_right' => ' USD',
                                    'subtotal' => 'Subtotal',
                                    'update_button' => 'update',
                                    'checkout_button' => 'checkout',
                                    'currency_code' => 'USD',
                                    'checkout_checkmoneyorder_button' => 'Checkout with Check/Money Order',
                                    'checkout_paypal_button' => 'Checkout with PayPal',
                                    'checkout_authorizenet_button' => 'Checkout with Authorize.NET',
                                    'checkout_2checkout_button' => 'Checkout with 2CheckOut',
                                    'checkout_libertyreserve_button' => 'Checkout with Liberty Reserve',
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
                                    'total' => 'Total',
                                    'shipping' => 'Shipping',
                                    'requireregistration' => 'true',
                                    'enablecoupons' => 'true',
                                    'login' => 'Login',
                                    'register' => 'Register',
                                    'logout' => 'Logout',
                                    'username' => 'Username',
                                    'password' => 'Password',
                                    'email' => 'Email',
                                    'myordersandpurchases' => 'My Orders &amp; Purchases',
                                    'required_symbol' => '*',
                                    'required_help' => '* - Fields with an asterick are required.',
                                    'flatrateshipping' => 'individual',
                                    'flatrateamount' => '0.00',
                                    'calculateshipping' => 'Calculate Shipping',
                                    'itemsperpage' => '10',
                                    'libertyreservesecretword' => '',
                                    'guestcheckout' => 'Guest Checkout',
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
                                    'checkoutimages' => 'false',
                                    'checkoutimagewidth' => '25',
                                    'checkoutimageheight' => '25',
                                    'checkoutlinktoproduct' => 'false',
                                    'displaypriceonview' => 'false',
                                    'menu_style' => 'version3',
                                    'admin_capability' => 'manage_options',
                                    'orders_profile' => 'display',
                                    'allowqbms' => 'false',
                                    'qbms_ticket' => '',
                                    'qbms_login' => '',
                                    'qbms_testingmode' => 'false',
                                    'cc_name' => 'Full name on card',
                                    'cc_number' => 'Credit Card #',
                                    'cc_expires' => 'Expires',
                                    'cc_expires_month' => 'Month',
                                    'cc_expires_year' => 'Year',
                                    'cc_address' => 'Address of Credit Card',
                                    'cc_postalcode' => 'Zipcode of Credit Card',
                                    'cc_cvv' => 'CVV',
                                    'checkout_xhtml_type' => 'div',
                                    'disable_inline_styles' => 'false',
                                    'field_order_0' => '0',
                                    'field_order_1' => '1',
                                    'field_order_2' => '2',
                                    'field_order_3' => '3',
                                    'field_order_4' => '4',
                                    'pcicompliant' => 'false',
                                    'paypalipnurl' => WP_PLUGIN_URL.'/wpstorecart/php/payment/paypal_ipn.php',
                                    'button_classes_addtocart' => '',
                                    'button_classes_checkout' => '',
                                    'button_classes_meta' => '',
                                    'trial_period_1' => 'Trial Period:',
                                    'trial_period_2' => '2nd Trial Period:',
                                    'subscription_price' => 'Subscription Price:',
                                    'for' => 'for',
                                    'afterwards' => 'afterwards',
                                    'every' => 'every',
                                    'free' => 'FREE',
                                    'day' => 'day(s)',
                                    'week' => 'week(s)',
                                    'month' => 'month(s)',
                                    'year' => 'year(s)',
                                    'buy_now' => 'Buy Now'
                                    );

            
            if($this->wpStoreCartSettings!=NULL) {
                $devOptions = $this->wpStoreCartSettings;
                $devOptions['menu_style']='version3';
            } else {
                $devOptions = get_option($this->adminOptionsName);
            }

            // Generates the wpStoreCart Desktop Alert API keys if they were not present
            if($devOptions['wpsc_api_key']=='') {
                $apAdminOptions['wpsc_api_key'] = md5(rand(0,255).rand(rand(0,255),rand(256,512)));
            }
            if($devOptions['wpsc_secret_hash']=='') {
                $apAdminOptions['wpsc_secret_hash'] = md5(rand(0,255).rand(rand(0,255),rand(256,512)));
            }

            if (!empty($devOptions)) {
                foreach ($devOptions as $key => $option) {
                    $apAdminOptions[$key] = $option;
                }
            }
            $apAdminOptions['menu_style']='version3'; // This hardcodes the new menu system as the default
            update_option($this->adminOptionsName, $apAdminOptions);
            
            return $apAdminOptions;
        }


        /**
         * wpstorecart_alert() Method
         *
         *
         */
        function wpstorecart_alert() {
            global $wpdb;
            echo '<div style="float:right;width:20%;max-width:20%;padding: 12px;border-left: 1px solid #e3e3e3;display:inline;">';
            $logofile = 'logo.png';

            $wpstorecartpro = false;
            $logofilelarge = 'logo_large.png';
            if(file_exists(WP_PLUGIN_DIR.'/wpsc-affiliates-pro/saStoreCartPro/affiliates.pro.php')) {
                if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php')) {
                    if(file_exists(WP_PLUGIN_DIR.'/wpsc-statistics-pro/saStoreCartPro/statistics.pro.php')) {
                        $logofile = 'logo_pro.png';
                        $logofilelarge = 'logo_pro_large.png';
                        $wpstorecartpro = true;
                    }
                }
            }
            echo '<center><a href="http://wpstorecart.com" target="_blank"><img src="'.plugins_url('/images/'.$logofilelarge , __FILE__).'" alt="wpstorecart" /></a></center>';
            
            $devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
                    exit();
            }

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

            $dailyAverage = $totalearned / 30;

            // inlinebar
            //
            $lastrecordssql = "SELECT * FROM `{$table_name_orders}` ORDER BY `date` DESC LIMIT 0, 30";
            $lastrecords = $wpdb->get_results( $lastrecordssql , ARRAY_A );

            echo '<ul>';
            echo '<li>30 days: <strong><span>'.$devOptions['currency_symbol'].number_format($totalearned).$devOptions['currency_symbol_right'].'</span></strong> ('.$devOptions['currency_symbol'].number_format($dailyAverage).$devOptions['currency_symbol_right'].'/day)</li>';
            echo '<li>All Time: <strong><span>'.$devOptions['currency_symbol'].number_format($allTimeGrossRevenue).$devOptions['currency_symbol_right'].'</span></strong></li>';
            echo "<li><span style=\"float:left;padding:0 0 0 10px;\"><strong>Sales last 30 days:</strong> <br /><img src=\"http://chart.apis.google.com/chart?chxt=y&chbh=a,2&chs=200x50&cht=bvg&chco=224499&chds=0,{$highestNumber}&chd=t:0";while($currentDay != $enddate) {echo $salesOnDay[$currentDay].',';$dayAgo--;$currentDay = date("Ymd", strtotime("{$dayAgo} days ago"));} echo"0\" alt=\"\" /></span><div style=\"clear:both;\"></div></li>";
            echo '</ul>';

            if(!$wpstorecartpro) {
            echo '<h3>More Ecommerce Tools</h3>';
            echo '
            <center>
            <img src="'.plugins_url('/images/products/pro.png' , __FILE__).'" alt="wpstorecart" onclick="jQuery(\'#buypro\').submit();" style="cursor:pointer;"  />
            <p style="font-size:10px">Includes 5+ payment gateways, affiliate program, stats, business support &amp; more.  Order now!</p>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post" id="buypro" name="buypro">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="6PZ2X87LHLQV8">
            <table>
            <tr><td><input type="hidden" name="on0" value="License">License</td></tr><tr><td><select name="os0">
                    <option value="Single Domain">Single Domain $29.99</option>
                    <option value="2 Domains">2 Domains $39.99</option>
                    <option value="10 Domains">10 Domains $119.99</option>
                    <option value="Unlimited Domains">Unlimited Domains $209.99</option>
            </select> </td></tr>
            </table>
            <input type="hidden" name="currency_code" value="USD">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_buynow_SM.gif" border="0"  alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
            </center>';
            
            echo '
            <h3>Support Our Development</h3>
            If you have found this plugin useful, PayPal donations are always appreciated:<center>
            <form action="https://www.paypal.com/cgi-bin/webscr" method="post">
            <input type="hidden" name="cmd" value="_s-xclick">
            <input type="hidden" name="hosted_button_id" value="9LWHPC2QC4F7Q">
            <input type="image" src="https://www.paypalobjects.com/en_US/i/btn/btn_donateCC_LG.gif" border="0" name="submit" alt="PayPal - The safer, easier way to pay online!">
            <img alt="" border="0" src="https://www.paypalobjects.com/en_US/i/scr/pixel.gif" width="1" height="1">
            </form>
            </center>or Flattr us to help keep development going strong:<center>
            <script type="text/javascript">
            /* <![CDATA[ */
                (function() {
                    var s = document.createElement(\'script\'), t = document.getElementsByTagName(\'script\')[0];
                    s.type = \'text/javascript\';
                    s.async = true;
                    s.src = \'http://api.flattr.com/js/0.6/load.js?mode=auto\';
                    t.parentNode.insertBefore(s, t);
                })();
            /* ]]> */
            </script>
            <a class="FlattrButton" style="display:none;" rev="flattr;button:compact;" href="http://wpstorecart.com"></a>
            <noscript><a href="http://flattr.com/thing/348418/wpStoreCart" target="_blank">
            <img src="http://api.flattr.com/button/flattr-badge-large.png" alt="Flattr this" title="Flattr this" border="0" /></a></noscript>
            </center>
            Or at the very least we\'d appreciate it if you give us a <a href="http://wordpress.org/extend/plugins/wpstorecart/">good rating at the plugin directory</a>, link to our site, blog, tweet, or post about wpStoreCart.
            ';
            }
echo '
<h3>Get Support</h3>
<ul>
<li> <img src="'.plugins_url('/images/help.png' , __FILE__).'" /> <a href="http://wpstorecart.com/documentation/" target="_blank">Documentation</a></li>
<li> <img src="'.plugins_url('/images/table.png' , __FILE__).'" /> <a href="http://wpstorecart.com/forums/" target="_blank">Support Forums</a></li>
<li> <img src="'.plugins_url('/images/bug.png' , __FILE__).'" /> <a href="http://wpstorecart.com/bugtracker/thebuggenie/wpstorecart/issues/new/bugreport" target="_blank">Report a Bug</a></li>';
if($wpstorecartpro) {
    echo '<li> <img src="'.plugins_url('/images/email.png' , __FILE__).'" /> <a href="http://wpstorecart.com/support/contact-us/" target="_blank">Contact Us</a></li>';
} else {
    echo '<li> <img src="'.plugins_url('/images/money.png' , __FILE__).'" /> <a href="http://wpstorecart.com/support/contact-us/" target="_blank">Presale Question</a></li>';
}
echo '</ul>
';
            echo '<h3>wpStoreCart News</h3>';
            include_once(ABSPATH . WPINC . '/feed.php');
            $rss = fetch_feed('http://wpstorecart.com/category/blog/feed/');
            if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly
                // Figure out how many total items there are, but limit it to 2.
                $maxitems = $rss->get_item_quantity(5);

                // Build an array of all the items, starting with element 0 (first element).
                $rss_items = $rss->get_items(0, $maxitems);
            endif;
            echo '<ul>';
                if ($maxitems == 0) {
                    echo '<li>No items.</li>';
                } else {
                    // Loop through each feed item and display each item as a hyperlink.
                    foreach ( $rss_items as $item ) {
                        echo '<li><a target="_blank" href="'. $item->get_permalink() .'" title="Posted "'.$item->get_date('j F Y | g:i a').'">'.$item->get_title().'</a></li>';
                    }
                }
            echo '</ul>';
            echo '</div>';

        }

        function printAdminPageDiagnostics() {
            $devOptions = $this->getAdminOptions();
            if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart'))
            {
              wp_die( __('wpStoreCart: You do not have sufficient permissions to access this page.') );
            }

            $this->spHeader();
            $this->wpstorecart_alert();

            

            echo '<div style="width:75%;max-width:75%;float:left;"><img src="'.plugins_url('/images/development.png' , __FILE__).'" alt="" style="float:left;" /><h2 style="font-size:32px;">&nbsp;&nbsp;Diagnostics <a href="http://wpstorecart.com/documentation/diagnostics/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2><br style="clear:both;" />';

            echo 'WARNING: Do not post this code in a public place.  The code below contains sensitive information about your site.  Only share this information with parties you trust, such as wpStoreCart staff, your web developers, etc.  The information here may be useful for technicians to diagnose what may be causing an issue that you have.  To view this data, run it through the base64_decode() PHP function.
                <br /><br /><a href="" onclick="jQuery(\'#wpsc-diag\').select();return false;">Select All...</a><br />';

            echo '<textarea readonly="readonly" id="wpsc-diag" onclick="jQuery(\'#wpsc-diag\').select();return false;" onfocus="jQuery(\'#wpsc-diag\').select();return false;" style="border:1px solid #666;width:850px;max-width:850px;height:250px;overflow:scroll;">';
            $arr = get_defined_vars();

            $output = '<pre>';
            $output .= "===== wpStoreCart settings =====
";
            foreach ($arr as $key=>$value){
                $output .= $key ." => ". $value ."
";
                if(is_array($value)) {
                    $output .= print_r($value, true);
                }
            }

            $output .= "===== php.ini settings =====
";
            $output .= print_r(ini_get_all(), true);
            $output .= "===== your local settings =====
";
            $output .= "Your OS: ".php_uname()."
";
            $output .= "===== apache modules =====
";
            $output .= print_r(apache_get_modules(), true);
            $output .= "===== php version=====
";
            $output .= "PHP version: ".phpversion(). "
                ";
            $output .= "===== stream wrappers =====
";
            $output .= print_r(stream_get_wrappers(), true);
            $output .= "===== stream transports =====
";
            $output .= print_r(stream_get_transports(), true);
            $output .= "===== stream filters =====
";
            $output .= print_r(stream_get_filters(), true);
            $output .= "===== Wordpress and Global settings =====
";
            $output .= print_r($GLOBALS, true).'</pre>';

            echo base64_encode($output);
            echo '  </textarea>';
            echo '</div>';

        }
		
		
        /**
         *
         * Prints out the admin page
         *
         * @global object $wpdb
         * @global <type> $wp_roles
         * @global <type> $devOptions
         * @global <type> $devOptions
         */
        function printAdminPage() {
			global $wpdb;

                $devOptions = $this->getAdminOptions();
                if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) 
                {
                  wp_die( __('wpStoreCart: You do not have sufficient permissions to access this page.') );
                }


                        
                        
                        // Added in 2.3.2, so that if we change the mainpage, all the products will change their parent to the new mainpage as well
                        if(isset($_POST['wpStoreCartmainpage']) &&  ($devOptions['mainpage']!=$_POST['wpStoreCartmainpage'])) {
                            $table_name_products = $wpdb->prefix . "wpstorecart_products";
                            $grabpostid = "SELECT `postid` FROM `{$table_name_products}` ;";
                            $results_renames = $wpdb->get_results( $grabpostid , ARRAY_A );
                            $sql_to_run = "UPDATE `{$wpdb->prefix}posts` SET `post_parent`='".$wpdb->escape($_POST['wpStoreCartmainpage'])."' WHERE ";
                            if(isset($results_renames)) {
                                $firstime = true;
                                foreach ($results_renames as $results_rename) {
                                    if(!$firstime) {
                                        $sql_to_run .= "OR ";
                                    }
                                    $sql_to_run .= "`ID`='{$results_rename['postid']}' ";
                                    $firstime = false;
                                }
                                $sql_to_run .= ';';
                                if(!$firstime) {
                                    $wpdb->query($sql_to_run);
                                }

                            }
                        }
		
			if (isset($_POST['update_wpStoreCartSettings'])) {
				if (isset($_POST['wpStoreCartmainpage'])) {
					$devOptions['mainpage'] = $wpdb->escape($_POST['wpStoreCartmainpage']);
				} 		
				if (isset($_POST['checkoutpage'])) {
					$devOptions['checkoutpage'] = $wpdb->escape($_POST['checkoutpage']);
					$devOptions['checkoutpageurl'] = get_permalink($devOptions['checkoutpage']);
				}
				if (isset($_POST['orderspage'])) {
					$devOptions['orderspage'] = $wpdb->escape($_POST['orderspage']);
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
				if (isset($_POST['emailserialnumber'])) {
					$devOptions['emailserialnumber'] = $wpdb->escape($_POST['emailserialnumber']);
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
				if (isset($_POST['shipping_zip_origin'])) {
 					$devOptions['shipping_zip_origin'] = $wpdb->escape($_POST['shipping_zip_origin']);
				}
				if (isset($_POST['enableusps'])) {
 					$devOptions['enableusps'] = $wpdb->escape($_POST['enableusps']);
				}
				if (isset($_POST['enableups'])) {
 					$devOptions['enableups'] = $wpdb->escape($_POST['enableups']);
				}
				if (isset($_POST['enablefedex'])) {
 					$devOptions['enablefedex'] = $wpdb->escape($_POST['enablefedex']);
				}
				if (isset($_POST['storetype'])) {
 					$devOptions['storetype'] = $wpdb->escape($_POST['storetype']);
				}
				if (isset($_POST['uspsapiname'])) {
 					$devOptions['uspsapiname'] = $wpdb->escape($_POST['uspsapiname']);
				}

				if (isset($_POST['displayshipping'])) {
 					$devOptions['displayshipping'] = $wpdb->escape($_POST['displayshipping']);
				}
				if (isset($_POST['displaysubtotal'])) {
 					$devOptions['displaysubtotal'] = $wpdb->escape($_POST['displaysubtotal']);
				}
				if (isset($_POST['displaytotal'])) {
 					$devOptions['displaytotal'] = $wpdb->escape($_POST['displaytotal']);
				}
				if (isset($_POST['total'])) {
 					$devOptions['total'] = $wpdb->escape($_POST['total']);
				}
				if (isset($_POST['shipping'])) {
 					$devOptions['shipping'] = $wpdb->escape($_POST['shipping']);
				}

				if (isset($_POST['requireregistration'])) {
 					$devOptions['requireregistration'] = $wpdb->escape($_POST['requireregistration']);
				}
				if (isset($_POST['enablecoupons'])) {
 					$devOptions['enablecoupons'] = $wpdb->escape($_POST['enablecoupons']);
				}
				if (isset($_POST['login'])) {
 					$devOptions['login'] = $wpdb->escape($_POST['login']);
				}
				if (isset($_POST['register'])) {
 					$devOptions['register'] = $wpdb->escape($_POST['register']);
				}

				if (isset($_POST['logout'])) {
 					$devOptions['logout'] = $wpdb->escape($_POST['logout']);
				}
				if (isset($_POST['username'])) {
 					$devOptions['username'] = $wpdb->escape($_POST['username']);
				}
				if (isset($_POST['password'])) {
 					$devOptions['password'] = $wpdb->escape($_POST['password']);
				}
				if (isset($_POST['email'])) {
 					$devOptions['email'] = $wpdb->escape($_POST['email']);
				}
				if (isset($_POST['myordersandpurchases'])) {
 					$devOptions['myordersandpurchases'] = $wpdb->escape($_POST['myordersandpurchases']);
				}

				if (isset($_POST['required_symbol'])) {
 					$devOptions['required_symbol'] = $wpdb->escape($_POST['required_symbol']);
				}
				if (isset($_POST['required_help'])) {
 					$devOptions['required_help'] = $wpdb->escape($_POST['required_help']);
				}

				if (isset($_POST['flatrateshipping'])) {
 					$devOptions['flatrateshipping'] = $wpdb->escape($_POST['flatrateshipping']);
				}
				if (isset($_POST['flatrateamount'])) {
 					$devOptions['flatrateamount'] = $wpdb->escape($_POST['flatrateamount']);
				}
				if (isset($_POST['calculateshipping'])) {
 					$devOptions['calculateshipping'] = $wpdb->escape($_POST['calculateshipping']);
				}
				if (isset($_POST['itemsperpage'])) {
 					$devOptions['itemsperpage'] = $wpdb->escape($_POST['itemsperpage']);
				}
				if (isset($_POST['allowlibertyreserve'])) {
 					$devOptions['allowlibertyreserve'] = $wpdb->escape($_POST['allowlibertyreserve']);
				}
				if (isset($_POST['allowqbms'])) {
 					$devOptions['allowqbms'] = $wpdb->escape($_POST['allowqbms']);
				}
				if (isset($_POST['qbms_ticket'])) {
 					$devOptions['qbms_ticket'] = $wpdb->escape($_POST['qbms_ticket']);
				}
				if (isset($_POST['qbms_login'])) {
 					$devOptions['qbms_login'] = $wpdb->escape($_POST['qbms_login']);
				}
				if (isset($_POST['qbms_testingmode'])) {
 					$devOptions['qbms_testingmode'] = $wpdb->escape($_POST['qbms_testingmode']);
				}

				if (isset($_POST['libertyreserveaccount'])) {
 					$devOptions['libertyreserveaccount'] = $wpdb->escape($_POST['libertyreserveaccount']);
				}
				if (isset($_POST['libertyreservestore'])) {
 					$devOptions['libertyreservestore'] = $wpdb->escape($_POST['libertyreservestore']);
				}
				if (isset($_POST['checkout_libertyreserve_button'])) {
 					$devOptions['checkout_libertyreserve_button'] = $wpdb->escape($_POST['checkout_libertyreserve_button']);
				}
				if (isset($_POST['libertyreservesecretword'])) {
 					$devOptions['libertyreservesecretword'] = $wpdb->escape($_POST['libertyreservesecretword']);
				}
				if (isset($_POST['guestcheckout'])) {
 					$devOptions['guestcheckout'] = $wpdb->escape($_POST['guestcheckout']);
				}
				if (isset($_POST['useimagebox'])) {
 					$devOptions['useimagebox'] = $wpdb->escape($_POST['useimagebox']);
				}
				if (isset($_POST['showproductgallery'])) {
 					$devOptions['showproductgallery'] = $wpdb->escape($_POST['showproductgallery']);
				}
				if (isset($_POST['showproductgallerywhere'])) {
 					$devOptions['showproductgallerywhere'] = $wpdb->escape($_POST['showproductgallerywhere']);
				}

				if (isset($_POST['displaytaxes'])) {
 					$devOptions['displaytaxes'] = $wpdb->escape($_POST['displaytaxes']);
				}
				if (isset($_POST['tax'])) {
 					$devOptions['tax'] = $wpdb->escape($_POST['tax']);
				}
				if (isset($_POST['checkoutimages'])) {
 					$devOptions['checkoutimages'] = $wpdb->escape($_POST['checkoutimages']);
				}
				if (isset($_POST['checkoutimagewidth'])) {
 					$devOptions['checkoutimagewidth'] = $wpdb->escape($_POST['checkoutimagewidth']);
				}
				if (isset($_POST['checkoutimageheight'])) {
 					$devOptions['checkoutimageheight'] = $wpdb->escape($_POST['checkoutimageheight']);
				}
				if (isset($_POST['checkoutlinktoproduct'])) {
 					$devOptions['checkoutlinktoproduct'] = $wpdb->escape($_POST['checkoutlinktoproduct']);
				}
				if (isset($_POST['displaypriceonview'])) {
 					$devOptions['displaypriceonview'] = $wpdb->escape($_POST['displaypriceonview']);
				}
				if (isset($_POST['menu_style'])) {
 					$devOptions['menu_style'] = $wpdb->escape($_POST['menu_style']);
				}
				if (isset($_POST['orders_profile'])) {
 					$devOptions['orders_profile'] = $wpdb->escape($_POST['orders_profile']);
				}
				if (isset($_POST['cc_name'])) {
 					$devOptions['cc_name'] = $wpdb->escape($_POST['cc_name']);
				}
				if (isset($_POST['cc_number'])) {
 					$devOptions['cc_number'] = $wpdb->escape($_POST['cc_number']);
				}
				if (isset($_POST['cc_expires'])) {
 					$devOptions['cc_expires'] = $wpdb->escape($_POST['cc_expires']);
				}
				if (isset($_POST['cc_expires_month'])) {
 					$devOptions['cc_expires_month'] = $wpdb->escape($_POST['cc_expires_month']);
				}
				if (isset($_POST['cc_expires_year'])) {
 					$devOptions['cc_expires_year'] = $wpdb->escape($_POST['cc_expires_year']);
				}
				if (isset($_POST['cc_address'])) {
 					$devOptions['cc_address'] = $wpdb->escape($_POST['cc_address']);
				}
				if (isset($_POST['cc_postalcode'])) {
 					$devOptions['cc_postalcode'] = $wpdb->escape($_POST['cc_postalcode']);
				}
				if (isset($_POST['cc_cvv'])) {
 					$devOptions['cc_cvv'] = $wpdb->escape($_POST['cc_cvv']);
				}
				if (isset($_POST['checkout_xhtml_type'])) {
 					$devOptions['checkout_xhtml_type'] = $wpdb->escape($_POST['checkout_xhtml_type']);
				}
				if (isset($_POST['disable_inline_styles'])) {
 					$devOptions['disable_inline_styles'] = $wpdb->escape($_POST['disable_inline_styles']);
				}
				if (isset($_POST['field_order_0'])) {
 					$devOptions['field_order_0'] = $wpdb->escape($_POST['field_order_0']);
				}
				if (isset($_POST['field_order_1'])) {
 					$devOptions['field_order_1'] = $wpdb->escape($_POST['field_order_1']);
				}
				if (isset($_POST['field_order_2'])) {
 					$devOptions['field_order_2'] = $wpdb->escape($_POST['field_order_2']);
				}
				if (isset($_POST['field_order_3'])) {
 					$devOptions['field_order_3'] = $wpdb->escape($_POST['field_order_3']);
				}
				if (isset($_POST['field_order_4'])) {
 					$devOptions['field_order_4'] = $wpdb->escape($_POST['field_order_4']);
				}
				if (isset($_POST['pcicompliant'])) {
 					$devOptions['pcicompliant'] = $wpdb->escape($_POST['pcicompliant']);
				}
                                if (isset($_POST['paypalipnurl'])) {
 					$devOptions['paypalipnurl'] = $wpdb->escape($_POST['paypalipnurl']);
				}

                                if (isset($_POST['button_classes_addtocart'])) {
 					$devOptions['button_classes_addtocart'] = $wpdb->escape($_POST['button_classes_addtocart']);
				}
                                if (isset($_POST['button_classes_checkout'])) {
 					$devOptions['button_classes_checkout'] = $wpdb->escape($_POST['button_classes_checkout']);
				}
                                if (isset($_POST['button_classes_meta'])) {
 					$devOptions['button_classes_meta'] = $wpdb->escape($_POST['button_classes_meta']);
				}

                                if (isset($_POST['trial_period_1'])) {
 					$devOptions['trial_period_1'] = $wpdb->escape($_POST['trial_period_1']);
				}
                                if (isset($_POST['trial_period_2'])) {
 					$devOptions['trial_period_2'] = $wpdb->escape($_POST['trial_period_2']);
				}
                                if (isset($_POST['subscription_price'])) {
 					$devOptions['subscription_price'] = $wpdb->escape($_POST['subscription_price']);
				}
                                if (isset($_POST['for'])) {
 					$devOptions['for'] = $wpdb->escape($_POST['for']);
				}
                                if (isset($_POST['afterwards'])) {
 					$devOptions['afterwards'] = $wpdb->escape($_POST['afterwards']);
				}
                                if (isset($_POST['every'])) {
 					$devOptions['every'] = $wpdb->escape($_POST['every']);
				}
                                if (isset($_POST['free'])) {
 					$devOptions['free'] = $wpdb->escape($_POST['free']);
				}

                                if (isset($_POST['day'])) {
 					$devOptions['day'] = $wpdb->escape($_POST['day']);
				}
                                if (isset($_POST['week'])) {
 					$devOptions['week'] = $wpdb->escape($_POST['week']);
				}
                                if (isset($_POST['month'])) {
 					$devOptions['month'] = $wpdb->escape($_POST['month']);
				}
                                if (isset($_POST['year'])) {
 					$devOptions['year'] = $wpdb->escape($_POST['year']);
				}
                                if (isset($_POST['buy_now'])) {
 					$devOptions['buy_now'] = $wpdb->escape($_POST['buy_now']);
				}

				if (isset($_POST['admin_capability'])) {
                                        global $wp_roles;
 					$devOptions['admin_capability'] = $wpdb->escape($_POST['admin_capability']);
                                        if($devOptions['admin_capability']=='administrator') {
                                            $wp_roles->remove_cap( 'editor', 'manage_wpstorecart' );
                                            $wp_roles->remove_cap( 'author', 'manage_wpstorecart' );
                                            $wp_roles->remove_cap( 'contributor', 'manage_wpstorecart' );
                                        }
                                        if($devOptions['admin_capability']=='editor') {
                                            $wp_roles->add_cap( 'administrator', 'manage_wpstorecart' );
                                            $wp_roles->add_cap( 'editor', 'manage_wpstorecart' );
                                            $wp_roles->remove_cap( 'author', 'manage_wpstorecart' );
                                            $wp_roles->remove_cap( 'contributor', 'manage_wpstorecart' );
                                        }
                                        if($devOptions['admin_capability']=='author') {
                                            $wp_roles->add_cap( 'administrator', 'manage_wpstorecart' );
                                            $wp_roles->add_cap( 'editor', 'manage_wpstorecart' );
                                            $wp_roles->add_cap( 'author', 'manage_wpstorecart' );
                                            $wp_roles->remove_cap( 'contributor', 'manage_wpstorecart' );
                                        }
                                        if($devOptions['admin_capability']=='contributor') {
                                            $wp_roles->add_cap( 'administrator', 'manage_wpstorecart' );
                                            $wp_roles->add_cap( 'editor', 'manage_wpstorecart' );
                                            $wp_roles->add_cap( 'author', 'manage_wpstorecart' );
                                            $wp_roles->add_cap( 'contributor', 'manage_wpstorecart' );
                                        }
				}

				update_option($this->adminOptionsName, $devOptions);

                                if (isset($_POST['required_info_key']) && isset($_POST['required_info_name']) && isset($_POST['required_info_type'])) {
                                    $arrayCounter = 0;
                                    $table_name777 = $wpdb->prefix . "wpstorecart_meta";
                                    foreach ($_POST['required_info_key'] as $currentKey) {
                                        $updateSQL = "UPDATE  `{$table_name777}` SET  `value` =  '{$_POST['required_info_name'][$arrayCounter]}||{$_POST['required_info_required_'.$currentKey]}||{$_POST['required_info_type'][$arrayCounter]}' WHERE  `primkey` ={$currentKey};";
                                        $results = $wpdb->query($updateSQL);
                                        $arrayCounter++;
                                    }
                                }


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
			
			$this->spHeader();
                        
			$this->spSettings();
                        $this->wpstorecart_alert();

			echo'<div style="width:75%;max-width:75%;float:left;">
                            <h2> </h2>
			<form method="post" action="'. $_SERVER["REQUEST_URI"].'" name="wpscform" id="wpscform">
                            <input type="hidden" name="theCurrentTab" id="theCurrentTab" value="" />
                        <ul class="tabs">
                            <li><a href="#tab1" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab1\');"><img src="'.plugins_url('/images/buttons_general.jpg' , __FILE__).'" /></a></li>
                            <li><a href="#tab2" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab2\');"><img src="'.plugins_url('/images/buttons_email.jpg' , __FILE__).'" /></a></li>';

                        echo '<li><a href="#tab3" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab3\');"><img src="'.plugins_url('/images/buttons_product.jpg' , __FILE__).'" /></a></li>';

                        if($devOptions['storetype']!='Digital Goods Only') { // Hide shipping if digital only store
                            echo '<li><a href="#tab6" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab6\');"><img src="'.plugins_url('/images/buttons_shipping.jpg' , __FILE__).'" /></a></li>';
                        }
                        echo '<li><a href="#tab4" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab4\');"><img src="'.plugins_url('/images/buttons_payment.jpg' , __FILE__).'" /></a></li>
                            <li><a href="#tab5" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab5\');"><img src="'.plugins_url('/images/buttons_text.jpg' , __FILE__).'" /></a></li>
                            <li><a href="#tab7" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab7\');"><img src="'.plugins_url('/images/buttons_customers.jpg' , __FILE__).'" /></a></li>
                            <li><a href="#tab8" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab8\');"><img src="'.plugins_url('/images/buttons_taxes.jpg' , __FILE__).'" /></a></li>
                        </ul>
                        <div style="clear:both;"></div>';

                        echo '<div id="tab1" class="tab_content">
			<div id="icon-options-general" class="icon32"></div><h2>wpStoreCart General Options <a href="http://wpstorecart.com/documentation/settings/general-settings/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>
			';
			
			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>
			';			

			echo '
			<tr><td><h3>wpStoreCart Main Page: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1">wpStoreCart uses pages, and needs a single pre-existing page to act as the main page from which most other wpStoreCart pages descend from.  For example, all product pages will be subpages of this page.</div></h3></td>
			<td class="tableDescription"><p>The main page that wpStoreCart will use to display products and other wpStoreCart related pages. </p></td>
			<td><select name="wpStoreCartmainpage"> 
			 <option value="">
						';
			  esc_attr(__('Select page'));
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

			<tr><td><h3>Checkout Page: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2">The checkout page can be any page you specify.  This is the page customers will visit to pay for the products they have added to their cart.</div></h3></td>
			<td class="tableDescription"><p>The page that customers will use during checkout.  The page must have this shortcode in it: [wpstorecart display="checkout"]</p></td>
			<td><select name="checkoutpage"> 
			 <option value="">
						';
			  esc_attr(__('Select page'));
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

			<tr><td><h3>Orders Page: <i>(optional)</i> <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-211145" /><div class="tooltip-content" id="example-content-211145">The Orders &amp; Downloads page, which is optional.  However, if you wish to use the [downloadurl] code in emails, to tell your customers the URL they need to visit in order to download their orders, then this setting must be set.</div></h3></td>
			<td class="tableDescription"><p>The page where customers can view their orders, must have this shortcode in it: [wpstorecart display="orders"]</p></td>
			<td><select name="orderspage">
			 <option value="">
						';
			  esc_attr(__('Select page'));
			  echo '</option>';

			  $pages = get_pages();
			  foreach ($pages as $pagg) {
				$option = '<option value="'.$pagg->ID.'"';
				if($pagg->ID==$devOptions['orderspage']) {
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
			
			<tr><td><h3>Turn wpStoreCart on? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3">If you want to disable wpStoreCart without deactivating the plugin, then set this to No.  This is useful if you want to disable products and purchasing, but not remove the records or uninstall anything.</div></h3></td>
			<td class="tableDescription"><p>Selecting "No" will turn off wpStoreCart, but will not deactivate it.</p></td>
			<td><p><label for="turnwpStoreCartOn_yes"><input type="radio" id="turnwpStoreCartOn_yes" name="turnwpStoreCartOn" value="true" '; if ($devOptions['turnon_wpstorecart'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="turnwpStoreCartOn_no"><input type="radio" id="turnwpStoreCartOn_no" name="turnwpStoreCartOn" value="false" '; if ($devOptions['turnon_wpstorecart'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p></td>
			</td></tr>

			<tr><td><h3>Store Type <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-6999866" /><div class="tooltip-content" id="example-content-6999866">Setting this to mixed will allow products to be either physical or digital.  Setting this to Physical only will hide the product downloads.  Setting this to Digital Only will hide the shipping options, as well as weight, height, length, etc.</div></h3></td>
			<td class="tableDescription"><p>What type of goods do you sell, digital, phyiscal, or both?</p></td>
			<td>
                        <select name="storetype">
';

                        $theOptions[0] = 'Mixed (physical and digital)';
                        $theOptions[1] = 'Physical Goods Only';
                        $theOptions[2] = 'Digital Goods Only';
                        foreach ($theOptions as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['storetype']) {
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

                        <h2>Advanced Options</h2>
                        <table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

			<tr><td><h3>Google Analytics UA: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4000" /><div class="tooltip-content" id="example-content-4000">Insert your Google Analytics UA code in order to track ecommerce conversions using Google Analytics.  Leave this blank if you\'re not using Google Analytics.  Note, this does not insert tracking code anywhere except when a customer purchases something.</div></h3></td>
			<td class="tableDescription"><p>Insert your Google Analytics UA-XXXXX-XX code here to keep track of sales using Google Analytics.  Leave blank if you don\'t use Google Analytics.</p></td>
			<td><input type="text" name="ga_trackingnum" value="'; _e(apply_filters('format_to_edit',$devOptions['ga_trackingnum']), 'wpStoreCart'); echo'" />
			</td></tr>


			<tr><td><h3>Admin Access <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-69998661234" /><div class="tooltip-content" id="example-content-69998661234">This allows you to choose who has access to the wpStoreCart administration page.  Be careful, changing this can allow more people access to your store!  </div></h3></td>
			<td class="tableDescription"><p>Minimum role required to access the wpStoreCart admin pages. </p></td>
			<td>
                        <select name="admin_capability">
';

                        $theOptionsAc[0] = 'administrator';$theOptionsAcName[0] = 'Administrator';
                        $theOptionsAc[1] = 'editor';$theOptionsAcName[1] = 'Editor &amp; above';
                        $theOptionsAc[2] = 'author';$theOptionsAcName[2] = 'Author &amp; above';
                        $theOptionsAc[3] = 'contributor';$theOptionsAcName[3] = 'Contributor &amp; above';
                        $fcounter=0;
                        foreach ($theOptionsAc as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['admin_capability']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionsAcName[$fcounter];
				$option .= '</option>';
				echo $option;
                                $fcounter++;
                        }

   			echo '
			</select>
			</td></tr>

			<tr style="display:none;"><td><h3>Admin Menu Style <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-799934534" /><div class="tooltip-content" id="example-content-799934534">Allows you to use the classic admin panel style from wpStoreCart 1.x and 2.x, the new style of wpStoreCart 3, or a mixture of both. </div></h3></td>
			<td class="tableDescription"><p>The admin panel menu style <strong>(Requires a refresh after changing)</strong></p></td>
			<td>
                        <select name="menu_style">
';

                        $theOptionzar[0] = 'classic';$theOptionzarName[0] = 'Classic 2.x';
                        $theOptionzar[1] = 'version3';$theOptionzarName[1] = 'new 3.0 (beta)';
                        $theOptionzar[2] = 'both';$theOptionzarName[2] = 'Use both';
                        $fcounter=0;
                        foreach ($theOptionzar as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['menu_style']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionzarName[$fcounter];
				$option .= '</option>';
				echo $option;
                                $fcounter++;
                        }

   			echo '
			</select>
			</td></tr>

			</table>
			<br style="clear:both;" /><br />

                        <h2>wpStoreCart Desktop Alert API</h2>
                        <table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

                            <tr><td><h3>wpStoreCart API URI: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-400061399" /><div class="tooltip-content" id="example-content-400061399">Copy your API URI from here and paste it into the wpStoreCart Desktop Alert Settings screen.  This will allow you to receive Alerts on your desktop when activity such as sales or disputes happen. </div></h3></td>
                            <td class="tableDescription"><p>The URI that wpStoreCart Desktop Alert will call.</p></td>
                            <td><input type="text" onclick="this.focus();this.select();" style="min-width:300px;width:300px;" readonly="readonly" value="'; echo plugins_url('/api/' , __FILE__); echo'" />
                            </td></tr>

                            <tr><td><h3>wpStoreCart API Key: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4000613" /><div class="tooltip-content" id="example-content-4000613">Copy your API Key from here and paste it into the wpStoreCart Desktop Alert Settings screen.  This will allow you to receive Alerts on your desktop when activity such as sales or disputes happen. </div></h3></td>
                            <td class="tableDescription"><p>Your wpStoreCart API Key</p></td>
                            <td><input type="text" onclick="this.focus();this.select();" style="min-width:300px;width:300px;" readonly="readonly" value="'; _e(apply_filters('format_to_edit',$devOptions['wpsc_api_key']), 'wpStoreCart'); echo'" />
                            </td></tr>

                            <tr><td><h3>wpStoreCart Secret Hash: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-40006131" /><div class="tooltip-content" id="example-content-40006131">Copy your Secret Hash from here and paste it into the wpStoreCart Desktop Alert Settings screen.  This will allow you to receive Alerts on your desktop when activity such as sales or disputes happen. </div></h3></td>
                            <td class="tableDescription"><p>Your wpStoreCart Secret Hash</p></td>
                            <td><input type="text" onclick="this.focus();this.select();" style="min-width:300px;width:300px;" readonly="readonly" value="'; _e(apply_filters('format_to_edit',$devOptions['wpsc_secret_hash']), 'wpStoreCart'); echo'" />
                            </td></tr>
                        </table>
			<br style="clear:both;" /><br />
                        </div>
                        <div id="tab2" class="tab_content">
			<div id="icon-users" class="icon32"></div><h2>EMail Options <a href="http://wpstorecart.com/documentation/settings/email-settings/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>';

			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

			<tr><td><h3>Email Address <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4">wpStoreCart attempts to send emails when a customer purchasing something.  Whatever email address you enter here will be used as the FROM address.  Set this to an email address where you will expect to receive customer replies.</div></h3></td>
			<td class="tableDescription"><p>The email address that you wish to send and recieve all customer emails.</p></td>
			<td><input type="text" name="wpStoreCartEmail" value="'; _e(apply_filters('format_to_edit',$devOptions['wpStoreCartEmail']), 'wpStoreCart'); echo'" />
			</td></tr>	

			<tr><td><h3>Email Sent On Purchase <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-40" /><div class="tooltip-content" id="example-content-40">wpStoreCart attempts to send an email directly after a purchase is made.  This gives the customer feedback that their purchase was successful, and should also inform them that there will be a delay pending the approval of the purchase from a store admin.</div></h3></td>
			<td class="tableDescription"><p>The email to send when a customer purchases something.</p></td>
			<td><textarea name="emailonpurchase" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailonpurchase']), 'wpStoreCart'); echo'</textarea>
			</td></tr>	

			<tr><td><h3>Email Sent On Approval <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-41" /><div class="tooltip-content" id="example-content-41">wpStoreCart attempts to send an email once the order has been approved by an admin.  This lets the customer know that their order is fulfilled, and for digital downloads, it means they now have immediate access to their order.  Physical products are not yet shipped at this stage.</div></h3></td>
			<td class="tableDescription"><p>The email to send when an admin approves an order.</p></td>
			<td><textarea name="emailonapproval" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailonapproval']), 'wpStoreCart'); echo'</textarea>
			</td></tr>	

			<tr><td><h3>Email Sent When Shipped <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-42" /><div class="tooltip-content" id="example-content-42">wpStoreCart attempts to send an email after you\'ve marked an order shipped.  This let\'s customers know the status of their order.  You will need to manually send or update tracking information at this time.</div></h3></td>
			<td class="tableDescription"><p>The email to send when you\'ve shipped a product.</p></td>
			<td><textarea name="emailonshipped" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailonshipped']), 'wpStoreCart'); echo'</textarea>
			</td></tr>				

			<tr><td><h3>Email Sent When Issuing Serial Number <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-429998987" /><div class="tooltip-content" id="example-content-429998987">wpStoreCart attempts to send an email when a serial number is issued for a product. Each serial number issued has a separate email.</div></h3></td>
			<td class="tableDescription"><p>The email to send when issuing a serial number</p></td>
			<td><textarea name="emailserialnumber" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailserialnumber']), 'wpStoreCart'); echo'</textarea>
			</td></tr>

			<tr><td><h3>Email Signature <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-43" /><div class="tooltip-content" id="example-content-43">The bottom of your emails sent will always contain the same footer or signiture.  Fill that out here.</div></h3></td>
			<td class="tableDescription"><p>This is always included at the bottom of each email sent out.</p></td>
			<td><textarea name="emailsig" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['emailsig']), 'wpStoreCart'); echo'</textarea>
			</td></tr>				
			
			</table>
			<br style="clear:both;" /><br />
                        </div>
                        <div id="tab3" class="tab_content">
			<div id="icon-themes" class="icon32"></div><h2>Display Options <a href="http://wpstorecart.com/documentation/settings/display-settings/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>
			';
			
			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>';

                        echo '
			<tr><td><h3>Number of products/categories per page <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-444999" /><div class="tooltip-content" id="example-content-444999">The number of items and/or categories you want to be displayed per page.  Default is 10.</div></h3></td>
			<td class="tableDescription"><p>The number of items to display on each page.</p></td>
			<td><input type="text" name="itemsperpage" id="itemsperpage" class="validate[custom[positiveInt]]" value="'; _e(apply_filters('format_to_edit',$devOptions['itemsperpage']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr style="display:none;"><td><h3>jQuery UI Theme <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-43133" /><div class="tooltip-content" id="example-content-43133">You can style your shopping cart, products, and other wpStoreCart related elements here using jQuery UI.</div></h3></td>
			<td class="tableDescription"><p>jQuery UI Theme</p></td>
			<td>
                        <select name="wpscjQueryUITheme">
			 <option value="" selected="selected"></option>
			</select>
			</td></tr>

			<tr><td><h3>wpStoreCart Additional CSS <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-431" /><div class="tooltip-content" id="example-content-431">You can style your shopping cart, products, and other wpStoreCart related elements here, but is recommended that you do it in your theme\'s CSS file to keep all CSS in one place.</div></h3></td>
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
			<td>Width: <input type="text" class="validate[custom[positiveInt]]" name="wpStoreCartwidth" id="wpStoreCartwidth" style="width: 58px;" value="'; _e(apply_filters('format_to_edit',$devOptions['wpStoreCartwidth']), 'wpStoreCart'); echo'" />px  <br />Height: <input type="text" name="wpStoreCartheight" id="wpStoreCartheight" class="validate[custom[positiveInt]]" style="width: 58px;" value="'; _e(apply_filters('format_to_edit',$devOptions['wpStoreCartheight']), 'wpStoreCart'); echo'" />px
			</td></tr>

			</table>
			<br style="clear:both;" /><br />
                        <h2>Product Page</h2>
              			';

			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>';

                        echo '

			<tr><td><h3>Display thumbnail under product? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5">This effects the product short tag (and thus, the default product pages as well.)  If set to yes, the products thumbnail will be displayed underneath the product.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the thumbnail for the product will be displayed underneath the product itself</p></td>
			<td><p><label for="showproductthumbnail"><input type="radio" id="showproductthumbnail_yes" name="showproductthumbnail" value="true" '; if ($devOptions['showproductthumbnail'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="showproductthumbnail_no"><input type="radio" id="showproductthumbnail_no" name="showproductthumbnail" value="false" '; if ($devOptions['showproductthumbnail'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>		
			</td></tr>

			<tr><td><h3>Display description under product? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6">This also effects the product short tag (including the default product pages.)  If set to yes, the products description will be written underneath the product thumbnail (if its enabled.)</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the description for the product is written underneath the product, after the thumbnail.</p></td>
			<td><p><label for="showproductdescription"><input type="radio" id="showproductdescription_yes" name="showproductdescription" value="true" '; if ($devOptions['showproductdescription'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="showproductdescription_no"><input type="radio" id="showproductdescription_no" name="showproductdescription" value="false" '; if ($devOptions['showproductdescription'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Use a thickbox image viewer? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-6999123456" /><div class="tooltip-content" id="example-content-6999123456">Makes the thumbnail clickable but instead of redirecting to a new page, it creates the image in a pop up using Thickbox or another alternative.</div></h3></td>
			<td class="tableDescription"><p>Set to "thickbox" to make your product\'s thumbnail show a bigger version when clicked (if it exists)</p></td>
			<td>
                        <select name="useimagebox">
';

                        $theOptionsTb[0] = '';
                        $theOptionsTb[1] = 'thickbox';
                        foreach ($theOptionsTb as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['useimagebox']) {
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

			<tr><td><h3>Display product\'s picture gallery? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-699912345600" /><div class="tooltip-content" id="example-content-699912345600">Displays an image gallery of all the images associated with the product.</div></h3></td>
			<td class="tableDescription"><p>Shows a product\'s images.</p></td>
			<td>
                        <select name="showproductgallery">
';

                        $theOptionsTbg[0] = 'true';
                        $theOptionsTbg[1] = 'false';
                        foreach ($theOptionsTbg as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['showproductgallery']) {
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

			<tr><td><h3>Where to display the gallery? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-699912345601" /><div class="tooltip-content" id="example-content-699912345601">You can either display the picture gallery after the product\'s thumbnail, add to cart button, intro description, or description.</div></h3></td>
			<td class="tableDescription"><p>Where on the product page you wish to display the image gallery.</p></td>
			<td>
                        <select name="showproductgallerywhere">
';

                        $theOptionsTbgw[0] = 'Directly after the Thumbnail';
                        $theOptionsTbgw[1] = 'Directly after the Add to Cart';
                        $theOptionsTbgw[2] = 'Directly after the Intro Description';
                        $theOptionsTbgw[3] = 'Directly after the Description';
                        foreach ($theOptionsTbgw as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['showproductgallerywhere']) {
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
                        <h2>Main Page</h2>
              			';

			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>';

                        echo '

			<tr><td><h3>Content of the Main Page <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-6999" /><div class="tooltip-content" id="example-content-6999">The main page of your store can either list products, or the categories of the site.  It can also display the products either by newest first, or most popular first.</div></h3></td>
			<td class="tableDescription"><p>Changing this will effect what is displayed on the main page of your store.</p></td>
			<td>
                        <select name="frontpageDisplays">
';

                        $theOptions[0] = 'List all products';
                        $theOptions[1] = 'List all categories';
                        $theOptions[2] = 'List all categories (Ascending)';
                        $theOptions[3] = 'List newest products';
                        $theOptions[4] = 'List most popular products';
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

			<tr><td><h3>Display thumbnails on Main Page? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-5554" /><div class="tooltip-content" id="example-content-5554">This effects the main wpStoreCart short tag (and thus, the default Main Page and Category pages as well.)  If set to yes, the product or category thumbnails will be displayed on the Main Page/Category page.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the thumbnail for the products or categories will be displayed on the Main Page and Category pages.</p></td>
			<td><p><label for="displayThumb"><input type="radio" id="displayThumb_yes" name="displayThumb" value="true" '; if ($devOptions['displayThumb'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displayThumb_no"><input type="radio" id="displayThumb_no" name="displayThumb" value="false" '; if ($devOptions['displayThumb'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Display titles on Main Page? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-55544" /><div class="tooltip-content" id="example-content-55544">This effects the main wpStoreCart short tag (and thus, the default Main Page and Category pages as well.)  If set to yes, the product or category title will be displayed on the Main Page/Category page.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the title of the products or categories will be displayed on the Main Page and Category pages.</p></td>
			<td><p><label for="displayTitle"><input type="radio" id="displayTitle_yes" name="displayTitle" value="true" '; if ($devOptions['displayTitle'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displayTitle_no"><input type="radio" id="displayTitle_no" name="displayTitle" value="false" '; if ($devOptions['displayTitle'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Display small descriptions on Main Page? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-55545" /><div class="tooltip-content" id="example-content-55545">This effects the main wpStoreCart short tag (and thus, the default Main Page and Categpoy pages as well.)  If set to yes, the product or category introductory description will be displayed on the Main Page/Category page.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the introductory description of the products or categories will be displayed on the Main Page and Category pages.</p></td>
			<td><p><label for="displayintroDesc"><input type="radio" id="displayintroDesc_yes" name="displayintroDesc" value="true" '; if ($devOptions['displayintroDesc'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displayintroDesc_no"><input type="radio" id="displayintroDesc_no" name="displayintroDesc" value="false" '; if ($devOptions['displayintroDesc'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Display the prices of products? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-555451234" /><div class="tooltip-content" id="example-content-555451234">This effects the main wpStoreCart short tag (and thus, the default Main Page and Categpoy pages as well.)  If set to yes, each products price will be displayed on the Main Page/Category page.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the prices of products will be displayed on the Main Page and Category pages.</p></td>
			<td><p><label for="displaypriceonview"><input type="radio" id="displaypriceonview_yes" name="displaypriceonview" value="true" '; if ($devOptions['displaypriceonview'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displaypriceonview_no"><input type="radio" id="displaypriceonview_no" name="displaypriceonview" value="false" '; if ($devOptions['displaypriceonview'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Display Type <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-7999" /><div class="tooltip-content" id="example-content-7999">This effects the main wpStoreCart short tag (and thus, the default Main Page and Categpoy pages as well.)  If set to grid, the product or category will be displayed within a grid format, or if it\'s set to list, they will be presented in a top down, one at a time list view on the Main Page/Category page.</div></h3></td>
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

                        <h2>Checkout Page</h2>
              			';

			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>';

                        echo '

			<tr><td><h3>Display shipping total? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-500055" /><div class="tooltip-content" id="example-content-500055">Displays shipping cost on the checkout page.  This will automatically be disabled for Digital Only stores, regardless of the setting here.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, shipping will be displayed in shopping carts.</p></td>
			<td><p><label for="displayshipping"><input type="radio" id="displayshipping_yes" name="displayshipping" value="true" '; if ($devOptions['displayshipping'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displayshipping_no"><input type="radio" id="displayshipping_no" name="displayshipping" value="false" '; if ($devOptions['displayshipping'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Display taxes? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-600055999" /><div class="tooltip-content" id="example-content-600055999">Displays taxes on the checkout page.  </div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the tax will be displayed in shopping carts.</p></td>
			<td><p><label for="displaytaxes"><input type="radio" id="displaytaxes_yes" name="displaytaxes" value="true" '; if ($devOptions['displaytaxes'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displaytaxes_no"><input type="radio" id="displaytaxes_no" name="displaytaxes" value="false" '; if ($devOptions['displaytaxes'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Display subtotal? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-600055" /><div class="tooltip-content" id="example-content-600055">Displays subtotal, without shipping on the checkout page.  This will be identical to the total for all items without shipping, and may be reduntant on Digital Only stores.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the subtotal will be displayed in shopping carts.</p></td>
			<td><p><label for="displaysubtotal"><input type="radio" id="displaysubtotal_yes" name="displaysubtotal" value="true" '; if ($devOptions['displaysubtotal'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displaysubtotal_no"><input type="radio" id="displaysubtotal_no" name="displaysubtotal" value="false" '; if ($devOptions['displaysubtotal'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Display final total? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-700055" /><div class="tooltip-content" id="example-content-700055">Displays the total, including any calculated shipping, on the checkout page. Recommended.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, the final total will be displayed in shopping carts.</p></td>
			<td><p><label for="displaytotal"><input type="radio" id="displaytotal_yes" name="displaytotal" value="true" '; if ($devOptions['displaytotal'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="displaytotal_no"><input type="radio" id="displaytotal_no" name="displaytotal" value="false" '; if ($devOptions['displaytotal'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Display product thumbnails? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-70005512" /><div class="tooltip-content" id="example-content-70005512">Next to each product, displays the products thumbnail.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, each product will display it\'s thumbnail.</p></td>
			<td><p><label for="checkoutimages"><input type="radio" id="checkoutimages_yes" name="checkoutimages" value="true" '; if ($devOptions['displaytotal'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="checkoutimages_no"><input type="radio" id="checkoutimages_no" name="checkoutimages" value="false" '; if ($devOptions['checkoutimages'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                        Width: <input style="width:35px;" class="validate[custom[positiveInt]]" type="text" name="checkoutimagewidth" id="checkoutimagewidth" value="'; _e(apply_filters('format_to_edit',$devOptions['checkoutimagewidth']), 'wpStoreCart'); echo'" />px &nbsp; &nbsp; Height: <input style="width:35px;" class="validate[custom[positiveInt]]" type="text" name="checkoutimageheight" id="checkoutimageheight" value="'; _e(apply_filters('format_to_edit',$devOptions['checkoutimageheight']), 'wpStoreCart'); echo'" />px
			</td></tr>

			<tr><td><h3>Enable Coupons &amp; display form? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-706055" /><div class="tooltip-content" id="example-content-706055">Enables coupons during checkout and displays the coupon input.</div></h3></td>
			<td class="tableDescription"><p>Yes to enable or No to disable coupons.</p></td>
			<td><p><label for="enablecoupons"><input type="radio" id="enablecoupons_yes" name="enablecoupons" value="true" '; if ($devOptions['enablecoupons'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enablecoupons_no"><input type="radio" id="enablecoupons_no" name="enablecoupons" value="false" '; if ($devOptions['enablecoupons'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Checkout XHTML Type <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-79990000" /><div class="tooltip-content" id="example-content-79990000">From wpStoreCart 2.3.13 and below, the checkout could only be displayed using DIVs.  However, now if you want to use a TABLE instead, you can do so easily.</div></h3></td>
			<td class="tableDescription"><p>Do you want the default DIV based markup, or a TABLE based checkout page?</p></td>
			<td>
                        <select name="checkout_xhtml_type">
                        ';

                        $theOptionz[0] = 'div';
                        $theOptionz[1] = 'table';
                        foreach ($theOptionz as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['checkout_xhtml_type']) {
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

			<tr><td><h3>Disable Inline Styles? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-70605655" /><div class="tooltip-content" id="example-content-70605655">Disables a few inline CSS styles, such as floating the astericks to the left of the registration fields.</div></h3></td>
			<td class="tableDescription"><p>Yes to disable the inline CSS. Only set to Yes if you are having CSS rendering problems.</p></td>
			<td><p><label for="disable_inline_styles"><input type="radio" id="disable_inline_styles_yes" name="disable_inline_styles" value="true" '; if ($devOptions['disable_inline_styles'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="disable_inline_styles_no"><input type="radio" id="disable_inline_styles_no" name="disable_inline_styles" value="false" '; if ($devOptions['disable_inline_styles'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Field Order <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-1170605655" /><div class="tooltip-content" id="example-content-1170605655">Determine the order in which the quantity, pic, name, price, & remove are displayed on the checkout page & widget.</div></h3></td>
			<td class="tableDescription"><p>The order in which the quantity, pic, name, price, & remove are displayed on the checkout page & widget</p></td>
			<td>
                        <select name="field_order_0">
';

                        $theOptionzarr[0] = '0';$theOptionzarrName[0] = 'qty';
                        $theOptionzarr[1] = '1';$theOptionzarrName[1] = 'pic';
                        $theOptionzarr[2] = '2';$theOptionzarrName[2] = 'name';
                        $theOptionzarr[3] = '3';$theOptionzarrName[3] = 'price';
                        $theOptionzarr[4] = '4';$theOptionzarrName[4] = 'remove';
                        $fcounter=0;
                        foreach ($theOptionzarr as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['field_order_0']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionzarrName[$fcounter];
				$option .= '</option>';
				echo $option;
                                $fcounter++;
                        }

   			echo '</select>
                        <select name="field_order_1">
';

                        $theOptionzarr[0] = '0';$theOptionzarrName[0] = 'qty';
                        $theOptionzarr[1] = '1';$theOptionzarrName[1] = 'pic';
                        $theOptionzarr[2] = '2';$theOptionzarrName[2] = 'name';
                        $theOptionzarr[3] = '3';$theOptionzarrName[3] = 'price';
                        $theOptionzarr[4] = '4';$theOptionzarrName[4] = 'remove';
                        $fcounter=0;
                        foreach ($theOptionzarr as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['field_order_1']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionzarrName[$fcounter];
				$option .= '</option>';
				echo $option;
                                $fcounter++;
                        }

   			echo '</select>
                        <select name="field_order_2">
';

                        $theOptionzarr[0] = '0';$theOptionzarrName[0] = 'qty';
                        $theOptionzarr[1] = '1';$theOptionzarrName[1] = 'pic';
                        $theOptionzarr[2] = '2';$theOptionzarrName[2] = 'name';
                        $theOptionzarr[3] = '3';$theOptionzarrName[3] = 'price';
                        $theOptionzarr[4] = '4';$theOptionzarrName[4] = 'remove';
                        $fcounter=0;
                        foreach ($theOptionzarr as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['field_order_2']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionzarrName[$fcounter];
				$option .= '</option>';
				echo $option;
                                $fcounter++;
                        }

   			echo '</select>
                        <select name="field_order_3">
';

                        $theOptionzarr[0] = '0';$theOptionzarrName[0] = 'qty';
                        $theOptionzarr[1] = '1';$theOptionzarrName[1] = 'pic';
                        $theOptionzarr[2] = '2';$theOptionzarrName[2] = 'name';
                        $theOptionzarr[3] = '3';$theOptionzarrName[3] = 'price';
                        $theOptionzarr[4] = '4';$theOptionzarrName[4] = 'remove';
                        $fcounter=0;
                        foreach ($theOptionzarr as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['field_order_3']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionzarrName[$fcounter];
				$option .= '</option>';
				echo $option;
                                $fcounter++;
                        }

   			echo '</select>
                        <select name="field_order_4">
';

                        $theOptionzarr[0] = '0';$theOptionzarrName[0] = 'qty';
                        $theOptionzarr[1] = '1';$theOptionzarrName[1] = 'pic';
                        $theOptionzarr[2] = '2';$theOptionzarrName[2] = 'name';
                        $theOptionzarr[3] = '3';$theOptionzarrName[3] = 'price';
                        $theOptionzarr[4] = '4';$theOptionzarrName[4] = 'remove';
                        $fcounter=0;
                        foreach ($theOptionzarr as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['field_order_4']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionzarrName[$fcounter];
				$option .= '</option>';
				echo $option;
                                $fcounter++;
                        }

   			echo '
			</select>
			</td></tr>

			</table>
			<br style="clear:both;" /><br />

                        <h2>My Orders &amp; Downloads Page</h2>
              			';

			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>';

                        echo '

			<tr><td><h3>Profile Editable? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-69998661234555" /><div class="tooltip-content" id="example-content-69998661234555">This allows you to choose who whether or not the profile is editable, static, or shows both the static information as well as an editable version.</div></h3></td>
			<td class="tableDescription"><p>How to display profile information </p></td>
			<td>
                        <select name="orders_profile">
';

                        $theOptionsAcc[0] = 'editable';$theOptionsAccName[0] = 'Show editable profile';
                        $theOptionsAcc[1] = 'display';$theOptionsAccName[1] = 'Show profile information';
                        $theOptionsAcc[2] = 'both';$theOptionsAccName[2] = 'Show both';
                        $fcounter=0;
                        foreach ($theOptionsAcc as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['orders_profile']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionsAccName[$fcounter];
				$option .= '</option>';
				echo $option;
                                $fcounter++;
                        }

   			echo '
			</select>
			</td></tr>
                        </table>
			<br style="clear:both;" /><br />

                        <h2>Buttons</h2>

                        <table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>
			<tr><td><h3>Add to Cart button classes</h3></td>
			<td class="tableDescription"><p>Additional Classes for Add to Cart buttons.  Enter classes without periods, space separated. </p></td>
			<td><input type="text" name="button_classes_addtocart" value="'; _e(apply_filters('format_to_edit',$devOptions['button_classes_addtocart']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Checkout button classes</h3></td>
			<td class="tableDescription"><p>Additional Classes for Checkout buttons.  Enter classes without periods, space separated. </p></td>
			<td><input type="text" name="button_classes_checkout" value="'; _e(apply_filters('format_to_edit',$devOptions['button_classes_checkout']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Other button classes</h3></td>
			<td class="tableDescription"><p>Additional Classes for update, clear, & other buttons.  Enter classes without periods, space separated. </p></td>
			<td><input type="text" name="button_classes_meta" value="'; _e(apply_filters('format_to_edit',$devOptions['button_classes_meta']), 'wpStoreCart'); echo'" />
			</td></tr>
                        
                        </table>

                        </div>
                        <div id="tab6" class="tab_content">
			<div id="icon-options-general" class="icon32"></div><h2>Shipping Options <a href="http://wpstorecart.com/documentation/settings/shipping-settings/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>';

                        if($devOptions['storetype']=='Digital Goods Only') {

                            echo '<i>Your store is set to only sell digital items, therefore shipping has been disabled.  If you would like to enable shipping, please goto the <a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab1">General Settings</a>, and change Store Type to Mixed (physical and digital).  Then click the Update Settings button and return to this page.</i>';

                        } else {

                            if (@!extension_loaded('curl')) {
                                $curl_is_disabled = true;
                            } else {
                                if (@!function_exists('curl_init')) {
                                    $curl_is_disabled = true;
                                } else {
                                    $curl_is_disabled = false;
                                }
                            }



                            echo '<h2>Flat Rate Shipping</h2>
                            <table class="widefat">
                            <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

                            <tr><td><h3>Flat Rate Type: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-7999777" /><div class="tooltip-content" id="example-content-7999777">When each product "has it\'s own flat rate amount", it means that when you add or edit a product, the flat rate amount you specify there is what will be charged each time the product is added to the cart.  <br /><br />  If you select "There is one flat rate, but each item in cart adds the flat rate to the shipping cost", that means all items in your store have the exact same flat rate shipping charge, and that each item in the cart adds that charge to the total shipping. <br /> <br /> If you select "There is one flat rate that is charged, regardless of the number of items in the cart" then no matter how little or how much is added to the cart, there will only be one shipping charge.</div></h3></td>
                            <td class="tableDescription"><p>Allows you to use several different types of flat rate shipping or to disable it.</p></td>
                            <td>
                            <select name="flatrateshipping" id="flatrateshipping" onclick="if(jQuery(\'#flatrateshipping\').val()==\'individual\' || jQuery(\'#flatrateshipping\').val()==\'off\'){jQuery(\'#flatratetr\').fadeOut(\'slow\');} else {jQuery(\'#flatratetr\').fadeIn(\'slow\');}">
                            ';

                            $theOptionzr[0] = 'individual'; $theOptionzrr[0] = 'Each product has it\'s own flat rate amount';
                            $theOptionzr[1] = 'all_single'; $theOptionzrr[1] = 'There is one flat rate, but each item in cart adds the flat rate to the shipping cost';
                            $theOptionzr[2] = 'all_global'; $theOptionzrr[2] = 'There is one flat rate that is charged, regardless of the number of items in the cart';
                            $theOptionzr[3] = 'off'; $theOptionzrr[3] = 'Off.  Flat rate shipping is completely disabled';
                            $icounter = 0;
                            foreach ($theOptionzr as $theOption) {

                                    $option = '<option value="'.$theOption.'"';
                                    if($theOption == $devOptions['flatrateshipping']) {
                                            $option .= ' selected="selected"';
                                    }
                                    $option .='>';
                                    $option .= $theOptionzrr[$icounter];
                                    $option .= '</option>';
                                    echo $option;
                                    $icounter++;
                            }

                            echo '
                            </select>
                            ';

                            echo '
                            <tr id="flatratetr"><td><h3>Flat Rate Amount <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-444333" /><div class="tooltip-content" id="example-content-444333">The flat rate that is charged based on the setting above.</div></h3></td>
                            <td class="tableDescription"><p>The global flat rate shipping cost.</p></td>
                            <td><input type="text" name="flatrateamount" value="'; _e(apply_filters('format_to_edit',$devOptions['flatrateamount']), 'wpStoreCart'); echo'" />
                            </td></tr>

                            </table>';

                            if($devOptions['flatrateshipping']=='off' || $devOptions['flatrateshipping']=='individual') {
                                echo '<script type="text/javascript">
                                    /* <![CDATA[ */
                                    jQuery(\'#flatratetr\').hide();
                                    /* ]]> */
                                    </script>';
                            }

                            if($curl_is_disabled == true) {
                                echo '<br /><div class="fade"><p><strong>CURL is either not installed or not enabled.  Contact a system administrator and have them enable CURL for your server.  Until then, the "Shipping Services" shipping options on this page cannot be used and have been disabled.</strong></p></div>';
                            }

                            echo '<h2>Shipping Services</h2>
                            <table class="widefat"';if($curl_is_disabled == true) {echo ' style="opacity:0.5;"';} echo '>
                            <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

                            <tr><td><h3>Zip code you ship FROM <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-438333" /><div class="tooltip-content" id="example-content-438333">The 5 digit zip code where you ship your packages from.  This is used in shipping calculation to determine price.</div></h3></td>
                            <td class="tableDescription"><p>The 5 digit zip code where you ship your products FROM.</p></td>
                            <td><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="text" name="shipping_zip_origin" value="'; _e(apply_filters('format_to_edit',$devOptions['shipping_zip_origin']), 'wpStoreCart'); echo'" />
                            </td></tr>

                            <tr><td><h3>Enable USPS Shipping? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-81234" /><div class="tooltip-content" id="example-content-81234">If your business is based out of the United States, this allows you to ship via USPS and allows the customer to calculate the shipping rates before purchase.</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, will allow customers to select USPS as a shipping option and will give shipping price quotes for USPS.</p></td>
                            <td><p><label for="enableusps"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enableusps_yes" name="enableusps" value="true" '; if ($devOptions['enableusps'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enableusps_no"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enableusps_no" name="enableusps" value="false" '; if ($devOptions['enableusps'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                                Username: <input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="text" name="uspsapiname" value="'; _e(apply_filters('format_to_edit',$devOptions['uspsapiname']), 'wpStoreCart'); echo'" />
                            </td></tr>

                            <tr><td><h3>Enable UPS Shipping? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-81235" /><div class="tooltip-content" id="example-content-81235">This allows you to ship via UPS and allows the customer to calculate the shipping rates before purchase.</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, will allow customers to select USPS as a shipping option and will give shipping price quotes for UPS.</p></td>
                            <td><p><label for="enableups"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enableups_yes" name="enableups" value="true" '; if ($devOptions['enableups'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enableups_no"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enableups_no" name="enableups" value="false" '; if ($devOptions['enableups'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr style="display:none;"><td><h3>Enable FedEx Shipping? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-81236" /><div class="tooltip-content" id="example-content-81236">This allows you to ship via FedEx and allows the customer to calculate the shipping rates before purchase.</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, will allow customers to select USPS as a shipping option and will give shipping price quotes for FedEx.</p></td>
                            <td><p><label for="enablefedex"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enablefedex_yes" name="enablefedex" value="true" '; /*if ($devOptions['enablefedex'] == "true") { _e('checked="checked"', "wpStoreCart"); };*/ echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enablefedex_no"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enablefedex_no" name="enablefedex" value="false" '; /*if ($devOptions['enablefedex'] == "false") {*/ _e('checked="checked"', "wpStoreCart"); /*};*/ echo '/> No</label></p>
                            </td></tr>

                            </table>';

                        }

                        echo '
			<br style="clear:both;" /><br />
                        </div>
                        <div id="tab4" class="tab_content">
			<div id="icon-options-general" class="icon32"></div><h2>Payment Options <a href="http://wpstorecart.com/documentation/settings/payment-settings/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>';


                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/updater.pro.php') ) {
                            include_once(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php');
                            include_once(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/updater.pro.php');
                        }

                        echo '

                        <table class="widefat">
			<tr><td><h3>Currency Symbol</h3></td>
			<td class="tableDescription"><p>Left Symbol Default: <i>$</i></p><p>Right Symbol Default: </p></td>
			<td>Left symbol: <input type="text" name="currency_symbol" value="'; _e(apply_filters('format_to_edit',$devOptions['currency_symbol']), 'wpStoreCart'); echo'" />
                        <br />Right symbol: <input type="text" name="currency_symbol_right" value="'; _e(apply_filters('format_to_edit',$devOptions['currency_symbol_right']), 'wpStoreCart'); echo'" />
			</td></tr>
                        </table>

                        <br style="clear:both;" /><br />

                        <h3>PayPal Payment Gateway</h3>
                        <table class="widefat">
                                                <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>
			<tr><td><h3>Accept PayPal Payments? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7">Want to accept PayPal payments?  Then set this to yes!</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using PayPal.</p></td>
			<td><p><label for="allowpaypal"><input type="radio" id="allowpaypal_yes" name="allowpaypal" value="true" '; if ($devOptions['allowpaypal'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowpaypal_no"><input type="radio" id="allowpaypal_no" name="allowpaypal" value="false" '; if ($devOptions['allowpaypal'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>		
			</td></tr>

			<tr><td><h3>Turn on PayPal Test Mode? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-8" /><div class="tooltip-content" id="example-content-8">If you need to do tests with the PayPal Sandbox then set this to yes.  Warning!  These payments are not real, so any purchases made under the Sandbox will result in live sales with no money!  You can delete test orders in the Orders tab above.</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, all transactions are done using the PayPal sandbox.</p></td>
			<td><p><label for="paypaltestmode"><input type="radio" id="paypaltestmode_yes" name="paypaltestmode" value="true" '; if ($devOptions['paypaltestmode'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="paypaltestmode_no"><input type="radio" id="paypaltestmode_no" name="paypaltestmode" value="false" '; if ($devOptions['paypaltestmode'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>		
			</td></tr>			
			
			<tr><td><h3>PayPal Email Address <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-9" /><div class="tooltip-content" id="example-content-9">The PayPal email address you wish to recieve payments to.  Make sure you have already registered this email address with PayPal.</div></h3></td>
			<td class="tableDescription"><p>The email address you wish to receive PayPal payments.</p></td>
			<td><input type="text" name="paypalemail" value="'; _e(apply_filters('format_to_edit',$devOptions['paypalemail']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Currency <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-941" /><div class="tooltip-content" id="example-content-941">Change this to whatever currency your shop is in.  Note that this is currently only supported in PayPal payments.</div></h3></td>
			<td class="tableDescription"><p>The type of currency that your store uses.</p></td>
			<td>
                        <select name="currency_code">
';

                        $theOptionsz[0] = 'USD';$theOptionszName[0] = 'U.S. Dollars ($)';
                        $theOptionsz[1] = 'AUD';$theOptionszName[1] = 'Australian Dollars (A $)';
                        $theOptionsz[2] = 'CAD';$theOptionszName[2] = 'Canadian Dollars (C $)';
                        $theOptionsz[3] = 'EUR';$theOptionszName[3] = 'Euros (&#8364)';
                        $theOptionsz[4] = 'GBP';$theOptionszName[4] = 'Pounds Sterling (&#163)';
                        $theOptionsz[5] = 'JPY';$theOptionszName[5] = 'Yen (&#165)';
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

			<tr><td><h3>Advanced Settings <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-8222222222222222" /><div class="tooltip-content" id="example-content-8222222222222222">For advanced users only, here are a few settings most users shouldn\'t touch, even if you\'re experiencing issues.</div></h3></td>
			<td class="tableDescription"><p>Advanced settings.  Do not edit or use unless you know exactly what you\'re doing!</p></td>
			<td>IPN URL: <input type="text" name="paypalipnurl" value="'; _e(apply_filters('format_to_edit',$devOptions['paypalipnurl']), 'wpStoreCart'); echo'" style="width:200px;" />
			</td></tr>

                        </table>
                        <br style="clear:both;" /><br />
                        ';

                        echo '
                        <h3>Check/Money Order/COD Payments</h3>
                        <table class="widefat">
                                                <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>
			<tr><td><h3>Accept Payments via Mail? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-78777" /><div class="tooltip-content" id="example-content-78777">Want to accept payments through the mail from check, money orders, or cash on delivery (COD)?  You can even use this to record your cash transactions in your brick and mortar store if you wish.  Remember, don\'t send anything until the payment clears!</div></h3></td>
			<td class="tableDescription"><p>If set to Yes, customers can purchase using Check, Money Order or COD</p></td>
			<td><p><label for="allowcheckmoneyorder"><input type="radio" id="allowcheckmoneyorder_yes" name="allowcheckmoneyorder" value="true" '; if ($devOptions['allowcheckmoneyorder'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowcheckmoneyorder_no"><input type="radio" id="allowcheckmoneyorder_no" name="allowcheckmoneyorder" value="false" '; if ($devOptions['allowcheckmoneyorder'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>

			<tr><td><h3>Text to Display <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-415555" /><div class="tooltip-content" id="example-content-415555">You should place instructions here as to what address the customer should send their check or money orders to.  Be complete and accurate, and be sure to tell them how long they should wait and who they can contact about their order.</div></h3></td>
			<td class="tableDescription"><p>The text/html that is displayed to customers who choose to pay via check or money order.</p></td>
			<td><textarea name="checkmoneyordertext" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['checkmoneyordertext']), 'wpStoreCart'); echo'</textarea>
			</td></tr>

                        </table>
                        <br style="clear:both;" /><br />
                        ';

                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php')) {
                            echo '
                            <h3>Authorize.NET Gateway (wpStoreCart PRO)</h3>
                            <table class="widefat">
                            <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

                            <tr><td><h3>Accept Authorize.NET Payments? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-70099" /><div class="tooltip-content" id="example-content-70099">Want to accept Authorize.NET payments?  Then set this to yes!</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using Authorize.NET.</p></td>
                            <td><p><label for="allowauthorizenet"><input type="radio" id="allowauthorizenet_yes" name="allowauthorizenet" value="true" '; if ($devOptions['allowauthorizenet'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allowauthorizenet_no"><input type="radio" id="allowauthorizenet_no" name="allowauthorizenet" value="false" '; if ($devOptions['allowauthorizenet'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr><td><h3>Turn on Authorize.NET Test Mode? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-81111" /><div class="tooltip-content" id="example-content-81111">If you need to do tests with Authorize.NET then set this to yes.  Warning!  These payments are not real, so any purchases made under the Sandbox will result in live sales with no money!  You can delete test orders in the Orders tab above.</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, all transactions are tests done using Authorize.NET.</p></td>
                            <td><p><label for="authorizenettestmode"><input type="radio" id="authorizenettestmode_yes" name="authorizenettestmode" value="true" '; if ($devOptions['authorizenettestmode'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="authorizenettestmode_no"><input type="radio" id="authorizenettestmode_no" name="authorizenettestmode" value="false" '; if ($devOptions['authorizenettestmode'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr><td><h3>API Login ID <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-9666" /><div class="tooltip-content" id="example-content-9666">The Authorize.NET API Login ID assigned to you.  </div></h3></td>
                            <td class="tableDescription"><p>The API Login ID you are assigned to use access your Authorize.NET account.</p></td>
                            <td><input type="text" name="authorizenetemail" value="'; _e(apply_filters('format_to_edit',$devOptions['authorizenetemail']), 'wpStoreCart'); echo'" />
                            </td></tr>

                            <tr><td><h3>Secret Key <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-9667" /><div class="tooltip-content" id="example-content-9667">The Authorize.NET secret key which is used to authenticate your shop.</div></h3></td>
                            <td class="tableDescription"><p>The Authorize.NET secret key md5 hash value.</p></td>
                            <td><input type="text" name="authorizenetsecretkey" value="'; _e(apply_filters('format_to_edit',$devOptions['authorizenetsecretkey']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            </table>
                            <br style="clear:both;" /><br />


                            <h3>2CheckOut Gateway (wpStoreCart PRO)</h3>
                            <table class="widefat">
                            <thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

                            <tr><td><h3>Accept 2CheckOut Payments? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-700992" /><div class="tooltip-content" id="example-content-700992">Want to accept 2CheckOut payments?  Then set this to yes!</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, customers can purchase during checkout using 2CheckOut.</p></td>
                            <td><p><label for="allow2checkout"><input type="radio" id="allow2checkout_yes" name="allow2checkout" value="true" '; if ($devOptions['allow2checkout'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="allow2checkout_no"><input type="radio" id="allow2checkout_no" name="allow2checkout" value="false" '; if ($devOptions['allow2checkout'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr><td><h3>Turn on 2CheckOut Test Mode? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-8111166" /><div class="tooltip-content" id="example-content-8111166">If you need to do tests with 2CheckOut then set this to yes.  Warning!  These payments are not real, so any purchases made under the Sandbox will result in live sales with no money!  You can delete test orders in the Orders tab above.</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, all transactions are tests done using 2CheckOut.</p></td>
                            <td><p><label for="2checkouttestmode"><input type="radio" id="2checkouttestmode_yes" name="2checkouttestmode" value="true" '; if ($devOptions['2checkouttestmode'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="2checkouttestmode_no"><input type="radio" id="2checkouttestmode_no" name="2checkouttestmode" value="false" '; if ($devOptions['2checkouttestmode'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
                            </td></tr>

                            <tr><td><h3>2CheckOut Vendor ID <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-966644" /><div class="tooltip-content" id="example-content-966644">The 2CheckOut Vendor ID assigned to you.  </div></h3></td>
                            <td class="tableDescription"><p>The 2CheckOut Vendor ID you are assigned to use access your 2CheckOut account.</p></td>
                            <td><input type="text" name="2checkoutemail" value="'; _e(apply_filters('format_to_edit',$devOptions['2checkoutemail']), 'wpStoreCart'); echo'" />
                            </td></tr>

                            </table>
                            <br style="clear:both;" /><br />


                            ';
                        }

                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/lr/lb_form.php')) {
                            global $devOptions;
                            include_once(WP_PLUGIN_DIR.'/wpsc-payments-pro/lr/lb_form.php');
                        }

                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/qbms/qb_form.php')) {
                            global $devOptions;
                            include_once(WP_PLUGIN_DIR.'/wpsc-payments-pro/qbms/qb_form.php');
                        }

                        echo '
        		
			
			
                        </div>
                        <div id="tab5" class="tab_content">
                        <div id="icon-edit-comments" class="icon32"></div><h2>Text &amp; Language Options <a href="http://wpstorecart.com/documentation/settings/language-settings/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>';


			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

			<tr><td><h3>Successful Payment Text <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-400" /><div class="tooltip-content" id="example-content-400">After the customer is redirected to the payment gateway, such as PayPal, this is the text they will see after successfully completing the payment.</div></h3></td>
			<td class="tableDescription"><p>The text and HTML that is displayed when a customers returns from the payment gateway after successfully paying.</p></td>
			<td><textarea name="success_text" style="width:300px;height:250px;">'; _e(apply_filters('format_to_edit',$devOptions['success_text']), 'wpStoreCart'); echo'</textarea>
			</td></tr>

			<tr><td><h3>Failed Payment Text<img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-401" /><div class="tooltip-content" id="example-content-401">After the customer is redirected to the payment gateway, such as PayPal, this is the text they will see after failing to complete the payment.</div></h3></td>
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

			<tr><td><h3>Total</h3></td>
			<td class="tableDescription"><p>Default: <i>Total</i></p></td>
			<td><input type="text" name="total" value="'; _e(apply_filters('format_to_edit',$devOptions['total']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Tax</h3></td>
			<td class="tableDescription"><p>Default: <i>Tax</i></p></td>
			<td><input type="text" name="tax" value="'; _e(apply_filters('format_to_edit',$devOptions['tax']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Shipping</h3></td>
			<td class="tableDescription"><p>Default: <i>Shipping</i></p></td>
			<td><input type="text" name="shipping" value="'; _e(apply_filters('format_to_edit',$devOptions['shipping']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>Calculate Shipping</h3></td>
			<td class="tableDescription"><p>Default: <i>Calculate Shipping</i></p></td>
			<td><input type="text" name="calculateshipping" value="'; _e(apply_filters('format_to_edit',$devOptions['calculateshipping']), 'wpStoreCart'); echo'" />
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

                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php')) {
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

                            echo '
                            <tr><td><h3>Checkout Liberty Reserve Button</h3></td>
                            <td class="tableDescription"><p>Default: <i>Checkout with Liberty Reserve</i></p></td>
                            <td><input type="text" name="checkout_libertyreserve_button" value="'; _e(apply_filters('format_to_edit',$devOptions['checkout_libertyreserve_button']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>Full name on card</h3></td>
                            <td class="tableDescription"><p>Default: <i>Full name on card</i></p></td>
                            <td><input type="text" name="cc_name" value="'; _e(apply_filters('format_to_edit',$devOptions['cc_name']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>Credit Card #</h3></td>
                            <td class="tableDescription"><p>Default: <i>Credit Card #</i></p></td>
                            <td><input type="text" name="cc_number" value="'; _e(apply_filters('format_to_edit',$devOptions['cc_number']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>Expires</h3></td>
                            <td class="tableDescription"><p>Default: <i>Expires</i></p></td>
                            <td><input type="text" name="cc_expires" value="'; _e(apply_filters('format_to_edit',$devOptions['cc_expires']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>Month</h3></td>
                            <td class="tableDescription"><p>Default: <i>Month</i></p></td>
                            <td><input type="text" name="cc_expires_month" value="'; _e(apply_filters('format_to_edit',$devOptions['cc_expires_month']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>Year</h3></td>
                            <td class="tableDescription"><p>Default: <i>Year</i></p></td>
                            <td><input type="text" name="cc_expires_year" value="'; _e(apply_filters('format_to_edit',$devOptions['cc_expires_year']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>Address of Credit Card</h3></td>
                            <td class="tableDescription"><p>Default: <i>Address of Credit Card</i></p></td>
                            <td><input type="text" name="cc_address" value="'; _e(apply_filters('format_to_edit',$devOptions['cc_address']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>Zipcode of Credit Card</h3></td>
                            <td class="tableDescription"><p>Default: <i>Zipcode of Credit Card</i></p></td>
                            <td><input type="text" name="cc_postalcode" value="'; _e(apply_filters('format_to_edit',$devOptions['cc_postalcode']), 'wpStoreCart'); echo'" />
                            </td></tr>
                            ';

                            echo '
                            <tr><td><h3>CVV</h3></td>
                            <td class="tableDescription"><p>Default: <i>CVV</i></p></td>
                            <td><input type="text" name="cc_cvv" value="'; _e(apply_filters('format_to_edit',$devOptions['cc_cvv']), 'wpStoreCart'); echo'" />
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

                        <tr><td><h3>Login</h3></td>
			<td class="tableDescription"><p>Default: <i>Login</i></p></td>
			<td><input type="text" name="login" value="'; _e(apply_filters('format_to_edit',$devOptions['login']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Logout</h3></td>
			<td class="tableDescription"><p>Default: <i>Logout</i></p></td>
			<td><input type="text" name="logout" value="'; _e(apply_filters('format_to_edit',$devOptions['logout']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Register</h3></td>
			<td class="tableDescription"><p>Default: <i>Register</i></p></td>
			<td><input type="text" name="register" value="'; _e(apply_filters('format_to_edit',$devOptions['register']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Username</h3></td>
			<td class="tableDescription"><p>Default: <i>Username</i></p></td>
			<td><input type="text" name="username" value="'; _e(apply_filters('format_to_edit',$devOptions['username']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Password</h3></td>
			<td class="tableDescription"><p>Default: <i>Password</i></p></td>
			<td><input type="text" name="password" value="'; _e(apply_filters('format_to_edit',$devOptions['password']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Email</h3></td>
			<td class="tableDescription"><p>Default: <i>Email</i></p></td>
			<td><input type="text" name="email" value="'; _e(apply_filters('format_to_edit',$devOptions['email']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>My Orders &amp; Purchases</h3></td>
			<td class="tableDescription"><p>Default: <i>My Orders &amp; Purchases</i></p></td>
			<td><input type="text" name="myordersandpurchases" value="'; _e(apply_filters('format_to_edit',$devOptions['myordersandpurchases']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Required Symbol</h3></td>
			<td class="tableDescription"><p>Default: <i>*</i></p></td>
			<td><input type="text" name="required_symbol" value="'; _e(apply_filters('format_to_edit',$devOptions['required_symbol']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Required Symbol Description</h3></td>
			<td class="tableDescription"><p>Default: <i>* - Fields with an asterick are required.</i></p></td>
			<td><input type="text" name="required_help" value="'; _e(apply_filters('format_to_edit',$devOptions['required_help']), 'wpStoreCart'); echo'" />
			</td></tr>

                        <tr><td><h3>Guest Checkout</h3></td>
			<td class="tableDescription"><p>Default: <i>Guest Checkout</i></p></td>
			<td><input type="text" name="guestcheckout" value="'; _e(apply_filters('format_to_edit',$devOptions['guestcheckout']), 'wpStoreCart'); echo'" />
			</td></tr>

			</table>
			<br style="clear:both;" /><br />
                        </div>
                        <div id="tab7" class="tab_content">
			<div id="icon-users" class="icon32"></div><h2>Customer Options <a href="http://wpstorecart.com/documentation/settings/customer-settings/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>';

			echo '<table class="widefat">
			<thead><tr><th>Option</th><th>Description</th><th>Value</th></tr></thead><tbody>

			<tr><td><h3>Require Registration? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4745996" /><div class="tooltip-content" id="example-content-4745996">Set to "Yes" if you require the customer to register on your site before a purchase can be completed, set it to "No" if you do not want customers to have to register.</div></h3></td>
			<td class="tableDescription"><p>Controls whether or not your site requires registration before checkout completes.</p></td>
			<td><p><label for="requireregistration"><input type="radio" id="requireregistration_yes" name="requireregistration" value="true" '; if ($devOptions['requireregistration'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="requireregistration_no"><input type="radio" id="requireregistration_no" name="requireregistration" value="false" '; if ($devOptions['requireregistration'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>
                        </table>

                        <h2>Required Information At Checkout:</h2>

                         <script type="text/javascript">
                            /* <![CDATA[ */

                            function addwpscfield() {
                                jQuery.ajax({ url: "'.plugins_url('/php/addfield.php' , __FILE__).'", type:"POST", data:"createnewfieldname="+jQuery("#createnewfieldname").val()+"&createnewfieldtype="+jQuery("#createnewfieldtype").val()+"&createnewfieldrequired="+jQuery("input:radio[name=createnewfieldrequired]:checked").val(), success: function(txt){
                                    jQuery("#requiredul").prepend("<li style=\'font-size:90%;cursor:move;background: url('.plugins_url('/images/sort.png' , __FILE__).') top left no-repeat;width:523px;min-width:523px;height:35px;min-height:35px;padding:4px 0 0 30px;margin-bottom:-8px;\' id=\'requiredinfo_"+txt+"\'><img onclick=\'delwpscfield("+txt+");\' style=\'cursor:pointer;position:relative;top:4px;\' src=\''.plugins_url('/images/cross.png' , __FILE__).'\' /><input type=\'text\' value=\'"+jQuery("#createnewfieldname").val()+"\' name=\'required_info_name[]\' /><input type=\'hidden\' name=\'required_info_key[]\' value=\'"+txt+"\' /><select name=\'required_info_type[]\' id=\'ri_"+txt+"\'><option value=\'input (text)\'>Input (text)</option><option value=\'input (numeric)\'>Input (numeric)</option><option value=\'textarea\'>Input Textarea</option><option value=\'states\'>Input US States</option><option value=\'countries\'>Input Countries</option><option value=\'email\'>Input Email Address</option><option value=\'separator\'>--- Separator ---</option><option value=\'header\'>Header &lt;h2&gt;&lt;/h2&gt;</option><option value=\'text\'>Text &lt;p&gt;&lt;/p&gt;</option></select><label for=\'required_info_required_"+txt+"\'><input type=\'radio\' id=\'required_info_required_"+txt+"_yes\' name=\'required_info_required_"+txt+"\' value=\'required\' /> Required</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for=\'required_info_required_"+txt+"_no\'><input type=\'radio\' id=\'required_info_required_"+txt+"_no\' name=\'required_info_required_"+txt+"\' value=\'optional\' /> Optional</label></li>");
                                    jQuery("#ri_"+txt).val(jQuery("#createnewfieldtype").val());
                                    if(jQuery("input:radio[name=createnewfieldrequired]:checked").val()=="required") {
                                        jQuery(\'input[name="required_info_required_\'+txt+\'"][value="required"]\').attr("checked", true);
                                    } else {
                                        jQuery(\'input[name="required_info_required_\'+txt+\'"][value="optional"]\').attr("checked", true);
                                    }
                                    
                                    jQuery("ri_"+txt).val(jQuery("#createnewfieldname").val());

                                }});
                            }

                            function delwpscfield(keytodel) {
                                jQuery.ajax({ url: "'.plugins_url('/php/delfield.php' , __FILE__).'", type:"POST", data:"delete="+keytodel, success: function(){
                                    jQuery("#requiredinfo_"+keytodel).remove();
                                }});
                            }

                            jQuery(document).ready(function(){

                                    jQuery(function() {

                                            jQuery("#requiredsort ul").sortable({ opacity: 0.6, cursor: \'move\', update: function() {
                                                    var order = jQuery(this).sortable("serialize") + \'&action=updateRecordsListings\';
                                                    jQuery.post("'.plugins_url('/php/sortfields.php' , __FILE__).'", order, function(theResponse){
                                                            jQuery("#requiredsort ul").sortable(\'refresh\');
                                                    });
                                            }
                                            });

                                    });


                            });

                           /* ]]> */
                        </script>
                        ';
                        
                        /**
                             * The options for the checkout fields
                             */
                        $theOptionszz[0] = 'input (text)';$theOptionszzName[0] = 'Input (text)';
                        $theOptionszz[1] = 'input (numeric)';$theOptionszzName[1] = 'Input (numeric)';
                        $theOptionszz[2] = 'textarea';$theOptionszzName[2] = 'Input Textarea';
                        $theOptionszz[3] = 'states';$theOptionszzName[3] = 'US States';
                        $theOptionszz[4] = 'taxstates';$theOptionszzName[4] = 'US States (to charge tax)';
                        $theOptionszz[5] = 'countries';$theOptionszzName[5] = 'Countries';
                        $theOptionszz[6] = 'taxcountries';$theOptionszzName[6] = 'Countries (to charge tax)';
                        $theOptionszz[7] = 'email';$theOptionszzName[7] = 'Input Email Address';
                        $theOptionszz[8] = 'separator';$theOptionszzName[8] = '--- Separator ---';
                        $theOptionszz[9] = 'header';$theOptionszzName[9] = 'Header &lt;h2&gt;&lt;/h2&gt;';
                        $theOptionszz[10] = 'text';$theOptionszzName[10] = 'Text &lt;p&gt;&lt;/p&gt;';
                        //$theOptionszz[11] = 'dropdown';$theOptionszzName[11] = 'Drop down list';
                        //$theOptionszz[12] = 'checkbox';$theOptionszzName[12] = 'Input Checkbox';

                        echo'
                        Add new field: <strong>Name: </strong><input type="text" name="createnewfieldname" id="createnewfieldname" value="" /> <strong>Type: </strong><select name="createnewfieldtype" id="createnewfieldtype"><br />';

                        $icounter = 0;
                        foreach ($theOptionszz as $theOption) {

                                $option = '<option value="'.$theOption.'"';
                                $option .='>';
                                $option .= $theOptionszzName[$icounter];
                                $option .= '</option>';
                                echo $option;
                                $icounter++;
                        }

                        echo '</select><label for="createnewfieldrequired_yes"><input type="radio" id="createnewfieldrequired_yes" name="createnewfieldrequired" value="required" checked="checked" /> Required</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="createnewfieldrequired_no"><input type="radio" id="createnewfieldrequired_no" name="createnewfieldrequired" value="optional" /> Optional</label> <img style="cursor:pointer;" src="'.plugins_url('/images/add.png' , __FILE__).'" onclick="addwpscfield();" /><br /><br />
                        <div id="requiredsort" >
                            <ul id="requiredul">
                            ';

                            $table_name33 = $wpdb->prefix . "wpstorecart_meta";
                            $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='requiredinfo' ORDER BY `foreignkey` ASC;";

                            $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                            if(isset($results)) {
                                    foreach ($results as $result) {
                                        $theKey = $result['primkey'];
                                        $exploder = explode('||', $result['value']);
                                        echo '<li style="font-size:90%;cursor:move;background: url(\''.plugins_url('/images/sort.png' , __FILE__).'\') top left no-repeat;width:523px;min-width:523px;height:35px;min-height:35px;padding:4px 0 0 30px;margin-bottom:-8px;" id="requiredinfo_'.$theKey.'"><img onclick="delwpscfield('.$theKey.');" style="cursor:pointer;position:relative;top:4px;" src="'.plugins_url('/images/cross.png' , __FILE__).'" /><input type="text" value="'.$exploder[0];echo '" name="required_info_name[]" /><input type="hidden" name="required_info_key[]" value="'.$theKey.'" /><select name="required_info_type[]">';

                                        $icounter = 0;
                                        foreach ($theOptionszz as $theOption) {

                                                $option = '<option value="'.$theOption.'"';
                                                if($theOption == $exploder[2]) {
                                                        $option .= ' selected="selected"';
                                                }
                                                $option .='>';
                                                $option .= $theOptionszzName[$icounter];
                                                $option .= '</option>';
                                                echo $option;
                                                $icounter++;
                                        }

                                        echo '</select><label for="required_info_required_'.$theKey.'"><input type="radio" id="required_info_required_'.$theKey.'_yes" name="required_info_required_'.$theKey.'" value="required" '; if ($exploder[1]=='required') { _e('checked="checked"', "wpStoreCart"); }; echo '/> Required</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="required_info_required_'.$theKey.'_no"><input type="radio" id="required_info_required_'.$theKey.'_no" name="required_info_required_'.$theKey.'" value="optional" '; if ($exploder[1]=='optional') { _e('checked="checked"', "wpStoreCart"); }; echo '/> Optional</label>'; echo '</li>
                                            ';
                                    }
                            }

                            echo '
                            </ul>
                        </div>
                        <br style="clear:both;" /><br />';

                        echo '
                        <div id="contentRight">
                        </div>
			<br style="clear:both;" /><br />
                        </div>
                        ';

                        echo '<div id="tab8" class="tab_content">
			<div id="icon-options-general" class="icon32"></div><h2>Taxes <a href="http://wpstorecart.com/documentation/settings/general-settings/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>
			';

			echo '

                         <script type="text/javascript">
                            /* <![CDATA[ */

                            function addwpsctax() {
                                jQuery.ajax({ url: "'.plugins_url('/php/addtax.php' , __FILE__).'", type:"POST", data:"taxprimkey="+jQuery("#taxprimkey").val()+"&taxname="+jQuery("#taxname").val()+"&countriestotax="+jQuery("#countriestotax").val()+"&statestotax="+jQuery("#statestotax").val()+"&taxpercent="+jQuery("#taxpercent").val(), success: function(txt){
                                    if (jQuery("#wpsctax-"+txt).length){
                                        jQuery("#wpsctaxname-"+txt).html(jQuery("#taxname").val());
                                        jQuery("#wpsctaxpercent-"+txt).html(jQuery("#taxpercent").val()+"%");
                                        jQuery("#wpsctaxregion-"+txt).html(jQuery("#countriestotax").val() + jQuery("#statestotax").val());
                                    } else {
                                        jQuery("#edittaxes").append("<tr id=\'wpsctax-"+txt+"\'><td><img onclick=\'delwpsctax("+txt+");\' style=\'cursor:pointer;position:relative;top:4px;\' src=\''.plugins_url('/images/cross.png' , __FILE__).'\' /> &nbsp; </td><td id=\'wpsctaxname-"+txt+"\'><input type=\'hidden\' name=\'tax_info_key[]\' value=\'"+txt+"\' /></td><td id=\'wpsctaxpercent-"+txt+"\'></td><td id=\'wpsctaxregion-"+txt+"\'></td></tr>");
                                        jQuery("#wpsctaxname-"+txt).html(jQuery("#taxname").val());
                                        jQuery("#wpsctaxpercent-"+txt).html(jQuery("#taxpercent").val());
                                        jQuery("#wpsctaxregion-"+txt).html(jQuery("#countriestotax").val() + jQuery("#statestotax").val());
                                    }
                                }});
                            }

                            function delwpsctax(keytodel) {
                                jQuery.ajax({ url: "'.plugins_url('/php/deltax.php' , __FILE__).'", type:"POST", data:"delete="+keytodel, success: function(){
                                    jQuery("#wpsctax-"+keytodel).remove();
                                }});
                            }

                            function setMaps(states,countries) {
                                if(jQuery("#usa_image").mapster("get") != "") {
                                    jQuery("#usa_image").mapster("set", false, jQuery("#usa_image").mapster("get"));
                                }
                                if(states != "") {
                                    jQuery("#usa_image").mapster("set",true,states);
                                }
                                if(jQuery("#world_image").mapster("get") != "") {
                                    jQuery("#world_image").mapster("set", false, jQuery("#world_image").mapster("get"));
                                }
                                if(countries != "") {
                                    jQuery("#world_image").mapster("set",true,countries);
                                }
                            }

                           /* ]]> */
                        </script>

                        <div id="taxsort" >
                        <table class="widefat">
                		<thead><tr><th>ID</th><th>Name</th><th>Tax Percent</th><th>Regions</th></tr></thead><tbody id="edittaxes">

                            ';

                            $table_name33 = $wpdb->prefix . "wpstorecart_meta";
                            $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='tax' ORDER BY `primkey` ASC;";

                            $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                            if(isset($results)) {
                                    foreach ($results as $result) {
                                        $theKey = $result['primkey'];
                                        $exploder = explode('||', $result['value']);
                                        echo '<tr id="wpsctax-'.$theKey.'"><td><img onclick="delwpsctax('.$theKey.');" style="cursor:pointer;position:relative;top:4px;" src="'.plugins_url('/images/cross.png' , __FILE__).'" /> &nbsp; <img onclick="jQuery(\'#taxprimkey\').val(\''.$theKey.'\');jQuery(\'#taxname\').val(\''.$exploder[0].'\');jQuery(\'#countriestotax\').val(\''.$exploder[1].'\');jQuery(\'#statestotax\').val(\''.$exploder[2].'\');jQuery(\'#taxpercent\').val(\''.$exploder[3].'\');setMaps(\''.$exploder[2].'\',\''.$exploder[1].'\');" style="cursor:pointer;position:relative;top:4px;" src="'.plugins_url('/images/pencil.png' , __FILE__).'" /></td><td id="wpsctaxname-'.$theKey.'">'.$exploder[0]. '<input type="hidden" name="tax_info_key[]" value="'.$theKey.'" /></td><td id="wpsctaxpercent-'.$theKey.'">'.$exploder[3].'%</td><td id="wpsctaxregion-'.$theKey.'">'.$exploder[1].' '.$exploder[2].'</td></tr>
                                            ';
                                    }
                            }

                            echo '
                            </tbody></table>
                        </div>


                        <table class="widefat">
			<thead><tr><th><h3>Add/Edit tax</h3><a href="#" onclick="jQuery(\'#taxprimkey\').val(\'0\');jQuery(\'#taxname\').val(\'New Tax\');jQuery(\'#countriestotax\').val(\'\');jQuery(\'#statestotax\').val(\'\');jQuery(\'#taxpercent\').val(\'0.0\');setMaps(\'\',\'\');return false;">Create New Tax</a><br />Use the image maps below, to select all applicable areas where this tax applies.</th></tr></thead><tbody><tr><td>
                        <table>
                        <tr><td>Name this tax: </td><td><input type="hidden" name="taxprimkey" id="taxprimkey" value="0" /><input type="text" name="taxname" id="taxname" value="New Tax" /></td></tr>
                        <tr><td>Countries: </td><td><input type="text" readonly="readonly" name="countriestotax" id="countriestotax" value="" style="width:700px;" /></td></tr>
                        <tr><td>US States: </td><td><input type="text" readonly="readonly" name="statestotax" id="statestotax" value="" style="width:700px;" /></td></tr>
                        <tr><td>Tax Percentage: </td><td><input type="text" name="taxpercent" id="taxpercent" value="0.00" style="width:45px;" />%</td></tr>
                        <tr><td></td><td><button class="button-primary" onclick="addwpsctax();return false;">Submit Tax</button></td></tr>
                        </table>
                        </td></tr></table>

                        <h3>Tax By Country</h3>
                        <center>
                        <img class="map" id="world_image" src="'.plugins_url('/images/world.png' , __FILE__).'" width="800" height="400" usemap="#world">
                        <img id="world_image2" src="'.plugins_url('/images/world_highlight.png' , __FILE__).'" width="800" height="400" style="display:none;" >
                        </center>

                        <map name="world" id="world_image_map">
                        <area shape="poly" title="Antarctica" alt="Antarctica" href="#" coords="778,386, 783,386, 783,386, 800,388, 800,400, 667,400, 534,400, 400,400, 267,400, 134,400, 0,400, 0,388, 91,390, 92,389, 61,387, 60,387, 60,386, 61,385, 63,384, 57,383, 56,382, 58,382, 52,381, 52,381, 70,381, 77,379, 54,375, 58,375, 57,374, 53,375, 49,373, 50,372, 76,373, 76,371, 68,370,137,366, 138,366, 140,366, 145,366, 145,365, 147,365, 149,365, 149,366, 149,366, 151,367, 152,366, 152,366, 154,365, 155,366, 156,367, 155,367, 155,368, 163,368, 166,367, 178,368, 179,367, 179,367, 176,367, 178,367, 178,366, 177,366, 175,366, 174,365, 172,365, 171,364, 179,364, 178,364, 171,363, 171,363, 171,362, 173,362,174,363, 202,363, 202,362, 204,362, 204,363, 209,363, 210,364, 219,365, 220,364, 221,363, 224,363, 226,364, 251,362, 252,361, 252,360, 250,359, 249,357, 248,357, 248,356, 249,355, 251,354, 252,353, 251,351, 250,351, 250,349, 273,341, 274,342, 270,343, 268,343, 262,347, 262,348, 261,349, 259,348, 255,350, 255,351, 256,353,259,353, 260,354, 263,357, 264,358, 265,361, 266,362, 264,365, 261,367, 260,367, 258,369, 232,370, 231,370, 226,369, 226,370, 226,371, 232,373, 237,373, 239,373, 219,373, 221,374, 228,375, 228,375, 218,376, 216,374, 214,374, 214,375, 218,377, 231,377, 231,377, 231,378, 223,378, 269,384, 270,385, 305,383, 308,381, 337,379,338,378, 337,378, 336,377, 320,376, 320,375, 360,369, 363,367, 369,365, 367,365, 365,365, 363,365, 364,364, 368,363, 369,362, 375,361, 375,361, 373,360, 373,359, 375,359, 375,360, 379,358, 380,359, 381,360, 384,360, 384,359, 385,358, 387,357, 388,358, 387,359, 387,359, 398,359, 399,360, 422,356, 424,358, 425,358, 427,358,429,357, 474,355, 474,354, 475,353, 477,353, 478,354, 481,355, 482,355, 483,355, 484,354, 485,355, 484,355, 485,356, 486,355, 486,355, 486,356, 486,356, 488,355, 489,354, 503,351, 503,351, 503,350, 504,350, 505,350, 505,351, 506,351, 509,350, 508,350, 508,349, 511,349, 512,350, 513,350, 512,349, 512,349, 512,348, 512,348,523,347, 526,348, 528,348, 528,349, 527,349, 527,349, 555,351, 556,352, 556,353, 555,354, 554,355, 554,356, 553,356, 552,356, 552,356, 551,357, 553,358, 554,357, 554,357, 553,358, 552,358, 550,360, 550,361, 552,361, 555,361, 561,357, 574,353, 575,352, 596,349, 596,348, 597,347, 597,347, 598,349, 620,348, 621,349, 625,347,640,348, 641,349, 646,348, 646,348, 647,347, 652,347, 654,347, 655,348, 658,348, 664,349, 679,349, 680,348, 682,348, 683,349, 687,349, 688,348, 690,348, 696,348, 697,347, 699,348, 699,346, 699,345, 700,345, 701,346, 700,347, 724,350, 724,350, 724,351, 727,352, 730,351, 730,352, 736,353, 736,353, 737,354, 742,354, 742,353,743,353, 744,353, 744,354, 748,354, 750,354, 758,356, 759,357, 760,357, 762,357, 763,357, 764,357, 764,357, 773,358, 774,358, 779,360, 779,359, 780,360, 780,360, 778,361, 778,361, 778,361, 779,361, 779,362, 778,362, 778,362, 778,362, 777,363, 776,363, 776,364, 767,365, 768,366, 766,367, 765,366, 764,367, 764,367, 762,367,762,368, 763,368, 762,369, 762,369, 762,370, 762,370, 762,371, 762,371, 762,372, 763,372, 764,372, 764,372, 764,373, 765,373, 766,374, 768,374, 769,375, 771,375, 758,376, 757,376, 756,376, 757,377, 757,377, 756,378, 755,378, 752,379, 753,379, 755,379, 756,380, 756,380, 757,380, 758,382, 775,385, 775,385, 773,386, 773,386,778,386, 778,386, 778,386"/>
                        <area shape="poly" title="Antarctica" alt="Antarctica" href="#" coords="36,377, 37,376, 39,376, 48,377, 47,378, 36,377, 36,377"/>
                        <area shape="poly" title="Antarctica" alt="Antarctica" href="#" coords="278,380, 279,379, 281,378, 286,378, 287,377, 288,375, 292,374, 303,375, 303,377, 303,378, 301,379, 278,380, 278,380, 278,380"/>
                        <area shape="poly" title="Antarctica" alt="Antarctica" href="#" coords="238,362, 239,361, 240,361, 241,361, 241,360, 236,361, 234,361, 234,360, 241,358, 240,357, 241,357, 241,355, 240,354, 244,353, 244,354, 249,359, 248,361, 245,362, 238,362, 238,362, 238,362"/>
                        <area shape="poly" title="Antarctica" alt="Antarctica" href="#" coords="173,361, 185,360, 188,361, 185,362, 173,361, 173,361, 173,361"/>
                        <area shape="poly" title="Antarctica" alt="Antarctica" href="#" coords="65,371, 67,371, 68,371, 69,372, 65,371, 65,371, 65,371"/>
                        <!-- north america -->
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="251,100, 251,100, 251,100, 251,100, 251,99, 251,99, 251,99, 251,99, 251,99, 251,99, 250,96, 250,96, 250,95, 249,95, 249,96, 248,95, 248,95, 247,95, 244,100, 242,100, 242,101, 235,100, 231,102, 230,103, 230,103, 226,104, 225,104, 225,104, 225,105, 218,108, 217,108, 216,107, 216,107, 217,106,218,104, 217,100, 215,99, 215,98, 215,98, 214,98, 214,98, 213,97, 213,97, 213,97, 212,97, 212,96, 204,93, 202,94, 201,94, 200,94, 199,94, 199,94, 199,93, 197,94, 197,94, 196,93, 196,93, 196,93, 195,93, 195,93, 195,93, 194,93, 194,93, 193,92, 193,93, 190,90, 190,91, 190,91, 128,91, 127,91, 127,91, 127,90, 126,90, 126,90,126,90, 125,89, 125,89, 125,90, 124,90, 123,89, 123,88, 122,88, 120,88, 120,87, 120,87, 119,87, 118,87, 118,87, 117,87, 117,87, 117,86, 118,86, 118,85, 116,85, 117,85, 118,84, 118,84, 117,84, 116,84, 116,83, 115,83, 114,82, 114,82, 115,81, 114,81, 113,82, 112,81, 111,80, 111,79, 112,78, 111,77, 112,76, 108,75, 100,68, 99,68,99,68, 97,69, 96,69, 96,70, 92,67, 92,67, 92,67, 88,67, 88,66, 87,46, 99,48, 100,48, 99,47, 99,47, 98,46, 100,46, 101,46, 101,46, 109,45, 110,45, 112,45, 112,45, 107,46, 106,47, 104,47, 104,47, 106,48, 106,48, 106,47, 107,47, 108,47, 117,44, 117,44, 115,43, 117,44, 118,44, 119,45, 122,46, 122,46, 123,46, 122,46, 122,45,124,45, 124,45, 124,46, 124,46, 126,46, 127,46, 127,45, 147,48, 147,49, 144,49, 144,50, 161,51, 161,51, 160,51, 160,51, 162,53, 162,52, 162,52, 163,52, 163,51, 162,51, 161,50, 162,49, 165,49, 165,49, 165,49, 171,49, 171,50, 182,49, 184,50, 185,50, 185,50, 185,49, 182,49, 182,49, 187,49, 187,49, 187,49, 187,50, 187,51, 189,51,189,51, 189,50, 189,49, 189,49, 190,49, 192,48, 193,48, 193,48, 193,48, 193,47, 191,47, 191,47, 192,47, 192,47, 193,47, 194,47, 194,47, 194,46, 187,45, 186,45, 186,44, 188,44, 186,43, 186,43, 187,42, 187,42, 188,42, 189,42, 189,42, 189,42, 188,41, 188,41, 188,41, 189,41, 192,41, 194,41, 194,42, 194,42, 194,43, 197,44, 197,45,196,45, 195,45, 195,45, 197,46, 198,46, 200,46, 199,46, 199,47, 199,47, 200,47, 200,48, 200,49, 201,49, 201,48, 202,47, 203,47, 203,47, 205,48, 206,48, 206,49, 204,49, 204,49, 204,49, 207,51, 209,51, 209,50, 210,49, 211,48, 211,48, 213,48, 213,47, 211,46, 211,46, 212,45, 217,46, 218,47, 219,46, 220,47, 220,48, 219,48, 219,48,219,48, 218,48, 218,48, 217,48, 217,49, 220,50, 220,51, 220,51, 218,52, 217,52, 216,53, 213,51, 213,51, 212,52, 215,53, 212,53, 212,53, 211,52, 208,53, 208,53, 210,53, 210,54, 207,55, 206,55, 201,54, 199,54, 207,56, 206,57, 204,58, 203,58, 201,58, 201,58, 201,59, 201,59, 193,58, 199,60, 199,60, 199,60, 199,61, 196,61, 195,61,197,61, 196,62, 193,63, 193,64, 193,64, 192,64, 191,65, 190,67, 190,68, 190,68, 190,70, 191,70, 191,70, 191,70, 194,70, 194,70, 195,73, 195,74, 200,73, 205,75, 206,76, 218,78, 218,78, 218,80, 218,80, 218,81, 218,82, 218,82, 218,83, 219,83, 219,84, 219,84, 219,85, 220,85, 222,86, 222,86, 223,86, 224,87, 224,87, 224,86, 225,86,225,86, 226,87, 226,86, 226,85, 226,85, 226,84, 226,84, 226,83, 226,83, 226,82, 225,81, 226,81, 226,81, 225,80, 225,80, 224,80, 224,79, 229,77, 231,75, 231,74, 230,73, 227,70, 226,70, 226,70, 227,69, 228,68, 228,68, 229,68, 229,68, 229,67, 229,66, 229,66, 229,66, 229,66, 227,65, 229,64, 229,64, 228,63, 227,63, 227,62, 228,62,229,62, 237,62, 237,62, 241,64, 241,64, 246,65, 246,65, 247,65, 247,66, 246,66, 246,66, 247,69, 247,69, 246,69, 246,69, 245,70, 246,70, 248,70, 249,70, 249,70, 249,71, 249,71, 250,71, 252,71, 253,70, 253,70, 253,70, 254,71, 254,71, 254,70, 255,69, 256,69, 255,68, 255,68, 255,68, 256,68, 257,67, 257,67, 259,68, 260,69, 261,70,261,70, 261,70, 261,71, 261,71, 262,71, 262,72, 263,72, 263,72, 263,73, 263,73, 263,73, 264,74, 264,74, 264,74, 264,74, 264,74, 263,74, 262,74, 262,74, 263,75, 263,75, 264,76, 264,76, 266,77, 267,77, 267,77, 266,78, 267,78, 268,78, 273,79, 273,79, 267,81, 267,81, 267,82, 267,82, 273,80, 274,80, 274,81, 273,81, 274,81, 274,82,275,81, 277,82, 277,82, 277,82, 277,83, 276,83, 276,84, 277,84, 277,85, 274,86, 271,86, 268,88, 253,89, 252,89, 252,90, 251,90, 251,91, 248,92, 244,95, 243,97, 247,94, 256,91, 257,91, 258,92, 258,92, 258,92, 258,93, 256,94, 255,94, 254,93, 253,94, 254,94, 255,94, 255,95, 256,94, 256,94, 257,94, 257,94, 257,95, 257,95, 256,95,256,95, 255,96, 257,96, 257,96, 257,96, 257,97, 257,97, 257,97, 257,97, 257,97, 257,97, 257,98, 258,98, 258,98, 258,98, 259,98, 259,98, 258,98, 258,98, 259,98, 259,98, 260,98, 260,99, 260,99, 260,99, 261,99, 261,99, 261,99, 262,99, 261,99, 262,99, 262,99, 263,98, 263,99, 264,99, 264,99, 265,99, 264,99, 264,100, 265,100,265,100, 265,100, 265,100, 264,100, 264,100, 264,100, 264,100, 263,100, 263,100, 263,100, 263,100, 262,100, 262,101, 262,101, 261,101, 261,101, 261,101, 261,101, 260,101, 260,101, 260,101, 260,101, 260,101, 260,102, 259,102, 259,101, 259,101, 259,101, 259,101, 258,102, 258,101, 258,101, 258,102, 258,102, 258,102, 258,102,258,102, 258,102, 257,102, 257,103, 257,103, 257,103, 257,103, 257,103, 256,103, 256,103, 256,103, 256,103, 256,103, 256,103, 256,103, 256,103, 255,103, 255,104, 255,104, 255,104, 254,103, 254,103, 254,102, 254,102, 254,101, 254,101, 254,101, 254,102, 254,102, 255,101, 255,101, 255,101, 255,101, 258,100, 258,100, 258,100,258,100, 258,100, 258,100, 260,100, 260,100, 260,100, 260,99, 257,100, 257,99, 258,99, 258,99, 258,99, 258,98, 257,99, 257,99, 257,98, 257,98, 257,99, 257,99, 254,100, 254,100, 254,100, 253,100, 253,100, 252,100, 252,100, 251,100, 251,100, 251,100"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="231,51, 230,51, 230,51, 229,51, 229,50, 230,49, 232,49, 234,49, 234,50, 233,51, 231,51, 231,51, 231,51"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="186,48, 180,47, 180,47, 182,46, 182,46, 183,45, 184,45, 187,46, 187,47, 187,47, 189,48, 187,48, 186,48, 186,48"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="146,39, 148,39, 149,38, 153,39, 153,40, 156,39, 155,38, 155,38, 159,39, 159,39, 159,40, 160,41, 161,41, 162,41, 162,40, 161,39, 160,38, 160,37, 166,39, 167,39, 167,40, 169,41, 169,41, 168,42, 168,43, 176,45, 177,46, 176,46, 174,45, 171,46, 171,46, 174,46, 175,47, 175,47, 172,47, 165,46,164,46, 163,46, 163,46, 162,47, 148,48, 148,47, 148,47, 148,47, 142,46, 140,45, 152,44, 153,44, 137,43, 137,43, 144,42, 144,42, 144,41, 140,42, 138,41, 137,41, 136,41, 136,40, 138,39, 146,37, 147,38, 147,38, 146,38, 146,39, 146,39, 146,39"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="244,61, 243,61, 242,60, 241,60, 242,60, 242,59, 240,59, 238,57, 238,57, 235,56, 235,57, 235,57, 233,57, 232,58, 227,57, 227,57, 227,56, 228,56, 229,56, 229,56, 229,55, 232,56, 233,56, 233,56, 233,56, 233,55, 237,55, 238,55, 236,54, 236,53, 236,53, 238,52, 239,52, 240,51, 240,50, 238,49,235,48, 235,47, 235,47, 234,47, 233,47, 233,48, 231,48, 230,47, 233,47, 233,47, 230,45, 229,45, 228,45, 228,45, 225,43, 224,44, 222,43, 222,43, 222,44, 225,44, 226,45, 226,45, 225,45, 221,44, 220,45, 220,45, 221,45, 221,46, 219,45, 218,45, 209,44, 209,44, 208,44, 208,44, 207,44, 205,44, 203,43, 203,43, 203,43, 207,43, 207,43,205,42, 201,41, 201,40, 202,40, 202,40, 202,40, 201,40, 201,39, 202,39, 205,37, 205,37, 206,37, 211,36, 208,38, 208,39, 208,39, 209,39, 209,40, 209,40, 209,40, 211,42, 211,42, 210,42, 210,42, 212,43, 213,42, 213,41, 213,41, 211,41, 211,40, 211,40, 212,40, 213,40, 212,39, 212,39, 211,39, 211,39, 211,38, 212,38, 214,38, 212,37,220,37, 222,38, 222,38, 221,40, 221,40, 222,40, 223,40, 223,40, 224,40, 225,40, 225,40, 225,41, 226,41, 226,41, 226,40, 226,40, 227,39, 233,39, 236,40, 236,41, 234,41, 234,41, 234,42, 235,42, 236,41, 237,41, 237,41, 238,42, 240,41, 242,42, 242,42, 241,43, 244,42, 244,43, 245,44, 247,43, 249,44, 248,44, 246,44, 246,45, 246,45,249,45, 249,44, 250,44, 251,44, 252,45, 250,45, 249,46, 249,46, 252,46, 253,47, 249,46, 248,47, 250,47, 250,47, 248,47, 248,48, 252,49, 252,49, 252,49, 252,49, 253,49, 255,50, 257,49, 259,51, 257,51, 257,51, 261,51, 260,52, 262,52, 263,52, 265,52, 264,53, 264,53, 264,53, 263,53, 262,53, 262,53, 263,54, 263,54, 262,54, 261,55,261,55, 260,54, 260,55, 260,56, 260,56, 258,56, 258,55, 258,55, 258,55, 257,55, 256,55, 256,54, 256,54, 256,54, 255,54, 252,52, 251,52, 251,53, 250,53, 250,53, 252,54, 251,54, 249,53, 250,55, 251,55, 253,57, 253,56, 256,57, 256,57, 255,58, 258,59, 258,59, 258,60, 256,59, 256,59, 256,59, 257,60, 257,61, 256,60, 256,61, 257,62,255,60, 255,60, 254,60, 250,59, 250,59, 249,59, 248,59, 248,59, 254,62, 254,63, 247,62, 246,61, 244,61, 244,61, 244,61, 244,61"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="181,42, 173,39, 173,38, 177,39, 178,38, 179,38, 179,38, 177,37, 176,36, 176,36, 178,36, 179,36, 179,36, 180,37, 181,37, 182,36, 185,36, 185,37, 184,37, 184,37, 182,38, 182,38, 182,38, 183,38, 184,38, 186,39, 187,39, 186,40, 186,41, 185,41, 184,41, 182,41, 182,41, 183,41, 182,42, 181,42,181,42, 181,42"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="191,40, 190,40, 189,39, 188,38, 188,38, 189,37, 189,37, 190,37, 190,36, 190,36, 190,36, 190,36, 200,36, 200,36, 196,39, 191,39, 193,39, 193,40, 192,40, 191,40, 191,40, 191,40"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="128,42, 126,42, 126,41, 124,41, 122,40, 121,40, 121,40, 123,39, 123,39, 122,38, 123,38, 123,38, 124,38, 125,37, 125,37, 125,36, 124,35, 123,35, 144,37, 133,40, 133,41, 133,41, 132,41, 130,42, 129,42, 128,42, 128,42, 128,42"/>

                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="200,31, 199,31, 198,32, 202,32, 203,33, 204,33, 204,33, 204,32, 206,32, 209,33, 210,32, 220,32, 224,33, 224,33, 222,34, 222,34, 222,35, 216,34, 216,34, 215,34, 215,34, 215,34, 209,34, 209,35, 205,35, 205,34, 204,34, 203,34, 203,34, 202,34, 200,35, 198,34, 198,34, 197,34, 196,33, 196,32,196,32, 196,32, 194,31, 194,30, 201,31, 200,31, 200,31, 200,31"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="179,34, 178,33, 178,33, 179,33, 179,32, 178,32, 174,32, 173,32, 173,32, 174,32, 174,31, 175,31, 174,31, 175,30, 178,32, 179,31, 179,31, 177,31, 177,30, 178,30, 181,31, 181,30, 182,30, 183,30, 183,30, 184,31, 184,32, 184,32, 183,32, 183,33, 183,33, 183,34, 179,34, 179,34, 179,34"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="145,32, 143,32, 142,31, 147,31, 148,31, 150,31, 153,32, 158,32, 158,32, 158,31, 155,31, 156,30, 157,30, 159,30, 159,30, 160,30, 160,31, 160,32, 164,32, 163,32, 164,31, 164,31, 166,32, 166,32, 166,33, 166,33, 165,34, 147,35, 147,34, 153,34, 154,33, 140,33, 140,33, 141,32, 145,32, 145,32,145,32"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="192,31, 188,31, 186,30, 186,29, 191,30, 192,30, 192,30, 192,30, 192,31, 192,31, 192,31"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="133,32, 131,31, 129,31, 128,31, 128,31, 128,30, 133,29, 134,29, 142,28, 144,28, 142,29, 142,29, 142,29, 142,30, 141,30, 140,31, 139,31, 138,30, 137,29, 135,31, 135,31, 135,31, 134,32, 133,32, 133,32, 133,32"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="157,27, 155,28, 150,28, 149,28, 150,27, 157,27, 157,27, 157,27"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="186,27, 184,27, 182,26, 182,25, 182,25, 189,26, 189,26, 189,27, 186,27, 186,27, 186,27"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="179,28, 178,27, 177,27, 168,26, 167,26, 168,26, 171,26, 171,26, 171,25, 166,24, 166,24, 171,24, 173,25, 179,26, 181,27, 181,27, 179,28, 179,28"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="200,21, 201,21, 202,21, 203,22, 204,22, 204,22, 204,22, 205,22, 206,22, 207,22, 207,23, 207,23, 207,24, 210,24, 211,24, 210,24, 207,25, 206,25, 206,26, 205,26, 204,25, 203,25, 204,26, 204,26, 203,26, 203,26, 201,26, 200,26, 200,26, 195,26, 192,25, 191,25, 199,25, 190,24, 186,23, 186,22,190,22, 188,22, 189,22, 191,21, 191,21, 190,20, 194,20, 194,20, 193,19, 195,20, 199,21, 200,21, 200,21, 200,21"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="200,19, 198,19, 198,19, 209,18, 209,18, 223,18, 223,18, 219,17, 220,17, 226,16, 232,17, 232,17, 231,16, 231,16, 238,17, 238,16, 238,16, 241,16, 243,16, 243,16, 243,16, 243,16, 263,17, 263,18, 251,18, 250,19, 251,19, 255,19, 256,19, 256,19, 244,21, 245,22, 236,23, 235,23, 238,24, 229,24,229,24, 234,25, 234,25, 234,25, 233,26, 232,26, 231,26, 231,26, 231,27, 232,27, 231,27, 227,27, 227,28, 227,28, 226,29, 220,28, 218,29, 227,29, 228,30, 228,30, 227,30, 221,31, 220,30, 217,30, 217,30, 218,31, 215,30, 213,30, 212,30, 213,30, 213,31, 205,30, 205,30, 204,31, 202,30, 202,30, 202,29, 207,29, 205,28, 205,28, 216,29,216,28, 216,28, 212,28, 212,27, 212,27, 208,27, 208,26, 209,25, 219,25, 211,23, 210,23, 209,22, 209,22, 209,22, 221,23, 218,23, 219,22, 229,21, 217,21, 215,21, 211,21, 211,20, 212,20, 212,20, 210,20, 202,21, 202,20, 202,20, 203,20, 202,20, 202,20, 200,19, 200,19, 200,19"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="212,60, 211,60, 211,59, 211,59, 207,59, 207,59, 210,58, 209,57, 209,57, 211,54, 212,55, 211,55, 212,56, 213,56, 213,55, 219,57, 219,57, 219,58, 222,58, 223,59, 221,59, 218,59, 217,58, 215,58, 215,58, 215,59, 215,59, 214,59, 213,60, 212,60, 212,60, 212,60"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="266,99, 265,99, 265,99, 264,98, 264,97, 266,96, 267,96, 266,97, 266,97, 266,97, 265,98, 265,98, 266,98, 266,98, 267,98, 267,98, 268,98, 268,98, 266,99, 266,99, 266,99, 266,99"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="272,92, 270,92, 271,91, 271,91, 272,91, 272,91, 272,90, 275,87, 276,86, 277,86, 278,86, 278,86, 277,86, 277,86, 277,87, 277,87, 275,90, 275,90, 275,90, 275,90, 276,89, 276,89, 277,89, 276,90, 277,90, 278,91, 279,91, 279,91, 280,90, 282,91, 280,92, 281,93, 282,93, 282,92, 283,92, 283,93,282,93, 281,93, 281,93, 281,94, 281,94, 282,94, 282,94, 283,94, 283,95, 283,95, 284,95, 284,95, 283,97, 282,96, 282,96, 281,96, 281,96, 281,96, 281,95, 281,95, 281,94, 280,94, 280,95, 279,95, 278,96, 277,96, 277,96, 277,96, 278,95, 278,95, 279,94, 278,94, 277,95, 277,95, 277,95, 277,94, 276,94, 276,94, 276,95, 269,95, 269,94,272,92, 272,92, 272,92"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="125,93, 123,92, 123,92, 123,92, 123,91, 122,91, 122,91, 121,91, 121,91, 121,90, 120,90, 119,90, 120,90, 119,89, 118,89, 118,89, 115,88, 115,88, 121,88, 123,90, 126,91, 126,92, 126,92, 126,93, 125,93, 125,93, 125,93"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="106,82, 105,81, 105,81, 105,80, 105,80, 107,80, 108,81, 108,81, 107,81, 107,82, 106,82, 106,82, 106,82"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="107,79, 105,77, 105,76, 104,76, 104,75, 105,75, 105,76, 106,76, 106,76, 106,77, 106,77, 107,78, 107,79, 107,79, 107,79"/>
                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="101,74, 101,74, 101,75, 100,73, 100,73, 100,73, 101,73, 101,74, 101,74, 101,74"/>

                        <area shape="poly" title="Canada" alt="Canada" href="#" coords="100,71, 101,72, 100,72, 99,72, 99,73, 99,73, 97,72, 98,71, 98,71, 99,71, 99,71, 100,71, 100,71, 100,71"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="140,128, 140,128, 140,128, 140,128, 140,127, 140,126, 139,125, 138,125, 138,125, 138,125, 138,125, 138,125, 137,125, 138,125, 137,125, 137,125, 137,125, 137,125, 136,124, 135,124, 135,124, 133,124, 133,124, 133,124, 133,123, 133,123, 132,123, 133,122, 132,122, 132,122, 132,122, 132,122,132,122, 132,122, 132,121, 131,121, 131,121, 131,120, 130,120, 130,120, 130,120, 130,119, 130,119, 130,119, 130,118, 130,118, 130,118, 129,118, 129,118, 129,118, 129,117, 129,117, 128,117, 128,117, 128,116, 129,116, 129,116, 129,117, 129,117, 129,117, 129,117, 129,117, 129,116, 129,116, 129,116, 129,116, 129,116, 128,116,128,116, 128,116, 128,116, 128,116, 127,116, 127,115, 126,114, 126,114, 125,113, 126,112, 124,111, 124,111, 125,108, 124,105, 124,105, 124,104, 125,104, 125,103, 125,103, 124,103, 125,99, 125,99, 125,98, 125,98, 125,98, 125,98, 125,98, 126,98, 126,98, 125,97, 125,97, 125,96, 125,96, 125,96, 125,96, 125,96, 125,96, 125,96,125,96, 125,96, 125,95, 124,94, 124,93, 124,93, 127,93, 128,94, 128,94, 127,95, 128,95, 128,95, 128,95, 128,95, 129,95, 129,94, 128,92, 128,91, 190,91, 190,91, 190,90, 193,93, 193,92, 194,93, 194,93, 195,93, 195,93, 195,93, 196,93, 196,93, 196,93, 197,94, 197,94, 199,93, 199,94, 199,94, 200,94, 201,94, 202,94, 204,93, 212,96,212,97, 213,97, 213,97, 213,97, 213,98, 214,98, 215,98, 215,98, 215,99, 217,100, 218,103, 218,104, 217,106, 216,107, 216,107, 217,108, 218,108, 225,105, 225,104, 225,104, 226,104, 230,103, 230,103, 231,102, 235,100, 242,101, 242,100, 244,100, 247,95, 248,95, 248,95, 248,95, 249,95, 250,95, 250,96, 250,96, 251,99, 251,99,251,99, 251,99, 251,99, 251,99, 251,100, 251,100, 251,100, 251,100, 252,100, 252,101, 252,101, 252,101, 252,101, 251,101, 251,101, 251,101, 251,101, 250,101, 250,101, 250,101, 250,102, 250,102, 250,102, 249,101, 249,102, 248,102, 249,102, 248,102, 248,101, 248,101, 247,103, 247,102, 246,103, 246,103, 246,103, 246,103, 246,103,246,103, 245,103, 245,103, 245,103, 245,103, 245,104, 244,104, 244,104, 243,105, 243,105, 244,106, 244,106, 244,106, 244,106, 244,106, 243,106, 243,106, 243,106, 243,106, 243,106, 244,107, 244,108, 245,108, 245,107, 245,107, 245,107, 245,107, 245,107, 245,107, 245,107, 245,107, 245,107, 245,108, 244,108, 244,108, 244,108,244,108, 243,108, 243,108, 243,108, 243,108, 243,108, 243,108, 243,107, 242,108, 242,107, 242,108, 237,109, 237,110, 236,110, 236,110, 236,110, 237,109, 237,109, 236,110, 236,110, 236,110, 236,110, 236,111, 236,112, 236,113, 235,112, 235,113, 235,113, 234,114, 234,114, 234,114, 234,113, 234,113, 234,113, 233,113, 233,113,233,112, 233,112, 233,112, 233,112, 233,112, 233,113, 233,113, 234,114, 234,114, 234,115, 234,115, 234,115, 233,117, 233,117, 233,117, 233,117, 232,118, 232,118, 232,118, 232,117, 232,116, 233,116, 233,116, 233,116, 232,116, 232,116, 232,116, 232,116, 232,115, 232,115, 232,115, 232,115, 232,115, 231,115, 231,115, 232,115,232,115, 232,115, 231,114, 231,114, 231,114, 231,114, 231,114, 232,114, 232,114, 232,114, 231,114, 231,114, 231,114, 232,114, 232,113, 231,113, 231,113, 232,113, 232,113, 232,113, 232,112, 232,112, 232,113, 232,113, 231,113, 231,113, 231,113, 231,113, 231,113, 231,113, 231,113, 231,113, 231,113, 231,114, 231,114, 231,115,231,115, 231,115, 230,115, 230,115, 231,115, 231,115, 231,116, 231,115, 230,115, 230,115, 230,115, 230,115, 229,115, 229,115, 229,115, 230,114, 229,114, 229,115, 229,115, 230,115, 230,115, 231,116, 231,116, 231,116, 231,116, 231,117, 231,117, 230,116, 231,117, 231,117, 231,117, 231,117, 231,117, 231,117, 231,117, 231,117,231,117, 231,117, 231,118, 230,117, 230,117, 231,117, 231,118, 231,118, 231,118, 231,118, 231,118, 230,118, 230,117, 230,118, 230,118, 230,118, 231,118, 231,118, 231,118, 231,118, 232,118, 233,121, 233,121, 232,119, 232,119, 232,119, 232,119, 232,119, 232,119, 232,120, 232,120, 232,120, 232,120, 232,120, 231,120, 231,120,232,120, 231,120, 231,120, 231,120, 231,120, 231,120, 230,120, 230,120, 230,120, 230,120, 230,120, 230,120, 230,120, 230,120, 232,120, 232,121, 232,121, 232,121, 232,121, 232,121, 232,120, 232,121, 233,121, 232,121, 232,121, 232,121, 232,122, 232,122, 231,122, 231,121, 230,121, 231,122, 231,122, 230,121, 230,122, 230,122,231,122, 231,122, 230,122, 231,122, 230,122, 230,123, 230,123, 230,122, 230,123, 230,123, 231,123, 231,123, 231,122, 231,123, 231,123, 231,123, 231,123, 231,123, 229,123, 228,125, 225,126, 225,126, 224,127, 223,128, 222,128, 220,132, 222,140, 222,140, 223,140, 223,141, 223,143, 222,144, 222,144, 222,144, 222,145, 222,145,222,145, 222,144, 221,145, 221,144, 221,145, 220,144, 220,144, 220,143, 219,143, 219,142, 219,142, 218,141, 218,141, 218,141, 217,139, 217,139, 218,139, 218,139, 217,138, 217,139, 217,139, 217,136, 217,136, 217,136, 216,135, 214,134, 214,134, 213,134, 212,134, 211,135, 211,134, 211,134, 211,133, 210,133, 210,133, 209,133,209,133, 207,133, 207,133, 207,133, 206,133, 205,132, 205,133, 202,133, 202,133, 201,133, 200,133, 200,133, 200,133, 200,134, 201,134, 201,133, 201,134, 202,134, 201,134, 201,135, 202,135, 202,135, 203,136, 202,136, 202,136, 202,135, 201,135, 201,135, 200,135, 200,135, 201,136, 200,136, 200,135, 199,135, 199,136, 198,135,198,135, 196,134, 196,134, 196,135, 196,135, 193,134, 192,134, 191,135, 190,135, 190,134, 190,134, 190,135, 190,135, 188,136, 187,137, 186,137, 186,137, 186,137, 186,138, 184,138, 185,139, 185,139, 184,140, 184,140, 184,141, 185,143, 183,143, 183,143, 181,142, 180,141, 179,139, 176,135, 175,134, 174,134, 173,134, 172,136,171,136, 171,136, 168,135, 168,134, 167,133, 164,130, 160,130, 160,131, 160,131, 154,131, 146,128, 145,128, 140,128, 140,128, 140,128"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="53,155, 53,155, 53,154, 52,154, 52,154, 53,154, 53,154, 53,154, 54,154, 54,154, 54,155, 53,155, 53,155"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="50,153, 50,153, 50,153, 50,153, 49,153, 49,153, 49,153, 49,153, 49,152, 49,152, 50,153, 50,153, 50,153, 50,153, 50,153"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="55,158, 54,158, 54,157, 54,156, 54,156, 54,156, 54,156, 54,155, 54,155, 56,156, 56,157, 56,157, 56,157, 57,157, 56,157, 56,157, 56,157, 55,158, 55,158, 55,158, 55,158, 55,158"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="23,60, 21,59, 19,59, 18,59, 19,59, 25,60, 25,60, 24,60, 23,60, 23,60, 23,60"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="112,78, 112,78, 111,78, 111,78, 110,78, 110,78, 110,77, 110,77, 109,76, 109,76, 108,76, 107,76, 107,76, 107,76, 107,75, 106,74, 104,73, 104,73, 104,72, 104,72, 104,72, 104,72, 104,72, 103,72, 101,70, 100,69, 100,69, 100,69, 100,71, 100,71, 99,71, 98,70, 98,70, 96,69, 98,70, 98,70, 96,71,90,68, 91,68, 91,68, 91,67, 91,67, 88,68, 87,67, 87,67, 86,67, 86,67, 86,67, 85,67, 80,67, 79,66, 79,66, 78,66, 77,66, 77,66, 76,66, 72,64, 71,65, 71,65, 71,65, 70,65, 70,66, 71,66, 71,66, 71,67, 71,67, 68,67, 64,69, 63,69, 64,69, 64,68, 64,68, 63,67, 63,67, 65,66, 65,66, 66,65, 69,65, 69,65, 67,64, 66,64, 65,64, 61,66,61,67, 61,67, 59,68, 58,69, 58,69, 58,69, 59,69, 60,70, 60,70, 59,70, 58,71, 57,71, 53,73, 53,73, 53,74, 49,75, 48,76, 47,76, 45,76, 44,76, 44,76, 44,76, 47,74, 48,74, 48,74, 48,74, 50,72, 50,72, 51,71, 51,70, 52,70, 52,70, 49,70, 49,69, 48,70, 48,70, 48,70, 48,71, 48,71, 46,69, 43,69, 43,70, 43,70, 41,70, 41,70, 41,69,41,68, 41,68, 40,67, 36,68, 34,66, 34,66, 34,66, 34,65, 37,66, 37,66, 37,65, 37,65, 36,65, 36,65, 35,65, 33,64, 32,64, 31,63, 32,63, 32,63, 33,62, 33,62, 36,60, 38,60, 39,60, 40,60, 43,59, 43,59, 43,59, 43,58, 42,57, 42,57, 43,57, 43,57, 43,56, 42,56, 39,57, 31,57, 31,57, 30,56, 31,56, 32,56, 33,55, 27,54, 35,52, 37,53,37,53, 37,53, 41,54, 42,53, 45,53, 45,53, 45,53, 41,52, 41,51, 37,51, 37,51, 30,48, 31,48, 31,47, 37,47, 39,45, 45,44, 46,44, 45,44, 45,43, 45,43, 50,43, 52,42, 53,42, 54,42, 54,42, 54,42, 54,43, 55,43, 56,42, 61,43, 63,44, 87,46, 88,66, 88,67, 92,67, 92,67, 92,67, 96,70, 96,69, 99,68, 100,68, 108,75, 112,76, 111,77, 112,78,112,78"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="28,82, 27,82, 26,82, 26,82, 27,81, 27,81, 28,82, 28,82, 28,82, 28,82"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="30,82, 30,81, 29,81, 30,80, 31,81, 31,81, 31,81, 31,81, 30,81, 30,82, 30,82, 30,82"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="38,79, 37,79, 36,79, 36,79, 35,80, 35,80, 34,79, 34,79, 35,79, 37,78, 38,78, 38,79, 38,79, 38,79"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="40,78, 40,78, 39,78, 39,77, 40,77, 41,76, 42,76, 44,77, 44,77, 42,77, 40,78, 40,78, 40,78"/>
                        <area shape="poly" title="United States" alt="United States" href="#" coords="60,73, 60,73, 60,74, 59,74, 58,74, 57,73, 57,73, 57,73, 58,73, 58,72, 59,73, 60,72, 60,72, 61,72, 62,72, 62,73, 60,73, 60,73, 60,73, 60,73"/>
                        <!-- cental america -->
                        <area shape="poly" title="Panama" alt="Panama" href="#" coords="217,179, 218,179, 218,180, 218,180, 218,181, 219,181, 219,180, 220,181, 220,181, 221,181, 222,180, 223,180, 224,179, 225,179, 225,179, 226,179, 228,180, 228,181, 229,181, 229,182, 229,182, 229,183, 229,183, 229,183, 228,184, 228,183, 228,183, 228,184, 228,184, 228,185, 227,182, 227,182,227,182, 228,182, 228,182, 227,182, 227,181, 227,182, 227,182, 226,181, 226,181, 226,181, 226,181, 226,181, 226,181, 226,181, 225,180, 225,180, 224,181, 224,181, 224,181, 223,182, 222,182, 222,182, 222,183, 223,183, 223,184, 223,184, 223,184, 223,184, 222,184, 222,184, 221,184, 221,184, 221,184, 221,183, 220,183, 220,183,219,182, 219,182, 218,182, 218,182, 217,182, 217,182, 216,183, 217,182, 216,182, 217,181, 217,181, 217,181, 217,180, 217,179, 217,179, 217,179, 217,179, 217,179, 217,179"/>
                        <area shape="poly" title="Costa Rica"" alt="Costa Rica"" href="#" coords="215,176, 216,178, 216,178, 217,179, 217,179, 217,179, 217,179, 217,179, 217,180, 217,181, 217,181, 217,181, 216,182, 217,182, 216,183, 216,182, 216,182, 216,181, 216,181, 215,181, 216,181, 216,182, 216,182, 215,181, 215,181, 215,181, 215,180, 215,180, 214,180, 214,179, 213,179, 213,179,213,179, 213,178, 211,178, 212,178, 212,178, 212,179, 212,179, 212,179, 210,178, 210,178, 210,177, 210,177, 211,177, 211,176, 210,176, 210,176, 210,176, 210,176, 210,176, 212,176, 213,176, 213,176, 213,176, 214,176, 214,177, 215,176, 215,176, 215,176"/>
                        <area shape="poly" title="Nicagarua" alt="Nicagarua" href="#" coords="210,176, 206,172, 206,172, 208,171, 208,171, 208,171, 208,170, 209,170, 210,169, 210,170, 211,169, 212,168, 212,168, 213,168, 213,168, 213,168, 215,167, 216,168, 215,169, 215,170, 214,175, 215,176, 214,177, 214,176, 213,176, 213,176, 213,176, 212,176, 210,176, 210,176, 210,176, 210,176"/>
                        <area shape="poly" title="Honduras" alt="Honduras" href="#" coords="205,165, 205,165, 206,165, 209,165, 210,165, 210,165, 213,165, 214,165, 214,166, 214,166, 214,166, 215,167, 215,167, 213,168, 213,168, 213,168, 212,168, 212,168, 211,169, 210,170, 210,169, 209,170, 208,170, 208,171, 208,171, 208,171, 207,171, 206,171, 206,170, 206,170, 206,170, 205,170,205,169, 204,170, 204,170, 204,169, 203,169, 203,169, 203,168, 202,168, 203,168, 202,167, 203,167, 205,165, 205,165, 205,165"/>

                        <area shape="poly" title="El Salvador"" alt="El Salvador"" href="#" coords="206,170, 206,171, 206,171, 206,171, 205,171, 201,170, 200,170, 200,170, 201,169, 201,169, 201,169, 202,169, 202,169, 202,168, 202,168, 203,168, 203,169, 203,169, 204,169, 204,170, 204,170, 205,169, 205,170, 206,170, 206,170, 206,170, 206,170, 206,170"/>
                        <area shape="poly" title="Belize" alt="Belize" href="#" coords="205,159, 204,160, 205,160, 205,160, 205,161, 205,162, 205,162, 205,163, 204,164, 204,164, 204,164, 204,165, 203,165, 203,165, 202,165, 203,160, 203,160, 203,161, 204,159, 205,159, 205,159"/>
                        <area shape="poly" title="Guatemala" alt="Guatemala" href="#" coords="200,170, 199,169, 198,169, 196,168, 196,167, 196,167, 196,166, 197,165, 200,165, 200,164, 199,164, 199,163, 198,163, 198,162, 198,162, 199,162, 199,161, 203,161, 202,165, 203,165, 203,165, 204,166, 204,165, 204,165, 205,165, 203,167, 202,167, 203,168, 202,168, 202,168, 202,169, 202,169,201,169, 201,169, 201,169, 200,170, 200,170, 200,170, 200,170"/>
                        <area shape="poly" title="Mexico" alt="Mexico" href="#" coords="185,143, 184,145, 183,151, 187,158, 188,159, 189,159, 191,160, 191,160, 196,159, 196,159, 197,160, 197,159, 197,159, 197,159, 199,157, 199,157, 199,156, 199,155, 200,154, 205,153, 206,153, 207,153, 207,153, 208,153, 208,153, 208,154, 206,156, 206,156, 206,157, 206,157, 206,157, 206,160,205,158, 205,159, 205,159, 204,159, 203,161, 203,160, 203,160, 203,161, 199,161, 199,162, 198,162, 198,162, 198,163, 199,163, 199,164, 200,164, 200,165, 197,165, 196,166, 196,167, 196,167, 196,168, 192,165, 190,164, 189,164, 187,165, 176,162, 174,160, 174,160, 173,160, 171,160, 168,158, 168,157, 166,155, 166,155, 167,155,167,155, 167,155, 166,154, 167,153, 167,153, 167,153, 166,152, 166,151, 166,151, 166,151, 161,145, 161,145, 161,145, 159,144, 159,144, 159,143, 158,144, 158,143, 158,143, 158,143, 158,142, 158,142, 157,141, 157,141, 156,140, 155,140, 155,140, 155,139, 155,139, 155,139, 154,138, 153,137, 149,132, 150,131, 148,131, 148,130,148,130, 148,130, 147,130, 147,130, 146,130, 145,131, 145,131, 146,132, 146,133, 147,134, 147,135, 148,136, 149,137, 149,137, 150,137, 150,137, 150,139, 150,139, 151,139, 151,139, 151,139, 152,141, 153,141, 153,141, 155,145, 155,146, 155,147, 155,147, 156,147, 156,146, 157,148, 157,148, 157,148, 158,148, 157,149, 157,150,156,150, 156,148, 152,145, 152,145, 152,144, 152,144, 151,142, 151,142, 150,142, 149,141, 149,141, 148,141, 146,140, 145,139, 146,139, 147,138, 147,138, 147,137, 144,134, 143,133, 143,133, 143,133, 143,132, 141,130, 141,130, 142,130, 141,129, 140,128, 145,128, 146,128, 154,131, 160,131, 160,131, 160,130, 164,130, 167,132,168,135, 171,136, 171,136, 172,136, 173,134, 174,134, 175,134, 176,135, 179,139, 180,141, 181,142, 183,143, 183,143, 185,143, 185,143"/>
                        <!-- caribbean -->
                        <area shape="poly" title="Haiti" alt="Haiti" href="#" coords="241,157, 241,158, 241,159, 241,159, 242,160, 241,160, 237,160, 237,160, 236,160, 235,159, 235,159, 236,159, 239,159, 240,159, 239,158, 239,158, 239,157, 239,157, 238,157, 237,157, 238,156, 238,156, 239,156, 241,157, 241,157, 241,157, 241,157"/>
                        <area shape="poly" title="Dominican Republic"" alt="Dominican Republic"" href="#" coords="242,160, 241,159, 241,159, 241,158, 241,157, 242,156, 242,156, 243,156, 243,156, 243,156, 243,156, 245,157, 245,157, 245,157, 249,159, 249,159, 248,160, 248,160, 247,159, 246,159, 246,159, 246,159, 245,159, 245,160, 244,160, 244,159, 244,159, 243,160, 242,161, 242,161, 242,160, 242,160,242,160"/>
                        <area shape="poly" title="Puerto Rico"" alt="Puerto Rico"" href="#" coords="252,161, 252,160, 251,160, 252,160, 251,159, 252,159, 252,159, 254,159, 255,160, 255,160, 254,160, 254,160, 252,160, 252,161, 252,161, 252,161"/>
                        <area shape="poly" title="Jamaica" alt="Jamaica" href="#" coords="229,161, 228,161, 227,160, 227,160, 227,160, 227,159, 228,159, 231,160, 231,160, 231,161, 231,161, 230,160, 230,160, 229,161, 229,161, 229,161, 229,161"/>
                        <area shape="poly" title="Cuba" alt="Cuba" href="#" coords="218,151, 219,150, 219,150, 217,150, 217,150, 215,151, 214,151, 214,151, 214,152, 214,152, 213,152, 213,152, 213,151, 213,151, 214,150, 218,149, 223,150, 224,151, 228,152, 229,152, 229,153, 232,154, 233,154, 232,154, 233,154, 234,154, 236,156, 228,156, 229,155, 229,155, 229,154, 227,154,226,154, 226,153, 226,152, 218,151, 218,151, 218,151"/>
                        <!-- south america -->
                        <area shape="poly" title="Suriname" alt="Suriname" href="#" coords="275,196, 275,196, 274,193, 273,193, 272,192, 272,190, 272,190, 272,190, 274,189, 274,189, 274,187, 274,187, 275,187, 277,188, 277,187, 277,187, 280,187, 281,188, 280,189, 280,190, 280,192, 281,193, 281,193, 280,195, 279,195, 279,195, 278,195, 278,195, 277,195, 276,195, 276,196, 277,197,275,196, 275,196, 275,196"/>
                        <area shape="poly" title="French Guiana"" alt="French Guiana"" href="#" coords="286,191, 286,192, 284,196, 283,196, 283,196, 282,195, 281,196, 281,196, 280,195, 281,193, 281,193, 280,192, 280,190, 281,188, 284,189, 286,191, 286,191, 286,191, 286,191"/>
                        <area shape="poly" title="Guyana" alt="Guyana" href="#" coords="266,189, 265,187, 265,187, 265,187, 265,186, 267,185, 267,185, 267,185, 266,184, 266,184, 266,184, 268,182, 268,182, 268,182, 271,184, 271,186, 271,185, 271,185, 272,185, 272,185, 273,186, 274,187, 274,189, 274,189, 272,190, 272,190, 272,190, 272,192, 273,193, 274,193, 275,196, 275,196,274,196, 271,198, 270,198, 268,197, 268,196, 268,196, 268,194, 269,192, 268,191, 267,191, 268,189, 268,189, 267,189, 267,189, 266,189, 266,189, 266,189"/>
                        <area shape="poly" title="Trinidad and Tobago"" alt="Trinidad and Tobago"" href="#" coords="265,176, 265,177, 265,177, 265,177, 265,177, 265,177, 265,178, 265,178, 264,178, 264,178, 264,178, 264,178, 263,178, 263,178, 263,178, 263,178, 263,178, 263,178, 263,178, 263,178, 264,178, 264,178, 264,177, 264,177, 264,177, 263,177, 263,177, 264,177, 264,176, 264,177, 265,176, 265,176,265,176, 265,176"/>
                        <area shape="poly" title="Venezuela" alt="Venezuela" href="#" coords="266,189, 266,189, 266,190, 264,191, 262,191, 262,191, 262,191, 261,193, 261,193, 260,192, 259,192, 257,191, 257,191, 257,191, 258,193, 259,195, 259,195, 260,195, 260,196, 260,196, 259,196, 259,196, 258,197, 258,197, 257,198, 256,199, 255,199, 255,198, 254,199, 254,199, 253,198, 252,198,252,196, 250,194, 251,193, 250,191, 250,189, 250,188, 250,187, 251,187, 247,187, 245,185, 244,185, 241,185, 240,184, 240,183, 240,182, 239,180, 238,180, 238,180, 238,180, 240,176, 241,176, 241,175, 241,175, 241,174, 242,174, 243,174, 241,174, 241,175, 241,176, 241,176, 241,176, 241,177, 241,177, 241,177, 241,178, 241,179,241,179, 241,180, 242,180, 242,180, 243,180, 243,179, 243,178, 242,177, 242,177, 242,177, 242,177, 242,176, 242,176, 245,175, 245,175, 246,175, 246,174, 246,174, 246,174, 245,174, 245,174, 245,174, 244,174, 245,174, 245,173, 245,173, 245,173, 246,174, 246,174, 246,174, 246,174, 246,175, 246,175, 248,175, 248,175, 249,176,249,177, 249,177, 254,177, 254,177, 255,178, 257,178, 259,177, 262,177, 263,177, 263,177, 262,177, 261,177, 261,177, 261,177, 262,178, 262,178, 262,179, 263,178, 263,178, 263,178, 264,179, 264,179, 266,180, 265,180, 265,181, 265,181, 264,181, 265,182, 265,181, 267,181, 268,182, 268,182, 268,182, 266,184, 266,184, 266,184,267,185, 267,185, 267,185, 265,186, 265,187, 265,187, 265,187, 266,189, 266,189, 266,189"/>
                        <area shape="poly" title="Colombia" alt="Colombia" href="#" coords="243,174, 242,174, 241,174, 241,175, 241,175, 241,176, 240,176, 238,180, 238,180, 238,180, 239,180, 240,182, 240,183, 240,184, 241,185, 244,185, 245,185, 247,187, 251,187, 250,187, 250,188, 250,189, 250,191, 251,193, 250,194, 251,196, 250,197, 246,197, 246,198, 246,198, 247,198, 247,198,247,199, 247,199, 246,199, 245,199, 245,199, 245,201, 246,202, 246,203, 247,203, 247,203, 246,210, 245,210, 245,209, 245,209, 244,209, 244,209, 245,207, 245,207, 243,206, 242,206, 242,205, 241,206, 241,206, 239,206, 239,206, 239,205, 238,205, 238,205, 238,205, 237,204, 236,203, 235,202, 234,201, 233,201, 233,201, 232,200,231,200, 230,200, 229,200, 229,200, 229,199, 228,199, 225,197, 225,197, 225,197, 225,197, 226,196, 226,197, 226,196, 226,196, 226,195, 226,195, 227,195, 227,195, 228,195, 229,192, 229,192, 229,191, 229,191, 229,190, 229,188, 229,188, 229,188, 229,188, 229,187, 229,187, 229,187, 229,186, 229,186, 228,185, 228,184, 228,184,228,183, 228,183, 228,184, 229,183, 229,183, 229,183, 229,182, 229,182, 229,181, 229,182, 230,183, 230,183, 230,183, 230,182, 230,182, 230,181, 230,181, 231,180, 232,180, 232,180, 233,180, 233,179, 233,179, 233,177, 235,176, 235,176, 235,176, 235,177, 236,176, 236,175, 236,175, 236,175, 238,175, 240,174, 240,174, 241,173,241,173, 241,173, 242,173, 243,173, 243,174, 243,174, 243,174, 243,174"/>

                        <area shape="poly" title="Equador" alt="Equador" href="#" coords="222,208, 223,208, 224,207, 224,206, 223,206, 223,205, 222,207, 221,206, 221,206, 221,205, 221,205, 221,205, 221,204, 221,203, 222,202, 222,202, 222,201, 222,201, 223,200, 223,199, 223,199, 223,199, 224,198, 225,198, 225,197, 228,199, 229,199, 229,200, 229,200, 230,200, 231,200, 232,200,233,201, 234,202, 232,205, 230,206, 227,208, 225,212, 225,212, 225,212, 224,211, 224,211, 224,210, 222,210, 222,210, 222,210, 222,209, 222,208, 222,208, 222,208"/>
                        <area shape="poly" title="Peru" alt="Peru" href="#" coords="246,225, 248,228, 248,230, 248,231, 248,232, 247,233, 247,234, 247,234, 247,235, 247,236, 247,237, 247,237, 247,238, 246,238, 246,239, 246,239, 246,239, 246,240, 246,240, 246,240, 246,241, 245,241, 244,241, 244,241, 244,241, 242,240, 242,239, 242,239, 232,232, 231,232, 231,230, 229,226,229,226, 228,226, 228,225, 228,225, 222,215, 220,214, 220,214, 220,214, 221,214, 221,213, 220,211, 220,211, 220,210, 222,208, 222,209, 222,210, 222,210, 222,210, 224,210, 224,211, 224,211, 225,212, 225,212, 225,212, 226,211, 227,208, 227,208, 230,206, 232,205, 234,202, 233,201, 234,201, 235,202, 236,203, 237,204, 238,205,238,205, 238,205, 239,205, 239,206, 239,206, 241,206, 241,206, 242,205, 242,206, 243,206, 245,207, 245,207, 244,209, 245,209, 245,209, 245,210, 246,210, 245,210, 244,210, 244,210, 243,210, 243,211, 242,210, 239,212, 238,214, 238,214, 238,215, 237,216, 237,217, 237,217, 239,221, 238,221, 240,221, 240,222, 240,222, 240,223,242,223, 244,222, 244,224, 244,225, 244,225, 245,225, 246,225, 246,225, 246,225"/>
                        <area shape="poly" title="Bolivia" alt="Bolivia" href="#" coords="246,225, 248,225, 249,225, 250,224, 250,224, 253,222, 256,222, 256,226, 258,228, 259,228, 261,229, 262,229, 263,230, 263,230, 264,231, 264,231, 266,231, 267,231, 267,231, 267,237, 267,237, 271,237, 271,237, 271,238, 271,239, 271,239, 272,239, 273,241, 272,244, 272,244, 272,244, 270,243,267,243, 267,243, 264,244, 263,246, 263,246, 263,247, 262,250, 259,250, 258,251, 258,250, 257,250, 255,250, 254,250, 254,249, 251,252, 250,252, 250,251, 248,246, 249,244, 249,244, 248,243, 248,242, 248,241, 246,240, 246,239, 246,239, 246,239, 246,238, 247,238, 247,237, 247,237, 247,236, 247,235, 247,234, 247,234, 247,233,248,232, 248,231, 248,229, 248,228, 246,225, 246,225"/>
                        <area shape="poly" title="Chile" alt="Chile" href="#" coords="249,317, 248,317, 247,317, 247,317, 247,317, 246,317, 246,317, 244,318, 244,318, 243,319, 243,319, 243,320, 243,320, 242,320, 241,320, 243,318, 243,318, 242,318, 241,318, 240,318, 239,319, 239,318, 239,318, 240,318, 240,318, 241,317, 240,317, 240,317, 240,317, 240,316, 239,316, 239,316,238,316, 238,316, 238,315, 237,316, 237,315, 237,315, 237,315, 237,314, 237,314, 237,314, 236,314, 237,314, 237,313, 237,313, 237,313, 237,313, 237,313, 236,313, 236,313, 236,312, 236,312, 236,311, 236,311, 237,310, 236,310, 236,310, 236,309, 236,308, 236,307, 236,307, 236,307, 237,307, 238,307, 238,307, 238,307, 237,307,236,307, 236,307, 235,307, 235,306, 235,306, 235,306, 235,306, 236,306, 236,306, 236,305, 236,305, 235,304, 234,304, 234,304, 234,304, 233,304, 233,304, 233,304, 234,303, 235,302, 236,302, 236,303, 236,303, 236,303, 235,303, 235,304, 236,304, 236,304, 236,303, 236,303, 236,303, 236,304, 237,303, 238,302, 238,302, 238,301,238,301, 238,301, 238,301, 238,301, 239,300, 240,299, 239,299, 238,299, 238,298, 239,298, 239,298, 239,297, 239,296, 240,296, 239,296, 239,295, 239,295, 240,294, 240,294, 240,294, 239,294, 239,294, 239,293, 239,293, 239,293, 239,293, 238,293, 238,293, 237,293, 237,291, 237,290, 238,288, 238,288, 238,286, 238,285, 237,284,237,283, 237,283, 238,283, 238,283, 241,276, 241,276, 242,275, 242,275, 241,274, 242,273, 241,268, 242,267, 242,266, 242,266, 242,265, 243,263, 243,262, 244,258, 244,257, 244,255, 244,255, 244,254, 244,253, 244,253, 244,253, 244,252, 244,252, 245,252, 244,241, 245,241, 246,241, 246,240, 246,240, 246,240, 248,241, 248,242,248,243, 249,244, 249,244, 248,246, 250,251, 250,252, 251,252, 252,252, 251,254, 249,255, 249,255, 249,256, 248,257, 249,258, 249,258, 249,259, 248,259, 249,260, 249,260, 249,261, 249,261, 248,261, 245,266, 246,268, 245,268, 245,268, 245,269, 244,271, 246,276, 246,277, 244,279, 244,280, 244,281, 243,282, 243,284, 243,287,242,287, 242,287, 241,291, 241,294, 241,294, 241,295, 241,295, 241,296, 241,296, 241,296, 241,298, 242,299, 242,299, 243,299, 243,300, 243,300, 243,300, 241,300, 242,301, 242,301, 241,302, 241,304, 241,306, 241,306, 240,306, 240,306, 240,307, 240,308, 240,309, 237,311, 237,311, 238,313, 238,314, 240,313, 240,313, 240,316,241,316, 249,317, 249,317, 249,317"/>
                        <area shape="poly" title="Chile" alt="Chile" href="#" coords="248,322, 247,323, 246,322, 244,323, 244,322, 244,322, 246,321, 247,322, 247,322, 247,321, 246,321, 245,321, 245,320, 247,319, 246,319, 246,319, 244,319, 244,319, 245,318, 247,317, 247,318, 248,318, 248,322, 248,322, 248,322"/>
                        <area shape="poly" title="Chile" alt="Chile" href="#" coords="238,318, 238,318, 238,317, 237,317, 237,317, 237,317, 238,317, 239,317, 239,317, 239,317, 238,318, 238,318, 238,318"/>
                        <area shape="poly" title="Chile" alt="Chile" href="#" coords="237,297, 236,297, 236,297, 236,297, 236,295, 236,295, 236,294, 237,294, 238,294, 238,294, 237,295, 237,295, 237,296, 237,297, 237,297, 237,297, 237,297"/>
                        <area shape="poly" title="Paraguay" alt="Paraguay" href="#" coords="279,258, 280,258, 279,259, 277,261, 276,261, 275,261, 272,261, 271,261, 270,261, 270,261, 271,260, 271,260, 271,259, 271,259, 273,257, 273,257, 266,254, 262,250, 262,250, 263,247, 263,246, 263,246, 264,244, 267,243, 267,243, 270,243, 272,244, 272,245, 272,246, 272,246, 272,250, 275,250,276,250, 276,250, 277,250, 278,254, 278,254, 280,253, 280,254, 280,254, 279,257, 279,258, 279,258, 279,258"/>
                        <area shape="poly" title="Uruguay" alt="Uruguay" href="#" coords="271,276, 271,276, 271,276, 271,275, 271,275, 272,274, 272,274, 272,272, 272,269, 273,268, 275,268, 275,268, 277,270, 277,270, 278,269, 278,270, 280,271, 281,272, 282,273, 282,273, 282,273, 282,274, 282,274, 282,275, 282,276, 281,277, 280,278, 279,278, 278,278, 276,278, 276,278, 275,278,273,277, 272,277, 272,277, 271,277, 271,276, 271,276, 271,276"/>
                        <area shape="poly" title="Argentina" alt="Argentina" href="#" coords="248,319, 248,319, 248,319, 249,319, 253,322, 254,322, 254,322, 256,322, 256,322, 256,322, 248,322, 248,319, 248,319, 248,319"/>
                        <area shape="poly" title="Argentina" alt="Argentina" href="#" coords="249,317, 241,316, 240,316, 240,313, 240,313, 238,314, 238,313, 237,311, 237,311, 240,309, 240,308, 240,307, 240,306, 240,306, 241,306, 241,305, 241,304, 241,302, 242,301, 242,301, 241,300, 243,300, 243,300, 243,300, 243,299, 242,299, 242,299, 241,298, 241,296, 241,296, 241,296, 241,295,241,295, 241,294, 241,294, 241,291, 242,287, 242,287, 243,287, 243,284, 243,282, 244,281, 244,280, 244,279, 246,277, 246,275, 244,271, 245,269, 245,268, 245,268, 246,268, 245,266, 248,261, 249,261, 249,261, 249,260, 249,260, 248,259, 249,259, 249,258, 249,258, 248,257, 249,256, 249,255, 249,255, 251,254, 252,252, 251,252,254,249, 254,250, 255,250, 257,250, 258,250, 258,251, 259,249, 262,250, 265,253, 273,257, 273,257, 271,259, 271,259, 271,260, 271,260, 270,261, 270,261, 271,261, 272,261, 274,261, 275,261, 276,261, 276,261, 277,261, 279,259, 280,258, 279,258, 281,257, 281,258, 282,259, 281,261, 272,269, 272,273, 272,274, 272,274, 271,275,271,275, 271,276, 271,276, 271,276, 271,277, 271,277, 272,278, 273,279, 274,279, 274,280, 273,280, 273,280, 273,281, 274,281, 274,282, 275,282, 275,283, 273,285, 273,285, 263,287, 262,287, 262,287, 262,288, 263,288, 262,290, 262,290, 262,291, 262,291, 262,291, 261,292, 259,292, 256,291, 256,291, 256,291, 256,292, 256,293,257,293, 256,294, 256,294, 257,295, 258,295, 258,295, 258,294, 259,294, 259,295, 259,295, 259,296, 258,296, 258,295, 258,295, 256,295, 256,296, 256,296, 258,296, 256,297, 256,297, 256,299, 256,299, 255,300, 255,300, 255,300, 253,301, 251,302, 251,302, 251,303, 251,303, 251,304, 253,305, 255,305, 255,306, 254,307, 254,307,251,309, 250,310, 250,310, 250,311, 250,311, 250,311, 248,312, 247,313, 247,314, 247,315, 249,317, 249,317, 249,317, 249,317"/>
                        <area shape="poly" title="Argentina" alt="Argentina" href="#" coords="248,319, 248,318, 249,318, 249,318, 248,319, 248,319, 248,319"/>
                        <area shape="poly" title="Brazil" alt="Brazil" href="#" coords="289,204, 289,204, 288,204, 288,203, 288,203, 289,201, 289,201, 291,201, 292,201, 293,201, 292,203, 292,204, 291,204, 289,204, 289,204, 289,204"/>
                        <area shape="poly" title="Brazil" alt="Brazil" href="#" coords="279,258, 279,256, 280,254, 280,254, 280,253, 278,254, 278,254, 277,250, 276,250, 275,250, 275,250, 272,250, 272,246, 272,246, 272,245, 272,244, 272,243, 273,241, 272,239, 271,239, 271,239, 271,238, 271,237, 271,237, 267,237, 267,237, 267,231, 267,231, 266,231, 264,231, 264,231, 263,230,263,230, 262,229, 259,228, 258,228, 258,228, 256,226, 256,226, 256,222, 253,222, 250,224, 250,224, 249,225, 248,225, 246,225, 245,225, 244,225, 244,225, 244,224, 244,222, 242,223, 240,223, 240,222, 240,222, 240,222, 238,221, 239,221, 237,217, 237,217, 237,216, 238,215, 238,214, 238,214, 239,212, 242,210, 243,211, 243,210,244,210, 244,210, 245,210, 246,210, 246,210, 246,210, 247,203, 247,203, 246,203, 246,202, 245,201, 245,199, 245,199, 246,199, 247,199, 247,199, 247,198, 247,198, 246,198, 246,198, 246,197, 250,197, 252,196, 252,196, 252,198, 253,198, 254,199, 254,199, 255,198, 255,199, 256,199, 257,198, 258,197, 258,197, 259,196, 259,196,260,196, 260,196, 260,195, 259,195, 259,195, 258,193, 257,191, 257,191, 257,191, 259,192, 260,192, 261,193, 261,193, 262,191, 262,191, 263,191, 266,190, 266,189, 266,189, 267,189, 267,189, 268,189, 268,189, 267,191, 268,191, 269,192, 268,194, 268,196, 268,196, 268,197, 270,198, 271,198, 274,196, 277,197, 276,196, 276,195,277,195, 278,195, 278,195, 279,195, 279,195, 281,196, 282,195, 283,196, 283,196, 284,196, 286,192, 286,191, 286,191, 287,191, 288,196, 289,196, 290,197, 290,197, 290,198, 289,198, 288,200, 286,201, 285,204, 285,204, 288,203, 288,205, 289,205, 291,205, 291,204, 291,205, 291,206, 293,204, 294,202, 294,202, 295,202, 296,202,296,202, 297,203, 297,203, 297,203, 299,203, 300,204, 300,204, 300,204, 300,204, 301,204, 301,204, 302,205, 302,206, 301,207, 302,207, 303,207, 305,206, 307,207, 311,207, 318,211, 322,212, 323,217, 322,222, 319,224, 319,225, 318,225, 318,225, 318,226, 315,229, 315,229, 315,229, 315,229, 315,229, 314,232, 314,236, 314,239,314,240, 313,240, 313,241, 312,242, 313,244, 312,244, 310,248, 310,249, 310,250, 308,250, 308,251, 308,251, 302,252, 302,252, 300,253, 300,253, 300,253, 299,253, 294,256, 294,257, 293,257, 293,257, 293,257, 293,257, 293,258, 293,259, 292,259, 292,259, 293,260, 293,263, 292,264, 291,265, 286,271, 287,270, 288,269, 288,268,288,268, 288,268, 288,268, 287,268, 287,268, 287,268, 286,269, 286,270, 285,271, 284,273, 282,276, 282,275, 282,274, 282,274, 283,273, 282,273, 282,273, 281,272, 280,271, 278,270, 278,269, 277,270, 277,270, 275,268, 275,268, 273,268, 281,261, 282,259, 281,258, 281,257, 280,258, 279,258, 279,258"/>
                        <!-- europe -->
                        <area shape="poly" title="Greenland" alt="Greenland" href="#" coords="341,41, 343,42, 344,42, 339,43, 338,43, 338,43, 338,44, 352,45, 347,46, 345,46, 341,48, 335,49, 335,48, 334,48, 333,49, 332,49, 329,48, 329,48, 329,48, 330,49, 329,50, 328,50, 324,53, 322,53, 321,53, 321,54, 321,54, 320,54, 318,54, 317,55, 318,53, 316,53, 317,53, 316,54, 316,54, 315,55,312,55, 312,55, 313,56, 313,56, 312,56, 310,56, 311,57, 311,57, 311,57, 311,57, 309,58, 311,59, 310,59, 310,59, 309,59, 309,59, 309,60, 309,60, 309,60, 308,60, 308,60, 308,61, 308,61, 305,61, 307,62, 307,63, 306,64, 306,64, 305,64, 306,65, 306,65, 306,66, 306,66, 304,66, 305,66, 305,67, 305,67, 305,68, 303,67, 302,67, 302,68,301,67, 301,66, 301,66, 300,66, 299,66, 299,66, 300,65, 300,65, 299,65, 298,65, 298,65, 298,65, 296,65, 295,66, 295,65, 294,65, 294,65, 292,64, 292,63, 292,62, 291,62, 291,62, 291,62, 290,62, 289,62, 289,62, 289,61, 290,61, 290,61, 289,60, 288,60, 288,59, 288,59, 287,59, 287,59, 287,59, 287,58, 290,57, 290,57, 289,57, 287,57,286,58, 285,57, 285,56, 285,56, 285,55, 285,55, 284,55, 284,55, 283,54, 283,54, 284,53, 284,53, 283,53, 282,53, 282,53, 283,53, 284,52, 285,52, 282,52, 282,52, 282,51, 282,51, 282,51, 283,51, 283,51, 283,51, 282,51, 282,51, 282,50, 282,50, 283,50, 289,50, 289,50, 289,50, 289,49, 283,48, 284,48, 287,48, 288,48, 288,47, 289,47,289,47, 289,47, 289,47, 288,46, 288,46, 289,45, 288,45, 282,44, 280,43, 280,43, 288,44, 288,44, 288,44, 287,43, 287,43, 286,42, 285,42, 285,42, 286,41, 286,41, 282,41, 281,42, 280,42, 278,42, 277,41, 277,41, 278,41, 278,40, 278,39, 278,39, 279,39, 279,39, 280,39, 278,38, 278,37, 274,34, 272,33, 271,33, 271,32, 253,31, 253,32,253,32, 247,31, 249,30, 249,30, 242,29, 243,29, 254,28, 254,28, 252,28, 244,28, 243,27, 242,27, 240,27, 240,26, 240,26, 257,24, 257,23, 257,23, 251,22, 252,21, 265,20, 265,20, 265,20, 265,19, 265,19, 273,19, 274,19, 272,19, 272,18, 281,18, 282,19, 282,19, 283,19, 283,18, 290,19, 290,19, 288,17, 288,17, 301,19, 302,19, 303,17,300,16, 315,16, 315,16, 315,15, 351,17, 351,17, 351,17, 328,18, 328,19, 329,19, 352,18, 352,19, 352,19, 347,21, 347,21, 348,21, 373,19, 373,19, 373,20, 356,22, 356,23, 356,23, 361,23, 361,23, 358,24, 357,24, 357,24, 357,25, 354,25, 353,27, 352,28, 353,28, 353,28, 355,27, 358,27, 358,28, 358,28, 356,28, 356,28, 356,28, 360,29,361,29, 361,29, 361,30, 361,30, 355,30, 354,30, 351,30, 351,30, 357,31, 358,32, 358,32, 358,33, 358,33, 357,33, 355,33, 355,33, 355,34, 355,34, 358,35, 359,35, 358,35, 352,35, 352,36, 356,36, 356,37, 356,37, 353,37, 353,38, 350,37, 347,37, 340,38, 340,38, 340,38, 340,39, 350,41, 350,41, 351,42, 352,42, 353,42, 353,42, 353,44,353,44, 353,44, 348,44, 347,43, 347,43, 344,41, 341,41, 341,41, 341,41"/>
                        <area shape="poly" title="Greenland" alt="Greenland" href="#" coords="282,46, 280,46, 279,46, 279,45, 279,44, 286,46, 286,46, 282,46, 282,46"/>

                        <area shape="poly" title="Greenland" alt="Greenland" href="#" coords="352,40, 348,39, 348,39, 352,40, 352,40, 352,40, 352,40, 352,40, 352,40"/>
                        <area shape="poly" title="Russia" alt="Russia" href="#" coords="445,80, 446,79, 445,79, 445,79, 445,79, 445,79, 445,79, 445,79, 448,79, 448,78, 448,78, 448,78, 448,78, 448,78, 448,78, 448,78, 450,78, 450,78, 451,78, 451,78, 451,78, 451,79, 452,79, 451,79, 451,79, 451,79, 451,80, 445,80, 445,80, 445,80"/>
                        <area shape="poly" title="Cyprus" alt="Cyprus" href="#" coords="476,122, 476,122, 476,123, 476,123, 476,123, 476,123, 475,123, 475,123, 474,123, 474,124, 474,124, 474,124, 473,124, 473,123, 473,123, 472,123, 472,123, 472,123, 472,123, 473,123, 473,122, 473,122, 474,122, 474,122, 474,122, 474,122, 475,122, 477,121, 476,122, 476,122, 476,122"/>
                        <area shape="poly" title="Montenegro" alt="Montenegro" href="#" coords="444,107, 443,107, 443,107, 443,107, 442,106, 442,106, 442,106, 442,106, 442,106, 442,106, 442,106, 442,106, 442,105, 442,105, 442,105, 442,105, 442,105, 442,105, 442,105, 442,104, 442,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104,443,104, 443,104, 443,104, 444,104, 444,104, 444,104, 444,105, 445,105, 445,105, 445,105, 445,105, 445,105, 445,105, 446,105, 446,105, 446,105, 446,105, 446,105, 446,105, 446,105, 446,105, 446,105, 445,105, 445,105, 445,106, 445,106, 445,106, 445,106, 445,106, 445,106, 444,106, 444,106, 444,107, 444,107, 444,107, 444,107"/>
                        <area shape="poly" title="Albania" alt="Albania" href="#" coords="446,113, 445,112, 444,111, 444,111, 444,111, 444,111, 444,110, 444,110, 444,109, 444,109, 444,109, 444,108, 444,108, 444,108, 444,107, 444,107, 444,107, 444,106, 444,106, 445,106, 447,107, 447,108, 446,108, 447,109, 447,109, 448,110, 447,110, 446,113, 446,113, 446,113"/>
                        <area shape="poly" title="Macedonia" alt="Macedonia" href="#" coords="452,109, 451,109, 450,109, 449,109, 449,110, 448,110, 447,109, 447,109, 446,108, 447,108, 447,107, 447,107, 447,107, 447,107, 447,107, 447,107, 447,107, 447,107, 448,107, 448,107, 448,107, 448,107, 448,107, 448,107, 448,107, 448,107, 449,107, 449,107, 449,106, 450,106, 450,106, 450,106,450,106, 450,106, 450,106, 451,107, 451,107, 452,107, 452,108, 452,108, 452,109, 452,109, 452,109, 452,109"/>
                        <area shape="poly" title="Greece" alt="Greece" href="#" coords="446,113, 447,110, 449,110, 449,109, 450,109, 451,109, 455,108, 457,109, 459,109, 459,108, 459,108, 459,108, 459,108, 460,108, 460,108, 459,110, 459,110, 456,109, 456,109, 456,110, 455,110, 455,109, 455,109, 455,110, 454,110, 454,110, 454,110, 453,110, 453,110, 453,110, 454,110, 454,111,455,111, 455,111, 454,111, 454,111, 454,111, 454,111, 453,111, 453,111, 453,111, 454,111, 454,111, 454,111, 454,112, 454,112, 453,111, 453,111, 453,111, 452,111, 453,111, 453,112, 453,112, 453,112, 452,112, 453,111, 452,111, 452,111, 452,111, 451,110, 452,110, 452,110, 452,110, 451,110, 451,110, 451,110, 451,111, 451,111,451,111, 451,111, 451,112, 451,112, 451,112, 451,112, 452,113, 452,113, 452,113, 452,114, 452,113, 452,113, 452,113, 451,113, 451,113, 452,113, 452,114, 451,114, 451,114, 451,114, 451,114, 452,114, 452,115, 452,115, 452,115, 453,115, 453,115, 453,115, 454,115, 454,116, 454,116, 454,116, 454,117, 454,117, 454,117, 454,116,453,116, 453,116, 452,116, 452,116, 452,116, 452,117, 452,117, 453,117, 453,117, 453,117, 452,117, 452,117, 452,117, 452,117, 452,117, 452,117, 451,117, 451,117, 451,117, 451,118, 451,118, 451,118, 452,118, 452,119, 451,119, 451,119, 451,119, 450,119, 450,118, 449,119, 449,118, 449,118, 449,118, 449,117, 448,116, 448,116,448,116, 449,116, 449,115, 451,116, 452,116, 452,116, 450,115, 448,115, 447,114, 447,114, 447,114, 448,114, 448,114, 447,114, 447,114, 447,114, 446,113, 445,113, 446,113, 446,113"/>
                        <area shape="poly" title="Greece" alt="Greece" href="#" coords="456,123, 456,123, 456,122, 453,122, 453,122, 453,121, 453,121, 453,121, 453,121, 454,121, 454,121, 454,121, 454,121, 454,121, 454,121, 454,121, 454,122, 454,122, 454,122, 455,122, 455,122, 456,122, 456,122, 457,122, 458,122, 458,122, 458,122, 458,122, 458,122, 459,122, 459,122, 459,122,459,122, 459,122, 459,123, 456,123, 456,123, 456,123"/>
                        <area shape="poly" title="Bulgaria" alt="Bulgaria" href="#" coords="450,106, 450,106, 451,105, 451,105, 452,105, 452,105, 450,103, 450,103, 451,102, 452,102, 452,103, 452,103, 453,103, 454,103, 456,103, 457,103, 460,102, 464,103, 464,104, 464,104, 463,104, 463,104, 463,105, 463,105, 463,105, 462,106, 462,106, 462,106, 462,106, 462,106, 462,107, 463,107,463,107, 460,107, 459,108, 459,108, 459,108, 459,108, 459,109, 457,109, 455,108, 452,109, 452,109, 452,108, 452,108, 452,107, 451,107, 451,107, 450,106, 450,106, 450,106, 450,106"/>
                        <area shape="poly" title="Bosnia Herzegovina"" alt="Bosnia Herzegovina"" href="#" coords="440,105, 440,105, 440,105, 440,105, 439,104, 439,104, 438,103, 438,103, 438,103, 437,103, 437,102, 437,102, 437,102, 437,102, 437,102, 437,102, 436,101, 436,101, 436,101, 436,101, 436,101, 436,100, 436,100, 436,100, 436,100, 437,100, 437,100, 437,100, 437,100, 438,100, 438,100, 439,100,439,100, 439,100, 439,100, 439,100, 440,100, 440,100, 441,100, 441,100, 441,100, 441,100, 442,100, 442,100, 442,100, 442,100, 442,100, 442,100, 442,100, 443,100, 443,101, 443,101, 443,101, 443,101, 443,101, 444,101, 444,101, 444,101, 444,101, 443,102, 443,102, 443,102, 443,102, 443,102, 443,102, 444,102, 444,102, 444,102,444,103, 444,103, 444,103, 444,103, 444,103, 444,103, 444,103, 444,104, 444,104, 444,104, 444,104, 444,104, 444,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 443,104, 442,104, 442,104, 442,105, 442,105, 442,105, 442,105, 442,105, 442,105, 442,106,442,106, 442,106, 442,106, 441,106, 441,106, 441,105, 441,105, 441,105, 440,105, 440,105, 440,105, 440,105, 440,105"/>
                        <area shape="poly" title="Serbia" alt="Serbia" href="#" coords="451,102, 450,103, 450,103, 452,105, 452,105, 451,105, 451,105, 450,106, 450,106, 450,106, 450,106, 450,106, 450,106, 449,106, 449,107, 449,107, 448,107, 448,107, 448,107, 448,107, 448,107, 448,107, 448,107, 448,107, 447,107, 447,107, 447,107, 447,107, 447,107, 447,107, 447,107, 447,107,447,107, 445,106, 445,106, 445,106, 445,106, 445,106, 445,106, 445,106, 445,105, 445,105, 446,105, 446,105, 446,105, 446,105, 446,105, 446,105, 446,105, 446,105, 446,105, 445,105, 445,105, 445,105, 445,105, 445,105, 445,105, 444,104, 444,104, 444,104, 444,104, 444,104, 444,104, 444,104, 444,104, 444,104, 444,103, 444,103,444,103, 444,103, 444,103, 444,103, 444,103, 444,102, 444,102, 444,102, 443,102, 443,102, 443,102, 443,102, 443,102, 443,102, 444,101, 444,101, 444,101, 444,101, 443,101, 443,101, 443,101, 443,101, 443,101, 443,101, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 444,100, 444,100, 444,100,444,100, 444,100, 444,100, 444,100, 444,100, 444,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,98, 445,98, 446,98, 446,98, 447,98, 447,99, 447,99, 447,99, 447,99, 447,99, 448,100, 448,100,449,100, 448,100, 448,100, 449,101, 448,101, 448,101, 449,101, 449,101, 449,101, 450,102, 450,101, 451,101, 451,101, 451,101, 451,101, 451,102, 451,102, 451,102, 451,102, 451,102"/>
                        <area shape="poly" title="Croatia" alt="Croatia" href="#" coords="440,105, 439,104, 439,104, 438,104, 438,104, 437,104, 436,104, 436,103, 435,103, 434,102, 435,102, 434,101, 434,101, 434,101, 434,100, 433,100, 432,100, 432,100, 432,101, 432,101, 432,101, 432,101, 431,100, 431,99, 431,99, 431,99, 432,99, 432,99, 432,99, 432,99, 432,99, 432,99, 432,99,432,99, 432,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 434,99, 434,99, 434,99, 434,99, 434,99, 434,99, 435,100, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 436,99, 436,98, 436,98, 436,98, 435,98, 435,98, 436,98, 436,98, 436,98,436,98, 437,97, 437,97, 437,97, 437,97, 437,97, 437,97, 437,97, 437,97, 440,99, 443,98, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,99, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 444,100, 444,100, 444,100, 444,100, 444,100, 444,100, 444,100,444,100, 444,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,100, 443,101, 443,101, 443,101, 443,101, 443,101, 443,101, 443,101, 443,101, 443,100, 442,100, 442,100, 442,100, 442,100, 442,100, 442,100, 442,100, 441,100, 441,100, 441,100, 441,100, 440,100, 440,100, 439,100, 439,100, 439,100,439,100, 439,100, 438,100, 438,100, 437,100, 437,100, 437,100, 437,100, 436,100, 436,100, 436,100, 436,100, 436,101, 436,101, 436,101, 436,101, 436,101, 437,102, 437,102, 437,102, 437,102, 437,102, 437,102, 437,103, 438,103, 438,103, 438,103, 439,104, 439,104, 440,105, 440,105, 440,105, 440,105"/>
                        <area shape="poly" title="Croatia" alt="Croatia" href="#" coords="440,105, 440,105, 440,105, 441,105, 441,105, 441,105, 441,106, 441,106, 442,106, 442,106, 442,106, 442,106, 442,106, 442,106, 442,106, 442,106, 440,105, 440,105, 440,105"/>
                        <area shape="poly" title="Estonia" alt="Estonia" href="#" coords="463,68, 463,68, 463,69, 463,69, 463,69, 463,69, 462,69, 462,69, 462,70, 462,70, 462,71, 462,71, 462,71, 462,71, 462,71, 462,71, 462,72, 462,72, 463,72, 462,72, 462,72, 462,72, 462,72, 461,73, 462,73, 461,73, 461,72, 460,73, 460,72, 460,73, 460,73, 460,73, 459,73, 459,72, 459,72, 459,72,459,72, 458,72, 458,72, 458,72, 458,72, 457,72, 457,72, 457,71, 457,72, 457,72, 457,72, 457,71, 457,71, 455,72, 455,72, 455,72, 455,72, 455,71, 455,71, 455,71, 454,71, 454,71, 453,71, 453,70, 453,70, 453,70, 453,70, 453,69, 453,69, 453,69, 454,69, 454,69, 455,69, 455,69, 455,69, 455,69, 456,69, 456,68, 457,69, 457,68, 457,68,463,69, 463,68, 463,68, 463,68"/>
                        <area shape="poly" title="Latvia" alt="Latvia" href="#" coords="462,73, 462,73, 462,73, 462,73, 462,73, 463,73, 463,73, 462,73, 462,74, 462,74, 462,74, 462,74, 462,74, 462,74, 463,74, 463,74, 463,74, 463,74, 463,74, 463,74, 463,75, 463,75, 463,75, 463,75, 463,75, 463,75, 463,75, 463,75, 463,76, 463,76, 463,76, 463,76, 463,76, 462,76, 462,76, 462,76,462,76, 461,76, 461,76, 460,77, 460,77, 460,77, 460,77, 459,77, 459,76, 458,76, 457,76, 456,75, 456,75, 456,75, 455,75, 454,75, 454,75, 452,75, 452,75, 452,75, 452,75, 450,75, 448,76, 448,76, 447,76, 447,76, 447,75, 448,74, 448,74, 448,73, 449,73, 451,72, 451,73, 452,73, 452,74, 453,74, 454,74, 455,74, 455,73, 455,72, 455,72,455,72, 455,72, 457,71, 457,71, 457,72, 457,72, 457,71, 457,71, 457,72, 457,72, 458,72, 458,72, 458,72, 458,72, 459,72, 459,72, 459,72, 459,72, 459,72, 460,73, 460,73, 460,73, 460,72, 460,72, 461,72, 461,73, 462,73, 462,73, 462,73"/>
                        <area shape="poly" title="Lithuania" alt="Lithuania" href="#" coords="451,80, 451,79, 451,79, 451,79, 452,79, 451,79, 451,78, 451,78, 451,78, 450,78, 450,78, 449,78, 449,78, 448,78, 448,78, 448,78, 448,78, 448,78, 448,78, 448,77, 447,77, 447,76, 447,76, 448,76, 448,76, 450,75, 452,75, 452,75, 452,75, 452,75, 454,75, 454,75, 455,75, 456,75, 456,75, 456,75,457,76, 458,76, 459,76, 459,77, 460,77, 460,77, 460,77, 460,78, 460,78, 460,78, 460,78, 460,78, 460,78, 460,78, 460,78, 460,78, 459,78, 459,78, 459,78, 458,78, 458,79, 458,79, 458,79, 458,79, 458,79, 458,79, 458,80, 458,80, 458,80, 458,80, 458,80, 457,80, 457,80, 457,80, 457,80, 457,80, 457,80, 457,80, 457,80, 456,80, 456,80,456,80, 456,80, 456,81, 456,81, 456,81, 455,81, 455,81, 455,81, 454,81, 453,81, 453,81, 453,80, 452,79, 451,80, 451,80, 451,80"/>
                        <area shape="poly" title="Romania" alt="Romania" href="#" coords="446,98, 446,98, 446,98, 447,98, 448,97, 450,95, 450,94, 452,94, 452,94, 453,94, 453,94, 456,94, 456,94, 456,94, 456,94, 456,94, 457,94, 457,94, 459,94, 459,94, 459,93, 460,93, 461,93, 463,96, 463,96, 464,97, 463,98, 463,99, 463,99, 463,99, 463,99, 464,99, 464,100, 464,100, 465,100, 465,100,465,100, 466,99, 467,100, 467,100, 467,100, 466,101, 465,101, 465,101, 465,102, 464,103, 460,102, 457,103, 455,103, 454,103, 453,103, 452,103, 452,103, 452,102, 451,102, 451,102, 451,101, 451,101, 451,101, 451,101, 450,101, 450,102, 449,101, 449,101, 449,101, 448,101, 448,101, 449,101, 448,100, 449,100, 448,100, 448,100,447,99, 447,99, 447,99, 447,99, 447,99, 447,98, 446,98, 446,98, 446,98, 446,98"/>

                        <area shape="poly" title="Moldova" alt="Moldova" href="#" coords="460,93, 460,93, 460,93, 460,93, 460,93, 460,93, 461,93, 461,93, 461,93, 461,93, 462,93, 462,93, 462,93, 462,93, 463,93, 463,93, 463,93, 463,93, 463,93, 463,93, 463,93, 464,93, 464,93, 464,93, 464,93, 464,93, 464,94, 464,94, 464,93, 465,94, 465,94, 465,94, 465,94, 465,94, 465,94, 466,94,466,94, 466,94, 466,94, 466,94, 466,94, 466,95, 465,95, 465,95, 466,95, 466,95, 466,95, 466,95, 466,95, 466,95, 466,96, 466,96, 466,96, 466,96, 466,96, 467,96, 466,96, 467,96, 467,96, 467,96, 467,96, 467,97, 467,97, 467,97, 467,97, 467,97, 468,97, 468,97, 468,97, 467,97, 467,97, 467,97, 467,97, 467,97, 467,97, 467,97, 466,97,466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 465,97, 465,97, 465,97, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,99, 464,99, 464,99, 464,99, 464,99, 464,99, 464,99, 464,99, 464,99, 464,99, 464,99, 463,99, 463,99, 463,99, 463,99,463,99, 463,98, 464,97, 463,96, 463,96, 461,93, 460,93, 460,93, 460,93, 460,93"/>
                        <area shape="poly" title="Ukraine" alt="Ukraine" href="#" coords="453,86, 453,86, 454,86, 454,86, 455,85, 455,85, 455,85, 459,85, 459,85, 459,85, 460,85, 461,85, 461,85, 461,85, 461,86, 461,86, 461,86, 462,86, 462,86, 462,86, 462,86, 463,86, 463,86, 463,86, 463,86, 463,86, 463,86, 464,86, 464,86, 464,86, 464,86, 464,86, 464,86, 465,86, 465,86, 465,86,465,86, 465,86, 466,86, 466,86, 466,86, 467,86, 467,86, 468,86, 468,86, 468,86, 468,86, 469,86, 469,86, 469,86, 468,86, 469,86, 469,85, 469,85, 470,85, 470,85, 470,85, 472,85, 473,85, 473,84, 473,84, 474,84, 475,84, 475,84, 476,84, 476,84, 476,84, 477,85, 477,85, 477,86, 477,86, 477,86, 477,86, 477,87, 478,87, 478,87, 479,87,479,87, 479,87, 479,87, 479,88, 480,88, 479,88, 480,88, 480,88, 480,89, 480,88, 481,88, 481,88, 481,89, 482,89, 482,89, 482,89, 484,88, 484,89, 484,89, 484,89, 485,89, 485,89, 485,89, 485,89, 485,90, 486,89, 486,89, 486,89, 486,89, 487,90, 488,90, 488,90, 489,90, 489,90, 489,90, 489,90, 490,90, 490,90, 490,90, 490,91, 490,91,490,91, 490,91, 489,91, 489,91, 489,92, 490,92, 490,92, 489,92, 489,92, 489,93, 489,93, 489,93, 489,94, 489,94, 489,94, 487,94, 487,94, 487,94, 486,95, 486,96, 484,96, 483,96, 480,97, 480,97, 479,97, 479,97, 479,97, 478,98, 476,98, 479,99, 479,99, 479,99, 479,100, 479,100, 480,99, 482,100, 482,100, 482,100, 481,100, 480,100,480,100, 479,100, 479,101, 478,101, 477,101, 477,101, 476,102, 475,101, 475,101, 475,100, 475,100, 474,100, 473,100, 473,99, 476,98, 475,98, 472,98, 472,98, 472,97, 472,97, 472,96, 471,97, 469,97, 468,99, 467,99, 466,99, 466,99, 467,100, 466,99, 465,100, 465,100, 465,100, 464,100, 464,100, 464,99, 463,99, 464,99, 464,99,464,99, 464,99, 464,99, 464,99, 464,99, 464,99, 464,99, 464,99, 465,99, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,98, 465,97, 465,97, 465,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 466,97, 467,97, 467,97, 467,97, 467,97, 467,97,467,97, 467,97, 468,97, 468,97, 468,97, 467,97, 467,97, 467,97, 467,97, 467,97, 467,96, 467,96, 467,96, 467,96, 466,96, 467,96, 466,96, 466,96, 466,96, 466,96, 466,96, 466,95, 466,95, 466,95, 466,95, 466,95, 466,95, 466,95, 465,95, 466,95, 466,94, 466,94, 466,94, 466,94, 466,94, 466,94, 465,94, 465,94, 465,94, 465,94, 465,94,465,94, 464,93, 464,94, 464,94, 464,93, 464,93, 464,93, 464,93, 464,93, 463,93, 463,93, 463,93, 463,93, 463,93, 463,93, 462,93, 462,93, 462,93, 462,93, 461,93, 461,93, 461,93, 461,93, 460,93, 460,93, 460,93, 460,93, 460,93, 460,93, 459,93, 459,94, 459,94, 457,94, 457,94, 456,94, 456,94, 456,94, 456,94, 453,94, 453,94, 452,94,452,94, 450,93, 450,93, 451,91, 452,91, 451,91, 451,90, 451,90, 454,88, 454,88, 454,88, 454,88, 454,88, 454,87, 454,87, 453,86, 453,86, 453,86, 453,86, 453,86"/>
                        <area shape="poly" title="Belarus" alt="Belarus" href="#" coords="453,81, 453,81, 454,81, 455,81, 455,81, 455,81, 456,81, 456,81, 456,81, 456,80, 456,80, 456,80, 456,80, 457,80, 457,80, 457,80, 457,80, 457,80, 457,80, 457,80, 457,80, 458,80, 458,80, 458,80, 458,80, 458,80, 458,80, 458,79, 458,79, 458,79, 458,79, 458,79, 458,78, 459,78, 459,78, 459,78,460,78, 460,78, 460,78, 460,78, 460,78, 460,78, 460,78, 460,78, 460,78, 460,77, 460,77, 460,77, 460,77, 460,77, 460,77, 461,76, 461,76, 462,76, 462,76, 462,76, 462,76, 462,76, 463,76, 463,76, 463,76, 463,76, 464,76, 464,76, 464,76, 465,76, 465,76, 466,76, 466,76, 466,76, 466,76, 466,77, 466,77, 466,77, 467,77, 467,77, 467,76,468,76, 468,76, 468,76, 469,77, 469,77, 469,77, 469,77, 469,77, 469,77, 469,77, 469,77, 469,78, 469,78, 470,78, 470,78, 470,78, 469,78, 469,78, 469,78, 469,79, 469,79, 470,79, 470,79, 470,79, 470,79, 470,79, 470,79, 470,80, 471,80, 472,81, 471,81, 472,81, 472,81, 473,81, 473,81, 473,81, 473,81, 473,81, 473,82, 473,82, 473,82,473,82, 473,82, 473,82, 472,82, 471,82, 471,82, 471,82, 470,83, 470,83, 471,83, 471,83, 471,83, 471,84, 471,84, 471,84, 471,84, 471,84, 471,84, 471,85, 471,85, 470,85, 470,85, 470,85, 469,85, 469,85, 469,86, 468,86, 469,86, 469,86, 469,86, 468,86, 468,86, 468,86, 468,86, 467,86, 467,86, 466,86, 466,86, 466,86, 465,86, 465,86,465,86, 465,86, 465,86, 464,86, 464,86, 464,86, 464,86, 464,86, 464,86, 463,86, 463,86, 463,86, 463,86, 463,86, 463,86, 462,86, 462,86, 462,86, 462,86, 461,86, 461,86, 461,86, 461,85, 461,85, 461,85, 460,85, 459,85, 459,85, 459,85, 455,85, 455,85, 455,85, 454,86, 454,86, 453,86, 453,86, 453,86, 453,85, 453,85, 453,85, 453,84,453,84, 452,84, 452,84, 453,84, 453,83, 454,83, 454,83, 454,83, 453,81, 453,81, 453,81"/>
                        <area shape="poly" title="Hungary" alt="Hungary" href="#" coords="443,98, 440,99, 437,97, 437,96, 437,96, 437,96, 437,95, 437,95, 438,95, 438,95, 438,95, 437,95, 437,94, 438,94, 438,95, 439,94, 439,94, 440,94, 441,94, 443,94, 443,93, 445,93, 447,93, 448,92, 449,93, 450,93, 452,94, 452,94, 450,94, 450,95, 448,97, 447,98, 446,98, 446,98, 445,98, 443,98,443,98, 443,98"/>
                        <area shape="poly" title="Slovakia" alt="Slovakia" href="#" coords="438,92, 439,92, 439,92, 439,92, 439,92, 439,92, 439,92, 439,92, 440,92, 440,92, 440,92, 440,92, 441,92, 441,91, 441,91, 441,91, 441,91, 441,91, 441,91, 441,91, 442,91, 442,90, 442,90, 442,90, 443,90, 443,90, 443,91, 443,91, 444,90, 444,90, 444,90, 444,90, 444,91, 445,91, 445,91, 445,91,445,91, 445,91, 445,91, 445,91, 446,91, 446,91, 446,91, 447,91, 447,91, 448,91, 448,90, 449,90, 449,91, 450,91, 450,91, 451,91, 450,93, 449,93, 448,92, 447,93, 445,93, 443,93, 443,94, 441,94, 440,94, 439,94, 439,93, 438,93, 438,92, 438,92, 438,92, 438,92"/>
                        <area shape="poly" title="Slovenia" alt="Slovenia" href="#" coords="431,97, 432,97, 432,97, 433,97, 433,97, 434,97, 434,97, 435,97, 435,97, 436,97, 436,96, 437,96, 437,97, 438,97, 438,97, 437,97, 437,97, 437,97, 437,97, 437,97, 437,97, 437,97, 437,97, 436,98, 436,98, 436,98, 436,98, 435,98, 435,98, 436,98, 436,98, 436,98, 436,99, 435,99, 435,99, 435,99,435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 435,99, 434,99, 434,99, 434,99, 434,99, 434,99, 434,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 433,99, 432,99, 432,99, 432,99, 432,99, 432,99, 432,99, 432,99, 432,99, 432,99, 431,99, 431,99, 431,99, 431,99, 432,99,431,99, 431,99, 431,98, 431,98, 431,98, 431,98, 431,98, 430,98, 431,97, 431,97, 431,97, 431,97"/>
                        <area shape="poly" title="Czech Republic"" alt="Czech Republic"" href="#" coords="432,92, 431,92, 431,92, 431,91, 431,92, 431,91, 430,91, 430,91, 429,91, 429,90, 429,90, 428,90, 429,90, 429,89, 428,89, 428,89, 428,88, 429,88, 429,88, 430,88, 430,88, 430,88, 430,88, 431,88, 431,88, 431,88, 432,88, 432,87, 433,87, 432,87, 433,87, 433,87, 433,87, 434,87, 434,87, 434,87,434,87, 434,87, 434,87, 435,87, 435,87, 435,87, 435,87, 436,88, 436,88, 436,88, 436,88, 437,88, 437,88, 437,88, 437,88, 437,88, 437,88, 437,88, 437,89, 438,89, 438,89, 438,89, 439,89, 438,88, 438,88, 439,88, 439,89, 440,89, 440,89, 440,89, 440,89, 441,89, 441,89, 441,89, 442,89, 442,90, 442,90, 443,90, 443,90, 443,90, 442,90,442,90, 442,90, 442,90, 442,91, 441,91, 441,91, 441,91, 441,91, 441,91, 441,91, 441,92, 440,92, 440,92, 440,92, 440,92, 440,92, 439,92, 439,92, 439,92, 439,92, 439,92, 439,92, 439,92, 438,92, 438,92, 438,92, 436,92, 434,92, 433,92, 433,93, 432,92, 432,92, 432,92"/>
                        <area shape="poly" title="Switzerland" alt="Switzerland" href="#" coords="422,95, 422,95, 422,95, 422,96, 422,96, 423,96, 423,96, 423,96, 424,96, 424,96, 424,97, 424,97, 424,97, 424,97, 423,97, 423,97, 423,97, 423,97, 423,98, 423,97, 422,97, 422,98, 422,98, 422,97, 422,97, 421,97, 421,97, 421,98, 421,98, 421,99, 420,98, 420,98, 420,98, 419,98, 419,97, 419,98,419,98, 418,98, 418,98, 417,98, 417,98, 416,98, 416,97, 415,97, 414,98, 414,98, 414,98, 414,97, 414,97, 414,97, 415,96, 415,96, 416,96, 416,96, 416,95, 416,95, 416,95, 416,95, 416,95, 417,95, 417,95, 417,95, 417,95, 418,95, 418,95, 418,95, 419,95, 419,95, 419,95, 419,94, 420,94, 420,94, 420,94, 420,94, 421,94, 422,95, 422,95,422,95"/>
                        <area shape="poly" title="Austria" alt="Austria" href="#" coords="422,95, 422,95, 422,95, 423,95, 423,95, 423,95, 423,95, 423,95, 423,95, 423,95, 423,95, 424,95, 424,95, 424,95, 424,95, 425,95, 425,95, 426,95, 426,95, 426,95, 426,95, 428,95, 428,94, 428,94, 428,95, 429,94, 429,95, 429,95, 430,95, 430,95, 430,95, 430,94, 429,94, 430,94, 429,93, 430,93,430,93, 431,92, 431,92, 431,93, 432,92, 433,93, 433,92, 434,92, 436,92, 438,92, 438,92, 438,92, 438,93, 439,93, 439,94, 439,94, 439,94, 438,95, 438,94, 437,94, 437,95, 438,95, 438,95, 438,95, 437,95, 437,95, 437,96, 437,96, 437,96, 436,96, 436,97, 435,97, 435,97, 434,97, 434,97, 433,97, 433,97, 432,97, 432,97, 428,97, 428,96,428,96, 428,96, 427,96, 426,96, 426,96, 426,96, 425,96, 425,96, 424,96, 424,96, 424,96, 423,96, 423,96, 423,96, 422,96, 422,96, 422,95, 422,95, 422,95, 422,95"/>
                        <area shape="poly" title="Italy" alt="Italy" href="#" coords="431,99, 431,99, 431,99, 431,99, 430,99, 430,99, 430,99, 428,100, 429,99, 428,100, 428,100, 428,100, 428,100, 428,100, 428,100, 429,101, 429,101, 428,101, 428,102, 429,103, 431,104, 432,106, 433,107, 434,107, 434,107, 435,107, 436,107, 437,107, 437,108, 436,108, 436,108, 436,108, 441,110,441,110, 442,111, 442,111, 442,112, 441,112, 441,111, 440,111, 439,111, 439,111, 439,110, 439,110, 438,110, 438,111, 438,111, 438,112, 438,112, 438,112, 439,113, 439,113, 439,114, 439,114, 439,114, 438,114, 438,114, 438,114, 438,115, 437,115, 436,116, 436,116, 436,116, 436,115, 436,115, 436,115, 436,115, 436,114, 437,114,437,114, 437,114, 437,114, 436,111, 435,111, 435,112, 434,111, 434,111, 434,111, 434,110, 434,110, 433,110, 433,110, 433,110, 433,110, 432,110, 432,110, 431,109, 430,109, 425,105, 423,102, 421,102, 420,102, 419,103, 418,103, 418,102, 418,102, 417,102, 416,102, 416,101, 416,101, 416,101, 416,101, 416,101, 415,100, 416,100,416,100, 416,100, 417,99, 416,99, 416,99, 416,99, 416,98, 417,98, 417,98, 418,98, 418,98, 419,98, 419,98, 419,97, 419,98, 420,98, 420,98, 420,98, 421,99, 421,98, 421,98, 421,97, 421,97, 422,97, 422,97, 422,98, 422,98, 422,97, 423,97, 423,98, 423,97, 423,97, 423,97, 423,97, 424,97, 424,97, 424,97, 424,97, 424,96, 425,96,425,96, 426,96, 426,96, 426,96, 427,96, 428,96, 428,96, 428,96, 428,97, 431,97, 431,97, 430,98, 431,98, 431,98, 431,98, 431,98, 431,98, 431,99, 431,99, 432,99, 431,99, 431,99, 431,99"/>
                        <area shape="poly" title="Italy" alt="Italy" href="#" coords="434,119, 433,119, 430,117, 429,117, 429,117, 428,117, 429,116, 429,116, 429,116, 430,116, 430,116, 431,116, 435,116, 435,116, 435,116, 435,117, 434,117, 434,118, 435,118, 435,118, 434,119, 434,119, 434,119, 434,119"/>
                        <area shape="poly" title="Italy" alt="Italy" href="#" coords="420,114, 420,114, 419,113, 419,113, 420,112, 420,111, 419,110, 419,110, 419,110, 420,110, 421,109, 423,110, 423,111, 422,111, 422,111, 422,112, 422,113, 422,113, 421,113, 421,113, 421,113, 421,114, 420,114, 420,114, 420,114"/>
                        <area shape="poly" title="Portugal" alt="Portugal" href="#" coords="384,118, 383,118, 383,118, 382,118, 381,118, 381,118, 381,117, 381,116, 381,116, 381,116, 381,115, 381,115, 381,115, 380,115, 380,115, 381,115, 381,114, 381,114, 381,114, 380,114, 380,115, 380,114, 380,114, 380,113, 380,113, 380,113, 381,112, 381,111, 381,111, 381,109, 381,108, 381,108,381,108, 381,107, 383,107, 383,107, 385,107, 386,107, 386,107, 386,107, 386,108, 387,108, 387,108, 386,109, 386,112, 385,112, 385,112, 384,113, 385,114, 385,115, 384,115, 385,115, 386,116, 385,116, 384,118, 384,118, 384,118"/>
                        <area shape="poly" title="Spain" alt="Spain" href="#" coords="381,107, 381,107, 382,106, 381,105, 380,105, 380,105, 380,105, 380,105, 381,104, 382,104, 382,104, 383,104, 383,103, 386,104, 387,103, 393,104, 394,104, 395,104, 396,104, 397,104, 397,104, 397,104, 398,105, 397,105, 397,105, 398,105, 398,105, 399,105, 399,105, 399,105, 399,105, 400,105,400,105, 400,106, 400,106, 401,106, 401,106, 402,106, 402,105, 404,106, 404,106, 404,106, 405,106, 405,106, 405,106, 405,106, 406,106, 406,106, 407,106, 407,106, 407,106, 408,106, 408,107, 408,107, 408,107, 406,108, 405,109, 403,109, 402,110, 402,110, 403,110, 403,110, 402,110, 400,113, 400,114, 401,114, 401,114, 400,115,400,116, 399,116, 399,117, 399,117, 399,117, 398,117, 397,118, 396,118, 396,119, 396,119, 395,119, 395,119, 394,119, 394,119, 391,119, 390,119, 389,120, 389,120, 389,120, 389,120, 389,120, 389,120, 389,120, 388,120, 387,120, 387,119, 387,119, 386,119, 386,119, 387,119, 386,118, 385,118, 384,118, 385,116, 386,116, 385,115,384,115, 385,115, 385,114, 384,113, 385,112, 385,112, 386,112, 386,109, 387,108, 387,108, 386,108, 386,107, 386,107, 386,107, 385,107, 383,107, 383,107, 381,107, 381,107"/>
                        <area shape="poly" title="France" alt="France" href="#" coords="397,104, 397,104, 397,104, 398,101, 398,101, 398,101, 398,101, 398,99, 398,99, 399,100, 399,100, 399,100, 398,99, 398,99, 398,98, 398,98, 398,97, 397,97, 396,96, 396,96, 396,96, 395,95, 395,95, 395,95, 395,95, 394,95, 393,94, 392,94, 392,94, 391,94, 391,94, 391,94, 390,94, 390,94, 391,94,391,93, 391,93, 391,93, 391,93, 390,93, 390,93, 391,93, 392,92, 392,92, 392,92, 393,92, 393,92, 394,92, 395,92, 395,93, 396,92, 396,92, 397,92, 397,92, 398,92, 397,92, 397,91, 397,91, 397,90, 396,90, 397,90, 398,90, 398,90, 398,91, 399,91, 400,91, 402,91, 401,90, 401,90, 401,90, 404,89, 404,89, 404,88, 404,88, 405,87, 406,87,406,87, 407,87, 407,88, 407,88, 408,88, 408,88, 408,88, 408,88, 409,88, 409,89, 410,89, 410,89, 410,89, 410,89, 410,89, 410,90, 411,89, 411,89, 412,89, 411,89, 412,90, 412,90, 412,90, 413,90, 413,90, 414,90, 414,90, 415,91, 416,91, 416,91, 416,91, 417,91, 417,91, 417,91, 417,91, 419,92, 418,92, 418,93, 418,93, 417,94, 418,94,417,94, 417,95, 417,95, 417,95, 417,95, 416,95, 416,95, 416,95, 416,95, 416,95, 416,96, 416,96, 415,96, 415,96, 414,97, 414,97, 414,97, 414,98, 414,98, 414,98, 415,97, 416,97, 416,98, 416,99, 416,99, 416,99, 417,99, 416,100, 416,100, 416,100, 415,100, 416,101, 416,101, 416,101, 416,101, 416,101, 416,102, 417,102, 418,102,418,102, 418,103, 415,105, 413,105, 412,104, 411,104, 411,104, 410,104, 409,104, 408,104, 408,105, 407,105, 408,106, 407,106, 407,106, 407,106, 406,106, 406,106, 405,106, 405,106, 405,106, 405,106, 404,106, 404,106, 404,106, 402,105, 402,106, 401,106, 401,106, 400,106, 400,106, 400,105, 400,105, 399,105, 399,105, 399,105,399,105, 398,105, 398,105, 397,105, 397,105, 398,105, 397,104, 397,104, 397,104, 397,104, 397,104"/>
                        <area shape="poly" title="France" alt="France" href="#" coords="422,105, 422,105, 422,106, 422,106, 422,106, 422,107, 422,107, 421,108, 421,109, 421,109, 421,108, 420,108, 420,108, 420,108, 420,108, 420,108, 420,107, 420,107, 420,107, 420,107, 420,107, 420,107, 420,107, 420,107, 420,107, 420,107, 420,106, 420,106, 421,106, 421,106, 421,106, 421,106,421,106, 421,105, 421,105, 421,105, 422,105, 422,105"/>
                        <area shape="poly" title="Luxembourg" alt="Luxembourg" href="#" coords="414,90, 414,90, 414,90, 414,90, 414,89, 414,89, 414,89, 414,89, 415,90, 415,90, 415,90, 415,90, 415,91, 415,91, 414,90, 414,90, 414,90"/>

                        <area shape="poly" title="Belgum" alt="Belgum" href="#" coords="406,87, 408,86, 409,86, 409,86, 411,86, 411,86, 411,86, 411,86, 412,86, 412,86, 412,86, 412,87, 413,86, 414,87, 414,87, 413,87, 413,88, 414,88, 414,88, 415,88, 415,89, 414,89, 414,89, 414,89, 414,90, 414,90, 414,90, 414,90, 414,90, 413,90, 413,90, 412,90, 412,90, 412,90, 411,89, 412,89,411,89, 411,89, 410,90, 410,89, 410,89, 410,89, 410,89, 410,89, 409,89, 409,88, 408,88, 408,88, 408,88, 408,88, 407,88, 407,88, 407,87, 406,87, 406,87, 406,87"/>
                        <area shape="poly" title="Netherland" alt="Netherland" href="#" coords="417,82, 417,83, 417,83, 416,84, 416,83, 416,84, 416,84, 416,84, 416,84, 416,84, 416,84, 416,85, 416,85, 415,85, 415,85, 414,85, 414,86, 414,86, 415,86, 414,87, 415,87, 414,87, 414,87, 414,87, 414,87, 414,88, 413,88, 413,87, 414,87, 414,87, 413,86, 412,87, 412,86, 412,86, 412,86, 411,86,411,86, 411,86, 411,86, 409,86, 409,86, 409,86, 410,85, 411,83, 411,83, 412,83, 412,83, 412,84, 412,84, 412,84, 412,84, 412,84, 413,84, 413,84, 414,84, 414,84, 413,84, 413,84, 413,83, 413,83, 413,83, 413,83, 413,83, 413,83, 413,82, 416,82, 416,82, 416,82, 416,82, 417,82, 417,82"/>
                        <area shape="poly" title="Poland" alt="Poland" href="#" coords="451,91, 450,91, 450,91, 449,91, 449,90, 448,90, 448,91, 447,91, 447,91, 446,91, 446,91, 446,91, 445,91, 445,91, 445,91, 445,91, 445,91, 445,91, 445,91, 444,91, 444,90, 444,90, 444,90, 444,90, 443,91, 443,91, 443,90, 443,90, 443,90, 442,90, 442,90, 442,89, 441,89, 441,89, 441,89, 440,89,440,89, 440,89, 440,89, 439,89, 439,88, 438,88, 438,88, 439,89, 438,89, 438,89, 438,89, 437,89, 437,88, 437,88, 437,88, 437,88, 437,88, 437,88, 437,88, 436,88, 436,88, 436,88, 436,88, 435,87, 435,87, 435,87, 435,87, 434,87, 434,87, 434,87, 434,87, 434,87, 434,87, 434,86, 433,86, 434,86, 433,85, 433,85, 433,85, 434,85, 433,85,433,84, 433,84, 433,84, 433,84, 433,83, 432,83, 432,83, 433,83, 433,82, 433,81, 433,81, 433,81, 433,81, 433,81, 433,81, 433,81, 433,81, 441,79, 442,79, 442,79, 442,79, 442,79, 442,80, 443,80, 445,79, 445,79, 444,80, 444,80, 444,80, 445,79, 452,79, 453,80, 454,83, 454,83, 454,83, 453,83, 453,84, 452,84, 452,84, 453,84, 453,84,453,85, 453,85, 453,86, 453,86, 453,86, 454,87, 454,87, 454,88, 454,88, 454,88, 454,88, 454,88, 451,90, 451,90, 451,91, 452,91, 451,91, 451,91, 451,91"/>
                        <area shape="poly" title="Ireland" alt="Ireland" href="#" coords="387,81, 386,81, 387,81, 387,83, 387,83, 387,84, 387,84, 385,84, 385,85, 384,85, 382,86, 379,86, 379,86, 379,85, 378,85, 378,85, 379,85, 377,85, 378,84, 379,84, 379,84, 379,84, 380,84, 380,83, 379,83, 380,83, 380,83, 380,82, 381,82, 381,82, 381,82, 380,82, 380,82, 380,82, 379,82, 379,82,379,82, 378,82, 379,82, 378,81, 379,81, 379,81, 379,81, 379,81, 378,81, 378,80, 379,80, 380,80, 380,80, 382,80, 381,80, 382,79, 383,79, 381,79, 381,79, 382,79, 382,78, 382,78, 384,78, 384,78, 384,78, 384,78, 384,78, 384,79, 383,79, 384,79, 383,79, 383,80, 383,80, 384,80, 385,80, 385,80, 385,80, 386,80, 386,80, 386,80, 387,80,387,81, 387,81, 387,81"/>
                        <area shape="poly" title="Britain" alt="Britain" href="#" coords="387,81, 387,80, 386,80, 386,80, 386,80, 385,80, 385,80, 385,80, 384,80, 383,80, 383,80, 383,79, 384,79, 383,79, 384,79, 384,78, 384,78, 386,78, 387,78, 387,78, 387,78, 388,79, 388,79, 388,79, 388,80, 388,80, 388,80, 387,81, 387,81, 387,81"/>
                        <area shape="poly" title="Britain" alt="Britain" href="#" coords="400,82, 401,82, 401,82, 401,83, 401,83, 402,83, 402,83, 403,83, 404,83, 404,83, 405,84, 404,84, 404,85, 401,86, 403,86, 403,86, 404,86, 404,87, 403,87, 403,87, 402,87, 401,88, 400,88, 399,88, 398,88, 396,88, 396,88, 395,88, 395,88, 394,88, 394,88, 393,88, 393,89, 393,89, 392,89, 392,89,391,88, 391,89, 391,89, 390,89, 389,89, 389,89, 388,89, 389,89, 390,88, 390,88, 391,88, 391,87, 391,87, 391,87, 391,87, 394,87, 394,86, 394,86, 395,86, 394,86, 393,86, 392,86, 390,86, 389,85, 389,85, 390,85, 391,84, 392,84, 392,83, 392,83, 391,83, 391,83, 392,82, 394,82, 394,82, 394,80, 394,80, 394,80, 393,80, 393,80, 393,79,394,79, 393,78, 392,79, 391,79, 391,79, 391,79, 389,79, 390,78, 390,77, 390,77, 390,77, 390,77, 391,76, 390,76, 390,76, 389,76, 389,76, 388,77, 388,76, 389,75, 389,75, 388,75, 388,75, 388,75, 387,75, 388,74, 388,74, 388,74, 388,73, 388,73, 389,73, 388,72, 388,72, 389,72, 390,72, 389,71, 390,71, 389,71, 390,70, 391,71, 394,70,394,71, 394,71, 392,72, 391,72, 392,73, 396,73, 396,73, 396,74, 396,74, 395,74, 395,75, 395,75, 394,75, 395,76, 395,76, 394,76, 394,76, 393,76, 392,76, 393,76, 394,76, 394,76, 395,76, 396,76, 397,77, 397,77, 397,78, 398,79, 399,79, 400,80, 400,81, 400,81, 400,82, 400,82, 400,82"/>
                        <area shape="poly" title="Denmark" alt="Denmark" href="#" coords="427,78, 427,78, 426,78, 425,77, 426,77, 426,76, 427,77, 427,77, 428,77, 428,76, 428,76, 428,77, 428,77, 428,77, 428,78, 427,78, 427,78, 427,78"/>
                        <area shape="poly" title="Denmark" alt="Denmark" href="#" coords="422,78, 420,78, 420,78, 420,77, 420,77, 419,77, 419,77, 419,77, 419,77, 419,77, 419,76, 419,76, 419,75, 419,75, 420,75, 420,75, 421,75, 421,75, 421,75, 422,74, 423,74, 424,75, 424,75, 425,75, 425,75, 425,76, 424,76, 424,76, 424,76, 423,76, 424,76, 423,77, 423,77, 423,77, 422,77, 422,77,422,77, 422,77, 422,78, 422,78, 422,78, 422,78, 422,78"/>
                        <area shape="poly" title="Denmark" alt="Denmark" href="#" coords="419,74, 419,74, 420,73, 420,74, 421,73, 422,73, 423,72, 424,72, 424,72, 424,72, 424,72, 424,73, 424,73, 424,73, 424,73, 424,74, 422,74, 422,74, 421,74, 421,74, 420,74, 420,74, 420,74, 420,75, 420,75, 419,74, 419,74, 419,74"/>
                        <area shape="poly" title="Iceland" alt="Iceland" href="#" coords="347,55, 347,55, 348,54, 349,54, 348,54, 349,53, 349,53, 350,54, 351,54, 351,54, 351,54, 350,53, 350,53, 353,53, 354,54, 353,54, 353,55, 354,56, 356,55, 356,55, 356,54, 356,54, 357,54, 358,55, 358,55, 358,54, 358,54, 358,54, 359,54, 361,54, 361,54, 361,54, 361,54, 364,54, 364,53, 364,53,366,53, 366,53, 368,54, 368,54, 368,54, 370,55, 371,55, 371,56, 371,56, 370,56, 369,57, 369,57, 369,57, 368,58, 368,58, 367,58, 367,58, 364,59, 362,59, 361,60, 357,60, 355,59, 351,59, 351,59, 352,58, 353,57, 352,57, 351,57, 351,57, 349,57, 348,57, 348,57, 352,56, 352,56, 352,56, 352,55, 352,55, 348,55, 347,55, 347,55, 347,55,347,55"/>
                        <area shape="poly" title="Norway" alt="Norway" href="#" coords="426,69, 425,69, 425,69, 425,69, 424,68, 424,68, 424,68, 424,68, 424,69, 424,69, 423,69, 423,69, 423,69, 422,69, 422,70, 422,70, 419,71, 419,71, 416,72, 416,71, 416,71, 416,71, 415,71, 416,71, 413,71, 413,70, 413,70, 413,69, 413,69, 413,70, 414,70, 414,70, 414,70, 414,69, 415,69, 414,69,414,69, 414,69, 415,69, 414,69, 414,69, 414,68, 414,68, 414,69, 414,68, 413,68, 413,68, 413,69, 413,69, 413,69, 413,68, 413,69, 412,69, 412,68, 413,68, 413,68, 413,68, 413,68, 414,68, 413,68, 414,68, 415,68, 413,68, 413,68, 414,67, 414,67, 413,67, 412,67, 413,66, 413,66, 412,66, 412,66, 412,66, 412,65, 413,65, 412,65, 412,64,412,64, 412,63, 412,63, 414,63, 414,63, 413,63, 413,63, 413,62, 413,62, 414,62, 414,62, 415,62, 415,62, 415,61, 417,62, 418,61, 417,61, 416,61, 417,61, 419,61, 419,61, 419,60, 420,60, 420,60, 421,60, 421,60, 422,59, 423,59, 423,59, 425,57, 425,57, 426,57, 426,57, 426,57, 426,57, 428,56, 428,56, 429,56, 429,55, 428,55, 429,54,430,54, 430,53, 431,53, 431,53, 430,53, 430,53, 430,53, 431,52, 432,52, 431,52, 433,51, 434,51, 433,51, 433,51, 434,50, 435,51, 435,50, 436,50, 436,50, 435,50, 436,50, 436,49, 437,49, 437,50, 437,50, 438,49, 438,49, 439,49, 439,48, 438,48, 439,48, 440,47, 440,47, 441,47, 441,47, 441,47, 441,47, 441,46, 442,47, 443,46, 444,47,446,45, 446,45, 446,46, 446,46, 446,46, 447,45, 448,45, 448,45, 449,45, 450,44, 451,45, 452,45, 454,44, 454,44, 455,44, 455,43, 458,43, 457,44, 457,44, 457,45, 457,45, 460,43, 460,44, 461,44, 462,43, 463,43, 464,43, 463,43, 463,44, 463,44, 464,44, 464,44, 465,43, 469,44, 469,44, 468,45, 465,45, 466,45, 467,46, 469,45, 470,46,469,46, 467,46, 465,48, 465,47, 466,46, 465,46, 463,45, 460,45, 458,46, 458,48, 456,48, 454,48, 451,48, 451,48, 449,47, 448,47, 448,47, 447,47, 447,47, 446,47, 445,49, 445,49, 441,48, 441,48, 441,49, 441,49, 441,50, 439,50, 438,50, 437,51, 437,52, 437,52, 434,54, 433,54, 433,54, 433,55, 431,57, 432,57, 432,58, 432,58, 432,58,430,58, 429,59, 428,60, 427,61, 428,62, 428,63, 428,64, 429,64, 429,64, 429,65, 428,65, 429,66, 428,66, 428,67, 428,67, 427,67, 427,68, 427,68, 427,69, 426,70, 426,70, 426,69, 426,69, 426,69, 426,69"/>
                        <area shape="poly" title="Norway" alt="Norway" href="#" coords="430,23, 430,24, 431,24, 432,24, 433,23, 434,23, 437,25, 437,24, 436,23, 436,23, 437,23, 440,23, 440,24, 440,24, 440,24, 441,24, 442,24, 447,25, 443,26, 442,28, 440,28, 440,29, 439,29, 439,30, 438,30, 433,29, 434,28, 437,28, 438,28, 437,27, 432,28, 432,27, 438,26, 434,25, 434,26, 433,26,431,27, 427,25, 429,25, 428,24, 426,25, 426,24, 425,24, 426,23, 430,23, 430,23, 430,23"/>
                        <area shape="poly" title="Norway" alt="Norway" href="#" coords="450,23, 442,23, 442,22, 443,22, 445,21, 446,22, 449,22, 450,22, 451,22, 452,22, 452,21, 452,22, 452,22, 453,22, 455,22, 456,22, 456,22, 461,22, 461,23, 447,24, 447,24, 449,24, 450,23, 450,23, 450,23"/>
                        <area shape="poly" title="Germany" alt="Germany" href="#" coords="417,95, 417,94, 418,94, 417,94, 418,93, 418,93, 418,92, 419,92, 417,91, 417,91, 417,91, 417,91, 416,91, 416,91, 416,91, 415,91, 415,90, 415,90, 415,90, 415,90, 414,89, 414,89, 415,89, 415,88, 414,88, 414,88, 414,87, 414,87, 414,87, 414,87, 415,87, 414,87, 415,86, 414,86, 414,86, 414,85,415,85, 415,85, 416,85, 416,85, 416,84, 416,84, 416,84, 416,84, 416,84, 416,84, 416,83, 416,84, 417,83, 417,83, 417,82, 417,82, 416,82, 416,82, 417,81, 418,81, 419,82, 419,82, 419,82, 419,82, 419,82, 419,81, 419,82, 420,82, 420,81, 420,81, 420,81, 421,81, 420,81, 420,81, 421,80, 420,80, 420,80, 420,80, 421,80, 420,80, 420,80,420,80, 421,80, 420,79, 420,78, 422,78, 423,79, 423,79, 423,79, 423,80, 423,79, 423,80, 423,80, 424,80, 424,80, 424,80, 425,80, 425,80, 425,80, 425,81, 425,81, 426,81, 426,81, 426,81, 426,80, 429,80, 429,80, 430,80, 430,80, 430,80, 431,80, 431,80, 431,80, 431,81, 432,81, 432,81, 432,81, 433,81, 433,82, 433,83, 432,83, 432,83,433,83, 433,84, 433,84, 433,84, 433,84, 433,85, 434,85, 433,85, 433,85, 433,85, 434,86, 433,86, 434,86, 434,87, 434,87, 434,87, 433,87, 433,87, 433,87, 432,87, 433,87, 432,87, 432,88, 431,88, 431,88, 431,88, 430,88, 430,88, 430,88, 430,88, 429,88, 429,88, 428,88, 428,89, 428,89, 429,89, 429,90, 428,90, 429,90, 429,90, 429,91,430,91, 430,91, 431,91, 431,92, 431,91, 431,92, 431,92, 432,92, 431,93, 431,92, 431,92, 430,93, 430,93, 429,93, 430,94, 429,94, 430,94, 430,95, 430,95, 430,95, 429,95, 429,95, 429,94, 428,95, 428,94, 428,94, 428,95, 426,95, 426,95, 426,95, 426,95, 425,95, 425,95, 424,95, 424,95, 424,95, 424,95, 423,95, 423,95, 423,95, 423,95,423,95, 423,95, 423,95, 423,95, 422,95, 422,95, 422,95, 421,94, 420,94, 420,94, 420,94, 420,94, 419,94, 419,95, 419,95, 419,95, 418,95, 418,95, 418,95, 417,95, 417,95, 417,95"/>
                        <area shape="poly" title="Germany" alt="Germany" href="#" coords="432,81, 432,81, 432,81, 432,81, 431,81, 431,80, 432,81, 432,81, 432,81, 432,81"/>
                        <area shape="poly" title="Finland" alt="Finland" href="#" coords="465,48, 465,48, 464,49, 464,49, 467,50, 465,52, 467,54, 467,56, 467,56, 468,58, 468,58, 467,59, 468,59, 470,60, 470,61, 471,61, 470,61, 469,62, 464,65, 464,66, 462,66, 462,66, 461,66, 461,66, 461,66, 460,66, 460,66, 460,66, 460,66, 459,67, 458,67, 458,67, 458,67, 457,67, 457,67, 456,67,456,67, 455,67, 455,67, 455,67, 454,67, 454,67, 452,68, 452,67, 452,67, 452,67, 451,67, 451,67, 451,66, 450,67, 449,66, 448,66, 448,65, 449,64, 448,63, 448,62, 448,62, 448,62, 448,62, 448,61, 448,61, 449,61, 449,61, 449,60, 449,60, 450,60, 450,60, 451,60, 451,59, 451,59, 451,59, 452,59, 456,56, 457,56, 457,56, 457,56, 457,56,457,55, 457,55, 455,55, 455,54, 454,54, 454,54, 454,54, 453,53, 454,52, 454,52, 453,51, 453,50, 452,49, 449,48, 447,47, 447,47, 448,47, 448,47, 449,47, 451,48, 454,48, 456,48, 458,48, 458,46, 460,45, 463,45, 465,46, 466,46, 465,47, 465,48, 465,48, 465,48"/>
                        <area shape="poly" title="Sweden" alt="Sweden" href="#" coords="454,54, 452,54, 451,54, 450,54, 450,55, 449,55, 449,55, 448,56, 448,56, 449,56, 447,57, 448,57, 448,57, 448,57, 448,58, 447,59, 444,60, 444,59, 444,59, 443,60, 442,60, 442,61, 441,61, 440,61, 441,61, 440,62, 439,62, 439,62, 440,62, 439,63, 439,63, 439,64, 439,65, 439,65, 439,66, 440,66,440,66, 441,66, 442,67, 443,67, 443,67, 443,67, 443,68, 443,68, 441,69, 440,69, 440,68, 438,68, 438,68, 438,68, 437,68, 438,68, 439,69, 439,69, 439,69, 440,69, 440,69, 440,69, 441,69, 442,69, 442,69, 442,68, 442,68, 442,69, 442,69, 441,69, 442,69, 441,69, 441,70, 440,70, 440,70, 440,69, 440,69, 440,69, 440,70, 439,70, 439,70,439,70, 439,70, 437,70, 437,71, 438,71, 438,71, 438,72, 438,72, 437,72, 437,72, 438,72, 438,73, 438,73, 437,73, 437,74, 437,74, 436,76, 435,76, 435,76, 433,76, 433,76, 433,76, 432,76, 432,77, 433,77, 432,77, 432,77, 430,78, 429,77, 429,77, 429,77, 429,77, 430,77, 428,75, 429,75, 429,75, 429,75, 429,75, 429,75, 429,75, 429,74,428,74, 427,73, 427,73, 427,73, 427,72, 427,71, 427,71, 426,71, 426,71, 426,71, 426,70, 425,70, 426,69, 426,69, 426,70, 426,70, 427,69, 427,68, 427,68, 427,67, 428,67, 428,67, 428,66, 429,66, 428,65, 429,65, 429,64, 429,64, 428,64, 428,63, 428,62, 427,61, 428,60, 429,59, 430,58, 432,58, 432,58, 432,58, 432,57, 431,57, 433,55,433,54, 433,54, 434,54, 437,52, 437,52, 437,51, 438,50, 439,50, 441,50, 441,49, 441,49, 441,48, 441,48, 445,49, 445,49, 446,47, 447,47, 449,48, 452,49, 453,50, 453,51, 454,52, 454,52, 453,53, 454,54, 454,54, 454,54, 454,54, 454,54"/>

                        <!-- africa -->
                        <area shape="poly" title="Gambia" alt="Gambia" href="#" coords="364,170, 366,170, 366,170, 366,170, 366,170, 367,170, 367,170, 367,170, 367,170, 367,170, 368,170, 368,170, 368,170, 368,170, 368,170, 368,171, 369,171, 369,170, 369,170, 370,170, 370,171, 370,171, 370,171, 369,171, 369,171, 368,171, 368,171, 368,171, 368,171, 368,171, 368,171, 367,170,367,170, 367,170, 367,170, 367,171, 366,171, 366,171, 366,171, 366,171, 365,171, 365,171, 364,171, 364,171, 364,171, 364,171, 364,170, 364,170, 364,170, 364,170"/>
                        <area shape="poly" title="Guinea-Bissau" alt="Guinea-Bissau" href="#" coords="364,173, 367,173, 367,172, 370,172, 370,174, 370,174, 370,175, 369,175, 367,176, 367,176, 367,176, 367,175, 367,175, 367,174, 367,174, 367,174, 366,174, 366,174, 366,174, 365,174, 365,173, 364,173, 364,173, 364,173"/>
                        <area shape="poly" title="Guinea" alt="Guinea" href="#" coords="383,178, 383,178, 383,179, 383,179, 383,180, 384,180, 384,180, 383,181, 384,182, 384,182, 384,182, 384,182, 383,182, 383,182, 383,182, 383,182, 383,183, 383,183, 382,184, 382,184, 382,184, 382,184, 382,184, 381,185, 380,185, 380,184, 380,183, 379,182, 379,182, 379,182, 378,182, 378,182,378,183, 377,180, 376,179, 376,178, 374,179, 373,179, 372,181, 372,180, 371,179, 370,178, 369,177, 368,177, 368,177, 368,176, 368,176, 367,176, 367,176, 367,176, 369,175, 369,175, 370,175, 370,174, 370,174, 370,172, 372,172, 373,173, 375,173, 376,173, 377,174, 378,173, 379,174, 381,173, 381,173, 381,173, 381,174, 382,176,383,177, 383,178, 383,178"/>
                        <area shape="poly" title="Sierra Leone"" alt="Sierra Leone"" href="#" coords="372,181, 373,179, 374,179, 376,178, 377,179, 377,180, 378,183, 377,183, 375,185, 374,184, 373,184, 374,184, 374,183, 372,183, 371,182, 372,182, 372,181, 372,181, 372,181, 372,181, 372,181, 372,181, 372,181"/>
                        <area shape="poly" title="Liberia" alt="Liberia" href="#" coords="378,183, 378,182, 378,182, 379,182, 379,182, 379,182, 380,183, 380,184, 380,185, 381,185, 382,184, 382,184, 382,184, 382,184, 382,184, 382,185, 382,185, 382,186, 382,186, 382,186, 382,186, 383,186, 384,188, 384,188, 384,191, 375,185, 377,183, 378,183, 378,183, 378,183"/>
                        <area shape="poly" title="Ivory Coast"" alt="Ivory Coast"" href="#" coords="384,191, 384,188, 384,188, 383,186, 382,186, 382,186, 382,186, 382,186, 382,185, 382,185, 382,184, 382,184, 383,183, 383,183, 383,182, 383,182, 383,182, 383,182, 384,182, 384,182, 384,182, 384,182, 383,181, 384,180, 384,180, 383,180, 383,179, 383,179, 383,178, 384,177, 384,177, 385,178,386,177, 387,177, 387,177, 387,177, 387,177, 387,178, 388,178, 388,177, 389,178, 389,178, 390,178, 390,178, 390,179, 391,179, 393,179, 394,179, 395,179, 395,182, 394,186, 395,189, 395,189, 394,189, 394,189, 394,189, 394,189, 390,189, 384,191, 384,191, 384,191, 384,191, 384,191"/>
                        <area shape="poly" title="Burkina Faso"" alt="Burkina Faso"" href="#" coords="401,176, 400,176, 399,176, 395,176, 395,176, 394,177, 395,179, 394,179, 394,179, 393,179, 391,179, 390,179, 390,178, 390,178, 389,178, 389,178, 388,177, 389,177, 389,175, 389,174, 389,174, 390,174, 391,173, 391,171, 392,171, 392,171, 393,171, 393,171, 395,169, 395,169, 396,169, 396,169,397,168, 399,167, 400,167, 401,167, 401,168, 401,168, 402,169, 403,171, 403,171, 403,171, 403,172, 404,172, 404,172, 405,172, 406,174, 406,175, 405,175, 405,175, 404,175, 404,175, 403,176, 403,176, 403,176, 401,176, 401,176, 401,176"/>
                        <area shape="poly" title="Ghana" alt="Ghana" href="#" coords="394,179, 395,179, 394,177, 395,176, 395,176, 399,176, 400,176, 401,176, 401,177, 401,177, 402,178, 401,179, 402,182, 402,182, 402,183, 402,185, 402,186, 403,187, 403,188, 396,190, 395,189, 394,186, 395,182, 395,179, 394,179, 394,179, 394,179"/>
                        <area shape="poly" title="Togo" alt="Togo" href="#" coords="401,176, 403,176, 403,177, 403,178, 404,178, 404,185, 404,185, 405,187, 404,187, 403,187, 403,187, 402,186, 402,185, 402,183, 402,182, 402,182, 401,179, 402,178, 401,177, 401,177, 401,176, 401,176, 401,176"/>
                        <area shape="poly" title="Benin" alt="Benin" href="#" coords="409,174, 409,175, 409,176, 409,177, 409,178, 409,178, 408,179, 408,179, 408,180, 408,180, 407,180, 407,181, 407,186, 405,187, 404,185, 404,180, 404,178, 403,178, 403,177, 403,176, 403,176, 404,175, 404,175, 405,175, 405,175, 406,175, 406,174, 406,173, 407,173, 409,174, 409,174, 409,174"/>
                        <area shape="poly" title="Nigeria" alt="Nigeria" href="#" coords="432,171, 432,172, 432,173, 432,173, 433,173, 433,173, 433,174, 433,175, 433,175, 433,175, 432,176, 431,176, 430,179, 429,179, 429,181, 429,181, 428,181, 426,186, 426,186, 425,186, 425,185, 423,185, 420,188, 420,189, 420,190, 420,190, 419,190, 419,190, 419,190, 419,190, 419,190, 419,191,417,191, 417,190, 416,190, 416,191, 416,191, 414,191, 414,191, 413,190, 413,189, 413,189, 413,188, 413,188, 412,188, 411,187, 410,186, 410,186, 409,186, 409,186, 407,186, 407,181, 407,180, 408,180, 408,180, 408,179, 408,179, 409,178, 409,178, 409,177, 409,176, 409,175, 409,173, 410,170, 411,170, 413,170, 415,170, 416,171,416,172, 417,172, 418,171, 419,171, 420,172, 421,172, 422,172, 423,171, 426,171, 428,171, 428,171, 430,170, 431,170, 431,170, 432,171, 432,171, 432,171"/>
                        <area shape="poly" title="Equatorial Guinea"" alt=""" href="#" coords="423,196, 426,196, 426,198, 422,198, 422,198, 422,198, 422,198, 422,198, 423,196, 423,196, 423,196, 423,196"/>
                        <area shape="poly" title="Equatorial Guinea"" alt="Equatorial Guinea"" href="#" coords="420,194, 419,194, 419,193, 419,193, 420,193, 420,193, 420,192, 420,192, 421,193, 420,193, 420,194, 420,194, 420,194"/>
                        <area shape="poly" title="Gabon" alt="Gabon" href="#" coords="426,196, 426,195, 430,196, 430,196, 430,198, 430,198, 430,198, 431,197, 432,197, 432,198, 433,199, 433,199, 432,200, 431,200, 431,201, 432,201, 432,201, 433,202, 433,202, 433,202, 432,206, 432,206, 432,206, 432,206, 431,205, 431,205, 430,206, 430,206, 430,206, 429,205, 429,205, 429,205,428,205, 428,206, 428,206, 427,206, 427,206, 426,207, 427,207, 427,208, 427,208, 427,209, 426,209, 426,209, 426,209, 423,206, 423,206, 423,206, 422,206, 421,204, 422,204, 422,204, 421,203, 420,202, 421,202, 422,200, 423,200, 423,200, 422,199, 422,199, 422,198, 426,198, 426,196, 426,196, 426,196"/>
                        <area shape="poly" title="Central African Republic"" alt="Central African Republic"" href="#" coords="437,196, 436,194, 434,192, 434,191, 433,190, 433,187, 433,187, 434,186, 435,184, 435,184, 435,184, 436,184, 438,183, 438,184, 438,184, 438,184, 440,183, 442,183, 442,183, 443,181, 443,181, 443,180, 446,180, 449,178, 449,177, 449,177, 450,176, 451,176, 451,176, 451,176, 453,178, 453,179,453,180, 453,181, 453,181, 454,181, 454,181, 455,182, 455,182, 457,183, 457,183, 457,184, 457,184, 459,185, 459,186, 459,187, 459,187, 460,187, 461,187, 461,188, 461,188, 461,189, 461,189, 460,189, 457,189, 457,189, 457,189, 457,190, 457,190, 455,189, 453,190, 453,190, 452,190, 451,190, 451,191, 450,192, 446,191, 446,190,445,190, 444,189, 444,189, 442,191, 442,191, 442,193, 441,193, 439,193, 438,193, 438,193, 437,195, 437,196, 437,196, 437,196"/>
                        <area shape="poly" title="Cameroon" alt="Cameroon" href="#" coords="426,196, 423,196, 423,196, 423,195, 423,194, 422,192, 423,192, 422,192, 422,191, 421,192, 421,191, 421,191, 421,190, 421,190, 420,190, 420,190, 420,189, 420,188, 423,185, 425,185, 425,186, 426,186, 426,186, 428,181, 429,181, 429,181, 429,179, 430,179, 431,176, 432,176, 433,175, 433,175,433,175, 433,174, 433,173, 433,173, 432,173, 432,173, 432,172, 432,171, 433,172, 434,174, 434,175, 434,176, 434,177, 435,178, 435,179, 433,178, 432,178, 432,179, 434,181, 435,184, 435,184, 435,184, 434,186, 433,187, 433,187, 433,187, 434,191, 434,191, 434,192, 436,194, 437,196, 436,196, 436,197, 436,197, 426,195, 426,196,426,196, 426,196"/>

                        <area shape="poly" title="Congo" alt="Congo" href="#" coords="430,211, 429,211, 429,211, 427,212, 427,211, 426,209, 426,209, 426,209, 427,209, 427,208, 427,208, 427,207, 426,207, 427,206, 427,206, 428,206, 428,206, 428,205, 429,205, 429,205, 429,205, 430,206, 430,206, 430,206, 431,205, 431,205, 432,206, 432,206, 432,206, 432,206, 433,202, 433,202,433,202, 432,201, 432,201, 431,201, 431,200, 432,200, 433,199, 433,199, 432,198, 432,197, 431,197, 430,198, 430,198, 430,198, 430,196, 430,196, 436,197, 436,197, 436,196, 437,195, 438,193, 438,193, 439,193, 441,193, 442,193, 442,193, 440,202, 438,204, 437,205, 436,207, 436,208, 436,209, 433,211, 433,211, 433,210, 431,211,431,211, 430,211, 430,211, 430,211, 430,211, 430,211"/>
                        <area shape="poly" title="Zaire" alt="Zaire" href="#" coords="430,211, 430,211, 430,211, 431,211, 431,211, 433,210, 433,211, 433,211, 436,209, 436,208, 436,207, 437,206, 437,204, 439,203, 439,203, 440,202, 442,193, 442,193, 442,193, 442,192, 442,191, 442,191, 443,190, 444,189, 444,189, 445,190, 446,190, 446,191, 450,192, 451,191, 451,190, 452,190,453,190, 453,190, 455,189, 457,190, 457,190, 457,189, 457,189, 457,189, 460,189, 461,189, 463,191, 464,191, 464,191, 465,191, 465,191, 466,191, 466,191, 466,190, 467,190, 467,190, 469,193, 469,193, 469,193, 469,195, 469,195, 470,195, 470,196, 470,196, 470,196, 467,199, 467,203, 465,204, 465,206, 466,208, 466,209, 466,210,466,212, 466,213, 466,214, 466,215, 468,216, 469,219, 465,219, 465,220, 464,221, 464,224, 464,226, 464,226, 465,228, 465,228, 466,228, 466,228, 467,227, 467,228, 467,230, 465,230, 465,230, 464,229, 463,228, 462,228, 462,228, 461,226, 460,227, 460,227, 457,227, 457,225, 455,226, 455,226, 455,225, 455,225, 453,225, 450,225,450,225, 450,223, 449,222, 449,219, 449,219, 449,218, 449,217, 449,217, 449,217, 447,217, 446,217, 447,216, 446,216, 444,216, 444,216, 444,217, 444,218, 440,218, 440,218, 438,216, 438,214, 438,214, 437,214, 430,214, 429,214, 428,214, 428,213, 428,213, 429,213, 429,213, 429,212, 430,211, 430,211, 430,211"/>
                        <area shape="poly" title="Uganda" alt="Uganda" href="#" coords="466,204, 467,203, 467,199, 470,196, 470,196, 470,196, 470,195, 469,195, 469,195, 469,193, 469,193, 469,193, 470,192, 472,193, 474,192, 475,192, 475,192, 476,192, 476,192, 477,193, 477,194, 479,196, 479,197, 479,197, 475,203, 468,203, 467,204, 467,204, 466,204, 466,204, 466,204"/>
                        <area shape="poly" title="Rwanda" alt="Rwanda" href="#" coords="465,207, 465,206, 465,204, 466,204, 467,204, 467,204, 468,203, 469,203, 469,205, 469,206, 469,206, 467,206, 467,207, 466,207, 466,207, 465,207, 465,207, 465,207"/>
                        <area shape="poly" title="Burundi" alt="Burundi" href="#" coords="466,211, 466,210, 466,209, 466,208, 465,207, 466,207, 466,207, 467,207, 467,206, 468,206, 468,207, 469,207, 469,207, 469,208, 469,208, 467,211, 466,211, 466,211, 466,211"/>
                        <area shape="poly" title="Angola" alt="Angola" href="#" coords="428,214, 429,214, 430,214, 437,214, 438,214, 439,218, 440,218, 440,218, 444,218, 444,217, 444,216, 444,216, 446,216, 447,216, 446,217, 447,217, 449,217, 449,217, 449,217, 449,218, 449,219, 449,220, 449,221, 449,222, 450,223, 450,225, 450,225, 453,225, 454,225, 454,226, 454,227, 454,228,454,228, 454,228, 454,229, 454,229, 454,229, 450,229, 450,237, 452,240, 446,240, 446,240, 442,240, 442,239, 431,239, 430,238, 430,238, 428,239, 428,239, 427,239, 427,239, 427,236, 428,235, 429,230, 429,230, 430,229, 430,229, 430,229, 431,228, 432,226, 431,224, 430,221, 430,221, 431,220, 431,219, 428,214, 428,214"/>
                        <area shape="poly" title="Angola" alt="Angola" href="#" coords="427,212, 429,211, 429,211, 430,211, 429,212, 429,213, 429,213, 428,213, 428,213, 427,212, 427,212, 427,212"/>
                        <area shape="poly" title="Zambia" alt="Zambia" href="#" coords="452,240, 450,237, 450,229, 454,229, 454,229, 454,229, 454,228, 454,228, 454,228, 454,227, 454,226, 454,225, 455,225, 455,225, 455,226, 455,226, 457,226, 457,227, 460,227, 460,227, 461,226, 462,228, 464,228, 465,230, 465,230, 467,230, 467,228, 467,227, 466,228, 466,228, 465,228, 464,226,464,226, 464,224, 464,221, 465,220, 465,219, 469,219, 470,220, 474,222, 476,224, 476,224, 475,224, 475,225, 475,227, 475,228, 476,228, 475,228, 474,229, 474,231, 474,232, 474,232, 468,234, 468,234, 468,235, 467,235, 466,235, 465,236, 465,237, 465,237, 465,237, 463,238, 461,241, 460,241, 459,241, 459,240, 458,241, 458,241,457,240, 457,240, 457,240, 456,240, 455,239, 454,239, 454,239, 452,240, 452,240, 452,240"/>
                        <area shape="poly" title="Malawi" alt="Malawi" href="#" coords="477,226, 477,228, 478,230, 480,233, 481,233, 480,236, 480,236, 479,236, 479,237, 479,238, 479,239, 477,236, 477,236, 477,235, 477,233, 477,233, 477,233, 476,233, 475,232, 474,232, 474,231, 474,229, 475,228, 476,228, 475,228, 475,227, 475,225, 475,224, 476,224, 476,224, 475,222, 476,222,476,223, 477,223, 477,226, 477,226"/>
                        <area shape="poly" title="Namibia" alt="Namibia" href="#" coords="445,256, 445,264, 443,265, 440,265, 440,264, 440,264, 440,263, 439,263, 438,263, 438,264, 437,264, 435,262, 434,260, 434,259, 434,258, 434,257, 434,256, 433,252, 434,252, 434,251, 433,251, 433,251, 433,250, 427,240, 427,239, 427,239, 428,239, 428,239, 430,238, 430,238, 430,238, 431,239,442,239, 442,240, 446,240, 446,240, 454,239, 454,239, 455,239, 456,240, 456,240, 456,240, 455,240, 454,241, 453,241, 453,241, 453,241, 447,241, 447,249, 445,250, 445,256, 445,256, 445,256"/>
                        <area shape="poly" title="Botswana" alt="Botswana" href="#" coords="456,240, 457,240, 457,240, 457,240, 457,240, 457,241, 458,242, 459,244, 461,245, 461,245, 461,246, 461,246, 461,246, 462,246, 462,246, 462,247, 463,248, 465,249, 465,249, 466,250, 466,250, 465,250, 463,251, 461,253, 460,255, 458,256, 458,257, 457,258, 457,258, 455,258, 453,257, 452,257,452,257, 451,258, 450,259, 449,260, 447,260, 447,260, 446,260, 446,260, 447,258, 446,257, 445,256, 445,256, 445,250, 447,249, 447,241, 453,241, 453,241, 453,241, 454,241, 455,240, 456,240, 456,240, 456,240, 456,240"/>
                        <area shape="poly" title="Zimbabwe" alt="Zimbabwe" href="#" coords="468,235, 468,236, 469,236, 470,236, 473,237, 474,238, 474,241, 473,243, 474,244, 474,245, 474,245, 473,246, 473,247, 473,247, 473,248, 473,248, 470,251, 470,250, 469,250, 468,250, 467,250, 466,250, 466,250, 465,249, 465,249, 463,248, 462,247, 462,246, 462,246, 461,246, 461,246, 461,246,461,245, 461,245, 459,244, 458,242, 457,241, 457,240, 458,241, 458,241, 459,240, 459,241, 460,241, 461,241, 463,238, 465,237, 465,237, 465,237, 465,236, 466,235, 468,235, 468,235, 468,235, 468,235"/>
                        <area shape="poly" title="Swaziland" alt="Swaziland" href="#" coords="472,258, 472,259, 472,259, 472,260, 472,260, 472,261, 472,261, 470,261, 470,261, 469,260, 469,260, 470,258, 470,258, 472,258, 472,258, 472,258"/>
                        <area shape="poly" title="Kenya" alt="Kenya" href="#" coords="493,205, 492,205, 492,205, 492,206, 491,206, 491,206, 490,207, 490,208, 489,210, 489,210, 489,211, 488,211, 488,211, 488,211, 484,209, 484,208, 485,208, 484,207, 476,203, 475,203, 479,197, 479,197, 479,196, 477,194, 477,193, 476,191, 477,190, 482,191, 485,193, 489,193, 491,191, 492,192,494,192, 492,194, 492,203, 493,205, 493,205, 493,205"/>
                        <area shape="poly" title="Tanzania" alt="Tanzania" href="#" coords="469,203, 476,203, 484,207, 485,208, 484,208, 484,209, 488,211, 487,214, 487,215, 488,216, 489,216, 489,216, 488,217, 488,218, 488,218, 488,218, 489,218, 488,220, 489,222, 491,224, 487,226, 486,226, 485,225, 485,225, 484,226, 484,227, 482,227, 481,226, 481,226, 481,226, 481,226, 480,226,477,226, 477,223, 476,223, 476,222, 475,222, 474,222, 470,220, 469,219, 468,216, 466,215, 466,214, 466,213, 466,212, 466,211, 467,211, 469,208, 469,208, 469,207, 469,207, 468,207, 468,206, 469,206, 469,206, 469,206, 469,205, 469,203, 469,203, 469,203"/>
                        <area shape="poly" title="Mozambique" alt="Mozambique" href="#" coords="474,260, 472,260, 472,260, 470,251, 470,251, 473,248, 473,248, 473,247, 473,247, 473,246, 474,245, 474,245, 474,244, 473,243, 474,241, 474,238, 473,237, 470,236, 469,236, 468,236, 468,234, 474,232, 475,232, 476,233, 477,233, 477,233, 477,233, 477,235, 477,236, 477,236, 478,238, 479,239,479,238, 479,237, 479,236, 480,236, 480,236, 481,233, 480,233, 478,230, 477,228, 477,226, 480,226, 481,226, 481,226, 481,226, 481,226, 482,227, 484,227, 484,226, 485,225, 485,225, 486,226, 487,226, 491,224, 491,224, 491,225, 490,226, 491,228, 491,229, 490,229, 491,231, 491,233, 491,233, 492,233, 492,233, 491,234, 491,234,491,235, 491,235, 478,245, 478,246, 478,246, 479,247, 479,247, 479,248, 479,250, 479,250, 480,250, 479,253, 480,254, 480,254, 479,255, 474,257, 473,258, 473,259, 474,259, 474,259, 474,260, 474,260, 474,260"/>
                        <area shape="poly" title="Lesotho" alt="Lesotho" href="#" coords="463,269, 463,269, 463,268, 465,267, 466,266, 466,266, 464,264, 462,265, 461,267, 461,267, 462,268, 463,269, 463,269, 463,269"/>

                        <area shape="poly" title="South Africa"" alt="South Africa"" href="#" coords="445,256, 445,256, 447,258, 447,258, 446,260, 446,260, 447,260, 447,260, 449,260, 450,259, 451,258, 452,257, 452,257, 453,257, 454,258, 457,258, 457,258, 458,257, 458,256, 459,256, 460,255, 460,255, 461,253, 463,251, 465,251, 465,250, 466,250, 466,250, 468,250, 469,250, 470,250, 470,251,472,258, 470,258, 470,258, 469,260, 469,260, 470,261, 470,261, 472,261, 472,261, 472,260, 474,260, 473,262, 473,263, 473,263, 473,263, 473,264, 473,264, 460,276, 458,276, 458,276, 458,276, 457,276, 456,276, 456,277, 455,277, 454,276, 453,276, 452,277, 451,276, 450,277, 449,277, 446,277, 446,277, 445,278, 444,278, 444,277,443,277, 443,277, 443,277, 442,276, 442,276, 442,277, 442,277, 442,277, 441,277, 442,276, 441,274, 441,274, 440,274, 441,274, 441,274, 441,273, 442,272, 438,264, 437,264, 438,264, 438,263, 439,263, 440,263, 440,264, 440,264, 440,265, 443,265, 445,264, 445,256, 445,256, 445,256"/>
                        <area shape="poly" title="South Africa"" alt="South Africa"" href="#" coords="433,251, 434,251, 434,252, 433,252, 433,252, 433,251, 433,251, 433,251, 433,251"/>
                        <area shape="poly" title="Western Sahara"" alt="Western Sahara"" href="#" coords="363,153, 363,152, 363,152, 364,150, 366,147, 368,145, 368,143, 369,142, 371,141, 372,139, 382,139, 381,143, 374,143, 374,148, 372,149, 372,150, 372,153, 363,153, 363,153, 363,153"/>
                        <area shape="poly" title="Mauritania" alt="Mauritania" href="#" coords="374,168, 372,166, 371,164, 371,164, 370,164, 369,163, 368,163, 365,163, 364,164, 365,161, 365,159, 365,158, 364,157, 364,157, 365,157, 365,155, 364,155, 364,154, 363,154, 363,154, 363,153, 372,153, 372,150, 372,149, 374,148, 374,143, 381,143, 382,140, 390,145, 386,145, 388,164, 389,164,389,166, 377,166, 377,166, 377,167, 377,167, 377,167, 376,167, 376,166, 376,166, 375,166, 375,166, 375,167, 375,167, 374,168, 374,168"/>
                        <area shape="poly" title="Senegal" alt="Senegal" href="#" coords="370,172, 367,172, 367,173, 364,173, 364,173, 364,173, 364,172, 364,172, 364,171, 365,171, 365,171, 366,171, 366,171, 366,171, 366,171, 367,171, 367,170, 367,170, 367,170, 367,170, 368,171, 368,171, 368,171, 368,171, 368,171, 368,171, 369,171, 369,171, 370,171, 370,171, 370,171, 370,170,369,170, 369,170, 369,171, 368,171, 368,170, 368,170, 368,170, 368,170, 368,170, 367,170, 367,170, 367,170, 367,170, 367,170, 366,170, 366,170, 366,170, 366,170, 364,170, 364,170, 364,170, 364,169, 363,168, 363,168, 362,168, 363,166, 364,165, 364,164, 365,163, 368,163, 369,163, 370,164, 371,164, 371,164, 373,167, 374,168,374,168, 374,168, 374,171, 375,171, 375,171, 375,171, 375,173, 375,173, 375,173, 373,173, 372,172, 370,172, 370,172, 370,172"/>
                        <area shape="poly" title="Mali" alt="Mali" href="#" coords="390,145, 403,153, 403,154, 403,154, 403,154, 405,156, 405,156, 406,155, 406,156, 407,156, 407,156, 408,156, 408,157, 408,157, 408,158, 408,158, 410,158, 410,165, 409,166, 408,166, 404,167, 403,167, 400,167, 399,167, 397,168, 396,169, 396,169, 395,169, 395,169, 393,171, 393,171, 392,171,392,171, 391,171, 391,173, 390,174, 389,174, 389,174, 389,174, 389,176, 389,177, 388,178, 387,178, 387,177, 387,177, 387,177, 387,177, 385,178, 384,177, 384,177, 383,178, 383,177, 382,176, 381,174, 381,173, 381,173, 381,173, 379,174, 378,173, 377,174, 376,173, 375,173, 375,173, 375,171, 375,171, 375,171, 374,171, 374,168,374,168, 375,167, 375,167, 375,166, 375,166, 376,166, 376,166, 376,167, 377,167, 377,167, 377,167, 377,166, 377,166, 379,166, 380,166, 389,166, 389,164, 388,164, 386,145, 390,145, 390,145, 390,145"/>
                        <area shape="poly" title="Niger" alt="Niger" href="#" coords="406,173, 405,172, 404,172, 404,172, 403,172, 403,171, 403,171, 403,171, 402,169, 401,168, 401,168, 401,167, 403,167, 404,167, 408,166, 409,166, 410,165, 410,158, 414,157, 427,148, 431,149, 432,150, 434,149, 435,153, 436,155, 436,155, 435,163, 431,168, 431,170, 430,170, 428,171, 428,171,425,171, 423,171, 422,172, 421,172, 420,172, 419,171, 418,171, 417,172, 416,172, 416,171, 415,170, 413,170, 411,170, 410,170, 409,173, 409,174, 407,173, 406,173, 406,173, 406,173"/>
                        <area shape="poly" title="Chad" alt="Chad" href="#" coords="432,171, 431,170, 431,168, 435,163, 436,156, 436,155, 435,154, 434,149, 436,149, 436,148, 454,157, 454,166, 452,166, 452,166, 451,167, 451,168, 451,168, 451,169, 451,169, 450,170, 450,170, 450,170, 450,171, 449,172, 449,172, 450,172, 450,173, 451,174, 451,175, 451,175, 452,175, 452,176,451,176, 451,176, 451,176, 450,176, 449,177, 449,177, 449,178, 446,180, 443,180, 443,181, 443,181, 442,183, 442,183, 440,183, 438,184, 438,184, 438,184, 438,183, 436,184, 435,184, 435,184, 434,181, 432,179, 432,178, 433,178, 435,179, 435,178, 434,177, 434,176, 434,175, 434,174, 433,172, 432,171, 432,171, 432,171"/>
                        <area shape="poly" title="Madagascar" alt="Madagascar" href="#" coords="500,237, 501,236, 501,236, 502,236, 502,236, 502,236, 503,236, 504,236, 504,236, 504,236, 504,235, 505,235, 505,235, 506,235, 505,234, 506,233, 506,234, 507,234, 507,233, 507,233, 507,232, 507,232, 507,232, 508,232, 507,231, 507,231, 508,231, 508,231, 508,231, 508,231, 508,231, 509,230,509,230, 510,229, 510,228, 510,228, 510,228, 511,229, 511,229, 512,229, 513,234, 513,235, 512,236, 512,236, 511,235, 511,235, 511,235, 511,236, 512,237, 512,237, 511,239, 511,240, 506,255, 505,256, 505,256, 503,257, 502,257, 501,257, 501,257, 501,257, 500,257, 500,257, 500,257, 499,256, 499,256, 499,255, 498,255, 498,255,498,254, 498,254, 498,252, 497,250, 497,248, 498,248, 498,248, 500,245, 500,244, 500,243, 499,243, 499,242, 499,241, 499,239, 499,239, 500,238, 500,237, 500,237, 500,237, 500,237"/>
                        <area shape="poly" title="Djibouti" alt="Djibouti" href="#" coords="496,176, 495,176, 494,176, 494,175, 495,173, 495,173, 496,173, 496,172, 497,173, 497,174, 495,175, 496,175, 497,175, 497,175, 496,176, 496,176, 496,176"/>
                        <area shape="poly" title="Eritrea" alt="Eritrea" href="#" coords="495,173, 495,173, 494,172, 494,172, 493,171, 492,170, 492,170, 491,169, 490,169, 490,168, 489,168, 489,168, 489,168, 488,168, 487,168, 487,168, 487,168, 487,168, 486,168, 485,168, 485,168, 485,168, 484,169, 483,168, 483,168, 483,169, 482,169, 482,169, 482,167, 483,163, 484,163, 484,163,484,162, 485,162, 485,162, 486,161, 486,161, 487,160, 487,160, 489,166, 489,167, 489,167, 489,166, 489,166, 491,167, 491,167, 494,170, 494,170, 495,171, 495,171, 495,171, 496,171, 496,172, 496,173, 495,173, 495,173, 495,173, 495,173, 495,173"/>
                        <area shape="poly" title="Somalia" alt="Somalia" href="#" coords="493,205, 492,203, 492,194, 494,191, 496,191, 497,190, 498,190, 501,190, 507,183, 507,183, 505,183, 498,180, 496,177, 496,177, 497,175, 497,175, 499,177, 501,177, 503,176, 504,177, 505,177, 506,176, 507,176, 509,175, 510,175, 512,175, 513,174, 515,174, 515,175, 514,175, 514,177, 514,177,514,178, 514,179, 512,182, 512,183, 511,183, 512,183, 510,186, 510,187, 493,204, 493,204, 493,205, 493,205, 493,205"/>
                        <area shape="poly" title="Ethiopia" alt="Ethiopia" href="#" coords="496,176, 496,177, 496,177, 498,180, 505,183, 507,183, 507,183, 501,190, 497,190, 496,191, 496,191, 494,191, 494,192, 492,192, 492,191, 491,191, 490,192, 489,193, 485,193, 482,191, 480,190, 480,190, 480,189, 480,189, 480,189, 479,188, 476,184, 474,183, 474,183, 474,182, 476,182, 476,182,477,177, 477,177, 478,177, 478,177, 478,177, 478,176, 479,174, 480,173, 480,172, 481,172, 482,169, 482,169, 483,169, 483,168, 483,168, 484,169, 485,168, 485,168, 485,168, 486,168, 487,168, 487,168, 487,168, 487,168, 488,168, 489,168, 489,168, 489,168, 490,168, 490,169, 491,169, 492,170, 492,170, 493,171, 494,172, 494,172,495,173, 495,173, 494,175, 494,176, 495,176, 496,176, 496,176, 496,176"/>
                        <area shape="poly" title="Sudan" alt="Sudan" href="#" coords="476,191, 476,192, 475,192, 475,192, 474,192, 471,193, 470,192, 469,193, 467,190, 467,190, 466,190, 466,191, 466,191, 465,191, 465,191, 464,191, 464,191, 461,189, 461,189, 461,188, 461,188, 461,187, 460,187, 459,187, 459,187, 459,186, 459,185, 457,184, 457,184, 457,183, 457,183, 455,182,455,182, 454,181, 454,181, 453,181, 453,181, 453,180, 453,179, 453,178, 451,176, 452,176, 452,175, 451,175, 451,175, 450,172, 450,172, 449,172, 449,172, 450,171, 450,170, 450,170, 450,170, 451,169, 451,169, 451,168, 451,168, 451,167, 452,166, 452,166, 454,166, 454,156, 456,156, 456,156, 456,152, 483,152, 483,153, 484,158,487,160, 486,161, 485,162, 484,162, 484,163, 484,163, 483,163, 481,172, 480,172, 480,173, 479,174, 478,176, 478,177, 478,177, 478,177, 477,177, 477,177, 476,182, 476,182, 474,182, 474,183, 474,183, 476,184, 479,188, 480,189, 480,189, 480,189, 480,190, 480,190, 480,190, 477,190, 476,191, 476,191, 476,191"/>
                        <area shape="poly" title="Egypt" alt="Egypt" href="#" coords="477,131, 478,135, 478,135, 477,139, 477,139, 476,139, 475,137, 475,136, 474,136, 473,134, 473,134, 473,135, 479,146, 480,147, 480,148, 480,148, 480,150, 482,151, 483,152, 456,152, 456,133, 456,132, 456,131, 456,131, 456,131, 457,130, 457,131, 462,131, 463,132, 466,132, 467,131, 470,131,471,131, 471,131, 472,132, 473,131, 473,132, 474,132, 474,131, 474,131, 475,132, 477,131, 477,131, 477,131"/>
                        <area shape="poly" title="Libya" alt="Libya" href="#" coords="456,152, 456,156, 456,156, 454,156, 454,157, 436,148, 436,149, 432,150, 431,149, 427,148, 427,148, 426,147, 426,147, 424,146, 424,146, 424,146, 423,145, 423,144, 422,143, 422,142, 423,142, 423,142, 423,141, 422,140, 423,135, 422,134, 422,133, 423,133, 423,132, 423,132, 423,131, 423,130,426,129, 426,128, 426,127, 429,128, 432,128, 433,128, 434,129, 434,129, 435,129, 435,130, 436,131, 443,133, 443,133, 444,133, 445,132, 445,132, 445,131, 445,130, 445,130, 445,129, 449,128, 451,128, 452,128, 452,128, 452,129, 452,129, 453,129, 454,129, 456,130, 456,130, 456,130, 457,130, 456,131, 456,131, 456,131, 456,132,456,133, 456,152, 456,152, 456,152"/>
                        <area shape="poly" title="Tunisia" alt="Tunisia" href="#" coords="426,127, 426,128, 426,129, 423,130, 423,131, 423,132, 423,132, 423,133, 422,133, 421,130, 420,129, 420,129, 419,127, 419,127, 418,127, 418,125, 418,125, 419,124, 419,123, 419,119, 420,119, 420,118, 422,118, 422,118, 423,118, 423,118, 424,119, 424,119, 425,118, 425,118, 425,119, 424,120,424,120, 424,120, 424,121, 425,121, 425,121, 426,122, 425,123, 423,124, 423,125, 424,126, 424,126, 425,126, 425,126, 425,126, 426,127, 426,127, 426,127"/>

                        <area shape="poly" title="Morocco" alt="Morocco" href="#" coords="396,123, 396,123, 397,123, 397,124, 397,127, 397,127, 398,128, 398,128, 398,128, 398,129, 395,129, 394,130, 394,130, 393,130, 392,130, 392,131, 392,131, 393,132, 392,132, 390,133, 389,133, 388,134, 387,134, 387,135, 385,135, 382,137, 382,139, 372,139, 372,139, 372,139, 376,137, 379,134,379,133, 379,133, 379,132, 379,131, 379,131, 380,129, 380,129, 380,128, 380,128, 382,126, 385,125, 388,121, 388,121, 389,122, 391,122, 393,122, 394,122, 394,122, 396,123, 396,123"/>
                        <area shape="poly" title="Algeria" alt="Algeria" href="#" coords="382,140, 382,137, 385,135, 387,135, 387,134, 388,134, 389,133, 390,133, 392,132, 393,132, 392,131, 392,131, 392,130, 393,130, 394,130, 394,130, 395,129, 398,129, 398,128, 398,128, 398,128, 397,127, 397,127, 397,124, 397,123, 396,123, 396,123, 397,122, 397,122, 398,122, 398,121, 400,121,400,121, 401,121, 401,121, 401,120, 403,120, 406,119, 407,119, 408,119, 408,119, 409,119, 412,119, 414,119, 415,118, 416,119, 417,118, 418,118, 418,118, 418,119, 418,119, 420,118, 420,119, 419,119, 419,123, 419,124, 418,125, 418,125, 418,127, 419,127, 419,127, 420,129, 420,129, 421,130, 422,133, 422,133, 422,134, 422,135,423,139, 422,140, 423,141, 423,142, 423,142, 422,142, 422,143, 423,144, 423,145, 424,146, 424,146, 424,146, 426,147, 426,147, 427,148, 427,148, 414,157, 408,158, 408,158, 408,157, 408,157, 408,156, 407,156, 407,156, 406,156, 406,155, 405,156, 405,156, 403,154, 403,154, 403,154, 403,153, 382,140, 382,140, 382,140"/>
                        <!-- middle east -->
                        <area shape="poly" title="Tajikistan" alt="Tajikistan" href="#" coords="567,118, 566,118, 566,117, 564,118, 564,118, 564,118, 564,117, 563,117, 562,118, 562,118, 561,118, 560,119, 560,119, 560,119, 559,118, 560,116, 559,116, 559,116, 559,116, 558,115, 558,115, 558,115, 558,115, 558,116, 557,116, 557,117, 557,117, 556,117, 556,117, 555,117, 555,117, 555,118,555,118, 554,118, 554,118, 553,118, 552,119, 552,119, 551,118, 552,118, 552,117, 552,116, 553,116, 553,116, 553,115, 552,115, 552,115, 552,114, 552,114, 552,114, 552,114, 551,114, 551,113, 551,113, 551,113, 551,112, 553,113, 553,112, 553,112, 554,112, 554,112, 554,111, 554,111, 554,111, 554,111, 553,111, 555,111, 555,111,555,111, 555,111, 555,110, 555,110, 555,110, 555,110, 556,110, 556,110, 557,110, 558,109, 558,110, 558,110, 557,111, 558,111, 558,111, 558,111, 559,111, 558,111, 558,112, 558,112, 558,111, 557,111, 556,111, 555,112, 555,112, 555,112, 555,112, 555,112, 555,112, 555,112, 555,113, 557,113, 557,113, 558,112, 558,113, 558,113,558,113, 559,113, 559,113, 559,113, 560,112, 560,113, 560,113, 560,113, 560,113, 561,113, 561,113, 561,113, 561,113, 562,113, 562,113, 563,113, 564,113, 565,113, 565,113, 565,114, 565,114, 565,114, 565,115, 565,115, 566,115, 567,115, 567,115, 567,115, 567,116, 567,116, 567,116, 567,116, 567,117, 567,117, 567,117, 567,117,567,118, 567,118, 567,118, 567,118"/>
                        <area shape="poly" title="Turkmenistan" alt="Turkmenistan" href="#" coords="537,121, 537,121, 537,119, 536,119, 535,119, 531,117, 528,116, 528,116, 526,115, 526,116, 524,116, 522,117, 521,117, 521,117, 521,116, 521,116, 521,115, 521,114, 521,114, 520,113, 520,113, 520,113, 519,113, 519,113, 519,113, 519,113, 519,113, 520,113, 520,113, 520,113, 520,112, 520,112,520,112, 520,112, 519,112, 519,112, 519,112, 518,111, 518,111, 518,110, 519,109, 519,110, 520,110, 520,110, 520,110, 520,110, 520,110, 520,110, 520,110, 521,110, 522,110, 522,110, 522,110, 522,110, 522,110, 522,110, 522,110, 523,110, 523,109, 522,109, 521,108, 521,108, 521,107, 521,107, 521,107, 519,107, 519,107, 518,108,519,108, 518,109, 518,109, 518,108, 518,108, 518,108, 518,108, 518,108, 519,107, 521,106, 523,107, 524,109, 524,109, 528,109, 528,108, 528,108, 528,107, 529,107, 529,107, 530,107, 530,106, 530,106, 531,106, 531,106, 530,106, 531,105, 532,106, 532,106, 533,106, 533,106, 534,106, 534,107, 534,107, 535,107, 535,108, 535,108,535,108, 535,108, 535,108, 536,109, 536,109, 537,109, 537,109, 538,109, 539,109, 539,109, 539,110, 539,110, 540,111, 540,111, 540,111, 542,113, 543,113, 544,114, 544,114, 547,115, 548,115, 549,116, 549,116, 549,116, 549,118, 547,117, 547,117, 546,118, 545,118, 544,119, 544,120, 544,120, 541,121, 541,121, 541,122, 541,122,540,122, 537,121, 537,121, 537,121, 537,121"/>
                        <area shape="poly" title="Afghanistan" alt="Afghanistan" href="#" coords="536,134, 538,132, 538,131, 536,130, 536,130, 535,127, 535,127, 536,126, 536,126, 535,126, 535,126, 535,125, 535,125, 536,124, 537,121, 540,122, 541,122, 541,122, 541,121, 541,121, 544,120, 544,120, 544,119, 545,118, 546,117, 547,117, 547,117, 551,118, 551,118, 552,119, 552,119, 553,118,554,118, 554,118, 555,118, 555,118, 555,117, 555,117, 556,117, 556,117, 557,117, 557,117, 557,116, 558,116, 558,115, 558,115, 558,115, 558,115, 559,116, 559,116, 559,116, 560,116, 559,118, 560,119, 560,119, 560,119, 561,118, 562,118, 562,118, 563,118, 564,117, 564,118, 564,118, 564,118, 566,117, 566,118, 567,118, 567,118,566,118, 566,118, 566,118, 566,118, 566,118, 566,118, 565,119, 565,119, 565,119, 562,119, 560,120, 560,120, 559,120, 559,121, 560,121, 560,121, 560,121, 560,122, 560,122, 559,124, 559,124, 559,124, 559,125, 559,125, 558,125, 556,125, 556,125, 557,126, 557,126, 557,127, 556,127, 555,127, 555,130, 554,130, 554,130, 553,130,552,130, 552,130, 551,131, 551,131, 550,131, 550,131, 548,132, 548,134, 548,134, 540,135, 536,134, 536,134, 536,134"/>
                        <area shape="poly" title="Pakistan" alt="Pakistan" href="#" coords="552,148, 551,148, 550,147, 550,146, 550,146, 549,145, 549,145, 549,145, 549,145, 549,144, 548,144, 544,145, 544,144, 543,144, 542,144, 542,145, 538,145, 538,143, 538,142, 540,142, 541,141, 541,141, 541,140, 541,140, 541,140, 540,139, 540,138, 540,138, 538,137, 536,134, 539,135, 548,134,548,134, 548,132, 549,131, 550,131, 551,131, 551,131, 552,130, 552,130, 553,130, 554,130, 554,130, 555,130, 555,127, 556,127, 557,127, 557,126, 557,126, 556,125, 556,125, 558,125, 559,125, 559,125, 559,124, 559,124, 559,124, 560,122, 560,122, 560,121, 560,121, 560,121, 559,121, 559,120, 560,120, 560,120, 562,119, 564,119,565,119, 565,119, 565,119, 566,118, 566,118, 566,118, 567,118, 567,118, 567,118, 568,118, 568,119, 568,119, 569,119, 569,119, 569,120, 569,120, 569,120, 570,121, 570,121, 571,121, 571,121, 571,121, 573,122, 573,122, 572,122, 572,123, 572,123, 571,123, 571,123, 570,123, 570,124, 570,124, 569,124, 566,123, 565,123, 565,124,565,124, 565,124, 565,125, 565,125, 565,125, 566,125, 566,125, 565,125, 565,126, 566,126, 565,126, 565,127, 565,127, 566,127, 566,128, 567,128, 567,128, 567,128, 568,128, 568,129, 568,129, 568,129, 566,130, 566,130, 567,130, 566,131, 565,133, 565,133, 565,134, 564,134, 563,136, 562,136, 561,138, 561,138, 560,139, 559,139,558,139, 558,139, 558,138, 557,138, 557,138, 557,139, 556,139, 555,140, 555,141, 555,141, 557,141, 556,143, 557,143, 557,143, 558,143, 559,146, 558,146, 559,146, 558,146, 558,147, 558,147, 558,146, 557,146, 557,146, 556,147, 556,147, 555,147, 554,146, 554,147, 553,147, 552,148, 552,148, 552,148, 552,148"/>
                        <area shape="poly" title="Gorgia" alt="Gorgia" href="#" coords="490,104, 490,104, 490,104, 491,104, 491,104, 491,104, 492,104, 493,104, 493,104, 495,104, 496,105, 496,104, 497,105, 497,105, 497,105, 498,105, 498,106, 498,106, 499,106, 499,106, 499,106, 499,105, 500,105, 500,105, 500,106, 501,105, 501,105, 501,106, 501,106, 502,106, 503,106, 503,106,502,107, 503,107, 504,107, 504,107, 504,107, 504,107, 504,108, 504,108, 505,109, 505,109, 505,109, 504,109, 504,109, 504,109, 503,109, 503,109, 503,109, 502,108, 502,108, 501,109, 497,109, 496,108, 496,108, 495,109, 493,108, 493,108, 493,108, 493,105, 492,105, 490,104, 490,104, 490,104"/>
                        <area shape="poly" title="Azerbaijan" alt="Azerbaijan" href="#" coords="500,112, 501,112, 501,112, 501,112, 501,112, 502,113, 502,113, 503,113, 503,113, 503,113, 503,113, 503,113, 504,114, 504,114, 503,114, 502,114, 502,114, 501,113, 501,113, 500,112, 500,112, 500,112, 500,112"/>
                        <area shape="poly" title="Azerbaijan" alt="Azerbaijan" href="#" coords="509,115, 509,115, 508,115, 508,115, 508,114, 507,114, 508,114, 508,114, 508,113, 508,113, 508,113, 508,113, 508,113, 507,112, 506,113, 505,113, 505,114, 504,114, 504,114, 504,114, 504,113, 504,113, 505,113, 504,113, 504,113, 504,113, 504,112, 503,112, 503,112, 503,112, 503,112, 502,112,502,111, 503,111, 503,111, 503,111, 503,111, 502,111, 502,110, 502,110, 502,110, 502,110, 502,109, 501,109, 501,109, 501,109, 501,109, 502,108, 502,108, 503,109, 503,109, 503,109, 504,109, 504,109, 504,109, 505,109, 505,109, 505,109, 504,108, 504,108, 504,107, 504,107, 504,108, 505,107, 505,108, 506,108, 506,108, 506,108,506,108, 506,109, 507,109, 507,109, 508,108, 509,108, 509,107, 510,109, 510,109, 510,109, 511,110, 511,110, 511,110, 512,110, 512,110, 513,111, 513,111, 513,111, 512,111, 512,111, 512,111, 511,111, 511,111, 511,113, 511,113, 511,113, 511,113, 510,113, 510,114, 510,114, 510,113, 510,113, 510,113, 510,114, 510,114, 510,114,510,114, 509,115, 509,115, 509,115"/>
                        <area shape="poly" title="Armenia" alt="Armenia" href="#" coords="504,114, 504,114, 504,114, 503,113, 503,113, 503,113, 503,113, 503,113, 502,113, 502,113, 501,112, 501,112, 501,112, 501,112, 500,112, 500,112, 498,112, 498,111, 498,111, 498,110, 498,110, 497,109, 501,109, 501,109, 501,109, 501,109, 502,109, 502,110, 502,110, 502,110, 502,110, 502,111,503,111, 503,111, 503,111, 503,111, 502,111, 502,112, 503,112, 503,112, 503,112, 503,112, 504,112, 504,113, 504,113, 504,113, 505,113, 504,113, 504,113, 504,114, 504,114, 504,114, 504,114, 504,114"/>
                        <area shape="poly" title="Iran" alt="Iran" href="#" coords="500,118, 500,118, 500,118, 500,117, 500,117, 500,117, 499,116, 499,116, 500,115, 500,115, 499,115, 499,115, 499,114, 499,114, 499,113, 499,113, 499,113, 499,112, 500,112, 500,112, 500,112, 500,112, 501,113, 501,113, 502,114, 502,114, 503,114, 504,114, 504,114, 505,114, 505,113, 506,113,507,112, 508,113, 508,113, 508,113, 508,113, 508,113, 508,114, 508,114, 507,114, 508,114, 508,115, 508,115, 509,115, 509,115, 509,117, 509,117, 512,118, 512,118, 513,119, 516,119, 520,119, 521,119, 521,118, 521,117, 521,117, 522,117, 524,116, 526,116, 526,115, 528,116, 528,116, 531,117, 535,119, 536,119, 537,119, 537,121,537,121, 536,124, 535,125, 535,125, 535,126, 535,126, 536,126, 536,126, 535,127, 535,127, 536,130, 536,130, 538,131, 538,132, 536,134, 538,137, 540,138, 540,138, 540,139, 541,140, 541,140, 541,140, 541,141, 541,141, 540,142, 538,142, 538,143, 538,145, 536,145, 536,144, 535,144, 532,144, 531,144, 529,144, 529,143, 528,143,528,143, 528,143, 528,143, 528,142, 528,142, 527,142, 527,141, 527,141, 527,141, 527,140, 525,140, 525,140, 524,141, 524,141, 523,141, 522,142, 522,142, 521,141, 520,141, 519,141, 518,140, 517,139, 515,139, 515,138, 513,136, 513,135, 512,134, 511,134, 511,134, 510,134, 510,133, 510,133, 510,133, 510,133, 509,134, 509,134,509,134, 509,134, 508,133, 508,133, 508,132, 508,132, 507,132, 507,131, 507,130, 507,130, 507,130, 504,128, 504,128, 503,128, 503,127, 503,127, 502,125, 502,124, 503,123, 503,122, 503,122, 503,121, 502,121, 500,118, 500,118, 500,118"/>
                        <area shape="poly" title="Turkey" alt="Turkey" href="#" coords="463,107, 463,107, 463,108, 463,108, 464,108, 465,109, 465,109, 465,109, 465,110, 463,109, 463,109, 463,109, 462,109, 461,110, 461,110, 460,111, 460,111, 459,111, 459,111, 459,111, 459,111, 459,111, 459,111, 460,110, 460,110, 459,110, 459,110, 460,108, 460,108, 459,108, 459,108, 460,107,463,107, 463,107, 463,107"/>
                        <area shape="poly" title="Turkey" alt="Turkey" href="#" coords="500,118, 500,118, 499,118, 499,118, 499,118, 499,118, 499,118, 498,118, 498,118, 497,118, 496,117, 496,118, 496,118, 495,118, 495,118, 495,118, 495,118, 493,118, 491,118, 488,119, 486,119, 485,119, 482,119, 482,119, 482,120, 482,120, 482,120, 482,121, 481,121, 481,121, 481,121, 481,121,480,121, 480,120, 480,120, 480,120, 481,119, 481,119, 481,119, 481,119, 481,119, 480,119, 480,119, 480,119, 480,119, 479,119, 479,119, 479,120, 479,120, 478,119, 477,119, 476,120, 476,120, 476,120, 476,120, 476,120, 475,121, 473,120, 473,120, 472,119, 469,119, 469,119, 469,120, 468,120, 468,120, 468,120, 466,120, 466,120,465,119, 465,119, 464,119, 464,119, 463,119, 463,119, 463,119, 463,119, 463,118, 461,118, 461,118, 462,118, 462,118, 462,117, 461,117, 461,117, 461,117, 461,116, 460,116, 460,116, 459,116, 459,116, 460,115, 460,115, 461,115, 460,115, 460,115, 460,115, 460,114, 461,114, 461,114, 461,114, 461,114, 460,114, 460,114, 460,114,460,113, 460,113, 460,113, 460,113, 461,113, 461,113, 460,113, 459,113, 459,113, 459,112, 459,112, 459,112, 459,112, 459,112, 459,111, 460,111, 460,111, 461,111, 461,111, 461,111, 462,111, 462,111, 463,111, 463,111, 462,111, 463,110, 463,110, 463,111, 463,111, 465,111, 466,111, 465,111, 465,111, 465,110, 465,110, 467,110,466,110, 466,110, 465,109, 466,109, 468,109, 468,109, 469,109, 470,109, 471,109, 471,109, 473,107, 478,107, 479,107, 479,108, 479,108, 481,108, 481,109, 482,109, 482,109, 483,109, 484,109, 493,109, 493,108, 495,109, 496,108, 496,108, 498,110, 498,110, 498,111, 498,111, 498,112, 500,112, 500,112, 500,112, 500,112, 499,112,499,113, 499,113, 499,113, 499,114, 499,114, 499,115, 499,115, 500,115, 500,115, 499,116, 499,116, 500,117, 500,117, 500,117, 500,118, 500,118, 500,118, 500,118, 500,118"/>
                        <area shape="poly" title="Jordan" alt="Jordan" href="#" coords="479,136, 479,135, 479,135, 479,135, 479,134, 479,133, 480,131, 480,131, 480,130, 480,128, 480,128, 482,129, 483,129, 487,127, 488,129, 488,129, 487,130, 483,131, 485,133, 484,134, 482,134, 481,136, 479,136, 479,136, 479,136"/>
                        <area shape="poly" title="Israel" alt="Israel" href="#" coords="478,135, 477,131, 477,131, 478,130, 478,128, 478,128, 478,128, 479,127, 479,127, 479,127, 479,127, 479,127, 479,127, 480,127, 480,127, 480,127, 480,127, 480,127, 480,128, 480,128, 480,128, 480,128, 480,128, 480,129, 480,129, 480,130, 480,131, 480,131, 479,133, 479,134, 479,135, 478,135,478,135, 478,135"/>
                        <area shape="poly" title="Lebanon" alt="Lebanon" href="#" coords="480,124, 481,124, 481,124, 481,124, 481,124, 481,124, 481,124, 482,124, 482,125, 482,125, 482,125, 482,125, 481,125, 481,126, 481,126, 480,126, 480,126, 481,126, 481,126, 480,127, 480,127, 480,127, 480,127, 480,127, 479,127, 479,127, 479,127, 479,127, 479,127, 479,127, 479,127, 479,126,479,125, 480,125, 480,125, 480,125, 480,124, 480,124, 481,124, 480,124, 480,124, 480,124"/>

                        <area shape="poly" title="Syria" alt="Syria" href="#" coords="480,128, 480,128, 480,128, 480,127, 480,127, 480,127, 480,127, 480,127, 481,126, 481,126, 480,126, 480,126, 481,126, 481,126, 481,125, 482,125, 482,125, 482,125, 482,125, 482,124, 481,124, 481,124, 481,124, 481,124, 481,124, 481,124, 480,124, 480,123, 480,123, 480,123, 480,122, 480,122,480,122, 480,121, 480,121, 480,121, 480,121, 480,121, 480,121, 481,121, 481,121, 481,121, 481,121, 482,121, 482,120, 482,120, 482,120, 482,119, 482,119, 484,119, 486,119, 488,119, 491,118, 493,118, 495,118, 495,118, 495,118, 495,118, 494,119, 493,119, 493,120, 493,120, 493,120, 493,121, 492,124, 483,129, 482,129, 480,128,480,128, 480,128"/>
                        <area shape="poly" title="Iraq" alt="Iraq" href="#" coords="487,127, 492,124, 493,121, 493,120, 493,120, 493,120, 493,119, 494,119, 495,118, 495,118, 496,118, 496,118, 496,117, 497,118, 498,118, 498,118, 499,118, 499,118, 499,118, 499,118, 499,118, 500,118, 500,118, 502,121, 503,121, 503,122, 503,122, 503,123, 502,124, 502,125, 503,127, 503,127,503,128, 504,128, 504,128, 507,130, 507,130, 507,132, 508,132, 508,132, 508,133, 508,133, 509,134, 509,134, 508,134, 507,134, 507,134, 505,134, 504,136, 500,136, 498,135, 498,135, 498,134, 498,133, 498,133, 497,133, 496,133, 496,132, 488,129, 487,127, 487,127, 487,127"/>
                        <area shape="poly" title="Kuwait" alt="Kuwait" href="#" coords="507,134, 507,135, 507,135, 507,135, 507,136, 507,135, 508,135, 508,137, 507,137, 506,136, 504,136, 505,134, 507,134, 507,134, 507,134"/>
                        <area shape="poly" title="Qatar" alt="Qatar" href="#" coords="515,146, 514,146, 514,146, 514,144, 514,144, 514,144, 514,143, 514,143, 515,143, 515,143, 515,143, 515,143, 515,144, 515,144, 515,145, 515,145, 515,145, 515,146, 515,146, 515,146"/>
                        <area shape="poly" title="United Arab Emirates"" alt="United Arab Emirates"" href="#" coords="514,146, 515,146, 515,146, 515,147, 515,147, 515,147, 515,147, 516,147, 516,147, 517,147, 517,147, 517,147, 517,147, 518,147, 518,147, 519,147, 519,147, 520,147, 521,147, 521,147, 521,147, 521,147, 522,147, 522,146, 522,146, 523,145, 524,144, 524,144, 526,143, 526,143, 525,144, 525,144,525,144, 526,144, 526,144, 526,145, 525,145, 525,145, 525,147, 524,148, 525,151, 525,151, 524,151, 524,151, 516,150, 514,146, 514,146, 514,146"/>
                        <area shape="poly" title="Oman" alt="Oman" href="#" coords="526,145, 526,145, 527,147, 530,148, 531,148, 531,148, 533,150, 534,151, 534,151, 531,155, 531,155, 530,155, 530,155, 529,156, 529,158, 528,158, 527,159, 527,159, 526,160, 526,160, 526,161, 524,161, 524,161, 524,161, 523,162, 523,163, 522,163, 522,163, 519,163, 518,162, 518,162, 516,159,516,158, 525,153, 525,152, 525,151, 524,148, 525,147, 525,145, 525,145, 526,145, 526,145, 526,145"/>
                        <area shape="poly" title="Oman" alt="Oman" href="#" coords="526,143, 526,142, 526,143, 526,144, 525,144, 525,144, 525,144, 526,143, 526,143, 526,143, 526,143"/>
                        <area shape="poly" title="Yemen" alt="Yemen" href="#" coords="519,163, 518,164, 517,165, 517,166, 510,168, 510,169, 509,169, 508,169, 504,171, 503,171, 501,172, 500,172, 500,172, 500,172, 499,172, 498,172, 497,172, 497,171, 497,171, 497,170, 496,166, 496,165, 496,164, 497,164, 497,162, 497,162, 497,162, 500,162, 502,167, 506,162, 506,162, 516,158,516,160, 518,162, 518,162, 519,163, 519,163, 519,163"/>
                        <area shape="poly" title="Saudi Arabia"" alt="Saudi Arabia"" href="#" coords="496,164, 496,163, 491,156, 489,155, 488,153, 488,150, 486,148, 485,147, 484,147, 483,146, 484,145, 479,138, 478,138, 478,138, 478,137, 479,136, 481,136, 482,134, 484,134, 485,133, 483,131, 487,130, 488,129, 496,132, 496,133, 497,133, 498,133, 498,133, 498,134, 498,135, 498,135, 500,136,506,136, 507,137, 508,137, 509,139, 510,139, 510,139, 510,140, 510,140, 511,140, 511,140, 512,141, 512,141, 512,141, 512,141, 512,142, 512,142, 512,143, 512,144, 512,144, 513,144, 513,144, 513,145, 513,146, 514,146, 514,146, 514,146, 514,146, 516,150, 524,151, 524,151, 525,151, 525,152, 525,153, 506,162, 506,162, 502,167,500,162, 497,162, 497,162, 497,162, 497,164, 496,164, 496,164"/>
                        <!-- oceania -->
                        <area shape="poly" title="Papua New Guinea"" alt="Papua New Guinea"" href="#" coords="714,221, 714,217, 714,215, 714,215, 714,207, 722,209, 722,209, 722,210, 725,212, 724,212, 724,213, 725,213, 728,214, 729,215, 729,215, 729,216, 727,216, 727,216, 727,216, 728,218, 730,219, 730,220, 731,221, 731,221, 732,221, 732,221, 732,221, 732,222, 734,222, 734,222, 734,222, 733,223,733,223, 735,223, 735,224, 735,224, 734,224, 734,224, 734,224, 734,224, 729,223, 728,223, 728,222, 728,222, 727,221, 725,218, 722,218, 721,218, 720,217, 720,218, 720,218, 720,219, 720,218, 719,219, 720,219, 720,219, 717,219, 719,220, 719,220, 718,221, 714,221, 714,221, 714,221"/>
                        <area shape="poly" title="Papua New Guinea"" alt="Papua New Guinea"" href="#" coords="733,215, 733,214, 732,214, 732,214, 731,214, 731,214, 730,213, 731,213, 733,213, 734,213, 734,212, 734,213, 735,213, 736,213, 736,213, 736,212, 737,212, 737,212, 738,212, 738,210, 738,210, 739,210, 739,211, 739,211, 739,212, 738,212, 738,212, 738,212, 739,213, 738,213, 738,213, 737,213,737,213, 736,214, 735,214, 733,215, 733,215, 733,215"/>
                        <area shape="poly" title="New Zealand"" alt="New Zealand"" href="#" coords="783,298, 783,298, 783,298, 782,299, 782,299, 782,299, 781,299, 781,299, 781,299, 781,300, 781,300, 780,300, 781,301, 780,303, 779,303, 779,303, 777,304, 776,304, 775,304, 774,304, 773,303, 773,303, 773,303, 772,303, 772,303, 772,303, 771,303, 771,303, 771,303, 771,303, 771,303, 771,302,772,302, 771,302, 772,302, 771,302, 772,302, 773,302, 773,302, 772,301, 772,301, 772,301, 773,300, 774,300, 774,299, 778,298, 778,297, 779,297, 779,297, 779,296, 781,296, 781,296, 781,295, 782,295, 782,294, 782,294, 782,294, 782,294, 783,293, 783,293, 783,292, 784,291, 784,291, 785,291, 785,291, 785,292, 785,292, 786,293,786,293, 787,292, 787,292, 788,292, 788,293, 788,293, 788,294, 788,294, 785,297, 784,297, 784,297, 784,298, 785,298, 785,298, 784,298, 784,298, 783,298, 783,298, 783,298"/>
                        <area shape="poly" title="New Zealand"" alt="New Zealand"" href="#" coords="790,283, 791,284, 791,283, 791,283, 791,283, 791,283, 792,283, 792,284, 795,285, 795,285, 797,285, 797,285, 798,285, 798,285, 797,287, 796,287, 796,287, 796,288, 795,288, 794,288, 794,288, 794,288, 794,289, 794,289, 792,292, 790,293, 790,293, 789,292, 789,292, 790,290, 790,290, 787,288,787,288, 787,288, 789,287, 789,287, 789,286, 789,286, 790,285, 790,285, 789,284, 789,283, 788,282, 788,282, 788,281, 787,281, 787,281, 788,281, 788,281, 787,281, 786,279, 786,279, 785,279, 785,279, 785,278, 784,277, 785,277, 785,277, 785,277, 785,277, 785,277, 785,278, 786,278, 786,278, 786,278, 787,279, 788,279, 788,279,788,279, 788,279, 788,279, 788,279, 789,280, 789,280, 789,280, 789,280, 788,280, 789,281, 789,282, 789,281, 789,282, 790,282, 789,282, 789,282, 790,283, 789,283, 790,283, 790,283, 790,283, 790,283, 790,283, 790,283, 790,283, 790,283"/>
                        <area shape="poly" title="Australia" alt="Australia" href="#" coords="717,225, 718,225, 718,226, 718,227, 718,227, 719,227, 719,227, 719,228, 719,229, 719,229, 720,233, 721,233, 721,233, 721,233, 722,232, 722,233, 723,234, 723,234, 724,237, 725,239, 725,239, 725,240, 725,241, 726,243, 728,244, 728,244, 729,245, 730,245, 731,245, 731,246, 731,246, 731,247,732,247, 733,250, 733,251, 734,251, 734,250, 736,251, 736,251, 736,253, 737,254, 738,254, 738,254, 739,255, 739,256, 739,256, 739,256, 740,257, 740,257, 740,257, 740,257, 741,259, 741,260, 741,261, 742,263, 742,265, 741,269, 741,270, 739,274, 738,274, 737,275, 737,276, 736,278, 736,278, 736,279, 735,280, 734,284, 733,285,730,285, 727,286, 727,287, 726,287, 726,288, 724,286, 724,286, 724,286, 723,285, 723,285, 722,285, 722,285, 722,286, 719,287, 716,286, 715,286, 715,286, 714,286, 712,284, 711,283, 712,282, 711,281, 711,281, 710,280, 710,280, 709,280, 708,280, 708,280, 708,280, 708,279, 709,279, 709,278, 708,277, 707,278, 707,279, 707,279,706,279, 706,279, 705,279, 705,278, 705,278, 706,278, 706,278, 706,278, 706,277, 707,276, 707,275, 707,275, 708,275, 708,274, 708,274, 707,273, 707,274, 706,274, 705,276, 703,277, 702,279, 702,278, 701,276, 700,275, 700,275, 700,274, 699,274, 699,274, 699,274, 699,274, 699,273, 698,272, 698,272, 697,272, 697,272, 696,272,695,272, 695,272, 695,272, 693,271, 677,274, 676,276, 675,276, 672,276, 671,276, 667,276, 667,276, 666,277, 666,277, 665,277, 665,277, 663,278, 662,279, 660,279, 657,277, 657,277, 656,277, 656,276, 656,275, 657,276, 657,275, 658,275, 658,272, 656,265, 654,263, 654,262, 652,259, 652,258, 652,259, 652,259, 653,259, 653,260,654,260, 653,259, 653,258, 653,258, 654,259, 654,259, 654,259, 655,259, 655,258, 653,255, 653,254, 654,253, 654,252, 653,251, 653,251, 654,249, 654,250, 654,250, 654,251, 655,251, 655,251, 655,250, 659,247, 662,247, 662,246, 663,246, 664,246, 665,246, 665,245, 665,245, 666,245, 669,244, 671,243, 671,242, 672,241, 672,241,672,239, 674,237, 674,237, 675,239, 675,240, 675,238, 676,239, 676,238, 676,238, 676,238, 675,238, 675,237, 675,237, 676,237, 677,237, 678,237, 678,237, 677,237, 677,236, 677,236, 677,235, 678,235, 678,235, 679,235, 679,235, 679,235, 679,234, 679,233, 680,233, 680,233, 681,232, 681,232, 682,232, 682,232, 682,232, 682,232,683,231, 683,232, 684,232, 685,233, 685,234, 685,234, 685,235, 686,234, 688,234, 689,234, 689,234, 689,234, 689,233, 689,233, 688,233, 688,232, 688,232, 689,232, 689,231, 690,230, 690,230, 690,230, 690,230, 691,229, 691,229, 692,229, 692,229, 692,228, 692,228, 692,228, 693,228, 696,228, 696,228, 696,227, 696,227, 695,226,695,226, 694,226, 694,226, 694,226, 695,226, 696,226, 696,226, 696,226, 697,227, 699,227, 700,227, 700,227, 701,228, 701,228, 701,228, 702,228, 702,228, 703,228, 703,228, 704,228, 704,227, 704,227, 704,228, 705,228, 705,228, 704,229, 704,229, 704,229, 704,230, 704,230, 704,230, 703,230, 703,230, 703,231, 703,231, 703,232,702,233, 702,233, 702,234, 702,234, 705,236, 705,236, 705,236, 705,236, 708,238, 710,238, 710,238, 710,239, 712,240, 713,240, 714,239, 716,234, 716,234, 715,233, 715,233, 716,232, 715,231, 715,229, 716,229, 716,229, 716,229, 716,228, 716,228, 717,226, 717,225, 717,225, 717,225"/>
                        <area shape="poly" title="Australia" alt="Australia" href="#" coords="727,298, 725,297, 723,295, 724,295, 724,295, 724,295, 723,293, 722,292, 722,291, 723,291, 726,292, 729,292, 729,292, 730,291, 730,292, 730,293, 730,295, 730,296, 729,296, 728,296, 728,297, 727,298, 727,298, 727,298, 727,298"/>
                        <!-- north asia -->

                        <area shape="poly" title="Kyrgyzstan" alt="Kyrgyzstan" href="#" coords="559,111, 559,111, 559,111, 560,111, 560,111, 561,111, 561,111, 561,111, 562,111, 562,110, 563,110, 564,110, 563,110, 562,110, 562,109, 561,109, 561,109, 561,109, 561,108, 560,108, 560,108, 560,109, 560,109, 560,109, 559,109, 558,108, 558,108, 558,108, 558,108, 557,108, 557,108, 558,107,558,107, 558,107, 559,107, 559,106, 559,107, 559,106, 559,105, 561,105, 564,106, 564,106, 565,105, 565,105, 566,104, 569,105, 570,105, 577,105, 577,106, 577,106, 578,106, 579,106, 579,107, 579,107, 579,107, 576,108, 575,109, 574,109, 572,109, 571,111, 569,111, 569,111, 569,110, 569,110, 568,110, 565,112, 565,112, 565,113,565,113, 564,113, 563,113, 562,113, 562,113, 561,113, 561,113, 561,113, 561,113, 560,113, 560,113, 560,113, 560,113, 560,112, 559,113, 559,113, 559,113, 558,113, 558,113, 558,113, 558,112, 557,113, 557,113, 555,113, 555,112, 555,112, 555,112, 555,112, 555,112, 555,112, 555,112, 556,111, 557,111, 558,111, 558,112, 558,112,558,111, 559,111, 559,111, 559,111"/>
                        <area shape="poly" title="Uzbekistan" alt="Uzbekistan" href="#" coords="551,118, 551,118, 551,118, 549,118, 549,116, 549,116, 549,116, 548,115, 547,115, 544,114, 544,114, 543,113, 542,113, 540,111, 540,111, 540,111, 539,110, 539,110, 539,109, 539,109, 538,109, 537,109, 537,109, 536,109, 536,109, 535,108, 535,108, 535,108, 535,108, 535,108, 535,107, 534,107,534,107, 534,106, 533,106, 533,106, 532,106, 532,106, 531,105, 530,106, 531,106, 531,106, 530,106, 530,106, 530,107, 529,107, 529,107, 528,107, 528,108, 528,108, 528,109, 525,109, 526,100, 531,99, 539,104, 544,104, 545,103, 546,104, 547,104, 547,105, 548,105, 548,106, 548,106, 548,107, 549,107, 549,109, 552,109, 552,109,552,109, 552,110, 553,110, 553,110, 554,110, 553,110, 554,109, 554,109, 555,108, 557,108, 557,107, 558,107, 558,107, 558,107, 559,106, 559,107, 558,107, 558,107, 558,107, 557,108, 557,108, 558,108, 558,108, 558,108, 558,109, 559,109, 560,109, 560,109, 560,109, 560,108, 560,108, 561,108, 561,109, 561,109, 562,109, 562,109,562,110, 563,110, 564,110, 563,110, 562,110, 562,111, 562,111, 561,111, 561,111, 561,111, 560,111, 560,111, 559,111, 559,111, 559,111, 558,111, 558,111, 558,111, 557,111, 558,110, 558,109, 557,110, 556,110, 556,110, 555,110, 555,110, 555,110, 555,110, 555,111, 555,111, 555,111, 555,111, 553,111, 554,111, 554,111, 554,111,554,111, 554,112, 554,112, 553,112, 553,112, 553,113, 551,112, 551,113, 551,113, 551,113, 551,114, 552,114, 552,114, 552,114, 552,114, 552,115, 552,115, 553,115, 553,116, 553,116, 552,116, 552,117, 552,118, 551,118, 551,118, 551,118"/>
                        <area shape="poly" title="Kasaksthan" alt="Kasaksthan" href="#" coords="579,107, 579,106, 578,106, 577,106, 577,106, 577,105, 570,105, 569,105, 566,104, 565,105, 565,105, 564,106, 564,106, 561,105, 559,105, 559,106, 559,107, 558,107, 558,107, 558,107, 557,107, 557,108, 555,108, 554,109, 554,109, 553,110, 554,110, 553,110, 553,110, 552,110, 552,109, 552,109,552,109, 549,109, 549,107, 548,107, 548,106, 548,106, 548,105, 547,105, 547,104, 546,104, 545,103, 544,104, 539,104, 531,99, 525,100, 525,109, 524,109, 524,108, 523,107, 521,106, 519,107, 518,108, 518,107, 518,107, 517,107, 518,107, 518,106, 518,106, 518,106, 518,106, 518,105, 518,105, 517,105, 517,105, 516,105, 516,104,515,105, 515,104, 515,104, 515,104, 514,102, 513,102, 513,102, 513,101, 513,101, 515,102, 515,101, 515,101, 514,101, 515,100, 515,100, 515,100, 516,100, 516,100, 516,99, 516,100, 519,100, 518,99, 518,99, 519,98, 519,98, 519,97, 519,97, 519,97, 519,97, 519,96, 518,96, 517,96, 517,96, 516,96, 516,96, 515,96, 514,96, 514,96,513,96, 512,97, 511,97, 510,97, 509,97, 509,97, 509,96, 510,97, 510,97, 509,95, 508,95, 508,94, 507,94, 506,94, 506,94, 506,94, 506,94, 506,93, 504,93, 505,92, 506,91, 505,91, 505,91, 505,89, 506,89, 506,88, 507,88, 507,89, 508,90, 509,90, 509,89, 510,89, 509,88, 511,87, 511,87, 514,86, 515,86, 516,86, 517,85, 517,86, 518,86,520,86, 520,87, 522,87, 522,88, 522,88, 523,88, 522,87, 523,87, 524,88, 524,88, 525,88, 525,88, 527,87, 528,87, 529,87, 529,87, 529,87, 531,87, 532,87, 534,88, 534,88, 534,87, 535,87, 535,88, 536,88, 538,87, 538,86, 538,86, 536,86, 536,86, 535,85, 535,85, 535,85, 535,85, 534,85, 536,84, 537,84, 536,83, 537,83, 537,83, 539,83,539,82, 537,82, 537,82, 537,82, 537,81, 537,81, 537,80, 546,80, 546,80, 546,79, 547,79, 552,78, 553,78, 553,78, 554,78, 554,77, 558,78, 559,78, 559,78, 559,79, 559,79, 559,79, 559,80, 559,80, 559,80, 560,80, 560,80, 560,80, 561,80, 561,80, 561,80, 562,80, 562,80, 562,80, 563,80, 563,80, 564,81, 564,81, 564,80, 565,80, 565,81,564,81, 564,81, 564,81, 565,81, 566,82, 572,80, 572,80, 571,80, 571,80, 575,83, 579,88, 580,87, 580,87, 581,87, 581,87, 581,87, 582,87, 582,88, 587,87, 588,88, 589,89, 589,89, 591,90, 593,90, 593,90, 594,90, 594,90, 594,91, 594,91, 595,91, 595,91, 594,91, 594,92, 594,92, 593,93, 592,93, 591,93, 591,94, 591,95, 591,95, 591,96,586,95, 584,99, 585,99, 585,100, 585,100, 584,100, 584,100, 582,100, 579,100, 579,100, 578,101, 578,101, 579,101, 579,101, 579,101, 580,103, 580,104, 580,104, 579,105, 579,107, 579,107, 579,107"/>
                        <area shape="poly" title="Mongolia" alt="Mongolia" href="#" coords="660,89, 658,94, 658,94, 658,94, 661,94, 661,94, 661,94, 662,94, 663,94, 664,94, 666,95, 667,96, 667,96, 667,97, 667,97, 664,97, 661,97, 661,97, 660,97, 660,98, 659,98, 659,99, 658,99, 657,99, 656,99, 654,101, 651,101, 651,100, 650,100, 649,100, 648,101, 648,102, 649,102, 649,103, 650,103,649,103, 649,104, 648,104, 646,105, 644,106, 639,106, 634,108, 634,108, 633,108, 633,108, 629,107, 627,106, 615,105, 614,105, 613,103, 612,102, 603,100, 602,99, 602,99, 602,99, 603,99, 603,97, 601,94, 597,93, 596,92, 596,91, 606,88, 610,88, 611,89, 617,90, 619,89, 619,88, 619,88, 619,88, 618,87, 618,87, 621,85, 628,86, 628,87,628,87, 628,88, 629,88, 631,89, 637,88, 645,91, 652,90, 654,89, 656,89, 657,89, 658,90, 660,89, 660,89, 660,89, 660,89"/>
                        <area shape="poly" title="Russia" alt="Russia" href="#" coords="0,56, 0,47, 11,50, 11,50, 11,51, 12,52, 12,52, 13,53, 14,53, 14,53, 14,53, 14,53, 13,52, 13,51, 18,52, 23,54, 23,54, 22,54, 21,54, 19,54, 19,54, 20,55, 16,54, 17,55, 17,55, 17,55, 18,55, 18,56, 18,56, 16,56, 16,56, 16,57, 16,57, 14,57, 13,57, 11,56, 11,57, 10,56, 10,56, 10,55, 9,55, 8,55,4,55, 4,54, 4,54, 3,54, 2,54, 2,54, 1,56, 0,56, 0,56"/>
                        <area shape="poly" title="Russia" alt="Russia" href="#" coords="518,39, 520,38, 526,38, 526,38, 526,39, 526,39, 525,39, 524,39, 524,39, 524,40, 525,41, 526,42, 528,43, 528,43, 523,44, 522,43, 520,43, 520,43, 521,42, 522,42, 518,41, 517,42, 516,42, 515,41, 515,41, 515,40, 516,40, 516,40, 516,40, 517,40, 519,39, 519,39, 518,39, 518,39, 518,39"/>
                        <area shape="poly" title="Russia" alt="Russia" href="#" coords="527,37, 522,37, 521,37, 523,36, 525,36, 525,35, 524,35, 525,35, 527,34, 526,34, 527,33, 528,33, 528,33, 536,32, 537,31, 547,30, 548,30, 553,29, 554,30, 554,30, 535,33, 535,34, 536,34, 534,35, 532,34, 531,35, 531,35, 532,35, 530,36, 529,36, 528,36, 529,36, 529,36, 527,37, 527,37, 527,37"/>
                        <area shape="poly" title="Russia" alt="Russia" href="#" coords="471,85,471,84,471,84,471,84,471,84,471,83,471,83,471,83,470,83,470,83,471,82,473,82,473,82,473,82,473,81,473,81,473,81,473,81,471,81,472,81,471,80,470,80,470,79,470,79,470,79,470,79,470,79,469,79,469,79,469,78,469,78,469,78,470,78,470,78,469,78,469,77,469,77,469,77,468,76,468,76,467,76,466,77,466,76,466,76,466,76,466,76,465,76,464,76,464,76,464,76,463,76,463,76,463,75,463,75,463,75,463,75,463,75,463,74,463,74,463,74,462,74,462,74,462,74,462,74,462,74,463,73,463,73,462,73,462,73,462,73,461,73,462,72,462,72,462,72,462,72,463,72,462,72,462,71,462,71,462,71,462,71,462,70,463,69,463,69,463,69,463,68,463,68,463,68,464,68,464,68,464,68,464,68,464,68,465,67,468,68,464,66,464,66,464,66,464,66,470,61,471,61,470,61,467,59,468,58,468,58,467,56,467,54,465,52,467,50,464,49,465,48,472,45,472,45,473,46,475,46,475,47,476,47,492,50,492,51,492,51,492,52,490,53,472,52,478,54,478,55,477,55,478,57,481,58,481,58,485,59,485,58,485,58,482,57,483,56,490,57,491,57,491,57,489,55,495,53,499,54,499,52,499,51,498,51,499,50,499,49,498,49,498,48,503,48,504,50,501,50,500,51,506,52,508,50,517,48,517,49,517,49,518,48,519,48,521,48,520,49,519,49,519,49,519,49,522,49,522,49,522,49,523,48,533,48,532,48,532,48,534,49,534,48,534,48,536,48,536,47,535,46,536,46,553,49,554,47,552,47,552,47,552,46,549,46,549,45,549,45,551,45,551,44,550,43,549,43,549,43,555,39,562,39,563,39,560,42,562,43,562,44,562,44,562,48,564,48,559,52,560,52,554,52,555,53,567,50,567,49,566,49,566,48,572,48,572,50,572,50,575,50,575,50,573,50,573,49,574,49,574,49,573,48,565,47,564,45,566,44,563,42,567,40,567,39,569,39,568,41,568,42,575,43,575,42,570,41,571,41,574,41,574,40,585,41,585,42,583,42,583,44,583,44,585,43,585,44,585,44,587,44,586,42,586,41,584,40,584,40,581,39,580,39,580,38,580,38,580,37,589,37,589,37,594,36,592,37,596,36,594,35,594,34,614,31,614,31,614,32,623,31,623,31,623,31,626,30,625,30,626,29,635,28,635,29,633,29,639,30,637,31,651,31,651,31,653,31,654,32,652,33,653,33,635,39,646,37,646,37,646,36,646,36,646,36,652,37,653,37,653,38,664,37,664,38,664,38,674,39,675,38,675,37,686,38,686,38,681,40,692,43,695,41,696,41,696,41,708,42,708,42,708,42,708,42,711,42,712,41,711,41,712,40,710,40,710,40,724,39,723,40,725,40,725,41,723,41,723,41,733,40,733,41,731,41,755,44,756,44,755,45,758,46,758,47,758,47,776,47,777,48,780,47,781,47,780,46,779,46,780,45,785,45,785,46,800,47,800,56,795,57,795,56,794,56,794,56,792,56,792,57,792,57,793,57,794,57,794,57,794,58,795,58,796,58,796,58,797,58,797,59,797,59,797,59,799,61,798,61,799,61,799,61,794,61,795,61,779,66,779,67,778,67,777,66,770,68,770,68,770,66,767,67,767,68,767,68,766,68,766,67,765,67,764,68,765,67,764,68,763,68,763,69,764,69,762,69,763,70,760,71,760,72,763,72,763,73,762,74,762,75,763,75,763,76,763,76,762,76,762,76,762,75,762,75,760,78,761,79,761,79,756,81,756,81,756,82,756,82,753,83,753,84,749,87,747,75,749,73,749,72,755,70,756,69,765,64,764,64,765,62,768,62,768,62,763,62,763,62,762,63,762,63,762,64,757,66,756,66,757,65,756,65,756,65,757,63,757,63,755,64,754,63,748,64,748,64,748,65,747,65,747,66,743,68,743,68,745,69,745,69,736,70,736,70,738,69,738,69,731,68,731,69,731,69,725,69,725,69,724,69,724,69,701,79,701,79,701,79,704,79,704,81,706,81,705,82,708,81,708,81,708,80,714,83,714,83,714,83,714,84,714,84,714,84,715,85,696,106,696,105,695,105,695,104,693,105,694,104,691,107,690,106,692,105,692,104,692,104,692,104,692,101,694,100,696,100,700,94,700,93,692,94,691,92,685,90,680,82,667,83,667,84,669,84,669,84,669,85,666,88,666,89,666,89,629,88,628,86,621,85,618,87,619,88,619,89,618,90,611,89,610,88,595,91,595,91,594,91,594,90,594,90,591,90,587,87,582,88,582,87,581,87,581,87,581,87,580,87,579,88,571,80,571,80,572,80,572,80,564,81,565,80,563,80,562,80,561,80,559,80,559,80,559,80,559,79,559,79,559,78,559,78,558,78,554,77,554,78,553,78,553,78,552,78,546,79,546,80,537,80,537,81,537,82,537,82,537,82,539,82,539,83,537,83,536,83,537,84,534,85,535,85,535,85,535,85,536,86,536,86,538,86,538,86,538,87,535,88,535,87,534,87,534,88,532,87,531,87,529,87,529,87,527,87,525,88,522,87,523,88,522,88,522,88,522,87,520,87,520,86,518,86,517,86,516,86,515,86,509,88,509,89,508,90,507,88,507,88,506,89,505,89,505,91,506,91,504,93,506,93,506,94,506,94,506,94,508,94,510,97,510,97,509,97,509,97,510,97,509,98,509,98,509,98,509,98,507,99,507,99,507,99,507,99,507,99,505,101,505,102,506,102,506,102,506,102,506,103,506,104,506,104,507,103,507,103,507,104,507,104,506,105,507,105,507,106,507,106,509,107,509,107,509,107,507,109,506,109,506,108,506,108,506,108,505,107,504,108,504,107,504,107,504,107,502,107,503,106,501,105,501,106,500,105,499,106,498,106,498,105,490,104,490,104,490,104,485,101,484,101,483,100,484,100,485,98,486,98,485,97,488,96,488,95,486,96,486,95,487,94,487,94,489,94,489,93,489,93,489,92,489,92,490,92,490,92,489,92,489,91,490,91,490,91,490,91,490,90,490,90,489,90,489,90,489,90,486,89,486,89,485,90,485,89,484,89,484,89,484,89,484,88,480,89,479,88,480,88,479,87,479,87,479,87,478,87,478,87,477,87,477,86,477,86,477,85,476,84,473,84,473,85,472,85,471,85,471,85,471,85"/>
                        <area shape="poly" title="Russia" alt="Russia" href="#" coords="718,81, 719,83, 719,83, 719,84, 718,85, 718,86, 722,92, 720,91, 719,91, 718,91, 717,94, 718,95, 719,96, 719,96, 720,97, 719,98, 719,97, 718,97, 717,97, 717,97, 716,98, 716,98, 716,97, 716,96, 716,95, 716,95, 716,94, 716,92, 716,92, 717,87, 716,86, 716,85, 715,84, 716,83, 716,83, 716,82,716,82, 717,82, 718,81, 718,81"/>
                        <!-- south east asia -->
                        <area shape="poly" title="Brunai" alt="Brunai" href="#" coords="654,191, 654,190, 655,190, 656,189, 656,189, 656,190, 656,190, 656,190, 657,190, 656,191, 657,191, 657,191, 655,191, 655,192, 655,191, 655,191, 654,191, 654,191, 654,191, 654,191, 654,191"/>
                        <area shape="poly" title="Taiwan" alt="Taiwan" href="#" coords="669,152, 669,152, 669,151, 668,150, 667,149, 667,148, 669,145, 670,145, 670,145, 671,145, 671,145, 671,146, 671,147, 671,147, 670,148, 669,151, 669,152, 669,152, 669,152, 669,152"/>
                        <area shape="poly" title="Philippines" alt="Philippines" href="#" coords="681,186, 681,186, 680,184, 680,184, 679,186, 680,187, 680,188, 679,188, 679,188, 679,188, 679,187, 679,187, 678,187, 678,188, 677,187, 676,186, 676,186, 676,185, 677,184, 676,184, 676,183, 675,183, 675,183, 675,184, 674,184, 674,184, 673,183, 673,183, 673,183, 672,185, 672,185, 672,185,672,185, 672,183, 672,183, 673,183, 673,183, 674,183, 674,182, 674,182, 675,182, 675,182, 676,183, 676,182, 676,182, 677,182, 677,182, 677,181, 678,182, 678,181, 678,181, 679,181, 679,181, 679,180, 679,181, 680,180, 679,179, 680,179, 681,180, 681,181, 681,182, 681,182, 681,182, 681,182, 681,183, 681,183, 682,184, 682,185,681,186, 681,186, 681,186"/>
                        <area shape="poly" title="Philippines" alt="Philippines" href="#" coords="661,182, 661,182, 662,181, 664,178, 664,179, 664,179, 664,180, 663,180, 663,181, 662,181, 662,181, 661,182, 661,182, 661,182"/>
                        <area shape="poly" title="Philippines" alt="Philippines" href="#" coords="674,180, 673,179, 673,179, 673,179, 673,178, 673,178, 674,176, 674,176, 675,176, 675,176, 675,177, 674,179, 674,180, 674,180, 674,180, 674,180, 674,180"/>
                        <area shape="poly" title="Philippines" alt="Philippines" href="#" coords="665,178, 665,178, 665,178, 665,178, 665,177, 666,176, 666,176, 666,176, 666,176, 666,177, 665,178, 665,178, 665,178"/>
                        <area shape="poly" title="Philippines" alt="Philippines" href="#" coords="672,177, 672,174, 673,175, 673,175, 674,175, 674,176, 673,176, 672,177, 672,177, 672,177"/>

                        <area shape="poly" title="Philippines" alt="Philippines" href="#" coords="679,176, 679,175, 679,175, 678,175, 678,175, 678,175, 678,174, 677,173, 677,173, 678,173, 679,173, 679,173, 680,176, 679,176, 679,176, 679,176"/>
                        <area shape="poly" title="Philippines" alt="Philippines" href="#" coords="670,173, 669,173, 669,171, 668,171, 670,171, 670,171, 670,171, 670,173, 670,173, 670,173, 670,173"/>
                        <area shape="poly" title="Philippines" alt="Philippines" href="#" coords="668,168, 667,167, 667,166, 667,165, 667,164, 667,164, 668,165, 668,164, 668,160, 669,159, 669,159, 670,159, 671,160, 672,160, 672,160, 672,159, 672,160, 672,160, 672,160, 672,162, 673,162, 673,162, 672,164, 671,165, 671,165, 670,166, 670,166, 671,169, 671,169, 672,169, 672,170, 673,169,673,169, 673,169, 674,169, 674,170, 674,170, 674,170, 675,169, 676,170, 676,170, 676,170, 675,170, 675,170, 676,172, 676,172, 675,172, 675,171, 673,170, 673,170, 673,170, 673,171, 673,171, 672,170, 671,170, 671,170, 670,170, 670,170, 670,170, 670,170, 669,170, 669,169, 669,168, 670,169, 670,169, 670,169, 670,168, 669,168,669,168, 669,168, 668,168, 668,168, 668,168"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="667,200, 667,199, 668,199, 668,199, 668,199, 669,198, 670,198, 670,198, 671,199, 672,199, 674,199, 674,199, 677,199, 677,198, 678,198, 677,198, 678,197, 678,197, 678,197, 679,197, 679,197, 678,198, 677,200, 675,200, 675,200, 674,200, 668,200, 668,200, 667,201, 667,202, 668,202, 668,203,669,204, 669,204, 670,204, 671,203, 671,203, 671,203, 672,203, 672,203, 674,202, 674,202, 674,202, 675,202, 675,202, 675,203, 674,203, 674,203, 672,204, 671,205, 671,205, 671,205, 670,205, 670,205, 670,205, 672,208, 672,209, 672,209, 673,210, 673,210, 673,210, 672,210, 672,211, 672,211, 671,211, 671,211, 671,211, 671,210,669,209, 669,208, 669,208, 670,208, 670,207, 670,207, 669,207, 668,207, 668,209, 668,210, 668,211, 668,211, 668,212, 668,212, 668,213, 666,213, 666,213, 666,212, 666,212, 666,210, 666,209, 666,209, 666,209, 666,208, 665,209, 665,209, 665,207, 666,205, 666,204, 666,202, 667,202, 667,202, 667,200, 667,200, 667,200"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="662,192, 662,192, 662,192, 662,193, 661,193, 661,193, 661,193, 661,193, 661,194, 662,194, 663,195, 663,196, 662,196, 662,196, 665,199, 664,199, 663,199, 662,199, 662,199, 661,201, 662,202, 662,203, 661,203, 661,203, 659,205, 659,205, 659,206, 659,206, 659,206, 659,206, 659,207, 658,209,655,210, 655,210, 655,209, 655,208, 655,208, 654,208, 654,208, 654,208, 653,208, 653,208, 653,208, 652,208, 652,207, 651,207, 651,208, 651,208, 650,208, 650,208, 649,208, 649,209, 649,207, 648,207, 647,207, 647,207, 646,207, 646,207, 645,207, 645,204, 645,203, 643,202, 644,202, 643,201, 643,201, 643,200, 643,200, 643,199,643,199, 644,196, 644,196, 644,196, 644,197, 646,198, 646,199, 646,199, 647,198, 648,198, 649,199, 650,198, 651,197, 651,197, 653,198, 653,198, 654,198, 655,197, 656,197, 657,195, 656,194, 657,194, 657,194, 658,193, 657,192, 658,192, 658,191, 658,191, 661,191, 662,192, 662,192, 662,192"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="668,224, 665,222, 665,222, 667,221, 668,223, 669,223, 669,223, 668,223, 668,224, 668,224, 668,224"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="675,223, 675,223, 675,222, 676,222, 678,221, 679,220, 683,219, 683,219, 683,220, 683,220, 678,222, 677,223, 675,223, 675,223, 675,223"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="635,216, 634,216, 635,215, 635,216, 636,215, 636,215, 636,215, 636,214, 636,214, 636,214, 636,214, 638,214, 638,214, 638,214, 638,214, 638,214, 639,214, 640,215, 640,215, 640,215, 641,215, 641,215, 641,215, 642,215, 642,216, 643,216, 643,216, 646,216, 646,216, 646,215, 647,215, 647,215,647,215, 648,216, 650,216, 651,217, 651,217, 651,218, 651,218, 652,218, 653,218, 654,218, 654,218, 655,218, 655,218, 654,219, 655,220, 652,219, 652,219, 651,219, 642,218, 642,218, 640,218, 639,217, 637,217, 637,217, 637,217, 638,216, 637,216, 635,216, 635,216, 635,216"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="638,208, 636,207, 636,206, 635,205, 635,205, 635,205, 635,205, 635,204, 635,204, 636,205, 636,204, 636,204, 636,205, 637,206, 638,206, 638,207, 637,207, 638,208, 638,208, 638,208"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="684,200, 684,200, 684,199, 684,199, 684,198, 684,197, 685,196, 685,197, 685,197, 685,198, 685,199, 685,199, 685,198, 685,198, 686,197, 687,197, 687,198, 687,198, 686,199, 686,199, 687,199, 687,200, 687,200, 685,200, 685,200, 685,200, 685,201, 686,203, 686,203, 684,201, 684,200, 684,200,684,200"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="631,200, 631,201, 631,201, 631,202, 630,202, 630,202, 631,203, 632,203, 632,203, 633,205, 633,205, 633,205, 634,205, 633,206, 633,206, 634,206, 635,206, 635,206, 636,207, 636,208, 636,213, 636,214, 636,214, 635,213, 635,213, 635,213, 634,214, 633,213, 633,213, 633,214, 633,214, 628,209,628,208, 625,206, 625,205, 625,205, 622,201, 621,200, 620,197, 620,197, 620,197, 618,195, 617,194, 616,192, 615,192, 613,189, 612,188, 612,188, 613,188, 613,188, 614,188, 614,189, 615,189, 617,189, 618,190, 619,191, 619,191, 619,191, 623,194, 623,194, 623,195, 623,195, 625,196, 625,196, 625,196, 625,196, 625,196, 625,196,626,197, 626,197, 627,197, 627,197, 628,199, 629,199, 630,199, 631,200, 631,200, 631,200, 631,200"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="657,220, 655,219, 655,219, 656,219, 656,219, 657,219, 657,219, 657,220, 657,220, 657,220, 657,220"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="663,219, 662,219, 662,219, 662,219, 663,219, 663,219, 663,219, 663,219, 663,219, 664,219, 664,219, 664,219, 664,220, 664,219, 664,219, 665,219, 665,219, 665,220, 665,220, 665,220, 665,220, 665,220, 665,220, 664,220, 664,220, 664,220, 663,220, 664,220, 663,220, 663,220, 663,220, 661,221,661,221, 661,221, 660,221, 660,221, 660,220, 660,220, 660,220, 661,219, 662,220, 662,220, 663,220, 663,220, 663,220, 663,220, 663,219, 663,219, 663,219"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="674,219, 673,219, 673,220, 672,220, 672,220, 671,220, 670,220, 670,221, 670,221, 669,220, 667,220, 667,220, 667,220, 668,219, 669,219, 670,219, 670,220, 671,220, 671,220, 672,219, 672,220, 673,220, 673,219, 673,219, 674,219, 674,219, 673,219, 673,218, 674,218, 674,219, 674,219, 674,219,674,219, 674,219"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="708,219, 707,219, 708,218, 708,217, 709,217, 709,218, 709,218, 709,219, 708,219, 708,219, 708,219"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="682,209, 682,209, 681,209, 681,208, 681,208, 682,208, 683,208, 683,208, 683,208, 683,208, 683,209, 682,209, 682,209, 682,209"/>
                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="691,209, 691,209, 689,208, 689,208, 689,208, 689,208, 689,208, 688,208, 687,208, 687,208, 686,208, 685,207, 686,207, 687,207, 688,207, 688,207, 689,207, 690,207, 691,208, 691,209, 691,209, 691,209, 691,209"/>

                        <area shape="poly" title="Indonesia" alt="Indonesia" href="#" coords="714,207, 714,215, 714,215, 714,221, 712,219, 709,219, 710,219, 710,218, 710,218, 709,216, 709,216, 709,216, 710,216, 710,216, 709,215, 707,213, 706,212, 700,210, 700,210, 700,209, 700,209, 699,209, 699,209, 698,209, 698,208, 698,208, 698,208, 698,208, 697,209, 696,210, 696,210, 696,209,696,209, 696,209, 696,208, 696,208, 695,207, 694,207, 694,207, 694,207, 695,207, 696,207, 696,207, 697,206, 697,206, 697,207, 698,206, 698,206, 698,206, 698,205, 698,205, 698,206, 695,206, 694,205, 694,205, 694,204, 694,204, 692,204, 692,204, 692,203, 693,203, 695,202, 698,203, 699,204, 699,205, 699,207, 700,207, 700,206,700,207, 700,208, 701,208, 701,208, 702,208, 702,208, 704,206, 705,205, 705,205, 705,205, 707,204, 707,204, 714,207, 714,207, 714,207"/>
                        <area shape="poly" title="Malaysia" alt="Malaysia" href="#" coords="644,196, 644,196, 645,197, 646,197, 647,198, 647,198, 648,198, 647,197, 648,195, 651,194, 652,193, 652,192, 653,192, 654,191, 654,191, 654,191, 654,191, 655,191, 655,191, 655,192, 655,191, 657,191, 657,191, 656,191, 657,190, 656,190, 656,190, 657,190, 657,190, 657,189, 657,189, 657,189,657,188, 658,188, 658,188, 659,186, 660,185, 660,186, 661,186, 661,185, 661,186, 662,186, 662,187, 662,187, 662,188, 663,187, 663,188, 662,188, 664,188, 664,188, 665,189, 665,189, 665,189, 665,189, 664,190, 663,189, 663,190, 663,190, 664,191, 664,191, 662,191, 662,192, 661,191, 658,191, 658,191, 658,192, 657,192, 658,193,657,194, 657,194, 656,194, 657,195, 656,197, 655,197, 654,198, 653,198, 653,198, 651,197, 651,197, 650,198, 649,199, 648,198, 647,198, 646,199, 646,199, 646,198, 644,197, 644,196, 644,196, 644,196"/>
                        <area shape="poly" title="Malaysia" alt="Malaysia" href="#" coords="628,187, 628,187, 628,187, 630,190, 630,194, 632,197, 631,197, 631,197, 626,194, 626,193, 624,192, 624,191, 624,190, 624,190, 624,189, 623,187, 623,186, 624,186, 624,186, 624,187, 625,187, 625,187, 625,187, 625,188, 625,188, 625,188, 626,187, 626,188, 626,188, 627,188, 627,187, 628,187,628,187, 628,187, 628,187"/>
                        <area shape="poly" title="Thailand" alt="Thailand" href="#" coords="628,187, 628,187, 627,187, 627,188, 626,188, 626,188, 626,187, 625,188, 625,188, 625,188, 625,187, 625,187, 625,187, 624,187, 624,186, 624,186, 623,186, 624,186, 623,186, 623,186, 623,186, 622,186, 622,185, 622,185, 622,184, 622,184, 621,184, 621,183, 620,182, 619,182, 619,182, 619,182,619,181, 620,178, 620,178, 620,178, 622,174, 622,174, 621,172, 621,171, 621,170, 620,169, 619,167, 620,166, 620,166, 620,165, 620,165, 620,164, 620,164, 619,162, 618,161, 618,160, 617,159, 618,159, 618,159, 618,157, 618,157, 619,157, 620,157, 621,156, 621,156, 622,155, 623,155, 623,155, 623,155, 624,155, 624,155, 624,157,624,157, 625,157, 625,157, 625,157, 626,158, 625,161, 625,162, 626,162, 627,160, 628,160, 629,161, 630,160, 630,159, 631,160, 632,160, 632,160, 633,162, 633,164, 635,167, 635,168, 634,169, 634,169, 630,169, 628,170, 628,171, 628,172, 629,174, 629,174, 629,174, 629,173, 628,173, 627,172, 627,172, 627,172, 626,173, 625,172,625,172, 625,171, 625,171, 624,170, 623,171, 623,171, 623,172, 623,173, 623,174, 621,178, 621,180, 621,180, 621,180, 622,180, 623,180, 623,180, 623,181, 623,182, 623,181, 624,183, 624,183, 624,184, 624,185, 624,185, 626,185, 626,185, 626,185, 627,186, 628,187, 628,187, 628,187"/>
                        <area shape="poly" title="Cambodia" alt="Cambodia" href="#" coords="634,169, 636,170, 637,170, 637,169, 637,169, 636,169, 636,169, 637,168, 638,168, 638,169, 639,168, 639,170, 640,171, 640,172, 639,173, 636,175, 636,175, 636,176, 637,176, 637,176, 636,176, 635,176, 633,177, 633,178, 632,177, 632,177, 632,177, 631,177, 631,177, 631,177, 631,177, 631,176,631,176, 631,176, 630,176, 630,176, 629,174, 629,174, 628,172, 628,171, 630,169, 634,169, 634,169, 634,169, 634,169"/>
                        <area shape="poly" title="Laos" alt="Laos" href="#" coords="634,169, 635,168, 635,167, 633,164, 633,162, 632,160, 632,160, 631,160, 630,159, 630,160, 629,161, 627,160, 627,160, 626,162, 625,162, 625,161, 626,158, 625,157, 625,157, 625,157, 624,157, 624,157, 624,156, 624,155, 623,155, 625,153, 626,153, 627,153, 627,153, 627,153, 627,152, 627,152,626,151, 626,151, 627,151, 627,150, 629,152, 630,154, 631,155, 631,154, 632,154, 632,154, 632,154, 633,156, 633,156, 633,156, 633,157, 633,157, 632,157, 632,157, 632,157, 632,158, 639,165, 639,165, 639,165, 639,166, 639,166, 640,167, 640,167, 639,168, 638,169, 638,168, 637,168, 636,169, 636,169, 637,169, 637,169, 637,170,636,170, 634,169, 634,169, 634,169"/>
                        <area shape="poly" title="Vietnam" alt="Vietnam" href="#" coords="627,150, 628,150, 629,150, 629,150, 630,151, 630,150, 633,150, 635,149, 635,149, 638,149, 638,150, 638,150, 637,151, 638,151, 638,152, 639,152, 640,153, 641,153, 639,153, 640,154, 639,154, 638,154, 638,154, 638,155, 637,156, 636,156, 635,159, 637,160, 637,161, 640,164, 641,164, 641,165,642,166, 643,167, 644,172, 643,173, 643,174, 643,174, 643,175, 643,175, 642,176, 639,177, 639,177, 639,177, 638,177, 638,177, 638,178, 638,179, 637,179, 637,179, 637,179, 637,180, 636,180, 635,180, 635,181, 634,182, 634,182, 634,179, 634,179, 634,178, 634,178, 633,178, 633,178, 633,177, 635,176, 636,176, 637,176, 637,176,636,176, 636,175, 636,175, 639,173, 640,172, 640,171, 639,170, 640,167, 639,166, 639,166, 639,165, 639,165, 639,165, 632,158, 632,157, 632,157, 632,157, 633,157, 633,157, 633,156, 633,156, 633,156, 632,154, 632,154, 632,154, 631,154, 631,155, 630,154, 629,152, 627,150, 627,150, 627,150"/>
                        <area shape="poly" title="Burma" alt="Burma" href="#" coords="606,151, 607,152, 607,151, 608,151, 608,150, 608,149, 608,149, 608,149, 608,147, 608,147, 610,147, 610,147, 610,147, 611,145, 611,145, 611,144, 611,144, 612,143, 612,143, 612,142, 612,141, 615,140, 616,140, 616,140, 616,139, 616,139, 617,139, 617,138, 617,138, 617,137, 618,137, 618,138,619,138, 619,139, 619,139, 620,139, 620,139, 620,142, 619,143, 618,144, 618,145, 618,147, 618,147, 618,147, 618,147, 619,147, 620,147, 620,147, 621,149, 621,149, 621,149, 622,149, 622,149, 621,151, 621,151, 621,151, 622,151, 623,152, 623,153, 624,153, 624,153, 625,152, 625,153, 625,153, 623,155, 623,155, 622,155, 621,156,621,156, 619,157, 619,157, 618,157, 618,159, 618,159, 618,159, 617,159, 618,160, 618,161, 619,162, 620,164, 620,164, 620,165, 620,165, 620,166, 620,166, 619,167, 620,169, 621,170, 621,171, 621,172, 622,174, 622,174, 620,178, 620,178, 620,178, 619,177, 620,176, 620,175, 620,175, 620,175, 620,174, 620,174, 620,174, 618,164,617,164, 617,164, 617,163, 616,162, 616,162, 616,163, 615,164, 613,165, 613,165, 613,166, 612,165, 612,165, 612,165, 612,165, 612,165, 611,165, 611,165, 611,164, 611,165, 610,165, 610,165, 610,164, 611,162, 611,160, 610,158, 609,158, 608,158, 609,158, 609,158, 609,157, 608,156, 608,156, 608,156, 607,156, 607,155, 607,155,607,155, 607,155, 606,154, 606,154, 606,153, 607,153, 606,151, 606,151"/>
                        <area shape="poly" title="Sri Lanka"" alt="Sri Lanka"" href="#" coords="580,187, 579,187, 579,187, 578,184, 578,182, 578,182, 578,181, 578,181, 578,181, 578,181, 579,180, 579,180, 579,179, 579,179, 580,180, 581,181, 581,182, 581,182, 582,182, 583,185, 582,186, 581,187, 580,187, 580,187, 580,187, 580,187"/>
                        <area shape="poly" title="Bangladesh" alt="Bangladesh" href="#" coords="606,154, 604,150, 604,150, 603,150, 603,150, 603,150, 603,150, 602,149, 602,149, 602,150, 602,150, 602,151, 602,151, 602,151, 602,152, 601,152, 601,152, 601,151, 600,151, 600,152, 600,152, 600,152, 600,152, 599,152, 599,152, 598,148, 598,148, 597,148, 598,147, 598,147, 598,146, 597,146,596,146, 597,145, 598,145, 598,144, 596,143, 596,143, 596,143, 597,142, 599,142, 599,143, 600,143, 600,144, 601,144, 606,145, 606,145, 606,145, 606,146, 606,146, 605,147, 603,147, 603,148, 603,148, 604,149, 605,148, 606,148, 607,153, 606,153, 606,154, 606,154"/>
                        <area shape="poly" title="Buthan" alt="Buthan" href="#" coords="598,140, 600,138, 601,138, 601,138, 602,138, 604,138, 604,138, 604,139, 604,139, 604,139, 605,140, 605,140, 605,140, 605,140, 605,141, 602,141, 601,141, 601,141, 600,141, 598,141, 598,140, 598,140, 598,140, 598,140"/>
                        <area shape="poly" title="Nepal" alt="Nepal" href="#" coords="581,133, 581,134, 581,134, 582,133, 582,133, 582,133, 582,133, 583,133, 583,134, 584,134, 586,135, 586,135, 586,135, 587,136, 587,135, 588,135, 588,135, 588,136, 588,136, 589,137, 590,137, 590,137, 590,137, 590,137, 591,137, 592,138, 592,138, 592,138, 592,138, 593,138, 593,138, 593,138,594,138, 594,138, 595,139, 595,139, 596,139, 596,138, 597,139, 597,139, 596,139, 596,140, 597,141, 597,142, 596,142, 596,142, 595,142, 595,142, 594,142, 594,141, 594,142, 592,141, 592,141, 591,141, 591,141, 591,141, 591,141, 590,141, 590,140, 589,140, 589,140, 589,140, 588,140, 588,139, 587,140, 587,139, 586,139, 586,140,586,140, 586,139, 585,139, 585,139, 584,139, 583,138, 583,138, 582,138, 581,137, 581,137, 580,137, 579,137, 579,136, 579,136, 579,135, 579,135, 579,135, 579,134, 580,134, 581,133, 581,133, 581,133"/>
                        <area shape="poly" title="South Korea"" alt="South Korea"" href="#" coords="682,117, 683,116, 683,115, 685,116, 685,115, 686,115, 688,118, 688,121, 688,121, 688,121, 687,123, 687,123, 686,122, 686,123, 686,123, 685,123, 685,123, 684,123, 683,124, 682,124, 682,124, 681,122, 681,122, 682,121, 682,121, 682,121, 682,120, 682,120, 682,119, 681,119, 682,118, 682,119,683,118, 683,118, 682,117, 682,117, 682,117, 682,117, 682,117"/>
                        <area shape="poly" title="North Korea"" alt="North Korea"" href="#" coords="686,115, 685,115, 685,116, 683,115, 683,116, 682,117, 681,117, 681,117, 680,116, 680,116, 679,117, 679,117, 678,116, 678,116, 678,115, 678,115, 679,115, 679,114, 679,114, 679,113, 679,113, 678,112, 678,112, 678,113, 677,113, 677,112, 677,112, 677,111, 681,109, 682,108, 682,108, 683,108,683,108, 684,109, 685,108, 685,107, 685,107, 685,107, 687,107, 688,106, 689,105, 689,105, 689,105, 690,105, 691,106, 689,108, 689,108, 689,109, 689,110, 688,110, 686,112, 685,112, 685,112, 684,112, 684,113, 686,115, 686,115, 686,115"/>
                        <area shape="poly" title="Japan" alt="Japan" href="#" coords="712,109, 712,108, 712,108, 712,108, 712,107, 712,107, 711,107, 711,106, 713,105, 713,105, 713,104, 713,104, 713,104, 714,105, 714,105, 715,104, 715,103, 716,103, 716,101, 715,101, 715,100, 715,100, 716,99, 720,103, 722,103, 722,103, 723,102, 724,102, 724,102, 723,103, 724,105, 723,105,722,105, 722,105, 721,105, 720,105, 719,107, 719,107, 719,107, 716,106, 715,106, 714,107, 714,106, 713,106, 713,106, 712,106, 712,107, 714,107, 714,108, 714,108, 714,108, 713,108, 713,108, 712,109, 712,109, 712,109, 712,109"/>
                        <area shape="poly" title="Japan" alt="Japan" href="#" coords="691,132, 691,132, 691,132, 691,130, 691,130, 691,131, 691,131, 690,131, 690,131, 690,131, 690,131, 690,130, 690,129, 691,129, 691,129, 691,128, 690,127, 690,127, 689,128, 689,128, 689,127, 688,127, 688,127, 689,126, 689,127, 690,126, 691,126, 692,126, 692,126, 692,126, 693,126, 693,126,693,126, 693,127, 694,128, 694,128, 693,129, 693,130, 692,131, 692,131, 691,132, 691,132, 691,132"/>
                        <area shape="poly" title="Japan" alt="Japan" href="#" coords="696,128, 695,128, 695,128, 695,127, 694,126, 695,126, 696,125, 696,125, 697,125, 697,125, 698,124, 699,124, 700,125, 700,125, 700,125, 700,126, 699,127, 698,126, 697,126, 697,126, 697,127, 696,128, 696,128, 696,128, 696,128"/>

                        <area shape="poly" title="Japan" alt="Japan" href="#" coords="705,118, 706,117, 706,118, 705,118, 705,119, 705,119, 706,119, 709,117, 709,117, 710,116, 711,116, 711,115, 711,114, 711,114, 712,113, 712,112, 712,112, 711,112, 712,112, 712,111, 712,110, 712,110, 713,109, 713,109, 713,110, 714,110, 714,109, 714,109, 714,109, 714,108, 715,109, 715,110,715,111, 716,112, 716,113, 716,113, 715,115, 715,115, 714,115, 714,116, 714,116, 714,118, 713,119, 713,120, 713,121, 713,121, 714,121, 713,122, 713,122, 713,123, 712,123, 711,123, 712,122, 712,121, 711,122, 711,122, 710,122, 710,123, 710,123, 709,124, 709,124, 709,123, 709,123, 709,123, 708,123, 708,124, 706,124, 705,123,705,123, 704,123, 704,124, 704,124, 705,124, 705,125, 704,125, 703,125, 702,126, 701,126, 701,125, 701,124, 701,124, 701,124, 701,124, 701,124, 701,124, 700,123, 698,124, 698,124, 695,125, 695,124, 695,124, 694,125, 694,125, 693,125, 692,125, 691,125, 691,125, 692,124, 693,124, 696,122, 696,122, 696,122, 701,121, 701,122,702,122, 702,122, 703,121, 703,121, 704,119, 704,118, 705,118, 705,118, 705,118"/>
                        <area shape="poly" title="China" alt="China" href="#" coords="627,150, 627,151, 626,151, 626,151, 627,152, 627,152, 627,153, 627,153, 627,153, 626,153, 625,153, 625,153, 625,152, 624,153, 624,153, 623,153, 623,152, 622,151, 621,151, 621,151, 621,151, 622,149, 622,149, 621,149, 621,149, 621,149, 620,147, 620,147, 619,147, 618,147, 618,147, 618,147,618,147, 618,145, 618,144, 619,143, 620,142, 620,139, 620,139, 619,139, 619,139, 619,138, 618,138, 618,137, 617,137, 617,138, 617,138, 615,137, 615,137, 615,137, 615,136, 614,135, 613,136, 612,136, 611,136, 606,139, 605,139, 604,139, 604,138, 604,138, 602,138, 601,138, 601,138, 600,138, 598,140, 598,139, 598,139, 598,138,598,138, 597,139, 596,138, 596,139, 595,139, 595,139, 594,138, 594,138, 593,138, 593,138, 593,138, 592,138, 592,138, 592,138, 592,138, 591,137, 590,137, 590,137, 590,137, 590,137, 589,137, 588,136, 588,136, 588,135, 588,135, 587,135, 587,136, 586,135, 586,135, 586,135, 584,134, 583,134, 583,133, 582,133, 582,133, 582,133,582,133, 581,134, 581,134, 581,133, 580,133, 580,133, 579,132, 579,132, 578,132, 578,132, 577,131, 576,131, 576,131, 576,130, 576,130, 576,130, 576,129, 575,129, 575,128, 576,128, 576,128, 576,128, 577,128, 578,128, 577,127, 577,127, 577,127, 577,126, 577,126, 576,126, 576,126, 575,125, 576,124, 575,124, 574,124, 574,122,574,122, 573,122, 573,122, 571,121, 571,121, 571,121, 570,121, 570,121, 569,120, 569,120, 569,120, 569,119, 569,119, 568,119, 568,119, 568,118, 567,118, 567,118, 567,118, 566,118, 566,118, 566,118, 566,118, 567,118, 567,118, 567,118, 567,117, 567,117, 567,117, 567,117, 567,116, 567,116, 567,116, 567,116, 567,115, 567,115,567,115, 566,115, 565,115, 565,115, 565,114, 565,114, 565,114, 565,112, 565,112, 568,110, 569,110, 569,110, 569,111, 569,111, 571,111, 572,109, 574,109, 574,109, 576,108, 579,107, 579,107, 579,105, 580,104, 580,104, 580,103, 580,101, 579,101, 579,101, 578,101, 578,101, 579,100, 579,100, 582,100, 584,100, 584,100, 585,100,585,100, 585,99, 584,99, 586,95, 590,96, 591,96, 591,95, 591,95, 591,94, 591,93, 592,93, 593,93, 594,92, 594,92, 594,91, 596,91, 596,92, 597,93, 601,94, 603,97, 603,99, 602,99, 602,99, 602,99, 603,100, 612,102, 613,103, 615,105, 615,105, 627,106, 629,107, 633,108, 633,108, 634,108, 634,108, 639,106, 644,106, 646,105, 648,104,649,104, 649,103, 650,103, 649,103, 649,102, 648,102, 648,101, 649,100, 650,100, 651,100, 651,101, 654,101, 656,99, 658,99, 659,99, 659,99, 659,98, 660,98, 660,97, 661,97, 661,97, 664,97, 667,97, 667,97, 667,96, 667,96, 666,95, 664,94, 663,94, 662,94, 661,94, 661,94, 661,94, 658,94, 658,94, 658,94, 660,89, 662,90, 666,89,666,89, 666,89, 666,88, 666,88, 666,88, 668,85, 669,85, 669,84, 669,84, 669,84, 667,84, 667,83, 669,82, 675,82, 678,82, 680,82, 680,83, 684,90, 685,90, 691,92, 691,92, 692,94, 700,93, 700,93, 700,94, 699,95, 697,99, 696,100, 694,100, 692,101, 692,104, 692,104, 692,104, 692,105, 692,105, 690,106, 690,105, 689,105, 689,105,689,105, 688,106, 687,107, 686,107, 686,107, 685,107, 685,107, 685,107, 685,108, 684,109, 683,108, 683,108, 682,108, 682,108, 681,109, 677,111, 677,112, 677,112, 670,114, 670,114, 670,114, 671,113, 671,113, 670,113, 670,113, 670,113, 672,111, 672,110, 671,110, 670,110, 670,110, 668,111, 666,112, 666,112, 665,113, 665,113,665,113, 664,113, 664,114, 664,114, 663,113, 663,113, 662,113, 662,115, 662,115, 662,115, 663,116, 665,116, 665,116, 665,118, 665,118, 666,118, 667,118, 668,117, 669,117, 671,117, 672,117, 672,118, 673,118, 672,119, 672,119, 669,119, 669,120, 669,120, 668,120, 668,120, 668,120, 668,120, 667,120, 667,121, 667,121, 665,123,665,123, 665,123, 667,125, 668,125, 668,126, 669,127, 669,127, 669,128, 669,128, 670,129, 670,129, 671,129, 671,130, 671,130, 671,130, 670,131, 671,131, 671,132, 671,132, 671,132, 667,134, 668,134, 669,133, 670,133, 670,133, 671,134, 671,134, 671,134, 671,134, 671,135, 670,135, 671,135, 671,135, 671,135, 670,137, 670,137,670,138, 670,138, 670,138, 668,140, 668,140, 667,141, 667,141, 667,141, 666,141, 666,142, 666,143, 666,143, 666,143, 666,144, 665,144, 665,145, 664,146, 664,146, 663,146, 663,146, 663,147, 662,148, 661,148, 660,149, 660,149, 659,149, 659,149, 659,150, 658,150, 658,150, 657,150, 657,150, 656,150, 655,151, 655,150, 654,150,654,151, 653,150, 653,150, 653,150, 653,150, 653,151, 653,151, 652,152, 652,151, 651,152, 651,152, 651,152, 650,152, 649,152, 649,152, 649,153, 648,153, 648,153, 647,153, 646,153, 646,153, 646,154, 645,154, 645,154, 646,155, 646,155, 646,155, 646,155, 645,155, 644,154, 644,154, 645,153, 645,153, 644,152, 644,153, 644,153,643,153, 643,153, 642,152, 642,152, 642,153, 641,153, 641,153, 641,153, 640,153, 639,152, 638,152, 638,151, 637,151, 638,150, 638,150, 638,150, 636,149, 635,149, 635,149, 633,150, 630,150, 630,151, 629,150, 629,150, 628,150, 627,150, 627,150, 627,150"/>
                        <area shape="poly" title="China" alt="China" href="#" coords="644,160, 642,159, 642,158, 642,158, 643,157, 644,157, 644,156, 645,156, 645,156, 647,156, 647,157, 646,158, 646,158, 646,159, 646,159, 644,160, 644,160, 644,160, 644,160"/>
                        <area shape="poly" title="India" alt="India" href="#" coords="606,151, 606,148, 605,148, 604,149, 603,148, 603,148, 603,147, 605,147, 606,146, 606,146, 606,145, 606,145, 606,145, 601,144, 600,144, 600,143, 599,143, 599,142, 597,142, 596,143, 596,143, 596,143, 598,144, 598,145, 597,145, 596,146, 597,146, 598,146, 598,147, 598,147, 597,148, 597,148,598,148, 599,152, 599,152, 599,153, 598,153, 598,153, 598,152, 598,152, 597,152, 597,153, 597,152, 597,152, 596,152, 596,152, 594,153, 594,153, 594,153, 594,154, 593,155, 593,156, 592,156, 591,157, 590,157, 587,160, 587,160, 586,161, 584,162, 584,163, 583,164, 583,164, 582,164, 582,164, 581,164, 581,165, 581,166, 580,165,580,165, 579,166, 579,167, 579,170, 579,170, 579,171, 579,171, 578,175, 578,177, 578,178, 577,178, 577,178, 576,179, 576,179, 577,180, 576,180, 576,180, 575,180, 575,181, 574,182, 574,182, 573,183, 573,183, 573,183, 571,181, 562,156, 563,154, 563,154, 562,153, 562,151, 561,151, 561,152, 561,152, 561,152, 561,153, 561,153,561,153, 559,155, 558,155, 557,154, 554,151, 556,151, 557,150, 557,150, 557,150, 556,150, 554,149, 552,148, 552,148, 552,148, 553,147, 554,147, 554,146, 555,147, 556,147, 556,147, 557,146, 557,146, 558,146, 558,147, 558,147, 558,146, 559,146, 558,146, 559,146, 558,143, 557,143, 557,143, 556,143, 557,141, 555,141, 555,141,555,140, 556,139, 557,139, 557,138, 557,138, 558,138, 558,139, 558,139, 559,139, 560,139, 561,138, 561,138, 562,136, 563,136, 564,134, 565,134, 565,133, 565,133, 566,131, 567,130, 566,130, 566,130, 568,129, 568,129, 568,129, 568,128, 567,128, 567,128, 567,128, 566,128, 566,127, 565,127, 565,127, 565,126, 566,126, 565,126,565,125, 566,125, 566,125, 565,125, 565,125, 565,125, 565,124, 565,124, 565,124, 565,123, 566,123, 569,124, 570,124, 570,124, 570,123, 571,123, 571,123, 572,123, 572,123, 572,122, 574,122, 574,122, 574,124, 575,124, 576,124, 575,125, 576,126, 576,126, 577,126, 577,126, 577,127, 577,127, 577,127, 578,128, 577,128, 576,128,576,128, 576,128, 575,128, 575,129, 576,129, 576,130, 576,130, 576,130, 576,131, 576,131, 577,131, 578,132, 578,132, 579,132, 579,132, 580,133, 580,133, 581,133, 580,134, 579,134, 579,135, 579,135, 579,135, 579,136, 579,136, 579,137, 580,137, 581,137, 581,137, 582,138, 583,138, 583,138, 584,139, 585,139, 585,139, 586,139,586,140, 586,140, 586,139, 587,139, 587,140, 588,139, 588,140, 589,140, 589,140, 589,140, 590,140, 590,141, 590,141, 591,141, 591,141, 591,141, 592,141, 592,141, 594,142, 594,141, 594,142, 595,142, 595,142, 596,142, 596,142, 597,142, 597,141, 596,140, 596,139, 597,139, 597,139, 598,138, 598,138, 598,139, 598,139, 598,140,598,140, 598,141, 600,141, 601,141, 601,141, 605,141, 605,140, 605,140, 605,140, 605,140, 604,139, 604,139, 604,139, 605,139, 606,139, 611,136, 611,136, 612,136, 613,136, 614,135, 615,136, 615,137, 615,137, 615,137, 617,138, 617,138, 617,139, 616,139, 616,139, 616,140, 616,140, 615,140, 612,141, 612,142, 612,143, 612,143,611,144, 611,144, 611,145, 611,145, 610,147, 610,147, 608,147, 608,147, 608,149, 608,149, 608,149, 608,150, 608,151, 607,151, 607,152, 606,151, 606,151, 606,151"/>
                        </map>

                        <h3>Tax by US State</h3>
                        <center>
                        <img class="map" id="usa_image" src="'.plugins_url('/images/usa.png' , __FILE__).'" width="720" height="445" usemap="#usa">
                        <img id="usa_image2" src="'.plugins_url('/images/usa_highlight.png' , __FILE__).'" width="720" height="445" style="display:none;" >
                        </center>
                         <map id="usa_image_map" name="usa">
                            <area href="#" state="NH" full="New Hampshire" shape="rect" coords="512,29,586,44">
                            <area href="#" state="VT" full="Vermont" shape="rect" coords="543,49,586,62">
                            <area href="#" state="MA" full="Massachusetts" shape="rect" coords="515,68,585,80">
                            <area href="#" state="RI" full="Rhode Island" shape="rect" coords="650,149,711,161">
                            <area href="#" state="CT" full="Connecticut" shape="rect" coords="655,167,711,179">
                            <area href="#" state="NJ" full="New Jersey" shape="rect" coords="656,185,711,198">
                            <area href="#" state="DE" full="Delaware" shape="rect" coords="665,204,711,216">
                            <area href="#" state="MD" full="Maryland" shape="rect" coords="667,223,711,235">
                            <area href="#" state="DC" full="District of Columbia" shape="rect" coords="654,239,711,252">
                            <area href="#" state="WV" full="West Virginia" shape="rect" coords="649,257,711,270">
                            <area href="#" state="SC" full="South Carolina" shape="poly" coords="551,314,551,314,548,314,548,312,547,310,545,308,544,308,542,304,540,299,537,299,536,297,535,295,533,293,532,293,530,290,528,289,524,287,524,287,523,284,522,284,520,280,518,280,515,278,513,277,513,276,514,275,515,274,515,272,520,270,526,267,531,266,543,266,545,267,546,270,549,269,559,269,560,269,570,275,577,281,573,284,572,289,571,293,569,294,569,296,567,296,566,299,563,301,562,303,561,304,558,306,556,307,557,309,553,313,551,314">
                            <area href="#" state="HI" full="Hawaii" shape="poly" coords="169,391,170,389,172,388,172,389,170,391,169,391">
                            <area href="#" state="HI" shape="poly" coords="176,389,181,390,182,390,183,387,183,385,180,384,177,386,176,389">
                            <area href="#" state="HI" shape="poly" coords="199,395,201,400,203,399,204,399,205,400,208,400,208,398,206,398,205,395,203,392,199,395,199,395">
                            <area href="#" state="HI" shape="poly" coords="213,402,214,401,218,401,218,401,222,401,222,402,221,404,217,403,213,402">
                            <area href="#" state="HI" shape="poly" coords="217,406,218,409,221,407,221,407,220,405,217,405,217,406">
                            <area href="#" state="HI" shape="poly" coords="222,405,224,403,227,404,230,405,233,407,233,409,231,410,227,411,226,410,222,405">
                            <area href="#" state="HI" shape="poly" coords="234,416,236,415,238,416,243,419,245,421,247,422,248,425,251,428,251,428,248,431,245,431,244,431,242,432,240,435,239,437,237,437,235,435,234,431,235,430,233,426,232,425,232,422,233,422,235,419,236,419,234,418,234,416">
                            <area href="#" state="AK" full="Alaska" shape="poly" coords="114,344,114,405,116,406,118,406,119,405,121,405,121,407,125,413,126,414,128,413,129,413,129,410,131,410,131,409,133,408,135,410,135,412,137,413,137,414,140,416,143,420,145,422,146,425,147,428,151,428,155,430,155,434,156,436,155,438,154,440,152,439,152,437,149,436,149,435,148,436,149,437,149,440,148,440,146,440,145,438,146,440,146,441,146,441,143,437,143,435,141,434,141,430,140,430,140,433,140,433,139,430,138,428,137,427,138,431,138,432,137,431,134,427,133,426,132,424,131,422,130,421,130,419,131,419,131,418,129,419,127,417,125,415,122,413,119,411,119,409,119,407,118,409,116,410,113,409,109,407,105,407,104,407,100,404,98,404,96,400,94,400,92,401,92,404,92,402,93,403,92,406,95,404,95,405,92,408,91,408,91,407,90,406,89,407,87,405,85,407,83,408,81,410,77,410,77,408,80,408,80,407,78,407,79,404,80,402,80,401,80,400,84,398,85,399,86,399,86,398,83,397,80,399,77,401,77,404,75,405,72,406,70,408,70,410,71,410,72,411,70,414,65,417,60,420,59,421,54,422,50,423,52,424,51,425,50,426,49,425,46,425,46,427,45,427,45,425,43,426,41,427,38,426,36,428,34,428,32,428,31,429,29,428,27,428,26,428,25,429,24,428,24,427,26,426,31,426,34,425,35,424,38,423,39,422,41,422,42,424,43,423,44,422,47,421,49,420,50,420,50,420,51,420,52,418,55,416,56,414,58,410,59,410,59,407,58,409,56,409,55,407,54,407,53,408,53,410,53,410,51,406,50,407,50,406,50,405,47,405,45,406,43,406,44,404,44,403,44,401,45,401,46,401,45,399,45,396,45,395,44,396,40,396,38,395,38,392,37,390,37,389,38,389,38,387,39,386,38,386,38,386,37,384,38,380,41,378,43,377,44,374,46,374,48,374,48,376,50,376,53,374,53,374,54,375,56,375,57,374,58,371,58,366,56,367,54,368,53,367,50,366,47,366,44,363,44,360,44,359,43,357,41,355,42,354,47,353,48,353,49,354,50,354,50,353,53,353,54,353,55,353,54,355,54,356,56,357,59,359,61,358,59,355,59,353,59,352,56,350,56,350,56,349,56,346,54,342,52,339,54,338,56,338,58,338,61,338,64,335,65,333,67,332,68,332,71,332,73,330,74,330,74,331,78,331,80,329,80,329,83,330,85,332,84,332,85,333,86,332,89,332,89,335,90,336,95,337,100,340,101,339,105,341,107,341,108,340,111,341,114,344">
                            <area href="#" state="AK" shape="poly" coords="31,365,32,368,32,369,30,368,29,366,28,365,26,365,26,363,27,361,28,363,29,364,31,365">
                            <area href="#" state="AK" shape="poly" coords="29,389,32,389,35,390,35,391,34,393,32,393,29,391,29,389">
                            <area href="#" state="AK" shape="poly" coords="14,378,15,380,16,381,15,382,14,380,14,378,14,378">
                            <area href="#" state="AK" shape="poly" coords="4,431,7,430,9,429,11,429,11,431,13,431,14,429,14,428,16,428,18,430,17,431,14,432,12,431,9,431,6,431,5,432,4,431">
                            <area href="#" state="AK" shape="poly" coords="40,428,41,430,42,428,41,428,40,428">
                            <area href="#" state="AK" shape="poly" coords="42,431,43,428,44,429,44,431,42,431">
                            <area href="#" state="AK" shape="poly" coords="59,429,60,430,61,429,60,428,59,429">
                            <area href="#" state="AK" shape="poly" coords="65,420,66,424,68,425,72,422,75,421,74,419,74,417,73,418,71,418,71,417,73,417,76,416,77,415,74,414,75,413,73,414,70,417,66,419,65,420">
                            <area href="#" state="AK" shape="poly" coords="96,406,98,404,97,403,95,404,96,406">
                            <area href="#" state="FL" full="Florida" shape="poly" coords="548,337,549,343,552,350,556,356,558,361,562,365,565,368,566,370,565,371,565,372,566,377,569,380,571,383,573,387,577,393,578,399,578,407,578,409,578,411,576,413,577,413,576,415,576,417,577,419,575,421,572,422,569,422,569,423,567,424,566,423,565,422,565,421,564,418,562,414,559,413,557,413,556,413,554,410,553,407,551,404,550,404,549,405,548,405,546,401,544,398,542,395,540,392,537,390,539,388,541,384,541,383,538,383,536,383,537,383,539,384,538,387,537,388,536,385,535,381,535,379,536,376,536,369,533,366,533,364,529,363,527,362,526,361,524,359,523,357,521,356,519,353,516,353,514,351,512,351,509,352,509,353,509,354,509,355,507,355,504,357,502,359,499,359,497,360,497,358,495,356,493,356,492,355,486,352,481,350,477,351,473,351,469,353,466,353,466,347,464,346,463,344,463,342,470,341,489,339,494,339,498,339,500,341,501,343,507,343,515,342,530,341,534,341,538,341,538,343,540,344,540,341,539,337,539,336,544,337,548,337">
                            <area href="#" state="FL" shape="poly" coords="557,434,558,433,560,433,560,431,562,430,563,431,564,431,564,431,562,432,559,433,557,434,557,434">
                            <area href="#" state="FL" shape="poly" coords="566,430,567,431,569,429,573,426,576,423,578,419,578,417,578,415,578,415,577,417,576,420,574,425,571,428,568,428,566,430">
                            <area href="#" state="GA" full="Georgia" shape="poly" coords="500,274,497,275,490,275,484,276,484,278,484,279,485,281,487,287,489,295,490,299,491,302,492,308,494,312,495,314,496,317,497,317,497,319,496,323,496,325,496,326,497,329,497,333,497,335,497,336,498,336,498,339,500,341,501,343,507,343,515,342,530,341,534,341,538,341,538,343,540,344,540,341,539,337,539,336,544,337,548,337,547,332,548,325,550,322,549,320,552,315,551,314,551,314,548,314,548,312,547,310,545,308,544,308,542,304,540,299,537,299,536,297,535,295,533,293,532,293,530,290,528,289,524,287,524,287,523,284,522,284,520,280,518,280,515,278,513,277,513,276,514,275,515,274,515,272,514,272,510,273,505,274,500,274">
                            <area href="#" state="AL" full="Alabama" shape="poly" coords="453,353,452,342,450,329,450,318,451,296,451,284,451,279,457,278,476,277,484,276,484,278,484,279,485,281,487,287,489,295,490,299,491,302,492,308,494,312,495,314,496,317,497,317,497,319,496,323,496,325,496,326,497,329,497,333,497,335,497,336,498,336,499,339,494,339,489,339,470,341,463,342,463,344,464,346,466,347,467,353,461,355,460,355,461,353,461,353,459,348,458,348,457,351,456,353,455,353,453,353">
                            <area href="#" state="NC" full="North Carolina" shape="poly" coords="603,231,605,234,607,239,608,241,609,242,608,242,608,243,608,246,606,247,605,248,605,251,602,252,600,251,599,251,598,251,598,251,598,252,599,252,600,253,599,257,602,257,602,259,604,257,605,257,603,260,601,263,600,263,599,263,597,263,593,265,589,269,587,272,585,277,584,278,581,279,577,281,570,275,560,269,559,269,549,269,546,270,545,267,543,266,531,266,526,267,520,270,515,272,514,272,510,273,505,274,500,274,500,271,501,269,503,269,504,266,507,264,509,263,512,260,516,259,516,257,519,254,520,254,523,252,525,252,527,252,527,250,530,248,530,246,530,243,533,244,539,243,550,242,563,239,578,237,591,234,599,232,603,231">
                            <area href="#" state="NC" shape="poly" coords="606,255,608,253,610,251,611,251,611,249,611,245,610,243,609,242,610,242,612,245,612,248,612,251,610,252,608,254,607,255,606,255">
                            <area href="#" state="TN" full="Tennessee" shape="poly" coords="505,247,467,251,456,252,453,252,450,252,450,255,444,255,439,256,431,256,431,260,429,265,428,267,428,270,427,272,424,274,425,276,425,279,423,281,429,281,447,279,451,279,457,278,476,277,484,276,490,275,497,275,500,274,500,271,501,269,503,269,504,266,507,264,509,263,512,260,516,259,516,257,519,254,520,254,523,252,525,252,527,252,527,250,530,248,530,246,530,243,529,243,527,245,521,245,512,246,505,247">
                            <area href="#" state="RI" full="Rhode Island" shape="poly" coords="633,145,633,142,632,139,632,134,635,134,637,134,639,137,641,140,639,142,638,141,638,143,635,144,633,145">
                            <area href="#" state="CT" full="Connecticut" shape="poly" coords="634,145,633,142,632,139,632,134,628,135,612,139,612,141,614,146,614,152,613,154,614,155,617,153,620,151,621,149,622,149,624,149,628,148,634,145">
                            <area href="#" state="MA" full="Massachusetts" shape="poly" coords="653,140,654,140,654,139,655,139,656,140,655,141,652,141,653,140">
                            <area href="#" state="MA" shape="poly" coords="645,141,647,139,648,139,650,140,648,141,647,142,645,141">
                            <area href="#" state="MA" shape="poly" coords="620,125,633,122,635,122,636,119,639,118,641,122,639,125,639,126,641,128,641,128,642,128,644,129,647,134,650,134,651,134,653,132,652,130,650,129,649,129,648,128,649,128,650,128,652,128,653,131,654,132,654,134,651,135,648,137,645,140,644,141,644,140,646,140,646,138,645,136,644,137,643,138,643,140,641,140,639,137,637,134,635,134,632,134,628,135,612,139,611,134,611,127,615,126,620,125">
                            <area href="#" state="ME" full="Maine" shape="poly" coords="669,71,671,72,672,75,672,76,671,80,669,80,667,83,663,86,660,85,659,86,658,87,657,88,659,89,658,89,658,92,656,92,656,90,656,89,655,89,653,87,652,88,653,89,653,90,653,91,653,93,653,95,652,96,650,97,650,98,646,101,644,101,644,100,641,103,642,105,641,106,641,110,640,115,638,114,638,112,635,111,635,109,629,92,626,81,628,81,629,81,629,80,629,75,631,72,632,69,631,68,631,63,632,62,632,60,632,59,632,56,633,52,635,46,637,43,638,43,638,43,638,44,639,45,641,46,642,45,642,44,645,42,647,41,647,41,652,43,653,44,659,65,664,65,665,67,665,70,667,72,668,72,668,71,667,71,669,71">
                            <area href="#" state="ME" shape="poly" coords="654,92,655,92,656,92,656,94,655,95,654,92">
                            <area href="#" state="ME" shape="poly" coords="659,88,660,89,662,87,662,86,660,86,659,88">
                            <area href="#" state="NH" shape="poly" coords="639,118,639,117,640,115,638,114,638,112,635,111,635,109,629,92,626,81,626,81,625,83,624,82,623,81,623,83,622,87,622,91,623,93,623,96,621,99,619,100,619,101,620,102,620,108,619,115,619,118,620,119,620,122,620,124,620,125,633,122,635,122,636,119,639,118">
                            <area href="#" state="VT" shape="poly" coords="611,127,611,123,609,115,608,115,606,113,607,112,606,110,605,107,605,104,605,100,603,95,602,92,622,87,622,91,623,93,623,96,621,99,619,100,619,101,620,102,620,108,619,115,619,118,620,119,620,122,620,124,620,125,615,126,611,127">
                            <area href="#" state="NY" full="New York" shape="poly" coords="600,152,599,152,598,152,596,150,594,146,592,146,590,144,577,147,545,153,539,155,539,149,541,148,542,147,542,146,543,146,545,144,545,143,547,141,548,140,548,140,547,137,545,137,544,133,546,131,549,131,552,129,554,129,559,129,560,130,562,130,563,129,565,128,569,128,570,127,572,125,572,123,574,123,575,122,575,120,575,119,575,118,575,116,575,115,574,115,572,115,572,114,572,112,576,108,577,107,578,105,580,102,582,99,584,98,585,96,587,95,591,95,593,95,597,93,602,92,603,95,605,100,605,104,605,107,606,110,607,112,606,113,608,115,609,115,611,123,611,127,611,134,611,138,612,141,614,146,614,152,613,154,614,155,614,156,612,158,613,158,614,158,614,158,616,155,617,155,618,155,620,155,626,153,628,151,629,150,632,151,629,154,626,155,621,160,620,160,615,161,612,162,611,162,611,160,611,158,611,156,609,155,605,155,603,154,600,152">
                            <area href="#" state="NJ" full="New Jersey" shape="poly" coords="600,152,599,155,599,156,597,158,597,160,598,161,598,163,596,164,597,165,597,166,599,167,600,168,602,170,604,171,604,172,602,174,601,176,599,178,598,179,597,179,597,180,596,182,597,184,599,185,603,188,606,188,606,189,605,190,605,191,606,191,608,190,608,186,611,183,613,179,614,175,613,174,613,167,611,164,611,165,609,165,608,165,609,164,611,163,611,162,611,160,611,158,611,156,609,155,605,155,603,154,600,152">
                            <area href="#" state="PA" full="Pennsylvania" shape="poly" coords="597,179,598,179,599,178,601,176,602,174,604,172,604,171,602,170,600,168,599,167,597,166,597,165,596,164,598,163,598,161,597,160,597,158,599,156,599,155,600,152,599,152,598,152,596,150,594,146,592,146,590,144,577,147,545,153,539,155,539,149,535,153,534,154,531,156,533,170,534,178,537,191,540,191,549,190,576,185,587,182,593,181,594,180,596,179,597,179">
                            <area href="#" state="DE" shape="poly" coords="596,182,597,180,597,179,596,179,594,180,593,182,594,185,596,188,597,196,599,200,602,200,606,199,605,194,604,194,602,192,600,189,599,186,597,185,596,183,596,182">
                            <area href="#" state="MD" shape="poly" coords="606,199,602,200,599,200,597,196,596,188,594,185,593,181,587,182,576,185,549,190,550,194,551,197,551,197,552,196,554,194,556,194,557,192,558,191,559,191,561,191,563,189,565,188,566,188,567,188,569,190,571,191,572,192,575,193,575,195,578,196,580,197,581,196,582,197,581,200,581,201,580,203,580,205,580,206,584,207,587,207,589,208,590,208,591,206,590,205,590,203,588,202,587,198,588,194,588,193,587,191,590,188,591,186,591,187,590,188,590,191,590,192,591,192,591,196,590,197,590,200,590,199,591,197,593,199,591,200,591,203,593,205,596,205,597,205,599,209,600,209,600,212,599,215,599,220,599,222,601,222,602,219,602,217,602,212,605,208,606,203,606,199">
                            <area href="#" state="MD" shape="poly" coords="595,206,596,208,596,209,596,211,596,206,595,206">
                            <area href="#" state="WV" shape="poly" coords="549,190,550,194,551,197,551,197,552,196,554,194,556,194,557,192,558,191,559,191,561,191,563,189,565,188,566,188,567,188,569,190,571,191,572,192,571,195,566,193,563,192,563,196,563,197,562,200,561,200,559,202,559,204,557,204,556,206,555,210,554,210,552,209,551,208,550,208,550,211,548,216,545,224,545,224,545,227,544,228,542,227,540,230,538,229,537,232,530,233,528,234,527,233,525,233,524,230,521,229,520,227,518,224,517,223,515,221,515,221,515,217,516,216,518,216,518,214,518,213,519,209,519,206,521,206,521,207,521,208,523,207,524,206,523,205,523,203,523,202,525,200,526,199,527,199,529,198,531,195,533,193,533,188,533,185,533,182,533,179,533,178,534,177,537,191,540,191,549,190">
                            <area href="#" state="VA" full="Virginia" shape="poly" coords="524,230,525,233,527,233,528,234,530,233,532,232,538,229,540,230,542,227,544,228,545,227,545,224,545,224,548,216,550,211,550,208,551,208,552,209,554,210,555,210,556,206,557,204,559,204,559,202,561,200,562,200,563,197,563,196,563,192,566,193,571,195,572,191,575,193,575,195,578,196,580,197,581,196,582,197,581,200,581,201,580,203,580,205,580,206,584,207,585,208,589,209,590,210,593,210,594,212,593,215,594,215,594,217,596,218,596,220,593,219,593,220,594,221,594,221,596,222,596,224,596,225,595,227,595,227,597,227,599,226,600,226,603,231,599,232,591,234,578,237,563,239,550,242,539,243,533,244,530,243,529,243,527,245,521,245,512,246,505,247,507,246,511,244,514,242,514,241,515,239,518,236,521,233,524,230">
                            <area href="#" state="KY" full="Kentucky" shape="poly" coords="524,230,521,233,518,236,515,239,514,241,514,242,511,244,507,246,505,247,467,251,456,252,453,252,450,252,450,255,444,255,439,256,431,256,432,255,434,254,435,253,435,251,436,249,435,248,435,246,437,245,439,245,440,245,443,246,444,246,444,245,443,242,443,241,445,240,446,239,448,239,447,238,446,236,448,236,449,233,450,232,455,231,458,231,458,233,460,233,461,230,463,230,464,231,465,232,467,231,467,229,469,227,470,227,470,228,473,228,474,227,474,225,476,222,479,220,480,216,482,216,485,215,487,213,486,212,485,211,485,209,488,209,491,209,493,210,494,213,498,213,499,215,500,215,503,214,505,214,506,215,508,213,509,212,510,212,511,214,512,215,515,216,515,221,515,221,517,223,518,224,520,227,521,229,524,230">
                            <area href="#" state="OH" full="Ohio" shape="poly" coords="531,156,526,159,523,161,521,163,518,166,515,167,513,167,509,169,508,169,505,167,501,167,500,166,497,165,494,166,487,167,481,167,482,179,483,188,485,205,485,209,488,209,491,209,493,210,494,213,498,213,499,215,500,215,503,214,505,214,506,215,508,213,509,212,510,212,511,214,512,215,515,217,516,216,518,216,518,214,518,213,519,209,519,206,521,206,521,207,521,208,523,207,524,206,523,205,523,203,523,202,525,200,526,199,527,199,529,198,531,195,533,193,533,188,533,185,533,182,533,179,533,178,534,178,533,170,531,156">
                            <area href="#" state="MI" full="Michigan" shape="poly" coords="422,74,423,73,425,72,428,69,430,68,431,69,427,73,424,74,422,75,422,74">
                            <area href="#" state="MI" shape="poly" coords="484,98,485,99,487,99,488,98,485,96,484,96,483,97,484,98">
                            <area href="#" state="MI" shape="poly" coords="506,143,503,137,502,131,500,128,498,127,497,128,494,129,493,133,491,135,490,136,489,135,490,129,491,127,491,125,493,124,493,116,491,115,491,114,490,113,491,112,491,113,491,111,490,110,489,107,487,107,484,107,480,104,478,104,477,104,476,104,474,103,473,104,470,106,470,108,471,108,473,109,473,110,471,110,470,110,468,111,468,113,468,114,468,118,466,119,465,119,465,116,467,115,467,113,467,113,465,113,464,116,462,117,461,118,461,119,461,119,461,122,459,122,459,122,460,125,459,128,458,131,458,135,458,136,458,137,458,138,458,140,460,145,462,149,463,152,463,156,462,161,460,164,460,166,458,168,457,169,461,169,476,167,481,167,481,167,487,167,494,166,498,165,497,164,497,164,499,161,500,159,500,156,501,155,502,155,502,152,503,149,504,150,504,151,505,151,506,150,506,143">
                            <area href="#" state="MI" shape="poly" coords="410,95,412,95,414,94,416,92,416,92,417,92,421,91,423,89,426,88,426,87,428,85,429,84,430,83,431,81,434,80,438,79,439,80,439,80,436,81,435,83,433,84,433,86,431,88,431,90,431,90,432,89,434,87,436,89,437,89,440,89,440,90,442,92,444,94,446,94,448,93,449,95,450,95,451,94,452,94,453,93,456,91,458,90,463,89,467,89,468,87,470,87,470,92,470,92,472,92,473,92,478,91,479,90,479,90,479,95,482,98,483,98,484,99,483,99,482,99,479,98,478,99,476,99,474,100,473,100,468,99,464,99,464,101,458,101,457,102,455,104,455,105,455,105,453,104,450,106,449,106,449,104,448,104,447,107,446,110,443,116,442,116,441,115,440,107,437,107,437,104,428,103,425,102,419,100,413,99,410,95">
                            <area href="#" state="WY" full="Wyoming" shape="poly" coords="257,119,249,118,226,116,214,114,194,111,179,109,178,117,175,135,171,157,170,164,169,173,174,174,186,176,192,176,207,178,234,181,252,182,255,150,257,132,257,119">
                            <area href="#" state="MT" full="Montana" shape="poly" coords="259,104,260,95,261,77,262,66,263,56,240,53,219,51,197,48,174,44,161,41,137,37,134,52,137,57,135,61,137,64,139,65,142,73,144,75,145,76,147,77,147,78,142,91,142,93,144,95,145,95,148,93,149,92,150,93,149,97,152,106,154,107,155,108,156,110,155,112,156,115,157,116,158,113,161,113,163,115,164,114,167,114,170,116,172,115,173,113,175,113,176,113,176,116,178,117,179,109,194,111,214,114,226,116,249,118,257,119,259,107,259,104">
                            <area href="#" state="ID" full="Idaho" shape="poly" coords="102,143,105,130,108,117,110,114,111,110,110,108,109,108,108,107,108,107,109,104,112,101,113,100,114,99,114,97,115,96,118,92,121,89,121,86,119,84,117,81,118,74,120,62,124,47,126,38,127,35,137,37,134,52,137,57,135,61,137,64,139,65,142,73,144,75,145,76,147,77,147,78,142,91,142,93,144,95,145,95,148,93,149,92,150,93,149,97,152,106,154,107,155,108,156,110,155,112,156,115,157,116,158,113,161,113,163,115,164,114,167,114,170,116,172,115,173,113,175,113,176,113,176,116,178,117,175,135,172,157,168,156,162,155,155,154,146,152,137,151,131,149,124,148,117,146,102,143">
                            <area href="#" state="WA" full="Washington" shape="poly" coords="68,19,71,20,78,22,84,23,98,28,116,32,127,35,126,38,124,47,120,62,118,74,118,81,107,79,96,76,85,76,85,75,81,77,77,77,76,75,75,76,71,75,71,74,67,73,66,73,63,72,62,74,57,73,53,70,53,69,53,64,52,61,49,61,48,59,47,59,45,57,44,58,42,56,42,54,44,53,46,50,44,50,44,47,47,47,45,44,44,40,44,38,44,32,43,29,44,23,47,23,48,25,50,27,53,29,56,30,58,30,60,32,62,32,64,32,64,30,65,29,67,29,67,29,67,31,65,31,65,32,66,33,67,35,68,37,69,36,69,35,68,35,68,32,68,31,68,30,68,29,69,26,68,24,67,20,67,20,68,19">
                            <area href="#" state="WA" shape="poly" coords="61,23,62,23,62,24,64,23,65,23,66,24,65,26,65,26,65,28,64,28,63,26,63,26,62,27,61,26,61,23">
                            <area href="#" state="TX" full="Texas" shape="poly" coords="259,256,275,257,298,258,296,275,296,288,296,289,299,292,301,293,302,293,302,291,303,293,305,293,305,291,307,293,306,295,309,296,311,296,314,296,316,298,317,296,320,297,322,299,323,299,323,301,324,302,326,300,327,300,329,300,329,302,333,304,334,303,335,300,336,300,337,302,340,302,343,303,345,304,347,303,347,301,350,301,351,302,353,300,354,300,355,302,358,302,359,300,360,300,362,302,364,304,366,304,368,305,370,307,372,305,374,306,374,314,374,321,375,329,376,331,377,334,378,338,381,341,381,344,382,344,381,350,379,354,380,356,380,358,380,363,378,365,379,368,374,369,367,372,366,374,364,375,362,376,362,377,358,380,356,382,352,385,347,386,343,389,342,390,338,392,335,393,332,397,329,397,329,398,330,400,329,404,329,407,328,410,327,413,328,415,329,420,329,425,331,426,330,428,328,429,324,426,320,425,319,425,317,425,314,423,310,422,304,419,302,417,302,412,299,411,299,409,299,409,299,406,299,406,298,405,299,402,298,400,296,399,293,396,290,392,287,389,287,388,284,379,283,376,282,374,282,374,278,370,275,368,275,367,274,365,269,365,263,364,261,362,258,364,255,365,254,367,253,370,250,374,248,376,246,375,245,374,243,374,241,372,241,372,239,371,236,369,230,363,229,360,229,354,227,350,226,347,224,347,224,345,221,344,219,342,214,337,213,335,210,332,209,329,207,327,206,327,205,323,211,324,232,326,253,327,254,310,257,270,259,256,260,256">
                            <area href="#" state="TX" shape="poly" coords="332,426,331,421,329,416,329,410,329,404,332,399,335,395,337,393,338,393,334,398,331,403,329,407,329,411,329,416,332,421,332,425,332,425,332,426">
                            <area href="#" state="CA" full="California" shape="poly" coords="99,296,102,295,104,293,104,291,101,291,101,290,101,289,101,285,103,284,105,282,105,278,107,276,108,275,110,273,112,272,112,271,111,270,110,269,110,266,107,262,108,260,106,257,95,241,81,220,65,195,56,182,57,177,62,158,68,135,58,133,48,130,39,127,34,125,26,123,20,122,20,125,19,131,15,139,13,141,13,142,11,143,11,146,10,148,12,151,13,154,14,156,14,161,13,164,12,167,11,170,13,173,14,176,17,180,17,182,17,185,17,185,17,187,21,191,20,194,20,195,20,197,20,203,21,205,23,207,25,207,26,209,24,212,23,213,22,213,22,216,22,218,24,221,26,225,26,228,27,230,30,235,31,236,32,239,32,239,32,241,32,242,31,248,30,249,32,251,35,251,38,253,41,254,43,254,45,257,47,260,48,262,51,263,54,264,56,266,56,268,55,268,55,269,57,269,59,269,62,273,65,276,65,278,67,281,67,283,67,290,68,291,74,292,89,294,99,296">
                            <area href="#" state="CA" shape="poly" coords="35,259,36,260,36,261,34,261,33,260,33,259,35,259">
                            <area href="#" state="CA" shape="poly" coords="37,259,38,258,40,260,42,261,41,261,38,261,37,260,37,259">
                            <area href="#" state="CA" shape="poly" coords="52,273,53,275,53,275,55,276,55,275,54,274,53,272,52,272,52,273">
                            <area href="#" state="CA" shape="poly" coords="50,279,52,282,53,283,52,284,51,282,50,279">
                            <area href="#" state="AZ" full="Arizona" shape="poly" coords="100,296,98,297,98,298,98,299,112,306,120,312,131,318,143,326,152,327,172,330,173,320,176,301,181,262,184,239,165,237,146,233,122,229,119,242,119,242,118,245,116,245,115,242,113,242,113,241,112,241,111,242,110,242,110,248,110,248,109,258,108,260,107,262,110,266,110,269,111,270,112,271,112,272,110,273,108,275,107,276,105,278,105,282,103,284,101,285,101,289,101,290,101,291,104,291,104,293,102,295,100,296">
                            <area href="#" state="NV" full="Nevada" shape="poly" coords="102,143,117,146,124,148,131,149,137,151,136,155,133,167,131,182,129,189,128,199,125,211,123,221,122,230,119,242,119,242,118,245,116,245,115,242,113,242,113,241,112,241,111,242,110,242,110,248,110,248,109,258,108,260,106,257,95,241,81,220,65,195,56,182,57,177,62,158,68,135,92,141,102,143">
                            <area href="#" state="UT" full="Utah" shape="poly" coords="184,240,165,237,146,233,122,229,123,221,125,211,128,199,129,189,131,182,133,167,136,155,137,151,146,152,155,154,162,155,168,156,172,157,170,164,169,173,174,174,186,176,193,176,191,192,188,209,185,229,185,237,184,240">
                            <area href="#" state="CO" full="Colorado" shape="poly" coords="272,248,275,201,276,185,252,182,234,181,207,178,192,176,191,192,188,209,185,229,185,237,184,240,209,242,236,246,260,248,264,248,272,248">
                            <area href="#" state="NM" full="New Mexico" shape="poly" coords="206,327,205,323,211,324,232,326,253,327,254,310,257,270,259,256,260,256,260,248,236,246,209,242,184,240,181,262,176,301,173,320,172,330,183,331,184,324,196,326,206,327">
                            <area href="#" state="OR" full="Oregon" shape="poly" coords="102,143,105,130,108,117,110,114,111,110,110,108,109,108,108,107,108,107,109,104,112,101,113,100,114,99,114,97,115,96,118,92,121,89,121,86,119,84,118,81,107,79,96,76,85,76,85,75,81,77,77,77,76,75,75,76,71,75,71,74,67,73,66,73,63,72,62,74,57,73,53,70,53,69,53,64,52,61,49,61,48,59,47,59,42,60,41,65,38,72,36,77,32,87,28,97,22,106,20,108,20,114,19,119,20,122,26,123,34,125,39,127,48,130,58,133,68,135">
                            <area href="#" state="OR" shape="poly" coords="102,143,68,135,92,141,102,143">
                            <area href="#" state="ND" full="North Dakota" shape="poly" coords="342,107,341,101,341,96,339,86,338,80,338,77,336,73,336,65,337,63,335,59,314,59,300,58,281,57,263,56,262,66,261,77,260,95,259,104,300,107,342,107">
                            <area href="#" state="SD" full="South Dakota" shape="poly" coords="343,162,343,161,341,159,343,155,344,152,341,150,341,148,342,146,344,146,344,141,344,119,343,117,340,115,339,113,339,112,341,111,342,110,342,107,300,107,259,104,259,107,257,119,257,132,255,151,266,152,281,152,294,153,311,154,319,154,320,155,324,158,325,158,328,157,331,157,333,157,334,158,338,158,340,160,341,161,341,163,342,163,343,162">
                            <area href="#" state="NE" full="Nebraska" shape="poly" coords="352,194,353,195,353,197,354,200,356,203,352,203,320,203,291,201,275,200,276,185,252,182,255,151,266,152,281,152,294,153,311,154,319,154,320,155,324,158,325,158,328,157,331,157,333,157,334,158,338,158,340,160,341,161,341,163,342,163,344,162,344,167,347,172,347,176,349,178,349,182,350,185,350,190,352,194">
                            <area href="#" state="IA" full="Iowa" shape="poly" coords="411,161,411,162,413,162,413,163,414,164,417,167,417,169,417,171,416,174,415,176,413,177,412,177,408,179,407,180,407,182,407,182,409,183,409,186,407,188,407,188,407,191,406,191,404,192,404,193,404,194,404,196,401,193,400,191,395,192,387,192,369,193,359,193,353,194,352,194,350,190,350,185,349,182,349,178,347,176,347,172,344,167,344,162,342,161,341,159,343,155,344,152,341,150,341,148,342,146,343,146,352,146,388,146,401,146,404,145,404,148,406,149,406,150,404,152,404,155,407,158,408,158,410,158,411,161">
                            <area href="#" state="MS" full="Mississippipi" shape="poly" coords="453,353,452,354,449,354,448,353,446,353,441,355,440,354,438,357,437,358,437,356,436,353,433,350,434,345,434,344,432,344,426,345,409,346,408,344,409,338,411,334,415,328,414,326,415,326,416,324,414,323,414,321,413,318,413,314,413,312,413,309,412,307,413,305,412,304,413,303,413,299,416,296,415,295,418,291,419,290,419,289,419,287,421,284,423,283,423,281,429,281,447,279,451,279,451,284,451,296,450,318,450,329,452,342,453,353">
                            <area href="#" state="IN" full="Indiana" shape="poly" coords="449,233,448,230,449,227,450,225,452,222,453,219,453,215,452,213,452,211,452,207,452,202,451,190,450,179,449,170,452,171,452,172,453,172,455,170,457,169,461,169,476,167,481,167,481,167,482,179,483,188,485,205,485,209,485,211,486,212,487,213,485,215,482,216,480,216,479,220,476,222,474,225,474,227,473,228,470,228,470,227,469,227,467,229,467,231,465,232,464,231,463,230,461,230,460,233,458,233,458,231,455,231,450,232,449,233">
                            <area href="#" state="IL" full="Illinois" shape="poly" coords="448,233,448,230,449,227,450,225,452,222,453,219,453,215,452,213,452,211,452,207,452,202,451,190,450,179,449,170,449,170,448,168,447,165,446,164,445,162,444,158,437,159,418,161,411,160,411,162,413,162,413,163,414,164,417,167,417,169,417,171,416,174,415,176,413,177,412,177,408,179,407,180,407,182,407,182,409,183,409,186,407,188,407,188,407,191,406,191,404,192,404,193,404,194,404,195,403,197,403,200,404,206,410,211,414,213,413,217,414,218,419,218,421,219,421,221,419,226,419,228,420,231,425,235,428,236,429,239,431,241,431,243,431,246,433,248,435,248,435,246,437,245,439,245,440,245,443,246,444,246,444,245,443,242,443,241,445,240,446,239,448,239,447,238,446,236,448,236,448,233">
                            <area href="#" state="MN" full="Minnesota" shape="poly" coords="342,107,341,101,341,96,339,86,338,80,338,77,336,73,336,65,337,63,335,59,357,59,357,53,358,53,359,53,361,54,362,58,362,62,364,63,367,63,368,65,372,65,372,66,376,66,376,65,377,65,378,64,379,65,381,65,384,67,388,68,389,68,390,68,391,68,392,70,393,71,394,71,395,71,395,72,397,73,399,73,400,72,402,70,404,69,404,71,405,71,406,71,407,71,413,71,414,73,415,73,415,72,419,72,418,74,415,75,408,78,405,80,403,81,401,84,399,86,398,87,395,91,394,91,392,93,392,94,390,95,389,98,389,104,389,105,385,107,383,112,383,112,386,113,386,116,385,119,385,121,385,126,387,128,389,128,391,131,393,131,396,135,401,138,403,140,404,146,401,146,388,146,352,146,343,146,344,141,344,119,343,117,340,115,339,113,339,112,341,111,342,110,342,107">
                            <area href="#" state="WI" full="Wisconsin" shape="poly" coords="444,158,444,155,443,152,443,148,442,146,443,143,443,141,444,140,444,137,443,134,444,134,445,131,446,130,445,128,445,127,446,125,448,120,449,116,449,113,449,113,449,113,446,118,444,121,443,122,442,124,441,125,440,126,439,125,439,125,440,122,441,119,443,118,443,116,442,116,441,115,440,107,437,107,437,104,428,103,425,102,419,100,413,99,410,95,410,96,410,96,409,95,407,95,406,95,404,95,404,95,404,94,406,92,407,91,405,89,404,90,401,92,396,94,394,95,392,94,392,94,390,95,389,98,389,104,389,105,385,107,383,112,383,112,386,113,386,116,385,119,385,121,385,126,387,128,389,128,391,131,393,131,396,135,401,138,403,140,404,145,404,148,406,149,406,150,404,152,404,155,407,158,408,158,410,158,411,161,418,161,437,159,444,158">
                            <area href="#" state="MO" full="Missouri" shape="poly" coords="404,196,401,193,400,191,395,192,387,192,369,193,359,193,353,194,352,194,353,195,353,197,354,200,356,203,359,205,361,205,362,206,362,208,360,209,360,210,362,213,363,215,365,216,366,225,365,251,365,254,366,259,383,258,400,258,415,257,423,257,424,259,424,261,422,263,421,265,425,266,429,265,431,260,431,256,433,255,434,254,435,253,435,251,436,249,435,248,433,248,431,246,431,243,431,241,429,239,428,236,425,235,420,231,419,228,419,226,421,221,421,219,419,218,414,218,413,217,414,213,410,211,404,206,403,200,403,197,404,196">
                            <area href="#" state="AR" full="Arkansas" shape="poly" coords="429,265,425,266,421,265,422,263,424,261,424,259,423,257,415,257,400,258,383,258,366,259,367,264,367,270,368,278,368,305,370,307,372,305,374,306,374,314,391,314,404,314,413,314,413,312,413,309,412,307,413,305,412,304,413,303,413,299,416,296,415,295,418,291,419,290,419,289,419,287,421,284,423,283,423,281,425,280,425,276,424,274,427,272,428,270,428,267,429,265">
                            <area href="#" state="OK" full="Oklahoma" shape="poly" coords="272,248,264,248,260,248,260,248,260,256,275,257,298,258,296,275,296,288,296,289,299,292,301,293,302,293,302,291,303,293,305,293,305,291,307,293,306,295,309,296,311,296,314,296,316,298,317,296,320,297,322,299,323,299,323,301,324,302,326,300,327,300,329,300,329,302,333,304,334,303,335,300,336,300,337,302,340,302,343,303,345,304,347,303,347,301,350,301,351,302,353,300,354,300,355,302,358,302,359,300,360,300,362,302,364,304,366,304,368,305,368,278,367,270,367,264,366,259,365,254,365,251,356,251,322,251,290,249,272,248">
                            <area href="#" state="KS" full="Kansas" shape="poly" coords="365,251,356,251,322,251,290,249,272,248,275,200,291,201,320,203,352,203,356,203,359,205,361,205,362,206,362,208,360,209,360,210,362,213,363,215,365,216,366,225,365,251">
                            <area href="#" state="LA" full="Louisiana" shape="poly" coords="437,357,437,356,436,353,433,350,434,345,434,344,432,344,426,345,409,346,408,344,409,338,411,334,415,328,414,326,415,326,416,324,414,323,414,321,413,318,413,314,404,314,391,314,374,314,374,321,375,329,376,331,377,334,378,338,381,341,381,344,382,344,381,350,379,354,380,356,380,358,380,363,378,365,379,368,382,367,388,366,395,369,400,370,403,369,405,370,407,371,408,369,406,368,404,368,401,367,406,366,407,366,410,366,410,368,410,370,414,370,416,371,415,372,414,373,415,374,421,377,424,376,425,374,426,374,428,372,428,373,429,375,428,376,428,377,431,375,432,373,433,373,431,372,431,371,431,370,433,370,434,369,434,369,440,374,441,374,443,374,444,375,446,373,446,372,445,372,443,370,438,369,436,368,437,366,438,366,439,365,437,365,437,365,440,365,441,362,440,361,440,359,439,359,437,361,437,362,434,362,434,361,435,359,437,358,437,357">
                        </map>



                        <script type="text/javascript">

                        jQuery("#usa_image").mapster(
                        {
                            render_highlight: {
                                stroke: true,
                                strokeWidth: 2
                            },
                            altImage:"'.plugins_url('/images/usa_highlight.png' , __FILE__).'",
                            altImageOpacity: 0.7,
                            isSelectable: true,
                            mapKey: "state",
                            mapValue: "state",
                            showToolTip: false
                        });

                        jQuery("#world_image").mapster(
                        {
                            render_highlight: {
                                stroke: true,
                                strokeWidth: 2
                            },
                            altImage:"'.plugins_url('/images/world_highlight.png' , __FILE__).'",
                            altImageOpacity: 0.7,
                            isSelectable: true,
                            mapKey: "title",
                            mapValue: "title",
                            showToolTip: false
                        });

                        jQuery("#world_image_map").click(function() {
                            jQuery("#countriestotax").val(jQuery("#world_image").mapster("get"));
                        });

                        jQuery("#usa_image_map").click(function() {
                            jQuery("#statestotax").val(jQuery("#usa_image").mapster("get"));
                        });

                        </script>




                        <br style="clear:both;" /><br />
                        </div>

			<div class="submit">
			<input type="submit" name="update_wpStoreCartSettings" value="'; _e('Update Settings', 'wpStoreCart'); echo'" /></div>
			</form>
                        </dov>
			 </div>';		
		
		}
		//END Prints out the admin page ================================================================================		


	/**
         *
         * The Admin page for the ShareYourCart.com integration
         *
         * @global object $wpdb
         * @global <type> $user_level
         * @global <type> $wpstorecart_version_int
         * @global <type> $testing_mode
         */
        function printAdminPageShareYourCart() {
			global $wpdb, $user_level,$wpstorecart_version_int,$testing_mode;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}
                        require_once(WP_PLUGIN_DIR.'/wpstorecart/php/shareyourcart/shareyourcart-sdk.php');

			

                        $this->spHeader();
                        $this->wpstorecart_alert();

                        echo '<div style="width:75%;max-width:75%;float:left;">';
                        /**
                             * Process edits here
                             */
                        if (isset($_POST['shareyourcart_clientid'])) {
                                $devOptions['shareyourcart_clientid'] = $wpdb->escape($_POST['shareyourcart_clientid']);
                        }
                        if (isset($_POST['shareyourcart_appid'])) {
                                $devOptions['shareyourcart_appid'] = $wpdb->escape($_POST['shareyourcart_appid']);
                        }
                        if (isset($_POST['shareyourcart_secret'])) {
                                $devOptions['shareyourcart_secret'] = $wpdb->escape($_POST['shareyourcart_secret']);
                        }
                        if (isset($_POST['shareyourcart_skin'])) {
                                $devOptions['shareyourcart_skin'] = $wpdb->escape($_POST['shareyourcart_skin']);
                        }
                        if (isset($_POST['shareyourcart_activate'])) {
                                if($_POST['shareyourcart_activate']!=$devOptions['shareyourcart_activate']) { // If the new setting is different than what was saved, then we have a potential activation or deactivation to run
                                    // Let's check to see if we need to send an activate or deactivate call to the API
                                    if(trim($devOptions['shareyourcart_secret'])!='' && (trim($devOptions['shareyourcart_clientid'])=='' ||  trim($devOptions['shareyourcart_appid'])=='') && $devOptions['shareyourcart_activate']=='true') {
                                        // No need to send the activation if it hasn't been disabled previously
                                    } else {
                                        if($devOptions['shareyourcart_activate']=='true' ) { // Deactivate
                                            $deactivated = shareyourcart_setAccountStatusAPI($devOptions['shareyourcart_secret'], $devOptions['shareyourcart_clientid'], $devOptions['shareyourcart_appid'], false);
                                            if($deactivated) {
                                                echo '<div class="updated fade" style="margin-top:35px;padding:10px 10px 10px 10px;">Deactivated successfully.</div><br style="clear:both;" />';
                                            } else {
                                                echo '<div class="updated fade" style="margin-top:35px;padding:10px 10px 10px 10px;">Deactivation failed.</div><br style="clear:both;" />';
                                            }
                                        } else { // Reactivate
                                            $reactivated = shareyourcart_setAccountStatusAPI($devOptions['shareyourcart_secret'], $devOptions['shareyourcart_clientid'], $devOptions['shareyourcart_appid'], true);
                                            if($reactivated) {
                                                echo '<div class="updated fade" style="margin-top:35px;padding:10px 10px 10px 10px;">Reactivated successfully.</div><br style="clear:both;" />';
                                                $devOptions['shareyourcart_failedreg'] = 'false';
                                                update_option($this->adminOptionsName, $devOptions);
                                            } else {
                                                echo '<div class="updated fade" style="margin-top:35px;padding:10px 10px 10px 10px;">Reactivation failed.</div><br style="clear:both;" />';
                                            }
                                        }
                                    }
                                }
                                $devOptions['shareyourcart_activate'] = $wpdb->escape($_POST['shareyourcart_activate']);
                        }
                        if ($devOptions['shareyourcart_activate'] == "true") {
                            $textForButton = 'Disable';
                        } else {
                            $textForButton = 'Enable';
                        }
                        echo '<a href="http://www.shareyourcart.com" target="_blank"><img src="'.plugins_url('/images/shareyourcart-logo.png' , __FILE__).'" alt="" style="float:left" /></a> <a class="button-secondary" href="#" id="activate_button" onclick="if(jQuery(\'input[name=shareyourcart_activate]:checked\', \'#syc_form\').val()==\'true\'){jQuery(\'input:radio[name=shareyourcart_activate]\').filter(\'[value=false]\').attr(\'checked\', true);jQuery(\'#activate_button\').text(\'Enable\');}else{jQuery(\'input:radio[name=shareyourcart_activate]\').filter(\'[value=true]\').attr(\'checked\', true);jQuery(\'#activate_button\').text(\'Disable\');}jQuery(\'#syc_form\').submit();" style="float:right;margin-top:25px;">'.$textForButton.'</a><span style="clear:both;"> </span><h2 style="color:#FFF;">.</h2><div style="clear:both;"></div><br /><a href="http://www.shareyourcart.com" target="_blank">ShareYourCart&#8482;</a> helps you get more customers by motivating satisfied customers to talk with their friends about your products. Each customer that promotes your products, via social media, will receive a coupon that they can apply to their shopping cart in order to get a small discount.<br /><br />';

                        // If we haven't registered, let's try to!
                        if(trim($devOptions['shareyourcart_secret'])!='' && (trim($devOptions['shareyourcart_clientid'])=='' ||  trim($devOptions['shareyourcart_appid'])=='') && $devOptions['shareyourcart_activate']=='true') {
                            if (!function_exists('curl_init')) {
                              echo '<div class="updated fade" style="padding:10px 10px 10px 10px;">cURL Support Disabled! cURL is required in order for wpStoreCart to communicate with the ShareYourCart.com API!</div><br />';
                            }
                            ob_start();
                            try {
                                $new_client = shareyourcart_registerAPI(trim($devOptions['shareyourcart_secret']), trim('http://'.$_SERVER['HTTP_HOST']), trim($devOptions['wpStoreCartEmail']));
                            } catch (Exception $e) {
                                ob_end_clean();
                                echo $e->getMessage();
                                $new_client = false;

                            }
                            if(!$new_client) {
                              echo '<div class="updated fade"  style="padding:10px 10px 10px 10px;">The domain already has an account assigned to it, please recover your Client ID and App Key.</div><br style="clear:both;" />';
                                   $devOptions['shareyourcart_failedreg'] = 'false';
                            } else {
                              $devOptions['shareyourcart_clientid'] = $new_client['client_id'];
                              $devOptions['shareyourcart_appid'] = $new_client['app_key'];

                            }
                        }

                        update_option($this->adminOptionsName, $devOptions);
                        // Done processing edits

                        if($devOptions['shareyourcart_failedreg']=='true' && (trim($devOptions['shareyourcart_clientid'])=='' && trim($devOptions['shareyourcart_appid'])=='')) {
                            $Formlabel = 'Get them Now';
                        } else {
                            $Formlabel = 'Lost them?';
                        }

                        // The actual admin form
			echo '<br /><form method="post" action="'. $_SERVER["REQUEST_URI"].'" id="syc_form" name="syc_form">
                            <table class="widefat" style="max-width:535px;width:535px;">
			<tbody>

                        <tr style="display:none;"><td><h3>ShareYourCart.com Secret Key <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-400654761" /><div class="tooltip-content" id="example-content-400654761">Once you\'re registered with ShareYourCart.com, you will be given a "Secret Key" which will allow you to use the service.  Insert that "Secret Key" here, turn "Activate ShareYourCart.com?" to "Yes", and then click "Update Settings" to begin using the service.</div></h3></td>
			<td><input type="text" name="shareyourcart_secret" value="'; _e(apply_filters('format_to_edit',$devOptions['shareyourcart_secret']), 'wpStoreCart'); echo'" style="width:300px;" />
			</td></tr>


			<tr><td><h3>Client ID <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-400654765" /><div class="tooltip-content" id="example-content-400654765">The Client ID assigned to your website.  This number should be generated and inputed here for you when you successfully register and activate the service.  If you\'ve previously activated on this domain name before, you may need to recover your Client ID and App Key by clicking on the Recover option below.</div></h3></td>
			<td><input type="text" name="shareyourcart_clientid" value="'; _e(apply_filters('format_to_edit',$devOptions['shareyourcart_clientid']), 'wpStoreCart'); echo'" style="width:300px;margin-top:10px;" />
			</td></tr>

			<tr><td><h3>App Key <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4006547656" /><div class="tooltip-content" id="example-content-4006547656">The App Key assigned to your website.  This number should be generated and inputed here for you when you successfully register and activate the service.  If you\'ve previously activated on this domain name before, you may need to recover your Client ID and App Key by clicking on the Recover option below.</div></h3></td>
			<td><input type="text" name="shareyourcart_appid" value="'; _e(apply_filters('format_to_edit',$devOptions['shareyourcart_appid']), 'wpStoreCart'); echo'" style="width:300px;margin-top:10px;" />
			</td></tr>


			<tr style="display:none;"><td><h3>'.$Formlabel.' <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-474599688" /><div class="tooltip-content" id="example-content-474599688">Set this to yes to initially register and later to activate/reactivate and use ShareYourCart.com on your website (secret key is required for initial registration, afterwards, the Client ID, App Key, and Secret Key are required to activate and use the service.)</div></h3></td>
			<td><p><label for="shareyourcart_activate"><input type="radio" id="shareyourcart_activate_yes" name="shareyourcart_activate" value="true" '; if ($devOptions['shareyourcart_activate'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="shareyourcart_activate_no"><input type="radio" id="shareyourcart_activate_no" name="shareyourcart_activate" value="false" '; if ($devOptions['shareyourcart_activate'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
			</td></tr>
                        </table>

                        </table><br style="clear:both;" />
                        <div style="margin-left:134px;margin-top:-12px;"><i>These credentials are used to communicate with ShareYourCart&#8482;. <a href="" onclick="newwindow=window.open(\''.plugins_url('/php/shareyourcart/recover.php' , __FILE__).'\',\'recoveryname\',\'height=233,width=420\');if (window.focus) {newwindow.focus();};return false;">'.$Formlabel.'</a></i></div>
                        <br /><br />
                            <table class="widefat" style="max-width:275px;width:275px;">
			<tbody>

			<tr><td><h3>Button skin </h3></td>
			<td class="tableDescription"></td>
			<td>
                        <select name="shareyourcart_skin" style="width:100px;margin-top:10px;">
';

                        $theOptionszC[0] = 'orange';$theOptionszCName[0] = 'Orange';
                        $theOptionszC[1] = 'blue';$theOptionszCName[1] = 'Blue';

                        $icounter = 0;
                        foreach ($theOptionszC as $theOption) {

				$option = '<option value="'.$theOption.'"';
				if($theOption == $devOptions['shareyourcart_skin']) {
					$option .= ' selected="selected"';
				}
				$option .='>';
				$option .= $theOptionszCName[$icounter];
				$option .= '</option>';
				echo $option;
                                $icounter++;
                        }

   			echo '
			</select>
			</td></tr>
                        </tbody>
                        </table>
                        
			<div class="submit">
			<input type="submit" class="button-primary" name="update_ShareYourCartSettings" value="'; _e('Save', 'wpStoreCart'); echo'" /></div>
			</form>

                        <p>You can choose how much of a discount to give (in fixed amount, percentage, or free shipping) to which social media channels it should it be applied, as well as what the advertisement should say.</p>
                        <center><a style="border:none;outline:none;" href="http://www.shareyourcart.com/configure?client_id='.$devOptions['shareyourcart_clientid'].'&app_key='.$devOptions['shareyourcart_appid'].'" title="Configure" target="blank"><img src="'.plugins_url('/images/configure.png' , __FILE__).'" alt="Configure" /></a></center>
                        </div>

			';

        }

		
		//Prints out the Import/Export admin page =======================================================================
        function printAdminPageImport() {
			global $wpdb, $user_level,$wpstorecart_version_int,$testing_mode;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}

			
			$table_name = $wpdb->prefix . "wpstorecart_products";
                        $this->spHeader();
                        $this->wpstorecart_alert();

                        echo '<div style="width:75%;max-width:75%;float:left;"><img src="'.plugins_url('/images/refresh.png' , __FILE__).'" alt="" style="float:left;" /><h2 style="font-size:32px;">&nbsp;&nbsp;&nbsp;Import/Export <a href="http://wpstorecart.com/documentation/adding-editing-products/import-export/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2><br style="clear:both;" />';


			

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
                            /* <![CDATA[ */


                                    var productUploadStartEventHandler = function (file) {
                                            var continue_with_upload;

                                            continue_with_upload = true;

                                            return continue_with_upload;
                                    };

                                    var productUploadSuccessEventHandler = function (file, server_data, receivedResponse) {
                                            document.theimportform.importthisfile.value = file.name;
                                            jQuery("#upload-progressbar4").html("<center>Upload done. Import starting...</center>")
                                            window.open("'.plugins_url('/php/importcsv.php' , __FILE__).'?file="+ file.name,"myimportwindow","menubar=1,resizable=1,width=350,height=250");

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
                                            jQuery("#upload-progressbar4").html("<center>Upload progress: "+ percent+"%</center>");
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
                                                    upload_url : "'.plugins_url('/php/upload.php' , __FILE__).'",
                                                    post_params: {"PHPSESSID" : "'.session_id().'"},
                                                    flash_url : "'.get_option( 'siteurl' ).'/wp-includes/js/swfupload/swfupload.swf",
                                                    file_size_limit : "2048 MB",
                                                    file_types : "*.*",
                                                    file_types_description : "Any file type",
                                                    file_upload_limit : "1",
                                                    file_post_name: "Filedata",
                                                    button_placeholder_id : "spanSWFUploadButton4",
                                                    button_image_url : "'.plugins_url('/images/XPButtonUploadText_61x22.png' , __FILE__).'",
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
                            /* ]]> */
                            </script>
                            <br />';

                        if(@$_POST['isreal']=='true') {
                            echo '<h3>Attempting to '.$_POST['typeofoperation'] .' using '; if($_POST['typeofoperation']=='export') {echo $_POST['exportformat'];} else {echo $_POST['importformat'];} echo ' file...</h3>';

                            // Export routines here:
                            if($_POST['typeofoperation']=='export') {
                                if($_POST['exportformat']=='csv') {
                                    echo '
                                        <script type="text/javascript">
                                        /* <![CDATA[ */
                                        window.open("'.plugins_url('/php/exportcsv.php' , __FILE__).'");

                                        /* ]]> */
                                        </script>
                                        ';
                                }

                                if($_POST['exportformat']=='sql') {
                                    echo '
                                        <script type="text/javascript">
                                        /* <![CDATA[ */
                                        window.open("'.plugins_url('/php/exportsql.php' , __FILE__).'");
                                        /* ]]> */
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
                                  <!--<option value="sql">SQL file</option>-->
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
                             </form></div>
                             ';

        }
		//END Prints out the admin page ================================================================================



		
		//Prints out the Add products admin page =======================================================================
        function printAdminPageAddproducts() {
			global $wpdb, $user_level;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			
			$table_name = $wpdb->prefix . "wpstorecart_products";
                        $table_name_meta = $wpdb->prefix . "wpstorecart_meta";

                        // New products will have any available shipping options available.
                        $flatrateshipping_checked = 'yes';
                        $usps_checked = 'yes';
                        $ups_checked = 'yes';
                        $fedex_checked = 'yes';

                        // New products will also by default display the Add to Cart button, even if there are variations
                        $display_add_to_cart_at_all_times = 'no';
			
			// For new products
			if(!isset($_GET['keytoedit'])) {
				// Default form values
				$wpStoreCartproduct_name = '';
				$wpStoreCartproduct_introdescription = '';
				$wpStoreCartproduct_description = '';
				$wpStoreCartproduct_thumbnail = '';
				$wpStoreCartproduct_price = '0.00';
				$wpStoreCartproduct_shipping = '0.00';
				$wpStoreCartproduct_download = '';
				$wpStoreCartproduct_tags = '';
				$wpStoreCartproduct_category = 0;
				$wpStoreCartproduct_inventory = 0;
                                $wpStoreCartproduct_useinventory = 1;
                                $wpStoreCartproduct_weight = 0;
                                $wpStoreCartproduct_length = 0;
                                $wpStoreCartproduct_width = 0;
                                $wpStoreCartproduct_height = 0;
				$keytoedit=0;
				$_GET['keytoedit'] = 0;
                                $wpStoreCartproduct_donation = 'false';
                                $wpStoreCartproduct_serial_numbers = '';
                                $wpStoreCartproduct_serial_numbers_used = '';
                                $wpStoreCartproduct_discountprice = '0.00';
			} 
			
			
			// To edit a previous product
			$isanedit = false;
			if ($_GET['keytoedit']!=0 && is_numeric($_GET['keytoedit'])) {
				$isanedit = true;



                                // Grabs the serial numbers
                                $results_serial_numbers = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='serialnumbers' AND `foreignkey`={$_GET['keytoedit']};", ARRAY_N);
                                if($results_serial_numbers!=false ) {
                                    $wpStoreCartproduct_serial_numbers = $results_serial_numbers[0][0];
                                    if(isset($_POST['wpStoreCartproduct_serial_numbers'])) {
                                        $wpStoreCartproduct_serial_numbers = $_POST['wpStoreCartproduct_serial_numbers'];
                                        $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = '".base64_encode($_POST['wpStoreCartproduct_serial_numbers'])."' WHERE `type`='serialnumbers' AND `foreignkey` = {$_GET['keytoedit']};");
                                    }
                                } else {
                                    if(isset($_POST['wpStoreCartproduct_serial_numbers'])) {
                                        $wpStoreCartproduct_serial_numbers = $_POST['wpStoreCartproduct_serial_numbers'];
                                        $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".base64_encode($_POST['wpStoreCartproduct_serial_numbers'])."', 'serialnumbers', '{$_GET['keytoedit']}');");
                                    }
                                }

                                // Grabs the used serial numbers
                                $results_serial_numbers_used = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='serialnumbersused' AND `foreignkey`={$_GET['keytoedit']};", ARRAY_N);
                                if($results_serial_numbers_used!=false ) {
                                    $wpStoreCartproduct_serial_numbers_used = $results_serial_numbers_used[0][0];
                                    if(isset($_POST['wpStoreCartproduct_serial_numbers_used'])) {
                                        $wpStoreCartproduct_serial_numbers_used = $_POST['wpStoreCartproduct_serial_numbers_used'];
                                        $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = '".base64_encode($_POST['wpStoreCartproduct_serial_numbers_used'])."' WHERE `type`='serialnumbersused' AND `foreignkey` = {$_GET['keytoedit']};");
                                    }
                                } else {
                                    if(isset($_POST['wpStoreCartproduct_serial_numbers_used'])) {
                                        $wpStoreCartproduct_serial_numbers_used = $_POST['wpStoreCartproduct_serial_numbers_used'];
                                        $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".base64_encode($_POST['wpStoreCartproduct_serial_numbers_used'])."', 'serialnumbersused', '{$_GET['keytoedit']}');");
                                    }
                                }

                                // Disables the Add to Cart if needed
                                $results_disable_add_to_cart = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='disableaddtocart' AND `foreignkey`={$_GET['keytoedit']};", ARRAY_N);
                                if($results_disable_add_to_cart==false ) {
                                    $display_add_to_cart_at_all_times = 'no';
                                } else {
                                    if($results_disable_add_to_cart[0][0]=='yes') {
                                        $display_add_to_cart_at_all_times = 'yes';
                                    } else {
                                        $display_add_to_cart_at_all_times = 'no';
                                    }
                                }

                                // Shipping options are saved here
                                // Flat rate on/off for this product?
                                $results_flatrateshipping = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='wpsc_product_flatrateshipping' AND `foreignkey`={$_GET['keytoedit']};", ARRAY_N);
                                if($results_flatrateshipping==false ) {
                                    $flatrateshipping_checked = 'no';
                                } else {
                                    if($results_flatrateshipping[0][0]=='yes') {
                                        $flatrateshipping_checked = 'yes';
                                    } else {
                                        $flatrateshipping_checked = 'no';
                                    }
                                }

                                // USPS on/off for this product?
                                $results_usps = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='wpsc_product_usps' AND `foreignkey`={$_GET['keytoedit']};", ARRAY_N);
                                if($results_usps==false ) {
                                    $usps_checked = 'no';
                                } else {
                                    if($results_usps[0][0]=='yes') {
                                        $usps_checked = 'yes';
                                    } else {
                                        $usps_checked = 'no';
                                    }
                                }

                                // UPS on/off for this product?
                                $results_ups = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='wpsc_product_ups' AND `foreignkey`={$_GET['keytoedit']};", ARRAY_N);
                                if($results_ups==false ) {
                                    $ups_checked = 'no';
                                } else {
                                    if($results_ups[0][0]=='yes') {
                                        $ups_checked = 'yes';
                                    } else {
                                        $ups_checked = 'no';
                                    }
                                }

                                // FedEx on/off for this product?
                                $results_fedex = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='wpsc_product_fedex' AND `foreignkey`={$_GET['keytoedit']};", ARRAY_N);
                                if($results_fedex==false ) {
                                    $fedex_checked = 'no';
                                } else {
                                    if($results_fedex[0][0]=='yes') {
                                        $fedex_checked = 'yes';
                                    } else {
                                        $fedex_checked = 'no';
                                    }
                                }


				if (isset($_POST['wpStoreCartproduct_name']) && isset($_POST['wpStoreCartproduct_introdescription']) && isset($_POST['wpStoreCartproduct_description']) && isset($_POST['wpStoreCartproduct_thumbnail']) && isset($_POST['wpStoreCartproduct_price']) && isset($_POST['wpStoreCartproduct_shipping']) && isset($_POST['wpStoreCartproduct_download']) && isset($_POST['wpStoreCartproduct_tags']) && isset($_POST['wpStoreCartproduct_category']) && isset($_POST['wpStoreCartproduct_inventory'])) {

                                        // Add to Cart on/off for this product?

                                        if(isset($_POST['enableproduct_display_add_to_cart_variations']) && $_POST['enableproduct_display_add_to_cart_variations']=='yes') {
                                            $display_add_to_cart_at_all_times = 'yes';
                                            if($results_disable_add_to_cart==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'disableaddtocart', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'yes' WHERE `type`='disableaddtocart' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        } else {
                                            $display_add_to_cart_at_all_times = 'no';
                                            if($display_add_to_cart_at_all_times==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'disableaddtocart', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'no' WHERE `type`='disableaddtocart' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        }


                                        // Flat rate on/off for this product?
                                        if(isset($_POST['wpsc_product_flatrateshipping']) && $_POST['wpsc_product_flatrateshipping']=='yes') {
                                            $flatrateshipping_checked = 'yes';
                                            if($results_flatrateshipping==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'wpsc_product_flatrateshipping', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'yes' WHERE `type`='wpsc_product_flatrateshipping' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        } else {
                                            $flatrateshipping_checked = 'no';
                                            if($results_flatrateshipping==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'wpsc_product_flatrateshipping', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'no' WHERE `type`='wpsc_product_flatrateshipping' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        }

                                        // USPS on/off for this product?

                                        if(isset($_POST['wpsc_product_usps']) && $_POST['wpsc_product_usps']=='yes') {
                                            $usps_checked = 'yes';
                                            if($results_usps==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'wpsc_product_usps', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'yes' WHERE `type`='wpsc_product_usps' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        } else {
                                            $usps_checked = 'no';
                                            if($results_usps==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'wpsc_product_usps', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'no' WHERE `type`='wpsc_product_usps' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        }


                                        // UPS on/off for this product?

                                        if(isset($_POST['wpsc_product_ups']) && $_POST['wpsc_product_ups']=='yes') {
                                            $ups_checked = 'yes';
                                            if($results_ups==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'wpsc_product_ups', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'yes' WHERE `type`='wpsc_product_ups' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        } else {
                                            $ups_checked = 'no';
                                            if($results_ups==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'wpsc_product_ups', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'no' WHERE `type`='wpsc_product_ups' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        }


                                        // FedEx on/off for this product?

                                        if(isset($_POST['wpsc_product_fedex']) && $_POST['wpsc_product_fedex']=='yes') {
                                            $fedex_checked = 'yes';
                                            if($results_fedex==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'wpsc_product_fedex', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'yes' WHERE `type`='wpsc_product_fedex' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        } else {
                                            $fedex_checked = 'no';
                                            if($results_fedex==false ) {
                                                $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'wpsc_product_fedex', '{$_GET['keytoedit']}');");
                                            } else {
                                                $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'no' WHERE `type`='wpsc_product_fedex' AND `foreignkey` = {$_GET['keytoedit']};");
                                            }
                                        }

                                        $wpStoreCartproduct_name = $wpdb->escape($_POST['wpStoreCartproduct_name']);
					$wpStoreCartproduct_introdescription = $wpdb->escape($_POST['wpStoreCartproduct_introdescription']);
					$wpStoreCartproduct_description = $wpdb->escape($_POST['wpStoreCartproduct_description']);
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
                                        $wpStoreCartproduct_weight = $wpdb->escape($_POST['wpStoreCartproduct_weight']);
                                        $wpStoreCartproduct_length = $wpdb->escape($_POST['wpStoreCartproduct_length']);
                                        $wpStoreCartproduct_width = $wpdb->escape($_POST['wpStoreCartproduct_width']);
                                        $wpStoreCartproduct_height = $wpdb->escape($_POST['wpStoreCartproduct_height']);
                                        $wpStoreCartproduct_discountprice = $wpdb->escape($_POST['wpStoreCartproduct_discountprice']);
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
                                        `donation` =  '{$wpStoreCartproduct_donation}',
                                        `weight` = '{$wpStoreCartproduct_weight}',
                                        `length` = '{$wpStoreCartproduct_length}',
                                        `width` = '{$wpStoreCartproduct_width}',
                                        `height` = '{$wpStoreCartproduct_height}',
                                        `discountprice` = '{$wpStoreCartproduct_discountprice}'
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
                                                $wpStoreCartproduct_weight = stripslashes($result['weight']);
                                                $wpStoreCartproduct_length = stripslashes($result['length']);
                                                $wpStoreCartproduct_width = stripslashes($result['width']);
                                                $wpStoreCartproduct_height = stripslashes($result['height']);
                                                $wpStoreCartproduct_discountprice = stripslashes($result['discountprice']);

  

					}
				} else {
					echo '<div class="updated"><p><strong>';
					echo "There was a problem loading the product you wish to edit.  The query was: {$grabrecord} ";
					echo '</strong></p></div>';					
				}
			}
			
			if ($isanedit == false) { // New Products
			
					$wpStoreCartproduct_name = 'Product name';
					$wpStoreCartproduct_introdescription = '';
					$wpStoreCartproduct_description = '';
					$wpStoreCartproduct_thumbnail = '';
					$wpStoreCartproduct_price = 0.00;
					$wpStoreCartproduct_shipping = 0.00;
					$wpStoreCartproduct_download = '';
					$timestamp = date('Ymd');
					$wpStoreCartproduct_tags = '';
					$wpStoreCartproduct_category = 0;
					$wpStoreCartproduct_inventory = 0;
                                        $wpStoreCartproduct_useinventory = 0;
                                        $wpStoreCartproduct_donation = 0;
                                        $wpStoreCartproduct_weight = 0;
                                        $wpStoreCartproduct_length = 0;
                                        $wpStoreCartproduct_width = 0;
                                        $wpStoreCartproduct_height = 0;
                                        $wpStoreCartproduct_discountprice = 0;
	
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
						_e("ERROR 4: wpStoreCart didn't like your data and failed to create a page for it! Make sure you create a product with at least a title.", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';	
						return false;
					}


					// Now insert the product into the wpStoreCart database
					$insert = "
					INSERT INTO {$table_name} (`primkey`, `name`, `introdescription`, `description`, `thumbnail`, `price`, `shipping`, `download`, `tags`, `category`, `inventory`, `dateadded`, `postid`, `timesviewed`, `timesaddedtocart`, `timespurchased`, `useinventory`, `donation`, `weight`, `length`, `width`, `height`, `discountprice`) VALUES
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
                                        {$wpStoreCartproduct_donation},
                                        {$wpStoreCartproduct_weight},
                                        {$wpStoreCartproduct_length},
                                        {$wpStoreCartproduct_width},
                                        {$wpStoreCartproduct_height},
                                        '{$wpStoreCartproduct_discountprice}'
                                        );
					";					
					
					$results = $wpdb->query( $insert );
					$lastID = $wpdb->insert_id;
					$keytoedit = $lastID;

                                       
                                            // Now that we've inserted both the PAGE and the product, let's update and publish our post with the correct content
                                            $my_post = array();
                                            $my_post['ID'] = $thePostID;
                                            $my_post['post_content'] = '[wpstorecart display="product" primkey="'.$lastID.'"]';
                                            $my_post['post_status'] = 'draft';
                                            wp_update_post( $my_post );
                                            
	

					if($results===false) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 2: There was a problem with your form!  The database query was invalid. ", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';							
					} else { // If we get this far, we are still successful					
						echo '
                                                <script type="text/javascript">
                                                <!--
                                                window.location = "'.get_bloginfo('wpurl').'/wp-admin/admin.php?page=wpstorecart-add-products&keytoedit='.$keytoedit.'"
                                                //-->
                                                </script>
                                                ';//
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
                        $this->wpstorecart_alert();

			
			if($isanedit==true) { // An edit's REQUEST_URL will already have the key appended, while a new product won't
				$codeForKeyToEdit = NULL;
			} else {
				$codeForKeyToEdit = '&keytoedit='.$keytoedit;
			}
			if(isset($lastID)) {
				$codeForKeyToEdit = '&keytoedit='.$lastID;
			}
			
                        echo '
                        <script type="text/javascript">
                            //<![CDATA[
			jQuery(document).ready(function($) {
                                //When page loads...
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
                                $(".tab_content").hide(); //Hide all content

                                $("ul.tabs "+theCurrentTab).addClass("active").show(); //Activate first tab
                                $(theCurrentTab).show(); //Show first tab content

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

                        ';

			echo '
			<form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">
                        
                        ';

                        echo '<div style="width:75%;max-width:75%;float:left;"><img src="'.plugins_url('/images/addproduct.png' , __FILE__).'" alt="" style="float:left;" /><h2 style="font-size:32px;">&nbsp;&nbsp;&nbsp;Add &amp; Edit Products <a href="http://wpstorecart.com/documentation/adding-editing-products/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2><br style="clear:both;" />
                        <ul class="tabs">
                            ';
                            if($isanedit==true) {
                                echo '
                                    <li style="display:inline;"><a href="#tab1"><img src="'.plugins_url('/images/buttons_product_info.jpg' , __FILE__).'" /></a></li>
                                    <li style="display:inline;"><a href="#tab4"><img src="'.plugins_url('/images/buttons_pictures.jpg' , __FILE__).'" /></a></li>
                                    <li style="display:inline;" id="wpsc-variations-li"><a href="#tab2"><img src="'.plugins_url('/images/buttons_variation.jpg' , __FILE__).'" /></a></li>
                                    ';
                            }
                            if($devOptions['storetype']!='Physical Goods Only' && $isanedit==true){
                                echo '<li style="display:inline;"><a href="#tab3"><img src="'.plugins_url('/images/buttons_download.jpg' , __FILE__).'" /></a></li>';
                            }
                            if($isanedit == true) {
                                echo '<a href="'.get_permalink($result['postid']).'"><img src="'.plugins_url('/images/buttons_view_page.jpg' , __FILE__).'" style="display:inline;" /></a>';
                            }
                        echo '
                        </ul>

                        <div id="tab1" class="tab_content">';

			echo '<table class="widefat">
			<thead><tr><th>Product Attribute</th><th>Value</th><th>Description</th></tr></thead><tbody>
			';
			
			echo '
			<tr>
			<td><h3>Product<br />Name: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1">The name of the product.  We do not recommend stuffing this with keywords, unless you don\'t mind those keywords being repeated everytime the product is mentioned.  Instead, simply keep this as the actual name of the product.</div></h3></td>
			<td><input type="text" class="validate[required]" name="wpStoreCartproduct_name" id="wpStoreCartproduct_name" style="width: 80%;height:35px;font-size:22px;" value="'.$wpStoreCartproduct_name.'" /></td>
			<td><div style="width:300px;">The title of the product.</div></td>
			</tr>';			

			echo '
			<tr>
			<td><h3>Introduction<br />Description: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3">Keep this short and concise, as this text will be used in several places as a quick description of the product.  For higher sales and conversions, sum up the main features and benefits and include a direct call to action.</div></h3></td>
			<td><textarea class="wpStoreCartproduct_introdescription" id="wpStoreCartproduct_introdescription" name="wpStoreCartproduct_introdescription" style="width: 80%;">'.$wpStoreCartproduct_introdescription.'</textarea>  </td>
			<td><div style="width:300px;">A short introduction to the product. </div></td>
			</tr>';	
			
			
			echo '
			<tr>
			<td><h3>Description: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4">Put your complete sales pitch here.  There are many techniques which can help make your product\'s sale page more effective.  At the very least, most sales pages include at least some of the features and benefits of the product, and include one or more calls to action.</div></h3></td>
			<td><textarea class="wpStoreCartproduct_description" id="wpStoreCartproduct_description" name="wpStoreCartproduct_description" style="width: 80%;">'.$wpStoreCartproduct_description.'</textarea><p align="right">
                        <a class="button" onclick="if (!tinyMCE.get(\'wpStoreCartproduct_description\')) {tinyMCE.execCommand(\'mceAddControl\', false, \'wpStoreCartproduct_description\');};">Visual</a>
                        <a class="button" onclick="if (tinyMCE.get(\'wpStoreCartproduct_description\')) {tinyMCE.execCommand(\'mceRemoveControl\', false, \'wpStoreCartproduct_description\');};">HTML</a></p>
                        </td>
			<td><div style="width:300px;">Your detailed description and sales pitch for the product.</div></td>
			</tr>';			

                        $wpsc_price_type = 'charge';
                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php')) {
                            $membership_primkey = NULL;
                            $membership_value = NULL;
                            $wpsc_membership_trial1_allow = 'no';
                            $wpsc_membership_trial2_allow = 'no';
                            $wpsc_membership_trial1_amount = '0.00';
                            $wpsc_membership_trial2_amount = '0.00';
                            $wpsc_membership_regular_amount = '0.00';
                            $wpsc_membership_trial1_numberof = '1';
                            $wpsc_membership_trial2_numberof = '1';
                            $wpsc_membership_regular_numberof = '1';
                            $wpsc_membership_trial1_increment = 'D';
                            $wpsc_membership_trial2_increment = 'D';
                            $wpsc_membership_regular_increment = 'D';
                            $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                            if(isset($_GET['keytoedit']) && is_numeric($_GET['keytoedit'])) {
                                
                                $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$_GET['keytoedit']};";
                                $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                                if(isset($resultsMembership)) {
                                    foreach ($resultsMembership as $pagg) {
                                        $membership_primkey = $pagg['primkey'];
                                        $membership_value = $pagg['value'];
                                    }
                                    if($membership_value!='') {
                                        $theExploded = explode('||', $membership_value);
                                        // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                                        $wpsc_price_type = $theExploded[0];
                                        $wpsc_membership_trial1_allow = $theExploded[1];
                                        $wpsc_membership_trial2_allow = $theExploded[2];
                                        $wpsc_membership_trial1_amount = $theExploded[3];
                                        $wpsc_membership_trial2_amount = $theExploded[4];
                                        $wpsc_membership_regular_amount = $theExploded[5];
                                        $wpsc_membership_trial1_numberof = $theExploded[6];
                                        $wpsc_membership_trial2_numberof = $theExploded[7];
                                        $wpsc_membership_regular_numberof = $theExploded[8];
                                        $wpsc_membership_trial1_increment = $theExploded[9];
                                        $wpsc_membership_trial2_increment = $theExploded[10];
                                        $wpsc_membership_regular_increment = $theExploded[11];
                                    }
                                }

                                // Here's code to add or update the membership status
                                if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php')) {
                                    if($_POST['wpsc-price-type']=='membership' || $_POST['wpsc-price-type']=='charge') {

                                        $wpsc_membership_trial1_allow = 'no';
                                        $wpsc_membership_trial2_allow = 'no';
                                        if($_POST['wpsc_membership_trial1_allow']=='yes') {
                                            $wpsc_membership_trial1_allow = 'yes';
                                        }
                                        if($_POST['wpsc_membership_trial2_allow']=='yes') {
                                            $wpsc_membership_trial2_allow = 'yes';
                                        }
                                        $membership_value = $_POST['wpsc-price-type'] . '||' . $wpsc_membership_trial1_allow . '||' . $wpsc_membership_trial2_allow .'||'. $_POST['wpsc_membership_trial1_amount'] .'||'. $_POST['wpsc_membership_trial2_amount'] . '||' . $_POST['wpsc_membership_regular_amount'] . '||' . $_POST['wpsc_membership_trial1_numberof']. '||' . $_POST['wpsc_membership_trial2_numberof']. '||' . $_POST['wpsc_membership_regular_numberof']. '||' . $_POST['wpsc_membership_trial1_increment']. '||' . $_POST['wpsc_membership_trial2_increment']. '||' . $_POST['wpsc_membership_regular_increment'];
                                        $wpsc_price_type = $_POST['wpsc-price-type'];
                                        $wpsc_membership_trial1_amount = $_POST['wpsc_membership_trial1_amount'];
                                        $wpsc_membership_trial2_amount = $_POST['wpsc_membership_trial2_amount'];
                                        $wpsc_membership_regular_amount = $_POST['wpsc_membership_regular_amount'];
                                        $wpsc_membership_trial1_numberof = $_POST['wpsc_membership_trial1_numberof'];
                                        $wpsc_membership_trial2_numberof = $_POST['wpsc_membership_trial2_numberof'];
                                        $wpsc_membership_regular_numberof = $_POST['wpsc_membership_regular_numberof'];
                                        $wpsc_membership_trial1_increment = $_POST['wpsc_membership_trial1_increment'];
                                        $wpsc_membership_trial2_increment = $_POST['wpsc_membership_trial2_increment'];
                                        $wpsc_membership_regular_increment = $_POST['wpsc_membership_regular_increment'];

                                        if($membership_primkey != NULL) {
                                            // Must update the membership
                                            $insert = "UPDATE  `{$table_name_meta}` SET `value` = '".$membership_value."' WHERE `type`='membership' AND `foreignkey`='{$_GET['keytoedit']}';";
                                            $memresults = $wpdb->query( $insert );
                                        } else {
                                            // Must insert new membership
                                            $insert = "INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".$membership_value."', 'membership', '{$_GET['keytoedit']}');";
                                            $memresults = $wpdb->query( $insert );
                                        }

                                    } elseif ($_POST['wpsc-price-type']=='charge') {
                                        if($membership_primkey != NULL) {
                                            // Must update the membership meta entry to turn it off
                                        }
                                    }
                                }
                            }
                        }

			echo '
			<tr id="wpsc-price-tr"'; if($wpsc_price_type=='membership') {echo ' style="display:none;"';} echo'>
			<td><h3>Price'; if($devOptions['storetype']!='Digital Goods Only') { echo '<br />& Shipping';} echo ': <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2">The price you wish to charge for the product before tax and shipping charges.  You can also enter a flat rate shipping amount here, which will only be used if you do not offer other shipping options, such as FedEx or UPS.  If the shipping options are not here, that means the General Setting > Store Type is set to Digital Only.  Change that setting to restore the shipping options here.</div></h3></td>
			<td>';
                        
                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php')) {
                            if($wpsc_price_type=='membership') {
                                echo '
                                <script type="text/javascript">
                                    /* <![CDATA[ */
                                        jQuery("#wpsc-variations-li").hide("slow");
                                    /* ]]> */
                                </script>';
                            }

                            echo '<input type="radio" name="wpsc-price-type" '; if($wpsc_price_type=='charge') {echo ' checked="checked"';} echo' value="charge" onclick="jQuery(\'#wpsc-variations-li\').show(\'slow\');" /> Regular product<br />
                                <input type="radio" name="wpsc-price-type"  '; if($wpsc_price_type=='membership') {echo ' checked="checked"';} echo'  value="membership" onclick="jQuery(\'#wpsc-variations-li\').hide(\'slow\');jQuery(\'input:radio[name=wpsc-price-type2]\').filter(\'[value=membership]\').prop(\'checked\', true);jQuery(\'#wpsc-membership-tr\').toggle();jQuery(\'#wpsc-price-tr\').toggle();" /> Charge for this product on a reoccuring basis:<br />';
                        }

                        echo '<br /><div style="display:block;float:left;"><b>Full Price:</b> '.$devOptions['currency_symbol'].'<input type="text" class="validate[custom[positiveDecimal]]" name="wpStoreCartproduct_price" id="wpStoreCartproduct_price" style="width: 58px;" value="'.$wpStoreCartproduct_price.'" />'.$devOptions['currency_symbol_right'].' <br /><b>Sale Price:</b> '.$devOptions['currency_symbol'].'<input type="text" class="validate[custom[positiveDecimal]]" name="wpStoreCartproduct_discountprice" id="wpStoreCartproduct_discountprice" style="width: 58px;" value="'.$wpStoreCartproduct_discountprice.'" />'.$devOptions['currency_symbol_right'].' </div><div style=";display:block;float:left;margin-left:15px;position:relative;top-25px;"> '; if($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') { echo '<br /><input type="checkbox" name="wpsc_product_flatrateshipping" value="yes" '; if($flatrateshipping_checked == 'yes') {echo 'checked="checked"';} echo ' /> Flat Rate Shipping: '.$devOptions['currency_symbol'];} echo '<input type="';if($devOptions['storetype']=='Digital Goods Only' || $devOptions['flatrateshipping']!='individual') {echo 'hidden';} else {echo 'text';} echo '" name="wpStoreCartproduct_shipping" style="width: 58px;" value="'.$wpStoreCartproduct_shipping.'" />';if($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') {echo $devOptions['currency_symbol_right'];} if($devOptions['storetype']!='Digital Goods Only' && $devOptions['enableusps']=='true') {echo '<br /><input type="checkbox" '; if($usps_checked == 'yes') {echo 'checked="checked"';} echo ' name="wpsc_product_usps" id="wpsc_product_usps" onclick="if(jQuery(\'#wpsc_product_usps\').is(\':checked\')){jQuery(\'#wpscdimensions\').effect(\'pulsate\', { times:2 }, 500);}" value="yes" /> Offer USPS shipping for this product? ';}if($devOptions['storetype']!='Digital Goods Only' && $devOptions['enableups']=='true') {echo '<br /><input type="checkbox" '; if($ups_checked == 'yes') {echo 'checked="checked"';} echo ' name="wpsc_product_ups" id="wpsc_product_ups" onclick="if(jQuery(\'#wpsc_product_ups\').is(\':checked\')){jQuery(\'#wpscdimensions\').effect(\'pulsate\', { times:2 }, 500);}" value="yes" /> Offer UPS shipping for this product? ';} if($devOptions['storetype']!='Digital Goods Only' && $devOptions['enablefedex']=='true') {echo '<br /><input type="checkbox" '; if($fedex_checked == 'yes') {echo 'checked="checked"';} echo ' name="wpsc_product_fedex" id="wpsc_product_fedex" onclick="if(jQuery(\'#wpsc_product_fedex\').is(\':checked\')){jQuery(\'#wpscdimensions\').effect(\'pulsate\', { times:2 }, 500);}" value="yes" /> Offer Fedex shipping for this product? ';} echo '</div></td>
			<td><div style="width:300px;"><br /><div style="margin-left:20px;display:block;float:left;min-width:120px;min-height:30px;width:120px;height:30px;"><strong>Accept Donations? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-333777" /><div class="tooltip-content" id="example-content-333777">Note that this feature is only supported in the PayPal payment module currently.  If "Yes" is selected, this product is only given away when donations are made.  Note that the price you set above now becomes the minimum suggested donation amount.</div></strong><label for="wpStoreCartproduct_donation_yes"><input type="radio" id="wpStoreCartproduct_donation_yes" name="wpStoreCartproduct_donation" value="1" '; if ($wpStoreCartproduct_donation == 1) { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="wpStoreCartproduct_donation_no"><input type="radio" id="wpStoreCartproduct_donation_no" name="wpStoreCartproduct_donation" value="false" '; if ($wpStoreCartproduct_donation == 0) { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></div></div></td>
			</tr>';

                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php')) {
                            echo '
                            <tr id="wpsc-membership-tr"'; if($wpsc_price_type=='charge') {echo ' style="display:none;"';} echo'>
                            <td><h3>Subscription <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-61111235" /><div class="tooltip-content" id="example-content-61111235">Allows you to sell a subscription or membership on a reoccuring basis.</div></h3>
                            </td>
                            <td>';

                            if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php')) {
                                echo '<input type="radio" name="wpsc-price-type2" '; if($wpsc_price_type=='charge') {echo ' checked="checked"';} echo' value="charge" onclick="jQuery(\'#wpsc-variations-li\').show(\'slow\');jQuery(\'input:radio[name=wpsc-price-type]\').filter(\'[value=charge]\').prop(\'checked\', true);jQuery(\'#wpsc-membership-tr\').toggle();jQuery(\'#wpsc-price-tr\').toggle();"  /> Regular product<br />
                                    <input type="radio" name="wpsc-price-type2"  '; if($wpsc_price_type=='membership') {echo ' checked="checked"';} echo'  value="membership" onclick="jQuery(\'#wpsc-variations-li\').hide(\'slow\');" checked="yes" /> Charge for this product on a reoccuring basis:<br />';
                            }

                            echo 'Allow a trial period? <input type="checkbox"'; if($wpsc_membership_trial1_allow=='yes') {echo ' checked="checked"';} echo ' id="wpsc_membership_trial1_allow" name="wpsc_membership_trial1_allow" value="yes" onclick="if(jQuery(\'#wpsc_membership_trial1_allow\').is(\':checked\')){jQuery(\'#wpsc_membership_trial1_div\').show(\'slow\');jQuery(\'#wpsc_membership_trial2_prediv\').show(\'slow\');}else{jQuery(\'#wpsc_membership_trial2_allow\').prop(\'checked\',false);jQuery(\'#wpsc_membership_trial1_div\').hide(\'slow\');jQuery(\'#wpsc_membership_trial2_prediv\').hide(\'slow\');jQuery(\'#wpsc_membership_trial2_div\').hide(\'slow\');}" /><br />
                            <div id="wpsc_membership_trial2_prediv"'; if($wpsc_membership_trial1_allow=='no') {echo ' style="display:none;"';} echo '>Allow a 2nd trial period? <input type="checkbox" id="wpsc_membership_trial2_allow" '; if($wpsc_membership_trial2_allow=='yes') {echo ' checked="checked"';} echo ' name="wpsc_membership_trial2_allow" value="yes" onclick="if(jQuery(\'#wpsc_membership_trial2_allow\').is(\':checked\')){jQuery(\'#wpsc_membership_trial2_div\').show(\'slow\');}else{jQuery(\'#wpsc_membership_trial2_div\').hide(\'slow\');}" /><br /></div>
                            <br />
                            <div id="wpsc_membership_trial1_div"'; if($wpsc_membership_trial1_allow=='no') {echo ' style="display:none;"';} echo '>1st trial period, charge '.$devOptions['currency_symbol'].'<input type="text" value="'.$wpsc_membership_trial1_amount.'" name="wpsc_membership_trial1_amount" style="width:40px;" />'.$devOptions['currency_symbol_right'].' for <input type="text" value="'.$wpsc_membership_trial1_numberof.'" name="wpsc_membership_trial1_numberof" style="width:35px;" /> <select name="wpsc_membership_trial1_increment">

                                                    ';

                            $result2[0] = 'D';$result3[0] = 'Day(s)';
                            $result2[1] = 'W';$result3[1] = 'Week(s)';
                            $result2[2] = 'M';$result3[2] = 'Month(s)';
                            $result2[3] = 'Y';$result3[3] = 'Year(s)';

                            $i = 0;
                            foreach ($result2 as $pagg) {
                                    $option = '<option value="'.$pagg.'"';
                                    if($wpsc_membership_trial1_increment==$pagg) {
                                            $option .= ' selected="selected"';
                                    }
                                    $option .='>';
                                    $option .= $result3[$i];
                                    $option .= '</option>';
                                    echo $option;
                                    $i++;
                            }
                            

                            echo '
                            </select> and then:<br /></div>
                            <div id="wpsc_membership_trial2_div"'; if($wpsc_membership_trial2_allow=='no') {echo ' style="display:none;"';} echo '>2nd trial period, charge '.$devOptions['currency_symbol'].'<input type="text" value="'.$wpsc_membership_trial2_amount.'" name="wpsc_membership_trial2_amount" style="width:40px;" />'.$devOptions['currency_symbol_right'].' for <input type="text" value="'.$wpsc_membership_trial2_numberof.'" name="wpsc_membership_trial2_numberof" style="width:35px;" /> <select name="wpsc_membership_trial2_increment">

                                                    ';

                            $result2[0] = 'D';$result3[0] = 'Day(s)';
                            $result2[1] = 'W';$result3[1] = 'Week(s)';
                            $result2[2] = 'M';$result3[2] = 'Month(s)';
                            $result2[3] = 'Y';$result3[3] = 'Year(s)';

                            $i = 0;
                            foreach ($result2 as $pagg) {
                                    $option = '<option value="'.$pagg.'"';
                                    if($wpsc_membership_trial2_increment==$pagg) {
                                            $option .= ' selected="selected"';
                                    }
                                    $option .='>';
                                    $option .= $result3[$i];
                                    $option .= '</option>';
                                    echo $option;
                                    $i++;
                            }


                            echo '
                            </select> and then:<br /></div>
                            Regularly charge '.$devOptions['currency_symbol'].'<input type="text" value="'.$wpsc_membership_regular_amount.'" name="wpsc_membership_regular_amount" style="width:40px;" />'.$devOptions['currency_symbol_right'].' every <input type="text" value="'.$wpsc_membership_regular_numberof.'" name="wpsc_membership_regular_numberof" style="width:35px;" /> <select name="wpsc_membership_regular_increment">

                                                    ';

                            $result2[0] = 'D';$result3[0] = 'Day(s)';
                            $result2[1] = 'W';$result3[1] = 'Week(s)';
                            $result2[2] = 'M';$result3[2] = 'Month(s)';
                            $result2[3] = 'Y';$result3[3] = 'Year(s)';

                            $i = 0;
                            foreach ($result2 as $pagg) {
                                    $option = '<option value="'.$pagg.'"';
                                    if($wpsc_membership_regular_increment==$pagg) {
                                            $option .= ' selected="selected"';
                                    }
                                    $option .='>';
                                    $option .= $result3[$i];
                                    $option .= '</option>';
                                    echo $option;
                                    $i++;
                            }


                            echo '
                            </select> indefinitely.
                            </td>
                            <td><div style="width:300px;">wpsc Membership PRO addon.  Sell memberships &amp; subscriptions.</div></td>
                            </tr>';
                        }

                        echo '
			<tr';if($devOptions['storetype']=='Digital Goods Only') {echo ' style="display:none;"';}echo'><td><h3>Use<br />Inventory? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-333" /><div class="tooltip-content" id="example-content-333">Does this product have a limited number available?  If so, set this to yes to use the inventory to tell customers when your product is out of stock.</div></h3></td>
			<td><br /><p><label for="wpStoreCartproduct_useinventory_yes"><input type="radio" id="wpStoreCartproduct_useinventory_yes" name="wpStoreCartproduct_useinventory" value="1" '; if ($wpStoreCartproduct_useinventory == 1) { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="wpStoreCartproduct_useinventory_no"><input type="radio" id="wpStoreCartproduct_useinventory_no" name="wpStoreCartproduct_useinventory" value="false" '; if ($wpStoreCartproduct_useinventory == 0) { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label>';

                        
			echo '
                        <div style="margin-right:0px;position:relative;top:-40px;display:block;float:right;min-width:200px;min-height:40px;width:200px;height:40px;">
			<div';if($devOptions['storetype']=='Digital Goods Only') {echo ' style="display:none;"';}echo'>
			<strong>Inventory Quantity:</strong> <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5">If you\'re selling a limited number of a product (for example, a tangible item with a limited stock, or a digital product that you are only selling a limited number of copies.)</div><br />
			<input type="text" name="wpStoreCartproduct_inventory" style="width: 120px;" value="'.$wpStoreCartproduct_inventory.'" />  <br />
			</div>
                        </div>
                        ';

                        echo '</p></td>
			<td class="tableDescription"><p>Set to no for unlimited purchases or<br /> yes if you have a limited amount to sell.</p></td>
			</td></tr>
                        ';

			echo '
			<tr';if($devOptions['storetype']=='Digital Goods Only') {echo ' style="display:none;"';}echo'>
			<td><h3>Weight &<br />Dimensions: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2995" /><div class="tooltip-content" id="example-content-2995">If this is a physical product, enter in the products width, length, height, and weight.  If it is a digital product, keep these values at 0.</div></h3></td>
			<td><div id="wpscdimensions"><br />Weight: <input type="text" name="wpStoreCartproduct_weight" style="width: 58px;" value="'.$wpStoreCartproduct_weight.'" />  &nbsp; &nbsp; &nbsp; &nbsp; Height: <input type="text" name="wpStoreCartproduct_height" style="width: 58px;" value="'.$wpStoreCartproduct_height.'" /><br />Length: <input type="text" name="wpStoreCartproduct_length" style="width: 58px;" value="'.$wpStoreCartproduct_length.'" />  &nbsp; &nbsp; &nbsp; &nbsp; Width: <input type="text" name="wpStoreCartproduct_width" style="width: 58px;" value="'.$wpStoreCartproduct_width.'" />  &nbsp; &nbsp; &nbsp; &nbsp;</div></td>
			<td><div style="width:300px;">The physical details of the product.</div></td>
			</tr>';

			echo '
			<tr>
			<td><h3>Category <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6">Categories allow you to keep products in logically seperated order so that they are easier to find.</div></h3>
                        <h3>Tags <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7">Think of a word or phrase that describes your product: that is a tag.  Now use a comma to seperate each of these tags.</div></h3>
                        </td>
			<td><br /><select name="wpStoreCartproduct_category">
			 <option value="">
						';
			
			esc_attr(__('Select page'));
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
			</select> &nbsp; &nbsp; &nbsp; Tags: <input type="text" name="wpStoreCartproduct_tags" style="width: 200px;" value="'.$wpStoreCartproduct_tags.'" />
                        </td>
			<td><div style="width:300px;">The category the product belongs to.  Use a comma seperated list of tags to add additional categories.</div></td>
			</tr>';	

			echo '
			<tr';if($devOptions['storetype']=='Physical Goods Only') {echo ' style="display:none;"';}echo'>
			<td><h3>Downloadable<br />Files: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-8" /><div class="tooltip-content" id="example-content-8">If your product is digital in nature, then you can distribute it as a digital download.  If you need to upload more than one file, just select them all in the file selection dialog.  All uploads are stored at: '.WP_CONTENT_DIR . '/uploads/wpstorecart/</div></h3></td>
			<td><input type="hidden" name="wpStoreCartproduct_download" style="width: 200px;" value="'.$wpStoreCartproduct_download.'" /><br />
			Upload file(s): <span id="spanSWFUploadButton"></span>
                        <div id="upload-progressbar-container">
                            <div id="upload-progressbar">
                            </div>
                        </div>
			</td>
			<td><div style="width:300px;">The filename of a downloadable product.  Leave this blank for physical products.  Max filesize is either: <strong>'.ini_get('post_max_size').' or '.ini_get('upload_max_filesize').'</strong>, whichever is lower. Do not put URLs or full paths here, only use the upload box.</div></td>
			</tr>';			
			
                        if($wpStoreCartproduct_thumbnail==''||!isset($wpStoreCartproduct_thumbnail)) {
                            $wpStoreCartproduct_thumbnail = plugins_url('/images/default_product_img.jpg' , __FILE__);
                        }
			echo '
			<tr>
			<td><h3>Product<br />Thumbnail: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-9" /><div class="tooltip-content" id="example-content-9">The main product image.  It will be used in multiple places.  It is recommend that the image have a 1:1 width and height ratio.  For example, 100px X 100px.</div></h3></td>
			<td><input type="hidden" name="wpStoreCartproduct_thumbnail" style="width: 250px;" value="'.$wpStoreCartproduct_thumbnail.'" /><br />
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
                                /* <![CDATA[ */
                                function delvar(keytodel) {
                                    jQuery.ajax({ url: "'.plugins_url('/php/delvar.php' , __FILE__).'", type:"POST", data:"delete="+keytodel, success: function(){
                                        jQuery("#"+keytodel).remove();
                                    }});
                                }

                                function addvar() {
                                    jQuery.ajax({ url: "'.plugins_url('/php/addvar.php' , __FILE__).'", type:"POST", data:"createnewvar="+jQuery("#createnewvar").val()+"&varvalue="+jQuery("#varvalue").val()+"&varprice="+jQuery("#varprice").val()+"&vardesc="+jQuery("#vardesc").val()+"&vartype="+jQuery("#vartype_yes").is(":checked")+"'.$codeForKeyToEditAjax.'&vardownloads="+jQuery("#wpStoreCartproduct_variation").val(), success: function(txt){
                                        if(jQuery("#vartype_yes").is(":checked")) {
                                            jQuery("#varholder").append("<tr id=\'"+txt+"\'><td><img onclick=\'delvar("+txt+");\' style=\'cursor:pointer;\' src=\''.plugins_url('/images/cross.png' , __FILE__).'\' /> <p id=\'varcat_"+txt+"\' class=\'edit\'>"+jQuery("#createnewvar").val()+"</p></td><td><p class=\'edit\' id=\'varvalue_"+txt+"\'>"+jQuery("#varvalue").val()+"</p></td><td><p class=\'edit\' id=\'varprice_"+txt+"\'>"+jQuery("#varprice").val()+"</p></td><td><p class=\'edit_area\' id=\'vardesc_"+txt+"\'>"+jQuery("#vardesc").val()+"</p></td></tr>");
                                        } else {
                                            jQuery("#varholder2").append("<tr id=\'"+txt+"\'><td><img onclick=\'delvar("+txt+");\' style=\'cursor:pointer;\' src=\''.plugins_url('/images/cross.png' , __FILE__).'\' /> <p id=\'varcat_"+txt+"\' class=\'edit\'>"+jQuery("#createnewvar").val()+"</p></td><td><p class=\'edit\' id=\'varvalue_"+txt+"\'>"+jQuery("#varvalue").val()+"</p></td><td><p class=\'edit_area\' id=\'vardesc_"+txt+"\'>"+jQuery("#vardesc").val()+"</p></td></tr>");
                                        }
                                    }});
                                }

                                jQuery(document).ready(function($) {
                                    $(".edit").live("click", function() {
                                         $(".edit").editable("'.plugins_url('/php/varedit.php' , __FILE__).'", {
                                             indicator : "Saving...",
                                             tooltip   : "Click to edit...",
                                             cancel    : "Cancel",
                                             submit    : "OK",
                                             style     : "cursor: pointer;"
                                         });
                                    });

                                    $(".edit_area").live("click", function() {
                                         $(".edit_area").editable("'.plugins_url('/php/varedit.php' , __FILE__).'", {
                                             type      : "textarea",
                                             cancel    : "Cancel",
                                             submit    : "OK",
                                             style     : "cursor: pointer;",
                                             indicator : "<img src=\"'.plugins_url('/images/loader.gif' , __FILE__).'\">",
                                             tooltip   : "Click to edit..."
                                         });
                                    });

                                     $(".edit").editable("'.plugins_url('/php/varedit.php' , __FILE__).'", {
                                         indicator : "Saving...",
                                         tooltip   : "Click to edit...",
                                         cancel    : "Cancel",
                                         submit    : "OK",
                                         style     : "cursor: pointer;"
                                     });
                                     $(".edit_area").editable("'.plugins_url('/php/varedit.php' , __FILE__).'", {
                                         type      : "textarea",
                                         cancel    : "Cancel",
                                         submit    : "OK",
                                         style     : "cursor: pointer;",
                                         indicator : "<img src=\"'.plugins_url('/images/loader.gif' , __FILE__).'\">",
                                         tooltip   : "Click to edit..."
                                     });

                                 });

                                /* ]]> */
                            </script>
                            
                            <br style="clear:both;" />

                            </div>
                            <div id="tab2" class="tab_content">
                                                        <h2>Product Variations &amp; Attributes</h2>
                            ';

                            echo '
                            <p style="width:433px;max-width:533px;">Only display the Add to Cart button for this product when the variations are also displayed?<br />&nbsp; &nbsp; &nbsp; &nbsp; <label for="enableproduct_display_add_to_cart_variations"><input type="radio" id="enableproduct_display_add_to_cart_variations_yes" name="enableproduct_display_add_to_cart_variations" value="yes" '; if ($display_add_to_cart_at_all_times == "yes") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enableproduct_display_add_to_cart_variations_no"><input type="radio" id="enableproduct_display_add_to_cart_variations_no" name="enableproduct_display_add_to_cart_variations" value="no" '; if ($display_add_to_cart_at_all_times == "no") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No (always displays add to cart buttons)</label> </p>
                            ';

                            echo'

                            <table class="widefat">
                            <thead><tr><th>Simple or Advanced</th><th>Variation Category</th><th>One Possible Value</th><th id="varpriceth">Price Variation</th><th>Description</th><th';if($devOptions['storetype']=='Physical Goods Only') {echo ' style="display:none;"';}echo'>Downloads</th></tr></thead><tbody>
                            <tr><td><img onclick="addvar();" style="cursor:pointer;" src="'.plugins_url('/images/add.png' , __FILE__).'" /><br /><br /><br /><p><label for="vartype_yes"><input onclick="jQuery(\'#varpricetd\').show(\'slow\');jQuery(\'#varpriceth\').show(\'slow\');" type="radio" id="vartype_yes" name="vartype" value="simple" checked="checked" /> Simple</label><br /><label for="vartype_no"><input type="radio" onclick="jQuery(\'#varpricetd\').hide(\'slow\');jQuery(\'#varpriceth\').hide(\'slow\');" id="vartype_no" name="vartype" value="advanced" /> Advanced</label></td><td> <input type="text" style="width:80%;" name="createnewvar" id="createnewvar" /><br /><i>The name of the variation or attribute, for example: color, size, version, etc.</i></td><td><input type="text" name="varvalue" style="width:80%;" id="varvalue" /><br /><i>Here you should put one of the possible variations.  For example, if your variation was <strong>Color</strong>, then here you put a color, such as <strong>Red</strong>.</i></td><td id="varpricetd"><input type="text" name="varprice" id="varprice" value="0.00" /><br /><i>The amount that the price changes when a customer selects this variation.  Put 0 here if the price is the same as normal, put -21.90 to subtract from the total, or 35.99 to add to the cost of the item.</i></td><td><textarea id="vardesc" name="vardesc" style="width:80%;"></textarea><br /><i>An explaination of the variation so that customers know what to choose.</i></td><td';if($devOptions['storetype']=='Physical Goods Only') {echo ' style="display:none;"';}echo'>
                            <input type="hidden" id="wpStoreCartproduct_variation" name="wpStoreCartproduct_variation" style="width: 180px;" value="" />
                            Upload file(s): <span id="spanSWFUploadButton3"></span>
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
                            <h3>Simple Variations</h3>
                            <table class="widefat" id="varholder">
                                <thead><tr><th>Variation Category</th><th>One Possible Value</th><th>Price Variation</th><th>Description</th></tr></thead><tbody>';

                                $table_name3 = $wpdb->prefix . "wpstorecart_meta";
                                $grabrecord = "SELECT * FROM `{$table_name3}` WHERE `type`='productvariation' AND `foreignkey`={$_GET['keytoedit']};";

                                $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                                if(isset($results)) {
                                        foreach ($results as $result) {
                                            $theKey = $result['primkey'];
                                            $exploder = explode('||', $result['value']);
                                            if($exploder[5]!='advanced') {
                                                echo '<tr id="'.$theKey.'"><td> <img onclick="delvar('.$theKey.');" style="cursor:pointer;" src="'.plugins_url('/images/cross.png' , __FILE__).'" /> <p class="edit" id="varcat_'.$theKey.'">'.$exploder[0].'</p></td><td><p class="edit" id="varvalue_'.$theKey.'">'.$exploder[1].'</p></td><td><p class="edit" id="varprice_'.$theKey.'">'.$exploder[2].'</td><td><p class="edit_area" id="vardesc_'.$theKey.'">'.$exploder[3].'</p></td></tr>';
                                            }
                                        }
                                }

                            echo '
                            </table>
                            <br style="clear:both;" />
                            ';

                            echo '
                            <h3>Advanced Variations</h3>
                            <table class="widefat" id="varholder2">
                                <thead><tr><th>Variation Category</th><th>One Possible Value</th><th>Description</th></tr></thead><tbody>';

                                $table_name3 = $wpdb->prefix . "wpstorecart_meta";
                                $grabrecord = "SELECT * FROM `{$table_name3}` WHERE `type`='productvariation' AND `foreignkey`={$_GET['keytoedit']} ORDER BY `value` ASC;";

                                $results = $wpdb->get_results( $grabrecord , ARRAY_A );
                                if(isset($results)) {
                                        foreach ($results as $result) {
                                            $theKey = $result['primkey'];

                                            $exploder = explode('||', $result['value']);

                                            if($exploder[5]=='advanced') {
                                                if(!isset($numberOfCategory[$this->slug($exploder[0])])) {
                                                    $numberOfCategory[$this->slug($exploder[0])] = 0; // will be 1 in just a second
                                                }
                                                $numberOfCategory[$exploder[0]] =  $numberOfCategory[$exploder[0]] + 1;
                                                echo '<tr id="'.$theKey.'"><td> <img onclick="delvar('.$theKey.');" style="cursor:pointer;" src="'.plugins_url('/images/cross.png' , __FILE__).'" /> <p class="edit" id="varcat_'.$theKey.'">'.$exploder[0].'</p></td><td><p class="edit" id="varvalue_'.$theKey.'">'.$exploder[1].'</p></td><td><p class="edit_area" id="vardesc_'.$theKey.'">'.$exploder[3].'</p></td></tr>';
                                            }
                                        }
                                }

                            echo '
                            </table>
                            <br style="clear:both;" />
                            ';
                            echo '
                            <h3>Prices for Advanced Variations </h3>
                            <p>Don\'t assign prices here until you\'re done adding new "Variation Categories", or you will invalidate all the previously set prices. If you\'ve recently added additional Advanced Variations then resubmit the product to assign prices to the newly added variations.</p>
                            <ul>';

                            // Product variations
                            $table_name30 = $wpdb->prefix . "wpstorecart_meta";
                            $grabrecord = "SELECT * FROM `{$table_name30}` WHERE `type`='productvariation' AND `foreignkey`={$_GET['keytoedit']} ORDER BY `value` ASC;";

                            $vresults = $wpdb->get_results( $grabrecord , ARRAY_A );

                            if(isset($vresults)) {
                                $voutput = NULL;
                                $variationStorage = array();
                                $varStorageCounter = 0;
                                foreach ($vresults as $vresult) {
                                    $theKey = $vresult['primkey'];
                                    $exploder = explode('||', $vresult['value']);
                                    if($exploder[5]=='advanced') {
                                        $variationStorage[$varStorageCounter]['variationkey'] = $theKey;
                                        $variationStorage[$varStorageCounter]['variationname'] = $exploder[0];
                                        $variationStorage[$varStorageCounter]['variationvalue'] = $exploder[1];
                                        $variationStorage[$varStorageCounter]['variationprice'] = $exploder[2];
                                        $variationStorage[$varStorageCounter]['variationdesc'] = $exploder[3];
                                        $variationStorage[$varStorageCounter]['variationtype'] = $exploder[5];
                                        //$voutput .= '<li>'.$exploder[0].' '.$exploder[1].' '.$exploder[2].' '.$exploder[3].'</li>';
                                        $varStorageCounter++;
                                    }
                                }
                            }

                             $output .= '
                                <script type="text/javascript">
                                    //<![CDATA[
                                        
                                        var registerAdvVarName = new Array();

                                        function updateAdvVarPrice() {
                                            var query_string = "advvarprice=" + jQuery("#advvarprice").val() + "&advvarkey='.$_GET['keytoedit'].'";
                                            for(var i in registerAdvVarName)
                                            {
                                                query_string += "&advvarcombo[]=" + jQuery("#variation_"+registerAdvVarName[i]).val();
                                            }
                                            jQuery.ajax({ url: "'.plugins_url('/php/updateadvvar.php' , __FILE__).'", type:"POST", data:query_string, success: function(){
                                                
                                            }});
                                        }

                                        function loadAdvVarPrice() {
                                            var query_string = "advvarkey='.$_GET['keytoedit'].'";
                                            for(var i in registerAdvVarName)
                                            {
                                                query_string += "&advvarcombo[]=" + jQuery("#variation_"+registerAdvVarName[i]).val();
                                            }
                                            jQuery.ajax({ url: "'.plugins_url('/php/loadadvvar.php' , __FILE__).'", type:"POST", data:query_string, success: function(txt){
                                                jQuery("#advvarprice").val(txt);
                                            }});
                                        }

                                    //]]>
                                </script>
                             ';
                            $variationTest = array();
                            $variationCounter = 0;
                            if(@is_array($variationStorage) && @isset($variationStorage[0])) {
                                if(isset($variationStorage)) {
                                        foreach ($variationStorage as $variationStorageCycle) {
                                            if(@!isset($variationTest[$this->slug($variationStorageCycle['variationname'])])) {

                                             $output .= '
                                                <script type="text/javascript">
                                                    //<![CDATA[
                                                        registerAdvVarName['.$variationCounter.'] = "'.$this->slug($variationStorageCycle['variationname']).'";
                                                    //]]>
                                                </script>
                                             ';
                                             $voutput .= '
                                                <li>'.$variationStorageCycle['variationname'].' - <select name="variation_'.$this->slug($variationStorageCycle['variationname']).'" id="variation_'.$this->slug($variationStorageCycle['variationname']).'" onblur="updateAdvVarPrice();" onchange="loadAdvVarPrice();">';

                                            }
                                            if(isset($variationStorage)) {
                                                    foreach ($variationStorage as $currentVariation) {
                                                            if (($currentVariation['variationtype']=='advanced' && $currentVariation['variationname']==$variationStorageCycle['variationname']) && $variationTest[$this->slug($variationStorageCycle['variationname'])]!=true) {
                                                                $option = '<option value="'.$this->slug($currentVariation['variationvalue']).'"';
                                                                $option .='>';
                                                                $option .= $currentVariation['variationvalue'];
                                                                $option .= '</option>';
                                                                $voutput .=  $option;
                                                            }
                                                    }
                                            }

                                            if(@!isset($variationTest[$this->slug($variationStorageCycle['variationname'])])) {
                                                $voutput .=  '
                                                </select>   </li>';
                                                $variationTest[$this->slug($variationStorageCycle['variationname'])] = true;
                                            }
                                            $variationCounter++;
                                            $variationTest[$this->slug($variationStorageCycle['variationname'])] = true;
                                        }


                                    }
                            }
                            // Product variations

                            echo $output;
                            echo $voutput;

                            echo '
                            </ul>
                            <strong>Price for this variation combination:</strong> '.$devOptions['currency_symbol'].'<input type="text" value="" style="width:75px;" id="advvarprice" onclick="updateAdvVarPrice();" onblur="updateAdvVarPrice();" onchange="updateAdvVarPrice();" />'.$devOptions['currency_symbol_right'].'

                            <br style="clear:both;" />
                            ';

                        } else {
                            echo '
                            <br style="clear:both;" />
                            </div>
                            <div id="tab2" class="tab_content">
                            <h2>Product Variations &amp; Attributes</h2>
                            <p>Once you\'ve created your product, then you are able to create variations such as multiple sizes, colors, versions, upgrades, and downgrades, all with specific prices.  Save the product now to begin adding variations to it.</p>
                            ';
                        }
                        
                        echo '</div>';
                        if($devOptions['storetype']!='Physical Goods Only' && $isanedit==true){
                            echo ' <div id="tab3" class="tab_content">   ';

                            echo '
                            <h3>Downloads</h3>
                            ';

                            echo $this->listProductDownloads($_GET['keytoedit']);

                            echo '<br style="clear:both;" />
                            <h3>Serial Numbers</h3>
                            <p>Leave blank if you do not need to issue serial numbers for each purchase, otherwise, place each serial number on it\'s own line, then each time a customer buys this product, they will be issued that serial number and it will be made unavailable</p>

                            Unused serial numbers:<br /> <textarea style="width:400px;height:125px" name="wpStoreCartproduct_serial_numbers">'.base64_decode($wpStoreCartproduct_serial_numbers).'</textarea><br /><br />
                            Used serial numbers:<br /> <textarea style="width:400px;height:125px" name="wpStoreCartproduct_serial_numbers_used">'.base64_decode($wpStoreCartproduct_serial_numbers_used).'</textarea><br />
                            ';
                            echo '</div> <br style="clear:both;" />';
                        }
                        echo '
                        <div id="tab4" class="tab_content">
                            <h3>Picture Gallery</h3>';

                             $preresults = $wpdb->get_results("SELECT * FROM {$table_name30} WHERE `type`='wpsc_product_gallery' AND `foreignkey`='{$_GET['keytoedit']}'", ARRAY_A);
                             if(isset($_POST['wpStoreCartproduct_download_pg'])) {
                                    $icounter = 1;
                                    $ucounter = 0;
                                    $finalSlideShowCode ='';
                                    while($icounter <= 200) { // Max 200 images per product
                                            if(@isset($_POST['theimagefor_'.$ucounter])) {
                                                    if($_POST['theimagefor_'.$ucounter]!='' || $_POST['theimagefor_'.$ucounter]!=NULL) {
                                                            $finalSlideShowCode .= $_POST['theimagefor_'.$ucounter].'<<<'.$_POST['thelinkfor_'.$ucounter].'||';
                                                    }
                                                    $icounter++;
                                                    $ucounter++;
                                            } else {
                                                    $finalSlideShowCode .= $_POST['wpStoreCartproduct_download_pg'];
                                                    $icounter = 201; // This breaks us out of the loop if needed, after adding any new images to the database
                                            }
                                    }

                                    if($preresults==false) {
                                            $insert = "INSERT INTO `{$table_name30}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".$finalSlideShowCode."', 'wpsc_product_gallery', '{$_GET['keytoedit']}');";
                                    } else {
                                            $insert = "UPDATE  `{$table_name30}` SET `value` = '".$finalSlideShowCode."' WHERE `type`='wpsc_product_gallery' AND `foreignkey`='{$_GET['keytoedit']}';";
                                    }

                                    $newresults = $wpdb->query( $insert );
                                    $preresults = $wpdb->get_results("SELECT * FROM {$table_name30} WHERE `type`='wpsc_product_gallery' AND `foreignkey`='{$_GET['keytoedit']}';", ARRAY_A);

                            }

                            echo '
                            <tr><td>Upload new images:</td><td><input type="hidden" name="wpStoreCartproduct_download_pg" id="wpStoreCartproduct_download_pg" style="width: 200px;" value="" /><br />
				Upload a file: <span id="spanSWFUploadButton4"></span>
							<div id="upload-progressbar-container4">
								<div id="upload-progressbar4">
								</div>
							</div>
                            </td></tr>
                            <table class="widefat">
                            <thead><tr><th> </th><th>Image</th></tr></thead><tbody id="linksforimages"></tbody></table>
                            <input type="hidden" value="0" id="numberofslideshowimages" name="numberofslideshowimages" />
                            <input type="hidden" value="'.$preresults[0]['value'].'" id="wpStoreCartproduct_download_old" name="wpStoreCartproduct_download_old" />
                            <script type="text/javascript">
                            //<![CDATA[

                            jQuery(document).ready(function($) {

                                    var theSlideShowInfo = $("#wpStoreCartproduct_download_old").val();
                                    var brokenstring = theSlideShowInfo.split("||");
                                    var theContentForOutput = "";
                                    var numberofslideshowimages = 0;
                                    for(var i in brokenstring) {
                                            if(brokenstring[i]!="") {
                                                    var newbrokenstring = brokenstring[i].split("<<<");
                                                    if (newbrokenstring[1]==undefined) {
                                                            newbrokenstring[0] = brokenstring[i];
                                                            newbrokenstring[1] = "";
                                                    }
                                                    theContentForOutput = theContentForOutput + \'<tr id="slideshowimagetr_\'+[i]+\'"><td><img src="'.plugins_url('/wpstorecart/images/cross.png').'" alt="delete" style="cursor:pointer;" onclick="jQuery(\\\'#slideshowimagetr_\'+[i]+\'\\\').hide(\\\'explode\\\', 1000);jQuery(\\\'#thelinkfor_\'+[i]+\'\\\').val(\\\'\\\');jQuery(\\\'#theimagefor_\'+[i]+\'\\\').val(\\\'\\\');" /> <input type="text" value="\'+newbrokenstring[1]+\'" name="thelinkfor_\'+i+\'" id="thelinkfor_\'+i+\'" style="display:none;" /><input type="hidden" value="\'+newbrokenstring[0]+\'" name="theimagefor_\'+i+\'" id="theimagefor_\'+i+\'" /></td><td><img src="'.get_bloginfo('url'). '/wp-content/uploads/wpstorecart/\'+newbrokenstring[0]+\'" alt="" style="height:250px;max-height:250px;" /></td></tr>\';
                                            }
                                            $(\'#linksforimages\').replaceWith(\'<tbody id="linksforimages">\'+theContentForOutput+\'</tbody>\');
                                            numberofslideshowimages = i;
                                    }
                                    $("#numberofslideshowimages").val(numberofslideshowimages);
                            });

                            //]]>
                            </script>

                        </div> 
			<div class="submit">
                        ';

                             $poststatus = NULL;
                             if($isanedit == true) {
                                 if (isset($_POST['wpscpoststatus'])) {
                                    if($_POST['wpscpoststatus']=='publish') {
                                        $my_post = array();
                                        $my_post['ID'] = $result['postid'];
                                        $my_post['post_title'] = stripslashes($wpStoreCartproduct_name);
                                        $my_post['post_status'] = 'publish';
                                        wp_update_post( $my_post );
                                    }
                                    if($_POST['wpscpoststatus']=='draft') {
                                        $my_post = array();
                                        $my_post['ID'] = $result['postid'];
                                        $my_post['post_title'] = stripslashes($wpStoreCartproduct_name);
                                        $my_post['post_status'] = 'draft';
                                        wp_update_post( $my_post );
                                    }
                                    if($_POST['wpscpoststatus']=='orphan') {
					// Create our PAGE in draft mode in order to get the POST ID
					$my_post = array();
					$my_post['post_title'] = stripslashes($wpStoreCartproduct_name);
					$my_post['post_type'] = 'page';
					$my_post['post_content'] = '[wpstorecart display="product" primkey="'.$_GET['keytoedit'].'"]';
					$my_post['post_status'] = 'draft';
					$my_post['post_author'] = 1;
					$my_post['post_parent'] = $devOptions['mainpage'];

					// Insert the PAGE into the WP database
					$thePostID = wp_insert_post( $my_post );
					if($thePostID==0) {
						echo '<div class="updated"><p><strong>';
						_e("ERROR 4: wpStoreCart didn't like your data and failed to create a page for it! Make sure you create a product with at least a title.", "wpStoreCart");
						echo $wpdb->print_error();
						echo '</strong></p></div>';
						return false;
					} else {
                                            $update_sql = 'UPDATE `'. $wpdb->prefix . 'wpstorecart_products` SET `postid`='.$thePostID.' WHERE `primkey`='.$_GET['keytoedit'].';';
                                            $wpdb->query($update_sql);
                                            $result['postid'] = $thePostID;
                                        }

                                    }

                                 }

                                $product_post = get_post($result['postid'], ARRAY_A);
                                $highlight_button = NULL;
                                if($product_post['post_status']=='draft') {
                                    echo 'Product is saved as a <em>draft</em>. <br />';
                                    echo '<button class="button-primary" onclick="jQuery(\'#wpscpoststatus\').val(\'publish\');jQuery(\'#wpstorecartaddproductform\').submit();">Publish</button> ';
                                    $poststatus = 'draft';
                                } elseif($product_post['post_status']=='publish') {
                                    $highlight_button = '-primary';
                                    echo 'Product is <em>published</em>. <br />';
                                    echo '<button class="button" onclick="jQuery(\'#wpscpoststatus\').val(\'draft\');jQuery(\'#wpstorecartaddproductform\').submit();">Save as draft...</button> ';
                                    $poststatus = 'publish';
                                } else {
                                    echo 'Product is orphaned from it\'s page.  Click the "Create New Page" button to create a new page for your product, or you can continue to save the product orphaned. <br />';
                                    echo '<button class="button-primary" onclick="jQuery(\'#wpscpoststatus\').val(\'orphan\');jQuery(\'#wpstorecartaddproductform\').submit();">Create New Page...</button> ';
                                }

                             }

                            echo '<input type="hidden" name="wpscpoststatus" id="wpscpoststatus" value="'.$poststatus.'" /><input class="button'.$highlight_button.'" type="submit" name="addNewwpStoreCart_product" value="'; _e('Save product', 'wpStoreCart'); echo'" /></div>
			</form>
        		 </div></div>';
		
		}	
		// END Prints out the Add products admin page =======================================================================		
		
		
		
		
		//Prints out the Edit products admin page =======================================================================
        function printAdminPageEditproducts() {
			global $wpdb, $user_level;


			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
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
                        $this->wpstorecart_alert();

                       echo '

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
                                margin: 0 0 -5px 8px;
                                padding: 0;
                                float: left;
                                list-style: none;
                                height: 40px;
                                max-height: 40px;
                                width: 100%;
                                width:812px;
                                min-width:812px;
                            position:relative;
                            z-index:1;
                        }
                        ul.tabs li {
                                float: left;
                                margin: 0;
                                padding: 0;
                                height: 39px; /*--Subtract 1px from the height of the unordered list--*/
                                line-height: 39px; /*--Vertically aligns the text within the tab--*/
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
                                padding: 10px;

                        }
			</style>';


			echo '
		
			<script type="text/javascript">
                            /* <![CDATA[ */
			
                            var ischecked = false;

                            function SetAllCheckBoxes(FormName, FieldName, CheckValue) {
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


                        //]]>
                        </script>
                        ';
			echo '<div style="width:75%;max-width:75%;float:left;"><img src="'.plugins_url('/images/edit.png' , __FILE__).'" alt="" style="float:left;" /><h2 style="font-size:32px;">&nbsp;&nbsp;&nbsp;Edit Products <a href="http://wpstorecart.com/documentation/adding-editing-products/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2><br style="clear:both;" />
			
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
			</div></div>
			';
		
		}		
		//END Prints out the Edit products admin page =======================================================================
		


		//Prints out the Orders admin page =======================================================================
        function printAdminPageOrders() {
			global $wpdb, $user_info3;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			
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
			
			
			// To edit a previous order
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
                        $this->wpstorecart_alert();
			
			echo '
			<div style="width:75%;max-width:75%;float:left;"><form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">
			';
			if ($isanedit != true) {
				echo '<h2>Add a Order</h2>';
			} else {
				echo '<h2>Edit a Order</h2>Add a new order by <a href="admin.php?page=wpstorecart-orders">clicking here</a>.<br />';
			}
			
			echo '
                        <script type="text/javascript">
                            /* <![CDATA[ */
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
                            /* ]]> */
                        </script>
                        <table class="widefat">
			<thead><tr><th> </th><th>Order Status <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>"Dropped" means they added it to their cart, but never completed checkout.  "Order Recieved" means the customer successfully completed the checkout process, but an admin hasn\'t verified and approved the order yet.  "Pending" means the order is delayed until an admin changes the order status.  "Canceled" means the order was manually canceled by an admin.  "Completed" means the order is fulfilled, the payment was successfully recieved and approved.</h3></div></th><th>Cart Contents <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>The items that were in the customers shopping cart.  You can add or remove items if you need to modify or fulfill an order manually.</h3></div></th><th>Processor <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3"><h3>The payment gateway that was used in the transaction.</h3></div></th><th>Price <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4"><h3>The total price of everything added together in the shopping cart.</h3></div></th><th>Shipping <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5"><h3>The total shipping of everything in the shopping cart.</h3></div></th><th>User <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6"><h3>The Wordpress User ID of the purchaser.</h3></div></th><th>Email <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-8" /><div class="tooltip-content" id="example-content-8"><h3>The email address the customer used to make the purchase.</h3></div></th><th  style="display:none;">Affiliate <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7"><h3>The Wordpress user ID of the affiliate who is credited with driving the sale.</h3></div></th></tr></thead><tbody>
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
			
			esc_attr(__('Select page'));
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

                            esc_attr(__('Select product'));
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
                            </select><img src="'.plugins_url('/images/add.png' , __FILE__).'" style="cursor:pointer;" onclick="addItemToCart(jQuery(\'#addNewProduct\').val());" ></a><br />'.$this->splitOrderIntoProduct($keytoedit, 'edit');
                        }

                        if(isset($wpStoreCartwpuser) && $wpStoreCartwpuser!=0) {
                            $user_info3 = get_userdata($wpStoreCartwpuser);
                        }

                        echo '</td>
			<td><input type="text" name="wpStoreCartpaymentprocessor" style="width: 80%;" value="'.$wpStoreCartpaymentprocessor.'" /></td>
			<td><input type="text" name="wpStoreCartprice" style="width: 80%;" value="'.$wpStoreCartprice.'" /></td>
			<td><input type="text" name="wpStoreCartshipping" style="width: 80%;" value="'.$wpStoreCartshipping.'" /></td>
			<td><input type="text" name="wpStoreCartwpuser" style="width: 80%;" value="';if ($isanedit == true) {echo $user_info3->user_login;}; echo'" /></td>
			<td><input type="text" name="wpStoreCartemail" style="width: 80%;" value="'.$wpStoreCartemail.'" /></td>
			<td style="display:none;"><input type="text" name="wpStoreCartaffiliate" style="width: 80%;" value="'.$wpStoreCartaffiliate.'" /></td>
			</tr>';			
			
			echo '
			</tbody>
			</table>
			<br style="clear:both;" />
			<div class="submit">
			<input type="submit" name="addNewwpStoreCart_product" value="'; _e('Submit Order', 'wpStoreCart'); echo'" /></div>
			</form>
			';	
			
			echo '
			<br style="clear:both;" /><br />
			<h2>Edit Orders</h2>';

                        if(@isset($_GET['show'])) {
                            $_POST['show'] = $_GET['show'];
                        }

                        echo '<form action="" method="post">Show <select name="show"><option value="all">All Orders</option><option value="completed">Completed Purchases Only</option><option value="pending">Pending Orders Only</option><option value="refunded">Refunded Orders Only</option></select> Sort by <select name="orderby"><option value="`date`">date</option><option value="`wpuser`">user</option><option value="`orderstatus`">order status</option><option value="`affiliate`">affiliate</option><option value="`paymentprocessor`">processor</option><option value="`price`">price</option></select> in <select name="ordersort"><option value="DESC">descending</option><option value="ASC">ascending</option></select> order. <input type="submit" value="Submit"></input></form>';
			echo '<table class="widefat">
			<thead><tr><th>Date<br />Order #</th><th>Order Status</th><th>Cart Contents</th><th>Processor</th><th>Price<br /><i>(Shipping)</i></th><th>User<br /><i>Email</i></th><th>Affiliate</th></tr></thead><tbody>
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


                        if(@!isset($_POST['show'])) {
                            $whereclause = '';
                        } else {
                            if ($_POST['show']=='all') {
                                $whereclause = '';
                            }
                            if ($_POST['show']=='pending') {
                                $whereclause = 'WHERE `orderstatus`="Pending"';
                            }
                            if ($_POST['show']=='completed') {
                                $whereclause = 'WHERE `orderstatus`="Completed"';
                            }
                            if ($_POST['show']=='refunded') {
                                $whereclause = 'WHERE `orderstatus`="Refunded"';
                            }
                        }
			$grabrecord = "SELECT * FROM `{$table_name}` {$whereclause} ORDER BY {$orderby} {$ordersort};";
			
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
                                        if(isset($wpStoreCartaffiliate) && $wpStoreCartaffiliate!=0) {
                                            global $user_info4;
                                            $user_info4 = get_userdata($wpStoreCartaffiliate);
                                        }
                                        if(isset($wpStoreCartwpuser) && $wpStoreCartwpuser!=0) {
                                            $user_info3 = get_userdata($wpStoreCartwpuser);
                                        } 
                                        $wpStoreCartdate = $result['date'];



					echo "
					<tr>
					<td style=\"min-width:80px;\"><strong>{$wpStoreCartdate}</strong><br />{$result['primkey']} <a href=\"admin.php?page=wpstorecart-orders&keytoedit={$result['primkey']}\"><img src=\"".plugins_url('/images/pencil.png' , __FILE__)."\" alt=\"\" /></a> <a onclick=\"if (! confirm('Are you sure you want to delete this order?')) { return false;}\" href=\"admin.php?page=wpstorecart-orders&keytodelete={$result['primkey']}\"><img src=\"".plugins_url('/images/cross.png' , __FILE__)."\" alt=\"\" /></a></td>
					<td>{$wpStoreCartorderstatus}</td>
					<td>".$this->splitOrderIntoProduct($result['primkey'])."</td>
					<td>{$wpStoreCartpaymentprocessor}</td>";
                                        
                                        if($wpStoreCartpaymentprocessor!='PayPal Subscription') {
                                            echo "<td><strong>{$devOptions['currency_symbol']}{$wpStoreCartprice}{$devOptions['currency_symbol_right']}</strong>"; if($wpStoreCartshipping!=0.00) {echo"<br /><i>({$devOptions['currency_symbol']}{$wpStoreCartshipping}{$devOptions['currency_symbol_right']})</i>";} echo"</td>";
                                        } else {
                                            // This code checks to see if we will be potentially displaying subscription products with either the price or add to cart button visible.  If so, we query each product for subscription information
                                            $wpsc_price_type = 'charge';
                                            $membership_value = '';
                                            $theProductToCheck = explode('*', $wpStoreCartcartcontents);
                                            echo '<td>';
                                            if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php') && (isset($theProductToCheck[0]))){
                                                $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                                                $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$theProductToCheck[0]};";
                                                $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                                                if(isset($resultsMembership)) {
                                                    foreach ($resultsMembership as $pagg) {
                                                        $membership_primkey = $pagg['primkey'];
                                                        $membership_value = $pagg['value'];
                                                    }
                                                    if($membership_value!='') {
                                                        global $wpdb, $wpsc_membership_product_id, $purchaser_user_id, $purchaser_email, $membershipOptions, $wpsc_table_name, $wpsc_self_path, $wpsc_paypal_testmode, $wpsc_paypal_ipn, $wpsc_membership_product_name, $wpsc_membership_product_number, $wpsc_button_classes, $wpsc_paypal_currency_code, $wpsc_paypal_email, $wpsc_price_type,$wpsc_membership_trial1_allow, $wpsc_membership_trial2_allow, $wpsc_membership_trial1_amount , $wpsc_membership_trial2_amount, $wpsc_membership_regular_amount,$wpsc_membership_trial1_numberof,$wpsc_membership_trial2_numberof,$wpsc_membership_regular_numberof,$wpsc_membership_trial1_increment,$wpsc_membership_trial2_increment,$wpsc_membership_regular_increment;
                                                        $theExploded = explode('||', $membership_value);
                                                        // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                                                        $wpsc_membership_product_id = $results[0]['primkey'];
                                                        $wpsc_price_type = $theExploded[0];
                                                        $wpsc_membership_trial1_allow = $theExploded[1];
                                                        $wpsc_membership_trial2_allow = $theExploded[2];
                                                        $wpsc_membership_trial1_amount = $theExploded[3];
                                                        $wpsc_membership_trial2_amount = $theExploded[4];
                                                        $wpsc_membership_regular_amount = $theExploded[5];
                                                        $wpsc_membership_trial1_numberof = $theExploded[6];
                                                        $wpsc_membership_trial2_numberof = $theExploded[7];
                                                        $wpsc_membership_regular_numberof = $theExploded[8];
                                                        $wpsc_membership_trial1_increment = $theExploded[9];
                                                        $wpsc_membership_trial2_increment = $theExploded[10];
                                                        $wpsc_membership_regular_increment = $theExploded[11];
                                                        if($wpsc_membership_trial1_increment=='D'){$wpsc_membership_trial1_increment_display=$devOptions['day'];}
                                                        if($wpsc_membership_trial2_increment=='D'){$wpsc_membership_trial2_increment_display=$devOptions['day'];}
                                                        if($wpsc_membership_regular_increment=='D'){$wpsc_membership_regular_increment_display=$devOptions['day'];}
                                                        if($wpsc_membership_trial1_increment=='W'){$wpsc_membership_trial1_increment_display=$devOptions['week'];}
                                                        if($wpsc_membership_trial2_increment=='W'){$wpsc_membership_trial2_increment_display=$devOptions['week'];}
                                                        if($wpsc_membership_regular_increment=='W'){$wpsc_membership_regular_increment_display=$devOptions['week'];}
                                                        if($wpsc_membership_trial1_increment=='M'){$wpsc_membership_trial1_increment_display=$devOptions['month'];}
                                                        if($wpsc_membership_trial2_increment=='M'){$wpsc_membership_trial2_increment_display=$devOptions['month'];}
                                                        if($wpsc_membership_regular_increment=='M'){$wpsc_membership_regular_increment_display=$devOptions['month'];}
                                                        if($wpsc_membership_trial1_increment=='Y'){$wpsc_membership_trial1_increment_display=$devOptions['year'];}
                                                        if($wpsc_membership_trial2_increment=='Y'){$wpsc_membership_trial2_increment_display=$devOptions['year'];}
                                                        if($wpsc_membership_regular_increment=='Y'){$wpsc_membership_regular_increment_display=$devOptions['year'];}
                                                    }
                                                }
                                                echo '<ul class="wpsc-product-info">';
                                                if($wpsc_membership_trial1_allow=='yes') {
                                                    echo"<li class=\"list-item-price\">{$devOptions['trial_period_1']} {$devOptions['currency_symbol']}{$wpsc_membership_trial1_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial1_numberof} {$wpsc_membership_trial1_increment_display}</li>";
                                                }
                                                if($wpsc_membership_trial1_allow=='yes') {
                                                    echo "<li class=\"list-item-price\">{$devOptions['trial_period_2']} {$devOptions['currency_symbol']}{$wpsc_membership_trial2_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial2_numberof} {$wpsc_membership_trial2_increment_display}</li>";
                                                }
                                                echo "<li class=\"list-item-price\">{$devOptions['subscription_price']} {$devOptions['currency_symbol']}{$wpsc_membership_regular_amount}{$devOptions['currency_symbol_right']} {$devOptions['every']} {$wpsc_membership_regular_numberof} {$wpsc_membership_regular_increment_display}</li>";
                                                echo '</ul>';
                                            }

                                            echo "</td>";
                                        }

                                        echo"
					<td>";

                                        if(isset($wpStoreCartwpuser) && $wpStoreCartwpuser!=0) {
                                            echo "<a href=\"user-edit.php?user_id={$wpStoreCartwpuser}\">{$user_info3->user_login}</a><br />{$wpStoreCartemail}</td>";
                                        } else {
                                            echo '<strong>Guest</strong><br />'.$wpStoreCartemail.'</td>';
                                        }
                                        echo "
					<td>";
                                        if(isset($wpStoreCartaffiliate) && $wpStoreCartaffiliate!=0) {
                                            echo "<a href=\"user-edit.php?user_id={$wpStoreCartaffiliate}\">{$user_info4->user_login}</a></td>";
                                        } else {
                                            echo '</td>';
                                        }
                                        echo "</td>
					</tr>";	
					

				}
			}					
			
			
		

			
			echo '
			</tbody>
			</table></div>
			<br style="clear:both;" />';	
		
		}	
		// END Prints out the Orders admin page =======================================================================		
				
		



	
		//Prints out the Categories admin page =======================================================================
        function printAdminPageCategories() {
			global $wpdb, $testing_mode;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			
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
					if(!is_numeric($wpStoreCartCategoryParent)) {
						$wpStoreCartCategoryParent = 0;
					}
					if(!is_numeric($wpStoreCartCategoryPostID)) {
						$wpStoreCartCategoryPostID = 0;
					}			

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
						if($testing_mode==true) {
							echo '<pre>'.$updateSQL.'</pre>';
						}
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
					
					if(!is_numeric($wpStoreCartCategoryParent)) {
						$wpStoreCartCategoryParent = 0;
					}
					if(!is_numeric($wpStoreCartCategoryPostID)) {
						$wpStoreCartCategoryPostID = 0;
					}					

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
						if($testing_mode==true) {
							echo '<pre>'.$insert.'</pre>';
						}						
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
                        $this->wpstorecart_alert();
			
			echo '
			<div style="width:75%;max-width:75%;float:left;"><form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">
			';

                        echo '<img src="'.plugins_url('/images/categories.png' , __FILE__).'" alt="" style="float:left;" /><h2 style="font-size:32px;">&nbsp;&nbsp;&nbsp;Categories</h2><br style="clear:both;" /><a href="admin.php?page=wpstorecart-categories">Click here</a> to start creating a new category.<br />';

			
			echo '<table class="widefat">
			<thead><tr><th> </th><th>Category <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>The name of the category.  Essentially, if you\'re selling a bunch of hats, make a category called hats.  It\'s that easy!</h3></div></th><th>Parent <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>If you select a parent category, then the category you are creating is a child category.  For example, if you sold red and blue hats, you would select hats as the parent.</h3></div></th><th>Thumb</th><th>Description</th><th>Page</th></tr></thead><tbody>
			';
			
			echo '
			<tr>
			<td></td>
			<td><input type="text" name="wpStoreCartCategory" style="width: 80%;" value="'.$wpStoreCartCategory.'" /></td>
			<td><select name="wpStoreCartCategoryParent"> 
			 <option value="">
						';
			
			esc_attr(__('Select page'));
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
			';	
			
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
			</table></div>
			<br style="clear:both;" />';	
		
		}	
		// END Prints out the Categories admin page =======================================================================		
				
		


		
		
		
		//Prints out the Coupons admin page =======================================================================
        function printAdminPageCoupons() {
			global $wpdb;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			
			$table_name = $wpdb->prefix . "wpstorecart_coupons";

                        // Allows us to turn the coupon system off or on from this page
                        if(@isset($_POST['enablecoupons'])) {
                            $devOptions['enablecoupons'] = $wpdb->escape($_POST['enablecoupons']);
                            update_option('wpStoreCartAdminOptions', $devOptions);
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
				$wpStoreCartamount = '0.00';
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
                        $this->wpstorecart_alert();

			echo '<div style="width:75%;max-width:75%;float:left;">';
			if ($isanedit != true) {
				echo '<h2>Add a Coupon <a href="http://wpstorecart.com/documentation/coupons/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>';
			} else {
				echo '<h2>Edit a Coupon <a href="http://wpstorecart.com/documentation/coupons/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>Add a new coupon by <a href="admin.php?page=wpstorecart-coupon">clicking here</a>.<br />';
			}


                        echo '
			<form method="post" action="'. $_SERVER["REQUEST_URI"].'" name="wpstorecartcouponsetting">
                        <p>Coupons are enabled? <label for="enablecoupons"><input type="radio" id="enablecoupons_yes" name="enablecoupons" value="true" '; if ($devOptions['enablecoupons'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enablecoupons_no"><input type="radio" id="enablecoupons_no" name="enablecoupons" value="false" '; if ($devOptions['enablecoupons'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label> &nbsp;<input type="submit" value="Update" /></p>
                        </form>';


			echo '
			<form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">
			';

			echo '<table class="widefat">
			<thead><tr><th>Coupon Code <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>Don\'t use spaces! This is what people should type or paste into the coupon box during checkout in order to recieve a discount.  As such, this should be a short code, with no spaces, all alpha numeric characters, etc.</h3></div></th><th>Flat<br /> Discount <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>A flat amount to deduct when the coupon code is used.  You can combine this with the Pecentage Discount, but for simplicities sake, we recommend choosing either a flat discount or a percentage, but not both.</h3></div></th><th>Percentage<br />Discount <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-3" /><div class="tooltip-content" id="example-content-3"><h3>The percentage of the price to deduct from the purchase.</h3></div></th><th>Description <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4" /><div class="tooltip-content" id="example-content-4"><h3>Take a note of what your coupon is meant to do by writing a description here.</h3></div></th><th>Product <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-5" /><div class="tooltip-content" id="example-content-5"><h3>The product you want the coupon to apply to.  <!--Set to 0 for the coupon to work on all products in the store.--></h3></div></th><th>Start Date <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-6" /><div class="tooltip-content" id="example-content-6"><h3>The day which the coupon starts working.  Before this date, the coupon is invalid.</h3></div></th><th>Expiration Date <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-7" /><div class="tooltip-content" id="example-content-7"><h3>The date which the coupon code stops working.  After this date, the coupon is invalid.</h3></div></th></tr></thead><tbody>
			';

			
			echo '
			<tr>
			<td><input type="text" name="wpStoreCartcode" style="width: 80%;" value="'.$wpStoreCartcode.'" /></td>
			<td>'.$devOptions['currency_symbol'].'<input type="text" name="wpStoreCartamount" style="width: 60px;" value="'.$wpStoreCartamount.'" />'.$devOptions['currency_symbol_right'].'</td>
			<td><input type="text" name="wpStoreCartpercent" style="width: 40px;" value="'.$wpStoreCartpercent.'" /> %</td>
			<td><textarea name="wpStoreCartdescription">'.$wpStoreCartdescription.'</textarea></td>
			<td>

                        <select name="wpStoreCartproduct">
			 <option value="0">(Any &amp; All Products)</option>';
			
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
			<td><input type="text" name="wpStoreCartstartdate" id="wpStoreCartstartdate" style="width: 100px;" value="'.$wpStoreCartstartdate.'" /></td>
			<td><input type="text" name="wpStoreCartenddate" id="wpStoreCartenddate" style="width: 100px;" value="'.$wpStoreCartenddate.'" /></td>
			</tr>';			
			
			echo '
			</tbody>
			</table>
			
			<script type="text/javascript">
                        /* <![CDATA[ */
			  AnyTime.picker( "wpStoreCartstartdate", 
				  { format: "%Y%m%d" } );
			  AnyTime.picker( "wpStoreCartenddate", 
				  { format: "%Y%m%d" } );
                                  /* ]]> */
			</script>
			
			<br style="clear:both;" />
			<div class="submit">
			<input type="submit" name="addNewwpStoreCart_product" value="'; _e('Submit Coupon', 'wpStoreCart'); echo'" /></div>
			</form>
			';	
			
			echo '
			<br style="clear:both;" /><br />
			<h2>Edit Coupons</h2>';
			
			echo '<table class="widefat">
			<thead><tr><th>Key</th><th>The Coupon Code</th><th>Flat Discount</th><th>Percentage Dicount</th><th>Description</th><th>Product</th><th>Start Date</th><th>Expiration Date</th></tr></thead><tbody>
			';


			$grabrecord = "SELECT * FROM `{$table_name}`;";
			
			$results = $wpdb->get_results( $grabrecord , ARRAY_A );		
			if(isset($results)) {
				foreach ($results as $result) {
					
					echo "
					<tr>
					<td>[ ".$result['primkey']." | <a href=\"admin.php?page=wpstorecart-coupon&keytoedit=".$result['primkey']."\">Edit</a> | <a onclick=\"if (! confirm('Are you sure you want to delete this coupon?')) { return false;}\" href=\"admin.php?page=wpstorecart-coupon&keytodelete=".$result['primkey']."\">Delete</a> ]</td>
					<td>".$result['code']."</td>
					<td>{$devOptions['currency_symbol']}".$result['amount']."{$devOptions['currency_symbol_right']}</td>
					<td>".$result['percent']."%</td>
					<td>".$result['description']."</td>
					<td>".$result['product']."</td>
					<td>".$result['startdate']."</td>
					<td>".$result['enddate']."</td>	
					</tr>";	
					

				}
			}					
			
			
		

			
			echo '
			</tbody>
			</table></div>
			<br style="clear:both;" />';	
		
		}	
		// END Prints out the Coupons admin page =======================================================================			
		
		
		
		//Prints out the Statistics admin page =======================================================================
        function printAdminPageStatistics() {
			global $wpdb, $devOptions;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}		
		
			
			
                        $this->spHeader();
		
			require_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/statistics.php');
		
		}
		// ==========================================================================================================		
		

		//Prints out the Overview admin page =======================================================================
        function printAdminPageOverview() {
			global $wpdb;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}		


			

                        if(isset($_GET['wpscaction']) && $_GET['wpscaction']=='removecurl') {
                            $devOptions['checkcurl']='false';
                            update_option('wpStoreCartAdminOptions', $devOptions);
                        }

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

                        echo '<img src="'.plugins_url('/images/overview.png' , __FILE__).'" alt="" style="float:left;" /><h2 style="font-size:32px;">&nbsp;MyStore</h2><div><br style="clear:both;" />
                                <div class="postbox-container" style="width:45%;">
                                    <div class="postbox">
                                        <div class="handlediv" title="_">
                                            <br />
                                        </div>
                                        <h3 class="hndle" style="padding:5px 5px 5px 5px;"><span style="">Overview</span></h3>
                                        <div class="inside" style="padding:0px 5px 5px 5px;">    ';

                                        $this->wpstorecart_main_dashboard_widget_function();

                        echo '          </div>
                                    </div>
                                </div>

                                <div class="postbox-container" style="width:45%;">
                                    <div class="postbox">
                                        <div class="handlediv" title="_">
                                            <br />
                                        </div>
                                        <h3 class="hndle" style="padding:5px 5px 5px 5px;"><span style="">News</span></h3>
                                        <div class="inside" style="padding:0px 5px 5px 5px;">    ';
			include_once(ABSPATH . WPINC . '/feed.php');
                        $rss = fetch_feed('http://wpstorecart.com/category/blog/feed/');
                        if (!is_wp_error( $rss ) ) : // Checks that the object is created correctly
                            // Figure out how many total items there are, but limit it to 2.
                            $maxitems = $rss->get_item_quantity(2);

                            // Build an array of all the items, starting with element 0 (first element).
                            $rss_items = $rss->get_items(0, $maxitems);
                        endif;


                        echo '<ul>';
                            if ($maxitems == 0) {
                                echo '<li>No items.</li>';
                            } else {
                                // Loop through each feed item and display each item as a hyperlink.
                                foreach ( $rss_items as $item ) {
                                    echo '<li><a style="font-weight:bold;font-size:120%;" target="_blank" href="'. $item->get_permalink() .'" title="Posted "'.$item->get_date('j F Y | g:i a').'">'.$item->get_title().'</a><br />'.$item->get_description().'</li>';
                                }
                            }
                        echo '</ul>';

                        echo '
                                        </div>
                                    </div>
                                </div>
<br style="clear:both;" />
                                <div class="postbox-container" style="width:95%">
                                    <div class="postbox">
                                        <div class="handlediv" title="_">
                                            <br />
                                        </div>
                                        <h3 class="hndle" style="padding:5px 5px 5px 5px;"><span style="">Basic Stats</span></h3>
                                        <div class="inside" style="padding:0px 5px 5px 5px;">    
                                        <table  class="widefat" >
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
                                        </div>
                                    </div>
                                </div>
                             </div>
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

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
			}


                        $this->spHeader();
                        $this->wpstorecart_alert();

                        echo '<div style="width:75%;max-width:75%;float:left;">';
			require_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.php');
                        echo '</div>';
		
		}
		// ==========================================================================================================
		

		//Prints out the Help admin page =======================================================================
        function printAdminPageHelp() {
			global $wpdb;

			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				die(__('Cheatin&#8217; uh?'));
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

                        /*
                         * @todo Add the ability to specify user permission levels for dashboard.  This is not a priority, but more of an after thought
                         */
			$devOptions = $this->getAdminOptions();if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
				exit();
			}
                        
			
			
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

                        $dailyAverage = $totalearned / 30;

			// inlinebar
			// 
			$lastrecordssql = "SELECT * FROM `{$table_name_orders}` ORDER BY `date` DESC LIMIT 0, 30";
			$lastrecords = $wpdb->get_results( $lastrecordssql , ARRAY_A );
			
			echo '<ul>';
                        echo '<li><u><span style="font-size:115%;"><strong>wpStoreCart v'.$wpstorecart_version.' :</strong></span> with '.$totalrecords.' product(s).</u></li>';
                        echo '<li>Last 30 days: <strong><span style="font-size:140%;">'.$devOptions['currency_symbol'].number_format($totalearned).$devOptions['currency_symbol_right'].'</span></strong> ('.$devOptions['currency_symbol'].number_format($dailyAverage).$devOptions['currency_symbol_right'].' avg per day)</li>';
                        echo '<li>All Time: <strong><span style="font-size:140%;">'.$devOptions['currency_symbol'].number_format($allTimeGrossRevenue).$devOptions['currency_symbol_right'].'</span></strong></li>';
                        echo "<li><span style=\"float:left;padding:0 10px 0 0;border-right:1px #CCC solid;\"><strong>Completed Orders / Total:</strong>  {$totalrecordsordercompleted}/{$totalrecordsorder} ({$orderpercentage}%) <br /><img src=\"http://chart.apis.google.com/chart?chs=200x50&cht=p3&chco=224499,BBCCED&chd=s:Uf&chdl=$totalrecordsordercompleted|$totalrecordsorder\"></span> </li>";
			echo "<li><span style=\"float:left;padding:0 0 0 10px;\"><strong>Sales last 30 days:</strong> <br /><img src=\"http://chart.apis.google.com/chart?chxt=y&chbh=a,2&chs=200x50&cht=bvg&chco=224499&chds=0,{$highestNumber}&chd=t:0";while($currentDay != $enddate) {echo $salesOnDay[$currentDay].',';$dayAgo--;$currentDay = date("Ymd", strtotime("{$dayAgo} days ago"));} echo"0\" alt=\"\" /></span><div style=\"clear:both;\"></div></li>";
        		echo '</ul>';
		} 
		
		// Create the function use in the action hook
		function wpstorecart_main_add_dashboard_widgets() {
			wp_add_dashboard_widget('wpstorecart_main_dashboard_widgets', 'wpStoreCart Overview', array(&$this, 'wpstorecart_main_dashboard_widget_function'));	
		} 
		
		
		/**
                 * wpStoreCart code that needs to be inserted into the header
                 */
		function  addHeaderCode() {
                        
			//echo '<!-- wpStoreCart BEGIN -->';

			wp_enqueue_script('wpsc', plugins_url('/php/wpsc-1.1/wpsc/wpsc-javascript.php' , __FILE__), array('jquery'),'1.3.2' );
                    

                        $devOptions = $this->getAdminOptions();

                        if($devOptions['wpscjQueryUITheme']!='') {

                            $myStyleUrl = plugins_url('/jqueryui/css/'.$devOptions['wpscjQueryUITheme'].'/jquery-ui-1.8.7.custom.css' , __FILE__);
                            $myStyleFile = WP_PLUGIN_DIR . '/jqueryui/css/'.$devOptions['wpscjQueryUITheme'].'/jquery-ui-1.8.7.custom.css';
                            if ( file_exists($myStyleFile) ) {
                                wp_register_style('myStyleSheets', $myStyleUrl);
                                wp_enqueue_style( 'myStyleSheets');
                            }


                            wp_enqueue_script('jqueryui-new', plugins_url('/jqueryui/js/jquery-ui-1.8.7.custom.min.js' , __FILE__), array('jquery'),'1.3.2' );

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


        /**
         *
         * Displays a Buy Now button for a subscription payment
         *
         * @global  $wpsc_buy_now
         * @global object $wpdb
         * @global <type> $wpsc_membership_product_id
         * @global <type> $purchaser_user_id
         * @global <type> $purchaser_email
         * @global <type> $membershipOptions
         * @global string $wpsc_table_name
         * @global string $wpsc_self_path
         * @global  $wpsc_paypal_testmode
         * @global  $wpsc_paypal_ipn
         * @global <type> $wpsc_membership_product_name
         * @global <type> $wpsc_membership_product_number
         * @global  $wpsc_button_classes
         * @global  $wpsc_paypal_currency_code
         * @global  $wpsc_paypal_email
         * @global <type> $wpsc_price_type
         * @global <type> $wpsc_membership_trial1_allow
         * @global <type> $wpsc_membership_trial2_allow
         * @global <type> $wpsc_membership_trial1_amount
         * @global <type> $wpsc_membership_trial2_amount
         * @global <type> $wpsc_membership_regular_amount
         * @global <type> $wpsc_membership_trial1_numberof
         * @global <type> $wpsc_membership_trial2_numberof
         * @global <type> $wpsc_membership_regular_numberof
         * @global <type> $wpsc_membership_trial1_increment
         * @global <type> $wpsc_membership_trial2_increment
         * @global <type> $wpsc_membership_regular_increment
         * @param <type> $itemPrimkey
         * @param <type> $listDetails
         * @return string
         */
        function displaySubscriptionBuyNow($itemPrimkey, $listDetails = false) {
            global $wpsc_buy_now, $wpdb, $wpsc_membership_product_id, $purchaser_user_id, $purchaser_email, $membershipOptions, $wpsc_table_name, $wpsc_self_path, $wpsc_paypal_testmode, $wpsc_paypal_ipn, $wpsc_membership_product_name, $wpsc_membership_product_number, $wpsc_button_classes, $wpsc_paypal_currency_code, $wpsc_paypal_email, $wpsc_price_type,$wpsc_membership_trial1_allow, $wpsc_membership_trial2_allow, $wpsc_membership_trial1_amount , $wpsc_membership_trial2_amount, $wpsc_membership_regular_amount,$wpsc_membership_trial1_numberof,$wpsc_membership_trial2_numberof,$wpsc_membership_regular_numberof,$wpsc_membership_trial1_increment,$wpsc_membership_trial2_increment,$wpsc_membership_regular_increment;
            if(isset($itemPrimkey) && is_numeric($itemPrimkey)) {
                $sql = "SELECT * FROM `{$table_name}` WHERE `primkey`={$itemPrimkey};";
                $results = $wpdb->get_results( $sql , ARRAY_A );
                if(isset($results)) {
                    // This code checks to see if we will be potentially displaying subscription products with either the price or add to cart button visible.  If so, we query each product for subscription information
                    $devOptions = $this->getAdminOptions();
                    $wpsc_price_type = 'charge';
                    $membership_value = '';
                    if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php') ){
                        $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                        $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$itemPrimkey};";
                        $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                        if(isset($resultsMembership)) {
                            foreach ($resultsMembership as $pagg) {
                                $membership_primkey = $pagg['primkey'];
                                $membership_value = $pagg['value'];
                            }
                            if($membership_value!='') {
                                $theExploded = explode('||', $membership_value);
                                // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                                $wpsc_membership_product_id = $itemPrimkey;
                                $wpsc_price_type = $theExploded[0];
                                $wpsc_membership_trial1_allow = $theExploded[1];
                                $wpsc_membership_trial2_allow = $theExploded[2];
                                $wpsc_membership_trial1_amount = $theExploded[3];
                                $wpsc_membership_trial2_amount = $theExploded[4];
                                $wpsc_membership_regular_amount = $theExploded[5];
                                $wpsc_membership_trial1_numberof = $theExploded[6];
                                $wpsc_membership_trial2_numberof = $theExploded[7];
                                $wpsc_membership_regular_numberof = $theExploded[8];
                                $wpsc_membership_trial1_increment = $theExploded[9];
                                $wpsc_membership_trial2_increment = $theExploded[10];
                                $wpsc_membership_regular_increment = $theExploded[11];
                                if($wpsc_membership_trial1_increment=='D'){$wpsc_membership_trial1_increment_display=$devOptions['day'];}
                                if($wpsc_membership_trial2_increment=='D'){$wpsc_membership_trial2_increment_display=$devOptions['day'];}
                                if($wpsc_membership_regular_increment=='D'){$wpsc_membership_regular_increment_display=$devOptions['day'];}
                                if($wpsc_membership_trial1_increment=='W'){$wpsc_membership_trial1_increment_display=$devOptions['week'];}
                                if($wpsc_membership_trial2_increment=='W'){$wpsc_membership_trial2_increment_display=$devOptions['week'];}
                                if($wpsc_membership_regular_increment=='W'){$wpsc_membership_regular_increment_display=$devOptions['week'];}
                                if($wpsc_membership_trial1_increment=='M'){$wpsc_membership_trial1_increment_display=$devOptions['month'];}
                                if($wpsc_membership_trial2_increment=='M'){$wpsc_membership_trial2_increment_display=$devOptions['month'];}
                                if($wpsc_membership_regular_increment=='M'){$wpsc_membership_regular_increment_display=$devOptions['month'];}
                                if($wpsc_membership_trial1_increment=='Y'){$wpsc_membership_trial1_increment_display=$devOptions['year'];}
                                if($wpsc_membership_trial2_increment=='Y'){$wpsc_membership_trial2_increment_display=$devOptions['year'];}
                                if($wpsc_membership_regular_increment=='Y'){$wpsc_membership_regular_increment_display=$devOptions['year'];}
                                $membershipOptions['databasename'] = DB_NAME;
                                $membershipOptions['databaseuser'] = DB_USER;
                                $membershipOptions['databasepass'] = DB_PASSWORD;
                                $membershipOptions['databasehost'] =DB_HOST;
                                $membershipOptions['databaseprefix'] = $wpdb->prefix;
                                $membershipOptions['databasetable'] = $membershipOptions['databaseprefix'] . 'wpstorecart_log';
                                $membershipOptions['databaseproductstable'] = $membershipOptions['databaseprefix'] . 'wpstorecart_products';
                            }
                        }
                    }

                    if ($wpsc_price_type == 'membership' ) {

                    if($listDetails) {
                        $output .= '
                        <ul class="wpsc-product-info">
                        <li id="list-item-name"><strong>'.stripslashes($results[0]['name']).'</strong></li>
                        ';
                        if($wpsc_membership_trial1_allow=='yes') {
                            $output.="<li class=\"list-item-price\">{$devOptions['trial_period_1']} {$devOptions['currency_symbol']}{$wpsc_membership_trial1_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial1_numberof} {$wpsc_membership_trial1_increment_display}</li>";
                        }
                        if($wpsc_membership_trial1_allow=='yes') {
                            $output.="<li class=\"list-item-price\">{$devOptions['trial_period_2']} {$devOptions['currency_symbol']}{$wpsc_membership_trial2_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial2_numberof} {$wpsc_membership_trial2_increment_display}</li>";
                        }
                        $output.="<li class=\"list-item-price\">{$devOptions['subscription_price']} {$devOptions['currency_symbol']}{$wpsc_membership_regular_amount}{$devOptions['currency_symbol_right']} {$devOptions['every']} {$wpsc_membership_regular_numberof} {$wpsc_membership_regular_increment_display}</li>";
                        $output .= '</ul>';
                    }
                    if($resultsMembership[0]['useinventory']==0 || ($resultsMembership[0]['useinventory']==1 && $resultsMembership[0]['inventory'] > 0) || $devOptions['storetype']=='Digital Goods Only' ) {
                            // Allows us to bypass registration and have guest only checkout
                            if($devOptions['requireregistration']=='false') {
                                if(@isset($_SESSION['wpsc_email'])) {
                                    $purchaser_user_id = 0;
                                    $purchaser_email = $wpdb->escape($_SESSION['wpsc_email']);
                                    $purchasing_display_name = 'Guest ('.$_SERVER['REMOTE_ADDR'].')';
                                } else {
                                    $purchaser_user_id = $current_user->ID;
                                    $purchaser_email = $current_user->user_email;
                                    $purchasing_display_name = '%user_display_name_with_link%';
                                }
                            } else {
                                    $purchaser_user_id = $current_user->ID;
                                    $purchaser_email = $current_user->user_email;
                                    $purchasing_display_name = '%user_display_name_with_link%';
                            }
                            $wpsc_membership_product_name = $resultsMembership[0]['name'];
                            $wpsc_membership_product_number = $resultsMembership[0]['primkey'];
                            $wpsc_paypal_currency_code = $devOptions['currency_code'];
                            $wpsc_paypal_email = $devOptions['paypalemail'];
                            $wpsc_button_classes = $devOptions['button_classes_addtocart'];
                            $wpsc_paypal_ipn = $devOptions['paypalipnurl'];
                            $wpsc_paypal_testmode = $devOptions['paypaltestmode'];
                            $wpsc_self_path = WP_PLUGIN_URL.'/wpsc-membership-pro/';
                            $wpsc_table_name = $wpdb->prefix .'wpstorecart_meta';
                            $wpsc_buy_now = $devOptions['buy_now'];
                            require(WP_PLUGIN_DIR.'/wpsc-membership-pro/paypal.php');
                            $output .= wpscMembershipButton();

                      } else {
                            $output .= '<form>
                            <input type="hidden" name="placeholder" />
                            ';
                            $output .= $devOptions['out_of_stock'];
                            $output .= '</form>';
                    }
                    }

                    return $output;
                } else {
                    return NULL;
                }

            }
        }




        /**
         *
         * wpStoreCart code that needs to be loaded into the footer
         *
         * @global <type> $is_checkout
         * @global <type> $cart
         * @global boolean $wpscCarthasBeenCalled
         * @global <type> $wpsc
         * @global <type> $wpsc_cart_type
         * @return string
         */
        function addFooterCode(){
                        global $is_checkout, $cart, $wpscCarthasBeenCalled, $wpsc, $wpsc_cart_type;

                        $output = '';

                        if($wpscCarthasBeenCalled==false) {
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
                            if($wpsc_cart_type == 'session') {
                                if(!isset($_SESSION)) {
                                        @session_start();
                                }
                                if(@!is_object($cart)) {
                                    $cart =& $_SESSION['wpsc'];
                                    if(@!is_object($cart)) {
                                        $cart = new wpsc();
                                    }
                                }
                            }

                            if($wpsc_cart_type == 'cookie') {
                                if(@!is_object($cart)) {
                                    if(isset($_COOKIE['wpsccart'])) { @$cart =& unserialize(base64_decode($_COOKIE['wpsccart'])); }
                                    if(@!is_object($cart) && !isset($_COOKIE['wpsccart'])) {
                                        $cart = new wpsc();
                                        $xdomain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;setcookie('wpsccart', base64_encode(serialize($cart)), time()+7222, '/', $xdomain, false);
                                    }
                                }
                                if(!isset($_SESSION)) { @session_start(); }
                            }
                            $old_checkout = $is_checkout;
                            $is_checkout = false;
                            $output= $cart->display_cart($wpsc, true);
                            $is_checkout = $old_checkout;
                            $wpscCarthasBeenCalled = true;
                        }

                        // ThickBox integration code
                        if($devOptions['useimagebox']=='thickbox'){
                            $output .= '
                            <script type="text/javascript">
                                /* <![CDATA[ */
                                    if ( typeof tb_pathToImage != \'string\' )
                                    {
                                        var tb_pathToImage = "'. get_bloginfo('url').'/wp-includes/js/thickbox/loadingAnimation.gif";
                                    }
                                    if ( typeof tb_closeImage != \'string\' )
                                    {
                                        var tb_closeImage = "'. get_bloginfo('url').'/wp-includes/js/thickbox/tb-close.png";
                                    }
                                /* ]]> */
                            </script>
                            ';
                        }

                        return $output;
        }


        /**
         *
         * @param string $content
         * @return string
         * @todo examine if this does anything, and remove it if it doesn't. 
         */
        function  addContent($content = '') {
            $content .= "<p>wpStoreCart</p>";
            return $content;
        }


                function wpstorecart_install_wpms() {

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
                                `useinventory` BOOL NOT NULL DEFAULT '1',
                                `donation` BOOL NOT NULL DEFAULT '0',
                                `weight` int(7) NOT NULL DEFAULT '0',
                                `length` int(7) NOT NULL DEFAULT '0',
                                `width` int(7) NOT NULL DEFAULT '0',
                                `height` int(7) NOT NULL DEFAULT '0',
                                `discountprice` DECIMAL(9,2) NOT NULL
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

                   $table_name = $wpdb->prefix . "wpstorecart_meta";
                   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

                            $sql = "
                                    CREATE TABLE {$table_name} (
                                    `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                                    `value` TEXT NOT NULL,
                                    `type` VARCHAR(32) NOT NULL,
                                    `foreignkey` INT NOT NULL
                                    );
                                    ";


                          dbDelta($sql);
                   }


                   $table_name = $wpdb->prefix . "wpstorecart_av";
                   if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {

                        $sql = "
                                CREATE TABLE IF NOT EXISTS `{$table_name}` (
                                    `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                                    `productkey` INT NOT NULL ,
                                    `values` TEXT NOT NULL ,
                                    `price` DECIMAL( 9, 2 ) NOT NULL
                                );
                                ";


                        dbDelta($sql);
                    }
                }


		// Installation ==============================================================================================		
		function wpstorecart_install() {
		   global $wpdb;
		   global $wpstorecart_db_version;

                    if (function_exists('is_multisite') && is_multisite()) {
                                // check if it is a network activation - if so, run the activation function for each blog id
                                if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
                                        $old_blog = $wpdb->blogid;
                                        // Get all blog ids
                                        $blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
                                        foreach ($blogids as $blog_id) {
                                                switch_to_blog($blog_id);
                                                $this->wpstorecart_install_wpms();
                                        }
                                        switch_to_blog($old_blog);
                                        return;
                                }
                        }
                        $this->wpstorecart_install_wpms();

		   
		}
		// END Installation ==============================================================================================
				

                function wpstorecart_picture_gallery($productid) {
                    global $wpdb;

                    $devOptions = $this->getAdminOptions();
                    $maxImageWidth = $devOptions['wpStoreCartwidth'];
                    $maxImageHeight = $devOptions['wpStoreCartheight'];

                    $output = '<div class="wpsc-gallery">';

                    $table_name = $wpdb->prefix . "wpstorecart_meta";
                    $preresults = $wpdb->get_results("SELECT * FROM {$table_name} WHERE `type`='wpsc_product_gallery' AND `foreignkey`='{$productid}'", ARRAY_A);

                    if(isset($preresults[0]['value'])) {
                        $theExploded = explode('||', str_replace('<<<','',$preresults[0]['value']));
                        foreach($theExploded as $theExplosion) {
                            if(trim($theExplosion!='')) {
                                $output .= '<a href="'.get_bloginfo('url'). '/wp-content/uploads/wpstorecart/'.$theExplosion.'" class="thickbox" rel="gallery-'.$productid.'"><img src="'.get_bloginfo('url'). '/wp-content/uploads/wpstorecart/'.$theExplosion.'" class="thickbox wpsc-gallery-thumbnail" ';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= ' /></a>';
                            }
                        }
                    }

                    $output.= '</div>';

                    return $output;

                }

		// Shortcode =========================================
		function wpstorecart_mainshortcode($atts, $content = null) {
			global $wpdb, $cart, $wpsc, $is_checkout, $current_user;

                        $statset = false;
			$table_name = $wpdb->prefix . "wpstorecart_products";
		
			$devOptions = $this->getAdminOptions();		
                         wp_get_current_user();

                        if ($devOptions['turnon_wpstorecart']=='true') {

                            extract(shortcode_atts(array(
                                    'display' => NULL,
                                    'primkey' => '0',
                                    'quantity' => 'unset',
                                    'usetext' => 'true',
                                    'usepictures' => 'false',
                                    'thecategory' => '',
                                    'displaytype' => '',
                                    'orderby' => '',
                                    'ordertype' => '',
                            ), $atts));

                            // This allows the shortcode to override the global setting
                            if($usetext=='false') {
                                $devOptions['displayTitle']=='false';
                            } else {
                                $usetext = 'true';
                            }

                            // Formats the orderby and order type stuff
                            if($orderby!='') {
                                if($ordertype=='') {$ordertype = 'ASC';}
                                $orderby = " ORDER BY `$orderby` $ordertype";
                            }

                            // Adds page pagination
                            if($quantity=='unset') {
                                $itemsperpage = $devOptions['itemsperpage'];
                                $quantity = $devOptions['itemsperpage'];
                            } else {
                                $itemsperpage = $quantity;
                            }


                            // Adds this shortcode: [wpstorecart displaytype="grid"] and [wpstorecart displaytype="list"]
                            if ($displaytype=='list' || $displaytype=='grid') {
                                $devOptions['displayType']=$displaytype;
                            }


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
                                    case 'shareyourcart':
                                       /**
                                                 * ShareYourCart.com Integration begins here
                                                 */
                                        if($devOptions['shareyourcart_activate']=='true') {
                                            $output .= '<iframe src="https://www.shareyourcart.com/button?client_id='.$devOptions['shareyourcart_clientid'].'&skin='.$devOptions['shareyourcart_skin'].'&callback_url='. urlencode(plugins_url('/php/shareyourcart/sendcart.php' , __FILE__))  . '" frameborder="0" border="0" style="border:none;" class="wpsc-shareyourcart-checkout"></iframe>';
                                        }
                                        // ShareYourCart.com Integration ends here
                                        break;
                                    case 'gallery': //
                                        $output.= $this->wpstorecart_picture_gallery($primkey);
                                        break;
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

                                            require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');

                                            global $wpsc_cart_type;
                                            if($wpsc_cart_type == 'session') {
                                                if(!isset($_SESSION)) {
                                                        @session_start();
                                                }
                                                if(@!is_object($cart)) {
                                                    $cart =& $_SESSION['wpsc'];
                                                    if(@!is_object($cart)) {
                                                        $cart = new wpsc();
                                                    }
                                                }
                                            }

                                            if($wpsc_cart_type == 'cookie') {
                                                if(@!is_object($cart)) {
                                                    if(isset($_COOKIE['wpsccart'])) { @$cart =& unserialize(base64_decode($_COOKIE['wpsccart'])); }
                                                    if(@!is_object($cart) && !isset($_COOKIE['wpsccart'])) {
                                                        $cart = new wpsc();
                                                        $xdomain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;setcookie('wpsccart', base64_encode(serialize($cart)), time()+7222, '/', $xdomain, false);
                                                    }
                                                }
                                                if(!isset($_SESSION)) { @session_start(); }
                                            }

                                            $output .= $cart->display_cart($wpsc);
                                           /**
                                                     * ShareYourCart.com Integration begins here
                                                     */
                                            if($devOptions['shareyourcart_activate']=='true') {
                                                $output .= '<iframe src="https://www.shareyourcart.com/button?client_id='.$devOptions['shareyourcart_clientid'].'&skin='.$devOptions['shareyourcart_skin'].'&callback_url='. urlencode(plugins_url('/php/shareyourcart/sendcart.php' , __FILE__))  . '" frameborder="0" border="0" style="border:none;" class="wpsc-shareyourcart-checkout"></iframe>';
                                            }
                                            // ShareYourCart.com Integration ends here

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
                                                                            $output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.stripslashes($result['name']).'" /></a>';
                                                                    }
                                                                    if($usetext=='true') {
                                                                            $output .= '<p><a href="'.$permalink.'">'.stripslashes($result['name']).'</a></p>';
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
                                                                            $output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.stripslashes($result['name']).'" /></a>';
                                                                    }
                                                                    if($usetext=='true') {
                                                                            $output .= '<p><a href="'.$permalink.'">'.stripslashes($result['name']).'</a></p>';
                                                                    }
                                                            }
                                                    }
                                            } else {
                                                    $output .= '<div class="wpsc-error">wpStoreCart did not like your topproducts shortcode!  The quantity field contained non-numeric data. Please fix your page or consult the wpStoreCart documentation for help.</div>';
                                            }
                                            $output .= '</div>';
                                            break;
                                    /**
                                            * @todo Add a shortcode to display only 1 specific category.  Do that by specifying a primkey and altering the SQL 
                                            */
                                    case 'categories': // Categories shortcode =========================================================
                                            $output .= '<div class="wpsc-by-category">';
                                            if($devOptions['showproductthumbnail']=='true') {
                                                $usepictures='true';
                                                $maxImageWidth = $devOptions['wpStoreCartwidth'];
                                                $maxImageHeight = $devOptions['wpStoreCartheight'];
                                            }
                                            if(is_numeric($quantity) && is_numeric($thecategory)){
                                                    if($orderby=='') {
                                                        $sql = "SELECT * FROM `{$table_name}` WHERE `category`='{$thecategory}' LIMIT 0, {$quantity};";
                                                    } else {
                                                        $sql = "SELECT * FROM `{$table_name}` WHERE `category`='{$thecategory}' {$orderby} LIMIT 0, {$quantity};";
                                                    }
                                                    $results = $wpdb->get_results( $sql , ARRAY_A );
                                                if(isset($results)) {
                                                        foreach ($results as $result) {

                                                                // This code checks to see if we will be potentially displaying subscription products with either the price or add to cart button visible.  If so, we query each product for subscription information
                                                                $wpsc_price_type = 'charge';
                                                                $membership_value = '';
                                                                if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php') && ($devOptions['displaypriceonview']=='true' || $devOptions['displayAddToCart']=='true')){
                                                                    $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                                                                    $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$result['primkey']};";
                                                                    $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                                                                    if(isset($resultsMembership)) {
                                                                        foreach ($resultsMembership as $pagg) {
                                                                            $membership_primkey = $pagg['primkey'];
                                                                            $membership_value = $pagg['value'];
                                                                        }
                                                                        if($membership_value!='') {
                                                                            $theExploded = explode('||', $membership_value);
                                                                            // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                                                                            $wpsc_price_type = $theExploded[0];
                                                                        }
                                                                    }
                                                                }

                                                                $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                                if($devOptions['displayType']=='grid'){
                                                                        $output .= '<div class="wpsc-grid wpsc-categories">';
                                                                }
                                                                if($devOptions['displayType']=='list'){
                                                                        $output .= '<div class="wpsc-list wpsc-categories">';
                                                                }
                                                                if($usepictures=='true') {
                                                                        $output .= '<a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$result['thumbnail'].'" alt="'.stripslashes($result['name']).'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
                                                                }
                                                                if($usetext=='true' && $devOptions['displayTitle']=='true') {
                                                                        $output .= '<a href="'.$permalink.'"><h1 class="wpsc-h1">'.stripslashes($result['name']).'</h1></a>';
                                                                }
                                                                if($devOptions['displayintroDesc']=='true'){
                                                                        $output .= '<p>'.stripslashes($result['introdescription']).'</p>';
                                                                }
                                                                if($devOptions['displaypriceonview']=='true'){
                                                                    if($wpsc_price_type == 'membership') {
                                                                        $output .= '<ul class="wpsc-product-info">';
                                                                        if($wpsc_membership_trial1_allow=='yes') {
                                                                            $output.="<li class=\"list-item-price\">{$devOptions['trial_period_1']} {$devOptions['currency_symbol']}{$wpsc_membership_trial1_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial1_numberof} {$wpsc_membership_trial1_increment_display}</li>";
                                                                        }
                                                                        if($wpsc_membership_trial2_allow=='yes') {
                                                                            $output.="<li class=\"list-item-price\">{$devOptions['trial_period_2']} {$devOptions['currency_symbol']}{$wpsc_membership_trial2_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial2_numberof} {$wpsc_membership_trial2_increment_display}</li>";
                                                                        }
                                                                        $output.="<li class=\"list-item-price\">{$devOptions['subscription_price']} {$devOptions['currency_symbol']}{$wpsc_membership_regular_amount}{$devOptions['currency_symbol_right']} {$devOptions['every']} {$wpsc_membership_regular_numberof} {$wpsc_membership_regular_increment_display}</li>";
                                                                        $output .= '</ul>';
                                                                    } else {
                                                                        if($result['discountprice']>0) {
                                                                            $output.="<span class=\"wpsc-{$devOptions['displayType']}-price\"><strike>{$devOptions['currency_symbol']}{$result['price']}{$devOptions['currency_symbol_right']}</strike> {$devOptions['currency_symbol']}{$result['discountprice']}{$devOptions['currency_symbol_right']}</span>";
                                                                        } else {
                                                                            $output.="<span class=\"wpsc-{$devOptions['displayType']}-price\">{$devOptions['currency_symbol']}{$result['price']}{$devOptions['currency_symbol_right']}</span>";
                                                                        }
                                                                    }
                                                                }
                                                                if($devOptions['displayAddToCart']=='true'){

                                                                        if($wpsc_price_type == 'charge') {
                                                                            // Flat rate shipping implmented here:
                                                                            if($devOptions['flatrateshipping']=='all_single') {
                                                                                $result['shipping'] = $devOptions['flatrateamount'];
                                                                            } elseif($devOptions['flatrateshipping']=='off' || $devOptions['flatrateshipping']=='all_global') {
                                                                                $result['shipping'] = '0.00';
                                                                            }

                                                                            if($result['discountprice'] > 0) {
                                                                                $theActualPrice = $result['discountprice'];
                                                                            } else {
                                                                                $theActualPrice = $result['price'];
                                                                            }
                                                                            $output .= '
                                                                            <form method="post" action="">

                                                                                    <input type="hidden" name="my-item-id" value="'.$result['primkey'].'" />
                                                                                    <input type="hidden" name="my-item-primkey" value="'.$result['primkey'].'" />
                                                                                    <input type="hidden" name="my-item-name" value="'.stripslashes($result['name']).'" />
                                                                                    <input type="hidden" id="my-item-price" name="my-item-price" value="'.$theActualPrice.'" />
                                                                                    <input type="hidden" name="my-item-shipping" value="'.$result['shipping'].'" />
                                                                                    <input type="hidden" id="my-item-img" name="my-item-img" value="'.$result['thumbnail'].'" />
                                                                                    <input type="hidden" id="my-item-url" name="my-item-url" value="'.get_permalink($result['postid']).'" />
                                                                                    <input type="hidden" id="my-item-tax" name="my-item-tax" value="0" />
                                                                                    <input type="hidden" id="my-item-variation" name="my-item-variation" value="0" />
                                                                                    <label class="wpsc-qtylabel">Qty: <input type="text" name="my-item-qty" value="1" size="3" class="wpsc-qty" /></label>

                                                                            ';

                                                                            if($result['useinventory']==0 || ($result['useinventory']==1 && $result['inventory'] > 0) || $devOptions['storetype']=='Digital Goods Only' ) {
                                                                                $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart '.$devOptions['button_classes_addtocart'].'" />';
                                                                            } else {
                                                                                $output .= $devOptions['out_of_stock'];
                                                                            }


                                                                            $output .= '
                                                                            </form>
                                                                            ';
                                                                        } elseif ($wpsc_price_type == 'membership' ) {
                                                                             $output.=$this->displaySubscriptionBuyNow($result['primkey'], false);

                                                                        }
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

                                                            // This code checks to see if we will be potentially displaying subscription products with either the price or add to cart button visible.  If so, we query each product for subscription information
                                                            $wpsc_price_type = 'charge';
                                                            $membership_value = '';
                                                            if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php') && ($devOptions['displaypriceonview']=='true' || $devOptions['displayAddToCart']=='true')){
                                                                $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                                                                $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$results[0]['primkey']};";
                                                                $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                                                                if(isset($resultsMembership)) {
                                                                    foreach ($resultsMembership as $pagg) {
                                                                        $membership_primkey = $pagg['primkey'];
                                                                        $membership_value = $pagg['value'];
                                                                    }
                                                                    if($membership_value!='') {
                                                                        global $wpsc_buy_now, $wpdb, $wpsc_membership_product_id, $purchaser_user_id, $purchaser_email, $membershipOptions, $wpsc_table_name, $wpsc_self_path, $wpsc_paypal_testmode, $wpsc_paypal_ipn, $wpsc_membership_product_name, $wpsc_membership_product_number, $wpsc_button_classes, $wpsc_paypal_currency_code, $wpsc_paypal_email, $wpsc_price_type,$wpsc_membership_trial1_allow, $wpsc_membership_trial2_allow, $wpsc_membership_trial1_amount , $wpsc_membership_trial2_amount, $wpsc_membership_regular_amount,$wpsc_membership_trial1_numberof,$wpsc_membership_trial2_numberof,$wpsc_membership_regular_numberof,$wpsc_membership_trial1_increment,$wpsc_membership_trial2_increment,$wpsc_membership_regular_increment;
                                                                        $theExploded = explode('||', $membership_value);
                                                                        // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                                                                        $wpsc_membership_product_id = $results[0]['primkey'];
                                                                        $wpsc_price_type = $theExploded[0];
                                                                        $wpsc_membership_trial1_allow = $theExploded[1];
                                                                        $wpsc_membership_trial2_allow = $theExploded[2];
                                                                        $wpsc_membership_trial1_amount = $theExploded[3];
                                                                        $wpsc_membership_trial2_amount = $theExploded[4];
                                                                        $wpsc_membership_regular_amount = $theExploded[5];
                                                                        $wpsc_membership_trial1_numberof = $theExploded[6];
                                                                        $wpsc_membership_trial2_numberof = $theExploded[7];
                                                                        $wpsc_membership_regular_numberof = $theExploded[8];
                                                                        $wpsc_membership_trial1_increment = $theExploded[9];
                                                                        $wpsc_membership_trial2_increment = $theExploded[10];
                                                                        $wpsc_membership_regular_increment = $theExploded[11];
                                                                        if($wpsc_membership_trial1_increment=='D'){$wpsc_membership_trial1_increment_display=$devOptions['day'];}
                                                                        if($wpsc_membership_trial2_increment=='D'){$wpsc_membership_trial2_increment_display=$devOptions['day'];}
                                                                        if($wpsc_membership_regular_increment=='D'){$wpsc_membership_regular_increment_display=$devOptions['day'];}
                                                                        if($wpsc_membership_trial1_increment=='W'){$wpsc_membership_trial1_increment_display=$devOptions['week'];}
                                                                        if($wpsc_membership_trial2_increment=='W'){$wpsc_membership_trial2_increment_display=$devOptions['week'];}
                                                                        if($wpsc_membership_regular_increment=='W'){$wpsc_membership_regular_increment_display=$devOptions['week'];}
                                                                        if($wpsc_membership_trial1_increment=='M'){$wpsc_membership_trial1_increment_display=$devOptions['month'];}
                                                                        if($wpsc_membership_trial2_increment=='M'){$wpsc_membership_trial2_increment_display=$devOptions['month'];}
                                                                        if($wpsc_membership_regular_increment=='M'){$wpsc_membership_regular_increment_display=$devOptions['month'];}
                                                                        if($wpsc_membership_trial1_increment=='Y'){$wpsc_membership_trial1_increment_display=$devOptions['year'];}
                                                                        if($wpsc_membership_trial2_increment=='Y'){$wpsc_membership_trial2_increment_display=$devOptions['year'];}
                                                                        if($wpsc_membership_regular_increment=='Y'){$wpsc_membership_regular_increment_display=$devOptions['year'];}
                                                                        $membershipOptions['databasename'] = DB_NAME;
                                                                        $membershipOptions['databaseuser'] = DB_USER;
                                                                        $membershipOptions['databasepass'] = DB_PASSWORD;
                                                                        $membershipOptions['databasehost'] =DB_HOST;
                                                                        $membershipOptions['databaseprefix'] = $wpdb->prefix;
                                                                        $membershipOptions['databasetable'] = $membershipOptions['databaseprefix'] . 'wpstorecart_log';
                                                                        $membershipOptions['databaseproductstable'] = $membershipOptions['databaseprefix'] . 'wpstorecart_products';
                                                                    }
                                                                }
                                                            }

                                                            if($devOptions['showproductthumbnail']=='true') {
                                                                    if($devOptions['wpStoreCartwidth']!=0 && $devOptions['wpStoreCartheight']!= 0) {
                                                                        if($devOptions['useimagebox']=='thickbox'){$output .='<a href="'.$results[0]['thumbnail'].'" class="thickbox" title="'. htmlentities($results[0]['name']. ' - ' . $results[0]['introdescription']).'">';} $output .= '<img class="wpsc-product-img" src="'.$results[0]['thumbnail'].'" alt="'.$results[0]['name'].'" style="max-width:'.$devOptions['wpStoreCartwidth'].'px;max-height:'.$devOptions['wpStoreCartheight'].'px;" />';if($devOptions['useimagebox']=='thickbox'){$output .='</a>';}$output .= '<br />';
                                                                    } else { // If width or height are zero, let's just display the image without css resizing
                                                                        if($devOptions['useimagebox']=='thickbox'){$output .='<a href="'.$results[0]['thumbnail'].'" class="thickbox" title="'. htmlentities($results[0]['name']. ' - ' . $results[0]['introdescription']).'">';} $output .= '<img class="wpsc-product-img" src="'.$results[0]['thumbnail'].'" alt="'.$results[0]['name'].'" />';if($devOptions['useimagebox']=='thickbox'){$output .='</a>';}$output .= '<br />';
                                                                    }
                                                            }
                                                            if($devOptions['showproductgallery']=='true'  && $devOptions['showproductgallerywhere']=='Directly after the Thumbnail') {
                                                                $output.= $this->wpstorecart_picture_gallery($primkey);
                                                            }

                                                            if($wpsc_price_type == 'charge') {
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
                                                                        $variationStorage[$varStorageCounter]['variationtype'] = $exploder[5];
                                                                        //$voutput .= '<li>'.$exploder[0].' '.$exploder[1].' '.$exploder[2].' '.$exploder[3].'</li>';
                                                                        $varStorageCounter++;
                                                                    }
                                                                }



                                                                $output .= '
                                                                <script type="text/javascript">
                                                                /* <![CDATA[ */
                                                                    var advancedVariationPrice = 0;
                                                                    var advancedVariationName = "";
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
                                                                /* ]]> */
                                                                </script>
                                                                ';
                                                                $variationTest = array();
                                                                $variationCounter = 0;
                                                                if(@is_array($variationStorage) && @isset($variationStorage[0])) {
                                                                    if(isset($variationStorage)) {
                                                                            foreach ($variationStorage as $variationStorageCycle) {
                                                                                if($variationStorageCycle['variationtype']!='advanced') {
                                                                                    if(@!isset($variationTest[$variationStorageCycle['variationname']])) {
                                                                                    $output .= '
                                                                                    <script type="text/javascript">
                                                                                        /* <![CDATA[ */
                                                                                        alteredPrice['.$variationCounter.'] = 0;
                                                                                        alteredName['.$variationCounter.'] = "";

                                                                                        function changePrice'.$variationCounter.'(amount) {
                                                                                            price = amount.split("||");
                                                                                            theprice = parseFloat(price[0]);
                                                                                            thename = price[1];
                                                                                            thekey = price[2];
                                                                                            alteredPrice['.$variationCounter.'] = theprice;
                                                                                            alteredName['.$variationCounter.'] = thename;
                                                                                                ';

                                                                                    if($results[0]['discountprice'] > 0) {
                                                                                        $output .= 'oldAmount = parseFloat('.$results[0]['discountprice'].');';
                                                                                    } else {
                                                                                        $output .= 'oldAmount = parseFloat('.$results[0]['price'].');';
                                                                                    }

                                                                                    $output .= '
                                                                                            newAmount = Math.round((oldAmount + alteredPrice[0] + alteredPrice[1] + alteredPrice[2] + alteredPrice[3] + alteredPrice[4] + alteredPrice[5] + alteredPrice[6] + alteredPrice[7] + alteredPrice[8] + alteredPrice[9] + alteredPrice[10] + alteredPrice[11] + alteredPrice[12] + alteredPrice[13] + advancedVariationPrice) *100)/100;
                                                                                            newName = alteredName[0] + " " + alteredName[1] + " " + alteredName[2] + " " + alteredName[3] + " " + alteredName[4] + " " + alteredName[5] + " " + alteredName[6] + " " + alteredName[7] + " " + alteredName[8] + " " + alteredName[9] + " " + alteredName[10] + " " + alteredName[11] + " " + alteredName[12] + " " + alteredName[13] + advancedVariationName;
                                                                                            jQuery("#list-item-price").replaceWith("<li id=\'list-item-price\'>Price: '.$devOptions['currency_symbol'].'"+ newAmount.toFixed(2) + "'.$devOptions['currency_symbol_right'].'</li>");
                                                                                            jQuery("#my-item-price").val(newAmount.toFixed(2));
                                                                                            jQuery("#my-item-name").val("'.stripslashes($results[0]['name']).' - " + newName);
                                                                                            jQuery("#my-item-id").val("'.$results[0]['primkey'].'-" + thekey);
                                                                                            jQuery("#my-item-primkey").val("'.$results[0]['primkey'].'-" + thekey);

                                                                                        }
                                                                                        /* ]]> */
                                                                                    </script>
                                                                                    ';
                                                                                     $voutput .= '
                                                                                        <li>'.$variationStorageCycle['variationname'].' - '.$variationStorageCycle['variationdesc'].'  <select name="variation_'.$variationStorageCycle['variationname'].'" onclick="changePrice'.$variationCounter.'(this.value);" onchange="changePrice'.$variationCounter.'(this.value);">';

                                                                                    }
                                                                                }
                                                                                if(isset($variationStorage)) {
                                                                                        foreach ($variationStorage as $currentVariation) {
                                                                                                if (($currentVariation['variationname']==$variationStorageCycle['variationname']) && $variationTest[$variationStorageCycle['variationname']]!=true && $currentVariation['variationtype']!='advanced') {
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
                                                                                $variationTest[$variationStorageCycle['variationname']] = true;


                                                                                /**
                                                                                             * Advanced Variations
                                                                                             */


                                                                                // Product variations
                                                                                $table_name30 = $wpdb->prefix . "wpstorecart_meta";
                                                                                $grabrecord = "SELECT * FROM `{$table_name30}` WHERE `type`='productvariation' AND `foreignkey`={$primkey} ORDER BY `value` ASC;";

                                                                                $vresults = $wpdb->get_results( $grabrecord , ARRAY_A );

                                                                                $atLeastOneAdv = false;

                                                                                if(isset($vresults)) {

                                                                                    $variationStorage = array();
                                                                                    $varStorageCounter = 0;
                                                                                    foreach ($vresults as $vresult) {
                                                                                        $theKey = $vresult['primkey'];
                                                                                        $exploder = explode('||', $vresult['value']);
                                                                                        if($exploder[5]=='advanced') {
                                                                                            $atLeastOneAdv = true;
                                                                                            $voutput = NULL;
                                                                                            $variationStorage[$varStorageCounter]['variationkey'] = $theKey;
                                                                                            $variationStorage[$varStorageCounter]['variationname'] = $exploder[0];
                                                                                            $variationStorage[$varStorageCounter]['variationvalue'] = $exploder[1];
                                                                                            $variationStorage[$varStorageCounter]['variationprice'] = $exploder[2];
                                                                                            $variationStorage[$varStorageCounter]['variationdesc'] = $exploder[3];
                                                                                            $variationStorage[$varStorageCounter]['variationtype'] = $exploder[5];
                                                                                            //$voutput .= '<li>'.$exploder[0].' '.$exploder[1].' '.$exploder[2].' '.$exploder[3].'</li>';
                                                                                            $varStorageCounter++;
                                                                                        }
                                                                                    }

                                                                                }

                                                                                if($atLeastOneAdv) {

                                                                                 $output .= '
                                                                                    <script type="text/javascript">
                                                                                        //<![CDATA[

                                                                                            var registerAdvVarName = new Array();


                                                                                            function loadAdvVarPrice() {
                                                                                                var query_string = "advvarkey='.$primkey.'";
                                                                                                var var_string = "";
                                                                                                for(var i in registerAdvVarName)
                                                                                                {
                                                                                                    query_string += "&advvarcombo[]=" + jQuery("#variation_"+registerAdvVarName[i]).val();
                                                                                                    var_string += " " + jQuery("#variation_"+registerAdvVarName[i]).val();
                                                                                                }
                                                                                                jQuery.ajax({ url: "'.plugins_url('/php/loadadvvar.php' , __FILE__).'", type:"POST", data:query_string, success: function(txt){
                                                                                                    advancedVariationPrice = parseFloat(txt);
                                                                                                    ';
                                                                                                if($results[0]['discountprice'] > 0) {
                                                                                                    $output .= 'oldAmount = parseFloat('.$results[0]['discountprice'].');';
                                                                                                } else {
                                                                                                    $output .= 'oldAmount = parseFloat('.$results[0]['price'].');';
                                                                                                }
                                                                                                $output .= '
                                                                                                    newAmount = Math.round((oldAmount + alteredPrice[0] + alteredPrice[1] + alteredPrice[2] + alteredPrice[3] + alteredPrice[4] + alteredPrice[5] + alteredPrice[6] + alteredPrice[7] + alteredPrice[8] + alteredPrice[9] + alteredPrice[10] + alteredPrice[11] + alteredPrice[12] + alteredPrice[13] + advancedVariationPrice) *100)/100;
                                                                                                    advancedVariationName = var_string;
                                                                                                    newName = alteredName[0] + " " + alteredName[1] + " " + alteredName[2] + " " + alteredName[3] + " " + alteredName[4] + " " + alteredName[5] + " " + alteredName[6] + " " + alteredName[7] + " " + alteredName[8] + " " + alteredName[9] + " " + alteredName[10] + " " + alteredName[11] + " " + alteredName[12] + " " + alteredName[13] + advancedVariationName;
                                                                                                    jQuery("#my-item-name").val("'.stripslashes($results[0]['name']).' - " + newName);
                                                                                                    jQuery("#list-item-price").replaceWith("<li id=\'list-item-price\'>Price: '.$devOptions['currency_symbol'].'"+ newAmount.toFixed(2) + "'.$devOptions['currency_symbol_right'].'</li>");
                                                                                                    jQuery("#my-item-price").val(newAmount.toFixed(2));
                                                                                                }});
                                                                                            }

                                                                                        //]]>
                                                                                    </script>
                                                                                 ';
                                                                                $variationTest = array();
                                                                                $variationCounter = 0;
                                                                                if(@is_array($variationStorage) && @isset($variationStorage[0])) {
                                                                                    if(isset($variationStorage)) {

                                                                                            foreach ($variationStorage as $variationStorageCycle) {
                                                                                                if(@!isset($variationTest[$this->slug($variationStorageCycle['variationname'])])) {

                                                                                                 $output .= '
                                                                                                    <script type="text/javascript">
                                                                                                        //<![CDATA[
                                                                                                            registerAdvVarName['.$variationCounter.'] = "'.$this->slug($variationStorageCycle['variationname']).'";
                                                                                                        //]]>
                                                                                                    </script>
                                                                                                 ';
                                                                                                 $voutput .= '
                                                                                                    <li>'.$variationStorageCycle['variationname'].' - <select name="variation_'.$this->slug($variationStorageCycle['variationname']).'" id="variation_'.$this->slug($variationStorageCycle['variationname']).'" onchange="loadAdvVarPrice();">';

                                                                                                }
                                                                                                if(isset($variationStorage)) {
                                                                                                        foreach ($variationStorage as $currentVariation) {
                                                                                                                if (($currentVariation['variationtype']=='advanced' && $currentVariation['variationname']==$variationStorageCycle['variationname']) && $variationTest[$this->slug($variationStorageCycle['variationname'])]!=true) {
                                                                                                                    $option = '<option value="'.$this->slug($currentVariation['variationvalue']).'"';
                                                                                                                    $option .='>';
                                                                                                                    $option .= $currentVariation['variationvalue'];
                                                                                                                    $option .= '</option>';
                                                                                                                    $voutput .=  $option;
                                                                                                                }
                                                                                                        }
                                                                                                }

                                                                                                if(@!isset($variationTest[$this->slug($variationStorageCycle['variationname'])])) {
                                                                                                    $voutput .=  '
                                                                                                    </select>   </li>';
                                                                                                    $variationTest[$this->slug($variationStorageCycle['variationname'])] = true;
                                                                                                }
                                                                                                $variationCounter++;
                                                                                                $variationTest[$this->slug($variationStorageCycle['variationname'])] = true;
                                                                                            }


                                                                                        }
                                                                                }

                                                                            // Product variations
                                                                                if($atLeastOneAdv) {
                                                                                   $voutput .= '
                                                                                        <script type="text/javascript">
                                                                                            //<![CDATA[

                                                                                                loadAdvVarPrice();

                                                                                            //]]>
                                                                                        </script>
                                                                                     ';
                                                                                }
                                                                                // Product variations


                                                                            }
                                                                        } // This is the end of $atLeastOneAdv is true, meaning that we're using advanced variations instead of simple

                                                                        }


                                                                }
                                                            



                                                                // Flat rate shipping implmented here:
                                                                if($devOptions['flatrateshipping']=='all_single') {
                                                                    $result['shipping'] = $devOptions['flatrateamount'];
                                                                } elseif($devOptions['flatrateshipping']=='off' || $devOptions['flatrateshipping']=='all_global') {
                                                                    $result['shipping'] = '0.00';
                                                                }

                                                                // Discount prices
                                                                if($results[0]['discountprice'] > 0) {
                                                                    $theActualPrice = $results[0]['discountprice'];
                                                                } else {
                                                                    $theActualPrice = $results[0]['price'];
                                                                }

                                                                $output .= '
                                                                <form method="post" action="">

                                                                        <input type="hidden" id="my-item-id" name="my-item-id" value="'.$results[0]['primkey'].'" />
                                                                        <input type="hidden" id="my-item-primkey" name="my-item-primkey" value="'.$results[0]['primkey'].'" />
                                                                        <input type="hidden" id="my-item-name" name="my-item-name" value="'.stripslashes($results[0]['name']).'" />
                                                                        <input type="hidden" id="my-item-price" name="my-item-price" value="'.$theActualPrice.'" />
                                                                        <input type="hidden" id="my-item-shipping" name="my-item-shipping" value="'.$result['shipping'].'" />
                                                                        <input type="hidden" id="my-item-img" name="my-item-img" value="'.$results[0]['thumbnail'].'" />
                                                                        <input type="hidden" id="my-item-url" name="my-item-url" value="'.get_permalink($results[0]['postid']).'" />
                                                                        <input type="hidden" id="my-item-tax" name="my-item-tax" value="0" />
                                                                        <input type="hidden" id="my-item-variation" name="my-item-variation" value="0" />

                                                                        <ul class="wpsc-product-info">
                                                                          <li id="list-item-name"><strong>'.stripslashes($results[0]['name']).'</strong></li>';

                                                                    

                                                                          if($results[0]['discountprice']>0) {
                                                                            $output .= '<li id="list-item-price"><strike>Price: '.$devOptions['currency_symbol'].$results[0]['price'].$devOptions['currency_symbol_right'].'</strike> Price: '.$devOptions['currency_symbol'].$results[0]['discountprice'].$devOptions['currency_symbol_right'].'</li>';
                                                                          } else {
                                                                            $output .= '<li id="list-item-price">Price: '.$devOptions['currency_symbol'].$results[0]['price'].$devOptions['currency_symbol_right'].'</li>';
                                                                          }
                                                                          $output .= '<li id="list-item-qty"><label class="wpsc-individualqtylabel">Qty: <input type="text" name="my-item-qty" value="1" size="3"  class="wpsc-individualqty" /></label>					   </li>';


                                                                        if($goutput!=NULL) {
                                                                            $output .= $goutput;
                                                                        }
                                                                        if($voutput!=NULL) {
                                                                            $output .= $voutput;
                                                                        }


                                                                $output .= '
                                                                         </ul>
                                                                ';

                                                               /**
                                                                         * ShareYourCart.com Integration begins here
                                                                         */
                                                                if($devOptions['shareyourcart_activate']=='true') {
                                                                    $output .= '<iframe src="https://www.shareyourcart.com/button?client_id='.$devOptions['shareyourcart_clientid'].'&skin='.$devOptions['shareyourcart_skin'].'&callback_url='. urlencode(plugins_url('/php/shareyourcart/sendcart.php?product='.$results[0]['primkey'], __FILE__))  . '" frameborder="0" border="0" style="border:none;" class="wpsc-shareyourcart-product"></iframe>';
                                                                }
                                                                // ShareYourCart.com Integration ends here


                                                                if($results[0]['useinventory']==0 || ($results[0]['useinventory']==1 && $results[0]['inventory'] > 0) || $devOptions['storetype']=='Digital Goods Only' ) {
                                                                    $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart '.$devOptions['button_classes_addtocart'].'" />';
                                                                } else {
                                                                    $output .= $devOptions['out_of_stock'];
                                                                }

                                                                $output .= '
                                                                </form>
                                                                ';
                                                            
                                                            } elseif ($wpsc_price_type == 'membership' ) {
                                                                
                                                                $output .= '
                                                                <ul class="wpsc-product-info">
                                                                <li id="list-item-name"><strong>'.stripslashes($results[0]['name']).'</strong></li>
                                                                
                                                                ';
                                                                if($wpsc_membership_trial1_allow=='yes') {
                                                                    $output.="<li class=\"list-item-price\">{$devOptions['trial_period_1']} {$devOptions['currency_symbol']}{$wpsc_membership_trial1_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial1_numberof} {$wpsc_membership_trial1_increment_display}</li>";
                                                                }
                                                                if($wpsc_membership_trial2_allow=='yes') {
                                                                    $output.="<li class=\"list-item-price\">{$devOptions['trial_period_2']} {$devOptions['currency_symbol']}{$wpsc_membership_trial2_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial2_numberof} {$wpsc_membership_trial2_increment_display}</li>";
                                                                }
                                                                $output.="<li class=\"list-item-price\">{$devOptions['subscription_price']} {$devOptions['currency_symbol']}{$wpsc_membership_regular_amount}{$devOptions['currency_symbol_right']} {$devOptions['every']} {$wpsc_membership_regular_numberof} {$wpsc_membership_regular_increment_display}</li>";
                                                                $output .= '</ul>';
                                                                if($results[0]['useinventory']==0 || ($results[0]['useinventory']==1 && $results[0]['inventory'] > 0) || $devOptions['storetype']=='Digital Goods Only' ) {
                                                                        // Allows us to bypass registration and have guest only checkout
                                                                        if($devOptions['requireregistration']=='false') {
                                                                            if(@isset($_SESSION['wpsc_email'])) {
                                                                                $purchaser_user_id = 0;
                                                                                $purchaser_email = $wpdb->escape($_SESSION['wpsc_email']);
                                                                                $purchasing_display_name = 'Guest ('.$_SERVER['REMOTE_ADDR'].')';
                                                                            } else {
                                                                                $purchaser_user_id = $current_user->ID;
                                                                                $purchaser_email = $current_user->user_email;
                                                                                $purchasing_display_name = '%user_display_name_with_link%';
                                                                            }
                                                                        } else {
                                                                                $purchaser_user_id = $current_user->ID;
                                                                                $purchaser_email = $current_user->user_email;
                                                                                $purchasing_display_name = '%user_display_name_with_link%';
                                                                        }
                                                                        $wpsc_membership_product_name = $results[0]['name'];
                                                                        $wpsc_membership_product_number = $results[0]['primkey'];
                                                                        $wpsc_paypal_currency_code = $devOptions['currency_code'];
                                                                        $wpsc_paypal_email = $devOptions['paypalemail'];
                                                                        $wpsc_button_classes = $devOptions['button_classes_addtocart'];
                                                                        $wpsc_paypal_ipn = $devOptions['paypalipnurl'];
                                                                        $wpsc_paypal_testmode = $devOptions['paypaltestmode'];
                                                                        $wpsc_self_path = WP_PLUGIN_URL.'/wpsc-membership-pro/';
                                                                        $wpsc_table_name = $wpdb->prefix .'wpstorecart_meta';
                                                                        $wpsc_buy_now = $devOptions['buy_now'];
                                                                        require(WP_PLUGIN_DIR.'/wpsc-membership-pro/paypal.php');
                                                                        $output .= wpscMembershipButton();

                                                                  } else {
                                                                        $output .= '<form>
                                                                        <input type="hidden" name="placeholder" />
                                                                        ';
                                                                        $output .= $devOptions['out_of_stock'];
                                                                        $output .= '</form>';
                                                                }
                                                            }

                                                            if($devOptions['showproductgallery']=='true'  && $devOptions['showproductgallerywhere']=='Directly after the Add to Cart') {
                                                                $output.= $this->wpstorecart_picture_gallery($primkey);
                                                            }

                                                            if($devOptions['showproductdescription']=='true') {
                                                                    $output .= stripslashes($results[0]['introdescription']) . '&nbsp; &nbsp;';
                                                                    if($devOptions['showproductgallery']=='true'  && $devOptions['showproductgallerywhere']=='Directly after the Intro Description') {
                                                                        $output.= $this->wpstorecart_picture_gallery($primkey);
                                                                    }
                                                                    $output .= stripslashes($results[0]['description']);
                                                                    if($devOptions['showproductgallery']=='true'  && $devOptions['showproductgallerywhere']=='Directly after the Description') {
                                                                        $output.= $this->wpstorecart_picture_gallery($primkey);
                                                                    }
                                                            } else {
                                                                    if($devOptions['showproductgallery']=='true'  && ($devOptions['showproductgallerywhere']=='Directly after the Description' || $devOptions['showproductgallerywhere']=='Directly after the Intro Description')) {
                                                                        $output.= $this->wpstorecart_picture_gallery($primkey);
                                                                    }
                                                            }

                                                            if ( 0 == $current_user->ID ) {
                                                                $activity_display_name = 'Guest ('.$_SERVER['REMOTE_ADDR'].')';
                                                            } else {
                                                                $activity_display_name = '%user_display_name_with_link%';
                                                            }

                                                            if(class_exists('ThreeWP_Activity_Monitor')) {
                                                                do_action('threewp_activity_monitor_new_activity', array(
                                                                    'activity_type' => 'wpsc-product-view',
                                                                    'tr_class' => '',
                                                                    'activity' => array(
                                                                        "" => "{$activity_display_name} has viewed the product {$results[0]['name']}",
                                                                    ),
                                                                ));
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

                                            if( !isset( $_GET['cpage'] ) || !is_numeric($_GET['cpage'])) {
                                                $startat = 0;
                                            } else {
                                                $startat = ($_GET['cpage'] - 1) * $quantity;
                                            }

                                            if($devOptions['frontpageDisplays']=='List all products' || $devOptions['frontpageDisplays']=='List newest products') {
                                                if($orderby=='') {
                                                    $sql = "SELECT * FROM `{$table_name}` ORDER BY `dateadded` DESC LIMIT {$startat}, {$quantity};";
                                                } else {
                                                    $sql = "SELECT * FROM `{$table_name}` {$orderby} LIMIT {$startat}, {$quantity};";
                                                }
                                                $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` ORDER BY `dateadded` DESC;");
                                            }
                                            if($devOptions['frontpageDisplays']=='List most popular products') {
                                                if($orderby=='') {
                                                    $sql = "SELECT * FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT {$startat}, {$quantity};";
                                                } else {
                                                    $sql = "SELECT * FROM `{$table_name}` {$orderby} LIMIT {$startat}, {$quantity};";
                                                }
                                                 $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC;");
                                            }
                                            if($devOptions['frontpageDisplays']=='List all categories (Ascending)') {
                                                if($orderby=='') {
                                                    $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` ASC LIMIT {$startat}, {$quantity};";
                                                } else {
                                                    $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 {$orderby} LIMIT {$startat}, {$quantity};";
                                                }
                                                $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` ASC;");
                                                $secondcss = 'wpsc-categories';
                                            } else {
                                                $secondcss = 'wpsc-products';
                                            }
                                            if($devOptions['frontpageDisplays']=='List all categories') {
                                                if($orderby=='') {
                                                    $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` DESC LIMIT {$startat}, {$quantity};";
                                                } else {
                                                    $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 {$orderby} LIMIT {$startat}, {$quantity};";
                                                }
                                                $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` DESC;");
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
                                            if($devOptions['frontpageDisplays']=='List all categories' || $devOptions['frontpageDisplays']=='List all categories (Ascending)') {
                                                if(isset($results)) {
                                                        foreach ($results as $result) {
                                                                if(trim($result['thumbnail']=='')) {
                                                                    $result['thumbnail'] = plugins_url('/images/default_product_img.jpg' , __FILE__);
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
                                                                if($usetext=='true' && $devOptions['displayTitle']=='true') {
                                                                        $output .= '<p><a href="'.$permalink.'">'.stripslashes($result['category']).'</a></p>';
                                                                }
                                                                if($devOptions['displayintroDesc']=='true'){
                                                                        $output .= '<p>'.stripslashes($result['description']).'</p>';
                                                                }
                                                                $output .= '</div>';
                                                        }
                                                        $output .= '<div class="wpsc-clear"></div>';
                                                }
                                            } else { // This is for products:
                                                if(isset($results)) {
                                                        foreach ($results as $result) {

                                                                // This code checks to see if we will be potentially displaying subscription products with either the price or add to cart button visible.  If so, we query each product for subscription information
                                                                $wpsc_price_type = 'charge';
                                                                $membership_value = '';
                                                                if(file_exists(WP_PLUGIN_DIR.'/wpsc-membership-pro/wpsc-membership-pro.php') && ($devOptions['displaypriceonview']=='true' || $devOptions['displayAddToCart']=='true')){
                                                                    $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                                                                    $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$result['primkey']};";
                                                                    $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                                                                    if(isset($resultsMembership)) {
                                                                        foreach ($resultsMembership as $pagg) {
                                                                            $membership_primkey = $pagg['primkey'];
                                                                            $membership_value = $pagg['value'];
                                                                        }
                                                                        if($membership_value!='') {
                                                                            $theExploded = explode('||', $membership_value);
                                                                            // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                                                                            $wpsc_membership_product_id = $resultsMembership[0]['primkey'];
                                                                            $wpsc_price_type = $theExploded[0];
                                                                            $wpsc_membership_trial1_allow = $theExploded[1];
                                                                            $wpsc_membership_trial2_allow = $theExploded[2];
                                                                            $wpsc_membership_trial1_amount = $theExploded[3];
                                                                            $wpsc_membership_trial2_amount = $theExploded[4];
                                                                            $wpsc_membership_regular_amount = $theExploded[5];
                                                                            $wpsc_membership_trial1_numberof = $theExploded[6];
                                                                            $wpsc_membership_trial2_numberof = $theExploded[7];
                                                                            $wpsc_membership_regular_numberof = $theExploded[8];
                                                                            $wpsc_membership_trial1_increment = $theExploded[9];
                                                                            $wpsc_membership_trial2_increment = $theExploded[10];
                                                                            $wpsc_membership_regular_increment = $theExploded[11];
                                                                            if($wpsc_membership_trial1_increment=='D'){$wpsc_membership_trial1_increment_display=$devOptions['day'];}
                                                                            if($wpsc_membership_trial2_increment=='D'){$wpsc_membership_trial2_increment_display=$devOptions['day'];}
                                                                            if($wpsc_membership_regular_increment=='D'){$wpsc_membership_regular_increment_display=$devOptions['day'];}
                                                                            if($wpsc_membership_trial1_increment=='W'){$wpsc_membership_trial1_increment_display=$devOptions['week'];}
                                                                            if($wpsc_membership_trial2_increment=='W'){$wpsc_membership_trial2_increment_display=$devOptions['week'];}
                                                                            if($wpsc_membership_regular_increment=='W'){$wpsc_membership_regular_increment_display=$devOptions['week'];}
                                                                            if($wpsc_membership_trial1_increment=='M'){$wpsc_membership_trial1_increment_display=$devOptions['month'];}
                                                                            if($wpsc_membership_trial2_increment=='M'){$wpsc_membership_trial2_increment_display=$devOptions['month'];}
                                                                            if($wpsc_membership_regular_increment=='M'){$wpsc_membership_regular_increment_display=$devOptions['month'];}
                                                                            if($wpsc_membership_trial1_increment=='Y'){$wpsc_membership_trial1_increment_display=$devOptions['year'];}
                                                                            if($wpsc_membership_trial2_increment=='Y'){$wpsc_membership_trial2_increment_display=$devOptions['year'];}
                                                                            if($wpsc_membership_regular_increment=='Y'){$wpsc_membership_regular_increment_display=$devOptions['year'];}
                                                                        }
                                                                    }
                                                                }

                                                                $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                                if($devOptions['displayType']=='grid'){
                                                                        $output .= '<div class="wpsc-grid wpsc-products">';
                                                                }
                                                                if($devOptions['displayType']=='list'){
                                                                        $output .= '<div class="wpsc-list wpsc-products">';
                                                                }
                                                                if($usepictures=='true') {
                                                                        $output .= '<a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$result['thumbnail'].'" alt="'.stripslashes($result['name']).'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= 'style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a>';
                                                                }
                                                                if($usetext=='true' && $devOptions['displayTitle']=='true') {
                                                                        $output .= '<a href="'.$permalink.'"><h1 class="wpsc-h1">'.stripslashes($result['name']).'</h1></a>';
                                                                }
                                                                if($devOptions['displayintroDesc']=='true'){
                                                                        $output .= '<p>'.stripslashes($result['introdescription']).'</p>';
                                                                }
                                                                if($devOptions['displaypriceonview']=='true'){
                                                                    if($wpsc_price_type == 'membership') {
                                                                        $output .= '<ul class="wpsc-product-info">';
                                                                        if($wpsc_membership_trial1_allow=='yes') {
                                                                            $output.="<li class=\"list-item-price\">{$devOptions['trial_period_1']} {$devOptions['currency_symbol']}{$wpsc_membership_trial1_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial1_numberof} {$wpsc_membership_trial1_increment_display}</li>";
                                                                        }
                                                                        if($wpsc_membership_trial2_allow=='yes') {
                                                                            $output.="<li class=\"list-item-price\">{$devOptions['trial_period_2']} {$devOptions['currency_symbol']}{$wpsc_membership_trial2_amount}{$devOptions['currency_symbol_right']} {$devOptions['for']} {$wpsc_membership_trial2_numberof} {$wpsc_membership_trial2_increment_display}</li>";
                                                                        }
                                                                        $output.="<li class=\"list-item-price\">{$devOptions['subscription_price']} {$devOptions['currency_symbol']}{$wpsc_membership_regular_amount}{$devOptions['currency_symbol_right']} {$devOptions['every']} {$wpsc_membership_regular_numberof} {$wpsc_membership_regular_increment_display}</li>";
                                                                        $output .= '</ul>';
                                                                    } else {
                                                                        if($result['discountprice']>0) {
                                                                            $output.="<span class=\"wpsc-{$devOptions['displayType']}-price\"><strike>{$devOptions['currency_symbol']}{$result['price']}{$devOptions['currency_symbol_right']}</strike> {$devOptions['currency_symbol']}{$result['discountprice']}{$devOptions['currency_symbol_right']}</span>";
                                                                        } else {
                                                                            $output.="<span class=\"wpsc-{$devOptions['displayType']}-price\">{$devOptions['currency_symbol']}{$result['price']}{$devOptions['currency_symbol_right']}</span>";
                                                                        }
                                                                    }
                                                                }
                                                                if($devOptions['displayAddToCart']=='true'){
                                                                    if($wpsc_price_type == 'charge') {
                                                                        // Flat rate shipping implmented here:
                                                                        if($devOptions['flatrateshipping']=='all_single') {
                                                                            $result['shipping'] = $devOptions['flatrateamount'];
                                                                        } elseif($devOptions['flatrateshipping']=='off' || $devOptions['flatrateshipping']=='all_global') {
                                                                            $result['shipping'] = '0.00';
                                                                        }
                                                                        // Discount prices
                                                                        if($result['discountprice'] > 0) {
                                                                            $theActualPrice = $result['discountprice'];
                                                                        } else {
                                                                            $theActualPrice = $result['price'];
                                                                        }
                                                                        $output .= '
                                                                        <form method="post" action="">

                                                                                <input type="hidden" name="my-item-id" value="'.$result['primkey'].'" />
                                                                                <input type="hidden" name="my-item-primkey" value="'.$result['primkey'].'" />
                                                                                <input type="hidden" name="my-item-name" value="'.stripslashes($result['name']).'" />
                                                                                <input type="hidden" name="my-item-price" value="'.$theActualPrice.'" />
                                                                                <input type="hidden" name="my-item-shipping" value="'.$result['shipping'].'" />
                                                                                <input type="hidden" id="my-item-img" name="my-item-img" value="'.$result['thumbnail'].'" />
                                                                                <input type="hidden" id="my-item-url" name="my-item-url" value="'.get_permalink($result['postid']).'" />
                                                                                <input type="hidden" id="my-item-tax" name="my-item-tax" value="0" />
                                                                                <label class="wpsc-qtylabel">Qty: <input type="text" name="my-item-qty" value="1" size="3" class="wpsc-qty" /></label>

                                                                        ';

                                                                        if($result['useinventory']==0 || ($result['useinventory']==1 && $result['inventory'] > 0) || $devOptions['storetype']=='Digital Goods Only' ) {

                                                                                                                                    // Product variations
                                                                            $table_name30 = $wpdb->prefix . "wpstorecart_meta";
                                                                            $grabrecord = "SELECT * FROM `{$table_name30}` WHERE `type`='productvariation' AND `foreignkey`={$primkey};";

                                                                            $vresults = $wpdb->get_results( $grabrecord , ARRAY_A );

                                                                            if(isset($vresults)) {
                                                                                $results_disable_add_to_cart = $wpdb->get_results("SELECT `value` FROM `{$table_name30}` WHERE `type`='disableaddtocart' AND `foreignkey`={$result['primkey']};", ARRAY_N);
                                                                                if(!isset($results_disable_add_to_cart[0][0]) ) {
                                                                                    $display_add_to_cart_at_all_times = 'no';
                                                                                } else {
                                                                                    if($results_disable_add_to_cart[0][0]=='yes') {
                                                                                        $display_add_to_cart_at_all_times = 'yes';
                                                                                    } else {
                                                                                        $display_add_to_cart_at_all_times = 'no';
                                                                                    }
                                                                                }
                                                                                if($display_add_to_cart_at_all_times=='no') { // will display the Add to Cart if there are no variations or if it is set to display automatically.
                                                                                    $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart '.$devOptions['button_classes_addtocart'].'" />';
                                                                                }
                                                                            }
                                                                        } else {
                                                                            $output .= $devOptions['out_of_stock'];
                                                                        }


                                                                        $output .= '
                                                                        </form>
                                                                        ';
                                                                    } elseif ($wpsc_price_type == 'membership' ) {
                                                                            $output .= $this->displaySubscriptionBuyNow($result['primkey'], false);

                                                                    }
                                                                }


                                                                $output .= '</div>';
                                                        }

                                                        $output .= '<div class="wpsc-clear"></div>';
                                                        $output .= '<div class="wpsc-navigation">';

                                                        $comments_per_page = $quantity;
                                                        $page = isset( $_GET['cpage'] ) ? abs( (int) $_GET['cpage'] ) : 1;

                                                        $output .= paginate_links( array(
                                                            'base' => add_query_arg( 'cpage', '%#%' ),
                                                            'format' => '',
                                                            'prev_text' => __('&laquo;'),
                                                            'next_text' => __('&raquo;'),
                                                            'total' => ceil($total / $comments_per_page),
                                                            'current' => $page
                                                        ));

                                                        $output .= '</div>';

                                                        $output .= '<div class="wpsc-clear"></div>';
                                                }
                                            }
                                        } else {
                                            if($_GET['wpsc']=='affiliate') {
                                                global $affiliatemanager, $affiliatesettings, $affiliatepurchases;

                                                if(class_exists('ThreeWP_Activity_Monitor')) {
                                                    do_action('threewp_activity_monitor_new_activity', array(
                                                        'activity_type' => 'wpsc-affiliate-view',
                                                        'tr_class' => '',
                                                        'activity' => array(
                                                            "" => "%user_display_name_with_link% has viewed the affiliate management page.",
                                                        ),
                                                    ));
                                                }

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
                                                @include_once(WP_PLUGIN_DIR.'/wpsc-affiliates-pro/saStoreCartPro/affiliates.pro.php');
                                                echo @wpscAffiliates();
                                                $affiliatemanager = false;
                                            }
                                            if($_GET['wpsc']=='orders') {
                                                $output .= $devOptions['myordersandpurchases'];

                                                // ** Here's where we disable the user login system during checkout if registration is not required
                                                if ( is_user_logged_in() ) {
                                                    $isLoggedIn = true;
                                                    if(class_exists('ThreeWP_Activity_Monitor')) {
                                                        do_action('threewp_activity_monitor_new_activity', array(
                                                            'activity_type' => 'wpsc-orders-view',
                                                            'tr_class' => '',
                                                            'activity' => array(
                                                                "" => "%user_display_name_with_link% has viewed their Downloads and Orders page.",
                                                            ),
                                                        ));
                                                    }

                                                } else {
                                                    if($devOptions['requireregistration']=='false') {
                                                        if(@isset($_POST['guest_email'])) {
                                                            $_SESSION['wpsc_email'] = $wpdb->escape($_POST['guest_email']);
                                                        }
                                                        if(@isset($_SESSION['wpsc_email'])) {
                                                            $isLoggedIn = true;
                                                        } else {
                                                            $output .= '
                                                                <form name="wpsc-nonregisterform" id="wpsc-nonregisterform" action="#" method="post">
                                                                    <label><span>'. $devOptions['email'] .' <ins><div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div></ins></span><input type="text" name="guest_email" value="'.$_SESSION['wpsc_email'].'" /></label>
                                                                    <input type="submit">
                                                                </form>
                                                                ';
                                                            $isLoggedIn = false;

                                                        }
                                                    } else {
                                                       $isLoggedIn = false;
                                                    }
                                                }

                                                if ( $isLoggedIn == false ) {
                                                    // Not logged in.
                                                } else {
                                                    // Logged in.
                                                    $table_name3 = $wpdb->prefix . "wpstorecart_orders";
                                                    if ( is_user_logged_in()  ) { // for logged in users
                                                        $sql = "SELECT * FROM `{$table_name3}` WHERE `wpuser`='{$current_user->ID}' ORDER BY `date` DESC;";
                                                    } else { // For guests
                                                        $sql = "SELECT * FROM `{$table_name3}` WHERE `wpuser`='0' AND `email`='{$_SESSION['wpsc_email']}' ORDER BY `date` DESC;";
                                                    }

                                                    $results = $wpdb->get_results( $sql , ARRAY_A );
                                                    if(isset($results)) {
                                                            $output .= '<table><tr><td>Order Status</td><td>Date</td><td>Items</td><td>Total Price</td></tr>';
                                                            foreach ($results as $result) {
                                                                $output .= '<tr><td>'.$result['orderstatus'].'</td><td>'.$result['date'].'</td><td>'.$this->splitOrderIntoProduct($result['primkey'], 'download').'</td><td>'.$result['price'].'</td></tr>';
                                                            }
                                                            $output .= '</table>';
                                                    }

                                                    if ( is_user_logged_in()  ) {

                                                        if($devOptions['orders_profile']=='editable' || $devOptions['orders_profile']== 'both') {
                                                            /* Added 2.3.8 */
                                                            global $current_user, $wp_roles;
                                                            get_currentuserinfo();

                                                            /* Load the registration file. */
                                                            require_once( ABSPATH . WPINC . '/registration.php' );
                                                            $error = false;

                                                            /* If profile was saved, update profile. */
                                                            if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

                                                                /* Update user password. */
                                                                if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
                                                                    if ( $_POST['pass1'] == $_POST['pass2'] )
                                                                        wp_update_user( array( 'ID' => $current_user->id, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
                                                                    else
                                                                        $error = __('The passwords you entered do not match.  Your password was not updated.', 'profile');
                                                                }

                                                                /* Update user information. */
                                                                if ( !empty( $_POST['url'] ) )
                                                                    update_usermeta( $current_user->id, 'user_url', esc_url( $_POST['url'] ) );
                                                                if ( !empty( $_POST['email'] ) )
                                                                    update_usermeta( $current_user->id, 'user_email', esc_attr( $_POST['email'] ) );
                                                                if ( !empty( $_POST['first-name'] ) )
                                                                    update_usermeta( $current_user->id, 'first_name', esc_attr( $_POST['first-name'] ) );
                                                                if ( !empty( $_POST['last-name'] ) )
                                                                    update_usermeta($current_user->id, 'last_name', esc_attr( $_POST['last-name'] ) );
                                                                if ( !empty( $_POST['description'] ) )
                                                                    update_usermeta( $current_user->id, 'description', esc_attr( $_POST['description'] ) );


                                                                if ( $error ) {
                                                                    $output .= '<p class="error">'.$error.'</p>';
                                                                }
                                                            }
                                                        }

                                                        if($devOptions['orders_profile']=='display' || $devOptions['orders_profile']== 'both') {
                                                            $output .= '<br />';
                                                            $output .= 'Username: ' . $current_user->user_login . '<br />';
                                                            $output .= 'Email: ' . $current_user->user_email . '<br />';
                                                            $output .= 'First name: ' . $current_user->user_firstname . '<br />';
                                                            $output .= 'Last name: ' . $current_user->user_lastname . '<br />';
                                                            $output .= 'Display name: ' . $current_user->display_name . '<br />';
                                                            $output .= 'User ID: ' . $current_user->ID . '<br />';
                                                        }

                                                        if($devOptions['orders_profile']=='editable' || $devOptions['orders_profile']== 'both') {
                                                            $output .= '<form method="post" id="adduser" action="'. get_permalink().'">
                                                                <table>
                                                                <tr class="wpsc-profile-form-username">
                                                                    <td><label for="first-name">'; $output .= translate('First Name', 'profile'); $output .= '</label></td>
                                                                    <td><input class="text-input" name="first-name" type="text" id="first-name" value="'. get_the_author_meta( 'user_firstname', $current_user->id ).'" /></td>
                                                                </tr><!-- .form-username -->
                                                                <tr class="wpsc-profile-form-username">
                                                                    <td><label for="last-name">'; $output .= translate('Last Name', 'profile'); $output .= '</label></td>
                                                                    <td><input class="text-input" name="last-name" type="text" id="last-name" value="'. get_the_author_meta( 'user_lastname', $current_user->id ) .'" /></td>
                                                                </tr><!-- .form-username -->
                                                                <tr class="wpsc-profile-form-email">
                                                                    <td><label for="email">'; $output .= translate('E-mail *', 'profile'); $output .= '</label></td>
                                                                    <td><input class="text-input" name="email" type="text" id="email" value="'. get_the_author_meta( 'user_email', $current_user->id ).'" /></td>
                                                                </tr><!-- .form-email -->
                                                                <tr class="wpsc-profile-form-url">
                                                                    <td><label for="url">'; $output .= translate('Website', 'profile'); $output .= '</label></td>
                                                                    <td><input class="text-input" name="url" type="text" id="url" value="'. get_the_author_meta( 'user_url', $current_user->id ).'" /></td>
                                                                </tr><!-- .form-url -->
                                                                <tr class="wpsc-profile-form-password">
                                                                    <td><label for="pass1">'; $output .= translate('Password *', 'profile'); $output .= ' </label></td>
                                                                    <td><input class="text-input" name="pass1" type="password" id="pass1" /></td>
                                                                </tr><!-- .form-password -->
                                                                <tr class="wpsc-profile-form-password">
                                                                    <td><label for="pass2">'; $output .= translate('Repeat Password *', 'profile'); $output .= '</label></td>
                                                                    <td><input class="text-input" name="pass2" type="password" id="pass2" /></td>
                                                                </tr><!-- .form-password -->
                                                                <tr class="wpsc-profile-form-textarea">
                                                                    <td><label for="description">'; $output .= translate('Biographical Information', 'profile') ; $output .= '</label></td>
                                                                    <td><textarea name="description" id="description" rows="3" cols="50">'. get_the_author_meta( 'description', $current_user->id ).'</textarea></td>
                                                                </tr><!-- .form-textarea -->
                                                                <tr class="wpsc-profile-form-submit">
                                                                    <td></td><td>'. $referer.'
                                                                    <input name="updateuser" type="submit" id="updateuser" class="submit button" value="'; $output .= translate('Update', 'profile'); ; $output .= '" />
                                                                    '; $output .= wp_nonce_field( 'update-user' );
                                                                    $output .= '<input name="action" type="hidden" id="action" value="update-user" />
                                                                </td></tr><!-- .form-submit -->
                                                                </table>
                                                            </form><!-- #adduser -->';
                                                        }




                                                    } else {
                                                        $output .= '<br />Email: ' . $_SESSION['wpsc_email'] . '<br />';
                                                        $output .= '
                                                            <form name="wpsc-nonregisterform" id="wpsc-nonregisterform" action="#" method="post">
                                                                <label><span>'. $devOptions['email'] .' <ins><div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div></ins></span><input type="text" name="guest_email" value="'.$_SESSION['wpsc_email'].'" /></label>
                                                                <input type="submit">
                                                            </form>
                                                            ';
                                                    }

                                                }
                                            }
                                            if($_GET['wpsc']=='manual') {
                                                $output .= '<h2>Order total: '.$devOptions['currency_symbol']. $_GET['price'] .$devOptions['currency_symbol_right'].'</h2>';
                                                $output .= $devOptions['checkmoneyordertext'];
                                                if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                                                    $permalink = get_permalink($devOptions['mainpage']) .'?wpsc=manualresponse&order='.$_GET['order'];
                                                } else {
                                                    $permalink = get_permalink($devOptions['mainpage']) .'&wpsc=manualresponse&order='.$_GET['order'];
                                                }
                                                $output .= '<form action="'.$permalink.'" method="post"><textarea class="wpsc-textarea" name="manualresponsetext"></textarea><input type="submit" class="wpsc-button '.$devOptions['button_classes_meta'].'" value="Submit" /> </form>';
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
                                            if($_GET['wpsc']=='failed') {
                                                $output .= $this->makeEmailTxt($devOptions['failed_text'].'<br /><br />'.$_GET['wpscerror']);
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
                                                    /* <![CDATA[ */
                                                  var gaJsHost = (("https:" == document.location.protocol ) ? "https://ssl." : "http://www.");
                                                  document.write(unescape("%3Cscript src=\'" + gaJsHost + "google-analytics.com/ga.js\' type=\'text/javascript\'%3E%3C/script%3E"));
                                                  /* ]]> */
                                                </script>';
                                                echo '
                                                <script type="text/javascript">
                                                /* <![CDATA[ */
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
                                                /* ]]> */
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
                        } else {
                            $output = 'wpStoreCart has been switched off.';
                        }



			return $output;
		}
		// END SHORTCODE ================================================

		function add_script_swfobject($posts){
			if (empty($posts)) return $posts;
		 
			wp_enqueue_script('swfobject');

			return $posts;
		}		

                function overlay_css() {
                    global $APjavascriptQueue, $hasOverlayCss;

                    if(!isset($hasOverlayCss)) {
                        $APjavascriptQueue .= '

                            <script type="text/javascript">
                            /* <![CDATA[ */
                                jQuery.noConflict();
                                jQuery(window).load(function() {

                                        jQuery("a[rel]").overlay({

                                                effect: \'apple\',
                                                mask: {
                                                    color: \'#91d8ff\',
                                                    loadSpeed: 2000,
                                                    opacity: 0.93
                                                },
                                                onBeforeLoad: function() {
                                                        var wrap = this.getOverlay().find(".contentWrap");
                                                        wrap.load(this.getTrigger().attr("href"));
                                                }

                                        });
                                });
                                /* ]]> */
                            </script>

                            <style type="text/css">
                            .apple_overlay {
                                    display:none;
                                    width:640px;
                                    padding:0px;
                                    font-size:11px;
                                    z-index:99999;
                                    position: absolute;
                            }


                            .apple_overlay .close {
                                    background-image:url('.plugins_url('/images/wizard/button_cancel.png' , __FILE__).');
                                    position:absolute; right:8px; top:11px;
                                    cursor:pointer;
                                    height:32px;
                                    width:33px;
                            }
                            #overlay {
                                    position: absolute;
                                    z-index:99999;
                                    background-image:url('.plugins_url('/images/wizard/background.png' , __FILE__).');
                                    color:#efefef;
                                    width:640px;
                                    height:480px;
                            }

                            /* container for external content. uses vertical scrollbar, if needed */
                            div.contentWrap {
                                    height:480px;
                                    z-index:99999;
                                    position: absolute;

                            }

                            div.contentWrap a:hover{
                                opacity:0.8;
                            }
                            </style>
                        ';
                        $hasOverlayCss = 'isset';
                    }
                }


                function my_import_scripts() {
                    wp_enqueue_script('swfupload');
                }

		function my_mainpage_scripts() {
			global $APjavascriptQueue;

            
                        $APjavascriptQueue .= '
                            <link href="'.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/charting/css/basic.css" type="text/css" rel="stylesheet" />
                            <script type="text/javascript" src="'.WP_PLUGIN_URL . '/wpstorecart/js/jqVisualize/_shared/EnhanceJS/enhance.js"></script>
                            <script type="text/javascript">
                            /* <![CDATA[ */
                            // Run capabilities test

                            jQuery(document).ready(function($) {
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
                            /* ]]> */
                            </script>
                                ';


                        wp_enqueue_script('toolbox_expose',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/toolbox.expose.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jqt_overlay',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jqt_overlay_apple',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.apple.min.js',array('jquery'),'1.4' );
                        $this->overlay_css();

		}		
		
		function my_tooltip_script() {
			global $APjavascriptQueue;

                        wp_enqueue_script('jquery-ui-core',array('jquery'),'1.4');
                        wp_enqueue_script('jquery-ui-sortable',array('jquery'),'1.4');
			wp_enqueue_script('ezpz_tooltip',WP_PLUGIN_URL . '/wpstorecart/js/jquery.ezpz_tooltip.js',array('jquery'),'1.4' );
                        wp_enqueue_script('toolbox_expose',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/toolbox.expose.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jqt_overlay',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jqt_overlay_apple',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.apple.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jq-validation-engine-en', WP_PLUGIN_URL .'/wpstorecart/js/formValidator/js/languages/jquery.validationEngine-wpsc.js',array('jquery'),'1.6');
                        wp_enqueue_script('jq-validation-engine', WP_PLUGIN_URL .'/wpstorecart/js/formValidator/js/jquery.validationEngine.js',array('jquery'),'1.6');
                        wp_enqueue_script('imagemapster', WP_PLUGIN_URL .'/wpstorecart/js/imagemapster/jquery.imagemapster.1.2b2.js',array('jquery'),'1.6');


			$APjavascriptQueue .= '
                        <link rel="stylesheet" href="'.WP_PLUGIN_URL .'/wpstorecart/js/formValidator/css/validationEngine.jquery.css" type="text/css" media="all" />    
                        ';

                        $this->overlay_css();

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
                                        $("#wpscform").validationEngine("attach");
				});
			//]]>
			</script>
			';
			
		}


		function admin_script_anytime() {
			global $APjavascriptQueue;

                        wp_enqueue_script('toolbox_expose',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/toolbox.expose.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jqt_overlay',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jqt_overlay_apple',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.apple.min.js',array('jquery'),'1.4' );
			wp_enqueue_script('ezpz_tooltip',WP_PLUGIN_URL . '/wpstorecart/js/jquery.ezpz_tooltip.js',array('jquery'),'1.4' );
			wp_enqueue_script('anytime',WP_PLUGIN_URL . '/wpstorecart/js/anytime/anytimec.js',array('jquery'),'1.4' );
                        $this->overlay_css();
		
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

                    if(!is_admin()) {
                        $devOptions = $this->getAdminOptions();


                        if($devOptions['wpscCss']!='') {
                            $myStyleUrl = plugins_url('/themes/'.$devOptions['wpscCss'] , __FILE__);
                            $myStyleFile = WP_PLUGIN_DIR . '/wpstorecart/themes/'.$devOptions['wpscCss'];
                            if ( file_exists($myStyleFile) ) {
                                wp_register_style('myStyleSheets', $myStyleUrl);
                                wp_enqueue_style( 'myStyleSheets');
                            }
                        }

                        if($devOptions['wpscjQueryUITheme']!='') {
                            $myStyleUrljQUI = plugins_url('/jqueryui/css/'.$devOptions['wpscjQueryUITheme'].'/jquery-ui-1.8.7.custom.css' , __FILE__);
                            $myStyleFilejQUI = WP_PLUGIN_DIR . '/wpstorecart/jqueryui/css/'.$devOptions['wpscjQueryUITheme'].'/jquery-ui-1.8.7.custom.css';
                            if ( file_exists($myStyleFilejQUI) ) {
                                wp_register_style('myStyleSheetsjQUI', $myStyleUrljQUI);
                                wp_enqueue_style( 'myStyleSheetsjQUI');
                            }
                        }
                        if($devOptions['useimagebox']=='thickbox' && !is_admin()) {
                            wp_enqueue_style('thickbox.css', '/'.WPINC.'/js/thickbox/thickbox.css', null, '1.0');
                        }
                    }
                }

                function enqueue_my_scripts() {
                    $devOptions = $this->getAdminOptions();
                    wp_enqueue_script('jquery-ui-effects', WP_PLUGIN_URL .'/wpstorecart/js/jquery-ui-effects-1.8.11.min.js',array('jquery'),'1.4');
                    if($devOptions['useimagebox']=='thickbox' && !is_admin()) {
                        wp_enqueue_script('thickbox',null,array('jquery'));
                    }
                }

		function my_admin_scripts(){
			global $APjavascriptQueue;

                        
			wp_tiny_mce( false , // true makes the editor "teeny"
				array(
					"editor_selector" => "wpStoreCartproduct_description"
				)
			);		 

                        wp_enqueue_script('jeditable-wpsc', WP_PLUGIN_URL .'/wpstorecart/js/jquery.jeditable.mini.js',array('jquery'),'1.4');
                        wp_enqueue_script('jquery-ui-effects', WP_PLUGIN_URL .'/wpstorecart/js/jquery-ui-effects-1.8.11.min.js',array('jquery'),'1.4');
			wp_enqueue_script('swfupload');
			wp_enqueue_script('ezpz_tooltip',WP_PLUGIN_URL . '/wpstorecart/js/jquery.ezpz_tooltip.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jquery-ui-core',array('jquery'),'1.4');
                        wp_enqueue_script('jquery-ui-sortable',array('jquery'),'1.4');
                        wp_enqueue_script('jq-validation-engine-en', WP_PLUGIN_URL .'/wpstorecart/js/formValidator/js/languages/jquery.validationEngine-wpsc.js',array('jquery'),'1.6');
                        wp_enqueue_script('jq-validation-engine', WP_PLUGIN_URL .'/wpstorecart/js/formValidator/js/jquery.validationEngine.js',array('jquery'),'1.6');

			if (session_id() == "") {@session_start();};
			
			$APjavascriptQueue = '';
			
			$APjavascriptQueue .= '
                        <link rel="stylesheet" href="'.WP_PLUGIN_URL .'/wpstorecart/js/formValidator/css/validationEngine.jquery.css" type="text/css" media="all" />

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

                            #upload-progressbar-container, #upload-progressbar-container2, #upload-progressbar-container3, #upload-progressbar-container4 {
                                min-width:200px;
                                max-width:200px;
                                min-height:20px;
                                max-height:20px;
                                background-color:#FFF;
                                display:block;
                            }
                            #upload-progressbar, #upload-progressbar2, #upload-progressbar3, #upload-progressbar4 {
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
                                $("#wpstorecartaddproductform").validationEngine("attach");
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
			var productUploadSuccessEventHandler4 = function (file, server_data, receivedResponse) {
                                jQuery("#uploadimage4").attr("src","'.WP_PLUGIN_URL.'/wpstorecart/images/white.gif");
				document.wpstorecartaddproductform.wpStoreCartproduct_download_pg.value = document.wpstorecartaddproductform.wpStoreCartproduct_download_pg.value + file.name + "||";
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

                        function uploadProgress4(file, bytesLoaded, bytesTotal) {
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
			var swfu2;
                        var swfu3;
                        var swfu4;
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

				var settings_object4 = {
					upload_url : "'.WP_PLUGIN_URL.'/wpstorecart/php/upload.php",
					post_params: {"PHPSESSID" : "'.session_id().'"},
					flash_url : "'.get_option( 'siteurl' ).'/wp-includes/js/swfupload/swfupload.swf",
					file_size_limit : "2048 MB",
					file_types : "*.jpg;*.gif;*.png;*.jpeg;*.bmp;*.tiff;",
					file_types_description : "Image files",
					file_upload_limit : "0",
					file_post_name: "Filedata",
					button_placeholder_id : "spanSWFUploadButton4",
					button_image_url : "'.WP_PLUGIN_URL.'/wpstorecart/images/XPButtonUploadText_61x22.png",
					button_width: 61,
					button_height: 22,
					debug : false,
					debug_handler : debugSWFUpload,
					file_dialog_complete_handler: beginTheUpload,
                                        upload_progress_handler: uploadProgress4,
					upload_start_handler : productUploadStartEventHandler,
					upload_success_handler : productUploadSuccessEventHandler4,
					upload_error_handler : uploadError
				};

				swfu = new SWFUpload(settings_object); 
				swfu2 = new SWFUpload(settings_object2);
                                swfu3 = new SWFUpload(settings_object3);
                                swfu4 = new SWFUpload(settings_object4);
			};


			//]]>
			</script>			
			';

                        wp_enqueue_script('toolbox_expose',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/toolbox.expose.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jqt_overlay',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.min.js',array('jquery'),'1.4' );
                        wp_enqueue_script('jqt_overlay_apple',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.apple.min.js',array('jquery'),'1.4' );
                        $this->overlay_css();

		}			

                function placeAdminFooter() {
                    echo '			<!-- overlayed element -->
                        <div class="apple_overlay" id="overlay">

                                <!-- the external content is loaded inside this tag -->
                                <div class="contentWrap"></div>

                        </div>';
                }

                function placeAdminHeaderEnqueue() {
                    wp_enqueue_script('toolbox_expose',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/toolbox.expose.min.js',array('jquery'),'1.4' );
                    wp_enqueue_script('jqt_overlay',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.min.js',array('jquery'),'1.4' );
                    wp_enqueue_script('jqt_overlay_apple',WP_PLUGIN_URL . '/wpstorecart/js/jqt_overlay/overlay.apple.min.js',array('jquery'),'1.4' );
                    wp_enqueue_script('jeegoocontext',WP_PLUGIN_URL . '/wpstorecart/js/jeegoocontext/jeegoocontext/jquery.jeegoocontext.min.js',array('jquery'),'1.4' );
                }

		function placeAdminHeaderCode() {
			global $APjavascriptQueue;

                        $this->overlay_css();

                        $APjavascriptQueue .= '

                        <style type="text/css">



                        /* menu styles */

                        #jsddm
                        {	margin: 0;
                                padding: 0
                                position:relative;
                                z-index:999;
                                font: 11px "Segoe UI", Tahoma, Arial;
                        }

                                #jsddm li
                                {	float: left;
                                        list-style: none;
                                        
                                position:relative;
                                z-index:999;
                                }

                                #jsddm li a
                                {	display: block;
                                        background: #FFF url("'.get_option( 'siteurl' ).'/wp-admin/images/gray-grad.png") repeat;
                                        padding: 4px 10px;
                                        text-decoration: none;
                                        border-right: 1px solid white;
                                        color: #000;
                                        white-space: nowrap;
                                        position:relative;
                                        z-index:999;
                                        font: 11px "Segoe UI", Tahoma, Arial;
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
                                                z-index:999;
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

                        <link href="'.plugins_url('/js/jeegoocontext/jeegoocontext/skins/cm_default/style.css' , __FILE__).'" rel="Stylesheet" type="text/css" />

                        <script type="text/javascript">
                            /* <![CDATA[ */


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
                            /* ]]> */
                        </script>
                        ';

			echo $APjavascriptQueue;
		}
				
                function makeEmailTxt($theEmail, $theEmailAddressOrderID = 0) {
                    global $current_user, $wpdb;
                    get_currentuserinfo();

                    $devOptions = $this->getAdminOptions();

                    if($theEmailAddressOrderID == 0) {
                        $theEmail = str_replace("[customername]", $current_user->display_name, $theEmail);
                    } else {
                        $table_name = $wpdb->prefix . "wpstorecart_orders";
                        $sql = "SELECT `email` FROM `{$table_name}` WHERE `primkey`={$theEmailAddressOrderID};";
                        $results = $wpdb->get_results( $sql , ARRAY_A );
                        $theEmailAddress = 'Customer';
                        if(isset($results)) {
                            $theEmailAddress = $results[0]['email'];
                        }
                        $theEmail = str_replace("[customername]", $theEmailAddress, $theEmail);
                    }
                    $theEmail = str_replace("[sitename]", get_bloginfo(), $theEmail);
                    if(trim($devOptions['orderspage'])!='') {
                        $theEmail = str_replace("[downloadurl]", get_permalink($devOptions['orderspage']), $theEmail);
                    } else {
                        if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                            $permalink = get_permalink($devOptions['mainpage']) .'?wpsc=orders';
                        } else {
                            $permalink = get_permalink($devOptions['mainpage']) .'&wpsc=orders';
                        }
                        $theEmail = str_replace("[downloadurl]", $permalink, $theEmail);
                    }
                    
                    return $theEmail;
                }

                /**
                     *  Assign a serial number to an order and email them the serial number
                     *
                     * @global object $wpdb
                     * @param integer $productid The product that you are pulling the serial number from
                     * @param integer $orderid The order that has the serial number associated with it
                     */
                function assignSerialNumber($productid, $orderid=0) {
                    global $wpdb;
                    $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                    $table_name2 = $wpdb->prefix . "wpstorecart_products";
                    $table_name = $wpdb->prefix . "wpstorecart_orders";

                    $devOptions = $this->getAdminOptions();

                    // Grabs the serial numbers
                    $results_serial_numbers = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='serialnumbers' AND `foreignkey`={$productid};", ARRAY_N);
                    $results_serial_numbers_used = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='serialnumbersused' AND `foreignkey`={$productid};", ARRAY_N);
                    if($results_serial_numbers!=false ) {
                        $wpStoreCartproduct_serial_numbers = base64_decode($results_serial_numbers[0][0]);
                        if($results_serial_numbers!=false ) {
                            $wpStoreCartproduct_serial_numbers_used  = base64_decode($results_serial_numbers_used[0][0]);
                        } else {
                            $wpStoreCartproduct_serial_numbers_used  = '';
                        }
                        $grab_one = explode("\n",$wpStoreCartproduct_serial_numbers);
                        $wpStoreCartproduct_serial_numbers_used = $grab_one[0]."\n".$wpStoreCartproduct_serial_numbers_used;
                        $wpStoreCartproduct_serial_numbers = str_replace($grab_one[0]."\n", "", $wpStoreCartproduct_serial_numbers);
                        $results111 = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".base64_encode($grab_one[0])."', 'serialnumberassigned', '{$orderid}');");
                        $results222 = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = '".base64_encode($wpStoreCartproduct_serial_numbers)."' WHERE `type`='serialnumbers' AND `foreignkey` = {$productid};");
                        if($results_serial_numbers!=false ) {
                            $results333 = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = '".base64_encode($wpStoreCartproduct_serial_numbers_used)."' WHERE `type`='serialnumbersused' AND `foreignkey` = {$productid};");
                        } else {
                            $results333 = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".base64_encode($wpStoreCartproduct_serial_numbers_used)."', 'serialnumberassigned', '{$productid}');");
                        }
                        if($results111 && $results222 && $results333 && $orderid!=0) {
                            // Do the email here
                            $sql2 = "SELECT `name` FROM `{$table_name2}` WHERE `primkey`={$current_item[0]};";
                            $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );
                            $theProductsName = $devOptions['single_item'];
                            if(isset($moreresults) && $moreresults[0]['name']!='') {
                                    $theProductsName = $moreresults[0]['name'];
                            }
                            $theEmail = $devOptions['emailserialnumber'];
                            $theEmail = str_replace("[productname]", $theProductsName, $theEmail);
                            $theEmail = str_replace("[serialnumber]", $grab_one[0], $theEmail);
                            $message = $theEmail;

                            $headers = 'From: '.$devOptions['wpStoreCartEmail'] . "\r\n" .
                                'Reply-To: ' .$devOptions['wpStoreCartEmail']. "\r\n" .
                                'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;

                            // Send an email when purchase is submitted
                            $sql = "SELECT `email` FROM `{$table_name}` WHERE `primkey`={$orderid};";
                            $results = $wpdb->get_results( $sql , ARRAY_A );
                            if(isset($results)) {
                                mail($results[0]['email'], 'The serial number for your recent purchase', $message, $headers);
                            }
                        }
                    }

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

        function listProductDownloads($primkey, $type="download") {
            global $wpdb;
            $table_name = $wpdb->prefix . "wpstorecart_orders";
            $table_name2 = $wpdb->prefix . "wpstorecart_products";
            $table_name3 = $wpdb->prefix . "wpstorecart_meta";
            $thevariationdetail[0] = NULL;
            $thevariationdetail[1] = NULL;
            $output = NULL;
            $sql3 = "SELECT * FROM `{$table_name3}` WHERE `type`='productvariation' AND `foreignkey`={$primkey};";
            $moreresults3 = $wpdb->get_results( $sql3 , ARRAY_A );
            if(@isset($moreresults3[0])) {
                    $thevariationdetail = explode('||',$moreresults3[0]['value']);
                    if(@isset($thevariationdetail[4])) { // If the variation has downloads associated with it
                            $variationdownloads = explode('****',$thevariationdetail[4]);
                    }
            }

            $sql2 = "SELECT `primkey`, `name`, `download`, `postid` FROM `{$table_name2}` WHERE `primkey`={$primkey};";
            $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );

            if($type=="download" && isset($moreresults[0])) {
                    $output .= ', <br />';
                    if($output==', <br />') {$output = '';}
                    if($moreresults[0]['download']=='') { // Non-downloads products below:
                            $output .= $moreresults[0]['download'].' '.$thevariationdetail[0].' '.$thevariationdetail[1];
                    } else { // Download products below:
                            if(@isset($variationdownloads)) { // If we've got variations that have downloads
                                    foreach ($variationdownloads as $variationdownload) {
                                            if($variationdownload!='') {
                                                $output .= '<a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.stripslashes($variationdownload).'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png"> '.$variationdownload.'</a> (Variation Download)<br />';
                                            }
                                    }
                            }

                            $multidownloads = explode('||', $moreresults[0]['download']);
                            if(@isset($multidownloads[0]) && @isset($multidownloads[1])) {
                                    $downloadcount = 0;
                                    foreach($multidownloads as $multidownload) {
                                            if($multidownload!='') {
                                                    $output .= '<a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.stripslashes($multidownload).'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png"> '.$multidownload.'</a><br />';
                                            }
                                                    $downloadcount++;
                                    }
                            } else {
                                    $output .= '<a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.stripslashes($moreresults[0]['download']).'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png"> '.$moreresults[0]['download'].'</a>';
                            }
                    }

            }

            return $output;

        }


        function slug($str) {
                $str = strtolower(trim($str));
                $str = preg_replace('/[^a-z0-9-]/', '_', $str);
                $str = preg_replace('/-+/', "_", $str);
                return $str;
        }

        function grab_custom_reg_fields() {
            global $wpdb;
            $table_name = $wpdb->prefix . "wpstorecart_meta";
            if($this->wpStoreCartRegistrationFields==null) {
                $sql = "SELECT * FROM `{$table_name}` WHERE `type`='requiredinfo' ORDER BY `foreignkey` ASC;";
                $results = $wpdb->get_results( $sql , ARRAY_A );
                $this->wpStoreCartRegistrationFields = $results;
            } 

            return $this->wpStoreCartRegistrationFields;
            
        }

        /**
         *
         * Creates
         *
         * @global object $wpdb
         * @param <type> $contactmethods
         * @return array
         */
        function add_custom_contactmethod( $contactmethods ) {
            global $wpdb;

            $fields = $this->grab_custom_reg_fields();
            foreach ($fields as $field) {
                $specific_items = explode("||", $field['value']);
                    if($specific_items[2]!='separator' && $specific_items[2]!='header' && $specific_items[2]!='text') {
                        $slug = $this->slug($specific_items[0]);
                        $contactmethods[$slug] = $wpdb->escape($specific_items[0]); // This makes something like this: $contactmethods['address'] = 'Address';
                    }

            }

            return $contactmethods;
        }

        function show_custom_reg_fields(){
            $devOptions = $this->getAdminOptions();

            $disable_inline_styles = ' style="float:left;"';
            if($devOptions['disable_inline_styles']=='true') {
                $disable_inline_styles = '';
            }            

            $output = '';
            $fields = $this->grab_custom_reg_fields();
            foreach ($fields as $field) {
                $specific_items = explode("||", $field['value']);
                    // $contactmethods[$this->slug($specific_item[0])] = $specific_item[0];
                    if($specific_items[2]=='input (text)') {
                        $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="'.$this->slug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.$this->slug($specific_items[0])].'" name="'.$this->slug($specific_items[0]).'" /></td></tr>';
                    }
                    if($specific_items[2]=='input (numeric)') {
                        $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="'.$this->slug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.$this->slug($specific_items[0])].'" name="'.$this->slug($specific_items[0]).'" /></td></tr>';
                    }
                    if($specific_items[2]=='textarea') {
                        $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div></ins>';}$output.='</td><td><textarea class="input" id="'.$this->slug($specific_items[0]).'" name="'.$this->slug($specific_items[0]).'">'.$_SESSION['wpsc_'.$this->slug($specific_items[0])].'</textarea></td></tr>';
                    }
                    if($specific_items[2]=='states' || $specific_items[2]=='taxstates') {
                        $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div></ins>';}$output.='</td><td><select class="input" name="'; if ($specific_items[2]=='states'){$output.= $this->slug($specific_items[0]);} else {$output.='taxstate';} $output.='" class="wpsc-states">
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
                        </select></td></tr>';
                    }
                    if($specific_items[2]=='countries' || $specific_items[2]=='taxcountries') {
                        $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div></ins>';}$output.='</td><td><select class="input" name="'; if($specific_items[2]=='countries') {$output.=$this->slug($specific_items[0]);} else {$output.='taxcountries';} $output.='" class="wpsc-countries">
                        <option value="United States" selected="selected">United States</option>
                        <option value="Canada">Canada</option>
                        <option value="United Kingdom" >United Kingdom</option>
                        <option value="Ireland" >Ireland</option>
                        <option value="Australia" >Australia</option>
                        <option value="New Zealand" >New Zealand</option>
                        <option value="Afghanistan">Afghanistan</option>
                        <option value="Albania">Albania</option>
                        <option value="Algeria">Algeria</option>
                        <option value="American Samoa">American Samoa</option>
                        <option value="Andorra">Andorra</option>
                        <option value="Angola">Angola</option>
                        <option value="Anguilla">Anguilla</option>
                        <option value="Antarctica">Antarctica</option>
                        <option value="Antigua and Barbuda">Antigua and Barbuda</option>
                        <option value="Argentina">Argentina</option>
                        <option value="Armenia">Armenia</option>
                        <option value="Aruba">Aruba</option>
                        <option value="Australia">Australia</option>
                        <option value="Austria">Austria</option>
                        <option value="Azerbaijan">Azerbaijan</option>
                        <option value="Bahamas">Bahamas</option>
                        <option value="Bahrain">Bahrain</option>
                        <option value="Bangladesh">Bangladesh</option>
                        <option value="Barbados">Barbados</option>
                        <option value="Belarus">Belarus</option>
                        <option value="Belgium">Belgium</option>
                        <option value="Belize">Belize</option>
                        <option value="Benin">Benin</option>
                        <option value="Bermuda">Bermuda</option>
                        <option value="Bhutan">Bhutan</option>
                        <option value="Bolivia">Bolivia</option>
                        <option value="Bosnia and Herzegovina">Bosnia and Herzegovina</option>
                        <option value="Botswana">Botswana</option>
                        <option value="Bouvet Island">Bouvet Island</option>
                        <option value="Brazil">Brazil</option>
                        <option value="British Indian Ocean Territory">British Indian Ocean Territory</option>
                        <option value="Brunei Darussalam">Brunei Darussalam</option>
                        <option value="Bulgaria">Bulgaria</option>
                        <option value="Burkina Faso">Burkina Faso</option>
                        <option value="Burundi">Burundi</option>
                        <option value="Cambodia">Cambodia</option>
                        <option value="Cameroon">Cameroon</option>
                        <option value="Canada">Canada</option>
                        <option value="Cape Verde">Cape Verde</option>
                        <option value="Cayman Islands">Cayman Islands</option>
                        <option value="Central African Republic">Central African Republic</option>
                        <option value="Chad">Chad</option>
                        <option value="Chile">Chile</option>
                        <option value="China">China</option>
                        <option value="Christmas Island">Christmas Island</option>
                        <option value="Cocos (Keeling) Islands">Cocos (Keeling) Islands</option>
                        <option value="Colombia">Colombia</option>
                        <option value="Comoros">Comoros</option>
                        <option value="Congo">Congo</option>
                        <option value="Congo, The Democratic Republic of The">Congo, The Democratic Republic of The</option>
                        <option value="Cook Islands">Cook Islands</option>
                        <option value="Costa Rica">Costa Rica</option>
                        <option value="Cote D\'ivoire">Cote D\'ivoire</option>
                        <option value="Croatia">Croatia</option>
                        <option value="Cuba">Cuba</option>
                        <option value="Cyprus">Cyprus</option>
                        <option value="Czech Republic">Czech Republic</option>
                        <option value="Denmark">Denmark</option>
                        <option value="Djibouti">Djibouti</option>
                        <option value="Dominica">Dominica</option>
                        <option value="Dominican Republic">Dominican Republic</option>
                        <option value="Ecuador">Ecuador</option>
                        <option value="Egypt">Egypt</option>
                        <option value="El Salvador">El Salvador</option>
                        <option value="Equatorial Guinea">Equatorial Guinea</option>
                        <option value="Eritrea">Eritrea</option>
                        <option value="Estonia">Estonia</option>
                        <option value="Ethiopia">Ethiopia</option>
                        <option value="Falkland Islands (Malvinas)">Falkland Islands (Malvinas)</option>
                        <option value="Faroe Islands">Faroe Islands</option>
                        <option value="Fiji">Fiji</option>
                        <option value="Finland">Finland</option>
                        <option value="France">France</option>
                        <option value="French Guiana">French Guiana</option>
                        <option value="French Polynesia">French Polynesia</option>
                        <option value="French Southern Territories">French Southern Territories</option>
                        <option value="Gabon">Gabon</option>
                        <option value="Gambia">Gambia</option>
                        <option value="Georgia">Georgia</option>
                        <option value="Germany">Germany</option>
                        <option value="Ghana">Ghana</option>
                        <option value="Gibraltar">Gibraltar</option>
                        <option value="Greece">Greece</option>
                        <option value="Greenland">Greenland</option>
                        <option value="Grenada">Grenada</option>
                        <option value="Guadeloupe">Guadeloupe</option>
                        <option value="Guam">Guam</option>
                        <option value="Guatemala">Guatemala</option>
                        <option value="Guinea">Guinea</option>
                        <option value="Guinea-bissau">Guinea-bissau</option>
                        <option value="Guyana">Guyana</option>
                        <option value="Haiti">Haiti</option>
                        <option value="Heard Island and Mcdonald Islands">Heard Island and Mcdonald Islands</option>
                        <option value="Holy See (Vatican City State)">Holy See (Vatican City State)</option>
                        <option value="Honduras">Honduras</option>
                        <option value="Hong Kong">Hong Kong</option>
                        <option value="Hungary">Hungary</option>
                        <option value="Iceland">Iceland</option>
                        <option value="India">India</option>
                        <option value="Indonesia">Indonesia</option>
                        <option value="Iran, Islamic Republic of">Iran, Islamic Republic of</option>
                        <option value="Iraq">Iraq</option>
                        <option value="Ireland">Ireland</option>
                        <option value="Israel">Israel</option>
                        <option value="Italy">Italy</option>
                        <option value="Jamaica">Jamaica</option>
                        <option value="Japan">Japan</option>
                        <option value="Jordan">Jordan</option>
                        <option value="Kazakhstan">Kazakhstan</option>
                        <option value="Kenya">Kenya</option>
                        <option value="Kiribati">Kiribati</option>
                        <option value="Korea, Democratic People\'s Republic of">Korea, Democratic People\'s Republic of</option>
                        <option value="Korea, Republic of">Korea, Republic of</option>
                        <option value="Kuwait">Kuwait</option>
                        <option value="Kyrgyzstan">Kyrgyzstan</option>
                        <option value="Lao People\'s Democratic Republic">Lao People\'s Democratic Republic</option>
                        <option value="Latvia">Latvia</option>
                        <option value="Lebanon">Lebanon</option>
                        <option value="Lesotho">Lesotho</option>
                        <option value="Liberia">Liberia</option>
                        <option value="Libyan Arab Jamahiriya">Libyan Arab Jamahiriya</option>
                        <option value="Liechtenstein">Liechtenstein</option>
                        <option value="Lithuania">Lithuania</option>
                        <option value="Luxembourg">Luxembourg</option>
                        <option value="Macao">Macao</option>
                        <option value="Macedonia, The Former Yugoslav Republic of">Macedonia, The Former Yugoslav Republic of</option>
                        <option value="Madagascar">Madagascar</option>
                        <option value="Malawi">Malawi</option>
                        <option value="Malaysia">Malaysia</option>
                        <option value="Maldives">Maldives</option>
                        <option value="Mali">Mali</option>
                        <option value="Malta">Malta</option>
                        <option value="Marshall Islands">Marshall Islands</option>
                        <option value="Martinique">Martinique</option>
                        <option value="Mauritania">Mauritania</option>
                        <option value="Mauritius">Mauritius</option>
                        <option value="Mayotte">Mayotte</option>
                        <option value="Mexico">Mexico</option>
                        <option value="Micronesia, Federated States of">Micronesia, Federated States of</option>
                        <option value="Moldova, Republic of">Moldova, Republic of</option>
                        <option value="Monaco">Monaco</option>
                        <option value="Mongolia">Mongolia</option>
                        <option value="Montserrat">Montserrat</option>
                        <option value="Morocco">Morocco</option>
                        <option value="Mozambique">Mozambique</option>
                        <option value="Myanmar">Myanmar</option>
                        <option value="Namibia">Namibia</option>
                        <option value="Nauru">Nauru</option>
                        <option value="Nepal">Nepal</option>
                        <option value="Netherlands">Netherlands</option>
                        <option value="Netherlands Antilles">Netherlands Antilles</option>
                        <option value="New Caledonia">New Caledonia</option>
                        <option value="New Zealand">New Zealand</option>
                        <option value="Nicaragua">Nicaragua</option>
                        <option value="Niger">Niger</option>
                        <option value="Nigeria">Nigeria</option>
                        <option value="Niue">Niue</option>
                        <option value="Norfolk Island">Norfolk Island</option>
                        <option value="Northern Mariana Islands">Northern Mariana Islands</option>
                        <option value="Norway">Norway</option>
                        <option value="Oman">Oman</option>
                        <option value="Pakistan">Pakistan</option>
                        <option value="Palau">Palau</option>
                        <option value="Palestinian Territory, Occupied">Palestinian Territory, Occupied</option>
                        <option value="Panama">Panama</option>
                        <option value="Papua New Guinea">Papua New Guinea</option>
                        <option value="Paraguay">Paraguay</option>
                        <option value="Peru">Peru</option>
                        <option value="Philippines">Philippines</option>
                        <option value="Pitcairn">Pitcairn</option>
                        <option value="Poland">Poland</option>
                        <option value="Portugal">Portugal</option>
                        <option value="Puerto Rico">Puerto Rico</option>
                        <option value="Qatar">Qatar</option>
                        <option value="Reunion">Reunion</option>
                        <option value="Romania">Romania</option>
                        <option value="Russian Federation">Russian Federation</option>
                        <option value="Rwanda">Rwanda</option>
                        <option value="Saint Helena">Saint Helena</option>
                        <option value="Saint Kitts and Nevis">Saint Kitts and Nevis</option>
                        <option value="Saint Lucia">Saint Lucia</option>
                        <option value="Saint Pierre and Miquelon">Saint Pierre and Miquelon</option>
                        <option value="Saint Vincent and The Grenadines">Saint Vincent and The Grenadines</option>
                        <option value="Samoa">Samoa</option>
                        <option value="San Marino">San Marino</option>
                        <option value="Sao Tome and Principe">Sao Tome and Principe</option>
                        <option value="Saudi Arabia">Saudi Arabia</option>
                        <option value="Senegal">Senegal</option>
                        <option value="Serbia and Montenegro">Serbia and Montenegro</option>
                        <option value="Seychelles">Seychelles</option>
                        <option value="Sierra Leone">Sierra Leone</option>
                        <option value="Singapore">Singapore</option>
                        <option value="Slovakia">Slovakia</option>
                        <option value="Slovenia">Slovenia</option>
                        <option value="Solomon Islands">Solomon Islands</option>
                        <option value="Somalia">Somalia</option>
                        <option value="South Africa">South Africa</option>
                        <option value="South Georgia and The South Sandwich Islands">South Georgia and The South Sandwich Islands</option>
                        <option value="Spain">Spain</option>
                        <option value="Sri Lanka">Sri Lanka</option>
                        <option value="Sudan">Sudan</option>
                        <option value="Suriname">Suriname</option>
                        <option value="Svalbard and Jan Mayen">Svalbard and Jan Mayen</option>
                        <option value="Swaziland">Swaziland</option>
                        <option value="Sweden">Sweden</option>
                        <option value="Switzerland">Switzerland</option>
                        <option value="Syrian Arab Republic">Syrian Arab Republic</option>
                        <option value="Taiwan, Province of China">Taiwan, Province of China</option>
                        <option value="Tajikistan">Tajikistan</option>
                        <option value="Tanzania, United Republic of">Tanzania, United Republic of</option>
                        <option value="Thailand">Thailand</option>
                        <option value="Timor-leste">Timor-leste</option>
                        <option value="Togo">Togo</option>
                        <option value="Tokelau">Tokelau</option>
                        <option value="Tonga">Tonga</option>
                        <option value="Trinidad and Tobago">Trinidad and Tobago</option>
                        <option value="Tunisia">Tunisia</option>
                        <option value="Turkey">Turkey</option>
                        <option value="Turkmenistan">Turkmenistan</option>
                        <option value="Turks and Caicos Islands">Turks and Caicos Islands</option>
                        <option value="Tuvalu">Tuvalu</option>
                        <option value="Uganda">Uganda</option>
                        <option value="Ukraine">Ukraine</option>
                        <option value="United Arab Emirates">United Arab Emirates</option>
                        <option value="United Kingdom">United Kingdom</option>
                        <option value="United States">United States</option>
                        <option value="United States Minor Outlying Islands">United States Minor Outlying Islands</option>
                        <option value="Uruguay">Uruguay</option>
                        <option value="Uzbekistan">Uzbekistan</option>
                        <option value="Vanuatu">Vanuatu</option>
                        <option value="Venezuela">Venezuela</option>
                        <option value="Viet Nam">Viet Nam</option>
                        <option value="Virgin Islands, British">Virgin Islands, British</option>
                        <option value="Virgin Islands, U.S.">Virgin Islands, U.S.</option>
                        <option value="Wallis and Futuna">Wallis and Futuna</option>
                        <option value="Western Sahara">Western Sahara</option>
                        <option value="Yemen">Yemen</option>
                        <option value="Zambia">Zambia</option>
                        <option value="Zimbabwe">Zimbabwe</option>
                        </select></td></tr>';
                    }
                    if($specific_items[2]=='email') {
                        $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="'.$this->slug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.$this->slug($specific_items[0])].'" name="'.$this->slug($specific_items[0]).'" /></td></tr>';
                    }
                    if($specific_items[2]=='separator') {
                        $output .= $specific_items[0] .'<tr><td></td><td></td></tr>';
                    }
                    if($specific_items[2]=='header') {
                        $output .= '</table><h2>'.$specific_items[0] .'</h2><table>';
                    }
                    if($specific_items[2]=='text') {
                        $output .= '</table><br />'.$specific_items[0] .'<br /><table>';
                    }

                    
                
            }

            return $output;


        }

        function register_extra_fields($user_id, $password="", $meta=array()) {
            global $wpdb;
            if ( !current_user_can( 'edit_user', $user_id ) ) { 
                return false;
            } else {
                $userdata = array();
                $userdata['ID'] = $user_id;
                wp_update_user($userdata);

                $fields = $this->grab_custom_reg_fields();
                foreach ($fields as $field) {
                    $specific_items = explode("||", $field['value']);
                    foreach ($specific_items as $specific_item) {
                        if($specific_item[2]!='separator' && $specific_item[2]!='header' && $specific_item[2]!='text') {
                            update_usermeta( $user_id, $this->slug($specific_item[0]), $wpdb->escape($_POST[$this->slug($specific_item[0])]) );
                        }
                    }
                }


            }
        }

        function check_fields($login, $email, $errors) {
                global $wpdb;

                // Make sure errors are displayed for empty fields which are marked as required
                $fields = $this->grab_custom_reg_fields();
                foreach ($fields as $field) {
                    $specific_items = explode("||", $field['value']);

                    if($specific_items[2]!='separator' && $specific_items[2]!='header' && $specific_items[2]!='text') {
                        $current_field = trim($_POST[$this->slug($specific_items[0])]);
                        if ($specific_items[1]=='required' && $current_field=='') {
                            $_SESSION['wpsc_'.$this->slug($specific_items[0])]=$_POST[$this->slug($specific_items[0])]; // This allows us to save data in case the form needs to be refilled out due to it being incomplete
                            $errors->add('empty_'.$this->slug($specific_items[0]), "<strong>ERROR</strong>: Please Enter in {$specific_items[0]}");
                        }
                    }

                }

        }


        /**
         *
         * USPSParcelRate
         *
         * @param <type> $weight
         * @param <type> $dest_zip
         * @return array
         */
        function USPSParcelRate($weight,$dest_zip) {

            $devOptions = $this->getAdminOptions();

            // This script was written by Mark Sanborn at http://www.marksanborn.net
            // If this script benefits you are your business please consider a donation
            // You can donate at http://www.marksanborn.net/donate.

            // ========== CHANGE THESE VALUES TO MATCH YOUR OWN ===========

            $userName = $devOptions['uspsapiname']; // Your USPS Username
            $orig_zip = $devOptions['shipping_zip_origin']; // Zipcode you are shipping FROM

            // =============== DON'T CHANGE BELOW THIS LINE ===============

            $url = "http://Production.ShippingAPIs.com/ShippingAPI.dll";
            $ch = curl_init();

            // set the target url
            curl_setopt($ch, CURLOPT_URL,$url);
            curl_setopt($ch, CURLOPT_HEADER, 1);
            curl_setopt($ch,CURLOPT_RETURNTRANSFER,1);

            // parameters to post
            curl_setopt($ch, CURLOPT_POST, 1);

            $data = "API=RateV3&XML=<RateV3Request USERID=\"$userName\"><Package ID=\"1ST\"><Service>PRIORITY</Service><ZipOrigination>$orig_zip</ZipOrigination><ZipDestination>$dest_zip</ZipDestination><Pounds>$weight</Pounds><Ounces>0</Ounces><Size>REGULAR</Size><Machinable>TRUE</Machinable></Package></RateV3Request>";

            // send the POST values to USPS
            curl_setopt($ch, CURLOPT_POSTFIELDS,$data);

            $result=curl_exec ($ch);
            $data = strstr($result, '<?');
            // echo '<!-- '. $data. ' -->'; // Uncomment to show XML in comments
            $xml_parser = xml_parser_create();
            xml_parse_into_struct($xml_parser, $data, $vals, $index);
            xml_parser_free($xml_parser);
            $params = array();
            $level = array();
            foreach ($vals as $xml_elem) {
                    if ($xml_elem['type'] == 'open') {
                            if (array_key_exists('attributes',$xml_elem)) {
                                    list($level[$xml_elem['level']],$extra) = array_values($xml_elem['attributes']);
                            } else {
                            $level[$xml_elem['level']] = $xml_elem['tag'];
                            }
                    }
                    if ($xml_elem['type'] == 'complete') {
                    $start_level = 1;
                    $php_stmt = '$params';
                    while($start_level < $xml_elem['level']) {
                            $php_stmt .= '[$level['.$start_level.']]';
                            $start_level++;
                    }
                    $php_stmt .= '[$xml_elem[\'tag\']] = $xml_elem[\'value\'];';
                    eval($php_stmt);
                    }
            }
            curl_close($ch);
            //echo '<pre>'; print_r($params); echo'</pre>'; // Uncomment to see xml tags
            return $params['RATEV3RESPONSE']['1ST']['1']['RATE'];
        }

        function wpstorecart_needs_to_start_sessions_before_anything_else() {
                global $cart, $wpsc_cart_type;
                require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
                require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
                require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
                if($wpsc_cart_type == 'session') {
                    if(!isset($_SESSION)) {
                            @session_start();
                    }
                    if(@!is_object($cart)) {
                        $cart =& $_SESSION['wpsc'];
                        if(@!is_object($cart)) {
                            $cart = new wpsc();
                        }
                    }
                }

                if($wpsc_cart_type == 'cookie') {
                    if(@!is_object($cart)) {
                        if(isset($_COOKIE['wpsccart'])) { @$cart =& unserialize(base64_decode($_COOKIE['wpsccart'])); }
                        if(@!is_object($cart) && !isset($_COOKIE['wpsccart'])) {
                            $cart = new wpsc();
                            $xdomain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;setcookie('wpsccart', base64_encode(serialize($cart)), time()+7222, '/', $xdomain, false);
                        }
                    }
                    if(!isset($_SESSION)) { @session_start(); }
                }
               
            //echo '<!-- /**'.var_dump(unserialize(base64_decode($_COOKIE['wpsccart']))).' **/ -->';

        }


    }

 /*
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
                                                            $result['thumbnail'] = plugins_url('/images/default_product_img.jpg' , __FILE__);
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
			global $wpdb, $cart, $wpsc, $is_checkout,$wpscCarthasBeenCalled, $wpscWidgetSettings, $wpsc_cart_type;
                        $wpscWidgetSettings = array();

			extract( $args );
			$title = apply_filters('widget_title', $instance['title']);
                        $widgetShowproductImages = empty($instance['widgetShowproductImages']) ? 'false' : $instance['widgetShowproductImages'];
                        $widgetShowTax = empty($instance['widgetShowTax']) ? 'true' : $instance['widgetShowTax'];
                        $widgetShowSubtotal = empty($instance['widgetShowSubtotal']) ? 'true' : $instance['widgetShowSubtotal'];
                        $widgetShowTotal = empty($instance['widgetShowTotal']) ? 'true' : $instance['widgetShowTotal'];
                        $widgetShowShipping = empty($instance['widgetShowShipping']) ? 'true' : $instance['widgetShowShipping'];
                        $wpscWidgetSettings['iswidget']=true;
                        $wpscWidgetSettings['widgetShowTax']=$widgetShowTax;
                        $wpscWidgetSettings['widgetShowSubtotal']=$widgetShowSubtotal;
                        $wpscWidgetSettings['widgetShowTotal']=$widgetShowTotal;
                        $wpscWidgetSettings['widgetShowShipping']=$widgetShowShipping;
                        
                        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
                        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
                        require_once(ABSPATH . '/wp-content/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');
                        if($wpsc_cart_type == 'session') {
                            if(!isset($_SESSION)) {
                                    @session_start();
                            }
                            if(@!is_object($cart)) {
                                $cart =& $_SESSION['wpsc'];
                                if(@!is_object($cart)) {
                                    $cart = new wpsc();
                                }
                            }
                        }

                        if($wpsc_cart_type == 'cookie') {
                            if(@!is_object($cart)) {
                                if(isset($_COOKIE['wpsccart'])) { @$cart =& unserialize(base64_decode($_COOKIE['wpsccart'])); }
                                if(@!is_object($cart) && !isset($_COOKIE['wpsccart'])) {
                                    $cart = new wpsc();
                                    $xdomain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;setcookie('wpsccart', base64_encode(serialize($cart)), time()+7222, '/', $xdomain, false);
                                }
                            }
                            if(!isset($_SESSION)) { @session_start(); }
                        }
			$output = NULL;

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
                        $instance['widgetShowShipping'] = strip_tags(stripslashes($new_instance['widgetShowShipping']));
                        $instance['widgetShowTax'] = strip_tags(stripslashes($new_instance['widgetShowTax']));
                        $instance['widgetShowSubtotal'] = strip_tags(stripslashes($new_instance['widgetShowSubtotal']));
                        $instance['widgetShowTotal'] = strip_tags(stripslashes($new_instance['widgetShowTotal']));
			return $instance;
		}

		/** @see WP_Widget::form */
		function form($instance) {
			@$title = esc_attr($instance['title']);
                        @$widgetShowproductImages = htmlspecialchars($instance['widgetShowproductImages']);
                        @$widgetShowShipping = htmlspecialchars($instance['widgetShowShipping']);
                        @$widgetShowSubtotal = htmlspecialchars($instance['widgetShowSubtotal']);
                        @$widgetShowTax = htmlspecialchars($instance['widgetShowTax']);
                        @$widgetShowTotal = htmlspecialchars($instance['widgetShowTotal']);
			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Use as the final checkout:') . '<br /><label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowShipping') . '">' . __('Show shipping costs:') . '<br /><label for="' . $this->get_field_name('widgetShowShipping') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowShipping') . '_yes" name="' . $this->get_field_name('widgetShowShipping') . '" value="true" '; if ($widgetShowShipping == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowShipping') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowShipping') . '_no" name="' . $this->get_field_name('widgetShowShipping') . '" value="false" '; if ($widgetShowShipping == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowTax') . '">' . __('Show tax:') . '<br /><label for="' . $this->get_field_name('widgetShowTax') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowTax') . '_yes" name="' . $this->get_field_name('widgetShowTax') . '" value="true" '; if ($widgetShowTax == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowTax') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowTax') . '_no" name="' . $this->get_field_name('widgetShowTax') . '" value="false" '; if ($widgetShowTax == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowSubtotal') . '">' . __('Show subtotal without shipping:') . '<br /><label for="' . $this->get_field_name('widgetShowSubtotal') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowSubtotal') . '_yes" name="' . $this->get_field_name('widgetShowSubtotal') . '" value="true" '; if ($widgetShowSubtotal == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowSubtotal') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowSubtotal') . '_no" name="' . $this->get_field_name('widgetShowSubtotal') . '" value="false" '; if ($widgetShowSubtotal == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowTotal') . '">' . __('Show total, including any shipping:') . '<br /><label for="' . $this->get_field_name('widgetShowTotal') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowTotal') . '_yes" name="' . $this->get_field_name('widgetShowTotal') . '" value="true" '; if ($widgetShowTotal == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowTotal') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowTotal') . '_no" name="' . $this->get_field_name('widgetShowTotal') . '" value="false" '; if ($widgetShowTotal == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
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


                        if($devOptions['orderspage']=='') {
                            if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
                                $permalink = get_permalink($devOptions['mainpage']) .'?wpsc=orders';
                            } else {
                                $permalink = get_permalink($devOptions['mainpage']) .'&wpsc=orders';
                            }
                        } else {
                             $permalink = get_permalink($devOptions['orderspage']);
                        }
                        
                        if ( is_user_logged_in() ) {
                                $output .= '<ul>';
                                $output .= '<li><a href="'.$permalink.'">'.$devOptions['myordersandpurchases'].'</a></li>';
                                $output .= '<li><a href="'.wp_logout_url(get_permalink()).'">'.$devOptions['logout'].'</a></li>';

                                $output .= '</ul>';
                        } else {

                             $output .= '
                            <strong>'.$devOptions['login'].'</strong><br />
                            <form id="login" method="post" action="'. wp_login_url( get_permalink() ) .'">
                                                                    
                                <label>'.$devOptions['username'].' <input type="text" value="" name="log" /></label><br />
                                <label>'.$devOptions['password'].' <input type="password" value="" name="pwd"  /></label><br />
                                <input type="submit" value="Login" />

                            </form>

                            ';
                             if($devOptions['requireregistration']=='false') {
                                $output .= '<ul>';
                                $output .= '<li><a href="'.$permalink.'">'.$devOptions['myordersandpurchases'].'</a></li>';
                                $output .= '</ul>';

                             }
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
						$output .= '<p><a href="'.$permalink.'">'.stripslashes($result['name']).'</a></p>';
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
						$output .= '<p><a href="'.$permalink.'">'.stripslashes($result['name']).'</a></p>';
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

        $devOptions = $wpStoreCart->getAdminOptions();
        if (function_exists('add_menu_page')) {
            $mainPage = add_menu_page('wpStoreCart - Open Source WP Shopping Cart &amp; eCommerce Plugin', 'wpStoreCart', 'manage_wpstorecart', 'wpstorecart-admin', array(&$wpStoreCart, 'printAdminPageOverview'), plugins_url('/images/controller.png' , __FILE__));
            $settingsPage = add_submenu_page('wpstorecart-admin','Settings - wpStoreCart ', 'Settings', 'manage_wpstorecart', 'wpstorecart-settings', array(&$wpStoreCart, 'printAdminPage'));
            $page = add_submenu_page('wpstorecart-admin','Add product - wpStoreCart ', 'Add product', 'manage_wpstorecart', 'wpstorecart-add-products', array(&$wpStoreCart, 'printAdminPageAddproducts'));
            $editproductpage = add_submenu_page('wpstorecart-admin','Edit products - wpStoreCart ', 'Edit products', 'manage_wpstorecart', 'wpstorecart-edit-products', array(&$wpStoreCart, 'printAdminPageEditproducts'));

            $importpage = add_submenu_page('wpstorecart-admin','Import and Export - wpStoreCart ', 'Import/Export', 'manage_wpstorecart', 'wpstorecart-import', array(&$wpStoreCart, 'printAdminPageImport'));
            add_action("admin_print_scripts-$importpage", array(&$wpStoreCart, 'my_import_scripts') );

            $categoriesPage = add_submenu_page('wpstorecart-admin','Categories - wpStoreCart ', 'Categories', 'manage_wpstorecart', 'wpstorecart-categories', array(&$wpStoreCart, 'printAdminPageCategories'));
            $ordersPage = add_submenu_page('wpstorecart-admin','Orders &amp; Customers - wpStoreCart', 'Orders', 'manage_wpstorecart', 'wpstorecart-orders', array(&$wpStoreCart, 'printAdminPageOrders'));
            $page2 = add_submenu_page('wpstorecart-admin','Coupons &amp; Discounts - wpStoreCart ', 'Coupons', 'manage_wpstorecart', 'wpstorecart-coupon', array(&$wpStoreCart, 'printAdminPageCoupons'));
            $page2a = add_submenu_page('wpstorecart-admin','ShareYourCart.com - wpStoreCart ', 'ShareYourCart&#8482;', 'manage_wpstorecart', 'wpstorecart-shareyourcart', array(&$wpStoreCart, 'printAdminPageShareYourCart'));
            $affiliatespage = add_submenu_page('wpstorecart-admin','Affiliates - wpStoreCart PRO', 'Affiliates', 'manage_wpstorecart', 'wpstorecart-affiliates', array(&$wpStoreCart, 'printAdminPageAffiliates'));
            $statsPage = add_submenu_page('wpstorecart-admin','Statistics - wpStoreCart PRO', 'Statistics', 'manage_wpstorecart', 'wpstorecart-statistics', array(&$wpStoreCart, 'printAdminPageStatistics'));
            $diagnosticsPage = add_submenu_page(NULL,'Diagnostics - wpStoreCart', 'Diagnostics', 'manage_wpstorecart', 'wpstorecart-diagnostics', array(&$wpStoreCart, 'printAdminPageDiagnostics'));
            add_submenu_page('wpstorecart-admin','Help - wpStoreCart PRO', 'Help', 'manage_wpstorecart', 'wpstorecart-help', array(&$wpStoreCart, 'printAdminPageHelp'));
            add_action("admin_print_scripts-$settingsPage", array(&$wpStoreCart, 'my_tooltip_script') );
            add_action("admin_print_scripts-$categoriesPage", array(&$wpStoreCart, 'my_tooltip_script') );
            add_action("admin_print_scripts-$ordersPage", array(&$wpStoreCart, 'my_tooltip_script') );
            add_action("admin_print_scripts-$page", array(&$wpStoreCart, 'my_admin_scripts') );
            add_action("admin_print_scripts-$page2", array(&$wpStoreCart, 'admin_script_anytime'), 1);
            add_action("admin_print_scripts-$page2a", array(&$wpStoreCart, 'my_tooltip_script'));
            add_action("admin_print_scripts-$mainPage", array(&$wpStoreCart, 'my_mainpage_scripts') );
            add_action("admin_print_scripts-$statsPage", array(&$wpStoreCart, 'my_mainpage_scripts') );
            add_action("admin_print_scripts-$affiliatespage", array(&$wpStoreCart, 'my_tooltip_script') );
            add_action("admin_print_scripts-$editproductpage", array(&$wpStoreCart, 'my_admin_scripts') );
            add_action("admin_print_scripts-$diagnosticsPage", array(&$wpStoreCart, 'my_tooltip_script') );

            if(is_admin() && ($devOptions['menu_style']=='version3' || $devOptions['menu_style']=='both' || $_POST['menu_style']=='version3' || $_POST['menu_style']=='both')) {
                require_once(WP_PLUGIN_DIR.'/wpstorecart/php/screen-meta-links.php');

                echo add_screen_meta_link(
                        'wpsc-statistics-link',
                        'Statistics',
                        'admin.php?page=wpstorecart-statistics',
                        //An array of page/screen IDs specifying where to display the link.
                        array(
                                $mainPage,
                                $settingsPage,
                                $page,
                                $editproductpage,
                                $importpage,
                                $categoriesPage,
                                $page2,
                                $page2a,
                                $affiliatespage,
                                $statsPage,
                                $ordersPage,
                                $diagnosticsPage,
                        ),
                        //Additional attributes for the link tag.
                        array(
                                'onclick' => 'location=\'admin.php?page=wpstorecart-statistics\';return true;',
                        )
                );

                echo add_screen_meta_link(
                        'wpsc-affiliates-link',
                        'Affiliates',
                        'admin.php?page=wpstorecart-affiliates',
                        //An array of page/screen IDs specifying where to display the link.
                        array(
                                $mainPage,
                                $settingsPage,
                                $page,
                                $editproductpage,
                                $importpage,
                                $categoriesPage,
                                $page2,
                                $page2a,
                                $affiliatespage,
                                $statsPage,
                                $ordersPage,
                                $diagnosticsPage,
                        ),
                        //Additional attributes for the link tag.
                        array(
                                'onclick' => 'location=\'admin.php?page=wpstorecart-affiliates\';return true;',
                        )
                );

                echo add_screen_meta_link(
                        'wpsc-marketing-link',
                        'Marketing',
                        'admin.php?page=wpstorecart-coupon',
                        //An array of page/screen IDs specifying where to display the link.
                        array(
                                $mainPage,
                                $settingsPage,
                                $page,
                                $editproductpage,
                                $importpage,
                                $categoriesPage,
                                $page2,
                                $page2a,
                                $affiliatespage,
                                $statsPage,
                                $ordersPage,
                                $diagnosticsPage,
                        ),
                        //Additional attributes for the link tag.
                        array(
                                'onclick' => 'location=\'admin.php?page=wpstorecart-coupon\';return true;',
                        )
                );

                echo add_screen_meta_link(
                        'wpsc-orders-link',
                        'Orders',
                        'admin.php?page=wpstorecart-orders',
                        //An array of page/screen IDs specifying where to display the link.
                        array(
                                $mainPage,
                                $settingsPage,
                                $page,
                                $editproductpage,
                                $importpage,
                                $categoriesPage,
                                $page2,
                                $page2a,
                                $affiliatespage,
                                $statsPage,
                                $ordersPage,
                                $diagnosticsPage,
                        ),
                        //Additional attributes for the link tag.
                        array(
                                'onclick' => 'location=\'admin.php?page=wpstorecart-orders\';return true;',
                        )
                );



                echo add_screen_meta_link(
                        'wpsc-add-product-link',
                        'Products',
                        'admin.php?page=wpstorecart-add-products',
                        //An array of page/screen IDs specifying where to display the link.
                        array(
                                $mainPage,
                                $settingsPage,
                                $page,
                                $editproductpage,
                                $importpage,
                                $categoriesPage,
                                $page2,
                                $page2a,
                                $affiliatespage,
                                $statsPage,
                                $ordersPage,
                                $diagnosticsPage,
                        ),
                        //Additional attributes for the link tag.
                        array(
                                'onclick' => 'location=\'admin.php?page=wpstorecart-add-products\';return true;',
                        )
                );

                echo add_screen_meta_link(
                        'wpsc-settings-link',
                        'Settings',
                        'admin.php?page=wpstorecart-settings',
                        //An array of page/screen IDs specifying where to display the link.
                        array(
                                $mainPage,
                                $settingsPage,
                                $page,
                                $editproductpage,
                                $importpage,
                                $categoriesPage,
                                $page2,
                                $page2a,
                                $affiliatespage,
                                $statsPage,
                                $ordersPage,
                                $diagnosticsPage,
                        ),
                        //Additional attributes for the link tag.
                        array(
                                'onclick' => 'location=\'admin.php?page=wpstorecart-settings\';return true;',
                        )
                );

                $wpstorecartpro = false;
                if(file_exists(WP_PLUGIN_DIR.'/wpsc-affiliates-pro/saStoreCartPro/affiliates.pro.php')) {
                    if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php')) {
                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-statistics-pro/saStoreCartPro/statistics.pro.php')) {
                            $wpstorecartpro=true;
                        }
                    }
                }

                if($wpstorecartpro) {
                    $procss = 'background: #6290be;font-weight: bold;color: #FFF;text-shadow: none;';
                } else {
                    $procss = 'font-weight: bold;text-shadow: none;';
                }

                echo add_screen_meta_link(
                        'wpsc-dashboard-link',
                        'MyStore',
                        plugins_url('/php/wizard/wizard_begin.php' , __FILE__),
                        //An array of page/screen IDs specifying where to display the link.
                        array(
                                $mainPage,
                                $settingsPage,
                                $page,
                                $editproductpage,
                                $importpage,
                                $categoriesPage,
                                $page2,
                                $page2a,
                                $affiliatespage,
                                $statsPage,
                                $ordersPage,
                                $diagnosticsPage,
                        ),
                        //Additional attributes for the link tag.
                        array(
                                'rel' => '#overlay',
                                'style' => $procss,
                                'onclick' => 'return true;',
                        )
                );


                if(!$wpstorecartpro) {
                    echo add_screen_meta_link(
                            'wpsc-upgrade-to-pro',
                            'Upgrade to Pro',
                            'http://wpstorecart.com/store/business-support-wpstorecart-pro/',
                            //An array of page/screen IDs specifying where to display the link.
                            array(
                                    $mainPage,
                                    $settingsPage,
                                    $page,
                                    $editproductpage,
                                    $importpage,
                                    $categoriesPage,
                                    $page2,
                                    $page2a,
                                    $affiliatespage,
                                    $statsPage,
                                    $ordersPage,
                                    $diagnosticsPage,
                            ),
                            //Additional attributes for the link tag.
                            array(
                                    'target' => '_blank',
                                    'style' => 'background: #6290be;font-weight: bold;color: #FFF;text-shadow: none;',
                                    'onclick' => 'location=\'http://wpstorecart.com/store/business-support-wpstorecart-pro/\';return true;',
                            )
                    );
                }
                

            }

        }
    }   
}
 /**
 * ===============================================================================================================
 * END Initialize the admin panel
 */

function wpsc_list_activities($activities)
{
	// Create an array which we fill with out own activities.
	$activities = array(
		'wpsc-product-view' => array(
			'name' => _('User viewed a product pages'),
			'description' => _('Logs the product and user browser a page'),
			'sensitive' => true,
			'can_be_converted_to_post' => false,		// In this example, it can.
		),
		'wpsc-affiliate-view' => array(
			'name' => _('User viewed the affiliate manager'),
			'description' => _('Logs the user that viewed the affiliate manager'),
			'sensitive' => true,
			'can_be_converted_to_post' => false,		// In this example, it can.
		),
		'wpsc-orders-view' => array(
			'name' => _('User viewed their orders page'),
			'description' => _('Logs the user who browsed their orders page'),
			'sensitive' => true,
			'can_be_converted_to_post' => false,		// In this example, it can.
		),
		'wpsc-checkout' => array(
			'name' => _('User began purchasing a product'),
			'description' => _('Logs the user and details of the order'),
			'sensitive' => true,
			'can_be_converted_to_post' => false,		// In this example, it can.
		),
		'wpsc-download' => array(
			'name' => _('User downloaded a product'),
			'description' => _('Logs the user and details of the download'),
			'sensitive' => true,
			'can_be_converted_to_post' => false,		// In this example, it can.
		),
	);

	// Insert our module name in all the array values.
	// You can do this once, here, or several times manually in the above array.
	// After inserting the plugin name, insert the activity into the main $activities array.
	foreach( $activities as $index => $activity )
	{
		$activity['plugin'] = 'wpStoreCart';
		$activities[ $index ] = $activity;
	}

	// Return the complete array.
	return $activities;
}
 
 /**
 * ===============================================================================================================
 * Call everything
 */

//Actions and Filters   
if (isset($wpStoreCart)) {
    //Actions



        /* Disable the Admin Bar for all but admins. */
        if(!current_user_can('administrator')) {
            //show_admin_bar(false);
        }

        require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-config.php');
        require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc-defaults.php');
	require_once(WP_CONTENT_DIR . '/plugins/wpstorecart/php/wpsc-1.1/wpsc/wpsc.php');


        if($wpsc_cart_type == 'session') {
            if(!isset($_SESSION)) {
                    @session_start();
            }
            if(@!is_object($cart)) {
                $cart =& $_SESSION['wpsc'];
                if(@!is_object($cart)) {
                    $cart = new wpsc();
                }
            }
        }

        if($wpsc_cart_type == 'cookie') {
            if(@!is_object($cart)) {
                if(isset($_COOKIE['wpsccart'])) { @$cart =& unserialize(base64_decode($_COOKIE['wpsccart'])); }
                if(@!is_object($cart) && !isset($_COOKIE['wpsccart'])) {
                    $cart = new wpsc();
                    $xdomain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;setcookie('wpsccart', base64_encode(serialize($cart)), time()+7222, '/', $xdomain, false);
                }
            }
            if(!isset($_SESSION)) { @session_start(); }
        }

        add_action('activated_plugin',array(&$wpStoreCart, 'save_error'));
	register_activation_hook(__FILE__, array(&$wpStoreCart, 'wpstorecart_install')); // Install DB schema
        add_action('plugins_loaded', array(&$wpStoreCart, 'wpstorecart_needs_to_start_sessions_before_anything_else'), 1); // Trys to insure that wpStoreCart is the first plugin that starts a session,  but may not be possible :(
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
        add_action('wp_print_scripts', array(&$wpStoreCart, 'enqueue_my_scripts'));
        add_action( 'wp_print_styles', array(&$wpStoreCart, 'enqueue_my_styles') );

        // Allows us to place the Setup Wizard in all admin pages
        add_action('admin_init', array(&$wpStoreCart, 'placeAdminHeaderEnqueue')); 
	add_action('admin_head', array(&$wpStoreCart, 'placeAdminHeaderCode'));
        add_filter('admin_notices', array(&$wpStoreCart, 'placeAdminFooter'));
        


        /**
         * Adds the custom registration fields
         */
        add_filter('user_contactmethods', array(&$wpStoreCart, 'add_custom_contactmethod'),10,1);
        add_action('register_form', array(&$wpStoreCart, 'show_custom_reg_fields'));
        add_action('user_register',  array(&$wpStoreCart, 'register_extra_fields'));
        add_action('register_post',array(&$wpStoreCart, 'check_fields'),10,3);

        //Filters
	add_filter('the_posts', array(&$wpStoreCart, 'add_script_swfobject')); 
        //add_filter('threewp_activity_monitor_list_activities','wpsc_list_activities'); // for ThreeWP Activity Monitor version 2

}
 /**
 * ===============================================================================================================
 */



?>