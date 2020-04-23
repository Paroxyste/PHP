<?php

declare(strict_types=1);

require('./view/header.php');
require('./view/navbar.php');
require('./view/user_counter.php');

require('./controller/form_handlers/close_account_handler.php');

?>

<div class="row">


    <div class="col-lg-12 col-xl-12">
        <div class="card-box card-details">

            <h5 class="mb-3 text-uppercase bg-light p-2">
                <i class="ti-close mr-1"></i>
                Close Account
            </h5>

            <div class="row">
                <div class="col-12">
                    <div class="card">
                        <div class="alert alert-danger text-center" role="alert">

                            <strong>
                                Are tou sure you want to close your account ?
                            </strong>

                            <p class="mt-3">
                                Closing your account will hide your profile and
                                all your activity from other users.
                            </p>

                            <p>
                                You can re-open your account at any time by
                                simply logging in.
                            </p>
                        </div>
                    </div>

                    <div class="clearfix text-right">
                        <form action="close_account.php" method="POST">
                            <button class="btn btn-success" type="submit"
                                    name="cancel">
                                Cancel
                            </button>

                            <button class="btn btn-danger" type="submit"
                                    name="close_account">
                                Yes, Close it !
                            </button>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php

    require('./view/footer.php');

    ?>