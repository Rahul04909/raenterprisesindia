<?php
session_start();
require_once '../database/db_config.php';

// If already logged in, redirect to dashboard
if (isset($_SESSION['admin_logged_in']) && $_SESSION['admin_logged_in'] === true) {
    header("Location: index.php");
    exit;
}

$error = '';

if ($_SERVER["REQUEST_METHOD"] == "POST") {
    $username = trim($_POST['username']);
    $password = trim($_POST['password']);

    if (empty($username) || empty($password)) {
        $error = "Please enter both username and password.";
    } else {
        try {
            $stmt = $pdo->prepare("SELECT id, username, password FROM admins WHERE username = ?");
            $stmt->execute([$username]);
            $user = $stmt->fetch();

            if ($user && password_verify($password, $user['password'])) {
                // Password is correct, start session
                $_SESSION['admin_logged_in'] = true;
                $_SESSION['admin_id'] = $user['id'];
                $_SESSION['admin_username'] = $user['username'];
                
                header("Location: index.php");
                exit;
            } else {
                $error = "Invalid username or password.";
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
    <title>Log In &lsaquo; RA Enterprises Admin</title>
    <link rel="stylesheet" href="assets/css/admin.css">
    <style>
        body {
            background-color: #f0f0f1;
            display: flex;
            align-items: center;
            justify-content: center;
            height: 100vh;
        }
        .login-card {
            background: #fff;
            padding: 24px;
            width: 320px;
            border: 1px solid #c3c4c7;
            box-shadow: 0 1px 3px rgba(0,0,0,0.04);
        }
        .login-logo {
            text-align: center;
            margin-bottom: 24px;
            font-size: 20px;
            font-weight: 600;
            color: #3c434a;
        }
        .form-group {
            margin-bottom: 16px;
        }
        .form-group label {
            display: block;
            margin-bottom: 5px;
            font-size: 14px;
            color: #3c434a;
        }
        .form-group input {
            width: 100%;
            padding: 6px 15px; /* Added horizontal padding */
            border: 1px solid #8c8f94;
            border-radius: 4px;
            font-size: 16px; /* Reduced font size slightly */
            box-sizing: border-box; /* Ensure padding doesn't affect width */
            height: 40px; /* Fixed height for better touch targets */
        }
        .login-btn {
            background: #2271b1;
            color: #fff;
            border: none;
            padding: 6px 12px;
            font-size: 13px;
            border-radius: 3px;
            cursor: pointer;
            width: 100%;
            height: 35px;
            font-weight: 500;
        }
        .login-btn:hover {
            background: #135e96;
        }
        .error-msg {
            background: #fff;
            border-left: 4px solid #d63638;
            padding: 12px;
            box-shadow: 0 1px 1px 0 rgba(0,0,0,.1);
            margin-bottom: 20px;
            font-size: 13px;
            width: 320px;
            box-sizing: border-box;
        }
    </style>
</head>
<body>

<div class="login-wrapper">
    <div class="login-logo">
        RA Admin
    </div>

    <?php if($error): ?>
        <div class="error-msg"><?php echo $error; ?></div>
    <?php endif; ?>

    <div class="login-card">
        <form method="POST" action="">
            <div class="form-group">
                <label for="username">Username or Email Address</label>
                <input type="text" id="username" name="username" required autofocus>
            </div>
            <div class="form-group">
                <label for="password">Password</label>
                <input type="password" id="password" name="password" required>
            </div>
            <button type="submit" class="login-btn">Log In</button>
        </form>
    </div>
    
    <p style="text-align: center; margin-top: 20px; font-size: 13px;">
        <a href="../index.php" style="color: #50575e; text-decoration: none;">&larr; Go to RA Enterprises</a>
    </p>
</div>

</body>
</html>
