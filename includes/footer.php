<?php 
// Auto-detect admin folder for correct paths
$base = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin') ? '../' : ''; 
?>
<footer class="site-footer">
    <div class="container">
        <div class="row g-4">
            <div class="col-md-4">
                <h5><i class="bi bi-geo-alt-fill"></i> Tourist Planner</h5>
                <p class="small mb-0">A local tourism discovery and one-day trip planning platform for Eravur and Batticaloa, Sri Lanka.</p>
            </div>
            <div class="col-md-4">
                <h5>Quick Links</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><a href="<?= $base ?>index.php">Home</a></li>
                    <li class="mb-2"><a href="<?= $base ?>places.php">Places</a></li>
                    <li class="mb-2"><a href="<?= $base ?>planner.php">Visit Planner</a></li>
                    <li class="mb-2"><a href="<?= $base ?>about.php">About</a></li>
                    <li class="mb-2"><a href="<?= $base ?>contact.php">Contact</a></li>
                </ul>
            </div>
            <div class="col-md-4">
                <h5>Get in Touch</h5>
                <ul class="list-unstyled small">
                    <li class="mb-2"><i class="bi bi-geo-alt me-1"></i> Eravur, Batticaloa, Sri Lanka</li>
                    <li class="mb-2"><i class="bi bi-envelope me-1"></i> info@touristplanner.lk</li>
                </ul>
            </div>
        </div>
        <div class="footer-bottom text-center">
            &copy; 2026 Local Tourist Day Visit Planner &mdash; Eravur, Sri Lanka
        </div>
    </div>
</footer>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- =====================================================
     SCROLL REVEAL — Intersection Observer
     ===================================================== -->
<script>
(function() {
    const reveals = document.querySelectorAll('.reveal');
    if (reveals.length === 0) return;

    const observer = new IntersectionObserver(function(entries) {
        entries.forEach(function(entry) {
            if (entry.isIntersecting) {
                entry.target.classList.add('visible');
                observer.unobserve(entry.target);
            }
        });
    }, { threshold: 0.1, rootMargin: '0px 0px -50px 0px' });

    reveals.forEach(function(el) { observer.observe(el); });
})();
</script>

</body>
</html>