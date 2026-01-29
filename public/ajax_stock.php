<?php
// ajax_stock.php — returns JSON ONLY

// Start session FIRST
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/functions.php";

// If not logged in, return empty JSON (prevents redirect breaking JS)
if (!is_logged_in()) {
    header("Content-Type: application/json");
    echo json_encode(["ok" => false, "items" => []]);
    exit;
}

header("Content-Type: application/json; charset=utf-8");

$sql = "
SELECT p.product_id, p.product_name, p.price, p.stock, s.supplier_name
FROM products p
JOIN suppliers s ON s.supplier_id = p.supplier_id
ORDER BY p.product_id DESC
";

$result = $conn->query($sql);

$items = [];
while ($row = $result->fetch_assoc()) {
    $items[] = [
        "product_id"    => (int)$row["product_id"],
        "product_name"  => $row["product_name"],
        "supplier_name" => $row["supplier_name"],
        "price"         => (float)$row["price"],
        "stock"         => (int)$row["stock"],
    ];
}

echo json_encode([
    "ok"    => true,
    "items" => $items
], JSON_UNESCAPED_UNICODE);