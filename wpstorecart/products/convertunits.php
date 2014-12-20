<?php

@header('Cache-Control: no-cache, must-revalidate');
@header('Expires: Mon, 26 Jul 1997 05:00:00 GMT');

if (!function_exists('add_action')) {
    require_once("../../../../../wp-config.php");
}

if($_POST['operation']=='poundstokg') {
    echo wpscProductConvertPoundsToKilograms($_POST['value']);
} elseif($_POST['operation']=='kgtopounds') {
    echo wpscProductConvertKilogramsToPounds($_POST['value']);
} elseif($_POST['operation']=='inchestocm') {
    echo wpscProductConvertInchesToCentimeters($_POST['value']);
} elseif($_POST['operation']=='cmtoinches') {
    echo wpscProductConvertCentimetersToInches($_POST['value']);
} else {
    echo $_POST['value'];
}


?>