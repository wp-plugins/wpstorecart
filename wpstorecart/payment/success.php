<?php

if (!function_exists('add_action'))
{
    require_once("../../../../../wp-config.php");
}

$wpStoreCartOptions = get_option('wpStoreCartAdminOptions'); 

if(strpos(get_permalink($wpStoreCartOptions['mainpage']),'?')===false) {
    $successpermalink = get_permalink($wpStoreCartOptions['mainpage']) .'?wpsc=success';
} else {
    $successpermalink = get_permalink($wpStoreCartOptions['mainpage']) .'&wpsc=success';
}

        
if(!headers_sent()) {
    header('HTTP/1.1 301 Moved Permanently');
    header('Location: '.$successpermalink);
} else {
    echo '
    <script type="text/javascript">
    <!--
    window.location = "'.$successpermalink.'"
    //-->
    </script>                
    ';
}

?>