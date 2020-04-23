<?php

declare(strict_types=1);

if (
    isset($_POST['cancel'])
) {
    echo "
        <script>
            location.href='settings.php';
        </script>
    ";
}

if (
    isset($_POST['close_account'])
) {
    $closeAccountQuery = "UPDATE users
                          SET user_closed='yes'
                          WHERE (username='$userLoggedIn')";

    $closeAccount = $con->query($closeAccountQuery);

    session_destroy();

    echo "
        <script>
            location.href='login.php';
        </script>
    ";
}

?>
