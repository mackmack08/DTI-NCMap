<?php
session_start();
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTI Office Locator - Cebu Province</title>
    
    <!-- Leaflet.js CSS -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <!-- Font Awesome -->
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <!-- Custom CSS -->
    <link rel="stylesheet" href="style.css">
    <!-- Favicon -->
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">
    <style>
        /* Additional styles for Cebu focus */
        .cebu-region {
            background-color: #e3f2fd;
            color: #0d47a1;
            font-weight: bold;
            padding: 2px 8px;
            border-radius: 4px;
        }
        
        .other-region {
            background-color: #f5f5f5;
            color: #757575 !important;
            padding: 2px 8px;
            border-radius: 4px;
        }
        
        /* Improved Marker Popup Styles */
        .marker-popup {
            padding: 0;
            font-family: 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
        }

        .popup-header {
            background-color: #f8f9fa;
            padding: 12px 15px;
            border-bottom: 1px solid #e9ecef;
            border-radius: 4px 4px 0 0;
        }

        .popup-title {
            margin: 0 0 8px 0;
            font-size: 16px;
            font-weight: 600;
            color: #333;
            line-height: 1.3;
        }

        .popup-badge {
            display: inline-block;
            padding: 4px 8px;
            font-size: 12px;
            font-weight: 500;
            border-radius: 4px;
            margin-bottom: 5px;
        }

        .popup-badge.regional-office {
            background-color: #e3f2fd;
            color: #0d47a1;
        }

        .popup-badge.provincial-office {
            background-color: #e8f5e9;
            color: #1b5e20;
        }

        .popup-badge.field-office {
            background-color: #fff3e0;
            color: #e65100;
        }

        .popup-badge.extension-office {
            background-color: #f3e5f5;
            color: #6a1b9a;
        }

        .popup-body {
            padding: 15px;
        }

        .popup-info {
            margin-bottom: 10px;
        }

        .popup-info-item {
            display: flex;
            align-items: flex-start;
            margin-bottom: 8px;
            font-size: 13px;
            line-height: 1.4;
        }

        .popup-info-item i {
            color: #666;
            margin-right: 8px;
            min-width: 16px;
            margin-top: 2px;
        }

        .popup-info-item span {
            flex: 1;
            color: #333;
        }

        .popup-footer {
            padding: 10px 15px 15px;
            display: flex;
            gap: 8px;
            justify-content: space-between;
        }

        .popup-btn {
            flex: 1;
            text-align: center;
            padding: 6px 12px;
            font-size: 13px;
        }

        /* Override Leaflet's default popup styles */
        .leaflet-popup-content-wrapper {
            padding: 0;
            border-radius: 6px;
            box-shadow: 0 3px 14px rgba(0,0,0,0.2);
        }

        .leaflet-popup-content {
            margin: 0;
            width: 280px !important;
        }

        .leaflet-popup-tip {
            box-shadow: 0 3px 14px rgba(0,0,0,0.2);
        }

        .office-popup .leaflet-popup-close-button {
            padding: 8px;
            font-size: 18px;
            color: #666;
        }

        .office-popup .leaflet-popup-close-button:hover {
            color: #333;
        }
        
        /* Custom NC marker styles */
        .nc-marker-icon {
            background-color: #1976D2;
            color: white;
            border-radius: 50%;
            width: 36px;
            height: 36px;
            display: flex;
            align-items: center;
            justify-content: center;
            font-weight: bold;
            font-size: 14px;
            box-shadow: 0 2px 5px rgba(0,0,0,0.3);
            border: 2px solid white;
        }
        
        /* Ensure popups don't overlap other markers */
        .leaflet-popup {
            z-index: 1000 !important;
        }
        
        .leaflet-marker-icon {
            z-index: 900;
        }
        
        .leaflet-marker-icon.active {
            z-index: 950;
        }
    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="logo-section">
                <img src="img/logos.png" alt="DTI Logo" class="logo">
                <div class="title-section">
                    <h1>Department of Trade and Industry</h1>
                    <p>Cebu Province Office Locator System</p>
                </div>
            </div>
            <div class="header-actions">
                <button class="btn btn-primary" onclick="toggleAddOffice()">
                    <i class="fas fa-plus"></i> Add DTI Office
                </button>
            </div>
        </div>
    </header>

    <!-- Main Content -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Search Section -->
            <div class="search-section">
                <h3><i class="fas fa-search"></i> Find DTI Offices</h3>
                <div class="search-controls">
                    <input type="text" id="searchInput" placeholder="Search by office name or location..." class="search-input">
                    <div class="form-row">
                        <select id="cityFilter" class="filter-select">
                            <option value="">All Cities/Municipalities</option>
                            <option value="Cebu City">Cebu City</option>
                            <option value="Mandaue City">Mandaue City</option>
                            <option value="Lapu-Lapu City">Lapu-Lapu City</option>
                            <option value="Talisay City">Talisay City</option>
                            <option value="Danao City">Danao City</option>
                            <option value="Toledo City">Toledo City</option>
                            <option value="Bogo City">Bogo City</option>
                            <option value="Carcar City">Carcar City</option>
                            <option value="Naga City">Naga City</option>
                        </select>
                        <select id="officeTypeFilter" class="filter-select">
                            <option value="">All Office Types</option>
                            <option value="Regional Office">Regional Office</option>
                            <option value="Provincial Office">Provincial Office</option>
                            <option value="Field Office">Field Office</option>
                            <option value="Extension Office">Extension Office</option>
                        </select>
                    </div>
                    <button class="btn btn-secondary" onclick="clearFilters()">
                        <i class="fas fa-times"></i> Clear Filters
                    </button>
                </div>
            </div>

            <!-- Add Office Form -->
            <div class="add-office-section" id="addOfficeSection" style="display: none;">
                <h3><i class="fas fa-building"></i> Add New DTI Office</h3>
                <form id="addOfficeForm" enctype="multipart/form-data">
                    <div class="form-group">
                        <label for="office_name">Office Name <span class="required">*</span></label>
                        <input type="text" id="office_name" name="office_name" required class="form-input" placeholder="Enter complete office name">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="office_type">Office Type <span class="required">*</span></label>
                            <select id="office_type" name="office_type" required class="form-select">
                                <option value="">Select type</option>
                                <option value="Regional Office">Regional Office</option>
                                <option value="Provincial Office">Provincial Office</option>
                                <option value="Field Office">Field Office</option>
                                <option value="Extension Office">Extension Office</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="city">City/Municipality <span class="required">*</span></label>
                            <select id="city" name="city" required class="form-select">
                                <option value="">Select city/municipality</option>
                                <option value="Cebu City">Cebu City</option>
                                <option value="Mandaue City">Mandaue City</option>
                                <option value="Lapu-Lapu City">Lapu-Lapu City</option>
                                <option value="Talisay City">Talisay City</option>
                                <option value="Danao City">Danao City</option>
                                <option value="Toledo City">Toledo City</option>
                                <option value="Bogo City">Bogo City</option>
                                <option value="Carcar City">Carcar City</option>
                                <option value="Naga City">Naga City</option>
                                <option value="Other">Other Municipality</option>
                            </select>
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="address">Complete Address <span class="required">*</span></label>
                        <textarea id="address" name="address" required class="form-textarea" rows="2" placeholder="Enter full street address, city, province"></textarea>
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="contact_number">Contact Number</label>
                            <input type="text" id="contact_number" name="contact_number" class="form-input" placeholder="e.g., (032) 123-4567">
                        </div>
                        <div class="form-group">
                            <label for="email">Email Address</label>
                            <input type="email" id="email" name="email" class="form-input" placeholder="e.g., office@dti.gov.ph">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="office_head">Office Head/Director</label>
                        <input type="text" id="office_head" name="office_head" class="form-input" placeholder="Full name and position">
                    </div>

                    <div class="form-row">
                        <div class="form-group">
                            <label for="formLatitude">Latitude <span class="required">*</span></label>
                            <input type="text" name="latitude" id="formLatitude" readonly class="form-input" placeholder="Click on map to set location">
                        </div>
                        <div class="form-group">
                            <label for="formLongitude">Longitude <span class="required">*</span></label>
                            <input type="text" name="longitude" id="formLongitude" readonly class="form-input" placeholder="Click on map to set location">
                        </div>
                    </div>

                    <div class="form-group">
                        <label for="description">Office Description</label>
                        <textarea id="description" name="description" class="form-textarea" rows="3" placeholder="Brief description of the office and its functions"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="services_offered">Services Offered</label>
                        <textarea id="services_offered" name="services_offered" class="form-textarea" rows="2" placeholder="e.g., Business Registration, Trade Promotion, Consumer Protection"></textarea>
                    </div>

                    <div class="form-group">
                        <label for="office_hours">Office Hours</label>
                        <input type="text" id="office_hours" name="office_hours" class="form-input" value="8:00 AM - 5:00 PM" placeholder="e.g., 8:00 AM - 5:00 PM, Monday to Friday">
                    </div>

                    <div class="form-group">
                        <label for="office_image">Office Image</label>
                        <input type="file" id="office_image" name="office_image" accept="image/*" class="form-file">
                        <small class="form-help">Upload an image of the DTI office (JPEG or PNG, max 5MB)</small>
                    </div>

                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Office
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="toggleAddOffice()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>

            <!-- Office List -->
            <div class="office-list-section">
                <h3><i class="fas fa-list"></i> DTI Offices (<span id="officeCount">4</span>)</h3>
                <div id="officeListEmpty" style="display: none;">
                    <div class="alert alert-warning">
                        <i class="fas fa-info-circle"></i>
                        <span>No offices found matching your search criteria. Try adjusting your filters.</span>
                    </div>
                </div>
                <div class="office-list" id="officeList">
                    <!-- Sample Office Items (for demonstration) -->
                    <div class="office-item" onclick="viewOfficeDetails(1)">
                        <div class="office-header">
                            <div class="office-name">DTI Region VII - Regional Office</div>
                            <div class="office-type">Regional Office</div>
                        </div>
                        <div class="office-region cebu-region">Cebu City</div>
                                                <div class="office-address">3F LDM Building, MJ Cuenco Avenue, Cebu City</div>
                        <div class="office-actions">
                            <button class="btn btn-primary" onclick="viewOfficeDetails(1); event.stopPropagation();">
                                <i class="fas fa-info-circle"></i> View Details
                            </button>
                            <button class="btn btn-secondary" onclick="editOffice(1); event.stopPropagation();">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                    
                    <div class="office-item" onclick="viewOfficeDetails(2)">
                        <div class="office-header">
                            <div class="office-name">DTI Cebu Provincial Office</div>
                            <div class="office-type">Provincial Office</div>
                        </div>
                        <div class="office-region cebu-region">Cebu City</div>
                        <div class="office-address">4F Robinsons Galleria Cebu, Gen. Maxilom Ave., Cebu City</div>
                        <div class="office-actions">
                            <button class="btn btn-primary" onclick="viewOfficeDetails(2); event.stopPropagation();">
                                <i class="fas fa-info-circle"></i> View Details
                            </button>
                            <button class="btn btn-secondary" onclick="editOffice(2); event.stopPropagation();">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                    
                    <div class="office-item" onclick="viewOfficeDetails(3)">
                        <div class="office-header">
                            <div class="office-name">DTI Mandaue Field Office</div>
                            <div class="office-type">Field Office</div>
                        </div>
                        <div class="office-region cebu-region">Mandaue City</div>
                        <div class="office-address">2F Mandaue City Hall, North Reclamation Area, Mandaue City</div>
                        <div class="office-actions">
                            <button class="btn btn-primary" onclick="viewOfficeDetails(3); event.stopPropagation();">
                                <i class="fas fa-info-circle"></i> View Details
                            </button>
                            <button class="btn btn-secondary" onclick="editOffice(3); event.stopPropagation();">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                    
                    <div class="office-item" onclick="viewOfficeDetails(4)">
                        <div class="office-header">
                            <div class="office-name">DTI Lapu-Lapu Extension Office</div>
                            <div class="office-type">Extension Office</div>
                        </div>
                        <div class="office-region cebu-region">Lapu-Lapu City</div>
                        <div class="office-address">1F Lapu-Lapu City Hall, Pajo, Lapu-Lapu City</div>
                        <div class="office-actions">
                            <button class="btn btn-primary" onclick="viewOfficeDetails(4); event.stopPropagation();">
                                <i class="fas fa-info-circle"></i> View Details
                            </button>
                            <button class="btn btn-secondary" onclick="editOffice(4); event.stopPropagation();">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                        </div>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="map-container">
            <div class="map-controls">
                <button class="map-control-btn" onclick="toggleSidebar()" title="Toggle Sidebar">
                    <i class="fas fa-bars"></i>
                </button>
                <button class="map-control-btn" onclick="resetMapView()" title="Reset Map View">
                    <i class="fas fa-home"></i>
                </button>
                <button class="map-control-btn" onclick="toggleFullscreen()" title="Toggle Fullscreen">
                    <i class="fas fa-expand"></i>
                </button>
            </div>
                        <div class="map" id="map"></div>
            <div class="map-legend">
                <h4>Map Legend</h4>
                <div class="legend-item">
                    <span class="legend-marker regional"></span>
                    <span>Regional Office</span>
                </div>
                <div class="legend-item">
                    <span class="legend-marker provincial"></span>
                    <span>Provincial Office</span>
                </div>
                <div class="legend-item">
                    <span class="legend-marker field"></span>
                    <span>Field Office</span>
                </div>
                <div class="legend-item">
                    <span class="legend-marker extension"></span>
                    <span>Extension Office</span>
                </div>
            </div>
        </div>
    </div>

    <!-- Office Details Modal -->
    <div class="modal" id="officeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Office Details</h2>
                <button class="modal-close" onclick="closeModal()" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Sample Office Details (for demonstration) -->
                <div class="office-detail-grid">
                    <div class="office-detail-header">
                        <img src="img/sample-office.jpg" alt="DTI Region VII - Regional Office" class="office-detail-image">
                        <h3 class="office-detail-title">DTI Region VII - Regional Office</h3>
                        <div class="office-detail-badges">
                            <span class="badge badge-primary">Regional Office</span>
                            <span class="badge badge-warning">Cebu City</span>
                        </div>
                    </div>
                    
                    <div class="office-detail-info">
                        <div class="info-section">
                            <h4><i class="fas fa-map-marker-alt"></i> Location Information</h4>
                            <div class="info-item">
                                <i class="fas fa-building"></i>
                                <div>
                                    <strong>Address:</strong>
                                    <div>3F LDM Building, MJ Cuenco Avenue, Cebu City</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-phone"></i>
                                <div>
                                    <strong>Contact Number:</strong>
                                    <div>(032) 253-3926</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-envelope"></i>
                                <div>
                                    <strong>Email:</strong>
                                    <div>region7@dti.gov.ph</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-clock"></i>
                                <div>
                                    <strong>Office Hours:</strong>
                                    <div>8:00 AM - 5:00 PM, Monday to Friday</div>
                                </div>
                            </div>
                            <div class="info-item">
                                <i class="fas fa-user-tie"></i>
                                <div>
                                    <strong>Office Head:</strong>
                                    <div>Dir. Maria Elena Arbon</div>
                                </div>
                            </div>
                        </div>
                        
                        <div class="info-section">
                            <h4><i class="fas fa-info-circle"></i> About the Office</h4>
                            <p>The DTI Region VII Regional Office oversees all trade and industry activities within Central Visayas. It provides various services to businesses and consumers in Cebu, Bohol, Negros Oriental, and Siquijor.</p>
                        </div>
                        
                        <div class="info-section">
                            <h4><i class="fas fa-briefcase"></i> Services Offered</h4>
                            <ul>
                                <li>Business Name Registration</li>
                                <li>Consumer Protection</li>
                                <li>Trade Promotion</li>
                                <li>MSME Development</li>
                                <li>Export Assistance</li>
                            </ul>
                        </div>
                    </div>
                    
                    <!-- Staff Section -->
                    <div class="office-staff-section" id="officeStaffSection">
                        <div class="staff-section-header">
                            <h3><i class="fas fa-users"></i> Office Staff</h3>
                            <div class="staff-controls">
                                <button class="btn btn-primary btn-sm" onclick="openAddStaffModal()">
                                    <i class="fas fa-user-plus"></i> Add Staff
                                </button>
                            </div>
                        </div>
                        
                        <div class="staff-list" id="staffList">
                            <!-- Sample Staff Cards (for demonstration) -->
                            <div class="staff-card" data-id="1">
                                <div class="staff-header">
                                    <img src="img/sample-staff1.jpg" alt="Maria Santos" class="staff-photo">
                                    <div class="staff-name-section">
                                        <h4 class="staff-name">Maria Santos</h4>
                                        <div class="staff-position">Division Chief, Business Development</div>
                                        <span class="staff-type staff-type-a">NC Type A</span>
                                    </div>
                                </div>
                                <div class="staff-body">
                                    <div class="staff-info">
                                        <div class="staff-info-item">
                                            <i class="fas fa-phone"></i>
                                            <span>(032) 253-3926 loc. 101</span>
                                        </div>
                                        <div class="staff-info-item">
                                            <i class="fas fa-envelope"></i>
                                            <span>maria.santos@dti.gov.ph</span>
                                        </div>
                                    </div>
                                    <div class="staff-actions">
                                        <button class="btn btn-primary" onclick="viewStaffDetails(1)">
                                            <i class="fas fa-info-circle"></i> View Details
                                        </button>
                                        <button class="btn btn-secondary" onclick="editStaffMember(1)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="staff-card" data-id="2">
                                <div class="staff-header">
                                    <div class="staff-photo-placeholder">
                                        <i class="fas fa-user"></i>
                                    </div>
                                    <div class="staff-name-section">
                                        <h4 class="staff-name">Roberto Reyes</h4>
                                        <div class="staff-position">Consumer Protection Officer</div>
                                        <span class="staff-type staff-type-b">NC Type B</span>
                                    </div>
                                </div>
                                <div class="staff-body">
                                    <div class="staff-info">
                                        <div class="staff-info-item">
                                            <i class="fas fa-phone"></i>
                                            <span>(032) 253-3926 loc. 102</span>
                                        </div>
                                        <div class="staff-info-item">
                                            <i class="fas fa-envelope"></i>
                                            <span>roberto.reyes@dti.gov.ph</span>
                                        </div>
                                    </div>
                                    <div class="staff-actions">
                                        <button class="btn btn-primary" onclick="viewStaffDetails(2)">
                                            <i class="fas fa-info-circle"></i> View Details
                                        </button>
                                        <button class="btn btn-secondary" onclick="editStaffMember(2)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="staff-card" data-id="3">
                                <div class="staff-header">
                                    <img src="img/sample-staff3.jpg" alt="Elena Cruz" class="staff-photo">
                                    <div class="staff-name-section">
                                        <h4 class="staff-name">Elena Cruz</h4>
                                        <div class="staff-position">MSME Development Specialist</div>
                                        <span class="staff-type staff-type-a">NC Type A</span>
                                    </div>
                                </div>
                                <div class="staff-body">
                                    <div class="staff-info">
                                        <div class="staff-info-item">
                                            <i class="fas fa-phone"></i>
                                            <span>(032) 253-3926 loc. 103</span>
                                        </div>
                                        <div class="staff-info-item">
                                            <i class="fas fa-envelope"></i>
                                            <span>elena.cruz@dti.gov.ph</span>
                                        </div>
                                    </div>
                                    <div class="staff-actions">
                                        <button class="btn btn-primary" onclick="viewStaffDetails(3)">
                                            <i class="fas fa-info-circle"></i> View Details
                                        </button>
                                        <button class="btn btn-secondary" onclick="editStaffMember(3)">
                                            <i class="fas fa-edit"></i> Edit
                                        </button>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- Staff Modal -->
    <div class="modal" id="staffModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="staffModalTitle">Add Staff Member</h2>
                <button class="modal-close" onclick="closeStaffModal()" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body">
                <form id="staffForm" enctype="multipart/form-data">
                    <input type="hidden" id="staffId" name="staff_id" value="">
                    <input type="hidden" id="staffOfficeId" name="office_id" value="1">
                    
                    <div class="form-group">
                        <label for="staffName">Full Name <span class="required">*</span></label>
                        <input type="text                        <input type="text" id="staffName" name="full_name" required class="form-input" placeholder="Enter staff member's full name">
                    </div>
                    
                    <div class="form-group">
                        <label for="staffPosition">Position <span class="required">*</span></label>
                        <input type="text" id="staffPosition" name="position" required class="form-input" placeholder="Enter job title/position">
                    </div>
                    
                    <div class="form-group">
                        <label for="staffType">Staff Type <span class="required">*</span></label>
                        <select id="staffType" name="staff_type" required class="form-select">
                            <option value="">Select type</option>
                            <option value="NC Type A">NC Type A</option>
                            <option value="NC Type B">NC Type B</option>
                        </select>
                    </div>
                    
                    <div class="form-group">
                        <label for="staffServices">Services Offered</label>
                        <textarea id="staffServices" name="services_offered" class="form-textarea" rows="2" placeholder="List the services this staff member handles"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="staffContact">Contact Number</label>
                            <input type="text" id="staffContact" name="contact_number" class="form-input" placeholder="e.g., (032) 253-3926 loc. 101">
                        </div>
                        <div class="form-group">
                            <label for="staffEmail">Email Address</label>
                            <input type="email" id="staffEmail" name="email" class="form-input" placeholder="e.g., name@dti.gov.ph">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="staffBio">Biography</label>
                        <textarea id="staffBio" name="bio" class="form-textarea" rows="3" placeholder="Brief background and expertise"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="staffPhoto">Staff Photo</label>
                        <input type="file" id="staffPhoto" name="photo" accept="image/*" class="form-file">
                        <small class="form-help">Upload a professional photo (JPEG or PNG, max 2MB)</small>
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Staff Member
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeStaffModal()">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Staff Details Modal -->
    <div class="modal" id="staffDetailsModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="staffDetailsTitle">Staff Details</h2>
                <button class="modal-close" onclick="closeStaffDetailsModal()" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body" id="staffDetailsBody">
                <!-- Sample Staff Details (for demonstration) -->
                <div class="staff-detail-header">
                    <img src="img/sample-staff1.jpg" alt="Maria Santos" class="staff-detail-photo">
                    <div class="staff-detail-info">
                        <h3 class="staff-detail-name">Maria Santos</h3>
                        <div class="staff-detail-position">Division Chief, Business Development</div>
                        <div class="staff-detail-badges">
                            <span class="staff-type staff-type-a">NC Type A</span>
                        </div>
                    </div>
                </div>
                
                <div class="staff-detail-content">
                    <div class="staff-detail-section">
                        <h4><i class="fas fa-id-card"></i> Contact Information</h4>
                        <div class="staff-detail-item">
                            <div class="staff-detail-label">Phone:</div>
                            <div class="staff-detail-value">(032) 253-3926 loc. 101</div>
                        </div>
                        <div class="staff-detail-item">
                            <div class="staff-detail-label">Email:</div>
                            <div class="staff-detail-value">maria.santos@dti.gov.ph</div>
                        </div>
                    </div>
                    
                    <div class="staff-detail-section">
                        <h4><i class="fas fa-briefcase"></i> Services Offered</h4>
                        <ul class="staff-services-list">
                            <li>Business Name Registration</li>
                            <li>MSME Development</li>
                            <li>Business Advisory</li>
                            <li>Entrepreneurship Training</li>
                        </ul>
                    </div>
                    
                    <div class="staff-detail-section">
                        <h4><i class="fas fa-user-circle"></i> Biography</h4>
                        <p class="staff-bio">
                            Maria Santos has been with DTI for over 10 years, specializing in business development and MSME support. 
                            She holds a Master's degree in Business Administration from the University of San Carlos and has 
                            extensive experience in helping small businesses grow and succeed in competitive markets.
                        </p>
                    </div>
                </div>
            </div>
            <div class="modal-footer">
                <div class="modal-actions">
                    <button class="btn btn-secondary" id="editStaffBtn" onclick="editStaffMember(1)">
                        <i class="fas fa-edit"></i> Edit
                    </button>
                    <button class="btn btn-danger" id="deleteStaffBtn" onclick="confirmDeleteStaff(1)">
                        <i class="fas fa-trash-alt"></i> Delete
                    </button>
                </div>
            </div>
        </div>
    </div>

    <!-- Confirmation Modal -->
    <div class="modal" id="confirmationModal">
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h2 id="confirmationTitle">Confirm Action</h2>
                <button class="modal-close" onclick="closeConfirmationModal()" aria-label="Close modal">&times;</button>
            </div>
            <div class="modal-body" id="confirmationBody">
                <p>Are you sure you want to delete this staff member?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            </div>
            <div class="modal-footer">            
                <button class="btn btn-secondary" onclick="closeConfirmationModal()">
                    <i class="fas fa-times"></i> Cancel
                </button>
                <button class="btn btn-danger" id="confirmActionBtn">
                    <i class="fas fa-trash-alt"></i> Delete
                </button>
            </div>
        </div>
    </div>

    <!-- Loading Spinner -->
    <div class="loading-overlay" id="loadingOverlay">
        <div class="spinner"></div>
        <p>Loading DTI Cebu Office Map...</p>
    </div>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script>
        // Map initialization with Cebu focus
        let map;
        let currentMarker = null;
        let officeMarkers = [];
        
        // Cebu province boundaries (approximate)
        const CEBU_CENTER = [10.3157, 123.8854]; // Approximate center of Cebu City
        const MIN_ZOOM = 9;  // Minimum zoom level to restrict panning
        const MAX_ZOOM = 18; // Maximum zoom level
        const INITIAL_ZOOM = 10;
        
        // Cebu province bounds (approximate)
        const CEBU_BOUNDS = [
            [9.4, 123.0],  // Southwest corner
            [11.3, 124.5]  // Northeast corner
        ];
        
        // Initialize the map when the page loads
        document.addEventListener('DOMContentLoaded', function() {
            initMap();
            
            // Hide loading overlay after map loads
            setTimeout(function() {
                document.getElementById('loadingOverlay').style.display = 'none';
            }, 1500);
            
            // Initialize form event listeners
            document.getElementById('addOfficeForm').addEventListener('submit', handleOfficeFormSubmit);
            document.getElementById('staffForm').addEventListener('submit', handleStaffFormSubmit);
            
            // Initialize search and filter functionality
            document.getElementById('searchInput').addEventListener('input', filterOffices);
            document.getElementById('cityFilter').addEventListener('change', filterOffices);
            document.getElementById('officeTypeFilter').addEventListener('change', filterOffices);
        });
        
        function initMap() {
            // Create the map centered on Cebu
            map = L.map('map', {
                center: CEBU_CENTER,
                zoom: INITIAL_ZOOM,
                minZoom: MIN_ZOOM,
                maxZoom: MAX_ZOOM,
                maxBounds: CEBU_BOUNDS,
                maxBoundsViscosity: 1.0 // Prevents the map from being dragged outside bounds
            });
            
            // Add OpenStreetMap tile layer
            L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
                noWrap: true // Prevents the map from repeating across the x-axis
            }).addTo(map);
            
            // Add neighboring provinces with subtle styling
            addNeighboringProvinces();
            
            // Add sample office markers
            addSampleOfficeMarkers();
            
            // Add click event to set location for new offices
            map.on('click', function(e) {
                if (document.getElementById('addOfficeSection').style.display === 'block') {
                    setFormLocation(e.latlng.lat, e.latlng.lng);
                }
            });
        }
        
        function addNeighboringProvinces() {
            // Add neighboring provinces with very subtle gray styling
            const neighboringProvinces = [
                {
                    "type": "Feature",
                    "properties": {
                        "name": "Bohol Province",
                        "region": "Region VII"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [123.7, 9.5],
                            [124.5, 9.5],
                            [124.5, 10.2],
                            [123.7, 10.2],
                            [123.7, 9.5]
                        ]]
                    }
                },
                {
                    "type": "Feature",
                    "properties": {
                        "name": "Negros Oriental",
                        "region": "Region VII"
                    },
                    "geometry": {
                        "type": "Polygon",
                        "coordinates": [[
                            [122.8, 9.0],
                            [123.4, 9.0],
                            [123.4, 10.5],
                            [122.8, 10.5],
                            [122.8, 9.0]
                        ]]
                    }
                }
            ];
            
            // Add neighboring provinces with very subtle gray styling
            L.geoJSON(neighboringProvinces, {
                style: {
                    color: '#9e9e9e',
                    weight: 1,
                    opacity: 0.3,
                    fillColor: '#f5f5f5',
                    fillOpacity: 0.1
                }
            }).addTo(map);
        }
        
        function createNCMarkerIcon(officeType) {
            // Create a custom HTML element for the marker
            const markerHtml = document.createElement('div');
            markerHtml.className = 'nc-marker-icon';
            
            // Set background color based on office type
            let bgColor = '#1976D2'; // Default blue
            
            switch(officeType) {
                case 'Regional Office':
                    bgColor = '#1565C0'; // Darker blue
                    break;
                case 'Provincial Office':
                    bgColor = '#2E7D32'; // Green
                    break;
                case 'Field Office':
                    bgColor = '#EF6C00'; // Orange
                    break;
                case 'Extension Office':
                    bgColor = '#6A1B9A'; // Purple
                    break;
            }
            
            markerHtml.style.backgroundColor = bgColor;
            
            // Add NC text
            markerHtml.innerHTML = 'NC';
            
            // Create a custom divIcon
            return L.divIcon({
                html: markerHtml,
                className: 'custom-nc-marker',
                iconSize: [36, 36],
                iconAnchor: [18, 36],
                popupAnchor: [0, -36]
            });
        }
        
        function addSampleOfficeMarkers() {
            // Sample office data for Cebu
            const offices = [
                {
                    id: 1,
                    name: "DTI Region VII - Regional Office",
                    type: "Regional Office",
                    lat: 10.3157,
                    lng: 123.8854,
                    address: "3F LDM Building, MJ Cuenco Avenue, Cebu City",
                    city: "Cebu City",
                    contact: "(032) 253-3926",
                    email: "region7@dti.gov.ph",
                    head: "Dir. Maria Elena Arbon",
                    hours: "8:00 AM - 5:00 PM, Monday to Friday",
                    description: "The DTI Region VII Regional Office oversees all trade and industry activities within Central Visayas. It provides various services to businesses and consumers in Cebu, Bohol, Negros Oriental, and Siquijor.",
                    services: ["Business Name Registration", "Consumer Protection", "Trade Promotion", "MSME Development", "Export Assistance"]
                },
                {
                    id: 2,
                    name: "DTI Cebu Provincial Office",
                    type: "Provincial Office",
                    lat: 10.3116,
                    lng: 123.9120,
                    address: "4F Robinsons Galleria Cebu, Gen. Maxilom Ave., Cebu City",
                    city: "Cebu City",
                    contact: "(032) 255-6971",
                    email: "cebu@dti.gov.ph",
                    head: "Dir. Rose Mae Quiñanola",
                    hours: "8:00 AM - 5:00 PM, Monday to Friday",
                    description: "The DTI Cebu Provincial Office serves businesses and consumers in Cebu Province, providing various services to support local economic development.",
                                        services: ["Business Name Registration", "Consumer Protection", "MSME Development", "Negosyo Center Services"]
                },
                {
                    id: 3,
                    name: "DTI Mandaue Field Office",
                    type: "Field Office",
                    lat: 10.3231,
                    lng: 123.9400,
                    address: "2F Mandaue City Hall, North Reclamation Area, Mandaue City",
                    city: "Mandaue City",
                    contact: "(032) 346-7252",
                    email: "mandaue@dti.gov.ph",
                    head: "Mr. John Santos",
                    hours: "8:00 AM - 5:00 PM, Monday to Friday",
                    description: "The DTI Mandaue Field Office provides services to businesses and consumers in Mandaue City, focusing on local economic development and consumer protection.",
                    services: ["Business Name Registration", "Consumer Complaints", "MSME Development", "Business Advisory"]
                },
                {
                    id: 4,
                    name: "DTI Lapu-Lapu Extension Office",
                    type: "Extension Office",
                    lat: 10.3100,
                    lng: 123.9500,
                    address: "1F Lapu-Lapu City Hall, Pajo, Lapu-Lapu City",
                    city: "Lapu-Lapu City",
                    contact: "(032) 340-1456",
                    email: "lapulapu@dti.gov.ph",
                    head: "Ms. Elena Gomez",
                    hours: "8:00 AM - 5:00 PM, Monday to Friday",
                    description: "The DTI Lapu-Lapu Extension Office serves businesses and consumers in Lapu-Lapu City, providing support for local entrepreneurs and ensuring consumer protection.",
                    services: ["Business Name Registration", "Consumer Protection", "MSME Development", "Export Assistance"]
                }
            ];
            
            // Clear existing markers
            officeMarkers.forEach(marker => map.removeLayer(marker));
            officeMarkers = [];
            
            // Add markers for each office
            offices.forEach(office => {
                // Create custom NC marker icon
                const markerIcon = createNCMarkerIcon(office.type);
                
                // Get badge class based on office type
                let badgeClass = 'regional-office';
                switch(office.type) {
                    case 'Provincial Office':
                        badgeClass = 'provincial-office';
                        break;
                    case 'Field Office':
                        badgeClass = 'field-office';
                        break;
                    case 'Extension Office':
                        badgeClass = 'extension-office';
                        break;
                }
                
                // Create improved popup content with better formatting
                const popupContent = `
                    <div class="marker-popup">
                        <div class="popup-header">
                            <h3 class="popup-title">${office.name}</h3>
                            <span class="popup-badge ${badgeClass}">${office.type}</span>
                        </div>
                        <div class="popup-body">
                            <div class="popup-info">
                                <div class="popup-info-item">
                                    <i class="fas fa-map-marker-alt"></i>
                                    <span>${office.address}</span>
                                </div>
                                <div class="popup-info-item">
                                    <i class="fas fa-phone"></i>
                                    <span>${office.contact}</span>
                                </div>
                            </div>
                        </div>
                        <div class="popup-footer">
                            <button class="btn btn-primary popup-btn" onclick="viewOfficeDetails(${office.id})">
                                <i class="fas fa-info-circle"></i> View Details
                            </button>
                        </div>
                    </div>
                `;
                
                const marker = L.marker([office.lat, office.lng], {icon: markerIcon})
                    .addTo(map)
                    .bindPopup(popupContent, {
                        maxWidth: 300,
                        minWidth: 280,
                        className: 'office-popup',
                        autoPanPadding: [50, 50],
                        offset: [0, -20]
                    });
                
                // Store reference to marker and office data
                marker.officeId = office.id;
                marker.officeData = office;
                officeMarkers.push(marker);
                
                // Add click event to zoom to office
                marker.on('click', function() {
                    // Make this marker appear on top of others
                    this._icon.classList.add('active');
                    
                    // Remove active class from other markers
                    officeMarkers.forEach(m => {
                        if (m !== this && m._icon) {
                            m._icon.classList.remove('active');
                        }
                    });
                    
                    zoomToOffice(office.lat, office.lng);
                });
            });
        }
        
        function setFormLocation(lat, lng) {
            // Check if the location is within Cebu bounds
            if (!isWithinCebuBounds(lat, lng)) {
                alert("Please select a location within Cebu province.");
                return;
            }
            
            // Update form fields with selected coordinates
            document.getElementById('formLatitude').value = lat.toFixed(6);
            document.getElementById('formLongitude').value = lng.toFixed(6);
            
            // Add or update marker on the map
            if (currentMarker) {
                map.removeLayer(currentMarker);
            }
            
            // Create a custom NC marker for the new office
            const newOfficeIcon = createNCMarkerIcon('New Office');
            
            currentMarker = L.marker([lat, lng], {
                icon: newOfficeIcon,
                draggable: true
            }).addTo(map);
            
            // Update form when marker is dragged
            currentMarker.on('dragend', function(e) {
                const position = e.target.getLatLng();
                
                // Check if the new position is within Cebu bounds
                if (!isWithinCebuBounds(position.lat, position.lng)) {
                    alert("Please keep the marker within Cebu province.");
                    // Reset marker to previous position
                    currentMarker.setLatLng([lat, lng]);
                    return;
                }
                
                document.getElementById('formLatitude').value = position.lat.toFixed(6);
                document.getElementById('formLongitude').value = position.lng.toFixed(6);
            });
        }
        
        function isWithinCebuBounds(lat, lng) {
            // Check if coordinates are within the defined Cebu bounds
            return lat >= CEBU_BOUNDS[0][0] && lat <= CEBU_BOUNDS[1][0] &&
                   lng >= CEBU_BOUNDS[0][1] && lng <= CEBU_BOUNDS[1][1];
        }
        
        function zoomToOffice(lat, lng) {
            // Zoom to the selected office
            map.setView([lat, lng], 16, {
                animate: true,
                duration: 1
            });
        }
        
        function resetMapView() {
            // Reset to default view of Cebu
            map.setView(CEBU_CENTER, INITIAL_ZOOM, {
                animate: true,
                duration: 1
            });
            
            // Close any open popups
            map.closePopup();
            
            // Remove active class from all markers
            officeMarkers.forEach(m => {
                if (m._icon) {
                    m._icon.classList.remove('active');
                }
            });
        }
        
        function toggleSidebar() {
            const sidebar = document.getElementById('sidebar');
            sidebar.classList.toggle('collapsed');
            
            // Update map size after sidebar toggle
            setTimeout(function() {
                map.invalidateSize();
            }, 300);
        }
        
        function toggleFullscreen() {
            const mapContainer = document.querySelector('.map-container');
            
            if (!document.fullscreenElement) {
                mapContainer.requestFullscreen().catch(err => {
                    console.error(`Error attempting to enable fullscreen: ${err.message}`);
                });
            } else {
                document.exitFullscreen();
            }
        }
        
        function toggleAddOffice() {
            const addOfficeSection = document.getElementById('addOfficeSection');
            const isVisible = addOfficeSection.style.display === 'block';
            
            addOfficeSection.style.display = isVisible ? 'none' : 'block';
            
            // Clear form and marker if closing
            if (isVisible && currentMarker) {
                map.removeLayer(currentMarker);
                currentMarker = null;
                document.getElementById('addOfficeForm').reset();
            }
                        
            // Scroll to form if opening
            if (!isVisible) {
                addOfficeSection.scrollIntoView({ behavior: 'smooth' });
            }
        }
        
        function viewOfficeDetails(officeId) {
            // Find the office marker and zoom to it
            const marker = officeMarkers.find(m => m.officeId === officeId);
            if (marker) {
                const position = marker.getLatLng();
                const office = marker.officeData;
                
                // Zoom to the office location
                zoomToOffice(position.lat, position.lng);
                
                // Make this marker appear on top of others
                if (marker._icon) {
                    marker._icon.classList.add('active');
                    
                    // Remove active class from other markers
                    officeMarkers.forEach(m => {
                        if (m !== marker && m._icon) {
                            m._icon.classList.remove('active');
                        }
                    });
                }
                
                // Build services list HTML
                let servicesHtml = '';
                if (office.services && office.services.length > 0) {
                    servicesHtml = office.services.map(service => `<li>${service}</li>`).join('');
                } else {
                    servicesHtml = '<li>No specific services listed.</li>';
                }
                
                // Build office details HTML
                const officeDetailsHtml = `
                    <div class="office-detail-grid">
                        <div class="office-detail-header">
                            <img src="img/sample-office.jpg" alt="${office.name}" class="office-detail-image">
                            <h3 class="office-detail-title">${office.name}</h3>
                            <div class="office-detail-badges">
                                <span class="badge badge-primary">${office.type}</span>
                                <span class="badge badge-warning">${office.city}</span>
                            </div>
                        </div>
                        
                        <div class="office-detail-info">
                            <div class="info-section">
                                <h4><i class="fas fa-map-marker-alt"></i> Location Information</h4>
                                <div class="info-item">
                                    <i class="fas fa-building"></i>
                                    <div>
                                        <strong>Address:</strong>
                                        <div>${office.address}</div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-phone"></i>
                                    <div>
                                        <strong>Contact Number:</strong>
                                        <div>${office.contact}</div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-envelope"></i>
                                    <div>
                                        <strong>Email:</strong>
                                        <div>${office.email}</div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-clock"></i>
                                    <div>
                                        <strong>Office Hours:</strong>
                                        <div>${office.hours}</div>
                                    </div>
                                </div>
                                <div class="info-item">
                                    <i class="fas fa-user-tie"></i>
                                    <div>
                                        <strong>Office Head:</strong>
                                        <div>${office.head}</div>
                                    </div>
                                </div>
                            </div>
                            
                            <div class="info-section">
                                <h4><i class="fas fa-info-circle"></i> About the Office</h4>
                                <p>${office.description}</p>
                            </div>
                            
                            <div class="info-section">
                                <h4><i class="fas fa-briefcase"></i> Services Offered</h4>
                                <ul>
                                    ${servicesHtml}
                                </ul>
                            </div>
                        </div>
                        
                        <!-- Staff Section -->
                        <div class="office-staff-section" id="officeStaffSection">
                            <div class="staff-section-header">
                                <h3><i class="fas fa-users"></i> Office Staff</h3>
                                <div class="staff-controls">
                                    <button class="btn btn-primary btn-sm" onclick="openAddStaffModal()">
                                        <i class="fas fa-user-plus"></i> Add Staff
                                    </button>
                                </div>
                            </div>
                            
                            <div class="staff-list" id="staffList">
                                <!-- Sample Staff Cards (for demonstration) -->
                                <div class="staff-card" data-id="1">
                                    <div class="staff-header">
                                        <img src="img/sample-staff1.jpg" alt="Maria Santos" class="staff-photo">
                                        <div class="staff-name-section">
                                            <h4 class="staff-name">Maria Santos</h4>
                                            <div class="staff-position">Division Chief, Business Development</div>
                                            <span class="staff-type staff-type-a">NC Type A</span>
                                        </div>
                                    </div>
                                    <div class="staff-body">
                                        <div class="staff-info">
                                            <div class="staff-info-item">
                                                <i class="fas fa-phone"></i>
                                                <span>(032) 253-3926 loc. 101</span>
                                            </div>
                                            <div class="staff-info-item">
                                                <i class="fas fa-envelope"></i>
                                                <span>maria.santos@dti.gov.ph</span>
                                            </div>
                                        </div>
                                        <div class="staff-actions">
                                            <button class="btn btn-primary" onclick="viewStaffDetails(1)">
                                                <i class="fas fa-info-circle"></i> View Details
                                            </button>
                                            <button class="btn btn-secondary" onclick="editStaffMember(1)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="staff-card" data-id="2">
                                    <div class="staff-header">
                                        <div class="staff-photo-placeholder">
                                            <i class="fas fa-user"></i>
                                                                                </div>
                                        <div class="staff-name-section">
                                            <h4 class="staff-name">Roberto Reyes</h4>
                                            <div class="staff-position">Consumer Protection Officer</div>
                                            <span class="staff-type staff-type-b">NC Type B</span>
                                        </div>
                                    </div>
                                    <div class="staff-body">
                                        <div class="staff-info">
                                            <div class="staff-info-item">
                                                <i class="fas fa-phone"></i>
                                                <span>(032) 253-3926 loc. 102</span>
                                            </div>
                                            <div class="staff-info-item">
                                                <i class="fas fa-envelope"></i>
                                                <span>roberto.reyes@dti.gov.ph</span>
                                            </div>
                                        </div>
                                        <div class="staff-actions">
                                            <button class="btn btn-primary" onclick="viewStaffDetails(2)">
                                                <i class="fas fa-info-circle"></i> View Details
                                            </button>
                                            <button class="btn btn-secondary" onclick="editStaffMember(2)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                                
                                <div class="staff-card" data-id="3">
                                    <div class="staff-header">
                                        <img src="img/sample-staff3.jpg" alt="Elena Cruz" class="staff-photo">
                                        <div class="staff-name-section">
                                            <h4 class="staff-name">Elena Cruz</h4>
                                            <div class="staff-position">MSME Development Specialist</div>
                                            <span class="staff-type staff-type-a">NC Type A</span>
                                        </div>
                                    </div>
                                    <div class="staff-body">
                                        <div class="staff-info">
                                            <div class="staff-info-item">
                                                <i class="fas fa-phone"></i>
                                                <span>(032) 253-3926 loc. 103</span>
                                            </div>
                                            <div class="staff-info-item">
                                                <i class="fas fa-envelope"></i>
                                                <span>elena.cruz@dti.gov.ph</span>
                                            </div>
                                        </div>
                                        <div class="staff-actions">
                                            <button class="btn btn-primary" onclick="viewStaffDetails(3)">
                                                <i class="fas fa-info-circle"></i> View Details
                                            </button>
                                            <button class="btn btn-secondary" onclick="editStaffMember(3)">
                                                <i class="fas fa-edit"></i> Edit
                                            </button>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                `;
                
                // Update modal content
                document.getElementById('modalTitle').textContent = office.name;
                document.getElementById('modalBody').innerHTML = officeDetailsHtml;
            }
            
            // Show office details modal
            document.getElementById('officeModal').style.display = 'block';
            
            // Set the current office ID for staff operations
            document.getElementById('staffOfficeId').value = officeId;
        }
        
        function editOffice(officeId) {
            // Find the office data
            const marker = officeMarkers.find(m => m.officeId === officeId);
            if (marker) {
                const position = marker.getLatLng();
                const office = marker.officeData;
                
                // Show the add office form
                document.getElementById('addOfficeSection').style.display = 'block';
                
                // Populate form with office data
                document.getElementById('office_name').value = office.name;
                document.getElementById('office_type').value = office.type;
                document.getElementById('city').value = office.city;
                document.getElementById('address').value = office.address;
                document.getElementById('contact_number').value = office.contact;
                document.getElementById('email').value = office.email;
                document.getElementById('office_head').value = office.head;
                document.getElementById('office_hours').value = office.hours;
                document.getElementById('description').value = office.description;
                
                if (office.services && office.services.length > 0) {
                    document.getElementById('services_offered').value = office.services.join(', ');
                }
                
                // Set form location
                setFormLocation(position.lat, position.lng);
                
                // Scroll to form
                document.getElementById('addOfficeSection').scrollIntoView({ behavior: 'smooth' });
                
                // Close the modal if open
                closeModal();
            }
        }
        
        function closeModal() {
            document.getElementById('officeModal').style.display = 'none';
        }
        
        function clearFilters() {
            document.getElementById('searchInput').value = '';
            document.getElementById('cityFilter').value = '';
            document.getElementById('officeTypeFilter').value = '';
            
            // Reset the office list display
            filterOffices();
        }
        
        function filterOffices() {
            const searchTerm = document.getElementById('searchInput').value.toLowerCase();
            const cityFilter = document.getElementById('cityFilter').value;
            const typeFilter = document.getElementById('officeTypeFilter').value;
            
            // Get all office items
            const officeItems = document.querySelectorAll('.office-item');
            let visibleCount = 0;
            
            // Filter office items
            officeItems.forEach(item => {
                const officeName = item.querySelector('.office-name').textContent.toLowerCase();
                const officeType = item.querySelector('.office-type').textContent;
                const officeCity = item.querySelector('.office-region').textContent;
                const officeAddress = item.querySelector('.office-address').textContent.toLowerCase();
                
                // Check if office matches all filters
                const matchesSearch = officeName.includes(searchTerm) || officeAddress.includes(searchTerm);
                const matchesCity = !cityFilter || officeCity.includes(cityFilter);
                const matchesType = !typeFilter || officeType === typeFilter;
                
                // Show or hide based on filter results
                if (matchesSearch && matchesCity && matchesType) {
                    item.style.display = 'block';
                    visibleCount++;
                } else {
                    item.style.display = 'none';
                }
            });
            
            // Update office count
            document.getElementById('officeCount').textContent = visibleCount;
            
            // Show/hide empty message
            document.getElementById('officeListEmpty').style.display = 
                visibleCount === 0 ? 'block' : 'none';
        }
        
        function handleOfficeFormSubmit(e) {
            e.preventDefault();
            
            // Validate form
            const form = e.target;
            if (!form.checkValidity()) {
                alert("Please fill all required fields.");
                return;
            }
            
            // Check if location is set
            const lat = document.getElementById('formLatitude').value;
            const lng = document.getElementById('formLongitude').value;
            if (!lat || !lng) {
                alert("Please select a location on the map for this office.");
                return;
            }
            
            // Get form data
            const formData = new FormData(form);
            
            // This would typically send data to the server
            // For now, just show a success message and close the form
            alert("Office information saved successfully!");
            toggleAddOffice();
            
            // Refresh the map with the new office (in a real app)
            // addSampleOfficeMarkers();
        }
        
        // Staff-related functions
        function openAddStaffModal() {
            document.getElementById('staffModalTitle').textContent = 'Add Staff Member';
            document.getElementById('staffId').value = '';
            document.getElementById('staffForm').reset();
            document.getElementById('staffModal').style.display = 'block';
        }
        
        function closeStaffModal() {
            document.getElementById('staffModal').style.display = 'none';
        }
        
        function viewStaffDetails(staffId) {
            // Sample staff data (in a real app, this would come from the server)
            const staffData = {
                1: {
                    id: 1,
                    name: "Maria Santos",
                    position: "Division Chief, Business Development",
                    type: "NC Type A",
                    contact: "(032) 253-3926 loc. 101",
                    email: "maria.santos@dti.gov.ph",
                    services: ["Business Name Registration", "MSME Development", "Business Advisory", "Entrepreneurship Training"],
                    bio: "Maria Santos has been with DTI for over 10 years, specializing in business development and MSME support. She holds a Master's degree in Business Administration from the University of San Carlos and has extensive experience in helping small businesses grow and succeed in competitive markets.",
                    photo: "img/sample-staff1.jpg"
                },
                2: {
                    id: 2,
                    name: "Roberto Reyes",
                    position: "Consumer Protection Officer",
                    type: "NC Type B",
                    contact: "(032) 253-3926 loc. 102",
                    email: "roberto.reyes@dti.gov.ph",
                    services: ["Consumer Complaints", "Product Standards", "Price Monitoring", "Fair Trade Laws Enforcement"],
                    bio: "Roberto Reyes has been working with DTI's Consumer Protection Group for 5 years. He specializes in handling consumer complaints and ensuring businesses comply with fair trade laws and product standards. He has a background in Law from the University of the Philippines.",
                    photo: ""
                },
                3: {
                    id: 3,
                    name: "Elena Cruz",
                    position: "MSME Development Specialist",
                    type: "NC Type A",
                    contact: "(032) 253-3926 loc. 103",
                    email: "elena.cruz@dti.gov.ph",
                    services: ["MSME Development", "Business Mentoring", "Access to Finance", "Market Access Programs"],
                    bio: "Elena Cruz is an expert in MSME development with over 8 years of experience at DTI. She has helped hundreds of small businesses access financing, improve their operations, and expand to new markets. She holds a degree in Business Economics from the University of Cebu.",
                    photo: "img/sample-staff3.jpg"
                }
            };
            
            // Get staff data
            const staff = staffData[staffId];
            if (!staff) {
                alert("Staff information not found.");
                return;
            }
            
            // Build services list HTML
            let servicesHtml = '';
            if (staff.services && staff.services.length > 0) {
                servicesHtml = staff.services.map(service => `<li>${service}</li>`).join('');
            } else {
                servicesHtml = '<li>No specific services listed.</li>';
            }
            
            // Determine photo HTML
            let photoHtml = '';
            if (staff.photo) {
                photoHtml = `<img src="${staff.photo}" alt="${staff.name}" class="staff-detail-photo">`;
            } else {
                photoHtml = `
                    <div class="staff-detail-photo-placeholder">
                        <i class="fas fa-user"></i>
                    </div>
                `;
            }
            
            // Build staff details HTML
            const staffDetailsHtml = `
                <div class="staff-detail-header">
                    ${photoHtml}
                    <div class="staff-detail-info">
                        <h3 class="staff-detail-name">${staff.name}</h3>
                        <div class="staff-detail-position">${staff.position}</div>
                        <div class="staff-detail-badges">
                            <span class="staff-type staff-type-${staff.type.includes('Type A') ? 'a' : 'b'}">${staff.type}</span>
                        </div>
                    </div>
                </div>
                
                <div class="staff-detail-content">
                    <div class="staff-detail-section">
                        <h4><i class="fas fa-id-card"></i> Contact Information</h4>
                        <div class="staff-detail-item">
                            <div class="staff-detail-label">Phone:</div>
                            <div class="staff-detail-value">${staff.contact}</div>
                        </div>
                        <div class="staff-detail-item">
                            <div class="staff-detail-label">Email:</div>
                            <div class="staff-detail-value">${staff.email}</div>
                        </div>
                    </div>
                    
                    <div class="staff-detail-section">
                        <h4><i class="fas fa-briefcase"></i> Services Offered</h4>
                        <ul class="staff-services-list">
                            ${servicesHtml}
                        </ul>
                    </div>
                    
                    <div class="staff-detail-section">
                        <h4><i class="fas fa-user-circle"></i> Biography</h4>
                        <p class="staff-bio">
                            ${staff.bio}
                        </p>
                    </div>
                </div>
            `;
            
            // Update modal content
            document.getElementById('staffDetailsTitle').textContent = `${staff.name} - ${staff.position}`;
            document.getElementById('staffDetailsBody').innerHTML = staffDetailsHtml;
            document.getElementById('editStaffBtn').setAttribute('onclick', `editStaffMember(${staffId})`);
            document.getElementById('deleteStaffBtn').setAttribute('onclick', `confirmDeleteStaff(${staffId})`);
            
            // Show the modal
            document.getElementById('staffDetailsModal').style.display = 'block';
        }
        
        function closeStaffDetailsModal() {
            document.getElementById('staffDetailsModal').style.display = 'none';
        }
        
        function editStaffMember(staffId) {
            // Sample staff data (in a real app, this would come from the server)
            const staffData = {
                1: {
                    id: 1,
                    name: "Maria Santos",
                    position: "Division Chief, Business Development",
                    type: "NC Type A",
                    contact: "(032) 253-3926 loc. 101",
                    email: "maria.santos@dti.gov.ph",
                    services: ["Business Name Registration", "MSME Development", "Business Advisory", "Entrepreneurship Training"],
                    bio: "Maria Santos has been with DTI for over 10 years, specializing in business development and MSME support. She holds a Master's degree in Business Administration from the University of San Carlos and has extensive experience in helping small businesses grow and succeed in competitive markets."
                },
                2: {
                    id: 2,
                    name: "Roberto Reyes",
                    position: "Consumer Protection Officer",
                    type: "NC Type B",
                    contact: "(032) 253-3926 loc. 102",
                    email: "roberto.reyes@dti.gov.ph",
                    services: ["Consumer Complaints", "Product Standards", "Price Monitoring", "Fair Trade Laws Enforcement"],
                                        bio: "Roberto Reyes has been working with DTI's Consumer Protection Group for 5 years. He specializes in handling consumer complaints and ensuring businesses comply with fair trade laws and product standards. He has a background in Law from the University of the Philippines."
                },
                3: {
                    id: 3,
                    name: "Elena Cruz",
                    position: "MSME Development Specialist",
                    type: "NC Type A",
                    contact: "(032) 253-3926 loc. 103",
                    email: "elena.cruz@dti.gov.ph",
                    services: ["MSME Development", "Business Mentoring", "Access to Finance", "Market Access Programs"],
                    bio: "Elena Cruz is an expert in MSME development with over 8 years of experience at DTI. She has helped hundreds of small businesses access financing, improve their operations, and expand to new markets. She holds a degree in Business Economics from the University of Cebu."
                }
            };
            
            // Close the details modal if open
            document.getElementById('staffDetailsModal').style.display = 'none';
            
            // Get staff data
            const staff = staffData[staffId];
            if (!staff) {
                alert("Staff information not found.");
                return;
            }
            
            // Set the staff ID in the form
            document.getElementById('staffId').value = staffId;
            document.getElementById('staffModalTitle').textContent = 'Edit Staff Member';
            
            // Populate form with staff data
            document.getElementById('staffName').value = staff.name;
            document.getElementById('staffPosition').value = staff.position;
            document.getElementById('staffType').value = staff.type;
            document.getElementById('staffContact').value = staff.contact;
            document.getElementById('staffEmail').value = staff.email;
            document.getElementById('staffBio').value = staff.bio;
            
            if (staff.services && staff.services.length > 0) {
                document.getElementById('staffServices').value = staff.services.join(', ');
            }
            
            // Show the staff form modal
            document.getElementById('staffModal').style.display = 'block';
        }
        
        function confirmDeleteStaff(staffId) {
            document.getElementById('confirmationTitle').textContent = 'Confirm Delete';
            document.getElementById('confirmationBody').innerHTML = `
                <p>Are you sure you want to delete this staff member?</p>
                <p class="text-danger"><strong>This action cannot be undone.</strong></p>
            `;
            document.getElementById('confirmActionBtn').innerHTML = '<i class="fas fa-trash-alt"></i> Delete';
            document.getElementById('confirmActionBtn').setAttribute('onclick', `deleteStaffMember(${staffId})`);
            document.getElementById('confirmationModal').style.display = 'block';
        }
        
        function closeConfirmationModal() {
            document.getElementById('confirmationModal').style.display = 'none';
        }
        
        function deleteStaffMember(staffId) {
            // This would typically send a delete request to the server
            // For now, just close the modals and show a success message
            alert(`Staff member with ID ${staffId} has been deleted successfully.`);
            document.getElementById('confirmationModal').style.display = 'none';
            document.getElementById('staffDetailsModal').style.display = 'none';
            
            // Remove the staff card from the list (for demonstration)
            const staffCard = document.querySelector(`.staff-card[data-id="${staffId}"]`);
            if (staffCard) {
                staffCard.remove();
            }
        }
        
        function handleStaffFormSubmit(e) {
            e.preventDefault();
            
            // Validate form
            const form = e.target;
            if (!form.checkValidity()) {
                alert("Please fill all required fields.");
                return;
            }
            
            // Get form data
            const formData = new FormData(form);
            const staffId = formData.get('staff_id');
            
            // This would typically send data to the server
            // For now, just show a success message and close the form
            if (staffId) {
                alert(`Staff member updated successfully!`);
            } else {
                alert(`New staff member added successfully!`);
            }
            
            closeStaffModal();
        }
    </script>
    <script src="main.js"></script>
    <script src="staff.js"></script>
</body>
</html>


