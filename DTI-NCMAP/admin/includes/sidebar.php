<div class="admin-sidebar" id="adminSidebar">
    <div class="sidebar-header">
        <div class="sidebar-logo">
            <i class="fas fa-map-marked-alt"></i>
        </div>
        <div class="sidebar-title">DTI Admin</div>
        <div class="sidebar-subtitle">NC Locator System</div>
    </div>
    
    <nav class="sidebar-nav">
        <div class="nav-section">
            <div class="nav-section-title">Main</div>
            <div class="nav-item">
                <a href="index.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'index.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-tachometer-alt"></i>
                    <span class="nav-text">Dashboard</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="../index.php" class="nav-link" target="_blank">
                    <i class="nav-icon fas fa-map"></i>
                    <span class="nav-text">View Map</span>
                    <span class="nav-badge">Live</span>
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Management</div>
            <div class="nav-item">
                <a href="offices.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'offices.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-building"></i>
                    <span class="nav-text">DTI Offices</span>
                                    </a>
            </div>
            <div class="nav-item">
                <a href="staff.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'staff.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-users"></i>
                    <span class="nav-text">Staff Members</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="locations.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'locations.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-map-marker-alt"></i>
                    <span class="nav-text">Locations</span>
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">System</div>
            <div class="nav-item">
                <a href="users.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'users.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-user-shield"></i>
                    <span class="nav-text">Admin Users</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="settings.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'settings.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-cog"></i>
                    <span class="nav-text">Settings</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="backup.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'backup.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-database"></i>
                    <span class="nav-text">Backup</span>
                </a>
            </div>
        </div>
        
        <div class="nav-section">
            <div class="nav-section-title">Account</div>
            <div class="nav-item">
                <a href="profile.php" class="nav-link <?php echo basename($_SERVER['PHP_SELF']) == 'profile.php' ? 'active' : ''; ?>">
                    <i class="nav-icon fas fa-user"></i>
                    <span class="nav-text">My Profile</span>
                </a>
            </div>
            <div class="nav-item">
                <a href="logout.php" class="nav-link">
                    <i class="nav-icon fas fa-sign-out-alt"></i>
                    <span class="nav-text">Logout</span>
                </a>
            </div>
        </div>
    </nav>
</div>

