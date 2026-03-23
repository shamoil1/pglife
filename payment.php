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

$monthly_rent = (float)$property['rent'];
$security_dep = $monthly_rent * 2;
$platform_fee = 499;
$total_due    = $monthly_rent + $security_dep + $platform_fee;
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8"/>
    <meta name="viewport" content="width=device-width, initial-scale=1"/>
    <title>Payment | <?= htmlspecialchars($property['property_name']) ?> | PG Life</title>
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
            --pg-success:  #28a745;
        }
        body { background:var(--pg-light-bg); font-family:'Manrope',sans-serif; }
        .breadcrumb { background:transparent; }
        .breadcrumb-item a { color:var(--pg-primary); }

        /* Stepper */
        .stepper { display:flex; align-items:center; margin-bottom:1.5rem; }
        .step { display:flex; align-items:center; gap:.4rem; font-size:.8rem; font-weight:700; color:var(--pg-muted); white-space:nowrap; }
        .step-circle { width:32px; height:32px; border-radius:50%; display:flex; align-items:center; justify-content:center; font-size:.78rem; font-weight:800; border:2px solid var(--pg-border); background:#fff; color:var(--pg-muted); }
        .step.active .step-circle { background:var(--pg-primary); border-color:var(--pg-primary); color:#fff; }
        .step.active { color:var(--pg-primary); }
        .step.done .step-circle { background:#e8f4fd; border-color:var(--pg-primary); color:var(--pg-primary); }
        .step.done { color:var(--pg-primary); }
        .step-line { flex:1; height:2px; background:var(--pg-border); margin:0 .5rem; min-width:20px; }
        .step-line.done { background:var(--pg-primary); }

        .payment-wrapper { max-width:1060px; margin:0 auto; padding:1.5rem 1rem 4rem; }
        .page-heading h1 { font-size:1.8rem; font-weight:800; color:var(--pg-navy); letter-spacing:-.02em; }
        .page-heading p  { color:var(--pg-muted); font-size:.9rem; }

        .pg-card { background:#fff; border:1px solid var(--pg-border); border-radius:1.25rem; overflow:hidden; box-shadow:0 2px 16px rgba(0,0,0,.06); }

        /* Summary card */
        .summary-hero { height:155px; background-size:cover; background-position:center; position:relative; }
        .summary-hero-overlay { position:absolute; inset:0; background:linear-gradient(to top,rgba(0,0,0,.7),transparent); display:flex; align-items:flex-end; padding:.9rem 1.2rem; }
        .badge-pg { background:var(--pg-primary); color:#fff; font-size:.68rem; font-weight:800; padding:.22rem .7rem; border-radius:999px; letter-spacing:.07em; text-transform:uppercase; }
        .summary-body { padding:1.2rem 1.5rem; }
        .sum-title { font-size:1.05rem; font-weight:800; color:var(--pg-navy); }
        .sum-addr  { font-size:.8rem; color:var(--pg-muted); margin-top:.15rem; }

        .price-table { margin-top:.9rem; font-size:.86rem; }
        .price-row { display:flex; justify-content:space-between; padding:.45rem 0; color:var(--pg-muted); }
        .price-row.total { font-size:.95rem; font-weight:800; border-top:2px solid var(--pg-border); margin-top:.4rem; padding-top:.7rem; color:var(--pg-navy); }
        .price-row.total span:last-child { color:var(--pg-primary); }

        .whats-included { margin-top:.9rem; padding-top:.9rem; border-top:1px solid var(--pg-border); font-size:.8rem; }
        .wi-label { font-size:.72rem; color:var(--pg-muted); font-weight:700; text-transform:uppercase; letter-spacing:.05em; margin-bottom:.45rem; }
        .wi-item { color:var(--pg-navy); margin-bottom:.3rem; }
        .wi-item i { color:var(--pg-primary); margin-right:.4rem; }

        .trust-strip { background:rgba(19,127,236,.05); border:1px solid rgba(19,127,236,.12); border-radius:1rem; padding:.9rem 1.2rem; display:flex; align-items:center; gap:.9rem; }
        .trust-strip .material-symbols-outlined { color:var(--pg-primary); font-size:1.7rem; }
        .trust-strip h6 { color:var(--pg-primary); font-weight:700; margin-bottom:.1rem; font-size:.88rem; }
        .trust-strip p  { color:var(--pg-muted); font-size:.75rem; margin:0; }

        /* Form */
        .form-card-header { padding:1.4rem 1.8rem 0; }
        .form-card-header h2 { font-size:1.2rem; font-weight:800; color:var(--pg-navy); margin-bottom:.15rem; }
        .form-card-header p  { font-size:.8rem; color:var(--pg-muted); }
        .form-section { padding:1.4rem 1.8rem 1.8rem; }

        .flabel { font-size:.8rem; font-weight:700; color:var(--pg-navy); margin-bottom:.3rem; display:block; }
        .finput { width:100%; padding:.65rem .9rem; border:1.5px solid var(--pg-border); border-radius:.7rem; font-size:.86rem; font-family:'Manrope',sans-serif; background:#fff; color:var(--pg-navy); transition:border .2s,box-shadow .2s; }
        .finput:focus { outline:none; border-color:var(--pg-primary); box-shadow:0 0 0 3px rgba(19,127,236,.1); }
        .fdivider { border:none; border-top:1.5px solid var(--pg-border); margin:1.2rem 0; }

        /* Pay method tabs */
        .pay-tabs { display:flex; gap:.6rem; flex-wrap:wrap; margin-bottom:1.2rem; }
        .pay-tab { flex:1; min-width:100px; padding:.7rem; border:2px solid var(--pg-border); border-radius:.7rem; cursor:pointer; text-align:center; background:#fff; transition:all .2s; }
        .pay-tab:hover, .pay-tab.active { border-color:var(--pg-primary); background:#e8f4fd; }
        .pay-tab i { display:block; font-size:1.3rem; color:var(--pg-primary); margin-bottom:.25rem; }
        .pay-tab span { font-size:.75rem; font-weight:700; color:var(--pg-navy); }

        .pay-section { display:none; }
        .pay-section.show { display:block; }

        .card-num-wrap { position:relative; }
        .card-icon { position:absolute; right:.9rem; top:50%; transform:translateY(-50%); color:var(--pg-muted); }

        /* Terms */
        .terms-row { display:flex; align-items:flex-start; gap:.65rem; }
        .terms-row input { margin-top:.18rem; accent-color:var(--pg-primary); width:15px; height:15px; }
        .terms-row label { font-size:.76rem; color:var(--pg-muted); line-height:1.5; }
        .terms-row a { color:var(--pg-primary); font-weight:600; text-decoration:underline; }

        /* CTA */
        .btn-pg { width:100%; background:var(--pg-primary); color:#fff; border:none; border-radius:999px; padding:.95rem; font-size:.95rem; font-weight:800; font-family:'Manrope',sans-serif; cursor:pointer; display:flex; align-items:center; justify-content:center; gap:.45rem; transition:background .2s,transform .15s,box-shadow .2s; box-shadow:0 5px 18px rgba(19,127,236,.28); }
        .btn-pg:hover { background:#0e6fd4; transform:translateY(-1px); box-shadow:0 7px 22px rgba(19,127,236,.35); }
        .btn-pg:disabled { background:#7ab8f5; cursor:not-allowed; transform:none; }

        .assurance-strip { display:flex; justify-content:center; gap:1.5rem; flex-wrap:wrap; border:2px dashed var(--pg-border); border-radius:1rem; padding:.9rem; margin-top:.5rem; }
        .assurance-item { display:flex; align-items:center; gap:.35rem; color:var(--pg-muted); font-size:.76rem; font-weight:600; }
        .assurance-item .material-symbols-outlined { font-size:1rem; }

        /* Success overlay */
        .success-overlay { display:none; position:fixed; inset:0; background:rgba(0,0,0,.55); z-index:9999; align-items:center; justify-content:center; }
        .success-overlay.show { display:flex; }
        .success-box { background:#fff; border-radius:1.5rem; padding:2.5rem 2rem; text-align:center; max-width:400px; width:90%; animation:popIn .4s cubic-bezier(.34,1.56,.64,1) both; }
        @keyframes popIn { from{transform:scale(.7);opacity:0} to{transform:scale(1);opacity:1} }
        .success-icon { width:68px; height:68px; background:#e8f5e9; border-radius:50%; display:flex; align-items:center; justify-content:center; margin:0 auto 1.1rem; }
        .success-icon i { font-size:1.8rem; color:var(--pg-success); }
        .success-box h3 { font-size:1.4rem; font-weight:800; color:var(--pg-navy); margin-bottom:.4rem; }
        .success-box p { color:var(--pg-muted); font-size:.88rem; }
        .btn-ok { margin-top:1.4rem; background:var(--pg-primary); color:#fff; border:none; border-radius:999px; padding:.7rem 2.2rem; font-size:.9rem; font-weight:800; font-family:'Manrope',sans-serif; cursor:pointer; }
        .btn-ok:hover { background:#0e6fd4; }

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
            <li class="breadcrumb-item"><a href="booking.php?property_id=<?= $property_id ?>">Book Now</a></li>
            <li class="breadcrumb-item active">Payment</li>
        </ol>
    </nav>

    <div class="payment-wrapper">

        <!-- Stepper -->
        <div class="stepper">
            <div class="step done"><div class="step-circle"><i class="fas fa-check" style="font-size:.62rem"></i></div><span>Booking Details</span></div>
            <div class="step-line done"></div>
            <div class="step active"><div class="step-circle">2</div><span>Payment</span></div>
            <div class="step-line"></div>
            <div class="step"><div class="step-circle"><i class="fas fa-check" style="font-size:.62rem"></i></div><span>Confirmation</span></div>
        </div>

        <div class="page-heading mb-4">
            <h1>Secure Payment</h1>
            <p>Complete your payment to confirm the booking. A confirmation will be sent to your email.</p>
        </div>

        <div class="row mx-0">

            <!-- LEFT: Order summary -->
            <div class="col-lg-5 pl-0 pr-lg-3 mb-4 mb-lg-0">
                <div class="pg-card mb-3">
                    <div class="summary-hero" style="background-image:url('<?= htmlspecialchars($hero_image) ?>')">
                        <div class="summary-hero-overlay">
                            <span class="badge-pg">Order Summary</span>
                        </div>
                    </div>
                    <div class="summary-body">
                        <div class="sum-title"><?= htmlspecialchars($property['property_name']) ?></div>
                        <div class="sum-addr"><i class="fas fa-map-marker-alt mr-1" style="color:var(--pg-primary)"></i><?= htmlspecialchars($property['address']) ?></div>
                        <div class="price-table">
                            <div class="price-row"><span>First Month Rent</span><span>₹ <?= number_format($monthly_rent) ?></span></div>
                            <div class="price-row"><span>Security Deposit (2 months)</span><span>₹ <?= number_format($security_dep) ?></span></div>
                            <div class="price-row"><span>Platform Fee (one-time)</span><span>₹ <?= number_format($platform_fee) ?></span></div>
                            <div class="price-row total"><span>Total Due Today</span><span>₹ <?= number_format($total_due) ?></span></div>
                        </div>
                        <div class="whats-included">
                            <div class="wi-label">What's included</div>
                            <div class="wi-item"><i class="fas fa-check-circle"></i>Verified room as shown</div>
                            <div class="wi-item"><i class="fas fa-check-circle"></i>Direct owner contact details</div>
                            <div class="wi-item"><i class="fas fa-check-circle"></i>24/7 PG Life support</div>
                            <div class="wi-item"><i class="fas fa-check-circle"></i>Refundable security deposit</div>
                        </div>
                    </div>
                </div>
                <div class="trust-strip">
                    <span class="material-symbols-outlined">security</span>
                    <div>
                        <h6>100% Secure Payment</h6>
                        <p>Powered by Razorpay. Your card details are never stored on our servers.</p>
                    </div>
                </div>
            </div>

            <!-- RIGHT: Payment form -->
            <div class="col-lg-7 px-0">
                <div class="pg-card">
                    <div class="form-card-header">
                        <h2>Payment Details</h2>
                        <p>Choose your preferred payment method below.</p>
                    </div>
                    <div class="form-section">

                        <!-- Method tabs -->
                        <div class="pay-tabs">
                            <div class="pay-tab active" onclick="switchPay('card',this)"><i class="fas fa-credit-card"></i><span>Card</span></div>
                            <div class="pay-tab" onclick="switchPay('upi',this)"><i class="fas fa-mobile-alt"></i><span>UPI</span></div>
                            <div class="pay-tab" onclick="switchPay('netbank',this)"><i class="fas fa-university"></i><span>Net Banking</span></div>
                            <div class="pay-tab" onclick="switchPay('wallet',this)"><i class="fas fa-wallet"></i><span>Wallet</span></div>
                        </div>

                        <!-- Card -->
                        <div id="sec-card" class="pay-section show">
                            <div class="mb-3">
                                <label class="flabel">Cardholder Name</label>
                                <input type="text" class="finput" value="<?= htmlspecialchars($user['full_name']) ?>" placeholder="Name as on card"/>
                            </div>
                            <div class="mb-3">
                                <label class="flabel">Card Number</label>
                                <div class="card-num-wrap">
                                    <input type="text" class="finput" placeholder="1234  5678  9012  3456" maxlength="19" oninput="formatCard(this)"/>
                                    <span class="card-icon"><i class="fas fa-credit-card"></i></span>
                                </div>
                            </div>
                            <div class="row">
                                <div class="col-6 mb-3">
                                    <label class="flabel">Expiry Date</label>
                                    <input type="text" class="finput" placeholder="MM / YY" maxlength="7" oninput="formatExpiry(this)"/>
                                </div>
                                <div class="col-6 mb-3">
                                    <label class="flabel">CVV</label>
                                    <input type="password" class="finput" placeholder="• • •" maxlength="4"/>
                                </div>
                            </div>
                        </div>

                        <!-- UPI -->
                        <div id="sec-upi" class="pay-section">
                            <div class="mb-3">
                                <label class="flabel">UPI ID</label>
                                <input type="text" class="finput" placeholder="yourname@upi"/>
                            </div>
                            <p style="font-size:.76rem;color:var(--pg-muted)"><i class="fas fa-info-circle mr-1" style="color:var(--pg-primary)"></i>You will receive a payment request on your UPI app.</p>
                        </div>

                        <!-- Net Banking -->
                        <div id="sec-netbank" class="pay-section">
                            <div class="mb-3">
                                <label class="flabel">Select Your Bank</label>
                                <select class="finput" style="appearance:auto">
                                    <option value="">-- Choose bank --</option>
                                    <option>State Bank of India</option>
                                    <option>HDFC Bank</option>
                                    <option>ICICI Bank</option>
                                    <option>Axis Bank</option>
                                    <option>Kotak Mahindra Bank</option>
                                    <option>Punjab National Bank</option>
                                </select>
                            </div>
                        </div>

                        <!-- Wallet -->
                        <div id="sec-wallet" class="pay-section">
                            <label class="flabel mb-2">Select Wallet</label>
                            <div class="pay-tabs" style="flex-wrap:wrap">
                                <div class="pay-tab" onclick="selectWallet(this)" style="min-width:90px"><i class="fas fa-bolt"></i><span>Paytm</span></div>
                                <div class="pay-tab" onclick="selectWallet(this)" style="min-width:90px"><i class="fas fa-rupee-sign"></i><span>PhonePe</span></div>
                                <div class="pay-tab" onclick="selectWallet(this)" style="min-width:90px"><i class="fab fa-amazon-pay"></i><span>Amazon Pay</span></div>
                            </div>
                        </div>

                        <hr class="fdivider">

                        <div style="font-size:.82rem;font-weight:700;color:var(--pg-navy);margin-bottom:.6rem;">Billing Contact</div>
                        <div class="row">
                            <div class="col-md-6 mb-3">
                                <label class="flabel">Email</label>
                                <input type="email" class="finput" value="<?= htmlspecialchars($user['email']) ?>"/>
                            </div>
                            <div class="col-md-6 mb-3">
                                <label class="flabel">Phone</label>
                                <input type="tel" class="finput" value="<?= htmlspecialchars($user['phone']) ?>"/>
                            </div>
                        </div>

                        <hr class="fdivider">

                        <div class="terms-row mb-4">
                            <input type="checkbox" id="terms" checked/>
                            <label for="terms">I agree to the <a href="#">Terms of Service</a> and <a href="#">Privacy Policy</a>. I authorise PG Life to charge ₹ <?= number_format($total_due) ?> to my selected payment method.</label>
                        </div>

                        <button class="btn-pg" id="pay-btn" onclick="handlePayment(event)">
                            <span class="material-symbols-outlined" style="font-size:1.05rem">lock</span>
                            <span>Pay ₹ <?= number_format($total_due) ?> Securely</span>
                        </button>

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

    <!-- Success Overlay -->
    <div class="success-overlay" id="successOverlay">
        <div class="success-box">
            <div class="success-icon"><i class="fas fa-check"></i></div>
            <h3>Booking Confirmed! 🎉</h3>
            <p>Your booking for <strong><?= htmlspecialchars($property['property_name']) ?></strong> is confirmed.<br>A confirmation has been sent to <strong><?= htmlspecialchars($user['email']) ?></strong>.</p>
            <p style="margin-top:.5rem">Our team will contact you within 24 hours.</p>
            <button class="btn-ok" onclick="window.location.href='dashboard.php'">Go to Dashboard</button>
        </div>
    </div>

    <?php include "includes/footer.php"; ?>
    <script>
        const allSections = ['card','upi','netbank','wallet'];
        function switchPay(type, el) {
            document.querySelectorAll('.pay-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
            allSections.forEach(s => {
                const sec = document.getElementById('sec-' + s);
                if (sec) sec.className = 'pay-section' + (s === type ? ' show' : '');
            });
        }
        function formatCard(input) {
            let v = input.value.replace(/\D/g,'').substring(0,16);
            input.value = v.replace(/(.{4})/g,'$1  ').trim();
        }
        function formatExpiry(input) {
            let v = input.value.replace(/\D/g,'').substring(0,4);
            if (v.length >= 2) v = v.substring(0,2) + ' / ' + v.substring(2);
            input.value = v;
        }
        function selectWallet(el) {
            document.querySelectorAll('#sec-wallet .pay-tab').forEach(t => t.classList.remove('active'));
            el.classList.add('active');
        }
        function handlePayment(e) {
            e.preventDefault();
            if (!document.getElementById('terms').checked) {
                alert('Please agree to the Terms of Service to proceed.');
                return;
            }
            const btn = document.getElementById('pay-btn');
            btn.innerHTML = '<i class="fas fa-spinner fa-spin mr-2"></i> Processing…';
            btn.disabled = true;

            // Make API request to save booking
            fetch('api/book_property.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/x-www-form-urlencoded',
                },
                body: `property_id=<?= $property_id ?>&total_rent=<?= $total_due ?>`,
            })
            .then(response => response.json())
            .then(data => {
                if(data.status === 'success') {
                    // Update user flow status
                    setTimeout(() => {
                        document.getElementById('successOverlay').classList.add('show');
                    }, 500);
                } else {
                    alert(data.message || 'Failed to complete booking. Please try again.');
                    btn.innerHTML = 'Pay & Confirm Booking';
                    btn.disabled = false;
                }
            })
            .catch(error => {
                alert('Error processing payment. Please check your connection.');
                btn.innerHTML = 'Pay & Confirm Booking';
                btn.disabled = false;
            });
        }
    </script>
</body>
</html>
