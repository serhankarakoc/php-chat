<?php
$db_host = '';
$db_user = '';
$db_pass = '';
$db_name = '';

$conn = mysqli_connect($db_host, $db_user, $db_pass, $db_name);

if (!$conn) {
    die("Bağlantı hatası: " . mysqli_connect_error());
}
?> 