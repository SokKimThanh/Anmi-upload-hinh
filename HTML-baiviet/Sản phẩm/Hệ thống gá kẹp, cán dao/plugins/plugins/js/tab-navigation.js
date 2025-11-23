/**
 * Tab Navigation Functionality
 * File: tab-navigation.js
 * Version: 1.3.0
 * Created: November 17, 2025
 * Updated: November 23, 2025 - Removed keyboard nav & ARIA
 * Author: An Mi Tools Technical Team
 * 
 * Purpose: Simple click-to-switch tabs
 * Used with: anmi-holder-products.css tab navigation styles
 * 
 * Features:
 * - Click-to-switch tabs with active state management
 * - Smooth fade-in animation via CSS
 * 
 * Dependencies: None (vanilla JavaScript)
 * Browser Support: IE11+, Modern browsers
 * 
 * CHANGELOG v1.3.0:
 * - REMOVED: All ARIA attributes management (aria-selected, aria-controls, aria-hidden, tabindex)
 * - REMOVED: All keyboard navigation (Arrow keys, Home, End, Enter, Space)
 * - SIMPLIFIED: Only handle click events and CSS class toggling
 * - KEPT: Basic show/hide functionality with .active class
 */

(function() {
    'use strict';

    // Wait for DOM to be fully loaded
    document.addEventListener('DOMContentLoaded', function() {
        initTabNavigation();
    });

    /**
     * Initialize tab navigation system
     */
    function initTabNavigation() {
        // Find all tab buttons
        const tabButtons = document.querySelectorAll('.tab-btn');
        const tabContents = document.querySelectorAll('.tab-content');

        if (tabButtons.length === 0 || tabContents.length === 0) {
            return;
        }

        // Attach click handlers
        tabButtons.forEach(function(button) {
            button.addEventListener('click', function() {
                handleTabClick(this, tabButtons, tabContents);
            });
        });

        // Ensure first tab is active if none are marked active
        ensureActiveTab(tabButtons, tabContents);
    }

    /**
     * Ensure at least one tab is active on page load
     */
    function ensureActiveTab(tabButtons, tabContents) {
        const hasActiveTab = Array.from(tabButtons).some(function(btn) {
            return btn.classList.contains('active');
        });

        if (!hasActiveTab && tabButtons.length > 0) {
            tabButtons[0].classList.add('active');
            const firstTabId = tabButtons[0].getAttribute('data-tab');
            const firstContent = document.getElementById(firstTabId);
            if (firstContent) {
                firstContent.classList.add('active');
            }
        }
    }

    /**
     * Handle tab button click
     * @param {HTMLElement} clickedButton - The clicked tab button
     * @param {NodeList} allButtons - All tab buttons
     * @param {NodeList} allContents - All tab contents
     */
    function handleTabClick(clickedButton, allButtons, allContents) {
        const targetTab = clickedButton.getAttribute('data-tab');

        if (!targetTab) {
            console.warn('Tab button missing data-tab attribute');
            return;
        }

        // Remove active class from all buttons and contents
        allButtons.forEach(function(btn) {
            btn.classList.remove('active');
        });

        allContents.forEach(function(content) {
            content.classList.remove('active');
        });

        // Add active class to clicked button and corresponding content
        clickedButton.classList.add('active');

        const targetContent = document.getElementById(targetTab);
        if (targetContent) {
            targetContent.classList.add('active');
        } else {
            console.warn('Tab content not found for:', targetTab);
        }
    }

    /**
     * Public API (if needed for external access)
     */
    window.AnmiTabNavigation = {
        version: '1.3.0',
        init: initTabNavigation
    };

})();