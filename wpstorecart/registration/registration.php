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
    function wpscAddCustomContactMethod( $contactmethods ) {
        global $wpdb;

        $fields = wpscGrabCustomRegistrationFields();
        foreach ($fields as $field) {
            $specific_items = explode("||", $field['value']);
                if($specific_items[2]!='separator' && $specific_items[2]!='header' && $specific_items[2]!='text') {
                    $slug = wpscSlug($specific_items[0]);
                    $contactmethods[$slug] = $wpdb->escape($specific_items[0]); // This makes something like this: $contactmethods['address'] = 'Address';
                }

        }

        return $contactmethods;
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
                if($specific_items[2]=='input (numeric)') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="'.wpscSlug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.wpscSlug($specific_items[0])].'" name="'.wpscSlug($specific_items[0]).'" /></td></tr>';
                }
                if($specific_items[2]=='textarea') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><textarea class="input" id="'.wpscSlug($specific_items[0]).'" name="'.wpscSlug($specific_items[0]).'">'.$_SESSION['wpsc_'.wpscSlug($specific_items[0])].'</textarea></td></tr>';
                }
                if($specific_items[2]=='states' || $specific_items[2]=='taxstates') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><select class="input" name="'; if ($specific_items[2]=='states'){$output.= wpscSlug($specific_items[0]);} else {$output.='taxstate';} $output.='" class="wpsc-states">
                    <option value="not applicable">'.__('Other (Non-US)', 'wpstorecart').'</option>
                    <option value="AL">'.__('Alabama', 'wpstorecart').'</option>
                    <option value="AK">'.__('Alaska', 'wpstorecart').'</option>
                    <option value="AZ">'.__('Arizona', 'wpstorecart').'</option>
                    <option value="AR">'.__('Arkansas', 'wpstorecart').'</option>
                    <option value="CA">'.__('California', 'wpstorecart').'</option>
                    <option value="CO">'.__('Colorado', 'wpstorecart').'</option>
                    <option value="CT">'.__('Connecticut', 'wpstorecart').'</option>
                    <option value="DE">'.__('Delaware', 'wpstorecart').'</option>
                    <option value="DC">'.__('District Of Columbia', 'wpstorecart').'</option>
                    <option value="FL">'.__('Florida', 'wpstorecart').'</option>
                    <option value="GA">'.__('Georgia', 'wpstorecart').'</option>
                    <option value="HI">'.__('Hawaii', 'wpstorecart').'</option>
                    <option value="ID">'.__('Idaho', 'wpstorecart').'</option>
                    <option value="IL">'.__('Illinois', 'wpstorecart').'</option>
                    <option value="IN">'.__('Indiana', 'wpstorecart').'</option>
                    <option value="IA">'.__('Iowa', 'wpstorecart').'</option>
                    <option value="KS">'.__('Kansas', 'wpstorecart').'</option>
                    <option value="KY">'.__('Kentucky', 'wpstorecart').'</option>
                    <option value="LA">'.__('Louisiana', 'wpstorecart').'</option>
                    <option value="ME">'.__('Maine', 'wpstorecart').'</option>
                    <option value="MD">'.__('Maryland', 'wpstorecart').'</option>
                    <option value="MA">'.__('Massachusetts', 'wpstorecart').'</option>
                    <option value="MI">'.__('Michigan', 'wpstorecart').'</option>
                    <option value="MN">'.__('Minnesota', 'wpstorecart').'</option>
                    <option value="MS">'.__('Mississippi', 'wpstorecart').'</option>
                    <option value="MO">'.__('Missouri', 'wpstorecart').'</option>
                    <option value="MT">'.__('Montana', 'wpstorecart').'</option>
                    <option value="NE">'.__('Nebraska', 'wpstorecart').'</option>
                    <option value="NV">'.__('Nevada', 'wpstorecart').'</option>
                    <option value="NH">'.__('New Hampshire', 'wpstorecart').'</option>
                    <option value="NJ">'.__('New Jersey', 'wpstorecart').'</option>
                    <option value="NM">'.__('New Mexico', 'wpstorecart').'</option>
                    <option value="NY">'.__('New York', 'wpstorecart').'</option>
                    <option value="NC">'.__('North Carolina', 'wpstorecart').'</option>
                    <option value="ND">'.__('North Dakota', 'wpstorecart').'</option>
                    <option value="OH">'.__('Ohio', 'wpstorecart').'</option>
                    <option value="OK">'.__('Oklahoma', 'wpstorecart').'</option>
                    <option value="OR">'.__('Oregon', 'wpstorecart').'</option>
                    <option value="PA">'.__('Pennsylvania', 'wpstorecart').'</option>
                    <option value="RI">'.__('Rhode Island', 'wpstorecart').'</option>
                    <option value="SC">'.__('South Carolina', 'wpstorecart').'</option>
                    <option value="SD">'.__('South Dakota', 'wpstorecart').'</option>
                    <option value="TN">'.__('Tennessee', 'wpstorecart').'</option>
                    <option value="TX">'.__('Texas', 'wpstorecart').'</option>
                    <option value="UT">'.__('Utah', 'wpstorecart').'</option>
                    <option value="VT">'.__('Vermont', 'wpstorecart').'</option>
                    <option value="VA">'.__('Virginia', 'wpstorecart').'</option>
                    <option value="WA">'.__('Washington', 'wpstorecart').'</option>
                    <option value="WV">'.__('West Virginia', 'wpstorecart').'</option>
                    <option value="WI">'.__('Wisconsin', 'wpstorecart').'</option>
                    <option value="WY">'.__('Wyoming', 'wpstorecart').'</option>
                    </select></td></tr>';
                }
                if($specific_items[2]=='countries' || $specific_items[2]=='taxcountries') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><select class="input" name="'; if($specific_items[2]=='countries') {$output.=wpscSlug($specific_items[0]);} else {$output.='taxcountries';} $output.='" class="wpsc-countries">
                    <option value="United States" selected="selected">'.__('United States', 'wpstorecart').'</option>
                    <option value="Canada">'.__('Canada', 'wpstorecart').'</option>
                    <option value="United Kingdom" >'.__('United Kingdom', 'wpstorecart').'</option>
                    <option value="Ireland" >'.__('Ireland', 'wpstorecart').'</option>
                    <option value="Australia" >'.__('Australia', 'wpstorecart').'</option>
                    <option value="New Zealand" >'.__('New Zealand', 'wpstorecart').'</option>
                    <option value="Afghanistan">'.__('Afghanistan', 'wpstorecart').'</option>
                    <option value="Albania">'.__('Albania', 'wpstorecart').'</option>
                    <option value="Algeria">'.__('Algeria', 'wpstorecart').'</option>
                    <option value="American Samoa">'.__('American Samoa', 'wpstorecart').'</option>
                    <option value="Andorra">'.__('Andorra', 'wpstorecart').'</option>
                    <option value="Angola">'.__('Angola', 'wpstorecart').'</option>
                    <option value="Anguilla">'.__('Anguilla', 'wpstorecart').'</option>
                    <option value="Antarctica">'.__('Antarctica', 'wpstorecart').'</option>
                    <option value="Antigua and Barbuda">'.__('Antigua and Barbuda', 'wpstorecart').'</option>
                    <option value="Argentina">'.__('Argentina', 'wpstorecart').'</option>
                    <option value="Armenia">'.__('Armenia', 'wpstorecart').'</option>
                    <option value="Aruba">'.__('Aruba', 'wpstorecart').'</option>
                    <option value="Australia">'.__('Australia', 'wpstorecart').'</option>
                    <option value="Austria">'.__('Austria', 'wpstorecart').'</option>
                    <option value="Azerbaijan">'.__('Azerbaijan', 'wpstorecart').'</option>
                    <option value="Bahamas">'.__('Bahamas', 'wpstorecart').'</option>
                    <option value="Bahrain">'.__('Bahrain', 'wpstorecart').'</option>
                    <option value="Bangladesh">'.__('Bangladesh', 'wpstorecart').'</option>
                    <option value="Barbados">'.__('Barbados', 'wpstorecart').'</option>
                    <option value="Belarus">'.__('Belarus', 'wpstorecart').'</option>
                    <option value="Belgium">'.__('Belgium', 'wpstorecart').'</option>
                    <option value="Belize">'.__('Belize', 'wpstorecart').'</option>
                    <option value="Benin">'.__('Benin', 'wpstorecart').'</option>
                    <option value="Bermuda">'.__('Bermuda', 'wpstorecart').'</option>
                    <option value="Bhutan">'.__('Bhutan', 'wpstorecart').'</option>
                    <option value="Bolivia">'.__('Bolivia', 'wpstorecart').'</option>
                    <option value="Bosnia and Herzegovina">'.__('Bosnia and Herzegovina', 'wpstorecart').'</option>
                    <option value="Botswana">'.__('Botswana', 'wpstorecart').'</option>
                    <option value="Bouvet Island">'.__('Bouvet Island', 'wpstorecart').'</option>
                    <option value="Brazil">'.__('Brazil', 'wpstorecart').'</option>
                    <option value="British Indian Ocean Territory">'.__('British Indian Ocean Territory', 'wpstorecart').'</option>
                    <option value="Brunei Darussalam">'.__('Brunei Darussalam', 'wpstorecart').'</option>
                    <option value="Bulgaria">'.__('Bulgaria', 'wpstorecart').'</option>
                    <option value="Burkina Faso">'.__('Burkina Faso', 'wpstorecart').'</option>
                    <option value="Burundi">'.__('Burundi', 'wpstorecart').'</option>
                    <option value="Cambodia">'.__('Cambodia', 'wpstorecart').'</option>
                    <option value="Cameroon">'.__('Cameroon', 'wpstorecart').'</option>
                    <option value="Canada">'.__('Canada', 'wpstorecart').'</option>
                    <option value="Cape Verde">'.__('Cape Verde', 'wpstorecart').'</option>
                    <option value="Cayman Islands">'.__('Cayman Islands', 'wpstorecart').'</option>
                    <option value="Central African Republic">'.__('Central African Republic', 'wpstorecart').'</option>
                    <option value="Chad">'.__('Chad', 'wpstorecart').'</option>
                    <option value="Chile">'.__('Chile', 'wpstorecart').'</option>
                    <option value="China">'.__('China', 'wpstorecart').'</option>
                    <option value="Christmas Island">'.__('Christmas Island', 'wpstorecart').'</option>
                    <option value="Cocos (Keeling) Islands">'.__('Cocos (Keeling) Islands', 'wpstorecart').'</option>
                    <option value="Colombia">'.__('Colombia', 'wpstorecart').'</option>
                    <option value="Comoros">'.__('Comoros', 'wpstorecart').'</option>
                    <option value="Congo">'.__('Congo', 'wpstorecart').'</option>
                    <option value="Congo, The Democratic Republic of The">'.__('Congo, The Democratic Republic of The', 'wpstorecart').'</option>
                    <option value="Cook Islands">'.__('Cook Islands', 'wpstorecart').'</option>
                    <option value="Costa Rica">'.__('Costa Rica', 'wpstorecart').'</option>
                    <option value="Cote D\'ivoire">'.__('Cote D\'ivoire', 'wpstorecart').'</option>
                    <option value="Croatia">'.__('Croatia', 'wpstorecart').'</option>
                    <option value="Cuba">'.__('Cuba', 'wpstorecart').'</option>
                    <option value="Cyprus">'.__('Cyprus', 'wpstorecart').'</option>
                    <option value="Czech Republic">'.__('Czech Republic', 'wpstorecart').'</option>
                    <option value="Denmark">'.__('Denmark', 'wpstorecart').'</option>
                    <option value="Djibouti">'.__('Djibouti', 'wpstorecart').'</option>
                    <option value="Dominica">'.__('Dominica', 'wpstorecart').'</option>
                    <option value="Dominican Republic">'.__('Dominican Republic', 'wpstorecart').'</option>
                    <option value="Ecuador">'.__('Ecuador', 'wpstorecart').'</option>
                    <option value="Egypt">'.__('Egypt', 'wpstorecart').'</option>
                    <option value="El Salvador">'.__('El Salvador', 'wpstorecart').'</option>
                    <option value="Equatorial Guinea">'.__('Equatorial Guinea', 'wpstorecart').'</option>
                    <option value="Eritrea">'.__('Eritrea', 'wpstorecart').'</option>
                    <option value="Estonia">'.__('Estonia', 'wpstorecart').'</option>
                    <option value="Ethiopia">'.__('Ethiopia', 'wpstorecart').'</option>
                    <option value="Falkland Islands (Malvinas)">'.__('Falkland Islands (Malvinas)', 'wpstorecart').'</option>
                    <option value="Faroe Islands">'.__('Faroe Islands', 'wpstorecart').'</option>
                    <option value="Fiji">'.__('Fiji', 'wpstorecart').'</option>
                    <option value="Finland">'.__('Finland', 'wpstorecart').'</option>
                    <option value="France">'.__('France', 'wpstorecart').'</option>
                    <option value="French Guiana">'.__('French Guiana', 'wpstorecart').'</option>
                    <option value="French Polynesia">'.__('French Polynesia', 'wpstorecart').'</option>
                    <option value="French Southern Territories">'.__('French Southern Territories', 'wpstorecart').'</option>
                    <option value="Gabon">'.__('Gabon', 'wpstorecart').'</option>
                    <option value="Gambia">'.__('Gambia', 'wpstorecart').'</option>
                    <option value="Georgia">'.__('Georgia', 'wpstorecart').'</option>
                    <option value="Germany">'.__('Germany', 'wpstorecart').'</option>
                    <option value="Ghana">'.__('Ghana', 'wpstorecart').'</option>
                    <option value="Gibraltar">'.__('Gibraltar', 'wpstorecart').'</option>
                    <option value="Greece">'.__('Greece', 'wpstorecart').'</option>
                    <option value="Greenland">'.__('Greenland', 'wpstorecart').'</option>
                    <option value="Grenada">'.__('Grenada', 'wpstorecart').'</option>
                    <option value="Guadeloupe">'.__('Guadeloupe', 'wpstorecart').'</option>
                    <option value="Guam">'.__('Guam', 'wpstorecart').'</option>
                    <option value="Guatemala">'.__('Guatemala', 'wpstorecart').'</option>
                    <option value="Guinea">'.__('Guinea', 'wpstorecart').'</option>
                    <option value="Guinea-bissau">'.__('Guinea-bissau', 'wpstorecart').'</option>
                    <option value="Guyana">'.__('Guyana', 'wpstorecart').'</option>
                    <option value="Haiti">'.__('Haiti', 'wpstorecart').'</option>
                    <option value="Heard Island and Mcdonald Islands">'.__('Heard Island and Mcdonald Islands', 'wpstorecart').'</option>
                    <option value="Holy See (Vatican City State)">'.__('Holy See (Vatican City State)', 'wpstorecart').'</option>
                    <option value="Honduras">'.__('Honduras', 'wpstorecart').'</option>
                    <option value="Hong Kong">'.__('Hong Kong', 'wpstorecart').'</option>
                    <option value="Hungary">'.__('Hungary', 'wpstorecart').'</option>
                    <option value="Iceland">'.__('Iceland', 'wpstorecart').'</option>
                    <option value="India">'.__('India', 'wpstorecart').'</option>
                    <option value="Indonesia">'.__('Indonesia', 'wpstorecart').'</option>
                    <option value="Iran, Islamic Republic of">'.__('Iran, Islamic Republic of', 'wpstorecart').'</option>
                    <option value="Iraq">'.__('Iraq', 'wpstorecart').'</option>
                    <option value="Ireland">'.__('Ireland', 'wpstorecart').'</option>
                    <option value="Israel">'.__('Israel', 'wpstorecart').'</option>
                    <option value="Italy">'.__('Italy', 'wpstorecart').'</option>
                    <option value="Jamaica">'.__('Jamaica', 'wpstorecart').'</option>
                    <option value="Japan">'.__('Japan', 'wpstorecart').'</option>
                    <option value="Jordan">'.__('Jordan', 'wpstorecart').'</option>
                    <option value="Kazakhstan">'.__('Kazakhstan', 'wpstorecart').'</option>
                    <option value="Kenya">'.__('Kenya', 'wpstorecart').'</option>
                    <option value="Kiribati">'.__('Kiribati', 'wpstorecart').'</option>
                    <option value="Korea, Democratic People\'s Republic of">'.__('Korea, Democratic People\'s Republic of', 'wpstorecart').'</option>
                    <option value="Korea, Republic of">'.__('Korea, Republic of', 'wpstorecart').'</option>
                    <option value="Kuwait">'.__('Kuwait', 'wpstorecart').'</option>
                    <option value="Kyrgyzstan">'.__('Kyrgyzstan', 'wpstorecart').'</option>
                    <option value="Lao People\'s Democratic Republic">'.__('Lao People\'s Democratic Republic', 'wpstorecart').'</option>
                    <option value="Latvia">'.__('Latvia', 'wpstorecart').'</option>
                    <option value="Lebanon">'.__('Lebanon', 'wpstorecart').'</option>
                    <option value="Lesotho">'.__('Lesotho', 'wpstorecart').'</option>
                    <option value="Liberia">'.__('Liberia', 'wpstorecart').'</option>
                    <option value="Libyan Arab Jamahiriya">'.__('Libyan Arab Jamahiriya', 'wpstorecart').'</option>
                    <option value="Liechtenstein">'.__('Liechtenstein', 'wpstorecart').'</option>
                    <option value="Lithuania">'.__('Lithuania', 'wpstorecart').'</option>
                    <option value="Luxembourg">'.__('Luxembourg', 'wpstorecart').'</option>
                    <option value="Macao">'.__('Macao', 'wpstorecart').'</option>
                    <option value="Macedonia, The Former Yugoslav Republic of">'.__('Macedonia, The Former Yugoslav Republic of', 'wpstorecart').'</option>
                    <option value="Madagascar">'.__('Madagascar', 'wpstorecart').'</option>
                    <option value="Malawi">'.__('Malawi', 'wpstorecart').'</option>
                    <option value="Malaysia">'.__('Malaysia', 'wpstorecart').'</option>
                    <option value="Maldives">'.__('Maldives', 'wpstorecart').'</option>
                    <option value="Mali">'.__('Mali', 'wpstorecart').'</option>
                    <option value="Malta">'.__('Malta', 'wpstorecart').'</option>
                    <option value="Marshall Islands">'.__('Marshall Islands', 'wpstorecart').'</option>
                    <option value="Martinique">'.__('Martinique', 'wpstorecart').'</option>
                    <option value="Mauritania">'.__('Mauritania', 'wpstorecart').'</option>
                    <option value="Mauritius">'.__('Mauritius', 'wpstorecart').'</option>
                    <option value="Mayotte">'.__('Mayotte', 'wpstorecart').'</option>
                    <option value="Mexico">'.__('Mexico', 'wpstorecart').'</option>
                    <option value="Micronesia, Federated States of">'.__('Micronesia, Federated States of', 'wpstorecart').'</option>
                    <option value="Moldova, Republic of">'.__('Moldova, Republic of', 'wpstorecart').'</option>
                    <option value="Monaco">'.__('Monaco', 'wpstorecart').'</option>
                    <option value="Mongolia">'.__('Mongolia', 'wpstorecart').'</option>
                    <option value="Montserrat">'.__('Montserrat', 'wpstorecart').'</option>
                    <option value="Morocco">'.__('Morocco', 'wpstorecart').'</option>
                    <option value="Mozambique">'.__('Mozambique', 'wpstorecart').'</option>
                    <option value="Myanmar">'.__('Myanmar', 'wpstorecart').'</option>
                    <option value="Namibia">'.__('Namibia', 'wpstorecart').'</option>
                    <option value="Nauru">'.__('Nauru', 'wpstorecart').'</option>
                    <option value="Nepal">'.__('Nepal', 'wpstorecart').'</option>
                    <option value="Netherlands">'.__('Netherlands', 'wpstorecart').'</option>
                    <option value="Netherlands Antilles">'.__('Netherlands Antilles', 'wpstorecart').'</option>
                    <option value="New Caledonia">'.__('New Caledonia', 'wpstorecart').'</option>
                    <option value="New Zealand">'.__('New Zealand', 'wpstorecart').'</option>
                    <option value="Nicaragua">'.__('Nicaragua', 'wpstorecart').'</option>
                    <option value="Niger">'.__('Niger', 'wpstorecart').'</option>
                    <option value="Nigeria">'.__('Nigeria', 'wpstorecart').'</option>
                    <option value="Niue">'.__('Niue', 'wpstorecart').'</option>
                    <option value="Norfolk Island">'.__('Norfolk Island', 'wpstorecart').'</option>
                    <option value="Northern Mariana Islands">'.__('Northern Mariana Islands', 'wpstorecart').'</option>
                    <option value="Norway">'.__('Norway', 'wpstorecart').'</option>
                    <option value="Oman">'.__('Oman', 'wpstorecart').'</option>
                    <option value="Pakistan">'.__('Pakistan', 'wpstorecart').'</option>
                    <option value="Palau">'.__('Palau', 'wpstorecart').'</option>
                    <option value="Palestinian Territory, Occupied">'.__('Palestinian Territory, Occupied', 'wpstorecart').'</option>
                    <option value="Panama">'.__('Panama', 'wpstorecart').'</option>
                    <option value="Papua New Guinea">'.__('Papua New Guinea', 'wpstorecart').'</option>
                    <option value="Paraguay">'.__('Paraguay', 'wpstorecart').'</option>
                    <option value="Peru">'.__('Peru', 'wpstorecart').'</option>
                    <option value="Philippines">'.__('Philippines', 'wpstorecart').'</option>
                    <option value="Pitcairn">'.__('Pitcairn', 'wpstorecart').'</option>
                    <option value="Poland">'.__('Poland', 'wpstorecart').'</option>
                    <option value="Portugal">'.__('Portugal', 'wpstorecart').'</option>
                    <option value="Puerto Rico">'.__('Puerto Rico', 'wpstorecart').'</option>
                    <option value="Qatar">'.__('Qatar', 'wpstorecart').'</option>
                    <option value="Reunion">'.__('Reunion', 'wpstorecart').'</option>
                    <option value="Romania">'.__('Romania', 'wpstorecart').'</option>
                    <option value="Russian Federation">'.__('Russian Federation', 'wpstorecart').'</option>
                    <option value="Rwanda">'.__('Rwanda', 'wpstorecart').'</option>
                    <option value="Saint Helena">'.__('Saint Helena', 'wpstorecart').'</option>
                    <option value="Saint Kitts and Nevis">'.__('Saint Kitts and Nevis', 'wpstorecart').'</option>
                    <option value="Saint Lucia">'.__('Saint Lucia', 'wpstorecart').'</option>
                    <option value="Saint Pierre and Miquelon">'.__('Saint Pierre and Miquelon', 'wpstorecart').'</option>
                    <option value="Saint Vincent and The Grenadines">'.__('Saint Vincent and The Grenadines', 'wpstorecart').'</option>
                    <option value="Samoa">'.__('Samoa', 'wpstorecart').'</option>
                    <option value="San Marino">'.__('San Marino', 'wpstorecart').'</option>
                    <option value="Sao Tome and Principe">'.__('Sao Tome and Principe', 'wpstorecart').'</option>
                    <option value="Saudi Arabia">'.__('Saudi Arabia', 'wpstorecart').'</option>
                    <option value="Senegal">'.__('Senegal', 'wpstorecart').'</option>
                    <option value="Serbia and Montenegro">'.__('Serbia and Montenegro', 'wpstorecart').'</option>
                    <option value="Seychelles">'.__('Seychelles', 'wpstorecart').'</option>
                    <option value="Sierra Leone">'.__('Sierra Leone', 'wpstorecart').'</option>
                    <option value="Singapore">'.__('Singapore', 'wpstorecart').'</option>
                    <option value="Slovakia">'.__('Slovakia', 'wpstorecart').'</option>
                    <option value="Slovenia">'.__('Slovenia', 'wpstorecart').'</option>
                    <option value="Solomon Islands">'.__('Solomon Islands', 'wpstorecart').'</option>
                    <option value="Somalia">'.__('Somalia', 'wpstorecart').'</option>
                    <option value="South Africa">'.__('South Africa', 'wpstorecart').'</option>
                    <option value="South Georgia and The South Sandwich Islands">'.__('South Georgia and The South Sandwich Islands', 'wpstorecart').'</option>
                    <option value="Spain">'.__('Spain', 'wpstorecart').'</option>
                    <option value="Sri Lanka">'.__('Sri Lanka', 'wpstorecart').'</option>
                    <option value="Sudan">'.__('Sudan', 'wpstorecart').'</option>
                    <option value="Suriname">'.__('Suriname', 'wpstorecart').'</option>
                    <option value="Svalbard and Jan Mayen">'.__('Svalbard and Jan Mayen', 'wpstorecart').'</option>
                    <option value="Swaziland">'.__('Swaziland', 'wpstorecart').'</option>
                    <option value="Sweden">'.__('Sweden', 'wpstorecart').'</option>
                    <option value="Switzerland">'.__('Switzerland', 'wpstorecart').'</option>
                    <option value="Syrian Arab Republic">'.__('Syrian Arab Republic', 'wpstorecart').'</option>
                    <option value="Taiwan, Province of China">'.__('Taiwan, Province of China', 'wpstorecart').'</option>
                    <option value="Tajikistan">'.__('Tajikistan', 'wpstorecart').'</option>
                    <option value="Tanzania, United Republic of">'.__('Tanzania, United Republic of', 'wpstorecart').'</option>
                    <option value="Thailand">'.__('Thailand', 'wpstorecart').'</option>
                    <option value="Timor-leste">'.__('Timor-leste', 'wpstorecart').'</option>
                    <option value="Togo">'.__('Togo', 'wpstorecart').'</option>
                    <option value="Tokelau">'.__('Tokelau', 'wpstorecart').'</option>
                    <option value="Tonga">'.__('Tonga', 'wpstorecart').'</option>
                    <option value="Trinidad and Tobago">'.__('Trinidad and Tobago', 'wpstorecart').'</option>
                    <option value="Tunisia">'.__('Tunisia', 'wpstorecart').'</option>
                    <option value="Turkey">'.__('Turkey', 'wpstorecart').'</option>
                    <option value="Turkmenistan">'.__('Turkmenistan', 'wpstorecart').'</option>
                    <option value="Turks and Caicos Islands">'.__('Turks and Caicos Islands', 'wpstorecart').'</option>
                    <option value="Tuvalu">'.__('Tuvalu', 'wpstorecart').'</option>
                    <option value="Uganda">'.__('Uganda', 'wpstorecart').'</option>
                    <option value="Ukraine">'.__('Ukraine', 'wpstorecart').'</option>
                    <option value="United Arab Emirates">'.__('United Arab Emirates', 'wpstorecart').'</option>
                    <option value="United Kingdom">'.__('United Kingdom', 'wpstorecart').'</option>
                    <option value="United States">'.__('United States', 'wpstorecart').'</option>
                    <option value="United States Minor Outlying Islands">'.__('United States Minor Outlying Islands', 'wpstorecart').'</option>
                    <option value="Uruguay">'.__('Uruguay', 'wpstorecart').'</option>
                    <option value="Uzbekistan">'.__('Uzbekistan', 'wpstorecart').'</option>
                    <option value="Vanuatu">'.__('Vanuatu', 'wpstorecart').'</option>
                    <option value="Venezuela">'.__('Venezuela', 'wpstorecart').'</option>
                    <option value="Viet Nam">'.__('Viet Nam', 'wpstorecart').'</option>
                    <option value="Virgin Islands, British">'.__('Virgin Islands, British', 'wpstorecart').'</option>
                    <option value="Virgin Islands, U.S.">'.__('Virgin Islands, U.S.', 'wpstorecart').'</option>
                    <option value="Wallis and Futuna">'.__('Wallis and Futuna', 'wpstorecart').'</option>
                    <option value="Western Sahara">'.__('Western Sahara', 'wpstorecart').'</option>
                    <option value="Yemen">'.__('Yemen', 'wpstorecart').'</option>
                    <option value="Zambia">'.__('Zambia', 'wpstorecart').'</option>
                    <option value="Zimbabwe">'.__('Zimbabwe', 'wpstorecart').'</option>
                    </select></td></tr>';
                }
                if($specific_items[2]=='email') {
                    $output .= '<tr><td>'. $specific_items[0] ;if($specific_items[1]=='required'){$output .= '<ins'.$disable_inline_styles.'><div class="wpsc-required-symbol">'.$wpStoreCartOptions['required_symbol'].'</div></ins>';}$output.='</td><td><input class="input" id="'.wpscSlug($specific_items[0]).'" type="text" size="25" value="'.$_SESSION['wpsc_'.wpscSlug($specific_items[0])].'" name="'.wpscSlug($specific_items[0]).'" /></td></tr>';
                }
                if($specific_items[2]=='separator') {
                    $output .= $specific_items[0] .'<tr><td></td><td></td></tr>';
                }
                if($specific_items[2]=='header') {
                    $output .= '</table><h2>'.$specific_items[0] .'</h2><table>';
                }
                if($specific_items[2]=='text') {
                    $output .= '</table><br />'.$specific_items[0] .'<br /><table>';
                }



        }

        return $output;


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
                        update_usermeta( $user_id, wpscSlug($specific_item[0]), $wpdb->escape($_POST[wpscSlug($specific_item[0])]) );
                    }
                }
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
add_filter('user_contactmethods', 'wpscGrabCustomRegistrationFields',10,1);
add_action('register_form', 'wpscShowCustomRegistrationFields');
add_action('user_register',  'wpscRegisterExtraFields');
add_action('register_post', 'wpscCheckFields',10,3);

?>