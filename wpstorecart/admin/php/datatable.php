<?php
/*
 * Script:    DataTables server-side script for PHP and MySQL
 * Copyright: 2010 - Allan Jardine
 * License:   BSD (3-point)
 */

if(!headers_sent()) {
    @header("Cache-Control: no-cache, must-revalidate"); // HTTP/1.1
    @header("Expires: Sat, 26 Jul 1997 05:00:00 GMT"); // Date in the past
}

if (!function_exists('add_action'))
{
    require_once("../../../../../../wp-config.php");
}

global $wpdb, $current_user;

wp_get_current_user();
if ( 0 == $current_user->ID ) {
    // Not logged in.
} else {

    if (function_exists('current_user_can') && !current_user_can('manage_wpstorecart')) {
            die(__('Unauthorized Access - wpStoreCart', 'wpstorecart'));
    }

    /* Array of database columns which should be read and sent back to DataTables. Use a space where
     * you want to insert a non-database field (for example a counter or static image)
     */
    if($_POST['dbtable']=='wpstorecart_orders') {
        $sTable = $wpdb->prefix."wpstorecart_orders";
        $aColumns = array( 'primkey', 'orderstatus', 'cartcontents', 'paymentprocessor', 'price', 'shipping', 'wpuser', 'email', 'affiliate', 'date' );
    } elseif($_POST['dbtable']=='wpstorecart_alerts') {
        $sTable = $wpdb->prefix."wpstorecart_alerts";
        $aColumns = array( 'primkey', 'title', 'description', 'conditions', 'severity', 'image', 'url', 'qty', 'groupable', 'clearable', 'status', 'userid', 'adminpanel', 'textmessage', 'emailalert', 'desktop' );
    } elseif($_POST['dbtable']=='wpstorecart_coupons') {
        $sTable = $wpdb->prefix."wpstorecart_coupons";
        $aColumns = array( 'primkey', 'code', 'amount', 'percent', 'description', 'product', 'startdate', 'enddate' );
    }          
    
    /* Indexed column (used for fast and accurate table cardinality) */
    $sIndexColumn = "primkey";



    /* 
     * Paging
     */
    $sLimit = "";
    if ( isset( $_POST['iDisplayStart'] ) && $_POST['iDisplayLength'] != '-1' )
    {
            $sLimit = "LIMIT ".esc_sql( $_POST['iDisplayStart'] ).", ".esc_sql( $_POST['iDisplayLength'] );
    }


    /*
     * Ordering
     */
    if ( isset( $_POST['iSortCol_0'] ) )
    {
            $sOrder = "ORDER BY  ";
            for ( $i=0 ; $i<intval( $_POST['iSortingCols'] ) ; $i++ )
            {
                    if ( $_POST[ 'bSortable_'.intval($_POST['iSortCol_'.$i]) ] == "true" )
                    {
                            $sOrder .= $aColumns[ intval( $_POST['iSortCol_'.$i] ) ]."
                                    ".esc_sql( $_POST['sSortDir_'.$i] ) .", ";
                    }
            }

            $sOrder = substr_replace( $sOrder, "", -2 );
            if ( $sOrder == "ORDER BY" )
            {
                    $sOrder = "";
            }
    }


    /* 
     * Filtering
     * NOTE this does not match the built-in DataTables filtering which does it
     * word by word on any field. It's possible to do here, but concerned about efficiency
     * on very large tables, and MySQL's regex functionality is very limited
     */
    $sWhere = "";
    if ( $_POST['sSearch'] != "" )
    {
            $sWhere = "WHERE (";
            for ( $i=0 ; $i<count($aColumns) ; $i++ )
            {
                    $sWhere .= $aColumns[$i]." LIKE '%".esc_sql( $_POST['sSearch'] )."%' OR ";
            }
            $sWhere = substr_replace( $sWhere, "", -3 );
            $sWhere .= ')';
    }

    /* Individual column filtering */
    for ( $i=0 ; $i<count($aColumns) ; $i++ )
    {
            if ( $_POST['bSearchable_'.$i] == "true" && $_POST['sSearch_'.$i] != '' )
            {
                    if ( $sWhere == "" )
                    {
                            $sWhere = "WHERE ";
                    }
                    else
                    {
                            $sWhere .= " AND ";
                    }
                    $sWhere .= $aColumns[$i]." LIKE '%".esc_sql($_POST['sSearch_'.$i])."%' ";
            }
    }


    /*
     * SQL queries
     * Get data to display
     */
    $sQuery = "
            SELECT SQL_CALC_FOUND_ROWS ".str_replace(" , ", " ", implode(", ", $aColumns))."
            FROM   $sTable
            $sWhere
            $sOrder
            $sLimit
    ";
    $rResult = $wpdb->get_results( $sQuery, ARRAY_N );

    /* Data set length after filtering */
    $sQuery = "
            SELECT FOUND_ROWS()
    ";
    $rResultFilterTotal = $wpdb->get_results( $sQuery, ARRAY_N );
    //$aResultFilterTotal = mysql_fetch_array($rResultFilterTotal);
    $iFilteredTotal = $rResultFilterTotal[0];

    /* Total data set length */
    $sQuery = "
            SELECT COUNT(".$sIndexColumn.")
            FROM   $sTable
    ";
    $rResultTotal = $wpdb->get_results( $sQuery, ARRAY_N );
    //$aResultTotal = mysql_fetch_array($rResultTotal);
    $aResultTotal = $rResultTotal;
    $iTotal = $aResultTotal[0];


    /*
     * Output
     */
    $output = NULL;
    $output = array(
            "sEcho" => intval($_POST['sEcho']),
            "iTotalRecords" => $iTotal,
            "iTotalDisplayRecords" => $iFilteredTotal,
            "aaData" => array()
    );

    foreach ($rResult as $aRow ) {
            $row = array();
            for ( $i=0 ; $i<count($aColumns) ; $i++ ) {
                
                $row["DT_RowId"] = 'wpscid-'.$_POST['dbtable'].'-'.$aRow[0]; // Makes the ROW ID contain the table name, PRIMKEY, and column offset

                // This determines the DIV's class.  This class is used to determine the jEditable type
                switch ($aColumns[$i]) {
                    default:
                        $class = 'wpsc-edit-disabled';
                    break;                    
                    case 'date':
                    case 'startdate':
                    case 'enddate':
                        $class = 'wpsc-edit-date';
                    break;
                    case 'amount':
                    case 'percent':
                    case 'qty':
                    case 'price':
                    case 'shipping':
                    case 'code':
                    case 'status':
                    case 'url':
                    case 'severity':
                    case 'title':
                    case 'description':
                    case 'email':
                    case 'paymentprocessor':
                        $class = 'wpsc-edit-text';
                    break;
                    case 'orderstatus':
                        $class = 'wpsc-edit-orderstatus';
                    break;  
                    case 'userid':
                    case 'wpuser':
                    case 'affiliate':
                        $class = 'wpsc-edit-user';
                    break;           
                    case 'groupable':
                    case 'clearable':
                    case 'adminpanel':
                    case 'textmessage': 
                    case 'emailalert': 	
                    case 'desktop': 
                        $class = 'wpsc-booleanyesno';
                    break;
                    case 'product':
                        $class = 'wpsc-edit-product';
                    break;
                    case 'cartcontents':
                        $class = 'wpsc-products-add-remove-order';
                    break;
                    case 'conditions':
                        $class = 'wpsc-alert-conditions';
                    break;                
                }       

                
                if($aColumns[$i] == 'primkey') {
                    if ($_POST['dbtable']=='wpstorecart_orders') {
                        $row[] = '<div class="'.$class.'"><a href="admin.php?page=wpstorecart-invoice&orderNumber='.$aRow[$i].'"><img id="wpsc-view-record-'.$aRow[$i].'" src="'.plugins_url().'/wpstorecart/images/Invoice.png" style="cursor:pointer;float:left;" /></a><img id="wpsc-record-'.$aRow[$i].'" src="'.plugins_url().'/wpstorecart/images/delete.png" style="cursor:pointer;float:left;" onclick="wpscDeleteRecord('.$aRow[$i].', \''.$_POST['dbtable'].'\');" /> '.$aRow[$i].'</div>';
                    } else {
                        $row[] = '<div class="'.$class.'"><img id="wpsc-record-'.$aRow[$i].'" src="'.plugins_url().'/wpstorecart/images/delete.png" style="cursor:pointer;float:left;" onclick="wpscDeleteRecord('.$aRow[$i].', \''.$_POST['dbtable'].'\');" /> '.$aRow[$i].'</div>';
                    }
                } elseif($_POST['dbtable']=='wpstorecart_orders' && $aColumns[$i] == 'wpuser') { // A field of Wordpress guest or user
                    if($aRow[$i]==0) {
                        $row[] = '<div class="'.$class.'">'.__('Guest', 'wpstorecart').'</div>';
                    } else {
                        global $user_info;
                        $user_info = get_userdata($aRow[$i]);
                        $row[] = '<div class="'.$class.'">'.$user_info->user_login.'</div>';
                    }
                } elseif($_POST['dbtable']=='wpstorecart_orders' && $aColumns[$i] == 'affiliate') { // A field of Wordpress guest or user
                    if($aRow[$i]==0) {
                        $row[] = '<div class="'.$class.'">-</div>';
                    } else {
                        global $user_info;
                        $user_info = get_userdata($aRow[$i]);
                        $row[] = '<div class="'.$class.'">'.$user_info->user_login.'</div>';
                    }
                } elseif($_POST['dbtable']=='wpstorecart_orders' && $aColumns[$i] == 'cartcontents') { // A field of purchased products
                    if($aRow[$i]=='0*0' || $aRow[$i]==NULL) {
                        $row[] = '<div class="'.$class.'"><img src="'.plugins_url().'/wpstorecart/images/add.png" style="cursor:pointer;float:right;" onclick="wpscAddItemsToCart('.$aRow[0].');" /> - - -</div>';
                    } else {
                        $row[] = '<div class="'.$class.'"><img src="'.plugins_url().'/wpstorecart/images/add.png" style="cursor:pointer;float:right;" onclick="wpscAddItemsToCart('.$aRow[0].');" />'.wpscSplitOrderIntoProduct($aRow[0], 'edit').'</div>';
                    }
                } elseif($_POST['dbtable']=='wpstorecart_alerts' && $aColumns[$i] == 'userid') { // A field of Wordpress guest or user
                    if($aRow[$i]==0) {
                        $row[] = '<div class="'.$class.'">'.__('Guest', 'wpstorecart').'</div>';
                    } else {
                        global $user_info;
                        $user_info = get_userdata($aRow[$i]);
                        $row[] = '<div class="'.$class.'">'.$user_info->user_login.'</div>';
                    }
                } elseif($_POST['dbtable']=='wpstorecart_alerts' && ($aColumns[$i] == 'groupable' || $aColumns[$i] == 'clearable' || $aColumns[$i] == 'adminpanel' || $aColumns[$i] == 'emailalert' || $aColumns[$i] == 'desktop' || $aColumns[$i] == 'textmessage')) { // A field of Wordpress guest or user
                    if($aRow[$i]==0) {
                        $row[] = '<div class="'.$class.'"><img src="'.plugins_url().'/wpstorecart/images/bullet_red.png" alt="" /> '.__('No', 'wpstorecart').'</div>';
                    } else {
                        $row[] = '<div class="'.$class.'"><img src="'.plugins_url().'/wpstorecart/images/bullet_green.png" alt="" \ />'.__('Yes', 'wpstorecart').'</div>';
                    }
                } elseif($_POST['dbtable']=='wpstorecart_alerts' && $aColumns[$i] == 'image') { // An image
                    $row[] = '<div class="wpsc-edit-alert-image"><img src="'.plugins_url().'/wpstorecart/images/alerts/'.$aRow[$i].'" alt="" style="width:32px;height:32px;max-width:32px;max-height:32px;" /></div>';
                } elseif($_POST['dbtable']=='wpstorecart_coupons' && $aColumns[$i] == 'product') { // A product name
                    if($aRow[$i]==0) {
                        $productname = __('Any &amp; All Products', 'wpstorecart');
                    } else {
                        
                        $grabTheProduct = wpscProductGetProductById($aRow[$i]);
                        
                        if($grabTheProduct['producttype']=='variation' || $grabTheProduct['producttype']=='attribute') {
                            $results3 = $wpdb->get_results(  "SELECT `name` FROM `{$wpdb->prefix}wpstorecart_products` WHERE `primkey`={$grabTheProduct['postid']};" , ARRAY_A );
                            if( @isset($results3[0]['name']) ) {
                                $productname = $results3[0]['name'] .' - '.$grabTheProduct['name'];
                            } else {
                                $productname = $grabTheProduct['name'];
                            }
                        } else {
                            $productname = $grabTheProduct['name'];
                        }
                    }
                    $row[] = '<div class="'.$class.'">'.$productname.'</div>';
                } else {
                
                    /* General output */
                    $row[] = '<div class="'.$class.'">'.$aRow[$i].'</div>';
                }
                
                
                
            }
            $output['aaData'][] = $row;
            $row = NULL;
    }

    echo json_encode( $output );

}
?>