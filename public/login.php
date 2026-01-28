<?php
// File: public/login.php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/init.php';

// If already logged in, redirect
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Please enter username and password.';
    } else {
        // Access admin variables from session.php
        global $ADMIN_USERNAME, $ADMIN_EMAIL, $ADMIN_PASSWORD;
        
        // FIRST: Check if it's the hardcoded admin
        if (($username === $ADMIN_USERNAME || $username === $ADMIN_EMAIL) && 
            $password === $ADMIN_PASSWORD) {
            // It's the admin!
            $_SESSION['user_id'] = 0;
            $_SESSION['username'] = $ADMIN_USERNAME;
            $_SESSION['user_role'] = 'admin';
            $_SESSION['is_hardcoded_admin'] = true;
            
            // Ensure admin exists in database
            ensureAdminExists();
            
            header("Location: index.php");
            exit;
        }
        
        // If not admin, check database for regular users
        try {
            $stmt = $pdo->prepare("SELECT id, username, password, role FROM users WHERE username = ? OR email = ?");
            $stmt->execute([$username, $username]);
            $user = $stmt->fetch();
            
            if ($user && password_verify($password, $user['password'])) {
                $_SESSION['user_id'] = $user['id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['user_role'] = $user['role'];
                
                header("Location: index.php");
                exit;
            } else {
                $error = 'Invalid username or password.';
            }
        } catch (PDOException $e) {
            $error = 'Database error: ' . $e->getMessage();
        }
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - Movie Database</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="login-container">
        <h2>Login to Movie Database</h2>
        
<!--         <div class="admin-credentials">
            <p><strong>Fixed Admin Credentials:</strong></p>
            <?php global $ADMIN_USERNAME, $ADMIN_PASSWORD; ?>
            <p><strong>Username:</strong> <?php echo htmlspecialchars($ADMIN_USERNAME); ?></p>
            <p><strong>Password:</strong> <?php echo htmlspecialchars($ADMIN_PASSWORD); ?></p>
            <p><small>These credentials are hardcoded and cannot be changed via registration.</small></p>
        </div> -->
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username or Email</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>
        
        <div class="register-link">
            <p>Don't have a user account? <a href="register.php">Register</a></p>
        </div>
    </div>
</body>
</html>
