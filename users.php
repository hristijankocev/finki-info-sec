<?php

use function service\getUsers;
use function service\isAdmin;

session_start();

include 'service/authorization.php';
include 'service/user.php';
include 'controller/user.php';

if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true):
    if (!isAdmin()) {
        http_response_code(403);
        die();
    }

    // Check if the request method is POST
    if ($_SERVER['REQUEST_METHOD'] === 'POST') {
        controllers\setRole();
    }

    $users_result = getUsers();

    $roles = Roles::cases();
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
                <td>
                    <select name="role" id="role-<?= $row['username'] ?>"
                            onchange="sendPostRequest('role-<?= $row['username'] ?>')"
                            data-user="<?= $row['username'] ?>">
                        <?php foreach ($roles as $role): ?>
                            <option value="<?= $role->value ?>"
                                <?= strcasecmp($row['role'], $role->value) ? '' : 'selected' ?>>
                                <?= $role->name ?>
                            </option>
                        <?php endforeach; ?>
                    </select>
                </td>
            </tr>
        <?php endwhile; ?>
    </table>

    <script>
        function sendPostRequest(selectId) {
            // Get the selected value from the select element
            let selectedRole = document.getElementById(selectId).value;
            let forUser = document.getElementById(selectId).dataset.user;

            // Prepare the data to be sent in the POST request
            let data = {
                role: selectedRole,
                user: forUser
            };

            console.log(data);

            // Send a POST request using the fetch API
            fetch('<?= $_SERVER['PHP_SELF'] ?>', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify(data)
            })
                .then(response => response.json())
                .then(data => {
                    console.log('Success:', data);
                })
                .catch(error => {
                    console.error('Error:', error);
                    // Handle errors
                });
        }
    </script>
    </body>
    </html>
<?php else: header('Location: /login.php'); ?>
<?php endif; ?>


