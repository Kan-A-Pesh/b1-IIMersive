<?php
$id = $_GET["id"];

// Check post
$post = Post::get($id);

if ($post === 500)
    Response::error();

if ($post === 404)
    Response::error(404, "Post not found");

// Check auth
if ($_AUTH === null)
    Response::error(401, "User is not authenticated");

// Check method
if ($_SERVER["REQUEST_METHOD"] === "POST") {

    // Check if user already liked post
    if (Post::is_liked($post->getId(), $_AUTH["user"]->getHandle()))
        Response::error(409, "User already liked post");

    // Like post
    $result = Post::like_post($post->getId(), $_AUTH["user"]->getHandle());

    if ($result === 500)
        Response::error();

    // Return like
    Response::success(204, "Post liked");
} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    // Check if user already liked post
    if (!Post::is_liked($post->getId(), $_AUTH["user"]->getHandle()))
        Response::error(409, "User did not like post");

    // Unlike post
    $result = Post::unlike_post($post->getId(), $_AUTH["user"]->getHandle());

    if ($unlike === 500)
        Response::error();

    // Return unlike
    Response::success(204, "Post unliked");
} else {
    Response::error(405, "Method not allowed");
}
