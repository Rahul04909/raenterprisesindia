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

    // Handle Review Submission
    $review_msg = '';
    if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['submit_review'])) {
        $r_name = trim($_POST['reviewer_name']);
        $r_email = trim($_POST['reviewer_email']);
        $r_rating = (int)$_POST['rating'];
        $r_text = trim($_POST['review_text']);
        
        if ($r_rating < 1 || $r_rating > 5) {
            $review_msg = "<div style='color:red; margin-bottom:10px;'>Please select a valid rating (1-5 stars).</div>";
        } else {
            $stmtInsert = $pdo->prepare("INSERT INTO product_reviews (product_id, user_name, user_email, rating, review, status) VALUES (?, ?, ?, ?, ?, 'pending')");
            if ($stmtInsert->execute([$product['id'], $r_name, $r_email, $r_rating, $r_text])) {
                $review_msg = "<div style='color:green; margin-bottom:10px;'>Thank you! Your review has been submitted for approval.</div>";
            } else {
                $review_msg = "<div style='color:red; margin-bottom:10px;'>Error submitting review. Please try again.</div>";
            }
        }
    }

    // Fetch Reviews (Approved Only)
    $stmtReviews = $pdo->prepare("SELECT * FROM product_reviews WHERE product_id = ? AND status = 'approved' ORDER BY created_at DESC");
    $stmtReviews->execute([$product['id']]);
    $reviews = $stmtReviews->fetchAll();
    
    $avgRating = 0;
    $totalReviews = count($reviews);
    if ($totalReviews > 0) {
        $sum = 0;
        foreach ($reviews as $r) $sum += $r['rating'];
        $avgRating = round($sum / $totalReviews, 1);
    }

    // Fetch Related Products (Same Category, exclude current)
    $relatedProducts = [];
    if ($product['category_id']) {
        $stmtRelated = $pdo->prepare("SELECT * FROM products WHERE category_id = ? AND id != ? AND status = 'published' LIMIT 4");
        $stmtRelated->execute([$product['category_id'], $product['id']]);
        $relatedProducts = $stmtRelated->fetchAll();
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
                    <div style="display:flex; gap:10px; width:100%;">
                        <a href="https://wa.me/919999049135?text=I am interested in <?php echo urlencode($product['name']); ?>" target="_blank" class="action-btn btn-buy" style="background:#25D366; flex:1;"><i class="fa-brands fa-whatsapp"></i> Enquire Now</a>
                        <a href="mailto:support@raenterprises.com?subject=Quote Request for <?php echo urlencode($product['name']); ?>" class="action-btn btn-cart" style="background:#2874f0; flex:1;"><i class="fa-solid fa-envelope"></i> Get a Quote</a>
                    </div>
                <?php endif; ?>
            </div>
        </div>

        <!-- Right Column: Info -->
        <div class="product-info-col">
            <h1 class="product-title"><?php echo htmlspecialchars($product['name']); ?></h1>
            
            <div style="margin-bottom:10px;">
                <div class="product-rating-box">
                    <?php echo $avgRating > 0 ? $avgRating : 'N/A'; ?> <i class="fa-solid fa-star" style="font-size:10px;"></i>
                </div>
                <span class="product-reviews-count"><?php echo $totalReviews; ?> Ratings & Reviews</span>
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
                    <?php echo html_entity_decode($product['description']); // Decode HTML content ?>
                </div>
            </div>
            <?php endif; ?>

            <!-- Specifications -->
            <?php if($product['specifications']): ?>
            <div class="details-section">
                <div class="section-header">Specifications</div>
                <div class="section-content">
                    <?php echo html_entity_decode($product['specifications']); // Decode HTML Table ?>
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
    
    <!-- Reviews Section -->
    <div class="details-section" id="reviews-section">
        <div class="section-header">Ratings & Reviews</div>
        <div class="section-content">
            <?php if (!empty($review_msg)) echo $review_msg; ?>
            
            <div class="reviews-container" style="display:flex; flex-wrap:wrap; gap:30px;">
                <!-- Summary -->
                <div class="rating-summary" style="flex:1; min-width:250px;">
                    <div style="font-size:48px; font-weight:bold; display:flex; align-items:center;">
                        <?php echo $avgRating; ?> <i class="fa-solid fa-star" style="font-size:24px; color:#388e3c; margin-left:10px;"></i>
                    </div>
                    <div style="color:#878787; margin-bottom:20px;"><?php echo $totalReviews; ?> Verified Ratings</div>
                    
                    <button class="action-btn" style="background:#fff; color:#2874f0; border:1px solid #2874f0; box-shadow:none; width:100%;" onclick="toggleReviewForm()">Rate Product</button>
                    
                    <!-- Review Form -->
                    <div id="review-form-container" style="display:none; margin-top:20px; border-top:1px solid #eee; padding-top:20px;">
                        <form method="POST" action="">
                            <div style="margin-bottom:10px;">
                                <label style="display:block; font-size:13px; margin-bottom:5px;">Your Name</label>
                                <input type="text" name="reviewer_name" required style="width:100%; padding:8px; border:1px solid #ddd;">
                            </div>
                            <div style="margin-bottom:10px;">
                                <label style="display:block; font-size:13px; margin-bottom:5px;">Email (Optional)</label>
                                <input type="email" name="reviewer_email" style="width:100%; padding:8px; border:1px solid #ddd;">
                            </div>
                            <div style="margin-bottom:10px;">
                                <label style="display:block; font-size:13px; margin-bottom:5px;">Rating</label>
                                <select name="rating" required style="width:100%; padding:8px; border:1px solid #ddd;">
                                    <option value="5">5 Stars - Excellent</option>
                                    <option value="4">4 Stars - Very Good</option>
                                    <option value="3">3 Stars - Good</option>
                                    <option value="2">2 Stars - Fair</option>
                                    <option value="1">1 Star - Poor</option>
                                </select>
                            </div>
                            <div style="margin-bottom:10px;">
                                <label style="display:block; font-size:13px; margin-bottom:5px;">Review</label>
                                <textarea name="review_text" rows="3" required style="width:100%; padding:8px; border:1px solid #ddd;"></textarea>
                            </div>
                            <button type="submit" name="submit_review" class="action-btn" style="background:#fb641b; font-size:14px; padding:10px;">Submit Review</button>
                        </form>
                    </div>
                </div>

                <!-- Reviews List -->
                <div class="reviews-list" style="flex:2; min-width:300px;">
                    <?php if (count($reviews) > 0): ?>
                        <?php foreach ($reviews as $r): ?>
                            <div class="review-item" style="border-bottom:1px solid #f0f0f0; padding-bottom:15px; margin-bottom:15px;">
                                <div style="display:flex; align-items:center; gap:10px; margin-bottom:5px;">
                                    <div class="product-rating-box" style="font-size:11px; padding:1px 4px;"><?php echo $r['rating']; ?> <i class="fa-solid fa-star"></i></div>
                                    <span style="font-weight:600; font-size:14px;"><?php echo htmlspecialchars($r['review']); ?></span>
                                </div>
                                <div style="color:#878787; font-size:12px;">
                                    <?php echo htmlspecialchars($r['user_name']); ?> 
                                    <i class="fa-solid fa-circle-check" style="font-size:10px; color:#878787;"></i> 
                                    Certified Buyer, <?php echo date('M, Y', strtotime($r['created_at'])); ?>
                                </div>
                            </div>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <div style="color:#878787;">No reviews yet. Be the first to review!</div>
                    <?php endif; ?>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Related Products -->
    <?php if (count($relatedProducts) > 0): ?>
    <div style="margin-top:20px; background:#fff; padding:15px; border:1px solid #f0f0f0;"> <!-- Using inline style for section container just to keep it distinct from details-section class logic if needed, but cleaner to use class -->
        <h2 style="font-size:20px; font-weight:600; margin-bottom:15px;">Check These Out Too</h2>
        <div class="related-products-grid" style="display:flex; gap:15px; overflow-x:auto; padding-bottom:10px;">
            <?php foreach ($relatedProducts as $rp): ?>
                <a href="product-details.php?slug=<?php echo $rp['slug']; ?>" class="related-card" style="min-width:180px; max-width:180px; text-decoration:none; color:inherit; border:1px solid #f0f0f0; padding:10px; transition:box-shadow 0.2s;">
                    <div style="height:150px; display:flex; align-items:center; justify-content:center; margin-bottom:10px;">
                        <?php 
                            $rpImg = $rp['featured_image'] ? $rp['featured_image'] : 'https://placehold.co/150x150?text=Product';
                        ?>
                        <img src="<?php echo htmlspecialchars($rpImg); ?>" alt="<?php echo htmlspecialchars($rp['name']); ?>" style="max-width:100%; max-height:100%; object-fit:contain;">
                    </div>
                    <div style="font-size:13px; font-weight:500; height:36px; overflow:hidden; margin-bottom:5px;" title="<?php echo htmlspecialchars($rp['name']); ?>">
                        <?php echo htmlspecialchars($rp['name']); ?>
                    </div>
                    <div style="color:#388e3c; font-size:12px;">Recently Added</div>
                     <?php if ($rp['is_price_enabled'] && $rp['sales_price']): ?>
                        <div style="font-weight:600; font-size:15px; margin-top:5px;">₹<?php echo number_format($rp['sales_price']); ?></div>
                    <?php else: ?>
                        <div style="color:#d32f2f; font-size:13px; margin-top:5px;">Price on Request</div>
                    <?php endif; ?>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
    <?php endif; ?>

</div>

<script>
function toggleReviewForm() {
    var form = document.getElementById('review-form-container');
    if (form.style.display === 'none') {
        form.style.display = 'block';
    } else {
        form.style.display = 'none';
    }
}
</script>

<!-- Mobile Sticky Actions -->
<div class="mobile-sticky-actions">
    <?php if ($hasPrice): ?>
        <button class="action-btn btn-cart" style="flex:1;"><i class="fa-solid fa-cart-shopping"></i> Add to Cart</button>
        <button class="action-btn btn-buy" style="flex:1;"><i class="fa-solid fa-bolt"></i> Buy Now</button>
    <?php else: ?>
        <a href="https://wa.me/919999049135?text=I am interested in <?php echo urlencode($product['name']); ?>" class="action-btn btn-buy" style="flex:1; background:#25D366;"><i class="fa-brands fa-whatsapp"></i> Enquire Now</a>
        <a href="mailto:support@raenterprises.com?subject=Quote Request for <?php echo urlencode($product['name']); ?>" class="action-btn btn-cart" style="flex:1; background:#2874f0;"><i class="fa-solid fa-envelope"></i> Get a Quote</a>
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
