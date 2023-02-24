<?php
$handle = $_GET["handle"];

// Check method
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Check if user is authenticated
    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Get user
    $user = User::get($handle);

    if ($user === 500)
        Response::error();

    if ($user === 404)
        Response::error(404, "User not found");

    // Check arguments
    $limit = $_GET["limit"] ?? 25;
    $offset = $_GET["offset"] ?? 0;

    if (!is_numeric($limit) || !is_numeric($offset))
        Response::error(400, "Invalid limit or offset");

    if ($limit < 1 || $limit > 25)
        Response::error(400, "Invalid limit");

    if ($offset < 0)
        Response::error(400, "Invalid offset");

    // Get messages
    $messages = Message::list_conversation($handle, $_AUTH["user"]->handle, $limit, $offset);

    if ($messages === 500)
        Response::error();

    // Return messages
    Response::success(200, "Messages retrieved", $messages);
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if user is already authenticated
    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Get user
    $user = User::get($handle);

    if ($user === 500)
        Response::error();

    if ($user === 404)
        Response::error(404, "User not found");

    // Check arguments
    $content = $_POST["content"] ?? null;

    if ($content === null)
        Response::error(400, "Missing content");

    if (strlen($content) > 300)
        Response::error(400, "Content too long");

    // Create message
    $message = Message::send($_AUTH["user"]->handle, $handle, $content);

    if ($message === 500)
        Response::error();

    // Return message
    Response::success(200, "Message sent", $message);
} else {
    Response::error(405, "Method not allowed");
}
