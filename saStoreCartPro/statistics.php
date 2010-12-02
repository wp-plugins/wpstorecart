<?PHP
global $wpStoreCart, $allowedToAccess, $statsOptions;

$wpStoreCart::spHeader();

if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
        die(__('Cheatin&#8217; uh?'));
}

$allowedToAccess = true;

if(!file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/statistics.pro.php')) {

    echo '<iframe src="http://wpstorecart.com/store/advanced-statistics-revenue-gerowth-strategies/" frameborder="0" border="0" cellspacing="0" style="border:0px;width:1000px;height:2500px;min-width:1000px;min-height:2500px;"></iframe>';

    
} else {
    
    $statsOptions['databasename'] = DB_NAME;
    $statsOptions['databaseuser'] = DB_USER;
    $statsOptions['databasepass'] = DB_PASSWORD;
    $statsOptions['databasehost'] =DB_HOST;
    $statsOptions['databasetable'] = $wpdb->prefix . 'wpstorecart_log';

    require_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/statistics.pro.php');
}

?>