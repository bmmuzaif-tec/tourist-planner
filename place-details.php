<?php
session_start();
include 'config/db.php';

if(!isset($_GET['id'])) die("<div class='container mt-5'><div class='alert alert-danger text-center'>Invalid ID</div></div>");
$id = intval($_GET['id']);

$place = mysqli_fetch_assoc(mysqli_query($conn, "SELECT tp.*, c.category_name FROM tourist_places tp JOIN categories c ON tp.category_id = c.category_id WHERE tp.place_id = $id"));
if(!$place) die("<div class='container mt-5'><div class='alert alert-warning text-center'>Place Not Found</div></div>");

if(isset($_POST['submit_review'])){
    $name = mysqli_real_escape_string($conn, $_POST['visitor_name']);
    $rating = intval($_POST['rating']);
    $comment = mysqli_real_escape_string($conn, $_POST['comment']);
    mysqli_query($conn, "INSERT INTO reviews (place_id, visitor_name, rating, comment, review_date) VALUES ('$id', '$name', '$rating', '$comment', NOW())");
    header("Location: place-details.php?id=$id&success=1"); exit();
}

$reviews = mysqli_query($conn, "SELECT * FROM reviews WHERE place_id=$id ORDER BY review_date DESC");
$reviewCount = mysqli_num_rows($reviews);

// Calculate average rating
$ratingData = mysqli_fetch_assoc(mysqli_query($conn, "SELECT AVG(rating) as avg_rating, COUNT(*) as total FROM reviews WHERE place_id=$id"));
$avgRating = $ratingData['avg_rating'] ? round($ratingData['avg_rating'], 1) : 0;

// Smart Image Resolver
$imgName = htmlspecialchars($place['image']);
$imgPath = "https://via.placeholder.com/1200x600/0B6E4F/fff?text=" . urlencode($place['place_name']);
if(!empty($imgName)) {
    if(file_exists(__DIR__ . "/admin/uploads/$imgName")) $imgPath = "admin/uploads/$imgName";
    elseif(file_exists(__DIR__ . "/assets/images/places/$imgName")) $imgPath = "assets/images/places/$imgName";
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($place['place_name']) ?> | EravurGo</title>
    <link rel="preconnect" href="https://fonts.googleapis.com">
    <link href="https://fonts.googleapis.com/css2?family=Poppins:wght@600;700;800&family=Inter:wght@400;500;600;700&display=swap" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <link href="https://cdn.jsdelivr.net/npm/bootstrap-icons@1.11.0/font/bootstrap-icons.css" rel="stylesheet">
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>
<?php include 'includes/navbar.php'; ?>

<div class="container mt-4 mb-5">

    <!-- BREADCRUMB + SHARE -->
    <div class="d-flex justify-content-between align-items-center mb-3 reveal">
        <a href="places.php" class="text-muted small text-decoration-none">
            <i class="bi bi-arrow-left"></i> Back to Places
        </a>
        <button class="btn btn-outline-secondary btn-sm" onclick="sharePage()">
            <i class="bi bi-share"></i> Share
        </button>
    </div>

    <!-- TITLE + META HEADER (Duplicate button REMOVED, meta info RESTORED) -->
    <div class="place-header reveal">
        <div class="row align-items-end g-3">
            <div class="col-12">
                <span class="badge bg-success mb-2">
                    <i class="bi bi-tag-fill"></i> <?= htmlspecialchars($place['category_name']) ?>
                </span>
                <h1 class="place-title mb-2"><?= htmlspecialchars($place['place_name']) ?></h1>
                <div class="d-flex flex-wrap align-items-center gap-3 text-muted small">
                    <!-- Rating -->
                    <span>
                        <i class="bi bi-star-fill text-warning"></i>
                        <strong class="text-dark"><?= $avgRating ?></strong>
                        (<?= $reviewCount ?> review<?= $reviewCount == 1 ? '' : 's' ?>)
                    </span>
                    <!-- Distance -->
                    <span>
                        <i class="bi bi-geo-alt-fill text-success"></i>
                        <?= htmlspecialchars($place['distance']) ?> km from Eravur
                    </span>
                    <!-- Opening Hours -->
                    <?php if(!empty($place['opening_hours'])): ?>
                    <span>
                        <i class="bi bi-clock-fill text-primary"></i>
                        <?= htmlspecialchars($place['opening_hours']) ?>
                    </span>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>

    <!-- SINGLE IMAGE -->
    <div class="single-image-wrap reveal reveal-delay-1">
        <img src="<?= $imgPath ?>" alt="<?= htmlspecialchars($place['place_name']) ?>" class="single-image">
    </div>

    <!-- MAIN CONTENT + SIDEBAR -->
    <div class="row g-4 mt-4">

        <!-- LEFT: Place Info -->
        <div class="col-lg-8">

            <!-- About -->
            <div class="place-section reveal">
                <h4 class="section-heading">
                    <i class="bi bi-info-circle-fill text-success"></i> About this place
                </h4>
                <p class="text-muted mb-0" style="line-height: 1.7;">
                    <?= nl2br(htmlspecialchars($place['description'])) ?>
                </p>
            </div>

            <!-- Quick Info -->
            <div class="place-section reveal">
                <h4 class="section-heading">
                    <i class="bi bi-grid-fill text-success"></i> Quick Info
                </h4>
                <div class="row g-3">
                    <div class="col-md-6">
                        <div class="info-card-airbnb">
                            <div class="info-icon-airbnb"><i class="bi bi-geo-alt-fill"></i></div>
                            <div>
                                <small class="text-muted d-block mb-1">Address</small>
                                <strong class="small"><?= htmlspecialchars($place['address']) ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card-airbnb">
                            <div class="info-icon-airbnb"><i class="bi bi-clock-fill"></i></div>
                            <div>
                                <small class="text-muted d-block mb-1">Opening Hours</small>
                                <strong class="small"><?= htmlspecialchars($place['opening_hours'] ?: 'N/A') ?></strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card-airbnb">
                            <div class="info-icon-airbnb"><i class="bi bi-signpost-2-fill"></i></div>
                            <div>
                                <small class="text-muted d-block mb-1">Distance</small>
                                <strong class="small text-success"><?= htmlspecialchars($place['distance']) ?> km</strong>
                            </div>
                        </div>
                    </div>
                    <div class="col-md-6">
                        <div class="info-card-airbnb">
                            <div class="info-icon-airbnb"><i class="bi bi-lightbulb-fill"></i></div>
                            <div>
                                <small class="text-muted d-block mb-1">Travel Tips</small>
                                <strong class="small fst-italic"><?= htmlspecialchars($place['travel_tips'] ?: 'N/A') ?></strong>
                            </div>
                        </div>
                    </div>
                </div>
            </div>

            <!-- Location Map -->
            <div class="place-section reveal">
                <h4 class="section-heading">
                    <i class="bi bi-map-fill text-success"></i> Location
                </h4>
                <div class="map-frame-airbnb">
                    <iframe src="https://maps.google.com/maps?q=<?= $place['latitude'] ?>,<?= $place['longitude'] ?>&z=15&output=embed" loading="lazy"></iframe>
                </div>
            </div>

            <!-- Reviews -->
            <div class="place-section reveal">
                <h4 class="section-heading">
                    <i class="bi bi-chat-square-text-fill text-success"></i> 
                    Visitor Reviews 
                    <span class="badge bg-success ms-2"><?= $reviewCount ?></span>
                </h4>
                
                <?php if($reviewCount > 0): 
                    mysqli_data_seek($reviews, 0);
                    while($rev = mysqli_fetch_assoc($reviews)): ?>
                    <div class="review-card-airbnb">
                        <div class="review-avatar">
                            <?= strtoupper(substr($rev['visitor_name'], 0, 1)) ?>
                        </div>
                        <div class="review-content">
                            <div class="d-flex justify-content-between align-items-start mb-1">
                                <div>
                                    <strong class="d-block"><?= htmlspecialchars($rev['visitor_name']) ?></strong>
                                    <small class="text-muted"><?= date('F Y', strtotime($rev['review_date'])) ?></small>
                                </div>
                                <div class="review-stars">
                                    <?php for($i = 1; $i <= 5; $i++): ?>
                                        <i class="bi bi-star<?= $i <= $rev['rating'] ? '-fill' : '' ?>"></i>
                                    <?php endfor; ?>
                                </div>
                            </div>
                            <p class="text-muted small mb-0"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                        </div>
                    </div>
                <?php endwhile; else: ?>
                    <div class="empty-state">
                        <div class="empty-icon mb-2">
                            <i class="bi bi-chat-square"></i>
                        </div>
                        <p class="mb-0">No reviews yet. Be the first!</p>
                    </div>
                <?php endif; ?>
            </div>

        </div>

        <!-- RIGHT: Sidebar -->
        <div class="col-lg-4">
            <div class="booking-sidebar reveal">
                <div class="booking-card">
                    <div class="booking-header">
                        <span class="badge bg-warning text-dark">
                            <i class="bi bi-star-fill"></i> Popular
                        </span>
                        <h3 class="booking-price mt-2 mb-1">
                            <?= htmlspecialchars($place['distance']) ?> <small>km away</small>
                        </h3>
                        <p class="text-muted small mb-0">From Eravur city center</p>
                    </div>

                    <div class="booking-divider"></div>

                    <div class="booking-features">
                        <div class="booking-feature">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Verified destination</span>
                        </div>
                        <div class="booking-feature">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span><?= $reviewCount ?> visitor review<?= $reviewCount == 1 ? '' : 's' ?></span>
                        </div>
                        <div class="booking-feature">
                            <i class="bi bi-check-circle-fill text-success"></i>
                            <span>Google Maps directions</span>
                        </div>
                    </div>

                    <div class="booking-divider"></div>

                    <a href="planner.php" class="btn btn-success btn-lg w-100 mb-2">
                        <i class="bi bi-calendar-plus"></i> Add to Visit Plan
                    </a>
                    <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $place['latitude'] ?>,<?= $place['longitude'] ?>" 
                       target="_blank" 
                       class="btn btn-outline-success w-100">
                        <i class="bi bi-signpost-split"></i> Get Directions
                    </a>

                    <div class="booking-footer">
                        <small class="text-muted">
                            <i class="bi bi-shield-check"></i> 
                            Verified by EravurGo team
                        </small>
                    </div>
                </div>

                <!-- Write Review -->
                <div class="write-review-card mt-3">
                    <h5 class="fw-bold mb-3">
                        <i class="bi bi-pencil-square text-success"></i> 
                        Write a Review
                    </h5>
                    <?php if(isset($_GET['success'])): ?>
                        <div class="alert alert-success py-2 small">
                            <i class="bi bi-check-circle"></i> Review submitted!
                        </div>
                    <?php endif; ?>
                    <form method="POST">
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Your Name</label>
                            <input type="text" name="visitor_name" class="form-control form-control-sm" required>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Rating</label>
                            <select name="rating" class="form-select form-select-sm" required>
                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option>
                                <option value="4">⭐⭐⭐⭐ Very Good</option>
                                <option value="3">⭐⭐⭐ Good</option>
                                <option value="2">⭐⭐ Average</option>
                                <option value="1">⭐ Poor</option>
                            </select>
                        </div>
                        <div class="mb-3">
                            <label class="form-label small fw-bold">Review</label>
                            <textarea name="comment" class="form-control form-control-sm" rows="3" required></textarea>
                        </div>
                        <button type="submit" name="submit_review" class="btn btn-success btn-sm w-100">
                            <i class="bi bi-send"></i> Submit Review
                        </button>
                    </form>
                </div>
            </div>
        </div>

    </div>

</div>

<script>
function sharePage() {
    if (navigator.share) {
        navigator.share({
            title: '<?= htmlspecialchars($place['place_name']) ?>',
            url: window.location.href
        });
    } else {
        navigator.clipboard.writeText(window.location.href);
        alert('Link copied to clipboard!');
    }
}
</script>

<?php include 'includes/footer.php'; ?>