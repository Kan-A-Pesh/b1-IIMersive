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

    if (isset($_POST["avatar"])) {
        $extension = $_POST["avatar"]["extension"];
        $data = $_POST["avatar"]["data"];
        $media = new Media($data, $extension);

        if (!$media->isStaticImage())
            Response::error(400, "Invalid avatar");

        if (strlen($data) > 8000000) // 8MB
            Response::error(400, "Avatar too big");

        // Move image to CDN
        $fileSnowflake = MediaSnowflake::generate($media->extension);
        $filePath = $fileSnowflake->toFile();
        $media->save($filePath);

        $_POST["avatar"] = $fileSnowflake->toString();
    }

    if (isset($_POST["banner"])) {
        $extension = $_POST["banner"]["extension"];
        $data = $_POST["banner"]["data"];
        $media = new Media($data, $extension);

        if (!$media->isStaticImage())
            Response::error(400, "Invalid banner");

        if (strlen($data) > 8000000) // 8MB
            Response::error(400, "Banner too big");

        // Move image to CDN
        $fileSnowflake = MediaSnowflake::generate($media->extension);
        $filePath = $fileSnowflake->toFile();
        $media->save($filePath);

        $_POST["banner"] = $fileSnowflake->toString();
    }

    // Update user
    $user = User::update(
        $handle,
        $_POST["name"] ?? null,
        $_POST["email"] ?? null,
        null, // TODO: $_POST["password"] ?? null,
        $_POST["biography"] ?? null,
        $_POST["avatar"] ?? null,
        $_POST["banner"] ?? null
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
