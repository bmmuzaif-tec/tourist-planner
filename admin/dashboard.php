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
    <title>Admin Dashboard</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>

<body class="bg-light">

<nav class="navbar navbar-dark bg-success">
    <div class="container-fluid">
        <span class="navbar-brand mb-0 h1">Tourist Planner - Admin Panel</span>
        <div>
            <span class="text-white me-3">
                Welcome, <?php echo htmlspecialchars($_SESSION['admin_name']); ?>
            </span>
            <a href="logout.php" class="btn btn-light btn-sm">Logout</a>
        </div>
    </div>
</nav>

<div class="container mt-4">

    <h3 class="mb-3">Management</h3>
    <div class="row">
        <div class="col-md-4 mb-3">
            <a href="manage-places.php" class="btn btn-success w-100 p-3">🏖 Manage Tourist Places</a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="categories.php" class="btn btn-primary w-100 p-3">📂 Manage Categories</a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="reviews.php" class="btn btn-warning w-100 p-3">⭐ Manage Reviews</a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="manage-plans.php" class="btn btn-info w-100 p-3 text-white">📅 Visit Plans</a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="add-place.php" class="btn btn-dark w-100 p-3">➕ Add Tourist Place</a>
        </div>

        <div class="col-md-4 mb-3">
            <a href="../index.php" class="btn btn-secondary w-100 p-3">🌐 View Website</a>
        </div>
    </div>

    <hr class="my-4">

    <div class="row">
        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body py-3">
                    <h2 class="text-success mb-1 fw-bold"><?php echo $totalPlaces; ?></h2>
                    <p class="text-muted mb-0 small">Tourist Places</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body py-3">
                    <h2 class="text-primary mb-1 fw-bold"><?php echo $totalCategories; ?></h2>
                    <p class="text-muted mb-0 small">Categories</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body py-3">
                    <h2 class="text-info mb-1 fw-bold"><?php echo $totalPlans; ?></h2>
                    <p class="text-muted mb-0 small">Visit Plans</p>
                </div>
            </div>
        </div>

        <div class="col-md-3 mb-3">
            <div class="card text-center shadow-sm">
                <div class="card-body py-3">
                    <h2 class="text-warning mb-1 fw-bold"><?php echo $totalReviews; ?></h2>
                    <p class="text-muted mb-0 small">Reviews</p>
                </div>
            </div>
        </div>
    </div>

    <hr class="my-4">

    <div class="row mb-4">
        <div class="col-md-8 mx-auto">
            <div class="card shadow">
                <div class="card-header bg-success text-white">
                    <h5 class="mb-0">📊 Tourist Places by Category</h5>
                </div>
                <div class="card-body" style="position: relative; height: 350px; max-height: 350px;">
                    <canvas id="categoryChart"></canvas>
                </div>
            </div>
        </div>
    </div>

</div>

<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
    const ctx = document.getElementById('categoryChart');

    new Chart(ctx, {
        type: 'pie',
        data: {
            labels: <?php echo json_encode($labels); ?>,
            datasets: [{
                data: <?php echo json_encode($data); ?>,
                backgroundColor: [
                    '#0d6efd',
                    '#198754',
                    '#ffc107',
                    '#dc3545',
                    '#6f42c1',
                    '#20c997',
                    '#fd7e14',
                    '#6610f2'
                ],
                borderWidth: 1
            }]
        },
        options: {
            responsive: true,
            maintainAspectRatio: false,
            plugins: {
                legend: {
                    position: 'bottom',
                },
                tooltip: {
                    callbacks: {
                        label: function(context) {
                            let label = context.label || '';
                            if (label) {
                                label += ': ';
                            }
                            label += context.parsed + ' places';
                            return label;
                        }
                    }
                }
            }
        }
    });
</script>

</body>
</html>