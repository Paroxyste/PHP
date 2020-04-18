<?php

declare(strict_types=1);

require('./config/config.php');

$errEmail      = '';
$errPassword   = '';

$loginMsg      = '';

if (
    isset($_POST['log_btn'])
) {

    // ------------------------------------------------------------------ Email

    $email = test_input(filter_var($_POST['email'], FILTER_SANITIZE_EMAIL));

    $_SESSION['email'] = $email;

    if (
        empty($email)
    ) {
        $errEmail = 'This field is required';
    }

    if (
        filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        if (
            strlen($email) < 10
            || strlen($email) > 100
        ) {
            $errEmail = 'Your email must be between 10 & 100 characters';
        }

        if (
            !preg_match("/^[0-9a-zA-Z._-]+@[0-9a-zA-Z._-]+\.[a-z]{2,4}$/",
            $email)
        ) {
            $errEmail = 'The characters allowed are : [0-9 a-z A-Z ._-]';
        }
    }

    // --------------------------------------------------------------- Password

    $password = strip_tags($_POST['password']);

    if (
        empty($password)
    ) {
        $errPassword = 'This field is required';
    }

    if (
        strlen($password) < 8
    ) {
        $errPassword = 'Your password must have at least 8 characters';
    }

    // -------------------------------------------------------------- Check BDD

    $checkDatabaseQuery = "SELECT *
                           FROM users
                           WHERE (email='$email')";

    $checkDatabase = $con->query($checkDatabaseQuery);

    $checkLogin = $checkDatabase->num_rows;

    $row = $checkDatabase->fetch_assoc();

    $hashedPassword = $row['password'];
    $passwordVerify = password_verify($password, $hashedPassword);

    if (
        $checkLogin == 1
        && $passwordVerify == 1
    ) {
        $username   = $row['username'];
        $userStatus = $row['user_closed'];

        $_SESSION['username'] = $username;

        // ------------------------------------------------- Check user account

        $userClosedQuery = "SELECT *
                            FROM users
                            WHERE (email='$email')
                            AND (user_closed='$userStatus')";

        $userClosed = $con->query($userClosedQuery);

        if (
            $userClosed->num_rows == 1
        ) {
            $userStatus = 'no';

            $userStatus = $con->real_escape_string($userStatus);

            $reOpenAccountQuery = "UPDATE users
                                   SET user_closed='$userStatus'
                                   WHERE (email='$email')";

            $reOpenAccount = $con->query($reOpenAccountQuery);
        }

        header('refresh:3; url='. strip_tags($username));

        $loginMsg = "
            <div class='alert alert-success text-center'
                 role='alert' style='margin-bottom: 3.804vh;'>
                <i class='ti-check mr-2'></i>

                <strong>
                    Success ! You will be automatically redirected in 3s ...
                </strong>
            </div>
        ";
    } else {
        $loginMsg = "
            <div class='alert alert-danger text-center'
                 role='alert' style='margin-bottom: 3.804vh;'>
                <i class='ti-alert mr-2'></i>

                <strong>
                    Oops, an error occurred ! Please check your email address
                    and password ...
                </strong>
            </div>
        ";
    }
}

function test_input($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = str_replace(' ', '', $data);
    $data = ucfirst(strtolower($data));

    return $data;
}

?>
