<?php
if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}

global $wpdb;

$productkey = intval($_POST['productkey']);
$userid = intval($_POST['userid']);
$orderid = intval($_POST['orderid']);
$custdefkey = intval($_POST['custdefkey']);
$ipaddress = $_POST['ipaddress'];
$guestemail = $_POST['guestemail'];
$customtext = $_POST['customtext'];
$textformat = $_POST['textformat'];
$textx = intval($_POST['textx']);
$texty = intval($_POST['texty']);
$textwidth = intval($_POST['textwidth']);
$textheight = intval($_POST['textheight']);
$image = $_POST['image'];
$imagex = intval($_POST['imagex']);
$imagey = intval($_POST['imagey']);
$imagewidth = intval($_POST['imagewidth']);
$imageheight = intval($_POST['imageheight']);
$wpstorecartitemqty = intval($_POST['wpstorecartitemqty']);

$table_name = $wpdb->prefix . "wpstorecart_custom_orders";

$insert = "
    INSERT INTO `{$table_name}` (
        `primkey` ,
        `productkey` ,
        `userid` ,
        `orderid` ,
        `custdefkey` ,
        `ipaddress` ,
        `guestemail` ,
        `customtext` ,
        `textformat` ,
        `textx` ,
        `texty` ,
        `textwidth` ,
        `textheight` ,
        `image` ,
        `imagex` ,
        `imagey` ,
        `imagewidth` ,
        `imageheight`
    )
    VALUES (
        NULL , '{$productkey}', '{$userid}', '{$orderid}', '{$custdefkey}', '{$ipaddress}', '{$guestemail}', '{$customtext}', '{$textformat}', '{$textx}', '{$texty}', '{$textwidth}', '{$textheight}', '{$image}', '{$imagex}', '{$imagey}', '{$imagewidth}', '{$imageheight}'
    );
";

$wpdb->query($insert);
$keyToLookup = $wpdb->insert_id;

if(@!isset($_SESSION)) {
        @session_start();
}    

if(@$_SESSION['wpsc_custom_keys']!=null) {
    $_SESSION['wpsc_custom_keys'] .= ','.$keyToLookup;  
} else {
    $_SESSION['wpsc_custom_keys'] = $keyToLookup; 
}

$appstore = 'null=null';
if(@isset($_GET['wpsc-app-store'])) {
    $appstore = 'wpsc-app-store=1';
}

if($_POST['first']==1) { // This code only executes once
    $permalink = plugins_url().'/wpstorecart/wpstorecart/cart/quickaddtocart.php?'.$appstore.'&wpsc_pid='.$productkey.'&wpsc_qty='.$wpstorecartitemqty;

    if (!headers_sent()) {
        header('Location: '.$permalink);
        exit;
    } else {
        echo '
        <script type="text/javascript">
        /* <![CDATA[ */
        window.location = "'.$permalink.'"
        /* ]]> */
        </script>
        ';          
        exit;    
    }
}  

?>