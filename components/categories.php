<?php
$categories = [
    ['icon' => 'fa-solid fa-clock', 'label' => "24 hrs\nDelivery", 'color' => '#ff9f00'], // Custom color for "New" feel
    ['icon' => 'fa-solid fa-plug', 'label' => "Electrical &\nAppliances"],
    ['icon' => 'fa-solid fa-wrench', 'label' => "Industrial\nTools"],
    ['icon' => 'fa-solid fa-chair', 'label' => "Office\nSupplies"],
    ['icon' => 'fa-solid fa-helmet-safety', 'label' => "Safety\nSupplies"],
    ['icon' => 'fa-solid fa-flask', 'label' => "Medical & Lab\nSupplies"],
    ['icon' => 'fa-solid fa-seedling', 'label' => "Agri &\nGardening"],
    ['icon' => 'fa-solid fa-trowel-bricks', 'label' => "Construction\nMaterials"],
    ['icon' => 'fa-solid fa-car', 'label' => "Automotive"],
    ['icon' => 'fa-solid fa-box-open', 'label' => "Packaging & Material\nHandling"],
     ['icon' => 'fa-solid fa-truck-fast', 'label' => "Mogli\nExpress", 'color' => '#d32f2f']
];
?>

<div class="categories-wrapper">
    <div class="categories-container">
        <?php foreach ($categories as $category): ?>
            <a href="#" class="category-item">
                <div class="category-icon">
                    <!-- Using FontAwesome for now, can be replaced with <img> -->
                    <i class="<?php echo $category['icon']; ?>" style="<?php echo isset($category['color']) ? 'color:'.$category['color'] : ''; ?>"></i>
                </div>
                <div class="category-label"><?php echo nl2br($category['label']); ?></div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
