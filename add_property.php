<?php
session_start();
require "includes/database_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    die();
}

$sql_cities = "SELECT * FROM cities";
$result_cities = mysqli_query($conn, $sql_cities);
$cities = mysqli_fetch_all($result_cities, MYSQLI_ASSOC);
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <title>Post Property | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <style>
        .post-property-container {
            max-width: 600px;
            margin: 40px auto;
            background: #fff;
            padding: 30px;
            border-radius: 16px;
            box-shadow: 0 4px 15px rgba(0,0,0,0.05);
            border: 1px solid #e7edf3;
        }
        .post-property-container h1 {
            font-size: 1.8rem;
            font-weight: 800;
            color: #1a1f36;
            margin-bottom: 20px;
            text-align: center;
        }
        .form-group label {
            font-weight: 700;
            color: #4c739a;
            margin-bottom: 5px;
        }
        .form-control {
            border-radius: 8px;
            border: 2px solid #e7edf3;
            padding: 12px 15px;
            font-weight: 600;
            color: #1a1f36;
            height: auto;
        }
        .form-control:focus {
            border-color: #137fec;
            box-shadow: 0 0 0 4px rgba(19,127,236,0.1);
        }
    </style>
</head>
<body>
    <?php include "includes/header.php"; ?>

    <div class="post-property-container">
        <h1>Add New Property</h1>
        <form id="add-property-form" action="api/add_property_submit.php" method="POST">
            <div class="form-group">
                <label>Property Name</label>
                <input type="text" class="form-control" name="name" placeholder="e.g. Navkar PG" required>
            </div>
            
            <div class="form-group">
                <label>City</label>
                <select class="form-control" name="city_id" required>
                    <option value="" disabled selected>Select City</option>
                    <?php foreach ($cities as $city): ?>
                        <option value="<?= $city['id'] ?>"><?= htmlspecialchars($city['name']) ?></option>
                    <?php endforeach; ?>
                </select>
            </div>

            <div class="form-group">
                <label>Address</label>
                <textarea class="form-control" name="address" rows="2" placeholder="Full local address" required></textarea>
            </div>

            <div class="form-group">
                <label>Description</label>
                <textarea class="form-control" name="description" rows="3" placeholder="Tell us about the property..." required></textarea>
            </div>

            <div class="row">
                <div class="col-md-6 form-group">
                    <label>Gender Category</label>
                    <select class="form-control" name="gender" required>
                        <option value="" disabled selected>Select Gender</option>
                        <option value="male">Male</option>
                        <option value="female">Female</option>
                        <option value="unisex">Unisex</option>
                    </select>
                </div>
                <div class="col-md-6 form-group">
                    <label>Monthly Rent (₹)</label>
                    <input type="number" class="form-control" name="rent" placeholder="e.g. 8000" required>
                </div>
            </div>

            <button type="submit" class="btn btn-primary btn-block mt-4" style="font-size: 1.1rem; padding: 12px;">Publish Property</button>
        </form>
    </div>

    <?php include "includes/footer.php"; ?>
    <script>
        document.getElementById('add-property-form').addEventListener('submit', function(e) {
            e.preventDefault();
            const btn = this.querySelector('button[type="submit"]');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin"></i> Submitting...';
            btn.disabled = true;

            fetch(this.action, {
                method: 'POST',
                body: new FormData(this)
            })
            .then(res => res.json())
            .then(data => {
                if(data.success) {
                    alert('Property Listed Successfully!');
                    window.location.href = 'property_detail.php?property_id=' + data.property_id;
                } else {
                    alert(data.message || 'Error occurred');
                    btn.innerHTML = 'Publish Property';
                    btn.disabled = false;
                }
            }).catch(err => {
                alert('Connection error');
                btn.innerHTML = 'Publish Property';
                btn.disabled = false;
            });
        });
    </script>
</body>
</html>