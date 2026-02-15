<?php
$adminBase = '..';
include '../includes/check_login.php';
require_once '../../database/db_config.php';

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

try {
    // Get total count
    $stmt = $pdo->query("SELECT COUNT(*) FROM products");
    $total_results = $stmt->fetchColumn();
    $total_pages = ceil($total_results / $limit);

    // Get records
    $sql = "SELECT p.*, c.name as category_name, b.name as brand_name 
            FROM products p
            LEFT JOIN categories c ON p.category_id = c.id
            LEFT JOIN brands b ON p.brand_id = b.id
            ORDER BY p.id DESC LIMIT :start, :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $products = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Products &lsaquo; RA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .product-image {
            width: 80px;
            height: 50px;
            object-fit: contain;
            border: 1px solid #ddd;
            padding: 2px;
            border-radius: 4px;
            background-color: #fff;
        }
        .status-badge {
            padding: 3px 8px;
            border-radius: 3px;
            font-size: 11px;
            text-transform: uppercase;
            font-weight: 600;
        }
        .status-published { background-color: #e5f5fa; color: #008a20; border: 1px solid #008a20; }
        .status-draft { background-color: #f0f0f1; color: #50575e; border: 1px solid #c3c4c7; }
        
        .admin-table th, .admin-table td { padding: 10px; }
        .action-links a { margin-right: 8px; font-weight: 500; }
        .edit-link { color: #2271b1; }
        .delete-link { color: #d63638; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include '../includes/header.php'; ?>
        <main class="admin-main">
             <div style="display: flex; justify-content: space-between; align-items: center; margin-bottom: 20px;">
                <h1 class="page-title">Products</h1>
                <a href="add.php" class="button button-primary" style="background: #2271b1; color: white; padding: 8px 15px; text-decoration: none; border-radius: 4px;">Add New Product</a>
            </div>

            <div style="overflow-x: auto;">
                <table class="admin-table" style="width: 100%; border-collapse: collapse; background: white; white-space: nowrap;">
                    <thead>
                        <tr style="border-bottom: 1px solid #ccc; background: #f9f9f9;">
                            <th width="40">ID</th>
                            <th width="60">Image</th>
                            <th>Name</th>
                            <th>Category</th>
                            <th>Brand</th>
                            <th>Price</th>
                            <th>Stock</th>
                            <th>Status</th>
                            <th>Date</th>
                            <th>Actions</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($products) > 0): ?>
                            <?php foreach ($products as $p): ?>
                                <tr style="border-bottom: 1px solid #eee;">
                                    <td><?php echo $p['id']; ?></td>
                                    <td>
                                        <?php 
                                            $imgSrc = $p['featured_image'];
                                            if ($imgSrc) {
                                                if (strpos($imgSrc, 'http') !== 0) {
                                                    $imgSrc = '../../' . $imgSrc;
                                                }
                                                echo '<img src="' . htmlspecialchars($imgSrc) . '" class="product-image">';
                                            } else {
                                                echo '<div class="product-image" style="display:flex;align-items:center;justify-content:center;color:#ccc;"><i class="fa fa-image"></i></div>';
                                            }
                                        ?>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($p['name']); ?></strong></td>
                                    <td><?php echo htmlspecialchars($p['category_name'] ?: '-'); ?></td>
                                    <td><?php echo htmlspecialchars($p['brand_name'] ?: '-'); ?></td>
                                    <td>
                                        <?php if ($p['is_price_enabled']): ?>
                                            Min: ₹<?php echo number_format($p['sales_price']); ?>
                                        <?php else: ?>
                                            <span style="color:#666; font-style:italic;">On Request</span>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <?php if ($p['is_price_enabled']): ?>
                                             <?php echo $p['stock'] > 0 ? '<span style="color:green">In Stock (' . $p['stock'] . ')</span>' : '<span style="color:red">Out of Stock</span>'; ?>
                                        <?php else: ?>
                                            -
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <span class="status-badge status-<?php echo $p['status']; ?>"><?php echo ucfirst($p['status']); ?></span>
                                    </td>
                                    <td><?php echo date('d M Y', strtotime($p['created_at'])); ?></td>
                                    <td class="action-links">
                                        <a href="edit.php?id=<?php echo $p['id']; ?>" class="edit-link">Edit</a>
                                        <a href="delete.php?id=<?php echo $p['id']; ?>" class="delete-link" onclick="return confirm('Delete this product?');">Delete</a>
                                    </td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="10" style="text-align:center; padding: 20px;">No products found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>
            </div>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination" style="display:flex; justify-content:center; gap:5px; margin-top:20px;">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" style="padding:5px 10px; border:1px solid #ccc; text-decoration:none; <?php echo $page == $i ? 'background:#eee; font-weight:bold;' : 'background:white;'; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
