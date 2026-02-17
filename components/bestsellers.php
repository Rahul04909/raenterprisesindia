<?php
// Ensure DB connection
require_once __DIR__ . '/../database/db_config.php';

try {
    // 1. Fetch Categories marked as "Best Seller"
    $stmtCats = $pdo->query("SELECT id FROM categories WHERE is_best_seller = 1");
    $catIds = $stmtCats->fetchAll(PDO::FETCH_COLUMN);

    if (empty($catIds)) {
        // Fallback: If no categories selected, maybe show top rated products overall?
        // Or just hide the section. Let's show top rated overall for now.
        $sql = "SELECT * FROM products WHERE status = 'published' ORDER BY created_at DESC LIMIT 8"; // Or order by views/sales if available
        $stmt = $pdo->query($sql);
    } else {
        // 2. Fetch Products from these categories
        $inQuery = implode(',', array_fill(0, count($catIds), '?'));
        $sql = "SELECT * FROM products 
                WHERE category_id IN ($inQuery) 
                AND status = 'published' 
                ORDER BY id DESC 
                LIMIT 8"; // Adjust limit as needed
        $stmt = $pdo->prepare($sql);
        $stmt->execute($catIds);
    }
    
    $bestsellers = $stmt->fetchAll();

} catch (PDOException $e) {
    // echo "Error: " . $e->getMessage();
    $bestsellers = [];
}
?>

<?php if (count($bestsellers) > 0): ?>
<div class="bestsellers-section">
    <div class="bestsellers-header">
        <h2 class="bestsellers-title">Bestsellers</h2>
    </div>
    
    <div class="bestsellers-scroll-wrapper">
        <button class="bestsellers-nav-btn prev-btn" onclick="scrollBestsellers(this, -1)"><i class="fa-solid fa-angle-left"></i></button>
        
        <div class="bestsellers-container">
            <?php foreach ($bestsellers as $product): ?>
                <a href="product-details.php?slug=<?php echo $product['slug']; ?>" class="product-card">
                    <div class="product-image">
                        <?php 
                            $img = $product['featured_image'] ? $product['featured_image'] : 'https://placehold.co/150x150?text=Product';
                        ?>
                        <img src="<?php echo htmlspecialchars($img); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                    </div>
                    
                    <div class="rating-badge">
                        4.5 <i class="fa-solid fa-star"></i>
                    </div>
                    <span class="review-count" style="font-size:11px; color:#878787;">(0 Reviews)</span>
                    
                    <h3 class="product-title" title="<?php echo htmlspecialchars($product['name']); ?>">
                        <?php echo htmlspecialchars($product['name']); ?>
                    </h3>
                    
                    <div class="price-block">
                        <?php if ($product['is_price_enabled'] && $product['sales_price']): ?>
                            <span class="current-price">₹<?php echo number_format($product['sales_price']); ?></span>
                            <?php if ($product['mrp'] > $product['sales_price']): ?>
                                <span class="original-price">₹<?php echo number_format($product['mrp']); ?></span>
                                <?php 
                                    $discount = round((($product['mrp'] - $product['sales_price']) / $product['mrp']) * 100);
                                ?>
                                <span class="discount-text"><?php echo $discount; ?>% OFF</span>
                            <?php endif; ?>
                        <?php else: ?>
                            <span class="current-price" style="color:#d32f2f; font-size:14px;">Price on Request</span>
                        <?php endif; ?>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
        
        <button class="bestsellers-nav-btn next-btn" onclick="scrollBestsellers(this, 1)"><i class="fa-solid fa-angle-right"></i></button>
    </div>
</div>

<script>
function scrollBestsellers(btn, direction) {
    const wrapper = btn.closest('.bestsellers-scroll-wrapper');
    const container = wrapper.querySelector('.bestsellers-container');
    const scrollAmount = 300; 
    
    if (direction === 1) {
        container.scrollBy({ left: scrollAmount, behavior: 'smooth' });
    } else {
        container.scrollBy({ left: -scrollAmount, behavior: 'smooth' });
    }
}
</script>
<?php endif; ?>
