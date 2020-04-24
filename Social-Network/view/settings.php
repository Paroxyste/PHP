<?php

declare(strict_types=1);

require('./controller/form_handlers/settings_handler.php');

$userDetailsQuery = "SELECT first_name, last_name, email, username 
                     FROM users 
                     WHERE (username='$userLoggedIn')";

$userDetails = $con->query($userDetailsQuery);

$row = $userDetails->fetch_assoc();

$firstName = $row['first_name'];
$lastName  = $row['last_name'];
$email     = $row['email'];
$username  = $row['username'];

?>

<div class="row">
    <div class="col-lg-3 col-xl-3">

        <?php

        require('./view/dashboard/user_details.php');

        ?>

    </div>

    <div class="col-lg-9 col-xl-9">
        <div class="card-box card-details">

            <form action="settings.php" method="POST">

                <h5 class="mb-4 text-uppercase bg-light p-2">
                    <i class="ti-pencil-alt mr-1"></i>
                    User Details
                </h5>

                <div class="row">

                     <!-- Start username form -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="set_username">
                                Username
                            </label>

                            <input type="text" 
                                   class="form-control btn-light"
                                   id="set_username" 
                                   name="set_username"
                                   placeholder="<?php echo strip_tags($username); ?>"
                                   disabled 
                            />
                        </div>
                    </div>
                    <!-- End username form -->

                    <!-- Start email form -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="set_email">
                                Email Adress
                            </label>

                            <input type="email" 
                                   class="form-control"
                                   id="set_email" 
                                   name="set_email"
                                   minlength="10" 
                                   maxlength="100"
                                   placeholder="<?php echo strip_tags($email); ?>"
                                   required 
                            />

                            <p class="text-danger mt-1">
                                <?php 
                                    echo strip_tags($errEmail); 
                                ?>
                            </p>
                        </div>
                    </div>
                    <!-- End email form -->
                </div>

                <div class="row">

                    <!-- Start old password form -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="set_password">
                                Old Password
                            </label>

                            <input type="password" 
                                   class="form-control"
                                   id="set_password" 
                                   name="set_password"
                                   minlength="8" 
                                   placeholder="Enter password" />

                            <p class="text-danger mt-1">
                                <?php 
                                    echo strip_tags($errPassword); 
                                ?>
                            </p>
                        </div>
                    </div>
                    <!-- End old password form -->

                    <!-- Start new password form -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="set_newpassword">
                                New Password
                            </label>

                            <input type="password" 
                                   class="form-control"
                                   id="set_newpassword" 
                                   name="set_newpassword"
                                   minlength="8" 
                                   placeholder="Enter new password" 
                            />

                            <p class="text-danger mt-1">
                                <?php 
                                    echo strip_tags($errNewPassword); 
                                ?>
                            </p>

                        </div>
                    </div>
                    <!-- End new password form -->
                </div>

                <div class="text-right">
                    <button type="submit"
                            name="set_user_details"
                            class="btn btn-success mt-2 mb-3 waves-effect
                                   waves-light ">
                        Save
                    </button>
                </div>
            </form>

            <form action="settings.php" method="POST">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ti-user mr-1"></i>
                    Personnal Info
                </h5>

                <div class="row">

                    <!-- Start first name form -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="set_firstname">
                                First Name
                            </label>

                            <input type="text" 
                                   class="form-control"
                                   id="set_firstname" 
                                   name="set_firstname"
                                   minlength="2" 
                                   maxlength="20"
                                   placeholder="<?php echo strip_tags($firstName); ?>"
                                   required 
                            />

                            <p class="text-danger mt-1">
                                <?php 
                                    echo strip_tags($errFirstName); 
                                ?>
                            </p>
                        </div>
                    </div>
                    <!-- End first name form -->

                    <!-- Start last name form -->
                    <div class="col-md-6">
                        <div class="form-group">
                            <label for="set_lastname">
                                Last Name
                            </label>

                            <input type="text" 
                                   class="form-control"
                                   id="set_lastname" 
                                   name="set_lastname"
                                   minlength="2" 
                                   maxlength="20"
                                   placeholder="<?php echo strip_tags($lastName); ?>"
                                   required 
                            />

                            <p class="text-danger mt-1">
                                <?php 
                                    echo strip_tags($errLastName); 
                                ?>
                            </p>
                        </div>
                    </div>
                    <!-- End last name form -->
                </div>

                <div class="text-right">
                    <button type="submit"
                            name="set_perso_details"
                            class="btn btn-success mt-2 mb-3 waves-effect
                                   waves-light ">
                        Save
                    </button>
                </div>
            </form>

            <form action="close_account.php" method="POST">
                <h5 class="mb-3 text-uppercase bg-light p-2">
                    <i class="ti-lock mr-1"></i>
                    Close Account
                </h5>

                <div class="row">
                    <div class="col-12">
                        <button class="btn btn-block btn-danger waves-effect 
                                       waves-light">
                            I want to close my account ?
                        </button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
