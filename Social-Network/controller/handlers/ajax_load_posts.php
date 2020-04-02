<?php

declare(strict_types=1);

include('../../config/config.php');
include('../../model/User.php');
include('../../model/Post.php');

$limit = 10;

$posts = new Post($con, $_REQUEST['userLoggedIn']);
$posts->LoadPosts($_REQUEST, $limit);

?>
