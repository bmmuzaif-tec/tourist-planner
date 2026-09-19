<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

$message = "";

if(isset($_POST['save'])){

    $category_id = intval($_POST['category_id']);
    $place_name = mysqli_real_escape_string($conn,$_POST['place_name']);
    $description = mysqli_real_escape_string($conn,$_POST['description']);
    $address = mysqli_real_escape_string($conn,$_POST['address']);
    $opening_hours = mysqli_real_escape_string($conn,$_POST['opening_hours']);
    $travel_tips = mysqli_real_escape_string($conn,$_POST['travel_tips']);
    $distance = floatval($_POST['distance']);
    $latitude = $_POST['latitude'];
    $longitude = $_POST['longitude'];

    $image = "";

    if(isset($_FILES['image']) && $_FILES['image']['error']==0){

        $image = time()."_".$_FILES['image']['name'];

        move_uploaded_file(
            $_FILES['image']['tmp_name'],
            "../assets/images/places/".$image
        );

    }

    $sql = "INSERT INTO tourist_places
    (category_id,place_name,description,address,opening_hours,
    travel_tips,distance,latitude,longitude,image)

    VALUES(

    '$category_id',

    '$place_name',

    '$description',

    '$address',

    '$opening_hours',

    '$travel_tips',

    '$distance',

    '$latitude',

    '$longitude',

    '$image'

    )";

    if(mysqli_query($conn,$sql)){

        $message = "<div class='alert alert-success'>
        Tourist Place Added Successfully.
        </div>";

    }else{

        $message = "<div class='alert alert-danger'>
        ".mysqli_error($conn)."
        </div>";

    }

}
?>

<!DOCTYPE html>

<html>

<head>

<title>Add Tourist Place</title>

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

<body>

<div class="container mt-5">

<h2>Add Tourist Place</h2>

<?php echo $message; ?>

<form
method="POST"
enctype="multipart/form-data">

<div class="mb-3">

<label>Category</label>

<select
name="category_id"
class="form-select"
required>

<option value="">Select Category</option>

<?php

$cat = mysqli_query($conn,"SELECT * FROM categories");

while($c=mysqli_fetch_assoc($cat)){

?>

<option value="<?php echo $c['category_id'];?>">

<?php echo $c['category_name'];?>

</option>

<?php } ?>

</select>

</div>

<div class="mb-3">

<label>Place Name</label>

<input
type="text"
name="place_name"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Description</label>

<textarea
name="description"
class="form-control"
rows="4"
required></textarea>

</div>

<div class="mb-3">

<label>Address</label>

<input
type="text"
name="address"
class="form-control"
required>

</div>

<div class="mb-3">

<label>Opening Hours</label>

<input
type="text"
name="opening_hours"
class="form-control">

</div>

<div class="mb-3">

<label>Travel Tips</label>

<textarea
name="travel_tips"
class="form-control"
rows="3"></textarea>

</div>

<div class="row">

<div class="col-md-4">

<label>Distance (km)</label>

<input
type="number"
step="0.1"
name="distance"
class="form-control"
required>

</div>

<div class="col-md-4">

<label>Latitude</label>

<input
type="text"
name="latitude"
class="form-control"
required>

</div>

<div class="col-md-4">

<label>Longitude</label>

<input
type="text"
name="longitude"
class="form-control"
required>

</div>

</div>

<div class="mt-3">

<label>Place Image</label>

<input
type="file"
name="image"
class="form-control"
accept="image/*"
required>

</div>

<div class="mt-4">

<button
type="submit"
name="save"
class="btn btn-success">

Save Tourist Place

</button>

<a
href="manage-places.php"
class="btn btn-secondary">

Back

</a>

</div>

</form>

</div>

</body>

</html>