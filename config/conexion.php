<?php
$serverName = "sqlserver"; 
$database = getenv('DB_NAME');           
$uid = getenv('DB_USER');                 
$pwd = getenv('DB_PASSWORD');  

$conn = sqlsrv_connect($serverName, array(
    "Database" => $database,
    "UID"      => $uid,
    "PWD"      => $pwd,
    "CharacterSet" => "UTF-8"
));

if ($conn === false) {
    die(print_r(sqlsrv_errors(), true));
}
?>
