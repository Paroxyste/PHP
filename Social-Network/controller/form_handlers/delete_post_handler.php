<?php

declare(strict_types=1);

require('../../config/config.php');

if (
    isset($_GET['post_id'])
) {
    $postId = $_GET['post_id'];
}

if (
    isset($_POST['result'])
) {
    if (
        $_POST['result'] == 'true'
    ) {
        // Delete Post
        $postId = $con->real_escape_string($postId);

        $delPostQuery = "UPDATE posts
                         SET removed='yes'
                         WHERE (id='$postId')";

        $delPost = $con->query($delPostQuery);

        // Check user of posts
        $checkUserQuery = "SELECT posted_by
                           FROM posts
                           WHERE (id='$postId')";

        $checkUser = $con->query($checkUserQuery);
        $row = $checkUser->fetch_assoc();

        $postedBy = $row['posted_by'];

        // Update counter of posts
        $postedBy = $con->real_escape_string($postedBy);

        $updNumPostsQuery = "UPDATE users
                             SET num_posts = num_posts-1
                             WHERE (username='$postedBy')";

        $updNumPosts = $con->query($updNumPostsQuery);
    }
}

?>
