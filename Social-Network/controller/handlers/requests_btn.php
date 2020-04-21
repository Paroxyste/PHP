<?php

$sendMsg = "<a href='messages.php?u=". strip_tags($username) ."'
                    class='btn btn-blue btn-xs mb-2
                           waves-effect waves-light'>
                Send Message
            </a>";

$userLoggedInObj = new User($con, $userLoggedIn);

if (
    $userLoggedIn != $username
) {
    if (
        $userLoggedInObj->IsFriend($username)
    ) {
        // Remove friend btn
        echo "
            <button type='submit' name='remove_friend'
                    class='btn btn-danger btn-xs mb-2
                           waves-effect waves-light mr-1'>
                Remove Friend
            </button>" . $sendMsg;
    }

    elseif (
        $userLoggedInObj->DidReceiveRequest($username)
    ) {
        // Pending Request
        echo "
            <button type='submit' name='respond_request'
                    class='btn btn-warning btn-xs mb-2
                           waves-effect waves-light mr-1'
                    disabled>
                Respond to Request
            </button>" . $sendMsg;
    }

    elseif (
        $userLoggedInObj->DidSendRequest($username)
    ) {
        // Request Send
        echo "
            <button class='btn btn-secondary btn-xs mb-2
                           waves-effect waves-light mr-1'
                    disabled>
                Request Sent
            </button>" . $sendMsg;
    } else {
        // Add Friend
        echo "
            <button type='submit' name='add_friend' 
                    class='btn btn-success btn-xs mb-2
                           waves-effect waves-light mr-1'>
                Add Friend
            </button>" . $sendMsg;
    }
}

?>