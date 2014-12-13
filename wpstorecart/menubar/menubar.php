<?php

wpsc_admin_bar(); // Action hook

if(!function_exists('wpscWPAdminBar')) {
    function wpscWPAdminBar() {
        global $wp_admin_bar, $wpdb, $post;
        if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) { 
            return;
        }
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
        if(is_page($wpStoreCartOptions['mainpage'])){
            if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
                $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'?wpStoreCartDesigner=true';
            } else {
                $permalink = get_permalink($wpStoreCartOptions['mainpage']) .'&wpStoreCartDesigner=true';
            }            
            
            $wpsc_admin_bar_menu = '<a href="'.$permalink.'" id="wpsc-tabopener" class="ab-item">'. __('Design Your Store Front','wpstorecart') .'</a>
            <!--[if lt IE 9]>
            <style type="text/css"/>
                #wpsc-tabopener {display:none;}
            </style>
            <![endif]-->                
            ';

            $wp_admin_bar->add_menu( array( 'id' => 'wpsc_admin_bar', 'title' => __( 'wpStoreCart', 'wpstorecart' ), 'href' => FALSE ) );
            $wp_admin_bar->add_menu( array( 'parent' => 'wpsc_admin_bar', 'id' => 'wpsc_admin_bar_menu', 'title' => $wpsc_admin_bar_menu, 'href' => FALSE ) );
        }
        if(is_page() && ($post->post_parent == $wpStoreCartOptions['mainpage'])) {

            if(strpos(get_permalink($post->ID),'?')===false) {
                $permalink = get_permalink($post->ID) .'?wpStoreCartDesigner=true';
            } else {
                $permalink = get_permalink($post->ID) .'&wpStoreCartDesigner=true';
            } 
            
            $wpsc_admin_bar_menu = '<a href="'.$permalink.'" id="wpsc-tabopener" class="ab-item">'. __('Design Your Product Page','wpstorecart') .'</a>
            <!--[if lt IE 9]>
            <style type="text/css"/>
                #wpsc-tabopener {display:none;}
            </style>
            <![endif]-->                
            ';

            $wp_admin_bar->add_menu( array( 'id' => 'wpsc_admin_bar', 'title' => __( 'wpStoreCart', 'wpstorecart' ), 'href' => FALSE ) );
            $wp_admin_bar->add_menu( array( 'parent' => 'wpsc_admin_bar', 'id' => 'wpsc_admin_bar_menu', 'title' => $wpsc_admin_bar_menu, 'href' => FALSE ) );            
            
        }
    }
}

add_action( 'admin_bar_menu', 'wpscWPAdminBar', 1000 );



?>