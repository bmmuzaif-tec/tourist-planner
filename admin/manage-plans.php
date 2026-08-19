<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';


// Fetch Visit Plans

$sql = "SELECT 

vp.plan_id,
vp.visitor_name,
vp.visit_date,
COUNT(vpd.place_id) AS total_places

FROM visit_plan vp

LEFT JOIN visit_plan_details vpd

ON vp.plan_id = vpd.plan_id

GROUP BY vp.plan_id

ORDER BY vp.visit_date DESC";


$result = mysqli_query($conn,$sql);


?>


<!DOCTYPE html>

<html>

<head>

<title>
Manage Visit Plans
</title>


<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">


</head>


<body>


<div class="container mt-5">


<h2 class="text-success">

Visit Plan Management

</h2>



<table class="table table-bordered table-striped mt-4">


<thead class="table-success">

<tr>

<th>ID</th>

<th>Visitor Name</th>

<th>Visit Date</th>

<th>Total Places</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

if(mysqli_num_rows($result)>0){

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<?php echo $row['plan_id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['visitor_name']); ?>

</td>



<td>

<?php echo $row['visit_date']; ?>

</td>

<td>

<?php echo $row['total_places']; ?>

</td>

<td>

<a 

href="view-plan.php?id=<?php echo $row['plan_id'];?>"

class="btn btn-primary btn-sm">

View

</a>

<a

href="delete-plan.php?id=<?php echo $row['plan_id'];?>"

class="btn btn-danger btn-sm"

onclick="return confirm('Delete this visit plan?')">

Delete

</a>

</td>
</tr>

<?php

}

}

else{


echo "

<tr>

<td colspan='5' class='text-center'>

No Visit Plans Found

</td>

</tr>";

}

?>

</tbody>


</table>

<a href="dashboard.php" class="btn btn-secondary">

Back Dashboard

</a>
</div>
</body>
</html>