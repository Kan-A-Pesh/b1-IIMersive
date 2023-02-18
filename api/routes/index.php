<?php

// Check if tables exist
try {
    $stmt = Database::$pdo->prepare("SELECT * FROM $MYSQL_USER_TABLE");
    $stmt->execute();
    $stmt = Database::$pdo->prepare("SELECT * FROM $MYSQL_POST_TABLE");
    $stmt->execute();
    $stmt = Database::$pdo->prepare("SELECT * FROM $MYSQL_SESSION_TABLE");
    $stmt->execute();
    $stmt = Database::$pdo->prepare("SELECT * FROM $MYSQL_MESSAGE_TABLE");
    $stmt->execute();
    $stmt = Database::$pdo->prepare("SELECT * FROM $MYSQL_NOTIFICATION_TABLE");
    $stmt->execute();

    // Send a success message
    Response::success(204, "No Content");
} catch (PDOException $e) {
    // Send an error message
    Response::error(500, $e->getMessage());
}
