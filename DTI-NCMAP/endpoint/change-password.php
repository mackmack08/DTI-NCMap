<?php
session_start();
include("../conn/conn.php");

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

$success_message = '';
$error_message = '';

if ($_SERVER['REQUEST_METHOD'] === 'POST') {
    $current_password = $_POST['current_password'];
    $new_password = $_POST['new_password'];
    $confirm_password = $_POST['confirm_password'];
    
    // Validation
    if (empty($current_password) || empty($new_password) || empty($confirm_password)) {
        $error_message = 'All fields are required.';
    } elseif ($new_password !== $confirm_password) {
        $error_message = 'New passwords do not match.';
    } elseif (strlen($new_password) < 6) {
        $error_message = 'New password must be at least 6 characters long.';
    } else {
        try {
            // Get current user data
            $stmt = $conn->prepare("SELECT password_hash FROM admin_users WHERE user_id = ?");
            $stmt->execute([$_SESSION['admin_user_id']]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($current_password, $user['password_hash'])) {
                // Update password
                $new_password_hash = password_hash($new_password, PASSWORD_DEFAULT);
                $stmt = $conn->prepare("UPDATE admin_users SET password_hash = ?, updated_at = NOW() WHERE user_id = ?");
                $stmt->execute([$new_password_hash, $_SESSION['admin_user_id']]);
                
                $success_message = 'Password changed successfully!';
            } else {
                $error_message = 'Current password is incorrect.';
            }
        } catch (Exception $e) {
            $error_message = 'Error changing password. Please try again.';
        }
    }
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Change Password - DTI NC Locator Admin</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
    <style>
        .password-form {
            max-width: 500px;
            margin: 0 auto;
            background: var(--white);
            padding: 2rem;
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-light);
        }
        
        .form-group {
            margin-bottom: 1.5rem;
        }
        
        .form-group label {
            display: block;
            margin-bottom: 0.5rem;
            font-weight: 500;
            color: var(--text-dark);
        }
        
        .form-input {
            width: 100%;
            padding: 0.75rem;
            border: 1px solid #ddd;
            border-radius: var(--border-radius-sm);
            font-size: 1rem;
            transition: var(--transition-normal);
        }
        
        .form-input:focus {
            outline: none;
            border-color: var(--primary-color);
            box-shadow: 0 0 0 2px rgba(13, 71, 161, 0.1);
        }
        
        .password-requirements {
            background: var(--background-light);
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            margin-bottom: 1.5rem;
            font-size: 0.9rem;
            color: var(--text-medium);
        }
        
        .password-requirements h4 {
            color: var(--text-dark);
            margin-bottom: 0.5rem;
        }
        
        .password-requirements ul {
            margin: 0;
            padding-left: 1.2rem;
        }
        
        .success-message {
            background: #e8f5e8;
            color: #2e7d32;
            padding: 0.75rem;
            border-radius: var(--border-radius-sm);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .error-message {
            background: #ffebee;
            color: #c62828;
            padding: 0.75rem;
            border-radius: var(--border-radius-sm);
            margin-bottom: 1rem;
            display: flex;
            align-items: center;
            gap: 0.5rem;
        }
        
        .form-actions {
            display: flex;
            gap: 1rem;
            justify-content: flex-end;
        }
        
        .btn-secondary {
            background: #6c757d;
            color: var(--white);
        }
        
        .btn-secondary:hover {
            background: #5a6268;
        }
    </style>
</head>
<body>
    <div class="admin-container">
        <!-- Include Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1>Change Password</h1>
                <p>Update your account password</p>
            </div>
            
            <div class="password-form">
                <?php if ($success_message): ?>
                    <div class="success-message">
                        <i class="fas fa-check-circle"></i>
                        <?php echo htmlspecialchars($success_message); ?>
                    </div>
                <?php endif; ?>
                
                <?php if ($error_message): ?>
                    <div class="error-message">
                        <i class="fas fa-exclamation-triangle"></i>
                        <?php echo htmlspecialchars($error_message); ?>
                    </div>
                <?php endif; ?>
                
                <div class="password-requirements">
                    <h4>Password Requirements:</h4>
                    <ul>
                        <li>At least 6 characters long</li>
                        <li>Mix of letters and numbers recommended</li>
                        <li>Avoid using common passwords</li>
                    </ul>
                </div>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="current_password">Current Password</label>
                        <input type="password" id="current_password" name="current_password" 
                               class="form-input" placeholder="Enter your current password" required>
                    </div>
                    
                    <div class="form-group">
                        <label for="new_password">New Password</label>
                        <input type="password" id="new_password" name="new_password" 
                               class="form-input" placeholder="Enter your new password" required minlength="6">
                    </div>
                    
                    <div class="form-group">
                        <label for="confirm_password">Confirm New Password</label>
                        <input type="password" id="confirm_password" name="confirm_password" 
                               class="form-input" placeholder="Confirm your new password" required minlength="6">
                    </div>
                    
                    <div class="form-actions">
                        <a href="dashboard.php" class="btn btn-secondary">
                            <i class="fas fa-times"></i>
                            Cancel
                        </a>
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Change Password
                        </button>
                    </div>
                </form>
            </div>
        </main>
    </div>
    
    <script>
        // Password confirmation validation
        document.getElementById('confirm_password').addEventListener('input', function() {
            const newPassword = document.getElementById('new_password').value;
            const confirmPassword = this.value;
            
            if (newPassword !== confirmPassword) {
                this.setCustomValidity('Passwords do not match');
            } else {
                this.setCustomValidity('');
            }
        });
        
        document.getElementById('new_password').addEventListener('input', function() {
            const confirmPassword = document.getElementById('confirm_password');
            if (confirmPassword.value) {
                confirmPassword.dispatchEvent(new Event('input'));
            }
        });
    </script>
</body>
</html>
