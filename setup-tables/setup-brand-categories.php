<?php
require_once '../database/db_config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS brand_categories (
        id INT AUTO_INCREMENT PRIMARY KEY,
        brand_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE CASCADE
    )";

    $pdo->exec($sql);
    echo "Table 'brand_categories' created successfully.<br>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
