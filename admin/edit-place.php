<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

if(!isset($_GET['id'])){
    die("Invalid Place ID");
}

$id = intval($_GET['id']);

$result = mysqli_query($conn,"SELECT * FROM tourist_places WHERE place_id=$id");

if(mysqli_num_rows($result)==0){
    die("Place Not Found");
}

$row = mysqli_fetch_assoc($result);

$message="";

if(isset($_POST['update'])){

    $category_id = intval($_POST['category_id']);
    $place_name = mysqli_real_escape_string($conn,$_POST['place_name']);
    $description = mysqli_real_escape_string($conn,$_POST['description']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);
    $opening_hours = mysqli_real_escape_string($conn,$_POST['opening_hours']);
    $travel_tips = mysqli_real_escape_string($conn,$_POST['travel_tips']);
    $distance = $_POST['distance'];
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $image = $row['image'];

    if($_FILES['image']['error']==0){

        $image = time()."_".$_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/images/places/".$image
        );

    }

    $sql="UPDATE tourist_places SET

    category_id='$category_id',
    place_name='$place_name',
    description='$description',
    address='$address',
    opening_hours='$opening_hours',
    travel_tips='$travel_tips',
    distance='$distance',
    latitude='$latitude',
    longitude='$longitude',
    image='$image'

    WHERE place_id=$id";

    if(mysqli_query($conn,$sql)){

        header("Location: manage-places.php");
        exit();

    }else{

        $message=mysqli_error($conn);

    }

}
?>

<!DOCTYPE html>

<html>

<head>

<title>Edit Tourist Place</title>

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">

</head>

<body>

<div class="container mt-5">

<h2>Edit Tourist Place</h2>

<?php
if($message!=""){
echo "<div class='alert alert-danger'>$message</div>";
}
?>

<form method="POST" enctype="multipart/form-data">

<label>Category</label>

<select name="category_id" class="form-select mb-3">

<?php

$cat=mysqli_query($conn,"SELECT * FROM categories");

while($c=mysqli_fetch_assoc($cat)){

?>

<option
value="<?php echo $c['category_id'];?>"

<?php
if($c['category_id']==$row['category_id'])
echo "selected";
?>

>

<?php echo $c['category_name'];?>

</option>

<?php } ?>

</select>

<label>Place Name</label>

<input
type="text"
name="place_name"
class="form-control mb-3"
value="<?php echo htmlspecialchars($row['place_name']);?>">

<label>Description</label>

<textarea
name="description"
class="form-control mb-3"
rows="4"><?php echo htmlspecialchars($row['description']);?></textarea>

<label>Address</label>

<input
type="text"
name="address"
class="form-control mb-3"
value="<?php echo htmlspecialchars($row['address']);?>">

<label>Opening Hours</label>

<input
type="text"
name="opening_hours"
class="form-control mb-3"
value="<?php echo htmlspecialchars($row['opening_hours']);?>">

<label>Travel Tips</label>

<textarea
name="travel_tips"
class="form-control mb-3"
rows="3"><?php echo htmlspecialchars($row['travel_tips']);?></textarea>

<div class="row">

<div class="col-md-4">

<label>Distance</label>

<input
type="number"
step="0.1"
name="distance"
class="form-control"
value="<?php echo $row['distance'];?>">

</div>

<div class="col-md-4">

<label>Latitude</label>

<input
type="text"
name="latitude"
class="form-control"
value="<?php echo $row['latitude'];?>">

</div>

<div class="col-md-4">

<label>Longitude</label>

<input
type="text"
name="longitude"
class="form-control"
value="<?php echo $row['longitude'];?>">

</div>

</div>

<br>

<img
src="../assets/images/places/<?php echo $row['image'];?>"
width="200"
class="mb-3">

<input
type="file"
name="image"
class="form-control mb-3">

<button
type="submit"
name="update"
class="btn btn-success">

Update Tourist Place

</button>

<a
href="manage-places.php"
class="btn btn-secondary">

Cancel

</a>

</form>

</div>

</body>

</html>