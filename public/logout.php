<?php
require_once __DIR__ . "/../includes/header.php";

session_destroy();
session_start(); // restart for flash
set_flash("Logged out.");
redirect("login.php");