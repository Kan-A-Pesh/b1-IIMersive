<?php
// Check if mySQL server is reachable
try {
    // Connect to mySQL server
    $pdo = new PDO(
        "mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8",
        $MYSQL_USER,
        $MYSQL_PASS
    );

    // Set error mode to exceptions
    $pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Send an error message
    error(500, $e->getMessage());
}
