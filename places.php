<?php
include 'config/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$cat_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

$where = "WHERE 1=1";
if ($search) $where .= " AND tp.place_name LIKE '%$search%'";
if ($cat_id) $where .= " AND tp.category_id = $cat_id";

$sql = "SELECT tp.*, c.category_name FROM tourist_places tp 
        INNER JOIN categories c ON tp.category_id = c.category_id 
        $where ORDER BY tp.place_name ASC";
$result = mysqli_query($conn, $sql);
?>

<div class="container mt-5 mb-5">
    <h2 class="text-center mb-4 fw-bold text-success">🌍 Tourist Places Near Eravur</h2>

    <!-- Search & Filter Form -->
    <form method="GET" class="row g-2 mb-5 justify-content-center">
        <div class="col-md-5">
            <input type="text" name="search" class="form-control form-control-lg" placeholder="Search Tourist Place..." value="<?= htmlspecialchars($search) ?>">
        </div>
        <div class="col-md-5">
            <select name="category" class="form-select form-select-lg">
                <option value="">All Categories</option>
                <?php foreach(mysqli_query($conn, "SELECT * FROM categories") as $c): ?>
                    <option value="<?= $c['category_id'] ?>" <?= $cat_id == $c['category_id'] ? 'selected' : '' ?>>
                        <?= htmlspecialchars($c['category_name']) ?>
                    </option>
                <?php endforeach; ?>
            </select>
        </div>
        <div class="col-md-2">
            <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">🔍 Search</button>
        </div>
    </form>

    <!-- Tourist Places Grid -->
    <div class="row g-4">
        <?php if (mysqli_num_rows($result) > 0): while ($row = mysqli_fetch_assoc($result)): 
            
            // 🌟 SMART IMAGE RESOLVER (படம் எங்கிருந்தாலும் கண்டுபிடிக்கும்)
            $imgName = htmlspecialchars($row['image']);
            $imgPath = "https://via.placeholder.com/400x220/198754/ffffff?text=" . urlencode($row['place_name']);
            
            if (!empty($imgName)) {
                if (file_exists(__DIR__ . "/admin/uploads/$imgName")) {
                    $imgPath = "admin/uploads/$imgName";
                } elseif (file_exists(__DIR__ . "/assets/images/places/$imgName")) {
                    $imgPath = "assets/images/places/$imgName";
                }
            }
        ?>
            <div class="col-md-4 col-sm-6">
                <div class="card h-100 shadow-sm border-0 hover-card">
                    <img src="<?= $imgPath ?>" class="card-img-top" style="height:220px; object-fit:cover;" alt="<?= htmlspecialchars($row['place_name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-success mb-2 w-fit"><?= htmlspecialchars($row['category_name']) ?></span>
                        <h5 class="card-title fw-bold"><?= htmlspecialchars($row['place_name']) ?></h5>
                        <p class="card-text text-muted small flex-grow-1"><?= substr(htmlspecialchars($row['description']), 0, 120) ?>...</p>
                        <p class="text-primary fw-bold mb-3">📍 Distance: <?= htmlspecialchars($row['distance']) ?> km</p>
                        <a href="place-details.php?id=<?= $row['place_id'] ?>" class="btn btn-outline-success w-100 fw-bold">View Details</a>
                    </div>
                </div>
            </div>
        <?php endwhile; else: ?>
            <div class="col-12 text-center py-5">
                <div class="alert alert-warning fs-5">😕 No tourist places found matching your criteria.</div>
            </div>
        <?php endif; ?>
    </div>
</div>

<style>
    .hover-card { transition: transform 0.3s ease, box-shadow 0.3s ease; }
    .hover-card:hover { transform: translateY(-5px); box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important; }
    .w-fit { width: fit-content; }
</style>

<?php include 'includes/footer.php'; ?>