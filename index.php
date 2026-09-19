<?php
include 'config/db.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/hero.php';

// Count places strictly within 25km of Eravur
$totalPlaces = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM tourist_places WHERE (distance + 0) <= 25"))['count'];
$totalCategories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM categories"))['count'];
$totalReviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reviews"))['count'];

// Popular destinations (first 6, by nearest distance)
$popular = mysqli_query($conn, "SELECT tp.*, c.category_name FROM tourist_places tp
    INNER JOIN categories c ON tp.category_id = c.category_id
    ORDER BY tp.distance ASC LIMIT 6");

// Categories for chips
$categories = mysqli_query($conn, "SELECT * FROM categories ORDER BY category_name ASC");
?>

<!-- Popular Destinations -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="eyebrow">Handpicked for you</span>
            <h2 class="section-title">Popular Destinations</h2>
            <p class="section-subtitle mx-auto">The most-loved spots around Eravur, ready to add to your visit plan.</p>
        </div>
        <div class="row g-4">
            <?php while ($row = mysqli_fetch_assoc($popular)):
                $imgName = htmlspecialchars($row['image']);
                $imgPath = "https://via.placeholder.com/500x320/0B6E4F/ffffff?text=" . urlencode($row['place_name']);
                if (!empty($imgName)) {
                    if (file_exists(__DIR__ . "/admin/uploads/$imgName")) $imgPath = "admin/uploads/$imgName";
                    elseif (file_exists(__DIR__ . "/assets/images/places/$imgName")) $imgPath = "assets/images/places/$imgName";
                }
            ?>
            <div class="col-lg-4 col-md-6">
                <div class="card place-card h-100 border-0 shadow-sm">
                    <img src="<?= $imgPath ?>" class="card-img-top" style="height:220px; object-fit:cover;" alt="<?= htmlspecialchars($row['place_name']) ?>">
                    <div class="card-body d-flex flex-column">
                        <span class="badge bg-success mb-2" style="width:fit-content;"><?= htmlspecialchars($row['category_name']) ?></span>
                        <h5 class="fw-bold mb-2"><?= htmlspecialchars($row['place_name']) ?></h5>
                        <p class="text-muted small flex-grow-1"><?= substr(htmlspecialchars($row['description']), 0, 100) ?>...</p>
                        <div class="d-flex justify-content-between align-items-center">
                            <span class="text-muted small"><i class="bi bi-signpost-2"></i> <?= htmlspecialchars($row['distance']) ?> km</span>
                            <a href="place-details.php?id=<?= $row['place_id'] ?>" class="btn btn-outline-success btn-sm">Explore &rarr;</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endwhile; ?>
        </div>
        <div class="text-center mt-5">
            <a href="places.php" class="btn btn-success btn-lg"><i class="bi bi-grid"></i> View All Places</a>
        </div>
    </div>
</section>

<!-- Categories -->
<section class="section" style="background:var(--surface);">
    <div class="container">
        <div class="text-center mb-5">
            <span class="eyebrow">Browse by category</span>
            <h2 class="section-title">Find What You Love</h2>
        </div>
        <div class="d-flex flex-wrap justify-content-center gap-3">
            <?php while ($cat = mysqli_fetch_assoc($categories)): ?>
                <a href="places.php?category=<?= $cat['category_id'] ?>" class="category-chip glass-panel">
                    <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($cat['category_name']) ?>
                </a>
            <?php endwhile; ?>
        </div>
    </div>
</section>

<!-- Why Tourist Planner -->
<section class="section">
    <div class="container">
        <div class="text-center mb-5">
            <span class="eyebrow">Why use this planner</span>
            <h2 class="section-title">Everything You Need to Explore</h2>
        </div>
        <div class="row g-4 text-center">
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon"><i class="bi bi-signpost-split"></i></div>
                <h6 class="fw-bold">Explore Nearby</h6>
                <p class="text-muted small">Curated destinations within 25 km of Eravur.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon"><i class="bi bi-map"></i></div>
                <h6 class="fw-bold">Interactive Maps</h6>
                <p class="text-muted small">Google Maps directions for every location.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon"><i class="bi bi-calendar2-week"></i></div>
                <h6 class="fw-bold">Plan Your Day</h6>
                <p class="text-muted small">Build a one-day itinerary with total distance.</p>
            </div>
            <div class="col-md-3 col-sm-6">
                <div class="feature-icon"><i class="bi bi-binoculars"></i></div>
                <h6 class="fw-bold">Discover Local Places</h6>
                <p class="text-muted small">Beaches, heritage, culture and hidden gems.</p>
            </div>
        </div>
    </div>
</section>

<!-- Statistics & Final CTA -->
<section class="section" style="background:var(--surface);">
    <div class="container">
        <div class="row text-center g-4 mb-5">
            <div class="col-md-4">
                <div class="stat-card glass-panel">
                    <div class="stat-number"><?= $totalPlaces ?></div>
                    <p class="text-muted mb-0 fw-semibold">Places within 25km</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card glass-panel">
                    <div class="stat-number"><?= $totalCategories ?></div>
                    <p class="text-muted mb-0 fw-semibold">Categories</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="stat-card glass-panel">
                    <div class="stat-number"><?= $totalReviews ?></div>
                    <p class="text-muted mb-0 fw-semibold">Visitor Reviews</p>
                </div>
            </div>
        </div>

        <div class="text-center">
            <h3 class="fw-bold mb-3">Ready to plan your perfect day?</h3>
            <p class="text-muted mb-4">Choose your favourite destinations and create a one-day itinerary in minutes.</p>
            <a href="planner.php" class="btn btn-success btn-lg px-5 py-3">
                <i class="bi bi-rocket-takeoff"></i> Start Planning
            </a>
        </div>
    </div>
</section>

<?php include 'includes/footer.php'; ?>
