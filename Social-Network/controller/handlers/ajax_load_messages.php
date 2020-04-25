<?php

include('../../config/config.php');
include('../../model/Message.php');
include('../../model/User.php');

$limit = 5;
$msg   = new Message($con, strip_tags($_REQUEST['userLoggedIn']));

echo $msg->GetConversDropdown($_REQUEST, $limit);

?>