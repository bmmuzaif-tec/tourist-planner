<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$plan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

// Fetch Plan Info securely
$sql_plan = "SELECT plan_id, visitor_name, visit_date FROM visit_plan WHERE plan_id = ?";
$stmt = mysqli_prepare($conn, $sql_plan);
mysqli_stmt_bind_param($stmt, "i", $plan_id);
mysqli_stmt_execute($stmt);
$result_plan = mysqli_stmt_get_result($stmt);
$plan = mysqli_fetch_assoc($result_plan);

if(!$plan){
    header("Location: manage-plans.php");
    exit();
}

// Fetch Plan Details (Places in order)
$sql_details = "SELECT vpd.visit_order, tp.place_name, tp.distance, tp.opening_hours, tp.address
                FROM visit_plan_details vpd
                INNER JOIN tourist_places tp ON tp.place_id = vpd.place_id
                WHERE vpd.plan_id = ?
                ORDER BY vpd.visit_order ASC";
$stmt2 = mysqli_prepare($conn, $sql_details);
mysqli_stmt_bind_param($stmt2, "i", $plan_id);
mysqli_stmt_execute($stmt2);
$result_details = mysqli_stmt_get_result($stmt2);
?>
<!DOCTYPE html>
<html>
<head>
<title>View Visit Plan #<?php echo $plan['plan_id']; ?></title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
<link rel="stylesheet" href="../assets/css/style.css">
<style>
body{font-family:"Inter",sans-serif;background:var(--background);}
h1,h2,h3,h4,h5,h6{font-family:"Poppins",sans-serif;}
.navbar.bg-success{background:var(--primary) !important;}
.card{border-radius:var(--radius-sm);}
.btn{border-radius:999px;}
</style>
</head>
<body>
<div class="container mt-5">
    <div class="d-flex justify-content-between align-items-center mb-4">
        <h2 class="text-success">Visit Plan Details</h2>
        <a href="manage-plans.php" class="btn btn-secondary">← Back to Plans</a>
    </div>

    <div class="card mb-4 shadow-sm">
        <div class="card-body">
            <h5 class="card-title text-success">Plan Information</h5>
            <p class="mb-1"><strong>Plan ID:</strong> <?php echo $plan['plan_id']; ?></p>
            <p class="mb-1"><strong>Visitor Name:</strong> <?php echo htmlspecialchars($plan['visitor_name']); ?></p>
            <p class="mb-0"><strong>Visit Date:</strong> <?php echo $plan['visit_date']; ?></p>
        </div>
    </div>

    <h4 class="mb-3">Itinerary (Places to Visit)</h4>
    <table class="table table-bordered table-striped">
        <thead class="table-success">
            <tr>
                <th>Order</th>
                <th>Place Name</th>
                <th>Distance (km)</th>
                <th>Opening Hours</th>
                <th>Address</th>
            </tr>
        </thead>
        <tbody>
            <?php if(mysqli_num_rows($result_details) > 0): ?>
                <?php while($row = mysqli_fetch_assoc($result_details)): ?>
                <tr>
                    <td><?php echo $row['visit_order']; ?></td>
                    <td><?php echo htmlspecialchars($row['place_name']); ?></td>
                    <td><?php echo htmlspecialchars($row['distance']); ?></td>
                    <td><?php echo htmlspecialchars($row['opening_hours'] ?? 'N/A'); ?></td>
                    <td><?php echo htmlspecialchars($row['address'] ?? 'N/A'); ?></td>
                </tr>
                <?php endwhile; ?>
            <?php else: ?>
                <tr>
                    <td colspan="5" class="text-center">No places added to this plan.</td>
                </tr>
            <?php endif; ?>
        </tbody>
    </table>
</div>
</body>
</html>