<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$plan_id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if($plan_id > 0){
    // 1. Delete details first to avoid Foreign Key constraint issues
    $sql1 = "DELETE FROM visit_plan_details WHERE plan_id = ?";
    $stmt1 = mysqli_prepare($conn, $sql1);
    mysqli_stmt_bind_param($stmt1, "i", $plan_id);
    mysqli_stmt_execute($stmt1);

    // 2. Delete the main plan
    $sql2 = "DELETE FROM visit_plan WHERE plan_id = ?";
    $stmt2 = mysqli_prepare($conn, $sql2);
    mysqli_stmt_bind_param($stmt2, "i", $plan_id);
    mysqli_stmt_execute($stmt2);
}

// Redirect back to the manage plans page
header("Location: manage-plans.php");
exit();
?>