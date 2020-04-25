<?php

require('../../config/config.php');
require('../../model/Message.php');
require('../../model/User.php');

$limit = 5;
$msg   = new Message($con, strip_tags($_REQUEST['userLoggedIn']));

echo $msg->GetConversDropdown($_REQUEST, $limit);

?>