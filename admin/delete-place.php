<?php
session_start();

if(!isset($_SESSION['admin_id'])){
    header("Location: login.php");
    exit();
}

include '../config/db.php';

if(isset($_GET['id'])){

    $id = intval($_GET['id']);

    // Get image filename
    $result = mysqli_query($conn,"SELECT image FROM tourist_places WHERE place_id=$id");

    if($row = mysqli_fetch_assoc($result)){
        $imagePath = "../assets/images/places/".$row['image'];

        if(file_exists($imagePath)){
            unlink($imagePath);
        }
    }

    mysqli_query($conn,"DELETE FROM tourist_places WHERE place_id=$id");
}

header("Location: manage-places.php");
exit();
?>