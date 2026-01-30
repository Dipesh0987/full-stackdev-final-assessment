<?php
require_once __DIR__ . '/../config/db.php';
require_once __DIR__ . '/../includes/session.php';
require_once __DIR__ . '/../includes/init.php';

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
        
        // Check if it's the hardcoded admin
        if (($username === $ADMIN_USERNAME || $username === $ADMIN_EMAIL) && 
            $password === $ADMIN_PASSWORD) {
            $_SESSION['user_id'] = 0;
            $_SESSION['username'] = $ADMIN_USERNAME;
            $_SESSION['user_role'] = 'admin';
            $_SESSION['is_hardcoded_admin'] = true;
            
            header("Location: index.php");
            exit;
        } else {
            $error = 'Invalid username or password.';
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
    <!-- login container -->
    <div class="login-container">
        <h2>Login to Movie Database</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <form method="POST" action="">
            <div class="form-group">
                <label>Username</label>
                <input type="text" name="username" class="form-control" required>
            </div>
            
            <div class="form-group">
                <label>Password</label>
                <input type="password" name="password" class="form-control" required>
            </div>
            
            <button type="submit" class="btn-login">Login</button>
        </form>

    </div>
</body>
</html>
