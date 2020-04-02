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

            if (
                $userLoggedIn != $username
            ) {
                echo "<div class='tab-content'>";

                require('./view/dashboard/timeline.php');
            } else {
                echo "
                    <ul class='nav nav-pills navtab-bg nav-justified'>
                        <li class='nav-item'>
                            <a href='#aboutme' data-toggle='tab'
                               aria-expanded='false' class='nav-link'>
                                About Me
                            </a>
                        </li>

                        <li class='nav-item'>
                            <a href='#timeline' data-toggle='tab'
                               aria-expanded='true' class='nav-link active'>
                                Timeline
                            </a>
                        </li>

                        <li class='nav-item'>
                            <a href='#settings' data-toggle='tab'
                               aria-expanded='false' class='nav-link'>
                                Settings
                            </a>
                        </li>
                    </ul>

                    <div class='tab-content'>
                ";

                require('./view/dashboard/about_me.php');
                require('./view/dashboard/timeline.php');
                require('./view/dashboard/settings.php');

            }

            ?>

            </div>
        </div>
    </div>
</div>
</div>
</div>
</div>
