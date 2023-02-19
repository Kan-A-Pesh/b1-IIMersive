<?php

// Check method
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Check if user is already authenticated
    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Get parameters
    $query = $_GET["query"] ?? "";
    $limit = $_GET["limit"] ?? 25;
    $offset = $_GET["offset"] ?? 0;

    // Validate parameters
    if (!is_numeric($limit))
        Response::error(400, "Invalid limit");

    if (!is_numeric($offset))
        Response::error(400, "Invalid offset");

    // Get users
    $users = User::list($query, $limit, $offset);

    if ($users === 500)
        Response::error();

    // Return users
    Response::success(200, "Users retrieved", $users);
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if user is already authenticated
    if ($_AUTH !== null)
        Response::error(400, "User already authenticated");

    // Check arguments
    if (!isset($_POST["handle"]))
        Response::error(400, "Missing user handle");

    if (!isset($_POST["password"]))
        Response::error(400, "Missing password");

    if (!isset($_POST["email"]))
        Response::error(400, "Missing email");

    // Validate arguments
    // Handle (regex: a-z, A-Z, 0-9, _, 1-15 characters)
    if (!preg_match("/^[a-zA-Z0-9_]{1,15}$/", $_POST["handle"]))
        Response::error(400, "Invalid user handle");

    // Password (is string)
    if (!is_string($_POST["password"]))
        Response::error(400, "Invalid password");

    // Email (regex: email)
    if (!filter_var($_POST["email"], FILTER_VALIDATE_EMAIL))
        Response::error(400, "Invalid email");

    // Check if user already exists
    $user = User::get($_POST["handle"]);

    if ($user === 500)
        Response::error();

    if ($user !== 404)
        Response::error(409, "User already exists");

    // Create user
    $password_hash = Cypher::hash($_POST["password"]);
    $user = User::create($_POST["handle"], $password_hash, $_POST["email"]);

    if ($user === 500)
        Response::error();

    // Return user
    Response::success(201, "User created", [
        "user_handle" => $user->getHandle(),
        "email" => $user->email
    ]);
} else {
    Response::error(405, "Method not allowed");
}
