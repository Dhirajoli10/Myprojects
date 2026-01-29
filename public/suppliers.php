<?php
require_once __DIR__ . "/../config/db.php";
//require_once __DIR__ . "/../includes/header.php";

require_admin();

$errors = [];
$name = "";
$email = "";

if (is_post()) {
    csrf_check(); //  CSRF CHECK

    $name  = trim($_POST["supplier_name"] ?? "");
    $email = trim($_POST["contact_email"] ?? "");

    if ($name === "") $errors[] = "Supplier name required.";
    if ($email === "" || !filter_var($email, FILTER_VALIDATE_EMAIL)) $errors[] = "Valid email required.";

    if (!$errors) {
        $stmt = $conn->prepare("INSERT INTO suppliers (supplier_name, contact_email) VALUES (?, ?)");
        $stmt->bind_param("ss", $name, $email);
        $stmt->execute();

        set_flash("Supplier added.");
        redirect("suppliers.php");
    }
}

$suppliers = $conn->query("SELECT * FROM suppliers ORDER BY supplier_id DESC");
?>

<div class="card">
  <h2>Suppliers</h2>

  <?php if ($errors): ?>
    <div class="flash">
      <ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <!-- CSRF token inside the form -->
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="form-row">
      <label>Supplier Name</label>
      <input name="supplier_name" value="<?= e($name) ?>" required>
    </div>

    <div class="form-row">
      <label>Contact Email</label>
      <input name="contact_email" type="email" value="<?= e($email) ?>" required>
    </div>

    <button class="btn" type="submit">Add Supplier</button>
  </form>
</div>

<div class="card">
  <h3>All Suppliers</h3>
  <table>
    <thead><tr><th>ID</th><th>Name</th><th>Email</th></tr></thead>
    <tbody>
      <?php while ($s = $suppliers->fetch_assoc()): ?>
        <tr>
          <td><?= (int)$s["supplier_id"] ?></td>
          <td><?= e($s["supplier_name"]) ?></td>
          <td><?= e($s["contact_email"]) ?></td>
        </tr>
      <?php endwhile; ?>
    </tbody>
  </table>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>