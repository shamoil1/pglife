<?php
session_start();
require "includes/database_connect.php";

$user_id = isset($_SESSION['user_id']) ? $_SESSION['user_id'] : NULL;
$city_name = isset($_GET["city"]) ? mysqli_real_escape_string($conn, trim($_GET["city"])) : "";

if (!$city_name) {
    header("location: index.php");
    die();
}

$sql_1 = "SELECT * FROM cities WHERE name = '$city_name'";
$result_1 = mysqli_query($conn, $sql_1);
if (!$result_1) { echo "Something went wrong!"; return; }
$city = mysqli_fetch_assoc($result_1);
if (!$city) { ?>
    <!DOCTYPE html><html><head><title>Not Found | PG Life</title>
    <?php include "includes/head_links.php"; ?></head>
    <body><?php include "includes/header.php"; ?>
    <div class="page-container text-center py-5">
        <h3>Sorry! We do not have any PG listed in <strong><?= htmlspecialchars($_GET['city']) ?></strong>.</h3>
        <a href="index.php" class="btn btn-primary mt-3">Back to Home</a>
    </div>
    <?php include "includes/footer.php"; ?></body></html>
<?php return; }

$city_id = $city['id'];

// Gender filter
$gender_filter = "";
if (isset($_GET['gender']) && in_array($_GET['gender'], ['male','female','unisex'])) {
    $g = $_GET['gender'];
    $gender_filter = "AND gender = '$g'";
}

// Sort
$sort = "";
if (isset($_GET['sort'])) {
    if ($_GET['sort'] === 'desc') $sort = "ORDER BY rent DESC";
    if ($_GET['sort'] === 'asc')  $sort = "ORDER BY rent ASC";
}

$sql_2 = "SELECT * FROM properties WHERE city_id = $city_id $gender_filter $sort";
$result_2 = mysqli_query($conn, $sql_2);
if (!$result_2) { echo "Something went wrong!"; return; }
$properties = mysqli_fetch_all($result_2, MYSQLI_ASSOC);

$sql_3 = "SELECT *
            FROM interested_users_properties iup
            INNER JOIN properties p ON iup.property_id = p.id
            WHERE p.city_id = $city_id";
$result_3 = mysqli_query($conn, $sql_3);
if (!$result_3) { echo "Something went wrong!"; return; }
$interested_users_properties = mysqli_fetch_all($result_3, MYSQLI_ASSOC);

// Build interest map for O(1) lookup
$interest_map = [];
foreach ($interested_users_properties as $iup) {
    $pid = $iup['property_id'];
    if (!isset($interest_map[$pid])) $interest_map[$pid] = ['count' => 0, 'is_interested' => false];
    $interest_map[$pid]['count']++;
    if ($iup['user_id'] == $user_id) $interest_map[$pid]['is_interested'] = true;
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Best PG's in <?= htmlspecialchars($city_name) ?> | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <link href="css/property_list.css" rel="stylesheet" />
</head>
<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item active" aria-current="page"><?= htmlspecialchars($city_name) ?></li>
        </ol>
    </nav>

    <div class="page-container">
        <div class="filter-bar row justify-content-around">
            <div class="col-auto" data-toggle="modal" data-target="#filter-modal" style="cursor:pointer">
                <img src="img/filter.svg" alt="filter" />
                <span>Filter</span>
            </div>
            <div class="col-auto" style="cursor:pointer"
                onclick="window.location='property_list.php?city=<?= urlencode($city_name) ?>&sort=desc<?= isset($_GET['gender']) ? "&gender=".$_GET['gender'] : "" ?>'">
                <img src="img/desc.svg" alt="sort-desc" />
                <span>Highest rent first</span>
            </div>
            <div class="col-auto" style="cursor:pointer"
                onclick="window.location='property_list.php?city=<?= urlencode($city_name) ?>&sort=asc<?= isset($_GET['gender']) ? "&gender=".$_GET['gender'] : "" ?>'">
                <img src="img/asc.svg" alt="sort-asc" />
                <span>Lowest rent first</span>
            </div>
        </div>

        <?php foreach ($properties as $property):
            $property_images = glob("img/properties/" . $property['id'] . "/*");
            $img_src = (!empty($property_images)) ? $property_images[0] : "img/delhi.png";
            $total_rating = round(($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3, 1);
            $pid = $property['id'];
            $interested_count = isset($interest_map[$pid]) ? $interest_map[$pid]['count'] : 0;
            $is_interested    = isset($interest_map[$pid]) ? $interest_map[$pid]['is_interested'] : false;
        ?>
        <div class="property-card property-id-<?= $pid ?> row">
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
                        <i class="is-interested-image <?= $is_interested ? 'fas' : 'far' ?> fa-heart" property_id="<?= $pid ?>"></i>
                        <div class="interested-text">
                            <span class="interested-user-count"><?= $interested_count ?></span> interested
                        </div>
                    </div>
                </div>
                <div class="detail-container">
                    <div class="property-name"><?= htmlspecialchars($property['name']) ?></div>
                    <div class="property-address"><?= htmlspecialchars($property['address']) ?></div>
                    <div class="property-gender">
                        <?php if ($property['gender'] == "male"): ?>
                            <img src="img/male.png" />
                        <?php elseif ($property['gender'] == "female"): ?>
                            <img src="img/female.png" />
                        <?php else: ?>
                            <img src="img/unisex.png" />
                        <?php endif; ?>
                    </div>
                </div>
                <div class="row no-gutters">
                    <div class="rent-container col-6">
                        <div class="rent">₹ <?= number_format($property['rent']) ?>/-</div>
                        <div class="rent-unit">per month</div>
                    </div>
                    <div class="button-container col-6">
                        <a href="property_detail.php?property_id=<?= $pid ?>" class="btn btn-primary">View</a>
                    </div>
                </div>
            </div>
        </div>
        <?php endforeach; ?>

        <?php if (count($properties) == 0): ?>
        <div class="no-property-container text-center py-5">
            <p>No PG to list for the selected filters.</p>
            <a href="property_list.php?city=<?= urlencode($city_name) ?>" class="btn btn-outline-primary">Clear Filters</a>
        </div>
        <?php endif; ?>
    </div>

    <!-- Filter Modal -->
    <div class="modal fade" id="filter-modal" tabindex="-1" role="dialog" aria-labelledby="filter-heading" aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h3 class="modal-title" id="filter-heading">Filters</h3>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <h5>Gender</h5>
                    <hr />
                    <div class="d-flex flex-wrap gap-2">
                        <a href="property_list.php?city=<?= urlencode($city_name) ?><?= isset($_GET['sort']) ? "&sort=".$_GET['sort'] : "" ?>" class="btn <?= !isset($_GET['gender']) ? 'btn-dark' : 'btn-outline-dark' ?>">No Filter</a>
                        <a href="property_list.php?city=<?= urlencode($city_name) ?>&gender=unisex<?= isset($_GET['sort']) ? "&sort=".$_GET['sort'] : "" ?>" class="btn <?= (isset($_GET['gender']) && $_GET['gender']=='unisex') ? 'btn-dark' : 'btn-outline-dark' ?>"><i class="fas fa-venus-mars mr-1"></i>Unisex</a>
                        <a href="property_list.php?city=<?= urlencode($city_name) ?>&gender=male<?= isset($_GET['sort']) ? "&sort=".$_GET['sort'] : "" ?>" class="btn <?= (isset($_GET['gender']) && $_GET['gender']=='male') ? 'btn-dark' : 'btn-outline-dark' ?>"><i class="fas fa-mars mr-1"></i>Male</a>
                        <a href="property_list.php?city=<?= urlencode($city_name) ?>&gender=female<?= isset($_GET['sort']) ? "&sort=".$_GET['sort'] : "" ?>" class="btn <?= (isset($_GET['gender']) && $_GET['gender']=='female') ? 'btn-dark' : 'btn-outline-dark' ?>"><i class="fas fa-venus mr-1"></i>Female</a>
                    </div>
                </div>
                <div class="modal-footer">
                    <button data-dismiss="modal" class="btn btn-success">Okay</button>
                </div>
            </div>
        </div>
    </div>

    <?php
    include "includes/signup_modal.php";
    include "includes/login_modal.php";
    include "includes/footer.php";
    ?>
    <script type="text/javascript" src="js/property_list.js"></script>
</body>
</html>
