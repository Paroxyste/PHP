<?php

require('./controller/form_handlers/logout_handler.php');

?>

<!DOCTYPE html>
<html lang="en">

    <head>
        <!-- Metadata -->
        <meta charset="utf-8" />

        <meta name="viewport"
            content="width=device-width, initial-scale=1.0" />

        <meta http-equiv="X-UA-Compatible"
            content="IE=edge" />

        <!-- Favicon -->
        <link href="./view/images/favicon.ico"
            rel="shortcut icon" />

        <!-- CSS Link -->
        <link href="./view/css/app.min.css"
            rel="stylesheet" type="text/css" />

        <link href="./view/css/bootstrap.min.css"
            rel="stylesheet" type="text/css" />

        <link href="./view/css/style.min.css"
            rel="stylesheet" type="text/css" />

        <link href="./view/css/themify-icons.min.css"
            rel="stylesheet" type="text/css" />

        <title>My Social Network</title>
    </head>

    <body style="background: linear-gradient(to top, #514a9d, #e4e5e6);">
        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card">
                            <div class="card-body p-4">
                                <div class="text-center w-75 m-auto">
                                    <a href="index.php"
                                       class="logo text-center">
                    
                                       <span class="logo-lg-text-dark">
                                            My Social Network
                                        </span>
                                    </a>

                                    <p class="text-muted">
                                        You are now successfully sign out ! 
                                        You will be automatically redirected ...
                                    </p>
                                </div>

                                <div class="text-center">
                                    <div class="mt-3 mb-3">
                                        <div class="logout-checkmark">
                                            <svg version="1.1"
                                                 xmlns="http://www.w3.org/2000/svg"
                                                 viewBox="0 0 130.2 130.2">

                                                <circle class="path circle"
                                                        fill="none"
                                                        stroke="#4bd396"
                                                        stroke-width="3"
                                                        stroke-miterlimit="10"
                                                        cx="65.1" 
                                                        cy="65.1" 
                                                        r="62.1"
                                                />

                                                <polyline class="path check"
                                                        fill="none"
                                                        stroke="#4bd396"
                                                        stroke-width="3"
                                                        stroke-linecap="round"
                                                        stroke-miterlimit="10"
                                                        points="100.2,40.2 51.5,
                                                                88.8 29.8,67.5" 
                                                />
                                            </svg>
                                        </div>
                                    </div>

                                    <h3>
                                        See you again !
                                    </h3>

                                    </div>
                                </div>
                            </div>

                            <div class="row mt-3">
                                <div class="col-12 text-center">
                                    <p class="text-white-50">
                                        Back to
                                        <a href="login.php"
                                           class="text-white ml-1">
                                            <b>
                                                Sign In
                                            </b>
                                        </a>
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <footer class="footer footer-alt">
            <span class="text-white">
                &copy; 2019 -

                <a href="#" class="text-white-50">
                    My Social Network
                </a> by

                <a href="https://laurent-dev.fr"
                   class="text-white-50">
                    Laurent Echeverria
                </a>
            </span>
        </footer>
    </body>
</html>
