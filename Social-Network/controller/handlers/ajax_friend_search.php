<?php

declare(strict_types=1);

include('../../config/config.php');
include('../../model/User.php');

// Filter query
$query = filter_data(
    filter_var($_POST['query'], FILTER_SANITIZE_STRING)
 );

$query = upper_lower($query);

if (
!preg_match("/^[a-zA-Z0-9 -_]+$/", $query)
) {
return;
}

function filter_data($data) {
$data = trim($data);
$data = stripslashes($data);
$data = htmlspecialchars($data);

return $data;
}

function upper_lower($data) {
$data = ucfirst(strtolower($data));

return $data;
}

$userLoggedIn = $_POST['userLoggedIn'];
$names = explode(' ', $query);

if (
    strpos($query, '_') !== FALSE
) {

    // Search by username : john_doe
    $usersReturnedQuery = "SELECT *
                           FROM users
                           WHERE (username LIKE '$query%'
                           AND user_closed='no')
                           LIMIT 8";

    $usersReturned = $con->query($usersReturnedQuery);

}

elseif (
    count($names) == 2
) {
    // Search by first name and last name : John Doe
    $usersReturnedQuery = "SELECT *
                           FROM users
                           WHERE (first_name LIKE '%$names[0]%'
                           AND last_name LIKE '%$names[1]'
                           AND user_closed='no')
                           LIMIT 8";

    $usersReturned = $con->query($usersReturnedQuery);
} else {
    // Search by first name or last_name : John or Doe
    $usersReturnedQuery = "SELECT *
                           FROM users
                           WHERE (first_name LIKE '%$names[0]%'
                           OR last_name LIKE '%$names[0]')
                           AND user_closed='no'
                           LIMIT 8";

    $usersReturned = $con->query($usersReturnedQuery);
}

if (
    $query != ''
) {
    while($row = mysqli_fetch_array($usersReturned)) {
        $user = new User($con, $userLoggedIn);

        if (
            $row['username'] != $userLoggedIn
        ) {
            $mutualFriends = $user->GetMutualFriends($row['username']) .
                             ' friends in common';
        } else {
            $mutual_friends = '';
        }

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
