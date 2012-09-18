<?php

wpsc_compatibility(); // Hook for compatibility

if(!function_exists('wpscCompatibilitySettings')) {
        /**
         * Sets up a compatible environment for wpStoreCart
         */
	function wpscCompatibilitySettings() {
            global $wpsc_wordpress_upload_dir,$wpstorecart_upload_dir;
	    error_reporting(0); // Turns error reporting off

            // Make sure pluggable has already been called
            if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {require_once(ABSPATH . 'wp-includes/pluggable.php');}
            
	    // Create the proper directory structure if it is not already created
	    if(!is_dir($wpsc_wordpress_upload_dir['basedir'].'/')) {
	            @mkdir($wpsc_wordpress_upload_dir['basedir'].'/');
	    }
	    if(!is_dir($wpstorecart_upload_dir.'/')) {
	            @mkdir($wpstorecart_upload_dir.'/', 0777, true);
	    }
	    if(!is_dir($wpstorecart_upload_dir . '/images/')) {
	            @mkdir($wpstorecart_upload_dir . '/images/', 0777, true);
	    }
	    if(!is_dir($wpstorecart_upload_dir . '/downloads/')) {
	            @mkdir($wpstorecart_upload_dir . '/downloads/', 0777, true);
	    }            
	    if(!is_dir($wpstorecart_upload_dir . '/themes/')) {
	            @mkdir($wpstorecart_upload_dir . '/themes/', 0777, true);
	    }            
            // Create the customize.php if it does not already exist.
            if(!file_exists($wpstorecart_upload_dir.'/customize.php')) {
                $wpscFileHandle = @fopen($wpstorecart_upload_dir.'/customize.php', 'w');
                @fclose($wpscFileHandle);                
            }
            
            // Default themes need loading
            if(!file_exists($wpstorecart_upload_dir.'/themes/wpstorecart.custom.css')) {
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/themes/wpstorecart.custom.css', $wpstorecart_upload_dir.'/themes/wpstorecart.custom.css');
            }
            
            // Some default images should be copied
            if(!file_exists($wpstorecart_upload_dir.'/image01.jpg')) {
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image01.jpg', $wpstorecart_upload_dir.'/image01.jpg');
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image02.jpg', $wpstorecart_upload_dir.'/image02.jpg');
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image03.jpg', $wpstorecart_upload_dir.'/image03.jpg');
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image04.jpg', $wpstorecart_upload_dir.'/image04.jpg');
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image05.jpg', $wpstorecart_upload_dir.'/image05.jpg');
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
	}
}

if(!function_exists('wpscSlug')) {
        /**
         *
         * Returns a slug of the input string, suitable URLs, HTML and other space/character sensitive operations
         * 
         * @param string $str
         * @return string 
         */
        function wpscSlug($str) {
                $str = strtolower(trim($str));
                $str = preg_replace('/[^a-z0-9-]/', '_', $str);
                $str = preg_replace('/-+/', "_", $str);
                return $str;
        }
}


if(!function_exists('wpscGdCheck')) {
        /**
         * Tries to determine if GD is installed
         * 
         * @return boolean 
         */
        function wpscGdCheck() {
            if (@function_exists('imagecreatetruecolor')) {
                return true;
            }
            elseif (@function_exists('imagecreate')) {
                return true;
            }
            else {
                return false;
            }
        }      
}



// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
/**
 * Hook implementation:
 */

//add_action('wpsc_compatibility','wpscCompatibilitySettings',1); // Applies our compatiblity function to the compatibility action hook

wpscCompatibilitySettings();
?>