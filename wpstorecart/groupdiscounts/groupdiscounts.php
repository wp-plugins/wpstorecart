<?php

if(!function_exists('wpscGroupDiscounts')) {
    /**
     *
     * Check to see if a user qualifies for a group discount for the category, or if they can even see the category at all
     *
     * @global object $wpdb
     * @param integer $category_id
     * @param integer $user_id
     * @return array
     */
    function wpscGroupDiscounts($category_id, $user_id) {
        global $wpdb;
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        $user_id = intval($user_id);
        $category_id = intval($category_id);
        $return_value = array();
        $return_value['can_see_this_category'] = true;
        $return_value['can_have_discount'] = true;
        $return_value['discount_amount'] = 0;
        $return_value['gd_display'] = '';
        $return_value['gd_saleprice'] = true;
        if($wpStoreCartOptions['gd_enable']=='true') { // Are group discounts enabled?  If not, return the default values


            if($wpStoreCartOptions['gd_saleprice']=='false') {
                $return_value['gd_saleprice'] = false;
            }

            $user_check = new WP_User( $user_id );

            $grabrecord = "SELECT * FROM `{$wpdb->prefix}wpstorecart_categories` WHERE `primkey`={$category_id};";
            $results = $wpdb->get_results( $grabrecord , ARRAY_A );
            if(isset($results[0])) { 
                    $showtoall = stripslashes($results[0]['showtoall']);
                    if($showtoall == 0) { // If this group shouldn't be shown to specific groups, right here will say it can't be shown at all.  Next we'll do a permission check to see if we can reverse that decision
                            $return_value['can_see_this_category'] = false;
                            $showtowhichgroups = unserialize(stripslashes($results[0]['showtowhichgroups']));
                            foreach($showtowhichgroups as $showtothisuser) {
                                    if($user_check->has_cap( $showtothisuser )) {
                                            $return_value['can_see_this_category'] = true; // SUCCESS.  This user is in a group that can see this category
                                    }
                            }
                    }

                    $discountstoall = stripslashes($results[0]['discountstoall']);
                    if($discountstoall == 0) { // If this group shouldn't be given a discount to a specific group, right here will say it can't be done at all.  Next we'll do a permission check to see if we can reverse that decision
                            $return_value['can_have_discount'] = false;
                            $discountstowhichgroups = unserialize(stripslashes($results[0]['discountstowhichgroups']));
                            foreach($discountstowhichgroups as $discounttothisuser) {
                                    if($user_check->has_cap( $discounttothisuser )) {
                                            $return_value['can_have_discount'] = true; // SUCCESS.  This user is in a group can get a discount
                                    }
                            }
                    }

            } else {
                $return_value['can_have_discount'] = true; // No category matched this category number, so discount is assumed as yes
                $return_value['can_see_this_category'] = true; 
            }

            if($return_value['can_have_discount'] == true) { // Here at the end, if it's detemined this user still gets a discount, let's figure out how much
                $theResultsZ = $wpdb->get_results( "SELECT * FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `type`='groupdiscount';" , ARRAY_A );
                if(isset($theResultsZ[0]['primkey'])) {
                    foreach($theResultsZ as $theResult) {
                        $exploder = explode('||', $theResult['value']);
                        if($exploder[2]=='true') { // this means we're dealing with an active discount
                            if($exploder[1]>$return_value['discount_amount']) { // If the discount amount is greater than the previously held discount amount
                                $cap_name = wpscSlug($exploder[0]);
                                if($user_check->has_cap( $cap_name )) {
                                    $return_value['discount_amount'] = $exploder[1]; // This is our new discount percentage then
                                    if($wpStoreCartOptions['gd_display']=='true') {
                                        $return_value['gd_display'] = $exploder[0] . ' ('.$return_value['discount_amount'].'%)'; // This is what to display if the option is set to display the group name and discount percent
                                    }
                                }
                            }
                        }
                    }
                }
            }

        }
        return $return_value;

    }
}

if(!function_exists('wpscGroupDiscountReturnPrice')) {
    /**
     *
     * @param type $price
     * @param type $discountPrice
     * @param array $groupDiscount
     * @return array 
     */
    function wpscGroupDiscountReturnPrice($price, $discountPrice, $groupDiscount) {
        $result = array();
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        
        if($discountPrice > 0) {
        $priceToAdjust = $discountPrice;
        } else {
        $priceToAdjust = $price;
        }       

        if ($groupDiscount['can_have_discount']==true && $wpStoreCartOptions['gd_enable']=='true') {
            $percentDiscount = $groupDiscount['discount_amount'] / 100;
            $discountToSubtract = $priceToAdjust * $percentDiscount;
            if($groupDiscount['gd_saleprice']==true && $discountToSubtract > 0) {
                $result['discountprice'] = number_format($priceToAdjust - $discountToSubtract, 2);
            }                                                                      
            $priceToAdjust = number_format($priceToAdjust - $discountToSubtract, 2);
            if($result['discountprice']==0) { 
                $result['price'] = $priceToAdjust;
            }
        }   
        return $result;        
    }
}

?>