<?php

declare(strict_types=1);

session_start();

if (
    isset($_SESSION['username'])
) {
    unset($_SESSION['username']);
}

session_destroy();

header('refresh:5; url=login.php');

?>
