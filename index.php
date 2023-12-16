<?php
session_start();

if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true):?>
    <?php include 'service/authorization.php' ?>
    <html lang="en">
    <link rel="stylesheet" href="stylesheet.css">
    <body>
    <div style="right: 0; position:absolute; padding: 0 10px">
        <form action="logout.php" method="post">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">

            <button type="submit">
                Logout
            </button>
        </form>
    </div>
    <p>Welcome aboard, <?php echo $_SESSION['username'] ?>!</p>
    <hr>
    <table style="width: 100%; text-align: center">
        <tr>
            <th>Role</th>
            <th>IsAdmin</th>
            <th>IsRegular</th>
        </tr>
        <tr>
            <td><?php echo getRole() ?></td>
            <td><?php echo isAdmin() ? 'yes' : 'no' ?></td>
            <td><?php echo isRegular() ? 'yes' : 'no' ?></td>
        </tr>
    </table>
    <br>
    </body>
    </html>
<?php else: ?>
    <?php header('Location: login.php'); ?>
<?php endif; ?>
