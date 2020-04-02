<?php

declare(strict_types=1);

// Load comments
$getCommentsQuery = "SELECT *
                     FROM comments
                     WHERE (post_id='$postId')
                     ORDER BY id
                     DESC";

$getComments = $con->query($getCommentsQuery);
$count = $getComments->num_rows;

if (
    $count != 0
) {
    while ($comment = $getComments->fetch_assoc()) {
        $commentBody = $comment['post_body'];
        $postedTo    = $comment['posted_to'];
        $postedBy    = $comment['posted_by'];
        $dateTime    = $comment['date_added'];
        $removed     = $comment['removed'];

        // Insert Timeframe
        include('./controller/handlers/timeframe.php');

        echo "
            <div class='comment_section media'>
                <img class='mr-2 ml-3 avatar-sm rounded-circle'
                     src='".strip_tags($userObj->GetProfilePic())."'
                     alt='User placeholder image' />

                <div class='media-body ml-2'>
                    <h5 class='mt-0'>
                        <a href='". strip_tags($postedBy)."'>"
                            . strip_tags($userObj->GetFullName()) .
                        "</a>

                        <small class='text-muted ml-1'>"
                            . strip_tags($timeMsg) .
                        "</small>
                    </h5>"

                    . data_decode($commentBody) .

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