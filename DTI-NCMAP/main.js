// DTI NC Map - Main JavaScript File
class DTIMap {
    constructor() {
        this.map = null;
        this.offices = [];
        this.regions = [];
        this.markers = [];
        this.isAdmin = document.getElementById('officeForm') !== null;
        
        this.init();
    }
    
    async init() {
        this.initMap();
        await this.loadRegions();
        await this.loadOffices();
        this.setupEventListeners();
    }
    
    initMap() {
        // Initialize map centered on Cebu
        this.map = L.map('map').setView([10.3157, 123.8854], 10);
        
        // Add tile layer
        L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
            attribution: '© OpenStreetMap contributors'
        }).addTo(this.map);
        
        // Add click event for adding new offices (admin only)
        if (this.isAdmin) {
            this.map.on('click', (e) => {
                document.getElementById('latitude').value = e.latlng.lat.toFixed(6);
                document.getElementById('longitude').value = e.latlng.lng.toFixed(6);
            });
        }
    }
    
        async loadRegions() {
        try {
            const response = await fetch('endpoint/get-regions.php');
            const data = await response.json();
            
            if (data.success) {
                this.regions = data.regions;
                this.populateRegionFilters();
            }
        } catch (error) {
            console.error('Error loading regions:', error);
        }
    }
    
    async loadOffices() {
        try {
            const response = await fetch('endpoint/get-offices.php');
            const data = await response.json();
            
            if (data.success) {
                this.offices = data.offices;
                this.displayOffices();
                this.populateOfficeList();
            } else {
                this.showNotification('Error loading offices: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error loading offices:', error);
            this.showNotification('Error loading offices', 'error');
        }
    }
    
    displayOffices() {
        // Clear existing markers
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];
        
        this.offices.forEach(office => {
            const marker = L.marker([office.latitude, office.longitude], {
                icon: this.createMarkerIcon(office.office_type)
            }).addTo(this.map);
            
            marker.bindPopup(this.createPopupContent(office));
            marker.on('click', () => this.showOfficeDetails(office.office_id));
            
            this.markers.push(marker);
        });
    }
    
    createMarkerIcon(officeType) {
        let className = 'nc-marker-icon';
        
        switch(officeType) {
            case 'Regional Office':
                className += ' regional-office';
                break;
            case 'Provincial Office':
                className += ' provincial-office';
                break;
            case 'Field Office':
                className += ' field-office';
                break;
            case 'Extension Office':
                className += ' extension-office';
                break;
            case 'Negosyo Center':
                className += ' negosyo-center';
                break;
        }
        
        return L.divIcon({
            html: '<div class="' + className + '">NC</div>',
            className: 'custom-marker',
            iconSize: [40, 40],
            iconAnchor: [20, 40],
            popupAnchor: [0, -40]
        });
    }
    
    createPopupContent(office) {
        const badgeClass = office.office_type.toLowerCase().replace(/\s+/g, '-');
        
        return `
            <div class="marker-popup">
                <div class="popup-header">
                    <h3 class="popup-title">${office.office_name}</h3>
                    <span class="popup-badge ${badgeClass}">${office.office_type}</span>
                </div>
                <div class="popup-body">
                    <div class="popup-info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span>${office.address}</span>
                    </div>
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
                </div>
                <div class="popup-footer">
                    <button class="popup-btn btn-primary" onclick="dtiMap.showOfficeDetails(${office.office_id})">
                        <i class="fas fa-info-circle"></i> View Details
                    </button>
                    ${this.isAdmin ? `
                        <button class="popup-btn btn-secondary" onclick="dtiMap.editOffice(${office.office_id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                    ` : ''}
                </div>
            </div>
        `;
    }
    
    populateRegionFilters() {
        const regionFilter = document.getElementById('regionFilter');
        const regionSelect = document.getElementById('regionId');
        
        this.regions.forEach(region => {
            const option = new Option(region.region_name, region.region_id);
            regionFilter.appendChild(option.cloneNode(true));
            
            if (regionSelect) {
                regionSelect.appendChild(option);
            }
        });
    }
    
    populateOfficeList() {
        const officeList = document.getElementById('officeList');
        
        if (this.offices.length === 0) {
            officeList.innerHTML = '<p class="no-results">No offices found</p>';
            return;
        }
        
        const listHTML = this.offices.map(office => `
            <div class="office-item" onclick="dtiMap.showOfficeDetails(${office.office_id})">
                <div class="office-header">
                    <div class="office-name">${office.office_name}</div>
                    <div class="office-type">${office.office_type}</div>
                </div>
                <div class="office-region ${office.region_id === 7 ? 'cebu-region' : 'other-region'}">
                    ${office.region_name}
                </div>
                <div class="office-address">${office.address}</div>
                ${this.isAdmin ? `
                    <div class="office-actions">
                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); dtiMap.showOfficeDetails(${office.office_id})">
                            <i class="fas fa-info-circle"></i> Details
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); dtiMap.editOffice(${office.office_id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); dtiMap.deleteOffice(${office.office_id}, '${office.office_name}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                ` : ''}
            </div>
        `).join('');
        
        officeList.innerHTML = listHTML;
    }
    
    async showOfficeDetails(officeId) {
        try {
            const response = await fetch(`endpoint/get-office-details.php?office_id=${officeId}`);
            const data = await response.json();
            
            if (data.success) {
                const office = data.office;
                this.displayOfficeModal(office);
            } else {
                this.showNotification('Error loading office details', 'error');
            }
        } catch (error) {
            console.error('Error loading office details:', error);
            this.showNotification('Error loading office details', 'error');
        }
    }
    
    displayOfficeModal(office) {
        const modalTitle = document.getElementById('modalTitle');
        const modalBody = document.getElementById('modalBody');
        
        modalTitle.textContent = office.office_name;
        
        const servicesHTML = office.services && office.services.length > 0 
            ? office.services.map(service => `<span class="service-tag">${service}</span>`).join('')
            : '<span class="no-services">No services listed</span>';
        
        const staffHTML = office.staff && office.staff.length > 0
            ? office.staff.map(staff => `
                <div class="staff-item">
                    <div class="staff-info">
                        <h4>${staff.staff_name}</h4>
                        <p class="staff-position">${staff.position}</p>
                        ${staff.contact_number ? `<p><i class="fas fa-phone"></i> ${staff.contact_number}</p>` : ''}
                        ${staff.email ? `<p><i class="fas fa-envelope"></i> ${staff.email}</p>` : ''}
                    </div>
                    ${this.isAdmin ? `
                        <div class="staff-actions">
                            <button class="btn btn-sm btn-secondary" onclick="dtiMap.editStaff(${staff.staff_id})">
                                <i class="fas fa-edit"></i> Edit
                            </button>
                            <button class="btn btn-sm btn-danger" onclick="dtiMap.deleteStaff(${staff.staff_id}, '${staff.staff_name}')">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                        </div>
                    ` : ''}
                </div>
            `).join('')
            : '<p class="no-staff">No staff information available</p>';
        
        modalBody.innerHTML = `
            <div class="office-details">
                <div class="office-info">
                    <div class="info-item">
                        <i class="fas fa-building"></i>
                        <span><strong>Type:</strong> ${office.office_type}</span>
                    </div>
                    <div class="info-item">
                        <i class="fas fa-map-marker-alt"></i>
                        <span><strong>Address:</strong> ${office.address}</span>
                    </div>
                    ${office.contact_number ? `
                        <div class="info-item">
                            <i class="fas fa-phone"></i>
                            <span><strong>Contact:</strong> ${office.contact_number}</span>
                        </div>
                    ` : ''}
                    ${office.email ? `
                        <div class="info-item">
                            <i class="fas fa-envelope"></i>
                            <span><strong>Email:</strong> ${office.email}</span>
                        </div>
                    ` : ''}
                    ${office.office_head ? `
                        <div class="info-item">
                            <i class="fas fa-user-tie"></i>
                            <span><strong>Office Head:</strong> ${office.office_head}</span>
                        </div>
                    ` : ''}
                    ${office.office_hours ? `
                        <div class="info-item">
                            <i class="fas fa-clock"></i>
                            <span><strong>Office Hours:</strong> ${office.office_hours}</span>
                        </div>
                    ` : ''}
                </div>
                
                ${office.description ? `
                    <div class="office-description">
                        <h3>About This Office</h3>
                        <p>${office.description}</p>
                    </div>
                ` : ''}
                
                <div class="office-services">
                    <h3>Services Offered</h3>
                    <div class="services-list">${servicesHTML}</div>
                </div>
                
                <div class="office-staff">
                    <div class="staff-header">
                        <h3>Staff Members</h3>
                        ${this.isAdmin ? `
                            <button class="btn btn-primary" onclick="dtiMap.addStaff(${office.office_id})">
                                <i class="fas fa-plus"></i> Add Staff
                            </button>
                        ` : ''}
                    </div>
                    <div class="staff-list">${staffHTML}</div>
                </div>
            </div>
        `;
        
        document.getElementById('officeModal').style.display = 'block';
    }
    
    setupEventListeners() {
        // Search functionality
        document.getElementById('searchInput').addEventListener('input', () => {
            this.searchOffices();
        });
        
        document.getElementById('regionFilter').addEventListener('change', () => {
            this.searchOffices();
        });
        
        document.getElementById('typeFilter').addEventListener('change', () => {
            this.searchOffices();
        });
        
        // Form submissions
        if (this.isAdmin) {
            document.getElementById('officeForm').addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveOffice();
            });
            
            document.getElementById('staffForm').addEventListener('submit', (e) => {
                e.preventDefault();
                this.saveStaff();
            });
        }
        
        // Modal close events
        window.addEventListener('click', (e) => {
            if (e.target.classList.contains('modal')) {
                e.target.style.display = 'none';
            }
        });
    }
    
    async searchOffices() {
        const searchTerm = document.getElementById('searchInput').value;
        const regionId = document.getElementById('regionFilter').value;
        const officeType = document.getElementById('typeFilter').value;
        
        let filteredOffices = this.offices;
        
        if (searchTerm) {
            filteredOffices = filteredOffices.filter(office => 
                office.office_name.toLowerCase().includes(searchTerm.toLowerCase()) ||
                office.address.toLowerCase().includes(searchTerm.toLowerCase()) ||
                (office.office_head && office.office_head.toLowerCase().includes(searchTerm.toLowerCase()))
            );
        }
        
        if (regionId) {
            filteredOffices = filteredOffices.filter(office => 
                office.region_id == regionId
            );
        }
        
        if (officeType) {
            filteredOffices = filteredOffices.filter(office => 
                office.office_type === officeType
            );
        }
        
        // Update display
        this.displayFilteredOffices(filteredOffices);
    }
    
    displayFilteredOffices(offices) {
        // Clear existing markers
        this.markers.forEach(marker => this.map.removeLayer(marker));
        this.markers = [];
        
        // Add filtered markers
        offices.forEach(office => {
            const marker = L.marker([office.latitude, office.longitude], {
                icon: this.createMarkerIcon(office.office_type)
            }).addTo(this.map);
            
            marker.bindPopup(this.createPopupContent(office));
            marker.on('click', () => this.showOfficeDetails(office.office_id));
            
            this.markers.push(marker);
        });
        
        // Update office list
        const officeList = document.getElementById('officeList');
        
        if (offices.length === 0) {
            officeList.innerHTML = '<p class="no-results">No offices found matching your criteria</p>';
            return;
        }
        
        const listHTML = offices.map(office => `
            <div class="office-item" onclick="dtiMap.showOfficeDetails(${office.office_id})">
                <div class="office-header">
                    <div class="office-name">${office.office_name}</div>
                    <div class="office-type">${office.office_type}</div>
                </div>
                <div class="office-region ${office.region_id === 7 ? 'cebu-region' : 'other-region'}">
                    ${office.region_name}
                </div>
                <div class="office-address">${office.address}</div>
                ${this.isAdmin ? `
                                        <div class="office-actions">
                        <button class="btn btn-sm btn-primary" onclick="event.stopPropagation(); dtiMap.showOfficeDetails(${office.office_id})">
                            <i class="fas fa-info-circle"></i> Details
                        </button>
                        <button class="btn btn-sm btn-secondary" onclick="event.stopPropagation(); dtiMap.editOffice(${office.office_id})">
                            <i class="fas fa-edit"></i> Edit
                        </button>
                        <button class="btn btn-sm btn-danger" onclick="event.stopPropagation(); dtiMap.deleteOffice(${office.office_id}, '${office.office_name}')">
                            <i class="fas fa-trash"></i> Delete
                        </button>
                    </div>
                ` : ''}
            </div>
        `).join('');
        
        officeList.innerHTML = listHTML;
        
        // Fit map to show all filtered offices
        if (offices.length > 0) {
            const group = new L.featureGroup(this.markers);
            this.map.fitBounds(group.getBounds().pad(0.1));
        }
    }
    
    // Admin Functions
    toggleAddOffice() {
        document.getElementById('officeForm').reset();
        document.getElementById('officeId').value = '';
        document.getElementById('officeModalTitle').textContent = 'Add New Office';
        document.getElementById('addOfficeModal').style.display = 'block';
    }
    
    async editOffice(officeId) {
        try {
            const response = await fetch(`endpoint/get-office-details.php?office_id=${officeId}`);
            const data = await response.json();
            
            if (data.success) {
                const office = data.office;
                this.populateOfficeForm(office);
                document.getElementById('officeModalTitle').textContent = 'Edit Office';
                document.getElementById('addOfficeModal').style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading office for edit:', error);
            this.showNotification('Error loading office details', 'error');
        }
    }
    
    populateOfficeForm(office) {
        document.getElementById('officeId').value = office.office_id;
        document.getElementById('officeName').value = office.office_name;
        document.getElementById('officeType').value = office.office_type;
        document.getElementById('regionId').value = office.region_id;
        document.getElementById('address').value = office.address;
        document.getElementById('latitude').value = office.latitude;
        document.getElementById('longitude').value = office.longitude;
        document.getElementById('contactNumber').value = office.contact_number || '';
        document.getElementById('email').value = office.email || '';
        document.getElementById('officeHead').value = office.office_head || '';
        document.getElementById('description').value = office.description || '';
        document.getElementById('servicesOffered').value = office.services_offered || '';
        document.getElementById('officeHours').value = office.office_hours || '';
    }
    
    async saveOffice() {
        const formData = new FormData(document.getElementById('officeForm'));
        const isEdit = document.getElementById('officeId').value !== '';
        
        try {
            const endpoint = isEdit ? 'endpoint/update-office.php' : 'endpoint/add-office.php';
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(isEdit ? 'Office updated successfully' : 'Office added successfully', 'success');
                document.getElementById('addOfficeModal').style.display = 'none';
                await this.loadOffices(); // Reload offices
            } else {
                this.showNotification('Error: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error saving office:', error);
            this.showNotification('Error saving office', 'error');
        }
    }
    
    async deleteOffice(officeId, officeName) {
        if (!confirm(`Are you sure you want to delete "${officeName}"?`)) {
            return;
        }
        
        try {
            const response = await fetch('endpoint/delete-office.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ office_id: officeId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Office deleted successfully', 'success');
                await this.loadOffices(); // Reload offices
            } else {
                this.showNotification('Error: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error deleting office:', error);
            this.showNotification('Error deleting office', 'error');
        }
    }
    
    addStaff(officeId) {
        document.getElementById('staffForm').reset();
        document.getElementById('staffId').value = '';
        document.getElementById('staffOfficeId').value = officeId;
        document.getElementById('staffModalTitle').textContent = 'Add Staff Member';
        document.getElementById('staffModal').style.display = 'block';
    }
    
    async editStaff(staffId) {
        try {
            const response = await fetch(`endpoint/get-staff-details.php?staff_id=${staffId}`);
            const data = await response.json();
            
            if (data.success) {
                const staff = data.staff;
                this.populateStaffForm(staff);
                document.getElementById('staffModalTitle').textContent = 'Edit Staff Member';
                document.getElementById('staffModal').style.display = 'block';
            }
        } catch (error) {
            console.error('Error loading staff for edit:', error);
            this.showNotification('Error loading staff details', 'error');
        }
    }
    
    populateStaffForm(staff) {
        document.getElementById('staffId').value = staff.staff_id;
        document.getElementById('staffOfficeId').value = staff.office_id;
        document.getElementById('staffName').value = staff.staff_name;
        document.getElementById('position').value = staff.position;
        document.getElementById('staffType').value = staff.staff_type || 'Regular';
        document.getElementById('staffContact').value = staff.contact_number || '';
        document.getElementById('staffEmail').value = staff.email || '';
        document.getElementById('staffServices').value = staff.services_offered || '';
        document.getElementById('bio').value = staff.bio || '';
    }
    
    async saveStaff() {
        const formData = new FormData(document.getElementById('staffForm'));
        const isEdit = document.getElementById('staffId').value !== '';
        
        try {
            const endpoint = isEdit ? 'endpoint/update-staff.php' : 'endpoint/add-staff.php';
            const response = await fetch(endpoint, {
                method: 'POST',
                body: formData
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification(isEdit ? 'Staff updated successfully' : 'Staff added successfully', 'success');
                document.getElementById('staffModal').style.display = 'none';
                document.getElementById('officeModal').style.display = 'none';
                // Refresh the office details if modal was open
                const officeId = document.getElementById('staffOfficeId').value;
                if (officeId) {
                    setTimeout(() => this.showOfficeDetails(officeId), 500);
                }
            } else {
                this.showNotification('Error: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error saving staff:', error);
            this.showNotification('Error saving staff', 'error');
        }
    }
    
    async deleteStaff(staffId, staffName) {
        if (!confirm(`Are you sure you want to delete "${staffName}"?`)) {
            return;
        }
        
        try {
            const response = await fetch('endpoint/delete-staff.php', {
                method: 'POST',
                headers: {
                    'Content-Type': 'application/json',
                },
                body: JSON.stringify({ staff_id: staffId })
            });
            
            const data = await response.json();
            
            if (data.success) {
                this.showNotification('Staff deleted successfully', 'success');
                document.getElementById('officeModal').style.display = 'none';
                // Refresh the office details
                const officeId = document.getElementById('staffOfficeId').value;
                if (officeId) {
                    setTimeout(() => this.showOfficeDetails(officeId), 500);
                }
            } else {
                this.showNotification('Error: ' + data.message, 'error');
            }
        } catch (error) {
            console.error('Error deleting staff:', error);
            this.showNotification('Error deleting staff', 'error');
        }
    }
    
    showNotification(message, type = 'info') {
        const notification = document.createElement('div');
        notification.className = `notification ${type}`;
        notification.innerHTML = `
            <span>${message}</span>
            <button onclick="this.parentElement.remove()">&times;</button>
        `;
        
        document.body.appendChild(notification);
        
        // Auto remove after 5 seconds
        setTimeout(() => {
            if (notification.parentElement) {
                notification.remove();
            }
        }, 5000);
    }
}

// Global functions for onclick events
function closeModal(modalId) {
    document.getElementById(modalId).style.display = 'none';
}

function toggleAddOffice() {
    dtiMap.toggleAddOffice();
}

function searchOffices() {
    dtiMap.searchOffices();
}

// Initialize the map when DOM is loaded
let dtiMap;
document.addEventListener('DOMContentLoaded', function() {
    dtiMap = new DTIMap();
});

