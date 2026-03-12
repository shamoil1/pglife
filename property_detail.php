<?php
session_start();
require "includes/database_connect.php";

$user_id     = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$property_id = isset($_GET["property_id"]) ? intval($_GET["property_id"]) : 0;

if (!$property_id) { header("location: index.php"); die(); }

$sql_1 = "SELECT *, p.id AS property_id, p.name AS property_name, c.name AS city_name
            FROM properties p
            INNER JOIN cities c ON p.city_id = c.id
            WHERE p.id = $property_id";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) { echo "Something went wrong!"; return; }
$property = mysqli_fetch_assoc($result_1);
if (!$property) { echo "Property not found!"; return; }

$sql_2 = "SELECT * FROM testimonials WHERE property_id = $property_id";
$result_2 = mysqli_query($conn, $sql_2);
if (!$result_2) { echo "Something went wrong!"; return; }
$testimonials = mysqli_fetch_all($result_2, MYSQLI_ASSOC);

$sql_3 = "SELECT a.*
            FROM amenities a
            INNER JOIN properties_amenities pa ON a.id = pa.amenity_id
            WHERE pa.property_id = $property_id";
$result_3 = mysqli_query($conn, $sql_3);
if (!$result_3) { echo "Something went wrong!"; return; }
$amenities = mysqli_fetch_all($result_3, MYSQLI_ASSOC);

$sql_4 = "SELECT * FROM interested_users_properties WHERE property_id = $property_id";
$result_4 = mysqli_query($conn, $sql_4);
if (!$result_4) { echo "Something went wrong!"; return; }
$interested_users       = mysqli_fetch_all($result_4, MYSQLI_ASSOC);
$interested_users_count = count($interested_users);

$is_interested = false;
foreach ($interested_users as $iu) {
    if ($iu['user_id'] == $user_id) { $is_interested = true; break; }
}

$property_images = glob("img/properties/" . $property['property_id'] . "/*");
$total_rating = round(($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3, 1);

// Helper: render star icons
function render_stars($rating) {
    $html = '';
    for ($i = 0; $i < 5; $i++) {
        if ($rating >= $i + 0.8)     $html .= '<i class="fas fa-star"></i>';
        elseif ($rating >= $i + 0.3) $html .= '<i class="fas fa-star-half-alt"></i>';
        else                         $html .= '<i class="far fa-star"></i>';
    }
    return $html;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title><?= htmlspecialchars($property['property_name']) ?> | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <link href="css/property_detail.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="property_list.php?city=<?= urlencode($property['city_name']) ?>"><?= htmlspecialchars($property['city_name']) ?></a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($property['property_name']) ?></li>
        </ol>
    </nav>

    <!-- Image Carousel -->
    <div id="property-images" class="carousel slide" data-ride="carousel">
        <ol class="carousel-indicators">
            <?php foreach ($property_images as $index => $pi): ?>
                <li data-target="#property-images" data-slide-to="<?= $index ?>" class="<?= $index == 0 ? 'active' : '' ?>"></li>
            <?php endforeach; ?>
        </ol>
        <div class="carousel-inner">
            <?php if (empty($property_images)): ?>
                <div class="carousel-item active">
                    <img class="d-block w-100" src="img/delhi.png" alt="No image available" style="height:400px;object-fit:cover;">
                </div>
            <?php else: ?>
                <?php foreach ($property_images as $index => $pi): ?>
                <div class="carousel-item <?= $index == 0 ? 'active' : '' ?>">
                    <img class="d-block w-100" src="<?= htmlspecialchars($pi) ?>" alt="slide">
                </div>
                <?php endforeach; ?>
            <?php endif; ?>
        </div>
        <a class="carousel-control-prev" href="#property-images" role="button" data-slide="prev">
            <span class="carousel-control-prev-icon" aria-hidden="true"></span>
            <span class="sr-only">Previous</span>
        </a>
        <a class="carousel-control-next" href="#property-images" role="button" data-slide="next">
            <span class="carousel-control-next-icon" aria-hidden="true"></span>
            <span class="sr-only">Next</span>
        </a>
    </div>

    <!-- Property Summary -->
    <div class="property-summary page-container">
        <div class="row no-gutters justify-content-between">
            <div class="star-container" title="<?= $total_rating ?>">
                <?= render_stars($total_rating) ?>
            </div>
            <div class="interested-container">
                <i class="is-interested-image <?= $is_interested ? 'fas' : 'far' ?> fa-heart" property_id="<?= $property['property_id'] ?>"></i>
                <div class="interested-text">
                    <span class="interested-user-count"><?= $interested_users_count ?></span> interested
                </div>
            </div>
        </div>
        <div class="detail-container">
            <div class="property-name"><?= htmlspecialchars($property['property_name']) ?></div>
            <div class="property-address"><?= htmlspecialchars($property['address']) ?></div>
            <div class="property-gender">
                <?php if ($property['gender'] == "male"): ?>
                    <img src="img/male.png">
                <?php elseif ($property['gender'] == "female"): ?>
                    <img src="img/female.png">
                <?php else: ?>
                    <img src="img/unisex.png">
                <?php endif; ?>
            </div>
        </div>
        <div class="row no-gutters">
            <div class="rent-container col-6">
                <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                <div class="rent-unit">per month</div>
            </div>
            <div class="button-container col-6">
                <!-- ✅ BOOK NOW — links to booking.php -->
                <a href="booking.php?property_id=<?= $property['property_id'] ?>" class="btn btn-primary">Book Now</a>
            </div>
        </div>
    </div>

    <!-- Amenities -->
    <div class="property-amenities">
        <div class="page-container">
            <h1>Amenities</h1>
            <div class="row justify-content-between">
                <?php
                $amenity_types = ['Building', 'Common Area', 'Bedroom', 'Washroom'];
                foreach ($amenity_types as $type):
                    $filtered = array_filter($amenities, fn($a) => $a['type'] === $type);
                    if (empty($filtered)) continue;
                ?>
                <div class="col-md-auto">
                    <h5><?= $type ?></h5>
                    <?php foreach ($filtered as $amenity): ?>
                    <div class="amenity-container">
                        <img src="img/amenities/<?= htmlspecialchars($amenity['icon']) ?>.svg">
                        <span><?= htmlspecialchars($amenity['name']) ?></span>
                    </div>
                    <?php endforeach; ?>
                </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>

    <!-- About -->
    <div class="property-about page-container">
        <h1>About the Property</h1>
        <p><?= htmlspecialchars($property['description']) ?></p>
    </div>

    <!-- Ratings -->
    <div class="property-rating">
        <div class="page-container">
            <h1>Property Rating</h1>
            <div class="row align-items-center justify-content-between">
                <div class="col-md-6">
                    <?php
                    $criteria = [
                        ['icon'=>'fa-broom',    'label'=>'Cleanliness', 'val'=>$property['rating_clean']],
                        ['icon'=>'fa-utensils', 'label'=>'Food Quality','val'=>$property['rating_food']],
                        ['icon'=>'fa-lock',     'label'=>'Safety',      'val'=>$property['rating_safety']],
                    ];
                    foreach ($criteria as $c):
                    ?>
                    <div class="rating-criteria row">
                        <div class="col-6">
                            <i class="rating-criteria-icon fas <?= $c['icon'] ?>"></i>
                            <span class="rating-criteria-text"><?= $c['label'] ?></span>
                        </div>
                        <div class="rating-criteria-star-container col-6" title="<?= $c['val'] ?>">
                            <?= render_stars($c['val']) ?>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                <div class="col-md-4">
                    <div class="rating-circle">
                        <div class="total-rating"><?= $total_rating ?></div>
                        <div class="rating-circle-star-container">
                            <?= render_stars($total_rating) ?>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Testimonials -->
    <div class="property-testimonials page-container">
        <h1>What people say</h1>
        <?php foreach ($testimonials as $testimonial): ?>
        <div class="testimonial-block">
            <div class="testimonial-image-container">
                <img class="testimonial-img" src="img/man.png">
            </div>
            <div class="testimonial-text">
                <i class="fa fa-quote-left" aria-hidden="true"></i>
                <p><?= htmlspecialchars($testimonial['content']) ?></p>
            </div>
            <div class="testimonial-name">- <?= htmlspecialchars($testimonial['user_name']) ?></div>
        </div>
        <?php endforeach; ?>
    </div>

    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>
    <script type="text/javascript" src="js/property_detail.js"></script>
</body>
</html>
