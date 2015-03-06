<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $current_user;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    global $wpdb;
    
    error_reporting(E_ALL);
    
    $wpStoreCartOptions = get_option('wpStoreCartAdminOptions');
    
    // Grab the POST data
    $wpscProductDesignerCSSFilename = esc_sql($_POST['wpscProductDesignerCSSFilename']);
    $wpscProductDesignerCSS = esc_sql($_POST['wpscProductDesignerCSS']);
    $wpscProductDesignerElementOrder = array(); // Grab the order data

    // Create the output
    parse_str($_POST['wpscProductDesignerElementOrder'], $wpscProductDesignerElementOrder);
    //wpscsort[]=1&wpscsort[]=2&wpscsort[]=3&wpscsort[]=4&wpscsort[]=5&wpscsort[]=6
    $orderComment = '// wpStoreCart Product Designer CSS: ';
    $orderValue = '';
    foreach ($wpscProductDesignerElementOrder['wpscsort'] as $key => $value) {
        $orderValue .= $value.',';
    }
    $orderValue .= '0';
    $orderComment .= $orderValue .'

';
    
    global $wpstorecart_upload_dir;
    $wpStoreCartProductDesignerPath = $wpstorecart_upload_dir.'/themes/main/';             
    
    // Write/create the CSS file
    $wpscProductDesignerFileContents = $orderComment . $wpscProductDesignerCSS;
    $fd = fopen($wpStoreCartProductDesignerPath.$wpscProductDesignerCSSFilename, 'w');
    fwrite($fd, $wpscProductDesignerFileContents);
    fclose($fd);
    
    // Update options
    $wpStoreCartOptions['product_designer_css']=$wpscProductDesignerCSSFilename;
    $wpStoreCartOptions['product_designer_order']=$orderValue;
    update_option('wpStoreCartAdminOptions', $wpStoreCartOptions);
    
    echo $orderValue .'
';
    echo $wpStoreCartProductDesignerPath.$wpscProductDesignerCSSFilename.'
';
echo $wpscProductDesignerFileContents;    
    
}
?>