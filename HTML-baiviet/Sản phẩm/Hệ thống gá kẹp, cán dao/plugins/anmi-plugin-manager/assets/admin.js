/**
 * Anmi Plugin Manager Admin JavaScript
 */

(function($) {
    'use strict';

    $(document).ready(function() {
    const settings = window.anmiPM || {};

    const confirmModal  = $('#anmi-confirm-modal');
        const confirmInput  = $('#anmi-confirm-input');
        const confirmError  = $('#anmi-confirm-error');
        const confirmPlugin = $('#anmi-confirm-plugin');
        const confirmButton = $('#anmi-confirm-submit');
        const confirmCancel = $('#anmi-confirm-cancel');
        const confirmBackdrop = $('#anmi-confirm-backdrop');

        const jsonModal    = $('#anmi-json-modal');
        const jsonContent  = $('#anmi-json-content');
        const jsonCloseBtn = $('#anmi-json-close');
        const jsonBackdrop = $('#anmi-json-backdrop');

    const confirmPhrase = settings.confirmPhrase || 'DELETE';
        let confirmUrl = '';

    const killSwitchActive = !!settings.killSwitchActive;
    const killSwitchNotice = settings.killSwitchNotice || 'Actions are disabled while kill-switch is active.';

        // Helpers
        function openConfirmModal(pluginSlug, actionUrl) {
            confirmUrl = actionUrl;
            confirmInput.val('');
            confirmError.text('');
            confirmPlugin.text(pluginSlug);
            confirmModal.addClass('is-open');
            confirmInput.trigger('focus');
        }

        function closeConfirmModal() {
            confirmUrl = '';
            confirmModal.removeClass('is-open');
        }

        function openJsonModal(json) {
            jsonContent.text(json);
            jsonModal.addClass('is-open');
        }

        function closeJsonModal() {
            jsonModal.removeClass('is-open');
            jsonContent.text('');
        }

        // Action buttons (delete etc.)
        $('.anmi-trigger-confirm').on('click', function(e) {
            e.preventDefault();

            if (killSwitchActive) {
                window.alert(killSwitchNotice);
                return;
            }

            const $btn = $(this);
            const pluginSlug = $btn.data('pluginSlug') || '';
            const url = $btn.attr('href');

            openConfirmModal(pluginSlug, url);
        });

        confirmButton.on('click', function(e) {
            e.preventDefault();
            const value = confirmInput.val().trim();

            if (value !== confirmPhrase) {
                confirmError.text(settings.confirmPrompt || 'Type DELETE to proceed.');
                confirmInput.trigger('focus');
                return;
            }

            if (confirmUrl) {
                window.location.href = confirmUrl;
            }
        });

        confirmCancel.on('click', function(e) {
            e.preventDefault();
            closeConfirmModal();
        });

        confirmBackdrop.on('click', closeConfirmModal);

        $(document).on('keyup', function(e) {
            if (e.key === 'Escape') {
                closeConfirmModal();
                closeJsonModal();
            }
        });

        // Kill switch disabled buttons
        $('.anmi-action-disabled').on('click', function(e) {
            if (!killSwitchActive) {
                return;
            }
            e.preventDefault();
            window.alert(killSwitchNotice);
        });

        // JSON view modal
        $('.anmi-view-json').on('click', function(e) {
            e.preventDefault();
            const json = $(this).data('json') || '{}';
            openJsonModal(json);
        });

        $('#anmi-json-close').on('click', function(e) {
            e.preventDefault();
            closeJsonModal();
        });

        jsonBackdrop.on('click', closeJsonModal);

        // Auto-dismiss notices after 5 seconds
        setTimeout(function() {
            $('.notice.is-dismissible').fadeOut();
        }, 5000);
    });

})(jQuery);
