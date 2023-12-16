<?php
function getUsers(): bool|PgSql\Result
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