<?php
include 'includes/header.php';
include 'includes/navbar.php';
?>

<!-- =====================================================
     PAGE HEADER — Booking.com style
     ===================================================== -->
<section class="section pb-0 about-header">
    <div class="container text-center mb-5 reveal">
        <span class="eyebrow">Our Story</span>
        <h1 class="section-title">About Tourist Planner</h1>
        <p class="section-subtitle mx-auto">Promoting local tourism near Eravur.</p>
    </div>
</section>

<div class="container mb-5">

    <!-- =====================================================
         INTRO SECTION — Two-column layout
         ===================================================== -->
    <div class="row align-items-center g-5 mb-5 about-intro">
        <div class="col-md-7 reveal">
            <h2 class="fw-bold mb-3" style="color:var(--primary); font-size:1.8rem;">
                Local Tourist Day Visit Planner
            </h2>
            <p class="text-muted fs-5 mb-3">
                Welcome to the <strong>Local Tourist Day Visit Planner</strong>, a dedicated web application designed to showcase the beautiful, hidden, and popular tourist destinations within a <strong class="text-dark">25km radius of Eravur</strong>, Sri Lanka.
            </p>
            <p class="text-muted mb-4">
                Whether you are a local resident looking for a weekend getaway or a tourist visiting the Eastern Province, our platform helps you discover historical forts, serene beaches, natural lagoons, and authentic culinary spots effortlessly.
            </p>

            <!-- Booking.com style CTA buttons -->
            <div class="d-flex flex-wrap gap-2 about-cta">
                <a href="places.php" class="btn btn-success btn-lg">
                    <i class="bi bi-compass"></i> Explore Places
                </a>
                <a href="planner.php" class="btn btn-outline-success btn-lg">
                    <i class="bi bi-calendar-plus"></i> Plan Your Trip
                </a>
            </div>
        </div>
        <div class="col-md-5 text-center reveal reveal-delay-2">
            <div class="about-hero-card">
                <div class="feature-icon about-hero-icon">
                    <i class="bi bi-map"></i>
                </div>
                <h4 class="fw-bold mb-2">Discover Eravur</h4>
                <p class="text-muted small mb-0">Your gateway to the Eastern Province's hidden gems</p>
            </div>
        </div>
    </div>

    <!-- =====================================================
         TRUST STATS BAR — Booking.com style
         ===================================================== -->
    <div class="trust-stats-bar reveal mb-5">
        <div class="trust-stat">
            <i class="bi bi-geo-alt-fill"></i>
            <div>
                <strong>11+</strong>
                <span>Places</span>
            </div>
        </div>
        <div class="trust-stat">
            <i class="bi bi-tags-fill"></i>
            <div>
                <strong>6</strong>
                <span>Categories</span>
            </div>
        </div>
        <div class="trust-stat">
            <i class="bi bi-calendar-check-fill"></i>
            <div>
                <strong>7+</strong>
                <span>Visit Plans</span>
            </div>
        </div>
        <div class="trust-stat">
            <i class="bi bi-star-fill"></i>
            <div>
                <strong>100%</strong>
                <span>Local Focus</span>
            </div>
        </div>
    </div>

    <!-- =====================================================
         KEY FEATURES — Booking.com card style
         ===================================================== -->
    <div class="text-center mb-4 reveal">
        <span class="eyebrow">What you get</span>
        <h2 class="section-title">Key Features</h2>
    </div>
    <div class="row g-4 mb-5">
        <div class="col-md-4 reveal reveal-delay-1">
            <div class="card h-100 border-0 shadow-sm p-4 about-feature-card">
                <div class="feature-icon"><i class="bi bi-umbrella-beach"></i></div>
                <h5 class="fw-bold mb-2">Explore Local Places</h5>
                <p class="text-muted small mb-0">Discover beaches, historical sites, nature spots, and restaurants all within 25km of Eravur.</p>
            </div>
        </div>
        <div class="col-md-4 reveal reveal-delay-2">
            <div class="card h-100 border-0 shadow-sm p-4 about-feature-card">
                <div class="feature-icon"><i class="bi bi-calendar2-week"></i></div>
                <h5 class="fw-bold mb-2">One-Day Trip Planner</h5>
                <p class="text-muted small mb-0">Select multiple places and let our smart planner calculate the total distance for your perfect day trip.</p>
            </div>
        </div>
        <div class="col-md-4 reveal reveal-delay-3">
            <div class="card h-100 border-0 shadow-sm p-4 about-feature-card">
                <div class="feature-icon"><i class="bi bi-star"></i></div>
                <h5 class="fw-bold mb-2">Reviews &amp; Ratings</h5>
                <p class="text-muted small mb-0">Read honest reviews from other visitors and share your own experiences to help the local community.</p>
            </div>
        </div>
    </div>

    <!-- =====================================================
         MISSION SECTION — Booking.com style
         ===================================================== -->
    <div class="mission-banner reveal">
        <div class="mission-content text-center">
            <div class="mission-icon">
                <i class="bi bi-compass"></i>
            </div>
            <h2 class="fw-bold mb-3">Our Mission</h2>
            <p class="lead mb-0 mx-auto">
                To boost local tourism in the Batticaloa and Eravur regions by providing a centralized, easy-to-use digital platform that connects travelers with the natural and historical beauty of the Eastern Province.
            </p>
        </div>
    </div>

</div>

<?php include 'includes/footer.php'; ?>