<?php

declare(strict_types=1);

include('../../config/config.php');
include('../../model/User.php');
include('../../model/Notification.php');

$limit = 5;

$notifs = new Notification($con, $_REQUEST['userLoggedIn']);
$notifs->GetNotifications($_REQUEST, $limit);

?>
