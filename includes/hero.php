<section class="hero">
    <div class="container hero-inner text-center">
        <span class="eyebrow" style="background:rgba(255,255,255,0.15); color:#fff;">
            DISCOVER SRI LANKA
        </span>
        <h1 class="fw-bold mb-3">Discover Your Next Adventure</h1>
        <p class="lead mb-4">
            Explore beautiful destinations around Eravur and Batticaloa and create your perfect one-day trip.
        </p>
        <p class="mb-4" style="color:rgba(255,255,255,0.85);">
            <i class="bi bi-compass"></i> Explore destinations within 25 km of Eravur
        </p>

        <form action="places.php" method="GET" class="row justify-content-center g-2 mb-4">
            <div class="col-lg-6 col-md-8">
                <div class="search-box glass-panel d-flex align-items-center">
                    <input
                        type="text"
                        name="search"
                        class="form-control form-control-lg"
                        placeholder="Search destinations..."
                        value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
                    <button type="submit" class="btn btn-success btn-lg px-4">
                        <i class="bi bi-search"></i>
                    </button>
                </div>
            </div>
        </form>

        <div class="hero-cta">
            <a href="places.php" class="btn btn-warning btn-lg">Explore Places</a>
            <a href="planner.php" class="btn btn-outline-light btn-lg">Plan Your Trip</a>
        </div>
    </div>
</section>
