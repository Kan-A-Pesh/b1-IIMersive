<?php
# Debugging for development
ini_set('display_errors', 1);
ini_set('display_startup_errors', 1);
error_reporting(E_ALL);
# ------------------------

include_once(__DIR__ . "/config.php");

include_once(__DIR__ . "/utils/UUID4.php");
include_once(__DIR__ . "/utils/database.php");
include_once(__DIR__ . "/utils/response.php");
