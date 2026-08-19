<?php

$host = "localhost";
$user = "root";
$password = "";
$database = "tourist_planner";

$conn = mysqli_connect($host, $user, $password, $database);

if (!$conn) {
    die("Database Connection Failed: " . mysqli_connect_error());
}

mysqli_set_charset($conn, "utf8mb4");
?>