<?php

require('../../config/config.php');
require('../../model/User.php');

// Filter query
$query = filter_data(
            filter_var($_POST['query'], FILTER_SANITIZE_STRING)
         );

if (
    !preg_match("/^[a-zA-Z0-9 -_]+$/", $query)
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

$status       = 'no';
$userLoggedIn = strip_tags($_POST['userLoggedIn']);
$fullName     = explode(' ', $query);

// Search by username : john_doe
if (
    strpos($query, '_') !== FALSE
) {
    $usersReturnedQuery = "SELECT first_name, last_name, username, profile_pic
                           FROM users 
                           WHERE (username LIKE '$query%' 
                           AND user_closed='$status')
                           LIMIT 5";

    $usersReturned = $con->query($usersReturnedQuery);

} 

// Search by first name and last name : John Doe
elseif (
    count($fullName) == 2
) {
    $usersReturnedQuery = "SELECT first_name, last_name, username, profile_pic 
                           FROM users 
                           WHERE (first_name LIKE '$fullName[0]%' 
                           AND last_name LIKE '%$fullName[1]%' 
                           AND user_closed='$status')
                           LIMIT 5";

    $usersReturned = $con->query($usersReturnedQuery);
}

// Search by first name or last_name : John or Doe
else {
    $usersReturnedQuery = "SELECT first_name, last_name, username, profile_pic 
                           FROM users 
                           WHERE (first_name LIKE '$fullName[0]%' 
                           OR last_name LIKE '%$fullName[0]%') 
                           AND user_closed='$status' 
                           LIMIT 5";

    $usersReturned = $con->query($usersReturnedQuery);
}

if (
    strip_tags($query) != NULL
) {
    echo "
        <div class='card-box shadow p-3 mb-0 bg-white rounded'>
            <h5 class='header-title m-0 mb-3' style='margin-top: -7px;'>"
                . strip_tags($usersReturned->num_rows) . " Users Founds
            </h5>
        ";

        while ($row = $usersReturned->fetch_assoc()) {
            echo "
                <div class='row align-items-center p-2 mb-2' 
                    style='background-color: #f7f7f7;'>
                    <a href='". strip_tags($row['username']) ."'>
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

        if (
            $usersReturned->num_rows > 0
        ) {
            echo "
                <div class='text-center mt-3'>
                    <a href='search.php?user_search=". strip_tags($query) ."'>
                        <span class='text-primary'>
                            Show more users
                        </span>
                    </a>
                </div>
            ";
        } else {
            echo "
                <div class='text-center mt-3'>
                    <a href='search.php?user_search=". strip_tags($query) ."'>
                        <span class='text-primary'>
                            No users founds
                        </span>
                    </a>
                </div>
            ";
        }
    }

    ?>

</div>