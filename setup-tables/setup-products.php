<?php
require_once '../database/db_config.php';

try {
    // Products Table
    $sql_products = "CREATE TABLE IF NOT EXISTS products (
        id INT AUTO_INCREMENT PRIMARY KEY,
        name VARCHAR(255) NOT NULL,
        slug VARCHAR(255) NOT NULL UNIQUE,
        category_id INT,
        brand_id INT,
        brand_category_id INT,
        short_description TEXT,
        description LONGTEXT,
        is_price_enabled TINYINT(1) DEFAULT 0,
        mrp DECIMAL(10, 2) DEFAULT NULL,
        sales_price DECIMAL(10, 2) DEFAULT NULL,
        stock INT DEFAULT 0,
        sku VARCHAR(100),
        hsn VARCHAR(100),
        model_number VARCHAR(100),
        specifications LONGTEXT,
        brochure_path VARCHAR(255),
        featured_image VARCHAR(255),
        meta_title VARCHAR(255),
        meta_description TEXT,
        meta_keywords TEXT,
        schema_markup LONGTEXT,
        og_title VARCHAR(255),
        og_description TEXT,
        og_image VARCHAR(255),
        status ENUM('published', 'draft') DEFAULT 'published',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        updated_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP ON UPDATE CURRENT_TIMESTAMP,
        FOREIGN KEY (category_id) REFERENCES categories(id) ON DELETE SET NULL,
        FOREIGN KEY (brand_id) REFERENCES brands(id) ON DELETE SET NULL,
        FOREIGN KEY (brand_category_id) REFERENCES brand_categories(id) ON DELETE SET NULL
    )";
    $pdo->exec($sql_products);
    echo "Table 'products' created successfully.<br>";

    // Product Images Table (Gallery)
    $sql_images = "CREATE TABLE IF NOT EXISTS product_images (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        image_path VARCHAR(255) NOT NULL,
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_images);
    echo "Table 'product_images' created successfully.<br>";

    // Product Reviews Table
    $sql_reviews = "CREATE TABLE IF NOT EXISTS product_reviews (
        id INT AUTO_INCREMENT PRIMARY KEY,
        product_id INT NOT NULL,
        user_name VARCHAR(100) NOT NULL,
        user_email VARCHAR(100),
        rating INT NOT NULL CHECK (rating >= 1 AND rating <= 5),
        review TEXT,
        status ENUM('pending', 'approved', 'rejected') DEFAULT 'pending',
        created_at TIMESTAMP DEFAULT CURRENT_TIMESTAMP,
        FOREIGN KEY (product_id) REFERENCES products(id) ON DELETE CASCADE
    )";
    $pdo->exec($sql_reviews);
    echo "Table 'product_reviews' created successfully.<br>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
