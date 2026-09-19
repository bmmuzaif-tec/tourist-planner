<?php 
if (session_status() === PHP_SESSION_NONE) session_start();
$current = basename($_SERVER['PHP_SELF']);
$isAdmin = (basename(dirname($_SERVER['PHP_SELF'])) === 'admin');
$basePath = $isAdmin ? '../' : '';
$adminLink = $isAdmin ? 'dashboard.php' : 'admin/dashboard.php';
$loginLink = $isAdmin ? 'login.php' : 'admin/login.php';
?>
<div class="navbar-wrapper">
<nav class="navbar navbar-expand-lg site-navbar">
<div class="container-fluid px-4">

<a class="navbar-brand" href="<?= $basePath ?>index.php">
    <i class="bi bi-globe text-success"></i> Eravur Go
</a>

<div class="d-flex align-items-center order-lg-2 gap-2">
    <button class="theme-toggle" id="themeToggle" type="button" title="Toggle dark mode">
        <i class="bi bi-moon-stars" id="themeIcon"></i>
    </button>
    <button class="navbar-toggler border-0" type="button" data-bs-toggle="collapse" data-bs-target="#menu">
        <span class="navbar-toggler-icon"></span>
    </button>
</div>

<div class="collapse navbar-collapse" id="menu">
<ul class="navbar-nav ms-auto align-items-lg-center">
    <li class="nav-item"><a class="nav-link <?= $current == 'index.php' ? 'active' : '' ?>" href="<?= $basePath ?>index.php">Home</a></li>
    <li class="nav-item"><a class="nav-link <?= $current == 'places.php' ? 'active' : '' ?>" href="<?= $basePath ?>places.php">Places</a></li>
    <li class="nav-item"><a class="nav-link <?= $current == 'planner.php' ? 'active' : '' ?>" href="<?= $basePath ?>planner.php">Planner</a></li>
    <li class="nav-item"><a class="nav-link <?= $current == 'about.php' ? 'active' : '' ?>" href="<?= $basePath ?>about.php">About</a></li>
    <li class="nav-item"><a class="nav-link <?= $current == 'contact.php' ? 'active' : '' ?>" href="<?= $basePath ?>contact.php">Contact</a></li>
    
    <?php if (isset($_SESSION['admin_id'])): ?>
        <li class="nav-item">
            <a class="nav-link admin-link" href="<?= $adminLink ?>">
                <i class="bi bi-speedometer2"></i> Dashboard
            </a>
        </li>
    <?php else: ?>
        <li class="nav-item">
            <a class="nav-link admin-link" href="<?= $loginLink ?>">
                <i class="bi bi-shield-lock"></i> Admin
            </a>
        </li>
    <?php endif; ?>
</ul>
</div>

</div>
</nav>
</div>

<script>
(function(){
    const root = document.documentElement;
    const toggle = document.getElementById('themeToggle');
    const icon = document.getElementById('themeIcon');
    const saved = localStorage.getItem('tp-theme');
    if (saved === 'dark') { root.setAttribute('data-theme', 'dark'); icon.className = 'bi bi-sun'; }
    toggle.addEventListener('click', function(){
        const isDark = root.getAttribute('data-theme') === 'dark';
        if (isDark) { root.removeAttribute('data-theme'); localStorage.setItem('tp-theme', 'light'); icon.className = 'bi bi-moon-stars'; }
        else { root.setAttribute('data-theme', 'dark'); localStorage.setItem('tp-theme', 'dark'); icon.className = 'bi bi-sun'; }
    });
})();
</script>