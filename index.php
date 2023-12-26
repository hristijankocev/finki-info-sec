<?php

use function service\getRole;
use function service\isAdmin;
use function service\isModerator;
use function service\isRegular;

session_start();

if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true):?>
    <?php
    include 'service/authorization.php' ?>
    <html lang="en">
    <link rel="stylesheet" href="stylesheet.css">
    <body>
    <div class="navbar">
        <a href="/index.php">Home</a>
        <?php if (isAdmin()): ?>
            <a href="/users.php">Users</a>
        <?php endif; ?>
        <form action="logout.php" method="post" class="logout-form">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">

            <button type="submit" class="logout-button">
                Logout
            </button>
        </form>
    </div>

    <p style="font-size: 22px; text-align: center">Welcome aboard, <?php echo $_SESSION['username'] ?>!</p>

    <hr>

    <p style="text-align: center; font-weight: bold">Some info</p>
    <table style="width: 100%; text-align: center">
        <tr>
            <th>Role</th>
            <th>IsRegular</th>
            <th>IsModerator</th>
            <th>IsAdmin</th>
        </tr>
        <tr>
            <td><?php echo getRole() ?></td>
            <td><?php echo isRegular() ? 'yes' : 'no' ?></td>
            <td><?php echo isModerator() ? 'yes' : 'no' ?></td>
            <td><?php echo isAdmin() ? 'yes' : 'no' ?></td>
        </tr>
    </table>
    <br>
    </body>
    </html>
<?php else: ?>
    <?php header('Location: login.php'); ?>
<?php endif; ?>
