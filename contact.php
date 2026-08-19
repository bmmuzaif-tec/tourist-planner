<?php
include 'includes/header.php';
include 'includes/navbar.php';

// Simple form submission handler (UI only)
$message_sent = false;
if ($_SERVER["REQUEST_METHOD"] == "POST") {
    // இங்கு Email அனுப்பும் PHP code வரும். தற்போதைக்கு UI மட்டும்.
    $message_sent = true;
}
?>

<div class="container mt-5 mb-5">
    
    <!-- Page Header -->
    <div class="text-center mb-5">
        <h1 class="fw-bold text-success display-5">Contact Us</h1>
        <p class="lead text-muted">Have questions or feedback? We'd love to hear from you!</p>
        <hr class="w-25 mx-auto text-success" style="height: 3px; opacity: 1;">
    </div>

    <div class="row g-4">
        
        <!-- Left Column: Contact Info & Map -->
        <div class="col-md-5">
            <!-- Contact Information Card -->
            <div class="card shadow-sm border-0 mb-4">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-success mb-4">📞 Get in Touch</h4>
                    
                    <div class="d-flex mb-3">
                        <span class="fs-4 me-3">📍</span>
                        <div>
                            <h6 class="fw-bold mb-1">Our Address</h6>
                            <p class="text-muted mb-0 small">Eravur, Batticaloa, Eastern Province, Sri Lanka</p>
                        </div>
                    </div>
                    
                    <div class="d-flex mb-3">
                        <span class="fs-4 me-3">📧</span>
                        <div>
                            <h6 class="fw-bold mb-1">Email Us</h6>
                            <p class="text-muted mb-0 small">info@touristplanner.lk</p>
                        </div>
                    </div>
                    
                    <div class="d-flex">
                        <span class="fs-4 me-3">📱</span>
                        <div>
                            <h6 class="fw-bold mb-1">Call Us</h6>
                            <p class="text-muted mb-0 small">+94 77 123 4567</p>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Google Map -->
            <div class="card shadow-sm border-0">
                <div class="card-body p-2">
                    <iframe 
                        src="https://www.google.com/maps/embed?pb=!1m18!1m12!1m3!1d31778.07384265243!2d81.65000000000002!3d7.71666665!2m3!1f0!2f0!3f0!3m2!1i1024!2i768!4f13.1!3m3!1m2!1s0x3afe05a000000001%3A0x0!2sEravur!5e0!3m2!1sen!2slk!4v1690000000000!5m2!1sen!2slk" 
                        width="100%" 
                        height="250" 
                        style="border:0; border-radius: 8px;" 
                        allowfullscreen="" 
                        loading="lazy">
                    </iframe>
                </div>
            </div>
        </div>

        <!-- Right Column: Contact Form -->
        <div class="col-md-7">
            <div class="card shadow-sm border-0 h-100">
                <div class="card-body p-4">
                    <h4 class="fw-bold text-success mb-4">✉️ Send us a Message</h4>
                    
                    <?php if ($message_sent): ?>
                        <div class="alert alert-success alert-dismissible fade show" role="alert">
                            ✅ Thank you! Your message has been sent successfully. We will get back to you soon.
                            <button type="button" class="btn-close" data-bs-dismiss="alert" aria-label="Close"></button>
                        </div>
                    <?php endif; ?>

                    <form method="POST" action="">
                        <div class="row g-3">
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Your Name</label>
                                <input type="text" name="name" class="form-control" placeholder="John Doe" required>
                            </div>
                            <div class="col-md-6">
                                <label class="form-label fw-bold">Your Email</label>
                                <input type="email" name="email" class="form-control" placeholder="john@example.com" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Subject</label>
                                <input type="text" name="subject" class="form-control" placeholder="How can we help you?" required>
                            </div>
                            <div class="col-12">
                                <label class="form-label fw-bold">Message</label>
                                <textarea name="message" class="form-control" rows="6" placeholder="Write your message here..." required></textarea>
                            </div>
                            <div class="col-12">
                                <button type="submit" class="btn btn-success btn-lg w-100 fw-bold">
                                    🚀 Send Message
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