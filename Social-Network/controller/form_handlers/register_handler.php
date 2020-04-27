<?php

require('./config/config.php');

$firstName = NULL;
$lastName  = NULL;
$email     = NULL;
$password  = NULL;

$errFirstName = NULL;
$errLastName  = NULL;
$errEmail     = NULL;
$errPassword  = NULL;

$successQuery  = NULL;

if (
    isset($_POST['reg_btn'])
) {

    // --------------------------------------------------------- First name

    $firstName = filter_data(
                    filter_var($_POST['first_name'], FILTER_SANITIZE_STRING)
                );

    $firstName = upper_lower($firstName);

    $_SESSION['first_name'] = $firstName;

    if (
        empty($firstName)
    ) {
        $errFirstName = 'This field is required';
    }

    if (
        strlen($firstName) < 2
        || strlen($firstName) > 20
    ) {
        $errFirstName = 'Your first name must be between 2 & 20 characters';
    }

    if (
        !preg_match("/^[a-zA-Z -]+$/", $firstName)
    ) {
        $errFirstName = 'The characters allowed are : [a-z A-Z -]';
    }

    // -------------------------------------------------------------- Last name

    $lastName = filter_data(
                    filter_var($_POST['last_name'], FILTER_SANITIZE_STRING)
                );

    $lastName = upper_lower($lastName);

    $_SESSION['last_name'] = $lastName;

    if (
        empty($lastName)
    ) {
        $errLastName = 'This field is required';
    }

    if (
        strlen($lastName) < 2
        || strlen($lastName) > 20
    ) {
        $errLastName = 'Your last name must be between 2 & 20 characters';
    }

    if (
        !preg_match("/^[a-zA-Z -]+$/", $lastName)
    ) {
        $errLastName = 'The characters allowed are : [a-z A-Z -]';
    }

    // ------------------------------------------------------------------ Email

    $email = filter_data(
                filter_var($_POST['email'], FILTER_SANITIZE_EMAIL)
    );

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

        $emailCheckQuery = "SELECT email
                            FROM users
                            WHERE (email='$email')";

        $emailCheck = $con->query($emailCheckQuery);

        $numRows = $emailCheck->num_rows;

        if (
            $numRows > 0
        ) {
            $errEmail = 'Email already in use';
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

    // --------------------------------------------------------------- Username

    if (
        empty($errFirstName)
        && empty($errLastName)
        && empty($errEmail)
        && empty($errPassword)
    ) {
        $username = strtolower($firstName . '_' . $lastName);

        $usernameCheckQuery = "SELECT username
                               FROM users
                               WHERE (username='$username')";

        $usernameCheck = $con->query($usernameCheckQuery);
        $i = 0;

        while ($usernameCheck->num_rows != 0) {
            $i++;
            $username = $username . '_' . $i;

            $usernameCheckQuery = "SELECT username
                                   FROM users
                                   WHERE (username='$username')";

            $usernameCheck = $con->query($usernameCheckQuery);
        }

        // ----------------------------------------------------- Default Values

        $date        = date('Y.m.d');
        $profilePic  = './view/images/users/default.jpg';
        $numPosts    = $numLikes = '0';
        $friendArray = ',';
        $userClosed  = 'no';
    
        // ------------------------------------------------------ Hash Password

        $password = password_hash($password, PASSWORD_DEFAULT);

        // --------------------------------------------------------- Add to BDD

        $firstName   = $con->real_escape_string($firstName);
        $lastName    = $con->real_escape_string($lastName);
        $username    = $con->real_escape_string($username);
        $email       = $con->real_escape_string($email);
        $password    = $con->real_escape_string($password);
        $date        = $con->real_escape_string($date);
        $profilePic  = $con->real_escape_string($profilePic);
        $numPosts    = $con->real_escape_string($numPosts);
        $numLikes    = $con->real_escape_string($numLikes);
        $friendArray = $con->real_escape_string($friendArray);
        $userClosed  = $con->real_escape_string($userClosed);

        $addToDatabaseQuery = "INSERT INTO users
                               VALUES (0, '$firstName', '$lastName',
                                       '$email', '$username', '$password',
                                       '$date', '$profilePic', '$numPosts', 
                                       '$numLikes', '$friendArray',
                                       '$userClosed')";

        $addToDatabase = $con->query($addToDatabaseQuery);

        $successQuery = "
            <div class='alert alert-success text-center'
                 role='alert' style='margin-bottom: 3.804vh;'>

                <i class='ti-check mr-2'></i>

                <strong>
                    Success! Your account has been created.
                    <a href='login.php'>Log in</a> now !
                </strong>
            </div>
        ";

        $_SESSION['first_name'] = NULL;
        $_SESSION['last_name']  = NULL;
        $_SESSION['email']      = NULL;
    }
}

function filter_data($data) {
    $data = trim($data);
    $data = stripslashes($data);
    $data = htmlspecialchars($data);
    $data = str_replace(' ', '', $data);

    return $data;
}

function upper_lower($data) {
    $data = ucfirst(strtolower($data));

    return $data;
}

?>
