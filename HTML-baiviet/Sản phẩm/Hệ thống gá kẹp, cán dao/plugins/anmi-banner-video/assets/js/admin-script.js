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

        var columnToggleInputs = $('.anmi-column-toggle-list input[type="checkbox"]');
        if (columnToggleInputs.length) {
            var columnResetButton = $('.anmi-column-settings__reset');
            var storageKey = 'anmiBannerColumnVisibility';
            var statusMessage = $('.anmi-column-settings__status');

            var updateStatus = function(message) {
                if (statusMessage.length) {
                    statusMessage.text(message);
                }
            };

            var readSettings = function() {
                try {
                    var saved = window.localStorage.getItem(storageKey);
                    return saved ? JSON.parse(saved) : {};
                } catch (error) {
                    updateStatus('Trình duyệt đang chặn bộ nhớ cục bộ, cài đặt sẽ không được lưu sau khi tải lại.');
                    return {};
                }
            };

            var saveSettings = function(settings) {
                try {
                    if (Object.keys(settings).length === 0) {
                        window.localStorage.removeItem(storageKey);
                    } else {
                        window.localStorage.setItem(storageKey, JSON.stringify(settings));
                    }
                } catch (error) {
                    updateStatus('Không thể lưu cài đặt hiển thị cột. Vui lòng kiểm tra quyền trình duyệt.');
                    /* noop */
                }
            };

            var toggleColumn = function(columnClass, isVisible) {
                var selector = '.anmi-banner-table .' + columnClass;
                if (isVisible) {
                    $(selector).show();
                } else {
                    $(selector).hide();
                }
            };

            var applySettings = function(settings) {
                columnToggleInputs.each(function() {
                    var $input = $(this);
                    var columnClass = $input.data('column');
                    var isVisible = Object.prototype.hasOwnProperty.call(settings, columnClass) ? settings[columnClass] : true;
                    $input.prop('checked', isVisible);
                    toggleColumn(columnClass, isVisible);
                });
            };

            var storedSettings = readSettings();
            applySettings(storedSettings);
            updateStatus('Cài đặt hiển thị cột đã được áp dụng.');

            columnToggleInputs.on('change', function() {
                var $input = $(this);
                var columnClass = $input.data('column');
                var isVisible = $input.is(':checked');

                if (!isVisible && columnToggleInputs.filter(':checked').length === 0) {
                    $input.prop('checked', true);
                    toggleColumn(columnClass, true);
                    return;
                }

                toggleColumn(columnClass, isVisible);

                if (isVisible) {
                    delete storedSettings[columnClass];
                } else {
                    storedSettings[columnClass] = false;
                }

                saveSettings(storedSettings);
                updateStatus('Đã lưu cài đặt hiển thị cột.');
            });

            columnResetButton.on('click', function() {
                storedSettings = {};
                saveSettings(storedSettings);
                applySettings(storedSettings);
                updateStatus('Đã khôi phục hiển thị cột mặc định.');
            });
        }
        
    });
    
})(jQuery);
