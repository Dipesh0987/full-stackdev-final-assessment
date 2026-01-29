<!DOCTYPE html>
<html lang="en">

<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Movie Database</title>
    <link rel="stylesheet" href="../assets/css/style.css">
</head>

<body>
    <?php
    require_once __DIR__ . '/session.php';
    require_once __DIR__ . '/init.php';
    ?>
    <nav class="navbar">
        <div class="container">
            <a href="index.php" class="logo">Movie Database</a>
            <div class="nav-links">
                <a href="index.php" class="nav-btn">All Movies</a>
                <a href="search.php" class="nav-btn">Search</a>

                <?php if (isAdmin()): ?>
                    <a href="add.php" class="nav-btn">Add Movie</a>
                    <a href="genres.php" class="nav-btn">Genres</a>
                <?php endif; ?>

                <?php if (isLoggedIn()): ?>
                    <span class="nav-user">
                        Welcome, <?php echo htmlspecialchars(getUsername()); ?>
                    </span>
                    <a href="logout.php" class="nav-btn nav-btn-logout">Logout</a>
                <?php else: ?>
                    <a href="login.php" class="nav-btn">Login</a>
                    <a href="register.php" class="nav-btn nav-btn-register">Register</a>
                <?php endif; ?>
            </div>
        </div>
    </nav>

    <main>
        <div class="container">