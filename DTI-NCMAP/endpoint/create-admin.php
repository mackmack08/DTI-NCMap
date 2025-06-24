<?php
include("../conn/conn.php");

// Default admin credentials
$username = 'admin';
$password = 'admin123'; // Change this to your desired password
$full_name = 'System Administrator';
$email = 'admin@dti.gov.ph';
$role = 'Super Admin';

try {
    // Check if admin already exists
    $stmt = $conn->prepare("SELECT COUNT(*) FROM admin_users WHERE username = ?");
    $stmt->execute([$username]);
    $exists = $stmt->fetchColumn();
    
    if ($exists > 0) {
        echo "Admin user already exists!<br>";
        echo "Use the change password feature to update the password.";
    } else {
        // Hash the password
        $password_hash = password_hash($password, PASSWORD_DEFAULT);
        
        // Insert admin user
        $stmt = $conn->prepare("
            INSERT INTO admin_users (username, password_hash, full_name, email, role) 
            VALUES (?, ?, ?, ?, ?)
        ");
        $stmt->execute([$username, $password_hash, $full_name, $email, $role]);
        
        echo "Admin user created successfully!<br>";
        echo "Username: " . $username . "<br>";
        echo "Password: " . $password . "<br>";
        echo "Please change the password after first login.";
    }
} catch (Exception $e) {
    echo "Error: " . $e->getMessage();
}
?>
