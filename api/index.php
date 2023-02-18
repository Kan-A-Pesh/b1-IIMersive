<?php include_once(__DIR__ . "/includes.php");

$path_string = $_GET["path"];
$path_array = explode("/", $path_string);

// Check in routes folder if file exists
if (file_exists(__DIR__ . "/routes/" . $path_string . ".php")) {
    include_once(__DIR__ . "/routes/" . $path_string . ".php");
} else if (file_exists(__DIR__ . "/routes/" . $path_string . "/index.php")) {
    include_once(__DIR__ . "/routes/" . $path_string . "/index.php");
} else {
    Response::error(404, "Endpoint not found");
}
