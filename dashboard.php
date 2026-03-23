<?php
session_start();
require "includes/database_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    die();
}
$user_id = $_SESSION['user_id'];

$sql_1 = "SELECT * FROM users WHERE id = $user_id";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) { echo "Something went wrong!"; return; }
$user = mysqli_fetch_assoc($result_1);
if (!$user)     { echo "Something went wrong!"; return; }

$sql_2 = "SELECT *
            FROM interested_users_properties iup
            INNER JOIN properties p ON iup.property_id = p.id
            WHERE iup.user_id = $user_id";
$result_2 = mysqli_query($conn, $sql_2);
if (!$result_2) { echo "Something went wrong!"; return; }
$interested_properties = mysqli_fetch_all($result_2, MYSQLI_ASSOC);

// Fetch Bookings
$sql_3 = "SELECT b.id AS booking_id, b.total_rent, b.booking_date, b.status, p.* 
            FROM bookings b
            INNER JOIN properties p ON b.property_id = p.id
            WHERE b.user_id = $user_id ORDER BY b.booking_date DESC";
$result_3 = mysqli_query($conn, $sql_3);
$booked_properties = $result_3 ? mysqli_fetch_all($result_3, MYSQLI_ASSOC) : [];
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Dashboard | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <link href="css/dashboard.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page">Dashboard</li>
        </ol>
    </nav>

    <div class="my-profile page-container">
        <h1>My Profile</h1>
        <div class="row">
            <div class="col-md-3 profile-img-container">
                <i class="fas fa-user profile-img"></i>
            </div>
            <div class="col-md-9">
                <div class="row no-gutters justify-content-between align-items-end">
                    <div class="profile">
                        <div class="name"><?= htmlspecialchars($user['full_name']) ?></div>
                        <div class="email"><?= htmlspecialchars($user['email']) ?></div>
                        <div class="phone"><?= htmlspecialchars($user['phone']) ?></div>
                        <div class="college"><?= htmlspecialchars($user['college_name']) ?></div>
                    </div>
                    <div class="edit">
                        <div class="edit-profile">Edit Profile</div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <?php if (count($booked_properties) > 0): ?>
    <div class="my-interested-properties" style="margin-top: 40px; margin-bottom: 40px;">
        <div class="page-container">
            <h1>My Bookings</h1>
            <?php foreach ($booked_properties as $property):
                $property_images = glob("img/properties/" . $property['id'] . "/*");
                $img_src = (!empty($property_images)) ? $property_images[0] : "img/delhi.png";
                $total_rating = round(($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3, 1);
            ?>
            <div class="property-card property-id-<?= $property['id'] ?> row" style="border: 2px solid #28a745; box-shadow: 0 10px 30px rgba(40, 167, 69, 0.1);">
                <div class="image-container col-md-4">
                    <img src="<?= htmlspecialchars($img_src) ?>" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <div class="star-container" title="<?= $total_rating ?>">
                            <?php
                            $rating = $total_rating;
                            for ($i = 0; $i < 5; $i++) {
                                if ($rating >= $i + 0.8) echo '<i class="fas fa-star"></i>';
                                elseif ($rating >= $i + 0.3) echo '<i class="fas fa-star-half-alt"></i>';
                                else echo '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <div class="interested-container">
                            <span style="color: #28a745; font-weight: 800; font-size: 14px;"><i class="fas fa-check-circle" style="margin-right: 5px;"></i> <?= htmlspecialchars($property['status']) ?></span>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?= htmlspecialchars($property['name']) ?></div>
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
                            <div class="rent">₹ <?= number_format($property['total_rent']) ?>/-</div>
                            <div class="rent-unit">Total Paid</div>
                            <div class="text-muted" style="font-size: 11px; margin-top: 4px;">Booked on <?= date("d M Y", strtotime($property['booking_date'])) ?></div>
                        </div>
                        <div class="button-container col-6">
                            <a href="property_detail.php?property_id=<?= $property['id'] ?>" class="btn btn-primary" style="background:#28a745; border-color:#28a745;">View PG</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php if (count($interested_properties) > 0): ?>
    <div class="my-interested-properties">
        <div class="page-container">
            <h1>My Interested Properties</h1>
            <?php foreach ($interested_properties as $property):
                $property_images = glob("img/properties/" . $property['id'] . "/*");
                $img_src = (!empty($property_images)) ? $property_images[0] : "img/delhi.png";
                $total_rating = round(($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3, 1);
            ?>
            <div class="property-card property-id-<?= $property['id'] ?> row">
                <div class="image-container col-md-4">
                    <img src="<?= htmlspecialchars($img_src) ?>" />
                </div>
                <div class="content-container col-md-8">
                    <div class="row no-gutters justify-content-between">
                        <div class="star-container" title="<?= $total_rating ?>">
                            <?php
                            $rating = $total_rating;
                            for ($i = 0; $i < 5; $i++) {
                                if ($rating >= $i + 0.8) echo '<i class="fas fa-star"></i>';
                                elseif ($rating >= $i + 0.3) echo '<i class="fas fa-star-half-alt"></i>';
                                else echo '<i class="far fa-star"></i>';
                            }
                            ?>
                        </div>
                        <div class="interested-container">
                            <i class="is-interested-image fas fa-heart" property_id="<?= $property['id'] ?>"></i>
                        </div>
                    </div>
                    <div class="detail-container">
                        <div class="property-name"><?= htmlspecialchars($property['name']) ?></div>
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
                            <a href="property_detail.php?property_id=<?= $property['id'] ?>" class="btn btn-primary">View</a>
                        </div>
                    </div>
                </div>
            </div>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

    <?php include "includes/footer.php"; ?>
    <script type="text/javascript" src="js/dashboard.js"></script>
</body>
</html>
