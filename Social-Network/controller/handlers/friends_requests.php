<?php

if (
    isset($_POST['remove_friend'])
) {
    $user = new User($con, $userLoggedIn);

    $user->RemoveFriend($username);

    echo "
        <script>
            location.href='". strip_tags($username) ."';
        </script>
    ";
}

if (
    isset($_POST['add_friend'])
) {
    $user = new User($con, $userLoggedIn);

    $user->SendRequest($username);

    echo "
        <script>
            location.href='". strip_tags($username) ."';
        </script>
    ";
}

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