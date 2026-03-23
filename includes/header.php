<div class="header sticky-top">
    <nav class="navbar navbar-expand-md navbar-light bg-light" style="background: rgba(255, 255, 255, 0.98) !important;">
        <div class="container">
            <a class="navbar-brand font-weight-bold" href="index.php" style="color: #137fec; font-size: 1.5rem; letter-spacing: -0.5px;">
                <i class="fas fa-building" style="color: #137fec; margin-right: 5px;"></i> PGLife
            </a>
            <button class="navbar-toggler" type="button" data-toggle="collapse" data-target="#my-navbar">
                <span class="navbar-toggler-icon"></span>
            </button>

            <div class="collapse navbar-collapse justify-content-end" id="my-navbar">
                <ul class="navbar-nav align-items-center">
                    <?php if (!isset($_SESSION["user_id"])) { ?>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="modal" data-target="#signup-modal">
                            <i class="fas fa-user"></i>Signup
                        </a>
                    </li>
                    <div class="nav-vl d-none d-md-block"></div>
                    <li class="nav-item">
                        <a class="nav-link" href="#" data-toggle="modal" data-target="#login-modal">
                            <i class="fas fa-sign-in-alt"></i>Login
                        </a>
                    </li>
                    <?php } else { ?>
                    <li class="nav-item">
                        <span class="nav-link" style="color: #1a1f36 !important; font-weight: 700; padding-right: 20px;">
                            Hi, <?= htmlspecialchars($_SESSION["full_name"]) ?>
                        </span>
                    </li>
                    <li class="nav-item">                        <a class="nav-link text-primary font-weight-bold mr-2" href="add_property.php">
                            <i class="fas fa-plus-circle"></i> Add Property
                        </a>
                    </li>
                    <li class="nav-item">                        <a class="nav-link" href="dashboard.php">
                            <i class="fas fa-user"></i>Dashboard
                        </a>
                    </li>
                    <div class="nav-vl d-none d-md-block"></div>
                    <li class="nav-item">
                        <a class="nav-link" href="logout.php">
                            <i class="fas fa-sign-out-alt"></i>Logout
                        </a>
                    </li>
                    <?php } ?>
                </ul>
            </div>
        </div>
    </nav>
</div>

<div id="loading"></div>
