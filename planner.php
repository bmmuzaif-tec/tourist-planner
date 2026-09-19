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
                mysqli_query($conn, "INSERT INTO visit_plan_details (plan_id, place_id, visit_order) VALUES ($plan_id, ".intval($p_id).", $order)");
            }
            echo "<div class='container mt-3'><div class='alert alert-success text-center'><i class=\"bi bi-check-circle\"></i> Visit Plan Created!</div></div>";
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

<section class="section pb-0">
    <div class="container">
        <div class="text-center mb-4">
            <span class="eyebrow">One-day trip builder</span>
            <h1 class="section-title">Plan Your Perfect Day</h1>
            <p class="section-subtitle mx-auto">Choose your favourite destinations and create a one-day itinerary.</p>
        </div>
    </div>
</section>

<div class="container mb-5">
    <div class="card shadow-sm mb-4 border-0">
        <div class="card-header bg-success text-white"><h5 class="mb-0"><i class="bi bi-pencil-square"></i> Create New Plan</h5></div>
        <div class="card-body p-4">
            <form method="POST">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label">Visitor Name</label>
                        <input type="text" name="visitor_name" class="form-control" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label">Visit Date</label>
                        <input type="date" name="visit_date" class="form-control" required>
                    </div>
                </div>

                <div class="mb-3">
                    <label class="form-label">Select Tourist Places</label>
                    <div class="row g-2">
                        <?php 
                        $res = mysqli_query($conn, "SELECT place_id, place_name, distance FROM tourist_places ORDER BY place_name");
                        while($p = mysqli_fetch_assoc($res)): 
                        ?>
                        <div class="col-md-4 col-sm-6">
                            <label class="selectable-card d-flex align-items-center gap-2 mb-0" for="p<?= $p['place_id'] ?>">
                                <input class="form-check-input place-cb m-0" type="checkbox" name="place_id[]" value="<?= $p['place_id'] ?>" id="p<?= $p['place_id'] ?>" data-dist="<?= floatval($p['distance']) ?>" data-name="<?= htmlspecialchars($p['place_name'], ENT_QUOTES) ?>">
                                <span class="flex-grow-1">
                                    <?= htmlspecialchars($p['place_name']) ?>
                                    <span class="badge bg-secondary float-end"><?= $p['distance'] ?> km</span>
                                </span>
                            </label>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>

                <!-- Trip Summary (glass panel) -->
                <div class="trip-summary glass-panel mt-3" id="tripSummary">
                    <div class="d-flex justify-content-between align-items-center mb-2">
                        <span class="fw-bold"><i class="bi bi-signpost-2"></i> Estimated Total Distance</span>
                        <span class="fs-5 fw-bold" style="color:var(--primary)" id="totalDist">0.00 km</span>
                    </div>
                    <div id="itineraryWrap" style="display:none;">
                        <hr>
                        <p class="fw-semibold small text-muted mb-2">Your Itinerary</p>
                        <ul class="itinerary-list" id="itineraryList"></ul>
                    </div>
                </div>

                <button type="submit" name="save_plan" class="btn btn-success w-100 mt-3"><i class="bi bi-rocket-takeoff"></i> Create My Visit Plan &rarr;</button>
            </form>
        </div>
    </div>

    <h3 class="fw-bold mb-3"><i class="bi bi-list-check"></i> Planned Visits</h3>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0 align-middle">
                <thead style="background:rgba(132, 0, 77, 0.08);">
                    <tr>
                        <th>ID</th>
                        <th>Visitor</th>
                        <th>Date</th>
                        <th>Itinerary</th>
                        <th class="text-center">Action</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT vp.plan_id, vp.visitor_name, vp.visit_date, 
                            GROUP_CONCAT(tp.place_name SEPARATOR ' &rarr; ') AS places 
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
                        <td class="small"><?= $row['places'] ?></td>
                        <td class="text-center">
                            <a href="export-plan.php?id=<?= $row['plan_id'] ?>" class="btn btn-success btn-sm"><i class="bi bi-file-earmark-pdf"></i> PDF</a>
                            <a href="planner.php?delete=<?= $row['plan_id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this plan?')"><i class="bi bi-trash"></i></a>
                        </td>
                    </tr>
                    <?php 
                        endwhile; 
                    else: 
                    ?>
                    <tr>
                        <td colspan="5">
                            <div class="empty-state">
                                <div class="empty-icon"><i class="bi bi-calendar-x"></i></div>
                                <p class="mb-0">No visit plans yet. Create one above!</p>
                            </div>
                        </td>
                    </tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>

<!-- Live Distance Calculation Script -->
<script>
function refreshTrip() {
    const checked = document.querySelectorAll('.place-cb:checked');
    let total = 0;
    checked.forEach(c => total += parseFloat(c.dataset.dist) || 0);
    document.getElementById('totalDist').innerText = total.toFixed(2) + ' km';

    const wrap = document.getElementById('itineraryWrap');
    const list = document.getElementById('itineraryList');
    list.innerHTML = '';
    if (checked.length > 0) {
        wrap.style.display = 'block';
        checked.forEach((c, i) => {
            const li = document.createElement('li');
            li.innerHTML = '<span class="step-num">' + String(i + 1).padStart(2, '0') + '</span>' +
                '<span class="flex-grow-1">' + c.dataset.name + '</span>' +
                (i < checked.length - 1 ? '' : '');
            list.appendChild(li);
        });
    } else {
        wrap.style.display = 'none';
    }
}

document.querySelectorAll('.place-cb').forEach(cb => {
    cb.addEventListener('change', () => {
        cb.closest('.selectable-card').classList.toggle('selected', cb.checked);
        refreshTrip();
    });
});
</script>

<?php include 'includes/footer.php'; ?>
