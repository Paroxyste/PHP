<?php

include('./controller/handlers/friends_requests.php');

?>

<!-- Start profile card -->
<div class="card-box text-center card-details">
    <a href="upload.php">
        <img src="<?php echo strip_tags($user['profile_pic']); ?>"
             class="rounded-circle img-thumbnail mb-2"
             alt="profile-image" height="100px" width="100px"/>
    </a>

    <h4 class="mb-0">

        <?php

        echo
            strip_tags($user['first_name'])
            . ' ' .
            strip_tags($user['last_name']);

        ?>

    </h4>

    <p class="text-muted">

        <?php

        echo strip_tags($user['username']);

        ?>

    </p>

    <form action="<?php echo strip_tags($username); ?>" method="POST">

        <?php

        include('./controller/handlers/requests_btn.php');

        ?>

    </form>

    <div class="text-left mt-3">
        <h4 class="font-13 text-uppercase">
            About Me :
        </h4>

        <p class="text-muted font-13 mb-3">
            Lorem ipsum dolor sit amet, consectetur adipiscing elit.
            Sed luctus maximus dictum. Integer ac ex vel mauris mattis
            placerat. Pellentesque tellus libero, euismod a
            condimentum.
        </p>

        <?php

        if (
            $userLoggedInObj->isFriend($username)
        ) {
            echo "
                <p class='text-muted mb-2 font-13'>
                    <strong>
                        Mobile :
                    </strong>

                    <span class='ml-2'>
                        (+33) 1 02 03 04 05
                    </span>
                </p>

                <p class='text-muted mb-2 font-13'>
                    <strong>
                        Email :
                    </strong>

                    <span class='ml-2'>"
                        . strip_tags($user['email']) .
                    "</span>
                </p>

                <p class='text-muted mb-1 font-13'>
                    <strong>
                        Location :
                    </strong>

                    <span class='ml-2'>
                        FRANCE - FR
                    </span>
                </p>
            </div>
        ";
    } else {
        echo "</div>";
    }

    ?>

</div>