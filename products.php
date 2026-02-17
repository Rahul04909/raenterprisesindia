<?php
require_once 'database/db_config.php';

// --- filter Logic ---
$whereClauses = ["status = 'published'"];
$params = [];

// 1. Categories
$selectedCategories = isset($_GET['category']) ? (is_array($_GET['category']) ? $_GET['category'] : [$_GET['category']]) : [];
if (!empty($selectedCategories)) {
    $placeholders = implode(',', array_fill(0, count($selectedCategories), '?'));
    // If slugs are passed, we might need to join with categories table, but assuming IDs or slugs are distinct enough or we subquery
    // Should verify if we are passing slugs or IDs. Existing links pass slugs (e.g. products.php?category=slug).
    // Let's assume URL uses slugs for SEO.
    
    // Convert slugs to IDs if needed or join. Let's use subquery/IN with slugs if possible or just join.
    // Easier: WHERE category_id IN (SELECT id FROM categories WHERE slug IN (...))
    $whereClauses[] = "category_id IN (SELECT id FROM categories WHERE slug IN ($placeholders))";
    $params = array_merge($params, $selectedCategories);
}

// 2. Brands
$selectedBrands = isset($_GET['brand']) ? (is_array($_GET['brand']) ? $_GET['brand'] : [$_GET['brand']]) : [];
if (!empty($selectedBrands)) {
    $placeholders = implode(',', array_fill(0, count($selectedBrands), '?'));
    $whereClauses[] = "brand_id IN (SELECT id FROM brands WHERE slug IN ($placeholders))";
    $params = array_merge($params, $selectedBrands);
}

// 3. Brand Categories
$selectedBrandCats = isset($_GET['brand_category']) ? (is_array($_GET['brand_category']) ? $_GET['brand_category'] : [$_GET['brand_category']]) : [];
if (!empty($selectedBrandCats)) {
    $placeholders = implode(',', array_fill(0, count($selectedBrandCats), '?'));
    $whereClauses[] = "brand_category_id IN (SELECT id FROM brand_categories WHERE slug IN ($placeholders))";
    $params = array_merge($params, $selectedBrandCats);
}

// 4. Price Range
$minPrice = isset($_GET['min_price']) ? (int)$_GET['min_price'] : 0;
$maxPrice = isset($_GET['max_price']) ? (int)$_GET['max_price'] : 1000000; // High default
if ($minPrice > 0) {
    $whereClauses[] = "sales_price >= ?";
    $params[] = $minPrice;
}
if (isset($_GET['max_price'])) { // Only apply if set
    $whereClauses[] = "sales_price <= ?";
    $params[] = $maxPrice;
}

// 5. Search
$searchQuery = isset($_GET['q']) ? trim($_GET['q']) : '';
if (!empty($searchQuery)) {
    $whereClauses[] = "(name LIKE ? OR description LIKE ?)";
    $params[] = "%$searchQuery%";
    $params[] = "%$searchQuery%";
}


// Build SQL
$sqlWhere = implode(' AND ', $whereClauses);
$orderBy = "created_at DESC"; // Default
if (isset($_GET['sort'])) {
    switch ($_GET['sort']) {
        case 'price_asc': $orderBy = "sales_price ASC"; break;
        case 'price_desc': $orderBy = "sales_price DESC"; break;
        case 'name_asc': $orderBy = "name ASC"; break;
        case 'name_desc': $orderBy = "name DESC"; break;
    }
}

// Pagination
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$limit = 12;
$offset = ($page - 1) * $limit;

// Count Total
$stmtCount = $pdo->prepare("SELECT COUNT(*) FROM products WHERE $sqlWhere");
$stmtCount->execute($params);
$totalProducts = $stmtCount->fetchColumn();
$totalPages = ceil($totalProducts / $limit);

// Fetch Products
$stmtProducts = $pdo->prepare("SELECT * FROM products WHERE $sqlWhere ORDER BY $orderBy LIMIT $limit OFFSET $offset");
$stmtProducts->execute($params);
$products = $stmtProducts->fetchAll();

// --- Sidebar Data Fetching ---
// Get all categories
$allCategories = $pdo->query("SELECT * FROM categories ORDER BY name ASC")->fetchAll();
// Get all brands
$allBrands = $pdo->query("SELECT * FROM brands ORDER BY name ASC")->fetchAll();
// Get all brand categories
$allBrandCats = $pdo->query("SELECT * FROM brand_categories ORDER BY name ASC")->fetchAll();

// Get Price Range limits
$priceRange = $pdo->query("SELECT MIN(sales_price) as min_p, MAX(sales_price) as max_p FROM products WHERE status='published'")->fetch();
$globalMinPrice = $priceRange['min_p'] ?? 0;
$globalMaxPrice = $priceRange['max_p'] ?? 10000;

?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Shop - RA Enterprises</title>
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- CSS Includes -->
    <link rel="stylesheet" href="assets/css/style.css">
    <link rel="stylesheet" href="assets/css/header.css">
    <link rel="stylesheet" href="assets/css/footer.css">
    <link rel="stylesheet" href="assets/css/products.css">
</head>
<body>

<?php include 'includes/header.php'; ?>

<!-- Breadcrumbs (Optional but good) -->
<div class="breadcrumbs-container">
    <div class="container">
        <a href="index.php">Home</a> <i class="fa-solid fa-angle-right"></i> <span>Shop</span>
    </div>
</div>

<div class="shop-container">
    <!-- Sidebar -->
    <aside class="shop-sidebar">
        <div class="filter-header">
            <h3>Filters</h3>
            <a href="products.php" class="clear-filters">Clear All</a>
        </div>
        
        <form method="GET" action="products.php" id="filterForm">
            <!-- Retain current search/sort in hidden fields if needed, or JS handles it -->
            <?php if(!empty($searchQuery)): ?><input type="hidden" name="q" value="<?php echo htmlspecialchars($searchQuery); ?>"><?php endif; ?>
            <?php if(isset($_GET['sort'])): ?><input type="hidden" name="sort" value="<?php echo htmlspecialchars($_GET['sort']); ?>"><?php endif; ?>

            <!-- Categories -->
            <div class="filter-group">
                <h4>Categories</h4>
                <div class="filter-options">
                    <?php foreach($allCategories as $cat): ?>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="category[]" value="<?php echo $cat['slug']; ?>" 
                                <?php echo in_array($cat['slug'], $selectedCategories) ? 'checked' : ''; ?> 
                                onchange="this.form.submit()">
                            <span><?php echo htmlspecialchars($cat['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Price Range -->
            <div class="filter-group">
                <h4>Price</h4>
                <div class="price-inputs">
                    <input type="number" name="min_price" placeholder="Min" value="<?php echo $minPrice > 0 ? $minPrice : ''; ?>" min="0">
                    <span>-</span>
                    <input type="number" name="max_price" placeholder="Max" value="<?php echo isset($_GET['max_price']) ? $maxPrice : ''; ?>">
                    <button type="submit" class="price-go-btn"><i class="fa-solid fa-play"></i></button>
                </div>
            </div>

            <!-- Brands -->
            <div class="filter-group">
                <h4>Brands</h4>
                <div class="filter-options">
                    <?php foreach($allBrands as $brand): ?>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="brand[]" value="<?php echo $brand['slug']; ?>" 
                                <?php echo in_array($brand['slug'], $selectedBrands) ? 'checked' : ''; ?> 
                                onchange="this.form.submit()">
                            <span><?php echo htmlspecialchars($brand['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

             <!-- Brand Categories -->
             <div class="filter-group">
                <h4>Brand Categories</h4>
                <div class="filter-options">
                    <?php foreach($allBrandCats as $bc): ?>
                        <label class="filter-checkbox">
                            <input type="checkbox" name="brand_category[]" value="<?php echo $bc['slug']; ?>" 
                                <?php echo in_array($bc['slug'], $selectedBrandCats) ? 'checked' : ''; ?> 
                                onchange="this.form.submit()">
                            <span><?php echo htmlspecialchars($bc['name']); ?></span>
                        </label>
                    <?php endforeach; ?>
                </div>
            </div>

        </form>
    </aside>

    <!-- Main Content -->
    <main class="shop-content">
        <!-- Top Toolbar -->
        <div class="shop-toolbar">
            <div class="results-count">
                Showing <?php echo $offset + 1; ?> – <?php echo min($offset + $limit, $totalProducts); ?> of <?php echo $totalProducts; ?> results
            </div>
            <div class="sort-wrapper">
                <label>Sort By:</label>
                <select onchange="location = this.value;">
                    <option value="<?php echo getSortUrl('newest'); ?>" <?php echo (!isset($_GET['sort']) || $_GET['sort'] == 'newest') ? 'selected' : ''; ?>>Newest First</option>
                    <option value="<?php echo getSortUrl('price_asc'); ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_asc') ? 'selected' : ''; ?>>Price: Low to High</option>
                    <option value="<?php echo getSortUrl('price_desc'); ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'price_desc') ? 'selected' : ''; ?>>Price: High to Low</option>
                    <option value="<?php echo getSortUrl('name_asc'); ?>" <?php echo (isset($_GET['sort']) && $_GET['sort'] == 'name_asc') ? 'selected' : ''; ?>>Name: A-Z</option>
                </select>
            </div>
        </div>
        
        <!-- Mobile Filter Toggle -->
        <div class="mobile-filter-bar">
            <button onclick="document.querySelector('.shop-sidebar').classList.add('active')"><i class="fa-solid fa-filter"></i> Filters</button>
            <div class="mobile-filter-overlay" onclick="document.querySelector('.shop-sidebar').classList.remove('active')"></div>
        </div>

        <!-- Product Grid -->
        <?php if (count($products) > 0): ?>
        <div class="products-grid">
            <?php foreach ($products as $product): ?>
                <a href="product-details.php?slug=<?php echo $product['slug']; ?>" class="product-card">
                    <div class="product-image">
                        <?php 
                            $img = $product['featured_image'] ? $product['featured_image'] : 'https://placehold.co/300x300?text=No+Image';
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    <div class="product-info">
                        <div class="rating-badge-sm">4.5 <i class="fa-solid fa-star"></i></div>
                        <h3 class="product-title" title="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>
                        <div class="price-box">
                             <?php if ($product['is_price_enabled'] && $product['sales_price']): ?>
                                <span class="current-price">₹<?php echo number_format($product['sales_price']); ?></span>
                                <?php if ($product['mrp'] > $product['sales_price']): ?>
                                    <span class="original-price">₹<?php echo number_format($product['mrp']); ?></span>
                                    <span class="discount"><?php echo round((($product['mrp'] - $product['sales_price']) / $product['mrp']) * 100); ?>% OFF</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="price-on-request">Price on Request</span>
                            <?php endif; ?>
                        </div>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <!-- Pagination -->
        <?php if ($totalPages > 1): ?>
        <div class="pagination">
            <?php if ($page > 1): ?>
                <a href="<?php echo getPageUrl($page - 1); ?>" class="page-link prev"><i class="fa-solid fa-angle-left"></i> Previous</a>
            <?php endif; ?>
            
            <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                <?php if ($i == $page): ?>
                    <span class="page-link active"><?php echo $i; ?></span>
                <?php else: ?>
                    <a href="<?php echo getPageUrl($i); ?>" class="page-link"><?php echo $i; ?></a>
                <?php endif; ?>
            <?php endfor; ?>

            <?php if ($page < $totalPages): ?>
                <a href="<?php echo getPageUrl($page + 1); ?>" class="page-link next">Next <i class="fa-solid fa-angle-right"></i></a>
            <?php endif; ?>
        </div>
        <?php endif; ?>

        <?php else: ?>
            <div class="no-products-found">
                <img src="https://cdni.iconscout.com/illustration/premium/thumb/sorry-item-not-found-3328225-2809510.png" alt="No products" style="max-width:200px;">
                <h3>No products found</h3>
                <p>Try clearing some filters or searching for something else.</p>
                <a href="products.php" class="btn-primary">Clear Filters</a>
            </div>
        <?php endif; ?>

    </main>
</div>

<?php include 'includes/footer.php'; ?>

<!-- Mobile Filter Close Button (Inside sidebar usually, or handled by overlay) -->
<script>
    // Simple script if needed for mobile sidebar
</script>

</body>
</html>

<?php
// Helper to generate sort URL
function getSortUrl($sortVal) {
    $params = $_GET;
    $params['sort'] = $sortVal;
    return '?' . http_build_query($params);
}

// Helper to generate page URL
function getPageUrl($pageNum) {
    $params = $_GET;
    $params['page'] = $pageNum;
    return '?' . http_build_query($params);
}
?>
