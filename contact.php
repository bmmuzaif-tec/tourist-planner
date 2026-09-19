<?php
include 'config/db.php';  // Changed from includes/header.php to config/db.php for DB connection
include 'includes/header.php';
include 'includes/navbar.php';

$message_sent = false;
$error_message = "";

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // Sanitize input
    $name = trim($_POST['name']);
    $email = trim($_POST['email']);
    $subject = trim($_POST['subject']);
    $message = trim($_POST['message']);

    // Validate
    if (empty($name) || empty($email) || empty($subject) || empty($message)) {
        $error_message = "⚠️ Please fill in all fields.";
    } elseif (!filter_var($email, FILTER_VALIDATE_EMAIL)) {
        $error_message = "⚠️ Please enter a valid email address.";
    } else {
        // Save to database using prepared statement
        $stmt = $conn->prepare("INSERT INTO contact_messages (name, email, subject, message, created_at) VALUES (?, ?, ?, ?, NOW())");
        $stmt->bind_param("ssss", $name, $email, $subject, $message);

        if ($stmt->execute()) {
            $message_sent = true;
        } else {
            $error_message = "❌ Error saving message. Please try again.";
        }
        $stmt->close();
    }
}
?>

<section class="section pb-0">
    <div class="container text-center mb-5">
        <span class="eyebrow">We'd love to hear from you</span>
        <h1 class="section-title">Contact Us</h1>
        <p class="section-subtitle mx-auto">Have questions or feedback? Send us a message.</p>
    </div>
</section>

<div class="container mb-5">
    <div class="row g-4">

        <div class="col-md-5">
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4"><i class="bi bi-telephone"></i> Get in Touch</h4>

                    <div class="d-flex mb-3">
                        <span class="feature-icon" style="width:44px;height:44px;font-size:1.1rem;margin:0 12px 0 0;"><i class="bi bi-geo-alt"></i></span>
                        <div>
                            <h6 class="fw-bold mb-1">Our Address</h6>
                            <p class="text-muted mb-0 small">Eravur, Batticaloa, Eastern Province, Sri Lanka</p>
                        </div>
                    </div>

                    <div class="d-flex mb-3">
                        <span class="feature-icon" style="width:44px;height:44px;font-size:1.1rem;margin:0 12px 0 0;"><i class="bi bi-envelope"></i></span>
                        <div>
                            <h6 class="fw-bold mb-1">Email Us</h6>
                            <p class="text-muted mb-0 small">info@touristplanner.lk</p>
                        </div>
                    </div>

                    <div class="d-flex">
                        <span class="feature-icon" style="width:44px;height:44px;font-size:1.1rem;margin:0 12px 0 0;"><i class="bi bi-phone"></i></span>
                        <div>
                            <h6 class="fw-bold mb-1">Call Us</h6>
                            <p class="text-muted mb-0 small">+94 77 123 4567</p>
                        </div>
                    </div>
                </div>
            </div>

            <div class="map-frame">
                <iframe
                    src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31778.07384265243!2d81.65000000000002!3d7.71666665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3afe05a000000001%3A0x0!2sEravur!5e0!3m2!1sen!2slk!4v1690000000000!5m2!1sen!2slk"
                    height="250"
                    loading="lazy">
                </iframe>
            </div>
        </div>

        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h4 class="fw-bold mb-4"><i class="bi bi-envelope-paper"></i> Send us a Message</h4>

                    <?php if ($message_sent): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            <i class="bi bi-check-circle"></i> Thank you! Your message has been sent successfully. We will get back to you soon.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <?php if (!empty($error_message)): ?>
                        <div class="alert alert-danger alert-dismissible fade show" role="alert">
                            <i class="bi bi-exclamation-triangle"></i> <?= htmlspecialchars($error_message) ?>
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label">Your Name <span class="text-danger">*</span></label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label">Your Email <span class="text-danger">*</span></label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Subject <span class="text-danger">*</span></label>
                                <input type="text" name="subject" class="form-control" placeholder="How can we help you?" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label">Message <span class="text-danger">*</span></label>
                                <textarea name="message" class="form-control" rows="6" placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg w-100">
                                    <i class="bi bi-send"></i> Send Message
                                </button>
                            </div>
                        </div>
                    </form>
                </div>
            </div>
        </div>

    </div>
</div>

<?php include 'includes/footer.php'; ?>