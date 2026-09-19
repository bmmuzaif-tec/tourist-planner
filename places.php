<?php
include 'config/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

$search = isset($_GET['search']) ? mysqli_real_escape_string($conn, $_GET['search']) : '';
$cat_id = isset($_GET['category']) ? intval($_GET['category']) : 0;

$where = "WHERE 1=1";
if ($search) $where .= " AND (tp.place_name LIKE '%$search%' OR tp.description LIKE '%$search%')";
if ($cat_id) $where .= " AND tp.category_id = $cat_id";

$sql = "SELECT tp.*, c.category_name FROM tourist_places tp
        INNER JOIN categories c ON tp.category_id = c.category_id
        $where ORDER BY tp.place_name ASC";
$result = mysqli_query($conn, $sql);
$totalCount = mysqli_num_rows($result);

$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");

// build query string helper (preserves search while switching category)
function buildUrl($catId, $search) {
    $params = [];
    if ($catId) $params['category'] = $catId;
    if ($search) $params['search'] = $search;
    return 'places.php' . (count($params) ? '?' . http_build_query($params) : '');
}
?>

<section class="section pb-0">
    <div class="container">
        <div class="mb-4 reveal">
            <h1 class="section-title mb-1">Places of Interest</h1>
            <p class="section-subtitle">Browse verified local destinations located within a 25 km radius of Eravur. Filter by category or search by keyword.</p>
        </div>

        <!-- Filter Bar -->
        <form method="GET" class="filter-bar mb-2 reveal">
            <div class="filter-pills">
                <a href="<?= buildUrl(0, $search) ?>" class="filter-pill <?= !$cat_id ? 'active' : '' ?>">All Categories</a>
                <?php while ($c = mysqli_fetch_assoc($categories)): ?>
                    <a href="<?= buildUrl($c['category_id'], $search) ?>" class="filter-pill <?= $cat_id == $c['category_id'] ? 'active' : '' ?>">
                        <?= htmlspecialchars($c['category_name']) ?>
                    </a>
                <?php endwhile; ?>
            </div>
            <div class="filter-search">
                <input type="text" name="search" placeholder="Search by name, description..." value="<?= htmlspecialchars($search) ?>">
                <?php if ($cat_id): ?><input type="hidden" name="category" value="<?= $cat_id ?>"><?php endif; ?>
            </div>
        </form>

        <div class="d-flex justify-content-between align-items-center mb-4 flex-wrap gap-2 reveal">
            <p class="text-muted small mb-0">Showing <strong class="text-dark"><?= $totalCount ?></strong> place<?= $totalCount == 1 ? '' : 's' ?></p>
            <?php if ($search || $cat_id): ?>
                <a href="places.php" class="btn btn-outline-secondary btn-sm"><i class="bi bi-x-circle"></i> Clear Filters</a>
            <?php endif; ?>
        </div>
    </div>
</section>

<div class="container mb-5">
    <div class="row g-4">
        <?php if ($totalCount > 0): 
            $i = 0;
            while ($row = mysqli_fetch_assoc($result)):
                $i++;
                $delay = ($i % 4) + 1;
                $imgName = htmlspecialchars($row['image']);
                $imgPath = "https://via.placeholder.com/400x260/0B6E4F/ffffff?text=" . urlencode($row['place_name']);
                if (!empty($imgName)) {
                    if (file_exists(__DIR__ . "/admin/uploads/$imgName")) $imgPath = "admin/uploads/$imgName";
                    elseif (file_exists(__DIR__ . "/assets/images/places/$imgName")) $imgPath = "assets/images/places/$imgName";
                }
        ?>
            <div class="col-lg-3 col-md-4 col-sm-6 reveal reveal-delay-<?= $delay ?>">
                <div class="card place-card-v2 h-100 shadow-sm">
                    <div class="thumb-wrap">
                        <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($row['place_name']) ?>">
                        <span class="badge-cat-overlay"><?= htmlspecialchars($row['category_name']) ?></span>
                        <span class="badge-dist-overlay"><i class="bi bi-geo-alt-fill" style="font-size:0.65rem;"></i> <?= htmlspecialchars($row['distance']) ?> km</span>
                    </div>
                    <div class="card-body d-flex flex-column">
                        <h6 class="place-title"><?= htmlspecialchars($row['place_name']) ?></h6>
                        <p class="place-desc mb-2"><?= substr(htmlspecialchars($row['description']), 0, 80) ?>...</p>
                        <?php if (!empty($row['opening_hours'])): ?>
                        <p class="place-hours mb-2"><i class="bi bi-clock"></i> <?= htmlspecialchars($row['opening_hours']) ?></p>
                        <?php endif; ?>
                        <div class="mt-auto pt-1">
                            <a href="place-details.php?id=<?= $row['place_id'] ?>" class="view-details-link">View Details <i class="bi bi-arrow-right"></i></a>
                        </div>
                    </div>
                </div>
            </div>
        <?php endwhile; else: ?>
            <div class="col-12">
                <div class="empty-state reveal">
                    <div class="empty-icon"><i class="bi bi-search"></i></div>
                    <p class="fs-5 mb-1">No destinations found.</p>
                    <p class="text-muted small">Try another search or category.</p>
                </div>
            </div>
        <?php endif; ?>
    </div>
</div>

<?php include 'includes/footer.php'; ?>