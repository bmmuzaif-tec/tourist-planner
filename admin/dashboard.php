<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

// 1. Get Dashboard Statistics
$totalPlaces = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM tourist_places"))['total'];
$totalCategories = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM categories"))['total'];
$totalPlans = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM visit_plan"))['total'];
$totalReviews = mysqli_fetch_assoc(mysqli_query($conn, "SELECT COUNT(*) AS total FROM reviews"))['total'];

// 2. Get Category Data for Chart
$chartQuery = mysqli_query($conn, "
    SELECT c.category_name, COUNT(tp.place_id) AS total
    FROM categories c
    LEFT JOIN tourist_places tp
    ON c.category_id = tp.category_id
    GROUP BY c.category_id
");

$labels = [];
$data = [];

while($row = mysqli_fetch_assoc($chartQuery)){
    $labels[] = $row['category_name'];
    $data[] = $row['total'];
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard | EravurGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="../assets/css/style.css">
    <style>
        body{font-family:"Inter",sans-serif;background:var(--background);}
        h1,h2,h3,h4,h5,h6{font-family:"Poppins",sans-serif;}
        .card{border-radius:var(--radius-sm);}
        .btn{border-radius:999px;}
    </style>
</head>

<body class="bg-light">

<!-- =====================================================
     ADMIN NAVBAR
     ===================================================== -->
<nav class="navbar navbar-dark admin-navbar">
    <div class="container-fluid px-4">
        <span class="navbar-brand mb-0 h1">
            <i class="bi bi-shield-lock-fill"></i> EravurGo — Admin Panel
        </span>
        <div class="d-flex align-items-center gap-3">
            <span class="welcome-pill">
                <i class="bi bi-person-circle"></i>
                <?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?>
            </span>
            <a href="logout.php" class="btn btn-light btn-sm">
                <i class="bi bi-box-arrow-right"></i> Logout
            </a>
        </div>
    </div>
</nav>

<div class="container mt-4 mb-5">

    <!-- =====================================================
         WELCOME BANNER (Travel Go Style Hero)
         ===================================================== -->
    <div class="admin-hero reveal">
        <div class="row align-items-center g-3">
            <div class="col-md-8">
                <span class="hero-badge">
                    <i class="bi bi-stars"></i> Admin Overview
                </span>
                <h2 class="hero-title mb-2">
                    Welcome back, <span class="text-warning"><?= htmlspecialchars($_SESSION['admin_name'] ?? 'Admin') ?></span>!
                </h2>
                <p class="hero-subtitle mb-0">
                    <i class="bi bi-calendar3"></i> <?= date('l, d F Y') ?> — Here's your platform summary.
                </p>
            </div>
            <div class="col-md-4 text-md-end">
                <a href="../index.php" target="_blank" class="btn btn-light btn-lg hero-btn">
                    <i class="bi bi-globe2"></i> View Live Site
                </a>
            </div>
        </div>
    </div>

    <!-- =====================================================
         QUICK STATS (Travel Go Colorful Cards)
         ===================================================== -->
    <div class="row g-4 mt-4">
        <div class="col-lg-3 col-md-6 reveal reveal-delay-1">
            <div class="stat-card-tg stat-card-places">
                <div class="stat-card-tg-header">
                    <div class="stat-icon-tg">
                        <i class="bi bi-geo-alt-fill"></i>
                    </div>
                    <span class="stat-badge-tg">+<?= $totalPlaces ?></span>
                </div>
                <div class="stat-card-tg-body">
                    <h2 class="stat-number-tg"><?= $totalPlaces ?></h2>
                    <p class="stat-label-tg">Tourist Places</p>
                </div>
                <div class="stat-card-tg-footer">
                    <a href="manage-places.php">Manage <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 reveal reveal-delay-2">
            <div class="stat-card-tg stat-card-cats">
                <div class="stat-card-tg-header">
                    <div class="stat-icon-tg">
                        <i class="bi bi-tags-fill"></i>
                    </div>
                    <span class="stat-badge-tg">+<?= $totalCategories ?></span>
                </div>
                <div class="stat-card-tg-body">
                    <h2 class="stat-number-tg"><?= $totalCategories ?></h2>
                    <p class="stat-label-tg">Categories</p>
                </div>
                <div class="stat-card-tg-footer">
                    <a href="categories.php">Manage <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 reveal reveal-delay-3">
            <div class="stat-card-tg stat-card-plans">
                <div class="stat-card-tg-header">
                    <div class="stat-icon-tg">
                        <i class="bi bi-calendar-check-fill"></i>
                    </div>
                    <span class="stat-badge-tg">+<?= $totalPlans ?></span>
                </div>
                <div class="stat-card-tg-body">
                    <h2 class="stat-number-tg"><?= $totalPlans ?></h2>
                    <p class="stat-label-tg">Visit Plans</p>
                </div>
                <div class="stat-card-tg-footer">
                    <a href="manage-plans.php">Manage <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>

        <div class="col-lg-3 col-md-6 reveal reveal-delay-1">
            <div class="stat-card-tg stat-card-reviews">
                <div class="stat-card-tg-header">
                    <div class="stat-icon-tg">
                        <i class="bi bi-chat-square-heart-fill"></i>
                    </div>
                    <span class="stat-badge-tg">+<?= $totalReviews ?></span>
                </div>
                <div class="stat-card-tg-body">
                    <h2 class="stat-number-tg"><?= $totalReviews ?></h2>
                    <p class="stat-label-tg">Reviews</p>
                </div>
                <div class="stat-card-tg-footer">
                    <a href="reviews.php">Manage <i class="bi bi-arrow-right"></i></a>
                </div>
            </div>
        </div>
    </div>

    <!-- =====================================================
         MANAGEMENT — Icon Cards (Travel Go Style)
         ===================================================== -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-3 reveal">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-grid-1x2-fill text-success"></i> Management
        </h4>
        <small class="text-muted">Quick access to all admin tools</small>
    </div>
    <div class="row g-3">
        <div class="col-lg-4 col-md-6 reveal reveal-delay-1">
            <a href="manage-places.php" class="mgmt-card-tg">
                <div class="mgmt-icon-tg mgmt-icon-green">
                    <i class="bi bi-umbrella-beach-fill"></i>
                </div>
                <div class="mgmt-content-tg">
                    <h6>Tourist Places</h6>
                    <p>Add, edit, delete destinations</p>
                </div>
                <i class="bi bi-chevron-right mgmt-arrow-tg"></i>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 reveal reveal-delay-2">
            <a href="categories.php" class="mgmt-card-tg">
                <div class="mgmt-icon-tg mgmt-icon-blue">
                    <i class="bi bi-folder-fill"></i>
                </div>
                <div class="mgmt-content-tg">
                    <h6>Categories</h6>
                    <p>Organize destination types</p>
                </div>
                <i class="bi bi-chevron-right mgmt-arrow-tg"></i>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 reveal reveal-delay-3">
            <a href="reviews.php" class="mgmt-card-tg">
                <div class="mgmt-icon-tg mgmt-icon-gold">
                    <i class="bi bi-star-fill"></i>
                </div>
                <div class="mgmt-content-tg">
                    <h6>Reviews</h6>
                    <p>Moderate visitor feedback</p>
                </div>
                <i class="bi bi-chevron-right mgmt-arrow-tg"></i>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 reveal reveal-delay-1">
            <a href="manage-plans.php" class="mgmt-card-tg">
                <div class="mgmt-icon-tg mgmt-icon-cyan">
                    <i class="bi bi-calendar2-week-fill"></i>
                </div>
                <div class="mgmt-content-tg">
                    <h6>Visit Plans</h6>
                    <p>View and manage trip plans</p>
                </div>
                <i class="bi bi-chevron-right mgmt-arrow-tg"></i>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 reveal reveal-delay-2">
            <a href="add-place.php" class="mgmt-card-tg">
                <div class="mgmt-icon-tg mgmt-icon-dark">
                    <i class="bi bi-plus-circle-fill"></i>
                </div>
                <div class="mgmt-content-tg">
                    <h6>Add New Place</h6>
                    <p>Create a new destination</p>
                </div>
                <i class="bi bi-chevron-right mgmt-arrow-tg"></i>
            </a>
        </div>

        <div class="col-lg-4 col-md-6 reveal reveal-delay-3">
            <a href="../index.php" target="_blank" class="mgmt-card-tg">
                <div class="mgmt-icon-tg mgmt-icon-gray">
                    <i class="bi bi-globe2"></i>
                </div>
                <div class="mgmt-content-tg">
                    <h6>View Website</h6>
                    <p>Open live site in new tab</p>
                </div>
                <i class="bi bi-chevron-right mgmt-arrow-tg"></i>
            </a>
        </div>
    </div>

    <!-- =====================================================
         ANALYTICS CHART
         ===================================================== -->
    <div class="d-flex justify-content-between align-items-center mt-5 mb-3 reveal">
        <h4 class="fw-bold mb-0">
            <i class="bi bi-bar-chart-fill text-success"></i> Analytics
        </h4>
        <small class="text-muted">Places by category distribution</small>
    </div>
    <div class="row">
        <div class="col-lg-8 mx-auto reveal">
            <div class="card shadow-sm border-0 analytics-card-tg">
                <div class="card-header analytics-header-tg">
                    <h5 class="mb-0">
                        <i class="bi bi-pie-chart-fill"></i> Tourist Places by Category
                    </h5>
                </div>
                <div class="card-body" style="position: relative; height: 350px; max-height: 350px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<!-- =====================================================
     Chart.js + Scroll Reveal
     ===================================================== -->
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>
<script>
    const ctx = document.getElementById('categoryChart');
    new Chart(ctx, {
        type: 'doughnut',
        data: {
            labels: <?= json_encode($labels) ?>,
            datasets: [{
                data: <?= json_encode($data) ?>,
                backgroundColor: ['#0B6E4F', '#2D8659', '#D9A441', '#0d6efd', '#6f42c1', '#20c997', '#fd7e14', '#6610f2', '#dc3545', '#198754'],
                borderWidth: 2,
                borderColor: '#fff',
                hoverOffset: 12
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                    labels: { padding: 16, font: { family: 'Inter', size: 12, weight: '600' }, usePointStyle: true, pointStyle: 'circle' }
                },
                tooltip: {
                    backgroundColor: 'rgba(11, 110, 79, 0.95)',
                    padding: 12,
                    titleFont: { family: 'Poppins', size: 14, weight: 'bold' },
                    bodyFont: { family: 'Inter', size: 13 },
                    callbacks: {
                        label: function(c) { return ' ' + c.label + ': ' + c.parsed + ' place' + (c.parsed !== 1 ? 's' : ''); }
                    }
                }
            }
        }
    });
</script>
<script>
(function() {
    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length === 0) return;
    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });
    reveals.forEach(function(el) { observer.observe(el); });
})();
</script>

</body>
</html>