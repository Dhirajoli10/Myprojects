<?php
// config/db.php

declare(strict_types=1);

$DB_HOST = "localhost";
$DB_USER = "root";
$DB_PASS = "";     
$DB_NAME = "inventory_db";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Database connection failed.");
}