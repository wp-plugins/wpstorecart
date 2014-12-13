<?php

wpsc_compatibility(); // Hook for compatibility

if(!function_exists('wpscRecursivelyDeleteFolder')) {
    function wpscRecursivelyDeleteFolder($directory) { 
        foreach(glob("{$directory}/*") as $file)
        {
            if(is_dir($file)) { 
                @wpscRecursivelyDeleteFolder($file);
            } else {
                @unlink($file);
            }
        }
        @rmdir($directory);
    }
}

if(!function_exists('wpscCompatibilitySettings')) {
        /**
         * Sets up a compatible environment for wpStoreCart
         */
	function wpscCompatibilitySettings() {
            global $wpsc_wordpress_upload_dir,$wpstorecart_upload_dir;
	    error_reporting(0); // Turns error reporting off

            // Make sure pluggable has already been called
            if (file_exists(ABSPATH . 'wp-includes/pluggable.php')) {require_once(ABSPATH . 'wp-includes/pluggable.php');}
            
            // Deletes old out of date PRO plugins that are now integrated into the core
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-2co/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-2co/');
            }
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-affiliates-pro/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-affiliates-pro/');
            }            
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-flatrate-shipping/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-flatrate-shipping/');
            }    
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-liberty-reserve/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-liberty-reserve/');
            }            
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-membership-pro/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-membership-pro/');
            }                    
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-payments-pro/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-payments-pro/');
            }        
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-qbms/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-qbms/');
            }             
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-statistics-pro/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-statistics-pro/');
            }     
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-ups-shipping/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-ups-shipping/');
            }            
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-user-customize-products/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-user-customize-products/');
            }     
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-authorize-net/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-authorize-net/');
            }        
            if(is_dir(WP_PLUGIN_DIR .'/wpsc-free-shipping/') ) {
               @wpscRecursivelyDeleteFolder(WP_PLUGIN_DIR .'/wpsc-free-shipping/');
            }              
            
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
	    if(!is_dir($wpstorecart_upload_dir . '/themes/product/')) {
	            @mkdir($wpstorecart_upload_dir . '/themes/product/', 0777, true);
	    } 
	    if(!is_dir($wpstorecart_upload_dir . '/themes/main/')) {
	            @mkdir($wpstorecart_upload_dir . '/themes/main/', 0777, true);
	    }             
            // Create the customize.php if it does not already exist.
            if(!file_exists($wpstorecart_upload_dir.'/customize.php')) {
                $wpscFileHandle = @fopen($wpstorecart_upload_dir.'/customize.php', 'w');
                @fclose($wpscFileHandle);                
            }
            
            // Default themes need loading
            if(!file_exists($wpstorecart_upload_dir.'/themes/main/wpstorecart.custom.css')) {
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/themes/wpstorecart.custom.css', $wpstorecart_upload_dir.'/themes/main/wpstorecart.custom.css');
            }
            
            // Some default images should be copied
            if(!file_exists($wpstorecart_upload_dir.'/image01.jpg')) {
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image01.jpg', $wpstorecart_upload_dir.'/image01.jpg');
            }
            if(!file_exists($wpstorecart_upload_dir.'/image02.jpg')) {
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image02.jpg', $wpstorecart_upload_dir.'/image02.jpg');
            }
            if(!file_exists($wpstorecart_upload_dir.'/image03.jpg')) {
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image03.jpg', $wpstorecart_upload_dir.'/image03.jpg');
            }
            if(!file_exists($wpstorecart_upload_dir.'/image04.jpg')) {
                @copy(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/copy/image04.jpg', $wpstorecart_upload_dir.'/image04.jpg');
            }
            if(!file_exists($wpstorecart_upload_dir.'/image05.jpg')) {
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


if(!function_exists('wpscCalculateCategoryDepth')) {
    function wpscCalculateCategoryDepth() {
        global $wpdb;
        
        $results = $wpdb->get_results("SELECT `parent`, `primkey` FROM `{$wpdb->prefix}wpstorecart_categories`;", ARRAY_A);
        if(@isset($results[0]['parent'])) {
            foreach ($results as $result) {
                if($result['parent']==0) { // Root parent categories
                    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_categories` SET `lineage`='{$result['primkey']}' WHERE `primkey`='{$result['primkey']}';");
                } else {
                    $parent_results = $wpdb->get_results("SELECT `lineage`, `depth` FROM `{$wpdb->prefix}wpstorecart_categories` WHERE `primkey`='{$result['parent']}';", ARRAY_A);
                    if(@isset($parent_results[0]['lineage'])) {
                        $depth = $parent_results[0]['depth'] + 1;
                        $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_categories` SET `lineage`='{$parent_results[0]['lineage']}-{$result['primkey']}', `depth`={$depth} WHERE `primkey`='{$result['primkey']}';");
                    }
                }
            }
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