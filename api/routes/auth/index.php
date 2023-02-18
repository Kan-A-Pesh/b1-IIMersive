<?php

// Check method
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if user is already authenticated
    if ($_AUTH !== null)
        Response::error(400, "User already authenticated");

    // Check arguments
    if (!isset($_POST["handle"]))
        Response::error(400, "Missing user handle");

    if (!isset($_POST["password"]))
        Response::error(400, "Missing password");

    $user = User::get($_POST["handle"]);

    if ($user === 500)
        Response::error();

    if ($user === 404)
        Response::error(401, "Incorrect handle or password");

    if (!Cypher::verify($_POST["password"], $user->password_hash))
        Response::error(401, "Incorrect handle or password");

    // Create session
    $userHandle = $user->getHandle();
    $ip = $_SERVER["REMOTE_ADDR"];
    $userAgent = $_SERVER["HTTP_USER_AGENT"];

    $session = Session::create($userHandle, $ip, $userAgent);

    if ($session === 500)
        Response::error();

    // Return session token
    Response::success(201, "Session created", [
        "session_id" => $session->getId(),
        "expires" => $session->expires_at->format("Y-m-d H:i:s"),
        "user_handle" => $userHandle
    ]);
} else if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Update session
    Session::update($_AUTH["session"]->getId());
} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Delete session
    Session::delete($_AUTH["session"]->getId());
} else if ($_SERVER["REQUEST_METHOD"] === "GET") {

    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    Response::success(200, "User authenticated", [
        "session_id" => $_AUTH["session"]->getId(),
        "expires" => $_AUTH["session"]->expires_at->format("Y-m-d H:i:s"),
        "user_handle" => $_AUTH["user"]->getHandle()
    ]);
} else {
    Response::error(405, "Method not allowed");
}
