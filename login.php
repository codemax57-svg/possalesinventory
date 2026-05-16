<?php
/**
 * Login Page
 * Construction POS & Inventory System
 */

require_once 'config/database.php';
require_once 'config/constants.php';
require_once 'includes/functions.php';

// Start session
if (session_status() === PHP_SESSION_NONE) {
    session_start();
}

// If already logged in, redirect to dashboard
if (isLoggedIn()) {
    redirect(BASE_URL . '/index.php');
}

$error = '';
$success = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $username = sanitize($_POST['username'] ?? '');
    $password = $_POST['password'] ?? '';
    
    if (empty($username) || empty($password)) {
        $error = 'Username and password are required';
    } else {
        $db = new Database();
        $db->connect();
        
        $db->prepare('SELECT * FROM users WHERE username = ? AND status = "active"');
        $db->bind('s', $username);
        $db->execute();
        
        $user = $db->fetch();
        
        if ($user && password_verify($password, $user['password'])) {
            // Login successful
            $_SESSION['user_id'] = $user['id'];
            $_SESSION['username'] = $user['username'];
            $_SESSION['role'] = $user['role'];
            $_SESSION['last_activity'] = time();
            
            // Log activity
            logActivity('Login', 'Authentication', $user['id']);
            
            redirect(BASE_URL . '/index.php');
        } else {
            $error = 'Invalid username or password';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Login - <?php echo APP_NAME; ?></title>
    <link href="<?php echo ASSET_URL; ?>/css/bootstrap.min.css" rel="stylesheet">
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.4.0/css/all.min.css">
    <style>
        :root {
            --primary-color: #0d6efd;
            --secondary-color: #6c757d;
        }
        
        * {
            margin: 0;
            padding: 0;
            box-sizing: border-box;
        }
        
        body {
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
        }
        
        .login-container {
            width: 100%;
            max-width: 450px;
            padding: 20px;
        }
        
        .login-card {
            background: white;
            border-radius: 10px;
            box-shadow: 0 10px 40px rgba(0, 0, 0, 0.2);
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            padding: 2rem;
            text-align: center;
        }
        
        .login-header h1 {
            font-size: 1.8rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-header p {
            font-size: 0.95rem;
            opacity: 0.9;
            margin: 0;
        }
        
        .login-body {
            padding: 2.5rem;
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 600;
            color: #333;
        }
        
        .form-group input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: 5px;
            font-size: 1rem;
            transition: all 0.3s ease;
        }
        
        .form-group input:focus {
            outline: none;
            border-color: #0d6efd;
            box-shadow: 0 0 0 0.2rem rgba(13, 110, 253, 0.25);
        }
        
        .form-group input.is-invalid {
            border-color: #dc3545;
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            margin-bottom: 1.5rem;
            font-size: 0.95rem;
        }
        
        .remember-me input {
            margin-right: 0.5rem;
            cursor: pointer;
        }
        
        .login-button {
            width: 100%;
            padding: 0.875rem;
            background: linear-gradient(135deg, #0d6efd 0%, #0a58ca 100%);
            color: white;
            border: none;
            border-radius: 5px;
            font-size: 1rem;
            font-weight: 600;
            cursor: pointer;
            transition: all 0.3s ease;
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .login-button:hover {
            transform: translateY(-2px);
            box-shadow: 0 5px 20px rgba(13, 110, 253, 0.4);
        }
        
        .error-message {
            background-color: #f8d7da;
            color: #721c24;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            border: 1px solid #f5c6cb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .success-message {
            background-color: #d4edda;
            color: #155724;
            padding: 0.75rem 1rem;
            border-radius: 5px;
            margin-bottom: 1.5rem;
            border: 1px solid #c3e6cb;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .demo-credentials {
            background-color: #e7f3ff;
            border-left: 4px solid #0d6efd;
            padding: 1rem;
            border-radius: 5px;
            margin-top: 2rem;
            font-size: 0.9rem;
        }
        
        .demo-credentials h6 {
            margin-top: 0;
            color: #0d6efd;
            font-weight: 600;
        }
        
        .demo-credentials p {
            margin: 0.5rem 0;
            color: #333;
        }
        
        .demo-credentials code {
            background-color: white;
            padding: 0.2rem 0.4rem;
            border-radius: 3px;
            font-size: 0.85rem;
        }
        
        @media (max-width: 480px) {
            .login-container {
                padding: 10px;
            }
            
            .login-body {
                padding: 1.5rem;
            }
            
            .login-header {
                padding: 1.5rem;
            }
            
            .login-header h1 {
                font-size: 1.4rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-card">
            <div class="login-header">
                <h1><i class="fas fa-building"></i></h1>
                <h1><?php echo APP_NAME; ?></h1>
                <p>Construction Supply Management System</p>
            </div>
            
            <div class="login-body">
                <?php if (!empty($error)): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-circle"></i>
                        <span><?php echo htmlspecialchars($error); ?></span>
                    </div>
                <?php endif; ?>
                
                <?php if (!empty($success)): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <span><?php echo htmlspecialchars($success); ?></span>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username"><i class="fas fa-user"></i> Username</label>
                        <input type="text" id="username" name="username" placeholder="Enter your username" required autofocus>
                    </div>
                    
                    <div class="form-group">
                        <label for="password"><i class="fas fa-lock"></i> Password</label>
                        <input type="password" id="password" name="password" placeholder="Enter your password" required>
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember" style="margin: 0; cursor: pointer;">Remember me</label>
                    </div>
                    
                    <button type="submit" class="login-button">
                        <i class="fas fa-sign-in-alt"></i> Login
                    </button>
                </form>
                
                <div class="demo-credentials">
                    <h6><i class="fas fa-info-circle"></i> Demo Credentials</h6>
                    <p><strong>Admin:</strong><br>Username: <code>admin</code><br>Password: <code>admin123</code></p>
                    <p><strong>Cashier:</strong><br>Username: <code>cashier</code><br>Password: <code>cashier123</code></p>
                </div>
            </div>
        </div>
    </div>
    
    <script src="<?php echo ASSET_URL; ?>/js/bootstrap.bundle.min.js"></script>
</body>
</html>