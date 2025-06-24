<?php
// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Check if user is active
try {
    $stmt = $conn->prepare("SELECT is_active FROM admin_users WHERE user_id = ?");
    $stmt->execute([$_SESSION['user_id']]);
    $user = $stmt->fetch(PDO::FETCH_ASSOC);
    
    if (!$user || !$user['is_active']) {
        session_destroy();
        header("Location: login.php?error=account_disabled");
        exit();
    }
} catch (Exception $e) {
    session_destroy();
    header("Location: login.php?error=system_error");
    exit();
}
?>
