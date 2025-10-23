/**
 * Admin Page JavaScript
 * An Mi CSV Importer for WooCommerce
 */

(function($) {
    'use strict';
    
    const AnmiCsvImporter = {
        
        init: function() {
            this.bindEvents();
        },
        
        bindEvents: function() {
            $('#anmi-import-form').on('submit', this.handleImport.bind(this));
            $('#validate-btn').on('click', this.handleValidate.bind(this));
            $('#export-template-btn').on('click', this.handleExportTemplate.bind(this));
        },
        
        /**
         * Handle form submission for import
         */
        handleImport: function(e) {
            e.preventDefault();
            
            const fileInput = $('#csv_file')[0];
            
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Vui lòng chọn file CSV');
                return;
            }
            
            // Check file size
            const maxSize = anmiCsvImporter.max_file_size;
            if (fileInput.files[0].size > maxSize) {
                alert('File vượt quá kích thước cho phép: ' + this.formatBytes(maxSize));
                return;
            }
            
            // Show progress
            this.showProgress(anmiCsvImporter.i18n.processing);
            $('#import-results').hide();
            
            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'anmi_csv_import');
            formData.append('nonce', anmiCsvImporter.nonce);
            formData.append('csv_file', fileInput.files[0]);
            formData.append('update_existing', $('input[name="update_existing"]').is(':checked') ? 1 : 0);
            formData.append('create_categories', $('input[name="create_categories"]').is(':checked') ? 1 : 0);
            formData.append('update_price', $('input[name="update_price"]').is(':checked') ? 1 : 0);
            formData.append('update_stock', $('input[name="update_stock"]').is(':checked') ? 1 : 0);
            formData.append('delimiter', $('#delimiter').val());
            formData.append('encoding', $('#encoding').val());
            
            // Send AJAX request
            $.ajax({
                url: anmiCsvImporter.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: this.handleImportSuccess.bind(this),
                error: this.handleImportError.bind(this)
            });
        },
        
        /**
         * Handle successful import
         */
        handleImportSuccess: function(response) {
            this.hideProgress();
            
            if (response.success) {
                const data = response.data;
                this.showResults(true, data);
            } else {
                this.showResults(false, response.data);
            }
        },
        
        /**
         * Handle import error
         */
        handleImportError: function(xhr, status, error) {
            this.hideProgress();
            this.showResults(false, {
                message: 'Lỗi kết nối: ' + error
            });
        },
        
        /**
         * Handle file validation
         */
        handleValidate: function(e) {
            e.preventDefault();
            
            const fileInput = $('#csv_file')[0];
            
            if (!fileInput.files || !fileInput.files[0]) {
                alert('Vui lòng chọn file CSV');
                return;
            }
            
            // Show progress
            this.showProgress(anmiCsvImporter.i18n.validating);
            $('#import-results').hide();
            
            // Prepare form data
            const formData = new FormData();
            formData.append('action', 'anmi_csv_validate');
            formData.append('nonce', anmiCsvImporter.nonce);
            formData.append('csv_file', fileInput.files[0]);
            
            // Send AJAX request
            $.ajax({
                url: anmiCsvImporter.ajax_url,
                type: 'POST',
                data: formData,
                processData: false,
                contentType: false,
                success: this.handleValidateSuccess.bind(this),
                error: this.handleValidateError.bind(this)
            });
        },
        
        /**
         * Handle successful validation
         */
        handleValidateSuccess: function(response) {
            this.hideProgress();
            
            if (response.success) {
                const data = response.data;
                this.showValidationResults(data);
            } else {
                this.showResults(false, response.data);
            }
        },
        
        /**
         * Handle validation error
         */
        handleValidateError: function(xhr, status, error) {
            this.hideProgress();
            this.showResults(false, {
                message: 'Lỗi kết nối: ' + error
            });
        },
        
        /**
         * Handle template export
         */
        handleExportTemplate: function(e) {
            e.preventDefault();
            
            const url = anmiCsvImporter.ajax_url + 
                       '?action=anmi_csv_export_template' +
                       '&nonce=' + anmiCsvImporter.nonce;
            
            window.location.href = url;
        },
        
        /**
         * Show progress bar
         */
        showProgress: function(message) {
            $('#import-progress').show();
            $('.anmi-progress-text').text(message);
            $('.anmi-progress-fill').css('width', '50%');
            $('#import-btn').prop('disabled', true).html('<span class="anmi-spinner"></span> Đang xử lý...');
            $('#validate-btn').prop('disabled', true);
        },
        
        /**
         * Hide progress bar
         */
        hideProgress: function() {
            $('#import-progress').hide();
            $('.anmi-progress-fill').css('width', '0%');
            $('#import-btn').prop('disabled', false).html('🚀 Bắt Đầu Import');
            $('#validate-btn').prop('disabled', false);
        },
        
        /**
         * Show import results
         */
        showResults: function(success, data) {
            const $results = $('#import-results');
            $results.removeClass('success error').addClass(success ? 'success' : 'error');
            
            let html = '';
            
            if (success) {
                html += '<h3>✅ ' + data.message + '</h3>';
                
                if (data.stats) {
                    html += '<table class="anmi-results-table">';
                    html += '<tr><th>Tổng số dòng</th><td>' + data.stats.total + '</td></tr>';
                    html += '<tr><th>Nhập mới</th><td>' + data.stats.imported + '</td></tr>';
                    html += '<tr><th>Cập nhật</th><td>' + data.stats.updated + '</td></tr>';
                    html += '<tr><th>Bỏ qua</th><td>' + data.stats.skipped + '</td></tr>';
                    html += '<tr><th>Lỗi</th><td>' + data.stats.failed + '</td></tr>';
                    html += '</table>';
                }
                
                if (data.warnings && data.warnings.length > 0) {
                    html += '<h4>⚠️ Cảnh báo (' + data.warnings.length + '):</h4>';
                    html += '<ul class="anmi-results-list">';
                    data.warnings.slice(0, 10).forEach(function(warning) {
                        html += '<li>' + warning + '</li>';
                    });
                    if (data.warnings.length > 10) {
                        html += '<li><em>... và ' + (data.warnings.length - 10) + ' cảnh báo khác</em></li>';
                    }
                    html += '</ul>';
                }
                
                if (data.errors && data.errors.length > 0) {
                    html += '<h4>❌ Lỗi (' + data.errors.length + '):</h4>';
                    html += '<ul class="anmi-results-list">';
                    data.errors.slice(0, 10).forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                    if (data.errors.length > 10) {
                        html += '<li><em>... và ' + (data.errors.length - 10) + ' lỗi khác</em></li>';
                    }
                    html += '</ul>';
                }
            } else {
                html += '<h3>❌ Lỗi Import</h3>';
                html += '<p>' + (data.message || 'Có lỗi xảy ra trong quá trình import') + '</p>';
                
                if (data.errors && data.errors.length > 0) {
                    html += '<ul class="anmi-results-list">';
                    data.errors.forEach(function(error) {
                        html += '<li>' + error + '</li>';
                    });
                    html += '</ul>';
                }
            }
            
            $results.html(html).show();
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $results.offset().top - 100
            }, 500);
        },
        
        /**
         * Show validation results
         */
        showValidationResults: function(data) {
            const $results = $('#import-results');
            const isValid = data.valid;
            
            $results.removeClass('success error').addClass(isValid ? 'success' : 'error');
            
            let html = '';
            
            if (isValid) {
                html += '<h3>✅ File CSV hợp lệ!</h3>';
            } else {
                html += '<h3>⚠️ File CSV có vấn đề</h3>';
            }
            
            if (data.stats) {
                html += '<table class="anmi-results-table">';
                html += '<tr><th>Tổng số dòng</th><td>' + data.stats.total_rows + '</td></tr>';
                html += '<tr><th>Dòng hợp lệ</th><td>' + data.stats.valid_rows + '</td></tr>';
                html += '<tr><th>Dòng không hợp lệ</th><td>' + data.stats.invalid_rows + '</td></tr>';
                html += '</table>';
            }
            
            if (data.errors && data.errors.length > 0) {
                html += '<h4>❌ Lỗi (' + data.errors.length + '):</h4>';
                html += '<ul class="anmi-results-list">';
                data.errors.forEach(function(error) {
                    html += '<li>' + error + '</li>';
                });
                html += '</ul>';
            }
            
            if (data.warnings && data.warnings.length > 0) {
                html += '<h4>⚠️ Cảnh báo (' + data.warnings.length + '):</h4>';
                html += '<ul class="anmi-results-list">';
                data.warnings.slice(0, 15).forEach(function(warning) {
                    html += '<li>' + warning + '</li>';
                });
                if (data.warnings.length > 15) {
                    html += '<li><em>... và ' + (data.warnings.length - 15) + ' cảnh báo khác</em></li>';
                }
                html += '</ul>';
            }
            
            if (isValid) {
                html += '<p><strong>Bạn có thể tiến hành import file này.</strong></p>';
            } else {
                html += '<p><strong>Vui lòng sửa các lỗi trước khi import.</strong></p>';
            }
            
            $results.html(html).show();
            
            // Scroll to results
            $('html, body').animate({
                scrollTop: $results.offset().top - 100
            }, 500);
        },
        
        /**
         * Format bytes to human readable
         */
        formatBytes: function(bytes, decimals = 2) {
            if (bytes === 0) return '0 Bytes';
            
            const k = 1024;
            const dm = decimals < 0 ? 0 : decimals;
            const sizes = ['Bytes', 'KB', 'MB', 'GB', 'TB'];
            
            const i = Math.floor(Math.log(bytes) / Math.log(k));
            
            return parseFloat((bytes / Math.pow(k, i)).toFixed(dm)) + ' ' + sizes[i];
        }
    };
    
    // Initialize on document ready
    $(document).ready(function() {
        AnmiCsvImporter.init();
    });
    
})(jQuery);
