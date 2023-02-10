<?php

/**
 * Returns a JSON success response and exits the script
 *
 * @param integer $code HTTP status code
 * @param string $message Message to be displayed
 * @param mixed $payload Payload to be sent
 * @return void
 */
function success($code = 200, $message = "OK", $payload = null)
{
    $response = array(
        "success" => true,
        "code" => $code,
        "message" => $message,
        "payload" => $payload
    );

    http_response_code($code);
    echo json_encode($response);
    exit();
}

/**
 * Returns a JSON error response and exits the script
 *
 * @param integer $code HTTP status code
 * @param string $message Message to be displayed
 * @return void
 */
function error($code = 500, $message = "Internal Server Error")
{
    $response = array(
        "success" => false,
        "code" => $code,
        "message" => $message,
        "payload" => null
    );

    http_response_code($code);
    echo json_encode($response);
    exit();
}
