<?php

declare(strict_types=1);

class User
{
    private $con;
    private $user;

    // ------------------------------------------------------------ constructor

    public function __construct($con, $user)
    {

        $this->con = $con;

        $userDataQuery = "SELECT *
                          FROM users
                          WHERE (username='$user')";

        $userData = $this->con->query($userDataQuery);

        $this->user = $userData->fetch_assoc();

    }

    // ---------------------------------------------------------- ClosedAccount

    public function ClosedAccount()
    {

        $username = $this->user['username'];

        $closedAccountQuery = "SELECT user_closed
                               FROM users
                               WHERE (username='$username')";

        $closedAccount = $this->con->query($closedAccountQuery);

        $row = $closedAccount->fetch_assoc();

        if (
            $row['user_closed'] == 'yes'
        ) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    // ------------------------------------------------------ DidReceiveRequest

    public function DidReceiveRequest($userFrom)
    {

        $userTo = $this->user['username'];

        $didReceiveReqQuery = "SELECT *
                               FROM friend_requests
                               WHERE (user_from='$userFrom'
                               AND user_to='$userTo')";

        $didReceiveReq = $this->con->query($didReceiveReqQuery);

        if (
            $didReceiveReq->num_rows > 0
        ) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    // --------------------------------------------------------- DidSendRequest

    public function DidSendRequest($userTo)
    {

        $userFrom = $this->user['username'];

        $didSendReqQuery = "SELECT *
                            FROM friend_requests
                            WHERE (user_from='$userFrom'
                            AND user_to='$userTo')";

        $didSendReq = $this->con->query($didSendReqQuery);

        if (
            $didSendReq->num_rows > 0
        ) {
            return TRUE;
        } else {
            return FALSE;
        }

    }

    // --------------------------------------------------------- GetFriendArray

    public function GetFriendArray()
    {

        $username = $this->user['username'];

        $getFriendArrayQuery = "SELECT friend_array
                                FROM users
                                WHERE (username='$username')";

        $getFriendArray = $this->con->query($getFriendArrayQuery);

        $row = $getFriendArray->fetch_assoc();

        return $row['friend_array'];

    }

    // ------------------------------------------------------- GetFriendRequest

    public function GetFriendRequest()
    {

        $username = $this->user['username'];

        $getFriendReqQuery = "SELECT *
                              FROM friend_requests
                              WHERE (user_to='$username')";

        $getFriendReq = $this->con->query($getFriendReqQuery);

        return $getFriendReq->num_rows;

    }

    // ----------------------------------------------------------- GetFullName
    public function getFullName()
    {

        $username = $this->user['username'];

        $getFullNameQuery = "SELECT first_name, last_name
                             FROM users
                             WHERE (username='$username')";

        $getFullName = $this->con->query($getFullNameQuery);

        $row = $getFullName->fetch_assoc();

        return $row['first_name'] . ' ' . $row['last_name'];

    }

    // ------------------------------------------------------- GetMutualFriends

    public function GetMutualFriends($userToCheck)
    {

        $mutalFriends = 0;

        $friendArray = $this->user['friend_array'];
        $friendArrayExp = explode(',' , $friendArray);

        $getFriendArrayQuery = "SELECT friend_array
                                FROM users
                                WHERE (username='$userToCheck')";

        $getFriendArray = $this->con->query($getFriendArrayQuery);

        $row = $getFriendArray->fetch_assoc();

        $checkFriendArray = $row['friend_array'];
        $checkFriendArrayExp = explode(',' , $checkFriendArray);

        foreach ($friendArrayExp as $i) {
            foreach ($checkFriendArrayExp as $j) {
                if (
                    $i == $j
                    && $i != ''
                ) {
                    $mutalFriends++;
                }
            }
        }

        return $mutalFriends;

    }

    // ------------------------------------------------------------ GetNumPosts

    public function GetNumPosts()
    {

        $username = $this->user['username'];

        $getNumPostsQuery = "SELECT num_posts
                             FROM users
                             WHERE (username='$username')";

        $getNumPosts = $this->con->query($getNumPostsQuery);

        $row = $getNumPosts->fetch_assoc();

        return $row['num_posts'];

    }

    // ---------------------------------------------------------- GetProfilePic

    public function GetProfilePic()
    {

        return $this->user['profile_pic'];

    }

    // -------------------------------------------------------------- GetSignUp

    public function GetSignUp()
    {

        return $this->user['signup_date'];

    }

    // ------------------------------------------------------------ GetUsername

    public function GetUsername()
    {

        return $this->user['username'];

    }

    // --------------------------------------------------------------- IsFriend

    public function IsFriend($usernameToCheck)
    {

        $usernameComma = ',' . $usernameToCheck . ',';

        if (
            strstr($this->user['friend_array'], $usernameComma)
            || $usernameToCheck == $this->user['username']
        ) {
            return TRUE;
        } else {
            return FALSE;
        }
    }

    // ----------------------------------------------------------- RemoveFriend

    public function RemoveFriend($userToRemove)
    {

        $userLogged = $this->user['username'];

        $removeFriendQuery = "SELECT friend_array
                              FROM users
                              WHERE (username='$userToRemove')";

        $removeFriend = $this->con->query($removeFriendQuery);

        $row = $removeFriend->fetch_assoc();

        $friendArray = $row['friend_array'];

        $newFriendArray = str_replace(
                            $userToRemove . ',', '',$this->user['friend_array']
                          );

        $newFriendArray = $this->con->real_escape_string($newFriendArray);

        $updFriendArrayQuery = "UPDATE users
                                SET friend_array='$newFriendArray'
                                WHERE (username='$userLogged')";

        $updFriendArray = $this->con->query($updFriendArrayQuery);

        $newFriendArray = str_replace(
                            $this->user['username'] . ',', '', $friendArray
                          );

        $newFriendArray = $this->con->real_escape_string($newFriendArray);

        $updFriendArrayQuery = "UPDATE users
                                SET friend_array='$newFriendArray'
                                WHERE (username='$userToRemove')";

        $updFriendArray = $this->con->query($updFriendArrayQuery);

    }

    // ------------------------------------------------------------ SendRequest

    public function SendRequest($userTo)
    {

        $userFrom = $this->user['username'];

        $userFrom = $this->con->real_escape_string($userFrom);
        $userTo   = $this->con->real_escape_string($userTo);

        $sendRequestQuery = "INSERT INTO friend_requests
                             VALUES(0, '$userFrom', '$userTo')";

        $sendRequest = $this->con->query($sendRequestQuery);

    }

} // End class User

?>
