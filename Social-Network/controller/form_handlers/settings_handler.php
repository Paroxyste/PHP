<?php

$errFirstName   = NULL;
$errLastName    = NULL;
$errEmail       = NULL;
$errPassword    = NULL;
$errNewPassword = NULL;

$refresh = 'settings.php';

// --------------------------------------------------------------- User details

if (
    isset($_POST['set_user_details'])
) {
    // Filter email
    $email = filter_data(
                filter_var($_POST['set_email'], FILTER_SANITIZE_EMAIL)
            );

    // Empty email
    if (
        empty($email)
    ) {
        $errEmail = 'This field is required';
    }

    if (
        filter_var($email, FILTER_VALIDATE_EMAIL)
    ) {
        // Email length
        if (
            strlen($email) < 10
            || strlen($email) > 100
        ) {
            $errEmail = 'Your email must be between 10 & 100 characters';
        }

        // Email Regex
        if (
            !preg_match(
                "/^[0-9a-zA-Z._-]+@[0-9a-zA-Z._-]+\.[a-z]{2,4}$/", $email
            )
        ) {
            $errEmail = 'The characters allowed are : [0-9 a-z A-Z ._-]';
        }

        // Check if email already use
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

        // Check error & add to DB
        if (
            empty($errEmail)
        ) {
            $email = $con->real_escape_string($email);

            $bddUpdateQuery = "UPDATE users
                               SET email='$email'
                               WHERE (username='$userLoggedIn')";

            $bddUpdate = $con->query($bddUpdateQuery);

            echo "
                <script>
                    location.href='". strip_tags($refresh) ."';
                </script>
            ";
        }
    }
} 

// ----------------------------------------------------------- Password details

if (
    isset($_POST['set_pass_details'])
) {
    // Password & New Password
    $password    = $_POST['set_password'];
    $newPassword = $_POST['set_newpassword'];

    // Empty password
    if (
        empty($password)
    ) {
        $errPassword = 'This field is required';
    }

    // Empty new password
    if (
        empty($newPassword)
    ) {
        $errNewPassword = 'This field is required';
    }

    // Password length
    if (
        strlen($password) < 8
    ) {
        $errPassword = 'Your password must have at least 8 characters';
    }

    // New password legth
    if (
        strlen($newPassword) < 8
    ) {
        $errNewPassword = 'Your password must have at least 8 characters';
    }

    // Check if password are identical
    if (
        $password == $newPassword
    ) {
        $errPassword    = 'Your passwords must not be identical';
        $errNewPassword = 'Your passwords must not be identical';
    }

    // Check DB password
    $checkDatabaseQuery = "SELECT password
                           FROM users
                           WHERE (username='$userLoggedIn')";

    $checkDatabase = $con->query($checkDatabaseQuery);

    $row = $checkDatabase->fetch_assoc();

    // Get password
    $hashedPassword = $row['password'];
    $passwordVerify = password_verify($password, $hashedPassword);

    // Check password with DB password
    if (
        $password != $passwordVerify
    ) {
        $errPassword = 'The password is incorrect';
    }

    // Check error & add to DB
    if (
        empty($errPassword)
        && empty($errNewPassword)
    ) {
        // Hash new password
        $newPassword = password_hash($newPassword, PASSWORD_DEFAULT);

        // Update Bdd
        $newPassword = $con->real_escape_string($newPassword);

        $bddUpdateQuery = "UPDATE users
                           SET password='$newPassword'
                           WHERE (username='$userLoggedIn')";

        $bddUpdate = $con->query($bddUpdateQuery);

        echo "
            <script>
                location.href='". strip_tags($refresh) ."';
            </script>
        ";
    }
}

// ---------------------------------------------------------- Personnal Details

if (
    isset($_POST['set_perso_details'])
) {
    // Get firstName
    $firstName = filter_data(
                    filter_var($_POST['set_firstname'], FILTER_SANITIZE_STRING)
                );

    $firstName = upper_lower($firstName);

    // Get lastName
    $lastName = filter_data(
                    filter_var($_POST['set_lastname'], FILTER_SANITIZE_STRING)
                );
    
    $lastName = upper_lower($lastName);    

    // Empty firstName
    if (
        empty($firstName)
    ) {
        $errFirstName = 'This field is required';
    }

    // Empty last name
    if (
        empty($lastName)
    ) {
        $errLastName = 'This field is required';
    }


    // First name length
    if (
        strlen($firstName) < 2
        || strlen($firstName) > 20
    ) {
        $errFirstName = 'Your first name must be between 2 & 20 characters';
    }

    // Last name length
    if (
        strlen($lastName) < 2
        || strlen($lastName) > 20
    ) {
        $errLastName = 'Your last name must be between 2 & 20 characters';
    }

    // First name regex
    if (
        !preg_match("/^[a-zA-Z -]+$/", $firstName)
    ) {
        $errFirstName = 'The characters allowed are : [a-z A-Z -]';
    }

    // Last name regex
    if (
        !preg_match("/^[a-zA-Z -]+$/", $lastName)
    ) {
        $errLastName = 'The characters allowed are : [a-z A-Z -]';
    }

    // Check DB users
    $bddCheckQuery = "SELECT username
                      FROM users
                      WHERE (username='$userLoggedIn')";

    $bddCheck = $con->query($bddCheckQuery);

    $row = $bddCheck->fetch_assoc();

    $matchedUser = $row['username'];

    // Check error & add to DB
    if (
        $matchedUser == NULL
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

        echo "
            <script>
                location.href='". strip_tags($refresh) ."';
            </script>
        ";
    }
}

// ---------------------------------------------------------------- Data filter

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
