<?php

declare(strict_types=1);

class Notification 
{
    private $con;
    private $userObj;

    // ------------------------------------------------------------ constructor

    public function __construct($con, $user) 
    {

        $this->con = $con;
        $this->userObj = new User($con, $user);

    }

    // ------------------------------------------------------- GetNotifications

    public function getNotifications($data, $limit) 
    {

        $page = $data['page'];

        $userLoggedIn = $this->userObj->GetUsername();

        $str = '';

        if (
            $page == 1
        ) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $updViewedNotifQuery = "UPDATE notifications 
                                SET viewed='yes' 
                                WHERE (user_to='$userLoggedIn')";

        $updViewedNotif = $this->con->query($updViewedNotifQuery);

        $getNotifQuery = "SELECT * 
                          FROM notifications 
                          WHERE (user_to='$userLoggedIn') 
                          ORDER BY id 
                          DESC";

        $getNotif = $this->con->query($getNotifQuery);

        if (
            $getNotif->num_rows == 0
        ) {
            echo "
                <div class='alert alert-primary mb--2' 
                     style='border-radius: 0; text-align: center;'>

                    <span class='heading-small' style='font-weight:600;'>
                        You have no notifications.
                    </span>
                </div>
            ";

            return;

        }

        $numIterations = 0;
        $count = 1;

        while ($row = $getNotif->fetch_assoc()) {
            if (
                $numIterations++ < $start
            ) {
                continue;
            }

            if (
                $count > $limit
            ) {
                break;
            } else {
                $count++;
            }

            $userFrom = $row['user_from'];

            $userDataQuery = "SELECT * 
                              FROM users 
                              WHERE (username='$user_from')";

            $userData = $this->con->query($userDataQuery);

            $userRow = $userData->fetch_assoc();

            // Include Timeframe
            include('../../controller/handlers/timeframe.php');

            $str .= "
                <a href='" . $row['link'] . "' style='outline: none;'>
                    <h3 class='heading-small'>
                        <img  src='" . $userRow['profile_pic'] . "'
                              class='avatar ml-1 mr-1'/>

                        <span style='position: relative; top: -2vh'
                              class='text-primary mr-2'>"
                            . $row['message'] .
                        "</span>

                        <br />

                        <div class='mt--3' 
                             style='position: relative; left: 4vw;'>

                            <small class='text-muted'>"
                                . $timeMsg .
                            "</small>
                        </div>
                    </h3>

                    <hr class='mt-4 mb-2' />
                </a>
            ";

        } // End while($row = $getNotif->fetch_assoc())

        // If post were loaded
        if (
            $count > $limit
        ) {
            $str .= "
                <input type='hidden' 
                       class='nextPageDropdownData' 
                       value='" . ($page + 1) . "'/>

                <input type='hidden' 
                       class='noMoreDropdownData' 
                       value='FALSE' />
            ";
        } else {
            $str .= "
                <input type='hidden' class='noMoreDropdownData' value='TRUE' />

                <div class='alert alert-primary mt--2 mb--2' 
                     style='border-radius: 0; text-align:center;'>

                    <span class='heading-small' style='font-weight:600;'>
                        No more notifications !
                    </span>
                </div>
            ";

        }

        return $str;

    }

    // -------------------------------------------------------------- InsNotifs

    public function InsNotifs($postId, $userTo, $type) 
    {

        $userLoggedIn = $this->userObj->GetUsername();
        $userLoggedInFullName = $this->userObj->GetFullName();

        $dateTime = date('Y-m-d H:m:s');

        switch ($type) {
            case 'comment':
                $msg = $userLoggedInFullName . ' commented on your post';
                break;
            case 'like':
                $msg = $userLoggedInFullName . ' like your post';
                break;
            case 'profile_post':
                $msg = $userLoggedInFullName . ' posted on your profile';
                break;
            case 'comment_non_owner':
                $msg = $userLoggedInFullName . ' commented on a post you commented on';
                break;
            case 'profile_comment':
                $msg = $userLoggedInFullName . ' commented on your profile post';
                break;
        }

        $link = 'post.php?id=' . $postId;

        $userLoggedIn =  $this->con->real_escape_string($userLoggedIn);
        $userTo       =  $this->con->real_escape_string($userTo);
        $msg          =  $this->con->real_escape_string($msg);
        $dateTime     =  $this->con->real_escape_string($dateTime);
        $link         =  $this->con->real_escape_string($link);

        $insNotifsQuery = "INSERT INTO notifications 
                           VALUES('', '$userLoggedIn', '$userTo', '$msg', 
                                  '$dateTime', '$link', 'no', 'no')";

        $insNotifs = $this->con->query($insNotifsQuery);
    }

    // ----------------------------------------------------- UnreadNotifsNumber

    public function UnreadNotifsNumber() 
    {

        $userLoggedIn = $this->userObj->GetUsername();

        $getUnreadNotifsQuery = "SELECT * 
                                 FROM notifications 
                                 WHERE (viewed='no' 
                                 AND user_to='$userLoggedIn')";

        $getUnreadNotifs = $this->con->query($getUnreadNotifsQuery);

        return $getUnreadNotifs->num_rows;

    }

}  // End Notification class

?>
