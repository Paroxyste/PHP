<?php

require('./config/config.php');
require('./model/Message.php');
require('./model/Post.php');
require('./model/User.php');

if (
    isset($_SESSION['username'])
) {
    // Get data for userLoggedIn
    $userLoggedIn = $_SESSION['username'];

    $sessionDataQuery = "SELECT *
                         FROM users
                         WHERE (username='$userLoggedIn')";

    $sessionData = $con->query($sessionDataQuery);

    $user = $sessionData->fetch_assoc();

    // Get data for other profile
    $username = isset($_GET['profile_username']);

    $userDataQuery = "SELECT *
                      FROM users
                      WHERE (username='$username')";

    $userData = $con->query($userDataQuery);

    $data = $sessionData->fetch_assoc();
} else {
    echo "
        <script>
            location.href='login.php';
        </script>
    ";
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
<link href="./view/images/favicon.ico"
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

<body style="background: linear-gradient(to bottom, #514a9d, #e4e5e6);">