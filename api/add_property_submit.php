<?php
session_start();
require "../includes/database_connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["success" => false, "message" => "Please login first"]);
    exit;
}

$name = $_POST['name'] ?? '';
$city_id = $_POST['city_id'] ?? '';
$address = $_POST['address'] ?? '';
$description = $_POST['description'] ?? '';
$gender = $_POST['gender'] ?? '';
$rent = $_POST['rent'] ?? '';

if (!$name || !$city_id || !$address || !$gender || !$rent) {
    echo json_encode(["success" => false, "message" => "All fields are required"]);
    exit;
}

// Default random ratings to look realistic for new properties
$clean_rating = rand(35, 50) / 10;
$food_rating = rand(35, 50) / 10;
$safety_rating = rand(35, 50) / 10;

$name = mysqli_real_escape_string($conn, $name);
$address = mysqli_real_escape_string($conn, $address);
$description = mysqli_real_escape_string($conn, $description);

$sql = "INSERT INTO properties (city_id, name, address, description, gender, rent, rating_clean, rating_food, rating_safety) 
        VALUES ('$city_id', '$name', '$address', '$description', '$gender', '$rent', '$clean_rating', '$food_rating', '$safety_rating')";

if (mysqli_query($conn, $sql)) {
    $property_id = mysqli_insert_id($conn);

    // Give it a generic amenity or two 
    mysqli_query($conn, "INSERT INTO properties_amenities (property_id, amenity_id) VALUES ($property_id, 1), ($property_id, 5)");

    echo json_encode(["success" => true, "property_id" => $property_id]);
} else {
    echo json_encode(["success" => false, "message" => "Database Error: " . mysqli_error($conn)]);
}
?>