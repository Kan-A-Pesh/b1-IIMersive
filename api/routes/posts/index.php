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
    $fromUserObject = null;
    if ($fromUser !== null) {
        if (!is_string($fromUser))
            Response::error(400, "Invalid fromUser");

        $fromUserObject = User::get($fromUser);
        if ($fromUserObject === 500)
            Response::error();
        if ($fromUserObject === 404)
            Response::error(404, "User not found");
    }

    // excludeUser
    $excludeUserObject = null;
    if ($excludeUser !== null) {
        if (!is_string($excludeUser))
            Response::error(400, "Invalid excludeUser");

        $excludeUserObject = User::get($excludeUser);
        if ($excludeUserObject === 500)
            Response::error();
        if ($excludeUserObject === 404)
            Response::error(404, "User not found");
    }

    // replyTo
    if ($replyTo !== null) {
        if (!is_string($replyTo))
            Response::error(400, "Invalid replyTo");

        if ($replyTo !== "none" && $replyTo !== "any") {
            $replyToPost = Post::get($replyTo);
            if ($replyToPost === 500)
                Response::error();
            if ($replyToPost === 404)
                Response::error(404, "Post not found");

            $replyTo = $replyToPost->id;
        }
    }

    // hasMedia
    if ($hasMedia !== null && $hasMedia !== "true" && $hasMedia !== "false")
        Response::error(400, "Invalid hasMedia");

    $hasMedia = $hasMedia === "true" ? true : null;

    // Get posts
    $posts = Post::get_all(
        $query,
        $fromUserObject,
        $excludeUserObject,
        $replyTo,
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
    $tag = isset($_POST["tag"]) ? $_POST["tag"] : null;
    $content = isset($_POST["content"]) ? $_POST["content"] : null;
    $replyTo = isset($_POST["replyTo"]) ? $_POST["replyTo"] : null;

    $medias = null;
    if (isset($_POST['medias'])) {
        $medias = [];
        foreach ($_POST['medias'] as $mediaData) {
            $medias[] = new Media($mediaData["data"], $mediaData["ext"]);
        }
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
    if ($medias !== null) {
        if (!is_array($medias))
            Response::error(400, "Invalid media");

        if (count($medias) > 4)
            Response::error(400, "Too many files");

        foreach ($medias as $media) {
            // Check file size
            if (strlen($media->base64data) > 8000000) // 8MB
                Response::error(400, "File too large");

            if (!$media->isValid())
                Response::error(400, "Invalid media extension");
        }
    }

    // Upload media
    $mediaPaths = [];
    if ($media !== null) {
        foreach ($medias as $media) {
            // Generate snowflake
            $fileSnowflake = MediaSnowflake::generate($media->extension);

            // Upload file
            $filePath = $fileSnowflake->toFile();
            if (!$media->save($filePath))
                Response::error(500, "Failed to upload file");

            // Clean up
            // TODO: Remove metadata
            // TODO: Resize image (if needed)
            // TODO: Compress file

            $mediaPaths[] = $fileSnowflake->toString();
        }
    }

    // Create post
    $post = Post::create(
        $_AUTH["user"]->handle,
        $tag,
        $content,
        $mediaPaths,
        $replyToPost ?? null
    );

    if ($post === 500)
        Response::error();

    // Return post
    Response::success(201, "Post created", $post);
}
