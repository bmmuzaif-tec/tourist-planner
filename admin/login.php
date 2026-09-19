<?php
session_start();
include '../config/db.php';

$error = "";
$username = isset($_COOKIE['admin_username']) ? $_COOKIE['admin_username'] : "";

// CSRF Token
if (!isset($_SESSION['csrf_token'])) {
    $_SESSION['csrf_token'] = bin2hex(random_bytes(32));
}

// Session-based attempt tracking (instead of database)
if (!isset($_SESSION['attempts'])) {
    $_SESSION['attempts'] = 0;
    $_SESSION['lockout'] = 0;
}

// Check if locked out
if ($_SESSION['lockout'] && time() < $_SESSION['lockout']) {
    $error = "⛔ Too many attempts. Try again after 15 minutes.";
} elseif (isset($_POST['login']) && (!$_SESSION['lockout'] || time() >= $_SESSION['lockout'])) {
    
    // CSRF Validation
    if (!isset($_POST['csrf_token']) || $_POST['csrf_token'] !== $_SESSION['csrf_token']) {
        $error = "❌ Invalid security token.";
    } else {
        $user = trim($_POST['username']);
        $pass = $_POST['password'];

        // ✅ FIXED: Using 'administrator' table
        $stmt = $conn->prepare("SELECT admin_id, full_name, username, password, role, failed_attempts, lock_until FROM administrator WHERE username = ?");
        $stmt->bind_param("s", $user);
        $stmt->execute();
        $res = $stmt->get_result();

        if ($res->num_rows == 1) {
            $admin = $res->fetch_assoc();

            // Check if account is locked in database
            if ($admin['lock_until'] !== null && strtotime($admin['lock_until']) > time()) {
                $error = "⛔ Account locked. Try again after " . date('H:i', strtotime($admin['lock_until'])) . ".";
            } else {
                // Verify password
                if (password_verify($pass, $admin['password'])) {
                    // Reset failed attempts in database
                    $update = $conn->prepare("UPDATE administrator SET failed_attempts = 0, lock_until = NULL WHERE admin_id = ?");
                    $update->bind_param("i", $admin['admin_id']);
                    $update->execute();
                    $update->close();

                    // Reset session attempts
                    $_SESSION['attempts'] = 0;
                    $_SESSION['lockout'] = 0;

                    // Set session
                    $_SESSION['admin_id'] = $admin['admin_id'];
                    $_SESSION['admin_name'] = $admin['full_name'];
                    $_SESSION['username'] = $admin['username'];
                    $_SESSION['role'] = $admin['role'];

                    // Remember me cookie
                    if (isset($_POST['remember'])) {
                        setcookie('admin_username', $user, time() + 2592000, "/");
                    }

                    header("Location: dashboard.php");
                    exit();
                } else {
                    // Increment failed attempts in session
                    $_SESSION['attempts']++;
                    $failed = $_SESSION['attempts'];

                    // Update database failed_attempts
                    $update = $conn->prepare("UPDATE administrator SET failed_attempts = ? WHERE username = ?");
                    $update->bind_param("is", $failed, $user);
                    $update->execute();
                    $update->close();

                    // Lock if >= 5 attempts
                    if ($_SESSION['attempts'] >= 5) {
                        $_SESSION['lockout'] = time() + 60; // 15 minutes
                        $lock_until = date('Y-m-d H:i:s', time() + 60);
                        $update = $conn->prepare("UPDATE administrator SET lock_until = ? WHERE username = ?");
                        $update->bind_param("ss", $lock_until, $user);
                        $update->execute();
                        $update->close();
                        $error = "⛔ Too many failed attempts. Account locked for 15 minutes.";
                    } else {
                        $error = "❌ Invalid credentials. Attempts: " . $_SESSION['attempts'] . "/5";
                    }
                }
            }
        } else {
            $_SESSION['attempts']++;
            if ($_SESSION['attempts'] >= 5) {
                $_SESSION['lockout'] = time() + 60;
                $error = "⛔ Too many failed attempts. Try again after 15 minutes.";
            } else {
                $error = "❌ Invalid credentials. Attempts: " . $_SESSION['attempts'] . "/5";
            }
        }
        $stmt->close();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login | EravurGo</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
    <style>
        body {
            background: linear-gradient(135deg, #198754, #20c997);
            min-height: 100vh;
            display: flex;
            align-items: center;
        }
        .card {
            border: none;
            border-radius: 15px;
            animation: slideIn 0.5s;
        }
        @keyframes slideIn {
            from { opacity: 0; transform: translateY(-20px); }
            to { opacity: 1; transform: translateY(0); }
        }
        .form-control-lg {
            border-radius: 10px;
            padding: 12px 16px;
        }
        .btn-lg {
            border-radius: 10px;
            padding: 12px;
        }
    </style>
</head>
<body>
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-5">
            <div class="card shadow-lg">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0"><i class="bi bi-shield-lock"></i> Admin Login</h4>
                    <small>Tourist Planner Panel</small>
                </div>
                <div class="card-body p-4">
                    <?php if ($error): ?>
                        <div class="alert alert-danger alert-dismissible fade show">
                            <button class="btn-close" data-bs-dismiss="alert"></button>
                            <?= htmlspecialchars($error) ?>
                        </div>
                    <?php endif; ?>

                    <form method="POST">
                        <input type="hidden" name="csrf_token" value="<?= $_SESSION['csrf_token'] ?>">

                        <div class="mb-3">
                            <label class="fw-bold"><i class="bi bi-person"></i> Username</label>
                            <input type="text" name="username" class="form-control form-control-lg" 
                                   value="<?= htmlspecialchars($username) ?>" autofocus required>
                        </div>

                        <div class="mb-3">
                            <label class="fw-bold"><i class="bi bi-lock"></i> Password</label>
                            <div class="input-group">
                                <input type="password" name="password" id="pwd" class="form-control form-control-lg" required>
                                <button type="button" class="btn btn-outline-secondary" 
                                        onclick="document.getElementById('pwd').type=document.getElementById('pwd').type==='password'?'text':'password'">
                                    <i class="bi bi-eye"></i>
                                </button>
                            </div>
                        </div>

                        <div class="mb-3 form-check">
                            <input type="checkbox" name="remember" class="form-check-input" id="rem">
                            <label class="form-check-label" for="rem">Remember Me</label>
                        </div>

                        <button type="submit" name="login" class="btn btn-success btn-lg w-100 fw-bold">
                            <i class="bi bi-box-arrow-in-right"></i> Login
                        </button>
                    </form>

                    <div class="text-center mt-3">
                        <a href="../index.php" class="text-muted text-decoration-none">
                            <i class="bi bi-arrow-left"></i> Back to Website
                        </a>
                    </div>
                    <div class="text-center mt-2">
                        <small class="text-muted">Default: admin / admin123</small>
                    </div>
                </div>
            </div>
            <p class="text-center text-white mt-3 small">&copy; 2026 Local Tourist Day Visit Planner</p>
        </div>
    </div>
</div>
<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>
</body>
</html>