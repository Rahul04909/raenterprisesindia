<?php
// Ensure DB connection is available
require_once __DIR__ . '/../database/db_config.php';

try {
    // Fetch categories from the database
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    // Fallback or empty if error
    $categories = [];
}
?>

<div class="categories-wrapper">
    <div class="categories-container">
        <?php if (count($categories) > 0): ?>
            <?php foreach ($categories as $category): ?>
                <a href="products.php?category=<?php echo htmlspecialchars($category['slug']); ?>" class="category-item">
                    <div class="category-image">
                        <?php 
                            $imgSrc = $category['image'];
                            // Fallback image if empty
                            if (empty($imgSrc)) {
                                $placeholderText = urlencode($category['name']);
                                $imgSrc = "https://placehold.co/60x60/f5f5f5/333333?text=$placeholderText";
                            }
                        ?>
                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                             alt="<?php echo htmlspecialchars($category['name']); ?>"
                             title="<?php echo htmlspecialchars($category['name']); ?>"
                             onerror="this.src='https://placehold.co/60x60/f5f5f5/333333?text=N/A'"
                             style="width: 60px; height: 60px; object-fit: contain;"> 
                    </div>
                     <!-- Optional: If you want to show the name below the image matching the original design -->
                    <div class="category-label" style="text-align: center; font-size: 12px; margin-top: 5px; color: #333; font-weight: 500;">
                        <?php echo htmlspecialchars($category['name']); ?>
                    </div>
                </a>
            <?php endforeach; ?>
        <?php else: ?>
            <!-- Optional: Show placeholder or nothing if no categories found -->
             <p style="text-align:center; padding: 20px; color: #777;">No categories found.</p>
        <?php endif; ?>
    </div>
</div>
