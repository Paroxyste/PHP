<?php

// -------------------------------------------------------------- Cancel option

if (
    isset($_POST['cancel'])
) {
    echo "
        <script>
            location.href='settings.php';
        </script>
    ";
}

// --------------------------------------------------------------- Close option

$status = 'yes';

if (
    isset($_POST['close_account'])
) {
    $closeAccountQuery = "UPDATE users
                          SET user_closed='$status'
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
