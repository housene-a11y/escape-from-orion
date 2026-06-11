<?php

require_once 'config.php';

try {
    $db_connection = new PDO(
        "mysql:host=" . SERVERNAME . ";dbname=" . DATABASE . ";charset=utf8mb4",
        USERNAME,
        PASSWORD
    );

    $db_connection->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);

} catch (PDOException $e) {
    die("Databasefout: " . $e->getMessage());
}