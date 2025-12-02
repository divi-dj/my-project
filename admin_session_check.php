<?php
// Start session if not already started
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// Check if user is logged in and has admin role
if (!isset($_SESSION['user_id']) || !isset($_SESSION['role']) || $_SESSION['role'] !== 'admin') {
    // Redirect to login page
    header("Location: login.php?redirect=" . urlencode($_SERVER['REQUEST_URI']) . "&error=admin_required");
    exit;
}
?> 