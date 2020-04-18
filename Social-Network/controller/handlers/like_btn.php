<?php

declare(strict_types=1);

// Get ID of posts
if (
    isset($_GET['post_id'])
) {
    $id = $_GET['post_id'];
}

$getLikesQuery = "SELECT likes, posted_by
                  FROM posts
                  WHERE (id='$id')";

$getLikes = $con->query($getLikesQuery);

$row = $getLikes->fetch_assoc();

$totalLikes = $row['likes'];
$userLiked  = $row['posted_by'];

$userDetailsQuery = "SELECT *
                     FROM users
                     WHERE (username='$userLiked')";

$userDetails = $con->query($userDetailsQuery);

$row = $userDetails->fetch_assoc();

$totalUsersLikes = $row['num_likes'];

// Like button
if (
    isset($_POST['like_btn'])
) {
    $totalLikes++;

    $id = $con->real_escape_string($id);

    $updLikesCounterQuery = "UPDATE posts
                             SET likes='$totalLikes'
                             WHERE (id='$id')";

    $updLikesCounter = $con->query($updLikesCounterQuery);

    $totalUsersLikes++;

    $userLiked = $con->real_escape_string($userLiked);

    $updUsersLikesCounterQuery = "UPDATE users
                                  SET num_likes='$totalUsersLikes'
                                  WHERE (username='$userLiked')";

    $updUsersLikesCounter = $con->query($updUsersLikesCounterQuery);

    $userLoggedIn = $con->real_escape_string($userLoggedIn);
    $id       = $con->real_escape_string($id);

    $insertUserQuery = "INSERT INTO likes
                        VALUES(0, '$userLoggedIn', '$id')";

    $insertUser = $con->query($insertUserQuery);

    // Create new notification
    if (
        $userLiked != $userLoggedIn
    ) {
        $notifs = new Notification($con, $userLoggedIn);
        $notifs->InsNotifs($id, $userLiked, 'like');
    }
}

// Unlike button
if (
    isset($_POST['unlike_btn'])
) {
    $totalLikes--;

    $id = $con->real_escape_string($id);

    $updLikesCounterQuery = "UPDATE posts
                             SET likes='$totalLikes'
                             WHERE (id='$id')";

    $updLikesCounter = $con->query($updLikesCounterQuery);
    $totalUsersLikes--;

    $userLiked = $con->real_escape_string($userLiked);

    $updUsersLikesCounterQuery = "UPDATE users
                                  SET num_likes='$totalUsersLikes'
                                  WHERE (username='$userLiked')";

    $updUsersLikesCounter = $con->query($updUsersLikesCounterQuery);

    $userLoggedIn = $con->real_escape_string($userLoggedIn);
    $id           = $con->real_escape_string($id);

    $deleteUserQuery = "DELETE FROM likes
                        WHERE (user_from='$userLoggedIn'
                        AND post_id='$id')";

    $deleteUser = $con->query($deleteUserQuery);
}

// Check for previous like
$checkIsLikedQuery = "SELECT *
                      FROM likes
                      WHERE (user_from='$userLoggedIn'
                      AND post_id='$id')";

$checkIsLiked = $con->query($checkIsLikedQuery);

$isLiked = $checkIsLiked->num_rows;

if (
    $isLiked > 0
) {
    echo "
        <form action='like.php?post_id=" . strip_tags($id) . "'
              method='POST'>
                <button type='submit' name='unlike_btn'
                        class='btn btn-sm btn-link text-danger'>
                    <i class='ti-heart'></i>
                    (". $totalLikes .")
                </button>
            </div>
        </form>
    ";

} else {
    echo "
        <form action='like.php?post_id=" . strip_tags($id) . "'
              method='POST'>
            <div>
                <button type='submit' name='like_btn'
                        class='btn btn-sm btn-link text-muted'>
                    <i class='ti-heart'></i>
                    (". $totalLikes .")
                </button>
            </div>
        </form>
    ";
}

?>