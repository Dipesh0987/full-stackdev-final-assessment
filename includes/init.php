<?php
require_once __DIR__ . '/../config/db.php';

function ensureAdminExists() {
    global $pdo;
    
    
    require_once __DIR__ . '/session.php';
    
    try {
        // Check if users table exists
        $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
        
        if (!$tableExists) {
            return;
        }
        
        global $ADMIN_USERNAME;
        $check = $pdo->prepare("SELECT id FROM users WHERE username = ? AND role = 'admin'");
        $check->execute([$ADMIN_USERNAME]);
        
        if (!$check->fetch()) {
            global $ADMIN_EMAIL, $ADMIN_PASSWORD;
            $hashed_password = password_hash($ADMIN_PASSWORD, PASSWORD_DEFAULT);
            $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'admin')");
            $stmt->execute([$ADMIN_USERNAME, $ADMIN_EMAIL, $hashed_password]);
        }
    } catch (PDOException $e) {
        error_log("Admin creation error: " . $e->getMessage());
    }
}
?>