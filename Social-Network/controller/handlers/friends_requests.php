<?php

// -------------------------------------------------------------- Remove friend

if (
    isset($_POST['remove_friend'])
) {
    $user = new User($con, strip_tags($userLoggedIn));

    $user->RemoveFriend($username);

    echo "
        <script>
            location.href='". strip_tags($username) ."';
        </script>
    ";
}

// ----------------------------------------------------------------- Add friend

if (
    isset($_POST['add_friend'])
) {
    $user = new User($con, strip_tags($userLoggedIn));

    $user->SendRequest($username);

    echo "
        <script>
            location.href='". strip_tags($username) ."';
        </script>
    ";
}

// -------------------------------------------------------------------- Request

if (
    isset($_POST['respond_request'])
) {
    echo "
        <script>
            location.href='requests.php';
        </script>
    ";
}

?>