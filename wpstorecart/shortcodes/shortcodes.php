<?php
if(!function_exists('wpStoreCartMainShortcode')) {
    /**
     *
     * Handles the wpStoreCart shortcode
     * 
     * @param array $atts
     * @param string $content
     * @return string 
     */
    function wpStoreCartMainShortcode($atts, $content = null) {
        global $wpdb, $current_user, $wpstorecart_version;
        
        
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');        

        if(@!isset($_SESSION)) {@session_start();}
        // Clear guest session
        if(@$_GET['wpsc_guest_clear']=='1' || @$_GET['wpsc_guest_clear']==1) {
            @$_SESSION['wpsc_email'] = null;
            unset($_SESSION['wpsc_email']);
            @$_SESSION = array(); 
            if (@ini_get("session.use_cookies")) {
                @$params = session_get_cookie_params();
                @setcookie(session_name(), '', time() - 42000,
                    $params["path"], $params["domain"],
                    $params["secure"], $params["httponly"]
                );
            }            
            @session_destroy();
            @$_SESSION = array();            
        }        
        
        extract(shortcode_atts(array(
                'display' => NULL,
                'primkey' => '0',
                'quantity' => 'unset',
                'usetext' => 'true',
                'usepictures' => 'false',
                'thecategory' => '',
                'displaytype' => '',
                'orderby' => '',
                'ordertype' => '',
                'allowguests' => 'false',
        ), $atts));  


        // This allows the shortcode to override the global setting
        if($usetext=='false') {
            $wpStoreCartOptions['displayTitle']=='false';
        } else {
            $usetext = 'true';
        }

        // Formats the orderby and order type stuff
        if($orderby!='') {
            if($ordertype=='') {$ordertype = 'ASC';}
            $orderby = " ORDER BY `$orderby` $ordertype";
        }

        // Adds page pagination
        if($quantity=='unset') {
            $itemsperpage = $wpStoreCartOptions['itemsperpage'];
            $quantity = $wpStoreCartOptions['itemsperpage'];
        } else {
            $itemsperpage = $quantity;
        }

        // Depreciated in v3.0
        // Adds this shortcode: [wpstorecart displaytype="grid"] and [wpstorecart displaytype="list"]
        if ($displaytype=='list' || $displaytype=='grid') {
            $wpStoreCartOptions['displayType']=$displaytype;
        }


        // Adds this shortcode: [wpstorecart display="orders"]
        if ($display=='orders') {
            $display = NULL;
            $_GET['wpsc']='orders';
        }

        // Adds this shortcode: [wpstorecart display="affiliate"]
        if ($display=='affiliate') {
            $display = NULL;
            $_GET['wpsc']='affiliate';
        }

        // Lists the products in a category
        if (@isset($_GET['wpsc'])) {
            if($_GET['wpsc']=='lc' && @is_numeric($_GET['wpsccat'])){
                $display = 'categories';
                $thecategory = intval($_GET['wpsccat']);
            }
        }
        
        
        
        $output = '';
        switch ($display) {
                default:
                    
                    if($_GET['wpsc']=='manual') {
                        
                        $output .= '<h2>'.__('Order total:', 'wpstorecart').' '.$wpStoreCartOptions['currency_symbol']. $_GET['price'] .$wpStoreCartOptions['currency_symbol_right'].'</h2>';
                        $output .= $wpStoreCartOptions['checkmoneyordertext'];
                        if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                            $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=manualresponse&order='.$_GET['order'];
                        } else {
                            $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=manualresponse&order='.$_GET['order'];
                        }
                        $output .= '<form action="'.$permalink.'" method="post"><textarea class="wpsc-textarea" name="manualresponsetext"></textarea><input type="submit" class="wpsc-button '.$wpStoreCartOptions['button_classes_meta'].'" value="Submit" /> </form>';
                    }
                    if($_GET['wpsc']=='manualresponse') {
                        global $wpstorecart_version;
                        if(is_numeric($_GET['order'])) {
                            $orderNumber = intval($_GET['order']);
                            @$orderText = $wpdb->prepare($_POST['manualresponsetext']);
                            $table_name3 = $wpdb->prefix . "wpstorecart_orders";
                            $sql = "SELECT * FROM `{$table_name3}` WHERE `wpuser`='{$current_user->ID}' AND `primkey`={$orderNumber};";
                            $results = $wpdb->get_results( $sql , ARRAY_A );
                            if(isset($results)) {
                                $table_name3 = $wpdb->prefix . "wpstorecart_meta";
                                $sql = "INSERT INTO `{$table_name3}` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '{$orderText}', 'ordernote', '{$orderNumber}');";
                                $wpdb->query( $sql );
                            }
                            
                            $manual_order_message = '<strong>'.__('Order ID','wpstorecart') .':</strong> '. $results[0]['primkey'] . "<br />";
                            $manual_order_message .= '<strong>'.__('Customer Email','wpstorecart') .':</strong> '. $results[0]['email'] . "<br />";                                 
                            $manual_order_message .= '<strong>'.__('Total Price','wpstorecart') .':</strong> '.$results[0]['price']. "<br />";
                            $manual_order_message .= '<strong>'.__('Products Ordered','wpstorecart') .':</strong> '.wpscSplitOrderIntoProduct($results[0]['primkey']). "<br />";                                                    
                            $manual_order_message .= '<strong>'.__('Customer notes','wpstorecart') .':</strong> '.$orderText . "<br />";
                            
                            // Mail the admin about the manual purchase
                            wpscEmail($wpStoreCartOptions['wpStoreCartEmail'], __('A new manual order was placed.', 'wpstorecart'), $manual_order_message);

                            
                            $output .= wpscMakeEmailTxt($wpStoreCartOptions['success_text']);
                                // Let's send them an email telling them their purchase was successful
                                // In case any of our lines are larger than 70 characters, we should use wordwrap()
                            $message = wordwrap(wpscMakeEmailTxt($wpStoreCartOptions['emailonapproval']) . wpscMakeEmailTxt($devOptions['emailsig']), 70);

                            $headers = 'From: '.$wpStoreCartOptions['wpStoreCartEmail'] . "\r\n" .
                                'Reply-To: ' .$wpStoreCartOptions['wpStoreCartEmail']. "\r\n" .
                                'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;

                            // Send an email when purchase is submitted
                            @ini_set("sendmail_from", $wpStoreCartOptions['wpStoreCartEmail']);
                            if($current_user->ID != 0) {
                                @wp_mail($current_user->user_email, __('Your order has been fulfilled!', 'wpstorecart'), $message, $headers);
                            } else {
                                if(@isset($_SESSION['wpsc_email'])) {
                                    @wp_mail($_SESSION['wpsc_email'], __('Your order has been fulfilled!', 'wpstorecart'), $message, $headers);
                                }
                            }

                            $message = wordwrap(__("A note was added to a recent order. Here is the contents:", 'wpstorecart')."<br /> {$orderText}", 70);

                            $headers = 'From: '.$devOptions['wpStoreCartEmail'] . "\r\n" .
                                'Reply-To: ' .$devOptions['wpStoreCartEmail']. "\r\n" .
                                'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;

                            // Send an email when purchase is submitted
                            @ini_set("sendmail_from", $wpStoreCartOptions['wpStoreCartEmail']);
                            @wp_mail($wpStoreCartOptions['wpStoreCartEmail'], __('A note was added to a recent order!', 'wpstorecart'), $message, $headers);
                        }
                    }                    
                    
                    
                    if(@$_GET['wpsc']=='success') {
                        //$wpsc_piwik = intval(@$_GET['wpsc-piwik']);
                        //if($wpsc_piwik > 0 && $wpStoreCartOptions['piwik_enabled']=='true' && @isset($_GET['wpsc-piwik'])) { // Only execute this code if piwik is enabled
                        //    add_action('wp_footer', function() { wpscPiwikTrackOrderPlaced($wpsc_piwik);}); //Track Piwik ecommerce.  Record the piwik statistics for the order
                        //}
                    } 
                    
                    if(@$_GET['wpsc']=='orders') {
                        
                      
                        $output .= $wpStoreCartOptions['myordersandpurchases'];


                        // ** Here's where we disable the user login system during checkout if registration is not required
                        if ( is_user_logged_in() ) {
                            $isLoggedIn = true;

                        } else {
                            if($wpStoreCartOptions['requireregistration']=='false' || $wpStoreCartOptions['requireregistration']=='disable') {
                                if(@isset($_POST['guest_email'])) {

                                    if((@extension_loaded('gd') && @function_exists('gd_info') && $this->wpscGdCheck())) {
                                        @include_once(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/securimage/securimage.php');
                                        @$securimage = new Securimage();
                                        @$securimage->code_length = rand(3, 5);
                                        @$securimage->num_lines = rand(3, 6);   
                                        if (@$securimage->check($_POST['captcha_code']) == false && (@extension_loaded('gd') && @function_exists('gd_info') && wpscGdCheck() )) {
                                            $output .= __('CAPTCHA failed!', 'wpstorecart').'<br /><br />';
                                        } else {
                                            $_SESSION['wpsc_email'] = esc_sql($_POST['guest_email']);                                                                
                                        } 
                                    }

                                    if(@!is_object($securimage)) { // If captcha failed
                                        $_SESSION['wpsc_email'] = esc_sql($_POST['guest_email']); 
                                    }

                                }
                                if(@isset($_SESSION['wpsc_email'])) {

                                    $isLoggedIn = true;

                                } else {
                                    if(@!isset($securimage)) {
                                        if((@extension_loaded('gd') && @function_exists('gd_info'))) {
                                            @include_once(WP_PLUGIN_DIR.'/wpstorecart/wpstorecart/securimage/securimage.php');
                                            @$securimage = new Securimage();
                                            @$securimage->code_length = rand(3, 5);
                                            @$securimage->num_lines = rand(3, 6);                                                                
                                            if (@$securimage->check($_POST['captcha_code']) == false && (@extension_loaded('gd') && @function_exists('gd_info') && wpscGdCheck())) {
                                                $output .= '<br />'.__('CAPTCHA failed!', 'wpstorecart').'<br /><br />';
                                            } else {
                                                @$_SESSION['wpsc_email'] = esc_sql($_POST['guest_email']);                                                                
                                            } 
                                        }

                                        if(@!is_object($securimage)) { // If captcha failed
                                            @$_SESSION['wpsc_email'] = esc_sql($_POST['guest_email']); 
                                        }                        
                                    }
                                    if((@extension_loaded('gd') && @function_exists('gd_info') && wpscGdCheck())) {
                                        $captcha = '
                                            <form name="wpsc-nonregisterform" id="wpsc-nonregisterform" action="#" method="post">
                                                <img id="wpsc-captcha" src="'.plugins_url().'/wpstorecart/wpstorecart/securimage/securimage_show.php" alt="'.__('CAPTCHA Image', 'wpstorecart').'" /><br />
                                                <input type="text" name="captcha_code" size="10" maxlength="6" /> <a href="#" onclick="document.getElementById(\'wpsc-captcha\').src = \''.plugins_url().'/wpstorecart/wpstorecart/securimage/securimage_show.php?\' + Math.random(); return false">[ '.__('Different Image', 'wpstorecart').' ]</a><br />                                                                        
                                                <label><span>'. $wpStoreCartOptions['email'] .' <ins><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins></span><input type="text" name="guest_email" value="'.@$_SESSION['wpsc_email'].'" /></label>
                                                <input type="submit">
                                            </form>
                                            ';
                                    } else {
                                            $captcha = '
                                            <form name="wpsc-nonregisterform" id="wpsc-nonregisterform" action="#" method="post">
                                                <label><span>'. $wpStoreCartOptions['email'] .' <ins><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins></span><input type="text" name="guest_email" value="'.@$_SESSION['wpsc_email'].'" /></label>
                                                <input type="submit">
                                            </form>
                                            ';                                                            
                                    }
                                    $output .= $captcha;                                                            
                                    $isLoggedIn = false;

                                }
                            } else {
                                $isLoggedIn = false;
                            }
                        }

                        if ( $isLoggedIn == false ) {
                            // Not logged in.
                        } else {
                            // Logged in.
                            $table_name3 = $wpdb->prefix . "wpstorecart_orders";
                            if ( is_user_logged_in()  ) { // for logged in users
                                $sql = "SELECT * FROM `{$table_name3}` WHERE `wpuser`='{$current_user->ID}' ORDER BY `date` DESC;";
                            } else { // For guests
                                $sql = "SELECT * FROM `{$table_name3}` WHERE `wpuser`='0' AND `email`='{$_SESSION['wpsc_email']}' ORDER BY `date` DESC;";
                            }

                            $results = $wpdb->get_results( $sql , ARRAY_A );
                            if(isset($results)) {
                                    $output .= '<table><tr><td>'.__('Order Status', 'wpstorecart').'</td><td>'.__('Date', 'wpstorecart').'</td><td>'.__('Items', 'wpstorecart').'</td><td>'.__('Total Price', 'wpstorecart').'</td></tr>';
                                    foreach ($results as $result) {
                                        $output .= '<tr><td>'.$result['orderstatus'].'</td><td>'.$result['date'].'</td><td>'.wpscSplitOrderIntoProduct($result['primkey'], 'download').'</td><td>'.$result['price'].'</td></tr>';
                                    }
                                    $output .= '</table>';
                            }

                            if ( is_user_logged_in()  ) {

                                if($wpStoreCartOptions['orders_profile']=='editable' || $wpStoreCartOptions['orders_profile']== 'both') {
                                    /* Added 2.3.8 */
                                    global $current_user, $wp_roles;
                                    get_currentuserinfo();

                                    /* Load the registration file. */
                                    //require_once( ABSPATH . WPINC . '/registration.php' ); // Depreciated and not needed
                                    $error = false;

                                    /* If profile was saved, update profile. */
                                    if ( 'POST' == $_SERVER['REQUEST_METHOD'] && !empty( $_POST['action'] ) && $_POST['action'] == 'update-user' ) {

                                        
                                        wpscSaveFields($current_user->id);
                                        
                                        /* Update user password. */
                                        if ( !empty($_POST['pass1'] ) && !empty( $_POST['pass2'] ) ) {
                                            if ( $_POST['pass1'] == $_POST['pass2'] )
                                                wp_update_user( array( 'ID' => $current_user->id, 'user_pass' => esc_attr( $_POST['pass1'] ) ) );
                                            else
                                                $error = __('The passwords you entered do not match.  Your password was not updated.', 'wpstorecart');
                                        }

                                        /* Update user information. */
                                        if ( !empty( $_POST['url'] ) )
                                            update_usermeta( $current_user->id, 'user_url', esc_url( $_POST['url'] ) );
                                        if ( !empty( $_POST['email'] ) )
                                            update_usermeta( $current_user->id, 'user_email', esc_attr( $_POST['email'] ) );
                                        if ( !empty( $_POST['first-name'] ) )
                                            update_usermeta( $current_user->id, 'first_name', esc_attr( $_POST['first-name'] ) );
                                        if ( !empty( $_POST['last-name'] ) )
                                            update_usermeta($current_user->id, 'last_name', esc_attr( $_POST['last-name'] ) );
                                        if ( !empty( $_POST['description'] ) )
                                            update_usermeta( $current_user->id, 'description', esc_attr( $_POST['description'] ) );


                                        if ( $error ) {
                                            $output .= '<p class="error">'.$error.'</p>';
                                        }
                                    }
                                }

                                if($wpStoreCartOptions['orders_profile']=='display' || $wpStoreCartOptions['orders_profile']== 'both') {
                                    $output .= '<br />';
                                    $output .= __('Username', 'wpstorecart') .' :'. $current_user->user_login . '<br />';
                                    $output .= __('Email', 'wpstorecart') .' :'. $current_user->user_email . '<br />';
                                    $output .= __('First name', 'wpstorecart') .' :'. $current_user->user_firstname . '<br />';
                                    $output .= __('Last name', 'wpstorecart') .' :'. $current_user->user_lastname . '<br />';
                                    $output .= __('Display name', 'wpstorecart') .' :'. $current_user->display_name . '<br />';
                                    $output .= __('User ID', 'wpstorecart') .' :'. $current_user->ID . '<br />';
                                    
                                }

                                if($wpStoreCartOptions['orders_profile']=='editable' || $wpStoreCartOptions['orders_profile']== 'both') {
                                    $output .= '<form method="post" id="adduser" action="'. get_permalink().'">
                                        <table>
                                        <tr class="wpsc-profile-form-username">
                                            <td><label for="first-name">'; $output .= __('First Name', 'wpstorecart'); $output .= '</label></td>
                                            <td><input class="text-input" name="first-name" type="text" id="first-name" value="'. get_the_author_meta( 'user_firstname', $current_user->id ).'" /></td>
                                        </tr><!-- .form-username -->
                                        <tr class="wpsc-profile-form-username">
                                            <td><label for="last-name">'; $output .= __('Last Name', 'wpstorecart'); $output .= '</label></td>
                                            <td><input class="text-input" name="last-name" type="text" id="last-name" value="'. get_the_author_meta( 'user_lastname', $current_user->id ) .'" /></td>
                                        </tr><!-- .form-username -->
                                        <tr class="wpsc-profile-form-email">
                                            <td><label for="email">'; $output .= __('E-mail *', 'wpstorecart'); $output .= '</label></td>
                                            <td><input class="text-input" name="email" type="text" id="email" value="'. get_the_author_meta( 'user_email', $current_user->id ).'" /></td>
                                        </tr><!-- .form-email -->
                                        <tr class="wpsc-profile-form-url">
                                            <td><label for="url">'; $output .= __('Website', 'wpstorecart'); $output .= '</label></td>
                                            <td><input class="text-input" name="url" type="text" id="url" value="'. get_the_author_meta( 'user_url', $current_user->id ).'" /></td>
                                        </tr><!-- .form-url -->
                                        <tr class="wpsc-profile-form-password">
                                            <td><label for="pass1">'; $output .= __('Password *', 'wpstorecart'); $output .= ' </label></td>
                                            <td><input class="text-input" name="pass1" type="password" id="pass1" /></td>
                                        </tr><!-- .form-password -->
                                        <tr class="wpsc-profile-form-password">
                                            <td><label for="pass2">'; $output .= __('Repeat Password *', 'wpstorecart'); $output .= '</label></td>
                                            <td><input class="text-input" name="pass2" type="password" id="pass2" /></td>
                                        </tr><!-- .form-password -->
                                        <tr class="wpsc-profile-form-textarea">
                                            <td><label for="description">'; $output .= __('Biographical Information', 'wpstorecart') ; $output .= '</label></td>
                                            <td><textarea name="description" id="description" rows="3" cols="50">'. get_the_author_meta( 'description', $current_user->id ).'</textarea></td>
                                        </tr><!-- .form-textarea -->
                                        ';
                                        $output .= wpscShowCustomRegistrationFields();
                                        $output .= '
                                        <tr class="wpsc-profile-form-submit">
                                            <td></td><td>'. $referer.'
                                            <input name="updateuser" type="submit" id="updateuser" class="submit button" value="'; $output .= __('Update', 'wpstorecart'); ; $output .= '" />
                                            '; $output .= wp_nonce_field( 'update-user' );
                                            $output .= '<input name="action" type="hidden" id="action" value="update-user" />
                                        </td></tr><!-- .form-submit -->
                                        </table>
                                    </form><!-- #adduser -->';
                                }




                            } else {
                                    if((@extension_loaded('gd') && @function_exists('gd_info') && wpscGdCheck())) {
                                        $captcha = '
                                            <form name="wpsc-nonregisterform" id="wpsc-nonregisterform" action="#" method="post">
                                                <img id="wpsc-captcha" src="'.plugins_url().'/wpstorecart/wpstorecart/securimage/securimage_show.php" alt="'.__('CAPTCHA Image', 'wpstorecart').'" /><br />
                                                <input type="text" name="captcha_code" size="10" maxlength="6" /> <a href="#" onclick="document.getElementById(\'wpsc-captcha\').src = \''.plugins_url().'/wpstorecart/wpstorecart/securimage/securimage_show.php?\' + Math.random(); return false">[ '.__('Different Image', 'wpstorecart').' ]</a><br />                                                                        
                                                <label><span>'. $wpStoreCartOptions['email'] .' <ins><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins></span><input type="text" name="guest_email" value="'.$_SESSION['wpsc_email'].'" /></label>
                                                <input type="submit">
                                            </form>
                                            ';
                                    } else {
                                            $captcha = '
                                            <form name="wpsc-nonregisterform" id="wpsc-nonregisterform" action="#" method="post">
                                                <label><span>'. $wpStoreCartOptions['email'] .' <ins><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins></span><input type="text" name="guest_email" value="'.$_SESSION['wpsc_email'].'" /></label>
                                                <input type="submit">
                                            </form>
                                            ';                                                            
                                    }
                                    $output .= $captcha; 


                            }

                        }
                    }                    
                    
                    if($_GET['wpsc']=='affiliate') {

                        global $affiliatemanager, $affiliatesettings, $affiliatepurchases;
                        $affiliatemanager = true;
                        $affiliatesettings['current_user'] = $current_user->ID;
                        $affiliatesettings['available_products']  = NULL;
                        $affiliatesettings['product_urls'] = NULL;
                        $affiliatesettings['minimumAffiliatePayment'] = $wpStoreCartOptions['minimumAffiliatePayment'];
                        $affiliatesettings['minimumDaysBeforePaymentEligable'] = $wpStoreCartOptions['minimumDaysBeforePaymentEligable'];
                        $affiliatesettings['affiliateInstructions'] = $wpStoreCartOptions['affiliateInstructions'];

                        $table_name_products = $wpdb->prefix . "wpstorecart_products";
                        $sql = "SELECT `primkey`, `postid` FROM `{$table_name_products}` WHERE `status`='publish' ORDER BY `primkey` ASC;";
                        $results = $wpdb->get_results( $sql , ARRAY_A );
                        $affiliatesettings['base_url'] = plugins_url();
                        if(isset($results)) {
                            foreach ($results as $result) {
                                $affiliatesettings['available_products'] = $affiliatesettings['available_products'] . $result['primkey'] . ',';
                                $affiliatesettings['product_urls'] = $affiliatesettings['product_urls']  . urlencode(get_permalink($result['postid'])) . '|Z|Z|Z|';
                            }
                            $affiliatesettings['available_products'] = substr($affiliatesettings['available_products'], 0, -1);
                            $affiliatesettings['product_urls'] = substr($affiliatesettings['product_urls'], 0, -7);
                        }
                        $table_name = $wpdb->prefix . "wpstorecart_orders";
                        $table_name_meta = $wpdb->prefix . "wpstorecart_meta";
                        $sql = "SELECT * FROM `{$table_name}`, `{$table_name_meta}` WHERE  `{$table_name}`.`affiliate`='{$affiliatesettings['current_user']}' AND  `{$table_name}`.`orderstatus`='Completed' AND `{$table_name}`.`primkey`=`{$table_name_meta}`.`foreignkey` ORDER BY  `{$table_name}`.`affiliate`,  `{$table_name}`.`date` DESC;";
                        $results = $wpdb->get_results( $sql , ARRAY_A );
                        $icounter = 0;
                        foreach ($results as $result) {
                            global $userinfo2;
                            $affiliatepurchases[$icounter]['cartcontents'] = wpscSplitOrderIntoProduct($result['primkey']);
                            $affiliatepurchases[$icounter]['amountpaid'] = $result['value'];
                            $affiliatepurchases[$icounter]['primkey'] = $result['primkey'];
                            $affiliatepurchases[$icounter]['price'] = $result['price'];
                            $affiliatepurchases[$icounter]['date'] = $result['date'];
                            $affiliatepurchases[$icounter]['orderstatus'] = $result['orderstatus'];
                            $userinfo2 = get_userdata($result['affiliate']);
                            @$affiliatepurchases[$icounter]['affiliateusername'] = $userinfo2->user_login;
                            $icounter++;
                        }
                        @include_once(WP_PLUGIN_DIR.'/wpsc-affiliates-pro/saStoreCartPro/affiliates.pro.php');
                        $output .= @wpscAffiliates();
                        $affiliatemanager = false;
                        
                    }
                    
                    if(@!isset($_GET['wpsc'])) {
                        $output .= wpscProductMainPage();
                    }
                    
                break;
                case 'categories':
                    $output .= wpscProductMainPage($thecategory, true);
                break;
                case 'addtocart': // Add to cart button shortcode, to place add to cart buttons anywhere for any product
                    $output .= wpscProductGetAddToCartButton($primkey);
                break;                
                case 'gallery': // Picture gallery shortcode, to place the gallery of images for a product anywhere
                    $output.= wpscProductGetPictureGallery($primkey);
                break;            
                case 'checkout': // Checkout shortcode =========================================================
                    global $is_checkout;
                    $is_checkout = true;
                    $wpsc_shoppingcart = new wpsc_shoppingcart();
                    $output .= $wpsc_shoppingcart->display_cart();
                break;
                case 'product': // Individual product shortcode =========================================================
                    $output .= wpscProductPage($primkey);
                break;     
                case 'recentproducts': // Recent product shortcode =========================================================
                    $output .= '<div class="wpsc-recent-products">';
                    if(is_numeric($quantity)){
                            $quantity = intval($quantity);
                            $sql = "SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `status`='publish' ORDER BY `dateadded` DESC LIMIT 0, {$quantity};";
                            $results = $wpdb->get_results( $sql , ARRAY_A );
                            if(isset($results)) {
                                    foreach ($results as $result) {
                                            // Group code
                                            $groupDiscount = wpscGroupDiscounts($result['category'], $current_user->ID);
                                            if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {

                                            } else {                                                                
                                                $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                if($usepictures=='true') {
                                                        $output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.stripslashes($result['name']).'" /></a>';
                                                }
                                                if($usetext=='true') {
                                                        $output .= '<p><a href="'.$permalink.'">'.stripslashes($result['name']).'</a></p>';
                                                }
                                            }
                                    }
                            }
                    } else {
                            $output .= '<div class="wpsc-error">'.__('wpStoreCart did not like your recentproducts shortcode!  The quantity field contained non-numeric data. Please fix your page or consult the wpStoreCart documentation for help.', 'wpstorecart').'</div>';
                    }
                    $output .= '</div>';
                break;            
                case 'topproducts': // Top product shortcode =========================================================
                    $output .= '<div class="wpsc-top-products">';
                    if(is_numeric($quantity)){
                            $quantity = intval($quantity);
                            $sql = "SELECT * FROM `{$wpdb->prefix}wpstorecart_products` WHERE `status`='publish' ORDER BY `timespurchased`, `timesaddedtocart`, `timesviewed` DESC LIMIT 0, {$quantity};";
                            $results = $wpdb->get_results( $sql , ARRAY_A );
                            if(isset($results)) {
                                    foreach ($results as $result) {
                                            // Group code
                                            $groupDiscount = wpscGroupDiscounts($result['category'], $current_user->ID);
                                            if ($groupDiscount['can_see_this_category']==false && $wpStoreCartOptions['gd_enable']=='true') {

                                            } else {
                                                // end Group Code                                                                
                                                $permalink = get_permalink( $result['postid'] ); // Grab the permalink based on the post id associated with the product
                                                if($usepictures=='true') {
                                                        $output .= '<a href="'.$permalink.'"><img src="'.$result['thumbnail'].'" alt="'.stripslashes($result['name']).'" /></a>';
                                                }
                                                if($usetext=='true') {
                                                        $output .= '<p><a href="'.$permalink.'">'.stripslashes($result['name']).'</a></p>';
                                                }
                                            }
                                    }
                            }
                    } else {
                            $output .= '<div class="wpsc-error">'.__('wpStoreCart did not like your topproducts shortcode!  The quantity field contained non-numeric data. Please fix your page or consult the wpStoreCart documentation for help.', 'wpstorecart').'</div>';
                    }
                    $output .= '</div>';
                break;                
                case 'haspurchased': // If a person has purchased shortcode =========================================================

                    $table_name99 = $wpdb->prefix . "wpstorecart_orders";
                        if ( 0 == $current_user->ID ) {
                            // Not logged in.
                            if($allowguests=='true') {
                                if($wpStoreCartOptions['requireregistration']=='false' || $wpStoreCartOptions['requireregistration']=='disable') {
                                    if(@isset($_POST['guest_email'])) {
                                        $_SESSION['wpsc_email'] = esc_sql($_POST['guest_email']);
                                    }
                                    if(@isset($_SESSION['wpsc_email'])) {
                                        $isLoggedIn = true;
                                        $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name99}` WHERE `email`='".esc_sql($_SESSION['wpsc_email'])."';";
                                    } else {
                                        $output .= '
                                            <form name="wpsc-nonregisterform" id="wpsc-nonregisterform" action="#" method="post">
                                                <label><span>'. $wpStoreCartOptions['email'] .' <ins><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins></span><input type="text" name="guest_email" value="'.$_SESSION['wpsc_email'].'" /></label>
                                                <input type="submit">
                                            </form>
                                            ';
                                        $isLoggedIn = false;

                                    }
                                } else {
                                    $isLoggedIn = false;
                                }                                                    
                            }
                        } else {
                            // User is logged in, use this SQL:
                            $isLoggedIn = true;
                            $sql = "SELECT `cartcontents`, `orderstatus` FROM `{$table_name99}` WHERE `wpuser`={$current_user->ID};";
                        }    

                        if($isLoggedIn) {


                            $results = $wpdb->get_results( $sql , ARRAY_A );
                            if(isset($results)) {
                                foreach($results as $result) {
                                    $specific_items = explode(",", $result['cartcontents']);
                                    foreach($specific_items as $specific_item) {
                                        if($specific_item != '0*0') { // This is filler, all cart entries contain a 0*0 entry
                                            $current_item = explode('*', $specific_item);
                                            if($primkey!=0) { // If we're looking for a specific product, do this
                                                if(isset($current_item[0]) && $current_item[0]==$primkey && $result['orderstatus']=='Completed') {
                                                        $output .= $content;
                                                        break;
                                                } 
                                            }
                                        }
                                    }
                                    if($primkey==0 && $result['orderstatus']=='Completed') {
                                        $output .= $content;
                                        break;
                                    }                                     
                                }
                            }
                        }
                        break;            
        }

        return do_shortcode($output);
        

    }
}

if(!function_exists('wpscProductMainPageEnqueue')) {
    /**
     * Enqueues the peroper CSS and JS in order to use the Store Front Designer or work with products in general, for both end users and admins
     */
    function wpscProductMainPageEnqueue() {
        global $post, $wpsc_testing_mode;
        
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 
        
        wp_enqueue_script('jquery');
        wp_enqueue_script('wpsc-load-variation', plugins_url() . '/wpstorecart/js/wpstorecart/wpsc-load-variation-big.js');
        
        if($wpsc_testing_mode) {
            wp_enqueue_script('wpsc-console-log', plugins_url() . '/wpstorecart/wpstorecart/debugger/console.log.js');
        }        
        
        if(is_page() && ($post->ID == $wpStoreCartOptions['checkoutpage'])) { // If we're visiting the checkout page, load gritter
            wp_register_style('wpsc-checkout', plugins_url() . '/wpstorecart/css/wpsc-checkout.css');
            wp_enqueue_style('wpsc-checkout');
        }   


        
        if(isset($_GET['wpStoreCartDesigner'])){
            wpscCheckAdminPermissions(); // Only users with manage_wpstorecart should have access
            wp_enqueue_script('jquery-ui-core');
            wp_enqueue_script('jquery-ui-accordion');
            wp_enqueue_script('jquery-ui-tabs');
            wp_enqueue_script('jquery-ui-sortable');
            wp_enqueue_script('jquery-ui-draggable');
            wp_enqueue_script('jquery-ui-droppable');
            wp_enqueue_script('jquery-ui-selectable');
            wp_enqueue_script('jquery-ui-datepicker');
            wp_enqueue_script('jquery-ui-resizable');
            wp_enqueue_script('jquery-ui-dialog'); 
            global $wp_version;
            $wp_clean_version = substr($wp_version, 0, strpos($wp_version, "-"));
            if ( version_compare( $wp_clean_version, '3.6', '>=' ) ) {
                wp_register_style('wpsc-custom', plugins_url() . '/wpstorecart/wpstorecart/admin/css/custom-theme/jquery-ui-1.10.1.custom.css');
            } else {
                wp_register_style('wpsc-custom', plugins_url() . '/wpstorecart/wpstorecart/admin/css/custom-theme/jquery-ui-1.8.13.custom.css');
            }
            wp_enqueue_style('wpsc-custom');    
            wp_register_style('wpsc-designer', plugins_url() . '/wpstorecart/wpstorecart/admin/css/wpsc-designer.css');
            wp_enqueue_style('wpsc-designer');            
            wp_register_style('wpsc-mb-slider-css', plugins_url() . '/wpstorecart/js/jquery.mb.valueSlider-1.0/css/mb.slider.css');
            wp_enqueue_style('wpsc-mb-slider-css');             
            wp_register_style('wpsc-minicolors-css', plugins_url() . '/wpstorecart/wpstorecart/admin/js/miniColors/jquery.miniColors.css');
            wp_enqueue_style('wpsc-minicolors-css');   
            wp_enqueue_script('wpsc-jq-metadata', plugins_url() . '/wpstorecart/js/jquery.mb.valueSlider-1.0/inc/jquery.metadata.js');
            wp_enqueue_script('wpsc-mb-slider', plugins_url() . '/wpstorecart/js/jquery.mb.valueSlider-1.0/inc/jquery.mb.slider.js');
            wp_enqueue_script('wpsc-minicolors', plugins_url() . '/wpstorecart/wpstorecart/admin/js/miniColors/jquery.miniColors.min.js');  
            wp_enqueue_script('wpsc-designer-core', plugins_url() . '/wpstorecart/wpstorecart/admin/js/wpsc-designer-core.js');
            if(is_page($wpStoreCartOptions['mainpage'])){ // if we're editing the main page
                wp_enqueue_script('wpsc-storefront-designer', plugins_url() . '/wpstorecart/wpstorecart/admin/js/wpsc-storefront-designer.js');   
            }
            if(is_page() && ($post->post_parent == $wpStoreCartOptions['mainpage'])) { // If we're editing the single product page
                wp_enqueue_script('wpsc-singleproduct-designer', plugins_url() . '/wpstorecart/wpstorecart/admin/js/wpsc-singleproduct-designer.js');   
            }            
        }
    }
}


add_shortcode('wpstorecart', 'wpStoreCartMainShortcode');
add_shortcode('idbecommerce', 'wpStoreCartMainShortcode');

add_action('wp_enqueue_scripts', 'wpscProductMainPageEnqueue');

?>