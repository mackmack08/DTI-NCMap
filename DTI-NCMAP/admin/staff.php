<?php
session_start();
include("../conn/conn.php");
include("includes/auth-check.php");

// Get all staff with office information
try {
    $stmt = $conn->prepare("
        SELECT s.*, o.office_name, o.office_type 
        FROM staff s 
        LEFT JOIN offices o ON s.office_id = o.office_id 
        ORDER BY s.created_at DESC
    ");
    $stmt->execute();
    $staff_members = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
    // Get all offices for dropdown
    $stmt = $conn->prepare("SELECT office_id, office_name, office_type FROM offices ORDER BY office_name");
    $stmt->execute();
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
    
} catch (Exception $e) {
    $staff_members = [];
    $offices = [];
    $error_message = "Error loading staff: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Staff - DTI NC Locator Admin</title>
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
                    <div class="content-title-section">
                        <h1 class="content-title">Staff Members</h1>
                        <p class="content-description">Manage DTI staff members and their information</p>
                    </div>
                    <div class="content-actions">
                        <button class="btn btn-primary" onclick="openAddStaffModal()">
                            <i class="fas fa-user-plus"></i>
                            Add New Staff
                        </button>
                    </div>
                </div>
                
                <!-- Staff Cards Grid -->
                <div class="staff-grid" id="staffGrid">
                    <?php foreach ($staff_members as $staff): ?>
                    <div class="staff-card" data-office="<?php echo htmlspecialchars($staff['office_name']); ?>" data-position="<?php echo htmlspecialchars($staff['position']); ?>">
                        <div class="staff-photo">
                            <?php if ($staff['photo_path']): ?>
                                                                <img src="<?php echo htmlspecialchars($staff['photo_path']); ?>" alt="<?php echo htmlspecialchars($staff['name']); ?>">
                            <?php else: ?>
                                <div class="staff-photo-placeholder">
                                    <?php echo strtoupper(substr($staff['name'], 0, 2)); ?>
                                </div>
                            <?php endif; ?>
                        </div>
                        <div class="staff-info">
                            <h3 class="staff-name"><?php echo htmlspecialchars($staff['name']); ?></h3>
                            <p class="staff-position"><?php echo htmlspecialchars($staff['position']); ?></p>
                            <p class="staff-office">
                                <i class="fas fa-building"></i>
                                <?php echo htmlspecialchars($staff['office_name'] ?: 'No Office Assigned'); ?>
                            </p>
                            <?php if ($staff['contact']): ?>
                                <p class="staff-contact">
                                    <i class="fas fa-phone"></i>
                                    <?php echo htmlspecialchars($staff['contact']); ?>
                                </p>
                            <?php endif; ?>
                            <?php if ($staff['email']): ?>
                                <p class="staff-email">
                                    <i class="fas fa-envelope"></i>
                                    <?php echo htmlspecialchars($staff['email']); ?>
                                </p>
                            <?php endif; ?>
                        </div>
                        <div class="staff-actions">
                            <button class="btn-action btn-view" onclick="viewStaff(<?php echo $staff['staff_id']; ?>)" title="View Details">
                                <i class="fas fa-eye"></i>
                            </button>
                            <button class="btn-action btn-edit" onclick="editStaff(<?php echo $staff['staff_id']; ?>)" title="Edit Staff">
                                <i class="fas fa-edit"></i>
                            </button>
                            <button class="btn-action btn-delete" onclick="deleteStaff(<?php echo $staff['staff_id']; ?>)" title="Delete Staff">
                                <i class="fas fa-trash"></i>
                            </button>
                        </div>
                    </div>
                    <?php endforeach; ?>
                </div>
                
                <!-- Empty State -->
                <?php if (empty($staff_members)): ?>
                <div class="empty-state">
                    <div class="empty-icon">
                        <i class="fas fa-users"></i>
                    </div>
                    <h3>No Staff Members Found</h3>
                    <p>Start by adding your first staff member to the system.</p>
                    <button class="btn btn-primary" onclick="openAddStaffModal()">
                        <i class="fas fa-user-plus"></i>
                        Add First Staff Member
                    </button>
                </div>
                <?php endif; ?>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Staff Modal -->
    <div class="modal" id="staffModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="staffModalTitle">Add New Staff Member</h3>
                <button class="modal-close" onclick="closeStaffModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="staffForm" enctype="multipart/form-data">
                    <input type="hidden" id="staffId" name="staff_id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="staffName">Full Name *</label>
                            <input type="text" id="staffName" name="name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="staffPosition">Position *</label>
                            <input type="text" id="staffPosition" name="position" class="form-input" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="staffOffice">Assigned Office</label>
                            <select id="staffOffice" name="office_id" class="form-select">
                                <option value="">Select Office</option>
                                <?php foreach ($offices as $office): ?>
                                    <option value="<?php echo $office['office_id']; ?>">
                                        <?php echo htmlspecialchars($office['office_name']); ?> (<?php echo htmlspecialchars($office['office_type']); ?>)
                                    </option>
                                <?php endforeach; ?>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="staffType">Staff Type</label>
                            <select id="staffType" name="staff_type" class="form-select">
                                <option value="">Select Type</option>
                                <option value="Regular">Regular Staff</option>
                                <option value="Manager">Manager</option>
                                <option value="Supervisor">Supervisor</option>
                                <option value="Specialist">Specialist</option>
                                <option value="Assistant">Assistant</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="staffContact">Contact Number</label>
                            <input type="tel" id="staffContact" name="contact" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="staffEmail">Email Address</label>
                            <input type="email" id="staffEmail" name="email" class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="staffServices">Services/Specializations</label>
                        <textarea id="staffServices" name="services" class="form-textarea" rows="3" placeholder="Enter services or specializations separated by commas"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="staffBio">Biography/Description</label>
                        <textarea id="staffBio" name="bio" class="form-textarea" rows="4" placeholder="Brief description about the staff member"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="staffPhoto">Staff Photo</label>
                        <input type="file" id="staffPhoto" name="photo" class="form-file" accept="image/*">
                        <small class="form-help">Upload a professional photo (JPEG or PNG, max 2MB)</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Save Staff Member
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeStaffModal()">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- View Staff Modal -->
    <div class="modal" id="viewStaffModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="viewStaffTitle">Staff Details</h3>
                <button class="modal-close" onclick="closeViewStaffModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="viewStaffBody">
                <!-- Staff details will be loaded here -->
            </div>
        </div>
    </div>
    
    <script src="js/admin-scripts.js"></script>
    <script>
        // Staff management functions
        function openAddStaffModal() {
            document.getElementById('staffModalTitle').textContent = 'Add New Staff Member';
            document.getElementById('staffForm').reset();
            document.getElementById('staffId').value = '';
            document.getElementById('staffModal').style.display = 'block';
        }
        
        function closeStaffModal() {
            document.getElementById('staffModal').style.display = 'none';
        }
        
        function closeViewStaffModal() {
            document.getElementById('viewStaffModal').style.display = 'none';
        }
        
        function editStaff(staffId) {
            // Fetch staff data and populate form
            fetch(`endpoints/get-staff.php?id=${staffId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const staff = data.staff;
                        document.getElementById('staffModalTitle').textContent = 'Edit Staff Member';
                        document.getElementById('staffId').value = staff.staff_id;
                        document.getElementById('staffName').value = staff.name;
                        document.getElementById('staffPosition').value = staff.position;
                        document.getElementById('staffOffice').value = staff.office_id || '';
                        document.getElementById('staffType').value = staff.staff_type || '';
                        document.getElementById('staffContact').value = staff.contact || '';
                        document.getElementById('staffEmail').value = staff.email || '';
                        document.getElementById('staffServices').value = staff.services || '';
                        document.getElementById('staffBio').value = staff.bio || '';
                        
                        document.getElementById('staffModal').style.display = 'block';
                    } else {
                        alert('Error loading staff data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading staff data');
                });
        }
        
        function viewStaff(staffId) {
            // Fetch and display staff details
            fetch(`endpoints/get-staff.php?id=${staffId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const staff = data.staff;
                        document.getElementById('viewStaffTitle').textContent = staff.name;
                        
                        const staffDetailsHtml = `
                            <div class="staff-details">
                                <div class="staff-profile">
                                    <div class="profile-photo">
                                        ${staff.photo_path ? 
                                            `<img src="${staff.photo_path}" alt="${staff.name}">` : 
                                            `<div class="photo-placeholder">${staff.name.substring(0, 2).toUpperCase()}</div>`
                                        }
                                    </div>
                                    <div class="profile-info">
                                        <h2>${staff.name}</h2>
                                        <p class="position">${staff.position}</p>
                                        ${staff.office_name ? `<p class="office"><i class="fas fa-building"></i> ${staff.office_name}</p>` : ''}
                                    </div>
                                </div>
                                
                                <div class="detail-grid">
                                    ${staff.staff_type ? `<div class="detail-item"><label>Staff Type:</label><span>${staff.staff_type}</span></div>` : ''}
                                    ${staff.contact ? `<div class="detail-item"><label>Contact:</label><span>${staff.contact}</span></div>` : ''}
                                    ${staff.email ? `<div class="detail-item"><label>Email:</label><span>${staff.email}</span></div>` : ''}
                                    ${staff.services ? `<div class="detail-item full-width"><label>Services/Specializations:</label><span>${staff.services}</span></div>` : ''}
                                    ${staff.bio ? `<div class="detail-item full-width"><label>Biography:</label><span>${staff.bio}</span></div>` : ''}
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('viewStaffBody').innerHTML = staffDetailsHtml;
                        document.getElementById('viewStaffModal').style.display = 'block';
                    } else {
                        alert('Error loading staff details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading staff details');
                });
        }
        
        function deleteStaff(staffId) {
            if (confirm('Are you sure you want to delete this staff member? This action cannot be undone.')) {
                fetch('endpoints/delete-staff.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ staff_id: staffId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Staff member deleted successfully');
                        location.reload();
                    } else {
                        alert('Error deleting staff member: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting staff member');
                });
            }
        }
        
        // Form submission
        document.getElementById('staffForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const isEdit = document.getElementById('staffId').value !== '';
            const endpoint = isEdit ? 'endpoints/edit-staff.php' : 'endpoints/add-staff.php';
            
            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(isEdit ? 'Staff member updated successfully' : 'Staff member added successfully');
                    closeStaffModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving staff member');
            });
        });
    </script>
</body>
</html>
