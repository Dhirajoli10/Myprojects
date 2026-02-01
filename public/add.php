<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";

require_admin();

$suppliers = $conn->query("SELECT supplier_id, supplier_name FROM suppliers ORDER BY supplier_name ASC");

$errors = [];
$product_name = "";
$supplier_id = "";
$price = "";
$stock = "";

if (is_post()) {
    csrf_check(); 

    $product_name = trim($_POST["product_name"] ?? "");
    $supplier_id  = trim($_POST["supplier_id"] ?? "");
    $price        = trim($_POST["price"] ?? "");
    $stock        = trim($_POST["stock"] ?? "");

    if ($product_name === "") $errors[] = "Product name is required.";
    if ($supplier_id === "" || !ctype_digit($supplier_id)) $errors[] = "Supplier is required.";
    if ($price === "" || !is_numeric($price) || (float)$price < 0) $errors[] = "Price must be >= 0.";
    if ($stock === "" || !ctype_digit($stock)) $errors[] = "Stock must be a whole number (0+).";

    if (!$errors) {
        $stmt = $conn->prepare("
          INSERT INTO products (product_name, supplier_id, price, stock)
          VALUES (?, ?, ?, ?)
        ");
        $sid = (int)$supplier_id;
        $p   = (float)$price;
        $st  = (int)$stock;

        $stmt->bind_param("sidi", $product_name, $sid, $p, $st);
        $stmt->execute();

        set_flash("Product added successfully.");
        redirect("index.php");
    }
}
?>

<div class="card">
  <h2>Add Product</h2>

  <?php if ($errors): ?>
    <div class="flash">
      <strong>Please fix:</strong>
      <ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <!--  CSRF token inside the form -->
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="form-row">
      <label>Product Name</label>
      <input name="product_name" value="<?= e($product_name) ?>" required>
    </div>

    <div class="form-row">
      <label>Supplier</label>
      <select name="supplier_id" required>
        <option value="">-- Select Supplier --</option>
        <?php while ($s = $suppliers->fetch_assoc()): ?>
          <option value="<?= (int)$s["supplier_id"] ?>" <?= ($supplier_id == $s["supplier_id"]) ? "selected" : "" ?>>
            <?= e($s["supplier_name"]) ?>
          </option>
        <?php endwhile; ?>
      </select>
    </div>

    <div class="form-row">
      <label>Price (Rs)</label>
      <input name="price" type="number" step="0.01" min="0" value="<?= e($price) ?>" required>
    </div>

    <div class="form-row">
      <label>Stock</label>
      <input name="stock" type="number" min="0" value="<?= e($stock) ?>" required>
    </div>

    <button class="btn" type="submit">Save</but