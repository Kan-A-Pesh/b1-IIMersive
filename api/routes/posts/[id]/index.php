<?php
$id = $_GET["id"];

// Check post
$post = Post::get($id);

if ($post === 500)
    Response::error();

if ($post === 404)
    Response::error(404, "Post not found");

// Check method
if ($_SERVER["REQUEST_METHOD"] === "GET") {

    // Return post
    Response::success(200, "Post found", $post);
} else if ($_SERVER["REQUEST_METHOD"] === "DELETE") {

    // Check auth
    if ($_AUTH === null)
        Response::error(401, "User is not authenticated");

    // Check if user is post owner
    if ($post->author_handle !== $_AUTH["user"]->handle)
        Response::error(403, "User is not post owner");

    // Delete post
    $result = Post::delete($post->id);

    if ($result === 500)
        Response::error();

    // Return delete
    Response::success(204, "Post deleted");
} else {
    Response::error(405, "Method not allowed");
}
