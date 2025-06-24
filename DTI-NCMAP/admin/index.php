<?php
session_start();
include("../conn/conn.php");

// Check if user is logged in
if (!isset($_SESSION['user_id'])) {
    header("Location: login.php");
    exit();
}

// Get dashboard statistics
try {
    // Count total offices
    $stmt = $conn->prepare("SELECT COUNT(*) as total_offices FROM offices");
    $stmt->execute();
    $total_offices = $stmt->fetch(PDO::FETCH_ASSOC)['total_offices'] ?? 0;
    
    // Count total staff
    $stmt = $conn->prepare("SELECT COUNT(*) as total_staff FROM staff");
    $stmt->execute();
    $total_staff = $stmt->fetch(PDO::FETCH_ASSOC)['total_staff'] ?? 0;
    
    // Count active users
    $stmt = $conn->prepare("SELECT COUNT(*) as active_users FROM admin_users WHERE is_active = 1");
    $stmt->execute();
    $active_users = $stmt->fetch(PDO::FETCH_ASSOC)['active_users'] ?? 0;
    
    // Get recent activities
    $recent_activities = [
        ['action' => 'New office added', 'time' => '2 hours ago', 'user' => 'Admin'],
        ['action' => 'Staff member updated', 'time' => '4 hours ago', 'user' => 'Manager'],
        ['action' => 'User login', 'time' => '6 hours ago', 'user' => $_SESSION['username']],
    ];
    
} catch (Exception $e) {
    $total_offices = 0;
    $total_staff = 0;
    $active_users = 0;
    $recent_activities = [];
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
    <link rel="stylesheet" href="css/admin-styles.css">
    <link rel="shortcut icon" href="../img/favicon.ico" type="image/x-icon">
</head>
<body>
    <div class="admin-wrapper">
        <!-- Sidebar -->
        <?php include 'includes/sidebar.php'; ?>
        
        <!-- Main Content -->
        <div class="admin-main" id="adminMain">
            <!-- Header -->
            <?php include 'includes/header.php'; ?>
            
            <!-- Content -->
            <div class="admin-content">
                <div class="content-header">
                    <h1 class="content-title">Dashboard</h1>
                    <p class="content-description">Welcome back, <?php echo htmlspecialchars($_SESSION['full_name']); ?>! Here's what's happening with your DTI NC Locator system.</p>
                </div>
                
                <!-- Statistics Cards -->
                <div class="stats-grid">
                    <div class="stat-card primary">
                        <div class="stat-icon">
                            <i class="fas fa-building"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $total_offices; ?></div>
                            <div class="stat-label">Total Offices</div>
                        </div>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+12%</span>
                        </div>
                    </div>
                    
                    <div class="stat-card success">
                        <div class="stat-icon">
                            <i class="fas fa-users"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $total_staff; ?></div>
                            <div class="stat-label">Staff Members</div>
                        </div>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+8%</span>
                        </div>
                    </div>
                    
                    <div class="stat-card warning">
                        <div class="stat-icon">
                            <i class="fas fa-user-shield"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number"><?php echo $active_users; ?></div>
                            <div class="stat-label">Active Users</div>
                        </div>
                        <div class="stat-trend neutral">
                            <i class="fas fa-minus"></i>
                            <span>0%</span>
                        </div>
                    </div>
                    
                    <div class="stat-card info">
                        <div class="stat-icon">
                            <i class="fas fa-map-marker-alt"></i>
                        </div>
                        <div class="stat-content">
                            <div class="stat-number">47</div>
                            <div class="stat-label">Locations Mapped</div>
                        </div>
                        <div class="stat-trend positive">
                            <i class="fas fa-arrow-up"></i>
                            <span>+15%</span>
                        </div>
                    </div>
                </div>
                
                <!-- Main Content Grid -->
                <div class="dashboard-grid">
                    <!-- Recent Activities -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-clock"></i>
                                Recent Activities
                            </h3>
                            <a href="#" class="card-action">View All</a>
                        </div>
                        <div class="card-body">
                            <div class="activity-list">
                                <?php foreach ($recent_activities as $activity): ?>
                                <div class="activity-item">
                                    <div class="activity-icon">
                                        <i class="fas fa-circle"></i>
                                    </div>
                                    <div class="activity-content">
                                        <div class="activity-text"><?php echo htmlspecialchars($activity['action']); ?></div>
                                        <div class="activity-meta">
                                            <span class="activity-user"><?php echo htmlspecialchars($activity['user']); ?></span>
                                            <span class="activity-time"><?php echo htmlspecialchars($activity['time']); ?></span>
                                        </div>
                                    </div>
                                </div>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                    
                    <!-- Quick Actions -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-bolt"></i>
                                Quick Actions
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="quick-actions">
                                <a href="offices.php" class="quick-action-btn primary">
                                    <i class="fas fa-plus"></i>
                                    <span>Add New Office</span>
                                </a>
                                <a href="staff.php" class="quick-action-btn success">
                                    <i class="fas fa-user-plus"></i>
                                    <span>Add Staff Member</span>
                                </a>
                                <a href="users.php" class="quick-action-btn warning">
                                    <i class="fas fa-user-cog"></i>
                                    <span>Manage Users</span>
                                </a>
                                <a href="../index.php" class="quick-action-btn info" target="_blank">
                                    <i class="fas fa-map"></i>
                                    <span>View Map</span>
                                </a>
                            </div>
                        </div>
                    </div>
                    
                    <!-- System Status -->
                    <div class="dashboard-card">
                        <div class="card-header">
                            <h3 class="card-title">
                                <i class="fas fa-server"></i>
                                System Status
                            </h3>
                        </div>
                        <div class="card-body">
                            <div class="status-list">
                                <div class="status-item">
                                    <div class="status-indicator success"></div>
                                    <div class="status-content">
                                        <div class="status-label">Database Connection</div>
                                        <div class="status-value">Online</div>
                                    </div>
                                </div>
                                <div class="status-item">
                                    <div class="status-indicator success"></div>
                                    <div class="status-content">
                                        <div class="status-label">Map Service</div>
                                        <div class="status-value">Active</div>
                                    </div>
                                </div>
                                <div class="status-item">
                                    <div class="status-indicator warning"></div>
                                    <div class="status-content">
                                        <div class="status-label">Backup Status</div>
                                        <div class="status-value">Pending</div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <script src="js/admin-scripts.js"></script>
</body>
</html>
