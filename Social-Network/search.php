<?php

declare(strict_types=1);

include('./controller/form_handlers/search_handler.php');

?>

<div class='row'>
    <div class='col-12'>
        <div class='card-box'>
            <div class='row'>
                <div class='col-lg-8'>
                    <form class='form-inline'>

                        <input type='search' 
                               class='form-control'
                               name='user_search'
                               id='search_text_input'
                               placeholder='Search Friends ...' />

                        <div class='form-group mx-sm-3'>

                            <label for='status-select' class='mr-2'>
                                Searching By
                            </label>

                            <?php

                            if (
                                strip_tags($link_check) 
                                == strip_tags($link_username)
                            ) {
                                echo "
                                    <a class='btn btn-link mr-1' 
                                       href='".strip_tags($link_fullname)."'>
                                        Name
                                    </a>

                                    <a class='btn btn-outline-primary disabled'
                                       href='".strip_tags($link_username)."'>
                                        Username
                                    </a>
                                ";
                            }

                            if (
                                strip_tags($link_check)
                                == strip_tags($link_fullname)
                            ) {
                                echo "
                                    <a class='btn btn-outline-primary disabled 
                                              mr-1' 
                                       href='".strip_tags($link_fullname)."'>
                                        Name
                                    </a>

                                    <a class='btn btn-link' 
                                       href='".strip_tags($link_username)."'>
                                        Username
                                    </a>
                                ";
                            }
 
                            if (
                                strip_tags($link_check)
                                == strip_tags($link_base)
                            ) {
                                echo "
                                    <a class='btn btn-link mr-1' 
                                       href='".strip_tags($link_fullname)."'>
                                        Name
                                    </a>

                                    <a class='btn btn-link' 
                                       href='".strip_tags($link_username)."'>
                                        Username
                                    </a>
                                ";
                            }

                            ?>

                        </div>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php

// Check if query is empty
if (
    strip_tags($query) == ''
) {
    echo "
        <div class='row'>
            <div class='col-12'>
                <div class='alert alert-danger' role='alert'>
                    Please enter a name or username and try again.
                </div>
            </div>
        </div>
    ";
}

// Check if no results were found
elseif (
    $usersReturned->num_rows == 0
) {
    echo "
        <div class='row'>
            <div class='col-12'>
                <div class='alert alert-danger' role='alert'>" 
                    . $usersReturned->num_rows ."  Users Found
                </div>
            </div>
        </div>
    ";
}

// Check if results were found
elseif (
    $usersReturned->num_rows > 0
) {
    echo "
        <div class='alert alert-success mb-3' role='alert'>" 
            . $usersReturned->num_rows ."  Users Found
        </div>

        <div class='row'>

        ";

        while ($row = $usersReturned->fetch_assoc()) {
            $numFriends = substr_count($user['friend_array'], ',') - 1;

            echo"
                <div class='col-lg-3'>
                    <div class='text-center card-box'>
                        <div class='pt-2 pb-2'>
                            <img class='rounded-circle img-thumbnail avatar-xl'
                                src='".strip_tags($row['profile_pic'])."'
                                alt='profile-image' />

                            <h4 class='mt-3'>
                                <a href='".strip_tags($row['username'])."'
                                class='text-dark'>"
                                    . strip_tags($row['first_name']) .
                                    ' ' . strip_tags($row['last_name']) ."
                                </a>
                            </h4>

                            <p class='text-muted'>"
                                . strip_tags($row['username']) .
                            "</p>

                            <a class='btn btn-primary btn-sm waves-effect 
                                      mt-2 mb-1'
                               href='".strip_tags($row['username'])."'>
                                Show Profile
                            </a>

                            <div class='row mt-3'>
                                <div class='col-4'>
                                    <h4>"
                                        . strip_tags($row['num_posts']) .
                                    "</h4>

                                    <p class='mb-0 text-muted text-truncate'>
                                        Posts
                                    </p>
                                </div>

                                <div class='col-4'>
                                    <h4>"
                                        . strip_tags($row['num_likes']) .
                                    "</h4>

                                    <p class='mb-0 text-muted text-truncate'>
                                        Likes
                                    </p>
                                </div>

                                <div class='col-4'>
                                    <h4>"
                                        . $numFriends .
                                    "</h4>

                                    <p class='mb-0 text-muted text-truncate'>
                                        Friends
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            ";
        }

    echo '</div>';

}

?>

</body>
</html>