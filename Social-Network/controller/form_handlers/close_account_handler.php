<?php

declare(strict_types=1);

if (
    isset($_POST['cancel'])
) {
    header('Location: index.php');
}

if (
    isset($_POST['close_account'])
) {
    $closeAccountQuery = "UPDATE users
                          SET user_closed='yes'
                          WHERE (username='$userLoggedIn')";

    $closeAccount = $con->query($closeAccountQuery);

    session_destroy();
    header('Location: login.php');
}

?>
