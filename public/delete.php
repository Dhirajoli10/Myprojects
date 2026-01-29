<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";

require_admin();

$id = $_GET["id"] ?? "";
if (!ctype_digit($id)) {
    set_flash("Invalid product ID.");
    redirect("index.php");
}
$pid = (int)$id;

if (is_post()) {
    csrf_check(); //  CSRF CHECK

    $stmt = $conn->prepare("DELETE FROM products WHERE product_id = ?");
    $stmt->bind_param("i", $pid);
    $stmt->execute();

    set_flash("Product deleted.");
    redirect("index.php");
}

$stmt = $conn->prepare("SELECT product_name FROM products WHERE product_id = ?");
$stmt->bind_param("i", $pid);
$stmt->execute();
$row = $stmt->get_result()->fetch_assoc();

if (!$row) {
    set_flash("Product not found.");
    redirect("index.php");
}
?>

<div class="card">
  <h2>Delete Product</h2>
  <p>Are you sure you want to delete: <strong><?= e($row["product_name"]) ?></strong>?</p>

  <form method="post">
    <!--  CSRF token inside the form -->
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <button class="btn danger" type="submit">Yes, Delete</button>
    <a class="btn gray" href="index.php">Cancel</a>
  </form>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>