<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Dashboard &lsaquo; RA Enterprises Admin</title> <!-- WP Style Title -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
</head>
<body>
    
<div class="admin-wrapper">
    <!-- Sidebar -->
    <?php include 'includes/sidebar.php'; ?>

    <!-- Main Content -->
    <div class="admin-content">
        <!-- Top Header -->
        <?php include 'includes/header.php'; ?>

        <!-- Dashboard Content -->
        <main class="admin-main">
            <h1 class="page-title">Dashboard</h1>

            <!-- Welcome Panel -->
            <div class="welcome-panel">
                <h2>Welcome to RA Enterprises Admin</h2>
                <p>We’ve assembled some links to get you started:</p>
                <!-- Add more quick links or text like WP -->
            </div>

            <!-- Widgets -->
            <div class="dashboard-widgets">
                <!-- Widget 1 -->
                <div class="widget-card">
                    <h3 class="widget-title">At a Glance</h3>
                    <div class="widget-content">
                        <div class="widget-stats">
                            <div class="widget-value">124</div>
                            <div class="widget-label">Products</div>
                        </div>
                        <div class="widget-icon">
                            <i class="fa-solid fa-box"></i>
                        </div>
                    </div>
                </div>

                <!-- Widget 2 -->
                <div class="widget-card">
                    <h3 class="widget-title">Orders</h3>
                    <div class="widget-content">
                        <div class="widget-stats">
                            <div class="widget-value">8</div>
                            <div class="widget-label">Pending Orders</div>
                        </div>
                        <div class="widget-icon">
                            <i class="fa-solid fa-cart-shopping"></i>
                        </div>
                    </div>
                </div>
                
                 <!-- Widget 3 -->
                 <div class="widget-card">
                    <h3 class="widget-title">Revenue</h3>
                    <div class="widget-content">
                        <div class="widget-stats">
                            <div class="widget-value">₹45k</div>
                            <div class="widget-label">This Month</div>
                        </div>
                        <div class="widget-icon">
                            <i class="fa-solid fa-indian-rupee-sign"></i>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

</body>
</html>
