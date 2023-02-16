<?php
// Check if mySQL server is reachable
try {
    // Connect to mySQL server
    $_PDO = new PDO(
        "mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8",
        $MYSQL_USER,
        $MYSQL_PASS
    );

    // Set error mode to exceptions
    $_PDO->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
} catch (PDOException $e) {
    // Send an error message
    Response::error(500, $e->getMessage());
}
