<?php
session_start();

if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$message = "";

$edit = false;

$review_id = "";
$visitor_name = "";
$rating = "";
$comment = "";

/* DELETE */

if(isset($_GET['delete'])){

    $id = intval($_GET['delete']);

    mysqli_query($conn,"DELETE FROM reviews WHERE review_id=$id");

    $message = "<div class='alert alert-success'>
    Review Deleted Successfully.
    </div>";
}

/* EDIT */

if(isset($_GET['edit'])){

    $edit = true;

    $id = intval($_GET['edit']);

    $result = mysqli_query($conn,"
    SELECT *
    FROM reviews
    WHERE review_id=$id");

    $row = mysqli_fetch_assoc($result);

    $review_id = $row['review_id'];
    $visitor_name = $row['visitor_name'];
    $rating = $row['rating'];
    $comment = $row['comment'];
}

/* UPDATE */

if(isset($_POST['update'])){

    $id = intval($_POST['review_id']);

    $visitor_name = mysqli_real_escape_string($conn,$_POST['visitor_name']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn,$_POST['comment']);

    mysqli_query($conn,"
    UPDATE reviews

    SET

    visitor_name='$visitor_name',

    rating='$rating',

    comment='$comment'

    WHERE review_id=$id");

    $message = "<div class='alert alert-success'>
    Review Updated Successfully.
    </div>";

    $edit = false;
}

?>

<!DOCTYPE html>

<html>

<head>

<title>Manage Reviews</title>

<link
href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css"
rel="stylesheet">

</head>

<body class="bg-light">

<div class="container mt-5">

<div class="card shadow">

<div class="card-header bg-success text-white">

<h3>

Review Management

</h3>

</div>

<div class="card-body">

<?php echo $message; ?>

<?php if($edit){ ?>

<form method="POST">

<input
type="hidden"
name="review_id"
value="<?php echo $review_id; ?>">

<div class="mb-3">

<label>Visitor Name</label>

<input
type="text"
name="visitor_name"
class="form-control"
value="<?php echo htmlspecialchars($visitor_name); ?>"
required>

</div>

<div class="mb-3">

<label>Rating</label>

<select
name="rating"
class="form-select">

<?php

for($i=1;$i<=5;$i++){

?>

<option
value="<?php echo $i;?>"

<?php

if($rating==$i)
echo "selected";

?>

>

<?php echo $i; ?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label>Comment</label>

<textarea
name="comment"
class="form-control"
rows="4"
required><?php echo htmlspecialchars($comment); ?></textarea>

</div>

<button
type="submit"
name="update"
class="btn btn-warning">

Update Review

</button>

<a
href="reviews.php"
class="btn btn-secondary">

Cancel

</a>

</form>

<hr>

<?php } ?>

<table class="table table-bordered table-hover">

<thead class="table-success">

<tr>

<th>ID</th>

<th>Place</th>

<th>Visitor</th>

<th>Rating</th>

<th>Comment</th>

<th>Date</th>

<th>Action</th>

</tr>

</thead>

<tbody>

<?php

$sql="

SELECT

r.*,

tp.place_name

FROM reviews r

JOIN tourist_places tp

ON tp.place_id=r.place_id

ORDER BY review_id DESC";

$result=mysqli_query($conn,$sql);

while($row=mysqli_fetch_assoc($result)){

?>

<tr>

<td><?php echo $row['review_id']; ?></td>

<td><?php echo htmlspecialchars($row['place_name']); ?></td>

<td><?php echo htmlspecialchars($row['visitor_name']); ?></td>

<td><?php echo str_repeat("⭐",$row['rating']); ?></td>

<td><?php echo htmlspecialchars($row['comment']); ?></td>

<td><?php echo $row['review_date']; ?></td>

<td>

<a
href="reviews.php?edit=<?php echo $row['review_id'];?>"
class="btn btn-warning btn-sm">

Edit

</a>

<a
href="reviews.php?delete=<?php echo $row['review_id'];?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this review?')">

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