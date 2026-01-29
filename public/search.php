<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";

// Must be logged in to search/view inventory
require_login();

// Supplier dropdown
$suppliers = $conn->query("SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name ASC");

$supplier_id = trim($_GET["supplier_id"] ?? "");
$min_price   = trim($_GET["min_price"] ?? "");
$max_price   = trim($_GET["max_price"] ?? "");
$max_stock   = trim($_GET["max_stock"] ?? "");

$where = [];
$params = [];
$types = "";

// Build dynamic query safely (prepared statement)
if ($supplier_id !== "" && ctype_digit($supplier_id)) {
    $where[] = "p.supplier_id = ?";
    $params[] = (int)$supplier_id;
    $types .= "i";
}
if ($min_price !== "" && is_numeric($min_price)) {
    $where[] = "p.price >= ?";
    $params[] = (float)$min_price;
    $types .= "d";
}
if ($max_price !== "" && is_numeric($max_price)) {
    $where[] = "p.price <= ?";
    $params[] = (float)$max_price;
    $types .= "d";
}
if ($max_stock !== "" && ctype_digit($max_stock)) {
    $where[] = "p.stock <= ?";
    $params[] = (int)$max_stock;
    $types .= "i";
}

$sql = "
SELECT p.product_id, p.product_name, p.price, p.stock, s.supplier_name
FROM products p
JOIN suppliers s ON s.supplier_id = p.supplier_id
";

if ($where) {
    $sql .= " WHERE " . implode(" AND ", $where);
}

$sql .= " ORDER BY p.product_id DESC";

$stmt = $conn->prepare($sql);

if ($params) {
    // bind_param needs references
    $bind = [];
    $bind[] = $types;
    foreach ($params as $k => $v) {
        $bind[] = &$params[$k];
    }
    call_user_func_array([$stmt, "bind_param"], $bind);
}

$stmt->execute();
$results = $stmt->get_result();
?>

<div class="card">
  <h2>Advanced Search</h2>
  <p>Search products by supplier, price range, or stock level.</p>

  <form method="get">
    <div class="form-row">
      <label>Supplier</label>
      <select name="supplier_id">
        <option value="">-- Any --</option>
        <?php while ($s = $suppliers->fetch_assoc()): ?>
          <option value="<?= (int)$s["supplier_id"] ?>" <?= ($supplier_id == $s["supplier_id"]) ? "selected" : "" ?>>
            <?= e($s["supplier_name"]) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-row">
      <label>Min Price</label>
      <input name="min_price" type="number" step="0.01" value="<?= e($min_price) ?>">
    </div>

    <div class="form-row">
      <label>Max Price</label>
      <input name="max_price" type="number" step="0.01" value="<?= e($max_price) ?>">
    </div>

    <div class="form-row">
      <label>Max Stock (Example: 10)</label>
      <input name="max_stock" type="number" value="<?= e($max_stock) ?>">
    </div>

    <button class="btn" type="submit">Search</button>
    <a class="btn gray" href="search.php">Reset</a>
  </form>
</div>

<div class="card">
  <h3>Results</h3>
  <table>
    <thead>
      <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Supplier</th>
        <th>Price</th>
        <th>Stock</th>
      </tr>
    </thead>
    <tbody>
      <?php while ($r = $results->fetch_assoc()): ?>
        <tr>
          <td><?= (int)$r["product_id"] ?></td>
          <td><?= e($r["product_name"]) ?></td>
          <td><?= e($r["supplier_name"]) ?></td>
          <td>₹<?= number_format((float)$r["price"], 2) ?></td>
          <td><?= (int)$r["stock"] ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>