<?php

declare(strict_types=1);

$msgObj = new Message($con, $userLoggedIn);

if (
    isset($_GET['u'])
) {
    $userTo = $_GET['u'];
} else {
    $userTo = $msgObj->GetMostRecentUser();

    if (
        $userTo == FALSE
    ) {
      $userTo = 'new';
    }
}

if (
    $userTo != 'new'
) {
    $userToObj = new User($con, $userTo);
}

if (
    isset($_POST['post_msg'])
) {
    if (
        isset($_POST['msg_body'])
    ) {
        $message = $_POST['msg_body'];
        $dateTime = date('Y-m-d H:i:s');

        $message  = $con->real_escape_string($message);
        $dateTime = $con->real_escape_string($dateTime);
        $userTo   = $con->real_escape_string($userTo);

        $msgObj->SendMessage($userTo, $message, $dateTime);
    }
}

?>