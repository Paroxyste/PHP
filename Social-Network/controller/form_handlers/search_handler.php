<?php

// --------------------------------------------------------------- Query result

if (
    isset($_GET['user_search'])
) {
    $query = $_GET['user_search'];
} else {
    $query = NULL;
}

// ------------------------------------------------------- Query result by type

if (
    isset($_GET['type'])
) {
    $type = $_GET['type'];
} else {
    $type = 'name';
}

$status   = 'no';
$username = 'username';

$linkCheck =  "{$_SERVER['REQUEST_URI']}";
$linkBase  = '/search.php?user_search=' . strip_tags($query);

$userType = '&type=username';
$nameType = '&type=name';

$linkUserName = strip_tags($linkBase) . strip_tags($userType);
$linkFullName = strip_tags($linkBase) . strip_tags($nameType);

if (
    $type == strip_tags($username)
) {
    $usersReturnedQuery = "SELECT *
                           FROM users
                           WHERE (username LIKE '$query%'
                           AND user_closed='$status')
                           LIMIT 8";

    $usersReturned = $con->query($usersReturnedQuery);
} else {
    $names = explode(' ', $query);

    if (
        count($names) == 3
    ) {
        $usersReturnedQuery = "SELECT *
                               FROM users
                               WHERE ((first_name LIKE '$names[0]%'
                               AND last_name LIKE '%$names[2]%')
                               AND user_closed='$status')";

        $usersReturned = $con->query($usersReturnedQuery);
    }

    elseif (
        count($names) == 3
    ) {
        $usersReturnedQuery = "SELECT *
                               FROM users
                               WHERE ((first_name LIKE '$names[0]%'
                               AND last_name LIKE '%$names[1]%')
                               AND user_closed='$status')";

        $usersReturned = $con->query($usersReturnedQuery);
    } else {
        $usersReturnedQuery = "SELECT *
                               FROM users 
                               WHERE ((first_name LIKE '$names[0]%' 
                               OR last_name LIKE '%$names[0]%')
                               AND user_closed='$status')";

        $usersReturned = $con->query($usersReturnedQuery);
    }
}

?>