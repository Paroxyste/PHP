<?php

declare(strict_types=1);

$profileId = $user['username'];
$statusMsg = '';
$imgSrc    = '';

// Step 1 : Upload image to server
if (
    isset($_FILES['image']['name'])
) {
    if (
        $_FILES['image']['type'] != 'jpg'
        || $_FILES['image']['type'] != 'jpeg'
        || $_FILES['image']['type'] != 'JPG'
        || $_FILES['image']['type'] != 'JPEG'
        || $_FILES['image']['type'] != 'gif'
        || $_FILES['image']['type'] != 'GIF'
        || $_FILES['image']['type'] != 'png'
        || $_FILES['image']['type'] != 'PNG'
    ) {
        $statusMsg    = "
            <div></div>
        ";
    }

    // Get temp name | File Extension
    $imageTempName = $_FILES['image']['tmp_name'];
    $imageType = explode('/', $_FILES['image']['type']);
    $type = $imageType[1];

    // Set upload directory
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/view/images/users';

    // Set file name
    $fileName = $profileId . '.' . $type;
    $fullPath = $uploadDir . '/' . $fileName;

    // Move the file to correct location
    $move = move_uploaded_file($imageTempName, $fullPath);
    chmod($fullPath, 0777);

    // Check for valid upload
    if (
        !$move
    ) {
        die ('File didnt upload !');
    } else {
        $imgSrc = 'view/images/users/' . $fileName;

        $statusMsg= "
            <div class='alert alert-success mt-2 mb-4' role='alert'>
                <span class='text-lead'>
                    <strong>
                        Success !
                    </strong>
                        Upload Complete.
                </span>
            </div>
        ";
    }

    // Get image size
    $originalSize = getimagesize($fullPath);

    $imgWidth  = $originalSize[0];
    $imgHeight = $originalSize[1];

    // Specify the new size
    $newWidth  = 480;
    $newHeight = intval($imgHeight / ($imgWidth / $newWidth));

    // Create new image using correct PHP func
    if (
        $_FILES['image']['type'] == 'image/gif'
        || $_FILES['image']['type'] == 'image/GIF'
    ) {
        $src = imagecreatefromgif($fullPath);
    }

    if (
        $_FILES['image']['type'] == 'image/jpeg'
        || $_FILES['image']['type'] == 'image/jpg'
        || $_FILES['image']['type'] == 'image/JPEG'
        || $_FILES['image']['type'] == 'image/JPG'
    ) {
        $src = imagecreatefromjpeg($fullPath);
    }

    if (
        $_FILES['image']['type'] == 'image/png'
        || $_FILES['image']['type'] == 'image/PNG'
    ) {
        $src = imagecreatefrompng($fullPath);
    }

    // Create the new resized image
    $resizedImg = imagecreatetruecolor($newWidth, $newHeight);

    imagecopyresampled($resizedImg, $src, 0, 0, 0, 0,
                       $newWidth, $newHeight,
                       $imgWidth, $imgHeight);

    // Upload new version
    imagejpeg($resizedImg, $fullPath, 100);
    chmod($fullPath, 0777);

    // Free up memory
    imagedestroy($src);
    imagedestroy($resizedImg);
}

/*----------------------------------------------------------------------------*/

// Step 2 : Cropping & converting the image to JPG
if (
    isset($_POST['x'])
) {
    $type = $_POST['type'];

    // Set upload directory
    $uploadDir = $_SERVER['DOCUMENT_ROOT'] . '/view/images/users';

    // Set file name
    $fileName = $profileId . '.' . $type;
    $currPath = $uploadDir . '/' . $fileName;

    // The target dimensions 200x200 + Quality
    $cropWidth = $cropHeight = 200;
    $jpegQuality = 100;

    if (
        $type == 'jpg'
        || $type == 'jpeg'
        || $type == 'JPG'
        || $type == 'JPEG'
    ) {
        $src2 = imagecreatefromjpeg($currPath);
    }

    if (
        $type == 'png'
        || $type == 'PNG'
    ) {
        $src2 = imagecreatefrompng($currPath);
    }

    if (
        $type == 'gif'
        || $type == 'GIF'
    ) {
        $src2 = imagecreatefromgif($currPath);
    }

    // Retrieves the value of the position and converts into int
    $x = intval($_POST['x']);
    $y = intval($_POST['y']);

    $w = intval($_POST['w']);
    $h = intval($_POST['h']);

    // Create the new cropped image
    $cropImg = imagecreatetruecolor($cropWidth, $cropHeight);

    imagecopyresampled($cropImg, $src2, 0, 0, $x, $y,
                       $cropWidth, $cropHeight, $w, $h);

    // Upload new version
    imagejpeg($cropImg, $currPath, 100);

    // Rename the file with a hash
    $hashName = bin2hex(random_bytes(50)) . '.jpeg';
    $newPath = $uploadDir . '/' . $hashName;

    rename($currPath, $newPath);

    // Free up memory
    imagedestroy($src2);
    imagedestroy($cropImg);

    // Step 2 : Cropping & converting the image to JPG
    $resultPath = './view/images/users/' . $hashName;

    $resultPath = $con->real_escape_string($resultPath);

    $insertPicQuery = "UPDATE users
                       SET profile_pic='$resultPath'
                       WHERE (username='$userLoggedIn')";

    $insertPic = $con->query($insertPicQuery);

    echo "
        <script>
            location.href='". strip_tags($_SESSION['username']) ."';
        </script>
    ";
}

?>