<?php
require_once '../database/db_config.php';

try {
    $sql = "CREATE TABLE IF NOT EXISTS brands (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(100) NOT NULL,
        slug VARCHAR(100) NOT NULL UNIQUE,
        description TEXT,
        image VARCHAR(255),
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP
    )";

    $pdo->exec($sql);
    echo "Table 'brands' created successfully (or already exists).<br>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
