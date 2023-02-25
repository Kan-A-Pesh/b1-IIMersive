<?php

// Check method
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    // Check if user is authenticated
    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Get notifications
    $notifications = Notification::pop_all($_AUTH["user"]->handle);

    if ($notifications === 500)
        Response::error();

    // Return notifications
    Response::success(200, "Notifications retrieved", $notifications);
} else {
    Response::error(405, "Method not allowed");
}
