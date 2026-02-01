<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";

require_login();

$isAdmin = ((current_user()["role"] ?? "") === "admin");
?>

<div class="card">
  <h2>All Products</h2>
  <p>
    Only logged-in users can view this inventory.
    <?php if ($isAdmin): ?>
      <br><strong>Admin mode:</strong> You can add/edit/delete products.
    <?php else: ?>
      <br><strong>User mode:</strong> You can view and search only.
    <?php endif; ?>
  </p>

  <!-- Table will be rendered once by PHP, then updated by Ajax (JSON) -->
  <table id="productsTable">
    <thead>
      <tr>
        <th>ID</th>
        <th>Product</th>
        <th>Supplier</th>
        <th>Price NPR(Rs)</th>
        <th>Stock</th>
        <th>Status</th>
        <?php if ($isAdmin): ?>
          <th>Actions</th>
        <?php endif; ?>
      </tr>
    </thead>
    <tbody id="productsTbody">
      <!-- initial content will be filled by JS immediately -->
    </tbody>
  </table>
</div>

<script>
  // Pass role to JS safely
  window.__IS_ADMIN__ = <?= $isAdmin ? "true" : "false" ?>;
</script>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>