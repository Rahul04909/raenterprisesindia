<?php
// Data for Brands
$safetyBrands = [
    ['name' => 'JK Steel', 'logo' => 'https://placehold.co/60x60/orange/white?text=JK'],
    ['name' => 'ArmaDuro', 'logo' => 'https://placehold.co/60x60/black/white?text=AD'],
    ['name' => 'Durban', 'logo' => 'https://placehold.co/60x60/brown/white?text=DB'],
    ['name' => 'NEOSafe', 'logo' => 'https://placehold.co/60x60/blue/white?text=NS']
];

// Data for Featured Categories
$safetyCategories = [
    ['title' => 'Safety Shoes', 'image' => 'https://placehold.co/150x120/transparent/333?text=Shoes'],
    ['title' => 'Safety Gloves', 'image' => 'https://placehold.co/150x120/transparent/333?text=Gloves'],
    ['title' => 'Fire Extinguishers', 'image' => 'https://placehold.co/150x120/transparent/333?text=Fire+Ext'],
    ['title' => 'Safety Helmets', 'image' => 'https://placehold.co/150x120/transparent/333?text=Helmets']
];

// Data for Safety Products
$safetyProducts = [
    [
        'title' => 'JK Steel JKPI001BN Steel Toe Work Safety...',
        'image' => 'https://placehold.co/150x150?text=Safety+Shoe',
        'rating' => 4.7,
        'reviews' => 26,
        'price' => 649,
        'mrp' => 999,
        'discount' => 35
    ],
    [
        'title' => 'JK Steel JKPI002BN Steel Toe Work Safety...',
        'image' => 'https://placehold.co/150x150?text=Safety+Shoe+2',
        'rating' => 4.6,
        'reviews' => 38,
        'price' => 729,
        'mrp' => 999,
        'discount' => 27
    ],
    [
        'title' => 'Sai Safety Regular Size Cut Proof Nitrile PU...',
        'image' => 'https://placehold.co/150x150?text=Gloves',
        'rating' => 4.5,
        'reviews' => 6,
        'price' => 29,
        'mrp' => 79,
        'discount' => 63
    ],
    [
        'title' => 'SSWW 35g Blue Cotton Knitted Hand Gloves',
        'image' => 'https://placehold.co/150x150?text=Blue+Gloves',
        'rating' => 4.7,
        'reviews' => 6,
        'price' => 14,
        'mrp' => 19,
        'discount' => 26
    ],
    [
        'title' => 'Pyro Shield 6kg ABC Powder Type Fire...',
        'image' => 'https://placehold.co/150x150?text=Fire+Ext',
        'rating' => 4.3,
        'reviews' => 15,
        'price' => 1249,
        'mrp' => 7999,
        'discount' => 84
    ],
    [
        'title' => 'Ladwa ABS HDPE White Heavy Duty Superior...',
        'image' => 'https://placehold.co/150x150?text=Helmet',
        'rating' => 4.8, // Assuming value since image cut off
        'reviews' => 12,
        'price' => 105,
        'mrp' => 943,
        'discount' => 88
    ]
];
?>

<div class="safety-section">
    <div class="safety-header">
        <h2 class="safety-title">SAFETY</h2>
        <a href="#" class="view-all-btn">VIEW ALL</a>
    </div>

    <div class="safety-content">
        <!-- Top Row: Brands & Featured Categories -->
        <div class="safety-featured-grid">
            <!-- Brands Card -->
            <div class="brands-card">
                <div class="brands-title">Top Brands & Related Categories</div>
                <div class="brands-logos">
                    <?php foreach ($safetyBrands as $brand): ?>
                        <div class="brand-item">
                            <div class="brand-circle">
                                <img src="<?php echo $brand['logo']; ?>" alt="<?php echo $brand['name']; ?>">
                            </div>
                            <span class="brand-name"><?php echo $brand['name']; ?></span>
                        </div>
                    <?php endforeach; ?>
                </div>
            </div>

            <!-- Featured Categories Cards -->
            <?php foreach ($safetyCategories as $cat): ?>
                <a href="#" class="safety-cat-card">
                    <div class="safety-cat-image">
                        <img src="<?php echo $cat['image']; ?>" alt="<?php echo $cat['title']; ?>">
                    </div>
                    <div class="safety-cat-info">
                        <h3><?php echo $cat['title']; ?></h3>
                        <span class="safety-explore-link">Explore Now</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>

        <!-- Bottom Row: Products Slider -->
        <div class="safety-products-container">
            <?php foreach ($safetyProducts as $product): ?>
                <a href="#" class="safety-product-card">
                    <div class="product-image">
                        <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                    </div>
                    
                    <div class="rating-badge">
                        <?php echo $product['rating']; ?> <i class="fa-solid fa-star"></i>
                    </div>
                    <span class="review-count" style="font-size:11px; color:#878787;">(<?php echo $product['reviews']; ?> Reviews)</span>
                    
                    <h3 class="product-title" title="<?php echo htmlspecialchars($product['title']); ?>">
                        <?php echo htmlspecialchars($product['title']); ?>
                    </h3>
                    
                    <div class="price-block">
                        <span class="current-price">₹<?php echo number_format($product['price']); ?></span>
                        <span class="original-price">₹<?php echo number_format($product['mrp']); ?></span>
                        <span class="discount-text"><?php echo $product['discount']; ?>% OFF</span>
                    </div>
                </a>
            <?php endforeach; ?>
        </div>
    </div>
</div>
