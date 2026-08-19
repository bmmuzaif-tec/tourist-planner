<?php
session_start();
include 'config/db.php';
include 'includes/header.php';
include 'includes/navbar.php';

// 1. Create Plan
if(isset($_POST['save_plan'])){
    $v_name = mysqli_real_escape_string($conn, $_POST['visitor_name']);
    $v_date = $_POST['visit_date'];
    if(isset($_POST['place_id']) && is_array($_POST['place_id'])){
        if(mysqli_query($conn, "INSERT INTO visit_plan (visitor_name, visit_date) VALUES ('$v_name', '$v_date')")){
            $plan_id = mysqli_insert_id($conn); $order = 1;
            foreach(array_unique($_POST['place_id']) as $p_id){
                mysqli_query($conn, "INSERT INTO visit_plan_details (plan_id, place_id, visit_order) VALUES ($plan_id, ".intval($p_id).", $order++)");
            }
            echo "<div class='alert alert-success text-center mt-3'>✅ Visit Plan Created!</div>";
        }
    }
}
// 2. Delete Plan
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM visit_plan_details WHERE plan_id=$id");
    mysqli_query($conn, "DELETE FROM visit_plan WHERE plan_id=$id");
    header("Location: planner.php"); exit();
}
?>

<div class="container mt-5">
    <h2 class="text-center mb-4 fw-bold text-success">📅 One-Day Visit Planner</h2>
    
    <div class="card shadow mb-4 border-0">
        <div class="card-header bg-success text-white"><h5 class="mb-0">Create New Plan</h5></div>
        <div class="card-body">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Visitor Name</label>
                        <input type="text" name="visitor_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Visit Date</label>
                        <input type="date" name="visit_date" class="form-control" required>
                    </div>
                </div>
                
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Tourist Places:</label>
                    <div class="row">
                        <?php 
                        $res = mysqli_query($conn, "SELECT place_id, place_name, distance FROM tourist_places ORDER BY place_name");
                        while($p = mysqli_fetch_assoc($res)): 
                        ?>
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="form-check border rounded p-2 bg-light hover-shadow" style="cursor:pointer">
                                <input class="form-check-input place-cb" type="checkbox" name="place_id[]" value="<?= $p['place_id'] ?>" id="p<?= $p['place_id'] ?>" data-dist="<?= floatval($p['distance']) ?>">
                                <label class="form-check-label w-100" for="p<?= $p['place_id'] ?>">
                                    <?= htmlspecialchars($p['place_name']) ?> 
                                    <span class="badge bg-info text-dark"><?= $p['distance'] ?></span>
                                </label>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Live Distance Calculator -->
                <div class="alert alert-info d-flex justify-content-between align-items-center mt-3">
                    <span class="fw-bold">📍 Estimated Total Distance:</span>
                    <span class="fs-5 fw-bold text-primary" id="totalDist">0.00 km</span>
                </div>

                <button type="submit" name="save_plan" class="btn btn-success w-100 fw-bold">🚀 Create Visit Plan</button>
            </form>
        </div>
    </div>

    <h3 class="mb-3 fw-bold">📋 Planned Visits</h3>
    <div class="card shadow border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Visitor</th>
                        <th>Date</th>
                        <th>Tourist Places</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT vp.plan_id, vp.visitor_name, vp.visit_date, 
                            GROUP_CONCAT(tp.place_name SEPARATOR ' ➔ ') AS places 
                            FROM visit_plan vp 
                            JOIN visit_plan_details vpd ON vp.plan_id = vpd.plan_id 
                            JOIN tourist_places tp ON vpd.place_id = tp.place_id 
                            GROUP BY vp.plan_id 
                            ORDER BY vp.visit_date DESC";
                    $result = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($result) > 0): 
                        while($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= $row['plan_id'] ?></span></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['visitor_name']) ?></td>
                        <td><?= date('d M Y', strtotime($row['visit_date'])) ?></td>
                        <td class="small"><?= htmlspecialchars($row['places']) ?></td>
                        <td class="text-center">
                            <a href="export-plan.php?id=<?= $row['plan_id'] ?>" class="btn btn-primary btn-sm">📄 PDF</a>
                            <a href="planner.php?delete=<?= $row['plan_id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this plan?')">🗑️</a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                    <tr>
                        <td colspan="5" class="text-center py-4 text-muted">No Visit Plans Available. Create one above! ☝️</td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Live Distance Calculation Script -->
<script>
document.querySelectorAll('.place-cb').forEach(cb => {
    cb.addEventListener('change', () => {
        let total = 0;
        document.querySelectorAll('.place-cb:checked').forEach(c => total += parseFloat(c.dataset.dist) || 0);
        let el = document.getElementById('totalDist');
        el.innerText = total.toFixed(2) + ' km';
        // Change color to warning if distance is too high for one day (> 40km)
        el.parentElement.className = total > 40 ? 'alert alert-warning d-flex justify-content-between align-items-center mt-3' : 'alert alert-info d-flex justify-content-between align-items-center mt-3';
    });
});
</script>

<style>
.hover-shadow:hover { background-color: #e8f5e9 !important; border-color: #198754 !important; transform: translateY(-2px); }
</style>

<?php include 'includes/footer.php'; ?>