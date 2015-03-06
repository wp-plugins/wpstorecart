<?php

wpsc_installer_check(); // Hook for the start of the installer
//wpsc_installer_upgrade(); // Hook for upgrades



/**
 *
 * Checks to see if a column exists in a mysql table, if not, it creates it
 *
 * @global object $wpdb
 * @param string $db
 * @param string $column
 * @param string $column_attr
 */
function wpscAddColumnIfNotExist($db, $column, $column_attr = "VARCHAR( 255 ) NULL" ){
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


/**
 * wpscUpdate() Method
 *
 * This method handles all the special actions that need to be taken when upgrading from an older version of wpStoreCart (no matter how old) to the latest version.
 *
 * @global object $wpdb
 * @global string $wpstorecart_db_version
 */
function wpscUpdate() {
    global $wpdb, $wpstorecart_version_int;

    // 5.0.0
    //wpscCustomizeProductInstallWpms(); // Install User Customized Products
    
    // 3.0.7
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "lineage", "TEXT NOT NULL" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "depth", "INT NOT NULL" );
    @wpscCalculateCategoryDepth();
    
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "donation", "BOOLEAN NOT NULL DEFAULT '0'" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "weight", "INT( 7 ) NOT NULL DEFAULT  '0'" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "length", "INT( 7 ) NOT NULL DEFAULT  '0'" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "width", "INT( 7 ) NOT NULL DEFAULT  '0'" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "height", "INT( 7 ) NOT NULL DEFAULT  '0'" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "discountprice", "DECIMAL(22,2) NOT NULL" );
    
    // This change was added in wpStoreCart 3
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "producttype", "VARCHAR( 255 ) NOT NULL DEFAULT 'product'" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "status", "VARCHAR( 128 ) NOT NULL" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "options", "TEXT NOT NULL" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "productdesignercss", "TEXT NOT NULL" );    
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_products", "shippingservices", "TEXT NOT NULL" );  
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_log", "time", "TIMESTAMP NOT NULL" );  
    $wpdb->query("ALTER TABLE `{$wpdb->prefix}wpstorecart_products` CHANGE `price` `price` DECIMAL( 22, 2 ) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$wpdb->prefix}wpstorecart_products` CHANGE `discountprice` `discountprice` DECIMAL( 22, 2 ) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$wpdb->prefix}wpstorecart_orders` CHANGE `price` `price` DECIMAL( 22, 2 ) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$wpdb->prefix}wpstorecart_coupons` CHANGE `amount` `amount` DECIMAL( 22, 2 ) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$wpdb->prefix}wpstorecart_cart` CHANGE `total` `total` DECIMAL( 64, 2 ) NOT NULL;");
    $wpdb->query("ALTER TABLE `{$wpdb->prefix}wpstorecart_products` CHANGE `weight` `weight` DECIMAL( 22, 2 ) NOT NULL DEFAULT '0',
    CHANGE `length` `length` DECIMAL( 22, 2 ) NOT NULL DEFAULT '0',
    CHANGE `width` `width` DECIMAL( 22, 2 ) NOT NULL DEFAULT '0',
    CHANGE `height` `height` DECIMAL( 22, 2 ) NOT NULL DEFAULT '0';");
    
    if(@$wpdb->get_var("show tables like '{$wpdb->prefix}wpstorecart_av'") == "{$wpdb->prefix}wpstorecart_av") { 
        // This code block executes when a wpStoreCart 2.x installation is updated to wpStoreCart 4.x
        $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_av`;"); // Delete this table, it is no longer supported
        $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `producttype`='product' WHERE `producttype`='';"); // Lets make our productrs available
    }
    
    // Upgrade the database schema if they're running 2.0.2 or below:
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "thumbnail", "VARCHAR( 512 ) NOT NULL" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "description", "TEXT NOT NULL" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "postid", "INT NOT NULL" );
    
    // Added in 2.5.0 for extended group discounts coding
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "showtoall", "BOOLEAN NOT NULL DEFAULT '1'" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "showtowhichgroups", "TEXT NOT NULL" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "discountstoall", "BOOLEAN NOT NULL DEFAULT '1'" );
    wpscAddColumnIfNotExist($wpdb->prefix . "wpstorecart_categories", "discountstowhichgroups", "TEXT NOT NULL" );

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

    $table_name = $wpdb->prefix . "wpstorecart_packages";
    if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "
                CREATE TABLE IF NOT EXISTS `{$table_name}` (
                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                `productkey` INT NOT NULL ,
                `weight` DECIMAL( 11, 2 ) NOT NULL ,
                `length` DECIMAL( 11, 3 ) NOT NULL ,
                `width` DECIMAL( 11, 3 ) NOT NULL ,
                `depth` DECIMAL( 11, 3 ) NOT NULL ,
                `options` TEXT NOT NULL
                );
                ";
        $results = $wpdb->query( $sql );
    }
    
    
  /**
     * Let's make sure the the av table exists for those who are upgrading from a previous version
     */
   $table_name = $wpdb->prefix . "wpstorecart_quickvar";
   if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "
                CREATE TABLE IF NOT EXISTS `{$table_name}` (
                `primkey` int(11) NOT NULL AUTO_INCREMENT,
                `productkey` int(11) NOT NULL,
                `values` text NOT NULL,
                `price` decimal(22,2) NOT NULL,
                `type` varchar(32) NOT NULL,
                `title` text NOT NULL,
                `group` text NOT NULL,
                `useinventory` BOOLEAN NOT NULL DEFAULT '1', 
                PRIMARY KEY (`primkey`)
                );
                ";


        $results = $wpdb->query( $sql );
    }

  /**
     * Let's make sure the the cart table exists for those who are upgrading from a previous version
     */
   $table_name = $wpdb->prefix . "wpstorecart_cart";
   if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

        $sql = "
                CREATE TABLE IF NOT EXISTS `{$table_name}` (
                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
                `total` DECIMAL( 64, 2 ) NOT NULL,
                `itemcount` INT NOT NULL,
                `items` TEXT NOT NULL,
                `itemprices` TEXT NOT NULL,
                `itemqtys` TEXT NOT NULL,
                `itemname` TEXT NOT NULL,
                `itemshipping` TEXT NOT NULL,
                `itemtax` TEXT NOT NULL,
                `itemurl` TEXT NOT NULL,
                `itemimg` TEXT NOT NULL,
                `user_id` INT NOT NULL,
                `options` TEXT NOT NULL,
                `ipaddress` VARCHAR( 39 ) NOT NULL
                );
                ";


         $results = $wpdb->query( $sql );
   }

   /**
    * This is to make sure we add in the alerts table from 3.0
    */
    $table_name = $wpdb->prefix . "wpstorecart_alerts";
    if($wpdb->get_var("show tables like '$table_name'") != $table_name) {

            $sql = "
                    CREATE TABLE {$table_name} (
                      `primkey` int(11) NOT NULL AUTO_INCREMENT,
                      `title` varchar(512) NOT NULL,
                      `description` text NOT NULL,
                      `conditions` text NOT NULL,
                      `severity` varchar(64) NOT NULL,
                      `image` varchar(255) NOT NULL,
                      `url` varchar(255) NOT NULL,
                      `qty` varchar(32) NOT NULL,
                      `groupable` tinyint(1) NOT NULL,
                      `clearable` tinyint(1) NOT NULL,
                      `status` varchar(255) NOT NULL,
                      `userid` int(11) NOT NULL,
                      `adminpanel` tinyint(1) NOT NULL,
                      `textmessage` tinyint(1) NOT NULL,
                      `emailalert` tinyint(1) NOT NULL,
                      `desktop` tinyint(1) NOT NULL,
                      PRIMARY KEY (`primkey`)
                    );
                    ";


                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    dbDelta($sql);
                    
                    $wpdb->query("INSERT INTO `{$table_name}` (`primkey`, `title`, `description`, `conditions`, `severity`, `image`, `url`, `qty`, `groupable`, `clearable`, `status`, `userid`, `adminpanel`, `textmessage`, `emailalert`, `desktop`) VALUES
                    (null, '".esc_sql(__('New sale!', 'wpstorecart'))."', '".esc_sql(__('Triggered anytime there is a sale', 'wpstorecart'))."', 'newsales() = true;', 'Low', 'Badge.png', 'admin.php?page=wpstorecart-orders&wpsc_order_type=Completed', '', 0, 1, '".esc_sql(__('You got new sale(s)', 'wpstorecart'))."', 1, 1, 0, 0, 0),
                    (null, '".esc_sql(__('3 Days', 'wpstorecart'))."', '".esc_sql(__('No sales in 3 days', 'wpstorecart'))."', 'nosales() @ hours(72);', 'medium', 'SymbolRemove.png', 'admin.php?page=wpstorecart-orders', '', 0, 1, '".esc_sql(__('No sales in 3 days', 'wpstorecart'))."', 1, 1, 0, 0, 0),
                    (null, '".esc_sql(__('1 Month', 'wpstorecart'))."', '".esc_sql(__('No sales in 1 month', 'wpstorecart'))."', 'nosales() @ month(1);', 'High', 'SymbolDelete.png', 'admin.php?page=wpstorecart-orders', '', 0, 1, '".esc_sql(__('No sales in 1 month', 'wpstorecart'))."', 1, 1, 0, 0, 0),
                    (null, '".esc_sql(__('No AddToCarts!', 'wpstorecart'))."', '".esc_sql(__('No Add To Carts in the last day', 'wpstorecart'))."', 'noaddtocart() @ days(1);', '', 'Help.png', '', '', 0, 1, '".esc_sql(__('No AddToCarts in last day', 'wpstorecart'))."', 1, 1, 0, 0, 0);
                    ");                    
                    
    }
    
    
    $table_name = $wpdb->prefix . "wpstorecart_field_def";
    if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {
        $sql = "
                CREATE TABLE IF NOT EXISTS `{$table_name}` (
                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                `productkey` INT NOT NULL, `type` VARCHAR(32) NOT NULL, 
                `information` VARCHAR(255) NOT NULL, 
                `required` VARCHAR(32) NOT NULL, 
                `defaultvalue` TEXT NOT NULL, 
                `desc` TEXT NOT NULL, `name` VARCHAR(255) NOT NULL, 
                `availableoptions` TEXT NOT NULL, 
                `isactive` BOOLEAN NOT NULL
                );
                ";
        require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
        dbDelta($sql);
    }       
   

    // This little block of code insures that we don't run this update routine again until the next time wpStoreCart is updated.
    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `producttype`='product' WHERE `producttype`='';");
    
   
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    $wpStoreCartOptions['database_version'] = $wpstorecart_version_int;
    $wpStoreCartOptions['run_updates']='false'; // These updates only need to be ran once.
    update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
}


/**
 * Checks to see if we need to do an upgrade
 */
if(!function_exists('wpscInstallerCheck')) {
	function wpscInstallerCheck() {
		global $wpstorecart_version_int;
		
		$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

                if(@intval($wpStoreCartOptions['database_version']) < $wpstorecart_version_int) {
                    wpscUpdate();
                }			
                
	}
}

/**
*
* This method creates the database schema during installation
*
* @global object $wpdb
* @global int $wpstorecart_version_int
*/
if(!function_exists('wpscInstallWpms')) {
	function wpscInstallWpms() {
	
		global $wpdb;
	
		wpsc_installer_install(); // Hook for installing
		
		$table_name = $wpdb->prefix . "wpstorecart_products";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
	
			$sql = "
	                                CREATE TABLE {$table_name} (
	                                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	                                `name` VARCHAR(512) NOT NULL,
	                                `introdescription` TEXT NOT NULL,
	                                `description` TEXT NOT NULL,
	                                `thumbnail` VARCHAR(512) NOT NULL,
	                                `price` DECIMAL( 22, 2 ) NOT NULL,
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
	                                `discountprice` DECIMAL(22,2) NOT NULL,
                                        `producttype` VARCHAR( 255 ) NOT NULL DEFAULT 'product',
                                        `status` VARCHAR( 128 ) NOT NULL,
                                        `options` TEXT NOT NULL,
                                        `productdesignercss` TEXT NOT NULL,    
                                        `shippingservices` TEXT NOT NULL                                         
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
	                                `amount` DECIMAL(22,2) NOT NULL,
	                                `percent` INT(3) NOT NULL,
	                                `description` VARCHAR(512) NOT NULL,
	                                `product` INT NOT NULL,
	                                `startdate` INT(8) NOT NULL,
	                                `enddate` INT(8) NOT NULL
	                                );
	                                ";
	
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	
	
	
		$table_name = $wpdb->prefix . "wpstorecart_cart";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
	
			$sql = "
	                                CREATE TABLE `{$table_name}` (
	                                `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY,
	                                `total` DECIMAL( 62, 2 ) NOT NULL,
	                                `itemcount` INT NOT NULL,
	                                `items` TEXT NOT NULL,
	                                `itemprices` TEXT NOT NULL,
	                                `itemqtys` TEXT NOT NULL,
	                                `itemname` TEXT NOT NULL,
	                                `itemshipping` TEXT NOT NULL,
	                                `itemtax` TEXT NOT NULL,
	                                `itemurl` TEXT NOT NULL,
	                                `itemimg` TEXT NOT NULL,
	                                `user_id` INT NOT NULL,
	                                `options` TEXT NOT NULL,
	                                `ipaddress` VARCHAR( 39 ) NOT NULL
	                                );
	                                ";
	
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
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
	                                `price` DECIMAL( 22, 2 ) NOT NULL,
	                                `shipping` DECIMAL(8,2) NOT NULL,
	                                `wpuser` INT NOT NULL,
	                                `email` VARCHAR(255) NOT NULL,
	                                `affiliate` INT NOT NULL,
	                                `date` INT( 8 ) NOT NULL);
	                                ";
	
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
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
	                                `postid` INT NOT NULL,
	                                `showtoall` BOOLEAN NOT NULL DEFAULT '1',
	                                `showtowhichgroups` TEXT NOT NULL,
	                                `discountstoall` BOOLEAN NOT NULL DEFAULT '1',
	                                `discountstowhichgroups` TEXT NOT NULL,
                                        `lineage` TEXT NOT NULL,
                                        `depth` INT NOT NULL
	                                );
	                                ";
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
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
	                                `userid` INT NOT NULL,
                                        `time` TIMESTAMP NOT NULL 
	                                );
	                                ";
	
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
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
	
	
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
	
	
		$table_name = $wpdb->prefix . "wpstorecart_alerts";
		if($wpdb->get_var("show tables like '$table_name'") != $table_name) {
		
			$sql = "
				CREATE TABLE {$table_name} (
                                  `primkey` int(11) NOT NULL AUTO_INCREMENT,
                                  `title` varchar(512) NOT NULL,
                                  `description` text NOT NULL,
                                  `conditions` text NOT NULL,
                                  `severity` varchar(64) NOT NULL,
                                  `image` varchar(255) NOT NULL,
                                  `url` varchar(255) NOT NULL,
                                  `qty` varchar(32) NOT NULL,
                                  `groupable` tinyint(1) NOT NULL,
                                  `clearable` tinyint(1) NOT NULL,
                                  `status` varchar(255) NOT NULL,
                                  `userid` int(11) NOT NULL,
                                  `adminpanel` tinyint(1) NOT NULL,
                                  `textmessage` tinyint(1) NOT NULL,
                                  `emailalert` tinyint(1) NOT NULL,
                                  `desktop` tinyint(1) NOT NULL,
                                  PRIMARY KEY (`primkey`)
				);
				";
		
		
			require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
			dbDelta($sql);
		}
                
                $table_name = $wpdb->prefix . "wpstorecart_packages";
                if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {
                    $sql = "
                            CREATE TABLE IF NOT EXISTS `{$table_name}` (
                            `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY ,
                            `productkey` INT NOT NULL ,
                            `weight` DECIMAL( 11, 2 ) NOT NULL ,
                            `length` DECIMAL( 11, 3 ) NOT NULL ,
                            `width` DECIMAL( 11, 3 ) NOT NULL ,
                            `depth` DECIMAL( 11, 3 ) NOT NULL ,
                            `options` TEXT NOT NULL
                            );
                            ";
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    dbDelta($sql);
                }                
		
                $table_name = $wpdb->prefix . "wpstorecart_field_def";
                if(@$wpdb->get_var("show tables like '$table_name'") != $table_name) {
                    $sql = "
                            CREATE TABLE IF NOT EXISTS `{$table_name}` (
                            `primkey` INT NOT NULL AUTO_INCREMENT PRIMARY KEY, 
                            `productkey` INT NOT NULL, `type` VARCHAR(32) NOT NULL, 
                            `information` VARCHAR(255) NOT NULL, 
                            `required` VARCHAR(32) NOT NULL, 
                            `defaultvalue` TEXT NOT NULL, 
                            `desc` TEXT NOT NULL, `name` VARCHAR(255) NOT NULL, 
                            `availableoptions` TEXT NOT NULL, 
                            `isactive` BOOLEAN NOT NULL
                            );
                            ";
                    require_once(ABSPATH . 'wp-admin/includes/upgrade.php');
                    dbDelta($sql);
                }                    
                
	
	}
}

/**
*
* Uninstalls and deactivates wpStoreCart
*
* @global object $wpdb
*/
if(!function_exists('wpscUninstall')) {
       function wpscUninstall() {
           global $wpdb;
           delete_option('wpStoreCartAdminOptions');
           $wpdb->query("DELETE FROM `{$wpdb->prefix}options` WHERE `option_name` = 'wpStoreCartAdminOptions';");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_products`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_meta`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_categories`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_orders`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_av`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_cart`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_coupons`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_log`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_alerts`;");
           $wpdb->query("DROP TABLE `{$wpdb->prefix}wpstorecart_packages`;");
       }
}

/**
 *
 * Installs wpStoreCart
 *
 * @global object $wpdb
 * @global int $wpstorecart_version_int
 * @return type
 */
if(!function_exists('wpscInstall')) {
	function wpscInstall() {
		global $wpdb;
	
		if (function_exists('is_multisite') && is_multisite()) {
			// check if it is a network activation - if so, run the activation function for each blog id
			if (isset($_GET['networkwide']) && ($_GET['networkwide'] == 1)) {
				$old_blog = $wpdb->blogid;
				// Get all blog ids
				$blogids = $wpdb->get_col($wpdb->prepare("SELECT blog_id FROM $wpdb->blogs"));
				foreach ($blogids as $blog_id) {
					switch_to_blog($blog_id);
					wpscInstallWpms();
				}
				switch_to_blog($old_blog);
				return;
			}
		}
		wpscInstallWpms();
                wpscUpdate();
	}
}


// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
// = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = = =
/**
 * Hook implementation:
 */

add_action('wpsc_installer_check', 'wpscInstallerCheck', 1); // Checks if an update needs to be processed
wpscInstallerCheck();
//wpscUpdate(); // Uncomment this line in order to force an update run

?>