<?php

declare(strict_types=1);

$firstName   = '';
$lastName    = '';
$email       = '';
$password    = '';
$newPassword = '';

$errFirstName   = '';
$errLastName    = '';
$errEmail       = '';
$errPassword    = '';
$errNewPassword = '';

$successQuery  = '';

// --------------------------------------------------------------- User details

if (
    isset($_POST['set_user_details'])
) {
    // Email
    $email = filter_data(
                filter_var($_POST['set_email'], FILTER_SANITIZE_EMAIL)
             );

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

        if (
            empty($_POST['set_password'])
            && empty($_POST['set_newpassword'])
            && empty($errEmail)
        ) {
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

            $email = $con->real_escape_string($email);

            $bddUpdateQuery = "UPDATE users
                               SET email='$email'
                               WHERE (username='$userLoggedIn')";

            $bddUpdate = $con->query($bddUpdateQuery);
        }
    } else {
        // Password
        $password = strip_tags($_POST['set_password']);

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

        $newPassword = strip_tags($_POST['set_newpassword']);

        if (
            empty($newPassword)
        ) {
            $errNewPassword = 'This field is required';
        }

        if (
            strlen($newPassword) < 8
        ) {
            $errNewPassword = 'Your password must have at least 8 characters';
        }

        $checkDatabaseQuery = "SELECT *
                               FROM users
                               WHERE (email='$email')";

        $checkDatabase = $con->query($checkDatabaseQuery);

        $checkLogin = $checkDatabase->num_rows;

        $row = $checkDatabase->fetch_assoc();

        $hashedPassword = $row['password'];
        $passwordVerify = password_verify($password, $hashedPassword);

        if (
            $password != $passwordVerify
        ) {
            $errPassword = 'The password is incorrect';
        }

        if (
            $password = $passwordVerify
            && $newPassword = $passwordVerify
        ) {
            $errNewPassword = 'The new password is identical to the old one';
        }

        if (
            $password = $passwordVerify
            && $newPassword != $passwordVerify
        ) {
            // Hash new password
            $newPassword = password_hash($password, PASSWORD_DEFAULT);

            // Update Bdd
            $email = $con->real_escape_string($email);
            $newPassword = $con->real_escape_string($newPassword);

            $bddUpdateQuery = "UPDATE users
                               SET email='$email', password='$newPassword'
                               WHERE (username='$userLoggedIn')";

            $bddUpdate = $con->query($bddUpdateQuery);

        }
    } // else { password }
} // End User Details

// ---------------------------------------------------------- Personnal Details

if (
    isset($_POST['set_perso_details'])
) {
    $firstName = filter_data(
                    filter_var($_POST['set_firstname'], FILTER_SANITIZE_STRING)
                );

    $firstName = upper_lower($firstName);

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

    $lastName = filter_input(
                    filter_var($_POST['set_lastname'], FILTER_SANITIZE_STRING)
                );
    
    $lastName = upper_lower($lastName);

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

    $bddCheckQuery = "SELECT username
                      FROM users
                      WHERE (username='$userLoggedIn')";

    $bddCheck = $con->query($bddCheckQuery);

    $row = $bddCheck->fetch_assoc();

    $matchedUser = $row['username'];

    if (
        $matchedUser == ''
        || $matchedUser == $userLoggedIn
        && empty($errFirstName)
        && empty($errLastName)
    ) {
        $firstName = $con->real_escape_string($firstName);
        $lastName  = $con->real_escape_string($lastName);

        $bddUpdateQuery = "UPDATE users
                           SET first_name='$firstName', last_name='$lastName'
                           WHERE (username='$userLoggedIn')";

        $bddUpdate = $con->query($bddUpdateQuery);
    }
} // End Personnal Details

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
