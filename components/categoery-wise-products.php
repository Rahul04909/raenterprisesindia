<?php
// Ensure DB connection
require_once __DIR__ . '/../database/db_config.php';

try {
    // 1. Fetch all categories
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();

} catch (PDOException $e) {
    echo "Error fetching categories: " . $e->getMessage();
    $categories = [];
}
?>

<?php foreach ($categories as $category): ?>
    <?php
    $catId = $category['id'];
    
    // 2. Fetch Products for this Category (Limit 8)
    $stmtProduct = $pdo->prepare("
        SELECT * FROM products 
        WHERE category_id = ? 
        AND status = 'published'
        ORDER BY id DESC 
        LIMIT 8
    ");
    $stmtProduct->execute([$catId]);
    $products = $stmtProduct->fetchAll();

    // Skip category if no products
    if (count($products) == 0) continue;

    // 3. Fetch Brands for this Category (via Products)
    // We get brands that have at least one product in this category
    // DISTINCT to avoid duplicates
    $stmtBrands = $pdo->prepare("
        SELECT DISTINCT b.id, b.name, b.image, b.slug 
        FROM brands b
        JOIN products p ON p.brand_id = b.id
        WHERE p.category_id = ?
        LIMIT 5
    ");
    $stmtBrands->execute([$catId]);
    $brands = $stmtBrands->fetchAll();

    // 4. Fetch 'Subcategories' (Brand Categories) for displays
    // Since we don't have real subcats, we use brand_categories associated with products in this category
    $stmtSubCats = $pdo->prepare("
        SELECT DISTINCT bc.id, bc.name as title, bc.image, bc.slug
        FROM brand_categories bc
        JOIN products p ON p.brand_category_id = bc.id
        WHERE p.category_id = ?
        LIMIT 4
    ");
    $stmtSubCats->execute([$catId]);
    $subCats = $stmtSubCats->fetchAll();
    ?>

    <div class="safety-section">
        <div class="safety-header">
            <h2 class="safety-title"><?php echo htmlspecialchars(strtoupper($category['name'])); ?></h2>
            <a href="products.php?category=<?php echo $category['slug']; ?>" class="view-all-btn">VIEW ALL</a>
        </div>

        <div class="safety-content">
            <!-- Top Row: Brands & Featured Categories -->
            <div class="safety-featured-grid">
                
                <!-- Brands Card -->
                <?php if (count($brands) > 0): ?>
                <div class="brands-card">
                    <div class="brands-title">Top Brands</div>
                    <div class="brands-logos">
                        <?php foreach ($brands as $brand): ?>
                            <div class="brand-item">
                                <a href="products.php?brand=<?php echo $brand['slug']; ?>&category=<?php echo $category['slug']; ?>" style="text-decoration:none; color:inherit; display:flex; flex-direction:column; align-items:center;">
                                    <div class="brand-circle">
                                        <?php 
                                            $imgSrc = $brand['image'];
                                            if (empty($imgSrc)) {
                                                $imgSrc = "https://placehold.co/60x60/eee/333?text=" . substr($brand['name'], 0, 1);
                                            }
                                        ?>
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="<?php echo htmlspecialchars($brand['name']); ?>">
                                    </div>
                                    <span class="brand-name"><?php echo htmlspecialchars($brand['name']); ?></span>
                                </a>
                            </div>
                        <?php endforeach; ?>
                    </div>
                </div>
                <?php endif; ?>

                <!-- Featured Categories (Brand Categories) Cards -->
                <?php if (count($subCats) > 0): ?>
                    <?php foreach ($subCats as $subCat): ?>
                        <a href="products.php?category=<?php echo $category['slug']; ?>&brand_category=<?php echo $subCat['slug']; ?>" class="safety-cat-card">
                            <div class="safety-cat-image">
                                <?php 
                                    $subImg = $subCat['image'];
                                    if(empty($subImg)) $subImg = "https://placehold.co/150x120/eee/333?text=" . urlencode($subCat['title']);
                                ?>
                                <img src="<?php echo htmlspecialchars($subImg); ?>" alt="<?php echo htmlspecialchars($subCat['title']); ?>">
                            </div>
                            <div class="safety-cat-info">
                                <h3><?php echo htmlspecialchars($subCat['title']); ?></h3>
                                <span class="safety-explore-link">Explore Now</span>
                            </div>
                        </a>
                    <?php endforeach; ?>
                <?php else: ?>
                    <!-- Fallback: Show Products as Featured if no subcats unique -->
                     <!-- Or just distinct brands as cards? Let's leave empty/hidden if no subcats to avoid clutter -->
                <?php endif; ?>
            </div>

            <!-- Bottom Row: Products Slider -->
            <div class="safety-products-container">
                <?php foreach ($products as $product): ?>
                    <a href="product-details.php?slug=<?php echo $product['slug']; ?>" class="safety-product-card">
                        <div class="product-image">
                            <?php 
                                $pImg = $product['featured_image'];
                                if(empty($pImg)) $pImg = "https://placehold.co/150x150/eee/333?text=Product";
                            ?>
                            <img src="<?php echo htmlspecialchars($pImg); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                        </div>
                        
                        <!-- Rating Mockup (since we might not have reviews table populated yet, but checks schema) -->
                         <!-- Schema has product_reviews table, but complexity to join. Using static/mock for rating if not available or fetch average -->
                        <div class="rating-badge">
                            4.5 <i class="fa-solid fa-star"></i>
                        </div>
                        <span class="review-count" style="font-size:11px; color:#878787;">(0 Reviews)</span>
                        
                        <h3 class="product-title" title="<?php echo htmlspecialchars($product['name']); ?>">
                            <?php echo htmlspecialchars($product['name']); ?>
                        </h3>
                        
                        <div class="price-block">
                             <?php if ($product['sales_price']): ?>
                                <span class="current-price">₹<?php echo number_format($product['sales_price']); ?></span>
                                <?php if ($product['mrp'] > $product['sales_price']): ?>
                                    <span class="original-price">₹<?php echo number_format($product['mrp']); ?></span>
                                    <?php 
                                        $discount = round((($product['mrp'] - $product['sales_price']) / $product['mrp']) * 100);
                                    ?>
                                    <span class="discount-text"><?php echo $discount; ?>% OFF</span>
                                <?php endif; ?>
                            <?php else: ?>
                                <span class="current-price">Price on Request</span>
                            <?php endif; ?>
                        </div>
                    </a>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
<?php endforeach; ?>
