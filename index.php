<?php

use function service\getRole;
use function service\isAdmin;
use function service\otpStatus;

session_start();

require 'vendor/autoload.php';
require 'service/user.php';

use PragmaRX\Google2FA\Google2FA;
use BaconQrCode\Renderer\ImageRenderer;
use BaconQrCode\Renderer\Image\ImagickImageBackEnd;
use BaconQrCode\Renderer\RendererStyle\RendererStyle;
use BaconQrCode\Writer;

if (otpStatus() === 'disabled') {
    $gfa = new Google2FA();
    $otp_secret = $gfa->generateSecretKey();

    $qrCodeUrl = $gfa->getQRCodeUrl(
        'MMF-192029',
        $_SESSION['username'],
        $otp_secret
    );

    $renderer = new ImageRenderer(
        new RendererStyle(250),
        new ImagickImageBackEnd()
    );
    $writer = new Writer($renderer);

    $encoded_qr_data = base64_encode($writer->writeString($qrCodeUrl));
}


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
            <th>2FA Status</th>
        </tr>
        <tr>
            <td><?= getRole() ?></td>
            <td>
                <?= otpStatus() ?>
                <?php if (otpStatus() === 'enabled'): ?>
                    <input type="hidden" name="token" id="token" value="<?php echo $_SESSION['token'] ?? '' ?>">
                    <button style="border: none; color: blue; cursor: pointer" onclick="removeOTP()">
                        <u>(remove 2FA)</u>
                    </button>
                    <script>
                        function removeOTP() {
                            // Get the OTP values
                            let token = document.getElementById('token').value;

                            let data = {
                                token: token
                            }

                            console.log(data);

                            // Send a POST request using the fetch API
                            fetch('/otp-remove.php', {
                                method: 'POST',
                                headers: {
                                    'Content-Type': 'application/json',
                                },
                                body: JSON.stringify(data)
                            })
                                .then(response => response.json())
                                .then(data => {
                                    console.log('Success:', data);
                                    alert(data['message']);
                                    window.location.reload()
                                })
                                .catch(error => {
                                    console.error('Error:', error);
                                    alert('Removing 2FA failed :(')
                                });
                        }
                    </script>
                <?php endif; ?>
            </td>
        </tr>
    </table>

    <?php if (otpStatus() === 'disabled'): ?>
        <div style="text-align: center">
            <p>Want to set up 2FA? Scan the code and enter the 6-digit code to verify it.</p>
            <p>
                <img src="data:image/png;base64,<?= $encoded_qr_data ?>" alt="QR-Code">
            </p>
            <label>
                OTP Token:
                <input type="number" name="otp_token" id="otp_token">
            </label>
            <input type="hidden" name="otp_secret" id="otp_secret" value="<?= $otp_secret ?>">
            <p>
                <button type="submit" onclick="sendPostRequest()">Verify</button>
            </p>
        </div>
        <br>

        <script>
            function sendPostRequest() {
                // Get the OTP values
                let otpToken = document.getElementById('otp_token').value;
                let otpSecret = document.getElementById('otp_secret').value;

                // Prepare the data to be sent in the POST request
                let data = {
                    otp_token: otpToken,
                    otp_secret: otpSecret
                };

                // Send a POST request using the fetch API
                fetch('/otp-setup.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify(data)
                })
                    .then(response => response.json())
                    .then(data => {
                        console.log('Success:', data);
                        alert(data['message']);
                        window.location.reload()
                    })
                    .catch(error => {
                        console.error('Error:', error);
                        alert('Setting up 2FA failed :(')
                    });
            }
        </script>
    <?php endif; ?>
    </body>
    </html>
<?php else: ?>
    <?php header('Location: login.php'); ?>
<?php endif; ?>
