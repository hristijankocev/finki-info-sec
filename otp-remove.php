<?php
require 'service/user.php';
require 'service/authentication.php';

use function service\setOtp;
use function service\verifyToken;

session_start();

if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_SESSION['isLoggedIn']) && $_SESSION['isLoggedIn'] === true) {

    try {
        // Get the JSON data from the request body
        $json_data = file_get_contents('php://input');

        // Decode the JSON data
        $data = json_decode($json_data, true, 512, JSON_THROW_ON_ERROR);

        $tokenInput = $data['token'];

        $_POST['token'] = $tokenInput;

        verifyToken();

        if (!isset($_SESSION['otp_secret'])) {
            $response = array('message' => "2FA was not enabled in the first place");
            echo json_encode($response, JSON_THROW_ON_ERROR);
            die();
        }

        setOtp($_SESSION['username'], null);

        unset($_SESSION['otp_secret']);

        $response = array('message' => "Successfully removed 2FA");
        echo json_encode($response, JSON_THROW_ON_ERROR);

        die();
    } catch (JsonException $e) {
        $response = array('message' => "Oops... $e");
        /** @noinspection PhpUnhandledExceptionInspection */
        echo json_encode($response, JSON_THROW_ON_ERROR);
    }
}