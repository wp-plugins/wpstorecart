<?php

if(!function_exists('wpscPiwikAddEcommerceItem')) {
    /**
     *
     * This adds a product into the order, and must be called for each product in the order. productSKU is a required parameter, 
     * it is also recommended that you send other parameters if they are applicable in your Ecommerce shop.
     * 
     * @param type $productSKU
     * @param type $productName
     * @param type $productCategory
     * @param type $price
     * @param type $quantity 
     */
    function wpscPiwikAddEcommerceItem($productSKU, $productName=NULL, $productCategory=NULL, $price=0.00, $quantity=1) {
        return 'piwikTracker.addEcommerceItem("'.$productSKU.'", "'.$productName.'", "'.$productCategory.'", '.$price.', '.$quantity.' );';
    }
}

if(!function_exists('wpscPiwikTrackEcommerceOrder')) {
    /**
     *
     * This tracks an Ecommerce order and sends the data to your Piwik server, for both this order and products previously added. 
     * Only orderId and grandTotal (ie. revenue) are required.
     * 
     * @param type $orderId
     * @param type $grandTotal
     * @param type $subTotal
     * @param type $tax
     * @param type $shipping
     * @param type $discount 
     */
    function wpscPiwikTrackEcommerceOrder($orderId, $grandTotal, $subTotal = NULL, $tax=NULL, $shipping = NULL, $discount = 'false') {
        return 'piwikTracker.trackEcommerceOrder("'.$orderId.'", '.$grandTotal.', '.$subTotal.', '.$tax.', '.$shipping.', '.$discount.' );';
    }
}

if(!function_exists('wpscPiwikTrackEcommerceOrder')) {
    /**
     * 
     * @param type $amount
     * @return type 
     */
    function wpscPiwikTrackEcommerceCartUpdate($amount) {
        return 'piwikTracker.trackEcommerceCartUpdate('.$amount.');';
    }
}

if(!function_exists('wpscPiwikTrackTopCode')) {
    function wpscPiwikTrackTopCode() {
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        echo '
        <script type="text/javascript">
            var pkBaseURL = (("https:" == document.location.protocol) ? "https://'.$wpStoreCartOptions['piwik_url'].'" : "http://'.$wpStoreCartOptions['piwik_url'].'");
            document.write(unescape("%3Cscript src=\'" + pkBaseURL + "piwik.js\' type=\'text/javascript\'%3E%3C/script%3E"));
            </script><script type="text/javascript">
                try {
                    var piwikTracker = Piwik.getTracker(pkBaseURL + "piwik.php", '.$wpStoreCartOptions['piwik_siteid'].');
                    piwikTracker.trackPageView();
                    piwikTracker.enableLinkTracking();

';    
    }
}

if(!function_exists('wpscPiwikTrackBottomCode')) {
    function wpscPiwikTrackBottomCode() {
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
                    echo '

                } catch( err ) {}
        </script><noscript><p><img src="http://'.$wpStoreCartOptions['piwik_url'].'piwik.php?idsite='.$wpStoreCartOptions['piwik_siteid'].'" style="border:0" alt="" /></p></noscript>';   
    }
}

if(!function_exists('wpscPiwikTrack')) {
    function wpscPiwikTrack() {
         wpscPiwikTrackTopCode();
         wpscPiwikTrackBottomCode(); 
    }
}

if(!function_exists('wpscPiwikTrackOrderPlaced')) {
    function wpscPiwikTrackOrderPlaced($orderid) {
        global $wpdb;
        
        wpscPiwikTrackTopCode();

        $theIndividualPrimkeys = wpscSplitOrderIntoProductKeys($orderid);
        foreach($theIndividualPrimkeys as $theIndividualPrimkey) {
            $theIndividualProduct = wpscProductGetProductById($theIndividualPrimkey);
            echo wpscPiwikTrackEcommerceOrder($theIndividualProduct['primkey'], wpscProductGetPrice($theIndividualPrimkey));
        }

        @$results = $wpdb->get_results("SELECT `price`, `shipping` FROM `{$wpdb->prefix}wpstorecart_orders` WHERE `primkey`='{$orderid}';", ARRAY_A);
        @$grandTotal = $results[0]['price'] + $results[0]['shipping'];
        
        echo wpscPiwikTrackEcommerceOrder($orderid, $grandTotal);
        
        wpscPiwikTrackBottomCode();            
 
    }
}

$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
if($wpStoreCartOptions['piwik_enabled']=='true' && @!isset($_GET['wpsc-piwik'])) { 
    add_action('wp_footer', 'wpscPiwikTrack'); //Track Piwik ecommerce.
}

?>