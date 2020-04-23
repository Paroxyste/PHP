<?php

declare(strict_types=1);

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
    <div class="col-lg-3 col-xl-3">

        <?php

        require('./view/dashboard/user_details.php');

        ?>

    </div>

    <?php

    if (
        $userLoggedIn != $username
    ) {
        echo "<div class='col-lg-9 col-xl-9'>";
    } else {
        echo "<div class='col-lg-6 col-xl-6'>";
    }
    
    ?>
        <div class="tab-content">
            <div class="card-box">

                <?php

                require('./view/dashboard/timeline.php');

                ?>

            </div>
        </div>
    </div>

    <div class="col-lg-3 col-xl-3">

        <?php

        if (
            $userLoggedIn != $username
        ) {
            echo '</div>';
        } else {
            require('./view/dashboard/message_box.php');
        }

        ?>
    </div>

</div>
</div>
</div>
</div>