<?php
session_start();
include("../conn/conn.php");

// Check if user is logged in
if (!isset($_SESSION['admin_logged_in']) || $_SESSION['admin_logged_in'] !== true) {
    header("Location: ../login.php");
    exit();
}

// Get dashboard statistics
try {
    // Count offices by type
    $stmt = $conn->prepare("SELECT office_type, COUNT(*) as count FROM offices WHERE is_active = TRUE GROUP BY office_type");
    $stmt->execute();
    $office_stats = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Total offices
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM offices WHERE is_active = TRUE");
    $stmt->execute();
    $total_offices = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Total staff
    $stmt = $conn->prepare("SELECT COUNT(*) as total FROM staff WHERE is_active = TRUE");
    $stmt->execute();
    $total_staff = $stmt->fetch(PDO::FETCH_ASSOC)['total'];
    
    // Recent offices
    $stmt = $conn->prepare("SELECT office_name, office_type, created_at FROM offices WHERE is_active = TRUE ORDER BY created_at DESC LIMIT 5");
    $stmt->execute();
    $recent_offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $error_message = "Error loading dashboard data: " . $e->getMessage();
}
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Admin Dashboard - DTI NC Locator</title>
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
    <link rel="stylesheet" href="../assets/css/admin-styles.css">
</head>
<body>
    <div class="admin-container">
        <!-- Sidebar -->
        <nav class="sidebar">
            <div class="sidebar-header">
                <div class="sidebar-logo">
                    <i class="fas fa-map-marked-alt"></i>
                    <span>DTI NC Admin</span>
                </div>
            </div>
            
            <div class="sidebar-menu">
                <a href="dashboard.php" class="menu-item active">
                    <i class="fas fa-tachometer-alt"></i>
                    <span>Dashboard</span>
                </a>
                <a href="offices.php" class="menu-item">
                    <i class="fas fa-building"></i>
                    <span>Manage Offices</span>
                </a>
                <a href="staff.php" class="menu-item">
                    <i class="fas fa-users"></i>
                    <span>Manage Staff</span>
                </a>
                <a href="regions.php" class="menu-item">
                    <i class="fas fa-globe-asia"></i>
                    <span>Manage Regions</span>
                </a>
                <a href="users.php" class="menu-item">
                    <i class="fas fa-user-cog"></i>
                    <span>Admin Users</span>
                </a>
                <a href="../index.php" class="menu-item" target="_blank">
                    <i class="fas fa-external-link-alt"></i>
                    <span>View Map</span>
                </a>
            </div>
            
            <div class="sidebar-footer">
                <div class="user-info">
                    <div class="user-avatar">
                        <i class="fas fa-user"></i>
                    </div>
                    <div class="user-details">
                        <div class="user-name"><?php echo htmlspecialchars($_SESSION['admin_full_name']); ?></div>
                        <div class="user-role"><?php echo htmlspecialchars($_SESSION['admin_role']); ?></div>
                    </div>
                </div>
                <a href="logout.php" class="logout-btn">
                    <i class="fas fa-sign-out-alt"></i>
                </a>
            </div>
        </nav>
        
        <!-- Main Content -->
        <main class="main-content">
            <div class="content-header">
                <h1>Dashboard</h1>
                                <p>Welcome back, <?php echo htmlspecialchars($_SESSION['admin_full_name']); ?>!</p>
            </div>
            
            <div class="dashboard-content">
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-number"><?php echo $total_offices; ?></div>
                            <div class="stat-label">Total Offices</div>
                        </div>
                    </div>
                    
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-number"><?php echo $total_staff; ?></div>
                            <div class="stat-label">Total Staff</div>
                        </div>
                    </div>
                    
                    <?php foreach ($office_stats as $stat): ?>
                    <div class="stat-card">
                        <div class="stat-icon">
                            <i class="fas fa-<?php 
                                echo $stat['office_type'] === 'Regional Office' ? 'crown' : 
                                    ($stat['office_type'] === 'Provincial Office' ? 'city' : 
                                    ($stat['office_type'] === 'Negosyo Center' ? 'store' : 'building')); 
                            ?>"></i>
                        </div>
                        <div class="stat-info">
                            <div class="stat-number"><?php echo $stat['count']; ?></div>
                            <div class="stat-label"><?php echo htmlspecialchars($stat['office_type']); ?></div>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Recent Activity -->
                <div class="dashboard-grid">
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3>Recent Offices Added</h3>
                            <a href="offices.php" class="btn btn-sm btn-primary">View All</a>
                        </div>
                        <div class="card-body">
                            <?php if (!empty($recent_offices)): ?>
                                <div class="recent-list">
                                    <?php foreach ($recent_offices as $office): ?>
                                    <div class="recent-item">
                                        <div class="recent-info">
                                            <div class="recent-title"><?php echo htmlspecialchars($office['office_name']); ?></div>
                                            <div class="recent-meta">
                                                <span class="office-type"><?php echo htmlspecialchars($office['office_type']); ?></span>
                                                <span class="recent-date"><?php echo date('M j, Y', strtotime($office['created_at'])); ?></span>
                                            </div>
                                        </div>
                                    </div>
                                    <?php endforeach; ?>
                                </div>
                            <?php else: ?>
                                <p class="no-data">No offices added yet.</p>
                            <?php endif; ?>
                        </div>
                    </div>
                    
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3>Quick Actions</h3>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="offices.php?action=add" class="quick-action">
                                    <i class="fas fa-plus-circle"></i>
                                    <span>Add New Office</span>
                                </a>
                                <a href="staff.php?action=add" class="quick-action">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Add New Staff</span>
                                </a>
                                <a href="../index.php" class="quick-action" target="_blank">
                                    <i class="fas fa-map"></i>
                                    <span>View Public Map</span>
                                </a>
                                <a href="users.php" class="quick-action">
                                    <i class="fas fa-user-cog"></i>
                                    <span>Manage Users</span>
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</body>
</html>
