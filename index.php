<?php
include 'config/db.php';
include 'includes/header.php';
include 'includes/navbar.php';
include 'includes/hero.php';

// Count places strictly within 25km of Eravur
$totalPlaces = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM tourist_places WHERE (distance + 0) <= 25"))['count'];
$totalCategories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM categories"))['count'];
$totalReviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) as count FROM reviews"))['count'];
?>

<!-- Quick Stats & Call to Action Section -->
<section class="py-5 bg-light">
    <div class="container">
        
        <!-- Statistics Cards -->
        <div class="row text-center g-4 mb-5">
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm border-bottom border-success border-4">
                    <h2 class="display-4 fw-bold text-success mb-2"><?= $totalPlaces ?></h2>
                    <p class="text-muted mb-0 fw-bold">Places within 25km</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm border-bottom border-primary border-4">
                    <h2 class="display-4 fw-bold text-primary mb-2"><?= $totalCategories ?></h2>
                    <p class="text-muted mb-0 fw-bold">Categories</p>
                </div>
            </div>
            <div class="col-md-4">
                <div class="p-4 bg-white rounded shadow-sm border-bottom border-warning border-4">
                    <h2 class="display-4 fw-bold text-warning mb-2"><?= $totalReviews ?></h2>
                    <p class="text-muted mb-0 fw-bold">Visitor Reviews</p>
                </div>
            </div>
        </div>

        <!-- Call to Action Button -->
        <div class="text-center">
            <h3 class="fw-bold mb-3 text-dark">Ready to explore nearby places with in 25km?</h3>
            <p class="text-muted mb-4">Discover the best local spots for your perfect one-day trip.</p>
            <a href="places.php" class="btn btn-success btn-lg px-5 py-3 shadow fw-bold">
                🌍 Browse All Nearby Places
            </a>
        </div>

    </div>
</section>

<?php include 'includes/footer.php'; ?>