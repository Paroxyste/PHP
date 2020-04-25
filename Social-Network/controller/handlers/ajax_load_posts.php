<?php

require('../../config/config.php');
require('../../model/Post.php');
require('../../model/User.php');

$limit = 10;
$posts = new Post($con, strip_tags($_REQUEST['userLoggedIn']));

$posts->LoadPosts($_REQUEST, $limit);

?>
