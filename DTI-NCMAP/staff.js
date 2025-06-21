/**
 * Staff Management JavaScript
 * Handles all staff-related functionality for the DTI Office Locator System
 */

// Global variables
let currentOfficeId = null;
let currentStaffId = null;

// Event listeners
document.addEventListener('DOMContentLoaded', function() {
    // Initialize staff functionality
    initializeStaffFunctions();
});

/**
 * Initialize staff management functions
 */
function initializeStaffFunctions() {
    // Staff form submission
    const staffForm = document.getElementById('staffForm');
    if (staffForm) {
        staffForm.addEventListener('submit', function(e) {
            e.preventDefault();
            saveStaffMember();
        });
    }
}

/**
 * Open the add staff modal
 */
function openAddStaffModal() {
    // Reset the form
    document.getElementById('staffForm').reset();
    
    // Set the office ID
    document.getElementById('staffOfficeId').value = currentOfficeId || 1;
    
    // Clear the staff ID (for new staff)
    document.getElementById('staffId').value = '';
    
    // Set modal title
    document.getElementById('staffModalTitle').textContent = 'Add Staff Member';
    
    // Show the modal
    document.getElementById('staffModal').classList.add('active');
}

/**
 * Close the staff modal
 */
function closeStaffModal() {
    document.getElementById('staffModal').classList.remove('active');
}

/**
 * Save a staff member (add or update)
 * This is a mock function for the front-end demo
 */
function saveStaffMember() {
    // Show loading overlay
    document.getElementById('loadingOverlay').classList.add('active');
    
    // Simulate server request
    setTimeout(function() {
        // Hide loading overlay
        document.getElementById('loadingOverlay').classList.remove('active');
        
        // Close the modal
        closeStaffModal();
        
        // Show success message (would be implemented in a real notification system)
        alert('Staff member saved successfully!');
    }, 1000);
}

/**
 * View staff details
 * @param {number} staffId - The ID of the staff member
 */
function viewStaffDetails(staffId) {
    currentStaffId = staffId;
    
    // Set the staff ID for the edit/delete buttons
    document.getElementById('editStaffBtn').setAttribute('onclick', `editStaffMember(${staffId})`);
    document.getElementById('deleteStaffBtn').setAttribute('onclick', `confirmDeleteStaff(${staffId})`);
    
    // Show the modal
    document.getElementById('staffDetailsModal').classList.add('active');
    
    // In a real implementation, you would fetch staff details from the server
    // For this demo, we're using the sample data already in the HTML
}

/**
 * Close the staff details modal
 */
function closeStaffDetailsModal() {
    document.getElementById('staffDetailsModal').classList.remove('active');
}

/**
 * Edit a staff member
 * @param {number} staffId - The ID of the staff member
 */
function editStaffMember(staffId) {
    // Close details modal if open
    closeStaffDetailsModal();
    
    // For demo purposes, pre-fill the form with sample data
    document.getElementById('staffId').value = staffId;
    document.getElementById('staffOfficeId').value = currentOfficeId || 1;
    
    if (staffId === 1) {
        document.getElementById('staffName').value = 'Maria Santos';
        document.getElementById('staffPosition').value = 'Division Chief, Business Development';
        document.getElementById('staffType').value = 'Type A';
        document.getElementById('staffServices').value = 'Business Name Registration, MSME Development, Business Advisory, Entrepreneurship Training';
        document.getElementById('staffContact').value = '(02) 8896-4498 loc. 101';
                document.getElementById('staffEmail').value = 'maria.santos@dti.gov.ph';
        document.getElementById('staffBio').value = 'Maria Santos has been with DTI for over 10 years, specializing in business development and MSME support. She holds a Master\'s degree in Business Administration from the University of the Philippines and has extensive experience in helping small businesses grow and succeed in competitive markets.';
    } else if (staffId === 2) {
        document.getElementById('staffName').value = 'Roberto Reyes';
        document.getElementById('staffPosition').value = 'Consumer Protection Officer';
        document.getElementById('staffType').value = 'Type B';
        document.getElementById('staffServices').value = 'Consumer Complaints, Product Standards Monitoring, Price Monitoring';
        document.getElementById('staffContact').value = '(02) 8896-4498 loc. 102';
        document.getElementById('staffEmail').value = 'roberto.reyes@dti.gov.ph';
        document.getElementById('staffBio').value = 'Roberto Reyes is a dedicated consumer protection officer with 5 years of experience at DTI. He specializes in handling consumer complaints and ensuring businesses comply with fair trade practices.';
    } else if (staffId === 3) {
        document.getElementById('staffName').value = 'Elena Cruz';
        document.getElementById('staffPosition').value = 'MSME Development Specialist';
        document.getElementById('staffType').value = 'Type A';
        document.getElementById('staffServices').value = 'MSME Development, Business Advisory, Entrepreneurship Training, Access to Finance';
        document.getElementById('staffContact').value = '(02) 8896-4498 loc. 103';
        document.getElementById('staffEmail').value = 'elena.cruz@dti.gov.ph';
        document.getElementById('staffBio').value = 'Elena Cruz has been supporting MSMEs for over 8 years. She specializes in helping small businesses access financing options and government support programs.';
    }
    
    // Set modal title
    document.getElementById('staffModalTitle').textContent = 'Edit Staff Member';
    
    // Show the modal
    document.getElementById('staffModal').classList.add('active');
}

/**
 * Confirm deletion of a staff member
 * @param {number} staffId - The ID of the staff member to delete
 */
function confirmDeleteStaff(staffId) {
    // Set up confirmation modal
    document.getElementById('confirmationTitle').textContent = 'Delete Staff Member';
    document.getElementById('confirmationBody').innerHTML = `
        <p>Are you sure you want to delete this staff member?</p>
        <p class="text-danger"><strong>This action cannot be undone.</strong></p>
    `;
    
    // Set up confirm button
    const confirmBtn = document.getElementById('confirmActionBtn');
    confirmBtn.textContent = 'Delete';
    confirmBtn.onclick = function() {
        deleteStaffMember(staffId);
        closeConfirmationModal();
    };
    
    // Show confirmation modal
    document.getElementById('confirmationModal').classList.add('active');
}

/**
 * Delete a staff member
 * This is a mock function for the front-end demo
 * @param {number} staffId - The ID of the staff member to delete
 */
function deleteStaffMember(staffId) {
    // Show loading overlay
    document.getElementById('loadingOverlay').classList.add('active');
    
    // Close staff details modal
    closeStaffDetailsModal();
    
    // Simulate server request
    setTimeout(function() {
        // Hide loading overlay
        document.getElementById('loadingOverlay').classList.remove('active');
        
        // Show success message (would be implemented in a real notification system)
        alert('Staff member deleted successfully!');
        
        // In a real implementation, you would remove the staff card from the DOM
        const staffCard = document.querySelector(`.staff-card[data-id="${staffId}"]`);
        if (staffCard) {
            staffCard.remove();
        }
    }, 1000);
}

/**
 * Close the confirmation modal
 */
function closeConfirmationModal() {
    document.getElementById('confirmationModal').classList.remove('active');
}

/**
 * Load staff members for an office
 * This is a mock function for the front-end demo
 * @param {number} officeId - The ID of the office
 */
function loadStaffMembers(officeId) {
    // Store the current office ID
    currentOfficeId = officeId;
    
    // In a real implementation, you would fetch staff data from the server
    // For this demo, we're using the sample data already in the HTML
    console.log(`Loading staff members for office ID: ${officeId}`);
}

/**
 * View office details and load staff
 * This function extends the existing viewOfficeDetails function
 * @param {number} officeId - The ID of the office
 */
function viewOfficeDetails(officeId) {
    // In a real implementation, this would be handled by the main.js file
    // For this demo, we're just showing the modal with sample data
    document.getElementById('officeModal').classList.add('active');
    
    // Load staff members for this office
    loadStaffMembers(officeId);
}

/**
 * Close the office details modal
 */
function closeModal() {
    document.getElementById('officeModal').classList.remove('active');
}

// Add notification system (would be implemented in a real application)
function showNotification(message, type) {
    // Simple alert for demo purposes
    alert(message);
}

