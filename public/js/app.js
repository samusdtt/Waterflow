// Water Management SaaS - Custom JavaScript

$(document).ready(function() {
    console.log('Water Management SaaS - Application Loaded');
    
    // Initialize tooltips
    $('[data-bs-toggle="tooltip"]').tooltip();
    
    // Initialize popovers
    $('[data-bs-toggle="popover"]').popover();
    
    // Form validation
    $('form').on('submit', function(e) {
        const form = $(this);
        const submitBtn = form.find('button[type="submit"]');
        
        // Show loading state
        if (submitBtn.length) {
            const originalText = submitBtn.text();
            submitBtn.html('<span class="spinner"></span> Processing...');
            submitBtn.prop('disabled', true);
            
            // Re-enable after 3 seconds (fallback)
            setTimeout(function() {
                submitBtn.text(originalText);
                submitBtn.prop('disabled', false);
            }, 3000);
        }
    });
    
    // Auto-hide alerts after 5 seconds
    $('.alert').each(function() {
        const alert = $(this);
        setTimeout(function() {
            alert.fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    });
    
    // Confirm delete actions
    $('.btn-delete').on('click', function(e) {
        if (!confirm('Are you sure you want to delete this item? This action cannot be undone.')) {
            e.preventDefault();
        }
    });
    
    // Table row hover effects
    $('.table tbody tr').hover(
        function() {
            $(this).addClass('table-active');
        },
        function() {
            $(this).removeClass('table-active');
        }
    );
    
    // Search functionality
    $('#searchInput').on('keyup', function() {
        const value = $(this).val().toLowerCase();
        $('.table tbody tr').filter(function() {
            $(this).toggle($(this).text().toLowerCase().indexOf(value) > -1);
        });
    });
    
    // Add fade-in animation to cards
    $('.card').addClass('fade-in');
    
    // Initialize data tables if present
    if ($.fn.DataTable) {
        $('.data-table').DataTable({
            responsive: true,
            pageLength: 25,
            order: [[0, 'desc']],
            language: {
                search: "Search:",
                lengthMenu: "Show _MENU_ entries",
                info: "Showing _START_ to _END_ of _TOTAL_ entries",
                paginate: {
                    first: "First",
                    last: "Last",
                    next: "Next",
                    previous: "Previous"
                }
            }
        });
    }
});

// Global functions
window.WaterManagement = {
    // Show notification
    notify: function(message, type = 'info') {
        const alertClass = 'alert-' + type;
        const alertHtml = `
            <div class="alert ${alertClass} alert-dismissible fade show" role="alert">
                ${message}
                <button type="button" class="btn-close" data-bs-dismiss="alert"></button>
            </div>
        `;
        
        // Insert at top of main content
        $('main').prepend(alertHtml);
        
        // Auto-hide after 5 seconds
        setTimeout(function() {
            $('.alert').first().fadeOut('slow', function() {
                $(this).remove();
            });
        }, 5000);
    },
    
    // Confirm action
    confirm: function(message, callback) {
        if (confirm(message)) {
            callback();
        }
    },
    
    // Format currency
    formatCurrency: function(amount) {
        return new Intl.NumberFormat('en-US', {
            style: 'currency',
            currency: 'USD'
        }).format(amount);
    },
    
    // Format date
    formatDate: function(date) {
        return new Intl.DateTimeFormat('en-US', {
            year: 'numeric',
            month: 'short',
            day: 'numeric'
        }).format(new Date(date));
    }
};