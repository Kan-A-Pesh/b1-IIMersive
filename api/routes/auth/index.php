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

    if (!Cypher::verify($_POST["password"], $user->get_password_hash()))
        Response::error(401, "Incorrect handle or password");

    // Create session
    $userHandle = $user->handle;
    $ip = $_SERVER["REMOTE_ADDR"];
    $userAgent = $_SERVER["HTTP_USER_AGENT"];
    $remember_me = isset($_POST["remember_me"]) && $_POST["remember_me"] == true;

    $session = Session::create($userHandle, $ip, $userAgent, $remember_me);

    if ($session === 500)
        Response::error();

    // Return session token
    Response::success(201, "Session created", [
        "session_id" => $session->getId(),
        "expires" => $session->expires_at,
        "user_handle" => $userHandle
    ]);
} else if ($_SERVER["REQUEST_METHOD"] === "PUT") {

    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Check arguments
    $remember_me = isset($_POST["remember_me"]) && $_POST["remember_me"] == true;

    // Update session
    Session::update($_AUTH["session"]->getId(), $remember_me);

    Response::success(200, "Session updated", [
        "expires" => $_AUTH["session"]->expires_at,
    ]);
} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Delete session
    Session::delete($_AUTH["session"]->getId());

    Response::success(204, "Session deleted");
} else if ($_SERVER["REQUEST_METHOD"] === "GET") {

    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    Response::success(200, "User authenticated", [
        "session_id" => $_AUTH["session"]->getId(),
        "expires" => $_AUTH["session"]->expires_at,
        "user_handle" => $_AUTH["user"]->handle
    ]);
} else {
    Response::error(405, "Method not allowed");
}
