/**
 * Anmi Plugin Manager Admin JavaScript
 */

(function($) {
    'use strict';
    
    $(document).ready(function() {
        
        // Confirm before dangerous actions
        $('.button-link-delete').on('click', function(e) {
            if (!confirm('Are you sure you want to delete this plugin?')) {
                e.preventDefault();
                return false;
            }
        });
        
        // Auto-dismiss notices after 5 seconds
        setTimeout(function() {
            $('.notice.is-dismissible').fadeOut();
        }, 5000);
        
        // Future: AJAX functionality sẽ được thêm ở các batches sau
        
    });
    
})(jQuery);
