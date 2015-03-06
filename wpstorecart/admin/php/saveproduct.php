<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

//error_reporting(E_ALL);
error_reporting(0);

global $wpdb, $current_user;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

  
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    
    if ( function_exists('current_user_can') && !current_user_can('manage_wpstorecart') ) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }		

    $table_name = $wpdb->prefix . "wpstorecart_products";
    $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
    

    



    // New products will also by default display the Add to Cart button, even if there are variations
    $display_add_to_cart_at_all_times = 'no';

    // For new products
    if(!isset($_POST['wpsc-keytoedit'])) {
            // Default form values
            $wpStoreCartproduct_name = '';
            $wpStoreCartproduct_introdescription = '';
            $wpStoreCartproduct_description = '';
            $wpStoreCartproduct_thumbnail = '';
            $wpStoreCartproduct_price = '0.00';
            $wpStoreCartproduct_shipping = '0.00';
            $wpStoreCartproduct_download = '';
            $wpStoreCartproduct_tags = '';
            $wpStoreCartproduct_category = 0;
            $wpStoreCartproduct_inventory = 0;
            $wpStoreCartproduct_useinventory = 1;
            $wpStoreCartproduct_weight = 0;
            $wpStoreCartproduct_length = 0;
            $wpStoreCartproduct_width = 0;
            $wpStoreCartproduct_height = 0;
            $keytoedit=0;
            $_POST['wpsc-keytoedit'] = 0;
            $wpStoreCartproduct_donation = 'false';
            $wpStoreCartproduct_serial_numbers = '';
            $wpStoreCartproduct_serial_numbers_used = '';
            $wpStoreCartproduct_discountprice = '0.00';
    } 


    

    // To edit a previous product
    $isanedit = false;
    if ($_POST['wpsc-keytoedit']!=0 && is_numeric($_POST['wpsc-keytoedit'])) {
            $_POST['wpsc-keytoedit'] = intval($_POST['wpsc-keytoedit']);
            $isanedit = true;

            // Membership
                if(@$_POST['wpsc-price-type2']=='membership' || @$_POST['wpsc-price-type']=='charge') {

                    $wpsc_membership_trial1_allow = 'no';
                    $wpsc_membership_trial2_allow = 'no';
                    if(@$_POST['wpsc_membership_trial1_allow']=='yes') {
                        $wpsc_membership_trial1_allow = 'yes';
                    }
                    if(@$_POST['wpsc_membership_trial2_allow']=='yes') {
                        $wpsc_membership_trial2_allow = 'yes';
                    }
                    $membership_value = $_POST['wpsc-price-type'] . '||' . $wpsc_membership_trial1_allow . '||' . $wpsc_membership_trial2_allow .'||'. $_POST['wpsc_membership_trial1_amount'] .'||'. $_POST['wpsc_membership_trial2_amount'] . '||' . $_POST['wpsc_membership_regular_amount'] . '||' . $_POST['wpsc_membership_trial1_numberof']. '||' . $_POST['wpsc_membership_trial2_numberof']. '||' . $_POST['wpsc_membership_regular_numberof']. '||' . $_POST['wpsc_membership_trial1_increment']. '||' . $_POST['wpsc_membership_trial2_increment']. '||' . $_POST['wpsc_membership_regular_increment'];

                    if(wpscProductIsMembership($_POST['wpsc-keytoedit'])) {
                        // Must update the membership
                        $insert = "UPDATE  `{$table_name_meta}` SET `value` = '".$membership_value."' WHERE `type`='membership' AND `foreignkey`='{$_POST['wpsc-keytoedit']}';";
                        $memresults = $wpdb->query( $insert );
                    } else {
                        // Must insert new membership
                        $insert = "INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".$membership_value."', 'membership', '{$_POST['wpsc-keytoedit']}');";
                        $memresults = $wpdb->query( $insert );
                    }

                } elseif ($_POST['wpsc-price-type']=='charge') {
                    //
                }
                         
            
            
            // Attributes inventory toggle
            if(@isset($_POST['wpscuseinventoryonattributes'])) {
                @$wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_quickvar` SET `useinventory`=1 WHERE `productkey`={$_POST['wpsc-keytoedit']};");
                @$wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `useinventory`=1 WHERE `postid`={$_POST['wpsc-keytoedit']} AND `producttype`='attribute';");
            } else {
                @$wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_quickvar` SET `useinventory`=0 WHERE `productkey`={$_POST['wpsc-keytoedit']};");
                @$wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `useinventory`=0 WHERE `postid`={$_POST['wpsc-keytoedit']} AND `producttype`='attribute';");
            }           
            
            // Saves Shipping Packages
            $sql = "SELECT * FROM `{$wpdb->prefix}wpstorecart_packages` WHERE `productkey`='{$_POST['wpsc-keytoedit']}';";
            $packages_result = $wpdb->get_results($sql, ARRAY_A);            
            if(@isset($packages_result[0]['primkey'])) {
                foreach($packages_result as $package_result) {
                    $wpsc_weight = $_POST['wpsc_weight_'.$package_result['primkey']];
                    if($_POST['wpsc-unit-conversion_'.$package_result['primkey']]=='poundstokg') { // If the value is KG, convert to LBS
                        $wpsc_weight = wpscProductConvertKilogramsToPounds($_POST['wpsc_weight_'.$package_result['primkey']]);
                    }
                    
                    $wpsc_length = $_POST['wpsc_length_'.$package_result['primkey']];
                    $wpsc_width = $_POST['wpsc_width_'.$package_result['primkey']];
                    $wpsc_depth = $_POST['wpsc_depth_'.$package_result['primkey']];
                    
                    if($_POST['wpsc-unit-conversion-2_'.$package_result['primkey']]=='inchestocm') {
                        $wpsc_length = wpscProductConvertCentimetersToInches($_POST['wpsc_length_'.$package_result['primkey']]);
                        $wpsc_width = wpscProductConvertCentimetersToInches($_POST['wpsc_width_'.$package_result['primkey']]);
                        $wpsc_depth = wpscProductConvertCentimetersToInches($_POST['wpsc_depth_'.$package_result['primkey']]);
                    }

                    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_packages` SET `weight`='{$wpsc_weight}', `length`='{$wpsc_length}', `width`='{$wpsc_width}', `depth`='{$wpsc_depth}' WHERE `primkey`='{$package_result['primkey']}' ;");
                }
            }
            
            
            $table_name30 = $wpdb->prefix . 'wpstorecart_meta';
            $preresults = $wpdb->get_results("SELECT * FROM {$table_name30} WHERE `type`='wpsc_product_gallery' AND `foreignkey`='{$_POST['wpsc-keytoedit']}'", ARRAY_A);
            if(isset($_POST['wpStoreCartproduct_download_pg'])) {
                $icounter = 1;
                $ucounter = 0;
                $finalSlideShowCode ='';
                while($icounter <= 200) { // Max 200 images per product
                        if(@isset($_POST['theimagefor_'.$ucounter])) {
                                if($_POST['theimagefor_'.$ucounter]!='' || $_POST['theimagefor_'.$ucounter]!=NULL) {
                                        $finalSlideShowCode .= $_POST['theimagefor_'.$ucounter].'<<<'.$_POST['thelinkfor_'.$ucounter].'||';
                                }
                                $icounter++;
                                $ucounter++;
                        } else {
                                $finalSlideShowCode .= $_POST['wpStoreCartproduct_download_pg'];
                                $icounter = 201; // This breaks us out of the loop if needed, after adding any new images to the database
                        }
                }

                if($preresults==false) {
                        $insert = "INSERT INTO `{$table_name30}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".$finalSlideShowCode."', 'wpsc_product_gallery', '{$_POST['wpsc-keytoedit']}');";
                } else {
                        $insert = "UPDATE  `{$table_name30}` SET `value` = '".$finalSlideShowCode."' WHERE `type`='wpsc_product_gallery' AND `foreignkey`='{$_POST['wpsc-keytoedit']}';";
                }

                $wpdb->query( $insert );
                $preresults = $wpdb->get_results("SELECT * FROM {$table_name30} WHERE `type`='wpsc_product_gallery' AND `foreignkey`='{$_POST['wpsc-keytoedit']}';", ARRAY_A);

            }

            // Grabs the serial numbers
            $results_serial_numbers = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='serialnumbers' AND `foreignkey`={$_POST['wpsc-keytoedit']};", ARRAY_N);
            if($results_serial_numbers!=false ) {
                $wpStoreCartproduct_serial_numbers = base64_decode($results_serial_numbers[0][0]);
                if(isset($_POST['wpStoreCartproduct_serial_numbers'])) {
                    $wpStoreCartproduct_serial_numbers = $_POST['wpStoreCartproduct_serial_numbers'];
                    $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = '".base64_encode($_POST['wpStoreCartproduct_serial_numbers'])."' WHERE `type`='serialnumbers' AND `foreignkey` = {$_POST['wpsc-keytoedit']};");
                }
            } else {
                if(isset($_POST['wpStoreCartproduct_serial_numbers'])) {
                    $wpStoreCartproduct_serial_numbers = $_POST['wpStoreCartproduct_serial_numbers'];
                    $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".base64_encode($_POST['wpStoreCartproduct_serial_numbers'])."', 'serialnumbers', '{$_POST['wpsc-keytoedit']}');");
                }
            }

            // Grabs the used serial numbers
            $results_serial_numbers_used = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='serialnumbersused' AND `foreignkey`={$_POST['wpsc-keytoedit']};", ARRAY_N);
            if($results_serial_numbers_used!=false ) {
                $wpStoreCartproduct_serial_numbers_used = base64_decode($results_serial_numbers_used[0][0]);
                if(isset($_POST['wpStoreCartproduct_serial_numbers_used'])) {
                    $wpStoreCartproduct_serial_numbers_used = $_POST['wpStoreCartproduct_serial_numbers_used'];
                    $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = '".base64_encode($_POST['wpStoreCartproduct_serial_numbers_used'])."' WHERE `type`='serialnumbersused' AND `foreignkey` = {$_POST['wpsc-keytoedit']};");
                }
            } else {
                if(isset($_POST['wpStoreCartproduct_serial_numbers_used'])) {
                    $wpStoreCartproduct_serial_numbers_used = $_POST['wpStoreCartproduct_serial_numbers_used'];
                    $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, '".base64_encode($_POST['wpStoreCartproduct_serial_numbers_used'])."', 'serialnumbersused', '{$_POST['wpsc-keytoedit']}');");
                }
            }

            // Disables the Add to Cart if needed
            $results_disable_add_to_cart = $wpdb->get_results("SELECT `value` FROM `{$table_name_meta}` WHERE `type`='disableaddtocart' AND `foreignkey`={$_POST['wpsc-keytoedit']};", ARRAY_N);
            if($results_disable_add_to_cart==false ) {
                $display_add_to_cart_at_all_times = 'no';
            } else {
                if($results_disable_add_to_cart[0][0]=='yes') {
                    $display_add_to_cart_at_all_times = 'yes';
                } else {
                    $display_add_to_cart_at_all_times = 'no';
                }
            }

            // Shipping options are saved here
            wpsc_admin_save_product();
            


            if (isset($_POST['wpStoreCartproduct_name']) && isset($_POST['wpStoreCartproduct_introdescription']) && isset($_POST['wpStoreCartproduct_description']) && isset($_POST['wpStoreCartproduct_thumbnail']) && isset($_POST['wpStoreCartproduct_price'])  && isset($_POST['wpStoreCartproduct_tags']) && isset($_POST['wpStoreCartproduct_category']) && isset($_POST['wpStoreCartproduct_inventory'])) {

                    // Add to Cart on/off for this product?

                    if(isset($_POST['enableproduct_display_add_to_cart_variations']) && $_POST['enableproduct_display_add_to_cart_variations']=='yes') {
                        $display_add_to_cart_at_all_times = 'yes';
                        if($results_disable_add_to_cart==false ) {
                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'yes', 'disableaddtocart', '{$_POST['wpsc-keytoedit']}');");
                        } else {
                            $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'yes' WHERE `type`='disableaddtocart' AND `foreignkey` = {$_POST['wpsc-keytoedit']};");
                        }
                    } else {
                        $display_add_to_cart_at_all_times = 'no';
                        if($display_add_to_cart_at_all_times==false ) {
                            $results = $wpdb->query("INSERT INTO `{$table_name_meta}` (`primkey`, `value`, `type`, `foreignkey`) VALUES (NULL, 'no', 'disableaddtocart', '{$_POST['wpsc-keytoedit']}');");
                        } else {
                            $results = $wpdb->query("UPDATE `{$table_name_meta}` SET `value` = 'no' WHERE `type`='disableaddtocart' AND `foreignkey` = {$_POST['wpsc-keytoedit']};");
                        }
                    }


                    $wpStoreCartproduct_name = esc_attr($_POST['wpStoreCartproduct_name']);
                    $wpStoreCartproduct_introdescription = esc_sql($_POST['wpStoreCartproduct_introdescription']);
                    $wpStoreCartproduct_description = esc_sql($_POST['wpStoreCartproduct_description']);
                    $wpStoreCartproduct_thumbnail = esc_sql($_POST['wpStoreCartproduct_thumbnail']);
                    $wpStoreCartproduct_price = esc_sql($_POST['wpStoreCartproduct_price']);
                    @$wpStoreCartproduct_shipping = esc_sql($_POST['wpStoreCartproduct_shipping']);
                    $wpStoreCartproduct_download = esc_sql($_POST['wpStoreCartproduct_download']);	
                    $timestamp = date('Ymd');
                    $wpStoreCartproduct_tags = esc_sql($_POST['wpStoreCartproduct_tags']);
                    $wpStoreCartproduct_category = esc_sql($_POST['wpStoreCartproduct_category']);
                    $wpStoreCartproduct_inventory = esc_sql($_POST['wpStoreCartproduct_inventory']);
                    $wpStoreCartproduct_useinventory = esc_sql($_POST['wpStoreCartproduct_useinventory']);
                    $wpStoreCartproduct_donation = esc_sql($_POST['wpStoreCartproduct_donation']);
                    $wpStoreCartproduct_weight = 0;
                    $wpStoreCartproduct_length = 0;
                    $wpStoreCartproduct_width = 0;
                    $wpStoreCartproduct_height = 0;
                    $wpStoreCartproduct_discountprice = esc_sql($_POST['wpStoreCartproduct_discountprice']);
                    $wpStoreCartSelectedPage = esc_sql($_POST['wpStoreCartSelectedPage']);
                    $wpStoreCartProductStatus = esc_sql($_POST['wpStoreCartProductStatus']);
                    $cleanKey = esc_sql($_POST['wpsc-keytoedit']);

                    $wpscVariationGrouping = esc_sql($_POST['wpscVariationGrouping']);

                    $wpdb->query("UPDATE `{$wpdb->prefix}wpstorecart_products` SET `options`='{$wpscVariationGrouping}' WHERE `producttype`='variation' AND `postid`='{$cleanKey}';");                    
                    

                    if($wpStoreCartSelectedPage==0) { // Need to add a new page then:
                        // Create our PAGE in draft mode in order to get the POST ID
                        $my_post = array();
                        $my_post['post_title'] = stripslashes($wpStoreCartproduct_name);
                        $my_post['post_type'] = 'page';
                        $my_post['post_content'] = '';
                        $my_post['post_status'] = 'draft';
                        $my_post['post_author'] = 1;
                        $my_post['post_parent'] = $wpStoreCartOptions['mainpage'];

                        // Insert the PAGE into the WP database
                        $thePostID = wp_insert_post( $my_post );	
                        if($thePostID==0) {
                            echo 0;
                            exit();
                        }
                        $wpStoreCartSelectedPage = $thePostID;
                        
                        
                        // Now that we've inserted both the PAGE and the product, let's update and publish our post with the correct content
                        $my_post = array();
                        $my_post['ID'] = $thePostID;
                        $my_post['post_content'] = '[wpstorecart display="product" primkey="'.$cleanKey.'"]';
                        if($wpStoreCartProductStatus=='publish') {
                            $my_post['post_status'] = 'publish';
                        }
                        if($wpStoreCartProductStatus=='draft') {
                            $my_post['post_status'] = 'draft';
                        }                        
                        wp_update_post( $my_post );                            
                        
                        
                    } else {
                        // Now that we've inserted both the PAGE and the product, let's update and publish our post with the correct content
                        $my_post = array();
                        $my_post['ID'] = $wpStoreCartSelectedPage;
                        $my_post['post_content'] = '[wpstorecart display="product" primkey="'.$cleanKey.'"]';
                        if($wpStoreCartProductStatus=='publish') {
                            $my_post['post_status'] = 'publish';
                        }
                        if($wpStoreCartProductStatus=='draft') {
                            $my_post['post_status'] = 'draft';
                        }                        
                        wp_update_post( $my_post );                           
                    }
                    
                    $updateSQL = "
                    UPDATE `{$table_name}` SET 
                    `name` = '{$wpStoreCartproduct_name}', 
                    `introdescription` = '{$wpStoreCartproduct_introdescription}', 
                    `description` = '{$wpStoreCartproduct_description}', 
                    `thumbnail` = '{$wpStoreCartproduct_thumbnail}', 
                    `price` = '{$wpStoreCartproduct_price}', 
                    `shipping` = '{$wpStoreCartproduct_shipping}', 
                    `download` = '{$wpStoreCartproduct_download}', 
                    `tags` = '{$wpStoreCartproduct_tags}', 
                    `category` = '{$wpStoreCartproduct_category}', 
                    `inventory` = '{$wpStoreCartproduct_inventory}',
                    `useinventory` = '{$wpStoreCartproduct_useinventory}',
                    `donation` =  '{$wpStoreCartproduct_donation}',
                    `weight` = '{$wpStoreCartproduct_weight}',
                    `length` = '{$wpStoreCartproduct_length}',
                    `width` = '{$wpStoreCartproduct_width}',
                    `height` = '{$wpStoreCartproduct_height}',
                    `discountprice` = '{$wpStoreCartproduct_discountprice}',
                    `postid` = '{$wpStoreCartSelectedPage}',
                    `status` = '{$wpStoreCartProductStatus}'
                    WHERE `primkey` ={$cleanKey} LIMIT 1 ;				
                    ";

                    $results = $wpdb->query($updateSQL);

                    if($results===false) {
                            echo 0;
                            exit();
                    } else { // If we get this far, we are still successful					
                            echo $wpStoreCartSelectedPage;
                            exit();
                    } 

            }



            $keytoedit=$_POST['wpsc-keytoedit'];	
            $grabrecord = "SELECT * FROM {$table_name} WHERE `primkey`={$keytoedit};";					

            $results = $wpdb->get_results( $grabrecord , ARRAY_A );		
            if(isset($results)) {
                    foreach ($results as $result) {

                            $wpStoreCartproduct_name = $result['name'];
                            $wpStoreCartproduct_introdescription = stripslashes($result['introdescription']);
                            $wpStoreCartproduct_description = stripslashes($result['description']);
                            $wpStoreCartproduct_thumbnail = stripslashes($result['thumbnail']);
                            $wpStoreCartproduct_price = stripslashes($result['price']);
                            @$wpStoreCartproduct_shipping = stripslashes($result['shipping']);
                            $wpStoreCartproduct_download = stripslashes($result['download']);
                            $wpStoreCartproduct_tags = stripslashes($result['tags']);
                            $wpStoreCartproduct_category = stripslashes($result['category']);
                            $wpStoreCartproduct_inventory = stripslashes($result['inventory']);
                            $wpStoreCartproduct_useinventory = stripslashes($result['useinventory']);
                            $wpStoreCartproduct_donation =  stripslashes($result['donation']);
                            $wpStoreCartproduct_weight = stripslashes($result['weight']);
                            $wpStoreCartproduct_length = stripslashes($result['length']);
                            $wpStoreCartproduct_width = stripslashes($result['width']);
                            $wpStoreCartproduct_height = stripslashes($result['height']);
                            $wpStoreCartproduct_discountprice = stripslashes($result['discountprice']);
                            $wpStoreCartSelectedPage = stripslashes($result['postid']);


                    }
            } else {
                echo 0;
                exit();				
            }
    }

    if ($isanedit == false) { // New Products

                    $wpStoreCartproduct_name = 'Product name';
                    $wpStoreCartproduct_introdescription = '';
                    $wpStoreCartproduct_description = '';
                    $wpStoreCartproduct_thumbnail = '';
                    $wpStoreCartproduct_price = 0.00;
                    $wpStoreCartproduct_shipping = 0.00;
                    $wpStoreCartproduct_download = '';
                    $timestamp = date('Ymd');
                    $wpStoreCartproduct_tags = '';
                    $wpStoreCartproduct_category = 0;
                    $wpStoreCartproduct_inventory = 0;
                    $wpStoreCartproduct_useinventory = 0;
                    $wpStoreCartproduct_donation = 0;
                    $wpStoreCartproduct_weight = 0;
                    $wpStoreCartproduct_length = 0;
                    $wpStoreCartproduct_width = 0;
                    $wpStoreCartproduct_height = 0;
                    $wpStoreCartproduct_discountprice = 0;



                    // Create our PAGE in draft mode in order to get the POST ID
                    $my_post = array();
                    $my_post['post_title'] = stripslashes($wpStoreCartproduct_name);
                    $my_post['post_type'] = 'page';
                    $my_post['post_content'] = '';
                    $my_post['post_status'] = 'draft';
                    $my_post['post_author'] = 1;
                    $my_post['post_parent'] = $wpStoreCartOptions['mainpage'];

                    // Insert the PAGE into the WP database
                    $thePostID = wp_insert_post( $my_post );	
                    if($thePostID==0) {
                            echo 0;
                            exit();
                    }


                    // Now insert the product into the wpStoreCart database
                    $insert = "
                    INSERT INTO {$table_name} (`primkey`, `name`, `introdescription`, `description`, `thumbnail`, `price`, `shipping`, `download`, `tags`, `category`, `inventory`, `dateadded`, `postid`, `timesviewed`, `timesaddedtocart`, `timespurchased`, `useinventory`, `donation`, `weight`, `length`, `width`, `height`, `discountprice`, `producttype`, `status`, `options`, `productdesignercss`, `shippingservices`) VALUES
                    (NULL, 
                    '{$wpStoreCartproduct_name}', 
                    '{$wpStoreCartproduct_introdescription}', 
                    '{$wpStoreCartproduct_description}', 
                    '{$wpStoreCartproduct_thumbnail}', 
                    '{$wpStoreCartproduct_price}', 
                    '{$wpStoreCartproduct_shipping}', 
                    '{$wpStoreCartproduct_download}', 
                    '{$wpStoreCartproduct_tags}', 
                    '{$wpStoreCartproduct_category}', 
                    '{$wpStoreCartproduct_inventory}', 
                    '{$timestamp}', 
                    '{$thePostID}',
                    0,
                    0,
                    0,
                    {$wpStoreCartproduct_useinventory},
                    {$wpStoreCartproduct_donation},
                    {$wpStoreCartproduct_weight},
                    {$wpStoreCartproduct_length},
                    {$wpStoreCartproduct_width},
                    {$wpStoreCartproduct_height},
                    '{$wpStoreCartproduct_discountprice}',
                    '',
                    '{$wpStoreCartProductStatus}',
                    '',
                    '',
                    ''
                    );
                    ";					

                    $results = $wpdb->query( $insert );
                    $lastID = $wpdb->insert_id;
                    $keytoedit = $lastID;


                    // Now that we've inserted both the PAGE and the product, let's update and publish our post with the correct content
                    $my_post = array();
                    $my_post['ID'] = $thePostID;
                    $my_post['post_content'] = '[wpstorecart display="product" primkey="'.$lastID.'"]';
                    if($wpStoreCartProductStatus=='publish') {
                        $my_post['post_status'] = 'publish';
                    }
                    if($wpStoreCartProductStatus=='draft') {
                        $my_post['post_status'] = 'draft';
                    }                    
                    wp_update_post( $my_post );   



                    if($results===false) {
                            echo 0;						
                    } else { // If we get this far, we are still successful					
                            echo $thePostID;
                    }  





    }

    
}
?>