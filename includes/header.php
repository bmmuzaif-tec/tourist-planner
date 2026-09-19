<?php
// Dynamic page title
$pageTitle = $pageTitle ?? 'Tourist Planner | Discover Eravur & Batticaloa';
// Base path for admin folder
$basePath = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin') ? '../' : '';
?>
<!DOCTYPE html>
<html lang="en">
<head>
<meta charset="UTF-8">
<meta name="viewport" content="width=device-width, initial-scale=1">

<title><?= htmlspecialchars($pageTitle) ?></title>

<!-- SEO -->
<meta name="description" content="Discover tourist places within 25km of Eravur, Batticaloa. Plan your perfect one-day trip with our interactive planner.">
<meta name="keywords" content="Eravur, Batticaloa, Sri Lanka tourism, day trip planner, Pasikudah, Kallady Bridge">
<meta property="og:title" content="Tourist Planner — Eravur Go">
<meta property="og:description" content="Explore beaches, forts, and lagoons around Eravur.">
<meta property="og:type" content="website">

<link rel="preconnect" href="https://fonts.googleapis.com">
<link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">

<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">

<link rel="stylesheet" href="<?= $basePath ?>assets/css/style.css">
</head>
<body></body>