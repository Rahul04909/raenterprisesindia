<?php
$adminBase = '..';
include '../includes/check_login.php';
require_once '../../database/db_config.php';

$id = isset($_GET['id']) ? (int)$_GET['id'] : 0;
$error = '';
$success = '';

// Fetch existing data
try {
    $stmt = $pdo->prepare("SELECT * FROM brand_categories WHERE id = ?");
    $stmt->execute([$id]);
    $category = $stmt->fetch();
    
    if (!$category) {
        die("Brand Category not found.");
    }

    // Fetch Brands
    $stmt = $pdo->query("SELECT id, name FROM brands ORDER BY name ASC");
    $brands = $stmt->fetchAll();

} catch(PDOException $e) {
    die("Error: " . $e->getMessage());
}

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $brand_id = (int)$_POST['brand_id'];
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);
    $imagePath = $category['image'];

    // Image Handling
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image_file']['name'];
        $filesize = $_FILES['image_file']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
             $error = "Invalid file type.";
        } elseif ($filesize > 2 * 1024 * 1024) {
             $error = "File size is too large. Max 2MB.";
        } else {
            $newFilename = $slug . '-' . time() . '.' . $ext;
            $uploadDir = '../../assets/uploads/brand-categories/';
            
             if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $newFilename)) {
                $imagePath = 'assets/uploads/brand-categories/' . $newFilename;
            } else {
                $error = "Failed to upload image.";
            }
        }
    } elseif (!empty($_POST['image_url'])) {
        $imagePath = trim($_POST['image_url']);
    }

    if (empty($error)) {
        if (empty($name) || empty($slug) || $brand_id == 0) {
            $error = "Name, Slug, and Brand are required.";
        } else {
            try {
                // Check for duplicate slug
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM brand_categories WHERE slug = ? AND id != ?");
                $stmt->execute([$slug, $id]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "Slug already exists.";
                } else {
                    $stmt = $pdo->prepare("UPDATE brand_categories SET brand_id = ?, name = ?, slug = ?, description = ?, image = ? WHERE id = ?");
                    if ($stmt->execute([$brand_id, $name, $slug, $description, $imagePath, $id])) {
                        $success = "Brand Category updated successfully.";
                        $category['brand_id'] = $brand_id;
                        $category['name'] = $name;
                        $category['slug'] = $slug;
                        $category['description'] = $description;
                        $category['image'] = $imagePath;
                    } else {
                        $error = "Failed to update brand category.";
                    }
                }
            } catch(PDOException $e) {
                $error = "Error: " . $e->getMessage();
            }
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Edit Brand Category &lsaquo; RA Admin</title>
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
        .divider-text {
            text-align: center;
            margin: 10px 0;
            font-size: 12px;
            color: #646970;
            position: relative;
        }
        .divider-text::before, .divider-text::after {
            content: "";
            display: inline-block;
            width: 30%;
            height: 1px;
            background: #dcdcde;
            vertical-align: middle;
            margin: 0 10px;
        }
        .current-image {
            margin-bottom: 10px;
            display: block;
        }
        .current-image img {
            max-width: 100px;
            border: 1px solid #ddd;
            border-radius: 4px;
            padding: 4px;
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include '../includes/header.php'; ?>
        <main class="admin-main">
            <h1 class="page-title">Edit Brand Category</h1>
            
            <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?> <a href="index.php">Back to List</a></div><?php endif; ?>

            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Brand</label>
                        <select name="brand_id" required>
                            <option value="">Select Brand</option>
                            <?php foreach($brands as $brand): ?>
                                <option value="<?php echo $brand['id']; ?>" <?php if($brand['id'] == $category['brand_id']) echo 'selected'; ?>>
                                    <?php echo htmlspecialchars($brand['name']); ?>
                                </option>
                            <?php endforeach; ?>
                        </select>
                    </div>
                    <div class="form-group">
                        <label>Category Name</label>
                        <input type="text" name="name" value="<?php echo htmlspecialchars($category['name']); ?>" required>
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" value="<?php echo htmlspecialchars($category['slug']); ?>" required>
                    </div>
                    
                    <div class="form-group">
                        <label>Category Image (Upload)</label>
                        <?php if($category['image']): ?>
                            <div class="current-image">
                                <?php 
                                    $imgSrc = $category['image'];
                                    if(strpos($imgSrc, 'assets/uploads') !== false) {
                                         $imgSrc = '../../' . $imgSrc;
                                    }
                                ?>
                                <img src="<?php echo htmlspecialchars($imgSrc); ?>" alt="Current Image">
                                <small style="display:block; color:#666;">Current Image</small>
                            </div>
                        <?php endif; ?>
                        <input type="file" name="image_file" accept="image/*">
                    </div>
                    
                    <div class="divider-text">OR</div>

                    <div class="form-group">
                        <label>Category Image (URL)</label>
                        <input type="text" name="image_url" placeholder="https://example.com/image.jpg" value="<?php echo (strpos($category['image'], 'http') === 0) ? htmlspecialchars($category['image']) : ''; ?>">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"><?php echo htmlspecialchars($category['description']); ?></textarea>
                    </div>
                    <div style="display:flex; justify-content:space-between; align-items:center;">
                        <button type="submit" class="submit-btn">Update Category</button>
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
