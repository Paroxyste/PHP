<?php

declare(strict_types=1);

include('../../config/config.php');
include('../../model/User.php');

$query        = $_POST['query'];
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
                <a href='messages.php?u=" . $row['username'] . "'
                   style='outline: none;'>

                    <h3 class='heading'>
                        <img src='" . $row['profile_pic'] . "'
                             class='avatar' />

                        <span style='position: relative; top: -1.5vh'
                              class='text-primary ml-2'>"
                            . $row['first_name'] . " " . $row['last_name'] .

                            "<small class='text-muted'>
                                 - ". $row['username'] .
                            "</small>
                        </span>

                        <div class='text-left mt--3'
                             style='margin-left:3.9vw;'>
                            <small>" . $mutualFriends . "</small>
                        </div>
                    </h3>

                    <hr class='my-4' />
                </a>
            ";
        }
    }
}

?>
