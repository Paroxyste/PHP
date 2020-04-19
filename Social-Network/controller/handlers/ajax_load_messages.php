<?php

declare(strict_types=1);

include('../../config/config.php');
include('../../model/User.php');
include('../../model/Message.php');

$limit = 5;

$message = new Message($con, $_REQUEST['userLoggedIn']);
echo $message->GetConversDropdown($_REQUEST, $limit);

?>