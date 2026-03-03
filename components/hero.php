<?php
// Simulating dynamic banners (In a real app, this would come from a database)
$banners = [
    [
        'image' => 'assets/hero/hero-1.jpg',
        'alt' => 'Super Sale'
    ],
    [
        'image' => 'assets/hero/hero-1.jpg',
        'alt' => 'New Arrivals'
    ],
    [
        'image' => 'assets/hero/hero-1.jpg',
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
