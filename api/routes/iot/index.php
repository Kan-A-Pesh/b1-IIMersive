<?php

// Check method
if ($_SERVER["REQUEST_METHOD"] === "GET") {
    $posts = Post::get_all(
        limit: 1
    );

    if ($posts === 500)
        Response::error();

    Response::success(200, "OK", $posts[0]);
} else if ($_SERVER["REQUEST_METHOD"] === "POST") {
    $tag = 7;
    $content = "Hello from Raspberry Pi!";

    $post = Post::create(
        "raspberrypi",
        $tag,
        $content,
        [],
    );
}