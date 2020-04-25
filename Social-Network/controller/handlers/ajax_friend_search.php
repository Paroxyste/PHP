<?php

require('../../config/config.php');
require('../../model/User.php');

// Filter query
$query = filter_data(
            filter_var($_POST['query'], FILTER_SANITIZE_STRING)
        );

if (
    !preg_match("/^[a-zA-Z0-9 -]+$/", $query)
) {
    return;
}

function filter_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = ucfirst(strtolower($data));

    return $data;
}
$status = 'no';
$userLoggedIn = strip_tags($_POST['userLoggedIn']);
$names = explode(' ', $query);

if (
    count($names) == 2
) {
    // Search by first name AND last name : John Doe
    $usersReturnedQuery = "SELECT *
                           FROM users
                           WHERE (first_name LIKE '%$names[0]%'
                           AND last_name LIKE '%$names[1]'
                           AND user_closed='$status')
                           LIMIT 8";

    $usersReturned = $con->query($usersReturnedQuery);
} else {
    // Search by first name OR last name : John or Doe
    $usersReturnedQuery = "SELECT *
                           FROM users
                           WHERE (first_name LIKE '%$names[0]%'
                           OR last_name LIKE '%$names[0]')
                           AND user_closed='$status'
                           LIMIT 8";

    $usersReturned = $con->query($usersReturnedQuery);
}

if (
    $query != NULL
) {
    while($row = $usersReturned->fetch_assoc()) {
        $user = new User($con, strip_tags($userLoggedIn));

        if (
            $user->isFriend($row['username'])
            && ($row['username'] != $userLoggedIn)
        ) {
            echo "
                <div class='row align-items-center p-2 mb-2' 
                     style='background-color: #f7f7f7;'>
                    <a href='messages.php?u=".strip_tags($row['username'])."'>
                        <div class='media'>
                            <img src='". strip_tags($row['profile_pic']) ."'
                                class='d-flex align-self-center mr-3 
                                        rounded-circle'
                                alt='User Pics'
                                height='64' />

                            <div class='media-body'>
                                <h4 class='mt-2 mb-2 font-16'>"
                                    . strip_tags($row['first_name']) .
                                    " " . strip_tags($row['last_name']) .
                                "</h4>

                                <p class='mb-1'>"
                                    . strip_tags($row['username']) .
                                "</p>
                            </div>
                        </div>
                    </a>
                </div>
            ";
        }
    }
}

?>
