<?php
declare(strict_types=1);

// Start session for login + csrf
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

require_once __DIR__ . "/functions.php";
?>
<!doctype html>
<html lang="en">
<head>
  <meta charset="utf-8">
  <title>Inventory System</title>
  <meta name="viewport" content="width=device-width, initial-scale=1">
  <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
<header class="topbar">
  <div class="container">
    <h1>Inventory & Stock Tracking</h1>

    <nav>
      <?php if (is_logged_in()): ?>
        <a href="index.php">Home</a>
        <a href="search.php">Search</a>

        <?php if ((current_user()["role"] ?? "") === "admin"): ?>
          <a href="add.php">Add Product</a>
          <a href="suppliers.php">Suppliers</a>
        <?php endif; ?>

        <span style="margin-left:10px;">
          <?= e(current_user()["username"]) ?> (<?= e(current_user()["role"]) ?>)
        </span>
        <a style="margin-left:10px;" href="logout.php">Logout</a>
      <?php else: ?>
        <a href="login.php">Login</a>
      <?php endif; ?>
    </nav>
  </div>
</header>

<main class="container">
<?php $flash = get_flash(); ?>
<?php if ($flash): ?>
  <div class="flash"><?= e($flash) ?></div>
<?php endif; ?>