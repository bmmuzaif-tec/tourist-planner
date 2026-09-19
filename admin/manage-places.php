<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$sql = "SELECT tp.*, c.category_name
        FROM tourist_places tp
        JOIN categories c
        ON tp.category_id = c.category_id
        ORDER BY tp.place_id DESC";

$result = mysqli_query($conn,$sql);
?>

<!DOCTYPE html>
<html>
<head>

<title>Manage Tourist Places</title>

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

<div class="d-flex justify-content-between mb-4">

<h2>Manage Tourist Places</h2>

<a href="add-place.php" class="btn btn-success">

➕ Add New Place

</a>

</div>

<table class="table table-bordered table-hover">

<thead class="table-success">

<tr>

<th>ID</th>
<th>Image</th>
<th>Name</th>
<th>Category</th>
<th>Distance</th>
<th>Action</th>

</tr>

</thead>

<tbody>

<?php while($row=mysqli_fetch_assoc($result)){ ?>

<tr>

<td><?php echo $row['place_id']; ?></td>

<td>

<img
src="../assets/images/places/<?php echo $row['image'];?>"
width="100"
height="70"
style="object-fit:cover;">

</td>

<td><?php echo htmlspecialchars($row['place_name']); ?></td>

<td><?php echo htmlspecialchars($row['category_name']); ?></td>

<td><?php echo $row['distance']; ?> km</td>

<td>

<a
href="edit-place.php?id=<?php echo $row['place_id'];?>"
class="btn btn-warning btn-sm">

Edit

</a>

<a
href="delete-place.php?id=<?php echo $row['place_id'];?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this place?')">

Delete

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<a href="dashboard.php" class="btn btn-secondary">

← Back to Dashboard

</a>

</div>

</body>

</html>