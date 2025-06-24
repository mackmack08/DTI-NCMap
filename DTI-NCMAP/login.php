<?php
session_start();
include("conn/conn.php");

$error_message = '';
$success_message = '';
$reset_message = '';

// Handle login
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['login'])) {
    $username = trim($_POST['username']);
    $password = $_POST['password'];
    
    if (!empty($username) && !empty($password)) {
        try {
            $stmt = $conn->prepare("SELECT user_id, username, password_hash, full_name, email, role, is_active FROM admin_users WHERE username = ? AND is_active = TRUE");
            $stmt->execute([$username]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user && password_verify($password, $user['password_hash'])) {
                $_SESSION['user_id'] = $user['user_id'];
                $_SESSION['username'] = $user['username'];
                $_SESSION['full_name'] = $user['full_name'];
                $_SESSION['role'] = $user['role'];
                $_SESSION['email'] = $user['email'];
                
                // Update last login
                $stmt = $conn->prepare("UPDATE admin_users SET last_login = NOW() WHERE user_id = ?");
                $stmt->execute([$user['user_id']]);
                
                header("Location: index.php");
                exit();
            } else {
                $error_message = 'Invalid username or password.';
            }
        } catch (Exception $e) {
            $error_message = 'Login error. Please try again.';
        }
    } else {
        $error_message = 'Please enter both username and password.';
    }
}

// Handle password reset
if ($_SERVER['REQUEST_METHOD'] === 'POST' && isset($_POST['reset_password'])) {
    $reset_username = trim($_POST['reset_username']);
    $reset_email = trim($_POST['reset_email']);
    
    if (!empty($reset_username) && !empty($reset_email)) {
        try {
            $stmt = $conn->prepare("SELECT user_id, full_name, email FROM admin_users WHERE username = ? AND email = ? AND is_active = TRUE");
            $stmt->execute([$reset_username, $reset_email]);
            $user = $stmt->fetch(PDO::FETCH_ASSOC);
            
            if ($user) {
                // Generate temporary password
                $temp_password = 'temp' . rand(1000, 9999) . strtoupper(substr($reset_username, 0, 2));
                $password_hash = password_hash($temp_password, PASSWORD_DEFAULT);
                
                // Update password in database
                $stmt = $conn->prepare("UPDATE admin_users SET password_hash = ?, updated_at = NOW() WHERE user_id = ?");
                $stmt->execute([$password_hash, $user['user_id']]);
                
                $reset_message = "Password reset successful!<br><strong>Your temporary password is: <span style='color: #d32f2f; font-size: 1.2em;'>$temp_password</span></strong><br>Please login and change your password immediately.";
                
                // Log the reset
                error_log("Password reset for user: $reset_username at " . date('Y-m-d H:i:s'));
                
            } else {
                $error_message = 'No account found with that username and email combination.';
            }
        } catch (Exception $e) {
            $error_message = 'Error processing password reset. Please try again.';
        }
    } else {
        $error_message = 'Please enter both username and email for password reset.';
    }
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Login - DTI NC Locator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <style>
        :root {
            --primary-color: #0D47A1;
            --primary-dark: #002171;
            --primary-light: #BBDEFB;
            --secondary-color: #2E7D32;
            --accent-color: #EF6C00;
            --text-dark: #333333;
            --text-medium: #555555;
            --text-light: #777777;
            --background-light: #f8f9fa;
            --white: #ffffff;
            --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
            --shadow-medium: 0 4px 15px rgba(0,0,0,0.15);
            --transition-normal: all 0.3s ease;
            --border-radius-sm: 4px;
            --border-radius-md: 8px;
            --border-radius-lg: 12px;
        }
        
        * {
            box-sizing: border-box;
            margin: 0;
            padding: 0;
        }
        
        body {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            font-family: 'Roboto', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
            min-height: 100vh;
            display: flex;
            align-items: center;
            justify-content: center;
            color: var(--text-dark);
            line-height: 1.6;
            padding: 1rem;
        }
        
        .login-container {
            background: var(--white);
            border-radius: var(--border-radius-lg);
            box-shadow: var(--shadow-medium);
            width: 100%;
            max-width: 450px;
            overflow: hidden;
        }
        
        .login-header {
            background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
            color: var(--white);
            padding: 2rem;
            text-align: center;
        }
        
        .login-logo {
            width: 80px;
            height: 80px;
            margin: 0 auto 1rem;
            background: rgba(255, 255, 255, 0.2);
            border-radius: 50%;
            display: flex;
            align-items: center;
            justify-content: center;
            font-size: 2rem;
        }
        
        .login-title {
            font-size: 1.5rem;
            font-weight: 700;
            margin-bottom: 0.5rem;
        }
        
        .login-subtitle {
            font-size: 0.9rem;
            opacity: 0.9;
        }
        
        .login-body {
            padding: 2rem;
        }
        
        .form-tabs {
            display: flex;
            margin-bottom: 2rem;
            border-bottom: 1px solid #e0e0e0;
        }
        
        .tab-button {
            flex: 1;
            padding: 0.75rem;
            background: none;
            border: none;
            cursor: pointer;
            font-size: 0.9rem;
            font-weight: 500;
            color: var(--text-medium);
            transition: var(--transition-normal);
            border-bottom: 2px solid transparent;
        }
        
        .tab-button.active {
            color: var(--primary-color);
            border-bottom-color: var(--primary-color);
        }
        
        .tab-content {
            display: none;
        }
        
        .tab-content.active {
            display: block;
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
        
        .input-group {
            position: relative;
        }
        
        .input-group i {
            position: absolute;
            left: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            color: var(--text-light);
        }
        
        .input-group .form-input {
            padding-left: 2.5rem;
        }
        
        .password-toggle {
            position: absolute;
            right: 0.75rem;
            top: 50%;
            transform: translateY(-50%);
            background: none;
            border: none;
            color: var(--text-light);
            cursor: pointer;
            font-size: 1rem;
        }
        
        .btn {
            width: 100%;
            padding: 0.75rem;
            background: var(--primary-color);
            color: var(--white);
            border: none;
            border-radius: var(--border-radius-sm);
            font-size: 1rem;
            font-weight: 500;
            cursor: pointer;
            transition: var(--transition-normal);
            display: flex;
            align-items: center;
            justify-content: center;
            gap: 0.5rem;
        }
        
        .btn:hover {
            background: var(--primary-dark);
            transform: translateY(-1px);
        }
        
        .btn:active {
            transform: translateY(0);
        }
        
        .btn-reset {
            background: var(--accent-color);
        }
        
        .btn-reset:hover {
            background: #d84315;
        }
        
        .alert {
            padding: 1rem;
            border-radius: var(--border-radius-sm);
            margin-bottom: 1rem;
            font-size: 0.9rem;
            display: flex;
            align-items: flex-start;
            gap: 0.5rem;
            line-height: 1.5;
        }
        
        .alert-success {
            background: #e8f5e8;
            color: #2e7d32;
            border: 1px solid #c8e6c9;
        }
        
        .alert-error {
            background: #ffebee;
            color: #c62828;
            border: 1px solid #ffcdd2;
        }
        
        .alert-reset {
            background: #fff3e0;
            color: #ef6c00;
            border: 1px solid #ffcc02;
        }
        
        .login-footer {
            padding: 1.5rem 2rem;
            background: var(--background-light);
            text-align: center;
            border-top: 1px solid #e0e0e0;
        }
        
        .login-footer a {
            color: var(--primary-color);
            text-decoration: none;
            font-weight: 500;
            display: inline-flex;
            align-items: center;
            gap: 0.5rem;
            transition: var(--transition-normal);
        }
        
        .login-footer a:hover {
            color: var(--primary-dark);
        }
        
        .remember-me {
            display: flex;
            align-items: center;
            gap: 0.5rem;
            margin-bottom: 1rem;
        }
        
        .remember-me input[type="checkbox"] {
            width: auto;
        }
        
        .temp-password {
            background: #fff3e0;
            border: 2px solid var(--accent-color);
            border-radius: var(--border-radius-md);
            padding: 1.5rem;
            margin: 1rem 0;
            text-align: center;
        }
        
        .temp-password-value {
            font-size: 1.4rem;
            font-weight: 700;
            color: #d32f2f;
            font-family: 'Courier New', monospace;
            background: var(--white);
            padding: 0.5rem 1rem;
            border-radius: var(--border-radius-sm);
            margin: 0.5rem 0;
            border: 1px solid #ddd;
            letter-spacing: 1px;
        }
        
        @media (max-width: 480px) {
            .login-container {
                margin: 0.5rem;
            }
            
            .login-header {
                padding: 1.5rem;
            }
            
            .login-body {
                padding: 1.5rem;
            }
            
            .login-title {
                font-size: 1.3rem;
            }
        }
    </style>
</head>
<body>
    <div class="login-container">
        <div class="login-header">
            <div class="login-logo">
                <i class="fas fa-user-shield"></i>
            </div>
            <h1 class="login-title">Admin Login</h1>
            <p class="login-subtitle">DTI NC Locator System</p>
        </div>
        
        <div class="login-body">
            <div class="form-tabs">
                <button class="tab-button active" onclick="switchTab('login')">
                    <i class="fas fa-sign-in-alt"></i> Login
                </button>
                <button class="tab-button" onclick="switchTab('reset')">
                    <i class="fas fa-key"></i> Reset Password
                </button>
            </div>
            
            <!-- Login Tab -->
            <div id="login-tab" class="tab-content active">
                <?php if ($error_message && !isset($_POST['reset_password'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div><?php echo htmlspecialchars($error_message); ?></div>
                    </div>
                <?php endif; ?>
                
                <form method="POST" action="">
                    <div class="form-group">
                        <label for="username">Username</label>
                        <div class="input-group">
                            <i class="fas fa-user"></i>
                            <input type="text" id="username" name="username" class="form-input" 
                                   placeholder="Enter your username" required 
                                   value="<?php echo isset($_POST['username']) ? htmlspecialchars($_POST['username']) : ''; ?>">
                                                           </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="password">Password</label>
                        <div class="input-group">
                            <i class="fas fa-lock"></i>
                            <input type="password" id="password" name="password" class="form-input" 
                                   placeholder="Enter your password" required>
                            <button type="button" class="password-toggle" onclick="togglePassword('password')">
                                <i class="fas fa-eye" id="password-eye"></i>
                            </button>
                        </div>
                    </div>
                    
                    <div class="remember-me">
                        <input type="checkbox" id="remember" name="remember">
                        <label for="remember">Remember me</label>
                    </div>
                    
                    <button type="submit" name="login" class="btn">
                        <i class="fas fa-sign-in-alt"></i>
                        Login
                    </button>
                </form>
            </div>
            
            <!-- Reset Password Tab -->
            <div id="reset-tab" class="tab-content">
                <?php if ($error_message && isset($_POST['reset_password'])): ?>
                    <div class="alert alert-error">
                        <i class="fas fa-exclamation-triangle"></i>
                        <div><?php echo htmlspecialchars($error_message); ?></div>
                    </div>
                <?php endif; ?>
                
                <?php if ($reset_message): ?>
                    <div class="alert alert-reset">
                        <i class="fas fa-check-circle"></i>
                        <div><?php echo $reset_message; ?></div>
                    </div>
                    <div class="temp-password">
                        <p><strong>⚠️ Important Security Notice:</strong></p>
                        <p>Please copy this temporary password and login immediately.</p>
                        <p>Change your password after logging in for security.</p>
                        <button type="button" class="btn" onclick="switchTab('login')" style="margin-top: 1rem; max-width: 200px;">
                            <i class="fas fa-arrow-left"></i> Go to Login
                        </button>
                    </div>
                <?php else: ?>
                    <div class="alert alert-reset">
                        <i class="fas fa-info-circle"></i>
                        <div>
                            <strong>Password Reset Instructions:</strong><br>
                            Enter your username and email address. A temporary password will be displayed on this screen.
                            <strong>No email will be sent.</strong>
                        </div>
                    </div>
                    
                    <form method="POST" action="">
                        <div class="form-group">
                            <label for="reset_username">Username</label>
                            <div class="input-group">
                                <i class="fas fa-user"></i>
                                <input type="text" id="reset_username" name="reset_username" class="form-input" 
                                       placeholder="Enter your username" required
                                       value="<?php echo isset($_POST['reset_username']) ? htmlspecialchars($_POST['reset_username']) : ''; ?>">
                            </div>
                        </div>
                        
                        <div class="form-group">
                            <label for="reset_email">Email Address</label>
                            <div class="input-group">
                                <i class="fas fa-envelope"></i>
                                <input type="email" id="reset_email" name="reset_email" class="form-input" 
                                       placeholder="Enter your email address" required
                                       value="<?php echo isset($_POST['reset_email']) ? htmlspecialchars($_POST['reset_email']) : ''; ?>">
                            </div>
                        </div>
                        
                        <button type="submit" name="reset_password" class="btn btn-reset">
                            <i class="fas fa-key"></i>
                            Generate Temporary Password
                        </button>
                    </form>
                <?php endif; ?>
            </div>
        </div>
        
        <div class="login-footer">
            <a href="landing.php">
                <i class="fas fa-arrow-left"></i>
                Back to Landing Page
            </a>
        </div>
    </div>

    <script>
        function switchTab(tabName) {
            // Hide all tab contents
            document.querySelectorAll('.tab-content').forEach(content => {
                content.classList.remove('active');
            });
            
            // Remove active class from all tab buttons
            document.querySelectorAll('.tab-button').forEach(button => {
                button.classList.remove('active');
            });
            
            // Show selected tab content
            document.getElementById(tabName + '-tab').classList.add('active');
            
            // Add active class to selected tab button
            event.target.classList.add('active');
            
            // Clear any form data when switching tabs
            if (tabName === 'login') {
                document.getElementById('reset_username').value = '';
                document.getElementById('reset_email').value = '';
            } else {
                document.getElementById('username').value = '';
                document.getElementById('password').value = '';
            }
        }
        
        function togglePassword(inputId) {
            const input = document.getElementById(inputId);
            const eye = document.getElementById(inputId + '-eye');
            
            if (input.type === 'password') {
                input.type = 'text';
                eye.classList.remove('fa-eye');
                eye.classList.add('fa-eye-slash');
            } else {
                input.type = 'password';
                eye.classList.remove('fa-eye-slash');
                eye.classList.add('fa-eye');
            }
        }
        
        // Auto-focus on username field when page loads
        document.addEventListener('DOMContentLoaded', function() {
            document.getElementById('username').focus();
        });
        
        // Handle remember me functionality
        document.addEventListener('DOMContentLoaded', function() {
            const rememberCheckbox = document.getElementById('remember');
            const usernameInput = document.getElementById('username');
            
            // Load saved username if exists
            const savedUsername = localStorage.getItem('remembered_username');
            if (savedUsername) {
                usernameInput.value = savedUsername;
                rememberCheckbox.checked = true;
            }
            
            // Save/remove username based on checkbox
            document.querySelector('form').addEventListener('submit', function() {
                if (rememberCheckbox.checked) {
                    localStorage.setItem('remembered_username', usernameInput.value);
                } else {
                    localStorage.removeItem('remembered_username');
                }
            });
        });
        
        // Auto-switch to login tab after password reset
        <?php if ($reset_message): ?>
        setTimeout(function() {
            // Auto-fill username in login form
            const resetUsername = '<?php echo isset($_POST['reset_username']) ? addslashes($_POST['reset_username']) : ''; ?>';
            if (resetUsername) {
                document.getElementById('username').value = resetUsername;
            }
        }, 100);
        <?php endif; ?>
        
        // Add copy functionality for temporary password
        function copyTempPassword() {
            const tempPasswordElement = document.querySelector('.temp-password-value');
            if (tempPasswordElement) {
                const tempPassword = tempPasswordElement.textContent;
                navigator.clipboard.writeText(tempPassword).then(function() {
                    // Show copied feedback
                    const originalText = tempPasswordElement.innerHTML;
                    tempPasswordElement.innerHTML = '✓ Copied!';
                    tempPasswordElement.style.color = '#2e7d32';
                    
                    setTimeout(function() {
                        tempPasswordElement.innerHTML = originalText;
                        tempPasswordElement.style.color = '#d32f2f';
                    }, 2000);
                }).catch(function() {
                    // Fallback for older browsers
                    const textArea = document.createElement('textarea');
                    textArea.value = tempPassword;
                    document.body.appendChild(textArea);
                    textArea.select();
                    document.execCommand('copy');
                    document.body.removeChild(textArea);
                    
                    alert('Temporary password copied to clipboard!');
                });
            }
        }
        
        // Make temporary password clickable to copy
        document.addEventListener('DOMContentLoaded', function() {
            const tempPasswordElement = document.querySelector('.temp-password-value');
            if (tempPasswordElement) {
                tempPasswordElement.style.cursor = 'pointer';
                tempPasswordElement.title = 'Click to copy';
                tempPasswordElement.addEventListener('click', copyTempPassword);
            }
        });
        
        // Form validation
        document.querySelectorAll('form').forEach(form => {
            form.addEventListener('submit', function(e) {
                const inputs = form.querySelectorAll('input[required]');
                let isValid = true;
                
                inputs.forEach(input => {
                    if (!input.value.trim()) {
                        input.style.borderColor = '#d32f2f';
                        isValid = false;
                    } else {
                        input.style.borderColor = '#ddd';
                    }
                });
                
                if (!isValid) {
                    e.preventDefault();
                    alert('Please fill in all required fields.');
                }
            });
        });
        
        // Clear error styling on input
        document.querySelectorAll('.form-input').forEach(input => {
            input.addEventListener('input', function() {
                this.style.borderColor = '#ddd';
            });
        });
    </script>
</body>
</html>

