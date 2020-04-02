<?php

declare(strict_types=1);

if (
    isset($_GET['user_search'])
) {
    $query = $_GET['user_search'];
} else {
    $query = '';
}

if (
    isset($_GET['type'])
) {
    $type = $_GET['type'];
} else {
    $type = 'name';
}

$link_check =  "{$_SERVER['REQUEST_URI']}";
$link_base  = '/search.php?user_search=' . strip_tags($query);

$link_username = strip_tags($link_base) . '&type=username';
$link_fullname = strip_tags($link_base) . '&type=name';

if (
    $type == 'username'
) {
    $usersReturnedQuery = "SELECT *
                           FROM users
                           WHERE (username LIKE '$query%'
                           AND user_closed='no')
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
                               AND user_closed='no')";

        $usersReturned = $con->query($usersReturnedQuery);
    }

    elseif (
        count($names) == 3
    ) {
        $usersReturnedQuery = "SELECT *
                               FROM users
                               WHERE ((first_name LIKE '$names[0]%'
                               AND last_name LIKE '%$names[1]%')
                               AND user_closed='no')";

        $usersReturned = $con->query($usersReturnedQuery);
    } else {
        $usersReturnedQuery = "SELECT *
                               FROM users 
                               WHERE ((first_name LIKE '$names[0]%' 
                               OR last_name LIKE '%$names[0]%')
                               AND user_closed='no')";

        $usersReturned = $con->query($usersReturnedQuery);
    }
}

?>