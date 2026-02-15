<?php
$adminBase = '..';
include '../includes/check_login.php';
require_once '../../database/db_config.php';

// Pagination Setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
$start = ($page - 1) * $limit;

// Fetch Brand Categories with Brand Name
try {
    // Get total count
    $stmt = $pdo->query("SELECT COUNT(*) FROM brand_categories");
    $total_results = $stmt->fetchColumn();
    $total_pages = ceil($total_results / $limit);

    // Get records
    $sql = "SELECT brand_categories.*, brands.name as brand_name 
            FROM brand_categories 
            LEFT JOIN brands ON brand_categories.brand_id = brands.id 
            ORDER BY brand_categories.id DESC LIMIT :start, :limit";
    $stmt = $pdo->prepare($sql);
    $stmt->bindValue(':start', $start, PDO::PARAM_INT);
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->execute();
    $brand_categories = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Brand Categories &lsaquo; RA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .admin-table {
            width: 100%;
            border-collapse: collapse;
            background: #fff;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
        }
        .admin-table th, .admin-table td {
            text-align: left;
            padding: 12px 15px;
            border-bottom: 1px solid #f0f0f1;
        }
        .admin-table th {
            font-weight: 600;
            color: #1d2327;
        }
        .admin-table tr:hover {
            background-color: #f6f7f7;
        }
        .category-image {
            width: 80px;
            height: 50px;
            object-fit: contain;
            border: 1px solid #ddd;
            padding: 2px;
            border-radius: 4px;
            background-color: #fff;
        }
        .action-btn {
            text-decoration: none;
            margin-right: 10px;
            font-size: 13px;
        }
        .edit-btn { color: #2271b1; }
        .edit-btn:hover { color: #135e96; }
        .delete-btn { color: #d63638; }
        .delete-btn:hover { color: #a02526; }
        
        .pagination {
            display: flex;
            justify-content: center;
            margin-top: 20px;
            gap: 5px;
        }
        .page-link {
            padding: 5px 10px;
            border: 1px solid #c3c4c7;
            background: #fff;
            text-decoration: none;
            color: #2271b1;
            font-size: 13px;
        }
        .page-link.active {
            background: #f0f0f1;
            color: #3c434a;
            border-color: #8c8f94;
            font-weight: 600;
        }
        .add-new-btn {
            background-color: #2271b1;
            color: #fff;
            padding: 8px 12px;
            text-decoration: none;
            border-radius: 3px;
            font-size: 13px;
            display: inline-block;
            margin-bottom: 15px;
        }
        .add-new-btn:hover {
            background-color: #135e96;
        }
        .brand-badge {
            background-color: #f0f0f1;
            padding: 2px 6px;
            border-radius: 3px;
            font-size: 12px;
            color: #3c434a;
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include '../includes/header.php'; ?>
        <main class="admin-main">
            <div style="display: flex; justify-content: space-between; align-items: center;">
                <h1 class="page-title">Brand Categories</h1>
                <a href="add.php" class="add-new-btn">Add New Category</a>
            </div>

            <table class="admin-table">
                <thead>
                    <tr>
                        <th width="50">ID</th>
                        <th width="90">Image</th>
                        <th>Name</th>
                        <th>Brand</th>
                        <th>Slug</th>
                        <th width="150">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <?php if (count($brand_categories) > 0): ?>
                        <?php foreach ($brand_categories as $cat): ?>
                            <tr>
                                <td><?php echo $cat['id']; ?></td>
                                <td>
                                    <?php if($cat['image']): 
                                        $imgSrc = $cat['image'];
                                        if (strpos($imgSrc, 'http') !== 0) {
                                            $imgSrc = '../../' . $imgSrc;
                                        }
                                    ?>
                                        <img src="<?php echo htmlspecialchars($imgSrc); ?>" class="category-image" alt="Cat Img">
                                    <?php else: ?>
                                        <div class="category-image" style="display:flex;align-items:center;justify-content:center;color:#ccc;"><i class="fa-solid fa-image"></i></div>
                                    <?php endif; ?>
                                </td>
                                <td><strong><?php echo htmlspecialchars($cat['name']); ?></strong></td>
                                <td><span class="brand-badge"><?php echo htmlspecialchars($cat['brand_name']); ?></span></td>
                                <td><?php echo htmlspecialchars($cat['slug']); ?></td>
                                <td>
                                    <a href="edit.php?id=<?php echo $cat['id']; ?>" class="action-btn edit-btn">Edit</a>
                                    <a href="delete.php?id=<?php echo $cat['id']; ?>" class="action-btn delete-btn" onclick="return confirm('Are you sure you want to delete this category?');">Delete</a>
                                </td>
                            </tr>
                        <?php endforeach; ?>
                    <?php else: ?>
                        <tr>
                            <td colspan="6" style="text-align:center; padding: 20px;">No brand categories found.</td>
                        </tr>
                    <?php endif; ?>
                </tbody>
            </table>

            <!-- Pagination -->
            <?php if ($total_pages > 1): ?>
                <div class="pagination">
                    <?php for ($i = 1; $i <= $total_pages; $i++): ?>
                        <a href="?page=<?php echo $i; ?>" class="page-link <?php if($page == $i) echo 'active'; ?>"><?php echo $i; ?></a>
                    <?php endfor; ?>
                </div>
            <?php endif; ?>
        </main>
    </div>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
