<?php
$adminBase = '..';
// include '../includes/check_login.php'; // Using manual check as that file might check path differently or just be standard
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require_once '../../database/db_config.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Reset all first
        $pdo->exec("UPDATE categories SET is_best_seller = 0");

        if (isset($_POST['best_sellers']) && is_array($_POST['best_sellers'])) {
            $selectedIds = implode(',', array_map('intval', $_POST['best_sellers']));
            if (!empty($selectedIds)) {
                $pdo->exec("UPDATE categories SET is_best_seller = 1 WHERE id IN ($selectedIds)");
            }
        }
        $success = "Best Seller categories updated successfully!";
    } catch (PDOException $e) {
        $error = "Error updating categories: " . $e->getMessage();
    }
}

// Fetch all categories
try {
    $stmt = $pdo->query("SELECT * FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();
} catch (PDOException $e) {
    die("Error fetching data: " . $e->getMessage());
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Best Sellers &lsaquo; RA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .content-card {
            background: #fff;
            padding: 20px;
            box-shadow: 0 1px 1px rgba(0,0,0,0.04);
            border: 1px solid #c3c4c7;
        }
        .category-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(220px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .category-item {
            background: #fff;
            padding: 10px;
            border: 1px solid #dcdcde;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: border-color 0.2s;
        }
        .category-item:hover {
            border-color: #2271b1;
        }
        .category-item input {
            transform: scale(1.1);
            cursor: pointer;
            margin-right: 5px;
        }
        .category-icon {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 3px;
            background: #f0f0f1;
            border: 1px solid #f0f0f1;
        }
        .btn-save {
            background: #2271b1;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 14px;
            margin-top: 20px;
            font-weight: 600;
        }
        .btn-save:hover {
            background: #135e96;
        }
        .alert {
            padding: 10px 15px;
            margin-bottom: 20px;
            border-left: 4px solid;
        }
        .alert-success {
            background: #fff;
            border-color: #00a32a;
            color: #1d2327;
        }
        .alert-danger {
            background: #fff;
            border-color: #d63638;
            color: #1d2327;
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include '../includes/header.php'; ?>
        <main class="admin-main">
            <h1 class="page-title">Manage Best Seller Categories</h1>
            
            <?php if (isset($success)): ?>
                <div class="alert alert-success"><?php echo $success; ?></div>
            <?php endif; ?>
            <?php if (isset($error)): ?>
                <div class="alert alert-danger"><?php echo $error; ?></div>
            <?php endif; ?>

            <div class="content-card">
                <p style="margin-top:0; color:#50575e;">Select the categories you want to feature in the "Best Sellers" section on the homepage.</p>
                
                <form method="POST" action="">
                    <div class="category-list">
                        <?php foreach ($categories as $cat): ?>
                            <label class="category-item">
                                <input type="checkbox" name="best_sellers[]" value="<?php echo $cat['id']; ?>" 
                                    <?php echo $cat['is_best_seller'] ? 'checked' : ''; ?>>
                                
                                <?php if($cat['image']): ?>
                                    <img src="<?php echo htmlspecialchars($cat['image']); ?>" class="category-icon" alt="">
                                <?php else: ?>
                                    <div class="category-icon" style="display:flex;align-items:center;justify-content:center;color:#ccc;"><i class="fa-solid fa-image"></i></div>
                                <?php endif; ?>
                                
                                <span style="font-weight:500; font-size:14px;"><?php echo htmlspecialchars($cat['name']); ?></span>
                            </label>
                        <?php endforeach; ?>
                    </div>
                    
                    <button type="submit" class="btn-save">Save Changes</button>
                </form>
            </div>
        </main>
    </div>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
