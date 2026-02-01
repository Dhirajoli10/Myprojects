<?php
// includes/functions.php

declare(strict_types=1);

// Escape output to prevent XSS
function e(string $value): string {
    return htmlspecialchars($value, ENT_QUOTES, "UTF-8");
}

// Helpers
function is_post(): bool {
    return ($_SERVER["REQUEST_METHOD"] ?? "") === "POST";
}

function redirect(string $path): void {
    header("Location: $path");
    exit;
}

// Flash messages
function set_flash(string $msg): void {
    $_SESSION["flash"] = $msg;
}

function get_flash(): string {
    $msg = $_SESSION["flash"] ?? "";
    unset($_SESSION["flash"]);
    return $msg;
}

// Auth helpers
function is_logged_in(): bool {
    return isset($_SESSION["user"]);
}

function current_user(): ?array {
    return $_SESSION["user"] ?? null;
}

function require_login(): void {
    if (!is_logged_in()) {
        header("Location: login.php");
        exit;
    }
}

function require_admin(): void {
    require_login();
    $u = current_user();
    if (!$u || ($u["role"] ?? "") !== "admin") {
        http_response_code(403);
        exit("Access denied: Admin only.");
    }
}

/* =========================
   CSRF PROTECTION 
   ========================= */

function csrf_token(): string {
    // Generate token once per session
    if (empty($_SESSION["csrf_token"])) {
        $_SESSION["csrf_token"] = bin2hex(random_bytes(32));
    }
    return $_SESSION["csrf_token"];
}

function csrf_check(): void {
    $posted = $_POST["csrf_token"] ?? "";
    $session = $_SESSION["csrf_token"] ?? "";

    if ($posted === "" || $session === "" || !hash_equals($session, $posted)) {
        http_response_code(403);
        exit("Invalid CSRF token");
    }
}