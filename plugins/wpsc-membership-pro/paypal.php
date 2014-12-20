<?PHP
/**
 * wpsc Membership PRO - Commercial Membership PHP script v2.0.0
 *
 * PayPal subscription
 */

global $wpsc_buy_now, $purchaser_user_id, $purchaser_email, $membershipOptions, $wpsc_table_name, $wpsc_self_path, $wpsc_paypal_testmode, $wpsc_paypal_ipn, $wpsc_membership_product_name, $wpsc_membership_product_number, $wpsc_button_classes, $wpsc_paypal_currency_code, $wpsc_paypal_email, $wpsc_price_type,$wpsc_membership_trial1_allow, $wpsc_membership_trial2_allow, $wpsc_membership_trial1_amount , $wpsc_membership_trial2_amount, $wpsc_membership_regular_amount,$wpsc_membership_trial1_numberof,$wpsc_membership_trial2_numberof,$wpsc_membership_regular_numberof,$wpsc_membership_trial1_increment,$wpsc_membership_trial2_increment,$wpsc_membership_regular_increment;

error_reporting(0);

if($_POST['isclicked']=='x') {

    $purchaser_user_id = $_POST['purchaser_user_id'];
    $purchaser_email = $_POST['purchaser_email'];
    $membershipOptions = unserialize(base64_decode($_POST['membershipOptions']));
    $wpsc_table_name = $_POST['wpsc_table_name'];
    $wpsc_self_path = $_POST['wpsc_self_path'];
    $wpsc_paypal_testmode = $_POST['wpsc_paypal_testmode'];
    $wpsc_paypal_ipn = $_POST['wpsc_paypal_ipn'];
    $wpsc_membership_product_name = $_POST['wpsc_membership_product_name'];
    $wpsc_membership_product_number = $_POST['wpsc_membership_product_number'];
    $wpsc_button_classes = $_POST['wpsc_button_classes'];
    $wpsc_paypal_currency_code = $_POST['wpsc_paypal_currency_code'];
    $wpsc_paypal_email = $_POST['wpsc_paypal_email'];
    $wpsc_price_type = $_POST['wpsc_price_type'];
    $wpsc_membership_trial1_allow = $_POST['wpsc_membership_trial1_allow'];
    $wpsc_membership_trial2_allow = $_POST['wpsc_membership_trial2_allow'];
    $wpsc_membership_trial1_amount  = $_POST['wpsc_membership_trial1_amount'];
    $wpsc_membership_trial2_amount = $_POST['wpsc_membership_trial2_amount'];
    $wpsc_membership_regular_amount = $_POST['wpsc_membership_regular_amount'];
    $wpsc_membership_trial1_numberof = $_POST['wpsc_membership_trial1_numberof'];
    $wpsc_membership_trial2_numberof = $_POST['wpsc_membership_trial2_numberof'];
    $wpsc_membership_regular_numberof = $_POST['wpsc_membership_regular_numberof'];
    $wpsc_membership_trial1_increment = $_POST['wpsc_membership_trial1_increment'];
    $wpsc_membership_trial2_increment = $_POST['wpsc_membership_trial2_increment'];
    $wpsc_membership_regular_increment = $_POST['wpsc_membership_regular_increment'];


    if (!defined('PHP_VERSION_ID')) {
        $version = explode('.', PHP_VERSION);

        define('PHP_VERSION_ID', ($version[0] * 10000 + $version[1] * 100 + $version[2]));
    }



    if(!function_exists('mysqli_pconnect')) {
        function mysqli_pconnect($host, $username, $password, $new_link = false, $port = 0)
        {
            if(PHP_VERSION_ID >= 50300) {
                $persistant = 'p:';
            } else {
                $persistant = '';
            }
            if($port) {
                return @mysqli_connect($persistant.$host, $username, $password, $new_link, $port);
            } else {
                return @mysqli_connect($persistant.$host, $username, $password, $new_link);
            }
        }
    }

    $link = @mysqli_pconnect($membershipOptions['databasehost'], $membershipOptions['databaseuser'], $membershipOptions['databasepass'], $membershipOptions['databasename']);
    $dbi = 'mysqli';
    if (!$link) {
        $link = @mysql_pconnect($membershipOptions['databasehost'], $membershipOptions['databaseuser'], $membershipOptions['databasepass']);
        $dbi = 'mysql';
        if (!$link) {
            die('Could not connect: ' .$dbi. mysql_error());
        }
    }

    if($dbi == 'mysqli') {
        $db_selected = mysqli_select_db($link, $membershipOptions['databasename']);
        if (!$db_selected) {
            echo 'Can\'t use '.$membershipOptions['databasename'].': ' .$dbi.' '.mysqli_errno().' '. mysqli_error();
        }
    } else {
        $db_selected = mysql_select_db($membershipOptions['databasename'], $link);
        if (!$db_selected) {
            echo 'Can\'t use '.$membershipOptions['databasename'].': ' .$dbi. mysql_error();
        }
    }

    global $wpdb, $wpStoreCart, $cart, $current_user;
    $table_name = $membershipOptions['databaseprefix'] . "wpstorecart_orders";
    $timestamp = date('Ymd');
    if(!isset($_COOKIE['wpscPROaff']) || !is_numeric($_COOKIE['wpscPROaff'])) {
        $affiliateid = 0;
    } else {
        $affiliateid = $_COOKIE['wpscPROaff'];
        //setcookie ("wpscPROaff", "", time() - 3600); // Remove the affiliate ID
    }

    $timestamp = date('Ymd');
    $table_name = $membershipOptions['databaseprefix'] . "wpstorecart_orders";
    $table_name_meta = $membershipOptions['databaseprefix'] . "wpstorecart_orders";
    $grabrecord = "
    INSERT INTO `{$table_name}`
    (`primkey`, `orderstatus`, `cartcontents`, `paymentprocessor`, `price`, `shipping`,
    `wpuser`, `email`, `affiliate`, `date`) VALUES
    (NULL, 'Pending', '{$wpsc_membership_product_number}*1', 'PayPal Subscription', '0', '0', '{$purchaser_user_id}', '{$purchaser_email}', '{$affiliateid}', '{$timestamp}');
    ";

    if($dbi == 'mysqli') {
        $result = mysqli_query($link, $grabrecord);
        $lastID = mysqli_insert_id($link);

    } else {
        $result = mysql_query($grabrecord);
        $lastID = mysql_insert_id($link);

    }

    if(isset($_COOKIE['wpscPROaff']) || is_numeric($_COOKIE['wpscPROaff'])) { // More affiliate code
        $affiliateSql = "INSERT INTO `{$table_name_meta}` (`primkey` ,`value` ,`type` ,`foreignkey`)VALUES (NULL , '0.00', 'affiliatepayment', '{$lastID}');";
        if($dbi == 'mysqli') {
            $result = mysqli_query($link, $affiliateSql);
        } else {
            $result = mysql_query($affiliateSql);
        }
    }

    global $lastID;
    $keytoedit = $lastID;


    function displayPayPalMembershipForm() {
        global $lastID, $purchaser_user_id, $purchaser_email, $wpsc_table_name, $wpsc_self_path, $wpsc_paypal_testmode, $wpsc_paypal_ipn, $wpsc_membership_product_name, $wpsc_membership_product_number, $wpsc_button_classes, $wpsc_paypal_currency_code, $wpsc_paypal_email, $wpsc_price_type,$wpsc_membership_trial1_allow, $wpsc_membership_trial2_allow, $wpsc_membership_trial1_amount , $wpsc_membership_trial2_amount, $wpsc_membership_regular_amount,$wpsc_membership_trial1_numberof,$wpsc_membership_trial2_numberof,$wpsc_membership_regular_numberof,$wpsc_membership_trial1_increment,$wpsc_membership_trial2_increment,$wpsc_membership_regular_increment;

        $output = '
        <input type="hidden" name="business" value="'.$wpsc_paypal_email.'" />
        <input type="hidden" name="cmd" value="_xclick-subscriptions" />
        <input type="hidden" name="currency_code" value="'.$wpsc_paypal_currency_code.'" />
        <input type="hidden" name="notify_url" value="'.$wpsc_paypal_ipn.'" />
        <input type="hidden" name="custom" value="'.$lastID.'" />

        <!-- Identify the subscription. -->
        <input type="hidden" name="item_name" value="'.$wpsc_membership_product_name.'" />
        <input type="hidden" name="item_number" value="'.$wpsc_membership_product_number.'" />
        ';

        if($wpsc_membership_trial1_allow=='yes') {
            $output .= '
            <!-- Set the terms of the 1st trial period. -->
            <input type="hidden" name="a1" value="'.$wpsc_membership_trial1_amount.'" />
            <input type="hidden" name="p1" value="'.$wpsc_membership_trial1_numberof.'" />
            <input type="hidden" name="t1" value="'.$wpsc_membership_trial1_increment.'" />
            ';
        }

        if($wpsc_membership_trial1_allow=='yes' && $wpsc_membership_trial2_allow=='yes') {
            $output .= '
         <!-- Set the terms of the 2nd trial period. -->
        <input type="hidden" name="a2" value="'.$wpsc_membership_trial2_amount.'" />
        <input type="hidden" name="p2" value="'.$wpsc_membership_trial2_numberof.'" />
        <input type="hidden" name="t2" value="'.$wpsc_membership_trial2_increment.'" />
            ';
        }

        $output .= '
        <!-- Set the terms of the regular subscription. -->
        <input type="hidden" name="a3" value="'.$wpsc_membership_regular_amount.'" />
        <input type="hidden" name="p3" value="'.$wpsc_membership_regular_numberof.'" />
        <input type="hidden" name="t3" value="'.$wpsc_membership_regular_increment.'" />
        <input type="hidden" name="src" value="1">

         <!-- Display the payment button. -->
    ';

        return $output;
    }

        if($wpsc_paypal_testmode=='false') {
            $form_action = 'https://www.paypal.com/cgi-bin/webscr';
        } else {
            $form_action = 'https://www.sandbox.paypal.com/cgi-bin/webscr';
        }

            echo "<html>\n";
            echo "<head><title>Processing Payment...</title></head>\n";
            echo "<body onload=\"document.forms['gateway_form'].submit();\">\n";

            echo '<center><img src="'.$wpsc_self_path.'redirect.gif" alt="redirecting" />';

            echo '</center>';


            echo "<center><p style=\"text-align:center;\"><h2>Please wait, your order is being processed and you";
            echo " will be redirected to the payment website.</h2></p></center>\n";
            echo "<form method=\"POST\" name=\"gateway_form\" ";
            echo "action=\"" . $form_action . "\">\n";

            echo displayPayPalMembershipForm();

            echo "<center><p style=\"text-align:center;\"><br/><br/>If you are not automatically redirected to ";
            echo "payment website within 5 seconds...<br/><br/>\n";
            echo "<input type=\"submit\" value=\"Click Here\"></p></center>\n";

            echo "</form>\n";
            echo "</body></html>\n";
} else {
	if(!function_exists('wpscMembershipButton')) {
		function wpscMembershipButton() {
			global $wpsc_buy_now, $purchaser_user_id, $purchaser_email, $membershipOptions, $wpsc_table_name, $wpsc_self_path, $wpsc_paypal_testmode, $wpsc_paypal_ipn, $wpsc_membership_product_name, $wpsc_membership_product_number, $wpsc_button_classes, $wpsc_paypal_currency_code, $wpsc_paypal_email, $wpsc_price_type,$wpsc_membership_trial1_allow, $wpsc_membership_trial2_allow, $wpsc_membership_trial1_amount , $wpsc_membership_trial2_amount, $wpsc_membership_regular_amount,$wpsc_membership_trial1_numberof,$wpsc_membership_trial2_numberof,$wpsc_membership_regular_numberof,$wpsc_membership_trial1_increment,$wpsc_membership_trial2_increment,$wpsc_membership_regular_increment, $wpsc_membership_product_itemurl;
			return '<form action="'.$wpsc_self_path.'paypal.php" method="post">
				<input type="hidden" value="x" name="isclicked" />
				<input type="hidden" value="'.$purchaser_user_id.'" name="purchaser_user_id" />
				<input type="hidden" value="'.$purchaser_email.'" name="purchaser_email" />
                <input type="hidden" class="my-item-url" name="my-item-url" value="'.$wpsc_membership_product_itemurl.'" />
				<input type="hidden" value="'.base64_encode(serialize($membershipOptions)).'" name="membershipOptions" />
				<input type="hidden" value="'.$wpsc_table_name.'" name="wpsc_table_name" />
				<input type="hidden" value="'.$wpsc_self_path.'" name="wpsc_self_path" />
				<input type="hidden" value="'.$wpsc_paypal_testmode.'" name="wpsc_paypal_testmode" />
				<input type="hidden" value="'.$wpsc_paypal_ipn.'" name="wpsc_paypal_ipn" />
				<input type="hidden" value="'.$wpsc_membership_product_name.'" name="wpsc_membership_product_name" />
				<input type="hidden" value="'.$wpsc_membership_product_number.'" name="wpsc_membership_product_number" />
				<input type="hidden" value="'.$wpsc_button_classes.'" name="wpsc_button_classes" />
				<input type="hidden" value="'.$wpsc_paypal_currency_code.'" name="wpsc_paypal_currency_code" />
				<input type="hidden" value="'.$wpsc_paypal_email.'" name="wpsc_paypal_email" />
				<input type="hidden" value="'.$wpsc_price_type.'" name="wpsc_price_type" />
				<input type="hidden" value="'.$wpsc_membership_trial1_allow.'" name="wpsc_membership_trial1_allow" />
				<input type="hidden" value="'.$wpsc_membership_trial2_allow.'" name="wpsc_membership_trial2_allow" />
				<input type="hidden" value="'.$wpsc_membership_trial1_amount.'" name="wpsc_membership_trial1_amount" />
				<input type="hidden" value="'.$wpsc_membership_trial2_amount.'" name="wpsc_membership_trial2_amount" />
				<input type="hidden" value="'.$wpsc_membership_regular_amount.'" name="wpsc_membership_regular_amount" />
				<input type="hidden" value="'.$wpsc_membership_trial1_numberof.'" name="wpsc_membership_trial1_numberof" />
				<input type="hidden" value="'.$wpsc_membership_trial2_numberof.'" name="wpsc_membership_trial2_numberof" />
				<input type="hidden" value="'.$wpsc_membership_regular_numberof.'" name="wpsc_membership_regular_numberof" />
				<input type="hidden" value="'.$wpsc_membership_trial1_increment.'" name="wpsc_membership_trial1_increment" />
				<input type="hidden" value="'.$wpsc_membership_trial2_increment.'" name="wpsc_membership_trial2_increment" />
				<input type="hidden" value="'.$wpsc_membership_regular_increment.'" name="wpsc_membership_regular_increment" />
				<input type="submit" value="'.$wpsc_buy_now.'"  class="wpsc-button wpsc-buynow '.$wpsc_button_classes.'" />
				</form>
	';
		}
	}
}

?>