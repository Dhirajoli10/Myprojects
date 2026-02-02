<?php
// config/db.php

declare(strict_types=1);

$DB_HOST = "localhost";
$DB_USER = "np03cs4a240184";
$DB_PASS = "Rpp1oz3Tpa";     
$DB_NAME = "np03cs4a240184";

mysqli_report(MYSQLI_REPORT_ERROR | MYSQLI_REPORT_STRICT);

try {
    $conn = new mysqli($DB_HOST, $DB_USER, $DB_PASS, $DB_NAME);
    $conn->set_charset("utf8mb4");
} catch (mysqli_sql_exception $e) {
    http_response_code(500);
    exit("Database connection failed.");
}