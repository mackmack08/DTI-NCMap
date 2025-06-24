<?php
session_start();
include("conn/conn.php");

// Check if user is logged in for admin features
$isAdmin = isset($_SESSION['user_id']);
?>
<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="UTF-8">
    <meta name="viewport" content="width=device-width, initial-scale=1.0">
    <title>DTI Cebu Province - NC Locator Map</title>
    
    <!-- External Libraries -->
    <link rel="stylesheet" href="https://unpkg.com/leaflet/dist/leaflet.css" />
    <link rel="stylesheet" href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.0.0/css/all.min.css">
    <link href="https://fonts.googleapis.com/css2?family=Roboto:wght@300;400;500;700&display=swap" rel="stylesheet">
    
    <!-- Custom Styles -->
    <link rel="stylesheet" href="assets/css/dti-map-styles.css">
    <link rel="shortcut icon" href="img/favicon.ico" type="image/x-icon">

    <style>
        /* DTI NC Map - Complete Stylesheet */
:root {
    --primary-color: #0D47A1;
    --primary-dark: #002171;
    --primary-light: #BBDEFB;
    --secondary-color: #2E7D32;
    --accent-color: #EF6C00;
    --purple-color: #6A1B9A;
    --text-dark: #333333;
    --text-medium: #555555;
    --text-light: #777777;
    --background-light: #f8f9fa;
    --white: #ffffff;
    --shadow-light: 0 2px 10px rgba(0,0,0,0.1);
    --shadow-medium: 0 4px 15px rgba(0,0,0,0.15);
    --transition-normal: all 0.3s ease;
    --border-radius-sm: 4px;
    --border-radius-md: 8px;
    --border-radius-lg: 12px;
}

* {
    box-sizing: border-box;
    margin: 0;
    padding: 0;
}

body {
    font-family: 'Roboto', 'Segoe UI', Tahoma, Geneva, Verdana, sans-serif;
    background-color: var(--background-light);
    color: var(--text-dark);
    line-height: 1.6;
    height: 100vh;
    overflow: hidden;
}

/* Header */
.header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    padding: 1rem 2rem;
    box-shadow: var(--shadow-medium);
    z-index: 1000;
    position: relative;
}

.header-content {
    display: flex;
    justify-content: space-between;
    align-items: center;
    max-width: 1400px;
    margin: 0 auto;
}

.header-left {
    display: flex;
    align-items: center;
    gap: 1rem;
}

.header-logo {
    height: 50px;
    width: auto;
}

.header-title h1 {
    font-size: 1.5rem;
    font-weight: 700;
    margin-bottom: 0.2rem;
}

.header-title p {
    font-size: 0.9rem;
    opacity: 0.9;
}

.header-right {
    display: flex;
    gap: 0.5rem;
}

/* Main Container */
.main-container {
    display: flex;
    height: calc(100vh - 82px);
}

/* Sidebar */
.sidebar {
    width: 400px;
    background: white;
    border-right: 1px solid #e0e0e0;
    display: flex;
    flex-direction: column;
    overflow: hidden;
}

.search-section {
    padding: 1.5rem;
    border-bottom: 1px solid #e0e0e0;
    background: #f8f9fa;
}

.search-section h3 {
    color: var(--primary-color);
    margin-bottom: 1rem;
    font-size: 1.1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.search-controls {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.search-input, .filter-select {
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius-sm);
    font-size: 0.9rem;
    transition: var(--transition-normal);
}

.search-input:focus, .filter-select:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(13, 71, 161, 0.1);
}

/* Office List */
.office-list {
    flex: 1;
    overflow-y: auto;
    padding: 1rem;
}

.office-item {
    background: white;
    border: 1px solid #e0e0e0;
    border-radius: var(--border-radius-md);
    padding: 1rem;
    margin-bottom: 0.75rem;
    cursor: pointer;
    transition: var(--transition-normal);
}

.office-item:hover {
    box-shadow: var(--shadow-light);
    transform: translateY(-2px);
}

.office-header {
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
    margin-bottom: 0.5rem;
}

.office-name {
    font-weight: 600;
    color: var(--text-dark);
    font-size: 1rem;
}

.office-type {
    background: var(--primary-light);
    color: var(--primary-dark);
    padding: 0.25rem 0.5rem;
    border-radius: var(--border-radius-sm);
    font-size: 0.8rem;
    font-weight: 500;
}

.office-region {
    font-size: 0.85rem;
    margin-bottom: 0.5rem;
}

.cebu-region {
    background-color: #e3f2fd;
    color: #0d47a1;
    font-weight: bold;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
}

.other-region {
    background-color: #f5f5f5;
    color: #757575;
    padding: 2px 8px;
    border-radius: 4px;
    display: inline-block;
}

.office-address {
    color: var(--text-medium);
    font-size: 0.9rem;
    margin-bottom: 0.75rem;
}

.office-actions {
    display: flex;
    gap: 0.5rem;
    flex-wrap: wrap;
}

.no-results {
    text-align: center;
    color: var(--text-light);
    padding: 2rem;
    font-style: italic;
}

/* Legend */
.legend {
    padding: 1rem;
    border-top: 1px solid #e0e0e0;
    background: #f8f9fa;
}

.legend h4 {
    color: var(--primary-color);
    margin-bottom: 0.75rem;
    font-size: 1rem;
    display: flex;
    align-items: center;
    gap: 0.5rem;
}

.legend-items {
    display: flex;
    flex-direction: column;
    gap: 0.5rem;
}

.legend-item {
    display: flex;
    align-items: center;
    gap: 0.75rem;
    font-size: 0.9rem;
}

.legend-marker {
    width: 16px;
    height: 16px;
    border-radius: 50%;
    border: 2px solid white;
    box-shadow: 0 1px 3px rgba(0,0,0,0.3);
}

.legend-marker.regional { background: var(--primary-dark); }
.legend-marker.provincial { background: var(--secondary-color); }
.legend-marker.field { background: var(--accent-color); }
.legend-marker.extension { background: var(--purple-color); }
.legend-marker.negosyo { background: var(--primary-color); }

/* Map Container */
.map-container {
    flex: 1;
    position: relative;
}

#map {
    width: 100%;
    height: 100%;
}

/* Buttons */
.btn {
    padding: 0.5rem 1rem;
    border: none;
    border-radius: var(--border-radius-sm);
    font-size: 0.9rem;
    font-weight: 500;
    cursor: pointer;
    text-decoration: none;
    display: inline-flex;
    align-items: center;
    gap: 0.5rem;
    transition: var(--transition-normal);
    text-align: center;
    justify-content: center;
}

.btn-primary {
    background: var(--primary-color);
    color: white;
}

.btn-primary:hover {
    background: var(--primary-dark);
    transform: translateY(-1px);
}

.btn-secondary {
    background: white;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.btn-secondary:hover {
    background: var(--primary-light);
}

.btn-danger {
    background: #dc3545;
    color: white;
}

.btn-danger:hover {
    background: #c82333;
}

.btn-sm {
    padding: 0.375rem 0.75rem;
    font-size: 0.8rem;
}

/* Modals */
.modal {
    display: none;
    position: fixed;
    z-index: 10000;
    left: 0;
    top: 0;
    width: 100%;
    height: 100%;
    background-color: rgba(0,0,0,0.5);
    backdrop-filter: blur(2px);
}

.modal-content {
    background-color: white;
    margin: 2% auto;
    border-radius: var(--border-radius-lg);
    width: 90%;
    max-width: 800px;
    max-height: 90vh;
    overflow-y: auto;
    box-shadow: 0 10px 30px rgba(0,0,0,0.3);
}

.modal-header {
    padding: 1.5rem;
    border-bottom: 1px solid #e0e0e0;
    display: flex;
    justify-content: space-between;
    align-items: center;
    background: var(--primary-color);
    color: white;
    border-radius: var(--border-radius-lg) var(--border-radius-lg) 0 0;
}

.modal-header h2 {
    margin: 0;
    font-size: 1.3rem;
}

.close {
    color: white;
    font-size: 28px;
    font-weight: bold;
    cursor: pointer;
    opacity: 0.8;
    transition: opacity 0.2s;
}

.close:hover {
    opacity: 1;
}

.modal-body {
    padding: 1.5rem;
}

/* Forms */
.form-group {
    margin-bottom: 1rem;
}

.form-row {
    display: grid;
    grid-template-columns: 1fr 1fr;
    gap: 1rem;
}

.form-group label {
    display: block;
    margin-bottom: 0.5rem;
    font-weight: 500;
    color: var(--text-dark);
}

.form-input, .form-select, .form-textarea {
    width: 100%;
    padding: 0.75rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius-sm);
    font-size: 0.9rem;
    transition: var(--transition-normal);
}

.form-input:focus, .form-select:focus, .form-textarea:focus {
    outline: none;
    border-color: var(--primary-color);
    box-shadow: 0 0 0 2px rgba(13, 71, 161, 0.1);
}

.form-textarea {
    resize: vertical;
    min-height: 80px;
}

.form-file {
    width: 100%;
    padding: 0.5rem;
    border: 1px solid #ddd;
    border-radius: var(--border-radius-sm);
    font-size: 0.9rem;
}

.form-actions {
    display: flex;
    gap: 1rem;
    justify-content: flex-end;
    margin-top: 1.5rem;
    padding-top: 1rem;
    border-top: 1px solid #e0e0e0;
}

.required {
    color: #dc3545;
}

/* Office Details */
.office-details {
    display: flex;
    flex-direction: column;
    gap: 1.5rem;
}

.office-info {
    display: flex;
    flex-direction: column;
    gap: 0.75rem;
}

.info-item {
    display: flex;
    align-items: flex-start;
    gap: 0.75rem;
}

.info-item i {
    color: var(--primary-color);
    margin-top: 0.2rem;
    width: 16px;
}

.office-description h3,
.office-services h3,
.office-staff h3 {
    color: var(--primary-color);
    margin-bottom: 0.75rem;
    font-size: 1.1rem;
}

.services-list {
    display: flex;
    flex-wrap: wrap;
    gap: 0.5rem;
}

.service-tag {
    background: var(--primary-light);
    color: var(--primary-dark);
    padding: 0.25rem 0.75rem;
    border-radius: 15px;
    font-size: 0.8rem;
    font-weight: 500;
}

.no-services {
    color: var(--text-light);
    font-style: italic;
}

.staff-header {
    display: flex;
    justify-content: space-between;
    align-items: center;
    margin-bottom: 1rem;
}

.staff-list {
    display: flex;
    flex-direction: column;
    gap: 1rem;
}

.staff-item {
    background: #f8f9fa;
    padding: 1rem;
    border-radius: var(--border-radius-md);
    display: flex;
    justify-content: space-between;
    align-items: flex-start;
}

.staff-info h4 {
    color: var(--text-dark);
    margin-bottom: 0.25rem;
}

.staff-position {
    color: var(--primary-color);
    font-weight: 500;
    margin-bottom: 0.5rem;
}

.staff-info p {
    font-size: 0.9rem;
    color: var(--text-medium);
    margin-bottom: 0.25rem;
}

.staff-actions {
    display: flex;
    gap: 0.5rem;
}

.no-staff {
    color: var(--text-light);
    font-style: italic;
    text-align: center;
    padding: 1rem;
}

/* Map Popup Styles */
.marker-popup {
    padding: 0;
    font-family: 'Roboto', sans-serif;
}

.popup-header {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    padding: 1rem;
    border-radius: 6px 6px 0 0;
    color: white;
}

.popup-title {
    margin: 0 0 0.5rem 0;
    font-size: 1rem;
    font-weight: 600;
    line-height: 1.3;
}

.popup-badge {
    display: inline-block;
    padding: 0.25rem 0.5rem;
    font-size: 0.7rem;
    font-weight: 500;
    border-radius: 10px;
    text-transform: uppercase;
    letter-spacing: 0.5px;
    background: rgba(255, 255, 255, 0.2);
    border: 1px solid rgba(255, 255, 255, 0.3);
}

.popup-body {
    padding: 1rem;
    background: white;
}

.popup-info-item {
    display: flex;
    align-items: flex-start;
    margin-bottom: 0.5rem;
    font-size: 0.85rem;
}

.popup-info-item i {
    color: var(--primary-color);
    margin-right: 0.5rem;
    min-width: 14px;
    margin-top: 0.1rem;
}

.popup-footer {
    padding: 0.75rem 1rem;
    background: #f8f9fa;
    display: flex;
    gap: 0.5rem;
    border-radius: 0 0 6px 6px;
}

.popup-btn {
    flex: 1;
    padding: 0.5rem;
    font-size: 0.8rem;
    border-radius: var(--border-radius-sm);
    text-decoration: none;
    font-weight: 500;
    transition: var(--transition-normal);
    border: none;
    cursor: pointer;
    text-align: center;
}

.popup-btn.btn-primary {
    background: var(--primary-color);
    color: white;
}

.popup-btn.btn-primary:hover {
    background: var(--primary-dark);
}

.popup-btn.btn-secondary {
    background: white;
    color: var(--primary-color);
    border: 1px solid var(--primary-color);
}

.popup-btn.btn-secondary:hover {
    background: var(--primary-light);
}

/* Custom Leaflet Overrides */
.leaflet-popup-content-wrapper {
    padding: 0;
    border-radius: 8px;
    box-shadow: 0 8px 25px rgba(0,0,0,0.15);
    border: none;
}

.leaflet-popup-content {
    margin: 0;
    width: 300px !important;
}

.leaflet-popup-close-button {
    padding: 8px;
    font-size: 18px;
    color: rgba(255, 255, 255, 0.8);
    font-weight: bold;
    right: 8px;
    top: 8px;
    width: 28px;
    height: 28px;
    text-align: center;
    line-height: 12px;
}

.leaflet-popup-close-button:hover {
    color: white;
    background: rgba(255, 255, 255, 0.1);
    border-radius: 50%;
}

/* Custom Markers */
.nc-marker-icon {
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
    color: white;
    border-radius: 50%;
    width: 36px;
    height: 36px;
    display: flex;
    align-items: center;
    justify-content: center;
    font-weight: bold;
    font-size: 12px;
    box-shadow: 0 3px 10px rgba(0,0,0,0.3);
    border: 2px solid white;
    transition: var(--transition-normal);
}

.nc-marker-icon:hover {
    transform: scale(1.1);
}

.nc-marker-icon.regional-office {
    background: linear-gradient(135deg, var(--primary-dark) 0%, #001a5c 100%);
    width: 42px;
    height: 42px;
    font-size: 14px;
}

.nc-marker-icon.provincial-office {
    background: linear-gradient(135deg, var(--secondary-color) 0%, #1b4e1f 100%);
}

.nc-marker-icon.field-office {
    background: linear-gradient(135deg, var(--accent-color) 0%, #cc5a00 100%);
}

.nc-marker-icon.extension-office {
    background: linear-gradient(135deg, var(--purple-color) 0%, #4a148c 100%);
}

.nc-marker-icon.negosyo-center {
    width: 32px;
    height: 32px;
    font-size: 11px;
    background: linear-gradient(135deg, var(--primary-color) 0%, var(--primary-dark) 100%);
}

.custom-marker {
    background: transparent !important;
    border: none !important;
}

/* Notifications */
.notification {
    position: fixed;
    top: 100px;
    right: 20px;
    padding: 1rem 1.5rem;
    border-radius: var(--border-radius-md);
    color: white;
    font-weight: 500;
    z-index: 10001;
    display: flex;
    align-items: center;
    justify-content: space-between;
    gap: 1rem;
    min-width: 300px;
    box-shadow: var(--shadow-medium);
    animation: slideIn 0.3s ease-out;
}

.notification.success {
    background: #28a745;
}

.notification.error {
    background: #dc3545;
}

.notification.info {
    background: var(--primary-color);
}

.notification button {
    background: none;
    border: none;
    color: white;
    font-size: 1.2rem;
    cursor: pointer;
    padding: 0;
    width: 20px;
    height: 20px;
    display: flex;
    align-items: center;
    justify-content: center;
    border-radius: 50%;
    transition: background 0.2s;
}

.notification button:hover {
    background: rgba(255, 255, 255, 0.2);
}

@keyframes slideIn {
    from {
        transform: translateX(100%);
        opacity: 0;
    }
    to {
        transform: translateX(0);
        opacity: 1;
    }
}

/* Loading Spinner */
.loading-spinner {
    width: 40px;
    height: 40px;
    border: 4px solid #f3f3f3;
    border-top: 4px solid var(--primary-color);
    border-radius: 50%;
    animation: spin 1s linear infinite;
    margin: 2rem auto;
}

@keyframes spin {
    0% { transform: rotate(0deg); }
    100% { transform: rotate(360deg); }
}

/* Responsive Design */
@media (max-width: 1200px) {
    .sidebar {
        width: 350px;
    }
    
    .header-content {
        padding: 0 1rem;
    }
}

@media (max-width: 992px) {
    .main-container {
        flex-direction: column;
    }
    
    .sidebar {
        width: 100%;
        height: 40vh;
        border-right: none;
        border-bottom: 1px solid #e0e0e0;
    }
    
    .map-container {
        height: 60vh;
    }
    
    .header {
        padding: 0.75rem 1rem;
    }
    
    .header-title h1 {
        font-size: 1.3rem;
    }
    
    .header-title p {
        font-size: 0.8rem;
    }
    
    .search-section {
        padding: 1rem;
    }
    
    .office-list {
        padding: 0.75rem;
    }
    
    .legend {
        padding: 0.75rem;
    }
    
    .modal-content {
        width: 95%;
        margin: 5% auto;
    }
    
    .form-row {
        grid-template-columns: 1fr;
    }
}

@media (max-width: 768px) {
    .header-content {
        flex-direction: column;
        gap: 1rem;
        text-align: center;
    }
    
    .header-left {
        flex-direction: column;
        gap: 0.5rem;
    }
    
    .header-right {
        justify-content: center;
    }
    
    .sidebar {
        height: 50vh;
    }
    
    .map-container {
        height: 50vh;
    }
    
    .search-controls {
        gap: 0.5rem;
    }
    
    .office-actions {
        flex-direction: column;
    }
    
    .btn {
        font-size: 0.8rem;
        padding: 0.4rem 0.8rem;
    }
    
    .modal-header {
        padding: 1rem;
    }
    
    .modal-body {
        padding: 1rem;
    }
    
    .popup-footer {
        flex-direction: column;
    }
    
    .leaflet-popup-content {
        width: 250px !important;
    }
    
    .notification {
        right: 10px;
        left: 10px;
        min-width: auto;
    }
}

@media (max-width: 576px) {
    .header {
        padding: 0.5rem;
    }
    
    .header-logo {
        height: 40px;
    }
    
    .header-title h1 {
        font-size: 1.1rem;
    }
    
    .search-section h3 {
        font-size: 1rem;
    }
    
    .office-item {
        padding: 0.75rem;
    }
    
    .office-name {
        font-size: 0.9rem;
    }
    
    .btn-sm {
        padding: 0.25rem 0.5rem;
        font-size: 0.75rem;
    }
    
    .nc-marker-icon {
        width: 28px;
        height: 28px;
        font-size: 10px;
    }
    
    .nc-marker-icon.regional-office {
        width: 32px;
        height: 32px;
        font-size: 11px;
    }
    
    .leaflet-popup-content {
        width: 200px !important;
    }
    
    .popup-title {
        font-size: 0.9rem;
    }
    
    .popup-info-item {
        font-size: 0.8rem;
    }
}

/* Print Styles */
@media print {
    .header-right,
    .office-actions,
    .staff-actions,
    .form-actions,
    .popup-footer {
        display: none !important;
    }
    
    .main-container {
        height: auto;
    }
    
    .sidebar {
        width: 100%;
        height: auto;
    }
    
    .map-container {
        height: 400px;
    }
}

/* High Contrast Mode */
@media (prefers-contrast: high) {
    :root {
        --primary-color: #000080;
        --primary-dark: #000040;
        --text-dark: #000000;
        --text-medium: #333333;
        --background-light: #ffffff;
    }
    
    .office-item {
        border: 2px solid #000000;
    }
    
    .btn {
        border: 2px solid currentColor;
    }
}

/* Reduced Motion */
@media (prefers-reduced-motion: reduce) {
    * {
        animation-duration: 0.01ms !important;
        animation-iteration-count: 1 !important;
        transition-duration: 0.01ms !important;
    }
    
    .nc-marker-icon:hover {
        transform: none;
    }
    
    .office-item:hover {
        transform: none;
    }
}

    </style>
</head>
<body>
    <!-- Header -->
    <header class="header">
        <div class="header-content">
            <div class="header-left">
                <img src="img/dti-logo.png" alt="DTI Logo" class="header-logo">
                <div class="header-title">
                    <h1>DTI Cebu Province</h1>
                    <p>Negosyo Center Locator Map</p>
                </div>
            </div>
            <div class="header-right">
                <?php if ($isAdmin): ?>
                    <button class="btn btn-primary" onclick="toggleAddOffice()">
                        <i class="fas fa-plus"></i> Add Office
                    </button>
                    <a href="logout.php" class="btn btn-secondary">
                        <i class="fas fa-sign-out-alt"></i> Logout
                    </a>
                <?php else: ?>
                    <a href="login.php" class="btn btn-primary">
                        <i class="fas fa-user-lock"></i> Admin Login
                    </a>
                <?php endif; ?>
            </div>
        </div>
    </header>

    <!-- Main Container -->
    <div class="main-container">
        <!-- Sidebar -->
        <div class="sidebar" id="sidebar">
            <!-- Search Section -->
            <div class="search-section">
                <h3><i class="fas fa-search"></i> Find DTI Offices</h3>
                <div class="search-controls">
                    <input type="text" id="searchInput" placeholder="Search offices..." class="search-input">
                    <select id="regionFilter" class="filter-select">
                        <option value="">All Regions</option>
                    </select>
                    <select id="typeFilter" class="filter-select">
                        <option value="">All Office Types</option>
                        <option value="Regional Office">Regional Office</option>
                        <option value="Provincial Office">Provincial Office</option>
                        <option value="Field Office">Field Office</option>
                        <option value="Extension Office">Extension Office</option>
                        <option value="Negosyo Center">Negosyo Center</option>
                    </select>
                    <button class="btn btn-primary" onclick="searchOffices()">
                        <i class="fas fa-search"></i> Search
                    </button>
                </div>
            </div>

            <!-- Office List -->
            <div class="office-list" id="officeList">
                <div class="loading-spinner"></div>
                <p>Loading offices...</p>
            </div>

            <!-- Legend -->
            <div class="legend">
                <h4><i class="fas fa-info-circle"></i> Office Types</h4>
                <div class="legend-items">
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
                    <div class="legend-item">
                        <span class="legend-marker negosyo"></span>
                        <span>Negosyo Center</span>
                    </div>
                </div>
            </div>
        </div>

        <!-- Map Container -->
        <div class="map-container">
            <div id="map"></div>
        </div>
    </div>

    <!-- Office Details Modal -->
    <div class="modal" id="officeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="modalTitle">Office Details</h2>
                <span class="close" onclick="closeModal('officeModal')">&times;</span>
            </div>
            <div class="modal-body" id="modalBody">
                <!-- Content loaded dynamically -->
            </div>
        </div>
    </div>

    <!-- Add/Edit Office Modal -->
    <?php if ($isAdmin): ?>
    <div class="modal" id="addOfficeModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="officeModalTitle">Add New Office</h2>
                <span class="close" onclick="closeModal('addOfficeModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="officeForm" enctype="multipart/form-data">
                    <input type="hidden" id="officeId" name="office_id">
                    
                    <div class="form-group">
                        <label for="officeName">Office Name *</label>
                        <input type="text" id="officeName" name="office_name" required class="form-input">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="officeType">Office Type *</label>
                            <select id="officeType" name="office_type" required class="form-select">
                                <option value="">Select type</option>
                                <option value="Regional Office">Regional Office</option>
                                <option value="Provincial Office">Provincial Office</option>
                                <option value="Field Office">Field Office</option>
                                <option value="Extension Office">Extension Office</option>
                                <option value="Negosyo Center">Negosyo Center</option>
                            </select>
                        </div>
                        <div class="form-group">
                            <label for="regionId">Region *</label>
                            <select id="regionId" name="region_id" required class="form-select">
                                <option value="">Select region</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="address">Address *</label>
                        <textarea id="address" name="address" required class="form-textarea"></textarea>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="latitude">Latitude *</label>
                            <input type="number" id="latitude" name="latitude" step="any" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="longitude">Longitude *</label>
                            <input type="number" id="longitude" name="longitude" step="any" required class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="contactNumber">Contact Number</label>
                            <input type="text" id="contactNumber" name="contact_number" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="email">Email</label>
                            <input type="email" id="email" name="email" class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="officeHead">Office Head</label>
                        <input type="text" id="officeHead" name="office_head" class="form-input">
                    </div>
                    
                    <div class="form-group">
                        <label for="description">Description</label>
                        <textarea id="description" name="description" class="form-textarea"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="servicesOffered">Services Offered</label>
                        <textarea id="servicesOffered" name="services_offered" class="form-textarea" placeholder="Separate services with commas"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="officeHours">Office Hours</label>
                        <input type="text" id="officeHours" name="office_hours" class="form-input" value="8:00 AM - 5:00 PM">
                    </div>
                    
                    <div class="form-group">
                        <label for="officeImage">Office Image</label>
                        <input type="file" id="officeImage" name="office_image" accept="image/*" class="form-file">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Office
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('addOfficeModal')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Staff Modal -->
    <div class="modal" id="staffModal">
        <div class="modal-content">
            <div class="modal-header">
                <h2 id="staffModalTitle">Add Staff Member</h2>
                <span class="close" onclick="closeModal('staffModal')">&times;</span>
            </div>
            <div class="modal-body">
                <form id="staffForm" enctype="multipart/form-data">
                    <input type="hidden" id="staffId" name="staff_id">
                    <input type="hidden" id="staffOfficeId" name="office_id">
                    
                    <div class="form-group">
                        <label for="staffName">Staff Name *</label>
                        <input type="text" id="staffName" name="staff_name" required class="form-input">
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="position">Position *</label>
                            <input type="text" id="position" name="position" required class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="staffType">Staff Type</label>
                            <select id="staffType" name="staff_type" class="form-select">
                                <option value="Regular">Regular</option>
                                <option value="Contractual">Contractual</option>
                                <option value="Job Order">Job Order</option>
                            </select>
                        </div>
                    </div>
                    
                    <div class="form-row">
                        <div class="form-group">
                            <label for="staffContact">Contact Number</label>
                            <input type="text" id="staffContact" name="contact_number" class="form-input">
                        </div>
                        <div class="form-group">
                            <label for="staffEmail">Email</label>
                            <input type="email" id="staffEmail" name="email" class="form-input">
                        </div>
                    </div>
                    
                    <div class="form-group">
                        <label for="staffServices">Services/Specialization</label>
                        <textarea id="staffServices" name="services_offered" class="form-textarea" placeholder="Separate services with commas"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="bio">Bio/Description</label>
                        <textarea id="bio" name="bio" class="form-textarea"></textarea>
                    </div>
                    
                    <div class="form-group">
                        <label for="staffPhoto">Staff Photo</label>
                        <input type="file" id="staffPhoto" name="photo" accept="image/*" class="form-file">
                    </div>
                    
                    <div class="form-actions">
                        <button type="submit" class="btn btn-primary">
                            <i class="fas fa-save"></i> Save Staff
                        </button>
                        <button type="button" class="btn btn-secondary" onclick="closeModal('staffModal')">
                            <i class="fas fa-times"></i> Cancel
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
    <?php endif; ?>

    <!-- Scripts -->
    <script src="https://unpkg.com/leaflet/dist/leaflet.js"></script>
    <script src="main.js"></script>
</body>
</html>
