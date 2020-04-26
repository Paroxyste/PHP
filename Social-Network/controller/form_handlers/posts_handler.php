<?php

if (
    isset($_POST['post_btn'])
) {
    $uploadOk  = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $uploadMsg = NULL;

    // ----------------------------------------------------------- Check upload

    if (
        $imageName != NULL
    ) {
        $targetDir     = 'view/images/attached-files/';
        $imageName     = $targetDir . uniqid() . basename($imageName);
        $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

        // --------------------------------------------------- Error image size

        if (
            $_FILES['fileToUpload']['size'] > 3000000
        ) {
            $uploadMsg = "
                <div class='alert alert-danger alert-dismissible
                            fade show mb--2'
                     role='alert'>

                    <span class='alert-inner--text'>
                        <strong>
                            Error :
                        </strong>
                        Your file is too large ! (max: 3MB)
                    </span>

                    <button type='button' 
                            class='close'
                            data-dismiss='alert' 
                            aria-label='Close'>

                        <span aria-hidden='true'>
                            &times;
                        </span>

                    </button>
                </div>
            ";

            $uploadOk = 0;
        }

        // ----------------------------------------------- Error picture format

        if (
            strtolower($imageFileType) != 'jpeg'
            && strtolower($imageFileType) != 'jpg'
            && strtolower($imageFileType) != 'png'
            && strtolower($imageFileType) != 'gif'
        ) {
            $uploadMsg = "
                <div class='alert alert-danger alert-dismissible
                            fade show mb--2'
                     role='alert'>

                    <span class='alert-inner--text'>
                        <strong>
                            Error :
                        </strong>
                        Only jpeg, jpg, png or gif files are allowed !
                    </span>

                    <button type='button' 
                            class='close'
                            data-dismiss='alert' 
                            aria-label='Close'>

                        <span aria-hidden='true'>
                            &times;
                        </span>

                    </button>
                </div>
            ";

            $uploadOk = 0;
        }

        // ----------------------------------------------------- Success upload

        if (
            empty($uploadMsg)
        ) {
            move_uploaded_file($_FILES['fileToUpload']['tmp_name'], $imageName);
        }
    }

    // ------------------------------------------------------------- Check post

    $userNone = 'none';
    $postedTo = strip_tags($_POST['posted_to']);
    $postedBy = strip_tags($_POST['posted_by']);

    // -------------------------------------------------------- Filter postText

    $postText = filter_data(
                    filter_var($_POST['post_text'], FILTER_SANITIZE_STRING)
                );

    // ------------------------------------------------------------ Empty field
    if (
        empty($postText)
    ) {
        $uploadMsg = "
            <div class='alert alert-danger alert-dismissible
                        fade show mb--2'
                 role='alert'>

                <span class='alert-inner--text'>
                    <strong>
                        Error :
                    </strong>
                    This field is required !
                </span>

                <button type='button' 
                        class='close'
                        data-dismiss='alert' 
                        aria-label='Close'>

                    <span aria-hidden='true'>
                        &times;
                    </span>

                </button>
            </div>
        ";

        $uploadOk = 0;
    }

    // ------------------------------------------------------------ Limit filed

    if (
        strlen($postText) < 1
        || strlen($postText) > 160
    ) {
        $uploadMsg = "
            <div class='alert alert-danger alert-dismissible
                        fade show mb--2'
                 role='alert'>

                <span class='alert-inner--text'>
                    <strong>
                        Error :
                    </strong>
                    Your comment must be between 1 and 160 characters !
                </span>

                <button type='button' 
                        class='close'
                        data-dismiss='alert' 
                        aria-label='Close'>

                    <span aria-hidden='true'>
                        &times;
                    </span>

                </button>
            </div>
        ";

        $uploadOk = 0;
    }

    // ------------------------------------------------------------ Error check

    if (
        empty($uploadMsg)
        && $uploadOk
    ) {
        $post = new POST($con, strip_tags($postedBy));

        $postText  = $con->real_escape_string($postText);
        $postedTo  = $con->real_escape_string($postedTo);
        $userNone  = $con->real_escape_string($userNone);
        $imageName = $con->real_escape_string($imageName);

        // ---------------------------------------------------------- Add to DB

        if (
            $postedTo == $postedBy
        ) {
            $post->SubmitPost($postText, $userNone, $imageName);
        } else {
            $post->SubmitPost($postText, $postedTo, $imageName);
        }

        echo "
            <script>
                location.href='". strip_tags($username) ."';
            </script>
        ";
    }
} else {
    $uploadMsg = NULL;
}

// ---------------------------------------------------------------- Data filter

function filter_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlentities($data);

    return $data;
}

?>
