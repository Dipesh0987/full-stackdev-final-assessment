<?php
// made connection to database, session functions from session.php, initialize system and check if user is admin
require_once '../config/db.php';
require_once '../includes/session.php';
require_once '../includes/init.php';
requireAdmin();

require_once '../includes/header.php';

$error = '';
$success = '';


// check for the submission of the form
if ($_SERVER['REQUEST_METHOD'] === 'POST') {

    $title = trim($_POST['title'] ?? '');
    $director = trim($_POST['director'] ?? '');
    $year = $_POST['year'] ?? '';
    $genre = trim($_POST['genre'] ?? '');
    $rating = $_POST['rating'] ?? '';
    $description = trim($_POST['description'] ?? '');
    
    // Validate
    if (empty($title) || empty($director) || empty($year) || empty($genre)) {
        $error = 'Please fill in all required fields.';
    } else {
        // file upload check 
        $poster = 'no-image.png';
        // checking the file format
        if (isset($_FILES['poster']) && $_FILES['poster']['error'] === UPLOAD_ERR_OK) {
            $allowed = ['jpg', 'jpeg', 'png'];
            $filename = $_FILES['poster']['name'];
            $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));
            $filesize = $_FILES['poster']['size'];
            $tempname = $_FILES['poster']['tmp_name'];
            // check the file size of uploaded image
            if (in_array($ext, $allowed) && $filesize <= 5000000) {

                $poster = uniqid('movie_') . '.' . $ext;
                $upload_path = '../assets/uploads/' . $poster;
            
                if (!is_dir('../assets/uploads')) {
                    mkdir('../assets/uploads', 0755, true);
                }
                
                if (!move_uploaded_file($tempname, $upload_path)) {
                    $error = 'Failed to upload image. Using default.';
                    $poster = 'no-image.png';
                }
            } else {
                $error = 'Invalid file (max 5MB, allowed: JPG, PNG, JPEG). Using default.';
                $poster = 'no-image.png';
            }
        }
        
        if (empty($error)) {
            try {
               
                $tableExists = $pdo->query("SHOW TABLES LIKE 'movies'")->fetch();
                if (!$tableExists) {
                    $error = 'Movies table not found. Please run setup.';
                } else {
                    $stmt = $pdo->prepare("INSERT INTO movies (title, director, year, genre, rating, description, poster) VALUES (?, ?, ?, ?, ?, ?, ?)");
                    $stmt->execute([$title, $director, $year, $genre, $rating, $description, $poster]);
                    $success = 'Movie added successfully!';
                    $_POST = [];
                }
            } catch (PDOException $e) {
                $error = 'Database error: ' . $e->getMessage();
            }
        }
    }
}

try {
    $genres = $pdo->query("SELECT name FROM genres ORDER BY name")->fetchAll();
} catch (PDOException $e) {
    $genres = [];
    if (empty($error)) {
        $error = 'Unable to load genres. Please add genres first.';
    }
}
?>

<h2>Add New Movie</h2>

<?php if ($error): ?>
    <div class="alert alert-error"><?php echo htmlspecialchars($error); ?></div>
<?php endif; ?>

<?php if ($success): ?>
    <div class="alert alert-success"><?php echo htmlspecialchars($success); ?></div>
<?php endif; ?>

<form method="POST" enctype="multipart/form-data">
    <div class="form-group">
        <label>Title *</label>
        <input type="text" name="title" class="form-control" value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label>Director *</label>
        <input type="text" name="director" class="form-control" value="<?php echo htmlspecialchars($_POST['director'] ?? ''); ?>" required>
    </div>
    
    <div class="form-group">
        <label>Year *</label>
        <input type="number" name="year" class="form-control" value="<?php echo htmlspecialchars($_POST['year'] ?? date('Y')); ?>" min="1900" max="<?php echo date('Y'); ?>" required>
    </div>
    
    <div class="form-group">
        <label>Genre *</label>
        <select name="genre" class="form-control" required>
            <option value="">Select Genre</option>
            <?php foreach ($genres as $g): ?>
                <option value="<?php echo htmlspecialchars($g['name']); ?>" <?php echo ($_POST['genre'] ?? '') == $g['name'] ? 'selected' : ''; ?>>
                    <?php echo htmlspecialchars($g['name']); ?>
                </option>
            <?php endforeach; ?>
        </select>
    </div>
    
    <div class="form-group">
        <label>Rating (0-10)</label>
        <input type="number" name="rating" class="form-control" step="0.1" value="<?php echo htmlspecialchars($_POST['rating'] ?? ''); ?>" min="0" max="10">
    </div>
    
    <div class="form-group">
        <label>Description</label>
        <textarea name="description" class="form-control" rows="4"><?php echo htmlspecialchars($_POST['description'] ?? ''); ?></textarea>
    </div>
    
    <div class="form-group">
        <label>Poster Image</label>
        <input type="file" name="poster" class="form-control" accept="image/*">
        <small>Optional. Max 5MB. Allowed: JPG, PNG, JPEG</small>
    </div>
    
    <div class="form-group">
        <button type="submit" class="btn">Add Movie</button>
        <a href="index.php" class="btn btn-secondary">Cancel</a>
    </div>
</form>

<?php require_once '../includes/footer.php'; ?>