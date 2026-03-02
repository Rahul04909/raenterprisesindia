<?php
header('Content-Type: application/json');
require_once __DIR__ . '/../database/db_config.php';

if ($_SERVER['REQUEST_METHOD'] !== 'POST') {
    echo json_encode(['success' => false, 'message' => 'Invalid request method.']);
    exit;
}

try {
    $name = trim($_POST['name'] ?? '');
    $email = trim($_POST['email'] ?? '');
    $mobile = trim($_POST['mobile'] ?? '');
    $company = trim($_POST['company_name'] ?? '');
    $address = trim($_POST['address'] ?? '');

    if (empty($name) || empty($email) || empty($mobile) || empty($address)) {
        echo json_encode(['success' => false, 'message' => 'Please fill all required fields.']);
        exit;
    }

    $attachment_path = null;
    
    // File Upload Handling
    if (isset($_FILES['attachment']) && $_FILES['attachment']['error'] === UPLOAD_ERR_OK) {
        $file = $_FILES['attachment'];
        $fileName = time() . '_' . basename($file['name']);
        $uploadDir = __DIR__ . '/../assets/uploads/bulk-quotes/';
        $targetPath = $uploadDir . $fileName;

        // Allowed types
        $allowedExtensions = ['pdf', 'xlsx', 'xls', 'csv', 'docx', 'doc', 'jpg', 'jpeg', 'png'];
        $fileExt = strtolower(pathinfo($fileName, PATHINFO_EXTENSION));

        if (!in_array($fileExt, $allowedExtensions)) {
            echo json_encode(['success' => false, 'message' => 'Invalid file type. Allowed: PDF, Excel, Word, Images, CSV.']);
            exit;
        }

        if ($file['size'] > 10 * 1024 * 1024) { // 10MB limit
            echo json_encode(['success' => false, 'message' => 'File size too large (Max 10MB).']);
            exit;
        }

        if (move_uploaded_file($file['tmp_name'], $targetPath)) {
            $attachment_path = 'assets/uploads/bulk-quotes/' . $fileName;
        } else {
            echo json_encode(['success' => false, 'message' => 'Failed to save attachment.']);
            exit;
        }
    }

    // Insert into Database
    $stmt = $pdo->prepare("INSERT INTO bulk_quotes (name, email, mobile, company_name, address, attachment_path) VALUES (?, ?, ?, ?, ?, ?)");
    if ($stmt->execute([$name, $email, $mobile, $company, $address, $attachment_path])) {
        echo json_encode(['success' => true]);
    } else {
        echo json_encode(['success' => false, 'message' => 'Database error. Please try again.']);
    }

} catch (Exception $e) {
    echo json_encode(['success' => false, 'message' => 'Server error: ' . $e->getMessage()]);
}
?>
