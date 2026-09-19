<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$message = "";
$edit = false;
$category_id = "";
$category_name = "";

/* ---------------- DELETE ---------------- */

if (isset($_GET['delete'])) {

    $id = intval($_GET['delete']);

    mysqli_query($conn, "DELETE FROM categories WHERE category_id=$id");

    $message = "<div class='alert alert-success'>
                Category Deleted Successfully.
                </div>";
}

/* ---------------- EDIT ---------------- */

if (isset($_GET['edit'])) {

    $edit = true;

    $id = intval($_GET['edit']);

    $result = mysqli_query($conn,
    "SELECT * FROM categories WHERE category_id=$id");

    $row = mysqli_fetch_assoc($result);

    $category_id = $row['category_id'];
    $category_name = $row['category_name'];
}

/* ---------------- SAVE ---------------- */

if (isset($_POST['save'])) {

    $name = mysqli_real_escape_string($conn,$_POST['category_name']);

    mysqli_query($conn,
    "INSERT INTO categories(category_name)
    VALUES('$name')");

    $message = "<div class='alert alert-success'>
                Category Added Successfully.
                </div>";
}

/* ---------------- UPDATE ---------------- */

if (isset($_POST['update'])) {

    $id = intval($_POST['category_id']);

    $name = mysqli_real_escape_string($conn,$_POST['category_name']);

    mysqli_query($conn,
    "UPDATE categories
    SET category_name='$name'
    WHERE category_id=$id");

    $message = "<div class='alert alert-success'>
                Category Updated Successfully.
                </div>";

    $edit = false;
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Category Management</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">


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

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>

Category Management

</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<form method="POST">

<input
type="hidden"
name="category_id"
value="<?php echo $category_id; ?>">

<div class="mb-3">

<label class="form-label">

Category Name

</label>

<input
type="text"
name="category_name"
class="form-control"
required
value="<?php echo htmlspecialchars($category_name); ?>">

</div>

<?php if($edit){ ?>

<button
type="submit"
name="update"
class="btn btn-warning">

Update Category

</button>

<a
href="categories.php"
class="btn btn-secondary">

Cancel

</a>

<?php } else { ?>

<button
type="submit"
name="save"
class="btn btn-success">

Save Category

</button>

<?php } ?>

</form>

<hr>

<h4>

Category List

</h4>

<table class="table table-bordered table-hover">

<thead class="table-success">

<tr>

<th width="80">ID</th>

<th>Category Name</th>

<th width="180">Action</th>

</tr>

</thead>

<tbody>

<?php

$result = mysqli_query($conn,
"SELECT * FROM categories
ORDER BY category_id DESC");

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td>

<?php echo $row['category_id']; ?>

</td>

<td>

<?php echo htmlspecialchars($row['category_name']); ?>

</td>

<td>

<a
href="categories.php?edit=<?php echo $row['category_id']; ?>"
class="btn btn-warning btn-sm">

Edit

</a>

<a
href="categories.php?delete=<?php echo $row['category_id']; ?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this category?')">

Delete

</a>

</td>

</tr>

<?php } ?>

</tbody>

</table>

<a
href="dashboard.php"
class="btn btn-secondary">

← Back to Dashboard

</a>

</div>

</div>

</div>

</body>

</html>