<?php

namespace service;

function getUsers()
{
    # Establish DB connection
    include 'repository/db-conn.php';

    if (!isAdmin()) {
        http_response_code(403);
        die();
    }

    pg_prepare($conn, "get_users", 'SELECT username, email, role FROM USERS');

    return pg_execute($conn, "get_users", []);
}

function setRoleSvc($user, $role): void
{
    # Establish DB connection
    include 'repository/db-conn.php';

    if (!isAdmin()) {
        http_response_code(403);
        die();
    }

    pg_prepare($conn, "set_user_role", 'UPDATE users SET  role=$1 WHERE username=$2');
    pg_execute($conn, "set_user_role", [$role, $user]);
}