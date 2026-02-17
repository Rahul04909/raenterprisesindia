<?php
session_start();
if (!isset($_SESSION['admin_logged_in'])) {
    header('Location: ../login.php');
    exit;
}
require_once '../../database/db_config.php';

// Handle Form Submission
if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    try {
        // Reset all first (simpler than tracking changes)
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
    <title>Manage Best Seller Products - Admin Panel</title>
    <link rel="stylesheet" href="../../assets/css/style.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        .category-list {
            display: grid;
            grid-template-columns: repeat(auto-fill, minmax(250px, 1fr));
            gap: 15px;
            margin-top: 20px;
        }
        .category-item {
            background: #fff;
            padding: 15px;
            border: 1px solid #ddd;
            border-radius: 4px;
            display: flex;
            align-items: center;
            gap: 10px;
            cursor: pointer;
            transition: box-shadow 0.2s;
        }
        .category-item:hover {
            box-shadow: 0 2px 5px rgba(0,0,0,0.1);
        }
        .category-item input {
            transform: scale(1.2);
            cursor: pointer;
        }
        .category-icon {
            width: 40px;
            height: 40px;
            object-fit: contain;
            border-radius: 4px;
            background: #f9f9f9;
        }
        .btn-save {
            background: #2874f0;
            color: #fff;
            border: none;
            padding: 10px 20px;
            border-radius: 4px;
            cursor: pointer;
            font-size: 16px;
            margin-top: 20px;
        }
        .btn-save:hover {
            background: #1a5ac6;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <?php include '../includes/sidebar.php'; ?>
        
        <main class="admin-main">
            <?php include '../includes/header.php'; ?>
            
            <div class="admin-content">
                <div class="page-header">
                    <h1>Manage Best Seller Categories</h1>
                </div>

                <?php if (isset($success)): ?>
                    <div class="alert alert-success"><?php echo $success; ?></div>
                <?php endif; ?>
                <?php if (isset($error)): ?>
                    <div class="alert alert-danger"><?php echo $error; ?></div>
                <?php endif; ?>

                <div class="content-card">
                    <p>Select the categories you want to feature in the "Best Sellers" section on the homepage.</p>
                    
                    <form method="POST" action="">
                        <div class="category-list">
                            <?php foreach ($categories as $cat): ?>
                                <label class="category-item">
                                    <input type="checkbox" name="best_sellers[]" value="<?php echo $cat['id']; ?>" 
                                        <?php echo $cat['is_best_seller'] ? 'checked' : ''; ?>>
                                    
                                    <?php 
                                        $img = $cat['image'] ? '../../' . $cat['image'] : 'https://placehold.co/40?text=' . substr($cat['name'], 0, 1);
                                    ?>
                                    <img src="<?php echo htmlspecialchars($img); ?>" alt="" class="category-icon">
                                    
                                    <span><?php echo htmlspecialchars($cat['name']); ?></span>
                                </label>
                            <?php endforeach; ?>
                        </div>
                        
                        <button type="submit" class="btn-save"><i class="fa-solid fa-save"></i> Save Changes</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
    <script src="../assets/js/admin.js"></script>
</body>
</html>
