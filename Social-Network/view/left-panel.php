<?php

declare(strict_types=1);

if (
    $userLoggedIn = $_SESSION['username']
) {
    require('./view/dashboard/user_details_upl.php');
    require('./view/dashboard/message_box.php');
} else {
    require('./view/dashboard/user_details.php');
}

?>