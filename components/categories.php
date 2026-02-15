<?php
// Define categories with placeholder images matching the descriptive text
$categories = [
    ['image' => 'assets/images/categories/24-hrs.png', 'label' => "24 hrs\nDelivery", 'placeholder_text' => '24h'],
    ['image' => 'assets/images/categories/appliances.png', 'label' => "Electrical &\nAppliances", 'placeholder_text' => 'Appliances'],
    ['image' => 'assets/images/categories/tools.png', 'label' => "Industrial\nTools", 'placeholder_text' => 'Tools'],
    ['image' => 'assets/images/categories/office.png', 'label' => "Office\nSupplies", 'placeholder_text' => 'Office'],
    ['image' => 'assets/images/categories/safety.png', 'label' => "Safety\nSupplies", 'placeholder_text' => 'Safety'],
    ['image' => 'assets/images/categories/lab.png', 'label' => "Medical & Lab\nSupplies", 'placeholder_text' => 'Lab'],
    ['image' => 'assets/images/categories/agri.png', 'label' => "Agri &\nGardening", 'placeholder_text' => 'Agri'],
    ['image' => 'assets/images/categories/construction.png', 'label' => "Construction\nMaterials", 'placeholder_text' => 'Construction'],
    ['image' => 'assets/images/categories/automotive.png', 'label' => "Automotive", 'placeholder_text' => 'Auto'],
    ['image' => 'assets/images/categories/packaging.png', 'label' => "Packaging & Material\nHandling", 'placeholder_text' => 'Packaging'],
     ['image' => 'assets/images/categories/mogli.png', 'label' => "Mogli\nExpress", 'placeholder_text' => 'Mogli']
];
?>

<div class="categories-wrapper">
    <div class="categories-container">
        <?php foreach ($categories as $category): ?>
            <a href="#" class="category-item">
                <div class="category-image">
                    <!-- 
                        Using placehold.co for specific visual simulation. 
                        In production, replace with actual images in assets/images/categories/ 
                    -->
                    <img src="<?php echo $category['image']; ?>" 
                         alt="<?php echo str_replace("\n", " ", $category['label']); ?>"
                         title="<?php echo str_replace("\n", " ", $category['label']); ?>"
                         onerror="this.src='https://placehold.co/60x60/f5f5f5/333333?text=<?php echo urlencode($category['placeholder_text']); ?>'"
                    >
                </div>
            </a>
        <?php endforeach; ?>
    </div>
</div>
