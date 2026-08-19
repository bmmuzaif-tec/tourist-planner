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
include 'includes/navbar.php';

// Smart Image Resolver
$imgName = htmlspecialchars($place['image']);
$imgPath = "https://via.placeholder.com/600x400/198754/fff?text=" . urlencode($place['place_name']);
if(!empty($imgName)) {
    if(file_exists(__DIR__ . "/admin/uploads/$imgName")) $imgPath = "admin/uploads/$imgName";
    elseif(file_exists(__DIR__ . "/assets/images/places/$imgName")) $imgPath = "assets/images/places/$imgName";
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8"><meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title><?= htmlspecialchars($place['place_name']) ?> | Tourist Planner</title>
    <link href="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/css/bootstrap.min.css" rel="stylesheet">
    <style>.map-container iframe{width:100%;height:350px;border:0;border-radius:8px}.review-card{background:#f8f9fa;border-radius:8px;padding:15px;margin-bottom:15px}</style>
</head>
<body class="bg-light">
<div class="container mt-5 mb-5">
    <div class="card shadow border-0 mb-4">
        <div class="card-body p-4">
            <div class="row">
                <div class="col-md-6 mb-3">
                    <span class="badge bg-success mb-2"><?= htmlspecialchars($place['category_name']) ?></span>
                    <h1 class="fw-bold text-success mb-3"><?= htmlspecialchars($place['place_name']) ?></h1>
                    <img src="<?= $imgPath ?>" class="img-fluid rounded shadow-sm w-100" style="max-height:400px;object-fit:cover;" alt="<?= htmlspecialchars($place['place_name']) ?>">
                </div>
                <div class="col-md-6">
                    <h6 class="fw-bold text-success">📝 Description</h6><p class="text-muted"><?= nl2br(htmlspecialchars($place['description'])) ?></p>
                    <h6 class="fw-bold text-success">📍 Address</h6><p><?= htmlspecialchars($place['address']) ?></p>
                    <h6 class="fw-bold text-success">🕒 Opening Hours</h6><p><?= htmlspecialchars($place['opening_hours']) ?></p>
                    <h6 class="fw-bold text-success">💡 Travel Tips</h6><p class="fst-italic"><?= htmlspecialchars($place['travel_tips']) ?></p>
                    <h6 class="fw-bold text-success">🚗 Distance</h6><p class="fs-5 fw-bold text-primary">📍 <?= htmlspecialchars($place['distance']) ?> km</p>
                </div>
            </div>
            <hr class="my-4">
            <h4 class="text-success mb-3">🗺️ Location Map</h4>
            <div class="map-container mb-3">
                <iframe src="https://maps.google.com/maps?q=<?= $place['latitude'] ?>,<?= $place['longitude'] ?>&z=15&output=embed" loading="lazy"></iframe>
            </div>
            <div class="d-flex gap-2">
                <a href="https://www.google.com/maps/dir/?api=1&destination=<?= $place['latitude'] ?>,<?= $place['longitude'] ?>" target="_blank" class="btn btn-primary">📍 Get Directions</a>
                <a href="places.php" class="btn btn-outline-secondary">← Back</a>
                
            </div>
        </div>
    </div>

    <div class="row">
        <div class="col-md-5 mb-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h4 class="text-success mb-3">✍️ Write a Review</h4>
                    <?php if(isset($_GET['success'])): ?><div class="alert alert-success">✅ Review submitted!</div><?php endif; ?>
                    <form method="POST">
                        <div class="mb-3"><label class="form-label fw-bold">Name</label><input type="text" name="visitor_name" class="form-control" required></div>
                        <div class="mb-3"><label class="form-label fw-bold">Rating</label>
                            <select name="rating" class="form-select" required>
                                <option value="5">⭐⭐⭐⭐⭐ Excellent</option><option value="4">⭐⭐⭐⭐ Very Good</option>
                                <option value="3">⭐⭐⭐ Good</option><option value="2">⭐⭐ Average</option><option value="1">⭐ Poor</option>
                            </select>
                        </div>
                        <div class="mb-3"><label class="form-label fw-bold">Review</label><textarea name="comment" class="form-control" rows="3" required></textarea></div>
                        <button type="submit" name="submit_review" class="btn btn-success w-100 fw-bold">Submit Review</button>
                    </form>
                </div>
            </div>
        </div>

        <div class="col-md-7 mb-4">
            <div class="card shadow border-0">
                <div class="card-body p-4">
                    <h4 class="text-success mb-3">💬 Visitor Reviews</h4>
                    <?php if(mysqli_num_rows($reviews) > 0): while($rev = mysqli_fetch_assoc($reviews)): ?>
                    <div class="review-card">
                        <div class="d-flex justify-content-between"><h6 class="fw-bold mb-0"><?= htmlspecialchars($rev['visitor_name']) ?></h6><small class="text-muted"><?= date('d M Y', strtotime($rev['review_date'])) ?></small></div>
                        <div class="mb-1"><?= str_repeat("⭐", $rev['rating']) ?></div>
                        <p class="mb-0 text-muted small"><?= nl2br(htmlspecialchars($rev['comment'])) ?></p>
                    </div>
                    <?php endwhile; else: ?><p class="text-center text-muted py-3">No reviews yet. Be the first! ☝️</p><?php endif; ?>
                </div>
            </div>
        </div>
    </div>
</div>
<?php include 'includes/footer.php'; ?>
</body></html>