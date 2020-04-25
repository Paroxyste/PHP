<?php

require('../../config/config.php');

// ---------------------------------------------------------------- Get post ID

if (
    isset($_GET['post_id'])
) {
    $postId = $_GET['post_id'];
}

// ---------------------------------------------------------------- Delete post

$status = 'yes';

if (
    isset($_POST['result'])
) {
    if (
        $_POST['result'] == 'true'
    ) {
        // Delete Post
        $postId = $con->real_escape_string($postId);
        $status = $con->real_escape_string($status);

        $delPostQuery = "UPDATE posts
                         SET removed='$status'
                         WHERE (id='$postId')";

        $delPost = $con->query($delPostQuery);

        // Check user of posts
        $checkUserQuery = "SELECT posted_by
                           FROM posts
                           WHERE (id='$postId')";

        $checkUser = $con->query($checkUserQuery);

        $row = $checkUser->fetch_assoc();

        $postedBy = $row['posted_by'];

        // Check num_posts counter
        $checkNumPostsQuery = "SELECT num_posts
                               FROM users
                               WHERE (username='$postedBy')";
        
        $checkNumPosts = $con->query($checkNumPostsQuery);

        $user = $checkNumPosts->fetch_assoc();

        $numPosts = $user['num_posts'];
        $numPosts = intval($numPosts);
        $numPosts--;

        $newCounter = strval($numPosts);

        // Update counter of posts
        $postedBy   = $con->real_escape_string($postedBy);
        $newCounter = $con->real_escape_string($newCounter);

        $updNumPostsQuery = "UPDATE users
                             SET num_posts = '$newCounter'
                             WHERE (username='$postedBy')";

        $updNumPosts = $con->query($updNumPostsQuery);
    }
}

?>
