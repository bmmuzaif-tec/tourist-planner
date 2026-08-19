<?php
session_start(); include '../config/db.php'; $error = ""; $username = isset($_COOKIE['admin_username']) ? $_COOKIE['admin_username'] : "";
if(!isset($_SESSION['csrf_token'])) $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
if(!isset($_SESSION['attempts'])) { $_SESSION['attempts'] = 0; $_SESSION['lockout'] = 0; }
if($_SESSION['lockout'] && time() < $_SESSION['lockout']) $error = "Too many attempts. Try after 15 min.";
elseif(isset($_POST['login']) && (!$_SESSION['lockout'] || time() >= $_SESSION['lockout'])) {
    if(!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) $error = "Invalid token";
    else {
        $user = trim($_POST['username']); $pass = $_POST['password'];
        $stmt = $conn->prepare("SELECT admin_id, full_name, password FROM admin WHERE username=?");
        $stmt->bind_param("s", $user); $stmt->execute(); $res = $stmt->get_result();
        if($res->num_rows == 1) { $admin = $res->fetch_assoc();
            if(password_verify($pass, $admin['password'])) {
                $_SESSION['admin_id'] = $admin['admin_id']; $_SESSION['admin_name'] = $admin['full_name'];
                $_SESSION['attempts'] = 0; $_SESSION['lockout'] = 0;
                if(isset($_POST['remember'])) setcookie('admin_username', $user, time()+2592000, "/");
                header("Location: dashboard.php"); exit();
            } else { $_SESSION['attempts']++; if($_SESSION['attempts'] >= 5) $_SESSION['lockout'] = time()+900; }
        } else { $_SESSION['attempts']++; if($_SESSION['attempts'] >= 5) $_SESSION['lockout'] = time()+900; }
        $error = "Invalid credentials. Attempts: ".$_SESSION['attempts']."/5";
    }
}
?>
<!DOCTYPE html>
<html><head><title>Admin Login</title>
<link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
<link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
<style>body{background:linear-gradient(135deg,#198754,#20c997);min-height:100vh;display:flex;align-items:center}
.card{border:none;border-radius:15px;animation:slideIn 0.5s}@keyframes slideIn{from{opacity:0;transform:translateY(-20px)}to{opacity:1;transform:translateY(0)}}</style>
</head><body>
<div class="container"><div class="row justify-content-center"><div class="col-md-5">
<div class="card shadow-lg"><div class="card-header bg-success text-white text-center">
<h4 class="mb-0"><i class="bi bi-shield-lock"></i> Admin Login</h4><small>Tourist Planner Panel</small></div>
<div class="card-body p-4">
<?php if($error): ?><div class="alert alert-danger alert-dismissible"><button class="btn-close" data-bs-dismiss="alert"></button><?=$error?></div><?php endif; ?>
<form method="POST"><input type="hidden" name="csrf_token" value="<?=$_SESSION['csrf_token']?>">
<div class="mb-3"><label class="fw-bold"><i class="bi bi-person"></i> Username</label>
<input type="text" name="username" class="form-control form-control-lg" value="<?=htmlspecialchars($username)?>" autofocus required></div>
<div class="mb-3"><label class="fw-bold"><i class="bi bi-lock"></i> Password</label>
<div class="input-group"><input type="password" name="password" id="pwd" class="form-control form-control-lg" required>
<button type="button" class="btn btn-outline-secondary" onclick="document.getElementById('pwd').type=document.getElementById('pwd').type==='password'?'text':'password'"><i class="bi bi-eye"></i></button></div></div>
<div class="mb-3 form-check"><input type="checkbox" name="remember" class="form-check-input" id="rem">
<label class="form-check-label" for="rem">Remember Me</label></div>
<button type="submit" name="login" class="btn btn-success btn-lg w-100 fw-bold"><i class="bi bi-box-arrow-in-right"></i> Login</button>
</form><div class="text-center mt-3"><a href="../index.php" class="text-muted"><i class="bi bi-arrow-left"></i> Back to Website</a></div>
</div></div><p class="text-center text-white mt-3 small">&copy; 2026 Local Tourist Day Visit Planner</p>
</div></div></div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body></html>