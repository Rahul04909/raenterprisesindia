<?php
$adminBase = '..';
include '../includes/check_login.php';
require_once '../../database/db_config.php';

// Pagination setup
$limit = 10;
$page = isset($_GET['page']) ? (int)$_GET['page'] : 1;
if ($page < 1) $page = 1;
$offset = ($page - 1) * $limit;

try {
    // Get total count
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM product_quotes");
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // Fetch quotes with product names
    $stmt = $pdo->prepare("
        SELECT q.*, p.name as product_name, p.slug as product_slug 
        FROM product_quotes q
        JOIN products p ON q.product_id = p.id
        ORDER BY q.created_at DESC
        LIMIT :limit OFFSET :offset
    ");
    $stmt->bindValue(':limit', $limit, PDO::PARAM_INT);
    $stmt->bindValue(':offset', $offset, PDO::PARAM_INT);
    $stmt->execute();
    $quotes = $stmt->fetchAll();

} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Quote Requests &lsaquo; RA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .quotes-table-container {
            background: #fff;
            padding: 20px;
            border: 1px solid #c3c4c7;
            margin-top: 20px;
            overflow-x: auto;
        }
        table {
            width: 100%;
            border-collapse: collapse;
            font-size: 14px;
        }
        th, td {
            text-align: left;
            padding: 12px;
            border-bottom: 1px solid #f0f0f1;
        }
        th { background: #f8f9fa; font-weight: 600; }
        .status-badge {
            padding: 4px 8px;
            border-radius: 4px;
            font-size: 12px;
            font-weight: 500;
            text-transform: capitalize;
        }
        .status-new { background: #dcfce7; color: #166534; }
        
        .pagination {
            display: flex;
            justify-content: center;
            gap: 5px;
            margin-top: 20px;
        }
        .pagination a {
            padding: 8px 12px;
            border: 1px solid #c3c4c7;
            background: #fff;
            color: #2271b1;
            text-decoration: none;
            border-radius: 3px;
            font-size: 13px;
        }
        .pagination a.active {
            background: #2271b1;
            color: #fff;
            border-color: #2271b1;
        }
        .pagination a:hover:not(.active) {
            background: #f0f0f1;
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include '../includes/header.php'; ?>
        <main class="admin-main">
            <h1 class="page-title">Product Quote Requests</h1>
            
            <div class="quotes-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Product</th>
                            <th>Customer Name</th>
                            <th>Contact Info</th>
                            <th>Qty</th>
                            <th>Date</th>
                            <th>Status</th>
                        </tr>
                    </thead>
                    <tbody>
                        <?php if (count($quotes) > 0): ?>
                            <?php foreach ($quotes as $q): ?>
                                <tr>
                                    <td>#<?php echo $q['id']; ?></td>
                                    <td>
                                        <a href="../../product-details.php?slug=<?php echo $q['product_slug']; ?>" target="_blank" style="color:#2271b1; font-weight:500;">
                                            <?php echo htmlspecialchars($q['product_name']); ?>
                                        </a>
                                    </td>
                                    <td><strong><?php echo htmlspecialchars($q['name']); ?></strong></td>
                                    <td>
                                        <div style="font-size:12px; color:#646970;">
                                            <i class="fa-solid fa-envelope" style="width:14px;"></i> <?php echo htmlspecialchars($q['email']); ?>
                                        </div>
                                        <div style="font-size:12px; color:#646970; margin-top:2px;">
                                            <i class="fa-solid fa-phone" style="width:14px;"></i> <?php echo htmlspecialchars($q['mobile']); ?>
                                        </div>
                                    </td>
                                    <td><?php echo $q['quantity']; ?></td>
                                    <td><?php echo date('M d, Y', strtotime($q['created_at'])); ?></td>
                                    <td><span class="status-badge status-<?php echo $q['status']; ?>"><?php echo $q['status']; ?></span></td>
                                </tr>
                                <?php if($q['message']): ?>
                                    <tr style="background:#fdfdfd;">
                                        <td colspan="7" style="padding: 8px 12px 15px 40px; color:#50575e; font-style:italic;">
                                            <i class="fa-solid fa-comment-dots" style="margin-right:5px; color:#c3c4c7;"></i>
                                            "<?php echo htmlspecialchars($q['message']); ?>"
                                        </td>
                                    </tr>
                                <?php endif; ?>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center; padding:30px;">No quote requests found.</td></tr>
                        <?php endif; ?>
                    </tbody>
                </table>

                <?php if ($totalPages > 1): ?>
                    <div class="pagination">
                        <?php if ($page > 1): ?>
                            <a href="?page=<?php echo $page - 1; ?>">&laquo; Previous</a>
                        <?php endif; ?>
                        
                        <?php for ($i = 1; $i <= $totalPages; $i++): ?>
                            <a href="?page=<?php echo $i; ?>" class="<?php echo $i == $page ? 'active' : ''; ?>"><?php echo $i; ?></a>
                        <?php endfor; ?>

                        <?php if ($page < $totalPages): ?>
                            <a href="?page=<?php echo $page + 1; ?>">Next &raquo;</a>
                        <?php endif; ?>
                    </div>
                <?php endif; ?>
            </div>
        </main>
    </div>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
