<?php
session_start();
require "../includes/database_connect.php";

header('Content-Type: application/json');

if (!isset($_SESSION["user_id"])) {
    echo json_encode(["status" => "error", "message" => "User not logged in"]);
    exit;
}

$user_id = $_SESSION['user_id'];
$property_id = isset($_POST['property_id']) ? intval($_POST['property_id']) : 0;
$total_rent = isset($_POST['total_rent']) ? floatval($_POST['total_rent']) : 0;

if (!$property_id || !$total_rent) {
    echo json_encode(["status" => "error", "message" => "Invalid data"]);
    exit;
}

$sql = "INSERT INTO bookings (user_id, property_id, total_rent) VALUES ('$user_id', '$property_id', '$total_rent')";
if (mysqli_query($conn, $sql)) {
    echo json_encode(["status" => "success", "message" => "Booking confirmed successfully"]);
} else {
    echo json_encode(["status" => "error", "message" => "Database error: " . mysqli_error($conn)]);
}
?>