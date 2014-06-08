<?php


if(!function_exists('wpscWizardTemplate')) {
    function wpscWizardTemplate($id, $submitid, $submitbuttontext, $form, $action, $onsubmit=null) {
        if($onsubmit!=NULL) {
            $onsubmit = ' onsubmit="'.$onsubmit.'" ';
        }
        
        $output = '<form id="'.$id.'" action="'.$action.'" method="post" '.$onsubmit.'>';
        
        $output .= $form;
        
        $output .= '<button type="submit" class="wpsc-wizard-button" id="'.$submitid.'" name="'.$submitid.'">'.$submitbuttontext.'</button>';
        
        $output .= '</form>';
        
        $output .= '<script type="text/javascript">jQuery(document).ready(function() { jQuery("#'.$id.'").formToWizard({ submitButton: "'.$submitid.'" }); Cufon.replace("label"); Cufon.replace(".next"); Cufon.replace(".prev"); Cufon.replace(".wpsc-wizard-button"); });</script>';
        
        return $output;
    }
}



if(!function_exists('wpscWizardCreateDefaultPages')) {
    /**
     * Creates the default main page, checkout page, and orders page
     * @return boolean 
     */
    function wpscWizardCreateDefaultPages() {
        
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        wpscCheckAdminPermissions();

        if(!isset($wpStoreCartOptions['mainpage']) || !is_numeric($wpStoreCartOptions['mainpage']) || $wpStoreCartOptions['mainpage']==0 || $wpStoreCartOptions['mainpage']=='') {
            // Insert the PAGE into the WP database
            $my_post = array();
            $my_post['post_title'] = __('Store', 'wpstorecart');
            $my_post['post_type'] = 'page';
            $my_post['post_author'] = 1;
            $my_post['post_parent'] = 0;
            $my_post['post_content'] = '[wpstorecart]';
            $my_post['post_status'] = 'publish';
            $thePostIDx = wp_insert_post( $my_post, true );

            if(is_wp_error($thePostIDx)) {
                echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>';
                _e("ERROR 4a: wpStoreCart didn't like your data and failed to create a page for it!", "wpstorecart");
                echo '<br /><br />';
                echo $thePostIDx->get_error_messages();
                echo '</p></div></div>';
                die();
            } 
            
            $wpStoreCartOptions['mainpage']=$thePostIDx;
            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
            


        }
        
        if(!isset($wpStoreCartOptions['checkoutpage']) || !is_numeric($wpStoreCartOptions['checkoutpage']) || $wpStoreCartOptions['checkoutpage']==0 || $wpStoreCartOptions['checkoutpage']=='') {
            // Insert the PAGE into the WP database
            $my_post = array();
            $my_post['post_title'] = __('Checkout', 'wpstorecart');
            $my_post['post_type'] = 'page';
            $my_post['post_author'] = 1;
            $my_post['post_parent'] = 0;
            $my_post['post_content'] = '[wpstorecart display=checkout]';
            $my_post['post_status'] = 'publish';
            $thePostIDy = wp_insert_post( $my_post, true );

            if(is_wp_error($thePostIDy)) {
                echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>';
                _e("ERROR 4b: wpStoreCart didn't like your data and failed to create a page for it!", "wpstorecart");
                echo '<br /><br />';
                echo $thePostIDy->get_error_messages();
                echo '</p></div></div>';
                die();
            } 
            
            $wpStoreCartOptions['checkoutpage']=$thePostIDy;
            $wpStoreCartOptions['checkoutpageurl'] = get_permalink($wpStoreCartOptions['checkoutpage']);
            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
            

        }
        
       
        if(!isset($wpStoreCartOptions['orderspage']) || !is_numeric($wpStoreCartOptions['orderspage']) || $wpStoreCartOptions['orderspage']==0 || $wpStoreCartOptions['orderspage']=='') {
            // Insert the PAGE into the WP database
            $my_post = array();
            $my_post['post_title'] = __('Orders', 'wpstorecart');
            $my_post['post_type'] = 'page';
            $my_post['post_author'] = 1;
            $my_post['post_parent'] = 0;
            $my_post['post_content'] = '[wpstorecart display=orders]';
            $my_post['post_status'] = 'publish';
            $thePostIDz = wp_insert_post( $my_post, true );

            if(is_wp_error($thePostIDz)) {
                echo '<div class="ui-widget"><div class="ui-state-error ui-corner-all" style="padding: 0 .7em;"><p><span class="ui-icon ui-icon-alert" style="float: left; margin-right: .3em;"></span>';
                _e("ERROR 4c: wpStoreCart didn't like your data and failed to create a page for it!", "wpstorecart");
                echo '<br /><br />';
                echo $thePostIDz->get_error_messages();
                echo '</p></div></div>';
                die();
            } 
            
            $wpStoreCartOptions['orderspage']=$thePostIDz;
            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
            

        }        
        
    }
    
}




if (!function_exists('wpscAdminPageWizard')) {
    /**
     *
     * The Wizard
     * 
     * @global string $wpsc_current_wizard_title The title of the current Wizard
     * @global string $wpsc_current_wizard_form The form of the current Wizard
     * @global string $wpsc_current_wizard_action The URL where the Wizard will submit the form and perform it's functions
     */
    function wpscAdminPageWizard() {
        global $wpsc_current_wizard_title, $wpsc_current_wizard_form, $wpsc_current_wizard_action;
        
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

        wpscCheckAdminPermissions();
        wpscAdminHeader('Wizard');

        echo '
        <style type="text/css">       
        #wpsc-basic-wizard legend {font-size:36px;}
        #wpsc-basic-wizard label {font-size:20px;}
        .wpsc-wizard-button, .prev, .next { background-color:#86a3bd; padding:5px 10px; color:#fff; text-decoration:none; font-size:36px;}
        .wpsc-wizard-button {border:none;cursor:pointer;background-color:#b0232a;}
        .prev:hover, .next:hover, .wpsc-wizard-button:hover { background-color:#000; text-decoration:none;}
        .prev { float:left;}
        .next, .wpsc-wizard-button { float:right;}
        #steps { list-style:none; width:100%; overflow:hidden; margin:0px; padding:0px;}
        #steps li {font-size:24px; float:left; padding:10px; color:#b0b1b3;}
        #steps li span {font-size:11px; display:block;}
        #steps li.current { color:#000;}                
        </style>
        ';

        if(@isset($_GET['wpsc-wizard'])) {
            
            switch ($_GET['wpsc-wizard']) {
                
                case "all_done":
                    if(@isset($_POST['wpsc-wizard-paypal-accept'])) {
                        if($_POST['wpsc-wizard-manual-accept']=='yes') {
                            $wpStoreCartOptions['allowcheckmoneyorder'] = 'true';
                            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);                     
                            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options                            
                        }
                        if($_POST['wpsc-wizard-manual-accept']=='no') {
                            $wpStoreCartOptions['allowcheckmoneyorder'] = 'false';
                            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);                     
                            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options                            
                        }                        
                        if($_POST['wpsc-wizard-paypal-accept']=='yes') {
                            $wpStoreCartOptions['allowpaypal'] = 'true';
                            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);                     
                            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options
                        }
                        if($_POST['wpsc-wizard-paypal-accept']=='no') {
                            $wpStoreCartOptions['allowpaypal'] = 'false';
                            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);                            
                            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options
                        }
                        $wpStoreCartOptions['paypalemail'] = $_POST['wpsc-wizard-paypal-email'];
                        update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
                        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options

                    }
                    
                    $wpsc_current_wizard_title = __('All Done','wpstorecart');
                    // CURRENT WIZARD FORM
                    $wpsc_current_wizard_form = '
                    <fieldset>
                        <legend>'.__('Thank you','wpstorecart').'</legend>
                        <label for="wpsc-wizard-place-holder">'.__('You have finished all available wizards.','wpstorecart').'</label><br /><br />
                        <input name="wpsc-wizard-place-holder" type="hidden" value="" /><br /> 
            
                    </fieldset>
                    <fieldset>
                        <legend>'.__('Thank you','wpstorecart').'</legend>
                        <label for="wpsc-wizard-place-holder">'.__('You have finished all available wizards.','wpstorecart').'</label><br /><br />
                        <input name="wpsc-wizard-place-holder" type="hidden" value="" /><br /> 
            
                    </fieldset>                    
                    ';                      
                    
                break;
                
                case "payment_wizard":
                    
                    // PROCESS PREVIOUS WIZARD
                    // First, we'll handle any form data submitted from the previous wizard, the SETUP WIZARD
                    if(@isset($_POST['wpsc-wizard-product-types'])) { // if this is set then we do have form data to process
                        if(@$_POST['wpsc-wizard-auto-create']=='yes') { // If we're creating new pages. then this does it here.
                            wpscWizardCreateDefaultPages(); // Pages are automatically created here.
                            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options
                        }
                        if($_POST['wpsc-wizard-product-types']=='mixed') {
                            $wpStoreCartOptions['storetype'] = 'Mixed (physical and digital)';
                            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
                            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options
                        }
                        if($_POST['wpsc-wizard-product-types']=='physical') {
                            $wpStoreCartOptions['storetype'] = 'Physical Goods Only';
                            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);   
                            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options
                        }
                        if($_POST['wpsc-wizard-product-types']=='digital') {
                            $wpStoreCartOptions['storetype'] = 'Digital Goods Only';
                            update_option('wpStoreCartAdminOptions', $wpStoreCartOptions); 
                            $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options
                        }                         
                    }
                    
                    
                    // CURRENT WIZARD TITLE
                    $wpsc_current_wizard_title = __('Payment Wizard','wpstorecart');
                    $wpsc_current_wizard_action = 'admin.php?page=wpstorecart-wizard&wpsc-wizard=all_done';
                    
                    // CURRENT WIZARD FORM
                    $wpsc_current_wizard_form = '
                    <fieldset>
                        <legend>'.__('PayPal','wpstorecart').'</legend>
                        <label for="wpsc-wizard-paypal-email">'.__('wpStoreCart is designed to work PayPal, although other payment gateways are also available.  Do you wish to accept PayPal payments on your website?  Is so, please enter your PayPal email address below:','wpstorecart').'</label><br /><br />
                        '.__('PayPal email address: ', 'wpstorecart').'<input id="wpsc-wizard-paypal-email" name="wpsc-wizard-paypal-email" type="text" value="'.$wpStoreCartOptions['paypalemail'].'" style="width:250px;" /><br />
                        <input name="wpsc-wizard-paypal-accept" type="radio" value="yes" checked="checked" /> '.__('Yes, accept PayPal payments to the above email address.','wpstorecart').'<br /> 
                        <input name="wpsc-wizard-paypal-accept" type="radio" value="no" /> '.__('No, I do not use PayPal at this time.','wpstorecart').' <br />                        
                    </fieldset>';    
                    
                    $wpsc_current_wizard_form .= '
                    <fieldset>
                        <legend>'.__('Manual Payments','wpstorecart').'</legend>
                        <label for="wpsc-wizard-manual-payments">'.__('wpStoreCart can also be configured to accept payments offline, using whatever manual methods you devise.  Examples include Checks, Money Orders, Cash on Delivery (COD).  First, choose whether or not you want to accept payments using the manual payment gateway, and then please provide instructions to your customers on what information they should enter into the comments','wpstorecart').'</label><br /><br />
                        <input name="wpsc-wizard-manual-accept" type="radio" value="yes" /> '.__('Yes, I accept payments manually.','wpstorecart').'<br /> 
                        <input name="wpsc-wizard-manual-accept" type="radio" value="no" checked="checked" /> '.__('No, I do not accept manual payments.','wpstorecart').' <br /><br />
                        '.__('Instructions:','wpstorecart').'<br />
                        <textarea name="wpsc-wizard-manual-payments" style="width:100%;">'.$wpStoreCartOptions['checkmoneyordertext'].'</textarea> <br />                        
                    </fieldset>'; 
                  
                    if(!file_exists(WP_PLUGIN_DIR.'/wpsc-payments-pro/wpsc-payments-pro.php')) {
                        $wpsc_current_wizard_form .= '
                        <fieldset>
                            <legend>'.__('wpsc Payments Pro','wpstorecart').'</legend>
                            <label for="wpsc-wizard-wpsc-payments-pro">'.__('Upgrade wpStoreCart now to enable payment gateways for Authorize.NET, 2CheckOut, Liberty Reserve, Moneybookers/Skrill, and Quickbooks Merchant Services.','wpstorecart').'</label><br /><br />
                            <input name="wpsc-wizard-wpsc-payments-pro" type="radio" value="yes" checked="checked" /> '.__('Yes, upgrade my store so I can accept payment through these providers.','wpstorecart').'<br /> 
                            <input name="wpsc-wizard-wpsc-payments-pro" type="radio" value="no" /> '.__('No, I will not upgrade at this time.','wpstorecart').' <br />                        

                        </fieldset>'; 
                    }
                    
                    $wpsc_current_wizard_form .= '
                    <fieldset>
                        <legend>'.__('Save','wpstorecart').'</legend>
                        <label for="wpsc-wizard-save-setup-wizard">'.__('You have just finished the PAYMENT WIZARD.  If you are satisified with your choices, it is time to save.  Just click the "Save Your Settings" button below to finalize these settings.','wpstorecart').'</label>
                        <br /><br />
                    </fieldset>                
                    ';                     
                    
                break;
                
                case "auto_create_pages": // Automatically create main, checkout, and orders pages
                    wpscWizardCreateDefaultPages(); // Pages are automatically created here.
                    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); // Refresh the options
                    $wpsc_current_wizard_title = __('Pages Automatically Created','wpstorecart');
                    $wpsc_current_wizard_form = '
                    <fieldset>
                        <legend>'.__('Done','wpstorecart').'</legend>
                        <label for="wpsc-wizard-auto-save">'.__('wpStoreCart has automatically created your MAIN PAGE, CHECKOUT PAGE, and ORDER PAGE.  We recommend you continue now by running the SETUP WIZARD.  Click "Save Your Settings" to continue to the SETUP WIZARD.','wpstorecart').'</label>
                        <input id="wpsc-wizard-auto-save" type="hidden" value="" />
                    </fieldset>';                    
                break;
            
                default:
                    wpsc_admin_launch_wizard();
                break;
            }
        } else {
            $wpsc_current_wizard_action = 'admin.php?page=wpstorecart-wizard&wpsc-wizard=payment_wizard';
            $wpsc_current_wizard_title = __('Setup Wizard','wpstorecart');
            $wpsc_current_wizard_form = '
            <fieldset>
                <legend>'.__('Welcome','wpstorecart').'</legend>
                <label for="wpsc-wizard-intro">'.__('Welcome to wpStoreCart!','wpstorecart').'<br /><br />'.__('This is the SETUP WIZARD, which will ask you some questions about your website, and configure wpStoreCart to work best for your needs.','wpstorecart').'<br /><br />'.__('This wizard is optional, but recommended.  You can rerun this SETUP WIZARD at anytime.  To begin, press the NEXT button.  Note that you must complete the wizard and press the "Save Your Settings" button for any changes to take affect.','wpstorecart').'</label>
                <input id="wpsc-wizard-intro" type="hidden" value="" />
            </fieldset>';
        
            if(!isset($wpStoreCartOptions['mainpage']) || !is_numeric($wpStoreCartOptions['mainpage'])) {

                $wpsc_current_wizard_form .= '
                <fieldset>
                    <legend>'.__('Auto Create Pages','wpstorecart').'</legend>
                    <label for="wpsc-wizard-auto-create">'.__('wpStoreCart requires a few pages to be created, namely a MAIN PAGE, a CHECKOUT PAGE, and an ORDERS PAGE.  wpStoreCart can automatically create these pages for you and assign them correctly.  Would you like wpStoreCart to create these pages at the end of the Wizard?','wpstorecart').'</label>
                    <br /><br />
                    <input name="wpsc-wizard-auto-create" type="radio" value="yes" checked="checked" /> '.__('Yes, automatically create these pages for me','wpstorecart').'<br /> 
                    <input name="wpsc-wizard-auto-create" type="radio" value="no" /> '.__('No, I will create these pages, manually insert the shortcodes, and change the settings myself','wpstorecart').' <br />

                </fieldset>                
                ';

            }

            $wpsc_current_wizard_form .= '
            <fieldset>
                <legend>'.__('Physical and/or Digital Products','wpstorecart').'</legend>
                <label for="wpsc-wizard-product-types">'.__('Will your store sell physical goods that are shipped, digital goods that are downloaded, or a combination of both physical and digital products?','wpstorecart').'</label>
                <br /><br />
                <input name="wpsc-wizard-product-types" type="radio" value="mixed" checked="checked" /> '.__('Both Digital &amp; Physical Products','wpstorecart').' (<strong><em>'.__('* Recommend, as it keeps all product options available','wpstorecart').'</em></strong>)<br /> 
                <input name="wpsc-wizard-product-types" type="radio" value="physical" /> '.__('Physical products only  (disables product downloads and other non-physical options)','wpstorecart').'<br /> 
                <input name="wpsc-wizard-product-types" type="radio" value="digital" /> '.__('Digital products only (disables shipping and other non-digital options)','wpstorecart').'<br /><br />

            </fieldset>                
            '; 

            $wpsc_current_wizard_form .= '
            <fieldset>
                <legend>'.__('Save','wpstorecart').'</legend>
                <label for="wpsc-wizard-save-setup-wizard">'.__('You have just finished the SETUP WIZARD.  If you are satisified with your choices, it is time to save and move onto the PAYMENT WIZARD.  Just click the "Save Your Settings" button below to finalize these settings and continue to the PAYMENT WIZARD.    ','wpstorecart').'</label>
                <br /><br />
            </fieldset>                
            ';            
        }
        
        
        echo '
            <div id="main" role="main">
                <div id="content">
                    <div>
 
                        

                        <div class="grid_16">
                            <div class="box">
                                <h2>
                                    '.$wpsc_current_wizard_title.'
                                </h2>
                        

        ';

        echo wpscWizardTemplate('wpsc-basic-wizard', 
                                'wpsc-submit-basic-wizard', 
                                __('Save Your Settings', 'wpstorecart'), 
                                $wpsc_current_wizard_form, 
                                $wpsc_current_wizard_action, 
                                null);
        
        echo '
                            </div>
                        </div>
                        
                    </div>
                    <br style="clear:both;" />



                </div>
                <div class="clear"></div>            
        ';
        
    }
}





?>