// Admin Dashboard JavaScript
document.addEventListener('DOMContentLoaded', function() {
    initializeAdmin();
});

function initializeAdmin() {
    // Initialize sidebar toggle
    initSidebarToggle();
    
    // Initialize user dropdown
    initUserDropdown();
    
    // Initialize modals
    initModals();
    
    // Initialize form validations
    initFormValidations();
    
    // Initialize tooltips
    initTooltips();
    
    // Initialize search functionality
    initSearch();
}

// Sidebar Toggle Functionality
function initSidebarToggle() {
    const sidebarToggle = document.getElementById('sidebarToggle');
    const sidebar = document.getElementById('adminSidebar');
    const mainContent = document.getElementById('adminMain');
    
    if (sidebarToggle && sidebar && mainContent) {
        sidebarToggle.addEventListener('click', function() {
            sidebar.classList.toggle('collapsed');
            mainContent.classList.toggle('sidebar-collapsed');
            
            // Save state to localStorage
            const isCollapsed = sidebar.classList.contains('collapsed');
            localStorage.setItem('sidebarCollapsed', isCollapsed);
        });
        
        // Restore sidebar state
        const savedState = localStorage.getItem('sidebarCollapsed');
        if (savedState === 'true') {
            sidebar.classList.add('collapsed');
            mainContent.classList.add('sidebar-collapsed');
        }
    }
}

// User Dropdown Functionality
function initUserDropdown() {
    const userBtn = document.getElementById('userDropdownBtn');
    const userMenu = document.getElementById('userDropdownMenu');
    
    if (userBtn && userMenu) {
        userBtn.addEventListener('click', function(e) {
            e.stopPropagation();
            userMenu.classList.toggle('show');
        });
        
        // Close dropdown when clicking outside
        document.addEventListener('click', function() {
            userMenu.classList.remove('show');
        });
        
        // Prevent dropdown from closing when clicking inside
        userMenu.addEventListener('click', function(e) {
            e.stopPropagation();
        });
    }
}

// Modal Functionality
function initModals() {
    // Close modals when clicking outside
    document.addEventListener('click', function(e) {
        if (e.target.classList.contains('modal')) {
            e.target.style.display = 'none';
        }
    });
    
    // Close modals with Escape key
    document.addEventListener('keydown', function(e) {
        if (e.key === 'Escape') {
            const openModals = document.querySelectorAll('.modal[style*="block"]');
            openModals.forEach(modal => {
                modal.style.display = 'none';
            });
        }
    });
}

// Form Validation
function initFormValidations() {
    const forms = document.querySelectorAll('form[data-validate]');
    
    forms.forEach(form => {
        form.addEventListener('submit', function(e) {
            if (!validateForm(this)) {
                e.preventDefault();
            }
        });
        
        // Real-time validation
        const inputs = form.querySelectorAll('input, select, textarea');
        inputs.forEach(input => {
            input.addEventListener('blur', function() {
                validateField(this);
            });
        });
    });
}

function validateForm(form) {
    let isValid = true;
    const requiredFields = form.querySelectorAll('[required]');
    
    requiredFields.forEach(field => {
        if (!validateField(field)) {
            isValid = false;
        }
    });
    
    return isValid;
}

function validateField(field) {
    const value = field.value.trim();
    const fieldName = field.name || field.id;
    let isValid = true;
    let errorMessage = '';
    
    // Remove existing error styling
    field.classList.remove('error');
    removeFieldError(field);
    
    // Required field validation
    if (field.hasAttribute('required') && !value) {
        isValid = false;
        errorMessage = `${getFieldLabel(field)} is required`;
    }
    
    // Email validation
    if (field.type === 'email' && value) {
        const emailRegex = /^[^\s@]+@[^\s@]+\.[^\s@]+$/;
        if (!emailRegex.test(value)) {
            isValid = false;
            errorMessage = 'Please enter a valid email address';
        }
    }
    
    // Phone validation
    if (field.type === 'tel' && value) {
        const phoneRegex = /^[\+]?[1-9][\d]{0,15}$/;
        if (!phoneRegex.test(value.replace(/[\s\-\(\)]/g, ''))) {
            isValid = false;
            errorMessage = 'Please enter a valid phone number';
        }
    }
    
    // URL validation
    if (field.type === 'url' && value) {
        try {
            new URL(value);
        } catch {
            isValid = false;
            errorMessage = 'Please enter a valid URL';
        }
    }
    
    // Show error if validation failed
    if (!isValid) {
        field.classList.add('error');
        showFieldError(field, errorMessage);
    }
    
    return isValid;
}

function getFieldLabel(field) {
    const label = document.querySelector(`label[for="${field.id}"]`);
    return label ? label.textContent.replace('*', '').trim() : field.name || 'Field';
}

function showFieldError(field, message) {
    const errorElement = document.createElement('div');
    errorElement.className = 'field-error';
    errorElement.textContent = message;
    
    field.parentNode.appendChild(errorElement);
}

function removeFieldError(field) {
    const existingError = field.parentNode.querySelector('.field-error');
    if (existingError) {
        existingError.remove();
    }
}

// Tooltip Functionality
function initTooltips() {
    const tooltipElements = document.querySelectorAll('[data-tooltip]');
    
    tooltipElements.forEach(element => {
        element.addEventListener('mouseenter', showTooltip);
        element.addEventListener('mouseleave', hideTooltip);
    });
}

function showTooltip(e) {
    const element = e.target;
    const tooltipText = element.getAttribute('data-tooltip');
    
    if (!tooltipText) return;
    
    const tooltip = document.createElement('div');
    tooltip.className = 'tooltip';
    tooltip.textContent = tooltipText;
    
    document.body.appendChild(tooltip);
    
    const rect = element.getBoundingClientRect();
    tooltip.style.left = rect.left + (rect.width / 2) - (tooltip.offsetWidth / 2) + 'px';
    tooltip.style.top = rect.top - tooltip.offsetHeight - 10 + 'px';
    
    element._tooltip = tooltip;
}

function hideTooltip(e) {
    const element = e.target;
    if (element._tooltip) {
        element._tooltip.remove();
        delete element._tooltip;
    }
}

// Search Functionality (continued)
function initSearch() {
    const searchInputs = document.querySelectorAll('[data-search]');
    
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const targetSelector = this.getAttribute('data-search');
            const targets = document.querySelectorAll(targetSelector);
            
            targets.forEach(target => {
                const text = target.textContent.toLowerCase();
                const shouldShow = text.includes(searchTerm);
                
                target.style.display = shouldShow ? '' : 'none';
            });
        });
    });
}

// Notification System
function showNotification(message, type = 'info', duration = 5000) {
    const notification = document.createElement('div');
    notification.className = `notification notification-${type}`;
    notification.innerHTML = `
        <div class="notification-content">
            <i class="fas fa-${getNotificationIcon(type)}"></i>
            <span>${message}</span>
        </div>
        <button class="notification-close" onclick="closeNotification(this)">
            <i class="fas fa-times"></i>
        </button>
    `;
    
    // Add to notification container or create one
    let container = document.getElementById('notificationContainer');
    if (!container) {
        container = document.createElement('div');
        container.id = 'notificationContainer';
        container.className = 'notification-container';
        document.body.appendChild(container);
    }
    
    container.appendChild(notification);
    
    // Auto-remove after duration
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, duration);
    
    // Add entrance animation
    setTimeout(() => {
        notification.classList.add('show');
    }, 10);
}

function getNotificationIcon(type) {
    const icons = {
        success: 'check-circle',
        error: 'exclamation-circle',
        warning: 'exclamation-triangle',
        info: 'info-circle'
    };
    return icons[type] || 'info-circle';
}

function closeNotification(button) {
    const notification = button.closest('.notification');
    notification.classList.add('hide');
    setTimeout(() => {
        notification.remove();
    }, 300);
}

// Loading States
function showLoading(element) {
    element.classList.add('loading');
    element.disabled = true;
}

function hideLoading(element) {
    element.classList.remove('loading');
    element.disabled = false;
}

// Data Table Functionality
function initDataTable(tableId, options = {}) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const defaultOptions = {
        sortable: true,
        searchable: true,
        pagination: true,
        pageSize: 10
    };
    
    const config = { ...defaultOptions, ...options };
    
    if (config.sortable) {
        initTableSorting(table);
    }
    
    if (config.searchable) {
        initTableSearch(table);
    }
    
    if (config.pagination) {
        initTablePagination(table, config.pageSize);
    }
}

function initTableSorting(table) {
    const headers = table.querySelectorAll('th[data-sortable]');
    
    headers.forEach(header => {
        header.style.cursor = 'pointer';
        header.innerHTML += ' <i class="fas fa-sort sort-icon"></i>';
        
        header.addEventListener('click', function() {
            const column = this.getAttribute('data-sortable');
            const currentSort = this.getAttribute('data-sort') || 'asc';
            const newSort = currentSort === 'asc' ? 'desc' : 'asc';
            
            // Reset all other headers
            headers.forEach(h => {
                h.setAttribute('data-sort', '');
                h.querySelector('.sort-icon').className = 'fas fa-sort sort-icon';
            });
            
            // Set current header
            this.setAttribute('data-sort', newSort);
            this.querySelector('.sort-icon').className = `fas fa-sort-${newSort === 'asc' ? 'up' : 'down'} sort-icon`;
            
            sortTable(table, column, newSort);
        });
    });
}

function sortTable(table, column, direction) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    
    rows.sort((a, b) => {
        const aValue = a.querySelector(`[data-column="${column}"]`)?.textContent || '';
        const bValue = b.querySelector(`[data-column="${column}"]`)?.textContent || '';
        
        const comparison = aValue.localeCompare(bValue, undefined, { numeric: true });
        return direction === 'asc' ? comparison : -comparison;
    });
    
    rows.forEach(row => tbody.appendChild(row));
}

function initTableSearch(table) {
    const searchInput = document.querySelector(`[data-table-search="${table.id}"]`);
    if (!searchInput) return;
    
    searchInput.addEventListener('input', function() {
        const searchTerm = this.value.toLowerCase();
        const rows = table.querySelectorAll('tbody tr');
        
        rows.forEach(row => {
            const text = row.textContent.toLowerCase();
            row.style.display = text.includes(searchTerm) ? '' : 'none';
        });
    });
}

function initTablePagination(table, pageSize) {
    const tbody = table.querySelector('tbody');
    const rows = Array.from(tbody.querySelectorAll('tr'));
    let currentPage = 1;
    const totalPages = Math.ceil(rows.length / pageSize);
    
    // Create pagination container
    const paginationContainer = document.createElement('div');
    paginationContainer.className = 'table-pagination';
    table.parentNode.appendChild(paginationContainer);
    
    function showPage(page) {
        const start = (page - 1) * pageSize;
        const end = start + pageSize;
        
        rows.forEach((row, index) => {
            row.style.display = (index >= start && index < end) ? '' : 'none';
        });
        
        updatePaginationControls();
    }
    
    function updatePaginationControls() {
        paginationContainer.innerHTML = `
            <div class="pagination-info">
                Showing ${((currentPage - 1) * pageSize) + 1} to ${Math.min(currentPage * pageSize, rows.length)} of ${rows.length} entries
            </div>
            <div class="pagination-controls">
                <button class="btn btn-secondary" ${currentPage === 1 ? 'disabled' : ''} onclick="changePage(${currentPage - 1})">
                    <i class="fas fa-chevron-left"></i> Previous
                </button>
                <span class="page-numbers">
                    ${generatePageNumbers()}
                </span>
                <button class="btn btn-secondary" ${currentPage === totalPages ? 'disabled' : ''} onclick="changePage(${currentPage + 1})">
                    Next <i class="fas fa-chevron-right"></i>
                </button>
            </div>
        `;
    }
    
    function generatePageNumbers() {
        let html = '';
        const maxVisible = 5;
        let start = Math.max(1, currentPage - Math.floor(maxVisible / 2));
        let end = Math.min(totalPages, start + maxVisible - 1);
        
        if (end - start + 1 < maxVisible) {
            start = Math.max(1, end - maxVisible + 1);
        }
        
        for (let i = start; i <= end; i++) {
            html += `<button class="btn ${i === currentPage ? 'btn-primary' : 'btn-secondary'}" onclick="changePage(${i})">${i}</button>`;
        }
        
        return html;
    }
    
    window.changePage = function(page) {
        if (page >= 1 && page <= totalPages) {
            currentPage = page;
            showPage(currentPage);
        }
    };
    
    // Initialize first page
    showPage(1);
}

// File Upload Functionality
function initFileUpload() {
    const fileInputs = document.querySelectorAll('input[type="file"]');
    
    fileInputs.forEach(input => {
        input.addEventListener('change', function() {
            const files = this.files;
            const maxSize = this.getAttribute('data-max-size') || 5 * 1024 * 1024; // 5MB default
            const allowedTypes = this.getAttribute('data-allowed-types')?.split(',') || [];
            
            for (let file of files) {
                if (file.size > maxSize) {
                    showNotification(`File "${file.name}" is too large. Maximum size is ${formatFileSize(maxSize)}.`, 'error');
                    this.value = '';
                    return;
                }
                
                if (allowedTypes.length > 0 && !allowedTypes.includes(file.type)) {
                    showNotification(`File "${file.name}" has an invalid type. Allowed types: ${allowedTypes.join(', ')}.`, 'error');
                    this.value = '';
                    return;
                }
            }
            
            // Show file preview if applicable
            if (this.getAttribute('data-preview')) {
                showFilePreview(this, files[0]);
            }
        });
    });
}

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function showFilePreview(input, file) {
    const previewContainer = document.getElementById(input.getAttribute('data-preview'));
    if (!previewContainer) return;
    
    if (file.type.startsWith('image/')) {
        const reader = new FileReader();
        reader.onload = function(e) {
            previewContainer.innerHTML = `<img src="${e.target.result}" alt="Preview" style="max-width: 200px; max-height: 200px; object-fit: cover; border-radius: 8px;">`;
        };
        reader.readAsDataURL(file);
    } else {
        previewContainer.innerHTML = `<div class="file-info"><i class="fas fa-file"></i> ${file.name}</div>`;
    }
}

// AJAX Helper Functions
function makeRequest(url, options = {}) {
    const defaultOptions = {
        method: 'GET',
        headers: {
            'Content-Type': 'application/json',
        },
    };
    
    const config = { ...defaultOptions, ...options };
    
    return fetch(url, config)
        .then(response => {
            if (!response.ok) {
                throw new Error(`HTTP error! status: ${response.status}`);
            }
            return response.json();
        })
        .catch(error => {
            console.error('Request failed:', error);
            showNotification('An error occurred while processing your request.', 'error');
            throw error;
        });
}

function submitForm(form, successCallback, errorCallback) {
    const formData = new FormData(form);
    const submitButton = form.querySelector('button[type="submit"]');
    
    if (submitButton) {
        showLoading(submitButton);
    }
    
    fetch(form.action || window.location.href, {
        method: form.method || 'POST',
        body: formData
    })
    .then(response => response.json())
    .then(data => {
        if (data.success) {
            if (successCallback) {
                successCallback(data);
            } else {
                showNotification(data.message || 'Operation completed successfully!', 'success');
            }
        } else {
            if (errorCallback) {
                errorCallback(data);
            } else {
                showNotification(data.message || 'An error occurred.', 'error');
            }
        }
    })
    .catch(error => {
        console.error('Form submission failed:', error);
        if (errorCallback) {
            errorCallback({ message: 'Network error occurred.' });
        } else {
            showNotification('Network error occurred.', 'error');
        }
    })
    .finally(() => {
        if (submitButton) {
            hideLoading(submitButton);
        }
    });
}

// Confirmation Dialog
function showConfirmDialog(message, onConfirm, onCancel) {
    const dialog = document.createElement('div');
    dialog.className = 'modal';
    dialog.innerHTML = `
        <div class="modal-content" style="max-width: 400px;">
            <div class="modal-header">
                <h3 class="modal-title">Confirm Action</h3>
            </div>
            <div class="modal-body">
                <p>${message}</p>
            </div>
            <div class="modal-footer">
                <button class="btn btn-secondary" onclick="closeConfirmDialog(false)">Cancel</button>
                <button class="btn btn-danger" onclick="closeConfirmDialog(true)">Confirm</button>
            </div>
        </div>
    `;
    
    document.body.appendChild(dialog);
    dialog.style.display = 'block';
    
    window.closeConfirmDialog = function(confirmed) {
        dialog.remove();
        delete window.closeConfirmDialog;
        
        if (confirmed && onConfirm) {
            onConfirm();
        } else if (!confirmed && onCancel) {
            onCancel();
        }
    };
}

// Export Functions
function exportTableToCSV(tableId, filename = 'export.csv') {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    const rows = table.querySelectorAll('tr');
    const csvContent = [];
    
    rows.forEach(row => {
        const cols = row.querySelectorAll('th, td');
        const rowData = Array.from(cols).map(col => {
            return '"' + col.textContent.replace(/"/g, '""') + '"';
        });
        csvContent.push(rowData.join(','));
    });
    
    const csvString = csvContent.join('\n');
    downloadFile(csvString, filename, 'text/csv');
}

function downloadFile(content, filename, contentType) {
    const blob = new Blob([content], { type: contentType });
    const url = window.URL.createObjectURL(blob);
    const link = document.createElement('a');
    link.href = url;
    link.download = filename;
    document.body.appendChild(link);
    link.click();
    document.body.removeChild(link);
    window.URL.revokeObjectURL(url);
}

// Initialize file upload when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initFileUpload();
});

// Global error handler
window.addEventListener('error', function(e) {
    console.error('Global error:', e.error);
    showNotification('An unexpected error occurred.', 'error');
});

// Utility Functions (continued)
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

function throttle(func, limit) {
    let inThrottle;
    return function() {
        const args = arguments;
        const context = this;
        if (!inThrottle) {
            func.apply(context, args);
            inThrottle = true;
            setTimeout(() => inThrottle = false, limit);
        }
    };
}

function formatDate(date, format = 'YYYY-MM-DD') {
    const d = new Date(date);
    const year = d.getFullYear();
    const month = String(d.getMonth() + 1).padStart(2, '0');
    const day = String(d.getDate()).padStart(2, '0');
    const hours = String(d.getHours()).padStart(2, '0');
    const minutes = String(d.getMinutes()).padStart(2, '0');
    
    return format
        .replace('YYYY', year)
        .replace('MM', month)
        .replace('DD', day)
        .replace('HH', hours)
        .replace('mm', minutes);
}

function formatCurrency(amount, currency = 'PHP') {
    return new Intl.NumberFormat('en-PH', {
        style: 'currency',
        currency: currency
    }).format(amount);
}

function sanitizeHTML(str) {
    const temp = document.createElement('div');
    temp.textContent = str;
    return temp.innerHTML;
}

// Local Storage Helpers
function saveToStorage(key, data) {
    try {
        localStorage.setItem(key, JSON.stringify(data));
        return true;
    } catch (error) {
        console.error('Failed to save to localStorage:', error);
        return false;
    }
}

function loadFromStorage(key, defaultValue = null) {
    try {
        const item = localStorage.getItem(key);
        return item ? JSON.parse(item) : defaultValue;
    } catch (error) {
        console.error('Failed to load from localStorage:', error);
        return defaultValue;
    }
}

function removeFromStorage(key) {
    try {
        localStorage.removeItem(key);
        return true;
    } catch (error) {
        console.error('Failed to remove from localStorage:', error);
        return false;
    }
}

// Form Auto-save Functionality
function initAutoSave(formId, interval = 30000) {
    const form = document.getElementById(formId);
    if (!form) return;
    
    const storageKey = `autosave_${formId}`;
    
    // Load saved data
    const savedData = loadFromStorage(storageKey);
    if (savedData) {
        Object.keys(savedData).forEach(name => {
            const field = form.querySelector(`[name="${name}"]`);
            if (field && field.type !== 'file') {
                field.value = savedData[name];
            }
        });
        
        showNotification('Draft restored from auto-save', 'info', 3000);
    }
    
    // Auto-save function
    const autoSave = debounce(() => {
        const formData = new FormData(form);
        const data = {};
        
        for (let [key, value] of formData.entries()) {
            if (form.querySelector(`[name="${key}"]`).type !== 'file') {
                data[key] = value;
            }
        }
        
        saveToStorage(storageKey, data);
    }, 2000);
    
    // Listen for changes
    form.addEventListener('input', autoSave);
    form.addEventListener('change', autoSave);
    
    // Clear auto-save on successful submit
    form.addEventListener('submit', function() {
        setTimeout(() => {
            removeFromStorage(storageKey);
        }, 1000);
    });
    
    // Periodic save
    setInterval(autoSave, interval);
}

// Image Optimization
function optimizeImage(file, maxWidth = 800, maxHeight = 600, quality = 0.8) {
    return new Promise((resolve) => {
        const canvas = document.createElement('canvas');
        const ctx = canvas.getContext('2d');
        const img = new Image();
        
        img.onload = function() {
            // Calculate new dimensions
            let { width, height } = img;
            
            if (width > height) {
                if (width > maxWidth) {
                    height = (height * maxWidth) / width;
                    width = maxWidth;
                }
            } else {
                if (height > maxHeight) {
                    width = (width * maxHeight) / height;
                    height = maxHeight;
                }
            }
            
            canvas.width = width;
            canvas.height = height;
            
            // Draw and compress
            ctx.drawImage(img, 0, 0, width, height);
            
            canvas.toBlob(resolve, 'image/jpeg', quality);
        };
        
        img.src = URL.createObjectURL(file);
    });
}

// Bulk Actions
function initBulkActions(tableId) {
    const table = document.getElementById(tableId);
    if (!table) return;
    
    // Add master checkbox
    const headerCheckbox = table.querySelector('th input[type="checkbox"]');
    const rowCheckboxes = table.querySelectorAll('tbody input[type="checkbox"]');
    
    if (headerCheckbox) {
        headerCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActions();
        });
    }
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', function() {
            updateBulkActions();
            
            // Update master checkbox
            if (headerCheckbox) {
                const checkedCount = table.querySelectorAll('tbody input[type="checkbox"]:checked').length;
                headerCheckbox.checked = checkedCount === rowCheckboxes.length;
                headerCheckbox.indeterminate = checkedCount > 0 && checkedCount < rowCheckboxes.length;
            }
        });
    });
    
    function updateBulkActions() {
        const checkedBoxes = table.querySelectorAll('tbody input[type="checkbox"]:checked');
        const bulkActions = document.querySelector('.bulk-actions');
        
        if (bulkActions) {
            bulkActions.style.display = checkedBoxes.length > 0 ? 'block' : 'none';
            
            const countElement = bulkActions.querySelector('.selected-count');
            if (countElement) {
                countElement.textContent = checkedBoxes.length;
            }
        }
    }
}

function getSelectedIds(tableId) {
    const table = document.getElementById(tableId);
    const checkedBoxes = table.querySelectorAll('tbody input[type="checkbox"]:checked');
    return Array.from(checkedBoxes).map(checkbox => checkbox.value);
}

// Dashboard Statistics Animation
function animateCounters() {
    const counters = document.querySelectorAll('[data-counter]');
    
    counters.forEach(counter => {
        const target = parseInt(counter.getAttribute('data-counter'));
        const duration = 2000;
        const step = target / (duration / 16);
        let current = 0;
        
        const timer = setInterval(() => {
            current += step;
            if (current >= target) {
                current = target;
                clearInterval(timer);
            }
            counter.textContent = Math.floor(current).toLocaleString();
        }, 16);
    });
}

// Chart Helpers (for dashboard charts)
function createSimpleChart(canvasId, data, options = {}) {
    const canvas = document.getElementById(canvasId);
    if (!canvas) return;
    
    const ctx = canvas.getContext('2d');
    const defaultOptions = {
        type: 'bar',
        responsive: true,
        maintainAspectRatio: false
    };
    
    const config = { ...defaultOptions, ...options, data };
    
    // Simple chart implementation (you might want to use Chart.js instead)
    drawSimpleChart(ctx, config);
}

function drawSimpleChart(ctx, config) {
    const { data, type } = config;
    const canvas = ctx.canvas;
    const width = canvas.width;
    const height = canvas.height;
    
    ctx.clearRect(0, 0, width, height);
    
    if (type === 'bar') {
        drawBarChart(ctx, data, width, height);
    } else if (type === 'line') {
        drawLineChart(ctx, data, width, height);
    }
}

function drawBarChart(ctx, data, width, height) {
    const padding = 40;
    const chartWidth = width - (padding * 2);
    const chartHeight = height - (padding * 2);
    
    const maxValue = Math.max(...data.datasets[0].data);
    const barWidth = chartWidth / data.labels.length;
    
    // Draw bars
    data.datasets[0].data.forEach((value, index) => {
        const barHeight = (value / maxValue) * chartHeight;
        const x = padding + (index * barWidth) + (barWidth * 0.1);
        const y = height - padding - barHeight;
        
        ctx.fillStyle = data.datasets[0].backgroundColor || '#0D47A1';
        ctx.fillRect(x, y, barWidth * 0.8, barHeight);
        
        // Draw labels
        ctx.fillStyle = '#333';
        ctx.font = '12px Arial';
        ctx.textAlign = 'center';
        ctx.fillText(data.labels[index], x + (barWidth * 0.4), height - padding + 20);
        
        // Draw values
        ctx.fillText(value, x + (barWidth * 0.4), y - 5);
    });
}

// Keyboard Shortcuts
function initKeyboardShortcuts() {
    document.addEventListener('keydown', function(e) {
        // Ctrl/Cmd + S to save
        if ((e.ctrlKey || e.metaKey) && e.key === 's') {
            e.preventDefault();
            const activeForm = document.querySelector('form:focus-within');
            if (activeForm) {
                activeForm.dispatchEvent(new Event('submit'));
            }
        }
        
        // Ctrl/Cmd + N for new item
        if ((e.ctrlKey || e.metaKey) && e.key === 'n') {
            e.preventDefault();
            const addButton = document.querySelector('[data-action="add"]');
            if (addButton) {
                addButton.click();
            }
        }
        
        // Escape to close modals
        if (e.key === 'Escape') {
            const openModal = document.querySelector('.modal[style*="block"]');
            if (openModal) {
                openModal.style.display = 'none';
            }
        }
    });
}

// Performance Monitoring
function initPerformanceMonitoring() {
    // Monitor page load time
    window.addEventListener('load', function() {
        const loadTime = performance.now();
        console.log(`Page loaded in ${loadTime.toFixed(2)}ms`);
        
        // Send to analytics if needed
        if (loadTime > 3000) {
            console.warn('Slow page load detected');
        }
    });
    
    // Monitor AJAX requests
    const originalFetch = window.fetch;
    window.fetch = function(...args) {
        const start = performance.now();
        return originalFetch.apply(this, args)
            .then(response => {
                const duration = performance.now() - start;
                console.log(`Request to ${args[0]} took ${duration.toFixed(2)}ms`);
                return response;
            });
    };
}

// Initialize everything when DOM is ready
document.addEventListener('DOMContentLoaded', function() {
    initKeyboardShortcuts();
    initPerformanceMonitoring();
    
    // Animate counters if they exist
    if (document.querySelector('[data-counter]')) {
        setTimeout(animateCounters, 500);
    }
    
    // Initialize bulk actions for tables
    const tables = document.querySelectorAll('table[data-bulk-actions]');
    tables.forEach(table => {
        initBulkActions(table.id);
    });
    
    // Initialize auto-save for forms
    const autoSaveForms = document.querySelectorAll('form[data-auto-save]');
    autoSaveForms.forEach(form => {
        initAutoSave(form.id);
    });
});

// Export functions for global use
window.AdminJS = {
    showNotification,
    showConfirmDialog,
    makeRequest,
    submitForm,
    exportTableToCSV,
    initDataTable,
    optimizeImage,
    getSelectedIds,
    formatDate,
    formatCurrency,
    saveToStorage,
    loadFromStorage,
    removeFromStorage
};
