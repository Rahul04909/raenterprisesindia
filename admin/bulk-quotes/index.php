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
    $stmtCount = $pdo->query("SELECT COUNT(*) FROM bulk_quotes");
    $totalRows = $stmtCount->fetchColumn();
    $totalPages = ceil($totalRows / $limit);

    // Fetch bulk quotes
    $stmt = $pdo->prepare("
        SELECT * FROM bulk_quotes 
        ORDER BY created_at DESC 
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
    <title>Bulk Quotes &lsaquo; RA Admin</title>
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
        .status-contacted { background: #dbeafe; color: #1e40af; }
        
        .download-link {
            display: inline-flex;
            align-items: center;
            gap: 5px;
            background: #f0f0f1;
            padding: 5px 10px;
            border-radius: 4px;
            color: #2271b1;
            text-decoration: none;
            font-size: 12px;
            border: 1px solid #c3c4c7;
        }
        .download-link:hover { background: #fff; border-color: #2271b1; }

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
            <h1 class="page-title">Bulk Quote Requests</h1>
            
            <div class="quotes-table-container">
                <table>
                    <thead>
                        <tr>
                            <th>ID</th>
                            <th>Customer / Company</th>
                            <th>Contact Details</th>
                            <th>Address</th>
                            <th>Requirement File</th>
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
                                        <strong><?php echo htmlspecialchars($q['name']); ?></strong>
                                        <?php if($q['company_name']): ?>
                                            <div style="font-size:12px; color:#646970;"><?php echo htmlspecialchars($q['company_name']); ?></div>
                                        <?php endif; ?>
                                    </td>
                                    <td>
                                        <div style="font-size:12px;"><i class="fa-solid fa-envelope" style="width:14px; color:#646970;"></i> <?php echo htmlspecialchars($q['email']); ?></div>
                                        <div style="font-size:12px; margin-top:2px;"><i class="fa-solid fa-phone" style="width:14px; color:#646970;"></i> <?php echo htmlspecialchars($q['mobile']); ?></div>
                                    </td>
                                    <td>
                                        <div style="font-size:12px; color:#50575e; max-width:200px;"><?php echo htmlspecialchars($q['address']); ?></div>
                                    </td>
                                    <td>
                                        <?php if($q['attachment_path']): ?>
                                            <a href="../../<?php echo $q['attachment_path']; ?>" target="_blank" class="download-link">
                                                <i class="fa-solid fa-file-arrow-down"></i> Download File
                                            </a>
                                        <?php else: ?>
                                            <span style="color:#a0aec0; font-size:12px;">No file</span>
                                        <?php endif; ?>
                                    </td>
                                    <td><?php echo date('M d, Y', strtotime($q['created_at'])); ?></td>
                                    <td><span class="status-badge status-<?php echo $q['status']; ?>"><?php echo $q['status']; ?></span></td>
                                </tr>
                            <?php endforeach; ?>
                        <?php else: ?>
                            <tr><td colspan="7" style="text-align:center; padding:30px;">No bulk quote requests found.</td></tr>
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
