<?php
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/functions.php";

if (!is_logged_in()) {
    http_response_code(403);
    exit;
}

if (isset($_POST['id'], $_POST['stock'])) {
    $id = (int) $_POST['id'];
    $stock = (int) $_POST['stock'];

    $stmt = $conn->prepare("UPDATE products SET stock = ? WHERE product_id = ?");
    $stmt->bind_param("ii", $stock, $id);
    $stmt->execute();
}
