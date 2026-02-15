<?php
$adminBase = '..';
include '../includes/check_login.php';
require_once '../../database/db_config.php';

// Fetch Dropdown Data
$categories = $pdo->query("SELECT id, name FROM categories ORDER BY name ASC")->fetchAll();
$brands = $pdo->query("SELECT id, name FROM brands ORDER BY name ASC")->fetchAll();
$brand_categories = $pdo->query("SELECT id, name, brand_id FROM brand_categories ORDER BY name ASC")->fetchAll();

$success = '';
$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    try {
        // Collect Data
        $name = trim($_POST['name']);
        $slug = trim($_POST['slug']);
        $category_id = !empty($_POST['category_id']) ? $_POST['category_id'] : null;
        $brand_id = !empty($_POST['brand_id']) ? $_POST['brand_id'] : null;
        $brand_category_id = !empty($_POST['brand_category_id']) ? $_POST['brand_category_id'] : null;
        $short_desc = $_POST['short_description'];
        $description = $_POST['description'];
        
        $is_price_enabled = isset($_POST['is_price_enabled']) ? 1 : 0;
        $mrp = $is_price_enabled ? $_POST['mrp'] : null;
        $sales_price = $is_price_enabled ? $_POST['sales_price'] : null;
        $stock = $is_price_enabled ? $_POST['stock'] : 0;
        
        $sku = $_POST['sku'];
        $hsn = $_POST['hsn'];
        $model_number = $_POST['model_number'];
        $specifications = $_POST['specifications'];
        
        $meta_title = $_POST['meta_title'];
        $meta_desc = $_POST['meta_description'];
        $meta_kw = $_POST['meta_keywords'];
        $schema = $_POST['schema_markup'];
        $og_title = $_POST['og_title'];
        $og_desc = $_POST['og_description'];
        $status = $_POST['status'];

        // File Uploads
        $featured_image = '';
        if (!empty($_POST['featured_image_url'])) {
            $featured_image = $_POST['featured_image_url'];
        } elseif (isset($_FILES['featured_image_file']) && $_FILES['featured_image_file']['error'] == 0) {
            $ext = pathinfo($_FILES['featured_image_file']['name'], PATHINFO_EXTENSION);
            $newName = 'prod-' . time() . '.' . $ext;
            move_uploaded_file($_FILES['featured_image_file']['tmp_name'], '../../assets/uploads/products/' . $newName);
            $featured_image = 'assets/uploads/products/' . $newName;
        }

        $brochure_path = '';
        if (isset($_FILES['brochure_file']) && $_FILES['brochure_file']['error'] == 0) {
             $ext = pathinfo($_FILES['brochure_file']['name'], PATHINFO_EXTENSION);
             $newName = 'brochure-' . time() . '.' . $ext;
             move_uploaded_file($_FILES['brochure_file']['tmp_name'], '../../assets/uploads/brochures/' . $newName);
             $brochure_path = 'assets/uploads/brochures/' . $newName;
        }

        // Insert Product
        $sql = "INSERT INTO products (
            name, slug, category_id, brand_id, brand_category_id, 
            short_description, description, is_price_enabled, mrp, sales_price, stock,
            sku, hsn, model_number, specifications, brochure_path, featured_image,
            meta_title, meta_description, meta_keywords, schema_markup, 
            og_title, og_description, status
        ) VALUES (?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?, ?)";
        
        $stmt = $pdo->prepare($sql);
        $stmt->execute([
            $name, $slug, $category_id, $brand_id, $brand_category_id,
            $short_desc, $description, $is_price_enabled, $mrp, $sales_price, $stock,
            $sku, $hsn, $model_number, $specifications, $brochure_path, $featured_image,
            $meta_title, $meta_desc, $meta_kw, $schema,
            $og_title, $og_desc, $status
        ]);
        
        $product_id = $pdo->lastInsertId();

        // Handle Gallery Images
        // 1. URLs
        if (!empty($_POST['gallery_image_urls'])) {
            $urls = explode("\n", $_POST['gallery_image_urls']);
            foreach ($urls as $url) {
                $url = trim($url);
                if ($url) {
                    $pdo->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)")->execute([$product_id, $url]);
                }
            }
        }
        // 2. Files
        if (isset($_FILES['gallery_image_files'])) {
            $files = $_FILES['gallery_image_files'];
            for ($i = 0; $i < count($files['name']); $i++) {
                if ($files['error'][$i] == 0) {
                     $ext = pathinfo($files['name'][$i], PATHINFO_EXTENSION);
                     $newName = 'gallery-' . $product_id . '-' . $i . '-' . time() . '.' . $ext;
                     move_uploaded_file($files['tmp_name'][$i], '../../assets/uploads/products/' . $newName);
                     $path = 'assets/uploads/products/' . $newName;
                     $pdo->prepare("INSERT INTO product_images (product_id, image_path) VALUES (?, ?)")->execute([$product_id, $path]);
                }
            }
        }

        $success = "Product added successfully!";

    } catch (PDOException $e) {
        $error = "Database Error: " . $e->getMessage();
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Add Product &lsaquo; RA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    
    <!-- Summernote CSS -->
    <link href="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.css" rel="stylesheet">
    
    <link rel="stylesheet" href="../assets/css/admin.css">
    <style>
        .form-layout { display: flex; gap: 20px; }
        .form-main { flex: 2; }
        .form-sidebar { flex: 1; }
        
        .card { background: #fff; border: 1px solid #c3c4c7; padding: 20px; margin-bottom: 20px; }
        .card h3 { margin-top: 0; margin-bottom: 15px; border-bottom: 1px solid #eee; padding-bottom: 10px; font-size: 16px; }
        
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 600; font-size: 13px; }
        .form-group input[type="text"], .form-group input[type="number"], .form-group select, .form-group textarea {
            width: 100%; padding: 8px; border: 1px solid #8c8f94; border-radius: 4px; box-sizing: border-box;
        }
        
        .tabs { border-bottom: 1px solid #c3c4c7; margin-bottom: 20px; }
        .tab-btn {
            background: none; border: none; padding: 10px 20px; cursor: pointer; font-weight: 600; color: #50575e; border-bottom: 3px solid transparent;
        }
        .tab-btn.active { color: #2271b1; border-bottom-color: #2271b1; }
        .tab-content { display: none; }
        .tab-content.active { display: block; }
        
        .toggle-switch { display: flex; align-items: center; gap: 10px; }
        
        .image-preview-container { display: flex; flex-wrap: wrap; gap: 10px; margin-top: 10px; }
        .preview-img { width: 80px; height: 80px; object-fit: cover; border: 1px solid #ddd; }
        
        /* Specific Pricing Logic */
        #pricing_fields { display: none; background: #f9f9f9; padding: 15px; border: 1px solid #eee; margin-top: 10px; }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include '../includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include '../includes/header.php'; ?>
        <main class="admin-main">
            <h1 class="page-title">Add New Product</h1>
            
            <?php if ($error): ?><div style="background:white; border-left:4px solid red; padding:15px; margin-bottom:20px;"><?php echo $error; ?></div><?php endif; ?>
            <?php if ($success): ?><div style="background:white; border-left:4px solid green; padding:15px; margin-bottom:20px;"><?php echo $success; ?> <a href="index.php">View All</a></div><?php endif; ?>

            <form method="POST" action="" enctype="multipart/form-data">
                
                <div class="form-layout">
                    <!-- Main Column -->
                    <div class="form-main">
                        <div class="card">
                           <div class="form-group">
                                <label>Product Name</label>
                                <input type="text" name="name" id="name" required style="font-size: 1.2em; padding: 10px;">
                           </div>
                           <div class="form-group">
                                <label>Permalink (Slug)</label>
                                <input type="text" name="slug" id="slug" required style="background: #f0f0f1; color: #666;">
                           </div>
                        </div>

                        <!-- Tabs -->
                        <div class="tabs">
                            <button type="button" class="tab-btn active" onclick="openTab(event, 'tab-general')">General</button>
                            <button type="button" class="tab-btn" onclick="openTab(event, 'tab-spec')">Specifications</button>
                            <button type="button" class="tab-btn" onclick="openTab(event, 'tab-seo')">SEO & Schema</button>
                        </div>

                        <!-- General Tab -->
                        <div id="tab-general" class="tab-content active">
                            <div class="card">
                                <div class="form-group">
                                    <label>Short Description</label>
                                    <textarea name="short_description" rows="3"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Full Description</label>
                                    <textarea name="description" id="summernote"></textarea>
                                </div>
                            </div>
                        </div>

                        <!-- Specifications Tab -->
                        <div id="tab-spec" class="tab-content">
                            <div class="card">
                                <h3>Technical Details</h3>
                                <div class="form-group">
                                    <label>SKU</label>
                                    <input type="text" name="sku">
                                </div>
                                <div class="form-group">
                                    <label>Model Number</label>
                                    <input type="text" name="model_number">
                                </div>
                                <div class="form-group">
                                    <label>HSN Code</label>
                                    <input type="text" name="hsn">
                                </div>
                                <div class="form-group">
                                    <label>Specifications (Table/HTML)</label>
                                    <textarea name="specifications" id="summernote_specs"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Brochure (PDF)</label>
                                    <input type="file" name="brochure_file" accept=".pdf">
                                </div>
                            </div>
                        </div>

                        <!-- SEO Tab -->
                        <div id="tab-seo" class="tab-content">
                            <div class="card">
                                <h3>Search Engine Optimization</h3>
                                <div class="form-group">
                                    <label>Meta Title</label>
                                    <input type="text" name="meta_title" id="meta_title">
                                    <small><button type="button" onclick="generateSEO()">Auto-Generate</button></small>
                                </div>
                                <div class="form-group">
                                    <label>Meta Description</label>
                                    <textarea name="meta_description" id="meta_description" rows="2"></textarea>
                                </div>
                                <div class="form-group">
                                    <label>Meta Keywords</label>
                                    <input type="text" name="meta_keywords">
                                </div>
                                <div class="form-group">
                                    <label>Schema Markup (JSON-LD)</label>
                                    <textarea name="schema_markup" id="schema_markup" rows="8" style="font-family: monospace; font-size: 12px;"></textarea>
                                </div>
                            </div>
                            <div class="card">
                                <h3>Open Graph (Social Sharing)</h3>
                                <div class="form-group">
                                    <label>OG Title</label>
                                    <input type="text" name="og_title">
                                </div>
                                <div class="form-group">
                                    <label>OG Description</label>
                                    <textarea name="og_description" rows="2"></textarea>
                                </div>
                            </div>
                        </div>

                    </div>

                    <!-- Sidebar -->
                    <div class="form-sidebar">
                        
                        <!-- Publish -->
                        <div class="card">
                            <h3>Publish</h3>
                            <div class="form-group">
                                <label>Status</label>
                                <select name="status">
                                    <option value="published">Published</option>
                                    <option value="draft">Draft</option>
                                </select>
                            </div>
                            <div style="text-align: right; margin-top: 10px;">
                                <button type="submit" class="button button-primary" style="background: #2271b1; color: white; padding: 10px 20px; width: 100%;">Save Product</button>
                            </div>
                        </div>

                        <!-- Categorization -->
                        <div class="card">
                            <h3>Organization</h3>
                            <div class="form-group">
                                <label>Category</label>
                                <select name="category_id">
                                    <option value="">Select Category</option>
                                    <?php foreach ($categories as $c): ?>
                                        <option value="<?php echo $c['id']; ?>"><?php echo htmlspecialchars($c['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Brand</label>
                                <select name="brand_id" id="brand_select" onchange="filterBrandCategories()">
                                    <option value="">Select Brand</option>
                                    <?php foreach ($brands as $b): ?>
                                        <option value="<?php echo $b['id']; ?>"><?php echo htmlspecialchars($b['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                            <div class="form-group">
                                <label>Brand Category</label>
                                <select name="brand_category_id" id="brand_cat_select">
                                    <option value="">Select Brand Category</option>
                                    <?php foreach ($brand_categories as $bc): ?>
                                        <option value="<?php echo $bc['id']; ?>" data-brand="<?php echo $bc['brand_id']; ?>"><?php echo htmlspecialchars($bc['name']); ?></option>
                                    <?php endforeach; ?>
                                </select>
                            </div>
                        </div>

                        <!-- Pricing -->
                        <div class="card">
                            <h3>Pricing & Stock</h3>
                            <div class="toggle-switch">
                                <input type="checkbox" name="is_price_enabled" id="is_price_enabled" onchange="togglePricing()">
                                <label for="is_price_enabled">Enable Price/Stock Management</label>
                            </div>
                            <div id="pricing_fields">
                                <div class="form-group">
                                    <label>MRP</label>
                                    <input type="number" name="mrp" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Sales Price</label>
                                    <input type="number" name="sales_price" step="0.01">
                                </div>
                                <div class="form-group">
                                    <label>Stock Quantity</label>
                                    <input type="number" name="stock" value="0">
                                </div>
                            </div>
                            <p id="price_msg" style="color: #666; font-size: 13px; margin-top: 10px;">Product will act as "Price on Request" if disabled.</p>
                        </div>

                        <!-- Media -->
                        <div class="card">
                            <h3>Product Images</h3>
                            
                            <div class="form-group">
                                <label>Featured Image</label>
                                <input type="file" name="featured_image_file" accept="image/*" onchange="previewImage(this, 'featured_preview')">
                                <div class="divider-text" style="text-align: center; margin: 5px 0; color: #ccc;">OR</div>
                                <input type="text" name="featured_image_url" placeholder="Enter URL" onchange="document.getElementById('featured_preview').src = this.value">
                                <img id="featured_preview" class="preview-img" style="display: block; margin-top: 5px; width: 100%; height: auto;">
                            </div>
                            
                            <hr>
                            
                            <div class="form-group">
                                <label>Gallery Images</label>
                                <input type="file" name="gallery_image_files[]" multiple accept="image/*">
                                <div class="divider-text" style="text-align: center; margin: 5px 0; color: #ccc;">OR</div>
                                <textarea name="gallery_image_urls" placeholder="One URL per line" rows="3"></textarea>
                            </div>
                        </div>

                    </div>
                </div>

            </form>
        </main>
    </div>
</div>

<!-- Scripts -->
<script src="https://code.jquery.com/jquery-3.6.0.min.js"></script>
<script src="https://cdn.jsdelivr.net/npm/summernote@0.8.18/dist/summernote-lite.min.js"></script>
<script src="../assets/js/admin.js"></script>

<script>
    // Initialize Summernote
    $(document).ready(function() {
        $('#summernote').summernote({
            height: 300,
            toolbar: [
                ['style', ['style']],
                ['font', ['bold', 'underline', 'clear']],
                ['color', ['color']],
                ['para', ['ul', 'ol', 'paragraph']],
                ['table', ['table']],
                ['insert', ['link', 'picture', 'video']],
                ['view', ['fullscreen', 'codeview', 'help']]
            ]
        });
        $('#summernote_specs').summernote({
             height: 200,
             toolbar: [ ['table', ['table']], ['view', ['codeview']] ]
        });
    });

    // Tab Switching
    function openTab(evt, tabName) {
        var i, tabcontent, tablinks;
        tabcontent = document.getElementsByClassName("tab-content");
        for (i = 0; i < tabcontent.length; i++) {
            tabcontent[i].style.display = "none";
            tabcontent[i].classList.remove("active");
        }
        tablinks = document.getElementsByClassName("tab-btn");
        for (i = 0; i < tablinks.length; i++) {
            tablinks[i].className = tablinks[i].className.replace(" active", "");
        }
        document.getElementById(tabName).style.display = "block";
        document.getElementById(tabName).classList.add("active");
        evt.currentTarget.className += " active";
    }

    // Auto Slug
    document.getElementById('name').addEventListener('keyup', function() {
        let name = this.value;
        let slug = name.toLowerCase().replace(/[^a-z0-9]+/g, '-').replace(/(^-|-$)+/g, '');
        document.getElementById('slug').value = slug;
    });

    // Pricing Toggle
    function togglePricing() {
        var isChecked = document.getElementById('is_price_enabled').checked;
        var fields = document.getElementById('pricing_fields');
        var msg = document.getElementById('price_msg');
        
        if (isChecked) {
            fields.style.display = 'block';
            msg.style.display = 'none';
        } else {
            fields.style.display = 'none';
            msg.style.display = 'block';
        }
    }

    // Image Preview
    function previewImage(input, imgId) {
        if (input.files && input.files[0]) {
            var reader = new FileReader();
            reader.onload = function(e) {
                document.getElementById(imgId).src = e.target.result;
            }
            reader.readAsDataURL(input.files[0]);
        }
    }

    // Filter Brand Categories
    function filterBrandCategories() {
        var brandId = document.getElementById('brand_select').value;
        var options = document.getElementById('brand_cat_select').options;
        
        for (var i = 0; i < options.length; i++) {
            if (options[i].value === "") continue; 
            
            var categoryBrand = options[i].getAttribute('data-brand');
            if (brandId === "" || categoryBrand === brandId) {
                options[i].style.display = "block";
            } else {
                options[i].style.display = "none";
            }
        }
    }

    // Auto Generate SEO
    function generateSEO() {
        var name = document.getElementById('name').value;
        var desc = $('textarea[name="short_description"]').val();
        
        document.getElementById('meta_title').value = name + " - RA Enterprises India";
        document.getElementById('meta_description').value = desc.substring(0, 160);
        document.getElementById('og_title').value = name;
        document.getElementById('og_description').value = desc.substring(0, 200);

        // Simple JSON-LD Schema
        var schema = {
            "@context": "https://schema.org/",
            "@type": "Product",
            "name": name,
            "description": desc
        };
        document.getElementById('schema_markup').value = JSON.stringify(schema, null, 2);
    }
</script>
</body>
</html>
