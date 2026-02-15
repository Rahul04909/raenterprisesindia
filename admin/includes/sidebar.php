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
        
        <li class="menu-item-has-children">
            <a href="#">
                <i class="fa-solid fa-box-open"></i>
                <span>Products</span>
                <span class="submenu-icon"><i class="fa-solid fa-chevron-right"></i></span>
            </a>
            <ul class="submenu">
                <li><a href="#">All Products</a></li>
                <li><a href="#">Add New</a></li>
                <li><a href="#">Categories</a></li>
                <li><a href="#">Tags</a></li>
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
