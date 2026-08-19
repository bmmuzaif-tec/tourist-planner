<?php
include 'includes/header.php';
include 'includes/navbar.php';
?>

<div class="container mt-5 mb-5">
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="fw-bold text-success display-5">About Our Tourist Planner</h1>
        <p class="lead text-muted">Promoting Local Tourism near Eravur </p>
        <hr class="w-25 mx-auto text-success" style="height: 3px; opacity: 1;">
    </div>

    <!-- Project Overview -->
    <div class="row align-items-center mb-5">
        <div class="col-md-7">
            <h2 class="fw-bold text-success mb-3">🌍 Local Tourist Day Visit Planner</h2>
            <p class="text-muted fs-5">
                Welcome to the <strong>Local Tourist Day Visit Planner</strong>, a dedicated web application designed to showcase the beautiful, hidden, and popular tourist destinations within a <strong class="text-dark">25km radius of Eravur</strong>, Sri Lanka.
            </p>
            <p class="text-muted">
                Whether you are a local resident looking for a weekend getaway or a tourist visiting the Eastern Province, our platform helps you discover historical forts, serene beaches, natural lagoons, and authentic culinary spots effortlessly.
            </p>
        </div>
        <div class="col-md-5 text-center">
            <div class="bg-light p-4 rounded-circle shadow-sm d-inline-block border border-success border-3">
                <span style="font-size: 100px;">🗺️</span>
            </div>
        </div>
    </div>

    <!-- Key Features -->
    <h2 class="text-center fw-bold text-success mb-4">✨ Key Features</h2>
    <div class="row g-4 mb-5">
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 text-center p-4 hover-card">
                <div class="mb-3"><span style="font-size: 50px;">🏖️</span></div>
                <h5 class="fw-bold">Explore Local Places</h5>
                <p class="text-muted small">Discover beaches, historical sites, nature spots, and restaurants all within 25km of Eravur.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 text-center p-4 hover-card">
                <div class="mb-3"><span style="font-size: 50px;">📅</span></div>
                <h5 class="fw-bold">One-Day Trip Planner</h5>
                <p class="text-muted small">Select multiple places and let our smart planner calculate the total distance for your perfect day trip.</p>
            </div>
        </div>
        <div class="col-md-4">
            <div class="card h-100 shadow-sm border-0 text-center p-4 hover-card">
                <div class="mb-3"><span style="font-size: 50px;">⭐</span></div>
                <h5 class="fw-bold">Reviews & Ratings</h5>
                <p class="text-muted small">Read honest reviews from other visitors and share your own experiences to help the local community.</p>
            </div>
        </div>
    </div>

    <!-- Mission Section -->
    <div class="bg-success text-white rounded p-5 text-center shadow-lg">
        <h2 class="fw-bold mb-3">🎯 Our Mission</h2>
        <p class="lead mb-0 mx-auto" style="max-width: 800px;">
            To boost local tourism in the Batticaloa and Eravur regions by providing a centralized, easy-to-use digital platform that connects travelers with the natural and historical beauty of the Eastern Province.
        </p>
    </div>

</div>

<!-- Simple Hover Effect for Feature Cards -->
<style>
    .hover-card {
        transition: transform 0.3s ease, box-shadow 0.3s ease;
    }
    .hover-card:hover {
        transform: translateY(-5px);
        box-shadow: 0 10px 20px rgba(0,0,0,0.1) !important;
    }
</style>

<?php include 'includes/footer.php'; ?>