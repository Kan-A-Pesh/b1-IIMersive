<?php
include_once(__DIR__ . "/includes.php");

// Check if mySQL server is reachable
try {
    // Connect to mySQL server
    $pdo = new PDO(
        "mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8",
        $MYSQL_USER,
        $MYSQL_PASS
    );

    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

    // Check if tables exist
    $stmt = $pdo->prepare("SELECT * FROM $MYSQL_USER_TABLE");
    $stmt->execute();
    $stmt = $pdo->prepare("SELECT * FROM $MYSQL_POST_TABLE");
    $stmt->execute();
    $stmt = $pdo->prepare("SELECT * FROM $MYSQL_SESSION_TABLE");
    $stmt->execute();
    $stmt = $pdo->prepare("SELECT * FROM $MYSQL_MESSAGE_TABLE");
    $stmt->execute();
    $stmt = $pdo->prepare("SELECT * FROM $MYSQL_NOTIFICATION_TABLE");
    $stmt->execute();

    // Send a success message
    success(204, "No Content");
} catch (PDOException $e) {
    // Send an error message
    error(500, $e->getMessage());
}
