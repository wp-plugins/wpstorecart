<?PHP
global $wpStoreCart, $allowedToAccess, $wpdb, $affiliatepurchases;

$wpStoreCart::spHeader();

if ( function_exists('current_user_can') && !current_user_can('manage_options') ) {
        die(__('Cheatin&#8217; uh?'));
}

$allowedToAccess = true;

if(!file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.pro.php')) {

    echo '<iframe src="http://wpstorecart.com/store/wpscaffiliate-pro/" frameborder="0" border="0" cellspacing="0" style="border:0px;width:1000px;height:2500px;min-width:1000px;min-height:2500px;"></iframe>';

} else {
    $table_name = $wpdb->prefix . "wpstorecart_orders";
    $sql = "SELECT * FROM `{$table_name}` WHERE `affiliate`>0 AND `orderstatus`='Completed' ORDER BY `affiliate`, `date` DESC;";
    $affiliatepurchases = $wpdb->get_results( $sql , ARRAY_A );
    $icounter = 0;
    foreach ($affiliatepurchases as $affiliatepurchase) {
        global $userinfo2;
        $affiliatepurchases[$icounter]['cartcontents'] = $wpStoreCart->splitOrderIntoProduct($affiliatepurchase['primkey']);
        $userinfo2 = get_userdata($affiliatepurchase['affiliate']);
        @$affiliatepurchases[$icounter]['affiliateusername'] = $userinfo2->user_login;
        $userinfo2 = get_userdata($affiliatepurchase['wpuser']);
        @$affiliatepurchases[$icounter]['affiliatecustomer'] = $userinfo2->user_login;
        $icounter++;
    }
    require_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.pro.php');
}

?>