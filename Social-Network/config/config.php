<?php

declare(strict_types=1);

session_start();

$timezone = date_default_timezone_set('Europe/Paris');

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'my_bdd';

$con = new mysqli($host, $user, $pass, $db);

// check connection
if (
    mysqli_connect_errno()
) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

?>
