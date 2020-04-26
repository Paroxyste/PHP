<?php

session_start();

$url = 'login.php';

if (
    isset($_SESSION['username'])
) {
    unset($_SESSION['username']);
}

session_destroy();

echo "
    <script>
        location.href='". strip_tags($url) ."';
    </script>
";

?>
