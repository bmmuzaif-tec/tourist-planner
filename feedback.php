<?php
session_start();
include 'config/db.php';

$message = "";
if (isset($_POST['submit'])) {
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $rating = intval($_POST['rating']);
    $feedback = trim($_POST['message']);

    // Prepared Statement for Maximum Security
    $stmt = $conn->prepare("INSERT INTO feedbacks (name, email, rating, message, created_at) VALUES (?, ?, ?, ?, NOW())");
    $stmt->bind_param("ssis", $name, $email, $rating, $feedback);
    
    if ($stmt->execute()) {
        header("Location: feedback.php?success=1");
        exit();
    } else {
        $message = "❌ Error submitting feedback. Please try again.";
    }
    $stmt->close();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Feedback | Tourist Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css">
</head>
<body class="bg-light">

<?php include 'includes/navbar.php'; ?>

<div class="container mt-5 mb-5">
    <div class="row justify-content-center">
        <div class="col-md-8 col-lg-6">
            <div class="card shadow border-0">
                <div class="card-header bg-success text-white text-center py-3">
                    <h4 class="mb-0 fw-bold"><i class="bi bi-chat-heart"></i> Share Your Feedback</h4>
                    <small>Help us improve your local travel experience!</small>
                </div>
                <div class="card-body p-4">
                    
                    <?php if (isset($_GET['success'])): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> Thank you for your valuable feedback!
                            <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
                        </div>
                    <?php elseif ($message != ""): ?>
                        <div class="alert alert-danger"><?= $message ?></div>
                    <?php endif; ?>

                    <!-- REMOVED 'novalidate' so HTML5 required works, added 'needs-validation' for Bootstrap styling -->
                    <form method="POST" class="needs-validation" id="feedbackForm">
                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-person"></i> Your Name <span class="text-danger">*</span></label>
                            <input type="text" name="name" class="form-control" placeholder="Enter your name" required>
                            <div class="invalid-feedback">Please enter your name.</div>
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-envelope"></i> Email (Optional)</label>
                            <input type="email" name="email" class="form-control" placeholder="your@email.com">
                        </div>

                        <div class="mb-3">
                            <label class="form-label fw-bold"><i class="bi bi-star"></i> Rating <span class="text-danger">*</span></label>
                            <select name="rating" class="form-select" required>
                                <option value="" disabled selected>Choose a rating...</option>
                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                <option value="4">⭐⭐⭐⭐ Very Good</option>
                                <option value="3">⭐⭐⭐ Good</option>
                                <option value="2">⭐⭐ Average</option>
                                <option value="1">⭐ Poor</option>
                            </select>
                            <div class="invalid-feedback">Please select a rating.</div>
                        </div>

                        <div class="mb-4">
                            <label class="form-label fw-bold"><i class="bi bi-pencil"></i> Your Review <span class="text-danger">*</span></label>
                            <textarea name="message" class="form-control" rows="4" placeholder="Tell us about your experience..." required></textarea>
                            <div class="invalid-feedback">Please write a review.</div>
                        </div>

                        <button type="submit" name="submit" class="btn btn-success btn-lg w-100 fw-bold">
                            <i class="bi bi-send"></i> Submit Feedback
                        </button>
                    </form>
                </div>
            </div>
        </div>
    </div>
</div>

<?php include 'includes/footer.php'; ?>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"></script>

<!-- Bootstrap 5 Validation Script (Makes the 'required' attribute work beautifully with red/green borders) -->
<script>
(function () {
  'use strict'
  var forms = document.querySelectorAll('.needs-validation')
  Array.prototype.slice.call(forms).forEach(function (form) {
    form.addEventListener('submit', function (event) {
      if (!form.checkValidity()) {
        event.preventDefault()
        event.stopPropagation()
      }
      form.classList.add('was-validated')
    }, false)
  })
})()
</script>
</body>
</html>