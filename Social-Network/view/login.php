<?php

require('./controller/form_handlers/login_handler.php');

?>

<!DOCTYPE html>
<html lang="en">

    <head>

        <!-- Metadata -->
        <meta charset="utf-8" />

        <meta content="width=device-width, initial-scale=1.0, 
                       maximum-scale=1.0, user-scalable=0, 
                       shrink-to-fit=no" />

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

        <?php echo $loginMsg; ?>

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

                                    <p class="text-muted mb-4">
                                        Enter your email address and password 
                                        to login.
                                    </p>
                                </div>

                                <form role="form" 
                                      action="login.php" 
                                      method="POST">

                                    <!-- Start email form -->
                                    <div class="form-group mb-3">
                                        <label for="email">
                                            Email address
                                        </label>

                                        <input class="form-control" 
                                               type="email"
                                               id="email" 
                                               name="email"
                                               placeholder="Enter your email"
                                               minlength="10" 
                                               maxlength="100"
                                               value="
                                                <?php
                                                    if (
                                                        isset($_SESSION['email'])
                                                    ) {
                                                        echo strip_tags($_SESSION['email']);
                                                    }
                                                ?>"
                                               required
                                        />

                                        <p class="text-danger mt-1">
                                            <?php 
                                                echo strip_tags($errEmail); 
                                            ?>
                                        </p>
                                    </div>
                                    <!-- End email form -->

                                    <!-- Start password form -->
                                    <div class="form-group mb-3">
                                        <label for="password">
                                            Password
                                        </label>

                                        <input class="form-control" 
                                               type="password"
                                               id="password" 
                                               name="password" 
                                               placeholder="Enter your password"
                                               minlength="8"
                                               required 
                                        />

                                        <p class="text-danger mt-1">
                                            <?php 
                                                echo strip_tags($errPassword); 
                                            ?>
                                        </p>
                                    </div>
                                    <!-- End password form -->

                                    <!-- Start remember me form -->
                                    <div class="form-group mb-3">
                                        <div class="custom-control 
                                                    custom-checkbox">

                                            <input type="checkbox" 
                                                   class="custom-control-input"
                                                   id="checkbox-signin"
                                                   name="" 
                                            />

                                            <label class="custom-control-label"
                                                   for="checkbox-signin">
                                                Remember me
                                            </label>
                                        </div>
                                    </div>
                                    <!-- End remember me form -->

                                    <div class="form-group mb-0 text-center">
                                        <button class="btn btn-primary 
                                                       btn-block"
                                                type="submit"
                                                name="log_btn">
                                            Log In
                                        </button>
                                    </div>
                                </form>

                                <div class="row mt-3">
                                    <div class="col-12 text-center">
                                        <p>
                                            <a href="recoverpw.php"
                                               class="text-50">
                                                Forgot your password ?
                                            </a>
                                        </p>
                                    </div>
                                </div>
                            </div>
                        </div>

                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <p class="text-white-50">
                                    Don't have an account ?

                                    <a href="register.php"
                                       class="text-white ml-1">
                                        <b>
                                            Sign Up
                                        </b>
                                    </a>
                                </p>
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
