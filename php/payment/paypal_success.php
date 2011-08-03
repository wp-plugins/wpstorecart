<?PHP

// wpStoreCart, (c) 2010 wpStoreCart.com.  All rights reserved.

global $wpsc_error_reporting;
if($wpsc_error_reporting==false) {
    error_reporting(0);
}
if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}

global $wpStoreCart, $current_user;
get_currentuserinfo();
$message = '';
if (isset($wpStoreCart)) {
	$devOptions = $wpStoreCart->getAdminOptions();

         // In case any of our lines are larger than 70 characters, we should use wordwrap()
        $message = wordwrap($wpStoreCart->makeEmailTxt($devOptions['emailonpurchase']) . $wpStoreCart->makeEmailTxt($devOptions['emailsig']), 70);

        $headers = 'From: '.$current_user->user_email . "\r\n" .
            'Reply-To: ' .$current_user->user_email. "\r\n" .
            'X-Mailer: PHP/wpStoreCart v'.$wpstorecart_version;

        // Send an email when purchase is submitted
        mail($current_user->user_email, 'Thank you for your recent purchase', $message, $headers);


        header ('HTTP/1.1 301 Moved Permanently');
        if(strpos(get_permalink($devOptions['mainpage']),'?')===false) {
            header ('Location: '.get_permalink($devOptions['mainpage']).'?wpsc=success');
        } else {
            header ('Location: '.get_permalink($devOptions['mainpage']).'&wpsc=success');
        }
}
?>