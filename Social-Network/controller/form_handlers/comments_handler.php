<?php

// -------------------------------------------------------------------- Post ID

if (
    isset($_GET['post_id'])
) {
    $postId = $_GET['post_id'];
}

// ------------------------------------------------------------------ Post data

$userQuery = "SELECT posted_to
              FROM posts
              WHERE (id='$postId')";

$user = $con->query($userQuery);

$row = $user->fetch_assoc();

$postedTo = $row['posted_to'];

// ---------------------------------------------------------------- Add comment

if (
    isset($_POST['postComment' . $postId])
) {
    // Filter postBody
    $postBody = filter_data(
                    filter_var($_POST['post_body'], FILTER_SANITIZE_STRING)
                );

    // Empty field
    if (
        empty($postBody)
    ) {
        $errPostBody = 'This field is required';
    }

    // Width filed
    if (
        strlen($postBody) < 1
        || strlen($postBody) > 160
    ) {
        $errPostBody = 'Your comment must be between 1 and 160 characters';
    }

    // Error check & insert to DB
    if (
        empty($errPostBody)
    ) {
        $dateTime = date('Y-m-d H:i:s');

        $postBody     = $con->real_escape_string($postBody);
        $userLoggedIn = $con->real_escape_string($userLoggedIn);
        $postedTo     = $con->real_escape_string($postedTo);
        $dateTime     = $con->real_escape_string($dateTime);
        $postId       = $con->real_escape_string($postId);

        $insertPostQuery = "INSERT INTO comments
                            VALUES (0, '$postBody', '$userLoggedIn', 
                                   '$postedTo', '$dateTime', '$postId')";

        $insertPost = $con->query($insertPostQuery);

        echo "
            <div class='p-2 alert alert-success text-center' role='alert'>
                The comment has been posted !
            </div>
        ";
    } else {
        echo "
            <div class='p-2 alert alert-danger text-center' role='alert'>"
                . strip_tags($errPostBody) .
            "</div>
        ";
    }
}

?>

<script>
(function($, window, document) {

    $(function() {
        $('.alert-success').fadeOut(5000);
    });

}(window.jQuery, window, document));
</script>

<?php

$userObj = new User($con, strip_tags($userLoggedIn));

// ------------------------------------------------------ Comment form template

echo "
    <form action='comments.php?post_id=". strip_tags($postId) ."'
          method='POST' 
          id='comment_form'
          class='border border-light p-2 mb-3'
          name='postComment". strip_tags($postId). "'>

        <div class='media'>
            <a class='pr-2' href='#'>
                <img class='mr-2 ml-2 avatar-sm rounded-circle'
                     src='".strip_tags($userObj->GetProfilePic())."'
                     alt='User placeholder image' 
                     height='31' />
            </a>

            <textarea class='form-control border-0 form-control-sm mr-2'
                   name='post_body'
                   placeholder='Add comment'
                   minlength='1'
                   maxlength='160'
                   rows='1'
                   required></textarea>

            <div class='media-body'>
                <button type='submit'
                        name='postComment".strip_tags($postId)."'
                        class='btn btn-outline-blue btn-sm ml-2 mr-2
                               waves-effect waves-light float-right'>
                    Send
                </button>
            </div>
        </div>
    </form>
";

// --------------------------------------------------------------------- Filter

function filter_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlentities($data);

    return $data;
}

?>