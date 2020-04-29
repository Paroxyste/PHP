<?php

declare(strict_types=1);

session_start();

// error_reporting(0);

$timezone = date_default_timezone_set('Europe/Paris');

$host = 'localhost';
$user = 'root';
$pass = '';
$db   = 'social';

$con = new mysqli($host, $user, $pass, $db);

// ----------------------------------------------------------- Check connection
if (
    mysqli_connect_errno()
) {
    printf("Connect failed: %s\n", mysqli_connect_error());
    exit();
}

// ---------------------------------------------------------------- Redirection

// Config folder
if (
    $_SERVER['REQUEST_URI'] == '/config/'
) {
    echo "
        <script>
            location.href='../index.php'
        </script>
    ";
}

// Controller folder
if (
    $_SERVER['REQUEST_URI'] == '/controller/'
) {
    echo "
        <script>
            location.href='../index.php'
        </script>
    ";
}

// Model folder
if (
    $_SERVER['REQUEST_URI'] == '/model/'
) {
    echo "
        <script>
            location.href='../../index.php'
        </script>
    ";
}

// View folder
if (
    $_SERVER['REQUEST_URI'] == '/view/'
) {
    echo "
        <script>
            location.href='../index.php'
        </script>
    ";
}

?>