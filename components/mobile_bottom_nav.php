<!-- Mobile Bottom Navigation -->
<div class="mobile-bottom-nav">
    <a href="login.php" class="nav-item">
        <i class="fa-regular fa-circle-user"></i>
        <span>Login</span>
    </a>
    
    <a href="products.php?filter=24hours" class="nav-item">
        <i class="fa-solid fa-clock-rotate-left"></i>
        <span>24hrs Delivery</span>
    </a>
    
    <div class="nav-item search-trigger">
        <div class="search-fab">
            <i class="fa-solid fa-magnifying-glass"></i>
        </div>
    </div>
    
    <a href="orders.php" class="nav-item">
        <i class="fa-solid fa-clipboard-list"></i>
        <span>Orders</span>
    </a>
    
    <a href="cart.php" class="nav-item">
        <i class="fa-solid fa-cart-shopping"></i>
        <span>My Cart</span>
        <!-- Optional Badge -->
        <!-- <span class="cart-badge">2</span> -->
    </a>
</div>

<!-- Search Overlay (Hidden by default) -->
<div id="mobile-search-overlay" class="mobile-search-overlay">
    <div class="search-bar-container">
        <input type="text" placeholder="Search Product, Brand...">
        <button class="close-search"><i class="fa-solid fa-xmark"></i></button>
    </div>
</div>

<script>
document.querySelector('.search-trigger').addEventListener('click', function() {
    // Check if we want to toggle an overlay or focus the header search
    // For now, let's just focus the header search if visible or toggle overlay
    // document.querySelector('.mobile-search-bar input').focus();
    // Or simple overlay logic:
    const overlay = document.getElementById('mobile-search-overlay');
    if(overlay) {
        overlay.style.display = 'flex';
        overlay.querySelector('input').focus();
    } else {
        // Fallback to scrolling to header search
        window.scrollTo({ top: 0, behavior: 'smooth' });
        setTimeout(() => document.querySelector('.search-input').focus(), 500);
    }
});

const closeSearch = document.querySelector('.close-search');
if(closeSearch) {
    closeSearch.addEventListener('click', function() {
        document.getElementById('mobile-search-overlay').style.display = 'none';
    });
}
</script>
