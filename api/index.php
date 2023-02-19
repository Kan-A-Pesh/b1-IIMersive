<?php include_once(__DIR__ . "/includes.php");

// Enable JSON request body
$_POST = json_decode(file_get_contents("php://input"), true);

// Get path
$path_string = $_GET["path"];
$path_array = explode("/", $path_string);

// Routes
$routes = [
    "index" => "index.php",
    "coffee" => "coffee.php",
    "auth" => "auth/index.php",
    "users" => "users/index.php",
    "users/:handle" => "users/[handle].php",
    "messages/latest" => "messages/latest.php", // TODO
    "messages/:handle" => "messages/[handle].php", // TODO
    "notifications" => "notifications/index.php", // TODO
    "posts" => "posts/index.php", // TODO
    "posts/:id" => "posts/[id]/index.php", // TODO
    "posts/:id/like" => "posts/[id]/like.php", // TODO
];

// Re-route request to correct file
foreach ($routes as $route => $file) {
    $route_array = explode("/", $route);
    $route_array_length = count($route_array);
    $path_array_length = count($path_array);

    if ($route_array_length !== $path_array_length)
        continue;

    $match = true;

    for ($i = 0; $i < $route_array_length; $i++) {
        if ($route_array[$i] === $path_array[$i])
            continue;

        if (strpos($route_array[$i], ":") === 0) {
            $key = substr($route_array[$i], 1);
            $_GET[$key] = $path_array[$i];
            continue;
        }

        $match = false;
        break;
    }

    if ($match) {
        include(__DIR__ . "/routes/" . $file);
        exit;
    }
}

Response::error(404, "Route not found");
