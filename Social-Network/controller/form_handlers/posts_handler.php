<?php

declare(strict_types=1);

if (
    isset($_POST['post_btn'])
) {
    $uploadOk = 1;
    $imageName = $_FILES['fileToUpload']['name'];
    $uploadMsg = '';

    if (
        $imageName != ''
    ) {
        $targetDir     = 'view/images/attached-files/';
        $imageName     = $targetDir . uniqid() . basename($imageName);
        $imageFileType = pathinfo($imageName, PATHINFO_EXTENSION);

        if (
            $_FILES['fileToUpload']['size'] > 3000000
        ) {
            $uploadMsg = "
                <div class='col-md-8'>
                    <div class='alert alert-danger alert-dismissible
                                fade show mb--2'
                         role='alert'>

                        <span class='alert-inner--icon'>
                            <i class='fas fa-exclamation-triangle mr-2'></i>
                        </span>

                        <span class='alert-inner--text'>
                            <strong>
                                Error :
                            </strong>
                            Your file is too large ! (max: 3MB)
                        </span>

                        <button type='button' class='close'
                                data-dismiss='alert' aria-label='Close'>

                            <span aria-hidden='true'>
                                &times;
                            </span>

                        </button>
                    </div>
                </div>
            ";

            $uploadOk = 0;
        }

        if (
            strtolower($imageFileType) != 'jpeg'
            && strtolower($imageFileType) != 'jpg'
            && strtolower($imageFileType) != 'png'
            && strtolower($imageFileType) != 'gif'
        ) {
            $uploadMsg = "
                <div class='col-md-8'>
                    <div class='alert alert-danger alert-dismissible
                                fade show mb--2'
                         role='alert'>

                        <span class='alert-inner--icon'>
                            <i class='fas fa-exclamation-triangle mr-2'></i>
                        </span>

                        <span class='alert-inner--text'>
                            <strong>
                                Error :
                            </strong>
                            Only jpeg, jpg, png or gif files are allowed !
                        </span>

                        <button type='button' class='close'
                                data-dismiss='alert' aria-label='Close'>

                            <span aria-hidden='true'>
                                &times;
                            </span>

                        </button>
                    </div>
                </div>
            ";

            $uploadOk = 0;
        }

        if (
            $uploadOk
        ) {
            if (
                move_uploaded_file($_FILES['fileToUpload']['tmp_name'],
                                   $imageName)
            ) {
                $uploadMsg = "
                    <div class='col-md-8'>
                        <div class='alert alert-success alert-dismissible
                                    fade show mb--2'
                             role='alert'>

                            <span class='alert-inner--icon'>
                                <i class='fas fa-check mr-2'></i>
                            </span>

                            <span class='alert-inner--text'>
                                <strong>
                                    Succes :
                                </strong>
                                Image uploaded successfully !
                            </span>

                            <button type='button' class='close'
                                    data-dismiss='alert' aria-label='Close'>

                                <span aria-hidden='true'>
                                    &times;
                                </span>

                            </button>
                        </div>
                    </div>
                ";
            } else {
                $uploadOk = 0;
            }
        }
    }

    if (
        $uploadOk
    ) {
        $post = new POST($con, $_POST['posted_by']);

        if (
            $_POST['posted_to'] == $_POST['posted_by']
        ) {
            // If user is on own profile, user = 'none'
            $post->SubmitPost($_POST['post_text'], 'none', $imageName);
        } else {
            // Else user = '$_POST['posted_to']'
            $post->SubmitPost($_POST['post_text'],
                              $_POST['posted_to'],
                              $imageName);
        }

        echo "
            <script>
                location.href='". strip_tags($username) ."';
            </script>
        ";
    }
} else {
    $uploadMsg = '';
}

?>
