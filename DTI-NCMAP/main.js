// Global variables
let map;
let markers = [];
let currentMarker = null;
let offices = [];
let filteredOffices = [];

// Initialize the application
document.addEventListener('DOMContentLoaded', function() {
    initializeMap();
    loadOffices();
    setupEventListeners();
});

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    document.getElementById('searchInput').addEventListener('input', debounce(filterOffices, 300));
    document.getElementById('regionFilter').addEventListener('change', filterOffices);
    document.getElementById('officeTypeFilter').addEventListener('change', filterOffices);
    
    // Form submission
    document.getElementById('addOfficeForm').addEventListener('submit', handleAddOffice);
    
    // Modal close events
    document.getElementById('officeModal').addEventListener('click', function(e) {
        if (e.target === this) closeModal();
    });
    
    // Keyboard events
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            closeModal();
            if (document.getElementById('addOfficeSection').style.display !== 'none') {
                toggleAddOffice();
            }
        }
    });
}

// Initialize map
function initializeMap() {
    // Initialize map centered on Philippines
    map = L.map('map').setView([12.8797, 121.7740], 6);

    // Add tile layer
    L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
        attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
        maxZoom: 19
    }).addTo(map);

    // Handle map clicks for adding new offices
    map.on('click', function(e) {
        if (document.getElementById('addOfficeSection').style.display !== 'none') {
            handleMapClick(e);
        }
    });
}

// Handle map click for adding office
function handleMapClick(e) {
    const { lat, lng } = e.latlng;
    
    // Remove previous temporary marker
    if (currentMarker) {
        map.removeLayer(currentMarker);
    }
    
    // Add temporary marker
    currentMarker = L.marker([lat, lng], {
        icon: createCustomIcon('fas fa-plus', 'temporary')
    }).addTo(map);
    
    currentMarker.bindPopup(`
        <div class="custom-popup">
            <div class="popup-header">
                <div class="popup-title">New DTI Office Location</div>
                <div class="popup-type">Click "Save Office" to confirm</div>
            </div>
        </div>
    `).openPopup();
    
    // Update form coordinates
    document.getElementById('formLatitude').value = lat.toFixed(6);
    document.getElementById('formLongitude').value = lng.toFixed(6);
}

// Create custom map icon
function createCustomIcon(iconClass, type = 'field') {
    const colors = {
        'regional': '#dc2626',
        'provincial': '#d97706',
        'field': '#1e40af',
        'extension': '#059669',
        'ncr': '#059669',
        'temporary': '#6b7280'
    };
    
    return L.divIcon({
        className: 'custom-div-icon',
        html: `
            <div class="custom-marker ${type}" style="background-color: ${colors[type] || colors.field}">
                <i class="${iconClass}"></i>
            </div>
        `,
        iconSize: [30, 30],
        iconAnchor: [15, 30],
        popupAnchor: [0, -30]
    });
}

// This would be part of your main.js file where you create office items
function createOfficeItem(office) {
    const officeItem = document.createElement('div');
    officeItem.className = 'office-item';
    officeItem.setAttribute('data-id', office.id);
    
    // Determine if this is an NCR office
    const isNCR = office.is_ncr === '1';
    
    officeItem.innerHTML = `
        <div class="office-header">
            <h3 class="office-name">${office.office_name}</h3>
            <span class="office-type">${office.office_type}</span>
        </div>
        <span class="office-region ${isNCR ? 'ncr' : ''}">${office.region_name}${isNCR ? ' (NCR)' : ''}</span>
        <p class="office-address">
            <i class="fas fa-map-marker-alt"></i> ${office.address}
        </p>
        <div class="office-meta">
            ${office.contact_number ? `
            <div class="office-meta-item">
                <i class="fas fa-phone"></i>
                <span>${office.contact_number}</span>
            </div>` : ''}
            ${office.email ? `
            <div class="office-meta-item">
                <i class="fas fa-envelope"></i>
                <span>${office.email}</span>
            </div>` : ''}
        </div>
        <div class="office-actions">
            <button class="btn btn-primary" onclick="viewOfficeDetails(${office.id})">
                <i class="fas fa-info-circle"></i> Details
            </button>
            <button class="btn btn-secondary" onclick="locateOnMap(${office.id})">
                <i class="fas fa-map-pin"></i> Locate
            </button>
        </div>
    `;
    
    return officeItem;
            }
// Load offices from database
async function loadOffices() {
    try {
        showLoading(true);
        const response = await fetch('endpoint/get-offices.php');
        const data = await response.json();
        
        if (data.success) {
            offices = data.offices;
            filteredOffices = [...offices];
            displayOffices();
            addMarkersToMap();
        } else {
            showAlert('Error loading offices: ' + data.message, 'error');
        }
    } catch (error) {
        showAlert('Network error: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

// Display offices in sidebar
function displayOffices() {
    const officeList = document.getElementById('officeList');
    const officeCount = document.getElementById('officeCount');
    
    officeCount.textContent = filteredOffices.length;
    
    if (filteredOffices.length === 0) {
        officeList.innerHTML = `
            <div class="no-results">
                <i class="fas fa-search"></i>
                <p>No DTI offices found matching your criteria.</p>
            </div>
        `;
        return;
    }
    
    officeList.innerHTML = filteredOffices.map(office => `
        <div class="office-item" onclick="selectOffice(${office.office_id})" data-office-id="${office.office_id}">
            <div class="office-header">
                <div class="office-name">${office.office_name}</div>
                <div class="office-type">${office.office_type}</div>
            </div>
            <div class="office-region ${office.is_ncr ? 'ncr' : ''}">${office.region_name}${office.is_ncr ? ' (NCR)' : ''}</div>
            <div class="office-address">${office.address || 'Address not available'}</div>
            <div class="office-actions">
                <button class="btn btn-primary" onclick="event.stopPropagation(); viewOfficeOnMap(${office.office_id})">
                    <i class="fas fa-map-marker-alt"></i> View on Map
                </button>
                <button class="btn btn-secondary" onclick="event.stopPropagation(); showOfficeDetails(${office.office_id})">
                    <i class="fas fa-info-circle"></i> Details
                </button>
                                <button class="btn btn-danger" onclick="event.stopPropagation(); deleteOffice(${office.office_id})">
                    <i class="fas fa-trash"></i> Delete
                </button>
            </div>
        </div>
    `).join('');
}

// Add markers to map
function addMarkersToMap() {
    // Clear existing markers
    markers.forEach(marker => map.removeLayer(marker));
    markers = [];
    
    filteredOffices.forEach(office => {
        const iconClass = getOfficeIcon(office.office_type);
        const markerType = office.is_ncr ? 'ncr' : office.office_type.toLowerCase().replace(' ', '');
        
        const marker = L.marker([office.latitude, office.longitude], {
            icon: createCustomIcon(iconClass, markerType)
        }).addTo(map);
        
        marker.bindPopup(createPopupContent(office));
        marker.officeId = office.office_id;
        markers.push(marker);
    });
}

// Get icon for office type
function getOfficeIcon(officeType) {
    const icons = {
        'Regional Office': 'fas fa-building',
        'Provincial Office': 'fas fa-city',
        'Field Office': 'fas fa-home',
        'Extension Office': 'fas fa-store'
    };
    return icons[officeType] || 'fas fa-building';
}

// Create popup content
function createPopupContent(office) {
    return `
        <div class="custom-popup">
            <div class="popup-header">
                <div class="popup-title">${office.office_name}</div>
                <div class="popup-type">${office.office_type} - ${office.region_name}${office.is_ncr ? ' (NCR)' : ''}</div>
            </div>
            <div class="popup-content">
                <div class="popup-info">
                    ${office.address ? `
                        <div class="popup-info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <span>${office.address}</span>
                        </div>
                    ` : ''}
                    ${office.contact_number ? `
                        <div class="popup-info-item">
                            <i class="fas fa-phone"></i>
                            <span>${office.contact_number}</span>
                        </div>
                    ` : ''}
                    ${office.email ? `
                        <div class="popup-info-item">
                            <i class="fas fa-envelope"></i>
                            <span>${office.email}</span>
                        </div>
                    ` : ''}
                    ${office.office_hours ? `
                        <div class="popup-info-item">
                            <i class="fas fa-clock"></i>
                            <span>${office.office_hours}</span>
                        </div>
                    ` : ''}
                </div>
                <div class="popup-actions">
                    <button class="btn btn-primary" onclick="showOfficeDetails(${office.office_id})">
                        <i class="fas fa-info-circle"></i> Details
                    </button>
                    <button class="btn btn-secondary" onclick="getDirections(${office.latitude}, ${office.longitude})">
                        <i class="fas fa-directions"></i> Directions
                    </button>
                </div>
            </div>
        </div>
    `;
}

// Filter offices
function filterOffices() {
    const searchTerm = document.getElementById('searchInput').value.toLowerCase();
    const regionFilter = document.getElementById('regionFilter').value;
    const typeFilter = document.getElementById('officeTypeFilter').value;
    
    filteredOffices = offices.filter(office => {
        const matchesSearch = !searchTerm || 
            office.office_name.toLowerCase().includes(searchTerm) ||
            office.region_name.toLowerCase().includes(searchTerm) ||
            (office.address && office.address.toLowerCase().includes(searchTerm));
        
        const matchesRegion = !regionFilter || office.region_id == regionFilter;
        const matchesType = !typeFilter || office.office_type === typeFilter;
        
        return matchesSearch && matchesRegion && matchesType;
    });
    
    displayOffices();
    addMarkersToMap();
}

// Clear filters
function clearFilters() {
    document.getElementById('searchInput').value = '';
    document.getElementById('regionFilter').value = '';
    document.getElementById('officeTypeFilter').value = '';
    filterOffices();
}

// Select office
function selectOffice(officeId) {
    // Remove previous selection
    document.querySelectorAll('.office-item').forEach(item => {
        item.classList.remove('active');
    });
    
    // Add selection to clicked item
    document.querySelector(`[data-office-id="${officeId}"]`).classList.add('active');
    
    // Find and open marker popup
    const marker = markers.find(m => m.officeId === officeId);
    if (marker) {
        map.setView(marker.getLatLng(), 15);
        marker.openPopup();
    }
}

// View office on map
function viewOfficeOnMap(officeId) {
    const office = offices.find(o => o.office_id === officeId);
    if (office) {
        map.setView([office.latitude, office.longitude], 15);
        const marker = markers.find(m => m.officeId === officeId);
        if (marker) {
            marker.openPopup();
        }
    }
}

// Show office details in modal
function showOfficeDetails(officeId) {
    const office = offices.find(o => o.office_id === officeId);
    if (!office) return;
    
    const modalTitle = document.getElementById('modalTitle');
    const modalBody = document.getElementById('modalBody');
    
    modalTitle.textContent = office.office_name;
    modalBody.innerHTML = `
        <div class="office-detail-grid">
            <div class="office-detail-header">
                ${office.image_path ? `
                    <img src="${office.image_path}" alt="${office.office_name}" class="office-detail-image">
                ` : ''}
                <h3 class="office-detail-title">${office.office_name}</h3>
                <div class="office-detail-badges">
                    <span class="badge badge-primary">${office.office_type}</span>
                    <span class="badge ${office.is_ncr ? 'badge-warning' : 'badge-success'}">${office.region_name}${office.is_ncr ? ' (NCR)' : ''}</span>
                </div>
            </div>
            
            <div class="office-detail-info">
                <div class="info-section">
                    <h4><i class="fas fa-info-circle"></i> Contact Information</h4>
                    ${office.address ? `
                        <div class="info-item">
                            <i class="fas fa-map-marker-alt"></i>
                            <div><strong>Address:</strong> ${office.address}</div>
                        </div>
                    ` : ''}
                    ${office.contact_number ? `
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <div><strong>Phone:</strong> ${office.contact_number}</div>
                        </div>
                    ` : ''}
                    ${office.email ? `
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <div><strong>Email:</strong> ${office.email}</div>
                        </div>
                    ` : ''}
                    ${office.office_hours ? `
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <div><strong>Office Hours:</strong> ${office.office_hours}</div>
                        </div>
                    ` : ''}
                </div>
                
                ${office.office_head ? `
                    <div class="info-section">
                        <h4><i class="fas fa-user-tie"></i> Office Head</h4>
                        <div class="info-item">
                            <i class="fas fa-user"></i>
                            <div>${office.office_head}</div>
                        </div>
                    </div>
                ` : ''}
                
                ${office.services_offered ? `
                    <div class="info-section">
                        <h4><i class="fas fa-cogs"></i> Services Offered</h4>
                        <div class="info-item">
                            <i class="fas fa-list"></i>
                            <div>${office.services_offered}</div>
                        </div>
                    </div>
                ` : ''}
                
                ${office.description ? `
                    <div class="info-section">
                        <h4><i class="fas fa-file-alt"></i> Description</h4>
                        <div class="info-item">
                            <i class="fas fa-info"></i>
                            <div>${office.description}</div>
                        </div>
                    </div>
                ` : ''}
                
                <div class="info-section">
                    <h4><i class="fas fa-map"></i> Location</h4>
                    <div class="info-item">
                        <i class="fas fa-crosshairs"></i>
                        <div><strong>Coordinates:</strong> ${office.latitude}, ${office.longitude}</div>
                    </div>
                    <div style="margin-top: 1rem;">
                        <button class="btn btn-primary" onclick="viewOfficeOnMap(${office.office_id}); closeModal();">
                            <i class="fas fa-map-marker-alt"></i> View on Map
                        </button>
                        <button class="btn btn-secondary" onclick="getDirections(${office.latitude}, ${office.longitude})">
                            <i class="fas fa-directions"></i> Get Directions
                        </button>
                    </div>
                </div>
            </div>
        </div>
    `;
    
    openModal();
}

// Handle add office form submission
async function handleAddOffice(e) {
    e.preventDefault();
    
    const formData = new FormData(e.target);
    
    // Validate required fields
    if (!formData.get('latitude') || !formData.get('longitude')) {
        showAlert('Please click on the map to select a location.', 'error');
        return;
    }
    
    try {
        showLoading(true);
        
        const response = await fetch('endpoint/add-office.php', {
            method: 'POST',
            body: formData
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('DTI office added successfully!', 'success');
            toggleAddOffice();
            loadOffices(); // Reload offices
            
            // Remove temporary marker
            if (currentMarker) {
                map.removeLayer(currentMarker);
                currentMarker = null;
            }
            
            // Reset form
            e.target.reset();
            document.getElementById('formLatitude').value = '';
            document.getElementById('formLongitude').value = '';
        } else {
            showAlert('Error: ' + data.message, 'error');
        }
    } catch (error) {
        showAlert('Network error: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

// Delete office
async function deleteOffice(officeId) {
    if (!confirm('Are you sure you want to delete this DTI office?')) {
        return;
    }
    
    try {
        showLoading(true);
        
        const response = await fetch('endpoint/delete-office.php', {
            method: 'POST',
            headers: {
                'Content-Type': 'application/json',
            },
            body: JSON.stringify({ office_id: officeId })
        });
        
        const data = await response.json();
        
        if (data.success) {
            showAlert('DTI office deleted successfully!', 'success');
            loadOffices(); // Reload offices
        } else {
            showAlert('Error: ' + data.message, 'error');
        }
    } catch (error) {
        showAlert('Network error: ' + error.message, 'error');
    } finally {
        showLoading(false);
    }
}

// Get directions
function getDirections(lat, lng) {
    const url = `https://www.google.com/maps/dir/?api=1&destination=${lat},${lng}`;
    window.open(url, '_blank');
}

// Toggle add office section
function toggleAddOffice() {
    const section = document.getElementById('addOfficeSection');
    const isVisible = section.style.display !== 'none';
    
    section.style.display = isVisible ? 'none' : 'block';
    
    if (!isVisible) {
        // Scroll to form
        section.scrollIntoView({ behavior: 'smooth' });
    } else {
        // Remove temporary marker when closing
        if (currentMarker) {
            map.removeLayer(currentMarker);
            currentMarker = null;
        }
    }
}

// Toggle sidebar
function toggleSidebar() {
    const sidebar = document.getElementById('sidebar');
    sidebar.classList.toggle('collapsed');
}

// Reset map view
function resetMapView() {
    map.setView([12.8797, 121.7740], 6);
}

// Toggle fullscreen
function toggleFullscreen() {
    if (!document.fullscreenElement) {
        document.documentElement.requestFullscreen();
    } else {
        document.exitFullscreen();
    }
}

// Modal functions
function openModal() {
    document.getElementById('officeModal').classList.add('active');
    document.body.style.overflow = 'hidden';
}

function closeModal() {
    document.getElementById('officeModal').classList.remove('active');
    document.body.style.overflow = '';
}

// Loading functions
function showLoading(show) {
    const overlay = document.getElementById('loadingOverlay');
    if (show) {
        overlay.classList.add('active');
    } else {
        overlay.classList.remove('active');
    }
}

// Alert function
function showAlert(message, type = 'info') {
    // Remove existing alerts
    const existingAlerts = document.querySelectorAll('.alert');
    existingAlerts.forEach(alert => alert.remove());
    
    // Create new alert
    const alert = document.createElement('div');
    alert.className = `alert alert-${type}`;
    alert.innerHTML = `
        <i class="fas fa-${type === 'success' ? 'check-circle' : type === 'error' ? 'exclamation-circle' : 'info-circle'}"></i>
        <span>${message}</span>
    `;
    
    // Insert at top of sidebar
    const sidebar = document.getElementById('sidebar');
    sidebar.insertBefore(alert, sidebar.firstChild);
    
    // Auto remove after 5 seconds
    setTimeout(() => {
        if (alert.parentNode) {
            alert.remove();
        }
    }, 5000);
}

// Utility function for debouncing
function debounce(func, wait) {
    let timeout;
    return function executedFunction(...args) {
        const later = () => {
            clearTimeout(timeout);
            func(...args);
                    };
        clearTimeout(timeout);
        timeout = setTimeout(later, wait);
    };
}

// Export functions for global access
window.selectOffice = selectOffice;
window.viewOfficeOnMap = viewOfficeOnMap;
window.showOfficeDetails = showOfficeDetails;
window.deleteOffice = deleteOffice;
window.getDirections = getDirections;
window.toggleAddOffice = toggleAddOffice;
window.toggleSidebar = toggleSidebar;
window.resetMapView = resetMapView;
window.toggleFullscreen = toggleFullscreen;
window.closeModal = closeModal;
window.clearFilters = clearFilters;

