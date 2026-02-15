<?php
// Ensure DB connection
require_once __DIR__ . '/../database/db_config.php';

try {
    $stmt = $pdo->query("SELECT * FROM brands ORDER BY name ASC");
    $brands = $stmt->fetchAll();
} catch (PDOException $e) {
    $brands = [];
}
?>

<?php if (count($brands) > 0): ?>
<div class="brands-section">
    <div class="container">
        <h2 class="section-title">Our Brands</h2>
        <div class="brands-ticker-wrapper">
            <div class="brands-ticker-track">
                <!-- Original Set -->
                <?php foreach ($brands as $brand): ?>
                    <div class="brand-item">
                         <?php 
                            $imgSrc = $brand['image'];
                            if (empty($imgSrc)) {
                                $placeholderText = urlencode($brand['name']);
                                $imgSrc = "https://placehold.co/120x60/ffffff/333333?text=$placeholderText";
                            }
                        ?>
                        <a href="products.php?brand=<?php echo htmlspecialchars($brand['slug']); ?>">
                            <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                                 alt="<?php echo htmlspecialchars($brand['name']); ?>" 
                                 title="<?php echo htmlspecialchars($brand['name']); ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
                
                <!-- Duplicate Set for Seamless Loop -->
                <?php foreach ($brands as $brand): ?>
                    <div class="brand-item">
                         <?php 
                            $imgSrc = $brand['image'];
                            if (empty($imgSrc)) {
                                $placeholderText = urlencode($brand['name']);
                                $imgSrc = "https://placehold.co/120x60/ffffff/333333?text=$placeholderText";
                            }
                        ?>
                        <a href="products.php?brand=<?php echo htmlspecialchars($brand['slug']); ?>">
                             <img src="<?php echo htmlspecialchars($imgSrc); ?>" 
                                 alt="<?php echo htmlspecialchars($brand['name']); ?>" 
                                 title="<?php echo htmlspecialchars($brand['name']); ?>">
                        </a>
                    </div>
                <?php endforeach; ?>
            </div>
        </div>
    </div>
</div>
<?php endif; ?>
