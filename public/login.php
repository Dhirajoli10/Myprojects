<?php
require_once __DIR__ . "/../config/db.php";
require_once __DIR__ . "/../includes/header.php";

if (is_logged_in()) {
    redirect("index.php");
}

$errors = [];
$username = "";

if (is_post()) {
    csrf_check(); // CSRF CHECK

    $username = trim($_POST["username"] ?? "");
    $password = $_POST["password"] ?? "";

    if ($username === "" || $password === "") {
        $errors[] = "Username and password required.";
    } else {
        $stmt = $conn->prepare("SELECT user_id, username, password_hash, role FROM users WHERE username = ?");
        $stmt->bind_param("s", $username);
        $stmt->execute();
        $user = $stmt->get_result()->fetch_assoc();

        if ($user && password_verify($password, $user["password_hash"])) {
            $_SESSION["user"] = [
                "user_id" => (int)$user["user_id"],
                "username" => $user["username"],
                "role" => $user["role"]
            ];
            set_flash("Welcome, " . $user["username"] . "!");
            redirect("index.php");
        } else {
            $errors[] = "Invalid login.";
        }
    }
}
?>

<div class="card">
  <h2>Login</h2>

  <?php if ($errors): ?>
    <div class="flash">
      <ul><?php foreach ($errors as $er): ?><li><?= e($er) ?></li><?php endforeach; ?></ul>
    </div>
  <?php endif; ?>

  <form method="post">
    <!-- CSRF token inside the form -->
    <input type="hidden" name="csrf_token" value="<?= csrf_token() ?>">

    <div class="form-row">
      <label>Username</label>
      <input name="username" value="<?= e($username) ?>" required>
    </div>

    <div class="form-row">
      <label>Password</label>
      <input name="password" type="password" required>
    </div>

    <button class="btn" type="submit">Login</button>
  </form>
</div>

<?php require_once __DIR__ . "/../includes/footer.php"; ?>