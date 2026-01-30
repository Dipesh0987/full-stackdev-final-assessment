<?php
require_once '../config/db.php';
require_once '../includes/session.php';
require_once '../includes/init.php';
require_once '../includes/header.php';

$results = [];
$search_performed = false;
$search_query = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST' || isset($_GET['q'])) {
    $search_performed = true;

    $title = $_GET['q'] ?? $_POST['title'] ?? '';
    $genre = $_POST['genre'] ?? '';
    $year_from = $_POST['year_from'] ?? '';
    $year_to = $_POST['year_to'] ?? '';
    $rating_min = $_POST['rating_min'] ?? '';
    

    $sql = "SELECT * FROM movies WHERE 1=1";
    $params = [];
    
    if (!empty($title)) {
        $sql .= " AND title LIKE ?";
        $params[] = "%$title%";
    }
    
    if (!empty($genre)) {
        $sql .= " AND genre = ?";
        $params[] = $genre;
    }
    
    if (!empty($year_from)) {
        $sql .= " AND year >= ?";
        $params[] = $year_from;
    }
    
    if (!empty($year_to)) {
        $sql .= " AND year <= ?";
        $params[] = $year_to;
    }
    
    if (!empty($rating_min)) {
        $sql .= " AND rating >= ?";
        $params[] = $rating_min;
    }
    
    $sql .= " ORDER BY title";
    

    $stmt = $pdo->prepare($sql);
    $stmt->execute($params);
    $results = $stmt->fetchAll();
    

    $search_parts = [];
    if (!empty($title)) $search_parts[] = "Title: '$title'";
    if (!empty($genre)) $search_parts[] = "Genre: $genre";
    if (!empty($year_from) || !empty($year_to)) {
        $year_range = [];
        if (!empty($year_from)) $year_range[] = "from $year_from";
        if (!empty($year_to)) $year_range[] = "to $year_to";
        $search_parts[] = "Year " . implode(' ', $year_range);
    }
    if (!empty($rating_min)) $search_parts[] = "Rating ≥ $rating_min";
    $search_query = implode(', ', $search_parts);
}


$genres = $pdo->query("SELECT name FROM genres ORDER BY name")->fetchAll();
?>

<h2>Search Movies</h2>

<div class="search-container">
    <form method="POST" class="search-form" id="searchForm">
        <div class="form-group">
            <label for="searchBox">Title</label>
            <div class="search-suggestions">
                <input type="text" id="searchBox" name="title" class="form-control" 
                       placeholder="Search by title..." autocomplete="off"
                       value="<?php echo htmlspecialchars($_POST['title'] ?? ''); ?>">
                <ul id="suggestions"></ul>
            </div>
        </div>
        
        <div class="form-group">
            <label for="genre">Genre</label>
            <select id="genre" name="genre" class="form-control">
                <option value="">All Genres</option>
                <?php foreach ($genres as $g): ?>
                    <option value="<?php echo htmlspecialchars($g['name']); ?>"
                        <?php echo ($_POST['genre'] ?? '') == $g['name'] ? 'selected' : ''; ?>>
                        <?php echo htmlspecialchars($g['name']); ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        
        <div class="form-group">
            <label for="year_from">Year From</label>
            <input type="number" id="year_from" name="year_from" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['year_from'] ?? ''); ?>" 
                   min="1900" max="2030" placeholder="1900">
        </div>
        
        <div class="form-group">
            <label for="year_to">Year To</label>
            <input type="number" id="year_to" name="year_to" class="form-control" 
                   value="<?php echo htmlspecialchars($_POST['year_to'] ?? ''); ?>" 
                   min="1900" max="2030" placeholder="2030">
        </div>
        
        <div class="form-group">
            <label for="rating_min">Minimum Rating</label>
            <input type="number" id="rating_min" name="rating_min" class="form-control" step="0.1"
                   value="<?php echo htmlspecialchars($_POST['rating_min'] ?? ''); ?>" 
                   min="0" max="10" placeholder="0.0">
        </div>
        
        <div class="form-group" style="align-self: flex-end;">
            <button type="submit" class="btn">Search</button>
            <button type="button" class="btn btn-secondary" onclick="resetSearch()">Reset</button>
        </div>
    </form>
</div>

<?php if ($search_performed): ?>
    <h3>Search Results <?php echo $search_query ? "($search_query)" : ''; ?> (<?php echo count($results); ?> found)</h3>
    
    <?php if (count($results) > 0): ?>
        <div class="grid">
            <?php foreach ($results as $movie): ?>
                <div class="card">
                    <?php
                    $poster = $movie['poster'] ?? 'no-image.png';
                    $posterPath = '../assets/uploads/' . $poster;
                    if (!file_exists($posterPath)) {
                        $posterPath = '../assets/uploads/no-image.png';
                    }
                    ?>
                    <img src="<?php echo $posterPath; ?>" 
                         alt="<?php echo htmlspecialchars($movie['title']); ?>">
                    <div class="card-content">
                        <h3 class="card-title"><?php echo htmlspecialchars($movie['title']); ?></h3>
                        <p class="card-meta"><?php echo htmlspecialchars($movie['genre']); ?> | <?php echo htmlspecialchars($movie['year']); ?></p>
                        <p class="card-rating">Rating: <?php echo htmlspecialchars($movie['rating']); ?>/10</p>
                        
                        <div class="actions">
                            <a href="view.php?id=<?php echo $movie['id']; ?>">View</a>
                            <?php if (isAdmin()): ?>
                                <a href="edit.php?id=<?php echo $movie['id']; ?>">Edit</a>
                                <a href="cast.php?movie_id=<?php echo $movie['id']; ?>">Cast</a>
                                <a href="delete.php?id=<?php echo $movie['id']; ?>" 
                                   onclick="return confirm('Are you sure?')">Delete</a>
                            <?php endif; ?>
                        </div>
                    </div>
                </div>
            <?php endforeach; ?>
        </div>
    <?php else: ?>
        <div class="no-results">
            <p>No movies found matching your criteria.</p>
            <a href="search.php" class="btn">Try Again</a>
            <a href="index.php" class="btn btn-secondary">View All Movies</a>
        </div>
    <?php endif; ?>

<?php endif; ?>

<script>
function resetSearch() {
    window.location.href = 'search.php';
}
</script>

<?php require_once '../includes/footer.php'; ?>