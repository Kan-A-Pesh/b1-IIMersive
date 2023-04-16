<?php

$MYSQL_HOST = "localhost";
$MYSQL_PORT = 3306;
$MYSQL_USER = "redmoon";
$MYSQL_PASS = "s3cur3p4ssw0rd";
$MYSQL_DB = "iimersive";

$MYSQL_USER_TABLE = "users";
$MYSQL_POST_TABLE = "posts";
$MYSQL_SESSION_TABLE = "sessions";
$MYSQL_MESSAGE_TABLE = "messages";
$MYSQL_NOTIFICATION_TABLE = "notifications";
$MYSQL_LIKE_TABLE = "likes";

// ! This works for Docker only, consider using
// ! localhost or 127.0.0.1 if not using Docker.
$SOCKET_HOST = "host.docker.internal";
$SOCKET_THROW = false;
