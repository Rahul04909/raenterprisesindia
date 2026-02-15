<?php
$adminBase = '..';
include '../includes/check_login.php';
require_once '../../database/db_config.php';

$error = '';
$success = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $name = trim($_POST['name']);
    $slug = trim($_POST['slug']);
    $description = trim($_POST['description']);
    
    // Image Handling
    $imagePath = '';
    
    // 1. Check for file upload
    if (isset($_FILES['image_file']) && $_FILES['image_file']['error'] == 0) {
        $allowed = ['jpg', 'jpeg', 'png', 'gif', 'webp'];
        $filename = $_FILES['image_file']['name'];
        $filetype = $_FILES['image_file']['type'];
        $filesize = $_FILES['image_file']['size'];
        $ext = strtolower(pathinfo($filename, PATHINFO_EXTENSION));

        if (!in_array($ext, $allowed)) {
             $error = "Invalid file type. Only JPG, PNG, GIF, and WebP are allowed.";
        } elseif ($filesize > 2 * 1024 * 1024) { // 2MB limit
             $error = "File size is too large. Max 2MB.";
        } else {
            // Generate unique name
            $newFilename = $slug . '-' . time() . '.' . $ext;
            $uploadDir = '../../assets/uploads/brands/';
            
            // Create dir if not exists
            if (!file_exists($uploadDir)) {
                mkdir($uploadDir, 0777, true);
            }

            if (move_uploaded_file($_FILES['image_file']['tmp_name'], $uploadDir . $newFilename)) {
                $imagePath = 'assets/uploads/brands/' . $newFilename;
            } else {
                $error = "Failed to upload image.";
            }
        }
    } 
    // 2. Check for URL if no file uploaded/error
    elseif (!empty($_POST['image_url'])) {
        $imagePath = trim($_POST['image_url']);
    }

    if (empty($error)) {
        if (empty($name) || empty($slug)) {
            $error = "Name and Slug are required.";
        } else {
            try {
                // Check for duplicate slug
                $stmt = $pdo->prepare("SELECT COUNT(*) FROM brands WHERE slug = ?");
                $stmt->execute([$slug]);
                if ($stmt->fetchColumn() > 0) {
                    $error = "Slug already exists. Please choose another.";
                } else {
                    $stmt = $pdo->prepare("INSERT INTO brands (name, slug, description, image) VALUES (?, ?, ?, ?)");
                    if ($stmt->execute([$name, $slug, $description, $imagePath])) {
                        $success = "Brand added successfully.";
                    } else {
                        $error = "Failed to add brand.";
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
    <title>Add Brand &lsaquo; RA Admin</title>
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
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include '../includes/header.php'; ?>
        <main class="admin-main">
            <h1 class="page-title">Add New Brand</h1>
            
            <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?> <a href="index.php">View All</a></div><?php endif; ?>

            <div class="form-container">
                <form method="POST" action="" enctype="multipart/form-data">
                    <div class="form-group">
                        <label>Brand Name</label>
                        <input type="text" name="name" id="name" required>
                    </div>
                    <div class="form-group">
                        <label>Slug</label>
                        <input type="text" name="slug" id="slug" required>
                        <small style="color:#646970; font-size:12px;">URL-friendly version.</small>
                    </div>
                    
                    <div class="form-group">
                        <label>Brand Image (Upload)</label>
                        <input type="file" name="image_file" accept="image/*">
                    </div>
                    
                    <div class="divider-text">OR</div>

                    <div class="form-group">
                        <label>Brand Image (URL)</label>
                        <input type="text" name="image_url" placeholder="https://example.com/image.jpg">
                    </div>

                    <div class="form-group">
                        <label>Description</label>
                        <textarea name="description" rows="4"></textarea>
                    </div>
                    <button type="submit" class="submit-btn">Add Brand</button>
                </form>
            </div>
        </main>
    </div>
</div>
<script src="../assets/js/admin.js"></script>
<script>
    // Auto-generate slug
    document.getElementById('name').addEventListener('keyup', function() {
        let name = this.value;
        let slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        document.getElementById('slug').value = slug;
    });
</script>
</body>
</html>
