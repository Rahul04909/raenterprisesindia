<div class="admin-sidebar">
    <div class="sidebar-header">
        <?php $adminBase = isset($adminBase) ? $adminBase : '.'; ?>
        <a href="<?php echo $adminBase; ?>/index.php" class="sidebar-brand">
            <i class="fa-solid fa-gauge-high"></i>
            <span>RA Admin</span>
        </a>
    </div>
    
    <ul class="sidebar-menu">
        <li>
            <a href="<?php echo $adminBase; ?>/index.php" class="<?php echo basename($_SERVER['PHP_SELF']) == 'index.php' && strpos($_SERVER['REQUEST_URI'], 'product-categories') === false ? 'active' : ''; ?>">
                <i class="fa-solid fa-gauge"></i>
                <span>Dashboard</span>
            </a>
        </li>
        
        <li class="menu-item-has-children <?php echo strpos($_SERVER['REQUEST_URI'], 'product-categories') !== false ? 'open' : ''; ?>">
            <a href="#">
                <i class="fa-solid fa-box-open"></i>
                <span>Products</span>
                <span class="submenu-icon"><i class="fa-solid fa-chevron-right"></i></span>
            </a>
            <ul class="submenu">
                <li><a href="<?php echo $adminBase; ?>/products/index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/products/index.php') !== false ? 'active' : ''; ?>">All Products</a></li>
                <li><a href="<?php echo $adminBase; ?>/products/add.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/products/add.php') !== false ? 'active' : ''; ?>">Add New</a></li>
                <li><a href="<?php echo $adminBase; ?>/product-categories/index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], 'product-categories') !== false ? 'active' : ''; ?>">Categories</a></li>
                <li><a href="<?php echo $adminBase; ?>/brands/index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/brands/') !== false ? 'active' : ''; ?>">Brands</a></li>
                <li><a href="<?php echo $adminBase; ?>/brand-categories/index.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/brand-categories/') !== false ? 'active' : ''; ?>">Brand Categories</a></li>
                <li><a href="<?php echo $adminBase; ?>/best-sellers/best-seller-products.php" class="<?php echo strpos($_SERVER['REQUEST_URI'], '/best-sellers/') !== false ? 'active' : ''; ?>">Best Sellers</a></li>
            </ul>
        </li>

        <li class="menu-item-has-children">
            <a href="#">
                <i class="fa-solid fa-cart-shopping"></i>
                <span>Orders</span>
                <span class="submenu-icon"><i class="fa-solid fa-chevron-right"></i></span>
            </a>
             <ul class="submenu">
                <li><a href="#">All Orders</a></li>
                <li><a href="#">Pending</a></li>
                <li><a href="#">Completed</a></li>
            </ul>
        </li>

        <li>
            <a href="#">
                <i class="fa-solid fa-users"></i>
                <span>Customers</span>
            </a>
        </li>
        
        <li class="menu-item-has-children" style="margin-top: auto;">
             <a href="#">
                <i class="fa-solid fa-gear"></i>
                <span>Settings</span>
                <span class="submenu-icon"><i class="fa-solid fa-chevron-right"></i></span>
            </a>
            <ul class="submenu">
                <li><a href="#">General</a></li>
                <li><a href="#">Security</a></li>
            </ul>
        </li>
    </ul>
</div>
