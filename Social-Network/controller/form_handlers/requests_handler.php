<?php

$getRequestsQuery = "SELECT * 
                     FROM friend_requests 
                     WHERE (user_to='$userLoggedIn')";

$getRequests = $con->query($getRequestsQuery);

if (
    $getRequests->num_rows == 0
) {
    echo "
        <div class='tab-content'>
            <div id='alerts-disimissible-component' class='fade show active'>
                <div class='alert alert-success fade show'>
                    <span class='alert-inner--icon'>
                        <i class='ti-timer mr-1'></i>
                    </span>

                    <span class='alert-inner--text'>
                        <strong>
                            You are up to date !
                        </strong>

                        <br /><br />

                        You have no friend requests at this time ! 

                        <a href='".strip_tags($userLoggedIn)."'
                           class='btn-link text-success'>
                            <strong>
                                Click here to go back.
                            </strong>
                        </a>
                    </span>
                </div>
            </div>
        </div>
    ";
} else {
    while ($row = $getRequests->fetch_assoc()) {
        $userFrom = $row['user_from'];

        $userFromObj = new User($con, $userFrom);
        $signup = substr($userFromObj->GetSignUp(), 0);

        $userFromFriendArray = $userFromObj->GetFriendArray();

        $userFrom = $con->real_escape_string($userFrom);
        $userLoggedIn = $con->real_escape_string($userLoggedIn);

        if (
            isset($_POST['accept_request' . $userFrom])
        ) {
            $addFriendQuery = "UPDATE users 
                               SET friend_array=CONCAT(friend_array, 
                                                       '$userFrom,')
                               WHERE (username='$userLoggedIn')";

            $addFriend = $con->query($addFriendQuery);

            $addFriendQuery = "UPDATE users 
                               SET friend_array=CONCAT(friend_array, 
                                                       '$userLoggedIn,') 
                               WHERE (username='$userFrom')";

            $addFriend = $con->query($addFriendQuery);

            $deleteReqQuery = "DELETE FROM friend_requests 
                               WHERE (user_to='$userLoggedIn' 
                               AND user_from='$userFrom')";

            $delReq = $con->query($deleteReqQuery);

            echo "
                <script>
                    location.href='requests.php';
                </script>
            ";
        }

        if (
            isset($_POST['ignore_request' . $userFrom])
        ) {
            $deleteReqQuery = "DELETE FROM friend_requests 
                               WHERE (user_to='$userLoggedIn' 
                               AND user_from='$userFrom')";

            $delReq = $con->query($deleteReqQuery);

            echo "
                <script>
                    location.href='requests.php';
                </script>
            ";
        }

        echo "
            <div class='table-responsive'>
                <table class='table table-hover table-centered m-0'>
                    <thead class='thead-light'>
                        <tr>
                            <th >
                                Picture
                            </th>

                            <th>
                                Full Name
                            </th>

                            <th>
                                Username
                            </th>

                            <th>
                                Location
                            </th>

                            <th>
                                Action
                            </th>
                        </tr>
                    </thead>

                    <tbody>
                        <tr>
                            <th scope='row'>
                                <img src='".$userFromObj->GetProfilePic()."' 
                                     alt='profile_pic'
                                     title='contact-img'
                                     class='rounded-circle' 
                                     width='50px' />
                            </th>

                            <td>
                                <h5 class='m-0 font-weight-normal'>"
                                    . $userFromObj->GetFullName() .
                                "</h5>

                                <p class='mb-0 text-muted'>
                                    <small>
                                        Member Since " . $signup .
                                    "</small>
                                </p>
                            </td>

                            <td>"
                                . $userFromObj->GetUsername() .
                            "</td>

                            <td>
                                Country, City
                            </td>

                            <form action='requests.php' method='POST'>
                                <td>
                                    <button class='btn btn-xs btn-success'
                                            name='accept_request"
                                                  . strip_tags($userFrom) ."'
                                            type='submit'
                                            title='Add Friend'>
                                        <i class='ti-plus'></i>
                                    </button>

                                    <button class='btn btn-xs btn-danger'
                                            name='ignore_request"
                                                  . strip_tags($userFrom) ."'
                                            type='submit'
                                            title='Ignore'>
                                        <i class='ti-close'></i>
                                    </button>
                                </td>
                            </form>
                        </tr>
                    </tbody>
                </table>
            </div>
        ";
    }
}

?>