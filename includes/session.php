<?php
session_start();
if (!isset($_SESSION['created'])) {
    $_SESSION['created'] = time();
} elseif (time() - $_SESSION['created'] > 1800) {
    session_regenerate_id(true);
    $_SESSION['created'] = time();
}
if (!isset($_SESSION['initiated'])) {
    session_regenerate_id();
    $_SESSION['initiated'] = true;
}
$ADMIN_USERNAME = 'admin';
$ADMIN_EMAIL = 'admin@moviedb.com';
$ADMIN_PASSWORD = 'admin123';

function isLoggedIn() {
    return isset($_SESSION['user_id']);
}

function isAdmin() {
    return isset($_SESSION['user_role']) && $_SESSION['user_role'] === 'admin';
}

function requireLogin() {
    if (!isLoggedIn()) {
        header("Location: login.php");
        exit;
    }
}

function requireAdmin() {
    requireLogin();
    if (!isAdmin()) {
        header("Location: index.php?error=admin_access_required");
        exit;
    }
}

function getUserRole() {
    return $_SESSION['user_role'] ?? 'guest';
}

function getUsername() {
    return $_SESSION['username'] ?? 'Guest';
}
function checkAdminCredentials($username, $password) {
    global $ADMIN_USERNAME, $ADMIN_EMAIL, $ADMIN_PASSWORD;
    
    // Check username/email
    if (($username === $ADMIN_USERNAME || $username === $ADMIN_EMAIL) && 
        $password === $ADMIN_PASSWORD) {
        return true;
    }
    return false;
}
?>
 