<?php
require_once __DIR__ . '/../database/db_config.php';

try {
    // Add is_best_seller column to categories table if it doesn't exist
    $sql = "SHOW COLUMNS FROM categories LIKE 'is_best_seller'";
    $stmt = $pdo->prepare($sql);
    $stmt->execute();
    
    if ($stmt->rowCount() == 0) {
        $sql = "ALTER TABLE categories ADD COLUMN is_best_seller BOOLEAN DEFAULT 0";
        $pdo->exec($sql);
        echo "Column 'is_best_seller' added to 'categories' table successfully.<br>";
    } else {
        echo "Column 'is_best_seller' already exists in 'categories' table.<br>";
    }

} catch (PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
