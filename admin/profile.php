<?php
$adminBase = '.';
include 'includes/check_login.php';
require_once '../database/db_config.php';

$error = '';
$success = '';

$adminId = $_SESSION['admin_id'] ?? 1; // Fallback to 1 if session id isn't set, though check_login should ensure it

// Fetch current admin details
try {
    $stmt = $pdo->prepare("SELECT * FROM admins WHERE id = ?");
    $stmt->execute([$adminId]);
    $admin = $stmt->fetch();
    
    if (!$admin) {
        die("Admin not found.");
    }
} catch (PDOException $e) {
    die("Error: " . $e->getMessage());
}

// Handle Profile Update
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['update_profile'])) {
    $fullName = trim($_POST['full_name']);
    $email = trim($_POST['email']);
    $username = trim($_POST['username']);

    if (empty($username)) {
        $error = "Username is required.";
    } else {
        try {
            // Check for duplicate username (excluding current)
            $stmt = $pdo->prepare("SELECT COUNT(*) FROM admins WHERE username = ? AND id != ?");
            $stmt->execute([$username, $adminId]);
            if ($stmt->fetchColumn() > 0) {
                $error = "Username already taken.";
            } else {
                $stmt = $pdo->prepare("UPDATE admins SET username = ?, full_name = ?, email = ? WHERE id = ?");
                if ($stmt->execute([$username, $fullName, $email, $adminId])) {
                    $success = "Profile updated successfully.";
                    // Update session if username changed
                    $_SESSION['admin_username'] = $username;
                    // Refresh admin data
                    $admin['username'] = $username;
                    $admin['full_name'] = $fullName;
                    $admin['email'] = $email;
                } else {
                    $error = "Failed to update profile.";
                }
            }
        } catch (PDOException $e) {
            $error = "Error: " . $e->getMessage();
        }
    }
}

// Handle Password Change
if ($_SERVER["REQUEST_METHOD"] == "POST" && isset($_POST['change_password'])) {
    $currentPass = $_POST['current_password'];
    $newPass = $_POST['new_password'];
    $confirmPass = $_POST['confirm_password'];

    if (empty($currentPass) || empty($newPass) || empty($confirmPass)) {
        $error = "All password fields are required.";
    } elseif ($newPass !== $confirmPass) {
        $error = "New passwords do not match.";
    } elseif (strlen($newPass) < 6) {
        $error = "New password must be at least 6 characters.";
    } else {
        try {
            // Verify current password
            if (password_verify($currentPass, $admin['password'])) {
                $hashedPass = password_hash($newPass, PASSWORD_DEFAULT);
                $stmt = $pdo->prepare("UPDATE admins SET password = ? WHERE id = ?");
                if ($stmt->execute([$hashedPass, $adminId])) {
                    $success = "Password changed successfully.";
                } else {
                    $error = "Failed to change password.";
                }
            } else {
                $error = "Incorrect current password.";
            }
        } catch (PDOException $e) {
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
    <title>Admin Profile &lsaquo; RA Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        .profile-wrapper {
            display: grid;
            grid-template-columns: 1fr 1fr;
            gap: 20px;
        }
        .form-container {
            background: #fff;
            padding: 20px;
            border: 1px solid #c3c4c7;
        }
        .form-group { margin-bottom: 15px; }
        .form-group label { display: block; margin-bottom: 5px; font-weight: 500; font-size: 14px; }
        .form-group input {
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
            font-weight: 500;
        }
        .submit-btn:hover { background-color: #135e96; }
        .alert { padding: 10px; margin-bottom: 15px; border-left: 4px solid; }
        .alert-error { background: #fff; border-color: #d63638; }
        .alert-success { background: #fff; border-color: #00a32a; }
        
        @media (max-width: 768px) {
            .profile-wrapper {
                grid-template-columns: 1fr;
            }
        }
    </style>
</head>
<body>
<div class="admin-wrapper">
    <?php include 'includes/sidebar.php'; ?>
    <div class="admin-content">
        <?php include 'includes/header.php'; ?>
        <main class="admin-main">
            <h1 class="page-title">Admin Profile</h1>
            
            <?php if($error): ?><div class="alert alert-error"><?php echo $error; ?></div><?php endif; ?>
            <?php if($success): ?><div class="alert alert-success"><?php echo $success; ?></div><?php endif; ?>

            <div class="profile-wrapper">
                <!-- Profile Information -->
                <div class="form-container">
                    <h2 style="font-size: 18px; margin-top: 0; margin-bottom: 20px;">Profile Details</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Username</label>
                            <input type="text" name="username" value="<?php echo htmlspecialchars($admin['username']); ?>" required>
                        </div>
                        <div class="form-group">
                            <label>Full Name</label>
                            <input type="text" name="full_name" value="<?php echo htmlspecialchars($admin['full_name']); ?>">
                        </div>
                        <div class="form-group">
                            <label>Email Address</label>
                            <input type="email" name="email" value="<?php echo htmlspecialchars($admin['email']); ?>">
                        </div>
                        <button type="submit" name="update_profile" class="submit-btn">Update Profile</button>
                    </form>
                </div>

                <!-- Change Password -->
                <div class="form-container">
                    <h2 style="font-size: 18px; margin-top: 0; margin-bottom: 20px;">Change Password</h2>
                    <form method="POST" action="">
                        <div class="form-group">
                            <label>Current Password</label>
                            <input type="password" name="current_password" required>
                        </div>
                        <div class="form-group">
                            <label>New Password</label>
                            <input type="password" name="new_password" required>
                        </div>
                        <div class="form-group">
                            <label>Confirm New Password</label>
                            <input type="password" name="confirm_password" required>
                        </div>
                        <button type="submit" name="change_password" class="submit-btn">Update Password</button>
                    </form>
                </div>
            </div>
        </main>
    </div>
</div>
<script src="assets/js/admin.js"></script>
</body>
</html>
