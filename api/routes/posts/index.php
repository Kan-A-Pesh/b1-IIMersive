<?php

// Check method
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Get arguments
    $query = $_GET["query"] ?? null;
    $limit = $_GET["limit"] ?? 25;
    $offset = $_GET["offset"] ?? 0;
    $fromUser = $_GET["fromUser"] ?? null;
    $excludeUser = $_GET["excludeUser"] ?? null;
    $replyTo = $_GET["replyTo"] ?? null;
    $hasMedia = $_GET["hasMedia"] ?? null;

    // Check arguments
    // query
    if ($query !== null && !is_string($query))
        Response::error(400, "Invalid query");

    // limit & offset
    if (!is_numeric($limit) || !is_numeric($offset))
        Response::error(400, "Invalid limit or offset");

    if ($limit < 1 || $limit > 25)
        Response::error(400, "Invalid limit");

    if ($offset < 0)
        Response::error(400, "Invalid offset");

    // fromUser
    $fromUserArray = [];
    if ($fromUser !== null) {
        if (!is_string($fromUser))
            Response::error(400, "Invalid fromUser");

        if (strpos($fromUser, ",") !== false) {
            $fromUser = explode(",", $fromUser);
            foreach ($fromUser as $user) {
                if (!is_string($user))
                    Response::error(400, "Invalid fromUser");
            }
        } else
            $fromUser = [$fromUser];

        foreach ($fromUser as $user) {
            $user = User::get($user);
            if ($user === 500)
                Response::error();
            if ($user === 404)
                Response::error(404, "User not found");

            $fromUserArray[] = $user;
        }
    }

    // excludeUser
    $excludeUserArray = [];
    if ($excludeUser !== null) {
        if (!is_string($excludeUser))
            Response::error(400, "Invalid excludeUser");

        if (strpos($excludeUser, ",") !== false) {
            $excludeUser = explode(",", $excludeUser);
            foreach ($excludeUser as $user) {
                if (!is_string($user))
                    Response::error(400, "Invalid excludeUser");
            }
        } else
            $excludeUser = [$excludeUser];

        foreach ($excludeUser as $user) {
            $user = User::get($user);
            if ($user === 500)
                Response::error();
            if ($user === 404)
                Response::error(404, "User not found");

            $excludeUserArray[] = $user;
        }
    }

    // replyTo
    if ($replyTo !== null) {
        if (!is_string($replyTo))
            Response::error(400, "Invalid replyTo");

        $replyToPost = Post::get($replyTo);
        if ($replyToPost === 500)
            Response::error();
        if ($replyToPost === 404)
            Response::error(404, "Post not found");
    }

    // hasMedia
    if ($hasMedia !== null && !is_bool($hasMedia))
        Response::error(400, "Invalid hasMedia");


    // Get posts
    $posts = Post::get_all(
        $query,
        $fromUserArray,
        $excludeUserArray,
        $replyToPost ?? null,
        $hasMedia,
        $limit,
        $offset
    );

    if ($posts === 500)
        Response::error();

    // Return posts
    Response::success(200, "Posts retrieved", $posts);
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if user is already authenticated
    if ($_AUTH === null)
        Response::error(401, "User not authenticated");

    // Get arguments
    $tag = $_POST["tag"] ?? null;
    $content = $_POST["content"] ?? null;
    $replyTo = $_POST["replyTo"] ?? null;

    $countfiles = count($_FILES['files']['name']);
    $media = null;
    if ($countfiles > 0) {
        $media = $_FILES['files'];
    }

    // Check arguments
    // tag
    if (!is_numeric($tag) || $tag < 0 || $tag >= 10)
        Response::error(400, "Invalid tag");

    // content
    if ($content === null)
        Response::error(400, "Missing content");

    if (!is_string($content))
        Response::error(400, "Invalid content");

    // replyTo
    if ($replyTo !== null) {
        if (!is_string($replyTo))
            Response::error(400, "Invalid replyTo");

        $replyToPost = Post::get($replyTo);
        if ($replyToPost === 500)
            Response::error();
        if ($replyToPost === 404)
            Response::error(404, "Post not found");
    }

    // media
    if ($media !== null) {
        if (!is_array($media))
            Response::error(400, "Invalid media");

        if ($countfiles > 4)
            Response::error(400, "Too many files");

        foreach ($media["name"] as $key => $name) {
            if (!is_string($name))
                Response::error(400, "Invalid media name");
            if (!is_string($media["type"][$key]))
                Response::error(400, "Invalid media type");
            if (!is_string($media["tmp_name"][$key]))
                Response::error(400, "Invalid media tmp_name");
            if (!is_numeric($media["size"][$key]))
                Response::error(400, "Invalid media size");
            if (!is_numeric($media["error"][$key]))
                Response::error(400, "Invalid media error");

            if ($media["size"][$key] > 2000000) // 2MB
                Response::error(400, "File too large");

            $fileType = explode("/", $media["type"][$key]);
            if ($fileType[0] !== "image" && $fileType[0] !== "video" && $fileType[0] !== "audio")
                Response::error(400, "Invalid media type");

            $validExtensions = ["png", "jpg", "jpeg", "gif", "mp4", "webm", "ogg", "mp3", "wav"];
            if (!in_array($fileType[1], $validExtensions))
                Response::error(400, "Invalid media extension");
        }
    }

    // Upload media
    $mediaPaths = [];
    if ($media !== null) {
        foreach ($media["name"] as $key => $name) {
            $fileType = explode("/", $media["type"][$key]);
            $fileExtension = $fileType[1];

            // Generate snowflake
            $fileSnowflake = MediaSnowflake::generate($fileExtension);

            // Upload file
            if (!move_uploaded_file($media["tmp_name"][$key], $$fileSnowflake->toFile()))
                Response::error(500, "Failed to upload media");

            // Clean up
            chmod($filePath, 0644);
            // TODO: Remove metadata
            // TODO: Resize image (if needed)
            // TODO: Compress file

            $mediaPaths[] = $fileSnowflake->toString();
        }
    }

    // Create post
    $post = Post::create($_AUTH["user"], $tag, $content, $mediaPaths, $replyToPost ?? null);

    if ($post === 500)
        Response::error();

    // Return post
    Response::success(201, "Post created", $post);
}
