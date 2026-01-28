<?php
// File: public/register.php
require_once '../config/db.php';
require_once '../includes/session.php';
require_once '../includes/init.php';

// If already logged in, redirect
if (isLoggedIn()) {
    header("Location: index.php");
    exit;
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = trim($_POST['username'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $password = $_POST['password'] ?? '';
    $confirm_password = $_POST['confirm_password'] ?? '';
    
    // Access admin variables
    global $ADMIN_USERNAME, $ADMIN_EMAIL;
    
    // VALIDATION: Prevent anyone from registering as admin
    if (strtolower($username) === strtolower($ADMIN_USERNAME)) {
        $error = 'Username "' . htmlspecialchars($ADMIN_USERNAME) . '" is reserved. Please choose another username.';
    } elseif (strtolower($email) === strtolower($ADMIN_EMAIL)) {
        $error = 'Email "' . htmlspecialchars($ADMIN_EMAIL) . '" is reserved. Please use another email.';
    } elseif (empty($username) || empty($email) || empty($password)) {
        $error = 'Please fill all fields.';
    } elseif ($password !== $confirm_password) {
        $error = 'Passwords do not match.';
    } elseif (strlen($password) < 6) {
        $error = 'Password must be at least 6 characters.';
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error = 'Invalid email address.';
    } else {
        try {
            // Check if users table exists
            $tableExists = $pdo->query("SHOW TABLES LIKE 'users'")->fetch();
            if (!$tableExists) {
                $error = 'Database not set up. Please contact administrator.';
            } else {
                // Check if username or email already exists
                $checkStmt = $pdo->prepare("SELECT id FROM users WHERE username = ? OR email = ?");
                $checkStmt->execute([$username, $email]);
                
                if ($checkStmt->fetch()) {
                    $error = 'Username or email already exists.';
                } else {
                    // Hash password
                    $hashed_password = password_hash($password, PASSWORD_DEFAULT);
                    
                    // Insert user with DEFAULT role 'user' (NOT admin)
                    $stmt = $pdo->prepare("INSERT INTO users (username, email, password, role) VALUES (?, ?, ?, 'user')");
                    $stmt->execute([$username, $email, $hashed_password]);
                    
                    // Auto-login after registration
                    $user_id = $pdo->lastInsertId();
                    $loginStmt = $pdo->prepare("SELECT id, username, role FROM users WHERE id = ?");
                    $loginStmt->execute([$user_id]);
                    $new_user = $loginStmt->fetch();
                    
                    if ($new_user) {
                        $_SESSION['user_id'] = $new_user['id'];
                        $_SESSION['username'] = $new_user['username'];
                        $_SESSION['user_role'] = $new_user['role'];
                        
                        header("Location: index.php");
                        exit;
                    } else {
                        $success = 'Registration successful! You can now <a href="login.php">login</a>.';
                    }
                }
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
    <title>Register - Movie Database</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>
<body>
    <div class="register-container">
        <h2>Register as User</h2>
        
        <?php if ($error): ?>
            <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
        <?php endif; ?>
        
        <?php if ($success): ?>
            <div class="alert alert-success"><?php echo $success; ?></div>
        <?php else: ?>
            <form method="POST" action="">
                <div class="form-group">
                    <label>Username *</label>
                    <input type="text" name="username" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['username'] ?? ''); ?>" 
                           required minlength="3" maxlength="50">
                </div>
                
                <div class="form-group">
                    <label>Email *</label>
                    <input type="email" name="email" class="form-control" 
                           value="<?php echo htmlspecialchars($_POST['email'] ?? ''); ?>" 
                           required>
                </div>
                
                <div class="form-group">
                    <label>Password * (min 6 characters)</label>
                    <input type="password" name="password" class="form-control" 
                           required minlength="6">
                </div>
                
                <div class="form-group">
                    <label>Confirm Password *</label>
                    <input type="password" name="confirm_password" class="form-control" 
                           required minlength="6">
                </div>
                
                <button type="submit" class="btn-register">Register as User</button>
            </form>
        <?php endif; ?>
        
        <div class="login-link">
            <p>Already have an account? <a href="login.php">Login here</a></p>
        </div>
    </div>
</body>
</html>
