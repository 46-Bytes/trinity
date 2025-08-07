// Initialize Toastr globally
document.addEventListener("DOMContentLoaded", function () {
    // Check if toastr is available
    if (typeof toastr !== 'undefined') {
        // Configure toastr options
        toastr.options = {
            "closeButton": true,
            "debug": false,
            "newestOnTop": true,
            "progressBar": true,
            "positionClass": "toastr-top-right",
            "preventDuplicates": false,
            "onclick": null,
            "showDuration": "300",
            "hideDuration": "1000",
            "timeOut": "5000",
            "extendedTimeOut": "1000",
            "showEasing": "swing",
            "hideEasing": "linear",
            "showMethod": "fadeIn",
            "hideMethod": "fadeOut"
        };
        
        console.log('Toastr initialized successfully');
    } else {
        console.error('Toastr library not found');
    }
});

// Global function to show toastr messages
window.showToastr = function(type, message) {
    // Make sure toastr is available
    if (typeof toastr !== 'undefined') {
        switch(type) {
            case 'success':
                toastr.success(message);
                break;
            case 'error':
                toastr.error(message);
                break;
            case 'warning':
                toastr.warning(message);
                break;
            case 'info':
                toastr.info(message);
                break;
            default:
                console.log(message);
        }
    } else {
        console.error('Toastr is not available');
        alert(message);
    }
};
