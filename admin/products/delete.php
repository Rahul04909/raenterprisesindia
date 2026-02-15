<?php
$adminBase = '..';
include '../includes/check_login.php';
require_once '../../database/db_config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;

if ($id > 0) {
    try {
        // Delete images from folder (Optional - for cleanup)
        // This logic can be expanded to unlink files upon deletion

        // Delete from DB (Cascading will handle images and reviews)
        $stmt = $pdo->prepare("DELETE FROM products WHERE id = ?");
        if ($stmt->execute([$id])) {
             // Success
        }
    } catch(PDOException $e) {
        die("Error: " . $e->getMessage());
    }
}

// Redirect back to index
header("Location: index.php");
exit;
?>
