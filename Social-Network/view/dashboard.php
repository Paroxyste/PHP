<?php

if (
    isset($_GET['profile_username'])
) {
    $username = strip_tags($_GET['profile_username']);

    $userDataQuery = "SELECT *
                      FROM users
                      WHERE (username='$username')";

    $userData = $con->query($userDataQuery);

    $usernameCheck = $userData->num_rows;

    if (
        $usernameCheck == NULL
    ) {
        echo "
            <script>
                location.href='". strip_tags($userLoggedIn) ."'
            </script>
        ";
    } else {
        $user = $userData->fetch_assoc();
    }
} else {
    echo "
        <script>
            location.href='". strip_tags($userLoggedIn) ."'
        </script>
    ";
}

?>

<div class="row">
    <div class="col-lg-3 col-xl-3">

        <?php

        include('./view/dashboard/user_details.php');

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

                include('./view/dashboard/timeline.php');

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
            include('./view/dashboard/message_box.php');
        }

        ?>
    </div>

</div>
</div>
</div>
</div>