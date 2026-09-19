<?php
/**
 * Database Configuration Template
 * 
 * INSTRUCTIONS:
 * 1. Copy this file to `db.php`
 * 2. Update with your actual database credentials
 * 3. Do NOT commit db.php to version control
 */

$conn = mysqli_connect("localhost", "your_username", "your_password", "your_database");

if (!$conn) {
    die("Database connection failed: " . mysqli_connect_error());
}
?>