<?php
require_once __DIR__ . '/../database/db_config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS bulk_quotes (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        email VARCHAR(100) NOT NULL,
        mobile VARCHAR(20) NOT NULL,
        company_name VARCHAR(150),
        address TEXT NOT NULL,
        attachment_path VARCHAR(255),
        status ENUM('new', 'contacted', 'completed', 'cancelled') DEFAULT 'new',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
    echo "Table 'bulk_quotes' created successfully.<br>";

} catch (PDOException $e) {
    echo "Error creating table: " . $e->getMessage();
}
?>
