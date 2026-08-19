<?php

session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$sql = "SELECT 

reviews.*,

tourist_places.place_name

FROM reviews

JOIN tourist_places

ON reviews.place_id = tourist_places.place_id ORDER BY review_date DESC";

$result = mysqli_query($conn,$sql);

?>

<!DOCTYPE html>

<html>

<head>

<title>
Manage Reviews
</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2 class="text-success">
Review Management

</h2>

<table class="table table-bordered table-striped mt-4">

<tr>

<th>ID</th>

<th>Place</th>

<th>Visitor</th>

<th>Rating</th>

<th>Comment</th>

<th>Date</th>

<th>Action</th>

</tr>
<?php

while($row=mysqli_fetch_assoc($result)){

?>
<tr>
<td>
<?php echo $row['review_id']; ?>
</td>

<td>
<?php echo $row['place_name']; ?>
</td>

<td>
<?php echo $row['visitor_name']; ?>
</td>
<td>

<?php

for($i=1;$i<=$row['rating'];$i++){

echo "⭐";

}
?>
</td>

<td>
<?php echo $row['comment']; ?>
</td>

<td>
<?php echo $row['review_date']; ?>
</td>

<td>
<a
href="delete-review.php?id=<?php echo $row['review_id'];?>"
class="btn btn-danger btn-sm"
onclick="return confirm('Delete this review?')">
Delete
</a>
</td>

</tr>
<?php
}
?>
</table>

</div>


</body>

</html>