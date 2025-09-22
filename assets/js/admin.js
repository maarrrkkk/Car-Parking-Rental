// Admin Dashboard JavaScript

// Initialize charts when DOM is loaded
document.addEventListener('DOMContentLoaded', function() {
    initializeCharts();
    setupEventListeners();
});

// Revenue Chart
function initializeCharts() {
    // Revenue Chart
    const revenueCtx = document.getElementById('revenueChart');
    if (revenueCtx) {
        new Chart(revenueCtx, {
            type: 'line',
            data: {
                labels: ['Jan', 'Feb', 'Mar', 'Apr', 'May', 'Jun', 'Jul'],
                datasets: [{
                    label: 'Revenue ($)',
                    data: [15000, 18000, 22000, 19000, 25000, 28000, 32000],
                    borderColor: '#007bff',
                    backgroundColor: 'rgba(0, 123, 255, 0.1)',
                    borderWidth: 2,
                    fill: true,
                    tension: 0.4
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                scales: {
                    y: {
                        beginAtZero: true,
                        ticks: {
                            callback: function(value) {
                                return '$' + value.toLocaleString();
                            }
                        }
                    }
                },
                plugins: {
                    legend: {
                        display: false
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                return 'Revenue: $' + context.parsed.y.toLocaleString();
                            }
                        }
                    }
                }
            }
        });
    }

    // Utilization Chart
    const utilizationCtx = document.getElementById('utilizationChart');
    if (utilizationCtx) {
        new Chart(utilizationCtx, {
            type: 'doughnut',
            data: {
                labels: ['Occupied', 'Available'],
                datasets: [{
                    data: [634, 216],
                    backgroundColor: ['#28a745', '#dc3545'],
                    borderWidth: 0
                }]
            },
            options: {
                responsive: true,
                maintainAspectRatio: false,
                plugins: {
                    legend: {
                        position: 'bottom',
                        labels: {
                            padding: 20,
                            usePointStyle: true
                        }
                    },
                    tooltip: {
                        callbacks: {
                            label: function(context) {
                                const total = context.dataset.data.reduce((sum, value) => sum + value, 0);
                                const percentage = ((context.parsed / total) * 100).toFixed(1);
                                return context.label + ': ' + context.parsed + ' spaces (' + percentage + '%)';
                            }
                        }
                    }
                }
            }
        });
    }
}

// Setup event listeners
function setupEventListeners() {
    // Search functionality
    setupTableSearch();
    
    // Filter functionality
    setupTableFilters();
    
    // Refresh buttons
    setupRefreshButtons();
    
    // Auto-refresh for real-time data
    setupAutoRefresh();
}

// Table search functionality
function setupTableSearch() {
    const searchInputs = document.querySelectorAll('input[placeholder*="Search"]');
    searchInputs.forEach(input => {
        input.addEventListener('input', function() {
            const searchTerm = this.value.toLowerCase();
            const table = this.closest('.card').querySelector('table tbody');
            if (table) {
                const rows = table.querySelectorAll('tr');
                rows.forEach(row => {
                    const text = row.textContent.toLowerCase();
                    row.style.display = text.includes(searchTerm) ? '' : 'none';
                });
            }
        });
    });
}

// Table filter functionality
function setupTableFilters() {
    const filterSelects = document.querySelectorAll('select[id*="Filter"]');
    filterSelects.forEach(select => {
        select.addEventListener('change', function() {
            applyFilters();
        });
    });
}

// Apply all active filters
function applyFilters() {
    const statusFilter = document.getElementById('statusFilter');
    const paymentFilter = document.getElementById('paymentFilter');
    const locationFilter = document.getElementById('locationFilter');
    
    if (!statusFilter) return;
    
    const table = document.querySelector('#bookingsTable tbody');
    if (table) {
        const rows = table.querySelectorAll('tr');
        rows.forEach(row => {
            let show = true;
            
            // Status filter
            if (statusFilter && statusFilter.value) {
                const statusBadge = row.querySelector('.badge');
                const status = statusBadge ? statusBadge.textContent.toLowerCase() : '';
                if (!status.includes(statusFilter.value.toLowerCase())) {
                    show = false;
                }
            }
            
            // Payment filter
            if (paymentFilter && paymentFilter.value && show) {
                const paymentBadges = row.querySelectorAll('.badge');
                const paymentBadge = paymentBadges[paymentBadges.length - 1]; // Last badge is payment status
                const payment = paymentBadge ? paymentBadge.textContent.toLowerCase() : '';
                if (!payment.includes(paymentFilter.value.toLowerCase())) {
                    show = false;
                }
            }
            
            // Location filter
            if (locationFilter && locationFilter.value && show) {
                const locationCell = row.cells[2]; // Location is 3rd column
                const location = locationCell ? locationCell.textContent.toLowerCase() : '';
                if (!location.includes(locationFilter.value.toLowerCase())) {
                    show = false;
                }
            }
            
            row.style.display = show ? '' : 'none';
        });
    }
}

// Setup refresh buttons
function setupRefreshButtons() {
    const refreshButtons = document.querySelectorAll('[data-action="refresh"]');
    refreshButtons.forEach(button => {
        button.addEventListener('click', function() {
            const icon = this.querySelector('i');
            if (icon) {
                icon.classList.add('fa-spin');
                setTimeout(() => {
                    icon.classList.remove('fa-spin');
                    showNotification('Data refreshed successfully', 'success');
                }, 1000);
            }
        });
    });
}

// Auto-refresh functionality
function setupAutoRefresh() {
    // Refresh data every 30 seconds
    setInterval(function() {
        // In a real application, this would make AJAX calls to update data
        updateRealTimeData();
    }, 30000);
}

// Update real-time data (mock function)
function updateRealTimeData() {
    // Update occupancy rates
    const progressBars = document.querySelectorAll('.progress-bar');
    progressBars.forEach(bar => {
        // Simulate small changes in occupancy
        const currentWidth = parseInt(bar.style.width);
        const change = Math.floor(Math.random() * 10) - 5; // -5 to +5 change
        const newWidth = Math.max(0, Math.min(100, currentWidth + change));
        bar.style.width = newWidth + '%';
        bar.setAttribute('aria-valuenow', newWidth);
    });
}

// Notification system
function showNotification(message, type = 'info') {
    const notification = document.createElement('div');
    notification.className = `alert alert-${type} alert-dismissible fade show position-fixed`;
    notification.style.top = '20px';
    notification.style.right = '20px';
    notification.style.zIndex = '9999';
    notification.style.minWidth = '300px';
    
    notification.innerHTML = `
        ${message}
        <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
    `;
    
    document.body.appendChild(notification);
    
    // Auto-remove after 5 seconds
    setTimeout(() => {
        if (notification.parentNode) {
            notification.remove();
        }
    }, 5000);
}

// Export functionality
function exportData(format, dataType) {
    showNotification(`Exporting ${dataType} as ${format.toUpperCase()}...`, 'info');
    
    // Mock export - in real app, this would generate and download file
    setTimeout(() => {
        showNotification(`${dataType} exported successfully!`, 'success');
    }, 2000);
}

// Bulk actions
function setupBulkActions() {
    const selectAllCheckbox = document.getElementById('selectAll');
    const rowCheckboxes = document.querySelectorAll('input[name="selectedRows[]"]');
    const bulkActionButtons = document.querySelectorAll('[data-bulk-action]');
    
    if (selectAllCheckbox) {
        selectAllCheckbox.addEventListener('change', function() {
            rowCheckboxes.forEach(checkbox => {
                checkbox.checked = this.checked;
            });
            updateBulkActionButtons();
        });
    }
    
    rowCheckboxes.forEach(checkbox => {
        checkbox.addEventListener('change', updateBulkActionButtons);
    });
    
    function updateBulkActionButtons() {
        const selectedCount = document.querySelectorAll('input[name="selectedRows[]"]:checked').length;
        bulkActionButtons.forEach(button => {
            button.disabled = selectedCount === 0;
            const countSpan = button.querySelector('.selected-count');
            if (countSpan) {
                countSpan.textContent = selectedCount;
            }
        });
    }
}

// Form validation
function setupFormValidation() {
    const forms = document.querySelectorAll('.needs-validation');
    forms.forEach(form => {
        form.addEventListener('submit', function(event) {
            if (!form.checkValidity()) {
                event.preventDefault();
                event.stopPropagation();
            }
            form.classList.add('was-validated');
        });
    });
}

// Date range picker integration
function initializeDatePickers() {
    const dateInputs = document.querySelectorAll('input[type="date"]');
    dateInputs.forEach(input => {
        // Set max date to today
        input.max = new Date().toISOString().split('T')[0];
    });
}

// Responsive table handling
function setupResponsiveTables() {
    const tables = document.querySelectorAll('.table-responsive');
    tables.forEach(table => {
        if (table.scrollWidth > table.clientWidth) {
            table.classList.add('table-scroll-indicator');
        }
    });
}

// Initialize all functionality
function initializeAdminDashboard() {
    setupBulkActions();
    setupFormValidation();
    initializeDatePickers();
    setupResponsiveTables();
}

// Call initialization when DOM is ready
if (document.readyState === 'loading') {
    document.addEventListener('DOMContentLoaded', initializeAdminDashboard);
} else {
    initializeAdminDashboard();
}