<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpdb, $current_user;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    error_reporting(E_ALL);
    
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    
    if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }		
    global $wpdb;
    
    $wpscAccMaxItems = $_POST['wpsc_acc_max_items'];
    
    $icounter = 1;
    while($icounter < $wpscAccMaxItems) {
        $wpscParentProductId = intval($_POST['wpsc-keytoedit']);
        $wpscParentProductPrice = wpscProductGetPrice($wpscParentProductId, true);
        $wpscThisAttributeCombosPrice = $wpscParentProductPrice['price']  + $_POST['wpsc_acc_combo_pricediff_'.$icounter];
        $wpscThisAttributeCombosDiscountPrice = $wpscParentProductPrice['discountprice']  + $_POST['wpsc_acc_combo_pricediff_'.$icounter];
        $wpscThisAttributeCombosName = $_POST['wpsc_acc_combo_name_'.$icounter];
        $wpscThisAttributeCombosUK = $_POST['wpsc_acc_combo_uk_'.$icounter];
        $wpscThisAttributeCombosSKU = $_POST['wpsc_acc_combo_sku_'.$icounter];
        $wpscThisAttributeCombosQuantity = $_POST['wpsc_acc_combo_quantity_'.$icounter];
        $timestamp = date('Ymd');

        if($wpscParentProductPrice!=NULL) {

            
            $getTheAttributes = wpscProductGetAttributes($wpscParentProductId);
            if(@!isset($getTheAttributes[0]['useinventory'])) {
                $getTheAttributes[0]['useinventory'] = 0;
            }
            
            // Do an update or an insert
            $wpscAttributesGetDatabase = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `postid`='{$wpscParentProductId}' AND `status`='{$wpscThisAttributeCombosUK}';", ARRAY_A);
            if(isset($wpscAttributesGetDatabase[0]['primkey'])) {
                $insert = "UPDATE `{$wpdb->prefix}wpstorecart_products` SET `name`='{$wpscThisAttributeCombosName}', `price`='{$wpscThisAttributeCombosPrice}', `inventory`='{$wpscThisAttributeCombosQuantity}', `discountprice`='{$wpscThisAttributeCombosDiscountPrice}', `options`='{$wpscThisAttributeCombosSKU}' WHERE `postid`='{$wpscParentProductId}' AND `status`='{$wpscThisAttributeCombosUK}';";
            } else {
                $insert = "
                INSERT INTO `{$wpdb->prefix}wpstorecart_products` (`primkey`, `name`, `introdescription`, `description`, `thumbnail`, `price`, `shipping`, `download`, `tags`, `category`, `inventory`, `dateadded`, `postid`, `timesviewed`, `timesaddedtocart`, `timespurchased`, `useinventory`, `donation`, `weight`, `length`, `width`, `height`, `discountprice`, `producttype`, `status`, `options`, `productdesignercss`, `shippingservices`) VALUES
                (NULL, 
                '{$wpscThisAttributeCombosName}', 
                '', 
                '', 
                '', 
                '{$wpscThisAttributeCombosPrice}', 
                '', 
                '', 
                '', 
                '', 
                '{$wpscThisAttributeCombosQuantity}', 
                '{$timestamp}', 
                '{$wpscParentProductId}',
                0,
                0,
                0,
                {$getTheAttributes[0]['useinventory']},
                0,
                0,
                0,
                0,
                0,
                '{$wpscThisAttributeCombosDiscountPrice}',
                'attribute',
                '{$wpscThisAttributeCombosUK}',
                '{$wpscThisAttributeCombosSKU}',
                '',
                ''
                );
                ";	 
            }            
          
            $wpdb->query($insert);
        }
        
        $icounter++;
    }
    
}
?>