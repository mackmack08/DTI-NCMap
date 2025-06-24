<?php
include("../conn/conn.php");

// This script should be run manually by server administrator
// Usage: php reset-password.php username new_password

if ($argc != 3) {
    echo "Usage: php reset-password.php <username> <new_password>\n";
    exit(1);
}

$username = $argv[1];
$new_password = $argv[2];

try {
    // Check if user exists
    $stmt = $conn->prepare("SELECT user_id FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user) {
        echo "User '$username' not found.\n";
        exit(1);
    }
    
    // Hash new password
    $password_hash = password_hash($new_password, PASSWORD_DEFAULT);
    
    // Update password
    $stmt = $conn->prepare("UPDATE admin_users SET password_hash = ?, updated_at = NOW() WHERE username = ?");
    $stmt->execute([$password_hash, $username]);
    
    echo "Password for user '$username' has been reset successfully.\n";
    echo "New password: $new_password\n";
    echo "Please ask the user to change their password after login.\n";
    
} catch (Exception $e) {
    echo "Error: " . $e->getMessage() . "\n";
    exit(1);
}
?>

    echo "Error: " . $e->getMessage() . "\n";
    exit
