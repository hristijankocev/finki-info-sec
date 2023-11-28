<?php
include 'service/auth.php';

session_start();

if (isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {
    header('Location: index.php');
}

$requestMethod = $_SERVER['REQUEST_METHOD'];

if ($requestMethod === 'GET') {
    # CSRF prevention token
    $_SESSION['token'] = md5(uniqid(mt_rand(), true));
}

$bag = ['errors' => ''];
$username = '';
$email = '';

if (($requestMethod === 'POST') && isset($_POST['submit'])) {
    # Handle the POST request
    register($bag, $username, $email);
}
?>

<html lang="en">
<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Register</title>
</head>
<body style="background-color: #f8fff4">
<div>
    <div style="float: right">
        <a href="login.php">
            <button type="submit">
                Login
            </button>
        </a>
    </div>

    <form method="post" action="<?php echo $_SERVER['PHP_SELF'] ?>"
          style="margin: 0 auto">

        <input type="hidden" name="token" value="<?php echo $_SESSION['token'] ?? '' ?>">

        <div style="margin: 5px 0">
            <label for="username">Username:</label>
            <input type="text" name="username" id="username"
                   value="<?php echo htmlspecialchars($username) ?>">
        </div>

        <div style="margin: 5px 0">
            <label for="email">Email:</label>
            <input type="email" name="email" id="email"
                   value="<?php echo htmlspecialchars($email) ?>">
        </div>

        <div style="margin: 5px 0">
            <label for="password">Password:</label>
            <input type="password" name="password" id="password">
        </div>

        <div style="margin: 5px 0">
            <label for="repeatPassword">Repeat password:</label>
            <input type="password" name="repeatPassword" id="repeatPassword">
        </div>

        <div>
            <input type="submit" name="submit" value="Register">
        </div>
    </form>

    <?php if (strlen($bag['errors'])): ?>
        <div style="margin: 10px 0 0 0 ; color: red">
            <?php echo htmlspecialchars($bag['errors']) ?>
        </div>
    <?php endif; ?>
</div>
</body>
</html>