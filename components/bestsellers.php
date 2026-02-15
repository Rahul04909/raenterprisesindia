<?php
// Dummy data for bestsellers
$bestsellers = [
    [
        'title' => 'CyberNautt V4K 4MP Dual Lens Full HD Smart Wi-Fi Camera',
        'image' => 'https://placehold.co/150x150?text=Camera', 
        'rating' => 4.8,
        'reviews' => 148,
        'price' => 1449,
        'mrp' => 3999,
        'discount' => 63
    ],
    [
        'title' => 'Abbott FreeStyle Libre Glucometer Sensor',
        'image' => 'https://placehold.co/150x150?text=Sensor',
        'rating' => 4.9,
        'reviews' => 67,
        'price' => 4049,
        'mrp' => 5249,
        'discount' => 22
    ],
    [
        'title' => 'FAB 1 Sqmm Single Core Red FR PVC Copper Wire',
        'image' => 'https://placehold.co/150x150?text=Wire',
        'rating' => 4.7,
        'reviews' => 218,
        'price' => 469,
        'mrp' => 1600,
        'discount' => 70
    ],
    [
        'title' => 'Jakmister 18000rpm 950W Silver Heavy Duty Blower',
        'image' => 'https://placehold.co/150x150?text=Blower',
        'rating' => 4.8,
        'reviews' => 48,
        'price' => 1069,
        'mrp' => 4000,
        'discount' => 73
    ],
    [
        'title' => 'Aplus 12 Litre Hand Operating Milking Machine',
        'image' => 'https://placehold.co/150x150?text=Milking+Machine',
        'rating' => 4.7,
        'reviews' => 48,
        'price' => 5669,
        'mrp' => 10000,
        'discount' => 43
    ],
    [
        'title' => 'Longway 25L Grey Water Storage Geyser',
        'image' => 'https://placehold.co/150x150?text=Geyser',
        'rating' => 4.8,
        'reviews' => 85,
        'price' => 3599,
        'mrp' => 8669,
        'discount' => 58
    ],
    [
        'title' => 'Hillgrove 4 Pcs 2000W 7kg Copper Winding',
        'image' => 'https://placehold.co/150x150?text=Tools',
        'rating' => 4.7,
        'reviews' => 7,
        'price' => 3649,
        'mrp' => 10809,
        'discount' => 66
    ],
    [
        'title' => 'CabONE 2.5 Sqmm FR PVC Multi Strand Wire',
        'image' => 'https://placehold.co/150x150?text=Cable',
        'rating' => 4.7,
        'reviews' => 60,
        'price' => 969,
        'mrp' => 2690,
        'discount' => 63
    ]
];
?>

<div class="bestsellers-section">
    <div class="bestsellers-header">
        <h2 class="bestsellers-title">Bestsellers</h2>
    </div>
    
    <div class="bestsellers-container">
        <?php foreach ($bestsellers as $product): ?>
            <a href="#" class="product-card">
                <div class="product-image">
                    <img src="<?php echo $product['image']; ?>" alt="<?php echo htmlspecialchars($product['title']); ?>">
                </div>
                
                <div class="rating-badge">
                    <?php echo $product['rating']; ?> <i class="fa-solid fa-star"></i>
                </div>
                <span class="review-count">(<?php echo $product['reviews']; ?> Reviews)</span>
                
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
