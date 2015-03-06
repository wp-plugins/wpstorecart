<?php

if(!function_exists('wpscProductIsPublishedPage')) {
    /**
     *
     * @global object $wpdb
     * @param type $product_id
     * @return boolean Returns TRUE if the product is published, returns FALSE if its a draft or couldn't be found
     */
    function wpscProductIsPublishedPage($product_id) {
        global $wpdb;
        $result = $wpdb->get_results("SELECT `postid` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$product_id}'; ", ARRAY_A);
        if(isset($result[0]['postid'])) {
            $wpsc_check_draft_page = get_page($result[0]['postid']);  
            if($wpsc_check_draft_page->post_status == 'publish') {
                return true;
            } else {
                return false;
            }
        } else {
            return false;
        }
    }
}


if(!function_exists('wpscProductGetGrid')) {
    /**
     * @deprecated since 3.9.3
     * Returns the string that wraps the HTML in the grid div
     * @param string $input
     * @return string 
     */
    function wpscProductGetGrid($input=NULL) {
        //return '<div id="wpsc-grid">'.$input.'</div>';
        return null;
    }
}

if(!function_exists('wpscProductGetVariations')) {
    function wpscProductGetVariations($primkey, $option=NULL) {
        global $wpdb;
        if($option==NULL) {
            $results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `postid`='{$primkey}' AND `producttype`='variation';", ARRAY_A);
        } else {
            $results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `postid`='{$primkey}' AND `options`='{$option}' AND `producttype`='variation';", ARRAY_A);
        }
        if(isset($results[0]['primkey'])) {
            return $results;
        } else {
            return NULL;
        }
    }
}

if(!function_exists('wpscProductGetVariationsSelection')) {
    function wpscProductGetVariationsSelection($primkey) {
        global $wpdb;
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 
        
        $wpscIsAttributes = wpscProductCheckForAttributes($primkey);
        
        if(!$wpscIsAttributes) {
        
            // Here's for variations
            $output = NULL;
            $results = $wpdb->get_results("SELECT COUNT( * ) AS `Rows` , `options` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `postid`='$primkey' AND `producttype`='variation' GROUP BY `options` ORDER BY `options`;", ARRAY_A);
            $parentNameResults = $wpdb->get_results("SELECT `name` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='$primkey';", ARRAY_A);
            foreach($results as $result) {
                $nextResults = wpscProductGetVariations($primkey, $result['options']);

                $typeIsDetermined=false;
                foreach ($nextResults as $nextResult) {
                    if(!$typeIsDetermined && $nextResult['status']=='dropdown') {
                        $output .= htmlentities($result['options']).': <select onchange="wpscLoadProductVariation(jQuery(this).val(), \''.plugins_url().'\', '.$primkey.', \''.htmlentities($parentNameResults[0]['name']).'\', \''.htmlentities($wpStoreCartOptions['currency_symbol']).'\', \''.htmlentities($wpStoreCartOptions['currency_symbol_right']).'\' );"><option value="'.$primkey.'"></option>';
                        $typeIsDetermined='dropdown';
                    }
                    if($nextResult['status']=='dropdown') {
                        $output.='<option value="'.$nextResult['primkey'].'">'.$nextResult['name'].'</option>';
                    }

                }

                if($typeIsDetermined=='dropdown') {
                        $output .= '</select><br />';
                }             

            }
        
        }

        if($wpscIsAttributes) {
            
            $wpscAttributesResults = wpscProductGetAttributes($primkey);
            $wpscAttributesGroup = wpscProductGetAttributeGroups($wpscAttributesResults);
            $wpscProductGetAttribute = wpscProductGetAttributeKeyArray($wpscAttributesGroup);        
            $parentNameResults = $wpdb->get_results("SELECT `name` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='$primkey';", ARRAY_A);

            echo '<script type="text/javascript"> jQuery(document).ready(function($) { wpscLoadProductAttribute(\''.plugins_url().'\', '.$primkey.', \''.htmlentities($parentNameResults[0]['name']).'\', \''.htmlentities($wpStoreCartOptions['currency_symbol']).'\', \''.htmlentities($wpStoreCartOptions['currency_symbol_right']).'\' ); });</script>';            
            
            // Here's for attributes
            $datasetCount = 0;
            foreach ($wpscProductGetAttribute as $wpscAttributesGroupKey) {
                if($datasetCount == 0) {
                    $output .= '<div class="wpsc-product-attributes">';
                }
                $output .= htmlentities($wpscAttributesGroupKey). ': <select name="wpsc_attribute_'.wpscSlug($wpscAttributesGroupKey).'" class="wpsc-product-attribute-options" onchange="wpscLoadProductAttribute(\''.plugins_url().'\', '.$primkey.', \''.htmlentities($parentNameResults[0]['name']).'\', \''.htmlentities($wpStoreCartOptions['currency_symbol']).'\', \''.htmlentities($wpStoreCartOptions['currency_symbol_right']).'\' );">';
                foreach($wpscAttributesGroup["{$wpscAttributesGroupKey}"] as $wpscFinalAttributeGroup) {
                    $output.='<option  value="'.$wpscFinalAttributeGroup['primkey'].'A">'.$wpscFinalAttributeGroup['title'].'</option>';
                }
                $output .= '</select><br />';
                $datasetCount++;
            }
            $output .= '</div>';
       
        }
        
        
        return $output;
    }
}

if(!function_exists('wpscProductGetToolbar')) {
    function wpscProductGetToolbar($category=null) {
            global $wpdb;
            $output = "
            <p id=\"wpsc-filter\">";

            if($category==NULL) {$parent_category = 0;} else {$parent_category = $category;}

            $wpsc_cat_results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_categories` WHERE `parent`='$category';",ARRAY_A);
            $output .= '    <a onclick="jQuery.get(\''.plugins_url().'/wpstorecart/wpstorecart/products/filter.php?wpsc_cat_key=0\', function(data) {jQuery(\'#wpsc-grid\').slideToggle(\'slow\', function(){ jQuery(\'#wpsc-grid\').html(data); jQuery(\'#wpsc-grid\').fadeIn(\'fast\'); }); }); jQuery.get(\''.plugins_url().'/wpstorecart/wpstorecart/products/toolbar.php?wpsc_cat_key=0\', function(data) { jQuery(\'#wpsc-filter\').slideToggle(\'slow\', function(){ jQuery(\'#wpsc-filter\').html(data); jQuery(\'#wpsc-filter\').fadeIn(\'fast\'); }); });return false;" class="wpscfilterbutton" href="#"><button>'.__('All', 'wpstorecart').'</button></a>';
            if(@isset($wpsc_cat_results)) {
                foreach($wpsc_cat_results as $wpsc_cat_result) {
                    $output .= '    <a onclick="jQuery.get(\''.plugins_url().'/wpstorecart/wpstorecart/products/filter.php?wpsc_cat_key='.$wpsc_cat_result['primkey'].'\', function(data) {jQuery(\'#wpsc-grid\').slideToggle(\'slow\', function(){ jQuery(\'#wpsc-grid\').html(data); jQuery(\'#wpsc-grid\').fadeIn(\'fast\'); }); }); jQuery.get(\''.plugins_url().'/wpstorecart/wpstorecart/products/toolbar.php?wpsc_cat_key='.$wpsc_cat_result['primkey'].'\', function(data) { jQuery(\'#wpsc-filter\').slideToggle(\'slow\', function(){ jQuery(\'#wpsc-filter\').html(data); jQuery(\'#wpsc-filter\').fadeIn(\'fast\'); }); });return false;" class="wpscfilterbutton" href="#"><button>'.$wpsc_cat_result['category'].'</button></a>';
                }
            }
            $output .= "
            </p>

            ";

            return $output;
    }
}

if(!function_exists('wpscProductGetFieldsArray')) {
    function wpscProductGetFieldsArray($productkey) {
        global $wpdb;
        $results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_field_def` WHERE `productkey`='{$productkey}';", ARRAY_A);
        return $results;
    }
}


if(!function_exists('wpscProductGetFields')) {
    function wpscProductGetFields($productkey) {
        $output = null;
        $results = wpscProductGetFieldsArray($productkey);
        if(isset($results[0]['primkey'])) {
            foreach ($results as $result) {
                if($result['type']=='information') {
                    $output .= "{$result['desc']} : {$result['defaultvalue']}<br />";
                }
            }
        }
        return $output;
    }
}



if(!function_exists('wpscProductGetCatalog')) {
    /**
     *
     * @global object $wpdb
     * @param type $quantity
     * @param type $displayOrder
     * @param type $displayThumb
     * @param type $displayIntroDescription
     * @param type $displayDescription
     * @param type $displayThumbMaxWidth
     * @param type $displayThumbMaxHeight
     * @param type $orderby
     * @return string 
     */
    function wpscProductGetCatalog($quantity = 10, $category = null, $displayOrder = 'List all products in custom order', $displayThumb='true', $displayIntroDescription='true', $displayDescription='false', $displayThumbMaxWidth=NULL, $displayThumbMaxHeight=NULL, $orderby='') {
        global $wpdb, $current_user, $wpsc_result;
        
        $listsubcategories = false;
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 
        wp_get_current_user();
        if ( 0 == $current_user->ID ) {
            // Not logged in.
            $theuser = 0;
        } else {
            $theuser = $current_user->ID;
        }           
        
        $output = NULL;

        // If product filter is on
        if($wpStoreCartOptions['enable_product_filters']=='true' && !isset($_GET['wpsc_cat_key'])) {
            $output .= wpscProductGetToolbar($category);
        }         
        
        $output .= '<div id="wpsc-grid">';
        
        
        $table_name = $wpdb->prefix.'wpstorecart_products';
         

        if( !isset( $_GET['wpscPage'] ) || !is_numeric($_GET['wpscPage'])) {
            $startat = 0;
        } else {
            $startat = ($_GET['wpscPage'] - 1) * $quantity;
        }


        if(@isset($_GET['wpsccat']) || $category!=NULL ) {
            $displayOrder = 'List all products in custom order';
            $listsubcategories = true;
            if(@$_GET['wpsccat']==NULL || @!isset($_GET['wpsccat'])) {
                $_GET['wpsccat'] = $category;
            }
        }
        
        if($category!=null) {
            $category_sql = " AND `category`='{$category}' ";
        } else {
            $category_sql = NULL;
        }        
        
        if($displayOrder=='List all categories (Ascending)' && $category_sql == NULL) {
            if($orderby=='') {
                $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` ASC LIMIT {$startat}, {$quantity};";
            } else {
                $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 {$orderby} LIMIT {$startat}, {$quantity};";
            }
            $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` ASC;");
            $secondcss = 'wpsc-categories';
        } else {
            $secondcss = 'wpsc-products';
        }
        if($displayOrder=='List all categories' && $category_sql == NULL) {
            if($orderby=='') {
                $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` DESC LIMIT {$startat}, {$quantity};";
            } else {
                $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 {$orderby} LIMIT {$startat}, {$quantity};";
            }
            $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`=0 ORDER BY `primkey` DESC;");
            $secondcss = 'wpsc-categories';
        }        
        
        
        if($displayOrder=='List all products in custom order') {
        $grabrecord = "SELECT * FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `type`='custom_product_order';";
        $results = $wpdb->get_results( $grabrecord , ARRAY_A );
        if(isset($results[0]['primkey'])) {
            $sql = "SELECT * FROM `{$table_name}` WHERE `producttype`='product' AND `status`='publish' {$category_sql} ORDER BY field(primkey, {$results[0]['value']}) ASC LIMIT {$startat}, {$quantity};";
        } else {
            $sql = "SELECT * FROM `{$table_name}` WHERE `producttype`='product' AND `status`='publish' {$category_sql} LIMIT {$startat}, {$quantity};";
        }
        $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` WHERE `status`='publish' {$category_sql} ;");
        }

        if($displayOrder=='List all products' || $displayOrder=='List newest products') {
        if($orderby=='') {
            $sql = "SELECT * FROM `{$table_name}` WHERE `producttype`='product' AND `status`='publish' {$category_sql} ORDER BY `dateadded` DESC LIMIT {$startat}, {$quantity};";
        } else {
            $sql = "SELECT * FROM `{$table_name}` WHERE `producttype`='product' AND `status`='publish' {$category_sql} {$orderby} LIMIT {$startat}, {$quantity};";
        }
        $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` WHERE `status`='publish' {$category_sql} ;");
        }
        if($displayOrder=='List most popular products') {
        if($orderby=='') {
            $sql = "SELECT * FROM `{$table_name}` WHERE `producttype`='product' AND `status`='publish' {$category_sql} ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT {$startat}, {$quantity};";
        } else {
            $sql = "SELECT * FROM `{$table_name}` WHERE `producttype`='product' AND `status`='publish' {$category_sql} {$orderby} LIMIT {$startat}, {$quantity};";
        }
            $total = $wpdb->get_var("SELECT COUNT(primkey) FROM `{$table_name}` WHERE `status`='publish' {$category_sql} ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC;");
        }

        $results = $wpdb->get_results( $sql , ARRAY_A );

        if( $displayThumbMaxWidth==NULL) {$displayThumbMaxWidth=$wpStoreCartOptions['wpStoreCartwidth'];} 
        if( $displayThumbMaxHeight==NULL) {$displayThumbMaxHeight=$wpStoreCartOptions['wpStoreCartheight'];}
        
        if($displayThumb=='true') {
        $usepictures='true';
        $maxImageWidth = $displayThumbMaxWidth;
        $maxImageHeight = $displayThumbMaxHeight;
        }
        if($displayIntroDescription=='true') {
            $usetext='true';
        }


        // If we're dealing with categories, we have different fields to deal with than products.
        if($displayOrder=='List all categories' || $displayOrder=='List all categories (Ascending)') {
            if(isset($results)) {
                    foreach ($results as $result) {

                            // Group code
                            $groupDiscount = wpscGroupDiscounts($result['primkey'], $current_user->ID);
                            if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {

                            } else {

                                if($result['postid'] == 0 || $result['postid'] == '') { // If there's no dedicated category pages, use the default
                                    if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                                        $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=lc&wpsccat='.$result['primkey'];
                                    } else {
                                        $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=lc&wpsccat='.$result['primkey'];
                                    }
                                } else {
                                    $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                }                                
                                
                                $productListingOrder = wpscProductReturnCurrentGridItemOrder();

                                $output .= '<ul class="wpsc-products">';
                                foreach($productListingOrder as $productListingOrderCurrent) {                    
                                    switch($productListingOrderCurrent) {
                                        case 1:
                                            if(trim($result['thumbnail']=='')) {
                                                $result['thumbnail'] = plugins_url() .'/wpstorecart/images/default_product_img.jpg';
                                            }        
                                            if($usepictures=='true' || $result['thumbnail']!='' ) {
                                                    $output .= '<li class="wpsc-thumbnail-handle" id="wpscsort_1"><a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$result['thumbnail'].'" alt="'.wpscSlug($result['category']).'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a></li>';
                                            }                                            
                                        break;    
                                        case 2:
                                            if($usetext=='true' && $wpStoreCartOptions['displayTitle']=='true') {
                                                    $output .= '<li class="wpsc-title" id="wpscsort_2"><a href="'.$permalink.'">'.stripslashes($result['category']).'</a></li>';
                                            }                                    
                                        break;
                                        case 4:
                                            if($wpStoreCartOptions['displayintroDesc']=='true'){
                                                    $output .= '<li class="wpsc-description" id="wpscsort_4">'.stripslashes($result['description']).'</li>';
                                            }                                   
                                        break;                                        
                                    }                                
                                }
                                $output .= '</ul>';
                                
                            }
                    }
                    $output .= '<div class="wpsc-clear"></div>';
            }
        } else {        
            
            if($listsubcategories) {
                
                if($orderby=='') {
                    $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`='".intval($_GET['wpsccat'])."' ORDER BY `primkey` DESC LIMIT {$startat}, {$quantity};";
                } else {
                    $sql = "SELECT * FROM `". $wpdb->prefix ."wpstorecart_categories` WHERE `parent`='".intval($_GET['wpsccat'])."' {$orderby} LIMIT {$startat}, {$quantity};";
                }
           
                $scresults = $wpdb->get_results( $sql , ARRAY_A );
                
                if(isset($scresults)) {
                        foreach ($scresults as $result) {

                                // Group code
                                $groupDiscount = wpscGroupDiscounts($result['primkey'], $current_user->ID);
                                if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {

                                } else {

                                    if($result['postid'] == 0 || $result['postid'] == '') { // If there's no dedicated category pages, use the default
                                        if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                                            $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=lc&wpsccat='.$result['primkey'];
                                        } else {
                                            $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=lc&wpsccat='.$result['primkey'];
                                        }
                                    } else {
                                        $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                    }                                

                                    $productListingOrder = wpscProductReturnCurrentGridItemOrder();

                                    $output .= '<ul class="wpsc-products">';
                                    foreach($productListingOrder as $productListingOrderCurrent) {                    
                                        switch($productListingOrderCurrent) {
                                            case 1:
                                                if(trim($result['thumbnail']=='')) {
                                                    $result['thumbnail'] = plugins_url() .'/wpstorecart/images/default_product_img.jpg';
                                                }        
                                                if($usepictures=='true' || $result['thumbnail']!='' ) {
                                                        $output .= '<li class="wpsc-thumbnail-handle" id="wpscsort_1"><a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$result['thumbnail'].'" alt="'.wpscSlug($result['category']).'"';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= '/></a></li>';
                                                }                                            
                                            break;    
                                            case 2:
                                                if($usetext=='true' && $wpStoreCartOptions['displayTitle']=='true') {
                                                        $output .= '<li class="wpsc-title" id="wpscsort_2"><a href="'.$permalink.'">'.stripslashes($result['category']).'</a></li>';
                                                }                                    
                                            break;
                                            case 4:
                                                if($wpStoreCartOptions['displayintroDesc']=='true'){
                                                        $output .= '<li class="wpsc-description" id="wpscsort_4">'.stripslashes($result['description']).'</li>';
                                                }                                   
                                            break;                                        
                                        }                                
                                    }
                                    $output .= '</ul>';

                                }
                        }
                        $output .= '<div class="wpsc-clear"></div>';
                }                
            } 
            
            
            if(isset($results)) {
                    
                    foreach ($results as $wpsc_result) {


                            // Group code
                            $groupDiscount = wpscGroupDiscounts($wpsc_result['category'], $current_user->ID);
                            if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {

                            } else {
                            // end Group Code
                                // This code checks to see if we will be potentially displaying subscription products with either the price or add to cart button visible.  If so, we query each product for subscription information
                                $wpsc_price_type = 'charge';
                                $membership_value = '';
                                if($wpStoreCartOptions['displaypriceonview']=='true' || $wpStoreCartOptions['displayAddToCart']=='true'){
                                    $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                                    $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$wpsc_result['primkey']};";
                                    $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                                    if(isset($resultsMembership)) {
                                        foreach ($resultsMembership as $pagg) {
                                            $membership_primkey = $pagg['primkey'];
                                            $membership_value = $pagg['value'];
                                        }
                                        if($membership_value!='') {
                                            $theExploded = explode('||', $membership_value);
                                            // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                                            $wpsc_price_type = $theExploded[0];
                                            $wpsc_membership_trial1_allow = $theExploded[1];
                                            $wpsc_membership_trial2_allow = $theExploded[2];
                                            $wpsc_membership_trial1_amount = $theExploded[3];
                                            $wpsc_membership_trial2_amount = $theExploded[4];
                                            $wpsc_membership_regular_amount = $theExploded[5];
                                            $wpsc_membership_trial1_numberof = $theExploded[6];
                                            $wpsc_membership_trial2_numberof = $theExploded[7];
                                            $wpsc_membership_regular_numberof = $theExploded[8];
                                            $wpsc_membership_trial1_increment = $theExploded[9];
                                            $wpsc_membership_trial2_increment = $theExploded[10];
                                            $wpsc_membership_regular_increment = $theExploded[11];
                                            if($wpsc_membership_trial1_increment=='D'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['day'];}
                                            if($wpsc_membership_trial2_increment=='D'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['day'];}
                                            if($wpsc_membership_regular_increment=='D'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['day'];}
                                            if($wpsc_membership_trial1_increment=='W'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['week'];}
                                            if($wpsc_membership_trial2_increment=='W'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['week'];}
                                            if($wpsc_membership_regular_increment=='W'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['week'];}
                                            if($wpsc_membership_trial1_increment=='M'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['month'];}
                                            if($wpsc_membership_trial2_increment=='M'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['month'];}
                                            if($wpsc_membership_regular_increment=='M'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['month'];}
                                            if($wpsc_membership_trial1_increment=='Y'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['year'];}
                                            if($wpsc_membership_trial2_increment=='Y'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['year'];}
                                            if($wpsc_membership_regular_increment=='Y'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['year'];}
                                        }
                                    }
                                }

                                $permalink = get_permalink( $wpsc_result['postid'] ); // Grab the permalink based on the post id associated with the product

                                $output .= apply_filters('wpsc_display_catalog_start', '');


                                
                                $output .= '<ul class="wpsc-products wpsc-grid-product-'.$wpsc_result['primkey'].'">';

                                $productListingOrder = wpscProductReturnCurrentGridItemOrder();


                                foreach($productListingOrder as $productListingOrderCurrent) {                    
                                    switch($productListingOrderCurrent) {
                                        case 1:
                                            if($usepictures=='true') {

                                                $output .= '<li class="wpsc-thumbnail-handle" id="wpscsort_1">'; $output .= apply_filters('wpsc_display_catalog_before_thumbnail', ''); $output.= '<a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'.$wpsc_result['thumbnail'].'" alt="'.wpscSlug(stripslashes($wpsc_result['name'])).'" /></a>';$output .= apply_filters('wpsc_display_catalog_after_thumbnail', '');$output .='</li>';

                                            }                                
                                        break;
                                        case 2:
                                            if($usetext=='true' && $wpStoreCartOptions['displayTitle']=='true') {
                                                    $output .= '<li class="wpsc-title" id="wpscsort_2">'; $output .= apply_filters('wpsc_display_catalog_before_title', ''); $output.= '<a href="'.$permalink.'">'.stripslashes($wpsc_result['name']).'</a>'; $output .= apply_filters('wpsc_display_catalog_after_title', ''); $output.= '</li>';
                                            }                                    
                                        break;
                                        case 3:                
                                            if($displayIntroDescription=='true'){
                                                    $output .= '<li class="wpsc-intro" id="wpscsort_3">'; $output .= apply_filters('wpsc_display_catalog_before_intro', ''); $output.= stripslashes($wpsc_result['introdescription']); $output .= apply_filters('wpsc_display_catalog_after_intro', ''); $output.= '</li>';
                                            }
                                        break;
                                        case 4:                
                                            if($displayDescription=='true'){
                                                    $output .= '<li class="wpsc-description" id="wpscsort_4">'; $output .= apply_filters('wpsc_display_catalog_before_description', ''); $output.=stripslashes($wpsc_result['description']); $output .= apply_filters('wpsc_display_catalog_after_description', '').'</li>';
                                            }                                         
                                        break;
                                        case 5:                        
                                            if($wpStoreCartOptions['displaypriceonview']=='true'){
                                                if($wpsc_price_type == 'membership') {
                                                    //$output .= '<li class="wpsc-product-price">';
                                                    if($wpsc_membership_trial1_allow=='yes') {
                                                        $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\"><span class=\"wpsc-grid-price\">{$wpStoreCartOptions['trial_period_1']} {$wpStoreCartOptions['currency_symbol']}{$wpsc_membership_trial1_amount}{$wpStoreCartOptions['currency_symbol_right']} {$wpStoreCartOptions['for']} {$wpsc_membership_trial1_numberof} {$wpsc_membership_trial1_increment_display}</span></li>";
                                                    }
                                                    if($wpsc_membership_trial2_allow=='yes') {
                                                        $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\"><span class=\"wpsc-grid-price\">{$wpStoreCartOptions['trial_period_2']} {$wpStoreCartOptions['currency_symbol']}{$wpsc_membership_trial2_amount}{$wpStoreCartOptions['currency_symbol_right']} {$wpStoreCartOptions['for']} {$wpsc_membership_trial2_numberof} {$wpsc_membership_trial2_increment_display}</span></li>";
                                                    }
                                                    $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\"><span class=\"wpsc-grid-price\">{$wpStoreCartOptions['subscription_price']} {$wpStoreCartOptions['currency_symbol']}{$wpsc_membership_regular_amount}{$wpStoreCartOptions['currency_symbol_right']} {$wpStoreCartOptions['every']} {$wpsc_membership_regular_numberof} {$wpsc_membership_regular_increment_display}</span></li>";
                                                } else {
                                                    // Discount prices
                                                    if($wpsc_result['discountprice'] > 0) {
                                                        $theActualPrice = $wpsc_result['discountprice'];
                                                    } else {
                                                        $theActualPrice = $wpsc_result['price'];
                                                    }                                                                        

                                                    $wpscGroupDiscountPrices =  wpscGroupDiscountReturnPrice($wpsc_result['price'], $wpsc_result['discountprice'], $groupDiscount);
                                                    if(isset($wpscGroupDiscountPrices['discountprice'])) {
                                                        $wpsc_result['discountprice'] = $wpscGroupDiscountPrices['discountprice'];
                                                    }
                                                    if(isset($wpscGroupDiscountPrices['price'])) {
                                                        $wpsc_result['price'] = $wpscGroupDiscountPrices['price'];
                                                    } 

                                                    // Group discounts
                                                    if ($groupDiscount['can_have_discount']==true && $wpStoreCartOptions['gd_enable']=='true') {
                                                        $percentDiscount = $groupDiscount['discount_amount'] / 100;
                                                        $discountToSubtract = $theActualPrice * $percentDiscount;
                                                        if($groupDiscount['gd_saleprice']==true && $discountToSubtract > 0) {
                                                            $wpsc_result['discountprice'] = number_format($theActualPrice - $discountToSubtract, 2);
                                                        }                                                                      
                                                        $theActualPrice = number_format($theActualPrice - $discountToSubtract, 2);
                                                        if($wpsc_result['discountprice']==0) { 
                                                            $wpsc_result['price'] = $theActualPrice;
                                                        }
                                                    }   
                                                    // end group discount                                                                        

                                                    if($wpsc_result['discountprice']>0) {
                                                        if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) {
                                                            $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\">"; $output .= apply_filters('wpsc_display_catalog_before_price', ''); $output.= "<span class=\"wpsc-grid-price\"><strike class=\"wpsc-strike\">{$wpStoreCartOptions['currency_symbol']}{$wpStoreCartOptions['logged_out_price']}{$wpStoreCartOptions['currency_symbol_right']}</strike> {$wpStoreCartOptions['currency_symbol']}{$wpStoreCartOptions['logged_out_price']}{$wpStoreCartOptions['currency_symbol_right']}</span>"; $output .= apply_filters('wpsc_display_catalog_after_price', ''); $output.= "</li>";
                                                        } else {
                                                            $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\">"; $output .= apply_filters('wpsc_display_catalog_before_price', ''); $output.= "<span class=\"wpsc-grid-price\"><strike class=\"wpsc-strike\">{$wpStoreCartOptions['currency_symbol']}{$wpsc_result['price']}{$wpStoreCartOptions['currency_symbol_right']}</strike> {$wpStoreCartOptions['currency_symbol']}{$wpsc_result['discountprice']}{$wpStoreCartOptions['currency_symbol_right']}</span>"; $output .= apply_filters('wpsc_display_catalog_after_price', ''); $output.= "</li>";
                                                        }
                                                    } else {
                                                        if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) {
                                                            $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\">"; $output .= apply_filters('wpsc_display_catalog_before_price', ''); $output.= "<span class=\"wpsc-grid-price\">{$wpStoreCartOptions['currency_symbol']}{$wpStoreCartOptions['logged_out_price']}{$wpStoreCartOptions['currency_symbol_right']}</span>"; $output .= apply_filters('wpsc_display_catalog_after_price', ''); $output.= "</li>";
                                                        } else {
                                                            $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\">"; $output .= apply_filters('wpsc_display_catalog_before_price', ''); $output.= "<span class=\"wpsc-grid-price\">{$wpStoreCartOptions['currency_symbol']}{$wpsc_result['price']}{$wpStoreCartOptions['currency_symbol_right']}</span>"; $output .= apply_filters('wpsc_display_catalog_after_price', ''); $output.= "</li>";
                                                        }
                                                    }
                                                }
                                            }                                    
                                        break;
                                        case 6:                        
                                            if($wpStoreCartOptions['displayAddToCart']=='true'){
                                                if($wpsc_price_type == 'charge') {

                                                    // Discount prices
                                                    if($wpsc_result['discountprice'] > 0) {
                                                        $theActualPrice = $wpsc_result['discountprice'];
                                                    } else {
                                                        $theActualPrice = $wpsc_result['price'];
                                                    }                                                                        

                                                    $wpscGroupDiscountPrices =  wpscGroupDiscountReturnPrice($wpsc_result['price'], $wpsc_result['discountprice'], $groupDiscount);
                                                    if(isset($wpscGroupDiscountPrices['discountprice'])) {
                                                        $wpsc_result['discountprice'] = $wpscGroupDiscountPrices['discountprice'];
                                                    }
                                                    if(isset($wpscGroupDiscountPrices['price'])) {
                                                        $wpsc_result['price'] = $wpscGroupDiscountPrices['price'];
                                                    }                                                                       

                                                    // Flat rate shipping implmented here:
                                                    if($wpStoreCartOptions['flatrateshipping']=='all_single') {
                                                        $wpsc_result['shipping'] = $wpStoreCartOptions['flatrateamount'];
                                                    } elseif($wpStoreCartOptions['flatrateshipping']=='off' || $wpStoreCartOptions['flatrateshipping']=='all_global') {
                                                        $wpsc_result['shipping'] = '0.00';
                                                    }

                                                    $output .= '<li class="wpsc-mock-buttons" id="wpscsort_6">'; $output .= apply_filters('wpsc_display_catalog_before_addtocart', ''); $output.= wpscProductGetAddToCartButton($wpsc_result['primkey'], $theActualPrice); $output .= apply_filters('wpsc_display_catalog_after_addtocart', '');  $output .= apply_filters('wpsc_display_catalog_before_moreinfo', ''); $output.='<form action="'.get_permalink($wpsc_result['postid']).'" method="post"><button class="wpsc-button wpsc-moreinfo">'. __('More Info','wpstorecart').'</button></form>'; $output .= apply_filters('wpsc_display_catalog_after_moreinfo', ''); $output .='</li>';

                                                } elseif ($wpsc_price_type == 'membership' ) {
                                                        //$output .= $this->displaySubscriptionBuyNow($wpsc_result['primkey'], false);

                                                }
                                            }                                    
                                        break;
                                    }
                                }



                                $output .= '</ul>';
                                $output .= apply_filters('wpsc_display_catalog_end', '');
                            }


                    }

                    $output .= '<div class="wpsc-clear" style="clear:both;"></div>';
                    $output .= '<div class="wpsc-navigation">';

                    $comments_per_page = $quantity;
                    $page = isset( $_GET['wpscPage'] ) ? abs( (int) $_GET['wpscPage'] ) : 1;

                    $output .= paginate_links( array(
                        'base' =>  add_query_arg( 'wpscPage', '%#%', get_permalink( $wpStoreCartOptions['mainpage'] ) ),
                        'format' => '',
                        'prev_text' => __('&laquo;'),
                        'next_text' => __('&raquo;'),
                        'total' => ceil($total / $comments_per_page),
                        'current' => $page
                    ));

                    $output .= '</div>';

                    $output .= '<div class="wpsc-clear"></div>';
            }
        }
        
        $output .= '</div>';

        return $output;

    }
}

if (!function_exists('wpscProductPage')) {
    function wpscProductPage($primkey) {

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        
        global $wpsc_wordpress_upload_dir;
        $wpStoreCartProductSingleDesignerURL = $wpsc_wordpress_upload_dir['baseurl'].'/wpstorecart/themes/product/';    
      

        $output = '
        <script type="text/javascript">
            //<![CDATA[

            var wpscProductSingleDesignerCSSToLoad = "'.$wpStoreCartOptions['product_single_designer_css'].'";

            function wpscLoadProductSingleDesigner(fileToLoad) {
                try{
                    jQuery("#wpscLoadedProductSingleDesignerCSS").remove();
                } catch(err) {

                }
                jQuery("head").append("<link>");
                wpscDynamicCss = jQuery("head").children(":last");
                wpscDynamicCss.attr({
                    id: "wpscLoadedProductSingleDesignerCSS",
                    rel:  "stylesheet",
                    type: "text/css",
                    href: "'.$wpStoreCartProductSingleDesignerURL.'"+fileToLoad  
                });

            }

            wpscLoadProductSingleDesigner(wpscProductSingleDesignerCSSToLoad);

            //]]>
        </script>
        ';          
        
        if(!isset($_GET['wpStoreCartDesigner'])){ // User viewing the page         
            $output .= wpscProductGetPage($primkey);
        } else {
            $output .= wpscProductGetDummyPage();
        }
        
        return $output;
    }
}


if(!function_exists('wpscProductGetPrice')) {
    /**
    *
    * NOTE: Does not take group discounts in account.
    * 
    * @global object $wpdb
    * @param type $primkey 
    * @return mixed If $returnBothPrices is set to false, returns the lowest price of a product.  If $returnBothPrices is set to true, returns array of both prices
    */
    function wpscProductGetPrice($primkey, $returnBothPrices=false) {
        global $wpdb;

        $results = $wpdb->get_results("SELECT `price`, `discountprice` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$primkey}';", ARRAY_A);

        if(isset($results[0]['price'])) {
            if($returnBothPrices==false) {
                // Discount prices
                if($results[0]['discountprice'] > 0 && ($results[0]['discountprice'] < $results[0]['price'])) {
                    $theActualPrice = $results[0]['discountprice'];
                } else {
                    $theActualPrice = $results[0]['price'];
                } 

                return $theActualPrice;
            } else {
                $theActualPrice = array();
                $theActualPrice['discountprice'] = $results[0]['discountprice'];
                $theActualPrice['price'] = $results[0]['price'];   
                return $theActualPrice;
            }

        } else {
            return NULL;
        }

    }
}

if (!function_exists('wpscProductGetPage')) {
    /**
     *
     *  Individual product
     * 
     * @global object $wpdb
     * @global object $current_user
     * @param type $primkey
     * @return type 
     */
    function wpscProductGetPage($primkey) {
        global $wpdb, $current_user, $wpsc_results;

        $output = '';

        if(isset($primkey) && is_numeric($primkey)) {

            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');        

            if($primkey==0) { // 0 loads the Dummy product data
                $wpsc_results = array();
                $wpsc_results[0]['primkey'] = 0;
                $wpsc_results[0]['name'] = __('Dummy Product Title','wpstorecart');
                $wpsc_results[0]['introdescription'] = __('Here\'s the "Intro Description" for the Dummy Product, which is intended to give a brief overview of the product, and is displayed in many places.','wpstorecart');
                $wpsc_results[0]['description'] = __('This is the long "Description" for the Dummy Product, which is meant to describe and sell your product.  Here\'s some markup:','wpstorecart');
                $wpsc_results[0]['description'] .= '<br /><h1>'.__('Header H1','wpstorecart').'</h1>';
                $wpsc_results[0]['description'] .= '<ul><li>'.__('Ordered list item 1','wpstorecart').'</li><li>'.__('Ordered list item 2','wpstorecart').'</li><li>'.__('Ordered list item 3','wpstorecart').'</li></ul><br />';
                $wpsc_results[0]['description'] .= '<br /><strong>'.__('Here\'s some strong text.','wpstorecart').'</strong> '.__('and now some.','wpstorecart').' <i>'.__('italic text','wpstorecart').'</i> '.__('and even.','wpstorecart').' <u>'.__('underlined text.','wpstorecart').'</u>.  '.__('We also should checkout a link like','wpstorecart').' <a href="#">this link right here</a>.<br />';                
                $wpsc_results[0]['thumbnail'] = plugins_url().'/wpstorecart/images/photo.png';
                $wpsc_results[0]['price'] = 9999999999.99;
                $wpsc_results[0]['shipping'] = 0.00;
                $wpsc_results[0]['download'] = '';
                $wpsc_results[0]['tags'] = '';
                $wpsc_results[0]['category'] = 0;
                $wpsc_results[0]['useinventory'] = 1;
                $wpsc_results[0]['inventory'] = 999;
                $wpsc_results[0]['dateadded'] = 20120101;
                $wpsc_results[0]['postid'] = $wpStoreCartOptions['mainpage'];
                $wpsc_results[0]['timesviewed'] = 99999;
                $wpsc_results[0]['discountprice'] = 99.99;
                $wpsc_results[0]['producttype'] = '';        
                
                $output .= '<div id="wpsc-tabdlg" style="position:relative;top:1px;z-index:999999999;">
                                <ul id="wpsc-designer-tabbar">
                                    <li><a href="#wpsc-designer-window-tabs-1">'. __('Design','wpstorecart').'</a></li>
                                    <li><a href="#wpsc-designer-window-tabs-2">'. __('Colors &amp; FX','wpstorecart').'</a></li>               
                                    <li><a href="#wpsc-designer-window-tabs-3">'. __('Save &amp; Load','wpstorecart').'</a></li>
                                    <button id="wpsc-window-closer" style="float: right;"><span class="ui-icon ui-icon-closethick" style="margin-right: .3em;"></span></button>
                                </ul>
                                <div id="wpsc-designer-window-tabs-1" class="wpsc-designer-contents">
                                        <div>
                                            <center>
                                                <div class="wpsc-designer-contents-item">
                                                    <h2>'. __('Show or hide?','wpstorecart').'</h2>
                                                    <div class="wpsc-designer-multi-toggle">
                                                        <div class="wpsc-designer-toggle">'. __('Image?','wpstorecart').' <input id="wpsc-toggle-thumbnail" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-product-img\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-product-img\').css({\'display\':\'none\'});}" /></div>
                                                        <div class="wpsc-designer-toggle">'. __('Title?','wpstorecart').' <input id="wpsc-toggle-title" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-list-item-name\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-list-item-name\').css({\'display\':\'none\'});}" /></div>
                                                        <div class="wpsc-designer-toggle">'. __('Intro?','wpstorecart').' <input id="wpsc-toggle-intro" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-single-intro\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-single-intro\').css({\'display\':\'none\'});}" /></div>
                                                        <div class="wpsc-designer-toggle">'. __('Description?','wpstorecart').' <input id="wpsc-toggle-description" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-single-description\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-single-description\').css({\'display\':\'none\'});}" /></div>   
                                                        <div class="wpsc-designer-toggle">'. __('Crossed Out Price?','wpstorecart').' <input id="wpsc-toggle-strike" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-oldprice\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-oldprice\').css({\'display\':\'none\'});}" /></div>  
                                                        <div class="wpsc-designer-toggle">'. __('Price?','wpstorecart').' <input id="wpsc-toggle-price" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-list-item-price\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-list-item-price\').css({\'display\':\'none\'});}" /></div>   
                                                        <div class="wpsc-designer-toggle">'. __('Add to Cart button?','wpstorecart').' <input id="wpsc-toggle-addtocart" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-addtocart\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-addtocart\').css({\'display\':\'none\'});}" /></div>
                                                        <div class="wpsc-designer-toggle">'. __('Gallery?','wpstorecart').' <input id="wpsc-toggle-gallery" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-gallery\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-gallery\').css({\'display\':\'none\'});}" /></div>                                       
                                                        <div class="wpsc-designer-toggle">'. __('Qty Label?','wpstorecart').' <input id="wpsc-toggle-qtylabel" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-individualqtylabel\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-individualqtylabel\').css({\'display\':\'none\'});}" /></div>                                       
                                                        <div class="wpsc-designer-toggle">'. __('Quantity?','wpstorecart').' <input id="wpsc-toggle-qty" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-list-item-qty\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-list-item-qty\').css({\'display\':\'none\'});}" /></div>                                                                                                   
                                                        <div class="wpsc-designer-toggle">'. __('Inventory?','wpstorecart').' <input id="wpsc-toggle-inventory" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-list-item-inventory\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-list-item-inventory\').css({\'display\':\'none\'});}" /></div>                                                                                                   
                                                    </div>
                                                </div>      
                                                <div id="wpsc_ex1a" class="wpsc-designer-contents-item">
                                                    <h2>'. __('Main Image Size?','wpstorecart').'</h2>
                                                    <div id="wpsc_slider3"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:100, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                                </div>
                                                <div id="wpsc_ex2" class="wpsc-designer-contents-item">
                                                    <h2>'. __('Font Sizes?','wpstorecart'). '</h2>
                                                    '. __('Edit the font size of the','wpstorecart').' <select id="wpsc-select-edit-font" class="wpsc-designer-select">
                                                        <option value="title-font">'. __('title','wpstorecart').'</option>
                                                        <option value="intro-font">'. __('intro description','wpstorecart').'</option>
                                                        <option value="description-font">'. __('full description','wpstorecart').'</option>
                                                        <option value="strike-font">'. __('crossed out price','wpstorecart').'</option>
                                                        <option value="price-font">'. __('price','wpstorecart').'</option>
                                                        <option value="add-to-cart">'. __('add to cart button','wpstorecart').'</option>
                                                        <option value="more-info">'. __('meta box','wpstorecart').'</option>
                                                    </select>                                
                                                    <div id="wpsc_slider4"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:3, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                                </div> 
                                                <div id="wpsc_ex4" class="wpsc-designer-contents-item">
                                                    <h2>'. __('Border Sizes?','wpstorecart').'</h2>
                                                    '. __('Edit the size of the ','wpstorecart').' <select id="wpsc-select-edit-border" class="wpsc-designer-select">
                                                        <option value="product-border">'. __('product','wpstorecart').'</option>
                                                        <option value="add-to-cart">'. __('add to cart border','wpstorecart').'</option>
                                                        <option value="more-info">'. __('meta list','wpstorecart').'</option>
                                                    </select>                                
                                                    <div id="wpsc_slider5"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:1, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                                </div>    
                                                <div id="wpsc_ex5" class="wpsc-designer-contents-item">
                                                    <h2>'. __('Margin Sizes?','wpstorecart').'</h2>
                                                    '. __('Edit the ','wpstorecart').' <select id="wpsc-select-edit-margin" class="wpsc-designer-select">
                                                        <option value="product-margin-top">'. __('product margin top','wpstorecart').'</option>
                                                        <option value="product-margin-bottom">'. __('product margin bottom','wpstorecart').'</option>
                                                        <option value="product-margin-left">'. __('product margin left','wpstorecart').'</option>
                                                        <option value="product-margin-right">'. __('product margin right','wpstorecart').'</option>
                                                        <option value="add-to-cart-top">'. __('add to cart margin top','wpstorecart').'</option>
                                                        <option value="add-to-cart-bottom">'. __('add to cart margin bottom','wpstorecart').'</option>
                                                        <option value="add-to-cart-left">'. __('add to cart margin left','wpstorecart').'</option>
                                                        <option value="add-to-cart-right">'. __('add to cart margin right','wpstorecart').'</option>
                                                        <option value="more-info-top">'. __('more info margin top','wpstorecart').'</option>
                                                        <option value="more-info-bottom">'. __('more info margin bottom','wpstorecart').'</option>
                                                        <option value="more-info-left">'. __('more info margin left','wpstorecart').'</option>
                                                        <option value="more-info-right">'. __('more info margin right','wpstorecart').'</option>
                                                    </select>                                
                                                    <div id="wpsc_slider6"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:1, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                                </div>      
                                                <div id="wpsc_ex6" class="wpsc-designer-contents-item">
                                                    <h2>'. __('Padding Sizes?','wpstorecart').'</h2>
                                                    '. __('Edit the ','wpstorecart').' <select id="wpsc-select-edit-padding" class="wpsc-designer-select">
                                                        <option value="product-padding-top">'. __('product padding top','wpstorecart').'</option>
                                                        <option value="product-padding-bottom">'. __('product padding bottom','wpstorecart').'</option>
                                                        <option value="product-padding-left">'. __('product padding left','wpstorecart').'</option>
                                                        <option value="product-padding-right">'. __('product padding right','wpstorecart').'</option>
                                                        <option value="add-to-cart-top">'. __('add to cart padding top','wpstorecart').'</option>
                                                        <option value="add-to-cart-bottom">'. __('add to cart padding bottom','wpstorecart').'</option>
                                                        <option value="add-to-cart-left">'. __('add to cart padding left','wpstorecart').'</option>
                                                        <option value="add-to-cart-right">'. __('add to cart padding right','wpstorecart').'</option>
                                                        <option value="more-info-top">'. __('more info padding top','wpstorecart').'</option>
                                                        <option value="more-info-bottom">'. __('more info padding bottom','wpstorecart').'</option>
                                                        <option value="more-info-left">'. __('more info padding left','wpstorecart').'</option>
                                                        <option value="more-info-right">'. __('more info padding right','wpstorecart').'</option>
                                                    </select>                                
                                                    <div id="wpsc_slider7"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:1, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                                </div>                               
                                            </center>
                                        </div>
                                </div>
                                <div id="wpsc-designer-window-tabs-2"  class="wpsc-designer-contents">
                                        <div class="box">
                                            <center>                
                                            <div id="colorChooser">
                                                <h2>'. __('Colors','wpstorecart') . '</h2>

                                                '. __('Edit the color of the','wpstorecart') . ' <select id="wpsc-select-edit-color" class="wpsc-designer-select">
                                                    <option value="box-background">'. __('product background','wpstorecart') . '</option>
                                                    <option value="title-font">'. __('title','wpstorecart') . '</option>
                                                    <option value="box-border">'. __('product border','wpstorecart') . '</option>
                                                    <option value="addtocart_button">'. __('Add to Cart button','wpstorecart') . '</option>
                                                    <option value="addtocart_border">'. __('Add to Cart border','wpstorecart') . '</option>
                                                    <option value="addtocart_text">'. __('Add to Cart text','wpstorecart') . '</option>
                                                    <option value="moreinfo_button">'. __('meta box','wpstorecart') . '</option>
                                                    <option value="moreinfo_border">'. __('meta box border','wpstorecart') . '</option>
                                                    <option value="moreinfo_text">'. __('meta box text','wpstorecart') . '</option>
                                                </select>

                                                <div id="wpsc_ex3" style="display:inline-block;position:relative;">
                                                <div id="R" class="mb_slider {rangeColor:\'red\',negativeColor:\'#ffcc00\', startAt:100, grid:1, maxVal:255}" style="display:inline-block;"></div><br>
                                                <div id="G" class="mb_slider {rangeColor:\'lime\',negativeColor:\'#ffcc00\', startAt:45, grid:1, maxVal:255}" style="display:inline-block;"></div><br>
                                                <div id="B" class="mb_slider {rangeColor:\'blue\',negativeColor:\'#ffcc00\', startAt:74, grid:1, maxVal:255}" style="display:inline-block;"></div>
                                                </div>
                                                <br>

                                                <div style="clear:both;padding:20px;">
                                                <input id="colorValueHex" style="color:black;padding:10px; font:18px/16px Arial, sans-serif; width:130px;"></input><br><br>
                                                </div>

                                                <div class="wpsc-designer-contents-item">
                                                    <h2>'. __('Rounded Borders?','wpstorecart').'</h2>
                                                    <div class="wpsc-designer-multi-toggle">
                                                        <div class="wpsc-designer-toggle">'. __('Round Size:','wpstorecart').' <input type="text" value="10" id="wpsc-round-border-size" style="width:20px;" /> </div>
                                                        <div class="wpsc-designer-toggle">'. __('Product?','wpstorecart').' <input id="wpsc-toggle-round-product" checked="checked" type="checkbox"  onclick="if(jQuery(this).is(\':checked\')){wpscAnimateRoundedBordersOn(\'.wpsc-products\', jQuery(\'#wpsc-round-border-size\').val());}else{wpscAnimateRoundedBordersOff(\'.wpsc-products\', jQuery(\'#wpsc-round-border-size\').val());}" /></div>
                                                        <div class="wpsc-designer-toggle">'. __('Add to Cart button?','wpstorecart').' <input id="wpsc-toggle-round-addtocart" checked="checked" type="checkbox"  onclick="if(jQuery(this).is(\':checked\')){wpscAnimateRoundedBordersOn(\'.wpsc-addtocart\', jQuery(\'#wpsc-round-border-size\').val());}else{wpscAnimateRoundedBordersOff(\'.wpsc-addtocart\', jQuery(\'#wpsc-round-border-size\').val());}"/></div>
                                                        <div class="wpsc-designer-toggle">'. __('More Info button?','wpstorecart').' <input id="wpsc-toggle-round-moreinfo" checked="checked" type="checkbox"  onclick="if(jQuery(this).is(\':checked\')){wpscAnimateRoundedBordersOn(\'.wpsc-moreinfo\', jQuery(\'#wpsc-round-border-size\').val());}else{wpscAnimateRoundedBordersOff(\'.wpsc-moreinfo\', jQuery(\'#wpsc-round-border-size\').val());}" /></div>                                       
                                                    </div>
                                                </div> 

                                            </div>    
                                            </center>
                                        </div>                            
                                </div>
                                <div id="wpsc-designer-window-tabs-3" class="wpsc-designer-contents">
                                        <div style="text-align:left;">
                                            ';

                                $output .= '<span style="width:80px;min-width:80px;max-width:80px;">'. __('Save as:','wpstorecart').' </span><input name="wpscProductDesignerFilename" id="wpscProductDesignerFilename" value="wpstorecart.custom.css" style="width:200px;min-width:200px;max-width:200px;" />
                                <button onclick="wpscSaveProductDesigner2(jQuery(\'#wpscProductDesignerFilename\').val());return false;">'.__('Save','wpstorecart').'</button> <br />
                                <span style="width:80px;min-width:80px;max-width:80px;">'.__('Load:','wpstorecart').' </span><select id="wpscProductDesignerLoadFile" style="width:200px;min-width:200px;max-width:200px;">';

                                if ($wpscDirHandle = opendir($wpstorecart_upload_dir.'/themes/product/')) {
                                    while (false !== ($entry = readdir($wpscDirHandle))) {
                                        if ($entry != "." && $entry != "..") {
                                            $output .= "<option value=\"{$entry}\">{$entry}</option>\n";
                                        }
                                    }
                                    closedir($wpscDirHandle);
                                }

                                $output .= '</select> <button onclick="wpscLoadProductDesigner(jQuery(\'#wpscProductDesignerLoadFile\').val());jQuery(\'#wpscProductDesignerFilename\').val(jQuery(\'#wpscProductDesignerLoadFile\').val());wpscRefreshAllControls();return false;">'.__('Load >','wpstorecart').'</button><br />
                                '.__('Raw CSS:','wpstorecart').' <button onclick="wpscDumpStorefrontDesigner();return false;">'.__('Dump Current CSS Below:','wpstorecart').'</button>
                                <br />
                                <textarea style="width:100%;height:350px" id="wpsc-designer-textarea-css" name="wpsc-designer-textarea-css"></textarea>

                                </div>
                        </div>
                </div>                
            ';
                
                
            } else {
                $sql = "SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`={$primkey};";
                $wpsc_results = $wpdb->get_results( $sql , ARRAY_A );
            }
            
            if(isset($wpsc_results)) {
                
                
            $output .= '
            <script type="text/javascript">
                //<![CDATA[

                function wpscSaveProductDesigner2(wpscProductDesignerCSSFilename) {
                        var wpscProductDesignerCSS = "";
                        wpscProductDesignerCSS += " .wpsc-single-product {width:100%; margin: 0; padding: 0;} \n\ \n\ .wpsc-single-product img {border:none;}  \n\ \n\ .wpsc-thumbnail {width:50px;} \n\ \n\ #wpsc-product-info-sortable, #wpsc-product-info-sortable li { list-style: none; list-style-type: none; margin: 0px; padding: 0; } \n\ \n\ .wpsc-product-info-sortable li {margin: 0; padding: 0;} ";
                        wpscProductDesignerCSS += " \n\ \n\ ";
                        wpscProductDesignerCSS += wpscListCSSAttributes(".wpsc-single-product");
                        wpscProductDesignerCSS += wpscListCSSAttributes(".wpsc-product-img");
                        wpscProductDesignerCSS += wpscListCSSAttributes(".wpsc-product-info-sortable");
                        wpscProductDesignerCSS += wpscListCSSAttributes(".wpsc-addtocart"); 
                        wpscProductDesignerCSS += wpscListCSSAttributes(".wpsc-gallery"); 
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-list-item-name");
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-list-item-price");
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-list-item-inventory");  
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-list-item-qty");  
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-list-item-variations");  
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-product-price");  
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-single-intro");
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-single-description");
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-oldprice");
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-individualqtylabel");
                        var wpscProductDesignerElementOrder = jQuery(".wpsc-single-product").sortable("serialize");
                        jQuery.post("'. plugins_url().'/wpstorecart/wpstorecart/admin/php/saveindividualdesigner.php", { "wpscProductDesignerCSSFilename": wpscProductDesignerCSSFilename, "wpscProductDesignerCSS": wpscProductDesignerCSS, "wpscProductDesignerElementOrder":wpscProductDesignerElementOrder }, function(data) {

                        });
                }

                //]]>
            </script>
            ';                  
            

                wp_get_current_user();
                if ( 0 == $current_user->ID ) {
                    $theuser = 0; // Not logged in.
                } else {
                    $theuser = $current_user->ID;
                }

                // Group code
                $groupDiscount = wpscGroupDiscounts($wpsc_results[0]['category'], $theuser);
                if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {
                    break;
                }
                // end Group Code

                $statset = false;
                if($primkey == $wpsc_results[0]['primkey'] && $statset == false) {
                    wpscProductIncreaseProductViewedStatistic($primkey);
                    $statset = true;
                }                

                // Discount prices
                if($wpsc_results[0]['discountprice'] > 0) {
                    $theActualPrice = $wpsc_results[0]['discountprice'];
                } else {
                    $theActualPrice = $wpsc_results[0]['price'];
                }    
                
                $productListingOrder = wpscProductSingleReturnCurrentItemOrder();
                $productListItemOrder = wpscProductSingleReturnCurrentListItemOrder();

                // This code checks to see if we will be potentially displaying subscription products with either the price or add to cart button visible.  If so, we query each product for subscription information
                $wpsc_price_type = 'charge';
                $membership_value = '';
                if( ($primkey!=0 && $primkey!=NULL) && ($wpStoreCartOptions['displaypriceonview']=='true' || $wpStoreCartOptions['displayAddToCart']=='true')){
                    $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                    $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$primkey};";
                    $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                    if(isset($resultsMembership)) {
                        foreach ($resultsMembership as $pagg) {
                            $membership_primkey = $pagg['primkey'];
                            $membership_value = $pagg['value'];
                        }
                        if($membership_value!='') {
                            $theExploded = explode('||', $membership_value);
                            // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                            $wpsc_price_type = $theExploded[0];
                            $wpsc_membership_trial1_allow = $theExploded[1];
                            $wpsc_membership_trial2_allow = $theExploded[2];
                            $wpsc_membership_trial1_amount = $theExploded[3];
                            $wpsc_membership_trial2_amount = $theExploded[4];
                            $wpsc_membership_regular_amount = $theExploded[5];
                            $wpsc_membership_trial1_numberof = $theExploded[6];
                            $wpsc_membership_trial2_numberof = $theExploded[7];
                            $wpsc_membership_regular_numberof = $theExploded[8];
                            $wpsc_membership_trial1_increment = $theExploded[9];
                            $wpsc_membership_trial2_increment = $theExploded[10];
                            $wpsc_membership_regular_increment = $theExploded[11];
                            if($wpsc_membership_trial1_increment=='D'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['day'];}
                            if($wpsc_membership_trial2_increment=='D'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['day'];}
                            if($wpsc_membership_regular_increment=='D'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['day'];}
                            if($wpsc_membership_trial1_increment=='W'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['week'];}
                            if($wpsc_membership_trial2_increment=='W'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['week'];}
                            if($wpsc_membership_regular_increment=='W'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['week'];}
                            if($wpsc_membership_trial1_increment=='M'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['month'];}
                            if($wpsc_membership_trial2_increment=='M'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['month'];}
                            if($wpsc_membership_regular_increment=='M'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['month'];}
                            if($wpsc_membership_trial1_increment=='Y'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['year'];}
                            if($wpsc_membership_trial2_increment=='Y'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['year'];}
                            if($wpsc_membership_regular_increment=='Y'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['year'];}
                        }
                    }
                }                
                
                $output .= '<div class="wpsc-single-product">';
                
                $output .= apply_filters('wpsc_display_product_start', '');

                foreach($productListingOrder as $productListingOrderCurrent) { // THE SORTABLE COMPONENTS OF THE PRODUCT PAGE.
                    switch($productListingOrderCurrent) {
                        case 1: // THUMBNAIL
                            $output.='<div id="wpscsort_1" class="wpsc-product-element">';$output .= apply_filters('wpsc_display_product_before_thumbnail', '');if($wpStoreCartOptions['useimagebox']=='thickbox'){$output .='<a href="'.$wpsc_results[0]['thumbnail'].'" class="thickbox" title="'. htmlentities($wpsc_results[0]['name']. ' - ' . $wpsc_results[0]['introdescription']).'">';} $output .= '<img class="wpsc-product-img wpsc-product-img-'.$wpsc_results[0]['primkey'].'" src="'.$wpsc_results[0]['thumbnail'].'" alt="'.$wpsc_results[0]['name'].'" />';if($wpStoreCartOptions['useimagebox']=='thickbox'){$output .='</a>';}$output .= apply_filters('wpsc_display_product_after_thumbnail', '');$output .= '</div>';
                        break;
                        case 2: // PRODUCT INFO LIST (NAME, PRICE, ETC) 
                            $output.='<div id="wpscsort_2" class="wpsc-product-info wpsc-product-element><ul class="wpsc-product-info-sortable">';
                            
                            foreach($productListItemOrder as $productListItemOrderCurrent) { // THE PRODUCT INFO LIST ITSELF IS ALSO A SORTABLE LIST 
                                switch($productListItemOrderCurrent) {
                                    case 1:                            
                                        $output.= '<li id="wpsc-product-info-sort1" class="wpsc-list-item-name wpsc-list-item-name-'.$wpsc_results[0]['primkey'].'">'.$wpsc_results[0]['name'].'</li>';
                                    break;
                                    case 2:
                                        // Group discounts
                                        if ($groupDiscount['can_have_discount']==true && $wpStoreCartOptions['gd_enable']=='true') {
                                            $percentDiscount = $groupDiscount['discount_amount'] / 100;
                                            $discountToSubtract = $theActualPrice * $percentDiscount;
                                            if($groupDiscount['gd_saleprice']==true && $discountToSubtract > 0) {
                                                $wpsc_results[0]['discountprice'] = number_format($theActualPrice - $discountToSubtract, 2);
                                            }                                                                      
                                            $theActualPrice = number_format($theActualPrice - $discountToSubtract, 2);
                                            if($wpsc_results[0]['discountprice']==0) { 
                                                $wpsc_results[0]['price'] = $theActualPrice;
                                            }
                                        } 
                                        
                                        // Discount pricing
                                        if( ($wpsc_results[0]['discountprice'] > 0) && ($wpsc_results[0]['price'] > $wpsc_results[0]['discountprice'])  ) {
                                            $wpsDisplayPrice = $wpsc_results[0]['discountprice'];
                                        } else {
                                            $wpsDisplayPrice = NULL;
                                        }
                                        
                                        // If guest can't see prices
                                        if($wpStoreCartOptions['show_price_to_guests']=='false' && $theuser == 0) { 
                                            $wpsc_results[0]['price'] = $wpStoreCartOptions['logged_out_price'];
                                            $wpsc_results[0]['discountprice'] = $wpStoreCartOptions['logged_out_price'];
                                        }                                          
                                        
                                        $output.= '<li id="wpsc-product-info-sort2" class="wpsc-list-item-price">';

                                        if($wpsc_price_type == 'membership') {
                                            //$output .= '<li class="wpsc-product-price">';
                                            if($wpsc_membership_trial1_allow=='yes') {
                                                $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\"><span class=\"wpsc-grid-price\">{$wpStoreCartOptions['trial_period_1']} {$wpStoreCartOptions['currency_symbol']}{$wpsc_membership_trial1_amount}{$wpStoreCartOptions['currency_symbol_right']} {$wpStoreCartOptions['for']} {$wpsc_membership_trial1_numberof} {$wpsc_membership_trial1_increment_display}</span></li>";
                                            }
                                            if($wpsc_membership_trial2_allow=='yes') {
                                                $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\"><span class=\"wpsc-grid-price\">{$wpStoreCartOptions['trial_period_2']} {$wpStoreCartOptions['currency_symbol']}{$wpsc_membership_trial2_amount}{$wpStoreCartOptions['currency_symbol_right']} {$wpStoreCartOptions['for']} {$wpsc_membership_trial2_numberof} {$wpsc_membership_trial2_increment_display}</span></li>";
                                            }
                                            $output.="<li class=\"wpsc-product-price\" id=\"wpscsort_5\"><span class=\"wpsc-grid-price\">{$wpStoreCartOptions['subscription_price']} {$wpStoreCartOptions['currency_symbol']}{$wpsc_membership_regular_amount}{$wpStoreCartOptions['currency_symbol_right']} {$wpStoreCartOptions['every']} {$wpsc_membership_regular_numberof} {$wpsc_membership_regular_increment_display}</span></li>";
                                        } else {
                                        
                                            if($wpsDisplayPrice!=NULL) {
                                                $output .= '
                                                <div class="wpsc-oldprice wpsc-oldprice-'.$wpsc_results[0]['primkey'].'"><strike>'.$wpStoreCartOptions['currency_symbol'].$wpsc_results[0]['price'].$wpStoreCartOptions['currency_symbol_right'].'</strike></div>
                                                <div class="wpsc-price wpsc-price-'.$wpsc_results[0]['primkey'].'">'.$wpStoreCartOptions['currency_symbol'].$wpsc_results[0]['discountprice'].$wpStoreCartOptions['currency_symbol_right'].'</div>';                                        
                                            } else {
                                                $output .= '<div class="wpsc-price wpsc-price-'.$wpsc_results[0]['primkey'].'">'.$wpStoreCartOptions['currency_symbol'].$wpsc_results[0]['price'].$wpStoreCartOptions['currency_symbol_right'].'</div>';                                        
                                            }
                                            
                                        }

                                        $output .= '</li>';
                                    break;
                                    case 3:
                                        $output.= '
                                        <li id="wpsc-product-info-sort3" class="wpsc-list-item-inventory">';
                                        if($wpsc_results[0]['useinventory']==1 && $wpsc_results[0]['inventory'] > 0) {
                                            $output.= $wpStoreCartOptions['available_in_stock'] .' '. $wpsc_results[0]['inventory'];
                                        } 
                                        if($wpsc_results[0]['useinventory']==1 && $wpsc_results[0]['inventory'] <= 0) {
                                            $output.= $wpStoreCartOptions['out_of_stock'] .' '. $wpsc_results[0]['inventory'];
                                        }                                         
                                        $output .= '</li>';
                                    break;                                    
                                    case 4:
                                        //check inventory amount:
                                        $modified_js = null;
                                        $modified_js_final = null;
                                        if($wpsc_results[0]['useinventory']==1) {                                        
                                            $modified_js = 'if (jQuery(this).val() > '.$wpsc_results[0]['inventory'] .') {alert(\''.__('You have attempted to purchase more items than we currently have in stock.  We have adjusted the quantity to maximum available.  Please try again.', 'wpstorecart').'\');jQuery(this).val('.$wpsc_results[0]['inventory'].');} else { ';
                                            $modified_js_final = '}';
                                        }
                                        
                                        $output.= '
                                        <li id="wpsc-product-info-sort4" class="wpsc-list-item-qty">
                                            <label class="wpsc-individualqtylabel">
                                            '.$wpStoreCartOptions['qty'].'
                                            </label>   
                                            <input class="wpsc-individualqty" id="wpsc-individualqty-'.$wpsc_results[0]['primkey'].'" type="text" size="3" value="1" onchange="'.$modified_js.'jQuery(\'#wpstorecart-item-qty-'.$wpsc_results[0]['primkey'].'\').val(jQuery(this).val());'.$modified_js_final.'">
                                        </li>';
                                    break;
                                    case 5:                                
                                        $output.= '
                                        <li id="wpsc-product-info-sort5" class="wpsc-list-item-variation" >
                                            '.wpscProductGetVariationsSelection($primkey).'
                                            '.wpscProductGetFields($primkey).'
                                        </li>';
                                    break;
                                }
                            }
                            $output .= ' </ul></div>';
                        break;
                        case 3:
                            $output.='<div id="wpscsort_3" class="wpsc-product-element">';$output .= apply_filters('wpsc_display_product_before_addtocart', '');$output.= wpscProductGetAddToCartButton($primkey);$output .= apply_filters('wpsc_display_product_after_addtocart', '');$output .= '</div>';
                        break;
                        case 4:                
                            $output.='<div id="wpscsort_4" class="wpsc-product-element">';$output .= apply_filters('wpsc_display_product_before_picture_gallery', '');$output.= wpscProductGetPictureGallery($primkey);$output .= apply_filters('wpsc_display_product_after_picture_gallery', '');$output .= '</div>';
                        break;
                        case 5:                
                            $output.='<div id="wpscsort_5" class="wpsc-product-element">';$output .= apply_filters('wpsc_display_product_before_single_intro', '');$output .='<div class="wpsc-single-intro wpsc-single-intro-'.$wpsc_results[0]['primkey'].'">';$output.= stripslashes($wpsc_results[0]['introdescription']);$output .= '</div>';$output .= apply_filters('wpsc_display_product_after_single_intro', '');$output .='</div>';
                        break;
                        case 6:                        
                            $output.='<div id="wpscsort_6" class="wpsc-product-element">';$output .= apply_filters('wpsc_display_product_before_single_description', '');$output .='<div class="wpsc-single-description wpsc-single-description-'.$wpsc_results[0]['primkey'].'">';$output.= stripslashes($wpsc_results[0]['description']);$output .= '</div>';$output .= apply_filters('wpsc_display_product_after_single_description', '');$output .='</div>';
                        break;
                        case 7:                        
                            $output.='<div id="wpscsort_7" class="wpsc-product-element">';$output .= apply_filters('wpsc_display_product_before_accessories', '');$output.= wpscProductGetProductAccessories($primkey);$output .= apply_filters('wpsc_display_product_after_accessories', '');$output .= '</div>';
                        break;
                    }
                }                
                
                $output .= apply_filters('wpsc_display_product_end', '');

                $output .= '</div><!-- End .wpsc-single-product -->';
            } else {
                    $output .= '<div class="wpsc-error">'.__('This product has been removed, or does not exist.', 'wpstorecart').'</div>';
            }

        } else {
                $output .= __('wpStoreCart did not like the product primkey!  The primkey field contained non-numeric data. Please fix your page or consult the wpStoreCart documentation for help.', 'wpstorecart');
        }    

        return $output;
    }
}


if (!function_exists('wpscProductGetDummyPage')) {
    function wpscProductGetDummyPage() {
        return wpscProductGetPage(0);
    }
}


if (!function_exists('wpscProductMainPage')) {
    /**
        * Provides the main page, as well as the realtime, in-theme CSS editing of it
        * 
        * @global type $wpstorecart_upload_dir
        * @global type $wpsc_wordpress_upload_dir
        * @return string 
        */
    function wpscProductMainPage($thecategory=null, $onlycat=false) {
        global $wpstorecart_upload_dir;

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        
        global $wpsc_wordpress_upload_dir;
        $wpStoreCartProductDesignerURL = $wpsc_wordpress_upload_dir['baseurl'].'/wpstorecart/themes/main/';    
      

        $output = '
        <script type="text/javascript">
            //<![CDATA[

            var wpscProductDesignerCSSToLoad = "'.$wpStoreCartOptions['product_designer_css'].'";

            function wpscLoadProductDesigner(fileToLoad) {
                try{
                    jQuery("#wpscLoadedProductDesignerCSS").remove();
                } catch(err) {

                }
                jQuery("head").append("<link>");
                wpscDynamicCss = jQuery("head").children(":last");
                wpscDynamicCss.attr({
                    id: "wpscLoadedProductDesignerCSS",
                    rel:  "stylesheet",
                    type: "text/css",
                    href: "'.$wpStoreCartProductDesignerURL.'"+fileToLoad  
                });

            }

            wpscLoadProductDesigner(wpscProductDesignerCSSToLoad);

            //]]>
        </script>
        ';          
        
        if(!isset($_GET['wpStoreCartDesigner'])){ // User viewing the page 
            if($onlycat==false) {
                $output .= wpscProductGetCatalog($wpStoreCartOptions['itemsperpage'], $thecategory, $wpStoreCartOptions['frontpageDisplays']);
            } else {
                $output .= wpscProductGetCatalog($wpStoreCartOptions['itemsperpage'], $thecategory, 'List all categories');
            }
        } else { // Admin editing the layout          

            wpscCheckAdminPermissions();

            $output .= '
            <script type="text/javascript">
                //<![CDATA[

                function wpscSaveProductDesigner(wpscProductDesignerCSSFilename) {
                        var wpscProductDesignerCSS = "";
                        wpscProductDesignerCSS += " #wpsc-grid {width:100%; margin: 0; padding: 0;} \n\ \n\ #wpsc-grid img {border:none;}  \n\ \n\ .wpsc-thumbnail {width:50px;} \n\ \n\ #wpsc-grid, #wpsc-grid li { list-style: none; list-style-type: none; margin: 0px; padding: 0; } \n\ \n\ .wpsc-products li {margin: 0; padding: 0;} \n\ \n\ .wpsc-products {overflow:hidden; border:1px solid #DEDEDE; background-color:#EEEEEE; list-style-type: none;  margin: 5px 5px 5px 5px; padding: 10px; float: left; width: 25%; height: 100px; font-size: 0.8em; text-align: center; } \n\ \n\ .wpsc-title {font-size:14px;} \n\ \n\ .wpsc-addtocart {font-size:12px; background-color:#FFFFFF; color: #000000; border-color:#000000; border-width: 1px;} \n\ \n\ .wpsc-moreinfo {font-size:12px; background-color:#FFFFFF; color: #000000; border-color:#000000; border-width: 1px;} \n\ \n\ .wpsc-product-price, .wpsc-strike {font-size:12px;}";
                        wpscProductDesignerCSS += " \n\ \n\ ";
                        wpscProductDesignerCSS += wpscListCSSAttributes("ul.wpsc-products");
                        wpscProductDesignerCSS +=wpscListCSSAttributes(".wpsc-products");
                        wpscProductDesignerCSS +=wpscListCSSAttributes(".wpsc-title");
                        wpscProductDesignerCSS +=wpscListCSSAttributes(".wpsc-addtocart");
                        wpscProductDesignerCSS +=wpscListCSSAttributes(".wpsc-moreinfo");  
                        wpscProductDesignerCSS +=wpscListCSSAttributes(".wpsc-thumbnail");  
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-intro");  
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-description");  
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-product-price");  
                        wpscProductDesignerCSS +=wpscListTextCSSAttributes(".wpsc-strike");
                        var wpscProductDesignerElementOrder = jQuery(".wpsc-products").sortable("serialize");
                        jQuery.post("'. plugins_url().'/wpstorecart/wpstorecart/admin/php/saveproductdesigner.php", { "wpscProductDesignerCSSFilename": wpscProductDesignerCSSFilename, "wpscProductDesignerCSS": wpscProductDesignerCSS, "wpscProductDesignerElementOrder":wpscProductDesignerElementOrder }, function(data) {

                        });
                }

                //]]>
            </script>
            ';            


            $output .= '
                '.wpscProductGetDummyProducts().'

                <div id="wpsc-tabdlg" style="position:relative;top:1px;z-index:999999999;">
                        <ul id="wpsc-designer-tabbar">
                            <li><a href="#wpsc-designer-window-tabs-1">'. __('Design','wpstorecart').'</a></li>
                            <li><a href="#wpsc-designer-window-tabs-2">'. __('Colors &amp; FX','wpstorecart').'</a></li>               
                            <li><a href="#wpsc-designer-window-tabs-3">'. __('Save &amp; Load','wpstorecart').'</a></li>
                            <button id="wpsc-window-closer" style="float: right;"><span class="ui-icon ui-icon-closethick" style="margin-right: .3em;"></span></button>
                        </ul>
                        <div id="wpsc-designer-window-tabs-1" class="wpsc-designer-contents">
                                <div>
                                    <center>
                                        <div class="wpsc-designer-contents-item">
                                            <h2>'. __('Show or hide?','wpstorecart').'</h2>
                                            <div class="wpsc-designer-multi-toggle">
                                                <div class="wpsc-designer-toggle">'. __('Image?','wpstorecart').' <input id="wpsc-toggle-thumbnail" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-thumbnail\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-thumbnail\').css({\'display\':\'none\'});}" /></div>
                                                <div class="wpsc-designer-toggle">'. __('Title?','wpstorecart').' <input id="wpsc-toggle-title" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-title\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-title\').css({\'display\':\'none\'});}" /></div>
                                                <div class="wpsc-designer-toggle">'. __('Intro?','wpstorecart').' <input id="wpsc-toggle-intro" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-intro\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-intro\').css({\'display\':\'none\'});}" /></div>
                                                <div class="wpsc-designer-toggle">'. __('Description?','wpstorecart').' <input id="wpsc-toggle-description" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-description\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-description\').css({\'display\':\'none\'});}" /></div>   
                                                <div class="wpsc-designer-toggle">'. __('Crossed Out Price?','wpstorecart').' <input id="wpsc-toggle-strike" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-strike\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-strike\').css({\'display\':\'none\'});}" /></div>  
                                                <div class="wpsc-designer-toggle">'. __('Price?','wpstorecart').' <input id="wpsc-toggle-price" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-product-price\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-product-price\').css({\'display\':\'none\'});}" /></div>   
                                                <div class="wpsc-designer-toggle">'. __('Add to Cart button?','wpstorecart').' <input id="wpsc-toggle-addtocart" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-addtocart\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-addtocart\').css({\'display\':\'none\'});}" /></div>
                                                <div class="wpsc-designer-toggle">'. __('More Info button?','wpstorecart').' <input id="wpsc-toggle-moreinfo" type="checkbox" checked="checked" onclick="if(jQuery(this).is(\':checked\')){jQuery(\'.wpsc-moreinfo\').css({\'display\':\'inline\'});} else {jQuery(\'.wpsc-moreinfo\').css({\'display\':\'none\'});}" /></div>                                       
                                            </div>
                                        </div>      
                                        <div id="wpsc_ex0" class="wpsc-designer-contents-item">
                                            <h2>'. __('Product/Category Width?','wpstorecart').'</h2>
                                            <div id="wpsc_slider1"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:100, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                        </div>
                                        <div id="wpsc_ex1" class="wpsc-designer-contents-item">
                                            <h2>'. __('Product/Category Height?','wpstorecart').'</h2>
                                            <div id="wpsc_slider2"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:100, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                        </div>
                                        <div id="wpsc_ex1a" class="wpsc-designer-contents-item">
                                            <h2>'. __('Thumbnail Size?','wpstorecart').'</h2>
                                            <div id="wpsc_slider3"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:100, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                        </div>
                                        <div id="wpsc_ex2" class="wpsc-designer-contents-item">
                                            <h2>'. __('Font Sizes?','wpstorecart'). '</h2>
                                            '. __('Edit the font size of the','wpstorecart').' <select id="wpsc-select-edit-font" class="wpsc-designer-select">
                                                <option value="title-font">'. __('title','wpstorecart').'</option>
                                                <option value="intro-font">'. __('intro description','wpstorecart').'</option>
                                                <option value="description-font">'. __('full description','wpstorecart').'</option>
                                                <option value="strike-font">'. __('crossed out price','wpstorecart').'</option>
                                                <option value="price-font">'. __('price','wpstorecart').'</option>
                                                <option value="add-to-cart">'. __('add to cart button','wpstorecart').'</option>
                                                <option value="more-info">'. __('more info button','wpstorecart').'</option>
                                            </select>                                
                                            <div id="wpsc_slider4"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:3, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                        </div> 
                                        <div id="wpsc_ex4" class="wpsc-designer-contents-item">
                                            <h2>'. __('Border Sizes?','wpstorecart').'</h2>
                                            '. __('Edit the size of the ','wpstorecart').' <select id="wpsc-select-edit-border" class="wpsc-designer-select">
                                                <option value="product-border">'. __('product border','wpstorecart').'</option>
                                                <option value="add-to-cart">'. __('add to cart border','wpstorecart').'</option>
                                                <option value="more-info">'. __('more info border','wpstorecart').'</option>
                                            </select>                                
                                            <div id="wpsc_slider5"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:1, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                        </div>    
                                        <div id="wpsc_ex5" class="wpsc-designer-contents-item">
                                            <h2>'. __('Margin Sizes?','wpstorecart').'</h2>
                                            '. __('Edit the ','wpstorecart').' <select id="wpsc-select-edit-margin" class="wpsc-designer-select">
                                                <option value="product-margin-top">'. __('product margin top','wpstorecart').'</option>
                                                <option value="product-margin-bottom">'. __('product margin bottom','wpstorecart').'</option>
                                                <option value="product-margin-left">'. __('product margin left','wpstorecart').'</option>
                                                <option value="product-margin-right">'. __('product margin right','wpstorecart').'</option>
                                                <option value="add-to-cart-top">'. __('add to cart margin top','wpstorecart').'</option>
                                                <option value="add-to-cart-bottom">'. __('add to cart margin bottom','wpstorecart').'</option>
                                                <option value="add-to-cart-left">'. __('add to cart margin left','wpstorecart').'</option>
                                                <option value="add-to-cart-right">'. __('add to cart margin right','wpstorecart').'</option>
                                                <option value="more-info-top">'. __('more info margin top','wpstorecart').'</option>
                                                <option value="more-info-bottom">'. __('more info margin bottom','wpstorecart').'</option>
                                                <option value="more-info-left">'. __('more info margin left','wpstorecart').'</option>
                                                <option value="more-info-right">'. __('more info margin right','wpstorecart').'</option>
                                            </select>                                
                                            <div id="wpsc_slider6"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:1, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                        </div>      
                                        <div id="wpsc_ex6" class="wpsc-designer-contents-item">
                                            <h2>'. __('Padding Sizes?','wpstorecart').'</h2>
                                            '. __('Edit the ','wpstorecart').' <select id="wpsc-select-edit-padding" class="wpsc-designer-select">
                                                <option value="product-padding-top">'. __('product padding top','wpstorecart').'</option>
                                                <option value="product-padding-bottom">'. __('product padding bottom','wpstorecart').'</option>
                                                <option value="product-padding-left">'. __('product padding left','wpstorecart').'</option>
                                                <option value="product-padding-right">'. __('product padding right','wpstorecart').'</option>
                                                <option value="add-to-cart-top">'. __('add to cart padding top','wpstorecart').'</option>
                                                <option value="add-to-cart-bottom">'. __('add to cart padding bottom','wpstorecart').'</option>
                                                <option value="add-to-cart-left">'. __('add to cart padding left','wpstorecart').'</option>
                                                <option value="add-to-cart-right">'. __('add to cart padding right','wpstorecart').'</option>
                                                <option value="more-info-top">'. __('more info padding top','wpstorecart').'</option>
                                                <option value="more-info-bottom">'. __('more info padding bottom','wpstorecart').'</option>
                                                <option value="more-info-left">'. __('more info padding left','wpstorecart').'</option>
                                                <option value="more-info-right">'. __('more info padding right','wpstorecart').'</option>
                                            </select>                                
                                            <div id="wpsc_slider7"  class="mb_slider {rangeColor:\'#86A3BD\', startAt:1, grid:1}" style="display:inline-block;*display:inherit;"></div>
                                        </div>                               
                                    </center>
                                </div>
                        </div>
                        <div id="wpsc-designer-window-tabs-2"  class="wpsc-designer-contents">
                                <div class="box">
                                    <center>                
                                    <div id="colorChooser">
                                        <h2>'. __('Colors','wpstorecart') . '</h2>

                                        '. __('Edit the color of the','wpstorecart') . ' <select id="wpsc-select-edit-color" class="wpsc-designer-select">
                                            <option value="box-background">'. __('product background','wpstorecart') . '</option>
                                            <option value="title-font">'. __('title','wpstorecart') . '</option>
                                            <option value="box-border">'. __('product border','wpstorecart') . '</option>
                                            <option value="addtocart_button">'. __('Add to Cart button','wpstorecart') . '</option>
                                            <option value="addtocart_border">'. __('Add to Cart border','wpstorecart') . '</option>
                                            <option value="addtocart_text">'. __('Add to Cart text','wpstorecart') . '</option>
                                            <option value="moreinfo_button">'. __('More Info button','wpstorecart') . '</option>
                                            <option value="moreinfo_border">'. __('More Info border','wpstorecart') . '</option>
                                            <option value="moreinfo_text">'. __('More Info text','wpstorecart') . '</option>
                                        </select>

                                        <div id="wpsc_ex3" style="display:inline-block;position:relative;">
                                        <div id="R" class="mb_slider {rangeColor:\'red\',negativeColor:\'#ffcc00\', startAt:100, grid:1, maxVal:255}" style="display:inline-block;"></div><br>
                                        <div id="G" class="mb_slider {rangeColor:\'lime\',negativeColor:\'#ffcc00\', startAt:45, grid:1, maxVal:255}" style="display:inline-block;"></div><br>
                                        <div id="B" class="mb_slider {rangeColor:\'blue\',negativeColor:\'#ffcc00\', startAt:74, grid:1, maxVal:255}" style="display:inline-block;"></div>
                                        </div>
                                        <br>

                                        <div style="clear:both;padding:20px;">
                                        <input id="colorValueHex" style="color:black;padding:10px; font:18px/16px Arial, sans-serif; width:130px;"></input><br><br>
                                        </div>

                                        <div class="wpsc-designer-contents-item">
                                            <h2>'. __('Rounded Borders?','wpstorecart').'</h2>
                                            <div class="wpsc-designer-multi-toggle">
                                                <div class="wpsc-designer-toggle">'. __('Round Size:','wpstorecart').' <input type="text" value="10" id="wpsc-round-border-size" style="width:20px;" /> </div>
                                                <div class="wpsc-designer-toggle">'. __('Product?','wpstorecart').' <input id="wpsc-toggle-round-product" checked="checked" type="checkbox"  onclick="if(jQuery(this).is(\':checked\')){wpscAnimateRoundedBordersOn(\'.wpsc-products\', jQuery(\'#wpsc-round-border-size\').val());}else{wpscAnimateRoundedBordersOff(\'.wpsc-products\', jQuery(\'#wpsc-round-border-size\').val());}" /></div>
                                                <div class="wpsc-designer-toggle">'. __('Add to Cart button?','wpstorecart').' <input id="wpsc-toggle-round-addtocart" checked="checked" type="checkbox"  onclick="if(jQuery(this).is(\':checked\')){wpscAnimateRoundedBordersOn(\'.wpsc-addtocart\', jQuery(\'#wpsc-round-border-size\').val());}else{wpscAnimateRoundedBordersOff(\'.wpsc-addtocart\', jQuery(\'#wpsc-round-border-size\').val());}"/></div>
                                                <div class="wpsc-designer-toggle">'. __('More Info button?','wpstorecart').' <input id="wpsc-toggle-round-moreinfo" checked="checked" type="checkbox"  onclick="if(jQuery(this).is(\':checked\')){wpscAnimateRoundedBordersOn(\'.wpsc-moreinfo\', jQuery(\'#wpsc-round-border-size\').val());}else{wpscAnimateRoundedBordersOff(\'.wpsc-moreinfo\', jQuery(\'#wpsc-round-border-size\').val());}" /></div>                                       
                                            </div>
                                        </div> 

                                    </div>    
                                    </center>
                                </div>                            
                        </div>
                        <div id="wpsc-designer-window-tabs-3" class="wpsc-designer-contents">
                                <div style="text-align:left;">
                                    ';

                        $output .= '<span style="width:80px;min-width:80px;max-width:80px;">'. __('Save as:','wpstorecart').' </span><input name="wpscProductDesignerFilename" id="wpscProductDesignerFilename" value="wpstorecart.custom.css" style="width:200px;min-width:200px;max-width:200px;" />
                        <button onclick="wpscSaveProductDesigner(jQuery(\'#wpscProductDesignerFilename\').val());return false;">'.__('Save','wpstorecart').'</button> <br />
                        <span style="width:80px;min-width:80px;max-width:80px;">'.__('Load:','wpstorecart').' </span><select id="wpscProductDesignerLoadFile" style="width:200px;min-width:200px;max-width:200px;">';

                        if ($wpscDirHandle = opendir($wpstorecart_upload_dir.'/themes/main/')) {
                            while (false !== ($entry = readdir($wpscDirHandle))) {
                                if ($entry != "." && $entry != "..") {
                                    $output .= "<option value=\"{$entry}\">{$entry}</option>\n";
                                }
                            }
                            closedir($wpscDirHandle);
                        }

                        $output .= '</select> <button onclick="wpscLoadProductDesigner(jQuery(\'#wpscProductDesignerLoadFile\').val());jQuery(\'#wpscProductDesignerFilename\').val(jQuery(\'#wpscProductDesignerLoadFile\').val());wpscRefreshAllControls();return false;">'.__('Load >','wpstorecart').'</button><br />
                        '.__('Raw CSS:','wpstorecart').' <button onclick="wpscDumpStorefrontDesigner();return false;">'.__('Dump Current CSS Below:','wpstorecart').'</button>
                        <br />
                        <textarea style="width:100%;height:350px" id="wpsc-designer-textarea-css" name="wpsc-designer-textarea-css"></textarea>

                    </div>
            </div>
    </div>                
';

            
        }
        
        return $output;
    }
}



if(!function_exists('wpscProductGetDummyProducts')) {
    /**
     * Returns dummy products for the sake of styling
     * @return string
     */
    function wpscProductGetDummyProducts($num = 12) {
        $output = '';
        $counter = 0;
        $productListingOrder = wpscProductReturnCurrentGridItemOrder();
        $permalink = '#';
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        
        $output .= '<div id="wpsc-grid">';
        
        while ($counter < $num) {
            $output.= '<ul class="wpsc-products">';
            foreach($productListingOrder as $productListingOrderCurrent) {
                switch($productListingOrderCurrent) {
                    case 1:
                        $output.= '<li class="wpsc-thumbnail-handle" id="wpscsort_1"><a href="'.$permalink.'"><img class="wpsc-thumbnail" src="'. plugins_url() .'/wpstorecart/images/photo.png" alt="" /></a></li>';
                    break;
                    case 2:
                        $output.= '<li class="wpsc-title" id="wpscsort_2"><a href="'.$permalink.'">'. __('Product Title','wpstorecart').'</a></li>';
                    break;
                    case 3:                
                        $output.= '<li class="wpsc-intro" id="wpscsort_3">'. __('Introduction Description','wpstorecart').'</li>';
                    break;
                    case 4:                
                        $output.= '<li class="wpsc-description" id="wpscsort_4">'. __('Full description goes here. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Nullam semper augue vel neque cursus mattis. Cum sociis natoque penatibus et magnis dis parturient montes, nascetur ridiculus mus. Nullam scelerisque commodo vulputate. Proin odio augue, facilisis at pulvinar et, commodo vitae odio. Vivamus commodo sem quis mauris consequat at consequat ante varius. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Proin suscipit vulputate pretium. Morbi venenatis ligula in dolor pharetra varius. Nunc gravida ornare imperdiet. Aliquam consectetur sodales aliquet. Phasellus tellus turpis, pharetra eu sodales sit amet, elementum nec quam. Cras lectus nisl, commodo a molestie quis, fermentum ac ipsum. Quisque sed sem velit.
                        <br /><br />
                        Aliquam erat volutpat. Etiam varius volutpat consectetur. Donec sagittis, lectus eget cursus tristique, diam enim sollicitudin odio, eu ornare quam augue quis libero. Pellentesque sit amet ipsum eros, et dapibus velit. Cras sed orci eget risus viverra porttitor. Nullam luctus ullamcorper enim vel tincidunt. Donec ipsum odio, pulvinar interdum imperdiet eu, iaculis eget erat. Nullam id convallis arcu. Duis metus nibh, fringilla sed pretium vel, dapibus id mauris. Suspendisse luctus leo nec nisl faucibus convallis. Sed pretium ultricies dui ac suscipit. Proin aliquam sagittis neque, eu condimentum ipsum egestas sit amet.
                        <br /><br />
                        Quisque vitae imperdiet mi. Etiam vel nisi a nisl porttitor lobortis. Class aptent taciti sociosqu ad litora torquent per conubia nostra, per inceptos himenaeos. Suspendisse elementum, metus et pharetra semper, nisi dui volutpat quam, at tempor sem mauris faucibus libero. Aenean odio lacus, mattis non fringilla nec, commodo id risus. Sed laoreet tellus vitae lectus convallis a varius turpis adipiscing. Curabitur at adipiscing ante. Lorem ipsum dolor sit amet, consectetur adipiscing elit. Maecenas augue arcu, eleifend eget commodo eu, condimentum dapibus justo. Phasellus placerat felis sit amet lectus pretium eget accumsan elit varius. Praesent eget eros eget nisi sagittis scelerisque. Ut condimentum, turpis sit amet porttitor rutrum, purus urna ultricies lacus, ac accumsan enim nunc vel ante. Nulla facilisi. Cras ultricies suscipit risus, eget luctus arcu venenatis at. Pellentesque vel quam lorem, nec volutpat libero. ','wpstorecart').'</li>';
                    break;
                    case 5:                        
                        $output.= '<li class="wpsc-product-price" id="wpscsort_5"><span class="wpsc-grid-price"><strike class="wpsc-strike">'.$wpStoreCartOptions['currency_symbol'].'x.xx'.$wpStoreCartOptions['currency_symbol_right'].'</strike> '.$wpStoreCartOptions['currency_symbol'].'x.xx'.$wpStoreCartOptions['currency_symbol_right'].'</span></li>';
                    break;
                    case 6:                        
                        $output.= '<li class="wpsc-mock-buttons" id="wpscsort_6"><button class="wpsc-button wpsc-addtocart">'. __('Add to Cart','wpstorecart').'</button><button class="wpsc-button wpsc-moreinfo">'. __('More Info','wpstorecart').'</button></li>';
                    break;
                }
            }
            $output.= '</ul>
';            
            $counter++;
        }
        $output.= '</div>';
        return $output;
    }
}

if (!function_exists('wpscProductReturnCurrentGridItemOrder')) {
    function wpscProductReturnCurrentGridItemOrder() {

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        $productListingOrder = array();
        $productListingOrder[0]=1;
        $productListingOrder[1]=2;
        $productListingOrder[2]=3;
        $productListingOrder[3]=4;
        $productListingOrder[4]=5;
        $productListingOrder[5]=6;        
        
        try {
            $wpscLine = explode(',', $wpStoreCartOptions['product_designer_order']);
            if(isset($wpscLine[0])) {
                return $wpscLine;
            } else {
                return $productListingOrder;
            }
        } catch(Exception $e) {
            return $productListingOrder;
        }    

    }
}

if (!function_exists('wpscProductSingleReturnCurrentItemOrder')) {
    function wpscProductSingleReturnCurrentItemOrder() {

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        $productListingOrder = array();
        $productListingOrder[0]=1;
        $productListingOrder[1]=2;
        $productListingOrder[2]=3;
        $productListingOrder[3]=4;
        $productListingOrder[4]=5;
        $productListingOrder[5]=6;        
        $productListingOrder[6]=7;
        
        try {
            $wpscLine = explode(',', $wpStoreCartOptions['product_single_designer_order']);
            if(isset($wpscLine[0])) {
                return $wpscLine;
            } else {
                return $productListingOrder;
            }
        } catch(Exception $e) {
            return $productListingOrder;
        }    

    }
}


if (!function_exists('wpscProductSingleReturnCurrentListItemOrder')) {
    function wpscProductSingleReturnCurrentListItemOrder() {

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        $productListingOrder = array();
        $productListingOrder[0]=1;
        $productListingOrder[1]=2;
        $productListingOrder[2]=3;
        $productListingOrder[3]=4;
        $productListingOrder[4]=5;
        
        try {
            $wpscLine = explode(',', $wpStoreCartOptions['product_listitem_designer_order']);
            if(isset($wpscLine[0])) {
                return $wpscLine;
            } else {
                return $productListingOrder;
            }
        } catch(Exception $e) {
            return $productListingOrder;
        }    

    }
}


if(!function_exists('wpscProductGetPictureGallery')) {
    /**
     *
     * Returns an HTML picture gallery that is associated with the product specified in $productid
     * 
     * @global object $wpdb
     * @param integer $productid
     * @return string 
     */
    function wpscProductGetPictureGallery($productid) {
        global $wpdb;

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        $maxImageWidth = $wpStoreCartOptions['wpStoreCartwidth'];
        $maxImageHeight = $wpStoreCartOptions['wpStoreCartheight'];

        $output = '<div class="wpsc-gallery">';

        if($productid==0) {
            $preresults[0]['value'] = 'image01.jpg<<<||image02.jpg<<<||image03.jpg<<<||image04.jpg<<<||image05.jpg<<<||';
        } else {
            $table_name = $wpdb->prefix . "wpstorecart_meta";
            $preresults = $wpdb->get_results("SELECT * FROM {$table_name} WHERE `type`='wpsc_product_gallery' AND `foreignkey`='{$productid}'", ARRAY_A);
            
        }
        
        
        if(isset($preresults[0]['value'])) {
            $theExploded = explode('||', str_replace('<<<','',$preresults[0]['value']));
            foreach($theExploded as $theExplosion) {
                if(trim($theExplosion!='')) {
                    $output .= '<a href="'.get_bloginfo('url'). '/wp-content/uploads/wpstorecart/'.$theExplosion.'" class="thickbox" rel="gallery-'.$productid.'"><img src="'.get_bloginfo('url'). '/wp-content/uploads/wpstorecart/'.$theExplosion.'" class="thickbox wpsc-gallery-thumbnail" ';if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= ' /></a>';
                }
            }
        }

        $output.= '</div>';

        return $output;

    }        
}

if(!function_exists('wpscProductGetProductAccessories')) {
    /**
     *
     * Returns XHTML for the Frontend product accessories for the specified product with $primkey
     * 
     * @global object $wpdb
     * @global object $current_user
     * @global boolean $wpsc_testing_mode
     * @param type $primkey The key of the product you wish to display the accessories of
     * @return string 
     */
    function wpscProductGetProductAccessories($primkey) {
        global $wpdb, $current_user;

        
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        if($wpStoreCartOptions['combo_enable']=='true') {

            wp_get_current_user();
            if ( 0 == $current_user->ID ) {
                // Not logged in.
                $theuser = 0;
            } else {
                $theuser = $current_user->ID;
            }                        


            $output = NULL;
            $atLeast1Product = false;
            $theAccessories = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_meta` WHERE (`type`='productcombo' OR `type`='assignedcombo') AND `foreignkey`='{$primkey}';", ARRAY_A);
            if(isset($theAccessories[0]['primkey'])) {
                $maxImageWidth = $wpStoreCartOptions['wpStoreCartwidth'];
                $maxImageHeight = $wpStoreCartOptions['wpStoreCartheight'];                        
                $output.= '
                <script type="text/javascript">                            
                /* <![CDATA[ */

                jQuery.fn.getCheckboxVal = function(){
                    var vals = [];
                    var i = 0;
                    this.each(function(){
                        vals[i++] = jQuery(this).val();
                    });
                    return vals;
                }

                function wpscSubmitMultiAddToCart() {
                    var productsToAdd = jQuery("input[name=\'wpsc-add-product-combo\']:checked").getCheckboxVal();
                    jQuery.ajax({        
                           type: "POST",
                           url: "'.plugins_url().'/wpstorecart/wpstorecart/cart/multiaddtocart.php",
                           data: { productsToAddArray : productsToAdd },
                           success: function() {
    ';
                            if($wpStoreCartOptions['redirect_to_checkout']=='true' && $wpStoreCartOptions['checkoutpageurl']!='' ) {
                                $output.= 'window.location = "'.$wpStoreCartOptions['checkoutpageurl'].'";';          
                            } else {
                                $output.= 'location.reload(true);';
                            }
                            $output.= '
                           }
                        });                             
                }

                /* ]]> */
                </script>
                <form><input type="checkbox" name="wpsc-add-product-combo" checked="checked" value="'.$primkey.'" style="width:1px;height:1px;max-width:1px;max-height:1px;overflow:hidden;display:none;" /><table class="wpsc-product-accessories">';
                foreach ($theAccessories as $theAccessory){
                    if($theAccessory['type']=='productcombo') {
                        $exploded = explode('||', $theAccessory['value']);
                        $theCurrentAccProduct = $wpdb->get_results("SELECT `primkey`, `name`, `price`, `discountprice`, `shipping`, `thumbnail`, `postid`, `category` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`={$exploded[0]} AND `status`='publish';", ARRAY_A);
                        if(isset($theCurrentAccProduct[0]['primkey'])) {
                            foreach($theCurrentAccProduct as $currentAccProduct) {
                                $atLeast1Product = true;
                                $thePrice = $wpStoreCartOptions['currency_symbol'].$currentAccProduct['price'].$wpStoreCartOptions['currency_symbol_right'];
                                $theOriginalPrice = $thePrice;
                                $isADiscount = false;
                                $theNameToDisplay = NULL;

                                $groupDiscount = wpscGroupDiscounts($currentAccProduct['category'], $theuser);
                                if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {                                        

                                    // This hides products that are in a category that current user does not have permissions to see
                                } else {

                                    // These people can see the current category and continue to calculate price
                                    if((@$currentAccProduct['disountprice'] < @$currentAccProduct['price']) && @$currentAccProduct['discountprice']!='0.00') {
                                        $thePrice = $wpStoreCartOptions['currency_symbol'].$currentAccProduct['discountprice'].$wpStoreCartOptions['currency_symbol_right'];
                                        $isADiscount = true;
                                    }
                                    if($exploded[1] < $currentAccProduct['price']) {
                                        $thePrice = $wpStoreCartOptions['currency_symbol'].$exploded[1].$wpStoreCartOptions['currency_symbol_right'];
                                        $isADiscount = true;                                        
                                    }                                   
                                    if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) { // If guest cannot see prices, let's hide them here
                                        $thePrice = $wpStoreCartOptions['currency_symbol'].$wpStoreCartOptions['logged_out_price'].$wpStoreCartOptions['currency_symbol_right'];
                                        $theOriginalPrice = $thePrice;
                                    }
                                    if($wpStoreCartOptions['combo_display_links']=='true') {
                                        $theNameToDisplay = '<a href="'.get_permalink($currentAccProduct['postid']).'" alt="">'.$currentAccProduct['name'].'</a>';
                                    } else {
                                        $theNameToDisplay = $currentAccProduct['name'];
                                    }

                                    // Group discounts
                                    if ($groupDiscount['can_have_discount']==true && $wpStoreCartOptions['gd_enable']=='true') {
                                        $percentDiscount = $groupDiscount['discount_amount'] / 100;
                                        $discountToSubtract = $currentAccProduct['price'] * $percentDiscount;
                                        if($groupDiscount['gd_saleprice']==true && $discountToSubtract > 0) {
                                            $gdDiscountPrice = number_format($currentAccProduct['price'] - $discountToSubtract, 2);
                                        }                                                                      
                                        $currentAccProduct['price'] = number_format($currentAccProduct['price'] - $discountToSubtract, 2);
                                        if($gdDiscountPrice==0) { 
                                            // No change
                                        } else {
                                            if($gdDiscountPrice < $exploded[1]) {
                                                $isADiscount = true; 
                                                $thePrice = $wpStoreCartOptions['currency_symbol'].$gdDiscountPrice.$wpStoreCartOptions['currency_symbol_right'];
                                            }
                                        }
                                    }   
                                    // end group discount                                                

                                    $output.= '<tr><td style="vertical-align:middle;width:15px;"><input type="checkbox" name="wpsc-add-product-combo" value="'.$currentAccProduct['primkey'].'" /></td>';if($wpStoreCartOptions['combo_display_thumbs']=='true') { $output.='<td style="vertical-align:middle"><img src="'.$currentAccProduct['thumbnail'].'" alt="" '; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= ' /></td>';} $output.='<td style="vertical-align:middle">'.$theNameToDisplay.'</td>';if($wpStoreCartOptions['combo_display_prices']=='true') { $output.='<td style="vertical-align:middle">'; if($isADiscount){ $output.= '<strike>'.$theOriginalPrice.'</strike><br /> ';} $output .= $thePrice.'</td>';} $output.='</tr>';
                                }
                            }
                        }
                    }
                    if($theAccessory['type']=='assignedcombo') {
                        $assignedComboDisplay = array();
                        $assignedComboDisplayName = NULL;
                        $theNamesToDisplay = NULL;
                        $checkBoxes = NULL;
                        $theTotalPriceOfComboPack = 0;
                        $grabComboPack = $wpdb->get_results("SELECT `value` FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `primkey`='{$theAccessory['value']}';", ARRAY_A);
                        if(isset($grabComboPack[0]['value'])) {
                            $explodeLevel1 = explode('||',$grabComboPack[0]['value']);
                            $assignedComboDisplayName=$explodeLevel1[0];
                            if(isset($explodeLevel1[1])) {
                                $explodeLevel2 = explode(',',$explodeLevel1[1]);
                                if(isset($explodeLevel2[0])) {
                                    foreach($explodeLevel2 as $newComboToWorkWith) {
                                        if($newComboToWorkWith!=0) {
                                            $the_new_results = $wpdb->get_results("SELECT `price`, `discountprice`, `postid`, `name` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$newComboToWorkWith}';", ARRAY_A);
                                            if($the_new_results[0]['discountprice']==0 || $the_new_results[0]['discountprice']=='0.00' || ($the_new_results[0]['price'] < $the_new_results[0]['discountprice'] && ($the_new_results[0]['discountprice']!=0 || $the_new_results[0]['discountprice']!='0.00') ) ) {
                                                $theTotalPriceOfComboPack = $theTotalPriceOfComboPack + $the_new_results[0]['price'];
                                            } else {
                                                $theTotalPriceOfComboPack = $theTotalPriceOfComboPack + $the_new_results[0]['discountprice'];
                                            }
                                            if($wpStoreCartOptions['combo_display_links']=='true') {
                                                $theNamesToDisplay .= '<a href="'.get_permalink($the_new_results[0]['postid']).'" alt="">'.$the_new_results[0]['name'].'</a> ';
                                            } else {
                                                $theNamesToDisplay .= $the_new_results[0]['name'].' ';
                                            }                                                        
                                            $checkBoxes .= '<input type="checkbox" name="wpsc-add-product-combo" class="wpsc-add-product-combo-pack-'.$theAccessory['value'].'" value="'.$newComboToWorkWith.'"  style="display:none;" />';
                                            $the_new_results = NULL;
                                            $atLeast1Product = true;
                                        }
                                    }
                                }
                            }
                        }
                        if($theTotalPriceOfComboPack!=0) {
                            if($wpStoreCartOptions['show_price_to_guests']=='false' && !is_user_logged_in()) { // If guest cannot see prices, let's hide them here
                                $theTotalPriceOfComboPack = $wpStoreCartOptions['logged_out_price'];
                            }                                        
                            $output .= '<tr><td style="vertical-align:middle;width:15px;"><input type="checkbox" name="wpsc-add-product-combo-meta-'.$theAccessory['value'].'" onclick="if(jQuery(this).prop(\'checked\') == true){jQuery(\'.wpsc-add-product-combo-pack-'.$theAccessory['value'].'\').attr(\'checked\', true);} else {jQuery(\'.wpsc-add-product-combo-pack-'.$theAccessory['value'].'\').attr(\'checked\', false);}" >'.$checkBoxes.'</td>';
                            if($wpStoreCartOptions['combo_display_thumbs']=='true') { $output.='<td style="vertical-align:middle"><img src="'.plugins_url('/images/default_product_img.jpg' , __FILE__).'" alt="" '; if($maxImageWidth>1 || $maxImageHeight>1) { $output.= ' style="max-width:'.$maxImageWidth.'px;max-height:'.$maxImageHeight.'px;"';} $output .= ' /></td>';}
                            $output.='<td style="vertical-align:middle">'.$assignedComboDisplayName.'<br />'.$theNamesToDisplay.'</td>';if($wpStoreCartOptions['combo_display_prices']=='true') { $output.='<td style="vertical-align:middle">'; $output .= $wpStoreCartOptions['currency_symbol'].$theTotalPriceOfComboPack.$wpStoreCartOptions['currency_symbol_right'].'</td>';} $output.='</tr>';
                        }
                    }
                }
                if($atLeast1Product) {
                    $output .= '<tr><td></td>'; if($wpStoreCartOptions['combo_display_thumbs']=='true') { $output.='<td></td>';}  if($wpStoreCartOptions['combo_display_prices']=='true') { $output.='<td></td>';} $output.='<td><input type="submit" value="'.$wpStoreCartOptions['add_to_cart'].'" class="wpsc-button wpsc-addtocart" onclick="wpscSubmitMultiAddToCart();return false;" /></td></tr>';
                }
                $output.= '</table></form>';
            }

            return $output;
        }
    }        
}


if(!function_exists('wpscProductGetProductById')) {    
    /**
     * Returns the product specified by the ID
     * @param type $primkey 
     */
    function wpscProductGetProductById($primkey) {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpstorecart_products";
        $sql = "SELECT * FROM `{$table_name}` WHERE `primkey`={$primkey};";
        $results = $wpdb->get_results( $sql , ARRAY_A );    
        if(@isset($results[0])) {
            return $results[0];
        } else {
            return null;
        }
    }
}

if(!function_exists('wpscProductIncreaseProductStatistic')) {
    /**
     * This increments a product statistic
     * @param integer $primkey The primkey of the product you wish to increment
     * @param string $stat the stat you wish to increase
     */
    function wpscProductIncreaseProductStatistic($primkey=NULL, $stat=NULL) {
        global $wpdb;
        if(@isset($_POST['wpstorecart-item-id']) || $primkey!=NULL) {
            if($primkey==NULL) {
                $primkey = $_POST['wpstorecart-item-id'];
            }

            $newprimkey = intval($primkey); // Sanitized

            if(is_numeric($newprimkey)) {
                    $table_name = $wpdb->prefix . "wpstorecart_products";
                    $sql = "SELECT `{$stat}` FROM `{$table_name}` WHERE `primkey`={$newprimkey};";
                    $results = $wpdb->get_results( $sql , ARRAY_A );

                    if(isset($results)) {
                            $newStat = $results[0][$stat] + 1;
                            $wpdb->query("UPDATE `{$table_name}` SET `{$stat}` = '{$newStat}' WHERE `primkey` = {$newprimkey} LIMIT 1 ;"); 
                            if($stat == 'timesviewed') {$stat2 = 'productview';}
                            if($stat == 'timesaddedtocart') {$stat2 = 'addtocart';}
                            if($stat == 'timespurchased') {$stat2 = 'purchase';}
                            wpscLog(NULL, $stat2, esc_sql($_SERVER['REMOTE_ADDR']), $primkey, date('Ymd')); // write it to the log
                    }
            }

        }            
    }          
}

if(!function_exists('wpscProductIncreaseProductViewedStatistic')) {
    /**
     * This increments the view counter for the product statistics
     * @param integer $primkey The primkey of the product you wish to increment
     */
    function wpscProductIncreaseProductViewedStatistic($primkey=NULL) {
        wpscProductIncreaseProductStatistic($primkey, 'timesviewed');
    }        
}

if(!function_exists('wpscProductAddToCartStatistic')) {
    /**
     * This increments the add to cart counter for the product statistics
     * @param integer $primkey The primkey of the product you wish to increment
     */
    function wpscProductIncreaseProductAddToCartStatistic($primkey=NULL) {
        wpscProductIncreaseProductStatistic($primkey, 'timesaddedtocart');
    }
}

if(!function_exists('wpscProductIncreaseProductPurchasedStatistic')) {
    /**
     * This increments the purchased counter for the product statistics
     * @param integer $primkey The primkey of the product you wish to increment
     */
    function wpscProductIncreaseProductPurchasedStatistic($primkey=NULL) {
        wpscProductIncreaseProductStatistic($primkey, 'timespurchased');
    } 
}

if(!function_exists('wpscProductDecreaseProductInventory')) {
    function wpscProductDecreaseProductInventory($primkey=NULL, $quantity=1) {
        global $wpdb;
        $select = $wpdb->get_results("SELECT `inventory`, `useinventory` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$primkey}';", ARRAY_A);
        
        if (isset($select[0]['useinventory'])) {
            $newInventoryAmount = $select[0]['inventory'] - $quantity; // Decrease inventory regardless
            
            if($select[0]['useinventory']==1) { // If we're using inventory on this product
                if($select[0]['inventory']<=0) { // We are out of product.  Never the less, we will still decrease the inventory into negative numbers
                    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `inventory`='{$newInventoryAmount}' WHERE `primkey`='{$primkey}';");
                    wpscLog(NULL, 'oversold', esc_sql($_SERVER['REMOTE_ADDR']), $primkey, date('Ymd')); // write it to the log
                    return true; // Returns true because there was no issue.
                } else { // We're fine.  Let's get it over with.
                    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `inventory`='{$newInventoryAmount}' WHERE `primkey`='{$primkey}';");
                    
                    return true; // Returns true because there was no issue.
                }
            } else {
                return true; // Returns true because there was no issue.
            }
            
        } else {
            return false;
        }
    }
}

if(!function_exists('wpscProductIsMembership')) {
    /**
     *
     * If we're dealing with a membership, returns true, else it returns false
     * @global object $wpdb
     * @param integer $primkey
     * @return boolean 
     */
    function wpscProductIsMembership($primkey) {
        global $wpdb;
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');  
        // This code checks to see if we will be potentially displaying subscription products with either the price or add to cart button visible.  If so, we query each product for subscription information
        $wpsc_price_type = 'charge';
        $membership_value = '';
        if( ($primkey!=0 && $primkey!=NULL) && ($wpStoreCartOptions['displaypriceonview']=='true' || $wpStoreCartOptions['displayAddToCart']=='true')){
            $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
            $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$primkey};";
            $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
            if(isset($resultsMembership)) {
                foreach ($resultsMembership as $pagg) {
                    $membership_primkey = $pagg['primkey'];
                    $membership_value = $pagg['value'];
                }
                if($membership_value!='') {
                    $theExploded = explode('||', $membership_value);
                    // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                    $wpsc_price_type = $theExploded[0];
                }
            }
        }
        if($wpsc_price_type == 'membership') {
            return true;
        } else {
            return false;
        }
    }
}


if(!function_exists('wpscProductGetAddToCartButton')) {
    /**
     *
     * Returns an Add to Cart button for the specified product, optionally with an altered price
     * 
     * @global object $wpdb
     * @global $wpsc_buy_now $wpsc_buy_now
     * @global object $wpdb
     * @global integer $wpsc_membership_product_id
     * @global $purchaser_user_id $purchaser_user_id
     * @global $purchaser_email $purchaser_email
     * @global $membershipOptions $membershipOptions
     * @global string $wpsc_table_name
     * @global string $wpsc_self_path
     * @global $wpsc_paypal_testmode $wpsc_paypal_testmode
     * @global $wpsc_paypal_ipn $wpsc_paypal_ipn
     * @global $wpsc_membership_product_name $wpsc_membership_product_name
     * @global $wpsc_membership_product_number $wpsc_membership_product_number
     * @global $wpsc_button_classes $wpsc_button_classes
     * @global $wpsc_paypal_currency_code $wpsc_paypal_currency_code
     * @global $wpsc_paypal_email $wpsc_paypal_email
     * @global $wpsc_price_type $wpsc_price_type
     * @global $wpsc_membership_trial1_allow $wpsc_membership_trial1_allow
     * @global $wpsc_membership_trial2_allow $wpsc_membership_trial2_allow
     * @global $wpsc_membership_trial1_amount $wpsc_membership_trial1_amount
     * @global $wpsc_membership_trial2_amount $wpsc_membership_trial2_amount
     * @global $wpsc_membership_regular_amount $wpsc_membership_regular_amount
     * @global $wpsc_membership_trial1_numberof $wpsc_membership_trial1_numberof
     * @global $wpsc_membership_trial2_numberof $wpsc_membership_trial2_numberof
     * @global $wpsc_membership_regular_numberof $wpsc_membership_regular_numberof
     * @global $wpsc_membership_trial1_increment $wpsc_membership_trial1_increment
     * @global $wpsc_membership_trial2_increment $wpsc_membership_trial2_increment
     * @global $wpsc_membership_regular_increment $wpsc_membership_regular_increment
     * @param type $primkey
     * @param type $price
     * @return string 
     */
    function wpscProductGetAddToCartButton($primkey, $price = NULL) {
        global $wpdb;
        $output = '';
        
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        if(isset($primkey) && is_numeric($primkey)) {
                $table_name = $wpdb->prefix . "wpstorecart_products";
                $sql = "SELECT * FROM `{$table_name}` WHERE `primkey`={$primkey};";
                $results = $wpdb->get_results( $sql , ARRAY_A );
                    if(isset($results)) {

                            if(wpscProductIsMembership($primkey)) { // If this product is a membership
                                $membership_value = '';
                                if( ($wpStoreCartOptions['displaypriceonview']=='true' || $wpStoreCartOptions['displayAddToCart']=='true')){
                                    $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                                    $grabmember = "SELECT * FROM `{$table_name_meta}` WHERE `type`='membership' AND `foreignkey`={$results[0]['primkey']};";
                                    $resultsMembership = $wpdb->get_results( $grabmember , ARRAY_A );
                                    if(isset($resultsMembership)) {
                                        foreach ($resultsMembership as $pagg) {
                                            $membership_value = $pagg['value'];
                                        }
                                        if($membership_value!='') {
                                            global $wpsc_buy_now, $wpdb, $wpsc_membership_product_id, $purchaser_user_id, $purchaser_email, $membershipOptions, $wpsc_table_name, $wpsc_self_path, $wpsc_paypal_testmode, $wpsc_paypal_ipn, $wpsc_membership_product_name, $wpsc_membership_product_number, $wpsc_button_classes, $wpsc_paypal_currency_code, $wpsc_paypal_email, $wpsc_price_type,$wpsc_membership_trial1_allow, $wpsc_membership_trial2_allow, $wpsc_membership_trial1_amount , $wpsc_membership_trial2_amount, $wpsc_membership_regular_amount,$wpsc_membership_trial1_numberof,$wpsc_membership_trial2_numberof,$wpsc_membership_regular_numberof,$wpsc_membership_trial1_increment,$wpsc_membership_trial2_increment,$wpsc_membership_regular_increment;
                                            $theExploded = explode('||', $membership_value);
                                            // membership||yes||yes||0.00||0.00||0.00||1||1||1||D||D||D
                                            $wpsc_membership_product_id = $results[0]['primkey'];
                                            $wpsc_price_type = $theExploded[0];
                                            $wpsc_membership_trial1_allow = $theExploded[1];
                                            $wpsc_membership_trial2_allow = $theExploded[2];
                                            $wpsc_membership_trial1_amount = $theExploded[3];
                                            $wpsc_membership_trial2_amount = $theExploded[4];
                                            $wpsc_membership_regular_amount = $theExploded[5];
                                            $wpsc_membership_trial1_numberof = $theExploded[6];
                                            $wpsc_membership_trial2_numberof = $theExploded[7];
                                            $wpsc_membership_regular_numberof = $theExploded[8];
                                            $wpsc_membership_trial1_increment = $theExploded[9];
                                            $wpsc_membership_trial2_increment = $theExploded[10];
                                            $wpsc_membership_regular_increment = $theExploded[11];
                                            if($wpsc_membership_trial1_increment=='D'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['day'];}
                                            if($wpsc_membership_trial2_increment=='D'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['day'];}
                                            if($wpsc_membership_regular_increment=='D'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['day'];}
                                            if($wpsc_membership_trial1_increment=='W'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['week'];}
                                            if($wpsc_membership_trial2_increment=='W'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['week'];}
                                            if($wpsc_membership_regular_increment=='W'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['week'];}
                                            if($wpsc_membership_trial1_increment=='M'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['month'];}
                                            if($wpsc_membership_trial2_increment=='M'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['month'];}
                                            if($wpsc_membership_regular_increment=='M'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['month'];}
                                            if($wpsc_membership_trial1_increment=='Y'){$wpsc_membership_trial1_increment_display=$wpStoreCartOptions['year'];}
                                            if($wpsc_membership_trial2_increment=='Y'){$wpsc_membership_trial2_increment_display=$wpStoreCartOptions['year'];}
                                            if($wpsc_membership_regular_increment=='Y'){$wpsc_membership_regular_increment_display=$wpStoreCartOptions['year'];}
                                            $membershipOptions['databasename'] = DB_NAME;
                                            $membershipOptions['databaseuser'] = DB_USER;
                                            $membershipOptions['databasepass'] = DB_PASSWORD;
                                            $membershipOptions['databasehost'] =DB_HOST;
                                            $membershipOptions['databaseprefix'] = $wpdb->prefix;
                                            $membershipOptions['databasetable'] = $membershipOptions['databaseprefix'] . 'wpstorecart_log';
                                            $membershipOptions['databaseproductstable'] = $membershipOptions['databaseprefix'] . 'wpstorecart_products';
                                        }
                                    }
                                } 

                                if($results[0]['useinventory']==0 || ($results[0]['useinventory']==1 && $results[0]['inventory'] > 0) || $wpStoreCartOptions['storetype']=='Digital Goods Only' ) {
                                        // Allows us to bypass registration and have guest only checkout
                                        if($wpStoreCartOptions['requireregistration']=='false') {
                                            if(@isset($_SESSION['wpsc_email'])) {
                                                $purchaser_user_id = 0;
                                                $purchaser_email = esc_sql($_SESSION['wpsc_email']);
                                                $purchasing_display_name = 'Guest ('.esc_sql($_SERVER['REMOTE_ADDR']).')';
                                            } else {
                                                $purchaser_user_id = $current_user->ID;
                                                $purchaser_email = $current_user->user_email;
                                                $purchasing_display_name = '%user_display_name_with_link%';
                                            }
                                        } else {
                                                $purchaser_user_id = $current_user->ID;
                                                $purchaser_email = $current_user->user_email;
                                                $purchasing_display_name = '%user_display_name_with_link%';
                                        }
                                        $wpsc_membership_product_name = $results[0]['name'];
                                        $wpsc_membership_product_number = $results[0]['primkey'];
                                        $wpsc_paypal_currency_code = $wpStoreCartOptions['currency_code'];
                                        $wpsc_paypal_email = $wpStoreCartOptions['paypalemail'];
                                        $wpsc_button_classes = $wpStoreCartOptions['button_classes_addtocart'];
                                        $wpsc_paypal_ipn = $wpStoreCartOptions['paypalipnurl'];
                                        $wpsc_paypal_testmode = $wpStoreCartOptions['paypaltestmode'];
                                        $wpsc_self_path = plugins_url().'/wpstorecart/plugins/wpsc-membership-pro/';
                                        $wpsc_table_name = $wpdb->prefix .'wpstorecart_meta';
                                        $wpsc_buy_now = $wpStoreCartOptions['buy_now'];
                                        @require(WP_PLUGIN_DIR.'/wpstorecart/plugins/wpsc-membership-pro/paypal.php');
                                        $output .= wpscMembershipButton();

                                  } else {
                                        $output .= '<form class="wpsc_add_to_cart_form">
                                        <input type="hidden" name="placeholder" />
                                        ';
                                        $output .= $wpStoreCartOptions['out_of_stock'];
                                        $output .= '</form>';
                                }                                    

                            } else {
                                $disableresults = $wpdb->get_results("SELECT `value` FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `type`='disableaddtocart' AND `foreignkey`='{$primkey}';", ARRAY_A);
                                if(@$disableresults[0]['value']!='yes' || is_page($results[0]['postid'])){
                                    // Flat rate shipping implmented here:
                                    if($wpStoreCartOptions['flatrateshipping']=='all_single') {
                                        $result['shipping'] = $wpStoreCartOptions['flatrateamount'];
                                    } elseif($wpStoreCartOptions['flatrateshipping']=='off' || $wpStoreCartOptions['flatrateshipping']=='all_global') {
                                        $result['shipping'] = '0.00';
                                    }


                                    // Discount prices, as well as prices specifically set when calling this method
                                    if($price!=NULL) {
                                        if($price > 0) {
                                            $theActualPrice = $price;
                                        } else {
                                            // Discount prices
                                            if($results[0]['discountprice'] > 0) {
                                                $theActualPrice = $results[0]['discountprice'];
                                            } else {
                                                $theActualPrice = $results[0]['price'];
                                            }
                                        }                                            
                                    } else {
                                        // Discount prices
                                        if($results[0]['discountprice'] > 0) {
                                            $theActualPrice = $results[0]['discountprice'];
                                        } else {
                                            $theActualPrice = $results[0]['price'];
                                        }                                            
                                    }

                                    $output .= '
                                    <form method="post" action="" class="wpsc_add_to_cart_form">
                                    ';
                                    
                                    $fieldresults = wpscProductGetFieldsArray($primkey);
                                    if(isset($fieldresults[0]['primkey'])) {
                                        foreach($fieldresults as $fieldresult) {
                                            if($fieldresult['type']=='prompt') {
                                                $output .= "{$fieldresult['desc']} : <input type=\"text\" class=\"wpstorecart_product_options wpsc_field_required\" id=\"wpstorecart_product_options_id_{$fieldresult['primkey']}\" name=\"wpstorecart_product_options[]\" value=\"\" /><br />";
                                            }
                                        }
                                    }
                                    
                                    $output .= '
                                            <input type="hidden" class="wpstorecart-item-id" name="wpstorecart-item-id" id="wpstorecart-item-id-'.$results[0]['primkey'].'" value="'.$results[0]['primkey'].'" />
                                            <input type="hidden" class="wpstorecart-item-primkey" name="wpstorecart-item-primkey" id="wpstorecart-item-primkey-'.$results[0]['primkey'].'" value="'.$results[0]['primkey'].'" />
                                            <input type="hidden" class="wpstorecart-item-name" name="wpstorecart-item-name" id="wpstorecart-item-name-'.$results[0]['primkey'].'" value="'.stripslashes(str_replace('"', '',$results[0]['name'])).'" />
                                            <input type="hidden" class="wpstorecart-item-price" name="wpstorecart-item-price" id="wpstorecart-item-price-'.$results[0]['primkey'].'" value="'.$theActualPrice.'" />
                                            <input type="hidden" class="wpstorecart-item-shipping" name="wpstorecart-item-shipping" id="wpstorecart-item-shipping-'.$results[0]['primkey'].'" value="'.$results[0]['shipping'].'" />
                                            <input type="hidden" class="wpstorecart-item-img" name="wpstorecart-item-img" id="wpstorecart-item-img-'.$results[0]['primkey'].'" value="'.$results[0]['thumbnail'].'" />
                                            <input type="hidden" class="wpstorecart-item-url" name="wpstorecart-item-url" id="wpstorecart-item-url-'.$results[0]['primkey'].'" value="'.get_permalink($results[0]['postid']).'" />
                                            <input type="hidden" class="wpstorecart-item-tax" name="wpstorecart-item-tax" id="wpstorecart-item-tax-'.$results[0]['primkey'].'" value="0" />
                                            <input type="hidden" class="wpstorecart-item-qty" name="wpstorecart-item-qty" id="wpstorecart-item-qty-'.$results[0]['primkey'].'" value="1" />
                                            <input type="hidden" class="wpstorecart-item-variation" name="wpstorecart-item-variation" id="wpstorecart-item-variation-'.$results[0]['primkey'].'" value="0" />
                                            ';

                                    if($results[0]['useinventory']==0 || ($results[0]['useinventory']==1 && $results[0]['inventory'] > 0) || $wpStoreCartOptions['storetype']=='Digital Goods Only' ) {
                                        //check inventory amount:
                                        $modified_js = null;
                                        $modified_js_final = null;
                                        if($results[0]['useinventory']==1) {                                        
                                            $modified_js = 'if (jQuery(\'#wpsc-individualqty-'.$results[0]['primkey'].'\').val() > '.$results[0]['inventory'] .') {alert(\''.__('You have attempted to purchase more items than we currently have in stock.  We have adjusted the quantity to maximum available.  Please try again.', 'wpstorecart').'\');jQuery(\'#wpsc-individualqty-'.$results[0]['primkey'].'\').val('.$results[0]['inventory'].');return false;} else { ';
                                            $modified_js_final = '}';
                                        }
                                        
                                        $output .= '<button name="wpstorecart-add-to-cart" id="wpsc-addtocart-primkey-'.$results[0]['primkey'].'" class="wpsc-button wpsc-addtocart '.$wpStoreCartOptions['button_classes_addtocart'].'" onsubmit="'.$modified_js.' '.$modified_js_final.';jQuery( \'.wpstorecart_product_options\' ).each(function() { jQuery(this).val(this.id + \'::\' + jQuery(this).val() + \'||\'); });if(jQuery(\'.wpsc_field_required\').val()==\'\') {alert(\''.__('You must fill out all information before adding this product to your order.', 'wpstorecart').'\');return false;} " onclick="'.$modified_js.' '.$modified_js_final.';jQuery( \'.wpstorecart_product_options\' ).each(function() { jQuery(this).val(this.id + \'::\' + jQuery(this).val() + \'||\'); });if(jQuery(\'.wpsc_field_required\').val()==\'\') {alert(\''.__('You must fill out all information before adding this product to your order.', 'wpstorecart').'\');return false;}">'.$wpStoreCartOptions['add_to_cart'].'</button>';
                                    } else {
                                        $output .= $wpStoreCartOptions['out_of_stock'];
                                    }

                                    $output .= '
                                    </form>
                                    ';
                                }
                            } 



            } 
        }

        return $output;
    }        
}


if(!function_exists('wpscConvertSimpleVariationsToV3')) {
    function wpscConvertSimpleVariationsToV3() {
        global $wpdb;
        $wpscGrabOldVariations = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `type`='productvariation';", ARRAY_A);
        if(@isset($wpscGrabOldVariations[0]['primkey'])) {
            foreach(@$wpscGrabOldVariations as $wpscGrabOldVariation) {
                @$exploded = explode('||',$wpscGrabOldVariation['value']);
                
                $results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$wpscGrabOldVariation['foreignkey']}';", ARRAY_A);
                
                if(isset($results[0]['primkey'])) {
                    /**
                    $wpscGrabOldVariation['foreignkey']; 
                    'dropdown'; // dropdown, checkbox, colorbox, imageset
                    $exploded[0]; //  The group name for this variation. If we had multiple sizes of shirts for our variation, then this would be: SIZE
                    $exploded[1]; // The name of this specific variation within the group.  Using our size again, an example would be: EXTRA LARGE
                    $exploded[2]; // 0 if same as the parent price, otherwise add or subtract this amount
                    $exploded[3]; // ?
                    $exploded[4]; // Downloads
                    * 
                    */
                    
                    @$thisVariationsPrice = $results[0]['price'] + $exploded[2];
                    @$thisVariationsDownloads = str_replace('****', '||', $exploded[4]); // Reformat the variation's downloads into the format used by products
                    
                    // Now insert the variation into the wpStoreCart database
                    $insert = "
                    INSERT INTO `{$wpdb->prefix}wpstorecart_products` (`primkey`, `name`, `introdescription`, `description`, `thumbnail`, `price`, `shipping`, `download`, `tags`, `category`, `inventory`, `dateadded`, `postid`, `timesviewed`, `timesaddedtocart`, `timespurchased`, `useinventory`, `donation`, `weight`, `length`, `width`, `height`, `discountprice`, `producttype`, `status`, `options`, `productdesignercss`, `shippingservices`) VALUES
                    (NULL, 
                    '{$exploded[1]}', 
                    '{$results[0]['introdescription']}', 
                    '{$results[0]['description']}', 
                    '{$results[0]['thumbnail']}', 
                    '{$thisVariationsPrice}', 
                    '{$results[0]['shipping']}', 
                    '{$thisVariationsDownloads}', 
                    '{$results[0]['tags']}',  
                    '{$results[0]['category']}',  
                    '{$results[0]['inventory']}', 
                    '{$results[0]['dateadded']}',  
                    '{$wpscGrabOldVariation['foreignkey']}',
                    0,
                    0,
                    0,
                    '{$results[0]['useinventory']}',
                    '{$results[0]['donation']}',  
                    '{$results[0]['weight']}',  
                    '{$results[0]['length']}',  
                    '{$results[0]['width']}',  
                    '{$results[0]['height']}',  
                    '0',
                    'variation',
                    'dropdown',
                    '{$exploded[0]}',
                    '',
                    ''
                    );
                    ";					

                    $wpdb->query( $insert ); // This actually does the insert

                    // Next, let's go through every order and look for this variation, and convert the order into the correct new variation primkey
                    $lastID = NULL;
                    $lastID = $wpdb->insert_id; // Grab the variation's new primkey
                    
                    $ordersResults = $wpdb->get_results("SELECT `primkey`, `cartcontents` FROM `{$wpdb->prefix}wpstorecart_orders`;", ARRAY_A);
                    if($ordersResults[0]['primkey']) { // If there are orders
                        foreach($ordersResults as $ordersResult) { // then cycle through each order, one at a time
                            $newCartContents = NULL; // Reset this variable to NULL
                            $recordedAChange = false; // Rest this too
                            $explodedCartContents = explode(',', $ordersResult['cartcontents']); // now that we have a single order to look at, let's look at each item in the cart for that order
                            if(isset($explodedCartContents[0])) {
                                
                                foreach($explodedCartContents as $explodedCartContent) { // lets begin cycling
                                    
                                    if(strpos($explodedCartContent, $wpscGrabOldVariation['foreignkey'].'-'.$wpscGrabOldVariation['primkey'].'*')!==false) { // If this variation is found in a previous order:
                                        $newCartContents .= str_replace($wpscGrabOldVariation['foreignkey'].'-'.$wpscGrabOldVariation['primkey'].'*', $lastID.'*', $explodedCartContent).','; // Then replace
                                        $recordedAChange = true; // We had a change
                                    } else {
                                        $newCartContents .= $explodedCartContent.','; // Nothing to change, let's keep it
                                    }
                                    
                                }
                                $newCartContents = substr($newCartContents, 0, -1); // Remove the last comma
                                
                                if($recordedAChange) { // Update the order
                                    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_orders` SET `cartcontents`='{$newCartContents}' WHERE `primkey`='{$ordersResult['primkey']}';");
                                }
                            }
                        }
                    }
                    
                    $wpdb->query( "DELETE FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `primkey`='{$wpscGrabOldVariation['primkey']}';" );
                }
             
                
            }
        }
    }
}

if(!function_exists('wpscProductClone')) {
    /**
     *
     * Clone a product or create a new variation
     * 
     * @global object $wpdb
     * @param type $productIDToClone
     * @param string $name
     * @param type $type
     * @param type $option 
     */
    function wpscProductClone($productIDToClone, $name=NULL, $type='product', $option='dropdown', $options=NULL) {
        global $wpdb;
        
        wpscCheckAdminPermissions();

        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 
        
        $results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$productIDToClone}';", ARRAY_A);
        
        if(isset($results[0]['primkey'])) {

            if($name==NULL) {
                $name = __('Clone of','wpstorecart').' '.$results[0]['name'];
            }
            
            if($type=='variation') {
                $results[0]['status'] = $option;
                $thePostID = $productIDToClone;
            }
            
            if($options==NULL) {
                $options = $results[0]['options'];
            } else {
                $options = $options;
            }
            
            if($type=='product') {            
                // Create our PAGE in draft mode in order to get the POST ID
                $my_post = array();
                $my_post['post_title'] = stripslashes($name);
                $my_post['post_type'] = 'page';
                $my_post['post_content'] = '';
                $my_post['post_status'] = 'draft';
                $my_post['post_author'] = 1;
                $my_post['post_parent'] = $wpStoreCartOptions['mainpage'];

                // Insert the PAGE into the WP database
                $thePostID = wp_insert_post( $my_post );	

            }            
            
            $insert = "
            INSERT INTO `{$wpdb->prefix}wpstorecart_products` (`primkey`, `name`, `introdescription`, `description`, `thumbnail`, `price`, `shipping`, `download`, `tags`, `category`, `inventory`, `dateadded`, `postid`, `timesviewed`, `timesaddedtocart`, `timespurchased`, `useinventory`, `donation`, `weight`, `length`, `width`, `height`, `discountprice`, `producttype`, `status`, `options`, `productdesignercss`, `shippingservices`) VALUES
            (NULL, 
            '{$name}', 
            '{$results[0]['introdescription']}', 
            '{$results[0]['description']}', 
            '{$results[0]['thumbnail']}', 
            '{$results[0]['price']}', 
            '{$results[0]['shipping']}', 
            '{$results[0]['download']}', 
            '{$results[0]['tags']}',  
            '{$results[0]['category']}',  
            '{$results[0]['inventory']}', 
            '{$results[0]['dateadded']}',  
            '{$thePostID}',
            '{$results[0]['timesviewed']}', 
            '{$results[0]['timesaddedtocart']}', 
            '{$results[0]['timespurchased']}', 
            '{$results[0]['useinventory']}',
            '{$results[0]['donation']}',  
            '{$results[0]['weight']}',  
            '{$results[0]['length']}',  
            '{$results[0]['width']}',  
            '{$results[0]['height']}',  
            '{$results[0]['discountprice']}', 
            '{$type}', 
            '{$results[0]['status']}', 
            '{$options}',
            '{$results[0]['productdesignercss']}',
            '{$results[0]['shippingservices']}'
            );
            ";					

            $wpdb->query( $insert ); // This actually does the insert  
            
            $lastID = $wpdb->insert_id;
            $keytoedit = $lastID;

            if($type=='product') {            
                // Now that we've inserted both the PAGE and the product, let's update and publish our post with the correct content
                $my_post = array();
                $my_post['ID'] = $thePostID;
                $my_post['post_content'] = '[wpstorecart display="product" primkey="'.$lastID.'"]';

                wp_update_post( $my_post );     
                
            }
                   
            return $lastID;
            
        } else {
            return false;
        }
    }
}


if(!function_exists('wpscProductGetAttributes')) {
    function wpscProductGetAttributes($primkey) {
        global $wpdb;
        return $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}wpstorecart_quickvar` WHERE `productkey`='{$primkey}';", ARRAY_A);
    }
}

if(!function_exists('wpscProductGetAttributeGroups')) {
    /**
     * 
     * 
     * @param type $wpscAttributesResults
     * @return type 
     */
    function wpscProductGetAttributeGroups($wpscAttributesResults) {
        $wpscAttributesGroup = array();
        
        if(@isset($wpscAttributesResults[0]['primkey'])) {
            
            foreach($wpscAttributesResults as $wpscAttributesResult) {

                    $wpscAttributesGroup["{$wpscAttributesResult['group']}"]["{$wpscAttributesResult['primkey']}"]['primkey'] = $wpscAttributesResult['primkey'];
                    $wpscAttributesGroup["{$wpscAttributesResult['group']}"]["{$wpscAttributesResult['primkey']}"]['productkey'] = $wpscAttributesResult['productkey'];
                    $wpscAttributesGroup["{$wpscAttributesResult['group']}"]["{$wpscAttributesResult['primkey']}"]['values'] = $wpscAttributesResult['values'];                    
                    $wpscAttributesGroup["{$wpscAttributesResult['group']}"]["{$wpscAttributesResult['primkey']}"]['price'] = $wpscAttributesResult['price'];
                    $wpscAttributesGroup["{$wpscAttributesResult['group']}"]["{$wpscAttributesResult['primkey']}"]['type'] = $wpscAttributesResult['type'];
                    $wpscAttributesGroup["{$wpscAttributesResult['group']}"]["{$wpscAttributesResult['primkey']}"]['title'] = $wpscAttributesResult['title'];
                    $wpscAttributesGroup["{$wpscAttributesResult['group']}"]["{$wpscAttributesResult['primkey']}"]['group'] = $wpscAttributesResult['group'];
                    $wpscAttributesGroup["{$wpscAttributesResult['group']}"]["{$wpscAttributesResult['primkey']}"]['useinventory'] = $wpscAttributesResult['useinventory'];
                    

            }
        }  

       
        return $wpscAttributesGroup;
    }
}

if(!function_exists('wpscProductCheckForAttributes')) {
    /**
     * Check to see if a product has attributes saved
     * 
     * @global object $wpdb
     * @param type $product_id
     * @return boolean 
     */
    function wpscProductCheckForAttributes($product_id) {
        global $wpdb;
        
        $results = $wpdb->get_results("SELECT `primkey` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `producttype`='attribute' AND `postid`='{$product_id}';", ARRAY_A);
        if(isset($results[0]['primkey'])) {
            return true;
        } else {
            $results2 = $wpdb->get_results("SELECT `primkey` FROM `{$wpdb->prefix}wpstorecart_quickvar` WHERE `productkey`='{$product_id}';", ARRAY_A);
            if(isset($results2[0]['primkey'])) {
                return true;
            } else {
                return false;
            }
        }
    }
}

if(!function_exists('wpscProductCheckForVariations')) {
    /**
     * Check to see if a product has attributes saved
     * 
     * @global object $wpdb
     * @param type $product_id
     * @return boolean 
     */
    function wpscProductCheckForVariations($product_id) {
        global $wpdb;
        
        $results = $wpdb->get_results("SELECT `primkey` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `producttype`='variation' AND `postid`='{$product_id}';", ARRAY_A);
        if(isset($results[0]['primkey'])) {
            return true;
            
        } else {
            return false;
        }
        
    }
}


if(!function_exists('wpscProductGetAttributeKeyArray')) {
    function wpscProductGetAttributeKeyArray($wpscAttributesGroup) {
        $wpscKeyArray = array();
        foreach($wpscAttributesGroup as $key => $value) {
            $wpscKeyArray[] = $key;
        }   
        return $wpscKeyArray;
    }
}

if(!function_exists('wpscProductCreateAttributeDataset')) {
    function wpscProductCreateAttributeDataset($wpscAttributesGroup) {
        $datasetCount = 0;
        $wpscDatasets = array();
        foreach (wpscProductGetAttributeKeyArray($wpscAttributesGroup) as $wpscAttributesGroupKey) {
            $wpscDatasets[$datasetCount] = array();
            foreach($wpscAttributesGroup["{$wpscAttributesGroupKey}"] as $wpscFinalAttributeGroup) {
                array_push($wpscDatasets[$datasetCount], $wpscFinalAttributeGroup['primkey'].'||'.$wpscFinalAttributeGroup['title'].'||'.$wpscFinalAttributeGroup['price']);
            }
            $datasetCount++;
        }   
        return $wpscDatasets;
    }
}

if(!function_exists('wpscPossibleCombinationArray')) {
    function wpscPossibleCombinationArray($wpscInputArray) {
        $returnedArray = array();
        $wpscInputArray = array_values($wpscInputArray);
        $sizeIn = sizeof($wpscInputArray);

        if($sizeIn > 0) {
            $size = 1;
        } else {
            $size = 0;
        }

        foreach ($wpscInputArray as $array) {
                $size = $size * sizeof($array);
        }

        for ($iterationA = 0; $iterationA < $size; $iterationA++) {
            $returnedArray[$iterationA] = array();
            for ($iterationB = 0; $iterationB < $sizeIn; $iterationB++) {
                array_push($returnedArray[$iterationA], current($wpscInputArray[$iterationB]));
            }
            for ($iterationB = ($sizeIn -1); $iterationB >= 0; $iterationB--) {
                if (next($wpscInputArray[$iterationB])) {
                        break;
                } elseif (isset ($wpscInputArray[$iterationB])) {
                        reset($wpscInputArray[$iterationB]);
                }
            }
        }

        return $returnedArray;
    }
}


if(!function_exists('wpscProductConvertPoundsToKilograms')) {
    function wpscProductConvertPoundsToKilograms($lbs) {
        $kilograms = $lbs * 0.453592;
        return round($kilograms, 2);
    }
}

if(!function_exists('wpscProductConvertKilogramsToPounds')) {
    function wpscProductConvertKilogramsToPounds($kilograms) {
        $lbs = $kilograms * 2.20462;
        return round($lbs, 2);
    }
}

if(!function_exists('wpscProductConvertInchesToCentimeters')) {
    function wpscProductConvertInchesToCentimeters($inches) {
        $cm = $inches * 2.54;
        return round($cm, 3);
    }
}    

if(!function_exists('wpscProductConvertCentimetersToInches')) {
    function wpscProductConvertCentimetersToInches($cm) {
        $inches = $cm * 0.393701;
        return round($inches, 3);
    }
} 

if(!function_exists('wpscProductSelectDropdown')) {
    function wpscProductSelectDropdown($id='wpscProductSelectDropdown', $onselect=NULL, $includeVariations=true, $style=NULL) {
        global $wpdb;
        $output = NULL;
        
        if($includeVariations) {
            $sql = "SELECT * FROM `{$wpdb->prefix}wpstorecart_products`;";
        } else {
            $sql = "SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `producttype`='product';";
        }
        $results = $wpdb->get_results($sql,ARRAY_A);
        if(@isset($results[0]['primkey'])) {
            if($onselect==NULL) {
                $output .= "<select id=\"$id\" name=\"$id\" style=\"$style\">";
            } else {
                $output .= "<select id=\"$id\" name=\"$id\" onselect=\"{$onselect}\" style=\"$style\">";
            }
            foreach($results as $result) {
                if($result['producttype']=='variation' || $result['producttype']=='attribute') {
                    $nameresults = $wpdb->get_results("SELECT `name` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$result['postid']}';",ARRAY_A);
                    $output .= "<option value=\"{$result['primkey']}\">{$nameresults[0]['name']} - {$result['name']}</option>";
                } else {
                    $output .= "<option value=\"{$result['primkey']}\">{$result['name']}</option>";
                }
            }
            $output .= '</select>';
        }
        
        return $output;
    }
}

if(!function_exists('wpscProductGetNameById')) {
    /**
     * Returns a product array for the product key specified by $id
     * 
     * @global object $wpdb
     * @param integer $id
     * @return array or false on failure
     */
    function wpscProductGetNameById($id) {
        global $wpdb;
        $output = false;
        $sql = "SELECT `name`, `producttype`, `postid` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$id}';";
        $results = $wpdb->get_results($sql,ARRAY_A);
        if(@isset($results[0]['name'])) {
            if($results[0]['producttype']=='variation' || $results[0]['producttype']=='attribute') {
                $nameresults = $wpdb->get_results("SELECT `name` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$results[0]['postid']}';",ARRAY_A);
                $output .= "{$nameresults[0]['name']} - {$results[0]['name']}";
            } else {
                $output .= "{$results[0]['name']}";
            }
        }   
        
        return $output;
        
    }
}

if(!function_exists('wpscProductGetAllRecordsWithAttribute')) {
    function wpscProductGetAllRecordsWithAttribute($product_id, $attribute_id) {
        global $wpdb;
        $sql = "SELECT `name`, `producttype`, `postid` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`='{$id}';";
        $results = $wpdb->get_results($sql,ARRAY_A);
    }
}

if(!function_exists('wpscProductListProductDownloads')) {
    /**
        * 
        * Returns a string that lists all downloads associated with a product.  Use the $type parameter to return different styles of list.
        *
        * @global object $wpdb
        * @param integer $primkey
        * @param string $type  If $type = 'download' then it will allow a person to download the file (if they have purchased it)  If $type='edit' then it will allow admins to administrate
        * @return string 
        */
    function wpscProductListProductDownloads($primkey, $type="download") {
        global $wpdb;
        $table_name2 = $wpdb->prefix . "wpstorecart_products";
        $thevariationdetail[0] = NULL;
        $thevariationdetail[1] = NULL;
        $moutput = "
                    <script type=\"text/javascript\">
                        /* <![CDATA[ */
                        function str_replace(search, replace, subject, count) {
                            // Replaces all occurrences of search in haystack with replace
                            // MIT License

                                temp = '',
                                repl = '',
                                sl = 0,        fl = 0,
                                f = [].concat(search),
                                r = [].concat(replace),
                                s = subject,
                                ra = Object.prototype.toString.call(r) === '[object Array]',        sa = Object.prototype.toString.call(s) === '[object Array]';
                            s = [].concat(s);
                            if (count) {
                                this.window[count] = 0;
                            }
                            for (i = 0, sl = s.length; i < sl; i++) {
                                if (s[i] === '') {
                                    continue;
                                }        for (j = 0, fl = f.length; j < fl; j++) {
                                    temp = s[i] + '';
                                    repl = ra ? (r[j] !== undefined ? r[j] : '') : r[0];
                                    s[i] = (temp).split(f[j]).join(repl);
                                    if (count && s[i] !== temp) {                this.window[count] += (temp.length - s[i].length) / f[j].length;
                                    }
                                }
                            }
                            return sa ? s : s[0];
                        }
                        /* ]]> */
                    </script>
";

        $sql2 = "SELECT `primkey`, `name`, `download`, `postid` FROM `{$table_name2}` WHERE `primkey`={$primkey};";
        $moreresults = $wpdb->get_results( $sql2 , ARRAY_A );

        if($type=="download" && isset($moreresults[0])) {
                $output .= ', <br />';
                if($output==', <br />') {$output = '';}
                if($moreresults[0]['download']=='') { // Non-downloads products below:
                        $output .= $moreresults[0]['download'].' '.$thevariationdetail[0].' '.$thevariationdetail[1];
                } else { // Download products below:

                        $multidownloads = explode('||', $moreresults[0]['download']);
                        if(@isset($multidownloads[0]) && @isset($multidownloads[1])) {
                                $downloadcount = 0;
                                foreach($multidownloads as $multidownload) {
                                        if($multidownload!='') {
                                                $output .= '<a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.stripslashes($multidownload).'" class="wpsc-downloads-list"><img src="'.plugins_url().'/wpstorecart/images/disk.png"> '.$multidownload.'</a><br />';
                                        }
                                                $downloadcount++;
                                }
                        } else {
                                $output .= '<a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.stripslashes($moreresults[0]['download']).'" class="wpsc-downloads-list"><img src="'.plugins_url().'/wpstorecart/images/disk.png"> '.$moreresults[0]['download'].'</a>';
                        }
                }

        }

        // EDITING DOWNLOADS
        if($type=="edit" && isset($moreresults[0])) {
                $output .= ', <br />';
                if($output==', <br />') {$output = '';}
                if($moreresults[0]['download']=='') { // Non-downloads products below:
                        $output .= '';
                } else { // Download products below:

                        $multidownloads = explode('||', $moreresults[0]['download']);
                        if(@isset($multidownloads[0]) && @isset($multidownloads[1])) {
                                $downloadcount = 0;
                                foreach($multidownloads as $multidownload) {
                                        if($multidownload!='') {
                                                $output .= '<div id="'.md5($multidownload).'"><a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.stripslashes($multidownload).'" class="wpsc-downloads-list"> '.$multidownload.'</a> <a href="#" onclick="var answer = confirm(\'Are you sure you want to delete this download?\');if (answer){jQuery.post(\''.plugins_url().'/wpstorecart/wpstorecart/admin/php/deldownload.php\', { delete: \''.base64_encode($multidownload).'\', type: \'single\', primkey: \''.$primkey.'\' }, function(data) {document.wpstorecartaddproductform.wpStoreCartproduct_download.value = str_replace(\''.$multidownload.'||\', \'\', document.wpstorecartaddproductform.wpStoreCartproduct_download.value);jQuery(\'#'.md5($multidownload).'\').hide(\'slow\');});}else{return false;};"><img src="'.plugins_url().'/wpstorecart/images/cross.png"></a></div><br />';
                                        }
                                                $downloadcount++;
                                }
                        } else {
                                $output .= '<div id="'.md5($moreresults[0]['download']).'"><a href="'.WP_CONTENT_URL . '/uploads/wpstorecart/'.stripslashes($moreresults[0]['download']).'" class="wpsc-downloads-list"> '.$moreresults[0]['download'].'</a> <a href="#" onclick="var answer = confirm(\'Are you sure you want to delete this download?\');if (answer){jQuery.post(\''.plugins_url().'/wpstorecart/wpstorecart/admin/php/deldownload.php\', { delete: \''.base64_encode($moreresults[0]['download']).'\', type: \'single\', primkey: \''.$primkey.'\' }, function(data) {document.wpstorecartaddproductform.wpStoreCartproduct_download.value = str_replace(\''.$multidownload.'||\', \'\', document.wpstorecartaddproductform.wpStoreCartproduct_download.value);jQuery(\'#'.md5($moreresults[0]['download']).'\').hide(\'slow\');});}else{return false;};"><img src="'.plugins_url().'/wpstorecart/images/cross.png"></a></div>';
                        }
                }

        }

        return $moutput . $output;

    }
}

?>