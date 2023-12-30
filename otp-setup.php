<?php
require 'vendor/autoload.php';
require 'service/user.php';

use PragmaRX\Google2FA\Exceptions\IncompatibleWithGoogleAuthenticatorException;
use PragmaRX\Google2FA\Exceptions\InvalidCharactersException;
use PragmaRX\Google2FA\Exceptions\SecretKeyTooShortException;
use PragmaRX\Google2FA\Google2FA;
use function service\setOtp;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {

    // Get the JSON data from the request body
    $json_data = file_get_contents('php://input');

    try {
        // Decode the JSON data
        $data = json_decode($json_data, true, 512, JSON_THROW_ON_ERROR);

        $otpToken = $data['otp_token'];
        $otpSecret = $data['otp_secret'];

        if (isset($otpToken, $otpSecret)) {
            $gfa = new Google2FA();
            $isOtpValid = $gfa->verifyKey($otpSecret, $otpToken);

            if ($isOtpValid) {
                $loggedInUser = $_SESSION['username'];

                setOtp($loggedInUser, $otpSecret);

                $response = array('message' => "Successfully enabled 2FA!");

                $_SESSION['otp_secret'] = $otpSecret;

                echo json_encode($response, JSON_THROW_ON_ERROR);
                die();
            }
            header($_SERVER['SERVER_PROTOCOL'] . ' 401 Unauthorized');
            die();
        }

        header($_SERVER['SERVER_PROTOCOL'] . ' 422 Unprocessable Entity');
        die();

    } catch (JsonException|IncompatibleWithGoogleAuthenticatorException|InvalidCharactersException|SecretKeyTooShortException $e) {
        header($_SERVER['SERVER_PROTOCOL'] . ' 500 Internal Server Error');
        die();
    }
}
