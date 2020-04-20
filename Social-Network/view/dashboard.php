<?php

if (
    isset($_GET['profile_username'])
) {
    $username = $_GET['profile_username'];

    $username = $con->real_escape_string($username);

    $userDataQuery = "SELECT *
                      FROM users
                      WHERE (username='$username')";

    $userData = $con->query($userDataQuery);

    $user = $userData->fetch_assoc();
}

?>

<div class="row">
    <div class="col-lg-4 col-xl-4">

        <?php

        if (
            $userLoggedIn != $username
        ) {
            require('./view/dashboard/user_details.php');
        } else {
            require('./view/dashboard/user_details.php');
            require('./view/dashboard/message_box.php');
        }

        ?>

    </div>

    <div class="col-lg-8 col-xl-8">
        <div class="tab-content">
            <div class="card-box">

                <?php

                require('./view/dashboard/timeline.php');

                ?>

            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
