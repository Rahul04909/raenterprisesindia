<?php
require_once '../database/db_config.php';

try {
    // 1. Drop Foreign Key
    // We need to find the constraint name first, but usually it's brands_ibfk_1 or similar.
    // However, since we defined it as FOREIGN KEY (category_id) REFERENCES ..., MySQL generates a name.
    // Let's try to drop the foreign key by querying information_schema or just try standard names.
    // Alternatively, we can use a more robust approach:
    
    $dbname = $dbname; // from db_config
    $sql = "SELECT CONSTRAINT_NAME 
            FROM information_schema.KEY_COLUMN_USAGE 
            WHERE TABLE_NAME = 'brands' 
            AND COLUMN_NAME = 'category_id' 
            AND TABLE_SCHEMA = '$dbname'";
    
    $stmt = $pdo->query($sql);
    $constraintName = $stmt->fetchColumn();

    if ($constraintName) {
        $pdo->exec("ALTER TABLE brands DROP FOREIGN KEY $constraintName");
        echo "Foreign Foreign key '$constraintName' dropped.<br>";
    } else {
        echo "Foreign key not found (might already be dropped).<br>";
    }

    // 2. Drop Column
    // Check if column exists before dropping to avoid error? Or just try-catch.
    $pdo->exec("ALTER TABLE brands DROP COLUMN category_id");
    echo "Column 'category_id' dropped successfully.<br>";

} catch(PDOException $e) {
    echo "Error: " . $e->getMessage();
}
?>
