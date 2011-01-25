<?PHP
global $wpStoreCart, $allowedToAccess, $wpdb, $affiliatepurchases, $affiliatesettings,$wpstorecart_version,$wpscAffiliateVersion;
$wpscAffiliateVersion = 1.1;

$allowedToAccess = true;

if(!file_exists(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.pro.php')) {

    echo '<iframe src="http://wpstorecart.com/store/wpscaffiliate-pro/" frameborder="0" border="0" cellspacing="0" style="border:0px;width:1000px;height:2500px;min-width:1000px;min-height:2500px;"></iframe>';

} else {


    $devOptions = $wpStoreCart->getAdminOptions();

    if(@isset($_POST['minimumAffiliatePayment']) || @isset($_POST['minimumDaysBeforePaymentEligable'])) {
        if (isset($_POST['minimumAffiliatePayment'])) {
                $devOptions['minimumAffiliatePayment'] = $wpdb->escape($_POST['minimumAffiliatePayment']);
        }
        if (isset($_POST['minimumDaysBeforePaymentEligable'])) {
                $devOptions['minimumDaysBeforePaymentEligable'] = $wpdb->escape($_POST['minimumDaysBeforePaymentEligable']);
        }
        if (isset($_POST['affiliateInstructions'])) {
                $devOptions['affiliateInstructions'] = $wpdb->prepare($_POST['affiliateInstructions']);
        }
        update_option($wpStoreCart->adminOptionsName, $devOptions);
    }
    $affiliatesettings['minimumAffiliatePayment'] = $devOptions['minimumAffiliatePayment'];
    $affiliatesettings['minimumDaysBeforePaymentEligable'] = $devOptions['minimumDaysBeforePaymentEligable'];
    $affiliatesettings['affiliateInstructions'] = $devOptions['affiliateInstructions'];

    $table_name = $wpdb->prefix . "wpstorecart_orders";
    $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
    $sql = "SELECT * FROM `{$table_name_meta}`, `{$table_name}` WHERE  `{$table_name}`.`affiliate`>0 AND  `{$table_name}`.`orderstatus`='Completed' AND `{$table_name}`.`primkey`=`{$table_name_meta}`.`foreignkey` ORDER BY  `{$table_name}`.`affiliate`,  `{$table_name}`.`date` DESC;";
    $results = $wpdb->get_results( $sql , ARRAY_A );
    $icounter = 0;
    foreach ($results as $result) {
        global $userinfo2;
        $affiliatepurchases[$icounter]['cartcontents'] = $wpStoreCart->splitOrderIntoProduct($result['primkey']);
        $affiliatepurchases[$icounter]['amountpaid'] = $result['value'];
        // Mark that we paid people or vice versa
        if(@isset($_POST['amountpaid'. $result['primkey']])) {
            $amountpaid = $wpdb->prepare($_POST['amountpaid'. $result['primkey']]);
            $wpdb->query("UPDATE `{$table_name_meta}` SET `value`='$amountpaid' WHERE `foreignkey`={$result['primkey']} AND `type`='affiliatepayment'; ");
            $affiliatepurchases[$icounter]['amountpaid'] = $amountpaid;
        }
        $affiliatepurchases[$icounter]['primkey'] = $result['primkey'];
        $affiliatepurchases[$icounter]['price'] = $result['price'];
        $affiliatepurchases[$icounter]['date'] = $result['date'];
        $affiliatepurchases[$icounter]['orderstatus'] = $result['orderstatus'];
        $userinfo2 = get_userdata($result['affiliate']);
        @$affiliatepurchases[$icounter]['affiliateusername'] = $userinfo2->user_login;
        $userinfo2 = get_userdata($result['wpuser']);
        @$affiliatepurchases[$icounter]['affiliatecustomer'] = $userinfo2->user_login;
        $icounter++;
    }
    require_once(WP_PLUGIN_DIR.'/wpstorecart/saStoreCartPro/affiliates.pro.php');
}

?>