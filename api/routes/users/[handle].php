<?php
$handle = $_GET["handle"];

// Check method
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Get user
    $user = User::get($handle);

    if ($user === 500)
        Response::error();

    if ($user === 404)
        Response::error(404, "User not found");

    // Return user
    Response::success(200, "User retrieved", $user);
} else if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    // Check if user is already authenticated
    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Check if user is authenticated user
    if ($_AUTH["user"]->handle !== $handle)
        Response::error(403, "User not authorized");

    // Validate arguments
    // Email (regex: email)
    if (isset($_POST["email"]) && !filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
        Response::error(400, "Invalid email");

    // TODO: Validate avatar and banner for upload

    // Update user
    $user = User::update(
        $handle,
        $_POST["name"] ?? null,
        $_POST["email"] ?? null,
        $_POST["biography"] ?? null,
        null, // TODO: $_POST["avatar"] ?? null,
        null  // TODO: $_POST["banner"] ?? null
    );

    if ($user === 404)
        Response::error(404, "User not found");

    if ($user === 500)
        Response::error();

    // Return user
    Response::success(200, "User updated", $user);
} else {
    Response::error(405, "Method not allowed");
}
