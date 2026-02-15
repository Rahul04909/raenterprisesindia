<div class="admin-sidebar">
    <div class="sidebar-header">
        <a href="index.php" class="sidebar-brand">
            <i class="fa-solid fa-gauge-high"></i>
            <span>RA Admin</span>
        </a>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-box-open"></i>
                <span>Products</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Orders</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-users"></i>
                <span>Customers</span>
            </a>
        </li>
        <li>
            <a href="#">
                <i class="fa-solid fa-layer-group"></i>
                <span>Categories</span>
            </a>
        </li>
        <li style="margin-top: auto;"> <!-- Push to bottom if flex (Not standard WP but useful) -->
             <a href="#">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
            </a>
        </li>
    </ul>
</div>
