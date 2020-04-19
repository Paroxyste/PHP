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

        $str     = '';
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

            $latestMsgData = $this->GetLatestMessage($userLoggedIn, $username);

            // Add ... if the message preview is too long
            $dots  = (strlen($latestMsgData[0]) >= 50) ? ' ...' : '';
            $split = str_split($latestMsgData[0], 50);
            $split = $split[0] . $dots;

            // Style of the list of current conversations
            $str .= "
                <a href='messages.php?u=$username' style='outline: none;'>
                    <h3 class='heading'>
                        <img src='" . $userFoundObj->GetProfilePic() . "'
                             class='avatar' />

                        <span class='ml-3 text-primary'>"
                            . $userFoundObj->GetFullName() .
                        "</span>

                        <small class='text-muted'> - "
                            . $latestMsgData[1] .
                        "</small>

                        <div class='text-left ml-2 mt-4'>
                           <small>"
                               . $split .
                           "</small>
                        </div>
                    </h3>

                    <hr class='my-4' />
                </a>
            ";
        }

        return $str;

    }

    // ----------------------------------------------------- GetConversDropdown

    public function GetConversDropdown($data, $limit)
    {

        $page = $data['page'];

        $userLoggedIn = $this->userObj->GetUsername();

        $str     = '';
        $convers = array();

        if (
            $page == 1
        ) {
            $start = 0;
        } else {
            $start = ($page - 1) * $limit;
        }

        $updViewedMsgQuery = "UPDATE messages 
                              SET viewed='yes' 
                              WHERE (user_to='$userLoggedIn')";

        $updViewedMsg = $this->con->query($updViewedMsgQuery);

        $getConversDropdownQuery = "SELECT user_from, user_to 
                                    FROM messages 
                                    WHERE (user_from='$userLoggedIn' 
                                    OR user_to='$userLoggedIn') 
                                    ORDER BY id 
                                    DESC";

        $getConversDropdown = $this->con->query($getConversDropdownQuery);

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

            $latestMsgData = $this->GetLatestMessage($userLoggedIn, 
                                                     $username);

            // Add ... if the message preview is too long
            $dots  = (strlen($latestMsgData[0]) >= 29) ? ' ...' : '';
            $split = str_split($latestMsgData[0], 29);
            $split = $split[0] . $dots;

            // Style of notification of received messages
            $str .= "
                <a href='messages.php?u=$username' style='outline: none;'>
                    <h3 class='heading-small'>
                        <img src='" . $userFoundObj->GetProfilePic() . "' 
                             class='avatar ml-1 mr-1' />

                        <span style='position: relative; top: -2vh'
                              class='text-primary mr-2'>"
                            . $userFoundObj->getFullName() .
                        "</span>

                        <br />

                        <div class='mt--3' 
                            style='position: relative; left: 4vw;'>

                            <small class='text-muted'>"
                                . $latestMsgData[1] .
                            "</small>
                        </div>

                        <div class='text-left ml-2 mt-3 mb--3'>
                            <small>"
                                . $split .
                            "</small>
                        </div>
                    </h3>

                    <hr class='mt-4 mb-2' />
                </a>
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

                <div class='alert alert-primary mt--2 mb--2' 
                     style='border-radius: 0; text-align: center;'>

                    <span class='heading-small' style='font-weight:600;'>
                        No more messages !
                    </span>
                </div>
            ";
        }

        return $str;

    }

    // ----------------------------------------------------------- GetLatestMsg

    public function getLatestMessage($userLoggedIn, $user2)
    {

        $detailsArray = array();

        $getLatestMsgQuery = "SELECT user_to, massage, datetime 
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

        // Include Timeframe
        include('../../controller/handlers/timeframe.php');

        array_push($detailsArray, $row['message']);
        array_push($detailsArray, $timeMsg);

        return $detailsArray;

    }

    // ------------------------------------------------------------ GetMessages

    public function GetMessages($otherUser)
    {

        $userLoggedIn = $this->userObj->GetUsername();

        $str = '';

        $updOpenedMsgQuery = "UPDATE messages 
                              SET opened='yes' 
                              WHERE (user_from='$otherUser' 
                              AND user_to='$userLoggedIn')";

        $updOpenedMsg = $this->con->query($updOpenedMsgQuery);

        $getMsgQuery = "SELECT * 
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

            // Style of the bubbles in the messages sent / received
            $fromBubble = "<div class='bubble' id='green'>";
            $toBubble   = "<div class='bubble' id='primary'>";

            $divTop = ($userTo == $userLoggedIn) ? $toBubble : $fromBubble;
            $str    = $str . $divTop . $message . "</div>";
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
            $user_to != $userLoggedIn
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

        $getUnreadMsgQuery = "SELECT * 
                              FROM messages 
                              WHERE (viewed='no' 
                              AND user_to='$userLoggedIn')";

        $getUnreadMsg = $this->con->query($getUnreadMsgQuery);

        return $getUnreadMsg->num_rows;

    }

    // ------------------------------------------------------------ SendMessage

    public function SendMessage($userTo, $message, $dateTime) 
    {

        if (
            $message != ''
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
