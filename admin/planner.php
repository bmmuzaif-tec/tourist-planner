<?php
session_start();
if(!isset($_SESSION['admin_id'])){ header("Location: login.php"); exit(); }
include '../config/db.php';

// 1. DELETE
if(isset($_GET['delete'])){
    $id = intval($_GET['delete']);
    mysqli_query($conn, "DELETE FROM visit_plan_details WHERE plan_id=$id");
    mysqli_query($conn, "DELETE FROM visit_plan WHERE plan_id=$id");
    header("Location: planner.php"); exit();
}

// 2. CREATE / UPDATE
if(isset($_POST['save'])){
    $v_name = mysqli_real_escape_string($conn, $_POST['visitor_name']);
    $v_date = $_POST['visit_date'];
    $places = $_POST['places'] ?? [];

    if(isset($_POST['plan_id']) && $_POST['plan_id'] != ""){
        $plan_id = intval($_POST['plan_id']);
        mysqli_query($conn, "UPDATE visit_plan SET visitor_name='$v_name', visit_date='$v_date' WHERE plan_id=$plan_id");
        mysqli_query($conn, "DELETE FROM visit_plan_details WHERE plan_id=$plan_id");
    } else {
        mysqli_query($conn, "INSERT INTO visit_plan (visitor_name, visit_date) VALUES ('$v_name', '$v_date')");
        $plan_id = mysqli_insert_id($conn);
    }

    $order = 1;
    if(!empty($places)){
        foreach($places as $place_id){
            mysqli_query($conn, "INSERT INTO visit_plan_details (plan_id, place_id, visit_order) VALUES ($plan_id, ".intval($place_id).", $order++)");
        }
    }
    header("Location: planner.php"); exit();
}

// 3. EDIT DATA
$edit = []; 
$edit_places = [];
if(isset($_GET['edit'])){
    $id = intval($_GET['edit']);
    $edit = mysqli_fetch_assoc(mysqli_query($conn, "SELECT * FROM visit_plan WHERE plan_id=$id"));
    $res = mysqli_query($conn, "SELECT place_id FROM visit_plan_details WHERE plan_id=$id");
    while($row = mysqli_fetch_assoc($res)) $edit_places[] = $row['place_id'];
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Visit Plans</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
</head>
<body class="bg-light">
<div class="container mt-5">
    <h2 class="text-success fw-bold mb-4">📅 Manage Visit Plans</h2>
    
    <!-- Form -->
    <div class="card shadow-sm mb-5 border-0">
        <div class="card-body p-4">
            <form method="POST">
                <input type="hidden" name="plan_id" value="<?= $edit['plan_id'] ?? '' ?>">
                <div class="row mb-3">
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Visitor Name</label>
                        <input type="text" name="visitor_name" class="form-control" value="<?= htmlspecialchars($edit['visitor_name'] ?? '') ?>" required>
                    </div>
                    <div class="col-md-6">
                        <label class="form-label fw-bold">Visit Date</label>
                        <input type="date" name="visit_date" class="form-control" value="<?= $edit['visit_date'] ?? '' ?>" required>
                    </div>
                </div>
                <div class="mb-3">
                    <label class="form-label fw-bold">Select Places</label>
                    <div class="row">
                        <?php 
                        $places_q = mysqli_query($conn, "SELECT place_id, place_name FROM tourist_places ORDER BY place_name");
                        while($p = mysqli_fetch_assoc($places_q)): 
                            $checked = in_array($p['place_id'], $edit_places) ? 'checked' : '';
                        ?>
                        <div class="col-md-4 col-sm-6 mb-2">
                            <div class="form-check border rounded p-2 bg-light">
                                <input class="form-check-input" type="checkbox" name="places[]" value="<?= $p['place_id'] ?>" id="p<?= $p['place_id'] ?>" <?= $checked ?>>
                                <label class="form-check-label" for="p<?= $p['place_id'] ?>"><?= htmlspecialchars($p['place_name']) ?></label>
                            </div>
                        </div>
                        <?php endwhile; ?>
                    </div>
                </div>
                <button type="submit" name="save" class="btn btn-success w-100 fw-bold">💾 Save Plan</button>
            </form>
        </div>
    </div>

    <!-- Table -->
    <h3 class="fw-bold mb-3">All Visit Plans</h3>
    <div class="card shadow-sm border-0">
        <div class="table-responsive">
            <table class="table table-hover mb-0">
                <thead class="table-success">
                    <tr>
                        <th>ID</th>
                        <th>Visitor</th>
                        <th>Date</th>
                        <th>Selected Places</th>
                        <th class="text-center">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php 
                    $sql = "SELECT vp.plan_id, vp.visitor_name, vp.visit_date, 
                            GROUP_CONCAT(tp.place_name SEPARATOR ', ') AS places 
                            FROM visit_plan vp 
                            LEFT JOIN visit_plan_details vpd ON vp.plan_id = vpd.plan_id 
                            LEFT JOIN tourist_places tp ON vpd.place_id = tp.place_id 
                            GROUP BY vp.plan_id ORDER BY vp.visit_date DESC";
                    $result = mysqli_query($conn, $sql);
                    if(mysqli_num_rows($result) > 0): 
                        while($row = mysqli_fetch_assoc($result)): 
                    ?>
                    <tr>
                        <td><span class="badge bg-secondary">#<?= $row['plan_id'] ?></span></td>
                        <td class="fw-bold"><?= htmlspecialchars($row['visitor_name']) ?></td>
                        <td><?= date('d M Y', strtotime($row['visit_date'])) ?></td>
                        <td class="small text-muted"><?= htmlspecialchars($row['places'] ?? 'None') ?></td>
                        <td class="text-center">
                            <a href="planner.php?edit=<?= $row['plan_id'] ?>" class="btn btn-primary btn-sm">✏️ Edit</a>
                            <a href="planner.php?delete=<?= $row['plan_id'] ?>" class="btn btn-outline-danger btn-sm" onclick="return confirm('Delete this plan?')">🗑️ Delete</a>
                        </td>
                    </tr>
                    <?php endwhile; else: ?>
                    <tr><td colspan="5" class="text-center py-4 text-muted">No visit plans found.</td></tr>
                    <?php endif; ?>
                </tbody>
            </table>
        </div>
    </div>
</div>
</body>
</html>