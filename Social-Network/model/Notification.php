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

    public function GetNotifications($data, $limit) 
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
                <span class='dropdown-item text-center text-primary
                             notify-item notify-all'>
                    You dont have notifications !
                </span>
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
                              WHERE (username='$userFrom')";

            $userData = $this->con->query($userDataQuery);

            $userRow = $userData->fetch_assoc();

            // Include Timeframe
            include('../../controller/handlers/timeframe_notifs.php');

            $str .= "
                <div class='slimscroll noti-scroll'>

                    <a href='" . $row['link'] . "'
                       class='dropdown-item notify-item'>
                        <div class='notify-icon'>
                            <img src='" . $userRow['profile_pic'] . "'
                                 class='img-fluid rounded-circle'
                                 alt='Messages Icon' />
                        </div>

                        <p class='notify-details'>"
                            . $row['message'] .
                        "</p>

                        <p class='text-muted mb-0 user-msg'>
                            <small>"
                                . $timeMsg .
                            "</small>
                        </p>
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

                    <span class='dropdown-item text-center text-primary
                                 notify-item notify-all'>
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

        $link   = 'post.php?id=' . $postId;
        $viewed = $opened = 'no';

        $userLoggedIn =  $this->con->real_escape_string($userLoggedIn);
        $userTo       =  $this->con->real_escape_string($userTo);
        $msg          =  $this->con->real_escape_string($msg);
        $dateTime     =  $this->con->real_escape_string($dateTime);
        $link         =  $this->con->real_escape_string($link);
        $viewed       =  $this->con->real_escape_string($viewed);
        $opened       =  $this->con->real_escape_string($opened);

        $insNotifsQuery = "INSERT INTO notifications 
                           VALUES(0, '$userLoggedIn', '$userTo', 
                                  '$msg', '$dateTime', '$link', 
                                  '$viewed', '$opened')";

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
