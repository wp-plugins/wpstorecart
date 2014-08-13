<?php

/**
    *
    * Calculate taxes
    *
    * @global <type> $wpdb
    * @global <type> $wpStoreCartOptions
    */
function wpscCalculateTaxes($theTotal) {
    global $wpdb;

    if(@!isset($_SESSION)) {
        @session_start();
    }    
    
    $fields = wpscGrabCustomRegistrationFields();
    $taxstates = false;
    $taxcountries = false;
    foreach ($fields as $field) {
        $specific_items = explode("||", $field['value']);
            if($specific_items[2]=='taxstates') {
                $taxstates = true;
            }
            if($specific_items[2]=='taxcountries') {
                $taxcountries = true;
            }
    }

    if($taxstates || $taxcountries) {
        // Tax is calculated
        $mastertax = 0.0;
        $taxamount = 0;

        if($taxstates || $taxcountries) {
            $grabrecord = "SELECT * FROM `{$wpdb->prefix}wpstorecart_meta` WHERE `type`='tax' ORDER BY `primkey` ASC;";

            $results = $wpdb->get_results( $grabrecord , ARRAY_A );
            if(isset($results)) {
                    foreach ($results as $result) {
                        $wpscCalculateTaxes = false;
                        $exploder = explode('||', $result['value']);
                        foreach ($exploder as $exploded) {

                            $exploderInd = explode(',', $exploder[2]);
                            foreach ($exploderInd as $exploderEnd) {
                                if(trim($exploderEnd)==trim(get_user_meta( wp_get_current_user()->ID, 'wpsc_taxstates', true ) ) ) {
                                    $wpscCalculateTaxes = true;
                                    
                                } else {
                                    if (isset($_SESSION["wpsc_taxstates"])) {
                                        
                                        if($exploderEnd==trim($_SESSION["wpsc_taxstates"])) {
                                            $wpscCalculateTaxes = true;
                                        }
                                    }
                                }
                            }
                        }


                        if($wpscCalculateTaxes){
                            $mastertax = $mastertax + $exploder[3];
                        }
                        $wpscCalculateTaxes = false;


                        foreach ($exploder as $exploded) {
                            $exploderInd = explode(',', $exploder[1]);
                            foreach ($exploderInd as $exploderEnd) {
                                if(trim($exploderEnd)==trim(get_user_meta( wp_get_current_user()->ID, 'wpsc_taxcountries', true ) ) ) {
                                    $wpscCalculateTaxes = true;
                                } else {
                                    if (isset($_SESSION["wpsc_taxcountries"])) {
                                        if($exploderEnd==trim($_SESSION["wpsc_taxcountries"])) {
                                            $wpscCalculateTaxes = true;
                                        }
                                    }
                                }
                            }
                        }

                        if($wpscCalculateTaxes){
                            $mastertax = $mastertax + $exploder[3];
                        }
                        $wpscCalculateTaxes = false;

                    }
            }

        }


        if($mastertax > 0) {
            $taxamount = $theTotal * ($mastertax /100);

        }
        return number_format($taxamount,2);

    } else {
        // Taxes aren't enabled or are incorrectly configured
        return number_format(0,2);;
    }
}

?>