<?php

declare(strict_types=1);

include('../../config/config.php');
include('../../model/User.php');
include('../../model/Message.php');

$limit = 5;

$posts = new Post($con, $_REQUEST['userLoggedIn']);
$posts->LoadPostsFriends($_REQUEST, $limit);

?>

