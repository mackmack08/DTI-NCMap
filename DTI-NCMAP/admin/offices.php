<?php
session_start();
include("../conn/conn.php");
include("includes/auth-check.php");

// Get all offices
try {
    $stmt = $conn->prepare("SELECT * FROM offices ORDER BY created_at DESC");
    $stmt->execute();
    $offices = $stmt->fetchAll(PDO::FETCH_ASSOC);
} catch (Exception $e) {
    $offices = [];
    $error_message = "Error loading offices: " . $e->getMessage();
}
?>

<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>Manage Offices - DTI NC Locator Admin</title>
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
                        <h1 class="content-title">DTI Offices</h1>
                        <p class="content-description">Manage DTI offices and Negosyo Centers locations</p>
                    </div>
                    <div class="content-actions">
                        <button class="btn btn-primary" onclick="openAddOfficeModal()">
                            <i class="fas fa-plus"></i>
                            Add New Office
                        </button>
                    </div>
                </div>
                
                <!-- Offices Table -->
                <div class="data-table-container">
                    <div class="table-header">
                        <div class="table-title">
                            <h3>All Offices (<?php echo count($offices); ?>)</h3>
                        </div>
                        <div class="table-filters">
                            <input type="text" class="search-input" placeholder="Search offices..." id="officeSearch">
                            <select class="filter-select" id="typeFilter">
                                <option value="">All Types</option>
                                <option value="Regional Office">Regional Office</option>
                                <option value="Provincial Office">Provincial Office</option>
                                <option value="Field Office">Field Office</option>
                                <option value="Extension Office">Extension Office</option>
                                <option value="Negosyo Center">Negosyo Center</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="table-responsive">
                        <table class="data-table">
                            <thead>
                                <tr>
                                    <th>Office Name</th>
                                    <th>Type</th>
                                    <th>Address</th>
                                    <th>Contact</th>
                                    <th>Status</th>
                                    <th>Actions</th>
                                </tr>
                            </thead>
                            <tbody id="officesTableBody">
                                <?php foreach ($offices as $office): ?>
                                <tr>
                                    <td>
                                        <div class="office-info">
                                            <?php if ($office['image_path']): ?>
                                                <img src="<?php echo htmlspecialchars($office['image_path']); ?>" alt="Office" class="office-thumb">
                                            <?php else: ?>
                                                <div class="office-thumb-placeholder">
                                                    <i class="fas fa-building"></i>
                                                </div>
                                            <?php endif; ?>
                                            <div>
                                                <div class="office-name"><?php echo htmlspecialchars($office['office_name']); ?></div>
                                                <div class="office-region">Region <?php echo htmlspecialchars($office['region_id']); ?></div>
                                            </div>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="office-type-badge <?php echo strtolower(str_replace(' ', '-', $office['office_type'])); ?>">
                                            <?php echo htmlspecialchars($office['office_type']); ?>
                                        </span>
                                    </td>
                                    <td>
                                        <div class="office-address">
                                            <?php echo htmlspecialchars($office['address']); ?>
                                        </div>
                                    </td>
                                    <td>
                                        <div class="contact-info">
                                            <?php if ($office['phone']): ?>
                                                <div><i class="fas fa-phone"></i> <?php echo htmlspecialchars($office['phone']); ?></div>
                                            <?php endif; ?>
                                            <?php if ($office['email']): ?>
                                                <div><i class="fas fa-envelope"></i> <?php echo htmlspecialchars($office['email']); ?></div>
                                            <?php endif; ?>
                                        </div>
                                    </td>
                                    <td>
                                        <span class="status-badge active">Active</span>
                                    </td>
                                    <td>
                                        <div class="action-buttons">
                                            <button class="btn-action btn-view" onclick="viewOffice(<?php echo $office['office_id']; ?>)" title="View Details">
                                                <i class="fas fa-eye"></i>
                                            </button>
                                            <button class="btn-action btn-edit" onclick="editOffice(<?php echo $office['office_id']; ?>)" title="Edit Office">
                                                <i class="fas fa-edit"></i>
                                            </button>
                                            <button class="btn-action btn-delete" onclick="deleteOffice(<?php echo $office['office_id']; ?>)" title="Delete Office">
                                                <i class="fas fa-trash"></i>
                                            </button>
                                        </div>
                                    </td>
                                </tr>
                                <?php endforeach; ?>
                            </tbody>
                        </table>
                    </div>
                </div>
            </div>
        </div>
    </div>
    
    <!-- Add/Edit Office Modal -->
    <div class="modal" id="officeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="officeModalTitle">Add New Office</h3>
                <button class="modal-close" onclick="closeOfficeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body">
                <form id="officeForm" enctype="multipart/form-data">
                    <input type="hidden" id="officeId" name="office_id">
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="officeName">Office Name *</label>
                            <input type="text" id="officeName" name="office_name" class="form-input" required>
                        </div>
                        <div class="form-group">
                            <label for="officeType">Office Type *</label>
                            <select id="officeType" name="office_type" class="form-select" required>
                                <option value="">Select Type</option>
                                <option value="Regional Office">Regional Office</option>
                                <option value="Provincial Office">Provincial Office</option>
                                <option value="Field Office">Field Office</option>
                                <option value="Extension Office">Extension Office</option>
                                <option value="Negosyo Center">Negosyo Center</option>
                            </select>
                        </div>
                    </div>
                    
                                        <div class="form-row">
                        <div class="form-group">
                            <label for="regionId">Region *</label>
                            <select id="regionId" name="region_id" class="form-select" required>
                                <option value="">Select Region</option>
                                <option value="7">Region VII - Central Visayas</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="city">City/Municipality *</label>
                            <input type="text" id="city" name="city" class="form-input" required>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Complete Address *</label>
                        <textarea id="address" name="address" class="form-textarea" rows="3" required></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="latitude">Latitude *</label>
                            <input type="number" id="latitude" name="latitude" class="form-input" step="any" required>
                        </div>
                        <div class="form-group">
                            <label for="longitude">Longitude *</label>
                            <input type="number" id="longitude" name="longitude" class="form-input" step="any" required>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="phone">Phone Number</label>
                            <input type="tel" id="phone" name="phone" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-textarea" rows="3"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="services">Services Offered</label>
                        <textarea id="services" name="services" class="form-textarea" rows="2" placeholder="Enter services separated by commas"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="officeHours">Office Hours</label>
                        <input type="text" id="officeHours" name="office_hours" class="form-input" placeholder="e.g., 8:00 AM - 5:00 PM, Monday to Friday">
                    </div>
                    
                    <div class="form-group">
                        <label for="officeImage">Office Image</label>
                        <input type="file" id="officeImage" name="office_image" class="form-file" accept="image/*">
                        <small class="form-help">Upload an image of the office (JPEG or PNG, max 5MB)</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i>
                            Save Office
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeOfficeModal()">
                            <i class="fas fa-times"></i>
                            Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    
    <!-- View Office Modal -->
    <div class="modal" id="viewOfficeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h3 class="modal-title" id="viewOfficeTitle">Office Details</h3>
                <button class="modal-close" onclick="closeViewOfficeModal()">
                    <i class="fas fa-times"></i>
                </button>
            </div>
            <div class="modal-body" id="viewOfficeBody">
                <!-- Office details will be loaded here -->
            </div>
        </div>
    </div>
    
    <script src="js/admin-scripts.js"></script>
    <script>
        // Office management functions
        function openAddOfficeModal() {
            document.getElementById('officeModalTitle').textContent = 'Add New Office';
            document.getElementById('officeForm').reset();
            document.getElementById('officeId').value = '';
            document.getElementById('officeModal').style.display = 'block';
        }
        
        function closeOfficeModal() {
            document.getElementById('officeModal').style.display = 'none';
        }
        
        function closeViewOfficeModal() {
            document.getElementById('viewOfficeModal').style.display = 'none';
        }
        
        function editOffice(officeId) {
            // Fetch office data and populate form
            fetch(`endpoints/get-office.php?id=${officeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const office = data.office;
                        document.getElementById('officeModalTitle').textContent = 'Edit Office';
                        document.getElementById('officeId').value = office.office_id;
                        document.getElementById('officeName').value = office.office_name;
                        document.getElementById('officeType').value = office.office_type;
                        document.getElementById('regionId').value = office.region_id;
                        document.getElementById('city').value = office.city;
                        document.getElementById('address').value = office.address;
                        document.getElementById('latitude').value = office.latitude;
                        document.getElementById('longitude').value = office.longitude;
                        document.getElementById('phone').value = office.phone || '';
                        document.getElementById('email').value = office.email || '';
                        document.getElementById('description').value = office.description || '';
                        document.getElementById('services').value = office.services || '';
                        document.getElementById('officeHours').value = office.office_hours || '';
                        
                        document.getElementById('officeModal').style.display = 'block';
                    } else {
                        alert('Error loading office data');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading office data');
                });
        }
        
        function viewOffice(officeId) {
            // Fetch and display office details
            fetch(`endpoints/get-office.php?id=${officeId}`)
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        const office = data.office;
                        document.getElementById('viewOfficeTitle').textContent = office.office_name;
                        
                        const officeDetailsHtml = `
                            <div class="office-details">
                                ${office.image_path ? `<div class="office-image"><img src="${office.image_path}" alt="Office Image"></div>` : ''}
                                <div class="detail-grid">
                                    <div class="detail-item">
                                        <label>Office Type:</label>
                                        <span class="office-type-badge ${office.office_type.toLowerCase().replace(' ', '-')}">${office.office_type}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Region:</label>
                                        <span>Region ${office.region_id}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>City/Municipality:</label>
                                        <span>${office.city}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Address:</label>
                                        <span>${office.address}</span>
                                    </div>
                                    <div class="detail-item">
                                        <label>Coordinates:</label>
                                        <span>${office.latitude}, ${office.longitude}</span>
                                    </div>
                                    ${office.phone ? `<div class="detail-item"><label>Phone:</label><span>${office.phone}</span></div>` : ''}
                                    ${office.email ? `<div class="detail-item"><label>Email:</label><span>${office.email}</span></div>` : ''}
                                    ${office.office_hours ? `<div class="detail-item"><label>Office Hours:</label><span>${office.office_hours}</span></div>` : ''}
                                    ${office.description ? `<div class="detail-item full-width"><label>Description:</label><span>${office.description}</span></div>` : ''}
                                    ${office.services ? `<div class="detail-item full-width"><label>Services:</label><span>${office.services}</span></div>` : ''}
                                </div>
                            </div>
                        `;
                        
                        document.getElementById('viewOfficeBody').innerHTML = officeDetailsHtml;
                        document.getElementById('viewOfficeModal').style.display = 'block';
                    } else {
                        alert('Error loading office details');
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error loading office details');
                });
        }
        
        function deleteOffice(officeId) {
            if (confirm('Are you sure you want to delete this office? This action cannot be undone.')) {
                fetch('endpoints/delete-office.php', {
                    method: 'POST',
                    headers: {
                        'Content-Type': 'application/json',
                    },
                    body: JSON.stringify({ office_id: officeId })
                })
                .then(response => response.json())
                .then(data => {
                    if (data.success) {
                        alert('Office deleted successfully');
                        location.reload();
                    } else {
                        alert('Error deleting office: ' + data.message);
                    }
                })
                .catch(error => {
                    console.error('Error:', error);
                    alert('Error deleting office');
                });
            }
        }
        
        // Form submission
        document.getElementById('officeForm').addEventListener('submit', function(e) {
            e.preventDefault();
            
            const formData = new FormData(this);
            const isEdit = document.getElementById('officeId').value !== '';
            const endpoint = isEdit ? 'endpoints/edit-office.php' : 'endpoints/add-office.php';
            
            fetch(endpoint, {
                method: 'POST',
                body: formData
            })
            .then(response => response.json())
            .then(data => {
                if (data.success) {
                    alert(isEdit ? 'Office updated successfully' : 'Office added successfully');
                    closeOfficeModal();
                    location.reload();
                } else {
                    alert('Error: ' + data.message);
                }
            })
            .catch(error => {
                console.error('Error:', error);
                alert('Error saving office');
            });
        });
        
        // Search and filter functionality
        document.getElementById('officeSearch').addEventListener('input', filterOffices);
        document.getElementById('typeFilter').addEventListener('change', filterOffices);
        
        function filterOffices() {
            const searchTerm = document.getElementById('officeSearch').value.toLowerCase();
            const typeFilter = document.getElementById('typeFilter').value;
            const rows = document.querySelectorAll('#officesTableBody tr');
            
            rows.forEach(row => {
                const officeName = row.querySelector('.office-name').textContent.toLowerCase();
                const officeType = row.querySelector('.office-type-badge').textContent;
                const address = row.querySelector('.office-address').textContent.toLowerCase();
                
                const matchesSearch = officeName.includes(searchTerm) || address.includes(searchTerm);
                const matchesType = !typeFilter || officeType === typeFilter;
                
                row.style.display = matchesSearch && matchesType ? '' : 'none';
            });
        }
    </script>
</body>
</html>

