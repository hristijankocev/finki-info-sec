<?php
session_start();

include 'service/authorization.php';
include 'service/user.php';

if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true):
    if (!isAdmin()) {
        http_response_code(403);
        die();
    }

    $users_result = getUsers();
    ?>
    <html lang="en">
    <link rel="stylesheet" href="stylesheet.css">
    <body>
    <table style="width: 100%">
        <tr>
            <th>Username</th>
            <th>Email</th>
            <th>Role</th>
        </tr>

        <?php while ($row = pg_fetch_assoc($users_result)): ?>
            <tr style="text-align: center">
                <td><?= $row['username'] ?></td>
                <td><?= $row['email'] ?></td>
                <td><?= $row['role'] ?></td>
            </tr>
        <?php endwhile; ?>
    </table>
    </body>
    </html>
<?php else: header('Location: /login.php'); ?>
<?php endif; ?>


