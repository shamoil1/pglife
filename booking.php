<?php
session_start();
require "includes/database_connect.php";

if (!isset($_SESSION["user_id"])) {
    header("location: index.php");
    die();
}

$user_id     = $_SESSION['user_id'];
$property_id = isset($_GET['property_id']) ? intval($_GET['property_id']) : 0;

if (!$property_id) { header("location: index.php"); die(); }

$sql = "SELECT p.*, p.id AS property_id, p.name AS property_name, c.name AS city_name
        FROM properties p
        INNER JOIN cities c ON p.city_id = c.id
        WHERE p.id = $property_id";
$result = mysqli_query($conn, $sql);
if (!$result) { echo "Something went wrong!"; return; }
$property = mysqli_fetch_assoc($result);
if (!$property) { echo "Property not found!"; return; }

$result_u = mysqli_query($conn, "SELECT * FROM users WHERE id = $user_id");
$user = mysqli_fetch_assoc($result_u);

$property_images = glob("img/properties/" . $property_id . "/*");
$hero_image = (!empty($property_images)) ? $property_images[0] : "img/delhi.png";
$avg_rating = round(($property['rating_clean'] + $property['rating_food'] + $property['rating_safety']) / 3, 1);

function render_stars($r) {
    $h = '';
    for ($i = 0; $i < 5; $i++) {
        if ($r >= $i+0.8)     $h .= '<i class="fas fa-star"></i>';
        elseif ($r >= $i+0.3) $h .= '<i class="fas fa-star-half-alt"></i>';
        else                  $h .= '<i class="far fa-star"></i>';
    }
    return $h;
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Book <?= htmlspecialchars($property['property_name']) ?> | PG Life</title>
    <?php include "includes/head_links.php"; ?>
    <link href="https://fonts.googleapis.com/css2?family=Manrope:wght@400;600;700;800&display=swap" rel="stylesheet"/>
    <link href="https://fonts.googleapis.com/css2?family=Material+Symbols+Outlined:wght@100..700,0..1&display=swap" rel="stylesheet"/>
    <style>
        :root {
            --pg-primary:  #137fec;
            --pg-navy:     #1a1f36;
            --pg-light-bg: #f6f7f8;
            --pg-border:   #e7edf3;
            --pg-muted:    #4c739a;
        }
        body { background: var(--pg-light-bg); font-family: 'Manrope', sans-serif; }
        .breadcrumb { background: transparent; }
        .breadcrumb-item a { color: var(--pg-primary); }

        /* Stepper */
        .stepper { display:flex; align-items:center; margin-bottom:1.5rem; }
        .step { display:flex; align-items:center; gap:.4rem; font-size:.8rem; font-weight:700; color:var(--pg-muted); white-space:nowrap; }
        .step-circle { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.78rem; font-weight:800; border:2px solid var(--pg-border); background:#fff; color:var(--pg-muted); }
        .step.active .step-circle { background:var(--pg-primary); border-color:var(--pg-primary); color:#fff; }
        .step.active { color:var(--pg-primary); }
        .step.done .step-circle { background:#e8f4fd; border-color:var(--pg-primary); color:var(--pg-primary); }
        .step-line { flex:1; height:2px; background:var(--pg-border); margin:0 .5rem; min-width:20px; }
        .step-line.done { background:var(--pg-primary); }

        .booking-wrapper { max-width:1060px; margin:0 auto; padding:1.5rem 1rem 4rem; }
        .page-heading h1 { font-size:1.8rem; font-weight:800; color:var(--pg-navy); letter-spacing:-.02em; }
        .page-heading p  { color:var(--pg-muted); font-size:.9rem; }

        .pg-card { background:#fff; border:1px solid var(--pg-border); border-radius:1.25rem; overflow:hidden; box-shadow:0 2px 16px rgba(0,0,0,.06); }

        .property-hero { height:170px; background-size:cover; background-position:center; position:relative; }
        .property-hero-overlay { position:absolute; inset:0; background:linear-gradient(to top,rgba(0,0,0,.65),transparent); display:flex; align-items:flex-end; padding:.9rem 1.2rem; }
        .badge-pg { background:var(--pg-primary); color:#fff; font-size:.68rem; font-weight:800; padding:.22rem .7rem; border-radius:999px; letter-spacing:.07em; text-transform:uppercase; }

        .card-body-inner { padding:1.2rem 1.5rem; }
        .prop-name  { font-size:1.15rem; font-weight:800; color:var(--pg-navy); }
        .prop-addr  { font-size:.83rem; color:var(--pg-muted); margin-top:.2rem; }
        .rating-row { display:flex; align-items:center; gap:.35rem; margin-top:.5rem; }
        .rating-row .stars { color:#f5a623; font-size:.85rem; }
        .rating-row .score { font-weight:700; font-size:.85rem; color:var(--pg-navy); }
        .mini-ratings { font-size:.8rem; color:var(--pg-muted); margin-top:.75rem; }
        .mini-ratings .mr { display:flex; justify-content:space-between; margin-bottom:.25rem; }
        .mini-ratings strong { color:var(--pg-navy); }
        .rent-display { margin-top:.9rem; padding-top:.9rem; border-top:1px solid var(--pg-border); }
        .rent-amount { font-size:1.4rem; font-weight:800; color:var(--pg-primary); }
        .rent-unit   { font-size:.72rem; color:var(--pg-muted); }

        .trust-strip { background:rgba(19,127,236,.05); border:1px solid rgba(19,127,236,.12); border-radius:1rem; padding:.9rem 1.2rem; display:flex; align-items:center; gap:.9rem; }
        .trust-strip .material-symbols-outlined { color:var(--pg-primary); font-size:1.7rem; }
        .trust-strip h6 { color:var(--pg-primary); font-weight:700; margin-bottom:.1rem; font-size:.88rem; }
        .trust-strip p  { color:var(--pg-muted); font-size:.75rem; margin:0; }

        .form-card-header { padding:1.4rem 1.8rem 0; }
        .form-card-header h2 { font-size:1.2rem; font-weight:800; color:var(--pg-navy); }
        .form-section { padding:1.4rem 1.8rem 1.8rem; }

        .flabel { font-size:.8rem; font-weight:700; color:var(--pg-navy); margin-bottom:.3rem; display:block; }
        .finput {
            width:100%; padding:.65rem .9rem;
            border:1.5px solid var(--pg-border); border-radius:.7rem;
            font-size:.86rem; font-family:'Manrope',sans-serif;
            background:#fff; color:var(--pg-navy);
            transition:border .2s, box-shadow .2s;
        }
        .finput:focus { outline:none; border-color:var(--pg-primary); box-shadow:0 0 0 3px rgba(19,127,236,.1); }
        .phone-wrap { display:flex; }
        .phone-prefix { padding:.65rem .9rem; background:#f6f7f8; border:1.5px solid var(--pg-border); border-right:none; border-radius:.7rem 0 0 .7rem; font-size:.83rem; color:var(--pg-muted); font-weight:600; }
        .phone-wrap .finput { border-radius:0 .7rem .7rem 0; }
        .fdivider { border:none; border-top:1.5px solid var(--pg-border); margin:1.2rem 0; }

        /* Stay selector */
        .stay-selector { display:flex; gap:.6rem; flex-wrap:wrap; }
        .stay-option { flex:1; min-width:120px; border:2px solid var(--pg-border); border-radius:.7rem; padding:.8rem .9rem; cursor:pointer; text-align:center; background:#fff; transition:all .2s; }
        .stay-option:hover, .stay-option.selected { border-color:var(--pg-primary); background:#e8f4fd; }
        .stay-option .s-icon { font-size:1.3rem; color:var(--pg-primary); margin-bottom:.25rem; }
        .stay-option .s-label { font-size:.8rem; font-weight:700; color:var(--pg-navy); }
        .stay-option .s-sub { font-size:.7rem; color:var(--pg-muted); }

        /* Terms */
        .terms-row { display:flex; align-items:flex-start; gap:.65rem; }
        .terms-row input { margin-top:.18rem; accent-color:var(--pg-primary); width:15px; height:15px; }
        .terms-row label { font-size:.76rem; color:var(--pg-muted); line-height:1.5; }
        .terms-row a { color:var(--pg-primary); font-weight:600; text-decoration:underline; }

        /* CTA */
        .btn-pg {
            width:100%; background:var(--pg-primary); color:#fff; border:none;
            border-radius:999px; padding:.95rem; font-size:.95rem; font-weight:800;
            font-family:'Manrope',sans-serif; cursor:pointer;
            display:flex; align-items:center; justify-content:center; gap:.45rem;
            transition:background .2s, transform .15s, box-shadow .2s;
            box-shadow:0 5px 18px rgba(19,127,236,.28);
            text-decoration:none;
        }
        .btn-pg:hover { background:#0e6fd4; transform:translateY(-1px); box-shadow:0 7px 22px rgba(19,127,236,.35); color:#fff; }

        .assurance-strip { display:flex; justify-content:center; gap:1.5rem; flex-wrap:wrap; border:2px dashed var(--pg-border); border-radius:1rem; padding:.9rem; margin-top:.5rem; }
        .assurance-item { display:flex; align-items:center; gap:.35rem; color:var(--pg-muted); font-size:.76rem; font-weight:600; }
        .assurance-item .material-symbols-outlined { font-size:1rem; }

        @media(max-width:767px) {
            .form-section { padding:1rem; }
            .form-card-header { padding:1rem 1rem 0; }
            .page-heading h1 { font-size:1.4rem; }
            .stepper .step span { display:none; }
        }
    </style>
</head>
<body>
    <?php include "includes/header.php"; ?>

    <nav aria-label="breadcrumb">
        <ol class="breadcrumb py-2 px-3 px-md-4">
            <li class="breadcrumb-item"><a href="index.php">Home</a></li>
            <li class="breadcrumb-item"><a href="property_list.php?city=<?= urlencode($property['city_name']) ?>"><?= htmlspecialchars($property['city_name']) ?></a></li>
            <li class="breadcrumb-item"><a href="property_detail.php?property_id=<?= $property_id ?>"><?= htmlspecialchars($property['property_name']) ?></a></li>
            <li class="breadcrumb-item active">Book Now</li>
        </ol>
    </nav>

    <div class="booking-wrapper">

        <!-- Stepper -->
        <div class="stepper">
            <div class="step active"><div class="step-circle">1</div><span>Booking Details</span></div>
            <div class="step-line"></div>
            <div class="step"><div class="step-circle">2</div><span>Payment</span></div>
            <div class="step-line"></div>
            <div class="step"><div class="step-circle"><i class="fas fa-check" style="font-size:.65rem"></i></div><span>Confirmation</span></div>
        </div>

        <div class="page-heading mb-4">
            <h1>Book Your PG</h1>
            <p>Review property details and fill in your booking information to proceed.</p>
        </div>

        <div class="row mx-0">

            <!-- LEFT: Property Summary -->
            <div class="col-lg-5 pl-0 pr-lg-3 mb-4 mb-lg-0">
                <div class="pg-card mb-3">
                    <div class="property-hero" style="background-image:url('<?= htmlspecialchars($hero_image) ?>')">
                        <div class="property-hero-overlay">
                            <span class="badge-pg">
                                <?= $property['gender']==='male' ? '♂ Boys PG' : ($property['gender']==='female' ? '♀ Girls PG' : '⚤ Unisex PG') ?>
                            </span>
                        </div>
                    </div>
                    <div class="card-body-inner">
                        <div class="prop-name"><?= htmlspecialchars($property['property_name']) ?></div>
                        <div class="prop-addr"><i class="fas fa-map-marker-alt mr-1" style="color:var(--pg-primary)"></i><?= htmlspecialchars($property['address']) ?></div>
                        <div class="rating-row">
                            <span class="stars"><?= render_stars($avg_rating) ?></span>
                            <span class="score"><?= $avg_rating ?> / 5.0</span>
                        </div>
                        <div class="mini-ratings">
                            <div class="mr"><span><i class="fas fa-broom mr-1"></i>Cleanliness</span><strong><?= $property['rating_clean'] ?></strong></div>
                            <div class="mr"><span><i class="fas fa-utensils mr-1"></i>Food</span><strong><?= $property['rating_food'] ?></strong></div>
                            <div class="mr"><span><i class="fas fa-lock mr-1"></i>Safety</span><strong><?= $property['rating_safety'] ?></strong></div>
                        </div>
                        <div class="rent-display">
                            <div class="rent-amount">₹ <?= number_format($property['rent']) ?>/-</div>
                            <div class="rent-unit">per month</div>
                        </div>
                    </div>
                </div>
                <div class="trust-strip">
                    <span class="material-symbols-outlined">security</span>
                    <div>
                        <h6>Secure Booking</h6>
                        <p>Your information is protected by industry-standard encryption.</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Booking Form -->
            <div class="col-lg-7 px-0">
                <div class="pg-card">
                    <div class="form-card-header"><h2>Your Booking Details</h2></div>
                    <div class="form-section">

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="flabel">Full Name</label>
                                <input type="text" class="finput" value="<?= htmlspecialchars($user['full_name']) ?>" placeholder="Your full name"/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="flabel">Email Address</label>
                                <input type="email" class="finput" value="<?= htmlspecialchars($user['email']) ?>" placeholder="your@email.com"/>
                            </div>
                        </div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="flabel">Mobile Number</label>
                                <div class="phone-wrap">
                                    <span class="phone-prefix">+91</span>
                                    <input type="tel" class="finput" value="<?= htmlspecialchars($user['phone']) ?>" placeholder="9876543210"/>
                                </div>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="flabel">College / Organisation</label>
                                <input type="text" class="finput" value="<?= htmlspecialchars($user['college_name'] ?? '') ?>" placeholder="Your institution"/>
                            </div>
                        </div>

                        <hr class="fdivider">

                        <div class="mb-3">
                            <label class="flabel mb-2">Type of Stay</label>
                            <div class="stay-selector">
                                <div class="stay-option selected" onclick="selectStay(this)">
                                    <div class="s-icon"><i class="fas fa-user"></i></div>
                                    <div class="s-label">Single Room</div>
                                    <div class="s-sub">1 person</div>
                                </div>
                                <div class="stay-option" onclick="selectStay(this)">
                                    <div class="s-icon"><i class="fas fa-user-friends"></i></div>
                                    <div class="s-label">Double Sharing</div>
                                    <div class="s-sub">2 persons</div>
                                </div>
                                <div class="stay-option" onclick="selectStay(this)">
                                    <div class="s-icon"><i class="fas fa-users"></i></div>
                                    <div class="s-label">Triple Sharing</div>
                                    <div class="s-sub">3 persons</div>
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="flabel">Move-in Date</label>
                                <input type="date" class="finput" min="<?= date('Y-m-d') ?>"/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="flabel">Duration</label>
                                <select class="finput" style="appearance:auto">
                                    <option>1 month</option>
                                    <option>3 months</option>
                                    <option selected>6 months</option>
                                    <option>12 months</option>
                                </select>
                            </div>
                        </div>

                        <div class="mb-3">
                            <label class="flabel">Special Requests (Optional)</label>
                            <textarea class="finput" rows="2" placeholder="Any dietary requirements, accessibility needs, etc."></textarea>
                        </div>

                        <hr class="fdivider">

                        <div class="terms-row mb-4">
                            <input type="checkbox" id="terms"/>
                            <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I confirm the details above are correct.</label>
                        </div>

                        <a href="payment.php?property_id=<?= $property_id ?>" class="btn-pg">
                            <span>Proceed to Payment</span>
                            <span class="material-symbols-outlined" style="font-size:1.05rem">arrow_forward</span>
                        </a>

                        <div class="assurance-strip mt-3">
                            <div class="assurance-item"><span class="material-symbols-outlined">verified</span>Money-back guarantee</div>
                            <div class="assurance-item"><span class="material-symbols-outlined">lock</span>SSL Encrypted</div>
                            <div class="assurance-item"><span class="material-symbols-outlined">history</span>Cancel anytime</div>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>

    <?php include "includes/footer.php"; ?>
    <script>
        function selectStay(el) {
            document.querySelectorAll('.stay-option').forEach(o => o.classList.remove('selected'));
            el.classList.add('selected');
        }
    </script>
</body>
</html>
