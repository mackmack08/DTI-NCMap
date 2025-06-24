<div class="admin-header">
    <div class="header-left">
        <button class="sidebar-toggle" id="sidebarToggle">
            <i class="fas fa-bars"></i>
        </button>
        <div class="page-info">
            <h1 class="page-title"><?php echo ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></h1>
            <div class="breadcrumb">
                <div class="breadcrumb-item">
                    <a href="index.php" class="breadcrumb-link">Dashboard</a>
                </div>
                <?php if (basename($_SERVER['PHP_SELF']) != 'index.php'): ?>
                <div class="breadcrumb-item">
                    <span><?php echo ucfirst(str_replace('.php', '', basename($_SERVER['PHP_SELF']))); ?></span>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <div class="header-right">
        <div class="header-search">
            <div class="search-icon">
                <i class="fas fa-search"></i>
            </div>
            <input type="text" class="search-input" placeholder="Search...">
        </div>
        
        <div class="header-notifications">
            <button class="notification-btn">
                <i class="fas fa-bell"></i>
                <span class="notification-badge">3</span>
            </button>
        </div>
        
        <div class="user-dropdown">
            <button class="user-btn" onclick="toggleUserDropdown()">
                <div class="user-avatar">
                    <?php echo strtoupper(substr($_SESSION['full_name'], 0, 2)); ?>
                </div>
                <div class="user-info">
                    <div class="user-name"><?php echo htmlspecialchars($_SESSION['full_name']); ?></div>
                    <div class="user-role"><?php echo htmlspecialchars($_SESSION['role']); ?></div>
                </div>
                <i class="fas fa-chevron-down"></i>
            </button>
            <div class="user-dropdown-menu" id="userDropdownMenu">
                <a href="profile.php" class="dropdown-item">
                    <i class="fas fa-user"></i>
                    <span>My Profile</span>
                </a>
                <a href="settings.php" class="dropdown-item">
                    <i class="fas fa-cog"></i>
                    <span>Settings</span>
                </a>
                <div class="dropdown-divider"></div>
                <a href="logout.php" class="dropdown-item">
                    <i class="fas fa-sign-out-alt"></i>
                    <span>Logout</span>
                </a>
            </div>
        </div>
    </div>
</div>
