<?php
$adminBase = '..';
include '../includes/check_login.php';
require_once '../../database/db_config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Fetch existing data
try {
    $stmt = $pdo->prepare("SELECT * FROM brands WHERE id = ?");
    $stmt->execute([$id]);
    $brand = $stmt->fetch();
    
    if (!$brand) {
        die("Brand not found.");
    }

    // Fetch Categories
    $stmt = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC");
    $categories = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $category_id = (int)$_POST['category_id'];
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);
    $image = trim($_POST['image']);

    if (empty($name) || empty($slug) || $category_id == 0) {
        $error = "Name, Slug, and Category are required.";
    } else {
        try {
            // Check for duplicate slug
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM brands WHERE slug = ? AND id != ?");
            $stmt->execute([$slug, $id]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Slug already exists.";
            } else {
                $stmt = $pdo->prepare("UPDATE brands SET category_id = ?, name = ?, slug = ?, description = ?, image = ? WHERE id = ?");
                if ($stmt->execute([$category_id, $name, $slug, $description, $image, $id])) {
                    $success = "Brand updated successfully.";
                    $brand['category_id'] = $category_id;
                    $brand['name'] = $name;
                    $brand['slug'] = $slug;
                    $brand['description'] = $description;
                    $brand['image'] = $image;
                } else {
                    $error = "Failed to update brand.";
                }
            }
        } catch(PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Brand &lsaquo; RA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-container {
            background: #fff;
            padding: 20px;
            border: 1px solid #c3c4c7;
            max-width: 600px;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px; }
        .form-group input, .form-group textarea, .form-group select {
            width: 100%;
            padding: 8px;
            border: 1px solid #8c8f94;
            border-radius: 4px;
            box-sizing: border-box;
            font-size: 14px;
        }
        .submit-btn {
            background-color: #2271b1;
            color: #fff;
            border: none;
            padding: 10px 15px;
            border-radius: 3px;
            cursor: pointer;
            font-size: 13px;
        }
        .submit-btn:hover { background-color: #135e96; }
        .alert { padding: 10px; margin-bottom: 15px; border-left: 4px solid; }
        .alert-error { background: #fff; border-color: #d63638; }
        .alert-success { background: #fff; border-color: #00a32a; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include '../includes/header.php'; ?>
        <main class="admin-main">
            <h1 class="page-title">Edit Brand</h1>
            
            <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?> <a href="index.php">Back to Brands</a></div><?php endif; ?>

            <div class="form-container">
                <form method="POST" action="">
                    <div class="form-group">
                        <label>Category</label>
                        <select name="category_id" required>
                            <option value="">Select Category</option>
                            <?php foreach($categories as $cat): ?>
                                <option value="<?php echo $cat['id']; ?>" <?php if($cat['id'] == $brand['category_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($cat['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Brand Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($brand['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($brand['slug']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Image URL</label>
                        <input type="text" name="image" value="<?php echo htmlspecialchars($brand['image']); ?>">
                    </div>
                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?php echo htmlspecialchars($brand['description']); ?></textarea>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <button type="submit" class="submit-btn">Update Brand</button>
                        <a href="index.php" style="color:#d63638; text-decoration:none; font-size:13px;">Cancel</a>
                    </div>
                </form>
            </div>
        </main>
    </div>
</div>
<script src="../assets/js/admin.js"></script>
</body>
</html>
