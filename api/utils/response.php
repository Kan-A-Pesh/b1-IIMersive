<?php

class Response
{

    /**
     * Returns a JSON success response and exits the script
     *
     * @param integer $code HTTP status code
     * @param string $message Message to be displayed
     * @param mixed $payload Payload to be sent
     * @return void
     */
    public static function success($code = 200, $message = "OK", $payload = null)
    {
        $response = array(
            "success" => true,
            "code" => $code,
            "message" => $message,
            "payload" => $payload
        );

        // Set the response code (204 is a special case)
        if ($code !== 204)
            http_response_code($code);

        // Set the content type
        header("Content-Type: application/json");

        // Send the response
        echo json_encode($response);

        // Exit the script
        die();
    }

    /**
     * Returns a JSON error response and exits the script
     *
     * @param integer $code HTTP status code
     * @param string $message Message to be displayed
     * @return void
     */
    public static function error($code = 500, $message = "Internal Server Error")
    {
        $response = array(
            "success" => false,
            "code" => $code,
            "message" => $message,
            "payload" => null
        );

        // Set the response code
        http_response_code($code);

        // Set the content type
        header("Content-Type: application/json");

        // Send the response
        echo json_encode($response);

        // Exit the script
        die();
    }
}
