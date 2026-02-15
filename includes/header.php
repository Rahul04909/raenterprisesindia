<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>RA Enterprises India</title>
    <!-- Google Fonts -->
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@400;500;700&display=swap" rel="stylesheet">
    <!-- FontAwesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="assets/css/style.css">
</head>
<body>

<header class="main-header">
    <nav class="navbar">
        <!-- Logo Section -->
        <div class="logo-container">
            <a href="index.php" class="brand-logo">
                <img src="assets/logo/logo.png" alt="RA Enterprises" onerror="this.src='https://placehold.co/150x50?text=RA+Enterprises'">
            </a>
            
            <div class="location-selector d-md-flex">
                <i class="fa-solid fa-location-dot"></i>
                <div class="location-text">
                    <span>Deliver to</span>
                    <strong>Select Location ></strong>
                </div>
            </div>
        </div>

        <!-- Search Bar -->
        <div class="search-container d-md-flex">
            <input type="text" class="search-input" placeholder="Search Product, Category, Brand...">
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>

        <!-- Nav Actions -->
        <div class="nav-actions">
            <a href="#" class="nav-item">
                <i class="fa-regular fa-user"></i>
                <span>Login Now</span>
            </a>
            
            <a href="#" class="nav-item">
                <i class="fa-solid fa-truck-fast"></i>
                <span>Track Order</span>
            </a>
            
            <a href="#" class="nav-item">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Cart</span>
            </a>
            
             <div class="nav-item mobile-toggle">
                <i class="fa-solid fa-ellipsis-vertical"></i>
            </div>
        </div>
    </nav>

    <!-- Mobile Search Bar (Visible on Mobile Only) -->
    <div class="mobile-search-bar">
         <div class="search-container" style="display:flex; margin:0; width:100%;">
            <input type="text" class="search-input" placeholder="Search Product, Category, Brand...">
            <button type="submit" class="search-btn">
                <i class="fa-solid fa-magnifying-glass"></i>
            </button>
        </div>
    </div>
</header>

<!-- Main Content Wrapper Starts Here -->
<main>
