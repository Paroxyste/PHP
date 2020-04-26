<?php

declare(strict_types=1);

require('./config/config.php');
require('./model/Post.php');
require('./model/User.php');

if (
    isset($_SESSION['username'])
) {
    $userLoggedIn = $_SESSION['username'];

    $userDetailsQuery = "SELECT *
                         FROM users
                         WHERE (username='$userLoggedIn')";

    $userDetails = $con->query($userDetailsQuery);

    $user = $userDetails->fetch_assoc();
} else {
    header('Location: login.php');
}

?>

<!DOCTYPE html>

<html lang="en">

<head>

<!-- Metadata -->
<meta charset="utf-8" />

<meta name="viewport"
      content="width=device-width, initial-scale=1.0, maximum-scale=1.0,
                     user-scalable=0, shrink-to-fit=no" />

<meta http-equiv="X-UA-Compatible"
      content="IE=edge" />

<!-- Favicon -->
<link href="./images/favicon.ico"
      rel="shortcut icon" />

<!-- CSS Link -->
<link href="./view/css/app.min.css"
      rel="stylesheet"
      type="text/css" />

<link href="./view/css/bootstrap.min.css"
      rel="stylesheet"
      type="text/css" />

<link href="./view/css/jcrop.min.css"
      rel="stylesheet"
      type="text/css" />

<link href="./view/css/style.min.css"
      rel="stylesheet"
      type="text/css" />

<link href="./view/css/themify-icons.min.css"
      rel="stylesheet"
      type="text/css" />

<!-- Jquery Link -->
<script src="./view/js/jquery.min.js"
        type="text/javascript"></script>

<title>My Social Network</title>

</head>

<body class='comments-block'>

<?php

include('./controller/form_handlers/comments_handler.php');
include('./controller/handlers/load_comments.php');

?>

</body>
</html>