<?php

if(!function_exists('wpscGrabCustomRegistrationFields')) {
    /**
    * 
    * returns the custom registration data from the database
    *
    * @global object $wpdb
    * @return array 
    */
    function wpscGrabCustomRegistrationFields() {
        global $wpdb;
        $table_name = $wpdb->prefix . "wpstorecart_meta";
        $sql = "SELECT * FROM `{$table_name}` WHERE `type`='requiredinfo' ORDER BY `foreignkey` ASC;";
        $results = $wpdb->get_results( $sql , ARRAY_A ); 
       $wpStoreCartRegistrationFields = $results;
        return $wpStoreCartRegistrationFields;

    }
}

if(!function_exists('wpscAddCustomContactMethod')) {
    /**
    *
    * Outputs an array of custom customer contact information
    *
    * @global object $wpdb
    * @param array $contactmethods
    * @return array
    */
    function wpscAddCustomContactMethod( $contactmethods=NULL ) {
        global $wpdb;

        $fields = wpscGrabCustomRegistrationFields();
        foreach ($fields as $field) {
            $specific_items = explode("||", $field['value']);
                if($specific_items[2]!='separator' && $specific_items[2]!='header' && $specific_items[2]!='text') {
                    $slug = wpscSlug($specific_items[0]);
                    $contactmethods[$slug] = esc_sql($specific_items[0]); // This makes something like this: $contactmethods['address'] = 'Address';
                }

        }

        return $contactmethods;
    }
}


if(!function_exists('wpscProfileCustomContactMethod')) {
    /**
    *
    * Outputs an array of custom customer contact information
    *
    * @global object $wpdb
    * @param array $contactmethods
    * @return array
    */
    function wpscProfileCustomContactMethod( $contactmethods=NULL ) {
        global $wpdb, $current_user;
        $current_user = wp_get_current_user();
        
        if(@!isset($contactmethods)) {
            $contactmethods = array();
        }
        
        if ( 0 == $current_user->ID ) {
            return null;
        } else {

            $user_id = $current_user->ID;
            
            $fields = wpscGrabCustomRegistrationFields();
            foreach ($fields as $field) {
                $specific_items = explode("||", $field['value']);
                    if($specific_items[2]!='separator' && $specific_items[2]!='header' && $specific_items[2]!='text') {
                        $slug = wpscSlug($specific_items[0]);
                        
                        // city shipping
                        if($specific_items[2]=='shippingcity') {
                            $slug = 'wpsc_shipping_city';
                        }                    

                        // Firstname shipping
                        if($specific_items[2]=='firstname') {
                            $slug = 'wpsc_shipping_firstname';
                        }

                        // lastname shipping
                        if($specific_items[2]=='lastname') {
                            $slug =  'wpsc_shipping_lastname';
                        }                

                        // address shipping
                        if($specific_items[2]=='shippingaddress') {
                            $slug =  'wpsc_shipping_address';
                        } 

                        // zipcode shipping
                        if($specific_items[2]=='zipcode') {
                            $slug =  'wpsc_shipping_zipcode';
                        }    

                        // state shipping
                        if($specific_items[2]=='taxstates') {
                            $slug =  'wpsc_taxstates';
                        }     

                        // country shipping
                        if($specific_items[2]=='taxcountries') {
                            $slug =  'wpsc_taxcountries';
                        }                         
                        
                        $contactmethods[$slug] = $slug; // This makes something like this: $contactmethods['address'] = 'Address';
                    }

            }

            return $contactmethods;
        }
    }
}


if(!function_exists('wpscLoadFields')) {
    function wpscLoadFields() {
        global $wpdb;        
        
        if(@!isset($_SESSION)) {
                @session_start();
        }        
        
        if(is_user_logged_in()) {
            $user_id = wp_get_current_user()->ID;
            
            $results = $wpdb->get_results("SELECT * FROM `{$wpdb->prefix}usermeta` WHERE `user_id`='{$user_id}';", ARRAY_A);
            if(@isset($results[0]['umeta_id'])){
                foreach ($results as $result) {
                    $_SESSION[$result['meta_key']] = $result['meta_value'];
                }
            }
            
        } else {
            return false;
        }       
    }
}


if(!function_exists('wpscCountryCodes')) {
    /**
     * Returns the 2 let country code for the $search
     * @param type $search
     * @return type 
     */
    function wpscCountryCodes($search) {
        $countrycodes = array(
        'AF'=>'Afghanistan',
        'AL'=>'Albania',
        'DZ'=>'Algeria',
        'AS'=>'American Samoa',
        'AD'=>'Andorra',
        'AO'=>'Angola',
        'AI'=>'Anguilla',
        'AQ'=>'Antarctica',
        'AG'=>'Antigua And Barbuda',
        'AR'=>'Argentina',
        'AM'=>'Armenia',
        'AW'=>'Aruba',
        'AU'=>'Australia',
        'AT'=>'Austria',
        'AZ'=>'Azerbaijan',
        'BS'=>'Bahamas',
        'BH'=>'Bahrain',
        'BD'=>'Bangladesh',
        'BB'=>'Barbados',
        'BY'=>'Belarus',
        'BE'=>'Belgium',
        'BZ'=>'Belize',
        'BJ'=>'Benin',
        'BM'=>'Bermuda',
        'BT'=>'Bhutan',
        'BO'=>'Bolivia',
        'BA'=>'Bosnia And Herzegovina',
        'BW'=>'Botswana',
        'BV'=>'Bouvet Island',
        'BR'=>'Brazil',
        'IO'=>'British Indian Ocean Territory',
        'BN'=>'Brunei',
        'BG'=>'Bulgaria',
        'BF'=>'Burkina Faso',
        'BI'=>'Burundi',
        'KH'=>'Cambodia',
        'CM'=>'Cameroon',
        'CA'=>'Canada',
        'CV'=>'Cape Verde',
        'KY'=>'Cayman Islands',
        'CF'=>'Central African Republic',
        'TD'=>'Chad',
        'CL'=>'Chile',
        'CN'=>'China',
        'CX'=>'Christmas Island',
        'CC'=>'Cocos (Keeling) Islands',
        'CO'=>'Columbia',
        'KM'=>'Comoros',
        'CG'=>'Congo',
        'CK'=>'Cook Islands',
        'CR'=>'Costa Rica',
        'CI'=>'Cote D\'Ivorie (Ivory Coast)',
        'HR'=>'Croatia (Hrvatska)',
        'CU'=>'Cuba',
        'CY'=>'Cyprus',
        'CZ'=>'Czech Republic',
        'CD'=>'Democratic Republic Of Congo (Zaire)',
        'DK'=>'Denmark',
        'DJ'=>'Djibouti',
        'DM'=>'Dominica',
        'DO'=>'Dominican Republic',
        'TP'=>'East Timor',
        'EC'=>'Ecuador',
        'EG'=>'Egypt',
        'SV'=>'El Salvador',
        'GQ'=>'Equatorial Guinea',
        'ER'=>'Eritrea',
        'EE'=>'Estonia',
        'ET'=>'Ethiopia',
        'FK'=>'Falkland Islands (Malvinas)',
        'FO'=>'Faroe Islands',
        'FJ'=>'Fiji',
        'FI'=>'Finland',
        'FR'=>'France',
        'FX'=>'France, Metropolitan',
        'GF'=>'French Guinea',
        'PF'=>'French Polynesia',
        'TF'=>'French Southern Territories',
        'GA'=>'Gabon',
        'GM'=>'Gambia',
        'GE'=>'Georgia',
        'DE'=>'Germany',
        'GH'=>'Ghana',
        'GI'=>'Gibraltar',
        'GR'=>'Greece',
        'GL'=>'Greenland',
        'GD'=>'Grenada',
        'GP'=>'Guadeloupe',
        'GU'=>'Guam',
        'GT'=>'Guatemala',
        'GN'=>'Guinea',
        'GW'=>'Guinea-Bissau',
        'GY'=>'Guyana',
        'HT'=>'Haiti',
        'HM'=>'Heard And McDonald Islands',
        'HN'=>'Honduras',
        'HK'=>'Hong Kong',
        'HU'=>'Hungary',
        'IS'=>'Iceland',
        'IN'=>'India',
        'ID'=>'Indonesia',
        'IR'=>'Iran',
        'IQ'=>'Iraq',
        'IE'=>'Ireland',
        'IL'=>'Israel',
        'IT'=>'Italy',
        'JM'=>'Jamaica',
        'JP'=>'Japan',
        'JO'=>'Jordan',
        'KZ'=>'Kazakhstan',
        'KE'=>'Kenya',
        'KI'=>'Kiribati',
        'KW'=>'Kuwait',
        'KG'=>'Kyrgyzstan',
        'LA'=>'Laos',
        'LV'=>'Latvia',
        'LB'=>'Lebanon',
        'LS'=>'Lesotho',
        'LR'=>'Liberia',
        'LY'=>'Libya',
        'LI'=>'Liechtenstein',
        'LT'=>'Lithuania',
        'LU'=>'Luxembourg',
        'MO'=>'Macau',
        'MK'=>'Macedonia',
        'MG'=>'Madagascar',
        'MW'=>'Malawi',
        'MY'=>'Malaysia',
        'MV'=>'Maldives',
        'ML'=>'Mali',
        'MT'=>'Malta',
        'MH'=>'Marshall Islands',
        'MQ'=>'Martinique',
        'MR'=>'Mauritania',
        'MU'=>'Mauritius',
        'YT'=>'Mayotte',
        'MX'=>'Mexico',
        'FM'=>'Micronesia',
        'MD'=>'Moldova',
        'MC'=>'Monaco',
        'MN'=>'Mongolia',
        'MS'=>'Montserrat',
        'MA'=>'Morocco',
        'MZ'=>'Mozambique',
        'MM'=>'Myanmar (Burma)',
        'NA'=>'Namibia',
        'NR'=>'Nauru',
        'NP'=>'Nepal',
        'NL'=>'Netherlands',
        'AN'=>'Netherlands Antilles',
        'NC'=>'New Caledonia',
        'NZ'=>'New Zealand',
        'NI'=>'Nicaragua',
        'NE'=>'Niger',
        'NG'=>'Nigeria',
        'NU'=>'Niue',
        'NF'=>'Norfolk Island',
        'KP'=>'North Korea',
        'MP'=>'Northern Mariana Islands',
        'NO'=>'Norway',
        'OM'=>'Oman',
        'PK'=>'Pakistan',
        'PW'=>'Palau',
        'PA'=>'Panama',
        'PG'=>'Papua New Guinea',
        'PY'=>'Paraguay',
        'PE'=>'Peru',
        'PH'=>'Philippines',
        'PN'=>'Pitcairn',
        'PL'=>'Poland',
        'PT'=>'Portugal',
        'PR'=>'Puerto Rico',
        'QA'=>'Qatar',
        'RE'=>'Reunion',
        'RO'=>'Romania',
        'RU'=>'Russia',
        'RW'=>'Rwanda',
        'SH'=>'Saint Helena',
        'KN'=>'Saint Kitts And Nevis',
        'LC'=>'Saint Lucia',
        'PM'=>'Saint Pierre And Miquelon',
        'VC'=>'Saint Vincent And The Grenadines',
        'SM'=>'San Marino',
        'ST'=>'Sao Tome And Principe',
        'SA'=>'Saudi Arabia',
        'SN'=>'Senegal',
        'SC'=>'Seychelles',
        'SL'=>'Sierra Leone',
        'SG'=>'Singapore',
        'SK'=>'Slovak Republic',
        'SI'=>'Slovenia',
        'SB'=>'Solomon Islands',
        'SO'=>'Somalia',
        'ZA'=>'South Africa',
        'GS'=>'South Georgia And South Sandwich Islands',
        'KR'=>'South Korea',
        'ES'=>'Spain',
        'LK'=>'Sri Lanka',
        'SD'=>'Sudan',
        'SR'=>'Suriname',
        'SJ'=>'Svalbard And Jan Mayen',
        'SZ'=>'Swaziland',
        'SE'=>'Sweden',
        'CH'=>'Switzerland',
        'SY'=>'Syria',
        'TW'=>'Taiwan',
        'TJ'=>'Tajikistan',
        'TZ'=>'Tanzania',
        'TH'=>'Thailand',
        'TG'=>'Togo',
        'TK'=>'Tokelau',
        'TO'=>'Tonga',
        'TT'=>'Trinidad And Tobago',
        'TN'=>'Tunisia',
        'TR'=>'Turkey',
        'TM'=>'Turkmenistan',
        'TC'=>'Turks And Caicos Islands',
        'TV'=>'Tuvalu',
        'UG'=>'Uganda',
        'UA'=>'Ukraine',
        'AE'=>'United Arab Emirates',
        'UK'=>'United Kingdom',
        'US'=>'United States',
        'UM'=>'United States Minor Outlying Islands',
        'UY'=>'Uruguay',
        'UZ'=>'Uzbekistan',
        'VU'=>'Vanuatu',
        'VA'=>'Vatican City (Holy See)',
        'VE'=>'Venezuela',
        'VN'=>'Vietnam',
        'VG'=>'Virgin Islands (British)',
        'VI'=>'Virgin Islands (US)',
        'WF'=>'Wallis And Futuna Islands',
        'EH'=>'Western Sahara',
        'WS'=>'Western Samoa',
        'YE'=>'Yemen',
        'YU'=>'Yugoslavia',
        'ZM'=>'Zambia',
        'ZW'=>'Zimbabwe'
        );        
        $code = array_search($search, $countrycodes); 
        return $code;
    }
}

if(!function_exists('wpscShowCustomRegistrationFields')) {
    /**
    *
    * Returns a string contain all the custom form elements that are created from the custom registration fields
    * 
    * @return string 
    */
    function wpscShowCustomRegistrationFields(){
        $wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 

        
        if(@!isset($_SESSION)) {
            @session_start();
        }        
        

        
        wpscLoadFields();
        
        $disable_inline_styles = ' style="float:left;"';
        if($wpStoreCartOptions['disable_inline_styles']=='true') {
            $disable_inline_styles = '';
        }            

        $output = '';
        $fields = wpscGrabCustomRegistrationFields();
        foreach ($fields as $field) {
            $specific_items = explode("||", $field['value']);
                if($specific_items[2]=='input (text)') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="'.wpscSlug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.wpscSlug($specific_items[0])].'" name="'.wpscSlug($specific_items[0]).'" /></td></tr>';
                }
                if($specific_items[2]=='shippingcity') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="wpsc_shipping_city" type="text" size="25" value="'.$_SESSION['wpsc_shipping_city'].'" name="wpsc_shipping_city" /></td></tr>';
                }                
                if($specific_items[2]=='firstname') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="wpsc_shipping_firstname" type="text" size="25" value="'.$_SESSION['wpsc_shipping_firstname'].'" name="wpsc_shipping_firstname" /></td></tr>';
                }
                if($specific_items[2]=='lastname') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="wpsc_shipping_lastname" type="text" size="25" value="'.$_SESSION['wpsc_shipping_lastname'].'" name="wpsc_shipping_lastname" /></td></tr>';
                }                
                if($specific_items[2]=='shippingaddress') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="wpsc_shipping_address" type="text" size="25" value="'.$_SESSION['wpsc_shipping_address'].'" name="wpsc_shipping_address" /></td></tr>';
                }                
                if($specific_items[2]=='input (numeric)') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="'.wpscSlug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.wpscSlug($specific_items[0])].'" name="'.wpscSlug($specific_items[0]).'" /></td></tr>';
                }
                if($specific_items[2]=='zipcode') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="wpsc_shipping_zipcode" type="text" size="22" value="'.$_SESSION['wpsc_shipping_zipcode'].'" name="wpsc_shipping_zipcode" /></td></tr>';
                }                
                if($specific_items[2]=='textarea') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><textarea class="input" id="'.wpscSlug($specific_items[0]).'" name="'.wpscSlug($specific_items[0]).'">'.$_SESSION['wpsc_'.wpscSlug($specific_items[0])].'</textarea></td></tr>';
                }
                if($specific_items[2]=='states' || $specific_items[2]=='taxstates') {
                    if($specific_items[2]=='states') {
                        $prev_val = $_SESSION['wpsc_'.wpscSlug($specific_items[0])];
                    }
                    if($specific_items[2]=='taxstates') {
                        $prev_val = $_SESSION['wpsc_taxstates'];
                    }
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><select class="input" name="'; if ($specific_items[2]=='states'){$output.= wpscSlug($specific_items[0]);} else {$output.='taxstate';} $output.='" class="wpsc-states">
                    <option value="not applicable"'; if($prev_val==''){$output.=' selected="selected" ';}; $output.='>'.__('Other (Non-US)', 'wpstorecart').'</option>
                    <option value="AL"'; if($prev_val=='AL'){$output.=' selected="selected" ';}; $output.='>'.__('Alabama', 'wpstorecart').'</option>
                    <option value="AK"'; if($prev_val=='AK'){$output.=' selected="selected" ';}; $output.='>'.__('Alaska', 'wpstorecart').'</option>
                    <option value="AZ"'; if($prev_val=='AZ'){$output.=' selected="selected" ';}; $output.='>'.__('Arizona', 'wpstorecart').'</option>
                    <option value="AR"'; if($prev_val=='AR'){$output.=' selected="selected" ';}; $output.='>'.__('Arkansas', 'wpstorecart').'</option>
                    <option value="CA"'; if($prev_val=='CA'){$output.=' selected="selected" ';}; $output.='>'.__('California', 'wpstorecart').'</option>
                    <option value="CO"'; if($prev_val=='CO'){$output.=' selected="selected" ';}; $output.='>'.__('Colorado', 'wpstorecart').'</option>
                    <option value="CT"'; if($prev_val=='CT'){$output.=' selected="selected" ';}; $output.='>'.__('Connecticut', 'wpstorecart').'</option>
                    <option value="DE"'; if($prev_val=='DE'){$output.=' selected="selected" ';}; $output.='>'.__('Delaware', 'wpstorecart').'</option>
                    <option value="DC"'; if($prev_val=='DC'){$output.=' selected="selected" ';}; $output.='>'.__('District Of Columbia', 'wpstorecart').'</option>
                    <option value="FL"'; if($prev_val=='FL'){$output.=' selected="selected" ';}; $output.='>'.__('Florida', 'wpstorecart').'</option>
                    <option value="GA"'; if($prev_val=='GA'){$output.=' selected="selected" ';}; $output.='>'.__('Georgia', 'wpstorecart').'</option>
                    <option value="HI"'; if($prev_val=='HI'){$output.=' selected="selected" ';}; $output.='>'.__('Hawaii', 'wpstorecart').'</option>
                    <option value="ID"'; if($prev_val=='ID'){$output.=' selected="selected" ';}; $output.='>'.__('Idaho', 'wpstorecart').'</option>
                    <option value="IL"'; if($prev_val=='IL'){$output.=' selected="selected" ';}; $output.='>'.__('Illinois', 'wpstorecart').'</option>
                    <option value="IN"'; if($prev_val=='IN'){$output.=' selected="selected" ';}; $output.='>'.__('Indiana', 'wpstorecart').'</option>
                    <option value="IA"'; if($prev_val=='IA'){$output.=' selected="selected" ';}; $output.='>'.__('Iowa', 'wpstorecart').'</option>
                    <option value="KS"'; if($prev_val=='KS'){$output.=' selected="selected" ';}; $output.='>'.__('Kansas', 'wpstorecart').'</option>
                    <option value="KY"'; if($prev_val=='KY'){$output.=' selected="selected" ';}; $output.='>'.__('Kentucky', 'wpstorecart').'</option>
                    <option value="LA"'; if($prev_val=='LA'){$output.=' selected="selected" ';}; $output.='>'.__('Louisiana', 'wpstorecart').'</option>
                    <option value="ME"'; if($prev_val=='ME'){$output.=' selected="selected" ';}; $output.='>'.__('Maine', 'wpstorecart').'</option>
                    <option value="MD"'; if($prev_val=='MD'){$output.=' selected="selected" ';}; $output.='>'.__('Maryland', 'wpstorecart').'</option>
                    <option value="MA"'; if($prev_val=='MA'){$output.=' selected="selected" ';}; $output.='>'.__('Massachusetts', 'wpstorecart').'</option>
                    <option value="MI"'; if($prev_val=='MI'){$output.=' selected="selected" ';}; $output.='>'.__('Michigan', 'wpstorecart').'</option>
                    <option value="MN"'; if($prev_val=='MN'){$output.=' selected="selected" ';}; $output.='>'.__('Minnesota', 'wpstorecart').'</option>
                    <option value="MS"'; if($prev_val=='MS'){$output.=' selected="selected" ';}; $output.='>'.__('Mississippi', 'wpstorecart').'</option>
                    <option value="MO"'; if($prev_val=='MO'){$output.=' selected="selected" ';}; $output.='>'.__('Missouri', 'wpstorecart').'</option>
                    <option value="MT"'; if($prev_val=='MT'){$output.=' selected="selected" ';}; $output.='>'.__('Montana', 'wpstorecart').'</option>
                    <option value="NE"'; if($prev_val=='NE'){$output.=' selected="selected" ';}; $output.='>'.__('Nebraska', 'wpstorecart').'</option>
                    <option value="NV"'; if($prev_val=='NV'){$output.=' selected="selected" ';}; $output.='>'.__('Nevada', 'wpstorecart').'</option>
                    <option value="NH"'; if($prev_val=='NH'){$output.=' selected="selected" ';}; $output.='>'.__('New Hampshire', 'wpstorecart').'</option>
                    <option value="NJ"'; if($prev_val=='NJ'){$output.=' selected="selected" ';}; $output.='>'.__('New Jersey', 'wpstorecart').'</option>
                    <option value="NM"'; if($prev_val=='NM'){$output.=' selected="selected" ';}; $output.='>'.__('New Mexico', 'wpstorecart').'</option>
                    <option value="NY"'; if($prev_val=='NY'){$output.=' selected="selected" ';}; $output.='>'.__('New York', 'wpstorecart').'</option>
                    <option value="NC"'; if($prev_val=='NC'){$output.=' selected="selected" ';}; $output.='>'.__('North Carolina', 'wpstorecart').'</option>
                    <option value="ND"'; if($prev_val=='ND'){$output.=' selected="selected" ';}; $output.='>'.__('North Dakota', 'wpstorecart').'</option>
                    <option value="OH"'; if($prev_val=='OH'){$output.=' selected="selected" ';}; $output.='>'.__('Ohio', 'wpstorecart').'</option>
                    <option value="OK"'; if($prev_val=='OK'){$output.=' selected="selected" ';}; $output.='>'.__('Oklahoma', 'wpstorecart').'</option>
                    <option value="OR"'; if($prev_val=='OR'){$output.=' selected="selected" ';}; $output.='>'.__('Oregon', 'wpstorecart').'</option>
                    <option value="PA"'; if($prev_val=='PA'){$output.=' selected="selected" ';}; $output.='>'.__('Pennsylvania', 'wpstorecart').'</option>
                    <option value="RI"'; if($prev_val=='RI'){$output.=' selected="selected" ';}; $output.='>'.__('Rhode Island', 'wpstorecart').'</option>
                    <option value="SC"'; if($prev_val=='SC'){$output.=' selected="selected" ';}; $output.='>'.__('South Carolina', 'wpstorecart').'</option>
                    <option value="SD"'; if($prev_val=='SD'){$output.=' selected="selected" ';}; $output.='>'.__('South Dakota', 'wpstorecart').'</option>
                    <option value="TN"'; if($prev_val=='TN'){$output.=' selected="selected" ';}; $output.='>'.__('Tennessee', 'wpstorecart').'</option>
                    <option value="TX"'; if($prev_val=='TX'){$output.=' selected="selected" ';}; $output.='>'.__('Texas', 'wpstorecart').'</option>
                    <option value="UT"'; if($prev_val=='UT'){$output.=' selected="selected" ';}; $output.='>'.__('Utah', 'wpstorecart').'</option>
                    <option value="VT"'; if($prev_val=='VT'){$output.=' selected="selected" ';}; $output.='>'.__('Vermont', 'wpstorecart').'</option>
                    <option value="VA"'; if($prev_val=='VA'){$output.=' selected="selected" ';}; $output.='>'.__('Virginia', 'wpstorecart').'</option>
                    <option value="WA"'; if($prev_val=='WA'){$output.=' selected="selected" ';}; $output.='>'.__('Washington', 'wpstorecart').'</option>
                    <option value="WV"'; if($prev_val=='WV'){$output.=' selected="selected" ';}; $output.='>'.__('West Virginia', 'wpstorecart').'</option>
                    <option value="WI"'; if($prev_val=='WI'){$output.=' selected="selected" ';}; $output.='>'.__('Wisconsin', 'wpstorecart').'</option>
                    <option value="WY"'; if($prev_val=='WY'){$output.=' selected="selected" ';}; $output.='>'.__('Wyoming', 'wpstorecart').'</option>
                    </select></td></tr>';
                }
                if($specific_items[2]=='countries' || $specific_items[2]=='taxcountries') {
                    if($specific_items[2]=='countries') {
                        $prev_val = $_SESSION['wpsc_'.wpscSlug($specific_items[0])];
                    }
                    if($specific_items[2]=='taxcountries') {
                        $prev_val = $_SESSION['wpsc_taxcountries'];
                    }                    
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><select class="input" name="'; if($specific_items[2]=='countries') {$output.=wpscSlug($specific_items[0]);} else {$output.='taxcountries';} $output.='" class="wpsc-countries">
                    <option value="United States"'; if($prev_val=='United States'){$output.=' selected="selected" ';}; $output.='>'.__('United States', 'wpstorecart').'</option>
                    <option value="Canada"'; if($prev_val=='Canada'){$output.=' selected="selected" ';}; $output.='>'.__('Canada', 'wpstorecart').'</option>
                    <option value="United Kingdom"'; if($prev_val=='United Kingdom'){$output.=' selected="selected" ';}; $output.=' >'.__('United Kingdom', 'wpstorecart').'</option>
                    <option value="Ireland"'; if($prev_val=='Ireland'){$output.=' selected="selected" ';}; $output.=' >'.__('Ireland', 'wpstorecart').'</option>
                    <option value="Australia"'; if($prev_val=='Australia'){$output.=' selected="selected" ';}; $output.=' >'.__('Australia', 'wpstorecart').'</option>
                    <option value="New Zealand"'; if($prev_val=='New Zealand'){$output.=' selected="selected" ';}; $output.=' >'.__('New Zealand', 'wpstorecart').'</option>
                    <option value="Afghanistan"'; if($prev_val=='Afghanistan'){$output.=' selected="selected" ';}; $output.='>'.__('Afghanistan', 'wpstorecart').'</option>
                    <option value="Albania"'; if($prev_val=='Albania'){$output.=' selected="selected" ';}; $output.='>'.__('Albania', 'wpstorecart').'</option>
                    <option value="Algeria"'; if($prev_val=='Algeria'){$output.=' selected="selected" ';}; $output.='>'.__('Algeria', 'wpstorecart').'</option>
                    <option value="American Samoa"'; if($prev_val=='American Samoa'){$output.=' selected="selected" ';}; $output.='>'.__('American Samoa', 'wpstorecart').'</option>
                    <option value="Andorra"'; if($prev_val=='Andorra'){$output.=' selected="selected" ';}; $output.='>'.__('Andorra', 'wpstorecart').'</option>
                    <option value="Angola"'; if($prev_val=='Angola'){$output.=' selected="selected" ';}; $output.='>'.__('Angola', 'wpstorecart').'</option>
                    <option value="Anguilla"'; if($prev_val=='Anguilla'){$output.=' selected="selected" ';}; $output.='>'.__('Anguilla', 'wpstorecart').'</option>
                    <option value="Antarctica"'; if($prev_val=='Antarctica'){$output.=' selected="selected" ';}; $output.='>'.__('Antarctica', 'wpstorecart').'</option>
                    <option value="Antigua and Barbuda"'; if($prev_val=='Antigua and Barbuda'){$output.=' selected="selected" ';}; $output.='>'.__('Antigua and Barbuda', 'wpstorecart').'</option>
                    <option value="Argentina"'; if($prev_val=='Argentina'){$output.=' selected="selected" ';}; $output.='>'.__('Argentina', 'wpstorecart').'</option>
                    <option value="Armenia"'; if($prev_val=='Armenia'){$output.=' selected="selected" ';}; $output.='>'.__('Armenia', 'wpstorecart').'</option>
                    <option value="Aruba"'; if($prev_val=='Aruba'){$output.=' selected="selected" ';}; $output.='>'.__('Aruba', 'wpstorecart').'</option>
                    <option value="Australia"'; if($prev_val=='Australia'){$output.=' selected="selected" ';}; $output.='>'.__('Australia', 'wpstorecart').'</option>
                    <option value="Austria"'; if($prev_val=='Austria'){$output.=' selected="selected" ';}; $output.='>'.__('Austria', 'wpstorecart').'</option>
                    <option value="Azerbaijan"'; if($prev_val=='Azerbaijan'){$output.=' selected="selected" ';}; $output.='>'.__('Azerbaijan', 'wpstorecart').'</option>
                    <option value="Bahamas"'; if($prev_val=='Bahamas'){$output.=' selected="selected" ';}; $output.='>'.__('Bahamas', 'wpstorecart').'</option>
                    <option value="Bahrain"'; if($prev_val=='Bahrain'){$output.=' selected="selected" ';}; $output.='>'.__('Bahrain', 'wpstorecart').'</option>
                    <option value="Bangladesh"'; if($prev_val=='Bangladesh'){$output.=' selected="selected" ';}; $output.='>'.__('Bangladesh', 'wpstorecart').'</option>
                    <option value="Barbados"'; if($prev_val=='Barbados'){$output.=' selected="selected" ';}; $output.='>'.__('Barbados', 'wpstorecart').'</option>
                    <option value="Belarus"'; if($prev_val=='Belarus'){$output.=' selected="selected" ';}; $output.='>'.__('Belarus', 'wpstorecart').'</option>
                    <option value="Belgium"'; if($prev_val=='Belgium'){$output.=' selected="selected" ';}; $output.='>'.__('Belgium', 'wpstorecart').'</option>
                    <option value="Belize"'; if($prev_val=='Belize'){$output.=' selected="selected" ';}; $output.='>'.__('Belize', 'wpstorecart').'</option>
                    <option value="Benin"'; if($prev_val=='Benin'){$output.=' selected="selected" ';}; $output.='>'.__('Benin', 'wpstorecart').'</option>
                    <option value="Bermuda"'; if($prev_val=='Bermuda'){$output.=' selected="selected" ';}; $output.='>'.__('Bermuda', 'wpstorecart').'</option>
                    <option value="Bhutan"'; if($prev_val=='Bhutan'){$output.=' selected="selected" ';}; $output.='>'.__('Bhutan', 'wpstorecart').'</option>
                    <option value="Bolivia"'; if($prev_val=='Bolivia'){$output.=' selected="selected" ';}; $output.='>'.__('Bolivia', 'wpstorecart').'</option>
                    <option value="Bosnia and Herzegovina"'; if($prev_val=='Bosnia and Herzegovina'){$output.=' selected="selected" ';}; $output.='>'.__('Bosnia and Herzegovina', 'wpstorecart').'</option>
                    <option value="Botswana"'; if($prev_val=='Botswana'){$output.=' selected="selected" ';}; $output.='>'.__('Botswana', 'wpstorecart').'</option>
                    <option value="Bouvet Island"'; if($prev_val=='Bouvet Island'){$output.=' selected="selected" ';}; $output.='>'.__('Bouvet Island', 'wpstorecart').'</option>
                    <option value="Brazil"'; if($prev_val=='Brazil'){$output.=' selected="selected" ';}; $output.='>'.__('Brazil', 'wpstorecart').'</option>
                    <option value="British Indian Ocean Territory"'; if($prev_val=='British Indian Ocean Territory'){$output.=' selected="selected" ';}; $output.='>'.__('British Indian Ocean Territory', 'wpstorecart').'</option>
                    <option value="Brunei Darussalam"'; if($prev_val=='Brunei Darussalam'){$output.=' selected="selected" ';}; $output.='>'.__('Brunei Darussalam', 'wpstorecart').'</option>
                    <option value="Bulgaria"'; if($prev_val=='Bulgaria'){$output.=' selected="selected" ';}; $output.='>'.__('Bulgaria', 'wpstorecart').'</option>
                    <option value="Burkina Faso"'; if($prev_val=='Burkina Faso'){$output.=' selected="selected" ';}; $output.='>'.__('Burkina Faso', 'wpstorecart').'</option>
                    <option value="Burundi"'; if($prev_val=='Burundi'){$output.=' selected="selected" ';}; $output.='>'.__('Burundi', 'wpstorecart').'</option>
                    <option value="Cambodia"'; if($prev_val=='Cambodia'){$output.=' selected="selected" ';}; $output.='>'.__('Cambodia', 'wpstorecart').'</option>
                    <option value="Cameroon"'; if($prev_val=='Cameroon'){$output.=' selected="selected" ';}; $output.='>'.__('Cameroon', 'wpstorecart').'</option>
                    <option value="Canada"'; if($prev_val=='Canada'){$output.=' selected="selected" ';}; $output.='>'.__('Canada', 'wpstorecart').'</option>
                    <option value="Cape Verde"'; if($prev_val=='Cape Verde'){$output.=' selected="selected" ';}; $output.='>'.__('Cape Verde', 'wpstorecart').'</option>
                    <option value="Cayman Islands"'; if($prev_val=='Cayman Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Cayman Islands', 'wpstorecart').'</option>
                    <option value="Central African Republic"'; if($prev_val=='Central African Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Central African Republic', 'wpstorecart').'</option>
                    <option value="Chad"'; if($prev_val=='Chad'){$output.=' selected="selected" ';}; $output.='>'.__('Chad', 'wpstorecart').'</option>
                    <option value="Chile"'; if($prev_val=='Chile'){$output.=' selected="selected" ';}; $output.='>'.__('Chile', 'wpstorecart').'</option>
                    <option value="China"'; if($prev_val=='China'){$output.=' selected="selected" ';}; $output.='>'.__('China', 'wpstorecart').'</option>
                    <option value="Christmas Island"'; if($prev_val=='Christmas Island'){$output.=' selected="selected" ';}; $output.='>'.__('Christmas Island', 'wpstorecart').'</option>
                    <option value="Cocos (Keeling) Islands"'; if($prev_val=='Cocos (Keeling) Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Cocos (Keeling) Islands', 'wpstorecart').'</option>
                    <option value="Colombia"'; if($prev_val=='Colombia'){$output.=' selected="selected" ';}; $output.='>'.__('Colombia', 'wpstorecart').'</option>
                    <option value="Comoros"'; if($prev_val=='Comoros'){$output.=' selected="selected" ';}; $output.='>'.__('Comoros', 'wpstorecart').'</option>
                    <option value="Congo"'; if($prev_val=='Congo'){$output.=' selected="selected" ';}; $output.='>'.__('Congo', 'wpstorecart').'</option>
                    <option value="Congo, The Democratic Republic of The"'; if($prev_val=='Congo, The Democratic Republic of The'){$output.=' selected="selected" ';}; $output.='>'.__('Congo, The Democratic Republic of The', 'wpstorecart').'</option>
                    <option value="Cook Islands"'; if($prev_val=='Cook Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Cook Islands', 'wpstorecart').'</option>
                    <option value="Costa Rica"'; if($prev_val=='Costa Rica'){$output.=' selected="selected" ';}; $output.='>'.__('Costa Rica', 'wpstorecart').'</option>
                    <option value="Cote D\'ivoire"'; if($prev_val=='Cote D\'ivoire'){$output.=' selected="selected" ';}; $output.='>'.__('Cote D\'ivoire', 'wpstorecart').'</option>
                    <option value="Croatia"'; if($prev_val=='Croatia'){$output.=' selected="selected" ';}; $output.='>'.__('Croatia', 'wpstorecart').'</option>
                    <option value="Cuba"'; if($prev_val=='Cuba'){$output.=' selected="selected" ';}; $output.='>'.__('Cuba', 'wpstorecart').'</option>
                    <option value="Cyprus"'; if($prev_val=='Cyprus'){$output.=' selected="selected" ';}; $output.='>'.__('Cyprus', 'wpstorecart').'</option>
                    <option value="Czech Republic"'; if($prev_val=='Czech Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Czech Republic', 'wpstorecart').'</option>
                    <option value="Denmark"'; if($prev_val=='Denmark'){$output.=' selected="selected" ';}; $output.='>'.__('Denmark', 'wpstorecart').'</option>
                    <option value="Djibouti"'; if($prev_val=='Djibouti'){$output.=' selected="selected" ';}; $output.='>'.__('Djibouti', 'wpstorecart').'</option>
                    <option value="Dominica"'; if($prev_val=='Dominica'){$output.=' selected="selected" ';}; $output.='>'.__('Dominica', 'wpstorecart').'</option>
                    <option value="Dominican Republic"'; if($prev_val=='Dominican Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Dominican Republic', 'wpstorecart').'</option>
                    <option value="Ecuador"'; if($prev_val=='Ecuador'){$output.=' selected="selected" ';}; $output.='>'.__('Ecuador', 'wpstorecart').'</option>
                    <option value="Egypt"'; if($prev_val=='Egypt'){$output.=' selected="selected" ';}; $output.='>'.__('Egypt', 'wpstorecart').'</option>
                    <option value="El Salvador"'; if($prev_val=='El Salvador'){$output.=' selected="selected" ';}; $output.='>'.__('El Salvador', 'wpstorecart').'</option>
                    <option value="Equatorial Guinea"'; if($prev_val=='Equatorial Guinea'){$output.=' selected="selected" ';}; $output.='>'.__('Equatorial Guinea', 'wpstorecart').'</option>
                    <option value="Eritrea"'; if($prev_val=='Eritrea'){$output.=' selected="selected" ';}; $output.='>'.__('Eritrea', 'wpstorecart').'</option>
                    <option value="Estonia"'; if($prev_val=='Estonia'){$output.=' selected="selected" ';}; $output.='>'.__('Estonia', 'wpstorecart').'</option>
                    <option value="Ethiopia"'; if($prev_val=='Ethiopia'){$output.=' selected="selected" ';}; $output.='>'.__('Ethiopia', 'wpstorecart').'</option>
                    <option value="Falkland Islands (Malvinas)"'; if($prev_val=='Falkland Islands (Malvinas)'){$output.=' selected="selected" ';}; $output.='>'.__('Falkland Islands (Malvinas)', 'wpstorecart').'</option>
                    <option value="Faroe Islands"'; if($prev_val=='Faroe Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Faroe Islands', 'wpstorecart').'</option>
                    <option value="Fiji"'; if($prev_val=='Fiji'){$output.=' selected="selected" ';}; $output.='>'.__('Fiji', 'wpstorecart').'</option>
                    <option value="Finland"'; if($prev_val=='Finland'){$output.=' selected="selected" ';}; $output.='>'.__('Finland', 'wpstorecart').'</option>
                    <option value="France"'; if($prev_val=='France'){$output.=' selected="selected" ';}; $output.='>'.__('France', 'wpstorecart').'</option>
                    <option value="French Guiana"'; if($prev_val=='French Guiana'){$output.=' selected="selected" ';}; $output.='>'.__('French Guiana', 'wpstorecart').'</option>
                    <option value="French Polynesia"'; if($prev_val=='French Polynesia'){$output.=' selected="selected" ';}; $output.='>'.__('French Polynesia', 'wpstorecart').'</option>
                    <option value="French Southern Territories"'; if($prev_val=='French Southern Territories'){$output.=' selected="selected" ';}; $output.='>'.__('French Southern Territories', 'wpstorecart').'</option>
                    <option value="Gabon"'; if($prev_val=='Gabon'){$output.=' selected="selected" ';}; $output.='>'.__('Gabon', 'wpstorecart').'</option>
                    <option value="Gambia"'; if($prev_val=='Gambia'){$output.=' selected="selected" ';}; $output.='>'.__('Gambia', 'wpstorecart').'</option>
                    <option value="Georgia"'; if($prev_val=='Georgia'){$output.=' selected="selected" ';}; $output.='>'.__('Georgia', 'wpstorecart').'</option>
                    <option value="Germany"'; if($prev_val=='Germany'){$output.=' selected="selected" ';}; $output.='>'.__('Germany', 'wpstorecart').'</option>
                    <option value="Ghana"'; if($prev_val=='Ghana'){$output.=' selected="selected" ';}; $output.='>'.__('Ghana', 'wpstorecart').'</option>
                    <option value="Gibraltar"'; if($prev_val=='Gibraltar'){$output.=' selected="selected" ';}; $output.='>'.__('Gibraltar', 'wpstorecart').'</option>
                    <option value="Greece"'; if($prev_val=='Greece'){$output.=' selected="selected" ';}; $output.='>'.__('Greece', 'wpstorecart').'</option>
                    <option value="Greenland"'; if($prev_val=='Greenland'){$output.=' selected="selected" ';}; $output.='>'.__('Greenland', 'wpstorecart').'</option>
                    <option value="Grenada"'; if($prev_val=='Grenada'){$output.=' selected="selected" ';}; $output.='>'.__('Grenada', 'wpstorecart').'</option>
                    <option value="Guadeloupe"'; if($prev_val=='Guadeloupe'){$output.=' selected="selected" ';}; $output.='>'.__('Guadeloupe', 'wpstorecart').'</option>
                    <option value="Guam"'; if($prev_val=='Guam'){$output.=' selected="selected" ';}; $output.='>'.__('Guam', 'wpstorecart').'</option>
                    <option value="Guatemala"'; if($prev_val=='Guatemala'){$output.=' selected="selected" ';}; $output.='>'.__('Guatemala', 'wpstorecart').'</option>
                    <option value="Guinea"'; if($prev_val=='Guinea'){$output.=' selected="selected" ';}; $output.='>'.__('Guinea', 'wpstorecart').'</option>
                    <option value="Guinea-bissau"'; if($prev_val=='Guinea-bissau'){$output.=' selected="selected" ';}; $output.='>'.__('Guinea-bissau', 'wpstorecart').'</option>
                    <option value="Guyana"'; if($prev_val=='Guyana'){$output.=' selected="selected" ';}; $output.='>'.__('Guyana', 'wpstorecart').'</option>
                    <option value="Haiti"'; if($prev_val=='Haiti'){$output.=' selected="selected" ';}; $output.='>'.__('Haiti', 'wpstorecart').'</option>
                    <option value="Heard Island and Mcdonald Islands"'; if($prev_val=='Heard Island and Mcdonald Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Heard Island and Mcdonald Islands', 'wpstorecart').'</option>
                    <option value="Holy See (Vatican City State)"'; if($prev_val=='Holy See (Vatican City State)'){$output.=' selected="selected" ';}; $output.='>'.__('Holy See (Vatican City State)', 'wpstorecart').'</option>
                    <option value="Honduras"'; if($prev_val=='Honduras'){$output.=' selected="selected" ';}; $output.='>'.__('Honduras', 'wpstorecart').'</option>
                    <option value="Hong Kong"'; if($prev_val=='Hong Kong'){$output.=' selected="selected" ';}; $output.='>'.__('Hong Kong', 'wpstorecart').'</option>
                    <option value="Hungary"'; if($prev_val=='Hungary'){$output.=' selected="selected" ';}; $output.='>'.__('Hungary', 'wpstorecart').'</option>
                    <option value="Iceland"'; if($prev_val=='Iceland'){$output.=' selected="selected" ';}; $output.='>'.__('Iceland', 'wpstorecart').'</option>
                    <option value="India"'; if($prev_val=='India'){$output.=' selected="selected" ';}; $output.='>'.__('India', 'wpstorecart').'</option>
                    <option value="Indonesia"'; if($prev_val=='Indonesia'){$output.=' selected="selected" ';}; $output.='>'.__('Indonesia', 'wpstorecart').'</option>
                    <option value="Iran, Islamic Republic of"'; if($prev_val=='Iran, Islamic Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Iran, Islamic Republic of', 'wpstorecart').'</option>
                    <option value="Iraq"'; if($prev_val=='Iraq'){$output.=' selected="selected" ';}; $output.='>'.__('Iraq', 'wpstorecart').'</option>
                    <option value="Ireland"'; if($prev_val=='Ireland'){$output.=' selected="selected" ';}; $output.='>'.__('Ireland', 'wpstorecart').'</option>
                    <option value="Israel"'; if($prev_val=='Israel'){$output.=' selected="selected" ';}; $output.='>'.__('Israel', 'wpstorecart').'</option>
                    <option value="Italy"'; if($prev_val=='Italy'){$output.=' selected="selected" ';}; $output.='>'.__('Italy', 'wpstorecart').'</option>
                    <option value="Jamaica"'; if($prev_val=='Jamaica'){$output.=' selected="selected" ';}; $output.='>'.__('Jamaica', 'wpstorecart').'</option>
                    <option value="Japan"'; if($prev_val=='Japan'){$output.=' selected="selected" ';}; $output.='>'.__('Japan', 'wpstorecart').'</option>
                    <option value="Jordan"'; if($prev_val=='Jordan'){$output.=' selected="selected" ';}; $output.='>'.__('Jordan', 'wpstorecart').'</option>
                    <option value="Kazakhstan"'; if($prev_val=='Kazakhstan'){$output.=' selected="selected" ';}; $output.='>'.__('Kazakhstan', 'wpstorecart').'</option>
                    <option value="Kenya"'; if($prev_val=='Kenya'){$output.=' selected="selected" ';}; $output.='>'.__('Kenya', 'wpstorecart').'</option>
                    <option value="Kiribati"'; if($prev_val=='Kiribati'){$output.=' selected="selected" ';}; $output.='>'.__('Kiribati', 'wpstorecart').'</option>
                    <option value="Korea, Democratic People\'s Republic of"'; if($prev_val=='Korea, Democratic People\'s Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Korea, Democratic People\'s Republic of', 'wpstorecart').'</option>
                    <option value="Korea, Republic of"'; if($prev_val=='Korea, Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Korea, Republic of', 'wpstorecart').'</option>
                    <option value="Kuwait"'; if($prev_val=='Kuwait'){$output.=' selected="selected" ';}; $output.='>'.__('Kuwait', 'wpstorecart').'</option>
                    <option value="Kyrgyzstan"'; if($prev_val=='Kyrgyzstan'){$output.=' selected="selected" ';}; $output.='>'.__('Kyrgyzstan', 'wpstorecart').'</option>
                    <option value="Lao People\'s Democratic Republic"'; if($prev_val=='Lao People\'s Democratic Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Lao People\'s Democratic Republic', 'wpstorecart').'</option>
                    <option value="Latvia"'; if($prev_val=='Latvia'){$output.=' selected="selected" ';}; $output.='>'.__('Latvia', 'wpstorecart').'</option>
                    <option value="Lebanon"'; if($prev_val=='Lebanon'){$output.=' selected="selected" ';}; $output.='>'.__('Lebanon', 'wpstorecart').'</option>
                    <option value="Lesotho"'; if($prev_val=='Lesotho'){$output.=' selected="selected" ';}; $output.='>'.__('Lesotho', 'wpstorecart').'</option>
                    <option value="Liberia"'; if($prev_val=='Liberia'){$output.=' selected="selected" ';}; $output.='>'.__('Liberia', 'wpstorecart').'</option>
                    <option value="Libyan Arab Jamahiriya"'; if($prev_val=='Libyan Arab Jamahiriya'){$output.=' selected="selected" ';}; $output.='>'.__('Libyan Arab Jamahiriya', 'wpstorecart').'</option>
                    <option value="Liechtenstein"'; if($prev_val=='Liechtenstein'){$output.=' selected="selected" ';}; $output.='>'.__('Liechtenstein', 'wpstorecart').'</option>
                    <option value="Lithuania"'; if($prev_val=='Lithuania'){$output.=' selected="selected" ';}; $output.='>'.__('Lithuania', 'wpstorecart').'</option>
                    <option value="Luxembourg"'; if($prev_val=='Luxembourg'){$output.=' selected="selected" ';}; $output.='>'.__('Luxembourg', 'wpstorecart').'</option>
                    <option value="Macao"'; if($prev_val=='Macao'){$output.=' selected="selected" ';}; $output.='>'.__('Macao', 'wpstorecart').'</option>
                    <option value="Macedonia, The Former Yugoslav Republic of"'; if($prev_val=='Macedonia, The Former Yugoslav Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Macedonia, The Former Yugoslav Republic of', 'wpstorecart').'</option>
                    <option value="Madagascar"'; if($prev_val=='Madagascar'){$output.=' selected="selected" ';}; $output.='>'.__('Madagascar', 'wpstorecart').'</option>
                    <option value="Malawi"'; if($prev_val=='Malawi'){$output.=' selected="selected" ';}; $output.='>'.__('Malawi', 'wpstorecart').'</option>
                    <option value="Malaysia"'; if($prev_val=='Malaysia'){$output.=' selected="selected" ';}; $output.='>'.__('Malaysia', 'wpstorecart').'</option>
                    <option value="Maldives"'; if($prev_val=='Maldives'){$output.=' selected="selected" ';}; $output.='>'.__('Maldives', 'wpstorecart').'</option>
                    <option value="Mali"'; if($prev_val=='Mali'){$output.=' selected="selected" ';}; $output.='>'.__('Mali', 'wpstorecart').'</option>
                    <option value="Malta"'; if($prev_val=='Malta'){$output.=' selected="selected" ';}; $output.='>'.__('Malta', 'wpstorecart').'</option>
                    <option value="Marshall Islands"'; if($prev_val=='Marshall Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Marshall Islands', 'wpstorecart').'</option>
                    <option value="Martinique"'; if($prev_val=='Martinique'){$output.=' selected="selected" ';}; $output.='>'.__('Martinique', 'wpstorecart').'</option>
                    <option value="Mauritania"'; if($prev_val=='Mauritania'){$output.=' selected="selected" ';}; $output.='>'.__('Mauritania', 'wpstorecart').'</option>
                    <option value="Mauritius"'; if($prev_val=='Mauritius'){$output.=' selected="selected" ';}; $output.='>'.__('Mauritius', 'wpstorecart').'</option>
                    <option value="Mayotte"'; if($prev_val=='Mayotte'){$output.=' selected="selected" ';}; $output.='>'.__('Mayotte', 'wpstorecart').'</option>
                    <option value="Mexico"'; if($prev_val=='Mexico'){$output.=' selected="selected" ';}; $output.='>'.__('Mexico', 'wpstorecart').'</option>
                    <option value="Micronesia, Federated States of"'; if($prev_val=='Micronesia, Federated States of'){$output.=' selected="selected" ';}; $output.='>'.__('Micronesia, Federated States of', 'wpstorecart').'</option>
                    <option value="Moldova, Republic of"'; if($prev_val=='Moldova, Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Moldova, Republic of', 'wpstorecart').'</option>
                    <option value="Monaco"'; if($prev_val=='Monaco'){$output.=' selected="selected" ';}; $output.='>'.__('Monaco', 'wpstorecart').'</option>
                    <option value="Mongolia"'; if($prev_val=='Mongolia'){$output.=' selected="selected" ';}; $output.='>'.__('Mongolia', 'wpstorecart').'</option>
                    <option value="Montserrat"'; if($prev_val=='Montserrat'){$output.=' selected="selected" ';}; $output.='>'.__('Montserrat', 'wpstorecart').'</option>
                    <option value="Morocco"'; if($prev_val=='Morocco'){$output.=' selected="selected" ';}; $output.='>'.__('Morocco', 'wpstorecart').'</option>
                    <option value="Mozambique"'; if($prev_val=='Mozambique'){$output.=' selected="selected" ';}; $output.='>'.__('Mozambique', 'wpstorecart').'</option>
                    <option value="Myanmar"'; if($prev_val=='Myanmar'){$output.=' selected="selected" ';}; $output.='>'.__('Myanmar', 'wpstorecart').'</option>
                    <option value="Namibia"'; if($prev_val=='Namibia'){$output.=' selected="selected" ';}; $output.='>'.__('Namibia', 'wpstorecart').'</option>
                    <option value="Nauru"'; if($prev_val=='Nauru'){$output.=' selected="selected" ';}; $output.='>'.__('Nauru', 'wpstorecart').'</option>
                    <option value="Nepal"'; if($prev_val=='Nepal'){$output.=' selected="selected" ';}; $output.='>'.__('Nepal', 'wpstorecart').'</option>
                    <option value="Netherlands"'; if($prev_val=='Netherlands'){$output.=' selected="selected" ';}; $output.='>'.__('Netherlands', 'wpstorecart').'</option>
                    <option value="Netherlands Antilles"'; if($prev_val=='Netherlands Antilles'){$output.=' selected="selected" ';}; $output.='>'.__('Netherlands Antilles', 'wpstorecart').'</option>
                    <option value="New Caledonia"'; if($prev_val=='New Caledonia'){$output.=' selected="selected" ';}; $output.='>'.__('New Caledonia', 'wpstorecart').'</option>
                    <option value="New Zealand"'; if($prev_val=='New Zealand'){$output.=' selected="selected" ';}; $output.='>'.__('New Zealand', 'wpstorecart').'</option>
                    <option value="Nicaragua"'; if($prev_val=='Nicaragua'){$output.=' selected="selected" ';}; $output.='>'.__('Nicaragua', 'wpstorecart').'</option>
                    <option value="Niger"'; if($prev_val=='Niger'){$output.=' selected="selected" ';}; $output.='>'.__('Niger', 'wpstorecart').'</option>
                    <option value="Nigeria"'; if($prev_val=='Nigeria'){$output.=' selected="selected" ';}; $output.='>'.__('Nigeria', 'wpstorecart').'</option>
                    <option value="Niue"'; if($prev_val=='Niue'){$output.=' selected="selected" ';}; $output.='>'.__('Niue', 'wpstorecart').'</option>
                    <option value="Norfolk Island"'; if($prev_val=='Norfolk Island'){$output.=' selected="selected" ';}; $output.='>'.__('Norfolk Island', 'wpstorecart').'</option>
                    <option value="Northern Mariana Islands"'; if($prev_val=='Northern Mariana Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Northern Mariana Islands', 'wpstorecart').'</option>
                    <option value="Norway"'; if($prev_val=='Norway'){$output.=' selected="selected" ';}; $output.='>'.__('Norway', 'wpstorecart').'</option>
                    <option value="Oman"'; if($prev_val=='Oman'){$output.=' selected="selected" ';}; $output.='>'.__('Oman', 'wpstorecart').'</option>
                    <option value="Pakistan"'; if($prev_val=='Pakistan'){$output.=' selected="selected" ';}; $output.='>'.__('Pakistan', 'wpstorecart').'</option>
                    <option value="Palau"'; if($prev_val=='Palau'){$output.=' selected="selected" ';}; $output.='>'.__('Palau', 'wpstorecart').'</option>
                    <option value="Palestinian Territory, Occupied"'; if($prev_val=='Palestinian Territory, Occupied'){$output.=' selected="selected" ';}; $output.='>'.__('Palestinian Territory, Occupied', 'wpstorecart').'</option>
                    <option value="Panama"'; if($prev_val=='Panama'){$output.=' selected="selected" ';}; $output.='>'.__('Panama', 'wpstorecart').'</option>
                    <option value="Papua New Guinea"'; if($prev_val=='Papua New Guinea'){$output.=' selected="selected" ';}; $output.='>'.__('Papua New Guinea', 'wpstorecart').'</option>
                    <option value="Paraguay"'; if($prev_val=='Paraguay'){$output.=' selected="selected" ';}; $output.='>'.__('Paraguay', 'wpstorecart').'</option>
                    <option value="Peru"'; if($prev_val=='Peru'){$output.=' selected="selected" ';}; $output.='>'.__('Peru', 'wpstorecart').'</option>
                    <option value="Philippines"'; if($prev_val=='Philippines'){$output.=' selected="selected" ';}; $output.='>'.__('Philippines', 'wpstorecart').'</option>
                    <option value="Pitcairn"'; if($prev_val=='Pitcairn'){$output.=' selected="selected" ';}; $output.='>'.__('Pitcairn', 'wpstorecart').'</option>
                    <option value="Poland"'; if($prev_val=='Poland'){$output.=' selected="selected" ';}; $output.='>'.__('Poland', 'wpstorecart').'</option>
                    <option value="Portugal"'; if($prev_val=='Portugal'){$output.=' selected="selected" ';}; $output.='>'.__('Portugal', 'wpstorecart').'</option>
                    <option value="Puerto Rico"'; if($prev_val=='Puerto Rico'){$output.=' selected="selected" ';}; $output.='>'.__('Puerto Rico', 'wpstorecart').'</option>
                    <option value="Qatar"'; if($prev_val=='Qatar'){$output.=' selected="selected" ';}; $output.='>'.__('Qatar', 'wpstorecart').'</option>
                    <option value="Reunion"'; if($prev_val=='Reunion'){$output.=' selected="selected" ';}; $output.='>'.__('Reunion', 'wpstorecart').'</option>
                    <option value="Romania"'; if($prev_val=='Romania'){$output.=' selected="selected" ';}; $output.='>'.__('Romania', 'wpstorecart').'</option>
                    <option value="Russian Federation"'; if($prev_val=='Russian Federation'){$output.=' selected="selected" ';}; $output.='>'.__('Russian Federation', 'wpstorecart').'</option>
                    <option value="Rwanda"'; if($prev_val=='Rwanda'){$output.=' selected="selected" ';}; $output.='>'.__('Rwanda', 'wpstorecart').'</option>
                    <option value="Saint Helena"'; if($prev_val=='Saint Helena'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Helena', 'wpstorecart').'</option>
                    <option value="Saint Kitts and Nevis"'; if($prev_val=='Saint Kitts and Nevis'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Kitts and Nevis', 'wpstorecart').'</option>
                    <option value="Saint Lucia"'; if($prev_val=='Saint Lucia'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Lucia', 'wpstorecart').'</option>
                    <option value="Saint Pierre and Miquelon"'; if($prev_val=='Saint Pierre and Miquelon'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Pierre and Miquelon', 'wpstorecart').'</option>
                    <option value="Saint Vincent and The Grenadines"'; if($prev_val=='Saint Vincent and The Grenadines'){$output.=' selected="selected" ';}; $output.='>'.__('Saint Vincent and The Grenadines', 'wpstorecart').'</option>
                    <option value="Samoa"'; if($prev_val=='Samoa'){$output.=' selected="selected" ';}; $output.='>'.__('Samoa', 'wpstorecart').'</option>
                    <option value="San Marino"'; if($prev_val=='San Marino'){$output.=' selected="selected" ';}; $output.='>'.__('San Marino', 'wpstorecart').'</option>
                    <option value="Sao Tome and Principe"'; if($prev_val=='Sao Tome and Principe'){$output.=' selected="selected" ';}; $output.='>'.__('Sao Tome and Principe', 'wpstorecart').'</option>
                    <option value="Saudi Arabia"'; if($prev_val=='Saudi Arabia'){$output.=' selected="selected" ';}; $output.='>'.__('Saudi Arabia', 'wpstorecart').'</option>
                    <option value="Senegal"'; if($prev_val=='Senegal'){$output.=' selected="selected" ';}; $output.='>'.__('Senegal', 'wpstorecart').'</option>
                    <option value="Serbia and Montenegro"'; if($prev_val=='Serbia and Montenegro'){$output.=' selected="selected" ';}; $output.='>'.__('Serbia and Montenegro', 'wpstorecart').'</option>
                    <option value="Seychelles"'; if($prev_val=='Seychelles'){$output.=' selected="selected" ';}; $output.='>'.__('Seychelles', 'wpstorecart').'</option>
                    <option value="Sierra Leone"'; if($prev_val=='Sierra Leone'){$output.=' selected="selected" ';}; $output.='>'.__('Sierra Leone', 'wpstorecart').'</option>
                    <option value="Singapore"'; if($prev_val=='Singapore'){$output.=' selected="selected" ';}; $output.='>'.__('Singapore', 'wpstorecart').'</option>
                    <option value="Slovakia"'; if($prev_val=='Slovakia'){$output.=' selected="selected" ';}; $output.='>'.__('Slovakia', 'wpstorecart').'</option>
                    <option value="Slovenia"'; if($prev_val=='Slovenia'){$output.=' selected="selected" ';}; $output.='>'.__('Slovenia', 'wpstorecart').'</option>
                    <option value="Solomon Islands"'; if($prev_val=='Solomon Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Solomon Islands', 'wpstorecart').'</option>
                    <option value="Somalia"'; if($prev_val=='Somalia'){$output.=' selected="selected" ';}; $output.='>'.__('Somalia', 'wpstorecart').'</option>
                    <option value="South Africa"'; if($prev_val=='South Africa'){$output.=' selected="selected" ';}; $output.='>'.__('South Africa', 'wpstorecart').'</option>
                    <option value="South Georgia and The South Sandwich Islands"'; if($prev_val=='South Georgia and The South Sandwich Islands'){$output.=' selected="selected" ';}; $output.='>'.__('South Georgia and The South Sandwich Islands', 'wpstorecart').'</option>
                    <option value="Spain"'; if($prev_val=='Spain'){$output.=' selected="selected" ';}; $output.='>'.__('Spain', 'wpstorecart').'</option>
                    <option value="Sri Lanka"'; if($prev_val=='Sri Lanka'){$output.=' selected="selected" ';}; $output.='>'.__('Sri Lanka', 'wpstorecart').'</option>
                    <option value="Sudan"'; if($prev_val=='Sudan'){$output.=' selected="selected" ';}; $output.='>'.__('Sudan', 'wpstorecart').'</option>
                    <option value="Suriname"'; if($prev_val=='Suriname'){$output.=' selected="selected" ';}; $output.='>'.__('Suriname', 'wpstorecart').'</option>
                    <option value="Svalbard and Jan Mayen"'; if($prev_val=='Svalbard and Jan Mayen'){$output.=' selected="selected" ';}; $output.='>'.__('Svalbard and Jan Mayen', 'wpstorecart').'</option>
                    <option value="Swaziland"'; if($prev_val=='Swaziland'){$output.=' selected="selected" ';}; $output.='>'.__('Swaziland', 'wpstorecart').'</option>
                    <option value="Sweden"'; if($prev_val=='Sweden'){$output.=' selected="selected" ';}; $output.='>'.__('Sweden', 'wpstorecart').'</option>
                    <option value="Switzerland"'; if($prev_val=='Switzerland'){$output.=' selected="selected" ';}; $output.='>'.__('Switzerland', 'wpstorecart').'</option>
                    <option value="Syrian Arab Republic"'; if($prev_val=='Syrian Arab Republic'){$output.=' selected="selected" ';}; $output.='>'.__('Syrian Arab Republic', 'wpstorecart').'</option>
                    <option value="Taiwan, Province of China"'; if($prev_val=='Taiwan, Province of China'){$output.=' selected="selected" ';}; $output.='>'.__('Taiwan, Province of China', 'wpstorecart').'</option>
                    <option value="Tajikistan"'; if($prev_val=='Tajikistan'){$output.=' selected="selected" ';}; $output.='>'.__('Tajikistan', 'wpstorecart').'</option>
                    <option value="Tanzania, United Republic of"'; if($prev_val=='Tanzania, United Republic of'){$output.=' selected="selected" ';}; $output.='>'.__('Tanzania, United Republic of', 'wpstorecart').'</option>
                    <option value="Thailand"'; if($prev_val=='Thailand'){$output.=' selected="selected" ';}; $output.='>'.__('Thailand', 'wpstorecart').'</option>
                    <option value="Timor-leste"'; if($prev_val=='Timor-leste'){$output.=' selected="selected" ';}; $output.='>'.__('Timor-leste', 'wpstorecart').'</option>
                    <option value="Togo"'; if($prev_val=='Togo'){$output.=' selected="selected" ';}; $output.='>'.__('Togo', 'wpstorecart').'</option>
                    <option value="Tokelau"'; if($prev_val=='Tokelau'){$output.=' selected="selected" ';}; $output.='>'.__('Tokelau', 'wpstorecart').'</option>
                    <option value="Tonga"'; if($prev_val=='Tonga'){$output.=' selected="selected" ';}; $output.='>'.__('Tonga', 'wpstorecart').'</option>
                    <option value="Trinidad and Tobago"'; if($prev_val=='Trinidad and Tobago'){$output.=' selected="selected" ';}; $output.='>'.__('Trinidad and Tobago', 'wpstorecart').'</option>
                    <option value="Tunisia"'; if($prev_val=='Tunisia'){$output.=' selected="selected" ';}; $output.='>'.__('Tunisia', 'wpstorecart').'</option>
                    <option value="Turkey"'; if($prev_val=='Turkey'){$output.=' selected="selected" ';}; $output.='>'.__('Turkey', 'wpstorecart').'</option>
                    <option value="Turkmenistan"'; if($prev_val=='Turkmenistan'){$output.=' selected="selected" ';}; $output.='>'.__('Turkmenistan', 'wpstorecart').'</option>
                    <option value="Turks and Caicos Islands"'; if($prev_val=='Turks and Caicos Islands'){$output.=' selected="selected" ';}; $output.='>'.__('Turks and Caicos Islands', 'wpstorecart').'</option>
                    <option value="Tuvalu"'; if($prev_val=='Tuvalu'){$output.=' selected="selected" ';}; $output.='>'.__('Tuvalu', 'wpstorecart').'</option>
                    <option value="Uganda"'; if($prev_val=='Uganda'){$output.=' selected="selected" ';}; $output.='>'.__('Uganda', 'wpstorecart').'</option>
                    <option value="Ukraine"'; if($prev_val=='Ukraine'){$output.=' selected="selected" ';}; $output.='>'.__('Ukraine', 'wpstorecart').'</option>
                    <option value="United Arab Emirates"'; if($prev_val=='United Arab Emirates'){$output.=' selected="selected" ';}; $output.='>'.__('United Arab Emirates', 'wpstorecart').'</option>
                    <option value="United States Minor Outlying Islands"'; if($prev_val=='United States Minor Outlying Islands'){$output.=' selected="selected" ';}; $output.='>'.__('United States Minor Outlying Islands', 'wpstorecart').'</option>
                    <option value="Uruguay"'; if($prev_val=='Uruguay'){$output.=' selected="selected" ';}; $output.='>'.__('Uruguay', 'wpstorecart').'</option>
                    <option value="Uzbekistan"'; if($prev_val=='Uzbekistan'){$output.=' selected="selected" ';}; $output.='>'.__('Uzbekistan', 'wpstorecart').'</option>
                    <option value="Vanuatu"'; if($prev_val=='Vanuatu'){$output.=' selected="selected" ';}; $output.='>'.__('Vanuatu', 'wpstorecart').'</option>
                    <option value="Venezuela"'; if($prev_val=='Venezuela'){$output.=' selected="selected" ';}; $output.='>'.__('Venezuela', 'wpstorecart').'</option>
                    <option value="Viet Nam"'; if($prev_val=='Viet Nam'){$output.=' selected="selected" ';}; $output.='>'.__('Viet Nam', 'wpstorecart').'</option>
                    <option value="Virgin Islands, British"'; if($prev_val=='Virgin Islands, British'){$output.=' selected="selected" ';}; $output.='>'.__('Virgin Islands, British', 'wpstorecart').'</option>
                    <option value="Virgin Islands, U.S."'; if($prev_val=='Virgin Islands, U.S.'){$output.=' selected="selected" ';}; $output.='>'.__('Virgin Islands, U.S.', 'wpstorecart').'</option>
                    <option value="Wallis and Futuna"'; if($prev_val=='Wallis and Futuna'){$output.=' selected="selected" ';}; $output.='>'.__('Wallis and Futuna', 'wpstorecart').'</option>
                    <option value="Western Sahara"'; if($prev_val=='Western Sahara'){$output.=' selected="selected" ';}; $output.='>'.__('Western Sahara', 'wpstorecart').'</option>
                    <option value="Yemen"'; if($prev_val=='Yemen'){$output.=' selected="selected" ';}; $output.='>'.__('Yemen', 'wpstorecart').'</option>
                    <option value="Zambia"'; if($prev_val=='Zambia'){$output.=' selected="selected" ';}; $output.='>'.__('Zambia', 'wpstorecart').'</option>
                    <option value="Zimbabwe"'; if($prev_val=='Zimbabwe'){$output.=' selected="selected" ';}; $output.='>'.__('Zimbabwe', 'wpstorecart').'</option>
                    </select></td></tr>';
                }
                if($specific_items[2]=='email') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="'.wpscSlug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.wpscSlug($specific_items[0])].'" name="'.wpscSlug($specific_items[0]).'" /></td></tr>';
                }
                if($specific_items[2]=='separator') {
                    $output .= $specific_items[0] .'<tr><td></td><td></td></tr>';
                }
                if($specific_items[2]=='header') {
                    $output .= '<tr><td><h2>'.$specific_items[0] .'</h2></td><td></td></tr>';
                }
                if($specific_items[2]=='text') {
                    $output .= '<tr><td>'.$specific_items[0] .'</td><td></td></tr>';
                }



        }

        return $output;


    }
}


if(!function_exists('wpscVerifyNeedToShowRequiredCustomRegistrationFields')) {
    /**
     *
     * @param type $user_id
     * @return boolean If this returns true, then we need to show custom registration fields
     */
    function wpscVerifyNeedToShowRequiredCustomRegistrationFields($user_id) {
        $fields = wpscGrabCustomRegistrationFields();
        
        if(@!isset($_SESSION)) {
            @session_start();
        }        
        
        $needtoshow = false; // We do not show custom registration fields if this remains false
        foreach ($fields as $field) {
            $specific_items = explode("||", $field['value']);
            if($specific_items[1]=='required' && trim($specific_items[0])!=NULL && $specific_items[2]!='header' && $specific_items[2]!='separator' && $specific_items[2]!='text' ){
                if($user_id > 0 ) {
                    $value = get_user_meta( $user_id, wpscSlug($specific_items[0]), true );
                    if(@trim($value)!='') {
                        $_SESSION['wpsc_'.wpscSlug($specific_items[0])] = $value;
                    }    
                    
                    // city shipping
                    if($specific_items[2]=='shippingcity') {
                        $value = get_user_meta( $user_id, 'wpsc_shipping_city', true );
                        if(@trim($value)=='') {
                            $_SESSION['wpsc_shipping_city'] = $value;
                        }                          
                    }                    
                    
                    // Firstname shipping
                    if($specific_items[2]=='firstname') {
                        $value = get_user_meta( $user_id, 'wpsc_shipping_firstname', true );
                        if(@trim($value)=='') {
                            $_SESSION['wpsc_shipping_firstname'] = $value;
                        }                          
                    }

                    // lastname shipping
                    if($specific_items[2]=='lastname') {
                        $value = get_user_meta( $user_id, 'wpsc_shipping_lastname', true );
                        if(@trim($value)=='') {
                            $_SESSION['wpsc_shipping_lastname'] = $value;
                        }                          
                    }                

                    // address shipping
                    if($specific_items[2]=='shippingaddress') {
                        $value = get_user_meta( $user_id, 'wpsc_shipping_address', true );
                        if(@trim($value)=='') {
                            $_SESSION['wpsc_shipping_address'] = $value;
                        }                          
                    } 

                    // zipcode shipping
                    if($specific_items[2]=='zipcode') {
                        $value = get_user_meta( $user_id, 'wpsc_shipping_zipcode', true );
                        if(@trim($value)=='') {
                            $_SESSION['wpsc_shipping_zipcode'] = $value;
                        }                         
                    }    

                    // state shipping
                    if($specific_items[2]=='taxstates') {
                        $value = get_user_meta( $user_id, 'wpsc_taxstates', true );
                        if(@trim($value)=='') {
                            $_SESSION['wpsc_taxstates'] = $value;
                        }                         
                    }     

                    // country shipping
                    if($specific_items[2]=='taxcountries') {
                        $value = get_user_meta( $user_id, 'wpsc_taxcountries', true );
                        if(@trim($value)=='') {
                            $_SESSION['wpsc_taxcountries'] = $value;
                        }                         
                    }                    
                }
                if($user_id == 0) {
                    $value = $_SESSION['wpsc_'.wpscSlug($specific_items[0])];
                    
                    // city shipping
                    if($specific_items[2]=='shippingcity') {
                        $value = $_SESSION['wpsc_shipping_city'];
                    }                      
                    
                    // Firstname shipping
                    if($specific_items[2]=='firstname') {
                        $value = $_SESSION['wpsc_shipping_firstname'];
                    }

                    // lastname shipping
                    if($specific_items[2]=='lastname') {
                        $value = $_SESSION['wpsc_shipping_lastname'];
                    }                

                    // address shipping
                    if($specific_items[2]=='shippingaddress') {
                        $value = $_SESSION['wpsc_shipping_address'];
                    } 

                    // zipcode shipping
                    if($specific_items[2]=='zipcode') {
                        $value = $_SESSION['wpsc_shipping_zipcode'];
                    }    

                    // state shipping
                    if($specific_items[2]=='taxstates') {
                        $value = $_SESSION['wpsc_taxstates'];
                    }     

                    // country shipping
                    if($specific_items[2]=='taxcountries') {
                        $value = $_SESSION['wpsc_taxcountries'];
                    }                    
                }
                
                   
                
                  
                if(@trim($value)=='') {
                    //echo $specific_items[0];
                    $needtoshow = true;
                }
                //echo 'VERIFY: wpsc_'.wpscSlug($specific_items[0]).' : '.$value .'<br />';
            }
        }
        return $needtoshow;
    }
}


if(!function_exists('wpscRegisterExtraFields')) {
    /**
    * 
    * Creates additional profile fields for the user
    *
    * @global object $wpdb
    * @param integer $user_id
    * @param string $password
    * @param array $meta
    * @return NULL
    */
    function wpscRegisterExtraFields($user_id, $password="", $meta=array()) {
        global $wpdb;
        if ( !current_user_can( 'edit_user', $user_id ) ) { 
            return false;
        } else {
            $userdata = array();
            $userdata['ID'] = $user_id;
            wp_update_user($userdata);

            $fields = wpscGrabCustomRegistrationFields();
            foreach ($fields as $field) {
                $specific_items = explode("||", $field['value']);
                foreach ($specific_items as $specific_item) {
                    if($specific_item[2]!='separator' && $specific_item[2]!='header' && $specific_item[2]!='text') {
                        update_usermeta( $user_id, wpscSlug($specific_item[0]), esc_sql($_POST[wpscSlug($specific_item[0])]) );
                    }
                }
            }
        }
    }
}

if(!function_exists('wpscSaveFields')) {
    /**
     *
     * Saves custom regisration fields from guests and logged in users.  Guest store data in sessions, while logged in user use the wordpress usermeta table
     * 
     * @global object $wpdb
     * @param type $user_id 
     */
    function wpscSaveFields($user_id) {
        global $wpdb;
        $fields = wpscGrabCustomRegistrationFields();

        if(@!isset($_SESSION)) {
            @session_start();
        }
        
        // Firstname shipping
        if(@isset($_POST['wpsc_shipping_firstname'])) {
            if($user_id > 0) { // If we're dealing with a logged in user:
                update_usermeta( $user_id, 'wpsc_shipping_firstname', esc_sql($_POST['wpsc_shipping_firstname']) );
            }   
            $_SESSION['wpsc_shipping_firstname'] = esc_sql($_POST['wpsc_shipping_firstname']);
        }

        // lastname shipping
        if(@isset($_POST['wpsc_shipping_lastname'])) {
            if($user_id > 0) { // If we're dealing with a logged in user:
                update_usermeta( $user_id, 'wpsc_shipping_lastname', esc_sql($_POST['wpsc_shipping_lastname']) );
            }   
            $_SESSION['wpsc_shipping_lastname'] = esc_sql($_POST['wpsc_shipping_lastname']);
        }    

        // address shipping
        if(@isset($_POST['wpsc_shipping_address'])) {
            if($user_id > 0) { // If we're dealing with a logged in user:
                update_usermeta( $user_id, 'wpsc_shipping_address', esc_sql($_POST['wpsc_shipping_address']) );
            }   
            $_SESSION['wpsc_shipping_address'] = esc_sql($_POST['wpsc_shipping_address']);
        }            

        // zipcode shipping
        if(@isset($_POST['wpsc_shipping_zipcode'])) {
            if($user_id > 0) { // If we're dealing with a logged in user:
                update_usermeta( $user_id, 'wpsc_shipping_zipcode', esc_sql($_POST['wpsc_shipping_zipcode']) );
            }   
            $_SESSION['wpsc_shipping_zipcode'] = esc_sql($_POST['wpsc_shipping_zipcode']);
        }  


        // states shipping
        if(@isset($_POST['taxstate'])) {
            if($user_id > 0) { // If we're dealing with a logged in user:
                update_usermeta( $user_id, 'wpsc_taxstates', esc_sql($_POST['taxstate']) );
            }   
            $_SESSION['wpsc_taxstates'] = esc_sql($_POST['taxstate']);
        } 

        // city shipping
        if(@isset($_POST['wpsc_shipping_city'])) {
            if($user_id > 0) { // If we're dealing with a logged in user:
                update_usermeta( $user_id, 'wpsc_shipping_city', esc_sql($_POST['wpsc_shipping_city']) );
            }   
            $_SESSION['wpsc_shipping_city'] = esc_sql($_POST['wpsc_shipping_city']);
        }         

        // countries shipping
        if(@isset($_POST['taxcountries'])) {
            if($user_id > 0) { // If we're dealing with a logged in user:
                update_usermeta( $user_id, 'wpsc_taxcountries', esc_sql($_POST['taxcountries']) );
            }   
            $_SESSION['wpsc_taxcountries'] = esc_sql($_POST['taxcountries']);
        }          
      
        foreach ($fields as $field) {
            $specific_items = explode("||", $field['value']);
            if(@isset($_POST[wpscSlug($specific_items[0])])) {
                if($user_id > 0) { // If we're dealing with a logged in user:
                    update_usermeta( $user_id, wpscSlug($specific_items[0]), esc_sql($_POST[wpscSlug($specific_items[0])]) );
                }
                //  Session data should be saved regardless of if logged in
                $_SESSION['wpsc_'.wpscSlug($specific_items[0])] = esc_sql($_POST[wpscSlug($specific_items[0])]);
                //echo 'SAVE: wpsc_'.wpscSlug($specific_items[0]) .' : '.esc_sql($_POST[wpscSlug($specific_items[0])]) .'<br />';
            } 
            
        }
    }
}


if(!function_exists('wpscCheckFields')) {
    /**
    * Server side validation for customer custom registration fields
    * 
    * @global object $wpdb
    * @param string $login
    * @param string $email
    * @param object $errors 
    */
    function wpscCheckFields($login, $email, $errors) {

            // Make sure errors are displayed for empty fields which are marked as required
            $fields = wpscGrabCustomRegistrationFields();
            foreach ($fields as $field) {
                $specific_items = explode("||", $field['value']);

                if($specific_items[2]!='separator' && $specific_items[2]!='header' && $specific_items[2]!='text') {
                    $current_field = trim($_POST[wpscSlug($specific_items[0])]);
                    if ($specific_items[1]=='required' && $current_field=='') {
                        $_SESSION['wpsc_'.wpscSlug($specific_items[0])]=$_POST[wpscSlug($specific_items[0])]; // This allows us to save data in case the form needs to be refilled out due to it being incomplete
                        $errors->add('empty_'.wpscSlug($specific_items[0]), __("ERROR: Please Enter in", 'wpstorecart')." {$specific_items[0]}");
                    }
                }

            }

    }
}

/**
    * Adds the custom registration fields
    */
add_filter('user_contactmethods', 'wpscProfileCustomContactMethod',10,1);
add_action('register_form', 'wpscShowCustomRegistrationFields');
add_action('user_register',  'wpscRegisterExtraFields');
add_action('register_post', 'wpscCheckFields',10,3);

?>