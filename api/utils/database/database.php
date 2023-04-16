<?php

class Database
{

    public static $pdo = null;

    public static function init()
    {
        global $MYSQL_HOST, $MYSQL_PORT, $MYSQL_DB, $MYSQL_USER, $MYSQL_PASS;

        try {
            // Connect to mySQL server
            self::$pdo = new PDO(
                "mysql:host=$MYSQL_HOST;port=$MYSQL_PORT;dbname=$MYSQL_DB;charset=utf8mb4",
                $MYSQL_USER,
                $MYSQL_PASS
            );

            // Set error mode to exceptions
            self::$pdo->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
        } catch (PDOException $e) {
            // Send an error message
            Response::error(500, $e->getMessage());
        }
    }
}

Database::init();
