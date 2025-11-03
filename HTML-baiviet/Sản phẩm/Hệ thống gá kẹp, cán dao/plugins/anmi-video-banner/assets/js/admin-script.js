/**
 * AN MI VIDEO BANNER - ADMIN JAVASCRIPT
 * Version: 1.2.0
 */

(function($) {
    'use strict';
    
    // Admin functionality is mostly inline in the view files
    // This file is for global admin functions
    
    $(document).ready(function() {
        
        // Dismiss notices
        $(document).on('click', '.notice-dismiss', function() {
            $(this).closest('.notice').fadeOut();
        });
        
        // Confirm before leaving if form has changes
        var formChanged = false;
        
        $('#anmi-banner-form input, #anmi-banner-form select, #anmi-banner-form textarea').on('change', function() {
            formChanged = true;
        });
        
        $(window).on('beforeunload', function() {
            if (formChanged) {
                return 'You have unsaved changes. Are you sure you want to leave?';
            }
        });
        
        $('#anmi-banner-form').on('submit', function() {
            formChanged = false;
        });
        
        // Auto-dismiss success messages
        setTimeout(function() {
            $('.notice-success').fadeOut();
        }, 5000);
        
    });
    
})(jQuery);
