<?php

$msgObj = new Message($con, strip_tags($userLoggedIn));

$errMsg  = NULL;
$userNew = 'new';

// ---------------------------------------------------------------- New convers

if (
    isset($_GET['u'])
) {
    $userTo = strip_tags($_GET['u']);

    $userToCheckQuery = "SELECT username
                         FROM users
                         WHERE (username='$userTo')";

    $userToCheck = $con->query($userToCheckQuery);

    $row = $userToCheck->num_rows;

    if (
        $userTo != $userNew
        && $row == NULL
    ) {
        echo "
            <script>
                location.href='" . strip_tags($userLoggedIn) ."';
            </script>
        ";
    }
} else {
    $userTo = $msgObj->GetMostRecentUser();

    if (
        $userTo == FALSE
    ) {
        $userTo = $userNew;
    }
}

// --------------------------------------------------------------- Load Convers

if (
    $userTo != $userNew
) {
    $userToObj = new User($con, strip_tags($userTo));
}

// --------------------------------------------------------------- Post new msg

if (
    isset($_POST['post_msg'])
) {
    // Filter message
    $message = filter_data(
                    filter_var($_POST['msg_body'], FILTER_SANITIZE_STRING)
                );

    // Empty field
    if (
        empty($message)
    ) {
        $errMsg = 'This field is required';
    }

    // Width filed
    if (
        strlen($message) < 1
        || strlen($message) > 160
    ) {
        $errMsg = 'Your message must be between 1 and 160 characters';
    }

    if (
        empty($errMsg)
    ) {
        $dateTime = date('Y-m-d H:i:s');

        $message  = $con->real_escape_string($message);
        $dateTime = $con->real_escape_string($dateTime);
        $userTo   = $con->real_escape_string($userTo);
    
        $msgObj->SendMessage($userTo, $message, $dateTime);
    }
}

// ---------------------------------------------------------------- Data filter

function filter_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlentities($data);

    return $data;
}

?>