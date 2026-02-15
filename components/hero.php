<?php
// Simulating dynamic banners (In a real app, this would come from a database)
$banners = [
    [
        'image' => 'https://placehold.co/1200x350/2980b9/ffffff?text=Super+Sale+Live+Now',
        'alt' => 'Super Sale'
    ],
    [
        'image' => 'https://placehold.co/1200x350/e74c3c/ffffff?text=New+Arrivals+Starting+@999',
        'alt' => 'New Arrivals'
    ],
    [
        'image' => 'https://placehold.co/1200x350/27ae60/ffffff?text=Free+Delivery+On+First+Order',
        'alt' => 'Free Delivery'
    ]
];
?>

<div class="hero-container">
    <div class="hero-carousel">
        <?php foreach ($banners as $banner): ?>
            <div class="hero-slide">
                <img src="<?php echo $banner['image']; ?>" alt="<?php echo $banner['alt']; ?>">
            </div>
        <?php endforeach; ?>
    </div>

    <!-- Navigation Buttons -->
    <button class="carousel-btn prev-btn" aria-label="Previous Slide">
        <i class="fa-solid fa-chevron-left"></i>
    </button>
    <button class="carousel-btn next-btn" aria-label="Next Slide">
        <i class="fa-solid fa-chevron-right"></i>
    </button>

    <!-- Pagination Dots -->
    <div class="carousel-dots"></div>
</div>

<!-- Inline Script for Critical Load (Optional, but using external file as requested) -->
<script src="assets/js/hero.js"></script>
