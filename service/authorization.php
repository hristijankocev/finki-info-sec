<?php
include 'model/enum/Roles.php';


function getRole(): string
{
    include('repository/db-conn.php');

    # Establish DB connection

    $username = pg_escape_string($conn, $_SESSION['username']);

    pg_prepare($conn, "get_user_role", 'SELECT role FROM USERS WHERE username = $1');

    $result = pg_execute($conn, 'get_user_role', array($username));
    $resultAssoc = pg_fetch_assoc($result);

    return $resultAssoc['role'];
}

function isAdmin(): bool
{
    return getRole() === Roles::ADMINISTRATOR->value;
}

function isRegular(): bool
{
    return getRole() === Roles::REGULAR->value;
}