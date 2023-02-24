<?php

// Check method
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Check if user is authenticated
    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    $handle = $_AUTH["user"]->handle;
    $limit = $_GET["limit"] ?? 25;
    $offset = $_GET["offset"] ?? 0;

    if (!is_numeric($limit) || !is_numeric($offset))
        Response::error(400, "Invalid limit or offset");

    if ($limit < 1 || $limit > 25)
        Response::error(400, "Invalid limit");

    if ($offset < 0)
        Response::error(400, "Invalid offset");

    // Get latest messages
    $messages = Message::list_latest_for_user($handle, $limit, $offset);

    if ($messages === 500)
        Response::error();

    // Return messages
    Response::success(200, "Messages retrieved", $messages);
} else {
    Response::error(405, "Method not allowed");
}
