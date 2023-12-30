<?php

namespace service;

function register(&$bag, &$username, &$email): void
{
    verifyToken();

    # Establish DB connection
    include_once('repository/db-conn.php');

    # Sanitize user input before using it in queries
    $username = pg_escape_string($conn, $_POST['username']);
    $email = pg_escape_string($conn, $_POST['email']);
    $password = pg_escape_string($conn, $_POST['password']);
    $repeatPassword = pg_escape_string($conn, $_POST['repeatPassword']);

    if ($username !== '' && $password !== '' && $repeatPassword !== '' && $email !== '') {
        if ($password === $repeatPassword) {
            if (isPasswordInputValid($password)) {
                # Check if the username/email already exist
                if (checkIfUsernameExists($conn, $username)) {
                    $bag['errors'] = 'Username already taken';
                } else if (checkIfEmailExists($conn, $email)) {
                    $bag['errors'] = 'Email already taken';
                } else {
                    # Prepare the SQL statement, sanitize it before execution
                    pg_prepare($conn, "get_user",
                        'INSERT INTO USERS (username, password, email) VALUES ($1, $2, $3)');

                    $result = pg_execute($conn, "get_user",
                        array($username, password_hash($password, PASSWORD_DEFAULT), $email));
                    if ($result) {
                        header('Location: index.php');
                    }
                }
            } else {
                $bag['errors'] = 'Password length must be at least 8 characters, contain uppercase & lowercase 
                and a special character.';
            }

        } else {
            $bag['errors'] = 'Passwords need to match';
        }
    } else {
        $bag['errors'] = 'Please fill in all the fields';
    }
}

function checkIfUsernameExists($conn, $value): bool
{
    pg_prepare($conn, "username_exists",
        'SELECT * FROM users WHERE username = $1;');

    $result = pg_execute($conn, "username_exists", array($value));

    if ($result) {
        $user = pg_fetch_assoc($result);
        if (isset($user['username'])) {
            return true;
        }
        return false;
    }
    return false;
}

function checkIfEmailExists($conn, $value): bool
{
    pg_prepare($conn, "email_exists",
        'SELECT * FROM users WHERE email = $1;');

    $result = pg_execute($conn, "email_exists", array($value));

    if ($result) {
        $user = pg_fetch_assoc($result);
        if (isset($user['email'])) {
            return true;
        }
        return false;
    }
    return false;
}

function login(&$bag, &$username): void
{
    verifyToken();

    # Establish DB connection
    include_once('repository/db-conn.php');

    # Sanitize user input before using it in queries
    $username = pg_escape_string($conn, $_POST['username']);
    $password = pg_escape_string($conn, $_POST['password']);

    if ($username !== '' && $password !== '') {
        # Prepare the SQL statement, sanitize it before execution
        pg_prepare($conn, "get_user", 'SELECT * FROM users WHERE username = $1');

        $result = pg_execute($conn, "get_user", array($username));
        if ($result) {
            $user = pg_fetch_assoc($result);

            if ($user && password_verify($password, $user['password'])) {
                # User found and correct credentials
                $_SESSION['isLoggedIn'] = true;
                $_SESSION['username'] = htmlspecialchars($username);
                $_SESSION['role'] = $user['role'];

                header('Location: index.php');
            } else {
                $bag['errors'] = 'The login information you entered did not match our records. Please double-check and try again.';
            }
        } else {
            echo "error: " . $conn->error;
        }
    } else {
        $bag['errors'] = 'Please fill in your credentials';
    }
}

function logout(): void
{
    verifyToken();

    setcookie(session_name(), '', 100);
    session_unset();
    $_SESSION = array();
    session_destroy();
}

function verifyToken(): void
{
    $token = htmlspecialchars($_POST['token']);

    if (!$token || $token !== $_SESSION['token']) {
        // Return 405 http status code
        header($_SERVER['SERVER_PROTOCOL'] . ' 405 Method Not Allowed');
        exit;
    }
}

function isPasswordInputValid($password): bool
{
    $uppercase = preg_match('@[A-Z]@', $password);
    $lowercase = preg_match('@[a-z]@', $password);
    $number = preg_match('@\d@', $password);
    $specialChars = preg_match('@[^\w]@', $password);

    return $uppercase and $lowercase and $number and $specialChars;
}