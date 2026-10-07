/**
 * Admin Panel JavaScript
 */
document.addEventListener('DOMContentLoaded', function() {
    var bootstrapApi = window.bootstrap;

    // Initialize tooltips
    var tooltipTriggerList = [].slice.call(document.querySelectorAll('[data-bs-toggle="tooltip"]'));
    if (bootstrapApi && bootstrapApi.Tooltip) {
        tooltipTriggerList.map(function (tooltipTriggerEl) {
            return new bootstrapApi.Tooltip(tooltipTriggerEl);
        });
    }
    
    // Auto-hide alerts
    var alerts = document.querySelectorAll('.alert');
    alerts.forEach(function(alert) {
        setTimeout(function() {
            if (bootstrapApi && bootstrapApi.Alert) {
                var bsAlert = bootstrapApi.Alert.getOrCreateInstance(alert);
                if (bsAlert) bsAlert.close();
            } else {
                alert.remove();
            }
        }, 5000);
    });
    
    // Confirm delete actions
    var deleteLinks = document.querySelectorAll('a[href*="delete"]');
    deleteLinks.forEach(function(link) {
        link.addEventListener('click', function(e) {
            if (!confirm('Are you sure you want to delete this item?')) {
                e.preventDefault();
            }
        });
    });
    
    // Image preview for file inputs
    var imageInputs = document.querySelectorAll('input[type="file"][accept="image/*"]');
    imageInputs.forEach(function(input) {
        input.addEventListener('change', function(e) {
            var file = e.target.files[0];
            if (file) {
                var reader = new FileReader();
                reader.onload = function(e) {
                    var preview = input.closest('.mb-4').querySelector('.img-thumbnail, #imagePreview');
                    if (preview) {
                        if (preview.id === 'imagePreview') {
                            preview.innerHTML = '<img src="' + e.target.result + '" class="img-thumbnail" style="max-height: 100px;">';
                        } else {
                            preview.src = e.target.result;
                        }
                    }
                };
                reader.readAsDataURL(file);
            }
        });
    });
});
