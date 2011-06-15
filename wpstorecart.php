<?php
/*
Plugin Name: wpStoreCart
Plugin URI: http://wpstorecart.com/
Description: <a href="http://wpstorecart.com/" target="blank">wpStoreCart</a> is a powerful, yet simple to use e-commerce Wordpress plugin that accepts PayPal & more out of the box. It includes multiple widgets, dashboard widgets, shortcodes, and works using Wordpress pages to keep everything nice and simple.
Version: 2.3.4
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
 * @version 2.3.4
 * @author wpStoreCart.com <admin@wpstorecart.com>
 * @copyright Copyright &copy; 2010, 2011 wpStoreCart.com.  All rights reserved.
 * @link http://wpstorecart.com/
 *
 */

// Added in 2.3.2 to try and help fix session problems, but I doubt they will do much good :(
try {
    @ini_set('session.use_only_cookies', 1);
    @ini_set('session.auto_start', 0);
    @ini_set('session.use_only_cookies', 0);
} catch (Exception $e) {

}

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
$wpstorecart_version = '2.3.4';
$wpstorecart_version_int = 203004; // Mm_p__ which is 1 digit for Major, 2 for minor, and 3 digits for patch updates, so version 2.0.14 would be 200014
$wpstorecart_db_version = $wpstorecart_version_int; // Legacy, used to check db version
$testing_mode = false; // Enables or disables testing mode.  Should be set to false unless using on a test site, with test data, with no actual customers
$wpsc_error_reporting = false; // Enables or disables the advanced error reporting utilities included with wpStoreCart.  Should be set to false unless using on a test site, with test data, with no actual customers
$wpsc_error_level = E_ALL; // The error level to use if wpsc_error_reporting is set to true.  Default is E_ALL
$APjavascriptQueue = NULL;
$wpsc_cart_type = 'session';

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
if(!function_exists(copyr)) {
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
if(!file_exists(WP_CONTENT_DIR.'/themes/wpStoreCartTheme/index.php')) {
    @copyr(WP_CONTENT_DIR.'/plugins/wpstorecart/wpStoreCartTheme/', WP_CONTENT_DIR.'/themes/wpStoreCartTheme/');
}

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
            global $wpdb, $wpstorecart_db_version, $wpstorecart_version_int;

            $devOptions = $this->getAdminOptions();

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

            if($devOptions['database_version']==NULL || $devOptions['database_version']=='2.0.2' || $devOptions['database_version']=='2.0.11') { // 2.1.0 - Database schema update for 2.0.13 and below
                        $table_name = $wpdb->prefix . "wpstorecart_products";
                        $sql = "ALTER TABLE `{$table_name}` ADD `donation` BOOLEAN NOT NULL DEFAULT '0';";
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
                        if(file_exists(WP_PLUGIN_DIR.'/wpsc-affiliates-pro/saStoreCartPro/affiliates.pro.php')) {
                            if(file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/saStoreCartPro/payments.pro.php')) {
                                if(file_exists(WP_PLUGIN_DIR.'/wpsc-statistics-pro/saStoreCartPro/statistics.pro.php')) {
                                    $logofile = 'logo_pro.png';
                                }
                            }
                        }

                        echo'
			<!-- overlayed element -->
                        <div class="apple_overlay" id="overlay">

                                <!-- the external content is loaded inside this tag -->
                                <div class="contentWrap"></div>

                        </div>
                        
			<div class="wrap">
			<div style="padding: 20px 10px 10px 10px;">
			<div style="float:left;"><a href="http://wpstorecart.com" target="_blank"><img src="'.plugins_url('/images/'.$logofile , __FILE__).'" alt="wpstorecart" /></a><br />';if(!file_exists(WP_PLUGIN_DIR.'/wpsc-affiliates-pro/saStoreCartPro/affiliates.pro.php')) { echo '<a href="http://wpstorecart.com/store/business-support-wpstorecart-pro/" target="_blank"><img src="'.plugins_url('/images/order_pro.png' , __FILE__).'" alt="wpstorecart" /></a>';}
                        echo'</div>';
                        if($logofile != 'logo_pro.png') {
                            echo '
			<div style="float:right;">
				
                                <a style="position:absolute;top:50px;margin-left:-222px;" href="http://wpstorecart.com/store/business-support-wpstorecart-pro/" target="_blank"><img src="'.plugins_url('/images/hire_us.png' , __FILE__).'" alt="wpstorecart" /></a>
			</div>
                        ';
                        }

                        echo '
			<br style="clear:both;" />
			<ul id="jsddm">
				<li class="tab" style="border-left:1px solid #999;"><a href="'.plugins_url('/php/wizard/wizard_begin.php' , __FILE__).'" rel="#overlay" style="text-decoration:none" class="spmenu"><img src="'.plugins_url('/images/controller.png' , __FILE__).'" /> &nbsp;</a>
                                    <ul>
                                        <li><a href="admin.php?page=wpstorecart-admin" class="spmenu">Overview</a></li>
                                        <li><a href="'.plugins_url('/php/wizard/wizard_setup_01.php' , __FILE__).'" rel="#overlay" class="spmenu">Setup Wizard</a></li>
                                        <li><a href="'.plugins_url('/php/wizard/wizard_setup_04.php' , __FILE__).'" rel="#overlay" class="spmenu">Payments Wizard</a></li>
                                    </ul>
                                </li>
                                <li class="tab"><a href="admin.php?page=wpstorecart-settings" class="spmenu"><img src="'.plugins_url('/images/application_form_edit.png' , __FILE__).'" /> Settings</a>
                                    <ul>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab1" class="spmenu"><img src="'.plugins_url('/images/application_form_edit.png' , __FILE__).'" /> General</a></li>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab2" class="spmenu"><img src="'.plugins_url('/images/email.png' , __FILE__).'" /> E-Mail</a></li>';
                                        $theme_data = get_theme_data(get_stylesheet_uri());
                                        if(trim($theme_data['Title']) == 'wpStoreCart Default') {
                                            echo '<li><a href="admin.php?page=wpstorecarttheme-settings" class="spmenu"><img src="'.plugins_url('/images/table.png' , __FILE__).'" /> Theme Settings</a></li>';
                                        }
                                        echo '<li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab3" class="spmenu"><img src="'.plugins_url('/images/css.png' , __FILE__).'" /> Display</a></li>';

                                        if($devOptions['storetype']!='Digital Goods Only') { // Hide shipping if digital only store
                                            echo '<li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab6" class="spmenu"><img src="'.plugins_url('/images/package_go.png' , __FILE__).'" /> Shipping</a></li>';
                                        }
                                        echo '<li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab4" class="spmenu"><img src="'.plugins_url('/images/creditcards.png' , __FILE__).'" /> Payment</a></li>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab5" class="spmenu"><img src="'.plugins_url('/images/text_padding_top.png' , __FILE__).'" /> Language</a></li>
                                        <li><a href="admin.php?page=wpstorecart-settings&theCurrentTab=tab7" class="spmenu"><img src="'.plugins_url('/images/user_suit.png' , __FILE__).'" /> Customers</a></li>
                                    </ul>
                                </li>
				<li class="tab"><a href="admin.php?page=wpstorecart-add-products" class="spmenu"><img src="'.plugins_url('/images/basket_add.png' , __FILE__).'" />Products</a>
                                    <ul>
                                        <li><a href="admin.php?page=wpstorecart-add-products" class="spmenu"><img src="'.plugins_url('/images/basket_add.png' , __FILE__).'" /> Add Product</a></li>
                                        <li><a href="admin.php?page=wpstorecart-edit-products" class="spmenu"><img src="'.plugins_url('/images/basket_edit.png' , __FILE__).'" /> Edit Products</a></li>
                                        ';
                                        if($testing_mode==true || $wpstorecart_version_int >= 202000) { // Bleeding edge until 2.2, at which time this code block will automatically be enabled
                                            //echo '<li><a href="admin.php?page=wpstorecart-import" class="spmenu"><img src="'.plugins_url('/images/server_go.png' , __FILE__).'" /> Import/Export</a></li>';
                                        }

                                    echo '
                                    </ul>
                                </li>
				
				<li class="tab"><a href="admin.php?page=wpstorecart-categories" class="spmenu"><img src="'.plugins_url('/images/table.png' , __FILE__).'" /> Categories</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-orders" class="spmenu"><img src="'.plugins_url('/images/cart_go.png' , __FILE__).'" /> Orders</a>
                                    <ul>
                                        <li class="tab"><a href="admin.php?page=wpstorecart-orders" class="spmenu"><img src="'.plugins_url('/images/cart_go.png' , __FILE__).'" /> All Orders</a></li>
                                        <li class="tab"><a href="admin.php?page=wpstorecart-orders&show=completed" class="spmenu"><img src="'.plugins_url('/images/bullet_green.png' , __FILE__).'" /> Completed Orders</a></li>
                                        <li class="tab"><a href="admin.php?page=wpstorecart-orders&show=pending" class="spmenu"><img src="'.plugins_url('/images/bullet_orange.png' , __FILE__).'" /> Pending Orders</a></li>
                                        <li class="tab"><a href="admin.php?page=wpstorecart-orders&show=refunded" class="spmenu"><img src="'.plugins_url('/images/bullet_red.png' , __FILE__).'" /> Refunded Orders</a></li>
                                    </ul>
                                </li>
				<li class="tab"><a href="admin.php?page=wpstorecart-coupon" class="spmenu"><img src="'.plugins_url('/images/money.png' , __FILE__).'" /> Marketing</a>
                                    <ul>
                                        <li class="tab"><a href="admin.php?page=wpstorecart-coupon" class="spmenu"><img src="'.plugins_url('/images/money.png' , __FILE__).'" /> Coupons</a></li>
                                        <li class="tab"><a href="admin.php?page=wpstorecart-shareyourcart" class="spmenu"><img src="'.plugins_url('/images/shareyourcart.png' , __FILE__).'" /> ShareYourCart&#8482;</a></li>
                                    </ul>
                                </li>
				<li class="tab"><a href="admin.php?page=wpstorecart-affiliates" class="spmenu"><img src="'.plugins_url('/images/user_suit.png' , __FILE__).'" /> Affiliates</a></li>
				<li class="tab"><a href="admin.php?page=wpstorecart-statistics" class="spmenu"><img src="'.plugins_url('/images/chart_bar.png' , __FILE__).'" /> Statistics</a></li>
				<li class="tab" style="border-right:1px solid #999;"><a href="http://wpstorecart.com/help-support/" target="_blank" class="spmenu"><img src="'.plugins_url('/images/help.png' , __FILE__).'" /> Help</a>
                                    <ul>
                                        <li><a href="http://wpstorecart.com/forum/" class="spmenu"  target="_blank">Support Forum</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/initial-settings/" class="spmenu"  target="_blank"><img src="'.plugins_url('/images/application_form_edit.png' , __FILE__).'" /> Initial Settings</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/adding-editing-products/" class="spmenu"  target="_blank"><img src="'.plugins_url('/images/basket_add.png' , __FILE__).'" /> Products</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/widgets/" class="spmenu"  target="_blank"><img src="'.plugins_url('/images/text_padding_top.png' , __FILE__).'" /> Widgets</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/coupons/" class="spmenu"  target="_blank"><img src="'.plugins_url('/images/money.png' , __FILE__).'" /> Coupons</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/shortcodes/" class="spmenu"  target="_blank"><img src="'.plugins_url('/images/text_padding_top.png' , __FILE__).'" /> Shortcodes</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/error-messages/" class="spmenu"  target="_blank"><img src="'.plugins_url('/images/cross.png' , __FILE__).'" /> Error Messages</a></li>
                                        <li><a href="http://wpstorecart.com/documentation/styles-designs/" class="spmenu"  target="_blank"><img src="'.plugins_url('/images/css.png' , __FILE__).'" /> Styles &amp; Design</a></li>
                                        <li><a href="http://wpstorecart.com/faq/" class="spmenu"  target="_blank"><img src="'.plugins_url('/images/help.png' , __FILE__).'" /> FAQ</a></li>
                                        <li><a href="http://wpstorecart.com/help-support/" class="spmenu"  target="_blank">More Help</a></li>

                                    </ul>
                                </li>
			</ul>
			<br style="clear:both;" />

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
                                    'checkoutimages' => 'false',
                                    'checkoutimagewidth' => '25',
                                    'checkoutimageheight' => '25',
                                    'checkoutlinktoproduct' => 'false'
                                    );

            if($this->wpStoreCartSettings!=NULL) {
                $devOptions = $this->wpStoreCartSettings;
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
            update_option($this->adminOptionsName, $apAdminOptions);
            
            return $apAdminOptions;
        }
		
		//Prints out the admin page ================================================================================
        function printAdminPage() {
			global $wpdb;

                //must check that the user has the required capability
                if (function_exists('current_user_can') && !current_user_can('manage_options'))
                {
                  wp_die( __('wpStoreCart: You do not have sufficient permissions to access this page.') );
                }


                        $devOptions = $this->getAdminOptions();
                        
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
				if (isset($_POST['taxes'])) {
 					$devOptions['taxes'] = $wpdb->escape($_POST['taxes']);
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

			echo'<div style="width:810px;max-width:810px;">
                            <h2> </h2>
			<form method="post" action="'. $_SERVER["REQUEST_URI"].'">
                            <input type="hidden" name="theCurrentTab" id="theCurrentTab" value="" />
                        <ul class="tabs">
                            <li><a href="#tab1" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab1\');"><img src="'.plugins_url('/images/buttons_general.jpg' , __FILE__).'" /></a></li>
                            <li><a href="#tab2" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab2\');"><img src="'.plugins_url('/images/buttons_email.jpg' , __FILE__).'" /></a></li>';

                        $theme_data = get_theme_data(get_stylesheet_uri());
                        if(trim($theme_data['Title']) == 'wpStoreCart Default') {
                            echo '<li><a href="admin.php?page=wpstorecarttheme-settings" onclick="window.location = \'admin.php?page=wpstorecarttheme-settings\';"><img src="'.plugins_url('/images/buttons_theme.jpg' , __FILE__).'" /></a></li>';
                        }

                        echo '<li><a href="#tab3" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab3\');"><img src="'.plugins_url('/images/buttons_product.jpg' , __FILE__).'" /></a></li>';

                        if($devOptions['storetype']!='Digital Goods Only') { // Hide shipping if digital only store
                            echo '<li><a href="#tab6" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab6\');"><img src="'.plugins_url('/images/buttons_shipping.jpg' , __FILE__).'" /></a></li>';
                        }
                        echo '<li><a href="#tab4" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab4\');"><img src="'.plugins_url('/images/buttons_payment.jpg' , __FILE__).'" /></a></li>
                            <li><a href="#tab5" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab5\');"><img src="'.plugins_url('/images/buttons_text.jpg' , __FILE__).'" /></a></li>
                            <li><a href="#tab7" onclick="jQuery(\'#theCurrentTab:input\').val(\'#tab7\');"><img src="'.plugins_url('/images/buttons_customers.jpg' , __FILE__).'" /></a></li>
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

			<tr><td><h3>Checkout Page: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2">The checkout page can be any page you specify.  This is the page customers will visit to pay for the products they have added to their cart.</div></h3></td>
			<td class="tableDescription"><p>The page that customers will use during checkout.  The page must have this shortcode in it: [wpstorecart display="checkout"]</p></td>
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

			<tr><td><h3>Orders Page: <i>(optional)</i> <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-211145" /><div class="tooltip-content" id="example-content-211145">The Orders &amp; Downloads page, which is optional.  However, if you wish to use the [downloadurl] code in emails, to tell your customers the URL they need to visit in order to download their orders, then this setting must be set.</div></h3></td>
			<td class="tableDescription"><p>The page where customers can view their orders, must have this shortcode in it: [wpstorecart display="orders"]</p></td>
			<td><select name="orderspage">
			 <option value="">
						';
			  attribute_escape(__('Select page'));
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

			<tr><td><h3>Google Analytics UA: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-4000" /><div class="tooltip-content" id="example-content-4000">Insert your Google Analytics UA code in order to track ecommerce conversions using Google Analytics.  Leave this blank if you\'re not using Google Analytics.  Note, this does not insert tracking code anywhere except when a customer purchases something.</div></h3></td>
			<td class="tableDescription"><p>Insert your Google Analytics UA-XXXXX-XX code here to keep track of sales using Google Analytics.  Leave blank if you don\'t use Google Analytics.</p></td>
			<td><input type="text" name="ga_trackingnum" value="'; _e(apply_filters('format_to_edit',$devOptions['ga_trackingnum']), 'wpStoreCart'); echo'" />
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
			<td><input type="text" name="itemsperpage" value="'; _e(apply_filters('format_to_edit',$devOptions['itemsperpage']), 'wpStoreCart'); echo'" />
			</td></tr>

			<tr><td><h3>jQuery UI Theme <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-43133" /><div class="tooltip-content" id="example-content-43133">You can style your shopping cart, products, and other wpStoreCart related elements here using jQuery UI.</div></h3></td>
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
			<td>Width: <input type="text" name="wpStoreCartwidth" style="width: 58px;" value="'; _e(apply_filters('format_to_edit',$devOptions['wpStoreCartwidth']), 'wpStoreCart'); echo'" />  <br />Height: <input type="text" name="wpStoreCartheight" style="width: 58px;" value="'; _e(apply_filters('format_to_edit',$devOptions['wpStoreCartheight']), 'wpStoreCart'); echo'" />
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
                        Width: <input style="width:35px;" type="text" name="checkoutimagewidth" value="'; _e(apply_filters('format_to_edit',$devOptions['checkoutimagewidth']), 'wpStoreCart'); echo'" />px &nbsp; &nbsp; Height: <input style="width:35px;"  type="text" name="checkoutimageheight" value="'; _e(apply_filters('format_to_edit',$devOptions['checkoutimageheight']), 'wpStoreCart'); echo'" />px
			</td></tr>

			<tr><td><h3>Enable Coupons &amp; display form? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-706055" /><div class="tooltip-content" id="example-content-706055">Enables coupons during checkout and displays the coupon input.</div></h3></td>
			<td class="tableDescription"><p>Yes to enable or No to disable coupons.</p></td>
			<td><p><label for="enablecoupons"><input type="radio" id="enablecoupons_yes" name="enablecoupons" value="true" '; if ($devOptions['enablecoupons'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enablecoupons_no"><input type="radio" id="enablecoupons_no" name="enablecoupons" value="false" '; if ($devOptions['enablecoupons'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
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

                            <tr><td><h3>Enable FedEx Shipping? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-81236" /><div class="tooltip-content" id="example-content-81236">This allows you to ship via FedEx and allows the customer to calculate the shipping rates before purchase.</div></h3></td>
                            <td class="tableDescription"><p>If set to Yes, will allow customers to select USPS as a shipping option and will give shipping price quotes for FedEx.</p></td>
                            <td><p><label for="enablefedex"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enablefedex_yes" name="enablefedex" value="true" '; if ($devOptions['enablefedex'] == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="enablefedex_no"><input ';if($curl_is_disabled == true) {echo ' disabled="disabled"';}echo 'type="radio" id="enablefedex_no" name="enablefedex" value="false" '; if ($devOptions['enablefedex'] == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>
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
                        $theOptionszz[3] = 'states';$theOptionszzName[3] = 'Input US States';
                        $theOptionszz[4] = 'countries';$theOptionszzName[4] = 'Input Countries';
                        $theOptionszz[5] = 'email';$theOptionszzName[5] = 'Input Email Address';
                        $theOptionszz[6] = 'separator';$theOptionszzName[6] = '--- Separator ---';
                        $theOptionszz[7] = 'header';$theOptionszzName[7] = 'Header &lt;h2&gt;&lt;/h2&gt;';
                        $theOptionszz[8] = 'text';$theOptionszzName[8] = 'Text &lt;p&gt;&lt;/p&gt;';
                        //$theOptionszz[9] = 'dropdown';$theOptionszzName[9] = 'Drop down list';
                        //$theOptionszz[10] = 'checkbox';$theOptionszzName[10] = 'Input Checkbox';

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
                        <br style="clear:both;" /><br />
                        <div id="contentRight">
                        </div>
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

			if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
				die(__('Cheatin&#8217; uh?'));
			}
                        require_once(WP_PLUGIN_DIR.'/wpstorecart/php/shareyourcart/shareyourcart-sdk.php');

			$devOptions = $this->getAdminOptions();

                        $this->spHeader();

                        echo '<div style="max-width:760px;width:760px;">';
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
                            /* <![CDATA[ */


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
                                        $wpStoreCartproduct_weight = $wpdb->escape($_POST['wpStoreCartproduct_weight']);
                                        $wpStoreCartproduct_length = $wpdb->escape($_POST['wpStoreCartproduct_length']);
                                        $wpStoreCartproduct_width = $wpdb->escape($_POST['wpStoreCartproduct_width']);
                                        $wpStoreCartproduct_height = $wpdb->escape($_POST['wpStoreCartproduct_height']);
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
                                        `height` = '{$wpStoreCartproduct_height}'
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

                                                /*
                                                $rel_post = get_post($result['postid']) ;
                                                if(isset($rel_post->ID)) {
                                                    if(@$_GET['recreate']=='true') {
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
                                                                _e("ERROR 4: wpStoreCart couldn't recreate your product page :(", "wpStoreCart");
                                                                echo $wpdb->print_error();
                                                                echo '</strong></p></div>';
                                                                return false;
                                                        } else { // Successfuly draft, let's continue

                                                                $updateSQL = "
                                                                UPDATE `{$table_name}` SET
                                                                `postid` = '{$thePostID}'
                                                                WHERE `primkey` ={$keytoedit} LIMIT 1 ;
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

                                                            // Now that we've inserted both the PAGE and the product, let's update and publish our post with the correct content
                                                            $my_post = array();
                                                            $my_post['ID'] = $thePostID;
                                                            $my_post['post_content'] = '[wpstorecart display="product" primkey="'.$keytoedit.'"]';
                                                            $my_post['post_status'] = 'publish';
                                                            wp_update_post( $my_post );
                                                        }
                                                    } else {
                                                        echo '<div class="updated"><p><strong>';
                                                        echo "The product page associated with this product is missing.  To recreate the page, <a href=\"admin.php?page=wpstorecart-add-products&keytoedit=5&recreate=true\">click here</a> (make sure you save any changes you made to the product first or you will lose them!)";
                                                        echo '</strong></p></div>';
                                                    }
                                                }
                                                 * 
                                                 */

					}
				} else {
					echo '<div class="updated"><p><strong>';
					echo "There was a problem loading the product you wish to edit.  The query was: {$grabrecord} ";
					echo '</strong></p></div>';					
				}
			}
			
			if (isset($_POST['addNewwpStoreCart_product']) && $isanedit == false) { // New Products
			
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
                                        $wpStoreCartproduct_weight = $wpdb->escape($_POST['wpStoreCartproduct_weight']);
                                        $wpStoreCartproduct_length = $wpdb->escape($_POST['wpStoreCartproduct_length']);
                                        $wpStoreCartproduct_width = $wpdb->escape($_POST['wpStoreCartproduct_width']);
                                        $wpStoreCartproduct_height = $wpdb->escape($_POST['wpStoreCartproduct_height']);
	
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
					INSERT INTO {$table_name} (`primkey`, `name`, `introdescription`, `description`, `thumbnail`, `price`, `shipping`, `download`, `tags`, `category`, `inventory`, `dateadded`, `postid`, `timesviewed`, `timesaddedtocart`, `timespurchased`, `useinventory`, `donation`, `weight`, `length`, `width`, `height`) VALUES
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
                                        {$wpStoreCartproduct_height}
                                        );
					";					
					
					$results = $wpdb->query( $insert );
					$lastID = $wpdb->insert_id;
					$keytoedit = $lastID;

                                        if(isset($_POST['enableproduct_display_add_to_cart_variations']) && $_POST['enableproduct_display_add_to_cart_variations']=='yes') {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'disableaddtocart', '{$lastID}');");
                                        } else {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'disableaddtocart', '{$lastID}');");
                                        }

                                        // Shipping options are saved here
                                        if(isset($_POST['wpsc_product_flatrateshipping']) && $_POST['wpsc_product_flatrateshipping']=='yes') {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'wpsc_product_flatrateshipping', '{$lastID}');");
                                        } else {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'wpsc_product_flatrateshipping', '{$lastID}');");
                                        }
                                        if(isset($_POST['wpsc_product_usps']) && $_POST['wpsc_product_usps']=='yes') {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'wpsc_product_usps', '{$lastID}');");
                                        } else {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'wpsc_product_usps', '{$lastID}');");
                                        }
                                        if(isset($_POST['wpsc_product_ups']) && $_POST['wpsc_product_ups']=='yes') {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'wpsc_product_ups', '{$lastID}');");
                                        } else {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'wpsc_product_ups', '{$lastID}');");
                                        }
                                        if(isset($_POST['wpsc_product_fedex']) && $_POST['wpsc_product_fedex']=='yes') {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'wpsc_product_fedex', '{$lastID}');");
                                        } else {
                                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'wpsc_product_fedex', '{$lastID}');");
                                        }

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

                        <ul class="tabs">
                            ';
                            if($isanedit==true) {
                                echo '
                                    <li style="display:inline;"><a href="#tab1"><img src="'.plugins_url('/images/buttons_product_info.jpg' , __FILE__).'" /></a></li>
                                    <li style="display:inline;"><a href="#tab4"><img src="'.plugins_url('/images/buttons_pictures.jpg' , __FILE__).'" /></a></li>
                                    <li style="display:inline;"><a href="#tab2"><img src="'.plugins_url('/images/buttons_variation.jpg' , __FILE__).'" /></a></li>
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

                          ';

			echo '
			<form method="post" action="'. $_SERVER["REQUEST_URI"].$codeForKeyToEdit.'" name="wpstorecartaddproductform" id="wpstorecartaddproductform">
                        <div id="tab1" class="tab_content">
                        ';

                        if($isanedit != true) {
                            echo '<h2>Add';
                        } else {
                            echo '<h2>Edit';
                        }
			echo ' a Product <a href="http://wpstorecart.com/documentation/adding-editing-products/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>';

			echo '<table class="widefat">
			<thead><tr><th>Product Attribute</th><th>Value</th><th>Description</th></tr></thead><tbody>
			';
			
			echo '
			<tr>
			<td><h3>Product<br />Name: <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1">The name of the product.  We do not recommend stuffing this with keywords, unless you don\'t mind those keywords being repeated everytime the product is mentioned.  Instead, simply keep this as the actual name of the product.</div></h3></td>
			<td><input type="text" name="wpStoreCartproduct_name" style="width: 80%;height:35px;font-size:22px;" value="'.$wpStoreCartproduct_name.'" /></td>
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
			<td><textarea class="wpStoreCartproduct_description" id="wpStoreCartproduct_description" name="wpStoreCartproduct_description" style="width: 80%;">'.$wpStoreCartproduct_description.'</textarea>  </td>
			<td><div style="width:300px;">You should be very detailed and include not only the backstory of the product, but also helpful information like instructions, controls, and objectives.</div></td>
			</tr>';			

			echo '
			<tr>
			<td><h3>Price'; if($devOptions['storetype']!='Digital Goods Only') { echo '<br />& Shipping';} echo ': <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2">The price you wish to charge for the product before tax and shipping charges.  You can also enter a flat rate shipping amount here, which will only be used if you do not offer other shipping options, such as FedEx or UPS.  If the shipping options are not here, that means the General Setting > Store Type is set to Digital Only.  Change that setting to restore the shipping options here.</div></h3></td>
			<td><br /><div style=";display:block;float:left;">Price: '.$devOptions['currency_symbol'].'<input type="text" name="wpStoreCartproduct_price" style="width: 58px;" value="'.$wpStoreCartproduct_price.'" />'.$devOptions['currency_symbol_right'].'  &nbsp; &nbsp; &nbsp; &nbsp; '; if($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') { echo '<br /><input type="checkbox" name="wpsc_product_flatrateshipping" value="yes" '; if($flatrateshipping_checked == 'yes') {echo 'checked="checked"';} echo ' /> Flat Rate Shipping: '.$devOptions['currency_symbol'];} echo '<input type="';if($devOptions['storetype']=='Digital Goods Only' || $devOptions['flatrateshipping']!='individual') {echo 'hidden';} else {echo 'text';} echo '" name="wpStoreCartproduct_shipping" style="width: 58px;" value="'.$wpStoreCartproduct_shipping.'" />';if($devOptions['storetype']!='Digital Goods Only' && $devOptions['flatrateshipping']=='individual') {echo $devOptions['currency_symbol_right'];} if($devOptions['storetype']!='Digital Goods Only' && $devOptions['enableusps']=='true') {echo '<br /><input type="checkbox" '; if($usps_checked == 'yes') {echo 'checked="checked"';} echo ' name="wpsc_product_usps" id="wpsc_product_usps" onclick="if(jQuery(\'#wpsc_product_usps\').is(\':checked\')){jQuery(\'#wpscdimensions\').effect(\'pulsate\', { times:2 }, 500);}" value="yes" /> Offer USPS shipping for this product? ';}if($devOptions['storetype']!='Digital Goods Only' && $devOptions['enableups']=='true') {echo '<br /><input type="checkbox" '; if($ups_checked == 'yes') {echo 'checked="checked"';} echo ' name="wpsc_product_ups" id="wpsc_product_ups" onclick="if(jQuery(\'#wpsc_product_ups\').is(\':checked\')){jQuery(\'#wpscdimensions\').effect(\'pulsate\', { times:2 }, 500);}" value="yes" /> Offer UPS shipping for this product? ';} if($devOptions['storetype']!='Digital Goods Only' && $devOptions['enablefedex']=='true') {echo '<br /><input type="checkbox" '; if($fedex_checked == 'yes') {echo 'checked="checked"';} echo ' name="wpsc_product_fedex" id="wpsc_product_fedex" onclick="if(jQuery(\'#wpsc_product_fedex\').is(\':checked\')){jQuery(\'#wpscdimensions\').effect(\'pulsate\', { times:2 }, 500);}" value="yes" /> Offer Fedex shipping for this product? ';} echo '</div><div style="margin-left:20px;display:block;float:left;min-width:120px;min-height:30px;width:120px;height:30px;"><strong>Accept Donations? <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-333777" /><div class="tooltip-content" id="example-content-333777">Note that this feature is only supported in the PayPal payment module currently.  If "Yes" is selected, this product is only given away when donations are made.  Note that the price you set above now becomes the minimum suggested donation amount.</div></strong><label for="wpStoreCartproduct_donation_yes"><input type="radio" id="wpStoreCartproduct_donation_yes" name="wpStoreCartproduct_donation" value="1" '; if ($wpStoreCartproduct_donation == 1) { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="wpStoreCartproduct_donation_no"><input type="radio" id="wpStoreCartproduct_donation_no" name="wpStoreCartproduct_donation" value="false" '; if ($wpStoreCartproduct_donation == 0) { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></div></td>
			<td><div style="width:300px;">The price and shipping cost of the product.</div></td>
			</tr>';

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
			<td><div id="wpscdimensions"><br />Weight: <input type="text" name="wpStoreCartproduct_weight" style="width: 58px;" value="'.$wpStoreCartproduct_weight.'" />  &nbsp; &nbsp; &nbsp; &nbsp; Length: <input type="text" name="wpStoreCartproduct_length" style="width: 58px;" value="'.$wpStoreCartproduct_length.'" />  &nbsp; &nbsp; &nbsp; &nbsp; Width: <input type="text" name="wpStoreCartproduct_width" style="width: 58px;" value="'.$wpStoreCartproduct_width.'" />  &nbsp; &nbsp; &nbsp; &nbsp; Height: <input type="text" name="wpStoreCartproduct_height" style="width: 58px;" value="'.$wpStoreCartproduct_height.'" /></div></td>
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

                        </div> <br style="clear:both;" />
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

			<h2>Edit products <a href="http://wpstorecart.com/documentation/adding-editing-products/" target="_blank"><img src="'.plugins_url('/images/bighelp.png' , __FILE__).'" /></a></h2>
			
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
			</div>';	
			
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
					<td>{$wpStoreCartpaymentprocessor}</td>
					<td><strong>{$devOptions['currency_symbol']}{$wpStoreCartprice}{$devOptions['currency_symbol_right']}</strong>"; if($wpStoreCartshipping!=0.00) {echo"<br /><i>({$devOptions['currency_symbol']}{$wpStoreCartshipping}{$devOptions['currency_symbol_right']})</i>";} echo"</td>
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
			<thead><tr><th> </th><th>Category <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-1" /><div class="tooltip-content" id="example-content-1"><h3>The name of the category.  Essentially, if you\'re selling a bunch of hats, make a category called hats.  It\'s that easy!</h3></div></th><th>Parent <img src="'.plugins_url('/images/help.png' , __FILE__).'" class="tooltip-target" id="example-target-2" /><div class="tooltip-content" id="example-content-2"><h3>If you select a parent category, then the category you are creating is a child category.  For example, if you sold red and blue hats, you would select hats as the parent.</h3></div></th><th>Thumb</th><th>Description</th><th>Page</th></tr></thead><tbody>
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
			</div>';	
			
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

                        echo '<div>
                                <div class="postbox-container" style="min-width:322px;max-width:322px;width:322px;">
                                    <div class="postbox">
                                        <div class="handlediv" title="_">
                                            <br />
                                        </div>
                                        <h3 class="hndle" style="padding:5px 5px 5px 5px;position:relative;top:-10px;"><span style="">Overview</span></h3>
                                        <div class="inside" style="padding:0px 5px 5px 5px;">    ';

                                        $this->wpstorecart_main_dashboard_widget_function();

                        echo '          </div>
                                    </div>
                                </div>

                                <div class="postbox-container" style="min-width:360px;max-width:360px;width:360px;">
                                    <div class="postbox">
                                        <div class="handlediv" title="_">
                                            <br />
                                        </div>
                                        <h3 class="hndle" style="padding:5px 5px 5px 5px;position:relative;top:-10px;"><span style="">News</span></h3>
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
                                <div class="postbox-container" style="width:85%">
                                    <div class="postbox">
                                        <div class="handlediv" title="_">
                                            <br />
                                        </div>
                                        <h3 class="hndle" style="padding:5px 5px 5px 5px;position:relative;top:-10px;"><span style="">Basic Stats</span></h3>
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
                                `useinventory` BOOL NOT NULL DEFAULT '1',
                                `donation` BOOL NOT NULL DEFAULT '0',
                                `weight` int(7) NOT NULL DEFAULT '0',
                                `length` int(7) NOT NULL DEFAULT '0',
                                `width` int(7) NOT NULL DEFAULT '0',
                                `height` int(7) NOT NULL DEFAULT '0'
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
                            ), $atts));

                            // Adds page pagination
                            if($quantity=='unset') {
                                $itemsperpage = $devOptions['itemsperpage'];
                                $quantity = $devOptions['itemsperpage'];
                            } else {
                                $itemsperpage = $quantity;
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
                                                                                <input type="hidden" name="my-item-shipping" value="'.$result['shipping'].'" />
                                                                                <label class="wpsc-qtylabel">Qty: <input type="text" name="my-item-qty" value="1" size="3" class="wpsc-qty" /></label>

                                                                        ';

                                                                        if($result['useinventory']==0 || ($result['useinventory']==1 && $result['inventory'] > 0) || $devOptions['storetype']=='Digital Goods Only' ) {
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
                                                                        if($devOptions['useimagebox']=='thickbox'){$output .='<a href="'.$results[0]['thumbnail'].'" class="thickbox" title="'. htmlentities($results[0]['name']. ' - ' . $results[0]['introdescription']).'">';} $output .= '<img class="wpsc-product-img" src="'.$results[0]['thumbnail'].'" alt="'.$results[0]['name'].'" style="max-width:'.$devOptions['wpStoreCartwidth'].'px;max-height:'.$devOptions['wpStoreCartheight'].'px;" />';if($devOptions['useimagebox']=='thickbox'){$output .='</a>';}$output .= '<br />';
                                                                    } else { // If width or height are zero, let's just display the image without css resizing
                                                                        if($devOptions['useimagebox']=='thickbox'){$output .='<a href="'.$results[0]['thumbnail'].'" class="thickbox" title="'. htmlentities($results[0]['name']. ' - ' . $results[0]['introdescription']).'">';} $output .= '<img class="wpsc-product-img" src="'.$results[0]['thumbnail'].'" alt="'.$results[0]['name'].'" />';if($devOptions['useimagebox']=='thickbox'){$output .='</a>';}$output .= '<br />';
                                                                    }
                                                            }
                                                            if($devOptions['showproductgallery']=='true'  && $devOptions['showproductgallerywhere']=='Directly after the Thumbnail') {
                                                                $output.= $this->wpstorecart_picture_gallery($primkey);
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
                                                                                        oldAmount = parseFloat('.$results[0]['price'].');
                                                                                        newAmount = Math.round((oldAmount + alteredPrice[0] + alteredPrice[1] + alteredPrice[2] + alteredPrice[3] + alteredPrice[4] + alteredPrice[5] + alteredPrice[6] + alteredPrice[7] + alteredPrice[8] + alteredPrice[9] + alteredPrice[10] + alteredPrice[11] + alteredPrice[12] + alteredPrice[13] + advancedVariationPrice) *100)/100;
                                                                                        newName = alteredName[0] + " " + alteredName[1] + " " + alteredName[2] + " " + alteredName[3] + " " + alteredName[4] + " " + alteredName[5] + " " + alteredName[6] + " " + alteredName[7] + " " + alteredName[8] + " " + alteredName[9] + " " + alteredName[10] + " " + alteredName[11] + " " + alteredName[12] + " " + alteredName[13] + advancedVariationName;
                                                                                        jQuery("#list-item-price").replaceWith("<li id=\'list-item-price\'>Price: '.$devOptions['currency_symbol'].'"+ newAmount.toFixed(2) + "'.$devOptions['currency_symbol_right'].'</li>");
                                                                                        jQuery("#my-item-price").val(newAmount.toFixed(2));
                                                                                        jQuery("#my-item-name").val("'.$results[0]['name'].' - " + newName);
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
                                                                                                oldAmount = parseFloat('.$results[0]['price'].');
                                                                                                newAmount = Math.round((oldAmount + alteredPrice[0] + alteredPrice[1] + alteredPrice[2] + alteredPrice[3] + alteredPrice[4] + alteredPrice[5] + alteredPrice[6] + alteredPrice[7] + alteredPrice[8] + alteredPrice[9] + alteredPrice[10] + alteredPrice[11] + alteredPrice[12] + alteredPrice[13] + advancedVariationPrice) *100)/100;
                                                                                                advancedVariationName = var_string;
                                                                                                newName = alteredName[0] + " " + alteredName[1] + " " + alteredName[2] + " " + alteredName[3] + " " + alteredName[4] + " " + alteredName[5] + " " + alteredName[6] + " " + alteredName[7] + " " + alteredName[8] + " " + alteredName[9] + " " + alteredName[10] + " " + alteredName[11] + " " + alteredName[12] + " " + alteredName[13] + advancedVariationName;
                                                                                                jQuery("#my-item-name").val("'.$results[0]['name'].' - " + newName);
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

                                                            $output .= '
                                                            <form method="post" action="">

                                                                    <input type="hidden" id="my-item-id" name="my-item-id" value="'.$results[0]['primkey'].'" />
                                                                    <input type="hidden" id="my-item-primkey" name="my-item-primkey" value="'.$results[0]['primkey'].'" />
                                                                    <input type="hidden" id="my-item-name" name="my-item-name" value="'.$results[0]['name'].'" />
                                                                    <input type="hidden" id="my-item-price" name="my-item-price" value="'.$results[0]['price'].'" />
                                                                    <input type="hidden" id="my-item-shipping" name="my-item-shipping" value="'.$result['shipping'].'" />
                                                                    <input type="hidden" id="my-item-img" name="my-item-img" value="'.$results[0]['thumbnail'].'" />
                                                                    <input type="hidden" id="my-item-url" name="my-item-url" value="'.get_permalink($results[0]['postid']).'" />
                                                                    <input type="hidden" id="my-item-tax" name="my-item-tax" value="0" />
                                                                    <input type="hidden" id="my-item-variation" name="my-item-variation" value="0" />

                                                                    <ul class="wpsc-product-info">
                                                                      <li id="list-item-name"><strong>'.$results[0]['name'].'</strong></li>
                                                                      <li id="list-item-price">Price: '.$devOptions['currency_symbol'].$results[0]['price'].$devOptions['currency_symbol_right'].'</li>
                                                                      <li id="list-item-qty"><label class="wpsc-individualqtylabel">Qty: <input type="text" name="my-item-qty" value="1" size="3"  class="wpsc-individualqty" /></label>					   </li>';

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
                                                                $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart" />';
                                                            } else {
                                                                $output .= $devOptions['out_of_stock'];
                                                            }

                                                            $output .= '
                                                            </form>
                                                            ';

                                                            if($devOptions['showproductgallery']=='true'  && $devOptions['showproductgallerywhere']=='Directly after the Add to Cart') {
                                                                $output.= $this->wpstorecart_picture_gallery($primkey);
                                                            }

                                                            if($devOptions['showproductdescription']=='true') {
                                                                    $output .= $results[0]['introdescription'] . '&nbsp; &nbsp;';
                                                                    if($devOptions['showproductgallery']=='true'  && $devOptions['showproductgallerywhere']=='Directly after the Intro Description') {
                                                                        $output.= $this->wpstorecart_picture_gallery($primkey);
                                                                    }
                                                                    $output .= $results[0]['description'];
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
                                                $sql = "SELECT * FROM `{$table_name}` ORDER BY `dateadded` DESC LIMIT {$startat}, {$quantity};";
                                                $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` ORDER BY `dateadded` DESC;");
                                            }
                                            if($devOptions['frontpageDisplays']=='List most popular products') {
                                                $sql = "SELECT * FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT {$startat}, {$quantity};";
                                                 $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC;");
                                            }
                                            if($devOptions['frontpageDisplays']=='List all categories (Ascending)') {
                                                $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` ASC LIMIT {$startat}, {$quantity};";
                                                $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` ASC;");
                                                $secondcss = 'wpsc-categories';
                                            } else {
                                                $secondcss = 'wpsc-products';
                                            }
                                            if($devOptions['frontpageDisplays']=='List all categories') {
                                                $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` DESC LIMIT {$startat}, {$quantity};";
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

                                                                        // Flat rate shipping implmented here:
                                                                        if($devOptions['flatrateshipping']=='all_single') {
                                                                            $result['shipping'] = $devOptions['flatrateamount'];
                                                                        } elseif($devOptions['flatrateshipping']=='off' || $devOptions['flatrateshipping']=='all_global') {
                                                                            $result['shipping'] = '0.00';
                                                                        }

                                                                        $output .= '
                                                                        <form method="post" action="">

                                                                                <input type="hidden" name="my-item-id" value="'.$result['primkey'].'" />
                                                                                <input type="hidden" name="my-item-primkey" value="'.$result['primkey'].'" />
                                                                                <input type="hidden" name="my-item-name" value="'.$result['name'].'" />
                                                                                <input type="hidden" name="my-item-price" value="'.$result['price'].'" />
                                                                                <input type="hidden" name="my-item-shipping" value="'.$result['shipping'].'" />
                                                                                <input type="hidden" id="my-item-img" name="my-item-img" value="'.$results[0]['thumbnail'].'" />
                                                                                <input type="hidden" id="my-item-url" name="my-item-url" value="'.get_permalink($results[0]['postid']).'" />
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
                                                                                    $output .= '<input type="submit" name="my-add-button" value="'.$devOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart" />';
                                                                                }
                                                                            }
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
                                                        $output .= '<br />';
                                                        $output .= 'Username: ' . $current_user->user_login . '<br />';
                                                        $output .= 'Email: ' . $current_user->user_email . '<br />';
                                                        $output .= 'First name: ' . $current_user->user_firstname . '<br />';
                                                        $output .= 'Last name: ' . $current_user->user_lastname . '<br />';
                                                        $output .= 'Display name: ' . $current_user->display_name . '<br />';
                                                        $output .= 'User ID: ' . $current_user->ID . '<br />';
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
                                                $output .= '<a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.$variationdownload.'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png"> '.$variationdownload.'</a> (Variation Download)<br />';
                                            }
                                    }
                            }

                            $multidownloads = explode('||', $moreresults[0]['download']);
                            if(@isset($multidownloads[0]) && @isset($multidownloads[1])) {
                                    $downloadcount = 0;
                                    foreach($multidownloads as $multidownload) {
                                            if($multidownload!='') {
                                                    $output .= '<a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.$multidownload.'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png"> '.$multidownload.'</a><br />';
                                            }
                                                    $downloadcount++;
                                    }
                            } else {
                                    $output .= '<a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.$moreresults[0]['download'].'"><img src="'.WP_PLUGIN_URL.'/wpstorecart/images/disk.png"> '.$moreresults[0]['download'].'</a>';
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
            
            $fields = $this->grab_custom_reg_fields();
            foreach ($fields as $field) {
                $specific_items = explode("||", $field['value']);
                    // $contactmethods[$this->slug($specific_item[0])] = $specific_item[0];
                    if($specific_items[2]=='input (text)') {
                        echo '<label><span>'. $specific_items[0] .' ';if($specific_items[1]=='required'){echo '<div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div>';} echo'</span><input class="input" id="'.$this->slug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.$this->slug($specific_items[0])].'" name="'.$this->slug($specific_items[0]).'" /></label>';
                    }
                    if($specific_items[2]=='input (numeric)') {
                        echo '<label><span>'. $specific_items[0] .' ';if($specific_items[1]=='required'){echo '<div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div>';} echo'</span><input class="input" id="'.$this->slug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.$this->slug($specific_items[0])].'" name="'.$this->slug($specific_items[0]).'" /></label>';
                    }
                    if($specific_items[2]=='textarea') {
                        echo '<label><span>'. $specific_items[0] .' ';if($specific_items[1]=='required'){echo '<div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div>';} echo'</span><textarea class="input" id="'.$this->slug($specific_items[0]).'" name="'.$this->slug($specific_items[0]).'">'.$_SESSION['wpsc_'.$this->slug($specific_items[0])].'</textarea></label>';
                    }
                    if($specific_items[2]=='states') {
                        echo '<label><span>'. $specific_items[0] .' ';if($specific_items[1]=='required'){echo '<div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div>';} echo'</span><select class="input" name="'.$this->slug($specific_items[0]).'">
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
                        </select></label>';
                    }
                    if($specific_items[2]=='countries') {
                        echo '<label><span>'. $specific_items[0] .' ';if($specific_items[1]=='required'){echo '<div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div>';} echo'</span><select class="input" name="'.$this->slug($specific_items[0]).'">
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
                        </select></label>';
                    }
                    if($specific_items[2]=='email') {
                        echo '<label><span>'. $specific_items[0] .' ';if($specific_items[1]=='required'){echo '<div class="wpsc-required-symbol">'.$devOptions['required_symbol'].'</div>';} echo'</span><input class="input" id="'.$this->slug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.$this->slug($specific_items[0])].'" name="'.$this->slug($specific_items[0]).'" /></label>';
                    }
                    if($specific_items[2]=='separator') {
                        echo $specific_items[0] .'<br />';
                    }
                    if($specific_items[2]=='header') {
                        echo '<h2>'.$specific_items[0] .'</h2>';
                    }
                    if($specific_items[2]=='text') {
                        echo '<p>'.$specific_items[0] .'</p>';
                    }
                
            }


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
                        $widgetShowSubtotal = empty($instance['widgetShowSubtotal']) ? 'true' : $instance['widgetShowSubtotal'];
                        $widgetShowTotal = empty($instance['widgetShowTotal']) ? 'true' : $instance['widgetShowTotal'];
                        $widgetShowShipping = empty($instance['widgetShowShipping']) ? 'true' : $instance['widgetShowShipping'];
                        $wpscWidgetSettings['iswidget']=true;
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
                        @$widgetShowTotal = htmlspecialchars($instance['widgetShowTotal']);
			echo '<p><label for="'. $this->get_field_id('title') .'">'; _e('Title:'); echo ' <input class="widefat" id="'. $this->get_field_id('title') .'" name="'. $this->get_field_name('title') .'" type="text" value="'. $title .'" /></label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowproductImages') . '">' . __('Use as the final checkout:') . '<br /><label for="' . $this->get_field_name('widgetShowproductImages') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_yes" name="' . $this->get_field_name('widgetShowproductImages') . '" value="true" '; if ($widgetShowproductImages == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowproductImages') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowproductImages') . '_no" name="' . $this->get_field_name('widgetShowproductImages') . '" value="false" '; if ($widgetShowproductImages == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
                        echo '<p><label for="' . $this->get_field_name('widgetShowShipping') . '">' . __('Show shipping costs:') . '<br /><label for="' . $this->get_field_name('widgetShowShipping') . '_yes"><input type="radio" id="' . $this->get_field_id('widgetShowShipping') . '_yes" name="' . $this->get_field_name('widgetShowShipping') . '" value="true" '; if ($widgetShowShipping == "true") { _e('checked="checked"', "wpStoreCart"); }; echo '/> Yes</label>&nbsp;&nbsp;&nbsp;&nbsp;<label for="' . $this->get_field_name('widgetShowShipping') . '_no"><input type="radio" id="' . $this->get_field_id('widgetShowShipping') . '_no" name="' . $this->get_field_name('widgetShowShipping') . '" value="false" '; if ($widgetShowShipping == "false") { _e('checked="checked"', "wpStoreCart"); }; echo '/> No</label></p>';
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
            $mainPage = add_menu_page('wpStoreCart - Open Source WP Shopping Cart &amp; eCommerce Plugin', 'wpStoreCart', 'activate_plugins', 'wpstorecart-admin', array(&$wpStoreCart, 'printAdminPageOverview'), plugins_url('/images/controller.png' , __FILE__));
            $settingsPage = add_submenu_page('wpstorecart-admin','Settings - wpStoreCart ', 'Settings', 'activate_plugins', 'wpstorecart-settings', array(&$wpStoreCart, 'printAdminPage'));
            $page = add_submenu_page('wpstorecart-admin','Add product - wpStoreCart ', 'Add product', 'activate_plugins', 'wpstorecart-add-products', array(&$wpStoreCart, 'printAdminPageAddproducts'));
            $editproductpage = add_submenu_page('wpstorecart-admin','Edit products - wpStoreCart ', 'Edit products', 'activate_plugins', 'wpstorecart-edit-products', array(&$wpStoreCart, 'printAdminPageEditproducts'));

            $importpage = add_submenu_page('wpstorecart-admin','Import and Export - wpStoreCart ', 'Import/Export', 'activate_plugins', 'wpstorecart-import', array(&$wpStoreCart, 'printAdminPageImport'));
            add_action("admin_print_scripts-$importpage", array(&$wpStoreCart, 'my_import_scripts') );

            $categoriesPage = add_submenu_page('wpstorecart-admin','Categories - wpStoreCart ', 'Categories', 'activate_plugins', 'wpstorecart-categories', array(&$wpStoreCart, 'printAdminPageCategories'));
            $ordersPage = add_submenu_page('wpstorecart-admin','Orders &amp; Customers - wpStoreCart', 'Orders', 'activate_plugins', 'wpstorecart-orders', array(&$wpStoreCart, 'printAdminPageOrders'));
            $page2 = add_submenu_page('wpstorecart-admin','Coupons &amp; Discounts - wpStoreCart ', 'Coupons', 'activate_plugins', 'wpstorecart-coupon', array(&$wpStoreCart, 'printAdminPageCoupons'));
            $page2a = add_submenu_page('wpstorecart-admin','ShareYourCart.com - wpStoreCart ', 'ShareYourCart&#8482;', 'activate_plugins', 'wpstorecart-shareyourcart', array(&$wpStoreCart, 'printAdminPageShareYourCart'));
            $affiliatespage = add_submenu_page('wpstorecart-admin','Affiliates - wpStoreCart PRO', 'Affiliates', 'activate_plugins', 'wpstorecart-affiliates', array(&$wpStoreCart, 'printAdminPageAffiliates'));
            $statsPage = add_submenu_page('wpstorecart-admin','Statistics - wpStoreCart PRO', 'Statistics', 'activate_plugins', 'wpstorecart-statistics', array(&$wpStoreCart, 'printAdminPageStatistics'));
            add_submenu_page('wpstorecart-admin','Help - wpStoreCart PRO', 'Help', 'activate_plugins', 'wpstorecart-help', array(&$wpStoreCart, 'printAdminPageHelp'));
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


}
 /**
 * ===============================================================================================================
 */



?>