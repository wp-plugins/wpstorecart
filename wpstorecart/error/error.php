<?php

if (!function_exists('wpsc_ini_get_bool')) {
    /**
     *
     * A better ini_get() implementation 
     * 
     * @param string $a
     * @return boolean
     */
    function wpsc_ini_get_bool($a) {
        $b = @ini_get($a);

        switch (strtolower($b))
        {
        case 'on':
        case 'yes':
        case 'true':
            return 'assert.active' !== $a;

        case 'stdout':
        case 'stderr':
            return 'display_errors' === $a;

        default:
            return (bool) (int) $b;
        }
    }
}


if (!function_exists('wpscError')) {       
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
            $output .= '<div style="float:left;margin-top:10px;"><a href="admin.php?page=wpstorecart-wizard" rel="#overlay" style="text-decoration:none;"><img src="'.plugins_url().'/wpstorecart/images/wizard/button_setup_wizard2.png" /></a></div><div style="float:left;width:77%;margin-left:10px;"><strong>'.__('wpStoreCart is almost ready! Configuration required.', 'wpstorecart').'</strong>'.__('The easiest and best way to configure wpStoreCart is to <a href="admin.php?page=wpstorecart-wizard">click here</a> to run the <a href="admin.php?page=wpstorecart-wizard">Setup Wizard</a>.  You can also automatically create a "main page" and a "checkout page" for your store by <a href="admin.php?page=wpstorecart-wizard&wpsc-wizard=auto_create_pages">clicking here</a>, or you can create your own pages first &amp; then visit <a href="admin.php?page=wpstorecart-settings">the settings page</a> to specify which pre-existing pages to use.  See <a href="http://wpstorecart.com/documentation/error-messages/" target="_blank">this help entry</a> for more details.','wpstorecart').'</div><br style="clear:both;" />';
        }
        if($theError=='register_globals') {
            $output .= '<strong>'.__('wpStoreCart has detected that register_globals is set to ON.','wpstorecart').'</strong> '.__('This is a major security risk that can make it much easier for a hacker to gain full access to your website and it\'s data.  Please disable register_globals by following <a href="https://wpstorecart.com/documentation/error-messages/disable-register-globals/" target="_blank">the directions here</a> before using wpStoreCart. Your shopping cart, checkout, and add to cart functionality will not work while register_globals is set to On. See <a href="http://wpstorecart.com/documentation/error-messages/" target="_blank">this help entry</a> for more details.', 'wpstorecart');
        }
        if($theError=='nouploadsdir') {
            $output .= '<strong>'.__('wpStoreCart has detected that a required folder is missing and we could not automatically create it.','wpstorecart').'</strong> '.__('Please manually create this folder and give it 0777 permissions: ','wpstorecart').$variables;
        }
        if($theError=='testingmode') {
            $output .= '<strong>'.__('wpStoreCart "<a href="http://wpstorecart.com/documentation/advanced-technical-topics/testing-mode/" target="_blank">Testing Mode</a>" enabled. DO NOT USE TESTING MODE ON A SERVER THAT IS CONNECTED TO THE INTERNET. DO NOT USE IT ON A LIVE WEBSITE. DO NOT USE IT WITH ACTUAL CUSTOMERS OR EVEN ACTUAL CUSTOMER DATA. ONLY USE TESTING MODE ON A TEST SERVER, WITH TEST DATA.</strong>  Visit <a href="http://wpstorecart.com/documentation/advanced-technical-topics/testing-mode/" target="_blank">this topic</a> for information on how to disable Testing Mode and this message.  ', 'wpstorecart');
        }
        if($theError=='no_curl' ){
            $output .= '<strong>'.__('wpStoreCart has detected that CURL is not enabled.','wpstorecart').'</strong> '.__('CURL is required if you wish to allow customers to calculate shipping costs with USPS, FedEx, and UPS.  Please have a system administrator install and/or configure CURL so that you can use those features.  Until that happens, you must use either flat rate shipping or have PayPal or your payment processor calculate shipping for you.  <a href="?page=wpstorecart-admin&wpscaction=removecurl">Click here to remove this message</a>', 'wpstorecart');
        }
        if($theError=='uspsnotconfigured' ){
            $output .= '<strong>'.__('wpStoreCart has noticed a serious problem!', 'wpstorecart').'</strong>'.  __('You\'ve selected to offer shipping through the United States Postal Service (USPS) but did not enter a USPS API key.  If you already have a USPS API key, please visit the <a href="?page=wpstorecart-settings&theCurrentTab=tab6">wpStoreCart > Settings > Shipping admin page</a>, enter the API name in the form, and click the "Update Settings" button.  If you do not have a USPS API key, visit this URL now: <a href="https://secure.shippingapis.com/registration/" target="_blank">https://secure.shippingapis.com/registration/</a>, complete the registration process by filling out the form and click the "Submit" button, then visit the <a href="?page=wpstorecart-settings&theCurrentTab=tab6">wpStoreCart > Settings > Shipping admin page</a> enter the API name in the form, and click the "Update Settings" button. If you only want to use flat rate shipping for all products (regardless of shipping provider) then simply disable USPS as a shipping service to dismiss this message, and use the flat rate shipping option instead.', 'wpstorecart');
        }
        $output .= "</p></div>";
        return $output;
    }
}

if (!function_exists('wpscErrorNoCURL')) {
    /**
    * No CURL error message
    */
    function wpscErrorNoCURL() {
        echo wpscError('no_curl');
    }
}


if (!function_exists('wpscErrorRegisterGlobals')) {
    /**
        * Register_globals error message
        */
    function wpscErrorRegisterGlobals() {
        echo wpscError('register_globals');
    }
}

if (!function_exists('wpscErrorNoPage')) {
    /**
        * No main page error message
        */
    function wpscErrorNoPage() {
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        if(!isset($wpStoreCartOptions['mainpage']) || !is_numeric($wpStoreCartOptions['mainpage'])) {
            echo wpscError('nopage');
        }
    }
}

if (!function_exists('wpscErrorTestingMode')) {
    /**
        * Testing mode enabled error message
        */
    function wpscErrorTestingMode() {
        echo wpscError('testingmode');
    }
}

if (!function_exists('wpscErrorNoUploadDir')) {
    /**
    * No Upload Directory error message
    */
    function wpscErrorNoUploadDir() {
        echo wpscError('nouploadsdir',WP_CONTENT_DIR . '/uploads/');
    }
}

if (!function_exists('wpscErrorNoUploadWpDir')) {
    /**
        * No wpstorecart upload directory error message
        */
    function wpscErrorNoUploadWpDir() {
        echo wpscError('nouploadsdir',WP_CONTENT_DIR . '/uploads/wpstorecart/');
    }
}

if (!function_exists('wpscErrorUSPS')) {
    /**
        * USPS incorrectly configured error message
        */
    function wpscErrorUSPS() {
        echo wpscError('uspsnotconfigured');
    }
}


if (!function_exists('wpscErrorCheckOnInit')) {
    /**
        * 
        * wpStoreCart's custom initialization
        *
        * @global boolean $testing_mode
        * @global object $current_user
        * @global object $wpdb 
        */
    function wpscErrorCheckOnInit() {

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        if($wpStoreCartOptions['checkcurl']=='true') {
            if (@!extension_loaded('curl')) {
                add_action('admin_notices', 'wpscErrorNoCURL');
            } else {
                if (@!function_exists('curl_init')) {
                    add_action('admin_notices','wpscErrorNoCURL');
                }
            }
        }

        if($wpStoreCartOptions['enableusps']=='true' && $wpStoreCartOptions['uspsapiname']=='') {
            add_action('admin_notices', 'wpscErrorUSPS');
        }

        if(!is_dir(WP_CONTENT_DIR . '/uploads/')) {
            add_action('admin_notices', 'wpscErrorNoUploadDir');
        }
        if(!is_dir(WP_CONTENT_DIR . '/uploads/wpstorecart/')) {
            add_action('admin_notices', 'wpscErrorNoUploadWpDir');
        }

        if (wpsc_ini_get_bool('register_globals')==1) {
            add_action('admin_notices', 'wpscErrorRegisterGlobals');
        }
        if(!isset($wpStoreCartOptions['mainpage']) || !is_numeric($wpStoreCartOptions['mainpage'])) {
            add_action('admin_notices', 'wpscErrorNoPage');
        }

    }
}

add_action('init', 'wpscErrorCheckOnInit'); //Error checking added on init


?>