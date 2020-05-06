<?php

require('./controller/form_handlers/register_handler.php');

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

        <?php echo $successQuery; ?>

        <div class="account-pages mt-5 mb-5">
            <div class="container">
                <div class="row justify-content-center">
                    <div class="col-md-8 col-lg-6 col-xl-5">
                        <div class="card bg-pattern">
                            <div class="card-body p-4">
                                <div class="text-center w-75 m-auto">
                                    <a href="index.html"
                                    class="logo text-center">
                                        <span class="logo-lg-text-dark">
                                            My Social Network
                                        </span>
                                    </a>

                                    <p class="text-muted mb-4">
                                        Don't have an account ? Create your 
                                        account, it takes less than a minute 
                                        and it's free !
                                    </p>
                                </div>

                                <form role="form" 
                                       action="register.php" 
                                       method="POST">

                                    <!-- Start first_name form -->
                                    <div class="form-group mb-3">
                                        <label for="first_name">
                                            First Name
                                        </label>

                                        <input class="form-control" 
                                               type="text"
                                               id="first_name" 
                                               name="first_name" 
                                               placeholder="Enter your first name"
                                               minlength="2" 
                                               maxlength="20"
                                               required 
                                        />

                                        <p class="text-danger mt-1">
                                            <?php 
                                                echo strip_tags($errFirstName);
                                            ?>
                                        </p>
                                    </div>
                                    <!-- End first_name form -->

                                    <!-- Start last_name field -->
                                    <div class="form-group mb-3">
                                        <label for="last_name">
                                            Last Name
                                        </label>

                                        <input class="form-control" 
                                               type="text"
                                               id="last_name" 
                                               name="last_name" 
                                               placeholder="Enter your last name"
                                               minlength="2" 
                                               maxlength="20" 
                                               required 
                                        />

                                        <p class="text-danger mt-1">
                                            <?php 
                                                echo strip_tags($errLastName);
                                            ?>
                                        </p>
                                    </div>
                                    <!-- End last_name form -->


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

                                    <div class="form-group mb-0 text-center">
                                        <button class="btn btn-success 
                                                       btn-block"
                                                type="submit" 
                                                name="reg_btn">
                                            Sign Up
                                        </button>
                                    </div>
                                </form>
                            </div>
                        </div>


                        <div class="row mt-3">
                            <div class="col-12 text-center">
                                <p class="text-white-50">
                                    Already have account ?

                                    <a href="login.php"
                                    class="text-white ml-1">
                                        <b>
                                            Log In
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

                <a href="#"
                   class="text-white-50">
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