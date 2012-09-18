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
            $table_name33 = $wpdb->prefix . "wpstorecart_meta";
            $grabrecord = "SELECT * FROM `{$table_name33}` WHERE `type`='tax' ORDER BY `primkey` ASC;";

            $results = $wpdb->get_results( $grabrecord , ARRAY_A );
            if(isset($results)) {
                    foreach ($results as $result) {
                        $wpscCalculateTaxes = false;
                        $exploder = explode('||', $result['value']);
                        foreach ($exploder as $exploded) {
                            $exploderInd = explode(',', $exploder[2]);
                            foreach ($exploderInd as $exploderEnd) {
                                if(trim($exploderEnd)==trim(get_the_author_meta("taxstate", wp_get_current_user()->ID))) {
                                    $wpscCalculateTaxes = true;
                                } else {
                                    if (isset($_COOKIE["taxstate"])) {
                                        if($exploderEnd==trim($_COOKIE["taxstate"])) {
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
                                if(trim($exploderEnd)==trim(get_the_author_meta("taxcountries", wp_get_current_user()->ID))) {
                                    $wpscCalculateTaxes = true;
                                } else {
                                    if (isset($_COOKIE["taxcountries"])) {
                                        if($exploderEnd==trim($_COOKIE["taxcountries"])) {
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
        return 0;
    }
}

?>