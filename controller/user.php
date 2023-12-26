<?php

namespace controllers;

use JsonException;
use function service\setRoleSvc;

function setRole(): void
{
    try {
        // Get the JSON data from the request body
        $json_data = file_get_contents('php://input');

        // Decode the JSON data
        $data = json_decode($json_data, true, 512, JSON_THROW_ON_ERROR);

        // Check if the 'role' key exists in the decoded data
        if (isset($data['role'], $data['user'])) {
            // Do something with the role value
            $selectedRole = $data['role'];
            $forUser = $data['user'];
            $loggedInUser = $_SESSION['username'];

            // For demonstration purposes, you can create an associative array and encode it as JSON
            $response = array('message' => "Selected Role: $selectedRole, User: $forUser, Logged in: $loggedInUser");

            if ($forUser === $loggedInUser) {
                $response = array('message' => 'Cannot change your own role!');
                echo json_encode($response, JSON_THROW_ON_ERROR);
                die();
            }

            setRoleSvc($forUser, $selectedRole);
        } else {
            // Handle the case where 'role' key is not present
            http_response_code(400); // Bad Request
            $response = array('error' => "Missing 'role' or 'user' in the request data");
        }
        echo json_encode($response, JSON_THROW_ON_ERROR);
    } catch (JsonException $e) {
        http_response_code(400); // Bad Request
        $response = array('error' => "JSON exception: $e");
        /** @noinspection JsonEncodingApiUsageInspection */
        echo json_encode($response);
    } finally {
        die();
    }
}