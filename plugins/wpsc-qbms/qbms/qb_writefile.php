<?php
//This script is accessed by Intuit's QBMS to return data from a request.

$PHP_ConnectionTicket = $_POST['conntkt'];
$PHP_AppData = $_POST['appdata'];
$PHP_AppID = $_POST['appid'];

$handle = fopen("ticket.txt", "w");
fwrite($handle, $PHP_ConnectionTicket."\n");
fwrite($handle, $PHP_AppData."\n");
fwrite($handle, $PHP_AppID."\n"); 

?>