<?php

// -------------------------------------------------------------- Load comments

$getCommentsQuery = "SELECT post_body, posted_by, date_added
                     FROM comments
                     WHERE (post_id='$postId')
                     ORDER BY id
                     DESC";

$getComments = $con->query($getCommentsQuery);

$count = $getComments->num_rows;

// ---------------------------------------------------------- Comments template

if (
    $count > 0
) {
    while ($row = $getComments->fetch_assoc()) {
        $dateTime = $row['date_added'];
        $postedBy = $row['posted_by'];

        // Get posted_by user data
        $getUserDataQuery = "SELECT first_name, last_name, profile_pic
                             FROM users
                             WHERE (username='$postedBy')";

        $getUserData = $con->query($getUserDataQuery);

        $user = $getUserData->fetch_assoc();

        // Insert Timeframe
        include('./controller/handlers/timeframe.php');

        echo "
            <div class='comment_section media'>
                <img class='mr-2 ml-3 avatar-sm rounded-circle'
                     src='". strip_tags($user['profile_pic']) ."'
                     alt='User placeholder image' />

                <div class='media-body ml-2'>
                    <h5 class='mt-0 text-primary'>"
                        . strip_tags($user['first_name']) .
                        " " . strip_tags($user['last_name']) .

                        "<small class='text-muted ml-1'>"
                            . strip_tags($timeMsg) .
                        "</small>
                    </h5>"

                    . data_decode($row['post_body']) .

                    "</div>
                </div>

                <hr class='comment mt-3 mb-3' />

            </div>
        ";
    }
} else {
    echo "
        <div class='text-center'>
            <span class='text-danger'>
                <i class='ti-na mr-1'></i>
                No more comments to show ...
            </span>
        </div>
    ";
}

function data_decode($data) {
    $data = html_entity_decode($data);
    $data = strip_tags($data);
    $data = str_replace('\r\n', '\n', $data);
    $data = nl2br($data);

    return $data;
}

?>