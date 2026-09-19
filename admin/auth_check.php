<?php
session_start();

// 1. Check if user is logged in
if (!isset($_SESSION['admin_id'])) {
    header("Location: login.php");
    exit();
}

// 2.Session Timeout (SRS Section 5.3)
$timeout_duration = 60; // 

if (isset($_SESSION['LAST_ACTIVITY']) && (time() - $_SESSION['LAST_ACTIVITY'] > $timeout_duration)) {
    // Session expired, destroy it
    session_unset();
    session_destroy();
    // Redirect to login with a message
    header("Location: login.php?msg=timeout");
    exit();
}

// 3. Update last activity timestamp
$_SESSION['LAST_ACTIVITY'] = time();
?>