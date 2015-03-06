<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpdb, $current_user;

$wpStoreCartOptions = get_option('wpStoreCartAdminOptions');

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    $table_name = $wpdb->prefix . "wpstorecart_categories";

    //error_reporting(E_ALL);
    
    // To edit a previous category
    $isanedit = false;
    if(!isset($_POST['keytoedit'])) {$_POST['keytoedit'] = 0;}
    if ($_POST['keytoedit']!=0 && is_numeric($_POST['keytoedit'])) {
            $isanedit = true;

            if (isset($_POST['wpStoreCartCategory'])) {
                    $wpStoreCartCategory = esc_sql($_POST['wpStoreCartCategory']);
                    $wpStoreCartCategoryParent = esc_sql($_POST['wpStoreCartCategoryParent']);
                    $wpStoreCartproduct_thumbnail = esc_sql($_POST['wpStoreCartproduct_thumbnail']);
                    $wpStoreCartCategoryDescription = $wpdb->prepare($_POST['wpStoreCartCategoryDescription']);
                    $wpStoreCartCategoryPostID = $wpdb->prepare($_POST['wpStoreCartCategoryPostID']);
                    $showtoall = $wpdb->prepare($_POST['showtoall']);
                    $showtowhichgroups = $wpdb->prepare(serialize($_POST['showtowhichgroups']));
                    $discountstoall = $wpdb->prepare($_POST['discountstoall']);
                    $discountstowhichgroups = $wpdb->prepare(serialize($_POST['discountstowhichgroups']));
                    $cleanKey = esc_sql($_POST['keytoedit']);
                    if(!is_numeric($wpStoreCartCategoryParent)) {
                            $wpStoreCartCategoryParent = 0;
                    }
                    if(!is_numeric($wpStoreCartCategoryPostID)) {
                            $wpStoreCartCategoryPostID = 0;
                    }			

                    $updateSQL = "
                    UPDATE `{$table_name}` SET 
                    `parent` = '{$wpStoreCartCategoryParent}', 
                    `category` = '{$wpStoreCartCategory}',
                    `thumbnail` = '{$wpStoreCartproduct_thumbnail}',
                    `description` = '{$wpStoreCartCategoryDescription}',
                    `postid` = '{$wpStoreCartCategoryPostID}',
                    `showtoall` = '{$showtoall}',
                    `showtowhichgroups` = '{$showtowhichgroups}',
                    `discountstoall` = '{$discountstoall}',
                    `discountstowhichgroups` = '{$discountstowhichgroups}'
                    WHERE `primkey` ={$cleanKey} LIMIT 1 ;				
                    ";

                    $results = $wpdb->query($updateSQL);

                    if($results===false) {
                            // ERROR						
                    } else { // If we get this far, we are still successful					
                        echo $cleanKey;
                    } 

            }



            $keytoedit=$_POST['keytoedit'];	
            $grabrecord = "SELECT * FROM {$table_name} WHERE `primkey`={$keytoedit};";					

            $results = $wpdb->get_results( $grabrecord , ARRAY_A );		
            if(isset($results)) {
                    foreach ($results as $result) {

                            $wpStoreCartCategoryParent = stripslashes($result['parent']);
                            $wpStoreCartCategory = stripslashes($result['category']);
                            $wpStoreCartproduct_thumbnail = stripslashes($result['thumbnail']);
                            $wpStoreCartCategoryDescription = stripslashes($result['description']);
                            $wpStoreCartCategoryPostID = stripslashes($result['postid']);						
                            $showtoall = stripslashes($result['showtoall']);
                            $showtowhichgroups = unserialize(stripslashes($result['showtowhichgroups']));
                            $discountstoall = stripslashes($result['discountstoall']);
                            $discountstowhichgroups = unserialize(stripslashes($result['discountstowhichgroups']));
                    }
            } else {
                // ERROR
            }
    }

    if ($isanedit == false) {

            if (isset($_POST['wpStoreCartCategoryParent']) && isset($_POST['wpStoreCartCategory'])) {
                    $wpStoreCartCategoryParent = esc_sql($_POST['wpStoreCartCategoryParent']);
                    $wpStoreCartCategory = esc_sql($_POST['wpStoreCartCategory']);
                    $wpStoreCartproduct_thumbnail = esc_sql($_POST['wpStoreCartproduct_thumbnail']);
                    $wpStoreCartCategoryDescription = esc_sql($_POST['wpStoreCartCategoryDescription']);
                    $wpStoreCartCategoryPostID = esc_sql($_POST['wpStoreCartCategoryPostID']);					
                    $showtoall = esc_sql($_POST['showtoall']);
                    $showtowhichgroups = esc_sql(serialize($_POST['showtowhichgroups']));
                    $discountstoall = esc_sql($_POST['discountstoall']);
                    $discountstowhichgroups = esc_sql(serialize($_POST['discountstowhichgroups']));

                    if(!is_numeric($wpStoreCartCategoryParent)) {
                            $wpStoreCartCategoryParent = 0;
                    }
                    if(!is_numeric($wpStoreCartCategoryPostID)) {
                            $wpStoreCartCategoryPostID = 0;
                    }					

                    // Now insert the category into the wpStoreCart database
                    $insert = "
                    INSERT INTO `{$table_name}` (
                    `primkey` ,
                    `parent` ,
                    `category`,
                    `thumbnail`,
                    `description`,
                    `postid`,
                    `showtoall`,
                    `showtowhichgroups`,
                    `discountstoall`,
                    `discountstowhichgroups`
                    )
                    VALUES (
                    NULL , '{$wpStoreCartCategoryParent}', '{$wpStoreCartCategory}', '{$wpStoreCartproduct_thumbnail}', '{$wpStoreCartCategoryDescription}', '{$wpStoreCartCategoryPostID}', '{$showtoall}', '{$showtowhichgroups}', '{$discountstoall}', '{$discountstowhichgroups}'
                    );
                    ";					

                    $results = $wpdb->query($insert);

                    if($results===false) {
                        // ERROR
                    } else { // If we get this far, we are still successful					
                        echo $wpdb->insert_id;
                    }  

            } else {
                // ERROR
            }



    } 
    
    wpscCalculateCategoryDepth();


}
?>