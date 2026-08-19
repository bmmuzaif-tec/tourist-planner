<?php
include 'config/db.php';

if(!isset($_GET['id'])){

    die("Invalid Tourist Place");

}

$place_id = intval($_GET['id']);
// Get Place Details

$place_query = mysqli_query($conn,

"SELECT * FROM tourist_places WHERE place_id=$place_id"
);

if(mysqli_num_rows($place_query)==0){

    die("Place Not Found");
}

$place = mysqli_fetch_assoc($place_query);

$message="";

// Submit Review

if(isset($_POST['submit'])){

    $visitor_name = mysqli_real_escape_string(
        $conn,
        $_POST['visitor_name']
    );

    $rating = intval($_POST['rating']);

    $comment = mysqli_real_escape_string(
        $conn,
        $_POST['comment']
    );

    $sql="INSERT INTO reviews

    (place_id, visitor_name, rating, comment)

    VALUES

    ('$place_id','$visitor_name','$rating','$comment')";

    if(mysqli_query($conn,$sql)){

        $message="Review submitted successfully!";

    }
    else{

        $message=mysqli_error($conn);
    }

}

?>

<!DOCTYPE html>

<html>

<head>

<title>
Write Review
</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<div class="card shadow">

<div class="card-body">

<h2 class="text-success">

Review :

<?php echo $place['place_name']; ?>

</h2>
<?php

if($message!=""){

echo "

<div class='alert alert-success'>

$message

</div>";
}

?>

<form method="POST">

<label>
Your Name
</label>

<input

type="text"

name="visitor_name"

class="form-control mb-3"

required>

<label>
Rating
</label>

<select

name="rating"

class="form-select mb-3"

required>

<option value="5">
⭐⭐⭐⭐⭐ Excellent
</option>

<option value="4">
⭐⭐⭐⭐ Very Good
</option>

<option value="3">
⭐⭐⭐ Good
</option>

<option value="2">
⭐⭐ Average
</option>
<option value="1">
⭐ Poor
</option>

</select>

<label>
Your Review
</label>

<textarea

name="comment"

class="form-control mb-3"

rows="4"

required>

</textarea>

<button

type="submit"

name="submit"

class="btn btn-success">

Submit Review

</button>

<a

href="place-details.php?id=<?php echo $place_id;?>"

class="btn btn-secondary">

Back

</a>
</form>
</div>
</div>
<!-- Existing Reviews -->
<div class="card shadow mt-4">
<div class="card-body">

<h3 class="text-success">
Visitor Reviews
</h3>

<?php

$reviews=mysqli_query($conn,

"SELECT * FROM reviews

WHERE place_id=$place_id

ORDER BY review_date DESC"

);
if(mysqli_num_rows($reviews)>0){

while($row=mysqli_fetch_assoc($reviews)){

?>
<hr>

<h5>

<?php echo $row['visitor_name']; ?>
</h5>
<p>

<?php

for($i=1;$i<=$row['rating'];$i++){

echo "⭐";

}

?>
</p>
<p>

<?php echo $row['comment']; ?>

</p>

<small>

<?php echo $row['review_date']; ?>

</small>
<?php
}

}
else{
echo "No reviews yet.";

}
?>
</div>
</div>
</div>
</body>
</html>