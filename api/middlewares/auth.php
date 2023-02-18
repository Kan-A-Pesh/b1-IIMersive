<?php

$_AUTH = null;
$session_id = $_COOKIE["session_id"] ?? null;

if ($session_id !== null) {
    // Get session
    $session = Session::get($session_id);

    // Error handling
    if (!$session instanceof Session)
        return;

    // Check if session is expired
    if ($session->expires_at < new DateTime()) {
        Session::delete($session->getId());
        return;
    }

    // Security check
    if ($session->ip !== $_SERVER["REMOTE_ADDR"])
        return;

    if ($session->user_agent !== $_SERVER["HTTP_USER_AGENT"])
        return;

    // Get user
    $user = User::get($session->user_handle);

    // Error handling
    if (!$user instanceof User)
        return;

    // Set global variable
    $_AUTH = [
        "session" => $session,
        "user" => $user
    ];
}
