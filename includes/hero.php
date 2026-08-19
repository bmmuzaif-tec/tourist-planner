<style>
    .hero {
        /* சுத்தமான படம் மட்டும் - Dark Overlay நீக்கப்பட்டுள்ளது */
        background: url('assets/images/hero.jpg');
        background-size: cover;
        background-position: center;
        background-repeat: no-repeat;
        color: white;
        padding: 120px 0;
        min-height: 550px;
        display: flex;
        align-items: center;
    }
    
    /* படத்தின் வெள்ளை நிற பகுதிகளில் Text தெளிவா தெரிய Strong Shadow */
    .hero h1, .hero p {
        text-shadow: 3px 3px 6px rgba(0, 0, 0, 0.9);
        color: #ffffff;
    }
    
    .hero .form-control {
        box-shadow: 0 4px 12px rgba(0,0,0,0.4);
        border: none;
    }
</style>

<section class="hero">
    <div class="container text-center">
        <h1 class="display-4 fw-bold mb-3">
            Explore Sri Lanka's Beautiful Tourist Destinations
        </h1>
        <p class="lead mb-4">
            Plan your perfect one-day trip quickly and easily.
        </p>

        <form action="places.php" method="GET" class="row justify-content-center g-2">
            <div class="col-md-6 col-sm-8">
                <input 
                    type="text" 
                    name="search" 
                    class="form-control form-control-lg" 
                    placeholder="Search tourist places near Eravur..."
                    value="<?= isset($_GET['search']) ? htmlspecialchars($_GET['search']) : '' ?>">
            </div>
            <div class="col-md-3 col-sm-4">
                <button type="submit" class="btn btn-warning btn-lg w-100 fw-bold">
                    🔍 Search
                </button>
            </div>
        </form>
    </div>
</section>