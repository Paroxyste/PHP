<?php

declare(strict_types=1);

class Message 
{
    private $con;
    private $userObj;

    // ------------------------------------------------------------ constructor

    public function __construct($con, $user) 
    {

        $this->con = $con;
        $this->userObj = new User($con, $user);

    }

    // ------------------------------------------------------------- GetConvers

    public function GetConvers() 
    {

        $userLoggedIn = $this->userObj->getUsername();

        $str     = NULL;
        $convers = array();

        $getConversQuery = "SELECT user_from, user_to 
                            FROM messages 
                            WHERE (user_from='$userLoggedIn' 
                            OR user_to='$userLoggedIn') 
                            ORDER BY id 
                            DESC";

        $getConvers = $this->con->query($getConversQuery);

        while ($row = $getConvers->fetch_assoc()) {
            $userToPush = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if (
                !in_array($userToPush, $convers)
            ) {
                array_push($convers, $userToPush);
            }
        }

        foreach ($convers as $username) {
            $userFoundObj = new User($this->con, $username);

            $latestMsgData = $this->GetLatestMsg($userLoggedIn, $username);

            // Add ... if the message preview is too long
            $dots  = (strlen($latestMsgData[0]) >= 35) ? ' ...' : '';
            $split = str_split($latestMsgData[0], 35);
            $split = $split[0] . $dots;

            // Style of the list of current conversations
            $str .= "
                <div class='inbox-item'>
                    <div class='inbox-item-img'>
                        <img src='" . strip_tags($userFoundObj->GetProfilePic()) . "'
                             class='rounded-circle'
                             alt='' />
                    </div>

                    <p class='inbox-item-author'>"
                        . strip_tags($userFoundObj->GetFullName()) .
                    "</p>

                    <p class='inbox-item-text'>"
                        . strip_tags($split) .
                    "</p>

                    <p class='inbox-item-date'>
                        <a href='messages.php?u=". strip_tags($username) ."'
                           class='btn btn-sm btn-link text-info font-13'>
                            Reply
                        </a>
                    </p>
                </div>

                <hr />
            ";
        }

        return $str;

    }

    // ----------------------------------------------------- GetConversDropdown

    public function GetConversDropdown($data, $limit)
    {

        $page = $data['page'];

        $userLoggedIn = $this->userObj->GetUsername();

        $str     = NULL;
        $convers = array();
        $status  = 'yes';

        if (
            $page == 1
        ) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $updViewedMsgQuery = "UPDATE messages 
                              SET viewed='$status' 
                              WHERE (user_to='$userLoggedIn')";

        $updViewedMsg = $this->con->query($updViewedMsgQuery);

        $getConversDropdownQuery = "SELECT user_from, user_to 
                                    FROM messages 
                                    WHERE (user_from='$userLoggedIn' 
                                    OR user_to='$userLoggedIn')
                                    ORDER BY id 
                                    DESC";

        $getConversDropdown = $this->con->query($getConversDropdownQuery);

        if (
            $getConversDropdown->num_rows == 0
        ) {
            echo "
                <span class='dropdown-item text-center text-primary
                             notify-item notify-all alert-primary'>
                    You dont have messages !
                </span>
            ";

            return;

        }

        while ($row = $getConversDropdown->fetch_assoc()) {
            $userToPush = ($row['user_to'] != $userLoggedIn) ? $row['user_to'] : $row['user_from'];

            if (
                !in_array($userToPush, $convers)
            ) {
                array_push($convers, $userToPush);
            }
        }

        $numIterations = 0;
        $count         = 1;

        foreach ($convers as $username) {
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

            $getUnreadMsgQuery = "SELECT opened 
                                  FROM messages 
                                  WHERE (user_from='$username' 
                                  AND user_to='$userLoggedIn') 
                                  ORDER BY id 
                                  DESC";

            $getUnreadMsg = $this->con->query($getUnreadMsgQuery);

            $row = $getUnreadMsg->fetch_assoc();

            $userFoundObj = new User($this->con, $username);

            $latestMsgData = $this->GetLatestMsg($userLoggedIn, 
                                                 $username);

            // Add ... if the message preview is too long
            $dots  = (strlen($latestMsgData[0]) >= 29) ? ' ...' : '';
            $split = str_split($latestMsgData[0], 29);
            $split = $split[0] . $dots;

            // Style of notification of received messages
            $str .= "
                <div class='slimscroll noti-scroll'>
                    <a href='messages.php?u=". strip_tags($username) ."'
                       class='dropdown-item notify-item'>
                        <div class='notify-icon'>
                            <img src='" . strip_tags($userFoundObj->GetProfilePic()) . "'
                                class='img-fluid rounded-circle'
                                alt='Messages Icon' />
                        </div>

                        <p class='notify-details'>"
                            . strip_tags($userFoundObj->GetFullName()) .

                            "<small class='text-muted'>
                                <i>"
                                    . strip_tags($split) .
                                "</i>
                            </small>
                        </p>
                    </a>
                </div>
            </div>

            ";
        }

        // If post were loaded
        if (
            $count > $limit
        ) {
            $str .= "
                <input type='hidden' class='nextPageDropdownData' 
                       value='" . ($page + 1) . "'/>

                <input type='hidden' class='noMoreDropdownData' 
                       value='FALSE' />
            ";
        } else {
            $str .= "
                <input type='hidden' class='noMoreDropdownData'
                       value='TRUE' />

                <span class='dropdown-item text-center text-primary
                             notify-item notify-all alert-primary'>
                    No more messages !
                </span>
            ";
        }

        return $str;

    }

    // ----------------------------------------------------------- GetLatestMsg

    public function GetLatestMsg($userLoggedIn, $user2)
    {

        $detailsArray = array();

        $getLatestMsgQuery = "SELECT user_to, message, datetime 
                              FROM messages 
                              WHERE (user_from='$user2' 
                              AND user_to='$userLoggedIn') 
                              OR (user_from='$userLoggedIn' 
                              AND user_to='$user2') 
                              ORDER BY id 
                              DESC 
                              LIMIT 1";

        $getLatestMsg = $this->con->query($getLatestMsgQuery);

        $row = $getLatestMsg->fetch_assoc();

        array_push($detailsArray, $row['message']);

        return $detailsArray;

    }

    // ------------------------------------------------------------ GetMessages

    public function GetMessages($otherUser)
    {

        $userLoggedIn = $this->userObj->GetUsername();

        $str = NULL;
        $status = 'yes';

        $updOpenedMsgQuery = "UPDATE messages 
                              SET opened='$status'
                              WHERE (user_from='$otherUser' 
                              AND user_to='$userLoggedIn')";

        $updOpenedMsg = $this->con->query($updOpenedMsgQuery);

        $getMsgQuery = "SELECT user_from, user_to, message, datetime 
                        FROM messages 
                        WHERE (user_from='$otherUser' 
                        AND user_to='$userLoggedIn') 
                        OR (user_from='$userLoggedIn' 
                        AND user_to='$otherUser')";
                        

        $getMsg = $this->con->query($getMsgQuery);

        while($row = $getMsg->fetch_assoc()) {
            $userFrom = $row['user_from'];
            $userTo   = $row['user_to'];
            $message  = $row['message'];
            $dateTime = $row['datetime'];

            // Get user datas
            $getUserDatasQuery = "SELECT first_name, last_name, profile_pic
                                  FROM users
                                  WHERE (username='$userFrom')";

            $getUserDatas = $this->con->query($getUserDatasQuery);

            $user = $getUserDatas->fetch_assoc();

            $firstName  = $user['first_name'];
            $lastName   = $user['last_name'];
            $profilePic = $user['profile_pic'];

            // Include Timeframe
            include('./controller/handlers/timeframe.php');

            // Style of the bubbles in the messages sent / received
            $fromBubble = "
                <li class='clearfix'>
                    <div class='chat-avatar'>
                        <img src='". strip_tags($profilePic) . "' alt='Pic'>
                        <i style='font-size: 9px;'>"
                            . strip_tags($timeMsg) .
                        "</i>
                    </div>

                    <div class='conversation-text'>
                        <div class='ctext-wrap'>
                            <i>"
                                . strip_tags($firstName) .
                                " " . strip_tags($lastName) .
                            "</i>
    
                            <p style='font-size: 13px;'>"
                                . strip_tags($message) .
                            "</p>
                        </div>
                    </div>
                </li>
            ";

            $toBubble   = "
                <li class='clearfix odd'>
                    <div class='chat-avatar'>
                        <img src='". strip_tags($profilePic) ."' alt='Pic'>
                        <i style='font-size: 9px;'>"
                            . strip_tags($timeMsg) .
                        "</i>
                    </div>

                    <div class='conversation-text'>
                        <div class='ctext-wrap'>
                            <i>"
                                . strip_tags($firstName) .
                                " " . strip_tags($lastName) .
                            "</i>

                            <p style='font-size: 13px;'>"
                                . strip_tags($message) .
                            "</p>
                        </div>
                    </div>
                </li>
            ";

            $divTop = ($userTo == $userLoggedIn) ? $toBubble : $fromBubble;
            $str    = $str . $divTop;
        }

        return $str;

    }

    // ------------------------------------------------------ GetMostRecentUser

    public function GetMostRecentUser()
    {

        $userLoggedIn = $this->userObj->GetUsername();

        $getMostRecentUserQuery = "SELECT user_from, user_to, 
                                   FROM messages 
                                   WHERE (user_from='$userLoggedIn' 
                                   OR user_to='$userLoggedIn') 
                                   ORDER BY id 
                                   DESC 
                                   LIMIT 1";

        $getMostRecentUser = $this->con->query($getMostRecentUserQuery);

        if (
            $getMostRecentUser->num_rows == 0
        ) {
            return FALSE;
        }

        $row = $getMostRecentUser->fetch_assoc();

        $userFrom = $row['user_from'];
        $userTo   = $row['user_to'];

        if (
            $userTo != $userLoggedIn
        ) {
            return $userTo;
        } else {
            return $userFrom;
        }

    }

    // -------------------------------------------------------- UnreadMsgNumber

    public function UnreadMsgNumber() 
    {

        $userLoggedIn = $this->userObj->GetUsername();

        $status = 'no';

        $getUnreadMsgQuery = "SELECT * 
                              FROM messages 
                              WHERE (viewed='$status' 
                              AND user_to='$userLoggedIn')";

        $getUnreadMsg = $this->con->query($getUnreadMsgQuery);

        return $getUnreadMsg->num_rows;

    }

    // ------------------------------------------------------------ SendMessage

    public function SendMessage($userTo, $message, $dateTime) 
    {

        if (
            $message != NULL
        ) {
            $userLoggedIn = $this->userObj->GetUsername();

            $viewed = $opened = $deleted = 'no';

            $userLoggedIn = $this->con->real_escape_string($userLoggedIn);
            $userTo       = $this->con->real_escape_string($userTo);
            $message      = $this->con->real_escape_string($message);
            $dateTime     = $this->con->real_escape_string($dateTime);
            $viewed       = $this->con->real_escape_string($viewed);
            $opened       = $this->con->real_escape_string($opened);
            $deleted      = $this->con->real_escape_string($deleted);

            $insMsgQuery = "INSERT INTO messages 
                            VALUES(0, '$userLoggedIn', '$userTo', 
                                   '$message', '$dateTime', '$viewed', 
                                   '$opened', '$deleted')";

            $insMsg = $this->con->query($insMsgQuery);
        }

    }

}  // End Message class

?>
