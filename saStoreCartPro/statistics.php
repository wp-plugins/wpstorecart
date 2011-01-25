<?PHP
global $wpStoreCart, $allowedToAccess, $statsOptions, $wpdb, $devOptions;

$allowedToAccess = true;

if(!file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/statistics.pro.php')) {

    echo '
    <h2>Statistics</h2>
    <h3>Requires wpStoreCart PRO!  <a href="http://wpstorecart.com/store/business-support-wpstorecart-pro/" target="_blank">Upgrade to the PRO version now by making a donation!</a></h3>
    <p>wpStoreCart PRO adds several premium features to your wpStoreCart installation, including a full affiliate system, advanced
    built in analytics, and much more!  wpStoreCart PRO is not open source, but instead is
    licensed under a commercial license, since it uses wpStoreCart  which is LGPL.  However, once we
    have made $25,000 in donations, we will also release wpStoreCart PRO under the LGPL and there will no longer be any
    commercial licenses associated with this software.  Help support open source software by making a donation today to upgrade
    to wpStoreCart PRO!
    </p>
    <h3>Requires wpStoreCart PRO!  <a href="http://wpstorecart.com/store/business-support-wpstorecart-pro/" target="_blank">Upgrade to the PRO version now by making a donation!</a></h3>
    ';
} else {
    
    $statsOptions['databasename'] = DB_NAME;
    $statsOptions['databaseuser'] = DB_USER;
    $statsOptions['databasepass'] = DB_PASSWORD;
    $statsOptions['databasehost'] =DB_HOST;
    $statsOptions['databaseprefix'] = $wpdb->prefix;
    $statsOptions['databasetable'] = $wpdb->prefix . 'wpstorecart_log';

    
    require_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/statistics.pro.php');
}

?>