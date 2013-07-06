<?php

    /**
     * Action hook fire immediately after the actions are defined.  This is the first hook available.
     */
    function wpsc_loaded() {
        do_action('wpsc_loaded');
    }
    
    /**
     * Action hook for the compatibility file
     */
    function wpsc_compatibility() {
        do_action('wpsc_compatibility');
    }
    
    /**
    * Action hook for the start of the installer file
    */
    function wpsc_installer_check() {
    	do_action('wpsc_installer_check');
    }    

    /**
    * Action hook for installing wpStoreCart 4
    */
    function wpsc_installer_install() {
    	do_action('wpsc_installer_install');
    }

    /**
    * Action hook for upgrading
    */
    function wpsc_installer_upgrade() {
    	do_action('wpsc_installer_upgrade');
    }    
    
    /**
     * Action hook for the admin file
     */    
    function wpsc_admin() {
        do_action('wpsc_admin');
    }
    
    /**
     * Action hook for those who are admins or super admins and are in the dashboard
     */    
    function wpsc_isadmin() {
        do_action('wpsc_isadmin');
    }
    
    /**
     * 
     * Action hook for the wpStoreCart Alert functionality
     */
    function wpsc_alert() {
    	do_action('wpsc_alert');
    }
    
    
    /** Action hook before the admin dashboard menu */    
    function wpsc_admin_menu_before_dashboard() { do_action('wpsc_admin_menu_before_dashboard'); }
    /** Action hook before the admin settings menu */    
    function wpsc_admin_menu_before_settings() { do_action('wpsc_admin_menu_before_settings'); }
    /** Action hook before the admin products menu */    
    function wpsc_admin_menu_before_products() { do_action('wpsc_admin_menu_before_products'); }
    /** Action hook before the admin customers menu */    
    function wpsc_admin_menu_before_customers() { do_action('wpsc_admin_menu_before_customers'); }
    /** Action hook before the admin orders menu */    
    function wpsc_admin_menu_before_orders() { do_action('wpsc_admin_menu_before_orders'); }
    /** Action hook before the admin marketing menu */    
    function wpsc_admin_menu_before_marketing() { do_action('wpsc_admin_menu_before_marketing'); }
    /** Action hook before the admin affiliates menu */    
    function wpsc_admin_menu_before_affiliates() { do_action('wpsc_admin_menu_before_affiliates'); }
    /** Action hook before the admin statistics menu */    
    function wpsc_admin_menu_before_statistics() { do_action('wpsc_admin_menu_before_statistics'); }
    /** Action hook before the admin help menu */    
    function wpsc_admin_menu_before_help() { do_action('wpsc_admin_menu_before_help'); }    
    
    /** Action hook for the admin dashboard menu */    
    function wpsc_admin_menu_inside_dashboard() { do_action('wpsc_admin_menu_inside_dashboard'); }
    /** Action hook for the admin settings menu */    
    function wpsc_admin_menu_inside_settings() { do_action('wpsc_admin_menu_inside_settings'); }
    /** Action hook for the admin products menu */    
    function wpsc_admin_menu_inside_products() { do_action('wpsc_admin_menu_inside_products'); }
    /** Action hook for the admin customers menu */    
    function wpsc_admin_menu_inside_customers() { do_action('wpsc_admin_menu_inside_customers'); }
    /** Action hook for the admin orders menu */    
    function wpsc_admin_menu_inside_orders() { do_action('wpsc_admin_menu_inside_orders'); }
    /** Action hook for the admin marketing menu */    
    function wpsc_admin_menu_inside_marketing() { do_action('wpsc_admin_menu_inside_marketing'); }
    /** Action hook for the admin affiliates menu */    
    function wpsc_admin_menu_inside_affiliates() { do_action('wpsc_admin_menu_inside_affiliates'); }
    /** Action hook for the admin statistics menu */    
    function wpsc_admin_menu_inside_statistics() { do_action('wpsc_admin_menu_inside_statistics'); }
    /** Action hook for the admin help menu */    
    function wpsc_admin_menu_inside_help() { do_action('wpsc_admin_menu_inside_help'); }    
    
    
    /** Action hook for the top of the Edit Specific Products admin panel @since 3.0.14 */
    function wpsc_admin_edit_product_top() { do_action('wpsc_admin_edit_product_top'); }
    /** Action hook directly after the product has been loaded  @since 3.0.14 */
    function wpsc_admin_edit_product_loading() { do_action('wpsc_admin_edit_product_loading'); }    
    /** Action hook directly inside the javascript for the edit product page  @since 3.0.14 */
    function wpsc_admin_edit_product_js() { do_action('wpsc_admin_edit_product_js'); }       
    /** Action hook directly inside the <ul> tag that outputs tab headers  @since 3.0.14 */
    function wpsc_admin_edit_product_tab_header() { do_action('wpsc_admin_edit_product_tab_header'); }      
    /** Action hook to create new tab content  @since 3.0.14 */
    function wpsc_admin_edit_product_tab_contents() { do_action('wpsc_admin_edit_product_tab_contents'); }      
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_before_product_name() { do_action('wpsc_admin_edit_product_table_before_product_name'); }       
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_before_product_intro() { do_action('wpsc_admin_edit_product_table_before_product_intro'); }      
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_before_product_description() { do_action('wpsc_admin_edit_product_table_before_product_description'); }       
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_before_product_price() { do_action('wpsc_admin_edit_product_table_before_product_price'); }     
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_before_product_inventory() { do_action('wpsc_admin_edit_product_table_before_product_inventory'); }      
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_before_product_category() { do_action('wpsc_admin_edit_product_table_before_product_category'); }     
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_before_product_downloads() { do_action('wpsc_admin_edit_product_table_before_product_downloads'); }    
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_before_product_thumbnail() { do_action('wpsc_admin_edit_product_table_before_product_thumbnail'); }    
    /** Action hook to create new content in an existing tab  @since 3.0.14 */
    function wpsc_admin_edit_product_table_after_product_thumbnail() { do_action('wpsc_admin_edit_product_table_after_product_thumbnail'); }     
  
    /**
     * Action hook for the admin bar file
     */    
    function wpsc_admin_bar() {
        do_action('wpsc_admin_bar');
    }    
 
    /**
     * Action hook for when admin settings are saved.  Use this to add additional variables to save
     */
    function wpsc_admin_save_settings() {
        do_action('wpsc_admin_save_settings');
    }     
    
    /**
     * Action hook for the payment options admin page.  Use this to add settings for a new payment gateway
     */
    function wpsc_admin_payment_options_page() {
        do_action('wpsc_admin_payment_options_page');
    } 
    
    
   
    /**
     * Action hook for the payment processing.  Use this to add processing for a new payment gateway
     */
    function wpsc_process_payment_gateways() {
        do_action('wpsc_process_payment_gateways');
    }    
    
    
    
    
    
    
    
    
     
    
    
    
    /**
     * Action hook for the shipping options admin page.  Use this to add settings for a new shipping provider
     */
    function wpsc_admin_shipping_options_page() {
        do_action('wpsc_admin_shipping_options_page');
    }     
  
    // When editing a product, we need to determine if a shipping service has been selected for the product
    function wpsc_admin_shipping_product_load() {
        do_action('wpsc_admin_shipping_product_load');
    }
    
    // When editing a product, we need to display a checkbox to determine if a shipping service has been selected for the product
    function wpsc_admin_shipping_product_checkboxes() {
        do_action('wpsc_admin_shipping_product_checkboxes');
    }    
    
    // When editing a product, we may need to be able to display additional shipping services options
    function wpsc_admin_shipping_product_options() {
        do_action('wpsc_admin_shipping_product_options');
    }       
    
    
    /**
     * Action hook for when an admin saves a product.  Use this to add additional variables to save
     */
    function wpsc_admin_save_product() {
        do_action('wpsc_admin_save_product');
    }        
    
    
    /**
     * Action hook for the launching a custom made wizard, use the global variables $wpsc_current_wizard_title and $wpsc_current_wizard_form to create it
     */
    function wpsc_admin_launch_wizard() {
        do_action('wpsc_admin_launch_wizard');
    }     
    
    /**
     * Action hook for the products class.  This hook is in the constructor, and will fire anytime a new products object is created
     */
    function wpsc_products_init() {
        do_action('wpsc_products_init');
    }
    
    /**
     * Action hook for the products class.  This hook is in the constructor, and will fire anytime a new products object is created
     */
    function wpsc_shoppingcart() {
        do_action('wpsc_shoppingcart');
    }    
    
    
    /**
     * Action hook for things that happen at the end of wpStoreCart's cycle
     */    
    function wpsc_end() {
        do_action('wpsc_end');
    }        
    
    
?>