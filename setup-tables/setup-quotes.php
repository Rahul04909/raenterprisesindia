<?php
require_once __DIR__ . '/../database/db_config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS product_quotes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        quantity INT NOT NULL,
        message TEXT,
        status ENUM('new', 'processed', 'cancelled') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";

    $pdo->exec($sql);
    echo "Table 'product_quotes' created successfully.<br>";

} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
