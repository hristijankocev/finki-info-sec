<?php
session_start();

if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true):?>
    Welcome aboard, <?php echo $_SESSION['username'] ?>!
    <html lang="en">
    <body style="background-color: #f8fff4">
    <div style="float: right">
        <form action="logout.php" method="post">
            <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">

            <button type="submit">
                Logout
            </button>
        </form>
    </div>
    </body>
    </html>
<?php else: ?>
    <?php header('Location: login.php'); ?>
<?php endif; ?>
