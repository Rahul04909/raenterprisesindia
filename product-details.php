<?php
include 'includes/header.php';
require_once 'database/db_config.php';

// Get Slug from URL
$slug = isset($_GET['slug']) ? $_GET['slug'] : '';

if (empty($slug)) {
    echo "<div class='container' style='padding:50px;text-align:center;'><h2>Product Not Found</h2><a href='index.php'>Go Home</a></div>";
    include 'includes/footer.php';
    exit;
}

try {
    // Fetch Product Data
    $stmt = $pdo->prepare("
        SELECT p.*, c.name as category_name, c.slug as category_slug, b.name as brand_name, b.slug as brand_slug 
        FROM products p
        LEFT JOIN categories c ON p.category_id = c.id
        LEFT JOIN brands b ON p.brand_id = b.id
        WHERE p.slug = ? AND p.status = 'published'
    ");
    $stmt->execute([$slug]);
    $product = $stmt->fetch();

    if (!$product) {
        echo "<div class='container' style='padding:50px;text-align:center;'><h2>Product Not Found</h2><a href='index.php'>Go Home</a></div>";
        include 'includes/footer.php';
        exit;
    }

    // Fetch Gallery Images
    $stmtImg = $pdo->prepare("SELECT image_path FROM product_images WHERE product_id = ?");
    $stmtImg->execute([$product['id']]);
    $gallery = $stmtImg->fetchAll(PDO::FETCH_COLUMN);

    // Add Featured Image to Gallery if not present
    if ($product['featured_image']) {
        array_unshift($gallery, $product['featured_image']);
    }

    // Determine Logic
    $hasPrice = $product['is_price_enabled'];
    $discount = 0;
    if ($hasPrice && $product['mrp'] > 0 && $product['sales_price'] > 0) {
        $discount = round((($product['mrp'] - $product['sales_price']) / $product['mrp']) * 100);
    }

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>

<link rel="stylesheet" href="assets/css/product-details.css">

<div class="product-details-container">
    <!-- Breadcrumbs -->
    <div class="breadcrumbs">
        <a href="index.php">Home</a>
        <?php if($product['category_name']): ?>
             &rsaquo; <a href="products.php?category=<?php echo $product['category_slug']; ?>"><?php echo htmlspecialchars($product['category_name']); ?></a>
        <?php endif; ?>
        <?php if($product['brand_name']): ?>
             &rsaquo; <a href="products.php?brand=<?php echo $product['brand_slug']; ?>"><?php echo htmlspecialchars($product['brand_name']); ?></a>
        <?php endif; ?>
         &rsaquo; <span><?php echo htmlspecialchars($product['name']); ?></span>
    </div>

    <div class="product-main-wrapper">
        <!-- Left Column: Images -->
        <div class="product-images-col">
            <div class="image-gallery-container">
                <div class="main-image-frame">
                    <?php 
                        $mainImg = !empty($gallery) ? $gallery[0] : 'https://placehold.co/450x450?text=No+Image';
                        // Adjust absolute path logic for display
                        // If path doesn't start with http, and is not absolute, assume relative to root
                    ?>
                    <img id="main-product-image" src="<?php echo htmlspecialchars($mainImg); ?>" alt="<?php echo htmlspecialchars($product['name']); ?>">
                </div>
                
                <?php if (count($gallery) > 1): ?>
                <div class="thumbnail-strip">
                    <?php foreach ($gallery as $index => $img): ?>
                        <div class="thumbnail-item <?php echo $index === 0 ? 'active' : ''; ?>" onclick="changeImage(this, '<?php echo htmlspecialchars($img); ?>')">
                            <img src="<?php echo htmlspecialchars($img); ?>" alt="Thumbnail">
                        </div>
                    <?php endforeach; ?>
                </div>
                <?php endif; ?>
            </div>

            <div class="product-actions-desktop">
                <?php if ($hasPrice): ?>
                    <button class="action-btn btn-cart"><i class="fa-solid fa-cart-shopping"></i> Add to Cart</button>
                    <button class="action-btn btn-buy"><i class="fa-solid fa-bolt"></i> Buy Now</button>
                <?php else: ?>
                    <a href="https://wa.me/919999049135?text=I am interested in <?php echo urlencode($product['name']); ?>" target="_blank" class="action-btn btn-buy" style="background:#25D366;"><i class="fa-brands fa-whatsapp"></i> Enquire Now</a>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Info -->
        <div class="product-info-col">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div style="margin-bottom:10px;">
                <div class="product-rating-box">
                    4.5 <i class="fa-solid fa-star" style="font-size:10px;"></i>
                </div>
                <span class="product-reviews-count">12 Ratings & 2 Reviews</span>
            </div>

            <?php if ($hasPrice): ?>
                <div class="product-price-box">
                    <span class="price-main">₹<?php echo number_format($product['sales_price']); ?></span>
                    <?php if ($discount > 0): ?>
                        <span class="price-mrp">₹<?php echo number_format($product['mrp']); ?></span>
                        <span class="price-discount"><?php echo $discount; ?>% off</span>
                    <?php endif; ?>
                </div>
                
                <?php if($product['stock'] > 0): ?>
                    <div class="stock-status in-stock">In Stock</div>
                <?php else: ?>
                    <div class="stock-status out-stock">Out of Stock</div>
                <?php endif; ?>
            <?php else: ?>
                <div class="product-price-box">
                    <span class="price-main" style="color:#d32f2f; font-size: 24px;">Price on Request</span>
                </div>
            <?php endif; ?>
            
            <table class="info-table">
                <?php if($product['brand_name']): ?>
                <tr>
                    <td class="label-col">Brand</td>
                    <td class="value-col"><?php echo htmlspecialchars($product['brand_name']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if($product['model_number']): ?>
                <tr>
                    <td class="label-col">Model Name/No.</td>
                    <td class="value-col"><?php echo htmlspecialchars($product['model_number']); ?></td>
                </tr>
                <?php endif; ?>
                <?php if($product['sku']): ?>
                <tr>
                    <td class="label-col">SKU</td>
                    <td class="value-col"><?php echo htmlspecialchars($product['sku']); ?></td>
                </tr>
                <?php endif; ?>
                <tr>
                    <td class="label-col">Delivery</td>
                    <td class="value-col">Usually dispatched in 24 hours</td>
                </tr>
            </table>

            <!-- Descriptions -->
            <?php if($product['description']): ?>
            <div class="details-section">
                <div class="section-header">Product Description</div>
                <div class="section-content">
                    <?php echo $product['description']; // HTML allowed from summernote ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Specifications -->
            <?php if($product['specifications']): ?>
            <div class="details-section">
                <div class="section-header">Specifications</div>
                <div class="section-content">
                    <?php echo $product['specifications']; // HTML Table from summernote ?>
                </div>
            </div>
            <?php endif; ?>
            
             <!-- PDF Brochure -->
            <?php if($product['brochure_path']): ?>
            <div class="details-section">
                <div class="section-header">Downloads</div>
                <div class="section-content">
                     <a href="<?php echo htmlspecialchars($product['brochure_path']); ?>" target="_blank" style="text-decoration:none; color:#2874f0; font-weight:600;">
                        <i class="fa-solid fa-file-pdf" style="color:#d32f2f; margin-right:5px;"></i> Download Product Brochure
                     </a>
                </div>
            </div>
            <?php endif; ?>

        </div>
    </div>
    
    <!-- Related Products Placeholder -->
    <!-- Logic to be added later or now if part of scope. For now, we have "Category Wise Products" which serves similar purpose on homepage. Here we could reuse that logic or specific related products. -->

</div>

<!-- Mobile Sticky Actions -->
<div class="mobile-sticky-actions">
    <?php if ($hasPrice): ?>
        <button class="action-btn btn-cart" style="flex:1;"><i class="fa-solid fa-cart-shopping"></i> Add to Cart</button>
        <button class="action-btn btn-buy" style="flex:1;"><i class="fa-solid fa-bolt"></i> Buy Now</button>
    <?php else: ?>
        <a href="https://wa.me/919999049135?text=I am interested in <?php echo urlencode($product['name']); ?>" class="action-btn btn-buy" style="flex:1; background:#25D366;"><i class="fa-brands fa-whatsapp"></i> Enquire Now</a>
    <?php endif; ?>
</div>

<script>
function changeImage(el, src) {
    document.getElementById('main-product-image').src = src;
    
    // Update active class
    document.querySelectorAll('.thumbnail-item').forEach(item => item.classList.remove('active'));
    el.classList.add('active');
}
</script>

<link rel="stylesheet" href="assets/css/footer.css">
<?php include 'includes/footer.php'; ?>
